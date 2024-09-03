<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body>
    <div id="mobile-top-bar">
        <div id="mobile-item-top-bar">
            <a href="{{ route('seller') }}" class="logo">
                <img style="width: 110px;height:35px; margin-top:7px " src="{{ asset('/img/app/logo-eps.png') }}" />
            </a>
            <i style="margin-left: 10px; font-size: 30px;" class="material-symbols-outlined" id="google-icon">menu</i>
        </div>
        <div id="mobile-item-nama-toko">
            <a href="{{ route('seller') }}" class="profile">
                <img style="width: 50px;height:50px; margin-top:7px " src="{{ asset('/img/app/profile_toko.png') }}" />
            </a>
            <p style="text-align: center; color:#ffffff; margin-top:-3px"> <b> {{ session()->get('seller') }} </b></p>
        </div>
    </div>

    <div id="mobile-content">
        <p style="margin: 10px"><b>Selamat Datang, {{ session()->get('seller') }}</b></p>
        <div id="mobile-image-dashboard">
            <img src="{{ asset('/img/app/dasboard_toko.png') }}" style="width: 100%; height: 80px; margin-top: 20px;" />
        </div>
    </div>

    <div id="mini-box">
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $jmlhproduct ?? '' }}</p>
                <p style="font-size:7px;margin:0">Produk Aktif</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $neworder ?? '' }}</p>
                <p style="font-size:7px;margin:0">Pesanan Baru</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $ondelivery ?? '' }}</p>
                <p style="font-size:7px;margin:0">Pengiriman</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $product_habis ?? '' }}</p>
                <p style="font-size:7px;margin:0">Produk Habis</p>
            </div>
        </div>
    </div>

    <div id="mobile-nego-text">
        <p><b><i>Negosiasi Barang Bela Pengadaan</i></b></p>
    </div>

    <div id="mini-box">
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $newNego }}</p>
                <p style="font-size:7px;margin:0">Belum Direspon</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $NegoUlang }}</p>
                <p style="font-size:7px;margin:0">Nego Ulang</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">{{ $Nego }}</p>
                <p style="font-size:7px;margin:0">Telah Direspon</p>
            </div>
        </div>
    </div>

    <div id="mobile-kemajuan-text">
        <p><b><i>Kemajuan Kamu</i></b></p>
    </div>

    <div id="mini-box">
        <div id="mobile-box-item">
            <div id="info-saldo">
                <p style="text-align: left"><b> Saldo Kamu </b> </p>
                <span> <b>Rp. {{ number_format($saldo, 0, '.', '.') }} </b></span>
            </div>
        </div>
        <div id="mobile-box-item">
            <div id="info-saldo">
                <p style="text-align: left"><b> Total Pendapatan Kamu </b> </p>
                <span> <b>Rp. {{ number_format($dataPendapatan['total_diterima_seller'], 0, '.', '.') }} </b></span>
                <p id="info-saldo-order"><i class="material-symbols-outlined" id="google-icon">shopping_bag</i>
                    {{ $dataPendapatan['order'] }} Pesanan </p>
            </div>
        </div>
    </div>

    <div id="mobile-kemajuan-text">
        <p><b><i>Baru Saja Terjual</i></b></p>
    </div>

    <div id="mini-image-order">
        @if (!empty($lastOrder))
            @foreach ($lastOrder as $lO)
                <?php
                // Cek apakah URL image300 sudah mengandung 'http'
                $requiresBaseUrl = strpos($lO->image, 'http') === false;
                // Tentukan URL yang digunakan
                $image = $requiresBaseUrl ? 'http://eliteproxy.co.id/' . $lO->image : $lO->image;
                ?>
                <div id="item-mini-image-order">
                    <img src="{{ $image }}" alt="{{ $lO->nama }}">
                </div>
            @endforeach
        @else
            <p>Belum Ada Produk Terjual</p>
        @endif
    </div>
</body>

</html>
