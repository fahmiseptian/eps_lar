var csrfToken = $('meta[name="csrf-token"]').attr("content");

$(function () {
    // Konfigurasi DataTables untuk kedua tabel
    var dataTableOptions = {
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
        language: {
            emptyTable: "Belum ada Data", // Pesan untuk tabel kosong
        },
    };

    // Inisialisasi DataTables
    $("#example1").dataTable(dataTableOptions);
    $("#example2").dataTable(dataTableOptions);

    // Jika lebar jendela kurang dari atau sama dengan 800 piksel, atur lebar kolom pencarian
    if (window.innerWidth <= 800) {
        $(".dataTables_filter input").css({
            width: "110px",
            margin: "5px",
            padding: "3px",
        });
        $(".dataTables_length select ").css({
            width: "50px",
            margin: "5px",
        });
    }
});

function formatRupiah(angka) {
    var number_string = angka.toString().replace(/[^,\d]/g, "");
    var split = number_string.split(",");
    var sisa = split[0].length % 3;
    var rupiah = split[0].substr(0, sisa);
    var ribuan = split[0].substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        var separator = sisa ? "." : "";
        rupiah += separator + ribuan.join(".");
    }

    rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
    return "Rp. " + rupiah;
}

function unformatRupiah(formattedRupiah) {
    var number_string = formattedRupiah.replace(/[^,\d]/g, "");
    return parseInt(number_string.replace(/[.,]/g, ""));
}

var allItems = $(".item-box-filter-pesanan");
var activeItem;

