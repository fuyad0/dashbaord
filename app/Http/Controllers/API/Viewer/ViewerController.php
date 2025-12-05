<?php

namespace App\Http\Controllers\API\Viewer;

use App\Models\Viewer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class ViewerController extends Controller
{
    use ApiResponse;
    public function index(){
        $viwers = Viewer::get();
        return $this->success('Data Found', $viwers, 200);
    }

    public function store(Request $request){
        $validated = $request->validate([
        'products_id' => 'required|exists:products,id',
        'visitors' => 'nullable|string|max:255',
    ]);

    $visitorIp = $validated['visitors'] ?? $request->ip();
    $alreadyViewed = Viewer::where('products_id', $validated['products_id'])
        ->where('visitors', $visitorIp)
        ->exists();

    if ($alreadyViewed) {
        return $this->success('Viewer already exists for this product', null, 200);
    }
    $viewer = Viewer::create([
        'products_id' => $validated['products_id'],
        'visitors' => $visitorIp,
    ]);

    return $this->success('Viewer created successfully', $viewer, 200);
}

    public function totalView($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->success('No Data found', [], 200);
        }
        $totalViews = Viewer::where('products_id', $id)->count();

        return $this->success('Total views found '. $totalViews, ['product_id' => $id,'total_views' => $totalViews], 200);
    }

}
