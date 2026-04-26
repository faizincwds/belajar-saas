<?php

namespace App\Models;

use Spatie\Permission\Models\Role as BaseRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Role extends BaseRole
{
    use HasUuids, BelongsToTenant; // PENTING!
    
    //
}

