<?php

namespace App\Http\Controllers\Api\Purchasing\Master;

use Auth;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Purchasing\Setting;
use App\Http\Resources\Production\GeneralMasterResource;

class SettingController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $settingQuery = Setting::query()
            ->selectRaw('product.name as product_name, product.code as product_code, m_supplier.name as supplier_name, m_unit.name as unit_name, unit_price, setting.id, product_id as product, setting.m_supplier_id as supplier, setting.m_unit_id as unit')
            ->leftjoin("product.product", "product.id", "setting.product_id")
            ->leftjoin("product.m_supplier", "m_supplier.id", "setting.m_supplier_id")
            ->leftjoin("product.m_unit", "m_unit.id", "setting.m_unit_id");
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $settingQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $settingQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $settingQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $settingQuery->orderBy('id', 'desc');
        } else {
            $settingQuery->orderBy('id', 'asc');
        }
        return new GeneralCollection($settingQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        $params     = $request->all();
        if (empty($params['id'])) {
            $setting = Setting::create([
                'm_supplier_id' => $params['supplier'],
                'product_id'    => $params['product'],
                'm_unit_id'     => $params['unit'],
                'unit_price'    => $params['unit_price'],
                'created_by'    => Auth::user()->id
            ]);
        } else {
            $setting = Setting::find($params['id']);
            $setting->m_supplier_id = $params['supplier'];
            $setting->product_id    = $params['product'];
            $setting->m_unit_id     = $params['unit'];
            $setting->unit_price    = $params['unit_price'];
            $setting->updated_by    = Auth::user()->id;
            $setting->save();
        }
        return new GeneralMasterResource($setting);
    }
    public function destroy(Request $request)
    {
        try {
            $setting = Setting::find($request->id);
            $setting->deleted_by = Auth::user()->id;
            $setting->deleted_at = new DateTime();
            $setting->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }

    public function fetchProductSettingBySupplier(Request $request)
    {
        $searchParams = $request->all();
        $settingQuery = Setting::query()->selectRaw("product.name as name, product.id, setting.m_unit_id, setting.unit_price, product.code, m_unit.name as unit_name,(CASE WHEN data_stock_product.safety_stock-data_stock_product.qty_stock <= 0 THEN 0 ELSE data_stock_product.safety_stock-data_stock_product.qty_stock END) as recomend_qty")
            ->leftJoin('product.product', 'setting.product_id', 'product.id')
            ->leftJoin('product.m_unit', 'setting.m_unit_id', 'm_unit.id')
            ->leftJoin('product.data_stock_product', 'product.id', 'data_stock_product.id')
            ->where('purchasing.setting.m_supplier_id', $searchParams['id']);
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        return new GeneralCollection($settingQuery->paginate($limit));
    }
}
