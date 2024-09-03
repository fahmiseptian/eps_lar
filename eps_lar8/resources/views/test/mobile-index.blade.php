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
            <p style="text-align: center; color:#ffffff; margin-top:-3px"> <b> {{session()->get('seller')}} </b></p>
        </div>
    </div>

    <div id="mobile-content">
        <p style="margin: 10px"><b>Selamat Datang, {{session()->get('seller')}}</b></p>
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
                <p style="font-size:20px;margin:0">100</p>
                <p style="font-size:7px;margin:0">Belum Direspon</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">100</p>
                <p style="font-size:7px;margin:0">Nego Ulang</p>
            </div>
        </div>
        <div id="mini-box-item">
            <div>
                <p style="font-size:20px;margin:0">100</p>
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
                <span> <b>Rp. {{ number_format(1800000, 0, '.', '.') }} </b></span>
            </div>
        </div>
        <div id="mobile-box-item">
            <div id="info-saldo">
                <p style="text-align: left"><b> Total Pendapatan Kamu </b> </p>
                <span> <b>Rp. {{ number_format(99000000, 0, '.', '.') }} </b></span>
                <p id="info-saldo-order"><i class="material-symbols-outlined" id="google-icon">shopping_bag</i>9 Pesanan </p>
            </div>
        </div>
    </div>

    <div id="mobile-kemajuan-text">
        <p><b><i>Baru Saja Terjual</i></b></p>
    </div>

    <div id="mini-image-order">
        <div id="item-mini-image-order">
            <img src="https://s3-alpha-sig.figma.com/img/1d2a/3905/b944b9d9ddcf5a96308726bb357eb60b?Expires=1722211200&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=OgqfohNaggUkK2kAMoGVOoel85BTEEXyrF7fn9xMhE8rPbTttqqfR-LAUXJKEsYEwnn4v4unfV9JUVx5ASW7j6NnmamkuEu1l7qn6QgLThNEfX-9O2InKQslQfg~cpO0jKvYTrXNhIvYTO2URabD65sY4dU159Crb6LdI8Z2gGPAqRMsQRRjSuXctULFeOquASEc6goV97COQKvOok0cNuC-DthqTGN51Ee8l4yODb8K0TFtrTYq3UOrc3eDmP3l7z~LKvFoLJnaujCSliIDeCEZhp47u3UQ7frCYAUmZqsOhz9cf~dcIgufXCnW9pDumIVjaKUtg62i0nzAGucFJA__"
                alt="Product-order">
        </div>
        <div id="item-mini-image-order">
            <img src="https://s3-alpha-sig.figma.com/img/1d2a/3905/b944b9d9ddcf5a96308726bb357eb60b?Expires=1722211200&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=OgqfohNaggUkK2kAMoGVOoel85BTEEXyrF7fn9xMhE8rPbTttqqfR-LAUXJKEsYEwnn4v4unfV9JUVx5ASW7j6NnmamkuEu1l7qn6QgLThNEfX-9O2InKQslQfg~cpO0jKvYTrXNhIvYTO2URabD65sY4dU159Crb6LdI8Z2gGPAqRMsQRRjSuXctULFeOquASEc6goV97COQKvOok0cNuC-DthqTGN51Ee8l4yODb8K0TFtrTYq3UOrc3eDmP3l7z~LKvFoLJnaujCSliIDeCEZhp47u3UQ7frCYAUmZqsOhz9cf~dcIgufXCnW9pDumIVjaKUtg62i0nzAGucFJA__"
                alt="Product-order">
        </div>
        <div id="item-mini-image-order">
            <img src="https://s3-alpha-sig.figma.com/img/1d2a/3905/b944b9d9ddcf5a96308726bb357eb60b?Expires=1722211200&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=OgqfohNaggUkK2kAMoGVOoel85BTEEXyrF7fn9xMhE8rPbTttqqfR-LAUXJKEsYEwnn4v4unfV9JUVx5ASW7j6NnmamkuEu1l7qn6QgLThNEfX-9O2InKQslQfg~cpO0jKvYTrXNhIvYTO2URabD65sY4dU159Crb6LdI8Z2gGPAqRMsQRRjSuXctULFeOquASEc6goV97COQKvOok0cNuC-DthqTGN51Ee8l4yODb8K0TFtrTYq3UOrc3eDmP3l7z~LKvFoLJnaujCSliIDeCEZhp47u3UQ7frCYAUmZqsOhz9cf~dcIgufXCnW9pDumIVjaKUtg62i0nzAGucFJA__"
                alt="Product-order">
        </div>
    </div>
</body>

</html>
