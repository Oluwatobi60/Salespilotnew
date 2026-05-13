-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 10:30 AM
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
-- Table structure for table `subscription_features`
--

  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL DEFAULT 'general',
  `role` varchar(255) NOT NULL DEFAULT '''free''',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_features`
--

INSERT INTO `subscription_features` (`id`, `name`, `slug`, `description`, `category`, `role`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Owner Dashboard', 'owner_dashboard', 'Complete business overview with analytics and metrics', 'Dashboard', 'business_creator', 1, 1, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(2, 'Manage Managers', 'manage_managers', 'Add, edit, and remove manager accounts', 'User Management', 'business_creator', 1, 2, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(3, 'Manage Staff', 'manage_staff', 'Add, edit, and remove staff accounts', 'User Management', 'business_creator', 1, 3, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(4, 'Multi-Branch Management', 'multi_branch', 'Create and manage multiple business locations', 'Branch', 'business_creator', 1, 4, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(5, 'Stock Transfer', 'stock_transfer', 'Transfer inventory between branches', 'Inventory', 'business_creator', 1, 5, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(6, 'Inventory Management', 'advanced_inventory', 'Full inventory control (add, edit, delete items)', 'Inventory', 'business_creator', 1, 6, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(7, 'Supplier Management', 'supplier_management', 'Manage supplier information and relationships', 'Suppliers', 'business_creator', 1, 7, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(8, 'Customer Management', 'customer_management', 'Manage customer database and information', 'CRM', 'business_creator', 1, 8, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(9, 'POS System', 'pos_system', 'Process sales and transactions', 'Sales', 'business_creator', 1, 9, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(11, 'Activity Logs', 'activity_logs', 'View all user activities and audit trail', 'Monitoring', 'business_creator', 1, 11, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(12, 'Discount Management', 'discounts_promotions', 'Create and manage discounts and promotions', 'CRM', 'business_creator', 1, 12, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(13, 'Financial Reports', 'profit_loss_reports', 'Profit & Loss and financial statements', 'Reports', 'business_creator', 1, 13, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(14, 'Data Export', 'export_data', 'Export business data to CSV/Excel', 'Reports', 'business_creator', 1, 14, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(15, 'System Preferences', 'system_preferences', 'Configure business settings and preferences', 'Settings', 'business_creator', 1, 15, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(16, 'Manager Dashboard', 'manager_dashboard', 'Manager-level dashboard with key metrics', 'Dashboard', 'manager', 1, 1, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(17, 'Manage Staff (Manager)', 'manager_manage_staff', 'Manager can add and manage staff members', 'User Management', 'manager', 1, 2, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(18, 'View Branches (Manager)', 'manager_view_branches', 'View assigned branch information', 'Branch', 'manager', 1, 3, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(19, 'Inventory Management (Manager)', 'manager_inventory', 'Manage inventory for assigned branch', 'Inventory', 'manager', 1, 4, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(20, 'Supplier Access (Manager)', 'manager_suppliers', 'View and manage suppliers', 'Suppliers', 'manager', 1, 5, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(21, 'Customer Access (Manager)', 'manager_customers', 'View and manage customers', 'CRM', 'manager', 1, 6, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(22, 'POS System (Manager)', 'manager_pos', 'Process sales transactions', 'Sales', 'manager', 1, 7, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(24, 'Activity Logs (Manager)', 'manager_activity_logs', 'View activity logs for assigned branch', 'Monitoring', 'manager', 1, 9, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(25, 'Apply Discounts (Manager)', 'manager_discounts', 'Apply approved discounts to sales', 'CRM', 'manager', 1, 10, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(26, 'Staff Dashboard', 'staff_dashboard', 'Basic dashboard for daily tasks', 'Dashboard', 'staff', 1, 1, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(27, 'POS System (Staff)', 'staff_pos', 'Process customer sales', 'Sales', 'staff', 1, 2, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(28, 'View Inventory (Staff)', 'staff_view_inventory', 'View stock levels and product information', 'Inventory', 'staff', 1, 3, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(30, 'Apply Discounts (Staff)', 'staff_discounts', 'Apply approved discounts during checkout', 'Sales', 'staff', 1, 5, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(31, 'Customer Lookup (Staff)', 'staff_customers', 'View customer information during sales', 'CRM', 'staff', 1, 6, '2026-04-29 16:56:45', '2026-04-29 16:56:45'),
(32, 'Sales Summary Report', 'sales_summary', 'View sales summary and overview', 'Reports', 'business_creator', 1, 16, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(33, 'Sales by Staff Report', 'sales_by_staff', 'View sales performance by staff members', 'Reports', 'business_creator', 1, 17, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(34, 'Sales by Item Report', 'sales_by_item', 'View sales breakdown by items', 'Reports', 'business_creator', 1, 18, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(35, 'Sales by Category Report', 'sales_by_category', 'View sales breakdown by categories', 'Reports', 'business_creator', 1, 19, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(36, 'Inventory Valuation Report', 'inventory_valuation', 'View inventory value and stock worth', 'Reports', 'business_creator', 1, 20, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(37, 'Discount Report', 'discount_report', 'View discount usage and impact', 'Reports', 'business_creator', 1, 21, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(38, 'Sales Summary (Manager)', 'manager_sales_summary', 'View branch sales summary', 'Reports', 'manager', 1, 11, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(39, 'Sales by Staff (Manager)', 'manager_sales_by_staff', 'View staff sales performance', 'Reports', 'manager', 1, 12, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(40, 'Sales by Item (Manager)', 'manager_sales_by_item', 'View item sales breakdown', 'Reports', 'manager', 1, 13, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(41, 'Sales by Category (Manager)', 'manager_sales_by_category', 'View category sales breakdown', 'Reports', 'manager', 1, 14, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(42, 'Inventory Valuation (Manager)', 'manager_inventory_valuation', 'View branch inventory value', 'Reports', 'manager', 1, 15, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(43, 'Discount Report (Manager)', 'manager_discount_report', 'View branch discount usage', 'Reports', 'manager', 1, 16, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(44, 'Sales Summary (Staff)', 'staff_sales_summary', 'View own sales summary', 'Reports', 'staff', 1, 7, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(45, 'Sales by Item (Staff)', 'staff_sales_by_item', 'View own item sales', 'Reports', 'staff', 1, 8, '2026-04-29 17:29:46', '2026-04-29 17:29:46'),
(46, 'Discount Report (Staff)', 'staff_discount_report', 'View own discount usage', 'Reports', 'staff', 1, 9, '2026-04-29 17:29:46', '2026-04-29 17:29:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `subscription_features`
--
ALTER TABLE `subscription_features`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_features_slug_unique` (`slug`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `subscription_features`
--
ALTER TABLE `subscription_features`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
