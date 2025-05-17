<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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
        $possiblePaths = [
            'images/' . $imagePath,
            'img/' . $imagePath,
            'assets/images/' . $imagePath
        ];
        
        foreach ($possiblePaths as $path) {
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
     * @param string $imagePath
     * @return string
     */
    private function getPizzaImagePath($itemName, $imagePath = null)
    {
        // Map of pizza names to their image filenames
        $pizzaImageMap = [
            'pepporoni pizza' => ['pepporoni pizza.png', 'peperonipizza.jpg', 'pepperoni pizza.png'],
            'hawaiian pizza' => ['hawaian pizza.png', 'hawaiian pizza.jpg', 'hawaiian pizza.png'],
            'cheese pizza' => ['Cheese pizza.png', 'cheesepizza.jpg'],
            'meat pizza' => ['meaty pizza.png', 'meatpizza.jpg'],
            'overload cheese pizza' => ['overload cheese pizza.png', 'cheezypizza.jpg'],
            'cheesy pizza' => ['overload cheese pizza.png', 'cheezypizza.jpg'],
        ];
        
        $itemName = strtolower($itemName);
        
        // Check all possible directories for images
        $possibleDirectories = ['', 'images/', 'img/', 'assets/images/'];
        
        // First try the specific mapping
        if (isset($pizzaImageMap[$itemName])) {
            foreach ($pizzaImageMap[$itemName] as $possibleImage) {
                foreach ($possibleDirectories as $dir) {
                    if (file_exists(public_path($dir . $possibleImage))) {
                        return $dir . $possibleImage;
                    }
                }
            }
        }
        
        // Then try the provided image path
        if ($imagePath) {
            // Clean up the image path (remove any URL or asset path prefixes)
            $cleanImagePath = basename($imagePath);
            
            foreach ($possibleDirectories as $dir) {
                if (file_exists(public_path($dir . $cleanImagePath))) {
                    return $dir . $cleanImagePath;
                }
            }
            
            // If the exact path exists, use it
            if (file_exists(public_path($imagePath))) {
                return $imagePath;
            }
        }
        
        // Debug information - log what we're looking for
        \Log::info("Pizza image not found for: {$itemName}, tried image: {$imagePath}");
        
        // Default fallback - check if it exists
        foreach ($possibleDirectories as $dir) {
            if (file_exists(public_path($dir . 'pizza-placeholder.png'))) {
                return $dir . 'pizza-placeholder.png';
            }
        }
        
        // Ultimate fallback
        return 'pizza-placeholder.png';
    }

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

    public function index()
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', Auth::id())->get();
        
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

    public function checkout()
    {
        // Process the checkout logic here
        // For now, we'll just redirect back with a success message
        return redirect()->route('frontpage')->with('success', 'Order placed successfully!');
    }
    
    public function destroy(Cart $cart)
    {
        // Make sure the cart item belongs to the authenticated user
        if ($cart->user_id !== Auth::id()) {
            return redirect()->route('cart.index')->with('error', 'Unauthorized action.');
        }
        
        $cart->delete();
        return redirect()->route('cart.index')->with('success', 'Item removed from cart!');
    }
}