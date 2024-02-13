<?php

namespace App\Http\Controllers\Api\Customer\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Master\MProvinces;

class ProvincesController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $provincesQuery = MProvinces::query()
            ->selectRaw('id, name, code, status');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $provincesQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $provincesQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        $provincesQuery->orderBy('name', 'asc');

        // if ($sort == '-id') {
        //     $provincesQuery->orderBy('id', 'desc');
        // } else {
        //     $provincesQuery->orderBy('id', 'asc');
        // }
        return new GeneralCollection($provincesQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $provinces = MProvinces::create([
                'name'        => $params['name'],
                'code'        => empty($params['code']) ? null : $params['code'],
                'location_id' => '1',
                'status'      => '1',
                'created_by'  => Auth::user()->id
            ]);
        } else {
            $provinces = MProvinces::find($params['id']);
            $provinces->name        = $params['name'];
            $provinces->code        = empty($params['code']) ? null : $params['code'];
            $provinces->updated_by  = Auth::user()->id;
            $provinces->save();
        }
        return new GeneralCollection($provinces);
    }
    public function destroy(Request $request)
    {
        try {
            $provinces = MProvinces::find($request->id);
            $provinces->deleted_by = Auth::user()->id;
            $provinces->deleted_at = new DateTime();
            $provinces->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
