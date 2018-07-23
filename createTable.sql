CREATE TABLE `imports` ( 
`eventDatetime` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
`eventAction` VARCHAR(20) NOT NULL , 
`callRef` BIGINT UNSIGNED NOT NULL, 
`eventValue` DECIMAL NULL , 
`eventCurrencyCode` CHAR(3) NULL ) 
ENGINE = InnoDB;
