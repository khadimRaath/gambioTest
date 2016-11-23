'use strict';

/* --------------------------------------------------------------
 language_selection.js 2016-06-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module('language_selection', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * On Language Link Click
  *
  * Prevent the default link behavior and regenerate the correct URL by taking into concern the dynamic
  * GET parameters (e.g. from table filtering).
  *
  * @param {jQuery.Event} event
  */
	function _onClickLanguageLink(event) {
		event.preventDefault();

		var currentUrlParameters = $.deparam(window.location.search.slice(1));

		currentUrlParameters.language = $(this).data('languageCode');

		window.location.href = window.location.pathname + '?' + $.param(currentUrlParameters);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', 'a', _onClickLanguageLink);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9mb290ZXIvbGFuZ3VhZ2Vfc2VsZWN0aW9uLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiX29uQ2xpY2tMYW5ndWFnZUxpbmsiLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiY3VycmVudFVybFBhcmFtZXRlcnMiLCJkZXBhcmFtIiwid2luZG93IiwibG9jYXRpb24iLCJzZWFyY2giLCJzbGljZSIsImxhbmd1YWdlIiwiaHJlZiIsInBhdGhuYW1lIiwicGFyYW0iLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUFBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixvQkFBdEIsRUFBNEMsRUFBNUMsRUFBZ0QsVUFBU0MsSUFBVCxFQUFlOztBQUU5RDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7Ozs7O0FBUUEsVUFBU0ksb0JBQVQsQ0FBOEJDLEtBQTlCLEVBQXFDO0FBQ3BDQSxRQUFNQyxjQUFOOztBQUVBLE1BQU1DLHVCQUF1QkosRUFBRUssT0FBRixDQUFVQyxPQUFPQyxRQUFQLENBQWdCQyxNQUFoQixDQUF1QkMsS0FBdkIsQ0FBNkIsQ0FBN0IsQ0FBVixDQUE3Qjs7QUFFQUwsdUJBQXFCTSxRQUFyQixHQUFnQ1YsRUFBRSxJQUFGLEVBQVFGLElBQVIsQ0FBYSxjQUFiLENBQWhDOztBQUVBUSxTQUFPQyxRQUFQLENBQWdCSSxJQUFoQixHQUF1QkwsT0FBT0MsUUFBUCxDQUFnQkssUUFBaEIsR0FBMkIsR0FBM0IsR0FBaUNaLEVBQUVhLEtBQUYsQ0FBUVQsb0JBQVIsQ0FBeEQ7QUFDQTs7QUFFRDtBQUNBO0FBQ0E7O0FBRUFQLFFBQU9pQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCaEIsUUFBTWlCLEVBQU4sQ0FBUyxPQUFULEVBQWtCLEdBQWxCLEVBQXVCZixvQkFBdkI7QUFDQWM7QUFDQSxFQUhEOztBQUtBLFFBQU9sQixNQUFQO0FBRUEsQ0F2REQiLCJmaWxlIjoibGF5b3V0cy9tYWluL2Zvb3Rlci9sYW5ndWFnZV9zZWxlY3Rpb24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gbGFuZ3VhZ2Vfc2VsZWN0aW9uLmpzIDIwMTYtMDYtMDNcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5neC5jb250cm9sbGVycy5tb2R1bGUoJ2xhbmd1YWdlX3NlbGVjdGlvbicsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIExhbmd1YWdlIExpbmsgQ2xpY2tcclxuXHQgKlxyXG5cdCAqIFByZXZlbnQgdGhlIGRlZmF1bHQgbGluayBiZWhhdmlvciBhbmQgcmVnZW5lcmF0ZSB0aGUgY29ycmVjdCBVUkwgYnkgdGFraW5nIGludG8gY29uY2VybiB0aGUgZHluYW1pY1xyXG5cdCAqIEdFVCBwYXJhbWV0ZXJzIChlLmcuIGZyb20gdGFibGUgZmlsdGVyaW5nKS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkNsaWNrTGFuZ3VhZ2VMaW5rKGV2ZW50KSB7XHJcblx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHJcblx0XHRjb25zdCBjdXJyZW50VXJsUGFyYW1ldGVycyA9ICQuZGVwYXJhbSh3aW5kb3cubG9jYXRpb24uc2VhcmNoLnNsaWNlKDEpKTtcclxuXHRcdFxyXG5cdFx0Y3VycmVudFVybFBhcmFtZXRlcnMubGFuZ3VhZ2UgPSAkKHRoaXMpLmRhdGEoJ2xhbmd1YWdlQ29kZScpO1xyXG5cdFx0XHJcblx0XHR3aW5kb3cubG9jYXRpb24uaHJlZiA9IHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZSArICc/JyArICQucGFyYW0oY3VycmVudFVybFBhcmFtZXRlcnMpO1xyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXMub24oJ2NsaWNrJywgJ2EnLCBfb25DbGlja0xhbmd1YWdlTGluayk7XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG5cdFxyXG59KTsgIl19
