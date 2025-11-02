# 📖 Buku Warga | Community Management System

<div align="center">

[![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Filament](https://img.shields.io/badge/Filament-3.x-F59E0B?style=for-the-badge&logo=data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjQiIGhlaWdodD0iMjQiIHZpZXdCb3g9IjAgMCAyNCAyNCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEyIDJMMTMuMDkgOC4yNkwyMSA5TDEzLjA5IDE1Ljc0TDEyIDIyTDEwLjkxIDE1Ljc0TDMgOUwxMC45MSA4LjI2TDEyIDJaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4K&logoColor=white)](https://filamentphp.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MIT License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

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

### 🛠 Tech Stack

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Admin Panel**: Filament 3.x
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templates with Livewire
- **Styling**: Tailwind CSS
- **Icons**: Google Material Design Icons

### 📋 Requirements

- PHP 8.1 or higher
- Composer 2.x
- MySQL 8.0+ or PostgreSQL 13+
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

8. **Initialize community data**
   ```bash
   php artisan bukuwarga:init
   ```

9. **Build assets and start server**
   ```bash
   npm run build
   php artisan serve
   ```

### 📖 Usage

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

### 🛠 Teknologi

- **Backend**: Laravel 10.x (PHP 8.1+)
- **Panel Admin**: Filament 3.x
- **Database**: MySQL/PostgreSQL
- **Frontend**: Blade Templates dengan Livewire
- **Styling**: Tailwind CSS
- **Ikon**: Google Material Design Icons

### 📋 Persyaratan Sistem

- PHP 8.1 atau lebih tinggi
- Composer 2.x
- MySQL 8.0+ atau PostgreSQL 13+
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

8. **Inisialisasi data komunitas**
   ```bash
   php artisan bukuwarga:init
   ```

9. **Build asset dan jalankan server**
   ```bash
   npm run build
   php artisan serve
   ```

### 📖 Penggunaan

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

