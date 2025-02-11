<?php
/**
 *  ot_local_sales_tax module
 *
 *   By Heather Gardner AKA: LadyHLG
 *   The module should apply tax based on the field you
 *	choose options are Zip Code, City, and Suburb.
 *	It should also compound the tax to whatever zone
 *	taxes you already have set up.  Which means you
 *	can apply multiple taxes to any zone based on
 *	different criteria.
 *  local_sales_taxes.php  version 2.5.3
 */


define('HEADING_TITLE', 'Local Sales Taxes');

define('TEXT_INFO_HEADING_NEW_LOCAL_SALES_TAX', 'New Local Sales Tax');
define('TEXT_INFO_INSERT_INTRO', 'Please enter the new sales tax with its related data');
define('TEXT_INFO_COUNTRY', 'Country:');
define('TEXT_INFO_COUNTRY_ZONE', 'Zone:<br>What zone is this tax applied to?');
define('TEXT_INFO_TAX_RATE', 'Tax Rate (%)');
define('TEXT_INFO_FIELDMATCH', 'Search Field:<br>What info are we basing this sales tax on?');
define('TEXT_INFO_DATAMATCH', 'Search Data:<br>What data are we searching for?
		<br>For Ranges use "-to-" for seperator<BR>example:53000-to-56000.
		<br><br>Use semi-colon with no spaces for deliminated lists
		<br>example:Madison;Milwaukee;Green Bay
		<br><br>Delimited lists may include both ranges and single entries
		<br>example:53525;53711;53528;54000-to-56000');
define('TEXT_INFO_RATE_DESCRIPTION', 'Description:<br>Tax description will appear in cart checkout.');
define('TEXT_INFO_TAX_SHIPPING', 'Apply this tax to shipping charges?');
define('TEXT_INFO_TAX_CLASS_TITLE', 'Tax Class:');


define('TEXT_TAX_SHIPPING_TRUE', 'True');
define('TEXT_TAX_SHIPPING_FALSE', 'False');

define('TEXT_INFO_DESCRIPTION', 'Description');

define('TEXT_ALL_COUNTRIES', 'All Countries');
define('TYPE_BELOW', 'Select Zone');
define('PLEASE_SELECT', 'Please Select');


define('TEXT_INFO_HEADING_EDIT_LOCAL_SALES_TAX', 'Edit Tax Rate');
define('TEXT_INFO_EDIT_INTRO', 'Please make any necessary changes');


define('TEXT_INFO_HEADING_DELETE_LOCAL_SALES_TAX', 'Delete Tax Rate');
define('TEXT_INFO_DELETE_INTRO', 'Are you sure you want to delete this tax rate?');


define('TABLE_HEADING_LOCAL_SALES_TAX_ZONE', 'Tax Zone');
define('TABLE_HEADING_LOCAL_SALES_TAX_FIELD', 'Apply To');
define('TABLE_HEADING_LOCAL_SALES_TAX_DATA', 'Look For');
define('TABLE_HEADING_LOCAL_SALES_TAX_RATE', 'Tax Rate');
define('TABLE_HEADING_LOCAL_SALES_TAX_LABEL', 'Tax Description');
define('TABLE_HEADING_LOCAL_SALES_TAX_SHIPPING', 'Tax Shipping');
define('TABLE_HEADING_LOCAL_SALES_TAX_CLASS', 'Tax Class');
define('TABLE_HEADING_LOCAL_SALES_TAX_ID','ID');
define('TABLE_HEADING_ACTION', 'Action');

define('TEXT_DISPLAY_NUMBER_OF_LOCAL_ST', 'Displaying <b>%d</b> to <b>%d</b> (of <b>%d</b> local taxes)');
?>