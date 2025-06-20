<?php

namespace App\Filters;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class PendudukFilter
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        // Filter berdasarkan relasi ID
        if ($this->request->filled('daerah_id')) {
            $query->where('daerah_id', $this->request->daerah_id);
        }

        if ($this->request->filled('rw_id')) {
            $query->where('rw_id', $this->request->rw_id);
        }

        if ($this->request->filled('rt_id')) {
            $query->where('rt_id', $this->request->rt_id);
        }

        // Search hanya dijalankan jika semua wilayah dipilih
        if (
            $this->request->filled('search') &&
            $this->request->filled('daerah_id') &&
            $this->request->filled('rw_id') &&
            $this->request->filled('rt_id')
        ) {
            $query->where('nama', 'like', '%' . $this->request->search . '%');
        }

        // Filter jenis kelamin
        if ($this->request->filled('jenis_kelamin')) {
            $query->where('jenis_kelamin', $this->request->jenis_kelamin);
        }

        // Filter usia
        if ($this->request->filled('usia')) {
            $query->where(function ($q) {
                $usia = $this->request->usia;
                $now = Carbon::now();

                match ($usia) {
                    'Balita (< 5 Tahun)' => $q->where('tanggal_lahir', '>=', $now->copy()->subYears(5)),
                    'Anak-Anak (6 - 12 Tahun)' => $q->whereBetween('tanggal_lahir', [
                        $now->copy()->subYears(12),
                        $now->copy()->subYears(6),
                    ]),
                    'Remaja (13 - 17 Tahun)' => $q->whereBetween('tanggal_lahir', [
                        $now->copy()->subYears(17),
                        $now->copy()->subYears(13),
                    ]),
                    'Dewasa Muda (18 - 30 Tahun)' => $q->whereBetween('tanggal_lahir', [
                        $now->copy()->subYears(30),
                        $now->copy()->subYears(18),
                    ]),
                    'Dewasa Madya (31 - 59 Tahun)' => $q->whereBetween('tanggal_lahir', [
                        $now->copy()->subYears(59),
                        $now->copy()->subYears(31),
                    ]),
                    'Lansia (> 60 Tahun)' => $q->where('tanggal_lahir', '<=', $now->copy()->subYears(60)),
                    default => null,
                };
            });
        }

        // Filter agama
        if ($this->request->filled('agama')) {
            $query->where('agama', $this->request->agama);
        }

        // Filter pendidikan
        if ($this->request->filled('pendidikan')) {
            $pendidikan = $this->request->pendidikan;
            $pendidikanFixed = ['Belum Sekolah','SD', 'SLTP', 'SLTA', 'D3', 'S1', 'S2', 'S3'];

            if ($pendidikan === 'lainnya') {
                $query->whereNotIn('pendidikan', $pendidikanFixed);
            } else {
                $query->where('pendidikan', $pendidikan);
            }
        }

        // Filter pekerjaan
        if ($this->request->filled('pekerjaan')) {
            $pekerjaan = $this->request->pekerjaan;
            $pekerjaanFixed = [
                'Wiraswasta', 'Pegawai Swasta', 'Pegawai BUMN',
                'Ibu Rumah Tangga (IRT)', 'Buruh Lepas', 'Pelajar', 'Mahasiswa'
            ];

            if ($pekerjaan === 'lainnya') {
                $query->whereNotIn('pekerjaan', $pekerjaanFixed);
            } else {
                $query->where('pekerjaan', $pekerjaan);
            }
        }

        // Filter status pernikahan
        if ($this->request->filled('status_pernikahan')) {
            $query->where('status', $this->request->status_pernikahan);
        }

        return $query;
    }
}

// class PendudukFilter
// {
//     protected $request;

//     public function __construct(Request $request)
//     {
//         $this->request = $request;
//     }

//     public function apply(Builder $query): Builder
//     {
//         // Filter berdasarkan relasi ID
//         if ($this->request->filled('daerah_id')) {
//             $query->where('daerah_id', $this->request->daerah_id);
//         }

//         if ($this->request->filled('rw_id')) {
//             $query->where('rw_id', $this->request->rw_id);
//         }

//         if ($this->request->filled('rt_id')) {
//             $query->where('rt_id', $this->request->rt_id);
//         }

//         // Search hanya dijalankan jika semua wilayah dipilih
//         if (
//             $this->request->filled('search') &&
//             $this->request->filled('daerah_id') &&
//             $this->request->filled('rw_id') &&
//             $this->request->filled('rt_id')
//         ) {
//             $query->where('nama', 'like', '%' . $this->request->search . '%');
//         }

//         // Filter jenis kelamin
//         if ($this->request->filled('jenis_kelamin')) {
//             $query->where('jenis_kelamin', $this->request->jenis_kelamin);
//         }

//         // Filter usia
//         if ($this->request->filled('usia')) {
//             $query->where(function ($q) {
//                 $usia = $this->request->usia;
//                 $now = Carbon::now();

//                 match ($usia) {
//                     'Balita (< 5 Tahun)' => $q->where('tanggal_lahir', '>=', $now->copy()->subYears(5)),
//                     'Anak-Anak (6 - 12 Tahun)' => $q->whereBetween('tanggal_lahir', [
//                         $now->copy()->subYears(12),
//                         $now->copy()->subYears(6),
//                     ]),
//                     'Remaja (13 - 17 Tahun)' => $q->whereBetween('tanggal_lahir', [
//                         $now->copy()->subYears(17),
//                         $now->copy()->subYears(13),
//                     ]),
//                     'Dewasa Muda (18 - 30 Tahun)' => $q->whereBetween('tanggal_lahir', [
//                         $now->copy()->subYears(30),
//                         $now->copy()->subYears(18),
//                     ]),
//                     'Dewasa Madya (31 - 59 Tahun)' => $q->whereBetween('tanggal_lahir', [
//                         $now->copy()->subYears(59),
//                         $now->copy()->subYears(31),
//                     ]),
//                     'Lansia (> 60 Tahun)' => $q->where('tanggal_lahir', '<=', $now->copy()->subYears(60)),
//                     default => null,
//                 };
//             });
//         }

//         // Filter agama
//         if ($this->request->filled('agama')) {
//             $query->where('agama', $this->request->agama);
//         }

//         // Filter pendidikan
//         if ($this->request->filled('pendidikan')) {
//             $pendidikan = $this->request->pendidikan;
//             $pendidikanFixed = ['Belum Sekolah','SD', 'SLTP', 'SLTA', 'D3', 'S1', 'S2', 'S3'];

//             if ($pendidikan === 'lainnya') {
//                 $query->whereNotIn('pendidikan', $pendidikanFixed);
//             } else {
//                 $query->where('pendidikan', $pendidikan);
//             }
//         }

//         // Filter pekerjaan
//         if ($this->request->filled('pekerjaan')) {
//             $pekerjaan = $this->request->pekerjaan;
//             $pekerjaanFixed = [
//                 'Wiraswasta', 'Pegawai Swasta', 'Pegawai BUMN',
//                 'Ibu Rumah Tangga (IRT)', 'Buruh Lepas', 'Pelajar', 'Mahasiswa'
//             ];

//             if ($pekerjaan === 'lainnya') {
//                 $query->whereNotIn('pekerjaan', $pekerjaanFixed);
//             } else {
//                 $query->where('pekerjaan', $pekerjaan);
//             }
//         }

//         // Filter status pernikahan
//         if ($this->request->filled('status_pernikahan')) {
//             $query->where('status', $this->request->status_pernikahan);
//         }

//         return $query;
//     }
// }