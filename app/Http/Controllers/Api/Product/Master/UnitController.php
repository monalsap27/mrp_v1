<?php

namespace App\Http\Controllers\Api\Product\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\MasterResource;
use App\Laravue\Models\Product\Master\Unit;

class UnitController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $unitQuery = Unit::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $unitQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $unitQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $unitQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $unitQuery->orderBy('id', 'desc');
        } else {
            $unitQuery->orderBy('id', 'asc');
        }
        return MasterResource::collection($unitQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $unit = Unit::create([
                'name'        => $params['name'],
                'code'        => $params['code'],
                'description' => $params['description'],
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $unit = Unit::find($params['id']);
            $unit->name        = $params['name'];
            $unit->code        = $params['code'];
            $unit->description = $params['description'];
            $unit->updated_by  = Auth::user()->id;
            $unit->save();
        }
        return new MasterResource($unit);
    }
    public function destroy(Request $request)
    {
        try {
            $unit = Unit::find($request->id);
            $unit->deleted_by = Auth::user()->id;
            $unit->deleted_at = new DateTime();
            $unit->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
