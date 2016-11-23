<?php
/* --------------------------------------------------------------
   new_attributes_select.php 2015-09-10 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------

   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(new_attributes_select.php); www.oscommerce.com 
   (c) 2003	 nextcommerce (new_attributes_select.php,v 1.9 2003/08/21); www.nextcommerce.org
   (c) 2003 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: new_attributes_select.php 901 2005-04-29 10:32:14Z novalis $)

   Released under the GNU General Public License 
   --------------------------------------------------------------
   Third Party contributions:
   New Attribute Manager v4b				Autor: Mike G | mp3man@internetwork.net | http://downloads.ephing.com
   copy attributes                          Autor: Hubi | http://www.netz-designer.de

   Released under the GNU General Public License 
   --------------------------------------------------------------*/
	
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
	$adminImages = DIR_WS_CATALOG . "lang/". $_SESSION['language'] ."/admin/images/buttons/";
	$adminMenuLang = MainFactory::create('LanguageTextManager', 'admin_menu', $_SESSION['language_id']);
?>

<tr>
	<td>
		<table>
			<tr>
				<td class="dataTableHeadingContent">
					<a href="products_attributes.php">
						<?php echo $adminMenuLang->get_text('BOX_PRODUCTS_ATTRIBUTES'); ?>
					</a>
				</td>
				<td class="dataTableHeadingContent">
					<?php echo $adminMenuLang->get_text('BOX_ATTRIBUTES_MANAGER'); ?>
				</td>
			</tr>
		</table>
	</td>
</tr>

<tr>
	<td class="main" valign="top">
		<input type="hidden" name="action" value="edit" />
		<?php echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id()); ?>
	</td>
</tr>
<tr>
	<td class="main" valign="top">
		<strong><?php echo SELECT_PRODUCT; ?><strong> 
		<br />
		<br />
	</td>
</tr>
<tr>
	<td class="main" valign="top">
		<?php
			echo '<select name="current_product_id">';

			$result = xtc_db_query("
									SELECT
										p.products_id,
										p.products_model,
										pd.products_name
									FROM
										" . TABLE_PRODUCTS . " p,
										" . TABLE_PRODUCTS_DESCRIPTION . " pd
									WHERE 
										p.products_id = pd.products_id AND
										pd.language_id = '" . $_SESSION['languages_id'] . "'
									ORDER BY 
										pd.products_name ASC
									");

			$matches = xtc_db_num_rows($result);

			if ($matches) {
				while ($line = xtc_db_fetch_array($result)) {
					$title = $line['products_name'];
					if(!empty($line['products_model'])) {
						$title .= ' (' .  $line['products_model'] . ')';
					}
					$current_product_id = $line['products_id'];
					echo '<option value="' . $current_product_id . '">' . $title . '</option>'; 
				}
			} else {
				echo "You have no products at this time.";
			}

			echo '</select>' .
			'<br />' .
			'<br />';
			echo xtc_button(BUTTON_EDIT, 'submit', '', 'pull-right');
		?>
	</td>
</tr>
<tr>
	<td class="main" valign="top">
		<br />
		<strong><?php echo SELECT_COPY; ?></strong> 
		<br /><br />
	</td>
</tr>
<tr>
	<td class="main" valign="top">
		<?php 
			// BOF GM_MOD:
			echo '<select name="copy_product_id">';
			
			$copy_query = xtc_db_query("SELECT
											p.products_id,
											p.products_model,
											pd.products_name
										FROM
											" . TABLE_PRODUCTS . " p,
											" . TABLE_PRODUCTS_DESCRIPTION . " pd,
											" . TABLE_PRODUCTS_ATTRIBUTES . " pa
										WHERE
											pa.products_id = pd.products_id AND
											pd.products_id = p.products_id AND
											pd.language_id = '" . $_SESSION['languages_id'] . "'
										GROUP BY p.products_id
										ORDER BY pd.products_name ASC");
			$copy_count = xtc_db_num_rows($copy_query);

			if ($copy_count) {
				echo '<option value="0">no copy</option>';
				while ($copy_res = xtc_db_fetch_array($copy_query)) {
					$t_title = $copy_res['products_name'];
					if(!empty($copy_res['products_model']))
					{
						$t_title .= ' (' .  $copy_res['products_model'] . ')';
					}
					echo '<option value="' . $copy_res['products_id'] . '">' . $t_title . '</option>';
				}
			}
			else {
				echo 'No products to copy attributes from';
			}
			echo '</select>' .
				 '<br />' .
				 '<br />';
			echo xtc_button(BUTTON_COPY, 'submit', '', 'pull-right');
		?>
	</td>
</tr>