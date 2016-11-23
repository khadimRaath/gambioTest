/* --------------------------------------------------------------
 disable_ckedit.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Disable CKEdit
 *
 * Extension to enable or disable (readonly) CKEditors corresponding to a checkbox value.
 *
 * @module Admin/Extensions/disable_ckedit
 * @ignore
 */
gx.extensions.module(
	'disable_ckedit',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE INITIALIZATION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Extension Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {
				'invert': false // if true, the checkbox has to be deselected to enable the ckeditor
			},
			
			/**
			 * Final Extension Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {},
			
			/**
			 * Interval
			 *
			 * @type {number}
			 */
			interval = null;
		
		// ------------------------------------------------------------------------
		// EVENT HANDLER
		// ------------------------------------------------------------------------
		
		/**
		 * Switch CKEdit
		 *
		 * Function to detect if a CKEdit is bound to the target text field. If so,
		 * set the readonly state of the box corresponding to the checkbox value.
		 */
		var _switchCkEdit = function() {
			if (window.CKEDITOR && CKEDITOR.instances && CKEDITOR.instances[options.target]) {
				
				if (interval) {
					clearInterval(interval);
				}
				
				var checked = $this.prop('checked');
				checked = (options.invert) ? !checked : checked;
				try {
					CKEDITOR.instances[options.target].setReadOnly(!checked);
				} catch (err) {
					interval = setInterval(function() {
						CKEDITOR.instances[options.target].setReadOnly(!checked);
						clearInterval(interval);
					}, 100);
				}
				
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize function of the extension, called by the engine.
		 */
		module.init = function(done) {
			$this.on('change', _switchCkEdit);
			_switchCkEdit();
			done();
		};
		
		// Return data to module engine.
		return module;
		
	});
