<?php
/* --------------------------------------------------------------
   cross_selling.php 2016-07-18
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------


   (c) 2005 XT-Commerce - community made shopping http://www.xt-commerce.com ($Id: cross_selling.php 799 2005-02-23 18:08:06Z novalis $)

   Released under the GNU General Public License
   --------------------------------------------------------------*/

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
// select article data
$article_query = "SELECT products_name FROM ".TABLE_PRODUCTS_DESCRIPTION." WHERE products_id='".(int) $_GET['current_product_id']."' and language_id = '".$_SESSION['languages_id']."'";
$article_data = xtc_db_fetch_array(xtc_db_query($article_query));

$cross_sell_groups = xtc_get_cross_sell_groups();

function buildCAT($catID) {

	$cat = array ();

	while (getParent($catID) != 0 || $catID != 0) {
		$cat_select = xtc_db_query("SELECT categories_name FROM ".TABLE_CATEGORIES_DESCRIPTION." WHERE categories_id='".$catID."' and language_id='".$_SESSION['languages_id']."'");
		$cat_data = xtc_db_fetch_array($cat_select);
		$catID = getParent($catID);
		$cat[] = $cat_data['categories_name'];

	}

	$catStr = implode(' > ', $cat);

	return $catStr;
}

function getParent($catID) {
	$parent_query = xtc_db_query("SELECT parent_id FROM ".TABLE_CATEGORIES." WHERE categories_id='".$catID."'");
	$parent_data = xtc_db_fetch_array($parent_query);
	return $parent_data['parent_id'];
}
?>
    <table width="100%" cellspacing="0" cellpadding="2" data-gx-widget="single_checkbox" class="gx-container" style="margin-bottom: 24px">
      <tr>
        <td>
			<div class="pageHeading" style="background-image:url(html/assets/images/legacy/gm_icons/artkatalog.png)"><?php echo CROSS_SELLING.' : '.$article_data['products_name']; ?></div>
		</td>
      </tr>
      <tr>
        <td><?php echo xtc_draw_separator('pixel_trans.gif', '1', '10'); ?></td>
      </tr>
	  <tr>
        <td>
        
        <?php

echo xtc_draw_form('cross_selling', FILENAME_CATEGORIES, '', 'POST', '');
echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
echo xtc_draw_hidden_field('action', 'edit_crossselling');
echo xtc_draw_hidden_field('special', 'edit');
echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
?>
 
 
 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-bottom: 24px" data-gx-compatibility="row_selection">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent"><?php echo HEADING_DEL; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_SORTING; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_GROUP; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_MODEL; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_NAME; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_CATEGORY; ?></td>
  </tr>
<?php


