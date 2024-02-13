<?php

namespace App\Laravue\Models\Vendor;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class DeliveryOrderDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'sales.delivery_order_detail';
    protected $fillable = ['delivery_order_id', 'transaction_id', 'product_id', 'qty'];
    public function data_delivery()
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }
}
