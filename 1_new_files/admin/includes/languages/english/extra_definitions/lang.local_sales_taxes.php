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
$local_sales_tax_name = 'Local Sales Taxes';
return [
    'BOX_TAXES_LOCAL_SALES_TAXES' => $local_sales_tax_name,

    // Installation Scripts
    'LOCAL_SALES_TAXES_INSTALL_SUCCESS' => $local_sales_tax_name . ' installation completed!',
    'LOCAL_SALES_TAXES_INSTALL_ERROR' => $local_sales_tax_name . ' installation failed!',
    'LOCAL_SALES_TAXES_UNINSTALL_SUCCESS' => $local_sales_tax_name . ' removal completed!',
    'LOCAL_SALES_TAXES_UNINSTALL_ERROR' => $local_sales_tax_name . ' removal failed!',
    'LOCAL_SALES_TAXES_INSTALL_ERROR_AUTOLOAD' => 'The auto-loader file \'%s\' has not been deleted. For this module to work you must delete this file manually. Before you post on the Zen Cart forum to ask, YES you are REALLY supposed to follow these instructions',
    'LOCAL_SALES_TAXES_INSTALL_ERROR_FILE_NOT_FOUND' => 'Filesystem Error: Unable to access \'%s\'. Please make sure you uploaded the file and your webserver has access to read the file!',
    'LOCAL_SALES_TAXES_INSTALL_ERROR_FILE_FOUND' => 'The file \'%s\' has not been deleted. For this module to work you must delete this file manually. Before you post on the Zen Cart forum to ask, YES you are REALLY supposed to follow these instructions',
    'LOCAL_SALES_TAXES_INSTALL_ERROR_SORT_ORDER' => 'Database Error: Unable to access sort_order in table \'%s\'!',
    'LOCAL_SALES_TAXES_UNINSTALL_ERROR_DELETE' => 'Database Error: Unable to delete \'%s\' in table \'%s\'!',
    'LOCAL_SALES_TAXES_UNINSTALL_ERROR_TABLE' => 'Database Error: Unable to remove table \'%s\'!',
    'LOCAL_SALES_TAXES_UNINSTALL_ERROR_ADMIN_PAGES' => 'Unable to remove the registration of admin pages',
    'LOCAL_SALES_TAXES_UNINSTALL_ERROR_FILE_FOUND' => 'The file \'%s\' has not been deleted. To finish the removal you will need to manually delete the file from your server!',
    'LOCAL_SALES_TAXES_UNINSTALL_ERROR_MODULE_INSTALLED' => 'The ' . $local_sales_tax_name . ' module is still installed. You need to <a href="' . zen_href_link(FILENAME_MODULES, 'set=ordertotal&module=ot_local_sales_taxes') . '">remove the ' . $local_sales_tax_name . ' order total module</a> before uninstalling. This will remove all associated data!',
];