$cross_query = "SELECT cs.ID,cs.products_id,pd.products_name,cs.sort_order,p.products_model,p.products_id,cs.products_xsell_grp_name_id FROM ".TABLE_PRODUCTS_XSELL." cs, ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p WHERE cs.products_id = '".(int) $_GET['current_product_id']."' and cs.xsell_id=p.products_id and p.products_id=pd.products_id  and pd.language_id = '".$_SESSION['languages_id']."' ORDER BY cs.sort_order";
$cross_query = xtc_db_query($cross_query);
if (!xtc_db_num_rows($cross_query)) {
?>
  <tr class="dataTableRow">
    <td class="dataTableContent gm_strong categories_view_data" colspan="6"><?php echo GM_TITLE_NO_ENTRY; ?></td>
  </tr>
<?php


}
while ($cross_data = xtc_db_fetch_array($cross_query)) {
	$categorie_query = xtc_db_query("SELECT
		                                            categories_id
		                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
		                                            WHERE products_id='".$cross_data['products_id']."' LIMIT 0,1");
	$categorie_data = xtc_db_fetch_array($categorie_query);
?>

  <tr class="dataTableRow row_selection">
    <td class="dataTableContent categories_view_data"><input type="checkbox" name="ids[]" value="<?php echo $cross_data['ID']; ?>"></td>
    <td class="dataTableContent categories_view_data"><input name="sort[<?php echo $cross_data['ID']; ?>]" type="text" size="3" value="<?php echo $cross_data['sort_order']; ?>"></td>
    
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo xtc_draw_pull_down_menu('group_name['.$cross_data['ID'].']',$cross_sell_groups,$cross_data['products_xsell_grp_name_id']); ?></td>
    
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo $cross_data['products_model']; ?>&nbsp;</td>
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo $cross_data['products_name']; ?>&nbsp;</td>
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?>&nbsp;</td>
  </tr>

<?php } ?>
</table>
<input type="submit" class="button remove-margin" value="<?php echo BUTTON_SAVE; ?>" onClick="return confirm('<?php echo SAVE_ENTRY; ?>')">
</form>
</td>
</tr>


<!-- begin of Yoochoose recommendations -->
<?php
if (defined('YOOCHOOSE_ACTIVE') && YOOCHOOSE_ACTIVE) {
  require_once(DIR_FS_CATALOG.'includes/yoochoose/recommendations.php');
  require_once(DIR_FS_CATALOG.'includes/yoochoose/functions.php');
  require_once('../includes/classes/product.php');
?>
<tr>
  <td>
    <br />
    <h4><?php echo YOOCHOOSE_CROSS_SELL_RECOMMENDATION; ?></h4>
    <table width="100%" border="0" cellspacing="0" cellpadding="2"  class="gm_border dataTableRow" style="margin-bottom: 24px">
      <tr>
<?php
  echo xtc_draw_form('request_product_recommendation', FILENAME_CATEGORIES, '', 'GET');
  echo xtc_draw_hidden_field('action', 'edit_crossselling');
  echo xtc_draw_hidden_field('special', 'add_entries');
  echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
  echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
  echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
  if ($_GET['request_recommendation']) {
    $recommendations = recommendItems(getAlsoPurchasedStrategy(), $_GET['current_product_id'], 2);
    if (atLeastOneRecommendationIsProducts($recommendations)) {
?>
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="2" data-gx-compatibility="row_selection">
              <tr class="dataTableHeadingRow">
                <td class="dataTableHeadingContent"><?php echo HEADING_ADD; ?></td>
                <td class="dataTableHeadingContent"><?php echo HEADING_GROUP; ?></td>
                <td class="dataTableHeadingContent"><?php echo HEADING_MODEL; ?></td>
                <td class="dataTableHeadingContent"><?php echo HEADING_NAME; ?></td>
                <td class="dataTableHeadingContent"><?php echo HEADING_CATEGORY; ?></td>
              </tr>
<?php
      foreach ($recommendations as $recommendation) {
          $data = $recommendation->data;
          if ($recommendation->isProduct()) {
?>
              <tr class="dataTableRow row_selection">
                <td class="dataTableContent categories_view_data"><input type="checkbox" name="ids[]" value="<?php echo $data['products_id']; ?>"></td>
                <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo xtc_draw_pull_down_menu('group_name[' . $data['products_id'] . ']', $cross_sell_groups); ?></td>
                <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo $data['products_model']; ?>&nbsp;</td>
                <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo $data['products_name']; ?>&nbsp;</td>
                <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?>&nbsp;</td>
              </tr>
<?php
           }
      }
?>
              <tr>
                <td><input type="submit" class="button remove-margin" value="<?php echo BUTTON_SAVE; ?>"></td>
              </tr>
            </table>
          </td>
<?php
    } else {
        echo "<td>".YOOCHOOSE_RECOMMENDATION_EMPTY."</td>";
    }
  } else {
?>
          <td>
            <?php echo xtc_draw_hidden_field('request_recommendation', 'true');?>
            <input type="submit" class="button" onClick="this.blur();" value="<?php echo YOOCHOOSE_REQUEST_RECOMMENDATION; ?>"/>
          </td>
<?php
  }
?>
     </form>
      </tr>
    </table>
  </td>
</tr>
<?php
}
?>
<!-- end of Yoochoose recommendations -->

<tr>
<td>
<br />
    <br />
    <br />
<h4><?php echo CROSS_SELLING_SEARCH; ?></h4>
<?php
	echo xtc_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
	echo xtc_draw_hidden_field('action', 'edit_crossselling');
	echo xtc_draw_hidden_field(xtc_session_name(), xtc_session_id());
	echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
	echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
?>
<?php
    echo xtc_draw_input_field('search', '', 'style="font-size: 14px; height: 25px; float: left; margin-right: 5px;"');
	echo '<input type="submit" class="btn btn-primary" onClick="this.blur();" value="' . BUTTON_SEARCH . '"/>';
?>
</form>
</td>
</tr>
<tr>
<td>

<?php


	// search results
	if ($_GET['search']) {
		echo xtc_draw_form('product_search', FILENAME_CATEGORIES, '', 'GET');
		echo xtc_draw_hidden_field('action', 'edit_crossselling');
		echo xtc_draw_hidden_field('special', 'add_entries');
		echo xtc_draw_hidden_field('current_product_id', $_GET['current_product_id']);
		echo xtc_draw_hidden_field('cpath', $_GET['cpath']);
?>
 <table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-bottom: 24px" data-gx-compatibility="row_selection">
  <tr class="dataTableHeadingRow">
    <td class="dataTableHeadingContent"><?php echo HEADING_ADD; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_GROUP; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_MODEL; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_NAME; ?></td>
    <td class="dataTableHeadingContent"><?php echo HEADING_CATEGORY; ?></td>
  </tr>
  <?php


		$search_query = "SELECT * FROM ".TABLE_PRODUCTS_DESCRIPTION." pd, ".TABLE_PRODUCTS." p WHERE p.products_id=pd.products_id and pd.language_id='".$_SESSION['languages_id']."' and p.products_id!='".$_GET['current_product_id']."' and (pd.products_name LIKE '%".$_GET['search']."%' or p.products_model LIKE '%".$_GET['search']."%')";
		$search_query = xtc_db_query($search_query);

		while ($search_data = xtc_db_fetch_array($search_query)) {
			$categorie_query = xtc_db_query("SELECT
						                                            categories_id
						                                            FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
						                                            WHERE products_id='".$search_data['products_id']."' LIMIT 0,1");
			$categorie_data = xtc_db_fetch_array($categorie_query);
?>
  <tr class="dataTableRow row_selection">
    <td class="dataTableContent categories_view_data"><input type="checkbox" name="ids[]" value="<?php echo $search_data['products_id']; ?>"></td>
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo xtc_draw_pull_down_menu('group_name['.$search_data['products_id'].']',$cross_sell_groups); ?></td>
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo $search_data['products_model']; ?>&nbsp;</td>
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo $search_data['products_name']; ?>&nbsp;</td>
    <td class="dataTableContent categories_view_data" style="text-align: left;"><?php echo buildCAT($categorie_data['categories_id']); ?>&nbsp;</td>
  </tr>

<?php


		}
?>

</table>
<input type="submit" class="button remove-margin" value="<?php echo BUTTON_SAVE; ?>">
</form>
<?php } ?>

</td>
</tr>
</td>

<?php 

    /** Returns true, if at least one product in the ist is a 
     *  real product (product#isProduct method returns true). */
    function atLeastOneRecommendationIsProducts($recommendations) {
    	foreach ($recommendations as $recommendation) {
            if ($recommendation->isProduct()) {
            	return true;
            }
    	}
    	return false;
    }

?>
</table>