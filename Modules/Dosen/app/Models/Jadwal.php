<?php

namespace Modules\Dosen\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['jam'])]
class Jadwal extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'jadwals';
}