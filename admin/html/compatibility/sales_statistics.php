<?php
/* --------------------------------------------------------------
   sales_statistics.php 2016-03-17
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
/**
 * START DATE
 */
$jStartDate = ($startDate) ? (int)date('j', $startDate) : 1;
$mStartDate = ($startDate) ? (int)date('n', $startDate) : 1;
$yStartDate = ($startDate) ? date('Y') - date('Y', $startDate) : 0;

/**
 * END DATE
 */
$jEndDate = ($endDate) ? date('j', $endDate - 60 * 60 * 24) : date('j');
$mEndDate = ($endDate) ? date('n', $endDate - 60 * 60 * 24) : date('n');
$yEndDate = ($endDate) ? date('Y') - date('Y', $endDate - 60 * 60 * 24) : 0;

/**
 * OTHER VALUES
 */
$paymentsArray = explode(';', MODULE_PAYMENT_INSTALLED);

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
							<option value="<?php echo $i; ?>"
								<?php if($i == $jStartDate)
								{
									echo ' selected';
								}
								?>
								><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>

					<select class="date-select" name="startM">
						<?php for($i = 1; $i < 13; $i++): ?>
							<option value="<?php echo $i; ?>"
								<?php if($i == $mStartDate)
								{
									echo ' selected';
								}
								?>
								><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>

					<select class="date-select" name="startY">
						<?php for($i = 10; $i >= 0; $i--): ?>
							<option value="<?php echo date('Y') - $i; ?>"
								<?php if($i == $yStartDate)
								{
									echo ' selected';
								}
								?>
								><?php echo date('y') - $i; ?></option>
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
							<option value="<?php echo $i; ?>"
								<?php if($i == $jEndDate)
								{
									echo ' selected';
								}
								?>
								><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>

					<select class="date-select" name="endM">
						<?php for($i = 1; $i < 13; $i++): ?>
							<option value="<?php echo $i; ?>"
								<?php if($i == $mEndDate)
								{
									echo ' selected';
								}
								?>
								><?php echo (strlen($i) === 2) ? $i : '0' . $i; ?></option>
						<?php endfor; ?>
					</select>

					<select class="date-select" name="endY">
						<?php for($i = 10; $i >= 0; $i--): ?>
							<option value="<?php echo date('Y') - $i; ?>"
								<?php if($i == $yEndDate)
								{
									echo ' selected';
								}
								?>
								><?php echo date('y') - $i; ?></option>
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

			<!--
				EXPORT
			-->
			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_EXP; ?>
				</div>
				<div class="pull-right">
					<select name="export">
						<option value="0" selected><?php echo EXP_NORMAL; ?></option>
						<option value="1"><?php echo EXP_HTML; ?></option>
						<option value="2"><?php echo EXP_CSV; ?></option>
					</select>
				</div>
			</div>

			<!--
				SORTING
			-->
			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_SORT; ?>
				</div>
				<div class="pull-right">
					<select name="sort">
						<option value="0"<?php if((int)$srSort === 0)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL0; ?></option>
						<option value="1"<?php if((int)$srSort === 1)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL1; ?></option>
						<option value="2"<?php if((int)$srSort === 2)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL2; ?></option>
						<option value="3"<?php if((int)$srSort === 3)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL3; ?></option>
						<option value="4"<?php if((int)$srSort === 4)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL4; ?></option>
						<option value="5"<?php if((int)$srSort === 5)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL5; ?></option>
						<option value="6"<?php if((int)$srSort === 6)
						{
							echo ' selected';
						} ?>><?php echo SORT_VAL6; ?></option>
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
					<?php echo REPORT_MAX; ?>
				</div>
				<div class="pull-right">
					<select name="max">
						<option value="0"><?php echo REPORT_ALL; ?></option>
						<option<?php if((int)$srMax === 1)
						{
							echo ' selected';
						} ?>>1
						</option>
						<option<?php if((int)$srMax === 3)
						{
							echo ' selected';
						} ?>>3
						</option>
						<option<?php if((int)$srMax === 5)
						{
							echo ' selected';
						} ?>>5
						</option>
						<option<?php if((int)$srMax === 10)
						{
							echo ' selected';
						} ?>>10
						</option>
						<option<?php if((int)$srMax === 25)
						{
							echo ' selected';
						} ?>>25
						</option>
						<option<?php if((int)$srMax === 50)
						{
							echo ' selected';
						} ?>>50
						</option>
					</select>
				</div>
			</div>
			
			

			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_DETAIL; ?>
				</div>
				<div class="pull-right">
					<select name="detail">
						<option value="0"<?php if((int)$srDetail === 0)
						{
							echo ' selected';
						} ?>><?php echo DET_HEAD_ONLY; ?></option>
						<option value="1"<?php if((int)$srDetail === 1)
						{
							echo ' selected';
						} ?>><?php echo DET_DETAIL; ?></option>
						<option value="2"<?php if((int)$srDetail === 2)
						{
							echo ' selected';
						} ?>><?php echo DET_DETAIL_ONLY; ?></option>
					</select>
				</div>
			</div>

			<div class="span12">
				<div class="pull-left">
					<?php echo REPORT_PAYMENT_FILTER; ?>
				</div>
				<div class="pull-right">
					<select name="payment">
						<option value="0"
							<?php if((int)$srPayment === 0)
							{
								echo ' selected';
							}
							?>><?php echo REPORT_ALL; ?>
						</option>

						<?php foreach($paymentsArray as $paymentValue): ?>
							<?php
							$coo_lang_file_master->init_from_lang_file('lang/' . $_SESSION['language']
							                                           . '/modules/payment/' . $paymentValue);
							$payment      = substr($paymentValue, 0, strrpos($paymentValue, '.'));
							$payment_text = constant(MODULE_PAYMENT_ . strtoupper($payment) . _TEXT_TITLE);
							?>
							<option value="<?php echo $payment; ?>"<?php
							if($srPayment === $payment)
							{
								echo ' selected';
							}
							?>><?php echo $payment_text; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>

			<div class="span12" style="height: 96px">
				<div class="grid">
					<div class="span8 pull-left">
						<?php echo REPORT_STATUS_FILTER; ?>
					</div>
					<div class="span4">
						<div class="pull-right">
							<select name="orders_status[]" multiple>
								<?php foreach($sr->status as $value): ?>
									<option value="<?php echo $value["orders_status_id"] ?>"<?php if(in_array($value["orders_status_id"],
									                                                                          $srStatus))
									{
										echo " selected";
									} ?>><?php echo $value["orders_status_name"]; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
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
