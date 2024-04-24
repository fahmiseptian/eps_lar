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
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Informasi Saldo Toko</b></h3>
                        <br>
                        <div class="box box-info">
                            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                                <tr>
                                    <th style="padding: 10px; border-bottom: 1px solid #ccc;">Saldo</th>
                                    <th style="padding: 10px; border-bottom: 1px solid #ccc;">Rekening</th>
                                    <td style="padding: 10px; border-bottom: 1px solid #ccc; text-align: right;">No
                                        Rekening
                                    </td>
                                </tr>
                                <tr>
                                    <th style="padding: 10px; ">Rp {{ number_format($saldo, 0, ',', '.') }} <span
                                            class="btn btn-info">Tarik</span> </td>
                                    <th style="padding: 10px;">{{ $rekening->name }} <span
                                            class="btn btn-info">Utama</span></td>
                                    <th style="padding: 10px; text-align: right;">
                                        ****{{ substr($rekening->rek_number, -4) }}</td>
                                </tr>
                            </table>
                            <br>
                        </div>
                        <br>
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Transaksi Terakhir</b></h3>
                        <br>
                        <div class="box box-info">
                            <div>
                                <table id="example2" class="table table-bordered table-hover" style="width: 100%">
                                    <!-- Konten tabel untuk 'Sudah Dilepas' -->
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Penarikan</th>
                                            <th class="detail-full">Rekening</th>
                                            <th>Dana</th>
                                            <th>Status</th>
                                            <th class="detail-full">Tanggal Dipebaharui</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Baris data -->
                                        @php $i = 1 @endphp
                                        @foreach ($PenarikanDana as $Penarikan)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>TR {{ $Penarikan->id }}</td>
                                                <td class="detail-full">**** {{ substr($rekening->rek_number, -4) }}
                                                </td>
                                                <td>Rp {{ number_format($Penarikan->total, 0, ',', '.') }} </td>
                                                <td class="detail-full">
                                                    @if ($Penarikan->status == 'success')
                                                        Selesai
                                                    @else
                                                        Pengecekan
                                                    @endif
                                                </td>
                                                <td class="detail-full">{{ \Carbon\Carbon::parse($Penarikan->last_update)->format('Y-m-d') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>ID Penarikan</th>
                                            <th class="detail-full">Rekening</th>
                                            <th>Dana</th>
                                            <th>Status</th>
                                            <th class="detail-full">Tanggal Dipebaharui</th>
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
