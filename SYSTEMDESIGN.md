# ERD

```
erDiagram
    kelas ||--o{ siswa : "memiliki"

    guru ||--o{ guru_ampu : "ditugaskan"
    mata_pelajaran ||--o{ guru_ampu : "diajarkan pada"
    kelas ||--o{ guru_ampu : "menerima"
    tahun_ajaran ||--o{ guru_ampu : "berlangsung selama"

    guru_ampu ||--o{ pertemuan : "mengadakan"
    guru_ampu ||--o{ materi : "menyediakan"

    pertemuan ||--o{ absensi : "mencatat"
    siswa ||--o{ absensi : "menghadiri"

    admin {
        int id_admin PK
        string email
        string password
        string nama
    }

    tahun_ajaran {
        int id_tahun_ajaran PK
        string nama_tahun
        enum semester
        boolean status_aktif
    }

    kelas {
        int id_kelas PK
        string nama_kelas
    }

    mata_pelajaran {
        int id_mata_pelajaran PK
        string kode_mapel
        string nama_mapel
    }

    guru {
        int id_guru PK
        string nip
        string nama_guru
        string password
    }

    siswa {
        int id_siswa PK
        int id_kelas FK
        string nisn
        string nama_siswa
        string password
    }

    guru_ampu {
        int id_guru_ampu PK
        int id_guru FK
        int id_mata_pelajaran FK
        int id_kelas FK
        int id_tahun_ajaran FK
    }

    pertemuan {
        int id_pertemuan PK
        int id_guru_ampu FK
        int pertemuan_ke
        date tanggal
        text pokok_bahasan
    }

    absensi {
        int id_absensi PK
        int id_pertemuan FK
        int id_siswa FK
        enum status_kehadiran
    }

    materi {
        int id_materi PK
        int id_guru_ampu FK
        string judul_materi
        string file_path
        string jenis_file
    }
```

### **1. Fitur untuk Admin (Tata Usaha / Operator Sekolah)**

Admin memiliki hak akses penuh untuk mengelola data master atau data inti sekolah agar sistem bisa berjalan.

* **Autentikasi:** Login dan Logout.
* **Manajemen Data Tahun Ajaran:** Mengatur tahun ajaran aktif dan semester (Ganjil/Genap).
* **Manajemen Data Kelas:** Menambah, mengubah, dan menghapus (CRUD) daftar kelas (misal: X IPA 1, XI IPS 2).
* **Manajemen Data Mata Pelajaran:** Mengelola daftar mata pelajaran yang ada di sekolah.
* **Manajemen Data Guru:** Mengelola profil guru, termasuk kredensial login mereka.
* **Manajemen Data Siswa:** Mengelola data siswa dan menentukan penempatan siswa ke dalam kelas masing-masing.
* **Manajemen Guru Pengampu:** Menetapkan guru mana yang mengampu mata pelajaran tertentu di kelas tertentu.
* **Rekapitulasi Absensi Global:** Memantau dan mencetak (Export ke PDF/Excel) laporan rekapitulasi absensi seluruh siswa per kelas atau per rentang waktu tertentu.

### **2. Fitur untuk Guru**

Guru berfokus pada kegiatan operasional akademik harian di kelas yang mereka ampu.

* **Autentikasi:** Login dan Logout.
* **Dashboard Guru:** Menampilkan jadwal mengajar dan ringkasan kelas serta mata pelajaran yang diampu.
* **Pengolahan Absensi Siswa:**
* Memilih kelas dan jadwal pertemuan.
* Melakukan input status kehadiran siswa (Hadir, Sakit, Izin, Alpa).
* Mengubah data absensi jika terjadi kesalahan input.


* **Manajemen Materi Pembelajaran:**
* Mengunggah (*upload*) file materi (berupa dokumen PDF, PPT, Word, atau *link* eksternal/video).
* Mengelompokkan materi berdasarkan mata pelajaran dan pertemuan keberapa.
* Mengedit atau menghapus materi yang sudah diunggah.


* **Laporan Absensi Kelas:** Melihat dan mencetak rekap absensi khusus untuk kelas dan mata pelajaran yang diajarkannya.

### **3. Fitur untuk Siswa**

Siswa bertindak sebagai *end-user* yang mengonsumsi data dari sistem.

* **Autentikasi:** Login dan Logout (biasanya menggunakan NISN sebagai *username*).
* **Dashboard Siswa:** Ringkasan informasi akademik pribadi.
* **Riwayat Absensi:** Melihat catatan kehadiran pribadi per mata pelajaran (melihat berapa kali Hadir, Sakit, Izin, dan Alpa).
* **Akses Materi Pembelajaran:**
* Melihat daftar materi yang telah diunggah oleh guru.
* Mengunduh (*download*) atau membaca materi tersebut langsung dari sistem.



---

Rincian di atas sudah mencakup solusi dari masalah utama (absensi manual dan ketiadaan arsip digital materi).

Apakah dari daftar ini ada fitur tambahan lain yang sekiranya diminta oleh Mbak Cici, misalnya fitur pengumuman sekolah atau mungkin integrasi notifikasi WhatsApp untuk siswa yang alpa?
