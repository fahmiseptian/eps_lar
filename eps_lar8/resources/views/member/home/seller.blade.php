<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <div class="card-detail-toko">
            <div class="store-logo">
                <img src="https://eliteproxy.co.id/seller_center/{{ $avatar }}" alt="Avatar">
            </div>
            <div class="store-info">
                <h3 class="store-name">{{ $name }}</h3>
                <div class="store-rating">
                    <span class="rating">4.8</span>
                    <span class="rating-stars">★★★★★</span>
                    <span class="review-count">(123)</span>
                </div>
                <div class="store-details">
                    <p>Jumlah produk: {{ $jmlhproduct }}</p>
                    <p>Terjual: {{ $jmlhTerjualproduct }}</p>
                    <p>Lokasi: {{ $city_name }}</p>
                </div>

            </div>

            <a href="#" class="visit-store-button">Follow</a>
        </div>
        <ul id="menu-list">
            <li id="dashboard-tab" class="active">Dashboard</li>
            <li id="product-tab">Product</li>
        </ul>
        <div class="dashboard-toko" id="dashboard-content">
            <p><b>Product Terbaru</b></p>
            <div class="Product-terbaru">
                @if (!empty($NewProduct))
                    @foreach ($NewProduct as $product)
                        <div class="product-item-terbaru">
                            <a href="{{ route('product.show', ['id' => $product->id]) }}" class="product-link">
                                <img src="{{ $product->artwork_url_md[0] }}" alt="Produk">
                                <p title="{{ $product->name }}">{{ substr($product->name, 0, 20) }}...</p>
                                <p>Rp {{ number_format($product->hargaTayang, 0, ',', '.') }}</p>
                            </a>
                        </div>
                    @endforeach
                @else
                    <p>Tidak ada produk terbaru.</p>
                @endif
            </div>
            <div class="product-toko-detail">
                <div class="kategori-toko-detail" style="font-size:12px">
                    <h2>Kategori</h2>
                    <ol id="list-etalase" data-id="0" data-idshop="{{ $id }}">Semua</ol>
                    @foreach ($products->level2 as $lv2)
                        <ol id="list-category" data-id="{{ $lv2->id }}" data-idshop="{{ $id }}">
                            {{ $lv2->name }}</ol>
                    @endforeach
                    @foreach ($products->level3 as $lv3)
                        <ol id="list-category" data-id="{{ $lv3->id }}" data-idshop="{{ $id }}">
                            {{ $lv3->name }}</ol>
                    @endforeach
                </div>
                <div class="kategori-toko-detail-mobile">
                    <h2>Kategori</h2>
                    <select id="categoryDropdown" data-idshop="{{ $id }}">
                        <option value="0">Semua</option>
                        @foreach ($products->level2 as $lv2)
                            <option value="{{ $lv2->id }}">{{ $lv2->name }}</option>
                        @endforeach
                        @foreach ($products->level3 as $lv3)
                            <option value="{{ $lv3->id }}">{{ $lv3->name }}</option>
                        @endforeach
                    </select>

                </div>
                <div class="product-grid-kategori" id="productGrid-kategori">
                    @foreach ($products as $product)
                        <div class="product-item" style="margin-top: 10px;">
                            <a href="{{ route('product.show', ['id' => $product->id]) }}" class="product-link">
                                <img src="{{ $product->artwork_url_md[0] }}" alt="Produk">
                                <p title="{{ $product->name }}">{{ substr($product->name, 0, 20) }}...</p>
                                <p>Rp {{ number_format($product->hargaTayang, 0, ',', '.') }}</p>
                                <div class="product-info">
                                    <small
                                        title="{{ $product->namaToko }}">{{ substr($product->namaToko, 0, 6) }}...</small>
                                    <small>{{ $product->count_sold }} terjual</small>
                                    <small>{{ $product->province_name }}</small>
                                </div>
                            </a>
                        </div>
                    @endforeach
                    <div class="button-container">
                        <button class="load-more-button" id="loadMoreButton">
                            >>
                        </button>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
        <div class="product-toko" id="product-content" style="display: none;">
            <div class="product-toko-detail">
                <div class="kategori-toko">
                    <h2>Etalase</h2>
                    <ol id="list-etalase" data-id="0" data-idshop="{{ $id }}">Semua</ol>
                    @foreach ($etalsetoko as $et)
                        <ol id="list-etalase" data-id="{{ $et->id }}">{{ $et->name }}</ol>
                    @endforeach
                </div>
                <div class="product-grid-kategori" id="productGrid">
                    @foreach ($products as $product)
                        <div class="product-item" style="margin-top: 10px;">
                            <a href="{{ route('product.show', ['id' => $product->id]) }}" class="product-link">
                                <img src="{{ $product->artwork_url_md[0] }}" alt="Produk">
                                <p title="{{ $product->name }}">{{ substr($product->name, 0, 20) }}...</p>
                                <p>Rp {{ number_format($product->hargaTayang, 0, ',', '.') }}</p>
                                <div class="product-info">
                                    <small
                                        title="{{ $product->namaToko }}">{{ substr($product->namaToko, 0, 6) }}...</small>
                                    <small>{{ $product->count_sold }} terjual</small>
                                    <small>{{ $product->province_name }}</small>
                                </div>
                            </a>
                        </div>
                    @endforeach
                    <div class="button-container">
                        <button class="load-more-button" id="loadMoreButton">
                            >>
                        </button>
                    </div>
                </div>
            </div>
            <div style="clear: both;"></div> <!-- Untuk mengatasi masalah float -->
        </div>

    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>
