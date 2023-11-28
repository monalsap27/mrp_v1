<?php

namespace App\Http\Controllers\Api\Production;

use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Http\Resources\Production\GeneralMasterResource;
use App\Laravue\Models\Production\{Product, ProductDetail};
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $productQuery = Product::query()
            ->leftJoin("product.m_category as b", "product.m_category_id", "b.id")
            ->leftJoin("product.m_unit as c", "product.m_unit_id", "c.id")
            ->select('product.id', 'product.name', 'product.code', 'sales_price', 'b.name as category_name', 'sales_price', 'unit_cost', 'product.status', 'c.name as unit_name', 'safety_stock');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $productQuery->where('product.type', $searchParams['type']);
        if (!empty($keyword)) {
            $productQuery->where('product.name', 'LIKE', '%' . $keyword . '%');
            $productQuery->orWhere('product.code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $productQuery->orderBy('product.id', 'desc');
        } else {
            $productQuery->orderBy('product.id', 'asc');
        }
        return new GeneralCollection($productQuery->paginate($limit));
    }
    public function dataApproval(Request $request)
    {
        $searchParams = $request->all();
        $productQuery = Product::query()
            ->leftJoin("product.m_category as b", "product.m_category_id", "b.id")
            ->leftJoin("product.m_unit as c", "product.m_unit_id", "c.id")
            ->select('product.id', 'product.name', 'product.code', 'sales_price', 'b.name as category_name', 'sales_price', 'unit_cost', 'product.status', 'c.name as unit_name')
            ->where('type', '1')
            ->orderBy('product.status', 'asc')
            ->orderBy('product.created_at', 'desc');
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $productQuery->where('product.name', 'LIKE', '%' . $keyword . '%');
            $productQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $productQuery->orWhere('product.code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $productQuery->orderBy('product.id', 'desc');
        } else {
            $productQuery->orderBy('product.id', 'asc');
        }
        return new GeneralCollection($productQuery->paginate($limit));
    }
    public function comboData(Request $request)
    {
        $searchParams = $request->all();
        $productQuery = Product::query();
        $sort = Arr::get($searchParams, 'sort', '');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $productQuery->where('name', 'LIKE', '%' . $keyword . '%');
            $productQuery->orWhere('description', 'LIKE', '%' . $keyword . '%');
            $productQuery->orWhere('code', 'LIKE', '%' . $keyword . '%');
        }
        if ($sort == '-id') {
            $productQuery->orderBy('id', 'desc');
        } else {
            $productQuery->orderBy('id', 'asc');
        }
        return GeneralMasterResource::collection($productQuery->paginate($limit));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $params     = $request->all();
            if (empty($params['id'])) {
                $product = new Product();
                $product->name = $params['name'];
                $product->code = $params['code'];
                $product->unit_cost = empty($params['unit_cost']) ? null : $params['unit_cost'];
                $product->length = $params['length'];
                $product->width = $params['width'];
                $product->height = $params['height'];
                $product->m_unit_id = $params['unit'];
                $product->m_supplier_id = !empty($params['supplier']) ? $params['supplier'] : '0';
                $product->type = $params['type'];
                $product->m_category_id = $params['categories'];
                $product->weight = $params['weight'];
                $product->total_workforce = $params['total_workforce'];
                $product->total_timing = $params['total_timing'];
                if ($params['type'] == 1) {
                    $product->status = '1';
                    $product->save();
                    $jml_detail = count($request->items);
                    $index      = 1;
                    for ($x = 0; $x < $jml_detail; $x++) {
                        $product_detail         = new ProductDetail();
                        $product_detail->data_product()->associate($product);
                        $product_detail->workstation_id = is_array($request->items[$x]['workstation_id']) ? $request->items[$x]['workstation_id']['id'] : $request->items[$x]['workstation_id'];
                        $product_detail->timming = $request->items[$x]['timing'];
                        $product_detail->qty = $request->items[$x]['qty'];
                        $product_detail->material_id = $request->items[$x]['material_id'];
                        $product_detail->index = $index;
                        $product_detail->save();
                        $index++;
                    }
                }
                $product->save();
                DB::commit();
                return response()->json(['message' => 'has been created successfully !'], 200);
            } else {
                $product = Product::find($params['id']);
                $product->name = $params['name'];
                $product->code = $params['code'];
                $product->unit_cost = empty($params['unit_cost']) ? null : $params['unit_cost'];
                $product->length = $params['length'];
                $product->width = $params['width'];
                $product->height = $params['height'];
                $product->m_unit_id = $params['unit'];
                $product->m_supplier_id = !empty($params['supplier']) ? $params['supplier'] : '0';
                $product->type = $params['type'];
                $product->m_category_id = $params['categories'];
                $product->weight = $params['weight'];
                $product->total_workforce = $params['type'] == 1 ? $params['total_workforce'] : 0;
                $product->total_timing = $params['type'] == 1 ? $params['total_timing'] : 0;
                if ($product->save()) {
                    $jml_detail = count($request->items);
                    $index      = 1;
                    for ($x = 0; $x < $jml_detail; $x++) {
                        if (empty($request->items[$x]['id'])) {
                            $product_detail = new ProductDetail();
                            $product_detail->data_product()->associate($product);
                            $product_detail->workstation_id = is_array($request->items[$x]['workstation_id']) ? $request->items[$x]['workstation_id']['id'] : $request->items[$x]['workstation_id'];
                            $product_detail->timming = $request->items[$x]['timing'];
                            $product_detail->qty = $request->items[$x]['qty'];
                            $product_detail->index = $index;
                            $product_detail->material_id = $request->items[$x]['material_id'];
                            $product_detail->save();
                        } else {
                            $product_detail = ProductDetail::find($request->items[$x]['id']);
                            $product_detail->data_product()->associate($product);
                            $product_detail->workstation_id = $request->items[$x]['workstation_id'];
                            $product_detail->timming = $request->items[$x]['timing'];
                            $product_detail->qty = $request->items[$x]['qty'];
                            $product_detail->index = $index;
                            $product_detail->material_id = $request->items[$x]['material_id'];
                            $product_detail->save();
                        }
                    }
                    $index++;
                }
                DB::commit();
                return response()->json(['message' => 'has been updated successfully !'], 200);
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function approve(Request $request)
    {
        try {
            $product = Product::find($request->id);
            $product->status = $request->status;
            $product->note = $request->status == 2 ? '' : $request->note;
            $product->approved_by = auth()->user()->id;
            $product->approved_at = new DateTime();
            $product->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(200);
    }
    public function show(Request $request)
    {
        $product = Product::where('id', $request->id)
            ->with('categories', 'unit', 'supplier')
            ->select('product.id', 'product.name', 'product.description', 'product.code', 'product.total_workforce', 'product.length', 'product.height', 'product.width', 'product.weight', 'product.weight', 'product.unit_cost', 'product.unit_cost', 'm_category_id', 'type', 'variant', 'm_supplier_id', 'm_unit_id', 'total_workforce', 'total_timing', 'product.status', 'product.note')
            ->first();
        $arr_product  =  [
            'id' => $product->id,
            'product_name' => $product->name,
            'description' => $product->description,
            'code' => $product->code,
            'total_workforce' => $product->total_workforce,
            'length_product' => $product->length,
            'height' => $product->height,
            'width' => $product->width,
            'weight' => $product->weight,
            'unit_cost' => $product->unit_cost,
            'type' => $product->type,
            'type_name' => $product->type = 1 ? 'I make this product' : 'I buy this product',
            'm_category_id' => $product->m_category_id,
            'm_unit_id' => $product->m_unit_id,
            'm_supplier_id' => $product->m_supplier_id,
            'total_workforce' => $product->total_workforce,\
            'total_timing' => $product->total_timing,
            'category_name' => $product['categories']->name,
            'unit_name' => $product['unit']->name,
            'supplier_name' => $product['supplier']->name,
            'status'    => $product->status,
            'note'      => $product->note,
            'total_timing' => WorkstationController::hour($product->total_timing),
        ];
        return new GeneralCollection($arr_product);
    }
    public function showDetail(Request $request)
    {
        $workstation_detail  = ProductDetail::select('product_detail.id', 'product_detail.workstation_id', 'product_detail.timming as timing', 'product_detail.material_id', 'product_detail.qty', 'product_detail.product_id', 'workstation.name as workstation_name', 'workstation.description', 'c.name as material', 'workstation.total_workforce', 'workstation.id as workstation_id')
            ->where('product_detail.product_id', $request->id)
            ->leftJoin('product.workstation', 'product_detail.workstation_id', 'workstation.id')
            ->leftJoin('product.product as c', 'product_detail.material_id', 'c.id')
            ->get();

        $arr_detail = array();
        foreach ($workstation_detail as $workstation_detail) {
            $arr_detail[] = array(
                'id' => $workstation_detail->id,
                'product_id' => $workstation_detail->product_id,
                'qty' => $workstation_detail->qty,
                'workstation_name' => $workstation_detail->workstation_name,
                'description' => $workstation_detail->description,
                'material' => $workstation_detail->material,
                'total_workforce' => $workstation_detail->total_workforce,
                'timing' => WorkstationController::hour($workstation_detail->timing),
            );
        }
        return new GeneralCollection($arr_detail);
    }
    public function destroy(Request $request)
    {
        try {
            $product = Product::findOrFail($request->id);
            $product->deleted_by = auth()->user()->id;
            $product->save();
            $product->delete();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(["message" => " has been deleted successfully"], 200);
    }
    public function dataShowBillOf(Request $request)
    {
        $searchParams = $request->all();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        $data_bill = ProductDetail::query()
            ->select("c.name", "c.code", DB::RAW("sum(qty) as qty"))
            ->where('product_detail.product_id', $searchParams['id'])
            ->leftJoin('product.product as c', 'product_detail.material_id', 'c.id')
            ->groupBy("c.name", "c.code")
            ->orderBy("c.name", "asc");
        return new GeneralCollection($data_bill->paginate($limit));
    }
    public function updateSafetyStock(Request $request)
    {
        try {
            $product = Product::find($request->product_id);
            $product->safety_stock = $request->safety_stock;
            $product->save();
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(200);
    }
}
