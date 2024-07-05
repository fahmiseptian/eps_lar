<!DOCTYPE html>
<html lang="id">

@include('member.asset.header')

<body>

    @include('member.asset.navbar')

    <!-- Main Content -->
    <main>
        <!-- Data Section -->
        <section>
            <div class="container-fluid">
                <div class="row">
                    <!-- Side Navbar -->
                    <nav class="col-md-2 d-md-block bg-light sidebar">
                        <div>
                            <ul class="nav flex-column">
                                <li class="nav-item">Profile</li>
                                <li class="nav-item">Profile</li>
                                <li class="nav-item">Profile</li>
                                <li class="nav-item">Profile</li>
                                <li class="nav-item">Profile</li>
                                <li class="nav-item">Profile</li>
                            </ul>
                        </div>
                    </nav>

                    <!-- Main content -->
                    <main role="main" class="col-md-10 ml-md-auto main-content" style="background-color: white">
                        <h1>Main Content</h1>
                    </main>
                </div>
            </div>
        </section>
    </main>

    @include('member.asset.footer')
    <script src="{{ asset('/js/function/member/home.js') }}" type="text/javascript"></script>
</body>

</html>
