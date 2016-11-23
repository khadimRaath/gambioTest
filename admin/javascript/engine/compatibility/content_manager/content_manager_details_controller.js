/* --------------------------------------------------------------
 content_manager_details_controller.js 2016-08-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Content Manager Controller
 *
 * This controller contains the mapping logic of the content manager page.
 *
 * @module Compatibility/content_manager_details_controller
 */
gx.compatibility.module(
	'content_manager_details_controller',
	
	[],
	
	/**  @lends module:Compatibility/content_manager_details_controller */
	
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
		// MAIN FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		var saveButton = $this.find('[data-value="BUTTON_SAVE"]');
		var updateButton = $this.find('[data-value="BUTTON_UPDATE"]');
		var originalSaveButton = $this.find('[name="save"]');
		var originalUpdateButton = $this.find('[name="reload"]');
		
		saveButton.on('click', function() {
			originalSaveButton.click();
		});
		
		updateButton.on('click', function() {
			originalUpdateButton.click();
		});
		
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		module.init = function(done) {
			done();
		};
		
		return module;
	});
