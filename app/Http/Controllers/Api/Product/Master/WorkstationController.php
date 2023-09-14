<?php

namespace App\Http\Controllers\Api\Product\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Product\MasterResource;
use App\Laravue\Models\Product\Master\Workstation;

class WorkstationController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $workstationQuery = Workstation::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $workstationQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $workstationQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $workstationQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $workstationQuery->orderBy('id', 'desc');
        } else {
            $workstationQuery->orderBy('id', 'asc');
        }
        return MasterResource::collection($workstationQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $workstation = Workstation::create([
                'name'        => $params['name'],
                'code'        => $params['code'],
                'description' => $params['description'],
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $workstation = Workstation::find($params['id']);
            $workstation->name        = $params['name'];
            $workstation->code        = $params['code'];
            $workstation->description = $params['description'];
            $workstation->updated_by  = Auth::user()->id;
            $workstation->save();
        }
        return new MasterResource($workstation);
    }
    public function destroy(Request $request)
    {
        try {
            $workstation = Workstation::find($request->id);
            $workstation->deleted_by = Auth::user()->id;
            $workstation->deleted_at = new DateTime();
            $workstation->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
