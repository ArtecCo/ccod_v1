-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 10, 2026 at 08:42 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cloud_ops_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_requests`
--

CREATE TABLE `access_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `target_type` varchar(255) NOT NULL DEFAULT 'subscription',
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subscription_id` varchar(100) DEFAULT NULL,
  `target_name` varchar(255) NOT NULL,
  `requested_role` varchar(255) NOT NULL,
  `reason` text NOT NULL,
  `duration` varchar(255) NOT NULL DEFAULT 'permanent',
  `requested_until` timestamp NULL DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `decided_by_type` varchar(255) DEFAULT NULL,
  `decided_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `decided_at` timestamp NULL DEFAULT NULL,
  `decision_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `access_requests`
--

INSERT INTO `access_requests` (`id`, `user_id`, `target_type`, `team_id`, `subscription_id`, `target_name`, `requested_role`, `reason`, `duration`, `requested_until`, `status`, `decided_by_type`, `decided_by_id`, `decided_at`, `decision_reason`, `created_at`, `updated_at`) VALUES
(1, 2, 'subscription', NULL, '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'AA-SMS3-NA-PBAP-Prod', 'restricted_reader', 'For Daily health checks as a part of L1 team', 'time_bound', '2026-10-10 16:40:00', 'approved', 'App\\Models\\User', 1, '2026-10-10 11:13:03', 'Testing expiry', '2026-10-10 09:23:58', '2026-10-10 11:13:03'),
(2, 2, 'subscription', NULL, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'MA-SKX-eXtra-Production-Stage-Prod', 'restricted_reader', 'Need access to perform daily health operations.', 'permanent', NULL, 'revoked', 'App\\Models\\User', 1, '2026-10-10 10:09:22', 'Approved for DHC', '2026-10-10 09:57:31', '2026-10-10 10:09:22'),
(3, 2, 'subscription', NULL, 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'AA-GPM-BoschCloudPrinting-Prod', 'restricted_contributor', 'For report generation', 'time_bound', '2026-10-10 16:33:00', 'pending', NULL, NULL, NULL, NULL, '2026-10-10 11:02:26', '2026-10-10 11:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `log_name` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(255) DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `event` varchar(255) DEFAULT NULL,
  `batch_uuid` char(36) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `event`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, 'Requests', 'GET / (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\",\"route\":\"filament.admin.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":10347,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 02:50:20', '2026-10-10 02:50:20'),
(2, 'Requests', 'GET developer/audit-logs (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/audit-logs\",\"route\":\"filament.developer.resources.audit-logs.index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":7425,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 02:50:34', '2026-10-10 02:50:34'),
(3, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":1082,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 02:50:50', '2026-10-10 02:50:50'),
(4, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":145,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 02:50:54', '2026-10-10 02:50:54'),
(5, 'Requests', 'GET developer/audit-logs (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/audit-logs\",\"route\":\"filament.developer.resources.audit-logs.index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":14936,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 02:55:41', '2026-10-10 02:55:41'),
(6, 'Requests', 'GET developer/logs-control (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":954,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 02:55:45', '2026-10-10 02:55:45'),
(7, 'Requests', 'GET developer/logs-control (500)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Internal Server Error\",\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":500,\"response_time_ms\":17065,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 02:58:58', '2026-10-10 02:58:58'),
(8, 'Requests', 'GET developer/logs-control (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":15850,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:00:25', '2026-10-10 03:00:25'),
(9, 'Requests', 'GET developer/logs-control (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":15168,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:11:30', '2026-10-10 03:11:30'),
(10, 'Requests', 'GET developer (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\",\"route\":\"filament.developer.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":228,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:11:40', '2026-10-10 03:11:40'),
(11, 'Requests', 'GET / (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\",\"route\":\"filament.admin.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":5592,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:11:56', '2026-10-10 03:11:56'),
(12, 'Requests', 'GET developer (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\",\"route\":\"filament.developer.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":183,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:12:05', '2026-10-10 03:12:05'),
(13, 'Requests', 'GET developer (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\",\"route\":\"filament.developer.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":350,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:14:36', '2026-10-10 03:14:36'),
(14, 'Requests', 'GET developer/updater (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/updater\",\"route\":\"filament.developer.pages.updater\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":4934,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:14:44', '2026-10-10 03:14:44'),
(15, 'Requests', 'GET developer/logs-control (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":250,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:14:45', '2026-10-10 03:14:45'),
(16, 'Requests', 'GET developer/updater (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/updater\",\"route\":\"filament.developer.pages.updater\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":415,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:14:59', '2026-10-10 03:14:59'),
(17, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":560,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:15:12', '2026-10-10 03:15:12'),
(18, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":108,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:15:12', '2026-10-10 03:15:12'),
(19, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":110,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:15:14', '2026-10-10 03:15:14'),
(20, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:15', '2026-10-10 03:15:15'),
(21, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":2489,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:15:19', '2026-10-10 03:15:19'),
(22, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":58,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:19', '2026-10-10 03:15:19'),
(23, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":46,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:22', '2026-10-10 03:15:22'),
(24, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:25', '2026-10-10 03:15:25'),
(25, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":706,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:15:27', '2026-10-10 03:15:27'),
(26, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":39,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:29', '2026-10-10 03:15:29'),
(27, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":28,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:32', '2026-10-10 03:15:32'),
(28, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:36', '2026-10-10 03:15:36'),
(29, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:40', '2026-10-10 03:15:40'),
(30, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:44', '2026-10-10 03:15:44'),
(31, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":55,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:48', '2026-10-10 03:15:48'),
(32, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":23,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:52', '2026-10-10 03:15:52'),
(33, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":36,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:15:56', '2026-10-10 03:15:56'),
(34, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":29,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:00', '2026-10-10 03:16:00'),
(35, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:04', '2026-10-10 03:16:04'),
(36, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:08', '2026-10-10 03:16:08'),
(37, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":42,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:12', '2026-10-10 03:16:12'),
(38, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":26,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:16', '2026-10-10 03:16:16'),
(39, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":25,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:20', '2026-10-10 03:16:20'),
(40, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:24', '2026-10-10 03:16:24'),
(41, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:28', '2026-10-10 03:16:28'),
(42, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:31', '2026-10-10 03:16:31'),
(43, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:34', '2026-10-10 03:16:34'),
(44, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:37', '2026-10-10 03:16:37'),
(45, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":644,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:16:38', '2026-10-10 03:16:38'),
(46, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:40', '2026-10-10 03:16:40'),
(47, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":540,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:16:43', '2026-10-10 03:16:43'),
(48, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":46,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:44', '2026-10-10 03:16:44'),
(49, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":380,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:16:47', '2026-10-10 03:16:47'),
(50, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":17,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:47', '2026-10-10 03:16:47'),
(51, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":83,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:16:48', '2026-10-10 03:16:48'),
(52, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:50', '2026-10-10 03:16:50'),
(53, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:54', '2026-10-10 03:16:54'),
(54, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:16:57', '2026-10-10 03:16:57'),
(55, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:00', '2026-10-10 03:17:00'),
(56, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:03', '2026-10-10 03:17:03'),
(57, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:06', '2026-10-10 03:17:06'),
(58, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:09', '2026-10-10 03:17:09'),
(59, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:12', '2026-10-10 03:17:12'),
(60, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":305,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:17:13', '2026-10-10 03:17:13'),
(61, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:16', '2026-10-10 03:17:16'),
(62, 'Requests', 'GET developer/updater (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/updater\",\"route\":\"filament.developer.pages.updater\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":807,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:17:17', '2026-10-10 03:17:17'),
(63, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":44,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:23', '2026-10-10 03:17:23'),
(64, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:27', '2026-10-10 03:17:27'),
(65, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:31', '2026-10-10 03:17:31'),
(66, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":41,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:35', '2026-10-10 03:17:35'),
(67, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:39', '2026-10-10 03:17:39'),
(68, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":46,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:43', '2026-10-10 03:17:43'),
(69, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:47', '2026-10-10 03:17:47'),
(70, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":26,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:51', '2026-10-10 03:17:51'),
(71, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:55', '2026-10-10 03:17:55'),
(72, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:17:59', '2026-10-10 03:17:59'),
(73, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:03', '2026-10-10 03:18:03'),
(74, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:07', '2026-10-10 03:18:07'),
(75, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":48,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:11', '2026-10-10 03:18:11'),
(76, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":38,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:15', '2026-10-10 03:18:15'),
(77, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:19', '2026-10-10 03:18:19'),
(78, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:23', '2026-10-10 03:18:23'),
(79, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":41,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:27', '2026-10-10 03:18:27');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `event`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(80, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:31', '2026-10-10 03:18:31'),
(81, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":36,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:35', '2026-10-10 03:18:35'),
(82, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:39', '2026-10-10 03:18:39'),
(83, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:43', '2026-10-10 03:18:43'),
(84, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:47', '2026-10-10 03:18:47'),
(85, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":30,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:51', '2026-10-10 03:18:51'),
(86, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:55', '2026-10-10 03:18:55'),
(87, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:18:59', '2026-10-10 03:18:59'),
(88, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:03', '2026-10-10 03:19:03'),
(89, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:07', '2026-10-10 03:19:07'),
(90, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:11', '2026-10-10 03:19:11'),
(91, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:15', '2026-10-10 03:19:15'),
(92, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:19', '2026-10-10 03:19:19'),
(93, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":36,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:23', '2026-10-10 03:19:23'),
(94, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:27', '2026-10-10 03:19:27'),
(95, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:31', '2026-10-10 03:19:31'),
(96, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":61,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:35', '2026-10-10 03:19:35'),
(97, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":29,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:39', '2026-10-10 03:19:39'),
(98, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":36,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:43', '2026-10-10 03:19:43'),
(99, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":23,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:47', '2026-10-10 03:19:47'),
(100, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:51', '2026-10-10 03:19:51'),
(101, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":53,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:55', '2026-10-10 03:19:55'),
(102, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:19:59', '2026-10-10 03:19:59'),
(103, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:03', '2026-10-10 03:20:03'),
(104, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:07', '2026-10-10 03:20:07'),
(105, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:11', '2026-10-10 03:20:11'),
(106, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":46,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:15', '2026-10-10 03:20:15'),
(107, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:19', '2026-10-10 03:20:19'),
(108, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:23', '2026-10-10 03:20:23'),
(109, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":38,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:27', '2026-10-10 03:20:27'),
(110, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:31', '2026-10-10 03:20:31'),
(111, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":53,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:35', '2026-10-10 03:20:35'),
(112, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:39', '2026-10-10 03:20:39'),
(113, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":29,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:43', '2026-10-10 03:20:43'),
(114, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":28,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:47', '2026-10-10 03:20:47'),
(115, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":50,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:51', '2026-10-10 03:20:51'),
(116, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":44,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:55', '2026-10-10 03:20:55'),
(117, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":51,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:20:59', '2026-10-10 03:20:59'),
(118, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":42,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:03', '2026-10-10 03:21:03'),
(119, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:07', '2026-10-10 03:21:07'),
(120, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:11', '2026-10-10 03:21:11'),
(121, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:15', '2026-10-10 03:21:15'),
(122, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:19', '2026-10-10 03:21:19'),
(123, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:23', '2026-10-10 03:21:23'),
(124, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:27', '2026-10-10 03:21:27'),
(125, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:31', '2026-10-10 03:21:31'),
(126, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:35', '2026-10-10 03:21:35'),
(127, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":43,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:39', '2026-10-10 03:21:39'),
(128, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":52,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:43', '2026-10-10 03:21:43'),
(129, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":63,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:47', '2026-10-10 03:21:47'),
(130, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:51', '2026-10-10 03:21:51'),
(131, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:55', '2026-10-10 03:21:55'),
(132, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":25,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:21:59', '2026-10-10 03:21:59'),
(133, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:03', '2026-10-10 03:22:03'),
(134, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":38,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:07', '2026-10-10 03:22:07'),
(135, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:11', '2026-10-10 03:22:11'),
(136, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:15', '2026-10-10 03:22:15'),
(137, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":55,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:19', '2026-10-10 03:22:19'),
(138, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":46,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:23', '2026-10-10 03:22:23'),
(139, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:27', '2026-10-10 03:22:27'),
(140, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:31', '2026-10-10 03:22:31'),
(141, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:35', '2026-10-10 03:22:35'),
(142, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":52,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:39', '2026-10-10 03:22:39'),
(143, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":41,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:43', '2026-10-10 03:22:43'),
(144, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":51,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:47', '2026-10-10 03:22:47'),
(145, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":28,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:51', '2026-10-10 03:22:51'),
(146, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":50,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:55', '2026-10-10 03:22:55'),
(147, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:22:59', '2026-10-10 03:22:59'),
(148, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:23:03', '2026-10-10 03:23:03'),
(149, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:23:07', '2026-10-10 03:23:07'),
(150, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":26,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:23:11', '2026-10-10 03:23:11'),
(151, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":17,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:23:15', '2026-10-10 03:23:15'),
(152, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:23:19', '2026-10-10 03:23:19'),
(153, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":22,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:23:23', '2026-10-10 03:23:23'),
(154, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:04', '2026-10-10 03:24:04'),
(155, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:08', '2026-10-10 03:24:08'),
(156, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:12', '2026-10-10 03:24:12');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `event`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(157, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:16', '2026-10-10 03:24:16'),
(158, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":68,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:20', '2026-10-10 03:24:20'),
(159, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":28,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:24', '2026-10-10 03:24:24'),
(160, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":39,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:28', '2026-10-10 03:24:28'),
(161, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":45,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:32', '2026-10-10 03:24:32'),
(162, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:36', '2026-10-10 03:24:36'),
(163, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":43,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:40', '2026-10-10 03:24:40'),
(164, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":26,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:44', '2026-10-10 03:24:44'),
(165, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:48', '2026-10-10 03:24:48'),
(166, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:52', '2026-10-10 03:24:52'),
(167, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:24:56', '2026-10-10 03:24:56'),
(168, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":26,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:00', '2026-10-10 03:25:00'),
(169, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:04', '2026-10-10 03:25:04'),
(170, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:08', '2026-10-10 03:25:08'),
(171, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":31,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:12', '2026-10-10 03:25:12'),
(172, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":49,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:16', '2026-10-10 03:25:16'),
(173, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:20', '2026-10-10 03:25:20'),
(174, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":55,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:24', '2026-10-10 03:25:24'),
(175, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":29,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:28', '2026-10-10 03:25:28'),
(176, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:32', '2026-10-10 03:25:32'),
(177, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:36', '2026-10-10 03:25:36'),
(178, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:40', '2026-10-10 03:25:40'),
(179, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:44', '2026-10-10 03:25:44'),
(180, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":48,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:48', '2026-10-10 03:25:48'),
(181, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":30,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:52', '2026-10-10 03:25:52'),
(182, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:25:56', '2026-10-10 03:25:56'),
(183, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":29,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:00', '2026-10-10 03:26:00'),
(184, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:04', '2026-10-10 03:26:04'),
(185, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:08', '2026-10-10 03:26:08'),
(186, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:12', '2026-10-10 03:26:12'),
(187, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":43,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:16', '2026-10-10 03:26:16'),
(188, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:20', '2026-10-10 03:26:20'),
(189, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":36,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:24', '2026-10-10 03:26:24'),
(190, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":28,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:28', '2026-10-10 03:26:28'),
(191, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":37,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:32', '2026-10-10 03:26:32'),
(192, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:36', '2026-10-10 03:26:36'),
(193, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":57,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:40', '2026-10-10 03:26:40'),
(194, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:44', '2026-10-10 03:26:44'),
(195, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:48', '2026-10-10 03:26:48'),
(196, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":39,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:52', '2026-10-10 03:26:52'),
(197, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:26:56', '2026-10-10 03:26:56'),
(198, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":64,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:00', '2026-10-10 03:27:00'),
(199, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":39,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:04', '2026-10-10 03:27:04'),
(200, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:08', '2026-10-10 03:27:08'),
(201, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:12', '2026-10-10 03:27:12'),
(202, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":32,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:15', '2026-10-10 03:27:15'),
(203, 'Requests', 'GET developer/updater (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/updater\",\"route\":\"filament.developer.pages.updater\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":485,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:27:18', '2026-10-10 03:27:18'),
(204, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":44,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:21', '2026-10-10 03:27:21'),
(205, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:25', '2026-10-10 03:27:25'),
(206, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":10,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:29', '2026-10-10 03:27:29'),
(207, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:33', '2026-10-10 03:27:33'),
(208, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":33,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:37', '2026-10-10 03:27:37'),
(209, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":20,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:41', '2026-10-10 03:27:41'),
(210, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":13,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:45', '2026-10-10 03:27:45'),
(211, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":14,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:49', '2026-10-10 03:27:49'),
(212, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":12,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:53', '2026-10-10 03:27:53'),
(213, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":26,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:27:57', '2026-10-10 03:27:57'),
(214, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":20,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:01', '2026-10-10 03:28:01'),
(215, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":36,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:05', '2026-10-10 03:28:05'),
(216, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":14,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:09', '2026-10-10 03:28:09'),
(217, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":35,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:13', '2026-10-10 03:28:13'),
(218, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":27,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:17', '2026-10-10 03:28:17'),
(219, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":40,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:21', '2026-10-10 03:28:21'),
(220, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":30,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:25', '2026-10-10 03:28:25'),
(221, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":24,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:29', '2026-10-10 03:28:29'),
(222, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":19,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:33', '2026-10-10 03:28:33'),
(223, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":34,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:37', '2026-10-10 03:28:37'),
(224, 'Requests', 'GET updater/status (401)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":false,\"failure_reason\":\"Unauthorized\",\"url\":\"http:\\/\\/localhost:8000\\/updater\\/status\",\"route\":\"updater.status\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":401,\"response_time_ms\":39,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\",\"failure\"],\"risk\":\"high\"}', 'Page Request', NULL, '2026-10-10 03:28:41', '2026-10-10 03:28:41'),
(225, 'Requests', 'GET / (302)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\",\"route\":\"filament.admin.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":302,\"response_time_ms\":49,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:38:38', '2026-10-10 03:38:38'),
(226, 'Requests', 'GET login (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/login\",\"route\":\"filament.admin.auth.login\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":4221,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:38:42', '2026-10-10 03:38:42'),
(227, 'Requests', 'GET azure-subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/azure-subscriptions\\/5784d84d-05ea-4a9c-b625-7d0183e9240b\",\"route\":\"filament.admin.resources.azure-subscriptions.view\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":34911,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:39:24', '2026-10-10 03:39:24'),
(228, 'Access', 'Authentication succeeded.', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"guard\":\"web\",\"remember\":false,\"tags\":[\"authentication\",\"login\"]}', 'Login', NULL, '2026-10-10 03:40:39', '2026-10-10 03:40:39'),
(229, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":1820,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:40:39', '2026-10-10 03:40:39'),
(230, 'Requests', 'GET / (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\",\"route\":\"filament.admin.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":3541,\"is_refresh_or_navigation\":true,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:40:43', '2026-10-10 03:40:43'),
(231, 'Requests', 'GET developer (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\",\"route\":\"filament.developer.pages.dashboard\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":203,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:40:52', '2026-10-10 03:40:52'),
(232, 'Requests', 'GET developer/logs-control (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":771,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:40:58', '2026-10-10 03:40:58'),
(233, 'Requests', 'GET developer/audit-logs (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/audit-logs\",\"route\":\"filament.developer.resources.audit-logs.index\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":1043,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:41:00', '2026-10-10 03:41:00'),
(234, 'Requests', 'GET developer/logs-control (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/developer\\/logs-control\",\"route\":\"filament.developer.pages.logs-control\",\"method\":\"GET\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":225,\"is_refresh_or_navigation\":true,\"is_filament_request\":true,\"tags\":[\"request\"]}', 'Page Request', NULL, '2026-10-10 03:41:04', '2026-10-10 03:41:04');
INSERT INTO `activity_log` (`id`, `log_name`, `description`, `subject_type`, `subject_id`, `causer_type`, `causer_id`, `properties`, `event`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(235, 'Models', 'LogSetting #1 created', 'App\\Models\\LogSetting', 1, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(236, 'Models', 'LogSetting #2 created', 'App\\Models\\LogSetting', 2, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(237, 'Models', 'LogSetting #3 created', 'App\\Models\\LogSetting', 3, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(238, 'Models', 'LogSetting #4 created', 'App\\Models\\LogSetting', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(239, 'Models', 'LogSetting #5 created', 'App\\Models\\LogSetting', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(240, 'Models', 'LogSetting #6 created', 'App\\Models\\LogSetting', 6, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(241, 'Models', 'LogSetting #7 created', 'App\\Models\\LogSetting', 7, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(242, 'Models', 'LogSetting #8 created', 'App\\Models\\LogSetting', 8, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(243, 'Models', 'LogSetting #9 created', 'App\\Models\\LogSetting', 9, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(244, 'Models', 'LogSetting #10 created', 'App\\Models\\LogSetting', 10, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(245, 'Models', 'LogSetting #11 created', 'App\\Models\\LogSetting', 11, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(246, 'Models', 'LogSetting #12 created', 'App\\Models\\LogSetting', 12, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(247, 'Models', 'LogSetting #13 created', 'App\\Models\\LogSetting', 13, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(248, 'Models', 'LogSetting #14 created', 'App\\Models\\LogSetting', 14, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(249, 'Models', 'LogSetting #15 created', 'App\\Models\\LogSetting', 15, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(250, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":346,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(251, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":126,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:41:10', '2026-10-10 03:41:10'),
(252, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":2042,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:41:21', '2026-10-10 03:41:21'),
(253, 'Requests', 'POST livewire-27bd2a03/update (200)', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"status_code\":200,\"response_time_ms\":322,\"is_refresh_or_navigation\":false,\"is_filament_request\":false,\"tags\":[\"request\"]}', 'Request', NULL, '2026-10-10 03:41:25', '2026-10-10 03:41:25'),
(254, 'Models', 'LogSetting #5 updated', 'App\\Models\\LogSetting', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 03:41:32', '2026-10-10 03:41:32'),
(255, 'Models', 'Release #1 created', 'Ysfkaya\\ShipLog\\Models\\Release', 1, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 04:59:24', '2026-10-10 04:59:24'),
(256, 'Models', 'Release #1 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 1, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:06:36', '2026-10-10 05:06:36'),
(257, 'Models', 'Release #2 created', 'Ysfkaya\\ShipLog\\Models\\Release', 2, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 05:08:26', '2026-10-10 05:08:26'),
(258, 'Models', 'Release #3 created', 'Ysfkaya\\ShipLog\\Models\\Release', 3, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 05:10:32', '2026-10-10 05:10:32'),
(259, 'Models', 'Release #4 created', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 05:12:06', '2026-10-10 05:12:06'),
(260, 'Models', 'Release #5 created', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 05:13:07', '2026-10-10 05:13:07'),
(261, 'Models', 'Release #5 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:13:25', '2026-10-10 05:13:25'),
(262, 'Models', 'Release #5 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:14:59', '2026-10-10 05:14:59'),
(263, 'Models', 'Release #5 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:15:20', '2026-10-10 05:15:20'),
(264, 'Models', 'Release #5 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:15:34', '2026-10-10 05:15:34'),
(265, 'Models', 'Release #5 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:16:54', '2026-10-10 05:16:54'),
(266, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:17:07', '2026-10-10 05:17:07'),
(267, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:17:25', '2026-10-10 05:17:25'),
(268, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:17:43', '2026-10-10 05:17:43'),
(269, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:18:04', '2026-10-10 05:18:04'),
(270, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:19:28', '2026-10-10 05:19:28'),
(271, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:20:37', '2026-10-10 05:20:37'),
(272, 'Models', 'Release #5 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 5, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:21:43', '2026-10-10 05:21:43'),
(273, 'Models', 'Release #4 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 4, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:21:49', '2026-10-10 05:21:49'),
(274, 'Models', 'Release #3 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 3, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:21:55', '2026-10-10 05:21:55'),
(275, 'Models', 'Release #2 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 2, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:22:01', '2026-10-10 05:22:01'),
(276, 'Models', 'Release #1 updated', 'Ysfkaya\\ShipLog\\Models\\Release', 1, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 05:22:07', '2026-10-10 05:22:07'),
(277, 'Access', 'Authentication succeeded.', NULL, NULL, 'App\\Models\\User', 2, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko\\/20100101 Firefox\\/140.0\",\"guard\":\"web\",\"remember\":false,\"tags\":[\"authentication\",\"login\"]}', 'Login', NULL, '2026-10-10 07:05:51', '2026-10-10 07:05:51'),
(278, 'Notifications', 'Client portal notification \"Trial notifications\" delivered to 2 active user(s).', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"type\":\"general\",\"severity\":\"info\",\"recipient_count\":2,\"all_users\":true,\"team_count\":0,\"selected_user_count\":0,\"tags\":[\"notification\",\"client_portal\",\"sent\"]}', 'Notification Sent', NULL, '2026-10-10 07:11:39', '2026-10-10 07:11:39'),
(279, 'Access', 'Authentication session ended.', NULL, NULL, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/logout\",\"route\":\"filament.admin.auth.logout\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"guard\":\"web\",\"tags\":[\"authentication\",\"logout\"]}', 'Logout', NULL, '2026-10-10 08:53:58', '2026-10-10 08:53:58'),
(280, 'Access', 'Authentication failed.', NULL, NULL, NULL, NULL, '{\"success\":false,\"failure_reason\":\"Authentication credentials did not authenticate.\",\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"guard\":\"web\",\"identifier_type\":\"email\",\"tags\":[\"authentication\",\"failure\"],\"risk\":\"high\"}', 'Login Failed', NULL, '2026-10-10 08:54:10', '2026-10-10 08:54:10'),
(281, 'Access', 'Authentication succeeded.', NULL, NULL, 'App\\Models\\User', 2, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"guard\":\"web\",\"remember\":false,\"tags\":[\"authentication\",\"login\"]}', 'Login', NULL, '2026-10-10 08:54:18', '2026-10-10 08:54:18'),
(282, 'Models', 'Access request #1 created', 'App\\Models\\AccessRequest', 1, 'App\\Models\\User', 2, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 09:23:58', '2026-10-10 09:23:58'),
(283, 'Models', 'Access request #2 created', 'App\\Models\\AccessRequest', 2, 'App\\Models\\User', 2, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 09:57:31', '2026-10-10 09:57:31'),
(284, 'Access', 'Authentication succeeded.', NULL, NULL, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko\\/20100101 Firefox\\/140.0\",\"guard\":\"web\",\"remember\":false,\"tags\":[\"authentication\",\"login\"]}', 'Login', NULL, '2026-10-10 09:59:54', '2026-10-10 09:59:54'),
(285, 'Models', 'Access grant #1 created', 'App\\Models\\UserAccessGrant', 1, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko\\/20100101 Firefox\\/140.0\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 10:09:22', '2026-10-10 10:09:22'),
(286, 'Models', 'Access request #2 updated', 'App\\Models\\AccessRequest', 2, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko\\/20100101 Firefox\\/140.0\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 10:09:22', '2026-10-10 10:09:22'),
(287, 'Models', 'Team #4 created', 'App\\Models\\Team', 4, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko\\/20100101 Firefox\\/140.0\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(288, 'Models', 'Access request #3 created', 'App\\Models\\AccessRequest', 3, 'App\\Models\\User', 2, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 11:02:26', '2026-10-10 11:02:26'),
(289, 'Models', 'Access grant #2 created', 'App\\Models\\UserAccessGrant', 2, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 11:13:03', '2026-10-10 11:13:03'),
(290, 'Models', 'Access request #1 updated', 'App\\Models\\AccessRequest', 1, 'App\\Models\\Developer', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"updated\"]}', 'Updated', NULL, '2026-10-10 11:13:03', '2026-10-10 11:13:03'),
(291, 'Models', 'UserSubscriptionAccessOverride #1 created', 'App\\Models\\UserSubscriptionAccessOverride', 1, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko\\/20100101 Firefox\\/140.0\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-10 11:54:35', '2026-10-10 11:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `project_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(4, 2, 'eFOCuS', 'eFOCuS application environments.', 1, '2026-10-09 14:11:26', '2026-10-09 14:11:26'),
(5, 2, 'Extra', 'Extra application environments.', 1, '2026-10-09 14:11:26', '2026-10-09 14:11:26'),
(6, 2, 'CloudPrinting', 'CloudPrinting application environments.', 1, '2026-10-09 14:11:26', '2026-10-09 14:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `event` varchar(50) NOT NULL,
  `action` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` varchar(255) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`properties`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `azure_budgets`
--

CREATE TABLE `azure_budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `budget_name` varchar(255) NOT NULL,
  `amount` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `currency` varchar(16) DEFAULT NULL,
  `current_spend` decimal(18,4) DEFAULT NULL,
  `forecast_spend` decimal(18,4) DEFAULT NULL,
  `time_grain` varchar(32) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `fetched_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `azure_budgets`
--

INSERT INTO `azure_budgets` (`id`, `subscription_id`, `budget_name`, `amount`, `currency`, `current_spend`, `forecast_spend`, `time_grain`, `start_date`, `end_date`, `fetched_at`) VALUES
(51, '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Monthly_Budget_AA-AS-EIT2-Prod', 28000.0000, 'EUR', 22920.9526, 27272.5736, 'Monthly', '2026-02-01', '2030-01-31', '2026-09-26 15:41:31'),
(52, '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'CMS_QA_budget', 275.0000, 'EUR', 260.9420, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:36'),
(53, 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS_budget_alert', 600.0000, 'EUR', 368.8468, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:39'),
(54, '7498780e-1785-465f-9d50-c8ac1e929376', 'efocusQA_budget', 597.0000, 'EUR', 423.6815, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:42'),
(55, 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'BRP_Budget', 3910.0000, 'EUR', 3290.4573, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:45'),
(56, 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'Efocus_Prod_Budget', 4500.0000, 'EUR', 2374.1381, NULL, 'Monthly', '2026-05-01', '2028-04-30', '2026-09-26 15:41:48'),
(57, '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'Dashboard_budget', 1700.0000, 'EUR', 1482.6229, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:52'),
(58, 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'Monthly_Budget_for_Subscription_', 6300.0000, 'EUR', 4218.3294, 5012.5976, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:55'),
(59, '19fb36f7-0105-4d68-be29-02c3934a2abc', 'Monthly_budget_for_subscription_', 1800.0000, 'EUR', 1503.8530, 1793.0616, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:41:57'),
(60, '5b46ccc6-604b-4c5c-81c2-96b133061773', 'AutoParts-Dev-MonthlyBudget', 700.0000, 'EUR', 501.2845, 599.7850, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:02'),
(61, '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'Autoparts-PROD-MonthlyBudget', 3000.0000, 'EUR', 1685.6439, 2004.2659, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:04'),
(62, 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'Autoparts-QA-MonthlyBudget', 1300.0000, 'EUR', 1364.9840, 1660.6185, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:07'),
(63, '12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'Insiderlens_budget', 401.0000, 'EUR', 288.3594, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:10'),
(64, '50d41c25-8668-4497-8925-766161f0cddb', 'Miconic_qa_budget', 462.0000, 'EUR', 398.8026, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:12'),
(65, '02ab4266-3406-48d4-bfbc-76605dde11d7', 'Associate_connect_budget', 2164.0000, 'EUR', 967.2652, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:17'),
(66, 'fc152cdb-dc60-44e7-bbee-412c719cb49a', 'Miconic_dev_budget', 423.0000, 'EUR', 0.0276, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:20'),
(67, '4172676b-8a9c-44e0-b2ab-727057b691b7', 'Miconic_Prod', 500.0000, 'EUR', 564.0672, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:24'),
(68, '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'Budget_alert_BRP_QA', 500.0000, 'EUR', 369.4146, NULL, 'Monthly', '2025-06-01', '2027-05-31', '2026-09-26 15:42:27'),
(69, 'c12d79d2-0655-4caa-839e-fc47e019271c', 'Exim_budget', 2950.0000, 'EUR', 462.6341, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:29'),
(70, '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'Extra-Dev-MonthlyBudget', 1200.0000, 'EUR', 1160.7045, 1369.8490, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:32'),
(71, 'c080fc5b-797d-45db-b089-01cc05f9a758', 'Extra-QA-MonthlyBudget', 5500.0000, 'EUR', 3876.6916, 4647.9150, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:37'),
(72, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'Extra-PROD-MonthlyBudget', 7200.0000, 'EUR', 1019.9421, 1210.9856, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:40'),
(73, '219ccbf6-e35b-4758-961a-ede690403d86', 'Hamro_Bosch_QA', 250.0000, 'EUR', 131.2220, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:42'),
(74, '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'Hamrobosch_budget', 420.0000, 'EUR', 320.8875, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:45'),
(75, '6c389f01-77d0-4c2c-b846-8dfa36987531', 'Monthly_Budget_EAPSandbox', 50.0000, 'EUR', 147.5695, NULL, 'Monthly', '2026-02-01', '2028-01-31', '2026-09-26 15:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `azure_cost_forecasts`
--

CREATE TABLE `azure_cost_forecasts` (
  `subscription_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `forecast_amount` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `currency` varchar(16) DEFAULT NULL,
  `fetched_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `azure_cost_forecasts`
--

INSERT INTO `azure_cost_forecasts` (`subscription_id`, `forecast_amount`, `currency`, `fetched_at`) VALUES
('02ab4266-3406-48d4-bfbc-76605dde11d7', 1150.9157, 'EUR', '2026-09-26 15:40:41'),
('0e6dc52f-1c64-4d5a-9396-29e37fa41079', 26088.8822, 'EUR', '2026-09-26 15:39:32'),
('12db631f-5ab4-4237-9fa6-6d92de7a53e8', 342.6620, 'EUR', '2026-09-26 15:40:31'),
('19fb36f7-0105-4d68-be29-02c3934a2abc', 1306.3217, 'EUR', '2026-09-26 15:40:12'),
('1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 5440.2422, 'EUR', '2026-09-26 15:41:12'),
('1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 316.1285, 'EUR', '2026-09-26 15:39:40'),
('219ccbf6-e35b-4758-961a-ede690403d86', 156.8720, 'EUR', '2026-09-26 15:41:16'),
('2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 1892.5072, 'EUR', '2026-09-26 15:40:21'),
('4172676b-8a9c-44e0-b2ab-727057b691b7', 669.3120, 'EUR', '2026-09-26 15:40:51'),
('50d41c25-8668-4497-8925-766161f0cddb', 475.7954, 'EUR', '2026-09-26 15:40:37'),
('5784d84d-05ea-4a9c-b625-7d0183e9240b', 439.1368, 'EUR', '2026-09-26 15:40:55'),
('5b46ccc6-604b-4c5c-81c2-96b133061773', 453.0746, 'EUR', '2026-09-26 15:40:17'),
('6b8e63a8-5397-4d83-9b49-164067a4e892', 0.0000, NULL, '2026-09-26 15:39:23'),
('6c389f01-77d0-4c2c-b846-8dfa36987531', 176.3658, 'EUR', '2026-09-26 15:41:24'),
('7498780e-1785-465f-9d50-c8ac1e929376', 504.2768, 'EUR', '2026-09-26 15:39:49'),
('77839ff3-b3aa-42b0-b490-55830c14bd3a', 1100.8206, 'EUR', '2026-09-26 15:41:04'),
('8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 1677.3595, 'EUR', '2026-09-26 15:40:03'),
('8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 381.0768, 'EUR', '2026-09-26 15:41:20'),
('a77eebf3-17a1-4378-90bf-2c96b1028137', 1248.5536, 'EUR', '2026-09-26 15:40:26'),
('c080fc5b-797d-45db-b089-01cc05f9a758', 4276.3621, 'EUR', '2026-09-26 15:41:08'),
('c12d79d2-0655-4caa-839e-fc47e019271c', 564.0384, 'EUR', '2026-09-26 15:41:00'),
('c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 2814.9593, 'EUR', '2026-09-26 15:39:58'),
('d26dd531-2174-4c45-a240-032e0d05dfa0', 4660.0586, 'EUR', '2026-09-26 15:40:07'),
('d357b6e3-c707-4e36-8681-ba7ef9e30e8a', 0.0000, NULL, '2026-09-26 15:39:28'),
('dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 3923.8073, 'EUR', '2026-09-26 15:39:54'),
('e6800a5e-47c7-47df-8bf8-fc2c616e1442', 0.0000, NULL, '2026-09-26 15:39:35'),
('fc152cdb-dc60-44e7-bbee-412c719cb49a', 0.0328, 'EUR', '2026-09-26 15:40:46'),
('fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 439.8836, 'EUR', '2026-09-26 15:39:45');

-- --------------------------------------------------------

--
-- Table structure for table `azure_security_summary`
--

CREATE TABLE `azure_security_summary` (
  `summary_id` tinyint(3) UNSIGNED NOT NULL,
  `weighted_score` decimal(7,4) NOT NULL DEFAULT 0.0000,
  `total_weight` decimal(18,4) NOT NULL DEFAULT 0.0000,
  `fetched_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `azure_security_summary`
--

INSERT INTO `azure_security_summary` (`summary_id`, `weighted_score`, `total_weight`, `fetched_at`) VALUES
(1, 87.1598, 7248.0000, '2026-09-26 15:42:48');

-- --------------------------------------------------------

--
-- Table structure for table `azure_subscriptions`
--

CREATE TABLE `azure_subscriptions` (
  `subscription_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `key_vault_reference` varchar(255) DEFAULT NULL,
  `health_status` varchar(50) DEFAULT 'Healthy',
  `security_score` decimal(5,2) DEFAULT 100.00,
  `mtd_spend_eur` decimal(10,2) DEFAULT 0.00,
  `last_synced` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `azure_subscriptions`
--

INSERT INTO `azure_subscriptions` (`subscription_id`, `display_name`, `application_id`, `environment`, `key_vault_reference`, `health_status`, `security_score`, `mtd_spend_eur`, `last_synced`) VALUES
('02ab4266-3406-48d4-bfbc-76605dde11d7', 'RBIN-BDO-web-mobile-PROD', NULL, NULL, NULL, 'Degraded', 53.35, 972.31, '2026-09-26 15:38:20'),
('0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AA-AS-EIT2-Prod', NULL, NULL, NULL, 'Healthy', 91.07, 23014.95, '2026-09-26 15:36:48'),
('12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'OT-RBIN-BCS1-InsiderLens-Prod', NULL, NULL, NULL, 'Warning', 80.00, 290.37, '2026-09-26 15:38:09'),
('19fb36f7-0105-4d68-be29-02c3934a2abc', 'AA-GPM-BoschCloudPrinting-QA', 6, 'QA', NULL, 'Healthy', 89.63, 1511.27, '2026-10-09 19:41:26'),
('1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'MA-SKX-eXtra-Production-Stage-Prod', 5, 'PROD', NULL, 'Warning', 84.92, 5863.72, '2026-10-09 19:41:26'),
('1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'AA-ICO-IN-Azure-QA', NULL, NULL, NULL, 'Warning', 80.43, 261.75, '2026-09-26 15:37:01'),
('219ccbf6-e35b-4758-961a-ede690403d86', 'Hamro-Bosch', NULL, NULL, NULL, 'Healthy', 86.96, 132.08, '2026-09-26 15:39:07'),
('2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'AA-SMS3-NA-PBAP-Prod', NULL, NULL, NULL, 'Warning', 80.95, 1694.31, '2026-09-26 15:37:59'),
('4172676b-8a9c-44e0-b2ab-727057b691b7', 'OT-RBIN-PJ-DIGS-MICONIC-Prod', NULL, NULL, NULL, 'Healthy', 92.00, 567.71, '2026-09-26 15:38:31'),
('50d41c25-8668-4497-8925-766161f0cddb', 'OT-RBIN-PJ-DIGS-MICONIC-QA', NULL, NULL, NULL, 'Healthy', 85.19, 400.71, '2026-09-26 15:38:15'),
('5784d84d-05ea-4a9c-b625-7d0183e9240b', 'AA-BDO-IN-BoschRewards-QA', NULL, NULL, NULL, 'Healthy', 99.67, 372.00, '2026-09-26 15:38:36'),
('5b46ccc6-604b-4c5c-81c2-96b133061773', 'AA-SMS3-NA-BAP5.0-Dev', NULL, NULL, NULL, 'Healthy', 87.29, 503.93, '2026-09-26 15:37:52'),
('6b8e63a8-5397-4d83-9b49-164067a4e892', 'XC_Production_XC/ENG-Bp_866512', NULL, NULL, NULL, 'Degraded', 0.00, 0.00, '2026-09-26 15:36:35'),
('6c389f01-77d0-4c2c-b846-8dfa36987531', 'EAP Sandbox', NULL, NULL, NULL, 'Healthy', 90.51, 148.75, '2026-09-26 15:39:20'),
('7498780e-1785-465f-9d50-c8ac1e929376', 'AA-ICO-IN-eFOCuS-QA', 4, 'QA', NULL, 'Healthy', 91.47, 426.17, '2026-10-09 19:41:26'),
('77839ff3-b3aa-42b0-b490-55830c14bd3a', 'MA-SKX-eXtra-Dev-Stage-Dev', 5, 'DEV', NULL, 'Warning', 78.57, 1167.13, '2026-10-09 19:41:26'),
('8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'AA-ICO-IN-AA-Dashboard-Prod', NULL, NULL, NULL, 'Healthy', 87.12, 1494.64, '2026-09-26 15:37:32'),
('8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'Hamro Bosch Prod', NULL, NULL, NULL, 'Warning', 82.61, 323.00, '2026-09-26 15:39:13'),
('a77eebf3-17a1-4378-90bf-2c96b1028137', 'AA-SMS3-NA-QA', NULL, NULL, NULL, 'Healthy', 90.48, 1370.51, '2026-09-26 15:38:03'),
('c080fc5b-797d-45db-b089-01cc05f9a758', 'MA-SKX-eXtra-Promo-Stage-QA', 5, 'QA', NULL, 'Healthy', 87.57, 3900.99, '2026-10-09 19:41:26'),
('c12d79d2-0655-4caa-839e-fc47e019271c', 'OT-RBIN-GS-EXIMPortal-Prod', NULL, NULL, NULL, 'Degraded', 69.05, 465.59, '2026-09-26 15:38:42'),
('c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'AA-ICO-IN-eFOCuS-Prod', 4, 'PROD', NULL, 'Healthy', 92.26, 2391.48, '2026-10-09 19:41:26'),
('d26dd531-2174-4c45-a240-032e0d05dfa0', 'AA-GPM-BoschCloudPrinting-Prod', 6, 'PROD', NULL, 'Warning', 75.85, 4243.84, '2026-10-09 19:41:26'),
('d357b6e3-c707-4e36-8681-ba7ef9e30e8a', 'BD-PIP1-HardenedImages-Prod', NULL, NULL, NULL, 'Degraded', 0.00, 0.00, '2026-09-26 15:36:41'),
('dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'AA-ICO-IN-BoschRewards-Prod', NULL, NULL, NULL, 'Healthy', 94.94, 3325.95, '2026-09-26 15:37:19'),
('e6800a5e-47c7-47df-8bf8-fc2c616e1442', 'CI-DAE1.5-ASCWorkShop-QA', NULL, NULL, NULL, 'Degraded', 0.00, 0.00, '2026-09-26 15:36:54'),
('fc152cdb-dc60-44e7-bbee-412c719cb49a', 'AA-SWS-IN-MICONIC-Dev', NULL, NULL, NULL, 'Healthy', 100.00, 0.03, '2026-09-26 15:38:26'),
('fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'AA-ICO-IN-Claims-Management-Prod', NULL, NULL, NULL, 'Healthy', 91.70, 370.96, '2026-09-26 15:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `billing_resources`
--

CREATE TABLE `billing_resources` (
  `resource_id` varchar(500) NOT NULL,
  `subscription_id` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) NOT NULL,
  `resource_type` varchar(100) NOT NULL,
  `region` varchar(50) NOT NULL,
  `sku` varchar(100) DEFAULT 'N/A',
  `cost_eur` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing_resources`
--

INSERT INTO `billing_resources` (`resource_id`, `subscription_id`, `name`, `resource_type`, `region`, `sku`, `cost_eur`) VALUES
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/aftermarket-rg/providers/Microsoft.Storage/storageAccounts/csastorageaccount', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'csastorageaccount', 'storageaccounts', 'centralindia', 'StandardV2_GRS', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/aftermarket-rg/providers/Microsoft.Web/serverFarms/ASP-aftermarketrg-ae4b', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-aftermarketrg-ae4b', 'serverfarms', 'canadacentral', 'B1', 6.71),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/aftermarket-rg/providers/Microsoft.Web/sites/aftermarket-api-bosch', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'aftermarket-api-bosch', 'sites', 'canadacentral', 'N/A', 0.17),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-dev-rg-01/providers/Microsoft.Sql/servers/associ-connec-dev-srv-03/databases/master', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-dev-rg-01/providers/Microsoft.Sql/servers/associ-connec-dev-srv-03/databases/RBIN-Connect-DB-02', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'RBIN-Connect-DB-02', 'databases', 'southindia', 'Standard', 7.74),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-dev-rg-01/providers/Microsoft.Storage/storageAccounts/storageaccountassocb622', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'storageaccountassocb622', 'storageaccounts', 'centralus', 'Standard_LRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-flexi-dev/providers/Microsoft.Storage/storageAccounts/associconnecflexidevsa01', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associconnecflexidevsa01', 'storageaccounts', 'southindia', 'Standard_LRS', 1.30),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-flexi-dev/providers/Microsoft.Web/serverFarms/ASP-associconnecflexidev-92d4', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-associconnecflexidev-92d4', 'serverfarms', 'southindia', 'Y1', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-flexi-dev/providers/Microsoft.Web/serverFarms/associ-connec-dev-asp-01', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-dev-asp-01', 'serverfarms', 'southindia', 'S1', 37.03),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-flexi-dev/providers/Microsoft.Web/sites/associ-connec-dev-flexi-funapp', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-dev-flexi-funapp', 'sites', 'southindia', 'N/A', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-flexi-dev/providers/Microsoft.Web/sites/associ-connec-dev-flexi-webapp01', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-dev-flexi-webapp01', 'sites', 'southindia', 'N/A', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Sql/servers/associ-connec-prd-srv-01/databases/associ-connec-prd-db-01', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-prd-db-01', 'databases', 'southindia', 'Standard', 15.48),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Sql/servers/associ-connec-prd-srv-01/databases/master', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Storage/storageAccounts/associconnecprdstrac01', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associconnecprdstrac01', 'storageaccounts', 'southindia', 'Standard_LRS', 1.52),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Storage/storageAccounts/storageaccountassoc9c6a', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'storageaccountassoc9c6a', 'storageaccounts', 'southindia', 'Standard_LRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Web/serverFarms/ASP-associconnecprdrg01-bc47', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-associconnecprdrg01-bc47', 'serverfarms', 'southindia', 'S1', 37.03),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Web/serverFarms/associ-connec-prd-flexi', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-prd-flexi', 'serverfarms', 'southindia', 'S1', 74.05),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Web/sites/associ-connec-prd-flexi-apiapp', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-prd-flexi-apiapp', 'sites', 'southindia', 'N/A', 0.02),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Web/sites/associ-connec-prd-flexi-funapp', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-prd-flexi-funapp', 'sites', 'southindia', 'N/A', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Web/sites/associ-connec-prd-flexi-webapp', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-prd-flexi-webapp', 'sites', 'southindia', 'N/A', 0.24),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/associ-connec-prd-rg-01/providers/Microsoft.Web/sites/associ-connec-prd-wapp-01', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'associ-connec-prd-wapp-01', 'sites', 'southindia', 'N/A', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/cloud-shell-storage-southeastasia/providers/Microsoft.Storage/storageAccounts/cs1100320009c15f248', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'cs1100320009c15f248', 'storageaccounts', 'southeastasia', 'Standard_LRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/csa-dev-new-rg/providers/Microsoft.Compute/virtualMachines/csa-dev-new-vm', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'csa-dev-new-vm', 'virtualmachines', 'centralindia', 'N/A', 1.61),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/csa-dev-new-rg/providers/Microsoft.Storage/storageAccounts/csadevstorage', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'csadevstorage', 'storageaccounts', 'centralindia', 'Standard_GRS', 1.23),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/dfq-cri_group/providers/Microsoft.Web/sites/dfq-cri', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'dfq-cri', 'sites', 'southindia', 'N/A', 0.17),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/drbfm-dev/providers/Microsoft.Storage/storageAccounts/boschdst', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'boschdst', 'storageaccounts', 'centralindia', 'Standard_LRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/drbfm-dev/providers/Microsoft.Storage/storageAccounts/stske3ban017677481281722', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'stske3ban017677481281722', 'storageaccounts', 'eastus2', 'Standard_LRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Sql/servers/dfq-server-server/databases/dfq-server-database', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'dfq-server-database', 'databases', 'centralindia', 'Basic', 2.82),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Sql/servers/dfq-server-server/databases/master', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'master', 'databases', 'centralindia', 'GP_SYSTEM', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Web/serverFarms/ASP-MSILDFQ-9768', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-MSILDFQ-9768', 'serverfarms', 'centralindia', 'P1v2', 42.09),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Web/serverFarms/ASP-MSILDFQ-b489', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-MSILDFQ-b489', 'serverfarms', 'indiasouthcentral', 'B1', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Web/serverFarms/ASP-MSILDFQ-b79f', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-MSILDFQ-b79f', 'serverfarms', 'centralindia', 'B1', 6.71),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Web/sites/dfq', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'dfq', 'sites', 'centralindia', 'N/A', 0.20),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Web/sites/dfq-cri-bot', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'dfq-cri-bot', 'sites', 'indiasouthcentral', 'N/A', 0.16),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/MSIL_DFQ/providers/Microsoft.Web/sites/dfq-server', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'dfq-server', 'sites', 'centralindia', 'N/A', 0.20),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rbin-flexi-prod/providers/Microsoft.Storage/storageAccounts/rbinflexiprodsi001', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'rbinflexiprodsi001', 'storageaccounts', 'southindia', 'Standard_RAGRS', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-2wp-requirements/providers/Microsoft.Web/sites/bosch-2wp-requirements', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'bosch-2wp-requirements', 'sites', 'southindia', 'N/A', 0.16),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-auto-genie/providers/Microsoft.Storage/storageAccounts/autogenie', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'autogenie', 'storageaccounts', 'southindia', 'Standard_RAGRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-auto-genie/providers/Microsoft.Storage/storageAccounts/autogeniestorage', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'autogeniestorage', 'storageaccounts', 'swedencentral', 'Standard_LRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-auto-genie/providers/Microsoft.Web/serverFarms/ASP-rgautogenie-8d2e', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-rgautogenie-8d2e', 'serverfarms', 'southindia', 'B3', 27.41),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-auto-genie/providers/Microsoft.Web/sites/auto-genie-admin', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'auto-genie-admin', 'sites', 'southindia', 'N/A', 0.16),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-auto-genie/providers/Microsoft.Web/sites/auto-genie-api', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'auto-genie-api', 'sites', 'southindia', 'N/A', 0.18),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-auto-genie/providers/Microsoft.Web/sites/auto-genie-api-prod', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'auto-genie-api-prod', 'sites', 'southindia', 'N/A', 0.17),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-bosch-hub-prod/providers/Microsoft.Sql/servers/bosch-hub-sql-prod/databases/bosch-hub', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'bosch-hub', 'databases', 'indiasouthcentral', 'GP_S_Gen5', 1.44),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-bosch-hub-prod/providers/Microsoft.Sql/servers/bosch-hub-sql-prod/databases/master', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'master', 'databases', 'indiasouthcentral', 'GP_SYSTEM', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-bosch-hub-prod/providers/Microsoft.Storage/storageAccounts/boschhubprodyfemwmuvuzyu', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'boschhubprodyfemwmuvuzyu', 'storageaccounts', 'southindia', 'Standard_LRS', 0.10),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-bosch-hub-prod/providers/Microsoft.Web/serverFarms/asp-bosch-hub-prod', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'asp-bosch-hub-prod', 'serverfarms', 'southindia', 'P1v3', 1.26),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-bosch-hub-prod/providers/Microsoft.Web/sites/bosch-hub-api-prod', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'bosch-hub-api-prod', 'sites', 'southindia', 'N/A', 0.03),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-bosch-hub-prod/providers/Microsoft.Web/sites/bosch-hub-ui-prod', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'bosch-hub-ui-prod', 'sites', 'southindia', 'N/A', 0.03),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-drbfm/providers/Microsoft.Storage/storageAccounts/drbfmblob', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'drbfmblob', 'storageaccounts', 'southindia', 'Standard_RAGRS', 1.29),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-rbin-bdo-agents/providers/Microsoft.Storage/storageAccounts/rgrbinbdoagentsa04d', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'rgrbinbdoagentsa04d', 'storageaccounts', 'eastus2', 'Standard_LRS', 1.33),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-rbin-bdo-agents/providers/Microsoft.Web/serverFarms/ASP-rgrbinbdoagents-9c79', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-rgrbinbdoagents-9c79', 'serverfarms', 'eastus2', 'Y1', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-rbin-bdo-agents/providers/Microsoft.Web/serverFarms/ASP-rgrbinbdoagents-aa85', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'ASP-rgrbinbdoagents-aa85', 'serverfarms', 'eastus2', 'FC1', 0.00),
('/subscriptions/02ab4266-3406-48d4-bfbc-76605dde11d7/resourceGroups/rg-rbin-bdo-agents/providers/Microsoft.Web/sites/rbin-bdo-newsletter', '02ab4266-3406-48d4-bfbc-76605dde11d7', 'rbin-bdo-newsletter', 'sites', 'eastus2', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/dew-ocp-prod/providers/Microsoft.Storage/storageAccounts/dewocpprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'dewocpprod', 'storageaccounts', 'germanywestcentral', 'Standard_LRS', 7.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_ETS/providers/Microsoft.Sql/servers/eit-prod-ets-sql/databases/EnterpriseServices', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'EnterpriseServices', 'databases', 'centralus', 'GP_S_Gen5', 159.83),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_ETS/providers/Microsoft.Sql/servers/eit-prod-ets-sql/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_ETS/providers/Microsoft.Sql/servers/eit-prod-ets-sql/databases/Subscription_bosch-automotive_com', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Subscription_bosch-automotive_com', 'databases', 'centralus', 'GP_S_Gen5', 159.64),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_ETS/providers/Microsoft.Storage/storageAccounts/eitprodetsstorage', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'eitprodetsstorage', 'storageaccounts', 'centralus', 'Standard_RAGRS', 8.04),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_ets/providers/Microsoft.Storage/storageAccounts/sqlvaggdmihkmwyzy2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'sqlvaggdmihkmwyzy2', 'storageaccounts', 'centralus', 'Standard_LRS', 7.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_ETS/providers/Microsoft.Web/serverFarms/EIT-PROD-ETS-ASP-SVC2-W', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'EIT-PROD-ETS-ASP-SVC2-W', 'serverfarms', 'centralus', 'P1v2', 159.94),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_ETS/providers/Microsoft.Web/sites/Ets-Subscription-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Ets-Subscription-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-GMAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-GMAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.68),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-GMAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-GMAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.70),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-GMAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-GMAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.52),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-GMAP1-P4', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-GMAP1-P4', 'virtualmachines', 'centralus', 'N/A', 9.37),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-GMAP1-P5', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-GMAP1-P5', 'virtualmachines', 'centralus', 'N/A', 9.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-HOAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-HOAP1-P1', 'virtualmachines', 'centralus', 'N/A', 10.14),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-HOAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-HOAP1-P2', 'virtualmachines', 'centralus', 'N/A', 10.29),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-HOAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-HOAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.83),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-HOAP1-P4', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-HOAP1-P4', 'virtualmachines', 'centralus', 'N/A', 10.15),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-MCAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-MCAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.35),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-MCAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-MCAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.86),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-MCAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-MCAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.85),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-MCAP1-P4', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-MCAP1-P4', 'virtualmachines', 'centralus', 'N/A', 9.85),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-MZAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-MZAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.68),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-MZAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-MZAP1-P2', 'virtualmachines', 'centralus', 'N/A', 10.14),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-NIAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-NIAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.45),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-NIAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-NIAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.83),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-NIAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-NIAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-NIAP1-P4', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-NIAP1-P4', 'virtualmachines', 'centralus', 'N/A', 9.74),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-NIAP1-P5', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-NIAP1-P5', 'virtualmachines', 'centralus', 'N/A', 9.74),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-STAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-STAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.43),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-STAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-STAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.85),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-STAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-STAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.68),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/eit_prod_rdi/providers/Microsoft.Compute/virtualMachines/AZ1-STAP1-P4', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-STAP1-P4', 'virtualmachines', 'centralus', 'N/A', 9.98),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-SUAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-SUAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.53),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-SUAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-SUAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.67),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-SUAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-SUAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.83),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-SUAP1-P4', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-SUAP1-P4', 'virtualmachines', 'centralus', 'N/A', 9.67),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-TTAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-TTAP1-P1', 'virtualmachines', 'centralus', 'N/A', 9.98),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-TTAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-TTAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.62),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-VLAP1-P1', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-VLAP1-P1', 'virtualmachines', 'centralus', 'N/A', 10.31),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-VLAP1-P2', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-VLAP1-P2', 'virtualmachines', 'centralus', 'N/A', 9.84),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Compute/virtualMachines/AZ1-VLAP1-P3', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AZ1-VLAP1-P3', 'virtualmachines', 'centralus', 'N/A', 9.74),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/EIT_PROD_RDI/providers/Microsoft.Storage/storageAccounts/eitprodrdi', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'eitprodrdi', 'storageaccounts', 'centralus', 'Standard_LRS', 7.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-dias-prod/providers/Microsoft.Sql/servers/usc-dias-sql-prod/databases/DiasDataPipeline', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'DiasDataPipeline', 'databases', 'centralus', 'GP_S_Gen5', 79.67),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-dias-prod/providers/Microsoft.Sql/servers/usc-dias-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Sql/servers/usc-ets-sql-prod/databases/Cpd_bosch-automotive_com', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Cpd_bosch-automotive_com', 'databases', 'centralus', 'GP_S_Gen5', 156.81),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Sql/servers/usc-ets-sql-prod/databases/EnterpriseServices', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'EnterpriseServices', 'databases', 'centralus', 'GP_S_Gen5', 156.90),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Sql/servers/usc-ets-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Storage/storageAccounts/uscetsprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscetsprod', 'storageaccounts', 'centralus', 'Standard_LRS', 13.77),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/serverFarms/usc-ets-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-svc1-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.51),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/serverFarms/usc-ets-svc2-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-svc2-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.51),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/serverFarms/usc-ets-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-ui1-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.51),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/sites/usc-ets-Cpd-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-Cpd-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/sites/usc-ets-EmailSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-EmailSvc-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/sites/usc-ets-SerialScan-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-SerialScan-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/sites/usc-ets-SerialScanRSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-SerialScanRSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/sites/usc-ets-Subscription-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-Subscription-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ets-prod/providers/Microsoft.Web/sites/usc-ets-ZipCode-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ets-ZipCode-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-gacp-prod/providers/Microsoft.Storage/storageAccounts/uscgacpcdnprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscgacpcdnprod', 'storageaccounts', 'centralus', 'Standard_GRS', 7.63),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-hdst-prod/providers/Microsoft.Storage/storageAccounts/uschdstcdnprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uschdstcdnprod', 'storageaccounts', 'centralus', 'Standard_GRS', 7.75),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-hdst-prod/providers/Microsoft.Storage/storageAccounts/uschdstprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uschdstprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.24),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-hdst-prod/providers/Microsoft.Web/serverFarms/usc-hdst-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-hdst-fa1-w-prod', 'serverfarms', 'centralus', 'EP1', 84.12),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-hdst-prod/providers/Microsoft.Web/serverFarms/usc-hdst-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-hdst-ui1-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.61),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-hdst-prod/providers/Microsoft.Web/sites/usc-hdst-StreetPerformanceTuner-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-hdst-StreetPerformanceTuner-fa-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-hdst-prod/providers/Microsoft.Web/sites/usc-hdst-StreetPerformanceTuner-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-hdst-StreetPerformanceTuner-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ivw-prod/providers/Microsoft.Storage/storageAccounts/uscivwprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscivwprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ivw-prod/providers/Microsoft.Web/serverFarms/usc-ivw-ui1-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ivw-ui1-l-prod', 'serverfarms', 'centralus', 'P0v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ivw-prod/providers/Microsoft.Web/sites/usc-ivw-Analytics-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ivw-Analytics-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ivw-prod/providers/Microsoft.Web/sites/usc-ivw-AnalyticsDashboard-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-ivw-AnalyticsDashboard-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-jdi-prod/providers/Microsoft.Web/serverFarms/usc-jdi-svc1-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-jdi-svc1-l-prod', 'serverfarms', 'centralus', 'P0v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-jdi-prod/providers/Microsoft.Web/sites/usc-jdi-JdiSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-jdi-JdiSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-leg-prod/providers/Microsoft.Storage/storageAccounts/usclegprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usclegprod', 'storageaccounts', 'centralus', 'Standard_LRS', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-leg-prod/providers/Microsoft.Web/serverFarms/usc-leg-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-leg-ui1-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.61),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-leg-prod/providers/Microsoft.Web/sites/usc-leg-Subman-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-leg-Subman-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-ocp-prod/providers/Microsoft.Storage/storageAccounts/uscocpprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscocpprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-pgm-prod/providers/Microsoft.Sql/servers/usc-pgm-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-pgm-prod/providers/Microsoft.Sql/servers/usc-pgm-sql-prod/databases/Pgm_bosch-automotive_com', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Pgm_bosch-automotive_com', 'databases', 'centralus', 'GP_S_Gen5', 4.27),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-pgm-prod/providers/Microsoft.Storage/storageAccounts/uscpgmprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscpgmprod', 'storageaccounts', 'centralus', 'Standard_LRS', 0.08),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-s2c-prod/providers/Microsoft.Cache/Redis/usc-s2c-redis-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-s2c-redis-prod', 'redis', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-s2c-prod/providers/Microsoft.Storage/storageAccounts/uscs2cprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscs2cprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.47),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-s2c-prod/providers/Microsoft.Web/serverFarms/usc-s2c-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-s2c-fa1-w-prod', 'serverfarms', 'centralus', 'EP1', 83.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-s2c-prod/providers/Microsoft.Web/serverFarms/usc-s2c-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-s2c-svc1-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.51),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-s2c-prod/providers/Microsoft.Web/sites/usc-s2c-IotDevicesRSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-s2c-IotDevicesRSvc-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-s2c-prod/providers/Microsoft.Web/sites/usc-s2c-RegisterProduct-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-s2c-RegisterProduct-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Sql/servers/usc-sas-sql-prod/databases/Bosch_serviceaccel_com', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Bosch_serviceaccel_com', 'databases', 'centralus', 'GP_S_Gen5', 157.06),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Sql/servers/usc-sas-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Storage/storageAccounts/uscsasprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscsasprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.26),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Web/serverFarms/usc-sas-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-sas-svc1-w-prod', 'serverfarms', 'centralus', 'P1v3', 269.58),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Web/serverFarms/usc-sas-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-sas-ui1-w-prod', 'serverfarms', 'centralus', 'P1v3', 399.38),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Web/sites/usc-sas-CustomerPortal-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-sas-CustomerPortal-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Web/sites/usc-sas-SaPlatformRSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-sas-SaPlatformRSvc-prod', 'sites', 'centralus', 'N/A', 0.02),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-sas-prod/providers/Microsoft.Web/sites/usc-sas-Soap-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-sas-Soap-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-shared-prod/providers/Microsoft.Storage/storageAccounts/uscsharedprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscsharedprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.36),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Cache/Redis/usc-smi-redis-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-smi-redis-prod', 'redis', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Sql/servers/usc-smi-sql-prod/databases/Iam_Diagnostics', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Iam_Diagnostics', 'databases', 'centralus', 'GP_S_Gen5', 18.88),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Sql/servers/usc-smi-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Storage/storageAccounts/uscsmiprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscsmiprod', 'storageaccounts', 'centralus', 'Standard_LRS', 7.36),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Web/serverFarms/usc-smi-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-smi-svc1-w-prod', 'serverfarms', 'centralus', 'P0v3', 249.40),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Web/serverFarms/usc-smi-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-smi-ui1-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.51),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Web/sites/usc-smi-BoschSubscriptions-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-smi-BoschSubscriptions-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-smi-prod/providers/Microsoft.Web/sites/usc-smi-SubscriptionRSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-smi-SubscriptionRSvc-prod', 'sites', 'centralus', 'N/A', 0.09),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Sql/servers/usc-srs-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'System', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Sql/servers/usc-srs-sql-prod/databases/Srs20_Analytics', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Srs20_Analytics', 'databases', 'centralus', 'GP_S_Gen5', 31.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Sql/servers/usc-srs-sql-prod/databases/Srs20_Core', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'Srs20_Core', 'databases', 'centralus', 'HS_S_Gen5', 3340.85),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Storage/storageAccounts/uscsrscdnprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscsrscdnprod', 'storageaccounts', 'centralus', 'Standard_GRS', 7.21),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Storage/storageAccounts/uscsrsprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscsrsprod', 'storageaccounts', 'centralus', 'Standard_LRS', 33.34),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-adm1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-adm1-w-prod', 'serverfarms', 'centralus', 'P1v3', 120.54),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-fa1-w-prod', 'serverfarms', 'centralus', 'EP1', 83.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-solr3-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-solr3-w-prod', 'serverfarms', 'centralus', 'P3v3', 480.92),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-svc1-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-svc1-l-prod', 'serverfarms', 'centralus', 'P1v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-svc2-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-svc2-l-prod', 'serverfarms', 'centralus', 'P1v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-svc3-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-svc3-l-prod', 'serverfarms', 'centralus', 'P1v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-svc6-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-svc6-w-prod', 'serverfarms', 'centralus', 'P1v3', 128.11),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-svc7-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-svc7-w-prod', 'serverfarms', 'centralus', 'P1v3', 187.85),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-ui1-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-ui1-l-prod', 'serverfarms', 'centralus', 'P0v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-ui2-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-ui2-l-prod', 'serverfarms', 'centralus', 'P0v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-ui3-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-ui3-l-prod', 'serverfarms', 'centralus', 'P0v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-ui4-l-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-ui4-l-prod', 'serverfarms', 'centralus', 'P1v3', 6.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/serverFarms/usc-srs-ui5-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-ui5-w-prod', 'serverfarms', 'centralus', 'P1v3', 137.74),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-AllisonDoc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-AllisonDoc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-BackEndJobs-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-BackEndJobs-fa-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-BackEndJobsTmp-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-BackEndJobsTmp-fa-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-BoschTools-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-BoschTools-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-CAssist-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-CAssist-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-CAssistSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-CAssistSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-FordSpecialTools-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-FordSpecialTools-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-FordSpecialToolsTmp-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-FordSpecialToolsTmp-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-GmDeMx-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-GmDeMx-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-GmMdi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-GmMdi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-HarleyTools-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-HarleyTools-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-IsuzuTrucks-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-IsuzuTrucks-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-JdTipp-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-JdTipp-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-JlrEquipment-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-JlrEquipment-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MitsubishiCanada-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MitsubishiCanada-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlAuthApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlAuthApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlCartApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlCartApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlCatalogApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlCatalogApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlCheckoutApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlCheckoutApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlInitializeApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlInitializeApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlLocalizationApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlLocalizationApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlLoginApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlLoginApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlOrderHistoryApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlOrderHistoryApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlSubscriptionApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlSubscriptionApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-MlUtilityApi-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-MlUtilityApi-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-Parts-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-Parts-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-Robinair-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-Robinair-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-RobinairSupplies-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-RobinairSupplies-prod', 'sites', 'centralus', 'N/A', 0.00);
INSERT INTO `billing_resources` (`resource_id`, `subscription_id`, `name`, `resource_type`, `region`, `sku`, `cost_eur`) VALUES
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-Rotunda-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-Rotunda-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-Rpm-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-Rpm-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-ShippingSvcTmp-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-ShippingSvcTmp-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-Srs20Assets-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-Srs20Assets-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SrsCommerceSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SrsCommerceSvc-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SrsFrameworkSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SrsFrameworkSvc-prod', 'sites', 'centralus', 'N/A', 0.02),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SrsImplSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SrsImplSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SrsMlGatewaySvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SrsMlGatewaySvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SrsSolrSvc3-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SrsSolrSvc3-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SrsTiSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SrsTiSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SsoSimulator-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SsoSimulator-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-Subaru-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-Subaru-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-srs-prod/providers/Microsoft.Web/sites/usc-srs-SubaruCanada-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-srs-SubaruCanada-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tnbi-prod/providers/Microsoft.Storage/storageAccounts/usctnbiprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usctnbiprod', 'storageaccounts', 'centralus', 'Standard_GRS', 7.42),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Cache/Redis/usc-tns-redis-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-redis-prod', 'redis', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Storage/storageAccounts/usctnsprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usctnsprod', 'storageaccounts', 'centralus', 'Standard_LRS', 11.71),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/serverFarms/usc-tns-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-fa1-w-prod', 'serverfarms', 'centralus', 'EP1', 83.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/serverFarms/usc-tns-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-svc1-w-prod', 'serverfarms', 'centralus', 'P0v3', 95.06),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/serverFarms/usc-tns-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ui1-w-prod', 'serverfarms', 'centralus', 'P1v3', 134.06),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/serverFarms/usc-tns-ui2-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ui2-w-prod', 'serverfarms', 'centralus', 'P0v3', 65.60),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-Beta-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-Beta-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-FordToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-FordToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-JdToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-JdToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-JlrToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-JlrToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-NissanToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-NissanToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-OpelToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-OpelToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-RotundaToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-RotundaToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-SubaruToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-SubaruToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-ToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-ToolNetAssets-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ToolNetAssets-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-ToolNetBackgroundRSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ToolNetBackgroundRSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-ToolNetRSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ToolNetRSvc-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-ToyotaToolNet-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-ToyotaToolNet-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-tns-prod/providers/Microsoft.Web/sites/usc-tns-VwToolOrg-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-tns-VwToolOrg-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Sql/servers/usc-xms-sql-prod/databases/master', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'master', 'databases', 'centralus', 'GP_SYSTEM', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Sql/servers/usc-xms-sql-prod/databases/XmsCore', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'XmsCore', 'databases', 'centralus', 'GP_S_Gen5', 1090.50),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Sql/servers/usc-xms-sql-prod/databases/XmsReadinessCheck', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'XmsReadinessCheck', 'databases', 'centralus', 'GP_S_Gen5', 157.16),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Storage/storageAccounts/uscxmscdnprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscxmscdnprod', 'storageaccounts', 'centralus', 'Standard_GRS', 79.62),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Storage/storageAccounts/uscxmsprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uscxmsprod', 'storageaccounts', 'centralus', 'Standard_LRS', 11.76),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/serverFarms/usc-xms-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-fa1-w-prod', 'serverfarms', 'centralus', 'EP1', 83.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/serverFarms/usc-xms-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-svc1-w-prod', 'serverfarms', 'centralus', 'P1v3', 458.33),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/serverFarms/usc-xms-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-ui1-w-prod', 'serverfarms', 'centralus', 'P1v3', 128.58),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/sites/usc-xms-DatabaseReIndex-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-DatabaseReIndex-fa-prod', 'sites', 'centralus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/sites/usc-xms-PackageSyncSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-PackageSyncSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/sites/usc-xms-XmsCommandSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-XmsCommandSvc-prod', 'sites', 'centralus', 'N/A', 0.13),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/sites/usc-xms-XmsReadinessCheckSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-XmsReadinessCheckSvc-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/sites/usc-xms-XmsXpac-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-XmsXpac-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/usc-xms-prod/providers/Microsoft.Web/sites/usc-xms-XmsXwac-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usc-xms-XmsXwac-prod', 'sites', 'centralus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Storage/storageAccounts/usehdbsarchiveprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usehdbsarchiveprod', 'storageaccounts', 'eastus', 'Standard_LRS', 7.46),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Storage/storageAccounts/usehdbscdnprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usehdbscdnprod', 'storageaccounts', 'eastus', 'Standard_GRS', 8.72),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Storage/storageAccounts/usehdbsprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usehdbsprod', 'storageaccounts', 'eastus', 'Standard_ZRS', 7.82),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/serverFarms/use-hdbs-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-fa1-w-prod', 'serverfarms', 'eastus', 'EP1', 83.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/serverFarms/use-hdbs-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-svc1-w-prod', 'serverfarms', 'eastus', 'P0v3', 71.55),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/serverFarms/use-hdbs-svc2-wc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-svc2-wc-prod', 'serverfarms', 'eastus', 'P1v3', 119.62),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/serverFarms/use-hdbs-ui1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-ui1-w-prod', 'serverfarms', 'eastus', 'P0v3', 62.81),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/sites/use-hdbs-BoschSeedAndKey-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-BoschSeedAndKey-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/sites/use-hdbs-BusinessLayer-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-BusinessLayer-prod', 'sites', 'eastus', 'N/A', 0.02),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/sites/use-hdbs-HdAdminConsole-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-HdAdminConsole-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/sites/use-hdbs-HdPrivateFacadeSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-HdPrivateFacadeSvc-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/sites/use-hdbs-HdPublicFacadeSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-HdPublicFacadeSvc-prod', 'sites', 'eastus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-hdbs-prod/providers/Microsoft.Web/sites/use-hdbs-SeedAndKey-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-hdbs-SeedAndKey-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-keycloak-prod/providers/Microsoft.Storage/storageAccounts/usekeycloakprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'usekeycloakprod', 'storageaccounts', 'eastus', 'Standard_GRS', 11.92),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Storage/storageAccounts/uselsmprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'uselsmprod', 'storageaccounts', 'eastus', 'Standard_LRS', 11.03),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/serverFarms/use-lsm-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-fa1-w-prod', 'serverfarms', 'eastus', 'EP1', 83.99),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/serverFarms/use-lsm-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-svc1-w-prod', 'serverfarms', 'eastus', 'P0v3', 62.81),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/sites/use-lsm-LsmAuditSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-LsmAuditSvc-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/sites/use-lsm-LsmLicenseSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-LsmLicenseSvc-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/sites/use-lsm-LsmSubscriptionSvc-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-LsmSubscriptionSvc-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/sites/use-lsm-NotificationWorker-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-NotificationWorker-fa-prod', 'sites', 'eastus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-lsm-prod/providers/Microsoft.Web/sites/use-lsm-SubscriptionWorker-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-lsm-SubscriptionWorker-fa-prod', 'sites', 'eastus', 'N/A', 0.01),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Storage/storageAccounts/userdiprod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'userdiprod', 'storageaccounts', 'eastus', 'Standard_LRS', 31.26),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Web/serverFarms/use-rdi-adm1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-rdi-adm1-w-prod', 'serverfarms', 'eastus', 'P0v3', 62.81),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Web/serverFarms/use-rdi-fa1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-rdi-fa1-w-prod', 'serverfarms', 'eastus', 'EP1', 106.88),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Web/serverFarms/use-rdi-svc1-w-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-rdi-svc1-w-prod', 'serverfarms', 'eastus', 'P0v3', 63.19),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Web/sites/use-rdi-RemoteDiag-fa-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-rdi-RemoteDiag-fa-prod', 'sites', 'eastus', 'N/A', 0.02),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Web/sites/use-rdi-RemoteDiag-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-rdi-RemoteDiag-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/0e6dc52f-1c64-4d5a-9396-29e37fa41079/resourceGroups/use-rdi-prod/providers/Microsoft.Web/sites/use-rdi-RemoteDiagConsole-prod', '0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'use-rdi-RemoteDiagConsole-prod', 'sites', 'eastus', 'N/A', 0.00),
('/subscriptions/12db631f-5ab4-4237-9fa6-6d92de7a53e8/resourceGroups/pci-bcs1-inslens-app-rg/providers/Microsoft.Compute/virtualMachines/PC1-BCS1-INSLENS-APP-VM', '12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'PC1-BCS1-INSLENS-APP-VM', 'virtualmachines', 'centralindia', 'N/A', 190.79),
('/subscriptions/12db631f-5ab4-4237-9fa6-6d92de7a53e8/resourceGroups/PCI-BCS1-INSLENS-APP-RG/providers/Microsoft.Storage/storageAccounts/pc1bcs1inslenssqlbackup', '12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'pc1bcs1inslenssqlbackup', 'storageaccounts', 'centralindia', 'Standard_LRS', 2.58),
('/subscriptions/12db631f-5ab4-4237-9fa6-6d92de7a53e8/resourceGroups/PCI-BCS1-INSLENS-APP-RG/providers/Microsoft.Storage/storageAccounts/pcibcs1inslensapprgdiag', '12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'pcibcs1inslensapprgdiag', 'storageaccounts', 'centralindia', 'Standard_LRS', 16.94),
('/subscriptions/19fb36f7-0105-4d68-be29-02c3934a2abc/resourceGroups/cloud-printing-qa-rg01/providers/Microsoft.Compute/virtualMachines/Cloud-Printing-QA-App-VM01', '19fb36f7-0105-4d68-be29-02c3934a2abc', 'Cloud-Printing-QA-App-VM01', 'virtualmachines', 'westeurope', 'N/A', 8.35),
('/subscriptions/19fb36f7-0105-4d68-be29-02c3934a2abc/resourceGroups/CLOUD-PRINTING-QA-RG01/providers/Microsoft.Compute/virtualMachines/CLOUD-PRINTING-QA-BASTIONHOST', '19fb36f7-0105-4d68-be29-02c3934a2abc', 'Cloud-Printing-QA-BastionHost', 'virtualmachines', 'westeurope', 'N/A', 25.96),
('/subscriptions/19fb36f7-0105-4d68-be29-02c3934a2abc/resourceGroups/CLOUD-PRINTING-QA-RG01/providers/Microsoft.Compute/virtualMachines/CLOUD-PRINTING-QA-DB-VM01', '19fb36f7-0105-4d68-be29-02c3934a2abc', 'Cloud-Printing-QA-DB-VM01', 'virtualmachines', 'westeurope', 'N/A', 8.35),
('/subscriptions/19fb36f7-0105-4d68-be29-02c3934a2abc/resourceGroups/Cloud-Printing-QA-RG01/providers/Microsoft.Storage/storageAccounts/cloudprintingqastorage01', '19fb36f7-0105-4d68-be29-02c3934a2abc', 'cloudprintingqastorage01', 'storageaccounts', 'westeurope', 'Standard_LRS', 75.76),
('/subscriptions/19fb36f7-0105-4d68-be29-02c3934a2abc/resourceGroups/cloud-shell-storage-eastus/providers/Microsoft.Storage/storageAccounts/cs2100320019613777f', '19fb36f7-0105-4d68-be29-02c3934a2abc', 'cs2100320019613777f', 'storageaccounts', 'eastus', 'Standard_LRS', 7.31),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/AzureBackupRG_westeurope_1/providers/Microsoft.Storage/storageAccounts/extrareporting', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extrareporting', 'storageaccounts', 'westeurope', 'Standard_RAGRS', 7.17),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-africa-RG01/providers/Microsoft.Cache/Redis/extra-prod-rediscache02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-rediscache02', 'redis', 'southafricanorth', 'N/A', 73.93),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-africa-RG01/providers/Microsoft.Storage/storageAccounts/extramobileappbackup02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extramobileappbackup02', 'storageaccounts', 'southafricanorth', 'Standard_LRS', 7.16),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-africa-RG01/providers/Microsoft.Web/serverFarms/extra-prod-appserviceplan02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appserviceplan02', 'serverfarms', 'southafricanorth', 'P1v2', 89.73),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-africa-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-MobileApp-af', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-MobileApp-af', 'sites', 'southafricanorth', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-africa-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-MobileAppScheduler-af', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-MobileAppScheduler-af', 'sites', 'southafricanorth', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-eastasia-RG01/providers/Microsoft.Cache/Redis/extra-prod-rediscache05', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-rediscache05', 'redis', 'eastasia', 'N/A', 51.03),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-eastasia-RG01/providers/Microsoft.Web/serverFarms/extra-prod-appserviceplan05', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appserviceplan05', 'serverfarms', 'eastasia', 'P2v2', 97.66),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-eastasia-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-eastasia-mobileapp', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-eastasia-mobileapp', 'sites', 'eastasia', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-eastasia-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-eastasia-mobileappschedule', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-eastasia-mobileappschedule', 'sites', 'eastasia', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-eastasia-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-eastasia-warehouseapp', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-eastasia-warehouseapp', 'sites', 'eastasia', 'N/A', 0.31),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-eastus2-RG01/providers/Microsoft.Cache/Redis/extra-prod-rediscache01', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-rediscache01', 'redis', 'eastus2', 'N/A', 51.12),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-eastus2-RG01/providers/Microsoft.Storage/storageAccounts/extramobileappbackup', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extramobileappbackup', 'storageaccounts', 'eastus', 'Standard_LRS', 7.15),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-eastus2-RG01/providers/Microsoft.Web/serverFarms/extra-prod-appserviceplan', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appserviceplan', 'serverfarms', 'eastus2', 'P1v2', 44.04),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-eastus2-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-MobileApp', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-MobileApp', 'sites', 'eastus2', 'N/A', 0.28),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-prod-eastus2-rg01/providers/Microsoft.Web/sites/extra-prod-appservice-MobileAppScheduler', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-MobileAppScheduler', 'sites', 'eastus2', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-westeurope-RG01/providers/Microsoft.Cache/Redis/extra-prod-rediscache03', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-rediscache03', 'redis', 'westeurope', 'N/A', 51.12),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-westeurope-RG01/providers/Microsoft.Storage/storageAccounts/extramobileappbackup03', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extramobileappbackup03', 'storageaccounts', 'westeurope', 'Standard_LRS', 7.40),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-westeurope-RG01/providers/Microsoft.Web/serverFarms/extra-prod-appserviceplan03', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appserviceplan03', 'serverfarms', 'westeurope', 'P1v2', 48.86),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtramobile-Prod-westeurope-RG01/providers/Microsoft.Web/sites/extra-prod-appservice-MobileApp-eu', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-MobileApp-eu', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-prod-westeurope-rg01/providers/Microsoft.Web/sites/extra-prod-appservice-MobileAppScheduler-eu', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-MobileAppScheduler-eu', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-westeurope-RG02/providers/Microsoft.Cache/Redis/extra-prod-rediscache04', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-rediscache04', 'redis', 'westeurope', 'N/A', 51.12),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-westeurope-RG02/providers/Microsoft.Storage/storageAccounts/extratierlevel', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extratierlevel', 'storageaccounts', 'westeurope', 'Premium_LRS', 128.01),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-westeurope-RG02/providers/Microsoft.Web/serverFarms/extra-prod-appserviceplan04', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appserviceplan04', 'serverfarms', 'westeurope', 'P2v2', 91.56),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-westeurope-RG02/providers/Microsoft.Web/sites/extra-prod-appservice-engage', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-engage', 'sites', 'westeurope', 'N/A', 0.16),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-westeurope-RG02/providers/Microsoft.Web/sites/extra-prod-appservice-seheduler', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-seheduler', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extramobile-Prod-westeurope-RG02/providers/Microsoft.Web/sites/extra-prod-appservice-webserver', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extra-prod-appservice-webserver', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-emea-RG01/providers/Microsoft.Compute/virtualMachines/extraplatform-prod-DB01', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-DB01', 'virtualmachines', 'westeurope', 'N/A', 155.73),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-emea-RG01/providers/Microsoft.Compute/virtualMachines/extraplatform-prod-DB02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-DB02', 'virtualmachines', 'westeurope', 'N/A', 155.29),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extraplatform-prod-emea-rg01/providers/Microsoft.Compute/virtualMachines/extraplatform-prod-gw01', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-gw01', 'virtualmachines', 'westeurope', 'N/A', 7.34),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extraplatform-prod-emea-rg01/providers/Microsoft.Compute/virtualMachines/extraplatform-prod-sftp01', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-sftp01', 'virtualmachines', 'westeurope', 'N/A', 7.38),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extraplatform-prod-emea-rg01/providers/Microsoft.Compute/virtualMachines/extraplatform-prod-web01', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-web01', 'virtualmachines', 'westeurope', 'N/A', 9.26),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/extraplatform-prod-emea-rg01/providers/Microsoft.Compute/virtualMachines/extraplatform-prod-web02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-web02', 'virtualmachines', 'westeurope', 'N/A', 9.71),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-emea-RG01/providers/Microsoft.Storage/storageAccounts/extraitalybackup', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraitalybackup', 'storageaccounts', 'italynorth', 'Standard_LRS', 10.81),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-emea-RG01/providers/Microsoft.Storage/storageAccounts/extraplatformprodemearg0', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatformprodemearg0', 'storageaccounts', 'westeurope', 'Standard_LRS', 76.77),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-emea-RG01/providers/Microsoft.Storage/storageAccounts/extraplatformprodstg01', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatformprodstg01', 'storageaccounts', 'westeurope', 'Premium_LRS', 0.00),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-ETL-RG01/providers/Microsoft.Sql/servers/extraplatform-prod-sqldbserver01/databases/eXtraplatform-prod-sqldb-Reporting1', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'eXtraplatform-prod-sqldb-Reporting1', 'databases', 'westeurope', 'HS_S_Gen5', 313.52),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-ETL-RG01/providers/Microsoft.Sql/servers/extraplatform-prod-sqldbserver01/databases/eXtraplatform-prod-sqldb-stage1', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'eXtraplatform-prod-sqldb-stage1', 'databases', 'westeurope', 'HS_S_Gen5', 388.62),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-ETL-RG01/providers/Microsoft.Sql/servers/extraplatform-prod-sqldbserver01/databases/eXtraplatform-prod-sqldb-stage2', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'eXtraplatform-prod-sqldb-stage2', 'databases', 'westeurope', 'HS_S_Gen5', 306.82),
('/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourceGroups/eXtraPlatform-Prod-ETL-RG01/providers/Microsoft.Sql/servers/extraplatform-prod-sqldbserver01/databases/master', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'master', 'databases', 'westeurope', 'GP_SYSTEM', 0.00),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/ApnaBosch-QA-RG01/providers/Microsoft.Sql/servers/apnabosch-qa-sqlserver01/databases/ma-dxt-in-qa-db-apna-ci-01', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'ma-dxt-in-qa-db-apna-ci-01', 'databases', 'southindia', 'Standard', 15.48),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/ApnaBosch-QA-RG01/providers/Microsoft.Sql/servers/apnabosch-qa-sqlserver01/databases/master', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Sql/servers/cms-qa-sqlserver01/databases/cms-qa-sqldb01', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'cms-qa-sqldb01', 'databases', 'southindia', 'Standard', 38.57),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Sql/servers/cms-qa-sqlserver01/databases/master', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Storage/storageAccounts/cmsqapcdstorage', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'cmsqapcdstorage', 'storageaccounts', 'southindia', 'Standard_RAGRS', 2.78),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Storage/storageAccounts/cmsqastorageaccount001', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'cmsqastorageaccount001', 'storageaccounts', 'southindia', 'Standard_LRS', 2.89),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Storage/storageAccounts/finalbackupapnabosch2025', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'finalbackupapnabosch2025', 'storageaccounts', 'southindia', 'Standard_LRS', 2.43),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Web/serverFarms/CMS-QA-AppservicePlan01', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'CMS-QA-AppservicePlan01', 'serverfarms', 'southindia', 'S1', 43.07),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Web/sites/CMS-QA-WebAPI01', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'CMS-QA-WebAPI01', 'sites', 'southindia', 'N/A', 0.37),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/CMS-QA-RG01/providers/Microsoft.Web/sites/CMS-QA-Webapp01', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'CMS-QA-Webapp01', 'sites', 'southindia', 'N/A', 0.80),
('/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719/resourceGroups/cms-qa-rg01/providers/Microsoft.Web/sites/dbpaymentnotification1', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'dbpaymentnotification1', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Cache/Redis/ma-ci-hamrobosch-qa-redis01', '219ccbf6-e35b-4758-961a-ede690403d86', 'ma-ci-hamrobosch-qa-redis01', 'redis', 'centralindia', 'N/A', 8.17),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Sql/servers/ma-ci-hamrobosch-qa-sql01/databases/HamroBoschQA', '219ccbf6-e35b-4758-961a-ede690403d86', 'HamroBoschQA', 'databases', 'centralindia', 'Standard', 8.50),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Sql/servers/ma-ci-hamrobosch-qa-sql01/databases/master', '219ccbf6-e35b-4758-961a-ede690403d86', 'master', 'databases', 'centralindia', 'GP_SYSTEM', 0.00),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Storage/storageAccounts/qhamroboschstorage01', '219ccbf6-e35b-4758-961a-ede690403d86', 'qhamroboschstorage01', 'storageaccounts', 'centralindia', 'Standard_LRS', 7.16),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Storage/storageAccounts/qhbprivate', '219ccbf6-e35b-4758-961a-ede690403d86', 'qhbprivate', 'storageaccounts', 'centralindia', 'Standard_LRS', 7.16),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Storage/storageAccounts/qhbpublic', '219ccbf6-e35b-4758-961a-ede690403d86', 'qhbpublic', 'storageaccounts', 'centralindia', 'Standard_LRS', 7.16),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/MA-Hamro-Bosch-QA/providers/Microsoft.Web/serverFarms/ma-ci-hamrobosch-qa-asp01', '219ccbf6-e35b-4758-961a-ede690403d86', 'ma-ci-hamrobosch-qa-asp01', 'serverfarms', 'centralindia', 'S1', 46.85),
('/subscriptions/219ccbf6-e35b-4758-961a-ede690403d86/resourceGroups/ma-hamro-bosch-qa/providers/Microsoft.Web/sites/ma-ci-hamrobosch-qa-app01', '219ccbf6-e35b-4758-961a-ede690403d86', 'ma-ci-hamrobosch-qa-app01', 'sites', 'centralindia', 'N/A', 0.31),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/AUTOPARTS-PROD-WESTUS-RG-TFSTATE/providers/Microsoft.Storage/storageAccounts/autopartprodusstgtfstate', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autopartprodusstgtfstate', 'storageaccounts', 'westus2', 'Standard_LRS', 7.16),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-prod-westus-vm-elasticsearch0', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autoparts-prod-westus-vm-elasticsearch0', 'virtualmachines', 'westus2', 'N/A', 53.10),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-prod-westus-vm-elasticsearch1', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autoparts-prod-westus-vm-elasticsearch1', 'virtualmachines', 'westus2', 'N/A', 52.66),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-prod-westus-vm-elasticsearch2', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autoparts-prod-westus-vm-elasticsearch2', 'virtualmachines', 'westus2', 'N/A', 53.41),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-prod-westus-vm-liferay0', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autoparts-prod-westus-vm-liferay0', 'virtualmachines', 'westus2', 'N/A', 52.68),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-prod-westus-vm-liferay1', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autoparts-prod-westus-vm-liferay1', 'virtualmachines', 'westus2', 'N/A', 52.93),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartprodusstg01', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autopartprodusstg01', 'storageaccounts', 'westus2', 'Standard_LRS', 175.51),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartprodusstgdiag', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autopartprodusstgdiag', 'storageaccounts', 'westus2', 'Standard_LRS', 10.82),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartsprodlogs', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autopartsprodlogs', 'storageaccounts', 'westus2', 'Standard_LRS', 7.29),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartsstaticsite', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autopartsstaticsite', 'storageaccounts', 'westus2', 'Premium_ZRS', 7.16),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Storage/storageAccounts/boschautopartscdn', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'boschautopartscdn', 'storageaccounts', 'westus2', 'Standard_LRS', 8.66),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Storage/storageAccounts/sparkplugcomparisonprod', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'sparkplugcomparisonprod', 'storageaccounts', 'westus2', 'Standard_LRS', 7.16),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Web/serverFarms/ASP-autopartsprodwestusrg001-bfb6', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'ASP-autopartsprodwestusrg001-bfb6', 'serverfarms', 'westus2', 'Y1', 0.00),
('/subscriptions/2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b/resourceGroups/autoparts-prod-westus-rg001/providers/Microsoft.Web/sites/autoparts-prod-westus-fapp', '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'autoparts-prod-westus-fapp', 'sites', 'westus2', 'N/A', 0.00),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Cache/Redis/miconic-prd-redis-1', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-redis-1', 'redis', 'southindia', 'N/A', 51.12),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Compute/virtualMachines/miconi-prd-bas1', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconi-prd-bas1', 'virtualmachines', 'southindia', 'N/A', 0.00),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Sql/servers/miconic-prd-sqlsrv-1/databases/master', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Sql/servers/miconic-prd-sqlsrv-1/databases/miconic-prd-db-1', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-db-1', 'databases', 'southindia', 'Standard', 38.57),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Storage/storageAccounts/miconicprdrg1diag', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconicprdrg1diag', 'storageaccounts', 'southindia', 'Standard_LRS', 15.32),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Storage/storageAccounts/miconicprdstrac1', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconicprdstrac1', 'storageaccounts', 'southindia', 'Standard_LRS', 2.49),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Web/serverFarms/miconic-prd-asp-1', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-asp-1', 'serverfarms', 'southindia', 'S3', 154.39),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Web/sites/miconic-prd-wapp-1', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-wapp-1', 'sites', 'southindia', 'N/A', 1.12),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Web/sites/miconic-prd-wapp-2', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-wapp-2', 'sites', 'southindia', 'N/A', 1.14),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Web/sites/miconic-prd-wapp-3', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-wapp-3', 'sites', 'southindia', 'N/A', 1.21),
('/subscriptions/4172676b-8a9c-44e0-b2ab-727057b691b7/resourceGroups/miconi-prd-rg-1/providers/Microsoft.Web/sites/miconic-prd-wapp-4', '4172676b-8a9c-44e0-b2ab-727057b691b7', 'miconic-prd-wapp-4', 'sites', 'southindia', 'N/A', 1.03),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Cache/Redis/miconi-qa-redis-1', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-redis-1', 'redis', 'southindia', 'N/A', 51.12),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Compute/virtualMachines/miconi-qa-bas-1', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-bas-1', 'virtualmachines', 'southindia', 'N/A', 0.00),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Sql/servers/miconi-qa-sqlsrv-1/databases/master', '50d41c25-8668-4497-8925-766161f0cddb', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Sql/servers/miconi-qa-sqlsrv-1/databases/miconi-qa-db-1', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-db-1', 'databases', 'southindia', 'Standard', 38.57),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Storage/storageAccounts/miconiqarg1diag', '50d41c25-8668-4497-8925-766161f0cddb', 'miconiqarg1diag', 'storageaccounts', 'southindia', 'Standard_LRS', 6.96),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Storage/storageAccounts/miconiqastrac1', '50d41c25-8668-4497-8925-766161f0cddb', 'miconiqastrac1', 'storageaccounts', 'southindia', 'Standard_LRS', 0.14),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Web/serverFarms/miconi-qa-asp-1', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-asp-1', 'serverfarms', 'southindia', 'S3', 154.38),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Web/serverFarms/miconi-qa-asp-2', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-asp-2', 'serverfarms', 'centralus', 'F1', 0.00),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Web/sites/miconi-qa-wapp-1', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-wapp-1', 'sites', 'southindia', 'N/A', 0.41),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Web/sites/miconi-qa-wapp-2', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-wapp-2', 'sites', 'southindia', 'N/A', 0.40),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Web/sites/miconi-qa-wapp-3', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-wapp-3', 'sites', 'southindia', 'N/A', 0.41),
('/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourceGroups/miconi-qa-rg-1/providers/Microsoft.Web/sites/miconi-qa-wapp-4', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-wapp-4', 'sites', 'southindia', 'N/A', 0.38),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Cache/Redis/iBoschServiceQA', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceQA', 'redis', 'southeastasia', 'N/A', 51.03),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Sql/servers/boschrewardsqanew/databases/CVCashTest', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'CVCashTest', 'databases', 'southindia', 'ElasticPool', 0.00),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Sql/servers/boschrewardsqanew/databases/iBoschRewardsUAT', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschRewardsUAT', 'databases', 'southindia', 'ElasticPool', 0.00),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Sql/servers/boschrewardsqanew/databases/iBoschServiceDBUAT', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceDBUAT', 'databases', 'southindia', 'ElasticPool', 0.00),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Sql/servers/boschrewardsqanew/databases/LubricashTest', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'LubricashTest', 'databases', 'southindia', 'ElasticPool', 0.00),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Sql/servers/boschrewardsqanew/databases/master', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Storage/storageAccounts/iboschcouponstorage1qasi', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iboschcouponstorage1qasi', 'storageaccounts', 'southindia', 'Standard_RAGRS', 14.57),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Web/serverFarms/BoschRewards-QA-ASP01', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'BoschRewards-QA-ASP01', 'serverfarms', 'southindia', 'P2v2', 154.38),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Web/sites/ApnaBosch01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'ApnaBosch01SI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Web/sites/BoschCastrolAPITestQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'BoschCastrolAPITestQA01SI', 'sites', 'southindia', 'N/A', 0.27),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Web/sites/CVCashWCFServiceTestQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'CVCashWCFServiceTestQA01SI', 'sites', 'southindia', 'N/A', 0.31);
INSERT INTO `billing_resources` (`resource_id`, `subscription_id`, `name`, `resource_type`, `region`, `sku`, `cost_eur`) VALUES
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/BoschRewards-QA-RG/providers/Microsoft.Web/sites/DigitalOrder', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'DigitalOrder', 'sites', 'southindia', 'N/A', 0.27),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/BatteryIntegration', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'BatteryIntegration', 'sites', 'southindia', 'N/A', 0.35),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/BatteryIntegrationUAT', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'BatteryIntegrationUAT', 'sites', 'southindia', 'N/A', 0.36),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/BOSCHDemoWSMSERPQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'BOSCHDemoWSMSERPQA01SI', 'sites', 'southindia', 'N/A', 0.27),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/boschreawrdsapiqaSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'boschreawrdsapiqaSI', 'sites', 'southindia', 'N/A', 0.33),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/BoschTallyUAT', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'BoschTallyUAT', 'sites', 'southindia', 'N/A', 0.30),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/DynamicReportAPIQA', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'DynamicReportAPIQA', 'sites', 'southindia', 'N/A', 0.32),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/eFocusIntegrationQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'eFocusIntegrationQASI', 'sites', 'southindia', 'N/A', 0.32),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/eJCAPIQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'eJCAPIQASI', 'sites', 'southindia', 'N/A', 0.34),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/EJCBWSIntegrationSchedulerQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'EJCBWSIntegrationSchedulerQASI', 'sites', 'southindia', 'N/A', 0.33),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/FileDownloadTestingSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'FileDownloadTestingSI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iboschadvantageclubintegrationqasi', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iboschadvantageclubintegrationqasi', 'sites', 'southindia', 'N/A', 0.29),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschCouponRewardsMobileDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschCouponRewardsMobileDevSI', 'sites', 'southindia', 'N/A', 0.43),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschGYFTrIntegrationQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschGYFTrIntegrationQASI', 'sites', 'southindia', 'N/A', 0.29),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschReawrdsAPIDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschReawrdsAPIDevSI', 'sites', 'southindia', 'N/A', 0.33),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iboschreawrdsapiqaSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iboschreawrdsapiqaSI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschRewardsBankAPINotifyQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschRewardsBankAPINotifyQA01SI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschRewardsBankAPIQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschRewardsBankAPIQA01SI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschRewardsDemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschRewardsDemoQA01SI', 'sites', 'southindia', 'N/A', 1.04),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschRewardsDemoWCFQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschRewardsDemoWCFQA01SI', 'sites', 'southindia', 'N/A', 0.37),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iboschrewardstestingQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iboschrewardstestingQA01SI', 'sites', 'southindia', 'N/A', 0.39),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschRewardsWCFDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschRewardsWCFDevSI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServiceAMCAPIPenTestSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceAMCAPIPenTestSI', 'sites', 'southindia', 'N/A', 0.27),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServiceAPIDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceAPIDevSI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServiceAPIQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceAPIQASI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServiceDemoSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceDemoSI', 'sites', 'southindia', 'N/A', 0.71),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServicePenTestSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServicePenTestSI', 'sites', 'southindia', 'N/A', 0.63),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServiceWCFDemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceWCFDemoQA01SI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschServiceWCFDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschServiceWCFDevSI', 'sites', 'southindia', 'N/A', 0.34),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschUKUServiceQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschUKUServiceQASI', 'sites', 'southindia', 'N/A', 0.34),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschUtilityAPIDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschUtilityAPIDevSI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/iBoschUtilityWCFDevSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'iBoschUtilityWCFDevSI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/InfocommExportAPISI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'InfocommExportAPISI', 'sites', 'southindia', 'N/A', 0.33),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/InfocommExportAPISIQA', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'InfocommExportAPISIQA', 'sites', 'southindia', 'N/A', 0.32),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/LubricashWCFServiceTestQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'LubricashWCFServiceTestQA01SI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/QAIBoschRewardsReportSI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'QAIBoschRewardsReportSI', 'sites', 'southindia', 'N/A', 0.80),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/SocketAPIQA', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'SocketAPIQA', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/SocketAppQA', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'SocketAppQA', 'sites', 'southindia', 'N/A', 0.27),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/SSOiBoschRewardsQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'SSOiBoschRewardsQA01SI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/SSOiBoschServicePortalQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'SSOiBoschServicePortalQA01SI', 'sites', 'southindia', 'N/A', 0.63),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/SSOiBoschServicePortalReportsQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'SSOiBoschServicePortalReportsQA01SI', 'sites', 'southindia', 'N/A', 0.88),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/SSOiBoschServicePortalWCFQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'SSOiBoschServicePortalWCFQA01SI', 'sites', 'southindia', 'N/A', 0.35),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/WCFBoschAMCDemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'WCFBoschAMCDemoQA01SI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/WCFBoschAMCMobileDemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'WCFBoschAMCMobileDemoQA01SI', 'sites', 'southindia', 'N/A', 0.29),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/wcfBoschMobileQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'wcfBoschMobileQA01SI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/wcfcouponrewardsmobiledemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'wcfcouponrewardsmobiledemoQA01SI', 'sites', 'southindia', 'N/A', 0.28),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/WCFPromotionDemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'WCFPromotionDemoQA01SI', 'sites', 'southindia', 'N/A', 0.32),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/wcfrestfulcouponrewardsQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'wcfrestfulcouponrewardsQA01SI', 'sites', 'southindia', 'N/A', 0.31),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/WCFTargetPlannerDemoQA01SI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'WCFTargetPlannerDemoQA01SI', 'sites', 'southindia', 'N/A', 0.30),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/WCFUKUServiceQASI', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'WCFUKUServiceQASI', 'sites', 'southindia', 'N/A', 0.38),
('/subscriptions/5784d84d-05ea-4a9c-b625-7d0183e9240b/resourceGroups/iBoschRewards-QA-SI/providers/Microsoft.Web/sites/womAPIServiceUAT', '5784d84d-05ea-4a9c-b625-7d0183e9240b', 'womAPIServiceUAT', 'sites', 'southindia', 'N/A', 0.29),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/AUTOPARTS-DEV-WESTUS-RG-TFSTATE/providers/Microsoft.Storage/storageAccounts/autopartdevusstgtfstate', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autopartdevusstgtfstate', 'storageaccounts', 'westus2', 'Standard_RAGRS', 6.42),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/AUTOPARTS-DEV-WESTUS-RG001/providers/Microsoft.Compute/virtualMachines/AUTOPARTS-DEV-WESTUS-VM-ANSIBLE01', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autoparts-dev-westus-vm-ansible01', 'virtualmachines', 'westus2', 'N/A', 18.46),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/AUTOPARTS-DEV-WESTUS-RG001/providers/Microsoft.Compute/virtualMachines/AUTOPARTS-DEV-WESTUS-VM-ELASTICSEARCH01', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autoparts-dev-westus-vm-elasticsearch01', 'virtualmachines', 'westus2', 'N/A', 18.34),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/autoparts-dev-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-dev-westus-vm-liferay01', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autoparts-dev-westus-vm-liferay01', 'virtualmachines', 'westus2', 'N/A', 28.94),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/autoparts-dev-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartdevusstg01', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autopartdevusstg01', 'storageaccounts', 'westus2', 'Standard_LRS', 26.22),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/autoparts-dev-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartdevusstgdiag', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autopartdevusstgdiag', 'storageaccounts', 'westus2', 'Standard_LRS', 7.17),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/autoparts-dev-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartsmaintenancestat', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'autopartsmaintenancestat', 'storageaccounts', 'westus2', 'Standard_LRS', 6.43),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/autoparts-dev-westus-rg001/providers/Microsoft.Storage/storageAccounts/liferaydxppackages', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'liferaydxppackages', 'storageaccounts', 'westus2', 'Standard_LRS', 6.43),
('/subscriptions/5b46ccc6-604b-4c5c-81c2-96b133061773/resourceGroups/cloud-shell-storage-eastus/providers/Microsoft.Storage/storageAccounts/cs2100320011cdb3731', '5b46ccc6-604b-4c5c-81c2-96b133061773', 'cs2100320011cdb3731', 'storageaccounts', 'eastus', 'Standard_LRS', 6.58),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/AGENTICAI-FINOPS-POC/providers/Microsoft.Compute/virtualMachines/AgenticAI-FinOps', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'AgenticAI-FinOps', 'virtualmachines', 'southindia', 'N/A', 9.62),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/asset360-rg-qa/providers/Microsoft.Web/serverFarms/asset360-qa-api-app-plan', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'asset360-qa-api-app-plan', 'serverfarms', 'westeurope', 'B3', 113.66),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/asset360-rg-qa/providers/Microsoft.Web/serverFarms/asset360ui-qa-appplan', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'asset360ui-qa-appplan', 'serverfarms', 'canadacentral', 'B1', 9.41),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/asset360-rg-qa/providers/Microsoft.Web/sites/asset360-qa-api', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'asset360-qa-api', 'sites', 'westeurope', 'N/A', 0.00),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/EAP-SANDBOX-VM-RG/providers/Microsoft.Compute/virtualMachines/EAPSandboxSSLVM', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'EAPSandboxSSLVM', 'virtualmachines', 'centralindia', 'N/A', 0.02),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/EAP-Sandbox-VM-RG/providers/Microsoft.Storage/storageAccounts/vmimage123', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'vmimage123', 'storageaccounts', 'centralindia', 'Standard_LRS', 2.35),
('/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531/resourceGroups/eap-skyfleet-common/providers/Microsoft.Storage/storageAccounts/eapcommonstaccount01', '6c389f01-77d0-4c2c-b846-8dfa36987531', 'eapcommonstaccount01', 'storageaccounts', 'eastus', 'Standard_LRS', 2.24),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-images/providers/Microsoft.Storage/storageAccounts/efocusvhdimages', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocusvhdimages', 'storageaccounts', 'centralindia', 'Standard_LRS', 3.09),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-qty-compute-rg02/providers/Microsoft.Compute/virtualMachines/EFOCUSQNAS05', '7498780e-1785-465f-9d50-c8ac1e929376', 'EFOCUSQNAS05', 'virtualmachines', 'centralindia', 'N/A', 0.80),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-qty-compute-rg02/providers/Microsoft.Compute/virtualMachines/EFOCUSQNAS06', '7498780e-1785-465f-9d50-c8ac1e929376', 'EFOCUSQNAS06', 'virtualmachines', 'centralindia', 'N/A', 1.70),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-qty-compute-rg02/providers/Microsoft.Compute/virtualMachines/EFOCUSQRDS02', '7498780e-1785-465f-9d50-c8ac1e929376', 'EFOCUSQRDS02', 'virtualmachines', 'centralindia', 'N/A', 1.71),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-qty-compute-rg02/providers/Microsoft.Compute/virtualMachines/EFOCUSQSQL02', '7498780e-1785-465f-9d50-c8ac1e929376', 'EFOCUSQSQL02', 'virtualmachines', 'centralindia', 'N/A', 3.23),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-Qty-compute-rg02/providers/Microsoft.Storage/storageAccounts/efocusqtyfileshare02', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocusqtyfileshare02', 'storageaccounts', 'centralindia', 'Standard_LRS', 93.76),
('/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourceGroups/efocus-Qty-compute-rg02/providers/Microsoft.Storage/storageAccounts/efocusqtylogstgaccount01', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocusqtylogstgaccount01', 'storageaccounts', 'centralindia', 'Standard_LRS', 26.77),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Cache/Redis/extra-dev-rediscache1', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extra-dev-rediscache1', 'redis', 'westeurope', 'N/A', 16.46),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Storage/storageAccounts/extradevmobileapp', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extradevmobileapp', 'storageaccounts', 'westeurope', 'Standard_LRS', 2.32),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Web/serverFarms/extra-dev-Appserviceplan', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extra-dev-Appserviceplan', 'serverfarms', 'westeurope', 'P1v2', 33.34),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Web/sites/extra-dev-engage', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extra-dev-engage', 'sites', 'westeurope', 'N/A', 0.18),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Web/sites/extra-dev-mobileapp', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extra-dev-mobileapp', 'sites', 'westeurope', 'N/A', 0.19),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Web/sites/extra-dev-tierlevel', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extra-dev-tierlevel', 'sites', 'westeurope', 'N/A', 0.17),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraMobileplatform-dev-rg01/providers/Microsoft.Web/sites/extra-dev-warehouseapp', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extra-dev-warehouseapp', 'sites', 'westeurope', 'N/A', 0.17),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraplatform-dev-rg01/providers/Microsoft.Compute/virtualMachines/extraplatform-dev-db01', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extraplatform-dev-db01', 'virtualmachines', 'westeurope', 'N/A', 8.03),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraplatform-dev-rg01/providers/Microsoft.Compute/virtualMachines/extraplatform-dev-web01', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extraplatform-dev-web01', 'virtualmachines', 'westeurope', 'N/A', 8.05),
('/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a/resourceGroups/extraplatform-dev-rg01/providers/Microsoft.Storage/storageAccounts/extraplatformdevrg01diag', '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'extraplatformdevrg01diag', 'storageaccounts', 'westeurope', 'Standard_LRS', 26.95),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Compute/virtualMachines/AA-Prod-DB-VM01', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'AA-Prod-DB-VM01', 'virtualmachines', 'southindia', 'N/A', 154.66),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Sql/servers/aapremrsvm01/databases/aapremrsvm01', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aapremrsvm01', 'databases', 'southindia', 'Premium', 591.31),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Sql/servers/aapremrsvm01/databases/master', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Sql/servers/aaqavm01/databases/aapremrsvm01_Copy', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aapremrsvm01_Copy', 'databases', 'southindia', 'Standard', 110.17),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Sql/servers/aaqavm01/databases/AAQADB01', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'AAQADB01', 'databases', 'southindia', 'Standard', 77.39),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Sql/servers/aaqavm01/databases/archiveDB', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'archiveDB', 'databases', 'southindia', 'GP_S_Gen5', 3.06),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Sql/servers/aaqavm01/databases/master', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/aadashboardmetricbeat', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aadashboardmetricbeat', 'storageaccounts', 'southindia', 'Standard_LRS', 3.15),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/aadiagstor', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aadiagstor', 'storageaccounts', 'southindia', 'Standard_LRS', 7.18),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/aarg01diag171', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aarg01diag171', 'storageaccounts', 'southindia', 'Standard_LRS', 22.43),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/aastorageaccount01', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aastorageaccount01', 'storageaccounts', 'southindia', 'Standard_RAGRS', 36.12),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/aastorageaccount01backup', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aastorageaccount01backup', 'storageaccounts', 'southindia', 'Standard_RAGRS', 4.15),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/mabdoaistorageaccount', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'mabdoaistorageaccount', 'storageaccounts', 'southindia', 'Standard_RAGRS', 7.18),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Storage/storageAccounts/sqldbvulasst', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'sqldbvulasst', 'storageaccounts', 'southindia', 'Standard_LRS', 7.16),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/serverFarms/ASP-AARG01-9ede', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'ASP-AARG01-9ede', 'serverfarms', 'southindia', 'Y1', 0.00),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/serverFarms/ASP-AARG01-a66c', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'ASP-AARG01-a66c', 'serverfarms', 'eastus', 'B1', 12.86),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/serverFarms/ASP-EXESALES-DEV', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'ASP-EXESALES-DEV', 'serverfarms', 'eastus', 'B1', 8.31),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/DashboardFunction01', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'DashboardFunction01', 'sites', 'southindia', 'N/A', 0.27),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/execsalesfrontdev', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'execsalesfrontdev', 'sites', 'eastus', 'N/A', 0.31),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/exesalesdev', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'exesalesdev', 'sites', 'eastus', 'N/A', 0.32),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/MABDO', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'MABDO', 'sites', 'eastus', 'N/A', 0.34),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/mabdo-ai-assistant-ui', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'mabdo-ai-assistant-ui', 'sites', 'eastus', 'N/A', 0.29),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/rbin-ai-platform-dev-api', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'rbin-ai-platform-dev-api', 'sites', 'eastus', 'N/A', 0.26),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/AA_RG01/providers/Microsoft.Web/sites/rbin-ai-platform-dev-web', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'rbin-ai-platform-dev-web', 'sites', 'eastus', 'N/A', 0.22),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/email_alerts/providers/Microsoft.Storage/storageAccounts/emailalertsb998', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'emailalertsb998', 'storageaccounts', 'canadacentral', 'Standard_LRS', 7.90),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/email_alerts/providers/Microsoft.Web/serverFarms/ASP-emailalerts-aa80', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'ASP-emailalerts-aa80', 'serverfarms', 'canadacentral', 'WS1', 97.88),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/email_alerts/providers/Microsoft.Web/sites/emailAlerts1', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'emailAlerts1', 'sites', 'canadacentral', 'N/A', 0.01),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/powershell-azure-function-helloworlde30b/providers/Microsoft.Web/serverFarms/EastUSPlan', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'EastUSPlan', 'serverfarms', 'eastus', 'Y1', 0.00),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/powershell-azure-function-helloworlde30b/providers/Microsoft.Web/sites/powershell-azure-function-helloworlde30b', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'powershell-azure-function-helloworlde30b', 'sites', 'eastus', 'N/A', 0.27),
('/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourceGroups/securitydata/providers/Microsoft.Storage/storageAccounts/572313southindia', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', '572313southindia', 'storageaccounts', 'southindia', 'Standard_LRS', 7.93),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Cache/Redis/ma-ci-hamrobosch-prod-redis01', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'ma-ci-hamrobosch-prod-redis01', 'redis', 'centralindia', 'N/A', 8.17),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Sql/servers/ma-ci-hamrobosch-prod-sql01/databases/HamroBoschOld', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'HamroBoschOld', 'databases', 'centralindia', 'ElasticPool', 0.00),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Sql/servers/ma-ci-hamrobosch-prod-sql01/databases/HamroBoschProd', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'HamroBoschProd', 'databases', 'centralindia', 'ElasticPool', 0.00),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Sql/servers/ma-ci-hamrobosch-prod-sql01/databases/master', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'master', 'databases', 'centralindia', 'GP_SYSTEM', 0.00),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Storage/storageAccounts/prodhamroboschstorage01', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'prodhamroboschstorage01', 'storageaccounts', 'centralindia', 'Standard_LRS', 8.80),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Storage/storageAccounts/prodhbprivate', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'prodhbprivate', 'storageaccounts', 'centralindia', 'Standard_LRS', 7.17),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Storage/storageAccounts/prodhbpublic', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'prodhbpublic', 'storageaccounts', 'centralindia', 'Standard_LRS', 7.17),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Web/serverFarms/ma-ci-hamrobosch-prod-asp01', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'ma-ci-hamrobosch-prod-asp01', 'serverfarms', 'centralindia', 'S1', 46.84),
('/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd/resourceGroups/MA-Hamro-Bosch-Prod/providers/Microsoft.Web/sites/ma-ci-hamrobosch-prod-app01', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'ma-ci-hamrobosch-prod-app01', 'sites', 'centralindia', 'N/A', 0.31),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/AUTOPARTS-QA-WESTUS-RG-TFSTATE/providers/Microsoft.Storage/storageAccounts/autopartqausstgtfstate', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autopartqausstgtfstate', 'storageaccounts', 'westus2', 'Standard_LRS', 7.16),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/AUTOPARTS-QA-WESTUS-RG-TFSTATE/providers/Microsoft.Storage/storageAccounts/autopartsqalogs1', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autopartsqalogs1', 'storageaccounts', 'westus2', 'Standard_LRS', 7.18),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-qa-westus-vm-elasticsearch0', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autoparts-qa-westus-vm-elasticsearch0', 'virtualmachines', 'westus2', 'N/A', 30.62),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-qa-westus-vm-elasticsearch1', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autoparts-qa-westus-vm-elasticsearch1', 'virtualmachines', 'westus2', 'N/A', 30.87),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-qa-westus-vm-elasticsearch2', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autoparts-qa-westus-vm-elasticsearch2', 'virtualmachines', 'westus2', 'N/A', 30.99),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-qa-westus-vm-liferay0', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autoparts-qa-westus-vm-liferay0', 'virtualmachines', 'westus2', 'N/A', 31.11),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Compute/virtualMachines/autoparts-qa-westus-vm-liferay1', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autoparts-qa-westus-vm-liferay1', 'virtualmachines', 'westus2', 'N/A', 30.71),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartqausstg03', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autopartqausstg03', 'storageaccounts', 'westus2', 'Standard_LRS', 534.54),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartqausstgcdntest', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autopartqausstgcdntest', 'storageaccounts', 'westus2', 'Standard_LRS', 10.89),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartqausstgdiag', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autopartqausstgdiag', 'storageaccounts', 'westus2', 'Standard_LRS', 7.25),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Storage/storageAccounts/autopartqausstgdocker', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'autopartqausstgdocker', 'storageaccounts', 'westus2', 'Standard_LRS', 14.16),
('/subscriptions/a77eebf3-17a1-4378-90bf-2c96b1028137/resourceGroups/autoparts-qa-westus-rg001/providers/Microsoft.Storage/storageAccounts/sparkplugcomparisonqa', 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'sparkplugcomparisonqa', 'storageaccounts', 'westus2', 'Standard_LRS', 7.16),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-ETL-RG01/providers/Microsoft.Sql/servers/extra-qa-sqldbserver01/databases/extra-qa-sqldb-Reporting1', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-sqldb-Reporting1', 'databases', 'westeurope', 'HS_S_Gen5', 307.41),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-ETL-RG01/providers/Microsoft.Sql/servers/extra-qa-sqldbserver01/databases/extra-qa-sqldb-stage1', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-sqldb-stage1', 'databases', 'westeurope', 'HS_S_Gen5', 329.39),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-ETL-RG01/providers/Microsoft.Sql/servers/extra-qa-sqldbserver01/databases/extra-qa-sqldb-stage2', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-sqldb-stage2', 'databases', 'westeurope', 'HS_S_Gen5', 307.41),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-ETL-RG01/providers/Microsoft.Sql/servers/extra-qa-sqldbserver01/databases/master', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'master', 'databases', 'westeurope', 'GP_SYSTEM', 0.00),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg01/providers/Microsoft.Compute/virtualMachines/extra-qa-db01', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-db01', 'virtualmachines', 'westeurope', 'N/A', 156.69),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg01/providers/Microsoft.Compute/virtualMachines/extra-qa-db02', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-db02', 'virtualmachines', 'westeurope', 'N/A', 156.83),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/EXTRA-QA-RG01/providers/Microsoft.Compute/virtualMachines/extra-QA-GW01', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-QA-GW01', 'virtualmachines', 'westeurope', 'N/A', 8.40),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg01/providers/Microsoft.Compute/virtualMachines/extra-qa-sftp01', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-sftp01', 'virtualmachines', 'westeurope', 'N/A', 22.65),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg01/providers/Microsoft.Compute/virtualMachines/extra-qa-web001', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-web001', 'virtualmachines', 'westeurope', 'N/A', 8.39),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG01/providers/Microsoft.Storage/storageAccounts/extraqaftpstorage', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extraqaftpstorage', 'storageaccounts', 'westeurope', 'Standard_RAGRS', 7.16),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG01/providers/Microsoft.Storage/storageAccounts/extraqarg01diag', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extraqarg01diag', 'storageaccounts', 'westeurope', 'Standard_LRS', 54.45),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG01/providers/Microsoft.Storage/storageAccounts/extraqarg01diag912', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extraqarg01diag912', 'storageaccounts', 'westeurope', 'Standard_LRS', 8.16),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/EXTRA-QA-RG01/providers/Microsoft.Storage/storageAccounts/extraqarg01perfdiag993', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extraqarg01perfdiag993', 'storageaccounts', 'westeurope', 'Standard_LRS', 7.15),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Cache/Redis/extra-qa-rediscache01', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-rediscache01', 'redis', 'westeurope', 'N/A', 51.12),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Storage/storageAccounts/extraqaappbackup', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extraqaappbackup', 'storageaccounts', 'westeurope', 'Standard_LRS', 9.46),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Storage/storageAccounts/extraqatierlevel', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extraqatierlevel', 'storageaccounts', 'westeurope', 'Standard_LRS', 30.28),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/serverFarms/extra-qa-Appserviceplan', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-Appserviceplan', 'serverfarms', 'westeurope', 'P1v2', 48.92),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/serverFarms/extra-qa-Appserviceplan-pentest', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-Appserviceplan-pentest', 'serverfarms', 'westeurope', 'P1v2', 48.92),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/sites/extra-qa-appservice-engage', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-engage', 'sites', 'westeurope', 'N/A', 0.18),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/sites/extra-qa-appservice-MobileApp', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-MobileApp', 'sites', 'westeurope', 'N/A', 0.29),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/sites/extra-qa-appservice-MobileApp-pentest', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-MobileApp-pentest', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/sites/extra-qa-appservice-MobileAppScheduler', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-MobileAppScheduler', 'sites', 'westeurope', 'N/A', 0.00),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg02/providers/Microsoft.Web/sites/extra-qa-appservice-TierLevel', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-TierLevel', 'sites', 'westeurope', 'N/A', 0.47),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/eXtra-QA-RG02/providers/Microsoft.Web/sites/extra-qa-appservice-TierLevel-pentest', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-TierLevel-pentest', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg02/providers/Microsoft.Web/sites/extra-qa-appservice-TierLevelScheduler', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-TierLevelScheduler', 'sites', 'westeurope', 'N/A', 0.00),
('/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourceGroups/extra-qa-rg02/providers/Microsoft.Web/sites/extra-qa-appservice-WarehouseApp', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-appservice-WarehouseApp', 'sites', 'westeurope', 'N/A', 0.27),
('/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourceGroups/Exim-Portal-PRD-App/providers/Microsoft.Web/serverFarms/exim-history-portal-prd-asp-01', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'exim-history-portal-prd-asp-01', 'serverfarms', 'southindia', 'S2', 80.03),
('/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourceGroups/Exim-Portal-PRD-App/providers/Microsoft.Web/sites/exim-history-portal-prd-wapp-01', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'exim-history-portal-prd-wapp-01', 'sites', 'southindia', 'N/A', 0.46),
('/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourceGroups/Exim-Portal-PRD-DB/providers/Microsoft.Sql/servers/eximportalprdsqlsrv01/databases/eximportalprdsqldb01', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'eximportalprdsqldb01', 'databases', 'southindia', 'HS_Gen5', 179.73),
('/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourceGroups/Exim-Portal-PRD-DB/providers/Microsoft.Sql/servers/eximportalprdsqlsrv01/databases/master', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourceGroups/Exim-Portal-PRD-DMZ/providers/Microsoft.Storage/storageAccounts/eximportalprdstrac01', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'eximportalprdstrac01', 'storageaccounts', 'southindia', 'Standard_GRS', 142.28),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-Asr-backupvault-rg12/providers/Microsoft.Storage/storageAccounts/i97dr7efocusasrasrcache', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'i97dr7efocusasrasrcache', 'storageaccounts', 'centralindia', 'Standard_LRS', 2.19),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-asr-compute-rg04/providers/Microsoft.Storage/storageAccounts/efocusasrlogstgaccount01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'efocusasrlogstgaccount01', 'storageaccounts', 'southindia', 'Standard_LRS', 7.15),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-bic-rg09/providers/Microsoft.Compute/virtualMachines/EFOCUSPBIC01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPBIC01', 'virtualmachines', 'centralindia', 'N/A', 0.00),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-compute-rg03/providers/Microsoft.Storage/storageAccounts/efocusfilerepo', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'efocusfilerepo', 'storageaccounts', 'centralindia', 'Standard_LRS', 8.03),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-compute-rg03/providers/Microsoft.Storage/storageAccounts/efocusmasterdata', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'efocusmasterdata', 'storageaccounts', 'centralindia', 'Standard_LRS', 7.62),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-compute-rg03/providers/Microsoft.Storage/storageAccounts/efocusprodlogtgaccount01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'efocusprodlogtgaccount01', 'storageaccounts', 'centralindia', 'Standard_LRS', 114.63),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-dc-rg04/providers/Microsoft.Compute/virtualMachines/EFOCUSPDC01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPDC01', 'virtualmachines', 'centralindia', 'N/A', 5.05),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-dc-rg04/providers/Microsoft.Compute/virtualMachines/EFOCUSPDC02', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPDC02', 'virtualmachines', 'centralindia', 'N/A', 0.05),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-dc-rg04/providers/Microsoft.Compute/virtualMachines/EFOCUSPDC03', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPDC03', 'virtualmachines', 'centralindia', 'N/A', 4.99),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-nav-rg07/providers/Microsoft.Compute/virtualMachines/EFOCUSPNAS01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPNAS01', 'virtualmachines', 'centralindia', 'N/A', 5.20),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-nav-rg07/providers/Microsoft.Compute/virtualMachines/EFOCUSPNAS02', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPNAS02', 'virtualmachines', 'centralindia', 'N/A', 4.70),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-nav-rg07/providers/Microsoft.Compute/virtualMachines/EFOCUSPNAS03', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPNAS03', 'virtualmachines', 'centralindia', 'N/A', 5.67),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-nav-rg07/providers/Microsoft.Compute/virtualMachines/EFOCUSPNAS04', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPNAS04', 'virtualmachines', 'centralindia', 'N/A', 4.70),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-rds-rg08/providers/Microsoft.Compute/virtualMachines/EFOCUSPRDS01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPRDS01', 'virtualmachines', 'centralindia', 'N/A', 5.27),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-rds-rg08/providers/Microsoft.Compute/virtualMachines/EFOCUSPRDS02', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPRDS02', 'virtualmachines', 'centralindia', 'N/A', 5.02),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-rds-rg08/providers/Microsoft.Compute/virtualMachines/EFOCUSPRDS03', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPRDS03', 'virtualmachines', 'centralindia', 'N/A', 0.00),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocus-prod-sql-rg06/providers/Microsoft.Compute/virtualMachines/EFOCUSPSQL03', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'EFOCUSPSQL03', 'virtualmachines', 'centralindia', 'N/A', 555.77),
('/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5/resourceGroups/efocusprodbackup01-Migrated/providers/Microsoft.Storage/storageAccounts/efocusprodbackup01', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'efocusprodbackup01', 'storageaccounts', 'centralindia', 'Standard_LRS', 273.03),
('/subscriptions/d26dd531-2174-4c45-a240-032e0d05dfa0/resourceGroups/cloudprinting-prod-rg01/providers/Microsoft.Compute/virtualMachines/Cloud-Printing-Prod-App-VM01', 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'Cloud-Printing-Prod-App-VM01', 'virtualmachines', 'westeurope', 'N/A', 10.68),
('/subscriptions/d26dd531-2174-4c45-a240-032e0d05dfa0/resourceGroups/cloudprinting-prod-rg01/providers/Microsoft.Compute/virtualMachines/Cloud-Printing-Prod-BH-VM01', 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'Cloud-Printing-Prod-BH-VM01', 'virtualmachines', 'westeurope', 'N/A', 25.88),
('/subscriptions/d26dd531-2174-4c45-a240-032e0d05dfa0/resourceGroups/cloudprinting-prod-rg01/providers/Microsoft.Compute/virtualMachines/Cloud-Printing-Prod-DB-VM01', 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'Cloud-Printing-Prod-DB-VM01', 'virtualmachines', 'westeurope', 'N/A', 359.26),
('/subscriptions/d26dd531-2174-4c45-a240-032e0d05dfa0/resourceGroups/CloudPrinting-Prod-RG01/providers/Microsoft.Storage/storageAccounts/cloudprintingprodstorage', 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'cloudprintingprodstorage', 'storageaccounts', 'westeurope', 'Standard_GRS', 27.48),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Cache/Redis/iBoschService', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschService', 'redis', 'southeastasia', 'N/A', 51.03),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/CVCash', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'CVCash', 'databases', 'southeastasia', 'ElasticPool', 0.01),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/iBoschRewards', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewards', 'databases', 'southeastasia', 'ElasticPool', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/iBoschRewardsDump', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewardsDump', 'databases', 'southeastasia', 'ElasticPool', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/iBoschServiceDB', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschServiceDB', 'databases', 'southeastasia', 'ElasticPool', 0.00);
INSERT INTO `billing_resources` (`resource_id`, `subscription_id`, `name`, `resource_type`, `region`, `sku`, `cost_eur`) VALUES
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/iBoschServiceDBDump', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschServiceDBDump', 'databases', 'southeastasia', 'ElasticPool', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/lubricash', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'lubricash', 'databases', 'southeastasia', 'ElasticPool', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/LubricashDump', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'LubricashDump', 'databases', 'southeastasia', 'ElasticPool', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Sql/servers/iboschdbs/databases/master', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'master', 'databases', 'southeastasia', 'GP_SYSTEM', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/appsvcsbackup', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'appsvcsbackup', 'storageaccounts', 'southeastasia', 'Standard_LRS', 7.38),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschapplogsa', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschapplogsa', 'storageaccounts', 'southeastasia', 'Standard_LRS', 2.83),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschbcsstorage1', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschbcsstorage1', 'storageaccounts', 'southeastasia', 'Standard_LRS', 2.43),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschcouponstorage1', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschcouponstorage1', 'storageaccounts', 'southeastasia', 'Standard_LRS', 49.67),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschdblogsa', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschdblogsa', 'storageaccounts', 'southeastasia', 'Standard_LRS', 3.75),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschintegration', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschintegration', 'storageaccounts', 'southeastasia', 'Standard_LRS', 16.88),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschpcdintegration', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschpcdintegration', 'storageaccounts', 'southeastasia', 'Standard_LRS', 7.42),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschrewardsprivate', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschrewardsprivate', 'storageaccounts', 'southeastasia', 'Standard_LRS', 17.65),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschstorage1', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschstorage1', 'storageaccounts', 'southeastasia', 'Standard_LRS', 16.66),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschstorage1test', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschstorage1test', 'storageaccounts', 'southeastasia', 'Standard_LRS', 7.18),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/iboschstoragebankdb', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschstoragebankdb', 'storageaccounts', 'southeastasia', 'Standard_LRS', 2.20),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Storage/storageAccounts/sqlvawjoesym3jvylm', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'sqlvawjoesym3jvylm', 'storageaccounts', 'southeastasia', 'Standard_LRS', 2.20),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/microsoft.web/serverFarms/iBoschAppPlan', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschAppPlan', 'serverfarms', 'southeastasia', 'S3', 681.95),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/serverFarms/iBoschAppPlan02', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschAppPlan02', 'serverfarms', 'southeastasia', 'P2v2', 155.09),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/BoschCastrolAPILive', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'BoschCastrolAPILive', 'sites', 'southeastasia', 'N/A', 0.30),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/BoschRewardsAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'BoschRewardsAPI', 'sites', 'southeastasia', 'N/A', 0.44),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/BoschServiceAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'BoschServiceAPI', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/BoschTallyProd', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'BoschTallyProd', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/ComplaintTracker', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'ComplaintTracker', 'sites', 'southeastasia', 'N/A', 0.40),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/cvcashwebapi', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'cvcashwebapi', 'sites', 'southeastasia', 'N/A', 0.17),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/DynamicReportAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'DynamicReportAPI', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/eFocusIntegration', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'eFocusIntegration', 'sites', 'southeastasia', 'N/A', 0.32),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/eJCAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'eJCAPI', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/eJCBosch', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'eJCBosch', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/EJCBWSIntegrationScheduler', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'EJCBWSIntegrationScheduler', 'sites', 'southeastasia', 'N/A', 0.34),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iboschadvantageclubProd', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschadvantageclubProd', 'sites', 'southeastasia', 'N/A', 0.29),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschCouponRewardsWCFMobile', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschCouponRewardsWCFMobile', 'sites', 'southeastasia', 'N/A', 4.54),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschDIGIOAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschDIGIOAPI', 'sites', 'southeastasia', 'N/A', 0.29),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoscheFocusIntegrationAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoscheFocusIntegrationAPI', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschGYFTrIntegration', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschGYFTrIntegration', 'sites', 'southeastasia', 'N/A', 0.29),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iboschinformationalert', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschinformationalert', 'sites', 'southeastasia', 'N/A', 0.45),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschMastersFTP', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschMastersFTP', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschNotification', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschNotification', 'sites', 'southeastasia', 'N/A', 0.01),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschNotificationWCFMobile', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschNotificationWCFMobile', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschPaytmAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschPaytmAPI', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschPromotion', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschPromotion', 'sites', 'southeastasia', 'N/A', 0.01),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschRewardBankAPINotify', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewardBankAPINotify', 'sites', 'southeastasia', 'N/A', 0.33),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschRewardNew', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewardNew', 'sites', 'southeastasia', 'N/A', 0.12),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iboschresources/providers/Microsoft.Web/sites/iBoschRewards', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewards', 'sites', 'southeastasia', 'N/A', 24.16),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschRewardsBankAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewardsBankAPI', 'sites', 'southeastasia', 'N/A', 0.32),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschRewardsReports', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewardsReports', 'sites', 'southeastasia', 'N/A', 0.63),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iboschrewardstesting', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschrewardstesting', 'sites', 'southeastasia', 'N/A', 0.75),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschRewardsWCF', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschRewardsWCF', 'sites', 'southeastasia', 'N/A', 0.36),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschServicePortal', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschServicePortal', 'sites', 'southeastasia', 'N/A', 117.98),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschServiceReports', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschServiceReports', 'sites', 'southeastasia', 'N/A', 1.58),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschServiceTesting', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschServiceTesting', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschServiceWCF', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschServiceWCF', 'sites', 'southeastasia', 'N/A', 0.33),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschTPAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschTPAPI', 'sites', 'southeastasia', 'N/A', 0.30),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschUKUService', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschUKUService', 'sites', 'southeastasia', 'N/A', 0.30),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschUtility', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschUtility', 'sites', 'southeastasia', 'N/A', 0.67),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschUtilityAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschUtilityAPI', 'sites', 'southeastasia', 'N/A', 0.30),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschUtilityWCF', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschUtilityWCF', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iBoschVirtualSMSAPI', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iBoschVirtualSMSAPI', 'sites', 'southeastasia', 'N/A', 0.30),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/iRewardsAPINew', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iRewardsAPINew', 'sites', 'southeastasia', 'N/A', 0.14),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/lubricashwebapi', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'lubricashwebapi', 'sites', 'southeastasia', 'N/A', 0.78),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/SalesDataMigration', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'SalesDataMigration', 'sites', 'southeastasia', 'N/A', 0.29),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/SSOiBoschServicePortal', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'SSOiBoschServicePortal', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/SSOiBoschServicePortalReports', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'SSOiBoschServicePortalReports', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/SSOiBoschServicePortalWCF', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'SSOiBoschServicePortalWCF', 'sites', 'southeastasia', 'N/A', 0.00),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/WCFBoschAMC', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'WCFBoschAMC', 'sites', 'southeastasia', 'N/A', 0.29),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/WCFBoschAMCMobile', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'WCFBoschAMCMobile', 'sites', 'southeastasia', 'N/A', 0.33),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/WCFPromotion', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'WCFPromotion', 'sites', 'southeastasia', 'N/A', 0.31),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/WCFTargetPlanner', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'WCFTargetPlanner', 'sites', 'southeastasia', 'N/A', 0.28),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/iBoschResources/providers/Microsoft.Web/sites/womAPIServiceProd', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'womAPIServiceProd', 'sites', 'southeastasia', 'N/A', 0.30),
('/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourceGroups/KibanaStorageAccounts/providers/Microsoft.Storage/storageAccounts/kibanalogsrewardsseasia', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'kibanalogsrewardsseasia', 'storageaccounts', 'southeastasia', 'Standard_LRS', 4.18),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Sql/servers/cms-prod-sql01/databases/CMS-Prod-SQLDB01', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS-Prod-SQLDB01', 'databases', 'southindia', 'Standard', 154.79),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Sql/servers/cms-prod-sql01/databases/CMS-QA-SQLDB01', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS-QA-SQLDB01', 'databases', 'southindia', 'Standard', 7.74),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Sql/servers/cms-prod-sql01/databases/master', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'master', 'databases', 'southindia', 'GP_SYSTEM', 0.00),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Storage/storageAccounts/cmsprodbackupsa', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'cmsprodbackupsa', 'storageaccounts', 'southindia', 'Standard_LRS', 7.39),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Storage/storageAccounts/cmsprodpcdstorage', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'cmsprodpcdstorage', 'storageaccounts', 'southindia', 'Standard_LRS', 11.17),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Storage/storageAccounts/cmsshareddiaglogs', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'cmsshareddiaglogs', 'storageaccounts', 'southindia', 'Standard_LRS', 7.60),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Storage/storageAccounts/cmssharedstorage', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'cmssharedstorage', 'storageaccounts', 'southindia', 'Standard_LRS', 11.05),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Web/serverFarms/CMS-Production-Webapp-S2Plan', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS-Production-Webapp-S2Plan', 'serverfarms', 'southindia', 'P1v2', 80.09),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/claims-management-production-rg01/providers/Microsoft.Web/sites/CMS-Prod-WebAPI01', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS-Prod-WebAPI01', 'sites', 'southindia', 'N/A', 0.69),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Production-RG01/providers/Microsoft.Web/sites/CMS-Prod-Webapp', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS-Prod-Webapp', 'sites', 'southindia', 'N/A', 0.94),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/claims-management-shared-rg01/providers/Microsoft.Compute/virtualMachines/CMS-Jumphost', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'CMS-Jumphost', 'virtualmachines', 'southindia', 'N/A', 0.00),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Shared-RG01/providers/Microsoft.Storage/storageAccounts/claimsmanagementshare452', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'claimsmanagementshare452', 'storageaccounts', 'southindia', 'Standard_LRS', 2.73),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/Claims-Management-Shared-RG01/providers/Microsoft.Storage/storageAccounts/claimsmanagementsharedrg', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'claimsmanagementsharedrg', 'storageaccounts', 'southindia', 'Standard_LRS', 7.20),
('/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourceGroups/CMS-Prod-WebAPI/providers/Microsoft.Web/sites/DBPaymentNotification', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'DBPaymentNotification', 'sites', 'southindia', 'N/A', 0.32);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `developers`
--

