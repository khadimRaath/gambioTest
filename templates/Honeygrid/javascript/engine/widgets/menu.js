/* --------------------------------------------------------------
 menu.js 2016-09-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * This widget handles the horizontal menu/dropdown functionality.
 *
 * It's used for the top category navigation, the cart dropdown or the top menu (for example). It is
 * able to re-order the menu entries to a special "More" submenu to save space if the entries don't
 * fit in the current view. It's also able to work with different event types for opening/closing menu
 * items in the different view types.
 */
gambio.widgets.module(
	'menu',

	[
		gambio.source + '/libs/events',
		gambio.source + '/libs/responsive',
		gambio.source + '/libs/interaction'
	],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$window = $(window),
			$body = $('body'),
			$list = null,
			$entries = null,
			$more = null,
			$moreEntries = null,
			$menuEntries = null,
			$custom = null,
			$categories = null,
			touchEvents = null,
			currentWidth = null,
			mode = null,
			mobile = false,
			enterTimer = null,
			leaveTimer = null,
			initializedPos = false,
			onEnter = false,
			toucheStartEvent = null,
			toucheEndEvent = null,
			transition = {},
			isTouchDevice = Modernizr.touchevents || navigator.userAgent.search(/Touch/i) !== -1,
			defaults = {
				// The menu type must be either 'horizontal' or 'vertical'
				menuType: 'horizontal',

				// Vertical menu options.
				unfoldLevel: 0,
				accordion: false,
				showAllLink: false,

				// Minimum breakpoint to switch to mobile view
				breakpoint: 40,
				// Delay in ms after a mouseenter the element gets shown
				enterDelay: 0,
				// Delay in ms after a mouseleave an element gets hidden
				leaveDelay: 50,
				// Tolerance in px which gets substracted from the nav-width to prevent flickering
				widthTolerance: 10,
				// Class that gets added to an opened menu list item
				openClass: 'open',
				// If true, elements get moved from/to the more menu if there isn't enough space
				switchElementPosition: true,
				// Ignore menu functionality on elements inside this selection
				ignoreClass: 'ignore-menu',
				// Tolerance in px which is allowed for a "click" event on touch
				touchMoveTolerance: 10,
				// If true, the li with the active class gets opened
				openActive: false,
				events: {
					// Event types that open the menus in desktop view.
					// Possible values: ['click']; ['hover']; ['touch', 'hover']; ['click', 'hover']
					desktop: ['touch', 'hover'],
					// Event types that open the menus in mobile view.
					// Possible values: ['click']; ['hover']; ['touch', 'hover']; ['click', 'hover']; ['touch', 'click']
					mobile: ['touch', 'click']
				}
			},
			options = $.extend({}, defaults, data),
			module = {};


// ########## HELPER FUNCTIONS ##########

		/**
		 * Helper function to calculate the tolerance
		 * between the touchstart and touchend event.
		 * If the max tolarance is exceeded return true
		 * @param       {object}        e       jQuery event object
		 * @return     {boolean}               If true it is a move event
		 * @private
		 */
		var _touchMoveDetect = function() {
			toucheEndEvent = toucheEndEvent || toucheStartEvent;
			var diff = Math.abs(toucheEndEvent.event.originalEvent.pageY - toucheStartEvent.event.originalEvent.pageY);
			toucheEndEvent = null;
			return diff > options.touchMoveTolerance;
		};

		/**
		 * Updates the jQuery selection, because the
		 * list elements can be moved
		 *
		 * @private
		 */
		var _getSelections = function() {
			$list = $this.children('ul');
			// Exclude the ".navbar-topbar-item" elements because they
			// are cloned to this menu and are only shown in mobile view
			$entries = $list.children().not('.navbar-topbar-item');
			$more = $entries.filter('.dropdown-more');
			$moreEntries = $more.children('ul');
			$custom = $entries.filter('.custom');
			$menuEntries = $entries.not($more);
			$categories = $menuEntries.not($custom);
		};

		/**
		 * Helper function that detaches an element from the
		 * menu and attaches it to the correct position at
		 * the target
		 * @param       {object}    $item       jQuery selection of the item that gets detached / attached
		 * @param       {object}    $target     jQuery selection of the target container
		 * @private
		 */
		var _setItem = function($item, $target) {
			var positionId = $item.data('position'),
				done = false;

			// Look for the first item that has a higher
			// positionId that the item and insert it
			// before the found entry
			$target
				.children()
				.each(function() {
					var $self = $(this),
						position = $self.data('position');

					if (position > positionId) {
						$self.before($item.detach());
						done = true;
						return false;
					}
				});

			// Append the item if the positionId has
			// a higher value as the last item int the
			// target
			if (!done) {
				$target.append($item);
			}
		};

		/**
		 * Helper function that checks which elements needs
		 * to be added to the menu. Every element that needs
		 * to be added gets passed to the function
		 * "_setItem"
		 * @param       {integer}       diff        Amount of pixels that were free
		 * @private
		 */
		var _addElement = function(diff) {

			var done = false;

			/**
			 * Helper function that loops through the elements
			 * and tries to add the elements to the menu if
			 * it would fit.
			 * @param       {object}    $elements       jQuery selection of the entries inside the more-menu
			 * @private
			 */
			var _showElements = function($elements) {
				$elements.each(function() {
					var $self = $(this),
						width = $self.data().width;

					if (diff > width) {
						// Add the item to the menu
						_setItem($self, $list);
						diff -= width;
					} else {
						// The next item wouldn't fit anymore',
						// quit the loop
						done = true;
						return false;
					}
				});
			};

			// Update the selection of the visible menu items.
			_getSelections();

			// Add the content manager entries to the menu first.
			// If there is still space, add the "normal" category
			// items also
			_showElements($moreEntries.children('.custom'));
			if (!done) {
				_showElements($moreEntries.children());
			}

			// Check if the items still in the more menu
			// would fit inside the main menu if the more
			// menu would get hidden
			var width = 0;
			$moreEntries
				.children()
				.each(function() {
					width += $(this).data().width;
				});

			if (width === 0) {
				$more.hide();
			} else if (width < ($more.data().width + diff)) {
				$more.hide();
				diff += $more.data().width;
				_showElements($moreEntries.children());
			}

		};

		/**
		 * Helper function that checks which elements needs
		 * to be removed from the menu, so that it fits
		 * inside one menu line. Every element that needs
		 * to be removed gets passed to the function
		 * "_setItem"
		 * @param       {integer}       diff        Amount of pixels that needs to be saved
		 * @private
		 */
		var _removeElement = function(diff) {

			var done = false;

			/**
			 * Helper function that contains the check
			 * loop for determining which elements
			 * needs to be removed
			 * @param           {object}    $elements       jQuery selection of the menu items
			 * @private
			 */
			var _hideElements = function($elements) {
				$elements.each(function() {
					var $self = $(this),
						width = $self.data().width;

					// Remove the possibly set open state
					$self
						.filter('.' + options.openClass)
						.add($self.find('.' + options.openClass))
						.removeClass(options.openClass);

					// Add the entry to the more-menu
					_setItem($self, $moreEntries);

					diff -= width;

					if (diff < 0) {
						// Enough elements are removed,
						// quit the loop
						done = true;
						return false;
					}
				});
			};

			// Update the selection of the visible menu items
			_getSelections();

			// Add the width of the more entry if it's not
			// visible, because it will get shown during this
			// function call
			if ($more.is(':hidden')) {
				diff += $more.data().width;
				$more.removeClass('style');
				$more.show();
			}

			// First remove "normal" category entries. If that
			// isn't enough remove the content manager entries also
			_hideElements($($categories.get().reverse()));
			if (!done) {
				_hideElements($($custom.get().reverse()));
			}
		};

		/**
		 * Sets a data attribute to the menu items
		 * that contains the width of the elements.
		 * This is needed because if it is display
		 * none the detected with will be zero. It
		 * sets position id also.
		 * @private
		 */
		var _initElementSizesAndPosition = function() {
			$entries.each(function(i) {
				var $self = $(this),
					width = $self.outerWidth();

				$self.data({width: width, position: i});
			});
		};

		/**
		 * Helper function to close all menu entries.
		 * Needed for the desktop <-> mobile view
		 * change, mostly.
		 * @private
		 */
		var _closeMenu = function() {
			$this.find('li.' + options.openClass).each(function() {
				if ($(this).parents('.navbar-categories-left').length > 0) {
					return true;
				}
				$(this).removeClass(options.openClass);
			});
		};

		/**
		 * Helper function to clear all pending
		 * functions
		 * @private
		 */
		var _clearTimeouts = function() {
			enterTimer = enterTimer ? clearTimeout(enterTimer) : null;
			leaveTimer = leaveTimer ? clearTimeout(leaveTimer) : null;
		};

		/**
		 * Helper function to reset the css of the menu.
		 * This is needed to remove the overflow & height
		 * settings of the menu of the css file. The
		 * directives were set to prevent flickering on page
		 * load
		 * @private
		 */
		var _resetInitialCss = function() {
			$this.css({
				          'overflow': 'visible',
				          'height': 'auto'
			          });
		};

		/**
		 * Helper function to set positioning classes
		 * to the opend flyout. This is needed to keep
		 * the flyout inside the boundaries of the navigation
		 * @private
		 */
		var _repositionOpenLayer = function() {
			var listWidth = $list.width(),
				$openLayer = $entries
					.filter('.' + options.openClass)
					.children('ul');

			$openLayer.each(function() {
				var $self = $(this),
					$parent = $self.parent();

				// Reset the classes to prevent wrong calculation due to special styles
				$parent.removeClass('flyout-right flyout-left flyout-center flyout-wont-fit');

				var width = $self.outerWidth(),
					parentPosition = $parent.position().left,
					parentWidth = $parent.outerWidth();

				// Check witch class needs to be set
				if (listWidth > parentPosition + width) {
					$parent.addClass('flyout-right');
				} else if (parentPosition + parentWidth - width > 0) {
					$parent.addClass('flyout-left');
				} else if (width < listWidth) {
					$parent.addClass('flyout-center');
				} else {
					$parent.addClass('flyout-wont-fit');
				}

			});
		};

		/**
		 * Helper function to calculate the difference between
		 * the size of the visible elements in the menu and the
		 * container size. If there is space, it calls the function
		 * to activate an menu entry else it calls the function to
		 * deactivate a menu entry
		 * @param       {object}    e         jQuery event object
		 * @param       {string}    eventName Event name parameter of the event object
		 * @private
		 */
		var _updateCategoryMenu = function(e, eventName) {
			var containerWidth = $this.innerWidth() - options.widthTolerance,
				width = 0;

			// Check if the container width has changed since last call
			if (options.menuType === 'horizontal' 
				&& (currentWidth !== containerWidth || eventName === 'switchedToDesktop')) {

				$list
					.children(':visible')
					.each(function() {
						width += $(this).data('width');
					});

				// Add or remove elements depending on the size of the
				// visible elements
				if (containerWidth < width) {
					_removeElement(width - containerWidth);
				} else {
					_addElement(containerWidth - width);
				}

				_repositionOpenLayer();

				currentWidth = containerWidth;
			}

		};

		/**
		 * Helper function to switch to the mobile
		 * mode of the menu.
		 * @private
		 */
		var _switchToMobileView = function() {
			// Reset the current width so that
			// the "_updateCategoryMenu" will
			// perform correctly on the next view
			// change to desktop
			currentWidth = -1;
			_addElement(99999999);

			// Use the vertical menu on mobile view.
			if (options.menuType === 'vertical') {
				// fixes display horizontal menu after a switch to mobile and back to desktop is performed
				if ($('#categories nav.navbar-default:first').not('.nav-categories-left').length > 0) {
					$('#categories nav.navbar-default:first').css({
						                                              opacity: 0,
						                                              height: 0
					                                              })
					                                         .children().hide();
				}
				
				// move topmenu-content items from horizontal menu to vertical menu
				$this
					.find('ul.level-1 li.navbar-topbar-item:first')
					.before($('#categories nav.navbar-default li.topmenu-content').detach());
				
				$this.appendTo('#categories > .navbar-collapse');
				$this.addClass('navbar-default navbar-categories');
				$this.find('ul.level-1').addClass('navbar-nav');
				$this.find('.navbar-topbar-item').not('.topbar-search').show();
				
				_bindHorizontalEventHandlers();
				
				$body.trigger(jse.libs.template.events.MENU_REPOSITIONED(), ['switchedToMobile']);
			}
		};

		/**
		 * Helper function to switch to the desktop
		 * mode of the menu. Additionally, in case that
		 * the desktop mode is shown for the first time
		 * set the position and width of the elements
		 * @private
		 */
		var _switchToDesktopView = function() {
			// Revert all the changes made during the switch to mobile.
			if (options.menuType === 'vertical') {
				// fixes display horizontal menu after a switch to mobile and back to desktop is performed
				if ($('#categories nav.navbar-default:first').not('.nav-categories-left').length > 0) {
					$('#categories nav.navbar-default:first').css({
						                                              opacity: 1,
						                                              height: 'auto'
					                                              })
					                                         .children().show();
				}
				
				// move topmenu-content items back to horizontal menu
				var $topmenuContentElements = $this.find('li.topmenu-content').detach();
				$('#categories nav.navbar-default ul.level-1:first').append($topmenuContentElements);
				
				$this.appendTo('.box-categories');
				$this.removeClass('navbar-default navbar-categories');
				$this.find('ul.level-1').removeClass('navbar-nav');
				$this.find('.navbar-topbar-item').hide();
				_unbindHorizontalEventHandlers();
				
				$body.trigger(jse.libs.template.events.MENU_REPOSITIONED(), ['switchedToDesktop']);
			}


			if (!initializedPos) {
				_initElementSizesAndPosition();
				initializedPos = true;
			}

			if (options.menuType === 'horizontal') {
				_updateCategoryMenu();

				if (isTouchDevice) {
					$list.find('.enter-category').show();
					$list.find('.dropdown > a').click(function(e) {
						e.preventDefault();
					});
				}
			}
		};

		/**
		 * Helper function to add the class to the li-element
		 * depending on the open event. This can be a "touch"
		 * or a "mouse" class
		 * @param       {object}    $target         jQuery selection of the li-element
		 * @param       {string}    className       Name of the class that gets added
		 * @private
		 */
		var _setEventTypeClass = function($target, className) {
			$target
				.removeClass('touch mouse')
				.addClass(className || '');
		};


// ########## MAIN FUNCTIONALITY ##########

		/**
		 * Function that gets called by the breakpoint trigger
		 * (which is fired on browser resize). It checks for
		 * CSS view changes and reconfigures the the JS behaviour
		 * of the menu in that case
		 * @private
		 */
		var _breakpointHandler = function() {

			// Get the current viewmode
			var oldMode = mode || {},
				newMode = jse.libs.template.responsive.breakpoint();

			// Only do something if the view was changed
			if (newMode.id !== oldMode.id) {

				// Check if a view change between mobile and desktop view was made
				var switchToMobile = (newMode.id <= options.breakpoint && (!mobile || oldMode.id === undefined)),
					switchToDesktop = (newMode.id > options.breakpoint && (mobile || oldMode.id === undefined));

				// Store the new view settings
				mobile = newMode.id <= options.breakpoint;
				mode = $.extend({}, newMode);

				if (switchToMobile || switchToDesktop) {
					_clearTimeouts();
					if (options.menuType !== 'vertical') {
						_closeMenu();
					}

					// Change the visibility of the menu items
					// in case of desktop <-> mobile view change
					if (options.switchElementPosition) {
						if (switchToMobile) {
							_switchToMobileView();
						} else {
							_switchToDesktopView();
						}
					} else {
						_repositionOpenLayer();
					}

				} else if (!mobile && options.switchElementPosition) {
					// Update the visibility of the menu items
					// if the view change was desktop to desktop only
					_updateCategoryMenu();
				} else if (!mobile) {
					_repositionOpenLayer();
				}

			}

		};


// ######### EVENT HANDLER ##########

		/**
		 * Changes the epand / collapse state of the menu,
		 * if there is an submenu. In the other case it
		 * will let execute the default action (most times
		 * the execution of a link)
		 * @param {object}  e       jQuery event object
		 * @param {string}  mode    The current view mode (can be "mobile" or "desktop"
		 * @param {integer} delay   Custom delay (in ms) for opening closing the menu (needed for click / touch events)
		 * @private
		 */
		var _openMenu = function(e, type, delay) {

			var $self = $(this),
				$submenu = $self.children('ul'),
				length = $submenu.length,
				level = ($submenu.length) ? ($submenu.data('level') || '0') : 99,
				validSubmenu = (parseInt(level, 10) <= 2 && mode.id > options.breakpoint) || mode.id
					<= options.breakpoint;

			if (type === 'mobile') {
				e.stopPropagation();
			}

			// Only change the state if there is
			// a submenu
			if (length && validSubmenu) {
				e.preventDefault();

				if (type === 'mobile') {
					// Simply toggle the openClass in mobile mode
					$self.toggleClass(options.openClass);
				} else {
					// Perform the else case for the desktop view

					var visible = $self.hasClass(options.openClass),
						leave = $self.hasClass('leave'),
						action = (e.data && e.data.action) ? e.data.action :
						         (visible && leave) ? 'enter' :
						         visible ? 'leave' : 'enter';

					// Depending on the visibility and the event-action-parameter
					// the submenu gets opened or closed
					switch (action) {
						case 'enter':
							if (!onEnter && !jse.libs.template.interaction.isMouseDown()) {
								onEnter = true;
								// Set a timer for opening if the submenu (delayed opening)
								_clearTimeouts();
								enterTimer = setTimeout(function() {

									// Remove all openClass-classes from the
									// menu except the element to open and it's parents
									$list
										.find('.' + options.openClass)
										.not($self)
										.not($self.parentsUntil($this, '.' + options.openClass))
										.trigger(jse.libs.template.events.TRANSITION_STOP(), [])
										.removeClass(options.openClass);

									$list
										.find('.leave')
										.trigger(jse.libs.template.events.TRANSITION_STOP(), [])
										.removeClass('leave');

									// Open the submenu
									transition.open = true;

									// Set and unset the "onEnter" to prevent
									// closing the menu immediately after opening if
									// the cursor is at an place over the opening menu
									// (this can happen if other components trigger the
									// open event)
									$self
										.off(jse.libs.template.events.TRANSITION_FINISHED())
										.one(jse.libs.template.events.TRANSITION_FINISHED(), function() {
											onEnter = false;
										})
										.trigger(jse.libs.template.events.TRANSITION(), transition)
										.trigger(jse.libs.template.events.OPEN_FLYOUT(), [$this]);

									_repositionOpenLayer();
								}, (typeof delay === 'number') ? delay : options.enterDelay);

							}

							break;
						case 'leave':
							onEnter = false;
							// Set a timer for closing if the submenu (delayed closing)
							_clearTimeouts();
							$self.addClass('leave');
							leaveTimer = setTimeout(function() {
								// Remove all openClass-classes from the
								// menu except the elements parents
								transition.open = false;
								$list
									.find('.' + options.openClass)
									.not($self.parentsUntil($this, '.' + options.openClass))
									.off(jse.libs.template.events.TRANSITION_FINISHED())
									.one(jse.libs.template.events.TRANSITION_FINISHED(), function() {
										_setEventTypeClass($self, '');
										$self.removeClass('leave');
									})
									.trigger(jse.libs.template.events.TRANSITION(), transition);


							}, (typeof delay === 'number') ? delay : options.leaveDelay);
							break;
						default:
							break;
					}

				}

			}

		};

		/**
		 * Event handler for the click / mouseenter / mouseleave event
		 * on the navigation li elements. It checks if the event type
		 * is supported for the current view type and calls the
		 * openMenu-function if so.
		 * @param       {object}    e           jQuery event object
		 * @private
		 */
		var _mouseHandler = function(e) {
			var $self = $(this),
				viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop',
				events = (options.events && options.events[viewport]) ? options.events[viewport] : [];
			
			_setEventTypeClass($self, 'mouse');
			if ($.inArray(e.data.event, events) > -1) {
				_openMenu.call($self, e, viewport, e.data.delay);
			}
			
			// Perform navigation for custom links and category links on touch devices if no subcategories are found.
			if (($self.hasClass('custom') || (isTouchDevice && $self.children('ul').length === 0)) 
				&& e.data.event === 'click' && !$self.find('form').length) {
				e.preventDefault();
				e.stopPropagation();
				
				if ($self.find('a').attr('target') === '_blank') {
					window.open($self.find('a').attr('href'));
				} else {
					location.href = $self.find('a').attr('href');		
				}
			}
		};

		/**
		 * Event handler for the touchstart event (or "pointerdown"
		 * depending on the browser). It removes the other critical
		 * event handler (that would open the menu) from the list
		 * element if the the mouseenter was executed before and
		 * a click or touch event will be performed afterwards. This
		 * is needed to prevent the browser engine workarounds which
		 * will automatically perform mouse / click-events on touch
		 * also.
		 * @private
		 */
		var _touchHandler = function(e) {
			e.stopPropagation();

			var $self = $(this),
				viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop',
				events = (options.events && options.events[viewport]) ? options.events[viewport] : [];

			$list.find('.enter-category').show();
			$list.find('.dropdown > a').on('click', function(e) {
				e.preventDefault();
			});

			if (e.data.type === 'start') {
				toucheStartEvent = {event: e, timestamp: new Date().getTime(), top: $window.scrollTop()};
				$list.off('mouseenter.menu mouseleave.menu');
			} else if ($.inArray('touch', events) > -1 && !_touchMoveDetect(e)) {
				_setEventTypeClass($self, 'touch');

				if ($.inArray('hover', events) === -1 || touchEvents.start !== 'pointerdown') {
					_openMenu.call($self, e, viewport);
				}

				$list.on('mouseleave', function() {
					$list
						.on('mouseenter.menu', 'li', {event: 'hover'}, _mouseHandler)
						.on('mouseleave.menu', 'li', {event: 'hover', action: 'leave'}, _mouseHandler);
				});

			}

		};

		/**
		 * Stores the last touch position on touchmove
		 * @param       e       jQuery event object
		 * @private
		 */
		var _touchMoveHandler = function(e) {
			toucheEndEvent = {event: e, timestamp: new Date().getTime(), top: $window.scrollTop()};
		};

		/**
		 * Event handler for closing the menu if
		 * the user interacts with the page
		 * outside of the menu
		 * @param       {object}    e       jQuery event object
		 * @param       {object}    d       jQuery selection of the event emitter
		 * @private
		 */
		var _closeFlyout = function(e, d) {
			if (d !== $this && $this.find($(e.target)).length === 0) {
				// Remove open and close timer
				_clearTimeouts();

				// Remove all state-classes from the menu
				if (options.menuType === 'horizontal') {
					$list
						.find('.touch, .mouse, .leave, .' + options.openClass)
						.removeClass('touch mouse leave ' + options.openClass);
				}
			}
		};

		var _onClickAccordion = function(e) {
			e.preventDefault();
			e.stopPropagation();

			if ($(this).parents('.navbar-topbar-item').length > 0) {
				return;
			}

			if ($(this).hasClass('dropdown')) {
				if ($(this).hasClass(options.openClass)) {
					$(this)
						.removeClass(options.openClass)
						.find('.' + options.openClass)
						.removeClass(options.openClass);
				} else {
					$(this)
						.addClass(options.openClass)
						.parentsUntil($this, 'li')
						.addClass(options.openClass);
				}
			} else {
				location.href = $(this).find('a').attr('href');
			}
		};

		var _bindHorizontalEventHandlers = function() {
			$list
				.on(touchEvents.start + '.menu', 'li', {type: 'start'}, _touchHandler)
				.on(touchEvents.move + '.menu', 'li', {type: 'start'}, _touchMoveHandler)
				.on(touchEvents.end + '.menu', 'li', {type: 'end'}, _touchHandler)
				.on('click.menu', 'li', {event: 'click', 'delay': 0}, _mouseHandler)
				.on('mouseenter.menu', 'li', {event: 'hover', action: 'enter'}, _mouseHandler)
				.on('mouseleave.menu', 'li', {event: 'hover', action: 'leave'}, _mouseHandler);
			
			$body
				.on(jse.libs.template.events.MENU_REPOSITIONED(), _updateCategoryMenu);
		};

		var _unbindHorizontalEventHandlers = function() {
			$list
				.off(touchEvents.start + '.menu', 'li')
				.off(touchEvents.move + '.menu', 'li')
				.off(touchEvents.end + '.menu', 'li')
				.off('click.menu', 'li')
				.off('mouseenter.menu', 'li')
				.off('mouseleave.menu', 'li');
		};

// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			// @todo Getting the "touchEvents" config value produces problems in tablet devices.
			touchEvents = jse.core.config.get('touch');
			transition.classOpen = options.openClass;

			_getSelections();
			_resetInitialCss();

			$body
				.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler)
				.on(jse.libs.template.events.OPEN_FLYOUT() + ' click ' + touchEvents.end, _closeFlyout);

			if (options.menuType === 'horizontal') {
				_bindHorizontalEventHandlers();
			}

			if (options.menuType === 'vertical') {
				if (options.accordion === true) {
					$this.on('click', 'li', _onClickAccordion);
				}

				// if there is no top header we must create dummy html because other modules will not work correctly
				if ($('#categories').length === 0) {
					var html = '<div id="categories"><div class="navbar-collapse collapse">'
						+ '<nav class="navbar-default navbar-categories hidden"></nav></div></div>';
					$('#header').append(html);
				}
			}

			_breakpointHandler();
			
			/**
			 * Stop the propagation of the events inside this container
			 * (Workaround for the "more"-dropdown)
			 */
			$this
				.find('.' + options.ignoreClass)
				.on('mouseleave.menu mouseenter.menu click.menu ' + touchEvents.start + ' '
					+ touchEvents.end, 'li', function(e) {
					e.stopPropagation();
				});
			
			if (options.openActive) {
				var $active = $this.find('.active');
				$active
					.parentsUntil($this, 'li')
					.addClass('open');
			}

			done();
		};

		// Return data to widget engine
		return module;
	});
