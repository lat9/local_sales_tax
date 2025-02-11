<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

// Define the version.
$version = '3.0.0';

$failed = false;

// Check to make sure all new files have been uploaded.
// These are not intended to be perfect checks, just a quick 'hey you'.
$files = [
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'classes/observers/auto.local_sales_tax_admin.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'extra_datafiles/local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'auto_loaders/config.local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_local_sales_taxes_install.php',
    DIR_FS_ADMIN . DIR_WS_INCLUDES . 'init_includes/init_local_sales_taxes_uninstall.php',
    DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/lang.local_sales_taxes.php',
    DIR_FS_ADMIN . DIR_WS_LANGUAGES . 'english/extra_definitions/lang.local_sales_taxes.php',
    DIR_FS_ADMIN . FILENAME_LOCAL_SALES_TAXES . '.php',
    DIR_FS_CATALOG . DIR_WS_INCLUDES . 'classes/observers/auto.local_sales_tax.php',
    DIR_FS_CATALOG . DIR_WS_INCLUDES . 'extra_datafiles/ot_local_sales_taxes.php',
    DIR_FS_CATALOG . DIR_WS_LANGUAGES . 'english/modules/order_total/lang.ot_local_sales_taxes.php',
    DIR_FS_CATALOG . DIR_WS_MODULES . 'order_total/ot_local_sales_taxes.php'
];
foreach ($files as $file) {
    if (!file_exists($file)) {
        $messageStack->add(sprintf(LOCAL_SALES_TAXES_INSTALL_ERROR_FILE_NOT_FOUND, $file), 'error');
        $failed = true;
    }
}

// Attempt to remove files from older versions
$files = [
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
foreach ($files as $file) {
    if (!file_exists($file)) {
        continue;
    }

    @unlink($file);
    if (file_exists($file)) {
        $messageStack->add(sprintf(LOCAL_SALES_TAXES_INSTALL_ERROR_FILE_FOUND, $file), 'error');
        $failed = true;
    }
}

// Now check the required tables if not already present
if ($failed === false && !$sniffer->table_exists(TABLE_LOCAL_SALES_TAXES)) {
    $db->Execute(
        'CREATE TABLE IF NOT EXISTS `' . TABLE_LOCAL_SALES_TAXES . '` ( ' .
            '`local_tax_id` INT(11) NOT NULL AUTO_INCREMENT COMMENT \'tax id\', ' .
            '`zone_id` INT(11) DEFAULT NULL COMMENT \'zen cart zone to apply tax\', ' .
            '`local_fieldmatch` VARCHAR(100) DEFAULT NULL COMMENT \'name of field from delivery table to match\', ' .
            '`local_datamatch` TEXT COMMENT \'Data to match delievery field\', ' .
            '`local_tax_rate` DECIMAL(7,4) DEFAULT \'0.0000\' COMMENT \'local tax rate\', ' .
            '`local_tax_label` VARCHAR(100) DEFAULT NULL COMMENT \'Label for checkout\', ' .
            '`local_tax_shipping` VARCHAR(5) DEFAULT \'false\' COMMENT \'Apply this tax to shipping\', ' .
            '`local_tax_class_id` INT(1) DEFAULT NULL COMMENT \'Apply to products in what tax class\', ' .
            'PRIMARY KEY  (`local_tax_id`) ' .
        ') ENGINE=MyISAM'
    );
}

if (!zen_page_key_exists('localSalesTaxes')) {
    zen_register_admin_page('localSalesTaxes', 'BOX_TAXES_LOCAL_SALES_TAXES', 'FILENAME_LOCAL_SALES_TAXES', '', 'taxes', 'Y');
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
    $messageStack->add(LOCAL_SALES_TAXES_INSTALL_ERROR, 'error');
} else {
    $messageStack->add(LOCAL_SALES_TAXES_INSTALL_SUCCESS, 'success');
}
