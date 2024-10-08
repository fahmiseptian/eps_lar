<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    {{-- <main>
        <!-- Step 1: Email dan Password -->
        <form method="post">
            <input id="email" data-check="0" type="email" name="email" autocomplete="off" required>
            <input id="password" type="password" name="password" required="required">
            <button type="button" onclick="submitStep1()">Lanjut</button>
        </form>
    </main> --}}

    <section class="ftco-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="login-wrap p-4 p-md-5">
                            <img src="{{ asset('/img/app/logo-eps.png') }}" alt="Logo" width="50%" style="margin-left:22%">
                        <h3 class="text-center mb-4">Login</h3>
                        <form method="post">
                            <div class="form-group">
                                <input type="text" class="form-control rounded-left" placeholder="Email"
                                    id="email" data-check="0" type="email" name="email" autocomplete="off"
                                    required>
                            </div>
                            <div class="form-group d-flex">
                                <input type="password" class="form-control rounded-left" placeholder="Password"
                                    id="password" type="password" name="password" required="required">
                            </div>
                            <div class="form-group d-md-flex">
                                <div class="w-50">
                                    &nbsp;
                                </div>
                                <div class="w-50 text-md-right">
                                    <a href="#">Lupa Password</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-primary rounded submit p-3 px-5" onclick="submitStep1()">Masuk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="inputModal" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inputModalLabel">Data Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="login_form_step2_modal">
                        <div id="form_modal" data-email="0" data-password="0"></div>
                        <div class="mb-3">
                            <select class="form-select" id="instansi" name="instansi">
                                <option value="" selected disabled>Pilih Instansi</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select class="form-select" id="satker" name="satker">
                                <option value="" selected disabled>Pilih Satker</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <select class="form-select" id="bidang" name="bidang">
                                <option value="" selected>Pilih Bidang</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" id="next-step" class="waves-effect waves-blue btn blue"
                        onclick="submitStep2()">Login</a>
                </div>
            </div>
        </div>
    </div>

    @include('member.asset.footer')

    <script>
        function submitStep1() {
            var csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content");
            var email = $('#email').val();
            var password = $('#password').val();
            $.ajax({
                type: 'POST',
                url: appUrl + "/login",
                data: {
                    email: email,
                    password: password,
                    _token: csrfToken
                },
                dataType: 'json',
                success: function(response) {
                    // Tampilkan respons dari server sesuai kebutuhan Anda
                    if (response.status === true) {
                        $('#inputModal').modal('show');
                        get_instansi(response.instansi);
                        $('#form_modal').attr('data-email', email);
                        $('#form_modal').attr('data-password', password);
                    } else {
                        var toastHTML = '<span>' + response.message + '</span>';
                        M.toast({
                            html: toastHTML
                        });
                    }
                },
                error: function(err) {
                    // Tangani kesalahan Ajax jika terjadi
                    console.log(err);
                }
            });
        }

        function submitStep2() {
            var form = $('#login_form_step2_modal')[0];
            var formData = new FormData(form);
            var email = $('#form_modal').attr("data-email");
            var password = $('#form_modal').attr("data-password");
            formData.append("email", email);
            formData.append("password", password);
            formData.append("login_type", "login");

            // Menambahkan token CSRF
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            formData.append("_token", csrfToken);

            $.ajax({
                url: appUrl + "/login/submitStep2",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "JSON",
                beforeSend: function() {
                    $('.subm').hide();
                    $('.load-animation').show();
                },
                success: function(data) {
                    console.log(data);

                    $('.subm').show();
                    $('.load-animation').hide();

                    if (data.status == 'success') {
                        window.location.replace(data.redirectUrl);
                    } else if (data.status == 'success-forgot-password') {
                        $('#instruction').html('Email reset password sedang dikirim ke <a>' + data.email +
                            '</a>, silakan periksa di kotak masuk apabila tidak ada coba cek folder spam.');
                        $('#field-email').hide();
                        $('.subm').hide();
                    } else {
                        var toastHTML = '<span>' + data.status + '</span>';
                        M.toast({
                            html: toastHTML
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    $('.subm').show();
                    $('.load-animation').hide();

                    // Tampilkan pesan kesalahan dari server-side
                    var errorMessage = "Verifikasi tidak berhasil. Mohon isi inputan.";

                    if (xhr.responseText) {
                        try {
                            var response = JSON.parse(xhr.responseText);
                            if (response.message) {
                                errorMessage = response.message;
                            }
                        } catch (e) {
                            console.log("Error parsing JSON response: " + e);
                        }
                    }

                    var toastHTML = '<span>' + errorMessage + '</span>';
                    M.toast({
                        html: toastHTML
                    });
                }
            });
        }


        async function get_instansi(instansi) {
            var elem = $('#instansi');
            var txt = '';
            await $.ajax({
                url: appUrl + "/api/login/getinstansi",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    instansi: instansi
                },
                success: function(response) {
                    elem.empty();
                    txt += '<option value="" disabled selected>Pilih Instansi</option>';
                    if (response.success) {
                        $.each(response.data, function(index, val) {
                            txt += '<option value="' + val.id_instansi + '">' + val.nama_instansi +
                                '</option>';
                        });
                    }
                    elem.append(txt);
                }
            });
        }

        async function get_satker(instansi) {
            var elem = $('#satker');
            var txt = '';
            await $.ajax({
                url: appUrl + "/api/login/getsatker",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    instansi: instansi
                },
                success: function(response) {
                    elem.empty();
                    txt += '<option value="" disabled selected>Pilih Satker</option>';
                    if (response.success) {
                        $.each(response.data, function(index, val) {
                            txt += '<option value="' + val.id_satker + '">' + val.nama_satker +
                                '</option>';
                        });
                    }
                    elem.append(txt);
                }
            });
        }

        async function get_bidang(satker) {
            var elem = $('#bidang');
            var txt = '';
            await $.ajax({
                url: appUrl + "/api/login/getbidang",
                type: 'POST',
                dataType: 'JSON',
                data: {
                    satker: satker
                },
                success: function(response) {
                    elem.empty();
                    txt += '<option value="" disabled selected>Pilih Bidang</option>';
                    if (response.success) {
                        $.each(response.data, function(index, val) {
                            txt += '<option value="' + val.id_bidang + '">' + val.nama_bidang +
                                '</option>';
                        });
                    }
                    elem.append(txt);
                }
            });
        }

        $('#instansi').on('change', async function(e) {
            var ini = $(this).val();
            await get_satker(ini);
            $('select').select2().select2('destroy').select2();
        });

        $('#satker').on('change', async function(e) {
            var ini = $(this).val();
            await get_bidang(ini);
            $('select').select2().select2('destroy').select2();
        });
    </script>
    <script src="{{ asset('/js/login_member/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/login_member/popper.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/login_member/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/js/login_member/main.js') }}" type="text/javascript"></script>
</body>

</html>
