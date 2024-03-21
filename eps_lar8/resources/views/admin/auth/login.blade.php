<!DOCTYPE html>
<html>
@include('admin.asset.header')

<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('admin.login') }}"><b>EPS </b>Admin</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            @if ($errors->any())
                <div style="color: red;">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Username" id="username" name="username"
                        value="{{ old('username') }}" />
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" id="password" name="password" />
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div><!-- /.col -->
                </div>
            </form>
        </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->
    {{-- footer --}}
    @include('admin.asset.footer')

    <!-- page script -->
    <script src="{{ asset('/js/function/admin/login.js') }}" type="text/javascript"></script>

</body>

</html>
