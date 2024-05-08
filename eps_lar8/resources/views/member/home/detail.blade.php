<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <section class="detaiProduct">
            <div class="gambarProduct">
                <!-- Gambar besar -->
                <div class="product-carousel">
                    <div class="productdetail-item">
                        <img src="https://via.placeholder.com/300x300" alt="Gambar besar" class="product-image-large">
                    </div>
                </div>

                <!-- Gambar kecil -->
                <div class="product-small">
                    <img src="https://via.placeholder.com/100x100" alt="Gambar kecil" class="product-image-small">
                    <img src="https://via.placeholder.com/100x100" alt="Gambar kecil" class="product-image-small">
                    <img src="https://via.placeholder.com/100x100" alt="Gambar kecil" class="product-image-small">
                </div>
            </div>
            <div class="product-detail">
                <div class="product-info">
                    <h4>Honeywell Brankas 2901</h4>
                    <div class="rate-product">
                        <ul>
                            <li>0</li>
                            <li>0 Penilaian</li>
                            <li>0 Terjual</li>
                        </ul>
                    </div>
                    <h3>Rp80.000</h3>
                    <p class="discount">
                        <span class="box">20%</span>
                        <span class="discounted-price">Rp100.000</span>
                    </p>
                </div>

                <div class="quantity-selector">
                    <label for="quantity">Jumlah:</label>
                    <div class="quantity-input">
                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" name="quantity" min="1" max="10"
                            value="1" step="1">
                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                    <p>Stok tersisa: <span class="stock-remaining">10</span></p>
                </div>
                <div class="action-buttons">
                    <button class="cart-btn"><span class="material-icons">add_shopping_cart</span>
                        Keranjang</button>
                    <button class="buy-btn">Beli Sekarang</button>
                    <button class="nego-btn">Nego</button>
                </div>
            </div>
            <div class="seller-detail">
                    <img src="https://via.placeholder.com/50x50">
                    <p><b>nama seller</b></p>
                    <hr>
                    <small>Produk lainnya</small>
                    <div class="product-seller">
                        <img src="https://via.placeholder.com/50x50">
                        <img src="https://via.placeholder.com/50x50">
                        <img src="https://via.placeholder.com/50x50">
                        <img src="https://via.placeholder.com/50x50">
                    </div>
            </div>

        </section>

    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
</body>

</html>
