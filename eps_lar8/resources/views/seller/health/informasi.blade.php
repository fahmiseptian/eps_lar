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
                        <h2><b><i>Bisnis Saya</i></b></h2>
                        <div id="box-filter-pesanan">
                            <div class="item-box-filter-pesanan" data-tipe="health">
                                <b style="margin-left:20px">Kesehatan Toko <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="toko">
                                <b style="margin-left:20px"> Informasi Data <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="faq">
                                <b style="margin-left:20px">Pembelajaran <i style="margin-top:2px"
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
    <script src="{{ asset('/js/function/seller/shophealth.js') }}" type="text/javascript"></script>
    <script>
        loadData('toko')

        $('.item-box-filter-pesanan[data-tipe="toko"]').addClass("open");
        $('.item-box-filter-pesanan[data-tipe="toko"]').addClass("active");
        activeItem = $('.item-box-filter-pesanan[data-tipe="toko"]');
        allItems.slideUp();
        activeItem.slideDown();
    </script>
</body>

</html>