'use strict';

/* --------------------------------------------------------------
 slider_size.js 2016-02-04 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Gets the size of the biggest image from the applied element and puts the previous and next buttons to the right 
 * position, if the screen-width is bigger than 1920px.
 */
gambio.widgets.module('slider_size', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {},
	    maxWidth = 0,
	    nextButton = $('.js-teaser-slider-next.swiper-button-next'),
	    prevButton = $('.js-teaser-slider-prev.swiper-button-prev');

	// ########## PRIVATE FUNCTIONS ##########


	/**
  * Gets the biggest image from the applied element and calls the positioning method.
  * 
  * @private
  */
	var _getBiggestImageWidth = function _getBiggestImageWidth() {

		var windowWidth = $(window).width();

		$(window).load(function () {
			$('#slider').each(function () {

				$this.find('.swiper-container .swiper-wrapper .swiper-slide img').each(function () {

					var w = $(this).get(0).naturalWidth;
					if (w > maxWidth) {
						maxWidth = w;
					}
				});
				if (maxWidth && windowWidth > 1920) {
					_positionButtons(maxWidth);
				}
			});
		});
	};

	/**
  * Puts the previous and next buttons of the swiper to the correct position, if the screen-width is bigger than
  * 1920px
  * 
  * @param maxWidth int
  * @private
  */
	var _positionButtons = function _positionButtons(maxWidth) {

		var marginVal = Math.ceil(-(maxWidth / 2) + 30);

		nextButton.css({
			'right': '50%',
			'margin-right': marginVal + 'px'
		});

		prevButton.css({
			'left': '50%',
			'margin-left': marginVal + 'px'
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		_getBiggestImageWidth();

		$(window).resize(function () {
			if ($(window).width() <= 1920 && nextButton.attr('style') && prevButton.attr('style')) {
				nextButton.removeAttr('style');
				prevButton.removeAttr('style');
			} else if ($(window).width() > 1920 && !nextButton.attr('style') && !prevButton.attr('style')) {
				_positionButtons(maxWidth);
			}
		});

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvc2xpZGVyX3NpemUuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJtYXhXaWR0aCIsIm5leHRCdXR0b24iLCJwcmV2QnV0dG9uIiwiX2dldEJpZ2dlc3RJbWFnZVdpZHRoIiwid2luZG93V2lkdGgiLCJ3aW5kb3ciLCJ3aWR0aCIsImxvYWQiLCJlYWNoIiwiZmluZCIsInciLCJnZXQiLCJuYXR1cmFsV2lkdGgiLCJfcG9zaXRpb25CdXR0b25zIiwibWFyZ2luVmFsIiwiTWF0aCIsImNlaWwiLCJjc3MiLCJpbml0IiwiZG9uZSIsInJlc2l6ZSIsImF0dHIiLCJyZW1vdmVBdHRyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7QUFJQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0MsYUFERCxFQUdDLEVBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxXQUFXLEVBRFo7QUFBQSxLQUVDQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQUZYO0FBQUEsS0FHQ0QsU0FBUyxFQUhWO0FBQUEsS0FLQ08sV0FBVyxDQUxaO0FBQUEsS0FNQ0MsYUFBYUwsRUFBRSwyQ0FBRixDQU5kO0FBQUEsS0FPQ00sYUFBYU4sRUFBRSwyQ0FBRixDQVBkOztBQVVBOzs7QUFHQTs7Ozs7QUFLQSxLQUFJTyx3QkFBd0IsU0FBeEJBLHFCQUF3QixHQUFXOztBQUV0QyxNQUFJQyxjQUFjUixFQUFFUyxNQUFGLEVBQVVDLEtBQVYsRUFBbEI7O0FBRUFWLElBQUVTLE1BQUYsRUFBVUUsSUFBVixDQUFlLFlBQVU7QUFDeEJYLEtBQUUsU0FBRixFQUFhWSxJQUFiLENBQWtCLFlBQVU7O0FBRTNCYixVQUFNYyxJQUFOLENBQVcscURBQVgsRUFBa0VELElBQWxFLENBQXVFLFlBQVU7O0FBRWhGLFNBQUlFLElBQUlkLEVBQUUsSUFBRixFQUFRZSxHQUFSLENBQVksQ0FBWixFQUFlQyxZQUF2QjtBQUNBLFNBQUlGLElBQUlWLFFBQVIsRUFBa0I7QUFDakJBLGlCQUFXVSxDQUFYO0FBQ0E7QUFDRCxLQU5EO0FBT0EsUUFBSVYsWUFBWUksY0FBYyxJQUE5QixFQUFvQztBQUNuQ1Msc0JBQWlCYixRQUFqQjtBQUNBO0FBQ0QsSUFaRDtBQWFBLEdBZEQ7QUFlQSxFQW5CRDs7QUFxQkE7Ozs7Ozs7QUFPQSxLQUFJYSxtQkFBbUIsU0FBbkJBLGdCQUFtQixDQUFVYixRQUFWLEVBQW9COztBQUUxQyxNQUFJYyxZQUFZQyxLQUFLQyxJQUFMLENBQVUsRUFBRWhCLFdBQVMsQ0FBWCxJQUFjLEVBQXhCLENBQWhCOztBQUVBQyxhQUFXZ0IsR0FBWCxDQUFlO0FBQ2QsWUFBUSxLQURNO0FBRWQsbUJBQWdCSCxZQUFZO0FBRmQsR0FBZjs7QUFLQVosYUFBV2UsR0FBWCxDQUFlO0FBQ2QsV0FBTyxLQURPO0FBRWQsa0JBQWVILFlBQVk7QUFGYixHQUFmO0FBSUEsRUFiRDs7QUFnQkE7O0FBRUE7Ozs7QUFJQXJCLFFBQU95QixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1QmhCOztBQUVBUCxJQUFFUyxNQUFGLEVBQVVlLE1BQVYsQ0FBaUIsWUFBVztBQUMzQixPQUFJeEIsRUFBRVMsTUFBRixFQUFVQyxLQUFWLE1BQXFCLElBQXJCLElBQTZCTCxXQUFXb0IsSUFBWCxDQUFnQixPQUFoQixDQUE3QixJQUF5RG5CLFdBQVdtQixJQUFYLENBQWdCLE9BQWhCLENBQTdELEVBQXVGO0FBQ3RGcEIsZUFBV3FCLFVBQVgsQ0FBc0IsT0FBdEI7QUFDQXBCLGVBQVdvQixVQUFYLENBQXNCLE9BQXRCO0FBQ0EsSUFIRCxNQUdPLElBQUkxQixFQUFFUyxNQUFGLEVBQVVDLEtBQVYsS0FBb0IsSUFBcEIsSUFBNEIsQ0FBQ0wsV0FBV29CLElBQVgsQ0FBZ0IsT0FBaEIsQ0FBN0IsSUFBeUQsQ0FBQ25CLFdBQVdtQixJQUFYLENBQWdCLE9BQWhCLENBQTlELEVBQXdGO0FBQzlGUixxQkFBaUJiLFFBQWpCO0FBQ0E7QUFDRCxHQVBEOztBQVNBbUI7QUFDQSxFQWREOztBQWdCQTtBQUNBLFFBQU8xQixNQUFQO0FBQ0EsQ0FqR0YiLCJmaWxlIjoid2lkZ2V0cy9zbGlkZXJfc2l6ZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2xpZGVyX3NpemUuanMgMjAxNi0wMi0wNCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogR2V0cyB0aGUgc2l6ZSBvZiB0aGUgYmlnZ2VzdCBpbWFnZSBmcm9tIHRoZSBhcHBsaWVkIGVsZW1lbnQgYW5kIHB1dHMgdGhlIHByZXZpb3VzIGFuZCBuZXh0IGJ1dHRvbnMgdG8gdGhlIHJpZ2h0IFxuICogcG9zaXRpb24sIGlmIHRoZSBzY3JlZW4td2lkdGggaXMgYmlnZ2VyIHRoYW4gMTkyMHB4LlxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdzbGlkZXJfc2l6ZScsXG5cdFxuXHRbXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cdFx0XG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9LFxuXHRcdFxuXHRcdFx0bWF4V2lkdGggPSAwLFxuXHRcdFx0bmV4dEJ1dHRvbiA9ICQoJy5qcy10ZWFzZXItc2xpZGVyLW5leHQuc3dpcGVyLWJ1dHRvbi1uZXh0JyksXG5cdFx0XHRwcmV2QnV0dG9uID0gJCgnLmpzLXRlYXNlci1zbGlkZXItcHJldi5zd2lwZXItYnV0dG9uLXByZXYnKTtcblx0XHRcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIFBSSVZBVEUgRlVOQ1RJT05TICMjIyMjIyMjIyNcblx0XHRcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXRzIHRoZSBiaWdnZXN0IGltYWdlIGZyb20gdGhlIGFwcGxpZWQgZWxlbWVudCBhbmQgY2FsbHMgdGhlIHBvc2l0aW9uaW5nIG1ldGhvZC5cblx0XHQgKiBcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0QmlnZ2VzdEltYWdlV2lkdGggPSBmdW5jdGlvbigpIHtcblx0XHRcdFxuXHRcdFx0dmFyIHdpbmRvd1dpZHRoID0gJCh3aW5kb3cpLndpZHRoKCk7XG5cdFx0XHRcblx0XHRcdCQod2luZG93KS5sb2FkKGZ1bmN0aW9uKCl7XG5cdFx0XHRcdCQoJyNzbGlkZXInKS5lYWNoKGZ1bmN0aW9uKCl7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0JHRoaXMuZmluZCgnLnN3aXBlci1jb250YWluZXIgLnN3aXBlci13cmFwcGVyIC5zd2lwZXItc2xpZGUgaW1nJykuZWFjaChmdW5jdGlvbigpe1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHR2YXIgdyA9ICQodGhpcykuZ2V0KDApLm5hdHVyYWxXaWR0aDtcblx0XHRcdFx0XHRcdGlmICh3ID4gbWF4V2lkdGgpIHtcblx0XHRcdFx0XHRcdFx0bWF4V2lkdGggPSB3O1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdGlmIChtYXhXaWR0aCAmJiB3aW5kb3dXaWR0aCA+IDE5MjApIHtcblx0XHRcdFx0XHRcdF9wb3NpdGlvbkJ1dHRvbnMobWF4V2lkdGgpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFB1dHMgdGhlIHByZXZpb3VzIGFuZCBuZXh0IGJ1dHRvbnMgb2YgdGhlIHN3aXBlciB0byB0aGUgY29ycmVjdCBwb3NpdGlvbiwgaWYgdGhlIHNjcmVlbi13aWR0aCBpcyBiaWdnZXIgdGhhblxuXHRcdCAqIDE5MjBweFxuXHRcdCAqIFxuXHRcdCAqIEBwYXJhbSBtYXhXaWR0aCBpbnRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcG9zaXRpb25CdXR0b25zID0gZnVuY3Rpb24gKG1heFdpZHRoKSB7XG5cdFx0XHRcblx0XHRcdHZhciBtYXJnaW5WYWwgPSBNYXRoLmNlaWwoLShtYXhXaWR0aC8yKSszMCk7XG5cdFx0XHRcblx0XHRcdG5leHRCdXR0b24uY3NzKHtcblx0XHRcdFx0J3JpZ2h0JzonNTAlJyxcblx0XHRcdFx0J21hcmdpbi1yaWdodCc6IG1hcmdpblZhbCArICdweCdcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRwcmV2QnV0dG9uLmNzcyh7XG5cdFx0XHRcdCdsZWZ0JzonNTAlJyxcblx0XHRcdFx0J21hcmdpbi1sZWZ0JzogbWFyZ2luVmFsICsgJ3B4J1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdFxuXHRcdFx0X2dldEJpZ2dlc3RJbWFnZVdpZHRoKCk7XG5cdFx0XHRcblx0XHRcdCQod2luZG93KS5yZXNpemUoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICgkKHdpbmRvdykud2lkdGgoKSA8PSAxOTIwICYmIG5leHRCdXR0b24uYXR0cignc3R5bGUnKSAmJiBwcmV2QnV0dG9uLmF0dHIoJ3N0eWxlJykpIHtcblx0XHRcdFx0XHRuZXh0QnV0dG9uLnJlbW92ZUF0dHIoJ3N0eWxlJyk7XG5cdFx0XHRcdFx0cHJldkJ1dHRvbi5yZW1vdmVBdHRyKCdzdHlsZScpO1xuXHRcdFx0XHR9IGVsc2UgaWYgKCQod2luZG93KS53aWR0aCgpID4gMTkyMCAmJiAhbmV4dEJ1dHRvbi5hdHRyKCdzdHlsZScpICYmICFwcmV2QnV0dG9uLmF0dHIoJ3N0eWxlJykpIHtcblx0XHRcdFx0XHRfcG9zaXRpb25CdXR0b25zKG1heFdpZHRoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTsiXX0=
