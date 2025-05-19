<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt - CaptainCheff</title>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    @vite(['resources/css/frontpage.css'])
    <style>
        /* Base styles */
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        
        .receipt-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }
        
        .receipt-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .receipt-header p {
            color: #666;
            margin: 0;
        }
        
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .receipt-info-section {
            flex: 1;
        }
        
        .receipt-info-section h3 {
            margin-top: 0;
            color: #555;
            border-bottom: 1px solid #eee;
            padding-bottom: 0.5rem;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .receipt-item-details {
            flex: 3;
        }
        
        .receipt-item-price {
            flex: 1;
            text-align: right;
        }
        
        .receipt-total {
            margin-top: 2rem;
            text-align: right;
        }
        
        .receipt-total-row {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 0.5rem;
        }
        
        .receipt-total-row span:first-child {
            width: 150px;
            text-align: left;
        }
        
        .receipt-total-row.final {
            font-weight: bold;
            font-size: 1.2rem;
            border-top: 2px solid #eee;
            padding-top: 1rem;
            margin-top: 1rem;
        }
        
        .receipt-actions {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-secondary {
            background-color: #f0f0f0;
            color: #333;
        }
        
        .btn-print {
            background-color: #2196F3;
            color: white;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border-radius: 5px;
            max-width: 400px;
            text-align: center;
        }
        
        .modal-title {
            color: #e74c3c;
            margin-top: 0;
        }
        
        .modal-buttons {
            margin-top: 20px;
        }
        
        .modal-buttons button {
            padding: 8px 16px;
            margin: 0 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .confirm-delete {
            background-color: #e74c3c;
            color: white;
        }
        
        .cancel-delete {
            background-color: #7f8c8d;
            color: white;
        }
        
        /* Print styles */
        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            
            .receipt-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0;
            }
            
            .back-button, .print-button, .delete-button, .modal, .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="receipt-header">
            <h1>Order Receipt</h1>
            <p>Thank you for your order!</p>
            <p>Order #{{ $order->order_number }}</p>
            <p>{{ $order->created_at->format('F j, Y h:i A') }}</p>
        </div>
        
        <div class="receipt-info">
            <div class="receipt-info-section">
                <h3>Customer Information</h3>
                <p><strong>Name:</strong> {{ $user->name }}</p>
                <p><strong>Email:</strong> {{ $user->email }}</p>
                <p><strong>Phone:</strong> {{ $order->phone_number ?? $user->phone ?? 'Not provided' }}</p>
                <p><strong>Address:</strong> {{ $user->address ?? 'Not provided' }}</p>
            </div>
            
            <div class="receipt-info-section">
                <h3>Order Details</h3>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y') }}</p>
                <p><strong>Order Time:</strong> {{ $order->created_at->format('h:i A') }}</p>
                <p><strong>Status:</strong> <span style="color: green; font-weight: bold;">Completed</span></p>
            </div>
        </div>
        
        <h3>Order Items</h3>
        <div class="receipt-items">
            <div class="receipt-item">
                <div class="receipt-item-details">
                    <h4>{{ $order->item }}</h4>
                    <p>Size: {{ ucfirst($order->size) }}</p>
                    <p>Quantity: {{ $order->quantity }}</p>
                    @if($order->special_instructions)
                        <p><small>Special Instructions: {{ $order->special_instructions }}</small></p>
                    @endif
                </div>
                <div class="receipt-item-price">
                    <p>₱{{ number_format($order->price, 2) }} each</p>
                    <p><strong>₱{{ number_format($order->price * $order->quantity, 2) }}</strong></p>
                </div>
            </div>
        </div>
        
        <div class="receipt-total">
            <div class="receipt-total-row">
                <span>Subtotal:</span>
                <span>₱{{ number_format($order->price * $order->quantity, 2) }}</span>
            </div>
            <div class="receipt-total-row">
                <span>Delivery Fee:</span>
                <span>₱0.00</span>
            </div>
            <div class="receipt-total-row final">
                <span>Total:</span>
                <span>₱{{ number_format($order->price * $order->quantity, 2) }}</span>
            </div>
        </div>
        
        <div class="receipt-actions no-print">
            <a href="{{ route('profile.dashboard') }}" class="btn btn-secondary">Back to Dashboard</a>
            <button onclick="window.print()" class="btn btn-print">Print Receipt</button>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3 class="modal-title">Confirm Deletion</h3>
            <p>Are you sure you want to delete this order? This action cannot be undone.</p>
            <div class="modal-buttons">
                <form action="{{ route('order.delete', $order->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="confirm-delete">Yes, Delete</button>
                </form>
                <button class="cancel-delete" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        // Close modal if user clicks outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                closeDeleteModal();
            }
        }
    </script>
</body>
</html>