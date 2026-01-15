-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307:3307
-- Generation Time: Jan 15, 2026 at 02:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `xcelrent_car_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', 'password123', '2026-01-14 08:44:56');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','ongoing','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `car_id`, `user_id`, `start_date`, `end_date`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 1, '2026-01-15 08:00:00', '2026-01-16 08:00:00', 1799.00, 'ongoing', '2026-01-11 22:31:13'),
(2, 2, 2, '2026-01-20 10:00:00', '2026-01-21 10:00:00', 1799.00, 'pending', '2026-01-11 22:31:13'),
(3, 3, 3, '2026-01-25 09:00:00', '2026-01-26 09:00:00', 2299.00, 'confirmed', '2026-01-11 22:31:13');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `operator_id` int(11) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `plate_number` varchar(50) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `fuel_type` varchar(20) DEFAULT NULL,
  `seating` int(11) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `status` enum('live','hidden','maintenance') DEFAULT 'live',
  `created_at` datetime DEFAULT current_timestamp(),
  `driver_type` enum('self_drive','with_driver') DEFAULT 'self_drive',
  `tier1_12hrs` decimal(10,2) DEFAULT 0.00,
  `tier1_24hrs` decimal(10,2) DEFAULT 0.00,
  `tier2_12hrs` decimal(10,2) DEFAULT 0.00,
  `tier2_24hrs` decimal(10,2) DEFAULT 0.00,
  `tier3_24hrs` decimal(10,2) DEFAULT 0.00,
  `tier4_daily` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `operator_id`, `brand`, `model`, `plate_number`, `category`, `fuel_type`, `seating`, `location`, `status`, `created_at`, `driver_type`, `tier1_12hrs`, `tier1_24hrs`, `tier2_12hrs`, `tier2_24hrs`, `tier3_24hrs`, `tier4_daily`) VALUES
(1, 1, 'Toyota', 'Ativ', 'AAI-4681', 'Sedan', NULL, 4, 'Quezon City', 'live', '2026-01-11 22:29:11', 'self_drive', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(2, 1, 'Honda', 'City', 'ZLA-256', 'Sedan', NULL, 5, 'Manila', 'live', '2026-01-11 22:29:11', 'self_drive', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(3, 2, 'Toyota', 'Innova', 'DAK-1525', 'SUV', NULL, 7, 'Pangasinan', 'live', '2026-01-11 22:29:11', 'self_drive', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(4, 2, 'Isuzu', 'Sportivo X', 'AAN-4628', 'SUV', NULL, 9, 'Quezon City', 'maintenance', '2026-01-11 22:29:11', 'self_drive', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00),
(5, NULL, 'Porsche', '911 GT3 RS', 'AAN4623', '0', NULL, 4, 'Quezon City', 'live', '2026-01-12 11:51:11', 'self_drive', 0.00, 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `car_photos`
--

CREATE TABLE `car_photos` (
  `id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `doc_type` enum('Official Receipt (OR)','Certificate of Registration (CR)','NBI Clearance','Deed of Sale','Professional License','OR/CR') NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `rejection_reason` text DEFAULT NULL,
  `uploaded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `car_id`, `doc_type`, `file_path`, `verified`, `rejection_reason`, `uploaded_at`) VALUES
(1, NULL, 5, 'Official Receipt (OR)', 'uploads/cars/1768189871_2026JAN.jpg', 0, NULL, '2026-01-12 11:51:11'),
(2, NULL, NULL, 'Official Receipt (OR)', 'uploads/cars/1768437250_or_file_6.jpg', 0, NULL, '2026-01-15 08:34:10'),
(3, NULL, NULL, 'Certificate of Registration (CR)', 'uploads/cars/1768437250_cr_file_6.jpg', 0, NULL, '2026-01-15 08:34:10'),
(4, NULL, NULL, 'NBI Clearance', 'uploads/cars/1768437250_nbi_clearance_6.jpg', 0, NULL, '2026-01-15 08:34:10'),
(5, NULL, NULL, 'Deed of Sale', 'uploads/cars/1768437250_deed_of_sale_6.jpg', 0, NULL, '2026-01-15 08:34:10'),
(6, NULL, NULL, 'Professional License', 'uploads/cars/1768437250_pro_license_6.jpg', 0, NULL, '2026-01-15 08:34:10'),
(7, NULL, NULL, 'Official Receipt (OR)', 'uploads/cars/1768437999_or_file_7.jpg', 0, NULL, '2026-01-15 08:46:39'),
(8, NULL, NULL, 'Certificate of Registration (CR)', 'uploads/cars/1768437999_cr_file_7.jpg', 0, NULL, '2026-01-15 08:46:39'),
(9, NULL, NULL, 'NBI Clearance', 'uploads/cars/1768437999_nbi_clearance_7.jpg', 0, NULL, '2026-01-15 08:46:39'),
(10, NULL, NULL, 'Deed of Sale', 'uploads/cars/1768437999_deed_of_sale_7.jpg', 0, NULL, '2026-01-15 08:46:39'),
(11, NULL, NULL, 'Professional License', 'uploads/cars/1768437999_pro_license_7.jpg', 0, NULL, '2026-01-15 08:46:39'),
(12, NULL, NULL, 'Official Receipt (OR)', 'uploads/cars/1768438233_or_file_8.jpg', 0, NULL, '2026-01-15 08:50:33'),
(13, NULL, NULL, 'Certificate of Registration (CR)', 'uploads/cars/1768438233_cr_file_8.jpg', 0, NULL, '2026-01-15 08:50:33'),
(14, NULL, NULL, 'NBI Clearance', 'uploads/cars/1768438233_nbi_clearance_8.jpg', 0, NULL, '2026-01-15 08:50:33'),
(15, NULL, NULL, 'Deed of Sale', 'uploads/cars/1768438233_deed_of_sale_8.jpg', 0, NULL, '2026-01-15 08:50:33'),
(16, NULL, NULL, 'Professional License', 'uploads/cars/1768438233_pro_license_8.jpg', 0, NULL, '2026-01-15 08:50:33'),
(17, NULL, NULL, 'Official Receipt (OR)', 'uploads/cars/1768439199_or_file_9.jpg', 0, NULL, '2026-01-15 09:06:39'),
(18, NULL, NULL, 'Certificate of Registration (CR)', 'uploads/cars/1768439199_cr_file_9.jpg', 0, NULL, '2026-01-15 09:06:39'),
(19, NULL, NULL, 'NBI Clearance', 'uploads/cars/1768439199_nbi_clearance_9.jpg', 0, NULL, '2026-01-15 09:06:39'),
(20, NULL, NULL, 'Deed of Sale', 'uploads/cars/1768439199_deed_of_sale_9.jpg', 0, NULL, '2026-01-15 09:06:39'),
(21, NULL, NULL, 'Professional License', 'uploads/cars/1768439199_pro_license_9.jpg', 0, NULL, '2026-01-15 09:06:39');

-- --------------------------------------------------------

--
-- Table structure for table `operators`
--

CREATE TABLE `operators` (
  `id` int(11) NOT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `contact_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `operators`
