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
    document.getElementById("overlay").style.display = "none";
    document.getElementById("loading").style.display = "flex"; //menampilkan loading
    $.ajax({
        url: appUrl + "/seller/finance/sendVerificationCode",
        type: "POST",
        headers: {
            "X-CSRF-Token": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            // Tampilkan input untuk kode verifikasi
            document.getElementById("overlay").style.display = "block";
            document.getElementById("loading").style.display = "none"; //tutup loading
            Swal.fire({
                title: "Verifikasi Email",
                input: "text",
                inputPlaceholder: "Masukkan kode verifikasi",
                showCancelButton: true,
                confirmButtonText: "Verifikasi",
                cancelButtonText: "Batal",
                focusConfirm: false,
                preConfirm: (code) => {
                    // Kirim kode verifikasi ke server untuk diverifikasi
                    return fetch("/seller/finance/verifyCode", {
                        method: "POST",
                        body: JSON.stringify({ code: code }),
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-Token": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                    })
                        .then((response) => {
                            if (!response.ok) {
                                throw new Error("Kode verifikasi salah.");
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
                    // Jika verifikasi berhasil, tampilkan modal untuk memperbarui PIN
                    Swal.fire({
                        title: "Update PIN Saldo Penjual",
                        html: `
                            <form id="updatePinForm">
                                <div class="form-group">
                                    <label for="newPin">PIN Baru:</label>
                                    <input type="password" id="newPin" name="newPin" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirmNewPin">Konfirmasi PIN Baru:</label>
                                    <input type="password" id="confirmNewPin" name="confirmNewPin" class="form-control" required>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: "Update",
                        cancelButtonText: "Batal",
                        focusConfirm: false,
                        preConfirm: () => {
                            // Ambil data dari form
                            const form =
                                document.getElementById("updatePinForm");
                            const formData = new FormData(form);

                            // Validasi pin dan konfirmasi pin
                            const newPin = formData.get("newPin");
                            const confirmNewPin = formData.get("confirmNewPin");

                            if (newPin !== confirmNewPin) {
                                Swal.showValidationMessage(
                                    "PIN baru dan konfirmasi PIN baru tidak cocok."
                                );
                                return false;
                            }

                            // Kirim data dengan metode POST ke controller
                            return fetch("/seller/finance/updateNewPin", {
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
                                        throw new Error(
                                            "Gagal memperbarui PIN saldo."
                                        );
                                    }
                                    return response.json();
                                })
                                .catch((error) => {
                                    Swal.showValidationMessage(
                                        `Terjadi kesalahan: ${error.message}`
                                    );
                                });
                        },
                    }).then((finalResult) => {
                        if (finalResult.isConfirmed) {
                            Swal.fire(
                                "Sukses",
                                "PIN saldo penjual berhasil diperbarui",
                                "success"
                            ).then(() => {
                                // Refresh halaman setelah menutup modal Swal
                                window.location.reload();
                            });
                        }
                    });
                }
            });
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Error",
                "Terjadi kesalahan saat mengirim kode verifikasi",
                "error"
            );
        },
    });
});
