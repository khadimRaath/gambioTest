<?php
/* --------------------------------------------------------------
   gm_gprint_categories.inc.php 2014-03-21 mb
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?><?php 

$coo_gm_gprint_product_manager = new GMGPrintProductManager();

// assign selected set to products in selected categories
if(isset($_POST['gm_gprint_add']) && isset($_POST['categories']) && !empty($_POST['gm_gprint_surfaces_groups_id']))
{
	$f_categories_ids = $_POST['categories'];
	$c_gm_gprint_surfaces_groups_id = (int)$_POST['gm_gprint_surfaces_groups_id'];
	
	if($c_gm_gprint_surfaces_groups_id > 0)
	{
		for($i = 0; $i < count($f_categories_ids); $i++)
		{
			$c_categories_id = (int)$f_categories_ids[$i];
			
			$t_gm_gprint_get_products = xtc_db_query("SELECT products_id 
														FROM " . TABLE_PRODUCTS_TO_CATEGORIES . "
														WHERE categories_id = '" . $c_categories_id . "'");
			while($t_gm_gprint_products = xtc_db_fetch_array($t_gm_gprint_get_products))
			{
				$coo_gm_gprint_product_manager->add($c_gm_gprint_surfaces_groups_id, $t_gm_gprint_products['products_id']);
			}
		}
	}
	
}

// delete assignment of products in selected categories
elseif(isset($_POST['gm_gprint_delete']) && isset($_POST['categories']))
{
	$f_categories_ids = $_POST['categories'];
	
	for($i = 0; $i < count($f_categories_ids); $i++)
	{
		$c_categories_id = (int)$f_categories_ids[$i];
		
		$t_gm_gprint_get_products = xtc_db_query("SELECT p.products_id 
													FROM 
														" . TABLE_PRODUCTS_TO_CATEGORIES . " p,
														" . TABLE_GM_GPRINT_SURFACES_GROUPS_TO_PRODUCTS . " g
													WHERE 
														p.categories_id = '" . $c_categories_id . "'
														AND p.products_id = g.products_id");
		while($t_gm_gprint_products = xtc_db_fetch_array($t_gm_gprint_get_products))
		{
			$coo_gm_gprint_product_manager->remove($t_gm_gprint_products['products_id']);
		}
	}
}

// assign selected products to selected set
elseif(isset($_POST['gm_gprint_add_products']) && isset($_POST['products']) && !empty($_POST['gm_gprint_surfaces_groups_id']))
{
	$f_products_ids = $_POST['products'];
	$c_gm_gprint_surfaces_groups_id = (int)$_POST['gm_gprint_surfaces_groups_id'];
	
	if($c_gm_gprint_surfaces_groups_id > 0)
	{
		for($i = 0; $i < count($f_products_ids); $i++)
		{
			$c_products_id = (int)$f_products_ids[$i];
			
			$coo_gm_gprint_product_manager->add($c_gm_gprint_surfaces_groups_id, $c_products_id);
		}
	}
	
}

// delete assignment of selected products
elseif(isset($_POST['gm_gprint_delete_products']) && isset($_POST['products']))
{
	$f_products_ids = $_POST['products'];
	
	for($i = 0; $i < count($f_products_ids); $i++)
	{
		$c_products_id = (int)$f_products_ids[$i];
		
		$coo_gm_gprint_product_manager->remove($c_products_id);
	}
}

?>

<table style="margin-bottom:5px" border="0" cellpadding="0" cellspacing="0" width="100%" height="25">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContentText" style="padding: 0px 20px 0px 10px;"><?php echo GM_GPRINT_CATEGORIES_HEADING ?></td>
	</tr>
</table>

<table style="border: 1px solid #dddddd" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="dataTableRow">
		<td style="font-size: 12px; padding: 0px 10px 11px 10px; text-align: justify;">
			
		<?php 
		if(isset($_POST['gm_gprint_add']) || isset($_POST['gm_gprint_delete']) || isset($_POST['gm_gprint_add_products']) || isset($_POST['gm_gprint_delete_products']))
		{
			echo '<br />';
			echo '<span style="font-weight: bold; color: #408e2f">' . GM_GPRINT_SUCCESS . '</span>';
			echo '<br />';
			echo '<br />';
		}
		
		// show categories
		if(!isset($_GET['categories_id']))
		{
		?>
			<br />
			<?php echo GM_GPRINT_CATEGORIES_DESCRIPTION; ?>
			<br />
			<br />
			<form action="gm_gprint.php?action=categories" method="post" />
				<input type="checkbox" id="gm_gprint_check_all" style="margin-bottom: 10px;" /> <strong><?php echo GM_GPRINT_SELECT_ALL; ?></strong>
				<br />
				<?php 
				$t_gm_gprint_categories = xtc_get_category_tree();
				
				foreach($t_gm_gprint_categories AS $t_key => $t_value)
				{
					echo '<input type="checkbox" class="gm_gprint_checkbox" name="categories[]" value="' . $t_gm_gprint_categories[$t_key]['id'] . '" /> <a href="gm_gprint.php?action=categories&categories_id=' . $t_gm_gprint_categories[$t_key]['id'] . '" style="font-size: 12px" />' . $t_gm_gprint_categories[$t_key]['text'] . '</a>';
					echo '<br />';
				}
				?>
				<br />
				<input type="submit" class="button" name="gm_gprint_delete" style="width: auto;" value="<?php echo GM_GPRINT_BUTTON_DELTE_ASSIGNMENT; ?>" />
				<br />
				
				<select name="gm_gprint_surfaces_groups_id" size="1" />
					<?php 
					$t_gm_gprint_surfaces_groups = $coo_gm_gprint_product_manager->get_surfaces_groups();
					for($i = 0; $i < count($t_gm_gprint_surfaces_groups); $i++)
					{
						echo '<option value="' . $t_gm_gprint_surfaces_groups[$i]['ID'] . '">' . $t_gm_gprint_surfaces_groups[$i]['NAME'] . '</option>';
					}
					?>
				</select>
				
				<input type="submit" class="button" style="display: inline;" name="gm_gprint_add" value="<?php echo GM_GPRINT_BUTTON_ASSIGN; ?>" />
				
			</form>
			
		<?php
		}
		
		// show products
		else
		{
		?>
			<br />
			<?php echo GM_GPRINT_PRODUCTS_DESCRIPTION; ?>
			<br />
			<br />
			<form action="gm_gprint.php?action=categories&categories_id=<?php echo (int)$_GET['categories_id']; ?>" method="post" />
				<input type="checkbox" id="gm_gprint_check_all" style="margin-bottom: 10px;" /> <strong><?php echo GM_GPRINT_SELECT_ALL; ?></strong>
				<br />
				<?php 
				$c_categories_id = (int)$_GET['categories_id'];
				$c_languages_id = $_SESSION['languages_id'];
				
				$t_gm_gprint_get_products = xtc_db_query("SELECT 
																pd.products_id,
																pd.products_name 
															FROM 
																" . TABLE_PRODUCTS_TO_CATEGORIES . " ptc,
																" . TABLE_PRODUCTS_DESCRIPTION . " pd
															WHERE 
																ptc.categories_id = '" . $c_categories_id . "'
																AND ptc.products_id = pd.products_id
																AND pd.language_id = '" . $c_languages_id . "'");
				while($t_gm_gprint_products = xtc_db_fetch_array($t_gm_gprint_get_products))
				{
					echo '<input type="checkbox" class="gm_gprint_checkbox" name="products[]" value="' . $t_gm_gprint_products['products_id'] . '" /> ' . $t_gm_gprint_products['products_name'];
					echo '<br />';
				}
				?>
				<br />
				<input type="submit" class="button" style="width: auto;" name="gm_gprint_delete_products" value="<?php echo GM_GPRINT_BUTTON_DELTE_ASSIGNMENT; ?>" />
				<br />
				
				<select name="gm_gprint_surfaces_groups_id" size="1" />
					<?php 
					$t_gm_gprint_surfaces_groups = $coo_gm_gprint_product_manager->get_surfaces_groups();
					for($i = 0; $i < count($t_gm_gprint_surfaces_groups); $i++)
					{
						echo '<option value="' . $t_gm_gprint_surfaces_groups[$i]['ID'] . '">' . $t_gm_gprint_surfaces_groups[$i]['NAME'] . '</option>';
					}
					?>
				</select>
				
				<input type="submit" class="button" style="display: inline;" name="gm_gprint_add_products" value="<?php echo GM_GPRINT_BUTTON_ASSIGN; ?>" />
			</form>
			<br />
			<br />
			<br />
			<a href="gm_gprint.php?action=categories"><input type="button" class="button" style="display: inline;" value="<?php echo GM_GPRINT_BUTTON_BACK; ?>" /></a>
		<?php 
		}
		?>
		
		</td>
	</tr>
</table>
