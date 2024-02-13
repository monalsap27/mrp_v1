<?php

namespace App\Laravue\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{SoftDeletes, Model};

class Setting extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'purchasing.setting';
    protected $fillable = ['m_supplier_id', 'product_id', 'm_unit_id', 'unit_price'];
}
