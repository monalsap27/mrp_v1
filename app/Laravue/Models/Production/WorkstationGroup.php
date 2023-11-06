<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkstationGroup extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.workstation_group';
    protected $fillable = ['name', 'arr_workstation_id', 'total_workforce', 'code', 'description', 'total_timing', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];
}
