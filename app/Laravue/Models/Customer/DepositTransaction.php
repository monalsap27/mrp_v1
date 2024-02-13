<?php

namespace App\Laravue\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DepositTransaction extends Model
{
    use SoftDeletes;
    use HasFactory;
    protected $table = 'customer.deposit_transaction';
    protected $fillable = ['deposit_id', 'amount', 'type', 'note', 'bank_id', 'created_by', 'updated_by', 'deleted_by'];

    public function data_deposit()
    {
        return $this->belongsTo(Deposit::class, 'deposit_id');
    }
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
