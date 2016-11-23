/* --------------------------------------------------------------
 categories_goto_controller.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Overview Goto
 *
 * @module Compatibility/categories_goto_controller
 */
gx.compatibility.module(
	// Module name
	'categories_goto_controller',
	
	// Module dependencies
	[],
	
	/**  @lends module:Compatibility/categories_goto_controller */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		// Element: Module selector
		var $this = $(this);
		
		// Meta object
		var module = {};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		var _createForm = function() {
			var $form = $('<form>');
			
			$form.attr({
				name: data.name,
				action: data.action,
				method: 'get'
			});
			
			return $form;
		};
		
		var _initialize = function() {
			// Create new form
			var $form = _createForm();
			
			// Save HTML content
			var html = $this.html();
			
			// Insert HTML into form and put form into this element
			$form.html(html);
			
			$this
				.empty()
				.append($form);
		};
		
		module.init = function(done) {
			// Initialize
			_initialize();
			
			// Register as finished
			done();
		};
		
		return module;
	});
