<?php

use App\Http\Controllers\Api\admin\BookingController as AdminBookingController;
use App\Http\Controllers\Api\admin\ClientController as AdminClientController;
use App\Http\Controllers\Api\admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\admin\OperationTimeController as AdminOperationTimeController;
use App\Http\Controllers\Api\admin\PromotionController as AdminPromotionController;
use App\Http\Controllers\Api\admin\RolesController as AdminRolesController;
use App\Http\Controllers\Api\admin\ServiceCenterBookingController as AdminServiceCenterBookingController;
use App\Http\Controllers\Api\admin\ServiceCenterController as AdminServiceCenterController;
use App\Http\Controllers\Api\admin\ServiceCenterServicesController as AdminServiceCenterServicesController;
use App\Http\Controllers\Api\admin\ServiceCenterTimeSlotController as AdminServiceCenterTimeSlotController;
use App\Http\Controllers\Api\admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Api\admin\ServicesLogoController as AdminServicesLogoController;
use App\Http\Controllers\Api\admin\UserController as AdminUserController;
use App\Http\Controllers\Api\admin\AirconController as AdminAirconController;
use App\Http\Controllers\Api\admin\RatingController as AdminRatingController;
use App\Http\Controllers\Api\admin\ServiceCostController as AdminServiceCostController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\mobile\BookingController as MobileBookingController;
use App\Http\Controllers\Api\mobile\ClientController as MobileClientController;
use App\Http\Controllers\Api\mobile\NotificationController as MobileNotificationController;
use App\Http\Controllers\Api\mobile\promotionController as MobilePromotionController;
use App\Http\Controllers\Api\mobile\ServiceCenterController as MobileServiceCenterController;
use App\Http\Controllers\Api\mobile\ServiceCenterTimeSlotController as MobileServiceCenterTimeSlotController;
use App\Http\Controllers\Api\mobile\ServiceController as MobileServiceController;
use App\Http\Controllers\Api\mobile\AirconController as MobileAirconController;
use App\Http\Controllers\Api\mobile\RatingController as MobileRatingController;
use App\Http\Controllers\Api\OTP\OtpController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\tech\BookingController as TechBookingController;
use App\Http\Controllers\Api\tech\ServiceCenterController as TechServiceCenterController;
use App\Http\Controllers\Api\tech\UserController as TechUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ADMIN WEB APP
    Route::resource('/web/users', AdminUserController::class);
    Route::resource('/web/serviceslogo', AdminServicesLogoController::class);
    Route::resource('/web/services', AdminServiceController::class);
    Route::resource('/web/servicecenter', AdminServiceCenterController::class);
    Route::resource('/web/aircons', AdminAirconController::class);
    Route::resource('/web/service_center/service', AdminServiceCenterServicesController::class);
    Route::resource('/web/service_center/operationtime', AdminOperationTimeController::class);
    Route::resource('/web/service_center/timeslot', AdminServiceCenterTimeSlotController::class);
    Route::resource('/web/service_center/booking', AdminServiceCenterBookingController::class);
    Route::resource('/web/client', AdminClientController::class);
    Route::resource('/web/notification', AdminNotificationController::class);
    Route::resource('/web/bookings', AdminBookingController::class);
    Route::resource('/web/promotion', AdminPromotionController::class);
    Route::resource('/web/servicecost', AdminServiceCostController::class);

    Route::get('/web/testing', [AdminUserController::class, 'testing']);

    Route::get('/web/rating/{id}', [AdminRatingController::class, 'rating']);
    Route::get('/web/service_center/operation/{id}', [AdminOperationTimeController::class, 'operation']);
    Route::get('/web/corporate_account', [AdminUserController::class, 'corporate']);
    Route::get('/web/service_center/aircon/{id}', [AdminAirconController::class, 'aircon']);
    Route::get('/web/branchmanager/{id}', [AdminUserController::class, 'branchmanager']);
    Route::get('/web/service_center/timeslot/{id}/{year}/{month}/{day}',[AdminServiceCenterTimeSlotController::class, 'timeslot']);
    Route::get('/web/bookings/service_center/services/{id}', [AdminBookingController::class, 'services']);
    Route::get('/web/bookings/{id}/{year}/{month}/{day}',[AdminBookingController::class, 'timeslot']);
    Route::get('/web/bookings/service_center/{id}',[AdminBookingController::class, 'service_center']);
    Route::get('/web/corporateservicecenter/{id}', [AdminServiceCenterController::class, 'corporate']);
    Route::get('/web/roles/{id}', [AdminRolesController::class, 'show']);
    Route::get('/web/client_name', [AdminClientController::class, 'clients']);
    Route::get('/web/service_center_name/{id}', [AdminServiceCenterController::class, 'service_center']);

    
    // CLIENT APP
    Route::resource('/mobile/aircon', MobileAirconController::class);
    Route::resource('/mobile/clients', MobileClientController::class);
    Route::resource('/mobile/servicess', MobileServiceController::class);

    // change time
    Route::resource('/mobile/bookingss', MobileBookingController::class);

    Route::get('/mobile/filter_services/{id}', [MobileServiceController::class, 'getServices']);
    Route::post('/mobile/rating', [MobileRatingController::class, 'rating']);
    Route::put('/mobile/editclient', [MobileClientController::class, 'edit_profile']);
    Route::get('/mobile/notifications', [MobileNotificationController::class, 'notifications']);
    Route::get('/mobile/promotions/{id}', [MobilePromotionController::class, 'promotions']);
    Route::get('/mobile/upcomingbooking/{id}', [MobileBookingController::class, 'upcoming']);
    Route::get('/mobile/upcomingbooking24hrs/{id}', [MobileBookingController::class, 'upcoming24hrs']);
    Route::get('/mobile/records/{id}', [MobileBookingController::class, 'records']);
    Route::get('/mobile/servicecenters/{category}', [MobileServiceCenterController::class, 'getCategory']);
    Route::get('/mobile/servicecenters', [MobileServiceCenterController::class, 'getall']);
    Route::get('/mobile/servicecenterdays/{id}', [MobileServiceCenterController::class, 'getdays']);

    // change time
    Route::get('/mobile/service_center/timeslot/{id}/{year}/{month}/{day}',[MobileServiceCenterTimeSlotController::class, 'timeslot']);

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/reports_yearly/{id}', [ReportController::class, 'yearly']);
    Route::get('/reports_yearly/{id}/{year}', [ReportController::class, 'yearMonth']);
    Route::get('/reports_yearlyfilter/{id}/{yearStart}/{yearEnd}', [ReportController::class, 'yearlyfilter']);
    Route::get('/reports_today/{id}', [ReportController::class, 'today']);
    Route::get('/reports_monthly/{id}', [ReportController::class, 'monthly']);
    Route::get('/reports_monthly/{id}/{month}/{year}', [ReportController::class, 'monthDay']);
    Route::get('/reports_monthlyfilter/{id}/{monthStart}/{monthEnd}/{year}', [ReportController::class, 'monthlyfilter']);

    // TECHNICIAN APP
    Route::get('/tech/upcoming/{id}', [TechBookingController::class, 'upcoming']);
    Route::put('/tech/upcoming/{id}/{tech_id}', [TechBookingController::class, 'update_upcoming']);
    Route::get('/tech/intransit/{id}', [TechBookingController::class, 'intransit']);
    Route::put('/tech/intransit/{id}', [TechBookingController::class, 'update_intransit']);
    Route::get('/tech/inprocess/{id}', [TechBookingController::class, 'inprocess']);
    Route::put('/tech/inprocess/{id}', [TechBookingController::class, 'update_inprocess']);
    Route::get('/tech/completed/{id}', [TechBookingController::class, 'completed']);
    Route::get('/tech/booking/{id}', [TechBookingController::class, 'details']);
    Route::get('/tech/service_center/{id}', [TechServiceCenterController::class, 'service_center']);
    Route::get('/tech/info/{id}', [TechUserController::class, 'getDetails']);
    Route::get('/tech/available/{id}', [TechBookingController::class, 'available']);

    Route::post('/tech/delete_account/{id}', [TechUserController::class, 'delete']);
    Route::post('/tech/logout', [TechUserController::class, 'logout']);

    // testing email
    // Route::get('/mobile/email/{id}', [MobileBookingController::class, 'email_send']);
});

Route::post('/changepwd/{id}', [AuthController::class, 'changePass']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot_password', [AuthController::class, 'forgot_password']);

// Mobile
Route::post('/mobile/client_register', [MobileClientController::class, 'register']);
Route::post('/mobile/client_login', [MobileClientController::class, 'login']);
Route::post('/mobile/client_verify', [MobileClientController::class, 'verification']);
Route::delete('/mobile/client_otp_cancel/{number}', [MobileClientController::class, 'otp_cancel']);

//Technician
Route::post('/tech/login', [TechUserController::class, 'login']);
Route::post('/tech/verify', [TechUserController::class, 'verification']);
Route::post('/tech/changepwd', [TechUserController::class, 'changePass']);

// Email Verification
Route::post('/verifyotp_forgotpwd', [OtpController::class, 'verify']);
Route::post('/verifyotp', [OtpController::class, 'verification']);
Route::post('/expiredotp', [OtpController::class, 'expiredverification']);
Route::post('/resendotp', [OtpController::class, 'resend']);



