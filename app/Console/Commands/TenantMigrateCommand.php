<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('tenant:migrate {--fresh}')]
#[Description('Run migration for all tenants')]
class TenantMigrateCommand extends Command
{
    public function handle(): int
    {
        $this->info("=================================");
        $this->info("MIGRATING ALL TENANTS...");
        $this->info("=================================");

        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn("Tidak ada tenant.");
            return self::SUCCESS;
        }

        foreach ($tenants as $tenant) {
            $this->line("👉 Tenant: {$tenant->name}");

            // 🔥 Validasi data tenant
            if (!isset($tenant->data['db_path'])) {
                $this->error("DB path tidak ditemukan untuk {$tenant->name}");
                continue;
            }

            // 🔥 Set koneksi tenant
            config([
                'database.connections.tenant' => [
                    'driver' => 'sqlite',
                    'database' => $tenant->data['db_path'],
                    'prefix' => '',
                ],
                'database.default' => 'tenant',
            ]);

            // 🔥 Jalankan migration
            if ($this->option('fresh')) {
                $this->warn("⚠️ migrate:fresh: {$tenant->name}");

                Artisan::call('migrate:fresh', [
                    '--database' => 'tenant',
                    '--path' => '/database/migrations/tenant',
                    '--force' => true,
                ]);
            } else {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => '/database/migrations/tenant',
                    '--force' => true,
                ]);
            }

            $this->info("✔️ Selesai: {$tenant->name}");
            $this->line("---------------------------------");
        }

        $this->info("=================================");
        $this->info("SEMUA TENANT BERHASIL DIMIGRATE");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
