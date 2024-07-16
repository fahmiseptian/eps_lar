<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>TEST</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ asset('/bootstraps/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
        type="text/css" />
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/Seller_center.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/skins/_all-skins.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Sweet Alert -->
    <script src="{{ asset('/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <link href="{{ asset('/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body class="skin-blue">
    <div class="wrapper">

        @include('seller.asset.topbar')


        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">
                <!-- Sidebar user panel -->

                <div class="user-panel">
                    {{-- <div class="pull-left image">
                        <img src="" class="img-circle" alt="User Image" />
                    </div> --}}
                    <div class="pull-left info">
                        <a href="#" class="seller_type">
                            <img src="{{ asset('/img/app/trusted-seller.png') }}" />
                        </a>
                        <h5> <b><?= session()->get('seller') ?></b></h5>

                    </div>
                    <hr width="95%" align="center">
                </div>
                <div class="seller-balance">
                    <div class="info">
                        <p>Saldo Anda</p>
                        <h4>Rp {{ number_format(2131212, 0, ',', '.') }}</h4>
                        <hr width="100%" align="center">
                    </div>

                </div>
                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">
                    <li class="list-sidebar">
                        <a href="{{ route('seller') }}">
                            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="list-sidebar">
                        <a href="#">
                            <i class="fa fa-truck"></i> <span>Pengiriman</span> <i
                                class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a style="color: #eee" href="{{ route('seller.delivery') }}"><i
                                        class="fa fa-circle-o"></i> Jasa Pengiriman</a>
                            </li>
                            <li><a style="color: #eee" href="{{ route('seller.delivery.free-ongkir') }}"><i
                                        class="fa fa-circle-o"></i> Free Ongkir</a></li>
                        </ul>
                    </li>
                    <li class="list-sidebar">
                        <a href="{{ route('seller.order') }}">
                            <i class="fa fa-tasks"></i> <span>Pesanan Saya</span>
                        </a>
                    </li>
                    <li class="list-sidebar">
                        <a href="#">
                            <i class="fa fa-dropbox"></i> <span>Produk Saya</span> <i
                                class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a style="color: #eee" href="{{ route('seller.product') }}"><i
                                        class="fa fa-circle-o"></i> Produk</a></li>
                            <li><a style="color: #eee" href="{{ route('seller.product.add') }}"><i
                                        class="fa fa-circle-o"></i> Tambah Produk</a></li>
                            <li><a style="color: #eee" href="{{ route('seller.product.violation') }}"><i
                                        class="fa fa-circle-o"></i> Pelanggaran Saya</a></li>
                        </ul>
                    </li>
                    <li class="list-sidebar">
                        <a href="#">
                            <i class="fa fa-tags"></i> <span>Promosi</span>
                        </a>
                    </li>
                    <li class="list-sidebar">
                        <a href="{{ route('seller.nego') }}">
                            <i class="fa fa-comments"></i> <span>Nego Bela Pengadaan</span>
                        </a>
                    </li>
                    <li class="list-sidebar">
                        <a href="#">
                            <i class="fa fa-money"></i> <span>Keuangan</span> <i
                                class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a style="color: #eee" href="{{ route('seller.finance') }}"><i
                                        class="fa fa-circle-o"></i> Penghasilan</a></li>
                            <li><a style="color: #eee" href="{{ route('seller.finance.saldo') }}"><i
                                        class="fa fa-circle-o"></i> Saldo</a></li>
                            <li><a style="color: #eee" href="{{ route('seller.finance.rekening') }}"><i
                                        class="fa fa-circle-o"></i> Rekening</a></li>
                            <li><a style="color: #eee" href="{{ route('seller.finance.pembayaran') }}"><i
                                        class="fa fa-circle-o"></i> Pembayaran</a></li>
                        </ul>
                    </li>
                    <li class="list-sidebar">
                        <a href="#">
                            <i class="fa fa-share"></i> <span>Layanan Pembeli</span> <i
                                class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Asistent
                                    Chat</a></li>
                        </ul>
                    </li>
                    <li class="list-sidebar">
                        <a href="#">
                            <i class="fa fa-pie-chart"></i> <span>Data</span> <i
                                class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Kesehatan
                                    Toko</a></li>
                            <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Bisnis</a>
                            </li>
                    </li>
                </ul>
                </li>
                <li class="list-sidebar">
                    <a href="#">
                        <i class="fa fa-book"></i> <span>Toko</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Penilaian</a>
                        </li>
                        <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Profil</a></li>
                        <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Etalase</a>
                        </li>
                    </ul>
                </li>
                <li class="list-sidebar">
                    <a href="#">
                        <i class="fa fa-cog"></i> <span>Pengaturan</span> <i class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Alamat</a></li>
                        <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Pengaturan
                                Toko</a></li>
                </li>
                </ul>
                </li>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>


        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <h3 style="margin-left: 15px; margin-bottom:-5px"> <b> Kontrak </b></h3>
                        <hr>
                        <div class="box box-warning">
                            <div class="box-body">
                                <form id="tambahProdukpromosiForm">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td colspan="2">
                                                <div class="form-group">
                                                    <p style="font-size: 14px">No Kontrak</p>
                                                    <input type="text" class="form-control" id="noKontrak"
                                                        name="noKontrak" required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <p style="font-size: 14px">Total Harga</p>
                                                    <input type="text" class="form-control" id="totalHarga"
                                                        name="totalHarga" readonly required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <p style="font-size: 14px">Nilai Kontrak</p>
                                                    <input type="text" class="form-control" id="nilaiSayang"
                                                        name="nilaiSayang" readonly required>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="form-group">
                                                    <p style="font-size: 14px">Tanggal Kontrak</p>
                                                    <input type="text" class="form-control" id="tanggalKontrak"
                                                        name="tanggalKontrak" required>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <p style="font-size: 14px">Catatan</p>
                                                    <input type="text" class="form-control" id="catatan"
                                                        name="catatan" required hidden>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <div class="form-group">
                                                    <p style="font-size: 14px">Document</p>
                                                    <textarea id="document" name="document"></textarea>
                                                    <embed src="" type="application/pdf" width="100%" >
                                                </div>

                                            </td>
                                        </tr>
                                    </table>
                                    <button type="submit" class="btn btn-primary">Kirim</button>
                                </form>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->


    <!-- Modal -->
    <div id="negoUlangModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="negoUlangModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="negoUlangModalLabel">Nego Ulang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Form atau konten modal Anda di sini -->
                    <form id="negoUlangForm">
                        <!-- Form fields -->
                        <div class="form-group">
                            <label for="negoPrice">Harga Baru</label>
                            <input type="number" class="form-control" id="negoPrice" name="negoPrice" required>
                        </div>
                        <div class="form-group">
                            <label for="negoNote">
                                <div style="display: flex">
                                    <span id="google-icon" class="material-icons">contract_edit</span>
                                </div>

                            </label>
                            <textarea class="form-control" id="negoNote" name="negoNote" required></textarea>
                        </div>
                        <!-- Add other form fields as needed -->
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
{{-- footer --}}
@include('seller.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/seller/nego.js') }}" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        // Handle modal display when the link is clicked
        $('.btn-app[data-text="Nego Ulang"]').on('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            $('#negoUlangModal').modal('show'); // Show the modal
        });

        // Handle form submission
        $('#negoUlangForm').on('submit', function(event) {
            event.preventDefault();
            console.log($(this).serialize());
        });
    });
</script>

</html>
