# ETFC Fight Night — Betting Platform

A city-based fight-night betting platform. Customers register to a city, browse the fight card, build a multi-bet slip, and manage their wallet. Admins manage events, settle fights, and approve wallet requests.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Authentication](#authentication)
- [Response Format](#response-format)
- [Error Handling](#error-handling)
- [API Reference — Public](#api-reference--public)
- [API Reference — Auth](#api-reference--auth)
- [API Reference — Wallet](#api-reference--wallet)
- [API Reference — Bets](#api-reference--bets)
- [Bet Slip Flow](#bet-slip-flow)
- [Tax Calculation](#tax-calculation)
- [Enums](#enums)
- [Local Development](#local-development)

---

## Tech Stack

- **Backend**: Laravel 13, PHP 8.3
- **Auth**: Laravel Sanctum (token-based for API, session for web)
- **Database**: MySQL
- **API Base URL**: `/api/v1`

---

## Authentication

The API uses **Bearer tokens** via Laravel Sanctum.

1. Register or login → receive `token`
2. Include on every protected request:

```
Authorization: Bearer {token}
```

Rate limits apply to auth endpoints (5 requests per minute per IP).

---

## Response Format

All responses are JSON. Successful list endpoints follow Laravel's pagination envelope:

```json
{
  "data": [...],
  "links": { "first": "...", "last": "...", "prev": null, "next": "..." },
  "meta": { "current_page": 1, "last_page": 3, "per_page": 20, "total": 45 }
}
```

Single-resource responses use a named key:

```json
{ "city": { ... } }
{ "event": { ... } }
{ "bet": { ... } }
```

---

## Error Handling

| Status | Meaning |
|--------|---------|
| `401` | Unauthenticated — missing or invalid token |
| `403` | Forbidden — e.g. placing a bet in another city |
| `404` | Resource not found or inactive |
| `422` | Validation failed |
| `429` | Rate limit exceeded |

Validation errors follow Laravel's standard shape:

```json
{
  "message": "The phone field is required.",
  "errors": {
    "phone": ["The phone field is required."]
  }
}
```

---

## API Reference — Public

No authentication required.

---

### `GET /api/v1/cities`

List all active cities.

**Response `200`**
```json
{
  "cities": [
    {
      "id": 1,
      "name": "Addis Ababa",
      "slug": "addis-ababa",
      "hero_title": "ETFC Fight Night",
      "hero_subtitle": "September 5, 2026",
      "hero_image": "https://example.com/storage/cities/hero.jpg",
      "banners": []
    }
  ]
}
```

---

### `GET /api/v1/cities/{city}`

Show a city with its open fight card (events that are `open`, not yet started, not settled).

**Path params**

| Param | Type | Description |
|-------|------|-------------|
| `city` | integer | City ID |

**Response `200`**
```json
{
  "city": {
    "id": 1,
    "name": "Addis Ababa",
    "slug": "addis-ababa",
    "hero_title": "ETFC Fight Night",
    "hero_subtitle": null,
    "hero_image": null,
    "banners": [
      {
        "id": 1,
        "title": "Grand Opening",
        "subtitle": "50% bonus on first deposit",
        "image": "https://example.com/storage/banners/1.jpg",
        "link": null,
        "position": 1
      }
    ]
  },
  "events": [
    {
      "id": 10,
      "title": "Abel vs Dawit",
      "description": "Lightweight championship bout",
      "banner": null,
      "participants": [
        { "name": "Abel Tesfaye", "image": null },
        { "name": "Dawit Haile", "image": null }
      ],
      "status": "open",
      "starts_at": "2026-09-05T18:00:00.000000Z",
      "city_event_id": 42,
      "is_settled": false,
      "betting_options": [
        { "id": 101, "name": "Abel Tesfaye Wins", "odds": "1.85", "position": 1 },
        { "id": 102, "name": "Dawit Haile Wins", "odds": "2.10", "position": 2 },
        { "id": 103, "name": "Draw", "odds": "8.00", "position": 3 }
      ]
    }
  ]
}
```

---

### `GET /api/v1/cities/{city}/events/{event}`

Show a single event with all its betting options for a given city.

**Path params**

| Param | Type | Description |
|-------|------|-------------|
| `city` | integer | City ID |
| `event` | integer | Event ID |

**Response `200`**
```json
{
  "event": {
    "id": 10,
    "title": "Abel vs Dawit",
    "description": "Lightweight championship bout",
    "banner": null,
    "participants": [
      { "name": "Abel Tesfaye", "image": null },
      { "name": "Dawit Haile", "image": null }
    ],
    "status": "open",
    "starts_at": "2026-09-05T18:00:00.000000Z",
    "city_event_id": 42,
    "is_settled": false,
    "betting_options": [
      { "id": 101, "name": "Abel Tesfaye Wins", "odds": "1.85", "position": 1 },
      { "id": 102, "name": "Dawit Haile Wins", "odds": "2.10", "position": 2 },
      { "id": 103, "name": "Draw", "odds": "8.00", "position": 3 }
    ]
  }
}
```

---

## API Reference — Auth

---

### `POST /api/v1/auth/register`

Create a new customer account. Rate limited to **5 requests/minute**.

**Request body**

```json
{
  "name": "Abebe Girma",
  "phone": "0911000000",
  "password": "secret123",
  "password_confirmation": "secret123",
  "city_id": 1
}
```

| Field | Type | Rules |
|-------|------|-------|
| `name` | string | required, max 255 |
| `phone` | string | required, unique, max 30 |
| `password` | string | required, min 6, confirmed |
| `city_id` | integer | required, must be an active city |

**Response `201`**
```json
{
  "message": "Account created successfully.",
  "token": "1|abcdefghij...",
  "user": {
    "id": 5,
    "name": "Abebe Girma",
    "phone": "0911000000",
    "city": {
      "id": 1,
      "name": "Addis Ababa",
      "slug": "addis-ababa",
      "hero_title": null,
      "hero_subtitle": null,
      "hero_image": null,
      "banners": []
    },
    "created_at": "2026-08-11T10:00:00.000000Z"
  }
}
```

---

### `POST /api/v1/auth/login`

Authenticate and receive a token. Rate limited to **5 requests/minute**.

**Request body**

```json
{
  "phone": "0911000000",
  "password": "secret123"
}
```

**Response `200`**
```json
{
  "message": "Logged in successfully.",
  "token": "2|xyz...",
  "user": { "...same shape as register..." }
}
```

**Response `422`** — wrong credentials
```json
{
  "message": "These credentials do not match our records.",
  "errors": { "phone": ["These credentials do not match our records."] }
}
```

> Admin accounts cannot log in via the customer API.

---

### `POST /api/v1/auth/logout`

🔒 Requires auth. Revokes the current token.

**Response `200`**
```json
{ "message": "Logged out successfully." }
```

---

### `GET /api/v1/auth/me`

🔒 Requires auth. Returns the authenticated user and wallet balances.

**Response `200`**
```json
{
  "user": {
    "id": 5,
    "name": "Abebe Girma",
    "phone": "0911000000",
    "city": { "...city object..." },
    "created_at": "2026-08-11T10:00:00.000000Z"
  },
  "wallet": {
    "balance": "500.00",
    "available_balance": "350.00"
  }
}
```

> `available_balance` = `balance` minus the sum of pending withdrawal requests.

---

## API Reference — Wallet

All wallet endpoints require authentication (`Authorization: Bearer {token}`).

---

### `GET /api/v1/wallet`

Show wallet balance, transaction history, and request history (both paginated at 20 per page).

**Response `200`**
```json
{
  "wallet": {
    "balance": "500.00",
    "available_balance": "350.00",
    "pending_withdrawals": "150.00"
  },
  "transactions": {
    "data": [
      {
        "id": 12,
        "type": "bet_stake",
        "type_label": "Bet Stake",
        "is_credit": false,
        "amount": "100.00",
        "balance_after": "400.00",
        "description": "Bet stake on Abel Tesfaye Wins",
        "created_at": "2026-08-11T10:30:00.000000Z"
      }
    ],
    "links": { "...pagination links..." },
    "meta": { "current_page": 1, "per_page": 20, "total": 5 }
  },
  "requests": {
    "data": [
      {
        "id": 3,
        "type": "deposit",
        "type_label": "Deposit",
        "amount": "500.00",
        "status": "approved",
        "status_label": "Approved",
        "note": "CBE transfer",
        "admin_note": null,
        "reviewed_at": "2026-08-11T09:00:00.000000Z",
        "created_at": "2026-08-11T08:45:00.000000Z"
      }
    ],
    "links": { "...pagination links..." },
    "meta": { "...pagination meta..." }
  }
}
```

**Transaction `type` values**: `deposit`, `withdrawal`, `bet_stake`, `bet_win`, `bet_refund`

---

### `POST /api/v1/wallet/deposit`

Submit a deposit request. Funds appear in the wallet only after admin approval.

**Request body**

```json
{
  "amount": 500,
  "note": "CBE transfer ref #12345"
}
```

| Field | Type | Rules |
|-------|------|-------|
| `amount` | numeric | required, min 1, max 1,000,000 |
| `note` | string | optional, max 255 |

**Response `201`**
```json
{
  "message": "Deposit request submitted. Waiting for admin approval.",
  "request": {
    "id": 4,
    "type": "deposit",
    "type_label": "Deposit",
    "amount": "500.00",
    "status": "pending",
    "status_label": "Pending",
    "note": "CBE transfer ref #12345",
    "admin_note": null,
    "reviewed_at": null,
    "created_at": "2026-08-11T11:00:00.000000Z"
  }
}
```

---

### `POST /api/v1/wallet/withdraw`

Submit a withdrawal request. The amount is immediately reserved (reduces `available_balance`) until approved or rejected.

**Request body**

```json
{
  "amount": 200,
  "note": "Withdraw to TeleBirr"
}
```

| Field | Type | Rules |
|-------|------|-------|
| `amount` | numeric | required, min 1, max 1,000,000, must not exceed `available_balance` |
| `note` | string | optional, max 255 |

**Response `201`** — same shape as deposit request response.

**Response `422`** — insufficient balance
```json
{
  "message": "Insufficient available balance.",
  "errors": { "amount": ["Insufficient available balance for this withdrawal."] }
}
```

---

## API Reference — Bets

All bet endpoints require authentication (`Authorization: Bearer {token}`).

Customers can only place bets on events in their **registered city**.

---

### `GET /api/v1/bets`

List the authenticated user's bets, newest first (20 per page).

**Response `200`**
```json
{
  "data": [
    {
      "id": 7,
      "event_title": "Abel vs Dawit",
      "city_name": "Addis Ababa",
      "option_name": "Abel Tesfaye Wins",
      "stake": "100.00",
      "odds": "1.85",
      "potential_payout": "185.00",
      "status": "pending",
      "status_label": "Pending",
      "settled_at": null,
      "placed_at": "2026-08-11T10:30:00.000000Z"
    }
  ],
  "links": { "...pagination links..." },
  "meta": { "current_page": 1, "per_page": 20, "total": 1 }
}
```

**Bet `status` values**: `pending`, `won`, `lost`, `cancelled`, `refunded`

> `potential_payout` is the gross payout (stake × odds). See [Tax Calculation](#tax-calculation) to compute the net payout.

---

### `POST /api/v1/bets/multi`

Place multiple bets in a single atomic transaction — the primary endpoint for the bet slip. All bets are validated together; if any fails, none are placed and no funds are deducted.

> Maximum 10 bets per request.

**Request body**

```json
{
  "bets": [
    {
      "city_event_id": 42,
      "betting_option_id": 101,
      "stake": 100
    },
    {
      "city_event_id": 43,
      "betting_option_id": 205,
      "stake": 100
    }
  ]
}
```

| Field | Type | Rules |
|-------|------|-------|
| `bets` | array | required, 1–10 items |
| `bets.*.city_event_id` | integer | required, must be an active event in the user's registered city |
| `bets.*.betting_option_id` | integer | required, must belong to the given `city_event_id` and be active |
| `bets.*.stake` | numeric | required, min 1, max 1,000,000 |

**Business rules checked atomically**:
- Each event's status must be `open`
- Each event's `starts_at` must be in the future
- Each city event must not be settled
- The user's `available_balance` must cover the **total stake** across all bets

**Response `201`**
```json
{
  "message": "2 bets placed successfully.",
  "placed": 2,
  "bets": [
    {
      "id": 8,
      "event_title": "Abel vs Dawit",
      "city_name": "Addis Ababa",
      "option_name": "Abel Tesfaye Wins",
      "stake": "100.00",
      "odds": "1.85",
      "potential_payout": "185.00",
      "status": "pending",
      "status_label": "Pending",
      "settled_at": null,
      "placed_at": "2026-08-11T10:30:00.000000Z"
    },
    {
      "id": 9,
      "event_title": "Kalid vs Yonas",
      "city_name": "Addis Ababa",
      "option_name": "Kalid Ibrahim Wins",
      "stake": "100.00",
      "odds": "2.10",
      "potential_payout": "210.00",
      "status": "pending",
      "status_label": "Pending",
      "settled_at": null,
      "placed_at": "2026-08-11T10:30:00.000000Z"
    }
  ]
}
```

**Response `422`** — any validation or business rule failure (no bets are placed)
```json
{
  "message": "Insufficient balance for the total stake of 200.00 ETB.",
  "errors": { "bets": ["Insufficient balance for the total stake of 200.00 ETB."] }
}
```

---

### `POST /api/v1/cities/{city}/events/{event}/bets`

Place a single bet on a specific event.

**Path params**

| Param | Type | Description |
|-------|------|-------------|
| `city` | integer | City ID |
| `event` | integer | Event ID |

**Request body**

```json
{
  "betting_option_id": 101,
  "stake": 150
}
```

| Field | Type | Rules |
|-------|------|-------|
| `betting_option_id` | integer | required, must belong to this city-event and be active |
| `stake` | numeric | required, min 1, max 1,000,000 |

**Response `201`**
```json
{
  "message": "Bet placed successfully.",
  "bet": { "...bet object..." }
}
```

---

## Bet Slip Flow

Recommended flow for a multi-bet slip frontend:

```
1. GET  /api/v1/cities/{city}
        → returns fight card; each event has city_event_id + betting_options

2. User taps odds buttons → build local slip array:
   [
     { city_event_id: 42, betting_option_id: 101, stake: 100 },
     { city_event_id: 43, betting_option_id: 205, stake: 100 },
   ]

3. (Optional) GET /api/v1/auth/me
        → check available_balance before submitting

4. POST /api/v1/bets/multi  { bets: [...] }
        → all bets placed atomically, or none if any fails

5. GET  /api/v1/bets
        → confirm placement, show updated bet history
```

---

## Tax Calculation

Winnings are subject to **15% tax on profit only** (not on the stake returned).

```
gross_payout  = stake × odds
profit        = gross_payout − stake
tax           = profit × 0.15
net_payout    = gross_payout − tax
```

**Example** — 100 ETB stake at 1.85× odds:

| | ETB |
|--|--:|
| Gross payout | 185.00 |
| Stake (returned) | 100.00 |
| Profit | 85.00 |
| Tax (15% of profit) | −12.75 |
| **Net payout** | **172.25** |

The API returns `potential_payout` = gross. Apply the formula client-side to display the net amount.

---

## Enums

### Bet Status

| Value | Label |
|-------|-------|
| `pending` | Pending |
| `won` | Won |
| `lost` | Lost |
| `cancelled` | Cancelled |
| `refunded` | Refunded |

### Event Status

| Value | Label |
|-------|-------|
| `draft` | Draft |
| `open` | Open |
| `closed` | Closed |
| `cancelled` | Cancelled |

### Wallet Request Type

| Value | Label |
|-------|-------|
| `deposit` | Deposit |
| `withdrawal` | Withdrawal |

### Wallet Request Status

| Value | Label |
|-------|-------|
| `pending` | Pending |
| `approved` | Approved |
| `rejected` | Rejected |

### Wallet Transaction Type

| Value | Label | Direction |
|-------|-------|-----------|
| `deposit` | Deposit | Credit ✅ |
| `withdrawal` | Withdrawal | Debit ❌ |
| `bet_stake` | Bet Stake | Debit ❌ |
| `bet_win` | Bet Win | Credit ✅ |
| `bet_refund` | Bet Refund | Credit ✅ |

Use `is_credit` from the transaction resource (`true`/`false`) instead of hard-coding this mapping.

---

## Local Development

**Requirements**: PHP 8.3, Composer, Node 20+, MySQL

```bash
# Install dependencies
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
php artisan db:seed --class=DemoSeeder

# Storage link (for uploaded images)
php artisan storage:link

# Start dev servers (Laravel + Vite concurrently)
composer run dev
```

**Run tests**
```bash
php artisan test --compact
```

**Format PHP**
```bash
vendor/bin/pint --dirty
```
