<aside class="main-sidebar" >
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->

        <div class="user-panel">
            {{-- <div class="pull-left image">
                <img src="" class="img-circle" alt="User Image" />
            </div> --}}
            <div class="pull-left info">
                <a href="#" class="seller_type">
                    @if ($seller_type == 'trusted_seller')
                        <img src="{{ asset('/img/app/trusted-seller.png') }}" />
                    @endif
                </a>
                <h5> <b><?= session()->get('seller') ?></b></h5>

            </div>
            <hr width="95%" align="center">
        </div>
        <div class="seller-balance">
            <div class="info">
                <p>Saldo Anda</p>
                <h4>Rp {{number_format($saldo, 0, ',', '.')}}</h4>
                <hr width="100%" align="center">
            </div>

        </div>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li class="list-sidebar">
                <a href="{{ route('seller') }}">
                    <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-truck"></i> <span>Pengiriman</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="{{ route('seller.delivery') }}"><i class="fa fa-circle-o"></i> Jasa Pengiriman</a>
                    </li>
                    <li><a style="color: #eee" href="{{ route('seller.delivery.free-ongkir') }}"><i class="fa fa-circle-o"></i> Free Ongkir</a></li>
                </ul>
            </li>
            <li class="list-sidebar">
                <a href="{{ route('seller.order') }}">
                    <i class="fa fa-tasks"></i> <span>Pesanan Saya</span>
                </a>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-dropbox"></i> <span>Produk Saya</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="{{ route('seller.product') }}"><i class="fa fa-circle-o"></i> Produk</a></li>
                    <li><a style="color: #eee" href="{{ route('seller.product.add') }}"><i class="fa fa-circle-o"></i> Tambah Produk</a></li>
                    <li><a style="color: #eee" href="{{ route('seller.product.violation') }}"><i class="fa fa-circle-o"></i> Pelanggaran Saya</a></li>
                </ul>
            </li>
            <li class="list-sidebar">
                <a href="{{route('seller.promosi')}}">
                    <i class="fa fa-tags"></i> <span>Promosi</span>
                </a>
            </li>
            <li class="list-sidebar">
                <a href="{{route('seller.nego')}}">
                    <i class="fa fa-comments"></i> <span>Nego Bela Pengadaan</span>
                </a>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-money"></i> <span>Keuangan</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="{{ route('seller.finance')}}"><i class="fa fa-circle-o"></i> Penghasilan</a></li>
                    <li><a style="color: #eee" href="{{ route('seller.finance.saldo')}}"><i class="fa fa-circle-o"></i> Saldo</a></li>
                    <li><a style="color: #eee" href="{{ route('seller.finance.rekening')}}"><i class="fa fa-circle-o"></i> Rekening</a></li>
                    <li><a style="color: #eee" href="{{ route('seller.finance.pembayaran')}}"><i class="fa fa-circle-o"></i> Pembayaran</a></li>
                </ul>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-share"></i> <span>Layanan Pembeli</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Asistent Chat</a></li>
                </ul>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-pie-chart"></i> <span>Data</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Kesehatan Toko</a></li>
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Bisnis</a></li>
                    </li>
                </ul>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-book"></i> <span>Toko</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Penilaian</a></li>
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Profil</a></li>
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Etalase</a>
                    </li>
                </ul>
            </li>
            <li class="list-sidebar">
                <a href="#">
                    <i class="fa fa-cog"></i> <span>Pengaturan</span> <i class="fa fa-angle-left pull-right"></i>
                </a>
                <ul class="treeview-menu">
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Alamat</a></li>
                    <li><a style="color: #eee" href="index.html"><i class="fa fa-circle-o"></i> Pengaturan Toko</a></li>
                    </li>
                </ul>
            </li>
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>
