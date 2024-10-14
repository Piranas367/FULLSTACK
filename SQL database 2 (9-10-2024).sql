-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema ToolsForEver
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ToolsForEver
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ToolsForEver` DEFAULT CHARACTER SET utf8mb4 ;
USE `ToolsForEver` ;

-- -----------------------------------------------------
-- Table `ToolsForEver`.`Vestigingen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`Vestigingen` (
  `idVestigingen` INT(254) NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idVestigingen`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`artikel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`artikel` (
  `idartikel` INT(11) NOT NULL AUTO_INCREMENT,
  `naam` VARCHAR(225) NULL DEFAULT NULL,
  `inkoopprijs` DECIMAL(65,0) NULL DEFAULT NULL,
  `typeProduct` VARCHAR(425) NULL DEFAULT NULL,
  `verkoopprijs` DECIMAL(65,0) NULL DEFAULT NULL,
  `fabriek` VARCHAR(425) NULL DEFAULT NULL,
  PRIMARY KEY (`idartikel`))
ENGINE = InnoDB
AUTO_INCREMENT = 37
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`bestellijst`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`bestellijst` (
  `idbestellijst` INT(11) NOT NULL AUTO_INCREMENT,
  `aantal besteld` INT NULL DEFAULT NULL,
  `idVestigingen` INT(11) NOT NULL,
  `bestel datum` DATETIME NULL DEFAULT NULL,
  `leverings datum` VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (`idbestellijst`, `idVestigingen`),
  INDEX `fk_bestellijst_Vestigingen1_idx` (`idVestigingen` ASC) VISIBLE,
  CONSTRAINT `fk_bestellijst_Vestigingen1`
    FOREIGN KEY (`idVestigingen`)
    REFERENCES `ToolsForEver`.`Vestigingen` (`idVestigingen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`Bestellingen`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`Bestellingen` (
  `idartikel` INT(11) NOT NULL AUTO_INCREMENT,
  `idbestellijst` INT(11) NOT NULL,
  `aantal` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`idartikel`, `idbestellijst`),
  INDEX `fk_artikel_has_bestellijst_bestellijst1_idx` (`idbestellijst` ASC) VISIBLE,
  INDEX `fk_artikel_has_bestellijst_artikel1_idx` (`idartikel` ASC) VISIBLE,
  CONSTRAINT `fk_artikel_has_bestellijst_artikel1`
    FOREIGN KEY (`idartikel`)
    REFERENCES `ToolsForEver`.`artikel` (`idartikel`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_artikel_has_bestellijst_bestellijst1`
    FOREIGN KEY (`idbestellijst`)
    REFERENCES `ToolsForEver`.`bestellijst` (`idbestellijst`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Table `ToolsForEver`.`voorraad`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ToolsForEver`.`voorraad` (
  `idVestigingen` INT(11) NOT NULL,
  `aantal` INT(11) NULL DEFAULT NULL,
  `idVoorraad` VARCHAR(45) NOT NULL,
  `idartikel` INT(11) NOT NULL,
  PRIMARY KEY (`idVestigingen`, `idVoorraad`, `idartikel`),
  INDEX `fk_Vestigingen_has_artikel_Vestigingen_idx` (`idVestigingen` ASC) VISIBLE,
  INDEX `fk_voorraad_artikel1_idx` (`idartikel` ASC) VISIBLE,
  CONSTRAINT `fk_Vestigingen_has_artikel_Vestigingen`
    FOREIGN KEY (`idVestigingen`)
    REFERENCES `ToolsForEver`.`Vestigingen` (`idVestigingen`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_voorraad_artikel1`
    FOREIGN KEY (`idartikel`)
    REFERENCES `ToolsForEver`.`artikel` (`idartikel`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
