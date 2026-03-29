-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 09 فبراير 2026 الساعة 15:58
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `seragsoft_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-1EEBF2 من الطاف احمد', '127.0.0.1', '2026-01-27 17:09:53'),
(2, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-0CC517 من الطاف احمد يحيى', '127.0.0.1', '2026-01-27 17:14:08'),
(3, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-AAE439 من احمد', '127.0.0.1', '2026-01-27 17:23:06'),
(4, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-49432F من الطاف', '127.0.0.1', '2026-01-27 17:59:16'),
(5, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-6157FD من الطاف', '127.0.0.1', '2026-01-27 18:02:46'),
(6, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-EF1B7B من الطاف', '127.0.0.1', '2026-01-27 18:05:03'),
(7, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-CB84E9 من الطاف', '127.0.0.1', '2026-01-27 18:16:12'),
(8, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-F850A2 من لقلق', '127.0.0.1', '2026-01-27 18:34:39'),
(9, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-29DDF8 من شش', '127.0.0.1', '2026-01-27 18:37:22'),
(10, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-75F174 من aa', '127.0.0.1', '2026-01-27 18:43:03'),
(11, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-F51F80 من aa', '127.0.0.1', '2026-01-27 18:43:59'),
(12, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-0A74B4 من gg', '127.0.0.1', '2026-01-27 18:46:40'),
(13, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-58F04B من aa', '127.0.0.1', '2026-01-27 18:49:09'),
(14, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260127-893906 من aa', '127.0.0.1', '2026-01-27 18:51:52'),
(15, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260128-693459 من الطاف', '127.0.0.1', '2026-01-28 10:46:30'),
(16, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260128-C5B53F من hg', '127.0.0.1', '2026-01-28 10:47:24'),
(17, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260128-D1F6A2 من ajkjlskjdl', '127.0.0.1', '2026-01-28 10:48:13'),
(18, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260128-E240A2 من ghfhg', '127.0.0.1', '2026-01-28 17:20:14'),
(19, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260131-179E34 من hg', '127.0.0.1', '2026-01-31 08:52:01'),
(20, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260131-08EC91 من aa', '127.0.0.1', '2026-01-31 08:54:08'),
(21, 0, 'quote_request', 'طلب عرض سعر جديد: SRG-20260131-B04C1F من dd', '127.0.0.1', '2026-01-31 09:00:43'),
(22, 1, 'quote_request', 'طلب عرض سعر جديد: SRG-20260209-7DB1A7 من aa', '127.0.0.1', '2026-02-09 14:56:39');

-- --------------------------------------------------------

--
-- بنية الجدول `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active',
  `logo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `clients`
--

INSERT INTO `clients` (`id`, `name`, `description`, `location`, `display_order`, `status`, `logo`, `created_at`, `updated_at`) VALUES
(1, 'شركة التقنية المتطورة', '..', 'غير محدد', 1, 'active', 'client1.png', '2025-09-21 18:35:07', '2026-02-09 14:50:56'),
(2, 'مؤسسة اليمن للبرمجيات', '', 'غير محدد', 1, 'active', 'client2.png', '2025-09-21 18:35:07', '2026-02-05 17:40:00'),
(3, 'شركة النظم المتكاملة', NULL, NULL, 1, 'active', 'client3.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(4, 'مجموعة الحلول الذكية', NULL, NULL, 1, 'active', 'client4.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(5, 'شركة التميز التقني', NULL, NULL, 1, 'active', 'client5.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(6, 'مؤسسة الإبداع الرقمي', NULL, NULL, 1, 'active', 'client6.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(7, 'شركة المستقبل للتكنولوجيا.', '.', 'غير محدد', 1, 'active', 'client7.png', '2025-09-21 18:35:07', '2026-01-31 17:12:44'),
(8, 'مجموعة العملاء المميزين', NULL, NULL, 1, 'active', 'client8.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(9, 'شركة الريادة في التقنية', NULL, NULL, 1, 'active', 'client9.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(10, 'مؤسسة التطوير المتقدم', NULL, NULL, 1, 'active', 'client10.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(11, 'شركة الحلول الإلكترونية', NULL, NULL, 1, 'active', 'client11.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(12, 'مجموعة الابتكار التقني', NULL, NULL, 1, 'active', 'client12.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(13, 'شركة النجاح للأنظمة', NULL, NULL, 1, 'active', 'client13.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(14, 'مؤسسة التميز الرقمي', NULL, NULL, 1, 'active', 'client14.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(15, 'شركة التقدم التقني', NULL, NULL, 1, 'active', 'client15.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(16, 'مجموعة الحلول المتكاملة', NULL, NULL, 1, 'active', 'client16.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(17, 'شركة الإبداع التكنولوجي', NULL, NULL, 1, 'active', 'client17.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(18, 'مؤسسة التطور الرقمي', NULL, NULL, 1, 'active', 'client18.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(19, 'شركة الابتكار المتقدم', NULL, NULL, 1, 'active', 'client19.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(20, 'مجموعة النظم الذكية', NULL, NULL, 1, 'active', 'client20.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(21, 'شركة التميز التكنولوجي', NULL, NULL, 1, 'active', 'client21.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(22, 'مؤسسة الحلول المتطورة', NULL, NULL, 1, 'active', 'client22.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(23, 'شركة المستقبل الرقمي', NULL, NULL, 1, 'active', 'client23.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02'),
(24, 'مجموعة الإبداع التقني', NULL, NULL, 1, 'active', 'client24.png', '2025-09-21 18:35:07', '2026-01-25 13:33:02');

-- --------------------------------------------------------

--
-- بنية الجدول `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` enum('unread','read','replied','archived') DEFAULT 'unread',
  `updated_by` int(11) DEFAULT NULL,
  `reply_count` int(11) DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `message`, `create_at`, `status`, `updated_by`, `reply_count`, `updated_at`) VALUES
(14, 'altaf', 'altaf133@gmail.com', '77777777', 'aaaaa', '2026-02-09 14:36:32', 'unread', NULL, 0, '2026-02-09 14:36:32');

-- --------------------------------------------------------

--
-- بنية الجدول `message_replies`
--

CREATE TABLE `message_replies` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `replied_by` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `sent_via` enum('email','system','phone') DEFAULT 'email',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `quote_requests`
--

CREATE TABLE `quote_requests` (
  `id` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `service_type` varchar(100) DEFAULT NULL,
  `project_scope` varchar(100) DEFAULT NULL,
  `budget_range` varchar(100) DEFAULT NULL,
  `timeline` varchar(100) DEFAULT NULL,
  `features` text DEFAULT NULL,
  `project_description` text NOT NULL,
  `attachments` text DEFAULT NULL,
  `status` enum('pending','reviewed','quoted','accepted','rejected') DEFAULT 'pending',
  `assigned_to` int(11) DEFAULT NULL,
  `quoted_price` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `quote_requests`
--

INSERT INTO `quote_requests` (`id`, `reference`, `full_name`, `company_name`, `email`, `phone`, `service_type`, `project_scope`, `budget_range`, `timeline`, `features`, `project_description`, `attachments`, `status`, `assigned_to`, `quoted_price`, `notes`, `created_at`, `updated_at`) VALUES
(5, 'SRG-20260127-1EEBF2', 'الطاف احمد', 'سراج سوفت', 'aa@gmail.com', '+967 789798912', 'erp', 'small', '', 'normal', 'responsive-design, multi-language', 'hhjkhkjh', '[{\"original_name\":\"البرشور الامام لللمطاعم copy.jpg\",\"saved_name\":\"SRG-20260127-1EEBF2_1769533793_0.jpg\",\"path\":\"uploads\\/quote_attachments\\/SRG-20260127-1EEBF2_1769533793_0.jpg\"}]', 'pending', NULL, NULL, NULL, '2026-01-27 17:09:53', '2026-01-27 17:09:53'),
(6, 'SRG-20260127-0CC517', 'الطاف احمد يحيى', 'يمن سوفت', 'altaf@gmail.com', '+967 78798898', 'custom', 'medium', '0-5000', 'normal', 'responsive-design', 'hkjkfjxdj', '[{\"original_name\":\"البرشور الامام لللمطاعم copy.jpg\",\"saved_name\":\"SRG-20260127-0CC517_1769534048_0.jpg\",\"path\":\"uploads\\/quote_attachments\\/SRG-20260127-0CC517_1769534048_0.jpg\"}]', 'pending', NULL, NULL, NULL, '2026-01-27 17:14:08', '2026-01-27 17:14:08'),
(7, 'SRG-20260127-AAE439', 'احمد', 'سراج سوفت', 'altaf133@gmail.com', '+967 789798910', 'mobile', 'small', '0-5000', 'normal', 'responsive-design', 'jhjhj', '[]', 'pending', NULL, NULL, NULL, '2026-01-27 17:23:06', '2026-01-27 17:23:06'),
(8, 'SRG-20260127-49432F', 'الطاف', 'سراج سوفت', 'aa1@gmail.com', '+967 789798919', 'erp', 'medium', '5000-10000', 'urgent', 'responsive-design', 'hjhkhkjhk', '[]', 'pending', NULL, NULL, NULL, '2026-01-27 17:59:16', '2026-01-27 17:59:16'),
(9, 'SRG-20260127-6157FD', 'الطاف', 'سراج سوفت', 'aade1@gmail.com', '+967 789798912', 'erp', 'small', '5000-10000', 'normal', 'responsive-design', 'hjhjh', '[]', 'pending', NULL, NULL, NULL, '2026-01-27 18:02:46', '2026-01-27 18:02:46'),
(10, 'SRG-20260127-EF1B7B', 'الطاف', 'سراج سوفت', 'aa11@gmail.com', '+967 78798898', 'erp', 'small', '5000-10000', 'normal', 'responsive-design, admin-panel', 'jmnknk', '[]', 'quoted', NULL, NULL, '\n2026-02-09 16:36: تم\n2026-02-09 16:45: \r\n2026-02-09 16:36: تم', '2026-01-27 18:05:03', '2026-02-09 13:45:55'),
(26, 'SRG-20260209-7DB1A7', 'aa', 'aa', 'aaa@gmail.com', '+967 789798912', 'custom', 'large', '0-5000', 'flexible', 'responsive-design, multi-language, payment-gateway, admin-panel, api-integration, technical-support', 'hhh', '[]', 'pending', NULL, NULL, NULL, '2026-02-09 14:56:39', '2026-02-09 14:56:39');

-- --------------------------------------------------------

--
-- بنية الجدول `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `icon` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `services`
--

