-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le :  ven. 16 août 2019 à 23:14
-- Version du serveur :  10.3.17-MariaDB-1:10.3.17+maria~bionic-log
-- Version de PHP :  7.2.19-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `telegram_bot`
--

-- --------------------------------------------------------

--
-- Create Database
--
CREATE DATABASE telegram_bot;


--
-- Create Database
--
USE telegram_bot;


--
-- Structure de la table `bot_setting`
--

CREATE TABLE `bot_setting` (
  `id` int(11) NOT NULL,
  `app_id` varchar(200) DEFAULT NULL,
  `app_name` varchar(100) DEFAULT NULL,
  `support_chat_id` varchar(100) DEFAULT NULL,
  `wallet_id` varchar(200) DEFAULT NULL,
  `wallet_password` varchar(200) DEFAULT NULL,
  `wallet_second_password` varchar(200) DEFAULT NULL,
  `jwt_issuer` varchar(3) DEFAULT 'CMS',
  `jwt_audience` varchar(3) DEFAULT 'All',
  `jwt_key` varchar(128) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Structure de la table `error_logs`
--

CREATE TABLE `error_logs` (
  `id` int(11) NOT NULL,
  `error_number` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `file` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `line` text CHARACTER SET utf8mb4 DEFAULT NULL,
  `source` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` timestamp NULL DEFAULT current_timestamp(),
  `deleted` int(1) NOT NULL DEFAULT 0,
  `deleted_account_id` int(11) DEFAULT NULL,
  `deleted_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPACT;

-- --------------------------------------------------------

--
-- Structure de la table `gui_account`
--

CREATE TABLE `gui_account` (
  `id` int(11) NOT NULL,
  `first_name` varchar(30) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `password` tinytext DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `profile_img` text DEFAULT NULL,
  `last_login_date` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `last_login_ip` varchar(20) DEFAULT NULL,
  `login_attempt` int(1) NOT NULL DEFAULT 0,
  `account_group` int(1) DEFAULT NULL,
  `inscription_date` timestamp NULL DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `deleted_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

--
-- Déchargement des données de la table `gui_account`
--
LOCK TABLES `gui_account` WRITE;
/*!40000 ALTER TABLE `gui_account` DISABLE KEYS */;
INSERT INTO `gui_account` VALUES (1,'BladeBTC','(Admin)','bladebtc','$2y$10$ricm9SeFh3q/NaHAMLE6O.tpuUYjYJVMjYaSIjPMAnOSzM4cSavrG','bladebtc@bladebtc.com','avatar.png','2019-05-21 20:20:51','192.168.0.17',0,1,NULL,0,NULL);
/*!40000 ALTER TABLE `gui_account` ENABLE KEYS */;
UNLOCK TABLES;
-- --------------------------------------------------------

--
-- Structure de la table `gui_group`
--

CREATE TABLE `gui_group` (
  `id` int(11) NOT NULL,
  `group_id` int(2) NOT NULL,
  `group_name` varchar(35) NOT NULL,
  `dashboard` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `gui_group`
--

INSERT INTO `gui_group` (`id`, `group_id`, `group_name`, `dashboard`) VALUES
(1, 1, 'Admin', 'dashboard');

-- --------------------------------------------------------

--
-- Structure de la table `gui_menu`
--

CREATE TABLE `gui_menu` (
  `id` int(11) NOT NULL,
  `menu_id` int(1) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `icon` tinytext NOT NULL,
  `display_order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `gui_menu`
--

INSERT INTO `gui_menu` (`id`, `menu_id`, `title`, `icon`, `display_order`) VALUES
(1, 1, 'Configuration (GUI)', 'fa-cogs', 2),
(2, 2, 'Telegram (Bot)', 'fa-telegram', 1);

-- --------------------------------------------------------

--
-- Structure de la table `gui_module`
--

CREATE TABLE `gui_module` (
  `id` int(10) NOT NULL,
  `description` varchar(200) NOT NULL,
  `name` varchar(200) NOT NULL,
  `icon` varchar(200) NOT NULL,
  `access_level` tinytext NOT NULL,
  `parent` int(11) NOT NULL,
  `static` int(1) NOT NULL DEFAULT 0,
  `visits` int(11) DEFAULT 0,
  `last_visit` timestamp NULL DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `gui_module`
--

INSERT INTO `gui_module` (`id`, `description`, `name`, `icon`, `access_level`, `parent`, `static`, `visits`, `last_visit`, `active`) VALUES
(1, 'Dashboard', 'dashboard', 'fa-wrench', '1', -1, 1, 4, '2019-08-17 02:22:28', 1),
(2, 'Account', 'manage-account', 'fa-wrench', '1', 1, 0, 1, '2019-08-17 02:23:50', 1),
(3, 'Menu', 'manage-menu', 'fa-wrench', '1', 1, 0, 0, NULL, 1),
(4, 'Modules', 'manage-module', 'fa-wrench', '1', 1, 0, 0, NULL, 1),
(5, 'My Account', 'profile', 'fa-wrench', '1', -1, 1, 2, '2019-08-10 21:07:18', 1),
(6, 'RBAC', 'manage-rbac', 'fa-wrench', '1', 1, 0, 0, NULL, 1),
(7, 'Denied', 'denied', 'fa-wrench', '1', -1, 1, 0, NULL, 1),
(8, 'Settings (Bot)', 'telegram-bot-settings', 'fa-wrench', '1', 2, 0, 4, '2019-08-17 02:23:58', 1),
(9, 'Error Logs', 'telegram-error-log', 'fa-wrench', '1', 2, 0, 1, '2019-08-17 02:23:55', 1),
(10, 'Investment Plans (Bot)', 'telegram-investment-plan', 'fa-wrench', '1', 2, 0, 22, '2019-08-17 03:02:18', 1),
(11, 'Users (Bot)', 'telegram-users', 'fa-wrench', '1', 2, 0, 3, '2019-08-17 02:23:44', 1);

-- --------------------------------------------------------

--
-- Structure de la table `gui_rbac_assignment`
--

CREATE TABLE `gui_rbac_assignment` (
  `group_id` int(11) NOT NULL,
  `rbac_items_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `gui_rbac_assignment`
--

INSERT INTO `gui_rbac_assignment` (`group_id`, `rbac_items_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Structure de la table `gui_rbac_items`
--

CREATE TABLE `gui_rbac_items` (
  `id` int(11) NOT NULL,
  `description` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `gui_rbac_items`
--

INSERT INTO `gui_rbac_items` (`id`, `description`) VALUES
(1, 'Can see the loading time.'),
(2, 'Can see debug bar.');

-- --------------------------------------------------------

--
-- Structure de la table `investment`
--

CREATE TABLE `investment` (
  `id` int(11) NOT NULL,
  `telegram_id` int(11) NOT NULL,
  `amount` decimal(15,8) NOT NULL,
  `contract_end_date` timestamp NULL DEFAULT NULL,
  `contract_start_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `investment_plans`
--

CREATE TABLE `investment_plans` (
  `id` int(11) NOT NULL,
  `minimum_invest_usd` decimal(15,2) NOT NULL,
  `minimum_reinvest_usd` decimal(15,2) NOT NULL,
  `minimum_payout_usd` decimal(15,2) NOT NULL,
  `referral_bonus_usd` decimal(15,2) NOT NULL,
  `contract_day` int(11) NOT NULL,
  `required_confirmations` int(11) NOT NULL,
  `withdraw_fee` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT 0,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_account_id` int(11) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT 0,
  `deleted_date` timestamp NULL DEFAULT NULL,
  `deleted_account_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `investment_plans`
--

INSERT INTO `investment_plans` (`id`, `minimum_invest_usd`, `minimum_reinvest_usd`, `minimum_payout_usd`, `referral_bonus_usd`, `contract_day`, `required_confirmations`, `withdraw_fee`, `active`, `creation_date`, `created_account_id`, `deleted`, `deleted_date`, `deleted_account_id`) VALUES
(1, '100.00', '100.00', '50.00', '50.00', 30, 3, 50000, 1, '2019-05-16 20:16:18', 1, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `referrals`
--

CREATE TABLE `referrals` (
  `id` int(11) NOT NULL,
  `telegram_id_referent` int(11) NOT NULL,
  `telegram_id_referred` int(11) NOT NULL,
  `bind_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `telegram_id` int(25) DEFAULT NULL,
  `amount` decimal(15,8) DEFAULT NULL,
  `withdraw_address` tinytext DEFAULT NULL,
  `message` text DEFAULT NULL,
  `tx_hash` text DEFAULT NULL,
  `tx_id` text DEFAULT NULL,
  `status` int(1) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(25) NOT NULL,
  `telegram_username` tinytext DEFAULT NULL,
  `telegram_first` tinytext DEFAULT NULL,
  `telegram_last` tinytext DEFAULT NULL,
  `telegram_id` int(25) DEFAULT NULL,
  `balance` double(15,8) NOT NULL DEFAULT 0.00000000,
  `invested` double(15,8) NOT NULL DEFAULT 0.00000000,
  `reinvested` decimal(15,2) NOT NULL DEFAULT 0.00,
  `commission` double(15,8) NOT NULL DEFAULT 0.00000000,
  `payout` double(15,8) NOT NULL DEFAULT 0.00000000,
  `investment_address` varchar(500) DEFAULT NULL,
  `last_confirmed` double(15,8) DEFAULT NULL,
  `wallet_address` tinytext DEFAULT NULL,
  `current_minimum_btc` decimal(15,8) DEFAULT NULL,
  `referral_link` tinytext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `bot_setting`
--
ALTER TABLE `bot_setting`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `error_logs`
--
ALTER TABLE `error_logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gui_account`
--
ALTER TABLE `gui_account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `account_group` (`account_group`);

--
-- Index pour la table `gui_group`
--
ALTER TABLE `gui_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Index pour la table `gui_menu`
--
ALTER TABLE `gui_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Index pour la table `gui_module`
--
ALTER TABLE `gui_module`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `gui_rbac_assignment`
--
ALTER TABLE `gui_rbac_assignment`
  ADD PRIMARY KEY (`group_id`,`rbac_items_id`),
  ADD KEY `rbac_items_id` (`rbac_items_id`);

--
-- Index pour la table `gui_rbac_items`
--
ALTER TABLE `gui_rbac_items`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `investment`
--
ALTER TABLE `investment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `telegram_id` (`telegram_id`);

--
-- Index pour la table `investment_plans`
--
ALTER TABLE `investment_plans`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `referrals`
--
ALTER TABLE `referrals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_id_referred` (`telegram_id_referred`),
  ADD KEY `telegram_id_referent` (`telegram_id_referent`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `telegram_id` (`telegram_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `telegram_id` (`telegram_id`),
  ADD UNIQUE KEY `investment_address` (`investment_address`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `bot_setting`
--
ALTER TABLE `bot_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `error_logs`
--
ALTER TABLE `error_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `gui_account`
--
ALTER TABLE `gui_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `gui_group`
--
ALTER TABLE `gui_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `gui_menu`
--
ALTER TABLE `gui_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `gui_module`
--
ALTER TABLE `gui_module`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `gui_rbac_items`
--
ALTER TABLE `gui_rbac_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `investment`
--
ALTER TABLE `investment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `investment_plans`
--
ALTER TABLE `investment_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `referrals`
--
ALTER TABLE `referrals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(25) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `gui_rbac_assignment`
--
ALTER TABLE `gui_rbac_assignment`
  ADD CONSTRAINT `gui_rbac_assignment_ibfk_1` FOREIGN KEY (`rbac_items_id`) REFERENCES `gui_rbac_items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gui_rbac_assignment_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `gui_group` (`group_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `investment`
--
ALTER TABLE `investment`
  ADD CONSTRAINT `investment_ibfk_1` FOREIGN KEY (`telegram_id`) REFERENCES `users` (`telegram_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `referrals`
--
ALTER TABLE `referrals`
  ADD CONSTRAINT `referrals_ibfk_1` FOREIGN KEY (`telegram_id_referent`) REFERENCES `users` (`telegram_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `referrals_ibfk_2` FOREIGN KEY (`telegram_id_referred`) REFERENCES `users` (`telegram_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`telegram_id`) REFERENCES `users` (`telegram_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
