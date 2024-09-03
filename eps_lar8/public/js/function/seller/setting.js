var csrfToken = $('meta[name="csrf-token"]').attr("content");

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
            emptyTable: "Belum ada Data", // Pesan untuk tabel kosong
        },
    });
});

var allItems = $(".item-box-filter-pesanan");
var activeItem;
function loadData(tipe) {
    $("#overlay").show();

    $.ajax({
        type: "GET",
        url: appUrl + "/api/seller/setting/" + tipe,
        xhrFields: {
            withCredentials: true,
            loadData,
        },
        success: function (response) {
            var body = $("#content");
            body.empty();
            if (tipe == "address") {
                var view = viewAddress(response);
                body.append(view);
            } else {
                var view = viewOprasional(
                    response.operationals,
                    response.config_shop
                );
                body.append(view);
            }
            efektambahan();
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
    loadData("address");
    setupEvents();
}

$(document).on("click", "#editAddress", function () {
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
            withCredentials: true,
        },
        success: function (response) {
            if (response && response.address && response.address.length > 0) {
                var data = response.address[0];

                getProvince(data.province_id);
                getCities(data.province_id, data.city_id);
                getSubdistricts(data.city_id, data.subdistrict_id);

                // Populate the form fields
                $("#name").val(data.address_name);
                $("#id_address").val(data.member_address_id);
                $("#telp").val(data.phone);
                $("#kota").val(data.city_id);
                $("#kecamatan").val(data.subdistrict_id);
                $("#kd_pos").val(data.postal_code);
                $("#detail_address").val(data.address);

                // Show the modal
                $("#modaleditAddress").modal("show");
            } else {
                Swal.fire("Kesalahan", "Data alamat tidak ditemukan", "error");
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

function getProvince(selectedProvinceId = null) {
    $.ajax({
        url: appUrl + "/api/config/getProvince",
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            if (response && response.province) {
                var province = response.province;
                var selectProvince = `<option value="" disabled>Pilih Provinsi</option>`;

                // Loop through the province list and create the options
                $.each(province, function (index, item) {
                    if (item.province_id == selectedProvinceId) {
                        selectProvince += `<option value="${item.province_id}" selected>${item.province_name}</option>`;
                    } else {
                        selectProvince += `<option value="${item.province_id}">${item.province_name}</option>`;
                    }
                });

                // Append the options to the province select element
                $("#provinsi").html(selectProvince);
            } else {
                Swal.fire(
                    "Kesalahan",
                    "Tidak dapat memuat daftar provinsi",
                    "error"
                );
            }
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Kesalahan",
                "Terjadi kesalahan saat memuat provinsi",
                "error"
            );
        },
    });
}

function getCities(provinceId, selectedCityId = null) {
    $.ajax({
        url: appUrl + "/api/config/getCity/" + provinceId,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            if (response && response.citys) {
                var cities = response.citys;
                var selectCities = `<option value="" disabled>Pilih Kota</option>`;

                // Loop through the city list and create the options
                $.each(cities, function (index, city) {
                    if (city.city_id == selectedCityId) {
                        selectCities += `<option value="${city.city_id}" selected>${city.city_name}</option>`;
                    } else {
                        selectCities += `<option value="${city.city_id}">${city.city_name}</option>`;
                    }
                });

                // Append the options to the city select element
                $("#kota").html(selectCities);
            } else {
                Swal.fire(
                    "Kesalahan",
                    "Tidak dapat memuat daftar kota",
                    "error"
                );
            }
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Kesalahan",
                "Terjadi kesalahan saat memuat kota",
                "error"
            );
        },
    });
}

function getSubdistricts(cityId, selectedSubdistrictId = null) {
    $.ajax({
        url: appUrl + "/api/config/getdistrict/" + cityId,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            if (response && response.subdistricts) {
                var subdistricts = response.subdistricts;
                var selectSubdistricts = `<option value="" disabled>Pilih Kecamatan</option>`;

                // Loop through the subdistrict list and create the options
                $.each(subdistricts, function (index, subdistrict) {
                    if (subdistrict.subdistrict_id == selectedSubdistrictId) {
                        selectSubdistricts += `<option value="${subdistrict.subdistrict_id}" selected>${subdistrict.subdistrict_name}</option>`;
                    } else {
                        selectSubdistricts += `<option value="${subdistrict.subdistrict_id}">${subdistrict.subdistrict_name}</option>`;
                    }
                });

                // Append the options to the subdistrict select element
                $("#kecamatan").html(selectSubdistricts);
            } else {
                Swal.fire(
                    "Kesalahan",
                    "Tidak dapat memuat daftar kecamatan",
                    "error"
                );
            }
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Kesalahan",
                "Terjadi kesalahan saat memuat kecamatan",
                "error"
            );
        },
    });
}

