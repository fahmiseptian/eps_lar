var csrfToken = $('meta[name="csrf-token"]').attr('content');


function toggleFilterProduct(element) {
    var status= element.getAttribute("data-status");
    console.log(status);
    $.ajax({
        type: "GET",
        url: "/seller/product/" + status,
        success: function (data) {
            console.log("berhasil ");
            window.location.href = "/seller/product/" + status;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
    });
}
