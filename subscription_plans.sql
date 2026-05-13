-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 11:13 AM
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
-- Database: `salespilot`
--

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `monthly_price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `max_managers` int(11) NOT NULL DEFAULT 1,
  `max_staff` int(11) DEFAULT NULL,
  `max_branches` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `trial_days` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `name`, `monthly_price`, `description`, `features`, `max_managers`, `max_staff`, `max_branches`, `is_active`, `is_popular`, `trial_days`, `created_at`, `updated_at`) VALUES
(1, 'free', 0.00, 'Free 7-day trial to test all features', '[]', 1, 1, 1, 1, 0, 7, '2026-02-03 20:20:26', '2026-04-29 15:28:27'),
(2, 'basic', 5000.00, 'Perfect for small businesses', '[]', 1, 2, 1, 1, 1, 0, '2026-02-03 20:20:27', '2026-04-29 15:28:27'),
(3, 'standard', 10000.00, 'Ideal for growing businesses', '[\"multi_branch\",\"customer_management\",\"activity_logs\",\"supplier_management\",\"advanced_inventory\",\"discounts_promotions\",\"manage_managers\",\"manage_staff\",\"system_preferences\",\"stock_transfer\",\"pos_system\",\"owner_dashboard\",\"profit_loss_reports\",\"manager_pos\",\"manager_customers\",\"manager_suppliers\",\"manager_discounts\",\"manager_dashboard\",\"manager_activity_logs\",\"manager_sales_summary\",\"manager_sales_by_staff\",\"manager_sales_by_item\",\"manager_inventory\",\"manager_sales_by_category\",\"manager_inventory_valuation\",\"manager_discount_report\",\"staff_dashboard\",\"staff_sales_summary\",\"staff_customers\",\"staff_sales_by_item\",\"staff_discount_report\",\"staff_view_inventory\",\"staff_discounts\",\"staff_pos\"]', 2, 4, 2, 1, 1, 0, '2026-02-03 20:20:27', '2026-04-29 18:23:42'),
(4, 'premium', 20000.00, 'Complete solution for large businesses', '[\"manager_dashboard\",\"manager_manage_staff\",\"manager_customers\",\"manager_suppliers\",\"manager_inventory\",\"manager_view_branches\",\"manager_pos\",\"manager_activity_logs\",\"manager_discounts\",\"manager_sales_summary\",\"manager_sales_by_staff\",\"manager_sales_by_item\",\"manager_sales_by_category\",\"manager_inventory_valuation\",\"manager_discount_report\",\"owner_dashboard\",\"manage_managers\",\"manage_staff\",\"multi_branch\",\"stock_transfer\",\"advanced_inventory\",\"supplier_management\",\"customer_management\",\"pos_system\",\"activity_logs\",\"discounts_promotions\",\"profit_loss_reports\",\"export_data\",\"system_preferences\",\"sales_summary\",\"sales_by_staff\",\"sales_by_item\",\"sales_by_category\",\"inventory_valuation\",\"discount_report\",\"staff_dashboard\",\"staff_pos\",\"staff_discounts\",\"staff_customers\",\"staff_view_inventory\",\"staff_sales_summary\",\"staff_sales_by_item\",\"staff_discount_report\"]', 3, NULL, NULL, 1, 0, 0, '2026-02-03 20:20:27', '2026-04-29 17:41:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
