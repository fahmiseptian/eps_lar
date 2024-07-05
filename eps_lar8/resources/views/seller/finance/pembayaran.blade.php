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
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Pengaturan Pembayaran</b></h3>
                        <br>
                        <div class="box box-info">
                            <div
                                style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 10px;">
                                <div style="display: flex; align-items: center;">
                                    <i style="font-size:20px; margin-right:10px" class="fa fa-key"></i>
                                    <div>
                                        <strong>PIN Saldo Penjual</strong><br>
                                        <small>Update PIN saldo penjual</small>
                                    </div>
                                </div>
                                <div>
                                    <button style="margin:5px" class="btn btn-info" id="updatePIN">Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    @include('seller.finance.modal-update-pin')
</body>
{{-- footer --}}
@include('seller.asset.footer')
<!-- page script -->
<script src="{{ asset('/js/function/seller/finance.js') }}" type="text/javascript"></script>

</html>
