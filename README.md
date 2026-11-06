# MongoAdmin Backend

A modern, high-performance E-Commerce Backend and Administration Dashboard built with **Laravel** and **MongoDB**. This project provides a full-featured admin management panel, a robust versioned RESTful API (`v1`), interactive OpenAPI documentation, and containerized database infrastructure via Docker.

---

## Table of Contents

- [Overview & Architecture](#overview--architecture)
- [What's Inside](#whats-inside)
  - [1. Admin Management Panel](#1-admin-management-panel)
  - [2. RESTful API (`/api/v1`)](#2-restful-api-apiv1)
  - [3. Interactive API Documentation](#3-interactive-api-documentation)
  - [4. Data Models & MongoDB Collections](#4-data-models--mongodb-collections)
- [Prerequisites](#prerequisites)
- [Step-by-Step Setup & Run Manual](#step-by-step-setup--run-manual)
- [Default Credentials & Test Accounts](#default-credentials--test-accounts)
- [Docker MongoDB Management](#docker-mongodb-management)
- [Common Troubleshooting](#common-troubleshooting)

---

## Overview & Architecture

- **Backend Framework:** Laravel (PHP 8.3+)
- **Database Engine:** MongoDB 7.0 via `mongodb/laravel-mongodb` ODM
- **API Documentation:** Dedoc Scramble (OpenAPI / Swagger interactive docs)
- **Containerization:** Docker Compose for isolated MongoDB database management
- **Admin UI:** Responsive Blade views styled with Tailwind CSS

```
Backend_laravel/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/             # Admin Auth & Dashboard Controllers
│   │   │   └── Api/               # API Controllers (Products, Orders, etc.)
│   │   └── Middleware/            # Admin authorization middleware
│   └── Models/                    # MongoDB Document Models (Eloquent ODM)
├── config/                        # Laravel & MongoDB Configuration
├── database/
│   └── seeders/                   # Pre-built realistic dataset (DatabaseSeeder)
├── docker-compose.yml             # MongoDB 7.0 container specification
├── routes/
│   ├── api.php                    # REST API routes (/api/v1/...)
│   └── web.php                    # Admin panel & documentation routes
└── resources/views/admin/         # Blade templates for the Admin Panel
```

---

## What's Inside

### 1. Admin Management Panel
Accessible via web browser at `/admin`:
- **Dashboard (`/admin/dashboard`):** Real-time analytics, revenue, order totals, customer counts, and latest activity.
- **Product Management (`/admin/products`):** Full CRUD for catalog items, stock tracking, price management, categories, and dynamic custom document attributes (e.g., RAM, storage, dimensions).
- **Category Management (`/admin/categories`):** Organize catalog items with slugs, descriptions, icons, and status flags.
- **Order Management (`/admin/orders`):** Order tracking, order line items, status progression (`pending`, `paid`, `shipped`, `delivered`, `cancelled`), and coupon discounts.
- **User Management (`/admin/users`):** Manage registered customers and system administrators.
- **Coupon System (`/admin/coupons`):** Percentage and fixed discount codes, minimum spend thresholds, usage limits, and expiration tracking.
- **Review Moderation (`/admin/reviews`):** Product customer reviews and star rating management.

### 2. RESTful API (`/api/v1`)
A complete API designed for mobile and single-page applications:

| Endpoint | Method | Description |
| :--- | :--- | :--- |
| `/api/v1/auth/register` | `POST` | Register a new customer |
| `/api/v1/auth/login` | `POST` | Authenticate customer / admin & receive API token |
| `/api/v1/auth/me` | `GET` | Get authenticated user profile |
| `/api/v1/auth/logout` | `POST` | Invalidate current session/token |
| `/api/v1/products` | `GET` / `POST` | List products / Create a new product |
| `/api/v1/products/{id}` | `GET` / `PUT` / `DELETE` | View, update, or remove a product |
| `/api/v1/products/{id}/reviews` | `GET` / `POST` | Get product reviews / Submit a new review |
| `/api/v1/categories` | `GET` / `POST` | List / Create categories |
| `/api/v1/categories/{id}` | `GET` / `PUT` / `DELETE` | View, update, or remove a category |
| `/api/v1/orders` | `GET` / `POST` | List orders / Place a new order |
| `/api/v1/orders/{id}` | `GET` | View order breakdown and status |
| `/api/v1/orders/{id}/status` | `PATCH` | Update order processing status |
| `/api/v1/orders/{id}/cancel` | `POST` | Cancel an order |
| `/api/v1/coupons` | `GET` / `POST` | List / Create coupon codes |
| `/api/v1/coupons/apply` | `POST` | Validate and calculate coupon discount for a cart |
| `/api/v1/coupons/{id}` | `DELETE` | Delete a coupon |

### 3. Interactive API Documentation
Interactive OpenAPI documentation is built directly into the project:
- **URL:** `http://127.0.0.1:8000/docs` (or `/docs/api`)
- Test endpoints, inspect JSON schemas, parameters, and response structures directly in your browser.

### 4. Data Models & MongoDB Collections
Models extend `MongoDB\Laravel\Eloquent\Model`, leveraging document flexibility:
- **`User`**: Supports roles (`admin`, `customer`) and API token authentication.
- **`Product`**: Flexible `attributes` document array (e.g. key-value specs), category relationships, and stock metrics.
- **`Category`**: Catalog structure with active status and icon definitions.
- **`Order`**: Embedded `items` array storing historical prices and product snapshots at checkout.
- **`Coupon`**: Configurable discount rules (`percentage` or `fixed`).
- **`Review`**: Customer ratings (1-5) and feedback tied to products.

---

## Prerequisites

Before running the application, make sure you have the following installed:
- **PHP:** `^8.3` or higher (with extensions: `mongodb`, `curl`, `mbstring`, `xml`, `zip`)
- **Composer:** `^2.0`
- **Docker & Docker Compose:** To run the MongoDB 7 database container
- **Node.js & NPM:** (Node v18+) to compile frontend assets

---

## Step-by-Step Setup & Run Manual

### Step 1: Clone and Install Dependencies
Navigate into the project root and install PHP dependencies:
```bash
composer install
```

Install frontend assets:
```bash
npm install
npm run build
```

---

### Step 2: Configure Environment Variables
Create your local environment file:
```bash
cp .env.example .env
```

Generate your unique application encryption key:
```bash
php artisan key:generate
```

Verify your `.env` contains the MongoDB configuration:
```env
DB_CONNECTION=mongodb
DB_HOST=127.0.0.1
DB_PORT=27018
DB_DATABASE=backend_laravel_db
```
*(Note: Port `27018` is mapped to MongoDB port `27017` inside Docker)*

---

### Step 3: Start the MongoDB Container
Start the MongoDB database using Docker Compose:
```bash
docker compose up -d
```

Verify that the container is healthy and running:
```bash
docker compose ps
```
*Expected status: `laravel_backend_mongodb` with port mapping `0.0.0.0:27018->27017/tcp`.*

---

### Step 4: Seed the Database
Populate the database with sample products, categories, orders, coupons, and test users:
```bash
php artisan db:seed
```

---

### Step 5: Start the Laravel Development Server
Start the local server:
```bash
php artisan serve
```
*(Optional) If you want to run on a specific port such as `8003`:*
```bash
php artisan serve --port=8003
```

---

### Step 6: Access the Application

- **Admin Login:** [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login) *(or port `8003` if specified)*
- **Admin Dashboard:** [http://127.0.0.1:8000/admin/dashboard](http://127.0.0.1:8000/admin/dashboard)
- **API Documentation:** [http://127.0.0.1:8000/docs](http://127.0.0.1:8000/docs)
- **REST API Endpoint:** [http://127.0.0.1:8000/api/v1](http://127.0.0.1:8000/api/v1)

---

## Default Credentials & Test Accounts

The seeder (`DatabaseSeeder.php`) creates the following ready-to-use accounts:

### Administrator Account
- **Email:** `admin@example.com`
- **Password:** `password123`
- **Role:** `admin`
- **API Token:** `admin-secret-token-12345`

### Customer Account
- **Email:** `customer@example.com`
- **Password:** `password123`
- **Role:** `customer`
- **API Token:** `customer-secret-token-67890`

### Pre-configured Coupons
- `WELCOME10`: 10% discount on orders of \$50 or more.
- `SAVE25`: \$25 fixed discount on orders of \$100 or more.

---

## Docker MongoDB Management

The MongoDB instance is defined in [`docker-compose.yml`](file:///home/dens/ITE183/Backend_laravel/docker-compose.yml). Common commands:

```bash
# Start MongoDB in the background
docker compose up -d

# View container logs
docker compose logs -f mongodb

# Check running status
docker compose ps

# Stop MongoDB
docker compose stop

# Stop and remove containers (data persists in the mongo_data volume)
docker compose down

# Wipe all MongoDB data and start fresh
docker compose down -v
docker compose up -d
php artisan db:seed
```

---

## Common Troubleshooting

### 1. `ConnectionTimeoutException: [connection refused calling hello on '127.0.0.1:27018']`
- **Cause:** The MongoDB Docker container is not running.
- **Fix:** Run `docker compose up -d` and confirm the container is active with `docker compose ps`.

### 2. Port Conflict on `27018`
- If port `27018` is already used by another service, change the host port mapping in `docker-compose.yml` (e.g., `"27019:27017"`) and update `DB_PORT=27019` in your `.env`.

### 3. Missing Vite Manifest / Frontend Styles
- If CSS or layout elements look unstyled, build the frontend assets:
  ```bash
  npm run build
  ```
  Or run the Vite development server:
  ```bash
  npm run dev
  ```
