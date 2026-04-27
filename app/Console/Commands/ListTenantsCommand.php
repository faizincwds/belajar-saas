<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tenant:list')]
#[Description('Show all registered tenants')]
class ListTenantsCommand extends Command
{
    public function handle(): int
    {
        $tenants = Tenant::all();

        if ($tenants->isEmpty()) {
            $this->warn("Belum ada tenant yang terdaftar.");
            return self::SUCCESS;
        }

        $this->info("=================================");
        $this->info(" DAFTAR SEMUA TENANT ");
        $this->info("=================================");

        foreach ($tenants as $index => $tenant) {
            $no = $index + 1;
            $this->line(" {$no}. {$tenant->name}");
            $this->line("    • Domain : {$tenant->domain}");
            $this->line("    • DB     : {$tenant->data['db']}.sqlite");
            $this->line("    • ID     : {$tenant->id}");
            $this->line("---------------------------------");
        }

        $this->info("Total: " . $tenants->count() . " tenant");

        return self::SUCCESS;
    }
}
