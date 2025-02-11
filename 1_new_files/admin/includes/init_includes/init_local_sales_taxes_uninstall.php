<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// Define the version.
$version = '3.0.0';

// Make sure the order total module has been uninstalled.
// Stop immediately if it has not been uninstalled.
if (defined(MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STATUS)) {
    $messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_ERROR_MODULE_INSTALLED, 'error');
    return;
}

$failed = false;
$db->Execute('DROP TABLE IF EXISTS ' . TABLE_LOCAL_SALES_TAXES);
if ($sniffer->table_exists(TABLE_LOCAL_SALES_TAXES)) {
    $messageStack->add(sprintf(LOCAL_SALES_TAXES_UNINSTALL_ERROR_TABLE, TABLE_LOCAL_SALES_TAXES), 'error');
    $failed = true;
}

if (zen_page_key_exists('localSalesTaxes')) {
    zen_deregister_admin_pages('localSalesTaxes');
}

// Attempt to remove files
$files = [
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'classes/observers/auto.local_sales_tax_admin.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_local_sales_taxes_install.php',
    DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/lang.local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/extra_definitions/lang.local_sales_taxes.php',
    DIR_FS_ADMIN . FILENAME_LOCAL_SALES_TAXES . '.php',

    DIR_FS_CATALOG . DIR_WS_INCLUDES . 'classes/observers/auto.local_sales_tax.php',
    DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ot_local_sales_taxes.php',
    DIR_FS_CATALOG . DIR_WS_LANGUAGES . 'english/modules/order_total/lang.ot_local_sales_taxes.php',
    DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/ot_local_sales_taxes.php',

    // Files from older versions
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'boxes/extra_boxes/local_sales_tax_taxes_dhtml.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes_filenames.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes_database_tables.php',
    DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/extra_definitions/local_sales_taxes.php',
    DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ot_local_sales_taxes_databse_tables.php',
    DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/functions_local_sales_taxes.php',
    DIR_FS_CATALOG . DIR_WS_FUNCTIONS . 'extra_functions/functions_local_taxes.php',
    DIR_FS_CATALOG . DIR_WS_LANGUAGES . 'english/modules/order_total/ot_local_sales_taxes.php',
];
foreach($files as $file) {
    if (!file_exists($file)) {
        continue;
    }

    @unlink($file);
    if (file_exists($file)) {
        $messageStack->add(sprintf(LOCAL_SALES_TAXES_UNINSTALL_ERROR_FILE_FOUND, $file), 'error');
        $failed = true;
    }
}

if ($failed === false) {
    $uninstall_auto_load = DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.php';
    // Disable this script from running again
    if (file_exists($install_auto_load)) {
        if (!unlink($install_auto_load)) {
            $messageStack->add(sprintf(LOCAL_SALES_TAXES_INSTALL_ERROR_AUTOLOAD, $install_auto_load), 'error');
            $failed = true;
        }
    }
}

if ($failed === true) {
    $messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_ERROR, 'error');
} else {
    $messageStack->add(LOCAL_SALES_TAXES_UNINSTALL_SUCCESS, 'success');
}
