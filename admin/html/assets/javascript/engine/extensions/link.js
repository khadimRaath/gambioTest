'use strict';

/* --------------------------------------------------------------
 link.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals getSelection */

/**
 * ## Link Extension
 *
 * Use this extension to simulate any HTML element as an `<a>` link. Whenever the user clicks that element 
 * he will be navigated into the target page as if he was clicking an `<a>` element.
 *
 * This module requires one extra option which will define the target URL to be used when navigating to 
 * the next page. Provide it in the same element as in the following example. 
 * 
 * ### Options
 * 
 * **URL | data-link-url | String | Required** 
 * 
 * The destination URL to be used after the user clicks on the element.
 * 
 * ### Example
 * 
 * ```html 
 * <label data-gx-extension="link" data-link-url="http://gambio.de">Navigate To Official Website</label>
 * ```
 * 
 * @module Admin/Extensions/link
 */
gx.extensions.module('link', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		url: '#'
	},


	/**
  * Final Options
  *
  * @var {object}
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

	module.init = function (done) {

		$this.on('mouseup', function (event) {

			// 1 = left click, 2 = middle click
			if (event.which === 1 || event.which === 2) {
				event.preventDefault();
				event.stopPropagation();

				var target = event.which === 1 ? '_self' : '_blank';
				var sel = getSelection().toString();

				if (!sel) {
					window.open(options.url, target);
				}
			}
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpbmsuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwidXJsIiwib3B0aW9ucyIsImV4dGVuZCIsImluaXQiLCJkb25lIiwib24iLCJldmVudCIsIndoaWNoIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJ0YXJnZXQiLCJzZWwiLCJnZXRTZWxlY3Rpb24iLCJ0b1N0cmluZyIsIndpbmRvdyIsIm9wZW4iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUF1QkFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLE1BREQsRUFHQyxFQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVkMsT0FBSztBQURLLEVBYlo7OztBQWlCQzs7Ozs7QUFLQUMsV0FBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkgsSUFBN0IsQ0F0Qlg7OztBQXdCQzs7Ozs7QUFLQUQsVUFBUyxFQTdCVjs7QUErQkE7QUFDQTtBQUNBOztBQUVBQSxRQUFPUSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1QlAsUUFBTVEsRUFBTixDQUFTLFNBQVQsRUFBb0IsVUFBU0MsS0FBVCxFQUFnQjs7QUFFbkM7QUFDQSxPQUFJQSxNQUFNQyxLQUFOLEtBQWdCLENBQWhCLElBQXFCRCxNQUFNQyxLQUFOLEtBQWdCLENBQXpDLEVBQTRDO0FBQzNDRCxVQUFNRSxjQUFOO0FBQ0FGLFVBQU1HLGVBQU47O0FBRUEsUUFBSUMsU0FBVUosTUFBTUMsS0FBTixLQUFnQixDQUFqQixHQUFzQixPQUF0QixHQUFnQyxRQUE3QztBQUNBLFFBQUlJLE1BQU1DLGVBQWVDLFFBQWYsRUFBVjs7QUFFQSxRQUFJLENBQUNGLEdBQUwsRUFBVTtBQUNURyxZQUFPQyxJQUFQLENBQVlkLFFBQVFELEdBQXBCLEVBQXlCVSxNQUF6QjtBQUNBO0FBQ0Q7QUFFRCxHQWZEOztBQWlCQU47QUFDQSxFQXBCRDs7QUFzQkEsUUFBT1QsTUFBUDtBQUNBLENBdkVGIiwiZmlsZSI6ImxpbmsuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGxpbmsuanMgMjAxNS0wOS0yOSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qIGdsb2JhbHMgZ2V0U2VsZWN0aW9uICovXG5cbi8qKlxuICogIyMgTGluayBFeHRlbnNpb25cbiAqXG4gKiBVc2UgdGhpcyBleHRlbnNpb24gdG8gc2ltdWxhdGUgYW55IEhUTUwgZWxlbWVudCBhcyBhbiBgPGE+YCBsaW5rLiBXaGVuZXZlciB0aGUgdXNlciBjbGlja3MgdGhhdCBlbGVtZW50IFxuICogaGUgd2lsbCBiZSBuYXZpZ2F0ZWQgaW50byB0aGUgdGFyZ2V0IHBhZ2UgYXMgaWYgaGUgd2FzIGNsaWNraW5nIGFuIGA8YT5gIGVsZW1lbnQuXG4gKlxuICogVGhpcyBtb2R1bGUgcmVxdWlyZXMgb25lIGV4dHJhIG9wdGlvbiB3aGljaCB3aWxsIGRlZmluZSB0aGUgdGFyZ2V0IFVSTCB0byBiZSB1c2VkIHdoZW4gbmF2aWdhdGluZyB0byBcbiAqIHRoZSBuZXh0IHBhZ2UuIFByb3ZpZGUgaXQgaW4gdGhlIHNhbWUgZWxlbWVudCBhcyBpbiB0aGUgZm9sbG93aW5nIGV4YW1wbGUuIFxuICogXG4gKiAjIyMgT3B0aW9uc1xuICogXG4gKiAqKlVSTCB8IGRhdGEtbGluay11cmwgfCBTdHJpbmcgfCBSZXF1aXJlZCoqIFxuICogXG4gKiBUaGUgZGVzdGluYXRpb24gVVJMIHRvIGJlIHVzZWQgYWZ0ZXIgdGhlIHVzZXIgY2xpY2tzIG9uIHRoZSBlbGVtZW50LlxuICogXG4gKiAjIyMgRXhhbXBsZVxuICogXG4gKiBgYGBodG1sIFxuICogPGxhYmVsIGRhdGEtZ3gtZXh0ZW5zaW9uPVwibGlua1wiIGRhdGEtbGluay11cmw9XCJodHRwOi8vZ2FtYmlvLmRlXCI+TmF2aWdhdGUgVG8gT2ZmaWNpYWwgV2Vic2l0ZTwvbGFiZWw+XG4gKiBgYGBcbiAqIFxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2xpbmtcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCdsaW5rJyxcblx0XG5cdFtdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0dXJsOiAnIydcblx0XHRcdH0sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdCR0aGlzLm9uKCdtb3VzZXVwJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIDEgPSBsZWZ0IGNsaWNrLCAyID0gbWlkZGxlIGNsaWNrXG5cdFx0XHRcdGlmIChldmVudC53aGljaCA9PT0gMSB8fCBldmVudC53aGljaCA9PT0gMikge1xuXHRcdFx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0dmFyIHRhcmdldCA9IChldmVudC53aGljaCA9PT0gMSkgPyAnX3NlbGYnIDogJ19ibGFuayc7XG5cdFx0XHRcdFx0dmFyIHNlbCA9IGdldFNlbGVjdGlvbigpLnRvU3RyaW5nKCk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0aWYgKCFzZWwpIHtcblx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKG9wdGlvbnMudXJsLCB0YXJnZXQpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
