<?php

namespace App\Laravue\Models\Product\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Workstation extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.workstation';
    protected $fillable = ['name', 'code', 'description', 'code', 'timing', 'created_by', 'updated_by', 'deleted_by'];
}
