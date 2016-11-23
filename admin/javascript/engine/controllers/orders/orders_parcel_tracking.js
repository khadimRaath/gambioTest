/* --------------------------------------------------------------
 orders_parcel_tracking.js 2015-08-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order Tracking Codes Controller
 *
 * @module Controllers/orders_parcel_tracking
 */
gx.controllers.module(
	'orders_parcel_tracking',
	
	['fallback'],
	
	/** @lends module:Controllers/orders_parcel_tracking */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _addTrackingCode = function(event) {
			
			event.stopPropagation();
			
			var data_set = jse.libs.fallback._data($(this), 'orders_parcel_tracking');
			var tracking_code = $('#parcel_service_tracking_code').val();
			if (tracking_code === '') {
				return false;
			}
			
			$.ajax({
				'type': 'POST',
				'url': 'request_port.php?module=ParcelServices&action=add_tracking_code',
				'timeout': 30000,
				'dataType': 'json',
				'context': this,
				'data': {
					
					'tracking_code': tracking_code,
					'service_id': $('#parcel_services_dropdown option:selected').val(),
					'order_id': data_set.order_id,
					'page_token': data_set.page_token
				},
				success: function(response) {
					$('#tracking_code_wrapper > .frame-content > table').html(response.html);
				}
			});
			
			return false;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Init function of the widget
		 */
		module.init = function(done) {
			
			if (options.container === 'tracking_code_wrapper') {
				$this.on('click', '.add_tracking_code', _addTrackingCode);
			}
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});
