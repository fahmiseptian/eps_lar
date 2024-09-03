function showlogin() {
    var loginForm = document.querySelector(".login");
    var signupForm = document.querySelector(".daftar");
    var loginLink = document.querySelector(".box-title.active"); // Ubah pemilihan dari ID menjadi kelas
    var signupLink = document.querySelector(".box-title:not(.active)"); // Ubah pemilihan dari ID menjadi kelas

    loginForm.style.display = "block";
    signupForm.style.display = "none";
    loginLink.classList.remove("active");
    signupLink.classList.add("active");
}

function showSignup() {
    var loginForm = document.querySelector(".login");
    var signupForm = document.querySelector(".daftar");
    var loginLink = document.querySelector(".box-title.active"); // Ubah pemilihan dari ID menjadi kelas
    var signupLink = document.querySelector(".box-title:not(.active)"); // Ubah pemilihan dari ID menjadi kelas

    loginForm.style.display = "none";
    signupForm.style.display = "block";
    loginLink.classList.remove("active");
    signupLink.classList.add("active");
}

$(document).ready(function () {
    $("#loginForm").submit(function (e) {
        e.preventDefault(); // Prevent form submission

        // Validate hCaptcha
        // var response = hcaptcha.getResponse();
        // if (!response) {
        //     Swal.fire({
        //         title: "Error",
        //         text: "Please complete the hCaptcha.",
        //         icon: "error",
        //         showConfirmButton: false,
        //         timer: 1500,
        //     });
        //     return;
        // }

        // Form submission
        this.submit();

        // var loginUrl = $(this).attr('action');
        // var formData = $(this).serialize();

        // $.ajax({
        //     type: "POST",
        //     url: loginUrl,
        //     data: formData,
        //     success: function(response){
        //         window.location.href = "{{ route('seller') }}";
        //     },
        //     error: function(xhr, status, error){
        //         var err = JSON.parse(xhr.responseText);
        //         Swal.fire({
        //             icon: 'error',
        //             title: 'Oops...',
        //             text: err.error
        //         });
        //     }
        // });
    });
});

function updateFileName(input, targetInputId) {
    var file = input.files[0];
    var allowedExtensions = /(\.png|\.jpg|\.jpeg|\.pdf)$/i;

    if (file) {
        if (allowedExtensions.exec(file.name)) {
            var fileName = file.name;
            document.getElementById(targetInputId).value = fileName;
        } else {
            Swal.fire({
                icon: "error",
                title: "Invalid File Type",
                text: "Hanya file dengan ekstensi .png, .jpg, .jpeg, .pdf yang diizinkan.",
            });
            input.value = "";
        }
    }
}

$("#kategori-toko").on("click", function () {
    // Pastikan dropdown belum diisi
    if ($("#kategori-toko option").length === 1) {
        $.ajax({
            url: appUrl + "/api/getShop/kategori",
            type: "GET",
            dataType: "json",
            success: function (response) {
                var $select = $("#kategori-toko");
                $select.empty();
                $select.append(
                    '<option value="" disabled selected>Pilih Kategori</option>'
                );

                if (Array.isArray(response)) {
                    $.each(response, function (index, item) {
                        $select.append(
                            '<option value="' +
                                item.id +
                                '">' +
                                item.nama +
                                "</option>"
                        );
                    });
                } else {
                    console.warn("Respons tidak dalam format array:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Terjadi kesalahan:", error);
            },
        });
    }
});

$("#dropdown-provinsi").on("click", function () {
    // Pastikan dropdown belum diisi
    if ($("#dropdown-provinsi option").length === 1) {
        $.ajax({
            url: appUrl + "/api/config/getProvince",
            type: "GET",
            dataType: "json",
            success: function (response) {
                var $select = $("#dropdown-provinsi");
                $select.empty();
                $select.append(
                    '<option value="" disabled selected>Pilih Provinsi</option>'
                );

                if (Array.isArray(response.province)) {
                    $.each(response.province, function (index, item) {
                        $select.append(
                            '<option value="' +
                                item.province_id +
                                '">' +
                                item.province_name +
                                "</option>"
                        );
                    });
                } else {
                    console.warn("Respons tidak dalam format array:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Terjadi kesalahan:", error);
            },
        });
    } else {
        $("#dropdown-kota").val("");
        $("#dropdown-kecamatan").val("");
        $("#dropdown-kota")
            .empty()
            .append('<option value="" disabled selected>Pilih Kota</option>');
        $("#dropdown-kecamatan")
            .empty()
            .append(
                '<option value="" disabled selected>Pilih Kecamatan</option>'
            );
    }
});

$("#dropdown-kota").on("click", function () {
    var id_provinsi = $("#dropdown-provinsi").val();
    if (id_provinsi === "" || id_provinsi === null) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Silakan pilih provinsi terlebih dahulu!",
        });
        return;
    }

    // Pastikan dropdown belum diisi
    if ($("#dropdown-kota option").length === 1) {
        $.ajax({
            url: appUrl + "/api/config/getCity/" + id_provinsi,
            type: "GET",
            dataType: "json",
            success: function (response) {
                var $select = $("#dropdown-kota");
                $select.empty();
                $select.append(
                    '<option value="" disabled selected>Pilih Kota</option>'
                );

                if (Array.isArray(response.citys)) {
                    $.each(response.citys, function (index, item) {
                        $select.append(
                            '<option value="' +
                                item.city_id +
                                '">' +
                                item.city_name +
                                "</option>"
                        );
                    });
                } else {
                    console.warn("Respons tidak dalam format array:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Terjadi kesalahan:", error);
            },
        });
    } else {
        $("#dropdown-kecamatan").val("");
        $("#dropdown-kecamatan")
            .empty()
            .append(
                '<option value="" disabled selected>Pilih Kecamatan</option>'
            );
    }
});

