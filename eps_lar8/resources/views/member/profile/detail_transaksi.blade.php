<style>
    .header-detail-trx {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: -10px;
    }

    .header-detail-trx>p {
        color: #007bff;
        font-weight: bold;
        margin-right: 20px;
    }

    .btn-back {
        padding: 8px 16px;
        border: none;
        border-radius: 8px;
        color: white;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s, transform 0.2s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .btn-transaksi {
        background-color: #007bff;
    }

    .btn-upload {
        background-color: #28a745;
    }

    .btn-back:hover {
        opacity: 0.9;
        transform: translateY(-2px);
    }

    .btn-back:active {
        transform: translateY(0);
    }

    .transaction-item {
        background-color: red;
        border: solid 1px black;
        color: white;
    }
</style>

<div class="detail-transaksi-container">

    @php
    $subtotal_non_ppn = 0;
    $subtotal_sebelum_ppn = 0;
    $subtotal_ongkir_sebelum_ppn = 0;
    $subtotal_asuransi_sebelum_ppn = 0;
    $biaya_penganan_sebelum_ppn = 0;
    $total_ppn = 0;
    $total_transaksi = 0;
    $payment = '' ;
    $payment_method = null;
    $va_number = null;
    $id_cart = null;
    @endphp

    @php
    $firstTransaction = $transactions[0] ?? null;

    if ($firstTransaction) {
    $payment = $firstTransaction['detailOrder']->status_pembayaran_top;

    $bukti_transfer = $firstTransaction['detailOrder']->bukti_transfer;

    if ($firstTransaction['detailOrder']->status_pembayaran_top == '1' && $firstTransaction['detailOrder']->bukti_transfer != null) {
    $payment = 'Lunas';
    } elseif ($firstTransaction['detailOrder']->status_pembayaran_top == '0' && $firstTransaction['detailOrder']->bukti_transfer != null) {
    $payment = 'Menunggu_Pengecekan_Pembayaran';
    } else {
    $payment = 'Belum_Bayar';
    }
    } else {
    $payment = 'Tidak ada transaksi'; // Atau nilai default lainnya jika tidak ada transaksi
    }
    @endphp

    <div class="header-detail-trx">
        <h2 class="transaction-title">Detail Transaksi</h2>
        <p>{{ ucfirst(str_replace('_', ' ', $payment)) }}</p>
    </div>

    @if(!empty($transactions))
    @foreach($transactions as $transaction)
    @php
    $subtotal_non_ppn += $transaction['total_barang_tanpa_PPN'];
    $subtotal_sebelum_ppn += $transaction['total_barang_dengan_PPN'];
    $subtotal_ongkir_sebelum_ppn += $transaction['detailOrder']->sum_shipping;
    $subtotal_asuransi_sebelum_ppn += $transaction['detailOrder']->insurance_nominal;
    $biaya_penganan_sebelum_ppn += $transaction['detailOrder']->handling_cost_non_ppn;
    $total_ppn +=($transaction['detailOrder']->ppn_price + $transaction['detailOrder']->ppn_shipping);
    $total_transaksi += $transaction['detailOrder']->total;

    $payment = $transaction['detailOrder']->status_pembayaran_top;


    if ($transaction['detailOrder']->status_pembayaran_top == '1' && $transaction['detailOrder']->bukti_transfer != null) {
    $payment = 'Lunas';
    } elseif ($transaction['detailOrder']->status_pembayaran_top == '0' && $transaction['detailOrder']->bukti_transfer != null) {
    $payment = 'Menunggu_Pengecekan_Pembayaran';
    } else {
    $payment = 'Belum_Bayar';
    }

    $payment_method = $transaction['detailOrder']->pembayaran;
    $va_number = $transaction['detailOrder']->va_number;

    $status = $transaction['detailOrder']->status;
    $note = $transaction['detailOrder']->note ? $transaction['detailOrder']->note : $transaction['detailOrder']->note_seller;

    $id_cart = $transaction['detailOrder']->id_cart;
    @endphp

    @if($status == 'Pesanan_Dibatalkan')
    <div class="transaction-item cancelled">
        <b>Pesanan dibatalkan </b>
        <p>
            alasan pembatalan {{$note}}
        </p>
    </div>
    @endif

    <div class="transaction-block">
        <div class="transaction-info">
            <div class="info-section detail-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Pemohon :</span>
                        <span class="value">{{ $transaction['detailOrder']->nama }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">NPWP:</span>
                        <span class="value">{{ $transaction['detailOrder']->npwp }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Departemen:</span>
                        <span class="value">{{ $transaction['detailOrder']->satker }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Nama Penjual :</span>
                        <span class="value">{{ $transaction['detailOrder']->nama_seller }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">NPWP:</span>
                        <span class="value">{{ $transaction['detailOrder']->npwp_seller }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Pesan ke Penjual:</span>
                        <span class="value">{{ $transaction['detailOrder']->pesan_seller }}</span>
                    </div>
                </div>
            </div>

            <div class="info-section order-info">
                <h4>Informasi Pesanan</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Invoice:</span>
                        <span class="value">{{ $transaction['detailOrder']->invoice }} - {{ $transaction['detailOrder']->id_cart_shop }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Status:</span>
                        <span class="value status-badge status-{{$transaction['detailOrder']->status}}">
                            {{ ucwords(str_replace('_', ' ', $transaction['detailOrder']->status)) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Tanggal Pemesanan:</span>
                        <span class="value">{{ \Carbon\Carbon::parse($transaction['detailOrder']->created_date)->format('d M Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Metode Pembayaran:</span>
                        <span class="value">{{ $transaction['detailOrder']->pembayaran }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">TOP:</span>
                        <span class="value">{{ $transaction['detailOrder']->jml_top }} Hari</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Untuk Keperluan:</span>
                        <span class="value">{{ $transaction['detailOrder']->keperluan }}</span>
                    </div>
                </div>
            </div>

            <div class="info-section shipping-info">
                <h4>Informasi Pengiriman</h4>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Nama Penerima:</span>
                        <span class="value">{{ $transaction['detailOrder']->address_name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">No. Telepon:</span>
                        <span class="value">{{ $transaction['detailOrder']->phone }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Alamat Pengiriman:</span>
                        <span class="value">{{ $transaction['detailOrder']->address }}, {{ $transaction['detailOrder']->subdistrict_name }}, {{ $transaction['detailOrder']->city }}, {{ $transaction['detailOrder']->province_name }} {{ $transaction['detailOrder']->postal_code }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Kurir:</span>
                        <span class="value">{{ $transaction['detailOrder']->service }} (Estimasi {{ $transaction['detailOrder']->etd }} hari)</span>
                    </div>
                    <div class="info-item">
                        <span class="label">No Resi:</span>
                        <span class="value" id="no_resi">
                            @if($transaction['detailOrder']->no_resi)
                            {{ $transaction['detailOrder']->no_resi }}
                            @elseif($transaction['detailOrder']->status == 'Packing')
                            Pesanan Sedang Dikemas
                            @elseif($transaction['detailOrder']->status == 'Menunggu_Konfirmasi_Penjual')
                            Menunggu Konfirmasi Penjual
                            @else
                            Pesanan Belum Dikirim
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="label">Alamat Penagihan:</span>
                        <span class="value">{{ $transaction['billing']->address }}, {{ $transaction['billing']->subdistrict_name }}, {{ $transaction['billing']->city }}, {{ $transaction['billing']->province_name }} {{ $transaction['billing']->postal_code }}</span>
                    </div>
                </div>
            </div>
            @if($member->id_member_type == 3)
            <div class="info-section">
                <div class="action-buttons-detail-trx">
                    @if($transaction['detailOrder']->no_resi != '')
                    <button class="action-btn-detail-trx btn-primary lacakResi" data-resi="{{ $transaction['detailOrder']->no_resi }}" data-id_courier="{{ $transaction['detailOrder']->id_courier }}" data-id="{{ $transaction['detailOrder']->id_cart_shop }}">
                        <i class="material-icons">local_shipping</i>
                        <span>Lacak</span>
                    </button>
                    @endif
                    <button class="action-btn-detail-trx btn-success cetakKwitansi" data-id="{{ $transaction['detailOrder']->id_cart_shop }}" data-id_shop="{{ $transaction['detailOrder']->id_shop }}">
                        <i class="material-icons">receipt</i>
                        <span>Kwitansi</span>
                    </button>
                    <button class="action-btn-detail-trx btn-info cetakInvoice" data-id="{{ $transaction['detailOrder']->id_cart_shop }}" data-id_shop="{{ $transaction['detailOrder']->id_shop }}">
                        <i class="material-icons">description</i>
                        <span>Invoice</span>
                    </button>
                    <button class="action-btn-detail-trx btn-warning openKontrak" data-id="{{ $transaction['detailOrder']->id_cart_shop }}">
                        <i class="material-icons">assignment</i>
                        <span>Kontrak</span>
                    </button>
                    @if($transaction['detailOrder']->pmk == 59)
                    <button class="action-btn-detail-trx btn-danger openSuratPesanan" data-id="{{ $transaction['detailOrder']->id_cart_shop }}">
                        <i class="material-icons">mail</i>
                        <span>Surat Pesanan</span>
                    </button>
                    @if($transaction['detailOrder']->file_pajak != null)
                    <button class="action-btn-detail-trx btn-secondary">
                        <i class="material-icons">receipt_long</i>
                        <span>Faktur Pajak</span>
                    </button>
                    @endif
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="product-list-detail-trx">
            <h4 class="product-list-title-detail-trx">Produk</h4>
            @foreach($transaction['produk'] as $product)
            @php
            $requiresBaseUrl = strpos($product->image, 'http') === false;
            $pc_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $product->image : $product->image;
            @endphp
            <div class="product-item-detail-trx">
                <div class="product-image-container-detail-trx">
                    <img src="{{ $pc_image }}" alt="{{ $product->nama }}" class="product-image-detail-trx">
                </div>
                <div class="product-info-detail-trx">
                    <div class="product-details-detail-trx">
                        <h5 class="product-name">{{ $product->nama }} {{ $product->val_ppn == 0 ? ' (Tidak Kena PPN)' : '' }}</h5>
                        <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <p class="product-quantity">Jumlah: {{ $product->qty }}</p>
                    </div>
                    <div class="product-total-detail-trx">
                        <span class="label">Total:</span>
                        <span class="value">Rp {{ number_format($product->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="product-item-detail-trx">
                <div class="product-image-container-detail-trx">
                    <i class="material-icons" style="font-size: 48px; display: flex; justify-content: center; align-items: center; margin:auto;">local_shipping</i>
                </div>
                <div class="product-info-detail-trx">
                    <div class="product-details-detail-trx">
                        <h5 class="product-name">{{ $transaction['detailOrder']->deskripsi }}</h5>
                        <p class="product-price">{{$transaction['detailOrder']->service }}</p>
                    </div>
                    <div class="product-total-detail-trx">
                        <span class="label">Total:</span>
                        @php
                        $ongkir = $transaction['detailOrder']->sum_shipping + $transaction['detailOrder']->insurance_nominal + $transaction['detailOrder']->ppn_shipping;
                        @endphp
                        <span class="value">Rp {{ number_format($ongkir, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="transaction-summary">
            <h4>Ringkasan Pembayaran</h4>
            <div class="summary-item">
                <span class="label">Subtotal produk tanpa PPN:</span>
                <span class="value">Rp {{ number_format($transaction['total_barang_tanpa_PPN'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal produk sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['total_barang_dengan_PPN'], 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal Ongkos Kirim sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->sum_shipping, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal Asuransi Pengiriman sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->insurance_nominal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Biaya Penanganan sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->handling_cost_non_ppn, 0, ',', '.') }}</span>
            </div>
            @if($transaction['detailOrder']->pmk != 59)
            <div class="summary-item">
                <span class="label">PPN:</span>
                <span class="value">Rp {{ number_format(($transaction['detailOrder']->ppn_price + $transaction['detailOrder']->ppn_shipping), 0, ',', '.') }}</span>
            </div>
            @elseif ($transaction['detailOrder']->pmk == 59)
            <div class="summary-item">
                <span class="label">PPn:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->ppn_price, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Total Transaksi:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">PPh:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-item total">
                <span class="label">Subtotal:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->total, 0, ',', '.') }}</span>
            </div>
        </div>
        @if ($transaction['detailOrder']->pmk == 59 && $member->id_member_type == 3)
        <button class="btn-back btn-transaksi" onclick="uploadPajak('{{ $transaction['detailOrder']->id_cart_shop }}')">Upload Surat Setor Pajak</button>
        @endif
    </div>
    @endforeach
    <hr>
    <div class="transaction-summary">
        <h4>Total Transaksi</h4>
        <div class="summary-item">
            <span class="label">Subtotal produk tanpa PPN:</span>
            <span class="value">Rp {{ number_format($subtotal_non_ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Subtotal produk sebelum PPN:</span>
            <span class="value">Rp {{ number_format($subtotal_sebelum_ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Subtotal Ongkos Kirim sebelum PPN:</span>
            <span class="value">Rp {{ number_format($subtotal_ongkir_sebelum_ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Subtotal Asuransi Pengiriman sebelum PPN:</span>
            <span class="value">Rp {{ number_format($subtotal_asuransi_sebelum_ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="label">Biaya Penanganan sebelum PPN:</span>
            <span class="value">Rp {{ number_format($biaya_penganan_sebelum_ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item">
            <span class="label">PPN:</span>
            <span class="value">Rp {{ number_format($total_ppn, 0, ',', '.') }}</span>
        </div>
        <div class="summary-item total">
            <span class="label">Total:</span>
            <span class="value">Rp {{ number_format($total_transaksi, 0, ',', '.') }}</span>
        </div>
    </div>

    @else
    <p class="not-found">Detail transaksi tidak ditemukan.</p>
    @endif

    <div style="display: flex; justify-content: space-between; margin: bottom 40px;">
        @if($member->id_member_type != 3)
        <button class="btn-back btn-transaksi" onclick="loadTransaksiPemohon()">Kembali ke Daftar Transaksi</button>
        @else
        <button class="btn-back btn-transaksi" onclick="loadTransaksi()">Kembali ke Daftar Transaksi</button>
        @endif
        @if ($payment != 'sudah_bayar' && ($member->id_member_type == 3 || $member->id_member_type == 6) && $status != 'Menunggu_Konfirmasi_PPK' && $status != 'Pesanan_Dibatalkan')
        <button class="btn-back btn-upload" id="upload-payment" data-id_cart="{{ $id_cart }}" data-total="{{ $total_transaksi }}" style="margin-left: 15px;">Upload Pembayaran</button>
        @endif
        @if($status == 'Menunggu_Konfirmasi_PPK' && $member->id_member_type == 4)
        <button class="btn-back btn-danger" onclick="TolakPPK('{{ $id_cart }}')" style="margin-left: 15px;">Tolak</button>
        <button class="btn-back btn-upload" onclick="setujuinPPK('{{ $id_cart }}')" style="margin-left: 15px;">Setujui Pesanan</button>
        @endif
    </div>

</div>

<script>
    var bukti_transfer = "{{ $bukti_transfer }}";
    console.log(bukti_transfer);

    function loadTransaksi() {
        loadContent("{{ route('profile.transaksi') }}", $('#contentArea'));
    }

    function loadTransaksiPemohon() {
        loadContent("{{ route('profile.transaksi.pemohon') }}", $('#contentArea'));
    }

    function setujuinPPK(id_cart) {
        Swal.fire({
            title: "Konfirmasi Persetujuan PPK",
            text: "Apakah Anda yakin ingin mengizinkan transaksi ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, Setujui",
            cancelButtonText: "Tidak, Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                // If user clicks "Ya, Setujui", send AJAX request to approve the transaction
                $.ajax({
                    url: appUrl + "/api/approveTransaction",
                    type: "POST",
                    data: {
                        id_cart: id_cart
                    },
                    success: function(response) {
                        loadTransaksiPemohon();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.log("Error approving transaction: " + error);
                    }
                });
            }
        });
    }

    function TolakPPK(id_cart) {
        Swal.fire({
            title: "Konfirmasi Persetujuan PPK",
            text: "Apakah Anda yakin ingin membatalkan transaksi ini?",
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, batalkan",
            cancelButtonText: "Tidak"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: appUrl + "/api/rejectTransaction",
                    type: "POST",
                    data: {
                        id_cart: id_cart
                    },
                    success: function(response) {
                        loadTransaksiPemohon();
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.log("Error approving transaction: " + error);
                    }
                });
            }
        });
    }

    $('.cetakInvoice').click(function() {
        var id_cart_shop = $(this).data('id');
        var id_shop = $(this).data('id_shop');

        window.open(`{{ route('cetak.invoice') }}?id=${id_cart_shop}&id_shop=${id_shop}`, '_blank');
    });

    $('.cetakKwitansi').click(function() {
        var id_cart_shop = $(this).data('id');
        var id_shop = $(this).data('id_shop');
        window.open(`{{ route('cetak.kwitansi') }}?id=${id_cart_shop}&id_shop=${id_shop}`, '_blank');
    });

    $('.lacakResi').click(function() {
        var resi = $(this).data('resi');
        var id_courier = $(this).data('id_courier');
        var id_cart_shop = $(this).data('id');
        $.ajax({
            url: appUrl + "/api/kurir/view-tracking",
            method: "post",
            data: {
                id_courier: id_courier,
                resi: resi,
                cart_shop: id_cart_shop,
                _token: csrfToken,
            },
            xhrFields: {
                withCredentials: true,
            },
            success: function(response) {
                Swal.fire({
                    title: "Lacak Order",
                    html: response,
                    confirmButtonText: "OK",
                    width: window.innerWidth <= 600 ? "100%" : "60%",
                });
            },
            error: function(error) {
                console.error("Terjadi kesalahan:", error);
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: "Silakan coba lagi nanti.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            },
            complete: function() {
                $("#overlay").hide(); // Sembunyikan loader setelah selesai
            },
        });
    });

    $('.openKontrak').click(function() {
        var id_cart_shop = $(this).data('id');
        var url = "{{ route('profile.kontrak') }}?id=" + id_cart_shop;
        loadContent(url, $('#contentArea'));
    });

    $('.openSuratPesanan').click(function() {
        var id_cart_shop = $(this).data('id');
        var url = "{{ route('profile.suratpesanan') }}?id=" + id_cart_shop;
        loadContent(url, $('#contentArea'));
    });

    function uploadPayment(id_cart) {
        console.log(id_cart);
    }

    function uploadPajak(id_cart_shop) {
        console.log(id_cart_shop);
    }

    $(document).on("click", "#upload-payment", function() {
        var id_cart = $(this).data("id_cart");
        var total = $(this).data("total");

        var html = `
        <p>
            Nama Bank Tujuan    : PT. Elite Proxy Sistem <br>
            Bank Tujuan         : Bank BNI <br>
            No Rek Tujuan       : <b> 03975-60583 </b> <br>
            Total Pembayaran    : <b> ${formatRupiah(total)} </b>
        </p>
        <img id="swal2-image-preview" src="${ bukti_transfer ? bukti_transfer : '#' }" alt="Bukti Transfer" style="max-width: 200px; max-height: 200px; display: ${ bukti_transfer ? '' : 'none' };">
        <input type="file" id="swal2-file" name="img" accept="image/*" style="display: block; margin-top: 10px;">
    `;

        Swal.fire({
            title: "Upload Pembayaran",
            html: html,
            showCancelButton: true,
            confirmButtonText: "Unggah",
            cancelButtonText: "Batal",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve, reject) => {
                    var fileInput = document.getElementById("swal2-file");
                    var file = fileInput.files[0];
                    if (!file) {
                        reject("Anda harus memilih file gambar.");
                    } else {
                        resolve(file);
                    }
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.isConfirmed) {
                var file = result.value;
                var formData = new FormData();
                formData.append("id_cart", id_cart);
                formData.append("img", file);

                $.ajax({
                    url: appUrl + "/api/upload-payment",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        Swal.fire({
                            title: "Upload Berhasil",
                            text: "Pembayaran telah diunggah.",
                            icon: "success",
                        }).then(function() {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Upload Gagal",
                            text: "Terjadi kesalahan saat mengunggah pembayaran.",
                            icon: "error",
                        });
                    },
                });
            }
        });
    });

    $(document).on("change", "#swal2-file", function() {
        previewImage(this);
    });

    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                $("#swal2-image-preview").attr("src", e.target.result).show();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>