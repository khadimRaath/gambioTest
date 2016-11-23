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
gambio.widgets.module(
	'pageup',

	[
		gambio.source + '/libs/events'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$window = $(window),
			visible = false,
			transition = {},
			defaults = {
				top: 200,        // Pixel from top needs to be reached before the button gets displayed
				duration: 300,        // Animation time to scroll up
				showClass: 'visible'   // Class that gets added to show the pageup element (else it will be hidden)
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
		var _scrollHandler = function() {
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
		var _clickHandler = function(e) {
			e.preventDefault();
			$('html, body').animate({scrollTop: '0'}, options.duration);
		};

// ########## INITIALIZATION ##########


		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			transition.classOpen = options.showClass;

			$window.on('scroll', _scrollHandler);
			$this.on('click', _clickHandler);

			_scrollHandler();

			done();
		};

		// Return data to widget engine
		return module;
	});
