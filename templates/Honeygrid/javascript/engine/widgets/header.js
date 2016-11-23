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
gambio.widgets.module(
	'header',

	[
		gambio.source + '/libs/events'
	],

	function(data) {

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
		var _scrollHandler = function() {
			var position = $(document).scrollTop(),
				hasClass = $header.hasClass(options.stickyClass),
				scrollUp = currentPosition > position;

			if (position > options.scrollPosition && !scrollUp) {
				// Proceed if scrolling down under the minimum position given by the options
				scrollUpCounter = 0;
				if (!hasClass && !hover) {
					// Proceed if the class isn't set yet and the header isn't hovered with the mouse
					transition.open = false;
					$header
						.trigger(jse.libs.template.events.TRANSITION(), transition)
						.trigger(jse.libs.template.events.OPEN_FLYOUT(), [$this]);
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
			timeout = setTimeout(function() {
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
		var _mouseEnterHandler = function() {
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
		var _mouseOutHandler = function() {
			hover = false;
			_scrollHandler();
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$header = $this.find(options.header);
			currentPosition = $(document).scrollTop();
			transition.classClose = options.stickyClass;

			$window.on('scroll', _scrollHandler);

			// Add event handler for the mouseover events
			// this can cause problems with flickering menus!
			if (options.hover) {
				$header
					.on('mouseenter', _mouseEnterHandler)
					.on('mouseleave', _mouseOutHandler);
			}

			// Set the initial state of the header
			_scrollHandler();

			done();
		};

		// Return data to widget engine
		return module;
	});
