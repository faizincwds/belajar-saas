# 🚀 Laravel Module Maker Commands

Custom Artisan Commands untuk mempercepat development menggunakan struktur **Module** (seperti `nwidart/laravel-modules`) di Laravel 10–13.

Tool ini dibuat untuk developer yang ingin bekerja lebih **cepat, rapi, dan minim error**.

---

## 📋 Requirements

- PHP >= 8.5
- Laravel 13  
- Struktur modular (`Modules/`)

### 📦 Daftar Package Terinstall

### 🔐 Security & Form
| Package | Fungsi | Status |
| :--- | :--- | :--- |
| `anhskohbo/no-captcha` | Proteksi form dengan Google ReCAPTCHA | ✅ DONE |

---

### 📊 Data & Report
| Package | Fungsi | Status |
| :--- | :--- | :--- |
| `avadim/fast-excel-laravel` | Export & Import file Excel | ✅ DONE |
| `barryvdh/laravel-dompdf` | Generate file PDF | ✅ DONE |
| `arielmejiadev/larapex-charts` | Membuat grafik & chart interaktif | ✅ DONE |

---

### 🏗️ Core System & Architecture
| Package | Fungsi | Status |
| :--- | :--- | :--- |
| `nwidart/laravel-modules` | Sistem modular (terpisah per folder) | ✅ DONE |
| `stancl/tenancy` | Fitur Multi-Tenant / Multi-Company (SaaS) | ✅ DONE |
| `laravel/sentinel` | Autentikasi & keamanan tambahan | ✅ DONE |

---

### 🛡️ Spatie Packages
| Package | Fungsi | Status |
| :--- | :--- | :--- |
| `spatie/laravel-activitylog` | Mencatat log aktivitas user | ✅ DONE |
| `spatie/laravel-backup` | Backup database & file otomatis | ✅ DONE |
| `spatie/laravel-html` | Helper pembuatan form & elemen HTML | ✅ DONE |
| `spatie/laravel-medialibrary` | Manajemen upload file & gambar | ✅ DONE |
| `spatie/laravel-permission` | Pengaturan Role & Hak Akses User | ✅ DONE |
| `spatie/laravel-query-builder` | Filter data API & tabel dengan mudah | ✅ DONE |
| `spatie/laravel-responsecache` | Cache respon agar website cepat | ✅ DONE |
| `spatie/laravel-schedule-monitor` | Monitoring jadwal proses otomatis | ✅ DONE |
| `spatie/laravel-settings` | Menyimpan pengaturan sistem ke database | ✅ DONE |
| `spatie/laravel-sitemap` | Generate sitemap.xml untuk SEO | ✅ DONE |

---

### 🛠️ Development Tools
| Package | Fungsi | Status |
| :--- | :--- | :--- |
| `laravel/telescope` | Debug & melihat log request/query | ✅ DONE |
| `laravel/tinker` | Interaksi sistem via command line | ✅ DONE |
| `laravel/pail` | Melihat log sistem secara realtime | ✅ DONE |
| `pestphp/pest-plugin-laravel` | Tools testing kode aplikasi | ✅ DONE |

---

## ❌ Catatan Package yang Diskip

- **`spatie/laravel-updater`** ➡️ *SKIP* (Belum ada versi yang support Laravel 13).
- **`spatie/laravel-string`** ➡️ *SKIP* (Sudah digantikan helper `Str::` bawaan).
- **`sentry/sentry-laravel`** ➡️ *REMOVED* (Tidak menggunakan layanan pihak ketiga).

---

## 📝 Cara Update Modul

Karena tidak pakai auto-updater, cara update modul adalah:

### 1️⃣ Via Command Line
```bash
# Refresh daftar modul
php artisan module:refresh

# Update database modul tertentu
php artisan module:migrate NamaModul

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