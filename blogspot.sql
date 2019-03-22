-- phpMyAdmin SQL Dump
-- version 4.6.6deb5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 21, 2019 at 08:39 PM
-- Server version: 5.6.35-1+deb.sury.org~xenial+0.1
-- PHP Version: 5.6.37-1+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blogspot`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `companycode` varchar(15) NOT NULL,
  `companyname` varchar(150) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `contactname` char(45) DEFAULT NULL,
  `contactrole` char(25) DEFAULT NULL,
  `address` longtext,
  `businesstype` varchar(50) DEFAULT NULL,
  `businessline` char(30) DEFAULT NULL,
  `accountingyear` char(20) DEFAULT NULL,
  `currencycode` varchar(10) DEFAULT NULL,
  `postalcode` char(4) DEFAULT NULL,
  `tin` char(15) DEFAULT NULL,
  `phone` char(15) DEFAULT NULL,
  `fax` char(15) DEFAULT NULL,
  `mobile` char(15) DEFAULT NULL,
  `billingemail` varchar(50) DEFAULT NULL,
  `stat` enum('active','inactive') DEFAULT 'active',
  `companyimage` varchar(100) DEFAULT NULL,
  `enteredby` varchar(20) DEFAULT NULL,
  `entereddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updateby` varchar(20) DEFAULT NULL,
  `updatedate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateprogram` varchar(100) DEFAULT NULL,
  `rdo_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`companycode`, `companyname`, `email`, `contactname`, `contactrole`, `address`, `businesstype`, `businessline`, `accountingyear`, `currencycode`, `postalcode`, `tin`, `phone`, `fax`, `mobile`, `billingemail`, `stat`, `companyimage`, `enteredby`, `entereddate`, `updateby`, `updatedate`, `updateprogram`, `rdo_code`) VALUES
('CID', 'Cid Systems Solution Services', 'lumeng.lim@cid-systems.com', 'Lumeng Lim', 'Manager', 'U1410 Cityland Herrera Tower, 98 V. A. Rufino ST. Cor. Valero St. Salcedo Village, Makati City, Philippines', 'Computer Software /', '', 'calendar', 'PHP', '1700', '123-456-789-000', '+63 (2) 753.18.', '+63 (2) 751.08.', '091234567890', 'lumeng.lim@cid-systems.com', 'active', '9421222559.png', 'admin', '2017-08-30 06:41:37', 'cid_mark', '2017-08-30 06:41:37', 'Company|mod_view', '105');

-- --------------------------------------------------------

--
-- Table structure for table `content`
--

