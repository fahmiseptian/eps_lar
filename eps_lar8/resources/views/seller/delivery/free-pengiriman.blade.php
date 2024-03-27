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
                        <div class="box box-warning">
                            <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Pengaturan Pengiriman</b></h3>
                            <small style="margin-left: 15px; ">Pengaturan yang berhubungan dengan jasa kirim</small>
                            <hr>
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th style="width: 15px" rowspan="3">
                                            <p style="font-size: xx-large; margin:5px" class="fa fa-flag"></p>
                                        </th>
                                        <td colspan="2"><b>Free Ongkir</b><br><small>Pilihan Provinsi Gratis Ongkos Kirim.</small></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td></td>
                                        <td colspan="2">
                                            <p><b>Provinsi</b><br><small>Pilih provinsi yang kamu inginkan untuk gratis ongkir.</small></p>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td colspan="2" class="row">
                                            @foreach ($Province as $item)
                                                <div class="col-md-3">
                                                    <label class="checkbox-container">{{ $item->province_name }}
                                                        <input type="checkbox" class="minimal" data-province-id="{{ $item->id_province }}" {{ $item->checked ? 'checked' : '' }} onchange="updateFreeOngkir(this)">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td align="center"><a href="" class="btn btn-info">Simpan</a></td>
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
<script src="{{ asset('/js/function/seller/dashboard.js') }}" type="text/javascript"></script>

@include('seller.asset.footer')

</html>
