# Betting App — API Reference v1

**Base URL:** `https://your-domain.com/api/v1`
**Content-Type:** `application/json`
**Accept:** `application/json`

> Replace `your-domain.com` with the actual server host. During local development this is `http://localhost:8000/api/v1`.

---

## Authentication

Protected endpoints require this header on every request:

```
Authorization: Bearer <token>
```

Tokens are returned by register and login. Store the token in local storage and attach it to every authenticated request. If the token is missing or invalid the API returns `401 Unauthorized`.

---

## Response format

### Success
```json
{ "key": "value or object or array" }
```

### Validation error (422)
```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."],
    "phone": ["The phone has already been taken."]
  }
}
```

### Auth error (401)
```json
{ "message": "Unauthenticated." }
```

### Forbidden (403)
```json
{ "message": "You can only place bets in your registered city." }
```

### Not found (404)
```json
{ "message": "Not Found." }
```

---

## Paginated responses

List endpoints return this structure. Navigate with `?page=2`.

```json
{
  "data": [ ...items... ],
  "links": {
    "first": "https://your-domain.com/api/v1/bets?page=1",
    "last":  "https://your-domain.com/api/v1/bets?page=4",
    "prev":  null,
    "next":  "https://your-domain.com/api/v1/bets?page=2"
  },
  "meta": {
    "current_page": 1,
    "last_page": 4,
    "per_page": 20,
    "total": 73,
    "from": 1,
    "to": 20
  }
}
```

---

## Data types

| Type | Format |
|---|---|
| Money | Decimal string e.g. `"250.00"` — never a raw float |
| Dates | ISO 8601 UTC e.g. `"2026-08-15T18:00:00.000000Z"` |
| Images | Absolute URL e.g. `"https://your-domain.com/storage/events/abc.jpg"` — use directly in `<img src>` |
| Nullable | `null` when not set |

---

## Enum reference

### Event status
| Value | Label | Meaning |
|---|---|---|
| `open` | Open | Accepting bets |
| `closed` | Closed | No more bets, awaiting settlement |
| `draft` | Draft | Not yet published |
| `cancelled` | Cancelled | Event cancelled |

### Bet status
| Value | Label | Meaning |
|---|---|---|
| `pending` | Pending | Bet placed, not yet settled |
| `won` | Won | User won, payout credited |
| `lost` | Lost | User lost |
| `cancelled` | Cancelled | Bet cancelled |
| `refunded` | Refunded | Stake returned |

### Wallet request type
| Value | Label | Meaning |
|---|---|---|
| `deposit` | Deposit | Customer adding money |
| `withdrawal` | Withdrawal | Customer withdrawing money |

### Wallet request status
| Value | Label | Meaning |
|---|---|---|
| `pending` | Pending | Waiting for admin review |
| `approved` | Approved | Admin approved, balance updated |
| `rejected` | Rejected | Admin rejected |

### Wallet transaction type
| Value | Label | Credit? | Meaning |
|---|---|---|---|
| `deposit` | Deposit | yes | Funds added (approved deposit) |
| `withdrawal` | Withdrawal | no | Funds removed (approved withdrawal) |
| `bet_stake` | Bet Stake | no | Stake deducted when bet placed |
| `bet_win` | Bet Win | yes | Winnings credited after settlement |
| `bet_refund` | Bet Refund | yes | Stake refunded (cancelled event) |

---

## Endpoints

---

### 1. List cities

Used to populate the city picker on the registration screen.

```
GET /cities
```

**Auth:** None

**Response 200**
```json
{
  "cities": [
    {
      "id": 1,
      "name": "Addis Ababa",
      "slug": "addis",
      "hero_title": "Bet in Addis Ababa",
      "hero_subtitle": "City-specific odds for local events",
      "hero_image": null,
      "banners": []
    },
    {
      "id": 2,
      "name": "Gambella",
      "slug": "gambella",
      "hero_title": "Bet in Gambella",
      "hero_subtitle": "Local odds for Gambella events",
      "hero_image": null,
      "banners": []
    }
  ]
}
```

---

### 2. City homepage

Returns the city's banners and all upcoming open events with city-specific odds. This is the main screen after login.

```
GET /cities/{city}
```

**Auth:** None
**Route param:** `city` — city slug (e.g. `gambella`)

