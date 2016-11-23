/* --------------------------------------------------------------
 shortcuts.js 2015-09-23 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Keyboard Shortcuts
 *
 * Allows to assign shortcuts for different actions.
 *
 * @module Compatibility/shortcuts
 */
gx.compatibility.module(
	// Module name
	'shortcuts',
	
	// Module dependencies
	[],
	
	/**  @lends module:Compatibility/shortcuts */
	
	function() {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var keysPressed = [],
			module = {};
		
		// ------------------------------------------------------------------------
		// ELEMENTS DEFINITION
		// ------------------------------------------------------------------------
		
		var $adminSearchInput = $('input[name="admin_search"]'),
			$favoriteMenuItems = $('#BOX_HEADING_FAVORITES > li');
		
		// ------------------------------------------------------------------------
		// MAPS
		// ------------------------------------------------------------------------
		
		var keyMap = {
			
			ctrl: 17,
			shift: 16,
			
			f: 70,
			
			normal1: 49,
			normal2: 50,
			normal3: 51,
			normal4: 52,
			normal5: 53,
			normal6: 54,
			normal7: 55,
			normal8: 56,
			normal9: 57
			
		};
		
		var shortcutMap = {
			
			activateSearch: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.f],
				performAction: function() {
					$adminSearchInput.trigger('click');
				}
			},
			
			openFavorite1: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal1],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(0);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite2: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal2],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(1);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite3: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal3],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(2);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite4: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal4],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(3);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite5: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal5],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(4);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite6: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal6],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(5);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite7: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal7],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(6);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite8: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal8],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(7);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
			openFavorite9: {
				shortcut: [keyMap.ctrl, keyMap.shift, keyMap.normal9],
				performAction: function() {
					var $menuItem = $favoriteMenuItems.eq(8);
					if ($menuItem.length) {
						var link = $menuItem
							.find('a')
							.prop('href');
						window.open(link, '_self');
					}
				}
			},
			
		};
		
		// ------------------------------------------------------------------------
		// METHODS
		// ------------------------------------------------------------------------
		
		var _checkArrayEquality = function(a, b) {
			if (a === b) {
				return true;
			}
			if (a == null || b === null) {
				return false;
			}
			if (a.length !== b.length) {
				return false;
			}
			
			for (var i = 0; i < a.length; ++i) {
				if (a[i] !== b[i]) {
					return false;
				}
			}
			
			return true;
		};
		
		var _checkShortcut = function(keysPressed) {
			for (var map in shortcutMap) {
				if (shortcutMap.hasOwnProperty(map) &&
					_checkArrayEquality(shortcutMap[map].shortcut, keysPressed)
				) {
					shortcutMap[map].performAction();
				}
			}
		};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLER
		// ------------------------------------------------------------------------
		
		var _initialize = function() {
			$(document).on('keyup', function() {
				setTimeout(function() {
					_checkShortcut(keysPressed);
					keysPressed = [];
				}, 100);
			});
			
			$(document).on('keydown', function(event) {
				keysPressed.push(event.which);
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			_initialize();
			done();
		};
		
		return module;
	});
