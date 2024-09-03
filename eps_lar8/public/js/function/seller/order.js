var csrfToken = $('meta[name="csrf-token"]').attr("content");
const imgDetail = appUrl + "/img/app/detail.svg";

$(function () {
    // Konfigurasi DataTables untuk kedua tabel
    var dataTableOptions = {
        bPaginate: true,
        bLengthChange: false,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
        language: {
            emptyTable: "Belum ada Data", // Pesan untuk tabel kosong
        },
    };

    var dataTableOptions2 = {
        bPaginate: true,
        bLengthChange: false,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
        order: [[0, "dsc"]],
        language: {
            emptyTable: "Belum ada Data", // Pesan untuk tabel kosong
        },
    };
    $("#example2").DataTable(dataTableOptions2);
    $("#example2_filter input").attr("placeholder", "Pencarian");

    // Inisialisasi DataTables
    $("#example1").dataTable(dataTableOptions);

    // Jika lebar jendela kurang dari atau sama dengan 800 piksel, atur lebar kolom pencarian
    if (window.innerWidth <= 800) {
        $(".dataTables_filter input").css({
            width: "110px",
        });
    }
});

function formatRupiah(angka) {
    var number_string = angka.toString().replace(/[^,\d]/g, "");
    var split = number_string.split(",");
    var sisa = split[0].length % 3;
    var rupiah = split[0].substr(0, sisa);
    var ribuan = split[0].substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        var separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    return "Rp. " + rupiah;
}

function unformatRupiah(formattedRupiah) {
    var number_string = formattedRupiah.replace(/[^,\d]/g, "");
    return parseInt(number_string.replace(/[.,]/g, ""));
}

function formatTanggalIndo(createdDate) {
    // Array nama bulan dalam bahasa Indonesia
    const bulanIndo = [
        "Januari",
        "Februari",
        "Maret",
        "April",
        "Mei",
        "Juni",
        "Juli",
        "Agustus",
        "September",
        "Oktober",
        "November",
        "Desember",
    ];

    // Mengubah string created_date menjadi objek Date
    const date = new Date(createdDate);

    // Mengambil tanggal, bulan, dan tahun
    const tanggal = date.getDate();
    const bulan = bulanIndo[date.getMonth()]; // getMonth() mengembalikan indeks (0-11)
    const tahun = date.getFullYear();

    // Menggabungkan hasil dalam format yang diinginkan
    return `${tanggal} ${bulan} ${tahun}`;
}

$(document).ready(function () {
    var allItems = $(".item-box-filter-pesanan");
    var activeItem;

    function loadData(tipe) {
        $("#overlay").show();
        $.ajax({
            type: "GET",
            url: appUrl + "/seller/order/filter/" + tipe,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                var body = $("#table-content");
                body.empty();
                html = `
                    <div id="table-content" >
                        <table id="example2" class="table"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Invoice</th>
                                    <th >Tanggal Pesan</th>
                                    <th >Instansi</th>
                                    <th >Kota Tujuan</th>
                                    <th >Nilai</th>
                                    <th >Qty</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot hidden>
                                <tr>
                                    <th>Invoice</th>
                                    <th >Tanggal Pesan</th>
                                    <th >Instansi</th>
                                    <th >Kota Tujuan</th>
                                    <th >Nilai</th>
                                    <th >Qty</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                `;

                body.append(html);
                if ($.fn.dataTable.isDataTable("#example2")) {
                    $("#example2").DataTable().destroy();
                    var tbody = $("#example2 tbody");
                    tbody.empty();
                }

                var table = $("#example2").DataTable({
                    bPaginate: true,
                    bLengthChange: true,
                    bFilter: true,
                    bSort: true,
                    bInfo: true,
                    bAutoWidth: true,
                    order: [[0, "dsc"]],
                    language: {
                        emptyTable: "Belum ada Data",
                        zeroRecords: "Tidak ada catatan yang cocok ditemukan",
                        search: "",
                        sLengthMenu: "_MENU_ ",
                    },
                });
                $("#example2_filter input").attr("placeholder", "Pencarian");

                if (!response || response.length === 0) {
                    table.draw(); // Update tabel untuk menunjukkan pesan kosong
                } else {
                    // Tambahkan baris baru berdasarkan data yang diterima
                    var rows = response.map((order) => {
                        // Menentukan badge berdasarkan status
                        let statusBadge;
                        if (order.status === "send_by_seller") {
                            statusBadge =
                                '<span style="width:100%; background-color:#DEDEDE; color:black;" class="badge">Perlu Dikirim</span>';
                        } else if (
                            order.status === "complete" &&
                            order.status_pembayaran_top == 1
                        ) {
                            statusBadge =
                                '<span style="width:100%; background-color:#F9AC4D; color:white;" class="badge">Pesanan Selesai</span>';
                        } else if (order.status === "complete") {
                            statusBadge =
                                '<span style="width:100%; background-color:#00CA14; color:white;" class="badge">Barang Diterima</span>';
                        } else if (order.status === "waiting_accept_order") {
                            statusBadge =
                                '<span style="width:100%; background-color:#DEDEDE; color:black;" class="badge">Pesanan Baru</span>';
                        } else if (order.status === "on_packing_process") {
                            statusBadge =
                                '<span style="width:100%; background-color:#DEDEDE; color:black;" class="badge">Perlu Dikemas</span>';
                        } else {
                            statusBadge =
                                '<span style="width:100%; background-color:#FF5C5C; color:white;" class="badge">Pesanan Dibatalkan</span>';
                        }

                        return [
                            order.invoice + "-" + order.id,
                            order.created_date,
                            order.member_instansi,
                            order.city,
                            formatRupiah(order.total),
                            order.qty,
                            statusBadge, // Menambahkan badge status ke dalam tabel
                            `
                                <td>
                                    <p data-id-order="${order.id}" class="text-shadow detail-order">
                                        <img style="width: 25px;" src="${imgDetail}" alt="Detail Order"> Lihat
                                    </p>
                                </td>
                            `,
                        ];
                    });

                    table.rows.add(rows).draw(); // Tambahkan data dan perbarui tabel
                }
                $("#example2_filter input")
                    .attr("placeholder", "Pencarian")
                    .css({
                        color: "#999",
                        "font-style": "italic",
                    });
                $("#example2_filter input").attr(
                    "style",
                    "border-radius: 23px",
                    "box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2)",
                    "border: 1px solid #ddd",
                    "padding: 5px 10px",
                    "font-size: 14px",
                    "background-color: #F1F1F1;"
                );
                EventTambahan();
            },
            error: function (xhr, status, error) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function () {
                $("#overlay").hide(); // Sembunyikan loader setelah selesai
            },
        });
    }
    $(document).on("click", allItems.filter(".active"), function () {
        setupEvents();
    });

    function setupEvents() {
        // Handle click on the active item
        allItems
            .filter(".active")
            .off("click")
            .on("click", function () {
                allItems.slideDown();
            });

        allItems
            .not(".active")
            .off("click")
            .on("click", function () {
                var tipe = $(this).data("tipe");
                loadData(tipe);
                console.log(tipe);
                allItems.removeClass("active open").hide();
                $(this).addClass("open");
                $(this).addClass("active");
                activeItem = $(this);

                allItems.slideUp();
                activeItem.slideDown();
            });
    }

    function initialize() {
        // Initial setup
        allItems.hide();
        activeItem = allItems.first();
        activeItem.show().addClass("active");

        // Load initial data
        loadData("semua");

        // Setup event handlers
        setupEvents();
    }

    // Run initialization
    initialize();
});

