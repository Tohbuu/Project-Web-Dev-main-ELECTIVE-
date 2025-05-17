<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orders = Cart::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('profile.dashboard', compact('user', 'orders'));
    }
    
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);
        
        try {
            $user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);
            
            return redirect()->route('profile.dashboard')->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('profile.dashboard')->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }
    
    public function updateOrder(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'required|in:small,medium,large',
            'special_instructions' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:20',  // Add validation for phone number
        ]);
        
        try {
            $order = Cart::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
                
            $order->update([
                'quantity' => $request->quantity,
                'size' => $request->size,
                'special_instructions' => $request->special_instructions,
                'phone_number' => $request->phone_number,  // Include phone number
            ]);
            
            return redirect()->route('profile.dashboard')->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('profile.dashboard')->with('error', 'Error updating order: ' . $e->getMessage());
        }
    }
}