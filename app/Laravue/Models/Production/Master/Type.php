<?php

namespace App\Laravue\Models\Production\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Type extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.m_type';
    protected $fillable = ['name', 'code', 'description', 'created_by', 'updated_by', 'deleted_by'];
}
