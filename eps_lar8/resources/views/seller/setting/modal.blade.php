<!-- Modal -->
<div id="modaleditAddress" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"> Alamat Toko</h4>
            </div>
            <div class="modal-body">
                <form id="formeditAddress">
                    <table class="table table-bordered">
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Nama Kamu / Toko</p>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                    <input type="text" id="id_address" name="id_address" hidden>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">No Telepon </p>
                                    <input type="text" class="form-control" id="telp" name="telp" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Provinsi</p>
                                    <select class="form-control" id="provinsi" name="provinsi" required>
                                        <option value="" disabled selected>Pilih Provinsi</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Kota</p>
                                    <select class="form-control" id="kota" name="kota" required>
                                        <option value="" disabled selected>Pilih Kota</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Kecamatan</p>
                                    <select class="form-control" id="kecamatan" name="kecamatan" required>
                                        <option value="" disabled selected>Pilih Kecamatan</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Kode Pos</p>
                                    <input type="text" class="form-control" id="kd_pos" name="kd_pos" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label for="detail_address" style="font-size: 14px">Alamat Detail</label>
                                    <textarea class="form-control" id="detail_address" name="detail_address" rows="4" cols="50" required></textarea>
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
