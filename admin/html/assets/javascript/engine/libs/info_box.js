'use strict';

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

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

	var STATUS_NEW = 'new';
	var STATUS_READ = 'read';
	var STATUS_HIDDEN = 'hidden';
	var STATUS_DELETED = 'deleted';
	var TYPE_INFO = 'info';
	var TYPE_WARNING = 'warning';
	var TYPE_SUCCESS = 'success';
	var VISIBILITY_ALWAYS_ON = 'alwayson';
	var VISIBILITY_HIDEABLE = 'hideable';
	var VISIBILITY_REMOVABLE = 'removable';

	// Admin info box element selector.
	var infoboxSelector = '.info-box';

	/**
  * Class representing the client-side API for the admin info box.
  *
  * This class contains static methods only.
  */

	var InfoBoxLibrary = function () {
		function InfoBoxLibrary() {
			_classCallCheck(this, InfoBoxLibrary);
		}

		_createClass(InfoBoxLibrary, null, [{
			key: 'getMessages',

			/**
    * Returns the messages from the server (visible only).
    * @return {Promise}
    * @static
    */
			value: function getMessages() {
				return this._performRequest('/GetAllMessages', 'GET').then(JSON.parse);
			}

			/**
    * Sets the status of a message.
    * @param {Number} id Message ID.
    * @param {String} status Message status to set ('new', 'read', 'hidden', 'deleted').
    * @return {Promise}
    * @static
    */

		}, {
			key: 'setStatus',
			value: function setStatus(id, status) {
				// Valid message status.
				var validStatus = [STATUS_NEW, STATUS_READ, STATUS_HIDDEN, STATUS_DELETED];

				// Check arguments.
				if (!id || !status) {
					throw new Error('Missing ID or status');
				} else if (validStatus.indexOf(status) === -1) {
					throw new Error('Invalid status provided');
				}

				return this._performRequest('/SetMessageStatus', 'GET', { id: id, status: status });
			}

			/**
    * Deletes a message.
    * @param {Number} id Message ID.
    * @return {Promise}
    * @static
    */

		}, {
			key: 'deleteById',
			value: function deleteById(id) {
				if (!id) {
					throw new Error('Missing ID');
				}

				return this._performRequest('/DeleteById', 'GET', { id: id });
			}

			/**
    * Deletes a message by source.
    * @param {String} source Message source.
    * @return {Promise}
    * @static
    */

		}, {
			key: 'deleteBySource',
			value: function deleteBySource(source) {
				if (!source) {
					throw new Error('Missing source');
				}

				return this._performRequest('/DeleteBySource', 'GET', { source: source });
			}

			/**
    * Deletes a messages by the identifier.
    * @param {String} identifier Message identifier.
    * @return {Promise}
    * @static
    */

		}, {
			key: 'deleteByIdentifier',
			value: function deleteByIdentifier(identifier) {
				if (!identifier) {
					throw new Error('Missing identifier');
				}

				return this._performRequest('/DeleteByIdentifier', 'GET', { identifier: identifier });
			}

			/**
    * Reactivates the messages.
    * @return {Promise}
    * @static
    */

		}, {
			key: 'reactivateMessages',
			value: function reactivateMessages() {
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

		}, {
			key: 'addMessage',
			value: function addMessage(message) {
				if (!message) {
					throw new Error('Missing message object');
				} else if (!message.source) {
					throw new Error('Missing source');
				} else if (!message.identifier) {
					throw new Error('Missing identifier');
				} else if (!message.status || [STATUS_NEW, STATUS_READ, STATUS_HIDDEN, STATUS_DELETED].indexOf(message.status) === -1) {
					throw new Error('Missing or invalid status');
				} else if (!message.type || [TYPE_INFO, TYPE_WARNING, TYPE_SUCCESS].indexOf(message.type) === -1) {
					throw new Error('Missing or invalid type');
				} else if (!message.visibility || [VISIBILITY_ALWAYS_ON, VISIBILITY_HIDEABLE, VISIBILITY_REMOVABLE].indexOf(message.visibility) === -1) {
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

		}, {
			key: '_performRequest',
			value: function _performRequest(action, method, data) {
				var URL_BASE = 'admin.php?do=AdminInfoBoxAjax';

				// AJAX request options.
				var ajaxOptions = {
					url: URL_BASE + action,
					data: data,
					method: method
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

		}, {
			key: 'addSuccessMessage',
			value: function addSuccessMessage(message) {
				var skipRefresh = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

				// Add message.
				var request = this._performRequest('/AddSuccessMessage', 'GET', message ? { message: message } : {});

				// Optionally refresh the admin info box to show the message.
				if (!skipRefresh) {
					request.done(function () {
						return $(infoboxSelector).trigger('refresh:messages');
					});
				}
			}
		}]);

		return InfoBoxLibrary;
	}();

	// Expose library.


	exports.service = InfoBoxLibrary;
})(jse.libs.info_box);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImluZm9fYm94LmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJpbmZvX2JveCIsImV4cG9ydHMiLCJTVEFUVVNfTkVXIiwiU1RBVFVTX1JFQUQiLCJTVEFUVVNfSElEREVOIiwiU1RBVFVTX0RFTEVURUQiLCJUWVBFX0lORk8iLCJUWVBFX1dBUk5JTkciLCJUWVBFX1NVQ0NFU1MiLCJWSVNJQklMSVRZX0FMV0FZU19PTiIsIlZJU0lCSUxJVFlfSElERUFCTEUiLCJWSVNJQklMSVRZX1JFTU9WQUJMRSIsImluZm9ib3hTZWxlY3RvciIsIkluZm9Cb3hMaWJyYXJ5IiwiX3BlcmZvcm1SZXF1ZXN0IiwidGhlbiIsIkpTT04iLCJwYXJzZSIsImlkIiwic3RhdHVzIiwidmFsaWRTdGF0dXMiLCJFcnJvciIsImluZGV4T2YiLCJzb3VyY2UiLCJpZGVudGlmaWVyIiwibWVzc2FnZSIsInR5cGUiLCJ2aXNpYmlsaXR5IiwiYnV0dG9uTGluayIsImN1c3RvbWVySWQiLCJoZWFkbGluZSIsImJ1dHRvbkxhYmVsIiwiYWN0aW9uIiwibWV0aG9kIiwiZGF0YSIsIlVSTF9CQVNFIiwiYWpheE9wdGlvbnMiLCJ1cmwiLCIkIiwiYWpheCIsInNraXBSZWZyZXNoIiwicmVxdWVzdCIsImRvbmUiLCJ0cmlnZ2VyIiwic2VydmljZSJdLCJtYXBwaW5ncyI6Ijs7Ozs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxRQUFULEdBQW9CRixJQUFJQyxJQUFKLENBQVNDLFFBQVQsSUFBcUIsRUFBekM7O0FBRUE7Ozs7Ozs7O0FBUUMsV0FBVUMsT0FBVixFQUFtQjtBQUNuQjs7QUFFQTs7QUFDQSxLQUFNQyxhQUFhLEtBQW5CO0FBQ0EsS0FBTUMsY0FBYyxNQUFwQjtBQUNBLEtBQU1DLGdCQUFnQixRQUF0QjtBQUNBLEtBQU1DLGlCQUFpQixTQUF2QjtBQUNBLEtBQU1DLFlBQVksTUFBbEI7QUFDQSxLQUFNQyxlQUFlLFNBQXJCO0FBQ0EsS0FBTUMsZUFBZSxTQUFy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QUFWLENBQU47QUFDQTs7QUFFRCxXQUFPLEtBQUtQLGVBQUwsQ0FBcUIsYUFBckIsRUFBb0MsS0FBcEMsRUFBMkNXLE9BQTNDLENBQVA7QUFDQTs7QUFFRDs7Ozs7Ozs7OztBQWpLa0I7QUFBQTtBQUFBLG1DQTBLS08sTUExS0wsRUEwS2FDLE1BMUtiLEVBMEtxQkMsSUExS3JCLEVBMEsyQjtBQUM1QyxRQUFNQyxXQUFXLCtCQUFqQjs7QUFFQTtBQUNBLFFBQU1DLGNBQWM7QUFDbkJDLFVBQUtGLFdBQVdILE1BREc7QUFFbkJFLGVBRm1CO0FBR25CRDtBQUhtQixLQUFwQjs7QUFNQTtBQUNBLFdBQU9LLEVBQUVDLElBQUYsQ0FBT0gsV0FBUCxDQUFQO0FBQ0E7O0FBRUQ7Ozs7Ozs7QUF4TGtCO0FBQUE7QUFBQSxxQ0E4TE9YLE9BOUxQLEVBOExxQztBQUFBLFFBQXJCZSxXQUFxQix1RUFBUCxLQUFPOztBQUN0RDtBQUNBLFFBQU1DLFVBQVUsS0FBSzNCLGVBQUwsQ0FBcUIsb0JBQXJCLEVBQTJDLEtBQTNDLEVBQWtEVyxVQUFVLEVBQUVBLGdCQUFGLEVBQVYsR0FBd0IsRUFBMUUsQ0FBaEI7O0FBRUE7QUFDQSxRQUFJLENBQUNlLFdBQUwsRUFBa0I7QUFDakJDLGFBQVFDLElBQVIsQ0FBYTtBQUFBLGFBQU1KLEVBQUUxQixlQUFGLEVBQW1CK0IsT0FBbkIsQ0FBMkIsa0JBQTNCLENBQU47QUFBQSxNQUFiO0FBQ0E7QUFDRDtBQXRNaUI7O0FBQUE7QUFBQTs7QUF5TW5COzs7QUFDQTFDLFNBQVEyQyxPQUFSLEdBQWtCL0IsY0FBbEI7QUFFQSxDQTVNQSxFQTRNRWYsSUFBSUMsSUFBSixDQUFTQyxRQTVNWCxDQUFEIiwiZmlsZSI6ImluZm9fYm94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbmZvX2JveC5qcyAyMDE2LTA0LTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMuaW5mb19ib3ggPSBqc2UubGlicy5pbmZvX2JveCB8fCB7fTtcblxuLyoqXG4gKiAjIyBJbmZvIEJveCBNZXNzYWdlcyBMaWJyYXJ5XG4gKiBcbiAqIFRoaXMgbW9kdWxlIHByb3ZpZGVzIGFuIEFQSSB0byB0aGUgbmV3IGFkbWluIGxheW91dCBwYWdlcyBpbmZvIGJveC5cbiAqIFxuICogQG1vZHVsZSBBZG1pbi9MaWJzL2luZm9fYm94XG4gKiBAZXhwb3J0cyBqc2UubGlicy5pbmZvX2JveFxuICovXG4oZnVuY3Rpb24gKGV4cG9ydHMpIHtcblx0J3VzZSBzdHJpY3QnO1xuXG5cdC8vIERlZmluZSB2YWx1ZSBjb25zdGFudHMuXG5cdGNvbnN0IFNUQVRVU19ORVcgPSAnbmV3Jztcblx0Y29uc3QgU1RBVFVTX1JFQUQgPSAncmVhZCc7XG5cdGNvbnN0IFNUQVRVU19ISURERU4gPSAnaGlkZGVuJztcblx0Y29uc3QgU1RBVFVTX0RFTEVURUQgPSAnZGVsZXRlZCc7XG5cdGNvbnN0IFRZUEVfSU5GTyA9ICdpbmZvJztcblx0Y29uc3QgVFlQRV9XQVJOSU5HID0gJ3dhcm5pbmcnO1xuXHRjb25zdCBUWVBFX1NVQ0NFU1MgPSAnc3VjY2Vzcyc7XG5cdGNvbnN0IFZJU0lCSUxJVFlfQUxXQVlTX09OID0gJ2Fsd2F5c29uJztcblx0Y29uc3QgVklTSUJJTElUWV9ISURFQUJMRSA9ICdoaWRlYWJsZSc7XG5cdGNvbnN0IFZJU0lCSUxJVFlfUkVNT1ZBQkxFID0gJ3JlbW92YWJsZSc7XG5cblx0Ly8gQWRtaW4gaW5mbyBib3ggZWxlbWVudCBzZWxlY3Rvci5cblx0Y29uc3QgaW5mb2JveFNlbGVjdG9yID0gJy5pbmZvLWJveCc7XG5cblx0LyoqXG5cdCAqIENsYXNzIHJlcHJlc2VudGluZyB0aGUgY2xpZW50LXNpZGUgQVBJIGZvciB0aGUgYWRtaW4gaW5mbyBib3guXG5cdCAqXG5cdCAqIFRoaXMgY2xhc3MgY29udGFpbnMgc3RhdGljIG1ldGhvZHMgb25seS5cblx0ICovXG5cdGNsYXNzIEluZm9Cb3hMaWJyYXJ5IHtcblx0XHQvKipcblx0XHQgKiBSZXR1cm5zIHRoZSBtZXNzYWdlcyBmcm9tIHRoZSBzZXJ2ZXIgKHZpc2libGUgb25seSkuXG5cdFx0ICogQHJldHVybiB7UHJvbWlzZX1cblx0XHQgKiBAc3RhdGljXG5cdFx0ICovXG5cdFx0c3RhdGljIGdldE1lc3NhZ2VzKCkge1xuXHRcdFx0cmV0dXJuIHRoaXMuX3BlcmZvcm1SZXF1ZXN0KCcvR2V0QWxsTWVzc2FnZXMnLCAnR0VUJylcblx0XHRcdFx0LnRoZW4oSlNPTi5wYXJzZSk7XG5cdFx0fVxuXG5cdFx0LyoqXG5cdFx0ICogU2V0cyB0aGUgc3RhdHVzIG9mIGEgbWVzc2FnZS5cblx0XHQgKiBAcGFyYW0ge051bWJlcn0gaWQgTWVzc2FnZSBJRC5cblx0XHQgKiBAcGFyYW0ge1N0cmluZ30gc3RhdHVzIE1lc3NhZ2Ugc3RhdHVzIHRvIHNldCAoJ25ldycsICdyZWFkJywgJ2hpZGRlbicsICdkZWxldGVkJykuXG5cdFx0ICogQHJldHVybiB7UHJvbWlzZX1cblx0XHQgKiBAc3RhdGljXG5cdFx0ICovXG5cdFx0c3RhdGljIHNldFN0YXR1cyhpZCwgc3RhdHVzKSB7XG5cdFx0XHQvLyBWYWxpZCBtZXNzYWdlIHN0YXR1cy5cblx0XHRcdGNvbnN0IHZhbGlkU3RhdHVzID0gW1NUQVRVU19ORVcsIFNUQVRVU19SRUFELCBTVEFUVVNfSElEREVOLCBTVEFUVVNfREVMRVRFRF07XG5cblx0XHRcdC8vIENoZWNrIGFyZ3VtZW50cy5cblx0XHRcdGlmICghaWQgfHwgIXN0YXR1cykge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01pc3NpbmcgSUQgb3Igc3RhdHVzJyk7XG5cdFx0XHR9IGVsc2UgaWYgKHZhbGlkU3RhdHVzLmluZGV4T2Yoc3RhdHVzKSA9PT0gLTEpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdJbnZhbGlkIHN0YXR1cyBwcm92aWRlZCcpO1xuXHRcdFx0fVxuXG5cdFx0XHRyZXR1cm4gdGhpcy5fcGVyZm9ybVJlcXVlc3QoJy9TZXRNZXNzYWdlU3RhdHVzJywgJ0dFVCcsIHsgaWQsIHN0YXR1cyB9KTtcblx0XHR9XG5cblx0XHQvKipcblx0XHQgKiBEZWxldGVzIGEgbWVzc2FnZS5cblx0XHQgKiBAcGFyYW0ge051bWJlcn0gaWQgTWVzc2FnZSBJRC5cblx0XHQgKiBAcmV0dXJuIHtQcm9taXNlfVxuXHRcdCAqIEBzdGF0aWNcblx0XHQgKi9cblx0XHRzdGF0aWMgZGVsZXRlQnlJZChpZCkge1xuXHRcdFx0aWYgKCFpZCkge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01pc3NpbmcgSUQnKTtcblx0XHRcdH1cblxuXHRcdFx0cmV0dXJuIHRoaXMuX3BlcmZvcm1SZXF1ZXN0KCcvRGVsZXRlQnlJZCcsICdHRVQnLCB7IGlkIH0pO1xuXHRcdH1cblxuXHRcdC8qKlxuXHRcdCAqIERlbGV0ZXMgYSBtZXNzYWdlIGJ5IHNvdXJjZS5cblx0XHQgKiBAcGFyYW0ge1N0cmluZ30gc291cmNlIE1lc3NhZ2Ugc291cmNlLlxuXHRcdCAqIEByZXR1cm4ge1Byb21pc2V9XG5cdFx0ICogQHN0YXRpY1xuXHRcdCAqL1xuXHRcdHN0YXRpYyBkZWxldGVCeVNvdXJjZShzb3VyY2UpIHtcblx0XHRcdGlmICghc291cmNlKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignTWlzc2luZyBzb3VyY2UnKTtcblx0XHRcdH1cblxuXHRcdFx0cmV0dXJuIHRoaXMuX3BlcmZvcm1SZXF1ZXN0KCcvRGVsZXRlQnlTb3VyY2UnLCAnR0VUJywgeyBzb3VyY2UgfSk7XG5cdFx0fVxuXG5cdFx0LyoqXG5cdFx0ICogRGVsZXRlcyBhIG1lc3NhZ2VzIGJ5IHRoZSBpZGVudGlmaWVyLlxuXHRcdCAqIEBwYXJhbSB7U3RyaW5nfSBpZGVudGlmaWVyIE1lc3NhZ2UgaWRlbnRpZmllci5cblx0XHQgKiBAcmV0dXJuIHtQcm9taXNlfVxuXHRcdCAqIEBzdGF0aWNcblx0XHQgKi9cblx0XHRzdGF0aWMgZGVsZXRlQnlJZGVudGlmaWVyKGlkZW50aWZpZXIpIHtcblx0XHRcdGlmICghaWRlbnRpZmllcikge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01pc3NpbmcgaWRlbnRpZmllcicpO1xuXHRcdFx0fVxuXG5cdFx0XHRyZXR1cm4gdGhpcy5fcGVyZm9ybVJlcXVlc3QoJy9EZWxldGVCeUlkZW50aWZpZXInLCAnR0VUJywgeyBpZGVudGlmaWVyIH0pO1xuXHRcdH1cblxuXHRcdC8qKlxuXHRcdCAqIFJlYWN0aXZhdGVzIHRoZSBtZXNzYWdlcy5cblx0XHQgKiBAcmV0dXJuIHtQcm9taXNlfVxuXHRcdCAqIEBzdGF0aWNcblx0XHQgKi9cblx0XHRzdGF0aWMgcmVhY3RpdmF0ZU1lc3NhZ2VzKCkge1xuXHRcdFx0cmV0dXJuIHRoaXMuX3BlcmZvcm1SZXF1ZXN0KCcvUmVhY3RpdmF0ZU1lc3NhZ2VzJywgJ0dFVCcpO1xuXHRcdH1cblxuXHRcdC8qKlxuXHRcdCAqIFNhdmVzIGEgbmV3IG1lc3NhZ2UuXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IG1lc3NhZ2UgVGhlIG5ldyBtZXNzYWdlIHRvIHNhdmUuXG5cdFx0ICogQGV4YW1wbGVcblx0XHQgKiBqc2UubGlicy5pbmZvX2JveC5zZXJ2aWNlLmFkZE1lc3NhZ2Uoe1xuICAgICAgICAgKiAgIHNvdXJjZTogJ2FqYXgnLFxuICAgICAgICAgKiAgIGlkZW50aWZpZXI6ICdhc2RhcycsXG4gICAgICAgICAqICAgc3RhdHVzOiAnbmV3JyxcbiAgICAgICAgICogICB0eXBlOiAnc3VjY2VzcycsXG4gICAgICAgICAqICAgdmlzaWJpbGl0eTogJ3JlbW92YWJsZScsXG4gICAgICAgICAqICAgY3VzdG9tZXJJZDogMCxcbiAgICAgICAgICogICBoZWFkbGluZTogJ015IEhlYWRsaW5lJyxcbiAgICAgICAgICogICBidXR0b25MYWJlbDogJ2FzZGFzJyxcblx0XHQgKlx0IGJ1dHRvbkxpbms6ICdjdXN0b21lcnMucGhwJyxcbiAgICAgICAgICogICBtZXNzYWdlOiAnSGFsbG8hJyxcbiAgICAgICAgICogfSk7XG5cdFx0ICogQHJldHVybiB7UHJvbWlzZX1cblx0XHQgKiBAc3RhdGljXG5cdFx0ICovXG5cdFx0c3RhdGljIGFkZE1lc3NhZ2UobWVzc2FnZSkge1xuXHRcdFx0aWYgKCFtZXNzYWdlKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignTWlzc2luZyBtZXNzYWdlIG9iamVjdCcpO1xuXHRcdFx0fSBlbHNlIGlmICghbWVzc2FnZS5zb3VyY2UpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIHNvdXJjZScpO1xuXHRcdFx0fSBlbHNlIGlmICghbWVzc2FnZS5pZGVudGlmaWVyKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignTWlzc2luZyBpZGVudGlmaWVyJyk7XG5cdFx0XHR9IGVsc2UgaWYgKCFtZXNzYWdlLnN0YXR1cyB8fCBbXG5cdFx0XHRcdFNUQVRVU19ORVcsIFNUQVRVU19SRUFELCBTVEFUVVNfSElEREVOLFxuXHRcdFx0XHRTVEFUVVNfREVMRVRFRFxuXHRcdFx0XS5pbmRleE9mKG1lc3NhZ2Uuc3RhdHVzKSA9PT0gLTEpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIG9yIGludmFsaWQgc3RhdHVzJyk7XG5cdFx0XHR9IGVsc2UgaWYgKCFtZXNzYWdlLnR5cGUgfHwgW1RZUEVfSU5GTywgVFlQRV9XQVJOSU5HLCBUWVBFX1NVQ0NFU1NdLmluZGV4T2YobWVzc2FnZS50eXBlKSA9PT0gLTEpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIG9yIGludmFsaWQgdHlwZScpO1xuXHRcdFx0fSBlbHNlIGlmICghbWVzc2FnZS52aXNpYmlsaXR5IHx8IFtcblx0XHRcdFx0VklTSUJJTElUWV9BTFdBWVNfT04sIFZJU0lCSUxJVFlfSElERUFCTEUsXG5cdFx0XHRcdFZJU0lCSUxJVFlfUkVNT1ZBQkxFXG5cdFx0XHRdLmluZGV4T2YobWVzc2FnZS52aXNpYmlsaXR5KSA9PT0gLTEpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIG9yIGludmFsaWQgdmlzaWJpbGl0eScpO1xuXHRcdFx0fSBlbHNlIGlmICghbWVzc2FnZS5idXR0b25MaW5rKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignTWlzc2luZyBidXR0b24gbGluaycpO1xuXHRcdFx0fSBlbHNlIGlmICh0eXBlb2YgbWVzc2FnZS5jdXN0b21lcklkID09PSAndW5kZWZpbmVkJykge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01pc3NpbmcgY3VzdG9tZXIgSUQnKTtcblx0XHRcdH0gZWxzZSBpZiAoIW1lc3NhZ2UubWVzc2FnZSkge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01pc3NpbmcgbWVzc2FnZScpO1xuXHRcdFx0fSBlbHNlIGlmICghbWVzc2FnZS5oZWFkbGluZSkge1xuXHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ01pc3NpbmcgaGVhZGxpbmUnKTtcblx0XHRcdH0gZWxzZSBpZiAoIW1lc3NhZ2UuYnV0dG9uTGFiZWwpIHtcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNaXNzaW5nIGJ1dHRvbiBsYWJlbCcpO1xuXHRcdFx0fSBlbHNlIGlmICghbWVzc2FnZS5tZXNzYWdlKSB7XG5cdFx0XHRcdHRocm93IG5ldyBFcnJvcignTWlzc2luZyBtZXNzYWdlJyk7XG5cdFx0XHR9XG5cblx0XHRcdHJldHVybiB0aGlzLl9wZXJmb3JtUmVxdWVzdCgnL0FkZE1lc3NhZ2UnLCAnR0VUJywgbWVzc2FnZSk7XG5cdFx0fVxuXG5cdFx0LyoqXG5cdFx0ICogUGVyZm9ybXMgYW4gYWpheCByZXF1ZXN0IHRvIHRoZSBzZXJ2ZXIuXG5cdFx0ICogQHBhcmFtIHtTdHJpbmd9IGFjdGlvbiBVUkwgYWN0aW9uIHBhcnQuXG5cdFx0ICogQHBhcmFtIHtTdHJpbmd9IG1ldGhvZCBIVFRQIHJlcXVlc3QgbWV0aG9kLlxuXHRcdCAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhICAgUmVxdWVzdCBkYXRhLlxuXHRcdCAqIEByZXR1cm4ge0RlZmVycmVkfFByb21pc2V9XG5cdFx0ICogQHN0YXRpY1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0c3RhdGljIF9wZXJmb3JtUmVxdWVzdChhY3Rpb24sIG1ldGhvZCwgZGF0YSkge1xuXHRcdFx0Y29uc3QgVVJMX0JBU0UgPSAnYWRtaW4ucGhwP2RvPUFkbWluSW5mb0JveEFqYXgnO1xuXG5cdFx0XHQvLyBBSkFYIHJlcXVlc3Qgb3B0aW9ucy5cblx0XHRcdGNvbnN0IGFqYXhPcHRpb25zID0ge1xuXHRcdFx0XHR1cmw6IFVSTF9CQVNFICsgYWN0aW9uLFxuXHRcdFx0XHRkYXRhLFxuXHRcdFx0XHRtZXRob2Rcblx0XHRcdH07XG5cblx0XHRcdC8vIFJldHVybnMgZGVmZXJyZWQgb2JqZWN0LlxuXHRcdFx0cmV0dXJuICQuYWpheChhamF4T3B0aW9ucyk7XG5cdFx0fVxuXG5cdFx0LyoqXG5cdFx0ICogQWRkcyBhIHN1Y2Nlc3MgbWVzc2FnZSB0byB0aGUgYWRtaW4gaW5mbyBib3guXG5cdFx0ICogQHBhcmFtIHtTdHJpbmd9IFttZXNzYWdlXSBNZXNzYWdlIHRvIHNob3cuXG5cdFx0ICogQHBhcmFtIHtCb29sZWFufSBbc2tpcFJlZnJlc2ggPSBmYWxzZV0gUmVmcmVzaCB0aGUgYWRtaW4gaW5mbyBib3ggdG8gc2hvdyB0aGUgbWVzc2FnZT9cblx0XHQgKiBAc3RhdGljXG5cdFx0ICovXG5cdFx0c3RhdGljIGFkZFN1Y2Nlc3NNZXNzYWdlKG1lc3NhZ2UsIHNraXBSZWZyZXNoID0gZmFsc2UpIHtcblx0XHRcdC8vIEFkZCBtZXNzYWdlLlxuXHRcdFx0Y29uc3QgcmVxdWVzdCA9IHRoaXMuX3BlcmZvcm1SZXF1ZXN0KCcvQWRkU3VjY2Vzc01lc3NhZ2UnLCAnR0VUJywgbWVzc2FnZSA/IHsgbWVzc2FnZSB9IDoge30pO1xuXG5cdFx0XHQvLyBPcHRpb25hbGx5IHJlZnJlc2ggdGhlIGFkbWluIGluZm8gYm94IHRvIHNob3cgdGhlIG1lc3NhZ2UuXG5cdFx0XHRpZiAoIXNraXBSZWZyZXNoKSB7XG5cdFx0XHRcdHJlcXVlc3QuZG9uZSgoKSA9PiAkKGluZm9ib3hTZWxlY3RvcikudHJpZ2dlcigncmVmcmVzaDptZXNzYWdlcycpKTtcblx0XHRcdH1cblx0XHR9XG5cdH1cblxuXHQvLyBFeHBvc2UgbGlicmFyeS5cblx0ZXhwb3J0cy5zZXJ2aWNlID0gSW5mb0JveExpYnJhcnk7XG5cbn0gKGpzZS5saWJzLmluZm9fYm94KSk7XG4iXX0=
