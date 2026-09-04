# Sistem Informasi Manajemen Dokumen HRD (SIMD HRD)

Sistem berbasis web yang digunakan untuk menyimpan, mengelola, dan menampilkan berbagai dokumen HRD dengan konsep direktori hirarki menyerupai Google Drive (Divisi &rarr; Jabatan &rarr; Dokumen).

---

## 🌟 Fitur Utama

- **Role Management (RBAC)**:
  - **Ketua (Admin)**: Hak akses penuh (CRUD Dokumen, Kelola Divisi & Jabatan, Setting Hirarki Struktur Organisasi).
  - **User Umum**: Hak akses khusus *View Only*, *Inline Preview*, & *Download File*.
- **5 Kategori Dokumen HRD**:
  1. Standard Operating Procedure (SOP)
  2. Job Description (JOB)
  3. Job Analysis (JA)
  4. Business Process Model and Notation (BPMN)
  5. Struktur Organisasi Perusahaan
- **Pencarian Global**: Filter cepat berdasarkan kata kunci, tipe dokumen, divisi, dan jabatan.
- **Media Previewer**: Inline viewer untuk PDF, Gambar (`PNG/JPG/WEBP`), Video (`MP4`), Audio (`MP3`), dan file Office/BPMN/Archive.
- **Bagan Struktur Organisasi**: Visualisasi tree diagram hierarki jabatan per divisi.

---

## 🔑 Kredensial Akun (Default Seeder)

| Role | Email | Password |
|---|---|---|
| **Ketua HRD** | `ketua@sukhati.com` | `password` |
| **User Umum** | `user@sukhati.com` | `password` |

---

## 🛠️ Instalasi & Cara Menjalankan

1. **Clone repository**:
   ```bash
   git clone https://github.com/aseprahayu2244-wq/sukahati.git
   cd sukahati
   ```

2. **Install dependensi PHP & buat `.env`**:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. **Konfigurasi Database** di `.env` (MySQL):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=sukhati
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Jalankan Migration, Seeder & Storage Link**:
   ```bash
   php artisan migrate:fresh --seed
   php artisan storage:link
   ```

5. **Jalankan Local Server**:
   ```bash
   php artisan serve
   ```
   Buka di browser: `http://localhost:8000`

---

## 💻 Tech Stack

- **Backend**: PHP 8.1+ & Laravel 10
- **Auth**: Laravel Breeze
- **Frontend**: Blade Templates, Tailwind CSS CDN, FontAwesome 6 Icons
- **Database**: MySQL
