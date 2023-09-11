<?php

namespace App\Http\Controllers\Api\Product\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\MasterResource;
use App\Laravue\Models\Product\Master\Categories;

class CatergoriesController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $categoriesQuery = Categories::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $categoriesQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $categoriesQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $categoriesQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $categoriesQuery->orderBy('id', 'desc');
        } else {
            $categoriesQuery->orderBy('id', 'asc');
        }
        return MasterResource::collection($categoriesQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $categories = Categories::create([
                'name'        => $params['name'],
                'code'        => $params['code'],
                'description' => $params['description'],
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $categories = Categories::find($params['id']);
            $categories->name        = $params['name'];
            $categories->code        = $params['code'];
            $categories->description = $params['description'];
            $categories->updated_by  = Auth::user()->id;
            $categories->save();
        }
        return new MasterResource($categories);
    }
    public function destroy(Request $request)
    {
        try {
            $categories = Categories::find($request->id);
            $categories->deleted_by = Auth::user()->id;
            $categories->deleted_at = new DateTime();
            $categories->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
