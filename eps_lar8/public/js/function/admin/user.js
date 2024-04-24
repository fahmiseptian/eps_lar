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

    // Tambahkan event listener untuk menangani klik pada list access
    $('#accessList').on('click', function() {
        // Ambil tabel daftar access
        var accessDiv = $('#accesses');
        // Toggle visibility tabel
        accessDiv.slideToggle();
        // Ambil ikon
        var icon = $(this).find('i');
        // Ubah orientasi ikon
        icon.toggleClass('rotate-icon');
    });
});

function addAccess() {
    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Menampilkan SweetAlert dengan formulir tambah access
    Swal.fire({
        title: 'Tambah Akses Baru',
        html:
            '<input id="swal-input1" class="swal2-input" placeholder="Nama Akses">',
        showCancelButton: true,
        confirmButtonText: 'Tambah',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const name = Swal.getPopup().querySelector('#swal-input1').value;
            if (!name) {
                Swal.showValidationMessage('Nama akses harus diisi');
            }
            return { name: name };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const name = result.value.name;

            // Kirim data yang ditambahkan ke server
            $.ajax({
                url: baseUrl + "/admin/access/add",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                },
                data: { name: name },
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Akses berhasil ditambahkan.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menambahkan akses.',
                        'error'
                    );
                }
            });
        }
    });
}

function editAccess(id, name, code) { 
    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;
    Swal.fire({
        title: 'Edit Access',
        html:
            `<input id="swal-input1" class="swal2-input" value="${name}" placeholder="Nama Akses">` +
            `<input id="swal-input2" class="swal2-input" value="${code}" placeholder="Code Lama" hidden>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Simpan',
        cancelButtonText: 'Batal',
        preConfirm: () => {
            const name = Swal.getPopup().querySelector('#swal-input1').value;
            const old_code = Swal.getPopup().querySelector('#swal-input2').value;
            if (!name) {
                Swal.showValidationMessage('Nama akses harus diisi');
            }
            return { 
                name: name,
                old_code: old_code,
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const name = result.value.name;
            const old_code = result.value.old_code;
            // Mengirimkan permintaan AJAX untuk menyimpan data yang diedit
            $.ajax({
                url: baseUrl + "/admin/access/" + id + "/edit",
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                },
                data: {
                    id: id,
                    name: name,
                    old_code: old_code,
                },
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Akses berhasil diubah.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat mengubah akses.',
                        'error'
                    );
                }
            });
        }
    });
}

function deleteAccess(id, name) {
    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil data akses pengganti menggunakan AJAX
    $.ajax({
        url: baseUrl + "/admin/access/available/" + id,
        method: "GET",
        success: function (response) {
            var accessOptions = response.accesses;
            var dropdownHtml = '<div class="swal2-row"><label for="replacement-access" class="swal2-input-label">Pilih Akses Pengganti:</label>' +
                '<select id="replacement-access" class="swal2-select">';
            accessOptions.forEach(function (access) {
                dropdownHtml += `<option value="${access.id}">${access.name}</option>`;
            });
            dropdownHtml += '</select></div>';

            // Menampilkan pesan konfirmasi SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                html: '<p>Anda akan menghapus akses <strong>' + name + '</strong>! Pilih akses pengganti untuk pengguna dengan akses ini.</p>' + dropdownHtml, // Placeholder for access dropdown
                preConfirm: () => {
                    return document.getElementById('replacement-access').value;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    var replacementAccess = result.value;

                    // Menampilkan loading spinner saat proses penghapusan
                    Swal.fire({
                        title: "Memproses...",
                        html: '<div class="spinner-border" role="status"><span class="sr-only">Memproses...</span></div>',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                    });

                    // Jika pengguna mengonfirmasi penghapusan, mengirimkan permintaan AJAX
                    $.ajax({
                        url: baseUrl + "/admin/access/" + id + "/delete",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                        },
                        data: {
                            replacement_access: replacementAccess
                        },
                        success: function (response) {
                            Swal.fire(
                                'Berhasil!',
                                'Akses berhasil dihapus.',
                                'success'
                            );
                            // Refresh halaman untuk memperbarui tampilan
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat menghapus akses.',
                                'error'
                            );
                        },
                    });
                }
            });
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            Swal.fire(
                'Error!',
                'Terjadi kesalahan saat mengambil data akses.',
                'error'
            );
        }
    });
}

function detailUser(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });

    // Mengambil data user menggunakan AJAX
    $.ajax({
        url: baseUrl + "/admin/user/" + id,
        method: "GET",
        success: function (response) {
            var user = response.user;
            var profile = response.profile;

            // Menampilkan informasi user dengan SweetAlert
            Swal.fire({
                title: "Detail User",
                html: `
                    <table style="width:100%">
                        <tr>
                            <td style="width: 30%; text-align: right;"><strong>Username</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 67%; text-align: left;">${user.username || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; text-align: right;"><strong>Nama</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 67%; text-align: left;">${(profile.firstname + ' ' + profile.lastname) || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; text-align: right;"><strong>Akses</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 67%; text-align: left;">${(user.access.name) || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; text-align: right;"><strong>Status</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 67%; text-align: left;">${(user.active == 1 ? 'Aktif' : 'Tidak Aktif') || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; text-align: right;"><strong>No Telepon</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 67%; text-align: left;">${profile.phone || ""}</td>
                        </tr>
                        <tr>
                            <td style="width: 30%; text-align: right;"><strong>Alamat</strong></td>
                            <td style="width: 3%; text-align: left;"><strong>:</strong></td>
                            <td style="width: 67%; text-align: left;">${profile.address || ""}</td>
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
                text: "Terjadi kesalahan saat memuat detail user.",
                icon: "error",
                confirmButtonText: "Tutup",
            });
        },
    });
}

