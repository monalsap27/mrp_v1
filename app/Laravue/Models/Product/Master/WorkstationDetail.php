<?php

namespace App\Laravue\Models\Product\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkstationDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.workstation_detail';
    protected $fillable = ['workstation_id', 'product_id', 'qty', 'created_by', 'updated_by', 'deleted_by'];
}
