/* --------------------------------------------------------------
 form_changes_checker.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## From Changes Checker Extension
 * 
 * Stores all form data inside $(this) an waits for an trigger to compare the data with the 
 * original. A, with the trigger delivered deferred object gets resolved or rejected depending 
 * on the result.
 *
 * @todo Create some jQuery selector methods so that it is easier to check if something was changed.
 * @todo The extension must add a 'changed' or 'updated' class to the form so that other modules or code can determine
 * directly that something was changed.
 * @todo If a value is changed inside a input/select/textarea element this plugin must automatically perform the check.
 * Currently it just waits for the consumers to call the 'formchanges.check' event.
 * 
 * @module Admin/Extensions/form_changes_checker
 * @ignore
 */
gx.extensions.module(
	'form_changes_checker',
	
	['form'],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
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
				'ignoreClass': '.ignore_changes'
			},
			
			/**
			 * Final Extension Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Initial Form Data
			 *
			 * @type {array}
			 * 
			 * @todo Replace the initial value to an object.
			 */
			formData = [],
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLER
		// ------------------------------------------------------------------------
		
		/**
		 * Check Forms
		 *
		 * Function to compare the original data with the data that is currently in the
		 * form. the given deferred object gets resolved or rejected.
		 *
		 * @param {object} event jQuery event object
		 * @param {object} deferred JSON object containing the deferred object.
		 */
		var _checkForms = function(event, deferred) {
			event.stopPropagation();
			
			deferred = deferred.deferred;
			
			var newData = jse.libs.form.getData($this, options.ignoreClass),
				cache = JSON.stringify(formData),
				current = JSON.stringify(newData),
				returnData = {
					'original': $.extend({}, formData),
					'current': $.extend({}, newData)
				};
			
			if (cache === current) {
				deferred.resolve(returnData);
			} else {
				deferred.reject(returnData);
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Init function of the extension, called by the engine.
		 */
		module.init = function(done) {
			
			formData = jse.libs.form.getData($this, options.ignoreClass);
			$this
				.on('formchanges.check', _checkForms)
				.on('formchanges.update', function() {
					// Updates the form data stored in cache
					formData = jse.libs.form.getData($this, options.ignoreClass);
				});
			
			$('body').on('formchanges.check', function(e, d) {
				// Event listener that performs on every formchanges.check trigger that isn't handled 
				// by the form_changes_checker
				if (d && d.deferred) {
					d.deferred.resolve();
				}
			});
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
