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

$(document).ready(function() {
    $('#kategori-level1').change(function() {
        var level1Value = $(this).val();
        if (level1Value !== '') {
            $.ajax({
                url: '/seller/product/category/level2/' + level1Value,
                type: 'GET',
                success: function(response) {
                    $('#kategori-level2').empty();
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
});

$(document).ready(function() {
    $('#addProduct').submit(function(event) {
        console.log("masuk");
        var form = $(this);
        var loginUrl = form.attr('action');
        event.preventDefault(); // Mencegah pengiriman formulir default
        
        // Memeriksa jumlah file yang diunggah
        var fileInput = $('#image1'); // Ubah ke input pertama
        var fileCount = fileInput[0].files.length;
        if (fileCount < 1 || fileCount > 3) {
            alert("Please upload at least one image and maximum three images.");
            return false; 
        }
        
        var formData = new FormData(form[0]);
        
        // Test data yang dikirim
        // for (var pair of formData.entries()) {
        //     console.log(pair[0]+ ', ' + pair[1]); 
        // }
        
        $.ajax({
            url: loginUrl,
            type: 'POST',
            data: formData,
            processData: false, // Set processData ke false
            contentType: false, // Set contentType ke false
            success: function(response) {
                console.log("berhasil");
                // Handle respons sukses di sini
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                // Handle respons error di sini
            }
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