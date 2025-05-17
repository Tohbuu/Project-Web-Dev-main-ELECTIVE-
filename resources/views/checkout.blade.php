<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Pizza App</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/frontpage.css'])
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }
        .cart-table th, .cart-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .cart-table th {
            background-color: #f8f9fa;
        }
        .cart-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }
        .empty-cart {
            text-align: center;
            padding: 2rem;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>
    <!-- Reuse your navigation -->
    <nav class="shiny-pearl">
        <div class="navTop">
            <div class="navItem">
                <h1>{{ htmlspecialchars($user['username'] ?? 'Guest') }}</h1>
            </div>
            <div class="navItem">
                <div class="search">
                    <input id="search" type="text" class="searchInput" placeholder="Search...">
                    <label for="search"><i class='bx bx-search'></i></label>
                </div>
            </div>
            <div class="navItem">
                <form action="{{ url('/logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">Log Out</button>
                </form>
                <a href="{{ route('cart.index') }}" class="cart">
                    <i class='bx bx-cart icon'></i>
                    @if(isset($cartItems) && count($cartItems) > 0)
                        <span class="cart-count">{{ count($cartItems) }}</span>
                    @endif
                </a>
                <span class="user"><i class='bx bx-user-circle icon'></i></span>
            </div>
        </div>
        <!-- Keep your menu items if needed -->
    </nav>

    <div class="cart-container">
        <h1>Your Cart</h1>
        
        @if($cartItems->isEmpty())
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="{{ url('/') }}" class="button">Continue Shopping</a>
            </div>
        @else
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Size</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr>
                        <td>
                            <img src="{{ asset($item->image) }}" width="60" alt="{{ $item->item }}" style="margin-right: 10px;">
                            {{ $item->item }}
                            @if($item->special_instructions)
                                <p><small>Notes: {{ $item->special_instructions }}</small></p>
                            @endif
                        </td>
                        <td>₱{{ number_format($item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ ucfirst($item->size) }}</td>
                        <td>₱{{ number_format($item->price * $item->quantity, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.destroy', $item->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button" style="background-color: #ff4444; color: white;">Remove</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align: right;"><strong>Grand Total:</strong></td>
                        <td><strong>₱{{ number_format($cartItems->sum(function($item) {
                            return $item->price * $item->quantity;
                        }), 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="cart-actions">
                <a href="{{ url('/') }}" class="button">Continue Shopping</a>
                <form action="{{ route('cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="button" style="background-color: #4CAF50; color: white;">Proceed to Checkout</button>
                </form>
            </div>
        @endif
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>