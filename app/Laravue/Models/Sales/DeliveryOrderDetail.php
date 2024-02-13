<?php

namespace App\Laravue\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class DeliveryOrderDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'sales.delivery_order_detail';
    protected $fillable = ['delivery_order_id', 'sales_id', 'product_id', 'qty'];
    public function data_delivery()
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }
}
