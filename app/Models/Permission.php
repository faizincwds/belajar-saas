<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as BasePermission;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Permission extends BasePermission
{
    use HasUuids, BelongsToTenant; // PENTING!

    //
}
