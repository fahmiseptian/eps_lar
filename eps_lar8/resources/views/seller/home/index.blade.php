<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">
        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')
        <div class="content-wrapper">
            @include('seller.asset.section-info')
            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="box-home" style=" background-color:#EFF9FF">
                            <h4 class="page-header" style="margin: 2px">
                                Cek Informasi Toko tentang
                                <small>Ini merupakan informasi tentang toko mu</small>
                            </h4>
                        </div>
                        <div class="box-home" style="display: grid; background-color:#EFF9FF">
                            <!-- Container -->
                            <div class="box-home" id="item-box-home">
                                <div class="item" style="text-align: center; flex: 1; padding: 10px;">
                                    <p style="font-size: 20px">{{ $jmlhproduct }}</p>
                                    <b>Produk Aktif</b>
                                </div>
                                <div class="item">
                                    <p style="font-size: 20px">{{ $neworder }}</p>
                                    <b>Pesanan Baru</b>
                                </div>
                                <div class="item">
                                    <p style="font-size: 20px">{{ $ondelivery }}</p>
                                    <b>Pengiriman Diproses</b>
                                </div>
                                <div class="item">
                                    <p style="font-size: 20px">{{$product_habis ?? ''}}</p>
                                    <b>Produk Habis</b>
                                </div>
                            </div>
                            {{-- BOX 2 --}}
                            <div class="box-home">
                                <!-- Item 1 -->
                                <div class="item">
                                    <b>Total Pendapatan Hari Ini </b>
                                    <p style="font-size: 20px"><b>Rp {{number_format($pendapatanHariIni,0,".",",")}} </b></p>
                                </div>
                            </div>
                            {{-- BOX 3 --}}
                            <div class="box-home">
                                <div class="item" style="text-align: center; flex: 1; padding: 10px;">
                                    <p><b>Nego Baru</b></p>
                                    <p style="font-size: 20px"><b>{{ $newNego }}</b></p>
                                </div>
                                <div class="item">
                                    <p><b>Nego Ulang </b></p>
                                    <p style="font-size: 20px"><b>{{$NegoUlang}}</b></p>
                                </div>
                                <div class="item">
                                    <p><b>Nego Sukses</b></p>
                                    <p style="font-size: 20px"><b>{{$Nego}}</b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div>
    </div>
</body>
<script src="{{ asset('/js/function/seller/dashboard.js') }}" type="text/javascript"></script>

@include('seller.asset.footer')

</html>
