'use strict';

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

/* --------------------------------------------------------------
 bulk_email_invoice.js 2016-06-16
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Bulk Email Invoice Modal Controller
 */
gx.controllers.module('bulk_email_invoice', ['modal', 'loading_spinner'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {
		bindings: { subject: $this.find('.subject') }
	};

	/**
  * Key for placeholder map values that need to access to the response data returned from subject data request.
  *
  * @type {string}
  */
	var placeholderValueKey = 'requestKey';

	/**
  * Selector for the email list item.
  *
  * @type {String}
  */
	var emailListItemSelector = '.email-list-item';

	/**
  * Selector for the email list item ID.
  *
  * @type {String}
  */
	var emailListItemEmailSelector = '.email-input';

	/**
  * Selector for the modal content body layer.
  *
  * @type {String}
  */
	var modalContentSelector = '.modal-content';

	/**
  * Placeholder Map
  *
  * Used to replace the placeholder with the respective variables.
  *
  * @type {Object}
  */
	var placeholderMap = {
		'{INVOICE_NUM}': _defineProperty({}, placeholderValueKey, 'invoiceId'),
		'{INVOICE_DATE}': _defineProperty({}, placeholderValueKey, 'date'),
		'{ORDER_ID}': 'id'
	};

	/**
  * Request URL
  * 
  * @type {String}
  */
	var requestUrl = jse.core.config.get('appUrl') + '/admin/gm_pdf_order.php';

	/**
  * Request URL for retrieving subject data.
  *
  * @type {String}
  */
	var subjectDataRequestUrl = jse.core.config.get('appUrl') + '/admin/admin.php';

	/**
  * Loading spinner instance.
  *
  * @type {jQuery|null}
  */
	var $spinner = null;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Show/hide loading spinner.
  *
  * @param {Boolean} doShow Show the loading spinner?
  */
	function _toggleSpinner(doShow) {
		if (doShow) {
			$spinner = jse.libs.loading_spinner.show($this.find(modalContentSelector), $this.css('z-index'));
		} else {
			jse.libs.loading_spinner.hide($spinner);
		}
	}

	/**
  * Parse subject and replace the placeholders with the variables.
  *
  * @param {Object} orderData Order data.
  * @param {Object} subjectData Subject data.
  *
  * @return {String}
  */
	function _getParsedSubject(orderData, subjectData) {
		// Subject.
		var subject = module.bindings.subject.get();

		// Placeholder iterator function.
		var placeholderIterator = function placeholderIterator(placeholder) {
			// Value from placeholder map.
			var placeholderMapValue = placeholderMap[placeholder];

			// Get data from response of subject data request?
			var doesNeedAccessToSubjectData = typeof placeholderMap[placeholder] !== 'string';

			// Replaced value.
			var replacedValue = doesNeedAccessToSubjectData ? subjectData[placeholderMapValue[placeholderValueKey]] : orderData[placeholderMapValue];

			subject = subject.replace(placeholder, replacedValue);
		};

		// Iterate over the placeholders and replace the values.
		Object.keys(placeholderMap).forEach(placeholderIterator);

		return subject;
	}

	/**
  * Handles the successful delivery of all messages.
  */
	function _handleDeliverySuccess() {
		var message = jse.core.lang.translate('BULK_MAIL_SUCCESS', 'gm_send_order');

		// Show success message in the admin info box.
		jse.libs.info_box.service.addSuccessMessage(message);

		$('.orders .table-main').DataTable().ajax.reload();
		$('.orders .table-main').orders_overview_filter('reload');

		// Hide modal and loading spinner.
		_toggleSpinner(false);
		$this.modal('hide');
	}

	/**
  * Handles the failure of the message delivery.
  */
	function _handleDeliveryFail() {
		var title = jse.core.lang.translate('error', 'messages');
		var content = jse.core.lang.translate('BULK_MAIL_UNSUCCESS', 'gm_send_order');

		// Show error message in a modal.
		jse.libs.modal.message({ title: title, content: content });

		// Hide modal and the loading spinner and re-enable the send button.
		_toggleSpinner(false);
		$this.modal('hide');
	}

	/**
  * Send the modal data to the form through an AJAX call.
  */
	function _onSendClick() {
		// Collection of requests in promise format.
		var promises = [];

		// Email list item elements.
		var $emailListItems = $this.find(emailListItemSelector);

		// Abort and hide modal on empty email list entries.
		if (!$emailListItems.length) {
			$this.modal('hide');
			return;
		}

		// Show loading spinner.
		_toggleSpinner(true);

		// Fill orders array with data.
		$emailListItems.each(function (index, element) {
			// Order data.
			var orderData = $(element).data('invoice');

			// Email address entered in input field.
			var enteredEmail = $(element).find(emailListItemEmailSelector).val();

			// Promise wrapper for subject data AJAX request.
			var getSubjectPromise = new Promise(function (resolve, reject) {
				// Request options.
				var options = {
					url: subjectDataRequestUrl,
					method: 'GET',
					data: {
						do: 'OrdersModalsAjax/GetEmailInvoiceSubjectData',
						id: orderData.id,
						date: orderData.purchaseDate.date,
						pageToken: jse.core.config.get('pageToken')
					},
					dataType: 'json'
				};

				// Create AJAX request.
				var request = $.ajax(options);

				// Resolve promise on success.
				request.done(function (response) {
					return resolve(response);
				});

				// Reject promise on fail.
				request.fail(function () {
					return reject();
				});
			});

			// Promise wrapper for AJAX requests.
			var promise = new Promise(function (resolve, reject) {
				// Get subject data.
				getSubjectPromise.then(function (subjectData) {
					// Request GET parameters to send.
					var getParameters = {
						oID: orderData.id,
						type: 'invoice',
						mail: '1',
						gm_quick_mail: '1'
					};

					// Composed request URL.
					var url = requestUrl + '?' + $.param(getParameters);

					// Data to send.
					var data = {
						gm_mail: enteredEmail,
						gm_subject: _getParsedSubject(orderData, subjectData)
					};

					// Create AJAX request.
					var request = $.ajax({ method: 'POST', url: url, data: data });

					// Resolve promise on success.
					request.done(function (response) {
						return resolve(response);
					});

					// Reject promise on fail.
					request.fail(function () {
						return reject();
					});
				}).catch(reject);
			});

			// Add promise to array.
			promises.push(promise);
		});

		// Wait for all promise to respond and handle success/error.
		Promise.all(promises).then(_handleDeliverySuccess).catch(_handleDeliveryFail);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', '.btn.send', _onSendClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvYnVsa19lbWFpbF9pbnZvaWNlLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiYmluZGluZ3MiLCJzdWJqZWN0IiwiZmluZCIsInBsYWNlaG9sZGVyVmFsdWVLZXkiLCJlbWFpbExpc3RJdGVtU2VsZWN0b3IiLCJlbWFpbExpc3RJdGVtRW1haWxTZWxlY3RvciIsIm1vZGFsQ29udGVudFNlbGVjdG9yIiwicGxhY2Vob2xkZXJNYXAiLCJyZXF1ZXN0VXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsInN1YmplY3REYXRhUmVxdWVzdFVybCIsIiRzcGlubmVyIiwiX3RvZ2dsZVNwaW5uZXIiLCJkb1Nob3ciLCJsaWJzIiwibG9hZGluZ19zcGlubmVyIiwic2hvdyIsImNzcyIsImhpZGUiLCJfZ2V0UGFyc2VkU3ViamVjdCIsIm9yZGVyRGF0YSIsInN1YmplY3REYXRhIiwicGxhY2Vob2xkZXJJdGVyYXRvciIsInBsYWNlaG9sZGVyTWFwVmFsdWUiLCJwbGFjZWhvbGRlciIsImRvZXNOZWVkQWNjZXNzVG9TdWJqZWN0RGF0YSIsInJlcGxhY2VkVmFsdWUiLCJyZXBsYWNlIiwiT2JqZWN0Iiwia2V5cyIsImZvckVhY2giLCJfaGFuZGxlRGVsaXZlcnlTdWNjZXNzIiwibWVzc2FnZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJpbmZvX2JveCIsInNlcnZpY2UiLCJhZGRTdWNjZXNzTWVzc2FnZSIsIkRhdGFUYWJsZSIsImFqYXgiLCJyZWxvYWQiLCJvcmRlcnNfb3ZlcnZpZXdfZmlsdGVyIiwibW9kYWwiLCJfaGFuZGxlRGVsaXZlcnlGYWlsIiwidGl0bGUiLCJjb250ZW50IiwiX29uU2VuZENsaWNrIiwicHJvbWlzZXMiLCIkZW1haWxMaXN0SXRlbXMiLCJsZW5ndGgiLCJlYWNoIiwiaW5kZXgiLCJlbGVtZW50IiwiZW50ZXJlZEVtYWlsIiwidmFsIiwiZ2V0U3ViamVjdFByb21pc2UiLCJQcm9taXNlIiwicmVzb2x2ZSIsInJlamVjdCIsIm9wdGlvbnMiLCJ1cmwiLCJtZXRob2QiLCJkbyIsImlkIiwiZGF0ZSIsInB1cmNoYXNlRGF0ZSIsInBhZ2VUb2tlbiIsImRhdGFUeXBlIiwicmVxdWVzdCIsImRvbmUiLCJyZXNwb25zZSIsImZhaWwiLCJwcm9taXNlIiwidGhlbiIsImdldFBhcmFtZXRlcnMiLCJvSUQiLCJ0eXBlIiwibWFpbCIsImdtX3F1aWNrX21haWwiLCJwYXJhbSIsImdtX21haWwiLCJnbV9zdWJqZWN0IiwiY2F0Y2giLCJwdXNoIiwiYWxsIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7O0FBR0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixvQkFBdEIsRUFBNEMsQ0FBQyxPQUFELEVBQVUsaUJBQVYsQ0FBNUMsRUFBMEUsVUFBU0MsSUFBVCxFQUFlOztBQUV4Rjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVM7QUFDZEksWUFBVSxFQUFDQyxTQUFTSCxNQUFNSSxJQUFOLENBQVcsVUFBWCxDQUFWO0FBREksRUFBZjs7QUFJQTs7Ozs7QUFLQSxLQUFNQyxzQkFBc0IsWUFBNUI7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsd0JBQXdCLGtCQUE5Qjs7QUFFQTs7Ozs7QUFLQSxLQUFNQyw2QkFBNkIsY0FBbkM7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsdUJBQXVCLGdCQUE3Qjs7QUFFQTs7Ozs7OztBQU9BLEtBQU1DLGlCQUFpQjtBQUN0Qix1Q0FBbUJKLG1CQUFuQixFQUF5QyxXQUF6QyxDQURzQjtBQUV0Qix3Q0FBb0JBLG1CQUFwQixFQUEwQyxNQUExQyxDQUZzQjtBQUd0QixnQkFBYztBQUhRLEVBQXZCOztBQU1BOzs7OztBQUtBLEtBQU1LLGFBQWFDLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FB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QUFDckJDLFdBQUt2RCxVQUFVMkMsRUFETTtBQUVyQmEsWUFBTSxTQUZlO0FBR3JCQyxZQUFNLEdBSGU7QUFJckJDLHFCQUFlO0FBSk0sTUFBdEI7O0FBT0E7QUFDQSxTQUFNbEIsTUFBTXZELGFBQWEsR0FBYixHQUFtQlQsRUFBRW1GLEtBQUYsQ0FBUUwsYUFBUixDQUEvQjs7QUFFQTtBQUNBLFNBQU1oRixPQUFPO0FBQ1pzRixlQUFTM0IsWUFERztBQUVaNEIsa0JBQVk5RCxrQkFBa0JDLFNBQWxCLEVBQTZCQyxXQUE3QjtBQUZBLE1BQWI7O0FBS0E7QUFDQSxTQUFNK0MsVUFBVXhFLEVBQUUyQyxJQUFGLENBQU8sRUFBQ3NCLFFBQVEsTUFBVCxFQUFpQkQsUUFBakIsRUFBc0JsRSxVQUF0QixFQUFQLENBQWhCOztBQUVBO0FBQ0EwRSxhQUFRQyxJQUFSLENBQWE7QUFBQSxhQUFZWixRQUFRYSxRQUFSLENBQVo7QUFBQSxNQUFiOztBQUVBO0FBQ0FGLGFBQVFHLElBQVIsQ0FBYTtBQUFBLGFBQU1iLFFBQU47QUFBQSxNQUFiO0FBQ0EsS0EzQkYsRUE0QkV3QixLQTVCRixDQTRCUXhCLE1BNUJSO0FBNkJBLElBL0JlLENBQWhCOztBQWlDQTtBQUNBWCxZQUFTb0MsSUFBVCxDQUFjWCxPQUFkO0FBQ0EsR0FwRUQ7O0FBc0VBO0FBQ0FoQixVQUFRNEIsR0FBUixDQUFZckMsUUFBWixFQUNFMEIsSUFERixDQUNPMUMsc0JBRFAsRUFFRW1ELEtBRkYsQ0FFUXZDLG1CQUZSO0FBR0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBbEQsUUFBTzRGLElBQVAsR0FBYyxVQUFTaEIsSUFBVCxFQUFlO0FBQzVCMUUsUUFBTTJGLEVBQU4sQ0FBUyxPQUFULEVBQWtCLFdBQWxCLEVBQStCeEMsWUFBL0I7QUFDQXVCO0FBQ0EsRUFIRDs7QUFLQSxRQUFPNUUsTUFBUDtBQUNBLENBclJEIiwiZmlsZSI6Im9yZGVycy9tb2RhbHMvYnVsa19lbWFpbF9pbnZvaWNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBidWxrX2VtYWlsX2ludm9pY2UuanMgMjAxNi0wNi0xNlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQnVsayBFbWFpbCBJbnZvaWNlIE1vZGFsIENvbnRyb2xsZXJcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKCdidWxrX2VtYWlsX2ludm9pY2UnLCBbJ21vZGFsJywgJ2xvYWRpbmdfc3Bpbm5lciddLCBmdW5jdGlvbihkYXRhKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gVkFSSUFCTEVTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxuXHQgKlxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHQgKi9cblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuXHRcblx0LyoqXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxuXHQgKlxuXHQgKiBAdHlwZSB7T2JqZWN0fVxuXHQgKi9cblx0Y29uc3QgbW9kdWxlID0ge1xuXHRcdGJpbmRpbmdzOiB7c3ViamVjdDogJHRoaXMuZmluZCgnLnN1YmplY3QnKX1cblx0fTtcblx0XG5cdC8qKlxuXHQgKiBLZXkgZm9yIHBsYWNlaG9sZGVyIG1hcCB2YWx1ZXMgdGhhdCBuZWVkIHRvIGFjY2VzcyB0byB0aGUgcmVzcG9uc2UgZGF0YSByZXR1cm5lZCBmcm9tIHN1YmplY3QgZGF0YSByZXF1ZXN0LlxuXHQgKlxuXHQgKiBAdHlwZSB7c3RyaW5nfVxuXHQgKi9cblx0Y29uc3QgcGxhY2Vob2xkZXJWYWx1ZUtleSA9ICdyZXF1ZXN0S2V5Jztcblx0XG5cdC8qKlxuXHQgKiBTZWxlY3RvciBmb3IgdGhlIGVtYWlsIGxpc3QgaXRlbS5cblx0ICpcblx0ICogQHR5cGUge1N0cmluZ31cblx0ICovXG5cdGNvbnN0IGVtYWlsTGlzdEl0ZW1TZWxlY3RvciA9ICcuZW1haWwtbGlzdC1pdGVtJztcblx0XG5cdC8qKlxuXHQgKiBTZWxlY3RvciBmb3IgdGhlIGVtYWlsIGxpc3QgaXRlbSBJRC5cblx0ICpcblx0ICogQHR5cGUge1N0cmluZ31cblx0ICovXG5cdGNvbnN0IGVtYWlsTGlzdEl0ZW1FbWFpbFNlbGVjdG9yID0gJy5lbWFpbC1pbnB1dCc7XG5cdFxuXHQvKipcblx0ICogU2VsZWN0b3IgZm9yIHRoZSBtb2RhbCBjb250ZW50IGJvZHkgbGF5ZXIuXG5cdCAqXG5cdCAqIEB0eXBlIHtTdHJpbmd9XG5cdCAqL1xuXHRjb25zdCBtb2RhbENvbnRlbnRTZWxlY3RvciA9ICcubW9kYWwtY29udGVudCc7XG5cdFxuXHQvKipcblx0ICogUGxhY2Vob2xkZXIgTWFwXG5cdCAqXG5cdCAqIFVzZWQgdG8gcmVwbGFjZSB0aGUgcGxhY2Vob2xkZXIgd2l0aCB0aGUgcmVzcGVjdGl2ZSB2YXJpYWJsZXMuXG5cdCAqXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCBwbGFjZWhvbGRlck1hcCA9IHtcblx0XHQne0lOVk9JQ0VfTlVNfSc6IHtbcGxhY2Vob2xkZXJWYWx1ZUtleV06ICdpbnZvaWNlSWQnfSxcblx0XHQne0lOVk9JQ0VfREFURX0nOiB7W3BsYWNlaG9sZGVyVmFsdWVLZXldOiAnZGF0ZSd9LFxuXHRcdCd7T1JERVJfSUR9JzogJ2lkJ1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJlcXVlc3QgVVJMXG5cdCAqIFxuXHQgKiBAdHlwZSB7U3RyaW5nfVxuXHQgKi9cblx0Y29uc3QgcmVxdWVzdFVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9nbV9wZGZfb3JkZXIucGhwJztcblx0XG5cdC8qKlxuXHQgKiBSZXF1ZXN0IFVSTCBmb3IgcmV0cmlldmluZyBzdWJqZWN0IGRhdGEuXG5cdCAqXG5cdCAqIEB0eXBlIHtTdHJpbmd9XG5cdCAqL1xuXHRjb25zdCBzdWJqZWN0RGF0YVJlcXVlc3RVcmwgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwJztcblx0XG5cdC8qKlxuXHQgKiBMb2FkaW5nIHNwaW5uZXIgaW5zdGFuY2UuXG5cdCAqXG5cdCAqIEB0eXBlIHtqUXVlcnl8bnVsbH1cblx0ICovXG5cdGxldCAkc3Bpbm5lciA9IG51bGw7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gRlVOQ1RJT05TXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIFNob3cvaGlkZSBsb2FkaW5nIHNwaW5uZXIuXG5cdCAqXG5cdCAqIEBwYXJhbSB7Qm9vbGVhbn0gZG9TaG93IFNob3cgdGhlIGxvYWRpbmcgc3Bpbm5lcj9cblx0ICovXG5cdGZ1bmN0aW9uIF90b2dnbGVTcGlubmVyKGRvU2hvdykge1xuXHRcdGlmIChkb1Nob3cpIHtcblx0XHRcdCRzcGlubmVyID0ganNlLmxpYnMubG9hZGluZ19zcGlubmVyLnNob3coJHRoaXMuZmluZChtb2RhbENvbnRlbnRTZWxlY3RvciksICR0aGlzLmNzcygnei1pbmRleCcpKTtcblx0XHR9IGVsc2Uge1xuXHRcdFx0anNlLmxpYnMubG9hZGluZ19zcGlubmVyLmhpZGUoJHNwaW5uZXIpO1xuXHRcdH1cblx0fVxuXHRcblx0LyoqXG5cdCAqIFBhcnNlIHN1YmplY3QgYW5kIHJlcGxhY2UgdGhlIHBsYWNlaG9sZGVycyB3aXRoIHRoZSB2YXJpYWJsZXMuXG5cdCAqXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBvcmRlckRhdGEgT3JkZXIgZGF0YS5cblx0ICogQHBhcmFtIHtPYmplY3R9IHN1YmplY3REYXRhIFN1YmplY3QgZGF0YS5cblx0ICpcblx0ICogQHJldHVybiB7U3RyaW5nfVxuXHQgKi9cblx0ZnVuY3Rpb24gX2dldFBhcnNlZFN1YmplY3Qob3JkZXJEYXRhLCBzdWJqZWN0RGF0YSkge1xuXHRcdC8vIFN1YmplY3QuXG5cdFx0bGV0IHN1YmplY3QgPSBtb2R1bGUuYmluZGluZ3Muc3ViamVjdC5nZXQoKTtcblx0XHRcblx0XHQvLyBQbGFjZWhvbGRlciBpdGVyYXRvciBmdW5jdGlvbi5cblx0XHRjb25zdCBwbGFjZWhvbGRlckl0ZXJhdG9yID0gcGxhY2Vob2xkZXIgPT4ge1xuXHRcdFx0Ly8gVmFsdWUgZnJvbSBwbGFjZWhvbGRlciBtYXAuXG5cdFx0XHRjb25zdCBwbGFjZWhvbGRlck1hcFZhbHVlID0gcGxhY2Vob2xkZXJNYXBbcGxhY2Vob2xkZXJdO1xuXHRcdFx0XG5cdFx0XHQvLyBHZXQgZGF0YSBmcm9tIHJlc3BvbnNlIG9mIHN1YmplY3QgZGF0YSByZXF1ZXN0P1xuXHRcdFx0Y29uc3QgZG9lc05lZWRBY2Nlc3NUb1N1YmplY3REYXRhID0gdHlwZW9mIHBsYWNlaG9sZGVyTWFwW3BsYWNlaG9sZGVyXSAhPT0gJ3N0cmluZyc7XG5cdFx0XHRcblx0XHRcdC8vIFJlcGxhY2VkIHZhbHVlLlxuXHRcdFx0Y29uc3QgcmVwbGFjZWRWYWx1ZSA9IGRvZXNOZWVkQWNjZXNzVG9TdWJqZWN0RGF0YSA/XG5cdFx0XHQgICAgICAgICAgICAgICAgICAgICAgc3ViamVjdERhdGFbcGxhY2Vob2xkZXJNYXBWYWx1ZVtwbGFjZWhvbGRlclZhbHVlS2V5XV0gOlxuXHRcdFx0ICAgICAgICAgICAgICAgICAgICAgIG9yZGVyRGF0YVtwbGFjZWhvbGRlck1hcFZhbHVlXTtcblx0XHRcdFxuXHRcdFx0c3ViamVjdCA9IHN1YmplY3QucmVwbGFjZShwbGFjZWhvbGRlciwgcmVwbGFjZWRWYWx1ZSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBJdGVyYXRlIG92ZXIgdGhlIHBsYWNlaG9sZGVycyBhbmQgcmVwbGFjZSB0aGUgdmFsdWVzLlxuXHRcdE9iamVjdFxuXHRcdFx0LmtleXMocGxhY2Vob2xkZXJNYXApXG5cdFx0XHQuZm9yRWFjaChwbGFjZWhvbGRlckl0ZXJhdG9yKTtcblx0XHRcblx0XHRyZXR1cm4gc3ViamVjdDtcblx0fVxuXHRcblx0LyoqXG5cdCAqIEhhbmRsZXMgdGhlIHN1Y2Nlc3NmdWwgZGVsaXZlcnkgb2YgYWxsIG1lc3NhZ2VzLlxuXHQgKi9cblx0ZnVuY3Rpb24gX2hhbmRsZURlbGl2ZXJ5U3VjY2VzcygpIHtcblx0XHRjb25zdCBtZXNzYWdlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0JVTEtfTUFJTF9TVUNDRVNTJywgJ2dtX3NlbmRfb3JkZXInKTtcblx0XHRcblx0XHQvLyBTaG93IHN1Y2Nlc3MgbWVzc2FnZSBpbiB0aGUgYWRtaW4gaW5mbyBib3guXG5cdFx0anNlLmxpYnMuaW5mb19ib3guc2VydmljZS5hZGRTdWNjZXNzTWVzc2FnZShtZXNzYWdlKTtcblx0XHRcblx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcblx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcigncmVsb2FkJyk7XG5cdFx0XG5cdFx0Ly8gSGlkZSBtb2RhbCBhbmQgbG9hZGluZyBzcGlubmVyLlxuXHRcdF90b2dnbGVTcGlubmVyKGZhbHNlKTtcblx0XHQkdGhpcy5tb2RhbCgnaGlkZScpO1xuXHR9XG5cdFxuXHQvKipcblx0ICogSGFuZGxlcyB0aGUgZmFpbHVyZSBvZiB0aGUgbWVzc2FnZSBkZWxpdmVyeS5cblx0ICovXG5cdGZ1bmN0aW9uIF9oYW5kbGVEZWxpdmVyeUZhaWwoKSB7XG5cdFx0Y29uc3QgdGl0bGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKTtcblx0XHRjb25zdCBjb250ZW50ID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0JVTEtfTUFJTF9VTlNVQ0NFU1MnLCAnZ21fc2VuZF9vcmRlcicpO1xuXHRcdFxuXHRcdC8vIFNob3cgZXJyb3IgbWVzc2FnZSBpbiBhIG1vZGFsLlxuXHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe3RpdGxlLCBjb250ZW50fSk7XG5cdFx0XG5cdFx0Ly8gSGlkZSBtb2RhbCBhbmQgdGhlIGxvYWRpbmcgc3Bpbm5lciBhbmQgcmUtZW5hYmxlIHRoZSBzZW5kIGJ1dHRvbi5cblx0XHRfdG9nZ2xlU3Bpbm5lcihmYWxzZSk7XG5cdFx0JHRoaXMubW9kYWwoJ2hpZGUnKTtcblx0fVxuXHRcblx0LyoqXG5cdCAqIFNlbmQgdGhlIG1vZGFsIGRhdGEgdG8gdGhlIGZvcm0gdGhyb3VnaCBhbiBBSkFYIGNhbGwuXG5cdCAqL1xuXHRmdW5jdGlvbiBfb25TZW5kQ2xpY2soKSB7XG5cdFx0Ly8gQ29sbGVjdGlvbiBvZiByZXF1ZXN0cyBpbiBwcm9taXNlIGZvcm1hdC5cblx0XHRjb25zdCBwcm9taXNlcyA9IFtdO1xuXHRcdFxuXHRcdC8vIEVtYWlsIGxpc3QgaXRlbSBlbGVtZW50cy5cblx0XHRjb25zdCAkZW1haWxMaXN0SXRlbXMgPSAkdGhpcy5maW5kKGVtYWlsTGlzdEl0ZW1TZWxlY3Rvcik7XG5cdFx0XG5cdFx0Ly8gQWJvcnQgYW5kIGhpZGUgbW9kYWwgb24gZW1wdHkgZW1haWwgbGlzdCBlbnRyaWVzLlxuXHRcdGlmICghJGVtYWlsTGlzdEl0ZW1zLmxlbmd0aCkge1xuXHRcdFx0JHRoaXMubW9kYWwoJ2hpZGUnKTtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0Ly8gU2hvdyBsb2FkaW5nIHNwaW5uZXIuXG5cdFx0X3RvZ2dsZVNwaW5uZXIodHJ1ZSk7XG5cdFx0XG5cdFx0Ly8gRmlsbCBvcmRlcnMgYXJyYXkgd2l0aCBkYXRhLlxuXHRcdCRlbWFpbExpc3RJdGVtcy5lYWNoKChpbmRleCwgZWxlbWVudCkgPT4ge1xuXHRcdFx0Ly8gT3JkZXIgZGF0YS5cblx0XHRcdGNvbnN0IG9yZGVyRGF0YSA9ICQoZWxlbWVudCkuZGF0YSgnaW52b2ljZScpO1xuXHRcdFx0XG5cdFx0XHQvLyBFbWFpbCBhZGRyZXNzIGVudGVyZWQgaW4gaW5wdXQgZmllbGQuXG5cdFx0XHRjb25zdCBlbnRlcmVkRW1haWwgPSAkKGVsZW1lbnQpLmZpbmQoZW1haWxMaXN0SXRlbUVtYWlsU2VsZWN0b3IpLnZhbCgpO1xuXHRcdFx0XG5cdFx0XHQvLyBQcm9taXNlIHdyYXBwZXIgZm9yIHN1YmplY3QgZGF0YSBBSkFYIHJlcXVlc3QuXG5cdFx0XHRjb25zdCBnZXRTdWJqZWN0UHJvbWlzZSA9IG5ldyBQcm9taXNlKChyZXNvbHZlLCByZWplY3QpID0+IHtcblx0XHRcdFx0Ly8gUmVxdWVzdCBvcHRpb25zLlxuXHRcdFx0XHRjb25zdCBvcHRpb25zID0ge1xuXHRcdFx0XHRcdHVybDogc3ViamVjdERhdGFSZXF1ZXN0VXJsLFxuXHRcdFx0XHRcdG1ldGhvZDogJ0dFVCcsXG5cdFx0XHRcdFx0ZGF0YToge1xuXHRcdFx0XHRcdFx0ZG86ICdPcmRlcnNNb2RhbHNBamF4L0dldEVtYWlsSW52b2ljZVN1YmplY3REYXRhJyxcblx0XHRcdFx0XHRcdGlkOiBvcmRlckRhdGEuaWQsXG5cdFx0XHRcdFx0XHRkYXRlOiBvcmRlckRhdGEucHVyY2hhc2VEYXRlLmRhdGUsXG5cdFx0XHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHRcdH07XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBDcmVhdGUgQUpBWCByZXF1ZXN0LlxuXHRcdFx0XHRjb25zdCByZXF1ZXN0ID0gJC5hamF4KG9wdGlvbnMpO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUmVzb2x2ZSBwcm9taXNlIG9uIHN1Y2Nlc3MuXG5cdFx0XHRcdHJlcXVlc3QuZG9uZShyZXNwb25zZSA9PiByZXNvbHZlKHJlc3BvbnNlKSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBSZWplY3QgcHJvbWlzZSBvbiBmYWlsLlxuXHRcdFx0XHRyZXF1ZXN0LmZhaWwoKCkgPT4gcmVqZWN0KCkpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIFByb21pc2Ugd3JhcHBlciBmb3IgQUpBWCByZXF1ZXN0cy5cblx0XHRcdGNvbnN0IHByb21pc2UgPSBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG5cdFx0XHRcdC8vIEdldCBzdWJqZWN0IGRhdGEuXG5cdFx0XHRcdGdldFN1YmplY3RQcm9taXNlXG5cdFx0XHRcdFx0LnRoZW4oc3ViamVjdERhdGEgPT4ge1xuXHRcdFx0XHRcdFx0Ly8gUmVxdWVzdCBHRVQgcGFyYW1ldGVycyB0byBzZW5kLlxuXHRcdFx0XHRcdFx0Y29uc3QgZ2V0UGFyYW1ldGVycyA9IHtcblx0XHRcdFx0XHRcdFx0b0lEOiBvcmRlckRhdGEuaWQsXG5cdFx0XHRcdFx0XHRcdHR5cGU6ICdpbnZvaWNlJyxcblx0XHRcdFx0XHRcdFx0bWFpbDogJzEnLFxuXHRcdFx0XHRcdFx0XHRnbV9xdWlja19tYWlsOiAnMSdcblx0XHRcdFx0XHRcdH07XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdC8vIENvbXBvc2VkIHJlcXVlc3QgVVJMLlxuXHRcdFx0XHRcdFx0Y29uc3QgdXJsID0gcmVxdWVzdFVybCArICc/JyArICQucGFyYW0oZ2V0UGFyYW1ldGVycyk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdC8vIERhdGEgdG8gc2VuZC5cblx0XHRcdFx0XHRcdGNvbnN0IGRhdGEgPSB7XG5cdFx0XHRcdFx0XHRcdGdtX21haWw6IGVudGVyZWRFbWFpbCxcblx0XHRcdFx0XHRcdFx0Z21fc3ViamVjdDogX2dldFBhcnNlZFN1YmplY3Qob3JkZXJEYXRhLCBzdWJqZWN0RGF0YSlcblx0XHRcdFx0XHRcdH07XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdC8vIENyZWF0ZSBBSkFYIHJlcXVlc3QuXG5cdFx0XHRcdFx0XHRjb25zdCByZXF1ZXN0ID0gJC5hamF4KHttZXRob2Q6ICdQT1NUJywgdXJsLCBkYXRhfSk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdC8vIFJlc29sdmUgcHJvbWlzZSBvbiBzdWNjZXNzLlxuXHRcdFx0XHRcdFx0cmVxdWVzdC5kb25lKHJlc3BvbnNlID0+IHJlc29sdmUocmVzcG9uc2UpKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0Ly8gUmVqZWN0IHByb21pc2Ugb24gZmFpbC5cblx0XHRcdFx0XHRcdHJlcXVlc3QuZmFpbCgoKSA9PiByZWplY3QoKSk7XG5cdFx0XHRcdFx0fSlcblx0XHRcdFx0XHQuY2F0Y2gocmVqZWN0KTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQvLyBBZGQgcHJvbWlzZSB0byBhcnJheS5cblx0XHRcdHByb21pc2VzLnB1c2gocHJvbWlzZSk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0Ly8gV2FpdCBmb3IgYWxsIHByb21pc2UgdG8gcmVzcG9uZCBhbmQgaGFuZGxlIHN1Y2Nlc3MvZXJyb3IuXG5cdFx0UHJvbWlzZS5hbGwocHJvbWlzZXMpXG5cdFx0XHQudGhlbihfaGFuZGxlRGVsaXZlcnlTdWNjZXNzKVxuXHRcdFx0LmNhdGNoKF9oYW5kbGVEZWxpdmVyeUZhaWwpO1xuXHR9XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gSU5JVElBTElaQVRJT05cblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHQkdGhpcy5vbignY2xpY2snLCAnLmJ0bi5zZW5kJywgX29uU2VuZENsaWNrKTtcblx0XHRkb25lKCk7XG5cdH07XG5cdFxuXHRyZXR1cm4gbW9kdWxlO1xufSk7Il19
