<?php
/* --------------------------------------------------------------
   linked_categories.inc.php 2015-10-01
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * #####################################################################################################################
 * Set linked categories
 * #####################################################################################################################
 */

$query       = 'SELECT categories_id FROM products_to_categories WHERE products_id = ' . (int)$pInfo->products_id;
$result      = xtc_db_query($query);
$categoryIds = array();
while($row = xtc_db_fetch_array($result))
{
	$categoryIds[] = (int)$row['categories_id'];
}

if(isset($current_category_id))
{
	if(count($categoryIds) > 0 && in_array((int)$current_category_id, $categoryIds))
	{
		unset($categoryIds[array_search((int)$current_category_id, $categoryIds)]);
		array_unshift($categoryIds, (int)$current_category_id);

		// reset keys
		$categoryIds = array_values($categoryIds);
	}
	elseif(count($categoryIds) === 0)
	{
		$categoryIds[] = (int)$current_category_id;
	}
}
$categoryArray = xtc_get_category_tree(0, '&nbsp;&nbsp;&nbsp;');

/**
 * Define if a multi select should be used instead of an usual.
 * When the amount of all categories multiplied with amount of the linked product categories
 * is greater than 1200, a multi select box will be displayed.
 */
$multiSelect = (count($categoryArray) * count($categoryIds) > 1200) ? : false;

if($multiSelect)
{
	?>
	<div class="span6 linked-categories" data-gx-controller="product/add_category_to_product">
		<div class="grid control-group remove-border">
			<div class="span6">
				<label><?php echo TEXT_CATEGORY_LINK; ?></label>
			</div>
			<div class="span6">
				<select name="categories[]" class="full-width" multiple>
					<?php
					foreach($categoryArray as $category)
					{
						$selected = (in_array($category['id'], $categoryIds)) ? ' selected' : null;
						echo '<option value="'
						     . $category['id']
						     . '"'
						     . $selected
						     . '>'
						     . htmlentities_wrapper(html_entity_decode_wrapper($category['text']))
						     . '</option>';
					}
					?>
				</select>
			</div>
		</div>
	</div>
	<div class="span6 linked-categories">
		<div class="grid control-group remove-border">
			<?php foreach($categoryIds as $i => $categoryId): ?>
				<?php
				if($categoryId === 0)
				{
					continue;
				}
				?>
				<div class="span12 multi-select-container">
					<label class="category-path">
						<?php
						$categoryPathArray = xtc_generate_category_path($categoryId);
						$categoryPathArray = array_reverse($categoryPathArray[0]);

						$categoryTexts = array();
						foreach($categoryPathArray as $categoryData)
						{
							$categoryTexts[] = $categoryData['text'];
						}
						echo implode(' > ', $categoryTexts);
						?>
					</label>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php
}
else
{
	?>

	<!--
        LINKED CATEGORIES
	-->
	<div class="span12 linked-categories" data-gx-controller="product/add_category_to_product">
		<?php
		foreach($categoryIds as $i => $categoryId)
		{
			$categoryArray = xtc_get_category_tree(0, '&nbsp;&nbsp;&nbsp;');
			if($current_category_id != $categoryId)
			{
				array_unshift($categoryArray, array('id' => -1, 'text' => TEXT_NONE));
			}

			$removeBorder = ($i === count($categoryIds) - 1) ? ' remove-border' : '';
			?>
			<div class="grid control-group category-link-wrapper saved-category<?php echo $removeBorder; ?>">
				<div class="span3">
					<label><?php echo TEXT_CATEGORY_LINK; ?></label>
				</div>
				<div class="span3">
					<select name="categories[]" class="full-width">
						<?php
						foreach($categoryArray as $category)
						{
							$selected = ((int)$category['id'] === (int)$categoryId) ? ' selected' : null;

							echo '<option'
							     . $selected
							     . ' value="'
							     . $category['id']
							     . '">'
							     . htmlentities_wrapper(html_entity_decode_wrapper($category['text']))
							     . '</option>';
						}
						?>
					</select>
				</div>
				<div class="span6">
					<label class="category-path">
						<?php
						$categoryPathArray = xtc_generate_category_path($categoryId);
						$categoryPathArray = array_reverse($categoryPathArray[0]);

						$categoryTexts = array();
						foreach($categoryPathArray as $categoryData)
						{
							$categoryTexts[] = $categoryData['text'];
						}

						echo implode(' > ', $categoryTexts);
						?>
					</label>
				</div>
			</div>
			<?php
		}

		?>
		<div class="grid control-group remove-border category-template hidden">
			<div class="span3">
				<label><?php echo TEXT_CATEGORY_LINK; ?></label>
			</div>
			<div class="span3">
				<?php echo xtc_draw_pull_down_menu('categories[]',
				                                   $categoryArray,
				                                   -1,
				                                   'class="full-width" disabled'); ?>
			</div>
			<div class="span6">
				<label class="category-path"></label>
			</div>
		</div>

		<?php if(!$multiSelect): ?>
			<button type="button" class="btn add-category"><?php echo BUTTON_ADD ?></button>
		<?php endif; ?>
	</div>
	<?php
}
?>
