<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

#[Signature('tenant:seed {domain?} {class?}')]
#[Description('Run specific seeder class for tenant')]
class SeedTenantCommand extends Command
{
    public function handle(): int
    {
        $domain = $this->argument('domain');
        $classInput = $this->argument('class');

        $tenant = Tenant::where('domain', $domain)->first();

        if (!$tenant) {
            $this->error("❌ Tenant dengan domain {$domain} tidak ditemukan!");
            return self::FAILURE;
        }

        $dbPath = $tenant->data['db_path'];

        if (!file_exists($dbPath)) {
            $this->error("❌ Database tidak ditemukan di: {$dbPath}");
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

        $this->info("=================================");
        $this->info("SEEDING: {$tenant->name}");
        $this->info("=================================");
        $this->line("📂 Database: {$dbPath}");

        if ($classInput) {
            // ✅ JALANIN CLASS YANG DIMAKSUD
            $className = $classInput;
            $this->line("⚡ Running: {$className}");

            // 1. Cek di Database utama
            $classPath = "Database\\Seeders\\Tenants\\{$className}";
            if (class_exists($classPath)) {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--class' => $classPath,
                    '--force' => true,
                ]);
            }

            // 2. Cek di Modules
            $modulesRoot = base_path('Modules');
            if (File::isDirectory($modulesRoot)) {
                foreach (File::directories($modulesRoot) as $moduleFolder) {
                    $moduleName = basename($moduleFolder);
                    $classPathModule = "Modules\\{$moduleName}\\Database\\Seeders\\Tenants\\{$className}";
                    
                    if (class_exists($classPathModule)) {
                        Artisan::call('db:seed', [
                            '--database' => 'tenant',
                            '--class' => $classPathModule,
                            '--force' => true,
                        ]);
                    }
                }
            }

            $this->info("✅ SELESAI!");
        } else {
            // 📝 TAMPILIN PANDUAN
            $this->newLine();
            $this->info("📝 CARA PAKAI");
            $this->line("php artisan tenant:seed {$domain} NamaSeeder");

            $this->newLine();
            $this->info("📌 CONTOH");
            $this->line("php artisan tenant:seed {$domain} ProdukSeeder");
            $this->line("php artisan tenant:seed {$domain} UserSeeder");
            $this->line("php artisan tenant:seed {$domain} DomainTenantSeeder");
        }

        $this->info("=================================");

        return self::SUCCESS;
    }
}
