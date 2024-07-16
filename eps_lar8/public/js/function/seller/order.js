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

    // Inisialisasi DataTables
    $("#example1").dataTable(dataTableOptions);
    $("#example2").dataTable(dataTableOptions2);

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

function toggleFilterorder(element) {
    var status_order = element.getAttribute("data-status-order");

    $.ajax({
        type: "GET",
        url: appUrl + "/seller/order/filter/" + status_order,
        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            console.log("berhasil ");
            window.location.href =
                appUrl + "/seller/order/filter/" + status_order;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
    });
}

function viewDetail(element) {
    var id_cart_shop = element.getAttribute("data-id-order");
    console.log(appUrl);
    $.ajax({
        type: "GET",
        url: appUrl + "/seller/order/detail/" + id_cart_shop,
        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            console.log("berhasil");
            window.location.href =
                appUrl + "/seller/order/detail/" + id_cart_shop;
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
                xhrFields: {
                    withCredentials: true
                },
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
            xhrFields: {
                withCredentials: true
            },
            success: function () {
                location.reload();
            },
        });
    }
});

$(document).on("click", "#openKontrak", function () {
    var id_cart_shop = $(this).data("id");
    var body = $(".col-md-10");
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
            withCredentials: true
        },
        success: function (response) {
            var newBody = `
                <h3 style="margin-left: 15px; margin-bottom:-5px"><b>Kontrak</b></h3>
                <hr>
                <div class="box box-warning">
                    <div class="box-body">
                        <table id="tableKontak" class="table table-bordered table-hover table-striped" style="width: 100%">
                            <thead style="background-color: #fff;">
                                <tr>
                                    <th>No Kontrak</th>
                                    <th>Tanggal Kontrak</th>
                                    <th class="detail-full">Catatan</th>
                                    <th class="detail-full">Tanggal Buat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response === 0 ? `
                                <tr>
                                    <td colspan="5" style="text-align:center;">
                                        <a class="btn btn-info" id="CreateKontrak" style="width:100%;" data-id="${id_cart_shop}">
                                            Buat Kontrak <span id="google-icon" class="material-icons">contract_edit</span>
                                        </a>
                                    </td>
                                </tr>
                                ` : `
                                <tr>
                                    <td>${response.no_kontrak}</td>
                                    <td>${response.tanggal_kontrak}</td>
                                    <td>${response.catatan !== null ? response.catatan : '-'}</td>
                                    <td>${response.created_date}</td>
                                    <td>
                                        <div style="display: flex">
                                            <button id="edit-contract-button" class="material-icons" style="width: 50px; background-color: rgb(53, 152, 219); color: white;" data-id="${id_cart_shop}">edit_note</button>
                                            &nbsp;
                                            <button id="download-contract-button" class="material-icons" style="width: 50px; background-color: rgb(224, 62, 45); color: white;" data-id="${id_cart_shop}">download</button>
                                        </div>
                                    </td>
                                </tr>
                                `}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>No Kontrak</th>
                                    <th>Tanggal Kontrak</th>
                                    <th class="detail-full">Catatan</th>
                                    <th class="detail-full">Tanggal Buat</th>
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

function CreateKontrak(id_cart_shop) {
    console.log(id_cart_shop);
    var body = $(".col-md-10");
    body.empty();
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/getOrder/"+ id_cart_shop,
        type: "get",
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            var order=response.order;
            var dataArr = {
                'noKontrak':'',
                'id_cart_shop': id_cart_shop || '',
                'total': order.total || 0,
                'nilaiKontrak': order.total || 0,
                'tanggal_kontrak':'',
                'catatan':'',
                'content':response.htmlContent,
            };
            body.append(FormKotrak(dataArr));
            tinymce.init({
                selector: 'textarea#document',
                height: 700,
                plugins: 'autoresize',
                toolbar_mode: 'floating'
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
    var body = $(".col-md-10");
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
            withCredentials: true
        },
        success: function (response) {
            var dataArr = {
                'noKontrak':response.no_kontrak,
                'id_cart_shop': id_cart_shop,
                'total': response.total_harga,
                'nilaiKontrak': response.nilai_kontrak,
                'tanggal_kontrak':response.tanggal_kontrak,
                'catatan':response.catatan,
                'content':response.document,
            };
            body.append(FormKotrak(dataArr));
            tinymce.init({
                selector: 'textarea#document',
                height: 700,
                plugins: 'autoresize',
                toolbar_mode: 'floating'
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
            responseType: 'blob',  // Ubah ini menjadi 'blob'
            withCredentials: true
        },
        success: function (response, status, xhr) {
            var blob = new Blob([response], { type: 'application/pdf' });
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'kontrak_' + Date.now() + '.pdf';  // Nama file yang diunduh
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
    console.log(dataArr);
    var formulir = `
        <div class="box box-warning">
            <div class="box-body">
                <form action="{{ route('generate.kontrak') }}" method="POST">
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2">
                                <div class="form-group">
                                    <p style="font-size: 14px">No Kontrak</p>
                                    <input type="text" class="form-control" value="${dataArr.noKontrak}" id="noKontrak" name="noKontrak" required>
                                    <input type="hidden" class="form-control" value="${dataArr.id_cart_shop}" id="id_cs" name="id_cs">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Total Harga</p>
                                    <input type="text" class="form-control" id="totalHarga" name="totalHarga" readonly required value="${formatRupiah(dataArr.total)}">
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Nilai Kontrak</p>
                                    <input type="text" class="form-control" id="nilaiKontrak" name="nilaiKontrak" value="${dataArr.nilaiKontrak}" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Tanggal Kontrak</p>
                                    <input type="date" class="form-control" id="tanggalKontrak" name="tanggalKontrak" value="${dataArr.tanggal_kontrak}" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <p style="font-size: 14px">Catatan</p>
                                    <input type="text" class="form-control" id="catatan" name="catatan" value="${dataArr.catatan !== null ? dataArr.catatan : ''}" required>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b> *note : Mohon dirubah pada bagian text yang berwarna: </b>
                                <p style="color: rgb(53, 152, 219);">-Seller : Biru   </p>
                                <p style="color: rgb(224, 62, 45);">-Pembeli : Merah </p>
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
        selector: 'textarea#document',
        height: 700,
        plugins: 'autoresize',
        toolbar_mode: 'floating'
    });

    return formulir;
}

