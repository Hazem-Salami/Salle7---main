<?php

use App\Http\Controllers\auth\client\ClientAuthController;
use App\Http\Controllers\mail\MailController;
use App\Http\Controllers\map\MapController;
use App\Http\Controllers\order\client\OrderController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

/**
 * Client routes
 */

Route::group(['prefix' => 'client'],
    function () {
        /*********** Public routes  ***********/
        // auth
        Route::post('/register', [ClientAuthController::class, 'register'])->name('client.register');
        Route::post('/login', [ClientAuthController::class, 'login'])->name('client.login');

        /*********** Protected routes  ***********/
        Route::group(['middleware' => ['auth:api']],
            function () {
                // auth
                Route::post('/logout', [ClientAuthController::class, 'logout'])->name('client.logout');

                // profiles
                Route::get('/profile/mine', [ProfileController::class, 'getProfileClient'])->name('client.profile.get');

                // mail
                Route::group(['middleware' => ['verification.check'], 'prefix' => 'mail'],
                    function () {
                        Route::post('', [MailController::class, 'SendVerificationCode'])->name('client.mail.send');
                        Route::post('/verify', [MailController::class, 'verification'])->name('client.mail.verify');
                    });

                // google map routes
                Route::get('/map/home', [MapController::class, 'home'])->name('client.map.home');

                Route::group(['middleware' => ['wallet.check']], function () {

                    // workshops orders
                    Route::post('/orders/workshop/immediately/{workshop}', [OrderController::class, 'workshopOrder'])
                        ->where('workshop', '[0-9]+')
                        ->name('client.orders.workshop');

                    Route::get('/orders/workshop/immediately/{order}', [OrderController::class, 'getWorkshopOrder'])
                        ->where('order', '[0-9]+')
                        ->name('client.orders.workshop.show');

                    Route::post('/orders/workshop/immediately/pay/{order}', [OrderController::class, 'payWorkshopOrder'])
                        ->where('order', '[0-9]+')
                        ->name('client.orders.workshop.pay');

                    Route::group(['prefix' => 'orders'],
                        function () {

                            // workshops orders
                            Route::post('/workshop/immediately/{workshop}', [OrderController::class, 'workshopOrder'])
                                ->where('workshop', '[0-9]+')
                                ->name('client.orders.workshop');

                            Route::get('/workshop/immediately/{order}', [OrderController::class, 'getWorkshopOrder'])
                                ->where('order', '[0-9]+')
                                ->name('client.orders.workshop.show');

                            Route::post('/workshop/immediately/pay/{order}', [OrderController::class, 'payWorkshopOrder'])
                                ->where('order', '[0-9]+')
                                ->name('client.orders.workshop.pay');

                            // towing orders
                            Route::post('/towing/immediately/{towing}', [OrderController::class, 'towingOrder'])
                                ->where('towing', '[0-9]+')
                                ->name('client.orders.towing');

                            Route::get('/towing/immediately/{order}', [OrderController::class, 'getTowingOrder'])
                                ->where('order', '[0-9]+')
                                ->name('client.orders.towing.show');

                            Route::post('/towing/immediately/pay/{order}', [OrderController::class, 'payTowingOrder'])
                                ->where('order', '[0-9]+')
                                ->name('client.orders.towing.pay');

                            // preorders
                            Route::post('/preorders/maintenance/{workshop}', [OrderController::class, 'preorder'])
                                ->where('workshop', '[0-9]+')
                                ->name('client.maintenance.preorder');

                            Route::get('/preorders/maintenance/{order}', [OrderController::class, 'getPreorder'])
                                ->where('order', '[0-9]+')
                                ->name('client.maintenance.preorder.show');

                            Route::post('/preorders/maintenance/pay/{order}', [OrderController::class, 'payPreorder'])
                                ->where('order', '[0-9]+')
                                ->name('client.maintenance.preorder.pay');

                            Route::get('/latest/get', [OrderController::class, 'clientLatestOrders'])->name('client.order.latest.get');
                            Route::get('/towing/get', [OrderController::class, 'clientTowingOrders'])->name('client.order.towing.get');
                            Route::get('/preorder/get', [OrderController::class, 'clientPreorders'])->name('client.order.preorder.get');
                        });
                });
            });
    });
