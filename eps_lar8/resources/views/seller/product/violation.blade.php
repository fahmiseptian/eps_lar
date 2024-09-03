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
                            <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Pelanggaran saya</b></h3>
		                    <small style="margin-left: 15px;">Produk yang melanggar dan dilaporkan oleh pengguna.</small>
                            <hr>
                            <div class="box-body">
                                <table id="data-table" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th class="detail-full">Kategori</th>
                                            <th class="detail-full">Jenis</th>
                                            <th class="detail-full">Detail</th>
                                            <th class="detail-full">Saran</th>
                                            <th class="detail-small">Pelanggaran</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach($violations as $violation)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $violation->nameProduct }}</td>
                                            <td class="detail-full">{{ $violation->category }}</td>
                                            <td class="detail-full">{{ $violation->type }}</td>
                                            <td class="detail-full">{{ $violation->detail }}</td>
                                            <td class="detail-full">{{ $violation->saran }}</td>
                                            <td class="detail-small">{{ $violation->detail }}</td>
                                            <td>
                                                @if($violation->status == 0)
                                                <p class="fa fa-search" style="color:yellowgreen">&nbsp; Sedang DItinjau</p>
                                                @elseif($violation->status == 1)
                                                <p class="fa fa-check-circle" style="color: green">&nbsp; Laporan Disetujui</p>
                                                @else
                                                <p class="fa fa-minus-circle" style="color: red">&nbsp; Laporan Di tidak Valid</p>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Barang</th>
                                            <th class="detail-full">Kategori</th>
                                            <th class="detail-full">Jenis</th>
                                            <th class="detail-full">Detail</th>
                                            <th class="detail-full">Saran</th>
                                            <th class="detail-small">Pelanggaran</th>
                                            <th>Status</th>
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
@include('seller.product.modal')
@include('seller.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/seller/product.js') }}" type="text/javascript"></script>

</html>
