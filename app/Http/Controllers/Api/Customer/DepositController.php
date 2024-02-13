<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Deposit;
use App\Laravue\Models\Customer\DepositTransaction;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $depositQuery = Deposit::query()
            ->selectRaw('deposit.id, customer.name as customer, npwp, amount as balance')
            ->leftJoin('customer.customer', 'customer.id', 'deposit.customer_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $depositQuery->where('customer.name', 'LIKE', '%' . $keyword . '%');
            $depositQuery->orWhere('customer.npwp', 'LIKE', '%' . $keyword . '%');
        }
        $depositQuery->orderBy('customer.name', 'asc');
        return new GeneralCollection($depositQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();

        DB::beginTransaction();
        try {
            $get_deposit = Deposit::where('customer_id', $params['customer'])->first();
            if (empty($get_deposit)) {
                $deposit = Deposit::create([
                    'customer_id'       => $params['customer'],
                    'amount'            => $params['amount'],
                    'note'              => $params['note'],
                    'created_by'        => Auth::user()->id
                ]);

                $deposit_transaction = new DepositTransaction();
                $deposit_transaction->data_deposit()->associate($deposit);
                $deposit_transaction->amount = $params['amount'];
                $deposit_transaction->type = '+';
                $deposit_transaction->bank_id = $params['bank'];
                $deposit_transaction->note = 'Top Up Deposit Melalui Bank ' . $params['bank_name'] . ' (' . $params['no_rek'] . ')';
                $deposit_transaction->save();
                DB::commit();
                return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
            } else {
                $get_deposit->amount = ((float)$params['amount']) + (float)$get_deposit->amount;
                $get_deposit->save();

                $deposit_transaction = new DepositTransaction();
                $deposit_transaction->deposit_id = $get_deposit->id;
                $deposit_transaction->amount = $params['amount'];
                $deposit_transaction->type = '+';
                $deposit_transaction->bank_id = $params['bank'];
                $deposit_transaction->note = 'Top Up Deposit Melalui Bank ' . $params['bank_name'] . ' (' . $params['no_rek'] . ')';
                $deposit_transaction->save();
                DB::commit();
                return response()->json(['message' => 'new purchase order has been successfully created.'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function dataTransaction(Request $request)
    {
        $searchParams = $request->all();
        $depositTransactionQuery = DepositTransaction::query()
            ->selectRaw('type, amount, note, created_at')
            ->where('deposit_id', $searchParams['id']);
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $depositTransactionQuery->orderBy('created_at', 'asc');
        return new GeneralCollection($depositTransactionQuery->paginate($limit));
    }
    public function showByCustomer(Request $request)
    {
        $searchParams = $request->all();
        $deposit = Deposit::select('amount', 'id')
            ->where('customer_id', $searchParams['id'])
            ->first();
        $arr_deposit  =  [
            'id' => empty($deposit->id) ? 0 : $deposit->id,
            'amount' => empty($deposit->amount) ? 0 : $deposit->amount,
        ];
        return new GeneralCollection($arr_deposit);
    }
}
