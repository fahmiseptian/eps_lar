$(function () {
    $("#example1").dataTable();
    $("#example2").dataTable({
        bPaginate: true,
        bLengthChange: false,
        bFilter: false,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
        responsive: true,
    });
});

function detail(id) {
    // Menampilkan loading spinner
    loading();

    // Mengambil data anggota menggunakan AJAX
    $.ajax({
        url: baseUrl + "/admin/shop/" + id,
        method: "GET",
        success: function (response) {
            var shop = response.shop;
            var member = response.member;
            if (shop) {

                // Menampilkan informasi toko dengan SweetAlert
                Swal.fire({
                    title: "Detail Toko",
                    html: `
                        <table style="width:100%">
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>Nama Toko</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.name || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>Email</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${member.email || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>Password</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.password || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>No Telepon</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.phone || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>Nama Pemilik</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.nama_pemilik || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>No NIK</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.nik_pemilik || ""}</td>
                            </tr>
                            <tr>
                                <td style="width: 30%; text-align: right;"><strong>NPWP</strong></td>
                                <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                                <td style="width: 67%; text-align: left;">${shop.npwp || ""}</td>
                            </tr>
                        </table>
                    `,
                    confirmButtonText: "Tutup",
                });
            } else {
                // Menampilkan pesan jika data anggota tidak ditemukan
                Swal.fire({
                    title: "Detail Toko",
                    text: "Data Toko tidak ditemukan.",
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

// Upadte Status
function updateStatus(id) {
    loading();

    console.log("Shop ID:", id);
    console.log(baseUrl);
    $.ajax({
        url: baseUrl + "admin/shop/" + id + "/update-status",
        type: "GET",
        success: function (response) {
            console.log("Status anggota berhasil diubah");
            location.reload();
        },
        error: function (xhr, status, error) {
            console.error(
                "Terjadi kesalahan saat mengubah status toko:",
                error
            );
            alert("Terjadi kesalahan saat mengubah status Toko.");
        },
    });
}

function deleteShop(id) {
    // Menampilkan pesan konfirmasi SweetAlert
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus toko ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi penghapusan, mengirimkan permintaan AJAX
            $.ajax({
                url: baseUrl + "/admin/shop/" + id + "/delete",
                method: "GET",
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Toko berhasil dihapus.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus toko.',
                        'error'
                    );
                },
            });
        }
    });
}

function updateTypeUp(id) {
    loading();

    console.log("Shop ID:", id);

    $.ajax({
        url: baseUrl + "/admin/shop/" + id + "/update-type-up",
        type: "GET",
        success: function (response) {
            if (response.message === "Teratas") {
                Swal.fire({
                    title: "Peringatan",
                    text: "Tipe toko sudah trusted seller, tidak dapat ditingkatkan lagi.",
                    icon: "warning",
                    confirmButtonText: "Tutup",
                });
            } else {
                console.log("Tipe toko berhasil ditingkatkan");
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error("Terjadi kesalahan saat mengubah tipe toko:", error);
            alert("Terjadi kesalahan saat mengubah tipe Toko.");
        },
    });
}

function updateTypeDown(id) {
    loading();

    console.log("Shop ID:", id);

    $.ajax({
        url: baseUrl + "/admin/shop/" + id + "/update-type-down",
        type: "GET",
        success: function (response) {
            if (response.message === "Terbawah") {
                Swal.fire({
                    title: "Peringatan",
                    text: "Tipe toko sudah silver, tidak dapat diturunkan lagi.",
                    icon: "warning",
                    confirmButtonText: "Tutup",
                });
            } else {
                console.log("Tipe toko berhasil diturunkan");
                location.reload();
            }
        },
        error: function (xhr, status, error) {
            console.error("Terjadi kesalahan saat mengubah tipe toko:", error);
            alert("Terjadi kesalahan saat mengubah tipe Toko.");
        },
    });
}

var toggleIcons = document.querySelectorAll(".is_top");
toggleIcons.forEach(function (icon) {
    icon.addEventListener("click", function () {
        var shopId = this.getAttribute("data-shop-id");
        var isTop = this.getAttribute("data-is-top");
        console.log(shopId);
        // Kirim permintaan AJAX untuk memperbarui nilai is_top
        fetch("/admin/update-is-top/" + shopId, {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                // Mengubah ikon dan data is_top berdasarkan respons
                if (data.is_top == 1) {
                    icon.classList.remove("glyphicon-remove-sign");
                    icon.classList.add("glyphicon-ok-sign");
                    icon.setAttribute("data-is-top", 1);
                } else {
                    icon.classList.remove("glyphicon-ok-sign");
                    icon.classList.add("glyphicon-remove-sign");
                    icon.setAttribute("data-is-top", 0);
                }
            })
            .catch((error) => console.error("Error:", error));
    });
});

document.getElementById("formula-price").addEventListener("click", function () {
    // Menampilkan SweetAlert
    loading();

    $.ajax({
        url: "/admin/formula-lpse",
        method: "GET",
        success: function (response) {
            var formula = response.formula;
            if (formula) {
                Swal.fire({
                    title: "Detail Formula",
                    html: `
        <div style="text-align: justify;">
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                <thead>
                    <tr>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">PPH (%)</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">PPN (%)</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Fee Marketplace (%)</th>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                            <input type="text" class="form-control" id="inputPPHPercent" value="${formula.pph}" required>   
                        </td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                            <input type="text" class="form-control" id="inputPPNPercent" value="${formula.ppn}" required>
                        </td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                            <input type="text" class="form-control" id="inputFeeMPPercent" value="${formula.fee_mp_percent}" required>
                        </td>
                        <th style="padding: 8px; border-bottom: 1px solid #ddd; text-align: center;">
                            <a class="glyphicon glyphicon-saved" id="formula-saved">Simpan </a>
                        </th>
                    </tr>
                </tbody>
            </table>
        </div>
        <hr>
        <div style="margin-top: 20px;">
            <h4>Formulir Perhitungan</h4>
            <form id="calculation-form">
                <div class="form-group">
                    <label for="inputValue">Harga Seller:</label>
                    <input type="text" class="form-control" id="inputValue" placeholder="Rp Masukkan Nominal" required inputmode="numeric" pattern="[0-9]*">
                </div>
                <button type="button" class="btn btn-primary" id="calculate-button">Hitung</button>
            </form>
        </div>
        <div style="margin-top: 20px;" id="calculation-results"></div>
    `,
                    confirmButtonText: "Tutup",
                    width: "50%",
                    // Logika perhitungan saat tombol "Hitung" ditekan
                    didOpen: function () {
                        var originalValue = "";
                        var inputElement =
                            document.getElementById("inputValue");

                        inputElement.addEventListener(
                            "input",
                            function (event) {
                                var value = event.target.value.replace(
                                    /\D/g,
                                    ""
                                );
                                var formattedValue = value.replace(
                                    /\B(?=(\d{3})+(?!\d))/g,
                                    ","
                                );
                                event.target.value = formattedValue;
                                originalValue = value;
                            }
                        );

                        // Tambahkan event listener untuk tombol Simpan
                        $(document).on("click", "#formula-saved", function () {
                            var $row = $(this).closest("tr");
                            var newData = {};
                            $row.find("td:not(:last-child)").each(function (
                                index
                            ) {
                                var key = ["pph", "ppn", "fee_mp_percent"][
                                    index
                                ];
                                newData[key] = $(this)
                                    .find("input")
                                    .val()
                                    .trim();
                            });
                            var csrfToken = document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content");
                            $.ajax({
                                url: "/admin/update-formula",
                                method: "POST",
                                data: { ...newData, _token: csrfToken },
                                success: function (response) {
                                    // Tampilkan pesan sukses atau gagal
                                    Swal.fire({
                                        icon: "success",
                                        title: "Your work has been saved",
                                        showConfirmButton: false,
                                        timer: 1500
                                      });

                                    // Tampilkan kembali input fields dengan nilai yang baru disimpan
                                    $row.find("td:not(:last-child)").each(
                                        function (index) {
                                            var key = [
                                                "pph",
                                                "ppn",
                                                "fee_mp_percent",
                                            ][index];
                                            $(this).html(
                                                '<input type="text" class="form-control" value="' +
                                                    newData[key] +
                                                    '">'
                                            );
                                        }
                                    );
                                },
                                error: function (xhr, status, error) {
                                    // Tampilkan pesan kesalahan jika terjadi kesalahan saat mengirim permintaan
                                    alert("Terjadi kesalahan: " + error);
                                },
                            });
                        });

                        document.getElementById("calculate-button").onclick =
                            function () {
                                // Gunakan nilai asli untuk perhitungan
                                calculate(formula, originalValue);
                            };
                    },
                });
            } else {
                // Menampilkan pesan jika data anggota tidak ditemukan
                Swal.fire({
                    title: "Detail Formula",
                    text: "Formula Price Eror.",
                    icon: "error",
                    confirmButtonText: "Tutup",
                });
            }
        },
        error: function (xhr, status, error) {
            // Menampilkan pesan kesalahan
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Terjadi kesalahan saat memuat detail formula.",
                icon: "error",
                confirmButtonText: "Tutup",
            });
        },
    });
});

function calculate(formula, originalValue) {
    $.ajax({
        url: "/admin/formula-lpse",
        method: "GET",
        success: function (response) {
            var formula = response.formula;

            // Menggunakan nilai asli yang disimpan
            var inputValue = parseFloat(originalValue);

            if (!isNaN(inputValue)) {
                // Rumus
                var feeMarketplace = Math.round(
                    inputValue *
                        (formula.fee_mp_percent /
                            (100 - formula.fee_mp_percent))
                );
                var hargaDasarLPSE =
                    Math.ceil(
                        ((inputValue + feeMarketplace) * 100) /
                            (100 - formula.pph) /
                            1000
                    ) * 1000;
                var ppn = hargaDasarLPSE * (formula.ppn / 100);
                var hargaTayang = hargaDasarLPSE + ppn;

                var formattedInputValue = inputValue.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                });
                var formattedFeeMarketplace = feeMarketplace.toLocaleString(
                    "id-ID",
                    {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0,
                    }
                );
                var formattedHargaDasarLPSE = hargaDasarLPSE.toLocaleString(
                    "id-ID",
                    {
                        style: "currency",
                        currency: "IDR",
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0,
                    }
                );
                var formattedPPN = ppn.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                });
                var formattedHargaTayang = hargaTayang.toLocaleString("id-ID", {
                    style: "currency",
                    currency: "IDR",
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0,
                });

                document.getElementById("calculation-results").innerHTML = `
            <h4>Hasil Perhitungan:</h4>

            <div style="text-align: justify;">
                            <table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">
                                <thead>
                                    <tr>
                                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Harga Seller</th>
                                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Fee Marketplace</th>
                                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Harga Dasar LPSE</th>
                                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">PPN</th>
                                        <th style="padding: 8px; border-bottom: 1px solid #ddd;">Harga Tayang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">${formattedInputValue}</td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">${formattedFeeMarketplace}</td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">${formattedHargaDasarLPSE}</td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">${formattedPPN}</td>
                                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">${formattedHargaTayang}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
        `;
            } else {
                document.getElementById("calculation-results").innerHTML =
                    "Masukkan nilai yang valid.";
            }
        },
    });
}

