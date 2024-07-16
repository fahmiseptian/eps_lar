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
        language: {
            emptyTable: 'Belum ada Data'  // Pesan untuk tabel kosong
        }
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
        xhrFields: {
            withCredentials: true
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
        xhrFields: {
            withCredentials: true
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
        xhrFields: {
            withCredentials: true
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
        xhrFields: {
            withCredentials: true
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

$(document).on("click", "#ubahestimasi", function () {
    $.ajax({
        url: appUrl + "/api/seller/get-packingDay",
        method: "get",
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            var estimasi_lama= response.packing_estimation;
            Swal.fire({
                title: 'Ubah Estimasi Packing',
                html: `
                    <div style="display: flex; align-items: center; justify-content: center;">
                        <input id="estimasi-input" type="number" class="swal2-input" value="${estimasi_lama}" min="1" max="7" style="width: 60%; display: inline-block;">
                        <span style="margin-left: 10px;">hari</span>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Submit',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const estimasi = Swal.getPopup().querySelector('#estimasi-input').value;
                    if (!estimasi || estimasi < 1 || estimasi > 7) {
                        Swal.showValidationMessage('Masukkan jumlah hari antara 1 dan 7');
                    }
                    return { estimasi: estimasi };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const estimasi = result.value.estimasi;
                    console.log("Estimasi hari:", estimasi);
                    $.ajax({
                        url: appUrl + "/api/seller/update-packingDay",
                        method: "POST",
                        data: {
                            estimasi: estimasi
                        },
                        xhrFields: {
                            withCredentials: true
                        },
                        success: function (response) {
                            Swal.fire({
                                title: "Berhasil",
                                text: "Estimasi pengiriman berhasil diubah.",
                                icon: "success",
                                confirmButtonText: "OK"
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
                        }
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
                confirmButtonText: "OK"
            });
        }
    });
});

