'use strict';

/* --------------------------------------------------------------
 order_billsafe.js 2015-10-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## BillSAFE Payment Details on Order Page
 *
 * This module add the billsafe payment informationen to the order details page.
 *
 * @module Compatibility/order_billsafe
 */
gx.compatibility.module('order_billsafe', [],

/**  @lends module:Compatibility/order_billsafe */

function (data) {

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
	defaults = {},


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

		if ($('.billsafe').length > 0) {
			$this.append($('.billsafe'));
			$this.parents('.content:first').show();
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcl9iaWxsc2FmZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJsZW5ndGgiLCJhcHBlbmQiLCJwYXJlbnRzIiwic2hvdyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsZ0JBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7OztBQUtBRCxVQUFTLEVBM0JWOztBQTZCQTtBQUNBO0FBQ0E7O0FBRUFBLFFBQU9PLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCLE1BQUlMLEVBQUUsV0FBRixFQUFlTSxNQUFmLEdBQXdCLENBQTVCLEVBQStCO0FBQzlCUCxTQUFNUSxNQUFOLENBQWFQLEVBQUUsV0FBRixDQUFiO0FBQ0FELFNBQU1TLE9BQU4sQ0FBYyxnQkFBZCxFQUFnQ0MsSUFBaEM7QUFDQTs7QUFFREo7QUFDQSxFQVJEOztBQVVBLFFBQU9SLE1BQVA7QUFDQSxDQTNERiIsImZpbGUiOiJvcmRlcnMvb3JkZXJfYmlsbHNhZmUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIG9yZGVyX2JpbGxzYWZlLmpzIDIwMTUtMTAtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEJpbGxTQUZFIFBheW1lbnQgRGV0YWlscyBvbiBPcmRlciBQYWdlXG4gKlxuICogVGhpcyBtb2R1bGUgYWRkIHRoZSBiaWxsc2FmZSBwYXltZW50IGluZm9ybWF0aW9uZW4gdG8gdGhlIG9yZGVyIGRldGFpbHMgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvb3JkZXJfYmlsbHNhZmVcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdvcmRlcl9iaWxsc2FmZScsXG5cdFxuXHRbXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L29yZGVyX2JpbGxzYWZlICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdGlmICgkKCcuYmlsbHNhZmUnKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdCR0aGlzLmFwcGVuZCgkKCcuYmlsbHNhZmUnKSk7XG5cdFx0XHRcdCR0aGlzLnBhcmVudHMoJy5jb250ZW50OmZpcnN0Jykuc2hvdygpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
