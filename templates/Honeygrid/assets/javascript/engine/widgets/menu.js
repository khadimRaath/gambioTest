'use strict';

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
gambio.widgets.module('menu', [gambio.source + '/libs/events', gambio.source + '/libs/responsive', gambio.source + '/libs/interaction'], function (data) {

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
	var _touchMoveDetect = function _touchMoveDetect() {
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
	var _getSelections = function _getSelections() {
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
	var _setItem = function _setItem($item, $target) {
		var positionId = $item.data('position'),
		    done = false;

		// Look for the first item that has a higher
		// positionId that the item and insert it
		// before the found entry
		$target.children().each(function () {
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
	var _addElement = function _addElement(diff) {

		var done = false;

		/**
   * Helper function that loops through the elements
   * and tries to add the elements to the menu if
   * it would fit.
   * @param       {object}    $elements       jQuery selection of the entries inside the more-menu
   * @private
   */
		var _showElements = function _showElements($elements) {
			$elements.each(function () {
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
		$moreEntries.children().each(function () {
			width += $(this).data().width;
		});

		if (width === 0) {
			$more.hide();
		} else if (width < $more.data().width + diff) {
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
	var _removeElement = function _removeElement(diff) {

		var done = false;

		/**
   * Helper function that contains the check
   * loop for determining which elements
   * needs to be removed
   * @param           {object}    $elements       jQuery selection of the menu items
   * @private
   */
		var _hideElements = function _hideElements($elements) {
			$elements.each(function () {
				var $self = $(this),
				    width = $self.data().width;

				// Remove the possibly set open state
				$self.filter('.' + options.openClass).add($self.find('.' + options.openClass)).removeClass(options.openClass);

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
	var _initElementSizesAndPosition = function _initElementSizesAndPosition() {
		$entries.each(function (i) {
			var $self = $(this),
			    width = $self.outerWidth();

			$self.data({ width: width, position: i });
		});
	};

	/**
  * Helper function to close all menu entries.
  * Needed for the desktop <-> mobile view
  * change, mostly.
  * @private
  */
	var _closeMenu = function _closeMenu() {
		$this.find('li.' + options.openClass).each(function () {
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
	var _clearTimeouts = function _clearTimeouts() {
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
	var _resetInitialCss = function _resetInitialCss() {
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
	var _repositionOpenLayer = function _repositionOpenLayer() {
		var listWidth = $list.width(),
		    $openLayer = $entries.filter('.' + options.openClass).children('ul');

		$openLayer.each(function () {
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
	var _updateCategoryMenu = function _updateCategoryMenu(e, eventName) {
		var containerWidth = $this.innerWidth() - options.widthTolerance,
		    width = 0;

		// Check if the container width has changed since last call
		if (options.menuType === 'horizontal' && (currentWidth !== containerWidth || eventName === 'switchedToDesktop')) {

			$list.children(':visible').each(function () {
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
	var _switchToMobileView = function _switchToMobileView() {
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
				}).children().hide();
			}

			// move topmenu-content items from horizontal menu to vertical menu
			$this.find('ul.level-1 li.navbar-topbar-item:first').before($('#categories nav.navbar-default li.topmenu-content').detach());

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
	var _switchToDesktopView = function _switchToDesktopView() {
		// Revert all the changes made during the switch to mobile.
		if (options.menuType === 'vertical') {
			// fixes display horizontal menu after a switch to mobile and back to desktop is performed
			if ($('#categories nav.navbar-default:first').not('.nav-categories-left').length > 0) {
				$('#categories nav.navbar-default:first').css({
					opacity: 1,
					height: 'auto'
				}).children().show();
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
				$list.find('.dropdown > a').click(function (e) {
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
	var _setEventTypeClass = function _setEventTypeClass($target, className) {
		$target.removeClass('touch mouse').addClass(className || '');
	};

	// ########## MAIN FUNCTIONALITY ##########

	/**
  * Function that gets called by the breakpoint trigger
  * (which is fired on browser resize). It checks for
  * CSS view changes and reconfigures the the JS behaviour
  * of the menu in that case
  * @private
  */
	var _breakpointHandler = function _breakpointHandler() {

		// Get the current viewmode
		var oldMode = mode || {},
		    newMode = jse.libs.template.responsive.breakpoint();

		// Only do something if the view was changed
		if (newMode.id !== oldMode.id) {

			// Check if a view change between mobile and desktop view was made
			var switchToMobile = newMode.id <= options.breakpoint && (!mobile || oldMode.id === undefined),
			    switchToDesktop = newMode.id > options.breakpoint && (mobile || oldMode.id === undefined);

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
	var _openMenu = function _openMenu(e, type, delay) {

		var $self = $(this),
		    $submenu = $self.children('ul'),
		    length = $submenu.length,
		    level = $submenu.length ? $submenu.data('level') || '0' : 99,
		    validSubmenu = parseInt(level, 10) <= 2 && mode.id > options.breakpoint || mode.id <= options.breakpoint;

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
				    action = e.data && e.data.action ? e.data.action : visible && leave ? 'enter' : visible ? 'leave' : 'enter';

				// Depending on the visibility and the event-action-parameter
				// the submenu gets opened or closed
				switch (action) {
					case 'enter':
						if (!onEnter && !jse.libs.template.interaction.isMouseDown()) {
							onEnter = true;
							// Set a timer for opening if the submenu (delayed opening)
							_clearTimeouts();
							enterTimer = setTimeout(function () {

								// Remove all openClass-classes from the
								// menu except the element to open and it's parents
								$list.find('.' + options.openClass).not($self).not($self.parentsUntil($this, '.' + options.openClass)).trigger(jse.libs.template.events.TRANSITION_STOP(), []).removeClass(options.openClass);

								$list.find('.leave').trigger(jse.libs.template.events.TRANSITION_STOP(), []).removeClass('leave');

								// Open the submenu
								transition.open = true;

								// Set and unset the "onEnter" to prevent
								// closing the menu immediately after opening if
								// the cursor is at an place over the opening menu
								// (this can happen if other components trigger the
								// open event)
								$self.off(jse.libs.template.events.TRANSITION_FINISHED()).one(jse.libs.template.events.TRANSITION_FINISHED(), function () {
									onEnter = false;
								}).trigger(jse.libs.template.events.TRANSITION(), transition).trigger(jse.libs.template.events.OPEN_FLYOUT(), [$this]);

								_repositionOpenLayer();
							}, typeof delay === 'number' ? delay : options.enterDelay);
						}

						break;
					case 'leave':
						onEnter = false;
						// Set a timer for closing if the submenu (delayed closing)
						_clearTimeouts();
						$self.addClass('leave');
						leaveTimer = setTimeout(function () {
							// Remove all openClass-classes from the
							// menu except the elements parents
							transition.open = false;
							$list.find('.' + options.openClass).not($self.parentsUntil($this, '.' + options.openClass)).off(jse.libs.template.events.TRANSITION_FINISHED()).one(jse.libs.template.events.TRANSITION_FINISHED(), function () {
								_setEventTypeClass($self, '');
								$self.removeClass('leave');
							}).trigger(jse.libs.template.events.TRANSITION(), transition);
						}, typeof delay === 'number' ? delay : options.leaveDelay);
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
	var _mouseHandler = function _mouseHandler(e) {
		var $self = $(this),
		    viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop',
		    events = options.events && options.events[viewport] ? options.events[viewport] : [];

		_setEventTypeClass($self, 'mouse');
		if ($.inArray(e.data.event, events) > -1) {
			_openMenu.call($self, e, viewport, e.data.delay);
		}

		// Perform navigation for custom links and category links on touch devices if no subcategories are found.
		if (($self.hasClass('custom') || isTouchDevice && $self.children('ul').length === 0) && e.data.event === 'click' && !$self.find('form').length) {
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
	var _touchHandler = function _touchHandler(e) {
		e.stopPropagation();

		var $self = $(this),
		    viewport = mode.id <= options.breakpoint ? 'mobile' : 'desktop',
		    events = options.events && options.events[viewport] ? options.events[viewport] : [];

		$list.find('.enter-category').show();
		$list.find('.dropdown > a').on('click', function (e) {
			e.preventDefault();
		});

		if (e.data.type === 'start') {
			toucheStartEvent = { event: e, timestamp: new Date().getTime(), top: $window.scrollTop() };
			$list.off('mouseenter.menu mouseleave.menu');
		} else if ($.inArray('touch', events) > -1 && !_touchMoveDetect(e)) {
			_setEventTypeClass($self, 'touch');

			if ($.inArray('hover', events) === -1 || touchEvents.start !== 'pointerdown') {
				_openMenu.call($self, e, viewport);
			}

			$list.on('mouseleave', function () {
				$list.on('mouseenter.menu', 'li', { event: 'hover' }, _mouseHandler).on('mouseleave.menu', 'li', { event: 'hover', action: 'leave' }, _mouseHandler);
			});
		}
	};

	/**
  * Stores the last touch position on touchmove
  * @param       e       jQuery event object
  * @private
  */
	var _touchMoveHandler = function _touchMoveHandler(e) {
		toucheEndEvent = { event: e, timestamp: new Date().getTime(), top: $window.scrollTop() };
	};

	/**
  * Event handler for closing the menu if
  * the user interacts with the page
  * outside of the menu
  * @param       {object}    e       jQuery event object
  * @param       {object}    d       jQuery selection of the event emitter
  * @private
  */
	var _closeFlyout = function _closeFlyout(e, d) {
		if (d !== $this && $this.find($(e.target)).length === 0) {
			// Remove open and close timer
			_clearTimeouts();

			// Remove all state-classes from the menu
			if (options.menuType === 'horizontal') {
				$list.find('.touch, .mouse, .leave, .' + options.openClass).removeClass('touch mouse leave ' + options.openClass);
			}
		}
	};

	var _onClickAccordion = function _onClickAccordion(e) {
		e.preventDefault();
		e.stopPropagation();

		if ($(this).parents('.navbar-topbar-item').length > 0) {
			return;
		}

		if ($(this).hasClass('dropdown')) {
			if ($(this).hasClass(options.openClass)) {
				$(this).removeClass(options.openClass).find('.' + options.openClass).removeClass(options.openClass);
			} else {
				$(this).addClass(options.openClass).parentsUntil($this, 'li').addClass(options.openClass);
			}
		} else {
			location.href = $(this).find('a').attr('href');
		}
	};

	var _bindHorizontalEventHandlers = function _bindHorizontalEventHandlers() {
		$list.on(touchEvents.start + '.menu', 'li', { type: 'start' }, _touchHandler).on(touchEvents.move + '.menu', 'li', { type: 'start' }, _touchMoveHandler).on(touchEvents.end + '.menu', 'li', { type: 'end' }, _touchHandler).on('click.menu', 'li', { event: 'click', 'delay': 0 }, _mouseHandler).on('mouseenter.menu', 'li', { event: 'hover', action: 'enter' }, _mouseHandler).on('mouseleave.menu', 'li', { event: 'hover', action: 'leave' }, _mouseHandler);

		$body.on(jse.libs.template.events.MENU_REPOSITIONED(), _updateCategoryMenu);
	};

	var _unbindHorizontalEventHandlers = function _unbindHorizontalEventHandlers() {
		$list.off(touchEvents.start + '.menu', 'li').off(touchEvents.move + '.menu', 'li').off(touchEvents.end + '.menu', 'li').off('click.menu', 'li').off('mouseenter.menu', 'li').off('mouseleave.menu', 'li');
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {
		// @todo Getting the "touchEvents" config value produces problems in tablet devices.
		touchEvents = jse.core.config.get('touch');
		transition.classOpen = options.openClass;

		_getSelections();
		_resetInitialCss();

		$body.on(jse.libs.template.events.BREAKPOINT(), _breakpointHandler).on(jse.libs.template.events.OPEN_FLYOUT() + ' click ' + touchEvents.end, _closeFlyout);

		if (options.menuType === 'horizontal') {
			_bindHorizontalEventHandlers();
		}

		if (options.menuType === 'vertical') {
			if (options.accordion === true) {
				$this.on('click', 'li', _onClickAccordion);
			}

			// if there is no top header we must create dummy html because other modules will not work correctly
			if ($('#categories').length === 0) {
				var html = '<div id="categories"><div class="navbar-collapse collapse">' + '<nav class="navbar-default navbar-categories hidden"></nav></div></div>';
				$('#header').append(html);
			}
		}

		_breakpointHandler();

		/**
   * Stop the propagation of the events inside this container
   * (Workaround for the "more"-dropdown)
   */
		$this.find('.' + options.ignoreClass).on('mouseleave.menu mouseenter.menu click.menu ' + touchEvents.start + ' ' + touchEvents.end, 'li', function (e) {
			e.stopPropagation();
		});

		if (options.openActive) {
			var $active = $this.find('.active');
			$active.parentsUntil($this, 'li').addClass('open');
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbWVudS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiR3aW5kb3ciLCJ3aW5kb3ciLCIkYm9keSIsIiRsaXN0IiwiJGVudHJpZXMiLCIkbW9yZSIsIiRtb3JlRW50cmllcyIsIiRtZW51RW50cmllcyIsIiRjdXN0b20iLCIkY2F0ZWdvcmllcyIsInRvdWNoRXZlbnRzIiwiY3VycmVudFdpZHRoIiwibW9kZSIsIm1vYmlsZSIsImVudGVyVGltZXIiLCJsZWF2ZVRpbWVyIiwiaW5pdGlhbGl6ZWRQb3MiLCJvbkVudGVyIiwidG91Y2hlU3RhcnRFdmVudCIsInRvdWNoZUVuZEV2ZW50IiwidHJhbnNpdGlvbiIsImlzVG91Y2hEZXZpY2UiLCJNb2Rlcm5penIiLCJ0b3VjaGV2ZW50cyIsIm5hdmlnYXRvciIsInVzZXJBZ2VudCIsInNlYXJjaCIsImRlZmF1bHRzIiwibWVudVR5cGUiLCJ1bmZvbGRMZXZlbCIsImFjY29yZGlvbiIsInNob3dBbGxMaW5rIiwiYnJlYWtwb2ludCIsImVudGVyRGVsYXkiLCJsZWF2ZURlbGF5Iiwid2lkdGhUb2xlcmFuY2UiLCJvcGVuQ2xhc3MiLCJzd2l0Y2hFbGVtZW50UG9zaXRpb24iLCJpZ25vcmVDbGFzcyIsInRvdWNoTW92ZVRvbGVyYW5jZSIsIm9wZW5BY3RpdmUiLCJldmVudHMiLCJkZXNrdG9wIiwib3B0aW9ucyIsImV4dGVuZCIsIl90b3VjaE1vdmVEZXRlY3QiLCJkaWZmIiwiTWF0aCIsImFicyIsImV2ZW50Iiwib3JpZ2luYWxFdmVudCIsInBhZ2VZIiwiX2dldFNlbGVjdGlvbnMiLCJjaGlsZHJlbiIsIm5vdCIsImZpbHRlciIsIl9zZXRJdGVtIiwiJGl0ZW0iLCIkdGFyZ2V0IiwicG9zaXRpb25JZCIsImRvbmUiLCJlYWNoIiwiJHNlbGYiLCJwb3NpdGlvbiIsImJlZm9yZSIsImRldGFjaCIsImFwcGVuZCIsIl9hZGRFbGVtZW50IiwiX3Nob3dFbGVtZW50cyIsIiRlbGVtZW50cyIsIndpZHRoIiwiaGlkZSIsIl9yZW1vdmVFbGVtZW50IiwiX2hpZGVFbGVtZW50cyIsImFkZCIsImZpbmQiLCJyZW1vdmVDbGFzcyIsImlzIiwic2hvdyIsImdldCIsInJldmVyc2UiLCJfaW5pdEVsZW1lbnRTaXplc0FuZFBvc2l0aW9uIiwiaSIsIm91dGVyV2lkdGgiLCJfY2xvc2VNZW51IiwicGFyZW50cyIsImxlbmd0aCIsIl9jbGVhclRpbWVvdXRzIiwiY2xlYXJUaW1lb3V0IiwiX3Jlc2V0SW5pdGlhbENzcyIsImNzcyIsIl9yZXBvc2l0aW9uT3BlbkxheWVyIiwibGlzdFdpZHRoIiwiJG9wZW5MYXllciIsIiRwYXJlbnQiLCJwYXJlbnQiLCJwYXJlbnRQb3NpdGlvbiIsImxlZnQiLCJwYXJlbnRXaWR0aCIsImFkZENsYXNzIiwiX3VwZGF0ZUNhdGVnb3J5TWVudSIsImUiLCJldmVudE5hbWUiLCJjb250YWluZXJXaWR0aCIsImlubmVyV2lkdGgiLCJfc3dpdGNoVG9Nb2JpbGVWaWV3Iiwib3BhY2l0eSIsImhlaWdodCIsImFwcGVuZFRvIiwiX2JpbmRIb3Jpem9udGFsRXZlbnRIYW5kbGVycyIsInRyaWdnZXIiLCJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJNRU5VX1JFUE9TSVRJT05FRCIsIl9zd2l0Y2hUb0Rlc2t0b3BWaWV3IiwiJHRvcG1lbnVDb250ZW50RWxlbWVudHMiLCJfdW5iaW5kSG9yaXpvbnRhbEV2ZW50SGFuZGxlcnMiLCJjbGljayIsInByZXZlbnREZWZhdWx0IiwiX3NldEV2ZW50VHlwZUNsYXNzIiwiY2xhc3NOYW1lIiwiX2JyZWFrcG9pbnRIYW5kbGVyIiwib2xkTW9kZSIsIm5ld01vZGUiLCJyZXNwb25zaXZlIiwiaWQiLCJzd2l0Y2hUb01vYmlsZSIsInVuZGVmaW5lZCIsInN3aXRjaFRvRGVza3RvcCIsIl9vcGVuTWVudSIsInR5cGUiLCJkZWxheSIsIiRzdWJtZW51IiwibGV2ZWwiLCJ2YWxpZFN1Ym1lbnUiLCJwYXJzZUludCIsInN0b3BQcm9wYWdhdGlvbiIsInRvZ2dsZUNsYXNzIiwidmlzaWJsZSIsImhhc0NsYXNzIiwibGVhdmUiLCJhY3Rpb24iLCJpbnRlcmFjdGlvbiIsImlzTW91c2VEb3duIiwic2V0VGltZW91dCIsInBhcmVudHNVbnRpbCIsIlRSQU5TSVRJT05fU1RPUCIsIm9wZW4iLCJvZmYiLCJUUkFOU0lUSU9OX0ZJTklTSEVEIiwib25lIiwiVFJBTlNJVElPTiIsIk9QRU5fRkxZT1VUIiwiX21vdXNlSGFuZGxlciIsInZpZXdwb3J0IiwiaW5BcnJheSIsImNhbGwiLCJhdHRyIiwibG9jYXRpb24iLCJocmVmIiwiX3RvdWNoSGFuZGxlciIsIm9uIiwidGltZXN0YW1wIiwiRGF0ZSIsImdldFRpbWUiLCJ0b3AiLCJzY3JvbGxUb3AiLCJzdGFydCIsIl90b3VjaE1vdmVIYW5kbGVyIiwiX2Nsb3NlRmx5b3V0IiwiZCIsInRhcmdldCIsIl9vbkNsaWNrQWNjb3JkaW9uIiwibW92ZSIsImVuZCIsImluaXQiLCJjb3JlIiwiY29uZmlnIiwiY2xhc3NPcGVuIiwiQlJFQUtQT0lOVCIsImh0bWwiLCIkYWN0aXZlIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7O0FBUUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLE1BREQsRUFHQyxDQUNDRixPQUFPRyxNQUFQLEdBQWdCLGNBRGpCLEVBRUNILE9BQU9HLE1BQVAsR0FBZ0Isa0JBRmpCLEVBR0NILE9BQU9HLE1BQVAsR0FBZ0IsbUJBSGpCLENBSEQsRUFTQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUY7O0FBRUUsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxVQUFVRCxFQUFFRSxNQUFGLENBRFg7QUFBQSxLQUVDQyxRQUFRSCxFQUFFLE1BQUYsQ0FGVDtBQUFBLEtBR0NJLFFBQVEsSUFIVDtBQUFBLEtBSUNDLFdBQVcsSUFKWjtBQUFBLEtBS0NDLFFBQVEsSUFMVDtBQUFBLEtBTUND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LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtZW51LmpzIDIwMTYtMDktMjlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFRoaXMgd2lkZ2V0IGhhbmRsZXMgdGhlIGhvcml6b250YWwgbWVudS9kcm9wZG93biBmdW5jdGlvbmFsaXR5LlxuICpcbiAqIEl0J3MgdXNlZCBmb3IgdGhlIHRvcCBjYXRlZ29yeSBuYXZpZ2F0aW9uLCB0aGUgY2FydCBkcm9wZG93biBvciB0aGUgdG9wIG1lbnUgKGZvciBleGFtcGxlKS4gSXQgaXNcbiAqIGFibGUgdG8gcmUtb3JkZXIgdGhlIG1lbnUgZW50cmllcyB0byBhIHNwZWNpYWwgXCJNb3JlXCIgc3VibWVudSB0byBzYXZlIHNwYWNlIGlmIHRoZSBlbnRyaWVzIGRvbid0XG4gKiBmaXQgaW4gdGhlIGN1cnJlbnQgdmlldy4gSXQncyBhbHNvIGFibGUgdG8gd29yayB3aXRoIGRpZmZlcmVudCBldmVudCB0eXBlcyBmb3Igb3BlbmluZy9jbG9zaW5nIG1lbnVcbiAqIGl0ZW1zIGluIHRoZSBkaWZmZXJlbnQgdmlldyB0eXBlcy5cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQnbWVudScsXG5cblx0W1xuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvZXZlbnRzJyxcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL3Jlc3BvbnNpdmUnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvaW50ZXJhY3Rpb24nXG5cdF0sXG5cblx0ZnVuY3Rpb24oZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHQkd2luZG93ID0gJCh3aW5kb3cpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHQkbGlzdCA9IG51bGwsXG5cdFx0XHQkZW50cmllcyA9IG51bGwsXG5cdFx0XHQkbW9yZSA9IG51bGwsXG5cdFx0XHQkbW9yZUVudHJpZXMgPSBudWxsLFxuXHRcdFx0JG1lbnVFbnRyaWVzID0gbnVsbCxcblx0XHRcdCRjdXN0b20gPSBudWxsLFxuXHRcdFx0JGNhdGVnb3JpZXMgPSBudWxsLFxuXHRcdFx0dG91Y2hFdmVudHMgPSBudWxsLFxuXHRcdFx0Y3VycmVudFdpZHRoID0gbnVsbCxcblx0XHRcdG1vZGUgPSBudWxsLFxuXHRcdFx0bW9iaWxlID0gZmFsc2UsXG5cdFx0XHRlbnRlclRpbWVyID0gbnVsbCxcblx0XHRcdGxlYXZlVGltZXIgPSBudWxsLFxuXHRcdFx0aW5pdGlhbGl6ZWRQb3MgPSBmYWxzZSxcblx0XHRcdG9uRW50ZXIgPSBmYWxzZSxcblx0XHRcdHRvdWNoZVN0YXJ0RXZlbnQgPSBudWxsLFxuXHRcdFx0dG91Y2hlRW5kRXZlbnQgPSBudWxsLFxuXHRcdFx0dHJhbnNpdGlvbiA9IHt9LFxuXHRcdFx0aXNUb3VjaERldmljZSA9IE1vZGVybml6ci50b3VjaGV2ZW50cyB8fCBuYXZpZ2F0b3IudXNlckFnZW50LnNlYXJjaCgvVG91Y2gvaSkgIT09IC0xLFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdC8vIFRoZSBtZW51IHR5cGUgbXVzdCBiZSBlaXRoZXIgJ2hvcml6b250YWwnIG9yICd2ZXJ0aWNhbCdcblx0XHRcdFx0bWVudVR5cGU6ICdob3Jpem9udGFsJyxcblxuXHRcdFx0XHQvLyBWZXJ0aWNhbCBtZW51IG9wdGlvbnMuXG5cdFx0XHRcdHVuZm9sZExldmVsOiAwLFxuXHRcdFx0XHRhY2NvcmRpb246IGZhbHNlLFxuXHRcdFx0XHRzaG93QWxsTGluazogZmFsc2UsXG5cblx0XHRcdFx0Ly8gTWluaW11bSBicmVha3BvaW50IHRvIHN3aXRjaCB0byBtb2JpbGUgdmlld1xuXHRcdFx0XHRicmVha3BvaW50OiA0MCxcblx0XHRcdFx0Ly8gRGVsYXkgaW4gbXMgYWZ0ZXIgYSBtb3VzZWVudGVyIHRoZSBlbGVtZW50IGdldHMgc2hvd25cblx0XHRcdFx0ZW50ZXJEZWxheTogMCxcblx0XHRcdFx0Ly8gRGVsYXkgaW4gbXMgYWZ0ZXIgYSBtb3VzZWxlYXZlIGFuIGVsZW1lbnQgZ2V0cyBoaWRkZW5cblx0XHRcdFx0bGVhdmVEZWxheTogNTAsXG5cdFx0XHRcdC8vIFRvbGVyYW5jZSBpbiBweCB3aGljaCBnZXRzIHN1YnN0cmFjdGVkIGZyb20gdGhlIG5hdi13aWR0aCB0byBwcmV2ZW50IGZsaWNrZXJpbmdcblx0XHRcdFx0d2lkdGhUb2xlcmFuY2U6IDEwLFxuXHRcdFx0XHQvLyBDbGFzcyB0aGF0IGdldHMgYWRkZWQgdG8gYW4gb3BlbmVkIG1lbnUgbGlzdCBpdGVtXG5cdFx0XHRcdG9wZW5DbGFzczogJ29wZW4nLFxuXHRcdFx0XHQvLyBJZiB0cnVlLCBlbGVtZW50cyBnZXQgbW92ZWQgZnJvbS90byB0aGUgbW9yZSBtZW51IGlmIHRoZXJlIGlzbid0IGVub3VnaCBzcGFjZVxuXHRcdFx0XHRzd2l0Y2hFbGVtZW50UG9zaXRpb246IHRydWUsXG5cdFx0XHRcdC8vIElnbm9yZSBtZW51IGZ1bmN0aW9uYWxpdHkgb24gZWxlbWVudHMgaW5zaWRlIHRoaXMgc2VsZWN0aW9uXG5cdFx0XHRcdGlnbm9yZUNsYXNzOiAnaWdub3JlLW1lbnUnLFxuXHRcdFx0XHQvLyBUb2xlcmFuY2UgaW4gcHggd2hpY2ggaXMgYWxsb3dlZCBmb3IgYSBcImNsaWNrXCIgZXZlbnQgb24gdG91Y2hcblx0XHRcdFx0dG91Y2hNb3ZlVG9sZXJhbmNlOiAxMCxcblx0XHRcdFx0Ly8gSWYgdHJ1ZSwgdGhlIGxpIHdpdGggdGhlIGFjdGl2ZSBjbGFzcyBnZXRzIG9wZW5lZFxuXHRcdFx0XHRvcGVuQWN0aXZlOiBmYWxzZSxcblx0XHRcdFx0ZXZlbnRzOiB7XG5cdFx0XHRcdFx0Ly8gRXZlbnQgdHlwZXMgdGhhdCBvcGVuIHRoZSBtZW51cyBpbiBkZXNrdG9wIHZpZXcuXG5cdFx0XHRcdFx0Ly8gUG9zc2libGUgdmFsdWVzOiBbJ2NsaWNrJ107IFsnaG92ZXInXTsgWyd0b3VjaCcsICdob3ZlciddOyBbJ2NsaWNrJywgJ2hvdmVyJ11cblx0XHRcdFx0XHRkZXNrdG9wOiBbJ3RvdWNoJywgJ2hvdmVyJ10sXG5cdFx0XHRcdFx0Ly8gRXZlbnQgdHlwZXMgdGhhdCBvcGVuIHRoZSBtZW51cyBpbiBtb2JpbGUgdmlldy5cblx0XHRcdFx0XHQvLyBQb3NzaWJsZSB2YWx1ZXM6IFsnY2xpY2snXTsgWydob3ZlciddOyBbJ3RvdWNoJywgJ2hvdmVyJ107IFsnY2xpY2snLCAnaG92ZXInXTsgWyd0b3VjaCcsICdjbGljayddXG5cdFx0XHRcdFx0bW9iaWxlOiBbJ3RvdWNoJywgJ2NsaWNrJ11cblx0XHRcdFx0fVxuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cblxuLy8gIyMjIyMjIyMjIyBIRUxQRVIgRlVOQ1RJT05TICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBjYWxjdWxhdGUgdGhlIHRvbGVyYW5jZVxuXHRcdCAqIGJldHdlZW4gdGhlIHRvdWNoc3RhcnQgYW5kIHRvdWNoZW5kIGV2ZW50LlxuXHRcdCAqIElmIHRoZSBtYXggdG9sYXJhbmNlIGlzIGV4Y2VlZGVkIHJldHVybiB0cnVlXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcmV0dXJuICAgICB7Ym9vbGVhbn0gICAgICAgICAgICAgICBJZiB0cnVlIGl0IGlzIGEgbW92ZSBldmVudFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF90b3VjaE1vdmVEZXRlY3QgPSBmdW5jdGlvbigpIHtcblx0XHRcdHRvdWNoZUVuZEV2ZW50ID0gdG91Y2hlRW5kRXZlbnQgfHwgdG91Y2hlU3RhcnRFdmVudDtcblx0XHRcdHZhciBkaWZmID0gTWF0aC5hYnModG91Y2hlRW5kRXZlbnQuZXZlbnQub3JpZ2luYWxFdmVudC5wYWdlWSAtIHRvdWNoZVN0YXJ0RXZlbnQuZXZlbnQub3JpZ2luYWxFdmVudC5wYWdlWSk7XG5cdFx0XHR0b3VjaGVFbmRFdmVudCA9IG51bGw7XG5cdFx0XHRyZXR1cm4gZGlmZiA+IG9wdGlvbnMudG91Y2hNb3ZlVG9sZXJhbmNlO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBVcGRhdGVzIHRoZSBqUXVlcnkgc2VsZWN0aW9uLCBiZWNhdXNlIHRoZVxuXHRcdCAqIGxpc3QgZWxlbWVudHMgY2FuIGJlIG1vdmVkXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0U2VsZWN0aW9ucyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JGxpc3QgPSAkdGhpcy5jaGlsZHJlbigndWwnKTtcblx0XHRcdC8vIEV4Y2x1ZGUgdGhlIFwiLm5hdmJhci10b3BiYXItaXRlbVwiIGVsZW1lbnRzIGJlY2F1c2UgdGhleVxuXHRcdFx0Ly8gYXJlIGNsb25lZCB0byB0aGlzIG1lbnUgYW5kIGFyZSBvbmx5IHNob3duIGluIG1vYmlsZSB2aWV3XG5cdFx0XHQkZW50cmllcyA9ICRsaXN0LmNoaWxkcmVuKCkubm90KCcubmF2YmFyLXRvcGJhci1pdGVtJyk7XG5cdFx0XHQkbW9yZSA9ICRlbnRyaWVzLmZpbHRlcignLmRyb3Bkb3duLW1vcmUnKTtcblx0XHRcdCRtb3JlRW50cmllcyA9ICRtb3JlLmNoaWxkcmVuKCd1bCcpO1xuXHRcdFx0JGN1c3RvbSA9ICRlbnRyaWVzLmZpbHRlcignLmN1c3RvbScpO1xuXHRcdFx0JG1lbnVFbnRyaWVzID0gJGVudHJpZXMubm90KCRtb3JlKTtcblx0XHRcdCRjYXRlZ29yaWVzID0gJG1lbnVFbnRyaWVzLm5vdCgkY3VzdG9tKTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgZGV0YWNoZXMgYW4gZWxlbWVudCBmcm9tIHRoZVxuXHRcdCAqIG1lbnUgYW5kIGF0dGFjaGVzIGl0IHRvIHRoZSBjb3JyZWN0IHBvc2l0aW9uIGF0XG5cdFx0ICogdGhlIHRhcmdldFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAkaXRlbSAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBpdGVtIHRoYXQgZ2V0cyBkZXRhY2hlZCAvIGF0dGFjaGVkXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICR0YXJnZXQgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIHRhcmdldCBjb250YWluZXJcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0SXRlbSA9IGZ1bmN0aW9uKCRpdGVtLCAkdGFyZ2V0KSB7XG5cdFx0XHR2YXIgcG9zaXRpb25JZCA9ICRpdGVtLmRhdGEoJ3Bvc2l0aW9uJyksXG5cdFx0XHRcdGRvbmUgPSBmYWxzZTtcblxuXHRcdFx0Ly8gTG9vayBmb3IgdGhlIGZpcnN0IGl0ZW0gdGhhdCBoYXMgYSBoaWdoZXJcblx0XHRcdC8vIHBvc2l0aW9uSWQgdGhhdCB0aGUgaXRlbSBhbmQgaW5zZXJ0IGl0XG5cdFx0XHQvLyBiZWZvcmUgdGhlIGZvdW5kIGVudHJ5XG5cdFx0XHQkdGFyZ2V0XG5cdFx0XHRcdC5jaGlsZHJlbigpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0XHRwb3NpdGlvbiA9ICRzZWxmLmRhdGEoJ3Bvc2l0aW9uJyk7XG5cblx0XHRcdFx0XHRpZiAocG9zaXRpb24gPiBwb3NpdGlvbklkKSB7XG5cdFx0XHRcdFx0XHQkc2VsZi5iZWZvcmUoJGl0ZW0uZGV0YWNoKCkpO1xuXHRcdFx0XHRcdFx0ZG9uZSA9IHRydWU7XG5cdFx0XHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblxuXHRcdFx0Ly8gQXBwZW5kIHRoZSBpdGVtIGlmIHRoZSBwb3NpdGlvbklkIGhhc1xuXHRcdFx0Ly8gYSBoaWdoZXIgdmFsdWUgYXMgdGhlIGxhc3QgaXRlbSBpbnQgdGhlXG5cdFx0XHQvLyB0YXJnZXRcblx0XHRcdGlmICghZG9uZSkge1xuXHRcdFx0XHQkdGFyZ2V0LmFwcGVuZCgkaXRlbSk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGNoZWNrcyB3aGljaCBlbGVtZW50cyBuZWVkc1xuXHRcdCAqIHRvIGJlIGFkZGVkIHRvIHRoZSBtZW51LiBFdmVyeSBlbGVtZW50IHRoYXQgbmVlZHNcblx0XHQgKiB0byBiZSBhZGRlZCBnZXRzIHBhc3NlZCB0byB0aGUgZnVuY3Rpb25cblx0XHQgKiBcIl9zZXRJdGVtXCJcblx0XHQgKiBAcGFyYW0gICAgICAge2ludGVnZXJ9ICAgICAgIGRpZmYgICAgICAgIEFtb3VudCBvZiBwaXhlbHMgdGhhdCB3ZXJlIGZyZWVcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYWRkRWxlbWVudCA9IGZ1bmN0aW9uKGRpZmYpIHtcblxuXHRcdFx0dmFyIGRvbmUgPSBmYWxzZTtcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBsb29wcyB0aHJvdWdoIHRoZSBlbGVtZW50c1xuXHRcdFx0ICogYW5kIHRyaWVzIHRvIGFkZCB0aGUgZWxlbWVudHMgdG8gdGhlIG1lbnUgaWZcblx0XHRcdCAqIGl0IHdvdWxkIGZpdC5cblx0XHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAkZWxlbWVudHMgICAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgZW50cmllcyBpbnNpZGUgdGhlIG1vcmUtbWVudVxuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0dmFyIF9zaG93RWxlbWVudHMgPSBmdW5jdGlvbigkZWxlbWVudHMpIHtcblx0XHRcdFx0JGVsZW1lbnRzLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHRcdHdpZHRoID0gJHNlbGYuZGF0YSgpLndpZHRoO1xuXG5cdFx0XHRcdFx0aWYgKGRpZmYgPiB3aWR0aCkge1xuXHRcdFx0XHRcdFx0Ly8gQWRkIHRoZSBpdGVtIHRvIHRoZSBtZW51XG5cdFx0XHRcdFx0XHRfc2V0SXRlbSgkc2VsZiwgJGxpc3QpO1xuXHRcdFx0XHRcdFx0ZGlmZiAtPSB3aWR0aDtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0Ly8gVGhlIG5leHQgaXRlbSB3b3VsZG4ndCBmaXQgYW55bW9yZScsXG5cdFx0XHRcdFx0XHQvLyBxdWl0IHRoZSBsb29wXG5cdFx0XHRcdFx0XHRkb25lID0gdHJ1ZTtcblx0XHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0fTtcblxuXHRcdFx0Ly8gVXBkYXRlIHRoZSBzZWxlY3Rpb24gb2YgdGhlIHZpc2libGUgbWVudSBpdGVtcy5cblx0XHRcdF9nZXRTZWxlY3Rpb25zKCk7XG5cblx0XHRcdC8vIEFkZCB0aGUgY29udGVudCBtYW5hZ2VyIGVudHJpZXMgdG8gdGhlIG1lbnUgZmlyc3QuXG5cdFx0XHQvLyBJZiB0aGVyZSBpcyBzdGlsbCBzcGFjZSwgYWRkIHRoZSBcIm5vcm1hbFwiIGNhdGVnb3J5XG5cdFx0XHQvLyBpdGVtcyBhbHNvXG5cdFx0XHRfc2hvd0VsZW1lbnRzKCRtb3JlRW50cmllcy5jaGlsZHJlbignLmN1c3RvbScpKTtcblx0XHRcdGlmICghZG9uZSkge1xuXHRcdFx0XHRfc2hvd0VsZW1lbnRzKCRtb3JlRW50cmllcy5jaGlsZHJlbigpKTtcblx0XHRcdH1cblxuXHRcdFx0Ly8gQ2hlY2sgaWYgdGhlIGl0ZW1zIHN0aWxsIGluIHRoZSBtb3JlIG1lbnVcblx0XHRcdC8vIHdvdWxkIGZpdCBpbnNpZGUgdGhlIG1haW4gbWVudSBpZiB0aGUgbW9yZVxuXHRcdFx0Ly8gbWVudSB3b3VsZCBnZXQgaGlkZGVuXG5cdFx0XHR2YXIgd2lkdGggPSAwO1xuXHRcdFx0JG1vcmVFbnRyaWVzXG5cdFx0XHRcdC5jaGlsZHJlbigpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHdpZHRoICs9ICQodGhpcykuZGF0YSgpLndpZHRoO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0aWYgKHdpZHRoID09PSAwKSB7XG5cdFx0XHRcdCRtb3JlLmhpZGUoKTtcblx0XHRcdH0gZWxzZSBpZiAod2lkdGggPCAoJG1vcmUuZGF0YSgpLndpZHRoICsgZGlmZikpIHtcblx0XHRcdFx0JG1vcmUuaGlkZSgpO1xuXHRcdFx0XHRkaWZmICs9ICRtb3JlLmRhdGEoKS53aWR0aDtcblx0XHRcdFx0X3Nob3dFbGVtZW50cygkbW9yZUVudHJpZXMuY2hpbGRyZW4oKSk7XG5cdFx0XHR9XG5cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgY2hlY2tzIHdoaWNoIGVsZW1lbnRzIG5lZWRzXG5cdFx0ICogdG8gYmUgcmVtb3ZlZCBmcm9tIHRoZSBtZW51LCBzbyB0aGF0IGl0IGZpdHNcblx0XHQgKiBpbnNpZGUgb25lIG1lbnUgbGluZS4gRXZlcnkgZWxlbWVudCB0aGF0IG5lZWRzXG5cdFx0ICogdG8gYmUgcmVtb3ZlZCBnZXRzIHBhc3NlZCB0byB0aGUgZnVuY3Rpb25cblx0XHQgKiBcIl9zZXRJdGVtXCJcblx0XHQgKiBAcGFyYW0gICAgICAge2ludGVnZXJ9ICAgICAgIGRpZmYgICAgICAgIEFtb3VudCBvZiBwaXhlbHMgdGhhdCBuZWVkcyB0byBiZSBzYXZlZFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9yZW1vdmVFbGVtZW50ID0gZnVuY3Rpb24oZGlmZikge1xuXG5cdFx0XHR2YXIgZG9uZSA9IGZhbHNlO1xuXG5cdFx0XHQvKipcblx0XHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IGNvbnRhaW5zIHRoZSBjaGVja1xuXHRcdFx0ICogbG9vcCBmb3IgZGV0ZXJtaW5pbmcgd2hpY2ggZWxlbWVudHNcblx0XHRcdCAqIG5lZWRzIHRvIGJlIHJlbW92ZWRcblx0XHRcdCAqIEBwYXJhbSAgICAgICAgICAge29iamVjdH0gICAgJGVsZW1lbnRzICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIG1lbnUgaXRlbXNcblx0XHRcdCAqIEBwcml2YXRlXG5cdFx0XHQgKi9cblx0XHRcdHZhciBfaGlkZUVsZW1lbnRzID0gZnVuY3Rpb24oJGVsZW1lbnRzKSB7XG5cdFx0XHRcdCRlbGVtZW50cy5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0XHR3aWR0aCA9ICRzZWxmLmRhdGEoKS53aWR0aDtcblxuXHRcdFx0XHRcdC8vIFJlbW92ZSB0aGUgcG9zc2libHkgc2V0IG9wZW4gc3RhdGVcblx0XHRcdFx0XHQkc2VsZlxuXHRcdFx0XHRcdFx0LmZpbHRlcignLicgKyBvcHRpb25zLm9wZW5DbGFzcylcblx0XHRcdFx0XHRcdC5hZGQoJHNlbGYuZmluZCgnLicgKyBvcHRpb25zLm9wZW5DbGFzcykpXG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXG5cdFx0XHRcdFx0Ly8gQWRkIHRoZSBlbnRyeSB0byB0aGUgbW9yZS1tZW51XG5cdFx0XHRcdFx0X3NldEl0ZW0oJHNlbGYsICRtb3JlRW50cmllcyk7XG5cblx0XHRcdFx0XHRkaWZmIC09IHdpZHRoO1xuXG5cdFx0XHRcdFx0aWYgKGRpZmYgPCAwKSB7XG5cdFx0XHRcdFx0XHQvLyBFbm91Z2ggZWxlbWVudHMgYXJlIHJlbW92ZWQsXG5cdFx0XHRcdFx0XHQvLyBxdWl0IHRoZSBsb29wXG5cdFx0XHRcdFx0XHRkb25lID0gdHJ1ZTtcblx0XHRcdFx0XHRcdHJldHVybiBmYWxzZTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0fTtcblxuXHRcdFx0Ly8gVXBkYXRlIHRoZSBzZWxlY3Rpb24gb2YgdGhlIHZpc2libGUgbWVudSBpdGVtc1xuXHRcdFx0X2dldFNlbGVjdGlvbnMoKTtcblxuXHRcdFx0Ly8gQWRkIHRoZSB3aWR0aCBvZiB0aGUgbW9yZSBlbnRyeSBpZiBpdCdzIG5vdFxuXHRcdFx0Ly8gdmlzaWJsZSwgYmVjYXVzZSBpdCB3aWxsIGdldCBzaG93biBkdXJpbmcgdGhpc1xuXHRcdFx0Ly8gZnVuY3Rpb24gY2FsbFxuXHRcdFx0aWYgKCRtb3JlLmlzKCc6aGlkZGVuJykpIHtcblx0XHRcdFx0ZGlmZiArPSAkbW9yZS5kYXRhKCkud2lkdGg7XG5cdFx0XHRcdCRtb3JlLnJlbW92ZUNsYXNzKCdzdHlsZScpO1xuXHRcdFx0XHQkbW9yZS5zaG93KCk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIEZpcnN0IHJlbW92ZSBcIm5vcm1hbFwiIGNhdGVnb3J5IGVudHJpZXMuIElmIHRoYXRcblx0XHRcdC8vIGlzbid0IGVub3VnaCByZW1vdmUgdGhlIGNvbnRlbnQgbWFuYWdlciBlbnRyaWVzIGFsc29cblx0XHRcdF9oaWRlRWxlbWVudHMoJCgkY2F0ZWdvcmllcy5nZXQoKS5yZXZlcnNlKCkpKTtcblx0XHRcdGlmICghZG9uZSkge1xuXHRcdFx0XHRfaGlkZUVsZW1lbnRzKCQoJGN1c3RvbS5nZXQoKS5yZXZlcnNlKCkpKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogU2V0cyBhIGRhdGEgYXR0cmlidXRlIHRvIHRoZSBtZW51IGl0ZW1zXG5cdFx0ICogdGhhdCBjb250YWlucyB0aGUgd2lkdGggb2YgdGhlIGVsZW1lbnRzLlxuXHRcdCAqIFRoaXMgaXMgbmVlZGVkIGJlY2F1c2UgaWYgaXQgaXMgZGlzcGxheVxuXHRcdCAqIG5vbmUgdGhlIGRldGVjdGVkIHdpdGggd2lsbCBiZSB6ZXJvLiBJdFxuXHRcdCAqIHNldHMgcG9zaXRpb24gaWQgYWxzby5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfaW5pdEVsZW1lbnRTaXplc0FuZFBvc2l0aW9uID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkZW50cmllcy5lYWNoKGZ1bmN0aW9uKGkpIHtcblx0XHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0XHR3aWR0aCA9ICRzZWxmLm91dGVyV2lkdGgoKTtcblxuXHRcdFx0XHQkc2VsZi5kYXRhKHt3aWR0aDogd2lkdGgsIHBvc2l0aW9uOiBpfSk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRvIGNsb3NlIGFsbCBtZW51IGVudHJpZXMuXG5cdFx0ICogTmVlZGVkIGZvciB0aGUgZGVza3RvcCA8LT4gbW9iaWxlIHZpZXdcblx0XHQgKiBjaGFuZ2UsIG1vc3RseS5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2xvc2VNZW51ID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkdGhpcy5maW5kKCdsaS4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICgkKHRoaXMpLnBhcmVudHMoJy5uYXZiYXItY2F0ZWdvcmllcy1sZWZ0JykubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCQodGhpcykucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXHRcdFx0fSk7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byBjbGVhciBhbGwgcGVuZGluZ1xuXHRcdCAqIGZ1bmN0aW9uc1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9jbGVhclRpbWVvdXRzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRlbnRlclRpbWVyID0gZW50ZXJUaW1lciA/IGNsZWFyVGltZW91dChlbnRlclRpbWVyKSA6IG51bGw7XG5cdFx0XHRsZWF2ZVRpbWVyID0gbGVhdmVUaW1lciA/IGNsZWFyVGltZW91dChsZWF2ZVRpbWVyKSA6IG51bGw7XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0byByZXNldCB0aGUgY3NzIG9mIHRoZSBtZW51LlxuXHRcdCAqIFRoaXMgaXMgbmVlZGVkIHRvIHJlbW92ZSB0aGUgb3ZlcmZsb3cgJiBoZWlnaHRcblx0XHQgKiBzZXR0aW5ncyBvZiB0aGUgbWVudSBvZiB0aGUgY3NzIGZpbGUuIFRoZVxuXHRcdCAqIGRpcmVjdGl2ZXMgd2VyZSBzZXQgdG8gcHJldmVudCBmbGlja2VyaW5nIG9uIHBhZ2Vcblx0XHQgKiBsb2FkXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3Jlc2V0SW5pdGlhbENzcyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JHRoaXMuY3NzKHtcblx0XHRcdFx0ICAgICAgICAgICdvdmVyZmxvdyc6ICd2aXNpYmxlJyxcblx0XHRcdFx0ICAgICAgICAgICdoZWlnaHQnOiAnYXV0bydcblx0XHRcdCAgICAgICAgICB9KTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRvIHNldCBwb3NpdGlvbmluZyBjbGFzc2VzXG5cdFx0ICogdG8gdGhlIG9wZW5kIGZseW91dC4gVGhpcyBpcyBuZWVkZWQgdG8ga2VlcFxuXHRcdCAqIHRoZSBmbHlvdXQgaW5zaWRlIHRoZSBib3VuZGFyaWVzIG9mIHRoZSBuYXZpZ2F0aW9uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3JlcG9zaXRpb25PcGVuTGF5ZXIgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBsaXN0V2lkdGggPSAkbGlzdC53aWR0aCgpLFxuXHRcdFx0XHQkb3BlbkxheWVyID0gJGVudHJpZXNcblx0XHRcdFx0XHQuZmlsdGVyKCcuJyArIG9wdGlvbnMub3BlbkNsYXNzKVxuXHRcdFx0XHRcdC5jaGlsZHJlbigndWwnKTtcblxuXHRcdFx0JG9wZW5MYXllci5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdCRwYXJlbnQgPSAkc2VsZi5wYXJlbnQoKTtcblxuXHRcdFx0XHQvLyBSZXNldCB0aGUgY2xhc3NlcyB0byBwcmV2ZW50IHdyb25nIGNhbGN1bGF0aW9uIGR1ZSB0byBzcGVjaWFsIHN0eWxlc1xuXHRcdFx0XHQkcGFyZW50LnJlbW92ZUNsYXNzKCdmbHlvdXQtcmlnaHQgZmx5b3V0LWxlZnQgZmx5b3V0LWNlbnRlciBmbHlvdXQtd29udC1maXQnKTtcblxuXHRcdFx0XHR2YXIgd2lkdGggPSAkc2VsZi5vdXRlcldpZHRoKCksXG5cdFx0XHRcdFx0cGFyZW50UG9zaXRpb24gPSAkcGFyZW50LnBvc2l0aW9uKCkubGVmdCxcblx0XHRcdFx0XHRwYXJlbnRXaWR0aCA9ICRwYXJlbnQub3V0ZXJXaWR0aCgpO1xuXG5cdFx0XHRcdC8vIENoZWNrIHdpdGNoIGNsYXNzIG5lZWRzIHRvIGJlIHNldFxuXHRcdFx0XHRpZiAobGlzdFdpZHRoID4gcGFyZW50UG9zaXRpb24gKyB3aWR0aCkge1xuXHRcdFx0XHRcdCRwYXJlbnQuYWRkQ2xhc3MoJ2ZseW91dC1yaWdodCcpO1xuXHRcdFx0XHR9IGVsc2UgaWYgKHBhcmVudFBvc2l0aW9uICsgcGFyZW50V2lkdGggLSB3aWR0aCA+IDApIHtcblx0XHRcdFx0XHQkcGFyZW50LmFkZENsYXNzKCdmbHlvdXQtbGVmdCcpO1xuXHRcdFx0XHR9IGVsc2UgaWYgKHdpZHRoIDwgbGlzdFdpZHRoKSB7XG5cdFx0XHRcdFx0JHBhcmVudC5hZGRDbGFzcygnZmx5b3V0LWNlbnRlcicpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRwYXJlbnQuYWRkQ2xhc3MoJ2ZseW91dC13b250LWZpdCcpO1xuXHRcdFx0XHR9XG5cblx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gY2FsY3VsYXRlIHRoZSBkaWZmZXJlbmNlIGJldHdlZW5cblx0XHQgKiB0aGUgc2l6ZSBvZiB0aGUgdmlzaWJsZSBlbGVtZW50cyBpbiB0aGUgbWVudSBhbmQgdGhlXG5cdFx0ICogY29udGFpbmVyIHNpemUuIElmIHRoZXJlIGlzIHNwYWNlLCBpdCBjYWxscyB0aGUgZnVuY3Rpb25cblx0XHQgKiB0byBhY3RpdmF0ZSBhbiBtZW51IGVudHJ5IGVsc2UgaXQgY2FsbHMgdGhlIGZ1bmN0aW9uIHRvXG5cdFx0ICogZGVhY3RpdmF0ZSBhIG1lbnUgZW50cnlcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcGFyYW0gICAgICAge3N0cmluZ30gICAgZXZlbnROYW1lIEV2ZW50IG5hbWUgcGFyYW1ldGVyIG9mIHRoZSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfdXBkYXRlQ2F0ZWdvcnlNZW51ID0gZnVuY3Rpb24oZSwgZXZlbnROYW1lKSB7XG5cdFx0XHR2YXIgY29udGFpbmVyV2lkdGggPSAkdGhpcy5pbm5lcldpZHRoKCkgLSBvcHRpb25zLndpZHRoVG9sZXJhbmNlLFxuXHRcdFx0XHR3aWR0aCA9IDA7XG5cblx0XHRcdC8vIENoZWNrIGlmIHRoZSBjb250YWluZXIgd2lkdGggaGFzIGNoYW5nZWQgc2luY2UgbGFzdCBjYWxsXG5cdFx0XHRpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ2hvcml6b250YWwnIFxuXHRcdFx0XHQmJiAoY3VycmVudFdpZHRoICE9PSBjb250YWluZXJXaWR0aCB8fCBldmVudE5hbWUgPT09ICdzd2l0Y2hlZFRvRGVza3RvcCcpKSB7XG5cblx0XHRcdFx0JGxpc3Rcblx0XHRcdFx0XHQuY2hpbGRyZW4oJzp2aXNpYmxlJylcblx0XHRcdFx0XHQuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdHdpZHRoICs9ICQodGhpcykuZGF0YSgnd2lkdGgnKTtcblx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHQvLyBBZGQgb3IgcmVtb3ZlIGVsZW1lbnRzIGRlcGVuZGluZyBvbiB0aGUgc2l6ZSBvZiB0aGVcblx0XHRcdFx0Ly8gdmlzaWJsZSBlbGVtZW50c1xuXHRcdFx0XHRpZiAoY29udGFpbmVyV2lkdGggPCB3aWR0aCkge1xuXHRcdFx0XHRcdF9yZW1vdmVFbGVtZW50KHdpZHRoIC0gY29udGFpbmVyV2lkdGgpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdF9hZGRFbGVtZW50KGNvbnRhaW5lcldpZHRoIC0gd2lkdGgpO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0X3JlcG9zaXRpb25PcGVuTGF5ZXIoKTtcblxuXHRcdFx0XHRjdXJyZW50V2lkdGggPSBjb250YWluZXJXaWR0aDtcblx0XHRcdH1cblxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gc3dpdGNoIHRvIHRoZSBtb2JpbGVcblx0XHQgKiBtb2RlIG9mIHRoZSBtZW51LlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zd2l0Y2hUb01vYmlsZVZpZXcgPSBmdW5jdGlvbigpIHtcblx0XHRcdC8vIFJlc2V0IHRoZSBjdXJyZW50IHdpZHRoIHNvIHRoYXRcblx0XHRcdC8vIHRoZSBcIl91cGRhdGVDYXRlZ29yeU1lbnVcIiB3aWxsXG5cdFx0XHQvLyBwZXJmb3JtIGNvcnJlY3RseSBvbiB0aGUgbmV4dCB2aWV3XG5cdFx0XHQvLyBjaGFuZ2UgdG8gZGVza3RvcFxuXHRcdFx0Y3VycmVudFdpZHRoID0gLTE7XG5cdFx0XHRfYWRkRWxlbWVudCg5OTk5OTk5OSk7XG5cblx0XHRcdC8vIFVzZSB0aGUgdmVydGljYWwgbWVudSBvbiBtb2JpbGUgdmlldy5cblx0XHRcdGlmIChvcHRpb25zLm1lbnVUeXBlID09PSAndmVydGljYWwnKSB7XG5cdFx0XHRcdC8vIGZpeGVzIGRpc3BsYXkgaG9yaXpvbnRhbCBtZW51IGFmdGVyIGEgc3dpdGNoIHRvIG1vYmlsZSBhbmQgYmFjayB0byBkZXNrdG9wIGlzIHBlcmZvcm1lZFxuXHRcdFx0XHRpZiAoJCgnI2NhdGVnb3JpZXMgbmF2Lm5hdmJhci1kZWZhdWx0OmZpcnN0Jykubm90KCcubmF2LWNhdGVnb3JpZXMtbGVmdCcpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0XHQkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQ6Zmlyc3QnKS5jc3Moe1xuXHRcdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wYWNpdHk6IDAsXG5cdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaGVpZ2h0OiAwXG5cdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0pXG5cdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAuY2hpbGRyZW4oKS5oaWRlKCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdC8vIG1vdmUgdG9wbWVudS1jb250ZW50IGl0ZW1zIGZyb20gaG9yaXpvbnRhbCBtZW51IHRvIHZlcnRpY2FsIG1lbnVcblx0XHRcdFx0JHRoaXNcblx0XHRcdFx0XHQuZmluZCgndWwubGV2ZWwtMSBsaS5uYXZiYXItdG9wYmFyLWl0ZW06Zmlyc3QnKVxuXHRcdFx0XHRcdC5iZWZvcmUoJCgnI2NhdGVnb3JpZXMgbmF2Lm5hdmJhci1kZWZhdWx0IGxpLnRvcG1lbnUtY29udGVudCcpLmRldGFjaCgpKTtcblx0XHRcdFx0XG5cdFx0XHRcdCR0aGlzLmFwcGVuZFRvKCcjY2F0ZWdvcmllcyA+IC5uYXZiYXItY29sbGFwc2UnKTtcblx0XHRcdFx0JHRoaXMuYWRkQ2xhc3MoJ25hdmJhci1kZWZhdWx0IG5hdmJhci1jYXRlZ29yaWVzJyk7XG5cdFx0XHRcdCR0aGlzLmZpbmQoJ3VsLmxldmVsLTEnKS5hZGRDbGFzcygnbmF2YmFyLW5hdicpO1xuXHRcdFx0XHQkdGhpcy5maW5kKCcubmF2YmFyLXRvcGJhci1pdGVtJykubm90KCcudG9wYmFyLXNlYXJjaCcpLnNob3coKTtcblx0XHRcdFx0XG5cdFx0XHRcdF9iaW5kSG9yaXpvbnRhbEV2ZW50SGFuZGxlcnMoKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRib2R5LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLk1FTlVfUkVQT1NJVElPTkVEKCksIFsnc3dpdGNoZWRUb01vYmlsZSddKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRvIHN3aXRjaCB0byB0aGUgZGVza3RvcFxuXHRcdCAqIG1vZGUgb2YgdGhlIG1lbnUuIEFkZGl0aW9uYWxseSwgaW4gY2FzZSB0aGF0XG5cdFx0ICogdGhlIGRlc2t0b3AgbW9kZSBpcyBzaG93biBmb3IgdGhlIGZpcnN0IHRpbWVcblx0XHQgKiBzZXQgdGhlIHBvc2l0aW9uIGFuZCB3aWR0aCBvZiB0aGUgZWxlbWVudHNcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc3dpdGNoVG9EZXNrdG9wVmlldyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gUmV2ZXJ0IGFsbCB0aGUgY2hhbmdlcyBtYWRlIGR1cmluZyB0aGUgc3dpdGNoIHRvIG1vYmlsZS5cblx0XHRcdGlmIChvcHRpb25zLm1lbnVUeXBlID09PSAndmVydGljYWwnKSB7XG5cdFx0XHRcdC8vIGZpeGVzIGRpc3BsYXkgaG9yaXpvbnRhbCBtZW51IGFmdGVyIGEgc3dpdGNoIHRvIG1vYmlsZSBhbmQgYmFjayB0byBkZXNrdG9wIGlzIHBlcmZvcm1lZFxuXHRcdFx0XHRpZiAoJCgnI2NhdGVnb3JpZXMgbmF2Lm5hdmJhci1kZWZhdWx0OmZpcnN0Jykubm90KCcubmF2LWNhdGVnb3JpZXMtbGVmdCcpLmxlbmd0aCA+IDApIHtcblx0XHRcdFx0XHQkKCcjY2F0ZWdvcmllcyBuYXYubmF2YmFyLWRlZmF1bHQ6Zmlyc3QnKS5jc3Moe1xuXHRcdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wYWNpdHk6IDEsXG5cdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgaGVpZ2h0OiAnYXV0bydcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSlcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5jaGlsZHJlbigpLnNob3coKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0Ly8gbW92ZSB0b3BtZW51LWNvbnRlbnQgaXRlbXMgYmFjayB0byBob3Jpem9udGFsIG1lbnVcblx0XHRcdFx0dmFyICR0b3BtZW51Q29udGVudEVsZW1lbnRzID0gJHRoaXMuZmluZCgnbGkudG9wbWVudS1jb250ZW50JykuZGV0YWNoKCk7XG5cdFx0XHRcdCQoJyNjYXRlZ29yaWVzIG5hdi5uYXZiYXItZGVmYXVsdCB1bC5sZXZlbC0xOmZpcnN0JykuYXBwZW5kKCR0b3BtZW51Q29udGVudEVsZW1lbnRzKTtcblx0XHRcdFx0XG5cdFx0XHRcdCR0aGlzLmFwcGVuZFRvKCcuYm94LWNhdGVnb3JpZXMnKTtcblx0XHRcdFx0JHRoaXMucmVtb3ZlQ2xhc3MoJ25hdmJhci1kZWZhdWx0IG5hdmJhci1jYXRlZ29yaWVzJyk7XG5cdFx0XHRcdCR0aGlzLmZpbmQoJ3VsLmxldmVsLTEnKS5yZW1vdmVDbGFzcygnbmF2YmFyLW5hdicpO1xuXHRcdFx0XHQkdGhpcy5maW5kKCcubmF2YmFyLXRvcGJhci1pdGVtJykuaGlkZSgpO1xuXHRcdFx0XHRfdW5iaW5kSG9yaXpvbnRhbEV2ZW50SGFuZGxlcnMoKTtcblx0XHRcdFx0XG5cdFx0XHRcdCRib2R5LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLk1FTlVfUkVQT1NJVElPTkVEKCksIFsnc3dpdGNoZWRUb0Rlc2t0b3AnXSk7XG5cdFx0XHR9XG5cblxuXHRcdFx0aWYgKCFpbml0aWFsaXplZFBvcykge1xuXHRcdFx0XHRfaW5pdEVsZW1lbnRTaXplc0FuZFBvc2l0aW9uKCk7XG5cdFx0XHRcdGluaXRpYWxpemVkUG9zID0gdHJ1ZTtcblx0XHRcdH1cblxuXHRcdFx0aWYgKG9wdGlvbnMubWVudVR5cGUgPT09ICdob3Jpem9udGFsJykge1xuXHRcdFx0XHRfdXBkYXRlQ2F0ZWdvcnlNZW51KCk7XG5cblx0XHRcdFx0aWYgKGlzVG91Y2hEZXZpY2UpIHtcblx0XHRcdFx0XHQkbGlzdC5maW5kKCcuZW50ZXItY2F0ZWdvcnknKS5zaG93KCk7XG5cdFx0XHRcdFx0JGxpc3QuZmluZCgnLmRyb3Bkb3duID4gYScpLmNsaWNrKGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gYWRkIHRoZSBjbGFzcyB0byB0aGUgbGktZWxlbWVudFxuXHRcdCAqIGRlcGVuZGluZyBvbiB0aGUgb3BlbiBldmVudC4gVGhpcyBjYW4gYmUgYSBcInRvdWNoXCJcblx0XHQgKiBvciBhIFwibW91c2VcIiBjbGFzc1xuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAkdGFyZ2V0ICAgICAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgbGktZWxlbWVudFxuXHRcdCAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICBjbGFzc05hbWUgICAgICAgTmFtZSBvZiB0aGUgY2xhc3MgdGhhdCBnZXRzIGFkZGVkXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3NldEV2ZW50VHlwZUNsYXNzID0gZnVuY3Rpb24oJHRhcmdldCwgY2xhc3NOYW1lKSB7XG5cdFx0XHQkdGFyZ2V0XG5cdFx0XHRcdC5yZW1vdmVDbGFzcygndG91Y2ggbW91c2UnKVxuXHRcdFx0XHQuYWRkQ2xhc3MoY2xhc3NOYW1lIHx8ICcnKTtcblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyMgTUFJTiBGVU5DVElPTkFMSVRZICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEZ1bmN0aW9uIHRoYXQgZ2V0cyBjYWxsZWQgYnkgdGhlIGJyZWFrcG9pbnQgdHJpZ2dlclxuXHRcdCAqICh3aGljaCBpcyBmaXJlZCBvbiBicm93c2VyIHJlc2l6ZSkuIEl0IGNoZWNrcyBmb3Jcblx0XHQgKiBDU1MgdmlldyBjaGFuZ2VzIGFuZCByZWNvbmZpZ3VyZXMgdGhlIHRoZSBKUyBiZWhhdmlvdXJcblx0XHQgKiBvZiB0aGUgbWVudSBpbiB0aGF0IGNhc2Vcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfYnJlYWtwb2ludEhhbmRsZXIgPSBmdW5jdGlvbigpIHtcblxuXHRcdFx0Ly8gR2V0IHRoZSBjdXJyZW50IHZpZXdtb2RlXG5cdFx0XHR2YXIgb2xkTW9kZSA9IG1vZGUgfHwge30sXG5cdFx0XHRcdG5ld01vZGUgPSBqc2UubGlicy50ZW1wbGF0ZS5yZXNwb25zaXZlLmJyZWFrcG9pbnQoKTtcblxuXHRcdFx0Ly8gT25seSBkbyBzb21ldGhpbmcgaWYgdGhlIHZpZXcgd2FzIGNoYW5nZWRcblx0XHRcdGlmIChuZXdNb2RlLmlkICE9PSBvbGRNb2RlLmlkKSB7XG5cblx0XHRcdFx0Ly8gQ2hlY2sgaWYgYSB2aWV3IGNoYW5nZSBiZXR3ZWVuIG1vYmlsZSBhbmQgZGVza3RvcCB2aWV3IHdhcyBtYWRlXG5cdFx0XHRcdHZhciBzd2l0Y2hUb01vYmlsZSA9IChuZXdNb2RlLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludCAmJiAoIW1vYmlsZSB8fCBvbGRNb2RlLmlkID09PSB1bmRlZmluZWQpKSxcblx0XHRcdFx0XHRzd2l0Y2hUb0Rlc2t0b3AgPSAobmV3TW9kZS5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCAmJiAobW9iaWxlIHx8IG9sZE1vZGUuaWQgPT09IHVuZGVmaW5lZCkpO1xuXG5cdFx0XHRcdC8vIFN0b3JlIHRoZSBuZXcgdmlldyBzZXR0aW5nc1xuXHRcdFx0XHRtb2JpbGUgPSBuZXdNb2RlLmlkIDw9IG9wdGlvbnMuYnJlYWtwb2ludDtcblx0XHRcdFx0bW9kZSA9ICQuZXh0ZW5kKHt9LCBuZXdNb2RlKTtcblxuXHRcdFx0XHRpZiAoc3dpdGNoVG9Nb2JpbGUgfHwgc3dpdGNoVG9EZXNrdG9wKSB7XG5cdFx0XHRcdFx0X2NsZWFyVGltZW91dHMoKTtcblx0XHRcdFx0XHRpZiAob3B0aW9ucy5tZW51VHlwZSAhPT0gJ3ZlcnRpY2FsJykge1xuXHRcdFx0XHRcdFx0X2Nsb3NlTWVudSgpO1xuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIENoYW5nZSB0aGUgdmlzaWJpbGl0eSBvZiB0aGUgbWVudSBpdGVtc1xuXHRcdFx0XHRcdC8vIGluIGNhc2Ugb2YgZGVza3RvcCA8LT4gbW9iaWxlIHZpZXcgY2hhbmdlXG5cdFx0XHRcdFx0aWYgKG9wdGlvbnMuc3dpdGNoRWxlbWVudFBvc2l0aW9uKSB7XG5cdFx0XHRcdFx0XHRpZiAoc3dpdGNoVG9Nb2JpbGUpIHtcblx0XHRcdFx0XHRcdFx0X3N3aXRjaFRvTW9iaWxlVmlldygpO1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0X3N3aXRjaFRvRGVza3RvcFZpZXcoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0X3JlcG9zaXRpb25PcGVuTGF5ZXIoKTtcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0fSBlbHNlIGlmICghbW9iaWxlICYmIG9wdGlvbnMuc3dpdGNoRWxlbWVudFBvc2l0aW9uKSB7XG5cdFx0XHRcdFx0Ly8gVXBkYXRlIHRoZSB2aXNpYmlsaXR5IG9mIHRoZSBtZW51IGl0ZW1zXG5cdFx0XHRcdFx0Ly8gaWYgdGhlIHZpZXcgY2hhbmdlIHdhcyBkZXNrdG9wIHRvIGRlc2t0b3Agb25seVxuXHRcdFx0XHRcdF91cGRhdGVDYXRlZ29yeU1lbnUoKTtcblx0XHRcdFx0fSBlbHNlIGlmICghbW9iaWxlKSB7XG5cdFx0XHRcdFx0X3JlcG9zaXRpb25PcGVuTGF5ZXIoKTtcblx0XHRcdFx0fVxuXG5cdFx0XHR9XG5cblx0XHR9O1xuXG5cbi8vICMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIENoYW5nZXMgdGhlIGVwYW5kIC8gY29sbGFwc2Ugc3RhdGUgb2YgdGhlIG1lbnUsXG5cdFx0ICogaWYgdGhlcmUgaXMgYW4gc3VibWVudS4gSW4gdGhlIG90aGVyIGNhc2UgaXRcblx0XHQgKiB3aWxsIGxldCBleGVjdXRlIHRoZSBkZWZhdWx0IGFjdGlvbiAobW9zdCB0aW1lc1xuXHRcdCAqIHRoZSBleGVjdXRpb24gb2YgYSBsaW5rKVxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHBhcmFtIHtzdHJpbmd9ICBtb2RlICAgIFRoZSBjdXJyZW50IHZpZXcgbW9kZSAoY2FuIGJlIFwibW9iaWxlXCIgb3IgXCJkZXNrdG9wXCJcblx0XHQgKiBAcGFyYW0ge2ludGVnZXJ9IGRlbGF5ICAgQ3VzdG9tIGRlbGF5IChpbiBtcykgZm9yIG9wZW5pbmcgY2xvc2luZyB0aGUgbWVudSAobmVlZGVkIGZvciBjbGljayAvIHRvdWNoIGV2ZW50cylcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfb3Blbk1lbnUgPSBmdW5jdGlvbihlLCB0eXBlLCBkZWxheSkge1xuXG5cdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHQkc3VibWVudSA9ICRzZWxmLmNoaWxkcmVuKCd1bCcpLFxuXHRcdFx0XHRsZW5ndGggPSAkc3VibWVudS5sZW5ndGgsXG5cdFx0XHRcdGxldmVsID0gKCRzdWJtZW51Lmxlbmd0aCkgPyAoJHN1Ym1lbnUuZGF0YSgnbGV2ZWwnKSB8fCAnMCcpIDogOTksXG5cdFx0XHRcdHZhbGlkU3VibWVudSA9IChwYXJzZUludChsZXZlbCwgMTApIDw9IDIgJiYgbW9kZS5pZCA+IG9wdGlvbnMuYnJlYWtwb2ludCkgfHwgbW9kZS5pZFxuXHRcdFx0XHRcdDw9IG9wdGlvbnMuYnJlYWtwb2ludDtcblxuXHRcdFx0aWYgKHR5cGUgPT09ICdtb2JpbGUnKSB7XG5cdFx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIE9ubHkgY2hhbmdlIHRoZSBzdGF0ZSBpZiB0aGVyZSBpc1xuXHRcdFx0Ly8gYSBzdWJtZW51XG5cdFx0XHRpZiAobGVuZ3RoICYmIHZhbGlkU3VibWVudSkge1xuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRcdFx0aWYgKHR5cGUgPT09ICdtb2JpbGUnKSB7XG5cdFx0XHRcdFx0Ly8gU2ltcGx5IHRvZ2dsZSB0aGUgb3BlbkNsYXNzIGluIG1vYmlsZSBtb2RlXG5cdFx0XHRcdFx0JHNlbGYudG9nZ2xlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdC8vIFBlcmZvcm0gdGhlIGVsc2UgY2FzZSBmb3IgdGhlIGRlc2t0b3Agdmlld1xuXG5cdFx0XHRcdFx0dmFyIHZpc2libGUgPSAkc2VsZi5oYXNDbGFzcyhvcHRpb25zLm9wZW5DbGFzcyksXG5cdFx0XHRcdFx0XHRsZWF2ZSA9ICRzZWxmLmhhc0NsYXNzKCdsZWF2ZScpLFxuXHRcdFx0XHRcdFx0YWN0aW9uID0gKGUuZGF0YSAmJiBlLmRhdGEuYWN0aW9uKSA/IGUuZGF0YS5hY3Rpb24gOlxuXHRcdFx0XHRcdFx0ICAgICAgICAgKHZpc2libGUgJiYgbGVhdmUpID8gJ2VudGVyJyA6XG5cdFx0XHRcdFx0XHQgICAgICAgICB2aXNpYmxlID8gJ2xlYXZlJyA6ICdlbnRlcic7XG5cblx0XHRcdFx0XHQvLyBEZXBlbmRpbmcgb24gdGhlIHZpc2liaWxpdHkgYW5kIHRoZSBldmVudC1hY3Rpb24tcGFyYW1ldGVyXG5cdFx0XHRcdFx0Ly8gdGhlIHN1Ym1lbnUgZ2V0cyBvcGVuZWQgb3IgY2xvc2VkXG5cdFx0XHRcdFx0c3dpdGNoIChhY3Rpb24pIHtcblx0XHRcdFx0XHRcdGNhc2UgJ2VudGVyJzpcblx0XHRcdFx0XHRcdFx0aWYgKCFvbkVudGVyICYmICFqc2UubGlicy50ZW1wbGF0ZS5pbnRlcmFjdGlvbi5pc01vdXNlRG93bigpKSB7XG5cdFx0XHRcdFx0XHRcdFx0b25FbnRlciA9IHRydWU7XG5cdFx0XHRcdFx0XHRcdFx0Ly8gU2V0IGEgdGltZXIgZm9yIG9wZW5pbmcgaWYgdGhlIHN1Ym1lbnUgKGRlbGF5ZWQgb3BlbmluZylcblx0XHRcdFx0XHRcdFx0XHRfY2xlYXJUaW1lb3V0cygpO1xuXHRcdFx0XHRcdFx0XHRcdGVudGVyVGltZXIgPSBzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXG5cdFx0XHRcdFx0XHRcdFx0XHQvLyBSZW1vdmUgYWxsIG9wZW5DbGFzcy1jbGFzc2VzIGZyb20gdGhlXG5cdFx0XHRcdFx0XHRcdFx0XHQvLyBtZW51IGV4Y2VwdCB0aGUgZWxlbWVudCB0byBvcGVuIGFuZCBpdCdzIHBhcmVudHNcblx0XHRcdFx0XHRcdFx0XHRcdCRsaXN0XG5cdFx0XHRcdFx0XHRcdFx0XHRcdC5maW5kKCcuJyArIG9wdGlvbnMub3BlbkNsYXNzKVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQubm90KCRzZWxmKVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQubm90KCRzZWxmLnBhcmVudHNVbnRpbCgkdGhpcywgJy4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpKVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTl9TVE9QKCksIFtdKVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXG5cdFx0XHRcdFx0XHRcdFx0XHQkbGlzdFxuXHRcdFx0XHRcdFx0XHRcdFx0XHQuZmluZCgnLmxlYXZlJylcblx0XHRcdFx0XHRcdFx0XHRcdFx0LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT05fU1RPUCgpLCBbXSlcblx0XHRcdFx0XHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCdsZWF2ZScpO1xuXG5cdFx0XHRcdFx0XHRcdFx0XHQvLyBPcGVuIHRoZSBzdWJtZW51XG5cdFx0XHRcdFx0XHRcdFx0XHR0cmFuc2l0aW9uLm9wZW4gPSB0cnVlO1xuXG5cdFx0XHRcdFx0XHRcdFx0XHQvLyBTZXQgYW5kIHVuc2V0IHRoZSBcIm9uRW50ZXJcIiB0byBwcmV2ZW50XG5cdFx0XHRcdFx0XHRcdFx0XHQvLyBjbG9zaW5nIHRoZSBtZW51IGltbWVkaWF0ZWx5IGFmdGVyIG9wZW5pbmcgaWZcblx0XHRcdFx0XHRcdFx0XHRcdC8vIHRoZSBjdXJzb3IgaXMgYXQgYW4gcGxhY2Ugb3ZlciB0aGUgb3BlbmluZyBtZW51XG5cdFx0XHRcdFx0XHRcdFx0XHQvLyAodGhpcyBjYW4gaGFwcGVuIGlmIG90aGVyIGNvbXBvbmVudHMgdHJpZ2dlciB0aGVcblx0XHRcdFx0XHRcdFx0XHRcdC8vIG9wZW4gZXZlbnQpXG5cdFx0XHRcdFx0XHRcdFx0XHQkc2VsZlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQub2ZmKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5UUkFOU0lUSU9OX0ZJTklTSEVEKCkpXG5cdFx0XHRcdFx0XHRcdFx0XHRcdC5vbmUoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdFx0b25FbnRlciA9IGZhbHNlO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKVxuXHRcdFx0XHRcdFx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuT1BFTl9GTFlPVVQoKSwgWyR0aGlzXSk7XG5cblx0XHRcdFx0XHRcdFx0XHRcdF9yZXBvc2l0aW9uT3BlbkxheWVyKCk7XG5cdFx0XHRcdFx0XHRcdFx0fSwgKHR5cGVvZiBkZWxheSA9PT0gJ251bWJlcicpID8gZGVsYXkgOiBvcHRpb25zLmVudGVyRGVsYXkpO1xuXG5cdFx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdGNhc2UgJ2xlYXZlJzpcblx0XHRcdFx0XHRcdFx0b25FbnRlciA9IGZhbHNlO1xuXHRcdFx0XHRcdFx0XHQvLyBTZXQgYSB0aW1lciBmb3IgY2xvc2luZyBpZiB0aGUgc3VibWVudSAoZGVsYXllZCBjbG9zaW5nKVxuXHRcdFx0XHRcdFx0XHRfY2xlYXJUaW1lb3V0cygpO1xuXHRcdFx0XHRcdFx0XHQkc2VsZi5hZGRDbGFzcygnbGVhdmUnKTtcblx0XHRcdFx0XHRcdFx0bGVhdmVUaW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0Ly8gUmVtb3ZlIGFsbCBvcGVuQ2xhc3MtY2xhc3NlcyBmcm9tIHRoZVxuXHRcdFx0XHRcdFx0XHRcdC8vIG1lbnUgZXhjZXB0IHRoZSBlbGVtZW50cyBwYXJlbnRzXG5cdFx0XHRcdFx0XHRcdFx0dHJhbnNpdGlvbi5vcGVuID0gZmFsc2U7XG5cdFx0XHRcdFx0XHRcdFx0JGxpc3Rcblx0XHRcdFx0XHRcdFx0XHRcdC5maW5kKCcuJyArIG9wdGlvbnMub3BlbkNsYXNzKVxuXHRcdFx0XHRcdFx0XHRcdFx0Lm5vdCgkc2VsZi5wYXJlbnRzVW50aWwoJHRoaXMsICcuJyArIG9wdGlvbnMub3BlbkNsYXNzKSlcblx0XHRcdFx0XHRcdFx0XHRcdC5vZmYoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSlcblx0XHRcdFx0XHRcdFx0XHRcdC5vbmUoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLlRSQU5TSVRJT05fRklOSVNIRUQoKSwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdF9zZXRFdmVudFR5cGVDbGFzcygkc2VsZiwgJycpO1xuXHRcdFx0XHRcdFx0XHRcdFx0XHQkc2VsZi5yZW1vdmVDbGFzcygnbGVhdmUnKTtcblx0XHRcdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcblxuXG5cdFx0XHRcdFx0XHRcdH0sICh0eXBlb2YgZGVsYXkgPT09ICdudW1iZXInKSA/IGRlbGF5IDogb3B0aW9ucy5sZWF2ZURlbGF5KTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHR9XG5cblx0XHRcdFx0fVxuXG5cdFx0XHR9XG5cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIGNsaWNrIC8gbW91c2VlbnRlciAvIG1vdXNlbGVhdmUgZXZlbnRcblx0XHQgKiBvbiB0aGUgbmF2aWdhdGlvbiBsaSBlbGVtZW50cy4gSXQgY2hlY2tzIGlmIHRoZSBldmVudCB0eXBlXG5cdFx0ICogaXMgc3VwcG9ydGVkIGZvciB0aGUgY3VycmVudCB2aWV3IHR5cGUgYW5kIGNhbGxzIHRoZVxuXHRcdCAqIG9wZW5NZW51LWZ1bmN0aW9uIGlmIHNvLlxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX21vdXNlSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdHZpZXdwb3J0ID0gbW9kZS5pZCA8PSBvcHRpb25zLmJyZWFrcG9pbnQgPyAnbW9iaWxlJyA6ICdkZXNrdG9wJyxcblx0XHRcdFx0ZXZlbnRzID0gKG9wdGlvbnMuZXZlbnRzICYmIG9wdGlvbnMuZXZlbnRzW3ZpZXdwb3J0XSkgPyBvcHRpb25zLmV2ZW50c1t2aWV3cG9ydF0gOiBbXTtcblx0XHRcdFxuXHRcdFx0X3NldEV2ZW50VHlwZUNsYXNzKCRzZWxmLCAnbW91c2UnKTtcblx0XHRcdGlmICgkLmluQXJyYXkoZS5kYXRhLmV2ZW50LCBldmVudHMpID4gLTEpIHtcblx0XHRcdFx0X29wZW5NZW51LmNhbGwoJHNlbGYsIGUsIHZpZXdwb3J0LCBlLmRhdGEuZGVsYXkpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBQZXJmb3JtIG5hdmlnYXRpb24gZm9yIGN1c3RvbSBsaW5rcyBhbmQgY2F0ZWdvcnkgbGlua3Mgb24gdG91Y2ggZGV2aWNlcyBpZiBubyBzdWJjYXRlZ29yaWVzIGFyZSBmb3VuZC5cblx0XHRcdGlmICgoJHNlbGYuaGFzQ2xhc3MoJ2N1c3RvbScpIHx8IChpc1RvdWNoRGV2aWNlICYmICRzZWxmLmNoaWxkcmVuKCd1bCcpLmxlbmd0aCA9PT0gMCkpIFxuXHRcdFx0XHQmJiBlLmRhdGEuZXZlbnQgPT09ICdjbGljaycgJiYgISRzZWxmLmZpbmQoJ2Zvcm0nKS5sZW5ndGgpIHtcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKCRzZWxmLmZpbmQoJ2EnKS5hdHRyKCd0YXJnZXQnKSA9PT0gJ19ibGFuaycpIHtcblx0XHRcdFx0XHR3aW5kb3cub3Blbigkc2VsZi5maW5kKCdhJykuYXR0cignaHJlZicpKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRsb2NhdGlvbi5ocmVmID0gJHNlbGYuZmluZCgnYScpLmF0dHIoJ2hyZWYnKTtcdFx0XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgdGhlIHRvdWNoc3RhcnQgZXZlbnQgKG9yIFwicG9pbnRlcmRvd25cIlxuXHRcdCAqIGRlcGVuZGluZyBvbiB0aGUgYnJvd3NlcikuIEl0IHJlbW92ZXMgdGhlIG90aGVyIGNyaXRpY2FsXG5cdFx0ICogZXZlbnQgaGFuZGxlciAodGhhdCB3b3VsZCBvcGVuIHRoZSBtZW51KSBmcm9tIHRoZSBsaXN0XG5cdFx0ICogZWxlbWVudCBpZiB0aGUgdGhlIG1vdXNlZW50ZXIgd2FzIGV4ZWN1dGVkIGJlZm9yZSBhbmRcblx0XHQgKiBhIGNsaWNrIG9yIHRvdWNoIGV2ZW50IHdpbGwgYmUgcGVyZm9ybWVkIGFmdGVyd2FyZHMuIFRoaXNcblx0XHQgKiBpcyBuZWVkZWQgdG8gcHJldmVudCB0aGUgYnJvd3NlciBlbmdpbmUgd29ya2Fyb3VuZHMgd2hpY2hcblx0XHQgKiB3aWxsIGF1dG9tYXRpY2FsbHkgcGVyZm9ybSBtb3VzZSAvIGNsaWNrLWV2ZW50cyBvbiB0b3VjaFxuXHRcdCAqIGFsc28uXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RvdWNoSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdHZpZXdwb3J0ID0gbW9kZS5pZCA8PSBvcHRpb25zLmJyZWFrcG9pbnQgPyAnbW9iaWxlJyA6ICdkZXNrdG9wJyxcblx0XHRcdFx0ZXZlbnRzID0gKG9wdGlvbnMuZXZlbnRzICYmIG9wdGlvbnMuZXZlbnRzW3ZpZXdwb3J0XSkgPyBvcHRpb25zLmV2ZW50c1t2aWV3cG9ydF0gOiBbXTtcblxuXHRcdFx0JGxpc3QuZmluZCgnLmVudGVyLWNhdGVnb3J5Jykuc2hvdygpO1xuXHRcdFx0JGxpc3QuZmluZCgnLmRyb3Bkb3duID4gYScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdFx0fSk7XG5cblx0XHRcdGlmIChlLmRhdGEudHlwZSA9PT0gJ3N0YXJ0Jykge1xuXHRcdFx0XHR0b3VjaGVTdGFydEV2ZW50ID0ge2V2ZW50OiBlLCB0aW1lc3RhbXA6IG5ldyBEYXRlKCkuZ2V0VGltZSgpLCB0b3A6ICR3aW5kb3cuc2Nyb2xsVG9wKCl9O1xuXHRcdFx0XHQkbGlzdC5vZmYoJ21vdXNlZW50ZXIubWVudSBtb3VzZWxlYXZlLm1lbnUnKTtcblx0XHRcdH0gZWxzZSBpZiAoJC5pbkFycmF5KCd0b3VjaCcsIGV2ZW50cykgPiAtMSAmJiAhX3RvdWNoTW92ZURldGVjdChlKSkge1xuXHRcdFx0XHRfc2V0RXZlbnRUeXBlQ2xhc3MoJHNlbGYsICd0b3VjaCcpO1xuXG5cdFx0XHRcdGlmICgkLmluQXJyYXkoJ2hvdmVyJywgZXZlbnRzKSA9PT0gLTEgfHwgdG91Y2hFdmVudHMuc3RhcnQgIT09ICdwb2ludGVyZG93bicpIHtcblx0XHRcdFx0XHRfb3Blbk1lbnUuY2FsbCgkc2VsZiwgZSwgdmlld3BvcnQpO1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0JGxpc3Qub24oJ21vdXNlbGVhdmUnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQkbGlzdFxuXHRcdFx0XHRcdFx0Lm9uKCdtb3VzZWVudGVyLm1lbnUnLCAnbGknLCB7ZXZlbnQ6ICdob3Zlcid9LCBfbW91c2VIYW5kbGVyKVxuXHRcdFx0XHRcdFx0Lm9uKCdtb3VzZWxlYXZlLm1lbnUnLCAnbGknLCB7ZXZlbnQ6ICdob3ZlcicsIGFjdGlvbjogJ2xlYXZlJ30sIF9tb3VzZUhhbmRsZXIpO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0fVxuXG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFN0b3JlcyB0aGUgbGFzdCB0b3VjaCBwb3NpdGlvbiBvbiB0b3VjaG1vdmVcblx0XHQgKiBAcGFyYW0gICAgICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3RvdWNoTW92ZUhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0XHR0b3VjaGVFbmRFdmVudCA9IHtldmVudDogZSwgdGltZXN0YW1wOiBuZXcgRGF0ZSgpLmdldFRpbWUoKSwgdG9wOiAkd2luZG93LnNjcm9sbFRvcCgpfTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRXZlbnQgaGFuZGxlciBmb3IgY2xvc2luZyB0aGUgbWVudSBpZlxuXHRcdCAqIHRoZSB1c2VyIGludGVyYWN0cyB3aXRoIHRoZSBwYWdlXG5cdFx0ICogb3V0c2lkZSBvZiB0aGUgbWVudVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZCAgICAgICBqUXVlcnkgc2VsZWN0aW9uIG9mIHRoZSBldmVudCBlbWl0dGVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2Nsb3NlRmx5b3V0ID0gZnVuY3Rpb24oZSwgZCkge1xuXHRcdFx0aWYgKGQgIT09ICR0aGlzICYmICR0aGlzLmZpbmQoJChlLnRhcmdldCkpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHQvLyBSZW1vdmUgb3BlbiBhbmQgY2xvc2UgdGltZXJcblx0XHRcdFx0X2NsZWFyVGltZW91dHMoKTtcblxuXHRcdFx0XHQvLyBSZW1vdmUgYWxsIHN0YXRlLWNsYXNzZXMgZnJvbSB0aGUgbWVudVxuXHRcdFx0XHRpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ2hvcml6b250YWwnKSB7XG5cdFx0XHRcdFx0JGxpc3Rcblx0XHRcdFx0XHRcdC5maW5kKCcudG91Y2gsIC5tb3VzZSwgLmxlYXZlLCAuJyArIG9wdGlvbnMub3BlbkNsYXNzKVxuXHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKCd0b3VjaCBtb3VzZSBsZWF2ZSAnICsgb3B0aW9ucy5vcGVuQ2xhc3MpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdHZhciBfb25DbGlja0FjY29yZGlvbiA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdGlmICgkKHRoaXMpLnBhcmVudHMoJy5uYXZiYXItdG9wYmFyLWl0ZW0nKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdHJldHVybjtcblx0XHRcdH1cblxuXHRcdFx0aWYgKCQodGhpcykuaGFzQ2xhc3MoJ2Ryb3Bkb3duJykpIHtcblx0XHRcdFx0aWYgKCQodGhpcykuaGFzQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpKSB7XG5cdFx0XHRcdFx0JCh0aGlzKVxuXHRcdFx0XHRcdFx0LnJlbW92ZUNsYXNzKG9wdGlvbnMub3BlbkNsYXNzKVxuXHRcdFx0XHRcdFx0LmZpbmQoJy4nICsgb3B0aW9ucy5vcGVuQ2xhc3MpXG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCQodGhpcylcblx0XHRcdFx0XHRcdC5hZGRDbGFzcyhvcHRpb25zLm9wZW5DbGFzcylcblx0XHRcdFx0XHRcdC5wYXJlbnRzVW50aWwoJHRoaXMsICdsaScpXG5cdFx0XHRcdFx0XHQuYWRkQ2xhc3Mob3B0aW9ucy5vcGVuQ2xhc3MpO1xuXHRcdFx0XHR9XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRsb2NhdGlvbi5ocmVmID0gJCh0aGlzKS5maW5kKCdhJykuYXR0cignaHJlZicpO1xuXHRcdFx0fVxuXHRcdH07XG5cblx0XHR2YXIgX2JpbmRIb3Jpem9udGFsRXZlbnRIYW5kbGVycyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JGxpc3Rcblx0XHRcdFx0Lm9uKHRvdWNoRXZlbnRzLnN0YXJ0ICsgJy5tZW51JywgJ2xpJywge3R5cGU6ICdzdGFydCd9LCBfdG91Y2hIYW5kbGVyKVxuXHRcdFx0XHQub24odG91Y2hFdmVudHMubW92ZSArICcubWVudScsICdsaScsIHt0eXBlOiAnc3RhcnQnfSwgX3RvdWNoTW92ZUhhbmRsZXIpXG5cdFx0XHRcdC5vbih0b3VjaEV2ZW50cy5lbmQgKyAnLm1lbnUnLCAnbGknLCB7dHlwZTogJ2VuZCd9LCBfdG91Y2hIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrLm1lbnUnLCAnbGknLCB7ZXZlbnQ6ICdjbGljaycsICdkZWxheSc6IDB9LCBfbW91c2VIYW5kbGVyKVxuXHRcdFx0XHQub24oJ21vdXNlZW50ZXIubWVudScsICdsaScsIHtldmVudDogJ2hvdmVyJywgYWN0aW9uOiAnZW50ZXInfSwgX21vdXNlSGFuZGxlcilcblx0XHRcdFx0Lm9uKCdtb3VzZWxlYXZlLm1lbnUnLCAnbGknLCB7ZXZlbnQ6ICdob3ZlcicsIGFjdGlvbjogJ2xlYXZlJ30sIF9tb3VzZUhhbmRsZXIpO1xuXHRcdFx0XG5cdFx0XHQkYm9keVxuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLk1FTlVfUkVQT1NJVElPTkVEKCksIF91cGRhdGVDYXRlZ29yeU1lbnUpO1xuXHRcdH07XG5cblx0XHR2YXIgX3VuYmluZEhvcml6b250YWxFdmVudEhhbmRsZXJzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkbGlzdFxuXHRcdFx0XHQub2ZmKHRvdWNoRXZlbnRzLnN0YXJ0ICsgJy5tZW51JywgJ2xpJylcblx0XHRcdFx0Lm9mZih0b3VjaEV2ZW50cy5tb3ZlICsgJy5tZW51JywgJ2xpJylcblx0XHRcdFx0Lm9mZih0b3VjaEV2ZW50cy5lbmQgKyAnLm1lbnUnLCAnbGknKVxuXHRcdFx0XHQub2ZmKCdjbGljay5tZW51JywgJ2xpJylcblx0XHRcdFx0Lm9mZignbW91c2VlbnRlci5tZW51JywgJ2xpJylcblx0XHRcdFx0Lm9mZignbW91c2VsZWF2ZS5tZW51JywgJ2xpJyk7XG5cdFx0fTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdC8vIEB0b2RvIEdldHRpbmcgdGhlIFwidG91Y2hFdmVudHNcIiBjb25maWcgdmFsdWUgcHJvZHVjZXMgcHJvYmxlbXMgaW4gdGFibGV0IGRldmljZXMuXG5cdFx0XHR0b3VjaEV2ZW50cyA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3RvdWNoJyk7XG5cdFx0XHR0cmFuc2l0aW9uLmNsYXNzT3BlbiA9IG9wdGlvbnMub3BlbkNsYXNzO1xuXG5cdFx0XHRfZ2V0U2VsZWN0aW9ucygpO1xuXHRcdFx0X3Jlc2V0SW5pdGlhbENzcygpO1xuXG5cdFx0XHQkYm9keVxuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLkJSRUFLUE9JTlQoKSwgX2JyZWFrcG9pbnRIYW5kbGVyKVxuXHRcdFx0XHQub24oanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLk9QRU5fRkxZT1VUKCkgKyAnIGNsaWNrICcgKyB0b3VjaEV2ZW50cy5lbmQsIF9jbG9zZUZseW91dCk7XG5cblx0XHRcdGlmIChvcHRpb25zLm1lbnVUeXBlID09PSAnaG9yaXpvbnRhbCcpIHtcblx0XHRcdFx0X2JpbmRIb3Jpem9udGFsRXZlbnRIYW5kbGVycygpO1xuXHRcdFx0fVxuXG5cdFx0XHRpZiAob3B0aW9ucy5tZW51VHlwZSA9PT0gJ3ZlcnRpY2FsJykge1xuXHRcdFx0XHRpZiAob3B0aW9ucy5hY2NvcmRpb24gPT09IHRydWUpIHtcblx0XHRcdFx0XHQkdGhpcy5vbignY2xpY2snLCAnbGknLCBfb25DbGlja0FjY29yZGlvbik7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHQvLyBpZiB0aGVyZSBpcyBubyB0b3AgaGVhZGVyIHdlIG11c3QgY3JlYXRlIGR1bW15IGh0bWwgYmVjYXVzZSBvdGhlciBtb2R1bGVzIHdpbGwgbm90IHdvcmsgY29ycmVjdGx5XG5cdFx0XHRcdGlmICgkKCcjY2F0ZWdvcmllcycpLmxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHRcdHZhciBodG1sID0gJzxkaXYgaWQ9XCJjYXRlZ29yaWVzXCI+PGRpdiBjbGFzcz1cIm5hdmJhci1jb2xsYXBzZSBjb2xsYXBzZVwiPidcblx0XHRcdFx0XHRcdCsgJzxuYXYgY2xhc3M9XCJuYXZiYXItZGVmYXVsdCBuYXZiYXItY2F0ZWdvcmllcyBoaWRkZW5cIj48L25hdj48L2Rpdj48L2Rpdj4nO1xuXHRcdFx0XHRcdCQoJyNoZWFkZXInKS5hcHBlbmQoaHRtbCk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblxuXHRcdFx0X2JyZWFrcG9pbnRIYW5kbGVyKCk7XG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogU3RvcCB0aGUgcHJvcGFnYXRpb24gb2YgdGhlIGV2ZW50cyBpbnNpZGUgdGhpcyBjb250YWluZXJcblx0XHRcdCAqIChXb3JrYXJvdW5kIGZvciB0aGUgXCJtb3JlXCItZHJvcGRvd24pXG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5maW5kKCcuJyArIG9wdGlvbnMuaWdub3JlQ2xhc3MpXG5cdFx0XHRcdC5vbignbW91c2VsZWF2ZS5tZW51IG1vdXNlZW50ZXIubWVudSBjbGljay5tZW51ICcgKyB0b3VjaEV2ZW50cy5zdGFydCArICcgJ1xuXHRcdFx0XHRcdCsgdG91Y2hFdmVudHMuZW5kLCAnbGknLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdFx0ZS5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLm9wZW5BY3RpdmUpIHtcblx0XHRcdFx0dmFyICRhY3RpdmUgPSAkdGhpcy5maW5kKCcuYWN0aXZlJyk7XG5cdFx0XHRcdCRhY3RpdmVcblx0XHRcdFx0XHQucGFyZW50c1VudGlsKCR0aGlzLCAnbGknKVxuXHRcdFx0XHRcdC5hZGRDbGFzcygnb3BlbicpO1xuXHRcdFx0fVxuXG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblxuXHRcdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
