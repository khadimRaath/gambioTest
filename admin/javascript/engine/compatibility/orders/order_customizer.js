/* --------------------------------------------------------------
 order_customizer.js 2015-08-10 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Modal Layer Module
 *
 * This module will open a modal layer for order actions like deleting or changing the oder status.
 *
 * @module Compatibility/order_customizer
 */
gx.compatibility.module(
	'order_customizer',
	
	[],
	
	/**  @lends module:Compatibility/order_customizer */
	
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
		
		var _openCustomizerSet = function(event) {
			
			var $customizer = $(options.selector);
			
			event.preventDefault();
			event.stopPropagation();
			$customizer.dialog({
				'title': jse.core.lang.translate('HEADING_GX_CUSTOMIZER', 'orders'),
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': [
					{
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function() {
							$(this).dialog('close');
						}
					}
				],
				'width': 420
			});
			
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('click', _openCustomizerSet);
			done();
		};
		
		return module;
	});
