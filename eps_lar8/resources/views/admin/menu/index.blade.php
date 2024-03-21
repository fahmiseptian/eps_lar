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

                        <!-- general form elements -->
                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">Quick Example</h3>
                            </div><!-- /.box-header -->
                            <!-- form start -->
                            <form role="form" action="{{ route('admin.menu.store') }}" method="POST">
                                @csrf
                                <div class="box-body">
                                    <div class="form-group">
                                        <label for="nama">Nama</label>
                                        <input type="text" name="nama" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="route">Route</label>
                                        <input type="text" name="route" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="icon">Icon</label>
                                        <input type="text" name="icon" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="urutan">Urutan</label>
                                        <input type="number" name="urutan" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" class="form-control">
                                            <option value="1">Aktif</option>
                                            <option value="0">Nonaktif</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="parent_id">Parent Menu</label>
                                        <input type="number" name="parent_id" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-primary">Simpan</button>
                                </form>
                            </form>
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
