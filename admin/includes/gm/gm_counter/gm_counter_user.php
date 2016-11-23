<?php
/* --------------------------------------------------------------
   gm_counter_user.php 2008-03-17 gambio
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
<table border="0" width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td valign="top" align="left" class="main">
			<strong><?php echo constant("MENU_TITLE_". strtoupper($_GET['subpage']). ""); ?></strong>
		</td>
	</tr>
</table>
<br />
<table border="0" width="100%" cellspacing="3" cellpadding="3">
	<tr>
		<td valign="top" align="left" class="main" width="100">
			<?php echo TITLE_HITS; ?>
		</td>
		<td valign="top" align="left" class="main">
			<?php echo TITLE_NAME; ?>
		</td>
	</tr>
</table>
<table border="0" width="100%" cellspacing="3" cellpadding="3" id="gm_table_heading">
<?php foreach($gm_array as $value) { ?>
	<tr>
		<td valign="top" align="left" class="main" width="100">
			<?php echo $value['hits']; ?>
		</td>
		<td valign="top" align="left" class="main">
			<?php
				if($value['name'] == 'UNKNOWN') {
					echo TITLE_UNKNOWN;
				} else {
					echo $value['name']; 
				}
			?>
		</td>
	</tr>
<?php } ?>
</table>
<table border="0" width="100%" cellspacing="3" cellpadding="3" >
	<tr>
		<td valign="top" align="left" class="main" width="100">
 			<select onChange="gm_get_selected_content('<?php echo xtc_href_link('gm_counter_action.php', 'action=' . $_GET['action'] . '&subpage=' . $_GET['subpage']);	?>', 'gm_counter_user', '<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_box_submenu_user'); ?>')" style="width:80px;" id="gm_select_count">																		
				<option selected><?php echo TITLE_CHOOSE; ?></option>				
				<option value="5">5</option>				
				<option value="10">10</option>				
				<option value="20">20</option>				
				<option value="50">50</option>
				<option value="0"><?php echo TITLE_ALL; ?></option>
			</select>
		</td>
		<td valign="top" align="left" class="main">			
			<?php
				gm_navigate($_GET['gm_page'], $pages, $_GET['gm_count'], $_GET['action'], $_GET['subpage'], 'gm_box_submenu_user');
			?>
		</td>
	</tr>
</table>

