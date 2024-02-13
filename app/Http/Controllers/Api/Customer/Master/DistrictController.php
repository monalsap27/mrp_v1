<?php

namespace App\Http\Controllers\Api\Customer\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Master\MDistricts;

class DistrictController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $districtQuery = MDistricts::query()
            ->selectRaw('m_districts.id, m_districts.name, m_districts.code, m_provinces.name as province, m_cities_id, m_cities.name as city, m_provinces_id')
            ->leftJoin('customer.m_cities', 'm_districts.m_cities_id', 'm_cities.id')
            ->leftJoin('customer.m_provinces', 'm_provinces.id', 'm_cities.m_provinces_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $districtQuery->where('m_districts.name', 'LIKE', '%' . $keyword . '%');
            $districtQuery->orWhere('m_districts.code', 'LIKE', '%' . $keyword . '%');
            $districtQuery->orWhere('m_cities.name', 'LIKE', '%' . $keyword . '%');
            $districtQuery->orWhere('m_provinces.name', 'LIKE', '%' . $keyword . '%');
        }
        $districtQuery->orderBy('m_cities.name', 'asc');

        // if ($sort == '-id') {
        //     $provincesQuery->orderBy('id', 'desc');
        // } else {
        //     $provincesQuery->orderBy('id', 'asc');
        // }

        return new GeneralCollection($districtQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $district = MDistricts::create([
                'name'           => $params['name'],
                'm_cities_id'    => $params['cities'],
                'code'           => empty($params['code']) ? null : $params['code'],
                'created_by'     => Auth::user()->id
            ]);
        } else {
            $district = MDistricts::find($params['id']);
            $district->name           = $params['name'];
            $district->code           = empty($params['code']) ? null : $params['code'];
            $district->m_cities_id    = $params['cities'];
            $district->updated_by     = Auth::user()->id;
            $district->save();
        }
        return new GeneralCollection($district);
    }
    public function destroy(Request $request)
    {
        try {
            $district = MDistricts::find($request->id);
            $district->deleted_by = Auth::user()->id;
            $district->deleted_at = new DateTime();
            $district->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function showByCity(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = MDistricts::query()
            ->selectRaw('m_districts.id, m_districts.name, m_districts.code, m_cities_id')
            ->where('m_districts.m_cities_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
}
