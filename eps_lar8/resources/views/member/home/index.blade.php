<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <!-- Banner Section -->
        <section class="banner-section">
            <!-- Banner besar -->
            <div class="banner-carousel">
                @foreach ($banners as $banner)
                @php
                $requiresBaseUrl = strpos($banner->image, 'http') === false;
                $newBanner = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $banner->image : $banner->image;
                @endphp
                <div class="banner-item">
                    <img src="{{$newBanner}}" alt="Banner Besar 1">
                </div>
                @endforeach

            </div>

            <!-- Banner kecil -->
            <div class="banner-small">
                @foreach ($chill_banner as $banner)
                @php
                $requiresBaseUrl = strpos($banner->image, 'http') === false;
                $chill_banner = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $banner->image : $banner->image;
                @endphp
                <img src="{{$chill_banner}}" class="banner-small-img" alt="Banner Kecil">
                @endforeach
            </div>
        </section>

        <!-- Kategori -->
        <section class="categories">
            <h2>Kategori</h2>
            <div class="category-container">
                <button class="nav-btn prev-btn">&lt;</button>
                <div class="category-carousel">
                    @foreach ($banner_product_category as $pc)
                    @php
                    $requiresBaseUrl = strpos($pc->icon, 'http') === false;
                    $icon = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$pc->icon :$pc->icon;
                    @endphp
                    <div class="category-item" data-category="{{$pc->code}}">
                        <img src="{{ $icon}}" alt="{{ $pc->code }}">
                        <p>{{ $pc->name }}</p>
                    </div>
                    @endforeach
                </div>
                <button class="nav-btn next-btn">&gt;</button>
            </div>
        </section>

        <section class="popular-searches">
            <div class="popular-searches-header">
                <h2>Lagi Hits, nih</h2>
                <div class="reload-search" id="reloadHits">
                    <span class="material-icons">refresh</span>
                    <span>Cari lainnya</span>
                </div>
            </div>
            <div class="popular-searches-grid" id="popularSearchesGrid">

                @foreach ($random_search as $rs)
                @php
                $requiresBaseUrl = strpos($rs->image, 'http') === false;
                $image = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$rs->image :$rs->image;
                @endphp
                <div class="popular-search-item" data-keyword="{{$rs->keyword}}">
                    <img src="{{$image}}" alt="{{$rs->keyword}}">
                    <div class="search-info">
                        <p class="keyword">{{$rs->keyword}}</p>
                        <p class="product-count">{{$rs->search_count}} Produk</p>
                    </div>
                </div>
                @endforeach
            </div>
            <hr>
        </section>

        <!-- Promo -->
        <section class="promos">
            <div class="promo-container">
                <h2>Promo Menarik</h2>
                <div class="promo-grid">
                    @foreach ($promos as $promo)
                    @php
                    $requiresBaseUrl = strpos($promo->icon, 'http') === false;
                    $promo_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" . $promo->icon : $promo->icon;
                    @endphp
                    <div class="promo-item">
                        <img src="{{$promo_image}}" alt="{{ $promo->code }}">
                        <p>{{ $promo->name }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Flash Sale -->
        <!-- <section class="flash-sale">
            <p>Flash Sale</p>
            <div class="flash-sale-countdown">
                <span id="countdown-hours">00</span>:
                <span id="countdown-minutes">00</span>:
                <span id="countdown-seconds">00</span>
            </div>
        </section> -->


        <!-- Pencarian Terpopuler -->
        <section class="popular-searches">
            <h2>Pencarian Terpopuler</h2>
            <div class="popular-searches-grid">
                @foreach ($product_search as $ps)
                @php
                $requiresBaseUrl = strpos($ps->image, 'http') === false;
                $pc_image = $requiresBaseUrl ? "https://eliteproxy.co.id/" .$ps->image : $ps->image;
                @endphp
                <div class="popular-search-item" data-keyword="{{$ps->keyword}}">
                    <img src="{{$pc_image}}" alt="{{$ps->keyword}}">
                    <div class="search-info">
                        <p class="keyword">{{$ps->keyword}}</p>
                        <p class="product-count">{{$ps->search_count}} Produk</p>
                    </div>
                </div>
                @endforeach
                <!-- Tambahkan item pencarian terpopuler lainnya -->
            </div>
            <hr>
        </section>

        <!-- Produk Populer -->
        <section class="products">
            <div class="product-grid" id="productGrid">
                @include('member.home.product-list', ['products' => $products])
            </div>
            <button id="moreproduct" class="load-more-button">Muat Lebih Banyak Produk</button>
            <!-- <div class="loading" style="display: none;">
                <p>Loading...</p>
            </div> -->
        </section>
    </main>

    @include('member.asset.footer')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const categoryContainer = document.querySelector(".category-container");
            const categoryCarousel = document.querySelector(".category-carousel");
            const prevBtn = document.querySelector(".prev-btn");
            const nextBtn = document.querySelector(".next-btn");

            let scrollAmount = 0;
            const step = 200; // Jumlah pixel untuk di-scroll setiap klik

            function updateButtonVisibility() {
                prevBtn.style.display = scrollAmount <= 0 ? "none" : "block";
                nextBtn.style.display =
                    scrollAmount >=
                    categoryCarousel.scrollWidth - categoryCarousel.clientWidth ?
                    "none" :
                    "block";
            }

            nextBtn.addEventListener("click", function() {
                scrollAmount += step;
                if (
                    scrollAmount >
                    categoryCarousel.scrollWidth - categoryCarousel.clientWidth
                ) {
                    scrollAmount =
                        categoryCarousel.scrollWidth - categoryCarousel.clientWidth;
                }
                categoryCarousel.style.transform = `translateX(-${scrollAmount}px)`;
                updateButtonVisibility();
            });

            prevBtn.addEventListener("click", function() {
                scrollAmount -= step;
                if (scrollAmount < 0) {
                    scrollAmount = 0;
                }
                categoryCarousel.style.transform = `translateX(-${scrollAmount}px)`;
                updateButtonVisibility();
            });

            // Sembunyikan tombol saat halaman dimuat
            updateButtonVisibility();

            // Tampilkan tombol saat mouse masuk ke area carousel
            categoryContainer.addEventListener("mouseenter", function() {
                updateButtonVisibility();
            });

            // Sembunyikan tombol saat mouse meninggalkan area carousel
            categoryContainer.addEventListener("mouseleave", function() {
                prevBtn.style.display = "none";
                nextBtn.style.display = "none";
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            const reloadButton = document.getElementById("reloadHits");
            const searchesGrid = document.getElementById("popularSearchesGrid");

            reloadButton.addEventListener("click", function() {
                // Tampilkan indikator loading
                searchesGrid.innerHTML = "<p>Memuat...</p>";

                fetch(appUrl + "/api/refresh-hits")
                    .then((response) => response.json())
                    .then((data) => {
                        searchesGrid.innerHTML = "";
                        data.data.forEach((item) => {
                            var imageold = item.image;
                            var requiresBaseUrl = imageold.indexOf("http") === -1;
                            var image = requiresBaseUrl ?
                                "http://eliteproxy.co.id/" + imageold :
                                imageold;

                            const searchItem = document.createElement("div");
                            searchItem.className = "popular-search-item";
                            searchItem.innerHTML = `
                        <img src="${image}" alt="${item.keyword}">
                        <div class="search-info">
                            <p class="keyword">${item.keyword}</p>
                            <p class="product-count">${item.search_count} produk</p>
                        </div>
                    `;
                            searchesGrid.appendChild(searchItem);
                        });
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        searchesGrid.innerHTML =
                            "<p>Terjadi kesalahan saat memuat data.</p>";
                    });
            });
        });
    </script>
    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
    <script>
        let page = 1;

        function loadMoreProducts(page) {
            $.ajax({
                    url: "{{ route('products.get') }}?page=" + page,
                    type: "get",
                    beforeSend: function() {
                        $('.load-more-button').text('Memuat...');
                    }
                })
                .done(function(data) {
                    if (data.length === 0) {
                        $('.loading').html("<p align='center'>Semua Produk sudah ditampilkan</p>");
                        $('#moreproduct').hide(); // Sembunyikan tombol
                        return;
                    }
                    $('.load-more-button').text('Muat Lebih Banyak Produk');
                    $('.loading').hide();
                    $('#productGrid').append(data);
                })
                .fail(function(jqXHR, ajaxOptions, thrownError) {
                    alert('Terjadi kesalahan server');
                });
        }

        $('#moreproduct').click(function(e) {
            e.preventDefault();
            page++;
            loadMoreProducts(page);
        });

        // $(window).scroll(function() {
        //     if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
        //         page++;
        //         loadMoreProducts(page);
        //     }
        // });
    </script>
</body>

</html>