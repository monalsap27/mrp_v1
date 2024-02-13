<?php

namespace App\Http\Controllers\Api\Vendor;

use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Laravue\Models\Purchasing\Order;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Vendor\{DeliveryOrder, DeliveryOrderDetail, Transaction, TransactionDetail};

class DeliveryOrderController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $requestQuery = DeliveryOrder::query()->select("delivery_order.id", "delivery_order.date", "nomor", "received_date", "status");
        $keyword = Arr::get($searchParams, 'keyword', '');
        $sort = Arr::get($searchParams, 'sort', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if ($sort == '-id') {
            $requestQuery->orderBy('delivery_order.id', 'desc');
        } else {
            $requestQuery->orderBy('delivery_order.id', 'asc');
        }
        return new GeneralCollection($requestQuery->paginate($limit));
    }
    public function fetchNoDO()
    {
        $get_id_do = DeliveryOrder::selectRaw("COALESCE(max(id),01) as id")->first();
        $arr_data  =  [
            'no_do' => 'DO' . date("dmY") . $get_id_do->id . '.PO',
            'date' => date("d-m-Y h:i:s"),
        ];
        return new GeneralCollection($arr_data);
    }
    public function storeConfirm(Request $request)
    {
        DB::beginTransaction();
        try {
            $params      = $request->all();
            if (empty($params["id"])) {
                $delivery = new DeliveryOrder;
                $delivery->nomor = $params["nomor"];
                $delivery->status = 1;
                if ($delivery->save()) {
                    $jml_detail = count($request->items);
                    for ($x = 0; $x < $jml_detail; $x++) {
                        $trans = Transaction::where('id', $request->items[$x]["transaction_id"])->first();
                        $trans->status = 4;
                        $trans->save();

                        $order = Order::where('id', $trans->order_id)->first();
                        $order->status = ($order->status == 3) ? '5' : '4';
                        $order->save();

                        if (empty($request->items[$x]["id"])) {
                            $detail = new DeliveryOrderDetail();
                            $detail->data_delivery()->associate($delivery);
                            $detail->transaction_id = $request->items[$x]["transaction_id"];
                            $detail->qty = $request->items[$x]["qty"];
                            $detail->product_id = $request->items[$x]["product_id"];
                            $detail->save();
                        }
                    }
                    DB::commit();
                    return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function showDetail(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = TransactionDetail::query()
            ->selectRaw("transaction_detail.id as transaction_detail_id,transaction_detail.product_id,  case when delivery_order_detail.qty is not null then (transaction_detail.qty-delivery_order_detail.qty) else transaction_detail.qty end as qty, product.name as product_name, product.code as product_code, transaction_detail.product_id, transaction_detail.transaction_id, transaction.nomor")
            ->leftJoin('product.product', 'transaction_detail.product_id', 'product.id')
            ->leftJoin('vendor.transaction', 'transaction.id', 'transaction_detail.transaction_id')
            ->leftJoin('vendor.delivery_order_detail', 'transaction.id', 'delivery_order_detail.transaction_id')
            ->where('transaction_detail.transaction_id', $searchParams['id'])
            ->whereRaw('transaction_detail.qty > delivery_order_detail.qty');
        // ->whereRaw('transaction_detail.product_id not in (select product_id from vendor.delivery_order_detail where  transaction_id = ' . $searchParams['id'] . ')')->toSql();
        return new GeneralCollection($data->paginate($limit));
    }
    public function startShipment(Request $request)
    {
        $searchParams = $request->all();
        DB::beginTransaction();
        try {
            $data_product_detail = DeliveryOrder::where('id', $searchParams['id'])->first();
            $data_product_detail->status = 4;
            $data_product_detail->date = new DateTime();
            $data_product_detail->save();
            DB::commit();
            return response()->json(['message' => 'has been successfully !'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function showDelivery(Request $request)
    {
        $order = DeliveryOrder::where('id', $request->id)
            ->select('delivery_order.id', 'nomor', 'delivery_order.created_at',  'received_date', 'status')
            ->first();
        $arr_order  =  [
            'id' => $order->id,
            'nomor' => $order->nomor,
            'status' => $order->status,
            'created_at' => date_format(date_create($order->created_at), 'd-m-Y H:i:s'),
            'received_date' => empty($order->received_date) ? '' :  date_format(date_create($order->received_date), 'd-m-Y H:i:s'),
        ];
        return new GeneralCollection($arr_order);
    }
    public function showDeliveryDetail(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = DeliveryOrderDetail::query()
            ->selectRaw("delivery_order_detail.id, transaction.nomor,product.name, product.code,delivery_order_detail.qty")
            ->leftJoin('vendor.transaction', 'transaction.id', 'delivery_order_detail.transaction_id')
            ->leftJoin(
                'vendor.transaction_detail',
                function ($join) {
                    $join->on('delivery_order_detail.transaction_id', '=', 'transaction_detail.transaction_id');
                    $join->on('delivery_order_detail.product_id', '=', 'transaction_detail.product_id');
                }
            )
            ->leftJoin('product.product', 'product.id', 'delivery_order_detail.product_id')
            ->where('delivery_order_detail.delivery_order_id', $searchParams['id']);
        return new GeneralCollection($data->paginate($limit));
    }
    public function showDeliveryByTransaction(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = DeliveryOrder::query()
            ->selectRaw("delivery_order.nomor, delivery_order.date, delivery_order.received_date,delivery_order.status")
            ->leftJoin('vendor.delivery_order_detail', 'delivery_order.id', 'delivery_order_detail.delivery_order_id')
            ->leftJoin('vendor.transaction', 'delivery_order_detail.transaction_id', 'transaction.id')
            ->where('transaction.order_id', $searchParams['id']);
        return new GeneralCollection($data->paginate($limit));
    }
    public function dataDeliveryBySupplier(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = DeliveryOrder::query()
            ->selectRaw(" DISTINCT delivery_order.id, delivery_order.nomor, delivery_order.date, delivery_order.received_date,delivery_order.status, m_supplier.name as supplier_name")
            ->leftJoin('vendor.delivery_order_detail', 'delivery_order.id', 'delivery_order_detail.delivery_order_id')
            ->leftJoin('vendor.transaction', 'delivery_order_detail.transaction_id', 'transaction.id')
            ->leftJoin('purchasing.order', 'transaction.order_id', 'order.id')
            ->leftJoin('product.m_supplier', 'm_supplier.id', 'order.m_supplier_id');
        return new GeneralCollection($data->paginate($limit));
    }
}
