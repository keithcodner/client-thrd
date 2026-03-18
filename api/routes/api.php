<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\CreditPaymentController;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



// Public Routes
Route::middleware("guest")->group(function () {
   Route::post('/register', [RegisteredUserController::class, 'store'])
    ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Authenticated Routes
Route::middleware(['auth:sanctum'])->group(function () {
    
    //:::::::::::::::::: LOGOUT ROUTES::::::::::::::::::
    Route::group([], function () {
        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
    });

    //:::::::::::::::::: FETCH PROFILE ROUTES::::::::::::::::::
    Route::group([], function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });

    //:::::::::::::::::: CHAT ROUTES::::::::::::::::::
    Route::group([], function () {
        

        //:::CREATES CIRCLES:::
        Route::post('/create-circle', [ChatController::class, 'createCircle'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('create-circle');

        //:::LOADS CHATS AND CIRCLES:::
        Route::post('/chat', [ChatController::class, 'ChatIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat');

        // Route::get('/get-conversations', [ChatController::class, 'getConversations'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-conversations');
        // Route::get('/poll-chat', [ChatController::class, 'pollChat'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('poll-chat');

        // Route::post('/chat-init-circle', [ChatController::class, 'chatInit_circle'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-init-circle');
        // Route::post('/chat-init-network', [ChatController::class, 'chatInit_MyNetwork'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-init-network');
        // Route::post('/check-user-conversation', [ChatController::class, 'hasAlreadyMessagedUser'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-user-conversation');
        // Route::post('/chat-new', [ChatController::class, 'chatNewConverse'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-new');
        // Route::post('/chat-is-read', [ChatController::class, 'checkIfOtherChatIsRead_deprecated'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-is-read');
        // Route::get('/get-unread-conversations-count', [ChatController::class, 'checkIfOtherChatIsRead'])->name('get-unread-conversations-count');
        // Route::post('/post-chat', [ChatController::class, 'postChat'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('post-chat');
        // Route::post('/conv-click', [ChatController::class, 'chatConverseClick'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-click');
        // Route::post('/conv-details', [ChatController::class, 'chatUserDetails'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-details');
        // Route::post('/conv-delete', [ChatController::class, 'destoryConversation'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-delete');
        // Route::post('/conv-count', [ChatController::class, 'getConversationCount'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-count');

        //:::WEB CHAT CATEGORY GROUP ROUTES:::
        // Route::post('/add-chat-category-group', [ChatController::class, 'addChatCategoryGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-chat-category-group');
        // Route::post('/update-chat-category-group', [ChatController::class, 'updateChatCategoryGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-chat-category-group');
        // Route::post('/delete-chat-category-group', [ChatController::class, 'deleteChatCategoryGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('delete-chat-category-group');

        //:::WEB CHAT CATEGORY GROUP TRACKING ROUTES:::
        // Route::post('/add-convo-to-convo-category', [ChatController::class, 'addConvoToConvoCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-convo-to-convo-category');
        // Route::post('/remove-convo-from-convo-category', [ChatController::class, 'removeConvoFromConvoCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('remove-convo-from-convo-category');
        // Route::post('/reset-conversations-to-default-conversation-category', [ChatController::class, 'resetConversationsToDefaultConversationCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('reset-conversations-to-default-conversation-category');
        // Route::post('/update-expand-state', [ChatController::class, 'updateExpandState'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-expand-state');
    });

    //:::::::::::::::::: TEMPLATE ROUTES::::::::::::::::::
    Route::group([], function () {

    });

    //:::::::::::::::::: PAYMENT ROUTES::::::::::::::::::
    Route::group([], function () {
        //Stripe to handle payment intent creation (CSRF Exempt)
        Route::post('/payment/create-payment-intent', 
        [CreditPaymentController::class, 'createPaymentIntent']);

        //Stripe to handle successful payment (CSRF Exempt)
        Route::post('/payment/handle-payment-success', 
    [CreditPaymentController::class, 'handlePaymentSuccess']);
    });

});


