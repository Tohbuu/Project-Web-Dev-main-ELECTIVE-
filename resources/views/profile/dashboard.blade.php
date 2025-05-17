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
                    <input type="text" id="phone" name="phone" value="{{ $user->phone ?? '' }}">
                </div>
                <div class="info-item">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" rows="3">{{ $user->address ?? '' }}</textarea>
                </div>
                <button type="submit" class="update-btn">Update Profile</button>
            </form>
        </div>

        <div class="card">
            <h2 class="section-title">Order History</h2>
            @if($orders->count() > 0)
                <div class="orders-container">
                    @foreach($orders as $order)
                        <div class="order-item">
                            <div class="order-header">
                                <h3>Order #{{ $order->id }}</h3>
                                <span class="order-date">{{ $order->created_at->format('M d, Y h:i A') }}</span>
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
                                </div>
                            </div>
                            <div class="order-actions">
                                <button class="edit-btn" onclick="toggleEditForm('{{ $order->id }}')">Edit Order</button>
                            </div>
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
                                            <label>
                                                <input type="radio" name="size" value="large" {{ $order->size == 'large' ? 'checked' : '' }}>
                                                Large
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="special-{{ $order->id }}">Special Instructions:</label>
                                        <textarea id="special-{{ $order->id }}" name="special_instructions">{{ $order->special_instructions }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="phone-{{ $order->id }}">Phone Number:</label>
                                        <input type="tel" id="phone-{{ $order->id }}" name="phone_number" value="{{ $order->phone_number }}" class="form-input">
                                    </div>
                                    <button type="submit" class="save-btn">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>No orders found.</p>
            @endif
        </div>
    </div>

    <script>
        function toggleEditForm(orderId) {
            const form = document.getElementById(`edit-form-${orderId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
</body>
</html>