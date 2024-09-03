<!DOCTYPE html>
<html>
<style>
    .dataTables_paginate .paginate_button:not(.previous):not(.next):not(.first):not(.last) {
        display: none;
    }
</style>
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
                        <h2><b><i>Toko Saya</i></b></h2>
                        <div id="box-filter-pesanan">
                            <div class="item-box-filter-pesanan" data-tipe="rates_shop">
                                <b style="margin-left:20px">Penilaian <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="a_chat">
                                <b style="margin-left:20px"> Asistent Chat <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="profile">
                                <b style="margin-left:20px">Profile <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="etalase">
                                <b style="margin-left:20px">Etalase <i style="margin-top:2px"
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
    @include('seller.toko.modal')
    @include('seller.asset.footer')
    <script src="{{ asset('/js/function/seller/shop.js') }}" type="text/javascript"></script>
    <script>
        initialize()
    </script>
</body>

</html>
