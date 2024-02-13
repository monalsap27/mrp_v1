<?php

namespace App\Laravue\Models\Vendor;

use App\Laravue\Models\Vendor\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class TransactionDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'vendor.transaction_detail';
    protected $fillable = ['transaction_id', 'product_id', 'qty', 'qty_request', 'total_price', 'total_price_request', 'unit_price', 'unit_price_request'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
        });
        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
        });
    }
}