function editUser(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });
    
    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Mengambil data user yang akan diedit melalui permintaan AJAX
    $.ajax({
        url: baseUrl + "/admin/user/" + id,
        method: "GET",
        success: function (response) {
            var user = response.user;
            var profile = response.profile;
            var accesses = response.accesses;
            var SwalContent = "";
            accesses.forEach(access => {
                SwalContent += `<option value="${access.id}" ${user.access_id == access.id ? 'selected' : ''}>${access.name}</option>`;
            });
            // Menampilkan SweetAlert dengan formulir edit user
            Swal.fire({
                title: 'Edit User',
                width: '50%',
                html:
                    `<table border="0" style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 50%;">
                                    <table border=0 style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-username">Username</label></td>
                                                <td style="width: 70%;"><input id="edit-username" class="swal2-input" placeholder="Username" value="${user.username}"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-firstname">First Name</label></td>
                                                <td style="width: 70%;"><input id="edit-firstname" class="swal2-input" placeholder="First Name" value="${(profile.firstname === null ? '' : profile.firstname)}"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-lastname">Last Name</label></td>
                                                <td style="width: 70%;"><input id="edit-lastname" class="swal2-input" placeholder="Last Name" value="${(profile.lastname === null ? '' : profile.lastname)}"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="width: 50%;">
                                    <table border=0 style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-access">Access</label></td>
                                                <td style="width: 70%; text-align: left;">
                                                    <select id="edit-access" class="swal2-select">` + SwalContent +
                                                    `</select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-status">Status</label></td>
                                                <td style="width: 70%; text-align: left;">
                                                    <select id="edit-status" class="swal2-select">
                                                        <option value="1" ${user.active == 1 ? 'selected' : ''}>Aktif</option>
                                                        <option value="0" ${user.active == 0 ? 'selected' : ''}>Tidak Aktif</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-password">Change Password</label></td>
                                                <td style="width: 70%;"><input id="edit-password" class="swal2-input" placeholder="Change Password"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return {
                        username: document.getElementById('edit-username').value,
                        firstname: document.getElementById('edit-firstname').value,
                        lastname: document.getElementById('edit-lastname').value,
                        access_id: document.getElementById('edit-access').value,
                        active: document.getElementById('edit-status').value,
                        password: document.getElementById('edit-password').value,
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mengirimkan permintaan AJAX untuk menyimpan data yang diedit
                    $.ajax({
                        url: baseUrl + "/admin/user/" + id + "/edit",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Mengatur token CSRF dalam header permintaan
                        },
                        data: {
                            username: result.value.username,
                            firstname: result.value.firstname,
                            lastname: result.value.lastname,
                            access_id: result.value.access_id,
                            active: result.value.active,
                            password: result.value.password,
                        },
                        success: function (response) {
                            Swal.fire(
                                'Berhasil!',
                                'User berhasil diubah.',
                                'success'
                            );
                            // Refresh halaman untuk memperbarui tampilan
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat mengubah user.',
                                'error'
                            );
                        }
                    });
                }
            });
        },
        error: function (xhr, status, error) {
            console.log(xhr.responseText);
            Swal.fire(
                'Error!',
                'Terjadi kesalahan saat mengambil data user.',
                'error'
            );
        }
    });
}

function deleteUser(id) {
    // Menampilkan pesan konfirmasi SweetAlert
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus user ini!",
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
                url: baseUrl + "/admin/user/" + id + "/delete",
                method: "GET",
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'User berhasil dihapus.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus user.',
                        'error'
                    );
                },
            });
        }
    });
}