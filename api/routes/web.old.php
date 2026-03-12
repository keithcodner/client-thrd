<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\CheckAccessController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Middleware\AdminRoleMiddleware;

use App\Http\Controllers\Core\ChatController;
use App\Http\Controllers\Core\ChangePasswordController;
use App\Http\Controllers\Core\ContactController;
use App\Http\Controllers\Core\EditItemController;
use App\Http\Controllers\Core\HelpVerifyImagesController;
use App\Http\Controllers\Core\ItemController;
use App\Http\Controllers\Core\ItemViewController;
use App\Http\Controllers\Core\LocationController;
use App\Http\Controllers\Core\ManageItemController;
use App\Http\Controllers\Core\NewsCircleFeedController;
use App\Http\Controllers\Core\NotificationsController;
use App\Http\Controllers\Core\ReportController;
use App\Http\Controllers\Core\RankingController;
use App\Http\Controllers\Core\ViewCategoryController;
use App\Http\Controllers\Core\ViewPostsController;
use App\Http\Controllers\Core\WishListController;
use App\Http\Controllers\Core\WishListViewsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\E_Commerce\CartController;
use App\Http\Controllers\E_Commerce\CheckoutController;
use App\Http\Controllers\E_Commerce\ManagePurchaseController;
use App\Http\Controllers\E_Commerce\Products\ProductsController;
use App\Http\Controllers\Events\CalendarMonthController;
use App\Http\Controllers\Front\WelcomeController;
use App\Http\Controllers\HomeDashboardController;
use App\Http\Controllers\MyNetwork\MyNetworkHelpers;
use App\Http\Controllers\MyNetwork\MyNetworkListController;
use App\Http\Controllers\MyNetwork\MyNetworkProfileController;
use App\Http\Controllers\MyNetwork\MyNetworkGroupController;
use App\Http\Controllers\MyNetwork\MyNetworkRequestsController;
use App\Http\Controllers\MyNetwork\MyNetworkSearchController;
use App\Http\Controllers\MyNetwork\MyNetworkHashTagController;
use App\Http\Controllers\NewsArticles\NewsArticlesController;
use App\Http\Controllers\Offers\OffersController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Search\AgoraBrowseController;
use App\Http\Controllers\Search\SearchController;

use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Support\SupportController;
use App\Http\Controllers\Support\HelpSupportController;
use App\Http\Controllers\Circle\ManageCircleTransactionController;
use App\Http\Controllers\Circle\CircleInsightController;
use App\Http\Controllers\Vendor\Voyager\AdminAnalyticsController;
use App\Http\Controllers\Vendor\Voyager\AdminDashboardController;
use App\Http\Controllers\Vendor\Voyager\AdminReportsController;
use App\Http\Controllers\Vendor\Voyager\AdminSupportController;
use App\Http\Controllers\Vendor\Voyager\ContactInteractionController;
use App\Http\Controllers\Vendor\Voyager\ThrdSettingsController;
use App\Http\Controllers\Vendor\Voyager\ManageImageContentController;
use App\Http\Controllers\Vendor\Voyager\ManageProductsController;
use App\Http\Controllers\Vendor\Voyager\ManageRankingPermissionsController;
use App\Http\Controllers\Vendor\Voyager\ManageUserAccountsController;
use App\Http\Controllers\Vendor\Voyager\ValidateCommentPolicyController;
use App\Http\Controllers\Vendor\Voyager\ValidateReportedPostsController;
use App\Http\Controllers\Vendor\Voyager\ValidateCircleImagesController;

use \App\Http\Middleware\CheckAccess;


use App\Http\Controllers\Vendor\Voyager\ValidateUserCredentialsController;
use App\Http\Controllers\Vendor\Voyager\ViewSubscriptionRankingController;
use App\Livewire\Counter;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;






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


//:::::::::::::::::LANDING (No Protection)::::::::::::::::::::

Route::get('/landing', function () {
    return Inertia::render('ToolPages/ProductionPasswordProtectedPage');
})->name('landing');

Route::post('/check-access', [CheckAccessController::class, 'checkAccess'])->name('check-access');

//:::::::::::::::::PASSWORD PROTECTED ROUTES (Production)::::::::::::::::::::

