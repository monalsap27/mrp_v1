<?php

namespace App\Http\Controllers\Api\Production;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Http\Resources\Production\StockResource;
use App\Laravue\Models\Production\{StockIn, StockOut};
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $productQuery = DB::table('product.data_stock_product')
            ->select('id', 'name', 'total_timing', 'code', 'type', 'qty_stock as qty', 'harga_beli', 'harga_jual', 'safety_stock', 'created_at', 'm_supplier_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $productQuery->where('type', $searchParams['type']);
        if (!empty($keyword)) {
            $productQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $productQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($searchParams['type'] == 1) {
            $productQuery->where('status', '2');
        }
        if ($sort == '-id') {
            $productQuery->orderBy('id', 'desc');
        } else {
            $productQuery->orderBy('id', 'asc');
        }
        $records = $productQuery->paginate($limit);
        return response()->json($records, 200);
    }
    public function dataIN(Request $request)
    {
        $searchParams = $request->all();
        $productQuery = StockIn::query()
            ->select('stock_in.created_at', 'stock_in.control_id', 'stock_in.harga_beli', 'stock_in.harga_jual', 'stock_in.product_id')
            ->WHERE("stock_in.product_id", $searchParams['product_id'])
            ->whereNull("stock_in.is_out")
            ->orderBy('stock_in.created_at', 'desc');

        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        return new GeneralCollection($productQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $params     = $request->all();
            for ($x = 0; $x < $request->qty; $x++) {
                $get_id_max = StockIn::where("product_id", $params['product'])->max("id");
                $stock_in = new StockIn();
                $stock_in->control_id = $params['product'] . date("ymd") . (empty($get_id_max) ? '1' : ($get_id_max + 1));
                $stock_in->product_id = $params['product'];
                $stock_in->harga_beli = $params['harga_beli'];
                $stock_in->harga_jual = empty($params['harga_jual']) ? '0' : $params['harga_jual'];
                $stock_in->description = $params['description'];
                $stock_in->save();
            }
            DB::commit();
            return response()->json(['message' => 'has been created successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function storeOut(Request $request)
    {
        DB::beginTransaction();
        try {
            $params     = $request->all();
            $jml        = count($request->items);
            $data       = array();
            $arr_control_id = array();
            for ($x = 0; $x < $jml; $x++) {
                $data[] = [
                    'control_id' => $request->items[$x]['control_id'],
                    'product_id' => $request->items[$x]['product_id'],
                    'harga_beli' => $request->items[$x]['harga_beli'],
                    'harga_jual' => $params['harga_jual'],
                    'description' => $params['description'],
                ];
                $arr_control_id[] = $request->items[$x]['control_id'];
            }
            $insert_stock_out = StockOut::insert($data);
            $updateStatus = StockIn::whereIn('control_id', $arr_control_id)->update(['is_out' => '1']);
            DB::commit();
            return response()->json(['message' => 'has been created successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function showMutasi(Request $request)
    {
        $searchParams     = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if ($searchParams['type'] == 1) {
            $query = StockIn::query();
        } else if ($searchParams["type"] == 2) {
            $query = StockOut::query();
        }
        $records = $query->where('product_id', $searchParams['product_id'])
            ->select("control_id", "harga_beli", "harga_jual", "created_at", "description")
            ->orderBy("created_at", "desc");
        return StockResource::collection($records->paginate($limit));
    }
    public function showControlID(Request $request)
    {
        $searchParams = $request->all();
        $query = StockIn::selectRaw('id, control_id')
            ->where('product_id', $searchParams['product_id'])
            ->whereNotIn('control_id', function ($query) {
                $query->select('control_id')->from('product.stock_out');
            });
        return new GeneralCollection($query->get());
    }
    public function showByProduct(Request $request)
    {
        $searchParams = $request->all();
        $productQuery = DB::table('product.data_stock_product')
            ->select('id', 'qty_stock')
            ->where('data_stock_product.id', $searchParams['id'])
            ->first();
        $arr_price  =  [
            'id' => empty($productQuery->id) ? '0' : $productQuery->id,
            'stock' => empty($productQuery->qty_stock) ? '0' : $productQuery->qty_stock,
        ];
        return new GeneralCollection($arr_price);
    }
}
