-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2026 at 05:40 PM
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
-- Database: `cloud_ops_dashboard`
--

-- --------------------------------------------------------

--
-- Table structure for table `azure_budgets`
--

CREATE TABLE `azure_budgets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` varchar(100) NOT NULL,
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
  `subscription_id` varchar(100) NOT NULL,
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
  `subscription_id` varchar(100) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `health_status` varchar(50) DEFAULT 'Healthy',
  `security_score` decimal(5,2) DEFAULT 100.00,
  `mtd_spend_eur` decimal(10,2) DEFAULT 0.00,
  `last_synced` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `azure_subscriptions`
--

INSERT INTO `azure_subscriptions` (`subscription_id`, `display_name`, `health_status`, `security_score`, `mtd_spend_eur`, `last_synced`) VALUES
('02ab4266-3406-48d4-bfbc-76605dde11d7', 'RBIN-BDO-web-mobile-PROD', 'Degraded', 53.35, 972.31, '2026-09-26 15:38:20'),
('0e6dc52f-1c64-4d5a-9396-29e37fa41079', 'AA-AS-EIT2-Prod', 'Healthy', 91.07, 23014.95, '2026-09-26 15:36:48'),
('12db631f-5ab4-4237-9fa6-6d92de7a53e8', 'OT-RBIN-BCS1-InsiderLens-Prod', 'Warning', 80.00, 290.37, '2026-09-26 15:38:09'),
('19fb36f7-0105-4d68-be29-02c3934a2abc', 'AA-GPM-BoschCloudPrinting-QA', 'Healthy', 89.63, 1511.27, '2026-09-26 15:37:45'),
('1c119a3d-5a21-4652-8e7f-e8b55c8635f5', 'MA-SKX-eXtra-Production-Stage-Prod', 'Warning', 84.92, 5863.72, '2026-09-26 15:39:00'),
('1cf3b1cf-65eb-4ef0-a2bc-916e10d7e719', 'AA-ICO-IN-Azure-QA', 'Warning', 80.43, 261.75, '2026-09-26 15:37:01'),
('219ccbf6-e35b-4758-961a-ede690403d86', 'Hamro-Bosch', 'Healthy', 86.96, 132.08, '2026-09-26 15:39:07'),
('2ac2c1f8-60ec-4523-9c64-8fdf8bc2a75b', 'AA-SMS3-NA-PBAP-Prod', 'Warning', 80.95, 1694.31, '2026-09-26 15:37:59'),
('4172676b-8a9c-44e0-b2ab-727057b691b7', 'OT-RBIN-PJ-DIGS-MICONIC-Prod', 'Healthy', 92.00, 567.71, '2026-09-26 15:38:31'),
('50d41c25-8668-4497-8925-766161f0cddb', 'OT-RBIN-PJ-DIGS-MICONIC-QA', 'Healthy', 85.19, 400.71, '2026-09-26 15:38:15'),
('5784d84d-05ea-4a9c-b625-7d0183e9240b', 'AA-BDO-IN-BoschRewards-QA', 'Healthy', 99.67, 372.00, '2026-09-26 15:38:36'),
('5b46ccc6-604b-4c5c-81c2-96b133061773', 'AA-SMS3-NA-BAP5.0-Dev', 'Healthy', 87.29, 503.93, '2026-09-26 15:37:52'),
('6b8e63a8-5397-4d83-9b49-164067a4e892', 'XC_Production_XC/ENG-Bp_866512', 'Degraded', 0.00, 0.00, '2026-09-26 15:36:35'),
('6c389f01-77d0-4c2c-b846-8dfa36987531', 'EAP Sandbox', 'Healthy', 90.51, 148.75, '2026-09-26 15:39:20'),
('7498780e-1785-465f-9d50-c8ac1e929376', 'AA-ICO-IN-eFOCuS-QA', 'Healthy', 91.47, 426.17, '2026-09-26 15:37:12'),
('77839ff3-b3aa-42b0-b490-55830c14bd3a', 'MA-SKX-eXtra-Dev-Stage-Dev', 'Warning', 78.57, 1167.13, '2026-09-26 15:38:49'),
('8a8c77f4-cb44-47ad-b56a-6682d97b36bb', 'AA-ICO-IN-AA-Dashboard-Prod', 'Healthy', 87.12, 1494.64, '2026-09-26 15:37:32'),
('8c9dacf9-577b-4129-b900-2c6c9e39c3dd', 'Hamro Bosch Prod', 'Warning', 82.61, 323.00, '2026-09-26 15:39:13'),
('a77eebf3-17a1-4378-90bf-2c96b1028137', 'AA-SMS3-NA-QA', 'Healthy', 90.48, 1370.51, '2026-09-26 15:38:03'),
('c080fc5b-797d-45db-b089-01cc05f9a758', 'MA-SKX-eXtra-Promo-Stage-QA', 'Healthy', 87.57, 3900.99, '2026-09-26 15:38:54'),
('c12d79d2-0655-4caa-839e-fc47e019271c', 'OT-RBIN-GS-EXIMPortal-Prod', 'Degraded', 69.05, 465.59, '2026-09-26 15:38:42'),
('c97e52b5-1c64-44c4-9ac2-36ab44b2ece5', 'AA-ICO-IN-eFOCuS-Prod', 'Healthy', 92.26, 2391.48, '2026-09-26 15:37:27'),
('d26dd531-2174-4c45-a240-032e0d05dfa0', 'AA-GPM-BoschCloudPrinting-Prod', 'Warning', 75.85, 4243.84, '2026-09-26 15:37:39'),
('d357b6e3-c707-4e36-8681-ba7ef9e30e8a', 'BD-PIP1-HardenedImages-Prod', 'Degraded', 0.00, 0.00, '2026-09-26 15:36:41'),
('dca0a650-722e-4755-bbce-f2b7f6ed0f9f', 'AA-ICO-IN-BoschRewards-Prod', 'Healthy', 94.94, 3325.95, '2026-09-26 15:37:19'),
('e6800a5e-47c7-47df-8bf8-fc2c616e1442', 'CI-DAE1.5-ASCWorkShop-QA', 'Degraded', 0.00, 0.00, '2026-09-26 15:36:54'),
('fc152cdb-dc60-44e7-bbee-412c719cb49a', 'AA-SWS-IN-MICONIC-Dev', 'Healthy', 100.00, 0.03, '2026-09-26 15:38:26'),
('fdac5c1a-5d9c-4ed0-944f-54c8a3e49bf8', 'AA-ICO-IN-Claims-Management-Prod', 'Healthy', 91.70, 370.96, '2026-09-26 15:37:07');

