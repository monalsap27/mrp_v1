<?php

namespace App\Http\Controllers\Api\Sales\Master;

use Auth;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralCollection;
use App\Laravue\Models\Sales\Master\Tenor;
use DateTime;

class TenorController extends Controller
{
    const ITEM_PER_PAGE = 15;
    public function data(Request $request)
    {
        $searchParams = $request->all();
        $citiesQuery = Tenor::query()
            ->selectRaw('m_tenor.id, note, status, created_at, tenor');
        $keyword = Arr::get($searchParams, 'keyword', '');
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        if (!empty($keyword)) {
            $citiesQuery->where('tenor', 'LIKE', '%' . $keyword . '%');
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
                $tenor = Tenor::create([
                    'tenor'          => $params['tenor'],
                    'note'           => $params['note'],
                    'status'         => '1',
                    'created_by'     => Auth::user()->id
                ]);
                if ($tenor) {
                    DB::commit();
                    return response()->json(['message' => 'has been updated successfully !'], 200);
                }
            } else {
                $price = Tenor::find($params['id']);
                $price->tenor          = $params['tenor'];
                $price->note           = $params['note'];
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
    public function destroy(Request $request)
    {
        try {
            $tenor = Tenor::find($request->id);
            $tenor->deleted_by = Auth::user()->id;
            $tenor->deleted_at = new DateTime();
            $tenor->save();
        } catch (\Exception $ex) {
            \DB::rollBack();
            return response()->json(['error' => $ex->getMessage()], 403);
        }
        return response()->json(null, 204);
    }
}
