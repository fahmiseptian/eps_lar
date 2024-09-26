// Memeriksa apakah elemen dengan kelas 'banner-item' atau 'productdetail-item' tersedia
const bannerItems = document.querySelectorAll(".banner-item");
const productItems = document.querySelectorAll(".productdetail-item");
let slides;
var timeout;

$(document).ready(function () {
    function updateQuantity(amount) {
        var $quantityInput = $("#quantity");
        var currentQuantity = parseInt($quantityInput.val());
        var maxStock = parseInt($quantityInput.attr("max"));
        var newQuantity = currentQuantity + amount;

        if (newQuantity >= 1 && newQuantity <= maxStock) {
            $quantityInput.val(newQuantity);
            updateButtonStates();
        }
    }

    function updateButtonStates() {
        var currentQuantity = parseInt($("#quantity").val());
        var maxStock = parseInt($("#quantity").attr("max"));

        $(".quantity-btn.minus").prop("disabled", currentQuantity <= 1);
        $(".quantity-btn.plus").prop("disabled", currentQuantity >= maxStock);
    }

    $(".quantity-btn.minus").click(function () {
        updateQuantity(-1);
    });

    $(".quantity-btn.plus").click(function () {
        updateQuantity(1);
    });

    // Panggil updateButtonStates saat halaman dimuat
    updateButtonStates();
});
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
    const initialDisplayCount = 35;
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

function truncateString(str, num) {
    if (str && str.length > num) {
        return str.slice(0, num) + "...";
    }
    return str || "";
}

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

