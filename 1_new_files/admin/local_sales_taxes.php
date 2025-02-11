<?php
/**
 *  ot_local_sales_tax module
 *
 *   By Heather Gardner AKA: LadyHLG
 *   The module should apply tax based on the field you
 *	choose options are Zip Code, City, and Suburb.
 *	It should also compound the tax to whatever zone
 *	taxes you already have set up.  Which means you
 *	can apply multiple taxes to any zone based on
 *	different criteria.
 *  local_sales_taxes.php  version 2.5.3
 */

require('includes/application_top.php');
if (!defined('MAX_DISPLAY_TAX_RESULTS')) define('MAX_DISPLAY_TAX_RESULTS','1000');

//input cleanup and validation to protect against malicious input
//doubleval() test ONLY numbers are passes
if( ($_GET['page']) ){
	$_GET['page'] = doubleval(trim($_GET['page']));
}
//doubleval() test ONLY numbers are passed
if( ($_GET['stID'] ) ){
	$_GET['stID'] = doubleval(trim($_GET['stID']));
}
//addslashes() with text input
if( $_GET['action'] ){
	$_GET['action'] = addslashes(trim($_GET['action']));
}
else{
	$_GET['action'] = NULL;
}

//create array that can be used for Zip, City, or Suburb, depending on what is selected in the 'Local Sales Taxes' plug-in
$za_lookup = array(array('id' => 'postcode', 'text' => 'Zip Code'),
                   array('id' => 'city', 'text' => 'City'),
                   array('id' => 'suburb', 'text' => 'Suburb'));

$action = (isset($_GET['action']) ? $_GET['action'] : '');


