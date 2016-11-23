'use strict';

/* --------------------------------------------------------------
 cart_dropdown.js 2016-07-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Enables the functionality of the cart dropdown, to open
 * via an event. This is needed to open the flyout after
 * an item is added to the cart
 */
gambio.widgets.module('cart_dropdown', ['xhr', gambio.source + '/libs/events'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $window = $(window),
	    $body = $('body'),
	    $item = null,
	    $target = null,
	    isCartDropdownSticky = false,
	    timer = null,
	    animateTimer = null,
	    ajax = null,
	    ajaxData = {
		part1: 'header',
		part2: 'dropdown'
	},
	    defaults = {
		// Default delay (in ms) after which the flyout closes
		delay: 5000,
		// Update request url
		url: 'shop.php?do=CartDropdown',
		// Selection of the container the result gets filled in
		fillTarget: 'header',
		// Duration that the count badge gets resized after adding an item to the basket
		countAnimation: 2000,
		// AJAX response content selectors
		selectorMapping: {
			cartDropdown: '.cart-dropdown',
			cartDropdownProducts: '.products',
			cartDropdownProductsCount: '.cart-products-count'
		}
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Helper function that scroll the list
  * down to the end
  * @private
  */
	var _scrollDown = function _scrollDown() {
		var $list = $this.find('.products-list'),
		    height = $list.outerHeight() * 2; // Multiply with 2 to be sure that it gets scrolled to the bottom

		$list.animate({ 'scrollTop': height + 'px' }, 0);
	};

	/**
  * Triggers the mouseenter event
  * on the cartdropdown link
  * @param       {object}        e       jQuery event object
  * @private
  */
	var _open = function _open(e) {
		e.stopPropagation();

		if ($(defaults.selectorMapping.cartDropdownProductsCount).text() !== '0') {
			$(defaults.selectorMapping.cartDropdownProductsCount).removeClass('hidden');
		}

		$item.trigger('mouseenter', { prog: true });
	};

	/**
  * Triggers the mouseleave event
  * on the cartdropdown link
  * @param       {object}        e       jQuery event object
  * @private
  */
	var _close = function _close(e) {
		e.stopPropagation();
		$item.trigger('mouseleave', { prog: true });
	};

	/**
  * Helper function that resizes the count badge
  * after the add of an item to the basket for
  * a specific duration
  * @param       {string}    selector        Text value of the old badge (the count)
  * @param       {object}    config          The config for the badges from the ajax result content
  * @private
  */
	var _resizeCountBadge = function _resizeCountBadge(currentCount, config) {
		if (options.selectorMapping[config.selector] === undefined) {
			jse.core.debug.warn('The selector mapping "' + config.selector + '" doesn\'t exist.');
			return true;
		}

		var count = $(config.value).text(),
		    $counts = $target.find(options.selectorMapping[config.selector]);

		if (currentCount !== count) {
			if (animateTimer) {
				clearTimeout(animateTimer);
			}

			$counts.addClass('big');
			animateTimer = setTimeout(function () {
				$counts.removeClass('big');
			}, options.countAnimation);
		}
	};

	/**
  * Updates the dropdown with data from
  * the server and opens the layer for a
  * certain time
  * @param       {object}        e               jQuery event object
  * @param       {boolean}       openDropdown    Defines if the dropdown shall be opened after update
  * @private
  */
	var _update = function _update(e, openDropdown) {
		if (ajax) {
			ajax.abort();
		}

		ajax = jse.libs.xhr.ajax({ url: options.url, data: ajaxData }).done(function (result) {
			if (options.selectorMapping[result.content.count.selector] === undefined) {
				jse.core.debug.warn('The selector mapping "' + result.content.count.selector + '" doesn\'t exist.');
				return true;
			}

			var count = $(options.selectorMapping[result.content.count.selector]).first().text();
			jse.libs.template.helpers.fill(result.content, $target, options.selectorMapping);
			_resizeCountBadge(count, result.content.count);

			_scrollDown();

			if (openDropdown) {
				$this.trigger(jse.libs.template.events.CART_OPEN(), []);
				timer = setTimeout(function () {
					$this.trigger(jse.libs.template.events.CART_CLOSE(), []);
				}, options.delay);
			}
		});
	};

	/**
  * Event handler that listens on the
  * mouseenter / leave events. If these
  * events are not triggered by this script
  * stop the timer, because the user has
  * moved the mouse cursor over the object
  * @param       {object}        e       jQuery event object
  * @param       {object}        d       JSON which contains the status if the program triggered the event
  * @private
  */
	var _preventExec = function _preventExec(e, d) {
		if ((!d || !d.prog) && timer) {
			clearTimeout(timer);
		}
	};

	/**
  * Sticky Cart Dropdown 
  * 
  * There are cases when the user adds something to the cart and this pops out but it cannot be seen cause
  * it is out of the viewport (e.g. user has scrolled to bottom). This method will make sure that the cart
  * dropdown is always visible by applying a "sticky" positioning to respective elements.
  * 
  * @private
  */
	var _stickyCartDropdown = function _stickyCartDropdown() {
		// If the cart dropdown is not visible wait until the transition completes (see menu.js). 
		if (!$item.hasClass('open')) {
			var interval = setInterval(function () {
				if ($item.hasClass('open')) {
					_stickyCartDropdown();
					clearInterval(interval);
				}
			}, 100);

			isCartDropdownSticky = false;
			return;
		}

		var $cartDropdown = $(options.selectorMapping.cartDropdown);
		var cartDropdownOffset = $cartDropdown.offset();

		// Enable "sticky" position in order to make the cart dropdown visible to the user.
		if (!isCartDropdownSticky && cartDropdownOffset.top < $(window).scrollTop()) {
			$cartDropdown.css({
				position: 'fixed',
				top: 20,
				left: cartDropdownOffset.left
			});

			isCartDropdownSticky = true;
		}

		// Reset sticky position once the user has scrolled to top. 
		if (isCartDropdownSticky && cartDropdownOffset.top < $item.offset().top) {
			$cartDropdown.css({
				position: '',
				top: '',
				left: ''
			});

			isCartDropdownSticky = false;
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  *
  * @constructor
  */
	module.init = function (done) {

		$item = $this.find('> ul > li');
		$target = options.fillTarget ? $(options.fillTarget) : $this;

		$window.on('focus', _update).on('scroll', _stickyCartDropdown);

		$body.on(jse.libs.template.events.CART_OPEN(), _open).on(jse.libs.template.events.CART_CLOSE(), _close).on(jse.libs.template.events.CART_UPDATE(), _update);

		$item.on('mouseenter mouseleave', _preventExec).on('mouseenter', _stickyCartDropdown);

		_scrollDown();

		if (location.search.search('open_cart_dropdown=1') !== -1) {
			$body.trigger(jse.libs.template.events.CART_OPEN());
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2FydF9kcm9wZG93bi5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCIkYm9keSIsIiRpdGVtIiwiJHRhcmdldCIsImlzQ2FydERyb3Bkb3duU3RpY2t5IiwidGltZXIiLCJhbmltYXRlVGltZXIiLCJhamF4IiwiYWpheERhdGEiLCJwYXJ0MSIsInBhcnQyIiwiZGVmYXVsdHMiLCJkZWxheSIsInVybCIsImZpbGxUYXJnZXQiLCJjb3VudEFuaW1hdGlvbiIsInNlbGVjdG9yTWFwcGluZyIsImNhcnREcm9wZG93biIsImNhcnREcm9wZG93blByb2R1Y3RzIiwiY2FydERyb3Bkb3duUHJvZHVjdHNDb3VudCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfc2Nyb2xsRG93biIsIiRsaXN0IiwiZmluZCIsImhlaWdodCIsIm91dGVySGVpZ2h0IiwiYW5pbWF0ZSIsIl9vcGVuIiwiZSIsInN0b3BQcm9wYWdhdGlvbiIsInRleHQiLCJyZW1vdmVDbGFzcyIsInRyaWdnZXIiLCJwcm9nIiwiX2Nsb3NlIiwiX3Jlc2l6ZUNvdW50QmFkZ2UiLCJjdXJyZW50Q291bnQiLCJjb25maWciLCJzZWxlY3RvciIsInVuZGVmaW5lZCIsImpzZSIsImNvcmUiLCJkZWJ1ZyIsIndhcm4iLCJjb3VudCIsInZhbHVlIiwiJGNvdW50cyIsImNsZWFyVGltZW91dCIsImFkZENsYXNzIiwic2V0VGltZW91dCIsIl91cGRhdGUiLCJvcGVuRHJvcGRvd24iLCJhYm9ydCIsImxpYnMiLCJ4aHIiLCJkb25lIiwicmVzdWx0IiwiY29udGVudCIsImZpcnN0IiwidGVtcGxhdGUiLCJoZWxwZXJzIiwiZmlsbCIsImV2ZW50cyIsIkNBUlRfT1BFTiIsIkNBUlRfQ0xPU0UiLCJfcHJldmVudEV4ZWMiLCJkIiwiX3N0aWNreUNhcnREcm9wZG93biIsImhhc0NsYXNzIiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImNsZWFySW50ZXJ2YWwiLCIkY2FydERyb3Bkb3duIiwiY2FydERyb3Bkb3duT2Zmc2V0Iiwib2Zmc2V0IiwidG9wIiwic2Nyb2xsVG9wIiwiY3NzIiwicG9zaXRpb24iLCJsZWZ0IiwiaW5pdCIsIm9uIiwiQ0FSVF9VUERBVEUiLCJsb2NhdGlvbiIsInNlYXJjaCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxlQURELEVBR0MsQ0FDQyxLQURELEVBRUNGLE9BQU9HLE1BQVAsR0FBZ0IsY0FGakIsQ0FIRCxFQVFDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFRjs7QUFFRSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFVBQVVELEVBQUVFLE1BQUYsQ0FEWDtBQUFBLEtBRUNDLFFBQVFILEVBQUUsTUFBRixDQUZUO0FBQUEsS0FHQ0ksUUFBUSxJQUhUO0FBQUEsS0FJQ0MsVUFBVSxJQUpYO0FBQUEsS0FLQ0MsdUJBQXVCLEtBTHhCO0FBQUEsS0FNQ0MsUUFBUSxJQU5UO0FBQUEsS0FPQ0MsZUFBZSxJQVBoQjtBQUFBLEtBUUNDLE9BQU8sSUFSUjtBQUFBLEtBU0NDLFdBQVc7QUFDVkMsU0FBTyxRQURHO0FBRVZDLFNBQU87QUFGRyxFQVRaO0FBQUEsS0FhQ0MsV0FBVztBQUNWO0FBQ0FDLFNBQU8sSUFGRztBQUdWO0FBQ0FDLE9BQUssMEJBSks7QUFLVjtBQUNBQyxjQUFZLFFBTkY7QUFPVjtBQUNBQyxrQkFBZ0IsSUFSTjtBQVNWO0FBQ0FDLG1CQUFpQjtBQUNoQkMsaUJBQWMsZ0JBREU7QUFFaEJDLHlCQUFzQixXQUZOO0FBR2hCQyw4QkFBMkI7QUFIWDtBQVZQLEVBYlo7QUFBQSxLQTZCQ0MsVUFBVXRCLEVBQUV1QixNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJWLFFBQW5CLEVBQTZCZixJQUE3QixDQTdCWDtBQUFBLEtBOEJDRixTQUFTLEVBOUJW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QVNNLFFBQVQsQ0FBa0JHLE1BQWxCLENBQXlCRSxVQUF6QixFQUZMLEVBRTRDOUIsTUFGNUMsRUFHRStDLEVBSEYsQ0FHS3pDLElBQUlhLElBQUosQ0FBU00sUUFBVCxDQUFrQkcsTUFBbEIsQ0FBeUJvQixXQUF6QixFQUhMLEVBRzZDaEMsT0FIN0M7O0FBS0FqRCxRQUNFZ0YsRUFERixDQUNLLHVCQURMLEVBQzhCaEIsWUFEOUIsRUFFRWdCLEVBRkYsQ0FFSyxZQUZMLEVBRW1CZCxtQkFGbkI7O0FBSUE5Qzs7QUFFQSxNQUFJOEQsU0FBU0MsTUFBVCxDQUFnQkEsTUFBaEIsQ0FBdUIsc0JBQXZCLE1BQW1ELENBQUMsQ0FBeEQsRUFBMkQ7QUFDMURwRixTQUFNZ0MsT0FBTixDQUFjUSxJQUFJYSxJQUFKLENBQVNNLFFBQVQsQ0FBa0JHLE1BQWxCLENBQXlCQyxTQUF6QixFQUFkO0FBQ0E7O0FBRURSO0FBQ0EsRUF6QkQ7O0FBMkJBO0FBQ0EsUUFBTzlELE1BQVA7QUFDQSxDQTdQRiIsImZpbGUiOiJ3aWRnZXRzL2NhcnRfZHJvcGRvd24uanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhcnRfZHJvcGRvd24uanMgMjAxNi0wNy0yMFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogRW5hYmxlcyB0aGUgZnVuY3Rpb25hbGl0eSBvZiB0aGUgY2FydCBkcm9wZG93biwgdG8gb3BlblxuICogdmlhIGFuIGV2ZW50LiBUaGlzIGlzIG5lZWRlZCB0byBvcGVuIHRoZSBmbHlvdXQgYWZ0ZXJcbiAqIGFuIGl0ZW0gaXMgYWRkZWQgdG8gdGhlIGNhcnRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnY2FydF9kcm9wZG93bicsXG5cblx0W1xuXHRcdCd4aHInLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JHdpbmRvdyA9ICQod2luZG93KSxcblx0XHRcdCRib2R5ID0gJCgnYm9keScpLFxuXHRcdFx0JGl0ZW0gPSBudWxsLFxuXHRcdFx0JHRhcmdldCA9IG51bGwsXG5cdFx0XHRpc0NhcnREcm9wZG93blN0aWNreSA9IGZhbHNlLFxuXHRcdFx0dGltZXIgPSBudWxsLFxuXHRcdFx0YW5pbWF0ZVRpbWVyID0gbnVsbCxcblx0XHRcdGFqYXggPSBudWxsLFxuXHRcdFx0YWpheERhdGEgPSB7XG5cdFx0XHRcdHBhcnQxOiAnaGVhZGVyJyxcblx0XHRcdFx0cGFydDI6ICdkcm9wZG93bidcblx0XHRcdH0sXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0Ly8gRGVmYXVsdCBkZWxheSAoaW4gbXMpIGFmdGVyIHdoaWNoIHRoZSBmbHlvdXQgY2xvc2VzXG5cdFx0XHRcdGRlbGF5OiA1MDAwLFxuXHRcdFx0XHQvLyBVcGRhdGUgcmVxdWVzdCB1cmxcblx0XHRcdFx0dXJsOiAnc2hvcC5waHA/ZG89Q2FydERyb3Bkb3duJyxcblx0XHRcdFx0Ly8gU2VsZWN0aW9uIG9mIHRoZSBjb250YWluZXIgdGhlIHJlc3VsdCBnZXRzIGZpbGxlZCBpblxuXHRcdFx0XHRmaWxsVGFyZ2V0OiAnaGVhZGVyJyxcblx0XHRcdFx0Ly8gRHVyYXRpb24gdGhhdCB0aGUgY291bnQgYmFkZ2UgZ2V0cyByZXNpemVkIGFmdGVyIGFkZGluZyBhbiBpdGVtIHRvIHRoZSBiYXNrZXRcblx0XHRcdFx0Y291bnRBbmltYXRpb246IDIwMDAsXG5cdFx0XHRcdC8vIEFKQVggcmVzcG9uc2UgY29udGVudCBzZWxlY3RvcnNcblx0XHRcdFx0c2VsZWN0b3JNYXBwaW5nOiB7XG5cdFx0XHRcdFx0Y2FydERyb3Bkb3duOiAnLmNhcnQtZHJvcGRvd24nLFxuXHRcdFx0XHRcdGNhcnREcm9wZG93blByb2R1Y3RzOiAnLnByb2R1Y3RzJyxcblx0XHRcdFx0XHRjYXJ0RHJvcGRvd25Qcm9kdWN0c0NvdW50OiAnLmNhcnQtcHJvZHVjdHMtY291bnQnXG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBzY3JvbGwgdGhlIGxpc3Rcblx0XHQgKiBkb3duIHRvIHRoZSBlbmRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2Nyb2xsRG93biA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRsaXN0ID0gJHRoaXMuZmluZCgnLnByb2R1Y3RzLWxpc3QnKSxcblx0XHRcdFx0aGVpZ2h0ID0gJGxpc3Qub3V0ZXJIZWlnaHQoKSAqIDI7ICAgIC8vIE11bHRpcGx5IHdpdGggMiB0byBiZSBzdXJlIHRoYXQgaXQgZ2V0cyBzY3JvbGxlZCB0byB0aGUgYm90dG9tXG5cblx0XHRcdCRsaXN0LmFuaW1hdGUoeydzY3JvbGxUb3AnOiBoZWlnaHQgKyAncHgnfSwgMCk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFRyaWdnZXJzIHRoZSBtb3VzZWVudGVyIGV2ZW50XG5cdFx0ICogb24gdGhlIGNhcnRkcm9wZG93biBsaW5rXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfb3BlbiA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdGlmICgkKGRlZmF1bHRzLnNlbGVjdG9yTWFwcGluZy5jYXJ0RHJvcGRvd25Qcm9kdWN0c0NvdW50KS50ZXh0KCkgIT09ICcwJykge1xuICAgICAgICAgICAgICAgICQoZGVmYXVsdHMuc2VsZWN0b3JNYXBwaW5nLmNhcnREcm9wZG93blByb2R1Y3RzQ291bnQpLnJlbW92ZUNsYXNzKCdoaWRkZW4nKTtcblx0XHRcdH1cblxuXHRcdFx0JGl0ZW0udHJpZ2dlcignbW91c2VlbnRlcicsIHtwcm9nOiB0cnVlfSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFRyaWdnZXJzIHRoZSBtb3VzZWxlYXZlIGV2ZW50XG5cdFx0ICogb24gdGhlIGNhcnRkcm9wZG93biBsaW5rXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2xvc2UgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0JGl0ZW0udHJpZ2dlcignbW91c2VsZWF2ZScsIHtwcm9nOiB0cnVlfSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHJlc2l6ZXMgdGhlIGNvdW50IGJhZGdlXG5cdFx0ICogYWZ0ZXIgdGhlIGFkZCBvZiBhbiBpdGVtIHRvIHRoZSBiYXNrZXQgZm9yXG5cdFx0ICogYSBzcGVjaWZpYyBkdXJhdGlvblxuXHRcdCAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICBzZWxlY3RvciAgICAgICAgVGV4dCB2YWx1ZSBvZiB0aGUgb2xkIGJhZGdlICh0aGUgY291bnQpXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGNvbmZpZyAgICAgICAgICBUaGUgY29uZmlnIGZvciB0aGUgYmFkZ2VzIGZyb20gdGhlIGFqYXggcmVzdWx0IGNvbnRlbnRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcmVzaXplQ291bnRCYWRnZSA9IGZ1bmN0aW9uKGN1cnJlbnRDb3VudCwgY29uZmlnKSB7XG5cdFx0XHRpZiAob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbY29uZmlnLnNlbGVjdG9yXSA9PT0gdW5kZWZpbmVkKSB7XG5cdFx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBzZWxlY3RvciBtYXBwaW5nIFwiJyArIGNvbmZpZy5zZWxlY3RvciArICdcIiBkb2VzblxcJ3QgZXhpc3QuJyk7XG5cdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHR2YXIgY291bnQgPSAkKGNvbmZpZy52YWx1ZSkudGV4dCgpLFxuXHRcdFx0XHQkY291bnRzID0gJHRhcmdldC5maW5kKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nW2NvbmZpZy5zZWxlY3Rvcl0pO1xuXG5cdFx0XHRpZiAoY3VycmVudENvdW50ICE9PSBjb3VudCkge1xuXHRcdFx0XHRpZiAoYW5pbWF0ZVRpbWVyKSB7XG5cdFx0XHRcdFx0Y2xlYXJUaW1lb3V0KGFuaW1hdGVUaW1lcik7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHQkY291bnRzLmFkZENsYXNzKCdiaWcnKTtcblx0XHRcdFx0YW5pbWF0ZVRpbWVyID0gc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkY291bnRzLnJlbW92ZUNsYXNzKCdiaWcnKTtcblx0XHRcdFx0fSwgb3B0aW9ucy5jb3VudEFuaW1hdGlvbik7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFVwZGF0ZXMgdGhlIGRyb3Bkb3duIHdpdGggZGF0YSBmcm9tXG5cdFx0ICogdGhlIHNlcnZlciBhbmQgb3BlbnMgdGhlIGxheWVyIGZvciBhXG5cdFx0ICogY2VydGFpbiB0aW1lXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7Ym9vbGVhbn0gICAgICAgb3BlbkRyb3Bkb3duICAgIERlZmluZXMgaWYgdGhlIGRyb3Bkb3duIHNoYWxsIGJlIG9wZW5lZCBhZnRlciB1cGRhdGVcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdXBkYXRlID0gZnVuY3Rpb24oZSwgb3BlbkRyb3Bkb3duKSB7XG5cdFx0XHRpZiAoYWpheCkge1xuXHRcdFx0XHRhamF4LmFib3J0KCk7XG5cdFx0XHR9XG5cblx0XHRcdGFqYXggPSBqc2UubGlicy54aHIuYWpheCh7dXJsOiBvcHRpb25zLnVybCwgZGF0YTogYWpheERhdGF9KS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRpZiAob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbcmVzdWx0LmNvbnRlbnQuY291bnQuc2VsZWN0b3JdID09PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy53YXJuKCdUaGUgc2VsZWN0b3IgbWFwcGluZyBcIicgKyByZXN1bHQuY29udGVudC5jb3VudC5zZWxlY3RvciArICdcIiBkb2VzblxcJ3QgZXhpc3QuJyk7XG5cdFx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdHZhciBjb3VudCA9ICQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbcmVzdWx0LmNvbnRlbnQuY291bnQuc2VsZWN0b3JdKS5maXJzdCgpLnRleHQoKTtcblx0XHRcdFx0anNlLmxpYnMudGVtcGxhdGUuaGVscGVycy5maWxsKHJlc3VsdC5jb250ZW50LCAkdGFyZ2V0LCBvcHRpb25zLnNlbGVjdG9yTWFwcGluZyk7XG5cdFx0XHRcdF9yZXNpemVDb3VudEJhZGdlKGNvdW50LCByZXN1bHQuY29udGVudC5jb3VudCk7XG5cblx0XHRcdFx0X3Njcm9sbERvd24oKTtcblxuXHRcdFx0XHRpZiAob3BlbkRyb3Bkb3duKSB7XG5cdFx0XHRcdFx0JHRoaXMudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQ0FSVF9PUEVOKCksIFtdKTtcblx0XHRcdFx0XHR0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHQkdGhpcy50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5DQVJUX0NMT1NFKCksIFtdKTtcblx0XHRcdFx0XHR9LCBvcHRpb25zLmRlbGF5KTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgdGhhdCBsaXN0ZW5zIG9uIHRoZVxuXHRcdCAqIG1vdXNlZW50ZXIgLyBsZWF2ZSBldmVudHMuIElmIHRoZXNlXG5cdFx0ICogZXZlbnRzIGFyZSBub3QgdHJpZ2dlcmVkIGJ5IHRoaXMgc2NyaXB0XG5cdFx0ICogc3RvcCB0aGUgdGltZXIsIGJlY2F1c2UgdGhlIHVzZXIgaGFzXG5cdFx0ICogbW92ZWQgdGhlIG1vdXNlIGN1cnNvciBvdmVyIHRoZSBvYmplY3Rcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZCAgICAgICBKU09OIHdoaWNoIGNvbnRhaW5zIHRoZSBzdGF0dXMgaWYgdGhlIHByb2dyYW0gdHJpZ2dlcmVkIHRoZSBldmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9wcmV2ZW50RXhlYyA9IGZ1bmN0aW9uKGUsIGQpIHtcblx0XHRcdGlmICgoIWQgfHwgIWQucHJvZykgJiYgdGltZXIpIHtcblx0XHRcdFx0Y2xlYXJUaW1lb3V0KHRpbWVyKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFN0aWNreSBDYXJ0IERyb3Bkb3duIFxuXHRcdCAqIFxuXHRcdCAqIFRoZXJlIGFyZSBjYXNlcyB3aGVuIHRoZSB1c2VyIGFkZHMgc29tZXRoaW5nIHRvIHRoZSBjYXJ0IGFuZCB0aGlzIHBvcHMgb3V0IGJ1dCBpdCBjYW5ub3QgYmUgc2VlbiBjYXVzZVxuXHRcdCAqIGl0IGlzIG91dCBvZiB0aGUgdmlld3BvcnQgKGUuZy4gdXNlciBoYXMgc2Nyb2xsZWQgdG8gYm90dG9tKS4gVGhpcyBtZXRob2Qgd2lsbCBtYWtlIHN1cmUgdGhhdCB0aGUgY2FydFxuXHRcdCAqIGRyb3Bkb3duIGlzIGFsd2F5cyB2aXNpYmxlIGJ5IGFwcGx5aW5nIGEgXCJzdGlja3lcIiBwb3NpdGlvbmluZyB0byByZXNwZWN0aXZlIGVsZW1lbnRzLlxuXHRcdCAqIFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zdGlja3lDYXJ0RHJvcGRvd24gPSBmdW5jdGlvbigpIHtcblx0XHRcdC8vIElmIHRoZSBjYXJ0IGRyb3Bkb3duIGlzIG5vdCB2aXNpYmxlIHdhaXQgdW50aWwgdGhlIHRyYW5zaXRpb24gY29tcGxldGVzIChzZWUgbWVudS5qcykuIFxuXHRcdFx0aWYgKCEkaXRlbS5oYXNDbGFzcygnb3BlbicpKSB7XG5cdFx0XHRcdHZhciBpbnRlcnZhbCA9IHNldEludGVydmFsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGlmICgkaXRlbS5oYXNDbGFzcygnb3BlbicpKSB7XG5cdFx0XHRcdFx0XHRfc3RpY2t5Q2FydERyb3Bkb3duKCk7XG5cdFx0XHRcdFx0XHRjbGVhckludGVydmFsKGludGVydmFsKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0sIDEwMCk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpc0NhcnREcm9wZG93blN0aWNreSA9IGZhbHNlO1xuXHRcdFx0XHRyZXR1cm47IFxuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHR2YXIgJGNhcnREcm9wZG93biA9ICQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcuY2FydERyb3Bkb3duKTsgXG5cdFx0XHR2YXIgY2FydERyb3Bkb3duT2Zmc2V0ID0gJGNhcnREcm9wZG93bi5vZmZzZXQoKTtcblx0XHRcdFxuXHRcdFx0Ly8gRW5hYmxlIFwic3RpY2t5XCIgcG9zaXRpb24gaW4gb3JkZXIgdG8gbWFrZSB0aGUgY2FydCBkcm9wZG93biB2aXNpYmxlIHRvIHRoZSB1c2VyLlxuXHRcdFx0aWYgKCFpc0NhcnREcm9wZG93blN0aWNreSAmJiBjYXJ0RHJvcGRvd25PZmZzZXQudG9wIDwgJCh3aW5kb3cpLnNjcm9sbFRvcCgpKSB7XG5cdFx0XHRcdCRjYXJ0RHJvcGRvd24uY3NzKHtcblx0XHRcdFx0XHRwb3NpdGlvbjogJ2ZpeGVkJyxcblx0XHRcdFx0XHR0b3A6IDIwLFxuXHRcdFx0XHRcdGxlZnQ6IGNhcnREcm9wZG93bk9mZnNldC5sZWZ0XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0aXNDYXJ0RHJvcGRvd25TdGlja3kgPSB0cnVlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBSZXNldCBzdGlja3kgcG9zaXRpb24gb25jZSB0aGUgdXNlciBoYXMgc2Nyb2xsZWQgdG8gdG9wLiBcblx0XHRcdGlmIChpc0NhcnREcm9wZG93blN0aWNreSAmJiBjYXJ0RHJvcGRvd25PZmZzZXQudG9wIDwgJGl0ZW0ub2Zmc2V0KCkudG9wKSB7XG5cdFx0XHRcdCRjYXJ0RHJvcGRvd24uY3NzKHtcblx0XHRcdFx0XHRwb3NpdGlvbjogJycsXG5cdFx0XHRcdFx0dG9wOiAnJyxcblx0XHRcdFx0XHRsZWZ0OiAnJ1xuXHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdGlzQ2FydERyb3Bkb3duU3RpY2t5ID0gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqXG5cdFx0ICogQGNvbnN0cnVjdG9yXG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cblx0XHRcdCRpdGVtID0gJHRoaXMuZmluZCgnPiB1bCA+IGxpJyk7XG5cdFx0XHQkdGFyZ2V0ID0gb3B0aW9ucy5maWxsVGFyZ2V0ID8gJChvcHRpb25zLmZpbGxUYXJnZXQpIDogJHRoaXM7XG5cblx0XHRcdCR3aW5kb3dcblx0XHRcdFx0Lm9uKCdmb2N1cycsIF91cGRhdGUpXG5cdFx0XHRcdC5vbignc2Nyb2xsJywgX3N0aWNreUNhcnREcm9wZG93bik7IFxuXG5cdFx0XHQkYm9keVxuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLkNBUlRfT1BFTigpLCBfb3Blbilcblx0XHRcdFx0Lm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5DQVJUX0NMT1NFKCksIF9jbG9zZSlcblx0XHRcdFx0Lm9uKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5DQVJUX1VQREFURSgpLCBfdXBkYXRlKTtcblxuXHRcdFx0JGl0ZW1cblx0XHRcdFx0Lm9uKCdtb3VzZWVudGVyIG1vdXNlbGVhdmUnLCBfcHJldmVudEV4ZWMpXG5cdFx0XHRcdC5vbignbW91c2VlbnRlcicsIF9zdGlja3lDYXJ0RHJvcGRvd24pO1xuXHRcdFx0XG5cdFx0XHRfc2Nyb2xsRG93bigpO1xuXHRcdFx0XG5cdFx0XHRpZiAobG9jYXRpb24uc2VhcmNoLnNlYXJjaCgnb3Blbl9jYXJ0X2Ryb3Bkb3duPTEnKSAhPT0gLTEpIHtcblx0XHRcdFx0JGJvZHkudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQ0FSVF9PUEVOKCkpO1xuXHRcdFx0fVxuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
