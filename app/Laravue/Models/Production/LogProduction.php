<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LogProduction extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.log_production';
    protected $fillable = ['manufacture_order_id', 'description'];
}
