# Bynd AI Backend Integration Guide

This guide provides the necessary information to integrate the Bynd AI backend API with your application.

## General Information

- **Base URL**: `http://your-domain.com/api` (Replace with your server URL)
- **Headers**:
    - `Accept`: `application/json`
    - `Content-Type`: `application/json`
    - `Authorization`: `Bearer {token}` (Required for protected routes)

---

## 1. Authentication Flow

The backend uses **Laravel Sanctum** for authentication.

### **Step 1: Check Mobile & Send OTP**
Check if a phone number exists. If not, an OTP is sent for registration.
- **Endpoint**: `POST /check-mobile`
- **Body**:
```json
{
    "phone_no": "1234567890"
}
```
- **Response (Registration required)**: `200 OK`
```json
{
    "status": true,
    "process": "register",
    "message": "User not found. OTP sent for verification.",
    "data": { "token": "...", "expires_at": "..." }
}
```
- **Response (Login ready)**: `200 OK`
```json
{
    "status": true,
    "process": "login",
    "user": { ... }
}
```

### **Step 2: Verify OTP**
- **Endpoint**: `POST /verify-otp/{phone_no}/{token}`
- **Body**:
```json
{
    "otp": "1234"
}
```

### **Step 3: Registration**
- **Endpoint**: `POST /registration/{phone_no}/{token}`
- **Body**:
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "yourpassword"
}
```
- **Response**: Returns `token` and `user` object.

### **Step 4: Login Methods**

#### **A. Login with PIN**
- **Endpoint**: `POST /login/pin`
- **Body**: `{ "phone_no": "1234567890", "pin": "123456" }`

#### **B. Login with Password**
- **Endpoint**: `POST /login/phone/check` THEN `POST /login/pin` or `POST /login/{phone}` based on user choice.

---

## 2. Profile Management
(Requires Authorization Header)

### **Get/Update Profile**
- **Endpoint**: `GET|POST /profile`
- **GET Response**: Returns user details, business info, country, state, and district.
- **POST Body**: (Form-data for file uploads)
    - `fname`: string
    - `dept`: string
    - `desg`: string
    - `profile_pic`: file
    - `profile_banner`: file

### **Business Information**
- **Endpoint**: `GET|POST /business-info`
- **POST Body**: `company_name`, `gst_no`, `address`, `bank_acc_no`, `ifsc_code`, `branch_name`, `payment_qr` (image).

### **Social Links**
- **Endpoint**: `GET|POST|PUT|DELETE /links`
- **Method POST**: `{ "link_id": 1, "title": "Instagram", "link_data": "https://..." }`

---

## 3. Product Management
(Requires Authorization Header)

### **List Products**
- **Endpoint**: `GET /product`

### **Create Product**
- **Endpoint**: `POST /product/create`
- **Body**:
```json
{
    "product_name": "Product Name",
    "product_price": 100,
    "description": "Details",
    "title_links": ["Link Title"],
    "links": ["https://..."],
    "file_name": ["input_field_name_for_file"]
}
```

---

## 4. Social Features (Posts & Comments)
(Requires Authorization Header)

### **List Posts by Industry**
- **Endpoint**: `GET /show-postby-industry/{indus_type}`

### **Create Post**
- **Endpoint**: `POST /create-post`
- **Body**: (Form-data)
    - `description`: string
    - `image`: file (optional)

### **Toggle Save/Like Post**
- **Endpoint**: `POST /save-post/{post_id}`
- **Endpoint**: `POST /toggle-like/{post_id}`

### **Comments**
- **List Comments**: `GET /comment/{post_id}`
- **Create Comment**: `POST /comment` (Body: `{ "post_id": 1, "comment": "Nice!" }`)

---

## 5. Subscriptions & Plans
(Requires Authorization Header)

### **List Plans**
- **Endpoint**: `GET /plans`

### **Create Subscription**
- **Endpoint**: `POST /create-subscription`
- **Body**: `{ "plan_id": 1, "payment_id": "TRANS_ID" }`

---

## 6. Master Data
(Requires Authorization Header)

- **Countries**: `GET /master-country`
- **States**: `POST /master-state` (Body: `{ "country_id": 1 }`)
- **Districts**: `POST /master-district` (Body: `{ "state_id": 1 }`)

---

## 7. Connections & Chat
(Requires Authorization Header)

- **List Connections**: `GET /connection-show`
- **Connection Request**: `POST /connection-create` (Body: `{ "request_to": user_id }`)
- **Send Message**: `POST /chat/send-message` (Body: `{ "chat_id": 1, "message": "Hello" }`)

---

## Error Codes
- `401 Unauthorized`: Missing or invalid Bearer token.
- `422 Unprocessable Entity`: Validation error (check response for details).
- `404 Not Found`: Resource not found.
