<!DOCTYPE html>
<html>
@include('seller.asset.header')


<body class="skin-blue">
    <div class="wrapper">
        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')
        <div class="content-wrapper">
            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <!-- general form elements disabled -->
                        <div class="box-home" style="display: grid; background-color:#fff8ec">
                            <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Pengaturan Pengiriman</b></h3>
		                    <small style="margin-left: 15px; "  >Pengaturan yang berhubungan dengan jasa kirim</small>
                            <hr>
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 15px" rowspan="3">
                                            <p style="font-size: xx-large; margin:5px" class="fa fa-truck"></p>
                                        </th>
                                        <td colspan="2"> <b>Jasa Kirim</b> <br> <small>Aktifkan jasa kirim yang kamu
                                                inginkan.</small> </td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td colspan="2">
                                            <p> <b>Jasa Kirim yang Didukung</b> <br> <small>Nikmati pelayanan jasa kirim
                                                    yang lebih cepat dan handal dengan Jasa Kirim yang Didukung. Perlu
                                                    diingat bahwa kamu membutuhkan printer untuk mencetak label
                                                    pengiriman secara otomatis.</small></p>
                                        </td>
                                    </tr>
                                    @foreach ($datacourier as $item)
                                    <tr>
                                        <td></td>
                                        <td>{{ $item->name }} <a class="fa fa-question-circle" onclick="showDescription('{{ $item->description }}', '{{ $item->max_weight }}')"></a> </td>
                                        <td>
                                            <label class="switch">
                                                <input type="checkbox" data-courier-id="{{ $item->id }}" {{ $item->checked ? 'checked' : '' }} onchange="toggleCourier(this)">
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td style="width: 15px">
                                            <p style="font-size: xx-large; margin:5px" class="fa fa-calendar-o"></p>
                                        </td>
                                        <td><b> Dikirim dalam </b> <br> <small>Ubah jumlah hari "Dikirim dalam" untuk
                                                semua produk yang ada di toko Anda.</small> </td>
                                        <td>
                                            <a id="ubahestimasi" class="btn btn-info">Ubah</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-1"></div>
            </section>
        </div>
    </div>
</body>
@include('seller.asset.footer')

<script src="{{ asset('/js/function/seller/delivery.js') }}" type="text/javascript"></script>


</html>
