var csrfToken = $('meta[name="csrf-token"]').attr('content');
$(function () {
    $("#example1").dataTable();
    $("#example2").dataTable({
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
    });
});

function showDescription(description, berat) {
    if (window.innerWidth > 800) {
        // Tampilan desktop
        Swal.fire({
            title: 'Description',
            html: '<p>' + description + '</p><b>Maxsimal Berat: ' + berat + ' Gram </b>',
            icon: 'info',
            confirmButtonText: 'OK',
            width: '30%'
        });
    } else {
        // Tampilan ponsel atau tablet
        Swal.fire({
            title: 'Description',
            html: '<p>' + description + '</p><b>Maxsimal Berat: ' + berat + ' Gram </b>',
            icon: 'info',
            confirmButtonText: 'OK',
            width: 'auto'
        });
    }
}
function toggleCourier(checkbox) {
    var courierId = checkbox.getAttribute('data-courier-id');
    var isChecked = checkbox.checked;

    if (isChecked) {
        addCourier(courierId);
    } else {
        removeCourier(courierId);
    }
}

function addCourier(courierId) {
    $.ajax({
        type: 'GET', // Menggunakan metode GET
        url: '/seller/add-courier',
        data: {
            courierId: courierId,
        },
        success: function(data) {
            console.log('Kurir berhasil ditambahkan');
        },
        error: function(xhr, status, error) {
            console.error('Gagal menambahkan kurir:', error);
        }
    });
}


function removeCourier(courierId) {
    $.ajax({
        type: 'GET',
        url: '/seller/remove-courier',
        data: {
            courierId: courierId,
        },
        success: function(data) {
            console.log('Kurir berhasil dihapus');
        },
        error: function(xhr, status, error) {
            console.error('Gagal menghapus kurir:', error);
        }
    });
    
}
