<?php
/**
 *  ot_local_sales_tax module
 *
 *   By Heather Gardner AKA: LadyHLG
 *   The module should apply tax based on the field you
 * choose options are Zip Code, City, and Suburb.
 * It should also compound the tax to whatever zone
 * taxes you already have set up.  Which means you
 * can apply multiple taxes to any zone based on
 * different criteria.
 */
define('TABLE_LOCAL_SALES_TAXES', DB_PREFIX . 'tax_rates_local');
define('FILENAME_LOCAL_SALES_TAXES', 'local_sales_taxes');
