<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

#[Signature('module:migrate-mod {module} {--fresh}')]
#[Description('Run migration tenant from specific module')]
class MigrateModuleTenantCommand extends Command
{
    public function handle(): int
    {
        $moduleName = $this->argument('module');

        // 📂 Cek Module
        $modulePath = base_path("Modules/{$moduleName}");
        if (!File::isDirectory($modulePath)) {
            $this->error("❌ Module {$moduleName} tidak ditemukan!");
            return self::FAILURE;
        }

        // 📂 Path Migration
        $path = "{$modulePath}/Database/Migrations/Tenants";
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true);
        }

        $this->info("=================================");
        $this->info("🚀 MIGRATING MODULE: {$moduleName}");
        $this->line("📂 Path   : {$path}");
        
        // 🔥 TAMPILKAN NAMA FILE
        $files = File::files($path);
        if (count($files) > 0) {
            $this->line("📄 Files  :");
            foreach ($files as $file) {
                $this->line("   - " . basename($file));
            }
        } else {
            $this->line("📄 Files  : (Kosong)");
        }
        $this->info("=================================");

        // 🔍 Cari semua tenant
        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->warn("⚠️ Belum ada tenant yang terdaftar.");
            return self::SUCCESS;
        }

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

            // 🔥 Jalankan Migrate
            if ($this->option('fresh')) {
                Artisan::call('migrate:fresh', [
                    '--path' => $path,
                    '--force' => true,
                ]);
            } else {
                Artisan::call('migrate', [
                    '--path' => $path,
                    '--force' => true,
                ]);
            }

            $this->line("✅ Selesai");
            $this->line("---------------------------------");
        }

        $this->info("=================================");
        $this->info("✅ ALL DONE");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
