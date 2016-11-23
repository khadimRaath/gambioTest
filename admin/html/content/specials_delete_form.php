<form class="grid hidden" name="specials"
	  action="<?php echo xtc_href_link(FILENAME_SPECIALS, xtc_get_all_get_params(array('sID')) . 'action=deleteconfirm') ?>"
	  method="post" id="delete_confirm_form">

	<fieldset class="span12">
		<p class="modal-info-text"><?php echo TEXT_INFO_DELETE_INTRO ?></p>

		<div class="control-group">
			<label><?php echo TABLE_HEADING_PRODUCTS ?></label>
			<span class="product-name"></span>
		</div>

		<?php echo xtc_draw_hidden_field('page_token', $t_page_token) ?>

	</fieldset>
</form>