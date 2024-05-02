<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel" style="cursor: pointer;" onclick="editProfile(<?= session()->get('user_id') ?>)">
            <div class="pull-left image">
                <img src="{{ asset('/img/user2-160x160.jpg') }}" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <h4><?= session()->get('name') ?></h4>
                <p><?= session()->get('access_name') ?></p>
                <a><i class="fa fa-circle text-success"></i> Online</a>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li class="header">MAIN NAVIGATION</li>
            @foreach ($menus as $menu)
                <li>
                    @if ($menu->children->isNotEmpty())
                        <a href="#">
                        @else
                            @if ($menu->route)
                                <a href="{{ route($menu->route) }}">
                                @else
                                    <a href="#">
                            @endif
                    @endif
                    <i class="fa {{ $menu->icon }}"></i>{{ $menu->nama }}
                    @if ($menu->children->isNotEmpty())
                        <i class="fa fa-angle-left pull-right"></i>
                    @endif
                    </a>
                    @if ($menu->children->isNotEmpty())
                        <ul class="treeview-menu">
                            @foreach ($menu->children as $childMenu)
                                <li class="{{ Request::is($childMenu->route) ? 'active' : '' }}">
                                    @if ($childMenu->route)
                                        <a href="{{ route($childMenu->route) }}"><i class="fa {{ $childMenu->icon }}"></i>
                                            {{ $childMenu->nama }}</a>
                                    @else
                                        <a href="#"><i class="fa fa {{ $childMenu->icon }}"></i> {{ $childMenu->nama }}</a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ul>
        <!-- sidebar menu: : style can be found in sidebar.less -->
        {{-- <ul class="sidebar-menu">
        <li class="header">MAIN NAVIGATION</li>
        <li class="active treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Dashboard</span> <i class="fa fa-angle-left pull-right"></i>
          </a>
          <ul class="treeview-menu">
            <li><a href="index.html"><i class="fa fa-circle-o"></i> Dashboard v1</a></li>
            <li class="active"><a href="index2.html"><i class="fa fa-circle-o"></i> Dashboard v2</a></li>
          </ul>
        </li>
        <li><a href="#"><i class="fa fa-circle-o text-danger"></i> Test LINK</a></li>
        <li class="treeview">
            <a href="#">
              <i class="fa fa-files-o"></i>
              <span>Layout Options</span>
              <span class="label label-primary pull-right">4</span>
            </a>
            <ul class="treeview-menu">
              <li><a href="../layout/top-nav.html"><i class="fa fa-circle-o"></i> Top Navigation</a></li>
              <li><a href="../layout/boxed.html"><i class="fa fa-circle-o"></i> Boxed</a></li>
              <li><a href="../layout/fixed.html"><i class="fa fa-circle-o"></i> Fixed</a></li>
              <li><a href="../layout/collapsed-sidebar.html"><i class="fa fa-circle-o"></i> Collapsed Sidebar</a></li>
            </ul>
          </li>
        <li class="header">LABELS</li>
        <li><a href="#"><i class="fa fa-circle-o text-danger"></i> Important</a></li>
        <li><a href="#"><i class="fa fa-circle-o text-warning"></i> Warning</a></li>
        <li><a href="#"><i class="fa fa-circle-o text-info"></i> Information</a></li>
      </ul> --}}
    </section>
    <!-- /.sidebar -->
</aside>
