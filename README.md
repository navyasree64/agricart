# 🌱 Agricart E-commerce Platform

A comprehensive PHP-based e-commerce platform designed specifically for agricultural products and farming supplies. This platform provides a complete solution for online agricultural commerce with both customer-facing features and administrative capabilities.

## 🚀 Features

### Customer Features
- **Product Catalog**: Browse agricultural products by categories
- **Shopping Cart**: Add/remove products with quantity management
- **User Authentication**: Secure registration and login system
- **Order Management**: Place orders and track order status
- **Profile Management**: Update user information and addresses
- **Farming Guides**: Educational content for farmers
- **Responsive Design**: Mobile-friendly interface

### Admin Features
- **Dashboard**: Comprehensive admin panel with statistics
- **Product Management**: Add, edit, and delete products
- **Category Management**: Organize products by categories
- **Order Management**: Process and track customer orders
- **User Management**: Manage customer accounts
- **Inventory Control**: Monitor product stock levels

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Server**: Apache (XAMPP)
- **Architecture**: MVC Pattern

## 📋 Prerequisites

Before running this project, ensure you have:

- **XAMPP** (Apache, MySQL, PHP) installed
- **PHP 7.4** or higher
- **MySQL 5.7** or higher
- **Web browser** (Chrome, Firefox, Safari, Edge)

## 🔧 Installation

### Step 1: Clone the Repository
```bash
git clone https://github.com/Madhav8881/Agricart-Ecommerce-platform-.git
cd Agricart-Ecommerce-platform-
```

### Step 2: Setup XAMPP
1. Download and install [XAMPP](https://www.apachefriends.org/download.html)
2. Start Apache and MySQL services in XAMPP Control Panel
3. Copy the project folder to `C:\xampp\htdocs\` (Windows) or `/opt/lampp/htdocs/` (Linux/Mac)

### Step 3: Database Setup
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create a new database named `agricart_db`
3. Import the `setup_database.sql` file to create tables and sample data

### Step 4: Configuration
1. Update database credentials in `db.php`:
```php
$host = 'localhost';
$dbname = 'agricart_db';
$username = 'root';
$password = '';
```

### Step 5: Run the Application
1. Start XAMPP services (Apache & MySQL)
2. Open your browser and navigate to `http://localhost/webfinal`
3. The application should load successfully

## 📁 Project Structure

```
Agricart-Ecommerce-platform/
├── assets/
│   └── images/
│       ├── categories/     # Category images
│       └── products/      # Product images
├── includes/
│   ├── classes/           # PHP classes
│   ├── config/           # Configuration files
│   ├── functions.php     # Helper functions
│   └── helpers/          # Utility helpers
├── admin_footer.php      # Admin panel footer
├── admin_header.php      # Admin panel header
├── adminlogin.php        # Admin login page
├── cart.php             # Shopping cart
├── checkout.php         # Checkout process
├── dashboard.php        # Admin dashboard
├── db.php              # Database connection
├── functions.php       # Core functions
├── header.php          # Site header
├── footer.php          # Site footer
├── index.php           # Homepage
├── login.php           # User login
├── register.php        # User registration
├── product.php         # Product details
├── categories.php      # Category listing
└── README.md           # This file
```

## 🎯 Usage

### For Customers
1. **Browse Products**: Visit the homepage to see featured products
2. **Register/Login**: Create an account or login to existing account
3. **Add to Cart**: Click on products to add them to your cart
4. **Checkout**: Proceed to checkout and complete your order
5. **Track Orders**: View order status in your profile

### For Administrators
1. **Admin Login**: Access admin panel at `/adminlogin.php`
2. **Dashboard**: View sales statistics and recent orders
3. **Manage Products**: Add, edit, or remove products
4. **Process Orders**: Update order status and manage inventory
5. **User Management**: View and manage customer accounts

## 🔐 Default Admin Credentials

**Username**: `admin`  
**Password**: `admin123`

*⚠️ Important: Change these credentials after first login for security.*

## 🗄️ Database Schema

The application uses the following main tables:
- `users` - Customer information
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Order information
- `order_items` - Individual order items
- `cart` - Shopping cart items
- `addresses` - Customer addresses

## 🎨 Customization

### Adding New Products
1. Login to admin panel
2. Navigate to "Product Management"
3. Click "Add New Product"
4. Fill in product details and upload images
5. Save the product

### Modifying Categories
1. Access admin panel
2. Go to "Category Management"
3. Add, edit, or remove categories
4. Upload category images as needed

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error**
- Ensure MySQL is running in XAMPP
- Check database credentials in `db.php`
- Verify database exists in phpMyAdmin

**Images Not Loading**
- Check file permissions for `assets/images/` folder
- Ensure images are uploaded to correct directories
- Verify image file names match database entries

**Admin Panel Access Issues**
- Clear browser cache and cookies
- Check admin credentials
- Ensure `adminlogin.php` is accessible

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Madhav**  
GitHub: [@Madhav8881](https://github.com/Madhav8881)

## 🙏 Acknowledgments

- XAMPP Community for the development environment
- PHP Community for excellent documentation
- Bootstrap for responsive design components
- All contributors and testers

## 📞 Support

If you encounter any issues or have questions:

1. Check the [Issues](https://github.com/Madhav8881/Agricart-Ecommerce-platform-/issues) page
2. Create a new issue with detailed description
3. Contact the maintainer

---

**Happy Farming! 🌾**
