<?php
/**
 *  ot_local_sales_tax module
 *
 *   By Heather Gardner AKA: LadyHLG
 *   The module should apply tax based on the field you
 *   choose options are Zip Code, City, and Suburb.
 *   It should also compound the tax to whatever zone
 *   taxes you already have set up.  Which means you
 *   can apply multiple taxes to any zone based on
 *   different criteria.
 *  ot_local_sales_taxes.php  version 2.5.3
 */

class ot_local_sales_taxes
{

   var $title, $output;

//
   function __construct()
   {
      $this->code = 'ot_local_sales_taxes';
      $this->title = MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_TITLE;
      $this->description = MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_DESCRIPTION;

      $this->sort_order = defined('MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_SORT_ORDER') ? MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_SORT_ORDER : null;
      if (null === $this->sort_order) return false;
      $this->store_tax_basis = MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STORE_TAX_BASIS;
      $this->mod_debug = MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_DEBUG;
      $this->output = array();
   }

//Primary function that updates tax display
   function process()
   {

      global $order;
      global $currencies;
      global $db;
      global $debug;

      if (!empty($_SESSION['payment']) && $_SESSION['payment']  === 'quest') return []; 

      //print_r($order);

      if ($this->mod_debug == 'true') {
         //print_r($order);
         $debug = true;
      } else {
         $debug = false;
      }

      //find out the store tax method - checking for store pick up too
      $customer_ship_method = $order->info['shipping_module_code'];
      $ot_local_sales_taxes_basis = get_store_tax_basis($customer_ship_method);

      if ($debug) {
         echo 'Taxing based on: ' . $ot_local_sales_taxes_basis . '<br />';
      }

      //are we taxing based on billing address or shipping address zone id?
      //blank shipping means probably download default to billing
      $ot_local_sales_taxes_zoneid = get_local_taxable_zoneid($ot_local_sales_taxes_basis);

      if ($debug) {
         echo 'Taxing For Zone: ' . $ot_local_sales_taxes_zoneid . '<br />';
      }

      //echo $ot_local_sales_taxes_zoneid;
      if ($debug) {
         echo 'Local Store Tax For: ' . $this->store_tax_basis . '<br /><br />';
      }

      //go get all local taxes for the local taxable zone if none return 0
      $local_taxes = get_local_zone_taxes($ot_local_sales_taxes_zoneid, $ot_local_sales_taxes_basis, $this->store_tax_basis);

      //start of if local tax <> 0
      if ($local_taxes <> 0) {

         //list of data to search for should be in semicolon delimeted list
         //can be single entry -ie 53545
         //can be mulitple single entries - ie 53545;53711;54302
         //can be ranges - ie 53545-to-53571
         //treat all as arrays and split

         //print_r($local_taxes);

         //for each local zone tax loop through to see if it applies to this order and if it applies to any of the products
         //start foreach tax loop
         foreach ($local_taxes as $taxrec) {

            //print_r($local_taxes);
            //check to see if this tax applies to this order
            $apply_local_tax = check_for_datamatch($taxrec['order_data'], $taxrec['matching_data']);
            // Orders always taxable

            //if tax applies then get total for this tax class
            if ($apply_local_tax) {

               $tax_total_for_class = '';
               //$rec_tax_class, $rec_tax_name, $tax
               $tax_total_for_class = get_tax_total_for_class($taxrec['tax_class'], $taxrec['id'], $taxrec['tax']);
               //echo tax class total;

               //start of if tax total > 0
               if ($tax_total_for_class > 0) {

                  if ($debug) {
                     echo 'Tax for Class id ' . $taxrec['tax_class'] . ': ' . $tax_total_for_class . '<br /><br />';
                  }

                  //add total tax to order info array
                  $order->info['tax'] += $tax_total_for_class; 
                  $order->info['total'] += $tax_total_for_class;
                  if (!isset($order->info['local_tax'])) $order->info['local_tax'] = 0;
                  $order->info['local_tax'] += $tax_total_for_class; 

                  //add tax info to order info tax groups
                  if (isset($order->info['tax_groups'][$taxrec['id']])) {
                     $order->info['tax_groups'][$taxrec['id']] += $tax_total_for_class;
                  } else {
                     $order->info['tax_groups'][$taxrec['id']] = $tax_total_for_class;
                  }//end if is set tax groups

                  //update order info totals
                  $apply_tax_to_shipping = check_tax_on_shipping($taxrec['tax_shipping']);

                  if ($apply_tax_to_shipping) {
                     $shipping_tax = zen_calculate_tax($order->info['shipping_cost'], $taxrec['tax']);
                     $order->info['shipping_tax'] += $shipping_tax;
                     $order->info['total'] += $shipping_tax;
                     $order->info['tax_groups'][$taxrec['id']] += $shipping_tax;
                     $_SESSION['shipping_tax_amount'] += $shipping_tax;
                     if (!isset($order->info['shipping_local_tax'])) $order->info['shipping_local_tax'] = 0;
                     $order->info['shipping_local_tax'] += $shipping_tax;

                     $tax_total_for_class += $shipping_tax;
                  }// end if apply to shipping

                  $showtax = $currencies->format($tax_total_for_class, true, $order->info['currency'], $order->info['currency_value']);

                  $this->output[] = array('title' => $taxrec['id'] . ':',
                     'text' => $showtax,
                     'value' => $tax_total_for_class);

               }//end if tax total > 0
            }//end if apply local tax
         }//end foreach tax loop
      }//end if local tax <> 0
   }//end function

//
   function check()
   {

      global $db;

      if (!isset($this->_check)) {
         $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STATUS'");
         $this->_check = $check_query->RecordCount();
      }
      return $this->_check;
   }

//keys for the removal function remove()
   function keys()
   {
      return array('MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STATUS', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_SORT_ORDER', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STORE_TAX_BASIS', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_DEBUG');
   }

//adds mod to admin
   function install()
   {

      global $db;

      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('This module is installed', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STATUS', 'true', '', '6', '1','zen_cfg_select_option(array(\'true\'), ', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_SORT_ORDER', '301', 'Sort order of display.', '6', '2', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Store Pickup Tax Basis', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_STORE_TAX_BASIS', '', 'Should be a zip code, city name or suburb entry. This should match to at least one of the local tax records.', '6', '3', now())");
      $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Debugging is active', 'MODULE_ORDER_TOTAL_COUNTY_LOCAL_TAX_DEBUG', 'false', 'Turn Debugging on or off.', '6', '6','zen_cfg_select_option(array(\'false\', \'true\'), ', now())");
   }

//uninstalls the mod from the admin - does not remove the sales tax tables
   function remove()
   {
      global $db;
      $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
   }

}//close class
