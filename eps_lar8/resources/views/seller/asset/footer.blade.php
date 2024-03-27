    <script>
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
    </script>
    <!-- FastClick -->
    <script src='{{ asset('/plugins/fastclick/fastclick.min.js') }}'></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/js/app.min.js') }}" type="text/javascript"></script>
    <!-- Sparkline -->
    <script src="{{ asset('/plugins/sparkline/jquery.sparkline.min.js') }}" type="text/javascript"></script>
    <!-- jvectormap -->
    <script src="{{ asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}" type="text/javascript"></script>
    <!-- daterangepicker -->
    <script src="{{ asset('/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <!-- datepicker -->
    <script src="{{ asset('/plugins/datepicker/bootstrap-datepicker.js') }}" type="text/javascript"></script>
    <!-- iCheck -->
    <script src="{{ asset('/plugins/iCheck/icheck.min.js') }}" type="text/javascript"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="{{ asset('/plugins/slimScroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <!-- DATA TABES SCRIPT -->
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- ChartJS 1.0.1 -->
    <script src="{{ asset('/plugins/chartjs/Chart.min.js') }}" type="text/javascript"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" type="text/javascript"></script>
    <!-- jQuery 2.1.3 -->
    <script src="{{ asset('/plugins/jQuery/jQuery-2.1.3.min.js') }}"></script>
    <!-- Bootstrap 3.3.2 JS -->
    <script src="{{ asset('/bootstraps/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <!-- DATA TABES SCRIPT -->
    <script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}" type="text/javascript"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('/plugins/slimScroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
    <!-- FastClick -->
    <script src='{{ asset('/plugins/fastclick/fastclick.min.js') }}'></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('/js/app.min.js') }}" type="text/javascript"></script>