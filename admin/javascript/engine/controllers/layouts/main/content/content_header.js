/* --------------------------------------------------------------
 content_header.js 2016-04-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Content Header Controller
 *
 * This module handles the behavior of the header controller. It will roll-in whenever the user scrolls to top.
 * The widget will emmit an event "content_header:roll_in" when it is rolled in and a "content_header:roll_out"
 * whenever it's hiding. These events can be useful if there's a need to re-position elements that are static
 * e.g. fixed table headers.
 *
 * In extend the content-header element will have the "fixed" class as long as it stays fixed on the viewport.
 */
gx.controllers.module('content_header', [], function(data) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Module Selector
	 *
	 * @type {jQuery}
	 */
	const $this = $(this);
	
	/**
	 * The original position of the tab navigation
	 *
	 * @type {Number}
	 */
	let originalPosition = 0;
	
	/**
	 * The last scroll position
	 *
	 * @type {Number}
	 */
	let scrollPosition = $(window).scrollTop();
	
	/**
	 * The original left position of the pageHeading
	 *
	 * @type {Number}
	 */
	let originalLeft = 0;
	
	/**
	 * Tells if the tab navigation is within the view port
	 *
	 * @type {Boolean}
	 */
	let isOut = true;
	
	/**
	 * Whether the content header is currently on animation.
	 *
	 * @type {Boolean}
	 */
	let onAnimation = false;
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * On Window Scroll Handler
	 *
	 * Reset the page navigation frame to the original position if the user scrolls directly to top.
	 */
	function _onWindowScroll() {
		if (onAnimation) {
			return;
		}
		
		const newScrollPosition = $(window).scrollTop();
		const isScrollDown = scrollPosition - newScrollPosition < 0;
		const isScrollUp = scrollPosition - newScrollPosition > 0;
		
		originalPosition = $('#main-header').height();
		originalLeft = $('#main-menu').outerWidth();
		
		_setContentHeaderAbsoluteLeft();
		
		let scrolledToTop = _checkScrolledToTop();
		
		if (!scrolledToTop) {
			if (isScrollDown && !isScrollUp && !isOut) {
				_rollOut();
			}
			else if (!isScrollDown && isScrollUp && isOut) {
				_rollIn();
			}
		}
		
		scrollPosition = newScrollPosition;
	}
	
	/**
	 * Roll-in Animation Function
	 */
	function _rollIn() {
		isOut = false;
		onAnimation = true;
		
		$this.trigger('content_header:roll_in');
		
		$this.css({
			top: '0',
			position: 'fixed'
		});
		
		// Retain the page height with a temporary padding.
		$this.parent().css('padding-top', $this.outerHeight() + 'px');
		
		$this.animate({
			top: originalPosition + 'px'
		}, {
			complete: function() {
				_checkScrolledToTop();
				onAnimation = false;
				_onWindowScroll(); // Check if it's necessary to re-render the position of the content-header.
				$this.addClass('fixed');
			}
		}, 'fast');
	}
	
	/**
	 * Roll-out Animation Function
	 */
	function _rollOut() {
		isOut = true;
		onAnimation = true;
		
		$this.trigger('content_header:roll_out');
		
		$this.animate({
			top: '0'
		}, 'fast', 'swing', function() {
			$this.css({
				top: originalPosition + 'px',
				position: ''
			});
			
			$this.parent().css('padding-top', ''); // Remove temporary padding.
			
			onAnimation = false;
			
			$this.removeClass('fixed');
		});
	}
	
	/**
	 * Sets the left position of the pageHeading absolute
	 */
	function _setContentHeaderAbsoluteLeft() {
		$this.css('left', originalLeft - $(window).scrollLeft());
	}
	
	/**
	 * Check if user has scrolled to top of the page.
	 *
	 * @return {Boolean} Returns the check result.
	 */
	function _checkScrolledToTop() {
		if ($(window).scrollTop() === 0) {
			$this.css({
				top: originalPosition + 'px',
				position: ''
			});
			
			$this.parent().css('padding-top', ''); // Remove temporary padding.
			
			return true;
		}
		
		return false;
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$(window).on('scroll', _onWindowScroll);
		done();
	};
	
	return module;
});