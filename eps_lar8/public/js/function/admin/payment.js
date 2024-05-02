$(function () {
    $("#example1").dataTable();
    $("#example2").dataTable({
        bPaginate: true,
        bLengthChange: false,
        bFilter: false,
        bSort: true,
        bInfo: true,
        bAutoWidth: true,
    });
});

function paymentAdd() {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Adding New Payment",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });
    
    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Menampilkan SweetAlert dengan formulir tambah payment
    Swal.fire({
        title: 'Adding New Payment',
        width: '50%',
        html:
        '<table border=0 style="width: 100%;">' +
            '<tbody>' +
                '<tr>' +
                    '<td style="width: 50%;">' +
                        '<table border=0 style="width: 100%;">' +
                            '<tbody>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-name">Name</label></td>' +
                                    '<td width="70%"><input id="add-name" class="swal2-input" placeholder="Name"></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-code">Code</label></td>' +
                                    '<td width="70%"><input id="add-code" class="swal2-input" placeholder="Code"></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-fee_nominal">Fee Nominal (Rp)</label></td>' +
                                    '<td width="70%"><input id="add-fee_nominal" class="swal2-input" placeholder="Fee Nominal (Rp)"></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-fee_percent">Fee Percent (%)</label></td>' +
                                    '<td width="70%"><input id="add-fee_percent" class="swal2-input" placeholder="Fee Percent (%)" onkeypress="return isNumberKey(event)"></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-status">Status</label></td>' +
                                    '<td style="width: 70%; text-align: left;"><select id="add-status" class="swal2-select">' +
                                        '<option value= "Y">Aktif</option>' +
                                        '<option value= "N">Tidak Aktif</option>' +
                                    '</select></td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</td>' +
                    '<td style="width: 50%;">' +
                        '<table border=0 style="width: 100%;">' +
                            '<tbody>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-flag">Flag</label></td>' +
                                    '<td style="width: 70%; text-align: left;"><select id="add-flag" class="swal2-select">' +
                                        '<option value= "module">Module</option>' +
                                        '<option value= "jokul">Jokul</option>' +
                                    '</select></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-device">Device</label></td>' +
                                    '<td style="width: 70%; text-align: left;"><select id="add-device" class="swal2-select">' +
                                        '<option value= "all">Semua Perangkat</option>' +
                                        '<option value= "mobile">Hanya Mobile</option>' +
                                    '</select></td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-image">Image</label></td>' +
                                    '<td style="width: 70%; text-align: left;">' +
                                        '<img id="image-preview" src="#" alt="Preview" style="max-height: 125px; max-width: 200px; margin-top: 10px; padding-left: 25px; display: none;"><br>' +
                                        '<input id="add-image" type="file" class="swal2-file" onchange="previewImage(event)">' +
                                    '</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td style="width: 30%; text-align: right;"><label for="add-image-show">Image Show</label></td>' +
                                    '<td style="width: 70%; text-align: left; padding-left: 25px;">' +
                                        '<input id="add-image-show" type="checkbox" class="swal2-checkbox">' +
                                    '</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</td>' +
                '</tr>' +
            '</tbody>' +
        '</table>',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            // Buat objek FormData
            var formData = new FormData();
            // Mengirimkan data dengan format yang benar
            var feeNominal = document.getElementById('add-fee_nominal').value.replace(/\./g, '');
            var feePercent = document.getElementById('add-fee_percent').value.replace(',', '.');
            // Ambil nilai dari formulir edit
            formData.append('name', document.getElementById('add-name').value);
            formData.append('code', document.getElementById('add-code').value);
            formData.append('fee_nominal', feeNominal);
            formData.append('fee_percent', feePercent);
            formData.append('active', document.getElementById('add-status').value);
            formData.append('flag', document.getElementById('add-flag').value);
            formData.append('device', document.getElementById('add-device').value);
            formData.append('is_show', document.getElementById('add-image-show').checked ? 1 : 0); // Ubah menjadi 1 jika diceklis, 0 jika tidak
            formData.append('image', document.getElementById('add-image').files[0]);

            // Kirim data yang ditambahkan ke server
            $.ajax({
                url: baseUrl + "/admin/payment/add",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                },
                processData: false,
                contentType: false,
                data: formData,
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Payment berhasil ditambahkan.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menambahkan payment.',
                        'error'
                    );
                }
            });
        }
    });
    // Tambahkan event listener untuk mengonversi nilai fee_nominal ke format rupiah saat input berubah
    $(document).on('input', '#add-fee_nominal', function () {
        var input = $(this).val();
        var formattedInput = convertToRupiah(input);
        $(this).val(formattedInput);
    });
}

