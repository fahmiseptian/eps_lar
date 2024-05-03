var csrfToken = $('meta[name="csrf-token"]').attr("content");

$(function () {
    // Konfigurasi DataTables untuk kedua tabel
    var dataTableOptions = {
        bPaginate: true,
        bLengthChange: false,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
    };

    // Inisialisasi DataTables
    $("#example1").dataTable(dataTableOptions);
    $("#example2").dataTable(dataTableOptions);

    // Jika lebar jendela kurang dari atau sama dengan 800 piksel, atur lebar kolom pencarian
    if (window.innerWidth <= 800) {
        $(".dataTables_filter input").css({
            width: "110px",
        });
    }
});

function toggleFilterorder(element) {
    var status_order = element.getAttribute("data-status-order");

    $.ajax({
        type: "GET",
        url: appUrl + "/seller/order/filter/" + status_order,
        success: function (data) {
            console.log("berhasil ");
            window.location.href = "/seller/order/filter/" + status_order;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
    });
}

function viewDetail(element) {
    var id_cart_shop = element.getAttribute("data-id-order");

    $.ajax({
        type: "GET",
        url: appUrl + "/seller/order/detail/" + id_cart_shop,
        success: function (data) {
            console.log("berhasil");
            window.location.href = "/seller/order/detail/" + id_cart_shop;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
    });
}

function viewOrderDetail(invoice, tanggal, instansi, kota, nilai, qty) {
    Swal.fire({
        title: "Rincian Pesanan",
        html: `<b>Invoice:</b> ${invoice}<br>
               <b>Tanggal Pesan:</b> ${tanggal}<br>
               <b>Instansi:</b> ${instansi}<br>
               <b>Kota Tujuan:</b> ${kota}<br>
               <b>Nilai:</b> Rp. ${nilai}<br>
               <b>Qty:</b> ${qty}<br>`,
        showCloseButton: true,
        showCancelButton: false,
        focusConfirm: false,
        confirmButtonText: "Tutup",
    });
}

$(document).on("click", ".accept-this-order", function () {
    var id_cart_shop = $(this).data("id");
    Swal.fire({
        title: "Anda Yakin Menerima Pesanan?",
        text: "Pastikan Ketersediaan Barang Anda",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya",
    }).then((result) => {
        if (result.isConfirmed) {
            console.log(id_cart_shop);
            var csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
            $.ajax({
                url: appUrl + "/seller/order/accept",
                type: "POST",
                data: { id_cart_shop: id_cart_shop, _token: csrfToken },
                success: function () {
                    location.reload();
                },
            });
        }
    });
});

$(document).on("click", ".cancel-order", async function () {
    var id_cart_shop = $(this).data("id");
    console.log(id_cart_shop);
    const { value: noteSeller } = await Swal.fire({
        title: "Masukan Penyebab Pembatalan",
        input: "text",
        inputLabel: "Catatan",
        inputPlaceholder: "Masukan Penyebab Pembatalan",
        inputAttributes: {
            maxlength: "10",
            autocapitalize: "off",
            autocorrect: "off",
        },
    });
    if (noteSeller) {
        console.log(noteSeller);
        var csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content");
        $.ajax({
            url: appUrl + "/seller/order/cencel",
            type: "POST",
            data: {
                id_cart_shop: id_cart_shop,
                note: noteSeller,
                _token: csrfToken,
            },
            success: function () {
                location.reload();
            },
        });
    }
});

$(document).on("click", "#request_courier", function () {
    var id = $(this).data("id");
    var id_courier = $(this).data("id_courier");

    if (id_courier === 0) {
        // Tampilkan Swal.fire untuk mengisi nomor resi
        Swal.fire({
            title: "Masukkan Nomor Resi",
            input: "text",
            inputLabel: "Nomor Resi",
            inputPlaceholder: "Masukkan nomor resi di sini",
            showCancelButton: true,
            confirmButtonText: "Simpan",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                var nomorResi = result.value;

                // Kirim nomor resi ke controller menggunakan AJAX
                $.ajax({
                    url: appUrl + "/seller/order/addResi",
                    method: "POST",
                    data: {
                        id: id,
                        nomor_resi: nomorResi,
                        _token: csrfToken,
                    },
                    success: function (response) {
                        // Tangani respons sukses
                        console.log("Nomor resi berhasil disimpan:", response);
                        window.location.reload();
                    },
                    error: function (error) {
                        // Tangani respons error
                        console.error(
                            "Terjadi kesalahan saat menyimpan nomor resi:",
                            error
                        );
                    },
                });
            }
        });
    }
});

// Fitur copy resi
$(document).on("click", ".btn.btn-info.fa.fa-copy", function () {
    // Ambil data resi dari atribut data-resi
    var noResi = $(this).data("resi");
    // Salin data resi ke clipboard
    navigator.clipboard
        .writeText(noResi)
        .then(function () {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "Nomor resi berhasil disalin ke clipboard.",
                timer: 1500,
                showConfirmButton: false,
            });
        })
        .catch(function (error) {
            Swal.fire({
                icon: "error",
                title: "Gagal",
                text: "Terjadi kesalahan saat menyalin nomor resi.",
            });
            console.error(error);
        });
});

