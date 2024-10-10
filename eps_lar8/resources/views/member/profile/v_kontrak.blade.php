<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Data Kontrak</h5>
            <button class="btn btn-info rounded-box shadow-sm" onclick="kembaliKeDetailTransaksi()" title="Kembali ke Detail Transaksi">
                <i class="material-icons">arrow_back</i>
            </button>
        </div>
        @if($kontrak)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No Kontrak</th>
                    <th>Tanggal Kontrak</th>
                    <th>Catatan</th>
                    <th>Tanggal Buat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $kontrak->no_kontrak }}</td>
                    <td>{{ $kontrak->tanggal_kontrak }}</td>
                    <td>{{ $kontrak->catatan }}</td>
                    <td>{{ $kontrak->created_date }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="downloadKontrak('{{ $id_cart_shop }}')">
                            <i class="material-icons">download</i>
                        </button>
                        <button class="btn btn-sm btn-warning" style="color: white;" onclick="editKontrak('{{ $kontrak->id }}')">
                            <i class="material-icons">edit</i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
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
    function downloadKontrak(id) {
        $.ajax({
            url: appUrl + "/api/download-kontrak",
            type: "post",
            data: {
                idcs: id,
            },
            xhrFields: {
                responseType: "blob",
                withCredentials: true,
            },
            success: function(response, status, xhr) {
                var blob = new Blob([response], {
                    type: "application/pdf"
                });
                var link = document.createElement("a");
                link.href = window.URL.createObjectURL(blob);
                link.download = "kontrak_" + Date.now() + ".pdf"; // Nama file yang diunduh
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            error: function(xhr, status, error) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function() {
                $("#overlay").hide();
            },
        });
    }

    function editKontrak(id) {
        // Implementasi fungsi edit
        var url = "{{ route('profile.edit-kontrak') }}?id=" + id;
        loadContent(url, $('#contentArea'));
    }

    function buatKontrak() {
        var id_cart_shop = "{{ $id_cart_shop }}";
        var id_shop = "{{ $id_shop }}";
        var url = "{{ route('profile.create-kontrak') }}?id=" + id_cart_shop + "&id_shop=" + id_shop;
        loadContent(url, $('#contentArea'));
    }

    function kembaliKeDetailTransaksi() {
        var id_cart = "{{ $id_cart }}";

        var url = "{{ route('profile.transaksi.detail') }}?id=" + id_cart + "&token=" + token;
        loadContent(url, $('#contentArea'));
    }
</script>