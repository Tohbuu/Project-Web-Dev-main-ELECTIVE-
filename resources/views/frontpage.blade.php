<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Order delicious pizzas online - fresh and fast delivery">
    <title>Captain Chef - Fresh & Delicious Pizzas</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/frontpage.css', 'resources/css/animations.css'])
    <link rel="preconnect" href="https://unpkg.com">
</head>
<body class="fade-in">
    <!-- navigation bar -->
    <nav class="shiny-pearl animated-element nav-animation slide-in-right">
        <div class="navTop">
            <div class="navItem">
                @if(Auth::check())
                    <h1 class="welcome-title">Welcome, {{ htmlspecialchars(Auth::user()->name) }}!</h1>
                @else
                    <h1>{{ htmlspecialchars($user['username'] ?? 'Guest') }}</h1>
                @endif
            </div>
            <!-- Search bar -->
            <!--
            <div class="navItem">
                <div class="search">
                    <input id="search" type="text" class="searchInput" placeholder="Search...">
                    <label for="search"><i class='bx bx-search'></i></label>
                </div>
            </div>-->
            <div class="navItem">
    @if(Auth::check())
        <form action="{{ url('/logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;" class="animated-link">Log Out</button>
        </form>
        <a href="{{ route('cart.index') }}" class="cart animated-link">
            <i class='bx bx-cart icon'></i>
        </a>
        <a href="{{ route('profile.dashboard') }}" class="user animated-link">
            @if(Auth::user()->avatar)
                <img src="{{ Auth::user()->avatar }}&t={{ time() }}" alt="{{ Auth::user()->username }}" class="user-avatar">
            @else
                <i class='bx bx-user-circle icon'></i>
            @endif
        </a>
    @else
        <a href="{{ route('login') }}" class="animated-link">Log In</a>
        <a href="{{ route('cart.index') }}" class="cart animated-link">
            <i class='bx bx-cart icon'></i>
        </a>
        <a href="{{ route('profile.dashboard') }}" class="user animated-link">
            <i class='bx bx-user-circle icon'></i>
        </a>
    @endif
            </div>
        </div>

        <div class="navBottom">
            @php
                $menuItems = [
                    [ "name" => "Pepperoni Pizza", "class" => "pep" ],
                    [ "name" => "Cheese Pizza", "class" => "cheese" ],
                    [ "name" => "Hawaiian Pizza", "class" => "hawaian" ],
                    [ "name" => "Meat Pizza", "class" => "meat" ],
                    [ "name" => "Overload Cheese Pizza", "class" => "over" ],
                ];
            @endphp
            @foreach ($menuItems as $index => $item)
                <h3 class="menuItem color-transition" data-index="{{ $index }}">{{ $item['name'] }}</h3>
            @endforeach
        </div>
    </nav>

    <div class="slider animated-element slider-animation bounce-in">
        <div class="sliderWrapper">
            @php
                $pizzaItems = [
                    [
                        "name" => "Pepperoni Pizza",
                        "price" => "₱150",
                        "img" => "pepporoni pizza.png",
                        "class" => "pep",
                        "desc" => "Zesty pepperoni slices, tangy tomato sauce, melted mozzarella cheese, and a sprinkle of oregano on a golden crust."
                    ],
                    [ 
                        "name" => "Cheese Pizza", 
                        "price" => "₱150", 
                        "img" => "Cheese pizza.png", 
                        "class" => "cheese", 
                        "desc" => "Classic cheese pizza with our signature tomato sauce and a blend of premium mozzarella and cheddar cheeses." 
                    ],
                    [ 
                        "name" => "Hawaiian Pizza", 
                        "price" => "₱150", 
                        "img" => "hawaian pizza.png", 
                        "class" => "hawaian", 
                        "desc" => "Sweet pineapple chunks, savory ham, tomato sauce, and melted mozzarella cheese on our hand-tossed crust." 
                    ],
                    [ 
                        "name" => "Meat Pizza", 
                        "price" => "₱150", 
                        "img" => "meaty pizza.png", 
                        "class" => "meat", 
                        "desc" => "Loaded with pepperoni, Italian sausage, bacon, and ground beef on our signature sauce and cheese blend." 
                    ],
                    [ 
                        "name" => "Overload Cheesy Pizza", 
                        "price" => "₱150", 
                        "img" => "overload cheese pizza.png", 
                        "class" => "over", 
                        "desc" => "Five premium cheeses - mozzarella, cheddar, provolone, parmesan, and ricotta - melted to perfection." 
                    ]
                ];
            @endphp

            @foreach ($pizzaItems as $index => $pizza)
                <div class="sliderItem">
   <img src="{{ asset('images/' . $pizza['img']) }}" alt="{{ $pizza['name'] }}" class="sliderImg" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">                    <div class="sliderBg"></div>
                    <div class="container">
                        <h1 class="slidertitle {{ $pizza['class'] }}">{{ $pizza['name'] }}</h1>
                        <h2 class="sliderPrice">{{ $pizza['price'] }}</h2>
                        <button class="buyButton" data-index="{{ $index }}">BUY NOW</button>
                        @if (!empty($pizza['desc']))
                            <h3 class="Ingredients">Ingredients: {{ $pizza['desc'] }}</h3>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="features animated-element features-animation slide-in-left">
        <div class="feature">
            <img class="featureIcon" src="{{ asset('images/bird.png') }}" alt="Early Bird Special">
            <span class="FeatureTitle">EARLY BIRD FAMILY PACK</span>
            <span class="FeatureDesc">Order by 5 PM and snag two large pizzas <b>IN STORE ONLY</b></span>
        </div>

        <div class="feature">
            <img class="featureIcon" src="{{ asset('images/fast.png') }}" alt="Free Shipping">
            <span class="FeatureTitle">FREE SHIPPING</span>
            <span class="FeatureDesc">Free delivery near to you!</span>
        </div>

        <div class="feature">
            <img class="featureIcon" src="{{ asset('images/pizzaa.jpg') }}" alt="Fresh Pizza">
            <span class="FeatureTitle">GUARANTEED FRESH EVERYDAY!</span>
            <span class="FeatureDesc">Fresh from out of the oven</span>
        </div>
    </div>

    <div class="product animated-element product-animation zoom-in" id="product">
        <form id="orderForm" class="productForm" action="{{ url('/checkout') }}" method="POST">
            @csrf
            <img src="{{ asset('images/pepporoni pizza.png') }}" alt="Selected Pizza" class="productImg" id="formProductImg">
            <div class="productDetails">
                <h1 class="productTitle" id="productTitleDisplay">Pepperoni Pizza</h1>
                <h2 class="productPrice" id="productPriceDisplay">₱150</h2>
                <p class="Description" id="productDescDisplay">Zesty pepperoni slices, tangy tomato sauce, melted mozzarella cheese, and a sprinkle of oregano on a golden crust.</p>

                <input type="hidden" name="item" id="formItem" value="Pepperoni Pizza">
                <input type="hidden" name="price" id="formPrice" value="150">
                <input type="hidden" name="image" id="formImage" value="pepporoni pizza.png">
                <div class="formGroup">
                    <label for="quantity" class="formLabel">Quantity:</label>
                    <input type="number" id="quantity" name="quantity" min="1" value="1" class="formInput">
                </div>

                <div class="formGroup">
                    <label class="formLabel">Size:</label>
                    <div class="sizes">
                        <!--
                        <label class="sizeOption"><input type="radio" name="size" value="small" checked><span>Small</span></label> -->
                        <label class="sizeOption"><input type="radio" name="size" value="medium"><span>Medium</span></label>
                       <!--
                        <label class="sizeOption"><input type="radio" name="size" value="large"><span>Large</span></label>
                       -->
                    </div>
                </div>

                <div class="formGroup">
                    <label for="specialInstructions" class="formLabel">Special Instructions:</label>
                    <textarea id="specialInstructions" name="specialInstructions" class="formTextarea" placeholder="Any special requests?"></textarea>
                </div>

                <div class="formGroup">
                    <label for="phoneNumber" class="formLabel">Phone Number:</label>
                    <input type="tel" id="phoneNumber" name="phoneNumber" required class="formInput" 
                           placeholder="Enter your phone number" pattern="[0-9]{11}" 
                           data-profile-phone="{{ Auth::check() ? (Auth::user()->phone ?? '') : '' }}">
                    <span class="error-message" id="phone-error" style="display: none;">
                        This phone number doesn't match your profile phone number
                    </span>
                </div>

                <button type="submit" class="productButton">Place Order</button>
            </div>
        </form>
    </div>

    <footer class="site-footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>About Us</h3>
                <p>We serve the best pizzas in town, made with fresh ingredients and love.</p>
            </div>
            <div class="footer-section">
                <h3>Contact</h3>
                <p>Email: info@pizzashop.com</p>
                <p>Phone: +123 456 7890</p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#"><i class='bx bxl-facebook'></i></a>
                   
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Pizza Shop. All rights reserved.</p>
        </div>
    </footer>

    <!-- Add these hidden elements to store data for JavaScript -->
    <script type="application/json" id="pizza-data">
        @json($pizzaItems)
    </script>
    <script type="text/plain" id="asset-path">{{ asset('') }}</script>

    <!-- Add phone validation script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.getElementById('phoneNumber');
        const phoneError = document.getElementById('phone-error');
        const orderForm = document.getElementById('orderForm');
        
        // Auto-fill from profile if available
        const profilePhone = phoneInput.getAttribute('data-profile-phone');
        if (profilePhone) {
            phoneInput.value = profilePhone;
        }
        
        // Validate on input
        phoneInput.addEventListener('input', function() {
            validatePhoneNumber();
        });
        
        // Validate on form submission
        orderForm.addEventListener('submit', function(event) {
            if (!validatePhoneNumber()) {
                event.preventDefault();
            }
        });
        
        function validatePhoneNumber() {
            const profilePhone = phoneInput.getAttribute('data-profile-phone');
            
            // If user is logged in and has a profile phone number
            if (profilePhone) {
                if (phoneInput.value !== profilePhone) {
                    phoneInput.classList.add('invalid-input');
                    phoneError.style.display = 'block';
                    return false;
                } else {
                    phoneInput.classList.remove('invalid-input');
                    phoneError.style.display = 'none';
                    return true;
                }
            } else {
                // If no profile phone, just validate the pattern
                const phonePattern = /^\d{11}$/;
                if (!phonePattern.test(phoneInput.value)) {
                    phoneInput.classList.add('invalid-input');
                    phoneError.textContent = 'Please enter a valid 11-digit phone number';
                    phoneError.style.display = 'block';
                    return false;
                } else {
                    phoneInput.classList.remove('invalid-input');
                    phoneError.style.display = 'none';
                    return true;
                }
            }
        }
    });
    </script>

    <!-- Force Google avatar to display properly -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Find all avatar images
        const avatars = document.querySelectorAll('.user-avatar, .profile-avatar-img');
        
        avatars.forEach(avatar => {
            // Check if it's a Google avatar
            if (avatar.src && avatar.src.includes('googleusercontent.com')) {
                console.log('Processing Google avatar:', avatar.src);
                
                // Remove any existing query parameters
                const originalSrc = avatar.src.split('?')[0];
                
                // Generate unique parameters
                const timestamp = new Date().getTime();
                const random = Math.random().toString(36).substring(2, 15);
                
                // Create a new URL with size parameter and cache busters
                const newSrc = `${originalSrc}?sz=100&t=${timestamp}&r=${random}`;
                console.log('New avatar URL:', newSrc);
                
                // Update the image source
                avatar.src = newSrc;
                
                // Add error handling
                avatar.onerror = function() {
                    console.log('Avatar failed to load, trying default:', this.alt);
                    this.src = '{{ asset('images/default-avatar.png') }}?v=' + new Date().getTime();
                    this.onerror = null; // Prevent infinite error loop
                };
            }
        });
    });
    </script>

    <!-- Keep your existing script tag that loads app.js -->
    @vite(['resources/js/app.js'])
</body>
</html>