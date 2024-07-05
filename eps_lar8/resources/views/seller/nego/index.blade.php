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
                        <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Nego Produk</b></h3>
                        <hr>
                        <div>
                            <ul class="horizontal-list">
                                <li class="active" id="change-nego" data-kondisi="belum_direspon">Belum Direspon</li>
                                <li id="change-nego" data-kondisi="nego_ulang">Nego Ulang</li>
                                <li id="change-nego" data-kondisi="telah_direspon">Telah Direspon</li>
                                <li id="change-nego" data-kondisi="nego_batal">Nego Dibatalkan</li>
                            </ul>
                        </div>
                        {{-- Data --}}
                        &nbsp;
                        <br>
                        <div class="box"
                            style="background-color: #e3f2fd; border: 2px solid #FC6703; border-radius: 10px; box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.15); padding: 16px; margin-bottom: 20px;">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-hover table-striped"
                                        style="width: 100%">
                                        <thead style="background-color: #fff;">
                                            <tr>
                                                <th class="detail-full">Gambar</th>
                                                <th>Nama Barang</th>
                                                <th class="detail-full">QTY</th>
                                                <th class="detail-full">Harga Nego</th>
                                                <th class="detail-full">Harga yang di terima Seller</th>
                                                <th>Status Nego</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($negos as $nego)
                                                <tr>
                                                    <td class="detail-full">
                                                        @if (isset($nego->dataProduct->artwork_url_md[0]))
                                                            <img src="{{ $nego->dataProduct->artwork_url_md[0] }}"
                                                                style="width:50px; width:50px" alt="Product Image">
                                                        @else
                                                            No Image
                                                        @endif
                                                    </td>
                                                    <td>{{ $nego->dataProduct->name }}</td>
                                                    <td class="detail-full">{{ $nego->qty }}</td>
                                                    <td class="detail-full">
                                                        Rp.{{ str_replace(',', '.', number_format($nego->harga_nego / $nego->qty)) }}
                                                    </td>
                                                    <td class="detail-full">
                                                        Rp.{{ str_replace(',', '.', number_format($nego->nominal_didapat / $nego->qty)) }}
                                                    </td>
                                                    <td>
                                                        @if ($nego->status == 0)
                                                            Diajukan
                                                        @elseif ($nego->status == 1)
                                                            Diterima
                                                        @else
                                                            Ditolak
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a id="detailNego" data-id="{{ $nego->id_nego }}"><i
                                                                id="google-icon" class="material-icons">info</i> Detail
                                                            Nego
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th class="detail-full">Gambar</th>
                                                <th>Nama Barang</th>
                                                <th class="detail-full">QTY</th>
                                                <th class="detail-full">Harga Nego</th>
                                                <th class="detail-full">Harga yang di terima Seller</th>
                                                <th>Status Nego</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    @include('seller.nego.modal_nego')
</body>
{{-- footer --}}
@include('seller.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/seller/nego.js') }}" type="text/javascript"></script>

</html>
