<?php

namespace App\Http\Controllers\Api\Product\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\MasterResource;
use App\Laravue\Models\Product\Master\Material;

class MaterialController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $materialQuery = Material::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $materialQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $materialQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $materialQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $materialQuery->orderBy('id', 'desc');
        } else {
            $materialQuery->orderBy('id', 'asc');
        }
        return MasterResource::collection($materialQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $material = Material::create([
                'name'        => $params['name'],
                'code'        => $params['code'],
                'description' => $params['description'],
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $material = Material::find($params['id']);
            $material->name        = $params['name'];
            $material->code        = $params['code'];
            $material->description = $params['description'];
            $material->updated_by  = Auth::user()->id;
            $material->save();
        }
        return new MasterResource($material);
    }
    public function destroy(Request $request)
    {
        try {
            $material = Material::find($request->id);
            $material->deleted_by = Auth::user()->id;
            $material->deleted_at = new DateTime();
            $material->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
