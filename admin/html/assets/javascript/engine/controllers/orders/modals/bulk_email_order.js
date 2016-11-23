'use strict';

/* --------------------------------------------------------------
 bulk_email_order.js 2016-05-25
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Bulk Email Order Modal Controller
 */
gx.controllers.module('bulk_email_order', ['modal', 'loading_spinner'], function (data) {

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
  * Placeholder map.
  * Used to replace the placeholder with the respective variables.
  * 
  * Format: '{Placeholder}' : 'Attribute'
  * 
  * @type {Object}
  */
	var placeholderMap = {
		'{ORDER_ID}': 'id',
		'{ORDER_DATE}': 'purchaseDate'
	};

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
  * 
  * @return {String}
  */
	function _getParsedSubject(orderData) {
		// Subject.
		var subject = module.bindings.subject.get();

		// Iterate over the placeholders and replace the values.
		Object.keys(placeholderMap).forEach(function (placeholder) {
			return subject = subject.replace(placeholder, orderData[placeholderMap[placeholder]]);
		});

		return subject;
	}

	/**
  * Handles the successful delivery of all messages.
  */
	function _handleDeliverySuccess() {
		var message = jse.core.lang.translate('BULK_MAIL_SUCCESS', 'gm_send_order');

		// Show success message in the admin info box.
		jse.libs.info_box.service.addSuccessMessage(message);

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

		// Hide modal and the loading spinner and reenable the send button.
		_toggleSpinner(false);
		$this.modal('hide');
	}

	/**
  * Send the modal data to the form through an AJAX call.
  */
	function _onSendClick() {
		// Send type.
		var REQUEST_SEND_TYPE = 'send_order';

		// Request base URL.
		var REQUEST_URL = jse.core.config.get('appUrl') + '/admin/gm_send_order.php';

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
			var orderData = $(element).data('order');

			// Format the purchase date.
			var dateFormat = jse.core.config.get('languageCode') === 'de' ? 'DD.MM.YY' : 'MM.DD.YY';
			orderData.purchaseDate = moment(orderData.purchaseDate.date).format(dateFormat);

			// Email address entered in input field.
			var enteredEmail = $(element).find(emailListItemEmailSelector).val();

			// Request GET parameters to send.
			var getParameters = {
				oID: orderData.id,
				type: REQUEST_SEND_TYPE
			};

			// Composed request URL.
			var url = REQUEST_URL + '?' + $.param(getParameters);

			// Data to send.
			var data = {
				gm_mail: enteredEmail,
				gm_subject: _getParsedSubject(orderData)
			};

			// Promise wrapper for AJAX response.
			var promise = new Promise(function (resolve, reject) {
				// Create AJAX request.
				var request = $.ajax({ method: 'POST', url: url, data: data });

				request.success(function () {
					var orderId = getParameters.oID;
					var $tableRow = $('tbody tr#' + orderId);

					// Remove the e-mail symbol
					$tableRow.find('td.actions i.tooltip-confirmation-not-sent').remove();
				});

				// Resolve promise on success.
				request.done(function (response) {
					return resolve(response);
				});

				// Reject promise on fail.
				request.fail(function () {
					return reject();
				});
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvYnVsa19lbWFpbF9vcmRlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImJpbmRpbmdzIiwic3ViamVjdCIsImZpbmQiLCJlbWFpbExpc3RJdGVtU2VsZWN0b3IiLCJlbWFpbExpc3RJdGVtRW1haWxTZWxlY3RvciIsIm1vZGFsQ29udGVudFNlbGVjdG9yIiwicGxhY2Vob2xkZXJNYXAiLCIkc3Bpbm5lciIsIl90b2dnbGVTcGlubmVyIiwiZG9TaG93IiwianNlIiwibGlicyIsImxvYWRpbmdfc3Bpbm5lciIsInNob3ciLCJjc3MiLCJoaWRlIiwiX2dldFBhcnNlZFN1YmplY3QiLCJvcmRlckRhdGEiLCJnZXQiLCJPYmplY3QiLCJrZXlzIiwiZm9yRWFjaCIsInJlcGxhY2UiLCJwbGFjZWhvbGRlciIsIl9oYW5kbGVEZWxpdmVyeVN1Y2Nlc3MiLCJtZXNzYWdlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJpbmZvX2JveCIsInNlcnZpY2UiLCJhZGRTdWNjZXNzTWVzc2FnZSIsIm1vZGFsIiwiX2hhbmRsZURlbGl2ZXJ5RmFpbCIsInRpdGxlIiwiY29udGVudCIsIl9vblNlbmRDbGljayIsIlJFUVVFU1RfU0VORF9UWVBFIiwiUkVRVUVTVF9VUkwiLCJjb25maWciLCJwcm9taXNlcyIsIiRlbWFpbExpc3RJdGVtcyIsImxlbmd0aCIsImVhY2giLCJpbmRleCIsImVsZW1lbnQiLCJkYXRlRm9ybWF0IiwicHVyY2hhc2VEYXRlIiwibW9tZW50IiwiZGF0ZSIsImZvcm1hdCIsImVudGVyZWRFbWFpbCIsInZhbCIsImdldFBhcmFtZXRlcnMiLCJvSUQiLCJpZCIsInR5cGUiLCJ1cmwiLCJwYXJhbSIsImdtX21haWwiLCJnbV9zdWJqZWN0IiwicHJvbWlzZSIsIlByb21pc2UiLCJyZXNvbHZlIiwicmVqZWN0IiwicmVxdWVzdCIsImFqYXgiLCJtZXRob2QiLCJzdWNjZXNzIiwib3JkZXJJZCIsIiR0YWJsZVJvdyIsInJlbW92ZSIsImRvbmUiLCJyZXNwb25zZSIsImZhaWwiLCJwdXNoIiwiYWxsIiwidGhlbiIsImNhdGNoIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0Isa0JBQXRCLEVBQTBDLENBQUMsT0FBRCxFQUFVLGlCQUFWLENBQTFDLEVBQXdFLFVBQVNDLElBQVQsRUFBZTs7QUFFdEY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTO0FBQ2RJLFlBQVUsRUFBQ0MsU0FBU0gsTUFBTUksSUFBTixDQUFXLFVBQVgsQ0FBVjtBQURJLEVBQWY7O0FBSUE7Ozs7O0FBS0EsS0FBTUMsd0JBQXdCLGtCQUE5Qjs7QUFFQTs7Ozs7QUFLQSxLQUFNQyw2QkFBNkIsY0FBbkM7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsdUJBQXVCLGdCQUE3Qjs7QUFFQTs7Ozs7Ozs7QUFRQSxLQUFNQyxpQkFBaUI7QUFDdEIsZ0JBQWMsSUFEUTtBQUV0QixrQkFBZ0I7QUFGTSxFQUF2Qjs7QUFLQTs7Ozs7QUFLQSxLQUFJQyxXQUFXLElBQWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNDLGNBQVQsQ0FBd0JDLE1BQXhCLEVBQWdDO0FBQy9CLE1BQUlBLE1BQUosRUFBWTtBQUNYRixjQUFXRyxJQUFJQyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJDLElBQXpCLENBQThCZixNQUFNSSxJQUFOLENBQVdHLG9CQUFYLENBQTlCLEVBQWdFUCxNQUFNZ0IsR0FBTixDQUFVLFNBQVYsQ0FBaEUsQ0FBWDtBQUNBLEdBRkQsTUFFTztBQUNOSixPQUFJQyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJHLElBQXpCLENBQThCUixRQUE5QjtBQUNBO0FBQ0Q7O0FBRUQ7Ozs7Ozs7QUFPQSxVQUFTUyxp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QWxCO0FBRkEsSUFBYjs7QUFLQTtBQUNBLE9BQU00QyxVQUFVLElBQUlDLE9BQUosQ0FBWSxVQUFDQyxPQUFELEVBQVVDLE1BQVYsRUFBcUI7QUFDaEQ7QUFDQSxRQUFNQyxVQUFVbEUsRUFBRW1FLElBQUYsQ0FBTyxFQUFDQyxRQUFRLE1BQVQsRUFBaUJWLFFBQWpCLEVBQXNCNUQsVUFBdEIsRUFBUCxDQUFoQjs7QUFFQW9FLFlBQVFHLE9BQVIsQ0FBZ0IsWUFBTTtBQUNyQixTQUFNQyxVQUFVaEIsY0FBY0MsR0FBOUI7QUFDQSxTQUFNZ0IsWUFBWXZFLGdCQUFjc0UsT0FBZCxDQUFsQjs7QUFFQTtBQUNBQyxlQUFVcEUsSUFBVixDQUFlLDRDQUFmLEVBQTZEcUUsTUFBN0Q7QUFDQSxLQU5EOztBQVFBO0FBQ0FOLFlBQVFPLElBQVIsQ0FBYTtBQUFBLFlBQVlULFFBQVFVLFFBQVIsQ0FBWjtBQUFBLEtBQWI7O0FBRUE7QUFDQVIsWUFBUVMsSUFBUixDQUFhO0FBQUEsWUFBTVYsUUFBTjtBQUFBLEtBQWI7QUFDQSxJQWpCZSxDQUFoQjs7QUFtQkE7QUFDQXhCLFlBQVNtQyxJQUFULENBQWNkLE9BQWQ7QUFDQSxHQWhERDs7QUFrREE7QUFDQUMsVUFBUWMsR0FBUixDQUFZcEMsUUFBWixFQUNFcUMsSUFERixDQUNPckQsc0JBRFAsRUFFRXNELEtBRkYsQ0FFUTdDLG1CQUZSO0FBR0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBckMsUUFBT21GLElBQVAsR0FBYyxVQUFTUCxJQUFULEVBQWU7QUFDNUIxRSxRQUFNa0YsRUFBTixDQUFTLE9BQVQsRUFBa0IsV0FBbEIsRUFBK0I1QyxZQUEvQjtBQUNBb0M7QUFDQSxFQUhEOztBQUtBLFFBQU81RSxNQUFQO0FBQ0EsQ0E5TkQiLCJmaWxlIjoib3JkZXJzL21vZGFscy9idWxrX2VtYWlsX29yZGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBidWxrX2VtYWlsX29yZGVyLmpzIDIwMTYtMDUtMjVcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIEJ1bGsgRW1haWwgT3JkZXIgTW9kYWwgQ29udHJvbGxlclxuICovXG5neC5jb250cm9sbGVycy5tb2R1bGUoJ2J1bGtfZW1haWxfb3JkZXInLCBbJ21vZGFsJywgJ2xvYWRpbmdfc3Bpbm5lciddLCBmdW5jdGlvbihkYXRhKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gVkFSSUFCTEVTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxuXHQgKiBcblx0ICogQHR5cGUge2pRdWVyeX1cblx0ICovXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcblx0XG5cdC8qKlxuXHQgKiBNb2R1bGUgSW5zdGFuY2Vcblx0ICogXG5cdCAqIEB0eXBlIHtPYmplY3R9XG5cdCAqL1xuXHRjb25zdCBtb2R1bGUgPSB7XG5cdFx0YmluZGluZ3M6IHtzdWJqZWN0OiAkdGhpcy5maW5kKCcuc3ViamVjdCcpfVxuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFNlbGVjdG9yIGZvciB0aGUgZW1haWwgbGlzdCBpdGVtLlxuXHQgKiBcblx0ICogQHR5cGUge1N0cmluZ31cblx0ICovXG5cdGNvbnN0IGVtYWlsTGlzdEl0ZW1TZWxlY3RvciA9ICcuZW1haWwtbGlzdC1pdGVtJztcblx0XG5cdC8qKlxuXHQgKiBTZWxlY3RvciBmb3IgdGhlIGVtYWlsIGxpc3QgaXRlbSBJRC5cblx0ICogXG5cdCAqIEB0eXBlIHtTdHJpbmd9XG5cdCAqL1xuXHRjb25zdCBlbWFpbExpc3RJdGVtRW1haWxTZWxlY3RvciA9ICcuZW1haWwtaW5wdXQnO1xuXHRcblx0LyoqXG5cdCAqIFNlbGVjdG9yIGZvciB0aGUgbW9kYWwgY29udGVudCBib2R5IGxheWVyLlxuXHQgKiBcblx0ICogQHR5cGUge1N0cmluZ31cblx0ICovXG5cdGNvbnN0IG1vZGFsQ29udGVudFNlbGVjdG9yID0gJy5tb2RhbC1jb250ZW50Jztcblx0XG5cdC8qKlxuXHQgKiBQbGFjZWhvbGRlciBtYXAuXG5cdCAqIFVzZWQgdG8gcmVwbGFjZSB0aGUgcGxhY2Vob2xkZXIgd2l0aCB0aGUgcmVzcGVjdGl2ZSB2YXJpYWJsZXMuXG5cdCAqIFxuXHQgKiBGb3JtYXQ6ICd7UGxhY2Vob2xkZXJ9JyA6ICdBdHRyaWJ1dGUnXG5cdCAqIFxuXHQgKiBAdHlwZSB7T2JqZWN0fVxuXHQgKi9cblx0Y29uc3QgcGxhY2Vob2xkZXJNYXAgPSB7XG5cdFx0J3tPUkRFUl9JRH0nOiAnaWQnLFxuXHRcdCd7T1JERVJfREFURX0nOiAncHVyY2hhc2VEYXRlJ1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIExvYWRpbmcgc3Bpbm5lciBpbnN0YW5jZS5cblx0ICogXG5cdCAqIEB0eXBlIHtqUXVlcnl8bnVsbH1cblx0ICovXG5cdGxldCAkc3Bpbm5lciA9IG51bGw7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gRlVOQ1RJT05TXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIFNob3cvaGlkZSBsb2FkaW5nIHNwaW5uZXIuXG5cdCAqIFxuXHQgKiBAcGFyYW0ge0Jvb2xlYW59IGRvU2hvdyBTaG93IHRoZSBsb2FkaW5nIHNwaW5uZXI/XG5cdCAqL1xuXHRmdW5jdGlvbiBfdG9nZ2xlU3Bpbm5lcihkb1Nob3cpIHtcblx0XHRpZiAoZG9TaG93KSB7XG5cdFx0XHQkc3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5zaG93KCR0aGlzLmZpbmQobW9kYWxDb250ZW50U2VsZWN0b3IpLCAkdGhpcy5jc3MoJ3otaW5kZXgnKSk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5oaWRlKCRzcGlubmVyKTtcblx0XHR9XG5cdH1cblx0XG5cdC8qKlxuXHQgKiBQYXJzZSBzdWJqZWN0IGFuZCByZXBsYWNlIHRoZSBwbGFjZWhvbGRlcnMgd2l0aCB0aGUgdmFyaWFibGVzLlxuXHQgKiBcblx0ICogQHBhcmFtIHtPYmplY3R9IG9yZGVyRGF0YSBPcmRlciBkYXRhLlxuXHQgKiBcblx0ICogQHJldHVybiB7U3RyaW5nfVxuXHQgKi9cblx0ZnVuY3Rpb24gX2dldFBhcnNlZFN1YmplY3Qob3JkZXJEYXRhKSB7XG5cdFx0Ly8gU3ViamVjdC5cblx0XHRsZXQgc3ViamVjdCA9IG1vZHVsZS5iaW5kaW5ncy5zdWJqZWN0LmdldCgpO1xuXHRcdFxuXHRcdC8vIEl0ZXJhdGUgb3ZlciB0aGUgcGxhY2Vob2xkZXJzIGFuZCByZXBsYWNlIHRoZSB2YWx1ZXMuXG5cdFx0T2JqZWN0XG5cdFx0XHQua2V5cyhwbGFjZWhvbGRlck1hcClcblx0XHRcdC5mb3JFYWNoKHBsYWNlaG9sZGVyID0+IHN1YmplY3QgPSBzdWJqZWN0LnJlcGxhY2UocGxhY2Vob2xkZXIsIG9yZGVyRGF0YVtwbGFjZWhvbGRlck1hcFtwbGFjZWhvbGRlcl1dKSk7XG5cdFx0XG5cdFx0cmV0dXJuIHN1YmplY3Q7XG5cdH1cblx0XG5cdC8qKlxuXHQgKiBIYW5kbGVzIHRoZSBzdWNjZXNzZnVsIGRlbGl2ZXJ5IG9mIGFsbCBtZXNzYWdlcy5cblx0ICovXG5cdGZ1bmN0aW9uIF9oYW5kbGVEZWxpdmVyeVN1Y2Nlc3MoKSB7XG5cdFx0Y29uc3QgbWVzc2FnZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdCVUxLX01BSUxfU1VDQ0VTUycsICdnbV9zZW5kX29yZGVyJyk7XG5cdFx0XG5cdFx0Ly8gU2hvdyBzdWNjZXNzIG1lc3NhZ2UgaW4gdGhlIGFkbWluIGluZm8gYm94LlxuXHRcdGpzZS5saWJzLmluZm9fYm94LnNlcnZpY2UuYWRkU3VjY2Vzc01lc3NhZ2UobWVzc2FnZSk7XG5cdFx0XG5cdFx0Ly8gSGlkZSBtb2RhbCBhbmQgbG9hZGluZyBzcGlubmVyLlxuXHRcdF90b2dnbGVTcGlubmVyKGZhbHNlKTtcblx0XHQkdGhpcy5tb2RhbCgnaGlkZScpO1xuXHR9XG5cdFxuXHQvKipcblx0ICogSGFuZGxlcyB0aGUgZmFpbHVyZSBvZiB0aGUgbWVzc2FnZSBkZWxpdmVyeS5cblx0ICovXG5cdGZ1bmN0aW9uIF9oYW5kbGVEZWxpdmVyeUZhaWwoKSB7XG5cdFx0Y29uc3QgdGl0bGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKTtcblx0XHRjb25zdCBjb250ZW50ID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0JVTEtfTUFJTF9VTlNVQ0NFU1MnLCAnZ21fc2VuZF9vcmRlcicpO1xuXHRcdFxuXHRcdC8vIFNob3cgZXJyb3IgbWVzc2FnZSBpbiBhIG1vZGFsLlxuXHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe3RpdGxlLCBjb250ZW50fSk7XG5cdFx0XG5cdFx0Ly8gSGlkZSBtb2RhbCBhbmQgdGhlIGxvYWRpbmcgc3Bpbm5lciBhbmQgcmVlbmFibGUgdGhlIHNlbmQgYnV0dG9uLlxuXHRcdF90b2dnbGVTcGlubmVyKGZhbHNlKTtcblx0XHQkdGhpcy5tb2RhbCgnaGlkZScpO1xuXHR9XG5cdFxuXHQvKipcblx0ICogU2VuZCB0aGUgbW9kYWwgZGF0YSB0byB0aGUgZm9ybSB0aHJvdWdoIGFuIEFKQVggY2FsbC5cblx0ICovXG5cdGZ1bmN0aW9uIF9vblNlbmRDbGljaygpIHtcblx0XHQvLyBTZW5kIHR5cGUuXG5cdFx0Y29uc3QgUkVRVUVTVF9TRU5EX1RZUEUgPSAnc2VuZF9vcmRlcic7XG5cdFx0XG5cdFx0Ly8gUmVxdWVzdCBiYXNlIFVSTC5cblx0XHRjb25zdCBSRVFVRVNUX1VSTCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9nbV9zZW5kX29yZGVyLnBocCc7XG5cdFx0XG5cdFx0Ly8gQ29sbGVjdGlvbiBvZiByZXF1ZXN0cyBpbiBwcm9taXNlIGZvcm1hdC5cblx0XHRjb25zdCBwcm9taXNlcyA9IFtdO1xuXHRcdFxuXHRcdC8vIEVtYWlsIGxpc3QgaXRlbSBlbGVtZW50cy5cblx0XHRjb25zdCAkZW1haWxMaXN0SXRlbXMgPSAkdGhpcy5maW5kKGVtYWlsTGlzdEl0ZW1TZWxlY3Rvcik7XG5cdFx0XG5cdFx0Ly8gQWJvcnQgYW5kIGhpZGUgbW9kYWwgb24gZW1wdHkgZW1haWwgbGlzdCBlbnRyaWVzLlxuXHRcdGlmICghJGVtYWlsTGlzdEl0ZW1zLmxlbmd0aCkge1xuXHRcdFx0JHRoaXMubW9kYWwoJ2hpZGUnKTtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0Ly8gU2hvdyBsb2FkaW5nIHNwaW5uZXIuXG5cdFx0X3RvZ2dsZVNwaW5uZXIodHJ1ZSk7XG5cdFx0XG5cdFx0Ly8gRmlsbCBvcmRlcnMgYXJyYXkgd2l0aCBkYXRhLlxuXHRcdCRlbWFpbExpc3RJdGVtcy5lYWNoKChpbmRleCwgZWxlbWVudCkgPT4ge1xuXHRcdFx0Ly8gT3JkZXIgZGF0YS5cblx0XHRcdGNvbnN0IG9yZGVyRGF0YSA9ICQoZWxlbWVudCkuZGF0YSgnb3JkZXInKTtcblx0XHRcdFxuXHRcdFx0Ly8gRm9ybWF0IHRoZSBwdXJjaGFzZSBkYXRlLlxuXHRcdFx0Y29uc3QgZGF0ZUZvcm1hdCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpID09PSAnZGUnID8gJ0RELk1NLllZJyA6ICdNTS5ERC5ZWSc7XG5cdFx0XHRvcmRlckRhdGEucHVyY2hhc2VEYXRlID0gbW9tZW50KG9yZGVyRGF0YS5wdXJjaGFzZURhdGUuZGF0ZSkuZm9ybWF0KGRhdGVGb3JtYXQpO1xuXHRcdFx0XG5cdFx0XHQvLyBFbWFpbCBhZGRyZXNzIGVudGVyZWQgaW4gaW5wdXQgZmllbGQuXG5cdFx0XHRjb25zdCBlbnRlcmVkRW1haWwgPSAkKGVsZW1lbnQpLmZpbmQoZW1haWxMaXN0SXRlbUVtYWlsU2VsZWN0b3IpLnZhbCgpO1xuXHRcdFx0XG5cdFx0XHQvLyBSZXF1ZXN0IEdFVCBwYXJhbWV0ZXJzIHRvIHNlbmQuXG5cdFx0XHRjb25zdCBnZXRQYXJhbWV0ZXJzID0ge1xuXHRcdFx0XHRvSUQ6IG9yZGVyRGF0YS5pZCxcblx0XHRcdFx0dHlwZTogUkVRVUVTVF9TRU5EX1RZUEVcblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8vIENvbXBvc2VkIHJlcXVlc3QgVVJMLlxuXHRcdFx0Y29uc3QgdXJsID0gUkVRVUVTVF9VUkwgKyAnPycgKyAkLnBhcmFtKGdldFBhcmFtZXRlcnMpO1xuXHRcdFx0XG5cdFx0XHQvLyBEYXRhIHRvIHNlbmQuXG5cdFx0XHRjb25zdCBkYXRhID0ge1xuXHRcdFx0XHRnbV9tYWlsOiBlbnRlcmVkRW1haWwsXG5cdFx0XHRcdGdtX3N1YmplY3Q6IF9nZXRQYXJzZWRTdWJqZWN0KG9yZGVyRGF0YSlcblx0XHRcdH07XG5cdFx0XHRcblx0XHRcdC8vIFByb21pc2Ugd3JhcHBlciBmb3IgQUpBWCByZXNwb25zZS5cblx0XHRcdGNvbnN0IHByb21pc2UgPSBuZXcgUHJvbWlzZSgocmVzb2x2ZSwgcmVqZWN0KSA9PiB7XG5cdFx0XHRcdC8vIENyZWF0ZSBBSkFYIHJlcXVlc3QuXG5cdFx0XHRcdGNvbnN0IHJlcXVlc3QgPSAkLmFqYXgoe21ldGhvZDogJ1BPU1QnLCB1cmwsIGRhdGF9KTtcblx0XHRcdFx0XG5cdFx0XHRcdHJlcXVlc3Quc3VjY2VzcygoKSA9PiB7XG5cdFx0XHRcdFx0Y29uc3Qgb3JkZXJJZCA9IGdldFBhcmFtZXRlcnMub0lEO1xuXHRcdFx0XHRcdGNvbnN0ICR0YWJsZVJvdyA9ICQoYHRib2R5IHRyIyR7b3JkZXJJZH1gKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBSZW1vdmUgdGhlIGUtbWFpbCBzeW1ib2xcblx0XHRcdFx0XHQkdGFibGVSb3cuZmluZCgndGQuYWN0aW9ucyBpLnRvb2x0aXAtY29uZmlybWF0aW9uLW5vdC1zZW50JykucmVtb3ZlKCk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHRcblx0XHRcdFx0Ly8gUmVzb2x2ZSBwcm9taXNlIG9uIHN1Y2Nlc3MuXG5cdFx0XHRcdHJlcXVlc3QuZG9uZShyZXNwb25zZSA9PiByZXNvbHZlKHJlc3BvbnNlKSk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBSZWplY3QgcHJvbWlzZSBvbiBmYWlsLlxuXHRcdFx0XHRyZXF1ZXN0LmZhaWwoKCkgPT4gcmVqZWN0KCkpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdC8vIEFkZCBwcm9taXNlIHRvIGFycmF5LlxuXHRcdFx0cHJvbWlzZXMucHVzaChwcm9taXNlKTtcblx0XHR9KTtcblx0XHRcblx0XHQvLyBXYWl0IGZvciBhbGwgcHJvbWlzZSB0byByZXNwb25kIGFuZCBoYW5kbGUgc3VjY2Vzcy9lcnJvci5cblx0XHRQcm9taXNlLmFsbChwcm9taXNlcylcblx0XHRcdC50aGVuKF9oYW5kbGVEZWxpdmVyeVN1Y2Nlc3MpXG5cdFx0XHQuY2F0Y2goX2hhbmRsZURlbGl2ZXJ5RmFpbCk7XG5cdH1cblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBJTklUSUFMSVpBVElPTlxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdCR0aGlzLm9uKCdjbGljaycsICcuYnRuLnNlbmQnLCBfb25TZW5kQ2xpY2spO1xuXHRcdGRvbmUoKTtcblx0fTtcblx0XG5cdHJldHVybiBtb2R1bGU7XG59KTsiXX0=
