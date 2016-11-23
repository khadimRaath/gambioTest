'use strict';

/* --------------------------------------------------------------
 header.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that adds a class to a defined object if the page is
 * scrolled to a given position at least. It is used to set
 * the header size
 */
gambio.widgets.module('header', [gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $window = $(window),
	    $header = null,
	    hover = false,
	    currentPosition = null,
	    scrollUpCounter = 0,
	    transition = {},
	    timeout = 0,
	    defaults = {
		// Selector that defines the header element
		header: '#header',
		// Position in px that needs to be reached to minimize the header
		scrollPosition: 200,
		// Class that gets added if the scrollPosition gets reached
		stickyClass: 'sticky',
		// Maximize the target on mouse hover
		hover: false,
		// Tolerance in px that is used to detect scrolling up
		tolerance: 5
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Handler that gets called by scrolling down / up
  * the site. If the position is lower than the
  * scrollPosition from options, the header gets maximized
  * else it gets minimized
  * @private
  */
	var _scrollHandler = function _scrollHandler() {
		var position = $(document).scrollTop(),
		    hasClass = $header.hasClass(options.stickyClass),
		    scrollUp = currentPosition > position;

		if (position > options.scrollPosition && !scrollUp) {
			// Proceed if scrolling down under the minimum position given by the options
			scrollUpCounter = 0;
			if (!hasClass && !hover) {
				// Proceed if the class isn't set yet and the header isn't hovered with the mouse
				transition.open = false;
				$header.trigger(jse.libs.template.events.TRANSITION(), transition).trigger(jse.libs.template.events.OPEN_FLYOUT(), [$this]);
			}
		} else {
			scrollUpCounter += 1;
			if (hasClass && (options.scrollPosition > position || scrollUpCounter > options.tolerance)) {
				// Proceed if the the minimum position set in the option isn't reached
				// or a specific count of pixel is scrolled up
				transition.open = true;
				$header.trigger(jse.libs.template.events.TRANSITION(), transition);
			}
		}

		clearTimeout(timeout);
		timeout = setTimeout(function () {
			$window.trigger(jse.libs.template.events.REPOSITIONS_STICKYBOX());
		}, 250);

		// Store the current position
		currentPosition = position;
	};

	/**
  * Handler for the mouseenter event on the
  * header. It will remove the minimizer-class
  * from the header container and set the internal
  * header hover state to true
  * @private
  */
	var _mouseEnterHandler = function _mouseEnterHandler() {
		hover = true;
		transition.open = true;
		$header.trigger(jse.libs.template.events.TRANSITION(), transition);
	};

	/**
  * Handler for the mouseout event on the header
  * container. On mouse out, the hover state will
  * be set to false, and the header state will be
  * set by the current scroll position
  * @private
  */
	var _mouseOutHandler = function _mouseOutHandler() {
		hover = false;
		_scrollHandler();
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$header = $this.find(options.header);
		currentPosition = $(document).scrollTop();
		transition.classClose = options.stickyClass;

		$window.on('scroll', _scrollHandler);

		// Add event handler for the mouseover events
		// this can cause problems with flickering menus!
		if (options.hover) {
			$header.on('mouseenter', _mouseEnterHandler).on('mouseleave', _mouseOutHandler);
		}

		// Set the initial state of the header
		_scrollHandler();

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaGVhZGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHdpbmRvdyIsIndpbmRvdyIsIiRoZWFkZXIiLCJob3ZlciIsImN1cnJlbnRQb3NpdGlvbiIsInNjcm9sbFVwQ291bnRlciIsInRyYW5zaXRpb24iLCJ0aW1lb3V0IiwiZGVmYXVsdHMiLCJoZWFkZXIiLCJzY3JvbGxQb3NpdGlvbiIsInN0aWNreUNsYXNzIiwidG9sZXJhbmNlIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zY3JvbGxIYW5kbGVyIiwicG9zaXRpb24iLCJkb2N1bWVudCIsInNjcm9sbFRvcCIsImhhc0NsYXNzIiwic2Nyb2xsVXAiLCJvcGVuIiwidHJpZ2dlciIsImpzZSIsImxpYnMiLCJ0ZW1wbGF0ZSIsImV2ZW50cyIsIlRSQU5TSVRJT04iLCJPUEVOX0ZMWU9VVCIsImNsZWFyVGltZW91dCIsInNldFRpbWVvdXQiLCJSRVBPU0lUSU9OU19TVElDS1lCT1giLCJfbW91c2VFbnRlckhhbmRsZXIiLCJfbW91c2VPdXRIYW5kbGVyIiwiaW5pdCIsImRvbmUiLCJmaW5kIiwiY2xhc3NDbG9zZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7O0FBS0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLFFBREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLGNBRGpCLENBSEQsRUFPQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUY7O0FBRUUsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxVQUFVRCxFQUFFRSxNQUFGLENBRFg7QUFBQSxLQUVDQyxVQUFVLElBRlg7QUFBQSxLQUdDQyxRQUFRLEtBSFQ7QUFBQSxLQUlDQyxrQkFBa0IsSUFKbkI7QUFBQSxLQUtDQyxrQkFBa0IsQ0FMbkI7QUFBQSxLQU1DQyxhQUFhLEVBTmQ7QUFBQSxL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LEVBREYsQ0FDSyxZQURMLEVBQ21CTixrQkFEbkIsRUFFRU0sRUFGRixDQUVLLFlBRkwsRUFFbUJMLGdCQUZuQjtBQUdBOztBQUVEO0FBQ0FsQjs7QUFFQW9CO0FBQ0EsRUFwQkQ7O0FBc0JBO0FBQ0EsUUFBT3hDLE1BQVA7QUFDQSxDQXhJRiIsImZpbGUiOiJ3aWRnZXRzL2hlYWRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaGVhZGVyLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IGFkZHMgYSBjbGFzcyB0byBhIGRlZmluZWQgb2JqZWN0IGlmIHRoZSBwYWdlIGlzXG4gKiBzY3JvbGxlZCB0byBhIGdpdmVuIHBvc2l0aW9uIGF0IGxlYXN0LiBJdCBpcyB1c2VkIHRvIHNldFxuICogdGhlIGhlYWRlciBzaXplXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J2hlYWRlcicsXG5cblx0W1xuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JHdpbmRvdyA9ICQod2luZG93KSxcblx0XHRcdCRoZWFkZXIgPSBudWxsLFxuXHRcdFx0aG92ZXIgPSBmYWxzZSxcblx0XHRcdGN1cnJlbnRQb3NpdGlvbiA9IG51bGwsXG5cdFx0XHRzY3JvbGxVcENvdW50ZXIgPSAwLFxuXHRcdFx0dHJhbnNpdGlvbiA9IHt9LFxuXHRcdFx0dGltZW91dCA9IDAsXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Ly8gU2VsZWN0b3IgdGhhdCBkZWZpbmVzIHRoZSBoZWFkZXIgZWxlbWVudFxuXHRcdFx0XHRoZWFkZXI6ICcjaGVhZGVyJyxcblx0XHRcdFx0Ly8gUG9zaXRpb24gaW4gcHggdGhhdCBuZWVkcyB0byBiZSByZWFjaGVkIHRvIG1pbmltaXplIHRoZSBoZWFkZXJcblx0XHRcdFx0c2Nyb2xsUG9zaXRpb246IDIwMCxcblx0XHRcdFx0Ly8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIGlmIHRoZSBzY3JvbGxQb3NpdGlvbiBnZXRzIHJlYWNoZWRcblx0XHRcdFx0c3RpY2t5Q2xhc3M6ICdzdGlja3knLFxuXHRcdFx0XHQvLyBNYXhpbWl6ZSB0aGUgdGFyZ2V0IG9uIG1vdXNlIGhvdmVyXG5cdFx0XHRcdGhvdmVyOiBmYWxzZSxcblx0XHRcdFx0Ly8gVG9sZXJhbmNlIGluIHB4IHRoYXQgaXMgdXNlZCB0byBkZXRlY3Qgc2Nyb2xsaW5nIHVwXG5cdFx0XHRcdHRvbGVyYW5jZTogNVxuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgdGhhdCBnZXRzIGNhbGxlZCBieSBzY3JvbGxpbmcgZG93biAvIHVwXG5cdFx0ICogdGhlIHNpdGUuIElmIHRoZSBwb3NpdGlvbiBpcyBsb3dlciB0aGFuIHRoZVxuXHRcdCAqIHNjcm9sbFBvc2l0aW9uIGZyb20gb3B0aW9ucywgdGhlIGhlYWRlciBnZXRzIG1heGltaXplZFxuXHRcdCAqIGVsc2UgaXQgZ2V0cyBtaW5pbWl6ZWRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2Nyb2xsSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHBvc2l0aW9uID0gJChkb2N1bWVudCkuc2Nyb2xsVG9wKCksXG5cdFx0XHRcdGhhc0NsYXNzID0gJGhlYWRlci5oYXNDbGFzcyhvcHRpb25zLnN0aWNreUNsYXNzKSxcblx0XHRcdFx0c2Nyb2xsVXAgPSBjdXJyZW50UG9zaXRpb24gPiBwb3NpdGlvbjtcblxuXHRcdFx0aWYgKHBvc2l0aW9uID4gb3B0aW9ucy5zY3JvbGxQb3NpdGlvbiAmJiAhc2Nyb2xsVXApIHtcblx0XHRcdFx0Ly8gUHJvY2VlZCBpZiBzY3JvbGxpbmcgZG93biB1bmRlciB0aGUgbWluaW11bSBwb3NpdGlvbiBnaXZlbiBieSB0aGUgb3B0aW9uc1xuXHRcdFx0XHRzY3JvbGxVcENvdW50ZXIgPSAwO1xuXHRcdFx0XHRpZiAoIWhhc0NsYXNzICYmICFob3Zlcikge1xuXHRcdFx0XHRcdC8vIFByb2NlZWQgaWYgdGhlIGNsYXNzIGlzbid0IHNldCB5ZXQgYW5kIHRoZSBoZWFkZXIgaXNuJ3QgaG92ZXJlZCB3aXRoIHRoZSBtb3VzZVxuXHRcdFx0XHRcdHRyYW5zaXRpb24ub3BlbiA9IGZhbHNlO1xuXHRcdFx0XHRcdCRoZWFkZXJcblx0XHRcdFx0XHRcdC50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pXG5cdFx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuT1BFTl9GTFlPVVQoKSwgWyR0aGlzXSk7XG5cdFx0XHRcdH1cblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdHNjcm9sbFVwQ291bnRlciArPSAxO1xuXHRcdFx0XHRpZiAoaGFzQ2xhc3MgJiYgKG9wdGlvbnMuc2Nyb2xsUG9zaXRpb24gPiBwb3NpdGlvbiB8fCBzY3JvbGxVcENvdW50ZXIgPiBvcHRpb25zLnRvbGVyYW5jZSkpIHtcblx0XHRcdFx0XHQvLyBQcm9jZWVkIGlmIHRoZSB0aGUgbWluaW11bSBwb3NpdGlvbiBzZXQgaW4gdGhlIG9wdGlvbiBpc24ndCByZWFjaGVkXG5cdFx0XHRcdFx0Ly8gb3IgYSBzcGVjaWZpYyBjb3VudCBvZiBwaXhlbCBpcyBzY3JvbGxlZCB1cFxuXHRcdFx0XHRcdHRyYW5zaXRpb24ub3BlbiA9IHRydWU7XG5cdFx0XHRcdFx0JGhlYWRlci50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cblx0XHRcdGNsZWFyVGltZW91dCh0aW1lb3V0KTtcblx0XHRcdHRpbWVvdXQgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkd2luZG93LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlJFUE9TSVRJT05TX1NUSUNLWUJPWCgpKTtcblx0XHRcdH0sIDI1MCk7XG5cblx0XHRcdC8vIFN0b3JlIHRoZSBjdXJyZW50IHBvc2l0aW9uXG5cdFx0XHRjdXJyZW50UG9zaXRpb24gPSBwb3NpdGlvbjtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlciBmb3IgdGhlIG1vdXNlZW50ZXIgZXZlbnQgb24gdGhlXG5cdFx0ICogaGVhZGVyLiBJdCB3aWxsIHJlbW92ZSB0aGUgbWluaW1pemVyLWNsYXNzXG5cdFx0ICogZnJvbSB0aGUgaGVhZGVyIGNvbnRhaW5lciBhbmQgc2V0IHRoZSBpbnRlcm5hbFxuXHRcdCAqIGhlYWRlciBob3ZlciBzdGF0ZSB0byB0cnVlXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX21vdXNlRW50ZXJIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRob3ZlciA9IHRydWU7XG5cdFx0XHR0cmFuc2l0aW9uLm9wZW4gPSB0cnVlO1xuXHRcdFx0JGhlYWRlci50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OKCksIHRyYW5zaXRpb24pO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVyIGZvciB0aGUgbW91c2VvdXQgZXZlbnQgb24gdGhlIGhlYWRlclxuXHRcdCAqIGNvbnRhaW5lci4gT24gbW91c2Ugb3V0LCB0aGUgaG92ZXIgc3RhdGUgd2lsbFxuXHRcdCAqIGJlIHNldCB0byBmYWxzZSwgYW5kIHRoZSBoZWFkZXIgc3RhdGUgd2lsbCBiZVxuXHRcdCAqIHNldCBieSB0aGUgY3VycmVudCBzY3JvbGwgcG9zaXRpb25cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbW91c2VPdXRIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRob3ZlciA9IGZhbHNlO1xuXHRcdFx0X3Njcm9sbEhhbmRsZXIoKTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRcdCRoZWFkZXIgPSAkdGhpcy5maW5kKG9wdGlvbnMuaGVhZGVyKTtcblx0XHRcdGN1cnJlbnRQb3NpdGlvbiA9ICQoZG9jdW1lbnQpLnNjcm9sbFRvcCgpO1xuXHRcdFx0dHJhbnNpdGlvbi5jbGFzc0Nsb3NlID0gb3B0aW9ucy5zdGlja3lDbGFzcztcblxuXHRcdFx0JHdpbmRvdy5vbignc2Nyb2xsJywgX3Njcm9sbEhhbmRsZXIpO1xuXG5cdFx0XHQvLyBBZGQgZXZlbnQgaGFuZGxlciBmb3IgdGhlIG1vdXNlb3ZlciBldmVudHNcblx0XHRcdC8vIHRoaXMgY2FuIGNhdXNlIHByb2JsZW1zIHdpdGggZmxpY2tlcmluZyBtZW51cyFcblx0XHRcdGlmIChvcHRpb25zLmhvdmVyKSB7XG5cdFx0XHRcdCRoZWFkZXJcblx0XHRcdFx0XHQub24oJ21vdXNlZW50ZXInLCBfbW91c2VFbnRlckhhbmRsZXIpXG5cdFx0XHRcdFx0Lm9uKCdtb3VzZWxlYXZlJywgX21vdXNlT3V0SGFuZGxlcik7XG5cdFx0XHR9XG5cblx0XHRcdC8vIFNldCB0aGUgaW5pdGlhbCBzdGF0ZSBvZiB0aGUgaGVhZGVyXG5cdFx0XHRfc2Nyb2xsSGFuZGxlcigpO1xuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
