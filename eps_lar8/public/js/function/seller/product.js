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

function toggleFilterProduct(element) {
    var status = element.getAttribute("data-status");
    $("#overlay").show();
    $.ajax({
        type: "GET",
        url: appUrl + "/seller/product/" + status,
        xhrFields: {
            withCredentials: true,
        },
        success: function (data) {
            // console.log("berhasil ");
            // window.location.href = appUrl + "/seller/product/" + status;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

$("#kategori-level1").change(function () {
    var level1Value = $(this).val();
    if (level1Value !== "") {
        $.ajax({
            url: appUrl + "/seller/product/category/level2/" + level1Value,
            type: "GET",
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                $("#kategori-level2")
                    .empty()
                    .append('<option value="">Pilih Kategori Level 2</option>');
                $.each(response, function (key, value) {
                    $("#kategori-level2").append(
                        '<option value="' +
                            value.id +
                            '">' +
                            value.name +
                            "</option>"
                    );
                });
                $("#kategorilevel2").show();
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
        });
    } else {
        $("#kategorilevel2").hide();
    }
});

$("#kategori-level2").change(function () {
    var kategori = $(this).val();
    $.ajax({
        url: appUrl + "/api/seller/DetailCategory/" + kategori,
        type: "GET",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            $("#phpVariables").data("ppn", response.ppn);
            $("#phpVariables").data("pph", response.pph);
            $("#phpVariables").data("mp-percent", response["mp-percent"]);
        },
        error: function (xhr, status, error) {
            console.error(xhr.responseText);
        },
    });
});

$(document).ready(function () {
    $("#addProduct").submit(function (event) {
        $("#overlay").show();
        var form = $(this);
        var addUrl = form.attr("action");
        event.preventDefault();

        // Memeriksa jumlah file yang diunggah
        var fileInputs = $('input[name="images[]"]');
        var fileCount = 0;
        fileInputs.each(function () {
            if ($(this)[0].files.length > 0) {
                fileCount++;
            }
        });

        if (fileCount < 1 || fileCount > 5) {
            alert("Please upload at least one image and maximum 5 images.");
            return false;
        }

        var formData = new FormData(form[0]);
        $.ajax({
            url: addUrl,
            type: "POST",
            data: formData,
            xhrFields: {
                withCredentials: true,
            },
            processData: false, // Set processData ke false
            contentType: false, // Set contentType ke false
            success: function (response) {
                var targetUrl = appUrl + "/seller/product/";
                window.open(targetUrl);
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    });
});

$(document).ready(function () {
    // Function to calculate price based on harga input
    $("#harga").on("input", function () {
        var hargaAwal = parseFloat($(this).val());
        var ppn = parseFloat($("#phpVariables").data("ppn"));
        var pph = parseFloat($("#phpVariables").data("pph"));
        var mpPercent = parseFloat($("#phpVariables").data("mp-percent"));

        // Calculate
        var mp = Math.round(hargaAwal * (mpPercent / (100 - mpPercent)));
        // var hargaDasar = Math.ceil(((hargaAwal + mp) * 100) / (100 - pph) / 1000) * 1000;
        var hargaDasar =
            Math.ceil(((hargaAwal + mp) * 100) / (100 - pph) / 1000) * 1000;
        var biayaPpn = hargaDasar * (ppn / 100);
        var hargaTayang = hargaDasar + biayaPpn;

        // Set the calculated values to corresponding input fields

        $("#ppn").val(biayaPpn);
        $("#hargaSudahPPn").val(hargaTayang);
    });
});

$(document).on("click", "#review_product", function () {
    var id = $(this).data("id");
    var review_productUrl = appUrl + "/product/" + id;
    window.open(review_productUrl, "_blank");
});

$(document).on("click", "#edit_product", function () {
    var id = $(this).data("id");
    $("#overlay").show();
    $.ajax({
        type: "GET",
        url: appUrl + "/seller/product/edit/" + id,
        xhrFields: {
            withCredentials: true,
        },
        success: function (data) {
            $("#example2").DataTable().destroy();
            var tbody = $("#table-content");
            tbody.empty();

            var form = htmlFormProduk();
            tbody.append(form);

            var $satuan = $("#satuan");
            $satuan.empty();

            var $jenis_produk = $("#jenis_produk");
            $jenis_produk.empty();

            var tombol = $(".button-container");
            tombol.empty();

            var product = data.produk;
            DropdataProduk(product);

            $.each(data.satuanProduk, function (index, item) {
                var $option = $("<option></option>")
                    .val(item.id)
                    .text(item.satuan);
                if (item.id == product.id_satuan) {
                    $option.prop("selected", true);
                }
                $satuan.append($option);
            });

            $.each(data.jenisProduk, function (index, item) {
                var $option = $("<option></option>")
                    .val(item.id)
                    .text(item.name);
                if (item.id == product.id_satuan) {
                    $option.prop("selected", true);
                }
                $jenis_produk.append($option);
            });

            // Add a default "pilih jenis produk" option at the top
            $jenis_produk.prepend(
                $("<option>pilih jenis produk</option>")
                    .attr("disabled", true)
                    .attr("selected", true)
            );

            $("#jenis_produk").val(data.produk.id_tipe);

            new_tombol = `
                <button class="cancel-button" onclick="initialize()">Batal</button>
                <div class="button-group">
                    <button data-id="${data.produk.id}" id="update-archive">Update & Arsipkan</button>
                    <button data-id="${data.produk.id}" id="update-publish">Update & Tayangkan</button>
                </div>
            `;

            tombol.append(new_tombol);

            activateEventListeners();
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

function DropdataProduk(produk) {
    $("#nama").val(produk.name);
    $("#getbrands").empty();
    $("#getbrands").data("id", produk.id_brand);
    $("#getbrands").text(produk.brand_name);
    $("#spesifikasi").val(produk.spesifikasi);
    $("#harga").val(formatRupiah(produk.price));
    $("#stok").val(produk.stock);
    $("#berat").val(produk.weight);
    $("#pangjang").val(produk.dimension_length);
    $("#lebar").val(produk.dimension_width);
    $("#tinggi").val(produk.dimension_high);
    $("#sku").val(produk.sku);
    $("#kondisi").val(produk.status_new_product);

    // var lv1CategoryName = $("#txt-ket-lv1").text(produk.name_lvl2);
    // var lv2CategoryName = $("#txt-ket-lv2").text(produk.name_lvl3);

    // Pisahkan URL gambar menjadi array
    const imageUrls = produk.images.split(",");
    const vidio = produk.link;
    console.log(vidio);
    if (vidio !== null) {
        const videoHTML = `
        <video controls src="${vidio}" style="width: 100%; height: 100%; border-radius: 20px;"></video>
        <input type="hidden" name="old_vidio" value="${vidio}">
        <i class="material-icons trash-icon" onclick="removeVideo(this)">delete</i>
        `;

        // Tambahkan video ke dalam elemen menggunakan jQuery
        $(".vidio-produk-add").html(videoHTML);
    }

    // Tampilkan gambar
    displayImages(imageUrls);

    if (produk.is_pdn == 1) {
        $("#pdn-yes").prop("checked", true);
    } else {
        $("#pdn-no").prop("checked", true);
    }

    if (produk.status_preorder == "Y") {
        $("#preorder-yes").prop("checked", true);
    } else {
        $("#preorder-no").prop("checked", true);
    }

    var $tayangNPPn = $("#tayangNPPn");
    var $tayangYPPn = $("#tayangYPPn");
    var $PPn = $("#PPn");

    $("#kategori")
        .text(produk.name_lvl2 + " -> " + produk.name_lvl3)
        .attr("data-idlv1", produk.id_lvl2)
        .attr("data-idlv2", produk.id_category)
        .prepend(
            '<i class="material-symbols-outlined" style="font-size: 23px" id="google-icon"> ink_pen </i> '
        );

    if (produk.id_category) {
        $("#harga").prop("disabled", false);
    }

    $.ajax({
        url: appUrl + "/api/seller/calcHarga",
        method: "POST",
        data: {
            harga: produk.price,
            id_kategori: produk.id_category,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            $tayangNPPn.val(formatRupiah(response.harga_tayang_belum_ppn));
            $tayangYPPn.val(formatRupiah(response.harga_tayang));
            $PPn.val(formatRupiah(response.ppn_price));
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Kesalahan",
            });
        },
    });
}
$(document).on("click", "#deleteProduct", function () {
    var id = $(this).data("id");

    // Show confirmation dialog first
    Swal.fire({
        title: "Konfirmasi Hapus",
        text: "Apakah Anda yakin ingin menghapus produk ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $("#overlay").show();
            $.ajax({
                url: appUrl + "/api/seller/deleteProduct",
                method: "POST",
                data: {
                    id_product: id,
                },
                xhrFields: {
                    withCredentials: true,
                },
                success: function (response) {
                    Swal.fire({
                        title: "Berhasil",
                        text: "Product berhasil dihapus.",
                        icon: "success",
                        confirmButtonText: "OK",
                    }).then(() => {
                        // loadData('semua')
                        initialize();
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
                    $("#overlay").hide();
                },
            });
        }
    });
});

$(document).on("click", "#editStatus", function () {
    var id = $(this).data("id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/editStatusProduct",
        method: "POST",
        data: {
            id_product: id,
        },
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            Swal.fire({
                title: "Berhasil",
                text: "Berhasil Merubah Status Product.",
                icon: "success",
                confirmButtonText: "OK",
            }).then(() => {
                // loadData('semua')
                initialize();
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
            $("#overlay").hide();
        },
    });
});

$(document).ready(function () {
    $('input[type="file"]').change(function () {
        var file = this.files[0];
        var reader = new FileReader();
        var parentDiv = $(this).closest("div");

        reader.onload = function (e) {
            parentDiv.find("img").attr("src", e.target.result);
        };

        reader.readAsDataURL(file);
    });
});

var allItems = $(".item-box-filter-pesanan");
var activeItem;

function loadData(tipe) {
    $("#overlay").show();

    if (tipe == "addProduk") {
        $("#example2").DataTable().destroy();
        var tbody = $("#table-content");
        tbody.empty();

        $.ajax({
            url: appUrl + "/api/seller/product/satuan",
            method: "GET",
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                var form = htmlFormProduk();
                tbody.append(form);

                var $satuan = $("#satuan");
                $satuan.empty();

                var $jenis_produk = $("#jenis_produk");
                $jenis_produk.empty();

                $.each(response.satuanProduk, function (index, item) {
                    var $option = $("<option></option>")
                        .val(item.id)
                        .text(item.satuan);
                    $satuan.append($option);
                });

                $.each(response.jenisProduk, function (index, item) {
                    var $option = $("<option></option>")
                        .val(item.id)
                        .text(item.name);
                    $jenis_produk.append($option);
                });

                $satuan.prepend(
                    $("<option>Pilih Satuan Produk</option>")
                        .attr("disabled", true)
                        .attr("selected", true)
                );

                $jenis_produk.prepend(
                    $("<option>Pilih Jenis Produk</option>")
                        .attr("disabled", true)
                        .attr("selected", true)
                );

                activateEventListeners();
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Kesalahan",
                });
            },
            complete: function () {
                $("#overlay").hide(); // Sembunyikan loader setelah selesai
            },
        });
    } else {
        $.ajax({
            type: "GET",
            url: appUrl + "/seller/product/" + tipe,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                const imgReview = appUrl + "/img/app/riview-produk.png";
                const imgedit = appUrl + "/img/app/edit-produk.svg";
                const imgdelete = appUrl + "/img/app/sampah.png";
                const onAir = appUrl + "/img/app/on-air.png";
                const offAir = appUrl + "/img/app/OFF-AIR.png";

                var body = $("#table-content");
                body.empty();

                var table = `
                        <table id="example2" class="table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th class="detail-full">Nama</th>
                                        <th class="detail-full">SKU</th>
                                        <th class="detail-full">Harga Seller</th>
                                        <th class="detail-full">Harga Tayang</th>
                                        <th class="detail-full">Stok</th>
                                        <th>Status</th>
                                        <th style="width: 290px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot hidden>
                                    <tr>
                                        <th>No</th>
                                        <th class="detail-full">Nama</th>
                                        <th class="detail-full">SKU</th>
                                        <th class="detail-full">Harga Seller</th>
                                        <th class="detail-full">Harga Tayang</th>
                                        <th class="detail-full">Stok</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
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
                    var rows = response.map((produk) => {
                        // Menentukan badge berdasarkan status
                        let statusBadge;
                        if (produk.status_display === "Y") {
                            statusBadge = `<img id="editStatus" data-id="${produk.id}" style="width: 60px;" src="${onAir}" alt="Tayang">`;
                        } else {
                            statusBadge = `<img id="editStatus" data-id="${produk.id}" style="width: 60px;" src="${offAir}" alt="Tayang">`;
                        }

                        return [
                            counter++,
                            produk.name,
                            produk.sku,
                            formatRupiah(produk.price),
                            formatRupiah(produk.price_tayang),
                            produk.stock,
                            statusBadge, // Menambahkan badge status ke dalam tabel
                            `
                                <td>
                                    <div style="display: flex;">
                                        <div style="margin-right:15px; text-align: center;" id="review_product" data-id="${produk.id}">
                                            <img style="width: 50px;" src="${imgReview}" alt="Review Product">
                                            <br><span style="font-size: 12px;"> Lihat Produk </span>
                                        </div>
                                        <div style="margin-right:15px; text-align: center;" id="edit_product" data-id="${produk.id}" >
                                            <img style="width: 50px;" src="${imgedit}" alt="Edit Product">
                                            <br><span style="font-size: 12px;"> Edit Produk </span>
                                        </div>
                                        <div style="text-align: center;" id="deleteProduct" data-id="${produk.id}">
                                            <img style="width: 40px; height:40px" src="${imgdelete}" alt="Delete Product">
                                            <br><span style="font-size: 12px;"> Hapus Produk </span>
                                        </div>
                                    </div>
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
            },
            error: function (xhr, status, error) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function () {
                $("#overlay").hide(); // Sembunyikan loader setelah selesai
            },
        });
    }
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
    loadData("semua");
    setupEvents();
}

function activateEventListeners() {
    var typingTimer; // Timer identifier
    var doneTypingInterval = 1500; // Time in ms (1.5 seconds)
    var $harga = $("#harga");
    var $tayangNPPn = $("#tayangNPPn");
    var $tayangYPPn = $("#tayangYPPn");
    var $PPn = $("#PPn");

    // upload Image
    let uploadedFiles = {};

    $(".foto-produk-add").click(function () {
        const $this = $(this); // Reference to the clicked element
        const elementId = $this.attr("id"); // Get the element's ID

        // Clear the file input before reusing it
        $("#file-input").val("");

        // Open file dialog when .foto-produk-add is clicked
        $("#file-input")
            .off("change")
            .on("change", function (event) {
                const file = event.target.files[0]; // Get the selected file

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $this.empty(); // Clear the element's content
                        $this.append(
                            `<img src="${e.target.result}" width="70px" height="70px" alt="Uploaded Image">`
                        );
                    };
                    reader.readAsDataURL(file); // Read the image file
                    uploadedFiles[elementId] = file;
                }
            })
            .trigger("click"); // Trigger the file input click
    });

    // upload Video
    $(".vidio-produk-add").click(function () {
        const $this = $(this); // Element yang diklik

        $("#vidio-input")
            .off("change")
            .on("change", function (event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $this.html(
                            `<video controls src="${e.target.result}" style="width: 100%; height: 100%; border-radius: 20px;"></video>`
                        );
                    };
                    reader.readAsDataURL(file); // Read the video file as Data URL
                }
            })
            .trigger("click"); // Trigger the file input click
    });

    // get Brands
    $("#getbrands").click(function () {
        $("#overlay").show();
        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/product/brands",
            xhrFields: {
                withCredentials: true,
            },
            success: function (data) {
                let brandOptions = data.map((brand) => ({
                    text: brand.name,
                    value: brand.id, // Menggunakan ID sebagai value untuk memastikan ID tersedia di result
                }));

                // Menyiapkan opsi input untuk SweetAlert2
                const inputOptions = brandOptions.reduce((obj, brand) => {
                    obj[brand.value] = brand.text;
                    return obj;
                }, {});

                Swal.fire({
                    title: "Pilih Merek",
                    input: "select",
                    inputOptions: inputOptions,
                    inputPlaceholder: "Pilih sebuah merek",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        const selectedBrandId = result.value;
                        const selectedBrandText = inputOptions[selectedBrandId];

                        var text = $("#getbrands");
                        text.empty();
                        text.text(selectedBrandText);
                        text.data("id", selectedBrandId);
                    }
                });
            },
            error: function (xhr, status, error) {
                console.error("Gagal:", error);
            },
            complete: function () {
                $("#overlay").hide(); // Hide the overlay
            },
        });
    });

    // get kategori
    $(".getkategori").click(function () {
        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/product/getCategorylv1",
            xhrFields: {
                withCredentials: true,
            },
            success: function (data) {
                var categoryListLv1 = $("#lv1");
                categoryListLv1.empty(); // Clear previous data

                data.forEach(function (category) {
                    var listItem = $("<li>").text(category.name); // Adjust based on actual data structure
                    listItem.click(function () {
                        $("#txt-ket-lv1")
                            .text(category.name)
                            .attr("data-idlv1", category.id);
                        loadLv2Categories(category.id); // Function to load level 2 categories
                    });
                    categoryListLv1.append(listItem);
                });

                $("#kategoriProduk").modal("show");
            },
            error: function (xhr, status, error) {
                console.error("Gagal:", error);
            },
        });
    });

    // Load level 2 categories when a level 1 category is clicked
    function loadLv2Categories(lv1CategoryId) {
        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/product/getCategorylv2/" + lv1CategoryId,
            xhrFields: {
                withCredentials: true,
            },
            success: function (data) {
                var categoryListLv2 = $("#lv2");
                categoryListLv2.empty(); // Clear previous data

                data.forEach(function (category) {
                    var listItem = $("<li>").text(category.name); // Adjust based on actual data structure
                    listItem.click(function () {
                        $("#txt-ket-lv2")
                            .text(category.name)
                            .attr("data-idlv2", category.id);
                    });
                    categoryListLv2.append(listItem);
                });
            },
            error: function (xhr, status, error) {
                console.error("Gagal:", error);
            },
        });
    }

    $("#jenis_produk").on("change", function () {
        var lv2CategoryId = $("#txt-ket-lv2").attr("data-idlv2");
        if (lv2CategoryId !== "" && lv2CategoryId !== null) {
            $harga.prop("disabled", false);
        }
    });

    // Save the selected categories when the "Simpan" button is clicked
    $("#saveKategori").click(function () {
        var lv1CategoryId = $("#txt-ket-lv1").attr("data-idlv1");
        var lv2CategoryId = $("#txt-ket-lv2").attr("data-idlv2");
        var lv1CategoryName = $("#txt-ket-lv1").text();
        var lv2CategoryName = $("#txt-ket-lv2").text();

        // Update the text and data attributes of the kategori element
        $("#kategori")
            .text(lv1CategoryName + " -> " + lv2CategoryName)
            .attr("data-idlv1", lv1CategoryId)
            .attr("data-idlv2", lv2CategoryId)
            .prepend(
                '<i class="material-symbols-outlined" style="font-size: 23px" id="google-icon"> ink_pen </i> '
            );

        if (lv2CategoryId) {
            var jenisProdukVal = $("#jenis_produk").val();
            if (jenisProdukVal !== "" && jenisProdukVal !== null) {
                $harga.prop("disabled", false);
            }
        }

        $harga.val("");
        $tayangNPPn.val(0);
        $tayangYPPn.val(0);
        $PPn.val(0);
        // Close the modal after saving
        $("#kategoriProduk").modal("hide");
    });

    // perhitungan
    $harga.on("input", function () {
        $tayangNPPn.val("menghitung...");
        $PPn.val("menghitung...");
        $tayangYPPn.val("menghitung...");
        clearTimeout(typingTimer);
        if ($harga.val()) {
            $harga.val(formatRupiah($harga.val()));
            typingTimer = setTimeout(calcHargaTayang, doneTypingInterval);
        }
    });

    // Calculate harga tayang
    function calcHargaTayang() {
        var harga = unformatRupiah($("#harga").val());
        var idcategori = $("#kategori").attr("data-idlv2");
        var idtipeProduk = $("#jenis_produk").val();

        // Validation logic with SweetAlert2
        if (!idcategori) {
            Swal.fire({
                icon: "warning",
                title: "Kategori Level 2 Belum Dipilih",
                text: "Silakan pilih kategori level 2 sebelum menyimpan.",
            });
            return; // Exit the function if idcategori is not set
        }

        if (harga < 500) {
            Swal.fire({
                icon: "warning",
                title: "Harga Kurang dari 500",
                text: "Harga harus minimal 500.",
            });
            return; // Exit the function if harga is less than 500
        }

        if (harga > 200000000) {
            Swal.fire({
                icon: "warning",
                title: "Harga tidak diperbolehkan",
                text: "Harga maksimal 200.000.000.",
            });
            return; // Exit the function if harga is more than 200,000,000
        }

        $.ajax({
            url: appUrl + "/api/seller/calcHarga",
            method: "POST",
            data: {
                harga: harga,
                id_kategori: idcategori,
                idtipeProduk: idtipeProduk,
            },
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                $tayangNPPn.val(formatRupiah(response.harga_tayang_belum_ppn));
                $tayangYPPn.val(formatRupiah(response.harga_tayang));
                $PPn.val(formatRupiah(response.ppn_price));
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Kesalahan",
                });
            },
        });
    }

    // Handle button clicks
    $(document).on("click", "#save-archive", function () {
        handleSave("N");
    });

    $(document).on("click", "#save-publish", function () {
        handleSave("Y");
    });

    $(document)
        .off("click", "#update-archive")
        .on("click", "#update-archive", function () {
            var id = $(this).data("id");
            handleUpdate("N", id);
        });

    $(document)
        .off("click", "#update-publish")
        .on("click", "#update-publish", function () {
            var id = $(this).data("id");
            handleUpdate("Y", id);
        });

    var isProcessing = false;

    function handleUpdate(status, id) {
        if (isProcessing) return;

        var $submitButton = $("#submitButton");
        $submitButton.prop("disabled", true);

        var formData = new FormData();

        // Append text data
        formData.append("id", id);
        formData.append("name", $("#nama").val());
        formData.append("kategorilevel1", $("#kategori").attr("data-idlv1"));
        formData.append("kategorilevel2", $("#kategori").attr("data-idlv2"));
        formData.append("id_brand", $("#getbrands").data("id"));
        formData.append("spesifikasi", $("#spesifikasi").val());
        formData.append("price", unformatRupiah($("#harga").val()));
        formData.append(
            "price_exclude",
            unformatRupiah($("#tayangNPPn").val())
        );
        formData.append("PPn", unformatRupiah($("#tayangYPPn").val()));
        formData.append("price_lpse", unformatRupiah($("#tayangYPPn").val()));
        formData.append("stock", $("#stok").val());
        formData.append("id_satuan", $("#satuan").val());
        formData.append("id_jenis_produk", $("#jenis_produk").val());
        formData.append("weight", $("#berat").val());
        formData.append("dimension_length", $("#pangjang").val());
        formData.append("dimension_width", $("#lebar").val());
        formData.append("dimension_high", $("#tinggi").val());
        formData.append(
            "status_preorder",
            $('input[name="preorder"]:checked').val()
        );
        formData.append("is_pdn", $('input[name="pdn"]:checked').val());
        formData.append("status_new_product", $("#kondisi").val());
        formData.append("sku", $("#sku").val());
        formData.append("status_display", status);

        // Append old images
        $("input[name='old_images[]']").each(function () {
            formData.append("old_images[]", $(this).val());
        });

        $("input[name='old_vidio']").each(function () {
            formData.append("old_vidio", $(this).val());
        });

        // Append selected images
        // var photoInput = uploadedFiles;

        for (const key in uploadedFiles) {
            formData.append("images[]", uploadedFiles[key]);
        }

        // Append selected videos
        var videoInput = $("#vidio-input")[0].files;
        if (videoInput.length > 0) {
            for (var j = 0; j < videoInput.length; j++) {
                formData.append("videos[]", videoInput[j]);
            }
        }

        $("#overlay").show();

        $.ajax({
            url: appUrl + "/api/seller/product/update",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                Swal.close(); // Close the loading spinner
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Product updated successfully!",
                    willClose: () => {
                        initialize(); // Refresh UI
                        $submitButton.prop("disabled", false); // Re-enable the button
                    },
                });
            },
            error: function (xhr, status, error) {
                Swal.close(); // Close the loading spinner
                var errorMessage = "Failed to save data!";
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage,
                });
                $submitButton.prop("disabled", false); // Re-enable the button on error
            },
            complete: function () {
                $("#overlay").hide(); // Hide the overlay
            },
        });
    }

    function handleSave(status) {
        var formData = new FormData();

        // Append text data
        formData.append("name", $("#nama").val());
        formData.append("kategorilevel1", $("#kategori").attr("data-idlv1"));
        formData.append("kategorilevel2", $("#kategori").attr("data-idlv2"));
        formData.append("id_brand", $("#getbrands").data("id"));
        formData.append("spesifikasi", $("#spesifikasi").val());
        formData.append("price", unformatRupiah($("#harga").val()));
        formData.append(
            "price_exclude",
            unformatRupiah($("#tayangNPPn").val())
        );
        formData.append("PPn", unformatRupiah($("#tayangYPPn").val()));
        formData.append("price_lpse", unformatRupiah($("#tayangYPPn").val()));
        formData.append("stock", $("#stok").val());
        formData.append("id_satuan", $("#satuan").val());
        formData.append("id_jenis_produk", $("#jenis_produk").val());
        formData.append("weight", $("#berat").val());
        formData.append("dimension_length", $("#pangjang").val());
        formData.append("dimension_width", $("#lebar").val());
        formData.append("dimension_high", $("#tinggi").val());
        formData.append(
            "status_preorder",
            $('input[name="preorder"]:checked').val()
        );
        formData.append("is_pdn", $('input[name="pdn"]:checked').val());
        formData.append("status_new_product", $("#kondisi").val());
        formData.append("sku", $("#sku").val());
        formData.append("status_display", status);

        // Append files
        var videoInput = $("#vidio-input")[0].files;

        // Check if at least one image is uploaded
        // if (photoInput.length === 0) {
        //     Swal.fire({
        //         icon: "error",
        //         title: "Error",
        //         text: "Please upload at least one image.",
        //     });
        //     return;
        // }

        for (const key in uploadedFiles) {
            formData.append("images[]", uploadedFiles[key]);
        }

        for (var j = 0; j < videoInput.length; j++) {
            console.log("Video name:", videoInput[j].name);
            formData.append("videos[]", videoInput[j]);
        }

        $("#overlay").show();
        $.ajax({
            url: appUrl + "/api/seller/product/save",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil",
                    text: "Produk Berhasil Disimpan!",
                    willClose: () => {
                        initialize();
                    },
                });
            },
            error: function (xhr, status, error) {
                var errorMessage = "Gagal Menyimpan data!";
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: errorMessage,
                });
            },
            complete: function () {
                $("#overlay").hide(); // Hide the overlay
            },
        });
    }
}

