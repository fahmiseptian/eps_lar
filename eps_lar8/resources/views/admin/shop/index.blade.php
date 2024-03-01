<!DOCTYPE html>
<html>
@include('admin.asset.header')

<body class="skin-blue">
    <div class="wrapper">

        {{-- Navbar --}}
        @include('admin.asset.navbar')


        {{-- sidebar --}}
        @include('admin.asset.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            {{-- section-info --}}
            @include('admin.asset.section-info')

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">List Shop</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($datashop as $item)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <table>
                                                        <tr>
                                                            <td rowspan="2" style="width: 90%">
                                                                <button onclick="updateType('{{ $item->id }}')"
                                                                    class="btn 
                          @if ($item->type === 'silver') btn-secondary 
                          @elseif($item->type === 'gold') 
                            btn-warning 
                          @elseif($item->type === 'platinum') 
                            btn-info 
                          @elseif($item->type === 'trusted_seller') 
                            btn-success @endif">
                                                                    {{ ucfirst($item->type) }}
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <button onclick="updateTypeUp('{{ $item->id }}')"
                                                                  class="glyphicon glyphicon-upload {{ $item->type === 'trusted_seller' ? 'btn-secondary disabled' : 'btn-success' }}"
                                                                  {{ $item->type === 'trusted_seller' ? 'disabled' : '' }}>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                              <button onclick="updateTypeDown('{{ $item->id }}')"
                                                                class="glyphicon glyphicon-download {{ $item->type === 'silver' ? 'btn-secondary disabled' : 'btn-warning' }}"
                                                                {{ $item->type === 'silver' ? 'disabled' : '' }}>
                                                            </button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <button onclick="detail('{{ $item->id }}')"
                                                        class="btn btn-info">Detail Toko</button>
                                                    <button onclick="updateStatus('{{ $item->id }}')"
                                                        class="btn {{ $item->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                                        {{ $item->status === 'active' ? 'Aktif' : 'Tidak aktif' }}
                                                    </button>
                                                    <button onclick="deleteShop('{{ $item->id }}')"
                                                        class="btn btn-danger">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->
        {{-- section-footer --}}
        @include('admin.asset.section-footer')
    </div><!-- ./wrapper -->
</body>
{{-- footer --}}
@include('admin.asset.footer')

<!-- page script -->
<script type="text/javascript">
    $(function() {
        $("#example1").dataTable();
        $('#example2').dataTable({
            "bPaginate": true,
            "bLengthChange": false,
            "bFilter": false,
            "bSort": true,
            "bInfo": true,
            "bAutoWidth": false
        });
    });

    function detail(id) {
        // Menampilkan loading spinner
        Swal.fire({
            title: 'Memuat...',
            html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        // Mengambil data anggota menggunakan AJAX
        $.ajax({
            url: '/admin/shop/' + id,
            method: 'GET',
            success: function(response) {
                var shop = response.shop;
                var member = response.member;
                if (shop) {
                    // Menampilkan informasi anggota dengan SweetAlert
                    Swal.fire({
                        title: 'Detail Toko',
                        html: `
                                    <div style="text-align: justify;">
                                        <p><strong>Nama Toko:</strong> ${shop.name || ''}</p>
                                        <p><strong>Email :</strong> ${member.email || ''}</p>
                                        <p><strong>No Telepon:</strong> ${shop.phone || ''}</p>
                                        <p><strong>No NIK:</strong> ${shop.nik_pemilik || ''}</p>
                                        <p><strong>NPWP :</strong> ${shop.npwp || ''}</p>
                                    </div>
                                `,
                        confirmButtonText: 'Tutup',
                    });
                } else {
                    // Menampilkan pesan jika data anggota tidak ditemukan
                    Swal.fire({
                        title: 'Detail Toko',
                        text: 'Data Toko tidak ditemukan.',
                        icon: 'error',
                        confirmButtonText: 'Tutup',
                    });
                }
            },
            error: function(xhr, status, error) {
                // Menampilkan pesan kesalahan
                Swal.fire({
                    title: 'Terjadi Kesalahan',
                    text: 'Terjadi kesalahan saat memuat detail anggota.',
                    icon: 'error',
                    confirmButtonText: 'Tutup',
                });
            }
        });
    }

    // Upadte Status
    function updateStatus(id) {
        Swal.fire({
            title: 'Memuat...',
            html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });
        console.log('Shop ID:', id);
        $.ajax({
            url: '/admin/shop/' + id + '/update-status',
            type: 'GET',
            success: function(response) {
                console.log('Status anggota berhasil diubah');
                location.reload();
            },
            error: function(xhr, status, error) {
                console.error('Terjadi kesalahan saat mengubah status toko:', error);
                alert('Terjadi kesalahan saat mengubah status Toko.');
            }
        });
    }

    function deleteShop(id) {
        if (confirm('Apakah Anda yakin ingin menghapus Toko ini?')) {
            $.ajax({
                url: '/admin/shop/' + id + '/delete',
                method: 'GET',
                success: function(response) {
                    alert('Toko berhasil dihapus.');
                    // Refresh halaman untuk memperbarui tampilan
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan saat menghapus Toko.');
                }
            });
        }
    }

    function updateTypeUp(id) {
        Swal.fire({
            title: 'Memuat...',
            html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        console.log('Shop ID:', id);

        $.ajax({
            url: '/admin/shop/' + id + '/update-type-up',
            type: 'GET',
            success: function(response) {
                if (response.message === 'Teratas') {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Tipe toko sudah trusted seller, tidak dapat ditingkatkan lagi.',
                        icon: 'warning',
                        confirmButtonText: 'Tutup',
                    });
                } else {
                    console.log('Tipe toko berhasil ditingkatkan');
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Terjadi kesalahan saat mengubah tipe toko:', error);
                alert('Terjadi kesalahan saat mengubah tipe Toko.');
            }
        });
    }

    function updateTypeDown(id) {
        Swal.fire({
            title: 'Memuat...',
            html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        console.log('Shop ID:', id);

        $.ajax({
            url: '/admin/shop/' + id + '/update-type-down',
            type: 'GET',
            success: function(response) {
                if (response.message === 'Terbawah') {
                    Swal.fire({
                        title: 'Peringatan',
                        text: 'Tipe toko sudah silver, tidak dapat diturunkan lagi.',
                        icon: 'warning',
                        confirmButtonText: 'Tutup',
                    });
                } else {
                    console.log('Tipe toko berhasil diturunkan');
                    location.reload();
                }
            },
            error: function(xhr, status, error) {
                console.error('Terjadi kesalahan saat mengubah tipe toko:', error);
                alert('Terjadi kesalahan saat mengubah tipe Toko.');
            }
        });
    }
</script>

</html>
