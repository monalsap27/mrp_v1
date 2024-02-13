<?php

namespace App\Http\Controllers\Api\Sales\Master;

use Auth;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Sales\Master\Price;

class PriceController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $citiesQuery = Price::query()
            ->selectRaw('m_price.id, product_id, product.name as product_name, product.code as product_code, price, m_price.status, m_price.created_at')
            ->leftJoin('product.product', 'product.id', 'm_price.product_id');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $citiesQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $citiesQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        $citiesQuery->orderBy('id', 'desc');
        return new GeneralCollection($citiesQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $params     = $request->all();
            if (empty($params['id'])) {
                Price::where('product_id', $params['product'])->update(['status' => '0']);
                $price = Price::create([
                    'product_id'     => $params['product'],
                    'price'          => $params['price'],
                    'status'         => '1',
                    'created_by'     => Auth::user()->id
                ]);
                if ($price) {
                    DB::commit();
                    return response()->json(['message' => 'has been updated successfully !'], 200);
                }
            } else {
                $price = Price::find($params['id']);
                $price->product_id      = $params['product'];
                $price->price           = $params['price'];
                $price->updated_by     = Auth::user()->id;
                $price->save();
                if ($price->save()) {
                    DB::commit();
                    return response()->json(['message' => 'has been updated successfully !'], 200);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function showByProduct(Request $request)
    {
        $searchParams = $request->all();
        $price = Price::select('id', 'price')
            ->where('m_price.product_id', $searchParams['id'])
            ->first();
        $arr_price  =  [
            'id' => empty($price->id) ? '0' : $price->id,
            'price' => empty($price->price) ? '0' : $price->price,
        ];
        return new GeneralCollection($arr_price);
    }
}