function loadData(tipe) {
    $("#overlay").show();

    $.ajax({
        type: "GET",
        url: appUrl + "/api/seller/info/" + tipe,
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var body = $("#content");
            body.empty();

            if (tipe === "health") {
                view = viewHealth(response);
                body.append(view);
                eventTambahan();
            }

            if (tipe === "toko") {
                view = viewInfoToko(response);
                body.append(view);
                eventTambahan();
                getDataRank("penjualan");
                getRankCategory();
            }

            if (tipe === "faq") {
                view = viweFaq();
                body.append(view);
                eventTambahan();
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
    allItems.off("click").on("click", function () {
        var $this = $(this);

        if ($this.hasClass("active")) {
            allItems.slideDown();
        } else {
            var tipe = $this.data("tipe");
            loadData(tipe);
            allItems.removeClass("active open").slideUp();

            $this.addClass("active open").slideDown();
            activeItem = $this;
        }
    });
}

function initialize() {
    allItems.hide();
    activeItem = allItems.first();
    activeItem.show().addClass("active");
    loadData("health");
    setupEvents();
}
function initializeDataTable(selector) {
    if ($.fn.dataTable.isDataTable(selector)) {
        $(selector).DataTable().destroy();
    }

    $(selector).DataTable({
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bSort: true,
        aLengthMenu: [5, 10, 25, 50],
        bInfo: true,
        pageLength: 5,
        bAutoWidth: true,
        order: [[0, "asc"]],
        language: {
            emptyTable: "Belum ada Data",
            zeroRecords: "Tidak ada catatan yang cocok ditemukan",
            search: "",
            sLengthMenu: "_MENU_ ",
            oPaginate: {
                sPrevious: "Sebelumnya",
                sNext: "Selanjutnya",
            },
        },
    });

    $(selector + "_filter input").attr("placeholder", "Pencarian");
}

function eventTambahan() {
    $(".horizontal-list-shadow li").on("click", function () {
        var tipe = $(this).data("tipe"); // Mendapatkan nilai data-tipe dari item yang diklik

        $(".horizontal-list-shadow li").removeClass("active"); // Menghapus kelas 'active' dari semua item
        $(this).addClass("active"); // Menambahkan kelas 'active' ke item yang diklik
        $("#overlay").show();

        getDataRank(tipe);
    });
}

function getDataRank(tipe) {
    $.ajax({
        url: appUrl + "/api/seller/info/rank-produk/" + tipe,
        method: "GET",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var tbody = $("#example2 tbody");
            tbody.empty();

            // Hapus instance DataTable sebelumnya
            if ($.fn.DataTable.isDataTable("#example2")) {
                $("#example2").DataTable().clear().destroy();
            }

            // Tambah data baru ke tabel
            var data = viewDataPeringkat(response);
            tbody.append(data);

            // Inisialisasi DataTable setelah menambahkan data
            initializeDataTable("#example2");
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function getRankCategory() {
    $.ajax({
        url: appUrl + "/api/seller/info/rank-kategori",
        method: "GET",
        xhrFields: {
            withCredentials: true,
        },
        success: function (response) {
            var tbody = $("#example1 tbody");
            tbody.empty();

            // Hapus instance DataTable sebelumnya
            if ($.fn.DataTable.isDataTable("#example1")) {
                $("#example1").DataTable().clear().destroy();
            }

            // Tambah data baru ke tabel
            var data = viewPeringkatCategory(response);
            tbody.append(data);

            // Inisialisasi DataTable setelah menambahkan data
            initializeDataTable("#example1");
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
        },
        complete: function () {
            $("#overlay").hide();
        },
    });
}

function viewHealth(data) {
    var count_tidak_terselesaikan = Math.round(
        (data.tidak_terselesaikan / data.count_order) * 100
    );
    var count_pembatalan = Math.round(
        (data.pembatalan / data.count_order) * 100
    );
    var count_pengembalian = Math.round(
        (data.pengembalian / data.count_order) * 100
    );
    var count_pengemasan = Math.round(
        (data.pengemasan / data.count_order) * 100
    );
    var view = "";
    view += `
        <div class="container-health">
                <div class="top-view-health">
                    <h3>Produk Yang Dilarang</h3>
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 13px;">
                        <tr>
                            <th style="width: 40%">Statistik</th>
                            <th style="width: 30%">Toko Saya</th>
                            <th>Aksi</th>
                        </tr>
                        <tr>
                            <td>Pelanggaran Produk Berat</td>
                            <td>${data.pelanggaran_produk_berat}</td>
                            <td id="view-pelanggaran-berat">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Produk Spam</td>
                            <td>${data.produk_spam}</td>
                            <td id="view-produk-spam">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Produk Imitasi</td>
                            <td>${data.produk_imitasi}</td>
                            <td id="view-produk-imitasi">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Produk yang Dilarang</td>
                            <td>${data.produk_yang_dilarang}</td>
                            <td id="view-produk-banned">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Pelanggaran Produk Ringan</td>
                            <td>${data.pelanggaran_produk_ringan}</td>
                            <td id="view-pelanggaran-ringan">Lihat Rincian</td>
                        </tr>
                    </table>
                </div>
                <div class="center-view-health">
                    <h3>Pesanan Terselesaikan</h3>
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 13px;">
                        <tr>
                            <th style="width: 40%">Statistik</th>
                            <th style="width: 30%">Toko Saya</th>
                            <th>Aksi</th>
                        </tr>
                        <tr>
                            <td>Tingkat Pesanan Tidak Terselesaikan</td>
                            <td>${count_tidak_terselesaikan ? count_tidak_terselesaikan : 0}%</td>
                            <td id="view-pesanan-tidak-selesai">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Tingkat Pembatalan</td>
                            <td>${count_pembatalan ? count_pembatalan : 0}%</td>
                            <td id="view-pesanan-batal">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Tingkat Pengembalian</td>
                            <td>${count_pengembalian ? count_pengembalian : 0}%</td>
                            <td id="view-pesanan-pengebalian">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Masa Pengemasan</td>
                            <td>${count_pengemasan ? count_pengemasan : 0} hari</td>
                            <td id="view-waktu-pengemasan">Lihat Rincian</td>
                        </tr>
                    </table>
                </div>
                <div class="bottom-view-health">
                    <h3>Pelayanan Pembeli</h3>
                    <table style="width: 100%; border-collapse: separate; border-spacing: 0 13px;">
                        <tr>
                            <th style="width: 40%">Statistik</th>
                            <th style="width: 30%">Toko Saya</th>
                            <th>Aksi</th>
                        </tr>
                        <tr>
                            <td>Persentase Chat Dibalas</td>
                            <td>${data.total_percentage_chat ? data.total_percentage_chat : 0}%</td>
                            <td id="view-chat-dibales">Lihat Rincian</td>
                        </tr>
                        <tr>
                            <td>Waktu Chat Dibalas</td>
                            <td>${data.total_chat_time} hari</td>
                            <td id="view-chat-time">Lihat Rincian</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        `;
    return view;
}

function viewInfoToko(data) {
    // var RankProduk = viewDataPeringkat(data);
    var countPesanan = Math.round(data.penjualan / data.pesanan);
    var view = "";
    view += `
        <div class="info-data">
            <div class="top-view-info"
                style="">
                <div class="card-item--info">
                    <p>Penjualan
                        <i style="font-size:smaller;" class="material-icons">info</i>
                        <span class="tooltip-text">Total Semua nilai pesanan, termasuk yang di batalkan dan dikembalikan</span>
                    </p>
                    <b> ${formatRupiah(data.penjualan ? data.penjualan : 0)} </b>
                </div>
                <div class="card-item--info">
                    <p>Pesanan
                        <i style="font-size:smaller;" class="material-icons">info</i>
                        <span class="tooltip-text">Total jumlah pesanan siap dikirim dalam jangka waktu tertentu, termasuk pesanan yang dibatalkan dan dikembalikan.</span>
                    </p>
                    <b> ${data.pesanan} </b>
                </div>
                <div class="card-item--info">
                    <p>Pengunjung
                        <i style="font-size:smaller;" class="material-icons">info</i>
                        <span class="tooltip-text">Jumlah pengunjung baru yang melihat halaman toko dalam jangka waktu yang ditentukan. Beberapa kunjungan dari 1 halaman oleh pengunjung yang sama akan dihitung sebagai 1 kunjungan.</span>
                    </p>
                    <b> ${data.views} </b>
                </div>
                <div class="card-item--info">
                    <p>Produk Dilihat
                        <i style="font-size:smaller;" class="material-icons">info</i>
                        <span class="tooltip-text">Jumlah rincian produkmu yang dilihat dari aplikasi dan situs dalam jangka waktu yang ditentukan.</span>
                    </p>
                    <b> ${data.produk ? data.produk : 0} </b>
                </div>
                <div class="card-item--info">
                    <p>per Pesanan
                        <i style="font-size:smaller;" class="material-icons">info</i>
                        <span class="tooltip-text">Rata-rata total produk yang terjual dalam satu kali pembelian dalam jangka waktu tertentu. Dapat dihitung sebagai jumlah penjualan dibagi total pesanan.</span>
                    </p>
                    <b> ${formatRupiah(countPesanan ? countPesanan : 0)} </b>
                </div>
            </div>
            <div class="view-info-left">
                <h3> Peringakat Produk</h3>
                <ul class="horizontal-list-shadow">
                    <li class="active" data-tipe="penjualan">Berdasarkan Penjualan</li>
                    <li data-tipe="dilihat">Berdasarkan Produk Dilihat</li>
                </ul>
                <table id="example2" class="table" style="width: 90%; margin-top:20px">
                    <thead>
                        <tr>
                            <th>Perngkat</th>
                            <th> Produk </th>
                            <th> Harga </th>
                            <th> Jumlah </th>
                            <th> Diperbaharui </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot hidden>
                        <tr>
                            <th>Perngkat</th>
                            <th> Produk</th>
                            <th> Harga</th>
                            <th> Jumlah</th>
                            <th> Diperbaharui</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="view-info-right">
                <h3 style="margin-left: 15px"> Peringakat Kategori</h3>
                <p style="margin-left: 15px"> Berdasarkan Penjualan</p>
                <table id="example1" class="table" style="width: 90%; margin-top:20px:">
                    <thead>
                        <tr>
                            <th>Perngkat</th>
                            <th> Kategori </th>
                            <th> Terjual </th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot hidden>
                        <tr>
                            <th>Perngkat</th>
                            <th> Kategori</th>
                            <th> Terjual</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    `;
    return view;
}

function viewDataPeringkat(dataArray) {
    var row = ""; // Inisialisasi row sebagai string kosong
    dataArray.forEach(function (data, index) {
        row += `
            <tr>
                <td>${index + 1}</td>
                <td>${data.info}</td>
                <td>${formatRupiah(data.price)}</td>
                <td>${data.count}</td>
                <td>${data.last_update}</td>
            </tr>
        `;
    });
    return row;
}

function viewPeringkatCategory(dataArray) {
    var row = ""; // Inisialisasi row sebagai string kosong
    dataArray.forEach(function (data, index) {
        row += `
            <tr>
                <td>${index + 1}</td>
                <td>${data.name}</td>
                <td>${data.count}</td>
            </tr>
        `;
    });
    return row;
}

function viweFaq() {
    var view = "";
    view += `
        <div class="data-faq">
            <h3>FAQ</h3>
            <div class="text-faq">
                <h6>Apa manfaat menggunakan Bisnis Saya?</h6>
                <p>
                    Dengan menggunakan Bisnis Saya, kamu dapat:<br>
                    Melihat dan mengerti tren berdasarkan data tokomu.<br>
                    Mendalami data hingga ke tingkat produk agar kamu dapat menyesuaikan strategimu.<br>
                    Memahami kriteria penjualan dan meningkatkan penjualanmu.<br>
                    Memantau performa real-timemu<br>
                </p>
                <div class="garis-putih"></div>
                <h6>Saya tidak mengerti metrik di dalam Bisnis Saya. Di mana saya bisa mempelajarinya lebih lanjut?</h6>
                <p>
                    Kamu dapat melihat artikel ini pada Pusat Edukasi Penjual.
                </p>
                <div class="garis-putih"></div>
                <h6>Seberapa sering data diperbarui?</h6>
                <p>
                    Data Real-Time diperbarui setiap 30 detik. Data untuk Halaman Utama, Produk, Penjualan, dan Promosi diperbarui setiap jam. Semua data lain diperbarui setiap hari pada pukul 09:00 WIB.
                </p>
                <div class="garis-putih"></div>
                <h6>Mengapa angka penjualan saya berbeda dari pendapatan yang ditampilkan pada Penghasilan Saya?</h6>
                <p>
                    Penjualan dalam Bisnis Saya adalah total pembelian Pembeli (harga yang dibayar setelah Pembeli menggunakan Koin Elite Proxy atau voucher Elite Proxy). Penghasilan Saya menunjukkan jumlah yang kamu terima, meliputi jumlah yang diterima dari Koin Elite Proxy atau voucher Elite Proxy.
                </p>
                <div class="garis-putih"></div>
                <h6>Mengapa Tingkat Konversi lebih dari 100%?</h6>
                <p>
                    Hal ini karena Tingkat Konversi dihitung dari metrik yang relevan dalam jangka waktu yang dipilih. Sebagai contoh, Tingkat Konversi (Pesanan Siap Dikirim dibagi Pesanan Dibuat) pada Tinjauan Penjualan dihitung dari pesanan dibuat dan jumlah pesanan siap dikirim dalam jangka waktu yang dipilih. Jika ada pesanan yang dibuat dalam jangka waktu sebelumnya tetapi siap dikirim dalam jangka waktu yang dipilih, ini dapat mengakibatkan tingkat konversi lebih dari 100%.
                </p>
                <div class="garis-putih"></div>
                <h6>Bagaimana cara saya melihat data untuk semua produk saya?</h6>
                <p>
                    Fungsi Download Data memungkinkan kamu untuk men-download data untuk 1000 produk dalam jangka waktu yang dipilih.
                </p>
                <div class="garis-putih"></div>
                <h6>Bagaimana cara melihat data pada tingkat variasi di tab Produk?</h6>
                <p>
                    Temukan produk dengan variasi di halaman Peringkat Produk, Performa Produk, atau Analisis Produk. Klik panah kecil di samping foto produk untuk melihat data per variasi.
                </p>
                <div class="garis-putih"></div>
                <h6>Apa itu pesanan live streaming?</h6>
                <p>
                    Pesanan langsung adalah pesanan live streaming yang dilakukan dalam 7 hari sejak produk dimasukkan ke dalam keranjang saat menonton live streaming, siaran ulang, atau dari halaman produk saat menonton live streaming. Pesanan tidak langsung adalah semua pesanan produk live streaming yang dibuat penonton dalam 24 jam sejak menonton live streaming.
                </p>
                <div class="garis-putih"></div>
                <h6>Mengapa Persentase Chat Dibalas dalam Bisnis Saya berbeda dengan Persentase Chat Dibalas dalam Kesehatan Toko?</h6>
                <p>
                    Perhitungan Persentase Chat Dibalas pada Bisnis Saya dihitung berdasarkan jumlah chat dibalas dalam jangka waktu yang dipilih dan termasuk chat yang diterima ketika fitur Toko Libur diaktifkan. Persentase Chat Dibalas dalam Kesehatan Toko adalah rata-rata tanggapan terhadap chat dan penawaran yang diterima dalam 90 hari terakhir, di mana persentase yang lebih tinggi diberikan pada 25% chat baru yang diterima dalam 90 hari terakhir, dan tidak termasuk chat yang diterima ketika Toko Libur diaktifkan.
                </p>
                <div class="garis-putih"></div>
                <h6>Apa keuntungan dari Panduan Penjualan?</h6>
                <p>
                    Panduan Penjualan dapat membantu mengidentifikasi kesempatan penjualan baru dan mengoptimalkan konversi produkmu.
                </p>
                <div class="garis-putih"></div>
                <h6>Mengapa nilai metrik saya menunjukkan "-"?</h6>
                <p>
                    Nilai metrik menunjukkan "-", karena data sedang diproses dan belum diperbarui. Data diproses dari pk. 00:00-09:00 WIB setiap harinya, mohon periksa data yang diperbarui setelah pk. 09:00 WIB.
                </p>
                <div class="garis-putih"></div>
                <h6>Mengapa riwayat data saya untuk Pesanan dan Penjualan yang Dibatalkan dan Dikembalikan berubah?</h6>
                <p>
                    Data Pesanan dan Penjualan yang Dibatalkan dan Dikembalikan dicatat berdasarkan tanggal pesanan dibuat/dibayar, bukan tanggal pembatalan/pengembalian. Oleh karena itu, riwayat data untuk hari yang telah berlalu akan diperbarui lagi jika ada pesanan untuk hari tersebut yang berhasil dibatalkan/dikembalikan. Sebagai contoh, kamu memiliki pesanan yang dibuat pada 8 Agustus dan berhasil dibatalkan pada 10 Agustus. Jika kamu memeriksa riwayat data untuk 8 Agustus pada 9 Agustus, pesanan tersebut belum akan ditampilkan sebagai dibatalkan karena masih diproses untuk pembatalan. Namun, jika kamu memeriksa riwayat data yang sama untuk 8 Agustus pada 11 Agustus, data akan menunjukkan peningkatan jumlah pesanan yang dibatalkan, karena pesanan tersebut telah berhasil dibatalkan.
                </p>
                <div class="garis-putih"></div>
                <h6>Apa perbedaan antara Pesanan Dibuat, Dibayar, dan Siap Dikirim?</h6>
                <p>
                    "Pesanan Dibuat adalah pesanan yang berhasil dibuat oleh Pembeli, termasuk yang sudah dibayar dan belum dibayar. Pesanan Dibayar adalah pesanan yang telah dibayar oleh Pembeli. Pesanan Siap Dikirim* adalah pesanan non-COD yang telah dibayar dan pesanan COD yang telah dikonfirmasi untuk pengiriman (kurang lebih 30 menit setelah pesanan dibuat). *Pesanan Siap Dikirim hanya tersedia di negara yang mendukung layanan COD."
                </p>
                <div class="garis-putih"></div>
                <h6>Mengapa data penjualan dan pesanan untuk Pesanan Dibuat/Dibayar/Siap Dikirim tidak sesuai meskipun saya sudah memilih periode tanggal yang sama?</h6>
                <p>
                    "Pesanan dapat memiliki tiga status berbeda: Dibuat, Dibayar, dan Siap Dikirim*. Data untuk status-status tersebut dihitung berdasarkan kriteria yang berbeda. Pesanan Dibuat dihitung berdasarkan tanggal pesanan dibuat. Pesanan Dibayar dihitung berdasarkan tanggal pesanan dibayar. Pesanan Siap Dikirim dihitung berdasarkan: 1) tanggal pesanan non-COD dibayar, atau 2) tanggal pesanan COD terkonfirmasi untuk pengiriman. *Pesanan Siap Dikirim hanya tersedia di negara yang mendukung layanan COD."
                </p>
            </div>
        </div>
    `;
    return view;
}
