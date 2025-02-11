-- This was tested on MySQL 5.5.15. 
-- This script will NOT run from the Zencart SQL Query Executor, For advanced users ONLY.
-- To use this script you should be farmilar with using a MySQL tool such as HeidiSQL. Did NOT get it to run under phpMyAdmin!
-- Use at your own risk!
-- Backup your databases prior to using these files or making any changes.

-- This script is used to find and correct address book entries that have BAD zone ids in the address book.
-- This script will remove address entries that have bad zip codes, removed entries are move to the table address_book_issues.
-- The table "address_book_issues" should be reviewed after running this procedure.

-- Read the following.
-- 	--	The table names will need to be changed if your database uses a prefix such as 'zen_'.
--	--	Example: 
--	--			 change: address_book_issues to: 	zen_address_book_issues 
--	--			         address_book 		 to: 	zen_address_book
--	--					 zones 				 to: 	zen_zones
--	--					 tax_rates_local 	 to: 	zen_tax_rates_local					

-- check for bad addresses prior to using script, no reason to do any more if there are no bad entries.
SELECT address_book_id FROM address_book WHERE entry_zone_id NOT IN (SELECT zone_id FROM zones) LIMIT 1;

-- If bad entries exist and you have not manually corrected them....
-- USE with causion!!!

-- Create a table to hold address records that have issues.
CREATE TABLE address_book_issues (
	`address_book_id` INT(11) NOT NULL AUTO_INCREMENT,
	`customers_id` INT(11) NOT NULL DEFAULT '0',
	`entry_gender` CHAR(1) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`entry_company` VARCHAR(64) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`entry_firstname` VARCHAR(32) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`entry_lastname` VARCHAR(32) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`entry_street_address` VARCHAR(64) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`entry_suburb` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`entry_postcode` VARCHAR(10) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`entry_city` VARCHAR(32) NOT NULL DEFAULT '' COLLATE 'utf8_bin',
	`entry_state` VARCHAR(32) NULL DEFAULT NULL COLLATE 'utf8_bin',
	`entry_country_id` INT(11) NOT NULL DEFAULT '0',
	`entry_zone_id` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`address_book_id`),
	INDEX `idx_address_book_customers_id_zen` (`customers_id`)
)
COLLATE='utf8_bin'
ENGINE=MyISAM;


-- This is a DO WHILE Procedure, it is installed from a MYSQL tool like HeidieSQL.
-- Once the procedure is installed, it can be called (run) using: CALL `doWhileAddress`();
DROP PROCEDURE IF EXISTS doWhileAddress;
DELIMITER //
CREATE PROCEDURE doWhileAddress()

BEGIN
	
	SET @BadZone = NULL;
	SET @checkZip = NULL;
	SET @goodZone = NULL;
	
 addressVerify: WHILE ( (SELECT address_book_id FROM address_book WHERE entry_zone_id NOT IN (SELECT zone_id FROM zones) LIMIT 1)  ) DO
	
	-- get record with bad zone set
	SET @BadZone = (SELECT address_book_id FROM address_book WHERE entry_zone_id NOT IN (SELECT zone_id FROM zones) LIMIT 1);
	
	IF(@BadZone) THEN
	
		-- get zip code from bad record
		SET @checkZip = (SELECT entry_postcode FROM address_book WHERE address_book_id = @BadZone);
		-- remove +4 from zips
		SET @checkZip = SUBSTR(@checkZip,1,5);
		-- find correct zone based on zip 
		SET @goodZone = (SELECT zone_id FROM tax_rates_local WHERE INSTR(local_datamatch, @checkZip));
		
		IF(@goodZone) THEN
			-- correct the zone in the address book record
			UPDATE address_book SET `entry_zone_id` = @goodZone WHERE `address_book_id` = @BadZone;
			-- Display updated row
			SELECT * FROM address_book WHERE `address_book_id` = @BadZone;
		ELSE 
			SELECT 'BAD record removed', @BadZone, 'Zip Code', @checkZip;
			INSERT IGNORE INTO address_book_issues SELECT * FROM address_book where `address_book_id` = @BadZone;
			DELETE FROM address_book WHERE  `address_book_id` =  @BadZone LIMIT 1;
		END IF;
		
	END IF;

  END WHILE addressVerify;
  
  SELECT 'Done, No more bad records found, problem records were moved to the table address_book_issues!';

END//
