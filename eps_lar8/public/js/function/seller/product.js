var csrfToken = $('meta[name="csrf-token"]').attr('content');

$(function() {
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
            padding:"3px"
        });
        $(".dataTables_length select ").css({
            width: "50px",
            margin: "5px"
        });
    }
});

function toggleFilterProduct(element) {
    var status= element.getAttribute("data-status");
    $("#overlay").show();
    $.ajax({
        type: "GET",
        url: appUrl + "/seller/product/" + status,
        xhrFields: {
            withCredentials: true
        },
        success: function (data) {
            console.log("berhasil ");
            window.location.href = "/seller/product/" + status;
        },
        error: function (xhr, status, error) {
            console.error("Gagal:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

$('#kategori-level1').change(function() {
    var level1Value = $(this).val();
    if (level1Value !== '') {
        $.ajax({
            url: appUrl + '/seller/product/category/level2/' + level1Value,
            type: 'GET',
            xhrFields: {
                withCredentials: true
            },
            success: function(response) {
                $('#kategori-level2').empty().append('<option value="">Pilih Kategori Level 2</option>');
                $.each(response, function(key, value) {
                    $('#kategori-level2').append('<option value="' + value.id + '">' + value.name + '</option>');
                });
                $('#kategorilevel2').show();
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    } else {
        $('#kategorilevel2').hide();
    }
});

$('#kategori-level2').change(function() {
    var kategori = $(this).val();
    $.ajax({
        url: appUrl + '/api/seller/DetailCategory/'+ kategori ,
        type: 'GET',
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            $("#phpVariables").data("ppn", response.ppn);
            $("#phpVariables").data("pph", response.pph);
            $("#phpVariables").data("mp-percent", response["mp-percent"]);
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
});

$(document).ready(function() {
    $('#addProduct').submit(function(event) {
        $("#overlay").show();
        var form = $(this);
        var addUrl = form.attr('action');
        event.preventDefault();

        // Memeriksa jumlah file yang diunggah
        var fileInputs = $('input[name="images[]"]');
        var fileCount = 0;
        fileInputs.each(function() {
            if ($(this)[0].files.length > 0) {
                fileCount++;
            }
        });

        if (fileCount < 1 || fileCount > 5) {
            alert("Please upload at least one image and maximum three images.");
            return false;
        }

        var formData = new FormData(form[0]);
        $.ajax({
            url: addUrl,
            type: 'POST',
            data: formData,
            xhrFields: {
                withCredentials: true
            },
            processData: false, // Set processData ke false
            contentType: false, // Set contentType ke false
            success: function(response) {
                var targetUrl =
                    appUrl + "/seller/product/";
                window.open(targetUrl);
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    });
});



$(document).ready(function(){
    // Function to calculate price based on harga input
    $("#harga").on("input", function(){
        var hargaAwal = parseFloat($(this).val());
        var ppn = parseFloat($("#phpVariables").data("ppn"));
        var pph = parseFloat($("#phpVariables").data("pph"));
        var mpPercent = parseFloat($("#phpVariables").data("mp-percent"));

        // Calculate
        var mp = Math.round(
            hargaAwal *
                (mpPercent /
                    (100 - mpPercent))
        );
        // var hargaDasar = Math.ceil(((hargaAwal + mp) * 100) / (100 - pph) / 1000) * 1000;
        var hargaDasar =  Math.ceil(
            ((hargaAwal + mp) * 100) /
                (100 - pph) /
                1000
        ) * 1000;
        var biayaPpn = hargaDasar * (ppn / 100);
        var hargaTayang = hargaDasar + biayaPpn;

        // Set the calculated values to corresponding input fields
        $("#hargaBelumPPn").val(hargaDasar);
        $("#ppn").val(biayaPpn);
        $("#hargaSudahPPn").val(hargaTayang);
    });
});

$(document).on("click", "#review_product", function () {
    var id = $(this).data("id");
    var review_productUrl = appUrl + '/product/' + id;
    window.open(review_productUrl, '_blank');
});

$(document).on("click", "#edit_product", function () {
    var id = $(this).data("id");
});

$(document).on("click", "#deleteProduct", function () {
    var id = $(this).data("id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/deleteProduct",
        method: "POST",
        data: {
            id_product: id
        },
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            Swal.fire({
                title: "Berhasil",
                text: "Product berhasil dihapus.",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                location.reload();
            });
        },
        error: function (error) {
            console.error("Terjadi kesalahan:", error);
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Silakan coba lagi nanti.",
                icon: "error",
                confirmButtonText: "OK"
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).on("click", "#editStatus", function () {
    var id = $(this).data("id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/editStatusProduct",
        method: "POST",
        data: {
            id_product: id
        },
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            Swal.fire({
                title: "Berhasil",
                text: "Berhasil Merubah Status Product.",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                location.reload();
            });
        },
        error: function (error) {
            console.error("Terjadi kesalahan:", error);
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Silakan coba lagi nanti.",
                icon: "error",
                confirmButtonText: "OK"
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).ready(function() {
    $('input[type="file"]').change(function() {
        var file = this.files[0];
        var reader = new FileReader();
        var parentDiv = $(this).closest('div');

        reader.onload = function(e) {
            parentDiv.find('img').attr('src', e.target.result);
        }

        reader.readAsDataURL(file);
    });

});
