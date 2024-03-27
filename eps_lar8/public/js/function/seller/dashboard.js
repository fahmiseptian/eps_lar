function confirmLogout(element) {
    var logoutUrl = element.getAttribute('data-logout-url');
    Swal.fire({
        title: 'Anda yakin ingin logout?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, logout',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Memanggil route logout dengan metode GET
            $.ajax({
                url: logoutUrl,
                type: 'GET', // Menggunakan metode GET
                success: function(response) {
                    // Redirect ke halaman logout atau lakukan tindakan lain sesuai respons dari controller
                    window.location.href = "/seller/logout";
                },
                error: function(xhr, status, error) {
                    // Tangani kesalahan jika terjadi
                    console.error(error);
                }
            });
        }
    });
}
