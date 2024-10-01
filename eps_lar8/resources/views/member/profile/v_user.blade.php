<div class="container mt-4 transactions-container">
    <h3 class="transactions-title">User {{ ucwords(str_replace('_', ' ', $tipe)) }}</h3>
    <button class="btn btn-primary mb-3" id="addDataBtn" onclick="open_modal_pp()">Tambah Data</button>

    <table id="userTable" class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>Email</th>
                <th>Departemen</th>
                <th>No Handphone</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($member_satker as $key => $user)
            <tr>
                <td>{{ $key + 1 }}</td> 
                <td>{{ $user->nama }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->satker }}</td>
                <td>{{ $user->no_hp }}</td>
                <td>{{ $user->member_status }}</td>
                <td>
                    <button class="btn btn-info" onclick="detailUser({{ $user->id }})">Detail</button>
                    <button class="btn btn-danger" onclick="deleteUser({{ $user->id }})">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>