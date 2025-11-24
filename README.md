# Laravel E-Commerce Project

A fully functional **Laravel-based e-commerce platform** for managing products, shopping carts, wishlists, and coupon codes. Built using **Laravel 10**, **Blade templates**, and **ShoppingCart package**.

---

## Table of Contents
- [Features](#features)
- [Technologies Used](#technologies-used)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [Usage](#usage)
- [Screenshots](#screenshots)
- [Folder Structure](#folder-structure)
- [Contributing](#contributing)
- [License](#license)

---

## Features
- Product catalog with sorting and filtering by brand
- Shopping cart with quantity management
- Wishlist functionality for favorites
- Coupon codes for discounts (fixed and percentage-based)
- Dynamic calculation of cart totals, tax, and discounts
- User-friendly front-end using Blade templates
- Responsive design for desktop and mobile
- Optional analytics charts with ApexCharts.js
- Share buttons for products

---

## Technologies Used
- **Laravel 10**
- **PHP 8.x**
- **MySQL / MariaDB**
- **Blade Templates**
- **Bootstrap 5**
- **JavaScript / jQuery**
- **ApexCharts.js** (for analytics/charting)
- **Surfsidemedia ShoppingCart package** (for cart and wishlist)
- **Carbon** (for date handling)

---

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/laravel-ecommerce.git
cd laravel-ecommerce

2. Install PHP dependencies via Composer:
composer install

3. Install NPM dependencies (for frontend assets):
npm install
npm run dev

**## Configuration**
Copy .env.example to .env:
cp .env.example .env

## Update database and other environment variables in .env:
APP_NAME=LaravelEcommerce
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce_db
DB_USERNAME=root
DB_PASSWORD=

##Generate application key:
php artisan key:generate

##Database Setup
Run migrations to create tables:
php artisan migrate

##Usage
Start the development server:
php artisan serve


##Access the app in your browser:
http://127.0.0.1:8000


Features available:
Add products to cart
Apply coupon codes (fixed and percentage)
Add/remove products from wishlist
View cart totals including tax and discounts
Manage products via admin dashboard
Apply and remove coupons dynamically

License

This project is licensed under the MIT License.

Author: Paschal
Email: paschalnnamani10@gmail.com

GitHub: paschalkachi
