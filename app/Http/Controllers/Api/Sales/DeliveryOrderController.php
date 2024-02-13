<?php

namespace App\Http\Controllers\Api\Sales;

use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Sales\{DeliveryOrder, DeliveryOrderDetail, OrderDetail};

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
            'no_do' => 'DO' . date("dmY") . $get_id_do->id . '.SO',
            'date' => date("d-m-Y h:i:s"),
        ];
        return new GeneralCollection($arr_data);
    }
    public function store(Request $request)
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
                        $order_detail = OrderDetail::where('id', $request->items[$x]["detail_id"])->first();
                        $order_detail->status = '4';
                        $order_detail->save();
                        if (empty($request->items[$x]["id"])) {
                            $detail = new DeliveryOrderDetail();
                            $detail->data_delivery()->associate($delivery);
                            $detail->sales_order_id = $order_detail->order_id;
                            $detail->qty = $request->items[$x]["qty_packed"];
                            $detail->product_id = $request->items[$x]["product_id"];
                            $detail->save();
                        }
                    }
                    DB::commit();
                    return response()->json(['message' => 'new delivery order has been successfully created.'], 200);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
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
    public function dataDeliveryByCustomer(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = DeliveryOrder::query()
            ->selectRaw(" DISTINCT delivery_order.id, delivery_order.nomor, delivery_order.date, delivery_order.received_date,delivery_order.status, customer.name as customer_name")
            ->leftJoin('sales.delivery_order_detail', 'delivery_order.id', 'delivery_order_detail.delivery_order_id')
            ->leftJoin('sales.order', 'delivery_order_detail.sales_order_id', 'order.id')
            ->leftJoin('customer.customer', 'customer.id', 'order.customer_id');
        return new GeneralCollection($data->paginate($limit));
    }
}
