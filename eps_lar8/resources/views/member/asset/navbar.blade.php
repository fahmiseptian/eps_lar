<!-- Header / Navbar -->
<header>
    <div class="navbar">
        <div class="logo">
            <a href="{{ route('home') }}">
                <img id="site-logo" src="{{ asset('/img/app/logo-eps.png') }}" alt="Logo" height="40px">
            </a>
        </div>
        <div class="logo-mobile">
            <a href="{{ route('home') }}">
                <img id="site-logo" src="{{ asset('/img/app/logo eps.png') }}" alt="Logo" height="40px">
            </a>
        </div>
        <div class="search-bar">
            <input type="text" placeholder="Cari produk, toko, dan kategori">
            <button><span class="material-icons">search</span></button>
        </div>
        <div class="account">
            @if ($id_user == null)
                <a href="{{ route('login') }}"><span class="material-icons">account_circle</span></a>
                <a href="{{ route('login') }}"><span class="material-icons">shopping_cart</span></a>
            @else
                <a href="{{ route('profile') }}"><span
                        class="material-icons">account_circle</span></a>
                <a href="{{ route('cart') }}"><span class="material-icons">shopping_cart</span></a>
            @endif
        </div>
    </div>
</header>

<script>
    const appUrl = "{{ env('APP_URL') }}";
    window.appUrl = appUrl;
</script>
