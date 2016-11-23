<?php
	/*
	--------------------------------------------------------------
	gm_meta.php 2014-04-14 gm
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2014 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]

   IMPORTANT! THIS FILE IS DEPRECATED AND WILL BE REPLACED IN THE FUTURE. 
   MODIFY IT ONLY FOR FIXES. DO NOT APPEND IT WITH NEW FEATURES, USE THE
   NEW GX-ENGINE LIBRARIES INSTEAD.
	--------------------------------------------------------------
	*/
	defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
?>

<div class="simple-container">
	<div class="span6">
		<form class="remove-margin remove-padding" method="post" action="<?php echo xtc_href_link('gm_meta.php', 'action=' . $_GET['action'] . '&lang_id=' . $lang_id); ?>">
			<span class="pull-right">
				<?php echo gm_get_lang_link('gm_meta.php', 'gm_meta', ''); ?>
			</span>
			<table class="gx-compatibility-table" data-gx-widget="checkbox">
				<tbody>
					<?php for($i = 0; $i < count($gm_values); $i++) {?>
						<tr class="dataTableRow">
							<td class="dataTableContent configuration-label" style="width: 50%;">
								<span class="opensearch-label">
									<label for="value-<?php echo htmlspecialchars_wrapper($gm_values[$i]['gm_key']);?>">
										<?php
										if(defined(str_replace('GM_META_', 'GM_META_TITLE_', $gm_values[$i]['gm_key']))) {
											echo constant(str_replace('GM_META_', 'GM_META_TITLE_', $gm_values[$i]['gm_key']));
										} else {
											echo htmlentities_wrapper($gm_values[$i]['gm_key']);
										}
										?>
									</label>
								</span>
							</td>
							<td class="dataTableContent" style="width: 50%;">
								<!-- Do not put line breaks here -->
								<textarea id="value-<?php echo htmlspecialchars_wrapper($gm_values[$i]['gm_key']);?>"
										rows="1"
										cols="22"
										name="<?php echo htmlspecialchars_wrapper($gm_values[$i]['gm_key']);?>"
										style="white-space: normal;"><?php echo htmlspecialchars_wrapper($gm_values[$i]['gm_value']); ?></textarea>

								<?php if($gm_values[$i]['gm_key'] != 'robots' && $gm_values[$i]['gm_key'] != 'keywords') { ?>
									<div style="display: inline-block; width: 6px;"></div>
									<input id="GM_META_DELETE" type="checkbox" name="gm_delete[]" value="<?php echo htmlspecialchars_wrapper($gm_values[$i]['gm_contents_id']); ?>" data-single_checkbox>
									<label for="GM_META_DELETE" style="margin-left: 6px;"><?php echo GM_META_DELETE; ?></label>
								<?php } else { ?>
									<span style="margin-left: 12px;">*</span>
								<?php } ?>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>


			<div class="pull-left">
				<span style="margin-left: 24px;">
					* <?php echo GM_META_REQUIRED; ?>
				</span>
			</div>

			<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
			<input type="hidden" name="gm_lang" value="<?php echo $lang_id; ?>">
			<input class="button pull-right" type="submit" name="gm_submit" value="<?php echo BUTTON_SAVE;?>">
		</form>
	</div>
</div>
