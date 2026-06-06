-- AgriCart Database Export
-- Generated: 2026-06-05 19:09:42

SET FOREIGN_KEY_CHECKS=0;



CREATE TABLE `admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin` VALUES("1","admin","$2y$10$7Z8/EpxjU8.h4w/kI5DszuK4n/kP12h3JmH5K0y0X7n7Y47fN3S9C");


CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admins` VALUES("1","admin","admin123","2026-06-05 18:27:25","2026-05-23 11:29:11");


CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` VALUES("1","Seeds",NULL,"seeds.jpg");
INSERT INTO `categories` VALUES("2","Fertilizers",NULL,"fertilizers.jpg");
INSERT INTO `categories` VALUES("3","Tools",NULL,"tools.jpg");
INSERT INTO `categories` VALUES("4","Pesticides",NULL,"pesticides.jpg");


CREATE TABLE `notification_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notification_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `order_status_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `order_status_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(20) DEFAULT 'COD',
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `address_id` (`address_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`address_id`) REFERENCES `user_addresses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `review` text NOT NULL,
  `date_added` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `product_specifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `spec_name` varchar(255) NOT NULL,
  `spec_value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_specifications_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `product_specifications` VALUES("1","1","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("2","1","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("3","1","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("4","1","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("5","2","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("6","2","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("7","2","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("8","2","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("9","3","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("10","3","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("11","3","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("12","3","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("13","4","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("14","4","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("15","4","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("16","4","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("17","5","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("18","5","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("19","5","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("20","5","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("21","6","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("22","6","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("23","6","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("24","6","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("25","7","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("26","7","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("27","7","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("28","7","Shelf Life","10 - 15 Days");
INSERT INTO `product_specifications` VALUES("29","8","Origin","Local Organic Farm");
INSERT INTO `product_specifications` VALUES("30","8","Quality","Premium Grade-A");
INSERT INTO `product_specifications` VALUES("31","8","Packaging","Eco-friendly biodegradable bag");
INSERT INTO `product_specifications` VALUES("32","8","Shelf Life","10 - 15 Days");


CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `products` VALUES("1","Premium Wheat Seeds","High yield wheat seeds for general farming.","45.00",NULL,"1","150","wheat_seeds.jpg","2026-05-23 11:29:11");
INSERT INTO `products` VALUES("2","Organic Compost Fertilizer","100% natural organic compost fertilizer.","25.50",NULL,"2","200","compost.jpg","2026-05-23 11:29:11");
INSERT INTO `products` VALUES("3","Ergonomic Hand Trowel","Strong stainless steel trowel with comfortable handle.","12.99",NULL,"3","50","trowel.jpg","2026-05-23 11:29:11");
INSERT INTO `products` VALUES("4","Test Product","This is a test product","100.00",NULL,"1","10","test.jpg","2026-05-23 11:29:33");
INSERT INTO `products` VALUES("5","Monsoon Special Seeds Pack","Perfect for rainy season plantation.","999.00","699.00","1","100","season1.jpg","2026-06-05 17:28:21");
INSERT INTO `products` VALUES("6","Premium NPK Fertilizer","Balanced nutrition for all crops.","800.00","599.00","2","120","season2.jpg","2026-06-05 17:28:21");
INSERT INTO `products` VALUES("7","Organic Pest Control Spray","Chemical-free pest management solution.","1200.00","720.00","4","80","season3.jpg","2026-06-05 17:28:21");
INSERT INTO `products` VALUES("8","Solar Water Pump","Energy-efficient irrigation solution.","5000.00","4000.00","3","15","season4.jpg","2026-06-05 17:28:21");


CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES("1","Test User","test@example.com",NULL,"$2y$10$0ndTyH4UcFFhjvO.SgfkBuu2.hegaJlzryluk0VKeGXsIIWRt32K2","2026-06-05 14:32:47");

SET FOREIGN_KEY_CHECKS=1;
