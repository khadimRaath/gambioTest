/* --------------------------------------------------------------
 main_left_menu.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Compatibility Main Left Menu Handler
 *
 * This module will transform the old menu to the new theme.
 *
 * @module Compatibility/main_left_menu
 */
gx.compatibility.module(
	'main_left_menu',
	
	[],
	
	/**  @lends module:Compatibility/main_left_menu */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * setTimeout variable for clearing timeout
			 *
			 * @var {int}
			 */
			timeout = 0,
			
			/**
			 * Delay until submenu opens after entering left menu
			 *
			 * @var {int}
			 */
			initialShowSubmenuDelay = 100,
			
			/**
			 * Delay until submenu appears. Will be set to zero after first submenu was displayed
			 * and reset to the initial value after leaving the left menu.
			 *
			 * @var {int}
			 */
			showSubmenuDelay = initialShowSubmenuDelay,
			
			/**
			 * Save mouseDown event for not closing the submenu on dragging an entry into the favs-box
			 *
			 * @type {boolean}
			 */
			mouseDown = false,
			
			/**
			 * Submenu box wherein the mouseDown event was triggered
			 *
			 * @type {null}
			 */
			$mouseDownBox = null,
			
			/**
			 * Mouse X position on mousedown event
			 *
			 * @type {number}
			 */
			mouseX = 0,
			
			/**
			 * Mouse Y position on mousedown event
			 *
			 * @type {number}
			 */
			mouseY = 0,
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		var _showMenuBox = function($box) {
			
			var isCurrentBox = $box.hasClass('current');
			
			if ($box.find('li').length === 0 || isCurrentBox) {
				return;
			}
			
			if (!$box.is(':visible')) {
				var $menuParent = $box.prev().prev(),
					isFirstBox = ($('.leftmenu_box').index($box) === 0),
					marginTop = isFirstBox ? -4 : -5, // Fine tuning for the top position
					marginBottom = 10,
					marginLeft = -10,
					windowBottomY = $(window).scrollTop() + window.innerHeight,
				
				// error message box on dashboard page
					headerExtraContentHeight = $('.main-page-content').offset().top - $('.main-top-header').height(),
					
					topPosition = $menuParent.offset().top - headerExtraContentHeight + marginTop,
					bottomPosition = windowBottomY - $box.height() - headerExtraContentHeight + marginTop -
						marginBottom;
				
				$box.css({
					'left': $('.main-left-menu').width() + marginLeft
				}); // fine tuning left
				
				if (topPosition < bottomPosition) {
					$box.css({
						'top': topPosition
					}); // display submenu next to hovered menu item if it fits on screen
				} else {
					$box.css({
						'top': bottomPosition
					}); // else display submenu at the bottom of the screen
				}
				
				$box.fadeIn(100);
				$box.addClass('floating');
				$menuParent.addClass('active');
			}
		};
		
		var _hideMenuBox = function($box) {
			var isCurrentBox = $box.hasClass('current');
			
			if ($box.is(':visible') && !isCurrentBox && !mouseDown) {
				$box.fadeOut(100);
				$box.removeClass('floating');
				$box.prev().prev().removeClass('active');
			}
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		/**
		 * On menu head mouse enter menu event handler.
		 *
		 * @param {object} event
		 */
		var _onMenuHeadMouseEnter = function(event) {
			$(this).addClass('hover');
			var $that = $(this);
			
			clearTimeout(timeout);
			timeout = setTimeout(function() {
				_showMenuBox($that.next().next());
				showSubmenuDelay = 0;
			}, showSubmenuDelay);
		};
		
		/**
		 * On menu head mouse leave menu event handler.
		 *
		 * @param {object} event
		 */
		var _onMenuHeadMouseLeave = function(event) {
			clearTimeout(timeout);
			$(this).removeClass('hover');
			var $box = $(this).next().next(),
				$head = $(this);
			setTimeout(function() {
				if (!$box.hasClass('hover') && !$head.hasClass('hover')) {
					_hideMenuBox($box);
				}
			}, 10);
		};
		
		/**
		 * On menu mouse move event handler.
		 *
		 * Sometimes after multiple hovers the submenus remains hidden and this event handler
		 * will ensure that it will not happen while the user hovers the menu item.
		 *
		 * @param {option} event
		 */
		var _onMenuHeadMouseMove = function(event) {
			if (!$(this).hasClass('hover')) {
				$(this).addClass('hover');
			}
			
			var $box = $(this).next().next();
		};
		
		/**
		 * On menu box mouse enter menu event handler.
		 *
		 * @param {object} event
		 */
		var _onMenuBoxMouseEnter = function(event) {
			$(this).addClass('hover');
		};
		
		/**
		 * On menu box mouse leave menu event handler.
		 *
		 * @param {object} event
		 */
		var _onMenuBoxMouseLeave = function(event) {
			$(this).removeClass('hover');
			
			var $box = $(this),
				$head = $box.prev().prev();
			
			setTimeout(function() {
				if (!$box.hasClass('hover') && !$head.hasClass('hover')) {
					_hideMenuBox($box);
				}
			}, 10);
		};
		
		var _onMenuHeadingDown = function(event) {
			mouseX = event.pageX;
			mouseY = event.pageY;
		};
		
		/**
		 * On menu heading click event handler.
		 *
		 * @param {object} event
		 */
		var _onMenuHeadingClick = function(event) {
			
			// do not open link if mouse was moved more than 5px during mousdown event
			if (mouseX > (event.pageX + 5) || mouseX < (event.pageX - 5) || mouseY > (event.pageY + 5) ||
				mouseY < (event.pageY -
				5)) {
				return false;
			}
			
			// 1 = left click, 2 = middle click
			if (event.which === 1 || event.which === 2) {
				event.preventDefault();
				event.stopPropagation();
				
				var $heading = $(event.currentTarget);
				var $firstSubItem = $heading
					.next()
					.next()
					.find('li:first')
					.find('a:first');
				
				var target = (event.which === 1) ? '_self' : '_blank';
				
				// Open the first sub item's link
				if ($firstSubItem.prop('href')) {
					window.open($firstSubItem.prop('href'), target);
				}
			}
		};
		
		/**
		 * Reset submenu display delay after leaving the left menu
		 */
		var _resetShowSubmenuDelay = function() {
			showSubmenuDelay = initialShowSubmenuDelay;
		};
		
		/**
		 * Save submenu wherein the mouseDown event was triggered
		 */
		var _onMenuBoxMouseDown = function() {
			$mouseDownBox = $(this);
			mouseDown = true;
		};
		
		/**
		 * Hide submenu on mouseUp event after dragging an entry into the favs-box
		 */
		var _onMouseUp = function() {
			mouseDown = false;
			
			if ($mouseDownBox) {
				_hideMenuBox($mouseDownBox);
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZE CONTROLLER
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize controller.
		 */
		module.init = function(done) {
			$this
				.on('mouseenter', '.leftmenu_head', _onMenuHeadMouseEnter)
				.on('mouseleave', '.leftmenu_head', _onMenuHeadMouseLeave)
				.on('mousemove', '.leftmenu_head', _onMenuHeadMouseMove)
				.on('mouseenter', '.leftmenu_box', _onMenuBoxMouseEnter)
				.on('mouseleave', '.leftmenu_box', _onMenuBoxMouseLeave)
				.on('mousedown', '.leftmenu_box', _onMenuBoxMouseDown)
				.on('mousedown', '.leftmenu_head', _onMenuHeadingDown)
				.on('mouseup', '.leftmenu_head', _onMenuHeadingClick)
				.on('mouseleave', _resetShowSubmenuDelay);
			
			$(document).on('mouseup', _onMouseUp);
			
			done();
		};
		
		return module;
	});
