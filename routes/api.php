<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckPlanExpiry;
use App\Http\Controllers\API\Plan\PlanController;
use App\Http\Controllers\API\User\UserController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Offer\OfferController;
use App\Http\Controllers\API\Store\StoreController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Viewer\ViewerController;
use App\Http\Controllers\API\Payment\PaymentController;
use App\Http\Controllers\API\Product\ProductController;
use App\Http\Controllers\API\User\UserDetailController;
use App\Http\Controllers\API\Auth\ProfileUpdateController;
use App\Http\Controllers\API\DynamicPage\DynamicPageController;
use App\Http\Controllers\API\Product\ProductCategoryController;
use App\Http\Controllers\API\Availability\AvailabilityController;
use App\Http\Controllers\API\Coupon\CouponController;
use App\Http\Controllers\API\DashboardView\DashboardOverviewController;
use App\Http\Controllers\API\Enquiry\EnquiryController;
use App\Http\Controllers\API\Faq\FaqController;
use App\Http\Controllers\API\Newsletter\NewsletterController;
use App\Http\Controllers\API\Review\ReviewController;
use App\Http\Controllers\API\SystemSetting\SystemSettingController;
use App\Http\Controllers\API\Subscription\SubscriptionController;

// Route for System Setting
Route::get('/system-setting', [SystemSettingController::class, 'systemSetting']);

// Dynamic Pages routes
Route::get('/terms-and-conditions', [DynamicPageController::class, 'terms']);
Route::get('/privacy-policy', [DynamicPageController::class, 'privacy']);

Route::get('/plan/list', [PlanController::class,'index']);
Route::get('/plan/show/{id}', [PlanController::class,'show']);

// Payment Success routes
Route::get('/success-subscription', [SubscriptionController::class, 'successSubscription'])
    ->name('subscription.success');

Route::middleware(['guest'])->group(function () {

    //  Authentication routes
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('resend_otp', [RegisterController::class, 'resend_otp']);
    Route::post('verify_otp', [RegisterController::class, 'verify_otp']);
    Route::post('forgot-password', [RegisterController::class, 'forgot_password']);
    Route::post('forgot-verify-otp', [RegisterController::class, 'forgot_verify_otp']);
    Route::post('reset-password', [RegisterController::class, 'reset_password']);

});

Route::group(['middleware' => 'auth:sanctum'], function ($router) {
    // common routes
    Route::get('/user-detail', [LoginController::class, 'userDetails']);
    Route::post('/logout', [LoginController::class, 'logout']);

    // profile update routes
    Route::post('/change-password', [ProfileUpdateController::class, 'changePassword']);
    Route::post('/account-delete', [ProfileUpdateController::class, 'accountDelete']);


    Route::post('/profile-avatar-upload', [ProfileUpdateController::class, 'profileAvatarUpload']);
    Route::post('/profile-avatar-remove', [ProfileUpdateController::class, 'profileAvatarRemove']);


    Route::post('/profile/update', [UserDetailController::class, 'update']); //update or store both user and user's details

    // Subscription routes
    Route::post('/checkout-session', [SubscriptionController::class, 'createCheckoutSession'])->name('checkout.session');
    Route::post('/subscribe', [SubscriptionController::class, 'subscribeSuccess'])->name('subscribe');
    Route::post('/send-mail/{id}', [SubscriptionController::class, 'sendMail']);
    Route::post('/cancel-subscription', [SubscriptionController::class, 'cancelSubscription'])->name('subscription.cancel');
    Route::get('/subscription-status', [SubscriptionController::class, 'getSubscriptionStatus'])->name('subscription.status');
    Route::get('/user-subscription-check', [SubscriptionController::class, 'userSubscriptionCheck'])->name('user.subscription.check');


    //review related controller
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']); //logged in user can add store
    Route::post('/reviews/{reviews}', [ReviewController::class, 'update']); //only creator can update
    Route::delete('/reviews/{reviews}', [ReviewController::class, 'destroy']);//only creator can delete

    //enquiry related controller
    Route::get('/enquiry', [EnquiryController::class, 'index']);
    Route::post('/enquiry', [EnquiryController::class, 'store']); //logged in user can add store

    //faq related controller
    Route::get('/faq', [FaqController::class, 'index']);

});
