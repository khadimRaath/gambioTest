<form class="grid hidden" data-gx-widget="checkbox" name="orders" action="<?php echo xtc_href_link(FILENAME_ORDERS, xtc_get_all_get_params(array('oID', 'action')) . '&action=deleteconfirm') ?>" method="post" id="delete_confirm_form">

	<fieldset class="span12">
		<p class="modal-info-text"><?php echo TEXT_INFO_DELETE_INTRO ?></p>
		
		<div class="control-group">
			<label><?php echo TEXT_INFO_RESTOCK_PRODUCT_QUANTITY ?></label>
			<?php echo xtc_draw_checkbox_field('restock', '', (STOCK_LIMITED === 'true'), '', 'data-single_checkbox') ?>
		</div>
		
		<?php
		$auto_shipping_status = gm_get_conf('GM_AUTO_SHIPPING_STATUS');
		if($auto_shipping_status == 'true' && ACTIVATE_SHIPPING_STATUS == 'true' && STOCK_LIMITED == 'true')
		{
		?>
		<div class="control-group">
			<label><?php echo TEXT_INFO_RESHIPP ?></label>
			<?php echo xtc_draw_checkbox_field('reshipp', '', true, '', 'data-single_checkbox') ?>
		</div>
		<?php
		}
		?>
		
		<div class="control-group">
			<label><?php echo TEXT_INFO_REACTIVATEARTICLE ?></label>
			<?php echo xtc_draw_checkbox_field('reactivateArticle', '', false, '', 'data-single_checkbox') ?>
		</div>
		
		<?php echo xtc_draw_hidden_field('page_token', $t_page_token) ?>
		
	</fieldset>
</form>