-- --------------------------------------------------------

--
-- Table structure for table `billing_resources`
--

CREATE TABLE `billing_resources` (
  `resource_id` varchar(500) NOT NULL,
  `subscription_id` varchar(100) DEFAULT NULL,
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

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6', 'i:1;', 1791469279),
('laravel-cache-livewire-rate-limiter:16d36dff9abd246c67dfac3e63b993a169af77e6:timer', 'i:1791469279;', 1791469279);

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
(3, '0001_01_01_000002_create_jobs_table', 1);

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
('OvvukXb2QOzhsCHHiADZTZu8Gh9C2y48nfTEINz1', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiUktWVDljSVN2NE1Qa2hJQjB2OFB0UUk0ZVJFVlVZa2hXa09KVXBTciI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9henVyZS1zdWJzY3JpcHRpb25zIjtzOjU6InJvdXRlIjtzOjUwOiJmaWxhbWVudC5hZG1pbi5yZXNvdXJjZXMuYXp1cmUtc3Vic2NyaXB0aW9ucy5pbmRleCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6MTc6InBhc3N3b3JkX2hhc2hfd2ViIjtzOjY0OiJlZWYxYzNlNzcxZmUwYjA5NTk1YTZlZjVhNmUxNDEzMWQ1NWVjYmJlOWU1MzgyNDliMjNmYmJlNTRmY2Q1M2RmIjtzOjY6InRhYmxlcyI7YToxOntzOjQwOiJjNGM3YWZiY2Y1ZjBkMWZmZjZkNTcxNDgwODJiOTU1ZF9jb2x1bW5zIjthOjY6e2k6MDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMjoiZGlzcGxheV9uYW1lIjtzOjU6ImxhYmVsIjtzOjEyOiJTdWJzY3JpcHRpb24iO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aToxO2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjE1OiJzdWJzY3JpcHRpb25faWQiO3M6NToibGFiZWwiO3M6MTU6IlN1YnNjcmlwdGlvbiBJRCI7czo4OiJpc0hpZGRlbiI7YjowO3M6OToiaXNUb2dnbGVkIjtiOjE7czoxMjoiaXNUb2dnbGVhYmxlIjtiOjA7czoyNDoiaXNUb2dnbGVkSGlkZGVuQnlEZWZhdWx0IjtOO31pOjI7YTo3OntzOjQ6InR5cGUiO3M6NjoiY29sdW1uIjtzOjQ6Im5hbWUiO3M6MTM6ImhlYWx0aF9zdGF0dXMiO3M6NToibGFiZWwiO3M6NjoiSGVhbHRoIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6MzthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxNDoic2VjdXJpdHlfc2NvcmUiO3M6NToibGFiZWwiO3M6MTQ6IlNlY3VyaXR5IFNjb3JlIjtzOjg6ImlzSGlkZGVuIjtiOjA7czo5OiJpc1RvZ2dsZWQiO2I6MTtzOjEyOiJpc1RvZ2dsZWFibGUiO2I6MDtzOjI0OiJpc1RvZ2dsZWRIaWRkZW5CeURlZmF1bHQiO047fWk6NDthOjc6e3M6NDoidHlwZSI7czo2OiJjb2x1bW4iO3M6NDoibmFtZSI7czoxMzoibXRkX3NwZW5kX2V1ciI7czo1OiJsYWJlbCI7czo5OiJNVEQgU3BlbmQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9aTo1O2E6Nzp7czo0OiJ0eXBlIjtzOjY6ImNvbHVtbiI7czo0OiJuYW1lIjtzOjExOiJsYXN0X3N5bmNlZCI7czo1OiJsYWJlbCI7czoxMToiTGFzdCBTeW5jZWQiO3M6ODoiaXNIaWRkZW4iO2I6MDtzOjk6ImlzVG9nZ2xlZCI7YjoxO3M6MTI6ImlzVG9nZ2xlYWJsZSI7YjowO3M6MjQ6ImlzVG9nZ2xlZEhpZGRlbkJ5RGVmYXVsdCI7Tjt9fX19', 1791469600);

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
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'jra9cob', 'jra9cob@bosch.com', NULL, '$2y$12$gvrkcj5.HqEO.vMZyYvLFej075vWSkG.DOyfDfY2utnExYWjvFsyW', NULL, '2026-10-08 08:48:28', '2026-10-08 08:48:28');

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`subscription_id`);

--
-- Indexes for table `billing_resources`
--
ALTER TABLE `billing_resources`
  ADD PRIMARY KEY (`resource_id`),
  ADD KEY `idx_subscription` (`subscription_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `azure_budgets`
--
ALTER TABLE `azure_budgets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

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
-- Constraints for table `billing_resources`
--
ALTER TABLE `billing_resources`
  ADD CONSTRAINT `billing_resources_ibfk_1` FOREIGN KEY (`subscription_id`) REFERENCES `azure_subscriptions` (`subscription_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
