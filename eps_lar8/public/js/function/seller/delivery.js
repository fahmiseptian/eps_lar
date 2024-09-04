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

var allItems = $(".item-box-filter-pesanan");
var activeItem;
function loadData(tipe) {
    $("#overlay").show();

    $.ajax({
        type: "GET",
        url: appUrl + "/api/seller/delivery/" + tipe,
        xhrFields: {
            withCredentials: true,
            loadData,
        },
        success: function (response) {
            var body = $("#content");
            body.empty();
            if (tipe == 'jasa-ongkir') {
                var view =viewJasaPengiriman(response);
                body.append(view);
            } else {
                var view =viewFreeOngkir(response);
                body.append(view);
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}
$(document).on("click", allItems.filter(".open"), function () {
    setupEvents();
});
function setupEvents() {
    // Handle click on the active item
    allItems
        .filter(".active")
        .off("click")
        .on("click", function () {
            allItems.slideDown();
        });

    allItems
        .not(".active")
        .off("click")
        .on("click", function () {
            var tipe = $(this).data("tipe");

            loadData(tipe);
            console.log(tipe);
            allItems.removeClass("active open").hide();
            $(this).addClass("open");
            $(this).addClass("active");
            activeItem = $(this);

            allItems.slideUp();
            activeItem.slideDown();
        });
}
function initialize() {
    allItems.hide();
    activeItem = allItems.first();
    activeItem.show().addClass("active");
    loadData("jasa-ongkir");
    setupEvents();
}


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
    $("#overlay").show();
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
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Eror!',
                text: 'Gagal menambahkan kurir',
                showConfirmButton: true
            });
        },
        complete: function () {
            $("#overlay").hide(); // Sembunyikan loader setelah selesai
        },
    });
}


function removeCourier(courierId) {
    $("#overlay").show();
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
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Eror!',
                text: xhr.responseJSON
                        ? xhr.responseJSON.message
                        : `Gagal menghapus kurir`,
                showConfirmButton: true
            });
            loadData('jasa-ongkir');
        },
        complete: function () {
            $("#overlay").hide();
        },
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
    $("#overlay").show();
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
        },
        complete: function () {
            $("#overlay").hide(); // Sembunyikan loader setelah selesai
        },
    });
}


function removefreeCourier(id_province) {
    $("#overlay").show();
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
        },
        complete: function () {
            $("#overlay").hide(); // Sembunyikan loader setelah selesai
        },
    });

}

$(document).on("click", "#ubahestimasi", function () {
    $("#overlay").show();
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
                    <div class="form-group" style="display: flex; align-items: center; justify-content: center;">
                        <input id="estimasi-input" type="number" class="swal2-input" value="${estimasi_lama}" min="1" max="7" style="width: 50%; display: inline-block;">
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
                        },
                        complete: function () {
                            $("#overlay").hide();
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
                confirmButtonText: "OK"
            });
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

function viewJasaPengiriman(dataArray) {
    view = "";
    var data = listJasaPengiriman(dataArray);
    view +=`
        <div id="view-jasa-pengiriman">
            <div>
                <b>Jasa Kirim Yang Didukung</b> <br>
                <small>
                    Nikmati pelayanan jasa kirim yang lebih cepat dan handal dengan Jasa Kirim yang
                    Didukung. <br>
                    Perlu diingat bahwa kamu membutuhkan printer untuk mencetak label pengiriman secara
                    otomatis.
                </small>
            </div>
        </div>
        <div id="detail-jasa">
            ${data}
        </div>
    `;
    return view;
}

function listJasaPengiriman(data) {
    var listJasaPengiriman= "";
    data.forEach(function(pengiriman) {
        const isChecked = pengiriman.checked === true ? 'checked' : '';
        listJasaPengiriman += `
        <div id="view-oprasional">
            <div>
                <h2>${pengiriman.name}</h2>
                <small>
                    ${pengiriman.description} </br>
                    Batasan <br>
                    ${pengiriman.max_weight} gr
                </small>
            </div>
            <div>
                <label class="switch">
                    <input type="checkbox" ${isChecked} data-courier-id="${pengiriman.id}" onchange="toggleCourier(this)">
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        `;
    });
    return listJasaPengiriman;
}


function viewFreeOngkir(dataArray) {
    var data = listProvinsi(dataArray);
    view =`
        <div id="view-free-pengiriman">
            <p style="font-size: 20px;"><b>Provinsi</b></p>
            <small>Pilih provinsi yang kamu inginkan untuk gratis ongkir.</small>
            <hr>
            <div class="row">
                ${data}
            </div>
        </div>
    `;

    return view;
}


function listProvinsi(data) {
    var listProvinsi= "";
    data.forEach(function(province) {
        const isChecked = province.checked === true ? 'checked' : '';
        listProvinsi += `
        <div class="col-md-3" style="margin-bottom:10px">
            <div style="display: flex; align-item:center">
                <label class="switch">
                    <input type="checkbox" data-province-id="${province.province_id}" onchange="togglefreeCourier(this)" ${isChecked}>
                    <span class="slider round"></span>
                </label>
                <span class="switch-label">${province.province_name}</span>
            </div>
        </div>
        `;
    });
    return listProvinsi;
}
