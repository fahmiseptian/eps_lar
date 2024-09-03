var csrfToken = $('meta[name="csrf-token"]').attr("content");
const imgDetail = appUrl + "/img/app/detail.svg";
const imgWallet = appUrl + "/img/app/walet.svg";

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
    $("#overlay").show();

    if (tipe === "penghasilan") {
        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/finance/" + tipe,
            xhrFields: {
                withCredentials: true,
                loadData,
            },
            success: function (response) {
                var view = viewKeuangan(response.data, response.Pendingsaldo);
                var body = $("#table-content");
                body.empty();
                body.append(view);
                efekKeuangan();

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
            },
            error: function (xhr, status, error) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    }
    if (tipe === "saldo") {
        allItems.removeClass("active open").hide();

        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/finance/" + tipe,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                var body = $("#table-content");
                body.empty();
                var view = viewSaldo(response.data, response.PenarikanDana);
                body.append(view);
                efekKeuangan();

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
            },
            error: function (xhr, status, error) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    }
    if (tipe == "rekening") {
        allItems.removeClass("active open").hide();

        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/finance/" + tipe,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                var body = $("#table-content");
                body.empty();
                var viewAddRekening = viewaddRekening(
                    response.data,
                    response.rekeningNotdefault
                );

                body.append(viewAddRekening);
                efekKeuangan();
            },
            error: function (xhr, status, error) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    } else {
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
    allItems.hide();
    activeItem = allItems.first();
    activeItem.show().addClass("active");
    loadData("penghasilan");
    setupEvents();
}

function showTab(tabName) {
    document.getElementById("bisaDilepas").style.display = "none";
    document.getElementById("sudahDilepas").style.display = "none";

    document.getElementById(tabName).style.display = "block";

    document.getElementById("bisaDilepasTab").style.borderBottom =
        tabName === "bisaDilepas" ? "3px solid #FC6703" : "";
    document.getElementById("sudahDilepasTab").style.borderBottom =
        tabName === "sudahDilepas" ? "3px solid #FC6703" : "";
}

