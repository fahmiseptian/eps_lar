<!DOCTYPE html>
<html lang="id">
@include('member.asset.header')
<link href="{{ asset('/css/profile.css') }}" rel="stylesheet" type="text/css" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<body>

    @include('member.asset.navbar')
    <main class="container mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Menu Profil</h5>
                        <ul class="nav flex-column">
                            @if($member->id_member_type != 3)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.transaksi.pemohon') }}"><span class="material-icons">swap_horiz</span> Lihat Transaksi</a>
                            </li>
                            @endif
                            @if($member->id_member_type == 3)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.transaksi') }}"><span class="material-icons">swap_horiz</span> Lihat Transaksi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.nego') }}"><span class="material-icons">handshake</span> Nego</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.wish') }}"><span class="material-icons">favorite</span> Favorite</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="manajemenDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="material-icons">manage_accounts</span> Manajemen
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="manajemenDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile.user', ['tipe' => 'pemohon']) }}"><span class="material-icons">person_add</span> User Pemohon</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.user', ['tipe' => 'Penyetuju_Pemohonan']) }}"><span class="material-icons">how_to_reg</span> User Penyetuju</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.user', ['tipe' => 'finance']) }}"><span class="material-icons">account_balance</span> User Finance</a></li>
                                </ul>
                            </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="settingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="material-icons">settings</span> Setting
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="settingDropdown">
                                    <li><a class="dropdown-item" href="{{ route('profile.view') }}"><span class="material-icons">person</span> Profile</a></li>
                                    @if($member->id_member_type == 3)
                                    <li><a class="dropdown-item" href="{{ route('profile.address') }}"><span class="material-icons">location_on</span> Alamat</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('profile.update_password') }}"><span class="material-icons">vpn_key</span> Ganti Password</a></li>
                                </ul>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link-logout" href="{{ route('logout') }}">
                                    <span class="material-icons">logout</span> Keluar
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Selamat datang, {{ $nama_user }}</h4>
                        <p class="card-text">Pilih menu di sebelah kiri untuk mengelola akun Anda.</p>

                        <!-- Content will be loaded here based on menu selection -->
                        <div id="contentArea">
                            <div class="profile-container">
                                <h3 class="profile-title">Profil Saya</h3>
                                <div class="profile-info">
                                    <div class="profile-item">
                                        <span class="material-icons profile-icon">person</span>
                                        <div class="profile-detail">
                                            <label>Nama Lengkap</label>
                                            <p>{{ $user->nama ?? 'Nama belum diatur' }}</p>
                                        </div>
                                    </div>
                                    <div class="profile-item">
                                        <span class="material-icons profile-icon">email</span>
                                        <div class="profile-detail">
                                            <label>Email</label>
                                            <p>{{ $user->email ?? 'Email belum diatur' }}</p>
                                        </div>
                                    </div>
                                    <div class="profile-item">
                                        <span class="material-icons profile-icon">phone</span>
                                        <div class="profile-detail">
                                            <label>Nomor Telepon</label>
                                            <p>{{ $user->phone ?? 'Nomor telepon belum diatur' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="profile-actions">
                                    <button class="btn btn-primary" onclick="editProfile()">
                                        <span class="material-icons">edit</span> Edit Profil
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal -->
    <div class="modal fade" id="negoUlangModal" tabindex="-1" role="dialog" aria-labelledby="negoUlangModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="negoUlangModalLabel">Negosiasi Ulang</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="negoUlangForm">
                        <div class="form-group">
                            <label for="qty">Kuantitas</label>
                            <input type="number" class="form-control" id="qty" value="" readonly>
                            <input type="number" class="form-control" id="id_nego" readonly hidden>
                            <input type="number" class="form-control" id="last_id" readonly hidden>
                        </div>
                        <div class="form-group">
                            <label for="hargaResponseSatuan">Harga Response Penjual Satuan</label>
                            <input type="text" class="form-control" id="hargaResponseSatuan" value="0" readonly>
                        </div>
                        <div class="form-group">
                            <label for="hargaResponseTotal">Harga Response Penjual Total</label>
                            <input type="text" class="form-control" id="hargaResponseTotal" value="0" readonly>
                        </div>
                        <div class="form-group">
                            <label for="hargaNegoSatuan">Harga Nego Satuan</label>
                            <input type="text" class="form-control" id="hargaNegoSatuan" value="">
                        </div>
                        <div class="form-group">
                            <label for="hargaNegoTotal">Harga Nego Total</label>
                            <input type="text" class="form-control" id="hargaNegoTotal" value="0" readonly>
                        </div>
                        <div class="form-group">
                            <label for="catatan">Catatan</label>
                            <textarea class="form-control" id="catatan" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" id="submitNegoUlang">Kirim Nego Ulang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Data Modal -->
    <div class="modal fade" id="addDataPPModal" tabindex="-1" aria-labelledby="addDataPPLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDataPPLabel">Tambah Data User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="close_modal_pp()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addUserFormPP">
                        <div class="form-group">
                            <label for="fullName">Nama Lengkap</label>
                            <input type="text" class="form-control" id="fullName" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Departemen</label>
                            <input type="text" class="form-control" id="department" value="{{ $member->satker }}" readonly required>
                        </div>
                        <div class="form-group">
                            <label for="jabatan">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" required>
                        </div>
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select class="form-control" id="role">
                                <option value="3">Pemohon</option>
                                <option value="4">Penyetuju Pemohon</option>
                                <option value="6">Finance</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="phone">No Handphone</label>
                            <input type="text" class="form-control" id="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status">
                                <option value="Y">Aktif</option>
                                <option value="N">Tidak Aktif</option>
                            </select>
                        </div>

                        <!-- Additional Fields for 'Penyetuju Pemohon' Role -->
                        <div id="additionalFields" style="display: none;">
                            <div class="form-group">
                                <label for="batasAwal">Batas Awal</label>
                                <input type="text" class="form-control" id="batasAwal" value="0" required>
                            </div>
                            <div class="form-group">
                                <label for="batasAkhir">Batas Akhir</label>
                                <input type="text" class="form-control" id="batasAkhir" value="0" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="close_modal_pp()">Tutup</button>
                    <button type="submit" class="btn btn-primary" onclick="saveUserBtn()">Simpan</button>
                </div>
            </div>
        </div>
    </div>


    @include('member.asset.footer')

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        document.querySelectorAll('.nav-link, .dropdown-item').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const contentArea = document.getElementById('contentArea');
                const menuText = this.textContent.trim();
                const url = this.getAttribute('href');

                if (url == '' || url == null) {
                    return false;
                }

                // Menampilkan pesan loading
                contentArea.innerHTML = `<h5>Memuat konten untuk ${menuText}...</h5>`;

                // Melakukan request AJAX
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        contentArea.innerHTML = data;
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        contentArea.innerHTML = `<h5>Terjadi kesalahan saat memuat konten untuk ${menuText}. Silakan coba lagi.</h5>`;
                    });

                // Update active state pada menu
                document.querySelectorAll('.nav-link, .dropdown-item').forEach(item => {
                    item.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
    </script>

    <script>
        function formatRupiah(angka) {
            var number_string = angka.toString().replace(/[^,\d]/g, ""),
                split = number_string.split(","),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? "." : "";
                rupiah += separator + ribuan.join(".");
            }

            rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
            return "Rp. " + rupiah;
        }

        function unformatRupiah(formattedRupiah) {
            var number_string = formattedRupiah.replace(/[^,\d]/g, "");
            return parseInt(number_string.replace(/[.,]/g, ""));
        }

        function parseRupiah(rupiahString) {
            return parseInt(rupiahString.replace(/[^0-9]/g, ""));
        }
        $(document).on('click', '.detail-transaction', function(e) {
            e.preventDefault();
            var id_cart = $(this).data('id');
            var url = "{{ route('profile.transaksi.detail') }}?id=" + id_cart;
            loadContent(url, $('#contentArea'));
        });

        function loadContent(url, targetElement) {
            $.ajax({
                url: url,
                method: 'GET',
                beforeSend: function() {
                    targetElement.html('<h5>Memuat konten...</h5>');
                },
                success: function(response) {
                    targetElement.html(response);
                },
                error: function() {
                    targetElement.html('<h5>Terjadi kesalahan saat memuat konten. Silakan coba lagi.</h5>');
                }
            });
        }

        $(document).on('click', '#tambah-alamat', function(e) {
            var url = "{{ route('profile.edit-address') }}";
            loadContent(url, $('#contentArea'));
        });

        function editAlamat(id) {
            var url = "{{ route('profile.edit-address') }}?id_address=" + id;
            loadContent(url, $('#contentArea'));
        }

        function hapusAlamat(id) {
            $.ajax({
                url: appUrl + "/api/member/update-Address",
                method: 'POST',
                data: {
                    id_address: id,
                    action: 'delete'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'Alamat berhasil dihapus.', 'success').then((result) => {
                            if (result.isConfirmed) {
                                var url = "{{ route('profile.address') }}";
                                loadContent(url, $('#contentArea'));
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal menghapus alamat.', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    Swal.fire('Error', 'Terjadi kesalahan saat menghapus alamat.', 'error');
                }
            });
        }

        function aturSebagaiPenagihan(id) {
            $.ajax({
                url: appUrl + "/api/member/update-Address",
                method: 'POST',
                data: {
                    id_address: id,
                    action: 'set_billing'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'Alamat berhasil diset sebagai pengaihan.', 'success').then((result) => {
                            if (result.isConfirmed) {
                                var url = "{{ route('profile.address') }}";
                                loadContent(url, $('#contentArea'));
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal saat diset alamat penagihan', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    Swal.fire('Error', 'Terjadi kesalahan saat diset alamat utama', 'error');
                }
            });
        }

        function aturAlamatUtama(id) {
            $.ajax({
                url: appUrl + "/api/member/update-Address",
                method: 'POST',
                data: {
                    id_address: id,
                    action: 'set_shipping'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Berhasil!', 'Alamat berhasil diset sebagai alamat utama.', 'success').then((result) => {
                            if (result.isConfirmed) {
                                var url = "{{ route('profile.address') }}";
                                loadContent(url, $('#contentArea'));
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal set alamat utama', 'error');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    Swal.fire('Error', 'Terjadi kesalahan saat diset alamat utama', 'error');
                }
            });
        }

        $(document).on('click', '.negos-opsi', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            loadContent(url, $('#contentArea'));
        });

        $(document).on('click', '.detail_nego', function(e) {
            e.preventDefault();
            var id_nego = $(this).data('id');
            var url = "{{ route('profile.nego.detail') }}?id=" + id_nego;
            loadContent(url, $('#contentArea'));
        });

        $('#submitNegoUlang').click(function() {
            const id_nego = $('#id_nego').val();
            const last_id = $('#last_id').val();
            const nego_price = unformatRupiah($('#hargaNegoSatuan').val());
            const qty = $('#qty').val();
            const catatan = $('#catatan').val();

            // Perform AJAX to submit the new negotiation
            $.ajax({
                url: appUrl + '/api/member/nego/reqNego', // Replace with your actual endpoint
                type: 'POST',
                data: {
                    id_nego: id_nego,
                    last_id: last_id,
                    nego_price: nego_price,
                    qty: qty,
                    catatan: catatan,
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Negosiasi Dikirim!',
                        text: 'Negosiasi ulang berhasil dikirim.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    });
                    $('#negoUlangModal').modal('hide');
                    backtomenu();
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat mengirim negosiasi ulang. Silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        function togglePasswordVisibility(inputSelector, iconElement) {
            var $passwordInput = $(inputSelector);
            var $icon = $(iconElement).find('i');

            if ($passwordInput.attr('type') === 'password') {
                $passwordInput.attr('type', 'text');
                $icon.text('visibility_off');
            } else {
                $passwordInput.attr('type', 'password');
                $icon.text('visibility');
            }
        }

        // Handle form submission with jQuery AJAX
        function Update_password() {
            event.preventDefault(); // Prevent default form submission

            var currentPassword = $('#currentPassword').val();
            var newPassword = $('#newPassword').val();
            var confirmPassword = $('#confirmPassword').val();

            // Check if new password and confirm password match
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Password baru dan konfirmasi password tidak sama.',
                });
                return;
            }

            // Prepare the data to be sent
            var data = {
                current_password: currentPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            };

            // Send the data via jQuery AJAX
            $.ajax({
                url: appUrl + '/api/member/update-password',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: 'Password berhasil diupdate.',
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: response.error,
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: 'Gagal mengirim permintaan. Silakan coba lagi.',
                    });
                }
            });
        }

        function open_modal_pp() {
            $('#addDataPPModal').modal('show');
        }

        $('#role').change(function() {
            if ($(this).val() === '4') { // If 'Penyetuju Pemohon' is selected
                $('#additionalFields').show();
            } else {
                $('#additionalFields').hide();
                $('#batasAwal').val(0); // Reset to default value
                $('#batasAkhir').val(0); // Reset to default value
            }
        });

        function close_modal_pp() {
            $('#addDataPPModal').modal('hide');
        }

        function saveUserBtn() {
            // Gather form data
            var name = $('#fullName').val();
            var email = $('#email').val();
            var department = $('#department').val();
            var jabatan = $('#jabatan').val();
            var role = $('#role').val();
            var no_hp = $('#phone').val();
            var status = $('#status').val();
            var batas_awal = unformatRupiah($('#batasAwal').val()) || 0; // Default to 0 if empty
            var batas_akhir = unformatRupiah($('#batasAkhir').val()) || 0; // Default to 0 if empty

            // Create an object to hold the data
            var data = {
                name: name,
                email: email,
                department: department,
                jabatan: jabatan,
                role: role,
                no_hp: no_hp,
                status: status,
                batas_awal: batas_awal,
                batas_akhir: batas_akhir
            };

            // Perform AJAX request
            $.ajax({
                url: appUrl + '/api/member/add-user',
                method: 'POST',
                data: data,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: 'User berhasil ditambahkan.',
                        }).then(() => {
                            location.reload();
                            $('#addUserFormPP')[0].reset();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Terjadi kesalahan saat menambahkan user.',
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengirim permintaan. Silakan coba lagi.',
                    });
                }
            });
        }

        $('#batasAwal').on('input', function() {
            $('#batasAwal').val(formatRupiah($('#batasAwal').val()));
        });

        $('#batasAkhir').on('input', function() {
            $('#batasAkhir').val(formatRupiah($('#batasAkhir').val()));
        });

        function detailUser(id) {
            $.ajax({
                url: appUrl + '/profile/get-user/' + id,
                method: 'get',
                success: function(response) {
                    Swal.fire({
                        title: 'Detail User',
                        html: response,
                    })
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: 'Gagal mengirim permintaan. Silakan coba lagi.',
                    });
                }
            });
        }

        function deleteUser(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Kamu tidak akan bisa mengembalikan data pengguna ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: appUrl + '/api/member/delete-user/',
                        method: 'post',
                        data: {
                            id: id,
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses!',
                                    text: 'User berhasil dihapus.',
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message || 'Terjadi kesalahan saat menghapus user.',
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: 'Gagal mengirim permintaan. Silakan coba lagi.',
                            });
                        }
                    });
                }
            });
        }

        $(document).on("click", "#bayar-midtrans", function() {
            var id_cart = $(this).data("id_cart");
            var id_member = $(this).data("id_member");
            var cond = 'payment';

            Swal.fire({
                title: "Pembayran Kartu Kredit digital",
                text: "Apakah Anda yakin ingin melakukan pembayran transaksi ini?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Ya, Bayar",
                cancelButtonText: "Tidak"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: appUrl + "/api/midtrans/request-payment",
                        type: "POST",
                        data: {
                            id_cart: id_cart,
                            id_member: id_member,
                            cond: cond
                        },
                        success: function(response) {
                            if (response && response.token) {
                                var token = response.token;
                                console.log("Token yang diambil:", token);
                                snap.pay(token, {
                                    onSuccess: function(result) {
                                        console.log("Payment Success:", result);
                                        getnewtoken(id_member, id_cart, cond);
                                    },
                                    // Optional
                                    onPending: function(result) {
                                        console.log("Payment Pending:", result);
                                        getnewtoken(id_member, id_cart, cond);
                                    },
                                });
                            } else {
                                console.log("Token tidak ditemukan dalam respons");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log("Error approving transaction: " + error);
                        }
                    });
                }
            });
        });

        function getnewtoken(id_user, id_cart, cond) {
            console.log('masuk');
            $.ajax({
                url: appUrl + "/api/midtrans/status",
                type: "get",
                data: {
                    id_user: id_user,
                    id_cart: id_cart,
                    cond: cond,
                },
                dataType: "JSON",
                success: function(response) {
                    kembaliKemenuTransaksi();
                },
                error: function(xhr, status, error) {
                    console.log("Error approving transaction: " + error);
                }
            });
        }

        $(document).on("click", "#upload-payment-va", function() {
            var id_cart = $(this).data("id_cart");
            var total = $(this).data("total");
            var va_number = $(this).data("va_number");
            var id_payment = $(this).data("id_payment");

            if (id_payment == 30) {
                var html = `
                <p>
                    Nama Bank Tujuan    : PT. Elite Proxy Sistem <br>
                    Bank Tujuan         : Bank BCA Virtual Acount <br>
                    No Virtual Acount   : <b> ${va_number} </b> <br>
                    Total Pembayaran    : <b> ${formatRupiah(total)} </b>
                </p>
                <img id="swal2-image-preview" src="${ bukti_transfer ? bukti_transfer : '#' }" alt="Bukti Transfer" style="max-width: 200px; max-height: 200px; display: ${ bukti_transfer ? '' : 'none' };">
                <input type="file" id="swal2-file" name="img" accept="image/*" style="display: block; margin-top: 10px;">
            `;

                Swal.fire({
                    title: "Pembayaran Virtual Account ",
                    html: html,
                    showCancelButton: true,
                    confirmButtonText: "Unggah",
                    cancelButtonText: "Batal",
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return new Promise((resolve, reject) => {
                            var fileInput = document.getElementById("swal2-file");
                            var file = fileInput.files[0];
                            if (!file) {
                                reject("Anda harus memilih file gambar.");
                            } else {
                                resolve(file);
                            }
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.isConfirmed) {
                        var file = result.value;
                        var formData = new FormData();
                        formData.append("id_cart", id_cart);
                        formData.append("img", file);

                        $.ajax({
                            url: appUrl + "/api/upload-payment",
                            type: "POST",
                            data: formData,
                            contentType: false,
                            processData: false,
                            success: function(response) {
                                Swal.fire({
                                    title: "Upload Berhasil",
                                    text: "Pembayaran telah diunggah.",
                                    icon: "success",
                                }).then(function() {
                                    kembaliKemenuTransaksi();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire({
                                    title: "Upload Gagal",
                                    text: "Terjadi kesalahan saat mengunggah pembayaran.",
                                    icon: "error",
                                });
                            },
                        });
                    }
                });
            } else if (id_payment == 31) {
                console.log("BNI VA");
            }
        });

        $(document).on("click", "#upload-payment", function() {
            var id_cart = $(this).data("id_cart");
            var total = $(this).data("total");

            var html = `
        <p>
            Nama Bank Tujuan    : PT. Elite Proxy Sistem <br>
            Bank Tujuan         : Bank BNI <br>
            No Rek Tujuan       : <b> 03975-60583 </b> <br>
            Total Pembayaran    : <b> ${formatRupiah(total)} </b>
        </p>
        <img id="swal2-image-preview" src="${ bukti_transfer ? bukti_transfer : '#' }" alt="Bukti Transfer" style="max-width: 200px; max-height: 200px; display: ${ bukti_transfer ? '' : 'none' };">
        <input type="file" id="swal2-file" name="img" accept="image/*" style="display: block; margin-top: 10px;">
    `;

            Swal.fire({
                title: "Upload Pembayaran",
                html: html,
                showCancelButton: true,
                confirmButtonText: "Unggah",
                cancelButtonText: "Batal",
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return new Promise((resolve, reject) => {
                        var fileInput = document.getElementById("swal2-file");
                        var file = fileInput.files[0];
                        if (!file) {
                            reject("Anda harus memilih file gambar.");
                        } else {
                            resolve(file);
                        }
                    });
                },
                allowOutsideClick: () => !Swal.isLoading(),
            }).then((result) => {
                if (result.isConfirmed) {
                    var file = result.value;
                    var formData = new FormData();
                    formData.append("id_cart", id_cart);
                    formData.append("img", file);

                    $.ajax({
                        url: appUrl + "/api/upload-payment",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            Swal.fire({
                                title: "Upload Berhasil",
                                text: "Pembayaran telah diunggah.",
                                icon: "success",
                            }).then(function() {
                                kembaliKemenuTransaksi();
                            });
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                title: "Upload Gagal",
                                text: "Terjadi kesalahan saat mengunggah pembayaran.",
                                icon: "error",
                            });
                        },
                    });
                }
            });
        });

        $(document).on("change", "#swal2-file", function() {
            previewImage(this);
        });

        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $("#swal2-image-preview").attr("src", e.target.result).show();
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        function kembaliKemenuTransaksi() {
            const contentArea = document.getElementById('contentArea');
            const url = appUrl + '/profile/transaksi';

            // Menampilkan pesan loading
            contentArea.innerHTML = `<h5>Memuat konten untuk Transaksi...</h5>`;

            // Melakukan request AJAX
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    contentArea.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error:', error);
                    contentArea.innerHTML = `<h5>Terjadi kesalahan saat memuat konten untuk Transaksi. Silakan coba lagi.</h5>`;
                });
        }
    </script>
</body>

</html>