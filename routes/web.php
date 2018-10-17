<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('run', 'HomeController@run');
Route::get('phpInfo', 'HomeController@phpInfo');

/**
 * 登录相关
 */
Route::prefix('api')->group(function() {
    Route::get('attempt', 'HomeController@attempt');
    Route::any('login', 'Auth\LoginController@login');
    Route::post('register', 'Auth\RegisterController@register');
    Route::post('logout', 'Auth\LoginController@logout');
});

/**
 * 管理员相关
 */
Route::prefix('api')->middleware(['auth'])->group(function() {
    Route::get('ucenter/authorities', 'Auth\AdminController@getAuthorities');
    Route::get('ucenter/authorities/latest', 'Auth\AdminController@getLatestAuthorities');
    Route::post('ucenter/avatar', 'Auth\AdminController@uploadAvatar');
    Route::put('ucenter/profile', 'Auth\AdminController@updateProfile');
    Route::put('ucenter/password', 'Auth\ResetPasswordController@reset');
});

/**
 * 权限相关
 */
Route::prefix('api')->middleware(['auth', 'acl'])->group(function() {
    Route::put('admins/{admin_id}/password', 'Auth\AdminController@resetPassword');
    Route::apiResource('admins', 'Auth\AdminController');
    Route::apiResource('roles', 'Auth\RoleController');
    Route::apiResource('authorities', 'Auth\AuthorityController');
});

/**
 * 统计相关
 */
Route::prefix('api/count')->group(function() {
    Route::get('apps/downloads', 'App\AppDownloadController@countAppDownloads');
    Route::get('home', 'HomeController@count')->middleware('auth');
    Route::get('home/orders', 'HomeController@countOrders')->middleware('auth');
    Route::get('users/money_records', 'User\UserMoneyRecordController@count')->middleware('auth');
});

/**
 * 业务相关
 */
Route::prefix('api')->middleware(['auth', 'acl'])->group(function() {
    Route::get('lotteries', 'LotteryController@index');
    Route::get('lotteries/settlement/summary', 'LotteryController@analyze');
    Route::post('lotteries/settlement', 'LotteryController@settle');

    Route::apiResource('users/withdraws', 'User\UserWithdrawController');
    Route::apiResource('users/money_records', 'User\UserMoneyRecordController');
    Route::apiResource('users/channels', 'User\UserChannelController');
    Route::apiResource('users/orders', 'User\UserOrderController');
    Route::apiResource('users/orders/{order_id}/ticket', 'User\UserOrderTicketController');
    Route::get('users/orders/{order_id}/summary', 'User\UserOrderController@analyze');
    Route::apiResource('users', 'User\UserController');

    Route::apiResource('users/{user_id}/orders', 'User\UserOrderController');
    Route::apiResource('users/{user_id}/banks', 'User\UserBankController');
    Route::apiResource('users/{user_id}/recharges', 'User\UserRechargeController');
    Route::apiResource('users/{user_id}/withdraws', 'User\UserWithdrawController');
    Route::apiResource('users/{userId}/money_records', 'User\UserMoneyRecordController');

    Route::apiResource('matches/basketball', 'Jingcai\BasketballMatchController');
    Route::apiResource('matches/football', 'Jingcai\FootballMatchController');

    Route::apiResource('balance/user_records', 'UserBalanceRecordController');

    Route::apiResource('apps/downloads', 'App\AppDownloadController');

    Route::apiResource('activities/records', 'Activity\ActivityRecordController');
    Route::apiResource('activities', 'Activity\ActivityController');
});

