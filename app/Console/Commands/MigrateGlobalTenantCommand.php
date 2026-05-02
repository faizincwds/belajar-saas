<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

#[Signature('tenant:migrate-global {namatabel?} {--fresh}')]
#[Description('Run migration global for tenant')]
class MigrateGlobalTenantCommand extends Command
{
    public function handle(): int
    {
        $namaTabel = $this->argument('namatabel');

        // 🔍 Cari semua tenant di DB tenant model
        $tenants = Tenant::all();
        if ($tenants->isEmpty()) {
            $this->warn("⚠️ Belum ada tenant yang terdaftar.");
            return self::SUCCESS;
        }

        $this->info("=================================");
        $this->info("🚀 MIGRATING GLOBAL...");
        $this->info("=================================");

        // 📂 Path & Cek Folder
        $path = database_path('migrations/Tenants');
        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0777, true);
            $this->line("📂 Created: database/migrations/Tenants");
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
