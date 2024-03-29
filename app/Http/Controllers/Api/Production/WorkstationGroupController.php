<?php

namespace App\Http\Controllers\Api\Production;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Production\{Workstation, WorkstationGroup};
use App\Http\Resources\Production\WorkstationGroupResource;

class WorkstationGroupController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $wsQuery = WorkstationGroup::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $wsQuery->where('name', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $wsQuery->orderBy('id', 'desc');
        } else {
            $wsQuery->orderBy('id', 'asc');
        }
        return WorkstationGroupResource::collection($wsQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $tot_workforce = 0;
        $tot_timing    = 0;
        $arr_workstation = array();

        foreach ($request->list2 as $val) {
            $tot_workforce += (int)$val['total_workforce'];
            $tot_timing += (int)$val['timing'];
            array_push($arr_workstation, $val['id']);
        }
        $ws_group = new WorkstationGroup();
        $ws_group->name = $request->name;
        $ws_group->code = $request->code;
        $ws_group->total_workforce = $tot_workforce;
        $ws_group->total_timing = $tot_timing;
        $ws_group->arr_workstation_id = json_encode($arr_workstation, JSON_FORCE_OBJECT);
        $ws_group->created_by = Auth::user()->id;
        $ws_group->save();
    }
    public function dataDetail(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = WorkstationGroup::selectRaw("workstation_group.id,arr_workstation_id")
            ->where('id', $searchParams['id'])->first();
        $arr_detail = array();
        $arr_id = array();
        foreach (json_decode($data->arr_workstation_id) as $data) {
            array_push($arr_id, $data);
            $workstation = Workstation::find($data);
            $arr_detail[] = array(
                'id' => $workstation->id,
                'name' => $workstation->name,
                'code' => $workstation->code,
                'status' => '1',
            );
        }
        $workstation  = Workstation::select('id', 'name', 'code')->whereNotIn('id', $arr_id)->get();
        foreach ($workstation as $data) {
            $arr_detail[] = array(
                'id' => $data->id,
                'name' => $data->name,
                'code' => $data->code,
                'status' => '0',
            );
        }

        return new GeneralCollection($arr_detail);
    }
}
