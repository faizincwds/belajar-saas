<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

#[Signature('tenant:migrate {domain?} {--fresh}')]
#[Description('Run migration for specific tenant or all tenants with Module Support')]
class MigrateTenantCommand extends Command
{
    public function handle(): int
    {
        $domain = $this->argument('domain');

        if ($domain) {
            // 🎯 Jalankan untuk SATU Tenant saja
            $tenant = Tenant::where('domain', $domain)->first();

            if (!$tenant) {
                $this->error("❌ Tenant dengan domain {$domain} tidak ditemukan!");
                return self::FAILURE;
            }

            $this->info("=================================");
            $this->info("PROCESSING: {$tenant->name}");
            $this->info("=================================");

            $dbPath = $tenant->data['db_path'] ?? null;

            if (!$dbPath || !file_exists($dbPath)) {
                $this->error("❌ File database tidak ditemukan!");
                return self::FAILURE;
            }

            // 🔌 Set koneksi database
            config([
                'database.connections.tenant' => [
                    'driver' => 'sqlite',
                    'database' => $dbPath,
                    'prefix' => '',
                ],
            ]);

            $this->line("📂 Database: {$dbPath}");

            // 🔥 Jalankan Migration
            $this->runMigration();

            $this->info("✅ SELESAI untuk {$domain}");
            $this->line("---------------------------------");

        } else {
            // 🌐 Jalankan untuk SEMUA tenant
            $tenants = Tenant::all();

            if ($tenants->isEmpty()) {
                $this->warn("⚠️ Belum ada tenant yang terdaftar.");
                return self::SUCCESS;
            }

            $this->info("=================================");
            $this->info("🚀 MIGRATING ALL TENANTS...");
            $this->info("=================================");

            foreach ($tenants as $tenant) {
                $this->line("👉 {$tenant->name} ({$tenant->domain})");

                $dbPath = $tenant->data['db_path'] ?? null;

                if ($dbPath && file_exists($dbPath)) {
                    config([
                        'database.connections.tenant' => [
                            'driver' => 'sqlite',
                            'database' => $dbPath,
                            'prefix' => '',
                        ],
                    ]);

                    $this->runMigration();
                    $this->line("✅ Selesai");
                } else {
                    $this->error("❌ Database tidak ditemukan, dilewati.");
                }
                $this->line("---------------------------------");
            }

            $this->info("=================================");
            $this->info("✅ SEMUA TENANT BERHASIL DIMIGRATE");
            $this->info("=================================");
        }

        return self::SUCCESS;
    }

    /**
     * Method Internal untuk Jalankan Migration
     */
    protected function runMigration()
    {
        $optionFresh = $this->option('fresh');

        // 🔍 Cari semua path migration
        $paths = [];

        // 1. Path Utama (database/migrations/Tenants)
        $mainPath = database_path('migrations/Tenants');
        if (File::isDirectory($mainPath)) {
            $paths[] = $mainPath;
            $this->line("📂 Scanning: database/migrations/Tenants");
        }

        // 2. Path dari SEMUA MODULES (Perbaikan disini!)
        $modulesRoot = base_path('Modules'); // <-- Langsung path absolute

        if (File::isDirectory($modulesRoot)) {
            foreach (File::directories($modulesRoot) as $moduleFolder) {
                $moduleName = basename($moduleFolder);
                $tenantMigrationPath = "{$moduleFolder}/Database/Migrations/Tenants";
                
                if (File::isDirectory($tenantMigrationPath)) {
                    // ✅ Masukkan path lengkap langsung
                    $paths[] = $tenantMigrationPath;
                    $this->line("📂 Scanning: Modules/{$moduleName}/Database/Migrations/Tenants");
                }
            }
        }

        // 🚀 Eksekusi migration jika ada path
        if (!empty($paths)) {
            foreach ($paths as $path) {
                if ($optionFresh) {
                    Artisan::call('migrate:fresh', [
                        '--database' => 'tenant',
                        '--path' => $path,
                        '--force' => true,
                    ]);
                } else {
                    Artisan::call('migrate', [
                        '--database' => 'tenant',
                        '--path' => $path,
                        '--force' => true,
                    ]);
                }
            }
        } else {
            $this->line("⚠️  Tidak ada file migration ditemukan.");
        }
    }
}
