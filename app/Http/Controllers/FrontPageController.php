<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FrontPageController 
{
    public function index()
    {
        // Get authenticated user or null if guest
        $user = Auth::user();
        
        // Sample pizza data - you can move this to a model later
        $pizzaItems = [
            [
                "name" => "Pepporoni Pizza",
                "price" => "₱150",
                "img" => "pepporoni pizza.png",
                "class" => "pep",
                "desc" => "Zesty pepperoni slices, tangy tomato sauce, melted mozzarella cheese, and a sprinkle of oregano on a golden crust."
            ],
            // ... your other pizza items
        ];

        $menuItems = [
            [ "name" => "Pepporoni Pizza", "class" => "pep" ],
            // ... your other menu items
        ];

        return view('frontpage', [
            'user' => $user,
            'pizzaItems' => $pizzaItems,
            'menuItems' => $menuItems
        ]);
    }
}