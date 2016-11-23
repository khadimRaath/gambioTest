'use strict';

/* --------------------------------------------------------------
 progress_bar.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Progress Bar Widget
 *
 * Enables the jQuery UI progress bar in the selected element. You can access the
 * progress value in your code, or set a value in the data-progressbar-value attribute.
 *
 * jQueryUI Progress Bar API: {@link https://api.jqueryui.com/progressbar}
 * 
 * ### Options
 *
 * **Value | `data-progressbar-value` | Number | Optional**
 *
 * The progress value of the progressbar. If no value is provided, it defaults to **0**.
 *
 * ### Example
 *
 *```html
 * <div data-gx-widget="progressbar" data-progressbar-value="50"></div>
 *```
 *
 * @module Admin/Widgets/progressbar
 * @requires jQueryUI-Library
 */
gx.widgets.module('progressbar', [],

/** @lends module:Widgets/progressbar */

function (data) {

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
		value: 0
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
		$this.progressbar(options);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2dyZXNzYmFyLmpzIl0sIm5hbWVzIjpbImd4Iiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsInZhbHVlIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwicHJvZ3Jlc3NiYXIiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF1QkFBLEdBQUdDLE9BQUgsQ0FBV0MsTUFBWCxDQUNDLGFBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXO0FBQ1ZDLFNBQU87QUFERyxFQWJaOzs7QUFpQkM7Ozs7O0FBS0FDLFdBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkgsUUFBbkIsRUFBNkJILElBQTdCLENBdEJYOzs7QUF3QkM7Ozs7O0FBS0FELFVBQVMsRUE3QlY7O0FBK0JBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FBLFFBQU9RLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJQLFFBQU1RLFdBQU4sQ0FBa0JKLE9BQWxCO0FBQ0FHO0FBQ0EsRUFIRDs7QUFLQTtBQUNBLFFBQU9ULE1BQVA7QUFDQSxDQTVERiIsImZpbGUiOiJwcm9ncmVzc2Jhci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZ3Jlc3NfYmFyLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFByb2dyZXNzIEJhciBXaWRnZXRcbiAqXG4gKiBFbmFibGVzIHRoZSBqUXVlcnkgVUkgcHJvZ3Jlc3MgYmFyIGluIHRoZSBzZWxlY3RlZCBlbGVtZW50LiBZb3UgY2FuIGFjY2VzcyB0aGVcbiAqIHByb2dyZXNzIHZhbHVlIGluIHlvdXIgY29kZSwgb3Igc2V0IGEgdmFsdWUgaW4gdGhlIGRhdGEtcHJvZ3Jlc3NiYXItdmFsdWUgYXR0cmlidXRlLlxuICpcbiAqIGpRdWVyeVVJIFByb2dyZXNzIEJhciBBUEk6IHtAbGluayBodHRwczovL2FwaS5qcXVlcnl1aS5jb20vcHJvZ3Jlc3NiYXJ9XG4gKiBcbiAqICMjIyBPcHRpb25zXG4gKlxuICogKipWYWx1ZSB8IGBkYXRhLXByb2dyZXNzYmFyLXZhbHVlYCB8IE51bWJlciB8IE9wdGlvbmFsKipcbiAqXG4gKiBUaGUgcHJvZ3Jlc3MgdmFsdWUgb2YgdGhlIHByb2dyZXNzYmFyLiBJZiBubyB2YWx1ZSBpcyBwcm92aWRlZCwgaXQgZGVmYXVsdHMgdG8gKiowKiouXG4gKlxuICogIyMjIEV4YW1wbGVcbiAqXG4gKmBgYGh0bWxcbiAqIDxkaXYgZGF0YS1neC13aWRnZXQ9XCJwcm9ncmVzc2JhclwiIGRhdGEtcHJvZ3Jlc3NiYXItdmFsdWU9XCI1MFwiPjwvZGl2PlxuICpgYGBcbiAqXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvcHJvZ3Jlc3NiYXJcbiAqIEByZXF1aXJlcyBqUXVlcnlVSS1MaWJyYXJ5XG4gKi9cbmd4LndpZGdldHMubW9kdWxlKFxuXHQncHJvZ3Jlc3NiYXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogQGxlbmRzIG1vZHVsZTpXaWRnZXRzL3Byb2dyZXNzYmFyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZVxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdHZhbHVlOiAwXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIFdpZGdldCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdGlhbGl6ZSBtZXRob2Qgb2YgdGhlIHdpZGdldCwgY2FsbGVkIGJ5IHRoZSBlbmdpbmUuXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQkdGhpcy5wcm9ncmVzc2JhcihvcHRpb25zKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
