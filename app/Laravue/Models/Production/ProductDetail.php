<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.product_detail';
    protected $fillable = ['product_id', 'qty', 'material_id', 'index', 'timming', 'date', 'status', 'approved_by', 'approved_at'];

    public function data_product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
