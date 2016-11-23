/* --------------------------------------------------------------
 shop_offline.js 2016-06-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * General Controller of Shop
 */
gx.controllers.module('shop_offline', [], function(data) {
	
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
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	function _toggleLanguageSelection() {
		const $languagesButtonBar = $('.languages.buttonbar'); 
		
		if ($(this).attr('href') === '#status') {
			$languagesButtonBar.css('visibility', 'hidden'); 
		} else {
			$languagesButtonBar.css('visibility', 'visible'); 
		}
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.find('.tab-headline-wrapper > a').on('click', _toggleLanguageSelection); 
		
		_toggleLanguageSelection.call($('.tab-headline-wrapper a')[0]);
		
		done(); 
	}; 
	
	return module;
	
}); 