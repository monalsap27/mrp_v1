<?php

namespace App\Http\Controllers\Api\Production;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Production\{Workstation, WorkstationDetail, WorkstationGroup};
use App\Http\Resources\Production\{WorkstationResource, WorkstationDetailResource};

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
        return WorkstationResource::collection($workstationQuery->paginate($limit));
    }
    public function show(Request $request)
    {
        $workstation        = Workstation::where('id', $request->id)->first();
        $arr_workstation  = (object) [
            'id' => $workstation->id,
            'name' => $workstation->name,
            'description' => $workstation->description,
            'code' => $workstation->code,
            'total_workforce' => $workstation->total_workforce,
            'change_material' => $workstation->change_material,
            'workforce' => (array)json_decode($workstation->workforce),
            'timing' => $this->hour($workstation->timing),
        ];
        return new WorkstationResource($arr_workstation);
    }
    public function showDetail(Request $request)
    {
        $workstation_detail  = WorkstationDetail::where('workstation_detail.workstation_id', $request->id)
            ->leftJoin('product.product', 'workstation_detail.product_id', '=', 'product.id')
            ->get();
        return WorkstationDetailResource::collection($workstation_detail);
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            if (empty($request->id)) {
                $workstation                  = new Workstation();
                $workstation->name            = $request->name;
                $workstation->code            = $request->code;
                $workstation->timing          = $this->minutes($request->timing);
                $workstation->description     = $request->description;
                $workstation->workforce       = json_encode($request->workforce, JSON_FORCE_OBJECT);
                $workstation->total_workforce = count($request->workforce);
                $workstation->change_material = empty($request->change_material) ? 'f' : $request->change_material;
                $workstation->created_by      = Auth::user()->id;
                $workstation->save();
                $jml_detail                 = count($request->items);
                for ($x = 0; $x < $jml_detail; $x++) {
                    $workstation_detail             = new WorkstationDetail();
                    $workstation_detail->data_workstation()->associate($workstation);
                    $workstation_detail->product_id = $request->items[$x]['product'];
                    $workstation_detail->qty        = $request->items[$x]['qty'];
                    $workstation_detail->save();
                }
            } else {
                $workstation                  = Workstation::find($request->id);
                $workstation->name            = $request->name;
                $workstation->code            = $request->code;
                $workstation->timing          = $this->minutes($request->timing);
                $workstation->description     = $request->description;
                $workstation->workforce       = json_encode($request->workforce, JSON_FORCE_OBJECT);
                $workstation->total_workforce = count($request->workforce);
                $workstation->change_material = empty($request->change_material) ? 'f' : $request->change_material;
                $workstation->created_by      = Auth::user()->id;
                $workstation->save();
                WorkstationDetail::where('workstation_id', $request->id)->delete();
                $jml_detail = count($request->items);
                for ($x = 0; $x < $jml_detail; $x++) {
                    if (empty($request->items[$x]['id'])) {
                        $workstation_detail             = new WorkstationDetail();
                        $workstation_detail->workstation_id = $request->id;
                        $workstation_detail->product_id = $request->items[$x]['product'];
                        $workstation_detail->qty        = $request->items[$x]['qty'];
                        $workstation_detail->save();
                    } else {
                        WorkstationDetail::withTrashed()->find($request->items[$x]['id'])->restore();
                        $workstation_detail = WorkstationDetail::find($request->items[$x]['id']);
                        $workstation_detail->product_id = $request->items[$x]['product'];
                        $workstation_detail->qty        = $request->items[$x]['qty'];
                        $workstation_detail->save();
                    }
                }
            }
            DB::commit();
            return response()->json([
                'error'    => 0,
                'message' => 'Data Berhasil Disimpan !'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function destroy(Request $request)
    {
        try {
            $workstation = Workstation::find($request->id);
            $workstation->deleted_by = Auth::user()->id;
            $workstation->deleted_at = new DateTime();
            $workstation->save();

            WorkstationDetail::where('workstation_id', $request->id)
                ->update(['deleted_by' => Auth::user()->id, 'deleted_at' => new DateTime()]);
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function showByGroup(Request $request)
    {
        $get_data_group = WorkstationGroup::where('id', $request->id)->first();
        $get_workstation = Workstation::select('workstation.name as workstation_name', 'workstation.description', 'workstation.total_workforce', 'workstation.timing', 'c.name as material', 'b.qty', 'b.product_id as material_id', 'workstation.id as workstation_id')
            ->leftJoin('product.workstation_detail as b', 'workstation.id', 'b.workstation_id')
            ->leftJoin('product.product as c', 'b.product_id', 'c.id')
            ->whereIn('workstation.id', ((array) json_decode($get_data_group->arr_workstation_id)))
            ->orderBy('workstation.id', 'asc')
            ->get();
        return new GeneralCollection($get_workstation);
    }
    public function minutes($time)
    {
        $time = explode(':', $time);
        return ($time[0] * 60) + ($time[1]);
    }
    public function hour($time)
    {
        $hours = floor($time / 60);
        $min = $time - ($hours * 60);
        return ((strlen($hours) == 1 ? "0" . $hours : $hours) . ":" . (strlen($min) == 1 ? "0" . $min : $min));
    }
}
