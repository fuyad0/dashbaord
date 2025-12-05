<?php

namespace App\Http\Controllers\API\Enquiry;

use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Enquiry;

class EnquiryController extends Controller
{
    use ApiResponse;
    public function index(){
        $enquiry = Enquiry::all();
        if($enquiry->count() > 0){
            return $this->ok("data found", $enquiry);
        }else{
            return $this->success('No Data found', [], 200);
        }
    }

   public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "required|email",
            "phone" => "required",
            "membership_number" => "required",
            "redemption_code" => "nullable",
            "subject" => "required",
            "reason" => "required",
            "description" => "required",
        ]);

        $enquiry = Enquiry::create([
            "name" => $request->name,
            "email" => $request->email,
            "phone" => $request->phone,
            "membership_id" => $request->membership_number,
            "redemption_code" => $request->redemption_code,
            "subject" => $request->subject,
            "description" => $request->description,
            "reason" => $request->reason,
        ]);

        return $this->success("Your enquiry has been submitted", $enquiry, 200);
    }


}
