<?php
/* 
	--------------------------------------------------------------
	gm_opensearch_conf.php 2015-09-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
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
		<label class="title"><?php echo GM_TITLE_OPENSEARCH_CONF; ?></label>
		<span class="pull-right">
			<?php
				echo gm_get_lang_link('gm_opensearch.php', 'gm_opensearch_conf', '');
			?>
		</span>
	</div>
	<div class="frame-content simple-container remove-margin">
		<div class="span6">
			<form method="post"
			      action="<?php echo xtc_href_link('gm_opensearch.php',
			                                       'action=' . $_GET['action'] . '&lang_id=' . $lang_id); ?>">
				<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_OPENSEARCH_TEXT"><?php echo GM_TITLE_OPENSEARCH_TEXT; ?></label>
					</span>
					<span class="opensearch-input">
					<input id="GM_OPENSEARCH_TEXT"
					       type="text"
					       value="<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_TEXT',
					                                                                   $lang_id)); ?>"
					       name="GM_OPENSEARCH_TEXT" />
						</span>
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_TITLE_OPENSEARCH_LINK"><?php echo GM_TITLE_OPENSEARCH_LINK; ?></label>
					</span>
					<span class="opensearch-input">
						<input id="GM_TITLE_OPENSEARCH_LINK"
					        type="text"
					        value="<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_LINK', $lang_id)); ?>"
					        name="GM_OPENSEARCH_LINK" />
					</span>
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_TITLE_OPENSEARCH_SHORTNAME"><?php echo GM_TITLE_OPENSEARCH_SHORTNAME; ?></label>
					</span>
					<span class="opensearch-input">
						<input id="GM_TITLE_OPENSEARCH_SHORTNAME"
						       maxlength="16"
						       type="text"
						       value="<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_SHORTNAME',
					                                                                   $lang_id)); ?>"
						       name="GM_OPENSEARCH_SHORTNAME" />
					</span>
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_TITLE_OPENSEARCH_LONGNAME"><?php echo GM_TITLE_OPENSEARCH_LONGNAME; ?></label>
					</span>
					<span class="opensearch-input">
						<input id="GM_TITLE_OPENSEARCH_LONGNAME"
					           maxlength="48"
					           type="text"
					           value="<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_LONGNAME',
					                                                                   $lang_id)); ?>"
					           name="GM_OPENSEARCH_LONGNAME" />
					</span>
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_TITLE_OPENSEARCH_TAGS"><?php echo GM_TITLE_OPENSEARCH_TAGS; ?></label>
					</span>
					<span class="opensearch-input">
						<input id="GM_TITLE_OPENSEARCH_TAGS"
					           maxlength="256"
					           type="text"
					           value="<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_TAGS',
					                                                                   $lang_id)); ?>"
					           name="GM_OPENSEARCH_TAGS" />
					</span>
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_TITLE_OPENSEARCH_CONTACT"><?php echo GM_TITLE_OPENSEARCH_CONTACT; ?></label>
					</span>
					<span class="opensearch-input">
						<input id="GM_TITLE_OPENSEARCH_CONTACT"
					           type="text"
					           value="<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_TEXT',
					                                                                   $lang_id)); ?>"
					           name="GM_OPENSEARCH_TEXT" />
					</span>
				</div>
				<br />

				<div class="span6">
					<span class="opensearch-label">
						<label for="GM_TITLE_OPENSEARCH_DESCRIPTION"><?php echo GM_TITLE_OPENSEARCH_DESCRIPTION; ?></label>
					</span>
					<span class="opensearch-input">
						<textarea id="GM_TITLE_OPENSEARCH_DESCRIPTION"
					              rows="3"
					              name="GM_OPENSEARCH_DESCRIPTION">
							<?php echo html_entity_decode_wrapper(gm_get_content('GM_OPENSEARCH_DESCRIPTION', $lang_id)); ?>
						</textarea>
					</span>
				</div>
				<br />

				<input type="hidden" name="gm_lang" value="<?php echo $lang_id; ?>"> 
				<input class="button pull-right" type="submit" name="go_save" value="<?php echo BUTTON_SAVE; ?>">
			</form>
		</div>
		<!-- span6 -->
	</div>
	<!-- frame-content -->
</div><!-- frame-wrapper -->