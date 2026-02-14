<?php

use App\Http\Controllers\api\auth\AuthApiController;
use App\Http\Controllers\api\site\ChatApiController;
use App\Http\Controllers\api\site\ConnectionApiController;
use App\Http\Controllers\api\site\CustomerMasterApiController;
use App\Http\Controllers\api\site\LeadController;
use App\Http\Controllers\api\site\MasterApiController;
use App\Http\Controllers\api\site\MasterCommentApiController;
use App\Http\Controllers\api\site\MasterEventApiController;
use App\Http\Controllers\api\site\MasterKeywordController;
use App\Http\Controllers\api\site\MasterLinkController;
use App\Http\Controllers\api\site\MasterPostApiController;
use App\Http\Controllers\api\site\PostApiController;
use App\Http\Controllers\api\site\ProductApiController;
use App\Http\Controllers\api\site\ProfileApiController;
use App\Http\Controllers\api\site\RestrictionApiController;
use App\Http\Controllers\api\site\SubscriptionApiController;
use Illuminate\Support\Facades\Route;

Route::post('/check-mobile', [AuthApiController::class, 'checkMobile']);
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
Route::post('/login/phone/check', [AuthApiController::class, 'checkPhone']);
Route::post('/login/pin/check', [AuthApiController::class, 'checkPin']);
Route::post('/login/pin/set', [AuthApiController::class, 'setPin']);
Route::post('/login/pin', [AuthApiController::class, 'loginWithPin']);
Route::post('/login/{phone}', [AuthApiController::class, 'login']);

Route::get('/leads', [LeadController::class, 'index']);
Route::post('/leads', [LeadController::class, 'store']);
Route::get('/leads/{id}', [LeadController::class, 'show']);
Route::put('/leads/{id}', [LeadController::class, 'update']);
Route::delete('/leads/{id}', [LeadController::class, 'destroy']);
Route::post('/ocr-extract', [LeadController::class, 'ocrExtract']);
Route::post('/gpt-ocr', [PostApiController::class, 'gptOcr']);

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

    Route::get('/product', [ProductApiController::class, 'product']);
    Route::get('/product/update/{prod_id}', [
        ProductApiController::class,
        'getProductforUpdate',
    ]);
    Route::post('/product/create', [
        ProductApiController::class,
        'createProduct',
    ]);
    Route::post('/product/update/{prod_id}', [
        ProductApiController::class,
        'updateProduct',
    ]);
    Route::delete('/product/image/{img_id}', [
        ProductApiController::class,
        'deleteProductImage',
    ]);
    Route::delete('/product/link/{link_id}', [
        ProductApiController::class,
        'deleteProductLinks',
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

    // api for post end..

    Route::post('/logout', [AuthApiController::class, 'logout']);

});
