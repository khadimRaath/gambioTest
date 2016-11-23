<?php
/* --------------------------------------------------------------
   product_details.inc.php 2016-06-30
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
if($products_description[$language['id']])
{
	$t_products_complete_description = stripslashes($products_description[$language['id']]);
}
else
{
	$t_products_complete_description = xtc_get_products_description($pInfo->products_id, $language['id']);
}
$t_products_description   = '';
$t_products_tabs_headline = array();
$t_products_tabs          = array();

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
$t_products_complete_description = str_replace('[TAB:', '~TAB:', $t_products_complete_description);

$t_matches2 = array();
preg_match_all('/~TAB:([^\]]+)\]([^~]*)/', $t_products_complete_description, $t_matches2);
foreach($t_matches2[1] AS $key => $value)
{
	$t_products_tabs_headline[] = str_replace('#GMTilde#', '~', $t_matches2[1][$key]);
	$t_products_tabs[]          = str_replace('#GMTilde#', '~', $t_matches2[2][$key]);
}

$t_textarea_name = 'products_description_' . $language['id'];

/**
 * #####################################################################################################################
 * Set language values
 * #####################################################################################################################
 */
$coo_text_mgr               = new LanguageTextManager('article_tabs', $_SESSION['languages_id']);
$t_article_tabs_text        = $coo_text_mgr->get_text('article_tabs');
$t_article_tabs_add_text    = $coo_text_mgr->get_text('add_article_tab');
$t_article_tabs_edit_text   = $coo_text_mgr->get_text('edit_article_tab');
$t_article_tabs_delete_text = $coo_text_mgr->get_text('delete_article_tab');
?>
<!--
	PRODUCT NAME AND DESCRIPTION
-->
<div class="span12">
	<div class="control-group">
		<div class="span12 category-details">
			<label><?php echo TEXT_PRODUCTS_NAME; ?></label>
			<?php echo xtc_draw_input_field('products_name[' . $language['id'] . ']',
				(($products_name[$language['id']]) ? stripslashes($products_name[$language['id']]) : xtc_get_products_name($pInfo->products_id,
				                                                                                                           $language['id'])), 'class="important-data"'); ?>
		</div>
	</div>
	<div class="control-group">
		<div class="span12 ckeditor-container category-details">
			<label><?php echo TEXT_PRODUCTS_DESCRIPTION; ?></label>
			<div class="control-group"
			     <?php
			     if(USE_WYSIWYG == 'true')
			     {
				     echo 'data-gx-widget="ckeditor" data-ckeditor-height="300px"';
			     }
			     ?>>
				<textarea name="<?php echo $t_textarea_name; ?>" class="wysiwyg"><?php echo $t_products_description; ?></textarea>
			</div>
		</div>
	</div>
</div>

<!--
	PRODUCT TABS
-->
<div class="span12 product-tabs category-details" id="language_<?php echo $language['id'] ?>">
	<div class="grid">
		<div class="span6 tabs-title">
			<label><?php echo $languageTextManager->get_text('article_tabs', 'article_tabs'); ?></label>
		</div>
	</div>
	<?php foreach($t_products_tabs_headline as $key => $value): ?>
		<div class="grid tab-section">
			<div class="span6">
				<i class="fa fa-sort add-margin-right-12"></i>
				<span><?php echo htmlentities_wrapper($value) ?></span>
				<div class="pull-right tab-icons-container">
					<a href="article_tabs/article_tabs_edit.html?buttons=cancel-save" class="product_tabs_button">
						<i class="fa fa-pencil fa-fw cursor-pointer"></i>
					</a>
					<a href="article_tabs/article_tabs_delete.html?buttons=cancel-delete"
					   class="product_tabs_button">
						<i class="fa fa-trash-o fa-fw cursor-pointer"></i>
					</a>
				</div>
				<input type="hidden"
				       name="products_tab_headline_<?php echo $language['id'] ?>[]"
				       value="<?php echo htmlentities_wrapper($value, ENT_QUOTES) ?>" />
				<textarea class="hidden"
				          name="products_tab_<?php echo $language['id']; ?>[]"><?php echo $t_products_tabs[$key]; ?></textarea>
			</div>
		</div>
	<?php endforeach; ?>
	<div class="grid tab-btn-container tab-btn-container-<?php echo $language['id']; ?>">
		<div class="span12">
			<a href="article_tabs/article_tabs_add.html?buttons=cancel-save"
			   class="product_tabs_button btn"
			   title="<?php echo $t_article_tabs_add_text; ?>"><?php echo $t_article_tabs_add_text; ?>
			</a>
		</div>
	</div>
</div>

<!--
	SHORT DESCRIPTION
-->
<div class="span6">
	<div class="control-group">
		<div class="span12 ckeditor-container category-details">
			<label>
				<?php echo TEXT_PRODUCTS_SHORT_DESCRIPTION; ?>
			</label>
			<div
				<?php
				if(USE_WYSIWYG == 'true')
				{
					echo 'data-gx-widget="ckeditor" data-ckeditor-height="300px"';
				}
				?>>
				<textarea name="<?php echo 'products_short_description_' . $language['id']; ?>"
				          class="wysiwyg">
					<?php
						echo (($products_short_description[$language['id']]) ? stripslashes($products_short_description[$language['id']]) : xtc_get_products_short_description($pInfo->products_id, $language['id']));
					?>
				</textarea>
			</div>
		</div>
	</div>
