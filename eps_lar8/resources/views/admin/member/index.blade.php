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
                  <h3 class="box-title">List Invoice</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Email</th>
                            <th>Nama</th>
                            <th>No Telepon</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1 @endphp
                        @foreach ($members as $member)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->nama }}</td>
                            <td>{{ $member->no_hp }}</td>
                            <td>
                                <button onclick="showDetail('{{ $member->id }}')" class="btn btn-info">Detail</button>
                                <button onclick="toggleStatus('{{ $member->id }}')" class="toggle-status btn {{ $member->member_status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                    {{ $member->member_status === 'active' ? 'Aktif' : 'Suspend' }}
                                </button>                                
                                <button onclick="deleteMember('{{ $member->id }}')" class="btn btn-danger">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Email</th>
                            <th>Nama</th>
                            <th>No Telepon</th>
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
              $(function () {
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

              function showDetail(id) {
                // Menampilkan loading spinner
                Swal.fire({
                    title: 'Memuat...',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                // Mengambil data anggota menggunakan AJAX
                $.ajax({
                    url: '/admin/member/' + id,
                    method: 'GET',
                    success: function(response) {
                        var member = response.member;

                        if (member) {

                            // Menampilkan informasi anggota dengan SweetAlert
                            Swal.fire({
                                title: 'Detail Anggota',
                                html: `
                                    <div style="text-align: justify;">
                                        <p><strong>Email:</strong> ${member.email || ''}</p>
                                        <p><strong>Instansi Satuan Kerja:</strong> ${member.instansi || ''}</p>
                                        <p><strong>Nama:</strong> ${member.nama || ''}</p>
                                        <p><strong>No Telepon:</strong> ${member.no_telpon || ''}</p>
                                        <p><strong>NPWP:</strong> ${member.npwp || ''}</p>
                                        <p><strong>Alamat:</strong> ${member.alamat || ''}</p>
                                    </div>
                                `,
                                confirmButtonText: 'Tutup',
                            });
                        } else {
                            // Menampilkan pesan jika data anggota tidak ditemukan
                            Swal.fire({
                                title: 'Detail Anggota',
                                text: 'Data anggota tidak ditemukan.',
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

                function toggleStatus(id) {
                    Swal.fire({
                        title: 'Memuat...',
                        html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
                        showConfirmButton: false,
                        allowOutsideClick: false
                    });
                    // Mengirimkan permintaan AJAX untuk menghapus anggota dengan ID yang diberikan
                    console.log('Toggle status member dengan ID:', id);
                        $.ajax({
                        url: '/admin/member/' + id + '/toggle-status',
                        type: 'GET', 
                        success: function(response) {
                            console.log('Status anggota berhasil diubah');
                            location.reload();
                        },
                        error: function(xhr, status, error) {
                            console.error('Terjadi kesalahan saat mengubah status anggota:', error);
                            alert('Terjadi kesalahan saat mengubah status anggota.');
                        }
                    });
                }




                function deleteMember(id) {
                    // Mengirimkan permintaan AJAX untuk menghapus anggota dengan ID yang diberikan
                    if (confirm('Apakah Anda yakin ingin menghapus anggota ini?')) {
                        $.ajax({
                            url: '/admin/member/' + id + '/delete',
                            method: 'GET',
                            success: function(response) {
                                alert('Anggota berhasil dihapus.');
                                // Refresh halaman untuk memperbarui tampilan
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                alert('Terjadi kesalahan saat menghapus anggota.');
                            }
                        });
                    }
                }
            </script>
</html>



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
