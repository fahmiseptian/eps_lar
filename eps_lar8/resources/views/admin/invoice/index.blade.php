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
                            <th>No Invoice</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pemeriksa</th>
                            <th>Status Pajak</th>
                            <th>Pelapor</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1 @endphp
                        @foreach ($data as $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $item->invoice }}</td>
                            <td>Rp. {{ number_format($item->total, 0, ',', '.') }}</td>
        
                            <td>
                                
                                <?php
                                 if ($item->status == "complete_payment") {
                                    echo "Selesai";
                                 }
                                 elseif ($item->status == "cencel") {
                                    echo "Batal";
                                 }
                                 elseif ($item->status == "pending") {
                                    echo "Belum Dibayar";
                                 }
                                 elseif ($item->status == "on_delivery") {
                                    echo "Dalam Pengiriman";
                                 }
                                 elseif ($item->status == "waiting_approve_by_ppk") {
                                    echo "Menunggu Persetujuan";
                                 }
                                 elseif ($item->status == "expired") {
                                    echo "Expired";
                                 }
                                 elseif ($item->status == "cancel_part") {
                                    echo "Batals";
                                 }else {
                                    echo $item->status;
                                 }
                                ?>
                            </td>
                            <td>
                                @if ($item->user)
                                {{ $item->user->username }}
                                @else
                                    {{-- Ini Kalau data kosong --}}
                                @endif
                            </td>
                            <td>
                            <?php
        
                                if ($item->status_pelaporan_pajak == '2') {
                                    echo "Dilaporkan semua";
                                }
            
                                if ($item->status_pelaporan_pajak == '1') {
                                    echo "Dilaporkan sebagian";
                                }
            
                                if ($item->status_pelaporan_pajak == '0') {
                                    echo "Belum Dilaporkan";
                                } 
                            ?>
                            </td>
                            <td>{{ $item->pelapor_pajak }}</td>
                            <td><button></button></td>
                            
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>No Invoice</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pemeriksa</th>
                            <th>Status Pajak</th>
                            <th>Pelapor</th>
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