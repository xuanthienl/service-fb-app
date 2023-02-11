<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', 'API\UserController@login');
Route::post('register', 'API\UserController@register');
Route::post('reset-pasword', 'API\UserController@resetPassword');
Route::middleware('auth:api')->group(function () {
    Route::post('logout', 'API\UserController@logout');
});

Route::get('users', 'API\UserController@index');
Route::post('users/search', 'API\UserController@searchUser');
Route::get('user/{username}', 'API\UserController@profile');
Route::post('user/{id}/profile/update', 'API\UserController@profileUpdate');

Route::prefix('/system-settings')->group(function() {
    Route::get('index', 'API\SystemSettingsController@index');
    
    Route::post('telegramBot-settings', 'API\SystemSettingsController@PostTelegramBotSettings');
    Route::post('telegramBot-settings/{id}/delete', 'API\SystemSettingsController@PostDeleteTelegramBotSettings');
    
    Route::post('payment-settings', 'API\SystemSettingsController@PostPaymentSettings');
    Route::post('payment-settings/{id}/delete', 'API\SystemSettingsController@PostDeletePaymentSettings');
});

Route::prefix('/facebook')->group(function() {
    Route::get('settings', 'API\FacebookController@GetSettingsFacebook');
    Route::post('settings', 'API\FacebookController@PostSettingsFacebook');

    Route::get('order-share/{user_id}', 'API\FacebookController@GetOrderShareFacebook');
    Route::post('buff-share', 'API\FacebookController@BuffShare');

    Route::get('order-comment/{user_id}', 'API\FacebookController@GetOrderCommentFacebook');
    Route::post('buff-comment', 'API\FacebookController@BuffComment');

    Route::get('order/{user_id}/{buff_type}', 'API\FacebookController@GetListBuffFacebook');
    Route::post('buff', 'API\FacebookController@PostBuffFacebook');

    Route::get('buff/{id}/confirm', 'API\FacebookController@GetBuffConfirm');
    Route::post('buff/{id}/confirm', 'API\FacebookController@PostBuffConfirm');
    Route::post('buff/{id}/confirm-fail', 'API\FacebookController@PostBuffConfirmFail');

    Route::get('order/total', 'API\FacebookController@GetTotalOrderFacebook');
    Route::get('order/{user_id}/total', 'API\FacebookController@GetTotalOrderByUserFacebook');
});

Route::prefix('/payment')->group(function() {
    Route::get('{user_id}', 'API\PaymentController@index');
    Route::get('buy-currency/{code}', 'API\PaymentController@GetBuyCurrency');
    
    Route::post('buy-currency', 'API\PaymentController@PostBuyCurrency');
    Route::post('buy-currency/{id}/delete', 'API\PaymentController@PostDeleteBuyCurrency');
    Route::post('buy-currency/{id}/confirm', 'API\PaymentController@PostConfirmBuyCurrency');
    
    Route::post('sell-currency/{id}/confirm', 'API\PaymentController@PostConfirmSellCurrency');
    Route::post('sell-currency/{id}/confirm-fail', 'API\PaymentController@PostConfirmSellCurrencyFail');

    Route::post('admin-add-or-sub-currenry-user', 'API\PaymentController@PostAddOrSubCurrency');
});

Route::post('contact', 'API\ContactController@PostContact');