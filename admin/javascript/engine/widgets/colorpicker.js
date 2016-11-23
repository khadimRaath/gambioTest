/* --------------------------------------------------------------
 colorpicker.js 2016-04-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Colorpicker Widget
 *
 * Use this widget to add a colorpicker to a specific `<div>` element.
 * 
 * jQuery Colpick Website: {@link https://github.com/mrgrain/colpick}
 *
 * ### Options
 *
 * **Color | `data-colorpicker-color` | String | Optional**
 *
 * Provide the default color for the color picker. If no value is provided, it defaults
 * to `'#ffffff'`. 
 *
 * ### Example
 *
 * ```html
 * <div data-gx-widget="colorpicker"
 *     data-colorpicker-color="#555dfa">
 *   <button class="btn picker">Select Color</button>
 *   <strong class="color-preview">Color Preview</strong>
 *   <input type="hidden" id="color-value" />
 * </div>
 * ```
 *
 * @module Admin/Widgets/colorpicker
 * @requires jQuery-Colpick-Plugin
 *
 * @todo Replace the global-colorpicker.css with the one from bower components
 * @todo The $preview selector must be set dynamically through an option.
 *
 */
gx.widgets.module(
	'colorpicker',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Widget Reference
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Button Element Selector
			 *
			 * @type {object}
			 */
			$button = null,
			
			/**
			 * Preview Element Selector
			 *
			 * @type {object}
			 */
			$preview = null,
			
			/**
			 * Input Element Selector
			 *
			 * @type {object}
			 */
			$input = null,
			
			/**
			 * Default Options for Widget
			 *
			 * @type {object}
			 */
			defaults = {
				'color': '#ffffff' // Default color
			},
			
			/**
			 * Final Widget Options
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
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			$button = $this.find('.picker');
			$preview = $this.find('.color-preview');
			$input = $this.find('input[type="hidden"]');
			
			if ($input.val()) {
				options.color = $input.val();
			}
			
			// Enables the colorpicker.
			$button.colpick({
				'submitText': jse.core.lang.translate('ok', 'buttons'),
				'color': options.color,
				'onSubmit': function(result) {
					var hex = '#' + $.colpick.hsbToHex(result);
					$preview.css('background-color', hex);
					$input.val(hex).trigger('change');
					$button.colpickHide();
				}
			});
			
			// Sets the default values in view.
			$preview.css('background-color', options.color);
			$input.val(options.color);
			
			done();
		};
		
		// Return data to module engine.
		return module;
	});
