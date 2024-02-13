<?php

namespace App\Http\Controllers\Api\Customer\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Master\MCities;

class CitiesController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $citiesQuery = MCities::query()
            ->selectRaw('m_cities.id, m_cities.name, m_cities.code, m_provinces.name as province, m_provinces_id')
            ->leftJoin('customer.m_provinces', 'm_provinces.id', 'm_cities.m_provinces_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $citiesQuery->where('m_cities.name', 'LIKE', '%' . $keyword . '%');
            $citiesQuery->orWhere('m_cities.code', 'LIKE', '%' . $keyword . '%');
            $citiesQuery->orWhere('m_provinces.code', 'LIKE', '%' . $keyword . '%');
        }
        $citiesQuery->orderBy('m_cities.name', 'asc');

        // if ($sort == '-id') {
        //     $provincesQuery->orderBy('id', 'desc');
        // } else {
        //     $provincesQuery->orderBy('id', 'asc');
        // }
        return new GeneralCollection($citiesQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $cities = MCities::create([
                'name'           => $params['name'],
                'm_provinces_id' => $params['provinces'],
                'code'           => empty($params['code']) ? null : $params['code'],
                'created_by'     => Auth::user()->id
            ]);
        } else {
            $cities = MCities::find($params['id']);
            $cities->name           = $params['name'];
            $cities->code           = empty($params['code']) ? null : $params['code'];
            $cities->m_provinces_id = $params['provinces'];
            $cities->updated_by     = Auth::user()->id;
            $cities->save();
        }
        return new GeneralCollection($cities);
    }
    public function destroy(Request $request)
    {
        try {
            $cities = MCities::find($request->id);
            $cities->deleted_by = Auth::user()->id;
            $cities->deleted_at = new DateTime();
            $cities->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function showByProvince(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data = MCities::query()
            ->selectRaw('m_cities.id, m_cities.name, m_cities.code, m_provinces_id')
            ->where('m_cities.m_provinces_id', $searchParams['id']);

        return new GeneralCollection($data->paginate($limit));
    }
}
