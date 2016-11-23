/* --------------------------------------------------------------
 cookie_bar.js 2016-06-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Used for hiding the Cookie-Bar on click or on page change
 */
gambio.widgets.module(
	'cookie_bar',
	
	['xhr'],
	
	function(data) {
		
		'use strict';
		
		// ########## VARIABLE INITIALIZATION ##########
		
		var $this = $(this),
			defaults = {
				closeBtn : '.close-button',
				url: 'shop.php?do=CookieBar'
			},
			options = $.extend(true, {}, defaults, data),
			module = {},
			expiry = new Date();
		
		
		// ########## EVENT HANDLER ##########
		
		
		/**
		 * Shows the Cookie-Bar
		 * 
		 * @private
		 */
		var _showCookieBar = function() {
			$this.css('display', 'table');
		};
		
		
		/**
		 * Hides the Cookie-Bar, if the hiding cookie is set or if a link or button to close the Cookie-Bar is clicked
		 * 
		 * @private
		 */
		var _hideCookieBar = function() {
			$this.hide();
		};
		
		
		/**
		 * Sets the hiding cookie
		 *
		 * @private
		 */
		var _setCookie = function() {
			jse.libs.xhr.get({
				url: options.url
			}, true).done(function() {
				_hideCookieBar();
			});
		};
		
		// ########## INITIALIZATION ##########
		
		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			$(options.closeBtn).on('click', _setCookie);
			
			if (window.localStorage !== undefined) {
				if (localStorage.getItem('cookieBarSeen') === '1') {
					_setCookie();
				} else {
					localStorage.setItem('cookieBarSeen', '1');
					_showCookieBar();
				}
			} else {
				_showCookieBar();
			}
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});