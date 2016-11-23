/* --------------------------------------------------------------
 language_selection.js 2016-06-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module('language_selection', [], function(data) {
	
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
	
	/**
	 * On Language Link Click
	 *
	 * Prevent the default link behavior and regenerate the correct URL by taking into concern the dynamic
	 * GET parameters (e.g. from table filtering).
	 *
	 * @param {jQuery.Event} event
	 */
	function _onClickLanguageLink(event) {
		event.preventDefault();
		
		const currentUrlParameters = $.deparam(window.location.search.slice(1));
		
		currentUrlParameters.language = $(this).data('languageCode');
		
		window.location.href = window.location.pathname + '?' + $.param(currentUrlParameters);
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.on('click', 'a', _onClickLanguageLink);
		done();
	};
	
	return module;
	
}); 