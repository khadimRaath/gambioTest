<?php
/* --------------------------------------------------------------
   dashboard.php 2016-09-19
   Gambio GmbH
   http://www.gambio.de
   Copyright (c) 2016 Gambio GmbH
   Released under the GNU General Public License (Version 2)
   [http://www.gnu.org/licenses/gpl-2.0.html]
   --------------------------------------------------------------
*/
$dashboardLang = MainFactory::create_object('LanguageTextManager', array('start', $_SESSION['language_id']));
$orderLang     = MainFactory::create_object('LanguageTextManager', array('orders', $_SESSION['language_id']));

$userConfigurationService = StaticGXCoreLoader::getService('UserConfiguration');
$userId                   = new IdType((int)$_SESSION['customer_id']);
$collapsed                = $userConfigurationService->getUserConfiguration($userId, 'dashboard_chart_collapse');
$statisticsInterval       = $userConfigurationService->getUserConfiguration($userId, 'statisticsInterval');
$statisticsTab            = $userConfigurationService->getUserConfiguration($userId, 'statisticsTab');
$statisticsChartItem      = $userConfigurationService->getUserConfiguration($userId, 'statisticsChartItem');
?>
<div class="dashboard-wrapper"  
	 data-gx-compatibility="dashboard/dashboard_controller"
	 data-dashboard_controller-collapsed="<?php echo $collapsed; ?>"
	 data-dashboard_controller-statistics-interval="<?php echo $statisticsInterval; ?>"
	 data-dashboard_controller-statistics-tab="<?php echo $statisticsTab; ?>"
	 data-dashboard_controller-statistics-chart-item="<?php echo $statisticsChartItem; ?>">
	<!-- Statistics boxes -->
	<div class="gx-container" data-gx-extension='toolbar_icons'>
		<div id="statistic-grid" data-gx-widget="statistic_box"
			<?php
			if(gm_get_conf('MODULE_CENTER_OLDORDEROVERVIEW_INSTALLED') === '1')
			{
				echo ' data-statistic_box-orders-url="orders.php"';
			}
			?>
		>
			<div class="statistic-widget"
				 data-statistic_box-item="online"
				 data-statistic_box-icon="fa-dashboard"
				 data-statistic_box-color="green">
			</div>
			<div class="statistic-widget"
				 data-statistic_box-item="visitors"
				 data-statistic_box-icon="fa-users"
				 data-statistic_box-color="yellow">
			</div>
			<div class="statistic-widget"
				 data-statistic_box-item="orders"
				 data-statistic_box-icon="fa-shopping-cart"
				 data-statistic_box-color="blue">
			</div>
			<div class="statistic-widget"
				 data-statistic_box-item="conversionRate"
				 data-statistic_box-icon="fa-line-chart"
				 data-statistic_box-color="red">
			</div>
			<div class="statistic-widget"
				 data-statistic_box-item="sales"
				 data-statistic_box-icon="fa-diamond"
				 data-statistic_box-color="lila">
			</div>
		</div>
	</div>

	<div class="dashboard-chart">
		<div class="compatibility-dashboard gx-container">
			<div class="ui-tabs" data-gx-widget="tabs" style="margin-right: 0; margin-bottom: 24px;">
				<div class="tab-headline-wrapper">
					<a href="#last_orders"><?php echo $dashboardLang->get_text('LAST_ORDERS'); ?></a>
					<a href="#chart"><?php echo $dashboardLang->get_text('STATISTICS'); ?></a>
				</div>
				<div class="tab-content-wrapper">
					<!-- Latest orders -->
					<div>
						<!-- Will be loaded from JS -->
						<table class="latest-orders-table">
							<thead>
							<tr>
								<th class="text-right"><?php echo str_replace(':', '', $orderLang->get_text('EMAIL_TEXT_ORDER_NUMBER')); ?></th>
								<th><?php echo $orderLang->get_text('TABLE_HEADING_CUSTOMERS'); ?></th>
								<th><?php echo $orderLang->get_text('TABLE_HEADING_ORDER_TOTAL'); ?></th>
								<th><?php echo $orderLang->get_text('TABLE_HEADING_DATE_PURCHASED'); ?></th>
								<th><?php echo str_replace(':', '', $orderLang->get_text('TEXT_INFO_PAYMENT_METHOD')); ?></th>
								<th><?php echo $orderLang->get_text('TABLE_HEADING_STATUS'); ?></th>
							</tr>
							</thead>
							<tbody> <!-- Filled dynamically through AJAX (check dashboard_controller.js) --> </tbody>
						</table>
					</div>

					<!-- Statistic Chart -->
					<div class="statistic-chart">
						<!-- Chart -->
						<div class="statistic-chart-container">
							<div
								id="dashboard-chart"
								data-gx-widget="statistic_chart"
								data-statistic_chart-user-id="<?php echo (int)$_SESSION['customer_id']; ?>"
								></div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Dropdowns -->
		<div class="gx-container ">
			<div class="toolbar js-interval-dropdown-toolbar">
				<!-- Statistic Interval Dropdown -->
				<div class="pull-right">
					<span><?php echo $dashboardLang->get_text('SELECT_TIMESPAN'); ?>&nbsp;</span>
					<select class="js-interval-dropdown" data-user-id="<?php echo (int)$_SESSION['customer_id']; ?>">
						<option value="week"><?php echo STATISTICS_INTERVAL_ONE_WEEK; ?></option>
						<option value="two_weeks"><?php echo STATISTICS_INTERVAL_TWO_WEEKS; ?></option>
						<option value="month"><?php echo STATISTICS_INTERVAL_ONE_MONTH; ?></option>
						<option value="three_months"><?php echo STATISTICS_INTERVAL_THREE_MONTHS; ?></option>
						<option value="six_months"><?php echo STATISTICS_INTERVAL_SIX_MONTHS; ?></option>
						<option value="year"><?php echo STATISTICS_INTERVAL_ONE_YEAR; ?></option>
					</select>
				</div>

				<div class="pull-right" style="display: inline-block; width: 24px;">&nbsp;</div>

				<!-- Statistic Item Dropdown -->
				<div class="pull-right">
					<span><?php echo $dashboardLang->get_text('SELECT_ITEM'); ?>&nbsp;</span>
					<select class="statistic-chart-dropdown" name="item">
						<option value="sales"><?php echo $dashboardLang->get_text('STATISTICS_SALES'); ?></option>
						<option value="visitors"><?php echo $dashboardLang->get_text('STATISTICS_VISITORS'); ?></option>
						<option value="newCustomers"><?php echo $dashboardLang->get_text('STATISTICS_NEW_CUSTOMERS'); ?></option>
						<option value="orders"><?php echo $dashboardLang->get_text('STATISTICS_ORDERS_COUNT'); ?></option>
					</select>
				</div>

				<div class="pull-right" style="display: inline-block; width: 24px;">&nbsp;</div>

				<!-- SELECT TAB -->
				<div class="pull-right">
					<span><?php echo $dashboardLang->get_text('SHOW_TAB'); ?>&nbsp;</span>
					<select class="statistic-tab-dropdown">
						<option value="0"><?php echo $dashboardLang->get_text('LAST_ORDERS'); ?></option>
						<option value="1"><?php echo $dashboardLang->get_text('STATISTICS'); ?></option>
					</select>
				</div>
			</div>
		</div>
	</div>

	<div class="grid">
		<div class="span12 dashboard-toggler"></div>
	</div>

</div>
