<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')
    <!-- Main Content -->
    <main>
        <section class="checkout">
            <div class="pengiriman">
                <div class="lokasi">
                    <p><span class="material-icons">location_on</span> Alamat Pengiriman</p>
                    <p class="btn btn-info" data-id_address="{{ $cartAddress->member_address_id }}"
                        id="ubah-lokasi-pengiriman">Ubah</p>
                </div>
                <div class="alamat">
                    <span class="material-icons">radio_button_checked</span>
                    <p>{{ $cartAddress->address_name }} - {{ $cartAddress->phone }}</p>
                    <p>{{ $cartAddress->address }} <br> {{ $cartAddress->city }} - {{ $cartAddress->subdistrict_name }},
                        {{ $cartAddress->province_name }}, {{ $cartAddress->postal_code }}</p>
                </div>
            </div>

            <div class="product-checkout">
                <div class="head-list">
                    <b>Product Dipesan</b>
                    <p id="no-mobile">Harga Satuan</p>
                    <p>Jumlah</p>
                    <p>Subtotal Product</p>
                </div>
                @foreach ($cart->detail as $detail)
                    {{-- seller --}}
                    <div class="body-product">
                        <p>{{ $detail->nama_seller }}</p>
                        <hr>
                        @foreach ($detail->products as $product)
                            {{-- Product --}}
                            <div class="data-product-checkout">
                                <img src="{{ $product->gambar_product }}" alt="product">
                                <p style="width: 35%">{{ $product->nama_product }}</p>
                                <p id="no-mobile">Rp. {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p>{{ $product->qty }}</p>
                                <p>Rp {{ number_format($product->total, 0, ',', '.') }}</p>
                            </div>
                            <hr>
                        @endforeach
                        <div class="checkout-item">
                            <p><span class="material-icons" id="icon-kupon">confirmation_number</span> No Voucher
                            </p>
                            <p>Rp.-</p>
                        </div>
                        <div class="checkout-item">
                            <input type="text" name="keperluan" id="keperluan"
                                placeholder="Ketik Untuk Menambahkan Keperluan">
                            <input type="text" name="pesan" id="pesan"
                                placeholder="Ketik Untuk Menambahkan Pesan Ke Penjual">
                        </div>
                        <div class="checkout-item">
                            <select class="jasa-pengiriman" name="jasa-pengiriman" data-id_cs="{{ $detail->id_cs }}">
                                <option value="" disabled
                                    {{ $detail->id_shipping == 0 || is_null($detail->id_shipping) ? 'selected' : '' }}>
                                    Opsi Pengiriman</option>
                                @foreach ($detail->pengiriman as $ongkir)
                                    <option value="{{ $ongkir->id }}"
                                        {{ $ongkir->deskripsi == $detail->deskripsi_pengiriman ? 'selected' : '' }}>
                                        {{ $ongkir->deskripsi }}-({{ $ongkir->etd }} Hari) Rp.
                                        {{ number_format($ongkir->sum_shipping, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            </select>
                            <?php
                                $ppn_shipping = $cart->ppn * $detail->sum_shipping;
                                $total_ongkir = $detail->sum_shipping + $ppn_shipping;
                            ?>
                            <p id="ongkir-akhir">Rp.
                                {{ number_format($total_ongkir, 0, ',', '.') }}</p>
                        </div>

                        <div class="checkout-item" id="asuransi-pengirimans">
                            @if ($detail->is_insurance === 1)
                                <p id="asuransi-pengiriman" data-id_cs="{{ $detail->id_cs }}" data-id_shop="{{ $detail->id_shop }}" data-id_courier="{{ $detail->id_courier }}" data-status="delete"><span class="material-icons">check </span> Asuransikan Pengiriman</p>
                            @else
                                <p id="asuransi-pengiriman" data-id_cs="{{ $detail->id_cs }}" data-id_shop="{{ $detail->id_shop }}" data-id_courier="{{ $detail->id_courier }}" data-status="add"><span class="material-icons">check_box_outline_blank</span> Asuransikan Pengiriman</p>
                            @endif
                            <p>Rp. {{ number_format($detail->sum_asuransi, 0, ',', '.') }}</p>
                        </div>
                        {{-- end Product --}}
                        <hr>
                    </div>
                    {{-- end seller --}}
                @endforeach
                <div class="checkout-item">
                    <p><span class="material-icons" id="icon-kupon">confirmation_number</span>Voucher</p>
                    <select class="voucher-checkout" name="voucher">
                        <option value="0" selected>Tidak ada Voucher</option>
                    </select>
                </div>
            </div>

            <div class="pembayaran-checkout">
                <div class="payment-checkout">
                    <p>Pilihan Pembayaran :</p>
                    @foreach ($cart->payment as $pay )
                        <p id="paymend_method" data-id_pay="{{$pay->id}}"><span class="material-icons">{{ $pay->id == $cart->id_payment ? 'radio_button_checked' : 'radio_button_unchecked' }}</span>{{$pay->name}}</p>
                    @endforeach
                </div>
                <hr>
                @if ($cart->id_payment == 23 || $cart->id_payment == 30)
                    <div class="payment-checkout">
                        <p>Pilihan TOP :</p>
                        <p id="updateTOP" data-top="7"><span class="material-icons">{{ 7 == $cart->jml_top ? 'radio_button_checked' : 'radio_button_unchecked' }}</span>7 hari</p>
                        <p id="updateTOP" data-top="14"><span class="material-icons">{{ 14 == $cart->jml_top ? 'radio_button_checked' : 'radio_button_unchecked' }}</span>14 hari</p>
                        <p id="updateTOP" data-top="30"><span class="material-icons">{{ 30 == $cart->jml_top ? 'radio_button_checked' : 'radio_button_unchecked' }}</span>30 hari</p>
                    </div>
                    <hr>
                @endif

                <div class="container">
                    <div class="row">
                        <div class="col-md-5"></div>
                        <div class="col-md-7">
                            <p class="total-pembayaran">Detail Pembayaran</p>
                            <div class="detail-pembayaran">
                                <p>Subtotal Product tanpa PPN</p>
                                <p>Rp. {{ number_format($cart->total_barang_tanpa_PPN, 0, ',', '.') }}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Subtotal produk sebelum PPN</p>
                                <p>Rp. {{ number_format($cart->total_barang_dengan_PPN, 0, ',', '.') }}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Subtotal Ongkos Kirim sebelum PPN</p>
                                <p>Rp. {{ number_format($cart->total_shipping, 0, ',', '.') }}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Subtotal Asuransi Pengiriman sebelum PPN</p>
                                <p>Rp. {{ number_format($cart->total_insurance, 0, ',', '.') }}</p>
                            </div>
                            @if ($cart->id_payment == 30 || $cart->id_payment == 22)
                            <div class="detail-pembayaran">
                                <p>Biaya Penanganan sebelum PPN</p>
                                <p>Rp. {{ number_format($cart->handling_cost_non_ppn, 0, ',', '.') }}</p>
                            </div>
                            @endif
                            <div class="detail-pembayaran">
                                <p>PPN</p>
                                <p>Rp. {{ number_format($cart->total_ppn, 0, ',', '.') }}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Total Discount</p>
                                <p>Rp. {{ number_format($cart->total_diskon, 0, ',', '.') }}</p>
                            </div>
                            <div class="total-pembayaran">
                                <p>Total Pembayaran</p>
                                <p>Rp. {{ number_format($cart->total, 0, ',', '.') }}</p>
                            </div>
                            &nbsp;
                            <button class="btn" id="request-checkout" data-id_cart="{{$cart->id}}">Buat Pesanan</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>
    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>
