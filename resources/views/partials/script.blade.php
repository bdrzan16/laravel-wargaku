<!-- Bootstrap core JavaScript-->
<script src="{{ asset('assets2/vendor/jquery/jquery.js') }}"></script>
<script src="{{ asset('assets2/vendor/bootstrap/js/bootstrap.bundle.js') }}"></script>
<script src="{{ asset('assets2/js/sb-admin-2.js') }}"></script>

<script>
    const storeUrl = "{{ route('penduduk.store') }}";
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // DOM ELEMENTS
        const pendidikanSelect = document.getElementById('pendidikanSelect');
        const pendidikanLainnya = document.getElementById('pendidikanLainnyaInput');
        const kepSelect = document.getElementById('kep_di_kelurahanSelect');
        const kepLainnya = document.getElementById('kep_di_kelurahanLainnyaInput');

        // ✅ Tambahkan ajaxSetup di sini
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fungsi toggle input 'Lainnya'
        function toggleLainnya(select, input) {
            if (select.value === 'Lainnya') {
                input.style.display = 'block';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
                input.value = '';
            }
        }

        // Inisialisasi saat halaman dimuat
        if (pendidikanSelect && pendidikanLainnya) {
            toggleLainnya(pendidikanSelect, pendidikanLainnya);
            pendidikanSelect.addEventListener('change', () => toggleLainnya(pendidikanSelect, pendidikanLainnya));
        }

        if (kepSelect && kepLainnya) {
            toggleLainnya(kepSelect, kepLainnya);
            kepSelect.addEventListener('change', () => toggleLainnya(kepSelect, kepLainnya));
        }
        // toggleLainnya(pendidikanSelect, pendidikanLainnya);
        // toggleLainnya(kepSelect, kepLainnya);

        // Event listener perubahan select
        // pendidikanSelect.addEventListener('change', () => toggleLainnya(pendidikanSelect, pendidikanLainnya));
        // kepSelect.addEventListener('change', () => toggleLainnya(kepSelect, kepLainnya));

        // ========= TAMBAH DATA =========
        $('#btnTambahPenduduk').on('click', function () {
            $('#formPenduduk')[0].reset();
            $('#formMethod').val('POST');
            $('#modalTitle').text('Tambah Data Penduduk');
            $('#submitButton').text('Simpan');
            $('#formPenduduk').attr('action', storeUrl);

            const userRole = "{{ auth()->user()->getRoleNames()->first() }}";

            if (userRole === 'admin') {
                // Admin: isi dari dropdown
                $('#hiddenDaerah').val($('#daerahInput').val());
                $('#hiddenRW').val($('#rwSelect').val());
                $('#hiddenRT').val($('#rtSelect').val());
            } else if (userRole === 'rw') {
                // RW: rt_id dari filter request, lainnya dari user login
                $('#hiddenDaerah').val("{{ auth()->user()->daerah_id }}");
                $('#hiddenRW').val("{{ auth()->user()->rw_id }}");
                $('#hiddenRT').val("{{ request('rt_id') }}");
            } else if (userRole === 'rt') {
                // RT: semua dari user login
                $('#hiddenDaerah').val("{{ auth()->user()->daerah_id }}");
                $('#hiddenRW').val("{{ auth()->user()->rw_id }}");
                $('#hiddenRT').val("{{ auth()->user()->rt_id }}");
            }

            $('#readonlyWilayah').hide();

            // Sembunyikan field "Lainnya"
            pendidikanLainnya.style.display = 'none';
            pendidikanLainnya.value = '';
            kepLainnya.style.display = 'none';
            kepLainnya.value = '';

            $('#formPendudukModal').modal('show');
        });

        $('#btnTambahPendudukMobile').on('click', function () {
            $('#formPenduduk')[0].reset();
            $('#formMethod').val('POST');
            $('#modalTitle').text('Tambah Data Penduduk');
            $('#submitButton').text('Simpan');
            $('#formPenduduk').attr('action', storeUrl);

            const userRole = "{{ auth()->user()->getRoleNames()->first() }}";

            if (userRole === 'admin') {
                // Admin: isi dari dropdown
                $('#hiddenDaerah').val($('#daerahInput').val());
                $('#hiddenRW').val($('#rwSelect').val());
                $('#hiddenRT').val($('#rtSelect').val());
            } else if (userRole === 'rw') {
                // RW: rt_id dari filter request, lainnya dari user login
                $('#hiddenDaerah').val("{{ auth()->user()->daerah_id }}");
                $('#hiddenRW').val("{{ auth()->user()->rw_id }}");
                $('#hiddenRT').val("{{ request('rt_id') }}");
            } else if (userRole === 'rt') {
                // RT: semua dari user login
                $('#hiddenDaerah').val("{{ auth()->user()->daerah_id }}");
                $('#hiddenRW').val("{{ auth()->user()->rw_id }}");
                $('#hiddenRT').val("{{ auth()->user()->rt_id }}");
            }

            $('#readonlyWilayah').hide();

            // Sembunyikan field "Lainnya"
            pendidikanLainnya.style.display = 'none';
            pendidikanLainnya.value = '';
            kepLainnya.style.display = 'none';
            kepLainnya.value = '';

            $('#formPendudukModal').modal('show');
        });

        // ========= EDIT DATA =========
        $('.btn-edit-penduduk').on('click', function () {
            const data = $(this).data();

            $('#no_nik').val(data.no_nik);
            $('#nama').val(data.nama);
            $('#jenis_kelamin').val(data.jenis_kelamin);
            $('#tempat_lahir').val(data.tempat_lahir);
            $('#tanggal_lahir').val(data.tanggal_lahir);
            $('#status').val(data.status);
            $('#agama').val(data.agama);
            $('#pekerjaan').val(data.pekerjaan);
            $('#alamat').val(data.alamat);
            $('#tgl_mulai').val(data.tgl_mulai);

            // Pendidikan
            if ($('#pendidikanSelect option[value="' + data.pendidikan + '"]').length > 0) {
                $('#pendidikanSelect').val(data.pendidikan).trigger('change');
                pendidikanLainnya.style.display = 'none';
                pendidikanLainnya.value = '';
            } else {
                $('#pendidikanSelect').val('Lainnya').trigger('change');
                pendidikanLainnya.style.display = 'block';
                pendidikanLainnya.value = data.pendidikan_lainnya || data.pendidikan;
            }

            // Kep di Kelurahan
            if ($('#kep_di_kelurahanSelect option[value="' + data.kep_di_kelurahan + '"]').length > 0) {
                $('#kep_di_kelurahanSelect').val(data.kep_di_kelurahan).trigger('change');
                kepLainnya.style.display = 'none';
                kepLainnya.value = '';
            } else {
                $('#kep_di_kelurahanSelect').val('Lainnya').trigger('change');
                kepLainnya.style.display = 'block';
                kepLainnya.value = data.kep_di_kelurahan_lainnya || data.kep_di_kelurahan;
            }

            // Wilayah readonly
            $('#daerahReadonly').val(data.daerah);
            $('#rwReadonly').val(data.rw_nama);
            $('#rtReadonly').val(data.rt_nama);
            $('#readonlyWilayah').show();

            $('#formPenduduk').attr('action', `/data-penduduk/${data.id}`);
            console.log('Action set to:', $('#formPenduduk').attr('action')); // cek hasilnya
            $('#formMethod').val('PUT');
            $('#modalTitle').text('Edit Data Penduduk');
            $('#submitButton').text('Update');

            $('#formPendudukModal').modal('show');
        });

        // ========= SUBMIT FORM (AJAX) =========
        $('#formPenduduk').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const actionUrl = form.attr('action');
            const method = $('#formMethod').val();
            const formData = form.serialize();

            $.ajax({
                url: actionUrl,
                type: 'POST', // ← pakai POST saja
                data: formData,
                headers: {
                    'X-HTTP-Method-Override': method // ← override jadi PUT jika perlu
                },
                // type: method,
                // data: formData,
                success: function () {
                    $('#formPendudukModal').modal('hide');
                    form[0].reset();
                    location.reload();
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    alert('Terjadi kesalahan saat menyimpan data.');
                }
            });
        });
    });
