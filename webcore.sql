-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 02, 2017 at 03:36 PM
-- Server version: 5.6.35-1+deb.sury.org~xenial+0.1
-- PHP Version: 5.6.30-5+deb.sury.org~xenial+2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webcore`
--

-- --------------------------------------------------------

--
-- Table structure for table `wc_admin_logs`
--

CREATE TABLE `wc_admin_logs` (
  `caption` varchar(30) NOT NULL,
  `details` varchar(255) NOT NULL,
  `recorddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wc_modules`
--

CREATE TABLE `wc_modules` (
  `module_link` varchar(30) NOT NULL,
  `module_name` varchar(30) NOT NULL,
  `module_group` varchar(30) NOT NULL,
  `label` varchar(30) NOT NULL,
  `folder` varchar(30) NOT NULL,
  `file` varchar(30) NOT NULL,
  `default_function` varchar(30) NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wc_modules`
--

INSERT INTO `wc_modules` (`module_link`, `module_name`, `module_group`, `label`, `folder`, `file`, `default_function`, `active`) VALUES
('inventory/inquiry', 'Inventory Inquiry', 'Inventory', 'Modules', 'inventory', 'inventory_inquiry', 'listing', 1),
('inventory/issuance/%', 'Inventory Issuance', 'Inventory Maintenance', 'Modules', 'inventory', 'inventory_issuance', 'listing', 1),
('inventory/receipt/%', 'Inventory Receipt', 'Inventory Maintenance', 'Modules', 'inventory', 'inventory_receipt', 'listing', 1),
('inventory/tracking', 'Inventory Tracking', 'Inventory', 'Modules', 'inventory', 'inventory_tracking', 'listing', 1),
('item/%', 'Item Details', 'Inventory Maintenance', 'Modules', 'inventory', 'item', 'listing', 1),
('user/usergroup', 'User Group', 'Users', 'Maintenance', 'wc_core', 'usergroup', 'listing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wc_module_access`
--

CREATE TABLE `wc_module_access` (
  `module_name` varchar(30) NOT NULL,
  `usergroup_id` tinyint(3) UNSIGNED NOT NULL,
  `mod_add` tinyint(1) UNSIGNED NOT NULL,
  `mod_view` tinyint(1) UNSIGNED NOT NULL,
  `mod_edit` tinyint(1) UNSIGNED NOT NULL,
  `mod_delete` tinyint(1) UNSIGNED NOT NULL,
  `mod_list` tinyint(1) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wc_option`
--

CREATE TABLE `wc_option` (
  `code` varchar(10) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wc_reference`
--

CREATE TABLE `wc_reference` (
  `code` varchar(10) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wc_sequence_control`
--

CREATE TABLE `wc_sequence_control` (
  `code` varchar(10) NOT NULL,
  `start` int(10) UNSIGNED NOT NULL,
  `max` int(10) UNSIGNED NOT NULL,
  `prefix` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wc_users`
--

CREATE TABLE `wc_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `group_id` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `wc_user_group`
--

CREATE TABLE `wc_user_group` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `caption` varchar(30) NOT NULL,
  `details` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wc_modules`
--
ALTER TABLE `wc_modules`
  ADD PRIMARY KEY (`module_link`),
  ADD UNIQUE KEY `module_name` (`module_name`);

--
-- Indexes for table `wc_module_access`
--
ALTER TABLE `wc_module_access`
  ADD PRIMARY KEY (`module_name`,`usergroup_id`);

--
-- Indexes for table `wc_option`
--
ALTER TABLE `wc_option`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `wc_reference`
--
ALTER TABLE `wc_reference`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `wc_sequence_control`
--
ALTER TABLE `wc_sequence_control`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `wc_users`
--
ALTER TABLE `wc_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wc_user_group`
--
ALTER TABLE `wc_user_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `caption` (`caption`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wc_users`
--
ALTER TABLE `wc_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `wc_user_group`
--
ALTER TABLE `wc_user_group`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
