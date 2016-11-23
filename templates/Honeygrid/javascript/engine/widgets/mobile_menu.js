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
gambio.widgets.module(
	'mobile_menu',

	[
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$body = $('body'),
			$buttons = null,
			mobile = null,
			scrollTop, // scroll top backup
			scrollLeft, // scroll top backup
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
		var _setClasses = function(buttonData) {
			var found = false;

			$buttons.each(function() {
				var $self = $(this),
					d = $(this).parseModuleData('mobile_menu');

				if (!buttonData || (d && d.target !== buttonData.target)) {
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
		var _toggleContentVisibility = function(state) {
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
		var _clickHandler = function() {
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
		var _breakpointHandler = function(e, d) {
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
		var _clickTopBarItemHandler = function() {
			if ($(this).parent().hasClass('open')) {
				return;
			}
			$('.navbar-categories').find('.navbar-topbar-item.open').removeClass('open');
			$('#categories .navbar-collapse:first').animate({
				                                                scrollTop: $(this).parent().position().top + $(this)
					                                                .parent()
					                                                .height() - $('#header .navbar-header').height()
			                                                }, 500);
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

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
