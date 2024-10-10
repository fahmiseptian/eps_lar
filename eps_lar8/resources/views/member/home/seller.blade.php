<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <div class="toko-profile-card">
            @php
            $avatar = '';
            if ($shopData) {
            $requiresBaseUrl = strpos($shopData->avatar ?? '', 'http') === false;
            $avatar = $requiresBaseUrl ? "https://eliteproxy.co.id/" . ($shopData->avatar ?? '') : ($shopData->avatar ?? '');

            $requrl = strpos($shopData->image_banner, 'http') === false;
            $image_banner = $requrl ? "https://eliteproxy.co.id/" . $shopData->image_banner : $shopData->image_banner;
            }

            if ($shopData->id == '161') {
            $requiresBaseUrl = strpos($shopData->avatar ?? '', 'http') === false;
            $avatar = $requiresBaseUrl ? "https://eliteproxy.co.id/seller_center/" . ($shopData->avatar ?? '') : ($shopData->avatar ?? '');
            }
            @endphp
            <div class="toko-profile-banner" style="background-image: url('{{ $image_banner }}');">
                <div class="toko-profile-content">
                    <div class="toko-avatar">
                        <img src="{{ $avatar }}" alt="Avatar Toko">
                    </div>
                    <div class="toko-actions">
                        <h3 class="toko-name">{{ $shopData->nama_pt ?? 'Nama Toko Tidak Tersedia' }}</h3>
                        <div class="toko-buttons">
                            <button class="btn-chat">Chat</button>
                            <button class="btn-follow">Ikuti</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="toko-profile-right">
                <div class="toko-stats">
                    <div class="stat-item">
                        <span class="stat-value">{{ number_format($jmlhproduct ?? 0) }}</span>
                        <span class="stat-label">Produk</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{ number_format($jmlhTerjualproduct ?? 0) }}</span>
                        <span class="stat-label">Terjual</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="rating-stars" style="color: {{ $i <= $rate_toko ? 'yellow' : 'lightgray' }};">★</span>
                        @endfor
                        </span>
                        <span class="stat-label">Rating</span>
                    </div>
                </div>
                <div class="toko-location">
                    <i class="fas fa-map-marker-alt"></i> {{ $shopData->city_name ?? 'Lokasi Tidak Tersedia' }}
                </div>
            </div>
        </div>
        <ul id="menu-list">
            <li id="dashboard-tab" class="active">Dashboard</li>
            <li id="product-tab">Product</li>
        </ul>
        <div class="dashboard-toko" id="dashboard-content">
            <p><b>Product Terbaru</b></p>
            <div class="Product-terbaru-container">
                <button class="scroll-button scroll-left" aria-label="Scroll left">&lt;</button>
                <button class="scroll-button scroll-right" aria-label="Scroll right">&gt;</button>
                <div class="Product-terbaru">
                    @if (!empty($NewProduct))
                    @foreach ($NewProduct as $product)
                    <div class="product-item-terbaru">
                        <a href="{{ route('product.show', ['id' => $product->id , 'token' => $token]) }}" class="product-link">
                            @php
                            $requiresBaseUrl = strpos($product->image ?? '', 'http') === false;
                            $image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . ($product->image ?? '') : ($product->image ?? '');
                            @endphp
                            <img src="{{ $image ?? '' }}" alt="{{ $product->name }}">
                            <p title="{{ $product->name }}">{{ Str::limit($product->name, 20) }}</p>
                            <p class="price">Rp {{ number_format($product->hargaTayang ?? 0, 0, ',', '.') }}</p>
                        </a>
                    </div>
                    @endforeach
                    @else
                    <p>Tidak ada produk terbaru.</p>
                    @endif
                </div>
            </div>
            <div class="product-toko-detail">
                <div class="kategori-toko-detail">
                    <h2>Kategori</h2>
                    <ol>
                        <li class="category-item-shop" id="list-etalase" data-id="0" data-idshop="{{ $shopData->id ?? '' }}">Semua</li>
                        @foreach ($products->level2 ?? [] as $lv2)
                        <li class="category-item-shop" id="list-category" data-id="{{ $lv2->id ?? '' }}" data-idshop="{{ $shopData->id ?? '' }}">
                            {{ $lv2->name ?? '' }}
                        </li>
                        @endforeach
                        @foreach ($products->level3 ?? [] as $lv3)
                        <li class="category-item-shop" id="list-category" data-id="{{ $lv3->id ?? '' }}" data-idshop="{{ $shopData->id ?? '' }}">
                            {{ $lv3->name ?? '' }}
                        </li>
                        @endforeach
                    </ol>
                </div>
                <div class="kategori-toko-detail-mobile">
                    <select id="categoryDropdown" data-idshop="{{ $shopData->id ?? '' }}">
                        <option value="0">Semua Kategori</option>
                        @foreach ($products->level2 ?? [] as $lv2)
                        <option value="{{ $lv2->id ?? '' }}">{{ $lv2->name ?? '' }}</option>
                        @endforeach
                        @foreach ($products->level3 ?? [] as $lv3)
                        <option value="{{ $lv3->id ?? '' }}">{{ $lv3->name ?? '' }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="product-content">
                    <div class="product-options">
                        <div class="product-sort">
                            <label for="sortOrder">Urutkan:</label>
                            <select id="sortOrder">
                                <option value="terbaru">Terbaru</option>
                                <option value="h_terendah">Rendah ke Tinggi</option>
                                <option value="h_tertinggi">Tinggi ke Rendah</option>
                                <option value="terjual">Terpopuler</option>
                            </select>
                        </div>
                        <div class="product-search">
                            <input type="text" id="searchProduct" data-idshop="{{ $shopData->id ?? '' }}" placeholder="Cari produk...">
                        </div>
                    </div>
                    <div class="product-grid-kategori" id="productGrid-kategori">
                        @foreach ($products ?? [] as $product)
                        <div class="product-item-category">
                            <a href="{{ route('product.show', ['id' => $product->id ?? ''  , 'token' => $token]) }}" class="product-link">
                                @php
                                $requiresBaseUrl = strpos($product->image ?? '', 'http') === false;
                                $image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . ($product->image ?? '') : ($product->image ?? '');
                                @endphp
                                <img src="{{ $image }}" alt="{{ $product->name ?? 'Produk' }}">
                                <div class="product-info">
                                    <p class="product-name" title="{{ $product->name ?? '' }}">{{ Str::limit($product->name ?? '', 30) }}</p>
                                    <p class="product-price">Rp {{ number_format($product->hargaTayang ?? 0, 0, ',', '.') }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="product-toko" id="product-content" style="display: none;">
            <div class="product-toko-detail">
                <div class="kategori-toko">
                    <h2>Etalase</h2>
                    <ol id="list-etalase" data-id="0" data-idshop="{{ $shopData->id ?? '' }}">Semua</ol>
                    @foreach ($etalsetoko ?? [] as $et)
                    <ol id="list-etalase" data-id="{{ $et->id ?? '' }}">{{ $et->name ?? '' }}</ol>
                    @endforeach
                </div>
                <div class="product-grid-kategori" id="productGrid-kategori">
                    @foreach ($products ?? [] as $product)
                    <div class="product-item-category">
                        <a href="{{ route('product.show', ['id' => $product->id ?? ''  , 'token' => $token]) }}" class="product-link">
                            @php
                            $requiresBaseUrl = strpos($product->image ?? '', 'http') === false;
                            $image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . ($product->image ?? '') : ($product->image ?? '');
                            @endphp
                            <img src="{{ $image }}" alt="{{ $product->name ?? 'Produk' }}">
                            <div class="product-info">
                                <p class="product-name" title="{{ $product->name ?? '' }}">{{ Str::limit($product->name ?? '', 30) }}</p>
                                <p class="product-price">Rp {{ number_format($product->hargaTayang ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </main>

    @include('member.asset.footer')

    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const containerWrapper = document.querySelector('.Product-terbaru-container');
            const container = document.querySelector('.Product-terbaru');
            const leftBtn = document.querySelector('.scroll-left');
            const rightBtn = document.querySelector('.scroll-right');

            leftBtn.addEventListener('click', () => {
                container.scrollBy({
                    left: -200,
                    behavior: 'smooth'
                });
            });

            rightBtn.addEventListener('click', () => {
                container.scrollBy({
                    left: 200,
                    behavior: 'smooth'
                });
            });

            // Fungsi untuk mengecek apakah tombol perlu ditampilkan
            function toggleScrollButtons() {
                leftBtn.style.display = container.scrollLeft > 0 ? 'block' : 'none';
                rightBtn.style.display = container.scrollLeft < container.scrollWidth - container.clientWidth ? 'block' : 'none';
            }

            // Event listener untuk scroll dan resize
            container.addEventListener('scroll', toggleScrollButtons);
            window.addEventListener('resize', toggleScrollButtons);

            // Tampilkan/sembunyikan tombol saat hover
            containerWrapper.addEventListener('mouseenter', () => {
                toggleScrollButtons();
            });

            containerWrapper.addEventListener('mouseleave', () => {
                leftBtn.style.display = 'none';
                rightBtn.style.display = 'none';
            });

            // Panggil fungsi saat halaman dimuat
            toggleScrollButtons();
        });
    </script>
    {{-- <script src="{{ secure_asset('/js/function/member/home.js') }}" type="text/javascript"></script> --}}
</body>

</html>