$(document).ready(function () {
    // Tambahkan kelas 'active' ke 'Semua' saat halaman dimuat
    $("#list-etalase").addClass("active");

    $(document).on("click", "#list-category, #list-etalase", function () {
        var idCategory = $(this).data("id");
        var idshop = $(this).data("idshop");

        // Hapus kelas 'active' dari semua item dan tambahkan ke item yang diklik
        $("#list-etalase, #list-category").removeClass("active");
        $(this).addClass("active");

        const keyword = $("#searchProduct").val().trim();
        const condition = $("#sortOrder").val();

        applySort(keyword, idshop, idCategory, condition);
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

function unformatRupiah(formattedRupiah) {
    var number_string = formattedRupiah.replace(/[^,\d]/g, "");
    return parseInt(number_string.replace(/[.,]/g, ""));
}

function parseRupiah(rupiahString) {
    return parseInt(rupiahString.replace(/[^0-9]/g, ""));
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

function generateDataDetailorderHeader(data) {
    var html = '<div class="detail-transaksi">';
    data.detail.forEach(function (item) {
        html +=
            '<div id="nomor-inv">' +
            "<b>" +
            data.invoice +
            "-" +
            item.id +
            "</b>" +
            "</div>" +
            '<div id="status-payment">' +
            "<p>" +
            data.status +
            "</p>" +
            "</div>" +
            "</div>" +
            '<div class="alamat-penerima">' +
            '<div id="header-alamat-penerima">' +
            '<p><span class="material-icons">location_on</span> Alamat Pengiriman</p>' +
            "</div>" +
            '<div class="detail-alamat-penerima">' +
            '<div class="item-alamat-penerima">' +
            "<b>" +
            (data.buyyer.instansi ? data.buyyer.instansi : "No Department") +
            "</b>" +
            "<b>" +
            data.buyyer.nama_penerima +
            "&nbsp;| " +
            data.buyyer.phone_penerima +
            "</b>" +
            "</div>" +
            '<div class="item-alamat-penerima">' +
            "<b>" +
            data.buyyer.nama +
            "</b>" +
            "<p>" +
            data.buyyer.alamat_penerima +
            "<br>" +
            data.buyyer.district_penerima +
            ", " +
            data.buyyer.kota_penerima +
            "<br>" +
            data.buyyer.provinsi_penerima +
            ", ID " +
            data.buyyer.kode_pos_penerima +
            "</p>" +
            "</div>" +
            "</div>" +
            "</div>" +
            '<div class="data-transaksi-penjual">' +
            '<div class="item-transaksi-penjual">' +
            '<div class="row">' +
            '<div class="label"><b>Penjual</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            item.nama_pt +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>NPWP</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            item.npwp +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Tanggal Dibuat</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.created_date +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Pemohon</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.buyyer.nama +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Departemen</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            (data.buyyer.instansi ? data.buyyer.instansi : "No Department") +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Tipe Pembayaran</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.pembayaran +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>TOP</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.jml_top +
            "</div>" +
            "</div>" +
            "</div>" +
            '<div class="item-transaksi-penjual">' +
            '<div class="row">' +
            '<div class="label"><b>Untuk Keperluan</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            (item.keperluan ? item.keperluan : "") +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Alamat Pengiriman</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.buyyer.alamat_penerima +
            "<br>" +
            data.buyyer.district_penerima +
            ", " +
            data.buyyer.kota_penerima +
            "<br>" +
            data.buyyer.provinsi_penerima +
            ", ID " +
            data.buyyer.kode_pos_penerima +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Alamat Penagihan</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.buyyer.address_biller +
            "<br>" +
            data.buyyer.district_biller +
            ", " +
            data.buyyer.kota_biller +
            "<br>" +
            data.buyyer.provinsi_biller +
            ", ID " +
            data.buyyer.kode_pos_biller +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Penerima</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.buyyer.nama_penerima +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>No Telpon Penerima</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            data.buyyer.phone_penerima +
            "</div>" +
            "</div>" +
            '<div class="row">' +
            '<div class="label"><b>Pesan ke Penjual</b></div>' +
            '<div class="pemisah">:</div>' +
            '<div class="value">' +
            (item.pesan_seller ? item.pesan_seller : "") +
            "</div>" +
            "</div>" +
            "</div>" +
            "</div>";
        ("<br>");
        html += `
        <div class="detail-product-transaksi">
            <div class="toko-info">
                <p>${item.nama_pt}</p>
            </div>
            <div class="btn-group">
                <p id="lacak_pesanan" data-id_shop="${item.id_shop}" data-id_cs="${item.id}" class="btn"><span class="material-icons">content_copy</span>Lacak Pesanan</p>
                <a href="${appUrl}/kwitansi/${item.id_shop}/${item.id}" target="blank" class="btn"><span class="material-icons">content_copy</span>Kwitansi</a>
                <a href="${appUrl}/inv/${item.id_shop}/${item.id}" target="blank" class="btn"><span class="material-icons">content_copy</span>Invoice</a>
                <p class="btn"><span class="material-icons">content_copy</span>Kontrak</p>
            </div>
        </div>
        <div class="product-transaksi">
            <div class="product-list">
    `;
        item.products.forEach(function (product) {
            html +=
                '<div class="product-item">' +
                // '<div class="product-number">' + (index + 1) + '.</div>' +
                '<div class="">' +
                '<img src="' +
                product.image +
                '" alt="product" width="50px" height="50px">' +
                "</div>" +
                '<div class="product-name">' +
                product.nama +
                "</div>" +
                '<div class="product-price">' +
                formatRupiah(product.price) +
                "</div>" +
                '<div class="product-quantity">' +
                product.qty +
                "</div>" +
                '<div class="product-total">' +
                formatRupiah(product.total) +
                "</div>" +
                "</div>";
        });

        html +=
            '<div class="product-item">' +
            // '<div class="product-number">' + (index + 1) + '.</div>' +
            '<div class="product-image">' +
            '<img src="" alt="product" width="50px" height="50">' +
            "</div>" +
            '<div class="product-name"> Ongkos Kirim' +
            item.deskripsi +
            "-" +
            item.service +
            "(" +
            item.etd +
            " Hari)" +
            "</div>" +
            '<div class="product-price">' +
            formatRupiah(item.total_shipping) +
            "</div>" +
            '<div class="product-quantity">1</div>' +
            '<div class="product-total">' +
            formatRupiah(item.total_shipping) +
            "</div>" +
            "</div>";

        html +=
            "</div>" +
            "</div>" +
            '<div class="container">' +
            '<div class="row">' +
            '<div class="col-md-5"></div>' +
            '<div class="col-md-7">' +
            '<div class="detail-pembayaran">' +
            "<p>Subtotal Product tanpa PPN</p>" +
            "<p>" +
            formatRupiah(item.total_barang_tanpa_PPN) +
            "</p>" +
            "</div>" +
            '<div class="detail-pembayaran">' +
            "<p>Subtotal produk sebelum PPN</p>" +
            "<p>" +
            formatRupiah(item.total_barang_dengan_PPN) +
            "</p>" +
            "</div>" +
            '<div class="detail-pembayaran">' +
            "<p>Subtotal Ongkos Kirim sebelum PPN</p>" +
            "<p>" +
            formatRupiah(item.sum_shipping) +
            "</p>" +
            "</div>" +
            '<div class="detail-pembayaran">' +
            "<p>Subtotal Asuransi Pengiriman sebelum PPN</p>" +
            "<p>" +
            formatRupiah(item.insurance_nominal) +
            "</p>" +
            "</div>" +
            '<div class="detail-pembayaran">' +
            "<p>Biaya Penanganan sebelum PPN</p>" +
            "<p>" +
            formatRupiah(item.handling_cost_non_ppn) +
            "</p>" +
            "</div>" +
            '<div class="detail-pembayaran">' +
            "<p>PPN</p>" +
            "<p>" +
            formatRupiah(item.total_ppn) +
            "</p>" +
            "</div>" +
            '<div class="total-pembayaran">' +
            "<p>Grand Total</p>" +
            "<p>" +
            formatRupiah(item.total) +
            "</p>" +
            "</div>" +
            "&nbsp;" +
            "</div>" +
            "</div>" +
            "</div>" +
            "<hr>";
    });
    html += "</div>";
    return html;
}

function generateDataDetailorderMain(data) {
    if (data.id_pembayaran == 23) {
        var html = `
                <div class="container">
                    <div class="row">
                        <div class="col-md-5">
                            <p>Metode Pembayaran : <b>${
                                data.pembayaran
                            }</b> <br> <b> Bank BNI </b> <br> <br> No Rekening <b>03975-60583</b> a/n <br> <b>PT. Elite Proxy Sistem</b> </p>
                        </div>
                        <div class="col-md-7">
                            <div class="detail-pembayaran">
                                <p>Subtotal Product tanpa PPN</p>
                                <p>${formatRupiah(
                                    data.total_barang_tanpa_PPN
                                )}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Subtotal produk sebelum PPN</p>
                                <p>${formatRupiah(
                                    data.total_barang_dengan_PPN
                                )}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Subtotal Ongkos Kirim sebelum PPN</p>
                                <p>${formatRupiah(data.total_shipping)}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Subtotal Asuransi Pengiriman sebelum PPN</p>
                                <p>${formatRupiah(data.total_insurance)}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>Biaya Penanganan sebelum PPN</p>
                                <p>${formatRupiah(
                                    data.total_handling_cost_non_ppn
                                )}</p>
                            </div>
                            <div class="detail-pembayaran">
                                <p>PPN</p>
                                <p>${formatRupiah(data.total_ppn)}</p>
                            </div>
                            <div class="total-pembayaran">
                                <p>Total pembayaran</p>
                                <p>${formatRupiah(data.total)}</p>
                            </div>
                            &nbsp;
                        </div>
                    </div>
                </div>
                <hr>`;
    } else {
        var html = `
        // kode html biasa
        `;
    }
    if (data.status === "Belum Bayar" && data.id_pembayaran !== 22) {
        var button_pebayaran = `
            <p class="btn btn-primary">Kembali</p>
            <p class="btn btn-success" data-id_cart="${data.id_cart}" data-total="${data.total}" id="upload-payment">Upload Pembayaran</p>
        `;
    } else {
        var button_pebayaran = `
            <p class="btn btn-primary">Kembali</p>
        `;
    }

    html += `
    <div>
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                </div>
                <div class="col-md-7">
                    ${button_pebayaran}
                </div>
            </div>
        </div>
    </div>
    <hr>
    `;
    return html;
}

function generateviewtransaksibody(dataArray) {
    var html = '<div class="list-transaksi">';

    dataArray.forEach(function (item) {
        if (item.status_pembayaran === 1) {
            var status_pembayaran = "Selesai";
        }
        if (item.status_pembayaran === 0 && item.file_upload !== null) {
            var status_pembayaran = "Menunggu Pengecekkan Pembayaran";
        } else {
            var status_pembayaran = "Belum Bayar";
        }

        html += '<div class="item-transaksi">';
        html += `<p style="text-align: right; color:red">${status_pembayaran}</p>`;

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
            html += `<p> ${detail.nama_pt} </p> <p> ${status_pembayaran} </p>`;
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

            var tombol;
            if (detail.status_dari_toko === "send_by_seller") {
                tombol = `<p><button class="btn btn-warning" id="pesanan-diterima" data-id_cs="${detail.id}" data-id_cart="${item.id_transaksi}" data-id_shop=${detail.id_shop} style="width:100%">Terima Pesanan</button></p>`;
            } else if (detail.status_dari_toko === "complete") {
                tombol = `<p><button class="btn btn-success" style="width:100%">Pesanan Telah Sampai</button></p>`;
            } else {
                tombol = "";
            }
            html += tombol;
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
        html += `
            <p><button class="btn btn-primary" id="detail-order" data-id_cart="${item.id_transaksi}" style="width:100%">Detail</button></p>'
        `;
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

$(document).on("click", "#pesanan-diterima", function () {
    var id_cart = $(this).data("id_cart");
    var id_cs = $(this).data("id_cs");
    var id_shop = $(this).data("id_shop");

    $.ajax({
        url: appUrl + "/api/get_detail_transaksi/" + id_shop + "/" + id_cs,
        method: "GET",
        success: function (response) {
            var nama_seller = response.cart_shop.nama_seller;
            var detail_product = response.cart_shop.products
                .map(function (item) {
                    return `
                    <tr>
                        <td style="width: 40%; text-align: left; padding: 8px; border: 1px solid #ddd;">${item.nama}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">${item.qty}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <input id="qty_diterima_${item.id}" class="swal2-input" type="number" value="${item.qty}" style="width: calc(100% - 16px); padding: 6px; box-sizing: border-box;">
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd;">
                            <input id="qty_dikembalikan_${item.id}" class="swal2-input" type="number" value="0" style="width: calc(100% - 16px); padding: 6px; box-sizing: border-box;">
                        </td>
                    </tr>
                `;
                })
                .join("");
            var tableHtml = `
                <div style="margin-bottom: 10px;"><b>${nama_seller}</b></div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="width: 40%; text-align: left; padding: 8px; border: 1px solid #ddd;">Nama</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Qty</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Qty yang diterima</th>
                                <th style="padding: 8px; border: 1px solid #ddd;">Qty yang dikembalikan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${detail_product}
                        </tbody>
                    </table>
                </div>
            `;

            Swal.fire({
                title: "Formulir Penerimaan",
                html: tableHtml,
                confirmButtonText: "Simpan",
                width: window.innerWidth <= 600 ? "100%" : "40%",
                preConfirm: () => {
                    var qty_diterima = [];
                    var qty_dikembalikan = [];

                    response.cart_shop.products.forEach(function (item) {
                        var diterima = Swal.getPopup().querySelector(
                            `#qty_diterima_${item.id}`
                        ).value;
                        var dikembalikan = Swal.getPopup().querySelector(
                            `#qty_dikembalikan_${item.id}`
                        ).value;

                        if (!diterima || !dikembalikan) {
                            Swal.showValidationMessage(
                                `Harap mengisi semua field untuk semua produk`
                            );
                            return;
                        }

                        qty_diterima.push({
                            id: item.id,
                            qty_diterima: diterima,
                        });
                        qty_dikembalikan.push({
                            id: item.id,
                            qty_dikembalikan: dikembalikan,
                        });
                    });

                    return {
                        qty_diterima: qty_diterima,
                        qty_dikembalikan: qty_dikembalikan,
                    };
                },
            }).then((result) => {
                if (result.isConfirmed) {
                    var formData = new FormData();
                    formData.append("id_cart", id_cart);
                    formData.append("id_cs", id_cs);
                    formData.append(
                        "qty_diterima",
                        JSON.stringify(result.value.qty_diterima)
                    );
                    formData.append(
                        "qty_dikembalikan",
                        JSON.stringify(result.value.qty_dikembalikan)
                    );
                    $.ajax({
                        url: appUrl + "/api/sumbitBast",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            Swal.fire({
                                title: "Berhasil",
                                icon: "success",
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                title: "Gagal",
                                icon: "error",
                            });
                        },
                    });
                }
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
    });

    // Swal.fire({
    //     title: 'Formulir Penerimaan',
    //     html:
    //         '<input id="qty_diterima" class="swal2-input" placeholder="Qty Diterima">' +
    //         '<input id="qty_dikembalikan" class="swal2-input" placeholder="Qty Dikembalikan">',
    //     showCancelButton: true,
    //     confirmButtonText: 'Submit',
    //     preConfirm: () => {
    //         const qty_diterima = Swal.getPopup().querySelector('#qty_diterima').value;
    //         const qty_dikembalikan = Swal.getPopup().querySelector('#qty_dikembalikan').value;

    //         if (!qty_diterima || !qty_dikembalikan) {
    //             Swal.showValidationMessage(`Harap mengisi semua field`);
    //         }

    //         return { qty_diterima: qty_diterima, qty_dikembalikan: qty_dikembalikan };
    //     }
    // }).then((result) => {
    //     if (result.isConfirmed) {
    //         var formData = new FormData();
    //         formData.append('id_cart', id_cart);
    //         formData.append('id_cs', id_cs);
    //         formData.append('qty_diterima', result.value.qty_diterima);
    //         formData.append('qty_dikembalikan', result.value.qty_dikembalikan);

    // $.ajax({
    //     url: appUrl + '/api/sumbitBast',
    //     type: 'POST',
    //     data: formData,
    //     contentType: false,
    //     processData: false,
    //     success: function(response) {
    //         Swal.fire({
    //             title: 'Berhasil',
    //             icon: 'success'
    //         });
    //     },
    //     error: function(xhr, status, error) {
    //         Swal.fire({
    //             title: 'Gagal',
    //             icon: 'error'
    //         });
    //     }
    // });
    //     }
    // });
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
    // console.log("kondisi Salah");
});

$(document).on("click", "#menu-transaksi", function () {
    getdata(appUrl + "/api/transaksi/semua");
});

$(document).on("click", "#menu-dashboard", function () {
    getdata(appUrl + "/api/dashboard");
});

$(document).on("click", "#detail-order", function () {
    var id_cart = $(this).data("id_cart");
    console.log(id_cart);
    getdata(appUrl + "/api/getorder/" + id_cart);
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
            } else if (data.order != null) {
                var header = generateDataDetailorderHeader(data.order);
                var body = generateDataDetailorderMain(data.order);
                // console.log('masuk');
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

function updateqtyCart(id_cst, action, quantity = null) {
    $.ajax({
        url: appUrl + "/api/updateqtyCart",
        method: "POST",
        data: {
            id_cst: id_cst,
            action: action,
            quantity: quantity,
        },
        success: function (response) {
            console.log("masuk");
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
    });
}

$(document).on("click", "#kurang-qty", function () {
    var id_cst = $(this).data("id_cst");

    updateqtyCart(id_cst, "decrease");
});

$(document).on("click", "#tambah-qty", function () {
    var id_cst = $(this).data("id_cst");

    updateqtyCart(id_cst, "increase");
});

$(document).on("click", ".cart-btn", function () {
    const quantity = getQuantity();
    var id_product = $(this).data("id");
    var id_user = $(this).data("id_user");
    console.log(id_user);
    var qty = quantity;

    if (id_user == null || id_user == "") {
        Swal.fire({
            title: "Perhatian",
            text: "Harap login terlebih dahulu untuk menambahkan produk ke keranjang.",
            icon: "warning",
            confirmButtonText: "OK",
            showCancelButton: true,
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = appUrl + "/login";
            }
        });
        return;
    }
    $.ajax({
        url: appUrl + "/api/add-cart",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_product: id_product,
            qty: qty,
        },
        success: function (response) {
            Swal.fire({
                title: "Berhasil!",
                text: "Produk telah ditambahkan ke keranjang.",
                icon: "success",
                confirmButtonText: "OK",
            });
        },
        error: function (xhr, status, error) {
            var errorMessage =
                "Terjadi kesalahan saat menambah produk ke dalam keranjang.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
                title: "Gagal",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

$(document).on("click", ".buy-btn", function () {
    const quantity = getQuantity();
    var id_product = $(this).data("id");
    var id_user = $(this).data("id_user");
    console.log(id_user);
    var qty = quantity;

    if (id_user == null || id_user == "") {
        Swal.fire({
            title: "Perhatian",
            text: "Harap login terlebih dahulu untuk menambahkan produk ke keranjang.",
            icon: "warning",
            confirmButtonText: "OK",
            showCancelButton: true,
            cancelButtonText: "Batal",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = appUrl + "/login";
            }
        });
        return;
    }
    $.ajax({
        url: appUrl + "/api/add-cart",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_product: id_product,
            qty: qty,
        },
        success: function (response) {
            Swal.fire({
                title: "Berhasil!",
                text: "Produk berhasil ditambahkan ke keranjang.",
                icon: "success",
                confirmButtonText: "OK",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = appUrl + "/cart";
                }
            });
        },
        error: function (xhr, status, error) {
            var errorMessage =
                "Terjadi kesalahan saat menambah produk ke dalam keranjang.";
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            Swal.fire({
                title: "Gagal",
                text: errorMessage,
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

$(document).on("click", "#deleteCart", function () {
    var id_temporary = $(this).data("idtemp");
    var id_shop = $(this).data("idshop");
    $.ajax({
        url: appUrl + "/api/cart/" + id_temporary + "/" + id_shop,
        type: "delete",
        success: function (response) {
            Swal.fire({
                title: "Berhasil!",
                text: "Menghapus Produk dari keranjang.",
                icon: "success",
                confirmButtonText: "OK",
            });
            location.reload();
        },
        error: function (error) {
            console.error(error);
            Swal.fire({
                title: "Gagal",
                text: "Terjadi kesalahan saat menghapus produk di keranjang.",
                icon: "error",
                confirmButtonText: "OK",
            });
        },
    });
});

$(document).on("click", "#ubah-lokasi-pengiriman", function () {
    var member_address_id = $(this).data("id_address");
    $.ajax({
        url: appUrl + "/api/member/getaddress",
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
                    }, ${addr.city}, ${addr.province_name}, ${addr.postal_code}
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
                        url:
                            appUrl +
                            "/api/updateAddressCart/" +
                            selectedAddress,
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
            url: appUrl + "/api/shipping/" + selectedId + "/" + id_cs,
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

$(document).on("click", "#asuransi-pengiriman", function () {
    var id_shop = $(this).data("id_shop");
    var id_courier = $(this).data("id_courier");
    var id_cs = $(this).data("id_cs");
    var status = $(this).data("status");
    $.ajax({
        url:
            appUrl +
            "/api/insurance/" +
            id_shop +
            "/" +
            id_courier +
            "/" +
            id_cs +
            "/" +
            status,
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

$(document).on("click", "#paymend_method", function () {
    var id_payment = $(this).data("id_pay");
    $.ajax({
        url: appUrl + "/api/update-payment",
        type: "POST",
        data: {
            _token: $('meta[name="csrf-token"]').attr("content"),
            id_payment: id_payment,
        },
        success: function (response) {
            location.reload();
        },
        error: function (error) {
            console.error(error);
            alert("Terjadi kesalahan saat memperbarui Payment");
        },
    });
});

$(document).on("click", "#updateTOP", function () {
    var top = $(this).data("top");
    console.log(top);
    $.ajax({
        url: appUrl + "/api/update-top/" + top,
        type: "get",
        success: function (response) {
            location.reload();
        },
        error: function (error) {
            console.error(error);
            alert("Terjadi kesalahan saat Menambah TOP");
        },
    });
});

$(document).on("click", "#upload-payment", function () {
    var id_cart = $(this).data("id_cart");
    var total = $(this).data("total");

    var html = `
        <p>
            Nama Bank Tujuan    : PT. Elite Proxy Sistem <br>
            Bank Tujuan         : Bank BNI <br>
            No Rek Tujuan       : <b> 03975-60583 </b> <br>
            Total Pembayaran    : <b> ${formatRupiah(total)} </b>
        </p>
        <img id="swal2-image-preview" src="#" alt="Bukti Transfer" style="max-width: 200px; max-height: 200px; display: none;"> <!-- Tempat untuk menampilkan preview gambar -->
        <input type="file" id="swal2-file" name="img" accept="image/*" style="display: block; margin-top: 10px;">
    `;

    Swal.fire({
        title: "Upload Pembayaran",
        html: html,
        showCancelButton: true,
        confirmButtonText: "Unggah",
        cancelButtonText: "Batal",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return new Promise((resolve, reject) => {
                var fileInput = document.getElementById("swal2-file");
                var file = fileInput.files[0];
                if (!file) {
                    reject("Anda harus memilih file gambar.");
                } else {
                    resolve(file);
                }
            });
        },
        allowOutsideClick: () => !Swal.isLoading(),
    }).then((result) => {
        if (result.isConfirmed) {
            var file = result.value;
            var formData = new FormData();
            formData.append("id_cart", id_cart);
            formData.append("img", file);

            $.ajax({
                url: appUrl + "/api/upload-payment",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                    Swal.fire({
                        title: "Upload Berhasil",
                        text: "Pembayaran telah diunggah.",
                        icon: "success",
                    });
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: "Upload Gagal",
                        text: "Terjadi kesalahan saat mengunggah pembayaran.",
                        icon: "error",
                    });
                },
            });
        }
    });
});

$(document).on("change", "#swal2-file", function () {
    previewImage(this);
});

function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $("#swal2-image-preview").attr("src", e.target.result).show();
        };

        reader.readAsDataURL(input.files[0]);
    }
}

$(document).on("click", "#request-checkout", function () {
    var id_cart = $(this).data("id_cart");
    Swal.fire({
        title: "Syarat dan Ketentuan",
        html: `
            <p>Dengan melakukan checkout, Anda setuju dengan <a href="http://eliteproxy.co.id/info-term-and-condition" target="_blank">Syarat dan Ketentuan</a>.</p>
            <div>
                <input type="checkbox" id="terms-checkbox">
                <label for="terms-checkbox">Saya setuju dengan Syarat dan Ketentuan</label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Lanjutkan",
        cancelButtonText: "Batal",
        preConfirm: () => {
            if (!document.getElementById("terms-checkbox").checked) {
                Swal.showValidationMessage(
                    "Anda harus menyetujui Syarat dan Ketentuan untuk melanjutkan."
                );
                return false;
            }
        },
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan SweetAlert2 kedua untuk konfirmasi pesanan
            Swal.fire({
                title: "Konfirmasi Pesanan",
                text: "Selesaikan Pesanan?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Selesaikan",
                cancelButtonText: "Batal",
            }).then((confirmResult) => {
                if (confirmResult.isConfirmed) {
                    var formData = new FormData();
                    formData.append("id_cart", id_cart);

                    $.ajax({
                        url: appUrl + "/api/finishCheckout",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            Swal.fire({
                                title: "Pesanan Berhasil Diproses",
                                text: "Silahkan lihat pesanan anda di transaksi.",
                                icon: "success",
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                title: "Pesanan gagal Diproses",
                                text: "Terjadi kesalahan saat Memproses Transaksi.",
                                icon: "error",
                            });
                        },
                    });
                    console.log("Pesanan selesai = " + id_cart);
                } else {
                    console.log("Pesanan dibatalkan");
                }
            });
        } else {
            console.log("User tidak setuju dengan Syarat dan Ketentuan");
        }
    });
});

$(document).on("click", "#lacak_pesanan", function () {
    var id_shop = $(this).data("id_shop");
    var id_order_shop = $(this).data("id_cs");

    console.log(id_order_shop);

    $.ajax({
        url: appUrl + "/api/lacak_pengiriman/" + id_shop + `/` + id_order_shop,
        method: "GET",
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
    });
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
                <td style="text-align: left;"><span class="fa fa-map-marker"> Complete </span></td>
                <td style="text-align: left;">Selesai</td>
                <td style="text-align: left;">${deliveryEnd}</td>
            </tr>
        `;
    } else {
        tableHtml += `
            <tr>
                <td style="text-align: left;"><span class="fa fa-map-marker" colspan="3"> Pesanan Belum Melakukan Request Pengiriman</span></td>
            </tr>
        `;
    }

    tableHtml += `
            </tbody>
        </table>
    `;

    return tableHtml;
}

$(document).on("click", ".updateIsSelectProduct", function () {
    var id_cart = $(this).data("id_cart");
    var id_cst = $(this).data("id_cst");

    $.ajax({
        url: appUrl + "/api/updateIsSelectProduct",
        method: "POST",
        data: {
            id_cart: id_cart,
            id_cst: id_cst,
        },
        success: function (response) {
            $("#total-cart").empty();
            var sumprice = `${formatRupiah(response.carts.sumprice)} &nbsp;`;
            $("#total-cart").append(sumprice);

            $("#totalqty").empty();
            var qty = `${response.carts.qty} Produk`;
            $("#totalqty").append(qty);

            var icon =
                response.carts.is_selected === "Y"
                    ? "check_box"
                    : "check_box_outline_blank";
            $("#icon-" + id_cst).text(icon);

            // Cek jika ada produk yang tidak terpilih
            var allSelected = true;
            $(".updateIsSelectProduct").each(function () {
                if (
                    $(this).find(".material-icons").text() ===
                    "check_box_outline_blank"
                ) {
                    allSelected = false;
                }
            });

            // Ubah ikon toko berdasarkan status produk
            var sellerIcon = allSelected
                ? "check_box"
                : "check_box_outline_blank";
            $("#icon-seller-" + response.carts.id_shop).text(sellerIcon); // Ganti dengan ID seller yang sesuai
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
    });
});

$(".select-all-seller").click(function (e) {
    e.preventDefault();
    var sellerId = $(this).data("id_seller");
    var cartId = $(this).data("id_cart");
    var isSelected =
        $("#icon-seller-" + sellerId).text() === "check_box" ? "N" : "Y"; // Toggle status

    // Update status semua produk di toko
    $(this)
        .find(".material-icons")
        .text(isSelected === "Y" ? "check_box" : "check_box_outline_blank");

    // Update ikon produk di dalam toko
    $(".detail-product-cart[data-shop-id='" + sellerId + "']").each(
        function () {
            // Tambahkan filter berdasarkan sellerId
            var productIcon = $(this).find(
                ".updateIsSelectProduct .material-icons"
            );
            productIcon.text(
                isSelected === "Y" ? "check_box" : "check_box_outline_blank"
            );
        }
    );

    $.ajax({
        url: appUrl + "/api/update-product-selection/shop", // Ganti dengan URL yang sesuai
        method: "POST",
        data: {
            id_cart: cartId,
            id_shop: sellerId,
            is_selected: isSelected,
            _token: "{{ csrf_token() }}", // Tambahkan token CSRF
        },
        success: function (response) {
            $("#total-cart").empty();
            var sumprice = `${formatRupiah(response.carts.sumprice)} &nbsp;`;
            $("#total-cart").append(sumprice);

            $("#totalqty").empty();
            var qty = `${response.carts.qty} Produk`;
            $("#totalqty").append(qty);
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
    });
});

$(".input-qty").on("input", function () {
    $(".btn-checkout").prop("disabled", true);
    var $this = $(this);
    var id_produk = $this.data("id");
    var id_cst = $this.data("id_cst");
    var max = $this.data("max");

    var currentValue = $this.val();
    var cleanedValue = currentValue.replace(/[^0-9]/g, "");
    $this.val(cleanedValue);

    if (cleanedValue === "" || parseInt(cleanedValue) < 1) {
        $("#empty-" + id_produk).show(); // Tampilkan pesan error
        return;
    }
    $("#empty-" + id_produk).hide();

    var qty = cleanedValue;

    if (qty > max) {
        Swal.fire({
            title: "Peringatan",
            text: "Jumlah melebihi stok. Jumlah akan disesuaikan dengan stok maksimum.",
            icon: "warning",
        });
        $(`#qty-product-cart-${id_produk}`).val(max);
        qty = max;

        $.ajax({
            url: appUrl + "/api/cart/update-quantity",
            method: "POST",
            data: {
                qty: qty,
                id_cst: id_cst,
            },
            success: function (response) {
                if (response.success) {
                    $("#total-cart").empty();
                    var sumprice = `${formatRupiah(response.hasil.sumprice)}`;
                    $("#total-cart").append(sumprice);
                    $(`#qty-product-cart-${id_produk}`).val(qty);
                    var itemPrice = $(`#price-${id_cst}`).data("price");
                    var newSubtotal = itemPrice * qty;
                    $(`#subtotal-${id_cst}`).text(formatRupiah(newSubtotal));
                } else {
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: "Gagal memperbarui jumlah item.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("Error updating qty", error);
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: "Silakan coba lagi nanti.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            },
            complete: function () {
                $(".btn-checkout").prop("disabled", false);
            },
        });
        return;
    }

    clearTimeout(timeout);
    timeout = setTimeout(function () {
        $.ajax({
            url: appUrl + "/api/cart/update-quantity",
            method: "POST",
            data: {
                qty: qty,
                id_cst: id_cst,
            },
            success: function (response) {
                if (response.success) {
                    $("#total-cart").empty();
                    var sumprice = `${formatRupiah(response.hasil.sumprice)}`;
                    $("#total-cart").append(sumprice);
                    $(`#qty-product-cart-${id_produk}`).val(qty);
                } else {
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: "Gagal memperbarui jumlah item.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("Error updating qty", error);
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: "Silakan coba lagi nanti.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            },
        });
    }, 500);
});

function updateCartQuantity(action) {
    return function () {
        $(".btn-checkout").prop("disabled", true);
        var id_cst = $(this).data("id_cst");
        var id_produk = $(this).data("id");
        var max = $("#qty-product-cart-" + id_produk).data("max");

        // Ambil nilai qty langsung dari input
        var qty = parseInt($("#qty-product-cart-" + id_produk).val());
        var newQty = action === "decrease" ? qty - 1 : qty + 1;

        if (newQty < 1) {
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Minimal 1 Produk.",
                icon: "error",
                confirmButtonText: "OK",
            });
            return;
        }

        if (newQty > max) {
            Swal.fire({
                title: "Peringatan",
                text: "Jumlah melebihi stok. Jumlah akan disesuaikan dengan stok maksimum.",
                icon: "warning",
            });
            return;
        }

        $.ajax({
            url: appUrl + "/api/cart/update-quantity",
            method: "POST",
            data: {
                qty: newQty,
                id_cst: id_cst,
            },
            success: function (response) {
                if (response.success) {
                    $("#total-cart").empty();
                    var sumprice = `${formatRupiah(response.hasil.sumprice)}`;
                    $("#total-cart").append(sumprice);
                    $(`#qty-product-cart-${id_produk}`).val(newQty);
                    var itemPrice = $(`#price-${id_cst}`).data("price");
                    var newSubtotal = itemPrice * newQty;
                    $(`#subtotal-${id_cst}`).text(formatRupiah(newSubtotal));
                    $("#price-" + id_cst).empty();
                    $("#price-" + id_cst).append(formatRupiah(response.total));
                } else {
                    Swal.fire({
                        title: "Terjadi Kesalahan",
                        text: "Gagal memperbarui jumlah item.",
                        icon: "error",
                        confirmButtonText: "OK",
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log("Error updating qty", error);
                Swal.fire({
                    title: "Terjadi Kesalahan",
                    text: "Silakan coba lagi nanti.",
                    icon: "error",
                    confirmButtonText: "OK",
                });
            },
            complete: function () {
                $(".btn-checkout").prop("disabled", false);
            },
        });
    };
}

// Penggunaan:
$(".btn-kurang").click(updateCartQuantity("decrease"));
$(".btn-tambah").click(updateCartQuantity("increase"));

document.addEventListener("DOMContentLoaded", function () {
    const categoryItems = document.querySelectorAll(
        ".category-list .filter-item"
    );

    categoryItems.forEach((item) => {
        item.addEventListener("click", handleCategoryClick);
    });

    function handleCategoryClick(e) {
        e.preventDefault();

        // Remove active class from all items
        categoryItems.forEach((i) => i.classList.remove("active"));

        // Add active class to clicked item
        this.classList.add("active");

        // Close all open submenus
        document.querySelectorAll(".has-submenu.open").forEach((submenu) => {
            submenu.classList.remove("open");
        });

        // If item is in submenu, open parent submenu
        const parentSubmenu = this.closest(".submenu");
        if (parentSubmenu) {
            parentSubmenu.parentElement.classList.add("open");
        }

        // If item has submenu, toggle it
        const parentLi = this.closest("li");
        if (parentLi && parentLi.classList.contains("has-submenu")) {
            parentLi.classList.toggle("open");
        }

        applyFilters();
    }

    function applyFilters() {
        const activeFilter = document.querySelector(
            ".category-list .filter-item.active"
        );
        const activeCategory = activeFilter
            ? activeFilter.dataset.category
            : null;

        const keyword = document.getElementById("keyword").value;
        const max = unformatRupiah(document.getElementById("price_max").value);
        const min = unformatRupiah(document.getElementById("price_min").value);

        var dataArray = {
            category: activeCategory,
            keyword: keyword,
            max: max,
            min: min,
            condition: null,
        };

        console.log("Filter data:", dataArray);

        // Uncomment the line below when you're ready to implement the AJAX call
        fetchProducts(dataArray);
    }

    function fetchProducts(data) {
        $("#moreproduct").hide();
        // Implement your AJAX call here
        fetch(appUrl + "/api/filter-searching", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((data) => {
                updateProductList(data);
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    }

    function updateProductList(products) {
        const productSection = document.querySelector(".products-section");
        productSection.innerHTML = ""; // Bersihkan konten sebelumnya

        if (products && products.length > 0) {
            products.forEach((product) => {
                const productItem = document.createElement("div");
                productItem.className = "product-item";

                const image300 = product.image.startsWith("http")
                    ? product.image
                    : `https://eliteproxy.co.id/${product.image}`;

                productItem.innerHTML = `
                    <a href="/product/${product.id}" class="product-link">
                        <img src="${image300}" alt="${product.name}">
                        <p title="${product.name}">${truncateString(
                    product.name,
                    30
                )}</p>
                        <p>${formatRupiah(product.hargaTayang)}</p>
                        <div class="product-info">
                            <small title="${
                                product.shop_name || ""
                            }">${truncateString(
                    product.shop_name || "",
                    14
                )}</small>
                            <small>${product.total_sold || 0} terjual</small>
                            <small>${product.province_name || ""}</small>
                        </div>
                    </a>
                `;

                productSection.appendChild(productItem);
            });
        } else {
            productSection.innerHTML =
                "<p>Tidak ada produk yang ditemukan.</p>";
        }
    }

    // Event listener for condition filter
    document
        .querySelectorAll(".filter-item[data-condition]")
        .forEach((item) => {
            item.addEventListener("click", handleConditionClick);
        });

    function handleConditionClick() {
        const condition = this.dataset.condition;
        const activeFilter = document.querySelector(
            ".category-list .filter-item.active"
        );
        const activeCategory = activeFilter
            ? activeFilter.dataset.category
            : null;

        const keyword = document.getElementById("keyword").value;
        const max = document.getElementById("price_max").value;
        const min = document.getElementById("price_min").value;

        var dataArray = {
            category: activeCategory,
            keyword: keyword,
            max: max,
            min: min,
            condition: condition,
        };

        filterByCondition(dataArray);
    }

    function filterByCondition(data) {
        fetch(appUrl + "/api/filter-searching", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        })
            .then((response) => response.json())
            .then((data) => {
                updateProductList(data);
            })
            .catch((error) => {
                console.error("Error:", error);
            });
    }

    function debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    ["price_min", "price_max"].forEach((id) => {
        const input = document.getElementById(id);

        input.addEventListener("input", function (e) {
            let value = this.value.replace(/[^0-9]/g, "");
            if (value !== "") {
                const formattedValue = formatRupiah(parseInt(value));
                this.value = formattedValue;
            }
            debouncedApplyFilters();
        });

        input.addEventListener("blur", function () {
            if (this.value === "" || parseRupiah(this.value) === 0) {
                this.value = formatRupiah(0);
            }
        });
    });

    // Fungsi debounce yang sudah ada
    const debouncedApplyFilters = debounce(() => {
        applyFilters();
        setTimeout(() => {
            window.scrollTo({
                top: 0,
                behavior: "smooth",
            });
        }, 100);
    }, 1000);
});

document.addEventListener("DOMContentLoaded", function () {
    const sortOrder = document.getElementById("sort-order");

    sortOrder.addEventListener("change", function () {
        const selectedValue = this.value;
        console.log("Urutan yang dipilih:", selectedValue);

        applySort(selectedValue);
    });
});

function applySort(sortType) {
    const activeFilter = document.querySelector(
        ".category-list .filter-item.active"
    );
    const activeCategory = activeFilter ? activeFilter.dataset.category : null;
    const keyword = document.getElementById("keyword").value;
    const max = unformatRupiah(document.getElementById("price_max").value);
    const min = unformatRupiah(document.getElementById("price_min").value);

    var dataArray = {
        category: activeCategory,
        keyword: keyword,
        max: max,
        min: min,
        condition: null,
        sort: sortType,
    };

    console.log("Filter dan pengurutan data:", dataArray);
    fetchProducts(dataArray);

    window.scrollTo({
        top: 0,
        behavior: "smooth",
    });
}

// Pastikan fungsi fetchProducts sudah ada dan dapat menerima parameter sort
function fetchProducts(data) {
    fetch(appUrl + "/api/filter-searching", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
    })
        .then((response) => response.json())
        .then((data) => {
            updateListProduct(data);
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

function updateListProduct(products) {
    const productSection = document.querySelector(".products-section");
    productSection.innerHTML = ""; // Bersihkan konten sebelumnya

    if (products && products.length > 0) {
        products.forEach((product) => {
            const productItem = document.createElement("div");
            productItem.className = "product-item";

            const image300 = product.image.startsWith("http")
                ? product.image
                : `https://eliteproxy.co.id/${product.image}`;

            productItem.innerHTML = `
                <a href="/product/${product.id}" class="product-link">
                    <img src="${image300}" alt="${product.name}">
                    <p title="${product.name}">${truncateString(
                product.name,
                30
            )}</p>
                    <p>${formatRupiah(product.hargaTayang)}</p>
                    <div class="product-info">
                        <small title="${
                            product.shop_name || ""
                        }">${truncateString(
                product.shop_name || "",
                14
            )}</small>
                        <small>${product.total_sold || 0} terjual</small>
                        <small>${product.province_name || ""}</small>
                    </div>
                </a>
            `;

            productSection.appendChild(productItem);
        });
    } else {
        productSection.innerHTML = "<p>Tidak ada produk yang ditemukan.</p>";
    }
}

$(".popular-search-item").on("click", function () {
    const keyword = $(this).data("keyword");
    window.location.href = appUrl + "/find/" + keyword;
});

$(".category-item").on("click", function () {
    const category = $(this).data("category");
    window.location.href = appUrl + "/find/category/" + category;
});

let searchTimer;

$("#searchProduct").on("input", function () {
    clearTimeout(searchTimer);

    searchTimer = setTimeout(() => {
        const idshop = $(this).data("idshop");
        const keyword = $(this).val().trim();
        const category = $(".category-item-shop.active").data("id");
        const condition = $("#sortOrder").val();

        applySort(keyword, idshop, category, condition);
    }, 500); // Tunggu 500ms setelah pengguna berhenti mengetik
});

function applySort(
    keyword = null,
    idshop = null,
    category = null,
    condition = null
) {
    $.ajax({
        url: appUrl + "/api/shop/search-product",
        method: "GET",
        data: {
            keyword: keyword,
            idshop: idshop,
            category: category,
            condition: condition,
        },
        success: function (response) {
            console.log(response);
            updateProductList(response);
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
        },
    });
}

function updateProductList(products) {
    const productGrid = $("#productGrid-kategori");
    productGrid.empty();

    if (products && products.length > 0) {
        products.forEach((product) => {
            const image300 = product.image.startsWith("http")
                ? product.image
                : `https://eliteproxy.co.id/${product.image}`;
            const productItem = `
                <div class="product-item-category">
                    <a href="/product/${product.id}" class="product-link">
                        <img src="${image300}" alt="${product.name}">
                        <div class="product-info">
                            <p class="product-name" title="${
                                product.name
                            }">${truncateString(product.name, 30)}</p>
                            <p class="product-price">${formatRupiah(
                                product.hargaTayang
                            )}</p>
                        </div>
                    </a>
                </div>
            `;
            productGrid.append(productItem);
        });
    } else {
        productGrid.append("<p>Tidak ada produk yang ditemukan.</p>");
    }
}

$("#sortOrder").on("change", function () {
    const selectedValue = $(this).val();
    applySort(null, null, null, selectedValue);
});