CREATE TABLE `developers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `developers`
--

INSERT INTO `developers` (`id`, `name`, `email`, `password`, `is_active`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Archith', 'jra9cob@bosch.com', '$2y$12$jNpBVvprkjBmwbS/LiiIZe7JubTQYs089NhDhqixJ.BJoDMLQlZVy', 1, '2026-10-10 02:29:38', NULL, '2026-10-10 02:29:38', '2026-10-10 02:29:38');

-- --------------------------------------------------------

--
-- Table structure for table `documentations`
--

CREATE TABLE `documentations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `author_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `documentations`
--

INSERT INTO `documentations` (`id`, `title`, `slug`, `content`, `author_id`, `created_at`, `updated_at`) VALUES
(2, 'Project Details', 'project-details', 'This is a test document created to store project details for the **subscription.**\n\n> This is a quote test.\n\n* Hello 1\n* Hello 2\n* Hello 3', 1, '2026-10-09 16:02:31', '2026-10-09 16:02:31'),
(3, 'New document for Audit test', 'audit-test-doc', 'This is a new document created merely for audit testing.', 1, '2026-10-10 02:47:15', '2026-10-10 02:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"b184729c-4d7f-4795-b49d-42cb3de68c72\",\"displayName\":\"TomaszBoloz\\\\LaravelUpdater\\\\Jobs\\\\RunTask\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":3600,\"retryUntil\":null,\"data\":{\"commandName\":\"TomaszBoloz\\\\LaravelUpdater\\\\Jobs\\\\RunTask\",\"command\":\"O:39:\\\"TomaszBoloz\\\\LaravelUpdater\\\\Jobs\\\\RunTask\\\":17:{s:4:\\\"task\\\";s:5:\\\"check\\\";s:7:\\\"package\\\";N;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:5:\\\"tries\\\";i:1;s:7:\\\"timeout\\\";i:3600;s:9:\\\"uniqueFor\\\";i:3600;}\",\"batchId\":null},\"createdAt\":1791621911,\"delay\":null}', 0, NULL, 1791621911, 1791621911);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_settings`
--

