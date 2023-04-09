CREATE TABLE IF NOT EXISTS `sportisimo`.`users` (
                                                    `id` INT AUTO_INCREMENT,
                                                    `login` VARCHAR(45) NOT NULL,
                                                    `password` TEXT NOT NULL,
                                                    `email` VARCHAR(64) NOT NULL,
                                                    PRIMARY KEY (`id`),
                                                    UNIQUE INDEX `login_UNIQUE` (`login` ASC) ,
                                                    UNIQUE INDEX `email_UNIQUE` (`email` ASC) )
    ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `sportisimo`.`brands`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sportisimo`.`brands` (
                                                     `id` INT AUTO_INCREMENT,
                                                     `name` VARCHAR(45) NOT NULL,
                                                     `created_by` INT NOT NULL,
                                                     PRIMARY KEY (`id`, `created_by`),
                                                     INDEX `fk_brands_users_idx` (`created_by` ASC) ,
                                                     UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
                                                     CONSTRAINT `fk_brands_users`
                                                         FOREIGN KEY (`created_by`)
                                                             REFERENCES `sportisimo`.`users` (`id`)
                                                             ON DELETE NO ACTION
                                                             ON UPDATE NO ACTION)
    ENGINE = InnoDB;

