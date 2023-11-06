<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkstationDetail extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.workstation_detail';
    protected $fillable = ['workstation_id', 'product_id', 'qty', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public function data_workstation()
    {
        return $this->belongsTo(Workstation::class, 'workstation_id');
    }
    public function Detail()
    {
        return $this->hasOne('App\Models\Production\Workstation', 'id', 'workstation_id');
    }
}
