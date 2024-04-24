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
                                <div class="col-md-9" style="padding-left: 0;">
                                    <h3 class="box-title">List User</h3>
                                </div>
                                <!-- Daftar Parent Menu -->
                                <div class="col-md-3">
                                    <a href="javascript:;" id="accessList" class="pull-right">
                                        <h2 class="box-title" id="accessList">List Access</h2>
                                        <i class="fa fa-angle-left"></i>
                                    </a>
                                </div>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <div class="col-md-9">
                                </div>
                                <div class="col-md-3" id="accesses" style="display:none;">
                                    <button onclick="addAccess()" class="btn btn-primary pull-right" style="margin-bottom: 10px;">
                                        <i class="fa fa-plus-circle"></i>
                                        Add Access
                                    </button>
                                    <br>
                                    <table class="table table-bordered table-hover">
                                        <!-- Tabel daftar parent menu akan ditampilkan di sini -->
                                        <tr>
                                            <th widht=20%>No</th>
                                            <th widht=40%>Access</th>
                                            <th width=40%>Action</th>
                                        </tr>
                                        @php $x = 1 @endphp
                                        @foreach ($accesses as $access)
                                            <tr>
                                                <td>{{ $x++ }}</td>
                                                <td>{{ $access->name }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-transparent" onclick="editAccess('{{ $access->id }}', '{{ $access->name }}', '{{ $access->code }}')" title="Edit Access">
                                                        <span class="material-symbols-outlined" id="icon-warning">
                                                            edit_square
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="deleteAccess('{{ $access->id }}', '{{ $access->name }}')" title="Hapus Access">
                                                        <span class="material-symbols-outlined" id="icon-delete">
                                                            delete
                                                        </span>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </div>
                                    
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <!-- <th>Id</th> -->
                                            <th>Username</th>
                                            <th>Access</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <!-- <td>{{ $user->id }}</td> -->
                                                <td>{{ $user->username }}</td>
                                                <td>{{ $user->access->name }}</td>
                                                <td>
                                                    @if($user->active == 1)
                                                        Aktif
                                                    @else
                                                        Tidak Aktif
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-transparent" onclick="detailUser('{{ $user->id }}')" title="Info Detail">
                                                        <span class="material-symbols-outlined" id="icon-info">
                                                            info
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="editUser('{{ $user->id }}')" title="Edit User">
                                                        <span class="material-symbols-outlined" id="icon-warning">
                                                            edit_square
                                                        </span>
                                                    </button>
                                                    <button type="button" class="btn btn-transparent" onclick="deleteUser('{{ $user->id }}')" title="Hapus User">
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
                                            <th>Username</th>
                                            <th>Access</th>
                                            <th>Status</th>
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
<script src="{{ asset('/js/function/admin/user.js') }}" type="text/javascript"></script>

</html>
