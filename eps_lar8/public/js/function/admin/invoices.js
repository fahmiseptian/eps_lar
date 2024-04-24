// invoices.js

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

// detail untuk Canceled Invoice
function detail(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil data anggota menggunakan AJAX
    $.ajax({
        url: baseUrl + "/admin/invoice/" + id,
        method: "GET",
        success: function (response) {
            var invoice = response.invoice;
            var member = response.member;
            var cartshop = response.cartshop;
            var shop = response.shop;
            if (invoice) {
                // Menampilkan informasi anggota dengan SweetAlert
                Swal.fire({
                    title: "Detail Pesanan",
                    html: `
                        <table style="width:100%">
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>No Invoice</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${invoice.invoice || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>Pembeli</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${member.nama || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>Penjual</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.name || ""}</td>
                            </tr>
                        </table>
                    `,
                    confirmButtonText: "Tutup",
                });
            } else {
                // Menampilkan pesan jika data anggota tidak ditemukan
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: "Data pesanan tidak ditemukan.",
                    icon: "error",
                    confirmButtonText: "Tutup",
                });
            }
        },
        error: function (xhr, status, error) {
            // Menampilkan pesan kesalahan
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Terjadi kesalahan saat memuat detail pesanan.",
                icon: "error",
                confirmButtonText: "Tutup",
            });
        },
    });
}

// Upload file invoice
function upload_cancel(invoiceId) {
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    Swal.fire({
        title: 'Pilih File',
        input: 'file',
        inputAttributes: {
            accept: '.pdf',
        },
        showCancelButton: true,
        confirmButtonText: 'Unggah',
        showLoaderOnConfirm: true,
        preConfirm: (file) => {
            const formData = new FormData();
            formData.append('file', file);

            return fetch(`/admin/invoice/upload/cancel/${invoiceId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Upload gagal.');
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Upload file gagal: ${error}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sukses!',
                text: 'File berhasil diunggah.',
                icon: 'success',
                confirmButtonText: 'Tutup',
            });
            location.reload();
        }
    });
}

function reupload_cancel(invoiceId) {
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    Swal.fire({
        title: 'Pilih File Baru',
        input: 'file',
        inputAttributes: {
            accept: '.pdf',
        },
        showCancelButton: true,
        confirmButtonText: 'Unggah',
        showLoaderOnConfirm: true,
        preConfirm: (file) => {
            const formData = new FormData();
            formData.append('file', file);

            return fetch(`/admin/invoice/reupload/cancel/${invoiceId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                },
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Upload gagal.');
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(
                    `Upload file gagal: ${error}`
                );
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Sukses!',
                text: 'File berhasil diunggah.',
                icon: 'success',
                confirmButtonText: 'Tutup',
            });
        }
    });
}

function view_cancel(invoiceId) {
    Swal.fire({
        title: 'Memuat...',
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil URL file invoice menggunakan AJAX
    $.ajax({
        url: `/admin/invoice/view/cancel/${invoiceId}`,
        method: 'GET',
        success: function(response) {
            const fileUrl = response.file_url;

            // Buka file di halaman baru
            window.open(fileUrl);

            Swal.close();
        },
        error: function(xhr, status, error) {
            Swal.fire({
                title: 'Terjadi Kesalahan',
                text: 'Terjadi kesalahan saat memuat file invoice.',
                icon: 'error',
                confirmButtonText: 'Tutup',
            });
        }
    });
}