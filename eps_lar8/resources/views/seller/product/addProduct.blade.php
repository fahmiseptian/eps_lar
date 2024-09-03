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
                        <h2><b><i>Produk Saya</i></b></h2>
                        <div id="box-filter-pesanan">
                            <div class="item-box-filter-pesanan" data-tipe="semua">
                                <b style="margin-left:20px">Semua <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="live">
                                <b style="margin-left:20px">Tayang <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="habis">
                                <b style="margin-left:20px">Habis <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="arsip">
                                <b style="margin-left:20px">Arsip <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="addProduk">
                                <b style="margin-left:20px">Tambah Produk<i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                        </div>
                    </div>

                    <div id="table-content" >
                    </div>
                </div>
                {{-- end Content --}}
            </div>
        </div>
    </div>
    @include('seller.product.modal')
    @include('seller.asset.footer')
    <script src="{{ asset('/js/function/seller/product.js') }}" type="text/javascript"></script>
    <script>
        loadData('addProduk')

        $('.item-box-filter-pesanan[data-tipe="addProduk"]').addClass("open");
        $('.item-box-filter-pesanan[data-tipe="addProduk"]').addClass("active");
        activeItem = $('.item-box-filter-pesanan[data-tipe="addProduk"]');
        allItems.slideUp();
        activeItem.slideDown();
    </script>
</body>

</html>
