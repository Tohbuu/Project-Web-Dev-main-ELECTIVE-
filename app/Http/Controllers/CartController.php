<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    /**
     * Image directories to search for product images
     */
    private const IMAGE_DIRECTORIES = ['', 'images/', 'img/', 'assets/images/'];
    
    /**
     * Map of pizza names to their possible image filenames
     */
    private const PIZZA_IMAGE_MAP = [
        'pepporoni pizza' => ['pepporoni pizza.png', 'peperonipizza.jpg', 'pepperoni pizza.png'],
        'hawaiian pizza' => ['hawaian pizza.png', 'hawaiian pizza.jpg', 'hawaiian pizza.png'],
        'cheese pizza' => ['Cheese pizza.png', 'cheesepizza.jpg'],
        'meat pizza' => ['meaty pizza.png', 'meatpizza.jpg'],
        'overload cheese pizza' => ['overload cheese pizza.png', 'cheezypizza.jpg'],
        'cheesy pizza' => ['overload cheese pizza.png', 'cheezypizza.jpg'],
    ];

    /**
     * Normalize image path for storage
     * 
     * @param string $imagePath
     * @return string
     */
    private function normalizeImagePath($imagePath)
    {
        // Remove any URL or asset path prefixes
        $imagePath = basename($imagePath);
        
        // Check if the image exists in public directory
        foreach (self::IMAGE_DIRECTORIES as $dir) {
            $path = $dir . $imagePath;
            if (file_exists(public_path($path))) {
                return $path;
            }
        }
        
        // If we can't find the image, just return the basename
        return $imagePath;
    }

    /**
     * Get the correct image path for a pizza item
     * 
     * @param string $itemName
     * @param string|null $imagePath
     * @return string
     */
    private function getPizzaImagePath($itemName, $imagePath = null)
    {
        $itemName = strtolower($itemName);
        
        // First try the specific mapping
        if (isset(self::PIZZA_IMAGE_MAP[$itemName])) {
            foreach (self::PIZZA_IMAGE_MAP[$itemName] as $possibleImage) {
                foreach (self::IMAGE_DIRECTORIES as $dir) {
                    if (file_exists(public_path($dir . $possibleImage))) {
                        return $dir . $possibleImage;
                    }
                }
            }
        }
        
        // Then try the provided image path
        if ($imagePath) {
            // Clean up the image path
            $cleanImagePath = basename($imagePath);
            
            foreach (self::IMAGE_DIRECTORIES as $dir) {
                if (file_exists(public_path($dir . $cleanImagePath))) {
                    return $dir . $cleanImagePath;
                }
            }
            
            // If the exact path exists, use it
            if (file_exists(public_path($imagePath))) {
                return $imagePath;
            }
        }
        
        // Log what we're looking for
        Log::info("Pizza image not found for: {$itemName}, tried image: {$imagePath}");
        
        // Default fallback - check if it exists
        foreach (self::IMAGE_DIRECTORIES as $dir) {
            if (file_exists(public_path($dir . 'pizza-placeholder.png'))) {
                return $dir . 'pizza-placeholder.png';
            }
        }
        
        // Ultimate fallback
        return 'pizza-placeholder.png';
    }

    /**
     * Get pending cart items for the current user
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getPendingCartItems()
    {
        return Cart::where('user_id', Auth::id())
            ->where(function($query) {
                $query->where('status', 'pending')
                      ->orWhereNull('status');
            })
            ->get();
    }

    /**
     * Calculate total price of cart items
     * 
     * @param \Illuminate\Database\Eloquent\Collection $cartItems
     * @return float
     */
    private function calculateCartTotal($cartItems)
    {
        return $cartItems->sum(function($item) {
            return $item->price * $item->quantity;
        });
    }

    /**
     * Generate a unique order number
     * 
     * @return string
     */
    private function generateOrderNumber()
    {
        return 'ORD-' . time() . '-' . Auth::id();
    }

    /**
     * Add item to cart
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'item' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|in:small,medium,large',
            'image' => 'required|string',
            'specialInstructions' => 'nullable|string',
            'phoneNumber' => 'required|string',
        ]);
        
        // Normalize the image path
        $imagePath = $this->normalizeImagePath($request->image);
        
        Cart::create([
            'user_id' => Auth::id(),
            'item' => $request->item,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'size' => $request->size,
            'image' => $imagePath,
            'special_instructions' => $request->specialInstructions,
            'phone_number' => $request->phoneNumber,
        ]);
        
        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    /**
     * Display cart contents
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $this->getPendingCartItems();
        
        // Update image paths and standardize names for all cart items
        foreach ($cartItems as $item) {
            // Standardize pizza names
            if (strtolower($item->item) === 'cheesy pizza') {
                $item->item = 'Overload Cheese Pizza';
            }
            
            $item->displayImage = $this->getPizzaImagePath($item->item, $item->image);
        }
        
        return view('checkout', [
            'user' => $user,
            'cartItems' => $cartItems
        ]);
    }

    /**
     * Show checkout page
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request)
    {
        $cartItems = $this->getPendingCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        $total = $this->calculateCartTotal($cartItems);
        $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
        
        // Store order information in session for the receipt page
        session([
            'order_summary' => [
                'order_number' => $orderNumber,
                'order_date' => now()->format('M d, Y h:i A'),
                'items' => $cartItems,
                'total' => $total
            ]
        ]);
        
        return view('checkout-summary', [
            'orderNumber' => $orderNumber,
            'orderDate' => now()->format('M d, Y h:i A'),
            'cartItems' => $cartItems,
            'total' => $total,
            'user' => Auth::user()
        ]);
    }
    
    /**
     * Complete the checkout process and clear the cart
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function completeCheckout(Request $request)
    {
        $cartItems = $this->getPendingCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }
        
        $total = $this->calculateCartTotal($cartItems);
        $orderNumber = $this->generateOrderNumber();
        
        // Update all cart items to completed status
        foreach ($cartItems as $item) {
            $item->status = 'completed';
            $item->order_number = $orderNumber;
            $item->save();
        }
        
        // Get the order summary from session or create one
        $orderSummary = session('order_summary') ?? [
            'order_number' => $orderNumber,
            'order_date' => now()->format('M d, Y h:i A'),
            'items' => $cartItems,
            'total' => $total
        ];
        
        // Store checkout information in session for confirmation message
        session()->flash('checkout_complete', true);
        session()->flash('order_items_count', $cartItems->count());
        session()->flash('order_total', $total);
        session()->flash('order_number', $orderNumber);
        
        // Clear the order summary from session
        session()->forget('order_summary');
        
        return view('order-receipt', [
            'orderNumber' => $orderSummary['order_number'],
            'orderDate' => $orderSummary['order_date'],
            'items' => $orderSummary['items'],
            'total' => $orderSummary['total'],
            'user' => Auth::user()
        ]);
    }

    /**
     * Remove item from cart
     * 
     * @param Cart $cart
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Cart $cart)
    {
        // Make sure the cart item belongs to the authenticated user
        if ($cart->user_id !== Auth::id()) {
            return redirect()->route('cart.index')->with('error', 'Unauthorized action.');
        }
        
        $cart->delete();
        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }

    /**
     * Update the specified cart item in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);
        
        try {
            $cartItem = Cart::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $cartItem->update([
                'quantity' => $request->quantity,
            ]);
            
            // Calculate new item total and cart total
            $itemTotal = $cartItem->price * $cartItem->quantity;
            $cartTotal = Cart::where('user_id', Auth::id())
                ->sum(DB::raw('price * quantity'));
            
            // If it's an AJAX request, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cart updated successfully',
                    'itemTotal' => number_format($itemTotal, 2),
                    'cartTotal' => number_format($cartTotal, 2),
                    'quantity' => $cartItem->quantity
                ]);
            }
            
            // For non-AJAX requests, redirect back with success message
            return redirect()->route('cart.index')->with('success', 'Cart updated successfully');
        } catch (\Exception $e) {
            // If it's an AJAX request, return JSON error
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating cart: ' . $e->getMessage()
                ], 500);
            }
            
            // For non-AJAX requests, redirect back with error message
            return redirect()->route('cart.index')->with('error', 'Error updating cart: ' . $e->getMessage());
        }
    }

    /**
     * Complete order and redirect to dashboard
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function complete(Request $request)
    {
        $cartItems = $this->getPendingCartItems();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        $total = $this->calculateCartTotal($cartItems);
        $orderNumber = $this->generateOrderNumber();
        
        // Update all cart items to completed status
        foreach ($cartItems as $item) {
            $item->status = 'completed';
            $item->order_number = $orderNumber;
            $item->save();
        }
        
        // Store checkout information in session for confirmation message
        session()->flash('checkout_complete', true);
        session()->flash('order_items_count', $cartItems->count());
        session()->flash('order_total', $total);
        session()->flash('order_number', $orderNumber);
        
        return redirect()->route('profile.dashboard')->with('success', 'Your order has been placed successfully!');
    }
}