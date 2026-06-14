# Skenario Pengujian EduSiompu (Sistem Informasi Akademik)

Dokumen ini berisi skenario pengujian komprehensif (UAT - *User Acceptance Testing*) untuk memvalidasi fungsi-fungsi inti dari aplikasi EduSiompu.

---

## 1. Modul Autentikasi (Login & Register)

### 1.1 Login Pengguna (Siswa, Guru, Admin)
| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Login Berhasil (Admin) | Buka halaman `/login` > Pilih tab "Admin" > Masukkan Email dan Password valid > Klik "Masuk" | Diarahkan ke Dashboard Admin (`/admin/dashboard`), *sidebar* menampilkan menu Admin. | [ ] |
| 2 | Login Berhasil (Guru) | Buka halaman `/login` > Pilih tab "Guru" > Masukkan NIP dan Password valid > Klik "Masuk" | Diarahkan ke Dashboard Guru (`/guru/dashboard`), *sidebar* menampilkan menu Guru. | [ ] |
| 3 | Login Berhasil (Siswa) | Buka halaman `/login` > Pilih tab "Siswa" > Masukkan NISN dan Password valid > Klik "Masuk" | Diarahkan ke Dashboard Siswa (`/siswa/dashboard`), *sidebar* menampilkan menu Siswa. | [ ] |
| 4 | Login Gagal (Kredensial Salah) | Buka halaman `/login` > Pilih role > Masukkan data yang salah > Klik "Masuk" | Muncul pesan *error* validasi merah di bawah kolom input, tetap berada di halaman Login. | [ ] |
| 5 | Ganti Tab Role Login | Buka halaman `/login` > Klik tab Siswa, Guru, dan Admin secara bergantian | Teks panduan (NISN/NIP/Email Admin) pada kolom input berubah sesuai tab yang dipilih. | [ ] |

---

## 2. Modul Profil Pengguna

### 2.1 Manajemen Profil (Upload Foto)
| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Buka Halaman Profil | Login > Klik menu "Profile" di sudut kanan atas atau sidebar | Halaman profil sesuai dengan data asli dari pengguna (Nama, Email/NIP/NISN). | [ ] |
| 2 | Upload Avatar (Valid) | Di halaman profil > Pilih foto berformat JPG/PNG di bawah 2MB > Simpan | Foto profil terunggah, foto lama terhapus dari server, foto muncul di bingkai bulat. | [ ] |
| 3 | Upload Avatar (Tidak Valid) | Pilih file PDF atau gambar berukuran lebih dari 2MB > Simpan | Proses dihentikan, muncul pesan *error* validasi ukuran atau jenis file. | [ ] |

---

## 3. Modul Manajemen Admin

### 3.1 Manajemen Data Guru (`/admin/guru`)
| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Menambah Guru Baru | Klik "Tambah Guru" > Isi NIP, Nama, Password > Simpan | Guru berhasil disimpan, tampil di tabel dengan bingkai inisial nama. | [ ] |
| 2 | Upload Foto Guru | Edit atau Tambah Guru > Unggah foto pada form "Foto Profil" > Simpan | Foto langsung tampil secara asinkron (Live Preview), lalu disimpan ke tabel saat di-submit. | [ ] |
| 3 | Lihat Detail Guru | Pada baris tabel Guru > Klik ikon "Mata" (Detail) | Muncul pop-up detail berisi foto profil (melingkar), NIP, Nama, dan tanggal daftar. | [ ] |
| 4 | Edit Data Guru | Pada baris tabel Guru > Klik ikon "Pensil" (Edit) > Ubah nama > Simpan | Data di tabel langsung terbarui tanpa memuat ulang (*reload*) halaman. | [ ] |
| 5 | Hapus Data Guru | Klik ikon "Sampah" (Hapus) > Konfirmasi Hapus | Data hilang dari tabel, dan file foto profil guru tersebut di dalam `/storage/app/public` terhapus bersih. | [ ] |

