-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 22, 2026 at 01:28 PM
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
  `user_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `target_type` varchar(255) NOT NULL DEFAULT 'subscription',
  `business_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
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
(1, 'Models', 'UserGroup #3 created', 'App\\Models\\UserGroup', 3, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/154.0.0.0 Safari\\/537.36\",\"tags\":[\"model\",\"created\"]}', 'Created', NULL, '2026-10-21 02:53:44', '2026-10-21 02:53:44'),
(2, 'Access', 'Authentication succeeded.', NULL, NULL, 'App\\Models\\User', 1, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"guard\":\"web\",\"remember\":false,\"tags\":[\"authentication\",\"login\"]}', 'Login', NULL, '2026-10-22 00:42:40', '2026-10-22 00:42:40'),
(3, 'Access', 'Authentication succeeded.', NULL, NULL, 'App\\Models\\User', 5, '{\"success\":true,\"url\":\"http:\\/\\/localhost:8000\\/livewire-27bd2a03\\/update\",\"route\":\"default-livewire.update\",\"method\":\"POST\",\"ip\":\"127.0.0.1\",\"user_agent\":\"Mozilla\\/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit\\/537.36 (KHTML, like Gecko) Chrome\\/155.0.0.0 Safari\\/537.36\",\"guard\":\"web\",\"remember\":false,\"tags\":[\"authentication\",\"login\"]}', 'Login', NULL, '2026-10-22 00:45:35', '2026-10-22 00:45:35');

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

-- --------------------------------------------------------

--
-- Table structure for table `azure_alerts`
--

CREATE TABLE `azure_alerts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `azure_alert_id` varchar(1024) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `severity` varchar(10) NOT NULL,
  `affected_resource` text DEFAULT NULL,
  `target_resource_type` varchar(255) DEFAULT NULL,
  `alert_condition` varchar(50) DEFAULT NULL,
  `user_response` varchar(50) DEFAULT NULL,
  `monitor_service` varchar(255) DEFAULT NULL,
  `signal_type` varchar(255) DEFAULT NULL,
  `fire_time` datetime DEFAULT NULL,
  `last_modified_time` datetime DEFAULT NULL,
  `subscription_id` varchar(100) NOT NULL,
  `target_resource_group` varchar(255) DEFAULT NULL,
  `suppression_status` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `azure_alerts`
--

INSERT INTO `azure_alerts` (`id`, `azure_alert_id`, `name`, `severity`, `affected_resource`, `target_resource_type`, `alert_condition`, `user_response`, `monitor_service`, `signal_type`, `fire_time`, `last_modified_time`, `subscription_id`, `target_resource_group`, `suppression_status`, `created_at`, `updated_at`) VALUES
(1, 'c6cd1b35720140c1923a81c4ec782d8d3ca4ab9e378258188d35402904ef11a7', 'AA-Prod-DB-aapremrsvm01-HighDTU', 'Sev1', '/subscriptions/8a8c77f4-cb44-47ad-b56a-6682d97b36bb/resourcegroups/aa_rg01/providers/microsoft.sql/servers/aapremrsvm01/databases/aapremrsvm01', 'microsoft.sql/servers/databases', 'Fired', 'New', 'Platform', 'Metrics', '2026-10-13 16:44:28', '2026-10-13 16:44:28', '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'aa_rg01', 'None', '2026-10-13 11:51:00', '2026-10-13 11:51:00'),
(2, 'cc70f502d4df59223956b29d89809ff006bd579d474d7e64d1078e54d27fbbfb', 'miconic qa - 2woapi-miconic-qa-app-insights', 'Sev1', '/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourcegroups/miconi-qa-rg-1/providers/microsoft.insights/components/miconic-qa-app-insights', 'microsoft.insights/components', 'Fired', 'New', 'Platform', 'Metrics', '2026-10-13 16:43:44', '2026-10-13 16:43:44', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-rg-1', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(3, '045df36574884b30b682df2c2a27e68b7e53578c1dfab83db3051033c7f4bd41', 'miconic qa - internal-miconic-qa-app-insights', 'Sev1', '/subscriptions/50d41c25-8668-4497-8925-766161f0cddb/resourcegroups/miconi-qa-rg-1/providers/microsoft.insights/components/miconic-qa-app-insights', 'microsoft.insights/components', 'Fired', 'New', 'Platform', 'Metrics', '2026-10-13 16:42:43', '2026-10-13 16:42:43', '50d41c25-8668-4497-8925-766161f0cddb', 'miconi-qa-rg-1', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(4, '140202826a962c9670c5dfb550bd3bae494bed851925b5ec054879ab6082835b', 'boschrewards-dbdtu-iboschrewards', 'Sev3', '/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourcegroups/iboschresources/providers/microsoft.sql/servers/iboschdbs/databases/iboschrewards', 'microsoft.sql/servers/databases', 'Fired', 'New', 'Platform', 'Metrics', '2026-10-13 16:41:45', '2026-10-13 16:41:45', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschresources', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(5, '258551c46516481cb45749b77121d3e04ab6562ddd3727d894a9b00ab6053642', 'EFOCUSQSQL02 - VM has been deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqsql02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(6, '2c2477d050ceea2464e74b45df510b66773df93eea63ae1feb4fd59307bbfa4b', 'EFOCUSQRDS02 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqrds02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(7, 'a8d7a8cd9f8a0d2c01221217d29b36504e68c4653090db84a2a03591a1b78f06', 'EFOCUSQSQL02 - VM has been deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqsql02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(8, '87e439fda3803a9b77c7e9c85c5206eb971804a83ddfb6ab71344ca29b33b4a4', 'EFOCUSQSQL02 - VM has been deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqsql02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(9, '5e187f6431f88492fa77d8e4a95ac7d45f644a889bae36462d0b877266c176c6', 'EFOCUSQRDS02 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqrds02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(10, '14c9df0fde74a4c12148254bd23cd5fa283b6c28fede8d2e6206b9d067acfc63', 'EFOCUSNAS05 - VM has been deallocated - Critical', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas05', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(11, '62992f3161feb7d300b540644b116f261ce701f6c52e3fe63268a7fd9f465963', 'EFOCUSQRDS02 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqrds02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(12, '23072a9846b29fc088e2350fda831b365a6f163e3c5cac02ffe91064c0955bb8', 'EFOCUSQNAS06 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas06', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(13, '7e096edbd8ad6e8555d281872f34fa23ca81d655f6551acf379067e7d20757d4', 'EFOCUSQNAS06 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas06', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:49', '2026-10-13 15:41:49', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(14, 'ab758e7dc348b736e4f3e7a0a1365bea04062a806e44e7657eb2679a2da2dd8c', 'EFOCUSQNAS06 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas06', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:48', '2026-10-13 15:41:48', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(15, '939bc3083a5420436d84e8522168ddd7262e9d3468f31f52ffb03cac1ac969ac', 'EFOCUSNAS05 - VM has been deallocated - Critical', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas05', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:41:48', '2026-10-13 15:41:48', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(16, '444cc881373cb3500077a2a8761a274bd4ea0b6dd5ccb1d93da9a146c4fa9df9', 'BoschRewards-Info_Planned Monthly DTU update activity', 'Sev4', '/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourcegroups/iboschresources/providers/microsoft.sql/servers/iboschdbs/elasticpools/iboschelastic', 'microsoft.sql/servers/elasticpools', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 15:26:09', '2026-10-13 15:26:09', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschresources', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(17, '3ab075d962d2387a00e0b411276e7ee7f120ec310319cca457d8975591d9a30b', 'cmsprod-cms-prod-appinsights01', 'Sev1', '/subscriptions/fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8/resourcegroups/claims-management-production-rg01/providers/microsoft.insights/components/cms-prod-appinsights01', 'microsoft.insights/components', 'Fired', 'New', 'Platform', 'Metrics', '2026-10-13 14:37:18', '2026-10-13 14:37:18', 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'claims-management-production-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(18, '19ae1b1f866f56682ab83d59595995426d7506e7fbdf5b7837bf356d21180892', 'AGW01-RequestPerHour-ExtraPROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 14:15:04', '2026-10-13 14:15:04', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(19, '615c9d821598774c254519b20ec53196881853dd0e2857d722c80fd0aa5ea33a', 'AG-request-per-hour-Extra-QA-platform', 'Sev1', '/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourcegroups/extra-qa-rg01/providers/microsoft.network/applicationgateways/extra-qa-appgw-platform01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 13:51:39', '2026-10-13 13:51:39', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(20, 'e245c75ab6b207a0374d82597d497b77df898870cafb9b9c2c7ff56b4160c37a', 'EFOCUSQRDS02 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqrds02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:31:51', '2026-10-13 13:31:51', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(21, 'f5626e96d7a1c45c4d508c293f6aa490bdac30aa093fd12705f15ca6166bd656', 'EFOCUSQSQL02 - VM has been deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqsql02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:31:51', '2026-10-13 13:31:51', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(22, '8f762606259920ac0f33fce38142dd385b15a1de4b3dc899b4afbdf1d76c08f2', 'EFOCUSQSQL02 - VM has been deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqsql02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:31:51', '2026-10-13 13:31:51', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(23, 'e90de8b40939078fd7c6754fe6767f7bbf5684348077f6829d0b54d99d90d5ab', 'EFOCUSQRDS02 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqrds02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:51', '2026-10-13 13:26:51', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(24, '8928efc82234d80c2537c4c11e4f288bb3c6b08891801e2bd5b6b4a82d97d204', 'EFOCUSQNAS06 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas06', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(25, '7d6dda29d70ebb05873bb3e7dbb73befe96c7ea7347f602d9dec83141083efcf', 'EFOCUSQNAS06 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas06', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(26, '2cadf8f73660fafe798cead2c698078f56a8e6ed3863f5120446a1d727d9bcfd', 'EFOCUSNAS05 - VM has been deallocated - Critical', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas05', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(27, '80a171e392f096e80db9f6fdd0de9c0a4fcd68e1b45c5d1abb60921efaf04be0', 'EFOCUSQNAS06 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas06', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(28, '19e6d33b40cb81aa6fb59bfefc72c63e4b2b636d24184e702f63f7f36f6aee82', 'EFOCUSNAS05 - VM has been deallocated - Critical', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas05', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(29, '8cd4544ed7a9bd499e1df587534fbd1a0c95e08da3e99148408af04657a7f75a', 'EFOCUSQRDS02 - VM has been Deallocated - Warning', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqrds02', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(30, '5837048793ad997ab84b29a6a988b9d90577b85de252a8612083163653d56966', 'EFOCUSNAS05 - VM has been deallocated - Critical', 'Sev4', '/subscriptions/7498780e-1785-465f-9d50-c8ac1e929376/resourcegroups/efocus-qty-compute-rg02/providers/microsoft.compute/virtualmachines/efocusqnas05', 'microsoft.compute/virtualmachines', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 13:26:50', '2026-10-13 13:26:50', '7498780e-1785-465f-9d50-c8ac1e929376', 'efocus-qty-compute-rg02', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(31, '63db90e6c8a12d97f5a00c136c13592da5c632397559319f871f9d6e3bfdae85', 'AGW01-RequestPerHour-ExtraPROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 13:15:05', '2026-10-13 13:15:05', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(32, '54e5024006261dd8d23828f1cb88ae6deb2fc5799e921f5ee2dbb69da921d736', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:59:31', '2026-10-13 12:59:31', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(33, '12343712c766ff7c4eda8f1282ab50486deacd1d09a39918b52acafcfa63f9f7', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:59:25', '2026-10-13 12:59:25', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(34, '50803a18f35f37009ca18df77ef38530abd29fc5266d8978daf65a7047d07a95', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:49:23', '2026-10-13 12:49:23', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(35, 'bbd68ca7713fb6b3288fbb504022f0e8a8904d93c62adf1d598f985b5e1f29de', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:39:35', '2026-10-13 12:39:35', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(36, '3d14acac2609f550c7d67695b293433f250b9c8780101d1d38304ef263ea3f20', 'AGw01-requestPerHour-extra-PROD', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:36:33', '2026-10-13 12:36:33', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(37, '1f037987d23d81b549447ec965118163b31ae0bd56a4b965323c9204f45ae444', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:34:39', '2026-10-13 12:34:39', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(38, 'add9c7ad22177131c6ca422cdc222fd2c203c1838443cc843ec4d38585342d47', 'AGW01-RequestPerHour-ExtraPROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:15:04', '2026-10-13 12:15:04', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(39, '4b1ef7c76a564f8dad0727fda7fa7c186a51d82dbd219d13a607150276c81df2', 'AGw02-requestPerHour-extra-PROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 12:14:08', '2026-10-13 12:14:08', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(40, '50ece6f5ea2fca3c8b8a24a4550f9ce12f007df4e4b62b072c72e6018f1324e8', 'extraplatform-prod-appgw01-401error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 11:58:44', '2026-10-13 11:58:44', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(41, '4c14d2bd1e3905d7f907a885f5eeeeed65e4bd6cb621ed033a826508595d8146', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 11:54:20', '2026-10-13 11:54:20', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(42, 'ab0b26b1d1bb4213a0361babd3836597fc0bbd8b8bc2673fabc2f4c181107bcc', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 11:44:23', '2026-10-13 11:44:23', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(43, '10f32bf942ddfbabb031fd783a764cd253b42b7bb73fafa9217e6253a4161416', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 11:39:33', '2026-10-13 11:39:33', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(44, '76ac69e9da674528e771f6bd0ac7d67e3b6d3c8d1d75c43b4885a30c29419dc3', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 11:34:22', '2026-10-13 11:34:22', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(45, '6a307fae3e7df07a753e07cc4b3b9abc9f41c4e0990d2abe72ee486f1b3a4fbd', 'Defender-SecureScore-Below-85', 'Sev2', '/subscriptions/8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'microsoft.resources/subscriptions', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 10:51:08', '2026-10-13 10:51:08', '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(46, '333b29f109c44539831069471cfe236ddf53aa81c8f8e394f506d4de7d9c17c4', 'Security Score below 80', 'Sev2', '/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a', 'microsoft.resources/subscriptions', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 10:17:44', '2026-10-13 10:17:44', '77839ff3-b3aa-42b0-b490-55830c14bd3a', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(47, '0fdb2862261d2440c22c7f50837ab91864aea1530ac51126457809a18af2afa0', 'AGW01-RequestPerHour-ExtraPROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 09:15:05', '2026-10-13 09:15:05', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(48, '682f62e8a62fd1e67453c5f19407848372a39d0f908b0f6c221bd11fec3030f8', 'AG-request-per-hour-Extra-QA-platform', 'Sev1', '/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourcegroups/extra-qa-rg01/providers/microsoft.network/applicationgateways/extra-qa-appgw-platform01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 08:54:10', '2026-10-13 08:54:10', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(49, 'e26a5ffddf4670f4f9f18895a5224c0f4cf15cbcde37d0171e6c3059dd107a1b', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 08:44:21', '2026-10-13 08:44:21', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(50, '94746a308df22ecd9149297b72df57bf99401cefe2b71ed049dd1f57ba4a63ce', 'Defender-SecureScore-Below-85', 'Sev2', '/subscriptions/c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'microsoft.resources/subscriptions', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 08:39:28', '2026-10-13 08:39:28', 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(51, '0c4f71a9b1d3b949581601927cf5144ecc92469aea8dd83061960d348078d3c2', 'AGW01-RequestPerHour-ExtraPROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 08:22:37', '2026-10-13 08:22:37', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(52, 'bac91bdc31ecba24bbe8b95b0a6607adad3623211309d8ae2f5b7e1d03092473', 'extraplatform-prod-appgw01-403error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 08:05:21', '2026-10-13 08:05:21', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(53, '1723c02a0941ffecc652db36264c0132583bb28734af914c79d968293e62d259', 'AG-request-per-hour-Extra-QA-platform', 'Sev1', '/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourcegroups/extra-qa-rg01/providers/microsoft.network/applicationgateways/extra-qa-appgw-platform01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 07:59:10', '2026-10-13 07:59:10', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(54, 'be3ed261c99bb83f49c31e6f605cf664dc8a6e254dda8614c366a499f2a9f520', 'AG-request-per-hour-Extra-QA-platform', 'Sev1', '/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourcegroups/extra-qa-rg01/providers/microsoft.network/applicationgateways/extra-qa-appgw-platform01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 06:59:12', '2026-10-13 06:59:12', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(55, '6da188fe2d461f8fff898601906ec210d002fd2ef658a859874c2d3def22e0fa', 'Security Alert_EAP', 'Sev2', '/subscriptions/6c389f01-77d0-4c2c-b846-8dfa36987531', 'microsoft.resources/subscriptions', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 06:29:28', '2026-10-13 06:29:28', '6c389f01-77d0-4c2c-b846-8dfa36987531', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(56, 'ccd97130a46744abe44bacdde83557de022ebd0b676ab10ed52540f3a60d8827', 'Defender-SecureScore-Below-80', 'Sev2', '/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c', 'microsoft.resources/subscriptions', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 06:04:26', '2026-10-13 06:04:26', 'c12d79d2-0655-4caa-839e-fc47e019271c', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(57, '27d9c1d7dbf216aa855c997041ccbaa35d06b687e01eb60ffc4464b3d2fa0b05', 'extraplatform-prod-appgw02-403Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 05:58:18', '2026-10-13 05:58:18', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(58, '272a57e4188d30e3e0134613be83f0fc2eb37e1268fda8bddfcab9613f19c5c1', 'extraplatform-prod-appgw02-403Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 05:43:20', '2026-10-13 05:43:20', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(59, '24d6a98c31dfd134b6456036d7472ee55e3a98b855d669e45f0d1c670a7bc97f', 'extraplatform-prod-appgw02-503Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 05:34:35', '2026-10-13 05:34:35', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(60, '0eeafdafe6d5ed3469b2d9e13a948dd59db639d0357367fa4fbc18bddd9e245d', 'Defender-SecureScore-Below-85', 'Sev2', '/subscriptions/1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'microsoft.resources/subscriptions', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 05:30:35', '2026-10-13 05:30:35', '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(61, '599726feb04886523747526e86b0c7a6020751cef9aa82060a6b83a2a671c682', 'extraplatform-prod-appgw02-403Error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 05:05:50', '2026-10-13 05:05:50', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(62, '7b29309b98f741b8b5da46dd443bc47638200d355d45a82ae9b4b77489712062', 'extraplatform-prod-AllWindows-DiskAlert', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.operationalinsights/workspaces/extraplatform-prod-loganalytics', 'microsoft.operationalinsights/workspaces', 'Fired', 'New', 'Log Analytics', 'Log search', '2026-10-13 03:53:02', '2026-10-13 03:53:02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'Suppressed', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(63, '75aa69a45f1ca6ac84b3d718d863a75061015f181fbbdfc1ec4c6c61c82178c4', 'extraplatform-prod-AllWindows-DiskAlert', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.operationalinsights/workspaces/extraplatform-prod-loganalytics', 'microsoft.operationalinsights/workspaces', 'Fired', 'New', 'Log Analytics', 'Log search', '2026-10-13 03:47:51', '2026-10-13 03:47:51', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(64, '7fc5cba3c90bf9cc1f3d327e6a5cd4fcaa32e92e6abc63f38145553a9771ff93', 'boschrewrdshttpreqappsvc', 'Sev3', '/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourcegroups/iboschresources/providers/microsoft.web/sites/iboschcouponrewardswcfmobile', 'microsoft.web/sites', 'Fired', 'New', 'Platform', 'Metrics', '2026-10-13 02:43:14', '2026-10-13 02:43:14', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschresources', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(65, 'c7df65195b1514d888c9549b00e8aec813c2fd14047492ca03ba7be55b9142a1', 'AGw02-requestPerHour-extra-PROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 02:14:09', '2026-10-13 02:14:09', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(66, 'be37e10ab2f324be1bbaf94cac1df3771bb57a7f87b68295a96a8b81f8cd4d41', 'AGw02-requestPerHour-extra-PROD', 'Sev2', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 01:14:08', '2026-10-13 01:14:08', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(67, 'efd03d3d5eca1d0adc538dc20e8c4c20b4472ac47950e6cadcb86847b616aa1a', 'BoschRewards-Info_Planned Monthly DTU update activity', 'Sev4', '/subscriptions/dca0a650-722e-4755-bbce-f2b7f6ed0f9f/resourcegroups/iboschresources/providers/microsoft.sql/servers/iboschdbs/elasticpools/iboschelastic', 'microsoft.sql/servers/elasticpools', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-13 00:31:08', '2026-10-13 00:31:08', 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'iboschresources', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(68, '208e0cbfd03853ec030ee6cb9ad07928fd2d48e58f4f4aecfaffa6eb2e88d6b8', 'Rate-Limited for admin and customer login', 'Sev0', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw02', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-13 00:03:02', '2026-10-13 00:03:02', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(69, 'c6cf7515fc7bf5a4153a05eae3c7679dbcc4950cf2257ffcdfaac7bd4cec2261', 'AAextraplatform-Prod-Servicehealth', 'Sev4', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'microsoft.resources/subscriptions', 'Fired', 'New', 'ServiceHealth', 'Service health', '2026-10-12 23:42:37', '2026-10-12 23:42:37', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(70, 'a54d24e0f83ec8f22038f59d8dfaa06d9638c046c8a5067f41294ef5d45866f2', 'AA Extra- Dev - Service Health alert', 'Sev4', '/subscriptions/77839ff3-b3aa-42b0-b490-55830c14bd3a', 'microsoft.resources/subscriptions', 'Fired', 'New', 'ServiceHealth', 'Service health', '2026-10-12 23:39:58', '2026-10-12 23:39:58', '77839ff3-b3aa-42b0-b490-55830c14bd3a', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(71, '877e1875d7b34b19506d6862b19279d1e099b189d1c1a45df861d6622aed3581', 'AAextraplatform-QA-Servicehealth', 'Sev4', '/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758', 'microsoft.resources/subscriptions', 'Fired', 'New', 'ServiceHealth', 'Service health', '2026-10-12 23:35:34', '2026-10-12 23:35:34', 'c080fc5b-797d-45db-b089-01cc05f9a758', NULL, 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(72, '9e3ce32eadf6e5bf68a735cb010599e816c9736a7ff3e1d3499d51cebd377e93', 'extraplatform-prod-appgw01-403error', 'Sev1', '/subscriptions/1c119a3d-5a21-4652-8e7f-e8b55c8635f5/resourcegroups/extraplatform-prod-emea-rg01/providers/microsoft.network/applicationgateways/extraplatform-prod-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-12 22:08:32', '2026-10-12 22:08:32', '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'extraplatform-prod-emea-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(73, '1e0b928447a2e7e7595bc5d036db0123ab2a88ad9782e89a4348c77e8be90fb9', 'Exim - Prod-Administrative activities', 'Sev4', '/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourcegroups/exim-interface-prd/providers/microsoft.apimanagement/service/exim-interface-prd-apim', 'microsoft.apimanagement/service', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-12 20:36:47', '2026-10-12 20:36:47', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'exim-interface-prd', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(74, '9999cb5bda104e4e94d6593f4b703a1a63db9b5233371230e1cf982001f9788f', 'Exim- Administrative Activity logs', 'Sev4', '/subscriptions/c12d79d2-0655-4caa-839e-fc47e019271c/resourcegroups/exim-interface-prd/providers/microsoft.apimanagement/service/exim-interface-prd-apim', 'microsoft.apimanagement/service', 'Fired', 'New', 'ActivityLog Administrative', 'Activity log', '2026-10-12 20:36:47', '2026-10-12 20:36:47', 'c12d79d2-0655-4caa-839e-fc47e019271c', 'exim-interface-prd', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01'),
(75, 'dd4498f73ee5ce9c002b7d0d3b3970893efd4b3798c266d4c5fca9cecd6f47f8', 'extraplatform-qa-appgw01-404error', 'Sev3', '/subscriptions/c080fc5b-797d-45db-b089-01cc05f9a758/resourcegroups/extra-qa-rg01/providers/microsoft.network/applicationgateways/extraplatform-qa-appgw01', 'microsoft.network/applicationgateways', 'Fired', 'New', 'Log Alerts V2', 'Log search', '2026-10-12 19:01:02', '2026-10-12 19:01:02', 'c080fc5b-797d-45db-b089-01cc05f9a758', 'extra-qa-rg01', 'None', '2026-10-13 11:51:01', '2026-10-13 11:51:01');

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
('19fb36f7-0105-4d68-be29-02c3934a2abc', 'AA-GPM-BoschCloudPrinting-QA', NULL, 'QA', NULL, 'Healthy', 89.63, 1511.27, '2026-10-09 19:41:26'),
('1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'MA-SKX-eXtra-Production-Stage-Prod', NULL, 'PROD', NULL, 'Warning', 84.92, 5863.72, '2026-10-09 19:41:26'),
('1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'AA-ICO-IN-Azure-QA', NULL, NULL, NULL, 'Warning', 80.43, 261.75, '2026-09-26 15:37:01'),
('219ccbf6-e35b-4758-961a-ede690403d86', 'Hamro-Bosch', NULL, NULL, NULL, 'Healthy', 86.96, 132.08, '2026-09-26 15:39:07'),
('2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'AA-SMS3-NA-PBAP-Prod', NULL, NULL, NULL, 'Warning', 80.95, 1694.31, '2026-09-26 15:37:59'),
('4172676b-8a9c-44e0-b2ab-727057b691b7', 'OT-RBIN-PJ-DIGS-MICONIC-Prod', NULL, NULL, NULL, 'Healthy', 92.00, 567.71, '2026-09-26 15:38:31'),
('50d41c25-8668-4497-8925-766161f0cddb', 'OT-RBIN-PJ-DIGS-MICONIC-QA', NULL, NULL, NULL, 'Healthy', 85.19, 400.71, '2026-09-26 15:38:15'),
('5784d84d-05ea-4a9c-b625-7d0183e9240b', 'AA-BDO-IN-BoschRewards-QA', NULL, NULL, NULL, 'Healthy', 99.67, 372.00, '2026-09-26 15:38:36'),
('5b46ccc6-604b-4c5c-81c2-96b133061773', 'AA-SMS3-NA-BAP5.0-Dev', NULL, NULL, NULL, 'Healthy', 87.29, 503.93, '2026-09-26 15:37:52'),
('6b8e63a8-5397-4d83-9b49-164067a4e892', 'XC_Production_XC/ENG-Bp_866512', NULL, NULL, NULL, 'Degraded', 0.00, 0.00, '2026-09-26 15:36:35'),
('6c389f01-77d0-4c2c-b846-8dfa36987531', 'EAP Sandbox', NULL, NULL, NULL, 'Healthy', 90.51, 148.75, '2026-09-26 15:39:20'),
('7498780e-1785-465f-9d50-c8ac1e929376', 'AA-ICO-IN-eFOCuS-QA', NULL, 'QA', NULL, 'Healthy', 91.47, 426.17, '2026-10-09 19:41:26'),
('77839ff3-b3aa-42b0-b490-55830c14bd3a', 'MA-SKX-eXtra-Dev-Stage-Dev', NULL, 'DEV', NULL, 'Warning', 78.57, 1167.13, '2026-10-09 19:41:26'),
('8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'AA-ICO-IN-AA-Dashboard-Prod', NULL, NULL, NULL, 'Healthy', 87.12, 1494.64, '2026-09-26 15:37:32'),
('8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'Hamro Bosch Prod', NULL, NULL, NULL, 'Warning', 82.61, 323.00, '2026-09-26 15:39:13'),
('a77eebf3-17a1-4378-90bf-2c96b1028137', 'AA-SMS3-NA-QA', NULL, NULL, NULL, 'Healthy', 90.48, 1370.51, '2026-09-26 15:38:03'),
('c080fc5b-797d-45db-b089-01cc05f9a758', 'MA-SKX-eXtra-Promo-Stage-QA', NULL, 'QA', NULL, 'Healthy', 87.57, 3900.99, '2026-10-09 19:41:26'),
('c12d79d2-0655-4caa-839e-fc47e019271c', 'OT-RBIN-GS-EXIMPortal-Prod', NULL, NULL, NULL, 'Degraded', 69.05, 465.59, '2026-09-26 15:38:42'),
('c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'AA-ICO-IN-eFOCuS-Prod', NULL, 'PROD', NULL, 'Healthy', 92.26, 2391.48, '2026-10-09 19:41:26'),
('d26dd531-2174-4c45-a240-032e0d05dfa0', 'AA-GPM-BoschCloudPrinting-Prod', NULL, 'PROD', NULL, 'Warning', 75.85, 4243.84, '2026-10-09 19:41:26'),
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
-- Table structure for table `business_units`
--

CREATE TABLE `business_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `business_units`
--

INSERT INTO `business_units` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'MA-BDO', 'MA-BDO Team - Business, App & Security', '2026-10-08 10:49:27', '2026-10-08 10:49:27'),
(2, 'RBIN', 'RBIN Projects', '2026-10-08 10:55:55', '2026-10-08 10:55:55'),
(3, 'Others', 'Other Subscriptions', '2026-10-08 14:02:58', '2026-10-08 14:02:58'),
(4, 'MA', 'Mobility Aftermarket', '2026-10-10 10:17:30', '2026-10-10 10:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('ccod-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6', 'i:1;', 1792649795),
('ccod-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6:timer', 'i:1792649795;', 1792649795),
('ccod-cache-settings.App\\Settings\\StylingSettings', 's:655:\"O:28:\"App\\Settings\\StylingSettings\":15:{s:10:\"brand_name\";s:4:\"CCOD\";s:10:\"logo_light\";s:53:\"branding/8Xc3AklNf8s3Oq4838h4Cy7QIzdAakWt8YeVCYZo.png\";s:9:\"logo_dark\";s:53:\"branding/Wn3DHqP0ClHFSqry3kLpzSTskBH8bbm2OD4Av4A9.png\";s:7:\"favicon\";s:53:\"branding/DvMn4qkpi5yeO7K0yIde37uYBTepTIFn85iERIBM.png\";s:13:\"primary_color\";s:5:\"amber\";s:13:\"success_color\";s:5:\"green\";s:13:\"warning_color\";s:3:\"red\";s:12:\"danger_color\";s:3:\"red\";s:10:\"info_color\";s:4:\"cyan\";s:10:\"gray_color\";s:4:\"gray\";s:13:\"custom_colors\";a:0:{}s:13:\"corner_radius\";s:8:\"0.375rem\";s:16:\"light_mode_theme\";s:4:\"zinc\";s:15:\"dark_mode_theme\";s:5:\"black\";s:24:\"_settingsLoadedFromCache\";b:1;}\";', 2107924339);

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
(2, 'Project Details', 'project-details', 'This is a test document created to store project details for the **subscription.**\n\n# Header 1\n## Header 2\n### Header 3\n#### Header 4\n##### Header 5\n##### Header 6\n\n*Some text in italics*\n\n~~Strikethrough text~~\n\n> This is a quote.\n\nUnordered list:\n\n* Hello 1\n* Hello 2\n* Hello 3\n\nOrdered list:\n\n1. Hello 1\n2. Hello 2\n3. Hello 3\n\n| Column 1 | column 2 |\n|--|--|\n| Row 1 | Row 2 |\n\n```\nprint(\"Hello! World\");\n```\n\n**Link:**\n\n[Link to google search](https://google.com)\n\nImage:\n\n\n![](http://localhost:8000/storage/PTFgRTOsdc0IfJIYZRKTawhtOHRurIiKs1yI5BGc.png)', 1, '2026-10-09 16:02:31', '2026-10-13 22:44:49'),
(3, 'New document for Audit test', 'audit-test-doc', 'This is a new document created merely for audit testing.', 1, '2026-10-10 02:47:15', '2026-10-10 02:47:15'),
(4, 'Test article again', 'test-article-again', 'This is ', 1, '2026-10-11 03:30:11', '2026-10-11 03:30:11'),
(5, 'Test document creation', 'test-document-creation', 'This document was created to check session persistence', 1, '2026-10-13 02:13:15', '2026-10-13 02:13:15'),
(7, 'Role permission test note', 'role-permission-test-note', 'This note was created to test the role permission settings', 5, '2026-10-17 04:06:38', '2026-10-17 04:06:38'),
(8, 'Commit before notification queue implementation', 'commit-before-notification-queue-implementation', 'da2dfa870025f8420f7e01a830b2dc0e0dacab64 - Show assigned users when blocking role deletion ', 1, '2026-10-17 11:59:13', '2026-10-17 11:59:13'),
(9, 'Starting the notification worker', 'starting-the-notification-worker', 'php artisan queue:work database', 1, '2026-10-17 12:03:02', '2026-10-17 12:03:02'),
(10, 'Commit before auth and fetching check', 'commit-before-auth-and-fetching-check', 'This commit 145f4c5d1bbccd6caf2bb46956d6f8f936b52821 implements the notification queue and is right before the authorization and db fetch was improvised.', 1, '2026-10-17 12:25:06', '2026-10-17 12:25:06'),
(11, 'View role page fix commit', 'view-role-page-fix-commit', 'a20f0f6c9fcb64cccec0b6fa58e5ee07d80171c5', 1, '2026-10-17 13:55:55', '2026-10-17 13:55:55'),
(12, 'Commit before auth remodeling', 'commit-before-auth-remodeling', '8293c597b2ef390b34df47b5fa5cb46073513726\nQueue trigger worker added', 1, '2026-10-20 22:51:23', '2026-10-20 22:51:23');

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
(31, '2026_10_10_170000_create_user_subscription_access_overrides_table', 20),
(32, '2026_10_11_000000_create_pill_colors_table', 21),
(33, '2026_10_11_000000_create_settings_table', 22),
(34, '2026_10_11_000001_create_styling_settings', 23),
(35, '2026_10_11_000002_add_custom_colors_to_styling_settings', 24),
(36, '2026_10_11_000003_add_dark_mode_theme_to_styling_settings', 25),
(37, '2026_10_11_000004_normalize_notification_critical_severity', 25),
(38, '2026_10_11_000005_normalize_notification_rule_critical_severity', 25),
(39, '2026_10_11_000006_add_light_mode_theme_to_styling_settings', 26),
(40, '2026_10_12_000001_create_maintenance_settings', 27),
(41, '2026_10_13_000001_create_subscription_web_urls_table', 28),
(42, '2026_10_13_000002_add_is_active_to_subscription_web_urls_table', 29),
(43, '2026_10_13_000002_create_azure_alerts_table', 30),
(44, '2026_10_14_010000_rename_teams_to_business_units', 31),
(45, '2026_10_15_000001_create_rbac_tables', 32),
(46, '2026_10_15_010000_implement_resource_access_hierarchy', 33),
(47, '2026_10_15_020000_add_tags_to_project_subscriptions', 34),
(48, '2026_10_15_030000_add_documentation_permissions', 35),
(49, '2026_10_15_040000_create_tab_access_settings', 35),
(50, '2026_10_15_040000_remove_is_active_from_projects_table', 36),
(51, '2026_10_16_000001_sync_rbac_permissions_to_spec', 37),
(52, '2026_10_16_000002_seed_updated_rbac_roles', 37),
(53, '2026_10_16_000003_backfill_implicit_view_permissions', 37),
(54, '2026_10_16_000004_seed_managed_permission_catalog', 37),
(55, '2026_10_16_000005_repair_managed_rbac_permissions', 38),
(56, '2026_10_17_000001_repair_system_role_permission_assignments', 38),
(57, '2026_10_18_000001_split_access_request_permissions', 39),
(58, '2026_10_21_000001_create_user_groups_tables', 40),
(59, '2026_10_21_000002_add_user_group_to_access_requests', 40),
(60, '2026_10_21_000003_remove_user_business_unit_relationship', 41),
(61, '2026_10_21_000004_remove_unused_tables', 42);

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
(8, 'user_added_to_team', 0, 'users', '[1]', NULL, NULL, 'general', 'info', NULL, NULL, NULL, 1, '2026-10-10 05:51:20', '2026-10-11 08:53:34'),
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
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `slug`, `category`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Open home route', 'dashboard.view', 'Dashboard', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(2, 'View dashboard metrics', 'dashboard.metrics.view', 'Dashboard', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(3, 'View dashboard widgets', 'dashboard.widgets.view', 'Dashboard', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(4, 'Export dashboard data', 'dashboard.export', 'Dashboard', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(5, 'View projects', 'projects.view', 'Projects', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(6, 'Create projects', 'projects.create', 'Projects', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(7, 'Update/Edit projects', 'projects.edit', 'Projects', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(8, 'Delete projects', 'projects.delete', 'Projects', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(9, 'View applications', 'applications.view', 'Projects & Applications', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(10, 'Create applications', 'applications.create', 'Projects & Applications', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(11, 'Edit applications', 'applications.edit', 'Projects & Applications', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(12, 'Delete applications', 'applications.delete', 'Projects & Applications', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(13, 'View environments', 'environments.view', 'Environments & Subscriptions', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(14, 'Create environments', 'environments.create', 'Environments & Subscriptions', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(15, 'Edit environments', 'environments.edit', 'Environments & Subscriptions', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(16, 'Delete environments', 'environments.delete', 'Environments & Subscriptions', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(17, 'View subscription', 'subscriptions.view', 'Subscriptions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(18, 'Create subscription', 'subscriptions.create', 'Subscriptions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(19, 'Edit subscription', 'subscriptions.edit', 'Subscriptions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(20, 'Delete subscription', 'subscriptions.delete', 'Subscriptions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(21, 'Assign subscriptions to environments', 'subscriptions.assign_environment', 'Environments & Subscriptions', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(22, 'Assign subscriptions to business units', 'subscriptions.assign_business_unit', 'Environments & Subscriptions', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(23, 'View cost', 'cost.view', 'Cost', NULL, 1, '2026-10-15 00:39:28', '2026-10-17 06:17:22'),
(24, 'View cost breakdown', 'cost.breakdown.view', 'Cost', NULL, 1, '2026-10-15 00:39:28', '2026-10-17 06:20:52'),
(25, 'Export cost', 'cost.export', 'Cost', NULL, 1, '2026-10-15 00:39:28', '2026-10-17 06:20:52'),
(26, 'Manage cost settings', 'cost.settings.manage', 'Cost', NULL, 1, '2026-10-15 00:39:28', '2026-10-17 06:49:12'),
(27, 'View resources', 'resources.view', 'Resources', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(28, 'View resource metrics', 'resources.metrics.view', 'Resources', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(29, 'Export resources', 'resources.export', 'Resources', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(30, 'View alerts', 'alerts.view', 'Alerts', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(31, 'Acknowledge alerts', 'alerts.acknowledge', 'Monitoring & Alerts', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(32, 'Close alerts', 'alerts.close', 'Monitoring & Alerts', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(33, 'Configure alert settings', 'alerts.settings.manage', 'Monitoring & Alerts', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(34, 'Trigger monitoring checks', 'monitoring.checks.trigger', 'Monitoring & Alerts', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(35, 'View monitoring history', 'monitoring.history.view', 'Monitoring & Alerts', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(36, 'View tickets', 'tickets.view', 'Tickets', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(37, 'Create tickets', 'tickets.create', 'Tickets', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(38, 'Update/Edit tickets', 'tickets.update', 'Tickets', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(39, 'Assign tickets', 'tickets.assign', 'Tickets', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(40, 'Close tickets', 'tickets.close', 'Tickets', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(41, 'Export tickets', 'tickets.export', 'Tickets', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(42, 'View URLs', 'urls.view', 'URLs', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(43, 'Add URL', 'urls.create', 'URLs', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(44, 'Update/Edit URL', 'urls.edit', 'URLs', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(45, 'Delete URL', 'urls.delete', 'URLs', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(46, 'Trigger URL checks', 'urls.check.trigger', 'Web URL Monitoring', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(47, 'View URL check history', 'urls.history.view', 'Web URL Monitoring', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(48, 'View security', 'security.view', 'Security', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(49, 'View security scores', 'security.scores.view', 'Security', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(50, 'Configure security settings', 'security.settings.manage', 'Security', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(51, 'View users', 'users.view', 'Users', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(52, 'Create users', 'users.create', 'Users', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(53, 'Update/Edit users', 'users.edit', 'Users', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(54, 'Disable users', 'users.disable', 'Users', NULL, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(55, 'Manage access requests', 'users.roles.assign', 'Roles & Permissions', NULL, 0, '2026-10-15 00:39:28', '2026-10-20 00:16:07'),
(56, 'Assign scopes', 'users.scopes.assign', 'Users', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(57, 'View roles and permissions', 'roles.view', 'Roles & Permissions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(58, 'Create roles', 'roles.create', 'Roles & Permissions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(59, 'Update/Edit roles', 'roles.edit', 'Roles & Permissions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(60, 'Delete roles', 'roles.delete', 'Roles & Permissions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(61, 'Assign permissions', 'roles.permissions.assign', 'Roles & Permissions', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(62, 'Assign role scopes', 'roles.scopes.assign', 'Roles & Permissions', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(63, 'View system settings', 'system.settings.view', 'Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(64, 'Modify system settings', 'system.settings.manage', 'Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(65, 'Manage integrations', 'system.integrations.manage', 'Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(66, 'Manage audit logs', 'system.audit.manage', 'Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(67, 'Manage system configuration', 'system.configuration.manage', 'Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(68, 'Manage authorization configuration', 'authorization.configuration.manage', 'Super Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(69, 'Manage built-in roles', 'authorization.system_roles.manage', 'Super Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(70, 'Manage system-level security settings', 'authorization.security.manage', 'Super Administration', NULL, 1, '2026-10-15 00:39:28', '2026-10-16 06:25:44'),
(71, 'View documents', 'documentation.view', 'Documentation', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(72, 'Create documents', 'documentation.create', 'Documentation', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(73, 'Edit documents', 'documentation.edit', 'Documentation', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(74, 'Delete documents', 'documentation.delete', 'Documentation', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(75, 'Manual Sync', 'subscriptions.sync', 'Subscriptions', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(76, 'Export alerts', 'alerts.export', 'Alerts', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(77, 'Manual Sync', 'alerts.sync', 'Alerts', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(78, 'Manual Sync', 'resources.sync', 'Resources', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(79, 'Export security', 'security.export', 'Security', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(80, 'Manual Sync', 'security.sync', 'Security', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(81, 'Export URLs', 'urls.export', 'URLs', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(82, 'Delete users', 'users.delete', 'Users', NULL, 1, '2026-10-17 09:40:29', '2026-10-17 09:40:29'),
(83, 'View access requests', 'users.access_requests.view', 'Access Requests', NULL, 1, '2026-10-17 14:33:52', '2026-10-17 14:33:52'),
(84, 'Take decision', 'users.access_requests.decide', 'Access Requests', NULL, 1, '2026-10-17 14:33:52', '2026-10-17 14:33:52');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 5),
(1, 9),
(1, 13),
(1, 17),
(1, 23),
(1, 24),
(1, 27),
(1, 28),
(1, 30),
(1, 35),
(1, 36),
(1, 42),
(1, 47),
(1, 48),
(1, 49),
(1, 71),
(2, 1),
(2, 2),
(2, 3),
(2, 5),
(2, 9),
(2, 13),
(2, 17),
(2, 23),
(2, 24),
(2, 25),
(2, 27),
(2, 28),
(2, 29),
(2, 30),
(2, 31),
(2, 32),
(2, 34),
(2, 35),
(2, 36),
(2, 37),
(2, 38),
(2, 39),
(2, 40),
(2, 42),
(2, 46),
(2, 47),
(2, 48),
(2, 49),
(2, 71),
(3, 1),
(3, 2),
(3, 3),
(3, 5),
(3, 9),
(3, 13),
(3, 17),
(3, 23),
(3, 24),
(3, 27),
(3, 28),
(3, 30),
(3, 35),
(3, 36),
(3, 37),
(3, 42),
(3, 47),
(3, 48),
(3, 49),
(3, 71),
(4, 5),
(4, 17),
(4, 23),
(4, 25),
(4, 27),
(4, 29),
(4, 30),
(4, 36),
(4, 41),
(4, 42),
(4, 44),
(4, 48),
(4, 51),
(4, 57),
(4, 76),
(4, 79),
(4, 83),
(4, 84),
(5, 5),
(5, 6),
(5, 7),
(5, 8),
(5, 17),
(5, 19),
(5, 20),
(5, 23),
(5, 24),
(5, 25),
(5, 26),
(5, 27),
(5, 29),
(5, 30),
(5, 36),
(5, 37),
(5, 38),
(5, 39),
(5, 40),
(5, 41),
(5, 42),
(5, 43),
(5, 44),
(5, 45),
(5, 48),
(5, 51),
(5, 52),
(5, 53),
(5, 57),
(5, 58),
(5, 59),
(5, 60),
(5, 71),
(5, 72),
(5, 73),
(5, 74),
(5, 75),
(5, 76),
(5, 77),
(5, 78),
(5, 79),
(5, 80),
(5, 81),
(5, 82),
(5, 83),
(5, 84);

-- --------------------------------------------------------

--
-- Table structure for table `pill_colors`
--

CREATE TABLE `pill_colors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `color` varchar(30) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pill_colors`
--

INSERT INTO `pill_colors` (`id`, `key`, `color`, `created_at`, `updated_at`) VALUES
(1, 'pending', 'warning', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(2, 'approved', 'success', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(3, 'rejected', 'danger', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(4, 'revoked', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(5, 'team', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(6, 'subscription', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(7, 'global_owner', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(8, 'global_contributor', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(9, 'global_reader', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(10, 'restricted_owner', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(11, 'restricted_contributor', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(12, 'restricted_reader', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(13, 'healthy', 'success', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(14, 'degraded', 'warning', '2026-10-11 10:18:02', '2026-10-11 10:46:42'),
(15, 'critical', 'danger', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(16, 'unknown', 'gray', '2026-10-11 10:18:02', '2026-10-11 10:18:02'),
(17, 'viewer', 'gray', '2026-10-21 00:56:37', '2026-10-21 00:56:37'),
(18, 'operator', 'gray', '2026-10-21 00:56:37', '2026-10-21 00:56:37'),
(19, 'customer', 'gray', '2026-10-21 00:56:37', '2026-10-21 00:56:37'),
(20, 'admin', 'gray', '2026-10-21 00:56:37', '2026-10-21 00:56:37'),
(21, 'super_admin', 'gray', '2026-10-21 00:56:37', '2026-10-21 00:56:37');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `business_unit_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(3, 1, 'eFOCuS', 'eFOCuS application environments.', '2026-10-15 01:51:36', '2026-10-17 12:57:30'),
(4, 4, 'Extra', 'Extra application environments.', '2026-10-15 01:51:36', '2026-10-15 09:27:48'),
(6, 4, 'Cloud printing', 'MA Cloud printing project', '2026-10-15 09:07:04', '2026-10-15 09:07:04'),
(7, 1, 'Hamro Bosch', 'Hamro Bosch application for Nepal', '2026-10-15 10:22:23', '2026-10-17 12:57:30'),
(8, 1, 'Miconic', 'MA-BDO Miconic Application', '2026-10-15 10:31:35', '2026-10-17 12:57:30'),
(9, 1, 'Bosch Rewards', 'MA-BDO Bosch Rewards Application', '2026-10-15 10:33:19', '2026-10-17 12:57:30'),
(10, 1, 'CMS', 'Claims Management Applications for MA-BDO', '2026-10-15 10:34:04', '2026-10-17 12:57:30'),
(11, 1, 'Dashboard', 'MA-BDO AA Dashboard', '2026-10-15 10:34:34', '2026-10-17 12:57:30'),
(12, 4, 'Autoparts', 'MA Autoparts Application', '2026-10-15 10:39:22', '2026-10-15 10:39:22'),
(13, 2, 'InsiderLens', 'InsiderLens Application', '2026-10-15 10:49:34', '2026-10-15 10:49:34'),
(14, 2, 'Exim Portal', 'Exim Portal', '2026-10-15 10:50:31', '2026-10-15 10:50:31'),
(15, 2, 'Associate Connect', 'Associate Connect app for Bosch employees', '2026-10-15 10:52:33', '2026-10-15 10:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_subscriptions`
--

CREATE TABLE `project_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(100) NOT NULL,
  `environment` varchar(20) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_subscriptions`
--

INSERT INTO `project_subscriptions` (`id`, `project_id`, `subscription_id`, `environment`, `tags`, `created_at`, `updated_at`) VALUES
(2, 4, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'prod', '[]', '2026-10-15 01:51:36', '2026-10-15 09:27:48'),
(3, 3, '7498780e-1785-465f-9d50-c8ac1e929376', 'qa', '[\"qa\"]', '2026-10-15 01:51:36', '2026-10-15 01:51:36'),
(4, 4, '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'dev', '[]', '2026-10-15 01:51:36', '2026-10-15 09:27:48'),
(5, 4, 'c080fc5b-797d-45db-b089-01cc05f9a758', 'qa', '[]', '2026-10-15 01:51:36', '2026-10-15 09:27:48'),
(6, 3, 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'prod', '[\"prod\"]', '2026-10-15 01:51:36', '2026-10-15 01:51:36'),
(8, 7, '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', NULL, '[\"prod\"]', '2026-10-15 10:29:29', '2026-10-15 10:29:29'),
(9, 7, '219ccbf6-e35b-4758-961a-ede690403d86', NULL, '[\"qa\"]', '2026-10-15 10:29:29', '2026-10-15 10:29:29'),
(10, 6, 'd26dd531-2174-4c45-a240-032e0d05dfa0', NULL, '[\"prod\"]', '2026-10-15 10:30:25', '2026-10-15 10:30:25'),
(11, 6, '19fb36f7-0105-4d68-be29-02c3934a2abc', NULL, '[\"prod\"]', '2026-10-15 10:30:26', '2026-10-15 10:30:26'),
(12, 8, '4172676b-8a9c-44e0-b2ab-727057b691b7', NULL, '[\"prod\"]', '2026-10-15 10:32:26', '2026-10-15 10:32:26'),
(13, 8, '50d41c25-8668-4497-8925-766161f0cddb', NULL, '[\"qa\"]', '2026-10-15 10:32:26', '2026-10-15 10:32:26'),
(14, 8, 'fc152cdb-dc60-44e7-bbee-412c719cb49a', NULL, '[\"dev\"]', '2026-10-15 10:32:26', '2026-10-15 10:32:26'),
(15, 9, 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', NULL, '[\"prod\"]', '2026-10-15 10:33:19', '2026-10-15 10:33:19'),
(16, 9, '5784d84d-05ea-4a9c-b625-7d0183e9240b', NULL, '[\"qa\"]', '2026-10-15 10:33:19', '2026-10-15 10:33:19'),
(17, 10, 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', NULL, '[\"prod\"]', '2026-10-15 10:34:04', '2026-10-15 10:34:04'),
(18, 10, '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', NULL, '[\"qa\"]', '2026-10-15 10:34:04', '2026-10-15 10:34:04'),
(19, 11, '8a8c77f4-cb44-47ad-b56a-6682d97b36bb', NULL, '[\"prod\"]', '2026-10-15 10:34:34', '2026-10-15 10:34:34'),
(20, 12, '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', NULL, '[\"prod\"]', '2026-10-15 10:39:22', '2026-10-15 10:39:22'),
(21, 12, 'a77eebf3-17a1-4378-90bf-2c96b1028137', NULL, '[\"qa\"]', '2026-10-15 10:39:22', '2026-10-15 10:39:22'),
(22, 12, '5b46ccc6-604b-4c5c-81c2-96b133061773', NULL, '[\"dev\"]', '2026-10-15 10:39:22', '2026-10-15 10:39:22'),
(23, 13, '12db631f-5ab4-4237-9fa6-6d92de7a53e8', NULL, '[\"prod\"]', '2026-10-15 10:49:34', '2026-10-15 10:49:34'),
(24, 14, 'c12d79d2-0655-4caa-839e-fc47e019271c', NULL, '[\"prod\"]', '2026-10-15 10:50:31', '2026-10-15 10:50:31'),
(25, 15, '02ab4266-3406-48d4-bfbc-76605dde11d7', NULL, '[\"prod\"]', '2026-10-15 10:52:33', '2026-10-15 10:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_user_group`
--

CREATE TABLE `project_user_group` (
  `user_group_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_user_group`
--

INSERT INTO `project_user_group` (`user_group_id`, `project_id`, `created_at`, `updated_at`) VALUES
(1, 3, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 4, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 6, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 7, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 8, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 9, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 10, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 11, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 12, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 13, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 14, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(1, 15, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(2, 3, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 4, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 6, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 7, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 8, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 9, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 10, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 11, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 12, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 13, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 14, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(2, 15, '2026-10-21 02:12:11', '2026-10-21 02:12:11');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_system_role` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `is_system_role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Viewer', 'viewer', 'Read-only access to assigned resources.', 1, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(2, 'Operator', 'operator', 'Operational access to assigned resources.', 1, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(3, 'Customer', 'customer', 'Customer-facing access to assigned resources and tickets.', 1, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(4, 'Admin', 'admin', 'Administrative access to assigned resources.', 1, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(5, 'Super Admin', 'super_admin', 'Authorization and system administration.', 1, 1, '2026-10-15 00:39:28', '2026-10-15 00:39:28'),
(8, 'Test role', 'test-role', 'This is a test role', 0, 1, '2026-10-17 10:25:05', '2026-10-17 10:25:05');

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
('5qf9rBHmUzSvxXx5fGTakYNSLDtGpDoVdaDK1DIP', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUzNzdnV5VjA0dUxyTDQ3OUFuTG5lbjZSRzdFMDBkUzhBTDJ5eFF1QSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9ub3RpZmljYXRpb25zL2RhdGEiO3M6NToicm91dGUiO3M6MTk6Im5vdGlmaWNhdGlvbnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1792649576),
('Gm86yTgcx6DwIcCtSvmF8l3iWQzQh1nXTjvEbzp7', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoieXIxUUVKUXlYR3gwUDVoYUxlYW9ycDg2SDdVMHJzR1JpMEVwSTJrMyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjIxOiJodHRwOi8vbG9jYWxob3N0OjgwMDAiO3M6NToicm91dGUiO3M6MzA6ImZpbGFtZW50LmFkbWluLnBhZ2VzLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjQ6ImVmN2E3YjEyMTg3ZjJmYTYyN2Q4NGIyNDQ4ZGY1NDY2ODNlYzQyZTFjN2E3ZGEwYWRlNDQyYWZhZjlmYjY3ZTUiO3M6NjoidGFibGVzIjthOjE6e3M6NDA6IjM1MzM2YTgwODc3ODgzNGY4OTQxNzQ3MTEyYzI2NDNlX2NvbHVtbnMiO2E6NTp7aTowO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6Im5hbWUiO3M6NToibGFiZWwiO3M6NDoiTmFtZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoic2x1ZyI7czo1OiJsYWJlbCI7czo0OiJTbHVnIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MjthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNzoicGVybWlzc2lvbnNfY291bnQiO3M6NToibGFiZWwiO3M6MTE6IlBlcm1pc3Npb25zIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoiaXNfc3lzdGVtX3JvbGUiO3M6NToibGFiZWwiO3M6MTE6IlN5c3RlbSByb2xlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo5OiJpc19hY3RpdmUiO3M6NToibGFiZWwiO3M6OToiSXMgYWN0aXZlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX19fQ==', 1792651685),
('yhPsXs4cnzmHryVPaSZ6EGsUjrjur25AkEaeoT4L', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'YTo4OntzOjY6Il90b2tlbiI7czo0MDoiREZ4dDh0ODlkZm5kQ0RiOFRQY01PZnM3UFdhOG9PYU1Mc3JPM0FoViI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQwOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvbm90aWZpY2F0aW9ucy9kYXRhIjtzOjU6InJvdXRlIjtzOjE5OiJub3RpZmljYXRpb25zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjE3OiJwYXNzd29yZF9oYXNoX3dlYiI7czo2NDoiZWVmMWMzZTc3MWZlMGIwOTU5NWE2ZWY1YTZlMTQxMzFkNTVlY2JiZTllNTM4MjQ5YjIzZmJiZTU0ZmNkNTNkZiI7czo2OiJ0YWJsZXMiO2E6NDp7czo0MDoiNWI5YTY3YTIwODc2YzRmZmEwZGMzNTVmNDcxYTE4ZjVfY29sdW1ucyI7YTo1OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo1OiJHcm91cCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjE7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToicm9sZS5uYW1lIjtzOjU6ImxhYmVsIjtzOjQ6IlJvbGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJ1c2Vyc19jb3VudCI7czo1OiJsYWJlbCI7czo1OiJVc2VycyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6InByb2plY3RzX2NvdW50IjtzOjU6ImxhYmVsIjtzOjg6IlByb2plY3RzIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMDoiY3JlYXRlZF9hdCI7czo1OiJsYWJlbCI7czoxMDoiQ3JlYXRlZCBhdCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiNTc3ZWE3MDIzNGQzMmFiN2QxNDVhN2FkOGE5MTZhYzZfY29sdW1ucyI7YTozOntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJOYW1lIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoicHJvamVjdHNfY291bnQiO3M6NToibGFiZWwiO3M6ODoiUHJvamVjdHMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToyO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJ1cGRhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjEwOiJVcGRhdGVkIGF0IjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fX1zOjQwOiIzNTMzNmE4MDg3Nzg4MzRmODk0MTc0NzExMmMyNjQzZV9jb2x1bW5zIjthOjU6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo0OiJuYW1lIjtzOjU6ImxhYmVsIjtzOjQ6Ik5hbWUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjQ6InNsdWciO3M6NToibGFiZWwiO3M6NDoiU2x1ZyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTc6InBlcm1pc3Npb25zX2NvdW50IjtzOjU6ImxhYmVsIjtzOjExOiJQZXJtaXNzaW9ucyI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjM7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTQ6ImlzX3N5c3RlbV9yb2xlIjtzOjU6ImxhYmVsIjtzOjExOiJTeXN0ZW0gcm9sZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjQ7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6OToiaXNfYWN0aXZlIjtzOjU6ImxhYmVsIjtzOjk6IklzIGFjdGl2ZSI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO319czo0MDoiZTY0NDgzM2Y0ZTRlMDg3MTIzMTVkYTcxYjMzZmFjZDJfY29sdW1ucyI7YTo1OntpOjA7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6NDoibmFtZSI7czo1OiJsYWJlbCI7czo0OiJVc2VyIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MTthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czo1OiJlbWFpbCI7czo1OiJsYWJlbCI7czo1OiJFbWFpbCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTU6InVzZXJHcm91cHMubmFtZSI7czo1OiJsYWJlbCI7czoxMToiVXNlciBHcm91cHMiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTozO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjk6ImlzX2FjdGl2ZSI7czo1OiJsYWJlbCI7czo2OiJBY3RpdmUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo0O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjEwOiJjcmVhdGVkX2F0IjtzOjU6ImxhYmVsIjtzOjE1OiJBY3RpdmF0aW9uIERhdGUiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fX1zOjg6ImZpbGFtZW50IjthOjA6e319', 1792651792);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `group` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `locked` tinyint(1) NOT NULL DEFAULT 0,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `group`, `name`, `locked`, `payload`, `created_at`, `updated_at`) VALUES
(1, 'styling', 'brand_name', 0, '\"CCOD\"', '2026-10-11 10:03:38', '2026-10-21 01:02:18'),
(2, 'styling', 'logo_light', 0, '\"branding\\/8Xc3AklNf8s3Oq4838h4Cy7QIzdAakWt8YeVCYZo.png\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(3, 'styling', 'logo_dark', 0, '\"branding\\/Wn3DHqP0ClHFSqry3kLpzSTskBH8bbm2OD4Av4A9.png\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(4, 'styling', 'favicon', 0, '\"branding\\/DvMn4qkpi5yeO7K0yIde37uYBTepTIFn85iERIBM.png\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(5, 'styling', 'font_family', 0, '\"Inter\"', '2026-10-11 10:03:39', '2026-10-11 10:03:39'),
(6, 'styling', 'primary_color', 0, '\"amber\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(7, 'styling', 'success_color', 0, '\"green\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(8, 'styling', 'warning_color', 0, '\"red\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(9, 'styling', 'danger_color', 0, '\"red\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(10, 'styling', 'info_color', 0, '\"cyan\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(11, 'styling', 'gray_color', 0, '\"gray\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(12, 'styling', 'corner_radius', 0, '\"0.375rem\"', '2026-10-11 10:03:39', '2026-10-21 01:02:18'),
(79, 'styling', 'custom_colors', 0, '[]', '2026-10-11 11:20:10', '2026-10-21 01:02:18'),
(176, 'styling', 'dark_mode_theme', 0, '\"black\"', '2026-10-11 12:03:36', '2026-10-21 01:02:18'),
(203, 'styling', 'light_mode_theme', 0, '\"zinc\"', '2026-10-11 12:22:49', '2026-10-21 01:02:18'),
(933, 'maintenance', 'enabled', 0, 'false', '2026-10-12 23:52:19', '2026-10-13 22:47:29'),
(1049, 'tab_access', 'restricted_tab_behavior', 0, '\"disabled\"', '2026-10-15 10:28:27', '2026-10-15 10:28:27');

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
(4, 'v0.4.0', 'Developer Operations & Audit Logging', '### Added\n\n- Added a dedicated Developer Dashboard.\n- Added a separate developers authentication guard and provider.\n- Added developer account management and activation status.\n- Added developer-only access to operational tooling.\n\n### Audit Logging\n\n* Added application audit logging.\n* Added authentication event logging.\n* Added request logging.\n* Added CRUD/model-action logging.\n* Added failure-reason tracking.\n* Added HTTP request metadata and execution duration.\n* Added configurable audit-log categories.\n\n### Logging Controls\n\nAdded granular controls for:\n- Login\n- Logout\n- Failed authentication\n- Page requests\n- Application requests\n- Create\n- Update\n- Delete\n- Restore\n- Force delete\n\n ### Security\n \n* Audit logs are isolated from the normal CCOD dashboard.\n* Audit logs are accessible only from the developer dashboard.\n* Developer authorization is enforced separately from normal application users.\n* Audit records do not store old/new model values.', '2026-10-10', 'draft', '[\"local\"]', 0, '2026-10-10 05:12:06', '2026-10-13 00:29:54'),
(5, 'v0.5.0', 'Release Management & Changelog', '# Added\n\n* Integrated ShipLog for application release management.\n* Added database-backed release storage.\n* Added developer-only release management.\n* Added the Changelog experience for application users.\n* Added release visibility through the application user menu.\n\n# Changelog UX\n\n* Removed the separate sidebar Changelog entry.\n* Changelog is accessible from the user menu, directly below Sign out.\n* Uses Filament\'s native user-menu styling.\n* Removed the unwanted ShipLog \"What\'s New\" style entry point.\n\n# Developer Experience\n\nDevelopers can manage releases from:\n\n**Developer Dashboard → Maintenance → Changelog**\n\nwhile normal users can view published releases through:\n\n**User Menu → Changelog**', '2026-10-10', 'draft', '[\"local\"]', 0, '2026-10-10 05:13:07', '2026-10-13 00:29:15');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_web_urls`
--

CREATE TABLE `subscription_web_urls` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(100) NOT NULL,
  `url` varchar(2048) NOT NULL,
  `url_hash` char(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_live` tinyint(1) DEFAULT NULL,
  `http_status` smallint(5) UNSIGNED DEFAULT NULL,
  `response_time_ms` int(10) UNSIGNED DEFAULT NULL,
  `ssl_status` varchar(32) DEFAULT NULL,
  `ssl_provider` varchar(255) DEFAULT NULL,
  `ssl_subject` varchar(255) DEFAULT NULL,
  `ssl_issuer` varchar(255) DEFAULT NULL,
  `ssl_valid_from` datetime DEFAULT NULL,
  `ssl_expires_at` datetime DEFAULT NULL,
  `ssl_days_remaining` int(11) DEFAULT NULL,
  `last_checked_at` datetime DEFAULT NULL,
  `last_check_error` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_web_urls`
--

INSERT INTO `subscription_web_urls` (`id`, `subscription_id`, `url`, `url_hash`, `is_active`, `is_live`, `http_status`, `response_time_ms`, `ssl_status`, `ssl_provider`, `ssl_subject`, `ssl_issuer`, `ssl_valid_from`, `ssl_expires_at`, `ssl_days_remaining`, `last_checked_at`, `last_check_error`, `created_at`, `updated_at`) VALUES
(3, '2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'https://liferay.boschautoparts.com', '1e5bc447da4ef20430309cdef2b1b9cb903f80fd3400994ebf18759d288a5dc3', 1, 1, 200, 9020, '1', 'DigiCert Inc', 'boschautoparts.com', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2025-11-03 00:00:00', '2026-11-02 23:59:59', 20, '2026-10-13 18:37:29', NULL, '2026-10-13 05:05:51', '2026-10-13 13:07:29'),
(4, '5b46ccc6-604b-4c5c-81c2-96b133061773', 'https://liferaydev.boschautoparts.com', '870e685248bb3f563667c7924211cdf3d0da5f3759a41d721e544ceed5c00d97', 1, 1, 200, 2693, '1', 'DigiCert Inc', 'liferaydev.boschautoparts.com', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-04-09 00:00:00', '2026-10-24 23:59:59', 11, '2026-10-13 18:37:35', NULL, '2026-10-13 05:07:55', '2026-10-13 13:07:35'),
(5, 'a77eebf3-17a1-4378-90bf-2c96b1028137', 'https://liferayqa.boschautoparts.com', '5769bf05043e14506d34406e32b0559289cf0b524fef1b56c7721ab50ee4160c', 1, 1, 200, 2762, '1', 'DigiCert Inc', 'liferaydev.boschautoparts.com', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-04-09 00:00:00', '2026-10-24 23:59:59', 11, '2026-10-13 18:37:40', NULL, '2026-10-13 05:08:14', '2026-10-13 13:07:40'),
(6, '1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'https://www.extra-program.com', '7b4d5169c6d1512f5085e030a55cfb43aace1991209c24ce1966686b95835123', 1, 1, 200, 1304, '1', 'DigiCert Inc', 'extra-bonus.dk', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2025-10-27 00:00:00', '2026-10-26 23:59:59', 13, '2026-10-13 18:37:42', NULL, '2026-10-13 05:15:06', '2026-10-13 13:07:42'),
(7, '77839ff3-b3aa-42b0-b490-55830c14bd3a', 'https://keyunit-dev.extra-platform.com', '2bda09e04cad7d44653cefdca73d60bcdc148c84aab0eeef13609e2c45f6d131', 1, 1, 200, 1366, '1', 'DigiCert Inc', '*.extra-platform.com', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-05-28 00:00:00', '2026-12-12 23:59:59', 60, '2026-10-13 18:37:45', NULL, '2026-10-13 05:15:15', '2026-10-13 13:07:45'),
(8, 'c080fc5b-797d-45db-b089-01cc05f9a758', 'https://mx-qa.extra-platform.com', 'c23ad5ace0ba3949ef64b4c6be4c6409cbe9f076a35d9c1a24b825033909cb08', 1, 1, 200, 1428, '1', 'DigiCert Inc', '*.extra-platform.com', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-05-28 00:00:00', '2026-12-12 23:59:59', 60, '2026-10-13 18:37:47', NULL, '2026-10-13 05:15:25', '2026-10-13 13:07:47'),
(9, 'd26dd531-2174-4c45-a240-032e0d05dfa0', 'https://labelprint.bosch.tech/spectrum-server/security/login.jsp', '12f1db0616a0d58420086009b37dbb658e9a532c1a1619df9f3cd0d12d63333e', 1, 1, 200, 1112, '1', 'DigiCert Inc', 'labelprint.bosch.tech', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-02-17 00:00:00', '2027-02-16 23:59:59', 126, '2026-10-13 18:37:49', NULL, '2026-10-13 05:15:56', '2026-10-13 13:07:49'),
(10, '19fb36f7-0105-4d68-be29-02c3934a2abc', 'https://labelprint-q.bosch.tech/spectrum-server/security/login.jsp', '79fbe1f0efd0ee0d6fd89aabc3ac950a50effc1f9c35024603d90e679d6c922a', 1, 1, 200, 1078, '1', 'DigiCert Inc', 'labelprint-q.bosch.tech', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2025-11-04 00:00:00', '2026-11-03 23:59:59', 21, '2026-10-13 18:37:51', NULL, '2026-10-13 05:16:10', '2026-10-13 13:07:51'),
(11, '02ab4266-3406-48d4-bfbc-76605dde11d7', 'https://boschassociateconnect.com', 'ff7ba952d65eb014d125c72dc37b2600f6a763981affdccb71fcc145c34ffb4e', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 05:18:12', '2026-10-13 05:18:12'),
(12, 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'https://www.boschrewards.in', '26142c724ec096e75364aadd104bf7ed784412a46b30fe521e62cbd7e95c4cde', 1, 1, 200, 5043, '1', 'DigiCert Inc', '*.boschrewards.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-05-28 00:00:00', '2026-12-12 23:59:59', 60, '2026-10-13 18:37:59', NULL, '2026-10-13 05:20:42', '2026-10-13 13:07:59'),
(13, 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'https://www.boschserviceportal.com', 'ac896515e8c72ee6e5bc9d376bd82d1bf49829d37df12539062837f798981f4e', 1, 1, 200, 5241, '1', 'DigiCert Inc', '*.boschserviceportal.com', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-05-28 00:00:00', '2026-12-12 23:59:59', 60, '2026-10-13 18:38:06', NULL, '2026-10-13 05:20:51', '2026-10-13 13:08:06'),
(14, 'dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'https://iboschrewards.azurewebsites.net/Home/HealthCheck', '41e63a20127b717acdd0e01cec41592f0ac7bff9616c8d2f610ac9322cef03c0', 1, 0, NULL, 10405, '1', 'Microsoft Corporation', '*.azurewebsites.net', 'Microsoft TLS G2 RSA CA OCSP 04', '2026-10-13 14:22:06', '2027-01-21 13:22:06', 99, '2026-10-13 18:38:18', 'cURL error 28: Operation timed out after 10001 milliseconds with 0 bytes received (see https://curl.se/libcurl/c/libcurl-errors.html) for https://iboschrewards.azurewebsites.net/Home/HealthCheck', '2026-10-13 05:20:58', '2026-10-13 13:08:18'),
(15, '1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'https://qa.bosch-cms.com', 'a45dd3683ded7741a77eb3aa80196ea53572af812efb4b96b04b5584feb51974', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:38:28', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://qa.bosch-cms.com', '2026-10-13 05:22:32', '2026-10-13 13:08:28'),
(17, 'fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'https://bosch-cms.com', '41963d82ec47dfd6b451778bc68a5edefcb92649796cc14bbe6cf80435bdbf72', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:38:38', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://bosch-cms.com', '2026-10-13 05:23:01', '2026-10-13 13:08:38'),
(18, 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'https://boschefocus.com:5138', '5fb4d6c084e2d991a723e13bb1d09cdf911fe2c051efbf93f87b89887ce7ff69', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:38:48', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://boschefocus.com:5138', '2026-10-13 05:24:19', '2026-10-13 13:08:48'),
(19, 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'https://boschefocus.com:5141', '303183e49fde21d176c30c63e2b349c1f5d5feafc12add39714d2d5bc6be91de', 1, 0, NULL, 5002, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:38:58', 'cURL error 28: Connection timed out after 5000 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://boschefocus.com:5141', '2026-10-13 05:24:24', '2026-10-13 13:08:58'),
(20, 'c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'https://boschefocus.com:8080/DynamicsNAV90/WebClient/Default.aspx', 'caff7ae61afcb372b531dc31486f03b2a911e06e9052965c8469adcf8f2b7620', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:39:08', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://boschefocus.com:8080/DynamicsNAV90/WebClient/Default.aspx', '2026-10-13 05:24:30', '2026-10-13 13:09:08'),
(21, '7498780e-1785-465f-9d50-c8ac1e929376', 'https://boschefocus.com:8443/WebInterface/login.html', '195cdf0962182990937fe9a7a86fd3504d15c4c5c96c658b5676604b4f3fa340', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:39:18', 'cURL error 28: Connection timed out after 5000 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://boschefocus.com:8443/WebInterface/login.html', '2026-10-13 05:24:39', '2026-10-13 13:09:18'),
(22, '7498780e-1785-465f-9d50-c8ac1e929376', 'https://quality.boschefocus.com:8080/DynamicsNAV90/WebClient/Default.aspx', '4dcc60fed6cc06af1869e3a0fe4e090c957d5fb6fb26ef75d8fbc6d478d1e5a9', 1, 0, NULL, 5002, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:39:29', 'cURL error 28: Connection timed out after 5000 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://quality.boschefocus.com:8080/DynamicsNAV90/WebClient/Default.aspx', '2026-10-13 05:25:06', '2026-10-13 13:09:29'),
(23, '7498780e-1785-465f-9d50-c8ac1e929376', 'https://quality.boschefocus.com:5138', 'd82d4997bacb332b71f3c51d60fa44d47b9cb5d4a14d520a6ca813976ef3bde2', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:39:39', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://quality.boschefocus.com:5138', '2026-10-13 05:25:10', '2026-10-13 13:09:39'),
(24, '7498780e-1785-465f-9d50-c8ac1e929376', 'https://quality.boschefocus.com:5141', '68381710b8b008541fa5092274fcc2ab73849b977031a6e48c5998067bf5e8e7', 1, 0, NULL, 5002, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:39:49', 'cURL error 28: Connection timed out after 5000 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://quality.boschefocus.com:5141', '2026-10-13 05:25:14', '2026-10-13 13:09:49'),
(25, '219ccbf6-e35b-4758-961a-ede690403d86', 'https://qa.hamro.boschaftermarket.in/swagger/index.html', '27ee40a04bab6e84d5a06a94acb48d55a5e36fe034fa9d85bdd09fbc6cde0364', 1, 1, 200, 1922, '1', 'DigiCert Inc', '*.hamro.boschaftermarket.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-08-14 00:00:00', '2027-02-28 23:59:59', 138, '2026-10-13 18:39:52', NULL, '2026-10-13 05:25:44', '2026-10-13 13:09:52'),
(27, '8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'https://hamro.boschaftermarket.in/swagger/index.html', '28c5b4e32d51b074d7d17f06be6db3e65573209a4bf531f7b9ad8e3c6124fc39', 1, 1, 200, 2054, '1', 'DigiCert Inc', '*.hamro.boschaftermarket.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-08-14 00:00:00', '2027-02-28 23:59:59', 138, '2026-10-13 18:39:55', NULL, '2026-10-13 05:26:57', '2026-10-13 13:09:55'),
(28, '12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'https://boschinsites.com/insiderlens', 'c6d22873993ccf9f2aad41a37f2572e7bf9b647ccd95c2dc800c8c8afff3eecc', 1, 1, 200, 2405, '1', 'GoDaddy.com, Inc.', 'boschinsites.com', 'Go Daddy Secure Certificate Authority - G2', '2025-11-16 07:02:28', '2026-12-17 19:19:37', 65, '2026-10-13 18:39:59', NULL, '2026-10-13 05:27:15', '2026-10-13 13:09:59'),
(29, '50d41c25-8668-4497-8925-766161f0cddb', 'https://Q-INTERNAL.MICONIC.IN', '73712c2addbfb5d3a63646826ef4578db6d07b25dd4bd2984595b17927bead0f', 1, 0, NULL, 5002, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:40:09', 'cURL error 28: Connection timed out after 5000 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://q-internal.miconic.in', '2026-10-13 05:27:43', '2026-10-13 13:10:09'),
(30, '50d41c25-8668-4497-8925-766161f0cddb', 'https://Q-WEBAPI.MICONIC.IN', '236a99aea31e4556e7559edd2d96a07269d105cbc14bf25a083f9597f70a94ea', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:40:19', 'cURL error 28: Connection timed out after 5000 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://q-webapi.miconic.in', '2026-10-13 05:27:54', '2026-10-13 13:10:19'),
(31, '50d41c25-8668-4497-8925-766161f0cddb', 'https://Q-WSAPI.MICONIC.IN', '9cc076258de107be093d3b838a687e5433a99b2cf7d216807b1894b568d2beb1', 1, 0, NULL, 5004, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:40:29', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://q-wsapi.miconic.in', '2026-10-13 05:28:11', '2026-10-13 13:10:29'),
(32, '50d41c25-8668-4497-8925-766161f0cddb', 'https://Q-2WOAPI.MICONIC.IN', '9a9eeb88a04c7c14fa27ee1c5115bacd8e81d5a34f2de96d2be681bed061a275', 1, 0, NULL, 5003, '0', NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 18:40:39', 'cURL error 28: Connection timed out after 5001 milliseconds (see https://curl.se/libcurl/c/libcurl-errors.html) for https://q-2woapi.miconic.in', '2026-10-13 05:28:19', '2026-10-13 13:10:39'),
(33, 'fc152cdb-dc60-44e7-bbee-412c719cb49a', 'https://dev-wsapi.miconic.in', '8cdfcb04710596ea40e1b0555b8bd90ebab83d300ac47c35b07bc4ac92cb332e', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 05:28:37', '2026-10-13 05:28:37'),
(34, 'fc152cdb-dc60-44e7-bbee-412c719cb49a', 'https://dev-webapi.miconic.in', '2d79e10d0d8d09e3d72005a34a3a1eea046f746f791fd926b716ed52a17a5c36', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 05:28:45', '2026-10-13 05:31:46'),
(42, 'fc152cdb-dc60-44e7-bbee-412c719cb49a', 'https://dev-2woapi.miconic.in', '0ef646374e5cbe64684696e275e649c86b8b485deec62ebf6a5dc11cc7fe9751', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-10-13 05:31:00', '2026-10-13 05:31:44'),
(43, '4172676b-8a9c-44e0-b2ab-727057b691b7', 'https://INTERNAL.MICONIC.IN', 'c1ed27584ae21830943c2225ce32a214dac52e9b9212fec34189f1210481e759', 1, 1, 200, 4272, '1', 'DigiCert Inc', '*.miconic.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-01-06 00:00:00', '2027-01-05 23:59:59', 84, '2026-10-13 18:40:46', NULL, '2026-10-13 05:32:04', '2026-10-13 13:10:46'),
(44, '4172676b-8a9c-44e0-b2ab-727057b691b7', 'https://WSAPI.MICONIC.IN', '8624d5122d9a4e70d22aa322278e51f588654b3bdcaf9706fd5eede934a8f38b', 1, 1, 200, 2103, '1', 'DigiCert Inc', '*.miconic.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-01-06 00:00:00', '2027-01-05 23:59:59', 84, '2026-10-13 18:40:49', NULL, '2026-10-13 05:32:12', '2026-10-13 13:10:49'),
(45, '4172676b-8a9c-44e0-b2ab-727057b691b7', 'https://2WOAPI.MICONIC.IN', '9bf331b305b2ba447e7cba4a254f8bf227ae7d781570fad77fe42d1edfdb4875', 1, 1, 200, 2102, '1', 'DigiCert Inc', '*.miconic.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-01-06 00:00:00', '2027-01-05 23:59:59', 84, '2026-10-13 18:40:53', NULL, '2026-10-13 05:32:22', '2026-10-13 13:10:53'),
(46, '4172676b-8a9c-44e0-b2ab-727057b691b7', 'https://WEBAPI.MICONIC.IN', '686703b46d96936a6ef4732eaaf07318ef0c8424092151f0861d2d25dd783963', 1, 1, 200, 1920, '1', 'DigiCert Inc', '*.miconic.in', 'DigiCert Global G2 TLS RSA SHA256 2020 CA1', '2026-01-06 00:00:00', '2027-01-05 23:59:59', 84, '2026-10-13 18:40:56', NULL, '2026-10-13 05:32:29', '2026-10-13 13:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `team_subscription`
--

CREATE TABLE `team_subscription` (
  `business_unit_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_subscription`
--

INSERT INTO `team_subscription` (`business_unit_id`, `subscription_id`, `created_at`, `updated_at`) VALUES
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
(1, 'JRA9COB', 'jra9cob@bosch.com', NULL, '$2y$12$gvrkcj5.HqEO.vMZyYvLFej075vWSkG.DOyfDfY2utnExYWjvFsyW', 'super_admin', 1, NULL, '2026-10-08 08:48:28', '2026-10-09 09:18:45'),
(5, 'Test user', 'test@bosch.com', NULL, '$2y$12$606eOq4Z3bNgsiKQqlnGC.zB7nFl2BSZ6iQj3bxgAlWBUJzA5l2mW', 'restricted_reader', 1, NULL, '2026-10-15 09:03:28', '2026-10-15 13:07:28'),
(6, 'Another test user', 'anothertest@bosch.com', NULL, '$2y$12$FPD7xIJ2pIHlcIf9eLS3fu.XJsgZMNz1bHOQeBu6e0fdzvqEe.MVC', 'restricted_reader', 1, NULL, '2026-10-15 10:40:09', '2026-10-19 03:21:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_groups`
--

CREATE TABLE `user_groups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_groups`
--

INSERT INTO `user_groups` (`id`, `name`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 'SX-Admins', 4, '2026-10-21 01:41:43', '2026-10-21 01:41:43'),
(2, 'SX-SuperAdmins', 5, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(3, 'Test-group', 8, '2026-10-21 02:53:44', '2026-10-21 02:53:44');

-- --------------------------------------------------------

--
-- Table structure for table `user_group_user`
--

CREATE TABLE `user_group_user` (
  `user_group_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `starts_at` datetime DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_group_user`
--

INSERT INTO `user_group_user` (`user_group_id`, `user_id`, `starts_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(2, 1, NULL, NULL, '2026-10-21 02:12:11', '2026-10-21 02:12:11'),
(3, 5, NULL, NULL, '2026-10-21 02:53:44', '2026-10-21 02:53:44');

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
-- Indexes for dumped tables
--

--
-- Indexes for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `access_requests_team_id_foreign` (`business_unit_id`),
  ADD KEY `access_requests_status_created_at_index` (`status`,`created_at`),
  ADD KEY `access_requests_user_id_status_index` (`user_id`,`status`),
  ADD KEY `access_requests_target_type_team_id_index` (`target_type`,`business_unit_id`),
  ADD KEY `access_requests_target_type_subscription_id_index` (`target_type`,`subscription_id`),
  ADD KEY `access_requests_decided_by_type_decided_by_id_index` (`decided_by_type`,`decided_by_id`),
  ADD KEY `access_requests_user_group_id_foreign` (`user_group_id`),
  ADD KEY `access_requests_target_type_user_group_id_index` (`target_type`,`user_group_id`);

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
-- Indexes for table `azure_alerts`
--
ALTER TABLE `azure_alerts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `azure_alerts_azure_alert_id_unique` (`azure_alert_id`) USING HASH,
  ADD KEY `azure_alerts_subscription_id_index` (`subscription_id`),
  ADD KEY `azure_alerts_severity_index` (`severity`),
  ADD KEY `azure_alerts_fire_time_index` (`fire_time`),
  ADD KEY `azure_alerts_last_modified_time_index` (`last_modified_time`);

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
-- Indexes for table `business_units`
--
ALTER TABLE `business_units`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teams_name_unique` (`name`);

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
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`),
  ADD KEY `permissions_category_index` (`category`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `pill_colors`
--
ALTER TABLE `pill_colors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pill_colors_key_unique` (`key`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projects_name_unique` (`name`),
  ADD KEY `projects_business_unit_id_foreign` (`business_unit_id`);

--
-- Indexes for table `project_subscriptions`
--
ALTER TABLE `project_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_subscriptions_subscription_unique` (`subscription_id`),
  ADD UNIQUE KEY `project_subscriptions_project_subscription_unique` (`project_id`,`subscription_id`);

--
-- Indexes for table `project_user_group`
--
ALTER TABLE `project_user_group`
  ADD UNIQUE KEY `project_user_group_user_group_id_project_id_unique` (`user_group_id`,`project_id`),
  ADD KEY `project_user_group_project_id_foreign` (`project_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_group_name_unique` (`group`,`name`);

--
-- Indexes for table `shiplog_releases`
--
ALTER TABLE `shiplog_releases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shiplog_releases_version_index` (`version`),
  ADD KEY `shiplog_releases_released_at_index` (`released_at`),
  ADD KEY `shiplog_releases_status_index` (`status`);

--
-- Indexes for table `subscription_web_urls`
--
ALTER TABLE `subscription_web_urls`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_web_urls_subscription_id_url_hash_unique` (`subscription_id`,`url_hash`),
  ADD KEY `subscription_web_urls_subscription_id_is_live_index` (`subscription_id`,`is_live`),
  ADD KEY `subscription_web_urls_last_checked_at_index` (`last_checked_at`),
  ADD KEY `subscription_web_urls_subscription_id_is_active_index` (`subscription_id`,`is_active`);

--
-- Indexes for table `team_subscription`
--
ALTER TABLE `team_subscription`
  ADD PRIMARY KEY (`business_unit_id`,`subscription_id`),
  ADD KEY `team_subscription_subscription_id_foreign` (`subscription_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_groups_name_unique` (`name`),
  ADD KEY `user_groups_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_group_user`
--
ALTER TABLE `user_group_user`
  ADD UNIQUE KEY `user_group_user_user_group_id_user_id_unique` (`user_group_id`,`user_id`),
  ADD KEY `user_group_user_user_id_foreign` (`user_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `azure_alerts`
--
ALTER TABLE `azure_alerts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `azure_budgets`
--
ALTER TABLE `azure_budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `business_units`
--
ALTER TABLE `business_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `developers`
--
ALTER TABLE `developers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `documentations`
--
ALTER TABLE `documentations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `log_settings`
--
ALTER TABLE `log_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `notification_rules`
--
ALTER TABLE `notification_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `pill_colors`
--
ALTER TABLE `pill_colors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `project_subscriptions`
--
ALTER TABLE `project_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1136;

--
-- AUTO_INCREMENT for table `shiplog_releases`
--
ALTER TABLE `shiplog_releases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subscription_web_urls`
--
ALTER TABLE `subscription_web_urls`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_subscription_access_overrides`
--
ALTER TABLE `user_subscription_access_overrides`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_requests`
--
ALTER TABLE `access_requests`
  ADD CONSTRAINT `access_requests_team_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `access_requests_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `access_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

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
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_business_unit_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_subscriptions`
--
ALTER TABLE `project_subscriptions`
  ADD CONSTRAINT `project_subscriptions_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_subscriptions_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_user_group`
--
ALTER TABLE `project_user_group`
  ADD CONSTRAINT `project_user_group_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_user_group_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_web_urls`
--
ALTER TABLE `subscription_web_urls`
  ADD CONSTRAINT `subscription_web_urls_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE;

--
-- Constraints for table `team_subscription`
--
ALTER TABLE `team_subscription`
  ADD CONSTRAINT `team_subscription_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_subscription_team_id_foreign` FOREIGN KEY (`business_unit_id`) REFERENCES `business_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_groups`
--
ALTER TABLE `user_groups`
  ADD CONSTRAINT `user_groups_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_group_user`
--
ALTER TABLE `user_group_user`
  ADD CONSTRAINT `user_group_user_user_group_id_foreign` FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_group_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_subscription_access_overrides`
--
ALTER TABLE `user_subscription_access_overrides`
  ADD CONSTRAINT `user_subscription_access_overrides_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
