<?php

use App\Models\Subscription;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Backend\Faq\FaqController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\Mail\MailController;
use App\Http\Controllers\Web\Backend\Plan\PlanController;
use App\Http\Controllers\Web\Backend\Review\ReviewController;
use App\Http\Controllers\Web\Backend\Enquiry\EnquiryController;
use App\Http\Controllers\Web\Backend\Payment\PaymentController;
use App\Http\Controllers\Web\Backend\Stripe\StripeKeyController;
use App\Http\Controllers\Web\Backend\Activity\ActivityController;
use App\Http\Controllers\Web\Backend\Dynamic\DynamicPage;
use App\Http\Controllers\Web\Backend\EmailLog\EmailLogController;

// Route for Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


// Route for Users Page
Route::get('/user', [UserController::class, 'index'])->name('user.index');
Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
Route::get('/user/{id}', [UserController::class, 'show'])->name('user.show');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::patch('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');

// Route for Dynamic Page
Route::get('/page', [DynamicPage::class, 'index'])->name('page.index');
Route::get('/page/create', [DynamicPage::class, 'create'])->name('page.create');
Route::post('/page/store', [DynamicPage::class, 'store'])->name('page.store');
Route::get('/page/edit/{id}', [DynamicPage::class, 'edit'])->name('page.edit');
Route::patch('/page/update/{id}', [DynamicPage::class, 'update'])->name('page.update');
Route::delete('/page/destroy/{id}', [DynamicPage::class, 'destroy'])->name('page.destroy');


//plan related
Route::get('/plan', [PlanController::class, 'index'])->name('plan.index');
Route::get('/plan-create', [PlanController::class, 'create'])->name('plan.create');
Route::post('/plan-create', [PlanController::class, 'store'])->name('plan.store');
Route::get('/plan/{id}', [PlanController::class, 'show'])->name('plan.show');
Route::get('/plan/edit/{id}', [PlanController::class, 'edit'])->name('plan.edit');
Route::put('/plan/update/{id}', [PlanController::class, 'update'])->name('plan.update');
Route::get('/plan/status/{id}', [PlanController::class, 'status'])->name('plan.status');
Route::delete('/plan/delete/{id}', [PlanController::class, 'destroy'])->name('plan.destroy');


//Payment
Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');

Route::get('/payment-create', [PaymentController::class, 'create'])->name('payment.create');
Route::post('/payment-create', [PaymentController::class, 'store'])->name('payment.store');
Route::get('/payment/{id}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/cancel/{id}', [PaymentController::class, 'checkoutCancel'])->name('payment.cancel');
Route::delete('/payment/delete/{id}', [PaymentController::class, 'destroy'])->name('payment.destroy');

Route::get('/success-subscription', [PaymentController::class, 'checkoutSuccess'])->name('checkout.success');
Route::get('/checkout/payment-cancel', [Subscription::class, 'checkoutCancel'])->name('checkout.cancel');

//backend mail send
Route::get('/mail/tempalte', [MailController::class, 'index'])->name('mail.index');
Route::get('/mail/tempalte/create', [MailController::class, 'create'])->name('mail.create');
Route::post('/mail/tempalte/create', [MailController::class, 'store'])->name('mail.store');
Route::get('/mail/tempalte/edit/{id}', [MailController::class, 'edit'])->name('mail.edit');
Route::post('/mail/tempalte/update/{id}', [MailController::class, 'update'])->name('mailTemp.update');
Route::delete('/mail/tempalte/delete/{id}', [MailController::class, 'destroy'])->name('mail.destroy');

// send email Log
Route::get('/mail', [EmailLogController::class, 'index'])->name('email.index');
Route::get('/mail/send', [EmailLogController::class, 'create'])->name('email.create');
Route::post('/mail/send', [EmailLogController::class, 'sendBulkMail'])->name('send.bulk.mail');
Route::delete('/mail/delete/{id}', [EmailLogController::class, 'destroy'])->name('email.destroy');


//faq
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
Route::get('/faq/create', [FaqController::class, 'create'])->name('faq.create');
Route::post('/faq/create', [FaqController::class, 'store'])->name('faq.store');
Route::get('/faq/{id}', [FaqController::class, 'show'])->name('faq.show');
Route::get('/faq/edit/{id}', [FaqController::class, 'edit'])->name('faq.edit');
Route::put('/faq/update/{id}', [FaqController::class, 'update'])->name('faq.update');
Route::delete('/faq/delete/{id}', [FaqController::class, 'destroy'])->name('faq.destroy');


//enquiry
Route::get('/enquiry', [EnquiryController::class, 'index'])->name('enquiry.index');
Route::get('/enquiry/edit/{id}', [EnquiryController::class, 'edit'])->name('enquiry.edit');
Route::put('/enquiry/update/{id}', [EnquiryController::class, 'update'])->name('enquiry.update');
Route::delete('/enquiry/delete/{id}', [EnquiryController::class, 'destroy'])->name('enquiry.destroy');



//review
Route::get('/review', [ReviewController::class, 'index'])->name('review.index');
Route::get('/review/edit/{id}', [ReviewController::class, 'edit'])->name('review.edit');
Route::put('/review/update/{id}', [ReviewController::class, 'update'])->name('review.update');
Route::delete('/review/delete/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');


Route::get('/activity-logs', [ActivityController::class, 'index'])->name('activity.index');
Route::delete('/activity/delete/{id}', [ActivityController::class, 'destroy'])->name('activity.destroy');


Route::get('/stripe/edit', [StripeKeyController::class, 'edit'])->name('stripe.edit');
Route::put('/stripe/update', [StripeKeyController::class, 'update'])->name('stripe.update');