<?php
if (!defined('IS_ADMIN_FLAG')) {
  die('Illegal Access');
}

$autoLoadConfig[999][] = array(
  'autoType' => 'init_script',
  'loadFile' => 'init_local_sales_taxes_install.php'
);