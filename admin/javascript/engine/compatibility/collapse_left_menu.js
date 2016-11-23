/* --------------------------------------------------------------
 collapse_left_menu.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Collapse Main Left Menu
 *
 * This module will handle the collapse and expansion of the main left menu of the admin section. The HTML
 * for the collapse button comes from the "html/compatibility/collapse_left_menu.php".
 *
 * @module Compatibility/collapse_left_menu
 */
gx.compatibility.module(
	'collapse_left_menu',
	
	['user_configuration_service'],
	
	/**  @lends module:Compatibility/collapse_left_menu */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// ELEMENTS DEFINITION
		// ------------------------------------------------------------------------
		
		var $this = $(this),
			$menu = $('.main-left-menu'),
			$currentMenuBox = $menu.find('.leftmenu_box.current'),
			$menuToggleButton = $this.find('.menu-toggle-button'),
			$menuButtonIndicator = $menuToggleButton.find('#menu-button-indicator'),
			menuInitState = $menu.data('initState');
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var module = {},
			
			initialCssWidth = $menu.css('width'),
			
			userConfigurationService = jse.libs.user_configuration_service,
			
			userConfig = {
				userId: data.userId,
				configurationKey: 'menuVisibility'
			},
			
			stateMap = {
				collapse: {
					next: 'expand',
					button: 'right',
					class: 'collapsed',
					do: function(isAnimated) {
						_collapse(isAnimated);
					}
				},
				expand: {
					next: 'expandAll',
					button: 'down',
					class: 'expanded',
					do: function(isAnimated) {
						_expand(isAnimated);
					}
				},
				expandAll: {
					next: 'collapse',
					button: 'left',
					class: 'expanded-all',
					do: function(isAnimated) {
						_expandAll(isAnimated);
					}
				}
			},
			
			currentState;
		
		// ------------------------------------------------------------------------
		// HELPERS
		// ------------------------------------------------------------------------
		
		var isMenuVisible = function() {
			return !$menu.hasClass('collapsed');
		};
		
		// ------------------------------------------------------------------------
		// STATE CHANGE TRIGGERS
		// ------------------------------------------------------------------------
		
		var _changeState = function(state, isAnimated, saveConfig = true) {
			currentState = state;
			stateMap[currentState].do(isAnimated);
			
			if (saveConfig) {
				_saveConfig();
			}
			
			_changeButton();
		};
		
		var _changeButton = function() {
			var className = 'fa fa-caret-';
			var arrowDirection = stateMap[currentState].button;
			$menuButtonIndicator
				.removeAttr('class')
				.addClass(className + arrowDirection);
		};
		
		// ------------------------------------------------------------------------
		// COLLAPSE / EXPAND MENU
		// ------------------------------------------------------------------------
		
		/**
		 * Collapse Left Menu
		 * @param {boolean} isAnimated - Animate the hiding?
		 * @private
		 */
		var _collapse = function(isAnimated) {
			
			var currentBox = $this.parent().find('ul.current');
			
			// Collapse menu
			if (isAnimated) {
				$menu.animate({
					'width': '45px'
				}, 300, 'swing');
			} else {
				$menu.css('width', '45px');
				$('.columnLeft2').css('width', '45px');
			}
			currentBox.hide();
			
			$(document).trigger('leftmenu:collapse');
			
			// Fade out heading text
			$menu
				.find('.leftmenu_head span')
				.fadeOut('fast');
			
			// Class changes
			$menu
				.removeClass('expanded-all')
				.addClass('collapsed');
			
			$menu
				.find('.current:not(li)')
				.removeClass('current');
			
			$menu
				.find('.current-menu-head')
				.addClass('current');
			
			var interval = setInterval(function() {
				if (currentState === 'collapse') {
					if ($('.leftmenu_head.current').length > 1) {
						$menu
							.find('.leftmenu_head.current:not(.current-menu-head)')
							.removeClass('current');
						clearInterval(interval);
					}
				} else {
					clearInterval(interval);
				}
			}, 1);
			
		};
		
		/**
		 * Expand Left Menu
		 * @private
		 */
		var _expand = function() {
			
			var currentBox = $this.parent().find('ul.current');
			
			// Expand menu
			$menu.animate({
				'width': initialCssWidth
			}, 300, 'swing');
			currentBox.show();
			
			// Fade in heading text
			$menu.find('.leftmenu_head span').fadeIn('slow');
			
			$(document).trigger('leftmenu:expand');
			
			// Class changes
			$menu.removeClass('collapsed');
			$currentMenuBox.addClass('current');
			
		};
		
		/**
		 * Expand all menu items
		 * @private
		 */
		var _expandAll = function(isAnimated) {
			
			$menu
				.addClass('expanded-all');
			
			var $headingBoxes = $menu
				.find('div.leftmenu_head:not(.current)');
			
			if (isAnimated) {
				$headingBoxes.addClass('current', 750, 'swing');
			} else {
				$headingBoxes.addClass('current');
			}
			
			$(document).trigger('leftmenu:expand');
			
			$menu
				.find('ul.leftmenu_box:not(.current)')
				.addClass('current');
		};
		
		// ------------------------------------------------------------------------
		// USER CONFIGURATION HANDLER
		// ------------------------------------------------------------------------
		
		var _saveConfig = function() {
			userConfigurationService.set({
				data: $.extend(userConfig, {
					configurationValue: currentState
				})
			});
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _onClick = function(event) {
			if ($menuToggleButton.has(event.target).length) {
				_changeState(stateMap[currentState].next, true);
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			
			$('div.leftmenu_head.current').addClass('current-menu-head');
			
			if (!isMenuVisible()) {
				$currentMenuBox.removeClass('current');
			}
			
			currentState = menuInitState;
			
			if (currentState === '') {
				currentState = 'expand'; // Default value if there is no menuInitState set yet.
			}
			
			_changeState(currentState, false, false);
			
			$this.on('click', _onClick);
			
			done();
		};
		
		return module;
	});
