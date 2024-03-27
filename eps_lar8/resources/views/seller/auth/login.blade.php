<!DOCTYPE html>
<html>
@include('seller.asset.header')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<body class="login-page">
    <section class="content">
        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <!-- general form elements disabled -->
                <div class="box box-warning">
                    <div class="logo-login">
                        <img src="{{ asset('/img/logo-eps.png') }}" class="logologin" />
                    </div>
                    <div class="box-header">
                        <ul class="horizontal-list">
                            <li><a href="#" class="box-title active" onclick="showlogin()">Masuk</a></li>
                            <li><span class="separator">|</span></li>
                            <li><a href="#" class="box-title" onclick="showSignup()">Daftar</a></li>
                        </ul>
                    </div>
                    <div class="box-body">
                        {{-- Login --}}
                        <form id="loginForm" class="login" action="{{ route('seller.login') }}" method="POST" data-login-url="{{ route('seller.login') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" placeholder="Masukan Email anda" id="email" name="email" required/>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" placeholder="Masukan Password anda" id="password" name="password" required/>
                            </div>
                            <div class="form-group">
                                <div class="h-captcha" data-sitekey="09aec88c-7267-4df4-b181-219021898cd1"></div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Masuk</button>
                                </div>
                            </div>
                            <br>
                            <a href="#"> Lupa Password? </a>
                        </form>

                        {{-- Register --}}
                        <form class="daftar" style="display: none;">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" placeholder="Masukan Email anda" />
                            </div>
                            <div class="form-group">
                                <label>Pasword</label>
                                <input type="password" class="form-control" placeholder="Masukan Password anda" />
                            </div>
                            <div class="form-group">
                                <label>Pasword</label>
                                <input type="password" class="form-control" placeholder="Masukan Password anda" />
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="submit" class="btn btn-primary btn-block btn-flat">Daftar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('seller.asset.section-footer-login')
    {{-- footer --}}

    <script src="{{ asset('/js/function/seller/login.js') }}" type="text/javascript"></script>

</body>
@include('seller.asset.footer')

</html>
