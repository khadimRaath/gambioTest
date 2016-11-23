<?php
/* --------------------------------------------------------------
   new_product.php 2015-09-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   based on:
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(categories.php,v 1.140 2003/03/24); www.oscommerce.com
   (c) 2003  nextcommerce (categories.php,v 1.37 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: new_product.php 897 2005-04-28 21:36:55Z mz $)

   Released under the GNU General Public License
   --------------------------------------------------------------
   Third Party contribution:
   Enable_Disable_Categories 1.3               Autor: Mikel Williams | mikel@ladykatcostumes.com
   New Attribute Manager v4b                   Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   Category Descriptions (Version: 1.5 MS2)    Original Author:   Brian Lowe <blowe@wpcusrgrp.org> | Editor: Lord Illicious <shaolin-venoms@illicious.net>
   Customers Status v3.x  (c) 2002-2003 Copyright Elari elari@free.fr | www.unlockgsm.com/dload-osc/ | CVS : http://cvs.sourceforge.net/cgi-bin/viewcvs.cgi/elari/?sortby=date#dirlist

   Released under the GNU General Public License
   --------------------------------------------------------------*/

// BOF GM_MOD:
include_once(DIR_FS_CATALOG . 'gm/inc/gm_get_url_keywords.inc.php');
require_once(DIR_FS_INC . 'get_checkout_information.inc.php');

// BEGIN Hermes
require_once DIR_FS_CATALOG .'includes/classes/hermes.php';
$hermes = new Hermes();
$pclasses = $hermes->getPackageClasses();
$hermes_options = array(
	'min_pclass' => 'XS',
);
// END Hermes

if (($_GET['pID'])) {
	$query =
		"SELECT
			p.*,
			pd.*,
			pss.*,
			pic.code_isbn,
			pic.code_upc,
			pic.code_mpn,
			pic.code_jan,
			pic.google_export_condition,
			pic.google_export_availability_id,
			pic.brand_name,
			pic.identifier_exists,
			pic.gender,
			pic.age_group,
			date_format(pic.expiration_date, '%Y-%m-%d') as expiration_date,
			date_format(p.products_date_available, '%Y-%m-%d') as products_date_available
		FROM
			".TABLE_PRODUCTS." p
		LEFT JOIN ".TABLE_PRODUCTS_DESCRIPTION." pd ON pd.products_id = p.products_id
		LEFT JOIN products_slider_set pss ON pss.products_slider_set_id = p.products_id
		LEFT JOIN products_item_codes pic ON pic.products_id = p.products_id
		WHERE
			p.products_id = '".(int) $_GET['pID']."'
			AND pd.language_id = '".$_SESSION['languages_id']."'";
	$product_query = xtc_db_query($query);

	$product = xtc_db_fetch_array($product_query);

	$pInfo = new objectInfo($product);

	// BEGIN Hermes
	$hoptions = $hermes->getProductOptions((int)$_GET['pID']);
	if($hoptions !== false) {
		$hermes_options = $hoptions;
	}
	// END Hermes

}
elseif ($_POST) {
	$pInfo = new objectInfo($_POST);
	$products_name = $_POST['products_name'];
	$products_description = $_POST['products_description'];
	$products_short_description = $_POST['products_short_description'];
	$products_keywords = $_POST['products_keywords'];
	$products_meta_title = $_POST['products_meta_title'];
	$products_meta_description = $_POST['products_meta_description'];
	$products_meta_keywords = $_POST['products_meta_keywords'];
	$products_url = $_POST['products_url'];
	$pInfo->products_startpage = $_POST['products_startpage'];
  $products_startpage_sort = $_POST['products_startpage_sort'];
	// BOF GM_MOD
	$gm_url_keywords = gm_prepare_string($_POST['gm_url_keywords']);
	// EOF GM_MOD
	// BEGIN Hermes
	$hermes_options = array(
		'min_pclass' => gm_prepare_string($_POST['hermes_minpclass']),
	);
	// END Hermes
} else {
	$pInfo = new objectInfo(array ());
}

