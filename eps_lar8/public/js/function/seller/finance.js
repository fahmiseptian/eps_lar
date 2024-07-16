var csrfToken = $('meta[name="csrf-token"]').attr("content");

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
            emptyTable: 'Belum ada Data'  // Pesan untuk tabel kosong
        }
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
    $.ajax({
        url: appUrl + "/seller/finance/getRekening/" + rekeningId,
        type: "get",
        xhrFields: {
            withCredentials: true
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
                        return fetch("/seller/finance/updateRekening", {
                            method: "POST",
                            body: formData,
                            headers: {
                                "X-CSRF-Token": $(
                                    'meta[name="csrf-token"]'
                                ).attr("content"),
                            },
                        })
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
                            window.location.reload();
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
            // Jika konfirmasi penghapusan disetujui, kirim permintaan POST ke server
            $.ajax({
                url: appUrl + "/seller/finance/deleteRekening/" + rekeningId,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"), // Kirim token CSRF jika diperlukan
                    id: rekeningId,
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire(
                            "Sukses",
                            "Rekening telah berhasil dihapus.",
                            "success"
                        ).then(() => {
                            // Segarkan halaman setelah menghapus data
                            window.location.reload();
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
            // Kirim permintaan ke server untuk mengubah rekening utama
            $.ajax({
                url: appUrl + "/seller/finance/updateDefaultRekening",
                type: "POST",
                data: {
                    id: rekeningId,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function (response) {
                    Swal.fire(
                        "Sukses",
                        "Rekening utama berhasil diubah",
                        "success"
                    ).then(() => {
                        window.location.reload();
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        "Error",
                        "Terjadi kesalahan saat mengubah rekening utama",
                        "error"
                    );
                },
            });
        }
    });
});

$(document).on("click", "#updatePIN", function () {
    $("#updatePin").modal("show");
});

$('.pin-digit').on('input', function() {
    var $this = $(this);
    if ($this.val().length === 1) {
        $this.next('.pin-digit').focus();
    }
});

$('.pin-digit').on('keydown', function(e) {
    var $this = $(this);
    if (e.key === 'Backspace' && $this.val().length === 0) {
        $this.prev('.pin-digit').focus();
    }
});

$('#savePin').on('click', function() {
    var newPin = '';
    var confirmNewPin = '';

    $('#newPin .pin-digit').each(function() {
        newPin += $(this).val();
    });

    $('#confirmNewPin .pin-digit').each(function() {
        confirmNewPin += $(this).val();
    });

    if (newPin.length !== 6 || confirmNewPin.length !== 6) {
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'PIN harus terdiri dari 6 digit.'
        });
        return;
    }

    if (newPin !== confirmNewPin) {
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan',
            text: 'PIN dan konfirmasi PIN tidak cocok.'
        });
        return;
    }

    // Lakukan AJAX request
    $.ajax({
        url: appUrl + '/api/seller/finance/updatePin',  // Ganti dengan URL endpoint API Anda
        method: 'POST',
        data: {
            newPin: newPin
        },
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: 'PIN berhasil diperbarui.'
            }).then(() => {
                $('#updatePin').modal('hide');
            });
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: 'Anda Memasukan PIN yang Sama'
            });
        }
    });
});

$(document).on("click", "#tarikTrx", function () {
    var tbody = $("#tableTarikSaldo tbody");
    tbody.empty();

    $.ajax({
        url: appUrl + '/api/seller/finance/getTraxPending',
        method: 'GET',
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            var trxs = response.trx;
            if (trxs.length > 0) {
                trxs.forEach(function (trx) {
                    var row = `
                        <tr>
                            <td>${trx.invoice}</td>
                            <td class="detail-full">${trx.nama_instansi}</td>
                            <td>${formatRupiah(trx.total_diterima_seller)}</td>
                            <td><input type="checkbox" class="trx-checkbox" data-id="${trx.id}"></td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                if ($.fn.DataTable.isDataTable('#tableTarikSaldo')) {
                    $('#tableTarikSaldo').DataTable().destroy();
                }
                $("#tableTarikSaldo").DataTable({
                    bPaginate: true,
                    bLengthChange: true,
                    bFilter: true,
                    bSort: true,
                    bInfo: true,
                    bAutoWidth: true,
                    pageLength: 5
                });

                $("#tarikSaldo").modal("show");
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Transaksi',
                    text: 'Tidak ada transaksi yang dapat ditarik.'
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan',
                text: 'Terjadi kesalahan dalam memuat data transaksi.'
            });
        }
    });
});


$(document).on("click", "#requestSaldo", function () {
    var selectedIds = [];
    $("#tableTarikSaldo .trx-checkbox:checked").each(function () {
        selectedIds.push($(this).data('id'));
    });

    if (selectedIds.length > 0) {
        $("#modalPINSaldo").modal("show");
        $('#idTrx').val(selectedIds);
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Pilihan',
            text: 'Silakan pilih setidaknya satu transaksi.'
        });
    }
});

$(document).on("click", "#selectAll", function () {
    var checkboxes = $("#tableTarikSaldo .trx-checkbox");
    if (checkboxes.length > 0) {
        var allChecked = checkboxes.length === checkboxes.filter(":checked").length;
        checkboxes.prop("checked", !allChecked);
    }
});

$(document).ready(function() {
    $('#sendRequestTarikSaldo').on('click', function(event) {
        event.preventDefault();

        var idTrx = $('#idTrx').val();
        var pin = '';
        $('.pin-digit').each(function() {
            pin += $(this).val();
        });

        if (pin.length !== 6) {
            Swal.fire({
                icon: 'warning',
                title: 'PIN Tidak Lengkap',
                text: 'Silakan masukkan PIN lengkap.'
            });
            return;
        }
        console.log(pin);
        $.ajax({
            url: appUrl + '/api/seller/finance/requestrevenue',
            method: 'POST',
            data: {
                pin : pin,
                idTrx: idTrx,
            },
            xhrFields: {
                withCredentials: true
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Permintaan Berhasil Dikirim.'
                });
                $('#modalPINSaldo').modal('hide');
                $('#tarikSaldo').modal('hide');
                location.reload();
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Terjadi kesalahan dalam memvalidasi PIN.'
                });
            }
        });
    });

    // Auto focus on the next PIN input
    $('.pin-input .form-control').on('keyup', function() {
        if (this.value.length == this.maxLength) {
            $(this).next('.form-control').focus();
        }
    });
});