--

INSERT INTO `operators` (`id`, `company_name`, `contact_name`, `email`, `phone`, `verified`, `created_at`) VALUES
(1, 'ABC Rentals', 'John Operator', 'abc@example.com', '09555555555', 1, '2026-01-11 22:25:04'),
(2, 'XYZ Motors', 'Jane Operator', 'xyz@example.com', '09666666666', 1, '2026-01-11 22:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) DEFAULT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_date` datetime DEFAULT current_timestamp(),
  `proof_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `amount`, `method`, `status`, `payment_date`, `proof_path`) VALUES
(1, 1, 1799.00, 'GCash', 'completed', '2026-01-11 22:31:47', NULL),
(2, 2, 1799.00, 'GCash', 'pending', '2026-01-11 22:31:47', NULL),
(3, 3, 2299.00, 'Bank Transfer', 'completed', '2026-01-11 22:31:47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('active','suspended') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `created_at`, `status`) VALUES
(1, 'Thristan', 'Garcia', 'thris@example.com', '09291489912', '2026-01-11 22:05:01', 'active'),
(2, 'Josef', 'Casangcapan', 'jj@example.com', '09987654321', '2026-01-11 22:05:01', 'active'),
(3, 'Forrest', 'Gump', 'forrest@example.com', '09111111111', '2026-01-11 22:05:01', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_booking_status` (`status`),
  ADD KEY `idx_booking_dates` (`start_date`,`end_date`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `operator_id` (`operator_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_location` (`location`);

--
-- Indexes for table `car_photos`
--
ALTER TABLE `car_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `car_id` (`car_id`);

--
-- Indexes for table `operators`
--
ALTER TABLE `operators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `idx_payment_date` (`payment_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cars`
--
ALTER TABLE `cars`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `car_photos`
--
ALTER TABLE `car_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `operators`
--
ALTER TABLE `operators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cars`
--
ALTER TABLE `cars`
  ADD CONSTRAINT `cars_ibfk_1` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `car_photos`
--
ALTER TABLE `car_photos`
  ADD CONSTRAINT `car_photos_ibfk_1` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `documents_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
