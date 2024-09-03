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
                        <div class="box box-info">
                            <div class="box-body">
                                <form id="addProduct" method="POST" action="{{ route('seller.product.addProduct') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="name">Nama Barang:</label>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ $product->name }}" placeholder="Masukkan nama barang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="spesifikasi">Spesifikasi:</label>
                                        <textarea name="spesifikasi" id="spesifikasi" class="form-control" placeholder="Masukkan spesifikasi" required>{{ $product->spesifikasi }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="harga">Harga:</label>
                                        <input type="number" name="harga" id="harga" value="{{ $product->price }}" class="form-control" placeholder="Masukkan harga" required>
                                    </div>
                                    <div id="phpVariables"
                                        data-ppn="{{ $ppn }}"
                                        data-pph="{{ $pph }}"
                                        data-mp-percent="{{ $mp_percent }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="hargaBelumPPn">Harga Tayang Sebelum PPn:</label>
                                        <input type="number" name="hargaBelumPPn" id="hargaBelumPPn" class="form-control" readonly required>
                                    </div>
                                    <div class="form-group">
                                        <label for="ppn">PPN:</label>
                                        <input type="number" name="ppn" id="ppn" class="form-control" readonly required>
                                    </div>
                                    <div class="form-group">
                                        <label for="hargaSudahPPn">Harga Tayang Sesudah PPn:</label>
                                        <input type="number" name="hargaSudahPPn" id="hargaSudahPPn" class="form-control" readonly required>
                                    </div>
                                    <div class="form-group">
                                        <label for="stok">Stok:</label>
                                        <input type="number" name="stok" id="stok" value="{{ $product->stock }}" class="form-control" placeholder="Masukkan stok" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Gambar Produk:</label>
                                        <div style="display: flex; flex-wrap: nowrap; overflow-x: auto;">
                                            @foreach($product->artwork_url_sm as $index => $url)
                                                <div style="margin-right: 10px;">
                                                    <img src="{{ $url }}" alt="Product Image {{ $index + 1 }}" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                    <input type="file" name="images[]" class="form-control-file image-input" accept="image/*">
                                                </div>
                                            @endforeach
                                            @for ($i = count($product->artwork_url_sm); $i < 5; $i++)
                                                <div style="margin-right: 10px;">
                                                    <img src="https://via.placeholder.com/100" alt="Placeholder Image" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                    <input type="file" name="images[]" class="form-control-file" accept="image/*">
                                                </div>
                                            @endfor
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="berat">Berat:</label>
                                        <input type="number" name="berat" id="berat" value="{{ $product->weight }}" class="form-control" placeholder="Masukkan berat produk" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="dimensi">Dimensi:</label>
                                        <div class="row">
                                            <div class="col-xs-4">
                                                <input type="text" name="dimensipanjang" id="dimensi-panjang" value="{{ $product->dimension_length }}" class="form-control" placeholder="Panjang (cm)">
                                            </div>
                                            <div class="col-xs-4">
                                                <input type="text" name="dimensilebar" id="dimensi-lebar" value="{{ $product->dimension_width }}" class="form-control" placeholder="Lebar (cm)">
                                            </div>
                                            <div class="col-xs-4">
                                                <input type="text" name="dimensitinggi" id="dimensi-tinggi" value="{{ $product->dimension_high }}" class="form-control" placeholder="Tinggi (cm)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Pre Order: &nbsp;
                                            <input type="radio" name="preorder" value="Y" {{ $product->status_preorder == 'Y' ? 'checked' : '' }}> Ya &nbsp;
                                            <input type="radio" name="preorder" value="N" {{ $product->status_preorder == 'N' ? 'checked' : '' }}> Tidak
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Produk Dalam Negeri: &nbsp;
                                            <input type="radio" name="produk_dalam_negeri" value="1" {{ $product->is_pdn == 1 ? 'checked' : '' }}> Ya &nbsp;
                                            <input type="radio" name="produk_dalam_negeri" value="0" {{ $product->is_pdn == 0 ? 'checked' : '' }}> Tidak
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>Kondisi: &nbsp;
                                            <input type="radio" name="kondisi" value="Y" {{ $product->status_new_product == 'Y' ? 'checked' : '' }}> Baru &nbsp;
                                            <input type="radio" name="kondisi" value="N" {{ $product->status_new_product == 'N' ? 'checked' : '' }}> Bekas
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label for="sku">SKU Induk:</label>
                                        <input type="text" name="sku" id="sku" value="{{ $product->sku }}" class="form-control" placeholder="Masukkan SKU produk" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>
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
