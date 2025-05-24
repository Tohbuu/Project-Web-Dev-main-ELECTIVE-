<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/dashboard.css'])
</head>
<body>
    <div class="container">
        <a href="{{ url('/') }}" class="back-button">
            <i class='bx bx-arrow-back'></i> Back to Home
        </a>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Add the checkout confirmation here -->
        @if(session('checkout_complete'))
            <div class="order-confirmation">
                <div class="success-message">
                    <h3>Order Placed Successfully!</h3>
                    <p>Your order has been placed and is being processed.</p>
                    <p>Order Summary:</p>
                    <ul>
                        <li>Items: {{ session('order_items_count') }}</li>
                        <li>Total: ₱{{ number_format(session('order_total'), 2) }}</li>
                    </ul>
                </div>
            </div>
        @endif

        <div class="profile-header">
            @if(Auth::user()->avatar)
                <div class="profile-avatar">
                    <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->username }}" class="profile-avatar-img">
                </div>
            @endif
            <h1>{{ Auth::user()->name }}</h1>
            <p>{{ Auth::user()->email }}</p>
        </div>

        <div class="card">
            <h2 class="section-title">Personal Information</h2>
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                <div class="info-item">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" value="{{ $user->name }}" required>
                </div>
                <div class="info-item">
                    <label for="email">Email:</label>
                    <input type="email" id="email" value="{{ $user->email }}" disabled>
                </div>
                <div class="info-item">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="{{ $user->phone ?? '' }}" 
                           placeholder="Enter 11-digit phone number" pattern="[0-9]{11}" 
                           title="Please enter a valid 11-digit phone number">
                    <span class="error-message" id="profile-phone-error" style="display: none;">
                        Please enter a valid 11-digit phone number
                    </span>
                </div>
                <div class="info-item">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" rows="3">{{ $user->address ?? '' }}</textarea>
                </div>
                <button type="submit" class="update-btn">Update Profile</button>
            </form>
        </div>
