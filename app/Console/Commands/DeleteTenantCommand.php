<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tenant:delete {domain}')]
#[Description('Delete Tenant and their database file')]
class DeleteTenantCommand extends Command
{
    public function handle(): int
    {
        $domain = $this->argument('domain');

        // 🔍 Cari tenant berdasarkan domain
        $tenant = Tenant::where('domain', $domain)->first();

        if (!$tenant) {
            $this->error("Tenant dengan domain {$domain} tidak ditemukan!");
            return self::FAILURE;
        }

        // ⚠️ Konfirmasi sebelum hapus
        $this->warn("Hati-hati! Anda akan menghapus Tenant:");
        $this->line("Nama   : {$tenant->name}");
        $this->line("Domain : {$tenant->domain}");
        $this->line("DB File : {$tenant->data['db']}.sqlite");

        if (!$this->confirm('Yakin ingin menghapus data ini?')) {
            $this->info('Proses dibatalkan.');
            return self::SUCCESS;
        }

        // 🗑️ Ambil path database
        $dbPath = $tenant->data['db_path'] ?? null;

        // 🗑️ Hapus data dari tabel central
        $tenant->delete();

        // 🗑️ Hapus file database SQLite
        if ($dbPath && file_exists($dbPath)) {
            unlink($dbPath);
            $this->info("✅ File database berhasil dihapus.");
        }

        // ✅ Output sukses
        $this->info("=================================");
        $this->info("✅ Tenant berhasil dihapus!");
        $this->line("Domain : {$domain}");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
