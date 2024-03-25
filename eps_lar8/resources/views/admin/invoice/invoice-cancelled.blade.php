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
                                                    <?php
                                                    if ($invoice->completeCartShop->status == 'cancel_by_seller') {
                                                        echo 'Dibatalkan oleh Penjual';
                                                    } elseif ($invoice->completeCartShop->status == 'cancel_by_marketplace') {
                                                        echo 'Dibatalkan oleh Admin';
                                                    } elseif ($invoice->completeCartShop->status == 'cancel_manual_by_user') {
                                                        echo 'Dibatalkan oleh User';
                                                    } else {
                                                        echo $invoice->completeCartShop->status;
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    @if ($invoice->completeCartShop->note)
                                                        {{ $invoice->completeCartShop->note }}
                                                    @else
                                                        {{ $invoice->completeCartShop->note_seller }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a style="font-size: larger" onclick="detail('{{ $invoice->id }}')" class="glyphicon glyphicon-info-sign"></a> &nbsp;
                                                    <a style="font-size: larger" onclick="upload('{{ $invoice->id }}')" class="glyphicon glyphicon-upload"></a> &nbsp;
                                                    <a style="font-size: larger" onclick="view('{{ $invoice->id }}')" class="glyphicon glyphicon-file"></a>
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
