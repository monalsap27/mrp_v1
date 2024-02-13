<?php

namespace App\Http\Controllers\Api\Sales;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Production\ManufactureOrder;
use App\Laravue\Models\Production\StockIn;
use App\Laravue\Models\Production\StockOut;
use App\Laravue\Models\Sales\{Order, OrderDetail};

class OrderController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function fetchNoSO()
    {
        $get_id_so = Order::selectRaw("COALESCE(max(id),01) as id")->first();
        $arr_data  =  [
            'no_so' => 'SO' . date("dmY") . $get_id_so->id,
            'date' => date("d-m-Y h:i:s"),
        ];
        return new GeneralCollection($arr_data);
    }
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $orderQuery = Order::query()->select("order.id", "order.created_at", "customer.name as customer_name", "customer.npwp", "order.nomor", "order.total_price", "order.status", "users.name as created_name", "customer_id", DB::raw("coalesce(total_payment,0) total_payment"))
            ->leftJoin('customer.customer', 'order.customer_id', 'customer.id')
            ->leftJoin('public.users', 'order.created_by', 'users.id');
        // ->groupBy("order.id", "order.created_at", "customer.name", "order.nomor", "order.total_amount", "order.status", "users.name");
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $orderQuery->where('customer.name', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $orderQuery->orderBy('order.id', 'desc');
        } else {
            $orderQuery->orderBy('order.id', 'asc');
        }
        return new GeneralCollection($orderQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $params     = $request->all();
            if (empty($params['id'])) {
                $order = new Order();
                $order->nomor = $params['nomor'];
                $order->customer_id = $params['customer'];
                $order->category = $params['category'];
                $order->total_price = $params['total_amount'];
                $order->tenor = $params['tenor'];
                $order->status = '1';
                if ($order->save()) {
                    $jml_detail = count($request->items);
                    for ($x = 0; $x < $jml_detail; $x++) {
                        $order_detail = new OrderDetail();
                        $order_detail->data_order()->associate($order);
                        $order_detail->product_id = $request->items[$x]['product'];
                        $order_detail->unit_price = $request->items[$x]['price'];
                        $order_detail->qty = $request->items[$x]['qty'];
                        $order_detail->total_price = $request->items[$x]['sub_total'];
                        $order_detail->discount = $request->items[$x]['discount'];
                        $order_detail->save();
                    }
                    DB::commit();
                    return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
                }
            } else {
                $order = Order::find($params['id']);
                $order->m_supplier_id = $params['supplier'];
                $order->total_amount = $params['total_amount'];
                $order->category = $params['category'];
                $order->tenor = $params['tenor'];
                if ($order->save()) {
                    $jml_detail = count($request->items);
                    $index      = 1;
                    for ($x = 0; $x < $jml_detail; $x++) {
                        if (empty($request->items[$x]['id'])) {
                            $order_detail = new OrderDetail();
                            $order_detail->data_product()->associate($order);
                            $order_detail->product_id = $request->items[$x]['product'];
                            $order_detail->unit_price = $request->items[$x]['recomend_price'];
                            $order_detail->qty_request = $request->items[$x]['qty_request'];
                            $order_detail->total_price = $request->items[$x]['sub_total'];
                            $order_detail->qty = $request->items[$x]['qty'];
                            $order_detail->save();
                        } else {
                            $order_detail = OrderDetail::find($request->items[$x]['id']);
                            $order_detail->data_product()->associate($order);
                            $order_detail->product_id = $request->items[$x]['product'];
                            $order_detail->unit_price = $request->items[$x]['recomend_price'];
                            $order_detail->qty_request = $request->items[$x]['qty_request'];
                            $order_detail->total_price = $request->items[$x]['sub_total'];
                            $order_detail->qty = $request->items[$x]['qty'];
                            $order_detail->save();
                        }
                    }
                    $index++;
                }
                DB::commit();
                return response()->json(['message' => 'has been updated successfully !'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function show(Request $request)
    {
        $order = Order::where('order.id', $request->id)
            ->select('order.id', 'nomor', 'order.created_at', 'customer_id', 'customer.name as customer_name', 'category', 'order.status', 'total_price', 'category', 'npwp')
            ->leftJoin('customer.customer', 'order.customer_id', 'customer.id')
            ->first();
        $arr_order  =  [
            'id' => $order->id,
            'nomor' => $order->nomor,
            'customer_id' => $order->customer_id,
            'customer_name' => $order->customer_name,
            'category' => $order->category,
            'total_price' => $order->total_price,
            'status' => $order->status,
            'npwp' => $order->npwp,
            'created_at' => date_format(date_create($order->created_at), 'd-m-Y H:i:s'),
        ];
        return new GeneralCollection($arr_order);
    }
    public function showDetail(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = OrderDetail::query()
            ->selectRaw("order_detail.id as detail_id, order_detail.product_id, order_detail.qty as qty,  product.name as product_name, product.code as product_code,order_detail.unit_price, order_detail.total_price, discount, qty_stock, product.total_timing, coalesce(order_detail.status, 0) as status, coalesce(qty_packed, 0)qty_packed, nomor")
            ->leftJoin('sales.order', 'order_detail.order_id', 'order.id')
            ->leftJoin('product.product', 'order_detail.product_id', 'product.id')
            ->leftJoin('product.data_stock_product', 'order_detail.product_id', 'data_stock_product.id')
            ->where('order_detail.order_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
    public function showDetailByOrder(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = OrderDetail::query()
            ->selectRaw("order_detail.id as detail_id, order_detail.product_id, order_detail.qty as qty,  product.name as product_name, product.code as product_code,order_detail.unit_price, order_detail.total_price, discount, qty_stock, product.total_timing, coalesce(order_detail.status, 0) as status, coalesce((qty_packed-delivery_order_detail.qty), 0)qty_packed, nomor")
            ->leftJoin('sales.order', 'order_detail.order_id', 'order.id')
            ->leftJoin('product.product', 'order_detail.product_id', 'product.id')
            ->leftJoin('product.data_stock_product', 'order_detail.product_id', 'data_stock_product.id')
            ->leftJoin('sales.delivery_order_detail', function ($join) {
                $join->on('delivery_order_detail.sales_order_id', '=', 'order.id');
                $join->on('order_detail.product_id', '=', 'delivery_order_detail.product_id');
            })
            ->where('order_detail.order_id', $searchParams['id'])
            ->whereRaw('order_detail.qty >= order_detail.qty_packed')
            ->where('order_detail.qty_packed', '!=', '0');

        return new GeneralCollection($data->paginate($limit));
    }
    public function destroy(Request $request)
    {
        try {
            $order = Order::findOrFail($request->id);
            $order->deleted_by = auth()->user()->id;
            $order->save();
            $order->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(["message" => " has been deleted successfully"], 200);
    }
    public function dataRequest(Request $request)
    {
        $searchParams = $request->all();
        $orderQuery = Order::query()->select("order.id", "order.created_at", "customer.name as customer_name", "customer.npwp", "order.nomor", "order.total_price", "order.status", "users.name as created_name", "customer_id", DB::raw("coalesce(total_payment,0) total_payment"))
            ->leftJoin('customer.customer', 'order.customer_id', 'customer.id')
            ->leftJoin('public.users', 'order.created_by', 'users.id')
            ->whereNotIn('order.status', [1, 3]);
        // ->groupBy("order.id", "order.created_at", "customer.name", "order.nomor", "order.total_amount", "order.status", "users.name");
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $orderQuery->where('customer.name', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $orderQuery->orderBy('order.id', 'desc');
        } else {
            $orderQuery->orderBy('order.id', 'asc');
        }
        return new GeneralCollection($orderQuery->paginate($limit));
    }
    public function confirmManufacture(Request $request)
    {
        $params     = $request->all();
        DB::beginTransaction();
        try {
            $manufacturere_product = new ManufactureOrder();
            $manufacturere_product->product_id = $params["product_id"];
            $manufacturere_product->qty = $params["qty"];
            $manufacturere_product->date = date_format(date_create($params["date"]), "Y-m-d H:i:s");
            $manufacturere_product->status = '1';
            $manufacturere_product->total_timing = $params["total_timing"];
            $manufacturere_product->sales_order_detail_id = $params["detail_id"];
            $manufacturere_product->save();

            $order_detail = OrderDetail::find($params["detail_id"]);
            $order_detail->status = '1';
            $order_detail->save();

            if ($order_detail->save()); {
                DB::commit();
                return response()->json(['message' => 'has been created successfully !'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function confirmPacked(Request $request)
    {
        $params     = $request->all();
        DB::beginTransaction();
        try {

            $jml        = count($request->items);
            $data       = array();
            $arr_control_id = array();
            for ($x = 0; $x < $jml; $x++) {
                $data[] = [
                    'control_id' => $request->items[$x]['control_id'],
                    'product_id' => $request->items[$x]['product_id'],
                    'harga_beli' => $request->items[$x]['harga_beli'],
                    'harga_jual' => $request->items[$x]['harga_jual'],
                    'description' => 'Terjual dengan nomor ' . $request->nomor,
                ];
                $arr_control_id[] = $request->items[$x]['control_id'];
            }
            $insert_stock_out = StockOut::insert($data);
            $updateStatus = StockIn::whereIn('control_id', $arr_control_id)->update(['is_out' => '1']);

            $order_detail = OrderDetail::find($params["detail_id"]);
            $order_detail->qty_packed = (int)$jml + (int)$order_detail->qty_packed;
            $order_detail->status = '2';
            $order_detail->save();

            if ($order_detail->save()); {
                DB::commit();
                return response()->json(['message' => 'has been created successfully !'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
