<?php

namespace App\Http\Controllers\Api\Customer;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Customer\Customer;

class CustomerController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $customerQuery = Customer::query()
            ->selectRaw('customer.id, customer.name, npwp, nik, phone, address, customer.m_postal_code_id, v_address_postal_code.postal_code, v_address_postal_code.name as name_postal_code')
            ->leftJoin('customer.v_address_postal_code', 'v_address_postal_code.m_postal_code_id', 'customer.m_postal_code_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $customerQuery->where('customer.name', 'LIKE', '%' . $keyword . '%');
            $customerQuery->orWhere('customer.npwp', 'LIKE', '%' . $keyword . '%');
            $customerQuery->orWhere('v_address_postal_code.name', 'LIKE', '%' . $keyword . '%');
        }
        $customerQuery->orderBy('customer.name', 'asc');
        return new GeneralCollection($customerQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        $get_customer = Customer::where('npwp', $params['npwp'])->first();
        if (empty($get_customer)) {
            if (empty($params['id'])) {
                $customer = Customer::create([
                    'name'              => $params['name'],
                    'nik'               => empty($params['nik']) ? null : $params['nik'],
                    'npwp'              => $params['npwp'],
                    'phone'             => $params['phone'],
                    'address'           => $params['address'],
                    'm_postal_code_id'  => $params['postal_code_id'],
                    'created_by'        => Auth::user()->id
                ]);
            } else {
                $customer = Customer::find($params['id']);
                $customer->name             = $params['name'];
                $customer->nik              = empty($params['nik']) ? null : $params['nik'];
                $customer->npwp             = $params['npwp'];
                $customer->phone            = $params['phone'];
                $customer->address          = $params['address'];
                $customer->m_postal_code_id = $params['postal_code_id'];
                $customer->updated_by       = Auth::user()->id;
                $customer->save();
            }
            return new GeneralCollection($customer);
        } else {
            return response()->json(['message' => 'customer is already using.', 'is_empty' => 0], 200);
        }
    }
    public function destroy(Request $request)
    {
        try {
            $customer = Customer::find($request->id);
            $customer->deleted_by = Auth::user()->id;
            $customer->deleted_at = new DateTime();
            $customer->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
    public function fetchShowCustomer(Request $request)
    {
        $searchParams = $request->all();
        $customer = Customer::selectRaw('customer.id, customer.name, npwp, nik, phone, address, customer.m_postal_code_id, m_postal_code.m_subdistricts_id, m_postal_code.m_districts_id, m_postal_code.m_cities_id, m_postal_code.m_provinces_id, m_postal_code.postal_code')
            ->leftJoin('customer.m_postal_code', 'm_postal_code.id', 'customer.m_postal_code_id')
            ->where('customer.id', $searchParams['id'])
            ->first();
        $arr_customer  =  [
            'id' => $customer->id,
            'name' => $customer->name,
            'npwp' => $customer->npwp,
            'nik' => $customer->nik,
            'phone' => $customer->phone,
            'address' => $customer->address,
            'm_subdistricts_id' => $customer->m_subdistricts_id,
            'm_districts_id' => $customer->m_districts_id,
            'm_cities_id' => $customer->m_cities_id,
            'm_provinces_id' => $customer->m_provinces_id,
            'postal_code' => $customer->postal_code,
        ];
        return new GeneralCollection($arr_customer);
    }
}
