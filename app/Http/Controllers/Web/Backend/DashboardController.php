<?php

namespace App\Http\Controllers\Web\Backend;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use Illuminate\View\View;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\EmailLog;
use App\Models\Enquiry;
use App\Models\Faq;
use App\Models\Plan;
use App\Models\Review;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     *
     * @return View
     */


    public function index(): View
    {
        if (Auth::check() && (Auth::user()->role === 'Admin' || Auth::user()->role === 'Support')) {

            $admins = User::where('role', 'Admin')->count();
            $users = User::where('role', '!=', 'Admin')->count();
            $activeSubscritpions = Subscription::where('stripe_status', 'active')->count();
            $inactiveSubscritpions = Subscription::where('stripe_status', '!=' ,'active')->count();
            $plans = Plan::where('status', 'Active')->count();
            $email = EmailLog::where('status', 'success')->count();
            $review=Review::where('status', 'Active')->count();
            $faq=Faq::where('status', 'Active')->count();
            $enquiry=Enquiry::where('status', 'Active')->count();
            $activityLog=Activity::count();
            
            //dd(Auth::user());
            return view('backend.layouts.dashboard.index',
             compact('admins', 'users', 'activeSubscritpions', 'inactiveSubscritpions', 'plans', 'email', 'review', 'faq', 'enquiry', 'activityLog'));
        }else{
            return view('auth.layouts.login');
        }
    }
}
