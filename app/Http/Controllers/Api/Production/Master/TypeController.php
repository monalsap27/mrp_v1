<?php

namespace App\Http\Controllers\Api\Production\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Laravue\Models\Production\Master\Type;
use App\Http\Resources\Production\GeneralMasterResource;

class TypeController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $typeQuery = Type::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $typeQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $typeQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $typeQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $typeQuery->orderBy('id', 'desc');
        } else {
            $typeQuery->orderBy('id', 'asc');
        }
        return GeneralMasterResource::collection($typeQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $type = Type::create([
                'name'        => $params['name'],
                'code'        => $params['code'],
                'description' => $params['description'],
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $type = Type::find($params['id']);
            $type->name        = $params['name'];
            $type->code        = $params['code'];
            $type->description = $params['description'];
            $type->updated_by  = Auth::user()->id;
            $type->save();
        }
        return new GeneralMasterResource($type);
    }
    public function destroy(Request $request)
    {
        try {
            $type = Type::find($request->id);
            $type->deleted_by = Auth::user()->id;
            $type->deleted_at = new DateTime();
            $type->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
