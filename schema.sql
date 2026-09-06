-- Database Schema for Game Item Store
CREATE DATABASE IF NOT EXISTS `game_store` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `game_store`;

-- Settings Table
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `site_name` VARCHAR(255) DEFAULT 'GameShop',
  `site_title` VARCHAR(255) DEFAULT 'ร้านค้าไอเทมเกมออนไลน์',
  `wallet_phone` VARCHAR(10) DEFAULT '0800000000',
  `contact_link` VARCHAR(255) DEFAULT 'https://facebook.com'
);

INSERT INTO `settings` (`id`, `site_name`, `site_title`, `wallet_phone`, `contact_link`)
VALUES (1, 'GameShop', 'ร้านค้าไอเทมเกมออนไลน์', '0800000000', 'https://facebook.com')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Users Table
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('User', 'Admin') DEFAULT 'User',
  `credit` DECIMAL(10,2) DEFAULT 0.00,
  `total_topup` DECIMAL(10,2) DEFAULT 0.00,
  `profile_img` VARCHAR(255) DEFAULT 'https://api.dicebear.com/7.x/bottts/svg?seed=User',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin (username: admin / password: password123)
INSERT INTO `users` (`username`, `password`, `role`, `credit`, `total_topup`, `profile_img`)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 1000.00, 1000.00, 'https://api.dicebear.com/7.x/bottts/svg?seed=Admin')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Sliders Table (1300x400 px)
CREATE TABLE IF NOT EXISTS `sliders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `image_url` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO `sliders` (`image_url`) VALUES
('https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1300&h=400&fit=crop'),
('https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1300&h=400&fit=crop');

-- Categories Table
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `is_recommended` TINYINT(1) DEFAULT 0
);

INSERT INTO `categories` (`name`, `is_recommended`) VALUES
('ไอดีเกมยอดนิยม', 1),
('โค้ดเติมเกม & บัตรเติมเงิน', 1),
('สินค้าทั่วไป', 0);

-- Products Table
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `category_id` INT,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `image_url` VARCHAR(255),
  `price` DECIMAL(10,2) NOT NULL,
  `is_recommended` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL
);

INSERT INTO `products` (`category_id`, `name`, `description`, `image_url`, `price`, `is_recommended`) VALUES
(1, 'ID Valorant สกินครบ', 'ไอดี Valorant แรร์ ยศ Immortal มีสกินครบถ้วน', 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?w=500&fit=crop', 450.00, 1),
(2, 'Steam Wallet $10 Code', 'โค้ดเติมเงิน Steam Wallet มูลค่า 10 USD', 'https://images.unsplash.com/photo-1612287230202-1ff1d85d1bdf?w=500&fit=crop', 350.00, 1);

-- Stocks Table
CREATE TABLE IF NOT EXISTS `stocks` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `product_id` INT,
  `data_content` TEXT NOT NULL,
  `is_sold` TINYINT(1) DEFAULT 0,
  `user_id` INT DEFAULT NULL,
  `sold_at` DATETIME DEFAULT NULL,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
);

INSERT INTO `stocks` (`product_id`, `data_content`, `is_sold`) VALUES
(1, 'User: valogod01 | Pass: secret1234', 0),
(1, 'User: valogod02 | Pass: secret5678', 0),
(2, 'STEAM-CODE-XXXX-YYYY-ZZZZ', 0);

-- Purchase Orders Table
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `item_code` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
