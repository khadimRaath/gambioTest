<?php
/* --------------------------------------------------------------
   campaigns_statistics.php 2016-03-16
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/

/**
 * SET START AND END DATE VALUES
 */
$jStartDate = ($startDate) ? date('j', $startDate) : 1;
$mStartDate = ($startDate) ? date('n', $startDate) : 1;
$yStartDate = ($startDate) ? date('Y') - date('Y', $startDate) : 0;

$jEndDate = ($endDate) ? date('j', $endDate - 60 * 60 * 24) : date('j');
$mEndDate = ($endDate) ? date('n', $endDate - 60 * 60 * 24) : date('n');
$yEndDate = ($endDate) ? date('Y') - date('Y', $endDate - 60 * 60 * 24) : 0;

?>
<div class="grid sales-statistics-menu-wrapper">
	<!--
			LEFT COLUMN
		-->
	<div class="span6 left-column">
		<div class="grid">
			<!--
				FROM DATE
			-->
			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_START_DATE; ?>
				</div>
				<div class="pull-right">
					<select class="date-select" name="startD">
						<?php for($i = 1; $i < 32; $i++): ?>
							<option<?php if($i == $jStartDate)
							{
								echo ' selected';
							} ?>><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>

					<select class="date-select" name="startM">
						<?php for($i = 1; $i < 13; $i++): ?>
							<option<?php if($i == $mStartDate)
							{
								echo ' selected';
							} ?>><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>

					<select class="date-select" name="startY">
						<?php for($i = 10; $i >= 0; $i--): ?>
							<option<?php if($i == $yStartDate)
							{
								echo ' selected';
							} ?>><?php echo date('y') - $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>
			</div>

			<!--
				END DATE
			-->
			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_END_DATE; ?>
				</div>
				<div class="pull-right">
					<select class="date-select" name="endD">
						<?php for($i = 1; $i < 32; $i++): ?>
							<option<?php if($i == $jEndDate)
							{
								echo ' selected';
							} ?>><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>
					<select class="date-select" name="endM">
						<?php for($i = 1; $i < 13; $i++): ?>
							<option<?php if($i == $mEndDate)
							{
								echo ' selected';
							} ?>><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>
					<select class="date-select" name="endY">
						<?php for($i = 10; $i >= 0; $i--): ?>
							<option<?php if($i == $yEndDate)
							{
								echo ' selected';
							} ?>><?php echo date('y') - $i; ?></option>
						<?php endfor; ?>
					</select>
				</div>
			</div>

			<!--
							TIME SPAN
						-->
			<div class="span12">
				<div class="pull-left">
					<?php echo HEADING_TIME_SPAN; ?>
				</div>
				<div class="pull-right">
					<select name="report">
						<option value="1" <?php if((int)$srView === 1)
						{
							echo ' checked';
						} ?>><?php echo REPORT_TYPE_YEARLY; ?></option>
						<option value="2" <?php if((int)$srView === 2)
						{
							echo ' checked';
						} ?>><?php echo REPORT_TYPE_MONTHLY; ?></option>
						<option value="3" <?php if((int)$srView === 3)
						{
							echo ' checked';
						} ?>><?php echo REPORT_TYPE_WEEKLY; ?></option>
						<option value="4" <?php if((int)$srView === 4)
						{
							echo ' checked';
						} ?>><?php echo REPORT_TYPE_DAILY; ?></option>
					</select>
				</div>
			</div>

		</div>
	</div>

	<!--
		RIGHT COLUMN
	-->
	<div class="span6 right-column">
		<div class="grid">

			<!--
				MAX RESULTS
			-->
			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_STATUS_FILTER; ?>
				</div>
				<div class="pull-right">
					<?php
					echo xtc_draw_pull_down_menu('status', array_merge(array(array('id' => '0', 'text' => REPORT_ALL)),
					                                                   $orders_statuses), $_GET['status']);
					?>
				</div>
			</div>


			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_CAMPAIGN_FILTER; ?>
				</div>
				<div class="pull-right">
					<?php
					echo xtc_draw_pull_down_menu('campaign',
					                             array_merge(array(array('id' => '0', 'text' => REPORT_ALL)),
					                                         $campaigns), $_GET['campaign']);
					?>
				</div>
			</div>
		</div>
	</div>
	<div class="span12 btn-wrapper">
		<input type="submit"
		       class="btn btn-primary pull-right"
		       onClick="this.blur();"
		       value="<?php echo BUTTON_UPDATE; ?>" />
	</div>
</div>
