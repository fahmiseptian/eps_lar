var csrfToken = $('meta[name="csrf-token"]').attr("content");
const imgDetail = appUrl + "/img/app/detail.svg";

$(function () {
    // Konfigurasi DataTables untuk kedua tabel
    var dataTableOptions = {
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
        language: {
            emptyTable: "Belum ada Data", // Pesan untuk tabel kosong
        },
    };

    // Inisialisasi DataTables
    $("#example1").dataTable(dataTableOptions);
    $("#example2").dataTable(dataTableOptions);

    // Jika lebar jendela kurang dari atau sama dengan 800 piksel, atur lebar kolom pencarian
    if (window.innerWidth <= 800) {
        $(".dataTables_filter input").css({
            width: "110px",
            margin: "5px",
            padding: "3px",
        });
        $(".dataTables_length select ").css({
            width: "50px",
            margin: "5px",
        });
    }
});

$(document).ready(function () {
    $(".horizontal-list li").click(function () {
        $(".horizontal-list li").removeClass("active");
        $(this).addClass("active");
    });
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

var allItems = $(".item-box-filter-pesanan");
var activeItem;
function loadData(tipe) {
    $('.item-box-filter-pesanan[data-tipe="menuDetaiNego"]').addClass("hidden");
    if (tipe === "menuDetaiNego") {
        initialize();
    } else {
        $("#overlay").show();
        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/nego/" + tipe,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                var body = $("#table-content");
                body.empty();
                var table = `
                <table id="example2" class="table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th style="width: 200px;" class="detail-full">Nama</th>
                                <th class="detail-full">Qty</th>
                                <th class="detail-full">Harga Nego Pembeli</th>
                                <th class="detail-full">Harga Yang Diterima</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                        <tfoot hidden>
                            <tr>
                                <th>Gambar</th>
                                <th class="detail-full">Nama</th>
                                <th class="detail-full">Qty</th>
                                <th class="detail-full">Harga Nego Pembeli</th>
                                <th class="detail-full">Harga Yang Diterima</th>
                                <th>Detail</th>
                            </tr>
                        </tfoot>
                    </table>
            `;
                body.append(table);
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
                    order: [[0, "asc"]],
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
                    var counter = 1;
                    // Tambahkan baris baru berdasarkan data yang diterima
                    var rows = response.negos.map((nego) => {
                        return [
                            `
                            <td>
                                <img src="${nego.dataProduct.artwork_url_sm[0]}" alt="${nego.dataProduct.name}" width="50px" class="img-responsive">
                            </td>
                            `,
                            nego.dataProduct.name,
                            nego.qty,
                            formatRupiah((nego.harga_nego / nego.qty)),
                            formatRupiah((nego.nominal_didapat / nego.qty)),
                            `
                            <td>
                                <p data-id="${nego.idnego}" id="detailNego" class="text-shadow">
                                    <img style="width: 25px;" src="${imgDetail}" alt="Detail Nego"> Lihat
                                </p>
                            </td>
                            `,
                        ];
                    });
                    table.rows.add(rows).draw(); // Tambahkan data dan perbarui tabel
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
}
$(document).on("click", allItems.filter(".open"), function () {
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
            if (tipe === "menuDetaiNego") {
                initialize();
                $(
                    '.item-box-filter-pesanan[data-tipe="menuDetaiNego"]'
                ).addClass("hidden");
            } else {
                loadData(tipe);
                console.log(tipe);
                allItems.removeClass("active open").hide();
                $(this).addClass("open");
                $(this).addClass("active");
                activeItem = $(this);

                allItems.slideUp();
                activeItem.slideDown();
            }
        });
}
function initialize() {
    allItems.hide();
    activeItem = allItems.first();
    activeItem.show().addClass("active");
    loadData("belum_direspon");
    $('.item-box-filter-pesanan[data-tipe="menuDetaiNego"]').addClass("hidden");
    setupEvents();
}
initialize();

$(document).on("click", "#change-nego", function () {
    var kondisi = $(this).data("kondisi");
    $.ajax({
        url: appUrl + "/api/seller/nego/" + kondisi,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var negos = response.negos;
            var tbody = $("#example2 tbody");
            tbody.empty();
            // Loop untuk membangun kembali baris-baris tabel dengan data yang baru
            negos.forEach(function (nego) {
                var row = `
                    <tr>
                        <td class="detail-full">
                            ${
                                nego.dataProduct.artwork_url_md[0]
                                    ? `<img src="${nego.dataProduct.artwork_url_md[0]}" style="width:50px; width:50px" alt="Product Image">`
                                    : "No Image"
                            }
                        </td>
                        <td>${nego.dataProduct.name}</td>
                        <td class="detail-full">${nego.qty}</td>
                        <td class="detail-full">Rp.${(
                            nego.harga_nego / nego.qty
                        ).toLocaleString()}</td>
                        <td class="detail-full">Rp.${(
                            nego.nominal_didapat / nego.qty
                        ).toLocaleString()}</td>
                        <td>
                            ${
                                nego.status == 0
                                    ? "Diajukan"
                                    : nego.status == 1
                                    ? "Diterima"
                                    : "Ditolak"
                            }
                        </td>
                        <td><a id="detailNego" data-id="${
                            nego.id_nego
                        }"><i id="google-icon" class="material-icons">info</i> Detail Nego</a></td>
                    </tr>
                `;
                tbody.append(row);
            });
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Error",
                "Terjadi kesalahan saat Pindah Data Nego",
                "error"
            );
        },
    });
});

