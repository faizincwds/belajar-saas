<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

#[Signature('tenant:create {name} {domain}')]
#[Description('Create new tenant with database')]
class CreateTenantCommand extends Command
{
    public function handle(): int
    {
        $name = $this->argument('name');
        $domain = $this->argument('domain');

        // 🔒 Validasi domain
        if (Tenant::where('domain', $domain)->exists()) {
            $this->error('Domain sudah digunakan!');
            return self::FAILURE;
        }

        // 🔥 Generate DB name
        $dbName = 'tenant_' . Str::slug($name);

        // 🔥 Path SQLite
        $dbPath = database_path("tenants/{$dbName}.sqlite");

        // 🔥 Buat folder jika belum ada
        if (!file_exists(database_path('tenants'))) {
            mkdir(database_path('tenants'), 0777, true);
        }

        // 🔥 Buat file database
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }

        // 🔥 Simpan tenant
        $tenant = Tenant::create([
            'id' => (string) Str::uuid(),
            'name' => $name,
            'domain' => $domain,
            'data' => [
                'db' => $dbName,
                'db_path' => $dbPath,
            ],
        ]);

        // 🔥 Set koneksi sementara
        config([
            'database.connections.tenant' => [
                'driver' => 'sqlite',
                'database' => $dbPath,
                'prefix' => '',
            ],
        ]);

        // 🔥 Jalankan migration ke DB tenant
        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--path' => '/database/migrations/tenant', // penting
            '--force' => true,
        ]);

        // ✅ Output
        $this->info("=================================");
        $this->info("Tenant berhasil dibuat!");
        $this->line("Nama     : {$tenant->name}");
        $this->line("Domain   : {$tenant->domain}");
        $this->line("DB       : {$dbName}.sqlite");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
