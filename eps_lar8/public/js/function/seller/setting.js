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

$(document).on("click", "#editAddress", function () {
    $("#name").val('');
    $("#telp").val('');
    $("#provinsi").html(`<option value="0" disabled selected>Pilih Provinsi</option>`);
    $("#kota").html(`<option value="0" disabled selected>Mohon Pilih Provinsi terlebih dahulu </option>`);
    $("#kecamatan").html(`<option value="0" disabled selected>Mohon Pilih Kota terlebih dahulu </option>`);
    $("#kd_pos").val('');
    $("#detail_address").val('');

    var id_address = $(this).attr("data-id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/setting/address/getAddress",
        type: "post",
        data: {
            id_address: id_address,
            _token: csrfToken,
        },
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            var data = response.address;

            $.ajax({
                url: appUrl + "/api/config/getProvince",
                type: "get",
                xhrFields: {
                    withCredentials: true
                },
                success: function (response) {
                    var province = response.province;
                    var selectprovince = `<option value="0" disabled selected>Pilih Provinsi</option>`;

                    province.forEach(function (province) {
                        selectprovince += '<option value="' + province.province_id + '">' + province.province_name + '</option>';
                    });
                    $.ajax({
                        url: appUrl + "/api/config/getCity/"+data.province_id,
                        type: "get",
                        xhrFields: {
                            withCredentials: true
                        },
                        success: function (response) {
                            var citys = response.citys;
                            var selectcitys = `<option value="0" disabled selected>Pilih Kota</option>`;

                            citys.forEach(function (city) {
                                selectcitys += '<option value="' + city.city_id + '">' + city.city_name + '</option>';
                            });
                            $("#kota").html(selectcitys);

                            $.ajax({
                                url: appUrl + "/api/config/getdistrict/"+data.city_id,
                                type: "get",
                                xhrFields: {
                                    withCredentials: true
                                },
                                success: function (response) {
                                    var subdistricts = response.subdistricts;
                                    var selectsubdistricts= `<option value="0" disabled selected>Pilih Kota</option>`;

                                    subdistricts.forEach(function (subdistrict) {
                                        selectsubdistricts += '<option value="' + subdistrict.subdistrict_id + '">' + subdistrict.subdistrict_name + '</option>';
                                    });
                                    $("#kecamatan").html(selectsubdistricts);

                                    $("#provinsi").html(selectprovince);
                                    $("#name").val(data.address_name);
                                    $("#telp").val(data.phone);
                                    $("#provinsi").val(data.province_id);
                                    $("#kota").val(data.city_id);
                                    $("#kecamatan").val(data.subdistrict_id);
                                    $("#kd_pos").val(data.postal_code);
                                    $("#detail_address").val(data.address);
                                    $("#id_address").val(id_address);


                                    $("#modaleditAddress").modal("show");
                                },
                                error: function (xhr, status, error) {
                                    Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
                                },
                                complete: function () {
                                    $("#overlay").hide();
                                },
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
                        },
                        complete: function () {
                            $("#overlay").hide();
                        },
                    });

                },
                error: function (xhr, status, error) {
                    Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
                },
                complete: function () {
                    $("#overlay").hide();
                },
            });
        },
        error: function (xhr, status, error) {
            Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

async function getAddressData(id_address) {
    const response = await $.ajax({
        url: appUrl + "/api/seller/setting/address/getAddress",
        type: "post",
        data: { id_address: id_address, _token: csrfToken },
        xhrFields: { withCredentials: true }
    });
    return response.address;
}

async function getProvinces() {
    const response = await $.ajax({
        url: appUrl + "/api/config/getProvince",
        type: "get",
        xhrFields: { withCredentials: true }
    });
    return response.province;
}

function populateSelect(selector, data, selectedValue = null) {
    let options = `<option value="0" disabled selected>Pilih</option>`;
    data.forEach(item => {
        options += `<option value="${item.province_id || item.city_id || item.subdistrict_id}" ${item.province_id == selectedValue || item.city_id == selectedValue || item.subdistrict_id == selectedValue ? 'selected' : ''}>${item.province_name || item.city_name || item.subdistrict_name}</option>`;
    });
    $(selector).html(options);
}

$(document).on("click", "#addAddress", async function () {
    $("#name").val('');
    $("#telp").val('');
    $("#provinsi").html(`<option value="0" disabled selected>Pilih Provinsi</option>`);
    $("#kota").html(`<option value="0" disabled selected>Mohon Pilih Provinsi terlebih dahulu </option>`);
    $("#kecamatan").html(`<option value="0" disabled selected>Mohon Pilih Kota terlebih dahulu </option>`);
    $("#kd_pos").val('');
    $("#detail_address").val('');

    try {
        $("#overlay").show();
        const provinces = await getProvinces();
        populateSelect("#provinsi", provinces,0);
        $("#modaleditAddress").modal("show");
    } catch (error) {
        Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
    } finally {
        $("#overlay").hide();
    }
});

$('#provinsi').on('change', function() {
    var id_province = $(this).val();
    $("#kota").html(`<option value="0" disabled selected>Mohon Tunggu</option>`);

    $.ajax({
        url: appUrl + "/api/config/getCity/"+id_province,
        type: "get",
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            var citys = response.citys;
            var selectcitys = `<option value="0" disabled selected>Pilih Kota</option>`;

            citys.forEach(function (city) {
                selectcitys += '<option value="' + city.city_id + '">' + city.city_name + '</option>';
            });
            $("#kota").html(selectcitys);
        },
        error: function (xhr, status, error) {
            Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$('#kota').on('change', function() {
    var id_city = $(this).val();
    $("#kecamatan").html(`<option value="0" disabled selected>Mohon Tunggu</option>`);
    $.ajax({
        url: appUrl + "/api/config/getdistrict/"+id_city,
        type: "get",
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            var subdistricts = response.subdistricts;
            var selectsubdistricts= `<option value="0" disabled selected>Pilih Kota</option>`;

            subdistricts.forEach(function (subdistrict) {
                selectsubdistricts += '<option value="' + subdistrict.subdistrict_id + '">' + subdistrict.subdistrict_name + '</option>';
            });
            $("#kecamatan").html(selectsubdistricts);
        },
        error: function (xhr, status, error) {
            Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).on("click", "#DeleteAddress", function () {
    var id_address = $(this).attr("data-id");

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Apakah Anda benar-benar ingin menghapus alamat ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $("#overlay").show();
            $.ajax({
                url: appUrl + "/api/seller/setting/address/delete",
                type: "post",
                data: {
                    id_address: id_address,
                    _token: csrfToken,
                },
                xhrFields: {
                    withCredentials: true
                },
                success: function (response) {
                    location.reload();
                },
                error: function (xhr, status, error) {
                    Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
                },
                complete: function () {
                    $("#overlay").hide();
                },
            });
        }
    });
});

$(document).on("click", "#setDefaultAddress", function () {
    var id_address = $(this).attr("data-id");
    $("#overlay").show();
    $.ajax({
        url: appUrl + "/api/seller/setting/address/setdefault",
        type: "post",
        data: {
            id_address: id_address,
            _token: csrfToken,
        },
        xhrFields: {
            withCredentials: true
        },
        success: function (response) {
            location.reload();
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$('#formeditAddress').submit(function(event) {
    event.preventDefault();

    var formData = {
        id_address: $('#id_address').val(),
        name: $('#name').val(),
        telp: $('#telp').val(),
        provinsi: $('#provinsi').val(),
        kota: $('#kota').val(),
        kecamatan: $('#kecamatan').val(),
        kd_pos: $('#kd_pos').val(),
        detail_address: $('#detail_address').val(),
        _token: csrfToken
    };
    $("#overlay").show();

    console.log(formData);
    $.ajax({
        url: appUrl + '/api/seller/setting/address/update',
        type: 'POST',
        data: formData,
        xhrFields: {
            withCredentials: true
        },
        success: function(response) {
            $('#modaleditAddress').modal('hide');
            location.reload();
        },
        error: function(xhr, status, error) {
            Swal.fire('Kesalahan', 'Terjadi kesalahan saat mengirim data', 'error');
        },
        complete: function() {
            $("#overlay").hide();
        }
    });
});
