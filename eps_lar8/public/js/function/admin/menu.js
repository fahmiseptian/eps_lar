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

    // Tambahkan event listener untuk menangani klik pada list parent menu
    $('#parentMenuList').on('click', function() {
        // Ambil tabel daftar parent menu
        var parentMenuTable = $('#parentmenu');
        // Toggle visibility tabel
        parentMenuTable.slideToggle();
        // Ambil ikon
        var icon = $(this).find('i');
        // Ubah orientasi ikon
        icon.toggleClass('rotate-icon');
    });
});

function editMenu(id) {
    // Menampilkan loading spinner
    Swal.fire({
        title: "Memuat...",
        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
        showConfirmButton: false,
        allowOutsideClick: false,
    });
    
    // Mengambil token CSRF dari meta tag
    var csrfToken = document.head.querySelector('meta[name="csrf-token"]').content;

    // Mengambil data menu yang akan diedit melalui permintaan AJAX
    $.ajax({
        url: baseUrl + "/admin/menu/" + id,
        method: "GET",
        success: function (response) {
            var menu = response.menu;
            var accesses = response.accesses;
            var SwalContent = "";
            accesses.forEach(access => {
                SwalContent += `<input type="checkbox" id="edit-${access.code}" name="${access.code}" value="1" ${menu[access.code] == 1 ? 'checked' : ''}> ${access.name}<br>`;
            });

            // Menampilkan SweetAlert dengan formulir edit menu
            Swal.fire({
                title: 'Edit Menu',
                width: '50%',
                html:
                `<form id="edit-menu-form">
                    <table border=0 style="width: 100%;">
                        <tbody>
                            <tr>
                                <td style="width: 50%;">
                                    <table border=0 style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-nama">Nama</label></td>
                                                <td width="70%"><input id="edit-nama" name="nama" class="swal2-input" placeholder="Nama" value="${menu.nama}" required></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-route">Route</label></td>
                                                <td width="70%"><input id="edit-route" name="route" class="swal2-input" placeholder="Route" value="${menu.route === null ? '' : menu.route}"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-icon">Icon</label></td>
                                                <td width="70%"><input id="edit-icon" name="icon" class="swal2-input" placeholder="Icon" value="${menu.icon}" required></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-urutan">Urutan</label></td>
                                                <td width="70%"><input id="edit-urutan" name="urutan" class="swal2-input" placeholder="Urutan" value="${menu.urutan}" required></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                                <td style="width: 50%;">
                                    <table border=0 style="width: 100%;">
                                        <tbody>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-parent_id">Parent ID</label></td>
                                                <td width="70%"><input id="edit-parent_id" name="parent_id" class="swal2-input" placeholder="Parent ID" value="${menu.parent_id === null ? '' : menu.parent_id}"></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-status">Status</label></td>
                                                <td style="width: 70%; text-align: left;">
                                                    <select id="edit-status" name="status" class="swal2-select">
                                                        <option value="1"${menu.status === 1 ? ' selected' : ''}>Parent</option>
                                                        <option value="2"${menu.status === 2 ? ' selected' : ''}>Children</option>
                                                        <option value="0"${menu.status === 0 ? ' selected' : ''}>Nonaktif</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 30%; text-align: right;"><label for="edit-access">Akses</label></td>
                                                <td style="width: 70%; text-align: left; padding-left: 25px;">${SwalContent}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </form>`,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    var form = document.getElementById('edit-menu-form');
                    var formData = new FormData(form);

                    if (!formData.get('nama') || !formData.get('icon') || !formData.get('urutan')) {
                        Swal.showValidationMessage('Semua bidang harus diisi');
                        if (formData.get('status') == 2) {
                            if (!formData.get('route') || !formData.get('parent_id')) {
                                Swal.showValidationMessage('Semua bidang harus diisi');
                            }
                        }
                    } else {
                        return {
                            formData: formData,
                        };
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Mengirimkan permintaan AJAX untuk menyimpan data yang diedit
                    var formData = result.value.formData;

                    $.ajax({
                        url: baseUrl + "/admin/menu/" + id + "/edit",
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
                                'Menu berhasil diubah.',
                                'success'
                            );
                            // Refresh halaman untuk memperbarui tampilan
                            location.reload();
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                            Swal.fire(
                                'Error!',
                                'Terjadi kesalahan saat mengubah menu.',
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
                'Terjadi kesalahan saat mengambil data menu.',
                'error'
            );
        }
    });
}

function deleteMenu(id) {
    // Menampilkan pesan konfirmasi SweetAlert
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda akan menghapus menu ini!",
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
                url: baseUrl + "/admin/menu/" + id + "/delete",
                method: "GET",
                success: function (response) {
                    Swal.fire(
                        'Berhasil!',
                        'Menu berhasil dihapus.',
                        'success'
                    );
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function (xhr, status, error) {
                    console.log(xhr.responseText);
                    Swal.fire(
                        'Error!',
                        'Terjadi kesalahan saat menghapus menu.',
                        'error'
                    );
                },
            });
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    const statusSelect = document.querySelector('select[name="status"]');
    const routeInput = document.querySelector('input[name="route"]');
    const parentIdInput = document.querySelector('input[name="parent_id"]');

    // Function untuk menyesuaikan aturan required berdasarkan status yang dipilih
    function updateRequired() {
        if (statusSelect.value === '2') {
            routeInput.setAttribute('required', 'required');
            parentIdInput.setAttribute('required', 'required');
        } else {
            routeInput.removeAttribute('required');
            parentIdInput.removeAttribute('required');
        }
    }

    // Panggil function updateRequired saat halaman dimuat atau status berubah
    updateRequired();
    statusSelect.addEventListener('change', updateRequired);
});