<?php

namespace App\Http\Controllers\Api\Customer\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Master\MPostalCode;

class PostalCodeController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $sub_districtQuery = MPostalCode::query()
            ->selectRaw('m_postal_code.id, m_postal_code.postal_code, m_postal_code.m_subdistricts_id, m_postal_code.m_districts_id, m_postal_code.m_cities_id, m_postal_code.m_provinces_id, m_provinces.name as province, m_cities.name as city, m_districts.name as district, m_subdistricts.name as sub_district')
            ->leftJoin('customer.m_subdistricts', 'm_subdistricts.id', 'm_postal_code.m_subdistricts_id')
            ->leftJoin('customer.m_districts', 'm_districts.id', 'm_postal_code.m_districts_id')
            ->leftJoin('customer.m_cities', 'm_cities.id', 'm_postal_code.m_cities_id')
            ->leftJoin('customer.m_provinces', 'm_provinces.id', 'm_postal_code.m_provinces_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $sub_districtQuery->where('m_postal_code.postal_code', 'LIKE', '%' . $keyword . '%');
            $sub_districtQuery->orWhere('m_subdistricts.name', 'LIKE', '%' . $keyword . '%');
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
            $postal_code = MPostalCode::create([
                'm_provinces_id'    => $params['provinces'],
                'm_cities_id'       => $params['cities'],
                'm_districts_id'    => $params['district'],
                'm_subdistricts_id' => $params['sub_district'],
                'postal_code'       => $params['postal_code'],
                'created_by'        => Auth::user()->id
            ]);
        } else {
            $postal_code = MPostalCode::find($params['id']);
            $postal_code->m_provinces_id    = $params['provinces'];
            $postal_code->m_cities_id       = $params['cities'];
            $postal_code->m_districts_id    = $params['district'];
            $postal_code->m_subdistricts_id = $params['sub_district'];
            $postal_code->postal_code       = $params['postal_code'];
            $postal_code->updated_by        = Auth::user()->id;
            $postal_code->save();
        }
        return new GeneralCollection($postal_code);
    }
    public function destroy(Request $request)
    {
        try {
            $postal_code = MPostalCode::find($request->id);
            $postal_code->deleted_by = Auth::user()->id;
            $postal_code->deleted_at = new DateTime();
            $postal_code->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function showBySubDistrict(Request $request)
    {
        $searchParams = $request->all();
        $postal_code = MPostalCode::select('m_postal_code.id', 'm_postal_code.postal_code')
            ->where('m_postal_code.m_subdistricts_id', $searchParams['id'])
            ->first();
        $arr_postal_code  =  [
            'id' => $postal_code->id,
            'postal_code' => $postal_code->postal_code,
        ];
        return new GeneralCollection($arr_postal_code);
    }
}
