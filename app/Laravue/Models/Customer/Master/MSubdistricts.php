<?php

namespace App\Laravue\Models\Customer\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MSubdistricts extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'customer.m_subdistricts';
    protected $fillable = ['name', 'code', 'm_districts_id', 'created_by', 'updated_by', 'deleted_by'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->isDirty('created_by')) {
                $model->created_by = auth()->user()->id;
            }
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
        static::saving(function ($model) {
            if (!$model->isDirty('updated_by')) {
                $model->updated_by = auth()->user()->id;
            }
        });
        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
        });
    }
}