Route::middleware([CheckAccess::class])->group(function(){

    //:::::::::::::::::GUEST PAGES::::::::::::::::::::
    Route::get('/', function () {
        return Inertia::render('Hero/HeroPage', [
            //'canLogin' => Route::has('login'),
            //'canRegister' => Route::has('register'),
            //'laravelVersion' => Application::VERSION,
            //'phpVersion' => PHP_VERSION,
        ]);
    });

    
    //:::::::::::::::::FRONT PAGES::::::::::::::::::::
    Route::group([], function () {

        ////::::OAUTH ROUTES:::::
        Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirectToGoogle']);
        Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
        Route::post('/api/auth/google', [GoogleAuthController::class, 'handleGoogleToken']);


        //::::COMMON SINGLE PAGE ROUTES:::::
        Route::group([], function () {
        Route::get('/welcome-temp', [WelcomeController::class, 'index'])->name('welcome-temp');

            Route::get('/about', function () {
                return Inertia::render('Front/About');
            })->name('about');

            Route::get('/membership', function () {
                return Inertia::render('Front/GuestMembership');
            })->name('membership');

            Route::get('/accessibility', function () {
                return Inertia::render('Front/Accessibility');
            })->name('accessibility');

            Route::get('/privacy', function () {
                return Inertia::render('Front/Privacy');
            })->name('privacy');

            Route::get('/terms', function () {
                return Inertia::render('Front/Terms');
            })->name('terms');

            Route::get('/advertise', function () {
                return Inertia::render('Front/Advertise');
            })->name('advertise');
        });

        //::::CONTACT ROUTES:::::
        Route::group([], function () {
        Route::post('/contact-us', [ContactController::class, 'store'])->name('contact-us');
        Route::get('/contact', function () {
                return Inertia::render('Front/Contact');
            })->name('contact');
        });

        //::::NEWS ARTICLE FRONT ROUTES:::::
        Route::group([], function () {
        Route::get('/front-end-load-articles', [NewsArticlesController::class, 'frontEndLoadArticles']);

            Route::get('/front-end-load-one-article/{id}', [NewsArticlesController::class, 'frontEndLoadOneArticle']);

            Route::get('/trending-articles', [NewsArticlesController::class, 'getTrendingArticles']);

            Route::get('/trending-groups', [MyNetworkGroupController::class, 'getTrendingGroups']);

            Route::get('/news', function () {
                return Inertia::render('Front/News');
            })->name('news');

            Route::get('/news-post/{id}', function ($id) {
                return Inertia::render('Front/NewsPost', [
                    'id' => $id
                ]);
            })->name('news-post');
        });

        //::::NEWSLETTER ROUTES:::::
        Route::group([], function () {
            Route::post('/newsletter-email', [ForgotPasswordController::class, 'newsletterEmail'])->name('newsletter-email');
        });

        //::::FAQ ROUTES:::::
        Route::group([], function () {
            Route::get('/faq', function () {
                return Inertia::render('Front/FAQ');
            })->name('faq');
        });

        //::::PUBLIC AGORA/BROWSE & SEARCH  ROUTES:::::
        Route::group([], function () {

            //Browse
            Route::get('/browse',[SearchController::class, 'searchForProductAndServiceseOnAgoraRequestGuest'])->name('browse');

            Route::get('/search', function () {
                return Inertia::render('Front/Search');
            })->name('search');

            // Guest Search Route
            Route::get('/p-search',[SearchController::class, 'searchForProductAndServiceseOnAgoraRequestGuest'])->name('p-search');

            // Guest Category Search Route
            Route::get('/cp-search',[SearchController::class, 'searchForProductAndServiceseOnAgoraRequestBySubCategoryGuest'])->name('cp-search');

            // Search History Tracking (accessible to both guests and authenticated users)
            Route::post('/record-search-history',[SearchController::class, 'recordSearchHistory'])->name('record-search-history-public');
        });

        //::::TEMPLATE ROUTES:::::
        Route::group([], function () {
        
        });
    });

    //:::::::::::::::::GUEST SEARCH PAGES::::::::::::::::::::
    Route::group([], function () {
        

        // Test route for debugging
        Route::get('/test-guest-search', function() {
            return response()->json(['message' => 'Guest search route is working']);
        });

        Route::get('/dashboard', function () {
            return Inertia::render('Dashboard');
        })->middleware(['auth', 'verified'])->name('dashboard');

        Route::get('/settings', function () {
            return Inertia::render('Settings/AccountSettings');
        })->name('settings');
        

    });

    //::::ADMIN ROUTES:::::
    Route::group(['prefix' => 'admin'], function () {
        Route::middleware(['auth', AdminRoleMiddleware::class])->group(function () {

            //::::ADMIN DASHBOARD ROUTES:::::
            Route::group([], function () {
                Route::get('/admin-dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
                Route::get('/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics');
                Route::get('/reports', [AdminReportsController::class, 'index'])->name('admin.reports');
            });

            //::::PRODUCT MANAGEMENT ROUTES:::::
            Route::group([], function () {
                Route::get('/products', [ManageProductsController::class, 'index'])->name('admin.products');
                Route::post('/products', [ManageProductsController::class, 'store'])->name('admin.products.store');
                Route::get('/products/{product}', [ManageProductsController::class, 'show'])->name('admin.products.show');
                Route::put('/products/{product}', [ManageProductsController::class, 'update'])->name('admin.products.update');
                Route::delete('/products/{product}', [ManageProductsController::class, 'destroy'])->name('admin.products.destroy');
                Route::post('/products/update-status', [ManageProductsController::class, 'updateProductStatus'])->name('admin.products.update-status');
                Route::get('/products/users-dropdown', [ManageProductsController::class, 'getUsersForDropdown'])->name('admin.products.users-dropdown');
            });

            //::::USER MANAGEMENT ROUTES:::::
            Route::group([], function () {
                Route::get('/user-accounts', [ManageUserAccountsController::class, 'index'])->name('admin.user-accounts');
                Route::post('/update-account-status', [ManageUserAccountsController::class, 'updateAccountStatus'])->name('admin.update-account-status');
                Route::post('/retrieve-credential', [ManageUserAccountsController::class, 'retrieveCredential'])->name('admin.retrieve-credential');
                Route::post('/update-credential-status', [ManageUserAccountsController::class, 'updateCredentialStatus'])->name('admin.update-credential-status');
                Route::post('/suspend-account', [ManageUserAccountsController::class, 'suspendAccount'])->name('admin.suspend-account');
                Route::post('/check-if-active', [ManageUserAccountsController::class, 'checkIfActive'])->name('admin.check-if-active');
                Route::post('/check-if-suspended', [ManageUserAccountsController::class, 'checkIfSuspended'])->name('admin.check-if-suspended');
            });

            //::::KNOWLEDGE BASE ROUTES:::::
            Route::group(['prefix' => 'knowledge-base'], function () {
                Route::get('/', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'index'])->name('admin.knowledge-base');
                Route::post('/search', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'searchDocuments'])->name('admin.knowledge-base.search');
                Route::post('/pages', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'addOrUpdatePage'])->name('admin.knowledge-base.pages');
                Route::post('/sections', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'addOrUpdateSection'])->name('admin.knowledge-base.sections');
                Route::post('/page-info', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'getPageInfo'])->name('admin.knowledge-base.page-info');
                Route::post('/section-info', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'getSectionInfo'])->name('admin.knowledge-base.section-info');
                Route::post('/max-order', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'getMaxSectionOrder'])->name('admin.knowledge-base.max-order');
                Route::post('/inactive-docs', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'updateInactiveDocumentsDropDown'])->name('admin.knowledge-base.inactive-docs');
                Route::post('/set-docs', [App\Http\Controllers\Vendor\Voyager\ThrdKnowledgebaseController::class, 'updateSetDocumentsDropDown'])->name('admin.knowledge-base.set-docs');
            });

            //::::CONTENT MANAGEMENT ROUTES:::::
            Route::group(['prefix' => 'content-management'], function () {
                
                //::::Circle IMAGE VALIDATION ROUTES:::::
                Route::group(['prefix' => 'Circle-images'], function () {
                    Route::get('/', [ValidateCircleImagesController::class, 'index'])->name('admin.Circle-images.index');
                    Route::post('/get-incident-item', [ValidateCircleImagesController::class, 'retrieveIncidentItem'])->name('admin.Circle-images.get-item');
                    Route::post('/update-item-status', [ValidateCircleImagesController::class, 'itemStatusUpdate'])->name('admin.Circle-images.update-item-status');
                    Route::post('/update-incident-status', [ValidateCircleImagesController::class, 'incidentStatusUpdate'])->name('admin.Circle-images.update-incident-status');
                    Route::post('/get-incident-comments', [ValidateCircleImagesController::class, 'retreiveIncidentComment'])->name('admin.Circle-images.get-comments');
                    Route::post('/add-incident-comment', [ValidateCircleImagesController::class, 'addIncidentComment'])->name('admin.Circle-images.add-comment');
                    Route::post('/update-incident-comment', [ValidateCircleImagesController::class, 'updateIncidentComment'])->name('admin.Circle-images.update-comment');
                    Route::post('/delete-incident-comment', [ValidateCircleImagesController::class, 'deleteIncidentComment'])->name('admin.Circle-images.delete-comment');
                });

                //::::REPORTED POSTS VALIDATION ROUTES:::::
                Route::group(['prefix' => 'reported-posts'], function () {
                    Route::get('/', [ValidateReportedPostsController::class, 'index'])->name('admin.reported-posts.index');
                    Route::post('/get-incident-post', [ValidateReportedPostsController::class, 'retrieveIncidentPost'])->name('admin.reported-posts.get-post');
                    Route::post('/update-post-status', [ValidateReportedPostsController::class, 'postStatusUpdate'])->name('admin.reported-posts.update-post-status');
                    Route::post('/update-incident-status', [ValidateReportedPostsController::class, 'incidentStatusUpdate'])->name('admin.reported-posts.update-incident-status');
                    Route::post('/get-incident-comments', [ValidateReportedPostsController::class, 'retreiveIncidentComment'])->name('admin.reported-posts.get-comments');
                    Route::post('/add-incident-comment', [ValidateReportedPostsController::class, 'addIncidentComment'])->name('admin.reported-posts.add-comment');
                    Route::post('/update-incident-comment', [ValidateReportedPostsController::class, 'updateIncidentComment'])->name('admin.reported-posts.update-comment');
                    Route::post('/delete-incident-comment', [ValidateReportedPostsController::class, 'deleteIncidentComment'])->name('admin.reported-posts.delete-comment');
                });

                //::::GENERAL CONTENT VALIDATION ROUTES:::::
                Route::group([], function () {
                    Route::get('/image-content', [ManageImageContentController::class, 'index'])->name('admin.image-content.index');
                    Route::get('/user-credentials', [ValidateUserCredentialsController::class, 'index'])->name('admin.user-credentials.index');
                    Route::get('/comment-policy', [ValidateCommentPolicyController::class, 'index'])->name('admin.comment-policy.index');
                    Route::get('/content-appeals', [ManageRankingPermissionsController::class, 'index'])->name('admin.content-appeals.index');
                });
            });

            //::::SUPPORT ROUTES:::::
            Route::group(['prefix' => 'support'], function () {
                
                //::::CONTACT INTERACTIONS ROUTES:::::
                Route::group(['prefix' => 'contact-interactions'], function () {
                    Route::get('/', [ContactInteractionController::class, 'index'])->name('admin.contact-interactions.index');
                    Route::post('/reply', [ContactInteractionController::class, 'reply'])->name('admin.contact-interactions.reply');
                    Route::post('/archive', [ContactInteractionController::class, 'archive'])->name('admin.contact-interactions.archive');
                    Route::post('/delete', [ContactInteractionController::class, 'destroy'])->name('admin.contact-interactions.delete');
                    Route::post('/get-contact', [ContactInteractionController::class, 'getContact'])->name('admin.contact-interactions.get-contact');
                });

                //::::Thrd SETTINGS ROUTES:::::
                Route::group(['prefix' => 'Thrd-settings'], function () {
                    Route::get('/', [ThrdSettingsController::class, 'index'])->name('admin.Thrd-settings.index');
                    Route::post('/update', [ThrdSettingsController::class, 'update'])->name('admin.Thrd-settings.update');
                    Route::post('/get-setting', [ThrdSettingsController::class, 'getSetting'])->name('admin.Thrd-settings.get-setting');
                    Route::post('/reset-setting', [ThrdSettingsController::class, 'resetSetting'])->name('admin.Thrd-settings.reset-setting');
                    Route::post('/create-setting', [ThrdSettingsController::class, 'createSetting'])->name('admin.Thrd-settings.create-setting');
                    Route::get('/export', [ThrdSettingsController::class, 'exportSettings'])->name('admin.Thrd-settings.export');
                });

                //::::SUPPORT REQUESTS ROUTES:::::
                Route::group(['prefix' => 'support-requests'], function () {
                    Route::get('/', [AdminSupportController::class, 'supportRequestsIndex'])->name('admin.support-requests.index');
                    Route::post('/reply', [AdminSupportController::class, 'reply'])->name('admin.support-requests.reply');
                    Route::post('/update-status', [AdminSupportController::class, 'updateStatus'])->name('admin.support-requests.update-status');
                    Route::post('/assign', [AdminSupportController::class, 'assign'])->name('admin.support-requests.assign');
                    Route::post('/create', [AdminSupportController::class, 'create'])->name('admin.support-requests.create');
                    Route::post('/delete', [AdminSupportController::class, 'delete'])->name('admin.support-requests.delete');
                    Route::get('/{id}/comments', [AdminSupportController::class, 'getComments'])->name('admin.support-requests.comments');
                });
            });

            //::::RANKING MANAGEMENT ROUTES:::::
            Route::group(['prefix' => 'ranking-management'], function () {
                
                //::::RANKING CATALOG ROUTES:::::
                Route::group(['prefix' => 'ranking-catalog'], function () {
                    Route::get('/', [App\Http\Controllers\Vendor\Voyager\ManageRankingCatalogController::class, 'index'])->name('admin.ranking-catalog.index');
                    Route::post('/', [App\Http\Controllers\Vendor\Voyager\ManageRankingCatalogController::class, 'store'])->name('admin.ranking-catalog.store');
                    Route::put('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRankingCatalogController::class, 'update'])->name('admin.ranking-catalog.update');
                    Route::delete('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRankingCatalogController::class, 'destroy'])->name('admin.ranking-catalog.destroy');
                    Route::patch('/{id}/toggle-status', [App\Http\Controllers\Vendor\Voyager\ManageRankingCatalogController::class, 'toggleStatus'])->name('admin.ranking-catalog.toggle-status');
                });

                //::::RANKING GROUPS ROUTES:::::
                Route::group(['prefix' => 'ranking-groups'], function () {
                    Route::get('/', [App\Http\Controllers\Vendor\Voyager\ManageRankingGroupsController::class, 'index'])->name('admin.ranking-groups.index');
                    Route::post('/', [App\Http\Controllers\Vendor\Voyager\ManageRankingGroupsController::class, 'store'])->name('admin.ranking-groups.store');
                    Route::put('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRankingGroupsController::class, 'update'])->name('admin.ranking-groups.update');
                    Route::delete('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRankingGroupsController::class, 'destroy'])->name('admin.ranking-groups.destroy');
                    Route::patch('/{id}/toggle-status', [App\Http\Controllers\Vendor\Voyager\ManageRankingGroupsController::class, 'toggleStatus'])->name('admin.ranking-groups.toggle-status');
                });

                //::::RANKING PERMISSIONS ROUTES:::::
                Route::group(['prefix' => 'ranking-permissions'], function () {
                    Route::get('/', [App\Http\Controllers\Vendor\Voyager\ManageRankingPermissionsController::class, 'index'])->name('admin.ranking-permissions.index');
                    Route::post('/', [App\Http\Controllers\Vendor\Voyager\ManageRankingPermissionsController::class, 'store'])->name('admin.ranking-permissions.store');
                    Route::put('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRankingPermissionsController::class, 'update'])->name('admin.ranking-permissions.update');
                    Route::delete('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRankingPermissionsController::class, 'destroy'])->name('admin.ranking-permissions.destroy');
                    Route::patch('/{id}/toggle-status', [App\Http\Controllers\Vendor\Voyager\ManageRankingPermissionsController::class, 'toggleStatus'])->name('admin.ranking-permissions.toggle-status');
                });

                //::::USER RANKINGS ROUTES:::::
                Route::group(['prefix' => 'user-rankings'], function () {
                    Route::get('/', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'index'])->name('admin.user-rankings.index');
                    Route::post('/', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'store'])->name('admin.user-rankings.store');
                    Route::put('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'update'])->name('admin.user-rankings.update');
                    Route::delete('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'destroy'])->name('admin.user-rankings.destroy');
                    Route::get('/{id}/history', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'getRankingHistory'])->name('admin.user-rankings.history');
                    Route::post('/bulk-status', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'bulkUpdateStatus'])->name('admin.user-rankings.bulk-status');
                    Route::post('/{id}/recalculate', [App\Http\Controllers\Vendor\Voyager\ManageRanksController::class, 'recalculateRanking'])->name('admin.user-rankings.recalculate');
                });
            });

            //::::EVENT MANAGEMENT ROUTES:::::
            Route::group(['prefix' => 'event-management'], function () {
                Route::get('/', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'index'])->name('admin.event-management.index');
                Route::post('/', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'store'])->name('admin.event-management.store');
                Route::put('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'update'])->name('admin.event-management.update');
                Route::delete('/{id}', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'destroy'])->name('admin.event-management.destroy');
                Route::get('/calendar-events', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'getCalendarEvents'])->name('admin.event-management.calendar-events');
                Route::get('/user-events/{userId}', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'getUserEvents'])->name('admin.event-management.user-events');
                Route::post('/bulk-status', [App\Http\Controllers\Vendor\Voyager\ManageAdminEventController::class, 'bulkUpdateStatus'])->name('admin.event-management.bulk-status');
            });

        });
    });

}); // End CheckAccess middleware group

