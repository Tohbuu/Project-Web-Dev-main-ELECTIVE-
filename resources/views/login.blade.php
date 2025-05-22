<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Chef - Login</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="container">
        <div class="form-box login">
            {{-- Display validation errors --}}
            @if ($errors->any())
                <div class="error-messages">
                    @foreach ($errors->all() as $error)
                        <p class="error-text">{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            
            <form action="{{ url('/login') }}" method="POST">
                @csrf
                <h1>Login</h1>
                <div class="inputbox">
                    <input 
                        name="username" 
                        type="text" 
                        placeholder="Username" 
                        value="{{ old('username') }}" 
                        required
                    >
                    <i class='bx bxs-user'></i>
                </div>
                <div class="inputbox">
                    <input 
                        name="password" 
                        type="password" 
                        placeholder="Password" 
                        required
                    >
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="forgot-link">
                    <a href="#">Forgot password</a>
                </div>
                <button type="submit" class="button">Login</button>
                <div class="login-options">
                    <p>or login with</p>
                    <div class="social-icons">
                        <a href="{{ route('auth.google') }}" aria-label="Login with Google"><i class='bx bxl-google'></i></a>
                    </div>
                </div>
            </form>
        </div>

        <div class="form-box registration">
            <form action="{{ url('/register') }}" method="POST">
                @csrf
                <h1>Registration</h1>
                <div class="inputbox">
                    <input 
                        name="username" 
                        type="text" 
                        placeholder="Username" 
                        value="{{ old('username') }}" 
                        required
                    >
                    <i class='bx bxs-user'></i>
                </div>
                <div class="inputbox">
                    <input 
                        name="email" 
                        type="email" 
                        placeholder="Email" 
                        value="{{ old('email') }}" 
                        required
                    >
                    <i class='bx bxs-envelope'></i>
                </div>
                <div class="inputbox">
                    <input 
                        name="password" 
                        type="password" 
                        placeholder="Password" 
                        required
                    >
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <button type="submit" class="button">Register</button>
                <div class="login-options">
    <p>or register with</p>
    <div class="social-icons">
        <a href="{{ route('auth.google') }}" aria-label="Register with Google"><i class='bx bxl-google'></i></a>
    </div>
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
                <p>Already have an account?</p>
                <button class="button login-btn">Login</button>  
            </div>
        </div>
    </div>
    @vite(['resources/js/script.js'])
</body>
</html>

{{-- helopppo --}}