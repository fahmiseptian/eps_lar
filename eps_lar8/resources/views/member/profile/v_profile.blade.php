<!DOCTYPE html>
<html lang="id">
@include('member.asset.header')
<link href="{{ asset('/css/profile.css') }}" rel="stylesheet" type="text/css" />


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
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('profile.transaksi') }}"><span class="material-icons">swap_horiz</span> Lihat Transaksi</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href=""><span class="material-icons">handshake</span> Nego</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#favorite"><span class="material-icons">favorite</span> Favorite</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="settingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="material-icons">settings</span> Setting
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="settingDropdown">
                                    <li><a class="dropdown-item" href="#profile"><span class="material-icons">person</span> Profile</a></li>
                                    <li><a class="dropdown-item" href="#alamat"><span class="material-icons">location_on</span> Alamat</a></li>
                                    <li><a class="dropdown-item" href="#gantiPassword"><span class="material-icons">vpn_key</span> Ganti Password</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" id="manajemenDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="material-icons">manage_accounts</span> Manajemen
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="manajemenDropdown">
                                    <li><a class="dropdown-item" href="#userPemohon"><span class="material-icons">person_add</span> User Pemohon</a></li>
                                    <li><a class="dropdown-item" href="#userPenyetuju"><span class="material-icons">how_to_reg</span> User Penyetuju</a></li>
                                    <li><a class="dropdown-item" href="#userFinance"><span class="material-icons">account_balance</span> User Finance</a></li>
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
    </script>
</body>

</html>