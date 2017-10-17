CREATE TABLE IF NOT EXISTS `#__uber_driver` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`modified_by` INT(11)  NOT NULL ,
`phone` VARCHAR(255)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`avatar` TEXT NOT NULL ,
`id_card` VARCHAR(255)  NOT NULL ,
`number_plates` VARCHAR(255)  NOT NULL ,
`number_seat` VARCHAR(255)  NOT NULL ,
`car_type` VARCHAR(255)  NOT NULL ,
`license` VARCHAR(255)  NOT NULL ,
`vehicle_registration` TEXT NOT NULL ,
`address` VARCHAR(255)  NOT NULL ,
`balance` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