//insert, save or deleteconfirm
if( zen_not_null($action) ){

	switch ($action){

    	case 'insert':
        	$local_zone_id = zen_db_prepare_input($_POST['zone_id']);
        	$local_tax_fieldmatch = zen_db_prepare_input($_POST['tax_fieldmatch']);
			$local_tax_datamatch = zen_db_prepare_input($_POST['tax_datamatch']);
			$local_tax_rate = zen_db_prepare_input((float)$_POST['tax_rate']);
			$local_tax_description = zen_db_prepare_input($_POST['tax_description']);
			$local_tax_shipping = zen_db_prepare_input($_POST['tax_shipping']);
			$local_tax_class_id = zen_db_prepare_input($_POST['tax_class_id']);

        	$db->Execute("insert into " . TABLE_LOCAL_SALES_TAXES . "
            	        (zone_id, local_fieldmatch, local_datamatch, local_tax_rate, local_tax_label, local_tax_shipping, local_tax_class_id)
                    	values(
        						'" . (int)$local_zone_id . "',
                            	'" . zen_db_input($local_tax_fieldmatch) . "',
								'" . zen_db_input($local_tax_datamatch) . "',
								'" . zen_db_input($local_tax_rate) . "',
                            	'" . zen_db_input($local_tax_description) . "',
								'" . zen_db_input($local_tax_shipping) . "',
								'" . zen_db_input($local_tax_class_id) . "')");

        	zen_redirect(zen_href_link(FILENAME_LOCAL_SALES_TAXES));
        	break;

		case 'save':
			$local_tax_id = zen_db_prepare_input($_GET['stID']);
			$local_zone_id = zen_db_prepare_input($_POST['zone_id']);
        	$local_tax_fieldmatch = zen_db_prepare_input($_POST['tax_fieldmatch']);
			$local_tax_datamatch = zen_db_prepare_input($_POST['tax_datamatch']);
			$local_tax_rate = zen_db_prepare_input((float)$_POST['tax_rate']);
			$local_tax_description = zen_db_prepare_input($_POST['tax_description']);
			$local_tax_shipping = zen_db_prepare_input($_POST['tax_shipping']);
			$local_tax_class_id = zen_db_prepare_input($_POST['tax_class_id']);

        	$db->Execute("update " . TABLE_LOCAL_SALES_TAXES . "
                      		set zone_id = '" . (int)$local_zone_id . "',
                          	local_fieldmatch = '" . zen_db_input($local_tax_fieldmatch) . "',
                          	local_datamatch = '" . zen_db_input($local_tax_datamatch) . "',
							local_tax_rate = '" . zen_db_input($local_tax_rate) . "',
							local_tax_label = '" . zen_db_input($local_tax_description) . "',
							local_tax_shipping = '" . zen_db_input($local_tax_shipping) . "',
							local_tax_class_id = '" . zen_db_input($local_tax_class_id) . "'
                      	where local_tax_id = '" . (int)$local_tax_id . "'");

        	zen_redirect(zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $local_tax_id));
        	break;

		case 'deleteconfirm':
        	$local_tax_id = zen_db_prepare_input($_GET['stID']);

        	$db->Execute("delete from " . TABLE_LOCAL_SALES_TAXES . " where local_tax_id = '" . (int)$local_tax_id . "'");

        	zen_redirect(zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page']));
        	break;
	}
}

//The following sets up the screen display and FORM field actions
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>

<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">

<title><?php echo TITLE; ?></title>

<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">

<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
	<!--
  	function init(){
		cssjsmenu('navbar');
    	if (document.getElementById){
			var kill = document.getElementById('hoverJS');
      		kill.disabled = true;
    	}
  	}

	function update_zone(theForm){
  		var NumState = theForm.zone_id.options.length;
  		var SelectedCountry = "";

		while( NumState > 0 ){
	    	NumState--;
	  		theForm.zone_id.options[NumState] = null;
	  	}
		SelectedCountry = theForm.zone_country_id.options[theForm.zone_country_id.selectedIndex].value;
		<?php echo zen_js_zone_list('SelectedCountry', 'theForm', 'zone_id'); ?>
	}
  	// -->
</script>

</head>

<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
    <!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                <td class="pageHeading" align="right"><?php echo zen_draw_separator('pixel_trans.gif', HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
              </tr>
            </table></td>
        </tr>
        <tr>
          <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr class="dataTableHeadingRow">
						<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_ID; ?></td>
                      	<td class="dataTableHeadingContent"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_ZONE; ?></td>
                      	<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_FIELD; ?></td>
                      	<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_DATA; ?></td>
						<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_RATE; ?></td>
						<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_LABEL; ?></td>
						<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_SHIPPING; ?></td>
						<td class="dataTableHeadingContent" align="center"><?php echo TABLE_HEADING_LOCAL_SALES_TAX_CLASS; ?></td>
                      	<td class="dataTableHeadingContent" align="right"><?php echo TABLE_HEADING_ACTION; ?>&nbsp;</td>
                    </tr>
<?php

	$localtax_query_raw = "SELECT st.local_tax_id, st.zone_id, st.local_fieldmatch, st.local_datamatch, st.local_tax_rate, st.local_tax_label, st.local_tax_shipping, st.local_tax_class_id, tc.tax_class_title, z.zone_name, z.zone_country_id FROM (" . TABLE_LOCAL_SALES_TAXES . " st LEFT JOIN " . TABLE_TAX_CLASS . " tc ON st.local_tax_class_id = tc.tax_class_id) LEFT JOIN " . TABLE_ZONES . " z ON st.zone_id = z.zone_id ORDER BY st.local_datamatch";
	$localtax_split = new splitPageResults($_GET['page'], MAX_DISPLAY_TAX_RESULTS, $localtax_query_raw, $localtax_query_numrows);
  	$localtax = $db->Execute($localtax_query_raw);

  while (!$localtax->EOF) {

    if ( (!isset($_GET['stID']) || (isset($_GET['stID']) && ($_GET['stID'] == $localtax->fields['local_tax_id']))) && !isset($stInfo) && (substr($action, 0, 3) != 'new') ){
      	$stInfo = new objectInfo($localtax->fields);
    }

    if( isset($stInfo) && is_object($stInfo) && ($localtax->fields['local_tax_id'] == $stInfo->local_tax_id) ){
    	//This is changing the screen display to an edit view but not pushing anything to the database.
    	echo '<tr id="defaultSelected" class="dataTableRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id . '&action=edit') . '\'">' . "\n";
    }
    else{
    	echo '<tr class="dataTableRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="document.location.href=\'' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $localtax->fields['local_tax_id']) . '\'">' . "\n";
    }

?>
	<td class="dataTableContent"><?php echo $localtax->fields['local_tax_id']; ?></td>
    <td class="dataTableContent"><?php echo $localtax->fields['zone_name']; ?></td>
    <td class="dataTableContent" align="center"><?php echo $localtax->fields['local_fieldmatch']; ?></td>
	<td class="dataTableContent" align="center"><?php echo wordwrap($localtax->fields['local_datamatch'], 90, "\n", true); ?></td>
	<td class="dataTableContent" align="center"><?php echo $localtax->fields['local_tax_rate']; ?></td>
	<td class="dataTableContent" align="center"><?php echo $localtax->fields['local_tax_label']; ?></td>
	<td class="dataTableContent" align="center"><?php echo $localtax->fields['local_tax_shipping']; ?></td>
	<td class="dataTableContent" align="center"><?php echo $localtax->fields['tax_class_title']; ?></td>

    <td class="dataTableContent" align="right">

    <?php if( isset($stInfo) && is_object($stInfo) && ($localtax->fields['local_tax_id'] == $stInfo->local_tax_id) ){
    		echo zen_image(DIR_WS_IMAGES . 'icon_arrow_right.gif', '');
    	  }
    	  else{
    	  	echo '<a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $localtax->fields['local_tax_id']) . '">' . zen_image(DIR_WS_IMAGES . 'icon_info.gif', IMAGE_ICON_INFO) . '</a>';
		  }
    ?>

    &nbsp;</td></tr>

<?php
    $localtax->MoveNext();
  }
?>
                    <tr>
                      <td colspan="6"><table border="0" width="100%" cellspacing="0" cellpadding="2">
                          <tr>
                            <td class="smallText" valign="top"><?php echo $localtax_split->display_count($localtax_query_numrows, MAX_DISPLAY_TAX_RESULTS, $_GET['page'], TEXT_DISPLAY_NUMBER_OF_LOCAL_ST); ?></td>
                            <td class="smallText" align="right"><?php echo $localtax_split->display_links($localtax_query_numrows, MAX_DISPLAY_TAX_RESULTS, MAX_DISPLAY_PAGE_LINKS, $_GET['page']); ?></td>
						</tr>
<?php
  if (empty($action)) {
?>
                          <tr>
                            <td colspan="2" align="right"><?php echo '<a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&action=new') . '">' . zen_image_button('button_new_tax_rate.gif', IMAGE_NEW_TAX_RATE) . '</a>'; ?></td>
                          </tr>
<?php
  }
?>
                        </table></td>
                    </tr>
                  </table></td>
<?php
  $heading = array();
  $contents = array();

  switch ($action) {

    case 'new':

      	$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_NEW_LOCAL_SALES_TAX . '</b>');

      	$contents = array('form' => zen_draw_form('local_sales_tax', FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&action=insert'));
		$contents[] = array('text' => TEXT_INFO_INSERT_INTRO);
		$contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY . '<br>' . zen_draw_pull_down_menu('zone_country_id', zen_get_countries(TEXT_ALL_COUNTRIES), '', 'onChange="update_zone(this.form);"'));
      	$contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_ZONE . '<br>' . zen_draw_pull_down_menu('zone_id', zen_prepare_country_zones_pull_down()));
		$contents[] = array('text' => '<br>' . TEXT_INFO_TAX_RATE . '<br>' . zen_draw_input_field('tax_rate'));
		$contents[] = array('text' => '<br>' . TEXT_INFO_FIELDMATCH . '<br>' . zen_draw_pull_down_menu('tax_fieldmatch', $za_lookup));
		$contents[] = array('text' => '<br>' . TEXT_INFO_DATAMATCH . '<br>' . zen_draw_textarea_field('tax_datamatch', false, 35, 4));
      	$contents[] = array('text' => '<br>' . TEXT_INFO_RATE_DESCRIPTION . '<br>' . zen_draw_input_field('tax_description'));
		$contents[] = array('text' => '<br />' . TEXT_INFO_TAX_SHIPPING . '<br />' . zen_draw_radio_field('tax_shipping', 'false', true) . ' ' . TEXT_TAX_SHIPPING_FALSE . '<br />' . zen_draw_radio_field('tax_shipping', 'true') . ' ' . TEXT_TAX_SHIPPING_TRUE);
		$contents[] = array('text' => '<br>' . TEXT_INFO_TAX_CLASS_TITLE . '<br>' . zen_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"'));
		$contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_insert.gif', IMAGE_INSERT) . '&nbsp;<a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page']) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      	break;

    case 'edit':

    	$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_EDIT_LOCAL_SALES_TAX . '</b>');

		$contents = array('form' => zen_draw_form('local_sales_tax', FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id . '&action=save'));
		$contents[] = array('text' => TEXT_INFO_EDIT_INTRO);
      	$contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY . '<br>' . zen_draw_pull_down_menu('zone_country_id', zen_get_countries(TEXT_ALL_COUNTRIES), $stInfo->zone_country_id, 'onChange="update_zone(this.form);"'));
      	$contents[] = array('text' => '<br>' . TEXT_INFO_COUNTRY_ZONE . '<br>' . zen_draw_pull_down_menu('zone_id', zen_prepare_country_zones_pull_down($stInfo->zone_country_id), $stInfo->zone_id));
      	$contents[] = array('text' => '<br>' . TEXT_INFO_TAX_RATE . '<br>' . zen_draw_input_field('tax_rate', $stInfo->local_tax_rate));
		$contents[] = array('text' => '<br>' . TEXT_INFO_FIELDMATCH . '<br>' . zen_draw_pull_down_menu('tax_fieldmatch', $za_lookup, $stInfo->local_fieldmatch));
		$contents[] = array('text' => '<br>' . TEXT_INFO_DATAMATCH . '<br>' . zen_draw_textarea_field('tax_datamatch', false, 35, 4, $stInfo->local_datamatch));
      	$contents[] = array('text' => '<br>' . TEXT_INFO_RATE_DESCRIPTION . '<br>' . zen_draw_input_field('tax_description', $stInfo->local_tax_label));

	 	switch ($stInfo->local_tax_shipping) {
	 		case 'false': $on_status = false; $off_status = true; break;
        	case 'true': $on_status = true; $off_status = false; break;
        	default:  $on_status = false; $off_status = true; break;
      	}

		$contents[] = array('text' => '<br />' . TEXT_INFO_TAX_SHIPPING . '<br />' . zen_draw_radio_field('tax_shipping', 'false', $off_status) . ' ' . TEXT_TAX_SHIPPING_FALSE . '<br />' . zen_draw_radio_field('tax_shipping', 'true', $on_status) . ' ' . TEXT_TAX_SHIPPING_TRUE);
		$contents[] = array('text' => '<br>' . TEXT_INFO_TAX_CLASS_TITLE . '<br>' . zen_tax_classes_pull_down('name="tax_class_id" style="font-size:10px"', $stInfo->local_tax_class_id));
		$contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_update.gif', IMAGE_UPDATE) . '&nbsp;<a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      	break;

    case 'delete':

      	$heading[] = array('text' => '<b>' . TEXT_INFO_HEADING_DELETE_LOCAL_SALES_TAX . '</b>');

      	$contents = array('form' => zen_draw_form('local_sales_tax', FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id . '&action=deleteconfirm'));
      	$contents[] = array('text' => TEXT_INFO_DELETE_INTRO);
      	$contents[] = array('text' => '<br><b>' . $stInfo->local_tax_label . ' - ' . $stInfo->local_tax_rate . '%</b>');
      	$contents[] = array('align' => 'center', 'text' => '<br>' . zen_image_submit('button_delete.gif', IMAGE_DELETE) . '&nbsp;<a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id) . '">' . zen_image_button('button_cancel.gif', IMAGE_CANCEL) . '</a>');
      	break;

    default:

      if (isset($stInfo) && is_object($stInfo)) {
        $heading[] = array('text' => '<b>' . $stInfo->local_tax_label . '</b>');

        $contents[] = array('align' => 'center', 'text' => '<a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id . '&action=edit') . '">' . zen_image_button('button_edit.gif', IMAGE_EDIT) . '</a> <a href="' . zen_href_link(FILENAME_LOCAL_SALES_TAXES, 'page=' . $_GET['page'] . '&stID=' . $stInfo->local_tax_id . '&action=delete') . '">' . zen_image_button('button_delete.gif', IMAGE_DELETE) . '</a>');
        $contents[] = array('text' => '<br>' . TEXT_INFO_DESCRIPTION . '<br>' . $stInfo->local_tax_label . ' - ' . $stInfo->local_tax_rate . '%');
      }
      break;

  }

  if( (zen_not_null($heading)) && (zen_not_null($contents)) ){
    echo '<td width="25%" valign="top">' . "\n";
    $box = new box;
    echo $box->infoBox($heading, $contents);
    echo '</td>' . "\n";
  }
?>
              </tr>
            </table></td>
        </tr>
      </table></td>
    <!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
