<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\TrainingRequestController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\EventManagementController;
use App\Http\Controllers\VenueManagementController;
use App\Http\Controllers\EventTypeManagementController;
use App\Http\Controllers\SpeakerManagementController;
use App\Http\Controllers\SponsorManagementController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\EventEnquiryController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PartnerManagementController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\TestimonialManagementController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SetupController;

// First-time installation setup (blocked after installation by CheckInstalled middleware)
Route::get('/setup', [SetupController::class, 'index'])->name('setup')
    ->withoutMiddleware(\App\Http\Middleware\ScopeToOrganization::class);
Route::post('/setup', [SetupController::class, 'store'])->name('setup.store')
    ->withoutMiddleware(\App\Http\Middleware\ScopeToOrganization::class);
Route::get('/setup/success', [SetupController::class, 'success'])->name('setup.success')
    ->withoutMiddleware(\App\Http\Middleware\ScopeToOrganization::class);

// Public pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/events', [PageController::class, 'events'])->name('events');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/partners', [PageController::class, 'partners'])->name('partners');
Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');

// Legal pages
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');

// Event details - public
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');

// Stripe webhook - public (no auth), CSRF-exempt
Route::post('/stripe/webhook', [StripePaymentController::class, 'webhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class);

// Event Enquiries (public - guests can submit)
Route::post('/enquiries', [EventEnquiryController::class, 'store'])->name('enquiries.store')->middleware('throttle:5,10');

// Account Support Request (public - users blocked from OTP can submit)
Route::post('/account-support', [\App\Http\Controllers\AccountSupportController::class, 'store'])->name('account-support.store');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Email Verification (OTP) - accessible without auth for login redirect flow
Route::get('/email/verify', [AuthController::class, 'showVerifyForm'])->name('verification.notice');
Route::post('/email/verify', [AuthController::class, 'verifyOtp'])->name('verification.verify-otp');
Route::post('/email/resend', [AuthController::class, 'resendOtp'])->name('verification.resend-otp')->middleware('throttle:6,1');

Route::middleware('auth')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/consent', [\App\Http\Controllers\ConsentController::class, 'show'])->name('consent.show');
    Route::post('/consent', [\App\Http\Controllers\ConsentController::class, 'store'])->name('consent.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('verified');
});

Route::middleware(['auth', 'consent'])->group(function () {

    Route::post('/events/{slug}/purchase', [EventController::class, 'purchase'])->name('events.purchase')->middleware('verified');
    Route::post('/events/{slug}/reserve', [EventController::class, 'reserve'])->name('events.reserve')->middleware('verified');
    Route::get('/attendee-lookup', [DashboardController::class, 'attendeeLookup'])->name('attendee.lookup');

    // Enquiry reply attachments (staff and enquiry owner)
    Route::get('/enquiry-attachments/{id}/preview', [EventEnquiryController::class, 'previewAttachment'])->name('enquiries.attachment.preview');
    Route::get('/enquiry-attachments/{id}/download', [EventEnquiryController::class, 'downloadAttachment'])->name('enquiries.attachment.download');

    Route::get('/events/{slug}/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/events/{slug}/stripe/intent', [StripePaymentController::class, 'createIntent'])->name('events.stripe.intent');
    Route::post('/stripe/confirm', [StripePaymentController::class, 'confirm'])->name('stripe.confirm');

    Route::get('/training/request', [TrainingRequestController::class, 'index'])->name('training.index');
    Route::get('/training/request/create', [TrainingRequestController::class, 'create'])->name('training.create');
    Route::post('/training/request', [TrainingRequestController::class, 'store'])->name('training.store')->middleware('throttle:5,10');
    Route::get('/training/request/{slug}', [TrainingRequestController::class, 'show'])->name('training.show');
    Route::get('/training/request/{slug}/edit', [TrainingRequestController::class, 'edit'])->name('training.edit');
    Route::put('/training/request/{slug}', [TrainingRequestController::class, 'update'])->name('training.update');
    Route::post('/training/request/{slug}/accept', [TrainingRequestController::class, 'accept'])->name('training.accept');
    Route::post('/training/request/{slug}/schedule', [TrainingRequestController::class, 'schedule'])->name('training.schedule');

    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon'])->name('coupons.validate');

    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::put('/tickets/{ticket}/attendee', [TicketController::class, 'updateAttendee'])->name('tickets.attendee.update');
    Route::get('/tickets/{event}/{hash}', [TicketController::class, 'show'])->name('tickets.show');

    Route::middleware('role:staff')->group(function () {
        Route::get('/check-in', [CheckInController::class, 'index'])->name('checkin.index');
        Route::post('/check-in/select-event', [CheckInController::class, 'selectEvent'])->name('checkin.select-event');
        Route::post('/check-in/verify', [CheckInController::class, 'verify'])->name('checkin.verify');
        Route::post('/check-in/confirm', [CheckInController::class, 'confirm'])->name('checkin.confirm');
        Route::post('/check-in/reset', [CheckInController::class, 'reset'])->name('checkin.reset');
    });

    Route::middleware('role:staff')->prefix('manage')->name('manage.')->group(function () {
        Route::get('/tickets', [\App\Http\Controllers\TicketController::class, 'manageIndex'])->name('tickets.index');
        Route::post('/tickets/reservations/{order}/complete', [\App\Http\Controllers\TicketController::class, 'completeReservation'])->name('tickets.reservations.complete');
        Route::delete('/tickets/{ticket}', [\App\Http\Controllers\TicketController::class, 'manageDestroy'])->name('tickets.destroy');
        Route::post('/tickets/{ticket}/reactivate', [\App\Http\Controllers\TicketController::class, 'manageReactivate'])->name('tickets.reactivate');
    });

    // Manager & Admin management pages
    Route::middleware('role:manager')->prefix('manage')->name('manage.')->group(function () {
        Route::get('/events', [EventManagementController::class, 'index'])->name('events.index');
        Route::get('/events/create', [EventManagementController::class, 'create'])->name('events.create');
        Route::post('/events', [EventManagementController::class, 'store'])->name('events.store');
        Route::post('/events/description-images', [EventManagementController::class, 'uploadDescriptionImage'])->name('events.description-images.store');
        Route::get('/events/{slug}', [EventManagementController::class, 'show'])->name('events.show');
        Route::post('/events/{slug}/check-in', [EventManagementController::class, 'checkInAttendee'])->name('events.checkin');
        Route::get('/events/{slug}/edit', [EventManagementController::class, 'edit'])->name('events.edit');
        Route::put('/events/{slug}', [EventManagementController::class, 'update'])->name('events.update');
        Route::delete('/events/{slug}', [EventManagementController::class, 'destroy'])->name('events.destroy');

        Route::get('/venues', [VenueManagementController::class, 'index'])->name('venues.index');
        Route::get('/venues/create', [VenueManagementController::class, 'create'])->name('venues.create');
        Route::post('/venues', [VenueManagementController::class, 'store'])->name('venues.store');
        Route::get('/venues/{id}/edit', [VenueManagementController::class, 'edit'])->name('venues.edit');
        Route::put('/venues/{id}', [VenueManagementController::class, 'update'])->name('venues.update');
        Route::delete('/venues/{id}', [VenueManagementController::class, 'destroy'])->name('venues.destroy');

        Route::get('/event-types', [EventTypeManagementController::class, 'index'])->name('event-types.index');
        Route::get('/event-types/create', [EventTypeManagementController::class, 'create'])->name('event-types.create');
        Route::post('/event-types', [EventTypeManagementController::class, 'store'])->name('event-types.store');
        Route::post('/event-types/bulk-update', [EventTypeManagementController::class, 'bulkUpdate'])->name('event-types.bulk-update');
        Route::get('/event-types/{id}/edit', [EventTypeManagementController::class, 'edit'])->name('event-types.edit');
        Route::put('/event-types/{id}', [EventTypeManagementController::class, 'update'])->name('event-types.update');
        Route::delete('/event-types/{id}', [EventTypeManagementController::class, 'destroy'])->name('event-types.destroy');

        Route::get('/payments', [PaymentTypeController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentTypeController::class, 'store'])->name('payments.store');
        Route::put('/payments/{id}', [PaymentTypeController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{id}', [PaymentTypeController::class, 'destroy'])->name('payments.destroy');
        Route::post('/payments/orders/{order}/reserve', [PaymentTypeController::class, 'markOrderReserved'])->name('payments.orders.reserve');
        Route::post('/payments/orders/{order}/paid', [PaymentTypeController::class, 'markOrderPaid'])->name('payments.orders.paid');

        Route::get('/enquiries', [EventEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{id}', [EventEnquiryController::class, 'show'])->name('enquiries.show');
        Route::post('/enquiries/{id}/reply', [EventEnquiryController::class, 'reply'])->name('enquiries.reply');
        Route::delete('/enquiries/{id}', [EventEnquiryController::class, 'destroy'])->name('enquiries.destroy');

        Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [CouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{id}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{id}', [CouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{id}', [CouponController::class, 'destroy'])->name('coupons.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
        Route::get('/reports/events', [ReportController::class, 'events'])->name('reports.events');
        Route::get('/reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
        Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');
        Route::get('/reports/coupons', [ReportController::class, 'coupons'])->name('reports.coupons');
        Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('/contact-messages', [ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{contactMessage}', [ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::delete('/contact-messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        Route::get('/account-support', [\App\Http\Controllers\AccountSupportController::class, 'index'])->name('account-support.index');
        Route::get('/account-support/{accountSupport}', [\App\Http\Controllers\AccountSupportController::class, 'show'])->name('account-support.show');
        Route::post('/account-support/{accountSupport}/resolve', [\App\Http\Controllers\AccountSupportController::class, 'resolve'])->name('account-support.resolve');
    });

    // Speaker / Organizer management
    Route::middleware('role:manager')->prefix('manage')->name('manage.')->group(function () {
        Route::get('/speakers', [SpeakerManagementController::class, 'index'])->name('speakers.index');
        Route::post('/speakers', [SpeakerManagementController::class, 'store'])->name('speakers.store');
        Route::put('/speakers/{id}', [SpeakerManagementController::class, 'update'])->name('speakers.update');
        Route::delete('/speakers/{id}', [SpeakerManagementController::class, 'destroy'])->name('speakers.destroy');

        Route::get('/sponsors', [SponsorManagementController::class, 'index'])->name('sponsors.index');
        Route::get('/sponsors/create', [SponsorManagementController::class, 'create'])->name('sponsors.create');
        Route::post('/sponsors', [SponsorManagementController::class, 'store'])->name('sponsors.store');
        Route::get('/sponsors/{id}/edit', [SponsorManagementController::class, 'edit'])->name('sponsors.edit');
        Route::put('/sponsors/{id}', [SponsorManagementController::class, 'update'])->name('sponsors.update');
        Route::delete('/sponsors/{id}', [SponsorManagementController::class, 'destroy'])->name('sponsors.destroy');

        Route::get('/partners', [PartnerManagementController::class, 'index'])->name('partners.index');
        Route::get('/partners/create', [PartnerManagementController::class, 'create'])->name('partners.create');
        Route::post('/partners', [PartnerManagementController::class, 'store'])->name('partners.store');
        Route::get('/partners/{partner}/edit', [PartnerManagementController::class, 'edit'])->name('partners.edit');
        Route::put('/partners/{partner}', [PartnerManagementController::class, 'update'])->name('partners.update');
        Route::delete('/partners/{partner}', [PartnerManagementController::class, 'destroy'])->name('partners.destroy');

        Route::get('/testimonials', [TestimonialManagementController::class, 'index'])->name('testimonials.index');
        Route::post('/testimonials/{testimonial}/approve', [TestimonialManagementController::class, 'approve'])->name('testimonials.approve');
        Route::post('/testimonials/{testimonial}/reject', [TestimonialManagementController::class, 'reject'])->name('testimonials.reject');
        Route::delete('/testimonials/{testimonial}', [TestimonialManagementController::class, 'destroy'])->name('testimonials.destroy');
    });

// Notifications (top level group)
Route::middleware(['auth', 'consent'])->group(function () {
    Route::get('/my/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/my/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/my/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/my/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

    // Profile
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Customer pages
    Route::get('/my-tickets', [CustomerController::class, 'tickets'])->name('customer.tickets');
    Route::get('/enquiries', [CustomerController::class, 'enquiries'])->name('customer.enquiries');
    Route::get('/billing', [CustomerController::class, 'billing'])->name('customer.billing');
    Route::get('/saved-events', [CustomerController::class, 'savedEvents'])->name('customer.saved-events');
    Route::post('/saved-events/toggle', [CustomerController::class, 'toggleSave'])->name('customer.saved-events.toggle');
    Route::get('/calendar', [CustomerController::class, 'calendar'])->name('customer.calendar');
    Route::get('/notifications', [CustomerController::class, 'notifications'])->name('customer.notifications');
    Route::get('/account/settings', [CustomerController::class, 'settings'])->name('customer.settings');
    Route::post('/account/settings', [CustomerController::class, 'updateSettings'])->name('customer.settings.update');
    Route::get('/help', [CustomerController::class, 'help'])->name('customer.help');
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('customer.testimonials.store')->middleware('throttle:3,10');

    // Email verification
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard')->with('success', 'Email verified successfully.');
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification email sent.');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    // User management
    Route::middleware('role:manager')->prefix('manage')->name('manage.')->group(function () {
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
        Route::patch('/users/{id}/block', [AdminUserController::class, 'block'])->name('users.block');
        Route::patch('/users/{id}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');
        Route::post('/users/{id}/manual-verify', [AdminUserController::class, 'manualVerify'])->name('users.manual-verify');
        Route::post('/users/{id}/reset-verification', [AdminUserController::class, 'resetVerification'])->name('users.reset-verification');
        Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    });
}); // end auth+consent group

// Notifications
Route::middleware(['auth', 'consent'])->group(function () {
    Route::get('/my/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/my/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/my/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/my/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});