function paymentDetail(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil data payment menggunakan AJAX
    $.ajax({
        url: baseUrl + "/admin/payment/" + id,
        method: "GET",
        success: function (response) {
            var payment = response;

            // Menampilkan informasi payment dengan SweetAlert
            Swal.fire({
                title: "Detail Payment",
                html: `
                    <img src="${payment.image_url}" alt="Payment Image" style="max-height: 125px; max-width: 200px; margin-bottom: 10px;">
                    <table style="width:100%">
                        <tr>
                            <td style="width: 20%; text-align: right;"><strong>Name</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 77%; text-align: left;">${payment.name || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: right;"><strong>Code</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 72%; text-align: left;">${payment.code || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: right;"><strong>Fee Nominal</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 72%; text-align: left;">${"Rp" + payment.fee_nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: right;"><strong>Fee Percent</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 72%; text-align: left;">${payment.fee_percent.toString().replace('.', ',') + "%"}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: right;"><strong>Flag</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 72%; text-align: left;">${(payment.flag.replace(/\b\w/g, c => c.toUpperCase())) || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: right;"><strong>Device</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 72%; text-align: left;">${(payment.device == 'all' ? 'Semua Perangkat' : 'Hanya Mobile') || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 25%; text-align: right;"><strong>Image Show</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 72%; text-align: left;">${(payment.is_show == 1 ? 'Tampil' : 'Tidak Tampil') || ""}</td>
                        </tr>
                    </table>
                `,
                confirmButtonText: "Tutup",
            });
        },
        error: function (xhr, status, error) {
            // Menampilkan pesan kesalahan
            Swal.fire({
                title: "Terjadi Kesalahan",
                text: "Terjadi kesalahan saat memuat detail payment.",
                icon: "error",
                confirmButtonText: "Tutup",
            });
        },
    });
}

function paymentStatus(id) {
    // Menampilkan pesan konfirmasi SweetAlert
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan mengubah status payment ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Ubah!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi pengubahan, mengirimkan permintaan AJAX
            $.ajax({
                url: baseUrl + "/admin/payment/" + id + "/status",
                method: "GET",
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Status payment berhasil diubah.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat mengubah status payment.',
                        'error'
                    );
                },
            });
        }
    });
}

