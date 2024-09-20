<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Data Surat Pesanan </h5>
            <button class="btn btn-info" onclick="kembaliKeDetailTransaksi()">
                <i class="material-icons">arrow_back</i>
            </button>
        </div>

        @if($suratpesanan)
        <form id="suratpesananfrom">
            <div class="mb-3">
                <label for="no_invoice" class="form-label">No Invoice</label>
                <input type="text" class="form-control" id="no_invoice" name="no_invoice" value="{{$suratpesanan->invoice . '-' . $id_cart_shop }}" required readonly>
                <input type="hidden" class="form-control" id="id_cs" name="id_cs" value="{{ $id_cart_shop ?? $suratpesanan->id_cart_shop }}" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_pesan" class="form-label">Tanggal Surat Pesanan</label>
                <input type="date" class="form-control" id="tanggal_pesan" name="tanggal_pesan" value="{{ $suratpesanan->tanggal_pesan }}" required>
            </div>
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan">{{ $suratpesanan->catatan }}</textarea>
            </div>
            <div class="mb-3">
                <label for="dokumen_suratpesanan" class="form-label"><b>* note: Mohon dirubah pada bagian text yang berwarna:</b></label> <br>
                <label for="dokumen_suratpesanan" class="form-label"><span style="color: red;"> Pembeli : Merah </span> &nbsp; &nbsp; &nbsp; <span style="color: blue;"> Seller : Biru </span></label>
                <textarea class="form-control" id="dokumen_suratpesanan" name="dokumen_suratpesanan" style="height: auto;">{{ $htmlContent }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
        @else
        <div class="alert alert-info">
            <p>Surat Pesanan belum tersedia.</p>
            <button class="btn btn-success" onclick="buatSuratPesanan()">
                <i class="material-icons">add</i> Buat Surat Pesanan
            </button>
        </div>
        @endif
    </div>
</div>


<script>
    function initTinyMCE() {
        if (tinymce.get('dokumen_suratpesanan')) {
            tinymce.remove('#dokumen_suratpesanan');
        }
        tinymce.init({
            selector: '#dokumen_suratpesanan',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 300
        });
    }

    initTinyMCE();

    function kembaliKeDetailTransaksi() {
        var id_cart_shop = "{{ $id_cart_shop ?? $suratpesanan->id_cart_shop }}";
        var url = "{{ route('profile.suratpesanan') }}?id=" + id_cart_shop;
        loadContent(url, $('#contentArea'));
    }

    document.getElementById('suratpesananfrom').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        if (tinymce.get('dokumen_suratpesanan')) {
            formData.set('dokumen_suratpesanan', tinymce.get('dokumen_suratpesanan').getContent());
        }

        $.ajax({
            url: appUrl + "/api/storeSuratPesanan",
            data: formData,
            processData: false,
            method: 'POST',
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    kembaliKeDetailTransaksi();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Gagal menyimpan kontrak: ' + response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan: ' + error,
                });
            }
        });
    });
</script>