@component('layouts.guest', ['type' => 'auth'])
    <div class="login-container">
        <div class="login-card" style="max-width: 760px;">
            <div class="brand-logo mb-4">
                <div class="icon-wrapper"><i class="fas fa-chalkboard-teacher"></i></div>
                <h1>Pilih Mode Guru</h1>
                <p>Masuk sebagai {{ $guru->nama_guru }} sesuai tugas yang ingin dikerjakan.</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('guru.mode.select') }}">
                @csrf
                <div class="row g-3">
                    @if($hasMapel)
                        <div class="col-md-6">
                            <button type="submit" name="mode" value="mata_pelajaran" class="btn btn-outline-primary w-100 text-start p-4 h-100">
                                <i class="fas fa-book-open fa-2x mb-3"></i>
                                <strong class="d-block fs-5">Guru Mata Pelajaran</strong>
                                <span class="small d-block mt-2">Input nilai, kelola materi, tugas, dan absensi jadwal mengajar.</span>
                                <span class="small d-block mt-2 text-danger">Tidak dapat mencetak rapor atau laporan absensi kelas.</span>
                            </button>
                        </div>
                    @endif

                    @if($hasWali)
                        <div class="col-md-6">
                            <button type="submit" name="mode" value="wali_kelas" class="btn btn-outline-success w-100 text-start p-4 h-100">
                                <i class="fas fa-users fa-2x mb-3"></i>
                                <strong class="d-block fs-5">Wali Kelas</strong>
                                <span class="small d-block mt-2">Kelola pemantauan kelas dan akses laporan siswa.</span>
                                <span class="small d-block mt-2 text-success">Dapat mencetak rapor dan laporan absensi kelas.</span>
                            </button>
                        </div>
                    @endif
                </div>
            </form>

            @if(! $hasMapel && ! $hasWali)
                <div class="alert alert-warning mt-4 mb-0">Akun belum memiliki penugasan guru atau kelas wali.</div>
            @endif
        </div>
    </div>
@endcomponent
