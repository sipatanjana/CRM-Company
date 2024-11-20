<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        static::deleting(function (Company $data) {
            $data->Employes()->delete();
        });
    }

    protected $fillable = [
        'name',
        'email',
        'phone_number',
    ];

    public function Employes(): HasMany
    {
        return $this->hasMany(Employe::class, 'company_id', 'id');
    }
}
