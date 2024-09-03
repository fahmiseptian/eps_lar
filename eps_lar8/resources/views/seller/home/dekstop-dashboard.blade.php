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
                        <img src="{{ asset('/img/app/icon_lonceng.png') }}" width="50px" style="margin-left: 10px;">
                        <img src="{{ asset('/img/app/icon_chat.png') }}" width="50px">
                    </div>
                    <div id="image-dashboard">
                        <p> <b> Selamat Datang, {{ session()->get('seller') }} </b> </p>
                        <img src="{{ asset('/img/app/dasboard_toko.png') }}"; height="280px">
                    </div>
                    <div id="mini-box">
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">{{ $jmlhproduct ?? '' }}</p>
                                <p style="font-size:15px;margin:0">Produk Aktif</p>
                            </div>
                        </div>
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">{{ $neworder ?? '' }}</p>
                                <p style="font-size:15px;margin:0">Pesanan Baru</p>
                            </div>
                        </div>
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">{{ $ondelivery ?? '' }}</p>
                                <p style="font-size:15px;margin:0">Pengiriman</p>
                            </div>
                        </div>
                        <div id="mini-box-item">
                            <div>
                                <p style="font-size:30px;margin:0">{{ $product_habis ?? '' }}</p>
                                <p style="font-size:15px;margin:0">Produk Habis</p>
                            </div>
                        </div>
                    </div>
                    <div id="dashboard-nego">
                        <p style="margin-left: 30px; margin-top:40px "><b> <i> Negosiasi Barang Bela Pengadaan </i> </b>
                        </p>
                        <div id="box">
                            <div id="box-item">
                                <div>
                                    <p style="font-size:52px;margin:0">{{ $newNego }}</p>
                                    <p style="font-size:18px;margin:0">Belum Direspon</p>
                                </div>
                            </div>
                            <div id="box-item">
                                <div>
                                    <p style="font-size:52px;margin:0">{{ $NegoUlang }}</p>
                                    <p style="font-size:18px;margin:0">Nego Ulang</p>
                                </div>
                            </div>
                            <div id="box-item">
                                <div>
                                    <p style="font-size:52px;margin:0">{{ $Nego }}</p>
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
                            <span> <b>Rp. {{ number_format($saldo, 0, ',', '.') }} </b></span>
                        </div>
                    </div>
                    <div id="box-info">
                        <div id="info-saldo">
                            <p style="text-align: left"><b> Total Pendapatan Kamu </b> </p>
                            <span> <b>Rp. {{ number_format($dataPendapatan['total_diterima_seller'], 0, '.', '.') }}
                                </b></span>
                            <p id="info-saldo-order"><i class="material-symbols-outlined"
                                    id="google-icon">shopping_bag</i>{{ $dataPendapatan['order'] }} Pesanan </p>
                        </div>
                    </div>
                    <div id="info-order">
                        <b> <i>Baru Saja Terjual</i></b>
                        <div id="dashboard-image-order">
                            @if (!empty($lastOrder) && count($lastOrder) > 0)
                                <?php
                                $requiresBaseUrl = strpos($lastOrder[0]->image, 'http') === false;
                                $imageUtama = $requiresBaseUrl ? 'http://eliteproxy.co.id/' . $lastOrder[0]->image : $lastOrder[0]->image;
                                ?>
                                <div id="big-image-order">
                                    <img src="{{ htmlspecialchars($imageUtama) }}"
                                        alt="{{ htmlspecialchars($lastOrder[0]->nama) }}">
                                </div>
                                <div id="mini-image-order">
                                    @for ($i = 1; $i < count($lastOrder); $i++)
                                        <?php
                                        $requiresBaseUrl = strpos($lastOrder[$i]->image, 'http') === false;
                                        $image = $requiresBaseUrl ? 'http://eliteproxy.co.id/' . $lastOrder[$i]->image : $lastOrder[$i]->image;
                                        ?>
                                        <div id="item-mini-image-order">
                                            <img src="{{ htmlspecialchars($image) }}"
                                                alt="{{ htmlspecialchars($lastOrder[$i]->nama) }}">
                                        </div>
                                    @endfor
                                </div>
                            @else
                                <p>Belum Ada Produk Terjual</p>
                            @endif
                        </div>

                    </div>
                </div>
                {{-- end Content --}}
            </div>
        </div>
    </div>
    @include('seller.asset.footer')
    <script src="{{ asset('/js/function/seller/dashboard.js') }}" type="text/javascript"></script>
</body>

</html>
