var activeItem,
    csrfToken = $('meta[name="csrf-token"]').attr("content");
const imgEdit = appUrl + "/img/app/edit.svg",
    imgEditPan = appUrl + "/img/app/edit_stylus.svg";
function eventTambahan() {
    function a(a, t) {
        $.ajax({
            url: appUrl + "/api/seller/toko/updateReplyStatus",
            method: "POST",
            data: { tipe: a, status: t, update: "updateText" },
            xhrFields: { withCredentials: !0 },
            success: function (a) {
                console.log("Status reply berhasil diperbarui:", a),
                    $("#overlay").hide();
            },
            error: function (a, t, e) {
                console.error("Error saat memperbarui status reply:", e),
                    $("#overlay").hide(),
                    swal.fire(
                        "Error",
                        "Gagal memperbarui status reply. Coba lagi nanti.",
                        "error"
                    );
            },
        });
    }
    $(".horizontal-list-shadow li").click(function () {
        $(".horizontal-list-shadow li").removeClass("active"),
            $(this).addClass("active"),
            getRate($(this).data("tipe"));
    }),
        $("#btn-ubah-offline").click(function () {
            $("#message-offline").removeAttr("readonly"),
                $("#btn-simpan-offline, #btn-batal-offline").show(),
                $(this).hide();
        }),
        $("#btn-batal-offline").click(function () {
            $("#message-offline").attr("readonly", !0),
                $("#message-offline").val($(this).data("text")),
                $("#btn-simpan-offline, #btn-batal-offline").hide(),
                $("#btn-ubah-offline").show();
        }),
        $("#btn-simpan-offline").click(function () {
            $("#message-offline").attr("readonly", !0),
                $("#btn-simpan-offline, #btn-batal-offline").hide(),
                $("#btn-ubah-offline").show(),
                a("offline", $("#message-offline").val());
        }),
        $("#btn-ubah-online").click(function () {
            $("#message-online").removeAttr("readonly"),
                $("#btn-simpan-online, #btn-batal-online").show(),
                $(this).hide();
        }),
        $("#btn-batal-online").click(function () {
            $("#message-online").attr("readonly", !0),
                $("#message-online").val($(this).data("text")),
                $("#btn-simpan-online, #btn-batal-online").hide(),
                $("#btn-ubah-online").show();
        }),
        $("#btn-simpan-online").click(function () {
            $("#message-online").attr("readonly", !0),
                $("#btn-simpan-online, #btn-batal-online").hide(),
                $("#btn-ubah-online").show(),
                a("online", $("#message-online").val());
        }),
        $("#reply-online, #reply-offline").on("change", function () {
            var a,
                t,
                e = $(this).is(":checked");
            (a = "reply-online" === $(this).attr("id") ? "online" : "offline"),
                (t = e),
                $("#overlay").show(),
                $.ajax({
                    url: appUrl + "/api/seller/toko/updateReplyStatus",
                    method: "POST",
                    data: { tipe: a, status: t ? 1 : 0, update: "change" },
                    xhrFields: { withCredentials: !0 },
                    success: function (a) {
                        console.log("Status reply berhasil diperbarui:", a),
                            $("#overlay").hide();
                    },
                    error: function (a, t, e) {
                        console.error(
                            "Error saat memperbarui status reply:",
                            e
                        ),
                            $("#overlay").hide(),
                            swal.fire(
                                "Error",
                                "Gagal memperbarui status reply. Coba lagi nanti.",
                                "error"
                            );
                    },
                });
        });
    let t;
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
    }),
        $("#nama_pt,#npwp,#nama_ktp,#nik,#address_npwp,#deskripsi").on(
            "input",
            function () {
                clearTimeout(t);
                let a;
                "nama_pt" === $(this).attr("id")
                    ? (a = "nama_pt")
                    : "npwp" === $(this).attr("id")
                    ? (a = "npwp")
                    : "nama_ktp" === $(this).attr("id")
                    ? (a = "nama_ktp")
                    : "nik" === $(this).attr("id")
                    ? (a = "nik")
                    : "address_npwp" === $(this).attr("id")
                    ? (a = "address_npwp")
                    : "deskripsi" === $(this).attr("id") && (a = "deskripsi"),
                    (t = setTimeout(
                        function () {
                            var t, e;
                            let i;
                            (t = $(this)),
                                (e = a),
                                (i = t.val()),
                                $.ajax({
                                    url: "/api/seller/toko/updateProfile",
                                    method: "POST",
                                    data: { value: i, field: e },
                                    success: function (a) {
                                        console.log(a);
                                    },
                                    error: function (a, t, e) {
                                        console.error(e);
                                    },
                                });
                        }.bind(this),
                        2e3
                    ));
            }
        ),
        $("#edit_password").on("click", function () {
            $("#passwordModal").modal("show");
        }),
        $(".toggle-eye").on("click", function () {
            let a = $($(this).data("target")),
                t = "password" === a.attr("type") ? "text" : "password";
            a.attr("type", t), $(this).toggleClass("fa-eye fa-eye-slash");
        }),
        $("#savePasswordBtn").on("click", function () {
            let a = $("#old_password").val(),
                t = $("#new_password").val(),
                e = $("#confirm_password").val();
            if (!a || !t || !e) {
                Swal.fire({
                    icon: "error",
                    title: "Field Kosong",
                    text: "Semua field harus diisi!",
                });
                return;
            }
            if (t !== e) {
                Swal.fire({
                    icon: "error",
                    title: "Password tidak Sesuai",
                    text: "Password baru dan konfirmasi password tidak cocok!",
                });
                return;
            }
            $.ajax({
                url: "/api/seller/toko/updatePassword",
                method: "POST",
                data: { old_password: a, new_password: t },
                xhrFields: { withCredentials: !0 },
                success: function (a) {
                    Swal.fire({
                        icon: "success",
                        title: "Password Updated",
                        timer: 1e3,
                        showConfirmButton: !1,
                    });
                },
                error: function (a, t, e) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: a.responseJSON.message || e,
                        showConfirmButton: !0,
                    });
                },
            }),
                $("#passwordModal").modal("hide");
        }),
        $(document).on("click", ".Upload_file", function () {
            var a = $(this).data("tipe");
            $("#jenis").val(a), $("#upload_File_Modal").modal("show");
        }),
        $("#uploadBtn").on("click", function () {
            var a = new FormData($("#uploadForm")[0]);
            $.ajax({
                url: "/api/seller/toko/UploadFile",
                type: "POST",
                data: a,
                xhrFields: { withCredentials: !0 },
                processData: !1,
                contentType: !1,
                success: function (a) {
                    $("#file").val(""),
                        $("#uploadModal").modal("hide"),
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: "File berhasil diupload!",
                            timer: 2e3,
                            showConfirmButton: !1,
                        }),
                        loadData("profile");
                },
                error: function (a, t, e) {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: "Terjadi kesalahan saat mengupload file.",
                    });
                },
            });
        }),
        $("#bannerFile").on("change", function () {
            let a = this.files[0];
            if (a) {
                let t = new FileReader();
                (t.onload = function (a) {
                    $("#bannerPreview").css(
                        "background-image",
                        `url('${a.target.result}')`
                    );
                }),
                    t.readAsDataURL(a);
            }
        }),
        $(document).on("click", ".Upload_Banner", function () {
            $("#uploadBannerModal").modal("show");
        }),
        (isSubmitting = !1),
        $("#uploadBannerButton")
            .off("click")
            .on("click", function () {
                if ((console.log("Upload button clicked"), isSubmitting))
                    return;
                isSubmitting = !0;
                let a = new FormData($("#bannerForm")[0]);
                $.ajax({
                    url: "/api/seller/toko/UplaodBanner",
                    type: "POST",
                    data: a,
                    xhrFields: { withCredentials: !0 },
                    processData: !1,
                    contentType: !1,
                    success: function (a) {
                        console.log("Upload success:", a),
                            $("#bannerFile").val(""),
                            $(".card-profile-priview").css(
                                "background-image",
                                "none"
                            ),
                            $("#uploadBannerModal").modal("hide"),
                            loadData("profile"),
                            (isSubmitting = !1);
                    },
                    error: function (a, t, e) {
                        console.error("Upload error:", a.responseText),
                            (isSubmitting = !1);
                    },
                });
            }),
        $(document).on("click", ".item-banner-toko", function () {
            let a = $(this).data("urutan");
            Swal.fire({
                title: "Hapus Banner",
                text: "Apakah Anda yakin ingin menghapus banner ini?",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then((t) => {
                t.isConfirmed &&
                    $.ajax({
                        url: "/api/seller/toko/deleteBanner",
                        type: "POST",
                        data: { urutan: a, _method: "DELETE" },
                        xhrFields: { withCredentials: !0 },
                        success: function (a) {
                            Swal.fire(
                                "Dihapus!",
                                "Banner telah dihapus.",
                                "success"
                            ),
                                loadData("profile");
                        },
                        error: function (a, t, e) {
                            Swal.fire(
                                "Gagal!",
                                "Terjadi kesalahan saat menghapus banner.",
                                "error"
                            );
                        },
                    });
            });
        }),
        $(document).on("click", ".text-profile span img", function () {
            let a = $(".avatar-toko").attr("src"),
                t = $("#nama-seller").text();
            $("#editProfileModal").modal("show"),
                $("#avatarPreview").attr("src", a),
                $("#profileName").val(t);
        }),
        $("#avatarImage").on("change", function () {
            let a = this.files[0];
            if (a) {
                let t = new FileReader();
                (t.onload = function (a) {
                    $("#avatarPreview").attr("src", a.target.result);
                }),
                    t.readAsDataURL(a);
            }
        }),
        $("#add-etalase").on("click", function () {
            $("#modalTambahEtalase").modal("show");
        }),
        $("#saveProfileButton").on("click", function () {
            let a = new FormData($("#editProfileForm")[0]);
            $.ajax({
                url: "/api/seller/toko/updateProfileSeller",
                type: "POST",
                data: a,
                xhrFields: { withCredentials: !0 },
                processData: !1,
                contentType: !1,
                success: function (a) {
                    $("#editProfileModal").modal("hide"), loadData("profile");
                },
                error: function (a, t, e) {
                    console.error(a.responseText);
                },
            });
        }),
        $(document).on("change", ".status_display_etalase", function () {
            var a = $(this).data("id"),
                t = $(this).is(":checked") ? "Y" : "N";
            $.ajax({
                url: "/api/seller/toko/UpdateEtalase",
                method: "POST",
                data: { id: a, display_status: t },
                xhrFields: { withCredentials: !0 },
                success: function (a) {
                    Swal.fire("Success", "Display status updated!", "success");
                },
                error: function (a) {
                    Swal.fire("Error", "Failed to update status", "error");
                },
            });
        }),
        $(".deleteEtalase").on("click", function () {
            var a = $(this).data("id");
            Swal.fire({
                title: "Yakin hapus etalase ini?",
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: "warning",
                showCancelButton: !0,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
            }).then((t) => {
                t.isConfirmed &&
                    $.ajax({
                        url: "/api/seller/toko/delete-etalase",
                        method: "DELETE",
                        data: { etalaseId: a },
                        xhrFields: { withCredentials: !0 },
                        success: function (a) {
                            Swal.fire(
                                "Success",
                                "Etalase berhasil dihapus!",
                                "success"
                            ),
                                loadData("etalase");
                        },
                        error: function (a) {
                            Swal.fire(
                                "Error",
                                "Gagal menghapus etalase",
                                "error"
                            );
                        },
                    });
            });
        }),
        $("#formTambahEtalase").on("submit", function (a) {
            a.preventDefault(),
                $.ajax({
                    url: "/api/seller/toko/tambahEtalase",
                    method: "POST",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function (a) {
                        "success" === a.status
                            ? (Swal.fire("Berhasil", a.message, "success"),
                              $("#modalTambahEtalase").modal("hide"),
                              loadData("etalase"))
                            : Swal.fire("Gagal", a.message, "error");
                    },
                });
        });
}
function initializeDataTable(a) {
    $.fn.dataTable.isDataTable(a) && $(a).DataTable().destroy(),
        $(a).DataTable({
            bPaginate: !0,
            bLengthChange: !0,
            bFilter: !0,
            bSort: !0,
            aLengthMenu: [5, 10, 25, 50],
            bInfo: !0,
            pageLength: 5,
            bAutoWidth: !0,
            order: [[0, "asc"]],
            language: {
                emptyTable: "Belum ada Data",
                zeroRecords: "Tidak ada catatan yang cocok ditemukan",
                search: "",
                sLengthMenu: "_MENU_ ",
                oPaginate: { sPrevious: "Sebelumnya", sNext: "Selanjutnya" },
            },
        }),
        $(a + "_filter input").attr("placeholder", "Pencarian");
}
$(function () {
    var a = {
        bPaginate: !0,
        bLengthChange: !0,
        bFilter: !0,
        bSort: !0,
        bInfo: !0,
        bAutoWidth: !0,
        language: { emptyTable: "Belum ada Data" },
    };
    $("#example1").dataTable(a),
        $("#example2").dataTable(a),
        window.innerWidth <= 800 &&
            ($(".dataTables_filter input").css({
                width: "110px",
                margin: "5px",
                padding: "3px",
            }),
            $(".dataTables_length select ").css({
                width: "50px",
                margin: "5px",
            }));
}),
    $(function () {
        var a = {
            bPaginate: !0,
            bLengthChange: !0,
            bFilter: !0,
            bSort: !0,
            aLengthMenu: [10, 25, 50],
            bInfo: !0,
            bAutoWidth: !0,
            order: [[0, "asc"]],
            language: {
                emptyTable: "Belum ada Data",
                zeroRecords: "Tidak ada catatan yang cocok ditemukan",
                search: "",
                sLengthMenu: "_MENU_ ",
                oPaginate: { sPrevious: "Sebelumnya", sNext: "Selanjutnya" },
            },
        };
        $("#example1").dataTable(a), $("#example2").dataTable(a);
    });
var allItems = $(".item-box-filter-pesanan");
function loadData(a) {
    $("#overlay").show(),
        $.ajax({
            type: "GET",
            url: appUrl + "/api/seller/toko/" + a,
            xhrFields: { withCredentials: !0 },
            success: function (t) {
                var e = $("#content");
                if (
                    (e.empty(),
                    "rates_shop" === a &&
                        ((view = viewRates(t)),
                        e.append(view),
                        getRate("semua"),
                        initializeDataTable("#example2")),
                    "a_chat" === a && ((view = viewA_chat(t)), e.append(view)),
                    "profile" === a)
                ) {
                    (view = v_profile(t)), e.append(view);
                    var i = $(".mini-banner-toko");
                    i.empty();
                    var l = ViewBanner(t.banner);
                    i.append(l);
                }
                if ("etalase" === a) {
                    (view = ViewEtalase(t)),
                        e.append(view),
                        initializeDataTable("#example2");
                    var n = $("#example2 tbody");
                    n.empty();
                    var s = ViewDataEtalase(t);
                    n.append(s);
                }
                eventTambahan();
            },
            error: function (a, t, e) {
                Swal.fire("Error", "Terjadi kesalahan", "error");
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
}
function setupEvents() {
    allItems.off("click").on("click", function () {
        var a = $(this);
        a.hasClass("active")
            ? allItems.slideDown()
            : (loadData(a.data("tipe")),
              allItems.removeClass("active open").slideUp(),
              a.addClass("active open").slideDown(),
              (activeItem = a));
    });
}
function initialize() {
    allItems.hide(),
        (activeItem = allItems.first()).show().addClass("active"),
        loadData("rates_shop"),
        setupEvents();
}
function viewRates() {
    return `
     <div class="list-rates">
            <ul class="horizontal-list-shadow">
                <li class="active" data-tipe="semua" style="color: black">Semua</li>
                <li style="color: #F9AC4D; font-size: 24px;" data-tipe="5" class="bintang">★★★★★</li>
                <li style="color: #F9AC4D; font-size: 24px;" data-tipe="4" class="bintang">★★★★</li>
                <li style="color: #F9AC4D; font-size: 24px;" data-tipe="3" class="bintang">★★★</li>
                <li style="color: #F9AC4D; font-size: 24px;" data-tipe="2" class="bintang">★★</li>
                <li style="color: #F9AC4D; font-size: 24px;"data-tipe="1" class="bintang">★</li>
            </ul>
        </div>
        <div class="data-rate">
            <table id="example2" class="table" style="width: 100%; margin-top:20px">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th> Produk </th>
                        <th> Rating </th>
                        <th> Penilaian Pembeli </th>
                        <th> Balasanmu </th>
                        <th> Tanggal </th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot hidden>
                    <tr>
                        <th>Invoice</th>
                        <th> Produk </th>
                        <th> Rating </th>
                        <th> Penilaian Pembeli </th>
                        <th> Balasanmu </th>
                        <th> Tanggal </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    `;
}
function ViewEtalase() {
    return `
    <div style="margin: 20px">
        <b>Tampilkan produk unggulan kamu disini.</b>
    </div>
    <div class="etalase-toko" style="padding: 10px">
        <button class="btn-tambah-etalase" id="add-etalase">Tambah</button>
        <table id="example2" class="table" style="width: 95%; margin-top:20px">
            <thead>
                <tr>
                    <th>No</th>
                    <th> Nama Etalase </th>
                    <th> Jumlah Produk </th>
                    <th> Tampilkan </th>
                    <th> Aksi </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot hidden>
                <tr>
                    <th>No</th>
                    <th> Nama Etalase </th>
                    <th> Jumlah Produk </th>
                    <th> Tampilkan </th>
                    <th> Aksi </th>
                </tr>
            </tfoot>
        </table>
    </div>
    `;
}
function viewDataRate(a) {
    var t = "";
    return (
        a.forEach(function (a, e) {
            t += `
                <tr>
                    <td>${a.invoice}</td>
                    <td>${a.name}</td>
                    <td>${(function a(t) {
                        for (var e = "", i = 0; i < t; i++)
                            e +=
                                "<p style='color: #F9AC4D; display: inline;'>★</p>";
                        return e;
                    })(a.rating)}</td>
                    <td>${a.user_message ? a.user_message.message : ""}</td>
                    <td>${
                        a.seller_message
                            ? a.seller_message.message
                            : " <p class='btn CreateMassageRates'> Buat Balasan ? </p>"
                    }</td>
                    <td>${a.user_message ? a.user_message.created : ""}</td>
                </tr>
            `;
        }),
        t
    );
}
function ViewDataEtalase(a) {
    var t = "";
    return (
        a.forEach(function (a, e) {
            let i = "Y" === a.display_status ? "checked" : "";
            t += `
                <tr>
                    <td>${e + 1}</td>
                    <td> ${a.name} </td>
                    <td> ${a.cp} </td>
                    <td>
                        <label class="switch">
                            <input type="checkbox" class="status_display_etalase" ${i}
                            data-id="${a.id}">
                            <span class="slider round"></span>
                        </label>
                    </td>
                    <td style="display:flex">
                        <button class="btn-etalase">Tambah Produk</button>
                        <button class="btn-etalase deleteEtalase" data-id="${
                            a.id
                        }">Hapus</button>
                    </td>
                </tr>
            `;
        }),
        t
    );
}
function getRate(a) {
    $("#overlay").show(),
        $.ajax({
            url: appUrl + "/api/seller/toko/getRate/" + a,
            method: "GET",
            xhrFields: { withCredentials: !0 },
            success: function (a) {
                var t = a.original,
                    e = $("#example2 tbody");
                e.empty(),
                    $.fn.DataTable.isDataTable("#example2") &&
                        $("#example2").DataTable().clear().destroy();
                var i = viewDataRate(t);
                e.append(i), initializeDataTable("#example2");
            },
            error: function (a, t, e) {
                console.error("AJAX Error:", e);
            },
            complete: function () {
                $("#overlay").hide();
            },
        });
}
function viewA_chat(a) {
    let t = "Y" === a.autoreply_standar ? "checked" : "",
        e = "Y" === a.autoreply_offline ? "checked" : "";
    var i = "";
    return (
        i +
        `
    <b style="margin-left: 20px; margin-top: 20px;">Gunakan fitur Asisten Chat untuk memberikan layanan yang lebih
        efisien kepada Pembeli.</b>
    <div class="data-auto-reply">
        <h4 style="margin-left: 20px;"><b>Auto Reply</b></h4>
        <div id="view-oprasional">
            <div>
                <b style="color: #DFF7FF">Standart Auto Reply</b>
                <p>
                    Ketika diaktifkan, Pembeli akan menerima balasan otomatis setelah mengirimkan
                    pesan pertama setiap harinya.
                </p>
            </div>
            <div>
                <label class="switch">
                    <input type="checkbox" id="reply-online" ${t}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
        <div class="message-chat">
            <b style="color: #DFF7FF">Pesan</b>
            <br>
            <input style="width: 80%" type="text" name="message-online" id="message-online" class="input-underline" value="${a.autoreply_standar_text}" readonly>
            <br>
            <br>
            <button id="btn-ubah-online" class="button-yellow">Ubah</button>
            <button id="btn-simpan-online" class="button-green" style="display: none;">Simpan</button>
            <button id="btn-batal-online" class="button-red" style="display: none;" data-text="${a.autoreply_offline_text}">Batal</button>
        </div>
    </div>
    <div class="data-auto-reply-offline">
        <div id="view-oprasional">
            <div>
                <b style="color: #DFF7FF">Auto Reply Offline</b>
                <p>
                    Ketika diaktifkan, pesan yang dikirim Pembeli di luar jam operasional akan
                    otomatis dibalas dengan auto-reply offline.
                </p>
            </div>
            <div>
                <label class="switch">
                    <input type="checkbox" id="reply-offline" ${e}>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>
       <div class="message-chat">
            <b style="color: #DFF7FF">Pesan</b>
            <br>
            <input style="width: 80%" type="text" name="message-offline" id="message-offline" class="input-underline" value="${a.autoreply_offline_text}" readonly>
            <br>
            <br>
            <button id="btn-ubah-offline" class="button-yellow">Ubah</button>
            <button id="btn-simpan-offline" class="button-green" style="display: none;">Simpan</button>
            <button id="btn-batal-offline" class="button-red" style="display: none;" data-text="${a.autoreply_offline_text}">Batal</button>
        </div>

    </div>
    `
    );
}
function v_profile(a) {
    var t,
        e = a.data.created_date,
        i = new Date(e),
        l = a.lampiran.is_approved,
        nn = getFullUrl(a.lampiran.akta_pendirian);
        ns = getFullUrl(a.lampiran.akta);
        no = getFullUrl(a.lampiran.ktp);
        nr = getFullUrl(a.lampiran.pkp);
        nd = getFullUrl(a.lampiran.npwp);
        np = getFullUrl(a.lampiran.nib);

        (n = a.lampiran && nn ? nn : null),
        (s = a.lampiran && ns ? ns : null),
        (o = a.lampiran && no ? no : null),
        (r = a.lampiran && nr ? nr : null),
        (d = a.lampiran && nd ? nd : null),
        (p = a.lampiran && np ? np : null),
        (u = i.getDate()),
        (c = `${u} ${
            [
                "Januari",
                "Februari",
                "Maret",
                "April",
                "Mei",
                "Juni",
                "Juli",
                "Agustus",
                "September",
                "Oktober",
                "November",
                "Desember",
            ][i.getMonth()]
        } ${i.getFullYear()}`),
        (h = a.image_banner ? a.image_banner : null);
    if (null != h) var f = -1 === h.indexOf("http");
    var b = a.data.avatar ? a.data.avatar : null;
    if (null != b) var f = -1 === b.indexOf("http");
    var m = "";
    return (
        m +
        `
    <div style="margin: 20px">
        <b>Lihat Toko dan update profile toko Anda.</b>
    </div>
    <div class="data-profile">
        <div class="profile-toko">
            <div class="data-diri-toko">
                <div class="card-profile"
                    style="background-image: url('${
                        f ? "http://127.0.0.1:8001/" + h : h
                    }');">
                    <div class="text-profile">
                        <div>
                            <img class="avatar-toko" src="${
                                f
                                    ? "http://127.0.0.1:8001/seller_center/" + b
                                    : b
                            }"
                                alt="Icon-toko">
                        </div>
                        <div class="data-toko">
                            <h2 id="nama-seller">${a.data.name}</h2>
                            <span><img src="${imgEdit}" alt="edit" style="width:30px; height:30px"></span>
                            <p>Waktu Bergabung ${c}</p>
                            <div style="display: flex">
                                <p>Pengikut ${
                                    a.follower == null
                                        ? 0
                                        : a.follower.follower ?? 0
                                }</p>
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <p>Mengikuti ${
                                    a.follower == null
                                        ? 0
                                        : a.follower.following ?? 0
                                }</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="info-toko-profile">
                    <p>Preview Toko</p>
                    <span>lihat</span>
                </div>
                <div class="garisPutihPoint"></div>
                <div class="info-toko-profile">
                    <p>Produk</p>
                    <span>${a.produk}</span>
                </div>
                <div class="garisPutihPoint"></div>
                <div class="info-toko-profile">
                    <p>Penilaian Toko</p>
                    <span>${a.rate ? a.rate : 0} / 5</span>
                </div>
                <div class="garisPutihPoint"></div>
                <div class="info-toko-profile">
                    <p>Tingkat pesanan tidak terselesaikan</p>
                    <span>${a.count}%</span>
                </div>
                <div class="garisPutihPoint"></div>
                <div class="info-toko-profile" id="edit_password">
                    <p>Ganti Pasword</p>
                    <span><img src="${imgEditPan}" alt="edit" style="width:30px; height:30px"></span>
                </div>
            </div>
            <div class="list-data-diri-toko">
                <ul class="large-bullet-list">
                    <li>
                        <h4><b>Nama Usaha / PT</b></h4>
                        <input style="margin-top:0px; width:80%; font-size:15px;" type="text" id="nama_pt" value="${
                            a.data.nama_pt
                        }"
                            class="input-underline">
                    </li>
                    <li>
                        <h4><b>NPWP</b></h4>
                        <input style="margin-top:0px; width:80%; font-size:15px;" type="text" id="npwp" value="${
                            a.data.npwp
                        }"
                            class="input-underline">
                    </li>
                    <li>
                        <h4><b>Nama Pemilik sesuai KTP</b></h4>
                        <input style="margin-top:0px; width:80%; font-size:15px;" type="text" id="nama_ktp" value="${
                            a.data.nama_pemilik
                        }"
                            class="input-underline">
                    </li>
                    <li>
                        <h4><b>NIK Pemilik sesuai KTP</b></h4>
                        <input style="margin-top:0px; width:80%; font-size:15px;" type="text" id="nik" value="${
                            a.data.nik_pemilik
                        }"
                            class="input-underline">
                    </li>
                </ul>
            </div>
        </div>
        <div class="document-profile-toko">
            <p>Dokumen Yang Diperlukan</p>
            <small style="color: yellow">
                Lampiran file hanya menerima format : png, jpg, jpeg, pdf <br>
                Dokumen berhasil disetujui tidak dapat dirubah!
            </small>
            <table style="width: 100%">
                <tr>
                    <td>
                        <ul class="large-bullet-list">
                            <li>
                                <h4><b>Akta Pendirian</b></h4>
                                ${
                                    1 === l
                                        ? `<a href="${n}" target="_blank" class="btn file-profile-toko">Buka File</a>`
                                        : n
                                        ? '<p class="btn file-profile-toko Upload_file" data-tipe="akta_pendirian">Ganti File</p>'
                                        : '<p class="btn file-profile-toko Upload_file" data-tipe="akta_pendirian">Upload</p>'
                                }
                                ${
                                    n
                                        ? `<div class="box-shadow"><a href="${n}" target="_blank">Lihat file sebelumnya</a></div>`
                                        : ""
                                }
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="large-bullet-list">
                            <li>
                                <h4><b>Akta Perubahan</b></h4>
                                ${
                                    1 === l
                                        ? `<a href="${s}" target="_blank" class="btn file-profile-toko">Buka File</a>`
                                        : s
                                        ? '<p class="btn file-profile-toko Upload_file" data-tipe="akta_perubahan">Ganti File</p>'
                                        : '<p class="btn file-profile-toko Upload_file" data-tipe="akta_perubahan">Upload</p>'
                                }
                                ${
                                    s
                                        ? `<div class="box-shadow"><a href="${s}" target="_blank">Lihat file sebelumnya</a></div>`
                                        : ""
                                }
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="large-bullet-list">
                            <li>
                                <h4><b>NIB</b></h4>
                                ${
                                    1 === l
                                        ? `<a href="${p}" target="_blank" class="btn file-profile-toko">Buka File</a>`
                                        : p
                                        ? '<p class="btn file-profile-toko Upload_file" data-tipe="nib">Ganti FIle</p>'
                                        : '<p class="btn file-profile-toko Upload_file" data-tipe="nib">Upload</p>'
                                }
                                ${
                                    p
                                        ? `<div class="box-shadow"><a href="${p}" target="_blank">Lihat file sebelumnya</a></div>`
                                        : ""
                                }
                            </li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <td>
                        <ul class="large-bullet-list">
                            <li>
                                <h4><b>NPWP</b></h4>
                                ${
                                    1 === l
                                        ? `<a href="${d}" target="_blank" class="btn file-profile-toko">Buka File</a>`
                                        : d
                                        ? '<p class="btn file-profile-toko Upload_file" data-tipe="npwp">Ganti File</p>'
                                        : '<p class="btn file-profile-toko Upload_file" data-tipe="npwp">Upload</p>'
                                }
                                ${
                                    d
                                        ? `<div class="box-shadow"><a href="${d}" target="_blank">Lihat file sebelumnya</a></div>`
                                        : ""
                                }
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="large-bullet-list">
                            <li>
                                <h4><b>PKP</b></h4>
                                ${
                                    1 === l
                                        ? `<a href="${r}" target="_blank" class="btn file-profile-toko">Buka File</a>`
                                        : r
                                        ? '<p class="btn file-profile-toko Upload_file" data-tipe="pkp">Ganti File</p>'
                                        : '<p class="btn file-profile-toko Upload_file" data-tipe="pkp">Upload</p>'
                                }
                                ${
                                    r
                                        ? `<div class="box-shadow"><a href="${r}" target="_blank">Lihat file sebelumnya</a></div>`
                                        : ""
                                }
                            </li>
                        </ul>
                    </td>
                    <td>
                        <ul class="large-bullet-list">
                            <li>
                                <h4><b>KTP Direktur Utama</b></h4>
                                ${
                                    1 === l
                                        ? `<a href="${o}" target="_blank" class="btn file-profile-toko">Buka File</a>`
                                        : o
                                        ? '<p class="btn file-profile-toko Upload_file" data-tipe="ktp">Ganti File</p>'
                                        : '<p class="btn file-profile-toko Upload_file" data-tipe="ktp">Upload</p>'
                                }
                                ${
                                    o
                                        ? `<div class="box-shadow"><a href="${o}" target="_blank">Lihat file sebelumnya</a></div>`
                                        : ""
                                }
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="data-alamat-toko">
        <div class="profile-alamat-toko">
            <ul class="large-bullet-list">
                <li>
                    <h4><b>Alamat Sesuai NPWP</b></h4>
                    <input style="margin-top:0px; width:90%; font-size:15px;" type="text" id="address_npwp" value="${
                        a.data.npwp_address ? a.data.npwp_address : "-"
                    }" class="input-underline">
                </li>
                <li>
                    <h4><b>Deskripsi Toko</b></h4>
                    <input style="margin-top:0px; width:90%; font-size:15px;" type="text" id="deskripsi" value="${
                        a.data.description ? a.data.description : "-"
                    }" class="input-underline">
                </li>
                <li>
                    <h4><b>Kategoti Toko</b></h4>
                    <input style="margin-top:0px; width:90%; font-size:15px;" type="text" value="${
                        a.data.category
                    }" class="input-underline" disabled>
                </li>
            </ul>
            <b>Mini Banner</b>
            <div class="mini-banner-toko">
            </div>
        </div>
    </div>
    `
    );
}
function ViewBanner(a) {
    var t = "";
    a.forEach(function (a, e) {
        var i = a.image ? a.image : null,
            l = "";
        null != i &&
            (l = -1 === i.indexOf("http") ? "http://127.0.0.1:8001/" + i : i),
            (t += `
            <div class="box-shadow img-banner-toko">
                <img src="${l}" class="item-banner-toko"  data-urutan=${a.urutan}  width="300px" height="100px" alt="banner">
            </div>
        `);
    });
    for (var e = 3 - a.length, i = 0; i < e; i++)
        t += `
            <div class="box-shadow img-banner-toko Upload_Banner">
                <p align="center">Tambah Banner</p>
            </div>
        `;
    return t;
}

function getFullUrl(filePath) {
    const baseUrl = "http://127.0.0.1:8001/seller_center/";
    return filePath.indexOf("http") === -1 ? baseUrl + filePath : filePath;
}

$(document).on("click", allItems.filter(".open"), function () {
    setupEvents();
});
