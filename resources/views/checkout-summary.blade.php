<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Summary - CaptainCheff</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/frontpage.css'])
    <style>
        .checkout-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .checkout-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-bottom: 1px solid #ddd;
            padding-bottom: 1rem;
        }
        .order-info {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
        }
        .order-items {
            margin-bottom: 2rem;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        .item-details {
            display: flex;
            align-items: center;
        }
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 1rem;
        }
        .order-summary {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 5px;
            margin-top: 1.5rem;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        .total-row {
            font-size: 1.2rem;
            font-weight: bold;
            border-top: 1px solid #ddd;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        .checkout-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
        }
        .button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            text-decoration: none;
            display: inline-block;
        }
        .button.primary {
            background-color: #4CAF50;
            color: white;
        }
        .button.secondary {
            background-color: #f0f0f0;
            color: #333;
        }
        .customer-info {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Reuse your navigation -->
    <nav class="shiny-pearl">
        <div class="navTop">
            <div class="navItem">
                <h1>{{ htmlspecialchars($user->name ?? 'Guest') }}</h1>
            </div>
            <div class="navItem">
                @if(Auth::check())
                    <form action="{{ url('/logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" style="color: inherit; text-decoration: none;">Log In</a>
                @endif
                <a href="{{ route('profile.dashboard') }}" class="user">
                    <i class='bx bx-user-circle icon'></i>
                </a>
            </div>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="checkout-header">
            <h1>Checkout Summary</h1>
            <div>
                <p>Please review your order before confirming</p>
            </div>
        </div>
        
        <div class="order-info">
            <h3>Order Information</h3>
            <p><strong>Order Number:</strong> {{ $orderNumber }}</p>
            <p><strong>Date:</strong> {{ $orderDate }}</p>
        </div>
        
        <div class="customer-info">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> {{ $user->name }}</p>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Phone:</strong> {{ $user->phone ?? 'Not provided' }}</p>
            <p><strong>Address:</strong> {{ $user->address ?? 'Not provided' }}</p>
        </div>
        
        <div class="order-items">
            <h3>Order Items</h3>
            @foreach($cartItems as $item)
                <div class="order-item">
                    <div class="item-details">
                        <img src="{{ asset($item->displayImage ?? 'images/pizza-placeholder.png') }}" alt="{{ $item->item }}" class="item-image">
                        <div>
                            <h4>{{ $item->item }}</h4>
                            <p>Size: {{ ucfirst($item->size) }}</p>
                            <p>Quantity: {{ $item->quantity }}</p>
                            @if($item->special_instructions)
                                <p><small>Notes: {{ $item->special_instructions }}</small></p>
                            @endif
                        </div>
                    </div>
                    <div class="item-price">
                        <p>₱{{ number_format($item->price, 2) }} each</p>
                        <p><strong>₱{{ number_format($item->price * $item->quantity, 2) }}</strong></p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>₱{{ number_format($total, 2) }}</span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee:</span>
                <span>₱0.00</span>
            </div>
            <div class="summary-row total-row">
                <span>Total:</span>
                <span>₱{{ number_format($total, 2) }}</span>
            </div>
        </div>
        
        <div class="checkout-actions">
            <a href="{{ route('cart.index') }}" class="button secondary">Back to Cart</a>
            <form action="{{ route('cart.complete') }}" method="POST">
                @csrf
                <button type="submit" class="button primary">Confirm Order</button>
            </form>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>