//:::::::::::::::::USER AUTHENTICATED ROUTES::::::::::::::::::::
Route::middleware('auth')->group(function () {

    //:::::::::::::::::PROFILE PAGES::::::::::::::::::::
    Route::get('/profile', [ProfileController::class, 'edit'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('profile.update');
    Route::post('/update-profile', [ProfileController::class, 'updateProfile'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-profile');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('profile.destroy');

    //:::::::PASSWORD CHANGE ROUTES:::::
    Route::post('/change-password', [ChangePasswordController::class, 'changePassword'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('change-password');

    //:::::::EMAIL VALIDATION ROUTES:::::
    Route::post('/check-email-availability', [ProfileController::class, 'checkEmailAvailability'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-email-availability');

    //:::::::LOCATION ROUTES:::::
    Route::get('/get-cities/{country}', [LocationController::class, 'getCities'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-cities');
    Route::get('/search-cities/{country}', [LocationController::class, 'searchCities'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('search-cities');

    //:::::::LOGOUT ROUTES:::::
    Route::get('/logout', [LogoutController::class, 'index'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('logout');
    Route::post('/logout', [LogoutController::class, 'store'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('logout');

    //:::: DASHBOARD ROUTES:::::
    Route::group([], function () {
        Route::get('/home-dashboard',[HomeDashboardController::class, 'index'])->middleware(['auth', 'verified', \App\Http\Middleware\TrackUserActivity::class])->name('home-dashboard');
    });

    //:::: RANKING ROUTES:::::
    Route::get('/my-ranking', [RankingController::class, 'index'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('ranking.view');
    Route::post('/ranking/transaction', [RankingController::class, 'rankTransactionCommit'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('ranking.transaction');
    Route::post('/ranking/permission-check', [RankingController::class, 'UserHasMetRankLimitThreshold'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('ranking.permission-check');
    
    //:::: MYNETWORK ROUTES:::::
    //MyNetwork List
    Route::get('/mynetwork-list',[MyNetworkListController::class, 'searchForPeopleOnMyNetworkPage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-list');
    
    //MyNetwork Profile Page
    Route::get('/mynetwork-profile-view', [MyNetworkProfileController::class, 'myNetworkProfileView'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-view');

        //:::: MYNETWORK PROFILE ROUTES:::::
        //:::Sends MyNetwork Connection Request:::
        Route::post('/create-mynetwork-connection-request', [MyNetworkRequestsController::class, 'createMyNetworkConnectionRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('create-mynetwork-connection-request');

        //:::Sends MyNetwork Connection Request:::
        Route::post('/create-mynetwork-connection-request-response', [MyNetworkRequestsController::class, 'createMyNetworkConnectionRequestResponse'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('create-mynetwork-connection-request-response');

        //:::Mynetwork Incoming Requests Page:::
        Route::get('/mynetwork-incoming-requests-page', [MyNetworkRequestsController::class, 'incomingMyNetworkRequestsPage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-incoming-requests-page');

        //:::Accept Mynetwork Incoming Requests Page:::
        Route::post('/accept-mynetwork-incoming-request', [MyNetworkRequestsController::class, 'incomingMyNetworkRequestsAcceptRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('accept-mynetwork-incoming-request'); //TODO: Need to re-test

        //:::Group Invitation Routes:::
        Route::get('/mynetwork-search-users', [MyNetworkRequestsController::class, 'searchUsersForInvite'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-search-users');
        Route::get('/mynetwork-group-invited-users/{groupId}', [MyNetworkRequestsController::class, 'getInvitedUsers'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-invited-users');
        Route::post('/mynetwork-group-send-invites', [MyNetworkRequestsController::class, 'sendGroupInvites'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-send-invites');
        Route::post('/mynetwork-group-accept-invitation/{requestId}', [MyNetworkRequestsController::class, 'acceptGroupInvitation'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-accept-invitation');
        Route::post('/mynetwork-group-deny-invitation/{requestId}', [MyNetworkRequestsController::class, 'denyGroupInvitation'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-deny-invitation');


        //::::::::::::MyNetwork Profile CRUD Operations::::::::::::

        //:::Helpers:::
        Route::post('/get-canadian-school-by-province', [MyNetworkHelpers::class, 'getCanadianSchoolsByProvince'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-canadian-schools-by-province');
        Route::post('/get-american-schools-by-state', [MyNetworkHelpers::class, 'getAmericanSchoolsByState'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-american-schools-by-state');

        //:::Insert & Update Functions:::
        Route::post('/mynetwork-profile-education-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileEducationInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-education-insert-update-record');
        Route::post('/mynetwork-profile-experience-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileExperienceInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-experience-insert-update-record');
        Route::post('/mynetwork-profile-honours-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileHonoursInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-honours-insert-update-record');
        Route::post('/mynetwork-profile-interests-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileInterestsInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-interests-insert-update-record');
        Route::post('/mynetwork-profile-skill-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileSkillInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-skill-insert-update-record');
        Route::post('/mynetwork-profile-volunteering-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileVolunteeringInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-volunteering-insert-update-record');
        Route::post('/mynetwork-profile-about-insert-update-record', [MyNetworkProfileController::class, 'myNetworkProfileAboutInsertUpdateRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-about-insert-update-record');

        //:::Delete Functions:::
        Route::post('/mynetwork-profile-education-delete-record', [MyNetworkProfileController::class, 'myNetworkProfileEducationDeleteRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-education-delete-record');
        Route::post('/mynetwork-profile-experience-delete-record', [MyNetworkProfileController::class, 'myNetworkProfileExperienceDeleteRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-experience-delete-record');
        Route::post('/mynetwork-profile-honours-delete-record', [MyNetworkProfileController::class, 'myNetworkProfileHonoursDeleteRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-honours-delete-record');
        Route::post('/mynetwork-profile-interests-delete-record', [MyNetworkProfileController::class, 'myNetworkProfileInterestsDeleteRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-interests-delete-record');
        Route::post('/mynetwork-profile-skill-delete-record', [MyNetworkProfileController::class, 'myNetworkProfileSkillDeleteRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-skill-delete-record');
        Route::post('/mynetwork-profile-volunteering-delete-record', [MyNetworkProfileController::class, 'myNetworkProfileVolunteeringDeleteRecord'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-volunteering-delete-record');

        //:::File/Image Upload Functions:::
        Route::post('/mynetwork-profile-update-profile-picture-image', [MyNetworkProfileController::class, 'myNetworkProfileUpdateProfilePictureImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-update-profile-picture-image');
        Route::post('/mynetwork-profile-update-profile-header-image', [MyNetworkProfileController::class, 'myNetworkProfileUpdateProfileHeaderImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-profile-update-profile-header-image');

        //:::Analytics Functions:::
        Route::post('/update-views-count', [MyNetworkProfileController::class, 'updateViewsCount'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-views-count');
        
        //:::Profile Check Functions:::
        Route::get('/check-mynetwork-profile-exists', [MyNetworkProfileController::class, 'checkMyNetworkProfileExists'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-mynetwork-profile-exists');

        //:::: MYNETWORK GROUPS ROUTES:::::
        //:::Group Profile View:::
        Route::get('/mynetwork-group-profile-view', [MyNetworkGroupController::class, 'myNetworkGroupProfileView'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-profile-view');

        //:::Create Group:::
        Route::post('/mynetwork-group-create', [MyNetworkGroupController::class, 'createGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-create');

        //:::Update Group:::
        Route::post('/mynetwork-group-update', [MyNetworkGroupController::class, 'updateGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-update');

        //:::Join Group:::
        Route::post('/mynetwork-group-join', [MyNetworkGroupController::class, 'joinGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-join');

        //:::Leave Group:::
        Route::post('/mynetwork-group-leave', [MyNetworkGroupController::class, 'leaveGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-leave');

        //:::Create Group Post:::
        Route::post('/mynetwork-group-post-create', [MyNetworkGroupController::class, 'createGroupPost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-post-create');

        //:::Get Group Posts:::
        Route::post('/mynetwork-group-posts-get', [MyNetworkGroupController::class, 'getGroupPosts'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-posts-get');

        //:::Create Comment:::
        Route::post('/mynetwork-group-comment-create', [MyNetworkGroupController::class, 'createComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-comment-create');

        //:::Create Reply:::
        Route::post('/mynetwork-group-reply-create', [MyNetworkGroupController::class, 'createReply'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-reply-create');

        //:::Upload Header Image:::
        Route::post('/mynetwork-group-upload-header-image', [MyNetworkGroupController::class, 'uploadHeaderImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-upload-header-image');

        //:::Upload Profile Image:::
        Route::post('/mynetwork-group-upload-profile-image', [MyNetworkGroupController::class, 'uploadProfileImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-upload-profile-image');

        //:::Get User Groups:::
        Route::get('/mynetwork-group-get-user-groups', [MyNetworkGroupController::class, 'getUserGroups'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-get-user-groups');

        //:::Browse Groups Page:::
        Route::get('/browse-groups', [MyNetworkGroupController::class, 'browseGroupsPage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('browse-groups');

        //:::Get Browsable Groups:::
        Route::get('/mynetwork-group-get-browsable-groups', [MyNetworkGroupController::class, 'getBrowsableGroups'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-get-browsable-groups');

        //:::Update Group Permissions:::
        Route::post('/mynetwork-group-update-permissions', [MyNetworkGroupController::class, 'updateGroupPermissions'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-update-permissions');

        //:::Toggle Post Visibility:::
        Route::post('/mynetwork-group-toggle-post-visibility', [MyNetworkGroupController::class, 'togglePostVisibility'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-toggle-post-visibility');

        //:::Toggle Comment Visibility:::
        Route::post('/mynetwork-group-toggle-comment-visibility', [MyNetworkGroupController::class, 'toggleCommentVisibility'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-toggle-comment-visibility');

        //:::Get Group Members:::
        Route::get('/mynetwork-group-get-members', [MyNetworkGroupController::class, 'getGroupMembers'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-get-members');

        //:::Ban Member:::
        Route::post('/mynetwork-group-ban-member', [MyNetworkGroupController::class, 'banMember'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-ban-member');

        //:::Unban Member:::
        Route::post('/mynetwork-group-unban-member', [MyNetworkGroupController::class, 'unbanMember'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-unban-member');

        //:::Get Filtered Posts:::
        Route::get('/mynetwork-group-filtered-posts', [MyNetworkGroupController::class, 'getFilteredGroupPosts'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-filtered-posts');

        //:::Make Moderator:::
        Route::post('/mynetwork-group-make-moderator', [MyNetworkGroupController::class, 'makeModerator'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-make-moderator');

        //:::Remove Moderator:::
        Route::post('/mynetwork-group-remove-moderator', [MyNetworkGroupController::class, 'removeModerator'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-remove-moderator');

        //:::Remove Member:::
        Route::post('/mynetwork-group-remove-member', [MyNetworkGroupController::class, 'removeMember'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-remove-member');

        //:::Archive Group:::
        Route::post('/mynetwork-group-archive', [MyNetworkGroupController::class, 'archiveGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-archive');

        //:::Delete Group:::
        Route::post('/mynetwork-group-delete', [MyNetworkGroupController::class, 'deleteGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-delete');

        //:::Restore Group:::
        Route::post('/mynetwork-group-restore', [MyNetworkGroupController::class, 'restoreGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('mynetwork-group-restore');

        //:::View All User Groups Page:::
        Route::get('/view-all-mynetwork-groups', function () {
            return \Inertia\Inertia::render('MyNetwork/ViewAllMyNetworkGroupProfilePage');
        })->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('view-all-mynetwork-groups');

    //:::: HASHTAG ROUTES:::::
    //:::Search Hashtags:::
    Route::post('/hashtag/search', [MyNetworkHashTagController::class, 'searchHashtags'])->name('hashtag-search');

    //:::Process Hashtags (Extract and Link to Post):::
    Route::post('/hashtag/process', [MyNetworkHashTagController::class, 'processHashtags'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('hashtag-process');

    //:::Browse Hashtag Page:::
    Route::get('/hashtag/browse/{tagName}', [MyNetworkHashTagController::class, 'browseHashtag'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('hashtag-browse');

    //:::Get Popular Hashtags:::
    Route::get('/hashtag/popular', [MyNetworkHashTagController::class, 'getPopularHashtags'])->name('hashtag-popular');

    //:::Get Trending Hashtags:::
    Route::get('/hashtag/trending', [MyNetworkHashTagController::class, 'getTrendingHashtags'])->name('hashtag-trending');

    //:::Get Recent Hashtags:::
    Route::get('/hashtag/recent', [MyNetworkHashTagController::class, 'getRecentHashtags'])->name('hashtag-recent');

    //:::Get Posts by Hashtag (API):::
    Route::get('/hashtag/{tagName}', [MyNetworkHashTagController::class, 'getPostsByHashtag'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('hashtag-posts');

    //:::: SEARCH ROUTES:::::
    //:::Search All Pages:::
    Route::get('/Thrd-all-search-page',[SearchController::class, 'allSearchRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('Thrd-all-search-page');

    //:::Search People:::
    Route::get('/people-search-page',[MyNetworkSearchController::class, 'searchForPeopleOnMyNetworkPage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('people-search-page');
    Route::get('/groups-search-page',[MyNetworkSearchController::class, 'searchForGroupsOnMyNetworkPage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('groups-search-page');
    Route::get('/api/user-group-memberships',[MyNetworkSearchController::class, 'getUserGroupMemberships'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.user-group-memberships');
    Route::post('/api/join-group',[MyNetworkGroupController::class, 'joinGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.join-group');

    //:::Search Products & Product View:::
    Route::get('/product-search-page',[SearchController::class, 'searchForProductAndServiceseOnAgoraRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('product-search-page');

    Route::get('/category-product-search-page',[SearchController::class, 'searchForProductAndServiceseOnAgoraRequestBySubCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('category-product-search-page');

    Route::get('/product-item-view/{id}',[ItemViewController::class, 'ProductItemViewIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('product-item-view');

    Route::post('/get-item-data-by-item-id-via-request',[ItemViewController::class, 'getItemDataByItemIdViaRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-item-data-by-item-id-via-request');
    Route::get('/get-detailed-item-data',[ItemViewController::class, 'getDetailedItemData'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-detailed-item-data');
    Route::get('/get-user-Circle-reviews',[ItemViewController::class, 'getUserCircleReviews'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-user-Circle-reviews');
    Route::get('/get-comprehensive-item-data',[ItemViewController::class, 'getComprehensiveItemData'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-comprehensive-item-data');
    Route::get('/get-user-wishlist',[ItemViewController::class, 'getUserWishlist'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-user-wishlist');
    
    //:::Search Events:::
    Route::get('/Thrd-event-search-page',[MyNetworkSearchController::class, 'searchForAllOnThrdRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('Thrd-event-search-page');
    Route::get('/event-search-page',[SearchController::class, 'searchForEventsRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('event-search-page');

    //:::Search History Tracking:::
    Route::post('/record-search-history',[SearchController::class, 'recordSearchHistory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('record-search-history');

    Route::post('/get-states-by-country', [SearchController::class, 'getStatesByCountry'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-states-by-country');
    Route::post('/get-cities-by-state', [SearchController::class, 'getCitiesByState'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-cities-by-state');

    //:::: NEWS ARTICLE ROUTES:::::
    //INDEX VIEWS
    Route::get('/view-news-articles', [NewsArticlesController::class, 'viewNewsArticleManageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('view-news-articles');
    Route::get('/add-news-articles', [NewsArticlesController::class, 'addNewsArticleManageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-news-articles');
    Route::get('/edit-news-articles/{id}', [NewsArticlesController::class, 'editNewsArticleManageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('edit-news-articles');

    //CRUD OPERATIONS
    Route::post('/news-article-create', [NewsArticlesController::class, 'store'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('news-article-create');
    Route::post('/news-article-edit/{id}', [NewsArticlesController::class, 'update'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('news-article-store');
    Route::post('/news-article-delete/{id}', [NewsArticlesController::class, 'delete'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('news-article-edit');

    //:::: CHAT ROUTES:::::
    //:::WEB CHAT [NEW CHAT] ROUTES:::
    Route::get('/chat', [ChatController::class, 'ChatIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat');

    //Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/get-conversations', [ChatController::class, 'getConversations'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-conversations');
    Route::get('/poll-chat', [ChatController::class, 'pollChat'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('poll-chat');

    Route::post('/chat-init-Circle', [ChatController::class, 'chatInit_Circle'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-init-Circle');
    Route::post('/chat-init-network', [ChatController::class, 'chatInit_MyNetwork'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-init-network');
    Route::post('/check-user-conversation', [ChatController::class, 'hasAlreadyMessagedUser'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-user-conversation');
    Route::post('/chat-new', [ChatController::class, 'chatNewConverse'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-new');
    Route::post('/chat-is-read', [ChatController::class, 'checkIfOtherChatIsRead_deprecated'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('chat-is-read');
    Route::get('/get-unread-conversations-count', [ChatController::class, 'checkIfOtherChatIsRead'])->name('get-unread-conversations-count');
    Route::post('/post-chat', [ChatController::class, 'postChat'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('post-chat');
    Route::post('/conv-click', [ChatController::class, 'chatConverseClick'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-click');
    Route::post('/conv-details', [ChatController::class, 'chatUserDetails'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-details');
    Route::post('/conv-delete', [ChatController::class, 'destoryConversation'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-delete');
    Route::post('/conv-count', [ChatController::class, 'getConversationCount'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('conv-count');

    //:::WEB CHAT CATEGORY GROUP ROUTES:::
    Route::post('/add-chat-category-group', [ChatController::class, 'addChatCategoryGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-chat-category-group');
    Route::post('/update-chat-category-group', [ChatController::class, 'updateChatCategoryGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-chat-category-group');
    Route::post('/delete-chat-category-group', [ChatController::class, 'deleteChatCategoryGroup'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('delete-chat-category-group');

    //:::WEB CHAT CATEGORY GROUP TRACKING ROUTES:::
    Route::post('/add-convo-to-convo-category', [ChatController::class, 'addConvoToConvoCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-convo-to-convo-category');
    Route::post('/remove-convo-from-convo-category', [ChatController::class, 'removeConvoFromConvoCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('remove-convo-from-convo-category');
    Route::post('/reset-conversations-to-default-conversation-category', [ChatController::class, 'resetConversationsToDefaultConversationCategory'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('reset-conversations-to-default-conversation-category');
    Route::post('/update-expand-state', [ChatController::class, 'updateExpandState'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-expand-state');

    //:::: FEED ROUTES:::::
    //::::NEWSFEED ROUTES:::::
    //::Comment Routes::
    Route::get('/feed', [NewsCircleFeedController::class, 'feedViewIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('feed');
    Route::post('/prompt-next-feed-load', [NewsCircleFeedController::class, 'promptNextFeedLoad'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('prompt-next-feed-load');
    Route::post('/get-latest-posts', [NewsCircleFeedController::class, 'getLatestPosts'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-latest-posts');
    Route::post('/create-new-post', [NewsCircleFeedController::class, 'newPost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('create-new-post');
    Route::post('/store-image-for-post', [NewsCircleFeedController::class, 'storeImageForPost'])->middleware(\App\Http\Middleware\TrackUserActivity::class);
    Route::post('/pull-comment/{post:id}', [NewsCircleFeedController::class, 'loadCommentsForPost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('pull-comment');
    Route::post('/new-comment', [NewsCircleFeedController::class, 'sendNewComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('new-comment');
    Route::post('/comment-reply', [NewsCircleFeedController::class, 'sendReplyToComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('comment-reply');
    Route::post('/load-comments-replies', [NewsCircleFeedController::class, 'loadCommentsReplysForComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('load-comments-replies');
    Route::post('/check-report-threshold', [NewsCircleFeedController::class, 'reportThresholdPerDay'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('reports-per-day');

    //::::NEWSFEED - FAVOURITES ROUTES:::::
    Route::get('/feed-favourite', [NewsCircleFeedController::class, 'feedFavourite'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('feed-favourite');
    Route::get('/my-feed', [NewsCircleFeedController::class, 'feedOwner'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('my-feed');

    //::ANID Generator Route::
    Route::post('/gen-new-comm-anid', [NewsCircleFeedController::class, 'generateNewCommentANID'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('gen-new-comm-anid');

    //::Comment Controls Routes::
    Route::post('/delete-main-comment', [NewsCircleFeedController::class, 'deleteMainComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('delete-main-comment');
    Route::post('/delete-reply-comment', [NewsCircleFeedController::class, 'deleteReplyComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('delete-reply-comment');
    Route::post('/report-comment', [NewsCircleFeedController::class, 'reportComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('report-comment');

    //::POST CONTROLS ROUTES::
    Route::post('/report-post', [ReportController::class, 'reportPost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('report-post');
    Route::post('/report-item', [ReportController::class, 'reportItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('report-item');
    Route::post('/report-Circle', [ReportController::class, 'reportCircle'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('report-Circle');
    Route::get('/check-Circle-report-status', [ReportController::class, 'checkCircleReportStatus'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-Circle-report-status');
    Route::get('/check-report-status', [ReportController::class, 'checkReportStatus'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-report-status');
    Route::post('/like-post', [NewsCircleFeedController::class, 'likePost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('like-post');
    Route::post('/favourite-post', [NewsCircleFeedController::class, 'favouritePost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('favourite-post');
    Route::post('/share-post', [NewsCircleFeedController::class, 'sharePost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('share-post');
    Route::post('/increment-post-view', [NewsCircleFeedController::class, 'incrementPostView'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('increment-post-view');
    Route::post('/delete-post', [NewsCircleFeedController::class, 'deletePost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('delete-post');

    //::::NEWSFEED POSTVIEW ROUTES:::::
    Route::get('/postview/{post}', [ViewPostsController::class, 'index'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('postview');

    //:::: NOTIFICATION ROUTES:::::
    Route::get('/notifications', [NotificationsController::class, 'NotificationsIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('notifications');

    //:::: Circle DASHBOARD ROUTES:::::
    Route::get('/dashboard', [DashboardController::class, 'DashbaordIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('dashboard');

    //:::: Circle VIEW ROUTES:::::
    Route::get('/Circle-view', [ManageCircleTransactionController::class, 'CircleViewIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('Circle-view');

    //::::MANAGE Circle TRANSACTIONS ROUTES:::::
    Route::get('/get-Circle-transactions', [ManageCircleTransactionController::class, 'getCircleTransactions'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-Circle-transactions');
    Route::get('/Circleview/{id}', [ManageCircleTransactionController::class, 'index'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('Circleview');
    Route::post('/determine-item-exists-in-another-Circle', [ManageCircleTransactionController::class, 'determineItemExistsInAnotherCircle'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('determine-item-exists-in-another-Circle');
    Route::post('/get-Circle-transactions-by-user-id', [ManageCircleTransactionController::class, 'getCircleTransactionsByUserId'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-Circle-transactions-by-user-id');
    

    Route::post('/change-Circle-transaction-item', [ManageCircleTransactionController::class, 'changeCircleTransactionItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('change-Circle-transaction-item');
    Route::post('/complete-Circle-transaction', [ManageCircleTransactionController::class, 'completeCircleTransaction'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('complete-Circle-transaction');
    Route::post('/review-completed-Circle', [ManageCircleTransactionController::class, 'reviewCompletedCircle'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('review-completed-Circle');

    //::Quick Circle Options Features::
    Route::post('/accept-incoming-Circle-transaction', [ManageCircleTransactionController::class, 'acceptIncomingCircleTransaction'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('accept-incoming-Circle-transaction');// for prospect to accept
    Route::post('/accept-Circle-transaction-item-offer', [ManageCircleTransactionController::class, 'acceptCircleTransactionItemOffer'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('accept-Circle-transaction-item-offer'); //for either party to offer an item to barter for

    Route::post('/deny-incoming-Circle-transaction', [ManageCircleTransactionController::class, 'denyIncomingCircleTransaction'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('deny-incoming-Circle-transaction');
    Route::post('/deny-Circle-transaction-item-offer', [ManageCircleTransactionController::class, 'denyCircleTransactionItemOffer'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('deny-Circle-transaction-item-offer'); //for either party to offer an item to barter for

    Route::post('/abort-Circle-transaction', [ManageCircleTransactionController::class, 'abortCircleTransaction'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('abort-Circle-transaction');

    // Questionnaire data
    Route::post('/store-question-campaign-answers', [CircleInsightController::class, 'storeQuestionCampaignAnswers'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('store-question-campaign-answers');
    Route::get('/check-Circle-questionnaire-status', [CircleInsightController::class, 'checkCircleQuestionnaireStatus'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('check-Circle-questionnaire-status');

    //::::PRODUCT/SERVICE/ITEM ADD/EDIT/VIEW ROUTES:::::
    //:::ADD ITEM ROUTES:::
    Route::get('/add-item', [ItemController::class, 'AddItemIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-item');
    Route::post('/add-item', [ItemController::class, 'store'])->middleware(\App\Http\Middleware\TrackUserActivity::class);
    Route::post('/store-image', [ItemController::class, 'storeImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class);

    //:::MANAGE ITEM ROUTES:::
    Route::get('/manage-item', [ManageItemController::class, 'ManageItemIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('manage-item');
    Route::post('/delete-item', [ManageItemController::class, 'destroyItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('delete-item');

    //::::EDIT ITEM ROUTES:::::
    Route::get('/edit-item/{item}', [EditItemController::class, 'EditItemIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('edit-item');
    Route::post('/edit-main-image-item', [EditItemController::class, 'updateMainImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('edit-item.image');
    Route::post('/update-item', [EditItemController::class, 'updateItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-item');
    Route::post('/destroy-image', [EditItemController::class, 'destroyImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('destroy-image');

    //::::PRODUCT/ITEM VIEW ROUTES:::::
    //:::ADD WISHLIST ITEM ROUTES:::
    Route::get('/add-wishlist-item-view', [WishListController::class, 'addWishlistItemViewIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-wishlist-item-view');
    Route::post('/store-wishlist-item', [WishListController::class, 'storeWishlistItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('store-wishlist-item');
    Route::post('/store-wishlist-image', [WishListController::class, 'storeWishlistImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('store-wishlist-image');

    //:::EDIT WISHLIST ITEM ROUTES:::
    Route::get('/edit-wishlist-item-view/{wishlistItemId}', [WishListController::class, 'editWishlistItemViewIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('edit-wishlist-item-view');
    Route::post('/edit-wishlist-item', [WishListController::class, 'editWishlistItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('edit-wishlist-item');
    Route::post('/update-wishlist-image', [WishListController::class, 'updateWishlistImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-wishlist-image'); //deprecated
    Route::post('/update-wishlist-item', [WishListController::class, 'updateWishlistItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-wishlist-item'); //wishlist image updates are saved here

    Route::post('/update-wishlist-item', [WishListController::class, 'updateWishlistItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-wishlist-item');
    Route::post('/destroy-wishlist-image', [WishListController::class, 'destroyWishlistImage'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('destroy-wishlist-image');
    Route::post('/destroy-wishlist-item', [WishListController::class, 'destroyWishlistItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('destroy-wishlist-item');

    //:::MANAGE WISHLIST ITEM ROUTES:::
    Route::get('/manage-wishlist', [WishListController::class, 'manageWishlistIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('manage-wishlist');

    //:::VIEW WISHLIST ITEM ROUTES:::
    Route::get('/view-wishlist-item/{wishlistItemId}', [WishListViewsController::class, 'ViewWishlistItem'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('view-wishlist-item');
    Route::get('/view-wishlist-items-list/{userId}', [WishListViewsController::class, 'ViewWishlistItemsList'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('view-wishlist-items-list');

    //::::MANAGE ITEM FAVOURITES:::::
    Route::get('/manage-favourites', [WishListController::class, 'manageFavouritesIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('manage-favourites');

    Route::post('/favourite-item-post', [WishListController::class, 'favouriteItemPost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('favourite-item-post');

    //::::LIKE ROUTES (Posts, Comments, Comment Replies):::::
    Route::post('/like-post', [WishListController::class, 'likePost'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('like-post');
    Route::post('/like-comment', [WishListController::class, 'likeComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('like-comment');
    Route::post('/like-comment-reply', [WishListController::class, 'likeCommentReply'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('like-comment-reply');
    Route::post('/get-post-like-status', [WishListController::class, 'getPostLikeStatus'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-post-like-status');
    Route::post('/get-comment-like-status', [WishListController::class, 'getCommentLikeStatus'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-comment-like-status');

    //::::CHECKOUT/CART/E-COMMERCE ROUTES:::::
    Route::get('/checkout', [CheckoutController::class, 'CheckoutIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('checkout');
    Route::get('/checkout-success', [CheckoutController::class, 'checkoutSuccessIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('checkout-success');

    Route::post('/commit-cart-transaction', [CheckoutController::class, 'commitCartTransaction'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('commit-cart-transaction');
    Route::post('/create-stripe-transaction', [CheckoutController::class, 'createStripeTransaction'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('create-stripe-transaction');

    //::::CART ROUTES:::::
    Route::get('/cart', [CartController::class, 'CartIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('cart');
    Route::post('/store-cart-session', [CartController::class, 'storeCartSession'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('store-cart-session');
    Route::get('/get-cart-session', [CartController::class, 'getCartSession'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('get-cart-session');

    //::::PURCHASES ROUTES:::::
    Route::get('/view-receipt', [ManagePurchaseController::class, 'ViewReceiptIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('receipt');
    
    //::::TEST PRODUCTs ROUTES:::::
    Route::get('/test-product', [CartController::class, 'testProducts'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('test-product');
    Route::get('/add-membership', [ProductsController::class, 'AddMembershipIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('add-membership');

    //::::ACCOUNT SETTINGS ROUTES:::::
    Route::get('/account-settings', function () {
        return Inertia::render('AccountSettings/AccountSettings');
    })->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('account-settings');

    //::::OFFERS ROUTES:::::
    //::::OFFERS PAGE ROUTE::::
    Route::get('/offers', [OffersController::class, 'viewOffersPageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('offers');

    //::::RANK OFFERS: HELP VERIFY IMAGE ROUTE::::
    Route::get('/help-verify-image', [HelpVerifyImagesController::class, 'helpVerifyImageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('help-verify-image');
    Route::post('/help-verify-image-store', [HelpVerifyImagesController::class, 'store'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('help-verify-image-store');

    //::::HELP SUPPORT ROUTES:::::
    //::::HELP SUBMIT PAGE ROUTE::::
    Route::get('/help-support', [HelpSupportController::class, 'viewHelpSupportPageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('help-support');

    Route::get('/view-opened-requests', [HelpSupportController::class, 'viewOpenHelpSupportPageIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('view-opened-requests');
    Route::get('/support-request-details/{id}', [HelpSupportController::class, 'viewSupportRequestDetails'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support-request-details');

    //::::EVENT & CALENDAR ROUTES:::::
    Route::get('/event-calendar', [CalendarMonthController::class, 'eventCalendarViewIndex'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('event-calendar');

    // Event Management API Routes
    Route::prefix('api/events')->group(function () {
        Route::get('/get-events', [CalendarMonthController::class, 'getEvents'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.events.get');
        Route::get('/get-events-for-date', [CalendarMonthController::class, 'getEventsForDate'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.events.get-for-date');
        Route::get('/get-event-groups-with-events', [CalendarMonthController::class, 'getEventGroupsWithEvents'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.events.get-groups-with-events');
        Route::post('/create', [CalendarMonthController::class, 'createEvent'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.events.create');
        Route::put('/update/{eventId}', [CalendarMonthController::class, 'updateEvent'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.events.update');
        Route::delete('/delete/{eventId}', [CalendarMonthController::class, 'deleteEvent'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('api.events.delete');
    });

    //::::SUPPORT ROUTES:::::
    Route::get('/support', [SupportController::class, 'index'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support');
    Route::post('/submit-support-request', [SupportController::class, 'store'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('submit-support-request');
    Route::get('/my-support-requests', [SupportController::class, 'getUserSupportRequests'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('my-support-requests');
    Route::get('/support-request/{id}', [SupportController::class, 'getSupportRequestDetails'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support-request.details');
    Route::get('/support-request/{id}/comments', [SupportController::class, 'getSupportRequestComments'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support-request.comments');
    Route::post('/support-request/comments', [SupportController::class, 'addSupportRequestComment'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support-request.add-comment');
    Route::patch('/support-request/{id}/close', [SupportController::class, 'closeSupportRequest'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support-request.close');
    Route::patch('/support-request/{id}/status', [SupportController::class, 'updateStatus'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('support-request.update-status');

    //::::SETTINGS ROUTES:::::
    Route::post('/update-settings', [SettingsController::class, 'store'])->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('update-settings');

    //::::TEMPLATE ROUTES:::::
    Route::get('/prototypetemplate', function () {
        return Inertia::render('Templates/MainLayoutTemplate');
    })->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('prototypetemplate');

    //::::MAINTENANCE:::::
    //:::404:::
    Route::get('/page-not-found', function () {
        return Inertia::render('ToolPages/NotFoundPage');
    })->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('page-not-found');

    //::::TESTING ROUTES:::::
    Route::get('/testing1', function () {
        return Inertia::render('Testing/TestDynamicComponentUpdate');
    })->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('testing1');
    
    // Test activity tracking
    Route::get('/test-tracking', function () {
        return response()->json(['message' => 'Activity should be tracked', 'user_id' => auth()->id()]);
    })->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('test-tracking');
});

//:::Production Access Page - Outside all middleware groups:::
Route::get('/landing', function () {
    return Inertia::render('ToolPages/ProductionPasswordProtectedPage');
})->name('landing');

require __DIR__.'/auth.php';

