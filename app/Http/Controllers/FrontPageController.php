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
        
        // Sample pizza data with consistent naming
        $pizzaItems = [
            [
                "name" => "Pepporoni Pizza",
                "price" => "₱150",
                "img" => "pepporoni pizza.png",
                "class" => "pep",
                "desc" => "Zesty pepperoni slices, tangy tomato sauce, melted mozzarella cheese, and a sprinkle of oregano on a golden crust."
            ],
            [ 
                "name" => "Cheese Pizza", 
                "price" => "₱150", 
                "img" => "Cheese pizza.png", 
                "class" => "cheese", 
                "desc" => "Classic cheese pizza with our signature tomato sauce and a blend of premium mozzarella and cheddar cheeses." 
            ],
            [ 
                "name" => "Hawaiian Pizza", 
                "price" => "₱150", 
                "img" => "hawaian pizza.png", 
                "class" => "hawaian", 
                "desc" => "Sweet pineapple chunks, savory ham, tomato sauce, and melted mozzarella cheese on our hand-tossed crust." 
            ],
            [ 
                "name" => "Meat Pizza", 
                "price" => "₱150", 
                "img" => "meaty pizza.png", 
                "class" => "meat", 
                "desc" => "Loaded with pepperoni, Italian sausage, bacon, and ground beef on our signature sauce and cheese blend." 
            ],
            [ 
                "name" => "Overload Cheese Pizza", // Changed from "Cheesy Pizza" to "Overload Cheese Pizza"
                "price" => "₱150", 
                "img" => "overload cheese pizza.png", 
                "class" => "over", 
                "desc" => "Five premium cheeses - mozzarella, cheddar, provolone, parmesan, and ricotta - melted to perfection." 
            ]
        ];

        // Ensure all image paths are valid
        foreach ($pizzaItems as &$pizza) {
            $imagePath = 'images/' . $pizza['img'];
            if (!file_exists(public_path($imagePath))) {
                // If image doesn't exist, use placeholder
                $pizza['img'] = 'pizza-placeholder.png';
            }
        }

        $menuItems = [
            [ "name" => "Pepporoni Pizza", "class" => "pep" ],
            [ "name" => "Cheese Pizza", "class" => "cheese" ],
            [ "name" => "Hawaiian Pizza", "class" => "hawaian" ],
            [ "name" => "Meat Pizza", "class" => "meat" ],
            [ "name" => "Overload Cheese Pizza", "class" => "over" ], // Changed from "Overload Cheese Pizza" to match
        ];

        return view('frontpage', [
            'user' => $user,
            'pizzaItems' => $pizzaItems,
            'menuItems' => $menuItems
        ]);
    }
}