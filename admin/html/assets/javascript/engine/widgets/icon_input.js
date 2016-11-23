'use strict';

/* --------------------------------------------------------------
 icon_input.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Icon Input Widget
 *
 * Turns normal input fields into input fields with a provided background image.
 *
 * ### Example
 *
 * The "icon-input" activates the widget and attaches the needed styles for the background image
 * which is provided by the `data-icon` attribute.
 * 
 * ```html
 * <input data-gx-widget="icon_input" data-icon="url/to/image-file.png"/>
 * ```
 * 
 * @todo Add automatic image dimension adjustment. Images - for example if they are too big in dimensions - won't scale 
 * correctly at the moment. 
 * 
 * @module Admin/Widgets/icon_input
 */
gx.widgets.module('icon_input', [], function (data) {

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
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Adds the dropdown functionality to the button.
  *
  * Developers can manually add new <li> items to the list in order to display more options to
  * the users.
  *
  * @private
  */
	var _setBackgroundImage = function _setBackgroundImage() {
		var iconValue = $this.attr('data-icon');
		$this.css('background', 'url(' + iconValue + ')' + ' no-repeat right 8px center white');
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		_setBackgroundImage();
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImljb25faW5wdXQuanMiXSwibmFtZXMiOlsiZ3giLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRCYWNrZ3JvdW5kSW1hZ2UiLCJpY29uVmFsdWUiLCJhdHRyIiwiY3NzIiwiaW5pdCIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQW1CQUEsR0FBR0MsT0FBSCxDQUFXQyxNQUFYLENBRUMsWUFGRCxFQUlDLEVBSkQsRUFNQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUQsVUFBUyxFQTNCVjs7QUE2QkE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7OztBQVFBLEtBQUlPLHNCQUFzQixTQUF0QkEsbUJBQXNCLEdBQVc7QUFDcEMsTUFBSUMsWUFBWU4sTUFBTU8sSUFBTixDQUFXLFdBQVgsQ0FBaEI7QUFDQVAsUUFBTVEsR0FBTixDQUFVLFlBQVYsRUFBd0IsU0FBU0YsU0FBVCxHQUFxQixHQUFyQixHQUEyQixtQ0FBbkQ7QUFDQSxFQUhEOztBQUtBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FSLFFBQU9XLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJMO0FBQ0FLO0FBQ0EsRUFIRDs7QUFLQTtBQUNBLFFBQU9aLE1BQVA7QUFDQSxDQTFFRiIsImZpbGUiOiJpY29uX2lucHV0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpY29uX2lucHV0LmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEljb24gSW5wdXQgV2lkZ2V0XG4gKlxuICogVHVybnMgbm9ybWFsIGlucHV0IGZpZWxkcyBpbnRvIGlucHV0IGZpZWxkcyB3aXRoIGEgcHJvdmlkZWQgYmFja2dyb3VuZCBpbWFnZS5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIFRoZSBcImljb24taW5wdXRcIiBhY3RpdmF0ZXMgdGhlIHdpZGdldCBhbmQgYXR0YWNoZXMgdGhlIG5lZWRlZCBzdHlsZXMgZm9yIHRoZSBiYWNrZ3JvdW5kIGltYWdlXG4gKiB3aGljaCBpcyBwcm92aWRlZCBieSB0aGUgYGRhdGEtaWNvbmAgYXR0cmlidXRlLlxuICogXG4gKiBgYGBodG1sXG4gKiA8aW5wdXQgZGF0YS1neC13aWRnZXQ9XCJpY29uX2lucHV0XCIgZGF0YS1pY29uPVwidXJsL3RvL2ltYWdlLWZpbGUucG5nXCIvPlxuICogYGBgXG4gKiBcbiAqIEB0b2RvIEFkZCBhdXRvbWF0aWMgaW1hZ2UgZGltZW5zaW9uIGFkanVzdG1lbnQuIEltYWdlcyAtIGZvciBleGFtcGxlIGlmIHRoZXkgYXJlIHRvbyBiaWcgaW4gZGltZW5zaW9ucyAtIHdvbid0IHNjYWxlIFxuICogY29ycmVjdGx5IGF0IHRoZSBtb21lbnQuIFxuICogXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvaWNvbl9pbnB1dFxuICovXG5neC53aWRnZXRzLm1vZHVsZShcblx0XG5cdCdpY29uX2lucHV0Jyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIFdpZGdldCBSZWZlcmVuY2Vcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQWRkcyB0aGUgZHJvcGRvd24gZnVuY3Rpb25hbGl0eSB0byB0aGUgYnV0dG9uLlxuXHRcdCAqXG5cdFx0ICogRGV2ZWxvcGVycyBjYW4gbWFudWFsbHkgYWRkIG5ldyA8bGk+IGl0ZW1zIHRvIHRoZSBsaXN0IGluIG9yZGVyIHRvIGRpc3BsYXkgbW9yZSBvcHRpb25zIHRvXG5cdFx0ICogdGhlIHVzZXJzLlxuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3NldEJhY2tncm91bmRJbWFnZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGljb25WYWx1ZSA9ICR0aGlzLmF0dHIoJ2RhdGEtaWNvbicpO1xuXHRcdFx0JHRoaXMuY3NzKCdiYWNrZ3JvdW5kJywgJ3VybCgnICsgaWNvblZhbHVlICsgJyknICsgJyBuby1yZXBlYXQgcmlnaHQgOHB4IGNlbnRlciB3aGl0ZScpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdF9zZXRCYWNrZ3JvdW5kSW1hZ2UoKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
