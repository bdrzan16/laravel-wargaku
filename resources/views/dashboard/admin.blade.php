@extends('layouts.master')

@section('title', 'Dashboard Admin')
@section('text-welcome')
    Hai {{ Auth::user()->name }}
@endsection

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* ===================================================== */
        /* UKURAN KHUSUS DEVICE DESKTOP */
        /* ===================================================== */

        /* Atur default canvas */
        canvas {
            width: 100% !important;
            height: auto !important;
            min-height: 250px;
        }

        .daerah-progress {
            height: 20px;
        }

        .daerah-nama,
        .daerah-jumlah {
            font-size: 1rem;
        }

        .card-header {
            background-color: #29B6A5 !important;
            color: #ffffff !important;
        }

        /* ===================================================== */
        /* UKURAN KHUSUS DEVICE KECIL */
        /* ===================================================== */

        /* Responsive layout di bawah 576px (HP) */
        @media (max-width: 576px) {
            #userPieChart {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #userLineChart {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }

            .small-font {
                font-size: 0.8rem;
            }

            .progress {
                height: 16px !important;
            }

            .daerah-progress {
                height: 14px !important;
            }

            .daerah-nama,
            .daerah-jumlah {
                font-size: 0.8rem !important;
            }

            .modal-dialog {
                max-width: 70%; /* modal lebih ramping dari lebar layar */
                margin: 1.5rem auto;
            }

            .modal-content {
                max-height: 90vh;
                overflow-y: auto;
                border-radius: 0.75rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            }

            .modal-header {
                background-color: #29B6A5; /* warna hijau toska */
                color: white;
            }

            .modal-title {
                font-size: 1rem;
                font-weight: 600;
            }

            .close {
                font-size: 1.25rem;
                color: white;
            }

            .modal-body label {
                font-size: 0.8rem;
                margin-bottom: 0.25rem;
            }

            .modal-body select.form-control {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                height: calc(1.5em + 0.75rem + 2px);
            }

            .modal-body .form-group {
                margin-bottom: 0.75rem;
            }

            body.modal-open {
                overflow: hidden;
            }
        }

        /* Responsive layout di bawah 576px - 768 (HP) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .daerah-progress {
                height: 14px !important;
            }

            .daerah-nama,
            .daerah-jumlah {
                font-size: 1rem !important;
            }
        }

        /* Responsive layout di bawah 768px - 991.98 (HP) */
        @media (min-width: 768px) and (max-width: 1199.98px) {
            .grafik-line-card canvas,
            .diagram-pie-card canvas {
                min-height: 170px !important; /* sebelumnya 220px */
                max-width: 100%;
            }

            .grafik-line-card .card-body,
            .diagram-pie-card .card-body {
                display: flex;
                flex-direction: column;
                justify-content: center; /* Tengah vertikal */
                align-items: center;     /* Tengah horizontal */
                padding: 1rem;
            }

            .grafik-line-card .card-header,
            .diagram-pie-card .card-header {
                font-size: 1rem; /* sedikit lebih kecil */
                padding: 0.5rem 1rem;
            }

            .daerah-progress {
                height: 14px !important;
            }

            .daerah-nama,
            .daerah-jumlah {
                font-size: 0.9rem !important;
            }

            .daerah .card-header {
                font-size: 1rem; /* sedikit lebih kecil */
            }
        }
    </style>

    <div class="row">
        <!-- Grafik Line -->
        <div class="col-md-8 mb-4">
            <div class="card h-100 shadow grafik-line-card">
                <div class="card-header font-weight-bold text-primary">
                    Data Pengguna RT & RW
                </div>
                <div class="card-body">
                    <canvas id="userLineChart" class="w-100"></canvas>
                </div>
            </div>
        </div>

        <!-- Diagram Pie -->
        <div class="col-md-4 mb-4">
            <div class="card h-100 d-flex flex-column justify-content-end shadow diagram-pie-card">
                <div class="card-header font-weight-bold text-primary">
                    Persentase User RW vs RT
                </div>
                <div class="card-body">
                    <canvas id="userPieChart" class="w-100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bar Daerah -->
    <div class="row">
        <div class="col-12">
            <div class="card daerah shadow mb-4">
                <div class="card-header font-weight-bold text-center text-primary">
                    5 Daerah dengan Penduduk Terbanyak
                </div>
                <div class="card-body">
                    @foreach ($topDaerahs as $item)
                    <div class="mb-3 daerah-item">
                        <div class="d-flex justify-content-between">
                            <strong class="daerah-nama">{{ $item->daerah->name }}</strong>
                            <span class="daerah-jumlah">{{ $item->jumlah }} penduduk</span>
                        </div>
                        <div class="progress daerah-progress">
                            <div class="progress-bar bg-primary"
                                role="progressbar"
                                style="width: {{ ($item->jumlah / $totalMax) * 100 }}%"
                                aria-valuenow="{{ $item->jumlah }}"
                                aria-valuemin="0" aria-valuemax="{{ $totalMax }}">
                                {{ number_format(($item->jumlah / $totalMax) * 100, 0) }}%
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        const fontSize = window.innerWidth < 576 ? 10 : 12;

        const ctxLine = document.getElementById('userLineChart').getContext('2d');
        const userLineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Pendaftaran User RW/RT',
                    data: @json($chartData),
                    borderColor: '#4e73df',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#4e73df',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            font: {
                                size: fontSize // kecilkan font bulan di HP
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        suggestedMax: Math.max(...@json($chartData)) + 1,
                        ticks: {
                            font: {
                                size: fontSize // kecilkan font angka di HP
                            }
                        }
                    }
                }
            }
        });

        const ctxPie = document.getElementById('userPieChart').getContext('2d');
        const userPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: ['User RW', 'User RT'],
                datasets: [{
                    data: [{{ $pieData['RW'] }}, {{ $pieData['RT'] }}],
                    backgroundColor: ['#4e73df', '#1cc88a'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: {
                                size: window.innerWidth < 768 ? 10 : 12 // Perkecil font jika layar sempit
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection