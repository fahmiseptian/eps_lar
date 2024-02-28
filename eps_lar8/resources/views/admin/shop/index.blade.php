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
                @foreach ($data as $item)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->type }}</td>
                    <td> <button></td>
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
            <script type="text/javascript">
              $(function () {
                $("#example1").dataTable();
                $('#example2').dataTable({
                  "bPaginate": true,
                  "bLengthChange": false,
                  "bFilter": false,
                  "bSort": true,
                  "bInfo": true,
                  "bAutoWidth": false
                });
              });
            </script>
</html>