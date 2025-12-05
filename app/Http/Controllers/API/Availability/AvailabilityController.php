<?php

namespace App\Http\Controllers\API\Availability;

use App\Models\Availability;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AvailabilityController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $days = Availability::pluck('day'); 
        return $this->success('Availability found', $days, 200);
    }


    public function store(Request $request){
        $validated= $request->validate([
            'stores_id' => 'required|exists:stores,id',
            'day' => 'required|string',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i',
        ]);
        $availability = Availability::create($validated);
        return $this->success('Availability created successfully', $availability, 200);
    }

    public function destroy($id){
        $availability = Availability::find($id);
        if(!$availability){
           return $this->success('No Data found', [], 200);
        }
        $availability->delete();
         return $this->ok('Availability created successfully');
    }
}
