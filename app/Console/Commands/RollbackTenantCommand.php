<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

#[Signature('tenant:rollback {domain}')]
#[Description('Rollback migration for specific tenant')]
class RollbackTenantCommand extends Command
{
    public function handle(): int
    {
        $domain = $this->argument('domain');

        $tenant = Tenant::where('domain', $domain)->first();

        if (!$tenant) {
            $this->error("Tenant tidak ditemukan!");
            return self::FAILURE;
        }

        $dbPath = $tenant->data['db_path'];

        config([
            'database.connections.tenant' => [
                'driver' => 'sqlite',
                'database' => $dbPath,
                'prefix' => '',
            ],
        ]);

        Artisan::call('migrate:rollback', [
            '--database' => 'tenant',
            '--path' => '/database/migrations/Tenants',
            '--force' => true,
        ]);

        $this->info("✅ Rollback selesai!");

        return self::SUCCESS;
    }
}
