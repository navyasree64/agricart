# 🌱 Agricart E-commerce Platform

A responsive, feature-rich PHP e-commerce platform designed for agricultural products, farming supplies, and guides. The application features a fully responsive customer storefront and a comprehensive administration dashboard.

## 🚀 Live Demo
👉 **[http://agricart.ifree.page](http://agricart.ifree.page)**

---

## ✨ Features

### 🛒 Customer Storefront
* **Product Catalog**: Browse and search agricultural products by categories.
* **Shopping Cart**: Session-based cart with real-time quantity updates.
* **User Accounts**: Registration, login, shipping address management, and order history.
* **Order Tracking**: Order tracking dashboard with a visual status timeline.
* **Product Reviews**: Customer ratings and review sections.
* **Farming Guides**: Seasonal crop schedules and agricultural guides.

### 📊 Admin Dashboard
* **Sales Analytics**: Real-time sales metrics and site activity statistics.
* **Inventory Management**: Full CRUD operations for products (including image uploads) and categories.
* **Order Fulfillment**: View invoice details and update order delivery statuses.
* **User Management**: Monitor customer actions and safely delete user accounts with database cascade protection.

---

## 🛠️ Tech Stack
* **Backend**: PHP 7.4+ / PHP 8.x
* **Database**: MySQL
* **Frontend**: HTML5, CSS3, JavaScript
* **Server Support**: Compatible with XAMPP, WSL, Apache, or standard hosting (e.g. InfinityFree)

---

## 💻 Running the Project Locally

### Step 1: Database Setup
1. Import **`db_export.sql`** into your local MySQL server (via phpMyAdmin or command line).

### Step 2: Configuration
1. Copy **`config.php.example`** to a new file named **`config.php`**.
2. Configure your local database credentials in the local block of **`config.php`**:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'agri_ecommerce');
   ```

### Step 3: Run the Application
* **Using PHP server:**
  ```bash
  php -S localhost:8000
  ```
  Open `http://localhost:8000` in your browser.
* **Using XAMPP/WAMP:**
  Place the project folder in your `htdocs` directory and navigate to `http://localhost/agricart/`.

---

## 🔐 Default Admin Credentials
* **Admin Login URL**: `http://localhost:8000/adminlogin.php` (or your local equivalent)
* **Username**: `admin`
* **Password**: `admin123`

---

## 👩‍💻 Author
**Navyasree Madala**  
GitHub: [@navyasree64](https://github.com/navyasree64)

**Happy Farming! 🌾**
