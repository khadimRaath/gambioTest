'use strict';

/* --------------------------------------------------------------
 content_header.js 2016-04-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Content Header Controller
 *
 * This module handles the behavior of the header controller. It will roll-in whenever the user scrolls to top.
 * The widget will emmit an event "content_header:roll_in" when it is rolled in and a "content_header:roll_out"
 * whenever it's hiding. These events can be useful if there's a need to re-position elements that are static
 * e.g. fixed table headers.
 *
 * In extend the content-header element will have the "fixed" class as long as it stays fixed on the viewport.
 */
gx.controllers.module('content_header', [], function (data) {

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
  * The original position of the tab navigation
  *
  * @type {Number}
  */
	var originalPosition = 0;

	/**
  * The last scroll position
  *
  * @type {Number}
  */
	var scrollPosition = $(window).scrollTop();

	/**
  * The original left position of the pageHeading
  *
  * @type {Number}
  */
	var originalLeft = 0;

	/**
  * Tells if the tab navigation is within the view port
  *
  * @type {Boolean}
  */
	var isOut = true;

	/**
  * Whether the content header is currently on animation.
  *
  * @type {Boolean}
  */
	var onAnimation = false;

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
  * On Window Scroll Handler
  *
  * Reset the page navigation frame to the original position if the user scrolls directly to top.
  */
	function _onWindowScroll() {
		if (onAnimation) {
			return;
		}

		var newScrollPosition = $(window).scrollTop();
		var isScrollDown = scrollPosition - newScrollPosition < 0;
		var isScrollUp = scrollPosition - newScrollPosition > 0;

		originalPosition = $('#main-header').height();
		originalLeft = $('#main-menu').outerWidth();

		_setContentHeaderAbsoluteLeft();

		var scrolledToTop = _checkScrolledToTop();

		if (!scrolledToTop) {
			if (isScrollDown && !isScrollUp && !isOut) {
				_rollOut();
			} else if (!isScrollDown && isScrollUp && isOut) {
				_rollIn();
			}
		}

		scrollPosition = newScrollPosition;
	}

	/**
  * Roll-in Animation Function
  */
	function _rollIn() {
		isOut = false;
		onAnimation = true;

		$this.trigger('content_header:roll_in');

		$this.css({
			top: '0',
			position: 'fixed'
		});

		// Retain the page height with a temporary padding.
		$this.parent().css('padding-top', $this.outerHeight() + 'px');

		$this.animate({
			top: originalPosition + 'px'
		}, {
			complete: function complete() {
				_checkScrolledToTop();
				onAnimation = false;
				_onWindowScroll(); // Check if it's necessary to re-render the position of the content-header.
				$this.addClass('fixed');
			}
		}, 'fast');
	}

	/**
  * Roll-out Animation Function
  */
	function _rollOut() {
		isOut = true;
		onAnimation = true;

		$this.trigger('content_header:roll_out');

		$this.animate({
			top: '0'
		}, 'fast', 'swing', function () {
			$this.css({
				top: originalPosition + 'px',
				position: ''
			});

			$this.parent().css('padding-top', ''); // Remove temporary padding.

			onAnimation = false;

			$this.removeClass('fixed');
		});
	}

	/**
  * Sets the left position of the pageHeading absolute
  */
	function _setContentHeaderAbsoluteLeft() {
		$this.css('left', originalLeft - $(window).scrollLeft());
	}

	/**
  * Check if user has scrolled to top of the page.
  *
  * @return {Boolean} Returns the check result.
  */
	function _checkScrolledToTop() {
		if ($(window).scrollTop() === 0) {
			$this.css({
				top: originalPosition + 'px',
				position: ''
			});

			$this.parent().css('padding-top', ''); // Remove temporary padding.

			return true;
		}

		return false;
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$(window).on('scroll', _onWindowScroll);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9jb250ZW50L2NvbnRlbnRfaGVhZGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwib3JpZ2luYWxQb3NpdGlvbiIsInNjcm9sbFBvc2l0aW9uIiwid2luZG93Iiwic2Nyb2xsVG9wIiwib3JpZ2luYWxMZWZ0IiwiaXNPdXQiLCJvbkFuaW1hdGlvbiIsIl9vbldpbmRvd1Njcm9sbCIsIm5ld1Njcm9sbFBvc2l0aW9uIiwiaXNTY3JvbGxEb3duIiwiaXNTY3JvbGxVcCIsImhlaWdodCIsIm91dGVyV2lkdGgiLCJfc2V0Q29udGVudEhlYWRlckFic29sdXRlTGVmdCIsInNjcm9sbGVkVG9Ub3AiLCJfY2hlY2tTY3JvbGxlZFRvVG9wIiwiX3JvbGxPdXQiLCJfcm9sbEluIiwidHJpZ2dlciIsImNzcyIsInRvcCIsInBvc2l0aW9uIiwicGFyZW50Iiwib3V0ZXJIZWlnaHQiLCJhbmltYXRlIiwiY29tcGxldGUiLCJhZGRDbGFzcyIsInJlbW92ZUNsYXNzIiwic2Nyb2xsTGVmdCIsImluaXQiLCJkb25lIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7OztBQVVBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsZ0JBQXRCLEVBQXdDLEVBQXhDLEVBQTRDLFVBQVNDLElBQVQsRUFBZTs7QUFFMUQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFJQyxtQkFBbUIsQ0FBdkI7O0FBRUE7Ozs7O0FBS0EsS0FBSUMsaUJBQWlCRixFQUFFRyxNQUFGLEVBQVVDLFNBQVYsRUFBckI7O0FBRUE7Ozs7O0FBS0EsS0FBSUMsZUFBZSxDQUFuQjs7QUFFQTs7Ozs7QUFLQSxLQUFJQyxRQUFRLElBQVo7O0FBRUE7Ozs7O0FBS0EsS0FBSUMsY0FBYyxLQUFsQjs7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QUYsRUFBVTZCLEVBQVYsQ0FBYSxRQUFiLEVBQXVCeEIsZUFBdkI7QUFDQXVCO0FBQ0EsRUFIRDs7QUFLQSxRQUFPbEMsTUFBUDtBQUNBLENBekxEIiwiZmlsZSI6ImxheW91dHMvbWFpbi9jb250ZW50L2NvbnRlbnRfaGVhZGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGNvbnRlbnRfaGVhZGVyLmpzIDIwMTYtMDQtMjdcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogQ29udGVudCBIZWFkZXIgQ29udHJvbGxlclxyXG4gKlxyXG4gKiBUaGlzIG1vZHVsZSBoYW5kbGVzIHRoZSBiZWhhdmlvciBvZiB0aGUgaGVhZGVyIGNvbnRyb2xsZXIuIEl0IHdpbGwgcm9sbC1pbiB3aGVuZXZlciB0aGUgdXNlciBzY3JvbGxzIHRvIHRvcC5cclxuICogVGhlIHdpZGdldCB3aWxsIGVtbWl0IGFuIGV2ZW50IFwiY29udGVudF9oZWFkZXI6cm9sbF9pblwiIHdoZW4gaXQgaXMgcm9sbGVkIGluIGFuZCBhIFwiY29udGVudF9oZWFkZXI6cm9sbF9vdXRcIlxyXG4gKiB3aGVuZXZlciBpdCdzIGhpZGluZy4gVGhlc2UgZXZlbnRzIGNhbiBiZSB1c2VmdWwgaWYgdGhlcmUncyBhIG5lZWQgdG8gcmUtcG9zaXRpb24gZWxlbWVudHMgdGhhdCBhcmUgc3RhdGljXHJcbiAqIGUuZy4gZml4ZWQgdGFibGUgaGVhZGVycy5cclxuICpcclxuICogSW4gZXh0ZW5kIHRoZSBjb250ZW50LWhlYWRlciBlbGVtZW50IHdpbGwgaGF2ZSB0aGUgXCJmaXhlZFwiIGNsYXNzIGFzIGxvbmcgYXMgaXQgc3RheXMgZml4ZWQgb24gdGhlIHZpZXdwb3J0LlxyXG4gKi9cclxuZ3guY29udHJvbGxlcnMubW9kdWxlKCdjb250ZW50X2hlYWRlcicsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogVGhlIG9yaWdpbmFsIHBvc2l0aW9uIG9mIHRoZSB0YWIgbmF2aWdhdGlvblxyXG5cdCAqXHJcblx0ICogQHR5cGUge051bWJlcn1cclxuXHQgKi9cclxuXHRsZXQgb3JpZ2luYWxQb3NpdGlvbiA9IDA7XHJcblx0XHJcblx0LyoqXHJcblx0ICogVGhlIGxhc3Qgc2Nyb2xsIHBvc2l0aW9uXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7TnVtYmVyfVxyXG5cdCAqL1xyXG5cdGxldCBzY3JvbGxQb3NpdGlvbiA9ICQod2luZG93KS5zY3JvbGxUb3AoKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBUaGUgb3JpZ2luYWwgbGVmdCBwb3NpdGlvbiBvZiB0aGUgcGFnZUhlYWRpbmdcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtOdW1iZXJ9XHJcblx0ICovXHJcblx0bGV0IG9yaWdpbmFsTGVmdCA9IDA7XHJcblx0XHJcblx0LyoqXHJcblx0ICogVGVsbHMgaWYgdGhlIHRhYiBuYXZpZ2F0aW9uIGlzIHdpdGhpbiB0aGUgdmlldyBwb3J0XHJcblx0ICpcclxuXHQgKiBAdHlwZSB7Qm9vbGVhbn1cclxuXHQgKi9cclxuXHRsZXQgaXNPdXQgPSB0cnVlO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFdoZXRoZXIgdGhlIGNvbnRlbnQgaGVhZGVyIGlzIGN1cnJlbnRseSBvbiBhbmltYXRpb24uXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7Qm9vbGVhbn1cclxuXHQgKi9cclxuXHRsZXQgb25BbmltYXRpb24gPSBmYWxzZTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge307XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gRlVOQ1RJT05TXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gV2luZG93IFNjcm9sbCBIYW5kbGVyXHJcblx0ICpcclxuXHQgKiBSZXNldCB0aGUgcGFnZSBuYXZpZ2F0aW9uIGZyYW1lIHRvIHRoZSBvcmlnaW5hbCBwb3NpdGlvbiBpZiB0aGUgdXNlciBzY3JvbGxzIGRpcmVjdGx5IHRvIHRvcC5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25XaW5kb3dTY3JvbGwoKSB7XHJcblx0XHRpZiAob25BbmltYXRpb24pIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRjb25zdCBuZXdTY3JvbGxQb3NpdGlvbiA9ICQod2luZG93KS5zY3JvbGxUb3AoKTtcclxuXHRcdGNvbnN0IGlzU2Nyb2xsRG93biA9IHNjcm9sbFBvc2l0aW9uIC0gbmV3U2Nyb2xsUG9zaXRpb24gPCAwO1xyXG5cdFx0Y29uc3QgaXNTY3JvbGxVcCA9IHNjcm9sbFBvc2l0aW9uIC0gbmV3U2Nyb2xsUG9zaXRpb24gPiAwO1xyXG5cdFx0XHJcblx0XHRvcmlnaW5hbFBvc2l0aW9uID0gJCgnI21haW4taGVhZGVyJykuaGVpZ2h0KCk7XHJcblx0XHRvcmlnaW5hbExlZnQgPSAkKCcjbWFpbi1tZW51Jykub3V0ZXJXaWR0aCgpO1xyXG5cdFx0XHJcblx0XHRfc2V0Q29udGVudEhlYWRlckFic29sdXRlTGVmdCgpO1xyXG5cdFx0XHJcblx0XHRsZXQgc2Nyb2xsZWRUb1RvcCA9IF9jaGVja1Njcm9sbGVkVG9Ub3AoKTtcclxuXHRcdFxyXG5cdFx0aWYgKCFzY3JvbGxlZFRvVG9wKSB7XHJcblx0XHRcdGlmIChpc1Njcm9sbERvd24gJiYgIWlzU2Nyb2xsVXAgJiYgIWlzT3V0KSB7XHJcblx0XHRcdFx0X3JvbGxPdXQoKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRlbHNlIGlmICghaXNTY3JvbGxEb3duICYmIGlzU2Nyb2xsVXAgJiYgaXNPdXQpIHtcclxuXHRcdFx0XHRfcm9sbEluKCk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0c2Nyb2xsUG9zaXRpb24gPSBuZXdTY3JvbGxQb3NpdGlvbjtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogUm9sbC1pbiBBbmltYXRpb24gRnVuY3Rpb25cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfcm9sbEluKCkge1xyXG5cdFx0aXNPdXQgPSBmYWxzZTtcclxuXHRcdG9uQW5pbWF0aW9uID0gdHJ1ZTtcclxuXHRcdFxyXG5cdFx0JHRoaXMudHJpZ2dlcignY29udGVudF9oZWFkZXI6cm9sbF9pbicpO1xyXG5cdFx0XHJcblx0XHQkdGhpcy5jc3Moe1xyXG5cdFx0XHR0b3A6ICcwJyxcclxuXHRcdFx0cG9zaXRpb246ICdmaXhlZCdcclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHQvLyBSZXRhaW4gdGhlIHBhZ2UgaGVpZ2h0IHdpdGggYSB0ZW1wb3JhcnkgcGFkZGluZy5cclxuXHRcdCR0aGlzLnBhcmVudCgpLmNzcygncGFkZGluZy10b3AnLCAkdGhpcy5vdXRlckhlaWdodCgpICsgJ3B4Jyk7XHJcblx0XHRcclxuXHRcdCR0aGlzLmFuaW1hdGUoe1xyXG5cdFx0XHR0b3A6IG9yaWdpbmFsUG9zaXRpb24gKyAncHgnXHJcblx0XHR9LCB7XHJcblx0XHRcdGNvbXBsZXRlOiBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHRfY2hlY2tTY3JvbGxlZFRvVG9wKCk7XHJcblx0XHRcdFx0b25BbmltYXRpb24gPSBmYWxzZTtcclxuXHRcdFx0XHRfb25XaW5kb3dTY3JvbGwoKTsgLy8gQ2hlY2sgaWYgaXQncyBuZWNlc3NhcnkgdG8gcmUtcmVuZGVyIHRoZSBwb3NpdGlvbiBvZiB0aGUgY29udGVudC1oZWFkZXIuXHJcblx0XHRcdFx0JHRoaXMuYWRkQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHRcdH1cclxuXHRcdH0sICdmYXN0Jyk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFJvbGwtb3V0IEFuaW1hdGlvbiBGdW5jdGlvblxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9yb2xsT3V0KCkge1xyXG5cdFx0aXNPdXQgPSB0cnVlO1xyXG5cdFx0b25BbmltYXRpb24gPSB0cnVlO1xyXG5cdFx0XHJcblx0XHQkdGhpcy50cmlnZ2VyKCdjb250ZW50X2hlYWRlcjpyb2xsX291dCcpO1xyXG5cdFx0XHJcblx0XHQkdGhpcy5hbmltYXRlKHtcclxuXHRcdFx0dG9wOiAnMCdcclxuXHRcdH0sICdmYXN0JywgJ3N3aW5nJywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdCR0aGlzLmNzcyh7XHJcblx0XHRcdFx0dG9wOiBvcmlnaW5hbFBvc2l0aW9uICsgJ3B4JyxcclxuXHRcdFx0XHRwb3NpdGlvbjogJydcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQkdGhpcy5wYXJlbnQoKS5jc3MoJ3BhZGRpbmctdG9wJywgJycpOyAvLyBSZW1vdmUgdGVtcG9yYXJ5IHBhZGRpbmcuXHJcblx0XHRcdFxyXG5cdFx0XHRvbkFuaW1hdGlvbiA9IGZhbHNlO1xyXG5cdFx0XHRcclxuXHRcdFx0JHRoaXMucmVtb3ZlQ2xhc3MoJ2ZpeGVkJyk7XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogU2V0cyB0aGUgbGVmdCBwb3NpdGlvbiBvZiB0aGUgcGFnZUhlYWRpbmcgYWJzb2x1dGVcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfc2V0Q29udGVudEhlYWRlckFic29sdXRlTGVmdCgpIHtcclxuXHRcdCR0aGlzLmNzcygnbGVmdCcsIG9yaWdpbmFsTGVmdCAtICQod2luZG93KS5zY3JvbGxMZWZ0KCkpO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBDaGVjayBpZiB1c2VyIGhhcyBzY3JvbGxlZCB0byB0b3Agb2YgdGhlIHBhZ2UuXHJcblx0ICpcclxuXHQgKiBAcmV0dXJuIHtCb29sZWFufSBSZXR1cm5zIHRoZSBjaGVjayByZXN1bHQuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX2NoZWNrU2Nyb2xsZWRUb1RvcCgpIHtcclxuXHRcdGlmICgkKHdpbmRvdykuc2Nyb2xsVG9wKCkgPT09IDApIHtcclxuXHRcdFx0JHRoaXMuY3NzKHtcclxuXHRcdFx0XHR0b3A6IG9yaWdpbmFsUG9zaXRpb24gKyAncHgnLFxyXG5cdFx0XHRcdHBvc2l0aW9uOiAnJ1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdCR0aGlzLnBhcmVudCgpLmNzcygncGFkZGluZy10b3AnLCAnJyk7IC8vIFJlbW92ZSB0ZW1wb3JhcnkgcGFkZGluZy5cclxuXHRcdFx0XHJcblx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRyZXR1cm4gZmFsc2U7XHJcblx0fVxyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkKHdpbmRvdykub24oJ3Njcm9sbCcsIF9vbldpbmRvd1Njcm9sbCk7XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG59KTsiXX0=
