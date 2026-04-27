<?php

namespace Database\Seeders\Tenants;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DomainTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data contoh
        $data = [
            [
                'name' => 'PT Maju Jaya',
                'domain' => 'majujaya.test',
                'data' => [
                    'db' => 'tenant_majujaya',
                    'db_path' => database_path('Tenants/tenant_majujaya.sqlite'),
                ],
            ],
            [
                'name' => 'CV Sukses Bersama',
                'domain' => 'sukses.test',
                'data' => [
                    'db' => 'tenant_sukses',
                    'db_path' => database_path('Tenants/tenant_sukses.sqlite'),
                ],
            ],
        ];

        foreach ($data as $item) {
            // Cek kalau domain belum ada, baru masukkan
            if (!Tenant::where('domain', $item['domain'])->exists()) {
                Tenant::create([
                    'id' => (string) Str::uuid(),
                    'name' => $item['name'],
                    'domain' => $item['domain'],
                    'data' => $item['data'],
                ]);

                $this->line("✅ {$item['name']} ({$item['domain']})");
            }
        }

        $this->info("✅ Seeder Tenant Selesai!");
    }
}
