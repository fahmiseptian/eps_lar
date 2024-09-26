<style>
    :root {
        --primary-color: #fc6703;
        --secondary-color: #fff0e6;
        --text-color: #333333;
        --hover-color: #e55a00;
        --putih: #fff;
        --background-color: #efefef;
        --samar-background-color: #f9f9f9;
    }

    body {
        font-family: 'Arial', sans-serif;
        background-color: var(--background-color);
    }

    .card-body {
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .alamat-container {
        margin-top: 20px;
        padding: 15px;
        background-color: var(--samar-background-color);
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .alamat-item {
        margin-bottom: 20px;
        border-bottom: 1px solid var(--text-color);
        padding-bottom: 15px;
    }

    .alamat-detail {
        margin-bottom: 10px;
    }

    .alamat-actions {
        margin-top: 10px;
        display: flex;
        justify-content: flex-end;
    }

    .alamat-utama {
        margin-top: 15px;
        display: flex;
        justify-content: flex-end;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: bold;
        margin-left: 10px;
    }

    .btn .material-icons {
        margin-right: 5px;
    }

    /* Gaya untuk tombol */
    .btn-warning {
        background-color: #ffc107;
        color: var(--putih);
    }

    .btn-danger {
        background-color: #dc3545;
        color: var(--putih);
    }

    .btn-info {
        background-color: #17a2b8;
        color: var(--putih);
    }

    .btn-secondary {
        background-color: #6c757d;
        color: var(--putih);
    }

    /* Hover effects */
    .btn:hover {
        opacity: 0.9;
    }

    /* Gaya untuk ikon */
    .material-icons {
        font-size: 20px;
    }

    /* Gaya untuk label */
    label {
        font-weight: bold;
        color: var(--text-color);
    }

    /* Gaya untuk form tambah alamat */
    .form-tambah-alamat {
        display: none;
        /* Sembunyikan form awalnya */
        margin-top: 20px;
        padding: 15px;
        background-color: var(--samar-background-color);
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 15px;
    }

    .form-control {
        border-radius: 5px;
        border: 1px solid #ccc;
        padding: 10px;
        width: 100%;
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 5px rgba(252, 103, 3, 0.5);
    }

    .form-header {
        font-size: 1.5em;
        margin-bottom: 15px;
        color: var(--primary-color);
    }
</style>

<div class="card-body">
    <div class="d-flex justify-content-between">
        <h4 class="card-title">Alamat Saya</h4>
        <button class="btn btn-success" id="tambah-alamat">
            <i class="material-icons">add</i> Tambah Alamat
        </button>
    </div>

    <div class="alamat-container">
        @if(empty($addresses) || count($addresses) === 0)
        <p>Belum ada alamat yang ditambahkan. Silakan tambahkan alamat baru.</p>
        @else
        @foreach($addresses as $alamat)
        <div class="alamat-item">
            <div class="alamat-detail">
                <label><i class="material-icons">person</i> Nama Penerima</label>
                <p>{{ $alamat->address_name ?? 'Nama belum diatur' }}</p>
            </div>
            <div class="alamat-detail">
                <label><i class="material-icons">phone</i> Nomor Telepon</label>
                <p>{{ $alamat->phone ?? 'Nomor telepon belum diatur' }}</p>
            </div>
            <div class="alamat-detail">
                <label><i class="material-icons">home</i> Alamat</label>
                <p>{{ $alamat->address ?? 'Alamat belum diatur' }}</p>
            </div>
            <div class="alamat-detail">
                <label><i class="material-icons">location_city</i> Kota, Kecamatan</label>
                <p>{{ $alamat->city ?? 'Kota belum diatur' }}, {{ $alamat->subdistrict_name ?? 'Kecamatan belum diatur' }}</p>
            </div>
            <div class="alamat-detail">
                <label><i class="material-icons">map</i> Provinsi</label>
                <p>{{ $alamat->province_name ?? 'Provinsi belum diatur' }}</p>
            </div>
            <div class="alamat-detail">
                <label><i class="material-icons">mail</i> Kode Pos</label>
                <p>{{ "ID" . $alamat->postal_code ?? 'Kode pos belum diatur' }}</p>
            </div>
            <div class="alamat-actions">
                <button class="btn btn-warning" onclick="editAlamat('{{ $alamat->member_address_id }}')">
                    <i class="material-icons">edit</i> Edit
                </button>
                <button class="btn btn-danger" onclick="hapusAlamat('{{ $alamat->member_address_id }}')">
                    <i class="material-icons">delete</i> Hapus
                </button>
            </div>
            <div class="alamat-utama">
                @if ($alamat->is_default_billing == 'yes')
                <button class="btn btn-secondary" disabled>
                    <i class="material-icons">payment</i> Alamat Penagihan Utama
                </button>
                @else
                <button class="btn btn-danger" onclick="aturSebagaiPenagihan('{{ $alamat->member_address_id }}')">
                    <i class="material-icons">payment</i> Atur Sebagai Penagihan
                </button>
                @endif

                @if ($alamat->is_default_shipping == 'yes')
                <button class="btn btn-secondary" disabled>
                    <i class="material-icons">star</i> Alamat Pengiriman Utama
                </button>
                @else
                <button class="btn btn-info" onclick="aturAlamatUtama('{{ $alamat->member_address_id }}')">
                    <i class="material-icons">star</i> Atur Sebagai Alamat Utama
                </button>
                @endif
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>