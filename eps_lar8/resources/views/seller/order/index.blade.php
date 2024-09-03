<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body>
    <div class="container-fluid">
        <div class="row">
            @include('seller.asset.desktop.sidebar')
            <div class="col-middle">
                {{-- content --}}
                <div id="view-pesanan">
                    <div id="notif">
                        <img src="{{ asset('/img/app/icon_lonceng.png') }}" width="50px" style="margin-left: 10px;">
                        <img src="{{ asset('/img/app/icon_chat.png') }}" width="50px">
                    </div>

                    <div id="text-pesanan">
                        <h2><b><i>Pesanan Saya</i></b></h2>
                        <div id="box-filter-pesanan">
                            <div class="item-box-filter-pesanan" data-tipe="semua">
                                <b style="margin-left:20px">Semua <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="waiting_accept_order">
                                <b style="margin-left:20px">Perlu Dikemas <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="on_packing_process">
                                <b style="margin-left:20px">Perlu Dikirim <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="send_by_seller">
                                <b style="margin-left:20px">Dikirim <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="complete">
                                <b style="margin-left:20px">Pesanan Diterima <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="done">
                                <b style="margin-left:20px">Selesai <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            <div class="item-box-filter-pesanan" data-tipe="cancel_by_seller">
                                <b style="margin-left:20px">Dibatalkan <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div>
                            {{-- <div class="item-box-filter-pesanan" data-tipe="cancel_by_seller">
                                <b style="margin-left:20px">Pengembalian <i style="margin-top:2px"
                                        class="material-icons pull-right">arrow_drop_down</i></b>
                            </div> --}}
                        </div>
                    </div>
                    <div id="table-content" >
                        <table id="example2" class="table"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th class="detail-full">Tanggal Pesan</th>
                                    <th class="detail-full">Instansi</th>
                                    <th class="detail-full">Kota Tujuan</th>
                                    <th class="detail-full">Nilai</th>
                                    <th class="detail-full">Qty</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot hidden>
                                <tr>
                                    <th>Invoice</th>
                                    <th class="detail-full">Tanggal Pesan</th>
                                    <th class="detail-full">Instansi</th>
                                    <th class="detail-full">Kota Tujuan</th>
                                    <th class="detail-full">Nilai</th>
                                    <th class="detail-full">Qty</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    {{-- end Content --}}
                </div>
            </div>
        </div>
        @include('seller.asset.footer')
        <script src="{{ asset('/js/function/seller/order.js') }}" type="text/javascript"></script>
</body>

</html>