INSERT INTO `services` (`id`, `title`, `description`, `icon`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'أنظمة ERP', 'أنظمة مالية،موارد بشرية، إدارة مخازن متكاملة وشاملة لجميع احتياجات مؤسستك. صممت خصيصًا لتلبي متطلبات الأعمال بمختلف أحجامها..', 'fas fa-building', 1, '2025-09-21 19:02:42', '2026-02-09 14:51:17'),
(2, 'تطبيقات الجوال', 'تطبيقات iOS و Android عالية الأداء مع واجهات مستخدم جذابة وسهلة الاستخدام. نضمن تجربة مستخدم فريدة وسلسة.', 'fas fa-mobile-alt', 1, '2025-09-21 19:02:42', '2025-09-21 19:02:42'),
(3, 'تطوير الويب', 'مواقع إلكترونية ومتاجر تفاعلية بتقنيات حديثة وتصاميم متجاوبة. نبتكر حلولاً رقمية تعزز حضورك على الإنترنت.', 'fas fa-laptop-code', 1, '2025-09-21 19:02:42', '2025-09-21 19:02:42'),
(4, 'حلول مخصصة', 'برمجة خاصة وتطوير حلول فريدة لتلبية المتطلبات الخاصة بعملك. نقدم استشارات تقنية وحلولاً مبتكرة تناسب احتياجاتك.', 'fas fa-tools', 1, '2025-09-21 19:02:42', '2025-09-21 19:02:42');

