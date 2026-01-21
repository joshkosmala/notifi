# Notifi API Documentation

Base URL: `/api/v1`

## Authentication

The API uses phone-based authentication with SMS verification codes. After verification, a Bearer token is issued for authenticated requests.

### Auth Flow

1. Request verification code → `POST /auth/request-code`
2. User receives 6-digit SMS code
3. Verify code → `POST /auth/verify`
4. Use returned Bearer token for authenticated requests

### Headers

For authenticated endpoints, include:

```
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

---

## Public Endpoints

### Request Verification Code

```
POST /api/v1/auth/request-code
```

Request body:
```json
{
  "phone": "+64211234567"
}
```

Response:
```json
{
  "message": "Verification code sent.",
  "expires_in": 600
}
```

> **Note:** In local development, the `code` is included in the response for testing.

---

### Verify Code

```
POST /api/v1/auth/verify
```

Request body:
```json
{
  "phone": "+64211234567",
  "code": "123456"
}
```

Response:
```json
{
  "message": "Phone verified successfully.",
  "token": "1|abc123...",
  "subscriber": {
    "id": 1,
    "phone": "+64211234567",
    "name": null,
    "email": null
  }
}
```

---

### List Organisations

```
GET /api/v1/organisations
```

Query parameters:
- `search` - Filter by organisation name
- `page` - Pagination

Response:
```json
{
  "organisations": {
    "data": [
      {
        "id": 1,
        "name": "Tauranga City Council",
        "url": "https://www.tauranga.govt.nz"
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "total": 2
  }
}
```

---

### Get Organisation

```
GET /api/v1/organisations/{id}
```

Response:
```json
{
  "organisation": {
    "id": 1,
    "name": "Tauranga City Council",
    "url": "https://www.tauranga.govt.nz",
    "address": "123 Main St, Tauranga"
  }
}
```

---

## Protected Endpoints

All endpoints below require `Authorization: Bearer {token}` header.

### Get Current Subscriber

```
GET /api/v1/auth/me
```

Response:
```json
{
  "subscriber": {
    "id": 1,
    "phone": "+64211234567",
    "name": "John Doe",
    "email": "john@example.com",
    "phone_verified_at": "2026-01-21T10:00:00Z"
  }
}
```

---

### Update Profile

```
PUT /api/v1/auth/me
```

Request body:
```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

Response:
```json
{
  "message": "Profile updated.",
  "subscriber": {
    "id": 1,
    "phone": "+64211234567",
    "name": "John Doe",
    "email": "john@example.com"
  }
}
```

---

### Logout

```
POST /api/v1/auth/logout
```

Response:
```json
{
  "message": "Logged out successfully."
}
```

---

### List Subscriptions

```
GET /api/v1/subscriptions
```

Response:
```json
{
  "subscriptions": [
    {
      "id": 1,
      "name": "Tauranga City Council",
      "url": "https://www.tauranga.govt.nz",
      "subscribed_at": "2026-01-21T10:00:00Z"
    }
  ]
}
```

---

### Subscribe to Organisation

```
POST /api/v1/subscriptions
```

Request body:
```json
{
  "organisation_id": 1
}
```

Response:
```json
{
  "message": "Subscribed successfully."
}
```

---

### Unsubscribe from Organisation

```
DELETE /api/v1/subscriptions/{organisation_id}
```

Response:
```json
{
  "message": "Unsubscribed successfully."
}
```

---

### List Notifications

```
GET /api/v1/notifications
```

Returns notifications from subscribed organisations.

Query parameters:
- `page` - Pagination

Response:
```json
{
  "notifications": {
    "data": [
      {
        "id": 1,
        "title": "Road Closure - Marine Parade",
        "body": "Marine Parade will be closed...",
        "link": "https://example.com/more-info",
        "sent_at": "2026-01-21T10:00:00Z",
        "organisation": {
          "id": 1,
          "name": "Tauranga City Council"
        }
      }
    ],
    "current_page": 1,
    "last_page": 1,
    "total": 5
  }
}
```

---

### Get Notification

```
GET /api/v1/notifications/{id}
```

Response:
```json
{
  "notification": {
    "id": 1,
    "title": "Road Closure - Marine Parade",
    "body": "Marine Parade will be closed...",
    "link": "https://example.com/more-info",
    "sent_at": "2026-01-21T10:00:00Z",
    "organisation": {
      "id": 1,
      "name": "Tauranga City Council"
    }
  }
}
```

---

### Register Device for Push Notifications

```
POST /api/v1/device
```

Request body:
```json
{
  "token": "fcm_or_apns_device_token",
  "platform": "ios"
}
```

Platform must be `ios` or `android`.

Response:
```json
{
  "message": "Device registered for push notifications."
}
```

---

### Unregister Device

```
DELETE /api/v1/device
```

Response:
```json
{
  "message": "Device unregistered from push notifications."
}
```

---

## Error Responses

### Validation Error (422)

```json
{
  "message": "The phone field is required.",
  "errors": {
    "phone": ["The phone field is required."]
  }
}
```

### Unauthorized (401)

```json
{
  "message": "Unauthenticated."
}
```

### Not Found (404)

```json
{
  "message": "Phone number not found. Please request a new code."
}
```

---

## Phone Number Format

Phone numbers must be in E.164 format:
- Start with `+` followed by country code
- No spaces or dashes
- Examples: `+64211234567` (NZ), `+61412345678` (AU), `+14155551234` (US)
