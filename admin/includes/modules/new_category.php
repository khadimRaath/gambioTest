<?php
/* --------------------------------------------------------------
   new_category.php 2015-09-17
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
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: new_category.php 799 2005-02-23 18:08:06Z novalis $)

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
    include_once(DIR_FS_CATALOG. 'gm/inc/gm_get_url_keywords.inc.php');		
    $languages = xtc_get_languages();
	
	if ( ($_GET['cID']) ) {
		
		$category_query = xtc_db_query("
										SELECT 
											* 
										FROM " .
											TABLE_CATEGORIES				. " c, " .
											TABLE_CATEGORIES_DESCRIPTION	. " cd
										WHERE
											c.categories_id	= cd.categories_id
										AND
											c.categories_id	= '" . $_GET['cID'] . "'
										");

      $category = xtc_db_fetch_array($category_query);
      $cInfo = new objectInfo($category);

    } elseif ($_POST) {

		$cInfo = new objectInfo($_POST);
		$categories_name				= $_POST['categories_name'];
		$categories_heading_title		= $_POST['categories_heading_title'];
		$categories_description			= $_POST['categories_description'];
		$categories_meta_title			= $_POST['categories_meta_title'];
		$categories_meta_description	= $_POST['categories_meta_description'];
		$categories_meta_keywords		= $_POST['categories_meta_keywords'];
		
		$gm_url_keywords				= gm_prepare_string($_POST['gm_url_keywords']);

    } else {

      $cInfo = new objectInfo(array());

    }
	$text_new_or_edit = TEXT_INFO_HEADING_EDIT_CATEGORY . ': ' . xtc_get_categories_name($cInfo->categories_id, $_SESSION['languages_id']);
	if($_GET['action']=='new_category') {
		$text_new_or_edit = TEXT_INFO_HEADING_NEW_CATEGORY;
	}


		$form_action = ($_GET['cID']) ? 'update_category' : 'insert_category';
		$t_form_action_array = array();
		$t_form_action_array[]	= 'cPath=' . $cPath;
		$t_form_action_array[]	= 'cID=' . $_GET['cID'];
		$t_form_action_array[]	= 'action=' . $form_action;
		if(isset($_GET['search']))
		{
			$t_form_action_array[] = 'search=' . $_GET['search'];
		}
		
		echo xtc_draw_form('new_category', FILENAME_CATEGORIES, implode('&', $t_form_action_array), 'post', 'enctype="multipart/form-data"'); 

		//Extender
	$coo_admin_edit_category_extender_component = MainFactory::create_object('AdminEditCategoryExtenderComponent');
	$coo_admin_edit_category_extender_component->set_data('GET', $_GET);
	$coo_admin_edit_category_extender_component->set_data('POST', $_POST);
	$coo_admin_edit_category_extender_component->set_data('cInfo', $cInfo);
	$coo_admin_edit_category_extender_component->proceed();
	
	include DIR_FS_ADMIN . 'html/compatibility/new_category.php';
	?>
    <table width="100%" cellspacing="0" cellpadding="2" id="old-category-table">
	<tr >
		<td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">
				<?php echo sprintf($text_new_or_edit, xtc_output_generated_category_path($current_category_id)); ?>
			</div>
			<br />
	<?php
		$t_extender_output_array = $coo_admin_edit_category_extender_component->get_output('top');
	
		foreach($t_extender_output_array as $t_output_array)
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

			echo '<table border="0" width="99%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
					<tr>
						<td>
							<table class="main" border="0" width="100%">
								' . $t_title . '
								<tr>
									<td>' . $t_content . '</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>';
		}
	?>
      <div style="width:49%;margin-right:10px;float:left;">
			<table border="0" width="100%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">

				<!-- CATEGORIES-NAME -->
				<?php for ($i=0; $i<sizeof($languages); $i++) { ?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php if ($i == 0) echo TEXT_EDIT_CATEGORIES_NAME; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('categories_name[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_name($cInfo->categories_id, $languages[$i]['id']))); ?>								
					</td>
				</tr>
				<?php
					}
				?>

				<!-- CATEGORIES-HEADING -->
				<?php for ($i=0; $i<sizeof($languages); $i++) { ?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php if ($i == 0) echo TEXT_EDIT_CATEGORIES_HEADING_TITLE; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('categories_heading_title[' . $languages[$i]['id'] . ']', (($categories_name[$languages[$i]['id']]) ? stripslashes($categories_name[$languages[$i]['id']]) : xtc_get_categories_heading_title($cInfo->categories_id, $languages[$i]['id']))); ?>
					</td>
				</tr>
				<?php
					}
				?>

			</table>
			<table border="0" width="100%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
				
				<!-- CATEGORIES-STATUS -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_EDIT_STATUS; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('status', 'checkbox', '1',$cInfo->categories_status==1 ? true : false); ?>
					</td>
				</tr>
				<!-- CATEGORIES-TEMPLATE -->
				<?php
					$files=array();
					if ($dir= opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/')) {						
						while  (($file = readdir($dir)) !==false) {
							if (is_file( DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/categorie_listing/'.$file) 
								&& $file !="index.html"
								&& (strpos($file, '-USERMOD.') === false || strpos($pInfo->categories_template, '-USERMOD.') !== false)){
								$files[]=array(
												'id' => $file,
												'text' => $file
										);
							}
						}	
						closedir($dir);
					}

					$default_array=array();
					// set default value in dropdown!
					if ($content['content_file']=='') {
						$default_array[]=array('id' => 'default','text' => TEXT_SELECT);
						$default_value=$cInfo->categories_template;
						$files=array_merge($default_array,$files);
					} else {
						$default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
						$default_value=$cInfo->categories_template;
						$files=array_merge($default_array,$files);
					}
				?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_CHOOSE_INFO_TEMPLATE_CATEGORIE; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_pull_down_menu('categories_template',$files,$default_value); ?>
					</td>
				</tr>

				<!-- CATEGORIES-ARTICLE-TEMPLATE -->
				<?php
					$files=array();					
					if ($dir= opendir(DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/')){
						while  (($file = readdir($dir)) !==false) {
							if (is_file( DIR_FS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/module/product_listing/'.$file) 
								&& $file !="index.html"
								&& (strpos($file, '-USERMOD.') === false || strpos($pInfo->listing_template, '-USERMOD.') !== false)) {
								$files[]=array(
												'id' => $file,
												'text' => $file
												);
							}
						}
						closedir($dir);
					}
					$default_array=array();
					// set default value in dropdown!
					if ($content['content_file']=='') {
						$default_array[]=array('id' => 'default','text' => TEXT_SELECT);
						$default_value=$cInfo->listing_template;
						$files=array_merge($default_array,$files);
					} else {
						$default_array[]=array('id' => 'default','text' => TEXT_NO_FILE);
						$default_value=$cInfo->listing_template;
						$files=array_merge($default_array,$files);
					}
				?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_CHOOSE_INFO_TEMPLATE_LISTING; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_pull_down_menu('listing_template',$files,$default_value); ?>
					</td>
				</tr>
				
				<!-- CATEGORIES-ARTICLE-SORTING -->
				<?php
					$order_array='';
					$order_array=array(array('id' => 'p.products_price','text'=>TXT_PRICES),
									   array('id' => 'pd.products_name','text'=>TXT_NAME),
									   array('id' => 'p.products_ordered','text'=>TXT_ORDERED),
									   array('id' => 'p.products_sort','text'=>TXT_SORT),
									   array('id' => 'p.products_weight','text'=>TXT_WEIGHT),
                                       array('id' => 'p.products_date_added','text'=>TXT_DATE_ADDED),
									   array('id' => 'p.products_quantity','text'=>TXT_QTY));
					$default_value='p.products_sort';
					if(isset($cInfo->products_sorting) && !empty($cInfo->products_sorting))
					{
						$default_value = $cInfo->products_sorting;
					}
				?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_EDIT_PRODUCT_SORT_ORDER; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_pull_down_menu('products_sorting',$order_array,$default_value); ?>
						<?php
							// BOF GM_MOD
							$order_array='';
							$order_array=array(array('id' => 'ASC','text'=> GM_SORT_ASC),
											   array('id' => 'DESC','text'=> GM_SORT_DESC));
							echo ' ' . xtc_draw_pull_down_menu('products_sorting2',$order_array,$cInfo->products_sorting2); 
							// EOF GM_MOD
						?>					
					</td>
				</tr>

				<!-- CATEGORIES-ARTICLE-SORT-ORDER -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_EDIT_SORT_ORDER; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_input_field('sort_order', $cInfo->sort_order, 'size="2"'); ?>									
					</td>
				</tr>

				<!-- CATEGORIES-SHOW-QTY-INFO -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_TEXT_SHOW_CAT_QTY_INFO; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('gm_show_qty_info', 'checkbox', '1',$cInfo->gm_show_qty_info==1 ? true : false); ?>									
					</td>
				</tr>
				
				<!-- CATEGORIES-SHOW-ATTRIBUTES -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_TEXT_SHOW_ATTRIBUTES; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('gm_show_attributes', 'checkbox', '1',$cInfo->gm_show_attributes==1 ? true : false); ?>									
					</td>
				</tr>

				<!-- CATEGORIES-SHOW-GRADUATED_PRICES -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_TEXT_SHOW_GRADUATED_PRICES; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('gm_show_graduated_prices', 'checkbox', '1',$cInfo->gm_show_graduated_prices==1 ? true : false); ?>									
					</td>
				</tr>

				<!-- CATEGORIES-SHOW-QTY -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_TEXT_SHOW_QTY; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('gm_show_qty', 'checkbox', '1',$cInfo->gm_show_qty==1 ? true : false); ?>									
					</td>
				</tr>
				
				<!-- CATEGORIES-GROUPCHECK -->
				<?php
					if (GROUP_CHECK=='true') {
						$customers_statuses_array = xtc_get_customers_statuses();
						$customers_statuses_array=array_merge(array(array('id'=>'all','text'=>TXT_ALL)),$customers_statuses_array);
				?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo ENTRY_CUSTOMERS_STATUS; ?>:
					</td>
					<td class="main" valign="top" align="left">
					<?php
						for ($i=0;$n=sizeof($customers_statuses_array),$i<$n;$i++) {
							if ($category['group_permission_'.$customers_statuses_array[$i]['id']] == 1) {
								$checked='checked ';
							} else {
								$checked='';
							}
							echo '<input type="checkbox" name="groups[]" value="'.$customers_statuses_array[$i]['id'].'"'.$checked.'> '.$customers_statuses_array[$i]['text'].'<br />';
						}
					?>
					<br /><input type="checkbox" name="set_groups_recursive" value="1" /> <?php echo SET_GROUPS_RECURSIVE; ?><br />
					</td>
				</tr>
				<?php
					}
				?>

				<!-- CATEGORIES-SITEMAP -->

				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_SITEMAP_ENTRY; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php 
							if($cInfo->gm_sitemap_entry == '1') {
								echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', true); 
							} else {
								echo xtc_draw_checkbox_field('gm_sitemap_entry', '1', false); 
							}			
						?>					
					</td>
				</tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_SITEMAP_PRIORITY; ?>:
					</td>
					<td class="main" valign="top" align="left">
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
						<?php echo xtc_draw_pull_down_menu('gm_priority', $gm_priority, $cInfo->gm_priority); ?>									
					</td>
				</tr>

				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_SITEMAP_CHANGEFREQ; ?>:
					</td>
					<td class="main" valign="top" align="left">
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
						<?php echo xtc_draw_pull_down_menu('gm_changefreq', $gm_changefreq, $cInfo->gm_changefreq); ?>									
					</td>
				</tr>

				<?php
				// BOF GM_MOD GX-Customizer:
				require_once('../gm/modules/gm_gprint_admin_new_category.php');
				?>

				<!-- SHOW SUB PRODUCTS -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_SHOW_SUB_PRODUCTS; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php
						if($cInfo->show_sub_products == '1') {
							echo xtc_draw_checkbox_field('show_sub_products', '1', true);
						} else {
							echo xtc_draw_checkbox_field('show_sub_products', '1', false);
						}
						?>
					</td>
				</tr>

				<!-- SHOW TILED ARTICLE LISTING -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_SHOW_TILED_LISTING; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php
						if($cInfo->view_mode_tiled == '1') {
							echo xtc_draw_checkbox_field('view_mode_tiled', '1', true);
						} else {
							echo xtc_draw_checkbox_field('view_mode_tiled', '1', false);
						}
						?>
					</td>
				</tr>

			</table>


			<table border="0" width="100%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
				<!-- SUB CATEGORIES -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_SHOW_SUB_CATEGORIES; ?>:
					</td>
					<td class="main" valign="top" align="left">
						<?php
						if($cInfo->show_sub_categories == '1' || !isset($cInfo->show_sub_categories)) {
							echo xtc_draw_checkbox_field('show_sub_categories', '1', true);
						} else {
							echo xtc_draw_checkbox_field('show_sub_categories', '1', false);
						}
						?>
					</td>
				</tr>
				<!-- SHOW SUB CATEGORIES IMAGES -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150" style="padding-left: 25px;">
						<?php echo TEXT_SHOW_SUB_CATEGORIES_IMAGES; ?>:
					</td>
					<td class="main" valign="top" align="left" style="padding-left: 3px;">
						<?php
						if($cInfo->show_sub_categories_images == '1' || !isset($cInfo->show_sub_categories)) {
							echo xtc_draw_checkbox_field('show_sub_categories_images', '1', true);
						} else {
							echo xtc_draw_checkbox_field('show_sub_categories_images', '1', false);
						}
						?>
					</td>
				</tr>

				<!-- SHOW SUB CATEGORIES NAMES -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150" style="padding-left: 25px;">
						<?php echo TEXT_SHOW_SUB_CATEGORIES_NAMES; ?>:
					</td>
					<td class="main" valign="top" align="left" style="padding-left: 3px;">
						<?php
						if($cInfo->show_sub_categories_names == '1' || !isset($cInfo->show_sub_categories)) {
							echo xtc_draw_checkbox_field('show_sub_categories_names', '1', true);
						} else {
							echo xtc_draw_checkbox_field('show_sub_categories_names', '1', false);
						}
						?>
					</td>
				</tr>


			</table>

			<?php
				$t_extender_output_array = $coo_admin_edit_category_extender_component->get_output('left');

				foreach($t_extender_output_array as $t_output_array)
				{
					$t_title = '';
					if(isset($t_output_array['title']) && empty($t_output_array['title']) == false)
					{
						$t_title = '<tr><td style="font-size:14px;font-weight:bold;height: 28px;">' . $t_output_array['title'] . '</td></tr>';
					}
					$t_content = '';
					if(isset($t_output_array['content']))
					{
						$t_content = $t_output_array['content'];
					}

					echo '<table border="0" width="100%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
							<tr>
								<td>
									<table class="main" border="0" width="100%">
										' . $t_title . '
										<tr>
											<td>' . $t_content . '</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
				}
			?>

      </div>

      <!-- CATEGORIES-FILTER -->
      <?php
	  if((int)$_GET['cID'] > 0)
	  {
		  ?>
		  <div style="width:49%;margin-right:10px;overflow:hidden;">
			
			<?php
				$t_extender_output_array = $coo_admin_edit_category_extender_component->get_output('right');

				foreach($t_extender_output_array as $t_output_array)
				{
					$t_title = '';
					if(isset($t_output_array['title']) && empty($t_output_array['title']) == false)
					{
						$t_title = '<tr><td style="font-size:14px;font-weight:bold;height: 28px;">' . $t_output_array['title'] . '</td></tr>';
					}
					$t_content = '';
					if(isset($t_output_array['content']))
					{
						$t_content = $t_output_array['content'];
					}

					echo '<table border="0" width="100%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
							<tr>
								<td>
									<table class="main" border="0" width="100%">
										' . $t_title . '
										<tr>
											<td>' . $t_content . '</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
				}
			?>
		  </div>
		  <!-- CATEGORIES-FILTER -->
	  <?php } ?>

      <!-- CATEGORIES-SLIDER -->
      <?php
      function generateCatSliderSelect()
      {
        global $cat_slider_array;
        $category_id = (int) $_GET['cID'];
        $cat_slider_handler = MainFactory::create_object('CategorySliderHandler');
        $cat_slider_id = $cat_slider_handler->get_category_slider_id($category_id);
        $cat_slider_handler = NULL;
        $html = '';
        $t_text_select_none = TEXT_SELECT_NONE;
        if (strpos($p_param_name, 'index')>0) $t_text_select_none = TEXT_SELECT_NONE_INDEX;
        $html .= '<select name="cat_slider" size="1" style="width:200px">'."";
        $html .= '<option value="0">'.$t_text_select_none.'</option>'."<br>\n";
        foreach ($cat_slider_array as $f_key => $coo_slider) {
          $t_slider_set_id = $coo_slider->v_slider_set_id;
          $t_slider_set_name = $coo_slider->v_slider_set_name;
          $t_mark  = ($t_slider_set_id == $cat_slider_id) ? ' selected="selected"' : '';
          $html .= '<option value="'.$t_slider_set_id.'"'.$t_mark.'>'.$t_slider_set_name.'</option>'."<br>\n";
        }
        $html .= '</select>'."";
        return $html;
      }

      $coo_cat_slider   = MainFactory::create_object('SliderControl');
      $cat_slider_array = $coo_cat_slider->get_slider_set_array();
      ?>
      <?php if (!empty($cat_slider_array)) { ?>
      <div style="width:49%;margin-right:10px;overflow:hidden;">
      <table border="0" width="100%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
        <tr>
					<td class="main strong" valign="top" align="left">
            <?php echo TITLE_CAT_SLIDER; ?>:<br><br>
            <?php echo generateCatSliderSelect(); ?>
            <input type="submit" name="save_slider" value="speichern" class="button">
          </td>
        </tr>
      </table>
      </div>
      <?php } ?>
      <!-- CATEGORIES-SLIDER -->

      <table border="0" width="99%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
				
				<?php for ($i=0; $i<sizeof($languages); $i++) { ?>
				
				<!-- CATEGORIES-DESCRIPTION -->
				
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php  echo TEXT_EDIT_CATEGORIES_DESCRIPTION; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php 
						echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); 
						echo xtc_draw_textarea_field('categories_description[' . $languages[$i]['id'] . ']', 'soft', '70', '25', (($categories_description[$languages[$i]['id']]) ? stripslashes($categories_description[$languages[$i]['id']]) : xtc_get_categories_description($cInfo->categories_id, $languages[$i]['id'])));

						if(USE_WYSIWYG == 'true')
						{
							echo xtc_wysiwyg('categories_description', $_SESSION['language_code'], 'categories_description[' . $languages[$i]['id'] . ']');
						}
						?>
					</td>
				</tr>				

				<?php
					}
				?>

			</table>
			<table border="0" width="99%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
				
				<?php for ($i=0; $i<sizeof($languages); $i++) { ?>				
				
				<!-- CATEGORIES-META -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php  echo TEXT_META_TITLE; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;
						<?php echo xtc_draw_input_field('categories_meta_title[' . $languages[$i]['id'] . ']',(($categories_meta_title[$languages[$i]['id']]) ? stripslashes($categories_meta_title[$languages[$i]['id']]) : xtc_get_categories_meta_title($cInfo->categories_id, $languages[$i]['id'])), 'size=50'); ?>
					</td>
				</tr>

				<!-- CATEGORIES-META-DESCRIPTION -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php  echo TEXT_META_DESCRIPTION; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;	
						<?php echo xtc_draw_input_field('categories_meta_description[' . $languages[$i]['id'] . ']', (($categories_meta_description[$languages[$i]['id']]) ? stripslashes($categories_meta_description[$languages[$i]['id']]) : xtc_get_categories_meta_description($cInfo->categories_id, $languages[$i]['id'])),'size=50'); ?>
					</td>
				</tr>

				<!-- CATEGORIES-META-KEYWORDS -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php  echo TEXT_META_KEYWORDS; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;	
						<?php echo xtc_draw_input_field('categories_meta_keywords[' . $languages[$i]['id'] . ']',(($categories_meta_keywords[$languages[$i]['id']]) ? stripslashes($categories_meta_keywords[$languages[$i]['id']]) : xtc_get_categories_meta_keywords($cInfo->categories_id, $languages[$i]['id'])),'size=50'); ?>
					</td>
				</tr>

				<!-- CATEGORIES-URL-KEYWORDS -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php  echo GM_TEXT_URL_KEYWORDS; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']); ?>&nbsp;	
						<?php echo xtc_draw_input_field('gm_url_keywords[' . $languages[$i]['id'] . ']',(($gm_url_keywords[$languages[$i]['id']]) ? stripslashes($gm_url_keywords[$languages[$i]['id']]) : gm_get_categories_url_keywords($cInfo->categories_id, $languages[$i]['id'])),'size=50'); ?>
					</td>
				</tr>
				<tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>

				<?php
					}
				?>

			</table>
	  
			<?php
				$t_extender_output_array = $coo_admin_edit_category_extender_component->get_output('bottom');

				foreach($t_extender_output_array as $t_output_array)
				{
					$t_title = '';
					if(isset($t_output_array['title']) && empty($t_output_array['title']) == false)
					{
						$t_title = '<tr><td style="font-size:14px;font-weight:bold;height: 28px;">' . $t_output_array['title'] . '</td></tr>';
					}
					$t_content = '';
					if(isset($t_output_array['content']))
					{
						$t_content = $t_output_array['content'];
					}

					echo '<table border="0" width="99%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">
							<tr>
								<td>
									<table class="main" border="0" width="100%">
										' . $t_title . '
										<tr>
											<td>' . $t_content . '</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>';
				}
			?>
	  
			<table border="0" width="99%" cellspacing="3" cellpadding="3" class="gm_border dataTableRow">

				<!-- CATEGORIES-ICON -->				
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_CATEGORIES_ICON; ?>
					</td>
					<td class="main" valign="top" align="left">
			<?php
				echo xtc_draw_file_field('categories_icon');
				echo xtc_draw_hidden_field('categories_previous_icon', $cInfo->categories_icon);
			?>
					</td>
				</tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_CATEGORIES_IMAGE_NAME; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_input_field('gm_categories_icon_name'); ?>
					</td>
				</tr>
			<?php
				if ($cInfo->categories_icon) {
					$imagesize = @getimagesize(DIR_FS_CATALOG_IMAGES.'categories/icons/'.$cInfo->categories_icon);
			?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_DELETE; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('del_cat_ico', 'checkbox', 'yes'); ?>
					</td>
				</tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						&nbsp;
					</td>
					<td class="main" valign="top" align="left">
						<img style="float:left" src="<?php echo DIR_WS_CATALOG.'images/categories/icons/'.$cInfo->categories_icon; ?>" <?php echo $imagesize[3]; ?>>
						&nbsp;
						<?php echo $cInfo->categories_icon; ?>
					</td>
				</tr>
			<?php
				}
			?>

				<!-- CATEGORIES-IMAGE -->
				<tr><td colspan="2"><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td></tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_CATEGORIES_IMAGE; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_file_field('categories_image') . '<br />' . xtc_draw_hidden_field('categories_previous_image', $cInfo->categories_image); ?>					
					</td>
				</tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_CATEGORIES_IMAGE_NAME; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_input_field('gm_categories_image_name'); ?>
					</td>
				</tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo GM_CATEGORIES_IMAGE_ALT_TEXT; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php 
								$gmAlt = new GMAltText(); 
								for($i=0; $i < count($languages); $i++) {
									
									echo '<div style="padding-bottom:10px">' . xtc_image(DIR_WS_LANGUAGES.$languages[$i]['directory'].'/admin/images/'.$languages[$i]['image']) . '&nbsp;' . xtc_draw_input_field('gm_categories_image_alt_text_' . $languages[$i]['id'], $gmAlt->get_cat_alt($_GET['cID'],$languages[$i]['id']));
									echo "</div>";
								}				
						?>
					</td>
				</tr>
		  <?php
            if ($cInfo->categories_image) {				
				$image_size = getimagesize(DIR_FS_CATALOG_IMAGES.'categories/'.$cInfo->categories_image);
            ?>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						<?php echo TEXT_DELETE; ?>
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_selection_field('del_cat_pic', 'checkbox', 'yes'); ?>
					</td>
				</tr>
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						&nbsp;
					</td>
					<td class="main" valign="top" align="left">
						<img style="float:left;" src="<?php echo DIR_WS_CATALOG.'images/categories/'.$cInfo->categories_image; ?>" <?php echo $image_size[3]; ?>>
						&nbsp;
						<?php echo $cInfo->categories_image; ?><a name="gm_anchor"></a>
						<?php if($_GET['gm_redirect']==1) echo GM_TITLE_REDIRECT; ?>
					</td>
				</tr>
			<?php
				}
			?>
				<!-- BUTTONS -->
				<tr>
					<td class="main strong" valign="top" align="left" width="150">
						&nbsp;
					</td>
					<td class="main" valign="top" align="left">
						<?php echo xtc_draw_hidden_field('categories_date_added', (($cInfo->date_added) ? $cInfo->date_added : date('Y-m-d'))) . xtc_draw_hidden_field('parent_id', $cInfo->parent_id); ?> 
						<?php echo xtc_draw_hidden_field('categories_id', $cInfo->categories_id); ?> 
						<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?> 
						
						<input class="button" type="submit" name="update_category" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')" style="float:right">
						<input class="button" type="button" value="<?php echo BUTTON_CANCEL; ?>" onclick="javascript:history.go(-1)" style="float:right">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
