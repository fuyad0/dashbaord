<?php

namespace App\Http\Controllers\API\Store;

use App\Models\Store;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use App\Models\Availability;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    use AuthorizesRequests, ApiResponse;

    public function index()
    {
        $stores = Store::with([
            'products' => function ($query) {
                $query->where('status', 'Active');
            },
            'offers' => function ($query) {
                $query->where('status', 'Active');
            },
            'availabilities'
        ])->where('status', 'Active')->get();

        if ($stores->count() == 0) {
            return $this->error('No Store found', 200);
        }

        return $this->ok('Store founds', $stores);
    }

    public function show($id)
    {
        $store = Store::with([
            'products' => function ($query) {
                $query->where('status', 'Active')->get();
            },
            'offers' => function ($query) {
                $query->where('status', 'Active')->get();
            },
            'availabilities' => function ($query) {
                $query->select('id', 'stores_id', 'day', 'time_start', 'time_end');
            },

        ])
            ->where('id', $id)
            ->where('status', 'Active')
            ->first();

        if (! $store) {
            return $this->success('Store not found', [], 200);
        }

        $response = [
            'id'          => $store->id,
            'name'        => $store->name,
            'type'        => $store->type,
            'logo'        => $store->logourl,
            'banner'      => $store->bannerurl,
            'address'     => $store->address,
            'phone'       => $store->phone,
            'email'       => $store->email,
            'slug'        => $store->slug,
            'reservation' => $store->reservation,
            'longitude'   => $store->longitude,
            'latitude'    => $store->latitude,
            'details'     => $store->details,
            'slogan'      => $store->slogan,
            'email'       => $store->email,
            'whatsapp'    => $store->whatsapp,
            'website'     => $store->website,
            'facebook'    => $store->facebook,
            'twitter'     => $store->twitter,
            'tiktok'      => $store->tiktok,
            'youtube'     => $store->youtube,
            'date'        => $store->created_at->format('d-m-Y'),
            'status'      => $store->status,
            'availability'      => $store->availabilities,
            'user_name'         => $store->user->first_name . $store->user->last_name,
            'user_email'        => $store->user->email,
            'user_phone'        => $store->user->number,
            'user_online_status' => $store->user->is_online,
        ];

        return $this->ok('Store found', $response);
    }


    public function showWithUser(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->store) {
            return $this->success('No store found for this user', [], 200);
        }

        // Query শুরু
        $query = Store::with('availabilities')
            ->where('users_id', $user->id);

        // If type provided -> filter by type
        if ($request->has('type') && !empty($request->type)) {
            $query->where('type', $request->type);
        }

        $stores = $query->get();

        if ($stores->count() > 0) {
            return $this->success('Store retrieved successfully', $stores, 200);
        }

        return $this->success('No store found', [], 200);
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'users_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:Restaurants,Coffee,Cinemas,Deals',
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:stores,slug',
            'slogan' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'email' => 'required|email',
            'phone' => 'required|string',
            'whatsapp' => 'required|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'details' => 'nullable|string',
            'facebook' => 'nullable|string',
            'instagram' => 'nullable|string',
            'youtube' => 'nullable|string',
            'twitter' => 'nullable|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'reservation' => 'boolean',
            'availabilities' => 'required',
            'availabilities.*.day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'availabilities.*.time_start' => 'required|date_format:H:i',
            'availabilities.*.time_end' => 'required|date_format:H:i',
        ]);

        $availabilities = $request->input('availabilities');
        if (is_string($availabilities)) {
            $availabilities = json_decode($availabilities, true);
        }

        DB::beginTransaction();

        try {
            $slug = Str::slug($validated['slug']);
            $count = Store::where('slug', $slug)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }

            // Logo
            $logoPath = $store->logo ?? null;
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $logoName = Str::slug($validated['name'] ?? 'store') . '-logo-' . time() . '.' . $request->file('logo')->extension();
                $logoPath = $request->file('logo')->storeAs('public/stores', $logoName);
                $logoPath = Str::replaceFirst('public/', '', $logoPath);
            }

            // Banner
            $bannerPath = $store->banner ?? null;
            if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                $bannerName = Str::slug($validated['name'] ?? 'store') . '-banner-' . time() . '.' . $request->file('banner')->extension();
                $bannerPath = $request->file('banner')->storeAs('public/stores', $bannerName);
                $bannerPath = Str::replaceFirst('public/', '', $bannerPath);
            }


            $store = Store::create([
                'users_id' => $validated['users_id'],
                'type' => $validated['type'],
                'name' => $validated['name'],
                'slug' => $slug,
                'slogan' => $validated['slogan'] ?? null,
                'logo' => $logoPath,
                'banner' => $bannerPath,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'whatsapp' => $validated['whatsapp'],
                'website' => $validated['website'] ?? null,
                'address' => $validated['address'] ?? null,
                'details' => $validated['details'] ?? null,
                'facebook' => $validated['facebook'] ?? null,
                'twitter' => $validated['twitter'] ?? null,
                'youtube' => $validated['youtube'] ?? null,
                'tiktok' => $validated['tiktok'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'latitude' => $validated['latitude'] ?? null,
                'reservation' => $validated['reservation'] ?? false,
                'status' => 'Pending',
            ]);

            $createdAvailabilities = [];
            foreach ($availabilities as $availability) {
                $createdAvailabilities[] = Availability::create([
                    'stores_id' => $store->id,
                    'day' => $availability['day'],
                    'time_start' => $availability['time_start'],
                    'time_end' => $availability['time_end'],
                ]);
            }

            DB::commit();

            return $this->success('Store created successfully', [
                'store' => $store,
                'availabilities' => $createdAvailabilities,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Store creation failed: ' . $e->getMessage(), 500);
        }
    }


    public function update(Request $request, $id)
    {
        $store = Store::with('availabilities')->find($id);
        if (!$store) {
            return $this->error('Store not found', 200);
        }
        $this->authorize('update', $store);

        $validated = $request->validate([
            'users_id' => 'required|integer|exists:users,id',
            'type' => 'required|in:Restaurants,Coffee,Cinemas,Deals',
            'name' => 'required|string|max:255',
            'slug' => 'required',
            'string',
            'max:255',
            'slogan' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'email' => 'required|email',
            'phone' => 'required|string',
            'whatsapp' => 'required|string',
            'website' => 'nullable|url',
            'address' => 'nullable|string',
            'details' => 'nullable|string',
            'facebook' => 'nullable|string',
            'tiktok' => 'nullable|string',
            'youtube' => 'nullable|string',
            'twitter' => 'nullable|string',
            'longitude' => 'nullable|string',
            'latitude' => 'nullable|string',
            'reservation' => 'boolean',
            'availabilities' => 'required',
            'availabilities.*.day' => 'required|in:Saturday,Sunday,Monday,Tuesday,Wednesday,Thursday,Friday',
            'availabilities.*.time_start' => 'required|date_format:H:i',
            'availabilities.*.time_end' => 'required|date_format:H:i',
        ]);

        $availabilities = $validated['availabilities'];

        if (is_string($availabilities)) {
            $availabilities = json_decode($availabilities, true);
        }

        if (!is_array($availabilities)) {
            return $this->error('Invalid availabilities format', 422);
        }

        DB::beginTransaction();

        try {
            $slug = Str::slug($validated['slug']);
            $count = Store::where('slug', $slug)->where('id', '!=', $store->id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $validated['slug'] = $slug;

            // Upload Logo
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {

                $logoName = Str::slug($validated['name'] ?? 'store')
                    . '-logo-'
                    . time()
                    . '.'
                    . $request->file('logo')->extension();

                $logoPath = $request->file('logo')->storeAs('uploads/stores', $logoName);

                // Save to DB without public/
                $validated['logo'] = str_replace('public/', '', $logoPath);

                // Delete old file
                if ($store->logo && Storage::exists('public/' . $store->logo)) {
                    Storage::delete('public/' . $store->logo);
                }
            } else {
                $validated['logo'] = $store->logo;
            }


            // Upload Banner
            if ($request->hasFile('banner') && $request->file('banner')->isValid()) {

                $bannerName = Str::slug($validated['name'] ?? 'store')
                    . '-banner-'
                    . time()
                    . '.'
                    . $request->file('banner')->extension();

                $bannerPath = $request->file('banner')->storeAs('uploads/stores', $bannerName);

                $validated['banner'] = str_replace('public/', '', $bannerPath);

                if ($store->banner && Storage::exists('public/' . $store->banner)) {
                    Storage::delete('public/' . $store->banner);
                }
            } else {
                $validated['banner'] = $store->banner;
            }


            $store->update($validated);

            $store->availabilities()->delete();
            $createdAvailabilities = [];
            foreach ($availabilities as $av) {
                $createdAvailabilities[] = $store->availabilities()->create([
                    'day' => $av['day'],
                    'time_start' => $av['time_start'],
                    'time_end' => $av['time_end'],
                ]);
            }

            DB::commit();

            return $this->success('Store updated successfully', [
                'store' => $store,
                'availabilities' => $createdAvailabilities
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Store update failed: ' . $e->getMessage(), 500);
        }
    }


    public function filter(Request $request)
    {
        if (empty($request->all())) {
            return $this->success('Data not found', [], 200);
        }

        if (collect($request->all())->every(fn($value) => $value === null || $value === '')) {
            return $this->success('Empty Filter Data', [], 200);
        }


        $query = Store::with(['products.categories', 'offers', 'availabilities'])
            ->where('status', 'Active');


        if ($request->filled('operation')) {
            $query->where('type', $request->input('operation'));
        }
        if ($request->filled('reservation')) {
            $query->where('reservation', $request->input('reservation'));
        }
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('type', 'LIKE', "%{$keyword}%")
                    ->orWhere('address', 'LIKE', "%{$keyword}%");

                $q->orWhereHas('products', function ($p) use ($keyword) {
                    $p->where('name', 'LIKE', "%{$keyword}%")
                        ->orWhere('offer_type', 'LIKE', "%{$keyword}%")
                        ->orWhereHas('categories', function ($c) use ($keyword) {
                            $c->where('category', 'LIKE', "%{$keyword}%");
                        });
                });
            });
        }

        if ($request->boolean('nearMe') && $request->filled(['latitude', 'longitude'])) {
            $lat = $request->latitude;
            $lng = $request->longitude;
            $radius = 20;

            $nearQuery = clone $query;

            $nearStores = $nearQuery->select('stores.*')
                ->selectRaw(
                    "(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance_km",
                    [$lat, $lng, $lat]
                )
                ->having('distance_km', '<=', $radius)
                ->orderBy('distance_km')
                ->get();

            if ($nearStores->isNotEmpty()) {
                $stores = $nearStores;
            } else {
                $stores = $query->select('stores.*')->orderBy('name')->get();
            }
        } else {
            $stores = $query->select('stores.*')->orderBy('name')->get();
        }

        // Execute query
        $stores = $query->get();

        if ($stores->isEmpty()) {
            return $this->success('Data not found', [], 200);
        }

        $products = $stores->flatMap(function ($store) use ($request) {
            $filteredProducts = $store->products->filter(function ($product) use ($request) {
                if (
                    $request->filled('product_category') &&
                    !$product->categories->pluck('category')->contains($request->product_category)
                ) {
                    return false;
                }
                if (
                    $request->filled('offer') &&
                    !str_contains($product->offer_type ?? '', $request->offer) &&
                    !str_contains($product->offer_des ?? '', $request->offer)
                ) {
                    return false;
                }
                return true;
            });

            if ($store->type === 'Cinemas') {
                return collect([[
                    'store_id'        => $store->id,
                    'store_type'        => $store->type,
                    'store_name'        => $store->name,
                    'store_logo'        => $store->logourl,
                    'store_banner'        => $store->bannerurl,
                    'store_address'     => $store->address,
                    'store_phone'       => $store->phone,
                    'store_reservation' => $store->reservation,
                    'availability'      => $store->availabilities ?? [],
                ]]);
            }

            return $filteredProducts->map(function ($product) use ($store) {
                return [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_price' => $product->price ?? null,
                    'product_photo' => $product->photourl ?? null,
                    'product_offer' => $product->offer_type ?? null,
                    'category'      => $product->categories->pluck('category')->toArray(),
                    'status'        => $product->status ?? null,
                    'store_id'      => $store->id,
                    'store_type'    => $store->type,
                    'store_name'    => $store->name,
                    'store_address' => $store->address,
                    'store_phone'   => $store->phone ?? null,
                    'store_reservation'=> $store->reservation,
                    'availabilities' => $store->availabilities,
                ];
            });
        })->values();

        // 🔹 If no products matched any store
        if ($products->isEmpty()) {
            return $this->success('Data not found', [], 200);
        }

        return $this->success('Stores and products retrieved successfully', $products, 200);
    }



    public function nearestStore(Request $request)
    {
        $user_lat = $request->input('latitude');
        $user_lng = $request->input('longitude');
        $type = $request->input('type');

        if (!$type) {
            return $this->success('Type is required', [], 200);
        }

        $radius = 10;
        $minStores = 8;

        $baseQuery = Store::select('id', 'name', 'address', 'phone', 'reservation', 'status', 'latitude', 'longitude', 'type', 'logo', 'banner')
            ->selectRaw(
                "(6371 * acos(
                cos(radians(?)) 
                * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) 
                + sin(radians(?)) 
                * sin(radians(latitude))
            )) AS distance_km",
                [$user_lat, $user_lng, $user_lat]
            )
            ->with([
                'products' => function ($q) {
                    $q->where('status', 'Active')->with(['categories']);
                },
                'availabilities'
            ])
            ->where('status', 'Active')
            ->where('type', $type); // <-- only match the type

        // Stores within radius
        $stores = (clone $baseQuery)
            ->having('distance_km', '<=', $radius)
            ->orderBy('distance_km')
            ->get();

        // If less than minimum, add nearest ones of same type
        if ($stores->count() < $minStores) {
            $extraNeeded = $minStores - $stores->count();

            $extraStores = (clone $baseQuery)
                ->orderBy('distance_km')
                ->limit($extraNeeded)
                ->get();

            $stores = $stores->merge($extraStores)->unique('id')->values();
        }

        if ($stores->isEmpty()) {
            return $this->success('No stores found for this type', [], 200);
        }

        $products = $stores->flatMap(function ($store) {
            if ($store->type === 'Cinemas') {
                return collect([[
                    'store_id'          => $store->id,
                    'store_type'        => $store->type,
                    'store_name'        => $store->name,
                    'store_logo'        => $store->logourl,
                    'store_banner'      => $store->bannerurl,
                    'store_address'     => $store->address,
                    'store_phone'       => $store->phone,
                    'store_reservation' => $store->reservation,
                    'availability'      => $store->availabilities ?? [],
                ]]);
            }
            return $store->products->map(function ($product) use ($store) {
                return [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_price' => $product->price ?? null,
                    'product_photo' => $product->photourl ?? null,
                    'product_offer' => $product->offer_type ?? null,
                    'category'      => $product->categories->pluck('category')->toArray(),
                    'status'        => $product->status,
                    'store_id'        => $store->id,
                    'store_type'         => $store->type,
                    'store_name'         => $store->name,
                    'store_address' => $store->address,
                    'store_phone'   => $store->phone ?? null,
                    'store_reservation'   => $store->reservation,
                    'distance_km'   => round($store->distance_km, 2),
                    'availabilities' => $store->availabilities,
                ];
            });
        })->values();

        return $this->success('Nearby products retrieved successfully', $products, 200);
    }


    public function search(Request $request)
    {
        // 1️⃣ Validate type parameter
        if (!$request->has('type')) {
            return $this->error('Type parameter is required', 200);
        }

        $type = $request->input('type');

        // 2️⃣ Base query with relations
        $stores = Store::with([
            'products' => function ($q) {
                $q->where('status', 'Active')->with('categories');
            },
            'availabilities'
        ])
            ->where('status', 'Active')
            ->where('type', 'LIKE', "%{$type}%")
            ->get();

        // 3️⃣ Handle empty
        if ($stores->isEmpty()) {
            return $this->success('No Data found', [], 200);
        }

        // 4️⃣ Flatten products with store info inside
        $products = $stores->flatMap(function ($store) {
            if ($store->type === 'Cinemas') {
                return collect([[
                    'store_id'        => $store->id,
                    'store_type'        => $store->type,
                    'store_name'        => $store->name,
                    'store_logo'        => $store->logourl,
                    'store_banner'        => $store->bannerurl,
                    'store_address'     => $store->address,
                    'store_phone'       => $store->phone,
                    'store_reservation' => $store->reservation,
                    'availability'      => $store->availabilities ?? [],
                ]]);
            }
            return $store->products->map(function ($product) use ($store) {
                return [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_price' => $product->price ?? null,
                    'product_photo' => $product->photourl ?? null,
                    'product_offer' => $product->offer_type ?? null,
                    'category'      => $product->categories->pluck('category')->toArray(),
                    'status'        => $product->status,
                    'store_id'        => $store->id,
                    'store_type'    => $store->type,
                    'store_name'    => $store->name,
                    'store_address' => $store->address,
                    'store_phone'   => $store->phone,
                    'store_reservation'  => $store->reservation,
                    'availability' => $store->availabilities ?? [],
                ];
            });
        })->values();

        // 5️⃣ Return response
        return $this->success('Products with store info retrieved successfully', $products, 200);
    }


    public function destroy($id)
    {
        $store = Store::find($id);
        if (! $store) {
            return response()->json([
                'message' => 'Store not found',
            ], 404);
        }
        $this->authorize('delete', $store);
        $store->delete();

        return $this->success('Store deleted successfully', $store, 200);
    }

    public function updateStatus($id)
    {
        $store = Store::find($id);

        if (! $store) {
            return response()->json(['message' => 'Store not found'], 200);
        }

        $store->status = 'Active';
        $store->save();

        return $this->success('Store status updated successfully', $store, 200);
    }


    public function checkSlug(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $slug = Str::slug($request->slug);

        $count = Store::where('slug', $slug)->count();
        if ($count > 0) {
            return $this->error('Slug already exists', 422);
        }
        return $this->success('Slug is available', $slug, 200);
    }
}
