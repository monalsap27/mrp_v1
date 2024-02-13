<?php

namespace App\Laravue\Models\Purchasing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model, SoftDeletes};


class Submit extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'purchasing.submit';
    protected $fillable = ['product_id', 'm_supplier_id', 'qty', 'status', 'description', 'note', 'approved_by', 'created_by', 'updated_by', 'deleted_by', 'approved_at'];
}
