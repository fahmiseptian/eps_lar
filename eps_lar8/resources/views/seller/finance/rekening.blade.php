<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">

        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Rekening Bank</b></h3>
                        <br>
                        <div class="box box-info">
                            <div>
                                <div class="credit-card-container">
                                    <!-- Kartu Kredit Default -->
                                    <div class="credit-card">
                                        <div class="logo-and-bank">
                                            <div class="brand-logo"><img src="{{ asset('/img/app/logo eps.png') }}"
                                                    alt="Bank Logo" class="bank-logo"></div>
                                            <div class="bank-name">{{ $rekening->name }}</div>
                                            <span class="default-rek"></span>
                                            <div class="icon-container">
                                                <i class="fa fa-edit" data-id="{{ $rekening->id_rekening }}"
                                                    id="editRekening"></i>
                                                <i class="fa fa-trash-o" data-id="{{ $rekening->id_rekening }}"
                                                    id="hapusRekening"></i>
                                            </div>
                                        </div>
                                        <div class="card-info">
                                            <div class="card-number">** **** {{ substr($rekening->rek_number, -4) }}
                                            </div>
                                            <div class="card-location">{{ $rekening->rek_city }} &nbsp;
                                                {{ $rekening->rek_location }}</div>
                                            <div class="card-holder">{{ $rekening->rek_owner }}</div>
                                        </div>
                                    </div>
                                    <!-- Kartu Kredit Non-Default -->
                                    @foreach ($rekeningNotdefault as $rk)
                                        <div class="credit-card" >
                                            <div class="logo-and-bank">
                                                <div class="brand-logo" ><img src="{{ asset('/img/app/logo eps.png') }}"
                                                        alt="Bank Logo" class="bank-logo"></div>
                                                <div class="bank-name">{{ $rk->bank_name }}</div>
                                                <div class="icon-container">
                                                    <i class="fa fa-edit" data-id="{{ $rk->id }}"
                                                        id="editRekening"></i>
                                                    <i class="fa fa-trash-o" data-id="{{ $rk->id }}"
                                                        id="hapusRekening"></i>
                                                </div>
                                            </div>
                                            <div class="card-info" data-id="{{ $rk->id }}" id="editDefaultRekening">
                                                <div class="card-number">** **** {{ substr($rk->rek_number, -4) }}
                                                </div>
                                                <div class="card-location">{{ $rk->rek_city }} &nbsp;
                                                    {{ $rk->rek_location }}</div>
                                                <div class="card-holder">{{ $rk->rek_owner }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if ($jmlRekNdefault < 2)
                                        <div class="add-card">
                                            <i class="fa fa-plus" id="tambahRekening"></i>
                                        </div>
                                    @endif
                                </div>
                                &nbsp;
                                <p style="text-align: center; color:grey;">Setiap Toko Memiliki Maxsimal 3 Rekening
                                    Saja, Gunakan Sebijak Mungkin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
</body>
{{-- footer --}}
@include('seller.asset.footer')
<script>
    $(document).on("click", "#tambahRekening", function() {
        // Tampilkan modal menggunakan Swal.fire
        Swal.fire({
            title: "Tambah Rekening",
            html: `
            <form id="addRekeningForm">
                <div class="form-group">
                    <label for="nama">Nama Pemilik Rekening:</label>
                    <input type="text" id="nama" name="nama" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="bank">Bank:</label>
                    <select id="bank" name="bank" class="form-control" required>
                        @foreach ($Banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="noRekening">No. Rekening:</label>
                    <input type="text" id="noRekening" name="noRekening" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="cabangBank">Cabang Bank:</label>
                    <input type="text" id="cabangBank" name="cabangBank" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="kotaKabupaten">Kota/Kabupaten:</label>
                    <input type="text" id="kotaKabupaten" name="kotaKabupaten" class="form-control" required>
                </div>
            </form>
        `,
            showCancelButton: true,
            confirmButtonText: 'Tambah',
            cancelButtonText: 'Batal',
            focusConfirm: false,
            preConfirm: () => {
                // Ambil data dari form
                const form = document.getElementById("addRekeningForm");
                const formData = new FormData(form);

                // Periksa apakah semua bidang wajib diisi
                const inputs = form.querySelectorAll('input[required], select[required]');
                for (const input of inputs) {
                    if (input.value.trim() === "") {
                        Swal.showValidationMessage(
                            `Silakan isi bidang ${input.labels[0].textContent}`);
                        return false;
                    }
                }

                // Kirim data dengan metode POST ke controller
                return fetch("/seller/finance/addRekening", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
                        },
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Gagal menambahkan rekening.");
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Terjadi kesalahan: ${error.message}`);
                    });
            },
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire('Sukses', 'Rekening berhasil ditambahkan', 'success').then(() => {
                    // Refresh halaman setelah menutup modal Swal
                    window.location.reload();
                });
            }
        });
    });
</script>
<!-- page script -->
<script src="{{ asset('/js/function/seller/finance.js') }}" type="text/javascript"></script>

</html>
