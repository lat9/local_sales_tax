<?php
if (!defined('IS_ADMIN_FLAG')) {
	die('Illegal Access');
}

// Define the version.
$version = '2.5.3';

// This block uninstalls the plugin / module
if(!local_sales_taxes_uninstall($version)) {
	$failed = false;

	// Disable this script from running again
	if(file_exists(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.install.php'))
	{
		if(!unlink(DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.install.php'))
		{
			$messageStack->add(sprintf(LOCAL_SALES_TAXES_INSTALL_ERROR_AUTOLOAD, DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.install.php'), 'error');
			$failed = true;
		}
	}

	if(!$failed) $messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_SUCCESS, 'success');
}
else {
	$messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_ERROR, 'error');
}

function local_sales_taxes_uninstall($version) {
	global $db, $messageStack;

	$failed = false;

	// Make sure the order total module has been uninstalled.
	// Stop immediately if it has not been uninstalled.
	if(defined(MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STATUS)) {
		$messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_ERROR_MODULE_INSTALLED, 'error');
		return true;
	}

	$db->Execute('DROP TABLE IF EXISTS ' . TABLE_LOCAL_SALES_TAXES);
	if(local_sales_taxes_check_table(TABLE_LOCAL_SALES_TAXES)) {
		$messageStack->add(sprintf(LOCAL_SALES_TAXES_UNINSTALL_ERROR_TABLE, TABLE_LOCAL_SALES_TAXES), 'error');
		$failed = true;
	}

	if(function_exists('zen_deregister_admin_pages')) {
		if(zen_page_key_exists('localSalesTaxes')) zen_deregister_admin_pages('localSalesTaxes');
		if(zen_page_key_exists('localSalesTaxes')) {
			$messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_ERROR_ADMIN_PAGES);
			$failed = true;
		}
	}

	// Attempt to remove files
	$files = array(
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes.php',
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.php',
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_local_sales_taxes_install.php',
		DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/local_sales_taxes.php',
		DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/extra_definitions/local_sales_taxes.php',
		DIR_FS_ADMIN . FILENAME_LOCAL_SALES_TAXES . '.php',
		DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ot_local_sales_taxes.php',
		DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/functions_local_sales_taxes.php',
		DIR_FS_CATALOG . DIR_WS_LANGUAGES . 'english/modules/order_total/ot_local_sales_taxes.php',
		DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/ot_local_sales_taxes.php',

		// Zen Cart 1.3.9 specific files
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'boxes/extra_boxes/local_sales_tax_taxes_dhtml.php',

		// Files from older versions
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes_filenames.php',
		DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes_database_tables.php',
		DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ot_local_sales_taxes_databse_tables.php',
		DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/functions_local_taxes.php',
	);
	foreach($files as $file) {
		@unlink($file);

		if(file_exists($file)) {
			$messageStack->add(sprintf(LOCAL_SALES_TAXES_UNINSTALL_ERROR_FILE_FOUND, $file), 'error');
			$failed = true;
		}
	}

	return $failed;
}

function local_sales_taxes_check_table($table) {
	global $db;

	$check = $db->Execute(
		'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS ' .
		'WHERE TABLE_SCHEMA = \'' . DB_DATABASE . '\' ' .
		'AND TABLE_NAME = \'' . $db->prepare_input($table) . '\''
	);
	return !$check->EOF;
}