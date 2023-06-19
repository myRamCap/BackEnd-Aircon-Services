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
use App\Http\Controllers\Api\admin\VehicleController as AdminVehicleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\mobile\BookingController as MobileBookingController;
use App\Http\Controllers\Api\mobile\ClientController as MobileClientController;
use App\Http\Controllers\Api\mobile\NotificationController as MobileNotificationController;
use App\Http\Controllers\Api\mobile\promotionController as MobilePromotionController;
use App\Http\Controllers\Api\mobile\ServiceCenterController as MobileServiceCenterController;
use App\Http\Controllers\Api\mobile\ServiceCenterTimeSlotController as MobileServiceCenterTimeSlotController;
use App\Http\Controllers\Api\mobile\ServiceController as MobileServiceController;
use App\Http\Controllers\Api\mobile\VehicleController as MobileVehicleController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OTP\OtpController;
use App\Http\Controllers\Api\PromotionController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\RolesController;
use App\Http\Controllers\Api\ServiceCenterBookingController;
use App\Http\Controllers\Api\ServiceCenterController;
use App\Http\Controllers\Api\ServiceCenterServicesController;
use App\Http\Controllers\Api\ServiceCenterTimeSlotController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\ServicesLogoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VehicleController;
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

    // WEB
    Route::resource('/web/users', AdminUserController::class);
    Route::resource('/web/serviceslogo', AdminServicesLogoController::class);
    Route::resource('/web/services', AdminServiceController::class);
    Route::resource('/web/servicecenter', AdminServiceCenterController::class);
    Route::resource('/web/vehicles', AdminVehicleController::class);
    Route::resource('/web/service_center/services', AdminServiceCenterServicesController::class);
    Route::resource('/web/service_center/operationtime', AdminOperationTimeController::class);
    Route::resource('/web/service_center/timeslot', AdminServiceCenterTimeSlotController::class);
    Route::resource('/web/service_center/booking', AdminServiceCenterBookingController::class);
    Route::resource('/web/client', AdminClientController::class);
    Route::resource('/web/notification', AdminNotificationController::class);
    Route::resource('/web/booking', AdminBookingController::class);
    Route::resource('/web/promotion', AdminPromotionController::class);

    Route::get('/web/corporate_account', [AdminUserController::class, 'corporate']);
    Route::get('/web/service_center/vehicle/{id}', [AdminVehicleController::class, 'vehicle']);
    Route::get('/web/branchmanager/{id}', [AdminUserController::class, 'branchmanager']);
    Route::get('/web/service_center/timeslot/{id}/{year}/{month}/{day}',[AdminServiceCenterTimeSlotController::class, 'timeslot']);
    Route::get('/web/bookings/service_center/services/{id}', [AdminBookingController::class, 'services']);
    Route::get('/web/bookings/{id}/{year}/{month}/{day}',[AdminBookingController::class, 'timeslot']);
    Route::get('/web/bookings/service_center/{id}',[AdminBookingController::class, 'service_center']);
    Route::get('/web/corporateservicecenter/{id}', [AdminServiceCenterController::class, 'corporate']);
    Route::get('/web/roles/{id}', [AdminRolesController::class, 'show']);
    Route::get('/web/client_name', [AdminClientController::class, 'clients']);
    Route::get('/web/service_center_name/{id}', [AdminServiceCenterController::class, 'service_center']);

    // Mobile
   Route::resource('/mobile/vehicles', MobileVehicleController::class);
   Route::resource('/mobile/client', MobileClientController::class);
   Route::resource('/mobile/services', MobileServiceController::class);
   Route::resource('/mobile/booking', MobileBookingController::class);

   Route::get('/mobile/editclient/{id}', [MobileClientController::class, 'edit_profile']);
   Route::get('/mobile/notifications', [MobileNotificationController::class, 'notifications']);
   Route::get('/mobile/promotions', [MobilePromotionController::class, 'promotions']);
   Route::get('/mobile/upcomingbooking/{id}', [MobileBookingController::class, 'upcoming']);
   Route::get('/mobile/upcomingbooking24hrs/{id}', [MobileBookingController::class, 'upcoming24hrs']);
   Route::get('/mobile/records/{id}', [MobileBookingController::class, 'records']);
   Route::get('/mobile/servicecenters/{category}', [MobileServiceCenterController::class, 'getCategory']);
   Route::get('/mobile/servicecenters', [MobileServiceCenterController::class, 'getall']);
   Route::get('/mobile/servicecenterdays/{id}', [MobileServiceCenterController::class, 'getdays']);
   Route::get('/mobile/service_center/timeslot/{id}/{year}/{month}/{day}',[MobileServiceCenterTimeSlotController::class, 'timeslot']);
   


    // done
    // Route::resource('/users', UserController::class);
    // Route::resource('/serviceslogo', ServicesLogoController::class);
    // Route::resource('/services', ServiceController::class);
    // Route::resource('/servicecenter', ServiceCenterController::class);
    // Route::resource('/vehicles', VehicleController::class);
    // Route::resource('/service_center/services', ServiceCenterServicesController::class);
    // Route::resource('/service_center/timeslot', ServiceCenterTimeSlotController::class);
    // Route::resource('/service_center/booking', ServiceCenterBookingController::class);
    // Route::resource('/client', ClientController::class);
    // Route::resource('/notification', NotificationController::class);
    // Route::resource('/booking', BookingController::class);
    // Route::resource('/promotion', PromotionController::class);

    // Route::get('/roles/{id}', [RolesController::class, 'show']);
    // Route::get('/corporate_account', [UserController::class, 'corporate']);
    // Route::get('/corporateservicecenter/{id}', [ServiceCenterController::class, 'corporate']);
    // Route::get('/service_center/vehicle/{id}', [VehicleController::class, 'vehicle']);
    // Route::get('/service_center/timeslot/{id}/{year}/{month}/{day}',[ServiceCenterTimeSlotController::class, 'timeslot']);
    // Route::get('/client_name', [ClientController::class, 'clients']);
    // Route::get('/bookings/service_center/services/{id}', [BookingController::class, 'services']);
    // Route::get('/bookings/{id}/{year}/{month}/{day}',[BookingController::class, 'timeslot']);
    // Route::get('/bookings/service_center/{id}',[BookingController::class, 'service_center']);


   
    // Route::get('/servicecenter/services', [ServiceCenterServicesController::class, 'test']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/reports_yearly', [ReportController::class, 'yearly']);
    Route::get('/reports_yearly/{year}', [ReportController::class, 'yearMonth']);
    Route::get('/reports_yearlyfilter/{yearStart}/{yearEnd}', [ReportController::class, 'yearlyfilter']);
    Route::get('/reports_today', [ReportController::class, 'today']);
    Route::get('/reports_monthly', [ReportController::class, 'monthly']);
    Route::get('/reports_monthly/{month}/{year}', [ReportController::class, 'monthDay']);
    Route::get('/reports_monthlyfilter/{monthStart}/{monthEnd}/{year}', [ReportController::class, 'monthlyfilter']);
});

Route::post('/changepwd/{id}', [AuthController::class, 'changePass']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot_password', [AuthController::class, 'forgot_password']);

// Mobile
Route::post('/mobile/client_register', [MobileClientController::class, 'register']);
Route::post('/mobile/client_login', [MobileClientController::class, 'login']);
Route::post('/mobile/client_verify', [MobileClientController::class, 'verification']);


// OLD
// Route::post('/client_register', [ClientController::class, 'register']);
// Route::post('/client_login', [ClientController::class, 'login']);
// Route::post('/client_verify', [ClientController::class, 'verification']);

// Email Verification
Route::post('/verifyotp_forgotpwd', [OtpController::class, 'verify']);
Route::post('/verifyotp', [OtpController::class, 'verification']);
Route::post('/expiredotp', [OtpController::class, 'expiredverification']);
Route::post('/resendotp', [OtpController::class, 'resend']);