$("#dropdown-kecamatan").on("click", function () {
    var kota = $("#dropdown-kota").val();
    if (kota === "" || kota === null) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Silakan pilih Kota terlebih dahulu!",
        });
        return;
    }

    // Pastikan dropdown belum diisi
    if ($("#dropdown-kecamatan option").length === 1) {
        $.ajax({
            url: appUrl + "/api/config/getdistrict/" + kota,
            type: "GET",
            dataType: "json",
            success: function (response) {
                var $select = $("#dropdown-kecamatan");
                $select.empty();
                $select.append(
                    '<option value="" disabled selected>Pilih Kecamatan</option>'
                );

                if (Array.isArray(response.subdistricts)) {
                    $.each(response.subdistricts, function (index, item) {
                        $select.append(
                            '<option value="' +
                                item.subdistrict_id +
                                '">' +
                                item.subdistrict_name +
                                "</option>"
                        );
                    });
                } else {
                    console.warn("Respons tidak dalam format array:", response);
                }
            },
            error: function (xhr, status, error) {
                console.error("Terjadi kesalahan:", error);
            },
        });
    }
});

function validateForm() {
    var isValid = true;
    var errorMessage = "";

    $(".daftar input, .daftar select").each(function () {
        // Mengecek apakah elemen adalah input atau select
        // dan tidak memiliki atribut onchange
        if (
            $(this).is("input, select") &&
            $(this).val() === "" &&
            !$(this).attr("onchange")
        ) {
            isValid = false;
            errorMessage +=
                "<p>" + $(this).attr("placeholder") + " wajib diisi!</p>";
        }
    });

    if (!isValid) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            html: errorMessage,
        });
    }

    return isValid;
}

$(".daftar button[type='submit']").on("click", function (event) {
    event.preventDefault();

    if (validateForm()) {
        var formData = new FormData($(".daftar")[0]);
        $.ajax({
            url: appUrl + "/api/register",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            xhrFields: {
                withCredentials: true,
            },
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Sukses!",
                    text: response.message,
                });
                location.reload();
            },
            error: function (xhr) {
                var errors = xhr.responseJSON.errors;
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    html: errors,
                });
            },
        });
    }
});

document
    .getElementById("toggle-password")
    .addEventListener("click", function () {
        const passwordField = document.getElementById("password");
        const icon = this;

        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.textContent = "visibility";
        } else {
            passwordField.type = "password";
            icon.textContent = "visibility_off";
        }
    });

$("#npwp").on("input", function () {
    let a = $(this).val().replace(/\D/g, ""),
        t = "";
    a.length > 0 && (t += a.substring(0, 2)),
        a.length > 2 && (t += "." + a.substring(2, 5)),
        a.length > 5 && (t += "." + a.substring(5, 8)),
        a.length > 8 && (t += "." + a.substring(8, 9)),
        a.length > 9 && (t += "-" + a.substring(9, 12)),
        a.length > 12 && (t += "." + a.substring(12, 15)),
        $(this).val(t);
});
