/* --------------------------------------------------------------
 storage.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Storage */

jse.libs.storage = jse.libs.storage || {};

/**
 * ## Browser Storage API Library
 *
 * This library handles the HTML storage functionality. You can either store information in the session or the local 
 * storage of the browser.
 * 
 * @todo The concept of the library is good but the implementation and API need to be improved (simple getter and 
 * setter methods).  
 *
 * @module JSE/Libs/storage
 * @exports jse.libs.storage
 * @ignore
 */
(function (exports) {

	'use strict';

	/**
	 * JavaScript Storage Object
	 * 
	 * @type {boolean}
	 */
	var webStorage = (Storage !== undefined) ? true : false;

	/**
	 * Stores a value in the browser storage.
	 *
	 * @param {object} store Storage handler object.
	 * @param {boolean} overwrite Whether to overwrite an existing storage value.
	 * @param {string} value String defining the value key name to be stored.
	 * @param {object} dataset Contains the information to be stored.
	 * @param {number} userId User id will be used to identify stored information of a specific user.
	 *
	 * @return {boolean} Returns the operation result.
	 * 
	 * @private
	 */
	var _store = function (store, overwrite, value, dataset, userId) {

		var dataCache = null,
			result = null;

		if (webStorage) {
			dataCache = store.getItem('user_' + userId);
			dataCache = dataCache || '{}';
			dataCache = $.parseJSON(dataCache);

			if (overwrite || dataCache[value] === undefined) {
				dataCache[value] = dataset;
			} else {
				dataCache[value] = $.extend({}, dataCache[value], dataset);
			}

			result = JSON.stringify(dataCache);
			store.setItem('user_' + userId, result);
			return true;
		}
		return false;
	};

	/**
	 * Restores data from the browser storage.
	 *
	 * @param {object} store Storage handler object.
	 * @param {string} value Value key name to be retrieved.
	 * @param {number} userId User id that owns the value.
	 *
	 * @return {object} Returns the value if exists or an empty object if not.
	 * 
	 * @private
	 */
	var _restore = function (store, value, userId) {

		var dataCache = null;

		if (webStorage) {
			dataCache = store.getItem('user_' + userId);
			dataCache = dataCache || '{}';
			dataCache = $.parseJSON(dataCache);
			return dataCache[value] || {};
		}
		return {};
	};

	/**
	 * Stores data in the browser storage.
	 *
	 * @param {array} destinations Array containing where to store the data (session, local).
	 * @param {object} dataset Data to be stored.
	 * @param {boolean} overwrite Whether to overwrite existing values.
	 *
	 * @return {object} Returns a promise object.
	 */
	exports.store = function (destinations, dataset, overwrite) {

		var userID = $('body').data().userId,
			resultObject = {},
			promises = [];

		$.each(destinations, function (dest, value) {
			var localDeferred = $.Deferred();
			promises.push(localDeferred);

			switch (dest) {
				case 'session':
					resultObject.session = _store(sessionStorage, overwrite, value, dataset, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'local':
					resultObject.local = _store(localStorage, overwrite, value, dataset, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'server': // @todo Remove this case because it is not supported.
					localDeferred.resolve(resultObject);
					break;
				default:
					break;
			}
		});

		return $.when.apply(undefined, promises).promise();

	};

	/**
	 * Restores data from the browser storage.
	 *
	 * @param {array} sources Defines the source of the data to be retrieved (session, local).
	 *
	 * @return {object} Returns a promise object.
	 */
	exports.restore = function (sources) {
		var userID = $('body').data().userId,
			resultObject = {},
			promises = [];

		$.each(sources, function (src, value) {
			var localDeferred = $.Deferred();
			promises.push(localDeferred);

			switch (src) {
				case 'session':
					resultObject.session = _restore(sessionStorage, value, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'local':
					resultObject.local = _restore(localStorage, value, userID);
					localDeferred.resolve(resultObject);
					break;
				case 'server': // @todo Remove this case because it is not supported.
					localDeferred.resolve(resultObject);
					break;
				default:
					break;
			}
		});

		return $.when
			.apply(undefined, promises)
			.then(function (result) {
				      return $.extend(true, {}, result.local || {}, result.session || {}, result.server || {});
			      })
			.promise();
	};

}(jse.libs.storage));