-- --------------------------------------------------------

--
-- بنية الجدول `service_pricing`
--

CREATE TABLE `service_pricing` (
  `id` int(11) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) NOT NULL,
  `starting_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'ريال',
  `features` text DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `support_duration` varchar(100) DEFAULT NULL,
  `display_order` int(11) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `service_pricing`
--

INSERT INTO `service_pricing` (`id`, `service_type`, `title`, `description`, `icon`, `starting_price`, `currency`, `features`, `duration`, `support_duration`, `display_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'erp', 'أنظمة ERP', 'أنظمة مالية،موارد بشرية، إدارة مخازن متكاملة وشاملة لجميع احتياجات مؤسستك', 'fas fa-building', 50000.00, 'ريال', '[\"إدارة مالية ومحاسبية\",\"إدارة الموارد البشرية\",\"إدارة المخازن والمستودعات\",\"تقارير وتحليلات متقدمة\",\"دعم فني لمدة سنة\"]', '3-6 أشهر', '1 سنة', 1, 1, '2026-02-08 09:34:15', '2026-02-08 09:34:15'),
(2, 'mobile', 'تطبيقات الجوال', 'تطبيقات iOS و Android عالية الأداء مع واجهات مستخدم جذابة وسهلة الاستخدام', 'fas fa-mobile-alt', 25000.00, 'ريال', '[\"تطبيقات iOS و Android\",\"تصميم واجهة مستخدم جذابة\",\"تكامل مع واجهات برمجة API\",\"نشر في متاجر التطبيقات\",\"صيانة لمدة 6 أشهر\"]', '2-4 أشهر', '6 أشهر', 2, 1, '2026-02-08 09:34:15', '2026-02-08 09:34:15'),
(3, 'web', 'تطوير الويب', 'مواقع إلكترونية ومتاجر تفاعلية بتقنيات حديثة وتصاميم متجاوبة', 'fas fa-laptop-code', 15000.00, 'ريال', '[\"مواقع تفاعلية متجاوبة\",\"متاجر إلكترونية متكاملة\",\"لوحات تحكم متقدمة\",\"تحسين محركات البحث SEO\",\"استضافة وسنة صيانة\"]', '1-3 أشهر', '1 سنة', 3, 1, '2026-02-08 09:34:15', '2026-02-08 09:34:15'),
(4, 'custom', 'حلول مخصصة', 'برمجة خاصة وتطوير حلول فريدة لتلبية المتطلبات الخاصة بعملك', 'fas fa-tools', 0.00, 'ريال', '[\"تحليل وتخطيط المشروع\",\"تطوير حلول مخصصة\",\"اختبار وضمان الجودة\",\"تدريب المستخدمين\",\"دعم فني حسب الحاجة\"]', 'تختلف حسب المشروع', 'حسب العقد', 4, 1, '2026-02-08 09:34:15', '2026-02-08 09:34:15');

