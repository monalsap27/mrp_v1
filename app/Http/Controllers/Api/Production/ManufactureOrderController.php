<?php

namespace App\Http\Controllers\Api\Production;

use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\{Carbon, CarbonInterval};
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Production\{LogProduction, ManufactureMaterial, ManufactureOrder, ManufactureOrderDetail, ProductDetail, StockIn, StockOut};

class ManufactureOrderController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function store(Request $request)
    {
        $params     = $request->all();
        DB::beginTransaction();
        try {
            $manufacturereProduct = new ManufactureOrder();
            $manufacturereProduct->product_id = $params["product_id"];
            $manufacturereProduct->qty = $params["qty"];
            $manufacturereProduct->date = date_format(date_create($params["date"]), "Y-m-d H:i:s");
            $manufacturereProduct->status = '1';
            $manufacturereProduct->total_timing = $params["total_timing"];
            $manufacturereProduct->save();
            DB::commit();
            return response()->json(['message' => 'has been created successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function dataApproval(Request $request)
    {
        $searchParams = $request->all();
        $ManufactureOrderQuery = ManufactureOrder::query()
            ->select("manufacture_order.id", "manufacture_order.qty", "manufacture_order.created_at", "product.name", "manufacture_order.date as production_date", "manufacture_order.status", "manufacture_order.total_timing", "order.nomor as nomor_so", DB::RAW("FLOOR(avg(im.is_available)) as is_available"))
            ->leftJoin("product.product", "manufacture_order.product_id", "product.id")
            ->leftJoin("product.product_detail", "product.id", "product_detail.product_id")
            ->leftJoin("sales.order_detail", "order_detail.id", "manufacture_order.sales_order_detail_id")
            ->leftJoin("sales.order", "order.id", "order_detail.order_id")
            ->leftJoin(
                DB::RAW("(SELECT material_id, sum(qty_produksi) qty_produksi, qty_stock, CASE WHEN ( qty_stock - sum(qty_produksi) ) >= 0 THEN '1' ELSE 0 END is_available FROM product.ingredients_material group by material_id, qty_stock) as im"),
                "im.material_id",
                "product_detail.material_id"
            )->orderBy('manufacture_order.status', 'asc')
            ->groupBy("manufacture_order.id", "manufacture_order.qty", "manufacture_order.created_at", "product.name", "manufacture_order.date", "manufacture_order.status", "manufacture_order.total_timing", "order.nomor");
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $ManufactureOrderQuery->where('product.name', 'LIKE', '%' . $keyword . '%');
        }
        return new GeneralCollection($ManufactureOrderQuery->paginate($limit));
    }
    public function detailListAvailable(Request $request)
    {
        $searchParams = $request->all();
        $sub          = DB::table('product.ingredients_material')
            ->selectRaw("name_material, material_id,case when id = " . $searchParams['id'] . " then sum(qty_produksi) else '0' end qty_produksi, case when id != " . $searchParams['id'] . " then sum(qty_produksi) else '0' end claim_stock")
            ->groupBy("name_material", "qty_produksi", "id", "material_id");
        $query = DB::query()->from(DB::raw("(" . $sub->toSql() . ") as sub"))
            ->mergeBindings($sub)
            ->selectRaw("name_material, material_id,sum(qty_produksi) qty_produksi, sum(claim_stock)claim_stock, qty_stock")
            ->leftJoin("product.data_stock_product", "sub.material_id", "data_stock_product.id")
            ->groupBy("name_material", "material_id", "data_stock_product.qty_stock");

        return new GeneralCollection($query->get());
    }
    public function storeApproval(Request $request)
    {
        $params     = $request->all();
        DB::beginTransaction();
        try {
            $manufacturereProduct = ManufactureOrder::find($params['id']);
            $manufacturereProduct->qty = $params["qty"];
            $manufacturereProduct->date = date_format(date_create($params["date"]), "Y-m-d H:i:s");
            $manufacturereProduct->status =  $params["status"];
            $manufacturereProduct->note =  empty($params["note"]) ? null : $params["note"];
            $manufacturereProduct->approved_by = auth()->user()->id;
            $manufacturereProduct->approved_at = new DateTime();
            $manufacturereProduct->save();
            DB::commit();
            return response()->json(['message' => 'has been created successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function dataManufactureOrder(Request $request)
    {
        $searchParams = $request->all();
        $ManufactureOrderQuery = ManufactureOrder::query()
            ->select("manufacture_order.date", "manufacture_order.product_id", "manufacture_order.id", "manufacture_order.qty", "manufacture_order.created_at", "product.name", "manufacture_order.date as production_date", "manufacture_order.status", "manufacture_order.total_timing", DB::RAW("FLOOR(avg(im.is_available)) as is_available"))
            ->leftJoin("product.product", "manufacture_order.product_id", "product.id")
            ->leftJoin("product.product_detail", "product.id", "product_detail.product_id")
            ->leftJoin(
                DB::RAW("(SELECT material_id, sum(qty_produksi) qty_produksi, qty_stock, CASE WHEN ( qty_stock - sum(qty_produksi) ) >= 0 THEN '1' ELSE 0 END is_available FROM product.ingredients_material group by material_id, qty_stock) as im"),
                "im.material_id",
                "product_detail.material_id"
            )->orderBy('manufacture_order.status', 'asc')
            ->whereIn("manufacture_order.status", array(2, 3))
            ->groupBy("manufacture_order.product_id", "manufacture_order.id", "manufacture_order.qty", "manufacture_order.created_at", "product.name", "manufacture_order.date", "manufacture_order.status", "manufacture_order.total_timing");
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $ManufactureOrderQuery->where('product.name', 'LIKE', '%' . $keyword . '%');
        }
        return new GeneralCollection($ManufactureOrderQuery->paginate($limit));
    }
    public function startProduction(Request $request)
    {
        $searchParams = $request->all();
        DB::beginTransaction();
        try {
            $data_product_detail = ProductDetail::where('product_id', $searchParams['product_id'])->orderBy('index', 'asc')->get();
            foreach ($data_product_detail as $records) {
                $manufacture_detail = new ManufactureOrderDetail;
                $manufacture_detail->manufacture_order_id = $records->id;
                $manufacture_detail->workstation_id = $records->workstation_id;
                $manufacture_detail->timming = $records->timming;
                $manufacture_detail->manufacture_order_id = $searchParams['id'];
                $manufacture_detail->start_at = $searchParams['date'];
                $manufacture_detail->save();

                $total_qty  = (int)$records->qty + (int)$searchParams['qty'];
                $data_stock = DB::table('product.data_stock_by_control_id')->limit($total_qty)->where('product_id', $records->material_id)->orderBy('created_at', 'asc')->get();
                foreach ($data_stock as $record_ds) {
                    $stock_out = new StockOut;
                    $stock_out->control_id = $record_ds->control_id;
                    $stock_out->harga_beli = $record_ds->harga_beli;
                    $stock_out->harga_jual = $record_ds->harga_jual;
                    $stock_out->product_id = $records->material_id;
                    $stock_out->description = 'Produksi ' . $searchParams['name'] . ' Tanggal ' . date("d M Y");
                    $stock_out->save();

                    $manufacture_material  = new ManufactureMaterial;
                    $manufacture_material->data_stock_out()->associate($stock_out);
                    $manufacture_material->manufacture_order_id = $searchParams['id'];
                    $manufacture_material->control_id = $record_ds->control_id;
                    $manufacture_material->product_id = $records->material_id;
                    $manufacture_material->save();
                }
            }
            ManufactureOrder::where('id', $searchParams['id'])->update(['status' => '3', 'start_at' => new DateTime()]);

            LogProduction::create([
                'manufacture_order_id' => $searchParams['id'],
                'description' => 'Starting production',
            ]);
            DB::commit();
            return response()->json(['message' => 'has been created successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function timelineProgress(Request $request)
    {
        $searchParams = $request->all();
        $records = ManufactureOrderDetail::selectRaw('manufacture_order_detail.id, workstation.name, workstation.code, manufacture_order_detail.timming, manufacture_order_detail.id, manufacture_order_detail.actual_timing, start_at, pause_at, continue_at,finish_at, manufacture_order_detail.created_at, is_start, change_material,continue_at')
            ->where('manufacture_order_id', $searchParams['id'])
            ->leftJoin('product.workstation', 'manufacture_order_detail.workstation_id', 'workstation.id')
            ->orderBy("manufacture_order_detail.id", "asc")
            ->get();
        $date_timeline = 0;
        $button  = 'info';
        foreach ($records as $i => $records) {
            if ($i == 0) {
                $date = date_format(date_create(empty($records->start_at) ? $records->created_at : $records->start_at), "d-m-Y H:i:s");
            } else {
                $created_at =  date('d-m-Y H:i:s', strtotime("+{$date_timeline} minutes", strtotime($records->created_at)));
                $date  = empty($records->start_at) ? $created_at : date_format(date_create($records->start_at), "d-m-Y H:i:s");
            }
            $date_timeline = $date_timeline + $records->timming;
            $count_down =  date('d-m-Y H:i:s', strtotime("+{$date_timeline} minutes", strtotime($records->start_at)));
            if (empty($records->finish_at) && empty($records->is_start)) {
                $button = 'danger';
                $icon = 'el-icon-close';
            } elseif (empty($records->finish_at) && !empty($records->start_at) && !empty($records->is_start)) {
                $button = 'warning';
                $icon = 'el-icon-minus';
            } elseif (!empty($records->finish_at)) {
                $button = 'success';
                $icon = 'el-icon-check';
            }
            $arr_detail[] = array(
                'index' => $i,
                'disable_start' => (empty($records->finish_at)) ? false : true,
                'id' => $records->id,
                'name' => $records->name,
                'code' => $records->code,
                'timing' => WorkstationController::hour($records->timming),
                'actual_timing' => empty($records->actual_timing) ? '00:00' : CarbonInterval::minutes($records->actual_timing)->cascade()->forHumans(),
                'date' => $date,
                'type' => $button,
                'icon'  => empty($icon) ? '' : $icon,
                'date_timeline' => $date_timeline,
                'finish_at' => $records->finish_at,
                'start_at' => $records->start_at,
                'is_start' => $records->is_start,
                'continue_at' => $records->continue_at,
                'change_material' => $records->change_material == true ? false : true,
                'count_down'  => date_format(date_create($count_down), "Y-m-d H:i:s"),
                'actual_date' => date_format(date_create(empty($records->start_at) ? $records->created_at : $records->start_at), "d-m-Y H:i:s"),
            );
        }
        return new GeneralCollection($arr_detail);
    }
    public function showDetailManufaturingOrder(Request $request)
    {
        $searchParams = $request->all();
        $query        = ManufactureOrder::selectRaw("product.name, manufacture_order.qty,	manufacture_order.date, manufacture_order.status, manufacture_order.created_at,	manufacture_order.DATE,	manufacture_order.start_at,	manufacture_order.total_timing,	manufacture_order.actual_timing, (round(avg(case when manufacture_order_detail.finish_at is not null then 1 else 0 end),2)*100 )persen")
            ->leftJoin("product.product", "product.id", "manufacture_order.product_id")
            ->leftJoin("product.manufacture_order_detail", "manufacture_order.id", "manufacture_order_detail.manufacture_order_id")
            ->where('manufacture_order.id', $searchParams['id'])
            ->groupBy("product.name", "manufacture_order.qty", "manufacture_order.date", "manufacture_order.status", "manufacture_order.created_at", "manufacture_order.date", "manufacture_order.start_at", "manufacture_order.total_timing", "manufacture_order.actual_timing")
            ->first();
        $count_down =  date('d-m-Y H:i:s', strtotime("+{$query->total_timing} minutes", strtotime($query->date)));
        $arr_data  =  [
            'name' => $query->name,
            'qty' => $query->qty,
            'date' => $query->date,
            'status' => $query->status,
            'created_at' => $query->created_at,
            'start_at' => $query->start_at,
            'persen' => $query->persen,
            'count_down' => date_format(date_create($count_down), "Y-m-d H:i:s"),
            'total_timing' => WorkstationController::hour($query->total_timing),
            'actual_timing' => WorkstationController::hour($query->actual_timing),
        ];
        return new GeneralCollection($arr_data);
    }
    public function showDetailMaterial(Request $request)
    {
        $searchParams = $request->all();
        $query        = ManufactureMaterial::selectRaw("DISTINCT product.name, product.code, count(product_id) as qty, manufacture_order_id, product_id")
            ->leftJoin("product.product", "manufacture_material.product_id", "product.id")
            ->where("manufacture_order_id", $searchParams["id"])
            ->groupBy("product.name", "product.code", "manufacture_order_id", "product_id")
            ->orderBy("product.name", "asc")
            ->get();
        return new GeneralCollection($query);
    }
    public function startWorkstation(Request $request)
    {
        try {
            $detail = ManufactureOrderDetail::find($request->id);
            if (empty($detail->is_start)) {
                $detail->is_start = '1';
                $detail->start_at = new DateTime();
            } else {
                $detail->is_start = '1';
                $detail->continue_at = new DateTime();
            }
            $detail->save();
            DB::commit();
            return response()->json(['message' => 'updated data successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function pauseORFinish(Request $request)
    {
        $searchParams = $request->all();
        $startTime = Carbon::parse(!empty($searchParams['continue_at']) ? $searchParams['continue_at'] : ($searchParams['start_at']));
        $endTime = Carbon::now();
        $totalDuration = $endTime->diffInMinutes($startTime);
        try {
            $detail = ManufactureOrderDetail::find($request->id);
            if ($searchParams['type'] == 1) {
                $detail->is_start = '2';
                $detail->actual_timing = (int)$detail->actual_timing + (int)$totalDuration;
                $detail->save();
            } else if ($searchParams['type'] == 2) {
                $detail->is_start = '3';
                $detail->actual_timing = (int)$detail->actual_timing + (int)$totalDuration;
                $detail->finish_at = new DateTime();
                $detail->save();
                $get_finish_at = ManufactureOrderDetail::where('manufacture_order_id', $detail->manufacture_order_id)
                    ->selectRaw("(count(id) - count(finish_at)) as total_finish_at ")
                    ->first();
                if ($get_finish_at->total_finish_at == '0') {
                    $manufacture_order = ManufactureOrder::where('id', $detail->manufacture_order_id)
                        ->first();
                    $manufacture_order->status = '4';
                    $manufacture_order->finish_at = new DateTime();
                    $startTimeOrder = Carbon::parse($manufacture_order->start_at);
                    $totalDuration = $endTime->diffInMinutes($startTimeOrder);
                    $manufacture_order->actual_timing = $totalDuration;
                    $manufacture_order->code = 'MO' . date("ymd") . $detail->manufacture_order_id;
                    $manufacture_order->save();

                    for ($x = 0; $x < $manufacture_order->qty; $x++) {
                        $get_id_max = StockIn::where("product_id", $manufacture_order->product)->max("id");
                        $stock_in = new StockIn();
                        $stock_in->control_id = $manufacture_order->product_id . date("ymd") . (empty($get_id_max) ? '1' : ($get_id_max + 1));
                        $stock_in->product_id = $manufacture_order->product_id;
                        $stock_in->description = ' Hasil Produksi ' . $manufacture_order->code;
                        $stock_in->save();
                    }
                }
            }
            DB::commit();
            return response()->json(['message' => 'updated data successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function storeChangeControlID(Request $request)
    {
        $searchParams = $request->all();
        DB::beginTransaction();
        try {
            $manufacture_material = ManufactureMaterial::where('id', $searchParams['id'])->first();
            $manufacture_material->control_id = $request->control_id;
            if ($manufacture_material->save()) {
                $stock_out = StockOut::where('id', $manufacture_material->stock_out_id)->first();
                $stock_out->control_id = $request->control_id;
                if ($stock_out->save()) {
                    DB::commit();
                    return response()->json(['message' => 'has been created successfully !'], 200);
                }
            } else {
                DB::rollback();
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function getMaterialUsed(Request $request)
    {
        $searchParams = $request->all();
        $query = ManufactureMaterial::selectRaw('manufacture_material.id, manufacture_material.manufacture_order_id, manufacture_material.product_id, product.code, product.name, manufacture_material.control_id')
            ->where('product_id', $searchParams['product_id'])
            ->where('manufacture_order_id', $searchParams['manufacture_order_id'])
            ->leftJoin('product.product', 'product.id', 'manufacture_material.product_id');

        return new GeneralCollection($query->get());
    }

    public function dataManufactureOrderCompleted(Request $request)
    {
        $searchParams = $request->all();
        $ManufactureOrderQuery = ManufactureOrder::query()
            ->select("manufacture_order.date", "manufacture_order.product_id", "manufacture_order.id", "manufacture_order.qty", "manufacture_order.created_at", "product.name", "manufacture_order.date as production_date", "manufacture_order.status", "manufacture_order.total_timing", "finish_at", "manufacture_order.code", "manufacture_order.total_timing", "manufacture_order.actual_timing")
            ->leftJoin("product.product", "manufacture_order.product_id", "product.id")
            ->orderBy('manufacture_order.status', 'asc')
            ->where("manufacture_order.status", 4);
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $ManufactureOrderQuery->where('product.name', 'LIKE', '%' . $keyword . '%');
        }
        return new GeneralCollection($ManufactureOrderQuery->paginate($limit));
    }
    public function dataListThisMonth()
    {
        $query = ManufactureOrder::selectRaw('manufacture_order.id, product.name, product.code, manufacture_order.status, m_category.name as category_name, manufacture_order.qty')
            ->leftJoin('product.product', 'manufacture_order.product_id', 'product.id')
            ->leftJoin('product.m_category', 'product.m_category_id', 'm_category.id')
            ->whereRaw("EXTRACT(MONTH from date) <= EXTRACT(MONTH from CURRENT_DATE)");

        return new GeneralCollection($query->get());
    }
    public function dataManufactureOrderSchedule()
    {
        $ManufactureOrderQuery = ManufactureOrder::query()
            ->select("manufacture_order.date", "manufacture_order.product_id", "manufacture_order.id", "manufacture_order.qty", "manufacture_order.created_at", "product.name", "manufacture_order.date as production_date", "manufacture_order.status", "manufacture_order.total_timing", DB::raw("coalesce(finish_at, current_timestamp) as finish_at"), "manufacture_order.total_timing", "manufacture_order.actual_timing")
            ->leftJoin("product.product", "manufacture_order.product_id", "product.id")
            ->orderBy('manufacture_order.status', 'asc')
            ->get();
        return new GeneralCollection($ManufactureOrderQuery);
    }
}
