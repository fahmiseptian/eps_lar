<header class="main-header" style="background-color: #039be5">
  <!-- Logo -->
  <a href="{{ route('seller') }}" class="logo" style="background-color: #eee"><img style="width: 120px" src="{{ asset('/img/logo-eps.png') }}"/></a>
  <nav class="navbar navbar-static-top" role="navigation"  style="background-color: #eee; box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.5);">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button" style="color:#039be5 ">
        <span class="sr-only">Toggle navigation</span>
    </a>
    <div>
      <div class="notif">
        <a href="#" class="fa fa-bell-o" role="button" style="color:#039be5 "></a>
        <a href="#" class="fa fa-bullhorn" role="button" style="color:#039be5 "></a>
        <a href="#" onclick="confirmLogout(this)" role="button" style="color:#039be5 ">
          <img style="width: 20px" src="{{ asset('/img/logo eps.png') }}" data-logout-url="{{ route('seller.logout') }}" />
      </a>
    </div>

</nav>
  <div style="height: 2px; background-color: #039be5">&nbsp;</div>
  <!-- Header Navbar: style can be found in header.less -->
  
</header>