function detailProduct(id) {
    loading();

    console.log("Shop ID:", id);
    $.ajax({
        url: "/admin/shop/" + id + "/product/",
        method: "GET",
        success: function (response) {
            var products = response.products;
            var htmlContent = '<table class="table">';
            htmlContent +=
                '<thead><tr><th rowspan="2">Nama</th><th rowspan="2">Tanggal Update</th><th rowspan="2">Stok</th><td align="center" colspan="2"> <b> Action </b> </td></tr>';
            htmlContent += "<tbody>";
            products.forEach(function (product) {
                htmlContent +=
                    "<tr>" +
                    '<td align="left">' +
                    product.name +
                    "</td>" +
                    '<td align="left">' +
                    product.last_update +
                    "</td>" +
                    '<td align="left">' +
                    product.stock +
                    "</td>" +
                    '<td align="center"> <a class="glyphicon ' +
                    (product.status_lpse == 1
                        ? "glyphicon-eye-open"
                        : "glyphicon-eye-close") +
                    '" id="update-status" data-product-id="' +
                    product.id +
                    '"data-product-status="' +
                    product.status_lpse +
                    '"></a></td>' +
                    '<td align="center"> <a class="glyphicon glyphicon-log-in" id="review-product"></a></td>' +
                    "</tr>";
            });
            htmlContent += "</tbody></table>";

            Swal.fire({
                title: "Detail Produk",
                html: htmlContent,
                showConfirmButton: true,
                allowOutsideClick: true,
                width: "50%",
                didOpen: function () {
                }
            });
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Produk Toko Ini tidak ada",
            });
        },
    });
}


function loading() {
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });
}
