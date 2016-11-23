<?php
/* --------------------------------------------------------------
   gm_counter_conf.php 2015-09-21 gm
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

<form id="gm_counter_form">
	<table class="gx-configuration-table gx-configuration">
		<thead>
			<tr>
				<th colspan="2"><?php echo GM_COUNTER_TITLE_CONF; ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="configuration-label">
					<?php echo TITLE_CONF_IP; ?>
				</td>
				<td>
					<select onChange="gm_fadeout_boxes('gm_status');" id="GM_COUNTER_IP_BARRIER">
						<option selected value="<?php echo gm_get_conf('GM_COUNTER_IP_BARRIER'); ?>"><?php echo constant('SELECT_IP_' . gm_get_conf('GM_COUNTER_IP_BARRIER')); ?></option>
	
						<?php	if(gm_get_conf('GM_COUNTER_IP_BARRIER') != '60') { ?>
							<option value="60"><?php  echo SELECT_IP_60; ?></option>
	
						<?php	} if(gm_get_conf('GM_COUNTER_IP_BARRIER') != '3600') { ?>
							<option value="3600"><?php  echo SELECT_IP_3600; ?></option>
	
						<?php	} if(gm_get_conf('GM_COUNTER_IP_BARRIER') != '43200') { ?>
							<option value="43200"><?php echo SELECT_IP_43200; ?></option>
	
						<?php	} if(gm_get_conf('GM_COUNTER_IP_BARRIER') != '86400') { ?>
							<option value="86400"><?php echo SELECT_IP_86400; ?></option>
						<?php	} ?>
					</select>
				</td>
			</tr>
			
			<tr>
				<td class="configuration-label">
					<?php echo TITLE_CONF_VISITS; ?>
				</td>
				<td>
					<input onClick="gm_fadeout_boxes('gm_status');" type="text" value="<?php echo $gm_conf['hits']; ?>" id="gm_counter_visits_total">
				</td>
			</tr>
			
			<tr>
				<td class="configuration-label">
					<?php echo TITLE_CONF_START_DATE; ?>
				</td>
				<td>
					<input readonly class="gm_date-pick dp-applied" onClick="gm_fadeout_boxes('gm_status');" type="text" value="<?php echo date('Y-m-d', $gm_date['date']); ?>" id="gm_counter_date">
				</td>
			</tr>
		</tbody>
	</table>
	
	<div class="simple-container">
		<span id="gm_status" style="height:20px"></span>
		<input class="btn btn-primary pull-right" type="button" value="<?php echo BUTTON_SAVE;?>" onClick="gm_fadeout_boxes('gm_status');gm_update_boxes('<?php echo xtc_href_link('gm_counter_action.php', 'action=gm_counter_update'); ?>', 'gm_status')">																									 
	</div>
</form>