function htmlFormProduk() {
    html = `
        <div id="view-tambah-produk">
            <div id="form-product">
                <table id="table-data-produk">
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Nama Barang</label>
                        </td>
                        <td style="background-color: #429EBD">
                            <input type="text" name="nama" id="nama" required>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Kategori</label>
                        </td>
                        <td style="background-color: #429EBD">
                            <b style="font-size: 20px" id="kategori" class="getkategori" data-idv1="" data-idlv2="">
                                <i class="material-symbols-outlined" style="font-size: 23px" id="google-icon"> ink_pen </i> Pilih
                            </b>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label for="satuan">Jenis Produk</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <select name="jenis_produk" id="jenis_produk">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Merek</label>
                        </td>
                        <td style="background-color: #429EBD">
                            <p id="getbrands" class="brand" data-id=""> <b> Pilih </b></p>
                            <small style="font-size: 10px">Tidak ada di daftar? <span style="color:#F9AC4D ">Klik Disini Untuk daftar sendiri</span></small>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Spesifikasi</label>
                        </td>
                        <td>
                            <textarea  name="spesifikasi" id="spesifikasi" cols="30" rows="10"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Harga</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:35%" type="text" name="harga" id="harga" placeholder="Minimal 500 & maksimal 200.000.000" required disabled>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Harga Tayang Sebelum PPn</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:35%" type="text" name="tayangNPPn" id="tayangNPPn" placeholder="0" required readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">PPn</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:35%" type="text" name="PPn" id="PPn" placeholder="0" required readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Harga Tayang Termasuk PPn</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:35%" type="text" name="tayangYPPn" id="tayangYPPn" placeholder="0" required readonly>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Stok</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:15%" type="text" name="stok" id="stok" placeholder="0" required>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label for="satuan">Satuan Produk</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <select name="satuan" id="satuan">
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <b> Foto Produk </b>
                        </td>
                        <td>
                            <input type="file" id="file-input" style="display: none;" accept="image/*">
                            <div style="display: flex">
                                <div class="foto-produk-add" id="foto-produk-add-1" style="display:grid; text-align: center;">
                                    <i class="material-symbols-outlined" id="google-icon">add</i>
                                    <small>Utama</small>
                                </div>
                                <div class="foto-produk-add" id="foto-produk-add-2" style="display:grid; text-align: center;">
                                    <i class="material-symbols-outlined" id="google-icon">add</i>
                                </div>
                                <div class="foto-produk-add" id="foto-produk-add-3" style="display:grid; text-align: center;">
                                    <i class="material-symbols-outlined" id="google-icon">add</i>
                                </div>
                                <div class="foto-produk-add" id="foto-produk-add-4" style="display:grid; text-align: center;">
                                    <i class="material-symbols-outlined" id="google-icon">add</i>
                                </div>
                                <div class="foto-produk-add" id="foto-produk-add-5" style="display:grid; text-align: center;">
                                    <i class="material-symbols-outlined" id="google-icon">add</i>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b> Video Produk </b>
                        </td>
                        <td>
                            <input type="file" id="vidio-input" style="display: none;" accept="video/*">
                            <div style="display: flex">
                                <div class="vidio-produk-add" id="vidio-produk-add-1" style="display:grid; text-align: center;">
                                    <i class="material-symbols-outlined" id="google-icon">add</i>
                                    <small>Vidio</small>
                                </div>
                                <div id="text-aturan-upload">
                                    <ul>
                                        <li>Rasio 1:1, Ukuran Maks. 1MB dan resolusi 800x800px dengan background putih polos</li>
                                        <li>Nama file pada foto dan video yang akan diupload tidak boleh ada simbol titik</li>
                                        <li>Durasi: 10-60 detik</li>
                                        <li>Format: MP4</li>
                                        <li>Catatan: Kamu dapat menampilkan produk saat video sedang diproses. Video akan muncul setelah berhasil diproses.</li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Perkiraan Berat [Gram]</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:15%" type="text" name="berat" id="berat" placeholder="500" required>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Dimensi [CM]</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <div style="display: flex">
                                <input style="font-size: 14px; width:15%; margin-right:10px" type="text" name="pangjang" id="pangjang" placeholder="Panjang" required>
                                <input style="font-size: 14px; width:15%; margin-right:10px" type="text" name="lebar" id="lebar" placeholder="Lebar" required>
                                <input style="font-size: 14px; width:15%" type="text" name="tinggi" id="tinggi" placeholder="Tinggi" required>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="preorder">Pre Order</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <div style="display: flex; width:100px">
                                <div class="custom-radio">
                                    <input type="radio" name="preorder" id="preorder-yes" value="Y" />
                                    <label class="radio-label" for="preorder-yes">Ya</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" name="preorder" id="preorder-no" value="N" checked />
                                    <label class="radio-label" for="preorder-no">Tidak</label>
                                </div>
                            </div>
                            <small style="font-size: 12px">Kirimkan produk dalam 2 hari (tidak termasuk hari Sabtu, Minggu, libur nasional dan non-operasional jasa kirim).</small>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="preorder">Produk Dalam Negeri [PDN]</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <div style="display: flex; width:100px">
                                <div class="custom-radio">
                                    <input type="radio" name="pdn" id="pdn-yes" value="1" />
                                    <label class="radio-label" for="pdn-yes">Ya</label>
                                </div>
                                <div class="custom-radio">
                                    <input type="radio" name="pdn" id="pdn-no" value="0" checked />
                                    <label class="radio-label" for="pdn-no">Tidak</label>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">Kondisi</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <select name="kondisi" id="kondisi">
                                <option value="Y">Baru</option>
                                <option value="N">Pernah Dipakai</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 30%">
                            <label style="margin-left: 10px" for="name">SKU</label>
                        </td>
                        <td style="background-color: #429EBD;">
                            <input style="font-size: 14px; width:15%" type="text" name="sku" id="sku" placeholder="-" required>
                        </td>
                    </tr>
                </table>
                <div class="button-container">
                    <button class="cancel-button">Batal</button>
                    <div class="button-group">
                        <button id="save-archive">Simpan & Arsipkan</button>
                        <button id="save-publish">Simpan & Tayangkan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    `;
    return html;
}

