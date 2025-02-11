<?php
/**
 *  ot_local_sales_tax module
 *
 *  By Heather Gardner AKA: LadyHLG
 *  The module should apply tax based on the field you
 *	choose options are Zip Code, City, and Suburb.
 *	It should also compound the tax to whatever zone
 *	taxes you already have set up.  Which means you
 *	can apply multiple taxes to any zone based on
 *	different criteria.
 *  local_sales_taxes.php  version 2.5.3
 */

define('BOX_TAXES_LOCAL_SALES_TAXES', 'Local Sales Taxes');

// Installation Scripts
define('LOCAL_SALES_TAXES_INSTALL_SUCCESS', BOX_TAXES_LOCAL_SALES_TAXES . ' installation completed!');
define('LOCAL_SALES_TAXES_INSTALL_ERROR', BOX_TAXES_LOCAL_SALES_TAXES . ' installation failed!');
define('LOCAL_SALES_TAXES_UNINSTALL_SUCCESS', BOX_TAXES_LOCAL_SALES_TAXES . ' removal completed!');
define('LOCAL_SALES_TAXES_UNINSTALL_ERROR', BOX_TAXES_LOCAL_SALES_TAXES . ' removal failed!');
define('LOCAL_SALES_TAXES_INSTALL_ERROR_AUTOLOAD', 'The auto-loader file \'%s\' has not been deleted. For this module to work you must delete this file manually. Before you post on the Zen Cart forum to ask, YES you are REALLY supposed to follow these instructions');
define('LOCAL_SALES_TAXES_INSTALL_ERROR_FILE_NOT_FOUND', 'Filesystem Error: Unable to access \'%s\'. Please make sure you uploaded the file and your webserver has access to read the file!');
define('LOCAL_SALES_TAXES_INSTALL_ERROR_FILE_FOUND', 'The file \'%s\' has not been deleted. For this module to work you must delete this file manually. Before you post on the Zen Cart forum to ask, YES you are REALLY supposed to follow these instructions');
define('LOCAL_SALES_TAXES_INSTALL_ERROR_SORT_ORDER', 'Database Error: Unable to access sort_order in table \'%s\'!');
define('LOCAL_SALES_TAXES_UNINSTALL_ERROR_DELETE', 'Database Error: Unable to delete \'%s\' in table \'%s\'!');
define('LOCAL_SALES_TAXES_UNINSTALL_ERROR_TABLE', 'Database Error: Unable to remove table \'%s\'!');
define('LOCAL_SALES_TAXES_UNINSTALL_ERROR_ADMIN_PAGES', 'Unable to remove the registration of admin pages');
define('LOCAL_SALES_TAXES_UNINSTALL_ERROR_FILE_FOUND', 'The file \'%s\' has not been deleted. To finish the removal you will need to manually delete the file from your server!');
define('LOCAL_SALES_TAXES_UNINSTALL_ERROR_MODULE_INSTALLED', 'The ' . BOX_TAXES_LOCAL_SALES_TAXES . ' module is still installed. You need to <a href="' . zen_href_link(FILENAME_MODULES, 'set=ordertotal&module=ot_local_sales_taxes') . '">remove the ' . BOX_TAXES_LOCAL_SALES_TAXES . ' order total module</a> before uninstalling. This will remove all associated data!');
