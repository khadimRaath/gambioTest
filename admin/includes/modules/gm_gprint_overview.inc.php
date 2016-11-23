<?php
/* --------------------------------------------------------------
   gm_gprint_overview.inc.php 2015-09-09 gm
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

$t_gm_languages = xtc_get_languages();

$messageStack = MainFactory::create('messageStack'); 
$messageStack->add(GM_GPRINT_OVERVIEW_ADVICE, 'info');

?>

<div class="message_stack_container">
	<?php echo $messageStack->output(); ?>
	<div id="gm_gprint_save_success" class="alert alert-success" style="display: none;"><?php echo GM_GPRINT_SET_NAME_CHANGE_SUCCESS; ?></div>
</div>

<table border="0" cellpadding="0" cellspacing="0" width="100%" height="25">
	<tr class="dataTableHeadingRow">
		<td class="dataTableHeadingContentText" style="width:1%; padding: 0px 20px 0px 10px; white-space: nowrap"><?php echo GM_GPRINT_OVERVIEW; ?></td>
		<td class="dataTableHeadingContentText" style="border-right: 0px; padding: 0px 20px 0px 10px;"><a href="gm_gprint.php?action=configuration"><?php echo GM_GPRINT_CONFIGURATION; ?></a></td>
	</tr>
</table>

<table class="overview-page" border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr class="dataTableHeadingRow">
		<th class="dataTableHeadingContent"><?php echo GM_GPRINT_TEXT_SET; ?></th>
	</tr>
	<tr>
		<td valign="top">
			<?php 
			$t_found_surfaces_groups = false;
			
			$coo_gm_gprint_product_manager = new GMGPrintProductManager();
			
			$t_gm_gprint_surfaces_groups = $coo_gm_gprint_product_manager->get_surfaces_groups();
			
			for($i = 0; $i < count($t_gm_gprint_surfaces_groups); $i++)
			{
				$t_found_surfaces_groups = true;
				echo '
					<div class="sets_overview" id="set_' . $t_gm_gprint_surfaces_groups[$i]['ID'] . '">
						<a href="gm_gprint.php?action=edit&id=' . $t_gm_gprint_surfaces_groups[$i]['ID'] . '&languages_id=' . $_SESSION['languages_id'] . '">
							<span id="set_name_' . $t_gm_gprint_surfaces_groups[$i]['ID'] . '">' . $t_gm_gprint_surfaces_groups[$i]['NAME'] . '</span>
						</a>
					</div>
				';
			}
			?>			
		</td>
	</tr>
</table>
<div class="hidden">
	<div class="gm_gprint_menu gx-container gx-configuration-box">

		<div id="gm_gprint_selected_options" class="configuration-box-content">

			<div class="configuration-box-header">
				<h2><?php echo ucfirst(GM_GPRINT_TEXT_SELECTED_SET); ?></h2>
			</div>

			<div class="configuration-box-body">
				<div class="configuration-box-form-content editable">
					<form action="gm_gprint.php" method="get">
						<label class="gm_gprint_menu_text options-title"><?php echo GM_GPRINT_TEXT_COPY_NAME; ?></label>
						<br />
						<input type="text" name="surfaces_group_name_copy" id="surfaces_group_name_copy" value="" />
		
						<input class="btn" type="button" id="copy_surfaces_group" value="<?php echo ucfirst(GM_GPRINT_BUTTON_COPY); ?>" />
						<input type="hidden" class="set_id" id="copy_surfaces_groups_id" name="id" value="" />
					</form>
		
					<br />
		
					<form action="gm_gprint.php" method="get">
						<label class="gm_gprint_menu_text options-title"><?php echo GM_GPRINT_TEXT_RENAME_NAME; ?></label>
						<br />
						<input type="text" name="surfaces_group_name_rename" id="surfaces_group_name_rename" value="" />
						<input class="btn" type="button" id="rename_surfaces_group" value="<?php echo ucfirst(GM_GPRINT_BUTTON_RENAME); ?>" />
						<input type="hidden" class="set_id" id="rename_surfaces_groups_id" name="id" value="" />
					</form>
				</div>
			</div>

			<div class="configuration-box-footer">
				<div class="button-container">
					<form action="gm_gprint.php" method="get">
						<input class="btn btn-primary" type="submit"  value="<?php echo ucfirst(GM_GPRINT_BUTTON_EDIT); ?>" />
						<input type="hidden" class="set_id" name="id" value="" />
						<input type="hidden" name="action" value="edit" />
						<input type="hidden" name="languages_id" value="<?php echo $_SESSION['languages_id']; ?>" />
					</form>

					<form>
						<input class="btn" type="button" id="delete_set" value="<?php echo ucfirst(GM_GPRINT_BUTTON_DELETE); ?>" />
						<input type="hidden" class="set_id" id="delete_set_id" name="id" value="" />
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
				