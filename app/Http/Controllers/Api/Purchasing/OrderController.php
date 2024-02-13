<?php

namespace App\Http\Controllers\Api\Purchasing;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Production\StockIn;
use App\Laravue\Models\Vendor\{DeliveryOrder, DeliveryOrderDetail, Transaction, TransactionDetail};
use App\Laravue\Models\Purchasing\{Submit, Order, OrderDetail};

class OrderController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function fetchNoPO()
    {
        $get_id_po = Order::selectRaw("COALESCE(max(id),01) as id")->first();
        $arr_data  =  [
            'no_po' => 'PO' . date("dmY") . $get_id_po->id,
            'date' => date("d-m-Y h:i:s"),
        ];
        return new GeneralCollection($arr_data);
    }
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $orderQuery = Order::query()->select("order.id", "order.created_at", "m_supplier.name as supplier_name", "order.nomor", "order.total_amount", "order.status", "users.name as created_name", DB::RAW("count(DISTINCT(delivery_order_id)) as count_delivery"))
            ->leftJoin('product.m_supplier', 'order.m_supplier_id', 'm_supplier.id')
            ->leftJoin('vendor.transaction', 'transaction.order_id', 'order.id')
            ->leftJoin('vendor.delivery_order_detail', 'transaction.id', 'delivery_order_detail.transaction_id')
            ->leftJoin('public.users', 'order.created_by', 'users.id')
            ->groupBy("order.id", "order.created_at", "m_supplier.name", "order.nomor", "order.total_amount", "order.status", "users.name");
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $orderQuery->where('m_supplier.name', 'LIKE', '%' . $keyword . '%');
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
                $order->m_supplier_id = $params['supplier'];
                $order->total_amount = $params['total_amount'];
                $order->nomor = $params['nomor'];
                $order->status = '1';
                if ($order->save()) {
                    $jml_detail = count($request->items);
                    for ($x = 0; $x < $jml_detail; $x++) {
                        $order_detail = new OrderDetail();
                        $order_detail->data_order()->associate($order);
                        $order_detail->product_id = $request->items[$x]['product'];
                        $order_detail->unit_price = $request->items[$x]['recomend_price'];
                        $order_detail->qty_request = $request->items[$x]['qty_request'];
                        $order_detail->total_price = $request->items[$x]['sub_total'];
                        $order_detail->qty = $request->items[$x]['qty'];
                        $order_detail->save();
                        if (!empty($request->items[$x]['submit_id'])) {
                            $data_submit = Submit::find($request->items[$x]['submit_id']);
                            $data_submit->status = '2';
                            $data_submit->approved_by = Auth::user()->id;
                            $data_submit->approved_at = new DateTime();
                            $data_submit->note        = 'Approved By System';
                            $data_submit->save();
                        }
                    }
                    DB::commit();
                    return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
                }
            } else {
                $order = Order::find($params['id']);
                $order->m_supplier_id = $params['supplier'];
                $order->total_amount = $params['total_amount'];
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
            ->select('order.id', 'nomor', 'order.created_at', 'm_supplier_id', 'm_supplier.name as supplier_name', 'users.name as user_name', 'order.status', 'total_amount', 'note')
            ->leftJoin('product.m_supplier', 'order.m_supplier_id', 'm_supplier.id')
            ->leftJoin('public.users', 'order.created_by', 'users.id')
            ->first();
        $arr_order  =  [
            'id' => $order->id,
            'nomor' => $order->nomor,
            'm_supplier_id' => $order->m_supplier_id,
            'supplier_name' => $order->supplier_name,
            'user_name' => $order->user_name,
            'total_amount' => $order->total_amount,
            'status' => $order->status,
            'note' => $order->note,
            'created_at' => date_format(date_create($order->created_at), 'd-m-Y H:i:s'),
        ];
        return new GeneralCollection($arr_order);
    }
    public function showDetail(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = OrderDetail::query()
            ->selectRaw("order_detail.id as detail_id,order_detail.product_id, order_detail.qty as qty,order_detail.qty_request, product.name as product_name, product.code as product_code,order_detail.unit_price,  0 as recomend_qty, order_detail.m_unit_id, m_unit.name as unit_name")
            ->leftJoin('product.product', 'order_detail.product_id', 'product.id')
            ->leftJoin('product.m_unit', 'order_detail.m_unit_id', 'm_unit.id')
            ->leftJoin('product.data_stock_product', 'product.id', 'data_stock_product.id')
            ->where('purchasing.order_detail.order_id', $searchParams['id']);

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
    public function dataApproval(Request $request)
    {
        $searchParams = $request->all();
        $orderQuery = Order::query()->select("order.id", "order.created_at", "m_supplier.name as supplier_name", "nomor", "total_amount", "order.status", "users.name as created_name")
            ->leftJoin('product.m_supplier', 'order.m_supplier_id', 'm_supplier.id')
            ->leftJoin('public.users', 'order.created_by', 'users.id');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $orderQuery->where('m_supplier.name', 'LIKE', '%' . $keyword . '%');
        }
        $orderQuery->orderBy('order.status', 'asc');
        return new GeneralCollection($orderQuery->paginate($limit));
    }
    public function approve(Request $request)
    {
        try {
            $order = Order::find($request->id);
            $order->status = $request->status;
            $order->note = $request->status == 2 ? '' : $request->note;
            $order->approved_by = auth()->user()->id;
            $order->approved_at = new DateTime();
            if ($order->save()) {
                $get_id_transaction = Transaction::selectRaw("COALESCE(max(id),01) as id")->first();
                $transaction = new Transaction();
                $transaction->nomor = 'TR' . date("dmY") . $get_id_transaction->id;
                $transaction->status = '1';
                $transaction->order_id = $request->id;
                $transaction->total_amount_request = $order->total_amount;
                if ($transaction->save()) {
                    TransactionDetail::insertUsing([
                        'product_id', 'qty_request', 'unit_price_request',  'total_price_request', 'transaction_id'
                    ], OrderDetail::select('product_id', 'qty', 'unit_price', 'total_price', DB::raw($transaction->id . ' as transaction_id'))
                        ->where('order_id', $request->id));
                    DB::commit();
                    return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
                }
            }
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
    }
    public function showDataDeliveryInbound(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = OrderDetail::query()
            ->selectRaw('DISTINCT "order"."nomor", order_detail.order_id, order_detail.id as order_detail_id, delivery_order.id as delivery_order_id,order_detail.product_id,delivery_order.date,product.name as product_name, product.code as product_code, order_detail.qty, received_date, transaction_detail.unit_price')
            ->leftJoin('vendor.transaction', 'order_detail.order_id', 'transaction.order_id')
            ->leftJoin(
                'vendor.transaction_detail',
                function ($join) {
                    $join->on('transaction_detail.transaction_id', '=', 'transaction.id');
                    $join->on('order_detail.product_id', '=', 'transaction_detail.product_id');
                }
            )
            ->leftJoin('vendor.delivery_order_detail', 'transaction.id', 'delivery_order_detail.transaction_id')
            ->leftJoin('product.product', 'product.id', 'order_detail.product_id')
            ->leftJoin('purchasing.order', 'order_detail.order_id', 'order.id')
            ->leftJoin('vendor.delivery_order', 'delivery_order.id', 'delivery_order_detail.delivery_order_id')
            ->where('delivery_order.id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
    public function confirmInboundPO(Request $request)
    {

        DB::beginTransaction();
        try {
            $jml_detail = count($request->items);
            for ($x = 0; $x < $jml_detail; $x++) {
                $order_detail = OrderDetail::where("id", $request->items[$x]['order_detail_id'])->where("product_id", $request->items[$x]["product_id"])->first();
                $order_detail->qty_received = $request->items[$x]['qty_confirm'];
                $order_detail->save();

                $order = Order::where('id', $order_detail->order_id)->first();
                $order->status = '6';
                $order->save();

                $delivery = DeliveryOrder::where('id', $request->items[$x]['delivery_order_id'])->first();
                $delivery->status = '6';
                $delivery->received_date = new DateTime();
                $delivery->save();

                $order_detail = DeliveryOrderDetail::where("delivery_order_id", $request->items[$x]['delivery_order_id'])->where("product_id", $request->items[$x]["product_id"])->first();
                $order_detail->qty_received = $request->items[$x]['qty_confirm'];
                $order_detail->save();

                for ($y = 0; $y < $request->items[$x]['qty_confirm']; $y++) {
                    $get_id_max = StockIn::where("product_id", $request->items[$x]["product_id"])->max("id");

                    $stock_in = new StockIn();
                    $stock_in->control_id = $request->items[$x]["product_id"] . date("ymd") . (empty($get_id_max) ? '1' : ($get_id_max + 1));
                    $stock_in->product_id = $request->items[$x]["product_id"];
                    $stock_in->harga_beli = $request->items[$x]["unit_price"];
                    $stock_in->harga_jual = 0;
                    $stock_in->description = 'Inbound PO ' . $request->items[$x]["nomor"];
                    $stock_in->save();
                }
            }
            DB::commit();
            return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
