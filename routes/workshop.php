<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\workshop\WorkshopAuthController;
use App\Http\Controllers\auth\workshop\WorkshopTimesController;
use App\Http\Controllers\mail\MailController;
use App\Http\Controllers\order\workshop\WorkshopOrderController;
use App\Http\Controllers\Profile\ProfileController;

/**
 * Map routes
 */

Route::group(['prefix' => 'workshop'],
    function () {
        /*********** Public routes  ***********/
        // auth
        Route::post('/register', [WorkshopAuthController::class, 'register'])->name('workshop.register');
        Route::post('/login', [WorkshopAuthController::class, 'login'])->name('workshop.login');

        /*********** Protected routes  ***********/
        Route::group(['middleware' => ['auth:api']],
            function () {
                // auth
                Route::post('/logout', [WorkshopAuthController::class, 'logout'])->name('workshop.logout');

                // profiles
                Route::get('/profile/mine', [ProfileController::class, 'getProfileWorkshop'])->name('workshop.profile.get');

                Route::group(['middleware' => ['wallet.check']], function () {
                    Route::get('/activate/status/get', [WorkshopAuthController::class, 'getActiveStatus'])->name('workshop.active.status.get');
                    Route::post('/activate/toggle', [WorkshopAuthController::class, 'active'])->name('workshop.active.toggle');

                    Route::group(['prefix' => 'orders'],
                        function () {
                            Route::get('/immediately', [WorkshopOrderController::class, 'getImmediatelyOrders'])->name('workshop.orders.immediately.get');

                            Route::get('/immediately/{order}', [WorkshopOrderController::class, 'showImmediatelyOrder'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.orders.immediately.show');

                            Route::post('/immediately/accept/{order}', [WorkshopOrderController::class, 'acceptImmediatelyOrder'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.orders.immediately.accept');

                            Route::post('/immediately/maintenance/{order}', [WorkshopOrderController::class, 'acceptImmediatelyOrderMaintenance'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.orders.immediately.maintenance');

                            Route::post('/immediately/finish/{order}', [WorkshopOrderController::class, 'acceptImmediatelyOrderFinish'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.orders.immediately.finish');
                        });

                    Route::group(['prefix' => 'preorders'],
                        function () {
                            Route::get('/', [WorkshopOrderController::class, 'getPreorders'])->name('workshop.preorders.get');

                            Route::get('/{order}', [WorkshopOrderController::class, 'showPreorder'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.preorders.show');

                            Route::post('/accept/{order}', [WorkshopOrderController::class, 'acceptPreorder'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.preorders.accept');

                            Route::post('/finish/{order}', [WorkshopOrderController::class, 'acceptPreorderFinish'])
                                ->where('order', '[0-9]+')
                                ->name('workshop.preorders.finish');
                        });
                });

                Route::group(['middleware' => ['verification.check'], 'prefix' => 'mail'],
                    function () {
                        Route::post('', [MailController::class, 'SendVerificationCode'])->name('workshop.mail.send');
                        Route::post('/verify', [MailController::class, 'verification'])->name('workshop.mail.verify');
                    });

                Route::post('/file/send', [WorkshopAuthController::class, 'sendAuthFiles'])->name('workshop.file.send');

                Route::group(['prefix' => 'times'],
                    function () {
                        Route::post('/{workshop}', [WorkshopTimesController::class, 'setTimes'])->name('workshop.times.set');
                        Route::get('/{workshop}', [WorkshopTimesController::class, 'getTimes'])->name('workshop.times.get');
                    });
            });
    });
