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

$(".horizontal-list li").click(function () {
    $(".horizontal-list li").removeClass("active");
    $(this).addClass("active");
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

$(".delete-promo-product").click(function () {
    var id = $(this).data("id");

    // Tampilkan konfirmasi menggunakan SweetAlert
    Swal.fire({
        title: "Yakin menghapus promo?",
        text: "Promo di produk ini akan dihapus permanen.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi, lakukan AJAX untuk menghapus promo
            $.ajax({
                url: appUrl + "/api/seller/promo/delete-promo",
                type: "post",
                data: {
                    id: id,
                    _token: csrfToken,
                },
                success: function (response) {
                    // Jika berhasil hapus promo, reload halaman
                    location.reload();
                },
                error: function (xhr, status, error) {
                    // Jika terjadi kesalahan, tampilkan pesan error
                    Swal.fire("Error", "Terjadi kesalahan", "error");
                },
                complete: function () {
                    $("#overlay").hide(); // Sembunyikan loader setelah selesai
                },
            });
        }
    });
});


$(document).on("click", "#category-promotion", function () {
    var id = $(this).data("id");
    $("#overlay").show(); // Tampilkan loader

    $.ajax({
        url: appUrl + "/api/seller/promo/product",
        type: "post",
        data: {
            id: id,
            _token: csrfToken,
        },
        success: function (response) {
            var products = response.products;
            var tbody = $("#example2 tbody");
            tbody.empty();

            if (products.length === 0) {
                var emptyRow = `
                    <tr>
                        <td colspan="7" class="text-center">Belum ada produk promosi</td>
                    </tr>
                `;
                tbody.append(emptyRow);
            } else {
                products.forEach(function (product, index) {
                    var row = `
                       <tr>
                        <td class="detail-full">${index + 1}</td>
                        <td>${product.name}</td>
                        <td>${formatRupiah(product.promo_origin)}</td>
                        <td>${formatRupiah(product.promo_price)}</td>
                        <td class="detail-full">${formatRupiah(
                            product.price
                        )}</td>
                        <td class="detail-full">${product.created_dt}</td>
                        <td style="display:flex;">
                            <a href="#" class="delete-promo-product" data-id="${
                                product.id
                            }"><i class="material-icons">delete</i></a>
                        </td>
                    </tr>
                    `;
                    tbody.append(row);
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide(); // Sembunyikan loader setelah selesai
        },
    });
});

$("#modal_TambahPromosi").click(function () {
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/product/",
        type: "get",
        success: function (response) {
            var products = response.products;
            var selectOptions = "";
            var selectOptions = `<option value="0" disabled selected>Pilih Barang</option>`;

            products.forEach(function (product) {
                selectOptions +=
                    '<option value="' +
                    product.id +
                    '">' +
                    product.name +
                    "</option>";
            });
            $("#produkSelect").html(selectOptions);
            $.ajax({
                url: appUrl + "/api/seller/kategoripromo",
                type: "get",
                success: function (response) {
                    var kategoriSelect = response.promotions;
                    var options = '<option value="0" disabled selected>Pilih Kategori Promo</option>';

                    kategoriSelect.forEach(function (kategori) {
                        options += '<option value="' + kategori.id + '">' + kategori.name + '</option>';
                    });
                    $("#kategoriSelect").html(options);

                    $("#tambahProdukpromosi").modal("show");
                },
                error: function (xhr, status, error) {
                    Swal.fire("Error", "Terjadi kesalahan", "error");
                },
                complete: function () {
                    $("#overlay").hide();
                },
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide(); // Sembunyikan loader setelah selesai
        },
    });
});

$("#tambahProdukpromosiForm").on("submit", function (e) {
    e.preventDefault();
    // Mendapatkan nilai yang dipilih dari form
    var selectedProduct = $("#produkSelect").val();
    var selectedCategory = $("#kategoriSelect").val();
    var promoPrice = unformatRupiah($("#promoPrice").val());
    var promoTayangPrice = unformatRupiah($("#promoTayangPrice").val());

    $.ajax({
        url: appUrl + "/api/seller/promo/add-promo",
        type: "post",
        data: {
            id_category: selectedCategory,
            id_product: selectedProduct,
            promo_origin: promoPrice,
            promo_price: promoTayangPrice,
            _token: csrfToken,
        },
        success: function (response) {
            $("#promoPrice").empty();
            $("#promoTayangPrice").empty();
            location.reload();
        },
        error: function (xhr, status, error) {
            Swal.fire("Gagal", "Promo di produk ini sudah ada", "error");
        },
        complete: function () {
            $("#overlay").hide(); // Sembunyikan overlay setelah request selesai
        },
    })
});




$('#produkSelect').on('change', function() {
    var produkId = $(this).val();
    var price = $("#price");
    var hargaTayang = $("#hargaTayang");

    // Mengosongkan nilai sebelumnya
    price.val('menghitung...');
    hargaTayang.val('menghitung...');

    if (produkId) {
        $.ajax({
            url: appUrl + '/api/seller/product/price/' + produkId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                price.val(formatRupiah(response.price));
                hargaTayang.val(formatRupiah(response.harga_tayang));
            },
            error: function(xhr, status, error) {
                console.error('Error fetching data:', error);
                price.val('Error');
                hargaTayang.val('Error');
            }
        });
    } else {
        price.val('');
        hargaTayang.val('');
    }
});

var typingTimer;                // Timer identifier
var doneTypingInterval = 1500;  // Time in ms (1.5 seconds)
var $promoPrice = $("#promoPrice");
var $produkSelect = $("#produkSelect");

$promoPrice.on("input", function () {
    $('#promoTayangPrice').val('menghitung...');
    clearTimeout(typingTimer);
    if ($promoPrice.val()) {
        $promoPrice.val(formatRupiah($promoPrice.val()));
        typingTimer = setTimeout(calcHargaTayang, doneTypingInterval);
    }
});

function calcHargaTayang() {
    var promoPrice = unformatRupiah($promoPrice.val());
    var id_product = parseInt($produkSelect.val()) || 0;
    $('#promoTayangPrice').val('menghitung...');

    $.ajax({
        url: appUrl + '/api/seller/calcHargaTayang',
        type: 'POST',
        data: {
            id_product: id_product,
            price: promoPrice
        },
        success: function(response) {
            $('#promoTayangPrice').val(formatRupiah(response.harga_tayang));
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}



