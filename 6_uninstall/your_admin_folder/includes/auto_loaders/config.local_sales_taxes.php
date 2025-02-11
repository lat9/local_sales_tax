<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
}

$autoLoadConfig[999][] = [
  'autoType' => 'init_script',
  'loadFile' => 'init_local_sales_taxes_uninstall.php'
];
