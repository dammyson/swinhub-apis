<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProductAssetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductFileController;
use App\Http\Controllers\ProductSpecificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewerController;
use App\Http\Controllers\StageController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'Auth'], function () {
    Route::post('login',  [AuthController::class,'postLogin'])->name('auth.login');
    Route::post('registration', [AuthController::class,'create'])->name('auth.registration');
    Route::get('activate/{id}', [AuthController::class,'activate'])->name('auth.activate');
    Route::post('forget-password',[AuthController::class,'activate'])->name('forget.password');
    Route::get('/change-password-verification/{id}',[AuthController::class,'CheckCanUpdatePassword'])->name('forget.password_verification')->middleware('signed');
    Route::post('/change-password/{id}',[AuthController::class,'updatePassword'])->name('password.change');
});

Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::post('/', [ProfileController::class,'update'])->name('user.update_profile');
    Route::post('/password', [ProfileController::class,'updatePassword'])->name('password.update');
});

Route::post('/company', [CompanyController::class,'update'])->middleware('auth:api')->name('company.update');

Route::prefix('users')->group(function () {
    Route::post('/',[UserController::class,'create'])->middleware('auth:api')->name('users.invite');
    Route::get('/',  [UserController::class,'list'])->middleware('auth:api')->name('users.list');
    Route::post('/{id}/resend-invitaion', [UserController::class,'resend'])->middleware('auth:api')->name('users.reinvite');
    Route::post('/{id}',[UserController::class,'update'])->middleware('auth:api')->name('users.update.role');//
    Route::delete('/{id}', [UserController::class,'destroy'])->middleware('auth:api')->name('user.delete'); 
    Route::post('/{id}/status', [UserController::class,'updateStatus'])->middleware('auth:api')->name('users.update');//
    Route::post('/complete-account/{id}',[UserController::class,'completeRegistrationAccount'])->name('user.complete_registration');
    Route::get('/complete-account-verification/{id}', [UserController::class,'CheckCanCompleteAccount'])->name('user.complete_registration_verification')->middleware('signed'); 
    Route::get('/roles', [UserController::class,'listRoles'])->name('users.roles');
    Route::post('/account/{id}', [UserController::class,'completeRegistrationAccount'])->name('user.invitation'); 
});


Route::prefix('sales')->group(function () {
    Route::post('/add',[UserController::class,'addSales'])->middleware('auth:api')->name('sales.add');
    Route::post('/contact',[UserController::class,'contactSales'])->middleware('auth:api')->name('sales.contact');
    Route::get('/',[UserController::class,'getSales'])->middleware('auth:api')->name('sales.get');
   
});


Route::prefix('products')->name('product.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProductController::class,'get'])->name('list');
    Route::get('/{id}', [ProductController::class,'show'])->name('show');
    Route::post('/', [ProductController::class,'create'])->name('store');
    Route::post('/{id}', [ProductController::class,'update'])->name('update');//
    Route::post('/{id}/review', [ReviewerController::class,'reviewProduct'])->name('review');
    Route::get('/{id}/review', [ReviewerController::class,'getReviewProduct'])->name('get');
    Route::post('/{id}/recommend', [ProductController::class,'recommendProduct'])->name('recommend');
});

Route::prefix('files')->name('file.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProductFileController::class,'get'])->name('list');
    Route::get('/{id}', [ProductFileController::class,'show'])->name('show');
    Route::post('/', [ProductFileController::class,'create'])->name('store');
    Route::post('/{id}', [ProductFileController::class,'update'])->name('update');//
    Route::post('/request/{id}', [ProductFileController::class,'requestAccess'])->name('update');//
    Route::post('/grant_access/{id}', [ProductFileController::class,'requestGranted'])->name('grant_access');//
    Route::post('/upload', [ProductFileController::class,'creatFile'])->name('upload');
    Route::post('/upload/temp', [ProductFileController::class,'upload_image'])->name('upload.temp');
});

Route::prefix('assets')->name('asset.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProductAssetController::class,'get'])->name('list');
    Route::get('/{id}', [ProductAssetController::class,'show'])->name('show');
    Route::post('/', [ProductAssetController::class,'create'])->name('store');
    Route::post('/{id}', [ProductAssetController::class,'update'])->name('update');//
});


Route::prefix('specifications')->name('specification.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ProductSpecificationController::class,'get'])->name('list');
    Route::get('/{id}', [ProductSpecificationController::class,'show'])->name('show');
    Route::post('/', [ProductSpecificationController::class,'create'])->name('store');
    Route::post('/{id}', [ProductSpecificationController::class,'update'])->name('update');//
});


Route::prefix('reviews')->name('review.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ReviewController::class,'get'])->name('list');
    Route::get('/{id}', [ReviewController::class,'show'])->name('show');
    Route::post('/', [ReviewController::class,'create'])->name('store');
    Route::post('/{id}', [ReviewController::class,'update'])->name('update');//
    Route::get('/start/{id}', [ReviewController::class,'start'])->name('start');
});


Route::prefix('stages')->name('stage.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [StageController::class,'get'])->name('list');
    Route::get('/{id}', [StageController::class,'show'])->name('show');
    Route::post('/', [StageController::class,'create'])->name('store');
    Route::post('/{id}', [StageController::class,'update'])->name('update');//
});

Route::prefix('reviewers')->name('reviewer.')->middleware(['auth:api'])->group(function () {
    Route::get('/', [ReviewerController::class,'get'])->name('list');
    Route::get('/{id}', [ReviewerController::class,'show'])->name('show');
    Route::post('/', [ReviewerController::class,'create'])->name('store');
    Route::post('/{id}', [ReviewerController::class,'update'])->name('update');//
    Route::post('/{id}/review', [ReviewerController::class,'updateR'])->name('update');//
   
});

Route::prefix('guest')->name('guest.')->group(function () {
    Route::post('/search', [ProductController::class,'search'])->name('search');
    Route::get('/products', [ProductController::class,'getPublic'])->name('product');
    Route::get('/product/{id}', [ProductController::class,'getPublicshow'])->name('show');
    Route::post('/contact', [UserController::class,'contactForm'])->name('contacts');
   
});


Route::prefix('notifications')->middleware('auth:api')->group(function () {
    Route::get('/', [ProfileController::class,'getAllNotification'])->name('get');
    Route::get('/{id}', [ProfileController::class,'markNotificationAsRead'])->name('password.update');
});


Route::prefix('posts')->name('post.')->group(function () {
    Route::get('/', [PostController::class,'get'])->name('list');
    Route::get('/{id}', [PostController::class,'show'])->name('show');
    Route::post('/', [PostController::class,'create'])->name('store');
    Route::post('/{id}', [PostController::class,'update'])->name('update');//
});

Route::prefix('comments')->name('comment.')->group(function () {
    Route::get('/', [PostCommentController::class,'get'])->name('list');
    Route::post('/', [PostCommentController::class,'create'])->name('store');
});