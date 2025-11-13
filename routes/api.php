<?php

use App\Http\Controllers\api\auth\AuthApiController;
use App\Http\Controllers\api\site\ChatApiController;
use App\Http\Controllers\api\site\ConnectionApiController;
use App\Http\Controllers\api\site\CustomerMasterApiController;
use App\Http\Controllers\api\site\MasterApiController;
use App\Http\Controllers\api\site\MasterCommentApiController;
use App\Http\Controllers\api\site\MasterEventApiController;
use App\Http\Controllers\api\site\MasterKeywordApiController;
use App\Http\Controllers\api\site\MasterPostApiController;
use App\Http\Controllers\api\site\ProductApiController;
use App\Http\Controllers\api\site\ProfileApiController;
use App\Http\Controllers\api\site\RestrictionApiController;
use App\Http\Controllers\api\site\SubscriptionApiController;
use Illuminate\Support\Facades\Route;

Route::post("/check-mobile", [AuthApiController::class, "checkMobile"]);
Route::post("/reset-pin-otp", [AuthApiController::class, "resetPinOtp"]);
Route::get("/users/list", [AuthApiController::class, "listUsers"]);
Route::post("/regenerate-otp/{phone}", [
    AuthApiController::class,
    "regenerateOtp",
]);
Route::post("/verify-otp/{phone_no}/{token}", [
    AuthApiController::class,
    "verifyOtp",
]);
Route::post("/registration/{phone_no}/{token}", [
    AuthApiController::class,
    "registration",
]);
Route::post("/login/phone/check", [AuthApiController::class, "checkPhone"]);
Route::post("/login/pin/check", [AuthApiController::class, "checkPin"]);
Route::post("/login/pin/set", [AuthApiController::class, "setPin"]);
Route::post("/login/pin", [AuthApiController::class, "loginWithPin"]);
Route::post("/login/{phone}", [AuthApiController::class, "login"]);

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/master-links", [MasterApiController::class, "getMasterLinks"]);
    Route::get("/master-country", [MasterApiController::class, "getCountry"]);
    Route::get("/master-indus-type", [
        MasterApiController::class,
        "getIndustryTypeList",
    ]);
    Route::post("/master-state", [MasterApiController::class, "getState"]);
    Route::post("/master-district", [
        MasterApiController::class,
        "getDistrict",
    ]);

    Route::get("/profile-card", [
        ProfileApiController::class,
        "usersProfileCard",
    ]);
    Route::match(["get", "post"], "/profile", [
        ProfileApiController::class,
        "getOrUpdateProfile",
    ]);
    Route::match(["get", "post", "put", "patch", "delete"], "/links", [
        ProfileApiController::class,
        "UserLinks",
    ]);
    Route::match(["get", "post"], "/business-info", [
        ProfileApiController::class,
        "BusinessInfo",
    ]);

    Route::get("/product", [ProductApiController::class, "product"]);
    Route::get("/product/update/{prod_id}", [
        ProductApiController::class,
        "getProductforUpdate",
    ]);
    Route::post("/product/create", [
        ProductApiController::class,
        "createProduct",
    ]);
    Route::post("/product/update/{prod_id}", [
        ProductApiController::class,
        "updateProduct",
    ]);
    Route::delete("/product/image/{img_id}", [
        ProductApiController::class,
        "deleteProductImage",
    ]);
    Route::delete("/product/link/{link_id}", [
        ProductApiController::class,
        "deleteProductLinks",
    ]);

    Route::post("/customer-master/index", [
        CustomerMasterApiController::class,
        "index",
    ]);
    Route::post("/customer-master/create", [
        CustomerMasterApiController::class,
        "create",
    ]);
    Route::post("/customer-master/update/{id}", [
        CustomerMasterApiController::class,
        "update",
    ]);

    Route::match(["get", "post"], "/master-keywords", [
        MasterKeywordApiController::class,
        "index",
    ]);
    Route::delete("/master-keywords/{id}", [
        MasterKeywordApiController::class,
        "deleteKeyword",
    ]);
    Route::post("/search-users", [
        MasterKeywordApiController::class,
        "getUsersByKeywords",
    ]);

    Route::get("/connection-show", [
        ConnectionApiController::class,
        "connectionShow",
    ]);
    Route::get("/connection-count", [
        ConnectionApiController::class,
        "connectionCount",
    ]);
    Route::get("/connection-request", [
        ConnectionApiController::class,
        "connectionRequests",
    ]);
    Route::post("/connection-create", [
        ConnectionApiController::class,
        "connectionCreate",
    ]);
    Route::post("/connection-accept/{rid}", [
        ConnectionApiController::class,
        "connectionAccept",
    ]);
    Route::post("/connection-reject/{rid}", [
        ConnectionApiController::class,
        "connectionReject",
    ]);
    Route::post("/connection-remove/{rid}", [
        ConnectionApiController::class,
        "connectionRemove",
    ]);

    Route::post("/show-event", [MasterEventApiController::class, "showEvent"]);
    Route::post("/create-event", [
        MasterEventApiController::class,
        "createEvent",
    ]);
    Route::delete("/delete-event/{eid}", [
        MasterEventApiController::class,
        "deleteEevent",
    ]);

    Route::get("/show-postby-industry/{indus_type}", [
        MasterPostApiController::class,
        "showPostByIndustry",
    ]);
    Route::get("/show-save-postlist", [
        MasterPostApiController::class,
        "showSavePostList",
    ]);
    Route::get("/show-saved-post/{post_id}", [
        MasterPostApiController::class,
        "showSavedPost",
    ]);
    Route::post("/show-post", [MasterPostApiController::class, "showPost"]);
    Route::post("/create-post", [MasterPostApiController::class, "createPost"]);
    Route::post("/save-post/{post_id}", [
        MasterPostApiController::class,
        "toggleSavePost",
    ]);
    Route::post("/toggle-like/{post_id}", [
        MasterPostApiController::class,
        "likeOrUnlike",
    ]);
    Route::delete("/delete-post/{pid}", [
        MasterPostApiController::class,
        "deletePost",
    ]);

    Route::get("/comment/{post_id}", [
        MasterCommentApiController::class,
        "getComments",
    ]);
    Route::post("/comment", [
        MasterCommentApiController::class,
        "createComments",
    ]);
    Route::post("/comment/reply", [
        MasterCommentApiController::class,
        "replyComment",
    ]);
    Route::post("/comment/toggle-like", [
        MasterCommentApiController::class,
        "likeOrUnlikeComment",
    ]);
    Route::delete("/comment/delete/{c_id}", [
        MasterCommentApiController::class,
        "deleteComments",
    ]);

    Route::get("/chat/get-message/{chatId}", [
        ChatApiController::class,
        "getMessages",
    ]);
    Route::post("/chat/create-chat/{other_Id}", [
        ChatApiController::class,
        "createChat",
    ]);
    Route::post("/chat/send-message", [
        ChatApiController::class,
        "sendMessages",
    ]);
    Route::post("/chat/mark-read", [ChatApiController::class, "markAsRead"]);
    Route::post("/chat/like-message", [ChatApiController::class, "toggleLike"]);
    Route::post("/chat/delete-message/{message_Id}", [
        ChatApiController::class,
        "deleteMessage",
    ]);

    Route::get("/restrictions", [
        RestrictionApiController::class,
        "getRestrictionData",
    ]);
    Route::get("/restrictions/{id}", [
        RestrictionApiController::class,
        "getRestriction",
    ]);
    Route::post("/restrictions", [
        RestrictionApiController::class,
        "createRestriction",
    ]);
    Route::put("/restrictions/{id}", [
        RestrictionApiController::class,
        "updateRestriction",
    ]);

    Route::get("/plans", [SubscriptionApiController::class, "showPlan"]);
    Route::get("/plans/{plan_id}", [
        SubscriptionApiController::class,
        "getPlans",
    ]);
    Route::post("/plans", [SubscriptionApiController::class, "createPlan"]);
    Route::put("/plans/{plan_id}", [
        SubscriptionApiController::class,
        "getUpdatePlans",
    ]);
    Route::delete("/plans/{plan_id}", [
        SubscriptionApiController::class,
        "deletePlans",
    ]);
    Route::post("/create-subscription", [
        SubscriptionApiController::class,
        "createSubscription",
    ]);
    Route::post("/features", [
        SubscriptionApiController::class,
        "checkPaidPlans",
    ]);

    Route::post("/logout", [AuthApiController::class, "logout"]);
});
