<?php

namespace Database\Seeders\Tenants;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Kampus3sSeeder extends Seeder
{
    public function run()
    {
        // Sample Data
        $data = [];
        for($i=1; $i<=3; $i++){
            $item = [];
            foreach(['name', 'hp', 'logo'] as $col){
                $item[$col] = ucfirst($col) . " " . $i;
            }
            $item['id'] = (string) Str::uuid();
            $data[] = $item;
        }

        DB::table('kampus3s')->insert($data);
        $this->command->info('✅ Sample data inserted to kampus3s');
    }
}