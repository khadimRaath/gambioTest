/* --------------------------------------------------------------
 product_related_actions_controller.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Related Actions Controller
 *
 * This controller contains the mapping logic of the products properties/attributes/special buttons.
 *
 * @module Controllers/product_related_actions_controller
 */
gx.controllers.module(
	'product_related_actions_controller',
	
	[
		gx.source + '/libs/button_dropdown'
	],
	
	/** @lends module:Controllers/product_related_actions_controller */
	
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
				'properties_url': '',
				'attributes_url': '',
				'specials_url': '',
				'product_id': '',
				'c_path': '',
				'recent_button': 'BUTTON_SPECIAL'
			},
			
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
		
		/**
		 * Map actions to buttons.
		 *
		 * @private
		 */
		var _setActions = function() {
			var actions = [];
			
			actions.BUTTON_SPECIAL = _setSpecialPriceActionCallback;
			actions.BUTTON_PROPERTIES = _setPropertiesActionCallback;
			actions.BUTTON_ATTRIBUTES = _setAttributesActionCallback;
			
			if (options.attributes_url === '' && options.recent_button === 'BUTTON_ATTRIBUTES') {
				options.recent_button = defaults.recent_button;
			}
			
			jse.libs.button_dropdown.mapAction($this, 'BUTTON_SPECIAL', 'admin_buttons',
				_setSpecialPriceActionCallback);
			jse.libs.button_dropdown.mapAction($this, 'BUTTON_PROPERTIES', 'admin_buttons',
				_setPropertiesActionCallback);
			
			if (options.attributes_url !== '') {
				jse.libs.button_dropdown.mapAction($this, 'BUTTON_ATTRIBUTES', 'admin_buttons',
					_setAttributesActionCallback);
			}
		};
		
		/**
		 * Redirect to special pricing page.
		 *
		 * @returns {boolean}
		 *
		 * @private
		 */
		var _setSpecialPriceActionCallback = function() {
			
			if (options.specials_url !== '') {
				window.location.href = options.specials_url;
				
				return true;
			}
			
			return false;
		};
		
		/**
		 * Redirect to properties page.
		 *
		 * @returns {boolean}
		 * @private
		 */
		var _setPropertiesActionCallback = function() {
			
			if (options.properties_url !== '') {
				window.location.href = options.properties_url;
				
				return true;
			}
			
			return false;
		};
		
		/**
		 * Redirect to attributes page.
		 *
		 * @returns {boolean}
		 *
		 * @private
		 */
		var _setAttributesActionCallback = function() {
			
			if (options.attributes_url !== '' && options.product_id !== '') {
				var $form = $('<form action="' + options.attributes_url + '" method="post">' +
					'<input type="hidden" name="action" value="edit" />' +
					'<input type="hidden" name="current_product_id" value="' + options.product_id + '" />' +
					'<input type="hidden" name="cpath" value="' + options.c_path + '" />' +
					'</form>');
				
				$('body').prepend($form);
				
				$form.submit();
				
				return true;
			}
			
			return false;
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			window.setTimeout(_setActions, 300);
			done();	// Finish it
		};
		
		return module;
	});
