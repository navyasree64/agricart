# 🌱 Agricart E-commerce Platform

A comprehensive, responsive PHP-based e-commerce platform designed specifically for agricultural products, farming supplies, and guides. This platform provides a complete solution for online agricultural commerce with both customer-facing features and administrative capabilities.

## 🚀 Live Demo
You can view the live deployed project on InfinityFree here:  
👉 **[http://agricart.ifree.page](http://agricart.ifree.page)**

---

## ⚡ Key Updates & Features
1. **Centralized Administrative Interface**: Unified navigation across the admin panel (`dashboard.php`, `productadmin.php`, `users.php`, `categories.php`, `order_details.php`) using a dynamic template layout (`includes/admin_sidebar.php`).
2. **Enhanced User Analytics**: The **Manage Users** dashboard displays real-time customer data including **Phone Number**, **Total Orders**, and **Total Spent** (sum of completed orders).
3. **Robust Data Deletion**: Cascade database transactions clean up cart items, reviews, order items, status history, notification logs, and addresses securely when a user is deleted.
4. **Interactive Navigation**: Product tabs switch smoothly between **Description**, **Specifications**, and **Reviews** on `product.php` backed by the `getProductSpecifications` queries.

---

## 🚀 Key Modules & Capabilities

### Customer Experience
* **Product Catalog**: Browse agricultural products filtered by categories.
* **Shopping Cart**: Session-based cart with real-time quantity adjustments (available to authenticated customers).
* **Order Tracking**: Detailed tracking view with a visual progress bar.
* **User Accounts**: Login, registration, address management, and order history.
* **Farming Guides**: Section offering guides on seasonal crops and planting schedules.

### Admin Panel
* **Sales Dashboard**: Real-time sales statistics and site activity highlights.
* **Inventory Control**: Add, edit, and delete products, including image uploads.
* **Category Manager**: Organize inventory with hierarchical categories.
* **Order Processing**: Update delivery statuses and view order invoices.
* **User Accounts**: Oversee registered customers, view metrics, and manage user accounts.

---

## 🛠️ Tech Stack
* **Backend**: PHP 7.4+ / PHP 8.x
* **Database**: MySQL
* **Frontend**: HTML5, CSS3 (Vanilla), JavaScript
* **Deployment**: Optimized for standard Apache hosting / InfinityFree

---

## 💻 Running the Project Locally

To run this project on your local machine:

### Option A: Using PHP Built-in Server
1. Ensure PHP is installed on your computer.
2. Import the database file `db_export.sql` into your local MySQL server (using phpMyAdmin or command line).
3. Update database credentials in the local block of `config.php` (inside the `$isLocalhost` check):
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'agricart');
   ```
4. Start the PHP server from the project directory:
   ```bash
   php -S localhost:8000
   ```
5. Open `http://localhost:8000` in your web browser.

### Option B: Using XAMPP / WAMP
1. Move the project folder into your server's root folder (e.g., `C:/xampp/htdocs/agricart/`).
2. Start the Apache and MySQL modules in XAMPP.
3. Import `db_export.sql` into phpMyAdmin (`http://localhost/phpmyadmin`).
4. Access the site via `http://localhost/agricart/`.

---

## 🌐 Deploying to InfinityFree

Follow these steps to deploy the site to InfinityFree:

### Step 1: Create an InfinityFree Account
1. Register on [InfinityFree](https://infinityfree.com/) and create a new hosting account (e.g., generating a domain like `agricart.ifree.page`).
2. Go to the **MySQL Databases** section in your InfinityFree control panel and create a new database.
3. Keep the online credentials handy: **MySQL Hostname**, **MySQL Username**, **MySQL Password**, and **Database Name**.

### Step 2: Import the Database
1. Click on **Admin** (phpMyAdmin) next to your database in the InfinityFree Control Panel.
2. Go to the **Import** tab, upload the `db_export.sql` file from this repository, and click **Go** to seed the schema and initial data.

### Step 3: Upload Project Files
1. Open the online **File Manager** (or connect via FTP using FileZilla).
2. Navigate to the **`htdocs`** directory.
3. Upload all project files and directories (such as `assets`, `includes`, `home.php`, `config.php`, etc.) directly into `htdocs`. Do not upload the parent folder wrapper, only the files inside it.

### Step 4: Configure Database Connection
1. Edit the `config.php` file on your server.
2. Confirm the database credentials inside the production (`else`) block are filled in with your InfinityFree details:
   ```php
   define('DB_HOST', 'sql207.infinityfree.com');
   define('DB_USER', 'if0_42108185');
   define('DB_PASS', 'jpSlFNwdGePrqE');
   define('DB_NAME', 'if0_42108185_agricart');
   define('DB_PORT', 3306);
   ```

### Step 5: Initialize the Admin Account
1. Open your browser and go to `http://<your-subdomain>.ifree.page/setup_admin.php` (e.g., `http://agricart.ifree.page/setup_admin.php`).
2. Once the database seeds the admin credentials, **delete** `setup_admin.php` from your File Manager for security.

---

## 🔐 Default Admin Credentials
* **Admin Login URL**: `http://<your-subdomain>.ifree.page/adminlogin.php` (or `http://localhost:8000/adminlogin.php` locally)
* **Username**: `admin`
* **Password**: `admin123`

---

## 🗄️ Database Schema
The database contains the following tables:
* `users` - Customer account details
* `user_addresses` - Shipping addresses per user
* `products` - Product listings with details and image references
* `categories` - Product categories
* `orders` - Order records
* `order_items` - Association of products to specific orders
* `order_status_history` - Audit log for order state changes
* `notification_log` - System notifications
* `cart` - Saved cart products for active users
* `admins` - Credentials for administrative logins

---

## 🐛 Common Errors & Fixes

### ❌ `ERR_CONNECTION_RESET` or Domain Not Found
* **Cause**: DNS propagation is still in progress for your new account, or files were uploaded to the wrong folder.
* **Fix**: Ensure all files are uploaded directly inside the `htdocs` directory. If they are, wait 30–60 minutes for DNS propagation, or try visiting the page using a different network (e.g., mobile data).

### ❌ Database Connection Failed
* **Cause**: Incorrect database configuration in `config.php`.
* **Fix**: Double-check the MySQL host name, user, password, and database name from your InfinityFree client area and ensure they match the production block in `config.php` exactly.

### ❌ Broken Images
* **Cause**: Asset folders were not uploaded or put in the wrong place.
* **Fix**: Ensure the `assets/images/products/` and `assets/images/categories/` folders are fully uploaded and contain the product/category images.

---

## 👩‍💻 Author
**Navyasree Madala**  
GitHub: [@navyasree64](https://github.com/navyasree64)

**Happy Farming! 🌾**