let existingImages = [];
let updatedImages = {};

// Fungsi untuk menampilkan gambar di elemen
function displayImages(imageUrls) {
    const $fotoAddElements = $(".foto-produk-add");

    // Reset each foto-produk-add element
    $fotoAddElements.each(function () {
        $(this).html(`
            <i class="material-symbols-outlined" id="google-icon">add</i>
        `);
        $(this).find("input[type='hidden']").remove(); // Hapus input hidden jika ada
    });

    // Loop through image URLs and update each element
    imageUrls.slice(0, 5).forEach((url, index) => {
        const $element = $fotoAddElements.eq(index);
        $element.html(`
            <img src="${url}" width="70px" height="70px" alt="produk">
            <i class="material-icons trash-icon" onclick="removeImage(this)">delete</i>
            <input type="hidden" name="old_images[]" value="${url}">
        `);
    });
}

function removeImage(element) {
    var $imageItem = $(element).closest(".foto-produk-add");
    $imageItem.html(`
        <i class="material-symbols-outlined" id="google-icon">add</i>
    `);
    $imageItem.find('input[name="old_images[]"]').remove();
}

function removeVideo(element) {
    var $imageItem = $(element).closest(".vidio-produk-add");
    $imageItem.html(`
        <i class="material-symbols-outlined" id="google-icon">add</i>
    `);
    $imageItem.find('input[name="old_vidio"]').remove();
}