function paymentEdit(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Mengambil data payment yang akan diedit melalui permintaan AJAX
    $.ajax({
        url: baseUrl + "/admin/payment/" + id,
        method: "GET",
        success: function (response) {
            var payment = response;

            // Menampilkan SweetAlert dengan formulir edit payment
            Swal.fire({
                title: 'Edit Payment',
                width: '50%',
                html:
                '<table border=0 style="width: 100%;">' +
                    '<tbody>' +
                        '<tr>' +
                            '<td style="width: 50%;">' +
                                '<table border=0 style="width: 100%;">' +
                                    '<tbody>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-name">Name</label></td>' +
                                            '<td width="70%"><input id="edit-name" class="swal2-input" placeholder="Name" value="' + payment.name + '"></td>' +
                                        '</tr>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-code">Code</label></td>' +
                                            '<td width="70%"><input id="edit-code" class="swal2-input" placeholder="Code" value="' + payment.code + '"></td>' +
                                        '</tr>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-fee_nominal">Fee Nominal (Rp)</label></td>' +
                                            '<td width="70%"><input id="edit-fee_nominal" class="swal2-input" placeholder="Fee Nominal (Rp)" value="' + payment.fee_nominal.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + '"></td>' +
                                        '</tr>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-fee_percent">Fee Percent (%)</label></td>' +
                                            '<td width="70%"><input id="edit-fee_percent" class="swal2-input" placeholder="Fee Percent (%)" value="' + payment.fee_percent.toString().replace('.', ',') + '" onkeypress="return isNumberKey(event)"></td>' +
                                        '</tr>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-flag">Flag</label></td>' +
                                            '<td style="width: 70%; text-align: left;"><select id="edit-flag" class="swal2-select">' +
                                                '<option value= "module"' + (payment.flag === 'module' ? ' selected' : '') + '>Module</option>' +
                                                '<option value= "jokul"' + (payment.flag === 'jokul' ? ' selected' : '') + '>Jokul</option>' +
                                            '</select></td>' +
                                        '</tr>' +
                                    '</tbody>' +
                                '</table>' +
                            '</td>' +
                            '<td style="width: 50%;">' +
                                '<table border=0 style="width: 100%;">' +
                                    '<tbody>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-device">Device</label></td>' +
                                            '<td style="width: 70%; text-align: left;"><select id="edit-device" class="swal2-select">' +
                                                '<option value= "all"' + (payment.device === 'all' ? ' selected' : '') + '>Semua Perangkat</option>' +
                                                '<option value= "mobile"' + (payment.device === 'mobile' ? ' selected' : '') + '>Hanya Mobile</option>' +
                                            '</select></td>' +
                                        '</tr>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-image">Image</label></td>' +
                                            '<td style="width: 70%; text-align: left;">' +
                                                '<img id="image-preview" src="' + payment.image_url + '" alt="Preview" style="max-height: 125px; max-width: 200px; margin-top: 10px; padding-left: 25px;"><br>' +
                                                '<input id="edit-image" type="file" class="swal2-file" onchange="previewImage(event)">' +
                                            '</td>' +
                                        '</tr>' +
                                        '<tr>' +
                                            '<td style="width: 30%; text-align: right;"><label for="edit-image-show">Image Show</label></td>' +
                                            '<td style="width: 70%; text-align: left; padding-left: 25px;">' +
                                                '<input id="edit-image-show" type="checkbox" class="swal2-checkbox" ' + (payment.is_show === 1 ? 'checked' : '') + '>' +
                                            '</td>' +
                                        '</tr>' +
                                    '</tbody>' +
                                '</table>' +
                            '</td>' +
                        '</tr>' +
                    '</tbody>' +
                '</table>',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: function () {
                    return new Promise(function (resolve) {
                        // Buat objek FormData
                        var formData = new FormData();
                        // Mengirimkan data dengan format yang benar
                        var feeNominal = document.getElementById('edit-fee_nominal').value.replace(/\./g, '');
                        var feePercent = document.getElementById('edit-fee_percent').value.replace(',', '.');
                        // Ambil nilai dari formulir edit
                        formData.append('name', document.getElementById('edit-name').value);
                        formData.append('code', document.getElementById('edit-code').value);
                        formData.append('fee_nominal', feeNominal);
                        formData.append('fee_percent', feePercent);
                        formData.append('flag', document.getElementById('edit-flag').value);
                        formData.append('device', document.getElementById('edit-device').value);
                        formData.append('is_show', document.getElementById('edit-image-show').checked ? 1 : 0); // Ubah menjadi 1 jika diceklis, 0 jika tidak
                        // Periksa apakah gambar baru dipilih
                        var imageInput = document.getElementById('edit-image');
                        if (imageInput.files.length > 0) {
                            formData.append('image', imageInput.files[0]);
                        }

                        // Kirim data yang diedit ke server
                        $.ajax({
                            url: baseUrl + "/admin/payment/" + id + "/edit",
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                            },
                            processData: false,  // Jangan memproses data FormData
                            contentType: false,  // Jangan mengatur tipe konten
                            data: formData,
                            success: function (response) {
                                resolve(response);
                            },
                            error: function (xhr, status, error) {
                                console.error('Error:', error);
                                Swal.fire(
                                    'Error!',
                                    'Terjadi kesalahan saat menyimpan perubahan payment.',
                                    'error'
                                );
                            }
                        });
                    });
                }
            }).then(function (result) {
                if (result.value) {
                    Swal.fire(
                        'Berhasil!',
                        'Payment berhasil diubah.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                }
            });
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            Swal.fire(
                'Error!',
                'Terjadi kesalahan saat mengambil data payment.',
                'error'
            );
        }
    });

    // Tambahkan event listener untuk mengonversi nilai fee_nominal ke format rupiah saat input berubah
    $(document).on('input', '#edit-fee_nominal', function () {
        var input = $(this).val();
        var formattedInput = convertToRupiah(input);
        $(this).val(formattedInput);
    });
}

function paymentDelete(id) {
    // Menampilkan pesan konfirmasi SweetAlert
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus payment ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi penghapusan, mengirimkan permintaan AJAX
            $.ajax({
                url: baseUrl + "/admin/payment/" + id + "/delete",
                method: "GET",
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Payment berhasil dihapus.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus payment.',
                        'error'
                    );
                },
            });
        }
    });
}

