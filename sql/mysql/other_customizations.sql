ALTER TABLE `companies` ADD `witholdingtaxexempted` TINYINT(1) NOT NULL DEFAULT '1';
ALTER TABLE `companies` ADD `witholdingtaxglaccount` VARCHAR(20) NULL DEFAULT NULL;
INSERT INTO  `scripts` values('CustomerWitholdingTax.php','2', 'WitholdingTax receivable');

CREATE TABLE IF NOT EXISTS `customerwitholdings` (
   `id` INT NOT NULL AUTO_INCREMENT ,
   `debtorno` varchar(10) NOT NULL ,
   `debtortransid` INT NOT NULL ,
   `amount` DECIMAL(10,2) NOT NULL ,
   `witheldamount` DECIMAL(10,2) NOT NULL ,
   `certificate` varchar(200) DEFAULT NULL,
   `date_witheld` date DEFAULT NULL,
   `date_of_certificate` date DEFAULT NULL,
   `notes` text DEFAULT NULL,
   `status` TINYINT(1) NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  ALTER TABLE `customerwitholdings` ADD UNIQUE( `debtortransid`);
