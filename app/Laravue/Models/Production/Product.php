<?php

namespace App\Laravue\Models\Production;

use App\Laravue\Models\Production\Master\Categories;
use App\Laravue\Models\Production\Master\Supplier;
use App\Laravue\Models\Production\Master\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.product';
    protected $fillable = ['code', 'name', 'm_category_id', 'm_unit_id', 'length', 'height', 'width', 'weight', 'sales_price', 'unit_cost', 'type', 'variant', 'workstation_id', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by', 'm_supplier_id', 'total_workforce', 'total_timing'];

    public function data_detail()
    {
        return $this->hasOne('App\Models\Production\ProductDetail');
    }
    public function categories()
    {
        return $this->hasOne(Categories::class, 'id', 'm_category_id');
    }
    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'm_unit_id');
    }
    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'id', 'm_supplier_id');
    }
}