</script>

@if ($errors->any())
    <script>
        $(document).ready(function() {
            $('#formPendudukModal').modal('show');
        });
    </script>
@endif

<!-- Delete -->
<script>
    $('.btn-delete-penduduk').on('click', function () {
        const id = $(this).data('id');
        const nama = $(this).data('nama');
        const url = "{{ route('penduduk.destroy', ':id') }}".replace(':id', id);
        // const url = `/data-penduduk/${id}`; // Atau pakai route JS jika ada

        $('#formDeletePenduduk').attr('action', url);
        $('#namaPendudukHapus').text(nama);
        $('#modalDeletePenduduk').modal('show');
    });
</script>

<!-- Ubah Password (Icon Eye) -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(function (el) {
            el.addEventListener('click', function () {
                const input = document.querySelector(this.getAttribute('toggle'));
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    });
</script>

<script>
    const routeGetRW = "{{ route('get.rw.by.daerah') }}";
    const routeGetRT = "{{ route('get.rt.by.daerah.rw') }}";
</script>

<!-- Form Sort Daerah, RW & RT -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const daerahInput = document.getElementById('daerahInput');
        const rwSelect = document.getElementById('rwSelect');
        const rtSelect = document.getElementById('rtSelect');
        const btnTambah = document.getElementById('btnTambahPenduduk');
        const btnFilter = document.getElementById('btnFilterTambahan');
        const filterForm = document.getElementById('filterForm');

        function fetchRWRT(daerahId, selectedRW = '', selectedRT = '') {
            if (!daerahInput || !rwSelect || !rtSelect) return;

            if (daerahId) {
                fetch(`${routeGetRW}?daerah_id=${encodeURIComponent(daerahId)}`)
                    .then(res => res.json())
                    .then(data => {
                        rwSelect.innerHTML = `<option value="">Pilih RW</option>`;
                        data.forEach(item => {
                            const selected = (item.id == selectedRW) ? 'selected' : '';
                            rwSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.name.padStart(2, '0')}</option>`;
                        });
                        rwSelect.disabled = false;

                        if (selectedRW) {
                            fetch(`${routeGetRT}?rw_id=${selectedRW}`)
                                .then(res => res.json())
                                .then(data => {
                                    rtSelect.innerHTML = `<option value="">Pilih RT</option>`;
                                    data.forEach(item => {
                                        const selected = (item.id == selectedRT) ? 'selected' : '';
                                        rtSelect.innerHTML += `<option value="${item.id}" ${selected}>${item.name.padStart(2, '0')}</option>`;
                                    });
                                    rtSelect.disabled = false;
                                    toggleButtons();
                                });
                        } else {
                            rtSelect.disabled = true;
                            rtSelect.innerHTML = `<option value="">Pilih RT</option>`;
                            toggleButtons();
                        }
                    });
            } else {
                rwSelect.innerHTML = `<option value="">Pilih RW</option>`;
                rwSelect.disabled = true;
                rtSelect.innerHTML = `<option value="">Pilih RT</option>`;
                rtSelect.disabled = true;
                toggleButtons();
            }
        }

        function toggleButtons() {
            const daerahSelected = daerahInput?.value !== '';
            const rwSelected = rwSelect?.value !== '';
            const rtSelected = rtSelect?.value !== '';

            if (btnFilter) {
                btnFilter.classList.toggle('d-none', !daerahSelected);
            }

            if (btnTambah) {
                btnTambah.classList.toggle('d-none', !(daerahSelected && rwSelected && rtSelected));
            }
        }

        function cekDanSubmitOtomatis() {
            if (daerahInput && rwSelect && rtSelect) {
                if (daerahInput.value && rwSelect.value && rtSelect.value && filterForm) {
                    filterForm.submit();
                }
            }
        }

        if (daerahInput && rwSelect && rtSelect) {
            const daerahId = daerahInput.value;
            const selectedRW = rwSelect.getAttribute('data-selected');
            const selectedRT = rtSelect.getAttribute('data-selected');
            fetchRWRT(daerahId, selectedRW, selectedRT);
        }

        toggleButtons();

        daerahInput?.addEventListener('change', function () {
            fetchRWRT(this.value);
        });

        rwSelect?.addEventListener('change', function () {
            const rwId = this.value;
            if (rwId) {
                fetch(`${routeGetRT}?rw_id=${rwId}`)
                    .then(res => res.json())
                    .then(data => {
                        rtSelect.innerHTML = `<option value="">Pilih RT</option>`;
                        data.forEach(item => {
                            rtSelect.innerHTML += `<option value="${item.id}">${item.name.padStart(2, '0')}</option>`;
                        });
                        rtSelect.disabled = false;
                        toggleButtons();
                    });
            } else {
                rtSelect.innerHTML = `<option value="">Pilih RT</option>`;
                rtSelect.disabled = true;
                toggleButtons();
            }
        });

        rtSelect?.addEventListener('change', function () {
            toggleButtons();
            cekDanSubmitOtomatis();
        });

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("sidebarToggleTop");
        const sidebar = document.getElementById("accordionSidebar");
        const overlay = document.getElementById("sidebarOverlay");

        toggleBtn.addEventListener("click", function (e) {
            e.preventDefault();

            // Toggle class SB Admin 2 (`toggled`) + custom (`active`)
            sidebar.classList.toggle("active");
            sidebar.classList.toggle("toggled");
            overlay.classList.toggle("active");
        });

        overlay.addEventListener("click", function () {
            sidebar.classList.remove("active");
            sidebar.classList.remove("toggled");
            overlay.classList.remove("active");
        });
    });
</script>