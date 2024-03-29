<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufactureMaterial extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.manufacture_material';
    protected $fillable = ['manufacture_order_detail_id', 'control_id', 'created_by', 'updated_by', 'deleted_by'];

    public function data_stock_out()
    {
        return $this->belongsTo(StockOut::class, 'stock_out_id');
    }
    public function stock_out(): HasMany
    {
        return $this->hasMany(StockOut::class);
    }
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
