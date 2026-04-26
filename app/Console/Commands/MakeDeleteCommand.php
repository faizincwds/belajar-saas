<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;
use Illuminate\Support\Str;
use File;

#[Signature('module:make-delete {module} {name}')]
#[Description('Delete Migration, Model, Controller, View files and Remove Route completely')]
class MakeDeleteCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $moduleName     = $this->argument('module');
        $folderName     = $this->argument('name');
        $modelName      = ucfirst($folderName);
        $controllerName = ucfirst($folderName) . 'Controller';
        $tableName      = Str::snake(Str::plural($folderName));

        $this->warn("⚠️  You are about to delete files for Module: {$moduleName}, Name: {$folderName}");
        if (!$this->confirm("Continue?", true)) {
            $this->info("Operation cancelled.");
            return;
        }

        $this->newLine();
        $this->info("🗑️  Deleting all files...");

        // =============================================
        // 1. HAPUS MIGRATION
        // =============================================
        $migrationPath = module_path($moduleName, "Database/Migrations");
        $files = glob($migrationPath . "/*_create_{$tableName}_table.php");
        
        if (!empty($files)) {
            foreach ($files as $file) {
                File::delete($file);
                $this->line("✅ Deleted: Database/Migrations/" . basename($file));
            }
        } else {
            $this->line("⚠️  Migration not found.");
        }

        // =============================================
        // 2. HAPUS MODEL
        // =============================================
        $modelFile = module_path($moduleName, "app/Models/{$modelName}.php");
        if (File::exists($modelFile)) {
            File::delete($modelFile);
            $this->line("✅ Deleted: app/Models/{$modelName}.php");
        } else {
            $this->line("⚠️  Model not found.");
        }

        // =============================================
        // 3. HAPUS CONTROLLER
        // =============================================
        $controllerFile = module_path($moduleName, "app/Http/Controllers/{$controllerName}.php");
        if (File::exists($controllerFile)) {
            File::delete($controllerFile);
            $this->line("✅ Deleted: app/Http/Controllers/{$controllerName}.php");
        } else {
            $this->line("⚠️  Controller not found.");
        }

        // =============================================
        // 4. HAPUS FOLDER VIEWS
        // =============================================
        $viewFolder = module_path($moduleName, "Resources/views/{$folderName}");
        if (File::isDirectory($viewFolder)) {
            File::deleteDirectory($viewFolder);
            $this->line("✅ Deleted: Resources/views/{$folderName} (Folder)");
        } else {
            $this->line("⚠️  View folder not found.");
        }

        // =============================================
        // 5. HAPUS ROUTE & USE DI WEB.PHP
        // =============================================
        $routePath = module_path($moduleName, "Routes/web.php");
        if (File::exists($routePath)) {
            
            $content = File::get($routePath);
            
            // Kode yang mau dihapus
            $useStatement = "use Modules\\{$moduleName}\\Http\\Controllers\\{$controllerName};";
            $routeLine = "Route::resource('{$folderName}', {$controllerName}::class)->names('{$folderName}');";

            // ✅ HAPUS ROUTE
            $content = str_replace($routeLine, "", $content);
            $content = str_replace($routeLine . "\n", "", $content);
            
            // ✅ HAPUS USE STATEMENT
            $content = str_replace($useStatement, "", $content);
            $content = str_replace($useStatement . "\n", "", $content);

            // Bersihkan baris kosong yang numpuk biar rapi
            $content = preg_replace("/\n\n\n+/", "\n\n", $content);
            $content = rtrim($content) . "\n";

            File::put($routePath, $content);
            $this->line("✅ Route & Use statement removed from web.php");
        } else {
            $this->line("⚠️  Route file not found.");
        }

        $this->newLine();
        $this->info("🚀 ALL FILES DELETED SUCCESSFULLY!");
        $this->line("----------------------------------------------------");
        $this->line("📌 Run this command:");
        $this->line("   composer dump-autoload");
        $this->line("----------------------------------------------------");
    }
}
