<?php
/* --------------------------------------------------------------
   products_attributes.php 2016-03-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(products_attributes.php,v 1.48 2002/11/22); www.oscommerce.com 
   (c) 2003	 nextcommerce (products_attributes.php,v 1.10 2003/08/18); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: products_attributes.php 1155 2005-08-13 15:47:33Z matthias $)

   Released under the GNU General Public License 
   --------------------------------------------------------------*/

	require('includes/application_top.php');
	
	$languages = xtc_get_languages();
	
	if ($_GET['action'])
	{

		if(!empty($_GET['searchoption']))
		{
			$gm_searchoption = '&searchoption=' . rawurlencode($_GET['searchoption']);
		}

		if(!empty($_GET['search_optionsname']))
		{
			$gm_search_optionsname = '&search_optionsname=' . rawurlencode($_GET['search_optionsname']);
		}

		$page_info = 
						'option_page=' . 
						$_GET['option_page'] . 
						'&value_page=' . 
						$_GET['value_page'] . 
						'&attribute_page=' . 
						$_GET['attribute_page']	.
						$gm_searchoption .
						$gm_search_optionsname;


		switch($_GET['action']) 
		{
			case 'add_product_options':

				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) 
				{
					$option_name = $_POST['option_name'];
					
					xtc_db_query("
									INSERT INTO " . 
										TABLE_PRODUCTS_OPTIONS . " 
											(
												products_options_id, 
												products_options_name, 
												language_id
											) 
											VALUES 
											(
												'" . (int)$_POST['products_options_id'] . "', 
												'" . xtc_db_input($option_name[$languages[$i]['id']]) . "', 
												'" . (int)$languages[$i]['id'] . "'
											)
								");
				}
				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info)); 
			break;

			case 'add_product_option_values':

				// BOF GM_MOD
				$gm_filename = '';
				
				if ($gm_upload_file = & xtc_try_upload('gm_image_upload', DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/')) 
				{
					$gm_filename = $gm_upload_file->filename;
				}
				// EOF GM_MOD

				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) 
				{
					$value_name = $_POST['value_name'];

					xtc_db_query("
									INSERT INTO " . 
										TABLE_PRODUCTS_OPTIONS_VALUES . " 
											(
												products_options_values_id, 
												language_id, 
												products_options_values_name
											) 
										VALUES 
											(
												'" . $_POST['value_id'] . "', 
												'" . $languages[$i]['id'] . "', 
												'" . xtc_db_input($value_name[$languages[$i]['id']]) . "'
											)
								");
								
					// BOF GM_MOD
					if(($gm_filename))
					{
						$gm_get_last_id = xtc_db_query("
														SELECT 
															products_options_values_id 
																FROM " . 
															TABLE_PRODUCTS_OPTIONS_VALUES . " 
																ORDER BY 
																	products_options_values_id DESC 
																LIMIT 1
														");
						
						$gm_last_id = xtc_db_fetch_array($gm_get_last_id);
						
						xtc_db_query("
										UPDATE " . 
											TABLE_PRODUCTS_OPTIONS_VALUES . " 
										SET 
											gm_filename = 'ATTRIBUTE_" . $gm_last_id['products_options_values_id'] . strrchr($gm_filename, '.') . "' 
										WHERE 
											products_options_values_id = '" . $gm_last_id['products_options_values_id'] . "'
										");
					}
					// EOF GM_MOD
				}

				// BOF GM_MOD:
				if(!empty($gm_filename))
				{	
					rename(
							DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/' . $gm_filename,
							DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/ATTRIBUTE_' . $gm_last_id['products_options_values_id'] . strrchr($gm_filename, '.')
						);
				}
				
				xtc_db_query("
								INSERT INTO " . 
									TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " 
										(
											products_options_id, 
											products_options_values_id
										) 
									VALUES 
										(
											'" . (int)$_POST['option_id'] . "', 
											'" . (int)$_POST['value_id'] . "'
										)
							");

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));

				unset($gm_filename);
				// EOF GM_MOD

			break;

			case 'add_product_attributes':

				xtc_db_query("
								INSERT INTO " . 
									TABLE_PRODUCTS_ATTRIBUTES . " 
										VALUES 
											(
												'', 
												'" . (int)$_POST['products_id'] . "', 
												'" . (int)$_POST['options_id'] . "', 
												'" . (int)$_POST['values_id'] . "', 
												'" . xtc_db_input($_POST['value_price']) . "', 
												'" . xtc_db_input($_POST['price_prefix']) . "'
											)
							");
				
				$products_attributes_id = xtc_db_insert_id();
				
				if ((DOWNLOAD_ENABLED == 'true') && $_POST['products_attributes_filename'] != '') 
				{
					xtc_db_query("
									INSERT INTO " . 
										TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " 
											VALUES  
												(
													" . $products_attributes_id . ", 
													'" . xtc_db_input($_POST['products_attributes_filename']) . "', 
													'" . (int)$_POST['products_attributes_maxdays'] . "', 
													'" . (int)$_POST['products_attributes_maxcount'] . "'
												)
								");
				}

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));

			break;

			case 'update_option_name':

				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) 
				{
					$option_name = $_POST['option_name'];

					xtc_db_query("
									UPDATE " . 
										TABLE_PRODUCTS_OPTIONS . " 
									SET 
										products_options_name	= '" . xtc_db_input($option_name[$languages[$i]['id']]) . "' 
									WHERE
										products_options_id		= '" . (int)$_POST['option_id'] . "' 
									AND 
										language_id				= '" . (int)$languages[$i]['id'] . "'
								");

					// BOF GM_MOD
					if(
						xtc_db_num_rows(
							xtc_db_query("
										SELECT 
											* 
										FROM " . 
											TABLE_PRODUCTS_OPTIONS . " 
										WHERE 
											products_options_id = '" . (int)$_POST['option_id'] . "' 
										AND 
											language_id = '" . (int)$languages[$i]['id'] . "'
										")
						) == 0
					)
					{
						xtc_db_query("
										INSERT INTO " . 
											TABLE_PRODUCTS_OPTIONS . " 
												(
													products_options_id, 
													products_options_name, 
													language_id
												) 
											VALUES
												(
													'" . (int)$_POST['option_id'] . "', 
													'" . xtc_db_input($option_name[$languages[$i]['id']]) . "', 
													'" . (int)$languages[$i]['id'] . "'
												)
									");
					}
					// EOF GM_MOD
				}

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));

			break;

			case 'update_value':

				// BOF GM_MOD
				if($_POST['gm_delete_image'] == 1)
				{
					xtc_db_query("
								UPDATE " . 
									TABLE_PRODUCTS_OPTIONS_VALUES . " 
								SET 
									gm_filename = '' 
								WHERE 
									gm_filename = '" . xtc_db_input($_POST['gm_filename']) . "'
							");

					unlink(DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/'.$_POST['gm_filename']);
				}

				$gm_filename = '';
				if ($gm_upload_file = & xtc_try_upload('gm_image_upload', DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/'))
				{
					$gm_filename = $gm_upload_file->filename;
				}
				// EOF GM_MOD

				$value_name = $_POST['value_name'];

				for ($i = 0, $n = sizeof($languages); $i < $n; $i ++) 
				{
					xtc_db_query("
									UPDATE " . 
										TABLE_PRODUCTS_OPTIONS_VALUES . " 
									SET 
										products_options_values_name = '" . xtc_db_input($value_name[$languages[$i]['id']]) . "' 
									WHERE
										products_options_values_id = '" . (int)$_POST['value_id'] . "' 
									AND 
										language_id = '" . (int)$languages[$i]['id'] . "'
								");

					// BOF GM_MOD				
					if	
						(
							xtc_db_num_rows(
								xtc_db_query("
												SELECT 
													* 
												FROM " . 
													TABLE_PRODUCTS_OPTIONS_VALUES . " 
												WHERE 
													products_options_values_id = '" . (int)$_POST['value_id'] . "' 
												AND 
													language_id = '" . (int)$languages[$i]['id'] . "'
											")
							) == 0
						)
					{
						xtc_db_query("
										INSERT INTO " . 
											TABLE_PRODUCTS_OPTIONS_VALUES . " 
											(
												products_options_values_id, 
												language_id, 
												products_options_values_name
											) 
										VALUES
											(
												'" . (int)$_POST['value_id'] . "',
												'" . (int)$languages[$i]['id'] . "', 
												'" . xtc_db_input($value_name[$languages[$i]['id']]) . "'
											)
										");
					}

					if(!empty($gm_filename))
					{
						if($i == 0 && !empty($_POST['gm_filename']) && $_POST['gm_delete_image'] != 1)
						{
							unlink(DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/'.$_POST['gm_filename']);
						}

						xtc_db_query("
										UPDATE " . 
											TABLE_PRODUCTS_OPTIONS_VALUES . " 
										SET 
											gm_filename = 'ATTRIBUTE_" . (int)$_POST['value_id'] . strrchr($gm_filename, '.') . "' 
										WHERE 
											products_options_values_id = '" . (int)$_POST['value_id'] . "' 
										AND
											language_id = '" . (int)$languages[$i]['id'] . "'
									");
					}
				// EOF GM_MOD
				}

				//BOF GM_MOD:
				if(!empty($gm_filename))
				{
					rename(
							DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/' . $gm_filename,
							DIR_FS_CATALOG_IMAGES.'product_images/attribute_images/ATTRIBUTE_' . $_POST['value_id'] . strrchr($gm_filename, '.')
					);
				}

				xtc_db_query("
							UPDATE " . 
								TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " 
							SET 
								products_options_id = '" . (int)$_POST['option_id'] . "' 
							WHERE 
								products_options_values_id = '" . (int)$_POST['value_id'] . "'
							");
				
				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
				// EOF GM_MOD

				unset($gm_filename);

			break;

			case 'update_product_attribute':

				xtc_db_query("
								UPDATE " . 
									TABLE_PRODUCTS_ATTRIBUTES . " 
								SET 
									products_id = '" . (int)$_POST['products_id'] . "', 
									options_id = '" . (int)$_POST['options_id'] . "', 
									options_values_id = '" . (int)$_POST['values_id'] . "', 
									options_values_price = '" . xtc_db_input($_POST['value_price']) . "', 
									price_prefix = '" . xtc_db_input($_POST['price_prefix']) . "' 
								WHERE
									products_attributes_id = '" . (int)$_POST['attribute_id'] . "'
							");
				
				if ((DOWNLOAD_ENABLED == 'true') && $_POST['products_attributes_filename'] != '') 
				{
					xtc_db_query("
									UPDATE " . 
										TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " 
									SET
										products_attributes_filename='" . xtc_db_input($_POST['products_attributes_filename']) . "',
										products_attributes_maxdays='" . (int)$_POST['products_attributes_maxdays'] . "',
										products_attributes_maxcount='" . (int)$_POST['products_attributes_maxcount'] . "'
									WHERE
										products_attributes_id = '" . (int)$_POST['attribute_id'] . "'
								");
				}

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));

			break;

			case 'delete_option':

				$del_options = xtc_db_query(
											"
											SELECT
												products_options_values_id 
											FROM " . 
												TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . "
											WHERE
												products_options_id = '" . (int)$_GET['option_id'] . "'
											");
				
				while($del_options_values = xtc_db_fetch_array($del_options))
				{  
					xtc_db_query("
								DELETE 
								FROM " . 
									TABLE_PRODUCTS_OPTIONS_VALUES . " 
								WHERE 
									products_options_values_id = '" . (int)$del_options_values['products_options_values_id'] . "'
							");
				}

				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " 
								WHERE 
									products_options_id = '" . (int)$_GET['option_id'] . "'
							");
				
				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_OPTIONS . " 
								WHERE 
									products_options_id = '" . (int)$_GET['option_id'] . "'
							");

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));
			
			break;
			
			case 'delete_value':

				// BOF GM_MOD
				$gm_get_filename = xtc_db_query("
												SELECT 
													gm_filename 
												FROM " . 
													TABLE_PRODUCTS_OPTIONS_VALUES . " 
												WHERE
													products_options_values_id = '" . (int)$_GET['value_id'] . "' 
												LIMIT 1
											");
				
				if(xtc_db_num_rows($gm_get_filename) == 1)
				{
					$gm_filename = xtc_db_fetch_array($gm_get_filename);
					if(!empty($gm_filename['gm_filename']))
					{
						unlink(DIR_FS_CATALOG_IMAGES . 'product_images/attribute_images/' . $gm_filename['gm_filename']);
					}
				}
				// EOF GM_MOD
				
				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_OPTIONS_VALUES . " 
								WHERE 
									products_options_values_id = '" . (int)$_GET['value_id'] . "'
							");

				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_OPTIONS_VALUES . " 
								WHERE 
									products_options_values_id = '" . (int)$_GET['value_id'] . "'
							");

				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS . " 
								WHERE 
									products_options_values_id = '" . (int)$_GET['value_id'] . "'
							");

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));

			break;

			case 'delete_attribute':

				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_ATTRIBUTES . " 
								WHERE 
									products_attributes_id = '" . (int)$_GET['attribute_id'] . "'
							");

				xtc_db_query("
								DELETE 
								FROM " . 							
									TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " 
								WHERE 
									products_attributes_id = '" . (int)$_GET['attribute_id'] . "'
							");

				xtc_redirect(xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, $page_info));

			break;
		}
	}
?>

<!doctype html public "-//W3C//DTD HTML	4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
	<meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html;	charset=<?php echo $_SESSION['language_charset']; ?>"> 
    <title>
	<?php echo TITLE; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="html/assets/styles/legacy/stylesheet.css">
    <script type="text/javascript">
	<!--
	function go_option() 
	{
	    if (document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value != "none") 
	    {
		location = "<?php echo xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . ($_GET['option_page'] ? $_GET['option_page'] : 1));	?>&option_order_by="+document.option_order_by.selected.options[document.option_order_by.selected.selectedIndex].value;
	    }
	}
	//-->
    </script>
</head>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0" bgcolor="#FFFFFF" data-gx-extension="visibility_switcher" data-visibility_switcher-selections=".action-list">
    <!-- header	//-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->

    <!-- body //-->
    <table border="0" width="100%" cellspacing="2" cellpadding="2">
	<tr>
	    <td	class="columnLeft2" width="<?php echo BOX_WIDTH; ?>" valign="top">
		<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="1" cellpadding="1" class="columnLeft">
		    <!-- left_navigation //-->
		    <?php require(DIR_WS_INCLUDES . 'column_left.php');	?>
		    <!-- left_navigation_eof //-->
		</table>
	    </td>
	    
	    <!-- body_text //-->			
	    <td	class="boxCenter" width="100%" valign="top" data-gx-compatibility="dynamic_page_breakpoints" data-dynamic_page_breakpoints-large=".boxCenterWrapper">
		<div class="pageHeading" style="float:left; background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)">
		    <?php echo HEADING_TITLE; ?>
		</div>
		<br />

	    <table>
		    <tr>
			    <td class="dataTableHeadingContent">
				    <?php echo HEADING_TITLE; ?>
			    </td>
			    <td class="dataTableHeadingContent">
				    <a href="new_attributes.php"><?php echo BOX_ATTRIBUTES_MANAGER; ?></a>
			    </td>
		    </tr>
	    </table>
		    
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
		    <!-- options and values//-->
		    <tr>
				<td width="100%">
					
					<form class="gx-container" name="search"	action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>" method="GET">
						<?php echo TEXT_SEARCH;	?>
						<input type="text" name="searchoption" size="20" value="<?php echo htmlspecialchars_wrapper($_GET['searchoption']); ?>">
					</form>
					<form class="gx-container" name="option_order_by" action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>">
						<?php echo TEXT_SORT; ?>
						<select	name="selected"	onChange="go_option()">
							<option value="products_options_id"<?php if	($option_order_by == 'products_options_id') { echo ' SELECTED';	} ?>>
								<?php echo TEXT_OPTION_ID; ?>
							</option>
							<option value="products_options_name"<?php if ($option_order_by == 'products_options_name')	{ echo ' SELECTED'; } ?>>
								<?php echo TEXT_OPTION_NAME; ?>
							</option>
						</select>
					</form>
					
					<table	width="100%" border="0"	cellspacing="0"	cellpadding="0">
					<tr>
					    <td	valign="top" class="main" width="100%">
							<table width="100%" border="0" cellspacing="0" cellpadding="2" class="gx-container gx-compatibility-table">				    
<!-- OPTIONS BOF //-->
							<?php
							if ($_GET['action'] == 'delete_product_option')	
							{ 
								// delete product option
								$options = xtc_db_query("
														SELECT
															products_options_id, 
															products_options_name 
														FROM " . 
															TABLE_PRODUCTS_OPTIONS . " 
														WHERE
															products_options_id = '" . (int)$_GET['option_id'] . "' 
														AND 
															language_id = '" . (int)$_SESSION['languages_id'] . "'
														");

								$options_values = xtc_db_fetch_array($options);
							?>
							<!-- 							
								<tr>
									<td class="pageHeading">
										<?php 
											echo $options_values['products_options_name']; 
										?>
									</td>
									<td>
										&nbsp;
										<?php 
											echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/pixel_trans.gif', '', '1', '53'); ?>
										&nbsp;
									</td>
								</tr>
							-->
								<tr class="no-hover">
									<td>
										<table border="0" width="100%" cellspacing="0" cellpadding="2" class="normalize-table">
											<?php
												$products = xtc_db_query("select p.products_id,	pd.products_name, pov.products_options_values_name from	" . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_OPTIONS_VALUES . "	pov, " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_DESCRIPTION . " pd where pd.products_id =	p.products_id and pov.language_id = '" . (int)$_SESSION['languages_id'] . "'	and pd.language_id = '"	. (int)$_SESSION['languages_id'] . "' and pa.products_id = p.products_id and	pa.options_id='" . (int)$_GET['option_id'] .	"' and pov.products_options_values_id =	pa.options_values_id order by pd.products_name");												
												
												if (xtc_db_num_rows($products))	
												{
											?>
											<tr class="dataTableHeadingRow no-hover">
												<td	class="dataTableHeadingContent"	align="center">
													&nbsp;
													<?php 
														echo TABLE_HEADING_ID; 
													?>
													&nbsp;
												</td>
												<td	class="dataTableHeadingContent">
													&nbsp;
													<?php 
														echo TABLE_HEADING_PRODUCT;	
													?>
													&nbsp;
												</td>
												<td	class="dataTableHeadingContent">
													&nbsp;
													<?php 
														echo TABLE_HEADING_OPT_VALUE; 
													?>
													&nbsp;
												</td>
											</tr>
											<?php
											while ($products_values	= xtc_db_fetch_array($products)) 
											{
												$rows++;
											?>
											<tr class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
												<td	align="center" style="padding:9px">
													
													&nbsp;
													<?php 
														echo $products_values['products_id']; 
													?>
													&nbsp;
												</td>
												<td style="padding:9px">
													&nbsp;
													<?php 
														echo $products_values['products_name']; 
													?>
													&nbsp;
												</td>
												<td style="padding:9px">
													&nbsp;
													<?php 
														echo $products_values['products_options_values_name']; 
													?>
													&nbsp;
												</td>
											</tr>
											<?php
											}
											?>
											<tr class="no-hover">
												<td	colspan="3" class="main">
													<br />
													<strong>
													<?php 
														echo $options_values['products_options_name'] . ":</strong> " .  TEXT_WARNING_OF_DELETE; 
													?>
												</td>
											</tr>
											<tr class="no-hover">
												<td	align="left" colspan="3" class="main">
													<br />
													<?php 
														echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&value_page=' . $_GET['value_page']	. '&attribute_page=' . $attribute_page,	'NONSSL'), 'style="float:right"');
													?>
													&nbsp;
												</td>
											</tr>
											<?php
											} else {
											?>
											<tr class="no-hover">
												<td	class="main" colspan="3">
													<strong>
													<?php 
														echo $options_values['products_options_name'] . ":</strong> " . TEXT_OK_TO_DELETE; 
													?>
												</td>
											</tr>
											<tr class="no-hover">
												<td	class="main" align="left" colspan="3">
													<br />
													<?php 
														echo xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option&option_id=' . $_GET['option_id'], 'NONSSL'), 'style="float:right; margin:5px"', 'btn-primary');
													?>
													<?php 
														echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES,	'&order_by=' . $order_by . '&page=' . $page, 'NONSSL'), 'style="float:right; margin:5px"');
													?>
												</td>
											</tr>
										<?php
										}
										?>
										</table>
									</td>
								</tr>
								<?php
								} else {
									if ($_GET['option_order_by']) 
									{
										$option_order_by = xtc_db_prepare_input($_GET['option_order_by']);
									} 
									else 
									{
										$option_order_by = 'products_options_id';
									}
								?>
								<tr class="no-hover">
									<td colspan="3" class="smallText">
									<?php

										$option_page = (int)$_GET['option_page'];
										
										$per_page =	MAX_ROW_LISTS_OPTIONS;

										if (isset ($_GET['searchoption'])) 
										{
											$options = "SELECT
															* 
														FROM " . 
															TABLE_PRODUCTS_OPTIONS." 
														WHERE
															language_id = '" . (int)$_SESSION['languages_id'] . "'	
														AND
															(
																products_options_name LIKE '%" . xtc_db_input(trim($_GET['searchoption'])) . "%'
																OR 
																products_options_id = '" . xtc_db_input(trim($_GET['searchoption'])) . "'
															)
														ORDER BY " . 
															$option_order_by;
										} 
										elseif($_GET['action'] == "update_option" && !empty($_GET['searchoption']))			
										{
											$options = "SELECT
															* 
														FROM " . 
															TABLE_PRODUCTS_OPTIONS." 
														WHERE
															language_id =	'".(int)$_SESSION['languages_id']."'
														AND
															products_options_id = '" . (int)$_GET['option_id']. "'";
										}
										else 
										{
											$options = "SELECT
															* 
														FROM " . 
															TABLE_PRODUCTS_OPTIONS." 
														WHERE
															language_id =	'".(int)$_SESSION['languages_id']."'
														ORDER BY " . 
															$option_order_by;
										}

										if(empty($_GET['searchoption']))
										{
											if (!$option_page) 
											{
												$option_page = 1;
											}

											$prev_option_page =	$option_page - 1;
											$next_option_page =	$option_page + 1;									
											$option_query = xtc_db_query($options);
										
											$option_page_start = ($per_page * $option_page) - $per_page;
											$num_rows =	xtc_db_num_rows($option_query);
										
											if ($num_rows <= $per_page)	
											{
												$num_pages = 1;
											} 
											else if (($num_rows % $per_page) == 0) 
											{
												$num_pages = ($num_rows	/ $per_page);
											} 
											else 
											{
												$num_pages = ($num_rows	/ $per_page) + 1;
											}
											
											$num_pages = (int) $num_pages;
										
											$options = $options	. " LIMIT $option_page_start, $per_page";
										
											// Previous
											if ($prev_option_page)
											{
												echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' .	$prev_option_page.'&searchoption='.rawurlencode($_GET['searchoption'])) . '">	&lt;&lt; </a> |	';
											}
										
											for	($i = 1; $i <= $num_pages; $i++) 
											{
												if ($i != $option_page)	
												{
													echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . $i.'&searchoption='.rawurlencode($_GET['searchoption'])) . '">' .	$i . '</a> | ';
												} 
												else 
												{
													echo '<b><font color=red>' . $i . '</font></b> | ';
												}
											}
										
											// Next
											if ($option_page !=	$num_pages) 
											{
												echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' .	$next_option_page.'&searchoption='.rawurlencode($_GET['searchoption'])) . '">	&gt;&gt; </a>';
											}
										}
									?>
									</td>
								</tr>
								<tr	class="dataTableHeadingRow">
									<td class="dataTableHeadingContent">
										&nbsp;
										<?php 
											echo TABLE_HEADING_ID; 
										?>
										&nbsp;
									</td>
									<td class="dataTableHeadingContent">
										&nbsp;
										<?php 
											echo TABLE_HEADING_OPT_NAME; 
										?>
										&nbsp;
									</td>
									<td class="dataTableHeadingContent" align="center">
										&nbsp;
										<?php 
											echo TABLE_HEADING_ACTION; 
										?>
										&nbsp;
									</td>
								</tr>
								<?php
								$next_id = 1;

								$t_sql = "SELECT COUNT(*) AS cnt FROM " . TABLE_PRODUCTS_OPTIONS;
								$t_result = xtc_db_query($t_sql);
								$t_result_array = xtc_db_fetch_array($t_result);
								if((int)$t_result_array['cnt'] > 0)
								{
									$max_options_id_query = xtc_db_query("select max(products_options_id) + 1 as next_id from "	. TABLE_PRODUCTS_OPTIONS);
									$max_options_id_values = xtc_db_fetch_array($max_options_id_query);
									$next_id = $max_options_id_values['next_id'];
								}

								$options = xtc_db_query($options);
								
								while ($options_values = xtc_db_fetch_array($options)) 
								{
									$rows++;
								?>
								<tr class="visibility_switcher <?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd');	?>">
								<?php
									if (($_GET['action'] ==	'update_option') && ($_GET['option_id']	== $options_values['products_options_id'])) 
									{
										if(!empty($_GET['searchoption']))
										{
											$gm_searchoption = '&searchoption=' . rawurlencode($_GET['searchoption']);
										}
										echo '<form	name="option" action="'	. xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_name&option_page='.$_GET['option_page'].$gm_searchoption, 'NONSSL') . '" method="post">';
										$inputs = '';
										
										for	($i = 0, $n = sizeof($languages); $i < $n; $i ++) 
										{
											$option_name = xtc_db_query("select products_options_name from " . TABLE_PRODUCTS_OPTIONS . " where products_options_id	= '" . (int)$options_values['products_options_id'] .	"' and language_id = '"	. (int)$languages[$i]['id'] . "'");

											$option_name = xtc_db_fetch_array($option_name);
											$inputs	.= $languages[$i]['code'] . ':&nbsp;<input type="text" name="option_name[' . $languages[$i]['id'] . ']"	size="20" value="' . $option_name['products_options_name'] . '" style="padding:5px; margin:5px">&nbsp;<br />';
										}
								?>
									<td align="center">
										&nbsp;
										<?php echo $options_values['products_options_id']; ?>
										<input type="hidden" name="option_id" value="<?php echo	$options_values['products_options_id'];	?>">
										&nbsp;
									</td>
									<td>
										<?php 
											echo $inputs; 
										?>
									</td>
									<td align="center">
										&nbsp;
										<?php 
											echo xtc_button(BUTTON_UPDATE, 'submit', 'style="float:right; margin:5px"', 'btn-primary');									
										?>
										<?php 
											echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_page=' . $_GET['option_page'], 'NONSSL'), 'style="float:right; margin:5px"');
										?>
									</td>
								<?php
										echo '</form>' . "\n";
									} else {
								?>
									<td align="center">
										&nbsp;
										<?php echo $options_values["products_options_id"]; ?>
										&nbsp;
									</td>									
									<td>
										&nbsp;
										<?php 
											echo $options_values["products_options_name"]; 
										?>
										&nbsp;
									</td>
									<td align="center">
										<div class="action-list add-margin-right-24">
											<?php
											if(!empty($_GET['searchoption']))
											{
												$gm_searchoption = '&searchoption=' . rawurlencode($_GET['searchoption']);
											}
											$href = xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option&option_id=' . $options_values['products_options_id'] . '&option_order_by='	. $option_order_by . '&option_page=' . $option_page . $gm_searchoption, 'NONSSL');


											echo '
													<a href="' . $href . '" class="action-icon">
														<i class="fa fa-pencil"></i>
													</a>
												';
											?>
											
											<?php
											$href = xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_product_option&option_id=' . $options_values['products_options_id'], 'NONSSL');

											echo '
													<a href="' . $href . '" class="action-icon">
														<i class="fa fa-trash-o"></i>
													</a>
												';
											?>
										</div>
									</td>								
								<?php
									}
								?>
								</tr>
								<?php									
							}
							?>
							<?php
								if ($_GET['action'] != 'update_option')	
								{
							?>
								<tr	class="no-hover <?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd');	?>">
							<?php
									echo '<form name="options" action="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_options&option_page=' . $option_page, 'NONSSL') . '" method="post"><input type="hidden" name="products_options_id" value="' . $next_id .	'">';
									$inputs	= '';
									for ($i	= 0, $n	= sizeof($languages); $i < $n; $i ++) 
									{
										$inputs .= $languages[$i]['code'] .	':&nbsp;<input type="text" name="option_name[' . $languages[$i]['id'] .	']" size="20" style="padding:5px; margin:5px">&nbsp;<br	/>';
									}
							?>
									<td align="center">
										&nbsp;
										<?php 
											echo $next_id;	
										?>
										&nbsp;
									</td>
									<td>
										<?php 
											echo $inputs; 
										?>
									</td>
									<td align="center">
										&nbsp;
										<?php 
											echo xtc_button(BUTTON_INSERT, 'submit', 'style="float:right; margin:5px"'); 
										?>
										&nbsp;
									</td>
									<?php
										echo '</form>';
									?>
								</tr>
								<?php
								}
							}
							?>
							</table>
						</td>
<!-- OPTIONS EOF //-->
					</tr>
					<tr>
					<td valign="top" width="100%" class="main">
						<table width="100%" border="0" cellspacing="0" cellpadding="2" class="gx-container gx-compatibility-table">
<!-- OPTION VALUE BOF //-->
						<?php
						if ($_GET['action'] == 'delete_option_value') 
						{	
							// delete product option value
							$values = xtc_db_query("select products_options_values_id, products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id	= '" . (int)$_GET['value_id'] . "' and language_id =	'" . (int)$_SESSION['languages_id'] . "'");
							$values_values = xtc_db_fetch_array($values);
						?>
							<tr class="no-hover">
								<td	colspan="3">
									<div class="pageHeading gx-container">
										<div class="page-header"><?php echo HEADING_TITLE_VAL; ?></div> 
										<?php 
											echo $values_values['products_options_values_name']; 
										?>
									</div>
								</td>
								<td>
									&nbsp;
									<?php 
										echo xtc_image(DIR_WS_ADMIN . 'html/assets/images/legacy/pixel_trans.gif', '', '1', '53'); 
									?>
									&nbsp;
								</td>
							</tr>
							<tr class="no-hover">
								<td>
									<table border="0" width="100%" cellspacing="0" cellpadding="2">
										<?php
											$products =	xtc_db_query("select p.products_id, pd.products_name, po.products_options_name from " .	TABLE_PRODUCTS . " p, "	. TABLE_PRODUCTS_ATTRIBUTES . "	pa, " .	TABLE_PRODUCTS_OPTIONS . " po, " . TABLE_PRODUCTS_DESCRIPTION .	" pd where pd.products_id = p.products_id and pd.language_id = '" . (int)$_SESSION['languages_id'] .	"' and po.language_id =	'" . (int)$_SESSION['languages_id'] . "' and	pa.products_id = p.products_id and pa.options_values_id='" . (int)$_GET['value_id'] . "' and	po.products_options_id = pa.options_id order by	pd.products_name");
											if (xtc_db_num_rows($products)) 
											{
										?>
										<tr	class="dataTableHeadingRow">
											<td class="dataTableHeadingContent" align="center">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
											<td class="dataTableHeadingContent">&nbsp;<?php	echo TABLE_HEADING_PRODUCT; ?>&nbsp;</td>
											<td class="dataTableHeadingContent">&nbsp;<?php	echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
										</tr>
										<?php
											while ($products_values = xtc_db_fetch_array($products))
											{
												$rows++;
										?>
										<tr	class="<?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd');	?>">
											<td align="center">&nbsp;<?php echo $products_values['products_id']; ?>&nbsp;</td>
											<td>&nbsp;<?php echo $products_values['products_name']; ?>&nbsp;</td>
											<td>&nbsp;<?php echo $products_values['products_options_name']; ?>&nbsp;</td>
										</tr>
										<?php
											}
										?>
										<tr class="no-hover">
											<td class="main" colspan="3"><br /><?php echo TEXT_WARNING_OF_DELETE; ?></td>
										</tr>
										<tr class="no-hover">
											<td class="main" align="left" colspan="3">
												<br />
												<?php 
													echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&value_page=' .	$_GET['value_page'] . '&attribute_page=' . $attribute_page, 'NONSSL'), 'style="float:right;"');
												?>
											</td>
										</tr>
										<?php
											} else {
										?>
										<tr>
											<td class="main" colspan="3">
												<br />
												<?php 
													echo TEXT_OK_TO_DELETE; 
												?>
											</td>
										</tr>
										<tr>
											<td class="main" align="left" colspan="3">
												<br />
												<?php 
													echo xtc_button_link(BUTTON_DELETE, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_value&value_id=' . $_GET['value_id'], 'NONSSL'), 'style="float:right; margin:5px"', 'btn-primary');
												?>
												<?php 
													echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, '&option_page=' . $option_page . '&value_page=' . $_GET['value_page']	. '&attribute_page=' . $attribute_page,	'NONSSL'), 'style="float:right; margin:5px"');
												?>
											</td>
										</tr>
										<?php
											}
										?>
									</table>
								</td>
							</tr>
							<?php
								} else {
							?>
							<tr class="no-hover">
								<td	colspan="5">
									<br />
									<div class="pageHeading gx-container">
										<div class="page-header"><?php echo HEADING_TITLE_VAL; ?></div>
									</div>
								</td>
							</tr>
							<tr class="no-hover">
								<td	colspan="5" class="smallText">
									
									<form class="gx-container" name="search"	action="<?php echo FILENAME_PRODUCTS_ATTRIBUTES; ?>" method="GET">
										<?php echo TEXT_SEARCH;	?> <input type="text" name="search_optionsname"	size="20" value="<?php echo htmlspecialchars_wrapper($_GET['search_optionsname']);?>">
									</form>
									<br />
									<br />
									
								<?php
									
									$per_page = MAX_ROW_LISTS_OPTIONS;
									
									// BOF GM_MOD:
									if (!empty ($_GET['search_optionsname'])) 
									{
										$values = "
													SELECT 
													DISTINCT
														pov.products_options_values_id, 
														pov.products_options_values_name, 
														pov2po.products_options_id 
													FROM " . 
														TABLE_PRODUCTS_OPTIONS . " po, " . 
														TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
													LEFT JOIN " . 
														TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." pov2po 
													ON
														pov.products_options_values_id = pov2po.products_options_values_id 
													WHERE 
														pov.language_id = '" . (int)$_SESSION['languages_id'] . "'	
													AND
														pov2po.products_options_id = po.products_options_id
													AND 
														(
															po.products_options_name LIKE '%" . xtc_db_input(trim($_GET['search_optionsname'])) . "%' 
														OR 
															pov.products_options_values_name LIKE '%" . xtc_db_input(trim($_GET['search_optionsname'])) . "%'
														OR
															pov.products_options_values_id = '" . xtc_db_input(trim($_GET['search_optionsname'])) . "'
														)
													ORDER BY
														pov.products_options_values_id";
									} 
									elseif($_GET['action'] == 'update_option_value' && !empty($_GET['search_optionsname']))
									{										
										$values = "
												SELECT
													pov.products_options_values_id, 
													pov.products_options_values_name, 
													pov2po.products_options_id 
												FROM " . 
													TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
												LEFT JOIN " . 
													TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." pov2po 
												ON 
													pov.products_options_values_id = pov2po.products_options_values_id 
												WHERE
													pov.language_id = '" . (int)$_SESSION['languages_id'] . "'	
												AND
													pov.products_options_values_id = '" . (int)trim($_GET['value_id']) . "'
												ORDER BY
													pov.products_options_values_id";
									}
									else 
									{
										$values = "
												SELECT
													pov.products_options_values_id, 
													pov.products_options_values_name, 
													pov2po.products_options_id 
												FROM " . 
													TABLE_PRODUCTS_OPTIONS_VALUES . " pov 
												LEFT JOIN " . 
													TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS." pov2po 
												ON 
													pov.products_options_values_id = pov2po.products_options_values_id 
												WHERE
													pov.language_id = '" . (int)$_SESSION['languages_id'] . "'	
												ORDER BY
													pov.products_options_values_id";
									}
									
									if(empty($_GET['search_optionsname']))
									{
										
										if (!$_GET['value_page']) 
										{
											$_GET['value_page']	= 1;
										}

										$prev_value_page = $_GET['value_page'] - 1;
										$next_value_page = $_GET['value_page'] + 1;
										
										$value_query = xtc_db_query($values);
										
										$value_page_start = ($per_page * $_GET['value_page']) -	$per_page;
										$num_rows = xtc_db_num_rows($value_query);
										
										if ($num_rows <= $per_page) {
											$num_pages = 1;
										} else if (($num_rows %	$per_page) == 0) {
											$num_pages = ($num_rows / $per_page);
										} else {
											$num_pages = ($num_rows / $per_page) + 1;
										}
										$num_pages = (int) $num_pages;
										
										$values	= $values . " LIMIT $value_page_start, $per_page";
										
										// Previous
										if ($prev_value_page)  
										{
											echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' .	$option_order_by . '&value_page=' . $prev_value_page.'&search_optionsname='. rawurlencode($_GET['search_optionsname'])) . '"> &lt;&lt;	</a> | ';
										}
										
										for ($i	= 1; $i	<= $num_pages; $i++) 
										{
											if ($i != $_GET['value_page']) 
											{
												echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' . $option_order_by . '&value_page=' .	$i.'&search_optionsname='.rawurlencode($_GET['search_optionsname'])) . '">' .	$i . '</a> | ';
											} 
											else 
											{
												echo '<b><font color=red>' . $i	. '</font></b> | ';
											}
										}
										
										// Next
										if ($_GET['value_page']	!= $num_pages) 
										{
											echo '<a href="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'option_order_by=' .	$option_order_by . '&value_page=' . $next_value_page.'&search_optionsname='.rawurlencode($_GET['search_optionsname'])) . '"> &gt;&gt;</a> ';
										}
									}
								?>
								</td>
							</tr>
							<tr class="dataTableHeadingRow">
								<td	class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_ID; ?>&nbsp;</td>
								<td	class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_NAME; ?>&nbsp;</td>
								<td	class="dataTableHeadingContent">&nbsp;<?php echo TABLE_HEADING_OPT_VALUE; ?>&nbsp;</td>
								<?php // BOF GM_MOD	?>
								<td	class="dataTableHeadingContent">&nbsp;<?php echo GM_ATTRIBUTES_IMAGE_UPLOAD_IMAGE; ?>&nbsp;</td>
								<?php // EOF GM_MOD	?>
								<td	class="dataTableHeadingContent"	align="center">&nbsp;<?php echo	TABLE_HEADING_ACTION; ?>&nbsp;</td>
							</tr>
							<?php
								$next_id = 1;

								$t_sql = "SELECT COUNT(*) AS cnt FROM " . TABLE_PRODUCTS_OPTIONS_VALUES;
								$t_result = xtc_db_query($t_sql);
								$t_result_array = xtc_db_fetch_array($t_result);
								if((int)$t_result_array['cnt'] > 0)
								{
									$max_values_id_query = xtc_db_query("select max(products_options_values_id) + 1	as next_id from	" . TABLE_PRODUCTS_OPTIONS_VALUES);
									$max_values_id_values =	xtc_db_fetch_array($max_values_id_query);
									$next_id = $max_values_id_values['next_id'];
								}

								$values	= xtc_db_query($values);
								while ($values_values =	xtc_db_fetch_array($values)) 
								{
									$options_name = xtc_options_name($values_values['products_options_id']);
									$values_name = $values_values['products_options_values_name'];
									$rows++;
							?>
							<tr class="visibility_switcher <?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
							<?php
								if (($_GET['action'] == 'update_option_value') && ($_GET['value_id'] == $values_values['products_options_values_id']))
								{
								
								// BOF GM_MOD:
								if(!empty($_GET['search_optionsname']))
								{
									$gm_search_optionsname = '&search_optionsname=' . rawurlencode($_GET['search_optionsname']);
								}
								echo '<form name="values" action="' . xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_value&value_page='.$_GET['value_page'] . $gm_search_optionsname, 'NONSSL') . '"	method="post" enctype="multipart/form-data">';
								$inputs	= '';
								for ($i	= 0, $n	= sizeof($languages); $i < $n; $i ++) 
								{
									$value_name	= xtc_db_query("select products_options_values_name from " . TABLE_PRODUCTS_OPTIONS_VALUES . " where products_options_values_id	= '" . (int)$values_values['products_options_values_id'] . "' and language_id = '" .	(int)$languages[$i]['id'] . "'");
									$value_name	= xtc_db_fetch_array($value_name);
									$inputs .= $languages[$i]['code'] .	':&nbsp;<input type="text" name="value_name[' .	$languages[$i]['id'] . ']" size="15" value="' .	$value_name['products_options_values_name'] . '" style="padding:5px; margin:5px">&nbsp;<br />';
								}
							?>
								<td	align="center">
									&nbsp;
									<?php echo $values_values['products_options_values_id']; ?>
									<input type="hidden" name="value_id" value="<?php echo $values_values['products_options_values_id']; ?>">
									&nbsp;
								</td>
								<td	align="center">
									&nbsp;
									<?php echo "\n"; ?>
									<select name="option_id" style="padding:5px; margin:5px">
									<?php
										$options = xtc_db_query("select products_options_id, products_options_name from " .	TABLE_PRODUCTS_OPTIONS . " where language_id = '" . $_SESSION['languages_id'] .	"' order by products_options_name");
										while ($options_values = xtc_db_fetch_array($options)) {
										echo "\n" . '<option name="' . $options_values['products_options_name']	. '" value="' .	$options_values['products_options_id'] . '"';
										if ($values_values['products_options_id'] == $options_values['products_options_id']) { 
											echo ' selected';
										}
										echo '>' . $options_values['products_options_name'] . '</option>';
										} 
									?>
									</select>
									&nbsp;
								</td>
								<td>
								<?php 
									echo $inputs; 
								?>
								</td>
								<?php
									// BOF GM_MOD
									$gm_filename = array();
									$gm_get_filename = xtc_db_query("SELECT gm_filename	FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE products_options_values_id = '" . (int)$_GET['value_id'] . "' LIMIT	1");
									if(xtc_db_num_rows($gm_get_filename) == 1) $gm_filename = xtc_db_fetch_array($gm_get_filename);
								?>
								<td>
									&nbsp;
									<?php 
										if(!empty($gm_filename['gm_filename'])) echo '<a href="'.DIR_WS_CATALOG_IMAGES.'product_images/attribute_images/'.$gm_filename['gm_filename'].'" target="_blank"><img src="'.DIR_WS_CATALOG_IMAGES.'product_images/attribute_images/'.$gm_filename['gm_filename'].'" border="0" width="80" /></a>'; ?> <input type="file"	name="gm_image_upload" />&nbsp;<?php if(!empty($gm_filename['gm_filename'])) echo '<br /><input	type="checkbox"	name="gm_delete_image" value="1"/ >' . GM_ATTRIBUTES_IMAGE_UPLOAD_DELETE . '?<input type="hidden" name="gm_filename" value="' .	$gm_filename['gm_filename'] . '" />'; ?></td>
									<?php // BOF GM_MOD	?>								
									<td	align="center">
									&nbsp;
									<?php echo xtc_button(BUTTON_UPDATE, 'submit', 'style="float:right; margin:5px"', 'btn-primary'); ?>
									&nbsp;
									<?php echo xtc_button_link(BUTTON_CANCEL, xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'value_page='.$_GET['value_page'], 'NONSSL'), 'style="float:right; margin:5px"'); 
									?>
									&nbsp;
								</td>
								<?php
									echo '</form>';
									} else {
								?>
								<td	align="center">
									&nbsp;
									<?php 
										echo $values_values["products_options_values_id"];	
									?>
									&nbsp;
								</td>
								<td	align="center">
									&nbsp;
									<?php 
										echo $options_name; 
									?>
									&nbsp;
								</td>
								<td>
									&nbsp;
									<?php 
										echo $values_name; 
									?>
									&nbsp;
								</td>
								<?php
									// BOF GM_MOD
									$gm_filename = array();
									$gm_get_filename = xtc_db_query("SELECT gm_filename	FROM " . TABLE_PRODUCTS_OPTIONS_VALUES . " WHERE products_options_values_id = '" . (int)$values_values["products_options_values_id"]	. "' LIMIT 1");
									if(xtc_db_num_rows($gm_get_filename) == 1) $gm_filename = xtc_db_fetch_array($gm_get_filename);
								?>
								<td>
									&nbsp;
									<?php 
										if(!empty($gm_filename['gm_filename'])) echo '<a href="'.DIR_WS_CATALOG_IMAGES.'product_images/attribute_images/'.$gm_filename['gm_filename'].'" target="_blank"><img src="'.DIR_WS_CATALOG_IMAGES.'product_images/attribute_images/'.$gm_filename['gm_filename'].'" border="0" width="80" /></a>'; 
									?>
									&nbsp;
								</td>
								<?php 
									// EOF GM_MOD	
								?>		  
								<td	align="center">
									<div class="action-list add-margin-right-24">
										<?php
										if(!empty($_GET['search_optionsname']))
										{
											$gm_search_optionsname = '&search_optionsname=' . rawurlencode($_GET['search_optionsname']);
										}
										$href = xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=update_option_value&value_id=' 
						                                                    . $values_values['products_options_values_id'] . '&value_page=' 
						                                                    . $_GET['value_page'] . $gm_search_optionsname, 'NONSSL');

										echo '
													<a href="' . $href . '" class="action-icon">
														<i class="fa fa-pencil"></i>
													</a>
												';
										?>

										<?php
										$href = xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=delete_option_value&value_id='	
					                                                    . $values_values['products_options_values_id'], 'NONSSL');

										echo '
													<a href="' . $href . '" class="action-icon">
														<i class="fa fa-trash-o"></i>
													</a>
												';
										?>
									</div>
								</td>
								<?php
									}
									}
								?>
							</tr>
							<?php
								if ($_GET['action'] != 'update_option_value') {
							?>
							<tr class="no-hover <?php echo (floor($rows/2) == ($rows/2) ? 'attributes-even' : 'attributes-odd'); ?>">
							<?php
								// BOF GM_MOD:
								echo '<form	name="values" action="'	. xtc_href_link(FILENAME_PRODUCTS_ATTRIBUTES, 'action=add_product_option_values&value_page=' . $_GET['value_page'], 'NONSSL') .	'" method="post" enctype="multipart/form-data">';
							?>
								<td	align="center">
									&nbsp;
									<?php 
										echo $next_id; 
									?>
									&nbsp;
								</td>
								<td	align="center">
									&nbsp;
									<select name="option_id" style="padding:5px; margin:5px">
										<?php
										$options = xtc_db_query("select products_options_id, products_options_name from " .	TABLE_PRODUCTS_OPTIONS . " where language_id = '" . (int)$_SESSION['languages_id'] .	"' order by products_options_name");
										while ($options_values = xtc_db_fetch_array($options)) {
										echo '<option name="' .	$options_values['products_options_name'] . '" value="' . $options_values['products_options_id']	. '">' . $options_values['products_options_name'] . '</option>';
										}
									
										$inputs = '';
										for	($i = 0, $n = sizeof($languages); $i < $n; $i ++) {
										$inputs	.= $languages[$i]['code'] . ':&nbsp;<input type="text" name="value_name[' . $languages[$i]['id'] . ']" size="15" style="padding:5px; margin:5px">&nbsp;<br />';
										}
										?>
									</select>
									&nbsp;
								</td>
								<td>
									<input type="hidden" name="value_id" value="<?php echo $next_id; ?>">
									<?php 
									echo $inputs; 
									?>
								</td>
								<?php // BOF GM_MOD	?>
								<td>
									&nbsp;
									<input type="file" name="gm_image_upload" />
									&nbsp;
								</td>
								<?php // EOF GM_MOD	?>
								<td	align="center">
									&nbsp;
									<?php 
										echo xtc_button(BUTTON_INSERT, 'submit', 'style="float:right; margin:5px"'); 
									?>
									&nbsp;
								</td>
								<?php
								echo '</form>';
								?>
							</tr>
							<?php
								}
							}
							?>
						</table>
					</td>
				</tr>
			</table>
		</td>
<!-- OPTION VALUE EOF //-->
	</tr> 
</table>
</td>
<!-- products_attributes_eof //-->
</tr>
</table>
<!-- body_text_eof //-->
<!-- footer //-->
<?php require(DIR_WS_INCLUDES .	'footer.php'); ?>
    <!-- footer_eof //-->
</body>
</html>

<?php require(DIR_WS_INCLUDES .	'application_bottom.php'); ?>
