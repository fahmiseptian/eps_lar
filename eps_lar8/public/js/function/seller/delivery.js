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
    var csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
    $.ajax({
        type: 'post',
        url: appUrl + '/seller/add-courier/',
        data: {
            courierId: courierId, _token: csrfToken 
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
    var csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
    $.ajax({
        type: 'POST',
        url: appUrl +'/seller/remove-courier',
        data: {
            courierId: courierId, _token: csrfToken
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
        url: appUrl + '/seller/add-free-courier',
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
        url: appUrl + '/seller/remove-free-courier',
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

async function ubahestimasi() {
console.log("dsadas");
const { value: estimasi } = await Swal.fire({
    title: "Masukkan Estimasi Packing",
    input: "range",
    icon: "question",
    inputLabel: "Estimasi Packing",
    inputAttributes: {
        min: "1",
        max: "7",
        step: "1"
    },
    inputValue: 2,
    showCancelButton: true,
    confirmButtonText: "OK",
    cancelButtonText: "Batal",
    reverseButtons: true,
    allowOutsideClick: false,
});

// Jika pengguna mengklik tombol OK dan memberikan nilai estimasi
if (estimasi) {
    console.log(estimasi);
}

// Contoh kode ajax jika Anda perlu menggunakan csrfToken
// var csrfToken = $('meta[name="csrf-token"]').attr('content');
// $.ajax({
//     url: "/seller/order/cencel",
//     type: "POST",
//     data: { id_cart_shop: id_cart_shop, note: noteSeller, _token: csrfToken },
//     success: function () {
//         location.reload();
//     },
// });
}