$(document).on("click", "#editRekening", function () {
    var rekeningId = $(this).attr("data-id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/seller/finance/getRekening/" + rekeningId,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            if (response && response.data_rekening_seller) {
                const data = response.data_rekening_seller;

                // Tampilkan formulir menggunakan Swal.fire
                Swal.fire({
                    title: "Edit Data Rekening",
                    html: `
                        <form id="editRekeningForm">
                            <div class="form-group">
                                <label>Nama Pemilik Rekening:</label>
                                <input type="text" name="rek_owner" value="${data.rek_owner}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Cabang Rekening:</label>
                                <input type="text" name="rek_location" value="${data.rek_location}" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Kota Rekening:</label>
                                <input type="text" name="rek_city" value="${data.rek_city}" class="form-control">
                            </div>
                            <input type="hidden" name="id" value="${data.id}">
                        </form>
                    `,
                    showCancelButton: true,
                    focusConfirm: false,
                    preConfirm: () => {
                        // Ambil data dari form
                        const form =
                            document.getElementById("editRekeningForm");
                        const formData = new FormData(form);

                        // Kirim data dengan metode POST ke controller
                        return fetch(
                            appUrl + "/seller/finance/updateRekening",
                            {
                                method: "POST",
                                body: formData,
                                headers: {
                                    "X-CSRF-Token": csrfToken,
                                },
                            }
                        )
                            .then((response) => {
                                if (!response.ok) {
                                    throw new Error("Gagal mengirim data.");
                                }
                                return response.json();
                            })
                            .catch((error) => {
                                Swal.showValidationMessage(
                                    `Terjadi kesalahan: ${error.message}`
                                );
                            });
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire(
                            "Berhasil",
                            "Data berhasil diubah",
                            "success"
                        ).then(() => {
                            // Refresh halaman setelah menutup modal Swal
                            // window.location.reload();
                            loadData("rekening");
                        });
                    }
                });
            } else {
                Swal.fire(
                    "Error",
                    "Data rekening seller tidak ditemukan",
                    "error"
                );
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan saat memuat data", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).on("click", "#hapusRekening", function () {
    var rekeningId = $(this).attr("data-id");

    // Tampilkan konfirmasi penghapusan dengan Swal.fire
    Swal.fire({
        title: "Konfirmasi Penghapusan",
        text: "Apakah Anda yakin ingin menghapus rekening ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, hapus",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            $("#overlay").show();

            // Jika konfirmasi penghapusan disetujui, kirim permintaan POST ke server
            $.ajax({
                url: appUrl + "/seller/finance/deleteRekening/" + rekeningId,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"), // Kirim token CSRF jika diperlukan
                    id: rekeningId,
                },
                xhrFields: {
                    withCredentials: true,
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire(
                            "Sukses",
                            "Rekening telah berhasil dihapus.",
                            "success"
                        ).then(() => {
                            // Segarkan halaman setelah menghapus data
                            // window.location.reload();
                            loadData("rekening");
                        });
                    } else {
                        Swal.fire(
                            "Gagal",
                            "Terjadi kesalahan saat menghapus rekening.",
                            "error"
                        );
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        "Gagal",
                        "Terjadi kesalahan saat menghapus rekening.",
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

$(document).on("click", "#editDefaultRekening", function () {
    var rekeningId = $(this).data("id");
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah kamu yakin ingin menjadikan ini rekening utama anda?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Ya, jadikan rekening utama",
        cancelButtonText: "Batal",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $("#overlay").show();

            // Kirim permintaan ke server untuk mengubah rekening utama
            $.ajax({
                url: appUrl + "/seller/finance/updateDefaultRekening",
                type: "POST",
                data: {
                    id: rekeningId,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                xhrFields: {
                    withCredentials: true,
                },
                success: function (response) {
                    Swal.fire(
                        "Sukses",
                        "Rekening utama berhasil diubah",
                        "success"
                    ).then(() => {
                        // window.location.reload();
                        loadData("rekening");
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        "Error",
                        "Terjadi kesalahan saat mengubah rekening utama",
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

$(document).on("click", "#updatePIN", function () {
    $("#updatePin").modal("show");
});

$(".pin-digit").on("input", function () {
    var $this = $(this);
    if ($this.val().length === 1) {
        $this.next(".pin-digit").focus();
    }
});

$(".pin-digit").on("keydown", function (e) {
    var $this = $(this);
    if (e.key === "Backspace" && $this.val().length === 0) {
        $this.prev(".pin-digit").focus();
    }
});

$("#savePin").on("click", function () {
    var newPin = "";
    var confirmNewPin = "";

    $("#newPin .pin-digit").each(function () {
        newPin += $(this).val();
    });

    $("#confirmNewPin .pin-digit").each(function () {
        confirmNewPin += $(this).val();
    });

    if (newPin.length !== 6 || confirmNewPin.length !== 6) {
        Swal.fire({
            icon: "error",
            title: "Kesalahan",
            text: "PIN harus terdiri dari 6 digit.",
        });
        return;
    }

    if (newPin !== confirmNewPin) {
        Swal.fire({
            icon: "error",
            title: "Kesalahan",
            text: "PIN dan konfirmasi PIN tidak cocok.",
        });
        return;
    }
    $("#overlay").show();

    // Lakukan AJAX request
    $.ajax({
        url: appUrl + "/api/seller/finance/updatePin",
        method: "POST",
        data: {
            newPin: newPin,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "Berhasil",
                text: "PIN berhasil diperbarui.",
            }).then(() => {
                $("#updatePin").modal("hide");
            });
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Anda Memasukan PIN yang Sama",
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).on("click", "#tarikTrx", function () {
    var tbody = $("#tableTarikSaldo tbody");
    tbody.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/finance/getTraxPending",
        method: "GET",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var trxs = response.trx;
            if (trxs.length > 0) {
                trxs.forEach(function (trx) {
                    var row = `
                        <tr>
                            <td>${trx.invoice}</td>
                            <td class="detail-full">${
                                trx.nama_instansi ? trx.nama_instansi : ""
                            }</td>
                            <td>${formatRupiah(trx.total_diterima_seller)}</td>
                            <td><input type="checkbox" class="trx-checkbox" data-id="${
                                trx.id
                            }"></td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                if ($.fn.DataTable.isDataTable("#tableTarikSaldo")) {
                    $("#tableTarikSaldo").DataTable().destroy();
                }
                $("#tableTarikSaldo").DataTable({
                    bPaginate: true,
                    bLengthChange: true,
                    bFilter: true,
                    bSort: true,
                    bInfo: true,
                    bAutoWidth: true,
                    pageLength: 5,
                });

                $("#tarikSaldo").modal("show");
            } else {
                Swal.fire({
                    icon: "info",
                    title: "Tidak Ada Transaksi",
                    text: "Tidak ada transaksi yang dapat ditarik.",
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Terjadi kesalahan dalam memuat data transaksi.",
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).on("click", "#requestSaldo", function () {
    var selectedIds = [];
    $("#tableTarikSaldo .trx-checkbox:checked").each(function () {
        selectedIds.push($(this).data("id"));
    });

    if (selectedIds.length > 0) {
        $("#modalPINSaldo").modal("show");
        $("#idTrx").val(selectedIds);
    } else {
        Swal.fire({
            icon: "warning",
            title: "Tidak Ada Pilihan",
            text: "Silakan pilih setidaknya satu transaksi.",
        });
    }
});

$(document).on("click", "#selectAll", function () {
    var checkboxes = $("#tableTarikSaldo .trx-checkbox");
    if (checkboxes.length > 0) {
        var allChecked =
            checkboxes.length === checkboxes.filter(":checked").length;
        checkboxes.prop("checked", !allChecked);
    }
});

$(document).ready(function () {
    $("#sendRequestTarikSaldo").on("click", function (event) {
        event.preventDefault();

        var idTrx = $("#idTrx").val();
        var pin = "";
        $(".pin-digit").each(function () {
            pin += $(this).val();
        });

        if (pin.length !== 6) {
            Swal.fire({
                icon: "warning",
                title: "PIN Tidak Lengkap",
                text: "Silakan masukkan PIN lengkap.",
            });
            return;
        }
        $("#overlay").show();
        $.ajax({
            url: appUrl + "/api/seller/finance/requestrevenue",
            method: "POST",
            data: {
                pin: pin,
                idTrx: idTrx,
            },
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Permintaan Berhasil Dikirim.",
                });
                $("#modalPINSaldo").modal("hide");
                $("#tarikSaldo").modal("hide");
                // location.reload();
                loadData("saldo");
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Kesalahan",
                    text: xhr.responseJSON
                        ? xhr.responseJSON.message
                        : `Terjadi kesalahan: ${error}`,
                });
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    });

    // Auto focus on the next PIN input
    $(".pin-input .form-control").on("keyup", function () {
        if (this.value.length == this.maxLength) {
            $(this).next(".form-control").focus();
        }
    });
});

function viewKeuangan(data, dataArr) {
    var dataTable = dataPenghasilan(dataArr);
    var view = `
    <div id="view-informasi-penghasilan">
        <h3 style="margin-left:20px"> <b>Informasi Penghasilan</b></h3>
        <div class="informasi-penghasilan">
            <div>
                <p style="margin:0"> Akan Dilepas</p>
                <small style="margin:0">Total</small>
                <p style="margin:0">${formatRupiah(data.saldo)}</p>
                <span class="btn btn-danger" id="tarikTrx"> Tarik </span>
            </div>
            <div>
                <p style="margin:0"> Sudah Dilepas</p>
                <small style="margin:0">Total</small>
                <p style="margin:0">${formatRupiah(data.saldoSelesai)}</p>
            </div>
            <div>
                <small style="margin-left:-20%">Total</small>
                <p style="margin:0">${formatRupiah(
                    data.saldo + data.saldoSelesai
                )}</p>
            </div>
        </div>
    </div>
    <div class="rincian-penghasilan">
        <div class="dataRician">
            <p style="font-size: 20px;"> <b> Rincian Penghasilan </b> </p>
            <div style="display: flex">
                <div class="tipe-penghasilan active" data-tipe="akan_dilepas">
                    Akan Dilepas
                </div>
                <div class="tipe-penghasilan" data-tipe="sudah_dilepas">
                    Sudah Dilepas
                </div>
            </div>
            <div class="tableDataKeuangan">
                <table id="example2" class="table">
                    <thead>
                        <tr>
                            <th>No Invoice</th>
                            <th>Suku Dinas</th>
                            <th>Nama PIC</th>
                            <th>Jumlah Dana Dilepas</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${dataTable}
                    </tbody>
                    <tfoot hidden>
                        <tr>
                            <th>No Invoice</th>
                            <th>Suku Dinas</th>
                            <th>Nama PIC</th>
                            <th>Jumlah Dana Dilepas</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    `;
    return view;
}

function viewSaldo(data, dataArr) {
    var dataTable = dataPenarikan(dataArr);
    var view = `
    <div id="view-informasi-penghasilan">
        <h3 style="margin-left:20px"> <b>Informasi Penghasilan</b></h3>
        <div class="informasi-penghasilan">
            <div>
                <p style="margin:0"> Akan Dilepas</p>
                <small style="margin:0">Total</small>
                <p style="margin:0">${formatRupiah(data.saldo)}</p>
                <span class="btn btn-danger" id="tarikTrx"> Tarik </span>
            </div>
            <div>
                <p style="margin:0"> Sudah Dilepas</p>
                <small style="margin:0">Total</small>
                <p style="margin:0">${formatRupiah(data.saldoSelesai)}</p>
            </div>
            <div>
                <small style="margin-left:-20%">Total</small>
                <p style="margin:0">${formatRupiah(
                    data.saldo + data.saldoSelesai
                )}</p>
            </div>
        </div>
    </div>
    <div class="rincian-penghasilan">
        <div class="dataRician">
            <p style="font-size: 20px;"> <b> Transaksi Terakhir </b> </p>
            <div class="tableDataKeuangan">
                <table id="example2" class="table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Id Penarikan</th>
                            <th>Rekening</th>
                            <th>Total Dana</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${dataTable}
                    </tbody>
                    <tfoot hidden>
                        <tr>
                            <th>No</th>
                            <th>Id Penarikan</th>
                            <th>Rekening</th>
                            <th>Total Dana</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Detail</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    `;

    return view;
}

function viewDilepeas(dataArr) {
    var dataTable = dataPenghasilanKeluar(dataArr);
    var view = `
        <table id="example2" class="table">
            <thead>
                <tr>
                    <th>No Invoice</th>
                    <th>Nama PIC</th>
                    <th>Tanggal Dana Dilepas</th>
                    <th>Jumlah Dana Dilepas</th>
                    <th>Status</th>
                    <th>Bukti Transfer</th>
                </tr>
            </thead>
            <tbody>
                ${dataTable}
            </tbody>
            <tfoot hidden>
                <tr>
                    <th>No Invoice</th>
                    <th>Nama PIC</th>
                    <th>Tanggal Dana Dilepas</th>
                    <th>Jumlah Dana Dilepas</th>
                    <th>Status</th>
                    <th>Bukti Transfer</th>
                </tr>
            </tfoot>
        </table>
    `;
    return view;
}

function dataPenghasilan(dataArr) {
    var rows = "";
    dataArr.forEach(function (data) {
        rows += `
            <tr>
                <td>${data.invoice}</td>
                <td>${data.nama_instansi ? data.nama_instansi : "-"}</td>
                <td>${data.nama ? data.nama : "-"}</td>
                <td>${formatRupiah(data.total_diterima_seller)}</td>
            </tr>
        `;
    });
    return rows;
}

function dataPenarikan(dataArr) {
    var rows = "";
    dataArr.forEach(function (data) {
        var lastFourDigits = data.rek_number.slice(-4);
        rows += `
            <tr>
                <td>No</td>
                <td>TR ${data.id}</td>
                <td>${data.rek_owner} ${data.name}-****${lastFourDigits}</td>
                <td>${formatRupiah(data.total)} </td>
                <td>${data.status} </td>
                <td>${data.last_update} </td>
                <td>
                    <p data-id="${
                        data.id
                    }" id="lihat-detai-tr" class="text-shadow">
                        <img style="width: 25px;" src="${imgDetail}" alt="Detail TR"> Lihat
                    </p>
                </td>
            </tr>
        `;
    });
    return rows;
}

function viewRekening(data, dataArr) {
    var rekening = `
        <div class="add-card">
            <i class="fa fa-plus" id="tambahRekening"></i>
        </div>
    `;
    return rekening;
}

function dataPenghasilanKeluar(dataArr) {
    var rows = "";
    dataArr.forEach(function (data) {
        var imageold = data.bukti_transfer;
        var requiresBaseUrl = imageold.indexOf("http") === -1;
        var image = requiresBaseUrl
            ? "http://127.0.0.1:8001/" + imageold
            : imageold;

        rows += `
            <tr>
                <td>${data.invoice}</td>
                <td>${data.nama}</td>
                <td>${data.execute_date}</td>
                <td>${formatRupiah(data.total_diterima_seller)}</td>
                <td>${data.status}</td>
                <td>
                    <p data-image="${image}" id="lihat-bukti" class="text-shadow">
                       <img style="width: 25px;" src="${imgDetail}" alt="open-bukti"> Lihat
                    </p>
                </td>
            </tr>
        `;
    });
    return rows;
}

function viewaddRekening(data, dataArray) {
    // Define common HTML parts
    const addCardHtml = `
        <div class="add-Card-rekening">
        <p style="font-size: 20px; margin-left:15px "> <b>Tambah Rekening</b></p>
            <div class="add-card">
                <i class="fa fa-plus" id="tambahRekening"></i>
            </div>
        </div>
    `;

    var rekening = "";

    if (data.rekening != null) {
        rekening += `
        <div class="Card-rekening">
            <p style="font-size: 20px; margin-left:15px "> <b>Rekening Saya</b></p>
            <div class="item-rekening">
                <div class="credit-card">
                    <div class="logo-and-bank">
                        <div class="brand-logo"><img src="/img/app/logo eps.png" alt="Bank Logo" class="bank-logo"></div>
                        <div class="bank-name">${
                            data.rekening.name ? data.rekening.name : ""
                        }</div>
                        <div class="icon-container">
                            <i class="fa fa-edit" data-id="${
                                data.rekening.id
                            }" id="editRekening"></i>
                        </div>
                    </div>
                    <div class="card-info">
                        <div class="card-number">** **** ${data.rekening.rek_number.slice(
                            -4
                        )}</div>
                        <div class="card-location">${
                            data.rekening.rek_city
                                ? data.rekening.rek_city + " "
                                : ""
                        }${
            data.rekening.rek_location ? data.rekening.rek_location : ""
        }</div>
                        <div class="card-holder">${
                            data.rekening.rek_owner
                        }</div>
                    </div>
                </div>
        `;
    }

    const rekeningNotdefault = dataArray;
    let rekeningHtml = "";

    rekeningNotdefault.forEach((rk) => {
        rekeningHtml += `
                <div class="credit-card">
                    <div class="logo-and-bank">
                        <div class="brand-logo"><img src="/img/app/logo eps.png" alt="Bank Logo" class="bank-logo"></div>
                        <div class="bank-name">${rk.bank_name}</div>
                        <div class="icon-container">
                            <i class="fa fa-edit" data-id="${
                                rk.id
                            }" id="editRekening"></i>
                            <i class="fa fa-trash-o" data-id="${
                                rk.id
                            }" id="hapusRekening"></i>
                        </div>
                    </div>
                    <div class="card-info" data-id="${
                        rk.id
                    }" id="editDefaultRekening">
                        <div class="card-number">** **** ${rk.rek_number.slice(
                            -4
                        )}</div>
                        <div class="card-location">${
                            rk.rek_city ? rk.rek_city + " " : ""
                        }${rk.rek_location ? rk.rek_location : ""}</div>
                        <div class="card-holder">${rk.rek_owner}</div>
                    </div>
                </div>
            `;
    });

    rekeningHtml += "</div></div>";

    const pinUpdateHtml = `
        <div class="pin-rekening">
            <p style="font-size: 20px; margin-left:15px "> <b>Pengaturan PIN</b></p>
            <div style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img style="width: 50px;" src="${imgWallet}" alt="Dompet">
                    <p>Pin Saldo</p>
                    <p>********</p>
                </div>
                <div class="tombol-update-pin">
                    <p style="margin: 5px 20px;" id="updatePIN">Update Pin Saldo</p>
                </div>
            </div>
        </div>
    `;

    // Start building the view
    view = "";
    // Add PIN update HTML conditionally based on data.jmlRekening
    if (data.jmlRekening === 0 || data.jmlRekening === null) {
        view += addCardHtml + pinUpdateHtml;
    } else if (data.jmlRekening === 3) {
        view += rekening + rekeningHtml + pinUpdateHtml;
    } else {
        view += addCardHtml + rekening + rekeningHtml + pinUpdateHtml;
    }

    return view;
}

$(document).on("click", "#lihat-detai-tr", function () {
    var idtr = $(this).attr("data-id");

    var tbody = $("#tableDetailTrx tbody");
    tbody.empty();

    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/finance/detailPenarikan/" + idtr,
        method: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            $("#modalDetailTr").modal("show");
            var body = "";
            var headers = $("#info-trx");
            headers.empty();
            headers.text("Detail Transaksi TR-" + idtr);
            response.forEach(function (data) {
                body += `
                    <tr>
                        <td>${data.invoice}</td>
                        <td>${formatRupiah(data.total)}</td>
                        <td>${data.status}</td>
                    </tr>
                `;
            });
            tbody.append(body);
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Terjadi kesalahan Mohon coba lagi.",
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).on("click", "#tambahRekening", function () {
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/finance/getbank",
        method: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            // Generate the options for the bank select element
            let bankOptions = response
                .map(
                    (bank) => `<option value="${bank.id}">${bank.name}</option>`
                )
                .join("");

            // Tampilkan modal menggunakan Swal.fire
            Swal.fire({
                title: "Tambah Rekening",
                html: `
                    <form id="addRekeningForm">
                        <div class="form-group">
                            <label for="nama">Nama Pemilik Rekening:</label>
                            <input type="text" id="nama" name="nama" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="bank">Bank:</label>
                            <select id="bank" name="bank" class="form-control" required>
                                ${bankOptions}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="noRekening">No. Rekening:</label>
                            <input type="text" id="noRekening" name="noRekening" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="cabangBank">Cabang Bank:</label>
                            <input type="text" id="cabangBank" name="cabangBank" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="kotaKabupaten">Kota/Kabupaten:</label>
                            <input type="text" id="kotaKabupaten" name="kotaKabupaten" class="form-control" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: "Tambah",
                cancelButtonText: "Batal",
                focusConfirm: false,
                preConfirm: () => {
                    // Ambil data dari form
                    const form = document.getElementById("addRekeningForm");
                    const formData = new FormData(form);

                    // Periksa apakah semua bidang wajib diisi
                    const inputs = form.querySelectorAll(
                        "input[required], select[required]"
                    );
                    for (const input of inputs) {
                        if (input.value.trim() === "") {
                            Swal.showValidationMessage(
                                `Silakan isi bidang ${input.labels[0].textContent}`
                            );
                            return false;
                        }
                    }

                    // Kirim data dengan metode POST ke controller
                    return fetch(appUrl + "/seller/finance/addRekening", {
                        method: "POST",
                        body: formData,
                        headers: {
                            "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        xhrFields: {
                            withCredentials: true,
                        },
                    })
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error("Gagal menambahkan rekening.");
                            }
                            return response.json();
                        })
                        .catch((error) => {
                            Swal.showValidationMessage(
                                `Terjadi kesalahan: ${error.message}`
                            );
                        });
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        "Sukses",
                        "Rekening berhasil ditambahkan",
                        "success"
                    ).then(() => {
                        // Refresh halaman setelah menutup modal Swal
                        // window.location.reload();
                        loadData("rekening");
                    });
                }
            });
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
                text: "Terjadi kesalahan dalam memvalidasi PIN.",
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

function efekKeuangan() {
    $(".tipe-penghasilan").on("click", function () {
        $(".tipe-penghasilan").removeClass("active");
        $(this).addClass("active");
        var tipe = $(this).data("tipe");
        if (tipe === "sudah_dilepas") {
            $("#overlay").show();
            $.ajax({
                type: "GET",
                url: appUrl + "/api/seller/finance/" + tipe,
                xhrFields: {
                    withCredentials: true,
                },
                success: function (response) {
                    $(".tableDataKeuangan").empty();

                    var data = viewDilepeas(response.Pendingsaldo);
                    $(".tableDataKeuangan").append(data);
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
                            zeroRecords:
                                "Tidak ada catatan yang cocok ditemukan",
                            search: "",
                            sLengthMenu: "_MENU_ ",
                        },
                    });
                    $("#example2_filter input").attr(
                        "placeholder",
                        "Pencarian"
                    );
                },
                error: function (xhr, status, error) {
                    Swal.fire("Error", "Terjadi kesalahan", "error");
                },
                complete: function () {
                    $("#overlay").hide(); // Sembunyikan loader setelah selesai
                },
            });
        } else {
            initialize();
        }
    });

    $(document).on("click", "#lihat-bukti", function () {
        var image = $(this).data("image");
        console.log(image);
        Swal.fire({
            title: "Bukti Transfer",
            imageUrl: image,
            imageAlt: "Bukti Transfer",
            confirmButtonText: "Tutup",
        });
    });
}
