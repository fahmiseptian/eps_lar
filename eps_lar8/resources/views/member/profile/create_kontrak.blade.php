<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Data Kontrak</h5>
            <button class="btn btn-info" onclick="kembaliKeDetailTransaksi()">
                <i class="material-icons">arrow_back</i>
            </button>
        </div>

        @if($kontrak)
        <form id="kontrakForm">
            <div class="mb-3">
                <label for="no_kontrak" class="form-label">No Kontrak</label>
                <input type="text" class="form-control" id="no_kontrak" name="no_kontrak" value="{{ $kontrak->no_kontrak }}" required>
                <input type="hidden" class="form-control" id="id_cs" name="id_cs" value="{{ $id_cart_shop }}" required>
            </div>
            <div class="mb-3">
                <label for="total_harga" class="form-label">Total Harga</label>
                <input type="number" class="form-control" id="total_harga" name="total_harga" value="{{ number_format($kontrak->total ?? $kontrak->total_harga, 0, '.', '.') }}" required readonly>
            </div>
            <div class="mb-3">
                <label for="nilai_kontrak" class="form-label">Nilai Kontrak</label>
                <input type="number" class="form-control" id="nilai_kontrak" name="nilai_kontrak" value="{{ $kontrak->total ?? $kontrak->total_harga }}" required readonly>
            </div>
            <div class="mb-3">
                <label for="tanggal_kontrak" class="form-label">Tanggal Kontrak</label>
                <input type="date" class="form-control" id="tanggal_kontrak" name="tanggal_kontrak" value="{{ $kontrak->tanggal_kontrak }}" required>
            </div>
            <div class="mb-3">
                <label for="catatan" class="form-label">Catatan</label>
                <textarea class="form-control" id="catatan" name="catatan">{{ $kontrak->catatan }}</textarea>
            </div>
            <div class="mb-3">
                <label for="dokumen_kontrak" class="form-label"><b>* note: Mohon dirubah pada bagian text yang berwarna:</b></label> <br>
                <label for="dokumen_kontrak" class="form-label"><span style="color: red;"> Pembeli : Merah </span> &nbsp; &nbsp; &nbsp; <span style="color: blue;"> Seller : Biru </span></label>
                <textarea class="form-control" id="dokumen_kontrak" name="dokumen_kontrak" style="height: auto;">{{ $htmlContent }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
        @else
        <div class="alert alert-info">
            <p>Kontrak belum tersedia.</p>
            <button class="btn btn-success" onclick="buatKontrak()">
                <i class="material-icons">add</i> Buat Kontrak
            </button>
        </div>
        @endif
    </div>
</div>


<script>
    function initTinyMCE() {
        if (tinymce.get('dokumen_kontrak')) {
            tinymce.remove('#dokumen_kontrak');
        }
        tinymce.init({
            selector: '#dokumen_kontrak',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
            height: 300
        });
    }

    initTinyMCE();

    function kembaliKeDetailTransaksi() {
        var id_cart_shop = "{{ $id_cart_shop }}";
        var url = "{{ route('profile.kontrak') }}?id=" + id_cart_shop + "&token=" + token;
        loadContent(url, $('#contentArea'));
    }

    function buatKontrak() {
        // Implementasi fungsi buat kontrak
        alert('Membuat kontrak baru');
    }

    document.getElementById('kontrakForm').addEventListener('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        // Menghilangkan format angka pada total_harga
        var totalHarga = document.getElementById('total_harga').value;
        totalHarga = totalHarga.replace(/\./g, '').replace(',', '.'); // Menghapus titik dan mengganti koma dengan titik
        formData.set('total_harga', totalHarga);

        // Jika menggunakan TinyMCE, tambahkan konten editor ke formData
        if (tinymce.get('dokumen_kontrak')) {
            formData.set('dokumen_kontrak', tinymce.get('dokumen_kontrak').getContent());
        }

        // Menampilkan isi FormData di console
        for (var pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        $.ajax({
            url: appUrl + "/api/storeKontrak?token=" + token,
            type: 'POST',
            data: formData,
            processData: false,
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