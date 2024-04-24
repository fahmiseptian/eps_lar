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

                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header">
                                <div class="col-md-10" style="padding-left: 0;">
                                    <h3 class="box-title">List Payment</h3>
                                </div>
                                <!-- Daftar Parent Menu -->
                                <div class="col-md-2" style="text-align: right;">
                                    <button onclick="paymentAdd()" class="btn btn-primary">
                                        <i class="fa fa-plus-circle"></i>
                                        Add Payment
                                    </button>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Fee Nominal</th>
                                            <th>Fee Percent</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($listpayment as $payment)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $payment->name }}</td>
                                                <td><img src="{{ asset('storage/' . $payment->image) }}" alt="Payment Image" style="max-width: 100px;"></td>
                                                <td>Rp{{ number_format($payment->fee_nominal, 0, ',', '.') }}</td>
                                                <td>{{ str_replace('.', ',', $payment->fee_percent) }}%</td>
                                                <td>
                                                    <button type="button" class="btn btn-transparent" onclick="paymentDetail('{{ $payment->id }}')" title="Info Detail">
                                                        <span class="material-symbols-outlined" id="icon-info">
                                                            info
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="paymentStatus('{{ $payment->id }}')" title="Ubah Status Payment">
                                                        <span class="material-symbols-outlined" id="{{ $payment->active === 'Y' ? 'icon-active' : 'icon-disable' }}">
                                                            {{ $payment->active === 'Y' ? 'visibility' : 'visibility_off' }}
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="paymentEdit('{{ $payment->id }}')" title="Edit Payment">
                                                        <span class="material-symbols-outlined" id="icon-warning">
                                                            edit_square
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="paymentDelete('{{ $payment->id }}')" title="Hapus Payment">
                                                        <span class="material-symbols-outlined" id="icon-delete">
                                                            delete
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
                                            <th>Name</th>
                                            <th>Image</th>
                                            <th>Fee Nominal</th>
                                            <th>Fee Percent</th>
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
<script src="{{ asset('/js/function/admin/payment.js') }}" type="text/javascript"></script>

</html>