<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">

        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <?php
                        // Rumus
                        // ppn asuransi
                        $ppn_insurance = ($detailOrder->val_ppn / 100) * $detailOrder->insurance_nominal;
                        
                        // proforma Invoice
                        $proforma = false;
                        if ($detailOrder->status_pembayaran_top == '0') {
                            $proforma = true;
                        }
                        
                        // pembayaran
                        $pembayaran = false;
                        if ($detailOrder->status_pembayaran_top == '1') {
                            $pembayaran = true;
                        }
                        $action = '';
                        // Status
                        if (($detailOrder->status == 'waiting_accept_order' && $detailOrder->status_pembayaran_top == '0' && $detailOrder->is_top == '1') || ($detailOrder->status == 'waiting_accept_order' && $cek_payment && $detailOrder->status_pembayaran_top == '1' && $detailOrder->is_top == '0') || ($detailOrder->status == 'waiting_accept_order' && $cek_payment && $detailOrder->status_pembayaran_top == '1' && $detailOrder->is_top == '1')) {
                            $sta = 0;
                            $status = '<span style="width:100%;" class="badge  btn-primary">Pesanan <br> Baru</span>';
                            $action =
                                '<a style="width:80%; margin:5px; background-color:green;color:white" data-id="' .
                                $detailOrder->id_cart_shop .
                                '" href="javascript:;" class="btn fa fa-check accept-this-order">&nbsp;Terima</a>
                                                                                                                                                            <a style="width:80%; margin:5px; background-color:red;color:white" data-id="' .
                                $detailOrder->id_cart_shop .
                                '" href="javascript:;" class="btn fa fa-times cancel-order">&nbsp;Tolak</a>';
                        } elseif (($detailOrder->status == 'waiting_accept_order' || $detailOrder->status == 'on_packing_process') && $detailOrder->status_pembayaran_top == '0' && $detailOrder->is_top == '0') {
                            $sta = 1;
                            $status = '<span style="width:100%;background-color:orange;" class="badge ">Pesanan <br> Baru</span>';
                            $action = '<span style="width:100%;background-color:orange;">Menunggu pembeli menyelesaikan pembayaran</span>';
                        } elseif ($detailOrder->status == 'on_packing_process') {
                            $sta = 2;
                            $status = '<span style="width:100%;background-color:purple;" class="badge">Diproses</span>';
                        
                            $action = '<a style="width:80%; margin:5px; background-color:green;color:white" data-id="' . $detailOrder->id . '" data-id_courier="' . $detailOrder->id_courier . '" href="javascript:;" class="btn fa fa-truck">&nbsp;Request <br> Pengiriman</a>';
                        } elseif ($detailOrder->status == 'send_by_seller') {
                            $sta = 3;
                            $status = '<span style="width:100%;background-color:blue;" class="badge">Dikirim</span>';
                            $action =
                                '<a style="width:80%; margin:5px; color:white" data-resi="' .
                                $detailOrder->no_resi .
                                '" href="javascript:;" class="btn btn-info fa fa-copy">&nbsp;Resi</a>
                                <a style="width:80%; margin:5px; background-color:purple;color:white" data-id="' .
                                $detailOrder->id .
                                '" href="javascript:;" class="btn fa fa-map-marker">&nbsp;Lacak</a>';
                        
                            if ($detailOrder->id_courier == '0') {
                                $action .= '<a style="width:80%; margin:5px; color:white" data-id="' . $detailOrder->id . '" data-id_courier="' . $detailOrder->id_courier . '" href="javascript:;" class="btn btn-warning fa fa-upload">&nbsp; DO</a>';
                            }
                        } elseif ($detailOrder->status == 'complete' && $detailOrder->no_resi != null && $pembayaran == true && $detailOrder->is_bast == '1') {
                            $sta = 4;
                            $status = '<span style="width:100%;background-color:green;" class="badge">Selesai</span>';
                        
                            $action =
                                '<a style="width:80%; margin:5px; color:white" data-resi="' .
                                $detailOrder->no_resi .
                                '" href="javascript:;" class="btn btn-info fa fa-copy">&nbsp;Resi</a>
                                                                                                                                                            <a style="width:80%; margin:5px; background-color:purple;color:white" data-id="' .
                                $detailOrder->id .
                                '" href="javascript:;" class="btn fa fa-map-marker">&nbsp;Lacak</a>';
                        } elseif ($detailOrder->status == 'complete' && $detailOrder->no_resi != null && $pembayaran == true) {
                            $sta = 4;
                            $status = '<span style="width:100%;background-color:green;" class="badge">Selesai</span>';
                        
                            $action =
                                '<a style="width:80%; margin:5px; color:white" data-resi="' .
                                $detailOrder->no_resi .
                                '" href="javascript:;" class="btn btn-info fa fa-copy">&nbsp;Resi</a>
                                                                                                                                                            <a style="width:80%; margin:5px; background-color:purple;color:white" data-id="' .
                                $detailOrder->id .
                                '" href="javascript:;" class="btn fa fa-map-marker">&nbsp;Lacak</a>';
                        } elseif ($detailOrder->status == 'complete' && $detailOrder->no_resi != null && $pembayaran == false) {
                            $sta = 5;
                            $status = '<span style="width:100%;background-color:yellow;" class="badge">Pesanan <br> sedang <br> dikirim</span>';
                        
                            if ($detailOrder->is_bast == '1') {
                                $status = '<span style="width:100%;" class="badge btn-info">Pesanan <br> sudah <br>  Diterima</span>';
                            }
                        
                            $action =
                                '<a style="width:80%; margin:5px; color:white" data-resi="' .
                                $detailOrder->no_resi .
                                '" href="javascript:;" class="btn btn-info fa fa-copy">&nbsp;Resi</a>
                                                                                                                                                            <a style="width:80%; margin:5px; background-color:purple;color:white" data-id="' .
                                $detailOrder->id .
                                '" href="javascript:;" class="btn fa fa-map-marker">&nbsp;Lacak</a>';
                        } elseif ($detailOrder->status == 'waiting_approve_by_ppk') {
                            $sta = 6;
                            $status = '<span style="width:100%;background-color:red;" class="badge">Menunggu <br> Persetujuan <br> PPK</span>';
                        } else {
                            $sta = 7;
                            $status = '<span style="width:100%;background-color:red;" class="badge">Pesanan <br> Dibatalkan</span>';
                        }
                        ?>
                        <div class="box box-info">
                            <div class="box-body">
                                <table class="table-detail-order" style="width: 100%">
                                    <tr>
                                        <th>No <?= $proforma ? 'Proforma Invoice' : 'Invoice' ?></th>
                                        <th>Tanggal Pesanan</th>
                                        <th>Status</th>
                                        <th>Pembayaran</th>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ $proforma ? 'PRF' : '' }}&nbsp;{{ $detailOrder->invoice }}-{{ $detailOrder->id_cart_shop }}
                                        </td>
                                        <td>
                                            {{ date('Y-m-d', strtotime($detailOrder->created_date)) }}
                                        </td>
                                        <td>
                                            {{-- {{$detailOrder->status}} --}}
                                            <?= $status ?>
                                        </td>
                                        <td>
                                            @if ($detailOrder->status_pembayaran_top == 1)
                                                <span style="width:100%;background-color:green;" class="badge">Sudah
                                                </span>
                                            @else
                                                <span style="width:100%" class="badge btn-warning">Belum </span>
                                            @endif
                                        </td>
                                        <td class="btn-container">
                                            <?= $action ?>
                                        </td>
                                    </tr>
                                </table>

                            </div>
                        </div>
                        <div class="box box-warning">
                            <div class="box-body">
                                <table class="table-detail-order" style="width: 100%">
                                    <tr>
                                        <th>Informasi Pengiriman</th>
                                        <th></th>
                                        <th class="btn-container">
                                            @if ($detailOrder->no_resi != null)
                                                <a href="#" class="btn btn-info fa fa-print">&nbsp;Cetak</a>
                                            @else
                                                <a style="background-color: transparent;" class="btn">&nbsp;</a>
                                            @endif
                                        </th>
                                    </tr>
                                    <tr>
                                        <td> <b>Nama Penerima</b></td>
                                        <td><b>Nomor Telepon</b></td>
                                        <td><b>Alamat</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $detailOrder->nama }}</td>
                                        <td>{{ $detailOrder->phone }}</td>
                                        <td>{{ $detailOrder->address_name }}<br>
                                            {{ $detailOrder->address }},{{ $detailOrder->city }}-{{ $detailOrder->subdistrict_name }},{{ $detailOrder->province_name }}
                                            <br> {{ $detailOrder->postal_code }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Email</b></td>
                                        <td><b>NPWP</b></td>
                                        <td><b>Keperluan</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $detailOrder->email }}</td>
                                        <td>{{ $detailOrder->npwp }}</td>
                                        <td>{{ $detailOrder->keperluan }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Pengiriman</b></td>
                                        <td><b>Resi</b></td>
                                        <td><b>Pesan Pembeli</b></td>
                                    </tr>
                                    <tr>
                                        <td>{{ $detailOrder->deskripsi }}-{{ $detailOrder->service }}</td>
                                        <td>{{ $detailOrder->no_resi }}</td>
                                        <td>{{ $detailOrder->pesan_seller }}</td>

                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="box box-danger">
                            <div class="box-body">
                                <table class="table-detail-order" style="width: 100%">
                                    <tr>
                                        <td><a style="width: 100%" href="#"
                                                class="btn btn-info fa fa-file-o">&nbsp;INVOICE</a></td>
                                        <td><a style="width: 100%" href="#"
                                                class="btn btn-info fa fa-file-o">&nbsp;KWOTANSI</a></td>
                                        <td><a style="width: 100%" href="#"
                                                class="btn btn-info fa fa-plus">&nbsp;KONTRAK</a></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="box box-info">
                            <div class="box-body">
                                <table class="table-detail-order" style="width: 100%">
                                    <div class="detailproduct">
                                        <tr>
                                            <td colspan="2"><b>Produk Dipesan</b></td>
                                            <td>Harga Satuan</td>
                                            <td>Jumlah</td>
                                            <td>Subtotal</td>
                                        </tr>
                                        @foreach ($detailProductOrder as $productOrder)
                                            <tr>
                                                <td><img src="https://eliteproxy.co.id/{{ $productOrder->gambar_produk }}"
                                                        alt="produk"></td>
                                                {{-- gunakan gamabar ukuran 50 --}}
                                                <td>{{ $productOrder->nama_produk }}</td>
                                                <td>
                                                    Rp.
                                                    {{ str_replace(',', '.', number_format($productOrder->harga_satuan_produk)) }}
                                                </td>
                                                <td>{{ $productOrder->qty_produk }}</td>
                                                <td>
                                                    Rp.
                                                    {{ str_replace(',', '.', number_format($productOrder->harga_total_produk)) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </div>
                                </table>
                                <br>
                                <div class="detailbiaya">
                                    <table class="table-detail-order">
                                        <tr>
                                            <td><span class="fa fa-truck">&nbsp; Biaya Pengiriman :</span></td>
                                            <td>Rp.
                                                {{ str_replace(',', '.', number_format($detailOrder->sum_shipping + $detailOrder->ppn_shipping)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-shield">&nbsp; Asuransi Pengiriman :</span></td>
                                            <td>Rp.
                                                {{ str_replace(',', '.', number_format($detailOrder->insurance_nominal + $ppn_insurance)) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-money">&nbsp; Total Pesanan
                                                    ({{ $detailOrder->qty }}) :</span></td>
                                            <td>Rp. {{ str_replace(',', '.', number_format($detailOrder->total)) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div>
                                    <table class="table-detail-order" style="width: 100%">
                                        <tr>
                                            <th>Pembayaran : {{ $detailOrder->pembayaran }} </th>
                                        </tr>
                                        <tr>
                                            <th>Pilihan TOP : {{ $detailOrder->jml_top }} hari</th>
                                        </tr>
                                    </table>
                                </div>
                                <br>
                                <div class="detailbiayaorder">
                                    <table class="table-detail-order">
                                        <tr>
                                            <th style="color:orangered; font-size:20px" colspan="3">Detail Pembayaran
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Subtotal belum PPN:</th>
                                            <td style="color: :orangered">Rp. {{ str_replace(',', '.', number_format($detailOrder->sum_price_non_ppn)) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Ongkos Kirim belum PPN :</td>
                                            <td style="color: :orangered">Rp. {{ str_replace(',', '.', number_format($detailOrder->sum_shipping)) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Asuransi Pengiriman belum PPN :</td>
                                            <td style="color: :orangered">Rp. {{ str_replace(',', '.', number_format($detailOrder->insurance_nominal)) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">PPN {{$detailOrder->val_ppn}}% :</td>
                                            <td style="color: :orangered">Rp. {{ str_replace(',', '.', number_format($detailOrder->ppn_price)) }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">Total Diskon :</td>
                                            <td style="color: :orangered">Rp. {{ str_replace(',', '.', number_format($detailOrder->sum_discount)) }}</td>
                                        </tr>
                                        <tr>
                                            <th style="color:orangered; font-size:15px" colspan="2">Total Pembayaran :</th>
                                            <td style="color:orangered;" ><b> Rp. {{ str_replace(',', '.', number_format($detailOrder->total)) }} </b></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
</body>
{{-- footer --}}
@include('seller.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/seller/order.js') }}" type="text/javascript"></script>

</html>
