<!-- Modal -->
<div id="tambahProdukpromosi" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Tambah Promosi Produk</h4>
            </div>
            <div class="modal-body">
                <form id="tambahProdukpromosiForm">
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2">
                                <div class="form-group">
                                    <p style="font-size: 14px">Produk</p>
                                    <select class="form-control" id="produkSelect" name="Produk" required>
                                        <option value="">Pilih Produk</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div class="form-group">
                                    <p style="font-size: 14px">Kategori Promo</p>
                                    <select class="form-control" id="kategoriSelect" name="Produk" required>
                                        <option value="">Pilih Kategori</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Normal</p>
                                    <input type="text" class="form-control" id="price" name="price" readonly required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Tayang Normal</p>
                                    <input type="text" class="form-control" id="hargaTayang" name="hargaTayang" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Promo</p>
                                    <input type="text" class="form-control" id="promoPrice" name="promoPrice" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Tayang Promo</p>
                                    <input type="text" class="form-control" id="promoTayangPrice" name="promoTayangPrice" readonly required>
                                </div>
                            </td>
                        </tr>
                    </table>
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
