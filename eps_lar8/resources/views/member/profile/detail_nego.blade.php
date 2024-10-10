<style>
    :root {
        --primary-color: #fc6703;
        /* Warna utama */
        --secondary-color: #fff0e6;
        /* Warna latar belakang sekunder */
        --text-color: #333333;
        /* Warna teks */
        --hover-color: #e55a00;
        /* Warna hover */
        --putih: #fff;
        /* Warna putih */
        --background-color: #efefef;
        /* Warna latar belakang */
        --samar-background-color: #f9f9f9;
        /* Warna latar belakang samar */
    }

    body {
        font-family: Arial, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        padding: 20px;
    }

    .negotiation-detail {
        background-color: var(--putih);
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin-top: 20px;
    }

    h3,
    h4 {
        color: var(--primary-color);
    }

    .product-item-nego {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
        background-color: var(--samar-background-color);
    }

    .product-item-nego img {
        width: 50px;
        height: 50px;
        margin-right: 10px;
        border-radius: 5px;
    }

    .negotiation-history th,
    .negotiation-history td {
        padding: 10px;
        text-align: left;
    }

    .negotiation-history {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .negotiation-history th {
        background-color: var(--primary-color);
        color: var(--putih);
    }

    .negotiation-history td {
        border: 1px solid #ccc;
    }

    .action-buttons {
        display: flex;
        gap: 5px;
        height: 100%;
    }

    .action-buttons button {
        width: 100%;
    }
</style>

<div class="negotiation-detail">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="card-title">Detail Nego</h3>
        <button class="btn btn-info rounded-box shadow-sm" onclick="backtomenu()" title="Kembali ke Menu Nego">
            <i class="material-icons">arrow_back</i>
        </button>
    </div>
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <strong>Nama Toko:</strong> {{ $nego->nama_toko }}<br>
            <strong>Status:</strong>{{ ucfirst(str_replace('_', ' ', $nego->status)) }}
        </div>
        <div>
            <strong>Tanggal Nego:</strong> {{ \Carbon\Carbon::parse($nego->created_date)->format('d-m-Y') }}
        </div>
    </div>

    <br>
    <h4>Produk yang Dinego</h4>
    <div class="product-item-nego d-flex align-items-center border rounded p-3 mb-3 bg-light shadow-sm">
        <img src="{{ $nego->image }}" alt="Produk Contoh" class="img-fluid rounded mr-3" />
        <div class="flex-grow-1">
            <h5 class="mb-1">Nama Produk: <span class="font-weight-bold">{{ $nego->nama_produk }}</span></h5>
            <div class="d-flex justify-content-between">
                <span><strong>Qty:</strong> {{ number_format($nego->qty) }}</span>
                <span><strong>Harga Awal:</strong> Rp {{ number_format($nego->harga_awal_satuan, 0, ',', '.') }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span><strong>Harga Nego:</strong> Rp {{ number_format($nego->harga_nego, 0, ',', '.') }}</span>
                <span><strong>Total Nego:</strong> Rp {{ number_format($nego->harga_nego * $nego->qty, 0, ',', '.') }}</span>
            </div>

        </div>
    </div>
</div>


<h4>History Nego</h4>
<table class="table negotiation-history">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Oleh</th>
            <th>Harga Tayang</th>
            <th>Harga Nego</th>
            <th>Harga Response</th>
            <th>Status</th>
            <th>Catatan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($nego->history as $n)
        @php
        if($n->status == 2){
        $n->status = 'Ditolak';
        } elseif($n->status == 1){
        $n->status = 'Diterima';
        } else{
        $n->status = 'Diajukan';
        }
        @endphp
        @if($n->send_by == 0)
        <tr>
            <td>{{ \Carbon\Carbon::parse($n->timestamp)->format('d-m-Y') }}</td>
            <td>Pembeli</td>
            <td>Rp {{ number_format($nego->harga_awal_satuan, 0, ',', '.') }}</td>
            <td>Rp {{ number_format($n->base_price, 0, ',', '.') }}</td>
            <td></td>
            <td>{{ $n->status }}</td>
            <td>{{ $n->catatan_pembeli }}</td>
            <td class="action-buttons">
                -
            </td>
        </tr>
        @else
        <tr>
            <td>{{ \Carbon\Carbon::parse($n->timestamp)->format('d-m-Y') }}</td>
            <td>Penjual</td>
            <td>Rp {{ number_format($nego->harga_awal_satuan, 0, ',', '.') }}</td>
            <td></td>
            <td>Rp {{ number_format($n->base_price, 0, ',', '.') }}</td>
            <td>{{ $n->status }}</td>
            <td>{{ $n->catatan_penjual }}</td>
            <td class="action-buttons">
                @if($n->status == 'Diajukan')
                <button class="btn btn-success" onclick="terimaNego({{ $nego->id }})">Terima</button>
                <button class="btn btn-danger" onclick="tolakNego({{ $nego->id }})">Tolak</button>
                <button class="btn btn-warning" onclick="negoulang({{ $n->base_price }} , {{ $n->id }})">Nego Ulang</button>
                @else
                -
                @endif
            </td>
        </tr>

        @endif
        @endforeach
    </tbody>
</table>

<script>
    function formatRupiah(angka) {
        var number_string = angka.toString().replace(/[^,\d]/g, ""),
            split = number_string.split(","),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? "." : "";
            rupiah += separator + ribuan.join(".");
        }

        rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
        return "Rp. " + rupiah;
    }

    function unformatRupiah(formattedRupiah) {
        var number_string = formattedRupiah.replace(/[^,\d]/g, "");
        return parseInt(number_string.replace(/[.,]/g, ""));
    }

    function parseRupiah(rupiahString) {
        return parseInt(rupiahString.replace(/[^0-9]/g, ""));
    }

    function terimaNego(id) {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menerima nego ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, terima!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: appUrl + '/api/member/nego/accNego?token=' + token,
                    type: 'POST',
                    data: {
                        id_nego: id
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Nego diterima dengan sukses!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        backtomenu();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menerima nego. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }


    function tolakNego(id) {
        // Use SweetAlert2 to show a prompt dialog for rejection reason
        Swal.fire({
            title: 'Alasan Pembatalan',
            input: 'text',
            inputLabel: 'Silakan masukkan alasan Anda',
            inputPlaceholder: 'Alasan pembatalan...',
            showCancelButton: true,
            confirmButtonText: 'Tolak',
            cancelButtonText: 'Batal',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Alasan tidak boleh kosong!');
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const reason = result.value;

                $.ajax({
                    url: appUrl + '/api/member/nego/tolak_nego?token=' + token,
                    type: 'POST',
                    data: {
                        id_nego: id,
                        reason: reason
                    },
                    success: function(response) {
                        Swal.fire({
                            title: 'Ditolak!',
                            text: 'Nego telah ditolak dengan alasan: ' + reason,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        backtomenu();
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error!',
                            text: 'Terjadi kesalahan saat menolak nego. Silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }

    function negoulang(price, id) {
        var qty = {{ $nego->qty }};
        var total_response = (price * qty);

        $('#id_nego').val({{ $nego->id }});
        $('#last_id').val(id);
        $('#qty').val(qty);
        $('#hargaResponseSatuan').val(formatRupiah(price));
        $('#hargaResponseTotal').val(formatRupiah(total_response));
        $('#negoUlangModal').modal('show');
    }

    function backtomenu() {
        const url = "{{ route('profile.nego') }}?token=" + token;
        loadContent(url, $('#contentArea'));
    }

    $('#hargaNegoSatuan').on('input', function() {
            var price =  unformatRupiah($('#hargaResponseSatuan').val());
            const quantity = $('#qty').val();
            const negoPrice = $(this).val().replace(/[^0-9]/g, '');
            if (negoPrice > price) {
                $(this).val(formatRupiah(price));
                var total = (quantity * price);
                $('#hargaNegoTotal').val(formatRupiah(total));
            } else {
                $(this).val(formatRupiah(negoPrice));
                var total = (quantity * negoPrice);
                $('#hargaNegoTotal').val(formatRupiah(total));
            }
        });
</script>