<?php

namespace App\Laravue\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'purchasing.order';
    protected $fillable = ['code', 'm_supplier_id', 'status', 'total_amount', 'delivery_order_id', 'description', 'note', 'approved_by', 'created_by', 'updated_by', 'deleted_by', 'approved_at'];
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
