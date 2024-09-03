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
                        <h2><b><i>Keuangan</i></b></h2>
                        <div id="box-filter-pesanan">
                            <div class="item-box-filter-pesanan" data-tipe="penghasilan">
                                <b style="margin-left:20px">Penghasilan <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="saldo">
                                <b style="margin-left:20px"> Saldo <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="rekening">
                                <b style="margin-left:20px"> Rekening <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                        </div>
                    </div>

                    <div id="table-content">
                    </div>
                </div>
                {{-- end Content --}}
            </div>
        </div>
    </div>
    @include('seller.finance.modal-tarik-saldo')
    @include('seller.finance.modal')
    @include('seller.asset.footer')
    <script src="{{ asset('/js/function/seller/finance.js') }}" type="text/javascript"></script>
    <script>
        loadData('saldo')

        $('.item-box-filter-pesanan[data-tipe="saldo"]').addClass("open");
        $('.item-box-filter-pesanan[data-tipe="saldo"]').addClass("active");
        activeItem = $('.item-box-filter-pesanan[data-tipe="saldo"]');
        allItems.slideUp();
        activeItem.slideDown();

    </script>
</body>

</html>
