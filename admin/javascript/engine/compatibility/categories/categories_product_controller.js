/* --------------------------------------------------------------
 categories_product_controller.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Product Controller
 *
 * This controller contains the mapping logic of the categories save/update buttons.
 *
 * @module Compatibility/categories_product_controller
 */
gx.compatibility.module(
	'categories_product_controller',
	
	[
		gx.source + '/libs/button_dropdown'
	],
	
	/**  @lends module:Compatibility/categories_product_controller */
	
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
		// OPERATIONS
		// ------------------------------------------------------------------------
		// Hide the original buttons
		$('[name="gm_update"]').hide();
		$('[name="save_original"]').hide();
		
		// Map the new save option to the old save button
		jse.libs.button_dropdown.mapAction($this, 'BUTTON_SAVE', 'admin_buttons', function(event) {
			$('[name="save_original"]').trigger('click');
		});
		
		// Map the new update option to the old update button
		jse.libs.button_dropdown.mapAction($this, 'BUTTON_UPDATE', 'admin_buttons', function(event) {
			$('[name="gm_update"]').trigger('click');
		});
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			done();
		};
		
		return module;
	});
