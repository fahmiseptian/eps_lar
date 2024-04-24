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
        {{-- section-info --}}
        @include('admin.asset.section-info')

        <!-- Main content -->
        <section class="content">
          <!-- Info boxes -->
          <div class="row">
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-aqua"><i class="fa fa-gear"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">CPU Traffic</span>
                  <span class="info-box-number">90<small>%</small></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-red"><i class="fa fa-archive"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Product</span>
                  <span class="info-box-number"><?= $jmlhproduct;?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->

            <!-- fix for small devices only -->
            <div class="clearfix visible-sm-block"></div>

            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-green"><i class="fa fa-shopping-cart"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Toko</span>
                  <span class="info-box-number"><?= $shop;?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
            </div><!-- /.col -->
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-users"></i></span>
                <div class="info-box-content">
                  <span class="info-box-text">Member</span>
                  <span class="info-box-number"><?= $member;?></span>
                </div><!-- /.info-box-content -->
              </div><!-- /.info-box -->
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
</html>