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
                <div class="product-mobile">
                    @foreach ($artwork_url_lg as $gambar)
                    <div class="product-mobile-item">
                        <img src="{{ $gambar }}" alt="Gambar besar">
                    </div>
                    @endforeach
                </div>
                <div class="product-carousel">
                    @foreach ($artwork_url_lg as $gambar)
                        <div class="productdetail-item">
                            <img src="{{ $gambar }}" class="product-image-large">
                        </div>
                    @endforeach
                </div>

                <!-- Gambar kecil -->
                <div class="product-small">
                    @foreach ($artwork_url_sm as $gambar)
                        <img src="{{ $gambar }}" alt="Gambar kecil" class="product-image-small">
                    @endforeach
                </div>
            </div>
            <div class="product-detail">
                <div class="product-info-detail">
                    <h4>{{ $name }}</h4>
                    <div class="rate-product">
                        <ul>
                            <li>{{$count_rating}} Penilaian</li>
                            <li>{{$count_sold}} Terjual</li>
                        </ul>
                    </div>
                    <h3>Rp{{ number_format($hargaTayang, 0, ',', '.') }}</h3>
                    {{-- <p class="discount">
                        <span class="box">20%</span>
                        <span class="discounted-price">Rp100.000</span>
                    </p> --}}
                </div>

                <div class="quantity-selector">
                    <label for="quantity">Jumlah:</label>
                    <div class="quantity-input">
                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" id="quantity" name="quantity" min="1" max="{{$stock}}"
                            value="1" step="1">
                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                    <p>Stok tersisa: <span class="stock-remaining">{{$stock}}</span></p>
                </div>
                <div class="action-buttons">
                    <button class="cart-btn" data-id="{{$id}}"><span class="material-icons">add_shopping_cart</span>
                        Keranjang</button>
                    <button class="buy-btn">Beli Sekarang</button>
                    <button class="nego-btn">Nego</button>
                </div>
                <div class="deskripsi-product">
                    <ul>
                        <li onclick="showSection('deskripsi')">Deskripsi</li>
                        <li onclick="showSection('speksifikasi')">Speksifikasi</li>
                        <li onclick="showSection('informasi-toko')">Informasi Toko</li>
                    </ul>
                </div>
                <div id="deskripsi" class="section-content">
                    <!-- Konten Deskripsi Produk -->
                    <p>
                        {{ $description !== null && $description !== '' ? $description : 'Toko tidak memasukkan deskripsi Barang' }}
                    </p>
                </div>
                <div id="speksifikasi" class="section-content" style="display: none;">
                    <p>
                        Merek : {{$merek}} <br>
                        {{ strip_tags($spesifikasi) }}
                    </p>                    
                </div>
                <div id="informasi-toko" class="section-content" style="display: none;">
                    <!-- Konten Informasi Toko -->
                    <p>Informasi toko di sini...</p>
                </div>
            </div>
            <div class="seller-detail">
                <img src="https://eliteproxy.co.id/seller_center/{{ $avatar }}" alt="Avatar">
                <p>
                    <a href="{{ route('seller.detail', ['id' => $idToko]) }}" style="text-decoration: none;">
                        <b>{{ $namaToko }}</b>
                    </a>
                </p>
                <table style="width: 100%">
                    <tr>
                        <td style="font-size:10px">Produk lainnya</td>
                        <td style="font-size:10px; "><a style="color:grey" href="#">lihat lainnya...</a></td>
                    </tr>
                </table>
                <div class="product-seller">
                    @php
                        $combined = array_map(null, $productlain, $productidlain);
                    @endphp

                    @foreach ($combined as [$product, $idProduct])
                        <a href="{{ route('product.show', ['id' => $idProduct]) }}">
                            <img src="{{ $product }}" alt="Gambar produk">
                        </a>
                    @endforeach


                </div>
            </div>
        </section class="ulasan-product">
        <div class="Penlaian-product">
            <div class="action-cart-mobile">
                <button class="cart-btn" data-id="{{$id}}"><span class="material-icons">add_shopping_cart</span>
                    Keranjang</button>
                <button class="buy-btn">Beli Sekarang</button>
                <button class="nego-btn">Nego</button>
            </div>

            <div class="deskripsi-product-mobile">
                <ul>
                    <li onclick="showSectionMobile('deskripsi-mobile')">Deskripsi</li>
                    <li onclick="showSectionMobile('speksifikasi-mobile')">Speksifikasi</li>
                    <li onclick="showSectionMobile('informasi-toko-mobile')">Informasi Toko</li>
                </ul>
            </div>
            <div id="deskripsi-mobile" class="section-content-mobile" style="display: none;">
                <!-- Konten Deskripsi Produk -->
                <p>Deskripsi produk di sini...</p>
            </div>
            <div id="speksifikasi-mobile" class="section-content-mobile" style="display: none;">
                <!-- Konten Speksifikasi Produk -->
                <p>Speksifikasi produk di sini...</p>
            </div>
            <div id="informasi-toko-mobile" class="section-content-mobile" style="display: none;">
                <!-- Konten Informasi Toko -->
                <p>Informasi toko di sini...</p>
            </div>

            <p><b>Penilaian Product</b></p>
            <div class="bintangproduct">
                <div class="penilaian-product-dekstop">
                    <ul class="review-options">
                        <li onclick="showReviewSection('all')">
                            Semua
                        </li>
                        <li onclick="showReviewSection('b1')">
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b2')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b3')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b4')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                        <li onclick="showReviewSection('b5')">
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                            <span class="material-icons">star</span>
                        </li>
                    </ul>
                </div>

                <div class="penilaian-product-mobile">
                    <select id="review-options" onchange="showReviewSection(this.value)">
                        <option value="all">Semua</option>
                        <option value="b1">★ 1</option>
                        <option value="b2">★ 2</option>
                        <option value="b3">★ 3</option>
                        <option value="b4">★ 4</option>
                        <option value="b5">★ 5</option>
                    </select>
                </div>

                <div id="all" class="review-content">
                    <!-- Konten semua ulasan -->
                    <p>Semua ulasan di sini...</p>
                </div>
                <div id="b1" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 1 -->
                    <p>Ulasan bintang 1 di sini...</p>
                </div>
                <div id="b2" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 2 -->
                    <p>Ulasan bintang 2 di sini...</p>
                </div>
                <div id="b3" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 3 -->
                    <p>Ulasan bintang 3 di sini...</p>
                </div>
                <div id="b4" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 4 -->
                    <p>Ulasan bintang 4 di sini...</p>
                </div>
                <div id="b5" class="review-content" style="display: none;">
                    <!-- Konten ulasan bintang 5 -->
                    <p>Ulasan bintang 5 di sini...</p>
                </div>
            </div>

        </div>
        <section>
        </section>

    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>
