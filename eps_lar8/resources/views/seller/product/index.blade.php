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
                        <div class="list-tipeProduk">
                            <ul>
                                <li><a href="{{ route('seller.product') }}"
                                        class="{{ $tipe == null ? 'active' : '' }}">Semua</a></li>
                                <li><a href="javascript:;" data-status="live"
                                        class="{{ $tipe == 'live' ? 'active' : '' }}"
                                        onclick="toggleFilterProduct(this)">Live</a></li>
                                <li><a href="javascript:;" data-status="habis"
                                        class="{{ $tipe == 'habis' ? 'active' : '' }}"
                                        onclick="toggleFilterProduct(this)">Habis</a></li>
                                <li><a href="javascript:;" data-status="arsip"
                                        class="{{ $tipe == 'arsip' ? 'active' : '' }}"
                                        onclick="toggleFilterProduct(this)">Arsip</a></li>
                            </ul>
                        </div>
                        <div class="box box-info">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-hover" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th> Nama Barang</th>
                                                <th class="detail-full">SKU</th>
                                                <th class="detail-full">Harga</th>
                                                <th class="detail-full">Harga Tayang (include PPN)</th>
                                                <th class="detail-full">Stok</th>
                                                <th>Status Tampil</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $i = 1 @endphp
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>{{ $i++ }}</td>
                                                    <td><a href="#">{{ $product->name }}</a></td>
                                                    <td class="detail-full">{{ $product->sku }}</td>
                                                    <td class="detail-full">Rp.
                                                        {{ str_replace(',', '.', number_format($product->price_exclude)) }}
                                                    </td>
                                                    <td class="detail-full">Rp.
                                                        {{ str_replace(',', '.', number_format($product->price_tayang)) }}
                                                    </td>
                                                    <td class="detail-full">{{ $product->stock }}</td>
                                                    <td>Status Tampil</td>
                                                    <td>
                                                        <span>sasassa</span>
                                                        <span>sasassa</span>
                                                        <span>sasassa</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Barang</th>
                                                <th class="detail-full">SKU</th>
                                                <th class="detail-full">Harga</th>
                                                <th class="detail-full">Harga Tayang (include PPN)</th>
                                                <th class="detail-full">Stok</th>
                                                <th>Status Tampil</th>
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
</body>
{{-- footer --}}
@include('seller.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/seller/product.js') }}" type="text/javascript"></script>

</html>
