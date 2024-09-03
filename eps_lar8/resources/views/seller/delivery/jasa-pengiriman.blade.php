<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body>
    <div class="container-fluid">
        <div class="row">
            @include('seller.asset.desktop.sidebar')
            <div class="col-middle">
                {{-- content --}}
                <div id="view-data-product">
                    <div id="notif">
                        <img src="{{ asset('/img/app/icon_lonceng.png') }}" width="50px" style="margin-left: 10px;">
                        <img src="{{ asset('/img/app/icon_chat.png') }}" width="50px">
                    </div>

                    <div id="text-pesanan">
                        <h2><b><i>Pengaturan</i></b></h2>
                        <div id="box-filter-pesanan">
                            <div class="item-box-filter-pesanan" data-tipe="jasa-ongkir">
                                <b style="margin-left:20px">Jasa Pengiriman <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="free-ongkir">
                                <b style="margin-left:20px"> Free Ongkir <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                        </div>
                    </div>

                    <div id="content">
                    </div>
                </div>
                {{-- end Content --}}
            </div>
        </div>
    </div>
    @include('seller.asset.footer')
    <script src="{{ asset('/js/function/seller/delivery.js') }}" type="text/javascript"></script>
    <script>initialize()</script>
</body>

</html>
