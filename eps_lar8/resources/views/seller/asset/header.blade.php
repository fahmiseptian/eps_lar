<head>
    <meta charset="UTF-8">
    <title><?= $title ?></title>
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
    <link href="{{ asset('/css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('/css/skins/_all-skins.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- Sweet Alert -->
    <script src="{{ asset('/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
    <link href="{{ asset('/plugins/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com" rel="stylesheet">
</head>
