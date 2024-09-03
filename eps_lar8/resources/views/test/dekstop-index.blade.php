<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body>
    <div class="container-fluid">
        <div class="row">
            @include('seller.asset.desktop.sidebar')
            <div class="col-middle">
                {{-- content --}}
                <div id="box-left">
                    <div id="notif">
                        <img src="{{ asset('/img/app/icon_lonceng.png') }}" width="50px"
                            style="margin-left: 10px;">
                        <img src="{{ asset('/img/app/icon_chat.png') }}" width="50px">
                    </div>
                    <div id="image-dashboard">
                        <p> <b> Selamat Datang, (Nama Toko) </b> </p>
                        <img src="{{ asset('/img/app/dasboard_toko.png') }}"; height="260px">
                    </div>
                    <div id="mini-box">
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">100</p>
                                <p style="font-size:15px;margin:0">Produk Aktif</p>
                            </div>
                        </div>
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">100</p>
                                <p style="font-size:15px;margin:0">Pesanan Baru</p>
                            </div>
                        </div>
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">100</p>
                                <p style="font-size:15px;margin:0">Pengiriman</p>
                            </div>
                        </div>
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">100</p>
                                <p style="font-size:15px;margin:0">Produk Habis</p>
                            </div>
                        </div>
                    </div>
                    <div id="dashboard-nego">
                        <p style="margin-left: 30px; margin-top:40px "><b> <i> Negosiasi Barang Bela Pengadaan </i> </b></p>
                        <div id="box">
                            <div id="box-item">
                                <div>
                                    <p style="font-size:52px;margin:0">100</p>
                                    <p style="font-size:18px;margin:0">Belum Direspon</p>
                                </div>
                            </div>
                            <div id="box-item">
                                <div>
                                    <p style="font-size:52px;margin:0">100</p>
                                    <p style="font-size:18px;margin:0">Nego Ulang</p>
                                </div>
                            </div>
                            <div id="box-item">
                                <div>
                                    <p style="font-size:52px;margin:0">100</p>
                                    <p style="font-size:18px;margin:0">Telah Direspon</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="box-right">
                    <p><b>Kemajuan Kamu</b></p>
                    <div id="box-info">
                        <div id="info-saldo">
                            <p style="text-align: left"><b> Saldo Kamu </b> </p>
                            <span> <b>Rp. {{ number_format(1800000, 0, '.', '.') }} </b></span>
                        </div>
                    </div>
                    <div id="box-info">
                        <div id="info-saldo">
                            <p style="text-align: left"><b> Total Pendapatan Kamu </b> </p>
                            <span> <b>Rp. {{ number_format(99000000, 0, '.', '.') }} </b></span>
                            <p id="info-saldo-order"><i class="material-symbols-outlined" id="google-icon">shopping_bag</i>9 Pesanan </p>
                        </div>
                    </div>
                    <div id="info-order">
                        <b> <i>Baru Saja Terjual</i></b>
                        <div id="dashboard-image-order">
                            <div id="big-image-order">
                                <img src="https://s3-alpha-sig.figma.com/img/1d2a/3905/b944b9d9ddcf5a96308726bb357eb60b?Expires=1722211200&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=OgqfohNaggUkK2kAMoGVOoel85BTEEXyrF7fn9xMhE8rPbTttqqfR-LAUXJKEsYEwnn4v4unfV9JUVx5ASW7j6NnmamkuEu1l7qn6QgLThNEfX-9O2InKQslQfg~cpO0jKvYTrXNhIvYTO2URabD65sY4dU159Crb6LdI8Z2gGPAqRMsQRRjSuXctULFeOquASEc6goV97COQKvOok0cNuC-DthqTGN51Ee8l4yODb8K0TFtrTYq3UOrc3eDmP3l7z~LKvFoLJnaujCSliIDeCEZhp47u3UQ7frCYAUmZqsOhz9cf~dcIgufXCnW9pDumIVjaKUtg62i0nzAGucFJA__"
                                    alt="Product-order">
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
                            </div>
                        </div>
                    </div>
                </div>
                {{-- end Content --}}
            </div>

        </div>
    </div>


    @include('seller.asset.footer')
</body>

</html>
