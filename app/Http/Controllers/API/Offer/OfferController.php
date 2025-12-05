<?php

namespace App\Http\Controllers\API\Offer;

use App\Models\Offer;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OfferController extends Controller
{

    use AuthorizesRequests, ApiResponse;

    public function index($offer_type = null)
    {
        $offers = Offer::query()
            ->when($offer_type, fn($q) => $q->where('offer_type', $offer_type))
            ->with([
                'store:id,name,type,address,phone,reservation',
                'store.products' => fn($q) => $q->where('status', 'Active')
                    ->select('id', 'stores_id', 'name', 'tags', 'price', 'photo', 'offer_type', 'status'),
                'store.products.categories:id,products_id,category',
                'store.availabilities' // if you have availability relationship
            ])
            ->get();

        // Transform the data
        $offer = $offers->flatMap(function ($offer) {
            $store = $offer->store;

            return $store->products->map(function ($product) use ($store) {
                return [
                    'product_id'    => $product->id,
                    'product_name'  => $product->name,
                    'product_price' => $product->price ?? null,
                    'product_photo' => $product->photo ?? null,
                    'product_offer' => $product->offer_type ?? null,
                    'product_offer_des' => $product->offer_des ?? null,
                    'category'      => $product->categories->pluck('category')->toArray(),
                    'status'        => $product->status,
                    'store_name'         => $store->name,
                    'store_type'         => $store->type,
                    'store_address'      => $store->address,
                    'store_phone'        => $store->phone,
                    'store_reservation'  => $store->reservation,
                    'availability'       => $store->availabilities ?? [],
                ];
            });
        });


        if ($offer->isEmpty()) {
            return $this->success('No Data found', [], 200);
        }

        return $this->ok('Offers retrieved successfully', $offer);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'stores_id' => 'required|exists:stores,id',
            'offer_type' => 'required|string|max:255',
            'offer_des' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);
        $offer = Offer::create($validated);
        return $this->success('Offer created successfully', $offer, 200);
    }

    public function show(Offer $offer)
    {
        return $this->success('Found', $offer, 200);
    }

    public function update(Request $request, Offer $offer)
    {
        $this->authorize('update', $offer);
        $validated = $request->validate([
            'stores_id' => 'required|exists:stores,id',
            'offer_type' => 'required|string|max:255',
            'offer_des' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
        ]);
        $offer->update($validated);
        return $this->success('Offer updated successfully', $offer, 200);
    }

    public function destroy(Offer $offer)
    {
        $this->authorize('delete', $offer);
        $offer->delete();
        return $this->success('Offer deleted successfully', $offer, 200);
    }
}
