<?php

namespace Database\Seeders\Tenants;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Kampus4sSeeder extends Seeder
{
    public function run()
    {
        // Sample Data (Default Only ID)
        $data = [];
        for($i=1; $i<=3; $i++){
            $data[] = [
                'id' => (string) Str::uuid(),
            ];
        }

        DB::table('kampus4s')->insert($data);
        $this->command->info('✅ Sample ID inserted to kampus4s');
    }
}