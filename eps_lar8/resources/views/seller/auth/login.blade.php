<!DOCTYPE html>
<html>
@include('seller.asset.header')
<link href="{{ asset('/css/Seller_center.css') }}" rel="stylesheet" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<body class="login-page">
    <section class="content">
        @if (session('error_seller'))
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: "{{ session('error_seller') }}",
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Hapus session error_seller
                        <?php session()->forget('error_seller'); ?>
                    }
                });
            </script>
        @endif
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="logo-login">
                        <img src="{{ asset('/img/app/logo-eps.png') }}" class="logologin" />
                    </div>
                    <div class="box-header">
                        <ul class="horizontal-list">
                            <li><a href="#" class="box-title active" onclick="showlogin()">Masuk</a></li>
                            <li><span class="separator">|</span></li>
                            <li><a href="#" class="box-title" onclick="showSignup()">Daftar</a></li>
                        </ul>
                    </div>
                    <div class="box-body">
                        {{-- Login --}}
                        <form id="loginForm" class="login" action="{{ route('seller.login') }}" method="POST"
                            data-login-url="{{ route('seller.login') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control input-underline"
                                    placeholder="Masukan Email anda" id="email" name="email" required />
                            </div>
                            <div class="form-group position-relative">
                                <label>Password</label>
                                <input type="password" class="form-control input-underline"
                                    placeholder="Masukkan Password Anda" id="password" name="password" required />
                                <i class="material-icons position-absolute" id="toggle-password"
                                    style="right: 10px; top: 35%; cursor: pointer;">visibility_off</i>
                            </div>
                            <div class="form-group">
                                <div class="h-captcha" data-sitekey="09aec88c-7267-4df4-b181-219021898cd1"></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Masuk</button>
                                </div>
                            </div>
                            <br>
                            <a href="#"> Lupa Password? </a>
                        </form>

                        {{-- Register --}}
                        <form class="daftar" style="display: none;">
                            <h4 style="margin-top: 0px"><b>Informasi Pemilik</b></h4>
                            <div class="groups-register" style="margin-top: 0;">
                                <div class="form-group" style="flex: 1;">
                                    <label for="nama-pemilik">Nama Pemilik Sesuai KTP</label>
                                    <input type="text" id="nama-pemilik" name="nama_pemilik"
                                        class="form-control input-underline" placeholder="Masukan Nama Pemilik" />
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email"
                                        class="form-control input-underline" placeholder="Masukan Email anda" />
                                </div>
                            </div>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1;">
                                    <label for="no-telpon">No Telpon</label>
                                    <input type="text" id="no-telpon" name="no_telpon"
                                        class="form-control input-underline" placeholder="Masukan No Telpon" />
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label for="password">Password</label>
                                    <input type="password" id="password" name="password"
                                        class="form-control input-underline" placeholder="Masukan Password anda" />
                                </div>
                            </div>
                            <h4><b>Informasi Usaha</b></h4>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1;">
                                    <label for="nama-perusahaan">Nama Perusahaan</label>
                                    <input type="text" id="nama-perusahaan" name="nama_perusahaan"
                                        class="form-control input-underline"
                                        placeholder="Masukan Nama Perusahaan anda" />
                                </div>
                                <div class="form-group" style="flex: 1;">
                                    <label for="npwp">NPWP</label>
                                    <input type="text" id="npwp" name="npwp"
                                        class="form-control input-underline" placeholder="Masukan NPWP anda" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="nik">NIK Pemilik sesuai KTP</label>
                                <input type="text" id="nik" name="nik"
                                    class="form-control input-underline"
                                    placeholder="Masukan NIK Pemilik sesuai KTP" />
                            </div>
                            <small style="color:red;">
                                Lampiran file hanya menerima format : png, jpg, jpeg, pdf
                            </small>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <label class="custom-file-upload">
                                        <input type="file" id="akta_pendirian-file" name="akta_pendirian-file"
                                            class="form-control" style="display: none;"
                                            onchange="updateFileName(this,'file-akta_pendirian')" />
                                        File
                                    </label>
                                    <input type="text" id="file-akta_pendirian"
                                        class="form-control input-underline" placeholder="Akta Pendirian" disabled>
                                </div>
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <label class="custom-file-upload">
                                        <input type="file" id="akta_perubahan-file" name="akta_perubahan-file"
                                            class="form-control" style="display: none;"
                                            onchange="updateFileName(this,'file-akta_perubahan')" />
                                        File
                                    </label>
                                    <input type="text" id="file-akta_perubahan"
                                        class="form-control input-underline" placeholder="Akta Perubahan" disabled>
                                </div>
                            </div>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <label class="custom-file-upload">
                                        <input type="file" id="NIB-file" name="NIB-file" class="form-control"
                                            style="display: none;" onchange="updateFileName(this, 'file-NIB')" />
                                        File
                                    </label>
                                    <input type="text" id="file-NIB" class="form-control input-underline"
                                        placeholder="NIB" disabled>
                                </div>
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <label class="custom-file-upload">
                                        <input type="file" id="ktp-file" name="ktp-file" class="form-control"
                                            style="display: none;" onchange="updateFileName(this, 'file-ktp')" />
                                        File
                                    </label>
                                    <input type="text" id="file-ktp" class="form-control input-underline"
                                        placeholder="KTP Direktur Utama" disabled>
                                </div>
                            </div>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <label class="custom-file-upload">
                                        <input type="file" id="npwp-file" name="npwp-file" class="form-control"
                                            style="display: none;" onchange="updateFileName(this, 'file-npwp')" />
                                        File
                                    </label>
                                    <input type="text" id="file-npwp" class="form-control input-underline"
                                        placeholder="NPWP" disabled>
                                </div>
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <label class="custom-file-upload">
                                        <input type="file" id="pkp-file" name="pkp-file" class="form-control"
                                            style="display: none;" onchange="updateFileName(this, 'file-pkp')" />
                                        File
                                    </label>
                                    <input type="text" id="file-pkp" class="form-control input-underline"
                                        placeholder="PKP" disabled>
                                </div>
                            </div>
                            <small style="color:red;">
                                Alamat sesuai NPWP
                            </small>
                            <div class="form-group">
                                <label for="alamat-npwp">Alamat NPWP</label>
                                <input type="text" id="alamat-npwp" name="alamat_npwp"
                                    class="form-control input-underline" placeholder="Masukan Alamat NPWP" />
                            </div>
                            <div class="form-group">
                                <label for="kategori-toko">Kategori Toko</label>
                                <select id="kategori-toko" name="kategori_toko" class="form-control">
                                    <option value="" disabled selected>Pilih Kategori</option>
                                </select>
                            </div>

                            <h4><b>Informasi Alamat Pengiriman</b></h4>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <select id="dropdown-provinsi" name="provinsi" class="form-control">
                                        <option value="" disabled selected>Pilih Provinsi</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <select id="dropdown-kota" name="kota" class="form-control">
                                        <option value="" disabled selected>Pilih Kota / Kabupaten</option>
                                    </select>
                                </div>
                            </div>
                            <div class="groups-register">
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <select id="dropdown-kecamatan" name="kecamatan" class="form-control">
                                        <option value="" disabled selected>Pilih Kecamatan</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1; display: flex; align-items: center;">
                                    <input type="number" id="kode-pos" name="kode_pos"
                                        class="form-control input-underline" placeholder="Masukan Kode Pos" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="alamat-detail">Alamat </label>
                                <textarea id="alamat-detail" name="alamat_detail" class="form-control" rows="3"
                                    placeholder="Masukan Alamat Detail"></textarea>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Daftar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('seller.asset.section-footer-login')
    {{-- footer --}}

    <script src="{{ asset('/js/function/seller/login.js') }}" type="text/javascript"></script>

</body>
@include('seller.asset.footer')

</html>
