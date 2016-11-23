/* --------------------------------------------------------------
 orders_tooltip.js 2015-10-02
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order Tooltip
 *
 * This controller displays a tooltip when hovering the order
 *
 * @module Controllers/orders_tooltip
 */
gx.controllers.module(
	'orders_tooltip',
	
	[],
	
	/**  @lends module:Controllers/orders_tooltip */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
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
			defaults = {
				'url': ''
			},
			
			/**
			 * timeout for tooltip assignment
			 *
			 * @type {boolean}
			 */
			timeout = 0,
			
			/**
			 * delay until tooltip appears
			 *
			 * @type {number}
			 */
			delay = 300,
			
			/**
			 * flag, if element is hoverd
			 *
			 * @type {boolean}
			 */
			hoverd = true,
			
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
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		var _loadOrderData = function() {
			if (options.url !== '') {
				$.ajax({
					url: options.url,
					type: 'GET',
					dataType: 'json',
					success: function(response) {
						var content = '<table>';
						
						for (var id in response.products) {
							var product = response.products[id];
							content += '<tr>';
							
							for (var key in product) {
								
								if (typeof product[key] !== 'object') {
									
									var align = (key === 'price') ? ' align="right"' : '';
									
									content += '<td valign="top"' + align + '>' + product[key];
									
									if (key === 'name') {
										for (var i in product.attributes) {
											content += '<br />- ' + product.attributes[i].name;
											content += ': ' + product.attributes[i].value;
										}
									}
									
									content += '</td>';
								}
							}
							
							content += '</tr>';
						}
						
						content +=
							'<tr><td class="total_price" colspan="4" align="right">' + response.total_price +
							'</td></tr>';
						
						content += '</table>';
						
						timeout = window.setTimeout(function() {
							$this.qtip({
								content: content,
								style: {
									classes: 'gx-container gx-qtip info large'
								},
								position: {
									my: 'left top',
									at: 'right bottom'
								},
								show: {
									when: false,
									ready: hoverd,
									delay: delay
								},
								hide: {
									fixed: true
								}
							});
							
							options.url = '';
						}, delay);
					}
				});
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('hover', _loadOrderData);
			$this.on('mouseout', function() {
				hoverd = false;
				clearTimeout(timeout);
			});
			
			// Finish it
			done();
		};
		
		return module;
	});
