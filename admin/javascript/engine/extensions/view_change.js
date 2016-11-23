/* --------------------------------------------------------------
 view_change.js 2016-06-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## View Change Extension
 *
 * Use this extension to show or hide elements depending the state of a input-checkbox element. The extension
 * needs to be bound directly on the checkbox element. It requires two jQuery selector parameters that point
 * the elements that will be displayed when the checkbox is checked and when it isn't.
 * 
 * ### Options 
 * 
 * **On State Selector | `data-view_change-on` | String | Required** 
 * 
 * Define a jQuery selector that selects the elements to be displayed when the checkbox is checked.
 * 
 * **Off State Selector | `data-view_change-off` | String | Required**
 *
 * Define a jQuery selector that selects the elements to be displayed when the checkbox is unchecked (required).
 * 
 * **Closest Parent Selector | `data-view_change-closest` | String | Optional**
 *
 * Use this jQuery selector to specify which "closest" element will be the parent of the element search. This 
 * option can be useful for shrinking the search scope within a single parent container and not the whole page 
 * body.
 * 
 * ### Example 
 *
 * In the following example only the labels that reside inside the div.container element will be affected by the 
 * checkbox state. The label outside the container will always be visible.
 * 
 * ```html 
 * <div class="container">
 *   <input type="checkbox" data-gx-extension="view_change"
 *     data-view_change-on=".label-primary"
 *     data-view_change-off=".label-secondary"
 *     data-view_change-closest=".container" />
 *   <label class="label-primary">Test Label - Primary</label>
 *   <label class="label-secondary">Test Label - Secondary</label>  
 * </div>
 * 
 * <label class="label-primary">Test Label - Primary</label>  
 * ```
 * 
 * @module Admin/Extensions/view_change
 */
gx.extensions.module(
	'view_change',
	
	[],
	
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
			 * Parent Selector (default body)
			 *
			 * @type {object}
			 */
			$parent = $('body'),
			
			/**
			 * Default Options for Extension
			 *
			 * @type {object}
			 */
			defaults = {
				// @todo Rename this option to activeSelector
				on: null, // Selector for the elements that are shown if the checkbox is set
				// @todo Rename this option to inactiveSelector
				off: null, // Selector for the elements that are shown if the checkbox is not set
				// @todo Rename this option to parentSelector
				closest: null // Got to the closest X-element and search inside it for the views
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
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		/**
		 * Shows or hides elements corresponding to the checkbox state.
		 */
		var _changeHandler = function() {
			if ($this.prop('checked')) {
				$parent.find(options.on).show();
				$parent.find(options.off).hide();
				$this.attr('checked', 'checked');
			} else {
				$parent.find(options.on).hide();
				$parent.find(options.off).show();
				$this.removeAttr('checked');
			}
			
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the extension, called by the engine.
		 */
		module.init = function(done) {
			if (options.closest) {
				$parent = $this.closest(options.closest);
			}
			$this.on('change checkbox:change', _changeHandler);
			_changeHandler();
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