// Fungsi untuk menampilkan preview gambar
function previewImage(event) {
    var reader = new FileReader();
    reader.onload = function () {
        var output = document.getElementById('image-preview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Fungsi untuk mengonversi nilai fee_nominal ke format rupiah
function convertToRupiah(input) {
    // Pastikan input adalah string
    input = input.toString();
    // Hapus semua karakter kecuali angka
    var numericInput = input.replace(/\D/g, '');
    // Format ke format rupiah
    var formattedInput = numericInput.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    return formattedInput;
}

// Fungsi untuk memastikan hanya angka dan titik yang diterima pada fee_percent
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode != 44 && charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

document.getElementById("list-bank").addEventListener("click", function () {
    // Panggil fungsi showBankList hanya ketika elemen dengan ID list-bank diklik
    listBank(1); // Mulai dari halaman pertama
});

function listBank(page) {
    loading();
    showBankList(page);
}

// Fungsi untuk menampilkan daftar bank
function showBankList(page) {
    // Request data bank dari server
    $.ajax({
        url: "/admin/bank",
        method: "GET",
        data: { page: page },
        success: function (response) {
            // Menghentikan SweetAlert loading setelah menerima respons dari server
            Swal.close();

            var bankList = response.listbank.data; // Akses data bank dari respons
            var currentPage = response.listbank.current_page; // Halaman saat ini
            var lastPage = response.listbank.last_page; // Halaman terakhir

            // Buat konten HTML untuk tabel daftar bank
            var htmlContent = `
                <div class="text-right mb-3">
                    <button type="button" class="btn btn-primary" onclick="addBank()">Add Bank</button>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Bank</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
            `;
            
            // Iterasi melalui setiap bank dalam daftar
            bankList.forEach(function (bank) {
                htmlContent +=
                    "<tr>" +
                    '<td align="left">' +
                    bank.name +
                    "</td>" +
                    '<td align="center">' +
                    '<button type="button" class="btn btn-transparent" onclick="editBank(' + bank.id + ', \'' + bank.name + '\')" title="Edit Bank">' +
                    '<span class="material-symbols-outlined" id="icon-warning" style="font-size: 14pt;">edit</span></button>' +
                    '<button type="button" class="btn btn-transparent" onclick="deleteBank(' + bank.id + ')" title="Delete Bank">' +
                    '<span class="material-symbols-outlined" id="icon-delete" style="font-size: 14pt;">delete</span></button>' +
                    "</td>" +
                    "</tr>";
            });

            htmlContent += `</tbody></table>`;

            // Tampilkan daftar bank menggunakan SweetAlert
            Swal.fire({
                title: "List Bank",
                html: htmlContent,
                showConfirmButton: false, // Sembunyikan tombol konfirmasi
                width: "30%",
            }).then((result) => {
                if (!result.isConfirmed) {
                    // Jika tombol close di SweetAlert diklik, muat ulang daftar bank
                    // showBankList(currentPage); // Komentari ini agar tidak otomatis memuat saat menutup
                }
            });

            // Tambahkan pagination number ke SweetAlert setelah ditampilkan
            addPagination(currentPage, lastPage);
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Terjadi kesalahan saat memuat daftar bank.",
            });
        },
    });
}

function addPagination(currentPage, lastPage, totalRecords) {
    // Batasi jumlah halaman yang ditampilkan menjadi maksimal lima
    // Membuat tombol navigasi paginasi
    var paginationHtml = '<div class="pagination">';
    if (currentPage > 1) {
        paginationHtml += '<button onclick="listBank(' + (currentPage - 1) + ')">Prev</button>';
    } else {
        paginationHtml += '<button class="disabled">Prev</button>';
    }

    var startPage = Math.max(1, currentPage - 2);
    var endPage = Math.min(startPage + 4, lastPage);
    if (lastPage - currentPage < 2) {
        startPage = Math.max(1, lastPage - 4);
    }
    for (var i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
            paginationHtml += '<button class="current">' + i + '</button>';
        } else {
            paginationHtml += '<button onclick="listBank(' + i + ')">' + i + '</button>';
        }
    }

    if (currentPage < lastPage) {
        paginationHtml += '<button onclick="listBank(' + (currentPage + 1) + ')">Next</button>';
    } else {
        paginationHtml += '<button class="disabled">Next</button>';
    }
    paginationHtml += '<p>Showing ' + ((currentPage - 1) * 10 + 1) + ' to ' + Math.min(currentPage * 10, totalRecords) + ' of ' + totalRecords + ' entries</p>';
    paginationHtml += '</div>';
    
    // Menambahkan tombol paginasi ke dalam SweetAlert
    $(".swal2-html-container").append(paginationHtml);
}

