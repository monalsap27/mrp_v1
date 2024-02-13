<?php

namespace App\Laravue\Models\Vendor;

use App\Laravue\Models\Purchasing\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class Transaction extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'vendor.transaction';
    protected $fillable = ['nomor', 'status', 'note', 'total_amount', 'total_amount_request', 'order_id'];

    public function trans()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
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
