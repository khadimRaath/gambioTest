<?php
/* --------------------------------------------------------------
   sales_statistics.php 2015-09-15
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2015 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
?>
<div class="grid sales-statistics-menu-wrapper">
	<!--
		FIRST ROW
	-->
	<div class="span6 menu-group">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_START_DATE; ?></label>
			</div>
			<div class="span4">
				<select>
					<option>01</option>
				</select>
				<select>
					<option>01</option>
				</select>
				<select>
					<option>2015</option>
				</select>
			</div>
		</div>

	</div>
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_END_DATE; ?></label>
			</div>
			<div class="span4">
				<select>
					<option>01</option>
				</select>
				<select>
					<option>01</option>
				</select>
				<select>
					<option>2015</option>
				</select>
			</div>
		</div>
	</div>

	<!--
		SECOND ROW
	-->
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo HEADING_TIME_SPAN; ?></label>
			</div>
			<div class="span4">
				<select>
					<option>Immer</option>
					<option>Monat</option>
					<option>Jahr</option>
				</select>
			</div>
		</div>
	</div>
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_DETAIL; ?></label>
			</div>
			<div class="span4">
				<select>
					<option>1</option>
					<option>2</option>
				</select>
			</div>
		</div>
	</div>

	<!--
		THIRD ROW
	-->
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_MAX; ?></label>
			</div>
			<div class="span4">
				<select>
					<option>1</option>
					<option>2</option>
				</select>
			</div>
		</div>
	</div>
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_PAYMENT_FILTER; ?></label>
			</div>
			<div class="span4">
				<select>
					<option>cod</option>
				</select>
			</div>
		</div>
	</div>

	<!--
		4. ROW
	-->
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_EXP; ?></label>
			</div>
			<div class="span4">

			</div>
		</div>
	</div>
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo HEADING_TIME_SPAN; ?></label>
			</div>
			<div class="span4">

			</div>
		</div>
	</div>

	<!--
		5. ROW
	-->
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo REPORT_SORT; ?></label>
			</div>
			<div class="span4">

			</div>
		</div>
	</div>
	<div class="span6">
		<div class="grid">
			<div class="span8">
				<label><?php echo HEADING_TIME_SPAN; ?></label>
			</div>
			<div class="span4">

			</div>
		</div>
	</div>
</div>
