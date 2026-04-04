# API Integration Guide

This document contains a reference for key API endpoints available in the application, specifically focusing on profiles, products, user details, connections, and reviews. 
Unless specified otherwise, these routes require Sanctum authentication (`auth:sanctum`), and should include the `/api` prefix.

## 1. User Details
Endpoints to fetch and search for user information.

- **GET** `/api/user-details/{id}` - Fetch details of a specific user. (Controller: `PostApiController@getUserDetails`)
- **GET** `/api/users/list` - Fetch a list of users (Public/Unauthenticated).
- **POST** `/api/users/filter-list` - Fetch a filtered list of users (Public/Unauthenticated).
- **POST** `/api/users-with-business/list` - Get a list of users with their business information.
- **POST** `/api/search-users` - Search users by keywords.
- **POST** `/api/profile-completion` - Get user profile completion status metrics.

## 2. Profile Management
Endpoints related to updating and retrieving user profiles.

- **GET / POST** `/api/profile` - Get or update the authenticated user's profile.
- **POST** `/api/update_profile` - Alternate endpoint to update user profile.
- **GET** `/api/profile-card` - Get the authenticated user's profile card data.
- **GET** `/api/profile-card/{user_id}` - Get profile card data for a specific user ID.
- **GET** `/api/user-profile/{user_name}` - Get detailed profile data by user name.
- **POST** `/api/view-profile` - Record a page view on a user's profile.
- **POST** `/api/show-mini-website` - Show mini website generated for the user.
- **POST** `/api/upload-profile-pic` - Upload a new profile picture.

## 3. Business Information
Endpoints for managing business details (such as business name, industry, etc.) associated with a user or profile.

- **GET / POST** `/api/business-info` - Get all business information records, or create a new business profile.
- **GET** `/api/business-info/{id}` - Fetch a specific business information record by its ID.
- **POST** `/api/business-info-update/{id}` - Update a specific business information record.
- **DELETE** `/api/business-info-delete/{id}` - Delete a specific business information record.
- **GET** `/api/business-info-by-user/{user_id}` - Fetch the business information associated with a specific user.

## 4. Product Management
Endpoints for creating, fetching, and updating products.

- **GET** `/api/products` - Fetch all products (supports search, sort, pagination).
- **GET** `/api/products/{id}` - Fetch details of a single product.
- **GET** `/api/products-by-user/{user_id}` - Fetch all products belonging to a specific user.
- **POST** `/api/products` - Create a new product.
- **POST** `/api/products-update/{id}` - Update an existing product.
- **DELETE** `/api/products-delete/{id}` - Delete a product.
- **POST** `/api/product-click` - Record a click/view on a specific product.
- **GET** `/api/product-click` - Get product click statistics/logs.
- **POST** `/api/profile-product-clicks` - Get aggregate product click counts for a profile.

## 5. Connections & Friend Requests
Endpoints to handle user connection sending, accepting, and management.

- **POST** `/api/connection-create` - Create a direct connection immediately.
- **POST** `/api/connection-request` - Send a connection (friend) request to another user.
- **POST** `/api/connection-request/update-status` - Update connection request status.
- **POST** `/api/connection-accept/{rid}` - Accept an incoming connection request by ID.
- **POST** `/api/connection-reject/{rid}` - Reject an incoming connection request by ID.
- **GET** `/api/connection-show` - Fetch the user's active connections/friends list.
- **GET** `/api/connection-request` - Fetch pending connection requests for the user.
- **GET** `/api/connection-count` - Get the user's connection statistics/counts.
- **DELETE** `/api/connections/remove` - Remove an existing connection.
- **GET** `/api/friends/{user_id}` - Get a list of friends for a particular user ID.
- **POST** `/api/scanner-connection-create` - Create a connection by scanning a QR code.
- **GET** `/api/check-friendship/{userId}/{viewerId}` - Check the friendship status between two specified users.

## 6. Reviews
Endpoints for storing, fetching, and deleting user reviews.

- **POST** `/api/user-reviews` - Store a new review for a user.
- **GET** `/api/user-reviews/{userId}` - Fetch all reviews received by a specific user.
- **GET** `/api/user-reviews/{userId}/average` - Get the average review rating of a specific user.
- **GET** `/api/my-reviews` - Fetch reviews that the authenticated user has written.
- **POST** `/api/user-review/find` - Find or query a specific review.
- **DELETE** `/api/user-reviews/{id}` - Delete a specific review.
- **POST** `/api/reviews/generate` - Auto-generate or draft a review (e.g., using AI/GPT).
