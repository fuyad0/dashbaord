<?php

namespace App\Http\Controllers\API\User;

use App\Models\User;
use App\Models\UserDetails;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserDetailController extends Controller
{
    use ApiResponse;
    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'whatsapp' => 'nullable|string',
            'website' => 'nullable|url',
            'state' => 'nullable|string',
            'zip' => 'nullable|string',
            'address' => 'nullable|string',
            'facebook' => 'nullable|string',
            'twitter' => 'nullable|string',
            'youtube' => 'nullable|string',
            'tiktok' => 'nullable|string',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'email' => 'sometimes|email',
            'phone' => 'sometimes|string',
            'dob' => 'sometimes|date',
        ]);

        if(empty($request->user_id)){
            $user_id=Auth::user()->id;
            $validated['user_id']=$user_id;
        }

        $userDetail = UserDetails::updateOrCreate(
            ['users_id' => $validated['user_id']], // condition
            [
                'whatsapp' => $validated['whatsapp'],
                'website' => $validated['website'] ,
                'state' => $validated['state'] ,
                'zip' => $validated['zip'],
                'facebook' => $validated['facebook'],
                'twitter' => $validated['twitter'] ,
                'youtube' => $validated['youtube'],
                'tiktok' => $validated['tiktok'] ,
            ]
        );

        $user = User::find($validated['user_id']);

        $updateData = [
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'number'     => $request->phone,
            'dob'        => $request->dob,
            'address'    => $request->address,
        ];

        $user->update($updateData);


        return $this->success('User Details saved successfully', $userDetail, 200);
    }
}
