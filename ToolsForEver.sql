	-- MySQL Script generated by MySQL Workbench
-- Wed Jun 19 09:50:42 2024
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema ToolsForEver
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ToolsForEver
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ToolsForEver` DEFAULT CHARACTER SET utf8 ;
USE `ToolsForEver` ;

-- -----------------------------------------------------
-- Table `ToolsForEver`.`Vestigingen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`Vestigingen` (
  `idVestigingen` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NULL,
  PRIMARY KEY (`idVestigingen`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`artikel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`artikel` (
  `idartikel` INT NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NULL,
  `inkoopprijs` DECIMAL(4,2) NULL,
  `type` VARCHAR(45) NULL,
  `verkoopprijs` DECIMAL(4,2) NULL,
  `fabriek` VARCHAR(45) NULL,
  PRIMARY KEY (`idartikel`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`voorraad`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`voorraad` (
  `Vestigingen_idVestigingen` INT NOT NULL,
  `artikel_idartikel` INT NOT NULL,
  `aantal` INT NULL,
  PRIMARY KEY (`Vestigingen_idVestigingen`, `artikel_idartikel`),
  INDEX `fk_Vestigingen_has_artikel_artikel1_idx` (`artikel_idartikel` ASC) VISIBLE,
  INDEX `fk_Vestigingen_has_artikel_Vestigingen_idx` (`Vestigingen_idVestigingen` ASC) VISIBLE,
  CONSTRAINT `fk_Vestigingen_has_artikel_Vestigingen`
    FOREIGN KEY (`Vestigingen_idVestigingen`)
    REFERENCES `ToolsForEver`.`Vestigingen` (`idVestigingen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Vestigingen_has_artikel_artikel1`
    FOREIGN KEY (`artikel_idartikel`)
    REFERENCES `ToolsForEver`.`artikel` (`idartikel`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`bestellijst`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`bestellijst` (
  `idbestellijst` INT NOT NULL AUTO_INCREMENT,
  `aantal besteld` VARCHAR(45) NULL,
  `Vestigingen_idVestigingen` INT NOT NULL,
  `bestel datum` DATETIME NULL,
  `leverings datum` VARCHAR(45) NULL,
  PRIMARY KEY (`idbestellijst`, `Vestigingen_idVestigingen`),
  INDEX `fk_bestellijst_Vestigingen1_idx` (`Vestigingen_idVestigingen` ASC) VISIBLE,
  CONSTRAINT `fk_bestellijst_Vestigingen1`
    FOREIGN KEY (`Vestigingen_idVestigingen`)
    REFERENCES `ToolsForEver`.`Vestigingen` (`idVestigingen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`artikel_has_bestellijst`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`artikel_has_bestellijst` (
  `artikel_idartikel` INT NOT NULL,
  `bestellijst_idbestellijst` INT NOT NULL,
  `aantal te bestellen` INT NULL,
  PRIMARY KEY (`artikel_idartikel`, `bestellijst_idbestellijst`),
  INDEX `fk_artikel_has_bestellijst_bestellijst1_idx` (`bestellijst_idbestellijst` ASC) VISIBLE,
  INDEX `fk_artikel_has_bestellijst_artikel1_idx` (`artikel_idartikel` ASC) VISIBLE,
  CONSTRAINT `fk_artikel_has_bestellijst_artikel1`
    FOREIGN KEY (`artikel_idartikel`)
    REFERENCES `ToolsForEver`.`artikel` (`idartikel`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artikel_has_bestellijst_bestellijst1`
    FOREIGN KEY (`bestellijst_idbestellijst`)
    REFERENCES `ToolsForEver`.`bestellijst` (`idbestellijst`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