// Fungsi untuk menambahkan bank baru
function addBank() {
    loading();
    // Logika untuk menambahkan bank baru
    Swal.fire({
        title: "Add Bank",
        html: `
            <!-- Form untuk menambahkan bank baru -->
            <form id="addBankForm">
                <div class="form-group">
                    <label for="bankName">Nama Bank:</label>
                    <input type="text" class="form-control" id="bankName" required>
                </div>
            </form>
        `,
        showCancelButton: true, // Menampilkan tombol cancel
        showConfirmButton: true, // Sembunyikan tombol confirm
        focusConfirm: false, // Jangan fokuskan ke tombol confirm
        cancelButtonText: "Batal", // Tekst tombol cancel
        confirmButtonText: "Tambah", // Tekst tombol cancel
        cancelButtonColor: "#d33", // Warna tombol cancel
        preConfirm: () => {
            // Logika untuk menyimpan bank baru
            var token = document.head.querySelector('meta[name="csrf-token"]').content;
            var bankName = document.getElementById("bankName").value;
            saveNewBank(bankName, token);
        }
    });
}

// Fungsi untuk menyimpan bank baru ke server
function saveNewBank(name, token) {
    // Kirim permintaan AJAX untuk menyimpan bank baru
    $.ajax({
        url: "/admin/bank/add",
        method: "POST",
        data: { name: name, _token: token },
        success: function (response) {
            console.log("Success: ", response); // Tambahkan log di sini
            Swal.fire({
                icon: "success",
                title: "Bank added successfully!",
                showConfirmButton: false,
                timer: 1500
            });
            // Muat ulang daftar bank setelah menambahkan bank baru
            showBankList(1);
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Failed to add bank. Please try again.",
            });
        },
    });
}

// Fungsi untuk menampilkan form edit bank
function editBank(id, name) {
    Swal.fire({
        title: "Edit Bank",
        html: `
            <form id="editBankForm">
                <div class="form-group">
                    <label for="editedBankName">Nama Bank:</label>
                    <input type="text" class="form-control" id="editedBankName" value="${name}" required>
                </div>
            </form>
        `,
        showCancelButton: true,
        showConfirmButton: true,
        focusConfirm: false,
        cancelButtonText: "Batal",
        confirmButtonText: "Simpan",
        cancelButtonColor: "#d33",
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika user menekan tombol Yes
            var token = document.head.querySelector('meta[name="csrf-token"]').content;
            var newName = document.getElementById("editedBankName").value;
            updateBank(id, newName, token);
        // } else if (result.dismiss === Swal.DismissReason.cancel) {
        //     // Jika user membatalkan
        //     // Lakukan sesuatu, misalnya, tampilkan pesan
        //     Swal.fire({
        //         icon: "info",
        //         title: "Edit Bank dibatalkan!",
        //         showConfirmButton: false,
        //         timer: 1500
        //     });
        //     showBankList(1); // Muat ulang daftar bank setelah mengedit
        }
    });
}

// Fungsi untuk mengirim permintaan update bank ke server
function updateBank(id, newName, token) {
    $.ajax({
        url: "/admin/bank/update/" + id,
        method: "PUT",
        data: { name: newName, _token: token },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "Bank updated successfully!",
                showConfirmButton: false,
                timer: 1500
            });
            showBankList(1); // Muat ulang daftar bank setelah mengedit
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Failed to update bank. Please try again.",
            });
        },
    });
}

// Fungsi untuk mengkonfirmasi dan menghapus bank
function deleteBank(id) {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: "Anda akan menghapus bank ini!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya, Hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika user menekan tombol Yes
            var token = document.head.querySelector('meta[name="csrf-token"]').content;
            removeBank(id, token);
        // }  else if (result.dismiss === Swal.DismissReason.cancel) {
        //     // Jika user membatalkan
        //     // Lakukan sesuatu, misalnya, tampilkan pesan
        //     Swal.fire({
        //         icon: "info",
        //         title: "Delete Bank dibatalkan!",
        //         showConfirmButton: false,
        //         timer: 1500
        //     });
        //     showBankList(1); // Muat ulang daftar bank setelah mengedit
        }
    });
}

// Fungsi untuk mengirim permintaan penghapusan bank ke server
function removeBank(id, token) {
    $.ajax({
        url: "/admin/bank/delete/" + id,
        method: "DELETE",
        data: { _token: token },
        success: function (response) {
            Swal.fire({
                icon: "success",
                title: "Bank deleted successfully!",
                showConfirmButton: false,
                timer: 1500
            });
            showBankList(1); // Muat ulang daftar bank setelah menghapus
        },
        error: function (xhr, status, error) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Failed to delete bank. Please try again.",
            });
        },
    });
}

function loading() {
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });
}