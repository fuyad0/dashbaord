<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\User;
use Illuminate\View\View;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use DrewM\MailChimp\MailChimp;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\NewsLetter\NewsletterController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of all users.
     *
     * @param Request $request
     * @return JsonResponse|View
     */
    public function index(Request $request): JsonResponse | View
    {

        if ($request->ajax()) {
            $data = User::latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('avatar', function ($data) {
                    $defaultImage = asset('frontend/default-avatar-profile.jpg');
                    $url = $data->avatar ? asset($data->avatar) : $defaultImage;

                    return '<img src="' . $url . '" alt="Avatar" width="50" height="50" style="object-fit:cover; border-radius:50%;">';
                })
                ->addColumn('action', function ($data) {
                    return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                    <a href="' . route('user.edit', ['id' => $data->id]) . '" type="button" class="btn btn-primary fs-14 text-white edit-icn" title="Edit">
                                        <i class="fe fe-edit"></i>
                                    </a>
                                    <a href="#" type="button" onclick="showDeleteConfirm(' . $data->id . ')" class="btn btn-danger fs-14 text-white delete-icn" title="Delete">
                                        <i class="fe fe-trash"></i>
                                    </a>
                                </div>';
                })
                ->rawColumns(['avatar', 'action'])
                ->make();
        }
        return view('backend.layouts.user.index');
    }

    public function show($id)
    {
        $data = User::findOrFail($id);
        return view('backend.layouts.user.view', compact('data'));
    }


    public function create()
    {
        return view('backend.layouts.user.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'number' => 'nullable|max:20',
            'dob' => 'nullable|date',
            'password' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'role' => 'required|in:User,Company,Admin,Support',
        ]);


        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Handle avatar upload
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $avatarName = time() . '_avatar_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('uploads/avatar', $avatarName, 'public');
            }

            // Create user
            $data = new User();
            $data->first_name = $request->first_name;
            $data->last_name = $request->last_name ?? NULL;
            $data->email = $request->email;
            $data->email_verified_at = now();
            $data->password = Hash::make($request->password);
            $data->dob = $request->dob ?? NULL;
            $data->number = $request->number ?? NULL;
            $data->role = $request->role;
            $data->save();


            // Create user details
            $userdata = new UserDetails();
            $userdata->users_id = $data->id;
            $userdata->photo = $avatarPath ?? null;
            $userdata->save();

            /*$NewsletterController = new NewsletterController();
            $response = $NewsletterController->subscribe($request);
            if (session()->get('t-error')) {
                DB::rollBack();
                return redirect()->back()->with('t-error', session('t-error'));
            }*/
            DB::commit();
            return redirect()->route('user.index')->with('t-success', 'User created successfully');
        } catch (\Exception $exception) {
            DB::rollBack(); // rollback on any error
            return redirect()->route('user.index')->with('t-error', 'User creation failed: ' . $exception->getMessage());
        }
    }

    public function edit(int $id): View
    {
        $data = User::with('userDetails')->findOrFail($id);
        //dd($data);
        return view('backend.layouts.user.edit', compact('data'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        try {
            // Find the user
            $user = User::with('userDetails')->findOrFail($id);

            // Validate request
            $validator = Validator::make($request->all(), [
                'first_name' => 'nullable|string|max:100',
                'last_name' => 'nullable|string|max:100',
                'email' => 'nullable|string|email|unique:users,email,' . $id,
                'number'   => 'required|max:20',
                'password' => 'nullable|string',
                'dob' => 'nullable|string|max:100',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'role'   => 'required|in:User,Company,Admin,Support',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $avatarPath = $user->details?->photo ?? null;

            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($avatarPath && Storage::disk('public')->exists($avatarPath)) {
                    Storage::disk('public')->delete($avatarPath);
                }

                $avatar = $request->file('avatar');
                $avatarName = time() . '_avatar_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
                $avatarPath = $avatar->storeAs('uploads/avatar', $avatarName, 'public');
            }

            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->number = $request->number;
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            $user->dob = $request->dob ?? $user->dob;
            $user->role   = $request->role;
            $user->update();

            if ($user) {
                $userDetails = $user->userDetails ?? new UserDetails(['users_id' => $user->id]);

                if ($request->hasFile('avatar')) {
                    $userDetails->photo = $avatarPath;
                }
                $userDetails->save();
            }

            return redirect()->route('user.index')->with('t-success', 'User info Updated Successfully.');
        } catch (\Exception $exception) {
            return redirect()->route('user.index')->with('t-error', 'User info failed to update');
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json([
            't-success' => true,
            'message'   => 'Deleted successfully.',
        ]);
    }
}
