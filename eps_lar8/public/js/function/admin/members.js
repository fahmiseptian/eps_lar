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
                            <p><strong>Alamat:</strong> ${member.npwp_address || ''}</p>
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