$(document).on("click", "#detailNego", function () {
    var id_nego = $(this).data("id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/nego/detail/" + id_nego,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            $("#example2").DataTable().destroy();
            var tbody = $("#table-content");
            tbody.empty();

            allItems.removeClass("active open").hide();
            $(
                '.item-box-filter-pesanan[data-tipe="menuDetaiNego"]'
            ).removeClass("hidden");
            $('.item-box-filter-pesanan[data-tipe="menuDetaiNego"]').addClass(
                "open"
            );
            $('.item-box-filter-pesanan[data-tipe="menuDetaiNego"]').addClass(
                "active"
            );

            $(
                '.item-box-filter-pesanan[data-tipe="menuDetaiNego"]'
            ).slideDown();

            var view = viewdetailnego(response.nego);
            tbody.append(view);

            efectNegoDetail();
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Error",
                "Terjadi kesalahan saat Pindah Data Nego",
                "error"
            );
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).ready(function () {
    var typingTimer;
    var doneTypingInterval = 1500;
    var $input = $("#hargaResponSatuan");
    var $qty = $("#qty");
    var $product = $("#product");

    $input.on("input", function () {
        clearTimeout(typingTimer);
        if ($input.val()) {
            $("#hargaResponSatuan").val(formatRupiah($input.val()));
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });

    function doneTyping() {
        var hargaResponSatuan = unformatRupiah($input.val());
        var qty = parseInt($qty.val()) || 0;
        var total = hargaResponSatuan * qty;
        var id_product = parseInt($product.val()) || 0;

        $("#hargaResponTotal").val(formatRupiah(total));

        $.ajax({
            url: appUrl + "/api/seller/calcNego",
            type: "POST",
            data: {
                qty: qty,
                id_product: id_product,
                hargaResponSatuan: hargaResponSatuan,
            },
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                $("#hargaDiterimaSatuan").val(
                    formatRupiah(response.hargaSatuanDiterimaSeller)
                );
                $("#hargaDiterimaTotal").val(
                    formatRupiah(response.hargaTotalDiterimaSeller)
                );
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    }
});

// Call Modal Nego Ulang
$(document).ready(function () {
    $("#negoUlangForm").on("submit", function (event) {
        event.preventDefault();
        $("#overlay").show();

        // Ambil nilai asli dari input hargaSatuan dan hargaResponSatuan
        var hargaSatuanVal = unformatRupiah($("#hargaSatuan").val());
        var hargaDiterimaSatuanVal = unformatRupiah(
            $("#hargaDiterimaSatuan").val()
        );
        var hargaResponSatuanVal = unformatRupiah(
            $("#hargaResponSatuan").val()
        );

        // Set nilai asli kembali ke input
        $("#hargaSatuan").val(hargaSatuanVal);
        $("#hargaDiterimaSatuan").val(hargaDiterimaSatuanVal);
        $("#hargaResponSatuan").val(hargaResponSatuanVal);

        // Serialize form setelah nilai diubah
        var formData = $(this).serialize();
        console.log(formData);

        // Kirim AJAX request
        $.ajax({
            url: appUrl + "/api/seller/nego/add_respon",
            type: "POST",
            data: formData,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                loadData("menuDetaiNego");
                $("#negoUlangModal").modal("hide");
                $("#hargaDiterimaTotal").val('');
                $("#hargaResponTotal").val('');
                $("#hargaDiterimaSatuan").val('');
                $("#hargaResponSatuan").val('');
            },
            error: function (xhr, status, error) {
                console.error(error);
                // Handle error jika terjadi
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    });
});

function efectNegoDetail() {
    var typingTimer; // Timer identifier
    var doneTypingInterval = 1500; // Time in ms (1.5 seconds)
    var $input = $("#hargaResponSatuan");
    var $qty = $("#qty");
    var $product = $("#product");

    $(document).on("click", "#nego-ulang", function (event) {
        event.preventDefault();
        var hargaNegoSatuan = $(this).data("base_price");
        var hargaNegoTotal = $(this).data("total");
        var qty = $(this).data("qty");
        var product = $(this).data("product");
        var id_nego = $(this).data("id");
        var last_id = $(this).data("last_id");

        $("#hargaSatuan").val(formatRupiah(hargaNegoSatuan));
        $("#hargaNegoTotal").val(formatRupiah(hargaNegoTotal));
        $("#qty").val(qty);
        $("#product").val(product);
        $("#id_nego").val(id_nego);
        $("#last_id").val(last_id);

        $("#negoUlangModal").modal("show");
    });

    $input.on("input", function () {
        $("#hargaDiterimaSatuan").val("Menghitung...");
        $("#hargaDiterimaTotal").val("Menghitung...");
        $("#hargaResponTotal").val("Menghitung...");

        clearTimeout(typingTimer);
        if ($input.val()) {
            $("#hargaResponSatuan").val(formatRupiah($input.val()));
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });

    function doneTyping() {
        var hargaResponSatuan = unformatRupiah($input.val());
        var qty = parseInt($qty.val()) || 0;
        var total = hargaResponSatuan * qty;
        var id_product = parseInt($product.val()) || 0;
        var minimal = unformatRupiah($("#hargaSatuan").val());

        if (hargaResponSatuan <= minimal) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Nilai harus lebih besar dari nego Pembeli!",
            });
        } else {
            $("#hargaResponTotal").val(formatRupiah(total));

            $.ajax({
                url: appUrl + "/api/seller/calcNego",
                type: "POST",
                data: {
                    qty: qty,
                    id_product: id_product,
                    hargaResponSatuan: hargaResponSatuan,
                },
                xhrFields: {
                    withCredentials: true,
                },
                success: function (response) {
                    $("#hargaDiterimaSatuan").val(
                        formatRupiah(response.hargaSatuanDiterimaSeller)
                    );
                    $("#hargaDiterimaTotal").val(
                        formatRupiah(response.hargaTotalDiterimaSeller)
                    );
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                },
            });
        }
    }

    $(document).on("click", "#batal-nego", function (event) {
        event.preventDefault();
        var id_nego = $(this).data("id");

        swal.fire({
            title: "Apakah kamu yakin?",
            text: "Anda akan menolak negosiasi ini.",
            icon: "warning",
            input: "text",
            inputPlaceholder: "Masukkan alasan penolakan",
            showCancelButton: true,
            confirmButtonText: "Ya, Tolak!",
            cancelButtonText: "Batal",
            preConfirm: (alasan) => {
                return new Promise((resolve) => {
                    resolve(alasan || ""); // Resolves with the alasan value or an empty string
                });
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $("#overlay").show();
                var alasanPenolakan = result.value;
                $.ajax({
                    url: appUrl + "/api/seller/nego/tolak_nego",
                    type: "POST",
                    data: {
                        id_nego: id_nego,
                        alasan: alasanPenolakan,
                    },
                    xhrFields: {
                        withCredentials: true,
                    },
                    success: function (response) {
                        loadData("menuDetaiNego");
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $("#overlay").hide();
                    },
                });
            }
        });
    });

    $(document).on("click", "#setuju-nego", function (event) {
        event.preventDefault();
        var id_nego = $(this).data("id");

        swal.fire({
            title: "Apakah kamu yakin?",
            text: "Anda akan menyetujui negosiasi harga ini.",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "Ya, Setujui!",
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                $("#overlay").show();
                $.ajax({
                    url: appUrl + "/api/seller/nego/acc_nego",
                    type: "POST",
                    data: {
                        id_nego: id_nego,
                    },
                    xhrFields: {
                        withCredentials: true,
                    },
                    success: function (response) {
                        loadData("menuDetaiNego");
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    },
                    complete: function () {
                        $("#overlay").hide();
                    },
                });
            }
        });
    });
}

function viewdetailnego(data) {
    let statusText;
    switch (data.nego_status) {
        case 0:
            statusText = "Dalam Proses";
            desain_text = "proses_nego";
            break;
        case 1:
            statusText = "Disetujui";
            desain_text = "setujui_nego";
            break;
        case 2:
            statusText = "Ditolak";
            desain_text = "tolak_nego";
            break;
        default:
            statusText = "Status Tidak Dikenali";
    }
    var historiNego = ViewhistoriNego(data, data.dataNego);
    var html = `
    <div id="detail-nego-view">
        <div id="view-nego-produk">
            <div id="header-detail-nego">
                <h3>Derail Nego</h3>
                <div class="info-detail-nego">
                    <div class="text-left">
                        <p>${data.nama_pembeli}</p>
                        <p>${data.instansi ? data.instansi : "-"}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-histori-aksi" id="${desain_text}">${statusText}</p>
                        <p>${data.created_date}</p>
                    </div>
                </div>
            </div>
            <div id="body-detail-nego">
                <h3>Produk Nego</h3>
                <table class="table table-condensed">
                <thead>
                    <tr>
                        <th>Gambar Product</th>
                        <th>Nama</th>
                        <th>Qty</th>
                        <th>Harga Diterima Seller</th>
                        <th>Harga Invoice Pembeli</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <img src="${
                                data.dataProduct.artwork_url_sm[0]
                            }" alt="${
        data.dataProduct.name
    }" width="50px" class="img-responsive">
                        </td>
                        <td>${data.dataProduct.name}</td>
                        <td>${data.qty}</td>
                        <th>${formatRupiah((data.harga_didapat_terbaru / data.qty))}</th>
                        <th>${formatRupiah((data.harga_nego_terbaru / data.qty))}</th>
                    </tr>
                </tbody>
            </table>
            </div>
            <div id="footer-detail-nego">
                <h3>Histori Nego</h3>
                <table id="data-nego" class="table-histori-nego">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Oleh</th>
                        <th>Harga yang Di terima Seller</th>
                        <th>Harga Nego Pembeli</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>${data.created_date}</td>
                        <td>Harga Awal</td>
                        <td>${formatRupiah(data.dataProduct.price)}</td>
                        <td>${formatRupiah(data.dataProduct.hargaTayang)}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>-</td>
                    </tr>
                    ${historiNego}
                </tbody>
                <tfoot hidden>
                    <tr>
                        <th>Tanggal</th>
                        <th>Oleh</th>
                        <th>Harga yang Di terima Seller</th>
                        <th>Harga Nego Pembeli</th>
                        <th>Status</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </tfoot>
            </table>
            </div>
        </div>
    </div>
    `;
    return html;
}

function ViewhistoriNego(data, DataArr) {
    var view = "";
    DataArr.forEach(function (nego) {
        var sendByText = nego.send_by === 0 ? "Pembeli" : "Seller";
        var statusnego;
        switch (nego.status) {
            case 0:
                statusnego = "Dalam Proses";
                desain = "proses_nego";
                break;
            case 1:
                statusnego = "Disetujui";
                desain = "setuju_nego";
                break;
            case 2:
                statusnego = "Ditolak";
                desain = "tolak_nego";
                break;
            default:
                statusnego = "Status Tidak Dikenali";
        }

        if (sendByText == "Pembeli" && statusnego == "Dalam Proses") {
            aksi = `
                <p class="text-histori-aksi" id="setuju-nego" data-id="${nego.id_nego}">
                    Setujui
                </p>
                <p class="text-histori-aksi" id="nego-ulang" data-toggle="modal" data-target="#negoUlangModal" data-base_price="${nego.base_price}" data-total="${nego.harga_nego}" data-qty="${nego.qty}" data-product="${data.dataProduct.id}" data-id="${nego.id_nego}" data-last_id="${nego.id}" >
                    Nego
                </p>
                <p class="text-histori-aksi" id="batal-nego" data-id="${nego.id_nego}">
                    Tolak
                </p>
            `;
        } else {
            aksi = `-`;
        }

        view += `
            <tr>
                <td>${nego.timestamp}</td>
                <td>${sendByText}</td>
                <td>${formatRupiah(nego.nominal_didapat)}</td>
                <td>${formatRupiah(nego.harga_nego)}</td>
                <td>
                    <p class="text-histori-aksi" id="${desain}">
                        ${statusnego}
                    </p>
                </td>
                <td>${
                    nego.catatan_penjual
                        ? nego.catatan_penjual
                        : nego.catatan_pembeli
                        ? nego.catatan_pembeli
                        : "-"
                }</td>
                <td style="display:flex">${aksi}</td>
            </tr>
        `;
    });
    return view;
}