$manufacturers_array = array (array ('id' => '', 'text' => TEXT_NONE));
$manufacturers_query = xtc_db_query("select manufacturers_id, manufacturers_name from ".TABLE_MANUFACTURERS." order by manufacturers_name");
while ($manufacturers = xtc_db_fetch_array($manufacturers_query)) {
	$manufacturers_array[] = array ('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
}

$vpe_array = array (array ('id' => '', 'text' => TEXT_NONE));
$vpe_query = xtc_db_query("select products_vpe_id, products_vpe_name from ".TABLE_PRODUCTS_VPE." WHERE language_id='".$_SESSION['languages_id']."' order by products_vpe_name");
while ($vpe = xtc_db_fetch_array($vpe_query)) {
	$vpe_array[] = array ('id' => $vpe['products_vpe_id'], 'text' => $vpe['products_vpe_name']);
}

$tax_class_array = array (array ('id' => '0', 'text' => TEXT_NONE));
$tax_class_query = xtc_db_query("select tax_class_id, tax_class_title from ".TABLE_TAX_CLASS." order by tax_class_title");
while ($tax_class = xtc_db_fetch_array($tax_class_query)) {
	$tax_class_array[] = array ('id' => $tax_class['tax_class_id'], 'text' => $tax_class['tax_class_title']);
}
$shipping_statuses = array ();
$shipping_statuses = xtc_get_shipping_status();
$languages = xtc_get_languages();


// BOF 'QUANTITY UNITS'
# $quantity_unit, $quantity_unit_select
$coo_quantity_unit       = MainFactory::create_object('QuantityUnitControl');
$quantity_unit_coo_array = $coo_quantity_unit->get_quantity_unit_array();
$quantity_unit_array     = array();
foreach ($quantity_unit_coo_array as $unit_key => $coo_unit) {
  $id   = $coo_unit->get_quantity_unit_id();
  $name = $coo_unit->get_unit_name($_SESSION['languages_id']);
  if (!empty($name)) $quantity_unit_array[] = array('id'=>$id, 'text'=>$name);
}
$basic_array             = array(array('id'=>0, 'text'=>'-'));
$quantity_unit           = array_merge($basic_array, $quantity_unit_array);
$coo_quantity_unit       = NULL;

$coo_unit_handler        = MainFactory::create_object('ProductQuantityUnitHandler');
$quantity_unit_select    = $coo_unit_handler->get_quantity_unit_id( (int) $_GET['pID'] );
$coo_unit_handler        = NULL;
// EOF 'QUANTITY UNITS'


// BOF GM_MOD
switch ($pInfo->products_status) {
	case '0' :
		$status = 0;
		break;
	case '1' :
	default :
		$status = 1;
}

$product_types_array = array();
$t_query = 'SELECT * FROM product_types AS pt LEFT JOIN product_type_descriptions AS ptd USING(product_type_id) WHERE ptd.language_id="' . $_SESSION['languages_id'] . '" ORDER BY pt.product_type_id ASC';
$product_types_result = xtc_db_query($t_query);
while($t_row = xtc_db_fetch_array($product_types_result))
{
	$product_types_array[] = array ('id' => $t_row['product_type_id'], 'text' => $t_row['name']);
}

// get the slider select html
function generateProductSliderSelect()
{
	global $product_slider_array, $pInfo;
	$pro_slider_set_id = $pInfo->slider_set_id;
	$html = '';
	$t_text_select_none = TEXT_SELECT_NONE;
	if (strpos($p_param_name, 'index') > 0) {
		$t_text_select_none = TEXT_SELECT_NONE_INDEX;
	}
	$html .= '<select name="product_slider" size="1" style="width:130px">'."";
	$html .= '<option value="0">'.$t_text_select_none.'</option>'."<br>\n";
	foreach ($product_slider_array as $f_key => $coo_slider) {
		$t_slider_set_id = $coo_slider->v_slider_set_id;
		$t_slider_set_name = $coo_slider->v_slider_set_name;
		$t_mark  = ($t_slider_set_id == $pro_slider_set_id) ? ' selected="selected"' : '';
		$html .= '<option value="'.$t_slider_set_id.'"'.$t_mark.'>'.$t_slider_set_name.'</option>'."<br>\n";
	}
	$html .= '</select>'."";
	return $html;
}
$coo_cat_slider   = MainFactory::create_object('SliderControl');
$product_slider_array = $coo_cat_slider->get_slider_set_array();
// EOF GM_MOD
?>
<link rel="stylesheet" type="text/css" href="includes/javascript/spiffyCal/spiffyCal_v2_1.css">
<style type="text/css">
	table.hermescfg { background: #D6E6F3; }
</style>
<script type="text/javascript" src="includes/javascript/spiffyCal/spiffyCal_v2_1.js"></script>
<script type="text/javascript">
  var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "new_product", "products_date_available","btnDate1","<?php echo $pInfo->products_date_available; ?>",scBTNMODE_CUSTOMBLUE);
  var expirationDate = new ctlSpiffyCalendarBox("expirationDate", "new_product", "expiration_date","btnDate2","<?php echo $pInfo->expiration_date; ?>",scBTNMODE_CUSTOMBLUE);
</script>
<script type="text/javascript" src="html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>
<script type="text/javascript" src="../gm/javascript/jquery/ui/jquery-ui.js"></script>

<?php
	$coo_text_mgr = new LanguageTextManager('article_tabs', $_SESSION['languages_id']);
	$t_article_tabs_text = $coo_text_mgr->get_text('article_tabs');
	$t_article_tabs_add_text = $coo_text_mgr->get_text('add_article_tab');
	$t_article_tabs_edit_text = $coo_text_mgr->get_text('edit_article_tab');
	$t_article_tabs_delete_text = $coo_text_mgr->get_text('delete_article_tab');
?>

<script type="text/javascript">
use_wysiwyg = <?php echo USE_WYSIWYG; ?>;
article_tab_edit_title = "<?php echo $t_article_tabs_edit_text; ?>";
article_tab_delete_title = "<?php echo $t_article_tabs_delete_text; ?>";
$('.product_tabs_button').live('click', function(){
    container = $( this ).parent();
    $(this).lightbox_plugin({lightbox_width: 800});
    return false;
});

$(document).ready(function(){
	$(".product_tabs_wrapper").sortable({
		items: ".product_tab_box",
		axis: "y",
		containment: "parent"
	});
	$(".product_tabs_wrapper").disableSelection();
	$(".product_tabs_headline").disableSelection();
});
</script>
<?php
// BOF GM_MOD G-Motion
require_once(DIR_FS_CATALOG . 'gm/classes/GMGMotion.php');
$coo_gm_gmotion = new GMGMotion();

$t_gm_gmotion_settings_display = false;

if(!empty($pInfo->products_id))
{
	if($coo_gm_gmotion->check_status($pInfo->products_id)==1)
	{
		$t_gm_gmotion_settings_display = true;
	}
}
?>
<style>
<?php
if(!$t_gm_motion_lightbox)
{
?>
	.gm_gmotion_lightbox_settings
	{
		display: none;
	}
<?php
}
?>
<?php
if(!$t_gm_gmotion_settings_display)
{
?>
	.gm_gmotion_settings
	{
		display: none;
		background-color: #dddddd;
	}
<?php
}
else
{
?>
	.gm_gmotion_settings
	{
		background-color: #d6e6f3;
	}
<?php
}
?>
	.gm_gmotion_start
	{
		z-index: 11;
		cursor: move;
	}

	.gm_gmotion_end
	{
		z-index: 10;
		cursor: move;
	}
</style>

<script type="text/javascript">

$(document).ready(function()
{
	var t_gm_gmotion_icon_width = 17;
	var t_gm_gmotion_icon_height = 17;

	$('#gm_gmotion_activate').click(function()
	{
		if($('#gm_gmotion_activate').prop('checked'))
		{
			$('.gm_gmotion_settings').show();

			var t_image_nr = '';

			$('input[name^="gm_gmotion_image_"]').each(function()
			{
				if($(this).prop('checked') != true)
				{
					t_image_nr = $(this).attr('id').replace('gm_gmotion_image_', '');

					$('.gm_gmotion_settings_' + t_image_nr).hide();
				}
			});

		}
		else
		{
			$('.gm_gmotion_settings').hide();
		}
	});

	$('.gm_gmotion_image').click(function()
	{
		var t_image_nr = $(this).attr('id').replace('gm_gmotion_image_', '');

		if($(this).prop('checked'))
		{
			$('.gm_gmotion_settings_' + t_image_nr).show();
		}
		else
		{
			$('.gm_gmotion_settings_' + t_image_nr).hide();
		}
	});

	$('.gm_gmotion_start, .gm_gmotion_end').draggable(
	{
		containment: 'parent',
		stop: function()
			{
    			var t_top = Number($(this).css('top').replace('px', ''));
    			var t_left = Number($(this).css('left').replace('px', ''));
    			var t_image_nr = $(this).attr('id').replace('gm_gmotion_start_', '');
    			t_image_nr = t_image_nr.replace('gm_gmotion_end_', '');
    			var t_width = Number($('#gm_gmotion_position_area_' + t_image_nr).css('width').replace('px', ''));
    			var t_height = Number($('#gm_gmotion_position_area_' + t_image_nr).css('height').replace('px', ''));
    			var t_position_top = Math.round((t_top / (t_height - t_gm_gmotion_icon_height)) * 100);
    			var t_position_left = Math.round((t_left / (t_width - t_gm_gmotion_icon_width)) * 100);

    			if(t_position_top < 0)
    			{
    				t_position_top = 0;
    			}
    			else if(t_position_top > 100)
    			{
    				t_position_top = 100;
    			}

    			if(t_position_left < 0)
    			{
    				t_position_left = 0;
    			}
    			else if(t_position_left > 100)
    			{
    				t_position_left = 100;
    			}

    			if($(this).attr('id') == 'gm_gmotion_start_' + t_image_nr)
    			{
					$('#gm_gmotion_position_from_' + t_image_nr).val(t_position_left + '% ' + t_position_top + '%');
    			}
				else
				{
					$('#gm_gmotion_position_to_' + t_image_nr).val(t_position_left + '% ' + t_position_top + '%');
				}
		}
	});

	$('.gm_gmotion_position_from').keyup(function()
	{
		var t_image_nr = $(this).attr('id').replace('gm_gmotion_position_from_', '');
		var t_width = Number($('#gm_gmotion_position_area_' + t_image_nr).css('width').replace('px', ''));
		var t_height = Number($('#gm_gmotion_position_area_' + t_image_nr).css('height').replace('px', ''));

		var f_values = $(this).val();
		var t_values = f_values.replace(/%/g, '');
		t_values = t_values.replace(/,/g, '.');
		t_values = t_values.replace(/\s+/g, ' ');
		t_values = t_values.replace(/^\s/g, '');
		t_values = t_values.replace(/\s$/g, '');
		var t_values_array = t_values.split(' ');

		if(!isNaN(t_values_array[0]) && !isNaN(t_values_array[1]))
		{
			var t_position_left = Math.round(t_values_array[0]);
			var t_position_top = Math.round(t_values_array[1]);

			if(t_position_left < 0)
			{
				t_position_left = 0;
			}
			else if(t_position_left > 100)
			{
				t_position_left = 100;
			}

			if(t_position_top < 0)
			{
				t_position_top = 0;
			}
			else if(t_position_top > 100)
			{
				t_position_top = 100;
			}

			var t_left = t_position_left * (t_width - t_gm_gmotion_icon_width) / 100;
			var t_top = t_position_top * (t_height - t_gm_gmotion_icon_height) / 100;

			$('#gm_gmotion_start_' + t_image_nr).css(
			{
				left: t_left + 'px',
				top: t_top + 'px'
			});

			$(this).val(t_position_left + '% ' + t_position_top + '%');

			$(this).css('background-color', '#ffffff');
		}
		else
		{
			$(this).css('background-color', '#ffaaaa');
		}

	});

	$('.gm_gmotion_position_to').keyup(function()
			{
				var t_image_nr = $(this).attr('id').replace('gm_gmotion_position_to_', '');
				var t_width = Number($('#gm_gmotion_position_area_' + t_image_nr).css('width').replace('px', ''));
				var t_height = Number($('#gm_gmotion_position_area_' + t_image_nr).css('height').replace('px', ''));

				var f_values = $(this).val();
				var t_values = f_values.replace(/%/g, '');
				t_values = t_values.replace(/,/g, '.');
				t_values = t_values.replace(/\s+/g, ' ');
				t_values = t_values.replace(/^\s/g, '');
				t_values = t_values.replace(/\s$/g, '');
				var t_values_array = t_values.split(' ');

				if(!isNaN(t_values_array[0]) && !isNaN(t_values_array[1]))
				{
					var t_position_left = Math.round(t_values_array[0]);
					var t_position_top = Math.round(t_values_array[1]);

					if(t_position_left < 0)
					{
						t_position_left = 0;
					}
					else if(t_position_left > 100)
					{
						t_position_left = 100;
					}

					if(t_position_top < 0)
					{
						t_position_top = 0;
					}
					else if(t_position_top > 100)
					{
						t_position_top = 100;
					}

					var t_left = t_position_left * (t_width - t_gm_gmotion_icon_width) / 100;
					var t_top = t_position_top * (t_height - t_gm_gmotion_icon_height) / 100;

					$('#gm_gmotion_end_' + t_image_nr).css(
					{
						left: t_left + 'px',
						top: t_top + 'px'
					});

					$(this).val(t_position_left + '% ' + t_position_top + '%');

					$(this).css('background-color', '#ffffff');
				}
				else
				{
					$(this).css('background-color', '#ffaaaa');
				}

			});
});
</script>
<?php
// EOF GM_MOD G-Motion
?>
<?php
$form_action = ($_GET['pID']) ? 'update_product' : 'insert_product';
$fsk18_array = array(array('id' => 0,'text' => NO),array('id' => 1,'text' => YES));

$t_form_action_array = array();
$t_form_action_array[]	= 'cPath=' . $_GET['cPath'];
$t_form_action_array[]	= 'pID=' . $_GET['pID'];
$t_form_action_array[]	= 'action=' . $form_action;
if(isset($_GET['search']))
{
	$t_form_action_array[] = 'search=' . $_GET['search'];
}
echo xtc_draw_form('new_product', FILENAME_CATEGORIES, implode('&', $t_form_action_array), 'post', 'enctype="multipart/form-data"');
//include DIR_FS_ADMIN . 'html/compatibility/new_product.php';
?>
<table width="100%" cellspacing="0" cellpadding="2" class="hidden">
<tr><td>

<?php // BOF GM_MOD ?>
<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">
	<?php
		(($products_name[$_SESSION['languages_id']]) ? $t_products_name = stripslashes($products_name[$_SESSION['languages_id']]) : $t_products_name = xtc_get_products_name($pInfo->products_id, $_SESSION['languages_id']));
		if( trim( $t_products_name ) != '' )
		{
			echo TEXT_PRODUCTS_NAME . " " . $t_products_name . "<br />";
		}
		echo sprintf(TEXT_NEW_PRODUCT, xtc_output_generated_category_path($current_category_id));
	?>
</div>
<br>
<?php
$gm_p_status_array = array(array('id'=>0,'text'=>TEXT_PRODUCT_NOT_AVAILABLE),array('id'=>1,'text'=>TEXT_PRODUCT_AVAILABLE));
// EOF GM_MOD
?>

<table width="100%"  border="0">
  <tr>
    <td>
        <?php
        if (($_GET['pID'])) {
            echo '<a class="button float_left" href="' . xtc_href_link('properties_combis.php', 'products_id=' . $_GET['pID'] . '&cPath=' . $_GET['cPath'] . '&action=edit_category') . '">' . BUTTON_PROPERTIES . '</a>';
        }
        ?>

		<input type="submit" class="button float_right" name="save_original" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
		<input type="submit" class="button float_right" name="gm_update" value="<?php echo BUTTON_UPDATE; ?>" )>
	    <div class="gx-container" data-gx-widget="button_dropdown">
		    <div data-use-button_dropdown="true" data-gx-compatibility="categories/categories_product_controller" class="pull-right">
			    <button name="save" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')"><?php echo BUTTON_SAVE; ?></button>
			    <ul>
				    <li name="update"><span><?php echo BUTTON_UPDATE; ?></span></li>
			    </ul>
		    </div>
	    </div>
		<?php echo '<a class="button float_right" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
		<br />
		<br />

		<table bgcolor="#f3f3f3" style="border: 1px solid; border-color: #cccccc;" width="100%"  border="0">
    <tr>
		<td>
    <table class="main" border="0" style="width: 100%">
   		<tr>
        <td width="260px"><?php echo TEXT_PRODUCTS_STATUS; ?></td>
        <td width="170px"><?php echo xtc_draw_pull_down_menu('products_status', $gm_p_status_array, $status, 'style="width: 130px"'); ?></td>
				<td width="160px"><?php echo TEXT_PRODUCTS_QUANTITY; ?></td>
				<td><?php echo xtc_draw_input_field('products_quantity', (double)$pInfo->products_quantity,'style="width: 130px"'); ?></td>
   		</tr>
    	<tr>
        <td><?php echo TEXT_PRODUCTS_DATE_AVAILABLE; ?> <small>(JJJJ-MM-TT)</small></td>
    		<td><script type="text/javascript">dateAvailable.writeControl(); dateAvailable.dateFormat="yyyy-MM-dd";</script></td>
				<td><?php echo GM_TEXT_SHOW_QTY_INFO; ?></td>
      	<td><?php echo xtc_draw_selection_field('gm_show_qty_info', 'checkbox', '1',$pInfo->gm_show_qty_info==1 ? true : false); ?></td>
			</tr>
    	<tr>
				<td><?php echo GM_TEXT_SHOW_DATE_ADDED; ?></td>
				<td><?php echo xtc_draw_selection_field('gm_show_date_added', 'checkbox', '1',$pInfo->gm_show_date_added==1 ? true : false); ?></td>
				<td><?php echo TEXT_PRODUCTS_MODEL; ?> </td>
        <td><?php echo  xtc_draw_input_field('products_model', $pInfo->products_model,'style="width: 130px"'); ?></td>
			</tr>
			<tr>
				<td><?php echo TEXT_PRODUCTS_STARTPAGE; ?></td>
        <td><?php echo xtc_draw_selection_field('products_startpage', 'checkbox', '1', $pInfo->products_startpage==1 ? true : false); ?></td>
				<td><?php echo TEXT_PRODUCTS_EAN; ?> </td>
      	<td><?php echo  xtc_draw_input_field('products_ean', $pInfo->products_ean,'style="width: 130px"'); ?></td>
			</tr>
			<tr>
				<td><?php echo TEXT_PRODUCTS_STARTPAGE_SORT; ?></td>
   			<td><?php echo  xtc_draw_input_field('products_startpage_sort', $pInfo->products_startpage_sort ,'style="width: 130px"'); ?></td>
    		<td><?php echo TEXT_PRODUCTS_MANUFACTURER; ?></td>
        <td><?php echo xtc_draw_pull_down_menu('manufacturers_id', $manufacturers_array, $pInfo->manufacturers_id, 'style="width: 130px"'); ?></td>
		 	</tr>
      <tr>
				<td><?php echo TEXT_PRODUCTS_SORT; ?></td>
        <td><?php echo  xtc_draw_input_field('products_sort', $pInfo->products_sort,'style="width: 130px"'); ?></td>
		    <td><?php echo TEXT_PRODUCTS_WEIGHT; ?></td>
      	<td><?php echo xtc_draw_input_field('products_weight', $pInfo->products_weight,'style="width: 130px"'); ?><?php echo TEXT_PRODUCTS_WEIGHT_INFO; ?></td>
			</tr>
			<?php
			$gm_price_status_selection = array();
			$gm_price_status_selection[] = array('id' => 0, 'text' => GM_PRICE_STATUS_0);
			$gm_price_status_selection[] = array('id' => 1, 'text' => GM_PRICE_STATUS_1);
			$gm_price_status_selection[] = array('id' => 2, 'text' => GM_PRICE_STATUS_2);
			?>
      <tr>
        <td><?php echo TEXT_PRODUCTS_VPE_VISIBLE.xtc_draw_selection_field('products_vpe_status', 'checkbox', '1',$pInfo->products_vpe_status==1 ? true : false); ?>
        <?php echo TEXT_PRODUCTS_VPE_VALUE; ?></td>
        <td><?php echo xtc_draw_input_field('products_vpe_value', $pInfo->products_vpe_value,'style="width: 130px"'); ?></td>
				<td><?php echo GM_TEXT_SHOW_WEIGHT; ?></td>
      	<td><?php echo xtc_draw_selection_field('gm_show_weight', 'checkbox', '1',$pInfo->gm_show_weight==1 ? true : false); ?></td>
			</tr>
			<tr>
				<td><?php echo TEXT_PRODUCTS_VPE; ?></td>
				<td><?php echo xtc_draw_pull_down_menu('products_vpe', $vpe_array, $pInfo->products_vpe='' ?  DEFAULT_PRODUCTS_VPE_ID : $pInfo->products_vpe, 'style="width: 130px"'); ?></td>
				<td><?php echo GM_PRICE_STATUS; ?></td>
      	<td><?php echo xtc_draw_pull_down_menu('gm_price_status', $gm_price_status_selection, $pInfo->gm_price_status, 'style="width: 130px"'); ?></td>
			</tr>
			<tr>
        <td><?php echo TEXT_FSK18; ?></td>
      	<td><?php echo xtc_draw_pull_down_menu('fsk18', $fsk18_array, $pInfo->products_fsk18, 'style="width: 130px"'); ?></td>
				<td><?php echo TEXT_NC_GAMBIOULTRA_COSTS; ?></td>
        <td><?php echo xtc_draw_input_field('nc_ultra_shipping_costs', $pInfo->nc_ultra_shipping_costs, 'style="width: 130px"'); ?></td>
			</tr>
			<tr>
				<td><?php echo TEXT_QUANTITYUNIT; ?></td>
				<td><?php echo xtc_draw_pull_down_menu('quantityunit', $quantity_unit, $quantity_unit_select, 'style="width: 130px"'); ?></td>
				<?php if (ACTIVATE_SHIPPING_STATUS=='true') { ?>
				<td><?php echo BOX_SHIPPING_STATUS.':'; ?></td>
				<td><?php echo xtc_draw_pull_down_menu('shipping_status', $shipping_statuses, $pInfo->products_shippingtime, 'style="width: 130px"'); ?></td>
				<?php } ?>
			</tr>
			<tr>
				<td width="260px"><?php echo TEXT_PRODUCT_TYPE; ?></td>
				<td width="170px"><?php echo xtc_draw_pull_down_menu('product_type', $product_types_array, $pInfo->product_type, 'style="width: 130px"'); ?></td>
				<td colspan="2">
					<?php // BEGIN HERMES ?>
					<table class="main hermescfg">
						<tr>
							<th colspan="2">Hermes ProfiPaketService</th>
						</tr>
						<tr>
							<td class="label"><label for="hermes_minpclass">Paketklasse mindestens:</label></td>
							<td>
								<select id="hermes_minpclass" name="hermes_minpclass" size="1">
								<?php foreach($pclasses as $pclass): ?>
									<option value="<?php echo $pclass['name'] ?>" <?php echo $pclass['name'] == $hermes_options['min_pclass'] ? 'selected' : '' ?>><?php echo $pclass['name'] .' - '. $pclass['desc'] ?></option>
								<?php endforeach ?>
								</select>
							</td>
						</tr>
					</table>
					<?php // END HERMES ?>

					</td>
			</tr>
			<!-- CATEGORIES-SLIDER -->
			<?php // BOF GM_MOD
			if(!empty($product_slider_array)) { ?>
				<tr>
					<td><?php echo TITLE_PRODUCT_SLIDER; ?>:</td>
					<td><?php echo generateProductSliderSelect(); ?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			<?php }
			// EOF GM_MOD ?>
			<!-- CATEGORIES-SLIDER -->
		</table>

	<table class="main" border="0" style="float:left; width:50%">
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<?php
			$gm_min_order = 1;
			if($pInfo->gm_min_order != '') $gm_min_order = (double)$pInfo->gm_min_order;
			$gm_graduated_qty = 1;
			if($pInfo->gm_graduated_qty != '') $gm_graduated_qty = (double)$pInfo->gm_graduated_qty;
			?>
			<tr>
        <td width="260px"><?php echo GM_TEXT_MIN_ORDER; ?></td>
      	<td><?php echo xtc_draw_input_field('gm_min_order', $gm_min_order, 'style="width: 130px"') . GM_TEXT_INPUT_ADVICE; ?></td>
			</tr>
	  <tr>
      <td><?php echo GM_TEXT_GRADUATED_QTY; ?></td>
      <td><?php echo xtc_draw_input_field('gm_graduated_qty', $gm_graduated_qty, 'style="width: 130px"') . GM_TEXT_INPUT_ADVICE; ?></td>
		</tr>
		<tr>
          <?php

