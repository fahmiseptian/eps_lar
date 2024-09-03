<!-- Modal -->
<div id="kategoriProduk" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Kategori Produk</h4>
                <small>Silakan pilih sesuai dengan produk yang akan dijual</small>
            </div>
            <div class="modal-body">
                <div class="category-container">
                    <div class="category-column">
                        <ul id="lv1">
                            <!-- Categories Level 1 will be appended here -->
                        </ul>
                    </div>
                    <div class="category-column">
                        <ul id="lv2">
                            <!-- Categories Level 2 will be appended here -->
                        </ul>
                    </div>
                </div>
                <div class="category-final">
                    <b id="txt-ket-lv1" data-idlv1="">Kategori level 1</b> -> <b id="txt-ket-lv2" data-idlv2="">Kategori level 2</b>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="saveKategori">Simpan</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
