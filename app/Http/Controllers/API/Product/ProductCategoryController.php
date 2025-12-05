<?php

namespace App\Http\Controllers\API\Product;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $categories = ProductCategory::groupBy('category')
            ->pluck('category');

        return $this->success('Data Found', $categories, 200);
    }


    public function store(Request $request){
        $validated= $request->validate([
            'products_id'=> 'required|integer |exists:products,id',
            'category'=> 'required|string|max:255',
        ]);

        $productCategory= ProductCategory::create([
            'products_id'=> $validated['products_id'],
            'category'=> $validated['category'],
        ]);

        if($productCategory){
            return $this->success('Data Found', $productCategory, 200);
        } else {
            return response()->json([
                'message'=> 'Failed to create store category'
            ], 500);
        }
    }

    public function update(Request $request, ProductCategory $productCategory){
        $validated= $request->validate([
            'products_id'=> 'required|integer |exists:products,id',
            'category'=> 'sometimes|string|max:255',
        ]);

        $productCategory->update($validated);

        return response()->json([
            'message'=> 'Product category updated successfully',
            'storeCategory'=> $productCategory
        ], 200);
    }

    public function destroy(ProductCategory $productCategory){
        $productCategory->delete();

        return $this->success('Data deleted successfully', $productCategory, 200);
    }
}
