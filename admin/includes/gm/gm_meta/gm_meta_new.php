<?php
/* --------------------------------------------------------------
   gm_meta_new.php 2014-04-14 gambio
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
		<form method="post" action="<?php echo xtc_href_link('gm_meta.php', 'action=' . '&gm_new=1&lang_id=' . $lang_id); ?>">
			<table class="gx-compatibility-table">
				<tbody>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="GM_PRE_META_KEY"><?php echo GM_PRE_META_KEY; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<select id="GM_PRE_META_KEY" style="width:170px;" name="gm_meta">
								<option value="1" selected><?php echo MENU_TITLE_GM_META_NEW; ?></option>
								<option value="description">description</option>
								<option value="author">author</option>
								<option value="date">date</option>
								<option value="copyright">copyright</option>
								<option value="publisher">publisher</option>
								<option value="page-topic">page-topic</option>
								<option value="page-type">page-type</option>
								<option value="audience">audience</option>

								<option value="facebook_title">facebook_title</option>
								<option value="facebook_type">facebook_type</option>
								<option value="facebook_url">facebook_url</option>
								<option value="facebook_description">facebook_description</option>
							</select>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="GM_META_KEY"><?php echo GM_META_KEY; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<textarea id="GM_META_KEY" style="width:170px;" rows="1" name="gm_new_key"></textarea>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="GM_META_VALUE"><?php echo GM_META_VALUE; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<textarea id="GM_META_VALUE" style="width:170px;" rows="1" name="gm_new_value"></textarea>
						</td>
					</tr>
					<tr class="dataTableRow">
						<td class="dataTableContent configuration-label" style="width: 50%;">
							<label for="GM_META_LANG"><?php echo GM_META_LANG; ?></label>
						</td>
						<td class="dataTableContent" style="width: 50%;">
							<?php $gm_languages = gm_get_language(); ?>
							<select id="GM_META_LANG" name="gm_lang[]" multiple size="<?php echo count($gm_languages); ?>" style="width:170px; min-height: 38px;">
								<?php
								foreach($gm_languages as $t_language)
								{
									if($_SESSION['languages_id'] == $t_language['languages_id']) {
										echo '<option selected value="' . $t_language['languages_id'] . '">' . $t_language['name'] . '</option>';
									} else {
										echo '<option value="' . $t_language['languages_id'] . '">' . $t_language['name'] . '</option>';
									}
								}
								?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>

			<br />

			<?php echo xtc_draw_hidden_field('page_token', $_SESSION['coo_page_token']->generate_token()); ?>
			<input class="button pull-right" type="submit" value="<?php echo BUTTON_SAVE;?>">
			<span id="gm_status" class="pull-right" style="height:20px"></span>
		</form>
	</div>
</div>
