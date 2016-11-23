/* --------------------------------------------------------------
 configuration.js 2016-08-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.configuration = jse.libs.configuration || {};

/**
 * ## Configurations Library
 *
 * This library makes it possible to receive and/or set shop configurations.
 */

(function(exports) {
	'use strict';
	
	const pageToken = jse.core.config.get('pageToken');
	const baseUrl = `${jse.core.config.get('appUrl')}/shop.php?do=JsConfiguration`;
	
	/**
	 * Get the configuration value by the provided key.
	 *
	 * @param key Configuration key.
	 *
	 * @returns {Promise}
	 */
	exports.get = key => {
		return new Promise((resolve, reject) => {
			const url = `${baseUrl}/Get`;
			$.ajax({ url , data: { key, pageToken }})
				.done(response => resolve(response))
				.fail(error => reject(error));
		});
	};
	
	/**
	 * Set the provided value to the provided key.
	 *
	 * @param key Configuration key.
	 * @param value Configuration value.
	 *
	 * @returns {Promise}
	 */
	exports.set = (key, value) => {
		return new Promise((resolve, reject) => {
			const url = `${baseUrl}/Set`;
			$.ajax({ url, method: 'POST', data: { key, value, pageToken }})
				.done(response => resolve(response))
				.fail(error => reject(error));
		});
	};
	
})(jse.libs.configuration);
