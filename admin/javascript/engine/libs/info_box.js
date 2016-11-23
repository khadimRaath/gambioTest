/* --------------------------------------------------------------
 info_box.js 2016-04-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.info_box = jse.libs.info_box || {};

/**
 * ## Info Box Messages Library
 * 
 * This module provides an API to the new admin layout pages info box.
 * 
 * @module Admin/Libs/info_box
 * @exports jse.libs.info_box
 */
(function (exports) {
	'use strict';

	// Define value constants.
	const STATUS_NEW = 'new';
	const STATUS_READ = 'read';
	const STATUS_HIDDEN = 'hidden';
	const STATUS_DELETED = 'deleted';
	const TYPE_INFO = 'info';
	const TYPE_WARNING = 'warning';
	const TYPE_SUCCESS = 'success';
	const VISIBILITY_ALWAYS_ON = 'alwayson';
	const VISIBILITY_HIDEABLE = 'hideable';
	const VISIBILITY_REMOVABLE = 'removable';

	// Admin info box element selector.
	const infoboxSelector = '.info-box';

	/**
	 * Class representing the client-side API for the admin info box.
	 *
	 * This class contains static methods only.
	 */
	class InfoBoxLibrary {
		/**
		 * Returns the messages from the server (visible only).
		 * @return {Promise}
		 * @static
		 */
		static getMessages() {
			return this._performRequest('/GetAllMessages', 'GET')
				.then(JSON.parse);
		}

		/**
		 * Sets the status of a message.
		 * @param {Number} id Message ID.
		 * @param {String} status Message status to set ('new', 'read', 'hidden', 'deleted').
		 * @return {Promise}
		 * @static
		 */
		static setStatus(id, status) {
			// Valid message status.
			const validStatus = [STATUS_NEW, STATUS_READ, STATUS_HIDDEN, STATUS_DELETED];

			// Check arguments.
			if (!id || !status) {
				throw new Error('Missing ID or status');
			} else if (validStatus.indexOf(status) === -1) {
				throw new Error('Invalid status provided');
			}

			return this._performRequest('/SetMessageStatus', 'GET', { id, status });
		}

		/**
		 * Deletes a message.
		 * @param {Number} id Message ID.
		 * @return {Promise}
		 * @static
		 */
		static deleteById(id) {
			if (!id) {
				throw new Error('Missing ID');
			}

			return this._performRequest('/DeleteById', 'GET', { id });
		}

		/**
		 * Deletes a message by source.
		 * @param {String} source Message source.
		 * @return {Promise}
		 * @static
		 */
		static deleteBySource(source) {
			if (!source) {
				throw new Error('Missing source');
			}

			return this._performRequest('/DeleteBySource', 'GET', { source });
		}

		/**
		 * Deletes a messages by the identifier.
		 * @param {String} identifier Message identifier.
		 * @return {Promise}
		 * @static
		 */
		static deleteByIdentifier(identifier) {
			if (!identifier) {
				throw new Error('Missing identifier');
			}

			return this._performRequest('/DeleteByIdentifier', 'GET', { identifier });
		}

		/**
		 * Reactivates the messages.
		 * @return {Promise}
		 * @static
		 */
		static reactivateMessages() {
			return this._performRequest('/ReactivateMessages', 'GET');
		}

		/**
		 * Saves a new message.
		 * @param {Object} message The new message to save.
		 * @example
		 * jse.libs.info_box.service.addMessage({
         *   source: 'ajax',
         *   identifier: 'asdas',
         *   status: 'new',
         *   type: 'success',
         *   visibility: 'removable',
         *   customerId: 0,
         *   headline: 'My Headline',
         *   buttonLabel: 'asdas',
		 *	 buttonLink: 'customers.php',
         *   message: 'Hallo!',
         * });
		 * @return {Promise}
		 * @static
		 */
		static addMessage(message) {
			if (!message) {
				throw new Error('Missing message object');
			} else if (!message.source) {
				throw new Error('Missing source');
			} else if (!message.identifier) {
				throw new Error('Missing identifier');
			} else if (!message.status || [
				STATUS_NEW, STATUS_READ, STATUS_HIDDEN,
				STATUS_DELETED
			].indexOf(message.status) === -1) {
				throw new Error('Missing or invalid status');
			} else if (!message.type || [TYPE_INFO, TYPE_WARNING, TYPE_SUCCESS].indexOf(message.type) === -1) {
				throw new Error('Missing or invalid type');
			} else if (!message.visibility || [
				VISIBILITY_ALWAYS_ON, VISIBILITY_HIDEABLE,
				VISIBILITY_REMOVABLE
			].indexOf(message.visibility) === -1) {
				throw new Error('Missing or invalid visibility');
			} else if (!message.buttonLink) {
				throw new Error('Missing button link');
			} else if (typeof message.customerId === 'undefined') {
				throw new Error('Missing customer ID');
			} else if (!message.message) {
				throw new Error('Missing message');
			} else if (!message.headline) {
				throw new Error('Missing headline');
			} else if (!message.buttonLabel) {
				throw new Error('Missing button label');
			} else if (!message.message) {
				throw new Error('Missing message');
			}

			return this._performRequest('/AddMessage', 'GET', message);
		}

		/**
		 * Performs an ajax request to the server.
		 * @param {String} action URL action part.
		 * @param {String} method HTTP request method.
		 * @param {Object} data   Request data.
		 * @return {Deferred|Promise}
		 * @static
		 * @private
		 */
		static _performRequest(action, method, data) {
			const URL_BASE = 'admin.php?do=AdminInfoBoxAjax';

			// AJAX request options.
			const ajaxOptions = {
				url: URL_BASE + action,
				data,
				method
			};

			// Returns deferred object.
			return $.ajax(ajaxOptions);
		}

		/**
		 * Adds a success message to the admin info box.
		 * @param {String} [message] Message to show.
		 * @param {Boolean} [skipRefresh = false] Refresh the admin info box to show the message?
		 * @static
		 */
		static addSuccessMessage(message, skipRefresh = false) {
			// Add message.
			const request = this._performRequest('/AddSuccessMessage', 'GET', message ? { message } : {});

			// Optionally refresh the admin info box to show the message.
			if (!skipRefresh) {
				request.done(() => $(infoboxSelector).trigger('refresh:messages'));
			}
		}
	}

	// Expose library.
	exports.service = InfoBoxLibrary;

} (jse.libs.info_box));
