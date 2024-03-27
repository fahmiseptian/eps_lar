<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">
        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')
        <div class="content-wrapper">
            @include('seller.asset.section-info')
            <section class="content">
                <h4 class="page-header">
                    Cek Informasi Toko tentang
                    <small>Ini merupakan informasi tentang toko mu</small>
                </h4>
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>
                                    <?= $neworder ?>
                                </h3>
                                <p>
                                    Transaksi Baru
                                </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>
                                    <?= $persentase_sukses ?><sup style="font-size: 20px">%</sup>
                                </h3>
                                <p>
                                    Persentase Nego Sukses
                                </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>
                                    <?= $nego ?>
                                </h3>
                                <p>
                                    Nego baru
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-comments"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>
                                    <?= $ondelivery ?>
                                </h3>
                                <p>
                                    Dalam Pengiriman
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-truck"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>
                                    <?= $order ?>
                                </h3>
                                <p>
                                    Total Transaksi
                                </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-cart-outline"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>
                                    <?= $persentaseorder ?><sup style="font-size: 20px">%</sup>
                                </h3>
                                <p>
                                    Persentase Order Selesai
                                </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-briefcase-outline"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-teal">
                            <div class="inner">
                                <h3>
                                    <?= $start_time ?> - <?= $end_time ?>
                                </h3>
                                <p>
                                    <?= $Operational ?>
                                </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-alarm-outline"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-maroon">
                            <div class="inner">
                                <h3>
                                    <?= $jmlhproduct ?>
                                </h3>
                                <p>
                                    Products
                                </p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-ios-pricetag-outline"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                More info <i class="fa fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
<script src="{{ asset('/js/function/seller/dashboard.js') }}" type="text/javascript"></script>

@include('seller.asset.footer')

</html>