// $(document).on("click", "#editAddress", function () {
//     $("#name").val("");
//     $("#telp").val("");
//     $("#provinsi").html(
//         `<option value="0" disabled selected>Pilih Provinsi</option>`
//     );
//     $("#kota").html(
//         `<option value="0" disabled selected>Mohon Pilih Provinsi terlebih dahulu </option>`
//     );
//     $("#kecamatan").html(
//         `<option value="0" disabled selected>Mohon Pilih Kota terlebih dahulu </option>`
//     );
//     $("#kd_pos").val("");
//     $("#detail_address").val("");

//     var id_address = $(this).attr("data-id");
//     $("#overlay").show();
//     $.ajax({
//         url: appUrl + "/api/seller/setting/address/getAddress",
//         type: "post",
//         data: {
//             id_address: id_address,
//             _token: csrfToken,
//         },
//         xhrFields: {
//             withCredentials: true,
//         },
//         success: function (response) {
//             var data = response.address;
// $.ajax({
//     url: appUrl + "/api/config/getProvince",
//     type: "get",
//     xhrFields: {
//         withCredentials: true,
//     },
//     success: function (response) {
//         var province = response.province;
//         var selectprovince = `<option value="0" disabled selected>Pilih Provinsi</option>`;

//                     province.forEach(function (province) {
//                         selectprovince +=
//                             '<option value="' +
//                             province.province_id +
//                             '">' +
//                             province.province_name +
//                             "</option>";
//                     });
// $.ajax({
//     url: appUrl + "/api/config/getCity/" + data.province_id,
//     type: "get",
//     xhrFields: {
//         withCredentials: true,
//     },
//     success: function (response) {
//         var citys = response.citys;
//         var selectcitys = `<option value="0" disabled selected>Pilih Kota</option>`;

//         citys.forEach(function (city) {
//             selectcitys +=
//                 '<option value="' +
//                 city.city_id +
//                 '">' +
//                                     city.city_name +
//                                     "</option>";
//                             });
//                             $("#kota").html(selectcitys);

//                             $.ajax({
//                                 url:
//                                     appUrl +
//                                     "/api/config/getdistrict/" +
//                                     data.city_id,
//                                 type: "get",
//                                 xhrFields: {
//                                     withCredentials: true,
//                                 },
//                                 success: function (response) {
//                                     var subdistricts = response.subdistricts;
//                                     var selectsubdistricts = `<option value="0" disabled selected>Pilih Kota</option>`;

//                                     subdistricts.forEach(function (
//                                         subdistrict
//                                     ) {
//                                         selectsubdistricts +=
//                                             '<option value="' +
//                                             subdistrict.subdistrict_id +
//                                             '">' +
//                                             subdistrict.subdistrict_name +
//                                             "</option>";
//                                     });
//                                     $("#kecamatan").html(selectsubdistricts);

//                                     $("#provinsi").html(selectprovince);
//                                     $("#name").val(data.address_name);
//                                     $("#telp").val(data.phone);
//                                     $("#provinsi").val(data.province_id);
//                                     $("#kota").val(data.city_id);
//                                     $("#kecamatan").val(data.subdistrict_id);
//                                     $("#kd_pos").val(data.postal_code);
//                                     $("#detail_address").val(data.address);
//                                     $("#id_address").val(id_address);

