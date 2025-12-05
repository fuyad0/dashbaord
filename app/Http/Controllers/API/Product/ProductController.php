<?php

namespace App\Http\Controllers\API\Product;

use App\Models\Store;
use App\Models\Viewer;
use App\Models\Product;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Models\ProductCategory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProductController extends Controller
{
    use AuthorizesRequests, ApiResponse;
    public function index()
    {
        $products = Product::with(['categories'])->where('status', 'Active')
            ->whereHas('store', function ($q) {
                $q->where('status', 'Active');
            })->get();
        if ($products->isEmpty()) {
            return $this->error('No Data Available', 200);
        }
        return $this->success('Products retrieved successfully', $products, 200);
    }

    public function showWithUser(Request $request)
    {
        if($request->has('status')){
            $status= $request->status;
        }else{
            $status= 'Active';
        }
        $user = Auth::user();

        if (!$user || $user->store->isEmpty()) {
            return $this->success('No store found for this user', [], 200);
        }

        $allProducts = collect();

        foreach ($user->store as $store) {
            $products = Product::with(['categories', 'viewers'])
                ->where('stores_id', $store->id)
                ->where('status', $status)
                ->withCount('viewers')
                ->get()
                ->map(function ($product) use ($store) {
                    return [
                        'product_id'        => $product->id,
                        'product_name'      => $product->name,
                        'product_price'     => $product->price ?? null,
                        'product_photo'     => $product->photourl ?? null,
                        'product_offer'     => $product->offer_type ?? null,
                        'category'          => $product->categories->pluck('category')->toArray(),
                        'status'            => $product->status,
                        'store_type'        => $store->type,
                        'store_name'        => $store->name,
                        'store_logo'        => $store->logo,
                        'store_address'     => $store->address,
                        'store_phone'       => $store->phone ?? null,
                        'store_reservation' => $store->reservation,
                        'availabilities'    => $store->availabilities,
                        'total_views'   => $product->viewers_count,
                    ];
                });

            $allProducts = $allProducts->merge($products);
        }

        return $this->success('Products retrieved successfully', $allProducts->values(), 200);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'stores_id' => 'required|integer|exists:stores,id',
            'tags'      => 'nullable|array',
            'tags.*'    => 'string|max:50',
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:50',
            'price'     => 'nullable|string',
            'offer_type'     => 'nullable|string',
            'offer_des'     => 'nullable|string',
            'status'    => 'nullable|string|in:Active,Inactive',
        ]);

        $store = Store::find($validated['stores_id']);
        if ($store->status !== 'Active') {
            return $this->error('Cannot add product to an inactive store.', 403);
        }

        $photoPath = null;
        if ($request->hasFile('image')) {
            $photo = $request->file('image');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('uploads/products', $photoName, 'public');
        }

        $product = Product::create([
            'stores_id' => $validated['stores_id'],
            'tags'      => $validated['tags'] ?? null,
            'name'      => $validated['name'],
            'photo'     => $photoPath,
            'price'     => $validated['price'] ?? null,
            'offer_type'     => $validated['offer_type'] ?? null,
            'offer_des'     => $validated['offer_des'] ?? null,
            'status'    => $validated['status'] ?? 'Active',
        ]);

        if (!empty($validated['categories'])) {
            foreach ($validated['categories'] as $categoryName) {
                ProductCategory::create([
                    'products_id' => $product->id,
                    'category' => $categoryName,
                ]);
            }
        }

        if ($product) {
            return $this->success('Product created successfully', $product->load('categories'), 200);
        } else {
            return $this->error('Failed to create product', 500);
        }
    }


    public function show(Request $request, $id)
    {
        $product = Product::with(['categories', 'store', 'store.user', 'store.availabilities'])->find($id);

        if (!$product) {
            return $this->success('Product not found', [],  200);
        }

        $visitorIp = $request->ip();

        // Check if this visitor already viewed
        $alreadyViewed = Viewer::where('products_id', $id)
            ->where('visitors', $visitorIp)
            ->exists();

        if (!$alreadyViewed) {
            Viewer::create([
                'products_id' => $id,
                'visitors' => $visitorIp,
            ]);
        }

        // Reload to include the new viewer
        $product->load(['categories', 'store', 'store.user:id,email,is_online', 'store.availabilities']);

        $data = [
            'product_id'        => $product->id,
            'product_name'      => $product->name,
            'product_price'     => $product->price,
            'product_photo'     => $product->photo,
            'product_offer_type'=> $product->offer_type,
            'product_offer_des' => $product->offer_des,
            'product_photo'     => $product->photourl,
            'product_created_at'=> $product->created_at->format('d-m-Y'),
            'product_category'  => $product->categories,
            'product_status'    => $product->status,
            'store_id'          => $product->store->id,
            'store_name'        => $product->store->name,
            'store_type'        => $product->store->type,
            'store_logo'        => $product->store->logo,
            'store_banner'      => $product->store->banner,
            'store_address'     => $product->store->address,
            'store_phone'       => $product->store->phone,
            'store_email'       => $product->store->email,
            'store_slogan'      => $product->store->slogan,
            'store_reservation' => $product->store->reservation,
            'store_longitude'   => $product->store->longitude,
            'store_latitude'    => $product->store->latitude,
            'store_details'     => $product->store->details,
            'store_slogan'      => $product->store->slogan,
            'store_email'       => $product->store->email,
            'store_whatsapp'    => $product->store->whatsapp,
            'store_website'     => $product->store->website,
            'store_facebook'    => $product->store->facebook,
            'store_twitter'     => $product->store->twitter,
            'store_tiktok'      => $product->store->tiktok,
            'store_youtube'     => $product->store->youtube,
            'store_date'        => $product->store->created_at->format('d-m-Y'),
            'store_status'      => $product->store->status,
            'availability'      => $product->store->availabilities,
            'user_name'         => $product->store->user->first_name.$product->store->user->last_name,
            'user_email'        => $product->store->user->email,
            'user_phone'        => $product->store->user->number,
            'user_online_status'=> $product->store->user->is_online,
            'total_views' => $product->viewers->count(),
        ];

        return $this->success('Success', $data, 200);
    }


    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->error('Product not found', 200);
        }
        $this->authorize('update', $product);

        $validated = $request->validate([
            'stores_id'  => 'required|integer|exists:stores,id',
            'tags'       => 'sometimes|nullable|array',
            'tags.*'     => 'string|max:50',
            'name'       => 'sometimes|string|max:255',
            'photo'      => 'sometimes|nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'string|max:255',
            'price'      => 'nullable|string',
            'offer_type'     => 'nullable|string',
            'offer_des'     => 'nullable|string',
            'status'     => 'nullable|string|in:Active,Inactive',
        ]);

        if ($request->hasFile('photo')) {
            if ($product->photo && Storage::disk('public')->exists($product->photo)) {
                Storage::disk('public')->delete($product->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . $photo->getClientOriginalName();
            $photoPath = $photo->storeAs('uploads/products', $photoName, 'public');

            $validated['photo'] = $photoPath;
        }


        /*if (isset($validated['tags'])) {
            $validated['tags'] = json_encode($validated['tags']);
        }*/

        $product->update($validated);

        if (isset($validated['categories'])) {
            // Remove old categories
            ProductCategory::where('products_id', $product->id)->delete();

            foreach ($validated['categories'] as $categoryName) {
                ProductCategory::create([
                    'products_id' => $product->id,
                    'category' => $categoryName,
                ]);
            }
        }

        return $this->success('Product updated successfully', $product->load('categories'), 200);
    }


    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->success('No Data found', [], 200);
        }
        $this->authorize('delete', $product);

        $product->delete();
        return $this->success('Product deleted successfully', null, 200);
    }

    public function offers(Request $request)
    {
        $query = Product::with(['store', 'categories'])->where('status', 'Active');

        // Optional: offer type filter
        if ($request->has('offer_type')) {
            $offerType = urldecode($request->offer_type);
            $query->where('offer_type', 'like', '%' . $offerType . '%');
        }

        // Optional: store type filter
        if ($request->has('type')) {
            $storeType = $request->query('type');
            $query->whereHas('store', function ($q) use ($storeType) {
                $q->where('type', $storeType);
            });
        }

        $offers = $query->get();

        if ($offers->isEmpty()) {
            return $this->success('Data Not Found', [], 200);
        }

        $offerData = $offers->map(function ($product) {
            $store = $product->store;
            return [
                'product_id'        => $product->id,
                'product_name'      => $product->name,
                'product_price'     => $product->price ?? null,
                'product_photo'     => $product->photourl ?? null,
                'product_offer'     => $product->offer_type ?? null,
                'category'          => $product->categories->pluck('category')->toArray(),
                'status'            => $product->status ?? null,
                'store_type'        => $store->type ?? null,
                'store_name'        => $store->name ?? null,
                'store_address'     => $store->address ?? null,
                'store_phone'       => $store->phone ?? null,
                'store_reservation' => $store->reservation ?? null,
                'availabilities'    => $store->availabilities ?? null,
            ];
        });

        return $this->success('Data Found', $offerData, 200);
    }

}
