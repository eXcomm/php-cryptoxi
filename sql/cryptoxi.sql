SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`chat_sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`chat_sessions` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`chat_sessions` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `session_key` VARCHAR(32) NOT NULL ,
  `time` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `session_key_UNIQUE` (`session_key` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`chat_messages`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`chat_messages` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`chat_messages` (
  `id` INT NOT NULL ,
  `message` TEXT NOT NULL ,
  `chat_sessions_id` INT NOT NULL ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_table1_chat_sessions` (`chat_sessions_id` ASC) ,
  CONSTRAINT `fk_table1_chat_sessions`
    FOREIGN KEY (`chat_sessions_id` )
    REFERENCES `mydb`.`chat_sessions` (`id` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`chat_online`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `mydb`.`chat_online` ;

CREATE  TABLE IF NOT EXISTS `mydb`.`chat_online` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `session` CHAR(100) NOT NULL ,
  `time` INT(11) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
