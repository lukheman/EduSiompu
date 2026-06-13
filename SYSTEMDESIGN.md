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
