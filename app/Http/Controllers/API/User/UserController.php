<?php

namespace App\Http\Controllers\API\User;

use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\NewsLetter\NewsletterController;

class UserController extends Controller
{
    use ApiResponse;
    public function index()
    {
        $users = User::with(['userDetails', 'store'])->where("role", "User")/*->paginate(10)*/->get();
        return $this->ok('Users retrieved successfully', $users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'dob' => 'nullable|date',
            'number' => 'nullable|string|max:20',
            'address' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => $validated['first_name'],
                'last_name'  => $validated['last_name'],
                'email'      => $validated['email'],
                'password'   => $validated['password'],
                'dob'        => $validated['dob'] ?? null,
                'address'    => $validated['address'] ?? null,
                'number'     => $validated['number'] ?? null,
                'is_online'  => true,
                'role'       => 'User',
            ]);

            // 2. Subscribe to Mailchimp via NewsletterController
            $NewsletterController = new NewsletterController();
            $response = $NewsletterController->subscribe($request);

            // If subscribe() redirected with error, rollback
            if (session()->get('t-error')) {
                DB::rollBack();
                return $this->error('User already subscribed', 400);
            }

            //3. If everything passed, commit
            DB::commit();

            return $this->success('User created successfully', [$user, $response], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Something Wrong', 404);
        }
    }

    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        $user->load('userDetails');

        return $this->success('User found successfully', $user, 200);
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'dob' => 'required|date',
            'number' => 'required|string|max:20',
            'address' => 'required|string',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'dob' => $validated['dob'],
            'address' => $validated['address'],
            'number' => $validated['number'],
            'is_online' => true,
        ]);

        return $this->success('User updated successfully', $user, 200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
           return $this->success('No Data found', [], 200);
        }
        $user->delete();
        return $this->ok('User deleted successfully');
    }
}
