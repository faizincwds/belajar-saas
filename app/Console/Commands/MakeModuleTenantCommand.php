<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('make:module-tenant {name}')]
#[Description('Create new Module with Tenant migration & seeder folders')]
class MakeModuleTenantCommand extends Command
{
    public function handle(): int
    {
        $name = $this->argument('name');

        // 1. Buat modul utama
        $this->call('module:make', [
            'name' => [$name]
        ]);

        // 2. Buat folder khusus tenant
        $modulePath = module_path($name);

        // ✅ GUNAKAN File::makeDirectory (PASTI JALAN)
        $migrationsTenant = "{$modulePath}/Database/Migrations/Tenants";
        $seedersTenant   = "{$modulePath}/Database/Seeders/Tenants";

        if (!File::isDirectory($migrationsTenant)) {
            File::makeDirectory($migrationsTenant, 0777, true);
            $this->line("📂 Created: Database/Migrations/Tenants");
        }

        if (!File::isDirectory($seedersTenant)) {
            File::makeDirectory($seedersTenant, 0777, true);
            $this->line("📂 Created: Database/Seeders/Tenants");
        }

        $this->info("=================================");
        $this->info("✅ Modul {$name} dibuat dengan Tenant Support!");
        $this->line("Path : {$modulePath}");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
