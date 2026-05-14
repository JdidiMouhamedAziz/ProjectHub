-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 14 mai 2026 à 02:05
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projecthub`
--

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `type` enum('approved','rejected') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `task_id`, `type`, `message`, `is_read`, `created_at`) VALUES
(2, 23, 4, 'approved', '✅ Your submission for task \"Write Unit Tests\" has been approved!', 1, '2026-05-11 07:01:37'),
(3, 22, 1, 'rejected', '❌ Your submission for task \"Design Homepage UI\" was rejected. Please review and resubmit.', 1, '2026-05-11 07:06:14'),
(4, 23, 4, 'rejected', '❌ Your submission for task \"Write Unit Tests\" was rejected. Please review and resubmit.', 1, '2026-05-11 07:08:14'),
(5, 22, 1, 'approved', '✅ Your submission for task \"Design Homepage UI\" has been approved!', 1, '2026-05-11 07:08:23'),
(6, 23, 4, 'approved', '✅ Your submission for task \"Write Unit Tests\" has been approved!', 1, '2026-05-11 07:20:40'),
(7, 22, 8, 'approved', '✅ Your submission for task \"Leave Request Form\" has been approved!', 1, '2026-05-11 07:24:38'),
(8, 23, 2, 'approved', '✅ Your submission for task \"Build Product Listing\" has been approved!', 1, '2026-05-11 07:24:58'),
(9, 23, 4, 'rejected', '❌ Your submission for task \"Write Unit Tests\" was rejected. Please review and resubmit.', 1, '2026-05-12 16:57:49'),
(10, 23, 5, 'approved', '✅ Your submission for task \"Auth Module\" has been approved!', 1, '2026-05-12 17:08:50'),
(11, 22, 8, 'approved', '✅ Your submission for task \"Leave Request Form\" has been approved!', 0, '2026-05-12 17:09:05'),
(12, 22, 8, 'approved', '✅ Your submission for task \"Leave Request Form\" has been approved!', 0, '2026-05-12 17:09:13'),
(13, 23, 5, 'approved', '✅ Your submission for task \"Auth Module\" has been approved!', 1, '2026-05-12 17:14:03'),
(14, 23, 5, 'approved', '✅ Your submission for task \"Auth Module\" has been approved!', 1, '2026-05-12 17:15:40'),
(15, 22, 8, 'approved', '✅ Your submission for task \"Leave Request Form\" has been approved!', 0, '2026-05-12 21:46:43'),
(16, 23, 5, 'approved', '✅ Your submission for task \"Auth Module\" has been approved!', 1, '2026-05-12 22:01:41'),
(24, 23, 4, 'approved', '✅ Your submission for task \"Write Unit Tests\" has been approved!', 1, '2026-05-13 15:33:38'),
(25, 24, 9, 'rejected', '❌ Your submission for task \"Payroll Calculator\" was rejected. Please review and resubmit.', 0, '2026-05-13 23:02:01'),
(26, 24, 9, 'rejected', '❌ Your submission for task \"Payroll Calculator\" was rejected. Please review and resubmit.', 0, '2026-05-13 23:05:21');

-- --------------------------------------------------------

--
-- Structure de la table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projects`
--

INSERT INTO `projects` (`id`, `title`, `description`, `created_at`) VALUES
(1, 'E-Commerce Platform', 'Full stack online store with cart, checkout and admin panel.', '2026-05-10 21:22:57'),
(2, 'Mobile Banking App', 'Secure mobile app for transfers, balance and notifications.', '2026-05-10 21:22:57'),
(3, 'HR Management System', 'Internal tool for leave requests, payroll and employee records.', '2026-05-10 21:22:57');

-- --------------------------------------------------------

--
-- Structure de la table `project_members`
--

CREATE TABLE `project_members` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('admin','member') NOT NULL DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `project_members`
--

