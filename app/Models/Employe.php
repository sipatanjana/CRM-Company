<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Employe extends Model
{
    use SoftDeletes;

    public const POSITION = ['manager' => 'manager', 'employe' => 'employe'];

    protected static function booted()
    {
        static::deleting(function (Employe $data) {
            $data->User()->delete();
        });
    }

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'address',
        'position',
        'phone_number',
    ];

    public function scopeSearch($query, $term = null)
    {
        return $query->where(function ($query) use ($term) {
            $query->where(DB::raw('LOWER(name)'), 'like', "%{$term}%");
        });
    }

    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function Company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }
}