function generatePDF() {
    var noKontrak = document.getElementById('noKontrak').value;
    var id_cs = document.getElementById('id_cs').value;
    var catatan = document.getElementById('catatan').value;
    var tanggalKontrak = document.getElementById('tanggalKontrak').value;
    var totalHarga = document.getElementById('totalHarga').value;
    var nilaiKontrak = document.getElementById('nilaiKontrak').value;
    var content = document.getElementById('document').value;

    // Validasi input kosong
    if (noKontrak === '' || tanggalKontrak === '' || totalHarga === '' || nilaiKontrak === '' || content === '') {
        Swal.fire("Error", "Harap lengkapi semua kolom", "error");
        return;
    }

    $("#overlay").show();
    var formData = {
        'id_cs':id_cs,
        'no_kontrak': noKontrak,
        'catatan': catatan,
        'tanggal_kontrak': tanggalKontrak,
        'total_harga': unformatRupiah(totalHarga),
        'nilai_kontrak': nilaiKontrak,
        'content': content,
        '_token': csrfToken,
    };

    $.ajax({
        url: appUrl + "/api/generate-kontrak",
        type: "post",
        data: formData,
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            if (response.success) {
                Swal.fire("Success", "Kontrak Berhasil disimpan", "success").then(() => {
                    location.reload();
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
                    xhrFields: {
                        withCredentials: true
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
                                withCredentials: true
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
                                        location.reload();
                                    }
                                });
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
                                withCredentials: true
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
                                        location.reload();
                                    }
                                });
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
                                withCredentials: true
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
                                        location.reload();
                                    }
                                });
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
                                withCredentials: true
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
                                        location.reload();
                                    }
                                });
                            },
                        });
                    }
                }
            });
        }
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
    $("#overlay").show();
    var id_order_shop = $(this).data("id");
    var id_courier = $(this).data("id_courier");
    var resi = $(this).data("resi");

    if (id_courier === 0) {
        $.ajax({
            url: appUrl + "/seller/order/lacak_kurir_sendiri/" + id_order_shop,
            method: "GET",
            xhrFields: {
                withCredentials: true
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
                withCredentials: true
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
    var tableHtml = `
        <table class="table">
            <thead>
                <tr>
                    <th>Track ID</th>
                    <th>Deskripsi</th>
                    <th>Date/Time</th>
                </tr>
            </thead>
            <tbody>
    `;

    if (idCourier === 0 && deliveryStart !== null) {
        tableHtml += `
            <tr>
                <td style="text-align: left;"><span class="fa fa-map-marker"> On Progress </span></td>
                <td style="text-align: left;">Pesanan Dalam Pengiriman</td>
                <td style="text-align: left;">${deliveryStart}</td>
            </tr>
        `;
    } else if (idCourier === 0 && deliveryEnd !== null) {
        tableHtml += `
            <tr>
                <td style="text-align: left;"><span class="fa fa-map-marker"> On Progress </span></td>
                <td style="text-align: left;">Pesanan Dalam Pengiriman</td>
                <td style="text-align: left;">${deliveryStart}</td>
            </tr>
            <tr>
                <td style="text-align: left;"><span class="fa fa-map-marker"> Complete <a target="_blank" href="${filePdfUrl}">detail</a></span></td>
                <td style="text-align: left;">Selesai</td>
                <td style="text-align: left;">${deliveryEnd}</td>
            </tr>
        `;
    }

    tableHtml += `
            </tbody>
        </table>
    `;

    return tableHtml;
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

            // Mengirim data menggunakan AJAX
            $.ajax({
                url: appUrl + "/seller/order/uploadDo",
                method: "POST",
                data: formData,
                xhrFields: {
                    withCredentials: true
                },
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
