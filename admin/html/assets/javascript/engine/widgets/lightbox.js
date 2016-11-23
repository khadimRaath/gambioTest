'use strict';

/* --------------------------------------------------------------
 lightbox.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Lightbox Widget
 *
 * Widget to easily configure and open lightboxes.
 *
 * Lightbox Project Website: {@link http://lokeshdhakar.com/projects/lightbox2}
 *
 * **Notice:** This widget is used by some old pages. The use of lightboxes in new pages is not suggested and 
 * instead you should use the Bootstrap modals.
 * 
 * @module Admin/Widgets/lightbox
 * @requires Lightbox-Library
 * @ignore
 */
gx.widgets.module('lightbox', ['fallback'], function (data) {

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
  * Default Options for Widget
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
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	/**
  * Click handler that opens the lightbox and initializes the default behavior.
  *
  * @param {object} event jQuery-event-object
  */
	var _clickHandler = function _clickHandler(event) {
		event.preventDefault();
		event.stopPropagation();

		var $self = $(this),
		    dataset = jse.libs.fallback._data($self, 'lightbox'),
		    settingDataSet = {},
		    paramDataSet = {};

		$.each(dataset, function (key, value) {
			if (key.indexOf('setting') === 0) {
				settingDataSet[key.replace('setting_', '')] = value;
			} else {
				paramDataSet[key.replace('param_', '')] = value;
			}
		});

		$self.lightbox_plugin(settingDataSet, paramDataSet);
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		jse.core.debug.warn('The "lightbox" widget is deprecated as of v1.3. Use the jQueryUI dialog ' + 'method instead.');

		$this.on('click', '.open_lightbox', _clickHandler);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpZ2h0Ym94LmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2xpY2tIYW5kbGVyIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsInN0b3BQcm9wYWdhdGlvbiIsIiRzZWxmIiwiZGF0YXNldCIsImpzZSIsImxpYnMiLCJmYWxsYmFjayIsIl9kYXRhIiwic2V0dGluZ0RhdGFTZXQiLCJwYXJhbURhdGFTZXQiLCJlYWNoIiwia2V5IiwidmFsdWUiLCJpbmRleE9mIiwicmVwbGFjZSIsImxpZ2h0Ym94X3BsdWdpbiIsImluaXQiLCJkb25lIiwiY29yZSIsImRlYnVnIiwid2FybiIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7O0FBY0FBLEdBQUdDLE9BQUgsQ0FBV0MsTUFBWCxDQUNDLFVBREQsRUFHQyxDQUFDLFVBQUQsQ0FIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7O0FBS0EsS0FBSU8sZ0JBQWdCLFNBQWhCQSxhQUFnQixDQUFTQyxLQUFULEVBQWdCO0FBQ25DQSxRQUFNQyxjQUFOO0FBQ0FELFFBQU1FLGVBQU47O0FBRUEsTUFBSUMsUUFBUVIsRUFBRSxJQUFGLENBQVo7QUFBQSxNQUNDUyxVQUFVQyxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLEtBQWxCLENBQXdCTCxLQUF4QixFQUErQixVQUEvQixDQURYO0FBQUEsTUFFQ00saUJBQWlCLEVBRmxCO0FBQUEsTUFHQ0MsZUFBZSxFQUhoQjs7QUFLQWYsSUFBRWdCLElBQUYsQ0FBT1AsT0FBUCxFQUFnQixVQUFTUSxHQUFULEVBQWNDLEtBQWQsRUFBcUI7QUFDcEMsT0FBSUQsSUFBSUUsT0FBSixDQUFZLFNBQVosTUFBMkIsQ0FBL0IsRUFBa0M7QUFDakNMLG1CQUFlRyxJQUFJRyxPQUFKLENBQVksVUFBWixFQUF3QixFQUF4QixDQUFmLElBQThDRixLQUE5QztBQUNBLElBRkQsTUFFTztBQUNOSCxpQkFBYUUsSUFBSUcsT0FBSixDQUFZLFFBQVosRUFBc0IsRUFBdEIsQ0FBYixJQUEwQ0YsS0FBMUM7QUFDQTtBQUNELEdBTkQ7O0FBUUFWLFFBQU1hLGVBQU4sQ0FBc0JQLGNBQXRCLEVBQXNDQyxZQUF0QztBQUNBLEVBbEJEOztBQW9CQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBbEIsUUFBT3lCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJiLE1BQUljLElBQUosQ0FBU0MsS0FBVCxDQUFlQyxJQUFmLENBQW9CLDZFQUNsQixpQkFERjs7QUFHQTNCLFFBQU00QixFQUFOLENBQVMsT0FBVCxFQUFrQixnQkFBbEIsRUFBb0N2QixhQUFwQztBQUNBbUI7QUFDQSxFQU5EOztBQVFBO0FBQ0EsUUFBTzFCLE1BQVA7QUFDQSxDQXhGRiIsImZpbGUiOiJsaWdodGJveC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbGlnaHRib3guanMgMjAxNi0wMi0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgTGlnaHRib3ggV2lkZ2V0XG4gKlxuICogV2lkZ2V0IHRvIGVhc2lseSBjb25maWd1cmUgYW5kIG9wZW4gbGlnaHRib3hlcy5cbiAqXG4gKiBMaWdodGJveCBQcm9qZWN0IFdlYnNpdGU6IHtAbGluayBodHRwOi8vbG9rZXNoZGhha2FyLmNvbS9wcm9qZWN0cy9saWdodGJveDJ9XG4gKlxuICogKipOb3RpY2U6KiogVGhpcyB3aWRnZXQgaXMgdXNlZCBieSBzb21lIG9sZCBwYWdlcy4gVGhlIHVzZSBvZiBsaWdodGJveGVzIGluIG5ldyBwYWdlcyBpcyBub3Qgc3VnZ2VzdGVkIGFuZCBcbiAqIGluc3RlYWQgeW91IHNob3VsZCB1c2UgdGhlIEJvb3RzdHJhcCBtb2RhbHMuXG4gKiBcbiAqIEBtb2R1bGUgQWRtaW4vV2lkZ2V0cy9saWdodGJveFxuICogQHJlcXVpcmVzIExpZ2h0Ym94LUxpYnJhcnlcbiAqIEBpZ25vcmVcbiAqL1xuZ3gud2lkZ2V0cy5tb2R1bGUoXG5cdCdsaWdodGJveCcsXG5cdFxuXHRbJ2ZhbGxiYWNrJ10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIFdpZGdldFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgV2lkZ2V0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBDbGljayBoYW5kbGVyIHRoYXQgb3BlbnMgdGhlIGxpZ2h0Ym94IGFuZCBpbml0aWFsaXplcyB0aGUgZGVmYXVsdCBiZWhhdmlvci5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBldmVudCBqUXVlcnktZXZlbnQtb2JqZWN0XG5cdFx0ICovXG5cdFx0dmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRkYXRhc2V0ID0ganNlLmxpYnMuZmFsbGJhY2suX2RhdGEoJHNlbGYsICdsaWdodGJveCcpLFxuXHRcdFx0XHRzZXR0aW5nRGF0YVNldCA9IHt9LFxuXHRcdFx0XHRwYXJhbURhdGFTZXQgPSB7fTtcblx0XHRcdFxuXHRcdFx0JC5lYWNoKGRhdGFzZXQsIGZ1bmN0aW9uKGtleSwgdmFsdWUpIHtcblx0XHRcdFx0aWYgKGtleS5pbmRleE9mKCdzZXR0aW5nJykgPT09IDApIHtcblx0XHRcdFx0XHRzZXR0aW5nRGF0YVNldFtrZXkucmVwbGFjZSgnc2V0dGluZ18nLCAnJyldID0gdmFsdWU7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0cGFyYW1EYXRhU2V0W2tleS5yZXBsYWNlKCdwYXJhbV8nLCAnJyldID0gdmFsdWU7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkc2VsZi5saWdodGJveF9wbHVnaW4oc2V0dGluZ0RhdGFTZXQsIHBhcmFtRGF0YVNldCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXRpYWxpemUgbWV0aG9kIG9mIHRoZSB3aWRnZXQsIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0anNlLmNvcmUuZGVidWcud2FybignVGhlIFwibGlnaHRib3hcIiB3aWRnZXQgaXMgZGVwcmVjYXRlZCBhcyBvZiB2MS4zLiBVc2UgdGhlIGpRdWVyeVVJIGRpYWxvZyAnIFxuXHRcdFx0XHQrJ21ldGhvZCBpbnN0ZWFkLicpOyBcblx0XHRcdFxuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgJy5vcGVuX2xpZ2h0Ym94JywgX2NsaWNrSGFuZGxlcik7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byBtb2R1bGUgZW5naW5lLlxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
