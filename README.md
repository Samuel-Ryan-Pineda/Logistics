# Logistics — Courier and Order Management

A lightweight full‑stack web application to manage customers, couriers, and orders. The frontend uses HTML, Bootstrap, and vanilla JavaScript; the backend is PHP with the official MongoDB PHP library. Data is stored in a MongoDB database named `logistics`.

## Features
- Add and list customers with phone validation and toast notifications (`html/customer.html`)
- Create orders for a selected customer; auto‑create customer if missing (`php/addorder.php`)
- View order details with customer and courier joins via aggregation (`php/getorders.php`)
- Add and list couriers with optional star rating (`html/courier.html`, `php/addcourier.php`)
- Assign multiple unassigned orders to a courier in one action (`php/assignorders.php`)
- Mark orders as delivered with an Asia/Manila timestamp (`php/markdelivered.php`)

## Tech Stack
- Frontend: HTML, Bootstrap 5, Font Awesome, vanilla JS
- Backend: PHP 8+, Composer
- Database: MongoDB (`mongodb://localhost:27017`)
- Dependencies: `mongodb/mongodb` PHP library (Composer)

## Prerequisites
- PHP 8.0+ with the built‑in web server (`php -S`)
- Composer
- MongoDB Server running locally on `localhost:27017`

## Setup
1. Ensure MongoDB is running locally.
2. From the `Logistics` directory, install PHP dependencies:
   ```bash
   composer install
   ```
3. Start a local PHP dev server with the project root as the document root:
   ```bash
   php -S localhost:8000 -t .
   ```
   Then open:
   - Customers: http://localhost:8000/html/customer.html
   - Couriers: http://localhost:8000/html/courier.html
   - Orders: http://localhost:8000/html/orderdetails.html

## Directory Structure
```
Logistics/
├─ html/            # Frontend pages
│  ├─ customer.html
│  ├─ courier.html
│  └─ orderdetails.html
├─ css/             # Styles
│  └─ styles.css
├─ php/             # Backend endpoints
│  ├─ addcustomer.php
│  ├─ addcourier.php
│  ├─ addorder.php
│  ├─ assignorders.php
│  ├─ getcustomers.php
│  ├─ getcouriers.php
│  ├─ getorders.php
│  ├─ getunassignedorders.php
│  └─ markdelivered.php
├─ composer.json
└─ vendor/          # Composer dependencies
```

## Data Model
- `customers`: `{ name, phone, address }`
- `couriers`: `{ courierName, phoneNumber, deliveryHub, rating? }`
- `orders`: `{ customerId, item, quantity, amount, weight, status, courierId?, dateDelivered? }`

## Key Implementation Notes
- Phone formatting for customers uses the `+63` prefix (`php/addcustomer.php:14`)
- Orders join customers and couriers via `$lookup` aggregation (`php/getorders.php:12`)
- Delivered date stored as `UTCDateTime` using Asia/Manila time (`php/markdelivered.php:27`)
- Unassigned orders are matched by missing `courierId` (`php/getunassignedorders.php:13`)
- Bulk assignment updates many orders in one call (`php/assignorders.php:31`)

## Troubleshooting
- Cannot mark delivered if no courier is assigned; the API will return an error.
- Ensure MongoDB is reachable on `localhost:27017`; otherwise API requests will fail.
- If you already have `vendor/`, still run `composer install` to ensure dependencies align.

## License
No explicit license provided.