function toggleFilterorder(element) {
    var status_order = element.getAttribute("data-status-order");
    $("#overlay").show();
    $.ajax({
        type: "GET",
        url: appUrl + "/seller/order/filter/" + status_order,
        xhrFields: {
            withCredentials: true,
        },
        success: function (data) {
            window.location.href =
                appUrl + "/seller/order/filter/" + status_order;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function EventTambahan() {
    $(document).on("click", ".detail-order", function () {
        var id_cart_shop = $(this).attr("data-id-order");
        AjaxDetailCart(id_cart_shop);
    });
}

function AjaxDetailCart(id) {
    $("#overlay").show();
    $.ajax({
        type: "GET",
        url: appUrl + "/api/seller/detail/" + id,
        xhrFields: {
            withCredentials: true,
        },
        success: function (data) {
            var body = $("#table-content");
            body.empty();

            var view = V_detailOrder(data);
            body.append(view);
            V_produk_transaksi(data.produk);
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function viewDetail(element) {
    var id_cart_shop = element.getAttribute("data-id-order");
    $("#overlay").show();
    $.ajax({
        type: "GET",
        url: appUrl + "/seller/order/detail/" + id_cart_shop,
        xhrFields: {
            withCredentials: true,
        },
        success: function (data) {
            window.location.href =
                appUrl + "/seller/order/detail/" + id_cart_shop;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
        complete: function () {
            $("#overlay").hide();
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
            $("#overlay").show();
            $.ajax({
                url: appUrl + "/seller/order/accept",
                type: "POST",
                data: { id_cart_shop: id_cart_shop, _token: csrfToken },
                xhrFields: {
                    withCredentials: true,
                },
                success: function () {
                    AjaxDetailCart(id_cart_shop);
                },
                error: function (xhr, status, error) {
                    console.error("Gagal:", error);
                },
                complete: function () {
                    $("#overlay").hide();
                },
            });
        }
    });
});

$(document).on("click", ".cancel-order", async function () {
    var id_cart_shop = $(this).data("id");
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
        $("#overlay").show();
        $.ajax({
            url: appUrl + "/seller/order/cencel",
            type: "POST",
            data: {
                id_cart_shop: id_cart_shop,
                note: noteSeller,
                _token: csrfToken,
            },
            xhrFields: {
                withCredentials: true,
            },
            success: function () {
                AjaxDetailCart(id_cart_shop);
            },
            error: function (xhr, status, error) {
                console.error("Gagal:", error);
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    }
});

$(document).on("click", "#openKontrak", function () {
    var id_cart_shop = $(this).data("id");
    var body = $(".detail-transaksi");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getKontrak",
        type: "post",
        data: {
            idcs: id_cart_shop,
            _token: csrfToken,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var newBody = `
                <div class="aksi-invoice">
                    <div class="box-body">
                        <table id="tableKontak" class="table " style="width: 100%">
                            <thead>
                                <tr>
                                    <th>No Kontrak</th>
                                    <th>Tanggal Kontrak</th>
                                    <th >Catatan</th>
                                    <th >Tanggal Buat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${
                                    response === 0
                                        ? `
                                <tr>
                                    <td colspan="5" style="text-align:center;">
                                        <a class="btn btn-info fa fa-pencil-square-o" id="CreateKontrak" style="width:100%;" data-id="${id_cart_shop}">
                                            Buat Kontrak
                                        </a>
                                    </td>
                                </tr>
                                `
                                        : `
                                <tr>
                                    <td>${response.no_kontrak}</td>
                                    <td>${response.tanggal_kontrak}</td>
                                    <td>${
                                        response.catatan !== null
                                            ? response.catatan
                                            : "-"
                                    }</td>
                                    <td>${response.created_date}</td>
                                    <td>
                                        <div style="display: flex">
                                            <button id="edit-contract-button" class="material-icons" style="width: 50px; background-color: rgb(53, 152, 219); color: white;" data-id="${id_cart_shop}">edit_note</button>
                                            &nbsp;
                                            <button id="download-contract-button" class="material-icons" style="width: 50px; background-color: rgb(224, 62, 45); color: white;" data-id="${id_cart_shop}">download</button>
                                        </div>
                                    </td>
                                </tr>
                                `
                                }
                            </tbody>
                            <tfoot hidden>
                                <tr>
                                    <th>No Kontrak</th>
                                    <th>Tanggal Kontrak</th>
                                    <th >Catatan</th>
                                    <th >Tanggal Buat</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;
            body.append(newBody);
            $("#CreateKontrak").on("click", function () {
                var id_cart_shop = $(this).data("id");
                CreateKontrak(id_cart_shop);
            });
            $("#edit-contract-button").on("click", function () {
                var id_cart_shop = $(this).data("id");
                editKontrak(id_cart_shop);
            });
            $("#download-contract-button").on("click", function () {
                var id_cart_shop = $(this).data("id");
                downloadKontrak(id_cart_shop);
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

// Surat Pesanan
$(document).on("click", "#SuratPesanan", function () {
    var id_cart_shop = $(this).data("id");
    var body = $(".detail-transaksi");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getSuratPesanan",
        type: "post",
        data: {
            idcs: id_cart_shop,
            _token: csrfToken,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var newBody = `
                <div class="aksi-invoice">
                    <div class="box-body">
                        <table id="tableKontak" class="table " style="width: 100%">
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
                                ${
                                    response === 0
                                        ? `
                                <tr>
                                    <td colspan="5" style="text-align:center;">
                                        <a class="btn btn-info fa fa-pencil-square-o" id="CreateSP" style="width:100%;" data-id="${id_cart_shop}">
                                            Buat Surat Pesanan
                                        </a>
                                    </td>
                                </tr>
                                `
                                        : `
                                <tr>
                                    <td>${response.invoice}</td>
                                    <td>${response.tanggal_pesan}</td>
                                    <td>${
                                        response.catatan !== null
                                            ? response.catatan
                                            : "-"
                                    }</td>
                                    <td>${response.created_at}</td>
                                    <td>
                                        <div style="display: flex">
                                            <button id="edit-sp-button" class="material-icons" style="width: 50px; background-color: rgb(53, 152, 219); color: white;" data-id="${id_cart_shop}">edit_note</button>
                                            &nbsp;
                                            <button id="download-sp-button" class="material-icons" style="width: 50px; background-color: rgb(224, 62, 45); color: white;" data-id="${id_cart_shop}">download</button>
                                        </div>
                                    </td>
                                </tr>
                                `
                                }
                            </tbody>
                            <tfoot hidden>
                                <tr>
                                    <th>No Kontrak</th>
                                    <th>Tanggal Kontrak</th>
                                    <th >Catatan</th>
                                    <th >Tanggal Buat</th>
                                    <th>Aksi</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            `;
            body.append(newBody);
            $("#CreateSP").on("click", function () {
                var id_cart_shop = $(this).data("id");
                CreateSP(id_cart_shop);
            });
            $("#edit-sp-button").on("click", function () {
                var id_cart_shop = $(this).data("id");
                editSP(id_cart_shop);
            });
            $("#download-sp-button").on("click", function () {
                var id_cart_shop = $(this).data("id");
                downloadSp(id_cart_shop);
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

function CreateKontrak(id_cart_shop) {
    var body = $(".detail-transaksi");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getOrder/" + id_cart_shop,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var order = response.order;
            var dataArr = {
                noKontrak: "",
                id_cart_shop: id_cart_shop || "",
                total: order.total || 0,
                nilaiKontrak: order.total || 0,
                tanggal_kontrak: "",
                catatan: "",
                content: response.htmlContent,
            };
            body.append(FormKotrak(dataArr));
            tinymce.init({
                selector: "textarea#document",
                height: 700,
                plugins: "autoresize",
                toolbar_mode: "floating",
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function CreateSP(id_cart_shop) {
    var body = $(".detail-transaksi");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getSP/" + id_cart_shop,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var order = response.order;
            var dataArr = {
                invoice: order.invoice + "-" + id_cart_shop,
                id_cart_shop: id_cart_shop || "",
                tanggal: "",
                catatan: "",
                content: response.htmlContent,
            };
            body.append(FormSP(dataArr));
            tinymce.init({
                selector: "textarea#document",
                height: 700,
                plugins: "autoresize",
                toolbar_mode: "floating",
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function editKontrak(id_cart_shop) {
    var body = $(".detail-transaksi");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getKontrak",
        type: "post",
        data: {
            idcs: id_cart_shop,
            _token: csrfToken,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var dataArr = {
                noKontrak: response.no_kontrak,
                id_cart_shop: id_cart_shop,
                total: response.total_harga,
                nilaiKontrak: response.nilai_kontrak,
                tanggal_kontrak: response.tanggal_kontrak,
                catatan: response.catatan,
                content: response.document,
            };
            body.append(FormKotrak(dataArr));
            tinymce.init({
                selector: "textarea#document",
                height: 700,
                plugins: "autoresize",
                toolbar_mode: "floating",
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function editSP (id_cart_shop) {
    var body = $(".detail-transaksi");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getSuratPesanan",
        type: "post",
        data: {
            idcs: id_cart_shop,
            _token: csrfToken,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var dataArr = {
                invoice: response.invoice,
                id_cart_shop: id_cart_shop,
                tanggal: response.tanggal_pesan,
                catatan: response.catatan,
                content: response.document,
            };
            body.append(FormSP(dataArr));
            tinymce.init({
                selector: "textarea#document",
                height: 700,
                plugins: "autoresize",
                toolbar_mode: "floating",
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function downloadKontrak(id_cart_shop) {
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/download-kontrak",
        type: "post",
        data: {
            idcs: id_cart_shop,
            _token: csrfToken,
        },
        xhrFields: {
            responseType: "blob", // Ubah ini menjadi 'blob'
            withCredentials: true,
        },
        success: function (response, status, xhr) {
            var blob = new Blob([response], { type: "application/pdf" });
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = "kontrak_" + Date.now() + ".pdf"; // Nama file yang diunduh
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function downloadSp(id_cart_shop) {
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/download-sp",
        type: "post",
        data: {
            idcs: id_cart_shop,
            _token: csrfToken,
        },
        xhrFields: {
            responseType: "blob", // Ubah ini menjadi 'blob'
            withCredentials: true,
        },
        success: function (response, status, xhr) {
            var blob = new Blob([response], { type: "application/pdf" });
            var link = document.createElement("a");
            link.href = window.URL.createObjectURL(blob);
            link.download = "kontrak_" + Date.now() + ".pdf"; // Nama file yang diunduh
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function FormKotrak(dataArr) {
    var formulir = `
        <div class="detail-pengiriman">
            <div class="box-body">
                <form action="{{ route('generate.kontrak') }}" method="POST">
                    <table style="width:100%">
                        <tr>
                            <td colspan="2">
                                <div class="form-group">
                                    <p style="font-size: 14px">No Kontrak</p>
                                    <input type="text" class="form-control" value="${
                                        dataArr.noKontrak
                                    }" id="noKontrak" name="noKontrak" required>
                                    <input type="hidden" class="form-control" value="${
                                        dataArr.id_cart_shop
                                    }" id="id_cs" name="id_cs">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Total Harga</p>
                                    <input type="text" class="form-control" id="totalHarga" name="totalHarga" readonly required value="${formatRupiah(
                                        dataArr.total
                                    )}">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Nilai Kontrak</p>
                                    <input type="text" class="form-control" id="nilaiKontrak" name="nilaiKontrak" value="${
                                        dataArr.nilaiKontrak
                                    }" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Tanggal Kontrak</p>
                                    <input type="date" class="form-control" id="tanggalKontrak" name="tanggalKontrak" value="${
                                        dataArr.tanggal_kontrak
                                    }" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Catatan</p>
                                    <input type="text" class="form-control" id="catatan" name="catatan" value="${
                                        dataArr.catatan !== null
                                            ? dataArr.catatan
                                            : ""
                                    }" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b> *note : Mohon dirubah pada bagian text yang berwarna: </b>
                                <p style="color: blue;">-Seller : Biru  </p>
                                <p style="color: red;">-Pembeli : Merah </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="document" name="document" style="height: 700px; width:100%">
                                    ${dataArr.content}
                                </textarea>
                            </td>
                        </tr>
                    </table>
                    <button type="button" onclick="generatePDF()" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    `;

    tinymce.init({
        selector: "textarea#document",
        height: 700,
        plugins: "autoresize",
        toolbar_mode: "floating",
    });

    return formulir;
}

function FormSP(dataArr) {
    var formulir = `
        <div class="detail-pengiriman">
            <div class="box-body">
                <form method="POST">
                    <table style="width:100%">
                        <tr>
                            <td colspan="2">
                                <div class="form-group">
                                    <p style="font-size: 14px">No Invoice</p>
                                    <input type="text" class="form-control" value="${
                                        dataArr.invoice
                                    }" id="invoice" name="invoice" required>
                                    <input type="hidden" class="form-control" value="${
                                        dataArr.id_cart_shop
                                    }" id="id_cs" name="id_cs">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Tanggal Kontrak</p>
                                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="${
                                        dataArr.tanggal
                                    }" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Catatan</p>
                                    <input type="text" class="form-control" id="catatan" name="catatan" value="${
                                        dataArr.catatan ? dataArr.catatan : ""
                                    }" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b> *note : Mohon dirubah pada bagian text yang berwarna: </b>
                                <p style="color: blue;">-Seller : Biru  </p>
                                <p style="color: red;">-Pembeli : Merah </p>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <textarea id="document" name="document" style="height: 700px; width:100%">
                                    ${dataArr.content}
                                </textarea>
                            </td>
                        </tr>
                    </table>
                    <button type="button" onclick="generateSP()" class="btn btn-primary">Kirim</button>
                </form>
            </div>
        </div>
    `;

    tinymce.init({
        selector: "textarea#document",
        height: 700,
        plugins: "autoresize",
        toolbar_mode: "floating",
    });

    return formulir;
}

function generatePDF() {
    var noKontrak = document.getElementById("noKontrak").value;
    var id_cs = document.getElementById("id_cs").value;
    var catatan = document.getElementById("catatan").value;
    var tanggalKontrak = document.getElementById("tanggalKontrak").value;
    var totalHarga = document.getElementById("totalHarga").value;
    var nilaiKontrak = document.getElementById("nilaiKontrak").value;
    var content = document.getElementById("document").value;

    // Validasi input kosong
    if (
        noKontrak === "" ||
        tanggalKontrak === "" ||
        totalHarga === "" ||
        nilaiKontrak === "" ||
        content === ""
    ) {
        Swal.fire("Error", "Harap lengkapi semua kolom", "error");
        return;
    }

    $("#overlay").show();
    var formData = {
        id_cs: id_cs,
        no_kontrak: noKontrak,
        catatan: catatan,
        tanggal_kontrak: tanggalKontrak,
        total_harga: unformatRupiah(totalHarga),
        nilai_kontrak: nilaiKontrak,
        content: content,
        _token: csrfToken,
    };

    $.ajax({
        url: appUrl + "/api/generate-kontrak",
        type: "post",
        data: formData,
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            if (response.success) {
                Swal.fire(
                    "Success",
                    "Kontrak Berhasil disimpan",
                    "success"
                ).then(() => {
                    AjaxDetailCart(id_cs);
                });
            } else {
                Swal.fire("Error", "Gagal Menyimpan Kontrak", "error");
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function generateSP() {
    var invoice = document.getElementById("invoice").value;
    var id_cs = document.getElementById("id_cs").value;
    var catatan = document.getElementById("catatan").value;
    var tanggal = document.getElementById("tanggal").value;
    var content = document.getElementById("document").value;

    // Validasi input kosong
    if (invoice === "" || tanggal === "") {
        Swal.fire("Error", "Harap lengkapi semua kolom", "error");
        return;
    }

    $("#overlay").show();
    var formData = {
        id_cs: id_cs,
        invoice: invoice,
        catatan: catatan,
        tanggal: tanggal,
        content: content,
        _token: csrfToken,
    };

    $.ajax({
        url: appUrl + "/api/generate-sp",
        type: "post",
        data: formData,
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            if (response.success) {
                Swal.fire(
                    "Success",
                    "Surat Pesanan Berhasil disimpan",
                    "success"
                ).then(() => {
                    AjaxDetailCart(id_cs);
                });
            } else {
                Swal.fire("Error", "Gagal Menyimpan Surat Pesanan", "error");
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

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
                $("#overlay").show();
                $.ajax({
                    url: appUrl + "/seller/order/addResi",
                    method: "POST",
                    data: {
                        id: id,
                        nomor_resi: nomorResi,
                        _token: csrfToken,
                    },
                    xhrFields: {
                        withCredentials: true,
                    },
                    success: function (response) {
                        AjaxDetailCart(id);
                    },
                    error: function (error) {
                        // Tangani respons error
                        console.error(
                            "Terjadi kesalahan saat menyimpan nomor resi:",
                            error
                        );
                    },
                    complete: function () {
                        $("#overlay").hide();
                    },
                });
            }
        });
    } else {
        var data;
        if (id_courier == "1") {
            data = { Anter: "Anter ke agent terdekat" };
            Swal.fire({
                title: "Silakan pilih metode pengiriman yang ingin dilakukan",
                input: "select",
                inputOptions: data,
                inputPlaceholder: "Pilih metode pengiriman",
                showCancelButton: true,
                confirmButtonText: "Simpan",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedMethod = result.value;
                    if (selectedMethod === "Anter") {
                        $.ajax({
                            url: appUrl + "/api/kurir/anter",
                            type: "POST",
                            data: {
                                id_order_shop: id,
                                id_courier: id_courier,
                                _token: csrfToken,
                            },
                            xhrFields: {
                                withCredentials: true,
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Kesalahan Server!",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                });
                            },
                            success: function (data) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Silakan antar pesanan ini ke agen terdekat!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        AjaxDetailCart(id);
                                    }
                                });
                            },
                            complete: function () {
                                $("#overlay").hide();
                            },
                        });
                    }
                }
            });
        } else if (id_courier == "6") {
            console.log(id_courier);
            data = { Pickup: "Pickup Order" };
            Swal.fire({
                title: "Silakan pilih metode pengiriman yang ingin dilakukan",
                input: "select",
                inputOptions: data,
                inputPlaceholder: "Pilih metode pengiriman",
                showCancelButton: true,
                confirmButtonText: "Simpan",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedMethod = result.value;
                    if (selectedMethod === "Pickup") {
                        $.ajax({
                            url: appUrl + "/api/kurir/pickup",
                            type: "POST",
                            data: {
                                id_order_shop: id,
                                id_courier: id_courier,
                                _token: csrfToken,
                            },
                            xhrFields: {
                                withCredentials: true,
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Kesalahan Server!",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                });
                            },
                            success: function (data) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Anda berhasil merequest pickup pesanan ini!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        AjaxDetailCart(id);
                                    }
                                });
                            },
                            complete: function () {
                                $("#overlay").hide();
                            },
                        });
                    }
                }
            });
        } else if (id_courier == "4") {
            data = { Pickup: "Pickup Order", Anter: "Anter ke agent terdekat" };

            Swal.fire({
                title: "Silakan pilih metode pengiriman yang ingin dilakukan",
                input: "select",
                inputOptions: data,
                inputPlaceholder: "Pilih metode pengiriman",
                showCancelButton: true,
                confirmButtonText: "Simpan",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    var selectedMethod = result.value;
                    if (selectedMethod === "Anter") {
                        $.ajax({
                            url: appUrl + "/api/kurir/anter",
                            type: "POST",
                            data: {
                                id_order_shop: id,
                                id_courier: id_courier,
                                _token: csrfToken,
                            },
                            xhrFields: {
                                withCredentials: true,
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Kesalahan Server!",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                });
                            },
                            success: function (data) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Silakan antar pesanan ini ke agen terdekat!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        AjaxDetailCart(id);
                                    }
                                });
                            },
                            complete: function () {
                                $("#overlay").hide();
                            },
                        });
                    } else if (selectedMethod === "Pickup") {
                        $.ajax({
                            url: appUrl + "/api/kurir/pickup",
                            type: "POST",
                            data: {
                                id_order_shop: id,
                                id_courier: id_courier,
                                _token: csrfToken,
                            },
                            xhrFields: {
                                withCredentials: true,
                            },
                            error: function () {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Kesalahan Server!",
                                    icon: "error",
                                    confirmButtonText: "OK",
                                });
                            },
                            success: function (data) {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Anda berhasil merequest pickup pesanan ini!",
                                    icon: "success",
                                    confirmButtonText: "OK",
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        AjaxDetailCart(id);
                                    }
                                });
                            },
                            complete: function () {
                                $("#overlay").hide();
                            },
                        });
                    }
                }
            });
        }
    }
});

// Upload Faktur
$(document).on("click", "#UploadFaktur", function () {
    var id_order_shop = $(this).data("id");

    Swal.fire({
        title: "Upload Faktur",
        html: `
            <input type="file" id="fakturFile" class="swal2-file" accept="application/pdf,image/*">
        `,
        showCancelButton: true,
        confirmButtonText: "Upload",
        preConfirm: () => {
            const file = Swal.getPopup().querySelector("#fakturFile").files[0];
            if (!file) {
                Swal.showValidationMessage("Please select a file");
            }
            return { file: file };
        },
    }).then((result) => {
        if (result.isConfirmed) {
            var formData = new FormData();
            formData.append("faktur", result.value.file);
            formData.append("id_order_shop", id_order_shop);

            $.ajax({
                url: "/api/seller/order/upload-faktur",
                method: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire(
                        "Success",
                        "File uploaded successfully!",
                        "success"
                    );
                    AjaxDetailCart(id_order_shop);
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        "Error",
                        "There was an error uploading the file.",
                        "error"
                    );
                },
            });
        }
    });
});

// Fitur copy resi
$(document).on("click", ".fa.fa-copy", function () {
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
    $("#overlay").show();
    var id_order_shop = $(this).data("id");
    var id_courier = $(this).data("id_courier");
    var resi = $(this).data("resi");

    if (id_courier === 0) {
        $.ajax({
            url: appUrl + "/seller/order/lacak_kurir_sendiri/" + id_order_shop,
            method: "GET",
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                var status = response.ccs.status;
                var deliveryStart = response.ccs.delivery_start;
                var deliveryEnd = response.ccs.delivery_end;
                var fileDO = response.ccs.file_pdf_url;

                var tableHtml = generateTableHtml(
                    response.ccs.id_courier,
                    response.ccs.file_do,
                    deliveryStart,
                    deliveryEnd,
                    fileDO
                );

                Swal.fire({
                    title: "Lacak Order",
                    html: tableHtml,
                    confirmButtonText: "OK",
                    width: window.innerWidth <= 600 ? "100%" : "40%",
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
            complete: function () {
                $("#overlay").hide(); // Sembunyikan loader setelah selesai
            },
        });
    } else {
        $.ajax({
            url: appUrl + "/api/kurir/tracking",
            method: "post",
            data: {
                id_courier: id_courier,
                resi: resi,
                _token: csrfToken,
            },
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                console.log(response); // Log the entire response to inspect its structure
                var tableTrack = "";

                if (typeof response === "string") {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error("Error parsing JSON response", e);
                        Swal.fire({
                            title: "Terjadi Kesalahan",
                            text: "Respons tidak valid.",
                            icon: "error",
                            confirmButtonText: "OK",
                        });
                        return;
                    }
                }

                // Check if response contains the expected properties
                if (id_courier == 1 && response.history) {
                    tableTrack = `
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Track ID</th>
                                    <th>Deskripsi</th>
                                    <th>Date/Time</th>
                                </tr>
                            </thead>
                            <tbody>`;

                    response.history.forEach(function (history) {
                        tableTrack += `
                            <tr>
                                <td>${history.code}</td>
                                <td>${history.desc}</td>
                                <td>${history.date}</td>
                            </tr>`;
                    });

                    tableTrack += `
                            </tbody>
                        </table>`;
                } else if (
                    id_courier == 4 &&
                    response.RPX &&
                    response.RPX.DATA &&
                    response.RPX.DATA.length > 0
                ) {
                    tableTrack = `
                        <table class="table">
                            <thead>
                            </thead>
                            <tbody>`;

                    // Mengisi tabel dengan data tracking dari respons
                    response.RPX.DATA.forEach(function (data) {
                        var dateTime =
                            data.TRACKING_DATE + " " + data.TRACKING_TIME;
                        tableTrack += `
                            <tr>
                                <td>${data.TRACKING_ID}</td>
                                <td>${data.TRACKING_DESC}</td>
                                <td>${dateTime}</td>
                            </tr>`;
                    });

                    tableTrack += `
                            </tbody>
                        </table>`;
                } else if (id_courier == 6) {
                    tableTrack = `
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Track ID</th>
                                    <th>Deskripsi</th>
                                    <th>Date/Time</th>
                                </tr>
                            </thead>
                            <tbody>`;

                    response.forEach(function (history) {
                        var trackID = history.tracking_id;
                        var description = history.description
                            ? history.description
                            : "";
                        var dateTime = history.create_date
                            ? new Date(history.create_date).toLocaleString()
                            : "";

                        tableTrack += `
                            <tr>
                                <td>${trackID}</td>
                                <td>${description}</td>
                                <td>${dateTime}</td>
                            </tr>`;
                    });

                    tableTrack += `
                            </tbody>
                        </table>`;
                } else {
                    tableTrack = `<p>Data tracking tidak tersedia.</p>`;
                }

                Swal.fire({
                    title: "Lacak Order",
                    html: tableTrack,
                    confirmButtonText: "OK",
                    width: window.innerWidth <= 600 ? "100%" : "60%",
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
            complete: function () {
                $("#overlay").hide(); // Sembunyikan loader setelah selesai
            },
        });
    }
});

function generateTableHtml(
    idCourier,
    fileDO,
    deliveryStart,
    deliveryEnd,
    filePdfUrl
) {
    let tableRows = "";

    if (idCourier === 0) {
        tableRows += `
            <tr>
                <td style="text-align: left;"><span class="fa fa-map-marker"> On Progress </span></td>
                <td style="text-align: left;">Pesanan Dalam Pengiriman</td>
                <td style="text-align: left;">${deliveryStart}</td>
            </tr>
        `;

        if (deliveryEnd !== null) {
            tableRows += `
                <tr>
                    <td style="text-align: left;"><span class="fa fa-map-marker"> Complete <a target="_blank" href="${fileDO}">detail</a></span></td>
                    <td style="text-align: left;">Selesai</td>
                    <td style="text-align: left;">${deliveryEnd}</td>
                </tr>
            `;
        }
    }

    return `
        <table class="table">
            <thead>
                <tr>
                    <th>Track ID</th>
                    <th>Deskripsi</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
                ${tableRows}
            </tbody>
        </table>
    `;
}

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
            $("#overlay").show();
            // Mengirim data menggunakan AJAX
            $.ajax({
                url: appUrl + "/seller/order/uploadDo",
                method: "POST",
                data: formData,
                xhrFields: {
                    withCredentials: true,
                },
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire("Sukses", "File berhasil diunggah.", "success");
                    AjaxDetailCart(id_order_shop);
                },
                error: function (error) {
                    Swal.fire(
                        "Kesalahan",
                        "Terjadi kesalahan saat mengunggah file.",
                        "error"
                    );
                },
                complete: function () {
                    $("#overlay").hide();
                },
            });
        }
    });
});

$(document).on("click", "#cetakLabel", function (event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl =
        appUrl + "/seller/order/Resi/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

$(document).on("click", "#cetakInvoice", function (event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl =
        appUrl + "/seller/order/invoice/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

$(document).on("click", "#cetakkwantasi", function (event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl =
        appUrl + "/seller/order/kwantasi/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

$(document).on("click", "#cetakBast", function (event) {
    event.preventDefault(); // Mencegah tautan default untuk membuka halaman
    var id_cart_shop = $(this).data("id");
    var targetUrl =
        appUrl + "/seller/order/bast/" + encodeURIComponent(id_cart_shop);
    window.open(targetUrl, "_blank");
});

function V_detailOrder(data) {
    view = "";

    var detailorder = data.detailOrder;
    var detailProduct = data.produk;

    // Perhitungan
    ppn_insurance = (detailorder.val_ppn / 100) * detailorder.insurance_nominal;

    biaya_pengiriman = detailorder.sum_shipping + detailorder.ppn_shipping;
    asuransi_pengiriman = detailorder.insurance_nominal + ppn_insurance;
    total =
        detailorder.sum_price +
        (detailorder.sum_shipping + detailorder.ppn_shipping) +
        (detailorder.insurance_nominal + ppn_insurance);

    subtotal_nonPPn = detailorder.sum_price_non_ppn;
    ongir_nonPPn = detailorder.sum_shipping;
    asuransi_pengiriman_nonPPn = detailorder.insurance_nominal;
    total_PPn = detailorder.ppn_total;
    total_discount = detailorder.sum_discount;

    proforma = detailorder.status_pembayaran_top == 0 ? true : false;
    invoice = detailorder.invoice + "-" + detailorder.id;
    tanggal_pesanan = formatTanggalIndo(detailorder.created_date);
    batas_penerimaan_pesanan = formatTanggalIndo(detailorder.work_limit);
    pmk = detailorder.pmk;
    pembayaran = detailorder.status_pembayaran_top == 1 ? true : false;
    status_toko = detailorder.status;
    notif = detailorder.status == "waiting_accept_order" ? true : false;
    no_resi = detailorder.no_resi ? detailorder.no_resi : "";
    toko_top = detailorder.is_top == 1 ? true : false;
    user = detailorder.nama;
    nomor_telp = detailorder.phone;
    nama_alamat = detailorder.address_name;
    code_pos = detailorder.postal_code;
    pengiriman = detailorder.deskripsi + " - " + detailorder.service;

    alamat =
        detailorder.address +
        "," +
        detailorder.city +
        " - " +
        detailorder.subdistrict_name +
        "," +
        detailorder.province_name;

    var action = "";
    var sta = "";
    var text_status = "";

    if (detailorder.status == "waiting_accept_order" && toko_top) {
        sta = 0;
        text_status = `<p> Pesanan Baru </p>`;
        action += `
            <a data-id="${detailorder.id_cart_shop}" class="btn btn-success fa fa-check accept-this-order">&nbsp;Terima </a>
            <a data-id="${detailorder.id_cart_shop}" class="btn btn-danger fa fa-times cancel-order">&nbsp;Tolak </a>
        `;
    } else if (detailorder.status == "waiting_accept_order" && !toko_top) {
        sta = 0;
        text_status = `<p> Pesanan Baru </p>`;
        if (pembayaran) {
            action += `
            <a data-id="${detailorder.id_cart_shop}" class="btn btn-success fa fa-check accept-this-order">&nbsp;Terima </a>
            <a data-id="${detailorder.id_cart_shop}" class="btn btn-danger fa fa-times">&nbsp;Tolak </a>
        `;
        } else {
            action += `
                <p> Menunggu pembeli menyelesaikan pembayaran </p>
            `;
        }
    } else if (detailorder.status == "on_packing_process") {
        sta = 1;
        text_status = `<p> Pesanan Diproses </p>`;
        action += `
            <a data-id="${detailorder.id_cart_shop}" id="request_courier" class="btn btn-success fa fa-truck" data-id_courier="${detailorder.id_courier}">&nbsp;Request Pengiriman </a>
        `;
    } else if (detailorder.status == "send_by_seller") {
        sta = 2;
        text_status = `<p> Pesanan Dikirim </p>`;
        action += `
            <a class="btn btn-warning fa fa-copy" data-resi="${no_resi}">&nbsp;Copy Resi </a>
            <a class="btn btn-warning fa fa-map-marker" id="lacakResi" data-resi="${no_resi}" data-id_courier="${detailorder.id_courier}" data-id="${detailorder.id_cart_shop}">&nbsp;Lacak </a>
        `;
        if (detailorder.id_courier == "0") {
            if (detailorder.file_do == null || detailorder.file_do == "") {
                action += `
                    <a class="btn btn-warning fa fa-upload" id="uploadDO" data-id_courier="${detailorder.id_courier}" data-id="${detailorder.id_cart_shop}"  >&nbsp;Upload DO </a>
                `;
            }
        }
    } else if (
        detailorder.status == "complete" &&
        no_resi != "" &&
        pembayaran == false
    ) {
        sta = 3;
        text_status = `<p> Pesanan sedang dikirim </p>`;
        action += `
            <a class="btn btn-warning fa fa-copy" data-resi="${no_resi}">&nbsp;Copy Resi </a>
            <a class="btn btn-warning fa fa-map-marker" id="lacakResi" data-resi="${no_resi}" data-id_courier="${detailorder.id_courier}" data-id="${detailorder.id_cart_shop}">&nbsp;Lacak </a>
        `;
        if (detailorder.is_bast == "1") {
            text_status = `<p> Pesanan sudah Diterima </p>`;
        } else {
            if (detailorder.file_do == null || detailorder.file_do == "") {
                action += `
                    <a class="btn btn-warning fa fa-upload" id="uploadDO" data-id_courier="${detailorder.id_courier}" data-id="${detailorder.id_cart_shop}"  >&nbsp;Upload DO </a>
                `;
            }
        }
    } else if (
        detailorder.status == "complete" &&
        no_resi != "" &&
        pembayaran &&
        detailorder.is_bast == "1"
    ) {
        sta = 4;
        text_status = `<p> Pesanan Selesai </p>`;
        action += `
            <a class="btn btn-warning fa fa-copy" data-resi="${no_resi}">&nbsp;Copy Resi </a>
            <a class="btn btn-warning fa fa-map-marker" id="lacakResi" data-resi="${no_resi}" data-id_courier="${detailorder.id_courier}" data-id="${detailorder.id_cart_shop}">&nbsp;Lacak </a>
        `;
    } else if (detailorder.qty_dikembalikan > 0) {
        sta = 6;
        text_status = `<p> Pesanan Dikembalikan </p>`;
    } else if (detailorder.status == "waiting_approve_by_ppk") {
        sta = 7;
        text_status = `<p> Menunggu Persetujuan PPK </p>`;
    } else {
        sta = 5;
        text_status = `<p> Pesanan Dibatalkan </p>`;
    }

    view += `
    <div class="detail-transaksi">
        ${
            notif
                ? `
            <div class="batas-pengerjaan">
                <b class="warning-text">
                    <i class="material-symbols-outlined">warning</i>
                    Pesanan Belum Diterima Seller
                </b>
                <div class="date-pengerjaan">
                    <span class="left-text">Batas Proses Penerimaan 2 x 24 jam</span>
                    <span class="right-text">${batas_penerimaan_pesanan}</span>
                </div>
            </div>
            `
                : ""
        }

        <div class="aksi-invoice">
            <table style="width: 100%">
                <tr>
                    <th>No ${proforma ? "Proforma Invoice" : "Invoice"}</th>
                    <th>Tanggal Pesanan</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th rowspan="2" class="btn-container" >
                        ${action}
                    </th>
                </tr>
                <tr>
                    <td>
                        ${proforma ? "PRF" : ""} ${invoice}
                    </td>
                    <td>
                        ${tanggal_pesanan}
                    </td>
                    <td>
                        ${text_status}
                    </td>
                    <td>
                        ${pembayaran ? "Sudah Dibayar" : "Belum Dibayar"}
                    </td>
                </tr>
            </table>
        </div>
        <div class="detail-pengiriman">
            <table class="table-detail-order" style="width: 100%">
                <tr>
                    <th>Informasi Pengiriman</th>
                    <th></th>
                    <th class="btn-container">
                        ${
                            no_resi != ""
                                ? ` <a class="btn btn-danger fa fa-print" data-id="${detailorder.id_cart_shop}" id="cetakLabel">&nbsp;Cetak Lebel</a>`
                                : ` <a style="background-color: transparent;" class="btn">&nbsp;</a>`
                        }
                    </th>
                </tr>
                <tr>
                    <th>Nama Penerima</th>
                    <th>Nomor Telepon</th>
                    <th>Alamat</th>
                </tr>
                <tr>
                    <td> ${user} </td>
                    <td> ${nomor_telp} </td>
                    <td>${nama_alamat} <br>
                        ${alamat} <br>
                        ${code_pos}
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <th>NPWP</th>
                    <th>Keperluan</th>
                </tr>
                <tr>
                    <td>${detailorder.email}</td>
                    <td>${detailorder.npwp ? detailorder.npwp : ""}</td>
                    <td>${
                        detailorder.keperluan ? detailorder.keperluan : ""
                    }</td>
                </tr>
                <tr>
                    <th>Pengiriman</th>
                    <th>Resi</th>
                    <th>Pesan Pembeli</th>
                </tr>
                <tr>
                    <td>${pengiriman}</td>
                    <td> ${no_resi}</td>
                    <td> ${
                        detailorder.pesan_seller ? detailorder.pesan_seller : ""
                    } </td>
                </tr>
            </table>
        </div>
        <div class="aksi-file-transaksi">
            <a class="btn btn-warning fa fa-file" data-id="${
                detailorder.id_cart_shop
            }" id="cetakInvoice">&nbsp;INVOICE</a>
            <a class="btn btn-warning fa fa-file" data-id="${
                detailorder.id_cart_shop
            }" id="cetakkwantasi">&nbsp;KWITANSI</a>
            <a class="btn btn-warning fa fa-plus" data-id="${
                detailorder.id_cart_shop
            }" id="openKontrak">&nbsp;KONTRAK</a>
            ${
                detailorder.is_bast == 1
                    ? `<a class="btn btn-warning fa fa-file" data-id="${detailorder.id_cart_shop}" id="cetakBast">&nbsp;BAST</a>`
                    : ""
            }
            ${
                pmk == 59
                    ? `
                        <a class="btn btn-warning fa fa-file" data-id="${detailorder.id_cart_shop}" id="SuratPesanan">&nbsp;SURAT PESANAN</a>
                    ` +
                      (detailorder.file_pajak == null ||
                      detailorder.file_pajak == ""
                          ? `
                        <a class="btn btn-warning fa fa-upload" data-id="${detailorder.id_cart_shop}" id="UploadFaktur">&nbsp;Upload Faktur</a>
                    `
                          : "")
                    : ""
            }

        </div>
        <div class="detail-produk-transaksi">
        </div>
        <div class="detail-seller-transaksi">
            <div class="detailbiaya">
                <table class="table-detail-order">
                    <tr>
                        <td><span class="fa fa-truck">&nbsp; Biaya Pengiriman :</span></td>
                        <td>
                            ${formatRupiah(biaya_pengiriman)}
                        </td>
                    </tr>
                    <tr>
                        <td><span class="fa fa-shield">&nbsp; Asuransi Pengiriman :</span></td>
                        <td>
                            ${formatRupiah(asuransi_pengiriman)}
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span class="fa fa-money">
                                &nbsp; Total Pesanan
                                (${detailorder.qty}) :
                            </span>
                        </td>
                        <td>
                            ${formatRupiah(total)}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="detail-pembayaran-transaksi">
            <div>
                <table class="table-detail-order" style="width: 100%">
                    <tr>
                        <th>Pembayaran : ${detailorder.pembayaran} </th>
                        <th></th>
                    </tr>
                    <tr>
                        <th>Pilihan TOP : ${
                            detailorder.jml_top ? detailorder.jml_top : "0"
                        } hari</th>
                        <th></th>
                    </tr>
                </table>
            </div>
            <br>
            <div class="detailbiayaorder">
                <table class="table-detail-order">
                    <tr>
                        <th style="font-size:20px" colspan="3">
                            Detail Pembayaran
                        </th>
                        <th></th>
                    </tr>
                    <tr>
                        <td colspan="2">Subtotal belum PPN:</th>
                        <td>
                            ${formatRupiah(subtotal_nonPPn)}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Total Ongkos Kirim belum PPN :</td>
                        <td>
                            ${formatRupiah(ongir_nonPPn)}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Total Asuransi Pengiriman belum PPN :</td>
                        <td>
                            ${formatRupiah(asuransi_pengiriman_nonPPn)}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">PPN ${detailorder.val_ppn}% :</td>
                        <td>
                            ${formatRupiah(total_PPn)}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Total Diskon :</td>
                        <td>
                            ${formatRupiah(total_discount)}
                        </td>
                    </tr>
                    <tr>
                        <th style=" font-size:15px" colspan="2">
                            Total Pembayaran :
                        </th>
                        <td>
                            <b>
                                ${formatRupiah(total)}
                            </b>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    `;

    return view;
}

function V_produk_transaksi(dataArr) {
    view = `
        <table class="table-detail-order" style="width: 100%">
            <div class="detailproduct">
                <tr>
                    <th>Produk Dipesan</th>
                    <th>Harga Satuan</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
    `;

    var body = $(".detail-produk-transaksi");
    body.empty();

    dataArr.forEach(function (data) {
        var imageold = data.gambar_produk;
        var requiresBaseUrl = imageold.indexOf("http") === -1;
        var image = requiresBaseUrl
            ? "http://eliteproxy.co.id/" + imageold
            : imageold;

        view += `
            <tr>
                <td><img src="${image}" alt="produk"> ${data.nama_produk}</td>
                <td>
                    ${formatRupiah(data.harga_satuan_produk)}
                </td>
                <td>${data.qty_produk}</td>
                <td>
                    ${formatRupiah(data.harga_total_produk)}
                </td>
            </tr>
            `;
    });

    view += `</div> </table>`;

    body.append(view);
}
