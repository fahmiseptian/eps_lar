<form id="addUserFormPP">
    <div class="form-group">
        <label for="fullName">Nama Lengkap</label>
        <input type="text" class="form-control" id="fullName" value="{{ $user->nama }}" readonly required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly required>
    </div>
    <div class="form-group">
        <label for="department">Departemen</label>
        <input type="text" class="form-control" id="department" value="{{ $user->satker }}" readonly required>
    </div>
    <div class="form-group">
        <label for="jabatan">Jabatan</label>
        <input type="text" class="form-control" id="jabatan" value="{{ $user->jabatan }}" readonly required>
    </div>
    <div class="form-group">
        <label for="role">Role</label>
        <select class="form-control" id="role" disabled>
            <option value="3" {{ $user->id_member_type == 3 ? 'selected' : '' }}>Pemohon</option>
            <option value="4" {{ $user->id_member_type == 4 ? 'selected' : '' }}>Penyetuju Pemohon</option>
            <option value="6" {{ $user->id_member_type == 6 ? 'selected' : '' }}>Finance</option>
        </select>
    </div>
    <div class="form-group">
        <label for="phone">No Handphone</label>
        <input type="text" class="form-control" id="phone" value="{{ $user->no_hp }}" readonly required>
    </div>
    <div class="form-group">
        <label for="status">Status</label>
        <select class="form-control" id="status" disabled>
            <option value="Y" {{ $user->member_status == 'active' ? 'selected' : '' }}>Aktif</option>
            <option value="N" {{ $user->member_status != 'active' ? 'selected' : '' }}>Tidak Aktif</option>
        </select>
    </div>

    <!-- Additional Fields for 'Penyetuju Pemohon' Role -->
    @if ($user->id_member_type == 4)
    <div id="additionalFields">
        <div class="form-group">
            <label for="batasAwal">Batas Awal</label>
            <input type="text" class="form-control" id="batasAwal" value="Rp. {{ number_format($user->limit_start, 0, ',', '.') }}" readonly required>
        </div>
        <div class="form-group">
            <label for="batasAkhir">Batas Akhir</label>
            <input type="text" class="form-control" id="batasAkhir" value="Rp. {{ number_format($user->limit_end, 0, ',', '.') }}" readonly required>
        </div>
    </div>
    @endif
</form>