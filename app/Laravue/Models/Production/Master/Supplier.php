<?php

namespace App\Laravue\Models\Production\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.m_supplier';
    protected $fillable = ['name', 'code', 'description', 'phone', 'address', 'created_by', 'updated_by', 'deleted_by'];
}
