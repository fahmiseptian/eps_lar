<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')
    <!-- Main Content -->
    <main>
        <section class="cart-member">
            <div class="detail-cart">
                <div class="cart-header">
                    <div class="select-all">
                        <span class="material-icons google-icon">
                            check_box_outline_blank
                        </span>
                        &nbsp;
                        <span>Pilih Semua</span>
                    </div>
                    <b>Hapus Semua</b>
                </div>

                @foreach ($cart->detail as $detail)
                <div class="cart-detail-seller">
                    <span class="material-icons google-icon">
                        check_box_outline_blank
                    </span>
                    <b>{{ $detail->nama_seller }}</b>
                    <hr>
                    @foreach ($detail->products as $product)
                    <div class="detail-product-cart">
                        <div class="product-info">
                            <a id="updateIsSelectProduct" data-id_cart="{{ $cart->id }}" data-id_cst="{{ $product->id_cst }}">
                                <span id="icon-{{ $product->id_cst }}" class="material-icons">
                                    {{ $product->is_selected == 'Y' ? 'check_box' : 'check_box_outline_blank' }}
                                </span>
                            </a>
                            <img src="{{$product->gambar_product}}" alt="product" class="product-image-cart">
                            <p style="margin-left: 10px; font-size: 14px;">{{ $product->nama_product }}</p>
                        </div>
                        <b class="product-price-cart">Rp {{ number_format($product->price, 0, ',', '.') }}</b>
                    </div>
                    <hr style="margin-left: 20px;">
                    <div class="button-aksi-cart">
                        <div class="left-actions">
                            <button class="btn-nego">Nego</button>
                            <button class="btn-hapus" id="deleteCart" data-idtemp="{{$product->id_cst }}" data-idshop="{{$detail->id_shop}}">Hapus</button>
                        </div>
                        <div class="qty-produk-cart">
                            <div class="qty-cart-produk">
                                <button class="btn-qty btn-kurang" data-id_cst="{{ $product->id_cst }}" data-id="{{ $product->id }}">-</button>
                                <input type="text" data-max="{{ $product->stock }}" id="qty-product-cart-{{ $product->id }}" data-id_cst="{{ $product->id_cst }}" data-id_cs="{{ $detail->id_cs }}" data-id="{{ $product->id }}" value="{{ $product->qty }}" class="input-qty">
                                <button class="btn-qty btn-tambah" data-id_cst="{{ $product->id_cst }}" data-id="{{ $product->id }}">+</button>
                            </div>
                            <p id="empty-{{ $product->id }}" style="display: none; color: red;">Minimal 1 Produk</p>
                        </div>
                    </div>
                    <hr style="margin-left: 20px;">
                    @endforeach
                </div>
                @endforeach

            </div>
            </div>
            <div class="sub-total-cart">
                <b style="font-size: 18px; margin-bottom: 10px;" align="left">Ringkasan Belanja</b>
                <hr>
                <div class="sub-total-cart-content">
                    <p>Total Pesanan</p>
                    <p>{{ $cart->qty }} Product</p>
                </div>
                <div class="sub-total-cart-content">
                    <p>Total</p>
                    <b id="total-cart">Rp {{ number_format($cart->sumprice, 0, ',', '.') }}</b>
                </div>
                <hr>
                <a href="{{route('checkout')}}" class="btn-checkout">Checkout</a>
            </div>
        </section>
    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>