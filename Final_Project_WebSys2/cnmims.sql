-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2024 at 11:56 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cnmims`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `order_id`, `product_id`, `quantity`, `name`, `price`, `total`) VALUES
(1, 11, 1716098825, 6, 'Pogi Coffee', 31234.00, 187404.00),
(2, 10, 1, 3, 'Spanish Latteqqgfg', 150.00, 450.00),
(3, 10, 2, 4, 'Mango Smoothie', 5.00, 20.00),
(4, 10, 1716088715, 3, 'Choco Chips', 150.00, 450.00),
(5, 10, 1716088804, 5, 'Cookies and Cream ', 150.00, 750.00),
(6, 10, 1, 1, 'Spanish Latte', 150.00, 150.00),
(7, 10, 1716450985, 2, 'Langka Soda', 150.00, 300.00),
(8, 10, 1716544032, 3, 'Nespake', 20.00, 60.00),
(9, 10, 1716544032, 3, 'Nespake', 20.00, 60.00),
(10, 10, 1, 4, 'Spanish Latte', 150.00, 600.00),
(11, 10, 1, 3, 'Spanish Latte', 150.00, 450.00),
(12, 10, 1, 3, 'Spanish Latte', 150.00, 450.00),
(13, 12, 1716450985, 2, '', 0.00, 0.00),
(14, 13, 1716450986, 1, '', 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `totalBill` decimal(10,2) NOT NULL,
  `customerPayment` decimal(10,2) NOT NULL,
  `changeAmount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `totalBill`, `customerPayment`, `changeAmount`) VALUES
(10, 600.00, 1111.00, 511.00),
(11, 900.00, 1111.00, 211.00),
(12, 300.00, 333.00, 33.00),
(13, 123.00, 222.00, 99.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `retail_price` decimal(10,2) NOT NULL,
  `ideal_stock` int(11) NOT NULL,
  `available_stock` int(11) NOT NULL,
  `image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `purchase_price`, `retail_price`, `ideal_stock`, `available_stock`, `image`) VALUES
(1716450986, 'Macha', 'Coffee', 111.00, 123.00, 55, 54, 'images/port1.png'),
(1716450987, 'Blueberry', 'Frappe', 111.00, 123.00, 55, 55, 'images/port1.png'),
(1716450988, 'Strawberry', 'Creamery', 111.00, 123.00, 55, 55, 'images/'),
(1716450989, 'Lychee', 'Fruity Soda', 111.00, 123.00, 55, 55, 'images/port1.png');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `contact` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `contact`, `username`, `password`) VALUES
(3, 'ayaw ko na majinx', '696969696969', 'jinx', 'kissmuna'),
(4, 'trial', '0101010101', 'sanawalangerror', 'kissmuna'),
(5, 'user1111', '01112223344', 'basta', 'kissmuna'),
(6, 'subok ulit', '87000', 'joji27', 'kissmuna');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1716450990;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
