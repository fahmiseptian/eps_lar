<!DOCTYPE html>
<html>
@include('admin.asset.header')

<body class="login-page">
    @if(session('error_admin'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error_admin') }}",
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Hapus session error_admin
                    <?php session()->forget('error_admin'); ?>
                }
            });
        </script>
    @endif
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('admin.login') }}"><b>EPS </b>Admin</a>
        </div><!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <!-- @if (session('error_admin'))
                <div style="color: red;">
                    <p>{{ session('error_admin') }}</p>
                </div>
                // Hapus session error_admin
                <?php //session()->forget('error_admin'); ?>
            @endif -->
            <form action="{{ route('admin.login') }}" method="POST">
                @csrf
                <div class="form-group has-feedback">
                    <input type="text" class="form-control" placeholder="Username" id="username" name="username"
                        value="{{ old('username') }}" required />
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Password" id="password" name="password" required />
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