**Response 200**
```json
{
  "city": {
    "id": 2,
    "name": "Gambella",
    "slug": "gambella",
    "hero_title": "Bet in Gambella",
    "hero_subtitle": "Local odds for Gambella events",
    "hero_image": null,
    "banners": [
      {
        "id": 3,
        "title": "Welcome to Gambella",
        "subtitle": "Register now and get your first bet bonus",
        "image": null,
        "link": null,
        "position": 1
      },
      {
        "id": 4,
        "title": "Big Matches This Week",
        "subtitle": "Don't miss out on exclusive city odds",
        "image": null,
        "link": null,
        "position": 2
      }
    ]
  },
  "events": [
    {
      "id": 1,
      "title": "Abel vs Dawit",
      "description": "A showcase matchup between Abel and Dawit.",
      "banner": null,
      "participants": [
        { "name": "Abel",  "image": null },
        { "name": "Dawit", "image": null }
      ],
      "status": "open",
      "starts_at": "2026-08-11T18:00:00.000000Z",
      "city_event_id": 5,
      "is_settled": false,
      "betting_options": [
        { "id": 13, "name": "Abel Wins",  "odds": "2.50", "position": 1 },
        { "id": 14, "name": "Dawit Wins", "odds": "1.60", "position": 2 },
        { "id": 15, "name": "Draw",       "odds": "3.20", "position": 3 }
      ]
    }
  ]
}
```

**Errors**
- `404` — city not found or inactive

---

### 3. Event detail

Full details for a single event in a city with city-specific odds.

```
GET /cities/{city}/events/{event}
```

**Auth:** None
**Route params:** `city` = slug, `event` = event ID

**Response 200**
```json
{
  "event": {
    "id": 1,
    "title": "Abel vs Dawit",
    "description": "A showcase matchup between Abel and Dawit.",
    "banner": null,
    "participants": [
      { "name": "Abel",  "image": null },
      { "name": "Dawit", "image": null }
    ],
    "status": "open",
    "starts_at": "2026-08-11T18:00:00.000000Z",
    "city_event_id": 5,
    "is_settled": false,
    "betting_options": [
      { "id": 13, "name": "Abel Wins",  "odds": "2.50", "position": 1 },
      { "id": 14, "name": "Dawit Wins", "odds": "1.60", "position": 2 },
      { "id": 15, "name": "Draw",       "odds": "3.20", "position": 3 }
    ]
  }
}
```

**Errors**
- `404` — city inactive, event not found in city, or city-event inactive

---

### 4. Register

Creates a customer account. Every user is locked to one city and can only bet in that city.

```
POST /auth/register
```

**Auth:** None | **Rate limit:** 5 per minute

**Request**
```json
{
  "name": "Abel Tesfaye",
  "phone": "0911000001",
  "password": "secret123",
  "password_confirmation": "secret123",
  "city_id": 2
}
```

| Field | Rules |
|---|---|
| `name` | required, max 255 |
| `phone` | required, unique, max 30 |
| `password` | required, min 6 |
| `password_confirmation` | required, must match `password` |
| `city_id` | required, must be an active city ID — get IDs from `GET /cities` |

**Response 201**
```json
{
  "message": "Account created successfully.",
  "token": "1|abc123...",
  "user": {
    "id": 11,
    "name": "Abel Tesfaye",
    "phone": "0911000001",
    "city": {
      "id": 2,
      "name": "Gambella",
      "slug": "gambella",
      "hero_title": "Bet in Gambella",
      "hero_subtitle": "Local odds for Gambella events",
      "hero_image": null,
      "banners": []
    },
    "created_at": "2026-08-09T10:00:00.000000Z"
  }
}
```

> After register: save `token`, then navigate to `GET /cities/{user.city.slug}`.

**Errors**
- `422` — phone already taken, city inactive, passwords don't match

---

### 5. Login

```
POST /auth/login
```

**Auth:** None | **Rate limit:** 5 per minute

**Request**
```json
{
  "phone": "0911000001",
  "password": "secret123"
}
```

**Response 200**
```json
{
  "message": "Logged in successfully.",
  "token": "2|def456...",
  "user": {
    "id": 11,
    "name": "Abel Tesfaye",
    "phone": "0911000001",
    "city": {
      "id": 2,
      "name": "Gambella",
      "slug": "gambella",
      "hero_title": "Bet in Gambella",
      "hero_subtitle": "Local odds for Gambella events",
      "hero_image": null,
      "banners": []
    },
    "created_at": "2026-08-09T10:00:00.000000Z"
  }
}
```

> After login: save `token`, then navigate to `GET /cities/{user.city.slug}`.

**Errors**
- `422` — wrong credentials or admin account

---

### 6. Logout

Invalidates the current token.

```
POST /auth/logout
```

**Auth:** Required

**Response 200**
```json
{ "message": "Logged out successfully." }
```

---

### 7. My profile

Returns the logged-in user and their current wallet balance. Call this on app start to check session validity.

```
GET /auth/me
```

**Auth:** Required

