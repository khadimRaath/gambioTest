<?php
/* --------------------------------------------------------------
   gm_sitemap_conf.php 2015-09-10 gm
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
<form class="gx-container" method="post" action="<?php echo xtc_href_link('gm_sitemap.php', 'action=' . 'gm_sitemap_conf&update=1'); ?>">
	<br>
	<table class="gx-configuration" border="0" width="100%" cellspacing="0" cellpadding="2" id="gm_table">
		<tr>
			<td valign="top" align="left" class="main">
				<?php echo GM_SITEMAP_CHANGEFREQ; ?>
			</td>
			<td valign="top" align="left" class="main">
				<select name="GM_SITEMAP_GOOGLE_CHANGEFREQ" style="width:150px">
					<option selected value="<?php echo htmlspecialchars_wrapper($gm_conf['GM_SITEMAP_GOOGLE_CHANGEFREQ']); ?>"><?php echo htmlspecialchars_wrapper(constant('TITLE_' . strtoupper($gm_conf['GM_SITEMAP_GOOGLE_CHANGEFREQ']))); ?></option>
					<option value="always"><?php echo TITLE_ALWAYS; ?></option>
					<option value="hourly"><?php echo TITLE_HOURLY; ?></option>
					<option value="daily"><?php echo TITLE_DAILY; ?></option>
					<option value="weekly"><?php echo TITLE_WEEKLY; ?></option>
					<option value="monthly"><?php echo TITLE_MONTHLY; ?></option>
					<option value="yearly"><?php echo TITLE_YEARLY; ?></option>
					<option value="never"><?php echo TITLE_NEVER; ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" class="main">
				<?php echo GM_SITEMAP_PRIORITY; ?>
			</td>
			<td valign="top" align="left" class="main">
				<select name="GM_SITEMAP_GOOGLE_PRIORITY" style="width:150px">
					<option selected value="<?php echo htmlspecialchars_wrapper($gm_conf['GM_SITEMAP_GOOGLE_PRIORITY']); ?>"><?php echo htmlspecialchars_wrapper($gm_conf['GM_SITEMAP_GOOGLE_PRIORITY']); ?></option>
					<option value="0.0">0.0</option>
					<option value="0.1">0.1</option>
					<option value="0.2">0.2</option>
					<option value="0.3">0.3</option>
					<option value="0.4">0.4</option>
					<option value="0.5">0.5</option>
					<option value="0.6">0.6</option>
					<option value="0.7">0.7</option>
					<option value="0.8">0.8</option>
					<option value="0.9">0.9</option>
					<option value="1.0">1.0</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="top" align="left" class="main">
				<?php echo GM_META_LANG; ?>
			</td>
			<td valign="top" align="left" class="main">
				<?php $gm_languages = gm_get_language(); ?>
				<select name="GM_SITEMAP_GOOGLE_LANGUAGE_ID" style="width:150px">
					<?php
					foreach($gm_languages as $t_language_data)
					{
						if($gm_conf['GM_SITEMAP_GOOGLE_LANGUAGE_ID'] ==  $t_language_data['languages_id'])
						{
							echo '<option selected value="' . $t_language_data['languages_id'] . '">' . $t_language_data['name'] . '</option>';
						}
						else
						{
							echo '<option value="' . $t_language_data['languages_id'] . '">' . $t_language_data['name'] . '</option>';
						}
					}
					?>
				</select>
			</td>
		</tr>
	</table>

	<div style="height: 36px;">
		<input type="hidden" name="page_token" value="<?php echo $_SESSION['coo_page_token']->generate_token(); ?>" />
		<span id="gm_status" style="height:20px"></span>
		<input class="button pull-right" type="submit" value="<?php echo BUTTON_SAVE;?>">
	</div>
</form>
