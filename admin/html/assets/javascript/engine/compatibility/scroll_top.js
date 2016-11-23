'use strict';

/* --------------------------------------------------------------
 scroll_top.js 2015-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Scroll Top Module
 *
 * This module will display a scroll-to-top arrow on the left side of the admin menu. When the users
 * press this arrow the browser will automatically scroll to the top of the page.
 *
 * @module Compatibility/scroll_top
 */
gx.compatibility.module(
// Module name
'scroll_top',

// Module dependencies
[],

/**  @lends module:Compatibility/scroll_top */

function () {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var module = {},
	    $button;

	// ------------------------------------------------------------------------
	// EVENT HANDLER
	// ------------------------------------------------------------------------

	var _initialize = function _initialize() {
		$button = $('<div>').addClass('js-scroll-top-button').html('<i class="fa fa-caret-up"></i>').hide().appendTo('body').on('click', function () {
			$('html, body').animate({
				scrollTop: 0
			});
		});

		$(document).on('scroll', function () {
			var reachedMinimumScrolled = $(document).scrollTop() > 2500,
			    reachedDocumentBottom = $(document).scrollTop() + window.innerHeight === $(document).height();

			// Fade In / Out
			if (reachedMinimumScrolled) {
				$button.fadeIn();
			} else {
				$button.fadeOut();
			}

			// Fix poistion
			if (reachedMinimumScrolled) {
				$button.css({
					bottom: reachedDocumentBottom ? '100px' : '50px'
				});
			}
		});

		$(document).on('leftmenu:collapse', function () {
			$button.animate({
				left: '9px'
			});
		});

		$(document).on('leftmenu:expand', function () {
			$button.animate({
				left: '89px'
			});
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		_initialize();
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNjcm9sbF90b3AuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiJGJ1dHRvbiIsIl9pbml0aWFsaXplIiwiJCIsImFkZENsYXNzIiwiaHRtbCIsImhpZGUiLCJhcHBlbmRUbyIsIm9uIiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsImRvY3VtZW50IiwicmVhY2hlZE1pbmltdW1TY3JvbGxlZCIsInJlYWNoZWREb2N1bWVudEJvdHRvbSIsIndpbmRvdyIsImlubmVySGVpZ2h0IiwiaGVpZ2h0IiwiZmFkZUluIiwiZmFkZU91dCIsImNzcyIsImJvdHRvbSIsImxlZnQiLCJpbml0IiwiZG9uZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7OztBQVFBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQjtBQUNDO0FBQ0EsWUFGRDs7QUFJQztBQUNBLEVBTEQ7O0FBT0M7O0FBRUEsWUFBVzs7QUFFVjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSUEsU0FBUyxFQUFiO0FBQUEsS0FDQ0MsT0FERDs7QUFHQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSUMsY0FBYyxTQUFkQSxXQUFjLEdBQVc7QUFDNUJELFlBQVVFLEVBQUUsT0FBRixFQUNSQyxRQURRLENBQ0Msc0JBREQsRUFFUkMsSUFGUSxDQUVILGdDQUZHLEVBR1JDLElBSFEsR0FJUkMsUUFKUSxDQUlDLE1BSkQsRUFLUkMsRUFMUSxDQUtMLE9BTEssRUFLSSxZQUFXO0FBQ3ZCTCxLQUFFLFlBQUYsRUFBZ0JNLE9BQWhCLENBQXdCO0FBQ3ZCQyxlQUFXO0FBRFksSUFBeEI7QUFHQSxHQVRRLENBQVY7O0FBV0FQLElBQUVRLFFBQUYsRUFBWUgsRUFBWixDQUFlLFFBQWYsRUFBeUIsWUFBVztBQUNuQyxPQUFJSSx5QkFBMEJULEVBQUVRLFFBQUYsRUFBWUQsU0FBWixLQUEwQixJQUF4RDtBQUFBLE9BQ0NHLHdCQUNBVixFQUFFUSxRQUFGLEVBQVlELFNBQVosS0FDQUksT0FBT0MsV0FEUCxLQUN1QlosRUFBRVEsUUFBRixFQUFZSyxNQUFaLEVBSHhCOztBQUtBO0FBQ0EsT0FBSUosc0JBQUosRUFBNEI7QUFDM0JYLFlBQVFnQixNQUFSO0FBQ0EsSUFGRCxNQUVPO0FBQ05oQixZQUFRaUIsT0FBUjtBQUNBOztBQUVEO0FBQ0EsT0FBSU4sc0JBQUosRUFBNEI7QUFDM0JYLFlBQVFrQixHQUFSLENBQVk7QUFDWEMsYUFBU1Asd0JBQXdCLE9BQXhCLEdBQWtDO0FBRGhDLEtBQVo7QUFHQTtBQUNELEdBbkJEOztBQXFCQVYsSUFBRVEsUUFBRixFQUFZSCxFQUFaLENBQWUsbUJBQWYsRUFBb0MsWUFBVztBQUM5Q1AsV0FBUVEsT0FBUixDQUFnQjtBQUNmWSxVQUFNO0FBRFMsSUFBaEI7QUFHQSxHQUpEOztBQU1BbEIsSUFBRVEsUUFBRixFQUFZSCxFQUFaLENBQWUsaUJBQWYsRUFBa0MsWUFBVztBQUM1Q1AsV0FBUVEsT0FBUixDQUFnQjtBQUNmWSxVQUFNO0FBRFMsSUFBaEI7QUFHQSxHQUpEO0FBS0EsRUE1Q0Q7O0FBOENBO0FBQ0E7QUFDQTs7QUFFQXJCLFFBQU9zQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCckI7QUFDQXFCO0FBQ0EsRUFIRDs7QUFLQSxRQUFPdkIsTUFBUDtBQUNBLENBaEZGIiwiZmlsZSI6InNjcm9sbF90b3AuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHNjcm9sbF90b3AuanMgMjAxNS0wOS0yM1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgU2Nyb2xsIFRvcCBNb2R1bGVcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIGRpc3BsYXkgYSBzY3JvbGwtdG8tdG9wIGFycm93IG9uIHRoZSBsZWZ0IHNpZGUgb2YgdGhlIGFkbWluIG1lbnUuIFdoZW4gdGhlIHVzZXJzXG4gKiBwcmVzcyB0aGlzIGFycm93IHRoZSBicm93c2VyIHdpbGwgYXV0b21hdGljYWxseSBzY3JvbGwgdG8gdGhlIHRvcCBvZiB0aGUgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvc2Nyb2xsX3RvcFxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0Ly8gTW9kdWxlIG5hbWVcblx0J3Njcm9sbF90b3AnLFxuXHRcblx0Ly8gTW9kdWxlIGRlcGVuZGVuY2llc1xuXHRbXSxcblx0XG5cdC8qKiAgQGxlbmRzIG1vZHVsZTpDb21wYXRpYmlsaXR5L3Njcm9sbF90b3AgKi9cblx0XG5cdGZ1bmN0aW9uKCkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBtb2R1bGUgPSB7fSxcblx0XHRcdCRidXR0b247XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfaW5pdGlhbGl6ZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JGJ1dHRvbiA9ICQoJzxkaXY+Jylcblx0XHRcdFx0LmFkZENsYXNzKCdqcy1zY3JvbGwtdG9wLWJ1dHRvbicpXG5cdFx0XHRcdC5odG1sKCc8aSBjbGFzcz1cImZhIGZhLWNhcmV0LXVwXCI+PC9pPicpXG5cdFx0XHRcdC5oaWRlKClcblx0XHRcdFx0LmFwcGVuZFRvKCdib2R5Jylcblx0XHRcdFx0Lm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCQoJ2h0bWwsIGJvZHknKS5hbmltYXRlKHtcblx0XHRcdFx0XHRcdHNjcm9sbFRvcDogMFxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JChkb2N1bWVudCkub24oJ3Njcm9sbCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgcmVhY2hlZE1pbmltdW1TY3JvbGxlZCA9ICgkKGRvY3VtZW50KS5zY3JvbGxUb3AoKSA+IDI1MDApLFxuXHRcdFx0XHRcdHJlYWNoZWREb2N1bWVudEJvdHRvbSA9IChcblx0XHRcdFx0XHQkKGRvY3VtZW50KS5zY3JvbGxUb3AoKSArXG5cdFx0XHRcdFx0d2luZG93LmlubmVySGVpZ2h0ID09PSAkKGRvY3VtZW50KS5oZWlnaHQoKSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBGYWRlIEluIC8gT3V0XG5cdFx0XHRcdGlmIChyZWFjaGVkTWluaW11bVNjcm9sbGVkKSB7XG5cdFx0XHRcdFx0JGJ1dHRvbi5mYWRlSW4oKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHQkYnV0dG9uLmZhZGVPdXQoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gRml4IHBvaXN0aW9uXG5cdFx0XHRcdGlmIChyZWFjaGVkTWluaW11bVNjcm9sbGVkKSB7XG5cdFx0XHRcdFx0JGJ1dHRvbi5jc3Moe1xuXHRcdFx0XHRcdFx0Ym90dG9tOiAocmVhY2hlZERvY3VtZW50Qm90dG9tID8gJzEwMHB4JyA6ICc1MHB4Jylcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoZG9jdW1lbnQpLm9uKCdsZWZ0bWVudTpjb2xsYXBzZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkYnV0dG9uLmFuaW1hdGUoe1xuXHRcdFx0XHRcdGxlZnQ6ICc5cHgnXG5cdFx0XHRcdH0pO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoZG9jdW1lbnQpLm9uKCdsZWZ0bWVudTpleHBhbmQnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JGJ1dHRvbi5hbmltYXRlKHtcblx0XHRcdFx0XHRsZWZ0OiAnODlweCdcblx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRfaW5pdGlhbGl6ZSgpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
