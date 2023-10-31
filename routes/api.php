<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Wallet\ChargesController;
use App\Http\Controllers\category\CategoryController;
use App\Http\Controllers\product\ProductController;
use App\Http\Controllers\mail\MailController;
use App\Http\Controllers\Profile\ProfileController;
use App\Http\Controllers\ComplaintsAndSuggestions\ComplaintsSuggestionsController;
use App\Http\Controllers\order\purchase\PurchaseOrderController;
use App\Http\Controllers\AccountRecovery\AccountRecoveryController;
use App\Http\Controllers\Feedback\FeedbackController;

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

Route::get('/users/notify', [ProfileController::class, 'push']);

Route::group(['middleware' => ['auth:api', 'user.block']],
    function () {

        // profiles
        Route::get('/profile/user/{user}', [ProfileController::class, 'showProfileClient'])
            ->where(['user' => '[0-9]+'])
            ->name('users.profile.show.client');

        Route::get('/profile/workshop/{workshop}', [ProfileController::class, 'showProfileWorkshop'])
            ->where(['workshop' => '[0-9]+'])
            ->name('users.profile.show.workshop');

        Route::get('/profile/towing/{user}', [ProfileController::class, 'showProfileTowing'])
            ->where(['user' => '[0-9]+'])
            ->name('users.profile.show.towing');

        Route::group(['prefix' => 'users'],
            function () {

                //wallets
                Route::get('/wallets/status', [ChargesController::class, 'getStatus'])->name('users.wallets.status');
                Route::get('/wallets/balance', [ChargesController::class, 'getAmount'])->name('users.wallets.balance');
                Route::post('/wallets/new', [ChargesController::class, 'createWallet'])->name('users.wallets.new');

                Route::get('/isverify', [MailController::class, 'isVerified'])
                    ->name('users.check.verify')
                    ->middleware('verification.check');

                // category

                Route::group(['middleware' => ['wallet.check'],'prefix' => 'category'],
                    function () {

                        Route::get('/get', [CategoryController::class, 'getRootCategories'])->name('users.category.get');

                        Route::group(['middleware' => ['category.existence']],
                            function () {

                                Route::get('/{id}/get', [CategoryController::class, 'getChildCategories'])
                                    ->where(['id' => '[0-9]+'])
                                    ->name('users.category.child.get');

                                //products
                                Route::get('/{id}/product/get', [ProductController::class, 'getProductsCategory'])
                                    ->where(['id' => '[0-9]+'])
                                    ->name('users.category.product.get');

                            });

                    });

                Route::get('/product/last/get', [ProductController::class, 'getLastProduct'])
                    ->name('users.product.last.get');

                Route::get('/product/filter', [ProductController::class, 'filter'])
                    ->name('users.product.filter');

                Route::get('/search/{search}', [ProfileController::class, 'search'])
                    ->name('users.search');

                Route::group(['prefix' => 'purchase/order'],
                    function () {
                        Route::post('/send', [PurchaseOrderController::class, 'sendPurchaseOrder'])
                            ->name('users.purchase');

                        Route::get('/waiting/get', [PurchaseOrderController::class, 'getWaitingPurchaseOrders'])->name('store.purchase.order.waiting.get');
                        Route::get('/accepted/get', [PurchaseOrderController::class, 'getAcceptedPurchaseOrders'])->name('store.purchase.order.accepted.get');
                        Route::get('/rejected/get', [PurchaseOrderController::class, 'getRejectedPurchaseOrders'])->name('store.purchase.order.rejected.get');

                        Route::put('/done/{purchaseOrder}', [PurchaseOrderController::class, 'donePurchaseOrders'])
                            ->name('users.purchase.order.done');
                    });
                //Complaints And Suggestions
                Route::post('/suggestion/add', [ComplaintsSuggestionsController::class, 'addSuggestion'])->name('users.suggestion.add');
                Route::post('/complaint/add', [ComplaintsSuggestionsController::class, 'addComplaint'])->name('users.complaint.add');
                Route::get('/complaints', [ComplaintsSuggestionsController::class, 'getComplaints'])->name('users.complaints.get');
                Route::get('/suggestions', [ComplaintsSuggestionsController::class, 'getSuggestions'])->name('users.suggestions.get');

                Route::post('/feedback/add', [FeedbackController::class, 'addFeedback'])->name('users.feedback.add');
                Route::get('/feedback/get', [FeedbackController::class, 'getFeedback'])->name('users.feedback.get');
            });
    });

Route::group(['prefix' => 'users/mail/'],
    function () {
        Route::post('', [AccountRecoveryController::class, 'sendResetPasswordCode'])->name('users.mail.send');
        Route::post('/reset', [AccountRecoveryController::class, 'resetPassword'])->name('users.mail.reset');
    });
