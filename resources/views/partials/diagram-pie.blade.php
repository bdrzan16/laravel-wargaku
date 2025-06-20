<!-- Diagram Pie: Hitung Banyak Penduduk Sesuai Kriteria -->
<div class="container px-0" style="max-width: 900px;">
    <div class="row">
        
        <!-- Jenis Kelamin -->
        <div class="col-md-4 mb-4">
            <div class="card diagram-pie-card h-100">
                <div class="card-header text-center">Jenis Kelamin</div>
                <div class="card-body">
                    <canvas id="pieGender" width="250" height="250"></canvas>
                </div>
            </div>
        </div>
    
        <!-- Agama -->
        <div class="col-md-4 mb-4">
            <div class="card diagram-pie-card h-100">
                <div class="card-header text-center">Agama</div>
                <div class="card-body">
                    <canvas id="pieAgama" width="250" height="250"></canvas>
                </div>
            </div>
        </div>
    
        <!-- Pendidikan -->
        <div class="col-md-4 mb-4">
            <div class="card diagram-pie-card h-100">
                <div class="card-header text-center">Pendidikan</div>
                <div class="card-body">
                    <canvas id="piePendidikan" width="250" height="250"></canvas>
                </div>
            </div>
        </div>
    
        <!-- Pekerjaan -->
        <div class="col-md-4 mb-4">
            <div class="card diagram-pie-card h-100">
                <div class="card-header text-center">Pekerjaan</div>
                <div class="card-body">
                    <canvas id="piePekerjaan" width="250" height="250"></canvas>
                </div>
            </div>
        </div>
    
        <!-- Status Perkawinan -->
        <div class="col-md-4 mb-4">
            <div class="card diagram-pie-card h-100">
                <div class="card-header text-center">Status Pernikahan</div>
                <div class="card-body">
                    <canvas id="pieNikah" width="250" height="250"></canvas>
                </div>
            </div>
        </div>
    
        <!-- Usia -->
        <div class="col-md-4 mb-4">
            <div class="card diagram-pie-card h-100">
                <div class="card-header text-center">Usia</div>
                <div class="card-body">
                    <canvas id="pieUsia" width="250" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function renderPieChart(canvasId, labels, data, colors) {
        const ctxPie = document.getElementById(canvasId).getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
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
    }

    // Fungsi buat warna acak
    function generateColors(count) {
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push('hsl(' + (i * 360 / count) + ', 60%, 60%)');
        }
        return colors;
    }

    // Pie Data dari backend
    const pieData = @json($pieData);
    renderPieChart('pieGender', Object.keys(pieData.jenis_kelamin), Object.values(pieData.jenis_kelamin), generateColors(Object.keys(pieData.jenis_kelamin).length));
    renderPieChart('pieAgama', Object.keys(pieData.agama), Object.values(pieData.agama), generateColors(Object.keys(pieData.agama).length));
    renderPieChart('piePendidikan', Object.keys(pieData.pendidikan), Object.values(pieData.pendidikan), generateColors(Object.keys(pieData.pendidikan).length));
    renderPieChart('piePekerjaan', Object.keys(pieData.pekerjaan), Object.values(pieData.pekerjaan), generateColors(Object.keys(pieData.pekerjaan).length));
    renderPieChart('pieNikah', Object.keys(pieData.status), Object.values(pieData.status), generateColors(Object.keys(pieData.status).length));
    renderPieChart('pieUsia', Object.keys(pieData.usia), Object.values(pieData.usia), generateColors(Object.keys(pieData.usia).length));
</script>