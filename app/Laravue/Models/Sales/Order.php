<?php

namespace App\Laravue\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class Order extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'sales.order';
    protected $fillable = ['code', 'customer_id', 'category', 'status'];
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
