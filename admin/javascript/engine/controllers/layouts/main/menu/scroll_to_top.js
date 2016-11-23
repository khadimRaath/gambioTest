/* --------------------------------------------------------------
 scroll_to_top.js 2016-04-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Scroll to top functionality.
 */
gx.controllers.module('scroll_to_top', [], function() {
	
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
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	/**
	 * Animation Flag
	 *
	 * @type {Boolean}
	 */
	let onAnimation = false;
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * On Window Scroll
	 *
	 * If the site content is large and the user has scrolled to bottom display the caret icon.
	 */
	function _onWindowScroll() {
		let scrollPercentage = ($(window).scrollTop() + window.innerHeight) / $(document).outerHeight();
		
		if (!onAnimation && !$('#main-menu > nav > ul').hasClass('collapse')
			&& scrollPercentage > 0.9 && $(document).outerHeight() > 2500) {
			$this.fadeIn();
		} else if ($this.is(':visible')) {
			$this.fadeOut();
		}
	}
	
	/**
	 * On Icon Click
	 *
	 * Scroll to the top of the page whenever the user clicks on the caret icon.
	 */
	function _onIconClick() {
		onAnimation = true;
		
		$('html, body').animate({
			scrollTop: 0
		}, 'fast', function() {
			onAnimation = false;
		});
		
		$this.fadeOut();
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZE
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$(window).on('scroll', _onWindowScroll);
		$this.on('click', 'i', _onIconClick);
		done();
	};
	
	return module;
	
}); 