<?php

namespace App\Http\Controllers\Api\Customer\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Master\MSubdistricts;

class SubDistrictController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $sub_districtQuery = MSubdistricts::query()
            ->selectRaw('m_subdistricts.id, m_subdistricts.name, m_subdistricts.code, m_provinces.name as province, m_cities_id, m_cities.name as city, m_provinces_id, m_subdistricts.m_districts_id, m_districts.name as district')
            ->leftJoin('customer.m_districts', 'm_districts.id', 'm_subdistricts.m_districts_id')
            ->leftJoin('customer.m_cities', 'm_districts.m_cities_id', 'm_cities.id')
            ->leftJoin('customer.m_provinces', 'm_provinces.id', 'm_cities.m_provinces_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $sub_districtQuery->where('m_subdistricts.name', 'LIKE', '%' . $keyword . '%');
            $sub_districtQuery->orWhere('m_districts.name', 'LIKE', '%' . $keyword . '%');
            $sub_districtQuery->orWhere('m_districts.code', 'LIKE', '%' . $keyword . '%');
            $sub_districtQuery->orWhere('m_cities.name', 'LIKE', '%' . $keyword . '%');
            $sub_districtQuery->orWhere('m_provinces.name', 'LIKE', '%' . $keyword . '%');
        }
        $sub_districtQuery->orderBy('m_cities.name', 'asc');

        // if ($sort == '-id') {
        //     $provincesQuery->orderBy('id', 'desc');
        // } else {
        //     $provincesQuery->orderBy('id', 'asc');
        // }

        return new GeneralCollection($sub_districtQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $sub_district = MSubdistricts::create([
                'name'           => $params['name'],
                'm_districts_id' => $params['district'],
                'code'           => empty($params['code']) ? null : $params['code'],
                'created_by'     => Auth::user()->id
            ]);
        } else {
            $sub_district = MSubdistricts::find($params['id']);
            $sub_district->name           = $params['name'];
            $sub_district->code           = empty($params['code']) ? null : $params['code'];
            $sub_district->m_districts_id = $params['district'];
            $sub_district->updated_by     = Auth::user()->id;
            $sub_district->save();
        }
        return new GeneralCollection($sub_district);
    }
    public function destroy(Request $request)
    {
        try {
            $sub_district = MSubdistricts::find($request->id);
            $sub_district->deleted_by = Auth::user()->id;
            $sub_district->deleted_at = new DateTime();
            $sub_district->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function showByDistrict(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = MSubdistricts::query()
            ->selectRaw('m_subdistricts.id, m_subdistricts.name, m_subdistricts.code, m_districts_id')
            ->where('m_subdistricts.m_districts_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
}
