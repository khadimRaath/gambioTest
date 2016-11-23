<form class="grid hidden"
      data-gx-widget="checkbox"
      name="orders_multi_cancel"
      action="<?php echo xtc_href_link('gm_send_order.php'); ?>"
      method="post"
      id="multi_cancel_confirm_form">
	<fieldset class="span12">
		<p class="modal-info-text"><?php echo TEXT_INFO_MULTI_CANCEL_INTRO; ?></p>

		<div class="control-group">
			<label><?php echo TEXT_SELECTED_ORDERS; ?></label>
			<span class="selected_orders"></span>
		</div>

		<div class="control-group">
			<label><?php echo TEXT_INFO_RESTOCK_PRODUCT_QUANTITY ?></label>
			<?php echo xtc_draw_checkbox_field('gm_restock', '', (STOCK_LIMITED === 'true'), '', 'data-single_checkbox') ?>
		</div>

		<?php
		$auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
		if($auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true')
		{
			?>
			<div class="control-group">
				<label><?php echo TEXT_INFO_RESHIPP ?></label>
				<?php echo xtc_draw_checkbox_field('gm_reshipp', '', true, '', 'data-single_checkbox') ?>
			</div>
			<?php
		}
		?>

		<div class="control-group">
			<label><?php echo TEXT_INFO_REACTIVATEARTICLE ?></label>
			<?php echo xtc_draw_checkbox_field('gm_reactivateArticle', '', false, '', 'data-single_checkbox') ?>
		</div>

		<div class="control-group">
			<label><?php echo ENTRY_NOTIFY_CUSTOMER; ?></label>
			<?php echo xtc_draw_checkbox_field('gm_notify', '', false, '', 'data-single_checkbox') ?>
		</div>

		<div class="control-group">
			<label><?php echo ENTRY_NOTIFY_COMMENTS; ?></label>
			<?php echo xtc_draw_checkbox_field('gm_notify_comments', '', false, '', 'data-single_checkbox') ?>
		</div>

		<div class="control-group">
			<label><?php echo TABLE_HEADING_COMMENTS; ?></label>
			<textarea name="gm_comments"></textarea>
		</div>

		<?php echo xtc_draw_hidden_field('orders_multi_cancel', 'true') ?>
		<?php echo xtc_draw_hidden_field('page_token', $t_page_token) ?>

	</fieldset>
</form>