<?php

namespace App\Http\Controllers\Api\Sales;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Sales\{Order, PaymentTransaction};
use App\Laravue\Models\Customer\{Deposit, DepositTransaction};

class PaymentTransactionController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $bankQuery = Bank::query()
            ->selectRaw('id, name, no_rek');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $bankQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $bankQuery->orWhere('no_rek', 'LIKE', '%' . $keyword . '%');
        }
        $bankQuery->orderBy('name', 'asc');
        return new GeneralCollection($bankQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $params  = $request->all();
            $payment = new PaymentTransaction();
            $payment->order_id = $params['order_id'];
            $payment->m_payment_method_id = !empty($params['deposit']) ? '0' : $params['paymentMethod'];
            $payment->m_bank_id = $params['bank'];
            $payment->amount = !empty($params['totalPayment']) ? $params['totalPayment'] : $params['amount'];
            if (!empty($params['deposit'])) {
                $deposit = Deposit::where('customer_id', $params['customer_id'])->first();
                $deposit->amount = ((float)$deposit->amount) - (float)$params['amount'];
                $deposit->save();

                $deposit_transaction = new DepositTransaction();
                $deposit_transaction->deposit_id = $deposit->id;
                $deposit_transaction->amount = $params['amount'];
                $deposit_transaction->type = '-';
                $deposit_transaction->note = 'Untuk Pembayaran Nomor Nota ' . $params['nomor'];
                $deposit_transaction->save();
            }
            if ($payment->save()) {
                $order = Order::where('id', $params['order_id'])->first();
                $total_payment = ((float)(!empty($order->total_payment) ? $order->total_payment : 0))  + ((float)(!empty($params['totalPayment']) ? $params['totalPayment'] : $params['amount']));
                $order->status = ($total_payment == $order->total_price) ? 2 : 3;
                $order->total_payment = $total_payment;
                $order->save();

                DB::commit();
                return response()->json(['message' => 'Your payment was successful !'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function destroy(Request $request)
    {
        try {
            $bank = Bank::find($request->id);
            $bank->deleted_by = Auth::user()->id;
            $bank->deleted_at = new DateTime();
            $bank->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
