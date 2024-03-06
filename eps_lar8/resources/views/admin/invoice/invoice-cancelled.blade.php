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
                            <th>Status </th>
                            <th>Note </th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $i = 1 @endphp
                        @foreach ($cancelledInvoices as $key => $invoice)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $invoice->invoice }}</td>
                            <td>{{ $invoice->total }}</td>
                            <td>
                              <?php
                                 if ($invoice->completeCartShop->status == "cancel_by_seller") {
                                    echo "Dibatalkan oleh Penjual";
                                 }
                                 elseif ($invoice->completeCartShop->status == "cancel_by_marketplace") {
                                    echo "Dibatalkan oleh Admin";
                                 }
                                 elseif ($invoice->completeCartShop->status == "cancel_manual_by_user") {
                                    echo "Dibatalkan oleh User";
                                 }else {
                                    echo $invoice->completeCartShop->status;
                                 }
                                ?>
                            </td>
                            <td>
                              @if ($invoice->completeCartShop->note)
                                  {{ $invoice->completeCartShop->note }}
                              @else
                                  {{ $invoice->completeCartShop->note_seller }}
                              @endif
                            </td>
                            <td>
                              <button onclick="detail('{{ $invoice->id }}')" class="btn btn-info">Detail Pesanan</button>
                            </td>                          
                        </tr>
                    @endforeach
                    </tbody>
                    <div id="datepicker" style="display: none;"></div>
                    <tfoot>
                        <tr>
                          <th>No</th>
                          <th>No Invoice</th>
                          <th>Total</th>
                          <th>Status </th>
                          <th>Note </th>
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
    
    function detail(id) {
                // Menampilkan loading spinner
                Swal.fire({
                    title: 'Memuat...',
                    html: '<div class="spinner-border" role="status"><span class="sr-only">Memuat...</span></div>',
                    showConfirmButton: false,
                    allowOutsideClick: false
                });

                // Mengambil data anggota menggunakan AJAX
                $.ajax({
                    url: '/admin/invoice/' + id,
                    method: 'GET',
                    success: function(response) {
                        var invoice = response.invoice;
                        var member = response.member;
                        var cartshop = response.cartshop;
                        var shop = response.shop;
                        if (invoice) {

                            // Menampilkan informasi anggota dengan SweetAlert
                            Swal.fire({
                                title: 'Detail Pesanan',
                                html: `
                                    <div style="text-align: justify;">
                                        <p><strong>No Invoice:</strong> ${invoice.invoice || ''}</p>
                                        <p><strong>Pembeli:</strong> ${member.nama || ''}</p>
                                        <p><strong>Penjual:</strong> ${shop.name || ''}</p>
                                    </div>
                                `,
                                confirmButtonText: 'Tutup',
                            });
                        } else {
                            // Menampilkan pesan jika data anggota tidak ditemukan
                            Swal.fire({
                                title: 'Detail Pesanan',
                                text: 'Data PEsanan tidak ditemukan.',
                                icon: 'error',
                                confirmButtonText: 'Tutup',
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Menampilkan pesan kesalahan
                        Swal.fire({
                            title: 'Terjadi Kesalahan',
                            text: 'Terjadi kesalahan saat memuat detail anggota.',
                            icon: 'error',
                            confirmButtonText: 'Tutup',
                        });
                    }
                });
            }
  </script>
</html>