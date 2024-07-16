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
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Informasi penghasilan</b></h3>
                        <small>DIbawah ini Jumlah dana toko anda yang bisa dilepas dan sudah dilepas</small>
                        <br>
                        <br>
                        <div class="box box-info">
                            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                                <tr>
                                    <th style="padding: 10px; border-bottom: 1px solid #ccc;">Bisa Dilepas</th>
                                    <th style="padding: 10px; border-bottom: 1px solid #ccc;">Sudah dilepas</th>
                                    <td style="padding: 10px; border-bottom: 1px solid #ccc; text-align: right;">Total
                                    </td>
                                </tr>
                                <tr>
                                    <th style="padding: 10px; ">Rp {{ number_format($saldo, 0, ',', '.') }} </td>
                                    <th style="padding: 10px;">Rp {{ number_format($saldoSelesai, 0, ',', '.') }} </td>
                                    <th style="padding: 10px; text-align: right;">Rp
                                        {{ number_format($saldo, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                            <br>
                            <br>
                            @if ($rekening != null)
                                <small style="float: left; margin-top:-20px; margin-left:4px">Rekening Bank Saya: **** {{substr( $rekening->rek_number, -4);  }}</small>
                            @else
                                <small style="float: left; margin-top:-20px; margin-left:4px">Rekening Bank</small>
                            @endif
                            <a href="#"><small style="float: Right; margin-top:-20px; margin-right:4px">Saldo Penjual</small></a>
                        </div>
                        <br>
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Rincian penghasilan</b></h3>
                        <small>DIbawah ini Rincian dana toko anda yang bisa dilepas dan sudah dilepas</small>
                        <br>
                        <br>
                        <div class="box box-info" >
                            <div style="margin-top: 5px">
                                <table style="width: 100%">
                                    <tr>
                                        <th style="text-align: center; border-bottom: 3px solid #FC6703;"
                                            id="bisaDilepasTab">
                                            <a style="color:black" href="#"
                                                onclick="showTab('bisaDilepas'); return false;">Bisa Dilepas</a>
                                        </th>
                                        <th style="text-align: center;" id="sudahDilepasTab">
                                            <a style="color:black" href="#"
                                                onclick="showTab('sudahDilepas'); return false;">Sudah Dilepas</a>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                            <br>
                            <div class="bisaDilepas" id="bisaDilepas" style="display: block;">
                                <table id="example1" class="table table-bordered table-hover" style="width: 100%">
                                    <!-- Konten tabel untuk 'Bisa Dilepas' -->
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pesanan</th>
                                            <th>Pembeli</th>
                                            <th>Dana</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Baris data -->
                                        @php $i = 1 @endphp
                                        @foreach($dataPending as $Pendingsaldo)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $Pendingsaldo->invoice }}</td>
                                            <td>{{ $Pendingsaldo->nama }}</td>
                                            <td>
                                                Rp {{number_format($Pendingsaldo->total, 0, ',', '.')}}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Pesanan</th>
                                            <th>Pembeli</th>
                                            <th>Dana</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="sudahDilepas" id="sudahDilepas" style="display: none;">
                                <table id="example2" class="table table-bordered table-hover" style="width: 100%">
                                    <!-- Konten tabel untuk 'Sudah Dilepas' -->
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pesanan</th>
                                            <th class="detail-full">Pembeli</th>
                                            <th>Dana</th>
                                            <th class="detail-full">Tanggal Dana Dilepas</th>
                                            <th>Status</th>
                                            <th>Bukti</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Baris data -->
                                        @php $i = 1 @endphp
                                        @foreach($dataSuccess as $Successsaldo)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $Successsaldo->invoice }}</td>
                                            <td class="detail-full">{{ $Successsaldo->nama }}</td>
                                            <td>{{ $Successsaldo->total }}</td>
                                            <td class="detail-full">{{ $Successsaldo->execute_date }}</td>
                                            <td>{{ $Successsaldo->status }}</td>
                                            <td>#</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Pesanan</th>
                                            <th class="detail-full">Pembeli</th>
                                            <th>Dana</th>
                                            <th class="detail-full">Tanggal Dana Dilepas</th>
                                            <th>Status</th>
                                            <th>Bukti</th>
                                        </tr>
                                    </tfoot>
                                </table>
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
<script src="{{ asset('/js/function/seller/finance.js') }}" type="text/javascript"></script>

</html>
