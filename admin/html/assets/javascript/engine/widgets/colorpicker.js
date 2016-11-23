'use strict';

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
gx.widgets.module('colorpicker', [], function (data) {

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
	module.init = function (done) {
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
			'onSubmit': function onSubmit(result) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbG9ycGlja2VyLmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYnV0dG9uIiwiJHByZXZpZXciLCIkaW5wdXQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsImZpbmQiLCJ2YWwiLCJjb2xvciIsImNvbHBpY2siLCJqc2UiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsInJlc3VsdCIsImhleCIsImhzYlRvSGV4IiwiY3NzIiwidHJpZ2dlciIsImNvbHBpY2tIaWRlIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBZ0NBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxhQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxXQUFVLElBYlg7OztBQWVDOzs7OztBQUtBQyxZQUFXLElBcEJaOzs7QUFzQkM7Ozs7O0FBS0FDLFVBQVMsSUEzQlY7OztBQTZCQzs7Ozs7QUFLQUMsWUFBVztBQUNWLFdBQVMsU0FEQyxDQUNTO0FBRFQsRUFsQ1o7OztBQXNDQzs7Ozs7QUFLQUMsV0FBVUwsRUFBRU0sTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2Qk4sSUFBN0IsQ0EzQ1g7OztBQTZDQzs7Ozs7QUFLQUQsVUFBUyxFQWxEVjs7QUFvREE7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUEsUUFBT1UsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QlAsWUFBVUYsTUFBTVUsSUFBTixDQUFXLFNBQVgsQ0FBVjtBQUNBUCxhQUFXSCxNQUFNVSxJQUFOLENBQVcsZ0JBQVgsQ0FBWDtBQUNBTixXQUFTSixNQUFNVSxJQUFOLENBQVcsc0JBQVgsQ0FBVDs7QUFFQSxNQUFJTixPQUFPTyxHQUFQLEVBQUosRUFBa0I7QUFDakJMLFdBQVFNLEtBQVIsR0FBZ0JSLE9BQU9PLEdBQVAsRUFBaEI7QUFDQTs7QUFFRDtBQUNBVCxVQUFRVyxPQUFSLENBQWdCO0FBQ2YsaUJBQWNDLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLElBQXhCLEVBQThCLFNBQTlCLENBREM7QUFFZixZQUFTWCxRQUFRTSxLQUZGO0FBR2YsZUFBWSxrQkFBU00sTUFBVCxFQUFpQjtBQUM1QixRQUFJQyxNQUFNLE1BQU1sQixFQUFFWSxPQUFGLENBQVVPLFFBQVYsQ0FBbUJGLE1BQW5CLENBQWhCO0FBQ0FmLGFBQVNrQixHQUFULENBQWEsa0JBQWIsRUFBaUNGLEdBQWpDO0FBQ0FmLFdBQU9PLEdBQVAsQ0FBV1EsR0FBWCxFQUFnQkcsT0FBaEIsQ0FBd0IsUUFBeEI7QUFDQXBCLFlBQVFxQixXQUFSO0FBQ0E7QUFSYyxHQUFoQjs7QUFXQTtBQUNBcEIsV0FBU2tCLEdBQVQsQ0FBYSxrQkFBYixFQUFpQ2YsUUFBUU0sS0FBekM7QUFDQVIsU0FBT08sR0FBUCxDQUFXTCxRQUFRTSxLQUFuQjs7QUFFQUg7QUFDQSxFQTFCRDs7QUE0QkE7QUFDQSxRQUFPWCxNQUFQO0FBQ0EsQ0F0R0YiLCJmaWxlIjoiY29sb3JwaWNrZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNvbG9ycGlja2VyLmpzIDIwMTYtMDQtMDFcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIENvbG9ycGlja2VyIFdpZGdldFxuICpcbiAqIFVzZSB0aGlzIHdpZGdldCB0byBhZGQgYSBjb2xvcnBpY2tlciB0byBhIHNwZWNpZmljIGA8ZGl2PmAgZWxlbWVudC5cbiAqIFxuICogalF1ZXJ5IENvbHBpY2sgV2Vic2l0ZToge0BsaW5rIGh0dHBzOi8vZ2l0aHViLmNvbS9tcmdyYWluL2NvbHBpY2t9XG4gKlxuICogIyMjIE9wdGlvbnNcbiAqXG4gKiAqKkNvbG9yIHwgYGRhdGEtY29sb3JwaWNrZXItY29sb3JgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxuICpcbiAqIFByb3ZpZGUgdGhlIGRlZmF1bHQgY29sb3IgZm9yIHRoZSBjb2xvciBwaWNrZXIuIElmIG5vIHZhbHVlIGlzIHByb3ZpZGVkLCBpdCBkZWZhdWx0c1xuICogdG8gYCcjZmZmZmZmJ2AuIFxuICpcbiAqICMjIyBFeGFtcGxlXG4gKlxuICogYGBgaHRtbFxuICogPGRpdiBkYXRhLWd4LXdpZGdldD1cImNvbG9ycGlja2VyXCJcbiAqICAgICBkYXRhLWNvbG9ycGlja2VyLWNvbG9yPVwiIzU1NWRmYVwiPlxuICogICA8YnV0dG9uIGNsYXNzPVwiYnRuIHBpY2tlclwiPlNlbGVjdCBDb2xvcjwvYnV0dG9uPlxuICogICA8c3Ryb25nIGNsYXNzPVwiY29sb3ItcHJldmlld1wiPkNvbG9yIFByZXZpZXc8L3N0cm9uZz5cbiAqICAgPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBpZD1cImNvbG9yLXZhbHVlXCIgLz5cbiAqIDwvZGl2PlxuICogYGBgXG4gKlxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL2NvbG9ycGlja2VyXG4gKiBAcmVxdWlyZXMgalF1ZXJ5LUNvbHBpY2stUGx1Z2luXG4gKlxuICogQHRvZG8gUmVwbGFjZSB0aGUgZ2xvYmFsLWNvbG9ycGlja2VyLmNzcyB3aXRoIHRoZSBvbmUgZnJvbSBib3dlciBjb21wb25lbnRzXG4gKiBAdG9kbyBUaGUgJHByZXZpZXcgc2VsZWN0b3IgbXVzdCBiZSBzZXQgZHluYW1pY2FsbHkgdGhyb3VnaCBhbiBvcHRpb24uXG4gKlxuICovXG5neC53aWRnZXRzLm1vZHVsZShcblx0J2NvbG9ycGlja2VyJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIFdpZGdldCBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQnV0dG9uIEVsZW1lbnQgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkYnV0dG9uID0gbnVsbCxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBQcmV2aWV3IEVsZW1lbnQgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkcHJldmlldyA9IG51bGwsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogSW5wdXQgRWxlbWVudCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRpbnB1dCA9IG51bGwsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zIGZvciBXaWRnZXRcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0J2NvbG9yJzogJyNmZmZmZmYnIC8vIERlZmF1bHQgY29sb3Jcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCRidXR0b24gPSAkdGhpcy5maW5kKCcucGlja2VyJyk7XG5cdFx0XHQkcHJldmlldyA9ICR0aGlzLmZpbmQoJy5jb2xvci1wcmV2aWV3Jyk7XG5cdFx0XHQkaW5wdXQgPSAkdGhpcy5maW5kKCdpbnB1dFt0eXBlPVwiaGlkZGVuXCJdJyk7XG5cdFx0XHRcblx0XHRcdGlmICgkaW5wdXQudmFsKCkpIHtcblx0XHRcdFx0b3B0aW9ucy5jb2xvciA9ICRpbnB1dC52YWwoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gRW5hYmxlcyB0aGUgY29sb3JwaWNrZXIuXG5cdFx0XHQkYnV0dG9uLmNvbHBpY2soe1xuXHRcdFx0XHQnc3VibWl0VGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdvaycsICdidXR0b25zJyksXG5cdFx0XHRcdCdjb2xvcic6IG9wdGlvbnMuY29sb3IsXG5cdFx0XHRcdCdvblN1Ym1pdCc6IGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRcdHZhciBoZXggPSAnIycgKyAkLmNvbHBpY2suaHNiVG9IZXgocmVzdWx0KTtcblx0XHRcdFx0XHQkcHJldmlldy5jc3MoJ2JhY2tncm91bmQtY29sb3InLCBoZXgpO1xuXHRcdFx0XHRcdCRpbnB1dC52YWwoaGV4KS50cmlnZ2VyKCdjaGFuZ2UnKTtcblx0XHRcdFx0XHQkYnV0dG9uLmNvbHBpY2tIaWRlKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBTZXRzIHRoZSBkZWZhdWx0IHZhbHVlcyBpbiB2aWV3LlxuXHRcdFx0JHByZXZpZXcuY3NzKCdiYWNrZ3JvdW5kLWNvbG9yJywgb3B0aW9ucy5jb2xvcik7XG5cdFx0XHQkaW5wdXQudmFsKG9wdGlvbnMuY29sb3IpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
