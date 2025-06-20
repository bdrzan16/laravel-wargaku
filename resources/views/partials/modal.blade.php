<!-- Modal Filter -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-header-wargaku">
                <h5 class="modal-title" id="filterModalLabel">Filter Data Penduduk</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="filterForm" method="GET" action="{{ route('penduduk.index') }}">
                <div class="modal-body">
                    {{-- Hidden field agar tetap mengirim wilayah --}}
                    @role('admin')
                        <input type="hidden" name="daerah_id" value="{{ request('daerah_id') }}">
                        <input type="hidden" name="rw_id" value="{{ request('rw_id') }}">
                        <input type="hidden" name="rt_id" value="{{ request('rt_id') }}">
                    @endrole

                    @role('rt')
                        {{-- RT: semua data otomatis dari user --}}
                        <input type="hidden" name="rt_id" value="{{ auth()->user()->rt_id }}">
                        <input type="hidden" name="rw_id" value="{{ auth()->user()->rw_id }}">
                        <input type="hidden" name="daerah_id" value="{{ auth()->user()->daerah_id }}">
                    @endrole

                    @role('rw')
                        {{-- RW: rt_id dari filter (request), rw_id & daerah_id dari user --}}
                        <input type="hidden" name="rt_id" value="{{ request('rt_id') }}">
                        <input type="hidden" name="rw_id" value="{{ auth()->user()->rw_id }}">
                        <input type="hidden" name="daerah_id" value="{{ auth()->user()->daerah_id }}">
                    @endrole
                    <!-- Jenis Kelamin -->
                    <div class="form-group">
                        <label for="filterJK">Jenis Kelamin</label>
                        <select class="form-control" id="filterJK" name="jenis_kelamin">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ request('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ request('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                    <!-- Usia -->
                    <div class="form-group">
                        <label for="filterUsia">Usia</label>
                        <select class="form-control" id="filterUsia" name="usia">
                            <option value="">-- Pilih Usia --</option>
                            <option value="Balita (< 5 Tahun)" {{ request('usia') == 'Balita (< 5 Tahun)' ? 'selected' : '' }}>Balita (&lt; 5 Tahun)</option>
                            <option value="Anak-Anak (6 - 12 Tahun)" {{ request('usia') == 'Anak-Anak (6 - 12 Tahun)' ? 'selected' : '' }}>Anak-Anak (6 - 12 Tahun)</option>
                            <option value="Remaja (13 - 17 Tahun)" {{ request('usia') == 'Remaja (13 - 17 Tahun)' ? 'selected' : '' }}>Remaja (13 - 17 Tahun)</option>
                            <option value="Dewasa Muda (18 - 30 Tahun)" {{ request('usia') == 'Dewasa Muda (18 - 30 Tahun)' ? 'selected' : '' }}>Dewasa Muda (18 - 30 Tahun)</option>
                            <option value="Dewasa Madya (31 - 59 Tahun)" {{ request('usia') == 'Dewasa Madya (31 - 59 Tahun)' ? 'selected' : '' }}>Dewasa Madya (31 - 59 Tahun)</option>
                            <option value="Lansia (> 60 Tahun)" {{ request('usia') == 'Lansia (> 60 Tahun)' ? 'selected' : '' }}>Lansia (&gt; 60 Tahun)</option>
                        </select>
                    </div>
                    <!-- Status Pernikahan -->
                    <div class="form-group">
                        <label for="filterStatusNikah">Status Pernikahan</label>
                        <select class="form-control" id="filterStatusNikah" name="status_pernikahan">
                            <option value="">-- Pilih Status --</option>
                            <option value="Belum Kawin" {{ request('status_pernikahan') == 'Belum Kawin' ? 'selected' : '' }}>Belum Kawin</option>
                            <option value="Kawin" {{ request('status_pernikahan') == 'Kawin' ? 'selected' : '' }}>Kawin</option>
                            <option value="Duda" {{ request('status_pernikahan') == 'Duda' ? 'selected' : '' }}>Duda</option>
                            <option value="Janda" {{ request('status_pernikahan') == 'Janda' ? 'selected' : '' }}>Janda</option>
                        </select>
                    </div>
                    
                    <!-- Agama -->
                    <div class="form-group">
                        <label for="filterAgama">Agama</label>
                        <select class="form-control" id="filterAgama" name="agama">
                            <option value="">-- Pilih Agama --</option>
                            <option value="Islam" {{ request('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                            <option value="Kristen Protestan" {{ request('agama') == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                            <option value="Kristen Katolik" {{ request('agama') == 'Kristen Katolik' ? 'selected' : '' }}>Kristen Katolik</option>
                            <option value="Hindu" {{ request('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                            <option value="Buddha" {{ request('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                            <option value="Konghucu" {{ request('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        </select>
                    </div>
        
                    <!-- Pendidikan -->
                    <div class="form-group">
                        <label for="filterPendidikan">Pendidikan</label>
                        <select class="form-control" id="filterPendidikan" name="pendidikan">
                            <option value="">-- Pilih Pendidikan --</option>
                            @foreach (['Belum Sekolah','SD', 'SLTP', 'SLTA', 'D3', 'S1', 'S2', 'S3'] as $item)
                                <option value="{{ $item }}" {{ request('pendidikan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                            <option value="lainnya" {{ request('pendidikan') == 'lainnya' ? 'selected' : '' }}>Lainnya / Tidak Tercantum</option>
                        </select>
                    </div>
        
                    <!-- Pekerjaan -->
                    <div class="form-group">
                        <label for="filterPekerjaan">Pekerjaan</label>
                        <select class="form-control" id="filterPekerjaan" name="pekerjaan">
                            <option value="">-- Pilih Pekerjaan --</option>
                            @foreach (['Wiraswasta', 'Pegawai Swasta', 'Pegawai BUMN', 'Ibu Rumah Tangga (IRT)', 'Buruh Lepas', 'Pelajar', 'Mahasiswa'] as $item)
                                <option {{ request('pekerjaan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                            <option value="lainnya" {{ request('pekerjaan') == 'lainnya' ? 'selected' : '' }}>Lainnya / Tidak Tercantum</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-wargaku">Terapkan Filter</button>
                </div>
            </form> 
        </div>
    </div>
</div>
<!-- Modal Tambah/Edit Penduduk -->
<div class="modal fade @if($errors->any()) show d-block @endif" id="formPendudukModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="@if($errors->any()) display: block; @endif">
        <div class="modal-content">
            <form id="formPenduduk" method="POST">
                @csrf
                <input type="hidden" id="formMethod" name="_method" value="POST">
                <div class="modal-header modal-header-wargaku">
                    <h5 class="modal-title" id="modalTitle">Tambah Data Penduduk</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">
                    <div class="form-group col-md-6">
                        <label for="no_nik">Nomor NIK/KK</label>
                        <input type="text" name="no_nik" maxlength="16" pattern="\d{16}" class="form-control @error('no_nik') is-invalid @enderror" id="no_nik" value="{{ old('no_nik') }}" required autofocus>
                        @error('no_nik')
                            <div class="invalid-feedback">
                                {{ $message == 'The no nik has already been taken.' ? 'Nomor NIK tidak boleh sama.' : $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="nama">Nama Penduduk</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" id="nama" value="{{ old('nama') }}" required>
                        @error('nama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror" id="jenis_kelamin" required>
                            <option value="" selected disabled>Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                        @error('jenis_kelamin')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" id="tempat_lahir" value="{{ old('tempat_lahir') }}" required>
                        @error('tempat_lahir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" id="tanggal_lahir" value="{{ old('tanggal_lahir') }}" max="{{ date('Y-m-d') }}" required>
                        @error('tanggal_lahir')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="status">Status</label>
                        <select name="status" class="form-control @error('status') is-invalid @enderror" id="status" value="{{ old('status') }}" required>
                            <option value="" selected disabled>Pilih Status</option>
                            <option value="Kawin">Kawin</option>
                            <option value="Belum Kawin">Belum Kawin</option>
                            <option value="Duda">Duda</option>
                            <option value="Janda">Janda</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="agama">Agama</label>
                        <select name="agama" class="form-control @error('agama') is-invalid @enderror" id="agama" value="{{ old('agama') }}" required>
                            <option value="" selected disabled>Pilih Agama</option>
                            <option value="Islam">Islam</option>
                            <option value="Kristen Protestan">Kristen Protestan</option>
                            <option value="Kristen Katolik">Kristen Katolik</option>
                            <option value="Hindu">Hindu</option>
                            <option value="Buddha">Buddha</option>
                            <option value="Konghucu">Konghucu</option>
                        </select>
                        @error('agama')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <!-- Pendidikan -->
                    <div class="form-group col-md-6">
                        <label for="pendidikanSelect">Pendidikan</label>
                        <select name="pendidikan" class="form-control @error('pendidikan') is-invalid @enderror" id="pendidikanSelect" required>
                            <option value="" disabled {{ old('pendidikan') ? '' : 'selected' }}>Pilih Pendidikan</option>
                            @foreach(['Belum Sekolah','SD','SLTP','SLTA','D3','S1','S2','S3','Lainnya'] as $item)
                                <option value="{{ $item }}" {{ old('pendidikan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="pendidikan_lainnya" class="form-control mt-2" id="pendidikanLainnyaInput" placeholder="Tulis pendidikan..." style="display:none;" value="{{ old('pendidikan_lainnya') }}">
                        @error('pendidikan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pekerjaan">Pekerjaan</label>
                        <input type="text" name="pekerjaan" class="form-control @error('pekerjaan') is-invalid @enderror" id="pekerjaan" value="{{ old('pekerjaan') }}" required>
                        @error('pekerjaan')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <!-- Kependudukan -->
                    <div class="form-group col-md-6">
                        <label for="kep_di_kelurahanSelect">Kependudukan dalam Kelurahan</label>
                        <select name="kep_di_kelurahan" class="form-control @error('kep_di_kelurahan') is-invalid @enderror" id="kep_di_kelurahanSelect" required>
                            <option value="" disabled {{ old('kep_di_kelurahan') ? '' : 'selected' }}>Pilih Kependudukan</option>
                            @foreach(['Kepala Keluarga','Istri','Anak','Lainnya'] as $item)
                                <option value="{{ $item }}" {{ old('kep_di_kelurahan') == $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="kep_di_kelurahan_lainnya" class="form-control mt-2" id="kep_di_kelurahanLainnyaInput" placeholder="Tulis kependudukan..." style="display:none;" value="{{ old('kep_di_kelurahan_lainnya') }}">
                        @error('kep_di_kelurahan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="alamat">Alamat</label>
                        <input type="text" name="alamat" class="form-control @error('alamat') is-invalid @enderror" id="alamat" value="{{ old('alamat') }}" required>
                        @error('alamat')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="tgl_mulai">Tanggal Mulai Di RT</label>
                        <input type="date" name="tgl_mulai" class="form-control @error('tgl_mulai') is-invalid @enderror" id="tgl_mulai" value="{{ old('tgl_mulai') }}" required>
                        @error('tgl_mulai')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    @role('admin')
                        <input type="hidden" name="rt_id" id="hiddenRT" value="{{ request('rt_id') ?? '' }}">
                        <input type="hidden" name="rw_id" id="hiddenRW" value="{{ request('rw_id') ?? '' }}">
                        <input type="hidden" name="daerah_id" id="hiddenDaerah" value="{{ request('daerah_id') ?? '' }}">
                    @endrole

                    @role('rt')
                        {{-- RT: semua data otomatis dari user --}}
                        <input type="hidden" name="rt_id" value="{{ auth()->user()->rt_id }}">
                        <input type="hidden" name="rw_id" value="{{ auth()->user()->rw_id }}">
                        <input type="hidden" name="daerah_id" value="{{ auth()->user()->daerah_id }}">
                    @endrole

                    @role('rw')
                        {{-- RW: rt_id dari filter (request), rw_id & daerah_id dari user --}}
                        <input type="hidden" name="rt_id" value="{{ request('rt_id') }}">
                        <input type="hidden" name="rw_id" value="{{ auth()->user()->rw_id }}">
                        <input type="hidden" name="daerah_id" value="{{ auth()->user()->daerah_id }}">
                    @endrole
                    
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-wargaku" id="submitButton">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalDeletePenduduk" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <form method="POST" id="formDeletePenduduk">
        @csrf
        @method('DELETE')
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="modalDeleteLabel">Konfirmasi Hapus</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <p>Apakah kamu yakin ingin menghapus data penduduk <strong id="namaPendudukHapus"></strong>?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-danger">Hapus</button>
        </div>
        </div>
    </form>
    </div>
</div>