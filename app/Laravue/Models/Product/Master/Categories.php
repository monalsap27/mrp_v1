<?php

namespace App\Laravue\Models\Product\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categories extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.m_category';
    protected $fillable = ['name', 'code', 'description', 'created_by', 'updated_by', 'deleted_by'];
}
