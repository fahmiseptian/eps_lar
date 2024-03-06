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
        url: "/admin/invoice/" + id,
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
                                    <div style="text-align: justify;">
                                        <p><strong>No Invoice:</strong> ${
                                            invoice.invoice || ""
                                        }</p>
                                        <p><strong>Pembeli:</strong> ${
                                            member.nama || ""
                                        }</p>
                                        <p><strong>Penjual:</strong> ${
                                            shop.name || ""
                                        }</p>
                                    </div>
                                `,
                    confirmButtonText: "Tutup",
                });
            } else {
                // Menampilkan pesan jika data anggota tidak ditemukan
                Swal.fire({
                    title: "Detail Pesanan",
                    text: "Data PEsanan tidak ditemukan.",
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
