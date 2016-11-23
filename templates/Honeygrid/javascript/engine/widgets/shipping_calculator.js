/* --------------------------------------------------------------
 shipping_calculator.js 2016-05-19
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that updates the shipping cost box at the
 * shopping cart page
 */
gambio.widgets.module(
	'shipping_calculator',

	['form', 'xhr'],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$body = $('body'),
			defaults = {
				// URL at which the request is send.
				url: 'shop.php?do=CartShippingCosts',
				selectorMapping: {
					gambioUltraCosts: '.cart_shipping_costs_gambio_ultra_dropdown, ' 
						+ '.order-total-shipping-info-gambioultra-costs',
					shippingWeight: '.shipping-calculator-shipping-weight-unit, .shipping-weight-value',
					shippingCost: '.shipping-calculator-shipping-costs, .order-total-shipping-info, ' 
						+ '.shipping-cost-value',
					shippingCalculator: '.shipping-calculator-shipping-modules', 
					invalidCombinationError: '#cart_shipping_costs_invalid_combination_error'
				}
			},
			options = $.extend(true, {}, defaults, data),
			module = {};


// ########## EVENT HANDLER ##########

		/**
		 * Function that requests the given URL and
		 * fills the page with the delivered data
		 * @private
		 */
		var _updateShippingCosts = function() {
			var formdata = jse.libs.form.getData($this);

			jse.libs.xhr.ajax({url: options.url, data: formdata}).done(function(result) {
				jse.libs.template.helpers.fill(result.content, $body, options.selectorMapping);
			});

			// update modal content source
			var value = $this.find('select[name="cart_shipping_country"]').val();
			$('#shipping-information-layer.hidden select[name="cart_shipping_country"] option').attr('selected',false);
			$('#shipping-information-layer.hidden select[name="cart_shipping_country"] option[value="' + value + '"]')
				.attr('selected',true);
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$this.on('change update', _updateShippingCosts);

			done();

		};

		// Return data to widget engine
		return module;
	});