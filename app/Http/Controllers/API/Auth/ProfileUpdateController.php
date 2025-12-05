<?php

namespace App\Http\Controllers\API\Auth;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetails;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileUpdateController extends Controller
{
    use ApiResponse;

    public function changePassword(Request $request)
    {
        $validator = $request->validate([
            'old_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

           $user = Auth::user();

            if (!Hash::check($request->old_password, $user->password)) {
                return $this->error('Incorrect Current Password', 402);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            return $this->ok('Password changed successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileUrl(Request $request)
    {
        $validator = $request->validate([
            'url' => 'nullable|string|max:255',
        ]);

        if (!$validator) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

           $user = Auth::user();

            $url = $request->url;

            if ($user->url == $url) {
                return $this->error('Profile url cannot be same as your current profile url');
            }

            $exist_url = User::where('url', $url)->exists();

            if ($exist_url) {
                return $this->error('Profile url already exists');
            }

            $user->url = $url ?? $user->url;
            $user->save();

            return $this->ok('Profile info updated successfully');
        } catch (\Exception $exception) {
            return $this->error($exception->getMessage(), 404);
        }
    }

    public function profileAvatarUpload(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            if ($request->hasFile('avatar')) {
                // Get user details record
                $details = $user->userDetails;

                if (!$details) {
                    $details = new UserDetails();
                    $details->users_id = $user->id;
                }

                // Delete old photo if exists
                if ($details->photo) {
                    Helper::fileDelete($details->photo);
                }

                // Upload new photo
                $imagePath = Helper::fileUpload(
                    $request->file('avatar'),
                    'avatar',
                    time() . '_' . $request->file('avatar')->getClientOriginalName()
                );

                $details->photo = $imagePath;
                $details->save();
            }

            return $this->success('Profile photo updated successfully', $user, 200);

        } catch (\Exception $exception) {
            return response()->json([
                'status'  => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    }

   public function profileAvatarRemove()
    {
        try {

            $user = Auth::user();

            // Get user details record
            $details = $user->userDetails;

            if ($details->photo) {
                // Delete the file from storage
                Helper::fileDelete($details->photo);

                // Remove photo path from DB
                $details->photo = null;
                $details->save();

                return $this->success('Profile photo removed successfully', $user, 200);
            }

            return $this->error('Profile photo not found', 404);

        } catch (\Exception $exception) {
            return response()->json([
                'status'  => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function updateDetails(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "bio"           => 'nullable|string|max:255',
            "username"       => 'nullable|string|max:255|unique:users,username',
            "email"      => 'nullable|string|max:255|unique:users,email',
            "name"        => 'nullable|string|max:255',
            "date_of_birth"     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }

        $user = Auth::user();

        $update = $user->update([
            "bio"           => $request->bio ?? $user->bio,
            "username"       => $request->username ?? $user->username,
            "email"      => $request->email ?? $user->email,
            "dob"        => $request->date_of_birth ?? $user->dob,
            "name"        => $request->name ?? $user->name,
        ]);

        if ($update) {
            return $this->ok('Profile info updated successfully', $user, 200);
        } else {
            return $this->error('Profile info not found', 500);
        }
    }

    public function accountDelete(Request $request)
    {
        $user = Auth::user();

        $user->delete();

        return $this->ok('Account deleted successfully', [], 200);
    }

}
