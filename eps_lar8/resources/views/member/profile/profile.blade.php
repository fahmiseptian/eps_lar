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