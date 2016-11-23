/* --------------------------------------------------------------
 shipcloud_config_controller.js 2016-01-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module(
	// Module name
	'shipcloud_config_controller',
	// Module dependencies
	[],
	function() {
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var $this = $(this),
			module = {};
		
		// ------------------------------------------------------------------------
		
		var _initCarrierCheckboxes = function() {
			$('input[name="preselected_carriers[]"]').on('change', function(e) {
				if ($(this).get(0).checked === true) {
					$('input[name="checked_carriers[]"]', $(this).closest('tr')).removeAttr('disabled');
				}
				else {
					$('input[name="checked_carriers[]"]', $(this).closest('tr')).attr('disabled', 'disabled');
				}
			});
			$('input[name="preselected_carriers[]"]').trigger('change');
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the module, called by the engine.
		 */
		module.init = function(done) {
			_initCarrierCheckboxes();
			done();
		};
		
		return module;
	}
);