<!--order history-->
        <div class="card">
            <h2 class="section-title">Order History</h2>
            @php
                // Filter orders to only show completed orders
                $completedOrders = $orders->where('status', 'completed');
            @endphp
            
            @if($completedOrders->count() > 0)
                <div class="orders-container">
                    @foreach($completedOrders as $order)
                        <div class="order-item">
                            <div class="order-header">
                                <h3>Order #{{ $order->id }}</h3>
                                <span class="order-date">{{ $order->created_at->setTimezone('Asia/Manila')->format('M d, Y h:i A') }}</span>
                            </div>
                            <div class="order-details">
                                <div class="order-image">
                                    <img src="{{ asset($order->image) }}" alt="{{ $order->item }}">
                                </div>
                                <div class="order-info">
                                    <h4>{{ $order->item }}</h4>
                                    <p>Size: {{ ucfirst($order->size) }}</p>
                                    <p>Quantity: {{ $order->quantity }}</p>
                                    <p>Price: ₱{{ $order->price }}</p>
                                    <p>Total: ₱{{ $order->price * $order->quantity }}</p>
                                    @if($order->special_instructions)
                                        <p>Special Instructions: {{ $order->special_instructions }}</p>
                                    @endif
                                    @if($order->phone_number)
                                        <p>Phone Number: {{ $order->phone_number }}</p>
                                    @endif
                                </div>
                            </div>
                            <!--order status actions-->
                            <div class="order-actions">
                                <a href="{{ route('order.receipt', ['id' => $order->id]) }}" class="view-receipt-btn">View Receipt</a>
                                {{-- <button class="edit-btn" onclick="toggleEditForm('{{ $order->id }}')">Edit Order</button> --}}
                            </div>
                            <!-- Edit form remains the same -->
                            <div class="edit-form" id="edit-form-{{ $order->id }}" style="display: none;">
                                <form action="{{ route('order.update', $order->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="quantity-{{ $order->id }}">Quantity:</label>
                                        <input type="number" id="quantity-{{ $order->id }}" name="quantity" value="{{ $order->quantity }}" min="1" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Size:</label>
                                        <div class="size-options">
                                            <label>
                                                <input type="radio" name="size" value="small" {{ $order->size == 'small' ? 'checked' : '' }}>
                                                Small
                                            </label>
                                            <label>
                                                <input type="radio" name="size" value="medium" {{ $order->size == 'medium' ? 'checked' : '' }}>
                                                Medium
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="special-{{ $order->id }}">Special Instructions:</label>
                                        <textarea id="special-{{ $order->id }}" name="special_instructions">{{ $order->special_instructions }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone-{{ $order->id }}">Phone Number:</label>
                                        <input type="tel" id="phone-{{ $order->id }}" name="phone_number" value="{{ $order->phone_number }}" 
                                               class="form-input phone-input" placeholder="Enter 11-digit phone number" 
                                               pattern="[0-9]{11}" title="Please enter a valid 11-digit phone number" 
                                               data-profile-phone="{{ $user->phone ?? '' }}">
                                        <span class="error-message" id="phone-error-{{ $order->id }}" style="display: none;">
                                            Please enter a valid 11-digit phone number
                                        </span>
                                    </div>
                                    <button type="submit" class="save-btn">Save Changes</button>
                                    <button type="button" class="cancel-btn" onclick="toggleEditForm('{{ $order->id }}')">Cancel</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No completed orders found. After you complete the checkout process, your orders will appear here.</p>
            @endif
        </div>
    </div>

    <script>
        function toggleEditForm(orderId) {
            const form = document.getElementById(`edit-form-${orderId}`);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            } else {
                console.error(`Edit form for order ${orderId} not found`);
            }
        }

        // Phone number validation
        document.addEventListener('DOMContentLoaded', function() {
            // Profile phone validation
            const profilePhone = document.getElementById('phone');
            if (profilePhone) {
                profilePhone.addEventListener('input', function() {
                    validatePhoneNumber(this, 'profile-phone-error');
                });
            }

            // Order form phone validation
            const phoneInputs = document.querySelectorAll('.phone-input');
            phoneInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const orderId = this.id.split('-')[1];
                    validatePhoneNumber(this, `phone-error-${orderId}`);
                });

                // Auto-fill from profile if empty
                input.addEventListener('focus', function() {
                    if (!this.value) {
                        const profilePhone = this.getAttribute('data-profile-phone');
                        if (profilePhone) {
                            this.value = profilePhone;
                            const orderId = this.id.split('-')[1];
                            validatePhoneNumber(this, `phone-error-${orderId}`);
                        }
                    }
                });
            });

            function validatePhoneNumber(input, errorId) {
                const errorElement = document.getElementById(errorId);
                const phonePattern = /^\d{11}$/;
                
                if (!phonePattern.test(input.value) && input.value !== '') {
                    input.classList.add('invalid-input');
                    errorElement.style.display = 'block';
                    return false;
                } else {
                    input.classList.remove('invalid-input');
                    errorElement.style.display = 'none';
                    return true;
                }
            }

            // Form submission validation
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    let isValid = true;
                    
                    // Validate phone inputs in this form
                    const phoneInputs = this.querySelectorAll('input[type="tel"]');
                    phoneInputs.forEach(input => {
                        const orderId = input.id.split('-')[1];
                        if (!validatePhoneNumber(input, `phone-error-${orderId}`)) {
                            isValid = false;
                        }
                    });
                    
                    // Validate profile phone if it exists in this form
                    const profilePhone = this.querySelector('#phone');
                    if (profilePhone && !validatePhoneNumber(profilePhone, 'profile-phone-error')) {
                        isValid = false;
                    }
                    
                    if (!isValid) {
                        event.preventDefault();
                    }
                });
            });
        });
    </script>
</body>
</html>