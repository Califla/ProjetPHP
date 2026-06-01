SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';


CREATE SCHEMA IF NOT EXISTS `skillswaps` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
USE `skillswaps` ;


CREATE TABLE IF NOT EXISTS `skillswaps`.`utilisateurs` (
  `id_user` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(45) NULL DEFAULT NULL,
  `prenom` VARCHAR(45) NULL DEFAULT NULL,
  `email` VARCHAR(100) NULL DEFAULT NULL,
  `motdepasse` VARCHAR(255) NULL DEFAULT NULL,
  `role` ENUM('stagiaire', 'formateur', 'admin') NULL DEFAULT NULL,
  `score` INT NULL DEFAULT '0',
  `photo` VARCHAR(255) NULL DEFAULT NULL,
  `filiere` VARCHAR(100) NULL DEFAULT NULL,
  `statut` ENUM('actif', 'en_attente', 'suspendu') NULL DEFAULT NULL,
  `date_inscription` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE INDEX `email` (`email` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`aide` (
  `id_demande` INT NOT NULL AUTO_INCREMENT,
  `signal` TINYINT NULL,
  `titre` VARCHAR(150) NULL DEFAULT NULL,
  `description` TEXT NULL DEFAULT NULL,
  `status` ENUM('ouvert', 'ferme', 'resolu') NULL DEFAULT NULL,
  `date_pub` DATETIME NULL DEFAULT NULL,
  `tags` TEXT NULL DEFAULT NULL,
  `filiere` VARCHAR(100) NULL DEFAULT NULL,
  `level` VARCHAR(50) NULL DEFAULT NULL,
  `id_user` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id_demande`),
  INDEX `id_user` (`id_user` ASC),
  CONSTRAINT `aide_ibfk_1`
    FOREIGN KEY (`id_user`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`propositions_aide` (
  `id_proposition` INT NOT NULL AUTO_INCREMENT,
  `id_demande` INT NULL DEFAULT NULL,
  `id_user` INT NULL DEFAULT NULL,
  `status` ENUM('en_attente', 'acceptee', 'refusee') NULL DEFAULT NULL,
  `date_prop` DATETIME NULL DEFAULT NULL,
  `date_rep` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_proposition`),
  INDEX `id_demande` (`id_demande` ASC),
  INDEX `id_user` (`id_user` ASC),
  CONSTRAINT `propositions_aide_ibfk_1`
    FOREIGN KEY (`id_demande`)
    REFERENCES `skillswaps`.`aide` (`id_demande`),
  CONSTRAINT `propositions_aide_ibfk_2`
    FOREIGN KEY (`id_user`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`aide_effectuee` (
  `id_proposition` INT NOT NULL,
  `id_mentor` INT NOT NULL,
  `id_beneficiaire` INT NOT NULL,
  `date_intervention` DATETIME NULL,
  `note_mentor` INT NULL,
  `commentaire` TEXT NULL,
  INDEX `id_proposition` (`id_proposition` ASC),
  INDEX `id_aidant` (`id_mentor` ASC),
  INDEX `id_beneficiaire` (`id_beneficiaire` ASC),
  PRIMARY KEY (`id_proposition`, `id_mentor`, `id_beneficiaire`),
  CONSTRAINT `aide_effectuee_ibfk_1`
    FOREIGN KEY (`id_proposition`)
    REFERENCES `skillswaps`.`propositions_aide` (`id_proposition`),
  CONSTRAINT `aide_effectuee_ibfk_2`
    FOREIGN KEY (`id_mentor`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`),
  CONSTRAINT `aide_effectuee_ibfk_3`
    FOREIGN KEY (`id_beneficiaire`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`badges` (
  `id_badge` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NULL DEFAULT NULL,
  `points_requis` INT NULL DEFAULT NULL,
  `image` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id_badge`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`competences` (
  `id_competence` INT NOT NULL AUTO_INCREMENT,
  `nom` VARCHAR(100) NULL DEFAULT NULL,
  `categorie` VARCHAR(50) NULL DEFAULT NULL,
  PRIMARY KEY (`id_competence`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`obtention_badges` (
  `id_user` INT NOT NULL,
  `id_badge` INT NOT NULL,
  `confirmed_by` INT NULL DEFAULT NULL,
  `date_obtention` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`, `id_badge`),
  INDEX `id_badge` (`id_badge` ASC),
  CONSTRAINT `obtention_badges_ibfk_1`
    FOREIGN KEY (`id_user`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`),
  CONSTRAINT `obtention_badges_ibfk_2`
    FOREIGN KEY (`id_badge`)
    REFERENCES `skillswaps`.`badges` (`id_badge`),
  CONSTRAINT `obtention_badges_ibfk_3`
    FOREIGN KEY (`confirmed_by`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`possede` (
  `id_user` INT NOT NULL,
  `id_competence` INT NOT NULL,
  `niveau` ENUM('debutant', 'intermediaire', 'avance', 'expert') NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`, `id_competence`),
  INDEX `id_competence` (`id_competence` ASC),
  CONSTRAINT `possede_ibfk_1`
    FOREIGN KEY (`id_user`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`),
  CONSTRAINT `possede_ibfk_2`
    FOREIGN KEY (`id_competence`)
    REFERENCES `skillswaps`.`competences` (`id_competence`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;



CREATE TABLE IF NOT EXISTS `skillswaps`.`validation_competence` (
  `id_user` INT NOT NULL,
  `id_validateur` INT NULL DEFAULT NULL,
  `id_competence` INT NOT NULL,
  `status` ENUM('en_attente', 'validee', 'refusee') NULL DEFAULT NULL,
  `justification` TEXT NULL DEFAULT NULL,
  `date_validation` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_user`, `id_competence`),
  INDEX `id_validateur` (`id_validateur` ASC),
  INDEX `id_competence` (`id_competence` ASC),
  CONSTRAINT `validation_competence_ibfk_1`
    FOREIGN KEY (`id_user`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`),
  CONSTRAINT `validation_competence_ibfk_2`
    FOREIGN KEY (`id_validateur`)
    REFERENCES `skillswaps`.`utilisateurs` (`id_user`),
  CONSTRAINT `validation_competence_ibfk_3`
    FOREIGN KEY (`id_competence`)
    REFERENCES `skillswaps`.`competences` (`id_competence`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_general_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
