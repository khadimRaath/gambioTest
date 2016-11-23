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
gambio.widgets.module(
	'live_search',

	[
		'form',
		'xhr',
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

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
		var _setAutosuggestActive = function(index) {
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
		var _autoSuggestNavigationHandler = function(e) {
			var $all = $target.find('li'),
				$active = $all.filter('.active'),
				index = null,
				href = null;

			// Handler for the different key codes
			switch (e.keyCode) {
				case 13: // ENTER
					if ($active.length) {
						e.preventDefault();
						e.stopPropagation();

						href = $active
							.find('a')
							.attr('href');

						location.href = href;
					}
					break;
				case 38: // UP
					index = ($active.length) ? ($active.index() - 1) : ($all.length - 1);
					_setAutosuggestActive(index);
					break;
				case 40: // DOWN
					index = ($active.length) ? ($active.index() + 1) : 0;
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
		var _show = function(content) {
			transition.open = true;
			$target
				.html(content)
				.trigger(jse.libs.template.events.TRANSITION(), transition);

			// Inform other layers
			$this.trigger(jse.libs.template.events.OPEN_FLYOUT(), [$this]);

			$this
				.off('keydown.autosuggest')
				.on('keydown.autosuggest', _autoSuggestNavigationHandler);
		};

		/**
		 * Helper function to hide the dropdown
		 * @private
		 */
		var _hide = function() {
			transition.open = false;
			$target
				.off()
				.one(jse.libs.template.events.TRANSITION_FINISHED(), function() {
					$target.empty();
				})
				.trigger(jse.libs.template.events.TRANSITION(), transition);

			$this.off('keydown.autosuggest');
		};


// ########## EVENT HANDLER ##########

		/**
		 * Handler for the keyup event inside the search
		 * input field. It performs an ajax request after
		 * a given delay time to relieve the server
		 * @private
		 */
		var _keyupHandler = function(e) {

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

			timeout = setTimeout(function() {
				// Abort a pending ajax request
				if (ajaxCall) {
					ajaxCall.abort();
				}

				// Request the server for the search result
				ajaxCall = jse.libs.xhr.post({
					                             url: options.url,
					                             data: dataset,
					                             dataType: 'html'
				                             }, true).done(function(result) {
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
		var _categoryChangeHandler = function() {
			$input.trigger('focus', []);
		};

		/**
		 * Handles the switch between the breakpoints. If
		 * a switch between desktop & mobile view is detected
		 * the autosuggest layer will be closed
		 * again
		 * @private
		 */
		var _breakpointHandler = function() {

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
		var _closeFlyout = function(e, d) {
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
		module.init = function(done) {

			var focus = options.reopen ? ' focus' : '';

			mobile = jse.libs.template.responsive.breakpoint().id <= options.breakpoint;
			transition.classOpen = options.classOpen;
			$target = $this.find(options.target);
			$input = $this.find('input');
			$target.hide();

			$body
				.on(jse.libs.template.events.OPEN_FLYOUT() + ' click', _closeFlyout)
				.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler);

			$this
				.on('keyup' + focus, 'input', _keyupHandler)
				.on('change', 'select', _categoryChangeHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});
