/* --------------------------------------------------------------
 paypal_config.js 2015-09-20 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## PayPal Configuration
 *
 * Display info text in info message box.
 *
 * @module Compatibility/main_top_header
 */
gx.compatibility.module(
	'paypal_config',
	
	[
		gx.source + '/libs/info_messages'
	],
	
	/**  @lends module:Compatibility/paypal_config */
	
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
			 * Reference to the info messages library
			 * @var {object}
			 */
			messages = jse.libs.info_messages,
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			if ($('.firstconfig_note').length > 0) {
				$('.firstconfig_note').hide();
				messages.addInfo($('.firstconfig_note').html());
			}
			
			$('p.message').each(function() {
				messages.addInfo($(this).html());
				$(this).hide();
			});
			
			$('p.message_info').each(function() {
				messages.addWarning($(this).html());
				$(this).hide();
			});
			
			$('p.message_success').each(function() {
				messages.addSuccess($(this).html());
				$(this).hide();
			});
			
			$('p.message_error').each(function() {
				messages.addError($(this).html());
				$(this).hide();
			});
			
			$('.message_stack_container').addClass('breakpoint-large');
			
			done();
		};
		
		return module;
	});
