<?php

namespace App\Laravue\Models\Product\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.m_material';
    protected $fillable = ['name', 'code', 'description', 'created_by', 'updated_by', 'deleted_by'];
}
