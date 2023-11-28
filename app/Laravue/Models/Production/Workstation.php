<?php

namespace App\Laravue\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workstation extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'product.workstation';
    protected $fillable = ['name', 'description', 'code', 'timing', 'workforce', 'change_material', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'updated_by', 'deleted_by'];

    public function data_detail()
    {
        return $this->hasOne('App\Models\Production\WorkstationDetail');
    }
    public function Detail()
    {
        return $this->hasMany(WorkstationDetail::class, 'workstation_id', 'id');
    }
    public function delete(): ?bool
    {
        $this->data_detail()->delete();

        return parent::delete();
    }
}
