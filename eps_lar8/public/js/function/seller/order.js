$(function () {
    $("#example1").dataTable();
    $("#example2").dataTable({
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bSort: false,
        bInfo: true,
        bAutoWidth: true,
    });
});

function toggleFilterorder(element) {
    var status_order = element.getAttribute("data-status-order");

    $.ajax({
        type: "GET",
        url: "/seller/order/filter/" + status_order,
        success: function (data) {
            console.log("berhasil ");
            window.location.href = "/seller/order/filter/" + status_order;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
    });
}

function viewDetail(element) {
    var id_cart_shop = element.getAttribute("data-id-order");

    $.ajax({
        type: "GET",
        url: "/seller/order/detail/" + id_cart_shop,
        success: function (data) {
            console.log("berhasil");
            window.location.href = "/seller/order/detail/" + id_cart_shop;
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
                url: "/seller/order/accept",
                type: "POST",
                data: { id_cart_shop: id_cart_shop, _token: csrfToken },
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
            url: "/seller/order/cencel",
            type: "POST",
            data: { id_cart_shop: id_cart_shop, note: noteSeller, _token: csrfToken },
            success: function () {
                location.reload();
            },
        });
    }
});
