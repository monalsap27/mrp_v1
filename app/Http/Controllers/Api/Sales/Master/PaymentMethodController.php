<?php

namespace App\Http\Controllers\Api\Sales\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Sales\Master\{PaymentMethod};

class PaymentMethodController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $paymentMethodQuery = PaymentMethod::query()
            ->selectRaw('id, name, code');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $paymentMethodQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $paymentMethodQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        $paymentMethodQuery->orderBy('name', 'asc');
        return new GeneralCollection($paymentMethodQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $params     = $request->all();
            if (empty($params['id'])) {
                $payment_method = new PaymentMethod();
                $payment_method->name = $params['name'];
                $payment_method->code = $params['code'];
                if ($payment_method->save()) {
                    DB::commit();
                    return response()->json(['message' => 'new has been successfully created.'], 200);
                }
            } else {
                $payment_method = PaymentMethod::find($params['id']);
                $payment_method->name = $params['name'];
                $payment_method->code = $params['code'];
                if ($payment_method->save()) {
                    DB::commit();
                    return response()->json(['message' => 'has been updated successfully !'], 200);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function destroy(Request $request)
    {
        try {
            $payment_method = PaymentMethod::find($request->id);
            $payment_method->deleted_by = Auth::user()->id;
            $payment_method->deleted_at = new DateTime();
            $payment_method->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function ShowPaymentMethod(Request $request)
    {
        $searchParams = $request->all();
        $payment_method = PaymentMethod::select('id', 'name', 'code')
            ->where('id', $searchParams['id'])
            ->first();
        $arr_payment_method  =  [
            'id' => $payment_method->id,
            'name' => $payment_method->name,
            'code' => $payment_method->code,
        ];
        return new GeneralCollection($arr_payment_method);
    }
}
