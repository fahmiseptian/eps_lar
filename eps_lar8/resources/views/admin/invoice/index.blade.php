<!DOCTYPE html>
<html>


@include('admin.asset.header')

<body class="skin-blue">
    <div class="wrapper">

        {{-- Navbar --}}
        @include('admin.asset.navbar')


        {{-- sidebar --}}
        @include('admin.asset.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            {{-- section-info --}}
            @include('admin.asset.section-info')

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">List Invoice</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">

                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Bayar</th>
                                            <th>No Invoice</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Pemeriksa</th>
                                            <th>Status Pajak</th>
                                            <th>Pelapor</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($datainv as $item)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>
                                                    @if ($item->tanggal_bayar)
                                                        {{ date('Y-m-d', strtotime($item->tanggal_bayar)) }}
                                                    @else
                                                        {{ '-' }}
                                                    @endif
                                                </td>

                                                <td>{{ $item->invoice }}</td>
                                                <td>Rp{{ number_format($item->total, 0, ',', '.') }}</td>

                                                <td>

                                                    <?php
                                                    if ($item->status == 'complete_payment') {
                                                        echo 'Selesai';
                                                    } elseif ($item->status == 'cancel') {
                                                        echo 'Batal';
                                                    } elseif ($item->status == 'pending') {
                                                        echo 'Belum Dibayar';
                                                    } elseif ($item->status == 'on_delivery') {
                                                        echo 'Dalam Pengiriman';
                                                    } elseif ($item->status == 'waiting_approve_by_ppk') {
                                                        echo 'Menunggu Persetujuan';
                                                    } elseif ($item->status == 'expired') {
                                                        echo 'Expired';
                                                    } elseif ($item->status == 'cancel_part') {
                                                        echo 'Batals';
                                                    } else {
                                                        echo $item->status;
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    @if ($item->finance)
                                                        {{ $item->finance->username }}
                                                    @else
                                                        {{-- Ini Kalau data kosong --}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <?php
                                                    
                                                    if ($item->status_pelaporan_pajak == '2') {
                                                        echo 'Dilaporkan semua';
                                                    }
                                                    
                                                    if ($item->status_pelaporan_pajak == '1') {
                                                        echo 'Dilaporkan sebagian';
                                                    }
                                                    
                                                    if ($item->status_pelaporan_pajak == '0') {
                                                        echo 'Belum Dilaporkan';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    @if ($item->pajak)
                                                        {{ $item->pajak->username }}
                                                    @else
                                                        {{-- Ini Kalau data kosong --}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-transparent" onclick="#" title="Lapor Pajak">
                                                        <span class="material-symbols-outlined" id="icon-warning">
                                                            forward_to_inbox
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="#" title="Ubah Status Pembayaran">
                                                        <span class="material-symbols-outlined" id="icon-active">
                                                            currency_exchange
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <div id="datepicker" style="display: none;"></div>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>No Invoice</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Pemeriksa</th>
                                            <th>Status Pajak</th>
                                            <th>Pelapor</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>

                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->
        {{-- section-footer --}}
        @include('admin.asset.section-footer')
    </div><!-- ./wrapper -->
</body>
{{-- footer --}}
@include('admin.asset.footer')
<!-- page script -->
<script src="{{ asset('/js/function/admin/invoices.js') }}" type="text/javascript"></script>

</html>
