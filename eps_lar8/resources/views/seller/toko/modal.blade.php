<!-- Modal HTML -->
<div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="passwordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="passwordModalLabel">Ganti Password</h5>
            </div>
            <div class="modal-body">
                <form id="passwordForm">
                    <div class="form-group">
                        <label for="old_password">Password Lama</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="old_password"
                                placeholder="Masukkan password baru">
                            <i class="fa fa-eye-slash toggle-eye" data-target="#old_password"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="new_password"
                                placeholder="Masukkan password baru">
                            <i class="fa fa-eye-slash toggle-eye" data-target="#new_password"></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <div class="password-container">
                            <input type="password" class="form-control" id="confirm_password"
                                placeholder="Konfirmasi password baru">
                            <i class="fa fa-eye-slash toggle-eye" data-target="#confirm_password"></i>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup </button>
                <button type="button" class="btn btn-primary" id="savePasswordBtn">Simpan</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="upload_File_Modal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="uploadModalLabel">Upload File</h5>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="jenis" id="jenis" value="">
                    <div class="form-group">
                        <label for="file">Pilih file untuk diupload:</label>
                        <input type="file" id="file" name="file" class="form-control">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup </button>
                <button type="button" id="uploadBtn" class="btn btn-primary">Upload</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal for uploading banner -->
<div class="modal fade" id="uploadBannerModal" tabindex="-1" aria-labelledby="uploadBannerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="uploadBannerModalLabel">Upload Banner</h5>
            </div>
            <div class="modal-body">
                <form id="bannerForm">
                    <div class="mb-3">
                        <label for="bannerFile" class="form-label">Pilih File Banner</label>
                        <input class="form-control" type="file" id="bannerFile" name="file" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <br>
                        <h3>Preview Tampilan user</h3>
                        <br>
                        <!-- Preview of the banner image -->
                        <div class="card-profile-priview" id="bannerPreview" style="background-image: url('#');">
                            <div class="text-profile">
                                <div>
                                    <img src="{{ asset('/img/app/default_seller.jpg') }}" alt="Icon-toko">
                                </div>
                                <div class="data-toko">
                                    <h2>Nama Toko</h2>
                                    <p>Waktu Bergabung 7 Juni 2024</p>
                                    <div style="display: flex">
                                        <p>Pengikut 00</p>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                        <p>Mengikuti 00</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup
                </button>
                <button type="button" class="btn btn-primary" id="uploadBannerButton">Upload</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for editing profile -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
            </div>
            <div class="modal-body">
                <form id="editProfileForm">
                    <div class="mb-3">
                        <label for="avatarImage" class="form-label">Gambar Avatar</label>
                        <div class="avatar-preview mb-3">
                            <img id="avatarPreview" src="" alt="Avatar Preview"
                                style="width: 50%; height:auto;">
                        </div>
                        <input class="form-control" type="file" id="avatarImage" name="avatar"
                            accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label for="profileName" class="form-label">Nama Profil</label>
                        <input type="text" class="form-control" id="profileName" name="name"
                            placeholder="Nama Profil">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup
                </button>
                <button type="button" class="btn btn-primary" id="saveProfileButton">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahEtalase" tabindex="-1" aria-labelledby="modalTambahEtalaseLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title" id="modalTambahEtalaseLabel">Tambah Etalase</h5>
            </div>
            <div class="modal-body">
                <form id="formTambahEtalase">
                    <div class="mb-3">
                        <label for="namaEtalase" class="form-label">Nama Etalase</label>
                        <input type="text" class="form-control" id="namaEtalase" name="namaEtalase"
                            placeholder="Masukkan nama etalase" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Tutup
                </button>
                <button type="submit" form="formTambahEtalase" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
