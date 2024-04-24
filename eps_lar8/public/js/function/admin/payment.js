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