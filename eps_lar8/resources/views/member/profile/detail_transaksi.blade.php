<div class="detail-transaksi-container">
    <h2 class="transaction-title">Detail Transaksi</h2>

    @if(!empty($transactions))
    @foreach($transactions as $transaction)
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
                        <span class="value">{{ $transaction['detailOrder']->instansi }}</span>
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
                        <span class="value">{{ $transaction['detailOrder']->note_seller }}</span>
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
                        <h5 class="product-name">{{ $product->nama }}</h5>
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
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal produk sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal Ongkos Kirim sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Subtotal Asuransi Pengiriman sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Biaya Penanganan sebelum PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($transaction['detailOrder']->pmk != 59)
            <div class="summary-item">
                <span class="label">PPN:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
            </div>
            @elseif ($transaction['detailOrder']->pmk == 59)
            <div class="summary-item">
                <span class="label">PPn:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->subtotal, 0, ',', '.') }}</span>
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
                <span class="label">Total:</span>
                <span class="value">Rp {{ number_format($transaction['detailOrder']->total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    @endforeach
    @else
    <p class="not-found">Detail transaksi tidak ditemukan.</p>
    @endif

    <button class="btn-back" onclick="loadTransaksi()">Kembali ke Daftar Transaksi</button>
</div>

<script>
    function loadTransaksi() {
        loadContent("{{ route('profile.transaksi') }}", $('#contentArea'));
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
        console.log('resi : ' + resi);
        console.log('id_courier : ' + id_courier);
        console.log('id_cart_shop : ' + id_cart_shop);
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
</script>