**Response 200**
```json
{
  "user": {
    "id": 11,
    "name": "Abel Tesfaye",
    "phone": "0911000001",
    "city": {
      "id": 2,
      "name": "Gambella",
      "slug": "gambella",
      "hero_title": "Bet in Gambella",
      "hero_subtitle": "Local odds for Gambella events",
      "hero_image": null,
      "banners": []
    },
    "created_at": "2026-08-09T10:00:00.000000Z"
  },
  "wallet": {
    "balance": "350.00",
    "available_balance": "300.00"
  }
}
```

> `available_balance` = `balance` minus locked pending withdrawals. Always use `available_balance` as the max stake, not `balance`.

---

### 8. Wallet

Returns balance, transaction history, and deposit/withdrawal request history.

```
GET /wallet
```

**Auth:** Required | **Query params:** `?page=2` (applies to both `transactions` and `requests`)

**Response 200**
```json
{
  "wallet": {
    "balance": "350.00",
    "available_balance": "300.00",
    "pending_withdrawals": "50.00"
  },
  "transactions": {
    "data": [
      {
        "id": 1,
        "type": "deposit",
        "type_label": "Deposit",
        "is_credit": true,
        "amount": "2000.00",
        "balance_after": "2000.00",
        "description": "Initial deposit approved",
        "created_at": "2026-08-09T09:00:00.000000Z"
      },
      {
        "id": 2,
        "type": "bet_stake",
        "type_label": "Bet Stake",
        "is_credit": false,
        "amount": "100.00",
        "balance_after": "1900.00",
        "description": "Bet on Abel vs Dawit",
        "created_at": "2026-08-09T10:00:00.000000Z"
      }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 2 }
  },
  "requests": {
    "data": [
      {
        "id": 1,
        "type": "deposit",
        "type_label": "Deposit",
        "amount": "2000.00",
        "status": "approved",
        "status_label": "Approved",
        "note": "Initial deposit",
        "admin_note": null,
        "reviewed_at": "2026-08-09T08:50:00.000000Z",
        "created_at": "2026-08-09T08:30:00.000000Z"
      }
    ],
    "links": { "first": "...", "last": "...", "prev": null, "next": null },
    "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 1 }
  }
}
```

---

### 9. Request deposit

Submits a deposit request. Balance is **not** credited until an admin approves it.

```
POST /wallet/deposit
```

**Auth:** Required

**Request**
```json
{
  "amount": 500,
  "note": "CBE transfer ref 12345"
}
```

| Field | Rules |
|---|---|
| `amount` | required, number, min 1, max 1,000,000 |
| `note` | optional, max 255 — include transfer reference, account details, etc. |

**Response 201**
```json
{
  "message": "Deposit request submitted. Waiting for admin approval.",
  "request": {
    "id": 5,
    "type": "deposit",
    "type_label": "Deposit",
    "amount": "500.00",
    "status": "pending",
    "status_label": "Pending",
    "note": "CBE transfer ref 12345",
    "admin_note": null,
    "reviewed_at": null,
    "created_at": "2026-08-09T10:00:00.000000Z"
  }
}
```

**Errors**
- `422` — amount out of range

---

### 10. Request withdrawal

Submits a withdrawal request. The amount is locked from `available_balance` immediately so the user cannot double-spend it. Balance is not debited until admin approves.

```
POST /wallet/withdraw
```

**Auth:** Required

**Request**
```json
{
  "amount": 200,
  "note": "Send to TeleBirr 0911000001"
}
```

| Field | Rules |
|---|---|
| `amount` | required, number, min 1, max 1,000,000 |
| `note` | optional — payment method, account number, etc. |

**Response 201**
```json
{
  "message": "Withdrawal request submitted. Waiting for admin approval.",
  "request": {
    "id": 6,
    "type": "withdrawal",
    "type_label": "Withdrawal",
    "amount": "200.00",
    "status": "pending",
    "status_label": "Pending",
    "note": "Send to TeleBirr 0911000001",
    "admin_note": null,
    "reviewed_at": null,
    "created_at": "2026-08-09T11:00:00.000000Z"
  }
}
```

**Errors**
- `422 errors.amount` — "Insufficient available balance for this withdrawal."

---

### 11. My bets

Returns the user's bet history, newest first, 20 per page.

```
GET /bets
```

**Auth:** Required | **Query params:** `?page=2`

