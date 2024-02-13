<?php

namespace App\Laravue\Models\Sales;

use App\Laravue\Models\Sales\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class OrderDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'sales.order_detail';
    protected $fillable = ['order_id', 'product_id', 'qty', 'unit_price', 'discount', 'total_price', 'qty_packed'];

    public function data_order()
    {
        return $this->belongsTo(Order::class, 'order_id');
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
