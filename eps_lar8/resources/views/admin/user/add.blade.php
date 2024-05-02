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
                                <!-- Daftar List Access -->
                                <div class="col-md-3">
                                    <a href="javascript:;" id="accessList" class="pull-right">
                                        <h2 class="box-title" id="accessList">List Access</h2>
                                        <i class="fa fa-angle-left"></i>
                                    </a>
                                </div>
                            </div><!-- /.box-header -->
                               
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-9">
                                    </div>
                                    <div class="col-md-3" id="accesses" style="display:none;">
                                        <button onclick="addAccess()" class="btn btn-primary pull-right" style="margin-bottom: 10px;">
                                            <i class="fa fa-plus-circle"></i>
                                            Add Access
                                        </button>
                                        <br>
                                        <table class="table table-bordered table-hover">
                                            <!-- Tabel daftar list access akan ditampilkan di sini -->
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
                                </div>
                                
                                <form role="form" action="{{ route('admin.user.store') }}" method="POST">
                                @csrf
                                
                                    <div class="form-group">
                                        <label for="nama">Username</label>
                                        <input type="text" name="username" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="firstname">First Name</label>
                                        <input type="text" name="firstname" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" name="lastname" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="access_id">Access</label>
                                        <select name="access_id" class="form-control" required>
                                            @foreach ($accesses as $access)
                                            <option value={{ $access->id }}>{{ $access->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="active">Status</label>
                                        <select name="active" class="form-control" required>
                                            <option value="1">Aktif</option>
                                            <option value="0">Tidak Aktif</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">No Telepon</label>
                                        <input type="number" name="phone" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Alamat</label>
                                        <input type="text" name="address" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="text" name="password" class="form-control" required>
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
<script src="{{ asset('/js/function/admin/user.js') }}" type="text/javascript"></script>

</html>
