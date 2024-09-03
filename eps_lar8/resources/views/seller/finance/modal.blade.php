<!-- Modal -->
<div id="updatePin" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content" id="modal-update-pin">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h3 class="modal-title">Upadte PIN Saldo</h3>
                <small>Hindari penggunaan angka yang Berurutan atau Berulang, Tanggal Lahir, dan PIN ATM sebagai PIN
                    Saldo Penjual-mu</small>
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


<!-- Modal -->
<div id="tarikSaldo" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="tarikSaldoLabel">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="tarikSaldoLabel">Pilih Transaksi</h4>
            </div>
            <div class="modal-body">
                <table id="tableTarikSaldo" class="table table-bordered table-hover"
                    style="width: 100%; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Pembeli</th>
                            <th>Jumlah Dana</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot hidden>
                        <tr>
                            <th>Invoice</th>
                            <th>Pembeli</th>
                            <th>Jumlah Dana</th>
                            <th>Aksi</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Tutup</button>
                <button id="selectAll" class="btn btn-default">Pilih Semua</button>
                <button id="requestSaldo" type="button" class="btn btn-default">Kirim</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- Modal -->
<div id="modalPINSaldo" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Penarikan Dana</h4>
            </div>
            <div class="modal-body">
                <form id="RequestTarikSaldo">
                    <div class="form-group">
                        <input type="text" name="idTrx" id="idTrx" hidden>
                        <label for="newPin">Masukan PIN</label>
                        <div class="pin-input">
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
                <button type="button" class="btn btn-default" data-dismiss="modal">Batalkan</button>
                <button type="button" class="btn btn-primary" id="sendRequestTarikSaldo">Kirim</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

{{-- Modal Detail TR --}}
<div id="modalDetailTr" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog custom-modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="info-trx">Detail Trx</h4>
            </div>
            <div class="modal-body">
                <table id="tableDetailTrx" class="table table-bordered"
                    style="width: 100%; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot hidden>
                        <tr>
                            <th>Invoice</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
