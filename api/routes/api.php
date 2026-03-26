<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Chat\ChatController;
use App\Http\Controllers\Chat\ChatCircleController;
use App\Http\Controllers\Notifications\NotificationsController;
use App\Http\Controllers\CreditPaymentController;
use App\Http\Controllers\Event\EventController;
use \App\Http\Middleware\TrackUserActivity;
use App\Http\Controllers\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;




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

    Broadcast::routes();
    
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
        Route::post('/create-circle', [ChatController::class, 'createCircle'])->middleware(TrackUserActivity::class)->name('create-circle');

        //:::GET USERS CIRCLES:::
        Route::post('/user-circles', [ChatController::class, 'getUserCircleData'])->middleware(TrackUserActivity::class)->name('get-user-circles');

        //:::LOADS CHATS AND CIRCLES:::
        Route::post('/chat', [ChatController::class, 'getConversationChats'])->middleware(TrackUserActivity::class)->name('chat');

        //:::POST CHAT MESSAGE:::
        Route::post('/post-chat', [ChatController::class, 'postChat'])->middleware(TrackUserActivity::class)->name('post-chat');

        //:::UPDATE TYPING STATUS:::
        Route::post('/typing-status', [ChatController::class, 'updateTypingStatus'])->middleware(TrackUserActivity::class)->name('typing-status');

        //:::GET UNREAD MESSAGE COUNTS:::
        Route::get('/unread-message-counts', [ChatController::class, 'getUnreadMessageCounts'])->middleware(TrackUserActivity::class)->name('unread-message-counts');

        //:::MARK MESSAGES AS READ:::
        Route::post('/mark-messages-read', [ChatController::class, 'markMessagesAsRead'])->middleware(TrackUserActivity::class)->name('mark-messages-read');

        //:::SEARCH USERS FOR INVITE:::
        Route::post('/search-users', [ChatCircleController::class, 'searchUsers'])->middleware(TrackUserActivity::class)->name('search-users');

        //:::SEND CIRCLE CHAT INVITE:::
        Route::post('/send-circle-invite', [ChatCircleController::class, 'sendCircleChatInvite'])->middleware(TrackUserActivity::class)->name('send-circle-invite');

        //:::ACCEPT CIRCLE CHAT INVITE:::
        Route::post('/accept-circle-invite', [ChatCircleController::class, 'acceptCircleChatInvite'])->middleware(TrackUserActivity::class)->name('accept-circle-invite');

        //:::DENY CIRCLE CHAT INVITE:::
        Route::post('/deny-circle-invite', [ChatCircleController::class, 'denyCircleChatInvite'])->middleware(TrackUserActivity::class)->name('deny-circle-invite');

        //:::GET PENDING CIRCLE INVITES:::
        Route::post('/get-pending-circle-invites', [ChatCircleController::class, 'getPendingCircleInvites'])->middleware(TrackUserActivity::class)->name('get-pending-circle-invites');

        //:::GET CIRCLE MEMBERS:::
        Route::post('/get-circle-members', [ChatCircleController::class, 'getCircleMembers'])->middleware(TrackUserActivity::class)->name('get-circle-members');

        // Route::get('/get-conversations', [ChatController::class, 'getConversations'])->middleware(TrackUserActivity::class)->name('get-conversations');
        // Route::get('/poll-chat', [ChatController::class, 'pollChat'])->middleware(TrackUserActivity::class)->name('poll-chat');

        // Route::post('/chat-init-circle', [ChatController::class, 'chatInit_circle'])->middleware(TrackUserActivity::class)->name('chat-init-circle');
        // Route::post('/chat-init-network', [ChatController::class, 'chatInit_MyNetwork'])->middleware(TrackUserActivity::class)->name('chat-init-network');
        // Route::post('/check-user-conversation', [ChatController::class, 'hasAlreadyMessagedUser'])->middleware(TrackUserActivity::class)->name('check-user-conversation');
        // Route::post('/chat-new', [ChatController::class, 'chatNewConverse'])->middleware(TrackUserActivity::class)->name('chat-new');
        // Route::post('/chat-is-read', [ChatController::class, 'checkIfOtherChatIsRead_deprecated'])->middleware(TrackUserActivity::class)->name('chat-is-read');
        // Route::get('/get-unread-conversations-count', [ChatController::class, 'checkIfOtherChatIsRead'])->name('get-unread-conversations-count');
        // Route::post('/conv-click', [ChatController::class, 'chatConverseClick'])->middleware(TrackUserActivity::class)->name('conv-click');
        // Route::post('/conv-details', [ChatController::class, 'chatUserDetails'])->middleware(TrackUserActivity::class)->name('conv-details');
        // Route::post('/conv-delete', [ChatController::class, 'destoryConversation'])->middleware(TrackUserActivity::class)->name('conv-delete');
        // Route::post('/conv-count', [ChatController::class, 'getConversationCount'])->middleware(TrackUserActivity::class)->name('conv-count');

        //:::WEB CHAT CATEGORY GROUP ROUTES:::
        // Route::post('/add-chat-category-group', [ChatController::class, 'addChatCategoryGroup'])->middleware(TrackUserActivity::class)->name('add-chat-category-group');
        // Route::post('/update-chat-category-group', [ChatController::class, 'updateChatCategoryGroup'])->middleware(TrackUserActivity::class)->name('update-chat-category-group');
        // Route::post('/delete-chat-category-group', [ChatController::class, 'deleteChatCategoryGroup'])->middleware(TrackUserActivity::class)->name('delete-chat-category-group');

        //:::WEB CHAT CATEGORY GROUP TRACKING ROUTES:::
        // Route::post('/add-convo-to-convo-category', [ChatController::class, 'addConvoToConvoCategory'])->middleware(TrackUserActivity::class)->name('add-convo-to-convo-category');
        // Route::post('/remove-convo-from-convo-category', [ChatController::class, 'removeConvoFromConvoCategory'])->middleware(TrackUserActivity::class)->name('remove-convo-from-convo-category');
        // Route::post('/reset-conversations-to-default-conversation-category', [ChatController::class, 'resetConversationsToDefaultConversationCategory'])->middleware(TrackUserActivity::class)->name('reset-conversations-to-default-conversation-category');
        // Route::post('/update-expand-state', [ChatController::class, 'updateExpandState'])->middleware(TrackUserActivity::class)->name('update-expand-state');
    });

    //:::::::::::::::::: NOTIFICATION ROUTES::::::::::::::::::
    Route::group([], function () {
        //:::GET NOTIFICATIONS:::
        Route::post('/notifications', [NotificationsController::class, 'getNotifications'])->middleware(TrackUserActivity::class)->name('get-notifications');

        //:::GET NOTIFICATION BY ID:::
        Route::post('/notification', [NotificationsController::class, 'getNotificationById'])->middleware(TrackUserActivity::class)->name('get-notification');

        //:::MARK NOTIFICATION AS READ:::
        Route::post('/notification/mark-read', [NotificationsController::class, 'markAsRead'])->middleware(TrackUserActivity::class)->name('mark-notification-read');

        //:::GET UNREAD COUNT:::
        Route::get('/notifications/unread-count', [NotificationsController::class, 'getUnreadCount'])->middleware(TrackUserActivity::class)->name('get-unread-count');
    });

    //:::::::::::::::::: CALENDAR / EVENT ROUTES::::::::::::::::::
    Route::group([], function () {
        Route::get('/calendar/events',       [EventController::class, 'index'])->name('calendar.events.index');
        Route::get('/calendar/events/{id}',  [EventController::class, 'show'])->name('calendar.events.show');
        Route::post('/calendar/events',      [EventController::class, 'store'])->name('calendar.events.store');
        Route::put('/calendar/events/{id}',  [EventController::class, 'update'])->name('calendar.events.update');
        Route::delete('/calendar/events/{id}', [EventController::class, 'destroy'])->name('calendar.events.destroy');
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


