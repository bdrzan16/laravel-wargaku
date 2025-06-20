@extends('layouts.master')

@section('title', 'Dashboard RW')
@section('text-welcome')
    Selamat Bekerja {{ Auth::user()->name }}
@endsection

@section('content')

    <!-- Load Chart.js sekali saja -->
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

        .chart-container {
            position: relative;
            width: 100%;
            height: 430px;
        }

        .card-header {
            background-color: #29B6A5 !important;
            color: #ffffff !important;
            text-align: center;
        }

        /* ===================================================== */
        /* UKURAN KHUSUS DEVICE KECIL */
        /* ===================================================== */

        /* Responsive layout di bawah 576px (HP) */
        @media (max-width: 576px) {
            #pendudukLineChart {
                max-width:500px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 220px;
            }

            .chart-container {
                position: relative;
                width: 100%;
                height: 250px;
            }

            #pieGender {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #pieAgama {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #piePendidikan {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #piePekerjaan {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #pieNikah {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #pieUsia {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
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
            #pendudukLineChart {
                max-width:500px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 300px;
            }

            .chart-container {
                position: relative;
                width: 100%;
                height: 300px;
            }

            #pieGender {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #pieAgama {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #piePendidikan {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #piePekerjaan {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #pieNikah {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
            #pieUsia {
                max-width: 320px;
                margin: 0 auto;
                padding: 0 10px;
                min-height: 200px;
            }
        }

        /* Responsive layout di bawah 768px - 991.98 (HP) */
        @media (min-width: 768px) and (max-width: 1199.98px) {
            #pendudukLineChart {
                max-width:550px;
                margin: 0 auto;
                padding: 0 10px;
            }

            .chart-container {
                position: relative;
                width: 100%;
                height: 350px;
            }

            .diagram-pie-card canvas {
                min-height: 170px !important; /* sebelumnya 220px */
                max-width: 100%;
            }

            .diagram-pie-card .card-body {
                display: flex;
                flex-direction: column;
                justify-content: center; /* Tengah vertikal */
                align-items: center;     /* Tengah horizontal */
                padding: 1rem;
            }

            .diagram-pie-card .card-header {
                font-size: 0.8rem; /* sedikit lebih kecil */
                padding: 0.5rem 1rem;
            }
        }
    </style>

    <!-- Grafik Line : Hitung Banyak Penduduk RW -->
    <div class="row mt-4 mb-4">
        <div class="col-12">
            <div class="card grafik-line-card mx-auto" style="max-width: 900px;">
                <div class="card-header text-center">Statistik Penduduk RW</div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="pendudukLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        const fontSize = window.innerWidth < 576 ? 10 : 12;

        const ctxLine = document.getElementById('pendudukLineChart').getContext('2d');
        const userLineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Jumlah Penduduk per Bulan',
                    data: @json($chartData),
                    borderColor: '#29B6A5',
                    backgroundColor: 'transparent',
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#29B6A5',
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
    </script>

    @include('partials.diagram-pie')
@endsection