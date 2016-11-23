/* --------------------------------------------------------------
 security_page.js 2015-09-28 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Security Page Controller
 *
 * Changing behavior in the security page.
 * Add readonly-attribute to input elements if captcha_type-dropdown value 'standard' is selected
 *
 * @module Compatibility/security_page
 */
gx.compatibility.module(
	'security_page',
	
	[],
	
	/**  @lends module:Compatibility/security_page */
	
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
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _disableInputs = function() {
			console.log('change');
			var selectors = [
				'#GM_RECAPTCHA_PUBLIC_KEY',
				'#GM_RECAPTCHA_PRIVATE_KEY'
			];
			
			var read_only = true;
			if ($('#captcha_type').val() === 'recaptcha') {
				read_only = false;
			}
			
			$.each(selectors, function() {
				$(this).attr('readonly', read_only);
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			_disableInputs();
			$this.on('change', _disableInputs);
			done();
		};
		
		return module;
	});
