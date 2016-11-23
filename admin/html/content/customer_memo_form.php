<form class="grid hidden"
      data-gx-widget="checkbox"
      name="test"
      action="<?php echo DIR_WS_ADMIN . 'customers.php?cID=' . $_GET['cID'] . '&action=newMemo' ?>"
      method="post"
      id="customer_memo_form">

	<div class="grid add-padding-10">
		<div class="span12">
			<div class="control-group">
				<label><?php echo TEXT_TITLE; ?></label> <input type="text" name="memo_title" />
			</div>

			<div class="control-group">
				<label><?php echo TEXT_MEMO; ?></label> <textarea name="memo_text"></textarea>
			</div>


			<?php echo xtc_draw_hidden_field('page_token', $t_page_token) ?>
		</div>
	</div>
</form>
