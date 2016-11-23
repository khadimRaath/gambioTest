/* --------------------------------------------------------------
 modules_overview.js 2015-09-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Modules Overview Listing Handler
 *
 * This module will handle the listing actions on module pages like payment, shipping or order total
 *
 * @module Compatibility/modules_overview
 */
gx.compatibility.module(
	'modules_overview',
	
	[],
	
	/**  @lends module:Compatibility/modules_overview */
	
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
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		var _toggle = function(event) {
			var id = $(this).prop('id');
			
			$('.' + id).toggleClass('hidden');
			$(this).toggleClass('closed');
			
			$(this).find('i:last-child').toggleClass('fa-plus-square-o');
			$(this).find('i:last-child').toggleClass('fa-minus-square-o');
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			// init method
			
			$('.module-head').on('click', _toggle);
			
			done();
		};
		
		return module;
	});
