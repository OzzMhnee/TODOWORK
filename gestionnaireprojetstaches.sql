-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 04 sep. 2025 à 05:05
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestionnaireprojetstaches`
--

-- --------------------------------------------------------

--
-- Structure de la table `attachment`
--

DROP TABLE IF EXISTS `attachment`;
CREATE TABLE IF NOT EXISTS `attachment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `card_id` int DEFAULT NULL,
  `uploaded_by_id` int DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` double NOT NULL,
  `uploaded_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_795FD9BB4ACC9A20` (`card_id`),
  KEY `IDX_795FD9BBA2B28FE8` (`uploaded_by_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `board`
--

DROP TABLE IF EXISTS `board`;
CREATE TABLE IF NOT EXISTS `board` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_58562B47166D1F9C` (`project_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `board`
--

INSERT INTO `board` (`id`, `project_id`, `name`, `position`) VALUES
(1, 3, 'Board 1', 1),
(2, 2, 'Board 2', 2),
(3, 1, 'Board 3', 3);

-- --------------------------------------------------------

--
-- Structure de la table `card`
--

DROP TABLE IF EXISTS `card`;
CREATE TABLE IF NOT EXISTS `card` (
  `id` int NOT NULL AUTO_INCREMENT,
  `liste_id` int DEFAULT NULL,
  `created_by_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  `due_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `archived_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `scheduled_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  `eisenhower_quadrant` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scheduled_end_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_161498D3B03A8386` (`created_by_id`),
  KEY `IDX_161498D3E85441D8` (`liste_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `card`
--

INSERT INTO `card` (`id`, `liste_id`, `created_by_id`, `title`, `description`, `position`, `due_at`, `archived_at`, `scheduled_at`, `eisenhower_quadrant`, `scheduled_end_at`) VALUES
(1, 1, 4, '1st Card', 'description', 2, '2025-09-05 12:00:00', '2025-09-03 11:59:29', '2025-09-03 04:30:00', NULL, '2025-09-03 13:30:00'),
(2, 1, 4, 'Card Active', 'description', 2, '2222-02-22 22:22:00', NULL, '2025-09-04 12:00:00', NULL, '2025-09-04 17:30:00'),
(3, 2, 4, 'normalement sort Rouge', 'Descri Description', 0, '2025-12-05 12:00:00', NULL, '2025-09-03 06:00:00', NULL, '2025-09-03 12:30:00'),
(4, 2, 4, 'jdzah', 'ho', 1, '2026-06-06 21:00:00', NULL, '2025-09-05 08:00:00', 'not-urgent-not-important', '2025-09-05 14:30:00');

-- --------------------------------------------------------

--
-- Structure de la table `checklist`
--

DROP TABLE IF EXISTS `checklist`;
CREATE TABLE IF NOT EXISTS `checklist` (
  `id` int NOT NULL AUTO_INCREMENT,
  `card_id` int DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5C696D2F4ACC9A20` (`card_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `checklist`
--

INSERT INTO `checklist` (`id`, `card_id`, `title`, `position`) VALUES
(1, 4, 'cheklist1', 3),
(2, 4, 'cheklist2', 1),
(3, 4, 'cheklist3', 2);

-- --------------------------------------------------------

--
-- Structure de la table `checklist_item`
--

DROP TABLE IF EXISTS `checklist_item`;
CREATE TABLE IF NOT EXISTS `checklist_item` (
  `id` int NOT NULL AUTO_INCREMENT,
  `checklist_id` int DEFAULT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT '0',
  `position` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_99EB20F9B16D08A7` (`checklist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `checklist_item`
--

INSERT INTO `checklist_item` (`id`, `checklist_id`, `content`, `is_done`, `position`) VALUES
(1, 2, 'truc 1', 0, 1),
(2, 2, 'truc2', 0, 4),
(3, 2, 'truc 3', 0, 3),
(4, 2, 'truc 4', 0, 2);

-- --------------------------------------------------------

--
-- Structure de la table `comment`
--

DROP TABLE IF EXISTS `comment`;
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `card_id` int NOT NULL,
  `author_id` int NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_9474526C4ACC9A20` (`card_id`),
  KEY `IDX_9474526CF675F31B` (`author_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comment`
--

INSERT INTO `comment` (`id`, `card_id`, `author_id`, `content`, `created_at`) VALUES
(1, 2, 4, 'Premier commentaire', '2025-09-03 16:03:25'),
(2, 2, 4, 'Second commentaire', '2025-09-03 16:04:55');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250902095645', '2025-09-02 09:56:51', 516),
('DoctrineMigrations\\Version20250902101758', '2025-09-02 10:18:06', 13),
('DoctrineMigrations\\Version20250902115452', '2025-09-02 11:54:55', 84),
('DoctrineMigrations\\Version20250902134958', '2025-09-02 13:50:04', 16),
('DoctrineMigrations\\Version20250903115529', '2025-09-03 11:55:35', 56),
('DoctrineMigrations\\Version20250903132501', '2025-09-03 13:25:06', 15),
('DoctrineMigrations\\Version20250903141009', '2025-09-03 14:10:13', 12),
('DoctrineMigrations\\Version20250903144630', '2025-09-03 14:46:32', 13),
('DoctrineMigrations\\Version20250903154554', '2025-09-03 15:45:58', 56),
('DoctrineMigrations\\Version20250903155239', '2025-09-03 15:55:22', 79),
('DoctrineMigrations\\Version20250903202618', '2025-09-03 20:26:21', 29);

-- --------------------------------------------------------

--
-- Structure de la table `label`
--

DROP TABLE IF EXISTS `label`;
CREATE TABLE IF NOT EXISTS `label` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `label`
--

INSERT INTO `label` (`id`, `name`, `color`) VALUES
(1, 'Urgent & Important', '#8AB599'),
(2, 'Urgent & Pas important', '#A82B05'),
(3, 'Non urgent & Pas Important', '#1B8ECC'),
(4, 'Non urgent & Important', '#DE8904');

-- --------------------------------------------------------

--
-- Structure de la table `liste`
--

DROP TABLE IF EXISTS `liste`;
CREATE TABLE IF NOT EXISTS `liste` (
  `id` int NOT NULL AUTO_INCREMENT,
  `board_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_FCF22AF4E7EC5785` (`board_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `liste`
--

INSERT INTO `liste` (`id`, `board_id`, `name`, `position`) VALUES
(1, 1, 'Liste n°1', 1),
(2, 2, 'Liste n°2', 2);

-- --------------------------------------------------------

--
-- Structure de la table `member_ship`
--

DROP TABLE IF EXISTS `member_ship`;
CREATE TABLE IF NOT EXISTS `member_ship` (
  `id` int NOT NULL AUTO_INCREMENT,
  `project_id` int DEFAULT NULL,
  `person_id` int DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_6B8C778166D1F9C` (`project_id`),
  KEY `IDX_6B8C778217BBB47` (`person_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `member_ship`
--

INSERT INTO `member_ship` (`id`, `project_id`, `person_id`, `role`) VALUES
(1, 1, 5, 'editor'),
(2, 3, 5, 'editor'),
(3, 1, 7, 'reader'),
(4, 3, 7, 'reader'),
(5, 1, 6, 'editor'),
(6, 3, 6, 'editor');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
CREATE TABLE IF NOT EXISTS `messenger_messages` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `project`
--

DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `id` int NOT NULL AUTO_INCREMENT,
  `workspace_id` int NOT NULL,
  `created_by_id` int DEFAULT NULL,
  `label_id` int DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `archived_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  KEY `IDX_2FB3D0EE82D40A1F` (`workspace_id`),
  KEY `IDX_2FB3D0EEB03A8386` (`created_by_id`),
  KEY `IDX_2FB3D0EE33B92F39` (`label_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `project`
--

INSERT INTO `project` (`id`, `workspace_id`, `created_by_id`, `label_id`, `name`, `description`, `archived_at`) VALUES
(1, 1, 4, 1, 'Project n°1', 'ici votre description...', NULL),
(2, 2, 4, 2, 'Project n°2', 'Ici votre description ....', NULL),
(3, 1, 4, 4, 'Project n°3', 'Ici votre description....', NULL),
(4, 2, 4, 4, 'Project n°4', 'Ici votre description ....', NULL),
(5, 2, 4, 3, 'Project n°5', 'Description....', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `first_name`, `last_name`) VALUES
(4, 'JLM@mail.fr', '[\"ROLE_USER\", \"ROLE_EDITOR\", \"ROLE_ADMIN\"]', '$2y$13$btVHbjrHx/EMI6OOsx7uLufc4EyrZRZJjy9W.ju1jrIh93KIE98Sa', 'Jérémie', 'LeMéchant'),
(5, 'ADupontel@mail.fr', '[\"ROLE_USER\", \"ROLE_EDITOR\"]', '$2y$13$2uA0r2pmppRMuFTOP8a6b.r7TAZydMLYtd1nsj1LISbYa1Xj9RRJa', 'Albert', 'Dupontel'),
(6, 'BCantat@mail.fr', '[\"ROLE_USER\", \"ROLE_EDITOR\"]', '$2y$13$yLcDxqjjWedoAk3rdTH4iejE33zm6XqlEMLuEdD9uWQbbkoAfQ8Be', 'Bertrand', 'CANTAT'),
(7, 'STankian@mail.fr', '[\"ROLE_USER\"]', '$2y$13$1zJwRFwjGNoOzB7upOg2i.eAO8Yfm/kzjt3bmtge1mAkQv6q1dQPm', 'Serj', 'TANKIAN');

-- --------------------------------------------------------

--
-- Structure de la table `workspace`
--

DROP TABLE IF EXISTS `workspace`;
CREATE TABLE IF NOT EXISTS `workspace` (
  `id` int NOT NULL AUTO_INCREMENT,
  `owner_id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_8D9400197E3C61F9` (`owner_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `workspace`
--

INSERT INTO `workspace` (`id`, `owner_id`, `name`) VALUES
(1, 4, 'Workspace n°1'),
(2, 4, 'Workspace n°2'),
(4, 4, 'Workspace n°3'),
(5, 4, 'Workspace n°4'),
(6, 6, 'Workspace n°1');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attachment`
--
ALTER TABLE `attachment`
  ADD CONSTRAINT `FK_795FD9BB4ACC9A20` FOREIGN KEY (`card_id`) REFERENCES `card` (`id`),
  ADD CONSTRAINT `FK_795FD9BBA2B28FE8` FOREIGN KEY (`uploaded_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `board`
--
ALTER TABLE `board`
  ADD CONSTRAINT `FK_58562B47166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`);

--
-- Contraintes pour la table `card`
--
ALTER TABLE `card`
  ADD CONSTRAINT `FK_161498D3B03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `FK_161498D3E85441D8` FOREIGN KEY (`liste_id`) REFERENCES `liste` (`id`);

--
-- Contraintes pour la table `checklist`
--
ALTER TABLE `checklist`
  ADD CONSTRAINT `FK_5C696D2F4ACC9A20` FOREIGN KEY (`card_id`) REFERENCES `card` (`id`);

--
-- Contraintes pour la table `checklist_item`
--
ALTER TABLE `checklist_item`
  ADD CONSTRAINT `FK_99EB20F9B16D08A7` FOREIGN KEY (`checklist_id`) REFERENCES `checklist` (`id`);

--
-- Contraintes pour la table `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `FK_9474526C4ACC9A20` FOREIGN KEY (`card_id`) REFERENCES `card` (`id`),
  ADD CONSTRAINT `FK_9474526CF675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `liste`
--
ALTER TABLE `liste`
  ADD CONSTRAINT `FK_FCF22AF4E7EC5785` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`);

--
-- Contraintes pour la table `member_ship`
--
ALTER TABLE `member_ship`
  ADD CONSTRAINT `FK_6B8C778166D1F9C` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
  ADD CONSTRAINT `FK_6B8C778217BBB47` FOREIGN KEY (`person_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `project`
--
ALTER TABLE `project`
  ADD CONSTRAINT `FK_2FB3D0EE33B92F39` FOREIGN KEY (`label_id`) REFERENCES `label` (`id`),
  ADD CONSTRAINT `FK_2FB3D0EE82D40A1F` FOREIGN KEY (`workspace_id`) REFERENCES `workspace` (`id`),
  ADD CONSTRAINT `FK_2FB3D0EEB03A8386` FOREIGN KEY (`created_by_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `workspace`
--
ALTER TABLE `workspace`
  ADD CONSTRAINT `FK_8D9400197E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
