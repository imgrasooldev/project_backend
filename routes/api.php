<?php

// use App\Http\Controllers\Api\V1\CustomerController;
// use App\Http\Controllers\Api\V1\InvoiceController;

use App\Http\Controllers\Api\V1\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ServiceProviderController;
use App\Http\Controllers\Api\V1\UserController;
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
    });
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
        Route::get('/', [ServiceProviderController::class, 'index']);       // GET /api/v1/categories
    });


    // Route::post('invoices/bulk', ['uses' => 'InvoiceController@bulkStore']);
});
