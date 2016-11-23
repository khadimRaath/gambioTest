/* --------------------------------------------------------------
 normalize.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.normalize = jse.libs.normalize || {};

/**
 * ## Normalization Library
 * 
 * This library normalizes input and output (XSS protection). 
 *
 * @module JSE/Libs/normalize
 * @exports jse.libs.normalize
 */
(function (exports) {

	'use strict';

	/**
	 * Returns the escaped text from a HTML string.
	 *
	 * {@link http://stackoverflow.com/a/25207}
	 *
	 * @param {string} text The text to be escaped.
	 *
	 * @return {string} Returns the escaped string.
	 *
	 * @public
	 */
	exports.escapeHtml = function(text) {
		return $('<div/>').text(text).html();
	};

})(jse.libs.normalize);
