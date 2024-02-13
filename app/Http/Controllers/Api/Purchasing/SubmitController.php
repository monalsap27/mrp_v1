<?php

namespace App\Http\Controllers\Api\Purchasing;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Purchasing\Submit;
use Illuminate\Support\Facades\DB;

class SubmitController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function store(Request $request)
    {
        $params     = $request->all();
        DB::beginTransaction();
        try {
            $submit = Submit::create([
                'product_id'    => $params['product_id'],
                'm_supplier_id' => $params['supplier'],
                'qty'           => $params['qty'],
                'status'        => 1,
                'created_by'    => Auth::user()->id
            ]);
            if ($submit) {
                DB::commit();
                return response()->json(['message' => 'has been created successfully !'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $submitQuery = Submit::query()->selectRaw("submit.id, submit.created_at, product.name as product_name, submit.qty, m_supplier.name as supplier_name, users.name as request_by, submit.status")
            ->leftJoin('product.product', 'submit.product_id', 'product.id')
            ->leftJoin('product.m_supplier', 'submit.m_supplier_id', 'm_supplier.id')
            ->leftJoin('public.users', 'submit.created_by', 'users.id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $submitQuery->where('product.name', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $submitQuery->orderBy('submit.id', 'desc');
        } else {
            $submitQuery->orderBy('submit.id', 'asc');
        }
        return new GeneralCollection($submitQuery->paginate($limit));
    }
    public function storeReject(Request $request)
    {
        try {
            $submit = Submit::find($request->id);
            $submit->approved_by = Auth::user()->id;
            $submit->approved_at = new DateTime();
            $submit->note = $request->note;
            $submit->status = 3;
            $submit->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function dataSubmitBySupplier(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = Submit::query()
            ->selectRaw("submit.id as submit_id, submit.product_id, submit.qty as qty_request,product.name as product_name, product.code as product_code,ROUND(COALESCE(setting.unit_price,data_stock_product.harga_beli))unit_price, (CASE WHEN data_stock_product.safety_stock-data_stock_product.qty_stock <= 0 THEN 0 ELSE data_stock_product.safety_stock-data_stock_product.qty_stock END) as recomend_qty, COALESCE(setting.m_unit_id, product.m_unit_id)m_unit_id, m_unit.name as unit_name")
            ->leftJoin('product.product', 'submit.id', 'product.id')
            ->leftJoin('purchasing.setting', function ($join) {
                $join->on('submit.m_supplier_id', '=', 'setting.m_supplier_id');
                $join->on('submit.product_id', '=', 'setting.product_id');
            })
            ->leftJoin('product.m_unit', 'setting.m_unit_id', 'm_unit.id')
            ->leftJoin('product.data_stock_product', 'product.id', 'data_stock_product.id')
            ->where('purchasing.submit.status', '1')
            ->where('purchasing.submit.m_supplier_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
}
