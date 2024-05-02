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
                                <div class="col-md-10" style="padding-left: 0;">
                                    <h3 class="box-title">List Bank</h3>
                                </div>
                                <!-- Daftar Parent Menu -->
                                <div class="col-md-2" style="padding-right: 0;">
                                    <a href="javascript:;" id="parentMenuList" class="pull-right">
                                        <h2 class="box-title" id="parentMenuList">List Parent ID</h2>
                                        <i class="fa fa-angle-left"></i>
                                    </a>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="col-md-10">
                                </div>
                                <div class="col-md-2">
                                    <table id="parentmenu" class="table table-bordered table-hover" style="display:none;">
                                        <!-- Tabel daftar parent menu akan ditampilkan di sini -->
                                        <tr>
                                            <th widht=90%>Parent Name</th>
                                            <th width=10%>ID</th>
                                        </tr>
                                        @foreach ($listparent as $parentmenu)
                                            <tr>
                                                <td>{{ $parentmenu->nama }}</td>
                                                <td>{{ $parentmenu->id }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <!-- <th>Id</th> -->
                                            <th>Nama</th>
                                            <th>Route</th>
                                            <th>Icon</th>
                                            <th>Urutan</th>
                                            <th>Status</th>
                                            <th>Parent ID</th>
                                            <th>Akses</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($listmenu as $menu)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <!-- <td>{{ $menu->id }}</td> -->
                                                <td>{{ $menu->nama }}</td>
                                                <td>{{ $menu->route }}</td>
                                                <td>{{ $menu->icon }}</td>
                                                <td>{{ $menu->urutan }}</td>
                                                <td>
                                                    @if($menu->status == 1)
                                                        Parent
                                                    @elseif($menu->status == 2)
                                                        Children
                                                    @else
                                                        Nonaktif
                                                    @endif
                                                </td>
                                                <td>{{ $menu->parent_id }}</td>
                                                <td>
                                                    <input type="checkbox" name="access[]" value="developer" {{ $menu->developer == 1 ? 'checked' : '' }} disabled> Developer<br>
                                                    <input type="checkbox" name="access[]" value="superadmin" {{ $menu->superadmin == 1 ? 'checked' : '' }} disabled> Superadmin<br>
                                                    <input type="checkbox" name="access[]" value="webadmin" {{ $menu->web_admin == 1 ? 'checked' : '' }} disabled> Web Admin<br>
                                                    <input type="checkbox" name="access[]" value="finance" {{ $menu->finance == 1 ? 'checked' : '' }} disabled> Finance<br>
                                                    <input type="checkbox" name="access[]" value="pajak" {{ $menu->pajak == 1 ? 'checked' : '' }} disabled> Pajak<br>
                                                    <input type="checkbox" name="access[]" value="admin" {{ $menu->administration == 1 ? 'checked' : '' }} disabled> Administration<br>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-transparent" onclick="editMenu('{{ $menu->id }}')" title="Edit Menu">
                                                        <span class="material-symbols-outlined" id="icon-warning">
                                                            edit_square
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="deleteMenu('{{ $menu->id }}')" title="Hapus Menu">
                                                        <span class="material-symbols-outlined" id="icon-delete">
                                                            delete
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <div id="datepicker" style="display: none;"></div>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <!-- <th>Id</th> -->
                                            <th>Nama</th>
                                            <th>Route</th>
                                            <th>Icon</th>
                                            <th>Urutan</th>
                                            <th>Status</th>
                                            <th>Parent ID</th>
                                            <th>Akses</th>
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
<script src="{{ asset('/js/function/admin/menu.js') }}" type="text/javascript"></script>

</html>