<?php
/*
--------------------------------------------------------------
gm_meta_options.php 2014-04-14 gm
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
		<form method="post" action="<?php echo xtc_href_link('gm_meta.php', 'action=' . $_GET['action'] . '&gm_options=1&lang_id=' . $lang_id); ?>">
			<span class="pull-right">
				<?php echo gm_get_lang_link('gm_meta.php', 'gm_meta_options', ''); ?>
			</span>

			<table class="gx-compatibility-table" data-gx-widget="checkbox">
				<tbody>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label><?php echo GM_TITLE_USE_STANDARD_META_TITLE; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<input type="radio" value="1" name="GM_TITLE_USE_STANDARD_META_TITLE" <?php if(gm_get_conf('GM_TITLE_USE_STANDARD_META_TITLE') == '1') { echo 'checked="checked"'; } ?>>
							<input type="radio" value="0" name="GM_TITLE_USE_STANDARD_META_TITLE" <?php if(gm_get_conf('GM_TITLE_USE_STANDARD_META_TITLE') == '0') { echo 'checked="checked"'; } ?>>

						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="GM_TITLE_SHOW_STANDARD_META_TITLE">
								<?php echo GM_TITLE_SHOW_STANDARD_META_TITLE; ?>
							</label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<select id="GM_TITLE_SHOW_STANDARD_META_TITLE" style="width:170px;" name="GM_TITLE_SHOW_STANDARD_META_TITLE">
								<?php if (gm_get_conf('GM_TITLE_SHOW_STANDARD_META_TITLE') == 'before') { ?>
									<option value="before"
											selected="selected"><?php echo GM_TITLE_SHOW_STANDARD_META_TITLE_BEFORE; ?></option>
									<option value="after"><?php echo GM_TITLE_SHOW_STANDARD_META_TITLE_AFTER; ?></option>
								<?php } else { ?>
									<option value="before"><?php echo GM_TITLE_SHOW_STANDARD_META_TITLE_BEFORE; ?></option>
									<option value="after" selected="selected"><?php echo GM_TITLE_SHOW_STANDARD_META_TITLE_AFTER; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label>
								<?php echo GM_TITLE_STANDARD_META_TITLE; ?>
							</label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<?php echo xtc_draw_input_field('GM_TITLE_STANDARD_META_TITLE', gm_get_content('GM_TITLE_STANDARD_META_TITLE', $lang_id), 'style="width:170px"'); ?>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="GM_TITLE_STANDARD_META_TITLE_SEPARATOR">
								<?php echo GM_TITLE_STANDARD_META_TITLE_SEPARATOR; ?>
							</label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<input id="GM_TITLE_STANDARD_META_TITLE_SEPARATOR"
								type="text"
								name="GM_TITLE_STANDARD_META_TITLE_SEPARATOR"
								value="<?php echo str_replace(' ', '&nbsp;', htmlspecialchars_wrapper(gm_get_content('GM_TITLE_STANDARD_META_TITLE_SEPARATOR',
																												$lang_id))); ?>"
								style="width:170px;" />
						</td>
					</tr>
				</tbody>
			</table>

			<br />

			<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
			<input type="hidden" name="gm_lang" value="<?php echo $lang_id; ?>">
			<input class="button pull-right" type="submit" name="gm_submit" value="<?php echo BUTTON_SAVE; ?>">
		</form>
	</div>
</div>
