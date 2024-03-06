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
                                <h3 class="box-title">List Invoice</h3>
                            </div><!-- /.box-header -->
                            <div class="box-body">
                                <table id="example2" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Email</th>
                                            <th>Nama</th>
                                            <th>No Telepon</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $i = 1 @endphp
                                        @foreach ($members as $member)
                                            <tr>
                                                <td>{{ $i++ }}</td>
                                                <td>{{ $member->email }}</td>
                                                <td>{{ $member->nama }}</td>
                                                <td>{{ $member->no_hp }}</td>
                                                <td>
                                                    <button onclick="showDetail('{{ $member->id }}')"
                                                        class="btn btn-info">Detail</button>
                                                    <button onclick="toggleStatus('{{ $member->id }}')"
                                                        class="toggle-status btn {{ $member->member_status === 'active' ? 'btn-success' : 'btn-secondary' }}">
                                                        {{ $member->member_status === 'active' ? 'Aktif' : 'Suspend' }}
                                                    </button>
                                                    <button onclick="deleteMember('{{ $member->id }}')"
                                                        class="btn btn-danger">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>No</th>
                                            <th>Email</th>
                                            <th>Nama</th>
                                            <th>No Telepon</th>
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
<script src="{{ asset('/js/function/admin/members.js') }}" type="text/javascript"></script>

</html>
