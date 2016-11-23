/* --------------------------------------------------------------
 resize_page.js 2015-10-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Resize Page
 *
 * Resizes the page to a calculated height including the (absolutely positioned) configuration box on the right side.
 *
 * @module Compatibility/resize_page
 */
gx.compatibility.module(
	'resize_page',
	
	[],
	
	/**  @lends module:Compatibility/resize_page */
	
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
		// PRIVATE FUNCTIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Resizes the page to the maximum height of boxCenterWrapper and gx-configuration-box
		 */
		var _resizePage = function() {
			$('.boxCenterWrapper').height(Math.max($('.boxCenterWrapper').height(), $(
				'.configuration-box-content').height()));
			
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			if ($('div.gx-configuration-box').length) {
				$('div.gx-configuration-box').on('resize', _resizePage);
				$('.boxCenterWrapper').on('resize', _resizePage);
				window.setTimeout(function() {
					_resizePage();
				}, 500);
			}
			
			if ($('#toolbar').length) {
				$('#toolbar').on('click', _resizePage);
				$('#gm_gprint_content').on('dblclick', _resizePage);
				$('#element_type').on('change', _resizePage);
			}
			
			done();
		};
		
		return module;
	});
