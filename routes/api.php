<?php

use App\Http\Controllers\api\auth\AuthApiController;
use App\Http\Controllers\api\site\BusinessInfoApiController;
use App\Http\Controllers\api\site\ChatApiController;
use App\Http\Controllers\api\site\ConnectionApiController;
use App\Http\Controllers\api\site\CustomerMasterApiController;
use App\Http\Controllers\api\site\LeadCategoryController;
use App\Http\Controllers\api\site\LeadController;
use App\Http\Controllers\api\site\MasterApiController;
use App\Http\Controllers\api\site\MasterCommentApiController;
use App\Http\Controllers\api\site\MasterEventApiController;
use App\Http\Controllers\api\site\MasterKeywordApiController;
use App\Http\Controllers\api\site\MasterKeywordController;
use App\Http\Controllers\api\site\MasterLinkController;
use App\Http\Controllers\api\site\MasterPostApiController;
use App\Http\Controllers\api\site\PageController;
use App\Http\Controllers\api\site\PostApiController;
use App\Http\Controllers\api\site\ProductApiController;
use App\Http\Controllers\api\site\ProductClickController;
use App\Http\Controllers\api\site\ProfileApiController;
use App\Http\Controllers\api\site\ProfileLinkClickController;
use App\Http\Controllers\api\site\ProfileViewController;
use App\Http\Controllers\api\site\RestrictionApiController;
use App\Http\Controllers\api\site\SubscriptionApiController;
use App\Http\Controllers\api\site\UserAnalyticsController;
use App\Http\Controllers\api\site\UserListController;
use App\Http\Controllers\api\site\UserReviewApiController;
use App\Http\Controllers\frontend\site\ReviewController;
use App\Http\Controllers\api\site\SubscriptionPlanController;
use App\Http\Controllers\api\site\PlanFeatureController;
use App\Http\Controllers\api\site\UserSubscriptionController;
use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/test-notification', [NotificationController::class, 'test']);

Route::post('/check-mobile', [AuthApiController::class, 'checkMobile']);
Route::post('/check-sms-delivery', [AuthApiController::class, 'checkSmsDelivery']);
Route::post('/reset-pin-otp', [AuthApiController::class, 'resetPinOtp']);
Route::get('/users/list', [AuthApiController::class, 'listUsers']);
Route::post('/users/filter-list', [AuthApiController::class, 'filterListUsers']);
Route::post('/regenerate-otp/{phone}', [
    AuthApiController::class,
    'regenerateOtp',
]);
Route::post('/verify-otp/{phone_no}/{token}', [
    AuthApiController::class,
    'verifyOtp',
]);
Route::post('/registration/{phone_no}/{token}', [
    AuthApiController::class,
    'registration',
]);
Route::post('/check-username', [
    AuthApiController::class,
    'checkUsername',
]);

Route::get('/page/{slug}', [PageController::class, 'show']);
Route::post('/login/phone/check', [AuthApiController::class, 'checkPhone']);
Route::post('/login/pin/check', [AuthApiController::class, 'checkPin']);
Route::post('/login/pin/set', [AuthApiController::class, 'setPin']);
Route::post('/login/pin', [AuthApiController::class, 'loginWithPin']);
Route::post('/login/{phone}', [AuthApiController::class, 'login']);
Route::post('/google-login', [AuthApiController::class, 'googleLogin']);
Route::post('/google-register', [AuthApiController::class, 'googleRegister']);
Route::post('/check-email', [AuthApiController::class, 'checkEmail']);

