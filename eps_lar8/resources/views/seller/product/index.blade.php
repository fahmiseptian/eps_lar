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
                        <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Daftar Produk</b></h3>
                        <small style="margin-left: 15px;">Fitur managemen produk untuk Penjual.</small>
                        <hr>
                        <div class="list-tipeProduk" style="margin-top: -10px">
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
                                <li>
                                    <a class="btn btn-info fa fa-plus-square-o"
                                        href="{{ route('seller.product.add') }}">&nbsp;Tambah</a>
                                </li>
                            </ul>
                        </div>
                        <div class="box" style="background-color: #e3f2fd; border: 2px solid #FC6703; border-radius: 10px; box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.15); padding: 16px; margin-bottom: 20px;">
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="example2" class="table table-bordered table-hover table-striped"
                                        style="width: 100%">
                                        <thead style="background-color: #fff;">
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
                                                    <td align="center">
                                                        <a id="editStatus" data-id="{{$product->id}}">
                                                            <img style="width: 50px"
                                                                src="{{ asset('/img/app/' . ($product->status_display == 'Y' ? 'tayang.png' : 'tidak-tayang.png')) }}"
                                                                alt="{{ $product->status_display == 'Y' ? 'Aktif' : 'Tidak Aktif' }}">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <div style="display: flex">
                                                            <a id="edit_product" data-id="{{$product->id}}" class="material-icons" href="{{ route('seller.product.edit', ['id' => $product->id]) }}">edit</a>
                                                            <a id="review_product" data-id="{{$product->id}}" class="material-icons">rate_review</a>
                                                            <a id="deleteProduct" data-id="{{$product->id}}" class="material-icons">delete</a>
                                                        </div>
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
