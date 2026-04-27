<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('tenant:seed-global {class?}')]
#[Description('Run seeder global for tenant')]
class SeedGlobalTenantCommand extends Command
{
    public function handle(): int
    {
        $classInput = $this->argument('class');

        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->warn("⚠️ Belum ada tenant yang terdaftar.");
            return self::SUCCESS;
        }

        $this->info("=================================");
        $this->info("🌱 SEEDING GLOBAL...");
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
                $classPath = "Database\\Seeders\\Tenants\\{$classInput}";
                if (class_exists($classPath)) {
                    Artisan::call('db:seed', [
                        '--class' => $classPath,
                        '--force' => true,
                    ]);
                    $this->line("✅ Run: {$classInput}");
                } else {
                    $this->error("❌ Class {$classInput} tidak ditemukan!");
                }
            } else {
                $this->line("📝 Cara Pakai:");
                $this->line("php artisan tenant:seed-global NamaSeeder");
                $this->newLine();
                $this->info("📌 Contoh");
                $this->line("php artisan tenant:seed-global ProdukSeeder");
            }

            $this->line("---------------------------------");
        }

        $this->info("✅ ALL DONE");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
