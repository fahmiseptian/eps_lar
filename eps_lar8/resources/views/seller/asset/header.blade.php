<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title> Seller Center- ELITE PROXY</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('/bootstraps/css/bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet"
        type="text/css" />
    <link href="http://code.ionicframework.com/ionicons/2.0.0/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/morris/morris.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/plugins/datatables/dataTables.bootstrap.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/skins/_all-skins.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Sweet Alert -->
    <script src="{{ asset('/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <link href="{{ asset('/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    {{-- PDF.js --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script>
    {{-- tiny --}}
    <script src="https://cdn.tiny.cloud/1/f8waatczv445eylx4uaey67x4mj5uh7l669o6t6paor20czb/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
    <link href="{{ asset('/css/Seller_center.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/Seller.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/Responsif.css') }}" rel="stylesheet" type="text/css" />
    <!-- Tambahkan CSS Kustom di sini -->
    <style>
        /* Gaya untuk kolom pencarian di DataTables */
        #example2_filter input,
        #example1_filter input {
            border-radius: 15px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Shadow */
            border: 1px solid #ddd;
            /* Border input */
            padding: 5px 10px;
            /* Padding dalam input */
            font-size: 14px;
            /* Ukuran font */
            background-color: #F1F1F1;
        }

        /* Gaya placeholder dalam kolom pencarian */
        #example2_filter input,
        #example1_filter input::placeholder {
            color: #999;
            /* Warna placeholder */
            font-style: italic;
            /* Gaya font placeholder */
        }

        .dataTables_length select {
            border-radius: 15px !important;
            /* Radius sudut input */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            /* Shadow */
            border: 1px solid #ddd;
            /* Border input */
            padding: 5px 10px;
            /* Padding dalam dropdown */
            font-size: 14px;
            /* Ukuran font */
            background-color: #F1F1F1;
            /* Warna latar belakang */
            color: #333;
            /* Warna teks */
        }
    </style>
</head>


<div id="overlay" style="display: none;">
    <div class="overlay-content">
        <div id="loader" class="loader" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>
</div>

<script>
    const appUrl = "{{ env('APP_URL') }}";
    window.appUrl = appUrl;
</script>