CREATE TABLE `log_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `log_settings`
--

INSERT INTO `log_settings` (`id`, `key`, `enabled`, `created_at`, `updated_at`) VALUES
(1, 'login', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(2, 'logout', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(3, 'failed_authentication', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(4, 'page_requests', 0, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(5, 'application_requests', 0, '2026-10-10 03:41:09', '2026-10-10 03:41:32'),
(6, 'created', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(7, 'updated', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(8, 'deleted', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(9, 'restored', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(10, 'force_deleted', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(11, 'updates_available', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(12, 'update_succeeded', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(13, 'update_failed', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(14, 'packages_updated', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09'),
(15, 'packages_update_failed', 1, '2026-10-10 03:41:09', '2026-10-10 03:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_10_08_000001_add_ccod_access_fields_to_users_table', 2),
(5, '2026_10_08_000002_create_teams_table', 2),
(6, '2026_10_08_000003_create_team_user_table', 2),
(7, '2026_10_08_000004_create_team_subscription_table', 2),
(8, '2026_10_08_000005_add_key_vault_reference_to_azure_subscriptions', 3),
(9, '2026_10_09_000001_add_billing_resource_indexes', 4),
(10, '2026_10_09_000002_create_projects_table', 5),
(11, '2026_10_09_000003_create_applications_table', 5),
(12, '2026_10_09_000004_add_application_and_environment_to_azure_subscriptions', 5),
(14, '2026_10_09_000004_create_subscription_documentations_table', 6),
(15, '2026_10_09_000005_seed_project_application_hierarchy', 6),
(16, '2026_10_09_000006_create_documentations_table', 7),
(17, '2026_10_09_000007_rename_documentation_blocks_to_content', 8),
(18, '2026_10_10_000001_create_audit_logs_table', 9),
(19, '2026_10_10_000001_create_activity_log_table', 10),
(20, '2026_10_10_000002_create_developers_table', 10),
(21, '2026_10_10_000000_create_log_settings_table', 11),
(22, '2026_10_10_101750_create_shiplog_releases_table', 12),
(23, '2026_10_10_120000_create_notifications_table', 13),
(24, '2026_10_10_130000_create_notification_rules_table', 14),
(25, '2026_10_10_130000_create_access_requests_table', 15),
(26, '2026_10_10_130001_create_user_access_grants_table', 15),
(27, '2026_10_10_140000_create_access_requests_table', 16),
(28, '2026_10_10_140001_create_user_access_grants_table', 17),
(29, '2026_10_10_150000_standardize_subscription_id_collation', 18),
(30, '2026_10_10_150001_normalize_subscription_id_collations', 19),
(31, '2026_10_10_170000_create_user_subscription_access_overrides_table', 20);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('1273c577-596f-4b84-9fcc-3ad0f095c407', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 2, '{\"title\":\"Trial notifications\",\"message\":\"This is a trial notification with General type Information severity\",\"type\":\"general\",\"severity\":\"info\",\"action_url\":null,\"action_label\":null}', '2026-10-10 09:23:02', '2026-10-10 07:11:39', '2026-10-10 09:23:02'),
('9c0ad8f5-828c-4772-afeb-6237f70a47ea', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 2, '{\"title\":\"Access request approved\",\"message\":\"Your request for Restricted Reader access to AA-SMS3-NA-PBAP-Prod was approved with access until 2026-10-10 22:10:00.\",\"type\":\"access_request\",\"severity\":\"success\",\"action_url\":\"http:\\/\\/localhost:8000\\/developer\\/access-requests\\/1\",\"action_label\":\"View request\"}', '2026-10-10 11:13:36', '2026-10-10 11:13:03', '2026-10-10 11:13:36'),
('b1404c05-412a-4bdc-ba32-99cab0d3f150', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 1, '{\"title\":\"Trial notifications\",\"message\":\"This is a trial notification with General type Information severity\",\"type\":\"general\",\"severity\":\"info\",\"action_url\":null,\"action_label\":null}', '2026-10-10 07:19:48', '2026-10-10 07:11:39', '2026-10-10 07:19:48'),
('cd350297-13a9-4859-9261-14b1f3153d72', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 2, '{\"title\":\"Access request approved\",\"message\":\"Your request for Restricted Reader access to MA-SKX-eXtra-Production-Stage-Prod was approved.\",\"type\":\"access_request\",\"severity\":\"success\",\"action_url\":\"http:\\/\\/localhost:8000\\/access-requests\\/2\",\"action_label\":\"View request\"}', '2026-10-10 10:09:53', '2026-10-10 10:09:22', '2026-10-10 10:09:53'),
('e5e5aff3-024b-4311-a339-16a80b9c60e0', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 1, '{\"title\":\"Access request pending\",\"message\":\"MA-BDO User requested Restricted Contributor access to AA-GPM-BoschCloudPrinting-Prod.\",\"type\":\"access_request\",\"severity\":\"warning\",\"action_url\":\"http:\\/\\/localhost:8000\\/access-requests\\/3\",\"action_label\":\"Review request\"}', '2026-10-10 11:40:20', '2026-10-10 11:02:26', '2026-10-10 11:40:20'),
('f2edc8c7-c848-45f2-a2b4-acc172204395', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 1, '{\"title\":\"Access request pending\",\"message\":\"MA-BDO User requested Restricted Reader access to MA-SKX-eXtra-Production-Stage-Prod.\",\"type\":\"access_request\",\"severity\":\"warning\",\"action_url\":\"http:\\/\\/localhost:8000\\/access-requests\\/2\",\"action_label\":\"Review request\"}', '2026-10-10 10:00:11', '2026-10-10 09:57:31', '2026-10-10 10:00:11'),
('f88e5652-13ae-435c-88f7-f740f8459bdd', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 2, '{\"title\":\"Subscription access revoked\",\"message\":\"Your access to MA-SKX-eXtra-Production-Stage-Prod has been revoked.\",\"type\":\"access_revoked\",\"severity\":\"danger\",\"action_url\":null,\"action_label\":null}', '2026-10-10 11:54:58', '2026-10-10 11:54:35', '2026-10-10 11:54:58'),
('fe8f3138-2c9f-4004-ba98-6046fefec54f', 'App\\Notifications\\ClientPortalNotification', 'App\\Models\\User', 1, '{\"title\":\"Access request pending\",\"message\":\"MA-BDO User requested Restricted Reader access to AA-SMS3-NA-PBAP-Prod.\",\"type\":\"access_request\",\"severity\":\"warning\",\"action_url\":\"http:\\/\\/localhost:8000\\/access-requests\\/1\",\"action_label\":\"Review request\"}', '2026-10-10 10:01:23', '2026-10-10 09:23:58', '2026-10-10 10:01:23');

-- --------------------------------------------------------

--
-- Table structure for table `notification_rules`
--

CREATE TABLE `notification_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event_key` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 0,
  `recipient_type` varchar(255) NOT NULL,
  `recipient_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`recipient_ids`)),
  `title` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'general',
  `severity` varchar(255) NOT NULL DEFAULT 'info',
  `action_label` varchar(80) DEFAULT NULL,
  `action_url` varchar(2048) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_rules`
--

INSERT INTO `notification_rules` (`id`, `event_key`, `enabled`, `recipient_type`, `recipient_ids`, `title`, `message`, `type`, `severity`, `action_label`, `action_url`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'azure_subscription_connected', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(2, 'azure_subscription_disconnected', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(3, 'azure_sync_succeeded', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(4, 'azure_sync_failed', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(5, 'azure_critical_alert_detected', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(6, 'servicenow_incident_created', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(7, 'servicenow_incident_resolved', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(8, 'user_added_to_team', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(9, 'user_removed_from_team', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(10, 'subscription_assigned_to_team', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(11, 'subscription_removed_from_team', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(12, 'maintenance_scheduled', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20'),
(13, 'maintenance_completed', 0, 'all', NULL, NULL, NULL, 'general', 'info', NULL, NULL, NULL, NULL, '2026-10-10 05:51:20', '2026-10-10 05:51:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'MA-BDO', 'MA-BDO applications and Azure environments.', 1, '2026-10-09 14:11:26', '2026-10-09 14:11:26');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('2Hxtt2tMZbnwmTxpEPENaR5XcwootxEYgKsczWTI', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiV3lKVWFreWdySDdLWFFINkNQRUcyNThCZXJkRUVNOWNoeG03ejg2TiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hY2Nlc3MtcmVxdWVzdHMiO3M6NToicm91dGUiO3M6NDY6ImZpbGFtZW50LmFkbWluLnJlc291cmNlcy5hY2Nlc3MtcmVxdWVzdHMuaW5kZXgiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJkMWE4NTZlNGJlMzg4MWQzODRlMjk4M2VlMDk3MzAyNjUwNjkzZWY3OWVlYTk2NThmOGRkZjNiY2ExMzJkYTJkIjtzOjY6InRhYmxlcyI7YTo0OntzOjQwOiIxNGVjNGVkM2ZkZDA5OWRkYjU2NjNjN2EwZTk3YWIyOV9jb2x1bW5zIjthOjc6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJ1c2VyLm5hbWUiO3M6NToibGFiZWwiO3M6OToiUmVxdWVzdGVyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToidGFyZ2V0X25hbWUiO3M6NToibGFiZWwiO3M6NjoiVGFyZ2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToidGFyZ2V0X3R5cGUiO3M6NToibGFiZWwiO3M6NDoiVHlwZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InJlcXVlc3RlZF9yb2xlIjtzOjU6ImxhYmVsIjtzOjE0OiJSZXF1ZXN0ZWQgcm9sZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiZHVyYXRpb24iO3M6NToibGFiZWwiO3M6ODoiRHVyYXRpb24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjk6IlJlcXVlc3RlZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiNDZiZjllYWI3OTNlMDVlZWRlN2EwYWQ4MDI4MjUyN2NfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6ImJ1ZGdldF9uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IkJ1ZGdldCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NjoiYW1vdW50IjtzOjU6ImxhYmVsIjtzOjEzOiJCdWRnZXQgQW1vdW50IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoiY3VycmVudF9zcGVuZCI7czo1OiJsYWJlbCI7czoxMjoiQWN0dWFsIFNwZW5kIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoiZm9yZWNhc3Rfc3BlbmQiO3M6NToibGFiZWwiO3M6ODoiRm9yZWNhc3QiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ0aW1lX2dyYWluIjtzOjU6ImxhYmVsIjtzOjY6IlBlcmlvZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InN0YXJ0X2RhdGUiO3M6NToibGFiZWwiO3M6MTA6IlN0YXJ0IGRhdGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImVuZF9kYXRlIjtzOjU6ImxhYmVsIjtzOjg6IkVuZCBkYXRlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiI4MGMwM2RmYjE4NjdkZGVhMjkyZTViNDE4ZjE3NGI4Zl9jb2x1bW5zIjthOjQ6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjEzOiJSZXNvdXJjZSBOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoicmVzb3VyY2VfdHlwZSI7czo1OiJsYWJlbCI7czo0OiJUeXBlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJyZWdpb24iO3M6NToibGFiZWwiO3M6ODoiTG9jYXRpb24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImNvc3RfZXVyIjtzOjU6ImxhYmVsIjtzOjQ6IkNvc3QiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjQ2YTAzMWJlOTY2NThlOWMyMmRmYzEyNTM3YTNjN2MwX2NvbHVtbnMiO2E6NDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTM6IlJlc291cmNlIE5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJyZXNvdXJjZV90eXBlIjtzOjU6ImxhYmVsIjtzOjQ6IlR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InJlZ2lvbiI7czo1OiJsYWJlbCI7czo4OiJMb2NhdGlvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiY29zdF9ldXIiO3M6NToibGFiZWwiO3M6NDoiQ29zdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319fXM6ODoiZmlsYW1lbnQiO2E6MDp7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6NDk6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kZXZlbG9wZXIvYWNjZXNzLXJlcXVlc3RzLzEiO319', 1791656716),
('dNxjxPcygelnVf2eDOv5iMZ01BvH4l5umVIyF3bH', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:140.0) Gecko/20100101 Firefox/140.0', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoicTYzMFZyNlkwaDhvbVlvRFNGQzhMQWZGdlhuY3c4ZVJEUDU4UEM1biI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjc4OiJodHRwOi8vbG9jYWxob3N0OjgwMDAvYXp1cmUtc3Vic2NyaXB0aW9ucy81Nzg0ZDg0ZC0wNWVhLTRhOWMtYjYyNS03ZDAxODNlOTI0MGIiO3M6NToicm91dGUiO3M6NDk6ImZpbGFtZW50LmFkbWluLnJlc291cmNlcy5henVyZS1zdWJzY3JpcHRpb25zLnZpZXciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlZWYxYzNlNzcxZmUwYjA5NTk1YTZlZjVhNmUxNDEzMWQ1NWVjYmJlOWU1MzgyNDliMjNmYmJlNTRmY2Q1M2RmIjtzOjY6InRhYmxlcyI7YTo3OntzOjQwOiI0NmJmOWVhYjc5M2UwNWVlZGU3YTBhZDgwMjgyNTI3Y19jb2x1bW5zIjthOjc6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToiYnVkZ2V0X25hbWUiO3M6NToibGFiZWwiO3M6NjoiQnVkZ2V0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJhbW91bnQiO3M6NToibGFiZWwiO3M6MTM6IkJ1ZGdldCBBbW91bnQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJjdXJyZW50X3NwZW5kIjtzOjU6ImxhYmVsIjtzOjEyOiJBY3R1YWwgU3BlbmQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJmb3JlY2FzdF9zcGVuZCI7czo1OiJsYWJlbCI7czo4OiJGb3JlY2FzdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InRpbWVfZ3JhaW4iO3M6NToibGFiZWwiO3M6NjoiUGVyaW9kIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoic3RhcnRfZGF0ZSI7czo1OiJsYWJlbCI7czoxMDoiU3RhcnQgZGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiZW5kX2RhdGUiO3M6NToibGFiZWwiO3M6ODoiRW5kIGRhdGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjgwYzAzZGZiMTg2N2RkZWEyOTJlNWI0MThmMTc0YjhmX2NvbHVtbnMiO2E6NDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6MTM6IlJlc291cmNlIE5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJyZXNvdXJjZV90eXBlIjtzOjU6ImxhYmVsIjtzOjQ6IlR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InJlZ2lvbiI7czo1OiJsYWJlbCI7czo4OiJMb2NhdGlvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6ODoiY29zdF9ldXIiO3M6NToibGFiZWwiO3M6NDoiQ29zdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiMTRlYzRlZDNmZGQwOTlkZGI1NjYzYzdhMGU5N2FiMjlfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToidXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjk6IlJlcXVlc3RlciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InRhcmdldF9uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IlRhcmdldCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InRhcmdldF90eXBlIjtzOjU6ImxhYmVsIjtzOjQ6IlR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJyZXF1ZXN0ZWRfcm9sZSI7czo1OiJsYWJlbCI7czoxNDoiUmVxdWVzdGVkIHJvbGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImR1cmF0aW9uIjtzOjU6ImxhYmVsIjtzOjg6IkR1cmF0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo5OiJSZXF1ZXN0ZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6IjU3N2VhNzAyMzRkMzJhYjdkMTQ1YTdhZDhhOTE2YWM2X2NvbHVtbnMiO2E6NDp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NDoiTmFtZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InVzZXJzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjU6IlVzZXJzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxOToic3Vic2NyaXB0aW9uc19jb3VudCI7czo1OiJsYWJlbCI7czoxMzoiU3Vic2NyaXB0aW9ucyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6InVwZGF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IlVwZGF0ZWQgYXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6ImM0YzdhZmJjZjVmMGQxZmZmNmQ1NzE0ODA4MmI5NTVkX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJkaXNwbGF5X25hbWUiO3M6NToibGFiZWwiO3M6MTI6IlN1YnNjcmlwdGlvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6InN1YnNjcmlwdGlvbl9pZCI7czo1OiJsYWJlbCI7czoxNToiU3Vic2NyaXB0aW9uIElEIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoiaGVhbHRoX3N0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJIZWFsdGgiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InNlY3VyaXR5X3Njb3JlIjtzOjU6ImxhYmVsIjtzOjE0OiJTZWN1cml0eSBTY29yZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjA7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoibXRkX3NwZW5kX2V1ciI7czo1OiJsYWJlbCI7czo5OiJNVEQgU3BlbmQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6Imxhc3Rfc3luY2VkIjtzOjU6ImxhYmVsIjtzOjExOiJMYXN0IFN5bmNlZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiNDZhMDMxYmU5NjY1OGU5YzIyZGZjMTI1MzdhM2M3YzBfY29sdW1ucyI7YTo0OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czoxMzoiUmVzb3VyY2UgTmFtZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6InJlc291cmNlX3R5cGUiO3M6NToibGFiZWwiO3M6NDoiVHlwZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NjoicmVnaW9uIjtzOjU6ImxhYmVsIjtzOjg6IkxvY2F0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJjb3N0X2V1ciI7czo1OiJsYWJlbCI7czo0OiJDb3N0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiJlNjQ0ODMzZjRlNGUwODcxMjMxNWRhNzFiMzNmYWNkMl9jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6ImVtYWlsIjtzOjU6ImxhYmVsIjtzOjU6IkVtYWlsIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJyb2xlIjtzOjU6ImxhYmVsIjtzOjQ6IlJvbGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImlzX2FjdGl2ZSI7czo1OiJsYWJlbCI7czo2OiJBY3RpdmUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ0ZWFtcy5uYW1lIjtzOjU6ImxhYmVsIjtzOjU6IlRlYW1zIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxNToiQWN0aXZhdGlvbiBEYXRlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX19czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1791654613),
('Nnim7OVJOWsOwHPFA0RxDrouldNnl27VqlDdhZkF', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6IkNDU29hTkVyNWR3U1hMTk5kQnVQTDYyZHdSbURETmRETWRZTUJjSFAiO3M6MzoidXJsIjthOjA6e31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo1MDoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL2RldmVsb3Blci9ub3RpZmljYXRpb24tcnVsZXMiO3M6NToicm91dGUiO3M6NTM6ImZpbGFtZW50LmRldmVsb3Blci5yZXNvdXJjZXMubm90aWZpY2F0aW9uLXJ1bGVzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1NzoibG9naW5fZGV2ZWxvcGVyc181OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoyNDoicGFzc3dvcmRfaGFzaF9kZXZlbG9wZXJzIjtzOjY0OiIxODgxNWEzMmIwNTdkMmZlMDEwYTQ3MDJkNWYyZjk2MWM3YWI1YWI3ZjgxNDVlMmM2ODQwYTEzZmRjYTkzNTdhIjtzOjY6InRhYmxlcyI7YTo5OntzOjQwOiJiZTQ4YjExMGY5M2Y0Njk0ZmJhNjFhNzMxMWE3ODUxZF9jb2x1bW5zIjthOjk6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo0OiJUaW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo4OiJsb2dfbmFtZSI7czo1OiJsYWJlbCI7czo4OiJDYXRlZ29yeSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NToiZXZlbnQiO3M6NToibGFiZWwiO3M6NToiRXZlbnQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJkZXNjcmlwdGlvbiI7czo1OiJsYWJlbCI7czoxMToiRGVzY3JpcHRpb24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJjYXVzZXIubmFtZSI7czo1OiJsYWJlbCI7czo1OiJBY3RvciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTg6InByb3BlcnRpZXMuc3VjY2VzcyI7czo1OiJsYWJlbCI7czo2OiJSZXN1bHQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo2O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjI1OiJwcm9wZXJ0aWVzLmZhaWx1cmVfcmVhc29uIjtzOjU6ImxhYmVsIjtzOjE0OiJGYWlsdXJlIFJlYXNvbiI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjc7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MjI6InByb3BlcnRpZXMuc3RhdHVzX2NvZGUiO3M6NToibGFiZWwiO3M6NDoiSFRUUCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjg7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6InByb3BlcnRpZXMubWV0aG9kIjtzOjU6ImxhYmVsIjtzOjY6Ik1ldGhvZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiYjg1ZjBhODBjNGMyMDY0NDI5NTRjN2Q4NjQ1Y2FjMGNfY29sdW1ucyI7YTo2OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NzoidmVyc2lvbiI7czo1OiJsYWJlbCI7czo3OiJWZXJzaW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJ0aXRsZSI7czo1OiJsYWJlbCI7czo4OiJIZWFkbGluZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InJlbGVhc2VkX2F0IjtzOjU6ImxhYmVsIjtzOjEyOiJSZWxlYXNlIGRhdGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6InN0YXR1cyI7czo1OiJsYWJlbCI7czo2OiJTdGF0dXMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEyOiJlbnZpcm9ubWVudHMiO3M6NToibGFiZWwiO3M6MTI6IkVudmlyb25tZW50cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NjoieWFua2VkIjtzOjU6ImxhYmVsIjtzOjY6IllhbmtlZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjE7fX1zOjQwOiI1YzA3OTAyNjcwOWZmMWJiMDVkZmViZTc5ZDllZDdhZV9jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJ2ZXJzaW9uIjtzOjU6ImxhYmVsIjtzOjc6IlZlcnNpb24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjU6InRpdGxlIjtzOjU6ImxhYmVsIjtzOjg6IkhlYWRsaW5lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMToicmVsZWFzZWRfYXQiO3M6NToibGFiZWwiO3M6MTI6IlJlbGVhc2UgZGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6Njoic3RhdHVzIjtzOjU6ImxhYmVsIjtzOjY6IlN0YXR1cyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6ImVudmlyb25tZW50cyI7czo1OiJsYWJlbCI7czoxMjoiRW52aXJvbm1lbnRzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJ5YW5rZWQiO3M6NToibGFiZWwiO3M6NjoiWWFua2VkIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9fXM6NDA6IjFkMmM5M2U4YmJiYWI1N2U2ZjRkN2UyMzRjMGQ5NjNhX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImV2ZW50X2tleSI7czo1OiJsYWJlbCI7czo1OiJFdmVudCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJlbmFibGVkIjtzOjU6ImxhYmVsIjtzOjc6IkVuYWJsZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InJlY2lwaWVudF90eXBlIjtzOjU6ImxhYmVsIjtzOjg6IkF1ZGllbmNlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6InNldmVyaXR5IjtzOjU6ImxhYmVsIjtzOjg6IlNldmVyaXR5IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEyOiJMYXN0IHVwZGF0ZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO319czo0MDoiMTRlYzRlZDNmZGQwOTlkZGI1NjYzYzdhMGU5N2FiMjlfY29sdW1ucyI7YTo3OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToidXNlci5uYW1lIjtzOjU6ImxhYmVsIjtzOjk6IlJlcXVlc3RlciI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InRhcmdldF9uYW1lIjtzOjU6ImxhYmVsIjtzOjY6IlRhcmdldCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTE6InRhcmdldF90eXBlIjtzOjU6ImxhYmVsIjtzOjQ6IlR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE0OiJyZXF1ZXN0ZWRfcm9sZSI7czo1OiJsYWJlbCI7czoxNDoiUmVxdWVzdGVkIHJvbGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImR1cmF0aW9uIjtzOjU6ImxhYmVsIjtzOjg6IkR1cmF0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo2OiJzdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiU3RhdHVzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czo5OiJSZXF1ZXN0ZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fXM6NDA6Ijc4M2E5ODJlNWFkYTFhYWVkOTc5NGI0YTU5Y2Q0YjliX2NvbHVtbnMiO2E6Njp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ0YWJsZV9uYW1lIjtzOjU6ImxhYmVsIjtzOjU6IlRhYmxlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidGFibGVfcm93cyI7czo1OiJsYWJlbCI7czo0OiJSb3dzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo3OiJzaXplX21iIjtzOjU6ImxhYmVsIjtzOjQ6IlNpemUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjY6ImVuZ2luZSI7czo1OiJsYWJlbCI7czo2OiJFbmdpbmUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJ0YWJsZV9jb2xsYXRpb24iO3M6NToibGFiZWwiO3M6OToiQ29sbGF0aW9uIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MDtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MTt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEzOiJ0YWJsZV9jb21tZW50IjtzOjU6ImxhYmVsIjtzOjc6IkNvbW1lbnQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjowO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjoxO319czo0MToiNzgzYTk4MmU1YWRhMWFhZWQ5Nzk0YjRhNTljZDRiOWJfZ3JvdXBpbmciO3M6MTU6InRhYmxlX2dyb3VwOmFzYyI7czo0MDoiZTY0NDgzM2Y0ZTRlMDg3MTIzMTVkYTcxYjMzZmFjZDJfY29sdW1ucyI7YTo2OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJlbWFpbCI7czo1OiJsYWJlbCI7czo1OiJFbWFpbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoicm9sZSI7czo1OiJsYWJlbCI7czo0OiJSb2xlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6NjoiQWN0aXZlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoidGVhbXMubmFtZSI7czo1OiJsYWJlbCI7czo1OiJUZWFtcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjU7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTU6IkFjdGl2YXRpb24gRGF0ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiMDEwM2VlMzk1MWYyYmZiYzEwNzNjYWVjNDcxYzdiMjFfY29sdW1ucyI7YToxMjp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjI6ImlkIjtzOjU6ImxhYmVsIjtzOjI6IklkIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjg6ImxvZ19uYW1lIjtzOjU6ImxhYmVsIjtzOjg6IkxvZyBOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJkZXNjcmlwdGlvbiI7czo1OiJsYWJlbCI7czoxMToiRGVzY3JpcHRpb24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTI6InN1YmplY3RfdHlwZSI7czo1OiJsYWJlbCI7czoxMjoiU3ViamVjdCBUeXBlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJzdWJqZWN0X2lkIjtzOjU6ImxhYmVsIjtzOjEwOiJTdWJqZWN0IElkIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJjYXVzZXJfdHlwZSI7czo1OiJsYWJlbCI7czoxMToiQ2F1c2VyIFR5cGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjY7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiY2F1c2VyX2lkIjtzOjU6ImxhYmVsIjtzOjk6IkNhdXNlciBJZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6NzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoicHJvcGVydGllcyI7czo1OiJsYWJlbCI7czoxMDoiUHJvcGVydGllcyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6ODthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJldmVudCI7czo1OiJsYWJlbCI7czo1OiJFdmVudCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6OTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiYmF0Y2hfdXVpZCI7czo1OiJsYWJlbCI7czoxMDoiQmF0Y2ggVXVpZCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjE7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtiOjA7fWk6MTA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTA6ImNyZWF0ZWRfYXQiO3M6NToibGFiZWwiO3M6MTA6IkNyZWF0ZWQgQXQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjoxO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7YjowO31pOjExO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEwOiJVcGRhdGVkIEF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MTtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO2I6MDt9fX1zOjg6ImZpbGFtZW50IjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlZWYxYzNlNzcxZmUwYjA5NTk1YTZlZjVhNmUxNDEzMWQ1NWVjYmJlOWU1MzgyNDliMjNmYmJlNTRmY2Q1M2RmIjt9', 1791657649);

-- --------------------------------------------------------

--
-- Table structure for table `shiplog_releases`
--

CREATE TABLE `shiplog_releases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` longtext DEFAULT NULL,
  `released_at` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `environments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`environments`)),
  `yanked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `shiplog_releases`
--

INSERT INTO `shiplog_releases` (`id`, `version`, `title`, `body`, `released_at`, `status`, `environments`, `yanked`, `created_at`, `updated_at`) VALUES
(1, 'v0.1.0', 'CCOD Foundation', '# Added\n\n* Established the initial Cloud Operations Dashboard foundation.\n* Added the core Laravel / Filament application structure.\n* Added Azure subscription management.\n* Added the initial subscription dashboard and related resource views.\n* Introduced the initial user and access-control structure.\n* Added the foundation for team-based subscription access.\n* Added Azure subscription relationships required for the dashboard architecture.\n\n# Infrastructure\n\n* Added the initial database schema and migrations.\n* Established the application configuration and authentication foundation.\n* Added the base Filament resources and dashboard structure.', '2026-10-09', 'draft', '[\"local\"]', 0, '2026-10-10 04:59:24', '2026-10-10 05:22:07'),
(2, '0.2.0', 'Teams & Subscription Management', '# Added\n\n* Added Teams management.\n* Added team membership relationships.\n* Added team-to-subscription authorization relationships.\n* Added project and application hierarchy.\n* Added application/environment relationships to Azure subscriptions.\n* Added initial project hierarchy seeding.\n* Existing Azure subscriptions can now be grouped into the appropriate project/application structure.\n\n# Azure Subscriptions\n\n* Improved the Azure Subscription resource.\n* Added subscription detail views.\n* Added billing resource relationship management.\n* Added subscription-level statistics and cost information.\n* Added budget and resource cost breakdown information.\n* Added XLSX export for Azure subscriptions.\n\n# Access Control\n\n* Introduced role-based access concepts.\n* Subscription visibility can now be scoped through team membership.', '2026-10-09', 'draft', '[\"local\"]', 0, '2026-10-10 05:08:26', '2026-10-10 05:22:01'),
(3, '0.3.0', 'Documentation & Knowledge Management', '# Added\n\n* Added the Documentation resource.\n* Added documentation creation and editing.\n* Added documentation metadata.\n* Added documentation post model and persistence.\n* Added documentation policies.\n* Added subscription-specific documentation authorization.\n* Added rich-text editing through Filament\'s RichEditor.\n* Added a dedicated documentation reading/view mode.\n\n# Improvements\n\n* Documentation records now open in read/view mode by default.\n* Added an explicit Edit action from the documentation view.\n* Delete actions are restricted to edit mode.\n* Improved the documentation viewing experience with an infolist/blog-style presentation.\n* Improved navigation after creating documentation.', '2026-10-09', 'draft', '[\"local\"]', 0, '2026-10-10 05:10:32', '2026-10-10 05:21:55'),
(4, 'v0.4.0', 'Developer Operations & Audit Logging', '### Added\n\n- Added a dedicated Developer Dashboard.\n- Added a separate developers authentication guard and provider.\n- Added developer account management and activation status.\n- Added developer-only access to operational tooling.\n\n### Audit Logging\n\n* Added application audit logging.\n* Added authentication event logging.\n* Added request logging.\n* Added CRUD/model-action logging.\n* Added failure-reason tracking.\n* Added HTTP request metadata and execution duration.\n* Added configurable audit-log categories.\n\n### Logging Controls\n\nAdded granular controls for:\n- Login\n- Logout\n- Failed authentication\n- Page requests\n- Application requests\n- Create\n- Update\n- Delete\n- Restore\n- Force delete\n\n ### Security\n \n* Audit logs are isolated from the normal CCOD dashboard.\n* Audit logs are accessible only from the developer dashboard.\n* Developer authorization is enforced separately from normal application users.\n* Audit records do not store old/new model values.', '2026-10-10', 'draft', '[\"local\"]', 0, '2026-10-10 05:12:06', '2026-10-10 05:21:49'),
(5, 'v0.5.0', 'Release Management & Changelog', '# Added\n\n* Integrated ShipLog for application release management.\n* Added database-backed release storage.\n* Added developer-only release management.\n* Added the Changelog experience for application users.\n* Added release visibility through the application user menu.\n\n# Changelog UX\n\n* Removed the separate sidebar Changelog entry.\n* Changelog is accessible from the user menu, directly below Sign out.\n* Uses Filament\'s native user-menu styling.\n* Removed the unwanted ShipLog \"What\'s New\" style entry point.\n\n# Developer Experience\n\nDevelopers can manage releases from:\n\n**Developer Dashboard → Maintenance → Changelog**\n\nwhile normal users can view published releases through:\n\n**User Menu → Changelog**', '2026-10-10', 'draft', '[\"local\"]', 0, '2026-10-10 05:13:07', '2026-10-10 05:21:43');

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MA-BDO', 'MA-BDO Team - Business, App & Security', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(2, 'RBIN', 'RBIN Projects', '2026-10-08 10:55:55', '2026-10-08 10:55:55'),
(3, 'Others', 'Other Subscriptions', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(4, 'MA', 'Mobility Aftermarket', '2026-10-10 10:17:30', '2026-10-10 10:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `team_subscription`
--

CREATE TABLE `team_subscription` (
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_subscription`
--

