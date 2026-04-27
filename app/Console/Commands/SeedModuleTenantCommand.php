<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

#[Signature('module:seed-mod {module} {class?}')]
#[Description('Run seeder tenant from specific module')]
class SeedModuleTenantCommand extends Command
{
    public function handle(): int
    {
        $moduleName = $this->argument('module');
        $classInput = $this->argument('class');

        // Cek Module
        if (!File::isDirectory(base_path("Modules/{$moduleName}"))) {
            $this->error("❌ Module {$moduleName} tidak ditemukan!");
            return self::FAILURE;
        }

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->warn("⚠️ Belum ada tenant yang terdaftar.");
            return self::SUCCESS;
        }

        $this->info("=================================");
        $this->info("🌱 SEEDING MODULE: {$moduleName}");
        $this->info("=================================");

        foreach ($tenants as $t) {
            $this->line("👉 {$t->name} ({$t->domain})");

            // 🔌 Set koneksi
            config([
                'database.connections.tenant' => [
                    'driver' => 'sqlite',
                    'database' => $t->data['db_path'],
                    'prefix' => '',
                ]
            ]);

            if ($classInput) {
                $classPath = "Modules\\{$moduleName}\\Database\\Seeders\\Tenants\\{$classInput}";
                if (class_exists($classPath)) {
                    Artisan::call('db:seed', [
                        '--class' => $classPath,
                        '--force' => true,
                    ]);
                    $this->line("✅ Run: {$classInput}");
                } else {
                    $this->error("❌ Class {$classInput} tidak ditemukan di Module!");
                }
            } else {
                $this->line("📝 Cara Pakai:");
                $this->line("php artisan module:seed-mod {$moduleName} NamaSeeder");
                $this->newLine();
                $this->info("📌 Contoh");
                $this->line("php artisan module:seed-mod {$moduleName} ProdukSeeder");
            }

            $this->line("---------------------------------");
        }

        $this->info("✅ ALL DONE");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
