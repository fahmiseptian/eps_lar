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
        type: 'GET',
        url: '/seller/add-courier',
        data: {
            courierId: courierId,
        },
        success: function(data) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Kurir berhasil ditambahkan',
                timer: 1000,
                showConfirmButton: false 
            });
            console.log('Kurir berhasil ditambahkan');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Eror!',
                text: 'Gagal menambahkan kurir',
                showConfirmButton: true 
            });
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
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Kurir berhasil dihapus',
                timer: 1000,
                showConfirmButton: false 
            });
            console.log('Kurir berhasil dihapus');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Eror!',
                text: 'Gagal menghapus kurir',
                showConfirmButton: true 
            });
            console.error('Gagal menghapus kurir:', error);
        }
    });
    
}


function togglefreeCourier(checkbox) {
    var id_province = checkbox.getAttribute('data-province-id');
    var isChecked = checkbox.checked;         

    if (isChecked) {
        addfreeCourier(id_province);
    } else {
        removefreeCourier(id_province);
    }
}

function addfreeCourier(id_province) {
    $.ajax({
        type: 'GET',
        url: '/seller/add-free-courier',
        data: {
            id_province: id_province,
        },
        success: function(data) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Lokasi free ongkir berhasil ditambahkan',
                timer: 1000,
                showConfirmButton: false 
            });
            console.log('Lokasi free ongkir berhasil ditambahkan');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Eror!',
                text: 'Gagal menambahkan Lokasi free ongkir',
                showConfirmButton: true 
            });
            console.error('Gagal menambahkan Lokasi free ongkir:', error);
        }
    });
}


function removefreeCourier(id_province) {
    $.ajax({
        type: 'GET',
        url: '/seller/remove-free-courier',
        data: {
            id_province: id_province,
        },
        success: function(data) {
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: 'Lokasi free ongkir berhasil dihapus',
                timer: 1000,
                showConfirmButton: false 
            });
            console.log('Lokasi free ongkir berhasil dihapus');
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Eror!',
                text: 'Gagal menghapus Lokasi free ongkir',
                showConfirmButton: true 
            });
            console.error('Gagal menghapus Lokasi free ongkir:', error);
        }
    });
    
}