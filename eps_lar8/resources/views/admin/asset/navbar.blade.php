<header class="main-header">
  <!-- Logo -->
  <a href="#" class="logo"><b>Admin </b>EPS</a>
  <!-- Header Navbar: style can be found in header.less -->
  <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
      </a>

      <!-- Tombol Logout -->
      <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">
              <li class="dropdown user user-menu">
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                      <span class="hidden-xs"><?= session()->get('username') ?></span>
                  </a>
                  <ul class="dropdown-menu">
                      <!-- Menu Body -->
                      <li class="user-body">
                          <div class="pull-right">
                              <a href="{{ route('admin.logout') }}" class="btn btn-default btn-flat">Logout</a>
                          </div>
                      </li>
                  </ul>
              </li>
          </ul>
      </div>
  </nav>
</header>