### 3.2 Manajemen Data Siswa (`/admin/siswa`)
| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Menambah Siswa | Klik "Tambah Siswa" > Isi NISN, Nama, dan Password > Simpan | Data siswa baru muncul pada urutan teratas tabel, notifikasi berhasil (*flash message*). | [ ] |
| 2 | Search Siswa | Ketik nama atau NISN di kotak pencarian | Tabel secara instan (real-time/debounce) memfilter siswa sesuai pencarian. | [ ] |
| 3 | Lihat Detail Siswa | Klik ikon "Mata" pada baris siswa tertentu | Modal pop-up modern terbuka, menampilkan Foto profil siswa, NISN, Nama, dan Tanggal Terdaftar. | [ ] |
| 4 | Upload Foto Siswa | Buka modal Tambah/Edit Siswa > Upload gambar avatar | Pratinjau gambar (Live Preview) bekerja. Foto tersimpan rapi berbentuk lingkaran saat tabel dimuat ulang. | [ ] |

### 3.3 Manajemen Penugasan Guru / Guru Ampu (`/admin/guru-ampu`)
| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Tambah Penugasan | Klik "Tambah Penugasan" > Pilih Tahun Ajaran, Guru, Mapel, Kelas > Simpan | Data penugasan baru tampil di tabel dengan ikon/foto guru, mata pelajaran (badge), dan kelas. | [ ] |
| 2 | Validasi Foto di Tabel | Lihat tabel Penugasan Guru | Jika guru tersebut memiliki foto, tampil fotonya (bulat). Jika tidak, tampil kotak inisial (bulat). | [ ] |
| 3 | Filter & Pencarian | Gunakan Dropdown Filter Tahun Ajaran atau Filter Guru | Daftar penugasan secara *live* menyesuaikan data sesuai kriteria yang dipilih. | [ ] |
| 4 | Lihat Detail Penugasan | Klik ikon "Mata" pada baris tabel | Pop-up memunculkan Foto Guru, nama guru, NIP guru, beserta kelas & mapel yang diajarkan dalam bentuk tabel mini. | [ ] |

---

## 4. Keamanan dan Batasan (Authorization)

| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Akses Halaman Tanpa Login | Buka URL `/admin/dashboard` dari browser (Guest) | Langsung diarahkan paksa (*redirect*) kembali ke halaman `/login`. | [ ] |
| 2 | Akses Lintas Role (Cross-Role) | Login sebagai Siswa > Ketik URL `/admin/guru` secara manual di browser | Akses ditolak (Error 403 Forbidden / Unauthorized), sistem melarang siswa mengakses wilayah admin. | [ ] |
| 3 | Akses Halaman Terpisah | Periksa *Sidebar* ketika Login sebagai Guru | Menu-menu khusus Admin (seperti Manajemen Guru/Siswa) sama sekali tidak terlihat di layar Guru. | [ ] |

---

## 5. Antarmuka dan Pengalaman Pengguna (UI/UX)

| No | Skenario Pengujian | Langkah-Langkah | Hasil yang Diharapkan | Status |
|----|-------------------|-----------------|-----------------------|--------|
| 1 | Responsivitas Mobile | Buka aplikasi di perangkat layar kecil (Smartphone) / inspeksi layar HP | *Sidebar* otomatis tersembunyi/mengecil (bisa dimunculkan via tombol menu), tabel dapat digeser ke samping (*horizontal scroll*). | [ ] |
| 2 | Dark Mode | Klik ikon Bulan sabit di Navbar/Sidebar | Seluruh komponen berubah tema gelap (warna dominan pekat), tabel dan tulisan tetap terbaca dengan nyaman. | [ ] |
| 3 | Landing Page | Kunjungi URL `/` | Muncul antarmuka megah SMAN 1 Siompu dengan *background* foto gedung sekolah asli, tanpa jejak nama templat lama ("AdminPro"). | [ ] |
| 4 | Interaktivitas Tombol | Arahkan kursor (*Hover*) pada komponen *Glass Card* di Landing Page | Elemen terangkat sedikit dengan bayangan yang membesar. Ikon beranimasi halus membesar dan sedikit miring. | [ ] |
