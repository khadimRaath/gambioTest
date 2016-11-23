'use strict';

/* --------------------------------------------------------------
 mobile_menu.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that performs the actions for the topbar menu
 * buttons in mobile view. It opens / closes the menu items
 * after a click on a button was performed (or in special
 * cases opens a link).
 */
gambio.widgets.module('mobile_menu', [gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    $buttons = null,
	    mobile = null,
	    scrollTop,
	    // scroll top backup
	scrollLeft,
	    // scroll top backup
	defaults = {
		breakpoint: 40, // Minimum breakpoint to switch to mobile view
		buttonActiveClass: 'active', // Class that is set to the active button
		addClass: 'in' // Class to add to the menu contents if opened
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Function that sets and removes the classes
  * to the corresponding menu contents. If a data
  * object is given, open the corresponding menu while
  * closing all others. If no data is given, close all
  * menus.
  * @param       {object}    buttonData    [OPTIONAL] data object of the pressed button
  * @private
  */
	var _setClasses = function _setClasses(buttonData) {
		var found = false;

		$buttons.each(function () {
			var $self = $(this),
			    d = $(this).parseModuleData('mobile_menu');

			if (!buttonData || d && d.target !== buttonData.target) {
				// The target of the button isn't the one delivered by "buttonData"
				$(d.target).removeClass(options.addClass);
				$self.removeClass(options.buttonActiveClass);
				$body.removeClass(d.bodyClass);
			} else if (d && !found) {
				// The target is the same as the one delivered by buttonData
				// AND it wasn't opened / closed in this loop before
				var $target = $(d.target);
				$target.toggleClass(options.addClass);

				// Add or remove classes to the body and the buttons
				// depending on the state. The if / else case is used
				// to be more fail safe than a toggle
				if ($target.hasClass(options.addClass)) {
					$body.addClass(d.bodyClass);
					$self.addClass(options.buttonActiveClass);
					if ($self.data('mobilemenuToggleContentVisibility') !== undefined) {
						_toggleContentVisibility(false);
					}
				} else {
					$body.removeClass(d.bodyClass);
					$self.removeClass(options.buttonActiveClass);
					if ($self.data('mobilemenuToggleContentVisibility') !== undefined) {
						_toggleContentVisibility(true);
					}
				}

				// Set a flag that the target has been processed
				found = true;
			}
		});
	};

	/**
  * Toggle Content Visibility
  *
  * In some occasions some container elements cover the complete mobile screen but due to
  * buggy behavior the scrolling of the page is still available. Use this method to hide the
  * page content and solve the scrolling problem.
  *
  * @param {bool} state Sets whether the content is visible or not.
  *
  * @private
  */
	var _toggleContentVisibility = function _toggleContentVisibility(state) {
		var $content = $('#wrapper, #footer'),
		    $document = $(document);

		if (state) {
			$content.show();
			$document.scrollTop(scrollTop);
			$document.scrollLeft(scrollLeft);
			scrollTop = scrollLeft = null; // reset
		} else {
			if (!scrollTop) {
				scrollTop = $document.scrollTop(); // backup
			}
			if (!scrollLeft) {
				scrollLeft = $document.scrollLeft(); // backup
			}
			$content.hide();
		}
	};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler for the click event on the
  * buttons. In case the button is a menu button
  * the corresponding menu entry gets shown, while
  * all other menus getting closed
  * @private
  */
	var _clickHandler = function _clickHandler() {
		var $self = $(this),
		    buttonData = $self.parseModuleData('mobile_menu');

		if (buttonData.target) {
			// Set the classes for the open / close state of the menu
			_setClasses(buttonData);
		} else if (buttonData.location) {
			// Open a link
			location.href = buttonData.location;
		}
	};

	/**
  * Event handler that listens on the
  * "breakpoint" event. On every breakpoint
  * the function checks if there is a switch
  * from desktop to mobile. In case that
  * happens, all opened menus getting closed
  * @param       {object}    e jQuery event object
  * @param       {object}    d Data object that contains the information belonging to the current breakpoint
  * @private
  */
	var _breakpointHandler = function _breakpointHandler(e, d) {
		if (d.id > options.breakpoint && mobile) {
			// Close all menus on switch to desktop view
			_setClasses(null);
			$('#wrapper, #footer').show();
			mobile = false;
		} else if (d.id <= options.breakpoint && !mobile) {
			// Close all menus on switch to mobile view
			_setClasses(null);
			mobile = true;
		}
	};

	/**
  * Navbar Topbar Item Click
  *
  * This handler must close the other opened frames because only one item should be visible.
  *
  * @private
  */
	var _clickTopBarItemHandler = function _clickTopBarItemHandler() {
		if ($(this).parent().hasClass('open')) {
			return;
		}
		$('.navbar-categories').find('.navbar-topbar-item.open').removeClass('open');
		$('#categories .navbar-collapse:first').animate({
			scrollTop: $(this).parent().position().top + $(this).parent().height() - $('#header .navbar-header').height()
		}, 500);
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		mobile = jse.libs.template.responsive.breakpoint().id <= options.breakpoint;
		$buttons = $this.find('button');

		$body.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);
		$('.navbar-categories').on('mouseup', '.navbar-topbar-item > a', _clickTopBarItemHandler);
		$this.on('click', 'button', _clickHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbW9iaWxlX21lbnUuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsIiRidXR0b25zIiwibW9iaWxlIiwic2Nyb2xsVG9wIiwic2Nyb2xsTGVmdCIsImRlZmF1bHRzIiwiYnJlYWtwb2ludCIsImJ1dHRvbkFjdGl2ZUNsYXNzIiwiYWRkQ2xhc3MiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3NldENsYXNzZXMiLCJidXR0b25EYXRhIiwiZm91bmQiLCJlYWNoIiwiJHNlbGYiLCJkIiwicGFyc2VNb2R1bGVEYXRhIiwidGFyZ2V0IiwicmVtb3ZlQ2xhc3MiLCJib2R5Q2xhc3MiLCIkdGFyZ2V0IiwidG9nZ2xlQ2xhc3MiLCJoYXNDbGFzcyIsInVuZGVmaW5lZCIsIl90b2dnbGVDb250ZW50VmlzaWJpbGl0eSIsInN0YXRlIiwiJGNvbnRlbnQiLCIkZG9jdW1lbnQiLCJkb2N1bWVudCIsInNob3ciLCJoaWRlIiwiX2NsaWNrSGFuZGxlciIsImxvY2F0aW9uIiwiaHJlZiIsIl9icmVha3BvaW50SGFuZGxlciIsImUiLCJpZCIsIl9jbGlja1RvcEJhckl0ZW1IYW5kbGVyIiwicGFyZW50IiwiZmluZCIsImFuaW1hdGUiLCJwb3NpdGlvbiIsInRvcCIsImhlaWdodCIsImluaXQiLCJkb25lIiwianNlIiwibGlicyIsInRlbXBsYXRlIiwicmVzcG9uc2l2ZSIsIm9uIiwiZXZlbnRzIiwiQlJFQUtQT0lOVCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7QUFNQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0MsYUFERCxFQUdDLENBQ0NGLE9BQU9HLE1BQVAsR0FBZ0IsY0FEakIsRUFFQ0gsT0FBT0csTUFBUCxHQUFnQixrQkFGakIsQ0FIRCxFQVFDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFRjs7QUFFRSxLQUFJQyxRQUFRQyxFQUFF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QUE1QixHQUFrQzVDLEVBQUUsSUFBRixFQUMzQ3dDLE1BRDJDLEdBRTNDSyxNQUYyQyxFQUFsQyxHQUVFN0MsRUFBRSx3QkFBRixFQUE0QjZDLE1BQTVCO0FBSGQsR0FBaEQsRUFJbUQsR0FKbkQ7QUFLQSxFQVZEOztBQWFGOztBQUVFOzs7O0FBSUFqRCxRQUFPa0QsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUI1QyxXQUFTNkMsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxVQUFsQixDQUE2QjVDLFVBQTdCLEdBQTBDK0IsRUFBMUMsSUFBZ0Q1QixRQUFRSCxVQUFqRTtBQUNBTCxhQUFXSCxNQUFNMEMsSUFBTixDQUFXLFFBQVgsQ0FBWDs7QUFFQXhDLFFBQU1tRCxFQUFOLENBQVNKLElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQkcsTUFBbEIsQ0FBeUJDLFVBQXpCLEVBQVQsRUFBZ0RsQixrQkFBaEQ7QUFDQXBDLElBQUUsb0JBQUYsRUFBd0JvRCxFQUF4QixDQUEyQixTQUEzQixFQUFzQyx5QkFBdEMsRUFBaUViLHVCQUFqRTtBQUNBeEMsUUFBTXFELEVBQU4sQ0FBUyxPQUFULEVBQWtCLFFBQWxCLEVBQTRCbkIsYUFBNUI7O0FBRUFjO0FBQ0EsRUFWRDs7QUFZQTtBQUNBLFFBQU9uRCxNQUFQO0FBQ0EsQ0F2TUYiLCJmaWxlIjoid2lkZ2V0cy9tb2JpbGVfbWVudS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbW9iaWxlX21lbnUuanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogV2lkZ2V0IHRoYXQgcGVyZm9ybXMgdGhlIGFjdGlvbnMgZm9yIHRoZSB0b3BiYXIgbWVudVxuICogYnV0dG9ucyBpbiBtb2JpbGUgdmlldy4gSXQgb3BlbnMgLyBjbG9zZXMgdGhlIG1lbnUgaXRlbXNcbiAqIGFmdGVyIGEgY2xpY2sgb24gYSBidXR0b24gd2FzIHBlcmZvcm1lZCAob3IgaW4gc3BlY2lhbFxuICogY2FzZXMgb3BlbnMgYSBsaW5rKS5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnbW9iaWxlX21lbnUnLFxuXG5cdFtcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL2V2ZW50cycsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9yZXNwb25zaXZlJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHQkYnV0dG9ucyA9IG51bGwsXG5cdFx0XHRtb2JpbGUgPSBudWxsLFxuXHRcdFx0c2Nyb2xsVG9wLCAvLyBzY3JvbGwgdG9wIGJhY2t1cFxuXHRcdFx0c2Nyb2xsTGVmdCwgLy8gc2Nyb2xsIHRvcCBiYWNrdXBcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHRicmVha3BvaW50OiA0MCwgLy8gTWluaW11bSBicmVha3BvaW50IHRvIHN3aXRjaCB0byBtb2JpbGUgdmlld1xuXHRcdFx0XHRidXR0b25BY3RpdmVDbGFzczogJ2FjdGl2ZScsIC8vIENsYXNzIHRoYXQgaXMgc2V0IHRvIHRoZSBhY3RpdmUgYnV0dG9uXG5cdFx0XHRcdGFkZENsYXNzOiAnaW4nIC8vIENsYXNzIHRvIGFkZCB0byB0aGUgbWVudSBjb250ZW50cyBpZiBvcGVuZWRcblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBGdW5jdGlvbiB0aGF0IHNldHMgYW5kIHJlbW92ZXMgdGhlIGNsYXNzZXNcblx0XHQgKiB0byB0aGUgY29ycmVzcG9uZGluZyBtZW51IGNvbnRlbnRzLiBJZiBhIGRhdGFcblx0XHQgKiBvYmplY3QgaXMgZ2l2ZW4sIG9wZW4gdGhlIGNvcnJlc3BvbmRpbmcgbWVudSB3aGlsZVxuXHRcdCAqIGNsb3NpbmcgYWxsIG90aGVycy4gSWYgbm8gZGF0YSBpcyBnaXZlbiwgY2xvc2UgYWxsXG5cdFx0ICogbWVudXMuXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGJ1dHRvbkRhdGEgICAgW09QVElPTkFMXSBkYXRhIG9iamVjdCBvZiB0aGUgcHJlc3NlZCBidXR0b25cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0Q2xhc3NlcyA9IGZ1bmN0aW9uKGJ1dHRvbkRhdGEpIHtcblx0XHRcdHZhciBmb3VuZCA9IGZhbHNlO1xuXG5cdFx0XHQkYnV0dG9ucy5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdGQgPSAkKHRoaXMpLnBhcnNlTW9kdWxlRGF0YSgnbW9iaWxlX21lbnUnKTtcblxuXHRcdFx0XHRpZiAoIWJ1dHRvbkRhdGEgfHwgKGQgJiYgZC50YXJnZXQgIT09IGJ1dHRvbkRhdGEudGFyZ2V0KSkge1xuXHRcdFx0XHRcdC8vIFRoZSB0YXJnZXQgb2YgdGhlIGJ1dHRvbiBpc24ndCB0aGUgb25lIGRlbGl2ZXJlZCBieSBcImJ1dHRvbkRhdGFcIlxuXHRcdFx0XHRcdCQoZC50YXJnZXQpLnJlbW92ZUNsYXNzKG9wdGlvbnMuYWRkQ2xhc3MpO1xuXHRcdFx0XHRcdCRzZWxmLnJlbW92ZUNsYXNzKG9wdGlvbnMuYnV0dG9uQWN0aXZlQ2xhc3MpO1xuXHRcdFx0XHRcdCRib2R5LnJlbW92ZUNsYXNzKGQuYm9keUNsYXNzKTtcblx0XHRcdFx0fSBlbHNlIGlmIChkICYmICFmb3VuZCkge1xuXHRcdFx0XHRcdC8vIFRoZSB0YXJnZXQgaXMgdGhlIHNhbWUgYXMgdGhlIG9uZSBkZWxpdmVyZWQgYnkgYnV0dG9uRGF0YVxuXHRcdFx0XHRcdC8vIEFORCBpdCB3YXNuJ3Qgb3BlbmVkIC8gY2xvc2VkIGluIHRoaXMgbG9vcCBiZWZvcmVcblx0XHRcdFx0XHR2YXIgJHRhcmdldCA9ICQoZC50YXJnZXQpO1xuXHRcdFx0XHRcdCR0YXJnZXQudG9nZ2xlQ2xhc3Mob3B0aW9ucy5hZGRDbGFzcyk7XG5cblx0XHRcdFx0XHQvLyBBZGQgb3IgcmVtb3ZlIGNsYXNzZXMgdG8gdGhlIGJvZHkgYW5kIHRoZSBidXR0b25zXG5cdFx0XHRcdFx0Ly8gZGVwZW5kaW5nIG9uIHRoZSBzdGF0ZS4gVGhlIGlmIC8gZWxzZSBjYXNlIGlzIHVzZWRcblx0XHRcdFx0XHQvLyB0byBiZSBtb3JlIGZhaWwgc2FmZSB0aGFuIGEgdG9nZ2xlXG5cdFx0XHRcdFx0aWYgKCR0YXJnZXQuaGFzQ2xhc3Mob3B0aW9ucy5hZGRDbGFzcykpIHtcblx0XHRcdFx0XHRcdCRib2R5LmFkZENsYXNzKGQuYm9keUNsYXNzKTtcblx0XHRcdFx0XHRcdCRzZWxmLmFkZENsYXNzKG9wdGlvbnMuYnV0dG9uQWN0aXZlQ2xhc3MpO1xuXHRcdFx0XHRcdFx0aWYgKCRzZWxmLmRhdGEoJ21vYmlsZW1lbnVUb2dnbGVDb250ZW50VmlzaWJpbGl0eScpICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0XHRcdFx0X3RvZ2dsZUNvbnRlbnRWaXNpYmlsaXR5KGZhbHNlKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0JGJvZHkucmVtb3ZlQ2xhc3MoZC5ib2R5Q2xhc3MpO1xuXHRcdFx0XHRcdFx0JHNlbGYucmVtb3ZlQ2xhc3Mob3B0aW9ucy5idXR0b25BY3RpdmVDbGFzcyk7XG5cdFx0XHRcdFx0XHRpZiAoJHNlbGYuZGF0YSgnbW9iaWxlbWVudVRvZ2dsZUNvbnRlbnRWaXNpYmlsaXR5JykgIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRcdFx0XHRfdG9nZ2xlQ29udGVudFZpc2liaWxpdHkodHJ1ZSk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0Ly8gU2V0IGEgZmxhZyB0aGF0IHRoZSB0YXJnZXQgaGFzIGJlZW4gcHJvY2Vzc2VkXG5cdFx0XHRcdFx0Zm91bmQgPSB0cnVlO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBUb2dnbGUgQ29udGVudCBWaXNpYmlsaXR5XG5cdFx0ICpcblx0XHQgKiBJbiBzb21lIG9jY2FzaW9ucyBzb21lIGNvbnRhaW5lciBlbGVtZW50cyBjb3ZlciB0aGUgY29tcGxldGUgbW9iaWxlIHNjcmVlbiBidXQgZHVlIHRvXG5cdFx0ICogYnVnZ3kgYmVoYXZpb3IgdGhlIHNjcm9sbGluZyBvZiB0aGUgcGFnZSBpcyBzdGlsbCBhdmFpbGFibGUuIFVzZSB0aGlzIG1ldGhvZCB0byBoaWRlIHRoZVxuXHRcdCAqIHBhZ2UgY29udGVudCBhbmQgc29sdmUgdGhlIHNjcm9sbGluZyBwcm9ibGVtLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtib29sfSBzdGF0ZSBTZXRzIHdoZXRoZXIgdGhlIGNvbnRlbnQgaXMgdmlzaWJsZSBvciBub3QuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdG9nZ2xlQ29udGVudFZpc2liaWxpdHkgPSBmdW5jdGlvbihzdGF0ZSkge1xuXHRcdFx0dmFyICRjb250ZW50ID0gJCgnI3dyYXBwZXIsICNmb290ZXInKSxcblx0XHRcdFx0JGRvY3VtZW50ID0gJChkb2N1bWVudCk7XG5cblx0XHRcdGlmIChzdGF0ZSkge1xuXHRcdFx0XHQkY29udGVudC5zaG93KCk7XG5cdFx0XHRcdCRkb2N1bWVudC5zY3JvbGxUb3Aoc2Nyb2xsVG9wKTtcblx0XHRcdFx0JGRvY3VtZW50LnNjcm9sbExlZnQoc2Nyb2xsTGVmdCk7XG5cdFx0XHRcdHNjcm9sbFRvcCA9IHNjcm9sbExlZnQgPSBudWxsOyAvLyByZXNldFxuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0aWYgKCFzY3JvbGxUb3ApIHtcblx0XHRcdFx0XHRzY3JvbGxUb3AgPSAkZG9jdW1lbnQuc2Nyb2xsVG9wKCk7IC8vIGJhY2t1cFxuXHRcdFx0XHR9XG5cdFx0XHRcdGlmICghc2Nyb2xsTGVmdCkge1xuXHRcdFx0XHRcdHNjcm9sbExlZnQgPSAkZG9jdW1lbnQuc2Nyb2xsTGVmdCgpOyAvLyBiYWNrdXBcblx0XHRcdFx0fVxuXHRcdFx0XHQkY29udGVudC5oaWRlKCk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIGNsaWNrIGV2ZW50IG9uIHRoZVxuXHRcdCAqIGJ1dHRvbnMuIEluIGNhc2UgdGhlIGJ1dHRvbiBpcyBhIG1lbnUgYnV0dG9uXG5cdFx0ICogdGhlIGNvcnJlc3BvbmRpbmcgbWVudSBlbnRyeSBnZXRzIHNob3duLCB3aGlsZVxuXHRcdCAqIGFsbCBvdGhlciBtZW51cyBnZXR0aW5nIGNsb3NlZFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jbGlja0hhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdGJ1dHRvbkRhdGEgPSAkc2VsZi5wYXJzZU1vZHVsZURhdGEoJ21vYmlsZV9tZW51Jyk7XG5cblx0XHRcdGlmIChidXR0b25EYXRhLnRhcmdldCkge1xuXHRcdFx0XHQvLyBTZXQgdGhlIGNsYXNzZXMgZm9yIHRoZSBvcGVuIC8gY2xvc2Ugc3RhdGUgb2YgdGhlIG1lbnVcblx0XHRcdFx0X3NldENsYXNzZXMoYnV0dG9uRGF0YSk7XG5cdFx0XHR9IGVsc2UgaWYgKGJ1dHRvbkRhdGEubG9jYXRpb24pIHtcblx0XHRcdFx0Ly8gT3BlbiBhIGxpbmtcblx0XHRcdFx0bG9jYXRpb24uaHJlZiA9IGJ1dHRvbkRhdGEubG9jYXRpb247XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgdGhhdCBsaXN0ZW5zIG9uIHRoZVxuXHRcdCAqIFwiYnJlYWtwb2ludFwiIGV2ZW50LiBPbiBldmVyeSBicmVha3BvaW50XG5cdFx0ICogdGhlIGZ1bmN0aW9uIGNoZWNrcyBpZiB0aGVyZSBpcyBhIHN3aXRjaFxuXHRcdCAqIGZyb20gZGVza3RvcCB0byBtb2JpbGUuIEluIGNhc2UgdGhhdFxuXHRcdCAqIGhhcHBlbnMsIGFsbCBvcGVuZWQgbWVudXMgZ2V0dGluZyBjbG9zZWRcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGQgRGF0YSBvYmplY3QgdGhhdCBjb250YWlucyB0aGUgaW5mb3JtYXRpb24gYmVsb25naW5nIHRvIHRoZSBjdXJyZW50IGJyZWFrcG9pbnRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYnJlYWtwb2ludEhhbmRsZXIgPSBmdW5jdGlvbihlLCBkKSB7XG5cdFx0XHRpZiAoZC5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCAmJiBtb2JpbGUpIHtcblx0XHRcdFx0Ly8gQ2xvc2UgYWxsIG1lbnVzIG9uIHN3aXRjaCB0byBkZXNrdG9wIHZpZXdcblx0XHRcdFx0X3NldENsYXNzZXMobnVsbCk7XG5cdFx0XHRcdCQoJyN3cmFwcGVyLCAjZm9vdGVyJykuc2hvdygpO1xuXHRcdFx0XHRtb2JpbGUgPSBmYWxzZTtcblx0XHRcdH0gZWxzZSBpZiAoZC5pZCA8PSBvcHRpb25zLmJyZWFrcG9pbnQgJiYgIW1vYmlsZSkge1xuXHRcdFx0XHQvLyBDbG9zZSBhbGwgbWVudXMgb24gc3dpdGNoIHRvIG1vYmlsZSB2aWV3XG5cdFx0XHRcdF9zZXRDbGFzc2VzKG51bGwpO1xuXHRcdFx0XHRtb2JpbGUgPSB0cnVlO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBOYXZiYXIgVG9wYmFyIEl0ZW0gQ2xpY2tcblx0XHQgKlxuXHRcdCAqIFRoaXMgaGFuZGxlciBtdXN0IGNsb3NlIHRoZSBvdGhlciBvcGVuZWQgZnJhbWVzIGJlY2F1c2Ugb25seSBvbmUgaXRlbSBzaG91bGQgYmUgdmlzaWJsZS5cblx0XHQgKlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jbGlja1RvcEJhckl0ZW1IYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJCh0aGlzKS5wYXJlbnQoKS5oYXNDbGFzcygnb3BlbicpKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblx0XHRcdCQoJy5uYXZiYXItY2F0ZWdvcmllcycpLmZpbmQoJy5uYXZiYXItdG9wYmFyLWl0ZW0ub3BlbicpLnJlbW92ZUNsYXNzKCdvcGVuJyk7XG5cdFx0XHQkKCcjY2F0ZWdvcmllcyAubmF2YmFyLWNvbGxhcHNlOmZpcnN0JykuYW5pbWF0ZSh7XG5cdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNjcm9sbFRvcDogJCh0aGlzKS5wYXJlbnQoKS5wb3NpdGlvbigpLnRvcCArICQodGhpcylcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAucGFyZW50KClcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuaGVpZ2h0KCkgLSAkKCcjaGVhZGVyIC5uYXZiYXItaGVhZGVyJykuaGVpZ2h0KClcblx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0sIDUwMCk7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHRtb2JpbGUgPSBqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKS5pZCA8PSBvcHRpb25zLmJyZWFrcG9pbnQ7XG5cdFx0XHQkYnV0dG9ucyA9ICR0aGlzLmZpbmQoJ2J1dHRvbicpO1xuXG5cdFx0XHQkYm9keS5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBfYnJlYWtwb2ludEhhbmRsZXIpO1xuXHRcdFx0JCgnLm5hdmJhci1jYXRlZ29yaWVzJykub24oJ21vdXNldXAnLCAnLm5hdmJhci10b3BiYXItaXRlbSA+IGEnLCBfY2xpY2tUb3BCYXJJdGVtSGFuZGxlcik7XG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCAnYnV0dG9uJywgX2NsaWNrSGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
