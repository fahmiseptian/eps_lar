<!-- Header / Navbar -->
<link href="{{ asset('/css/navbar.css') }}" rel="stylesheet" type="text/css" />
<header>
    <div class="navbar">
        <div class="navbar-left">
            <div class="logo">
                <a href="{{ route('home') }}">
                    <img id="site-logo" src="{{ asset('/img/app/logo-eps.png') }}" alt="Logo" height="40px">
                </a>
            </div>
            <!-- <div class="category-dropdown">
                <button class="category-btn">Kategori <span class="material-icons">arrow_drop_down</span></button>
            </div> -->
        </div>
        <div class="search-container">
            <div class="search-bar">
                <input type="text" id="search-input" placeholder="Cari produk, toko, dan kategori">
                <button id="search-button"><span class="material-icons">search</span></button>
            </div>
            <div id="search-results" style="display: none;">
                <div id="search-loading" style="display: none;">Melakukan pencarian...</div>
                <div id="quick-search-results"></div>
            </div>
        </div>
        <div class="navbar-right">
            <a href="{{ $id_user ? route('cart') : route('login') }}" class="icon-btn">
                <span class="material-icons">shopping_cart</span>
                <span>Keranjang</span>
            </a>
            <div class="user-menu">
                <a href="{{ $id_user ? route('profile') : route('login') }}" class="user-btn">
                    <span class="material-icons">account_circle</span>
                    <span>{{ $id_user != null ? $nama_user : 'Login' }}</span>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
    const appUrl = "{{ env('APP_URL') }}";
    window.appUrl = appUrl;
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
</script>