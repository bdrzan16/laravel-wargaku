<div class="table-responsive">
    <table class="table">
        <thead class="table-wargaku">
            <tr>
                <th scope="col" class="text-center align-middle white-space-nowrap">No</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">NIK/KK</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Nama</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Jenis Kelamin</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Tempat Lahir</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Tanggal Lahir</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Umur</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Status</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Agama</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Pendidikan</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Pekerjaan</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Kependuduk dalam Kelurahan</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Alamat</th>
                <th scope="col" class="text-center align-middle white-space-nowrap">Tanggal Mulai Di RT</th>

                <th scope="col" class="text-center align-middle white-space-nowrap">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @if($penduduk->isEmpty())
                <tr>
                    <td colspan="18" class="text-center text-muted">
                        @if(request()->filled('daerah') || request()->filled('rw') || request()->filled('rt'))
                            Tidak ada data penduduk yang tersedia.
                        @else
                            Silakan pilih daerah terlebih dahulu.
                        @endif
                    </td>
                </tr>
            @else
                @foreach ($penduduk as $p)
                <tr>
                    <th scope="row" class="text-center align-middle">{{ $loop->iteration }}</th>
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->no_nik }}</td>
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->nama }}</td>
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->jenis_kelamin }}</td>
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->tempat_lahir }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ \Carbon\Carbon::parse($p->tanggal_lahir)->translatedFormat('d F Y') }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->umur }} tahun</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->status }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->agama }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->pendidikan }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->pekerjaan }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->kep_di_kelurahan }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ $p->alamat }}</td>        
                    <td class="text-center align-middle white-space-nowrap" >{{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d F Y') }}</td>

                    <td class="text-center align-middle white-space-nowrap">
                        <button class="btn btn-sm me-1 custom-soft-warning btn-edit-penduduk"
                            data-id="{{ e($p->id) }}"
                            data-no_nik="{{ e($p->no_nik) }}"
                            data-nama="{{ e($p->nama) }}"
                            data-jenis_kelamin="{{ e($p->jenis_kelamin) }}"
                            data-tempat_lahir="{{ e($p->tempat_lahir) }}"
                            data-tanggal_lahir_display="{{ e(\Carbon\Carbon::parse($p->tanggal_lahir)->translatedFormat('d F Y')) }}"
                            data-tanggal_lahir="{{ e(\Carbon\Carbon::parse($p->tanggal_lahir)->format('Y-m-d')) }}"
                            data-status="{{ e($p->status) }}"
                            data-agama="{{ e($p->agama) }}"
                            data-pendidikan="{{ e($p->pendidikan) }}"
                            data-pendidikan_lainnya="{{ e($p->pendidikan_lainnya) }}"
                            data-pekerjaan="{{ e($p->pekerjaan) }}"
                            data-kep_di_kelurahan="{{ e($p->kep_di_kelurahan) }}"
                            data-kep_di_kelurahan_lainnya="{{ e($p->kep_di_kelurahan_lainnya) }}"
                            data-alamat="{{ e($p->alamat) }}"
                            data-tgl_mulai_display="{{ e(\Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d F Y')) }}"
                            data-tgl_mulai="{{ e(\Carbon\Carbon::parse($p->tgl_mulai)->format('Y-m-d')) }}">
                            <i class="fas fa-edit fa-fw text-warning"></i>
                        </button>

                        <!-- Tombol delete -->
                        <button type="button" class="btn btn-sm me-1 custom-soft-danger btn-delete-penduduk"
                            data-id="{{ $p->id }}" data-nama="{{ $p->nama }}">
                            <i class="fas fa-trash fa-fw text-danger"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
    {{ $penduduk->appends(request()->query())->links() }}
</div>

{{-- Tampilan Mobile Card --}}
<div class="mobile-card">
    @if($penduduk->isEmpty())
        <p class="text-muted text-center">
            @if(request()->filled('daerah') || request()->filled('rw') || request()->filled('rt'))
                Tidak ada data penduduk yang tersedia.
            @else
                Silakan pilih daerah terlebih dahulu.
            @endif
        </p>
    @else
        @foreach ($penduduk as $p)
            <div class="card">
                <div class="card-title">{{ $p->nama }}</div>
                <div class="card-text">
                    <small><strong>NIK:</strong> {{ $p->no_nik }}</small>
                    <small><strong>Jenis Kelamin:</strong> {{ $p->jenis_kelamin }}</small>
                    <small><strong>Tempat, Tanggal Lahir:</strong> {{ $p->tempat_lahir }}, {{ \Carbon\Carbon::parse($p->tanggal_lahir)->translatedFormat('d F Y') }}</small>
                    <small><strong>Umur:</strong> {{ $p->umur }} tahun</small>
                    <small><strong>Status:</strong> {{ $p->status }}</small>
                    <small><strong>Agama:</strong> {{ $p->agama }}</small>
                    <small><strong>Pendidikan:</strong> {{ $p->pendidikan }}</small>
                    <small><strong>Pekerjaan:</strong> {{ $p->pekerjaan }}</small>
                    <small><strong>Alamat:</strong> {{ $p->alamat }}</small>
                    <small><strong>Kependuduk dalam Kelurahan:</strong> {{ $p->kep_di_kelurahan }}</small>
                    <small><strong>Tanggal Mulai Di RT:</strong> {{ \Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d F Y') }}</small>
                </div>
                <div class="mt-2 d-flex justify-content-end">
                    <button class="btn btn-sm me-1 btn-edit-penduduk"
                        data-id="{{ e($p->id) }}"
                        data-no_nik="{{ e($p->no_nik) }}"
                        data-nama="{{ e($p->nama) }}"
                        data-jenis_kelamin="{{ e($p->jenis_kelamin) }}"
                        data-tempat_lahir="{{ e($p->tempat_lahir) }}"
                        data-tanggal_lahir_display="{{ e(\Carbon\Carbon::parse($p->tanggal_lahir)->translatedFormat('d F Y')) }}"
                        data-tanggal_lahir="{{ e(\Carbon\Carbon::parse($p->tanggal_lahir)->format('Y-m-d')) }}"
                        data-status="{{ e($p->status) }}"
                        data-agama="{{ e($p->agama) }}"
                        data-pendidikan="{{ e($p->pendidikan) }}"
                        data-pendidikan_lainnya="{{ e($p->pendidikan_lainnya) }}"
                        data-pekerjaan="{{ e($p->pekerjaan) }}"
                        data-kep_di_kelurahan="{{ e($p->kep_di_kelurahan) }}"
                        data-kep_di_kelurahan_lainnya="{{ e($p->kep_di_kelurahan_lainnya) }}"
                        data-alamat="{{ e($p->alamat) }}"
                        data-tgl_mulai_display="{{ e(\Carbon\Carbon::parse($p->tgl_mulai)->translatedFormat('d F Y')) }}"
                        data-tgl_mulai="{{ e(\Carbon\Carbon::parse($p->tgl_mulai)->format('Y-m-d')) }}">
                        <i class="fas fa-edit fa-fw"></i>
                    </button>

                    <button type="button" class="btn btn-sm btn-delete-penduduk"
                        data-id="{{ $p->id }}" data-nama="{{ $p->nama }}">
                        <i class="fas fa-trash fa-fw"></i>
                    </button>
                </div>
            </div>
        @endforeach
    @endif
</div>
{{ $penduduk->appends(request()->query())->links() }}