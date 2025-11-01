<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuItemResource;
use App\Http\Resources\PublicCategoryResource;
use App\Http\Resources\PublicItemResource;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'item_name' => 'required|min:2',
            'price' => 'required|numeric',
            'tax_percentage' => 'required|numeric|min:0.1|max:100',
            'photo' => 'nullable|string'
        ], [
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

        if (!empty($validatedData['photo'])) {
            $imageData = $validatedData['photo'];

            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
                $extension = strtolower($type[1]);

                $filename = 'item' . Str::random(10) . '.' . $extension;
                $path = 'images/MenuItem/' . $filename;

                Storage::disk('public')->put($path, $imageData);

                $validatedData['photo'] = 'storage/' . $path;
            } else {
                return response()->json([
                    'message' => 'Invalid image format'
                ], 422);
            }
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

    public function index(Request $request){
        $user = Auth::user();

        $search = $request->query('search');
        $categoryIds = $request->query('categories', []);
        $perPage = $request->query('per_page', 10);
        $categories = $request->query('categories');

        $query = MenuItem::whereHas('category', function ($q) use ($user, $categoryIds) {
            $q->where('user_id', $user->id);

            if(!empty($categoryIds)){
                $q->whereIn('id', $categoryIds);
            }
        });

        if($search){
            $query->where('item_name', 'like', '%' . $search . '%');
        }

        $items = $query->with('category')->paginate($perPage);

        $categories = MenuCategory::where('user_id', $user->id)->get(['id', 'name']);
    
        return response()->json([
            'message' => 'Successfully retrieved menu items',
            'data' => MenuItemResource::collection($items),
            'categories' => $categories,
            'meta' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $items->perPage(),
                'total' => $items->total()
            ]
        ],200 );
    }

    public function update(Request $request, $id){

        $item = MenuItem::findOrFail($id);

        $validatedData = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'item_name' => 'required|min:2',
            'price' => 'required|numeric',
            'tax_percentage' => 'required|numeric|min:0.1|max:100',
            'photo' => 'nullable|string'
        ]);

        $user = Auth::user();

        $validItem = MenuCategory::where('id', $validatedData['category_id'])
                                ->where('user_id', $user->id)
                                ->first();

        if (!$validItem) {
            return response()->json([
                'message' => 'You are not authorized to add items to this category'
            ], 403);
        }

        if (!empty($validatedData['photo'])) {
            $photoData = $validatedData['photo'];

            if (Str::startsWith($photoData, 'data:image')) {
                if (preg_match('/^data:image\/(\w+);base64,/', $photoData, $type)) {
                    $photoData = substr($photoData, strpos($photoData, ',') + 1);
                    $photoData = base64_decode($photoData);
                    $extension = strtolower($type[1]);

                    $filename = 'category_' . Str::random(10) . '.' . $extension;
                    $path = 'images/MenuItem/' . $filename;

                    Storage::disk('public')->put($path, $photoData);

                    $validatedData['photo'] = 'storage/' . $path;
                } else {
                    return response()->json([
                        'message' => 'Invalid photo format'
                    ], 422);
                }
            }
        }

        $item->update($validatedData);

        return response()->json([
            'message' => 'Item updated successfully'
        ], 200);

    }

    public function destroy($id){
        $user = Auth::user();

        $item = MenuItem::with('category')->findOrFail($id);

        if($item->category->user_id !== $user->id){
            return response()->json([
                'message' => 'You are not authorized to delete this item'
            ], 403);
        }

        $item->delete();

        return response()->json([
            'message' => 'Item deleted successfully'
        ], 200);
    }

    public function publicMenu(Request $request, $slug){
        $user = User::where('slug', $slug)->firstOrFail();

        $categories = MenuCategory::where('user_id', $user->id)->get();

        $query = MenuItem::whereIn('category_id', $categories->pluck('id'));

        if ($request->has('categories')) {
            $query->whereIn('category_id', $request->categories);
        }

        if ($request->has('search')) {
            $query->where('item_name', 'like', "%{$request->search}%");
        }

        $items = $query->get();

        return response()->json([
            'business_name' => $user->business_name,
            'categories' => PublicCategoryResource::collection($categories),
            'items' => PublicItemResource::collection($items),
        ]);
    }
}
