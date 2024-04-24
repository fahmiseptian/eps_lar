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
                        <div class="box box-danger">
                            <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Kesehatan Toko</b></h3>
                            <small style="margin-left: 15px;">Fitur ini menampilkan tetang kesehatan toko anda.</small>
                            <hr>
                            <div class="box-body">
                                <fieldset>
                                    <legend>Produk yang dilarang</legend>
                                    <table id="data-table" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>Statistik</th>
                                                <th> Toko Saya</th>
                                                <th>Target</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Pelanggaran Produk Berat</td>
                                                <td>{{ number_format($pelanggaran_produk_berat) }}</td>
                                                <td>0</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Produk Spam</td>
                                                <td>{{ number_format($produk_spam) }}</td>
                                                <td>0</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Produk Imitasi</td>
                                                <td>{{ number_format($produk_imitasi) }}</td>
                                                <td>0</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Produk yang Dilarang</td>
                                                <td>{{ number_format($produk_yang_dilarang) }}</td>
                                                <td>0</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Pelanggaran Produk Ringan</td>
                                                <td>{{ number_format($pelanggaran_produk_ringan) }}</td>
                                                <td>0</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
                                <br>
                                <fieldset>
                                    <legend>Pesanan Terselesaikan</legend>
                                    <table id="data-table" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>Statistik</th>
                                                <th>Toko Saya</th>
                                                <th>Target</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Tingkat Pesanan Tidak Terselesaikan</td>
                                                <td><?php $count_tidak_terselesaikan = ($tidak_terselesaikan / $count_order) * 100;
                                                echo number_format($count_tidak_terselesaikan); ?>%</td>
                                                <td>
                                                    < 10% </td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Tingkat Pembatalan</td>
                                                <td><?php $count_pembatalan = ($pembatalan / $count_order) * 100;
                                                echo number_format($count_pembatalan); ?>%</td>
                                                <td>
                                                    < 5%</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Tingkat Pengembalian</td>
                                                <td><?php $count_pengembalian = ($pengembalian / $count_order) * 100;
                                                echo number_format($count_pengembalian); ?>%</td>
                                                <td>
                                                    < 5%</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Masa Pengemasan</td>
                                                <td><?php $count_pengemasan = ($pengemasan / $count_order) * 100;
                                                echo number_format($count_pengemasan); ?> Hari</td>
                                                <td>
                                                    < 2 Hari</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
                                <br>
                                <fieldset>
                                    <legend>Pesanan Terselesaikan</legend>
                                    <table id="data-table" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>Statistik</th>
                                                <th> Toko Saya</th>
                                                <th>Target</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Persentase Chat Dibalas</td>
                                                <td>{{$total_percentage_chat}}%</td>
                                                <td>≥ 70 %</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                            <tr>
                                                <td>Waktu Chat Dibalas</td>
                                                <td>{{number_format($total_chat_time)}}</td>
                                                <td>< 1 hari</td>
                                                <td><a href="#">Ricinan</a></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </fieldset>
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
<script src="{{ asset('/js/function/seller/shophealth.js') }}" type="text/javascript"></script>

</html>
