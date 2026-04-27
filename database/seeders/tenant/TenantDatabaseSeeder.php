<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TenantDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            'id' => (string) Str::uuid(),
            'name' => 'Admin Kampus',
            'email' => 'admin@kampus.local',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
