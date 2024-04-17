function showlogin() {
    var loginForm = document.querySelector(".login");
    var signupForm = document.querySelector(".daftar");
    var loginLink = document.querySelector(".box-title.active"); // Ubah pemilihan dari ID menjadi kelas
    var signupLink = document.querySelector(".box-title:not(.active)"); // Ubah pemilihan dari ID menjadi kelas

    loginForm.style.display = "block";
    signupForm.style.display = "none";
    loginLink.classList.remove("active");
    signupLink.classList.add("active");
}

function showSignup() {
    var loginForm = document.querySelector(".login");
    var signupForm = document.querySelector(".daftar");
    var loginLink = document.querySelector(".box-title.active"); // Ubah pemilihan dari ID menjadi kelas
    var signupLink = document.querySelector(".box-title:not(.active)"); // Ubah pemilihan dari ID menjadi kelas

    loginForm.style.display = "none";
    signupForm.style.display = "block";
    loginLink.classList.remove("active");
    signupLink.classList.add("active");
}



$(document).ready(function(){
    $('#loginForm').submit(function(e){
        e.preventDefault(); // Prevent form submission

        // Validate hCaptcha
        // var response = hcaptcha.getResponse();
        // if (!response) {
        //     Swal.fire({
        //         title: "Error",
        //         text: "Please complete the hCaptcha.",
        //         icon: "error",
        //         showConfirmButton: false,
        //         timer: 1500,
        //     });
        //     return;
        // }

        var loginUrl = $(this).attr('action');
        var formData = $(this).serialize();

        $.ajax({
            type: "POST",
            url: loginUrl,
            data: formData,
            success: function(response){
                window.location.href = "/seller";
            },
            error: function(xhr, status, error){
                var err = JSON.parse(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: err.error
                });
            }
        });
    });
});