//                                     $("#modaleditAddress").modal("show");
//                                 },
//                                 error: function (xhr, status, error) {
//                                     Swal.fire(
//                                         "Kesalahan",
//                                         "Terjadi kesalahan",
//                                         "error"
//                                     );
//                                 },
//                                 complete: function () {
//                                     $("#overlay").hide();
//                                 },
//                             });
//                         },
//                         error: function (xhr, status, error) {
//                             Swal.fire(
//                                 "Kesalahan",
//                                 "Terjadi kesalahan",
//                                 "error"
//                             );
//                         },
//                         complete: function () {
//                             $("#overlay").hide();
//                         },
//                     });
//                 },
//                 error: function (xhr, status, error) {
//                     Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
//                 },
//                 complete: function () {
//                     $("#overlay").hide();
//                 },
//             });
//         },
//         error: function (xhr, status, error) {
//             Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
//         },
//         complete: function () {
//             $("#overlay").hide();
//         },
//     });
// });

async function getAddressData(id_address) {
    const response = await $.ajax({
        url: appUrl + "/api/seller/setting/address/getAddress",
        type: "post",
        data: { id_address: id_address, _token: csrfToken },
        xhrFields: { withCredentials: true },
    });
    return response.address;
}

async function getProvinces() {
    const response = await $.ajax({
        url: appUrl + "/api/config/getProvince",
        type: "get",
        xhrFields: { withCredentials: true },
    });
    return response.province;
}

function populateSelect(selector, data, selectedValue = null) {
    let options = `<option value="0" disabled selected>Pilih</option>`;
    data.forEach((item) => {
        options += `<option value="${
            item.province_id || item.city_id || item.subdistrict_id
        }" ${
            item.province_id == selectedValue ||
            item.city_id == selectedValue ||
            item.subdistrict_id == selectedValue
                ? "selected"
                : ""
        }>${
            item.province_name || item.city_name || item.subdistrict_name
        }</option>`;
    });
    $(selector).html(options);
}

$(document).on("click", "#addAddress", async function () {
    $("#name").val("");
    $("#telp").val("");
    $("#provinsi").html(
        `<option value="0" disabled selected>Pilih Provinsi</option>`
    );
    $("#kota").html(
        `<option value="0" disabled selected>Mohon Pilih Provinsi terlebih dahulu </option>`
    );
    $("#kecamatan").html(
        `<option value="0" disabled selected>Mohon Pilih Kota terlebih dahulu </option>`
    );
    $("#kd_pos").val("");
    $("#detail_address").val("");

    try {
        $("#overlay").show();
        const provinces = await getProvinces();
        populateSelect("#provinsi", provinces, 0);
        $("#modaleditAddress").modal("show");
    } catch (error) {
        Swal.fire("Kesalahan", "Terjadi kesalahan", "error");
    } finally {
        $("#overlay").hide();
    }
});

