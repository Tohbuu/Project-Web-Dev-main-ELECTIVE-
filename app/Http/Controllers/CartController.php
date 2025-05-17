<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'item' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
            'size' => 'required|in:small,medium,large',
            'image' => 'required|string',
            'specialInstructions' => 'nullable|string',
            'phoneNumber' => 'required|string',  // Add validation for phone number
        ]);
        
        Cart::create([
            'user_id' => Auth::id(),
            'item' => $request->item,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'size' => $request->size,
            'image' => $request->image,
            'special_instructions' => $request->specialInstructions,
            'phone_number' => $request->phoneNumber,  // Include phone number
        ]);
        
        return redirect()->route('cart.index')->with('success', 'Item added to cart!');
    }

    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->get();
        return view('checkout', compact('cartItems'));
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