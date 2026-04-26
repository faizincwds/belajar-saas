<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Attributes\Description;
use Illuminate\Support\Str;
use File;

#[Signature('module:make-view {module} {name} {columns?*}')]
#[Description('Generate Migration, Model, Controller, View & Route automatically with UUID & Attributes')]
class MakeViewCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Ambil parameter dari command
        $moduleName     = $this->argument('module');
        $folderName     = $this->argument('name');
        $modelName      = ucfirst($folderName);
        $controllerName = ucfirst($folderName) . 'Controller';
        $tableName      = Str::snake(Str::plural($folderName));
        $migrationClassName = 'Create' . ucfirst(Str::camel($tableName)) . 'Table';
        $columns        = $this->argument('columns') ?? [];
        
        // ✅ TAMBAH INI: Buat variabel module huruf kecil
        $moduleNameLower = strtolower($moduleName);

        // Daftar File View
        $files = ['index', 'create', 'edit', 'show'];

        $this->info("⏳ Generating ALL files for Module: {$moduleName}...");
        $this->newLine();

        // =============================================
        // 0. PASTIKAN FOLDER DASAR SUDAH ADA
        // =============================================
        $basePath = module_path($moduleName);
        
        $folders = [
            "{$basePath}/app/Models",
            "{$basePath}/app/Http/Controllers",
            "{$basePath}/Resources/views",
            "{$basePath}/Database/Migrations",
            "{$basePath}/Routes",
        ];

        foreach ($folders as $folder) {
            if (!File::isDirectory($folder)) {
                File::makeDirectory($folder, 0755, true);
                $showPath = str_replace(base_path(), '', $folder);
                $this->line("📂 Folder created: .{$showPath}");
            }
        }

        // =============================================
        // 1. MEMBUAT MIGRATION
        // =============================================
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $migrationPath = module_path($moduleName, "Database/Migrations/{$migrationName}");

        if (!File::exists($migrationPath)) {
            $migrationColumns = "";
            foreach ($columns as $col) {
                $migrationColumns .= "            \$table->string('$col')->nullable();\n";
            }

            $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Builder;

class {$migrationClassName} extends Migration
{
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
$migrationColumns
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
}
PHP;
            File::put($migrationPath, $migrationContent);
            $this->line("✅ File created: Modules/{$moduleName}/Database/Migrations/{$migrationName}");
        } else {
            $this->line("⚠️  Migration already exists.");
        }

        // =============================================
        // 2. MEMBUAT MODEL
        // =============================================
        $modelPath = module_path($moduleName, "app/Models/{$modelName}.php");

        if (!File::exists($modelPath)) {
            $fillableString = "[" . implode(', ', array_map(fn($col) => "'$col'", $columns)) . "]";

            $modelContent = <<<PHP
<?php

namespace Modules\\{$moduleName}\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable({$fillableString})]
class {$modelName} extends Model
{
    use HasFactory, HasUuids;

