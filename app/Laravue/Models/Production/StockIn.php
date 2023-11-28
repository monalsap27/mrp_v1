<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockIn extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.stock_in';
    protected $fillable = ['product_id', 'control_id', 'harga_beli', 'harga_jual', 'description', 'created_by', 'updated_by', 'deleted_by'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
        static::saving(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
        });
    }
}
