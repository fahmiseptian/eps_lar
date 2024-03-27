<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <style>
        body {
            text-align: center;
            font-size: 24px;
        }
        .container {
            margin-top: 100px;
        }
        .button-container {
            margin-top: 20px;
        }
        button {
            font-size: 20px;
            padding: 10px 20px;
            margin: 0 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our Application</h1>
        <h2>This site is currently under development.</h2>
        <div class="button-container">
            <a href="{{ url('/admin') }}"><button>Admin</button></a>
            <a href="{{ url('/seller') }}"><button>Seller</button></a>
        </div>
    </div>
</body>
</html>