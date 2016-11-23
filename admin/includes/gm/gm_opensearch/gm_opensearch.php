<?php
/* 
--------------------------------------------------------------
gm_opensearch.php 01.04.2008 pt
Gambio OHG
http://www.gambio.de
Copyright (c) 2008 Gambio OHG
Released under the GNU General Public License (Version 2)
[http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
--------------------------------------------------------------
*/
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>

<div class="frame-wrapper">
	<div class="frame-head">
		<label class="title"><?php echo GM_TITLE_OPENSEARCH; ?></label>
	</div>
	<div class="frame-content simple-container">
		<div class="span6">
			<form action="<?php echo xtc_href_link('gm_opensearch.php', 'action=gm_opensearch'); ?>" method="post">
				<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
				<?php echo GM_TEXT_OPENSEARCH; ?><br /><br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_OPENSEARCH_BOX"><?php echo GM_ACTIVATE_OPENBOX; ?></label>
					</span>
					<input id="GM_OPENSEARCH_BOX"
						       type="checkbox"
						       name="GM_OPENSEARCH_BOX"
						       value="1" <?php echo ((int)gm_get_conf('GM_OPENSEARCH_BOX', 'ASSOC', true)
						                             == 1) ? 'checked="checked"' : ''; ?> />
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_OPENSEARCH_SEARCH"><?php echo GM_ACTIVATE_OPENSEARCH; ?></label>
					</span>
					<input id="GM_OPENSEARCH_SEARCH"
						       type="checkbox"
						       name="GM_OPENSEARCH_SEARCH"
						       value="1" <?php echo ((int)gm_get_conf('GM_OPENSEARCH_SEARCH', 'ASSOC', true)
						                             == 1) ? 'checked="checked"' : ''; ?> />
				</div>
				<input type="submit"
				       class="button pull-right"
				       name="go_opensearch"
				       value="<?php echo BUTTON_SAVE; ?>" />
			</form>
		</div>
		<!-- span6 -->
	</div>
	<!-- frame-content -->
</div><!-- frame-wrapper -->
