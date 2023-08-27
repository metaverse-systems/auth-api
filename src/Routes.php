<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use MetaverseSystems\AuthApi\Controllers\AuthController;
use MetaverseSystems\AuthApi\Controllers\UserController;

Route::group(['middleware'=>['api'], 'prefix'=>'api'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['middleware'=>"auth:api", "prefix"=>"api"], function () {
    Route::resource("user", UserController::class);
    Route::post("user/{id}/restore", [UserController::class, "restore"]);
});

/* A dummy route, needed for email verification to work */
Route::group(["middleware"=>"auth:api", "domain"=>config("metaverse-auth-api.app-domain")], function () {
    Route::get("email/verify/{id}/{hash}", function (EmailVerificationRequest $request) {
    })->name("verification.verify");
});

Route::group(["middleware"=>"auth:api"], function () {
    Route::get('email/verify/{id}/{hash}', [AuthController::class, "verify"]);
});
