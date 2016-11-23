/* --------------------------------------------------------------
 accounting_controller.js 2015-09-24 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 ----------------------------------------------------------------
 */

/**
 * ## Accounting Controller Widget
 *
 * This controller will handle the checkboxes in this page.
 *
 * @module Controllers/accounting_controller
 */
gx.controllers.module(
	// Module name
	'accounting_controller',
	
	// Module dependencies
	[],
	
	/** @lends module:Controllers/accounting_controller */
	
	function() {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var $this = $(this),
			module = {};
		
		// ------------------------------------------------------------------------
		// ELEMENTS DEFINITION
		// ------------------------------------------------------------------------
		
		var $mainCheckBox = $this.find('#check_all');
		var $checkboxes = $this.find('input[name="access[]"]');
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _onClick = function(event) {
			var $target = $(event.target);
			
			if ($target.is($mainCheckBox)) {
				var checked = $target.is(':checked');
				
				$checkboxes.each(function(index, element) {
					var $switcher = $(element).parent();
					
					$(element).attr('checked', checked);
					
					if (checked) {
						$switcher.addClass('checked');
					} else {
						$switcher.removeClass('checked');
					}
				});
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			$this.on('click', _onClick);
			done();
		};
		
		return module;
	});
