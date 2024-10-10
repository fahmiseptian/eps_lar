<!-- Footer -->
<footer>
    <div class="footer-content">
        <!-- Kolom Layanan Pelanggan -->
        <div class="footer-column">
            <h3>Layanan Pelanggan</h3>
            <ul>
                <li>Hubungi Kami</li>
                <li>Pusat Bantuan</li>
                <li>Kebijakan Pengembalian</li>
                <li>Kebijakan Privasi</li>
                <li>Ketentuan Layanan</li>
                <li>FAQ</li> <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
        <!-- Kolom Jelajahi -->
        <div class="footer-column">
            <h3>Jelajahi</h3>
            <ul>
                <li>Kategori</li>
                <li>Produk Populer</li>
                <li>Penjual Terbaik</li>
                <li>Diskon</li>
                <li>Baru Tiba</li>
                <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
        <!-- Kolom Pembayaran -->
        <div class="footer-column">
            <h3>Pembayaran</h3>
            <ul>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Metode Pembayaran"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Promo Pembayaran"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Kebijakan Pembayaran"
                        style="vertical-align: middle;">
                </li>
                <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
        <!-- Kolom Pengiriman -->
        <div class="footer-column">
            <h3>Pengiriman</h3>
            <ul>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Metode Pengiriman"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Opsi Pengiriman"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Biaya Pengiriman"
                        style="vertical-align: middle;">
                </li>
                <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>



<!-- Footer -->
<footer>
    <div class="footer-content">
        <!-- Kolom Layanan Pelanggan -->
        <div class="footer-column">
            <h3>Layanan Pelanggan</h3>
            <ul>
                <li>Hubungi Kami</li>
                <li>Pusat Bantuan</li>
                <li>Kebijakan Pengembalian</li>
                <li>Kebijakan Privasi</li>
                <li>Ketentuan Layanan</li>
                <li>FAQ</li> <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
        <!-- Kolom Jelajahi -->
        <div class="footer-column">
            <h3>Jelajahi</h3>
            <ul>
                <li>Kategori</li>
                <li>Produk Populer</li>
                <li>Penjual Terbaik</li>
                <li>Diskon</li>
                <li>Baru Tiba</li>
                <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
        <!-- Kolom Pembayaran -->
        <div class="footer-column">
            <h3>Pembayaran</h3>
            <ul>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Metode Pembayaran"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Promo Pembayaran"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Kebijakan Pembayaran"
                        style="vertical-align: middle;">
                </li>
                <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
        <!-- Kolom Pengiriman -->
        <div class="footer-column">
            <h3>Pengiriman</h3>
            <ul>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Metode Pengiriman"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Opsi Pengiriman"
                        style="vertical-align: middle;">
                </li>
                <li>
                    <img src="https://via.placeholder.com/50x30" alt="Biaya Pengiriman"
                        style="vertical-align: middle;">
                </li>
                <!-- Tambahkan lebih banyak item sesuai kebutuhan -->
            </ul>
        </div>
    </div>
</footer>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ secure_asset('plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script> 

<script>
    $(document).ready(function() {
        var searchTimer;

        $('#search-input').on('input', function() {
            clearTimeout(searchTimer);
            var query = $(this).val();

            if (query.length >= 2) {
                $('#search-results').show();
                $('#search-loading').show();
                $('#quick-search-results').html('');
                $('#full-search-results').html('');
            } else {
                $('#search-results').hide();
                return;
            }

            searchTimer = setTimeout(function() {
                if (query.length >= 2) {
                    $.ajax({
                        url: appUrl + '/api/quick-search?token=' + token,
                        method: 'GET',
                        data: {
                            query: query
                        },
                        success: function(response) {
                            $('#search-loading').hide();
                            $('#quick-search-results').html(response);
                        },
                        error: function(xhr, status, error) {
                            $('#search-loading').hide();
                            console.error("AJAX error: " + status + ": " + error);
                        }
                    });
                } else {
                    $('#search-loading').hide();
                    $('#quick-search-results').html('');
                    $('#search-results').hide();
                }
            }, 500);
        });

        $('#search-button').on('click', function() {
            var query = $('#search-input').val();
            if (query.length >= 2) {
                window.location.href = appUrl + '/find/' + query + '?token=' + token;
            }
        });

        // Sembunyikan hasil pencarian ketika klik di luar area pencarian
        $(document).on('click', function(event) {
            if (!$(event.target).closest('.search-container').length) {
                $('#search-results').hide();
            }
        });
    });
</script>