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
                                <h3 class="box-title">List Cancelled Invoice</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">

                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No Invoice</th>
                                            <th>Total</th>
                                            <th>Status </th>
                                            <th>Note </th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($cancelledInvoices as $key => $invoice)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $invoice->invoice }}</td>
                                                <td>{{ str_replace(',', '.', number_format($invoice->total)) }}</td>
                                                <td>
                                                    @if ($invoice->completeCartShop->status == 'cancel_by_seller')
                                                        Dibatalkan oleh Penjual
                                                    @elseif ($invoice->completeCartShop->status == 'cancel_by_marketplace')
                                                        Dibatalkan oleh Admin
                                                    @elseif ($invoice->completeCartShop->status == 'cancel_manual_by_user')
                                                        Dibatalkan oleh User
                                                    @else
                                                        {{ $invoice->completeCartShop->status }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($invoice->completeCartShop->note)
                                                        {{ $invoice->completeCartShop->note }}
                                                    @else
                                                        {{ $invoice->completeCartShop->note_seller }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-transparent" onclick="detail('{{ $invoice->id }}')" title="Info Detail">
                                                        <span class="material-symbols-outlined" id="icon-info">
                                                            info
                                                        </span>
                                                    </button>
                                                    @if (!$invoice->file_cancel)
                                                        <button type="button" class="btn btn-transparent" onclick="upload_cancel('{{ $invoice->id }}')" title="Upload File Pembatalan">
                                                            <span class="material-symbols-outlined" id="icon-active">
                                                                upload_file
                                                            </span>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-transparent" onclick="reupload_cancel('{{ $invoice->id }}')" title="Ganti File Pembatalan">
                                                            <span class="material-symbols-outlined" id="icon-warning">
                                                                edit_document
                                                            </span>
                                                        </button>
                                                        <button type="button" class="btn btn-transparent" onclick="view_cancel('{{ $invoice->id }}')" title="Lihat File Pembatalan">
                                                            <span class="material-symbols-outlined" id="icon-disable">
                                                                file_open
                                                            </span>
                                                        </button>
                                                    @endif
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
                                            <th>Status </th>
                                            <th>Note </th>
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
