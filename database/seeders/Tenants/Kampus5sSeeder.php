<?php

namespace Database\Seeders\Tenants;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Kampus5sSeeder extends Seeder
{
    public function run()
    {
        // Sample Data
        $data = [];
        for($i=1; $i<=3; $i++){
            $item = [];
            foreach(['id', 'name', 'domain'] as $col){
                $item[$col] = ucfirst($col) . " " . $i;
            }
            $item['id'] = (string) Str::uuid();
            $data[] = $item;
        }

        DB::table('kampus5s')->insert($data);
        $this->command->info('✅ Sample data inserted to kampus5s');
    }
}