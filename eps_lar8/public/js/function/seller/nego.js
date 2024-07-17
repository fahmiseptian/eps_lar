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

$(document).ready(function() {
    $(".horizontal-list li").click(function() {
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

$(document).on("click", "#change-nego", function () {
    var kondisi = $(this).data("kondisi");
    $.ajax({
        url: appUrl + "/api/seller/nego/" + kondisi,
        type: "get",
        xhrFields: {
            withCredentials: true
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
            withCredentials: true
        },
        success: function (response) {
            var tbody = $(".box-body");
            var list_nego = $(".horizontal-list");
            tbody.empty();
            list_nego.empty();

            var data = response.nego;
            var statusText;
            switch (data.nego_status) {
                case 0:
                    statusText = "Dalam Proses";
                    break;
                case 1:
                    statusText = "Disetujui";
                    break;
                case 2:
                    statusText = "Ditolak";
                    break;
                default:
                    statusText = "Status Tidak Dikenali";
            }

            // Menambahkan informasi detail negosiasi
            var detailHtml = `
            <div class="box-body">
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title"> <i id="google-icon" class="material-icons">arrow_back</i> Detail Nego</h3>
                    </div>
                    <div class="box-body">
                        <div id="data-detail-nego">
                            <p style="text-align: left;">${data.nama_pembeli}</p>
                            <p style="text-align: right;">${statusText}</p>
                        </div>
                        <div id="data-detail-nego">
                            <p style="text-align: left;">${data.instansi ? data.instansi : "-"}</p>
                            <p style="text-align: right;">${data.created_date}</p>
                        </div>
                    </div>
                </div>
            </div>
            `;

            tbody.append(detailHtml);

            // Menambahkan tabel produk negosiasi
            var produkHtml = `
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Produk Nego</h3>
                </div>
                <div class="box-body no-padding">
                    <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th class="detail-full" style="width: 100px">Gambar Product</th>
                                <th>Nama</th>
                                <th>Qty</th>
                                <th>Harga Diterima Seller</th>
                                <th>Harga Invoice User</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="detail-full"><img style="width: 50px; height:50px;" src="${data.dataProduct.artwork_url_md[0]}" alt="product"></td>
                                <td>${data.dataProduct.name}</td>
                                <td>${data.qty}</td>
                                <td>${formatRupiah(data.harga_didapat_terbaru)}</td>
                                <td>${formatRupiah(data.harga_nego_terbaru)}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            `;

            tbody.append(produkHtml);

            // Menambahkan tabel riwayat negosiasi
            var riwayatHtml = `
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">Nego</h3>
                </div>
                <div class="box-body">
                    <table id="data-nego" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Oleh</th>
                                <th>Harga yang Di terima Seller</th>
                                <th>Harga Nego User</th>
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
                                <td> - </td>
                                <td> - </td>
                                <td> - </td>
                            </tr>
                            <!-- Daftar riwayat negosiasi -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Tanggal</th>
                                <th>Oleh</th>
                                <th>Harga yang Di terima Seller</th>
                                <th>Harga Nego User</th>
                                <th>Status</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            `;

            tbody.append(riwayatHtml);

            // Memasukkan riwayat negosiasi ke dalam tabel
            data.dataNego.forEach(function (nego) {
                var sendByText = nego.send_by === 0 ? "User" : "Seller";
                var statusnego;
                var aksi;
                switch (nego.status) {
                    case 0:
                        statusnego = "Dalam Proses";
                        break;
                    case 1:
                        statusnego = "Disetujui";
                        break;
                    case 2:
                        statusnego = "Ditolak";
                        break;
                    default:
                        statusnego = "Status Tidak Dikenali";
                }

                if (sendByText == "User" && statusnego == "Dalam Proses") {
                    aksi = `
                        <a class="btn-app" href="" data-text="Setujui" data-id="${nego.id_nego}">
                            <i class="material-icons">check_circle</i>
                        </a>
                        <a class="btn-app" href="" data-text="Nego Ulang" data-toggle="modal" data-target="#negoUlangModal" data-base_price="${nego.base_price}" data-total="${nego.harga_nego}" data-qty="${nego.qty}" data-product="${data.dataProduct.id}" data-id="${nego.id_nego}" data-last_id="${nego.id}" >
                            <i class="material-icons">autorenew</i>
                        </a>
                        <a class="btn-app" href="" data-text="Tolak" data-id="${nego.id_nego}">
                            <i class="material-icons">cancel</i>
                        </a>
                    `;
                } else {
                    aksi = `-`;
                }
                var row = `
                <tr>
                    <td>${nego.timestamp}</td>
                    <td>${sendByText}</td>
                    <td>${formatRupiah(nego.nominal_didapat/nego.qty)}</td>
                    <td>${formatRupiah(nego.base_price)}</td>
                    <td>${statusnego}</td>
                    <td>${nego.catatan_penjual ? nego.catatan_penjual : (nego.catatan_pembeli ? nego.catatan_pembeli : '-')}</td>
                    <td>${aksi}</td>
                </tr>
                `;
                $("#data-nego tbody").append(row);
            });

            // Menambahkan modal ke dalam body
            var modalHtml = `
            <div id="negoUlangModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title">Nego Ulang</h4>
                        </div>
                        <div class="modal-body">
                            <form id="negoUlangForm">
                                <div class="form-group">
                                    <label for="negoPrice">Harga Baru</label>
                                    <input type="number" class="form-control" id="negoPrice" name="negoPrice" required>
                                </div>
                                <div class="form-group">
                                    <label for="negoNote">Catatan</label>
                                    <textarea class="form-control" id="negoNote" name="negoNote" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            `;
            $('body').append(modalHtml);

            // call Modal
            $(document).on('click', '.btn-app[data-text="Nego Ulang"]', function(event) {
                event.preventDefault();
                var hargaNegoSatuan = $(this).data('base_price');
                var hargaNegoTotal = $(this).data('total');
                var qty = $(this).data('qty');
                var product = $(this).data('product');
                var id_nego = $(this).data('id');
                var last_id = $(this).data('last_id');


                $('#hargaSatuan').val(formatRupiah(hargaNegoSatuan));
                $('#hargaNegoTotal').val(formatRupiah(hargaNegoTotal));
                $('#qty').val(qty);
                $('#product').val(product);
                $('#id_nego').val(id_nego);
                $('#last_id').val(last_id);

                $('#negoUlangModal').modal('show');
            });

            $(document).on('click', '.btn-app[data-text="Setujui"]', function(event) {
                event.preventDefault();
                var id_nego = $(this).data('id');
                $("#overlay").show();

                swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Anda akan menyetujui negosiasi harga ini.',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: appUrl + '/api/seller/nego/acc_nego',
                            type: 'POST',
                            data: {
                                id_nego : id_nego,
                            },
                            xhrFields: {
                                withCredentials: true
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            },
                            complete: function () {
                                $("#overlay").hide();
                            },
                        });
                    }
                });
            });

            $(document).on('click', '.btn-app[data-text="Tolak"]', function(event) {
                event.preventDefault();
                var id_nego = $(this).data('id');
                $("#overlay").show();

                swal.fire({
                    title: 'Apakah kamu yakin?',
                    text: 'Anda akan menolak negosiasi ini.',
                    icon: 'warning',
                    input: 'text',
                    inputPlaceholder: 'Masukkan alasan penolakan',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tolak!',
                    cancelButtonText: 'Batal',
                    preConfirm: (alasan) => {
                        return new Promise((resolve) => {
                            resolve(alasan || ''); // Resolves with the alasan value or an empty string
                        });
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var alasanPenolakan = result.value;
                        $.ajax({
                            url: appUrl + '/api/seller/nego/tolak_nego',
                            type: 'POST',
                            data: {
                                id_nego: id_nego,
                                alasan: alasanPenolakan
                            },
                            xhrFields: {
                                withCredentials: true
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            },
                            complete: function () {
                                $("#overlay").hide();
                            },
                        });
                    }
                });
            });
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

$(document).ready(function() {
    var typingTimer;                // Timer identifier
    var doneTypingInterval = 1500;  // Time in ms (1.5 seconds)
    var $input = $('#hargaResponSatuan');
    var $qty    = $('#qty');
    var $product    = $('#product');

    $input.on('input', function() {
        clearTimeout(typingTimer);
        if ($input.val()) {
            $('#hargaResponSatuan').val(formatRupiah($input.val()));
            typingTimer = setTimeout(doneTyping, doneTypingInterval);
        }
    });

    function doneTyping() {
        var hargaResponSatuan   = unformatRupiah($input.val());
        var qty                 = parseInt($qty.val()) || 0;
        var total               = hargaResponSatuan * qty ;
        var id_product          = parseInt($product.val()) || 0;

        $('#hargaResponTotal').val(formatRupiah(total));

        $.ajax({
            url: appUrl + '/api/seller/calcNego',
            type: 'POST',
            data: {
                qty:qty,
                id_product : id_product,
                hargaResponSatuan: hargaResponSatuan
            },
            xhrFields: {
                withCredentials: true
            },
            success: function(response) {
                $('#hargaDiterimaSatuan').val(formatRupiah(response.hargaSatuanDiterimaSeller));
                $('#hargaDiterimaTotal').val(formatRupiah(response.hargaTotalDiterimaSeller));
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }
});

// Call Modal Nego Ulang
$(document).ready(function() {
    $('#negoUlangForm').on('submit', function(event) {
        event.preventDefault();
        $("#overlay").show();

        // Ambil nilai asli dari input hargaSatuan dan hargaResponSatuan
        var hargaSatuanVal = unformatRupiah($('#hargaSatuan').val());
        var hargaDiterimaSatuanVal = unformatRupiah($('#hargaDiterimaSatuan').val());
        var hargaResponSatuanVal = unformatRupiah($('#hargaResponSatuan').val());

        // Set nilai asli kembali ke input
        $('#hargaSatuan').val(hargaSatuanVal);
        $('#hargaDiterimaSatuan').val(hargaDiterimaSatuanVal);
        $('#hargaResponSatuan').val(hargaResponSatuanVal);

        // Serialize form setelah nilai diubah
        var formData = $(this).serialize();
        console.log(formData);

        // Kirim AJAX request
        $.ajax({
            url: appUrl + "/api/seller/nego/add_respon",
            type: "POST",
            data: formData,
            xhrFields: {
                withCredentials: true
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error(error);
                // Handle error jika terjadi
            },
            complete: function() {
                $("#overlay").hide();
            }
        });
    });
});
