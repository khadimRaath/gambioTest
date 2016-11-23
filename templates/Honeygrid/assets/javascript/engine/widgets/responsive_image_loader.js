'use strict';

/* --------------------------------------------------------------
 responsive_image_loader.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Example:
 *
 * <img src="img/testbild4_320.jpg" title="Testbild" alt="Testbild" data-image-xs="img/testbild4_320.jpg"
 *      data-image-sm="img/testbild4_640.jpg" data-image-md="img/testbild4_1024.jpg"
 *      data-image-lg="img/testbild4_1600.jpg"/>
 */
gambio.widgets.module('responsive_image_loader', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		breakpoints: ['xs', 'sm', 'md', 'lg']
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ######### HELPER FUNCTIONS ##########

	/**
  * Helper function that registers the ":attr"
  * selector to jQuery. With this one it's possible
  * to select elements with an regular expression
  * @private
  */
	var _registerSelector = function _registerSelector() {
		if ($.expr.pseudos.attr === undefined) {
			$.expr.pseudos.attr = $.expr.createPseudo(function (arg) {
				var regexp = new RegExp(arg);
				return function (elem) {
					for (var i = 0; i < elem.attributes.length; i++) {
						var attr = elem.attributes[i];
						if (regexp.test(attr.name)) {
							return true;
						}
					}
					return false;
				};
			});
		}
	};

	// ########## MAIN FUNCTIONALITY ##########

	/**
  * Function that searches for the best fitting image
  * for the parent container, so that it can set the src-attribute
  * inside the img-tag
  * @param       {object}    $target     jQuery selection that contains the image to set (optional)
  * @private
  */
	var _resizeImages = function _resizeImages($target) {
		var $self = $(this),
		    breakpoint = jse.libs.template.responsive.breakpoint(),
		    $elements = $target && $target.length ? $target : $self.filter(':attr(^data-image)').add($self.find(':attr(^data-image)'));

		// Iterate trough every image element
		// and check if there is a new image
		// size to set
		$elements.not('.lazyLoading').each(function () {

			var $element = $(this),
			    breakpoint = jse.libs.template.responsive.breakpoint(),
			    bp = options.breakpoints.indexOf(breakpoint.name),
			    bpCount = options.breakpoints.length,
			    img = null;

			for (bp; bp < bpCount; bp += 1) {
				var attrName = 'data-image-' + options.breakpoints[bp],
				    value = $element.attr(attrName);

				if (value) {
					img = value;
					break;
				}
			}

			if (!img) {
				img = $element.attr('data-image');
			}

			// If an image was found and the target element has a
			// different value inside it's src-attribute set the
			// new value
			if (img && $element.attr('src') !== img) {
				$element.attr('src', img);
			}
		});
	};

	/**
  * Function that initializes the lazy loading
  * capability of images
  * @private
  */
	var _registerLazyLoading = function _registerLazyLoading() {
		var $elements = $this.filter('.lazyLoading:attr(^data-image)').add($this.find('.lazyLoading:attr(^data-image)'));

		/**
   * Function that scans the given elements for images
   * that are in the viewport and set the source attribute
   * @private
   */
		var _lazyLoadingScrollHandler = function _lazyLoadingScrollHandler() {

			var windowWidth = $(window).width(),
			    windowHeight = $(window).height(),
			    top = $(window).scrollTop(),
			    left = $(window).scrollLeft();

			$elements.each(function () {
				var $self = $(this),
				    offset = $self.offset();

				if (offset.top < top + windowHeight || offset.left < left + windowWidth) {
					$elements = $elements.not($self);
					$self.trigger('lazyLoadImage');
				}
			});
		};

		/**
   * Removes the class "lazyLoading" from the image
   * so that the "_resizeImages" is able to select it
   * Afterwards execute this function to set the
   * correct image source
   * @param       {object}    e       jQuery event object
   * @private
   */
		var _loadImage = function _loadImage(e) {
			e.stopPropagation();

			var $self = $(this);
			$self.removeClass('lazyLoading');
			_resizeImages($self);
		};

		// Add an event handler for loading the first real image
		// to every image element that is only executed once
		$elements.each(function () {
			$(this).one('lazyLoadImage', _loadImage);
		});

		// Add event handler to every event that changes the dimension / viewport
		$(window).on('scroll windowWasResized', _lazyLoadingScrollHandler);

		// Load images that are in view on load
		_lazyLoadingScrollHandler();
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		_registerSelector();
		_registerLazyLoading();

		$(window).on(jse.libs.template.events.BREAKPOINT(), function () {
			_resizeImages.call($this);
		});

		_resizeImages.call($this);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcmVzcG9uc2l2ZV9pbWFnZV9sb2FkZXIuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImJyZWFrcG9pbnRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9yZWdpc3RlclNlbGVjdG9yIiwiZXhwciIsInBzZXVkb3MiLCJhdHRyIiwidW5kZWZpbmVkIiwiY3JlYXRlUHNldWRvIiwiYXJnIiwicmVnZXhwIiwiUmVnRXhwIiwiZWxlbSIsImkiLCJhdHRyaWJ1dGVzIiwibGVuZ3RoIiwidGVzdCIsIm5hbWUiLCJfcmVzaXplSW1hZ2VzIiwiJHRhcmdldCIsIiRzZWxmIiwiYnJlYWtwb2ludCIsImpzZSIsImxpYnMiLCJ0ZW1wbGF0ZSIsInJlc3BvbnNpdmUiLCIkZWxlbWVudHMiLCJmaWx0ZXIiLCJhZGQiLCJmaW5kIiwibm90IiwiZWFjaCIsIiRlbGVtZW50IiwiYnAiLCJpbmRleE9mIiwiYnBDb3VudCIsImltZyIsImF0dHJOYW1lIiwidmFsdWUiLCJfcmVnaXN0ZXJMYXp5TG9hZGluZyIsIl9sYXp5TG9hZGluZ1Njcm9sbEhhbmRsZXIiLCJ3aW5kb3dXaWR0aCIsIndpbmRvdyIsIndpZHRoIiwid2luZG93SGVpZ2h0IiwiaGVpZ2h0IiwidG9wIiwic2Nyb2xsVG9wIiwibGVmdCIsInNjcm9sbExlZnQiLCJvZmZzZXQiLCJ0cmlnZ2VyIiwiX2xvYWRJbWFnZSIsImUiLCJzdG9wUHJvcGFnYXRpb24iLCJyZW1vdmVDbGFzcyIsIm9uZSIsIm9uIiwiaW5pdCIsImRvbmUiLCJldmVudHMiLCJCUkVBS1BPSU5UIiwiY2FsbCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLHlCQURELEVBR0MsQ0FDQ0YsT0FBT0csTUFBUCxHQUFnQixjQURqQixFQUVDSCxPQUFPRyxNQUFQ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LENBQWUsWUFBVztBQUN6QmpDLEtBQUUsSUFBRixFQUFRMEQsR0FBUixDQUFZLGVBQVosRUFBNkJKLFVBQTdCO0FBQ0EsR0FGRDs7QUFJQTtBQUNBdEQsSUFBRTRDLE1BQUYsRUFBVWUsRUFBVixDQUFhLHlCQUFiLEVBQXdDakIseUJBQXhDOztBQUVBO0FBQ0FBO0FBQ0EsRUF2REQ7O0FBMERGOztBQUVFOzs7O0FBSUE5QyxRQUFPZ0UsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUJ4RDtBQUNBb0M7O0FBRUF6QyxJQUFFNEMsTUFBRixFQUFVZSxFQUFWLENBQWFuQyxJQUFJQyxJQUFKLENBQVNDLFFBQVQsQ0FBa0JvQyxNQUFsQixDQUF5QkMsVUFBekIsRUFBYixFQUFvRCxZQUFXO0FBQzlEM0MsaUJBQWM0QyxJQUFkLENBQW1CakUsS0FBbkI7QUFDQSxHQUZEOztBQUlBcUIsZ0JBQWM0QyxJQUFkLENBQW1CakUsS0FBbkI7O0FBRUE4RDtBQUNBLEVBWkQ7O0FBY0E7QUFDQSxRQUFPakUsTUFBUDtBQUNBLENBekxGIiwiZmlsZSI6IndpZGdldHMvcmVzcG9uc2l2ZV9pbWFnZV9sb2FkZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHJlc3BvbnNpdmVfaW1hZ2VfbG9hZGVyLmpzIDIwMTYtMDMtMDlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEV4YW1wbGU6XG4gKlxuICogPGltZyBzcmM9XCJpbWcvdGVzdGJpbGQ0XzMyMC5qcGdcIiB0aXRsZT1cIlRlc3RiaWxkXCIgYWx0PVwiVGVzdGJpbGRcIiBkYXRhLWltYWdlLXhzPVwiaW1nL3Rlc3RiaWxkNF8zMjAuanBnXCJcbiAqICAgICAgZGF0YS1pbWFnZS1zbT1cImltZy90ZXN0YmlsZDRfNjQwLmpwZ1wiIGRhdGEtaW1hZ2UtbWQ9XCJpbWcvdGVzdGJpbGQ0XzEwMjQuanBnXCJcbiAqICAgICAgZGF0YS1pbWFnZS1sZz1cImltZy90ZXN0YmlsZDRfMTYwMC5qcGdcIi8+XG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J3Jlc3BvbnNpdmVfaW1hZ2VfbG9hZGVyJyxcblxuXHRbXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvcmVzcG9uc2l2ZSdcblx0XSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHRicmVha3BvaW50czogWyd4cycsICdzbScsICdtZCcsICdsZyddXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuLy8gIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgcmVnaXN0ZXJzIHRoZSBcIjphdHRyXCJcblx0XHQgKiBzZWxlY3RvciB0byBqUXVlcnkuIFdpdGggdGhpcyBvbmUgaXQncyBwb3NzaWJsZVxuXHRcdCAqIHRvIHNlbGVjdCBlbGVtZW50cyB3aXRoIGFuIHJlZ3VsYXIgZXhwcmVzc2lvblxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9yZWdpc3RlclNlbGVjdG9yID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJC5leHByLnBzZXVkb3MuYXR0ciA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdCQuZXhwci5wc2V1ZG9zLmF0dHIgPSAkLmV4cHIuY3JlYXRlUHNldWRvKGZ1bmN0aW9uKGFyZykge1xuXHRcdFx0XHRcdHZhciByZWdleHAgPSBuZXcgUmVnRXhwKGFyZyk7XG5cdFx0XHRcdFx0cmV0dXJuIGZ1bmN0aW9uKGVsZW0pIHtcblx0XHRcdFx0XHRcdGZvciAodmFyIGkgPSAwOyBpIDwgZWxlbS5hdHRyaWJ1dGVzLmxlbmd0aDsgaSsrKSB7XG5cdFx0XHRcdFx0XHRcdHZhciBhdHRyID0gZWxlbS5hdHRyaWJ1dGVzW2ldO1xuXHRcdFx0XHRcdFx0XHRpZiAocmVnZXhwLnRlc3QoYXR0ci5uYW1lKSkge1xuXHRcdFx0XHRcdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHRcdFx0fTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIE1BSU4gRlVOQ1RJT05BTElUWSAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBGdW5jdGlvbiB0aGF0IHNlYXJjaGVzIGZvciB0aGUgYmVzdCBmaXR0aW5nIGltYWdlXG5cdFx0ICogZm9yIHRoZSBwYXJlbnQgY29udGFpbmVyLCBzbyB0aGF0IGl0IGNhbiBzZXQgdGhlIHNyYy1hdHRyaWJ1dGVcblx0XHQgKiBpbnNpZGUgdGhlIGltZy10YWdcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgJHRhcmdldCAgICAgalF1ZXJ5IHNlbGVjdGlvbiB0aGF0IGNvbnRhaW5zIHRoZSBpbWFnZSB0byBzZXQgKG9wdGlvbmFsKVxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9yZXNpemVJbWFnZXMgPSBmdW5jdGlvbigkdGFyZ2V0KSB7XG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRicmVha3BvaW50ID0ganNlLmxpYnMudGVtcGxhdGUucmVzcG9uc2l2ZS5icmVha3BvaW50KCksXG5cdFx0XHRcdCRlbGVtZW50cyA9ICgkdGFyZ2V0ICYmICR0YXJnZXQubGVuZ3RoKSA/ICR0YXJnZXQgOiAkc2VsZlxuXHRcdFx0XHRcdC5maWx0ZXIoJzphdHRyKF5kYXRhLWltYWdlKScpXG5cdFx0XHRcdFx0LmFkZCgkc2VsZi5maW5kKCc6YXR0ciheZGF0YS1pbWFnZSknKSk7XG5cblx0XHRcdC8vIEl0ZXJhdGUgdHJvdWdoIGV2ZXJ5IGltYWdlIGVsZW1lbnRcblx0XHRcdC8vIGFuZCBjaGVjayBpZiB0aGVyZSBpcyBhIG5ldyBpbWFnZVxuXHRcdFx0Ly8gc2l6ZSB0byBzZXRcblx0XHRcdCRlbGVtZW50c1xuXHRcdFx0XHQubm90KCcubGF6eUxvYWRpbmcnKVxuXHRcdFx0XHQuZWFjaChmdW5jdGlvbigpIHtcblxuXHRcdFx0XHRcdHZhciAkZWxlbWVudCA9ICQodGhpcyksXG5cdFx0XHRcdFx0XHRicmVha3BvaW50ID0ganNlLmxpYnMudGVtcGxhdGUucmVzcG9uc2l2ZS5icmVha3BvaW50KCksXG5cdFx0XHRcdFx0XHRicCA9IG9wdGlvbnMuYnJlYWtwb2ludHMuaW5kZXhPZihicmVha3BvaW50Lm5hbWUpLFxuXHRcdFx0XHRcdFx0YnBDb3VudCA9IG9wdGlvbnMuYnJlYWtwb2ludHMubGVuZ3RoLFxuXHRcdFx0XHRcdFx0aW1nID0gbnVsbDtcblxuXHRcdFx0XHRcdGZvciAoYnA7IGJwIDwgYnBDb3VudDsgYnAgKz0gMSkge1xuXHRcdFx0XHRcdFx0dmFyIGF0dHJOYW1lID0gJ2RhdGEtaW1hZ2UtJyArIG9wdGlvbnMuYnJlYWtwb2ludHNbYnBdLFxuXHRcdFx0XHRcdFx0XHR2YWx1ZSA9ICRlbGVtZW50LmF0dHIoYXR0ck5hbWUpO1xuXG5cdFx0XHRcdFx0XHRpZiAodmFsdWUpIHtcblx0XHRcdFx0XHRcdFx0aW1nID0gdmFsdWU7XG5cdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdGlmICghaW1nKSB7XG5cdFx0XHRcdFx0XHRpbWcgPSAkZWxlbWVudC5hdHRyKCdkYXRhLWltYWdlJyk7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gSWYgYW4gaW1hZ2Ugd2FzIGZvdW5kIGFuZCB0aGUgdGFyZ2V0IGVsZW1lbnQgaGFzIGFcblx0XHRcdFx0XHQvLyBkaWZmZXJlbnQgdmFsdWUgaW5zaWRlIGl0J3Mgc3JjLWF0dHJpYnV0ZSBzZXQgdGhlXG5cdFx0XHRcdFx0Ly8gbmV3IHZhbHVlXG5cdFx0XHRcdFx0aWYgKGltZyAmJiAkZWxlbWVudC5hdHRyKCdzcmMnKSAhPT0gaW1nKSB7XG5cdFx0XHRcdFx0XHQkZWxlbWVudC5hdHRyKCdzcmMnLCBpbWcpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0fTtcblxuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBpbml0aWFsaXplcyB0aGUgbGF6eSBsb2FkaW5nXG5cdFx0ICogY2FwYWJpbGl0eSBvZiBpbWFnZXNcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcmVnaXN0ZXJMYXp5TG9hZGluZyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRlbGVtZW50cyA9ICR0aGlzXG5cdFx0XHRcdC5maWx0ZXIoJy5sYXp5TG9hZGluZzphdHRyKF5kYXRhLWltYWdlKScpXG5cdFx0XHRcdC5hZGQoJHRoaXMuZmluZCgnLmxhenlMb2FkaW5nOmF0dHIoXmRhdGEtaW1hZ2UpJykpO1xuXG5cdFx0XHQvKipcblx0XHRcdCAqIEZ1bmN0aW9uIHRoYXQgc2NhbnMgdGhlIGdpdmVuIGVsZW1lbnRzIGZvciBpbWFnZXNcblx0XHRcdCAqIHRoYXQgYXJlIGluIHRoZSB2aWV3cG9ydCBhbmQgc2V0IHRoZSBzb3VyY2UgYXR0cmlidXRlXG5cdFx0XHQgKiBAcHJpdmF0ZVxuXHRcdFx0ICovXG5cdFx0XHR2YXIgX2xhenlMb2FkaW5nU2Nyb2xsSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRcdHZhciB3aW5kb3dXaWR0aCA9ICQod2luZG93KS53aWR0aCgpLFxuXHRcdFx0XHRcdHdpbmRvd0hlaWdodCA9ICQod2luZG93KS5oZWlnaHQoKSxcblx0XHRcdFx0XHR0b3AgPSAkKHdpbmRvdykuc2Nyb2xsVG9wKCksXG5cdFx0XHRcdFx0bGVmdCA9ICQod2luZG93KS5zY3JvbGxMZWZ0KCk7XG5cblx0XHRcdFx0JGVsZW1lbnRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdG9mZnNldCA9ICRzZWxmLm9mZnNldCgpO1xuXG5cdFx0XHRcdFx0aWYgKG9mZnNldC50b3AgPCAodG9wICsgd2luZG93SGVpZ2h0KSB8fCBvZmZzZXQubGVmdCA8IChsZWZ0ICsgd2luZG93V2lkdGgpKSB7XG5cdFx0XHRcdFx0XHQkZWxlbWVudHMgPSAkZWxlbWVudHMubm90KCRzZWxmKTtcblx0XHRcdFx0XHRcdCRzZWxmLnRyaWdnZXIoJ2xhenlMb2FkSW1hZ2UnKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0fTtcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZW1vdmVzIHRoZSBjbGFzcyBcImxhenlMb2FkaW5nXCIgZnJvbSB0aGUgaW1hZ2Vcblx0XHRcdCAqIHNvIHRoYXQgdGhlIFwiX3Jlc2l6ZUltYWdlc1wiIGlzIGFibGUgdG8gc2VsZWN0IGl0XG5cdFx0XHQgKiBBZnRlcndhcmRzIGV4ZWN1dGUgdGhpcyBmdW5jdGlvbiB0byBzZXQgdGhlXG5cdFx0XHQgKiBjb3JyZWN0IGltYWdlIHNvdXJjZVxuXHRcdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0dmFyIF9sb2FkSW1hZ2UgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKTtcblx0XHRcdFx0JHNlbGYucmVtb3ZlQ2xhc3MoJ2xhenlMb2FkaW5nJyk7XG5cdFx0XHRcdF9yZXNpemVJbWFnZXMoJHNlbGYpO1xuXHRcdFx0fTtcblxuXHRcdFx0Ly8gQWRkIGFuIGV2ZW50IGhhbmRsZXIgZm9yIGxvYWRpbmcgdGhlIGZpcnN0IHJlYWwgaW1hZ2Vcblx0XHRcdC8vIHRvIGV2ZXJ5IGltYWdlIGVsZW1lbnQgdGhhdCBpcyBvbmx5IGV4ZWN1dGVkIG9uY2Vcblx0XHRcdCRlbGVtZW50cy5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkKHRoaXMpLm9uZSgnbGF6eUxvYWRJbWFnZScsIF9sb2FkSW1hZ2UpO1xuXHRcdFx0fSk7XG5cblx0XHRcdC8vIEFkZCBldmVudCBoYW5kbGVyIHRvIGV2ZXJ5IGV2ZW50IHRoYXQgY2hhbmdlcyB0aGUgZGltZW5zaW9uIC8gdmlld3BvcnRcblx0XHRcdCQod2luZG93KS5vbignc2Nyb2xsIHdpbmRvd1dhc1Jlc2l6ZWQnLCBfbGF6eUxvYWRpbmdTY3JvbGxIYW5kbGVyKTtcblxuXHRcdFx0Ly8gTG9hZCBpbWFnZXMgdGhhdCBhcmUgaW4gdmlldyBvbiBsb2FkXG5cdFx0XHRfbGF6eUxvYWRpbmdTY3JvbGxIYW5kbGVyKCk7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHRfcmVnaXN0ZXJTZWxlY3RvcigpO1xuXHRcdFx0X3JlZ2lzdGVyTGF6eUxvYWRpbmcoKTtcblxuXHRcdFx0JCh3aW5kb3cpLm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5CUkVBS1BPSU5UKCksIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfcmVzaXplSW1hZ2VzLmNhbGwoJHRoaXMpO1xuXHRcdFx0fSk7XG5cblx0XHRcdF9yZXNpemVJbWFnZXMuY2FsbCgkdGhpcyk7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
