# 🚀 Laravel Module Maker Commands

Custom Artisan Commands untuk mempercepat development menggunakan struktur **Module** (seperti `nwidart/laravel-modules`) di Laravel 10–13.

Tool ini dibuat untuk developer yang ingin bekerja lebih **cepat, rapi, dan minim error**.

---

## 📋 Requirements

- PHP >= 8.1  
- Laravel 10 / 11 / 12 / 13  
- Struktur modular (`Modules/`)

### 📦 Packages

- `nwidart/laravel-modules` → Module management  
- `spatie/laravel-permission` → Role & permission  
- `stancl/tenancy` *(optional)* → Multi-tenant  
- `Laravel UI / Breeze` → Authentication  

---

## ✨ Features

### 🟢 `module:make-view` (Generate)

Generate semua kebutuhan CRUD hanya dengan **1 command**:

#### ✅ Migration
- Nama class = nama file (**anti error redeclare**)  
- Support singular & plural otomatis  
- Primary key: **UUID**

#### ✅ Model
- Menggunakan PHP Attributes (`#[Fillable]`)  
- Include `HasUuids`  
- `$table` otomatis terisi  

#### ✅ Controller
- Full CRUD:
  - `index`
  - `create`
  - `store`
  - `show`
  - `edit`
  - `update`
  - `destroy`  
- View path otomatis lowercase (`module::view`)  
- Validasi & redirect siap pakai  

#### ✅ Views
Auto-generate:
- `index`
- `create`
- `edit`
- `show`

Template: **Bootstrap 5**

#### ✅ Route
- Auto tambah `use Controller`  
- Menggunakan style modern (`Controller::class`)  
- Route lama tetap aman  

---

### 🔴 `module:make-delete` (Cleanup)

Menghapus module secara **bersih total**:

- 🗑️ Migration  
- 🗑️ Model  
- 🗑️ Controller  
- 🗑️ Folder views  
- 🗑️ Baris route + `use` di `web.php`  

---

## 🛠️ Installation

### 1. Copy Command Files

Letakkan di:

```bash
app/Console/Commands/
```

Pastikan terdapat:
- `MakeViewCommand.php`
- `MakeDeleteCommand.php`

---

### 2. Permission (Optional)

```bash
chmod +x app/Console/Commands/*.php
```

---

### 3. Update Autoload

```bash
composer dump-autoload
```

---

## 📖 Usage

### 1️⃣ Generate Module

```bash
php artisan module:make-view {Module} {Fitur} {Kolom...}
```

#### Contoh:

```bash
php artisan module:make-view Dosen kelas nama ruangan
php artisan module:make-view Dosen jadwal hari jam ruangan
php artisan module:make-view Akademik matkul nama sks
```

---

### 📌 Hasil Route (`web.php`)

```php
use Modules\Dosen\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Route;

Route::resource('kelas', KelasController::class)->names('kelas');
```

---

### 📌 Hasil Controller

```php
return view('dosen::kelas.index', compact('data'));
```

---

### 2️⃣ Delete Module

```bash
php artisan module:make-delete {Module} {Fitur}
```

#### Contoh:

```bash
php artisan module:make-delete Dosen kelas
```

Konfirmasi:
- Ketik `yes` atau tekan **Enter**

---

## ⚠️ Important

Setelah delete module, wajib jalankan:

```bash
composer dump-autoload
```

---

## 🐛 Troubleshooting

### ❌ Class does not exist

```bash
composer dump-autoload
```

---

### ❌ No hint path defined for [Module]

```php
view('dosen::kelas.index') // ✅ BENAR
view('Dosen::kelas.index') // ❌ SALAH
```

---

### ❌ Cannot redeclare class

✔ Sudah diperbaiki:
- Nama class migration selalu sama dengan nama file

---

## 📂 Generated Structure

```bash
Modules/
└── Dosen/
    ├── app/
    │   ├── Http/Controllers/KelasController.php
    │   └── Models/Kelas.php
    ├── Database/Migrations/
    │   └── create_kelas_table.php
    ├── Resources/views/kelas/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── show.blade.php
    └── Routes/web.php
```

---

## 👨‍💻 Author

- GitHub: [@faizincwds](https://github.com/faizincwds)

---

## 📄 License

MIT License — bebas digunakan untuk kebutuhan pribadi maupun komersial.