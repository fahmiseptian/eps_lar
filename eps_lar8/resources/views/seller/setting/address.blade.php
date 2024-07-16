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
                        <h3 style="color:black;  margin-bottom:-5px"> <b>Alamat Saya</b></h3>
                        <br>
                        <div class="box box-info">
                            <hr style="width: 50%;" align="center">
                            <button class="add-Address" id="addAddress"> <i id="google-icon" class="material-icons">add</i> Tambah Alamat </button>
                            <div class="dashed-line"></div>
                            @foreach ($address as $adr)
                                <div style="width: 100%; display: flex; align-items: center; justify-content: space-between; padding: 10px;">
                                    <div style="margin-left: 5px">
                                        <p> {{$adr->address_name}} </p>
                                        <div class="DetailToko">
                                            (+62) {{$adr->phone}}<br>
                                            {{$adr->address}} <br>
                                            {{$adr->city_name}} - {{$adr->subdistrict_name}} <br>
                                            {{$adr->province_name}} <br>
                                            {{$adr->postal_code}}
                                        </div>
                                    </div>
                                    <div style="margin-right: 5px">
                                        <div style="display:flex;">
                                            <span class="edit-address" id="editAddress" data-id="{{$adr->member_address_id}}"><i id="google-icon" class="material-icons">edit</i> Ubah</span>
                                            @if ($adr->member_address_id != $adr->id_address_default)
                                                <span class="delete-address" id="DeleteAddress" data-id="{{$adr->member_address_id}}"><i id="google-icon" class="material-icons">delete</i> Hapus</span>
                                            @endif
                                        </div>
                                        @if ($adr->member_address_id == $adr->id_address_default)
                                            <span class="set-default-address" id="setDefaultAddress" data-id="{{$adr->member_address_id}}"><i id="google-icon" class="material-icons">home</i></span>
                                        @else
                                            <span class="set-default-address" style="background-color: grey" id="setDefaultAddress" data-id="{{$adr->member_address_id}}"><i id="google-icon" class="material-icons">home</i></span>
                                        @endif
                                    </div>

                                </div>
                            <hr>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                </div>
            </section>
        </div><!-- /.content-wrapper -->
    </div><!-- ./wrapper -->
    @include('seller.setting.modal')
</body>
{{-- footer --}}
@include('seller.asset.footer')
<!-- page script -->
<script src="{{ asset('/js/function/seller/setting.js') }}" type="text/javascript"></script>

</html>
