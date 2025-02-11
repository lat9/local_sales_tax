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
 *  functions_local_taxes.php  version 2.5.3
 */

//if not store pickup then set tax basis to cart defult
function get_store_tax_basis($customer_shipping_method)
{

   if ($customer_shipping_method == 'storepickup_storepickup') {
      return 'Store Pickup';
   } else {
      return STORE_PRODUCT_TAX_BASIS;
   }
}

//getting the taxable zone id from either billing or shipping address
//store pickup defaults to store zone
function get_local_taxable_zoneid($store_tax_basis)
{

   global $order;

   switch ($store_tax_basis) {
      case 'Shipping':
         if (empty($order->delivery['zone_id'])) {
            $taxable_zoneid = $order->billing['zone_id'];
         } else {
            $taxable_zoneid = $order->delivery['zone_id'];
         }
         break;

      case 'Billing':
         $taxable_zoneid = $order->billing['zone_id'];
         break;

      case 'Store':
         if ($billing_address->fields['entry_zone_id'] == STORE_ZONE) {
            $taxable_zoneid = $order->billing['zone_id'];
         } else {
            $taxable_zoneid = $order->delivery['zone_id'];
         }
         break;

      case 'Store Pickup':
         $taxable_zoneid = STORE_ZONE;
         break;
   }
   return $taxable_zoneid;
}

//
function get_local_data_field($store_tax_basis, $order_match)
{

   global $order;

   switch ($store_tax_basis) {
      case 'Shipping':
         if (empty($order->delivery[$order_match])) {
            $myfield = $order->billing[$order_match];
         } else {
            $myfield = $order->delivery[$order_match];
         }
         break;

      case 'Billing':
         $myfield = $order->billing[$order_match];
         break;

      case 'Store':
         if ($billing_address->fields[$order_match] == STORE_ZONE) {
            $myfield = $order->billing[$order_match];
         } else {
            $myfield = $order->delivery[$order_match];
         }
         break;

      case 'Store Pickup':
         $myfield = $this->$store_tax_basis;
         break;
   }
   return $myfield;
}

//
function get_local_zone_taxes($local_sales_taxes_zoneid, $ot_local_sales_taxes_basis, $store_tax_basis)
{

   global $db;
   global $debug;

   $taxarray = [];
   $taxsql = "select local_tax_id, zone_id, local_fieldmatch,
			    local_datamatch, local_tax_rate, local_tax_label,
				local_tax_shipping, local_tax_class_id
				from " . TABLE_LOCAL_SALES_TAXES . " where zone_id =  '" . $local_sales_taxes_zoneid . "'";

   //get tax rates for field lookup
   $local_taxes = $db->Execute($taxsql);

   if ($local_taxes->RecordCount() > 0) {//Check to see if it was null and not found.
      //echo $local_taxes->RecordCount();

      while (!$local_taxes->EOF) {
         //echo $local_taxes->fields['local_tax_id'].'<br />';
         $orderdata = get_order_data($ot_local_sales_taxes_basis, $local_taxes->fields['local_fieldmatch'], $store_tax_basis);
         $taxarray[$local_taxes->fields['local_tax_id']] =
            array('id' => $local_taxes->fields['local_tax_label'],
               'tax' => $local_taxes->fields['local_tax_rate'],
               'match_field' => $local_taxes->fields['local_fieldmatch'],
               'matching_data' => $local_taxes->fields['local_datamatch'],
               'tax_shipping' => $local_taxes->fields['local_tax_shipping'],
               'tax_class' => $local_taxes->fields['local_tax_class_id'],
               'order_data' => $orderdata);
         $local_taxes->MoveNext();
      }
   }
   //print_r($taxarray);
   return $taxarray;
}

//
function get_order_data($ot_local_sales_taxes_basis, $taxmatch, $store_tax_basis)
{

   global $order;
   //echo $ot_local_sales_taxes_basis;
   //echo $store_tax_basis;

   switch ($ot_local_sales_taxes_basis) {
      case 'Shipping':
         if (empty($order->delivery[$taxmatch])) {
            $orderdata = $order->billing[$taxmatch];
         } else {
            $orderdata = $order->delivery[$taxmatch];
         }
         break;

      case 'Billing':
         $orderdata = $order->billing[$taxmatch];
         break;

      case 'Store':
         if ($billing_address->fields[$taxmatch] == STORE_ZONE) {
            $orderdata = $order->billing[$taxmatch];
         } else {
            $orderdata = $order->delivery[$taxmatch];
         }
         break;

      case 'Store Pickup':
         $orderdata = $store_tax_basis;
         break;
   }
   return $orderdata;
}

