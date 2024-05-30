// Memeriksa apakah elemen dengan kelas 'banner-item' atau 'productdetail-item' tersedia
const bannerItems = document.querySelectorAll(".banner-item");
const productItems = document.querySelectorAll(".productdetail-item");
let slides;

// Pilih slides berdasarkan elemen yang ditemukan
if (bannerItems.length > 0) {
    slides = bannerItems;
} else if (productItems.length > 0) {
    slides = productItems;
}

if (slides && slides.length > 0) {
    let currentSlide = 0;
    const totalSlides = slides.length;
    function showSlide(index) {
        slides.forEach((slide) => {
            slide.style.display = "none";
        });
        slides[index].style.display = "block";
    }
    function nextSlide() {
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }
    showSlide(currentSlide);
    setInterval(nextSlide, 3000);
}

// Tampilan untuk mobile
const productMobileItems = document.querySelectorAll(".product-mobile-item");
let currentProductMobileIndex = 0;
function showProductMobileSlide(index) {
    productMobileItems.forEach((item) => {
        item.style.display = "none";
    });
    productMobileItems[index].style.display = "block";
}
function nextProductMobileSlide() {
    currentProductMobileIndex =
        (currentProductMobileIndex + 1) % productMobileItems.length;
    showProductMobileSlide(currentProductMobileIndex);
}
if (productMobileItems.length > 0) {
    showProductMobileSlide(currentProductMobileIndex);
    setInterval(nextProductMobileSlide, 3000);
}

// product
$(document).ready(function () {
    const initialDisplayCount = 12;
    let displayedCount = initialDisplayCount;

    $(".product-item").hide();
    $(".product-item:lt(" + initialDisplayCount + ")").show();

    $("#loadMoreButton").on("click", function () {
        displayedCount += initialDisplayCount;
        $(".product-item:lt(" + displayedCount + ")").show();

        if ($(".product-item").length <= displayedCount) {
            $("#loadMoreButton").hide();
        }
    });
});

// qty Product
function increaseQuantity() {
    const quantityInput = document.getElementById("quantity");
    let newQuantity = parseInt(quantityInput.value) + 1;
    // Pastikan nilai tidak melebihi batas maksimum
    if (newQuantity <= parseInt(quantityInput.max)) {
        quantityInput.value = newQuantity;
    }
}

function decreaseQuantity() {
    const quantityInput = document.getElementById("quantity");
    let newQuantity = parseInt(quantityInput.value) - 1;
    // Pastikan nilai tidak kurang dari batas minimum
    if (newQuantity >= parseInt(quantityInput.min)) {
        quantityInput.value = newQuantity;
    }
}

function getQuantity() {
    const quantityInput = document.getElementById("quantity");
    const quantityValue = parseInt(quantityInput.value);
    return quantityValue;
}

// deskripsi Product di detail
function showSection(section) {
    // Sembunyikan semua div dengan class "section-content"
    const sections = document.querySelectorAll(".section-content");
    sections.forEach((s) => (s.style.display = "none"));

    // Tampilkan div yang sesuai dengan bagian yang diklik
    const activeSection = document.getElementById(section);
    activeSection.style.display = "block";
}

function showSectionMobile(section) {
    // Sembunyikan semua konten bagian
    const sections = document.querySelectorAll(".section-content-mobile");
    sections.forEach((s) => (s.style.display = "none"));

    // Tampilkan bagian yang sesuai dengan ID yang diinginkan
    const activeSection = document.getElementById(section);
    if (activeSection) {
        activeSection.style.display = "block";
    }
}

// Bintang Product
function showReviewSection(section) {
    // Sembunyikan semua div review-content
    const reviewSections = document.querySelectorAll(".review-content");
    reviewSections.forEach((section) => (section.style.display = "none"));

    // Tampilkan div review-content yang sesuai dengan bagian yang dipilih
    const activeSection = document.getElementById(section);
    if (activeSection) {
        activeSection.style.display = "block";
    }
}

document.addEventListener("DOMContentLoaded", function () {
    showReviewSection("all");
});

// Zoom product
const images = document.querySelectorAll(".product-image-large");
function setImageScale(image, scale) {
    image.style.transform = `scale(${scale})`;
}
images.forEach((image) => {
    let scale = 1;

    image.addEventListener("wheel", (event) => {
        event.preventDefault();
        const delta = event.deltaY < 0 ? 0.1 : -0.1;
        scale = Math.min(Math.max(1, scale + delta), 3);
        // Terapkan skala pada gambar
        setImageScale(image, scale);
    });
});

