/* --------------------------------------------------------------
 depending_selects.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Depending Selects Extension
 *
 * Extension that fills other dropdowns with data that relate with the value of the
 * dropdown the listener is bound on.
 *
 * @module Admin/Extensions/depending_selects
 * 
 * @deprecated Since JS Engine v1.3
 * 
 * @ignore
 */
gx.extensions.module(
	'depending_selects',
	
	['form', 'fallback'],
	
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
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {
				'cache': false, // Cache requested data, so that an ajax is only called once
				'requestOnInit': true // Update the values on init
			},
			
			/**
			 * Cache Object
			 *
			 * @type {object}
			 */
			cache = {},
			
			/**
			 * Final Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		/**
		 * Generate Options
		 *
		 * Function that generates the option fields for the "other" dropdowns
		 *
		 * @param {object} dataset Data given by the AJAX-call.
		 */
		var _generateOptions = function(dataset) {
			$.each(dataset, function(index, value) {
				var $select = $this.find(index);
				$select.empty();
				jse.libs.form.createOptions($select, value, false, false);
			});
		};
		
		/**
		 * Change Handler
		 *
		 * Event handler for the change-event on the main dropdown.
		 */
		var _changeHandler = function() {
			var $self = $(this),
				$option = $self.children(':selected'),
				dataset = jse.libs.fallback._data($option, 'depending_selects');
			
			if (cache[dataset.url]) {
				// Use cached data if available
				_generateOptions(cache[dataset.url]);
			} else if (dataset.url) {
				// If an URL is given, request the data via an AJAX-call.
				$.get(dataset.url).done(function(result) {
					if (result.success) {
						if (options.cache) {
							// Cache the data if the option is given
							cache[dataset.url] = result.data;
						}
						_generateOptions(result.data);
					}
				});
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize function of the extension, called by the engine.
		 */
		module.init = function(done) {
			// Display Deprecation Mark
			jse.core.debug.warn('The "depending_selects" extension is deprecated as of v1.3.0, do not use it '
				+ 'on new pages.');
			
			// Bind the change handler on the main dropdown object.
			var $source = $this.find(options.target);
			$source.on('change', _changeHandler);
			
			// Sets the values of the other dropdowns.
			if (options.requestOnInit) {
				$source.trigger('change', []);
			}
			
			done();
		};
		
		// Return data to module engine
		return module;
	});