    protected \$table = '{$tableName}';
}
PHP;
            File::put($modelPath, $modelContent);
            $this->line("✅ File created: Modules/{$moduleName}/app/Models/{$modelName}.php");
        } else {
            $this->line("⚠️  Model already exists.");
        }

        $this->newLine();

        // =============================================
        // 3. MEMBUAT CONTROLLER (FIXED VIEW PATH)
        // =============================================
        $controllerPath = module_path($moduleName, "app/Http/Controllers/{$controllerName}.php");

        if (!File::exists($controllerPath)) {
            $validationRules = "";
            foreach ($columns as $col) {
                $validationRules .= "            '$col' => 'required',\n";
            }

            $controllerContent = <<<PHP
<?php

namespace Modules\\{$moduleName}\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\\{$moduleName}\Models\\{$modelName};

class {$controllerName} extends Controller
{
    public function index()
    {
        \$data = {$modelName}::latest()->paginate(10);
        return view('{$moduleNameLower}::{$folderName}.index', compact('data'));
    }

    public function create()
    {
        return view('{$moduleNameLower}::{$folderName}.create');
    }

    public function store(Request \$request)
    {
        try {
            \$validated = \$request->validate([
$validationRules
            ]);

            {$modelName}::create(\$validated);
            return redirect()->route('{$folderName}.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception \$e) {
            return back()->with('error', 'Error: ' . \$e->getMessage());
        }
    }

    public function edit(\$id)
    {
        \$data = {$modelName}::findOrFail(\$id);
        return view('{$moduleNameLower}::{$folderName}.edit', compact('data'));
    }

    public function update(Request \$request, \$id)
    {
        try {
            \$validated = \$request->validate([
$validationRules
            ]);

            \$item = {$modelName}::findOrFail(\$id);
            \$item->update(\$validated);
            return redirect()->route('{$folderName}.index')->with('success', 'Data berhasil diupdate.');
        } catch (\Exception \$e) {
            return back()->with('error', 'Error: ' . \$e->getMessage());
        }
    }

    public function show(\$id)
    {
        \$data = {$modelName}::findOrFail(\$id);
        return view('{$moduleNameLower}::{$folderName}.show', compact('data'));
    }

    public function destroy(\$id)
    {
        try {
            {$modelName}::findOrFail(\$id)->delete();
            return redirect()->route('{$folderName}.index')->with('success', 'Data berhasil dihapus.');
        } catch (\Exception \$e) {
            return back()->with('error', 'Error: ' . \$e->getMessage());
        }
    }
}
PHP;
            File::put($controllerPath, $controllerContent);
            $this->line("✅ File created: Modules/{$moduleName}/app/Http/Controllers/{$controllerName}.php");
        } else {
            $this->line("⚠️  Controller already exists.");
        }

        $this->newLine();

        // =============================================
        // 4. MEMBUAT VIEWS
        // =============================================
        $folderPath = module_path($moduleName, "Resources/views/{$folderName}");

        if (!File::isDirectory($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
            $this->line("📂 Folder created: Modules/{$moduleName}/Resources/views/{$folderName}");
        }

        foreach ($files as $file) {
            $filePath = "{$folderPath}/{$file}.blade.php";

            if (!File::exists($filePath)) {
                $content = <<<BLADE
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ ucfirst('{$folderName}') }} - {{ ucfirst('{$file}') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>{{ ucfirst('{$folderName}') }} - {{ ucfirst('{$file}') }}</h3>
                <a href="{{ route('{$folderName}.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
            </div>
            <div class="card-body">
                <!-- ISI KONTEN DISINI -->
                <p class="text-muted">// Content here</p>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
BLADE;
                File::put($filePath, $content);
                $this->line("📄 File created: Modules/{$moduleName}/Resources/views/{$folderName}/{$file}.blade.php");
            }
        }

        // =============================================
        // 5. TAMBAH ROUTE OTOMATIS
        // =============================================
        $routePath = module_path($moduleName, "Routes/web.php");

        if (!File::exists($routePath)) {
            File::put($routePath, "<?php\n\nuse Illuminate\Support\Facades\Route;\n");
        }

        $existingContent = File::get($routePath);
        $routeNameCheck = "->names('{$folderName}')";
        $useStatement = "use Modules\\{$moduleName}\\Http\\Controllers\\{$controllerName};";
        $routeLine = "Route::resource('{$folderName}', {$controllerName}::class)->names('{$folderName}');";

        if (!Str::contains($existingContent, $routeNameCheck)) {
            
            $contentWithoutTag = str_replace("<?php", "", $existingContent);

            if (!Str::contains($contentWithoutTag, $useStatement)) {
                $newContent = "<?php\n\n{$useStatement}\n" . ltrim($contentWithoutTag) . "\n{$routeLine}\n";
            } else {
                $newContent = $existingContent . "\n{$routeLine}\n";
            }

            File::put($routePath, $newContent);
            $this->line("✅ Route added: Modules/{$moduleName}/Routes/web.php");
        } else {
            $this->line("⚠️  Route already exists, skipped.");
        }

        $this->newLine();
        $this->info("🚀 ALL FILES GENERATED SUCCESSFULLY!");
        $this->line("----------------------------------------------------");
        $this->line("📌 Style: Laravel Modern (Attributes & HasUuids)");
        $this->line("📌 Route Style: Route::resource\('xx', Controller::class\)");
        $this->line("📌 View Path: Fixed lowercase (module::view)");
        $this->line("📌 Kolom: " . (empty($columns) ? "Tidak ada" : implode(', ', $columns)));
        $this->line("----------------------------------------------------");
    }
}
