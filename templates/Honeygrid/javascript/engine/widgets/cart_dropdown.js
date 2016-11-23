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
gambio.widgets.module(
	'cart_dropdown',

	[
		'xhr',
		gambio.source + '/libs/events'
	],

	function(data) {

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
		var _scrollDown = function() {
			var $list = $this.find('.products-list'),
				height = $list.outerHeight() * 2;    // Multiply with 2 to be sure that it gets scrolled to the bottom

			$list.animate({'scrollTop': height + 'px'}, 0);
		};

		/**
		 * Triggers the mouseenter event
		 * on the cartdropdown link
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _open = function(e) {
			e.stopPropagation();

			if ($(defaults.selectorMapping.cartDropdownProductsCount).text() !== '0') {
                $(defaults.selectorMapping.cartDropdownProductsCount).removeClass('hidden');
			}

			$item.trigger('mouseenter', {prog: true});
		};

		/**
		 * Triggers the mouseleave event
		 * on the cartdropdown link
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _close = function(e) {
			e.stopPropagation();
			$item.trigger('mouseleave', {prog: true});
		};

		/**
		 * Helper function that resizes the count badge
		 * after the add of an item to the basket for
		 * a specific duration
		 * @param       {string}    selector        Text value of the old badge (the count)
		 * @param       {object}    config          The config for the badges from the ajax result content
		 * @private
		 */
		var _resizeCountBadge = function(currentCount, config) {
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
				animateTimer = setTimeout(function() {
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
		var _update = function(e, openDropdown) {
			if (ajax) {
				ajax.abort();
			}

			ajax = jse.libs.xhr.ajax({url: options.url, data: ajaxData}).done(function(result) {
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
					timer = setTimeout(function() {
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
		var _preventExec = function(e, d) {
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
		var _stickyCartDropdown = function() {
			// If the cart dropdown is not visible wait until the transition completes (see menu.js). 
			if (!$item.hasClass('open')) {
				var interval = setInterval(function() {
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
		module.init = function(done) {

			$item = $this.find('> ul > li');
			$target = options.fillTarget ? $(options.fillTarget) : $this;

			$window
				.on('focus', _update)
				.on('scroll', _stickyCartDropdown); 

			$body
				.on(jse.libs.template.events.CART_OPEN(), _open)
				.on(jse.libs.template.events.CART_CLOSE(), _close)
				.on(jse.libs.template.events.CART_UPDATE(), _update);

			$item
				.on('mouseenter mouseleave', _preventExec)
				.on('mouseenter', _stickyCartDropdown);
			
			_scrollDown();
			
			if (location.search.search('open_cart_dropdown=1') !== -1) {
				$body.trigger(jse.libs.template.events.CART_OPEN());
			}

			done();
		};

		// Return data to widget engine
		return module;
	});
