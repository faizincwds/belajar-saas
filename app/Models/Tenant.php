<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Tenant extends Model
{
    use HasUuids, BelongsToTenant;

    protected $fillable = [
        'id',
        'name',
        'domain',
        'data',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'data' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($tenant) {
            if (empty($tenant->id)) {
                $tenant->id = (string) Str::uuid();
            }
        });
    }
}
