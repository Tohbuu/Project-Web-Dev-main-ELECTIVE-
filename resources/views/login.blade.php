<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain_Chef</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="container">
        {{-- Display validation errors --}}
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        @endif

        <div class="form-box login">
            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <h1>Login</h1>
                <div class="inputbox">
                    <input name="username" type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="inputbox">
                    <input name="password" type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot password</a>
                </div>
                <button type="submit" class="button">Login</button>
                <p>or login with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-github'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
            </form>
        </div>

        <div class="form-box registration">
            <form action="{{ url('/register') }}" method="POST">
                @csrf
                <h1>Registration</h1>
                <div class="inputbox">
                    <input name="username" type="text" placeholder="Username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="inputbox">
                    <input name="email" type="email" placeholder="Email" required>
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="inputbox">
                    <input name="password" type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="button">Register</button>
                <p>or register with social platforms</p>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-google'></i></a>
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                    <a href="#"><i class='bx bxl-github'></i></a>
                    <a href="#"><i class='bx bxl-linkedin'></i></a>
                </div>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Hello, Welcome!</h1>
                <p>Don't have an account yet?</p>
                <button class="button register-btn">Register</button>  
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Welcome Back!</h1>
                <p>Already have an account yet?</p>
                <button class="button login-btn">Login</button>  
            </div>
        </div>
    </div>

    @vite(['resources/js/script.js'])
</body>
</html>