//Primary test for customer location (normally zip code) to determine is tax should be applied
function check_for_datamatch($order_data, $local_data_list)
{

   $taxapplies = false;

   //Remove the - and plus 4 if the customer entered it with the zip code
   if (strstr($order_data, '-')) {//test first if the - is present, if not there is no plus 4
      $tmpOD = trim($order_data);//remove spaces fromn start and trailing
      $tmpOD = explode('-', $tmpOD);//explode to remove -plus4
      if (is_numeric($tmpOD[0])) {//ensure result is numeric (assummed to be zip code)
         $order_data = $tmpOD[0];//assign result
      }
   }

   $listarray = explode(";", $local_data_list);

   //loop through the array to check each item is it a range or single zip
   //ranges are usually used with postcodes
   $order_data = strtolower($order_data); 
   foreach ($listarray as $value) {
      $value = strtolower(trim($value));

      //this array item is a range
      if (strstr($value, "-to-")) {
         //split the range to see if zip falls within
         $rangearray = explode("-to-", $value);
         $lowerrange = trim($rangearray[0]);
         $upperrange = trim($rangearray[1]);

         if ($order_data >= $lowerrange && $order_data <= $upperrange) {
            $taxapplies = true;
            //stop here we have a match
            break;
         }
      }//this array item is a single zip / city, etc. 
      else {
         if (($order_data === $value)) {
            $taxapplies = true;
            //stop here we have a match
            break;
         }
      }
   }
   return $taxapplies;
}

//Check if shipping is taxed 
function check_tax_on_shipping($taxshipping)
{

   global $debug;

   //do we tax shipping 
   if ($taxshipping == "true") {
      if ($debug) {
         echo 'Apply Local Tax To Shipping <br /><br />';
      }
      return true;
   } else {
      if ($debug) {
         echo 'Do Not Apply Local Tax To Shipping <br /><br />';
      }
      return false;
   }
}

//
function get_product_tax_class($productid, $tax_class)
{

   global $db;
   global $debug;

   $productinfo = $db->Execute("select products_tax_class_id, products_model from " . TABLE_PRODUCTS . " where products_id = '" . $productid . "'");

   if ($productinfo->RecordCount() > 0) {//Check to see if it was null and not found.

      $ptc = $productinfo->fields['products_tax_class_id'];

      if ($debug) {
         echo $productinfo->fields['products_model'] . " - product tax class " . $ptc . ' - ';
      }

      if ($ptc == $tax_class) {
         if ($debug) {
            echo "tax class match" . '<br />';
         }
         return true;
      } else {
         if ($debug) {
            echo "not a tax class match" . '<br />';
         }
         return false;
      }
   } else {
      if ($debug) {
         echo "Item not found" . '<br /><br />';
      }
      return false;
   }
}

//
function get_tax_total_for_class($rec_tax_class, $rec_tax_name, $rec_tax)
{

   global $order;
   global $debug;

   foreach ($order->products as $key => $product) {
      $prodid = $product['id'];
      $prod_in_tax_class = get_product_tax_class($prodid, $rec_tax_class);

      if ($prod_in_tax_class) {

         //tax description
         $producttaxDescription = $rec_tax_name . ' ' . $rec_tax;

         //get product price
         $product_tax = 0;

         //$product_price = ($product['final_price'] * $product['qty']) + ($product['onetime_charges']);
         $product_tax = (zen_calculate_tax($product['final_price'] * $product['qty'], $rec_tax)) + zen_calculate_tax($product['onetime_charges'], $rec_tax);

         if ($debug) {
            echo 'Product Tax:' . $product_tax . '<br /><br />';
         }

         //add tax group to order product tax_group array
         if (!isset($order->products[$key]['tax_groups'][$producttaxDescription])) {
            $order->products[$key]['tax_groups'][$producttaxDescription] = $rec_tax;
         }

         //update order product tax info
         $order->products[$key]['tax'] += $rec_tax;
         $order->products[$key]['tax_description'] .= ' + ' . $producttaxDescription;

         //sum tax for all products in this class
         $tax_class_total += $product_tax;
      }
   }
   return $tax_class_total;
}