Route::get('/leads', [LeadController::class, 'index']);
Route::post('/leads', [LeadController::class, 'store']);
Route::get('/leads/{id}', [LeadController::class, 'show']);
Route::put('/leads/{id}', [LeadController::class, 'update']);
Route::delete('/leads/{id}', [LeadController::class, 'destroy']);
Route::get('/leads-list-by-user-id', [LeadController::class, 'getByUserId']);
Route::post('/ocr-extract', [LeadController::class, 'ocrExtract']);
Route::post('/gpt-ocr', [PostApiController::class, 'gptOcr']);
Route::get('/user/{id}/master-links', [MasterLinkController::class, 'getUserLinksById']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('/master-country', [MasterApiController::class, 'getCountry']);
    Route::get('/master-indus-type', [
        MasterApiController::class,
        'getIndustryTypeList',
    ]);
    Route::post('/master-state', [MasterApiController::class, 'getState']);
    Route::post('/master-district', [
        MasterApiController::class,
        'getDistrict',
    ]);

    Route::post('/update-fcm-token', [AuthApiController::class, 'updateFcmToken']);

    Route::get('/profile-card', [
        ProfileApiController::class,
        'usersProfileCard',
    ]);

    Route::get('/profile-card/{user_id}', [
        ProfileApiController::class,
        'usersProfileCardData',
    ]);

    Route::get('/user-profile/{user_name}', [
        ProfileApiController::class,
        'usersNameProfileData',
    ]);

    Route::match(['get', 'post'], '/profile', [
        ProfileApiController::class,
        'getOrUpdateProfile',
    ]);
    Route::match(['get', 'post', 'put', 'patch', 'delete'], '/links', [
        ProfileApiController::class,
        'UserLinks',
    ]);
    Route::match(['get', 'post'], '/business-info', [
        ProfileApiController::class,
        'BusinessInfo',
    ]);

    Route::post('/customer-master/index', [
        CustomerMasterApiController::class,
        'index',
    ]);
    Route::post('/customer-master/create', [
        CustomerMasterApiController::class,
        'create',
    ]);
    Route::post('/customer-master/update/{id}', [
        CustomerMasterApiController::class,
        'update',
    ]);

    Route::match(['get', 'post'], '/master-keywords', [
        MasterKeywordApiController::class,
        'index',
    ]);
    Route::delete('/master-keywords/{id}', [
        MasterKeywordApiController::class,
        'deleteKeyword',
    ]);
    Route::post('/search-users', [
        MasterKeywordApiController::class,
        'getUsersByKeywords',
    ]);

    Route::post('/connection-request', [
        ConnectionApiController::class,
        'sendConnectionRequest',
    ]);

    Route::get('/connection-show', [
        ConnectionApiController::class,
        'connectionShow',
    ]);
    Route::get('/connection-count', [
        ConnectionApiController::class,
        'connectionCount',
    ]);
    Route::get('/connection-request', [
        ConnectionApiController::class,
        'connectionRequests',
    ]);

    Route::post('/connection-accept/{rid}', [
        ConnectionApiController::class,
        'connectionAccept',
    ]);
    Route::post('/connection-reject/{rid}', [
        ConnectionApiController::class,
        'connectionReject',
    ]);
    Route::post('/connection-remove/{rid}', [
        ConnectionApiController::class,
        'connectionRemove',
    ]);

    Route::get('/show-event', [MasterEventApiController::class, 'showEvent']);
    Route::post('/create-event', [
        MasterEventApiController::class,
        'createEvent',
    ]);
    Route::delete('/delete-event/{eid}', [
        MasterEventApiController::class,
        'deleteEevent',
    ]);

    Route::get('/show-postby-industry/{indus_type}', [
        MasterPostApiController::class,
        'showPostByIndustry',
    ]);
    Route::get('/show-save-postlist', [
        MasterPostApiController::class,
        'showSavePostList',
    ]);
    Route::get('/show-saved-post/{post_id}', [
        MasterPostApiController::class,
        'showSavedPost',
    ]);
    Route::post('/show-post', [MasterPostApiController::class, 'showPost']);
    Route::post('/create-post', [MasterPostApiController::class, 'createPost']);
    Route::post('/save-post/{post_id}', [
        MasterPostApiController::class,
        'toggleSavePost',
    ]);
    Route::post('/toggle-like/{post_id}', [
        MasterPostApiController::class,
        'likeOrUnlike',
    ]);
    Route::delete('/delete-post/{pid}', [
        MasterPostApiController::class,
        'deletePost',
    ]);

    Route::get('/comment/{post_id}', [
        MasterCommentApiController::class,
        'getComments',
    ]);
    Route::post('/comment', [
        MasterCommentApiController::class,
        'createComments',
    ]);
    Route::post('/comment/reply', [
        MasterCommentApiController::class,
        'replyComment',
    ]);
    Route::post('/comment/toggle-like', [
        MasterCommentApiController::class,
        'likeOrUnlikeComment',
    ]);
    Route::delete('/comment/delete/{c_id}', [
        MasterCommentApiController::class,
        'deleteComments',
    ]);

    Route::get('/chat/get-message/{chatId}', [
        ChatApiController::class,
        'getMessages',
    ]);
    Route::post('/chat/create-chat/{other_Id}', [
        ChatApiController::class,
        'createChat',
    ]);
    Route::post('/chat/send-message', [
        ChatApiController::class,
        'sendMessages',
    ]);
    Route::post('/chat/mark-read/{chatId}', [ChatApiController::class, 'markAsRead']);
    Route::post('/chat/like-message', [ChatApiController::class, 'toggleLike']);
    Route::post('/chat/delete-message/{message_Id}', [
        ChatApiController::class,
        'deleteMessage',
    ]);

    Route::get('/restrictions', [
        RestrictionApiController::class,
        'getRestrictionData',
    ]);
    Route::get('/restrictions/{id}', [
        RestrictionApiController::class,
        'getRestriction',
    ]);
    Route::post('/restrictions', [
        RestrictionApiController::class,
        'createRestriction',
    ]);
    Route::put('/restrictions/{id}', [
        RestrictionApiController::class,
        'updateRestriction',
    ]);

    Route::get('/plans', [SubscriptionApiController::class, 'showPlan']);
    Route::get('/plans/{plan_id}', [
        SubscriptionApiController::class,
        'getPlans',
    ]);
    Route::post('/plans', [SubscriptionApiController::class, 'createPlan']);
    Route::put('/plans/{plan_id}', [
        SubscriptionApiController::class,
        'getUpdatePlans',
    ]);
    Route::delete('/plans/{plan_id}', [
        SubscriptionApiController::class,
        'deletePlans',
    ]);
    Route::post('/create-subscription', [
        SubscriptionApiController::class,
        'createSubscription',
    ]);
    Route::post('/features', [
        SubscriptionApiController::class,
        'checkPaidPlans',
    ]);

    // amit api

    Route::post('/connection-create', [
        ConnectionApiController::class,
        'connectionCreate',
    ]);

    Route::delete('/connections/remove', [
        ConnectionApiController::class,
        'removeConnection',
    ]);

    Route::get('/connection-receiver-senders/{receiver_id}', [
        ConnectionApiController::class,
        'getUniqueSendersByReceiver',
    ]);

    Route::get('/connection-senders-receiver/{sender_id}', [
        ConnectionApiController::class,
        'getUniqueReceiversBySender',
    ]);

    Route::post('/connection-request/update-status', [
        ConnectionApiController::class,
        'updateConnectionRequestStatus',
    ]);

    Route::post('/connection/status', [
        ConnectionApiController::class,
        'checkStatus',
    ]);

    Route::post('/connection-request/delete', [
        ConnectionApiController::class,
        'deleteConnectionRequest',
    ]);

    Route::get('/friends/{user_id}', [
        ConnectionApiController::class,
        'getFriendListByUser',
    ]);

    //  api for post start

    Route::get('/posts/all_post_list', [
        PostApiController::class,
        'allPostList',
    ]);

    Route::post('/posts/create', [
        PostApiController::class,
        'createPost',
    ]);

    Route::get('/posts/feed', [
        PostApiController::class,
        'feed',
    ]);

    Route::post('/posts/react', [
        PostApiController::class,
        'reactPost',
    ]);
    Route::post('/posts/comment', [
        PostApiController::class,
        'addComment',
    ]);

    Route::post('/posts/save', [
        PostApiController::class,
        'toggleSave',
    ]);
    Route::post('/posts/share', [
        PostApiController::class,
        'sharePost',
    ]);
    Route::post('/posts/view', [
        PostApiController::class,
        'viewPost',
    ]);

    Route::post('/posts/report', [
        PostApiController::class,
        'reportPost',
    ]);

    Route::get('/posts/list', [
        PostApiController::class,
        'listAllPosts',
    ]);

    Route::post('/posts/update/{post_id}', [
        PostApiController::class,
        'updatePost',
    ]);
    Route::delete('/posts/delete/{post_id}', [
        PostApiController::class,
        'deletePost',
    ]);

    Route::delete('/posts/comment/{comment_id}', [
        PostApiController::class,
        'deleteComment',
    ]);

    Route::get('/posts/comments_list/{postId}', [
        PostApiController::class,
        'getPostComments',
    ]);

    Route::get('/posts/user/{user_id}', [
        PostApiController::class,
        'userPosts',
    ]);

    Route::get('/posts/{post_id}', [
        PostApiController::class,
        'postDetail',
    ]);

    Route::get('/industries', [
        PostApiController::class,
        'getIndustriesData',
    ]);

    Route::get('/user-events/{user_id}', [
        MasterEventApiController::class,
        'getEventsByUserId',
    ]);

    Route::get('/states', [
        MasterEventApiController::class,
        'getAllStates',
    ]);

    Route::get('/districts', [
        MasterEventApiController::class,
        'getAllDistricts',
    ]);

    Route::post('/update-event/{id}', [
        MasterEventApiController::class,
        'updateEvent',
    ]);
    Route::get('/event/{id}', [
        MasterEventApiController::class,
        'getEvent',
    ]);
    Route::post('/delete-event-image', [
        MasterEventApiController::class,
        'deleteEventImage',
    ]);

    Route::post('/upload-profile-pic', [
        AuthApiController::class,
        'uploadProfilePic',
    ]);

    Route::get('/user-details/{id}', [
        PostApiController::class,
        'getUserDetails',
    ]);

    Route::post('/update_profile', [
        AuthApiController::class,
        'profileUpdate',
    ]);
    // api for links
    Route::get('/master-links', [
        MasterLinkController::class,
        'index',
    ]);
    Route::post('/master-links', [
        MasterLinkController::class,
        'store',
    ]);
    Route::get('/master-links/{id}', [
        MasterLinkController::class,
        'show',
    ]);
    Route::post('/master-links/{id}', [
        MasterLinkController::class,
        'update',
    ]);
    Route::delete('/master-links/{id}', [
        MasterLinkController::class,
        'destroy',
    ]);

    Route::match(['get', 'post'], '/master-user-keywords', [
        MasterKeywordController::class,
        'index',
    ]);

    // Delete keyword
    Route::delete('/master-user-keywords/{id}', [
        MasterKeywordController::class,
        'deleteKeyword',
    ]);

    Route::get('/user-keywords/{user_id}', [
        MasterKeywordController::class,
        'getKeywordsByUserId',
    ]);

    Route::put('/updated-user-keywords/{id}', [
        MasterKeywordController::class,
        'updateKeyword',
    ]);

    // GET all + POST create
    Route::match(['get', 'post'], '/business-info', [
        BusinessInfoApiController::class,
        'index',
    ]);

    // GET particular business info by id
    Route::get('/business-info/{id}', [
        BusinessInfoApiController::class,
        'show',
    ]);

    // UPDATE
    Route::post('/business-info-update/{id}', [
        BusinessInfoApiController::class,
        'update',
    ]);

    // DELETE
    Route::delete('/business-info-delete/{id}', [
        BusinessInfoApiController::class,
        'delete',
    ]);

    // GET by user id (admin type)
    Route::get('/business-info-by-user/{user_id}', [
        BusinessInfoApiController::class,
        'getByUserId',
    ]);

    // ✅ 1) GET all products (search + sort + pagination)
    Route::get('/products', [
        ProductApiController::class,
        'index',
    ]);

    // ✅ 2) GET products by user_id (search + sort + pagination)
    Route::get('/products-by-user/{user_id}', [
        ProductApiController::class,
        'productsByUser',
    ]);

    // ✅ 3) GET product by id (optional but useful)
    Route::get('/products/{id}', [
        ProductApiController::class,
        'show',
    ]);

    // ✅ 4) ADD product
    Route::post('/products', [
        ProductApiController::class,
        'store',
    ]);

    // ✅ 5) UPDATE product
    Route::post('/products-update/{id}', [
        ProductApiController::class,
        'update',
    ]);

    // ✅ 6) DELETE product
    Route::delete('/products-delete/{id}', [
        ProductApiController::class,
        'delete',
    ]);

    // ✅ Add Review
    Route::post('/user-reviews', [
        UserReviewApiController::class,
        'store',
    ]);

    Route::post('/user-review/find', [
        UserReviewApiController::class,
        'findReview',
    ]);

    // ✅ Get Reviews of a User (to_user_id)
    Route::get('/user-reviews/{userId}', [
        UserReviewApiController::class,
        'getUserReviews',
    ]);

    // ✅ Get Average Rating of a User...
    Route::get('/user-reviews/{userId}/average', [
        UserReviewApiController::class,
        'getUserAverageRating',
    ]);

    // ✅ Get My Given Reviews
    Route::get('/my-reviews', [
        UserReviewApiController::class,
        'myGivenReviews',
    ]);

    // ✅ Delete Review (optional)
    Route::delete('/user-reviews/{id}', [
        UserReviewApiController::class,
        'delete',
    ]);

    Route::post('/profile-view/start', [
        ProfileViewController::class,
        'startView',
    ]);
    Route::post('/profile-view/end', [
        ProfileViewController::class,
        'endView',
    ]);

    Route::post('/profile-link/click', [
        ProfileLinkClickController::class,
        'click',
    ]);

    Route::get('/check-friendship/{userId}/{viewerId}', [
        ConnectionApiController::class,
        'checkFriendship',
    ]);

    Route::post('/user-analytics', [
        UserAnalyticsController::class,
        'userAnalytics',
    ]);

    Route::post('/users-with-business/list', [
        UserListController::class,
        'listUsers',
    ]);

    Route::post('/profile-completion', [
        UserListController::class,
        'profileCompletion',
    ]);

    Route::post('/lead-category-create', [
        LeadCategoryController::class,
        'create',
    ]);
    Route::put('/lead-category-update/{id}', [
        LeadCategoryController::class,
        'update',
    ]);
    Route::delete('/lead-category-delete/{id}', [
        LeadCategoryController::class,
        'delete',
    ]);
    Route::get('/lead-category-user/{user_id}', [
        LeadCategoryController::class,
        'getByUserId',
    ]);

    Route::post('/reviews/generate', [
        ReviewController::class,
        'generateReview',
    ])->name('reviews.generate');

    Route::post('/product-click', [
        ProductClickController::class,
        'store',
    ]);

    Route::get('/product-click', [
        ProductClickController::class,
        'index',
    ]);

    Route::post('/profile-product-clicks', [
        ProductClickController::class,
        'getProfileProductClicks',
    ]);

    // subscription plan apis

     Route::get('/get-subscription-plan', [
        SubscriptionPlanController::class,
      'index'
     ]);      // Get all
    Route::post('/add-subscription-plan', [
        SubscriptionPlanController::class,
     'store'
    ]);     // Create
    Route::get('/get_plan_by_id/{id}', [
        SubscriptionPlanController::class,
     'show'
    ]);   // Single
    Route::put('/update_plan_by_id/{id}', [
        SubscriptionPlanController::class,
     'update'
    ]); // Update.......
    Route::delete('/delete_plan_by_id/{id}', [
        SubscriptionPlanController::class,
     'destroy'
    ]); // Delete

    Route::get('/plans/{id}/features', [
        SubscriptionPlanController::class,
         'getPlanWithFeatures'
    ]);

    Route::get('/get_all_plan_feature', [
        PlanFeatureController::class,
     'index'
    ]);        // All
    Route::post('/add_plan_feature', [
        PlanFeatureController::class,
     'store'
    ]);       // Create
    Route::get('/get_plan_feature_by_id/{id}', [
        PlanFeatureController::class,
     'show'
    ]);     // Single
    Route::put('/update_plan_feature_by_id/{id}', [
        PlanFeatureController::class,
     'update'
    ]);   // Update
    Route::delete('/delete_plan_feature_by_id/{id}', [
        PlanFeatureController::class,
     'destroy'
    ]); // Delete

     Route::get('/get_all_subscribe_user', [
        UserSubscriptionController::class,
      'index'
     ]);
    Route::post('/add_subscription', [
        UserSubscriptionController::class,
     'store'
    ]);
    Route::get('/get_subscription_by_id/{id}', [
        UserSubscriptionController::class,
     'show'
    ]);
    Route::put('/update_subscription_by_id/{id}', [
        UserSubscriptionController::class,
     'update'
    ]);
    Route::delete('/delete_subscription_by_id/{id}', [
        UserSubscriptionController::class,
     'destroy'
    ]);

    // Extra
    Route::get('/user/{userId}/active', [
        UserSubscriptionController::class,
     'getUserActiveSubscription'
    ]);


     Route::get('/get_all_subs_feature', [
        UserSubscriptionFeatureController::class,
      'index'
     ]);
    Route::post('/add_subs_feature', [
        UserSubscriptionFeatureController::class,
     'store'
    ]);
    Route::get('/get_by_id/{id}', [
        UserSubscriptionFeatureController::class,
     'show']);
    Route::put('/updated_by_id/{id}', [
        UserSubscriptionFeatureController::class,
     'update'
    ]);
    Route::delete('/delete_by_id/{id}', [
        UserSubscriptionFeatureController::class,
     'destroy'
    ]);

    // Extra
    Route::get('/subscription/{id}', [UserSubscriptionFeatureController::class, 'getBySubscription']);
    Route::post('/{id}/increment', [UserSubscriptionFeatureController::class, 'incrementUsage']);
    Route::post('/check-feature', [UserSubscriptionFeatureController::class, 'checkFeature']);

    // api for post end..........

    Route::post('/logout', [AuthApiController::class, 'logout']);

});
