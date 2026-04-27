<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

#[Signature('module:create-tenant {module} {table} {fields?*}')]
#[Description('Create Migration & Seeder for Tenant inside specific Module')]
class ModuleCreateTenantCommand extends Command
{
    public function handle(): int
    {
        $moduleName = $this->argument('module');
        $tableInput = $this->argument('table');
        $fields = $this->argument('fields') ?? [];

        $tableName = Str::snake(Str::plural($tableInput));
        $migrationName = 'create_' . $tableName . '_table';
        $seederName = ucfirst(Str::camel($tableName)) . 'Seeder';

        $this->info("=================================");
        $this->info("⚡ GENERATING FOR MODULE: {$moduleName}");
        $this->info("=================================");

        // 📂 Path Lokasi (Sesuai Nama Module)
        $modulePath = base_path("Modules/{$moduleName}");
        $migrationsPath = "{$modulePath}/Database/Migrations/Tenants";
        $seedersPath = "{$modulePath}/Database/Seeders/Tenants";

        // Cek Module ada atau tidak
        if (!File::isDirectory($modulePath)) {
            $this->error("❌ Module {$moduleName} tidak ditemukan!");
            return self::FAILURE;
        }

        // 📂 1. Buat Folder jika belum ada
        if (!File::isDirectory($migrationsPath)) {
            File::makeDirectory($migrationsPath, 0777, true);
            $this->line("📂 Created: Modules/{$moduleName}/Database/Migrations/Tenants");
        }

        if (!File::isDirectory($seedersPath)) {
            File::makeDirectory($seedersPath, 0777, true);
            $this->line("📂 Created: Modules/{$moduleName}/Database/Seeders/Tenants");
        }

        // 📄 2. Buat Migration
        $migrationFile = date('Y_m_d_His') . "_{$migrationName}.php";
        $migrationFullPath = "{$migrationsPath}/{$migrationFile}";

        if (!File::exists($migrationFullPath)) {
            // 🔥 Logic Fields
            if (empty($fields)) {
                $fieldsString = "// Default only ID & Timestamps";
            } else {
                $fieldsString = "";
                foreach ($fields as $f) {
                    $fieldsString .= "            \$table->string('$f')->nullable();\n";
                }
            }

            $migrationCode = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->uuid('id')->primary();
{$fieldsString}
            \$table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;
            File::put($migrationFullPath, $migrationCode);
            $this->line("✅ Created: Modules/{$moduleName}/Database/Migrations/Tenants/{$migrationFile}");
        }

        // 📄 3. Buat Seeder
        $seederFullPath = "{$seedersPath}/{$seederName}.php";

        if (!File::exists($seederFullPath)) {
            // 🔥 Logic Seeder
            if (empty($fields)) {
                $seederCode = <<<PHP
<?php

namespace Modules\\{$moduleName}\\Database\Seeders\Tenants;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class {$seederName} extends Seeder
{
    public function run()
    {
        // Sample Data (Default Only ID)
        \$data = [];
        for(\$i=1; \$i<=3; \$i++){
            \$data[] = [
                'id' => (string) Str::uuid(),
            ];
        }

        DB::table('{$tableName}')->insert(\$data);
        \$this->command->info('✅ Sample ID inserted to {$tableName}');
    }
}
PHP;
            } else {
                $fillable = implode(', ', array_map(fn($f) => "'$f'", $fields));
                $seederCode = <<<PHP
<?php

namespace Modules\\{$moduleName}\\Database\Seeders\Tenants;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class {$seederName} extends Seeder
{
    public function run()
    {
        // Sample Data
        \$data = [];
        for(\$i=1; \$i<=3; \$i++){
            \$item = [];
            foreach([{$fillable}] as \$col){
                \$item[\$col] = ucfirst(\$col) . " " . \$i;
            }
            \$item['id'] = (string) Str::uuid();
            \$data[] = \$item;
        }

        DB::table('{$tableName}')->insert(\$data);
        \$this->command->info('✅ Sample data inserted to {$tableName}');
    }
}
PHP;
            }

            File::put($seederFullPath, $seederCode);
            $this->line("✅ Created: Modules/{$moduleName}/Database/Seeders/Tenants/{$seederName}.php");
        }

        $this->newLine();
        $this->info("📝 CARA PAKAI");
        $this->line("php artisan tenant:migrate");
        $this->line("php artisan tenant:seed NamaModule {$seederName}");
        $this->info("=================================");

        return self::SUCCESS;
    }
}
