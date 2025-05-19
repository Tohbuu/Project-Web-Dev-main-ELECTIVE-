<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - CaptainCheff</title>
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
        .cart-item-image {
            width: 60px;
            height: 60px;
            object-fit: contain;
            margin-right: 10px;
        }
        .order-summary {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
        }
        .order-summary h3 {
            margin-top: 0;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            animation: fadeOut 3s forwards;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 1rem;
        }
        .quantity-btn {
            width: 25px;
            height: 25px;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            border-radius: 3px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .quantity-btn:hover {
            background-color: #e0e0e0;
        }
        
        .quantity-input {
            padding: 4px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .quantity-form {
            margin: 0;
        }
        
        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; }
        }
        
        .invalid-input {
            border: 1px solid #ff4444;
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
                @if(Auth::check())
                    <form action="{{ url('/logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: inherit; cursor: pointer;">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" style="color: inherit; text-decoration: none;">Log In</a>
                @endif
                <a href="{{ route('cart.index') }}" class="cart">
                    <i class='bx bx-cart icon'></i>
                    @if(isset($cartItems) && count($cartItems) > 0)
                        <span class="cart-count">{{ count($cartItems) }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.dashboard') }}" class="user">
                    <i class='bx bx-user-circle icon'></i>
                </a>
            </div>
        </div>
        <!-- Keep your menu items if needed -->
    </nav>
<!--Cart summary-->
    <div class="cart-container">
        <h1>Your Cart</h1>
        
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif
        
        @if(session('error'))
            <div class="error-message">
                {{ session('error') }}
            </div>
        @endif
        
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
                            <div style="display: flex; align-items: center;">
                                <img src="{{ asset($item->displayImage ?? 'images/pizza-placeholder.png') }}" alt="{{ $item->item }}" class="cart-item-image">
                                <div>
                                    <strong>{{ $item->item }}</strong>
                                    @if($item->special_instructions)
                                        <p><small>Notes: {{ $item->special_instructions }}</small></p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>₱{{ number_format($item->price, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="quantity-form">
                                @csrf
                                @method('PATCH')
                                <div style="display: flex; align-items: center;">
                                    <button type="button" class="quantity-btn decrease" data-id="{{ $item->id }}">-</button>
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="99" class="quantity-input" id="quantity-{{ $item->id }}" style="width: 40px; text-align: center; margin: 0 5px;">
                                    <button type="button" class="quantity-btn increase" data-id="{{ $item->id }}">+</button>
                                </div>
                            </form>
                        </td>
                        <td>{{ ucfirst($item->size) }}</td>
                        <td class="item-total" data-id="{{ $item->id }}">₱{{ number_format($item->price * $item->quantity, 2) }}</td>
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
                        <td id="grand-total"><strong>₱{{ number_format($cartItems->sum(function($item) {
                            return $item->price * $item->quantity;
                        }), 2) }}</strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <p>Items: <span id="total-items">{{ $cartItems->count() }}</span></p>
                <p>Total: <span id="total-price">₱{{ number_format($cartItems->sum(function($item) {
                    return $item->price * $item->quantity;
                }), 2) }}</span></p>
            </div>

            <div class="cart-actions">
                <a href="{{ url('/') }}" class="button">Continue Shopping</a>
                <form action="{{ route('cart.checkout') }}" method="POST">
                    @csrf
                    <button type="submit" class="button" style="background-color: #4CAF50; color: white;">Proceed to Checkout</button>
                </form>
            </div>
        @endif
    </div>

        <!-- Add JavaScript for quantity updates -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all quantity buttons
        const decreaseBtns = document.querySelectorAll('.quantity-btn.decrease');
        const increaseBtns = document.querySelectorAll('.quantity-btn.increase');
        const quantityInputs = document.querySelectorAll('.quantity-input');
        
        // Add event listeners to decrease buttons
        decreaseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const input = document.getElementById('quantity-' + id);
                let value = parseInt(input.value);
                if (value > 1) {
                    input.value = value - 1;
                    updateCart(id, input.value);
                }
            });
        });
        
        // Add event listeners to increase buttons
        increaseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const input = document.getElementById('quantity-' + id);
                let value = parseInt(input.value);
                if (value < 99) {
                    input.value = value + 1;
                    updateCart(id, input.value);
                }
            });
        });
        
        // Add event listeners to quantity inputs
        quantityInputs.forEach(input => {
            input.addEventListener('change', function() {
                const id = this.id.split('-')[1];
                let value = parseInt(this.value);
                
                // Validate input
                if (isNaN(value) || value < 1) {
                    value = 1;
                    this.value = 1;
                } else if (value > 99) {
                    value = 99;
                    this.value = 99;
                }
                
                updateCart(id, value);
            });
        });
        
        // Function to update cart via AJAX
        function updateCart(id, quantity) {
            // Create form data
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('_method', 'PATCH');
            formData.append('quantity', quantity);
            
            // Send AJAX request
            fetch('{{ url("cart") }}/' + id, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update item total
                    const itemTotal = document.querySelector('.item-total[data-id="' + id + '"]');
                    itemTotal.textContent = '₱' + parseFloat(data.itemTotal).toFixed(2);
                    
                    // Update grand total
                    document.getElementById('grand-total').innerHTML = '<strong>₱' + parseFloat(data.grandTotal).toFixed(2) + '</strong>';
                    document.getElementById('total-price').textContent = '₱' + parseFloat(data.grandTotal).toFixed(2);
                    
                    // Show success message if provided
                    if (data.message) {
                        const successDiv = document.createElement('div');
                        successDiv.className = 'success-message';
                        successDiv.textContent = data.message;
                        
                        const container = document.querySelector('.cart-container');
                        container.insertBefore(successDiv, container.firstChild);
                        
                        // Remove message after 3 seconds
                        setTimeout(() => {
                            successDiv.remove();
                        }, 3000);
                    }
                } else {
                    // Show error message
                    alert(data.message || 'Error updating cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the cart');
            });
        }
    });
    </script>

    @vite(['resources/js/app.js'])
</body>
</html>