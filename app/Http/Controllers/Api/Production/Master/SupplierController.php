<?php

namespace App\Http\Controllers\Api\Production\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Laravue\Models\Production\Master\Supplier;
use App\Http\Resources\Production\MasterSupplierResource;

class SupplierController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $supplierQuery = Supplier::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $supplierQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $supplierQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $supplierQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
            $supplierQuery->orWhere('phone', 'LIKE', '%' . $keyword . '%');
            $supplierQuery->orWhere('address', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $supplierQuery->orderBy('id', 'desc');
        } else {
            $supplierQuery->orderBy('id', 'asc');
        }
        return MasterSupplierResource::collection($supplierQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $supplier = Supplier::create([
                'name'        => $params['name'],
                'code'        => $params['code'],
                'description' => $params['description'],
                'phone'       => $params['phone'],
                'address'     => $params['address'],
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $supplier = Supplier::find($params['id']);
            $supplier->name        = $params['name'];
            $supplier->code        = $params['code'];
            $supplier->phone       = $params['phone'];
            $supplier->address     = $params['address'];
            $supplier->updated_by  = Auth::user()->id;
            $supplier->save();
        }
        return new MasterSupplierResource($supplier);
    }
    public function destroy(Request $request)
    {
        try {
            $supplier = Supplier::find($request->id);
            $supplier->deleted_by = Auth::user()->id;
            $supplier->deleted_at = new DateTime();
            $supplier->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
