<?php

namespace App\Http\Controllers\API\DashboardView;

use App\Models\Offer;
use App\Models\Store;
use App\Models\Review;
use App\Models\Product;
use App\Models\UserDetails;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardOverviewController extends Controller
{
    use ApiResponse;

    public function index()
    {
        
        $data = [
            'Total Products' => Product::count(),
            'Active Products' => Product::where('status', 'Active')->count(),
            'Inactive Products' => Product::where('status', 'Inactive')->count(),
            'Expired Offer' => Offer::where('status', 'Inactive')->count(),
        ];

        return $this->ok('success', $data);
    }
}
