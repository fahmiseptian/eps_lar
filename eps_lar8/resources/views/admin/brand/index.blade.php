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
                                <h3 class="box-title">Responsive Hover Table</h3>
                              </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <th>No</th>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Tanggal Buat</th>
                                        <th>Action</th>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($databrand as $item)
                                        <tr>
                                            <td>{{ $i++ }}</td>
                                            <td>{{ $item->code }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td><span class="label label-success">{{ $item->status }}</span></td>
                                            <td>{{ \Carbon\Carbon::parse($item->created_dt)->format('d-m-Y') }}</td>
                                            <td><a onclick="showDetail('{{ $item->id }}')" style="font-size: large" class="fa fa-edit"></a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Code</th>
                                            <th>Name</th>
                                            <th>Status</th>
                                            <th>Tanggal Buat</th>
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
<script src="{{ asset('/js/function/admin/brand.js') }}" type="text/javascript"></script>

</html>
