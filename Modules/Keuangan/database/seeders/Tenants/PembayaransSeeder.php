<?php

namespace Modules\Keuangan\Database\Seeders\Tenants;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaransSeeder extends Seeder
{
    public function run()
    {
        // Sample Data
        $data = [];
        for($i=1; $i<=3; $i++){
            $item = [];
            foreach(['name', 'hp', 'nota'] as $col){
                $item[$col] = ucfirst($col) . " " . $i;
            }
            $item['id'] = (string) Str::uuid();
            $data[] = $item;
        }

        DB::table('pembayarans')->insert($data);
        $this->command->info('✅ Sample data inserted to pembayarans');
    }
}