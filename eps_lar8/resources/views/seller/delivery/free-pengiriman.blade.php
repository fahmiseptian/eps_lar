<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">
        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')
        <div class="content-wrapper">
            <section class="content">
                <h4 class="page-header">
                    Free Pengiriman
                </h4>
            </section>
        </div>
    </div>
</body>
<script src="{{ asset('/js/function/seller/dashboard.js') }}" type="text/javascript"></script>

@include('seller.asset.footer')

</html>
