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
                                        <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama gambar" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Kategori:</label>
                                        <div class="row">
                                            <div class="col-xs-5" id="kategorilevel1">
                                                <select type="text" name="kategorilevel1" id="kategori-level1" class="form-control">
                                                    <option value="">Pilih Kategori Level 1</option>
                                                    @foreach($categorylevel1 as $level1)
                                                        <option value="{{$level1->id}}">{{$level1->name}}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-xs-5" id="kategorilevel2" style="display: none;">
                                                <select type="text" name="kategorilevel2" id="kategori-level2" class="form-control">
                                                    <option value="">Pilih Kategori Level 2</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="merek">Merek:</label>
                                            <select name="merek" id="merek" class="form-control select2">
                                                <option value="">Pilih Merek Produk</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Spesifikasi:</label>
                                        <textarea name="spesifikasi" id="spesifikasi" class="form-control" placeholder="Masukkan nama gambar" required>
                                        </textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="name">Harga:</label>
                                        <input type="number" name="harga" id="harga" class="form-control" placeholder="Masukkan harga" required>
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
                                        <label for="name">Stok:</label>
                                        <input type="number" name="stok" id="stok" class="form-control" placeholder="Masukkan Stok Barang" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="image1">Masukan Gambar Product:</label>
                                        <div style="display: flex; flex-wrap: nowrap; overflow-x: auto;">
                                            <div style="margin-right: 10px;">
                                                <img src="https://via.placeholder.com/100" alt="Placeholder Image" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                <input type="file" name="images[]" class="form-control-file" accept="image/*">
                                            </div>
                                            <div style="margin-right: 10px;">
                                                <img src="https://via.placeholder.com/100" alt="Placeholder Image" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                <input type="file" name="images[]" class="form-control-file" accept="image/*">
                                            </div>
                                            <div style="margin-right: 10px;">
                                                <img src="https://via.placeholder.com/100" alt="Placeholder Image" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                <input type="file" name="images[]" class="form-control-file" accept="image/*">
                                            </div>
                                            <div style="margin-right: 10px;">
                                                <img src="https://via.placeholder.com/100" alt="Placeholder Image" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                <input type="file" name="images[]" class="form-control-file" accept="image/*">
                                            </div>
                                            <div style="margin-right: 10px;">
                                                <img src="https://via.placeholder.com/100" alt="Placeholder Image" style="width: 100px; height: 100px; display: block; margin-bottom: 5px;">
                                                <input type="file" name="images[]" class="form-control-file" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Berat:</label>
                                        <input type="number" name="berat" id="berat" class="form-control" placeholder="Masukkan berat Product" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Dimensi:</label>
                                        <div class="row">
                                            <div class="col-xs-4">
                                              <input type="text" name="demensipanjang" id="demensi-panjang" class="form-control" placeholder="Panjang (cm)">
                                            </div>
                                            <div class="col-xs-4">
                                              <input type="text" name="demensilebar" id="demensi-lebar" class="form-control" placeholder="Lebar (cm)">
                                            </div>
                                            <div class="col-xs-4">
                                              <input type="text" name="demensitinggi" id="demensi-tinggi" class="form-control" placeholder="Tinggi (cm)">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            Pre Order: &nbsp;
                                            <input type="radio" name="preorder" value="Y" /> Ya &nbsp;
                                            <input type="radio" name="preorder" value="N" checked/> Tidak
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            Produk Dalam Negeri: &nbsp;
                                            <input type="radio" name="produk_dalam_negeri" value="1" checked/> Ya &nbsp;
                                            <input type="radio" name="produk_dalam_negeri" value="0" /> Tidak
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            Kondisi: &nbsp;
                                            <input type="radio" name="kondisi" value="Y" /> Baru &nbsp;
                                            <input type="radio" name="kondisi" value="N" checked/> Bekas
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <label for="name">SKU Induk:</label>
                                        <input type="text" name="sku" id="sku" class="form-control" placeholder="Masukkan SKU Produk">
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
@include('seller.asset.footer')
<!-- page script -->
<script src="{{ asset('/js/function/seller/product.js') }}" type="text/javascript"></script>

</html>
