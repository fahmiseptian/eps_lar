<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <div class="search-page">
            <div class="search-filters">
                <h3>Filter</h3>

                <div class="filter-section">
                    <h4>Kategori</h4>
                    <ul class="category-list">
                        @foreach($menuCategories as $category)
                        @if (!empty($category['submenus']))
                        <li class="has-submenu">
                            <a href="#" class="filter-item" data-category="{{ $category['id'] }}">
                                {{ $category['name'] }} <span class="arrow">></span>
                            </a>
                            <ul class="submenu">
                                @foreach($category['submenus'][0] as $submenu)
                                <li>
                                    <a href="#" class="filter-item" data-category="{{ $submenu->id }}">
                                        {{ $submenu->name }} <span class="arrow">></span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        @else
                        <li>
                            <a href="#" class="filter-item" data-category="{{ $category['id'] }}">
                                {{ $category['name'] }} <span class="arrow">></span>
                            </a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>

                <div class="filter-section">
                    <h4>Jenis Toko</h4>
                    <ul>
                        <li><a href="#" class="filter-item" data-store-type="official">Trused Store <span class="arrow">></span></a></li>
                        <li><a href="#" class="filter-item" data-store-type="power">Platinum <span class="arrow">></span></a></li>
                        <li><a href="#" class="filter-item" data-store-type="regular">Gold <span class="arrow">></span></a></li>
                        <li><a href="#" class="filter-item" data-store-type="regular">Silver <span class="arrow">></span></a></li>
                    </ul>
                </div>

                <div class="filter-section">
                    <h4>Harga</h4>
                    <div class="price-inputs">
                        <input type="text" id="price_min" name="price_min" placeholder="Harga Minimum">
                        <input type="text" id="price_max" name="price_max" placeholder="Harga Maksimum">
                    </div>
                </div>

                <div class="filter-section">
                    <h4>Kondisi</h4>
                    <ul>
                        <li><a href="#" class="filter-item" data-condition="Y">Baru <span class="arrow">></span></a></li>
                        <li><a href="#" class="filter-item" data-condition="N">Bekas <span class="arrow">></span></a></li>
                    </ul>
                </div>
            </div>

            <div class="search-results">
                <div class="search-header">
                    <small class="search-result-text">Hasil Pencarian untuk "{{ $keyword }}"</small>
                    <input type="text" id="keyword" value="{{ $keyword }}" hidden>
                    <div class="sort-dropdown">
                        <label for="sort-order"> <b>Urutkan:</b></label>
                        <select id="sort-order">
                            <option value="#">Paling Sesuai</option>
                            <option value="terbaru">Terbaru</option>
                            <option value="h_tertinggi">Harga Tertinggi</option>
                            <option value="terjual">Terjual</option>
                            <option value="h_terendah">Harga Terendah</option>
                        </select>
                    </div>
                </div>

                @if(!empty($stores) && count($stores) > 0)
                <div class="stores-section">
                    <h3>Toko</h3>
                    @foreach($stores as $store)
                    <div class="store-list">
                        <div class="store-card">

                            @php
                            $requiresBaseUrl = strpos($store->avatar, 'http') === false;
                            $avatar = $requiresBaseUrl ? "https://eliteproxy.co.id/seller_center/" .$store->avatar : $store->avatar;
                            @endphp
                            <img src="{{$avatar}}" alt="{{$store->name}}">
                            <h4>{{ $store->name }}</h4>
                        </div>
                    </div>
                    @endforeach
                </div>
                <hr>
                @endif

                <div class="products-section">
                    @if($productsearch->count() > 0)
                    @foreach($productsearch->get() as $product)
                    <div class="product-item">

                        @php
                        $requiresBaseUrl = strpos($product->image300, 'http') === false;
                        $image300 = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$product->image300 : $product->image300;
                        @endphp
                        <a href="{{ route('product.show', ['id' => $product->id]) }}" class="product-link">
                            <img src="{{ $image300}}" alt="{{ $product->name }}">
                            <p title="{{ $product->name }}">{{ Str::limit($product->name, 30) }}</p>
                            <p>Rp {{ number_format($product->hargaTayang, 0, ',', '.') }}</p>
                            <div class="product-info">
                                <small title="{{ $product->namaToko }}">{{ Str::limit($product->namaToko, 20) }}</small>
                                <small>{{ $product->total_sold ?? 0 }} terjual</small>
                                <small>{{ $product->province_name }}</small>
                            </div>
                        </a>
                    </div>
                    @endforeach
                    @else
                    <p>Tidak ada produk yang ditemukan.</p>
                    @endif
                </div>
            </div>
        </div>
    </main>

    @include('member.asset.footer')
    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
</body>

</html>