<?php

namespace App\Http\Controllers;

use App\Http\Resources\MenuCategoryResource;
use App\Models\MenuCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuCategoryController extends Controller
{
    public function store(Request $request){
        $validatedData = $request->validate([
            'name' => 'required',
            'images' => '',
            'describtion' => ''
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

        MenuCategory::create([
            'user_id' => $user->id,
            ...$validatedData
        ]);

        return response()->json([
            'message' => 'Successfully created a new category',
        ],201 );
    }

    public function index(){
        $user = Auth::user();

        $categories = MenuCategory::where('user_id', $user->id)->get();

        return response()->json([
            'message' => 'Successfully retrived data',
            'data' => MenuCategoryResource::collection($categories)
        ]);
    }

    public function update(Request $request, $id){
        $user = Auth::user();

        $category = MenuCategory::where('id', $id)
                                ->where('user_id', $user->id)
                                 ->firstOrFail();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|min:2',
            'image' => 'sometimes|string',
            'describtion' => 'sometimes|string'
        ]);

        
        if (isset($validatedData['name'])) {
            $checkNameExists = MenuCategory::where('user_id', $user->id)
                                            ->where('name', $validatedData['name'])
                                            ->first();

            if($checkNameExists){
                return response()->json([
                    'message' => 'The category name is already taken'
                ],409 );
            }
        }

        if (isset($validatedData['describtion'])) {
            $checkNameExists = MenuCategory::where('user_id', $user->id)
                                            ->where('describtion', $validatedData['describtion'])
                                            ->first();

            if($checkNameExists){
                return response()->json([
                    'message' => 'The category describtion is already taken'
                ],409 );
            }
        }

        $category->update($validatedData);

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