INSERT INTO `project_members` (`project_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 20, 'admin', '2026-05-10 21:22:57'),
(1, 22, 'member', '2026-05-10 21:22:57'),
(1, 23, 'member', '2026-05-13 18:11:53'),
(1, 24, 'member', '2026-05-13 15:31:16'),
(2, 21, 'admin', '2026-05-10 21:22:57'),
(2, 23, 'member', '2026-05-10 21:22:57'),
(2, 24, 'member', '2026-05-10 21:22:57'),
(3, 15, 'member', '2026-05-13 23:09:35'),
(3, 20, 'admin', '2026-05-10 21:22:57'),
(3, 22, 'member', '2026-05-10 21:22:57'),
(3, 24, 'member', '2026-05-10 21:22:57');

-- --------------------------------------------------------

--
-- Structure de la table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('open','in_progress','submitted','done') DEFAULT 'open',
  `complexity` tinyint(4) NOT NULL CHECK (`complexity` between 1 and 9),
  `project_id` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `status`, `complexity`, `project_id`, `assigned_to`, `created_at`) VALUES
(1, 'Design Homepage UI', 'Create wireframes and mockups for the homepage.', 'done', 2, 1, 22, '2026-05-10 21:22:57'),
(2, 'Build Product Listing', 'Implement product grid with filters and pagination.', 'submitted', 5, 1, 23, '2026-05-10 21:22:57'),
(3, 'Setup Payment Gateway', 'Integrate Stripe for checkout and refund flows.', 'open', 8, 1, 22, '2026-05-10 21:22:57'),
(4, 'Write Unit Tests', 'Cover cart and checkout logic with PHPUnit tests.', 'done', 4, 1, 23, '2026-05-10 21:22:57'),
(5, 'Auth Module', 'JWT login, refresh tokens and biometric support.', 'done', 7, 2, 23, '2026-05-10 21:22:57'),
(6, 'Transfer Flow UI', 'Design and implement the money transfer screens.', 'submitted', 5, 2, 24, '2026-05-10 21:22:57'),
(7, 'Push Notifications', 'Firebase integration for transaction alerts.', 'submitted', 3, 2, 23, '2026-05-10 21:22:57'),
(8, 'Leave Request Form', 'Build form with approval workflow and email notification.', 'done', 7, 3, 22, '2026-05-10 21:22:57'),
(9, 'Payroll Calculator', 'Implement salary computation with tax deductions.', 'in_progress', 9, 3, 24, '2026-05-10 21:22:57'),
(10, 'Employee Dashboard', 'Overview page showing stats, leave balance and tasks.', 'done', 3, 3, 22, '2026-05-10 21:22:57'),
(11, 'aaa', 'aa', 'submitted', 2, 1, 23, '2026-05-10 22:00:21'),
(31, 'aaaaaaa', 'aa', 'open', 5, 1, NULL, '2026-05-13 15:30:04'),
(34, 'aa', 'aa', 'open', 5, 1, 20, '2026-05-13 18:13:16');

-- --------------------------------------------------------

--
-- Structure de la table `task_submissions`
--

CREATE TABLE `task_submissions` (
  `id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `git_link` varchar(500) NOT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `task_submissions`
--

INSERT INTO `task_submissions` (`id`, `task_id`, `git_link`, `message`, `status`, `created_at`) VALUES
(1, 1, 'https://github.com/alice/ecommerce/pull/12', 'Homepage design completed. All breakpoints tested.', 'approved', '2026-05-10 21:22:57'),
(2, 4, 'https://github.com/bob/ecommerce/pull/18', 'Unit tests written for cart and checkout. Coverage at 87%.', 'approved', '2026-05-10 21:22:57'),
(3, 5, 'https://github.com/bob/banking/pull/7', 'Auth module complete. JWT + biometric both working.', 'approved', '2026-05-10 21:22:57'),
(4, 8, 'https://github.com/alice/hrms/pull/3', 'Leave form done with email notifications via SMTP.', 'approved', '2026-05-10 21:22:57'),
(5, 10, 'https://github.com/alice/hrms/pull/9', 'Dashboard complete. Charts integrated with real data.', 'approved', '2026-05-10 21:22:57'),
(6, 4, 'sdcs', 'qscvs', 'pending', '2026-05-11 07:18:18'),
(7, 7, 'vSV', '<V', 'pending', '2026-05-11 07:18:33'),
(8, 4, 'vs', '<v', 'rejected', '2026-05-11 07:18:38'),
(9, 2, 'csq', 'cq', 'approved', '2026-05-11 07:24:26'),
(10, 9, '', 'Submitted', 'rejected', '2026-05-12 21:53:00'),
(12, 6, 'aa', 'aa', 'pending', '2026-05-12 22:35:32'),
(20, 4, 'aa', '', 'approved', '2026-05-12 23:28:22'),
(23, 11, '', 'Submitted', 'pending', '2026-05-13 18:12:11'),
(24, 2, 'aa', '', 'pending', '2026-05-13 22:50:48'),
(25, 2, 'aa', '', 'pending', '2026-05-13 22:50:48'),
(26, 2, 'aa', '', 'pending', '2026-05-13 22:50:48'),
(27, 2, 'aa', '', 'pending', '2026-05-13 22:50:48'),
(28, 2, 'aa', '', 'pending', '2026-05-13 22:50:48'),
(29, 2, 'aaaa', '', 'pending', '2026-05-13 22:53:14');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('manager','user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(13, 'test', 'test@test.com', '$2y$10$DaFlDp7IDr.rxcDt8uN8TOL1BEwm9LAL8FS0ekGhbVL4rdirhEoxm', 'admin', '2026-05-07 22:58:18'),
(15, 'manager', 'manager@test.co', '$2y$10$x/R5/3zxaSkAVcJ3luGBu.WVvEwioTMr.x1CRPZf0IrsZvG.Zon9u', 'manager', '2026-05-07 23:18:31'),
(20, 'manager1', 'manager1@test.com', '$2y$10$DuGz/BUAq5pmvbHw/U1dKOmMYkGq1tooKaqMzu88wxoXuTAyNTeGy', 'manager', '2026-05-10 21:22:57'),
(21, 'manager', 'manager2@test.com', '$2y$10$FnkDJDNEmZhZLNc4Ip2IV.yMbju87H05uoOG9PIHnDvaW9QKnDCi.', 'manager', '2026-05-10 21:22:57'),
(22, 'alice', 'alice@test.com', '$2y$10$eEGRhKjclcB4qXPJyBj5j.dFYq5dzPs1AWc/O8Pj0T8u84DhoJMoG', 'user', '2026-05-10 21:22:57'),
(23, 'bob', 'bob@test.com', '$2y$10$znF3jQxb42DgCRQ10oynSO0cVxRAkh44j7CV/nMIBbrHE1PGEHwwK', 'user', '2026-05-10 21:22:57'),
(24, 'charlie', 'charlie@test.com', '$2y$10$vS6bHSrsRoZFHcVmYQFXf.bjGerobhIbg4BWnzC96CMjem9LngGE6', 'user', '2026-05-10 21:22:57');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notifications_ibfk_2` (`task_id`);

--
-- Index pour la table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `project_members`
--
ALTER TABLE `project_members`
  ADD PRIMARY KEY (`project_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_id` (`project_id`),
  ADD KEY `assigned_to` (`assigned_to`);

--
-- Index pour la table `task_submissions`
--
ALTER TABLE `task_submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_id` (`task_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT pour la table `task_submissions`
--
ALTER TABLE `task_submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `project_members`
--
ALTER TABLE `project_members`
  ADD CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `task_submissions`
--
ALTER TABLE `task_submissions`
  ADD CONSTRAINT `task_submissions_ibfk_1` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
