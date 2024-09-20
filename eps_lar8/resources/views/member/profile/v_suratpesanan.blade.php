<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title">Data Surat Pesanan</h5>
            <button class="btn btn-info rounded-box shadow-sm" onclick="kembaliKeDetailTransaksi()" title="Kembali ke Detail Transaksi">
                <i class="material-icons">arrow_back</i>
            </button>
        </div>
        @if($suratpesanan)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Tanggal Surat Pesanan</th>
                    <th>Catatan</th>
                    <th>Tanggal Buat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $suratpesanan->invoice }}</td>
                    <td>{{ $suratpesanan->tanggal_pesan }}</td>
                    <td>{{ $suratpesanan->catatan }}</td>
                    <td>{{ $suratpesanan->created_at }}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="downloadsuratpesanan('{{ $id_cart_shop }}')">
                            <i class="material-icons">download</i>
                        </button>
                        <button class="btn btn-sm btn-warning" style="color: white;" onclick="editsuratpesanan('{{ $suratpesanan->id }}')">
                            <i class="material-icons">edit</i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
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
    function kembaliKeDetailTransaksi() {
        var id_cart = "{{ $id_cart }}";

        var url = "{{ route('profile.transaksi.detail') }}?id=" + id_cart;
        loadContent(url, $('#contentArea'));
    }

    function editsuratpesanan(id) {
        var url = "{{ route('profile.edit-suratpesanan') }}?id=" + id;
        loadContent(url, $('#contentArea'));
    }   

    function buatSuratPesanan() {
        var id_cart_shop = "{{ $id_cart_shop }}";
        var id_shop = "{{ $id_shop }}";
        var url = "{{ route('profile.create-suratpesanan') }}?id=" + id_cart_shop + "&id_shop=" + id_shop;
        loadContent(url, $('#contentArea'));
    }

    function downloadsuratpesanan(id) {
        $.ajax({
            url: appUrl + "/api/download-sp",
            type: "post",
            data: {
                idcs: id,
                _token: csrfToken,
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
</script>