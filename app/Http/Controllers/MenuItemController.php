<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuItemResource;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuItemController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'item_name' => 'required|min:2',
            'price' => 'required|numeric',
            'tax_percentage' => 'required|numeric|min:0.1|max:100',
            'photo' => 'sometimes|string'
        ]);

        $user = Auth::user();

        $category = MenuCategory::where('id', $validatedData['category_id'])
                                ->where('user_id', $user->id)
                                ->first();

        if (!$category) {
            return response()->json([
                'message' => 'You are not authorized to add items to this category'
            ], 403);
        }

        MenuItem::create([
            'category_id' => $validatedData['category_id'],
            'item_name' => $validatedData['item_name'],
            'price' => $validatedData['price'],
            'tax_percentage' => $validatedData['tax_percentage'],
            'photo' => $validatedData['photo'] ?? null
        ]);

        return response()->json([
            'message' => 'Menu item created successfully',
        ], 201);
    }

    public function index(){
         $user = Auth::user();

        $items = MenuItem::whereHas('category', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();

        return response()->json([
            'message' => 'Successfully retrieved menu items',
            'data' => MenuItemResource::collection($items)
        ],200 );
    }
}
