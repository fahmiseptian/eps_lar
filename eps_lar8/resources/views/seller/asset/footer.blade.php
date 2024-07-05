<!-- jQuery -->
{{-- <script src="{{ asset('/plugins/jQuery/jQuery-2.1.3.min.js') }}"></script> --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 3.3.2 JS -->
<script src="{{ asset('/bootstraps/js/bootstrap.min.js') }}"></script>
<!-- SlimScroll -->
<script src="{{ asset('/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
<!-- FastClick -->
<script src="{{ asset('/plugins/fastclick/fastclick.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('/js/app.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('/plugins/sparkline/jquery.sparkline.min.js') }}"></script>
<!-- jvectormap -->
<script src="{{ asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>
<script src="{{ asset('/plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- datepicker -->
<script src="{{ asset('/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
<!-- iCheck -->
<script src="{{ asset('/plugins/iCheck/icheck.min.js') }}"></script>
<!-- DATA TABES SCRIPT -->
<script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/plugins/datatables/jquery.dataTables.js') }}"></script>
<script src="{{ asset('/plugins/datatables/dataTables.bootstrap.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('/plugins/chartjs/Chart.min.js') }}"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="{{ asset('/js/pages/dashboard2.js') }}"></script>
<!-- AdminLTE for demo purposes -->
{{-- <script src="{{ asset('/js/demo.js') }}"></script> --}}
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
                        window.location.href = "{{ route('seller.login') }}";
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