$files = array ();
if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/')) {
	while (($file = readdir($dir)) !== false) {
		if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_info/'.$file)
			&& $file !== "index.html"
			&& (strpos($file, '-USERMOD.') === false || strpos($pInfo->product_template, '-USERMOD.') !== false)) {
			$files[] = array ('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
$default_array = array ();
// set default value in dropdown!
if ($content['content_file'] == '') {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
	$default_value = $pInfo->product_template;
	$files = array_merge($default_array, $files);
} else {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
	$default_value = $pInfo->product_template;
	$files = array_merge($default_array, $files);
}
echo '<td>'.TEXT_CHOOSE_INFO_TEMPLATE.':</td>';
echo '<td>'.xtc_draw_pull_down_menu('info_template', $files, $default_value, 'style="width: 220px"');
?>
        </td>
      </tr>
      <tr>


          <?php

$files = array ();
if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/')) {
	while (($file = readdir($dir)) !== false) {
		if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_options/'.$file)
			&& $file != "index.html"
			&& (strpos($file, '-USERMOD.') === false || strpos($pInfo->options_template, '-USERMOD.') !== false)) {
			$files[] = array ('id' => $file, 'text' => $file);
		} //if
	} // while
	closedir($dir);
}
// set default value in dropdown!
$default_array = array ();
if ($content['content_file'] == '') {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
	$default_value = $pInfo->options_template;
	$files = array_merge($default_array, $files);
} else {
	$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
	$default_value = $pInfo->options_template;
	$files = array_merge($default_array, $files);
}
echo '<td>'.TEXT_CHOOSE_OPTIONS_TEMPLATE.':'.'</td>';
echo '<td>'.xtc_draw_pull_down_menu('options_template', $files, $default_value, 'style="width: 220px"');
?>
        </td>
      </tr>
			<tr>
			<?php
				$files = array ();
				if ($dir = opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/gm_product_options/')) {
					while (($file = readdir($dir)) !== false) {
						if (is_file(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/gm_product_options/'.$file)
							&& $file != "index.html"
							&& (strpos($file, '-USERMOD.') === false || strpos($pInfo->gm_options_template, '-USERMOD.') !== false)) {
							$files[] = array ('id' => $file, 'text' => $file);
						} //if
					} // while
					closedir($dir);
				}
				// set default value in dropdown!
				$default_array = array ();
				if ($content['content_file'] == '') {
					$default_array[] = array ('id' => 'default', 'text' => TEXT_SELECT);
					$default_value = $pInfo->gm_options_template;
					$files = array_merge($default_array, $files);
				} else {
					$default_array[] = array ('id' => 'default', 'text' => TEXT_NO_FILE);
					$default_value = $pInfo->gm_options_template;
					$files = array_merge($default_array, $files);
				}
				echo '<td>'.GM_TEXT_CHOOSE_OPTIONS_TEMPLATE.':'.'</td>';
				echo '<td>'.xtc_draw_pull_down_menu('gm_options_template', $files, $default_value, 'style="width: 220px"');
				?>
        </td>
			</tr>
			<tr>
        <td><?php echo GM_TEXT_SHOW_PRICE_OFFER; ?></td>
     		<td><?php echo xtc_draw_selection_field('gm_show_price_offer', 'checkbox', '1',$pInfo->gm_show_price_offer==1 ? true : false); ?></td>
			</tr>
	<?php // EOF GM_MOD ?>
		<tr>

			 <td><?php echo GM_SITEMAP_ENTRY; ?></td>
			<td><?php
					if($pInfo->gm_sitemap_entry == '1') {
						echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', true);
					} else {
						echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', false);
					}
				?></td>
		</tr>
		<tr>
		<?php
			$gm_priority   = array();
			$gm_priority[] = array('id' => '0.0', 'text' => '0.0');
			$gm_priority[] = array('id' => '0.1', 'text' => '0.1');
			$gm_priority[] = array('id' => '0.2', 'text' => '0.2');
			$gm_priority[] = array('id' => '0.3', 'text' => '0.3');
			$gm_priority[] = array('id' => '0.4', 'text' => '0.4');
			$gm_priority[] = array('id' => '0.5', 'text' => '0.5');
			$gm_priority[] = array('id' => '0.6', 'text' => '0.6');
			$gm_priority[] = array('id' => '0.7', 'text' => '0.7');
			$gm_priority[] = array('id' => '0.8', 'text' => '0.8');
			$gm_priority[] = array('id' => '0.9', 'text' => '0.9');
			$gm_priority[] = array('id' => '1.0', 'text' => '1.0');
		?>

        <td><?php echo GM_SITEMAP_PRIORITY; ?></td>
				<td><?php echo xtc_draw_pull_down_menu('gm_priority', $gm_priority, $pInfo->gm_priority); ?></td>
		<?php
			$gm_changefreq   = array();
			$gm_changefreq[] = array('id' => 'always', 'text' => TITLE_ALWAYS);
			$gm_changefreq[] = array('id' => 'hourly', 'text' => TITLE_HOURLY);
			$gm_changefreq[] = array('id' => 'daily', 'text' => TITLE_DAILY);
			$gm_changefreq[] = array('id' => 'weekly', 'text' => TITLE_WEEKLY);
			$gm_changefreq[] = array('id' => 'monthly', 'text' => TITLE_MONTHLY);
			$gm_changefreq[] = array('id' => 'yearly', 'text' => TITLE_YEARLY);
			$gm_changefreq[] = array('id' => 'never', 'text' => TITLE_NEVER);
		?>
		</tr>
		<tr>
        <td><?php echo GM_SITEMAP_CHANGEFREQ; ?></td>
      	<td><?php echo xtc_draw_pull_down_menu('gm_changefreq', $gm_changefreq, $pInfo->gm_changefreq); ?></td>
			</tr>
		<?php
		// BOF GM_MOD GX-Customizer:
		require_once('../gm/modules/gm_gprint_admin_new_product.php');
		?>

		<?php
		// BOF GM_MOD G-Motion
		?>
		<tr bgcolor="#d6e6f3">
			<td><?php echo GM_GMOTION_ACTIVATE; ?></td>
			<td><input type="checkbox" name="gm_gmotion_activate" id="gm_gmotion_activate" value="1"<?php echo $coo_gm_gmotion->check_status($pInfo->products_id)==1 ? ' checked="checked"' : ''; ?> /></td>
		</tr>
		<?php
		// EOF GM_MOD G-Motion
		?>
    </table>


	<?php
	$coo_text_mgr = MainFactory::create_object('LanguageTextManager', array('product_item_codes', $_SESSION['languages_id']) );

	$t_google_export_condition_array[] = array('id' => $coo_text_mgr->get_text('condition_value_new'), 'text' => $coo_text_mgr->get_text('condition_value_new') );
	$t_google_export_condition_array[] = array('id' => $coo_text_mgr->get_text('condition_value_used'), 'text' => $coo_text_mgr->get_text('condition_value_used') );
	$t_google_export_condition_array[] = array('id' => $coo_text_mgr->get_text('condition_value_refurbished'), 'text' => $coo_text_mgr->get_text('condition_value_refurbished') );

	$t_google_export_availability_array[] = array('id' => '', 'text' => $coo_text_mgr->get_text('text_please_select') );

	$t_availability_sql = "SELECT google_export_availability_id, availability FROM google_export_availability ORDER BY google_export_availability_id";
	$t_availability_result = xtc_db_query($t_availability_sql);
	while($t_availability_result_array = xtc_db_fetch_array($t_availability_result))
	{
		$t_google_export_availability_array[] = array('id' => $t_availability_result_array['google_export_availability_id'], 'text' => $t_availability_result_array['availability'] );
	}

	?>
	<table class="main" border="0" style="float: right; width: 50%">
		<tr>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_isbn') ?>:</td>
			<td><?php echo xtc_draw_input_field('code_isbn', $pInfo->code_isbn, 'style="width: 130px"'); ?> <small><?php echo $coo_text_mgr->get_text('label_isbn_info') ?></small></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_upc') ?>:</td>
			<td><?php echo xtc_draw_input_field('code_upc', $pInfo->code_upc, 'style="width: 130px"'); ?> <small><?php echo $coo_text_mgr->get_text('label_upc_info') ?></small></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_mpn') ?>:</td>
			<td><?php echo xtc_draw_input_field('code_mpn', $pInfo->code_mpn, 'style="width: 130px"'); ?> <small><?php echo $coo_text_mgr->get_text('label_mpn_info') ?></small></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_jan') ?>:</td>
			<td><?php echo xtc_draw_input_field('code_jan', $pInfo->code_jan, 'style="width: 130px"'); ?> <small><?php echo $coo_text_mgr->get_text('label_jan_info') ?></small></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_brand') ?>:</td>
			<td><?php echo xtc_draw_input_field('brand_name', $pInfo->brand_name, 'style="width: 130px"'); ?></td>
		</tr>
		<tr>
			<?php
				$t_default_gender = array('id' => '', 'text' => '---');
				$t_genders_array = array(
					0 => array('id' => '', 'text' => '---'),
					1 => array('id' => 'Herren', 'text' => 'Herren'),
					2 => array('id' => 'Damen', 'text' => 'Damen'),
					3 => array('id' => 'Unisex', 'text' => 'Unisex')
				);
				$t_default_age_group = array('id' => '', 'text' => '---');
				$t_age_groups_array = array(
					0 => array('id' => '', 'text' => '---'),
					1 => array('id' => 'Erwachsene', 'text' => 'Erwachsene'),
					2 => array('id' => 'Kinder', 'text' => 'Kinder')
				);
			?>
			<td><?php echo $coo_text_mgr->get_text('label_identifier_exists') ?>:</td>
			<td><?php echo xtc_draw_checkbox_field('identifier_exists', 1, ((isset($_GET['pID']) && !empty($_GET['pID'])) ? (boolean)$pInfo->identifier_exists : true)); ?></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_gender') ?>:</td>
			<td><?php echo xtc_draw_pull_down_menu('gender', $t_genders_array, (empty($pInfo->gender) ? $t_default_gender : $pInfo->gender), 'style="width: 130px"'); ?></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_age_group') ?>:</td>
			<td><?php echo xtc_draw_pull_down_menu('age_group', $t_age_groups_array, (empty($pInfo->age_group) ? $t_default_age_group : $pInfo->age_group), 'style="width: 130px"'); ?></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_expiration_date') ?>: <small>(JJJJ-MM-TT)</small></td>
    		<td><script type="text/javascript">expirationDate.writeControl(); expirationDate.dateFormat="yyyy-MM-dd";</script></td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_condition') ?>:</td>
			<td>
				<?php echo xtc_draw_pull_down_menu('google_export_condition', $t_google_export_condition_array, $pInfo->google_export_condition, 'style="width: 130px"'); ?>
				<small><?php echo $coo_text_mgr->get_text('label_google_export_only') ?></small>
			</td>
		</tr>
		<tr>
			<td><?php echo $coo_text_mgr->get_text('label_availability') ?>:</td>
			<td>
				<?php echo xtc_draw_pull_down_menu('google_export_availability_id', $t_google_export_availability_array, $pInfo->google_export_availability_id, 'style="width: 130px"'); ?>
				<small><?php echo $coo_text_mgr->get_text('label_google_export_only') ?></small>
			</td>
		</tr>
	</table>

		</td>
	</tr>
</table>

<?php include(DIR_FS_ADMIN . 'includes/modules/additional_fields.inc.php'); ?>

    <?php

	$coo_admin_edit_product_extender_component = MainFactory::create_object('AdminEditProductExtenderComponent');
	$coo_admin_edit_product_extender_component->set_data('GET', $_GET);
	$coo_admin_edit_product_extender_component->set_data('POST', $_POST);
	$coo_admin_edit_product_extender_component->proceed();

	$t_extender_output_array_top = $coo_admin_edit_product_extender_component->get_output('top');

	foreach($t_extender_output_array_top as $t_output_array)
	{
		$t_title = '';
		if(isset($t_output_array['title']))
		{
			$t_title = $t_output_array['title'];
		}
		$t_content = '';
		if(isset($t_output_array['content']))
		{
			$t_content = $t_output_array['content'];
		}

		echo '<table bgcolor="#f3f3f3" style="border:1px solid;border-color:#cccccc;margin-top:10px;" width="100%"  border="0">
				<tr>
					<td>
						<table class="main" border="0" width="100%">
							<tr>
								<td style="font-size:14px;font-weight:bold;height: 28px;">' . $t_title . '</td>
							</tr>
							<tr>
								<td>' . $t_content . '</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';
	}

	echo "<div class='google_categories_administration' id='product_id_".$_GET['pID']."'></div>";
	?>
  </td>
  </tr>
</table>
  <br /><br />
  <?php for ($i = 0, $n = sizeof($languages); $i < $n; $i++) { ?>
  <table width="100%" border="0">
  <tr>
  <td bgcolor="#000000" height="10"></td>
  </tr>
  <tr>
    <td bgcolor="#FFCC33" valign="top" class="main"><?php echo xtc_image(DIR_WS_LANGUAGES . $languages[$i]['directory'] .'/'. $languages[$i]['image'], $languages[$i]['name']); ?>&nbsp;<?php echo TEXT_PRODUCTS_NAME; ?><?php echo xtc_draw_input_field('products_name[' . $languages[$i]['id'] . ']', (($products_name[$languages[$i]['id']]) ? stripslashes($products_name[$languages[$i]['id']]) : xtc_get_products_name($pInfo->products_id, $languages[$i]['id'])),'size=60'); ?></td>
  </tr>
  <tr>
    <td class="main"><?php echo TEXT_PRODUCTS_URL . '&nbsp;<small>' . TEXT_PRODUCTS_URL_WITHOUT_HTTP . '</small>'; ?><?php echo xtc_draw_input_field('products_url[' . $languages[$i]['id'] . ']', (($products_url[$languages[$i]['id']]) ? stripslashes($products_url[$languages[$i]['id']]) : xtc_get_products_url($pInfo->products_id, $languages[$i]['id'])),'size=60'); ?></td>
  </tr>
</table>

<!-- input boxes desc, meta etc -->
<table width="100%" border="0">
  <tr>
    <td class="main">
        <STRONG><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></STRONG><br />
        <?php
		if($products_description[$languages[$i]['id']])
		{
			$t_products_complete_description = stripslashes($products_description[$languages[$i]['id']]);
		}
		else
		{
			$t_products_complete_description = xtc_get_products_description($pInfo->products_id, $languages[$i]['id']);
		}
		$t_products_description = '';
		$t_products_tabs_headline = array();
		$t_products_tabs = array();

		$t_matches = array();
		preg_match('/(.*)\[TAB:/isU', $t_products_complete_description, $t_matches);
		if(count($t_matches) > 1)
		{
			$t_products_description = $t_matches[1];
		}
		else
		{
			$t_products_description = $t_products_complete_description;
		}
		$t_products_complete_description = str_replace('~', '#GMTilde#', $t_products_complete_description);
		$t_products_complete_description = str_replace( '[TAB:', '~TAB:', $t_products_complete_description);

		$t_matches2 = array();
		preg_match_all('/~TAB:([^\]]+)\]([^~]*)/', $t_products_complete_description, $t_matches2);
		foreach($t_matches2[1] AS $key => $value)
		{
			$t_products_tabs_headline[] = str_replace('#GMTilde#', '~', $t_matches2[1][$key]);
			$t_products_tabs[] = str_replace('#GMTilde#', '~', $t_matches2[2][$key]);
		}

		$t_textarea_name = 'products_description_' . $languages[$i]['id'];
		echo xtc_draw_textarea_field($t_textarea_name, 'soft', '126', '30', $t_products_description);

		if(USE_WYSIWYG == 'true')
		{
			echo xtc_wysiwyg('products_description', $_SESSION['language_code'], 'products_description_' . $languages[$i]['id']);
		}
		?>
		</td>
  </tr>


  <tr>
    <td class="main">
        <div class="product_tabs_wrapper" id="language_<?php echo $languages[$i]['id']; ?>">
            <div class="product_tabs_headline"><strong><?php echo $t_article_tabs_text; ?>:</strong>&nbsp;&nbsp;(<a href="article_tabs/article_tabs_add.html?buttons=cancel-save" class="product_tabs_button" title="<?php echo $t_article_tabs_add_text; ?>"><img src="images/buttons/button_add_green_plus.png" border="0" style="height: 10px;" />&nbsp;<small><?php echo $t_article_tabs_add_text; ?></small></a>)</div>
            <?php
            foreach($t_products_tabs_headline AS $key => $value){
            ?>
            <div class="product_tab_box">
                <div class="product_tab_headline"><?php echo htmlentities_wrapper( $t_products_tabs_headline[$key] ); ?></div>
                <input type="text" value='<?php echo htmlentities_wrapper($t_products_tabs_headline[$key], ENT_QUOTES); ?>' name="products_tab_headline_<?php echo $languages[$i]['id']; ?>[]"/>
                <textarea name="products_tab_<?php echo $languages[$i]['id']; ?>[]"><?php echo $t_products_tabs[$key]; ?></textarea>
                <a href="article_tabs/article_tabs_delete.html?buttons=cancel-delete" class="product_tabs_button" title="<?php echo $t_article_tabs_delete_text; ?>"><img class="product_tab_delete" src="images/buttons/button_cancel_red_cross.png" border="0" alt="" /></a>
                <a href="article_tabs/article_tabs_edit.html?buttons=cancel-save" class="product_tabs_button" title="<?php echo $t_article_tabs_edit_text; ?>"><img class="product_tab_edit" src="images/buttons/button_edit_pencil.png" border="0" alt="" /></a>
                <div class="product_tab_clear"><!-- &nbsp; --></div>
            </div>
            <?php
            }
            ?>
            <div class="product_tab_clear"><!-- &nbsp; --></div>
        </div>
		</td>
  </tr>

  <tr>
    <td class="main" valign="top">

    <table cellpadding="0" cellspacing="0">
    <tr>
     <td width="60%" valign="top" class="main">
        <STRONG><?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?></STRONG><br />
        <?php
			echo xtc_draw_textarea_field('products_short_description_' . $languages[$i]['id'], 'soft', '86', '20', (($products_short_description[$languages[$i]['id']]) ? stripslashes($products_short_description[$languages[$i]['id']]) : xtc_get_products_short_description($pInfo->products_id, $languages[$i]['id'])));

			if(USE_WYSIWYG == 'true')
			{
				echo xtc_wysiwyg('products_short_description', $_SESSION['language_code'], 'products_short_description_' . $languages[$i]['id']);
			}
		?>
		 </td>
     <td class="main" valign="top" style="padding: 15px;">
        <?php echo TEXT_PRODUCTS_KEYWORDS; ?><br />
        <?php echo xtc_draw_input_field('products_keywords[' . $languages[$i]['id'] . ']',(($products_keywords[$languages[$i]['id']]) ? stripslashes($products_keywords[$languages[$i]['id']]) : xtc_get_products_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=30 maxlength=255'); ?><br />
        <?php echo TEXT_META_TITLE; ?><br />
        <?php echo xtc_draw_input_field('products_meta_title[' . $languages[$i]['id'] . ']',(($products_meta_title[$languages[$i]['id']]) ? stripslashes($products_meta_title[$languages[$i]['id']]) : xtc_get_products_meta_title($pInfo->products_id, $languages[$i]['id'])), 'size=30'); ?><br />
        <?php echo TEXT_META_DESCRIPTION; ?><br />
        <?php echo xtc_draw_input_field('products_meta_description[' . $languages[$i]['id'] . ']',(($products_meta_description[$languages[$i]['id']]) ? stripslashes($products_meta_description[$languages[$i]['id']]) : xtc_get_products_meta_description($pInfo->products_id, $languages[$i]['id'])), 'size=30'); ?><br />
        <?php echo TEXT_META_KEYWORDS; ?><br />
        <?php echo xtc_draw_input_field('products_meta_keywords[' . $languages[$i]['id'] . ']', (($products_meta_keywords[$languages[$i]['id']]) ? stripslashes($products_meta_keywords[$languages[$i]['id']]) : xtc_get_products_meta_keywords($pInfo->products_id, $languages[$i]['id'])), 'size=30'); ?>
<?php // BOF GM_MOD ?>
				<br />
				<?php echo GM_TEXT_URL_KEYWORDS; ?><br />
				<?php echo xtc_draw_input_field('gm_url_keywords[' . $languages[$i]['id'] . ']', (($gm_url_keywords[$languages[$i]['id']]) ? stripslashes($gm_url_keywords[$languages[$i]['id']]) : gm_get_products_url_keywords($pInfo->products_id, $languages[$i]['id'])), 'size="30"'); ?>
<?php // EOF GM_MOD ?>
     </td>
    </tr>
	<tr>
		<td class="main">
			<strong><?php echo TEXT_CHECKOUT_INFORMATION; ?></strong><br />
			<?php
				echo xtc_draw_textarea_field('checkout_information_' . $languages[$i]['id'], 'soft', '86', '10', (($checkout_information[$languages[$i]['id']]) ? stripslashes($checkout_information[$languages[$i]['id']]) : get_checkout_information($pInfo->products_id, $languages[$i]['id'])));

				if(USE_WYSIWYG == 'true')
				{
					echo xtc_wysiwyg('checkout_information', $_SESSION['language_code'], 'checkout_information_' . $languages[$i]['id']);
				}
				?>

		</td>
	</tr>
    </table>

   </td>
  </tr>
</table>

<?php } ?>
<table width="100%"><tr><td style="border-bottom: thin dashed Gray;">&nbsp;</td></tr></table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<!-- bof gm -->
<tr><td><span class="main" style="padding-left: 10px;"><a name="gm_anchor"><?php echo HEADING_PRODUCT_IMAGES; ?></a>
<?php if($_GET['gm_redirect']== 1) echo GM_TITLE_REDIRECT; else if($_GET['gm_redirect'] > 1) echo GM_TITLE_REDIRECT_MULTI; ?></span>
</td></tr>
<!-- eof gm -->
<tr><td><br />
<table width="100%" border="0" bgcolor="#f3f3f3" style="border: 1px solid; border-color: #cccccc;">

<?php

include (DIR_WS_MODULES.'products_images.php');
?>
    <tr><td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
<?php

if (GROUP_CHECK == 'true') {
	$customers_statuses_array = xtc_get_customers_statuses();
	$customers_statuses_array = array_merge(array (array ('id' => 'all', 'text' => TXT_ALL)), $customers_statuses_array);
?>
<tr>
<td style="border-top: 1px solid; border-color: #ff0000;" valign="top" class="main" ><?php echo ENTRY_CUSTOMERS_STATUS; ?></td>
<td style="border-top: 1px solid; border-left: 1px solid; border-color: #ff0000;"  bgcolor="#FFCC33" class="main">
<?php

	for ($i = 0; $n = sizeof($customers_statuses_array), $i < $n; $i ++) {
		$code = '$id=$pInfo->group_permission_'.$customers_statuses_array[$i]['id'].';';
		eval ($code);

		if ($id==1) {

			$checked = 'checked ';

		} else {
			$checked = '';
		}
		echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
	}

?>
</td>
</tr>
<?php

}
?>
</table>
</td></tr>

<tr><td>

<?php
	$t_extender_output_array_bottom = $coo_admin_edit_product_extender_component->get_output('bottom');

	foreach($t_extender_output_array_bottom as $t_output_array)
	{
		$t_title = '';
		if(isset($t_output_array['title']))
		{
			$t_title = $t_output_array['title'];
		}
		$t_content = '';
		if(isset($t_output_array['content']))
		{
			$t_content = $t_output_array['content'];
		}

		echo '<table bgcolor="#f3f3f3" style="border:1px solid;border-color:#cccccc;margin-top:10px;" width="100%"  border="0">
				<tr>
					<td>
						<table class="main" border="0" width="100%">
							<tr>
								<td style="font-size:14px;font-weight:bold;height: 28px;">' . $t_title . '</td>
							</tr>
							<tr>
								<td>' . $t_content . '</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';
	}
?>

<table width="100%" border="0">
        <tr>
          <td colspan="4"><?php include(DIR_WS_MODULES.'group_prices.php'); ?></td>
        </tr>
        <tr>
          <td colspan="4"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
        </tr>
</table>
</td></tr>

    <tr>
     <td class="main" align="right">
     <?php

		echo xtc_draw_hidden_field('products_date_added', (($pInfo->products_date_added) ? $pInfo->products_date_added : date('Y-m-d')));
		echo xtc_draw_hidden_field('products_id', $pInfo->products_id);
		echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token());

        if (($_GET['pID'])) {
            echo "<a class='button' style='float:left' href='" . xtc_href_link('properties_combis.php', 'products_id=' . $_GET['pID'] . '&cPath=' . $_GET['cPath'] . '&action=edit_category') . "'>" . BUTTON_PROPERTIES . "</a>";
        }
	?>

	<input type="submit" class="button float_right" name="save" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
	<input type="submit" class="button float_right" name="gm_update" value="<?php echo BUTTON_UPDATE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
	<?php echo '<a class="button float_right" href="' . xtc_href_link(FILENAME_CATEGORIES, 'cPath=' . $cPath . '&pID=' . $_GET['pID']) . '">' . BUTTON_CANCEL . '</a>'; ?>
  	 </td>
    </tr></form>
		        </tr>
        </table>
     </td>
    </tr>
        </table>
    <tr>
     <td>
