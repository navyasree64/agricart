# 🌱 Agricart E-commerce Platform

A comprehensive PHP-based e-commerce platform designed specifically for agricultural products and farming supplies. This platform provides a complete solution for online agricultural commerce with both customer-facing features and administrative capabilities.

## 🚀 Live Demo
You can view the live deployed project on InfinityFree here:  
👉 **[http://agricart.ifree.page](http://agricart.ifree.page)**

---

## ⚡ Key Updates & Modifications
1. **Centralized Administrative Interface**: Unified navigation across the admin panel (`dashboard.php`, `productadmin.php`, `users.php`, `categories.php`, `order_details.php`) using a dynamic template layout (`includes/admin_sidebar.php`).
2. **Enhanced User Analytics**: The **Manage Users** dashboard now displays real-time customer data including **Phone Number**, **Total Orders**, and **Total Spent** (sum of completed orders).
3. **Robust Data Deletion**: Integrated a cascade database transaction for user deletion that cleans up cart items, reviews, order items, status history, notification logs, and addresses securely.
4. **Improved Product Detail Navigation**: Enabled smooth tab-switching for **Description**, **Specifications**, and **Reviews** on `product.php` backed by the `getProductSpecifications` queries.
5. **Database Export Tool**: Created `export_db.php` to easily export the MySQL database without needing phpMyAdmin or XAMPP locally.

---

## 🚀 Features

### Customer Features
- **Product Catalog**: Browse agricultural products by categories
- **Shopping Cart**: Secure session-based cart with quantity management (only available to logged-in users)
- **User Authentication**: Secure registration and login system
- **Order Management**: Place orders and track order status with real-time progress bars
- **Profile Management**: Manage saved addresses, phone number, and account details
- **Farming Guides**: Educational guides for seasonal agriculture

### Admin Features
- **Dashboard**: Panel showing sales statistics and recent activities
- **Product Management**: Add, edit, and delete products with custom image uploads
- **Category Management**: Edit, add, and delete categories with clean layouts
- **Order Management**: Process, update status, and view detailed invoice pages
- **User Management**: Monitor customer actions and delete users safely

---

## 🛠️ Technology Stack
- **Backend**: PHP 7.4+ / PHP 8.x
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Server Support**: WSL, Apache, or direct PHP built-in server

---

## 🌐 Easy Deployment on InfinityFree (Without XAMPP)

If you don't have XAMPP installed, you can deploy the site easily and quickly for free:

### Step 1: Export Your Database
1. Run the custom export script in your workspace terminal (e.g. WSL):
   ```bash
   php export_db.php
   ```
2. This creates `db_export.sql` inside your project directory automatically.

### Step 2: Create your InfinityFree Account & Database
1. Sign up on [InfinityFree](https://infinityfree.com/) and create a hosting account (e.g. `agricart.ifree.page`).
2. Go to **MySQL Databases** in the InfinityFree Control Panel.
3. Create a new database and copy the online credentials (**Host Name**, **User Name**, **Password**, and **Database Name**).

### Step 3: Import SQL and Upload Files
1. Open **phpMyAdmin** from your InfinityFree database panel, go to **Import**, upload `db_export.sql`, and run it.
2. Open the online **File Manager** (or connect via FileZilla FTP).
3. Enter the **`htdocs`** folder and upload all your project files directly inside it.

### Step 4: Configure the Database Connection
1. Edit the **`config.php`** file on your web server. The production block already has the correct InfinityFree credentials filled in:
   ```php
   define('DB_HOST', 'sql207.infinityfree.com');
   define('DB_USER', 'if0_42108185');
   define('DB_PASS', 'jpSlFNwdGePrqE');
   define('DB_NAME', 'if0_42108185_agricart');
   ```
   > The `config.php` auto-detects if it is running locally or on InfinityFree and uses the correct credentials automatically.

### Step 5: Seed the Admin User
1. Open your browser and navigate to:  
   `http://agricart.ifree.page/setup_admin.php`
2. Once you see the success message, delete `setup_admin.php` and `export_db.php` from your online File Manager for security.

---

## 🔐 Default Admin Credentials
- **Admin Login URL**: `http://agricart.ifree.page/adminlogin.php`
- **Username**: `admin`
- **Password**: `admin123`

---

## 🗄️ Database Schema
The database incorporates the following tables:
- `users` - Customer accounts
- `user_addresses` - Shipping addresses
- `products` - Product inventory
- `categories` - Category organizational layout
- `orders` - Order transaction records
- `order_items` - Specific products in each order
- `order_status_history` - Audit log for administrative updates
- `notification_log` - System notifications
- `cart` - Persistent user carts
- `admins` - Admin dashboard logins

---

## 🐛 Common Errors & Fixes

### ❌ `ERR_CONNECTION_RESET` on browser
**Cause**: Files are outside the `htdocs` folder OR DNS propagation is still in progress.  
**Fix**:
- Make sure all files are uploaded **inside** the `htdocs` folder in the File Manager.
- Wait 10–30 minutes for DNS propagation after account creation.
- Test on mobile data (not Wi-Fi) to bypass local DNS cache.

### ❌ Database Connection Failed / Blank Page
**Cause**: Wrong database credentials in `config.php`.  
**Fix**: Confirm these values match exactly what is shown in your InfinityFree MySQL Databases panel:
```
Host:     sql207.infinityfree.com
User:     if0_42108185
Database: if0_42108185_agricart
```

### ❌ Images Not Loading
**Cause**: Images uploaded to wrong sub-directory.  
**Fix**: Ensure product images are inside `htdocs/assets/images/products/` and category images are inside `htdocs/assets/images/categories/`.

### ❌ Admin Login Not Working
**Cause**: `setup_admin.php` was not run after deployment.  
**Fix**: Navigate to `http://agricart.ifree.page/setup_admin.php` once to seed the admin user into the database.

---

## 👩‍💻 Author
**Navyasree Madala**  
GitHub: [@navyasree64](https://github.com/navyasree64)

**Happy Farming! 🌾**
