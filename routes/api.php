<?php

// use App\Http\Controllers\Api\V1\CustomerController;
// use App\Http\Controllers\Api\V1\InvoiceController;

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ServiceProviderController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\JobPostController;
use App\Http\Controllers\Api\V1\JobApplicationController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\NotificationController;
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

/* Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
}); */



// api/v1
Route::group([
    'prefix' => 'v1',
    'namespace' => 'App\Http\Controllers\Api\V1',
    'middleware' => 'auth:sanctum'
], function () {


    Route::withoutMiddleware('auth:sanctum')->group(function () {
        
        Route::post('login', [AuthController::class, 'signin']);
        Route::post('register', [AuthController::class, 'signup']);
        Route::post('google-login', [AuthController::class, 'googleLogin']); // Add this line

        Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('verify-forgot-otp', [AuthController::class, 'verifyForgotOtp']); // <-- new
        Route::post('reset-password', [AuthController::class, 'resetPassword']);
        
    });
    
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('resend-otp', [AuthController::class, 'resendOtp']);
    Route::post('logout', [AuthController::class, 'signout']);
    Route::get('/user/profile', [UserController::class, 'profile']);



    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('invoices', InvoiceController::class);

    Route::prefix('categories')->group(function () {
        Route::get('search-category-list-dropdown', [CategoryController::class, 'searchCategoryListDropdown']);
        Route::get('/', [CategoryController::class, 'index']);       // GET /api/v1/categories
        Route::post('/', [CategoryController::class, 'store']);      // POST /api/v1/categories
        Route::get('{id}', [CategoryController::class, 'show']);     // GET /api/v1/categories/{id}
        Route::put('{id}', [CategoryController::class, 'update']);   // PUT /api/v1/categories/{id}
        Route::delete('{id}', [CategoryController::class, 'destroy']);// DELETE /api/v1/categories/{id}
    });

    Route::prefix('service-providers')->group(function () {
        Route::get('user-services', [ServiceProviderController::class, 'getUserServices']);
        Route::get('/', [ServiceProviderController::class, 'index']);       // GET /api/v1/categories
        Route::post('/', [ServiceProviderController::class, 'store']); // ✅ New create route
        Route::put('{id}', [ServiceProviderController::class, 'update']);


    });


    Route::prefix('job-posts')->group(function () {
        Route::get('other_user_offer', [JobPostController::class, 'otherUserOffer']);
        Route::get('service_request', [JobPostController::class, 'serviceRequest']);
        Route::post('/direct', [JobPostController::class, 'store']); // alias for direct requests
        Route::get('/', [JobPostController::class, 'index']);       // GET /api/v1/categories
        Route::post('/', [JobPostController::class, 'store']);      // POST /api/v1/categories
        Route::get('{id}', [JobPostController::class, 'show']);     // GET /api/v1/categories/{id}
        Route::put('{id}', [JobPostController::class, 'update']);   // PUT /api/v1/categories/{id}
        Route::delete('{id}', [JobPostController::class, 'destroy']);// DELETE /api/v1/categories/{id}

    });

    Route::prefix('job-applications')->group(function () {
        // Route::post('/{id}/approve', [JobApplicationController::class, 'approve']);
        Route::post('/{id}/status', [JobApplicationController::class, 'updateStatus']);
        // Route::post('/{id}/withdraw', [JobApplicationController::class, 'withdraw']);
        Route::get('/my-work-history', [JobApplicationController::class, 'getProviderApplications']);
        Route::get('/', [JobApplicationController::class, 'index']);
        Route::post('/', [JobApplicationController::class, 'store']);
        Route::get('/{id}', [JobApplicationController::class, 'show']);
        Route::put('/{id}', [JobApplicationController::class, 'update']);
        Route::delete('/{id}', [JobApplicationController::class, 'destroy']);

    });

    Route::post('update-location', [LocationController::class, 'update']);
   
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    });


    
});
