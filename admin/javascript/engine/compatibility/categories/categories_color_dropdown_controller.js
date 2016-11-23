/* --------------------------------------------------------------
 categories_color_dropdown_controller.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Color Dropdown Controller
 *
 * This controller changes the color of the dropdown button to gambio blue
 *
 * @module Compatibility/categories_color_dropdown_controller
 */
gx.compatibility.module(
	'categories_color_dropdown_controller',
	
	[],
	
	/**  @lends module:Compatibility/categories_color_dropdown_controller */
	
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
		
		// Add the btn-primary css class to the dropdown
		$('.js-button-dropdown button').addClass('btn-primary');
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			done(); // Finish it
		};
		
		return module;
	});
