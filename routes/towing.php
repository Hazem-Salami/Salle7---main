<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\towing\TowingAuthController;
use App\Http\Controllers\mail\MailController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\order\towing\TowingOrderController;

/**
 * Map routes
 */

Route::group(['prefix' => 'towing'],
    function () {
        /*********** Public routes  ***********/
        // auth
        Route::post('/register', [TowingAuthController::class, 'register'])->name('towing.register');
        Route::post('/login', [TowingAuthController::class, 'login'])->name('towing.login');

        /*********** Protected routes  ***********/
        Route::group(['middleware' => ['auth:api']],
            function () {
                // auth
                Route::post('/logout', [TowingAuthController::class, 'logout'])->name('towing.logout');

                Route::get('/activate/status/get', [TowingAuthController::class, 'getActiveStatus'])->name('towing.active.status.get');
                Route::post('/activate/toggle', [TowingAuthController::class, 'active'])->name('towing.active.toggle');

                // profiles
                Route::get('/profile/mine', [ProfileController::class, 'getProfileTowing'])->name('towing.profile.get');


                Route::get('/activate/status/get', [TowingAuthController::class, 'getActiveStatus'])->name('towing.active.status.get');
                Route::post('/activate/toggle', [TowingAuthController::class, 'active'])->name('towing.active.toggle');

                Route::group(['middleware' => ['verification.check'], 'prefix' => 'mail'],
                    function () {
                        Route::post('', [MailController::class, 'SendVerificationCode'])->name('towing.mail.send');
                        Route::post('/verify', [MailController::class, 'verification'])->name('towing.mail.verify');
                    });

                Route::group(['middleware' => ['wallet.check']], function () {
                    Route::group(['prefix' => 'orders'],
                        function () {
                            Route::get('/immediately', [TowingOrderController::class, 'getImmediatelyOrders'])->name('towing.orders.immediately.get');

                            Route::get('/immediately/{order}', [TowingOrderController::class, 'showImmediatelyOrder'])
                                ->where('order', '[0-9]+')
                                ->name('towing.orders.immediately.show');

                            Route::post('/immediately/accept/{order}', [TowingOrderController::class, 'acceptImmediatelyOrder'])
                                ->where('order', '[0-9]+')
                                ->name('towing.orders.immediately.accept');

//                        Route::post('/immediately/towing/{order}', [TowingOrderController::class, 'acceptImmediatelyOrderMaintenance'])
//                            ->where('order', '[0-9]+')
//                            ->name('towing.orders.immediately.maintenance');

                            Route::post('/immediately/finish/{order}', [TowingOrderController::class, 'acceptImmediatelyOrderFinish'])
                                ->where('order', '[0-9]+')
                                ->name('towing.orders.immediately.finish');
                        });
                });


                Route::post('/file/send', [TowingAuthController::class, 'sendAuthFiles'])->name('towing.file.send');

            });
    });
