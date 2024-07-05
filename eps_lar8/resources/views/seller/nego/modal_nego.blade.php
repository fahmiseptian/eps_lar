<div id="negoUlangModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Nego Ulang</h4>
            </div>
            <div class="modal-body">
                <!-- Form atau konten modal Anda di sini -->
                <form id="negoUlangForm">
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <b>Kuantitas</b>
                            </td>
                            <td colspan="2" align="center">
                                <input id="qty" name="qty" readonly></input>
                                <input type="text" name="product" id="product" hidden>
                                <input type="text" name="id_nego" id="id_nego" hidden>
                                <input type="text" name="last_id" id="last_id" hidden>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Nego Pembeli</p>
                                    <small style="font-size: 10px">(sudah termasuk PPn & PPh)</small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="hargaSatuan">Harga Nego Satuan</label>
                                    <input type="text" class="form-control" id="hargaSatuan" name="hargaSatuan" readonly required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="hargaNegoTotal">Harga Nego Total</label>
                                    <input type="text" class="form-control" id="hargaNegoTotal" name="hargaSatuanTotal" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Diterima Penjual</p>
                                    <small style="font-size: 10px">(sudah termasuk PPn & PPh)</small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="hargaDiterimaSatuan">Harga Diterima Satuan</label>
                                    <input type="text" class="form-control" id="hargaDiterimaSatuan" name="hargaDiterimaSatuan" readonly required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="hargaDiterimaTotal">Harga Diterima Total</label>
                                    <input type="text" class="form-control" id="hargaDiterimaTotal" name="hargaDiterimaTotal" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Harga Respon Satuan</p>
                                    <small style="font-size: 10px">(sudah termasuk PPn & PPh)</small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="hargaResponSatuan">Harga Respon Satuan</label>
                                    <input type="text" class="form-control" id="hargaResponSatuan" name="hargaResponSatuan" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="hargaResponTotal">Harga Respon Total</label>
                                    <input type="text" class="form-control" id="hargaResponTotal" name="hargaResponTotal" readonly required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <div class="form-group">
                                    <label for="negoNote">Catatan</label>
                                    <textarea class="form-control" id="negoNote" name="negoNote"></textarea>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <!-- Add other form fields as needed -->
            </div>
            <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
