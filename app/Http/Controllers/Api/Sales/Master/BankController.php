<?php

namespace App\Http\Controllers\Api\Sales\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Sales\Master\{Bank};

class BankController extends Controller
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
            $params     = $request->all();
            if (empty($params['id'])) {
                $bank = new Bank();
                $bank->name = $params['name'];
                $bank->no_rek = $params['no_rek'];
                if ($bank->save()) {
                    DB::commit();
                    return response()->json(['message' => 'new has been successfully created.'], 200);
                }
            } else {
                $bank = Bank::find($params['id']);
                $bank->name = $params['name'];
                $bank->no_rek = $params['no_rek'];
                if ($bank->save()) {
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
