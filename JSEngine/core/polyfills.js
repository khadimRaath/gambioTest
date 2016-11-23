/* --------------------------------------------------------------
 polyfills.js 2016-05-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * JSE Polyfills 
 * 
 * Required polyfills for compatibility among old browsers.
 *
 * @module JSE/Core/polyfills
 */
(function () {

	'use strict';

	// Internet Explorer does not support the origin property of the window.location object.
	// {@link http://tosbourn.com/a-fix-for-window-location-origin-in-internet-explorer}
	if (!window.location.origin) {
		window.location.origin = window.location.protocol + '//' +
		                         window.location.hostname + (window.location.port ? ':' + window.location.port : '');
	}

	// Date.now method polyfill
	// {@link https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Date/now}
	if (!Date.now) {
		Date.now = function now() {
			return new Date().getTime();
		};
	}
	
})();


