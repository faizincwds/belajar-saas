<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;
use Illuminate\Support\Str;
use File;

#[Signature('tenant:delete-all')]
#[Description('Delete semua tenants file')]
class DeleteAllCommand extends Command
{
    public function handle(): int
    {
        
        $this->warn("⚠️  You are about to delete files for Tenants: Migrations, Seeders, SQLite");
        if (!$this->confirm("Continue?", false)) {
            $this->info("Operation cancelled.");
            return self::SUCCESS;
        }

        $this->newLine();
        $this->info("🗑️  Deleting all files...");
        $this->newLine();
        
        $migrationPath = database_path('migrations/Tenants');
        $files = glob($migrationPath . "/*_create_*_table.php");
        
        if (!empty($files)) {
            foreach ($files as $file) {
                File::delete($file);
                $this->line("✅ Del migrate file: "  . basename($file));
                $this->line("---------------------------------");
                $this->line("Path: {$migrationPath}");
                $this->newLine();
            }
        } else {
            $this->line("⚠️  Files Migration not found.");
        }
        
        $seedersPath = database_path('seeders/Tenants');
        $files = glob($seedersPath . "/*Seeder.php");
        
        if (!empty($files)) {
            foreach ($files as $file) {
                File::delete($file);
                $this->line("✅ Del seeders file: "  . basename($file));
                $this->line("Path: {$seedersPath}");
                $this->line("---------------------------------");
                $this->newLine();
            }
        } else {
            $this->line("⚠️  Files Seeders not found.");
        }
        
        $dbPath = database_path('/Tenants');
        $files = glob($dbPath . "/*.sqlite");
        
        if (!empty($files)) {
            foreach ($files as $file) {
                File::delete($file);
                $this->line("✅ Del Sqlite file: "  . basename($file));
                $this->line("Path: {$dbPath}");
                $this->line("✅ Selesai");
                $this->line("---------------------------------");
                $this->newLine();
            }
        } else {
            $this->line("⚠️  Files Sqlite not found.");
        }
        
        return self::SUCCESS;
    }
}