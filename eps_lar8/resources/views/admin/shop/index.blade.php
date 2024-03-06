<!DOCTYPE html>
<html>
@include('admin.asset.header')

<body class="skin-blue">
    <div class="wrapper">

        {{-- Navbar --}}
        @include('admin.asset.navbar')


        {{-- sidebar --}}
        @include('admin.asset.sidebar')

        <!-- Right side column. Contains the navbar and content of the page -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->

            {{-- section-info --}}
            @include('admin.asset.section-info')

            <!-- Main content -->
            <section class="content">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="box">
                            <div class="box-header">
                                <h3 class="box-title">List Shop</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($datashop as $item)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $item->name }}</td>
                                                <td>
                                                    <table>
                                                        <tr>
                                                            <td rowspan="2" style="width: 90%">
                                                                <button onclick="updateType('{{ $item->id }}')"
                                                                    class="btn 
                          @if ($item->type === 'silver') btn-secondary 
                          @elseif($item->type === 'gold') 
                            btn-warning 
                          @elseif($item->type === 'platinum') 
                            btn-info 
                          @elseif($item->type === 'trusted_seller') 
                            btn-success @endif">
                                                                    {{ ucfirst($item->type) }}
                                                                </button>
                                                            </td>
                                                            <td>
                                                                <button onclick="updateTypeUp('{{ $item->id }}')"
                                                                    class="glyphicon glyphicon-upload {{ $item->type === 'trusted_seller' ? 'btn-secondary disabled' : 'btn-success' }}"
                                                                    {{ $item->type === 'trusted_seller' ? 'disabled' : '' }}>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                <button onclick="updateTypeDown('{{ $item->id }}')"
                                                                    class="glyphicon glyphicon-download {{ $item->type === 'silver' ? 'btn-secondary disabled' : 'btn-warning' }}"
                                                                    {{ $item->type === 'silver' ? 'disabled' : '' }}>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td>
                                                    <button onclick="detail('{{ $item->id }}')"
                                                        class="btn btn-info">Detail Toko</button>
                                                    <button onclick="updateStatus('{{ $item->id }}')"
                                                        class="btn {{ $item->status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                                        {{ $item->status === 'active' ? 'Aktif' : 'Tidak aktif' }}
                                                    </button>
                                                    <button onclick="deleteShop('{{ $item->id }}')"
                                                        class="btn btn-danger">Hapus</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div><!-- /.box-body -->
                        </div><!-- /.box -->
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </section><!-- /.content -->
        </div><!-- /.content-wrapper -->
        {{-- section-footer --}}
        @include('admin.asset.section-footer')
    </div><!-- ./wrapper -->
</body>
{{-- footer --}}
@include('admin.asset.footer')

<!-- page script -->
<script src="{{ asset('/js/function/admin/shop.js') }}" type="text/javascript"></script>

</html>
