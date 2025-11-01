<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategoryResource;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MenuCategoryController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required',
            'image' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        $user = Auth::user();

        $checkNameExists = MenuCategory::where('user_id', $user->id)
                                        ->where('name', $validatedData['name'])
                                        ->first();

        if($checkNameExists){
            return response()->json([
                'message' => 'The food category name is already taken'
            ],422 );
        }

        if (!empty($validatedData['image'])) {
            $imageData = $validatedData['image'];

            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $imageData = base64_decode($imageData);
                $extension = strtolower($type[1]);

                $filename = 'category_' . Str::random(10) . '.' . $extension;
                $path = 'images/MenuCategory/' . $filename;

                Storage::disk('public')->put($path, $imageData);

                $validatedData['image'] = 'storage/' . $path;
            } else {
                return response()->json([
                    'message' => 'Invalid image format'
                ], 422);
            }
        }

        $validatedData['user_id'] = $user->id;

        MenuCategory::create($validatedData);

        return response()->json([
            'message' => 'Successfully created a new category',
        ],201 );
    }

    public function index(Request $request){
        $user = Auth::user();

        $search = $request->query('search');
        $perPage = $request->query('per_page', 10);

        $query = MenuCategory::where('user_id', $user->id);

        if($search){
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($perPage);

        return response()->json([
            'message' => 'Successfully retrived menu categories',
            'data' => MenuCategoryResource::collection($categories),
            'meta' => [
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total()
            ]
        ]);
    }

    public function update(Request $request, $id){
        $user = Auth::user();

        $category = MenuCategory::where('id', $id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();

        $validatedData = $request->validate([
            'name' => 'required|min:2|string',
            'image' => 'nullable',
            'description' => 'nullable|string'
        ]);
        
        if($validatedData['name']) {
            $checkNameExists = MenuCategory::where('user_id', $user->id)
                                            ->where('name', $validatedData['name'])
                                            ->first();

            if(!$checkNameExists){
                $category->update([
                    'name' => $validatedData['name']
                ]);
            }

        }

        if (isset($validatedData['description'])) {
            $checkNameExists = MenuCategory::where('user_id', $user->id)
                                            ->where('description', $validatedData['description'])
                                            ->first();

            if(!$checkNameExists){
                $category->update([
                    'description' => $validatedData['description']
                ]);
            }

        }

        if (!empty($validatedData['image'])) {
            $imageData = $validatedData['image'];

            if (Str::startsWith($imageData, 'data:image')) {
                if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                    $imageData = substr($imageData, strpos($imageData, ',') + 1);
                    $imageData = base64_decode($imageData);
                    $extension = strtolower($type[1]);

                    $filename = 'category_' . Str::random(10) . '.' . $extension;
                    $path = 'images/MenuCategory/' . $filename;

                    Storage::disk('public')->put($path, $imageData);

                    $validatedData['image'] = 'storage/' . $path;
                } else {
                    return response()->json([
                        'message' => 'Invalid image format'
                    ], 422);
                }
            }

            $category->update([
                'image' => $validatedData['image']
            ]);
        }

        return response()->json([
            'message' => 'Category updated successfully'
        ], 200);
    }

    public function destroy($id){
        $user = Auth::user();

        $category = MenuCategory::where('id', $id)
                                ->where('user_id', $user->id)
                                ->firstOrFail();

        $category->delete();

        return response()->json([
            'message' => 'Successfully delete category'
        ], 200);
        
    }
}
