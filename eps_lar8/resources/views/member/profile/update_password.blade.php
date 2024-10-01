<style>
    .btn-update-password {
        background-color: #fc6703;
        color: white;
    }

    .btn-update-password:hover {
        background-color: #e55a00;
    }

    .input-group-text {
        cursor: pointer;
    }
</style>

<div class="transactions-container">
    <h3 class="transactions-title">Ganti Password</h3>
    <!-- Current Password -->
    <form>
        <div class="mb-3">
            <label for="currentPassword" class="form-label">Password saat ini</label>
            <div class="input-group">
                <input type="password" class="form-control" id="currentPassword" placeholder="Masukan Password saat ini" required>
                <span class="input-group-text" onclick="togglePasswordVisibility('#currentPassword', this)">
                    <i class="material-icons">visibility</i>
                </span>
            </div>
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="newPassword" class="form-label">Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control" id="newPassword" placeholder="Masukan Password Baru" required>
                <span class="input-group-text" onclick="togglePasswordVisibility('#newPassword', this)">
                    <i class="material-icons">visibility</i>
                </span>
            </div>
        </div>

        <!-- Confirm New Password -->
        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Konfirmasi Password Baru</label>
            <div class="input-group">
                <input type="password" class="form-control" id="confirmPassword" placeholder="Konfirmasi Password Baru" required>
                <span class="input-group-text" onclick="togglePasswordVisibility('#confirmPassword', this)">
                    <i class="material-icons">visibility</i>
                </span>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="d-grid gap-2">
            <button type="button" class="btn btn-update-password" onclick="Update_password()">Update Password</button>
        </div>
    </form>
</div>