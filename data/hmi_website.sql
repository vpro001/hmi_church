-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 17, 2026 at 08:28 AM
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
-- Database: `hmi_website`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_type` enum('admin','customer') DEFAULT 'admin',
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT '',
  `ip_address` varchar(45) DEFAULT '',
  `user_agent` text DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `user_type`, `action`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'admin', 'login', 'Admin logged in', '127.0.0.1', 'curl/8.18.0', '2026-10-11 17:50:42'),
(2, 1, 'admin', 'login', 'Admin logged in', '127.0.0.1', 'curl/8.18.0', '2026-10-11 20:45:22'),
(3, 1, 'admin', 'login', 'Admin logged in', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-13 07:07:17'),
(4, 1, 'admin', 'login', 'Admin logged in', '::1', 'curl/8.18.0', '2026-10-13 07:43:04'),
(8, 2, 'customer', 'register', 'Customer registered', '', '', '2026-10-16 10:13:41'),
(9, 2, 'customer', 'login', 'Customer logged in', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 10:24:31'),
(10, 1, 'admin', 'login', 'Admin logged in', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 10:53:30'),
(11, 1, 'admin', 'event_update', 'Updated event: Marriage / Family Leadership Campaign', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 11:58:10'),
(12, 1, 'admin', 'event_update', 'Updated event: Marriage / Family Leadership Campaign', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 12:14:28'),
(13, 1, 'admin', 'event_update', 'Updated event: Marriage / Family Leadership Campaign', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 12:15:30'),
(14, 1, 'admin', 'event_update', 'Updated event: Marriage / Family Leadership Campaign', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 15:57:43'),
(15, 1, 'admin', 'event_update', 'Updated event: Singles & Married Meet', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-16 16:13:03'),
(16, 1, 'admin', 'livestream_end', 'Ended live stream: Healing My-Marriage: Weekly Family Impartation & Teaching', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-17 07:04:45'),
(17, 1, 'admin', 'livestream_update', 'Updated live stream: Healing My-Marriage: Weekly Family Impartation & Teaching', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-17 07:06:13'),
(18, 1, 'admin', 'livestream_start', 'Started live stream: Healing My-Marriage: Weekly Family Impartation & Teaching (youtube)', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-17 07:25:11'),
(19, 1, 'admin', 'livestream_end', 'Ended live stream: Healing My-Marriage: Weekly Family Impartation & Teaching', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', '2026-10-17 07:26:59');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('superadmin','admin','editor') DEFAULT 'admin',
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `email`, `password_hash`, `full_name`, `role`, `last_login`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@healingmymarriage.org', 'admin123', 'System Administrator', 'superadmin', '2026-10-16 10:53:30', 1, '2026-10-11 16:51:40', '2026-10-16 10:53:30');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Christian E-books', 'ebooks', 'Christian e-books on marriage, parenting and family life', 1, 1, '2026-10-11 16:51:40', '2026-10-11 16:51:40');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `summary` text DEFAULT '',
  `description` longtext DEFAULT '',
  `program_details` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT '',
  `program_image` varchar(255) DEFAULT '',
  `category` varchar(100) DEFAULT '',
  `location` varchar(200) DEFAULT '',
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `registration_required` tinyint(1) DEFAULT 0,
  `registration_link` varchar(255) DEFAULT '',
  `max_attendees` int(10) UNSIGNED DEFAULT NULL,
  `current_attendees` int(10) UNSIGNED DEFAULT 0,
  `status` enum('draft','published','cancelled','completed') DEFAULT 'draft',
  `meta_title` varchar(200) DEFAULT '',
  `meta_description` varchar(300) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `slug`, `title`, `summary`, `description`, `program_details`, `featured_image`, `program_image`, `category`, `location`, `start_datetime`, `end_datetime`, `registration_required`, `registration_link`, `max_attendees`, `current_attendees`, `status`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 'singles-married-meet', 'Singles & Married Meet', 'Join the teacher, Rev. Ogboo Akujuobi, PhD and other Ministers for a One Day meeting on Critical Marriage Evaluation.', 'Join the teacher, Rev. Ogboo Akujuobi, PhD and other Ministers for a One Day meeting on Critical Marriage Evaluation. A day of teaching, reflection and practical guidance for both singles and married individuals.', '09:00 AM - Arrival, Registration & Welcome\r\n09:30 AM - Opening Worship & Intercessory Prayer\r\n10:00 AM - Keynote Address: Critical Marriage Evaluation (Rev. Ogboo Akujuobi, PhD)\r\n11:30 AM - Breakout Sessions: Singles Preparedness vs. Married Restoration\r\n12:30 PM - Interactive Q&A with Panel of Ministers\r\n01:30 PM - Healing Prayers, Prophetic Blessings & Dedication\r\n02:00 PM - Fellowship, Refreshments & Free Pastoral Counseling', 'uploads/events/singles-married-meet_flyer_1792163583.jpg', '', 'Meeting', 'Mercyland Central / Diamond Estate, Rivers State', '2026-12-15 09:45:00', '2026-12-15 13:00:00', 0, '', NULL, 0, 'published', '', '', '2026-10-11 21:34:50', '2026-10-16 16:13:03'),
(2, 'marriage-family-leadership-campaign', 'Marriage / Family Leadership Campaign', 'Destiny People Global Ministry #2 Udo Essien Street, Eket. One Candle Lighting many others with Rev., Ogboo Akujuobi, PhD.', 'A Marriage / Family Leadership Campaign at Destiny People Global Ministry. One Candle Lighting many others with Rev. Ogboo Akujuobi, PhD. Equipping ministers and leaders for marriage and family ministry.', 'Day 1 (Wednesday):\r\n04:00 PM - Registration & Ministers Assembly\r\n04:30 PM - Praise, Thanksgiving & Opening Charge\r\n05:00 PM - Plenary 1: Equipping the Family Altar for Generational Impact\r\n06:30 PM - Ministering to Broken Marriages: Clinical & Biblical Tools\r\n07:30 PM - Evening Impartation & Closing Prayer\r\n\r\nDay 2 (Thursday):\r\n04:00 PM - Leadership Colloquium & Practical Case Studies\r\n05:30 PM - One Candle Lighting Many Others: Leadership Commissioning\r\n06:30 PM - Special Anointing Service for Couples & Family Leaders', 'uploads/events/marriage-family-leadership-campaign_flyer_1792162663.jpg', '', 'Campaign', 'Destiny People Global Ministry, Eket', '2027-01-20 16:00:00', '2027-01-21 18:00:00', 0, '', NULL, 0, 'published', '', '', '2026-10-11 21:34:50', '2026-10-16 15:57:43');

-- --------------------------------------------------------

--
-- Table structure for table `livestreams`
--

CREATE TABLE `livestreams` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT '',
  `platform` enum('youtube','facebook','both') NOT NULL DEFAULT 'both',
  `status` enum('live','scheduled','ended','offline') NOT NULL DEFAULT 'offline',
  `youtube_url` varchar(500) DEFAULT '',
  `youtube_video_id` varchar(100) DEFAULT '',
  `facebook_url` varchar(500) DEFAULT '',
  `banner_image` varchar(255) DEFAULT '',
  `scheduled_start` datetime DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `ended_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `livestreams`
