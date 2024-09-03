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
                        </div>
                    </div>
                    <div id="content">
                        <div class="detail-transaksi">

                            <div class="batas-pengerjaan">
                                <b class="warning-text">
                                    <i class="material-symbols-outlined">warning</i>
                                    Pesanan Belum Diterima Seller
                                </b>
                                <div class="date-pengerjaan">
                                    <span class="left-text">Batas Proses Penerimaan 2 x 24 jam</span>
                                    <span class="right-text">13 July 2025</span>
                                </div>
                            </div>

                            <div class="aksi-invoice">
                                <table style="width: 100%">
                                    <tr>
                                        <th>No </th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Status</th>
                                        <th>Pembayaran</th>
                                        <th rowspan="2" class="btn-container" >
                                            <a class="btn btn-warning fa fa-copy">&nbsp;Copy Resi </a>
                                            <a class="btn btn-warning fa fa-map-marker">&nbsp;Lacak </a>
                                            <a class="btn btn-warning fa fa-upload">&nbsp;Upload DO </a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <td>
                                            PRFINV-20240827707-788
                                        </td>
                                        <td>
                                            27 Agustus 2024
                                        </td>
                                        <td>
                                            Pesanan Baru
                                        </td>
                                        <td>
                                            Belum Dibayar
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="detail-pengiriman">
                                <table class="table-detail-order" style="width: 100%">
                                    <tr>
                                        <th>Informasi Pengiriman</th>
                                        <th></th>
                                        <th class="btn-container">
                                            <a class="btn btn-danger fa fa-print">&nbsp;Cetak Lebel</a>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>Nama Penerima</th>
                                        <th>Nomor Telepon</th>
                                        <th>Alamat</th>
                                    </tr>
                                    <tr>
                                        <td>USER</td>
                                        <td>087386474948</td>
                                        <td>NAMA<br>
                                            Test,Kabupaten Tangerang-Cikupa,Banten
                                            <br> 14939
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <th>NPWP</th>
                                        <th>Keperluan</th>
                                    </tr>
                                    <tr>
                                        <td>ppmerdeka@gmail.com</td>
                                        <td></td>
                                        <td>sas</td>
                                    </tr>
                                    <tr>
                                        <th>Pengiriman</th>
                                        <th>Resi</th>
                                        <th>Pesan Pembeli</th>
                                    </tr>
                                    <tr>
                                        <td>JNE - REG23</td>
                                        <td>456787654</td>
                                        <td> TEXT </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="aksi-file-transaksi">
                                <a class="btn btn-warning fa fa-file">&nbsp;INVOICE</a>
                                <a class="btn btn-warning fa fa-file">&nbsp;KWITANSI</a>
                                <a class="btn btn-warning fa fa-plus">&nbsp;KONTRAK</a>
                                <a class="btn btn-warning fa fa-file">&nbsp;SURAT PESANAN</a>
                                <a class="btn btn-warning fa fa-file">&nbsp;BAST</a>
                                <a class="btn btn-warning fa fa-upload">&nbsp;Upload Faktur</a>
                            </div>

                            <div class="detail-produk-transaksi">
                                <table class="table-detail-order" style="width: 100%">
                                    <div class="detailproduct">
                                        <tr>
                                            <td colspan="2"><b>Produk Dipesan</b></td>
                                            <td>Harga Satuan</td>
                                            <td>Jumlah</td>
                                            <td>Subtotal</td>
                                        </tr>
                                        <tr>
                                            <td><img src="https://eliteproxy.co.id/seller_center/upload/product/50-product_0_Shinpo-Hercules-Container-Box-150-Liter_.jpg" alt="produk"></td>
                                            <td> nama barang</td>
                                            <td>
                                                Rp.
                                                {{ str_replace(',', '.', number_format(20000)) }}
                                            </td>
                                            <td>22</td>
                                            <td>
                                                Rp.
                                                {{ str_replace(',', '.', number_format(200000)) }}
                                            </td>
                                        </tr>
                                    </div>
                                </table>
                            </div>

                            <div class="detail-seller-transaksi">
                                <div class="detailbiaya">
                                    <table class="table-detail-order">
                                        <tr>
                                            <td><span class="fa fa-truck">&nbsp; Biaya Pengiriman :</span></td>
                                            <td>Rp.
                                                {{ str_replace(',', '.', number_format(200000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-shield">&nbsp; Asuransi Pengiriman :</span></td>
                                            <td>Rp.
                                                {{ str_replace(',', '.', number_format(200000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <span class="fa fa-money">
                                                    &nbsp; Total Pesanan
                                                    (2) :
                                                </span>
                                            </td>
                                            <td>
                                                Rp. {{ str_replace(',', '.', number_format(200000)) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="detail-pembayaran-transaksi">
                                <div>
                                    <table class="table-detail-order" style="width: 100%">
                                        <tr>
                                            <th>Pembayaran : Transfer Bank </th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>Pilihan TOP : 30 hari</th>
                                            <th></th>
                                        </tr>
                                    </table>
                                </div>

                                <br>
                                <div class="detailbiayaorder">
                                    <table class="table-detail-order">
                                        <tr>
                                            <th style="font-size:20px" colspan="3">
                                                Detail Pembayaran
                                            </th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Subtotal belum PPN:</th>
                                            <td>Rp.
                                                {{ str_replace(',', '.', number_format(20000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Ongkos Kirim belum PPN :</td>
                                            <td style="color: :orangered">Rp.
                                                {{ str_replace(',', '.', number_format(20000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Asuransi Pengiriman belum PPN :</td>
                                            <td style="color: :orangered">Rp.
                                                {{ str_replace(',', '.', number_format(20000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">PPN 11% :</td>
                                            <td style="color: :orangered">Rp.
                                                {{ str_replace(',', '.', number_format(20000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Diskon :</td>
                                            <td style="color: :orangered">Rp.
                                                {{ str_replace(',', '.', number_format(20000)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style=" font-size:15px" colspan="2">
                                                Total Pembayaran :
                                            </th>
                                            <td><b> Rp.
                                                    {{ str_replace(',', '.', number_format(20000)) }}
                                                </b>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- end Content --}}
                </div>
            </div>
        </div>
        @include('seller.asset.footer')
        <script src="{{ asset('/js/function/seller/shop.js') }}" type="text/javascript"></script>
        <script>
            // initialize()
        </script>
</body>

</html>
