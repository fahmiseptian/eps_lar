<div class="profile-container">
    <h3 class="profile-title">Profil Saya</h3>
    <div class="profile-info">
        <div class="profile-item">
            <span class="material-icons profile-icon">person</span>
            <div class="profile-detail">
                <label>Nama Lengkap</label>
                <p>
                    <input id="edit-nama" value="{{ $user->nama }}" placeholder="Nama belum diatur" readonly style="border: none; background: transparent; width: 100%;">
                </p>
            </div>
        </div>
        <div class="profile-item">
            <span class="material-icons profile-icon">email</span>
            <div class="profile-detail">
                <label>Email</label>
                <p>
                    <input id="edit-email" value="{{ $user->email }}" placeholder="Email belum diatur" readonly style="border: none; background: transparent; width: 100%;">
                </p>
            </div>
        </div>
        <div class="profile-item">
            <span class="material-icons profile-icon">phone</span>
            <div class="profile-detail">
                <label>Nomor Telepon</label>
                <p>
                    <input id="edit-phone" value="{{ $user->phone }}" placeholder="Nomor telepon belum diatur" readonly style="border: none; background: transparent; width: 100%;">
                </p>
            </div>
        </div>
        <div class="profile-item">
            <span class="material-icons profile-icon">assignment</span>
            <div class="profile-detail">
                <label>NPWP</label>
                <p>
                    <input id="edit-npwp" value="{{ $user->npwp }}" placeholder="NPWP belum diatur" readonly style="border: none; background: transparent; width: 100%;">
                </p>
            </div>
        </div>
        <div class="profile-item">
            <span class="material-icons profile-icon">home</span>
            <div class="profile-detail">
                <label>Alamat NPWP</label>
                <p>
                    <input id="edit-npwp_address" value="{{ $user->npwp_address }}" placeholder="Alamat NPWP belum diatur" readonly style="border: none; background: transparent; width: 100%;">
                </p>
            </div>
        </div>
    </div>
    <div class="profile-actions">
        <button class="btn btn-primary" id="edit-button" onclick="toggleEdit()">
            <span class="material-icons">edit</span> Edit Profil
        </button>
        <button class="btn btn-primary" id="save-button" style="display: none;" onclick="saveProfile()">
            <span class="material-icons">save</span> Simpan Perubahan
        </button>
    </div>
</div>