</div>

<!--
	PRODUCT CHARACTERISTICS
-->
<div class="span6">
	<div class="control-group">
		<div class="span12 ckeditor-container category-details">
			<label>
				<?php echo TEXT_CHECKOUT_INFORMATION; ?>
			</label>
			<div
				<?php
				if(USE_WYSIWYG == 'true')
				{
					echo 'data-gx-widget="ckeditor" data-ckeditor-height="300px"';
				}
				?>>
				<textarea name="<?php echo 'checkout_information_' . $language['id']; ?>"
				          class="wysiwyg">
					<?php
					echo (($checkout_information[$language['id']]) ? stripslashes($checkout_information[$language['id']]) : get_checkout_information($pInfo->products_id, $language['id']));
					?>
				</textarea>
			</div>
		</div>
	</div>
</div>

<!--
	LEFT COLUMN OF PRODUCT META DATA
-->
<div class="span6">
	<div class="grid control-group first-meta-data-item">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_URL; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_url[' . $language['id'] . ']',
				(($products_url[$language['id']]) ? stripslashes($products_url[$language['id']]) : xtc_get_products_url($pInfo->products_id,
				                                                                                                        $language['id']))); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_PRODUCTS_KEYWORDS; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_keywords[' . $language['id'] . ']',
				(($products_keywords[$language['id']]) ? stripslashes($products_keywords[$language['id']]) : xtc_get_products_keywords($pInfo->products_id,
				                                                                                                                       $language['id']))); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo GM_TEXT_URL_KEYWORDS; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('gm_url_keywords[' . $language['id'] . ']',
				(($gm_url_keywords[$language['id']]) ? stripslashes($gm_url_keywords[$language['id']]) : gm_get_products_url_keywords($pInfo->products_id,
				                                                                                                                      $language['id']))); ?>
		</div>
	</div>
	<div class="grid control-group remove-border">
		<div class="span6">
			<label><?php echo GM_TEXT_URL_REWRITE; ?></label>
		</div>
		<div class="span5">
			<div class="input-group">
				<?php
					if(isset($pInfo->products_id))
					{
						$urlRewrite = $productReadService->findRewriteUrl(new IdType($pInfo->products_id),
						                                                  new IdType($language['id']));
					}
					echo xtc_draw_input_field('url_rewrites[' . $language['id'] . ']',
						((!is_null($urlRewrite)) ? stripslashes($urlRewrite->getRewriteUrl()) : ''));
					echo '<div class="input-group-addon">.html</div>';
				?>
			</div>
		</div>
		<div class="span1">
			<span class="pull-right" data-gx-widget="tooltip_icon" data-tooltip_icon-type="info">
				<?php echo GM_TEXT_URL_REWRITE_PRODUCT_INFO ?>
			</span>
		</div>
	</div>
</div>

<!--
	RIGHT COLUMN OF PRODUCT META DATA
-->
<div class="span6">
	<div class="grid control-group first-meta-data-item">
		<div class="span6">
			<label><?php echo TEXT_META_TITLE; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_meta_title[' . $language['id'] . ']',
				(($products_meta_title[$language['id']]) ? stripslashes($products_meta_title[$language['id']]) : xtc_get_products_meta_title($pInfo->products_id,
				                                                                                                                             $language['id']))); ?>
		</div>
	</div>
	<div class="grid control-group">
		<div class="span6">
			<label><?php echo TEXT_META_KEYWORDS; ?></label>
		</div>
		<div class="span6">
			<?php echo xtc_draw_input_field('products_meta_keywords[' . $language['id'] . ']',
				(($products_meta_keywords[$language['id']]) ? stripslashes($products_meta_keywords[$language['id']]) : xtc_get_products_meta_keywords($pInfo->products_id,
				                                                                                                                                      $language['id']))); ?>
		</div>
	</div>
	<div class="grid control-group remove-border">
		<div class="span6">
			<label><?php echo TEXT_META_DESCRIPTION; ?></label>
		</div>
		<div class="span6">
			<?php
			$metaDescription = xtc_get_products_meta_description($pInfo->products_id, $language['id']);
			?>
			
			<textarea data-gx-widget="input_counter"
			          title="<?php echo TEXT_META_DESCRIPTION; ?>"
			          name="products_meta_description[<?php echo $language['id']; ?>]"><?php echo $metaDescription; ?></textarea>
		</div>
	</div>
</div>

<script type="text/javascript" src="html/assets/javascript/legacy/gm/lightbox_plugin.js"></script>

<script type="text/javascript">
	use_wysiwyg = <?php echo USE_WYSIWYG; ?>;
	article_tab_edit_title = "<?php echo $t_article_tabs_edit_text; ?>";
	article_tab_delete_title = "<?php echo $t_article_tabs_delete_text; ?>";
	$('.product_tabs_button').live('click', function () {
		container = $(this).parent().parent();
		$(this).lightbox_plugin({lightbox_width: 800});
		return false;
	});

	$(document).ready(function () {
		$(".product-tabs").sortable({
			items:       ".product_tab_box",
			axis:        "y",
			containment: "parent"
		});
		$(".product-tabs").disableSelection();
		$(".product_tabs_headline").disableSelection();
	});
</script>