**Response 200**
```json
{
  "data": [
    {
      "id": 7,
      "event_title": "Abel vs Dawit",
      "city_name": "Gambella",
      "option_name": "Abel Wins",
      "stake": "100.00",
      "odds": "2.50",
      "potential_payout": "250.00",
      "status": "pending",
      "status_label": "Pending",
      "settled_at": null,
      "placed_at": "2026-08-09T10:00:00.000000Z"
    },
    {
      "id": 3,
      "event_title": "Tesfaye vs Girma",
      "city_name": "Gambella",
      "option_name": "Tesfaye Wins",
      "stake": "100.00",
      "odds": "1.80",
      "potential_payout": "180.00",
      "status": "won",
      "status_label": "Won",
      "settled_at": "2026-08-09T14:00:00.000000Z",
      "placed_at": "2026-08-07T10:00:00.000000Z"
    }
  ],
  "links": { "first": "...", "last": "...", "prev": null, "next": null },
  "meta": { "current_page": 1, "last_page": 1, "per_page": 20, "total": 2 }
}
```

---

### 12. Place a bet

```
POST /cities/{city}/events/{event}/bets
```

**Auth:** Required
**Route params:** `city` = city slug, `event` = event ID

**Request**
```json
{
  "betting_option_id": 13,
  "stake": 100
}
```

| Field | Rules |
|---|---|
| `betting_option_id` | required, integer, must belong to this city-event |
| `stake` | required, number, min 1, max 1,000,000 |

**Response 201**
```json
{
  "message": "Bet placed successfully.",
  "bet": {
    "id": 9,
    "event_title": "Abel vs Dawit",
    "city_name": "Gambella",
    "option_name": "Abel Wins",
    "stake": "100.00",
    "odds": "2.50",
    "potential_payout": "250.00",
    "status": "pending",
    "status_label": "Pending",
    "settled_at": null,
    "placed_at": "2026-08-09T10:05:00.000000Z"
  }
}
```

`potential_payout` = stake × odds, calculated and locked at placement time.

**Errors**

| Status | Field | Message |
|---|---|---|
| `403` | `city` | You can only place bets in your registered city. |
| `422` | `event` | Betting is only open for open events. |
| `422` | `event` | This event has already started. |
| `422` | `event` | This event has already been settled. |
| `422` | `betting_option_id` | Invalid betting option for this event. |
| `422` | `stake` | Insufficient available balance. |
| `404` | — | City or event not found / inactive |

---

## App flow

### Startup
1. Check for saved token in storage
2. If token → `GET /auth/me`
   - `200` → logged in, go to `GET /cities/{user.city.slug}`
   - `401` → expired, clear token, show login
3. No token → show login screen

### Registration
1. `GET /cities` → show city picker
2. User fills name, phone, password, picks city
3. `POST /auth/register`
4. Save token → `GET /cities/{user.city.slug}`

### Login
1. `POST /auth/login`
2. Save token → `GET /cities/{user.city.slug}`

### City homepage
- Call `GET /cities/{slug}`
- Show `city.hero_image` as hero banner if not null
- Render `city.banners[]` as promo cards; `link` is an optional tap target
- Render `events[]` as match cards:
  - Show `participants[0]` vs `participants[1]` with photos (fallback to initial letter if `image` is null)
  - Show `starts_at` formatted
  - Show `betting_options` as odds chips
- Tapping a card → event detail screen

### Event detail
- `GET /cities/{slug}/events/{event.id}`
- Show `banner` image at top (if not null)
- Show participants side-by-side with VS
- Show `betting_options` as selectable rows
- Check available balance from `GET /auth/me`:
  - `available_balance == "0.00"` → show "Your wallet is empty — request a deposit" button
  - Otherwise → show stake input with `max = available_balance`

### Place a bet
- `POST /cities/{slug}/events/{event.id}/bets`
- `201` → show success with `bet.potential_payout`
- `422 errors.stake` "Insufficient..." → prompt deposit
- `403` → "wrong city" message (shouldn't happen if app is correct)

### Wallet screen
- `GET /wallet`
- Show three balance figures: `balance`, `available_balance`, `pending_withdrawals`
- Deposit button → `POST /wallet/deposit`
- Withdraw button → `POST /wallet/withdraw`
- Transaction list: use `is_credit` to colour amounts green (credit) or red (debit); show `type_label` as the row title
- Request list: show `type_label` + `status_label` badge; `admin_note` shows the admin's reason when rejected

### My bets screen
- `GET /bets`
- Show each bet with `event_title`, `option_name`, `stake`, `odds`, `potential_payout`
- Use `status_label` for the badge; colour by status: pending=grey, won=green, lost=red, refunded=yellow, cancelled=grey

---

## Error handling

```js
switch (response.status) {
  case 200: case 201:
    // success
    break;
  case 401:
    // token expired — clear storage, redirect to login
    break;
  case 403:
    // show response.data.message to user
    break;
  case 404:
    // not found — show generic error or go back
    break;
  case 422:
    // show response.data.errors[field][0] next to each field
    break;
  case 429:
    // rate limited — "Too many attempts, please wait a minute"
    break;
  default:
    // server error — "Something went wrong, please try again"
}
```
