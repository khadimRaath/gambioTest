'use strict';

/* --------------------------------------------------------------
 pageup.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that displays a "Page Up" button if the
 * page is not at top position. On click the page
 * scrolls up to top
 */
gambio.widgets.module('pageup', [gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $window = $(window),
	    visible = false,
	    transition = {},
	    defaults = {
		top: 200, // Pixel from top needs to be reached before the button gets displayed
		duration: 300, // Animation time to scroll up
		showClass: 'visible' // Class that gets added to show the pageup element (else it will be hidden)
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the scroll event.
  * If the current scroll position ist higher
  * than the position given by options.top
  * the button gets displayed.
  * @private
  */
	var _scrollHandler = function _scrollHandler() {
		var show = $window.scrollTop() > options.top;

		if (show && !visible) {
			visible = true;
			transition.open = true;
			$this.trigger(jse.libs.template.events.TRANSITION(), transition);
		} else if (!show && visible) {
			visible = false;
			transition.open = false;
			$this.trigger(jse.libs.template.events.TRANSITION(), transition);
		}
	};

	/**
  * Event handler for clicking on the
  * page-up button. It scrolls up the
  * page.
  * @private
  */
	var _clickHandler = function _clickHandler(e) {
		e.preventDefault();
		$('html, body').animate({ scrollTop: '0' }, options.duration);
	};

	// ########## INITIALIZATION ##########


	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		transition.classOpen = options.showClass;

		$window.on('scroll', _scrollHandler);
		$this.on('click', _clickHandler);

		_scrollHandler();

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcGFnZXVwLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHdpbmRvdyIsIndpbmRvdyIsInZpc2libGUiLCJ0cmFuc2l0aW9uIiwiZGVmYXVsdHMiLCJ0b3AiLCJkdXJhdGlvbiIsInNob3dDbGFzcyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2Nyb2xsSGFuZGxlciIsInNob3ciLCJzY3JvbGxUb3AiLCJvcGVuIiwidHJpZ2dlciIsImpzZSIsImxpYnMiLCJ0ZW1wbGF0ZSIsImV2ZW50cyIsIlRSQU5TSVRJT04iLCJfY2xpY2tIYW5kbGVyIiwiZSIsInByZXZlbnREZWZhdWx0IiwiYW5pbWF0ZSIsImluaXQiLCJkb25lIiwiY2xhc3NPcGVuIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0MsUUFERCxFQUdDLENBQ0NGLE9BQU9HLE1BQVAsR0FBZ0IsY0FEakIsQ0FIRCxFQU9DLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFRjs7QUFFRSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFVBQVVELEVBQUVFLE1BQUYsQ0FEWDtBQUFBLEtBRUNDLFVBQVUsS0FGWDtBQUFBLEtBR0NDLGFBQWEsRUFIZDtBQUFBLEtBSUNDLFdBQVc7QUFDVkMsT0FBSyxHQURLLEVBQ087QUFDakJDLFlBQVUsR0FGQSxFQUVZO0FBQ3RCQyxhQUFXLFNBSEQsQ0FHYTtBQUhiLEVBSlo7QUFBQSxLQVNDQyxVQUFVVCxFQUFFVSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJMLFFBQW5CLEVBQTZCUCxJQUE3QixDQVRYO0FBQUEsS0FVQ0YsU0FBUyxFQVZWOztBQWFGOztBQUVFOzs7Ozs7O0FBT0EsS0FBSWUsaUJBQWlCLFNBQWpCQSxjQUFpQixHQUFXO0FBQy9CLE1BQUlDLE9BQU9YLFFBQVFZLFNBQVIsS0FBc0JKLFFBQVFILEdBQXpDOztBQUVBLE1BQUlNLFFBQVEsQ0FBQ1QsT0FBYixFQUFzQjtBQUNyQkEsYUFBVSxJQUFWO0FBQ0FDLGNBQVdVLElBQVgsR0FBa0IsSUFBbEI7QUFDQWYsU0FBTWdCLE9BQU4sQ0FBY0MsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxNQUFsQixDQUF5QkMsVUFBekIsRUFBZCxFQUFxRGhCLFVBQXJEO0FBQ0EsR0FKRCxNQUlPLElBQUksQ0FBQ1EsSUFBRCxJQUFTVCxPQUFiLEVBQXNCO0FBQzVCQSxhQUFVLEtBQVY7QUFDQUMsY0FBV1UsSUFBWCxHQUFrQixLQUFsQjtBQUNBZixTQUFNZ0IsT0FBTixDQUFjQyxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLE1BQWxCLENBQXlCQyxVQUF6QixFQUFkLEVBQXFEaEIsVUFBckQ7QUFDQTtBQUNELEVBWkQ7O0FBZUE7Ozs7OztBQU1BLEtBQUlpQixnQkFBZ0IsU0FBaEJBLGFBQWdCLENBQVNDLENBQVQsRUFBWTtBQUMvQkEsSUFBRUMsY0FBRjtBQUNBdkIsSUFBRSxZQUFGLEVBQWdCd0IsT0FBaEIsQ0FBd0IsRUFBQ1gsV0FBVyxHQUFaLEVBQXhCLEVBQTBDSixRQUFRRixRQUFsRDtBQUNBLEVBSEQ7O0FBS0Y7OztBQUdFOzs7O0FBSUFYLFFBQU82QixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1QnRCLGFBQVd1QixTQUFYLEdBQXVCbEIsUUFBUUQsU0FBL0I7O0FBRUFQLFVBQVEyQixFQUFSLENBQVcsUUFBWCxFQUFxQmpCLGNBQXJCO0FBQ0FaLFFBQU02QixFQUFOLENBQVMsT0FBVCxFQUFrQlAsYUFBbEI7O0FBRUFWOztBQUVBZTtBQUNBLEVBVkQ7O0FBWUE7QUFDQSxRQUFPOUIsTUFBUDtBQUNBLENBbEZGIiwiZmlsZSI6IndpZGdldHMvcGFnZXVwLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBwYWdldXAuanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQ29tcG9uZW50IHRoYXQgZGlzcGxheXMgYSBcIlBhZ2UgVXBcIiBidXR0b24gaWYgdGhlXG4gKiBwYWdlIGlzIG5vdCBhdCB0b3AgcG9zaXRpb24uIE9uIGNsaWNrIHRoZSBwYWdlXG4gKiBzY3JvbGxzIHVwIHRvIHRvcFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdwYWdldXAnLFxuXG5cdFtcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cydcblx0XSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdCR3aW5kb3cgPSAkKHdpbmRvdyksXG5cdFx0XHR2aXNpYmxlID0gZmFsc2UsXG5cdFx0XHR0cmFuc2l0aW9uID0ge30sXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0dG9wOiAyMDAsICAgICAgICAvLyBQaXhlbCBmcm9tIHRvcCBuZWVkcyB0byBiZSByZWFjaGVkIGJlZm9yZSB0aGUgYnV0dG9uIGdldHMgZGlzcGxheWVkXG5cdFx0XHRcdGR1cmF0aW9uOiAzMDAsICAgICAgICAvLyBBbmltYXRpb24gdGltZSB0byBzY3JvbGwgdXBcblx0XHRcdFx0c2hvd0NsYXNzOiAndmlzaWJsZScgICAvLyBDbGFzcyB0aGF0IGdldHMgYWRkZWQgdG8gc2hvdyB0aGUgcGFnZXVwIGVsZW1lbnQgKGVsc2UgaXQgd2lsbCBiZSBoaWRkZW4pXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIHNjcm9sbCBldmVudC5cblx0XHQgKiBJZiB0aGUgY3VycmVudCBzY3JvbGwgcG9zaXRpb24gaXN0IGhpZ2hlclxuXHRcdCAqIHRoYW4gdGhlIHBvc2l0aW9uIGdpdmVuIGJ5IG9wdGlvbnMudG9wXG5cdFx0ICogdGhlIGJ1dHRvbiBnZXRzIGRpc3BsYXllZC5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2Nyb2xsSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHNob3cgPSAkd2luZG93LnNjcm9sbFRvcCgpID4gb3B0aW9ucy50b3A7XG5cblx0XHRcdGlmIChzaG93ICYmICF2aXNpYmxlKSB7XG5cdFx0XHRcdHZpc2libGUgPSB0cnVlO1xuXHRcdFx0XHR0cmFuc2l0aW9uLm9wZW4gPSB0cnVlO1xuXHRcdFx0XHQkdGhpcy50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXHRcdFx0fSBlbHNlIGlmICghc2hvdyAmJiB2aXNpYmxlKSB7XG5cdFx0XHRcdHZpc2libGUgPSBmYWxzZTtcblx0XHRcdFx0dHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG5cdFx0XHRcdCR0aGlzLnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cdFx0XHR9XG5cdFx0fTtcblxuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgY2xpY2tpbmcgb24gdGhlXG5cdFx0ICogcGFnZS11cCBidXR0b24uIEl0IHNjcm9sbHMgdXAgdGhlXG5cdFx0ICogcGFnZS5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2xpY2tIYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0JCgnaHRtbCwgYm9keScpLmFuaW1hdGUoe3Njcm9sbFRvcDogJzAnfSwgb3B0aW9ucy5kdXJhdGlvbik7XG5cdFx0fTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHR0cmFuc2l0aW9uLmNsYXNzT3BlbiA9IG9wdGlvbnMuc2hvd0NsYXNzO1xuXG5cdFx0XHQkd2luZG93Lm9uKCdzY3JvbGwnLCBfc2Nyb2xsSGFuZGxlcik7XG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCBfY2xpY2tIYW5kbGVyKTtcblxuXHRcdFx0X3Njcm9sbEhhbmRsZXIoKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
