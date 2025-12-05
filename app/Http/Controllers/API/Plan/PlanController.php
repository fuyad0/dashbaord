<?php

namespace App\Http\Controllers\API\Plan;

use App\Models\Plan;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PlanController extends Controller
{
    use ApiResponse;

    public function index()
    {
        $plans = Plan::published()->with('planOptions')->get();

        return $this->success('Plans fetch successfully.', $plans, 200);
    }

    public function show($id)
    {
        $data = Plan::published()->with('planOptions')->find($id);

        if (!$data) {
           return $this->success('No Data found', [], 200);
        }

        return $this->success('Data fetch successfully.', $data, 200);
    }
}
