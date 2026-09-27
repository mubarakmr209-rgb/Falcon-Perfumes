-- Falcon Perfumes: clean sample database exported from MariaDB 10.11.
-- Import once into a fresh database. No customer/order/message test records.
-- Use phpMyAdmin Export after your local setup if required by your lecturer.

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `falcon_perfumes` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `falcon_perfumes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `admins` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `admins` VALUES
(1,'falcon_admin','$2y$10$zY6soxwAGXcWod5P0AybmObDb.4oveI9eycrVKd7U0qbQ8r1uqv02',1,'2026-09-27 11:27:53');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `brands` VALUES
(1,'Afnan'),
(8,'Ajmal'),
(3,'Armaf'),
(2,'Lattafa'),
(5,'Maison Alhambra'),
(9,'Paris Corner'),
(4,'Rasasi'),
(6,'Rayhaan'),
(7,'Riiffs');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `categories` VALUES
(1,'Men'),
(3,'Unisex'),
(2,'Women');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attempt_key` char(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_attempt_time` (`attempt_key`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `product_id` int(10) unsigned NOT NULL,
  `product_name` varchar(160) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `quantity` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `quantity_positive` CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `phone` varchar(25) NOT NULL,
  `address` varchar(500) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `shipping` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `status` enum('Pending','Confirmed','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(30) NOT NULL DEFAULT 'Cash on delivery',
  `checkout_token` char(64) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `checkout_token` (`checkout_token`),
  KEY `idx_customer_orders` (`user_id`,`created_at`),
  KEY `idx_order_status` (`status`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_gallery` (`product_id`,`sort_order`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `product_images` VALUES
(1,1,'images/products/9-pm-1.jpg',0),
(2,1,'images/products/9-pm-2.jpg',1),
(3,1,'images/products/9-pm-3.jpg',2),
(4,2,'images/products/khamrah-1.jpg',0),
(5,2,'images/products/khamrah-2.jpg',1),
(6,2,'images/products/khamrah-3.jpg',2),
(7,3,'images/products/cdn-1.jpg',0),
(8,3,'images/products/cdn-2.jpg',1),
(9,3,'images/products/cdn-3.jpg',2),
(10,4,'images/products/hawas-1.jpg',0),
(11,4,'images/products/hawas-2.jpg',1),
(12,4,'images/products/hawas-3.jpg',2),
(13,5,'images/products/supremacy-1.jpg',0),
(14,5,'images/products/supremacy-2.jpg',1),
(15,5,'images/products/supremacy-3.jpg',2),
(16,6,'images/products/asad-1.jpg',0),
(17,6,'images/products/asad-2.jpg',1),
(18,7,'images/products/jean-lowe-1.jpg',0),
(19,7,'images/products/jean-lowe-2.jpg',1),
(20,7,'images/products/jean-lowe-3.jpg',2),
(21,8,'images/products/yara-1.jpg',0),
(22,8,'images/products/yara-2.jpg',1),
(23,8,'images/products/yara-3.jpg',2);
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `name` varchar(160) NOT NULL,
  `concentration` varchar(40) NOT NULL DEFAULT 'Eau de Parfum',
  `size_ml` int(10) unsigned NOT NULL DEFAULT 100,
  `description` text NOT NULL,
  `top_notes` varchar(500) NOT NULL,
  `heart_notes` varchar(500) NOT NULL,
  `base_notes` varchar(500) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(10) unsigned NOT NULL DEFAULT 0,
  `is_sold_out` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `brand_id` (`brand_id`),
  KEY `category_id` (`category_id`),
  KEY `idx_catalog` (`is_active`,`category_id`,`brand_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `positive_price` CHECK (`price` > 0)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

INSERT INTO `products` VALUES
(1,1,1,'9 PM','Eau de Parfum',100,'A warm, sweet evening fragrance with crisp apple, aromatic lavandin and cinnamon. A floral heart settles into vanilla, amber and tonka bean.','Bergamot, lavandin, cinnamon, apple','Muguet, orange blossom','Patchouli, amber, vanilla, tonka bean',45.00,18,0,1,'2026-09-27 11:27:53'),
(2,2,3,'Khamrah','Eau de Parfum',100,'An aromatic spicy fragrance for women and men. Cinnamon and nutmeg meet a sweet heart of dates and praline, over a rich vanilla and resinous base.','Cinnamon, nutmeg, bergamot','Dates, praline, tuberose, mahonial','Vanilla, tonka bean, amberwood, myrrh, benzoin, akigalawood',40.00,14,0,1,'2026-09-27 11:27:53'),
(3,3,1,'Club de Nuit Intense Man','Eau de Toilette',105,'A woody-spicy fragrance with a bright citrus and fruit opening. Birch and flowers lead to a warm base of musk, patchouli and vanilla. This listing is the 105 ml Eau de Toilette.','Apple, bergamot, blackcurrant, pineapple, lemon','Rose, birch, jasmine','Musk, patchouli, vanilla',50.00,20,0,1,'2026-09-27 11:27:53'),
(4,4,1,'Hawas for Him','Eau de Parfum',100,'A fresh aquatic fragrance combining citrus, fruit and a touch of spice. Orange blossom and cardamom lead into a soft woody, musky dry-down.','Apple, bergamot, lemon, cinnamon','Orange blossom, cardamom, plum','Patchouli, grey amber, driftwood, musk',42.00,12,0,1,'2026-09-27 11:27:53'),
(5,1,1,'Supremacy Not Only Intense','Extrait de Parfum',100,'A bold, fruity and woody composition. Bergamot, apple and blackcurrant open the fragrance, with lavender, patchouli and oakmoss at its heart and a musky ambergris base.','Bergamot, apple, blackcurrant','Lavender, patchouli, oakmoss','Saffron, musk, ambergris',45.00,5,0,1,'2026-09-27 11:27:53'),
(6,2,1,'Asad','Eau de Parfum',100,'A warm amber fragrance for men. Pepper, pineapple and tobacco lead to coffee, iris and patchouli, finishing with vanilla, amber and dry woods.','Black pepper, tobacco, pineapple','Patchouli, coffee, iris','Vanilla, amber, dry wood, benzoin, labdanum',40.00,16,0,1,'2026-09-27 11:27:53'),
(7,5,1,'Jean Lowe Immortel','Eau de Parfum',100,'A fresh aromatic fragrance with an energetic grapefruit and ginger opening. Rosemary, sage and geranium add herbal character, followed by a warm amber and labdanum base.','Grapefruit, ginger, bergamot','Rosemary, sage, geranium','Ambroxan, amber, labdanum',30.00,8,0,1,'2026-09-27 11:27:53'),
(8,2,2,'Yara','Eau de Parfum',100,'A soft, creamy floral fragrance for women. Orchid and tangerine meet tropical fruits and a sweet gourmand accord, resting on vanilla, musk and sandalwood.','Orchid, heliotrope, tangerine','Gourmand accord, tropical fruits','Vanilla, musk, sandalwood',30.00,10,0,1,'2026-09-27 11:27:53');
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) NOT NULL,
  `email` varchar(190) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