--

INSERT INTO `livestreams` (`id`, `title`, `description`, `platform`, `status`, `youtube_url`, `youtube_video_id`, `facebook_url`, `banner_image`, `scheduled_start`, `started_at`, `ended_at`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Healing My-Marriage: Weekly Family Impartation & Teaching', 'Join Rev. Ogboo Akujuobi, PhD for an interactive biblical teaching on family harmony, conflict resolution in Christian homes, and healing prayers for couples.', 'youtube', 'ended', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'https://www.facebook.com/profile.php?id=61565801562860', '', '2026-10-18 22:19:00', '2026-10-17 07:25:11', '2026-10-17 07:26:59', 0, '2026-10-16 22:19:11', '2026-10-17 07:26:59');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `order_ref` varchar(30) NOT NULL,
  `status` enum('pending','paid','processing','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT '',
  `payment_reference` varchar(100) DEFAULT '',
  `subtotal` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `discount` int(10) UNSIGNED DEFAULT 0,
  `total` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `customer_address` text DEFAULT '',
  `notes` text DEFAULT '',
  `admin_notes` text DEFAULT '',
  `ip_address` varchar(45) DEFAULT '',
  `user_agent` text DEFAULT '',
  `paid_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_title` varchar(200) NOT NULL,
  `product_slug` varchar(150) NOT NULL,
  `price` int(10) UNSIGNED NOT NULL,
  `qty` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `subtotal` int(10) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` longtext DEFAULT '',
  `excerpt` text DEFAULT '',
  `meta_title` varchar(200) DEFAULT '',
  `meta_description` varchar(300) DEFAULT '',
  `featured_image` varchar(255) DEFAULT '',
  `template` varchar(50) DEFAULT 'default',
  `is_active` tinyint(1) DEFAULT 1,
  `show_in_nav` tinyint(1) DEFAULT 0,
  `nav_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `content`, `excerpt`, `meta_title`, `meta_description`, `featured_image`, `template`, `is_active`, `show_in_nav`, `nav_order`, `created_at`, `updated_at`) VALUES
(1, 'privacy', 'Privacy Policy', 'privacy', 'Our privacy policy and terms of use.', '', '', '', 'default', 1, 0, 0, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(2, 'about', 'About Us', 'about', 'Learn about our ministry, vision, mission, and leadership.', '', '', '', 'default', 1, 1, 10, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(3, 'contact', 'Contact Us', 'contact', 'Get in touch with us for counseling, inquiries, or prayer requests.', '', '', '', 'default', 1, 1, 20, '2026-10-11 16:51:52', '2026-10-11 16:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `excerpt` text DEFAULT '',
  `content` longtext DEFAULT '',
  `featured_image` varchar(255) DEFAULT '',
  `category` varchar(100) DEFAULT '',
  `author_id` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `published_at` datetime DEFAULT NULL,
  `meta_title` varchar(200) DEFAULT '',
  `meta_description` varchar(300) DEFAULT '',
  `view_count` int(10) UNSIGNED DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `slug`, `title`, `excerpt`, `content`, `featured_image`, `category`, `author_id`, `status`, `published_at`, `meta_title`, `meta_description`, `view_count`, `created_at`, `updated_at`) VALUES
(1, 'marriage-counseling', 'Marriage Counseling', 'Our Marriage Counseling service is dedicated to helping couples build stronger, healthier relationships. Whether you are newlyweds or have been together for years, our experienced counselors provide a safe, confidential space to work through challenges.', 'Our Marriage Counseling service is dedicated to helping couples build stronger, healthier relationships. Whether you are newlyweds or have been together for years, our experienced counselors provide a safe, confidential space to work through challenges. We offer personalized counseling sessions shaped around your own relationship and challenges. Our experienced counselors are dedicated to your relationship success. Utilizing effective techniques that foster understanding and connection. Rooted in Scripture, we apply biblical teaching to everyday decisions.', 'assets/img/live/marriage-counselling.jpg', 'Counseling', 1, 'published', '2026-09-04 10:00:00', '', '', 3, '2026-10-11 21:34:31', '2026-10-15 23:01:22'),
(2, 'teaching-and-biblical-guidance', 'Teaching and Biblical Guidance', 'My people are destroyed for lack of knowledge. - Hosea 4:6. At Healing My-Marriage International Ministry (HMI), our teaching is rooted in the full counsel of the Bible, focusing on marriage, family and relationships.', 'My people are destroyed for lack of knowledge. - Hosea 4:6. At Healing My-Marriage International Ministry (HMI), our teaching is rooted in the full counsel of the Bible, focusing on marriage, family and relationships. We provide clear understandable guidelines to initiate and maintain family through marriage. Our teaching helps couples see their marriage through the lens of God\'s Word and apply it to ordinary days: money, in-laws, children, work and rest.', 'assets/img/live/dfgdf.jpg', 'Teaching', 1, 'published', '2026-09-04 10:00:00', '', '', 2, '2026-10-11 21:34:31', '2026-10-15 23:17:18'),
(3, 'charitable-initiatives', 'Charitable Initiatives', 'Our Charitable Initiatives are an integral part of our commitment to serving others and making a positive impact in our community and beyond. We organize a variety of charity events and outreach programs.', 'Our Charitable Initiatives are an integral part of our commitment to serving others and making a positive impact in our community and beyond. We organize a variety of charity events and outreach programs. The ministry believes that strong families are a gift to a whole community. For that reason its work reaches beyond couples who walk through the door. Outreach and the Supporting Youths Initiative exist to encourage the next generation and to stand with families who need practical help and hope.', 'assets/img/live/jhj.jpg', 'Outreach', 1, 'published', '2026-09-04 10:00:00', '', '', 1, '2026-10-11 21:34:31', '2026-10-15 23:17:10'),
(4, 'prayer-meetings-asking-and-receiving', 'Prayer Meetings: Asking and Receiving', 'Our Prayer Meetings are a cornerstone of our spiritual community, providing a space for individuals to come together in faith, share their experiences, and lift their voices in prayer.', 'Our Prayer Meetings are a cornerstone of our spiritual community, providing a space for individuals to come together in faith, share their experiences, and lift their voices in prayer. Many couples can speak to God about almost anything, yet find it hard to pray out loud with each other. Praying together is one of the simplest ways to bring a marriage back to its foundation. Prayer meetings give couples and families a place to bring requests, to listen, and to be encouraged by others who are walking a similar road.', 'assets/img/live/prayer.jpg', 'Prayer', 1, 'published', '2026-09-04 10:00:00', '', '', 1, '2026-10-11 21:34:31', '2026-10-15 23:17:34'),
(5, 'spiritual-retreats', 'Spiritual Retreats', 'Our Spiritual Retreats offer a unique opportunity to step away from the hustle and bustle of everyday life and reconnect with your spiritual self. Set in serene locations, these retreats provide space for reflection and renewal.', 'Our Spiritual Retreats offer a unique opportunity to step away from the hustle and bustle of everyday life and reconnect with your spiritual self. Set in serene locations, these retreats provide space for reflection and renewal. Daily life leaves little room to reflect on the marriage itself. A retreat creates that room. Spiritual retreats gather couples for teaching, prayer, honest conversation and rest, away from the pressures that crowd ordinary routines.', 'assets/img/live/banner1.jpg', 'Events', 1, 'published', '2026-09-04 10:00:00', '', '', 1, '2026-10-11 21:34:31', '2026-10-15 23:16:58');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `slug` varchar(150) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT '',
  `short_description` text DEFAULT '',
  `price` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sale_price` int(10) UNSIGNED DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT '',
  `gallery_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery_images`)),
  `format` varchar(50) DEFAULT 'E-book (digital)',
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `meta_title` varchar(200) DEFAULT '',
  `meta_description` varchar(300) DEFAULT '',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `slug`, `title`, `description`, `short_description`, `price`, `sale_price`, `cover_image`, `gallery_images`, `format`, `is_featured`, `is_active`, `sort_order`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(1, 1, 'foundation-for-living', 'Foundation for Living', 'A foundational Christian e-book on marriage and family life from Healing My-Marriage International Ministry. This book provides essential biblical principles for building a strong, lasting marriage and family. Covers topics including God\'s design for marriage, communication, trust, and raising godly children.', 'A foundational Christian e-book on marriage and family life from Healing My-Marriage International Ministry. This book provides essential biblical principles for building a strong, lasting marriage an...', 13500, NULL, 'assets/img/live/book-foundation-for-living.jpg', NULL, 'E-book (digital)', 0, 1, 1, '', '', '2026-10-11 16:51:52', '2026-10-11 21:35:18'),
(2, 1, 'i-want-to-marry', 'I Want To Marry: WHY. HOW. WHAT.', 'I Want To Marry: WHY. HOW. WHAT. A Christian e-book guiding singles through the journey of preparing for marriage. Covers why to marry, how to choose a partner, and what to expect in marriage. Essential reading for anyone considering marriage.', 'I Want To Marry: WHY. HOW. WHAT. A Christian e-book guiding singles through the journey of preparing for marriage. Covers why to marry, how to choose a partner, and what to expect in marriage. Essenti...', 12000, NULL, 'assets/img/live/book-i-want-to-marry.jpg', NULL, 'E-book (digital)', 0, 1, 2, '', '', '2026-10-11 16:51:52', '2026-10-11 21:35:18'),
(3, 1, 'love-portion', 'Love Portion', 'Love Portion - A Christian e-book on understanding and nurturing love in marriage. Explores biblical love, intimacy, and how to keep the flame burning in your relationship. Practical guidance for couples at every stage.', 'Love Portion - A Christian e-book on understanding and nurturing love in marriage. Explores biblical love, intimacy, and how to keep the flame burning in your relationship. Practical guidance for coup...', 15000, NULL, 'assets/img/live/book-love-portion.jpg', NULL, 'E-book (digital)', 0, 1, 3, '', '', '2026-10-11 16:51:52', '2026-10-11 21:35:18'),
(4, 1, 'the-logics-of-parenting', 'The Logics of Parenting: A clinical approach', 'The Logics of Parenting: A clinical approach. A Christian e-book offering a clinical and biblical approach to parenting. Covers child development, discipline, spiritual nurture, and raising godly children in today\'s world.', 'The Logics of Parenting: A clinical approach. A Christian e-book offering a clinical and biblical approach to parenting. Covers child development, discipline, spiritual nurture, and raising godly chil...', 18000, NULL, 'assets/img/live/book-the-logics-of-parenting.gif', NULL, 'E-book (digital)', 0, 1, 4, '', '', '2026-10-11 16:51:52', '2026-10-11 21:35:18'),
(5, 1, 'the-understanding-of-marriage', 'The Understanding of Marriage', 'The Understanding of Marriage - A comprehensive Christian e-book on marriage from Healing My-Marriage International. Provides deep insights into God\'s design for marriage, roles, communication, conflict resolution, and building a marriage that honors God.', 'The Understanding of Marriage - A comprehensive Christian e-book on marriage from Healing My-Marriage International. Provides deep insights into God\'s design for marriage, roles, communication, confli...', 15000, NULL, 'assets/img/live/book-the-understanding-of-marriage.jpg', NULL, 'E-book (digital)', 0, 1, 5, '', '', '2026-10-11 16:51:52', '2026-10-11 21:35:18');

-- --------------------------------------------------------

--
-- Table structure for table `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` longtext DEFAULT NULL,
  `setting_type` enum('string','text','json','int','bool','image') DEFAULT 'string',
  `description` text DEFAULT '',
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Healing My-Marriage International Ministry, Inc.', 'string', 'Site name', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(2, 'site_short_name', 'Healing My-Marriage', 'string', 'Short site name', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(3, 'site_tagline', 'Reposition. Refix. Rekindle.', 'string', 'Site tagline', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(4, 'site_email', 'info@healingmymarriage.org', 'string', 'Contact email', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(5, 'site_phones', '[\"+234 704 222 6633\",\"+234 818 179 8886\"]', 'json', 'Phone numbers', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(6, 'site_phones_raw', '[\"+2347042226633\",\"+2348181798886\"]', 'json', 'Raw phone numbers', 0, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(7, 'site_address', 'Mercyland Central / Diamond Estate, Rivers State, Nigeria', 'string', 'Physical address', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(8, 'site_hours', 'Monday to Saturday, 8:00 am to 5:00 pm', 'string', 'Business hours', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(9, 'facebook_url', 'https://www.facebook.com/profile.php?id=61565801562860', 'string', 'Facebook URL', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(10, 'youtube_url', 'https://youtube.com/@healingmymarriageinternational', 'string', 'YouTube URL', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(11, 'ga_id', '', 'string', 'Google Analytics GA4 ID', 0, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(12, 'mail_enabled', '0', 'bool', 'Enable email notifications', 0, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(13, 'mail_to', 'info@healingmymarriage.org', 'string', 'Notification recipient', 0, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(14, 'mail_from', 'no-reply@healingmymarriage.org', 'string', 'From email address', 0, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(15, 'currency_symbol', '₦', 'string', 'Currency symbol', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52'),
(16, 'currency_code', 'NGN', 'string', 'Currency code', 1, '2026-10-11 16:51:52', '2026-10-11 16:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(10) UNSIGNED NOT NULL,
  `type` enum('contact','counseling','newsletter','order') NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT '',
  `subject` varchar(200) DEFAULT '',
  `message` text DEFAULT '',
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_hash` varchar(32) DEFAULT '',
  `is_read` tinyint(1) DEFAULT 0,
  `is_replied` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT '',
  `address` text DEFAULT '',
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(64) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `full_name`, `phone`, `address`, `is_verified`, `verification_token`, `reset_token`, `reset_token_expires`, `last_login`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'test@example.com', '$2y$12$U9L7ILLMFelV3uBHq/l.Geak6jP.tJG9rjOR3YrTJ7TvEu4ufQmLK', 'Test User', '', '', 0, 'eaa869623b1b8d881d892bc7db1bd27685b026d7eb976aac3a5f790ef080af9f', NULL, NULL, NULL, 1, '2026-10-11 17:51:30', '2026-10-11 17:51:30'),
(2, 'heath@example.com', '00000000', 'Orson Heath', '+1 (681) 611-1734', '493 Green Old Extension', 1, NULL, NULL, NULL, '2026-10-16 10:24:31', 1, '2026-10-16 10:13:41', '2026-10-16 10:24:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`,`user_type`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_start` (`start_datetime`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `livestreams`
--
ALTER TABLE `livestreams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_platform` (`platform`),
  ADD KEY `idx_scheduled` (`scheduled_start`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_ref` (`order_ref`),
  ADD KEY `idx_ref` (`order_ref`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_order` (`order_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_published` (`published_at`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `idx_slug` (`slug`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_category` (`category_id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_key` (`setting_key`),
  ADD KEY `idx_public` (`is_public`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_verification` (`verification_token`),
  ADD KEY `idx_reset` (`reset_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `livestreams`
--
ALTER TABLE `livestreams`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `admin_users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