// detail toko
$(document).ready(function () {
    $("#menu-list li").click(function () {
        $("#menu-list li").removeClass("active");
        $(this).addClass("active");

        if ($(this).attr("id") === "dashboard-tab") {
            $("#dashboard-content").show();
            $("#product-content").hide();
        } else if ($(this).attr("id") === "product-tab") {
            $("#dashboard-content").hide();
            $("#product-content").show();
        }
    });
});

$(document).on("click", "#list-etalase", function () {
    var idEtalase = $(this).attr("data-id");
    var idshop = $(this).attr("data-idshop");
    if (idEtalase === "0") {
        $.ajax({
            url: appUrl + "/getProductsByIdshop/" + idshop,
            type: "GET",
            dataType: "json",
            success: function (data) {
                // Bersihkan konten produk yang ada
                $("#productGrid").empty();
                data.products.forEach(function (product) {
                    var productItem = $("<div>")
                        .addClass("product-item")
                        .css("margin-top", "10px");
                    var productLink = $("<a>")
                        .attr("href", "/product/" + product.id)
                        .addClass("product-link");
                    productLink.append(
                        $("<img>")
                            .attr("src", product.artwork_url_md[0])
                            .attr("alt", "Produk")
                    );
                    productLink.append(
                        $("<p>")
                            .attr("title", product.name)
                            .text(product.name.substring(0, 20) + "...")
                    );
                    productLink.append(
                        $("<p>").text(
                            "Rp " + product.hargaTayang.toLocaleString("id-ID")
                        )
                    );
                    var productInfo = $("<div>").addClass("product-info");
                    productInfo.append(
                        $("<small>")
                            .attr("title", product.namaToko)
                            .text(product.namaToko.substring(0, 6) + "...")
                    );
                    productInfo.append(
                        $("<small>").text(product.count_sold + " terjual")
                    );
                    productInfo.append(
                        $("<small>").text(product.province_name)
                    );
                    productLink.append(productInfo);
                    productItem.append(productLink);
                    $("#productGrid").append(productItem);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(
                    "Error fetching products:",
                    textStatus,
                    errorThrown
                );
            },
        });
    } else {
        // Permintaan AJAX untuk mendapatkan data produk baru berdasarkan ID etalase
        $.ajax({
            url: appUrl + "/getProductsByEtalase/" + idEtalase,
            type: "GET",
            dataType: "json",
            success: function (data) {
                // Bersihkan konten produk yang ada
                $("#productGrid").empty();
                data.products.forEach(function (product) {
                    var productItem = $("<div>")
                        .addClass("product-item")
                        .css("margin-top", "10px");
                    var productLink = $("<a>")
                        .attr("href", "/product/" + product.id)
                        .addClass("product-link");
                    productLink.append(
                        $("<img>")
                            .attr("src", product.artwork_url_md[0])
                            .attr("alt", "Produk")
                    );
                    productLink.append(
                        $("<p>")
                            .attr("title", product.name)
                            .text(product.name.substring(0, 20) + "...")
                    );
                    productLink.append(
                        $("<p>").text(
                            "Rp " + product.hargaTayang.toLocaleString("id-ID")
                        )
                    );
                    var productInfo = $("<div>").addClass("product-info");
                    productInfo.append(
                        $("<small>")
                            .attr("title", product.namaToko)
                            .text(product.namaToko.substring(0, 6) + "...")
                    );
                    productInfo.append(
                        $("<small>").text(product.count_sold + " terjual")
                    );
                    productInfo.append(
                        $("<small>").text(product.province_name)
                    );
                    // Tambahkan elemen informasi produk ke tautan
                    productLink.append(productInfo);
                    // Tambahkan tautan produk ke dalam elemen produk
                    productItem.append(productLink);
                    // Tambahkan elemen produk ke dalam productGrid
                    $("#productGrid").append(productItem);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(
                    "Error fetching products:",
                    textStatus,
                    errorThrown
                );
            },
        });
    }
});

$(document).on("click", "#list-category", function () {
    var idCategory = $(this).data("id"); // Mengambil nilai option yang dipilih
    var idshop = $(this).data("idshop");

    // Permintaan AJAX untuk mendapatkan data produk baru berdasarkan ID kategori
    $.ajax({
        url: appUrl + "/api/kategoriProduct/" + idCategory + "/" + idshop,
        type: "GET",
        dataType: "json",
        success: function (data) {
            // Bersihkan konten produk yang ada
            $("#productGrid-kategori").empty();

            // Iterasi data produk dan tampilkan ke dalam grid produk
            data.products.forEach(function (product) {
                var productItem = $("<div>")
                    .addClass("product-item")
                    .css("margin-top", "10px");
                var productLink = $("<a>")
                    .attr("href", "/product/" + product.id)
                    .addClass("product-link");
                productLink.append(
                    $("<img>")
                        .attr("src", product.artwork_url_md[0])
                        .attr("alt", "Produk")
                );
                productLink.append(
                    $("<p>")
                        .attr("title", product.name)
                        .text(product.name.substring(0, 20) + "...")
                );
                productLink.append(
                    $("<p>").text(
                        "Rp " + product.hargaTayang.toLocaleString("id-ID")
                    )
                );
                var productInfo = $("<div>").addClass("product-info");
                productInfo.append(
                    $("<small>")
                        .attr("title", product.namaToko)
                        .text(product.namaToko.substring(0, 6) + "...")
                );
                productInfo.append(
                    $("<small>").text(product.count_sold + " terjual")
                );
                productInfo.append($("<small>").text(product.province_name));
                // Tambahkan elemen informasi produk ke tautan
                productLink.append(productInfo);
                // Tambahkan tautan produk ke dalam elemen produk
                productItem.append(productLink);
                // Tambahkan elemen produk ke dalam productGrid
                $("#productGrid-kategori").append(productItem);
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error fetching products:", textStatus, errorThrown);
        },
    });
});

// Buat Mobile
$(document).ready(function () {
    $("#categoryDropdown").on("change", function () {
        var selectedCategoryId = $(this).val();
        var idShop = $(this).data("idshop");
        $.ajax({
            url:
                appUrl +
                "/api/kategoriProduct/" +
                selectedCategoryId +
                "/" +
                idShop,
            type: "GET",
            dataType: "json",
            success: function (data) {
                // Bersihkan konten produk yang ada
                $("#productGrid-kategori").empty();

                // Iterasi data produk dan tampilkan ke dalam grid produk
                data.products.forEach(function (product) {
                    var productItem = $("<div>")
                        .addClass("product-item")
                        .css("margin-top", "10px");
                    var productLink = $("<a>")
                        .attr("href", "/product/" + product.id)
                        .addClass("product-link");
                    productLink.append(
                        $("<img>")
                            .attr("src", product.artwork_url_md[0])
                            .attr("alt", "Produk")
                    );
                    productLink.append(
                        $("<p>")
                            .attr("title", product.name)
                            .text(product.name.substring(0, 20) + "...")
                    );
                    productLink.append(
                        $("<p>").text(
                            "Rp " + product.hargaTayang.toLocaleString("id-ID")
                        )
                    );
                    var productInfo = $("<div>").addClass("product-info");
                    productInfo.append(
                        $("<small>")
                            .attr("title", product.namaToko)
                            .text(product.namaToko.substring(0, 6) + "...")
                    );
                    productInfo.append(
                        $("<small>").text(product.count_sold + " terjual")
                    );
                    productInfo.append(
                        $("<small>").text(product.province_name)
                    );
                    // Tambahkan elemen informasi produk ke tautan
                    productLink.append(productInfo);
                    // Tambahkan tautan produk ke dalam elemen produk
                    productItem.append(productLink);
                    // Tambahkan elemen produk ke dalam productGrid
                    $("#productGrid-kategori").append(productItem);
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error(
                    "Error fetching products:",
                    textStatus,
                    errorThrown
                );
            },
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const checkboxBtns = document.querySelectorAll(".checkbox-btn");

    checkboxBtns.forEach((btn) => {
        btn.addEventListener("click", function () {
            btn.classList.toggle("checked");
            if (btn.classList.contains("checked")) {
                btn.innerHTML = "☑";
            } else {
                btn.innerHTML = "☐";
            }
        });
    });
});

function formatRupiah(angka) {
    var number_string = angka.toString().replace(/[^,\d]/g, ""),
        split = number_string.split(","),
        sisa = split[0].length % 3,
        rupiah = split[0].substr(0, sisa),
        ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    return "Rp. " + rupiah;
}

function generateDataHTML(dataArray) {
    var html = '<div class="data-pengaturan">';
    dataArray.forEach(function (item) {
        html += '<div class="item-data-pengaturan">';
        html += '<p class="angka">' + item.angka + "</p>";
        html += '<p class="deskripsi">' + item.deskripsi + "</p>";
        html += "</div>";
    });
    html += "</div>";
    return html;
}
function generateviewtransaksiheader() {
    var html =
        '<div class="list-menu-transaksi"><ul><li class="active" id="item-transaksi" data-kondisi="semua">Semua</li><li  id="item-transaksi" data-kondisi="persetujuan">Butuh Persetujuan</li><li id="item-transaksi" data-kondisi="disetujui">Disetujui</li><li id="item-transaksi" data-kondisi="ditolak">Ditolak</li><li id="item-transaksi" data-kondisi="dikirim">Dikirim</li></ul></div>';
    return html;
}

function generateviewtransaksibody(dataArray) {
    var html = '<div class="list-transaksi">';

    dataArray.forEach(function (item) {
        html += '<div class="item-transaksi">';
        html += '<p style="text-align: right; color:red">Pembayaran</p>';

        // Akses properti detail dari setiap transaksi
        item.detail.forEach(function (detail) {
            html +=
                '<p style="font-size: 18px"><b>' +
                item.invoice +
                "-" +
                detail.id +
                "</b></p>";
            html += '<div class="item-product-transaksi">';
            html +=
                '<div style="display: flex; justify-content: space-between;">';
            html += "<p>" + detail.nama_pt + " </p> <p>status </p>";
            html += "</div>";

            detail.products.forEach(function (product) {
                html += '<div style="display: flex; align-items: center;">';
                html += '<p style="margin-right: 10px;">1.</p>';
                html +=
                    '<img src="http://eliteproxy.co.id/' +
                    product.gambar_produk +
                    '" style="width:50px; height:50px; margin-right: 10px;" alt="product">';
                html +=
                    "<p>" +
                    product.nama_produk +
                    " <br> " +
                    product.qty_produk +
                    " x " +
                    formatRupiah(product.harga_satuan_produk) +
                    "</p>";
                html +=
                    '<p style="margin-left: auto; text-align: right;"><b>Total </b> <br> ' +
                    formatRupiah(product.harga_total_produk) +
                    "</p>";
                html += "</div>";
            });

            html += "</div>";
        });

        html += '<div style="display: flex; justify-content: space-between;">';
        html += "<div>";
        html += "<p>Total Pesanan " + item.jmlh_qty + "</p>";
        html += "<p>Pesanan dibuat <br> " + item.pembuatan_pesanan + "</p>";
        html += "</div>";
        html += "<div>";
        html +=
            '<p style="text-align: right">Total Harga <b>' +
            formatRupiah(item.total) +
            "</b></p>";
        html +=
            '<p><button class="btn btn-primary" style="width:100%">Detail</button></p>';
        html += "</div>";
        html += "</div>";
        html += "</div>";
    });

    html += "</div>";

    return html;
}

function pesanans(data) {
    var pesanans = [
        { angka: data.pesanan, deskripsi: "Pesanan Baru" },
        { angka: data.pesananbelumbayar, deskripsi: "Belum Bayar" },
        { angka: data.dalampengiriman, deskripsi: "Pengiriman" },
        { angka: data.pesananselesai, deskripsi: "Selesai" },
        { angka: data.pesananbatal, deskripsi: "Batal" },
    ];

    var html = '<div class="data-pengaturan">';
    pesanans.forEach(function (item) {
        html += '<div class="item-data-pengaturan">';
        html += '<p class="angka">' + item.angka + "</p>";
        html += '<p class="deskripsi">' + item.deskripsi + "</p>";
        html += "</div>";
    });
    html += "</div>";
    $("#judul-pengaturan").html(
        '<p class="judul">Pesanan Barang Bela Pengadaan</p>'
    );
    return html;
}

function negose(data) {
    var negos = [
        { angka: data.negobelum, deskripsi: "Belum Direspon" },
        { angka: data.negosudah, deskripsi: "Telah Direspon" },
        { angka: data.negoulang, deskripsi: "Nego Ulang" },
    ];
    var html = '<div class="data-pengaturan">';
    negos.forEach(function (item) {
        html += '<div class="item-data-pengaturan">';
        html += '<p class="angka">' + item.angka + "</p>";
        html += '<p class="deskripsi">' + item.deskripsi + "</p>";
        html += "</div>";
    });
    html += "</div>";
    $("#main-pengaturan").html('<p class="judul">Negosiasi Pengadaan</p>');
    return html;
}

$(document).on("click", ".list-menu-transaksi li", function () {
    $(".list-menu-transaksi li").removeClass("active");
    $(this).addClass("active");
});

$(document).on("click", "#prevPage", function () {
    var prevPageUrl = $(this).data("url");
    getdata(prevPageUrl);
});

$(document).on("click", "#nextPage", function () {
    var nextPageUrl = $(this).data("url");
    getdata(nextPageUrl);
});

$(document).on("click", "#menu-profile", function () {});

$(document).on("click", "#item-transaksi", function () {
    var kondisi = $(this).data("kondisi");
    if (kondisi === "semua") {
        getdata(appUrl + "/api/transaksi/semua");
    } else if (kondisi === "persetujuan") {
        getdata(appUrl + "/api/transaksi/butuhpersetujuan");
    } else if (kondisi === "disetujui") {
        getdata(appUrl + "/api/transaksi/disetujui");
    } else if (kondisi === "ditolak") {
        getdata(appUrl + "/api/transaksi/ditolak");
    } else if (kondisi === "dikirim") {
        getdata(appUrl + "/api/transaksi/kirim");
    }
    console.log("kondisi Salah");
});

$(document).on("click", "#menu-transaksi", function () {
    getdata(appUrl + "/api/transaksi/semua");
});

$(document).on("click", "#menu-dashboard", function () {
    getdata(appUrl + "/api/dashboard");
});

function getdata(url) {
    $("#judul-pengaturan, #main-pengaturan").empty();
    $.ajax({
        url: url,
        type: "GET",
        dataType: "json",
        success: function (data) {
            if (data.pesanan != null && data.negobelum != null) {
                var header = pesanans(data);
                var body = negose(data);
            } else if (data.transaksi.data != null) {
                var header = generateviewtransaksiheader();
                var body = generateviewtransaksibody(data.transaksi.data);

                if (data.transaksi.prev_page_url) {
                    body +=
                        '<button id="prevPage" data-url="' +
                        data.transaksi.prev_page_url +
                        '" class="btn btn-primary">Previous Page</button>';
                }
                if (data.transaksi.next_page_url) {
                    body +=
                        '<button id="nextPage" data-url="' +
                        data.transaksi.next_page_url +
                        '" class="btn btn-primary">Next Page</button>';
                }
            }

            $("#judul-pengaturan").append(header);
            $("#main-pengaturan").append(body);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.error("Error :", textStatus, errorThrown);
        },
    });
}

$(document).ready(function () {
    function updateQuantity(id_cart, id_cst, id_cs, action, quantity = null) {
        $.ajax({
            url: "/api/update-quantity",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id_cart: id_cart,
                id_cst: id_cst,
                id_cs: id_cs,
                action: action,
                quantity: quantity,
            },
            success: function (response) {
                if (response.success) {
                    let productId = id_cst;
                    $("input[data-product-id='" + productId + "']").val(
                        response.new_quantity
                    );
                    $("p[data-product-id='" + productId + "']").text(
                        "Tersisa " + response.remaining_quantity + " buah"
                    );
                } else {
                    alert("Gagal memperbarui kuantitas");
                }
            },
            error: function (error) {
                console.error(error);
                alert("Terjadi kesalahan saat memperbarui kuantitas");
            },
        });
    }

    $(document).on("click", "#kurang-qty-cart", function () {
        var id_cart = $(this).data("id");
        var id_cst = $(this).data("id_cst");
        var id_cs = $(this).data("id_cs");
        updateQuantity(id_cart, id_cst, id_cs, "decrease");
    });

    $(document).on("click", "#tambah-qty-cart", function () {
        var id_cart = $(this).data("id");
        var id_cst = $(this).data("id_cst");
        var id_cs = $(this).data("id_cs");
        console.log(id_cart);
        updateQuantity(id_cart, id_cst, id_cs, "increase");
    });

    $(document).on("change", "#quantity", function () {
        var id_cart = $(this).data("id");
        var id_cst = $(this).data("id_cst");
        var id_cs = $(this).data("id_cs");
        var newQuantity = parseInt($(this).val());
        var stock = $(this).data("stock");
        console.log(id_cart);
        if (newQuantity > stock) {
            alert("Kuantitas melebihi stok yang tersedia");
            newQuantity = stock;
            $(this).val(stock);
        }
        updateQuantity(id_cart, id_cst, id_cs, "change", newQuantity);
    });

    $(document).on("click", ".cart-btn", function () {
        const quantity = getQuantity();
        var id_product = $(this).data("id");
        var qty = quantity;
        console.log("Quantity:", id_product);

        $.ajax({
            url: "/api/add-cart",
            type: "POST",
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"),
                id_product: id_product,
                qty: qty,
            },
            success: function (response) {
                console.log("test");
            },
            error: function (error) {
                console.error(error);
                alert("Terjadi kesalahan saat menambah cart");
            },
        });
    });

    $(document).on("click", "#deleteCart", function () {
        var id_temporary = $(this).data("idtemp");
        var id_shop = $(this).data("idshop");
        $.ajax({
            url: "/api/cart/" + id_temporary + "/" + id_shop,
            type: "delete",
            success: function (response) {
                console.log("berhasil");
            },
            error: function (error) {
                console.error(error);
                alert("Terjadi kesalahan saat Menghapus cart");
            },
        });
    });

    $(document).on("click", "#ubah-lokasi-pengiriman", function () {
        var member_address_id = $(this).data("id_address");
        $.ajax({
            url: "api/member/getaddress",
            type: "get",
            success: function (response) {
                let addressList = response.address;
                let addressHTML = addressList
                    .map(
                        (addr) => `
                    <div>
                        <input type="radio" id="address_${
                            addr.member_address_id
                        }" name="address" value="${addr.member_address_id}" ${
                            addr.member_address_id == member_address_id
                                ? "checked"
                                : ""
                        }>
                        <label for="address_${addr.member_address_id}">
                            ${addr.address_name} - ${addr.address}, ${
                            addr.subdistrict_name
                        }, ${addr.city}, ${addr.province_name}, ${
                            addr.postal_code
                        }
                        </label>
                    </div>
                `
                    )
                    .join("");

                Swal.fire({
                    title: "Pilih Alamat Pengiriman",
                    html: `<form id="addressForm">${addressHTML}</form>`,
                    showCancelButton: true,
                    confirmButtonText: "Pilih",
                    preConfirm: () => {
                        const selectedAddress = $(
                            '#addressForm input[name="address"]:checked'
                        ).val();

                        if (!selectedAddress) {
                            Swal.showValidationMessage(
                                "Anda harus memilih satu alamat"
                            );
                        }

                        return selectedAddress;
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        let selectedAddress = result.value;
                        console.log("Alamat yang dipilih:", selectedAddress);
                        // Mengirim permintaan AJAX ke route untuk memperbarui alamat di keranjang
                        $.ajax({
                            url: `api/updateAddressCart/${selectedAddress}`,
                            type: "get",
                            success: function (response) {
                                // Lakukan sesuatu setelah alamat keranjang berhasil diperbarui
                                console.log(
                                    "Alamat keranjang berhasil diperbarui",
                                    response
                                );
                                Swal.fire(
                                    "Sukses",
                                    "Alamat pengiriman berhasil diperbarui!",
                                    "success"
                                ).then(() => {
                                    location.reload();
                                });
                            },
                            error: function (error) {
                                console.error(
                                    "Terjadi kesalahan saat memperbarui alamat keranjang",
                                    error
                                );
                                Swal.fire(
                                    "Gagal",
                                    "Terjadi kesalahan saat memperbarui alamat pengiriman",
                                    "error"
                                );
                            },
                        });
                    }
                });
            },
            error: function (error) {
                console.error(error);
                alert("Terjadi kesalahan saat mengambil alamat");
            },
        });
    });

    $(document).ready(function () {
        $(".jasa-pengiriman").change(function () {
            var id_cs = $(this).data("id_cs");
            var selectedId = $(this).val();
            $.ajax({
                url: "/api/shipping/" + selectedId + "/" + id_cs,
                type: "get",
                success: function (response) {
                    location.reload();
                },
                error: function (error) {
                    console.error(error);
                    alert("Terjadi kesalahan saat Menambah shipping");
                },
            });
        });
    });
});

$(document).on("click", "#asuransi-pengiriman", function () {
        var id_shop = $(this).data("id_shop");
        var id_courier = $(this).data("id_courier");
        var id_cs = $(this).data("id_cs");
        var status = $(this).data("status");
        $.ajax({
            url: "/api/insurance/"+id_shop +"/"+id_courier +"/"+id_cs +"/"+status,
            type: "get",
            success: function (response) {
                location.reload();
            },
            error: function (error) {
                console.error(error);
                alert("Terjadi kesalahan saat Menambah asuransi");
            },
        });
});

