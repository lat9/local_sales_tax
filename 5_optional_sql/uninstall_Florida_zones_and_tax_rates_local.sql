-- This was tested on MySQL 5.5.15 with Zencart v1.5.0 (SQL Query Executor).
-- Use at your own risk!
-- Backup your databases prior to using these files or making any changes.

-- NOTES: 
--	The DELETE statements are used to remove the Florida entries from the tables zones_to_geo_zones, geo_zones, tax_rates and to remove the tax_rates_local table.
--	The order these are run is important.

--	No changes to table names (prefix) are required if you use the SQL Query Executor from the admin (Tools\Install SQL Patches). 
--	But, If you use a third party database tool, such as HeidiSQL, then you should read the following.
--	-- 	The table names will need to be changed if your database uses a prefix such as 'zen_'.
--	--	Example: change zones to zen_zones and tax_rates_local to zen_tax_rates_local.

-- Delete tax_rates_local table
DROP TABLE tax_rates_local;

-- Delete Florida entries in zones_to_geo_zones table
DELETE FROM zones_to_geo_zones WHERE  geo_zone_id = (SELECT geo_zone_id FROM geo_zones WHERE geo_zone_description = 'FL Sales Tax');

-- Delete Florida entries in geo_zones table
DELETE FROM geo_zones WHERE geo_zone_description = 'FL Sales Tax';

-- Delete Florida entries in tax_rates table
DELETE FROM tax_rates WHERE tax_description = 'FL Sales Tax (6%)';

-- Zencart v1.5.0 ONLY Delete the Admin menu item 'local Sales Taxes'
DELETE FROM admin_pages WHERE  `page_key`='localSalesTaxes' LIMIT 1;
