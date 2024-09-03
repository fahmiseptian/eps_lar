<div class="col-left">
    <a href="{{ route('seller') }}" class="logo"><img style="width: 180px; margin:5px; margin-top:20px"
            src="{{ asset('/img/app/logo-eps.png') }}" />
    </a>
    <br>
    <a href="{{ route('seller') }}" class="profile">
        <img src="{{ asset('/img/app/profile_toko.png') }}" />
    </a>
    <p style="text-align: center; color:#ffffff"> <b> <?= session()->get('seller') ?> </b></p>
    <ul class="sidebar-menu">
        <li class="list-sidebar">
            <a href="{{ route('seller') }}">
                <i class="material-symbols-outlined" id="google-icon">dashboard </i> <span>Dashboard</span>
            </a>
        </li>
        <li class="list-sidebar">
            <a href="#">
                <i class="material-symbols-outlined" id="google-icon">package </i> <span>Pengiriman</span>
                <i class="material-symbols-outlined pull-right" id="google-icon">arrow_drop_down </i>
            </a>
            <ul class="treeview-menu">
                <li><a href="{{ route('seller.delivery') }}"></i> Jasa Pengiriman</a></li>
                <li><a href="{{ route('seller.delivery.free-ongkir') }}"></i> Free Ongkir</a></li>
            </ul>
        </li>
        <li class="list-sidebar">
            <a href="{{ route('seller.order') }}">
                <i class="fa fa-tasks"></i> <span>Pesanan Saya</span>
            </a>
        </li>
        <li class="list-sidebar">
            <a href="#">
                <img src="{{ asset('/img/app/icon_product.png') }}" width="20px" /> <span>Produk
                    Saya</span> <i class="material-symbols-outlined pull-right" id="google-icon">arrow_drop_down </i>
            </a>
            <ul class="treeview-menu">
                <li><a style="color: #eee" href="{{ route('seller.product') }}">Produk</a></li>
                <li><a style="color: #eee" href="{{ route('seller.product.add') }}">Tambah Produk</a></li>
                {{-- <li><a style="color: #eee" href="{{ route('seller.product.violation') }}">Pelanggaran Saya</a></li> --}}
            </ul>

        </li>
        {{-- <li class="list-sidebar">
            <a href="{{ route('seller.promosi') }}">
                <i class="fa fa-tags"></i> <span>Promosi</span>
            </a>
        </li> --}}
        <li class="list-sidebar">
            <a href="{{ route('seller.nego') }}">
                <img src="{{ asset('/img/app/icon_negosiasi.png') }}" width="20px" /> </i>
                <span>Negosiasi</span>
            </a>
        </li>
        <li class="list-sidebar">
            <a href="#">
                <img src="{{ asset('/img/app/icon_dompet.png') }}" width="20px" /> <span>Keuangan</span>
                <i class="material-symbols-outlined pull-right" id="google-icon">arrow_drop_down </i>
            </a>
            <ul class="treeview-menu">
                <li><a style="color: #eee" href="{{ route('seller.finance') }}">Penghasilan</a></li>
                <li><a style="color: #eee" href="{{ route('seller.finance.saldo') }}"> Saldo</a></li>
                <li><a style="color: #eee" href="{{ route('seller.finance.rekening') }}"> Rekening</a></li>
                {{-- <li><a style="color: #eee" href="{{ route('seller.finance.pembayaran') }}"> Pembayaran</a> --}}
                </li>
            </ul>
        </li>
        <li class="list-sidebar">
            <a href="#">
                <img src="{{ asset('/img/app/icon_data.png') }}" width="20px" /> <span>Bisnis Saya</span>
                <i class="material-symbols-outlined pull-right" id="google-icon">arrow_drop_down </i>
            </a>
            <ul class="treeview-menu">
                <li><a style="color: #eee" href="{{ route('seller.health') }}"></i> Kesehatan Toko</a></li>
                <li><a style="color: #eee" href="{{ route('seller.health.info-toko') }}"></i> Bisnis</a></li>
        </li>
    </ul>
    <li class="list-sidebar">
        <a href="#">
            <img src="{{ asset('/img/app/icon_toko.png') }}" width="20px" /> <span>Toko</span> <i
                class="material-symbols-outlined pull-right" id="google-icon">arrow_drop_down </i>
        </a>
        <ul class="treeview-menu">
            <li><a style="color: #eee" href="{{ route('seller.shop') }}">Penilaian</a></li>
            <li><a style="color: #eee" href="{{ route('seller.shop.asistent-chat') }}">Asistent Chat</a></li>
            <li><a style="color: #eee" href="{{ route('seller.shop.profile') }}">Profil</a></li>
            <li><a style="color: #eee" href="{{ route('seller.shop.etalase')}}">Etalase</a>
            </li>
        </ul>
    </li>
    <li class="list-sidebar">
        <a href="#">
            <img src="{{ asset('/img/app/icon_setting.png') }}" width="20px" />
            <span>Pengaturan</span> <i class="material-symbols-outlined pull-right" id="google-icon">arrow_drop_down
            </i>
        </a>
        <ul class="treeview-menu">
            <li>
                <a style="color: #eee" href="{{ route('seller.setting.address') }}"> Alamat</a>
            </li>
            <li>
                <a style="color: #eee" href="{{ route('seller.setting') }}">Pengaturan Toko</a>
            </li>
        </ul>
    </li>
    </ul>
    <div id="button-exit">
        <a href="#" onclick="confirmLogout(this)" role="button" style="color:#039be5 ">
            <p style="margin-top:12%">Keluar</p>
        </a>
    </div>
</div>

<div id="overlay" style="display: none;">
    <div class="overlay-content">
        <div id="loader" class="loader" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

<script>
    const appUrl = "{{ env('APP_URL') }}";
    window.appUrl = appUrl;
</script>
