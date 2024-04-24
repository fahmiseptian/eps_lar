$(function () {
    $("#example1").dataTable();
    $("#example2").dataTable({
        bPaginate: true,
        bLengthChange: false,
        bFilter: false,
        bSort: true,
        bInfo: true,
        bAutoWidth: false,
    });
});

function showDetail(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil data anggota menggunakan AJAX
    $.ajax({
        url: baseUrl + "/admin/member/" + id,
        method: "GET",
        success: function (response) {
            var member = response.member;

            if (member) {

                // Menampilkan informasi anggota dengan SweetAlert
                Swal.fire({
                    title: "Detail Anggota",
                    html: `
                        <table style="width:100%">
                            <tr>
                                <td style="width: 37%; text-align: right;"><strong>Email</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 60%; text-align: left;">${member.email || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 37%; text-align: right;"><strong>Instansi Satuan Kerja</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 60%; text-align: left;">${(member.satker || "") + (member.satker && member.instansi ? ', ' : '') + (member.instansi || "")}</td>
                            </tr>
                            <tr>
                                <td style="width: 37%; text-align: right;"><strong>Nama</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 60%; text-align: left;">${member.nama || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 37%; text-align: right;"><strong>No Telepon</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 60%; text-align: left;">${member.no_hp || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 37%; text-align: right;"><strong>NPWP</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 60%; text-align: left;">${member.npwp || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 37%; text-align: right;"><strong>Alamat</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 60%; text-align: left;">${member.npwp_address || ""}</td>
                            </tr>
                        </table>
                    `,
                    confirmButtonText: "Tutup",
                });
            } else {
                // Menampilkan pesan jika data anggota tidak ditemukan
                Swal.fire({
                    title: "Detail Anggota",
                    text: "Data anggota tidak ditemukan.",
                    icon: "error",
                    confirmButtonText: "Tutup",
                });
            }
        },
        error: function (xhr, status, error) {
            // Menampilkan pesan kesalahan
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Terjadi kesalahan saat memuat detail anggota.",
                icon: "error",
                confirmButtonText: "Tutup",
            });
        },
    });
}

function toggleStatus(id) {
    Swal.fire({
        title: 'Memuat...',
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    // Mengambil token CSRF dari meta tag
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Mengirimkan permintaan AJAX untuk mengubah status anggota dengan ID yang diberikan
    console.log('Toggle status member dengan ID:', id);
    $.ajax({
        url: baseUrl + '/admin/member/' + id + '/toggle-status',
        type: 'POST',
        data: {
            // Menyertakan token CSRF dalam data
            _token: csrfToken
        },
        success: function(response) {
            console.log('Status anggota berhasil diubah');
            location.reload();
        },
        error: function(xhr, status, error) {
            console.error('Terjadi kesalahan saat mengubah status anggota:', error);
            alert('Terjadi kesalahan saat mengubah status anggota.');
        }
    });
}

function deleteMember(id) {
    // Menampilkan pesan konfirmasi SweetAlert
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus anggota ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi penghapusan, mengirimkan permintaan AJAX
            $.ajax({
                url: baseUrl + "/admin/member/" + id + "/delete",
                method: "GET",
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Anggota berhasil dihapus.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus anggota.',
                        'error'
                    );
                },
            });
        }
    });
}