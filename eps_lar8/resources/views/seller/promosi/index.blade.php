<!DOCTYPE html>
<html>
@include('seller.asset.header')

<body class="skin-blue">
    <div class="wrapper">

        @include('seller.asset.topbar')
        @include('seller.asset.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            <section class="content">
                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <h3 style="margin-left: 15px; margin-bottom:-5px"> <b>Promosi Produk</b></h3>
                        <hr>
                        <div class="box">
                            <ul class="horizontal-list">
                                <li id="category-promotion" data-id="0" class="active"><img
                                        src="https://eliteproxy.co.id/assets/images/icon/promo/icon-1649307434-promo.png"
                                        alt="all-promo" width="30px" height="30px">Semua Promo</li>
                                @foreach ($promotions as $promotion)
                                    <li id="category-promotion" data-id="{{$promotion->id}}"><img src="https://eliteproxy.co.id/{{ $promotion->icon }}"
                                            alt="all-promo" width="30px" height="30px">
                                        {{ $promotion->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="box"
                            style="background-color: #e3f2fd; border: 2px solid #FC6703; border-radius: 10px; box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.15); padding: 16px; margin-bottom: 20px;">
                            <div class="box-body">
                                <div>
                                    <span class="btn-sm btn-primary" id="modal_TambahPromosi"><i id="google-icon" class="material-icons">add</i>Produk</span>
                                </div>
                                <br>
                                <div class="box">
                                    <div class="table-responsive">
                                        <table id="example2" class="table table-bordered table-hover table-striped"
                                            style="width: 100%">
                                            <thead style="background-color: #fff;">
                                                <tr>
                                                    <th class="detail-full">No</th>
                                                    <th>Nama Produk</th>
                                                    <th>Harga Promo</th>
                                                    <th>Harga Normal</th>
                                                    <th class="detail-full">Harga Promo Tayang</th>
                                                    <th class="detail-full">Tanggal Buat</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $no = 1;
                                                @endphp
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td class="detail-full">{{$no++}}</td>
                                                    <td>{{ $product->name }}</td>
                                                    <td>
                                                        Rp.{{ str_replace(',', '.', number_format($product->promo_origin )) }}
                                                    </td>
                                                    <td>
                                                        Rp.{{ str_replace(',', '.', number_format($product->promo_price )) }}
                                                    </td>
                                                    <td class="detail-full">
                                                        Rp.{{ str_replace(',', '.', number_format($product->price)) }}
                                                    </td>
                                                    <td class="detail-full">{{$product->created_dt}}</td>
                                                    <td style="display: flex;">
                                                        <a href="#" class="delete-promo-product" data-id="{{$product->id}}"><i class="material-icons">delete</i></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th class="detail-full">No</th>
                                                    <th>Nama Produk</th>
                                                    <th>Harga Normal</th>
                                                    <th>Harga Promo</th>
                                                    <th class="detail-full">Harga Promo Tayang</th>
                                                    <th class="detail-full">Tanggal Buat</th>
                                                    <th>Delete</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    @include('seller.promosi.modal_promosi')
</body>
{{-- footer --}}
@include('seller.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/seller/promo.js') }}" type="text/javascript"></script>

</html>
