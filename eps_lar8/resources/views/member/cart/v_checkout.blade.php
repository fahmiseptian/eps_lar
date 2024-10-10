<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')
<style>
    :root {
        --primary-color: #fc6703;
        --secondary-color: #fff0e6;
        --text-color: #333333;
        --hover-color: #e55a00;
        --putih: #fff;
        --background-color: #efefef;
        --samar-background-color: #f9f9f9;
    }

    .checkout-container {
        background-color: var(--putih);
        padding: 40px;
        border-radius: 25px;
    }

    .order-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .payment-detail-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .order-list li {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .payment-detail-list li {
        padding: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .order-list li:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 50px;
        height: 50px;
        margin-right: 10px;
    }

    .co-name-produk {
        margin-left: 10px;
    }

    .product-name-co {
        color: var(--primary-color);
        text-align: left;
        font-weight: bold;
        margin-bottom: 0;
    }

    .product-total-price {
        color: var(--primary-color);
        font-size: 16px;
        font-weight: bold;
    }

    .detail-produk-co {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .satuan-price {
        display: flex;
        align-items: center;
        font-size: 14px;
    }

    .tujuan-pengiraiman-co {
        font-size: 16px;
        color: var(--text-color);
    }

    .checkout-container h3 {
        color: var(--primary-color);
    }

    .button-edit-pengiriman-co {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .button-edit-pengiriman-co button {
        font-size: small;
        height: auto;
        color: var(--secondary-color);
    }

    .top-opsi-co {
        padding: 15px;
    }

    .top-opsi-co p {
        font-weight: bold;
        margin-bottom: 10px;
    }

    .top-opsi-co label {
        display: inline-block;
        margin-right: 20px;
        font-size: 16px;
        cursor: pointer;
        position: relative;
        padding-left: 30px;
    }

    .top-opsi-co input[type="radio"] {
        position: absolute;
        left: 0;
        top: 2px;
        width: 18px;
        height: 18px;
    }

    .top-opsi-co input[type="radio"]:checked+span {
        color: #007bff;
        font-weight: bold;
    }

    .top-opsi-co label span {
        margin-left: 5px;
    }

    #place-order-btn {
        margin-top: 20px;
    }

    .nama-seller-co {
        color: var(--text-color);
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 2px;
        border-bottom: solid 1px var(--primary-color);
    }

    .asuransi_pengiriman {
        margin-top: 0px;
    }

    .price-pengiriman-co {
        vertical-align: bottom;
        text-align: right;
    }

    .row {
        margin-bottom: 20px;
    }

    /* Title styling */
    .shipping-method-title {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    /* Form group adjustments */
    .form-group {
        margin-bottom: 15px;
    }

    /* Align the checkbox label */
    .asuransi_pengiriman {
        display: block;
        margin-top: 10px;
        font-size: 16px;
    }

    /* Price alignment for the right side */
    .price-pengiriman-co,
    .asuransi-pengiriman-co {
        margin-top: 20px;
        font-size: 16px;
        margin-bottom: 10px;
        font-weight: bold;
        color: var(--primary-color);
    }

    /* Adjust spacing for the prices */
    .price-pengiriman-co {
        margin-bottom: 20px;
    }

    .asuransi-pengiriman-co {
        color: var(--primary-color);
    }

    .asuransi-pengiriman-collom {
        margin-top: 0;
    }

    .nama-pengiriman-co {
        font-weight: bold;
        margin-bottom: 0;
    }

    .detail-pengiriman-co {
        margin-top: 0;
    }

    .btn-checkout {
        background-color: var(--primary-color);
        color: white;
    }

    .btn-checkout:hover {
        background-color: var(--hover-color);
        color: white;
    }
</style>

<body>

    @include('member.asset.navbar')
    <!-- Main Content -->
    <main>
        <section class="checkout-container">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="shipping-title">Alamat Pengiriman</h3>
                    <div class="tujuan-pengiraiman-co">
                        <p class="nama-pengiriman-co"> {{ $cartAddress->address_name }} - {{ $cartAddress->phone }}</p>
                        <div class="detail-pengiriman-co">
                            <p>
                                {{ $cartAddress->address }} <br> {{ $cartAddress->city }} - {{ $cartAddress->subdistrict_name }},
                                {{ $cartAddress->province_name }}, {{ $cartAddress->postal_code }}
                            </p>
                        </div>
                    </div>
                    <div class="button-edit-pengiriman-co">
                        <button class="btn btn-secondary" onclick="openmodalalamat()">
                            <i class="material-icons">edit</i> Ubah
                        </button>
                        <button class="btn btn-info" data-id_address="{{ $cartAddress->member_address_id }}"
                            id="ubah-lokasi-pengiriman">
                            Ganti Alamat Pengiriman
                        </button>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="order-title">Product yang Dipesan</h3>
                    @foreach ($cart->detail as $detail)
                    <p class="nama-seller-co">{{ $detail->nama_seller }}</p>
                    <ul class="order-list">
                        @foreach ($detail->products as $product)
                        @php
                        $requiresBaseUrl = strpos($product->gambar_product, 'http') === false;
                        $pc_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$product->gambar_product : $product->gambar_product;
                        @endphp
                        <li>
                            <div class="detail-produk-co">
                                <img src="{{ $pc_image }}" alt="Product 1" width="50" height="50">
                                <div class="co-name-produk">
                                    <p class="product-name-co">{{ $product->nama_product }}</p>
                                    <div class="satuan-price">
                                        <span class="product-quantity">{{ $product->qty }} x</span>
                                        <span class="product-unit-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="product-total-price">Rp {{ number_format($product->total, 0, ',', '.') }}</span>
                        </li>
                        @endforeach
                    </ul>
                    <hr>
                    @endforeach
                </div>
            </div>
            <hr>
            @foreach ($cart->detail as $detail)
            <div class="row">
                <div class="col-md-6">
                    <h3 class="shipping-method-title">Pengiriman untuk {{ $detail->nama_seller }}</h3>
                    <form>
                        <div class="form-group">
                            <label for="shipping-method">Pilih Metode Pengiriman</label>
                            <select id="shipping-method" class="form-control jasa-pengiriman" name="jasa-pengiriman" data-id_cs="{{ $detail->id_cs }}" required>
                                <option value="" disabled
                                    {{ $detail->id_shipping == 0 || is_null($detail->id_shipping) ? 'selected' : '' }}>
                                    Opsi Pengiriman</option>
                                @foreach ($detail->pengiriman as $ongkir)
                                <option value="{{ $ongkir->id }}"
                                    {{ $ongkir->deskripsi == $detail->deskripsi_pengiriman ? 'selected' : '' }}>
                                    {{ $ongkir->deskripsi }} - ({{ $ongkir->etd }} Hari)
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    @php
                    $ppn_shipping = $cart->ppn * $detail->sum_shipping;
                    $total_ongkir = $detail->sum_shipping + $ppn_shipping;
                    @endphp
                    <p class="price-pengiriman-co">
                        Rp {{ number_format($total_ongkir, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mt-0">
                    <label class="asuransi_pengiriman">
                        @if ($detail->is_insurance === 1)
                        <input type="checkbox" value="asuransi_pengiriman" id="asuransi-pengiriman" data-id_cs="{{ $detail->id_cs }}" data-id_shop="{{ $detail->id_shop }}" data-id_courier="{{ $detail->id_courier }}" data-status="delete" checked>
                        @else
                        <input type="checkbox" value="asuransi_pengiriman" id="asuransi-pengiriman" data-id_cs="{{ $detail->id_cs }}" data-id_shop="{{ $detail->id_shop }}" data-id_courier="{{ $detail->id_courier }}" data-status="add">
                        @endif
                        Asuransikan Pengiriman
                    </label>
                </div>
                <div class="col-md-6 d-flex flex-column align-items-end">
                    <p class="asuransi-pengiriman-co">
                        Rp {{ number_format($detail->sum_asuransi, 0, ',', '.') }}
                    </p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shipping-method">Catatan Untuk Keperluan untuk {{ $detail->nama_seller }}</label>
                        <input type="text" class="form-control" name="keperluan" id="keperluan" data-id_cs="{{ $detail->id_cs }}" placeholder="Ketik untuk menambahkan keperluan" value="{{ $detail->keperluan }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="shipping-method">Catatan Untuk Penjual untuk {{ $detail->nama_seller }}</label>
                        <input type="text" class="form-control" name="note_seller" id="note_seller" data-id_cs="{{ $detail->id_cs }}" placeholder="Ketik untuk menambahkan Pesan" value="{{ $detail->pesan_seller }}">
                    </div>
                </div>
            </div>
            <hr>
            @endforeach

            <div class="row">
                <div class="col-md-6">
                    <h3 class="payment-method-title">Metode Pembayaran</h3>
                    <div class="form-group">
                        <select id="payment-method" class="form-control" required>
                            @foreach ($cart->payment as $pay )
                            <option
                                {{ $pay->id == $cart->id_payment ? 'selected' : '' }}
                                value="{{$pay->id}}">{{$pay->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="payment-method-title">Pilihan TOP</h3>
                    <div class="form-group">
                        <select id="TOP-opsi" class="form-control" @if($cart->id_payment == 22) disabled @endif>
                            <option value="">Pilih TOP Pembayaran</option>
                            <option value="7"
                                {{ 7 == $cart->jml_top ? 'selected' : '' }}> 7 Hari</option>
                            <option value="14"
                                {{ 14 == $cart->jml_top ? 'selected' : '' }}>14 Hari</option>
                            <option value="30"
                                {{ 30 == $cart->jml_top ? 'selected' : '' }}>30 Hari</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6 mt-2">
                    <h3 class="payment-detail-title">Detail Pembayaran</h3>
                    <ul class="payment-detail-list">
                        <li>
                            <span class="payment-detail-label">Subtotal Product tanpa PPN:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total_barang_tanpa_PPN, 0, ',', '.') }}</span>
                        </li>
                        <li>
                            <span class="payment-detail-label">Subtotal produk sebelum PPN:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total_barang_dengan_PPN, 0, ',', '.') }}</span>
                        </li>
                        <li>
                            <span class="payment-detail-label">Subtotal Ongkos Kirim sebelum PPN:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total_shipping, 0, ',', '.') }}</span>
                        </li>
                        <li>
                            <span class="payment-detail-label">Subtotal Asuransi Pengiriman sebelum PPN:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total_insurance, 0, ',', '.') }}</span>
                        </li>
                        @if ($cart->id_payment != 23)
                        <li>
                            <span class="payment-detail-label">Biaya Penanganan sebelum PPN:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->handling_cost_non_ppn,  0, ',', '.') }}</span>
                        </li>
                        @endif
                        <li>
                            <span class="payment-detail-label">PPN:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total_ppn,  0, ',', '.') }}</span>
                        </li>
                        <li>
                            <span class="payment-detail-label">Total Discount:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total_diskon, 0, ',', '.') }}</span>
                        </li>
                        <li>
                            <span class="payment-detail-label">Total Pembayaran:</span>
                            <span class="payment-detail-value">Rp. {{ number_format($cart->total, 0, ',', '.') }}</span>
                        </li>
                    </ul>
                    @if ($satker_ppk != null)
                    @if($cart->total >= $satker_ppk->batas_awal && $cart->total <= $satker_ppk->batas_akhir)
                        <button class="btn btn-checkout btn-block" id="request-persetujuan-ppk" onclick="openmodalrequestppk()" data-id_cart="{{$cart->id}}">Buat Permintaan Persetujuan PPK</button>
                        @else
                        <button class="btn btn-checkout btn-block" id="request-checkout" data-id_cart="{{$cart->id}}"> Buat Pesanan </button>
                        @endif
                        @else
                        <button class="btn btn-checkout btn-block" id="request-checkout" data-id_cart="{{$cart->id}}">Buat Pesanan </button>
                        @endif
                </div>
            </div>
        </section>
    </main>

    <!-- modal persetujuan PPK -->
    <div class="modal fade" id="requestppkModal" tabindex="-1" aria-labelledby="requestppkModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestppk">Permintaan Persetujuan PPK</h5>
                    <button type="button" onclick="closemodalrequestPPK()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        Total Belanjamu melebihi ketentuan, diperlukan approval dari PPK Satkermu untuk melanjutkan ke Checkout pembayaran.
                        Klik lanjutkan, jika setuju.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="closemodalrequestPPK()">Tutup</button>
                    <button type="button" class="btn btn-primary" id="request-checkout-withPPK" data-id_cart="{{$cart->id}}">Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alamatModal" tabindex="-1" aria-labelledby="alamatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="alamatModalLabel">Lanjutkan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="formTambahAlamat">
                        <div class="form-group">
                            <label for="nama_penerima">Nama Penerima</label>
                            <input type="text" class="form-control" id="nama_penerima" value=" {{ $cartAddress->address_name ?? '' }}" required>
                            <input type="hidden" id="id" value="{{ $cartAddress->member_address_id ?? null }}">
                        </div>
                        <div class="form-group">
                            <label for="no_telepon">Nomor Telepon</label>
                            <input type="text" class="form-control" id="no_telepon" value="{{ $cartAddress->phone ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label for="provinsi">Provinsi</label>
                            <select class="form-control" id="provinsi" required>
                                <option value="">Pilih Provinsi</option>
                                @foreach ($provinces as $province)
                                <option
                                    {{ $cartAddress->province_id == $province->province_id ? 'selected' : '' }}
                                    value="{{$province->province_id}}">{{$province->province_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kota">Kota</label>
                            <select class="form-control" id="kota" required>
                                <option value="">Pilih Kota</option>
                                <!-- Data kota akan diisi melalui AJAX -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kecamatan">Kecamatan</label>
                            <select class="form-control" id="kecamatan" required>
                                <option value="">Pilih Kecamatan</option>
                                <!-- Data kecamatan akan diisi melalui AJAX -->
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kode_pos">Kode Pos</label>
                            <input type="text" class="form-control" id="kode_pos" value="{{ $cartAddress->postal_code ?? '' }}" required>
                        </div>
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap</label>
                            <textarea class="form-control" id="alamat" rows="3" required>{{ $cartAddress->address ?? '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan Alamat</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('member.asset.footer')
    <script>
        ajaxCity('{{ $cartAddress->province_id ?? null }}', function() {
            $('#kota').val('{{ $cartAddress->city_id ?? null }}');
        });

        ajaxdistrix('{{ $cartAddress->city_id ?? null }}', function() {
            $('#kecamatan').val('{{ $cartAddress->subdistrict_id ?? null }}');
        });

        $('#provinsi').change(function() {
            var provinsiId = $(this).val();
            $('#kota').empty().append(new Option('Pilih Kota', ''));
            $('#kecamatan').empty().append(new Option('Pilih Kecamatan', ''));
            ajaxCity(provinsiId);
        });

        $('#kota').change(function() {
            var kotaId = $(this).val();
            $('#kecamatan').empty().append(new Option('Pilih Kecamatan', ''));
            ajaxdistrix(kotaId);
        });

        function ajaxCity(id_province, callback) { // Tambahkan parameter callback
            if (!id_province) {
                return;
            }

            $.ajax({
                url: appUrl + '/api/config/getCity/' + id_province,
                method: 'GET',
                success: function(data) {
                    $('#kota').empty().append(new Option('Pilih Kota', '')); // Kosongkan dropdown sebelum diisi
                    $.each(data.citys, function(index, kota) {
                        $('#kota').append(new Option(kota.city_name, kota.city_id));
                    });
                    if (callback) callback(); // Panggil callback jika ada
                },
                error: function(xhr) {
                    console.error('Error fetching cities:', xhr);
                }
            });
        }

        function ajaxdistrix(id_city, callback) { // Tambahkan parameter callback
            if (!id_city) {
                return;
            }

            $.ajax({
                url: appUrl + '/api/config/getdistrict/' + id_city,
                method: 'GET',
                success: function(data) {
                    $('#kecamatan').empty().append(new Option('Pilih Kecamatan', '')); // Kosongkan dropdown sebelum diisi
                    $.each(data.subdistricts, function(index, kecamatan) {
                        $('#kecamatan').append(new Option(kecamatan.subdistrict_name, kecamatan.subdistrict_id));
                    });
                    if (callback) callback(); // Panggil callback jika ada
                },
                error: function(xhr) {
                    console.error('Error fetching districts:', xhr);
                }
            });
        }


        function openmodalalamat() {
            $('#alamatModal').modal('show');
        }

        function openmodalrequestppk() {
            $('#requestppkModal').modal('show');
        }

        function closemodalrequestPPK() {
            $('#requestppkModal').modal('hide');
        }

        $('#formTambahAlamat').submit(function(e) {
            e.preventDefault();

            // Mengambil nilai dari setiap input
            var id = $('#id').val();
            var namaPenerima = $('#nama_penerima').val();
            var noTelepon = $('#no_telepon').val();
            var provinsi = $('#provinsi').val();
            var kota = $('#kota').val();
            var kecamatan = $('#kecamatan').val();
            var kodePos = $('#kode_pos').val();
            var alamat = $('#alamat').val();

            // Menyusun data yang akan dikirim
            var formData = {
                id: id,
                nama_penerima: namaPenerima,
                no_telepon: noTelepon,
                provinsi: provinsi,
                kota: kota,
                kecamatan: kecamatan,
                kode_pos: kodePos,
                alamat: alamat
            };

            // AJAX untuk mengirim data
            $.ajax({
                url: appUrl + '/api/member/storeAddress?token=' + token,
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire('Berhasil!', 'Alamat berhasil diPerbaharui!', 'success');
                    $('#formTambahAlamat')[0].reset();
                    location.reload();
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menambahkan alamat.');
                }
            });
        });
    </script>

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>