CREATE TABLE `content` (
  `id` int(11) NOT NULL,
  `companycode` varchar(15) NOT NULL,
  `title` varchar(250) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `tags` varchar(250) NOT NULL,
  `status` enum('published','unpublished') DEFAULT 'published',
  `enteredby` varchar(20) DEFAULT NULL,
  `entereddate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updateby` varchar(20) DEFAULT NULL,
  `updatedate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateprogram` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `content`
--

INSERT INTO `content` (`id`, `companycode`, `title`, `content`, `tags`, `status`, `enteredby`, `entereddate`, `updateby`, `updatedate`, `updateprogram`) VALUES
(1, 'CID', 'A Sample Content for my newly created Blog', '<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"http://localhost/blogspot/uploads/tinymce_uploads/1133604756-5c91f826d8107.jpeg\" alt=\"\" width=\"600\" height=\"419\" /></p>\r\n<p>MOA Arena</p>', 'sample,blogblogan', 'published', 'terens', '2019-03-22 02:16:00', 'terens', '2019-03-22 02:16:00', 'Content|mod_edit');

-- --------------------------------------------------------

--
-- Table structure for table `wc_admin_logs`
--

CREATE TABLE `wc_admin_logs` (
  `wc_admin_logsid` bigint(255) NOT NULL,
  `companycode` varchar(15) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `timestamps` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `activitydone` mediumtext,
  `ip_address` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `module` char(40) NOT NULL,
  `task` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `wc_admin_logs`
--

INSERT INTO `wc_admin_logs` (`wc_admin_logsid`, `companycode`, `username`, `timestamps`, `activitydone`, `ip_address`, `browser`, `module`, `task`) VALUES
(79, 'CID', 'superadmin', '2019-03-20 06:02:29', 'Login', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Login', 'login'),
(80, 'CID', 'superadmin', '2019-03-20 07:07:01', 'Create User []', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Users', 'mod_add'),
(81, 'CID', 'superadmin', '2019-03-20 07:08:29', 'Create User [terens]', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Users', 'mod_add'),
(82, 'CID', 'superadmin', '2019-03-20 07:09:52', 'Update User []', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Users', 'mod_edit'),
(83, 'CID', 'terens', '2019-03-20 07:35:25', 'Login', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Login', 'login'),
(84, 'CID', 'terens', '2019-03-20 07:43:16', 'Update User Group [superadmin]', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Users Group', 'mod_edit'),
(85, 'CID', 'terens', '2019-03-22 00:59:37', 'Login', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Login', 'login'),
(86, 'CID', 'terens', '2019-03-22 02:14:33', 'Delete Item Type [2]', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Content', 'mod_delete'),
(87, 'CID', 'terens', '2019-03-22 02:15:08', 'Delete Item Type [2]', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36', 'Content', 'mod_delete');

-- --------------------------------------------------------

--
-- Table structure for table `wc_modules`
--

CREATE TABLE `wc_modules` (
  `module_link` varchar(50) NOT NULL,
  `module_name` varchar(30) NOT NULL,
  `module_group` varchar(30) NOT NULL,
  `group_order` smallint(6) NOT NULL,
  `module_order` smallint(6) NOT NULL,
  `label` varchar(30) NOT NULL,
  `folder` varchar(30) NOT NULL,
  `file` varchar(30) NOT NULL,
  `default_function` varchar(30) NOT NULL,
  `show_nav` tinyint(1) UNSIGNED NOT NULL,
  `active` tinyint(1) UNSIGNED NOT NULL,
  `has_add` tinyint(1) NOT NULL,
  `has_view` tinyint(1) NOT NULL,
  `has_edit` tinyint(1) NOT NULL,
  `has_delete` tinyint(1) NOT NULL,
  `has_list` tinyint(1) NOT NULL,
  `has_print` tinyint(1) NOT NULL,
  `has_post` tinyint(1) NOT NULL,
  `has_unpost` tinyint(1) NOT NULL,
  `has_close` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wc_modules`
--

INSERT INTO `wc_modules` (`module_link`, `module_name`, `module_group`, `group_order`, `module_order`, `label`, `folder`, `file`, `default_function`, `show_nav`, `active`, `has_add`, `has_view`, `has_edit`, `has_delete`, `has_list`, `has_print`, `has_post`, `has_unpost`, `has_close`) VALUES
('content/%', 'Content', 'Content', 1000, 0, 'Content', 'content', 'content', 'listing', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0),
('maintenance/user/%', 'Users', 'Maintenance', 1000, 0, 'Maintenance', 'wc_core', 'user', 'listing', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0),
('maintenance/usergroup/%', 'Users Group', 'Maintenance', 1000, 0, 'Maintenance', 'wc_core', 'usergroup', 'listing', 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wc_module_access`
--

CREATE TABLE `wc_module_access` (
  `module_name` varchar(30) NOT NULL,
  `companycode` varchar(15) NOT NULL,
  `groupname` varchar(25) NOT NULL,
  `mod_add` tinyint(1) UNSIGNED NOT NULL,
  `mod_view` tinyint(1) UNSIGNED NOT NULL,
  `mod_edit` tinyint(1) UNSIGNED NOT NULL,
  `mod_delete` tinyint(1) UNSIGNED NOT NULL,
  `mod_list` tinyint(1) UNSIGNED NOT NULL,
  `mod_print` tinyint(1) UNSIGNED NOT NULL,
  `mod_post` tinyint(1) NOT NULL,
  `mod_unpost` tinyint(1) NOT NULL,
  `mod_close` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wc_module_access`
--

INSERT INTO `wc_module_access` (`module_name`, `companycode`, `groupname`, `mod_add`, `mod_view`, `mod_edit`, `mod_delete`, `mod_list`, `mod_print`, `mod_post`, `mod_unpost`, `mod_close`) VALUES
('Content', 'CID', 'superadmin', 1, 1, 1, 1, 1, 0, 0, 0, 0),
('Users', 'CID', 'superadmin', 1, 1, 1, 1, 1, 0, 0, 0, 0),
('Users Group', 'CID', 'superadmin', 1, 1, 1, 1, 1, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `wc_users`
--

CREATE TABLE `wc_users` (
  `username` varchar(20) NOT NULL,
  `companycode` varchar(15) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `stat` enum('active','inactive','deleted') DEFAULT 'active',
  `is_login` varchar(5) DEFAULT NULL,
  `useragent` varchar(255) DEFAULT NULL,
  `groupname` varchar(30) NOT NULL,
  `firstname` char(20) DEFAULT NULL,
  `middleinitial` char(2) DEFAULT NULL,
  `lastname` char(20) DEFAULT NULL,
  `initial` char(5) DEFAULT NULL,
  `phone` char(25) DEFAULT NULL,
  `mobile` char(20) DEFAULT NULL,
  `image` varchar(250) NOT NULL,
  `checktime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `locktime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enteredby` varchar(20) DEFAULT NULL,
  `entereddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateby` varchar(20) DEFAULT NULL,
  `updatedate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateprogram` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `wc_users`
--

INSERT INTO `wc_users` (`username`, `companycode`, `password`, `email`, `stat`, `is_login`, `useragent`, `groupname`, `firstname`, `middleinitial`, `lastname`, `initial`, `phone`, `mobile`, `image`, `checktime`, `locktime`, `enteredby`, `entereddate`, `updateby`, `updatedate`, `updateprogram`) VALUES
('superadmin', 'CID', '$2y$10$dNXFirki1ZRZM.BnakbehuPrEAik.iZinrfUg4XUwCWk58KV1nvtG', 'super@admin.com', 'active', 'no', '', 'superadmin', 'Super', '12', 'Admin', NULL, '123456', '123456', '', '2019-03-20 07:35:20', '2017-08-30 11:38:16', 'superadmin', '2017-06-15 18:11:11', 'jasmine.ang', '2017-08-30 11:33:16', 'Inventory Adjustment|mod_edit'),
('terens', 'CID', '$2y$10$BDFW9l2Itv4828AWNj6US.BQTOv7sJTyXnmN98duK7BEDT/M5qY2K', 'natividadter@gmail.com', 'active', '', '', 'superadmin', 'Terrence', 'T.', 'Natividad', NULL, '1234567', '1234567', '1ba0e70.jpg', '2019-03-22 02:59:47', '0000-00-00 00:00:00', 'superadmin', '2019-03-20 07:08:29', NULL, '0000-00-00 00:00:00', 'Users|mod_add');

-- --------------------------------------------------------

--
-- Table structure for table `wc_user_group`
--

CREATE TABLE `wc_user_group` (
  `groupname` varchar(30) NOT NULL,
  `companycode` varchar(15) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL,
  `enteredby` varchar(25) NOT NULL,
  `entereddate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updateby` varchar(25) NOT NULL,
  `updatedate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updateprogram` varchar(40) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `wc_user_group`
--

INSERT INTO `wc_user_group` (`groupname`, `companycode`, `description`, `status`, `enteredby`, `entereddate`, `updateby`, `updatedate`, `updateprogram`) VALUES
('superadmin', 'CID', 'test', 'active', 'superadmin', '2017-03-21 23:46:42', 'terens', '2019-03-20 07:43:16', 'Users Group|mod_edit');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`companycode`);

--
-- Indexes for table `content`
--
ALTER TABLE `content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wc_admin_logs`
--
ALTER TABLE `wc_admin_logs`
  ADD PRIMARY KEY (`wc_admin_logsid`,`companycode`),
  ADD KEY `companycode` (`companycode`),
  ADD KEY `username` (`username`) USING BTREE,
  ADD KEY `module` (`module`),
  ADD KEY `task` (`task`);

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
  ADD PRIMARY KEY (`module_name`,`companycode`,`groupname`),
  ADD KEY `groupname` (`groupname`,`companycode`),
  ADD KEY `wc_module_access_ibfk_2` (`companycode`,`groupname`);

--
-- Indexes for table `wc_users`
--
ALTER TABLE `wc_users`
  ADD PRIMARY KEY (`username`),
  ADD KEY `companycode` (`companycode`),
  ADD KEY `groupname` (`groupname`),
  ADD KEY `groupname_2` (`groupname`,`companycode`);

--
-- Indexes for table `wc_user_group`
--
ALTER TABLE `wc_user_group`
  ADD PRIMARY KEY (`groupname`,`companycode`),
  ADD KEY `companycode` (`companycode`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `content`
--
ALTER TABLE `content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `wc_admin_logs`
--
ALTER TABLE `wc_admin_logs`
  MODIFY `wc_admin_logsid` bigint(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `wc_module_access`
--
ALTER TABLE `wc_module_access`
  ADD CONSTRAINT `wc_module_access_ibfk_1` FOREIGN KEY (`module_name`) REFERENCES `wc_modules` (`module_name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wc_module_access_ibfk_2` FOREIGN KEY (`companycode`,`groupname`) REFERENCES `wc_user_group` (`companycode`, `groupname`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
