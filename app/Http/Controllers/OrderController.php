<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function update(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'size' => 'required|in:small,medium',
            'phone_number' => 'required|regex:/^[0-9]{11}$/',
        ]);

        try {
            // Find the order
            $order = Cart::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->firstOrFail();

            // Check if the order is still within the editable time window (e.g., 30 minutes)
            $orderTime = \Carbon\Carbon::parse($order->created_at);
            $currentTime = now();
            $timeElapsed = $orderTime->diffInMinutes($currentTime);
            
            // If more than 30 minutes have passed, don't allow updates
            if ($timeElapsed > 30) {
                return redirect()->route('profile.dashboard')->with('error', 'Orders can only be modified within 30 minutes of placement.');
            }

            // Update the order
            $order->quantity = $request->quantity;
            $order->size = $request->size;
            $order->special_instructions = $request->special_instructions;
            $order->phone_number = $request->phone_number;
            $order->save();

            return redirect()->route('profile.dashboard')->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            return redirect()->route('profile.dashboard')->with('error', 'Failed to update order: ' . $e->getMessage());
        }
    }

    public function receipt($id)
    {
        try {
            // Find the order
            $order = Cart::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('status', 'completed')
                ->firstOrFail();

            // Get the user
            $user = Auth::user();

            return view('order-receipt', compact('order', 'user'));
        } catch (\Exception $e) {
            return redirect()->route('profile.dashboard')->with('error', 'Order not found or you do not have permission to view it.');
        }
    }
}