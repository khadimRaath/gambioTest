'use strict';

/* --------------------------------------------------------------
 live_search.js 2016-03-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that adds a autosuggest functionality to
 * the search box
 */
gambio.widgets.module('live_search', ['form', 'xhr', gambio.source + '/libs/events', gambio.source + '/libs/responsive'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    $target = null,
	    $input = null,
	    ajaxCall = null,
	    timeout = null,
	    mobile = null,
	    transition = {},
	    defaults = {
		// The minimum diget count for the search needle
		needle: 3,
		// The selector where the result is placed
		target: '.search-result-container',
		// Delay (in ms) after the last keyup event is triggered (for ajax request)
		delay: 200,
		// URL to which the request ist posted
		url: 'shop.php?do=LiveSearch',
		// Minimum breakpoint to switch to mobile view
		breakpoint: 40,
		// If true, the layer will reopen on focus
		reopen: true,
		// Class that gets added to open the auto suggest layer
		classOpen: 'open'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function that sets the active
  * item inside the autosuggest layer
  * @param       {int}       index       The index of the item that is set to active
  * @private
  */
	var _setAutosuggestActive = function _setAutosuggestActive(index) {
		var $all = $target.find('li'),
		    $element = $all.eq(index);

		$all.removeClass('active');

		if (index >= 0) {
			$element.addClass('active');
		}
	};

	/**
  * Handler for the key events (up / down arrow & enter)
  * If the autosuggest layer is opened, navigate through
  * the items of the list
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _autoSuggestNavigationHandler = function _autoSuggestNavigationHandler(e) {
		var $all = $target.find('li'),
		    $active = $all.filter('.active'),
		    index = null,
		    href = null;

		// Handler for the different key codes
		switch (e.keyCode) {
			case 13:
				// ENTER
				if ($active.length) {
					e.preventDefault();
					e.stopPropagation();

					href = $active.find('a').attr('href');

					location.href = href;
				}
				break;
			case 38:
				// UP
				index = $active.length ? $active.index() - 1 : $all.length - 1;
				_setAutosuggestActive(index);
				break;
			case 40:
				// DOWN
				index = $active.length ? $active.index() + 1 : 0;
				_setAutosuggestActive(index);
				break;
			default:
				break;
		}
	};

	/**
  * Helper function to show the ajax
  * result in the search dropdown
  * @param       {string}      content     HTML markup
  * @private
  */
	var _show = function _show(content) {
		transition.open = true;
		$target.html(content).trigger(jse.libs.template.events.TRANSITION(), transition);

		// Inform other layers
		$this.trigger(jse.libs.template.events.OPEN_FLYOUT(), [$this]);

		$this.off('keydown.autosuggest').on('keydown.autosuggest', _autoSuggestNavigationHandler);
	};

	/**
  * Helper function to hide the dropdown
  * @private
  */
	var _hide = function _hide() {
		transition.open = false;
		$target.off().one(jse.libs.template.events.TRANSITION_FINISHED(), function () {
			$target.empty();
		}).trigger(jse.libs.template.events.TRANSITION(), transition);

		$this.off('keydown.autosuggest');
	};

	// ########## EVENT HANDLER ##########

	/**
  * Handler for the keyup event inside the search
  * input field. It performs an ajax request after
  * a given delay time to relieve the server
  * @private
  */
	var _keyupHandler = function _keyupHandler(e) {

		if ($.inArray(e.keyCode, [13, 37, 38, 39, 40]) > -1) {
			return true;
		}

		var dataset = jse.libs.form.getData($this);

		// Clear timeout irrespective of
		// the needle length
		if (timeout) {
			clearTimeout(timeout);
		}

		// Only proceed if the needle contains
		// at least a certain number of digits
		if (dataset.keywords.length < options.needle) {
			_hide();
			return;
		}

		timeout = setTimeout(function () {
			// Abort a pending ajax request
			if (ajaxCall) {
				ajaxCall.abort();
			}

			// Request the server for the search result
			ajaxCall = jse.libs.xhr.post({
				url: options.url,
				data: dataset,
				dataType: 'html'
			}, true).done(function (result) {
				if (result) {
					_show(result);
				} else {
					_hide();
				}
			});
		}, options.delay);
	};

	/**
  * Helper handler to reopen the autosuggests
  * on category dropdown change by triggering
  * the focus event. This needs the option
  * "reopen" to be set
  * @private
  */
	var _categoryChangeHandler = function _categoryChangeHandler() {
		$input.trigger('focus', []);
	};

	/**
  * Handles the switch between the breakpoints. If
  * a switch between desktop & mobile view is detected
  * the autosuggest layer will be closed
  * again
  * @private
  */
	var _breakpointHandler = function _breakpointHandler() {

		var switchToMobile = jse.libs.template.responsive.breakpoint().id <= options.breakpoint && !mobile,
		    switchToDesktop = jse.libs.template.responsive.breakpoint().id > options.breakpoint && mobile;

		if (switchToMobile || switchToDesktop) {
			$target.removeClass(options.classOpen);
		}
	};

	/**
  * Event handler for closing the autosuggest
  * if the user interacts with the page
  * outside of the layer
  * @param       {object}    e       jQuery event object
  * @param       {object}    d       jQuery selection of the event emitter
  * @private
  */
	var _closeFlyout = function _closeFlyout(e, d) {
		if (d !== $this && !$this.find($(e.target)).length) {
			$target.removeClass(options.classOpen);
			$input.trigger('blur', []);
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		var focus = options.reopen ? ' focus' : '';

		mobile = jse.libs.template.responsive.breakpoint().id <= options.breakpoint;
		transition.classOpen = options.classOpen;
		$target = $this.find(options.target);
		$input = $this.find('input');
		$target.hide();

		$body.on(jse.libs.template.events.OPEN_FLYOUT() + ' click', _closeFlyout).on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);

		$this.on('keyup' + focus, 'input', _keyupHandler).on('change', 'select', _categoryChangeHandler);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbGl2ZV9zZWFyY2guanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkYm9keSIsIiR0YXJnZXQiLCIkaW5wdXQiLCJhamF4Q2FsbCIsInRpbWVvdXQiLCJtb2JpbGUiLCJ0cmFuc2l0aW9uIiwiZGVmYXVsdHMiLCJuZWVkbGUiLCJ0YXJnZXQiLCJkZWxheSIsInVybCIsImJyZWFrcG9pbnQiLCJyZW9wZW4iLCJjbGFzc09wZW4iLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3NldEF1dG9zdWdnZXN0QWN0aXZlIiwiaW5kZXgiLCIkYWxsIiwiZmluZCIsIiRlbGVtZW50IiwiZXEiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX2F1dG9TdWdnZXN0TmF2aWdhdGlvbkhhbmRsZXIiLCJlIiwiJGFjdGl2ZSIsImZpbHRlciIsImhyZWYiLCJrZXlDb2RlIiwibGVuZ3RoIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJhdHRyIiwibG9jYXRpb24iLCJfc2hvdyIsImNvbnRlbnQiLCJvcGVuIiwiaHRtbCIsInRyaWdnZXIiLCJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJldmVudHMiLCJUUkFOU0lUSU9OIiwiT1BFTl9GTFlPVVQiLCJvZmYiLCJvbiIsIl9oaWRlIiwib25lIiwiVFJBTlNJVElPTl9GSU5JU0hFRCIsImVtcHR5IiwiX2tleXVwSGFuZGxlciIsImluQXJyYXkiLCJkYXRhc2V0IiwiZm9ybSIsImdldERhdGEiLCJjbGVhclRpbWVvdXQiLCJrZXl3b3JkcyIsInNldFRpbWVvdXQiLCJhYm9ydCIsInhociIsInBvc3QiLCJkYXRhVHlwZSIsImRvbmUiLCJyZXN1bHQiLCJfY2F0ZWdvcnlDaGFuZ2VIYW5kbGVyIiwiX2JyZWFrcG9pbnRIYW5kbGVyIiwic3dpdGNoVG9Nb2JpbGUiLCJyZXNwb25zaXZlIiwiaWQiLCJzd2l0Y2hUb0Rlc2t0b3AiLCJf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RHRCLEVBQytCdkIsYUFEL0IsRUFFRUwsRUFGRixDQUVLLFFBRkwsRUFFZSxRQUZmLEVBRXlCbUIsc0JBRnpCOztBQUlBRjtBQUNBLEVBbkJEOztBQXFCQTtBQUNBLFFBQU90RSxNQUFQO0FBQ0EsQ0F2UUYiLCJmaWxlIjoid2lkZ2V0cy9saXZlX3NlYXJjaC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbGl2ZV9zZWFyY2guanMgMjAxNi0wMy0wOVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogV2lkZ2V0IHRoYXQgYWRkcyBhIGF1dG9zdWdnZXN0IGZ1bmN0aW9uYWxpdHkgdG9cbiAqIHRoZSBzZWFyY2ggYm94XG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J2xpdmVfc2VhcmNoJyxcblxuXHRbXG5cdFx0J2Zvcm0nLFxuXHRcdCd4aHInLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL3Jlc3BvbnNpdmUnXG5cdF0sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkYm9keSA9ICQoJ2JvZHknKSxcblx0XHRcdCR0YXJnZXQgPSBudWxsLFxuXHRcdFx0JGlucHV0ID0gbnVsbCxcblx0XHRcdGFqYXhDYWxsID0gbnVsbCxcblx0XHRcdHRpbWVvdXQgPSBudWxsLFxuXHRcdFx0bW9iaWxlID0gbnVsbCxcblx0XHRcdHRyYW5zaXRpb24gPSB7fSxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQvLyBUaGUgbWluaW11bSBkaWdldCBjb3VudCBmb3IgdGhlIHNlYXJjaCBuZWVkbGVcblx0XHRcdFx0bmVlZGxlOiAzLFxuXHRcdFx0XHQvLyBUaGUgc2VsZWN0b3Igd2hlcmUgdGhlIHJlc3VsdCBpcyBwbGFjZWRcblx0XHRcdFx0dGFyZ2V0OiAnLnNlYXJjaC1yZXN1bHQtY29udGFpbmVyJyxcblx0XHRcdFx0Ly8gRGVsYXkgKGluIG1zKSBhZnRlciB0aGUgbGFzdCBrZXl1cCBldmVudCBpcyB0cmlnZ2VyZWQgKGZvciBhamF4IHJlcXVlc3QpXG5cdFx0XHRcdGRlbGF5OiAyMDAsXG5cdFx0XHRcdC8vIFVSTCB0byB3aGljaCB0aGUgcmVxdWVzdCBpc3QgcG9zdGVkXG5cdFx0XHRcdHVybDogJ3Nob3AucGhwP2RvPUxpdmVTZWFyY2gnLFxuXHRcdFx0XHQvLyBNaW5pbXVtIGJyZWFrcG9pbnQgdG8gc3dpdGNoIHRvIG1vYmlsZSB2aWV3XG5cdFx0XHRcdGJyZWFrcG9pbnQ6IDQwLFxuXHRcdFx0XHQvLyBJZiB0cnVlLCB0aGUgbGF5ZXIgd2lsbCByZW9wZW4gb24gZm9jdXNcblx0XHRcdFx0cmVvcGVuOiB0cnVlLFxuXHRcdFx0XHQvLyBDbGFzcyB0aGF0IGdldHMgYWRkZWQgdG8gb3BlbiB0aGUgYXV0byBzdWdnZXN0IGxheWVyXG5cdFx0XHRcdGNsYXNzT3BlbjogJ29wZW4nXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuXG4vLyAjIyMjIyMjIyMjIEhFTFBFUiBGVU5DVElPTlMgIyMjIyMjIyMjI1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgc2V0cyB0aGUgYWN0aXZlXG5cdFx0ICogaXRlbSBpbnNpZGUgdGhlIGF1dG9zdWdnZXN0IGxheWVyXG5cdFx0ICogQHBhcmFtICAgICAgIHtpbnR9ICAgICAgIGluZGV4ICAgICAgIFRoZSBpbmRleCBvZiB0aGUgaXRlbSB0aGF0IGlzIHNldCB0byBhY3RpdmVcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0QXV0b3N1Z2dlc3RBY3RpdmUgPSBmdW5jdGlvbihpbmRleCkge1xuXHRcdFx0dmFyICRhbGwgPSAkdGFyZ2V0LmZpbmQoJ2xpJyksXG5cdFx0XHRcdCRlbGVtZW50ID0gJGFsbC5lcShpbmRleCk7XG5cblx0XHRcdCRhbGwucmVtb3ZlQ2xhc3MoJ2FjdGl2ZScpO1xuXG5cdFx0XHRpZiAoaW5kZXggPj0gMCkge1xuXHRcdFx0XHQkZWxlbWVudC5hZGRDbGFzcygnYWN0aXZlJyk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSBrZXkgZXZlbnRzICh1cCAvIGRvd24gYXJyb3cgJiBlbnRlcilcblx0XHQgKiBJZiB0aGUgYXV0b3N1Z2dlc3QgbGF5ZXIgaXMgb3BlbmVkLCBuYXZpZ2F0ZSB0aHJvdWdoXG5cdFx0ICogdGhlIGl0ZW1zIG9mIHRoZSBsaXN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9hdXRvU3VnZ2VzdE5hdmlnYXRpb25IYW5kbGVyID0gZnVuY3Rpb24oZSkge1xuXHRcdFx0dmFyICRhbGwgPSAkdGFyZ2V0LmZpbmQoJ2xpJyksXG5cdFx0XHRcdCRhY3RpdmUgPSAkYWxsLmZpbHRlcignLmFjdGl2ZScpLFxuXHRcdFx0XHRpbmRleCA9IG51bGwsXG5cdFx0XHRcdGhyZWYgPSBudWxsO1xuXG5cdFx0XHQvLyBIYW5kbGVyIGZvciB0aGUgZGlmZmVyZW50IGtleSBjb2Rlc1xuXHRcdFx0c3dpdGNoIChlLmtleUNvZGUpIHtcblx0XHRcdFx0Y2FzZSAxMzogLy8gRU5URVJcblx0XHRcdFx0XHRpZiAoJGFjdGl2ZS5sZW5ndGgpIHtcblx0XHRcdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdFx0XHRcdGhyZWYgPSAkYWN0aXZlXG5cdFx0XHRcdFx0XHRcdC5maW5kKCdhJylcblx0XHRcdFx0XHRcdFx0LmF0dHIoJ2hyZWYnKTtcblxuXHRcdFx0XHRcdFx0bG9jYXRpb24uaHJlZiA9IGhyZWY7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlIDM4OiAvLyBVUFxuXHRcdFx0XHRcdGluZGV4ID0gKCRhY3RpdmUubGVuZ3RoKSA/ICgkYWN0aXZlLmluZGV4KCkgLSAxKSA6ICgkYWxsLmxlbmd0aCAtIDEpO1xuXHRcdFx0XHRcdF9zZXRBdXRvc3VnZ2VzdEFjdGl2ZShpbmRleCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgNDA6IC8vIERPV05cblx0XHRcdFx0XHRpbmRleCA9ICgkYWN0aXZlLmxlbmd0aCkgPyAoJGFjdGl2ZS5pbmRleCgpICsgMSkgOiAwO1xuXHRcdFx0XHRcdF9zZXRBdXRvc3VnZ2VzdEFjdGl2ZShpbmRleCk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBzaG93IHRoZSBhamF4XG5cdFx0ICogcmVzdWx0IGluIHRoZSBzZWFyY2ggZHJvcGRvd25cblx0XHQgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgICBjb250ZW50ICAgICBIVE1MIG1hcmt1cFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zaG93ID0gZnVuY3Rpb24oY29udGVudCkge1xuXHRcdFx0dHJhbnNpdGlvbi5vcGVuID0gdHJ1ZTtcblx0XHRcdCR0YXJnZXRcblx0XHRcdFx0Lmh0bWwoY29udGVudClcblx0XHRcdFx0LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cblx0XHRcdC8vIEluZm9ybSBvdGhlciBsYXllcnNcblx0XHRcdCR0aGlzLnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLk9QRU5fRkxZT1VUKCksIFskdGhpc10pO1xuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub2ZmKCdrZXlkb3duLmF1dG9zdWdnZXN0Jylcblx0XHRcdFx0Lm9uKCdrZXlkb3duLmF1dG9zdWdnZXN0JywgX2F1dG9TdWdnZXN0TmF2aWdhdGlvbkhhbmRsZXIpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gaGlkZSB0aGUgZHJvcGRvd25cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaGlkZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG5cdFx0XHQkdGFyZ2V0XG5cdFx0XHRcdC5vZmYoKVxuXHRcdFx0XHQub25lKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCksIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdCR0YXJnZXQuZW1wdHkoKTtcblx0XHRcdFx0fSlcblx0XHRcdFx0LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT04oKSwgdHJhbnNpdGlvbik7XG5cblx0XHRcdCR0aGlzLm9mZigna2V5ZG93bi5hdXRvc3VnZ2VzdCcpO1xuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSBrZXl1cCBldmVudCBpbnNpZGUgdGhlIHNlYXJjaFxuXHRcdCAqIGlucHV0IGZpZWxkLiBJdCBwZXJmb3JtcyBhbiBhamF4IHJlcXVlc3QgYWZ0ZXJcblx0XHQgKiBhIGdpdmVuIGRlbGF5IHRpbWUgdG8gcmVsaWV2ZSB0aGUgc2VydmVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2tleXVwSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblxuXHRcdFx0aWYgKCQuaW5BcnJheShlLmtleUNvZGUsIFsxMywgMzcsIDM4LCAzOSwgNDBdKSA+IC0xKSB7XG5cdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0fVxuXG5cdFx0XHR2YXIgZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGhpcyk7XG5cblx0XHRcdC8vIENsZWFyIHRpbWVvdXQgaXJyZXNwZWN0aXZlIG9mXG5cdFx0XHQvLyB0aGUgbmVlZGxlIGxlbmd0aFxuXHRcdFx0aWYgKHRpbWVvdXQpIHtcblx0XHRcdFx0Y2xlYXJUaW1lb3V0KHRpbWVvdXQpO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyBPbmx5IHByb2NlZWQgaWYgdGhlIG5lZWRsZSBjb250YWluc1xuXHRcdFx0Ly8gYXQgbGVhc3QgYSBjZXJ0YWluIG51bWJlciBvZiBkaWdpdHNcblx0XHRcdGlmIChkYXRhc2V0LmtleXdvcmRzLmxlbmd0aCA8IG9wdGlvbnMubmVlZGxlKSB7XG5cdFx0XHRcdF9oaWRlKCk7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0dGltZW91dCA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdC8vIEFib3J0IGEgcGVuZGluZyBhamF4IHJlcXVlc3Rcblx0XHRcdFx0aWYgKGFqYXhDYWxsKSB7XG5cdFx0XHRcdFx0YWpheENhbGwuYWJvcnQoKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdC8vIFJlcXVlc3QgdGhlIHNlcnZlciBmb3IgdGhlIHNlYXJjaCByZXN1bHRcblx0XHRcdFx0YWpheENhbGwgPSBqc2UubGlicy54aHIucG9zdCh7XG5cdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6IG9wdGlvbnMudXJsLFxuXHRcdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZGF0YTogZGF0YXNldCxcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGRhdGFUeXBlOiAnaHRtbCdcblx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICB9LCB0cnVlKS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRcdGlmIChyZXN1bHQpIHtcblx0XHRcdFx0XHRcdF9zaG93KHJlc3VsdCk7XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdF9oaWRlKCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHRcdH0sIG9wdGlvbnMuZGVsYXkpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgaGFuZGxlciB0byByZW9wZW4gdGhlIGF1dG9zdWdnZXN0c1xuXHRcdCAqIG9uIGNhdGVnb3J5IGRyb3Bkb3duIGNoYW5nZSBieSB0cmlnZ2VyaW5nXG5cdFx0ICogdGhlIGZvY3VzIGV2ZW50LiBUaGlzIG5lZWRzIHRoZSBvcHRpb25cblx0XHQgKiBcInJlb3BlblwiIHRvIGJlIHNldFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jYXRlZ29yeUNoYW5nZUhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdCRpbnB1dC50cmlnZ2VyKCdmb2N1cycsIFtdKTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlcyB0aGUgc3dpdGNoIGJldHdlZW4gdGhlIGJyZWFrcG9pbnRzLiBJZlxuXHRcdCAqIGEgc3dpdGNoIGJldHdlZW4gZGVza3RvcCAmIG1vYmlsZSB2aWV3IGlzIGRldGVjdGVkXG5cdFx0ICogdGhlIGF1dG9zdWdnZXN0IGxheWVyIHdpbGwgYmUgY2xvc2VkXG5cdFx0ICogYWdhaW5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYnJlYWtwb2ludEhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblxuXHRcdFx0dmFyIHN3aXRjaFRvTW9iaWxlID0ganNlLmxpYnMudGVtcGxhdGUucmVzcG9uc2l2ZS5icmVha3BvaW50KCkuaWQgPD0gb3B0aW9ucy5icmVha3BvaW50ICYmICFtb2JpbGUsXG5cdFx0XHRcdHN3aXRjaFRvRGVza3RvcCA9IGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkID4gb3B0aW9ucy5icmVha3BvaW50ICYmIG1vYmlsZTtcblxuXHRcdFx0aWYgKHN3aXRjaFRvTW9iaWxlIHx8IHN3aXRjaFRvRGVza3RvcCkge1xuXHRcdFx0XHQkdGFyZ2V0LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2xhc3NPcGVuKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgY2xvc2luZyB0aGUgYXV0b3N1Z2dlc3Rcblx0XHQgKiBpZiB0aGUgdXNlciBpbnRlcmFjdHMgd2l0aCB0aGUgcGFnZVxuXHRcdCAqIG91dHNpZGUgb2YgdGhlIGxheWVyXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBkICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIGV2ZW50IGVtaXR0ZXJcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2xvc2VGbHlvdXQgPSBmdW5jdGlvbihlLCBkKSB7XG5cdFx0XHRpZiAoZCAhPT0gJHRoaXMgJiYgISR0aGlzLmZpbmQoJChlLnRhcmdldCkpLmxlbmd0aCkge1xuXHRcdFx0XHQkdGFyZ2V0LnJlbW92ZUNsYXNzKG9wdGlvbnMuY2xhc3NPcGVuKTtcblx0XHRcdFx0JGlucHV0LnRyaWdnZXIoJ2JsdXInLCBbXSk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHR2YXIgZm9jdXMgPSBvcHRpb25zLnJlb3BlbiA/ICcgZm9jdXMnIDogJyc7XG5cblx0XHRcdG1vYmlsZSA9IGpzZS5saWJzLnRlbXBsYXRlLnJlc3BvbnNpdmUuYnJlYWtwb2ludCgpLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludDtcblx0XHRcdHRyYW5zaXRpb24uY2xhc3NPcGVuID0gb3B0aW9ucy5jbGFzc09wZW47XG5cdFx0XHQkdGFyZ2V0ID0gJHRoaXMuZmluZChvcHRpb25zLnRhcmdldCk7XG5cdFx0XHQkaW5wdXQgPSAkdGhpcy5maW5kKCdpbnB1dCcpO1xuXHRcdFx0JHRhcmdldC5oaWRlKCk7XG5cblx0XHRcdCRib2R5XG5cdFx0XHRcdC5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuT1BFTl9GTFlPVVQoKSArICcgY2xpY2snLCBfY2xvc2VGbHlvdXQpXG5cdFx0XHRcdC5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQlJFQUtQT0lOVCgpLCBfYnJlYWtwb2ludEhhbmRsZXIpO1xuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQub24oJ2tleXVwJyArIGZvY3VzLCAnaW5wdXQnLCBfa2V5dXBIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NoYW5nZScsICdzZWxlY3QnLCBfY2F0ZWdvcnlDaGFuZ2VIYW5kbGVyKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
