SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS `skillswaps` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `skillswaps`;

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_user` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(45) DEFAULT NULL,
  `prenom` VARCHAR(45) DEFAULT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `motdepasse` VARCHAR(255) DEFAULT NULL,
  `role` ENUM('stagiaire','formateur','mentor','admin') DEFAULT NULL,
  `score` INT DEFAULT 0,
  `photo` VARCHAR(255) DEFAULT NULL,
  `filiere` VARCHAR(100) DEFAULT NULL,
  `statut` ENUM('actif','en_attente','suspendu') DEFAULT NULL,
  `date_inscription` DATETIME DEFAULT NULL,
  `note_moyenne` DECIMAL(3,1) NOT NULL DEFAULT 0.0,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `aide` (
  `id_demande` INT NOT NULL AUTO_INCREMENT,
  `titre` VARCHAR(150) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('ouvert','en_coure','resolu') DEFAULT NULL,
  `date_pub` DATETIME DEFAULT NULL,
  `tags` TEXT DEFAULT NULL,
  `id_user` INT DEFAULT NULL,
  `signal` INT DEFAULT 0,
  PRIMARY KEY (`id_demande`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `aide_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `propositions_aide` (
  `id_proposition` INT NOT NULL AUTO_INCREMENT,
  `id_demande` INT DEFAULT NULL,
  `id_user` INT DEFAULT NULL,
  `status` ENUM('en_attente','acceptee','refusee') DEFAULT NULL,
  `date_prop` DATETIME DEFAULT NULL,
  `date_rep` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_proposition`),
  KEY `id_demande` (`id_demande`),
  KEY `id_user` (`id_user`),
  CONSTRAINT `propositions_aide_ibfk_1` FOREIGN KEY (`id_demande`) REFERENCES `aide` (`id_demande`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `propositions_aide_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `aide_effectuee` (
  `id_proposition` INT NOT NULL,
  `id_mentor` INT NOT NULL,
  `id_beneficiaire` INT NOT NULL,
  `date_intervention` DATETIME DEFAULT NULL,
  `note_mentor` INT DEFAULT NULL,
  `commentaire` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_proposition`),
  KEY `id_aidant` (`id_mentor`),
  KEY `id_beneficiaire` (`id_beneficiaire`),
  CONSTRAINT `aide_effectuee_ibfk_1` FOREIGN KEY (`id_proposition`) REFERENCES `propositions_aide` (`id_proposition`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `aide_effectuee_ibfk_2` FOREIGN KEY (`id_mentor`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `aide_effectuee_ibfk_3` FOREIGN KEY (`id_beneficiaire`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `badges` (
  `id_badge` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) DEFAULT NULL,
  `points_requis` INT DEFAULT NULL,
  `icone` VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (`id_badge`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `competences` (
  `id_competence` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) DEFAULT NULL,
  `categorie` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id_competence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `obtention_badges` (
  `id_user` INT NOT NULL,
  `id_badge` INT NOT NULL,
  `confirmed_by` INT DEFAULT NULL,
  `date_obtention` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id_user`, `id_badge`),
  KEY `id_badge` (`id_badge`),
  KEY `obtention_badges_ibfk_3` (`confirmed_by`),
  CONSTRAINT `obtention_badges_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `obtention_badges_ibfk_2` FOREIGN KEY (`id_badge`) REFERENCES `badges` (`id_badge`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `obtention_badges_ibfk_3` FOREIGN KEY (`confirmed_by`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `validation_competence` (
  `id_user` INT NOT NULL,
  `id_competence` INT NOT NULL,
  `id_validateur` INT DEFAULT NULL,
  `status` ENUM('en_attente','validee','refusee') DEFAULT NULL,
  `justification` TEXT DEFAULT NULL,
  `date_demande` DATETIME DEFAULT NULL,
  `date_validation` DATETIME DEFAULT NULL,
  `niveau` ENUM('debutant','intermediaire','avance') DEFAULT NULL,
  PRIMARY KEY (`id_user`, `id_competence`),
  KEY `id_competence` (`id_competence`),
  KEY `fk_validateur` (`id_validateur`),
  CONSTRAINT `fk_validateur` FOREIGN KEY (`id_validateur`) REFERENCES `utilisateurs` (`id_user`),
  CONSTRAINT `validation_competence_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateurs` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `validation_competence_ibfk_3` FOREIGN KEY (`id_competence`) REFERENCES `competences` (`id_competence`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;