-- --------------------------------------------------------

--
-- بنية الجدول `sirajsoft_features`
--

CREATE TABLE `sirajsoft_features` (
  `id` int(11) NOT NULL,
  `icon` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `sirajsoft_features`
--

INSERT INTO `sirajsoft_features` (`id`, `icon`, `title`, `description`, `display_order`, `is_active`) VALUES
(1, 'fas fa-globe', 'خبرة موثوقة', 'فريق عمل يمتلك خبرة عملية تجمع بين المعايير العالمية وفهم عميق للسوق المحلي', 1, 1),
(2, 'fas fa-medal', 'جودة وابتكار', 'نستخدم أحدث التقنيات لضمان تقديم منتجات عالية الجودة ومواكبة للتطور', 2, 1),
(3, 'fas fa-headset', 'دعم متواصل', 'دعم فني متواصل وخدمة مميزة لما بعد البيع لضمان استقرار الأنظمة', 3, 1);

-- --------------------------------------------------------

--
-- بنية الجدول `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'سراج سوفت', '2025-10-25 09:31:54'),
(2, 'site_description', 'شركة رائدة في مجال البرمجيات وتطوير الويب وتطبيقات الجوال', '2025-10-25 09:31:54'),
(3, 'contact_email', 'info@sirajsoft.com', '2025-10-25 09:31:54'),
(4, 'contact_phone', '+967777777777', '2025-10-25 09:31:54'),
(5, 'contact_address', 'صنعاء، اليمن', '2025-10-25 09:31:54'),
(6, 'system_maintenance_mode', '0', '2026-01-24 18:39:16'),
(7, 'system_registration_enabled', '1', '2026-01-24 18:39:16'),
(8, 'system_default_language', 'en', '2026-01-24 18:39:16'),
(9, 'system_timezone', 'Asia/Aden', '2026-01-24 18:39:16'),
(10, 'system_date_format', 'd/m/Y', '2026-01-24 18:39:16'),
(11, 'system_items_per_page', '10', '2026-01-24 18:39:16'),
(12, 'system_admin_email', 'admin@sirajsoft.com', '2026-01-24 18:39:16'),
(13, 'system_smtp_host', 'mail.sirajsoft.com', '2026-01-24 18:39:16'),
(14, 'system_smtp_port', '587', '2026-01-24 18:39:16'),
(15, 'system_smtp_username', 'noreply@sirajsoft.com', '2026-01-24 18:39:16'),
(16, 'system_smtp_password', '', '2026-01-24 18:39:16'),
(17, 'facebook_url', '', '2026-01-28 17:55:38'),
(18, 'twitter_url', '', '2026-01-28 17:55:38'),
(19, 'linkedin_url', '', '2026-01-28 17:55:38'),
(20, 'instagram_url', '', '2026-01-28 17:55:38');

-- --------------------------------------------------------

--
-- بنية الجدول `slides`
--

CREATE TABLE `slides` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- بنية الجدول `statistics`
--

CREATE TABLE `statistics` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT 'red',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `statistics`
--

INSERT INTO `statistics` (`id`, `title`, `value`, `icon`, `color`, `status`, `created_at`, `updated_at`) VALUES
(1, 'سنوات من الخبرة', 19, 'fas fa-calendar-alt', 'red', 'active', '2025-09-21 06:13:06', '2026-02-09 14:51:29'),
(2, 'مشروع ناجح', 100, 'fas fa-check-circle', 'red', 'active', '2025-09-21 12:13:06', '2026-01-31 17:11:00'),
(3, 'عميل راضٍ', 90, 'fas fa-smile', 'red', 'active', '2025-09-21 12:13:06', '2025-10-02 15:51:01');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','editor','viewer') DEFAULT 'viewer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `is_active`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@seragsoft.com', 'admin', 1, '2025-09-21 11:38:58', '2026-02-09 12:42:45'),
(2, 'altaf', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'altaf@seragsoft.com', 'editor', 1, '2025-10-24 14:33:59', '2026-02-06 15:40:10');

-- --------------------------------------------------------

--
-- بنية الجدول `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `permissions` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_name`, `permissions`, `created_at`) VALUES
(1, 'admin', '{\"dashboard\":true,\"statistics\":true,\"services\":true,\"clients\":true,\"messages\":true,\"settings\":true,\"users\":true,\"system\":true}', '2025-10-01 06:57:50'),
(2, 'editor', '{\"dashboard\":true,\"statistics\":true,\"services\":true,\"clients\":true,\"messages\":true,\"settings\":false,\"users\":false,\"system\":false}', '2025-10-01 06:57:50'),
(3, 'viewer', '{\"dashboard\":true,\"statistics\":false,\"services\":false,\"clients\":false,\"messages\":false,\"settings\":false,\"users\":false,\"system\":false}', '2025-10-01 06:57:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `message_replies`
--
ALTER TABLE `message_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `message_id` (`message_id`),
  ADD KEY `replied_by` (`replied_by`);

--
-- Indexes for table `quote_requests`
--
ALTER TABLE `quote_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_pricing`
--
ALTER TABLE `service_pricing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_type` (`service_type`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indexes for table `sirajsoft_features`
--
ALTER TABLE `sirajsoft_features`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `slides`
--
ALTER TABLE `slides`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statistics`
--
ALTER TABLE `statistics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `message_replies`
--
ALTER TABLE `message_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quote_requests`
--
ALTER TABLE `quote_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `service_pricing`
--
ALTER TABLE `service_pricing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sirajsoft_features`
--
ALTER TABLE `sirajsoft_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `slides`
--
ALTER TABLE `slides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `statistics`
--
ALTER TABLE `statistics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- قيود الجداول المُلقاة.
--

--
-- قيود الجداول `message_replies`
--
ALTER TABLE `message_replies`
  ADD CONSTRAINT `message_replies_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `contacts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `message_replies_ibfk_2` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
