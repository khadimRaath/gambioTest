/* --------------------------------------------------------------
 input_counter.js 2016-08-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Input Counter Widget
 *
 * Adds a counter element to input and textarea elements.
 *
 * ### Options:
 *
 * **Persistence | `data-input_counter-persistent` | bool | Optional**
 *
 * Omits to hide the counter element. This option is optional and the default value is true, so
 * the counter element is permanent displayed.
 *
 * **Pull | `data-input_counter-persistent` | bool/string | Optional**
 *
 * The option gives the possibility to pull the counter element to whether the right or left side.
 *
 * ### Example
 * ```html
 * <!-- Default input counter element -->
 * <input type="text" data-gx-widget="input_counter">
 * <textarea data-gx-widget="input_counter"></textarea>
 *
 * <!-- Show element on focus and hide on blur -->
 * <input type="text" data-input_counter-persistent="false">
 *
 * <!-- Disable counter pull -->
 * <input type="text" data-input_counter-pull="false">
 *
 * <!-- Pull counter to left side -->
 * <input type="text" data-input_counter-pull="left">
 * ```
 *
 * @module Admin/Widgets/input_counter
 */

gx.widgets.module(
	'input_counter',

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
			 * Default Widget Options
			 *
			 * @type {object}
			 */
			defaults = {
				persistent: true,
				pull: 'right'
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
			module = {},

			$counter = $('<span/>');

		var _showCharCounter = function(event) {
			$this.parent().append($counter);
			if (options.max) {
				$counter.text($this.val().length + '/' + options.max);
			}
			else {
				$counter.text($this.val().length);
			}
		};

		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------

		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			// check and set handling of persistent pull
			if (options.pull) {
				$counter.addClass('pull-' + options.pull);
			}

			// check and set handling of persistent option
			if (options.persistent) {
				_showCharCounter();
			}
			else {
				$this.focus(_showCharCounter);
				$this.blur(function() {
					$counter.remove();
				});
			}

			$this.on('keyup', _showCharCounter);
			done();
		};

		return module;
	}
);