$("#provinsi").on("change", function () {
    var id_province = $(this).val();
    $("#kota").html(
        `<option value="0" disabled selected>Mohon Tunggu</option>`
    );

    $.ajax({
        url: appUrl + "/api/config/getCity/" + id_province,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var citys = response.citys;
            var selectcitys = `<option value="0" disabled selected>Pilih Kota</option>`;

            citys.forEach(function (city) {
                selectcitys +=
                    '<option value="' +
                    city.city_id +
                    '">' +
                    city.city_name +
                    "</option>";
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

$("#kota").on("change", function () {
    var id_city = $(this).val();
    $("#kecamatan").html(
        `<option value="0" disabled selected>Mohon Tunggu</option>`
    );
    $.ajax({
        url: appUrl + "/api/config/getdistrict/" + id_city,
        type: "get",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var subdistricts = response.subdistricts;
            var selectsubdistricts = `<option value="0" disabled selected>Pilih Kota</option>`;

            subdistricts.forEach(function (subdistrict) {
                selectsubdistricts +=
                    '<option value="' +
                    subdistrict.subdistrict_id +
                    '">' +
                    subdistrict.subdistrict_name +
                    "</option>";
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
        title: "Apakah Anda yakin?",
        text: "Apakah Anda benar-benar ingin menghapus alamat ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
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
                    withCredentials: true,
                },
                success: function (response) {
                    // location.reload();
                    loadData("address");
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
            withCredentials: true,
        },
        success: function (response) {
            // location.reload();
            loadData("address");
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Terjadi kesalahan", "error");
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$("#formeditAddress").submit(function (event) {
    event.preventDefault();

    var formData = {
        id_address: $("#id_address").val(),
        name: $("#name").val(),
        telp: $("#telp").val(),
        provinsi: $("#provinsi").val(),
        kota: $("#kota").val(),
        kecamatan: $("#kecamatan").val(),
        kd_pos: $("#kd_pos").val(),
        detail_address: $("#detail_address").val(),
        _token: csrfToken,
    };
    $("#overlay").show();

    console.log(formData);
    $.ajax({
        url: appUrl + "/api/seller/setting/address/update",
        type: "POST",
        data: formData,
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            $("#modaleditAddress").modal("hide");
            loadData("address");
        },
        error: function (xhr, status, error) {
            Swal.fire(
                "Kesalahan",
                "Terjadi kesalahan saat mengirim data",
                "error"
            );
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
});

$(document).ready(function () {
    $(".horizontal-list li").click(function () {
        $(".horizontal-list li").removeClass("active");
        $(this).addClass("active");
    });
});

function viewAddress(dataArray) {
    view = "";
    var data = listAddress(dataArray);
    view += `
        <div id="view-address">
            <div id="header-address">
                <button class="add-Address" id="addAddress">
                    <i id="google-icon" class="material-icons">add</i> <b>Tambah</b>
                </button>
            </div>
            <hr >
            ${data}
        </div>
    `;
    return view;
}

function listAddress(data) {
    var listaddress = "";
    data.forEach(function (address) {
        let buttonsHtml = "";
        if (address.is_shop_address !== "yes") {
            buttonsHtml += `<button  id="setDefaultAddress"  class="aksi-Address" data-id="${address.member_address_id}">Atur Sebagai Alamat Toko</button>`;
            buttonsHtml += `<button id="DeleteAddress" class="aksi-Address" data-id="${address.member_address_id}">Hapus</button>`;
        }
        buttonsHtml += `<button id="editAddress" class="aksi-Address" data-id="${address.member_address_id}">Ubah</button>`;

        listaddress += `
        <div>
            <div id="main-address">
                <div id="list-address">
                    <p>
                        ${address.address_name} <br>
                        (+62) ${address.phone} <br>
                        ${address.address} <br>
                        ${address.city_name} - ${address.subdistrict_name} <br>
                        ${address.province_name} <br>
                        ${address.postal_code}
                    </p>
                </div>
                <div id="list-aksi-address">
                    ${buttonsHtml}
            </div>
        </div>
        <hr>
        `;
    });
    return listaddress;
}

function viewOprasional(dataArr, data) {
    const isChecked = data.is_libur === "Y" ? "checked" : "";
    const isDisabled = data.is_libur === "N" ? "disabled" : "";

    view = "";
    var data = listOperasional(dataArr, isDisabled);
    view += `
        <div id="view-toko-cuti">
            <div>
                <b>Fitur Toko Libur</b> <br>
                <small>
                    Aktifkan Fitur Toko Libur agar Pembeli tidak dapat membuat pesanan. <br>
                    Pesanan yang ada tetap harus diselesaikan. Kamu akan tetap menerima permintaan pengembalian/pembatalan, mohon tetap membalas permintaan tersebut. <br>
                    Perubahan akan memerlukan waktu 1 jam.
                </small>
            </div>
            <div>
                <label class="switch">
                    <input type="checkbox" class="toggle-is-libur" ${isChecked}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div id="detail-oprasional">
            <b>Pengaturan Jam Operasional Toko</b>
            <div id="view-oprasional" ${isDisabled}>
                <div>
                    Hari
                </div>
                <div style="margin-left: 40px">
                    Waktu
                </div>
                <div>
                    Status
                </div>
            </div>
            ${data}
        </div>
        <div id="simpan-oprasional">
            <button class="save-oprasional" id="saveOprasional" ${isDisabled}>
                <b>Simpan Perubahan</b>
            </button>
        </div>
    `;
    return view;
}

function listOperasional(data, aksi) {
    var listOperasional = "";
    data.forEach(function (Operasional) {
        const daysOfWeek = [
            "",
            "Senin",
            "Selasa",
            "Rabu",
            "Kamis",
            "Jumat",
            "Sabtu",
            "Minggu",
        ];
        const dayName = daysOfWeek[Operasional.id_day];
        const isChecked = Operasional.is_active === "Y" ? "checked" : "";

        listOperasional += `
        <div class="item-oprasional" id="item-oprasional">
            <div style = "width:70px">
                <b>${dayName}</b>
            </div>
            <div>
                <input type="time" name="start" id="start" class="input-underline start-time" value="${Operasional.start_time}" ${aksi} >
            </div>
            <div>
                <b>Ke</b>
            </div>
            <div>
                <input type="time" name="end" id="end" class="input-underline end-time" value="${Operasional.end_time}" ${aksi}>
            </div>
            <div>
                <label class="switch">
                    <input type="checkbox" class="toggle-switch" data-id="${Operasional.id}" ${isChecked} ${aksi}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        `;
    });
    return listOperasional;
}

function efektambahan() {
    $("#saveOprasional").on("click", function () {
        // Create an array to hold the data
        var formData = [];

        $(".item-oprasional").each(function () {
            var day = $(this).find("b").text();
            var startTime = $(this).find("input.start-time").val();
            var endTime = $(this).find("input.end-time").val();
            var isActive = $(this).find(".toggle-switch").is(":checked")
                ? "Y"
                : "N";
            var id = $(this).find(".toggle-switch").data("id");

            if (
                !/^([01]\d|2[0-3]):([0-5]\d)$/.test(startTime) ||
                !/^([01]\d|2[0-3]):([0-5]\d)$/.test(endTime)
            ) {
                Swal.fire({
                    icon: "warning",
                    title: "Invalid Time Format",
                    text: "Please ensure that the time is in the 24-hour format HH:MM.",
                });
            }

            console.log(startTime);

            // Push the collected data to the array
            formData.push({
                day: day,
                start_time: startTime,
                end_time: endTime,
                is_active: isActive,
                id: id,
            });
        });

        // Ensure formData is not empty before making the request
        if (formData.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "No data to save",
                text: "No operational data found to save.",
            });
            return;
        }
        $("#overlay").show();
        // Send the data using AJAX
        $.ajax({
            url: appUrl + "/api/seller/setting/update/oprasional",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({ operasional: formData }),
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                // Handle success
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Data berhasil diperbaharui!",
                });
                loadData('toko');
            },
            error: function (xhr, status, error) {
                // Handle error
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred: " + error,
                });
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    });

    $(document).on("change", ".toggle-is-libur", function () {
        $("#overlay").show();

        var isChecked = $(this).is(":checked");

        // Prepare data to send
        var dataToSend = {
            is_active: isChecked ? "Y" : "N",
        };
        // // Send AJAX request
        $.ajax({
            url: appUrl + "/api/seller/setting/update/libur",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(dataToSend),
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                // Handle success
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Status updated successfully!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // location.reload();
                        loadData('toko');
                    }
                });
            },
            error: function (xhr, status, error) {
                // Handle error
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred: " + error,
                });
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
    });
}
