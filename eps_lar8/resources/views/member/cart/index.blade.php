<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')
    <!-- Main Content -->
    <main>
        <section class="cart-member">
            <div class="cart-header">
                <p class="product-name">Nama Product</p>
                <p class="unit-price">Harga Satuan</p>
                <p class="quantity">Kuantitas</p>
                <p class="total-price">Total Harga</p>
                <p class="action">Aksi</p>
            </div>

            @foreach ($cart->detail as $detail)
                <div class="item-cart-seller-name">
                    {{-- Seller --}}
                    <p>{{ $detail->nama_seller }}</p>
                    <hr>
                    @foreach ($detail->products as $product)
                        {{-- Product --}}
                        <div class="item-cart-product">
                            <button class="checkbox-btn">☐</button>
                            <img src="{{$product->gambar_product}}"
                                alt="product" class="product-image">
                            <p class="product-name">{{ $product->nama_product }}</p>
                            <p class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <div>
                                <div class="quantity-input">
                                    <button type="button" id="kurang-qty-cart" data-id="{{ $cart->id }}" data-id_cst="{{ $product->id_cst }}" data-id_cs="{{ $detail->id_cs }}">-</button>
                                    <input type="number" id="quantity" name="quantity" min="1" max="{{ $product->stock }}" value="{{ $product->qty }}" step="1" class="quantity" data-product-id="{{ $product->id_cst }}" data-stock="{{ $product->stock }}" data-id="{{ $cart->id }}" data-id_cst="{{ $product->id_cst }}" data-id_cs="{{ $detail->id_cs }}">
                                    <button type="button" id="tambah-qty-cart" data-id="{{ $cart->id }}" data-id_cst="{{ $product->id_cst }}" data-id_cs="{{ $detail->id_cs }}">+</button>
                                </div>
                                <p id="remaining-quantity" style="font-size: small;" data-product-id="{{ $product->id_cst }}">Tersisa {{ $product->stock - $product->qty }} buah</p>
                            </div>
                            <p class="product-total">Rp {{ number_format($product->total, 0, ',', '.') }}</p>
                            <div class="item-cart-action">
                                <p>Nego</p>
                                <p id="deleteCart" data-idtemp="{{$product->id_cst }}" data-idshop="{{$detail->id_shop}}">Hapus</p>
                            </div>
                        </div>
                        <hr>
                        {{-- end Product --}}
                    @endforeach
                    <div class="item-cart-voucher">
                        <span class="material-icons" id="icon-kupon">confirmation_number</span>
                        {{-- voucher seller --}}
                        <select class="voucher-dropdown">
                            <option value="" disabled selected>Belum Ada Voucher Toko</option>
                            <option value="">Tidak Pakai</option>
                            <option value="voucher1">Voucher 1</option>
                            <option value="voucher2">Voucher 2</option>
                        </select>
                        {{-- end Voucher --}}
                    </div>
                    {{-- end Seller --}}
                </div>
            @endforeach

            <div class="cart-footer">
                <div class="voucher-total">
                    <span class="material-icons" id="icon-kupon">confirmation_number</span>
                    <select class="voucher-dropdown">
                        <option value="" disabled selected>Belum Ada Voucher Aplikasi</option>
                        <option value="">Tidak Pakai</option>
                    </select>
                </div>
                <div class="subtotal-checkout">
                    <p>Sub Total ({{ $cart->qty }} product) <b>Rp. {{ number_format($cart->total, 0, ',', '.') }}
                            &nbsp;</b> <a href="{{route('checkout')}}"> <span class="btn btn-danger">Checkout</span></a></p>
                </div>
            </div>

        </section>
    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>
