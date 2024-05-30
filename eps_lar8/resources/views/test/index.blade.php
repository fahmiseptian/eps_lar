<!-- resources/views/products/index.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Products</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <h1>Products</h1>
        <button id="show-products" class="btn btn-primary">Show Products</button>
        <button id="show-complete-cart-shop" class="btn btn-secondary">Show Complete Cart Shop</button>
        <div id="content-data" class="mt-4">
            <!-- Data produk akan dimasukkan di sini menggunakan AJAX -->
        </div>
    </div>

    <script>
        $(document).ready(function(){
            // Tambahkan event listener untuk tombol "Show Products"
            $('#show-products').click(function(){
                // Kirim permintaan AJAX ke server untuk mengambil data produk
                $.ajax({
                    url: '{{ route("fetch-products") }}',
                    type: 'get',
                    success: function(response){
                        // Manipulasi DOM untuk menambahkan produk ke halaman
                        $("#content-data").empty();
                        var products = response.products;
                        var html = '';
                        $.each(products, function(index, product){
                            html += '<div class="col-md-4">';
                            html += '<div class="card">';
                            html += '<img class="card-img-top" src="'+product.image_url+'" alt="'+product.name+'">';
                            html += '<div class="card-body">';
                            html += '<h5 class="card-title">'+product.name+'</h5>';
                            html += '<p class="card-text">'+product.description+'</p>';
                            html += '<p class="card-text">Price: '+product.price+'</p>';
                            html += '</div></div></div>';
                        });
                        $('#content-data').html('<div class="row">'+html+'</div>');
                    },
                    error: function(xhr, status, error){
                        console.error(error);
                    }
                });
            });
        });
    </script>
</body>
</html>
