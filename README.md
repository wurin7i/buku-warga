# 📖 Buku Warga | Community Management System

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-4.x-F59E0B?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMSA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDMgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.3+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MIT License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![Version](https://img.shields.io/badge/Version-0.1-blue?style=for-the-badge)](https://github.com/wurin7i/buku-warga/releases)

*Modern community management system for neighborhoods, villages, and residential areas*

[🇬🇧 English](#english) | [🇮🇩 Bahasa Indonesia](#bahasa-indonesia)

</div>

---

## 🇬🇧 English

### Overview

**Buku Warga** is a comprehensive community management system designed to help local administrators manage residents, properties, and neighborhood data efficiently. Built with modern web technologies, it provides an intuitive interface for tracking community demographics, property ownership, and residency information.

### ✨ Features

- **👥 Resident Management**
  - Complete demographic data (NIK, KK, personal information)
  - Family relationship tracking
  - Residency status monitoring (current residents, non-residents, deceased)
  - Occupancy history and move-in/move-out tracking

- **🏠 Property Management**
  - Property registration with detailed information
  - Owner and occupant relationship management
  - Building and land classification
  - Geographic area organization (RT/RW, clusters)

- **📊 Administrative Tools**
  - Interactive dashboard with community statistics
  - Advanced filtering and search capabilities
  - Data export functionality
  - Multi-level administrative regions support

- **🔐 Security & Access Control**
  - Role-based permission system
  - Secure user authentication
  - Data integrity protection

### 🇮🇩 Indonesian Data Integration

**Buku Warga** includes a custom **ID-Refs** package that provides comprehensive Indonesian citizen identity references:

- **Regional Data**: Complete Indonesian administrative regions (Province, Regency, District, Village)
- **Identity References**: Standard codes for gender, religion, marital status, blood type, occupation
- **Data Synchronization**: Built-in command to update regional data from Kemendagri sources
- **Validation**: NIK (16-digit) validation and formatting
- **Localization**: Full Indonesian language support

The integrated ID-Refs package ensures your community data aligns with official Indonesian standards and provides accurate demographic information management.

### 🛠 Tech Stack

- **Backend**: Laravel 11.x (PHP 8.3+)
- **Admin Panel**: Filament 4.x
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templates with Livewire
- **Styling**: Tailwind CSS
- **Icons**: Google Material Design Icons
- **Indonesian References**: Custom ID-Refs package for Indonesian citizen identity data

### 📋 Requirements

- PHP 8.3 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 13+
- Node.js 18+ and npm
- Web server (Apache/Nginx)

### 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/wurin7i/buku-warga.git
   cd buku-warga
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=buku_warga
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

8. **Seed Indonesian reference data (optional)**
   ```bash
   php artisan idrefs:update-data
   ```

9. **Initialize community data**
   ```bash
   php artisan bukuwarga:init
   ```

10. **Build assets and start server**
    ```bash
    npm run build
    php artisan serve
    ```

### � Troubleshooting

#### Filament Admin Panel Not Loading Properly

If the Filament admin panel appears broken or unstyled after installation or git operations, this is likely due to missing Filament assets. Follow these steps:

1. **Regenerate Filament assets**
   ```bash
   php artisan filament:assets
   ```

2. **Clear application cache**
   ```bash
   php artisan optimize:clear
   ```

3. **Optimize the application**
   ```bash
   php artisan optimize
   ```

4. **Refresh your browser** and clear browser cache if necessary

> **Note**: Filament assets are not tracked in Git (added to `.gitignore`) to prevent conflicts. They need to be regenerated after fresh installations or when switching between branches.

### �📖 Usage

1. **Access the application** at `http://localhost:8000`
2. **Login** with your admin credentials
3. **Initialize community** by selecting your village/area
4. **Start managing**:
   - Add residents with complete demographic data
   - Register properties and assign owners
   - Track occupancy and residency changes
   - Generate reports and statistics

### 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### 📞 Support

For support and questions:
- Create an issue on GitHub
- Contact: [wuri.nugrahadi@gmail.com](mailto:wuri.nugrahadi@gmail.com)

---

## 🇮🇩 Bahasa Indonesia

### Gambaran Umum

**Buku Warga** adalah sistem manajemen komunitas yang komprehensif yang dirancang untuk membantu administrator lokal mengelola data penduduk, properti, dan lingkungan secara efisien. Dibangun dengan teknologi web modern, sistem ini menyediakan antarmuka yang intuitif untuk melacak demografi komunitas, kepemilikan properti, dan informasi tempat tinggal.

### ✨ Fitur Utama

- **👥 Manajemen Warga**
  - Data demografi lengkap (NIK, KK, informasi pribadi)
  - Pelacakan hubungan keluarga
  - Pemantauan status tempat tinggal (penghuni aktif, non-penghuni, meninggal)
  - Riwayat hunian dan pelacakan pindah masuk/keluar

- **🏠 Manajemen Properti**
  - Pendaftaran properti dengan informasi detail
  - Manajemen hubungan pemilik dan penghuni
  - Klasifikasi bangunan dan tanah
  - Organisasi wilayah geografis (RT/RW, cluster)

- **📊 Alat Administratif**
  - Dashboard interaktif dengan statistik komunitas
  - Kemampuan filter dan pencarian lanjutan
  - Fungsi ekspor data
  - Dukungan wilayah administratif multi-level

- **🔐 Keamanan & Kontrol Akses**
  - Sistem izin berbasis peran
  - Autentikasi pengguna yang aman
  - Perlindungan integritas data

### 🇮🇩 Integrasi Data Indonesia

**Buku Warga** menyertakan paket **ID-Refs** khusus yang menyediakan referensi identitas warga Indonesia yang komprehensif:

- **Data Wilayah**: Wilayah administratif Indonesia lengkap (Provinsi, Kabupaten, Kecamatan, Desa)
- **Referensi Identitas**: Kode standar untuk jenis kelamin, agama, status perkawinan, golongan darah, pekerjaan
- **Sinkronisasi Data**: Command built-in untuk memperbarui data wilayah dari sumber Kemendagri
- **Validasi**: Validasi dan format NIK (16 digit)
- **Lokalisasi**: Dukungan bahasa Indonesia lengkap

Paket ID-Refs terintegrasi memastikan data komunitas Anda selaras dengan standar resmi Indonesia dan menyediakan pengelolaan informasi demografi yang akurat.

### 🛠 Teknologi

- **Backend**: Laravel 11.x (PHP 8.3+)
- **Panel Admin**: Filament 4.x
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templates dengan Livewire
- **Styling**: Tailwind CSS
- **Ikon**: Google Material Design Icons
- **Referensi Indonesia**: Paket ID-Refs khusus untuk data identitas warga Indonesia

### 📋 Persyaratan Sistem

- PHP 8.3 atau lebih tinggi
- Composer 2.x
- MySQL 8.0+ atau PostgreSQL 13+
- Node.js 18+ dan npm
- Web server (Apache/Nginx)

### 🚀 Instalasi

1. **Clone repository**
   ```bash
   git clone https://github.com/wurin7i/buku-warga.git
   cd buku-warga
   ```

2. **Install dependensi PHP**
   ```bash
   composer install
   ```

3. **Install dependensi Node.js**
   ```bash
   npm install
   ```

4. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Konfigurasi database**
   Edit file `.env` dengan kredensial database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=buku_warga
   DB_USERNAME=username_anda
   DB_PASSWORD=password_anda
   ```

6. **Jalankan migrasi**
   ```bash
   php artisan migrate
   ```

7. **Buat user admin**
   ```bash
   php artisan make:filament-user
   ```

8. **Seed data referensi Indonesia (opsional)**
   ```bash
   php artisan idrefs:update-data
   ```

9. **Inisialisasi data komunitas**
   ```bash
   php artisan bukuwarga:init
   ```

10. **Build asset dan jalankan server**
    ```bash
    npm run build
    php artisan serve
    ```

### � Pemecahan Masalah

#### Panel Admin Filament Tidak Muncul dengan Benar

Jika panel admin Filament terlihat rusak atau tidak bergaya setelah instalasi atau operasi git, kemungkinan disebabkan oleh asset Filament yang hilang. Ikuti langkah-langkah berikut:

1. **Regenerasi asset Filament**
   ```bash
   php artisan filament:assets
   ```

2. **Bersihkan cache aplikasi**
   ```bash
   php artisan optimize:clear
   ```

3. **Optimasi aplikasi**
   ```bash
   php artisan optimize
   ```

4. **Refresh browser** dan bersihkan cache browser jika diperlukan

> **Catatan**: Asset Filament tidak dilacak di Git (ditambahkan ke `.gitignore`) untuk mencegah konflik. Asset perlu di-regenerasi setelah instalasi baru atau saat berpindah antar branch.

### �📖 Penggunaan

1. **Akses aplikasi** di `http://localhost:8000`
2. **Login** dengan kredensial admin Anda
3. **Inisialisasi komunitas** dengan memilih desa/wilayah Anda
4. **Mulai mengelola**:
   - Tambahkan warga dengan data demografi lengkap
   - Daftarkan properti dan tetapkan pemilik
   - Lacak perubahan hunian dan tempat tinggal
   - Buat laporan dan statistik

### 🤝 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b feature/fitur-menakjubkan`)
3. Commit perubahan Anda (`git commit -m 'Tambah fitur menakjubkan'`)
4. Push ke branch (`git push origin feature/fitur-menakjubkan`)
5. Buka Pull Request

### 📄 Lisensi

Proyek ini dilisensikan di bawah Lisensi MIT - lihat file [LICENSE](LICENSE) untuk detail.

### 📞 Dukungan

Untuk dukungan dan pertanyaan:
- Buat issue di GitHub
- Kontak: [wuri.nugrahadi@gmail.com](mailto:wuri.nugrahadi@gmail.com)

---

<div align="center">

**Made with ❤️ for Indonesian Communities**

*Dibuat dengan ❤️ untuk Komunitas Indonesia*

</div>
