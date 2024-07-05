<!-- Modal -->
<div id="updatePin" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Pembaharui PIN</h4>
            </div>
            <div class="modal-body">
                <form id="updatePinForm">
                    <div class="form-group">
                        <label for="newPin">PIN Baru</label>
                        <div class="pin-input" id="newPin">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirmNewPin">Konfirmasi PIN Baru</label>
                        <div class="pin-input" id="confirmNewPin">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                            <input type="password" class="form-control pin-digit" maxlength="1">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-primary" id="savePin">Simpan</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
