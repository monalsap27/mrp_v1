<?php

namespace App\Http\Controllers\Api\Vendor;

use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Vendor\{Transaction, TransactionDetail};
use App\Laravue\Models\Purchasing\{Order};

class RequestController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function dataRequest(Request $request)
    {
        $searchParams = $request->all();
        $requestQuery = Transaction::query()->select("transaction.id", "transaction.created_at",  "nomor", DB::raw("COALESCE(total_amount, 0) as total_amount"), "status");
        $keyword = Arr::get($searchParams, 'keyword', '');
        $sort = Arr::get($searchParams, 'sort', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if ($sort == '-id') {
            $requestQuery->orderBy('transaction.id', 'desc');
        } else {
            $requestQuery->orderBy('transaction.id', 'asc');
        }
        return new GeneralCollection($requestQuery->paginate($limit));
    }
    public function showRequest(Request $request)
    {
        $order = Transaction::where('id', $request->id)
            ->select('transaction.id', 'nomor', 'transaction.created_at',  DB::RAW('COALESCE(total_amount, total_amount_request)total_amount'), 'status')
            ->first();
        $arr_order  =  [
            'id' => $order->id,
            'nomor' => $order->nomor,
            'status' => $order->status,
            'total_amount' => $order->total_amount,
            'created_at' => date_format(date_create($order->created_at), 'd-m-Y H:i:s'),
        ];
        return new GeneralCollection($arr_order);
    }
    public function showDetail(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = TransactionDetail::query()
            ->selectRaw("transaction_detail.id as transaction_detail_id,transaction_detail.product_id, COALESCE(qty, qty_request) as qty, qty_request, product.name as product_name, product.code as product_code, round(coalesce(unit_price,unit_price_request))unit_price, unit_price_request, total_price_request, transaction_detail.product_id, transaction_id, transaction.nomor")
            ->leftJoin('product.product', 'transaction_detail.product_id', 'product.id')
            ->leftJoin('vendor.transaction', 'transaction.id', 'transaction_detail.transaction_id')
            ->where('transaction_detail.transaction_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
    public function storeConfirm(Request $request)
    {
        DB::beginTransaction();
        try {
            $params      = $request->all();
            $transaction = Transaction::where('id', $params['id'])->first();
            $transaction->total_amount = $params['status'] == 2 ? $params['total_amount'] : '0';
            $transaction->status = $params['status'];
            $status_order = $params['status'];
            if ($transaction->save()) {
                if ($params['status'] == 2) {
                    $jml_detail = count($request->items);
                    for ($x = 0; $x < $jml_detail; $x++) {
                        if ($params['status'] == 2 && $request->items[$x]['qty'] == 0) {
                            $status_order = 3;
                        }
                        $transaction_detail = TransactionDetail::where('id', $request->items[$x]['transaction_detail_id'])->first();
                        $transaction_detail->qty = $request->items[$x]['qty'];
                        $transaction_detail->unit_price = $request->items[$x]['unit_price'];
                        $transaction_detail->total_price = $request->items[$x]['total'];
                        $transaction_detail->save();
                    }
                } else {
                    $status_order = 0;
                    $note = 'Rejected By Vendor';
                }
                $order = Order::where('id', $transaction->order_id)->first();
                $order->status = $status_order;
                $order->note = !empty($note) ? $note : null;
                $order->save();
                DB::commit();
                return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function showQtyVendor(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = Transaction::query()
            ->selectRaw("transaction_detail.id as transaction_detail_id,  transaction_detail.qty, transaction_detail.qty_request, product.name as product_name, product.code as product_code")
            ->leftJoin('vendor.transaction_detail', 'transaction.id', 'transaction_detail.transaction_id')
            ->leftJoin('product.product', 'transaction_detail.product_id', 'product.id')
            ->where('transaction.order_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
    public function dataTransaction(Request $request)
    {
        $searchParams = $request->all();
        $requestQuery = Transaction::query()->select("transaction.id", "transaction.created_at",  "nomor", DB::raw("COALESCE(total_amount, 0) as total_amount"), "status");
        $keyword = Arr::get($searchParams, 'keyword', '');
        $sort = Arr::get($searchParams, 'sort', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if ($sort == '-id') {
            $requestQuery->orderBy('transaction.id', 'desc');
        } else {
            $requestQuery->orderBy('transaction.id', 'asc');
        }
        return new GeneralCollection($requestQuery->paginate($limit));
    }
}