INSERT INTO `team_subscription` (`team_id`, `subscription_id`, `created_at`, `updated_at`) VALUES
(1, '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '219ccbf6-e35b-4758-961a-ede690403d86', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '4172676b-8a9c-44e0-b2ab-727057b691b7', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '50d41c25-8668-4497-8925-766161f0cddb', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '5784d84d-05ea-4a9c-b625-7d0183e9240b', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '7498780e-1785-465f-9d50-c8ac1e929376', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, 'fc152cdb-dc60-44e7-bbee-412c719cb49a', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(1, 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(2, '02ab4266-3406-48d4-bfbc-76605dde11d7', '2026-10-08 10:55:55', '2026-10-08 10:55:55'),
(2, '12db631f-5ab4-4237-9fa6-6d92de7a53e8', '2026-10-08 10:55:55', '2026-10-08 10:55:55'),
(2, 'c12d79d2-0655-4caa-839e-fc47e019271c', '2026-10-08 10:55:55', '2026-10-08 10:55:55'),
(3, '0e6dc52f-1c64-4d5a-9396-29e37fa41079', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(3, '6b8e63a8-5397-4d83-9b49-164067a4e892', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(3, '6c389f01-77d0-4c2c-b846-8dfa36987531', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(3, 'd357b6e3-c707-4e36-8681-ba7ef9e30e8a', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(3, 'e6800a5e-47c7-47df-8bf8-fc2c616e1442', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(4, '19fb36f7-0105-4d68-be29-02c3934a2abc', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, '5b46ccc6-604b-4c5c-81c2-96b133061773', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, '77839ff3-b3aa-42b0-b490-55830c14bd3a', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, 'a77eebf3-17a1-4378-90bf-2c96b1028137', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, 'c080fc5b-797d-45db-b089-01cc05f9a758', '2026-10-10 10:17:30', '2026-10-10 10:17:30'),
(4, 'd26dd531-2174-4c45-a240-032e0d05dfa0', '2026-10-10 10:17:30', '2026-10-10 10:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `team_user`
--

CREATE TABLE `team_user` (
  `team_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_user`
--

INSERT INTO `team_user` (`team_id`, `user_id`, `created_at`, `updated_at`) VALUES
(3, 1, '2026-10-08 14:02:58', '2026-10-08 14:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'restricted_reader',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'JRA9COB', 'jra9cob@bosch.com', NULL, '$2y$12$gvrkcj5.HqEO.vMZyYvLFej075vWSkG.DOyfDfY2utnExYWjvFsyW', 'global_owner', 1, NULL, '2026-10-08 08:48:28', '2026-10-09 09:18:45'),
(2, 'MA-BDO User', 'mabdo@bosch.com', NULL, '$2y$12$y7faO2LpXyjPMp5k5WdNO.OF4obRM1nNzFe9iHrA6Yjy6wnbt.1na', 'restricted_owner', 1, NULL, '2026-10-08 10:50:52', '2026-10-08 13:20:09');

-- --------------------------------------------------------

--
-- Table structure for table `user_access_grants`
--

CREATE TABLE `user_access_grants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `target_type` varchar(255) NOT NULL DEFAULT 'subscription',
  `team_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subscription_id` varchar(100) DEFAULT NULL,
  `target_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `granted_by_type` varchar(255) NOT NULL,
  `granted_by_id` bigint(20) UNSIGNED NOT NULL,
  `source_request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `starts_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_access_grants`
--

INSERT INTO `user_access_grants` (`id`, `user_id`, `target_type`, `team_id`, `subscription_id`, `target_name`, `role`, `granted_by_type`, `granted_by_id`, `source_request_id`, `starts_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 2, 'subscription', NULL, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'MA-SKX-eXtra-Production-Stage-Prod', 'restricted_reader', 'App\\Models\\User', 1, 2, '2026-10-10 10:09:22', NULL, '2026-10-10 10:09:22', '2026-10-10 10:09:22'),
(2, 2, 'subscription', NULL, '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'AA-SMS3-NA-PBAP-Prod', 'restricted_reader', 'App\\Models\\User', 1, 1, '2026-10-10 11:13:03', '2026-10-10 16:40:00', '2026-10-10 11:13:03', '2026-10-10 11:13:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_subscription_access_overrides`
--

CREATE TABLE `user_subscription_access_overrides` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(255) NOT NULL,
  `override` varchar(255) NOT NULL DEFAULT 'revoked',
  `revoked_by_type` varchar(255) DEFAULT NULL,
  `revoked_by_id` bigint(20) UNSIGNED DEFAULT NULL,
  `revoked_at` timestamp NULL DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_subscription_access_overrides`
--

INSERT INTO `user_subscription_access_overrides` (`id`, `user_id`, `subscription_id`, `override`, `revoked_by_type`, `revoked_by_id`, `revoked_at`, `reason`, `created_at`, `updated_at`) VALUES
(1, 2, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'revoked', 'App\\Models\\User', 1, '2026-10-10 11:54:35', 'Task completed', '2026-10-10 11:54:35', '2026-10-10 11:54:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_requests_team_id_foreign` (`team_id`),
  ADD KEY `access_requests_status_created_at_index` (`status`,`created_at`),
  ADD KEY `access_requests_user_id_status_index` (`user_id`,`status`),
  ADD KEY `access_requests_target_type_team_id_index` (`target_type`,`team_id`),
  ADD KEY `access_requests_target_type_subscription_id_index` (`target_type`,`subscription_id`),
  ADD KEY `access_requests_decided_by_type_decided_by_id_index` (`decided_by_type`,`decided_by_id`);

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `activity_log_causer_type_causer_id_index` (`causer_type`,`causer_id`),
  ADD KEY `activity_log_log_name_index` (`log_name`),
  ADD KEY `activity_log_event_created_at_index` (`event`,`created_at`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applications_project_id_name_unique` (`project_id`,`name`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_event_created_at_index` (`event`,`created_at`),
  ADD KEY `audit_logs_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  ADD KEY `audit_logs_user_id_created_at_index` (`user_id`,`created_at`);

--
-- Indexes for table `azure_budgets`
--
ALTER TABLE `azure_budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_subscription_budget` (`subscription_id`,`budget_name`),
  ADD KEY `idx_budget_subscription` (`subscription_id`);

--
-- Indexes for table `azure_cost_forecasts`
--
ALTER TABLE `azure_cost_forecasts`
  ADD PRIMARY KEY (`subscription_id`);

--
-- Indexes for table `azure_security_summary`
--
ALTER TABLE `azure_security_summary`
  ADD PRIMARY KEY (`summary_id`);

--
-- Indexes for table `azure_subscriptions`
--
ALTER TABLE `azure_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `azure_subscriptions_application_environment_index` (`application_id`,`environment`);

--
-- Indexes for table `billing_resources`
--
ALTER TABLE `billing_resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `idx_subscription` (`subscription_id`),
  ADD KEY `billing_resources_subscription_name_index` (`subscription_id`,`name`),
  ADD KEY `billing_resources_subscription_type_index` (`subscription_id`,`resource_type`),
  ADD KEY `billing_resources_subscription_region_index` (`subscription_id`,`region`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `developers`
--
ALTER TABLE `developers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `developers_email_unique` (`email`);

--
-- Indexes for table `documentations`
--
ALTER TABLE `documentations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `documentations_slug_unique` (`slug`),
  ADD KEY `documentations_author_id_foreign` (`author_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_settings`
--
ALTER TABLE `log_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `log_settings_key_unique` (`key`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_read_at_index` (`notifiable_type`,`notifiable_id`,`read_at`);

--
-- Indexes for table `notification_rules`
--
ALTER TABLE `notification_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_rules_event_key_unique` (`event_key`),
  ADD KEY `notification_rules_created_by_foreign` (`created_by`),
  ADD KEY `notification_rules_updated_by_foreign` (`updated_by`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_name_unique` (`name`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `shiplog_releases`
--
ALTER TABLE `shiplog_releases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shiplog_releases_version_index` (`version`),
  ADD KEY `shiplog_releases_released_at_index` (`released_at`),
  ADD KEY `shiplog_releases_status_index` (`status`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teams_name_unique` (`name`);

--
-- Indexes for table `team_subscription`
--
ALTER TABLE `team_subscription`
  ADD PRIMARY KEY (`team_id`,`subscription_id`),
  ADD KEY `team_subscription_subscription_id_foreign` (`subscription_id`);

--
-- Indexes for table `team_user`
--
ALTER TABLE `team_user`
  ADD PRIMARY KEY (`team_id`,`user_id`),
  ADD KEY `team_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_access_grants`
--
ALTER TABLE `user_access_grants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_access_grants_source_request_id_foreign` (`source_request_id`),
  ADD KEY `user_access_grants_user_id_target_type_index` (`user_id`,`target_type`),
  ADD KEY `user_access_grants_team_id_expires_at_index` (`team_id`,`expires_at`),
  ADD KEY `user_access_grants_subscription_id_expires_at_index` (`subscription_id`,`expires_at`),
  ADD KEY `user_access_grants_granted_by_type_granted_by_id_index` (`granted_by_type`,`granted_by_id`);

--
-- Indexes for table `user_subscription_access_overrides`
--
ALTER TABLE `user_subscription_access_overrides`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_sub_access_overrides_unique` (`user_id`,`subscription_id`),
  ADD KEY `user_sub_access_overrides_subscription_override_index` (`subscription_id`,`override`),
  ADD KEY `user_sub_access_overrides_revoked_by_index` (`revoked_by_type`,`revoked_by_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_requests`
--
ALTER TABLE `access_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=292;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `azure_budgets`
--
ALTER TABLE `azure_budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `developers`
--
ALTER TABLE `developers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `documentations`
--
ALTER TABLE `documentations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `log_settings`
--
ALTER TABLE `log_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notification_rules`
--
ALTER TABLE `notification_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shiplog_releases`
--
ALTER TABLE `shiplog_releases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_access_grants`
--
ALTER TABLE `user_access_grants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_subscription_access_overrides`
--
ALTER TABLE `user_subscription_access_overrides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD CONSTRAINT `access_requests_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `access_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `azure_budgets`
--
ALTER TABLE `azure_budgets`
  ADD CONSTRAINT `fk_azure_budget_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE;

--
-- Constraints for table `azure_cost_forecasts`
--
ALTER TABLE `azure_cost_forecasts`
  ADD CONSTRAINT `fk_cost_forecast_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE;

--
-- Constraints for table `azure_subscriptions`
--
ALTER TABLE `azure_subscriptions`
  ADD CONSTRAINT `azure_subscriptions_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `documentations`
--
ALTER TABLE `documentations`
  ADD CONSTRAINT `documentations_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notification_rules`
--
ALTER TABLE `notification_rules`
  ADD CONSTRAINT `notification_rules_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `developers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `notification_rules_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `developers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `team_subscription`
--
ALTER TABLE `team_subscription`
  ADD CONSTRAINT `team_subscription_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_subscription_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `team_user`
--
ALTER TABLE `team_user`
  ADD CONSTRAINT `team_user_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_access_grants`
--
ALTER TABLE `user_access_grants`
  ADD CONSTRAINT `user_access_grants_source_request_id_foreign` FOREIGN KEY (`source_request_id`) REFERENCES `access_requests` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_access_grants_team_id_foreign` FOREIGN KEY (`team_id`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_access_grants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_subscription_access_overrides`
--
ALTER TABLE `user_subscription_access_overrides`
  ADD CONSTRAINT `user_subscription_access_overrides_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
