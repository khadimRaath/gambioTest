'use strict';

/* --------------------------------------------------------------
 button.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Button Widget
 *
 * Enables the jQuery button functionality to an existing HTML element. By passing extra data
 * attributes you can specify additional options for the widget.
 *
 * jQueryUI Button API: {@link http://api.jqueryui.com/button}
 * 
 * ### Examples
 *
 * The following example will initialize the button with the jQuery UI API option "disabled". 
 * 
 * ```html
 * <button data-gx-widget="button" data-button-disabled="true">Disabled Button</button>
 * ```
 * 
 * Equals to ... 
 * 
 * ```js
 * $('button').button({ disabled: true });
 * ```
 *
 * The following example will initialize a button with custom jQuery UI icons by setting the "icons" option.
 * 
 * ```html
 * <button data-gx-widget="button" 
 *     data-button-icons='{ "primary": "ui-icon-triangle-1-s", "secondary": "ui-icon-triangle-1-s" }'>
 *   jQuery UI
 * </button>
 * ```
 * *Note that if you ever need to pass a JSON object as an option the value must be a 
 * [valid JSON string](https://en.wikipedia.org/wiki/JSON#Data_types.2C_syntax_and_example) 
 * otherwise the module will not parse it correctly.*
 * 
 * @deprecated Since v1.4, will be removed in v1.6. The jQuery button is not used in new admin pages.
 * 
 * @module Admin/Widgets/button
 * @requires jQueryUI-Library
 */
gx.widgets.module('button', [], function (data) {

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
	defaults = {},


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
		$this.button(options);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImJ1dHRvbi5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJidXR0b24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBdUNBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxRQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBQSxRQUFPTyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCTixRQUFNTyxNQUFOLENBQWFKLE9BQWI7QUFDQUc7QUFDQSxFQUhEOztBQUtBO0FBQ0EsUUFBT1IsTUFBUDtBQUNBLENBeERGIiwiZmlsZSI6ImJ1dHRvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYnV0dG9uLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEJ1dHRvbiBXaWRnZXRcbiAqXG4gKiBFbmFibGVzIHRoZSBqUXVlcnkgYnV0dG9uIGZ1bmN0aW9uYWxpdHkgdG8gYW4gZXhpc3RpbmcgSFRNTCBlbGVtZW50LiBCeSBwYXNzaW5nIGV4dHJhIGRhdGFcbiAqIGF0dHJpYnV0ZXMgeW91IGNhbiBzcGVjaWZ5IGFkZGl0aW9uYWwgb3B0aW9ucyBmb3IgdGhlIHdpZGdldC5cbiAqXG4gKiBqUXVlcnlVSSBCdXR0b24gQVBJOiB7QGxpbmsgaHR0cDovL2FwaS5qcXVlcnl1aS5jb20vYnV0dG9ufVxuICogXG4gKiAjIyMgRXhhbXBsZXNcbiAqXG4gKiBUaGUgZm9sbG93aW5nIGV4YW1wbGUgd2lsbCBpbml0aWFsaXplIHRoZSBidXR0b24gd2l0aCB0aGUgalF1ZXJ5IFVJIEFQSSBvcHRpb24gXCJkaXNhYmxlZFwiLiBcbiAqIFxuICogYGBgaHRtbFxuICogPGJ1dHRvbiBkYXRhLWd4LXdpZGdldD1cImJ1dHRvblwiIGRhdGEtYnV0dG9uLWRpc2FibGVkPVwidHJ1ZVwiPkRpc2FibGVkIEJ1dHRvbjwvYnV0dG9uPlxuICogYGBgXG4gKiBcbiAqIEVxdWFscyB0byAuLi4gXG4gKiBcbiAqIGBgYGpzXG4gKiAkKCdidXR0b24nKS5idXR0b24oeyBkaXNhYmxlZDogdHJ1ZSB9KTtcbiAqIGBgYFxuICpcbiAqIFRoZSBmb2xsb3dpbmcgZXhhbXBsZSB3aWxsIGluaXRpYWxpemUgYSBidXR0b24gd2l0aCBjdXN0b20galF1ZXJ5IFVJIGljb25zIGJ5IHNldHRpbmcgdGhlIFwiaWNvbnNcIiBvcHRpb24uXG4gKiBcbiAqIGBgYGh0bWxcbiAqIDxidXR0b24gZGF0YS1neC13aWRnZXQ9XCJidXR0b25cIiBcbiAqICAgICBkYXRhLWJ1dHRvbi1pY29ucz0neyBcInByaW1hcnlcIjogXCJ1aS1pY29uLXRyaWFuZ2xlLTEtc1wiLCBcInNlY29uZGFyeVwiOiBcInVpLWljb24tdHJpYW5nbGUtMS1zXCIgfSc+XG4gKiAgIGpRdWVyeSBVSVxuICogPC9idXR0b24+XG4gKiBgYGBcbiAqICpOb3RlIHRoYXQgaWYgeW91IGV2ZXIgbmVlZCB0byBwYXNzIGEgSlNPTiBvYmplY3QgYXMgYW4gb3B0aW9uIHRoZSB2YWx1ZSBtdXN0IGJlIGEgXG4gKiBbdmFsaWQgSlNPTiBzdHJpbmddKGh0dHBzOi8vZW4ud2lraXBlZGlhLm9yZy93aWtpL0pTT04jRGF0YV90eXBlcy4yQ19zeW50YXhfYW5kX2V4YW1wbGUpIFxuICogb3RoZXJ3aXNlIHRoZSBtb2R1bGUgd2lsbCBub3QgcGFyc2UgaXQgY29ycmVjdGx5LipcbiAqIFxuICogQGRlcHJlY2F0ZWQgU2luY2UgdjEuNCwgd2lsbCBiZSByZW1vdmVkIGluIHYxLjYuIFRoZSBqUXVlcnkgYnV0dG9uIGlzIG5vdCB1c2VkIGluIG5ldyBhZG1pbiBwYWdlcy5cbiAqIFxuICogQG1vZHVsZSBBZG1pbi9XaWRnZXRzL2J1dHRvblxuICogQHJlcXVpcmVzIGpRdWVyeVVJLUxpYnJhcnlcbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCdidXR0b24nLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXMuYnV0dG9uKG9wdGlvbnMpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
