<style>
    .negotiation-data {
        margin-top: 20px;
    }

    .negotiation-list {
        list-style-type: none;
        padding: 0;
    }

    .negotiation-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 5px;
    }

    .negotiation-item img {
        margin-right: 10px;
        border-radius: 5px;
    }

    .negotiation-details {
        flex-grow: 1;
        margin-left: 10px;
    }

    .negotiation-actions {
        display: flex;
        gap: 5px;
    }
</style>

<div class="transactions-container">
    <h3 class="transactions-title">Nego Saya</h3>

    <ul class="nav nav-tabs" id="opsinego" role="tablist">
        <li class="nav-item" role="presentation">
            <a class="nav-link negos-opsi {{ $selected_status == 'all' ? 'active' : '' }}" href="{{ route('profile.nego', ['status' => 'all' , 'token' => $token]) }}">Semua</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link negos-opsi {{ $selected_status == 'diajukan' ? 'active' : '' }}" href="{{ route('profile.nego', ['status' => 'diajukan' , 'token' => $token]) }}">Belum Direspon</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link negos-opsi {{ $selected_status == 'nego_ulang' ? 'active' : '' }}" href="{{ route('profile.nego', ['status' => 'nego_ulang' , 'token' => $token]) }}">Nego Ulang</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link negos-opsi {{ $selected_status == 'nego_diterima' ? 'active' : '' }}" href="{{ route('profile.nego', ['status' => 'nego_diterima' , 'token' => $token]) }}">Sudah Disetujui</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link negos-opsi {{ $selected_status == 'nego_ditolak' ? 'active' : '' }}" href="{{ route('profile.nego', ['status' => 'nego_ditolak' , 'token' => $token]) }}">Dibatalkan</a>
        </li>
        <li class="nav-item" role="presentation">
            <a class="nav-link negos-opsi {{ $selected_status == 'selesai' ? 'active' : '' }}" href="{{ route('profile.nego', ['status' => 'selesai' , 'token' => $token]) }}">Selesai</a>
        </li>
    </ul>

    <div id="negotiationData" class="negotiation-data">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Qty</th>
                    <th>Harga Awal</th>
                    <th>Harga Nego</th>
                    <th>Status Nego</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($negos as $nego)
                <tr data-status="{{ $nego->status }}">
                    <td><img src="{{ $nego->image }}" alt="{{ $nego->nama_produk }}" style="width:50px; height:50px;"></td>
                    <td>{{ $nego->nama_produk }}</td>
                    <td>{{ $nego->qty }}</td>
                    <td>Rp {{ number_format($nego->harga_awal, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($nego->harga_nego, 0, ',', '.') }}</td> 
                    <td>{{ ucfirst(str_replace('_', ' ', $nego->status)) }}</td>
                    <td>
                        <button class="btn btn-info detail_nego" data-id="{{ $nego->id }}">Lihat Detail</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
