/* --------------------------------------------------------------
 initialize.js 2016-04-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Admin Layout Initialization Controller
 *
 * This controller will handle the initialization of the admin pages. Bind this controller
 * in the body element of the page.
 */
gx.controllers.module('initialize', [], function(data) {
	
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
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$('body').on('JSENGINE_INIT_FINISHED', () => {
			$this.fadeIn(200, () => {
				$this.removeClass('page-loading');
			});
		});
		
		done();
	};
	
	return module;
	
});