$(document).on("click", "#lacakResi", function () {
    var id_order_shop = $(this).data("id");
    $.ajax({
        url: appUrl + "/seller/order/test/" + id_order_shop,
        method: "get",
        // data: {
        //     id_cart_shop: id_order_shop,
        //     _token: csrfToken,
        // },
        success: function (response) {
            // Respons data diterima dalam bentuk JSON
            var status = response.ccs.status;
            var deliveryStart = response.ccs.delivery_start;
            var deliveryEnd = response.ccs.delivery_end;
            var fileDO = response.ccs.file_pdf_url;

            if (response.ccs.id_courier === 0 && response.ccs.file_do === "") {
                // Buat tabel HTML untuk kondisi pertama
                var tableHtml =
                    '<table class="table">' +
                    "<thead>" +
                    "<tr>" +
                    "<th>Track ID</th>" +
                    "<th>Deskripsi</th>" +
                    "<th>Date/Time</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>" +
                    "<tr>" +
                    '<td style="text-align: left;">' +
                    '<span class="fa fa-map-marker"> On Progress </span>' +
                    "</td>" +
                    '<td style="text-align: left;">' +
                    "Pesanan Dalam Pengiriman" +
                    "</td>" +
                    '<td style="text-align: left;">' +
                    deliveryStart +
                    "</td>" +
                    "</tr>" +
                    "</tbody>" +
                    "</table>";
            } else if (response.ccs.id_courier === 0 && response.ccs.file_do !== "") {
                // Buat tabel HTML untuk kondisi kedua
                var tableHtml =
                    '<table class="table">' +
                    "<thead>" +
                    "<tr>" +
                    "<th>Track ID</th>" +
                    "<th>Deskripsi</th>" +
                    "<th>Date/Time</th>" +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>" +
                    "<tr>" +
                    '<td style="text-align: left;">' +
                    '<span class="fa fa-map-marker"> On Progress </span>' +
                    "</td>" +
                    '<td style="text-align: left;">' +
                    "Pesanan Dalam Pengiriman" +
                    "</td>" +
                    '<td style="text-align: left;">' +
                    deliveryStart +
                    "</td>" +
                    "</tr>" +
                    "<tr>" +
                    '<td style="text-align: left;">' +
                    '<span class="fa fa-map-marker"> Complate <a target="_blank" href="' + fileDO + '">detail</a></span>' +
                    "</td>" +
                    '<td style="text-align: left;">' +
                    "Selesai" +
                    "</td>" +
                    '<td style="text-align: left;">' +
                    deliveryEnd +
                    "</td>" +
                    "</tr>" +
                    "</tbody>" +
                    "</table>";
            }
            
            Swal.fire({
                title: "Lacak Order",
                html: tableHtml,
                confirmButtonText: "OK",
                width: window.innerWidth <= 600 ? '100%' : '40%'
            });            
        },
        error: function (error) {
            console.error("Terjadi kesalahan:", error);
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Silakan coba lagi nanti.",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

$(document).on("click", "#uploadDO", function () {
    var id_order_shop = $(this).data("id");

    // Membuat dialog menggunakan SweetAlert2 untuk mengunggah file
    Swal.fire({
        title: "Unggah Dokumen",
        html: `
            <input type="file" id="fileInput" class="swal2-input" accept=".pdf,.doc,.docx,.jpg,.png,.jpeg" />
        `,
        showCancelButton: true,
        confirmButtonText: "Unggah",
        cancelButtonText: "Batal",
        preConfirm: () => {
            // Ambil file yang dipilih oleh pengguna
            const file = Swal.getPopup().querySelector("#fileInput").files[0];
            if (!file) {
                Swal.showValidationMessage(
                    "Silakan pilih file terlebih dahulu"
                );
            }
            return file;
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi dan memilih file
            var file = result.value;

            // Membuat form data untuk mengirim file
            var formData = new FormData();
            formData.append("file_Do", file);
            formData.append("id_cart_shop", id_order_shop);
            formData.append("_token", csrfToken);

            // Mengirim data menggunakan AJAX
            $.ajax({
                url: appUrl + "/seller/order/uploadDo",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire("Sukses", "File berhasil diunggah.", "success");
                    window.location.reload();
                },
                error: function (error) {
                    Swal.fire(
                        "Kesalahan",
                        "Terjadi kesalahan saat mengunggah file.",
                        "error"
                    );
                },
            });
        }
    });
});

$(document).on("click", "#cetakLabel", function(event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl = appUrl + "/seller/order/Resi/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

$(document).on("click", "#cetakInvoice", function(event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl = appUrl + "/seller/order/invoice/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

$(document).on("click", "#cetakkwantasi", function(event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl = appUrl + "/seller/order/kwantasi/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

$(document).on("click", "#cetakBast", function(event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl = appUrl + "/seller/order/bast/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});
