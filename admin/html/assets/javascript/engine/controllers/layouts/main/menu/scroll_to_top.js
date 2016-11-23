'use strict';

/* --------------------------------------------------------------
 scroll_to_top.js 2016-04-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Scroll to top functionality.
 */
gx.controllers.module('scroll_to_top', [], function () {

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

	/**
  * Animation Flag
  *
  * @type {Boolean}
  */
	var onAnimation = false;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * On Window Scroll
  *
  * If the site content is large and the user has scrolled to bottom display the caret icon.
  */
	function _onWindowScroll() {
		var scrollPercentage = ($(window).scrollTop() + window.innerHeight) / $(document).outerHeight();

		if (!onAnimation && !$('#main-menu > nav > ul').hasClass('collapse') && scrollPercentage > 0.9 && $(document).outerHeight() > 2500) {
			$this.fadeIn();
		} else if ($this.is(':visible')) {
			$this.fadeOut();
		}
	}

	/**
  * On Icon Click
  *
  * Scroll to the top of the page whenever the user clicks on the caret icon.
  */
	function _onIconClick() {
		onAnimation = true;

		$('html, body').animate({
			scrollTop: 0
		}, 'fast', function () {
			onAnimation = false;
		});

		$this.fadeOut();
	}

	// ------------------------------------------------------------------------
	// INITIALIZE
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$(window).on('scroll', _onWindowScroll);
		$this.on('click', 'i', _onIconClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9tZW51L3Njcm9sbF90b190b3AuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsIiR0aGlzIiwiJCIsIm9uQW5pbWF0aW9uIiwiX29uV2luZG93U2Nyb2xsIiwic2Nyb2xsUGVyY2VudGFnZSIsIndpbmRvdyIsInNjcm9sbFRvcCIsImlubmVySGVpZ2h0IiwiZG9jdW1lbnQiLCJvdXRlckhlaWdodCIsImhhc0NsYXNzIiwiZmFkZUluIiwiaXMiLCJmYWRlT3V0IiwiX29uSWNvbkNsaWNrIiwiYW5pbWF0ZSIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixlQUF0QixFQUF1QyxFQUF2QyxFQUEyQyxZQUFXOztBQUVyRDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1GLFNBQVMsRUFBZjs7QUFFQTs7Ozs7QUFLQSxLQUFJRyxjQUFjLEtBQWxCOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7QUFLQSxVQUFTQyxlQUFULEdBQTJCO0FBQzFCLE1BQUlDLG1CQUFtQixDQUFDSCxFQUFFSSxNQUFGLEVBQVVDLFNBQVYsS0FBd0JELE9BQU9FLFdBQWhDLElBQStDTixFQUFFTyxRQUFGLEVBQVlDLFdBQVosRUFBdEU7O0FBRUEsTUFBSSxDQUFDUCxXQUFELElBQWdCLENBQUNELEVBQUUsdUJBQUYsRUFBMkJTLFFBQTNCLENBQW9DLFVBQXBDLENBQWpCLElBQ0FOLG1CQUFtQixHQURuQixJQUMwQkgsRUFBRU8sUUFBRixFQUFZQyxXQUFaLEtBQTRCLElBRDFELEVBQ2dFO0FBQy9EVCxTQUFNVyxNQUFOO0FBQ0EsR0FIRCxNQUdPLElBQUlYLE1BQU1ZLEVBQU4sQ0FBUyxVQUFULENBQUosRUFBMEI7QUFDaENaLFNBQU1hLE9BQU47QUFDQTtBQUNEOztBQUVEOzs7OztBQUtBLFVBQVNDLFlBQVQsR0FBd0I7QUFDdkJaLGdCQUFjLElBQWQ7O0FBRUFELElBQUUsWUFBRixFQUFnQmMsT0FBaEIsQ0FBd0I7QUFDdkJULGNBQVc7QUFEWSxHQUF4QixFQUVHLE1BRkgsRUFFVyxZQUFXO0FBQ3JCSixpQkFBYyxLQUFkO0FBQ0EsR0FKRDs7QUFNQUYsUUFBTWEsT0FBTjtBQUNBOztBQUVEO0FBQ0E7QUFDQTs7QUFFQWQsUUFBT2lCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJoQixJQUFFSSxNQUFGLEVBQVVhLEVBQVYsQ0FBYSxRQUFiLEVBQXVCZixlQUF2QjtBQUNBSCxRQUFNa0IsRUFBTixDQUFTLE9BQVQsRUFBa0IsR0FBbEIsRUFBdUJKLFlBQXZCO0FBQ0FHO0FBQ0EsRUFKRDs7QUFNQSxRQUFPbEIsTUFBUDtBQUVBLENBOUVEIiwiZmlsZSI6ImxheW91dHMvbWFpbi9tZW51L3Njcm9sbF90b190b3AuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gc2Nyb2xsX3RvX3RvcC5qcyAyMDE2LTA0LTI1XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqIFNjcm9sbCB0byB0b3AgZnVuY3Rpb25hbGl0eS5cclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZSgnc2Nyb2xsX3RvX3RvcCcsIFtdLCBmdW5jdGlvbigpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge307XHJcblx0XHJcblx0LyoqXHJcblx0ICogQW5pbWF0aW9uIEZsYWdcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtCb29sZWFufVxyXG5cdCAqL1xyXG5cdGxldCBvbkFuaW1hdGlvbiA9IGZhbHNlO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIFdpbmRvdyBTY3JvbGxcclxuXHQgKlxyXG5cdCAqIElmIHRoZSBzaXRlIGNvbnRlbnQgaXMgbGFyZ2UgYW5kIHRoZSB1c2VyIGhhcyBzY3JvbGxlZCB0byBib3R0b20gZGlzcGxheSB0aGUgY2FyZXQgaWNvbi5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25XaW5kb3dTY3JvbGwoKSB7XHJcblx0XHRsZXQgc2Nyb2xsUGVyY2VudGFnZSA9ICgkKHdpbmRvdykuc2Nyb2xsVG9wKCkgKyB3aW5kb3cuaW5uZXJIZWlnaHQpIC8gJChkb2N1bWVudCkub3V0ZXJIZWlnaHQoKTtcclxuXHRcdFxyXG5cdFx0aWYgKCFvbkFuaW1hdGlvbiAmJiAhJCgnI21haW4tbWVudSA+IG5hdiA+IHVsJykuaGFzQ2xhc3MoJ2NvbGxhcHNlJylcclxuXHRcdFx0JiYgc2Nyb2xsUGVyY2VudGFnZSA+IDAuOSAmJiAkKGRvY3VtZW50KS5vdXRlckhlaWdodCgpID4gMjUwMCkge1xyXG5cdFx0XHQkdGhpcy5mYWRlSW4oKTtcclxuXHRcdH0gZWxzZSBpZiAoJHRoaXMuaXMoJzp2aXNpYmxlJykpIHtcclxuXHRcdFx0JHRoaXMuZmFkZU91dCgpO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBJY29uIENsaWNrXHJcblx0ICpcclxuXHQgKiBTY3JvbGwgdG8gdGhlIHRvcCBvZiB0aGUgcGFnZSB3aGVuZXZlciB0aGUgdXNlciBjbGlja3Mgb24gdGhlIGNhcmV0IGljb24uXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uSWNvbkNsaWNrKCkge1xyXG5cdFx0b25BbmltYXRpb24gPSB0cnVlO1xyXG5cdFx0XHJcblx0XHQkKCdodG1sLCBib2R5JykuYW5pbWF0ZSh7XHJcblx0XHRcdHNjcm9sbFRvcDogMFxyXG5cdFx0fSwgJ2Zhc3QnLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0b25BbmltYXRpb24gPSBmYWxzZTtcclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHQkdGhpcy5mYWRlT3V0KCk7XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkVcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdCQod2luZG93KS5vbignc2Nyb2xsJywgX29uV2luZG93U2Nyb2xsKTtcclxuXHRcdCR0aGlzLm9uKCdjbGljaycsICdpJywgX29uSWNvbkNsaWNrKTtcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcblx0XHJcbn0pOyAiXX0=
