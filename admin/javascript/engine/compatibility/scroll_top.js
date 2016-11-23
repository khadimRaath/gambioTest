/* --------------------------------------------------------------
 scroll_top.js 2015-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Scroll Top Module
 *
 * This module will display a scroll-to-top arrow on the left side of the admin menu. When the users
 * press this arrow the browser will automatically scroll to the top of the page.
 *
 * @module Compatibility/scroll_top
 */
gx.compatibility.module(
	// Module name
	'scroll_top',
	
	// Module dependencies
	[],
	
	/**  @lends module:Compatibility/scroll_top */
	
	function() {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var module = {},
			$button;
		
		// ------------------------------------------------------------------------
		// EVENT HANDLER
		// ------------------------------------------------------------------------
		
		var _initialize = function() {
			$button = $('<div>')
				.addClass('js-scroll-top-button')
				.html('<i class="fa fa-caret-up"></i>')
				.hide()
				.appendTo('body')
				.on('click', function() {
					$('html, body').animate({
						scrollTop: 0
					});
				});
			
			$(document).on('scroll', function() {
				var reachedMinimumScrolled = ($(document).scrollTop() > 2500),
					reachedDocumentBottom = (
					$(document).scrollTop() +
					window.innerHeight === $(document).height());
				
				// Fade In / Out
				if (reachedMinimumScrolled) {
					$button.fadeIn();
				} else {
					$button.fadeOut();
				}
				
				// Fix poistion
				if (reachedMinimumScrolled) {
					$button.css({
						bottom: (reachedDocumentBottom ? '100px' : '50px')
					});
				}
			});
			
			$(document).on('leftmenu:collapse', function() {
				$button.animate({
					left: '9px'
				});
			});
			
			$(document).on('leftmenu:expand', function() {
				$button.animate({
					left: '89px'
				});
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
