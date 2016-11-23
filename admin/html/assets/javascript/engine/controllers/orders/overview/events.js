'use strict';

/* --------------------------------------------------------------
 events.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Main Table Events
 *
 * Handles the events of the main orders table.
 */
gx.controllers.module('events', ['loading_spinner'], function (data) {

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
	var module = {};

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
  * On Bulk Selection Change
  *
  * @param {jQuery.Event} event jQuery event object.
  * @param {Boolean} propagate Whether to affect the body elements. We do not need this on "draw.dt" event.
  */
	function _onBulkSelectionChange(event) {
		var propagate = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;

		if (propagate === false) {
			return; // Do not propagate on draw event because the body checkboxes are unchecked by default.
		}

		$this.find('tbody input:checkbox').single_checkbox('checked', $(this).prop('checked'));
	}

	/**
  * On Table Row Click
  *
  * When a row is clicked then the row-checkbox must be toggled.
  *
  * @param {jQuery.Event} event
  */
	function _onTableRowClick(event) {
		if (!$(event.target).is('td')) {
			return;
		}

		$(this).find('input:checkbox').prop('checked', !$(this).find('input:checkbox').prop('checked')).trigger('change');
	}

	/**
  * On Table Row Checkbox Change
  *
  * Adjust the bulk actions state whenever there are changes in the table checkboxes.
  */
	function _onTableRowCheckboxChange() {
		if ($this.find('input:checkbox:checked').length > 0) {
			$this.parents('.orders').find('.bulk-action > button').removeClass('disabled');
		} else {
			$this.parents('.orders').find('.bulk-action > button').addClass('disabled');
		}
	}

	/**
  * On Cancel Order Click
  *
  * @param {jQuery.Event} event
  */
	function _onCancelOrderClick(event) {
		event.preventDefault();

		var selectedOrders = _getSelectedOrders($(this));

		// Show the order delete modal.
		$('.cancel.modal .selected-orders').text(selectedOrders.join(', '));
		$('.cancel.modal').modal('show');
	}

	/**
  * On Delete Order Click
  *
  * Display the delete-modal.
  *
  * @param {jQuery.Event} event
  */
	function _onDeleteOrderClick(event) {
		event.preventDefault();

		var selectedOrders = _getSelectedOrders($(this));

		// Show the order delete modal.
		$('.delete.modal .selected-orders').text(selectedOrders.join(', '));
		$('.delete.modal').modal('show');
	}

	/**
  * On Send Order Click.
  *
  * Sends the email order confirmations.
  *
  * @param {jQuery.Event} event jQuery event object.
  */
	function _onBulkEmailOrderClick(event) {
		var $modal = $('.bulk-email-order.modal');
		var $mailList = $modal.find('.email-list');

		var generateMailRowMarkup = function generateMailRowMarkup(data) {
			var $row = $('<div/>', { class: 'form-group email-list-item' });
			var $idColumn = $('<div/>', { class: 'col-sm-2' });
			var $emailColumn = $('<div/>', { class: 'col-sm-10' });

			var $idLabel = $('<label/>', {
				class: 'control-label id-label force-text-color-black force-text-normal-weight',
				text: data.id
			});

			var $emailInput = $('<input/>', {
				class: 'form-control email-input',
				type: 'text',
				value: data.customerEmail
			});

			$idLabel.appendTo($idColumn);
			$emailInput.appendTo($emailColumn);

			$row.append([$idColumn, $emailColumn]);
			$row.data('order', data);

			return $row;
		};

		var selectedOrders = [];

		event.preventDefault();

		$this.find('tbody input:checkbox:checked').each(function () {
			var rowData = $(this).parents('tr').data();
			selectedOrders.push(rowData);
		});

		if (selectedOrders.length) {
			$mailList.empty();
			selectedOrders.forEach(function (order) {
				return $mailList.append(generateMailRowMarkup(order));
			});
			$modal.modal('show');
		}
	}

	/**
  * On Send Invoice Click.
  *
  * Sends the email invoice.
  *
  * @param {jQuery.Event} event Fired event.
  */
	function _onBulkEmailInvoiceClick(event) {
		var $modal = $('.bulk-email-invoice.modal');
		var $mailList = $modal.find('.email-list');

		var generateMailRowMarkup = function generateMailRowMarkup(data) {
			var $row = $('<div/>', { class: 'form-group email-list-item' });
			var $idColumn = $('<div/>', { class: 'col-sm-2' });
			var $emailColumn = $('<div/>', { class: 'col-sm-10' });

			var $idLabel = $('<label/>', {
				class: 'control-label id-label force-text-color-black force-text-normal-weight',
				text: data.id
			});

			var $emailInput = $('<input/>', {
				class: 'form-control email-input',
				type: 'text',
				value: data.customerEmail
			});

			$idLabel.appendTo($idColumn);
			$emailInput.appendTo($emailColumn);

			$row.append([$idColumn, $emailColumn]);
			$row.data('invoice', data);

			return $row;
		};

		var selectedInvoice = [];

		event.preventDefault();

		$this.find('tbody input:checkbox:checked').each(function () {
			var rowData = $(this).parents('tr').data();
			selectedInvoice.push(rowData);
		});

		if (selectedInvoice.length) {
			$mailList.empty();
			selectedInvoice.forEach(function (order) {
				return $mailList.append(generateMailRowMarkup(order));
			});
			$modal.modal('show');
		}
	}

	/**
  * Collects the IDs of the selected orders and returns them as an array
  *
  * @param {jQuery} $target The triggering target
  *
  * @return {Number[]} array of order IDs
  */
	function _getSelectedOrders($target) {
		var selectedOrders = [];

		if ($target.parents('.bulk-action').length > 0) {
			// Fetch the selected order IDs.
			$this.find('tbody input:checkbox:checked').each(function () {
				selectedOrders.push($(this).parents('tr').data('id'));
			});
		} else {
			var rowId = $target.parents('tr').data('id');

			if (!rowId) {
				return; // No order ID was found.
			}

			selectedOrders.push(rowId);
		}

		return selectedOrders;
	}

	/**
  * On Email Invoice Click
  *
  * Display the email-invoice modal.
  */
	function _onEmailInvoiceClick() {
		var $modal = $('.email-invoice.modal');
		var rowData = $(this).parents('tr').data();
		var url = jse.core.config.get('appUrl') + '/admin/admin.php';
		var data = {
			id: rowData.id,
			date: rowData.purchaseDate.date,
			do: 'OrdersModalsAjax/GetEmailInvoiceSubject',
			pageToken: jse.core.config.get('pageToken')
		};

		$modal.find('.customer-info').text('"' + rowData.customerName + '" "' + rowData.customerEmail + '"');
		$modal.find('.email-address').val(rowData.customerEmail);

		$modal.data('orderId', rowData.id).modal('show');

		$.ajax({ url: url, data: data, dataType: 'json' }).done(function (response) {
			$modal.find('.subject').val(response.subject);
			if (response.invoiceIdExists) {
				$modal.find('.invoice-num-info').addClass('hidden');
			} else {
				$modal.find('.invoice-num-info').removeClass('hidden');
			}
		});
	}

	/**
  * On Email Order Click
  *
  * Display the email-order modal.
  *
  * @param {jQuery.Event} event
  */
	function _onEmailOrderClick(event) {
		var $modal = $('.email-order.modal');
		var rowData = $(this).parents('tr').data();
		var dateFormat = jse.core.config.get('languageCode') === 'de' ? 'DD.MM.YY' : 'MM.DD.YY';

		$modal.find('.customer-info').text('"' + rowData.customerName + '" "' + rowData.customerEmail + '"');
		$modal.find('.subject').val(jse.core.lang.translate('ORDER_SUBJECT', 'gm_order_menu') + rowData.id + jse.core.lang.translate('ORDER_SUBJECT_FROM', 'gm_order_menu') + moment(rowData.purchaseDate).format(dateFormat));
		$modal.find('.email-address').val(rowData.customerEmail);

		$modal.data('orderId', rowData.id).modal('show');
	}

	/**
  * On Change Order Status Click
  *
  * Display the change order status modal.
  *
  * @param {jQuery.Event} event
  */
	function _onChangeOrderStatusClick(event) {
		if ($(event.target).hasClass('order-status')) {
			event.stopPropagation();
		}

		var $modal = $('.status.modal');
		var rowData = $(this).parents('tr').data();
		var selectedOrders = _getSelectedOrders($(this));

		$modal.find('#status-dropdown').val(rowData ? rowData.statusId : '');

		$modal.find('#comment').val('');
		$modal.find('#notify-customer, #send-parcel-tracking-code, #send-comment').attr('checked', false).parents('.single-checkbox').removeClass('checked');

		// Show the order delete modal.
		$modal.find('.selected-orders').text(selectedOrders.join(', '));
		$modal.modal('show');
	}

	/**
  * On Add Tracking Number Click
  *
  * @param {jQuery.Event} event
  */
	function _onAddTrackingNumberClick(event) {
		var $modal = $('.add-tracking-number.modal');
		var rowData = $(event.target).parents('tr').data();

		$modal.data('orderId', rowData.id);
		$modal.modal('show');
	}

	/**
  * Opens the gm_pdf_order.php in a new tab with invoices as type $_GET argument.
  *
  * The order ids are passed as a serialized array to the oID $_GET argument.
  */
	function _onBulkDownloadInvoiceClick() {
		var orderIds = [];
		var maxAmountInvoicesBulkPdf = $('#max-amount-invoices-bulk-pdf').text();
		var $modal = void 0;
		var $invoiceMessageContainer = void 0;

		$this.find('tbody input:checkbox:checked').each(function () {
			orderIds.push($(this).parents('tr').data('id'));
		});

		if (orderIds.length > maxAmountInvoicesBulkPdf) {
			$modal = $('.bulk-error.modal');
			$modal.modal('show');
			$invoiceMessageContainer = $modal.find('.invoices-message');

			$invoiceMessageContainer.removeClass('hidden');

			$modal.on('hide.bs.modal', function () {
				$invoiceMessageContainer.addClass('hidden');
			});
			return;
		}

		_createBulkPdf(orderIds, 'invoice');
	}

	/**
  * Opens the gm_pdf_order.php in a new tab with packing slip as type $_GET argument.
  *
  * The order ids are passed as a serialized array to the oID $_GET argument.
  */
	function _onBulkDownloadPackingSlipClick() {
		var orderIds = [];
		var maxAmountPackingSlipsBulkPdf = $('#max-amount-packing-slips-bulk-pdf').text();
		var $modal = void 0;
		var $packingSlipsMessageContainer = void 0;

		$this.find('tbody input:checkbox:checked').each(function () {
			orderIds.push($(this).parents('tr').data('id'));
		});

		if (orderIds.length > maxAmountPackingSlipsBulkPdf) {
			$modal = $('.bulk-error.modal');
			$modal.modal('show');
			$packingSlipsMessageContainer = $modal.find('.packing-slips-message');

			$packingSlipsMessageContainer.removeClass('hidden');

			$modal.on('hide.bs.modal', function () {
				$packingSlipsMessageContainer.addClass('hidden');
			});

			return;
		}

		_createBulkPdf(orderIds, 'packingslip');
	}

	/**
  * Creates a bulk pdf with invoices or packing slips information.
  *
  * @param {Number[]} orderIds
  * @param {String} type
  */
	function _createBulkPdf(orderIds, type) {
		var deferreds = [];
		var zIndex = $('.table-fixed-header thead.fixed').css('z-index'); // Could be "undefined" as well.

		$spinner = jse.libs.loading_spinner.show($this, zIndex);

		orderIds.forEach(function (id) {
			var data = {
				type: type,
				ajax: '1',
				oID: id
			};

			deferreds.push($.getJSON(jse.core.config.get('appUrl') + '/admin/gm_pdf_order.php', data));
		});

		$.when.apply(null, deferreds).done(function () {
			_openBulkPdfUrl(orderIds, type);

			// Keep checkboxes checked after a datatable reload.
			$this.DataTable().ajax.reload(function () {
				$this.off('single_checkbox:ready', _onSingleCheckboxReady).on('single_checkbox:ready', { orderIds: orderIds }, _onSingleCheckboxReady);
			});
			$this.orders_overview_filter('reload');
		});
	}

	function _onSingleCheckboxReady(event) {
		event.data.orderIds.forEach(function (id) {
			$this.find('tr#' + id + ' input:checkbox').single_checkbox('checked', true).trigger('change');
		});

		// Bulk action button should't be disabled after a datatable reload.
		if ($('tr input:checkbox:checked').length) {
			$('.bulk-action').find('button').removeClass('disabled');
		}
	}

	/**
  * Opens the url which provide the bulk PDF's as download.
  *
  * @param {Number[]} callbacks
  * @param {String} type
  */
	function _openBulkPdfUrl(orderIds, type) {
		var parameters = {
			do: 'OrdersModalsAjax/BulkPdf' + (type === 'invoice' ? 'Invoices' : 'PackingSlips'),
			pageToken: jse.core.config.get('pageToken'),
			o: orderIds
		};

		var url = jse.core.config.get('appUrl') + '/admin/admin.php?' + $.param(parameters);

		var downloadPdfWindow = window.open(url, '_blank');

		if (!downloadPdfWindow) {
			var $modal = $('.bulk-download-error.modal');
			var $downloadLink = $modal.find('.download-bulk-btn');
			$downloadLink.attr('href', url);
			$modal.modal('show');
		}

		jse.libs.loading_spinner.hide($spinner);
	}

	/**
  * On Invoice Link Click
  *
  * The script that generates the PDFs is changing the status of an order to "invoice-created". Thus the
  * table data need to be redrawn and the filter options to be updated.
  */
	function _onInvoiceClick() {
		var link = $(this).attr('href');

		window.open(link, '_blank');
		$this.DataTable().ajax.reload();
	}

	/**
  * On Edit Row Action Click
  */
	function _onEditOrderClick() {
		var orderId = $(this).parents('tr').data('id');

		var parameters = {
			oID: orderId,
			action: 'edit',
			overview: $.deparam(window.location.search.slice(1))
		};

		window.location.href = 'orders.php?' + $.param(parameters);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Bind table row actions.
		$this.on('click', 'tbody tr', _onTableRowClick).on('change', '.bulk-selection', _onBulkSelectionChange).on('change', 'input:checkbox', _onTableRowCheckboxChange).on('click', '.invoice', _onInvoiceClick).on('click', '.email-invoice', _onEmailInvoiceClick).on('click', '.email-order', _onEmailOrderClick).on('click', '.order-status.label', _onChangeOrderStatusClick).on('click', '.add-tracking-number', _onAddTrackingNumberClick).on('click', '.actions .edit', _onEditOrderClick);

		// Bind table row and bulk actions.
		$this.parents('.orders').on('click', '.btn-group .change-status', _onChangeOrderStatusClick).on('click', '.btn-group .cancel', _onCancelOrderClick).on('click', '.btn-group .delete, .actions .delete', _onDeleteOrderClick).on('click', '.btn-group .bulk-email-order', _onBulkEmailOrderClick).on('click', '.btn-group .bulk-email-invoice', _onBulkEmailInvoiceClick).on('click', '.btn-group .bulk-download-invoice', _onBulkDownloadInvoiceClick).on('click', '.btn-group .bulk-download-packing-slip', _onBulkDownloadPackingSlipClick);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vdmVydmlldy9ldmVudHMuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkc3Bpbm5lciIsIl9vbkJ1bGtTZWxlY3Rpb25DaGFuZ2UiLCJldmVudCIsInByb3BhZ2F0ZSIsImZpbmQiLCJzaW5nbGVfY2hlY2tib3giLCJwcm9wIiwiX29uVGFibGVSb3dDbGljayIsInRhcmdldCIsImlzIiwidHJpZ2dlciIsIl9vblRhYmxlUm93Q2hlY2tib3hDaGFuZ2UiLCJsZW5ndGgiLCJwYXJlbnRzIiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsIl9vbkNhbmNlbE9yZGVyQ2xpY2siLCJwcmV2ZW50RGVmYXVsdCIsInNlbGVjdGVkT3JkZXJzIiwiX2dldFNlbGVjdGVkT3JkZXJzIiwidGV4dCIsImpvaW4iLCJtb2RhbCIsIl9vbkRlbGV0ZU9yZGVyQ2xpY2siLCJfb25CdWxrRW1haWxPcmRlckNsaWNrIiwiJG1vZGFsIiwiJG1haWxMaXN0IiwiZ2VuZXJhdGVNYWlsUm93TWFya3VwIiwiJHJvdyIsImNsYXNzIiwiJGlkQ29sdW1uIiwiJGVtYWlsQ29sdW1uIiwiJGlkTGFiZWwiLCJpZCIsIiRlbWFpbElucHV0IiwidHlwZSIsInZhbHVlIiwiY3VzdG9tZXJFbWFpbCIsImFwcGVuZFRvIiwiYXBwZW5kIiwiZWFjaCIsInJvd0RhdGEiLCJwdXNoIiwiZW1wdHkiLCJmb3JFYWNoIiwib3JkZXIiLCJfb25CdWxrRW1haWxJbnZvaWNlQ2xpY2siLCJzZWxlY3RlZEludm9pY2UiLCIkdGFyZ2V0Iiwicm93SWQiLCJfb25FbWFpbEludm9pY2VDbGljayIsInVybCIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJkYXRlIiwicHVyY2hhc2VEYXRlIiwiZG8iLCJwYWdlVG9rZW4iLCJjdXN0b21lck5hbWUiLCJ2YWwiLCJhamF4IiwiZGF0YVR5cGUiLCJkb25lIiwicmVzcG9uc2UiLCJzdWJqZWN0IiwiaW52b2ljZUlkRXhpc3RzIiwiX29uRW1haWxPcmRlckNsaWNrIiwiZGF0ZUZvcm1hdCIsImxhbmciLCJ0cmFuc2xhdGUiLCJtb21lbnQiLCJmb3JtYXQiLCJfb25DaGFuZ2VPcmRlclN0YXR1c0NsaWNrIiwiaGFzQ2xhc3MiLCJzdG9wUHJvcGFnYXRpb24iLCJzdGF0dXNJZCIsImF0dHIiLCJfb25BZGRUcmFja2luZ051bWJlckNsaWNrIiwiX29uQnVsa0Rvd25sb2FkSW52b2ljZUNsaWNrIiwib3JkZXJJZHMiLCJtYXhBbW91bnRJbnZvaWNlc0J1bGtQZGYiLCIkaW52b2ljZU1lc3NhZ2VDb250YWluZXIiLCJvbiIsIl9jcmVhdGVCdWxrUGRmIiwiX29uQnVsa0Rvd25sb2FkUGFja2luZ1NsaXBDbGljayIsIm1heEFtb3VudFBhY2tpbmdTbGlwc0J1bGtQZGYiLCIkcGFja2luZ1NsaXBzTWVzc2FnZUNvbnRhaW5lciIsImRlZmVycmVkcyIsInpJbmRleCIsImNzcyIsImxpYnMiLCJsb2FkaW5nX3NwaW5uZXIiLCJzaG93Iiwib0lEIiwiZ2V0SlNPTiIsIndoZW4iLCJhcHBseSIsIl9vcGVuQnVsa1BkZlVybCIsIkRhdGFUYWJsZSIsInJlbG9hZCIsIm9mZiIsIl9vblNpbmdsZUNoZWNrYm94UmVhZHkiLCJvcmRlcnNfb3ZlcnZpZXdfZmlsdGVyIiwicGFyYW1ldGVycyIsIm8iLCJwYXJhbSIsImRvd25sb2FkUGRmV2luZG93Iiwid2luZG93Iiwib3BlbiIsIiRkb3dubG9hZExpbmsiLCJoaWRlIiwiX29uSW52b2ljZUNsaWNrIiwibGluayIsIl9vbkVkaXRPcmRlckNsaWNrIiwib3JkZXJJZCIsImFjdGlvbiIsIm92ZXJ2aWV3IiwiZGVwYXJhbSIsImxvY2F0aW9uIiwic2VhcmNoIiwic2xpY2UiLCJocmVmIiwiaW5pdCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7OztBQUtBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsUUFBdEIsRUFBZ0MsQ0FBQyxpQkFBRCxD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T2MscUJBUGQsRUFPcUNWLHlCQVByQyxFQVFFVSxFQVJGLENBUUssT0FSTCxFQVFjLHNCQVJkLEVBUXNDTCx5QkFSdEMsRUFTRUssRUFURixDQVNLLE9BVEwsRUFTYyxnQkFUZCxFQVNnQytCLGlCQVRoQzs7QUFXQTtBQUNBckgsUUFBTWUsT0FBTixDQUFjLFNBQWQsRUFDRXVFLEVBREYsQ0FDSyxPQURMLEVBQ2MsMkJBRGQsRUFDMkNWLHlCQUQzQyxFQUVFVSxFQUZGLENBRUssT0FGTCxFQUVjLG9CQUZkLEVBRW9DcEUsbUJBRnBDLEVBR0VvRSxFQUhGLENBR0ssT0FITCxFQUdjLHNDQUhkLEVBR3NEN0QsbUJBSHRELEVBSUU2RCxFQUpGLENBSUssT0FKTCxFQUljLDhCQUpkLEVBSThDNUQsc0JBSjlDLEVBS0U0RCxFQUxGLENBS0ssT0FMTCxFQUtjLGdDQUxkLEVBS2dEdEMsd0JBTGhELEVBTUVzQyxFQU5GLENBTUssT0FOTCxFQU1jLG1DQU5kLEVBTW1ESiwyQkFObkQsRUFPRUksRUFQRixDQU9LLE9BUEwsRUFPYyx3Q0FQZCxFQU93REUsK0JBUHhEOztBQVNBdEI7QUFDQSxFQXhCRDs7QUEwQkEsUUFBT3BFLE1BQVA7QUFDQSxDQXZoQkQiLCJmaWxlIjoib3JkZXJzL292ZXJ2aWV3L2V2ZW50cy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBldmVudHMuanMgMjAxNi0wNi0yMFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBNYWluIFRhYmxlIEV2ZW50c1xyXG4gKlxyXG4gKiBIYW5kbGVzIHRoZSBldmVudHMgb2YgdGhlIG1haW4gb3JkZXJzIHRhYmxlLlxyXG4gKi9cclxuZ3guY29udHJvbGxlcnMubW9kdWxlKCdldmVudHMnLCBbJ2xvYWRpbmdfc3Bpbm5lciddLCBmdW5jdGlvbihkYXRhKSB7XHJcblxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHJcblx0LyoqXHJcblx0ICogTG9hZGluZyBzcGlubmVyIGluc3RhbmNlLlxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeXxudWxsfVxyXG5cdCAqL1xyXG5cdGxldCAkc3Bpbm5lciA9IG51bGw7XHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHQvKipcclxuXHQgKiBPbiBCdWxrIFNlbGVjdGlvbiBDaGFuZ2VcclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBqUXVlcnkgZXZlbnQgb2JqZWN0LlxyXG5cdCAqIEBwYXJhbSB7Qm9vbGVhbn0gcHJvcGFnYXRlIFdoZXRoZXIgdG8gYWZmZWN0IHRoZSBib2R5IGVsZW1lbnRzLiBXZSBkbyBub3QgbmVlZCB0aGlzIG9uIFwiZHJhdy5kdFwiIGV2ZW50LlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkJ1bGtTZWxlY3Rpb25DaGFuZ2UoZXZlbnQsIHByb3BhZ2F0ZSA9IHRydWUpIHtcclxuXHRcdGlmIChwcm9wYWdhdGUgPT09IGZhbHNlKSB7XHJcblx0XHRcdHJldHVybjsgLy8gRG8gbm90IHByb3BhZ2F0ZSBvbiBkcmF3IGV2ZW50IGJlY2F1c2UgdGhlIGJvZHkgY2hlY2tib3hlcyBhcmUgdW5jaGVja2VkIGJ5IGRlZmF1bHQuXHJcblx0XHR9XHJcblxyXG5cdFx0JHRoaXMuZmluZCgndGJvZHkgaW5wdXQ6Y2hlY2tib3gnKS5zaW5nbGVfY2hlY2tib3goJ2NoZWNrZWQnLCAkKHRoaXMpLnByb3AoJ2NoZWNrZWQnKSk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBPbiBUYWJsZSBSb3cgQ2xpY2tcclxuXHQgKlxyXG5cdCAqIFdoZW4gYSByb3cgaXMgY2xpY2tlZCB0aGVuIHRoZSByb3ctY2hlY2tib3ggbXVzdCBiZSB0b2dnbGVkLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uVGFibGVSb3dDbGljayhldmVudCkge1xyXG5cdFx0aWYgKCEkKGV2ZW50LnRhcmdldCkuaXMoJ3RkJykpIHtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdCQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKVxyXG5cdFx0XHQucHJvcCgnY2hlY2tlZCcsICEkKHRoaXMpLmZpbmQoJ2lucHV0OmNoZWNrYm94JykucHJvcCgnY2hlY2tlZCcpKVxyXG5cdFx0XHQudHJpZ2dlcignY2hhbmdlJyk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBPbiBUYWJsZSBSb3cgQ2hlY2tib3ggQ2hhbmdlXHJcblx0ICpcclxuXHQgKiBBZGp1c3QgdGhlIGJ1bGsgYWN0aW9ucyBzdGF0ZSB3aGVuZXZlciB0aGVyZSBhcmUgY2hhbmdlcyBpbiB0aGUgdGFibGUgY2hlY2tib3hlcy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25UYWJsZVJvd0NoZWNrYm94Q2hhbmdlKCkge1xyXG5cdFx0aWYgKCR0aGlzLmZpbmQoJ2lucHV0OmNoZWNrYm94OmNoZWNrZWQnKS5sZW5ndGggPiAwKSB7XHJcblx0XHRcdCR0aGlzLnBhcmVudHMoJy5vcmRlcnMnKS5maW5kKCcuYnVsay1hY3Rpb24gPiBidXR0b24nKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdCR0aGlzLnBhcmVudHMoJy5vcmRlcnMnKS5maW5kKCcuYnVsay1hY3Rpb24gPiBidXR0b24nKS5hZGRDbGFzcygnZGlzYWJsZWQnKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIE9uIENhbmNlbCBPcmRlciBDbGlja1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uQ2FuY2VsT3JkZXJDbGljayhldmVudCkge1xyXG5cdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuXHJcblx0XHRjb25zdCBzZWxlY3RlZE9yZGVycyA9IF9nZXRTZWxlY3RlZE9yZGVycygkKHRoaXMpKTtcclxuXHJcblx0XHQvLyBTaG93IHRoZSBvcmRlciBkZWxldGUgbW9kYWwuXHJcblx0XHQkKCcuY2FuY2VsLm1vZGFsIC5zZWxlY3RlZC1vcmRlcnMnKS50ZXh0KHNlbGVjdGVkT3JkZXJzLmpvaW4oJywgJykpO1xyXG5cdFx0JCgnLmNhbmNlbC5tb2RhbCcpLm1vZGFsKCdzaG93Jyk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBPbiBEZWxldGUgT3JkZXIgQ2xpY2tcclxuXHQgKlxyXG5cdCAqIERpc3BsYXkgdGhlIGRlbGV0ZS1tb2RhbC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkRlbGV0ZU9yZGVyQ2xpY2soZXZlbnQpIHtcclxuXHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG5cdFx0Y29uc3Qgc2VsZWN0ZWRPcmRlcnMgPSBfZ2V0U2VsZWN0ZWRPcmRlcnMoJCh0aGlzKSk7XHJcblxyXG5cdFx0Ly8gU2hvdyB0aGUgb3JkZXIgZGVsZXRlIG1vZGFsLlxyXG5cdFx0JCgnLmRlbGV0ZS5tb2RhbCAuc2VsZWN0ZWQtb3JkZXJzJykudGV4dChzZWxlY3RlZE9yZGVycy5qb2luKCcsICcpKTtcclxuXHRcdCQoJy5kZWxldGUubW9kYWwnKS5tb2RhbCgnc2hvdycpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogT24gU2VuZCBPcmRlciBDbGljay5cclxuXHQgKlxyXG5cdCAqIFNlbmRzIHRoZSBlbWFpbCBvcmRlciBjb25maXJtYXRpb25zLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uQnVsa0VtYWlsT3JkZXJDbGljayhldmVudCkge1xyXG5cdFx0Y29uc3QgJG1vZGFsID0gJCgnLmJ1bGstZW1haWwtb3JkZXIubW9kYWwnKTtcclxuXHRcdGNvbnN0ICRtYWlsTGlzdCA9ICRtb2RhbC5maW5kKCcuZW1haWwtbGlzdCcpO1xyXG5cclxuXHRcdGNvbnN0IGdlbmVyYXRlTWFpbFJvd01hcmt1cCA9IGRhdGEgPT4ge1xyXG5cdFx0XHRjb25zdCAkcm93ID0gJCgnPGRpdi8+Jywge2NsYXNzOiAnZm9ybS1ncm91cCBlbWFpbC1saXN0LWl0ZW0nfSk7XHJcblx0XHRcdGNvbnN0ICRpZENvbHVtbiA9ICQoJzxkaXYvPicsIHtjbGFzczogJ2NvbC1zbS0yJ30pO1xyXG5cdFx0XHRjb25zdCAkZW1haWxDb2x1bW4gPSAkKCc8ZGl2Lz4nLCB7Y2xhc3M6ICdjb2wtc20tMTAnfSk7XHJcblxyXG5cdFx0XHRjb25zdCAkaWRMYWJlbCA9ICQoJzxsYWJlbC8+Jywge1xyXG5cdFx0XHRcdGNsYXNzOiAnY29udHJvbC1sYWJlbCBpZC1sYWJlbCBmb3JjZS10ZXh0LWNvbG9yLWJsYWNrIGZvcmNlLXRleHQtbm9ybWFsLXdlaWdodCcsXHJcblx0XHRcdFx0dGV4dDogZGF0YS5pZFxyXG5cdFx0XHR9KTtcclxuXHJcblx0XHRcdGNvbnN0ICRlbWFpbElucHV0ID0gJCgnPGlucHV0Lz4nLCB7XHJcblx0XHRcdFx0Y2xhc3M6ICdmb3JtLWNvbnRyb2wgZW1haWwtaW5wdXQnLFxyXG5cdFx0XHRcdHR5cGU6ICd0ZXh0JyxcclxuXHRcdFx0XHR2YWx1ZTogZGF0YS5jdXN0b21lckVtYWlsXHJcblx0XHRcdH0pO1xyXG5cclxuXHRcdFx0JGlkTGFiZWwuYXBwZW5kVG8oJGlkQ29sdW1uKTtcclxuXHRcdFx0JGVtYWlsSW5wdXQuYXBwZW5kVG8oJGVtYWlsQ29sdW1uKTtcclxuXHJcblx0XHRcdCRyb3cuYXBwZW5kKFskaWRDb2x1bW4sICRlbWFpbENvbHVtbl0pO1xyXG5cdFx0XHQkcm93LmRhdGEoJ29yZGVyJywgZGF0YSk7XHJcblxyXG5cdFx0XHRyZXR1cm4gJHJvdztcclxuXHRcdH07XHJcblxyXG5cdFx0Y29uc3Qgc2VsZWN0ZWRPcmRlcnMgPSBbXTtcclxuXHJcblx0XHRldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cclxuXHRcdCR0aGlzLmZpbmQoJ3Rib2R5IGlucHV0OmNoZWNrYm94OmNoZWNrZWQnKS5lYWNoKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRjb25zdCByb3dEYXRhID0gJCh0aGlzKS5wYXJlbnRzKCd0cicpLmRhdGEoKTtcclxuXHRcdFx0c2VsZWN0ZWRPcmRlcnMucHVzaChyb3dEYXRhKTtcclxuXHRcdH0pO1xyXG5cclxuXHRcdGlmIChzZWxlY3RlZE9yZGVycy5sZW5ndGgpIHtcclxuXHRcdFx0JG1haWxMaXN0LmVtcHR5KCk7XHJcblx0XHRcdHNlbGVjdGVkT3JkZXJzLmZvckVhY2gob3JkZXIgPT4gJG1haWxMaXN0LmFwcGVuZChnZW5lcmF0ZU1haWxSb3dNYXJrdXAob3JkZXIpKSk7XHJcblx0XHRcdCRtb2RhbC5tb2RhbCgnc2hvdycpO1xyXG5cdFx0fVxyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogT24gU2VuZCBJbnZvaWNlIENsaWNrLlxyXG5cdCAqXHJcblx0ICogU2VuZHMgdGhlIGVtYWlsIGludm9pY2UuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgRmlyZWQgZXZlbnQuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uQnVsa0VtYWlsSW52b2ljZUNsaWNrKGV2ZW50KSB7XHJcblx0XHRjb25zdCAkbW9kYWwgPSAkKCcuYnVsay1lbWFpbC1pbnZvaWNlLm1vZGFsJyk7XHJcblx0XHRjb25zdCAkbWFpbExpc3QgPSAkbW9kYWwuZmluZCgnLmVtYWlsLWxpc3QnKTtcclxuXHJcblx0XHRjb25zdCBnZW5lcmF0ZU1haWxSb3dNYXJrdXAgPSBkYXRhID0+IHtcclxuXHRcdFx0Y29uc3QgJHJvdyA9ICQoJzxkaXYvPicsIHtjbGFzczogJ2Zvcm0tZ3JvdXAgZW1haWwtbGlzdC1pdGVtJ30pO1xyXG5cdFx0XHRjb25zdCAkaWRDb2x1bW4gPSAkKCc8ZGl2Lz4nLCB7Y2xhc3M6ICdjb2wtc20tMid9KTtcclxuXHRcdFx0Y29uc3QgJGVtYWlsQ29sdW1uID0gJCgnPGRpdi8+Jywge2NsYXNzOiAnY29sLXNtLTEwJ30pO1xyXG5cclxuXHRcdFx0Y29uc3QgJGlkTGFiZWwgPSAkKCc8bGFiZWwvPicsIHtcclxuXHRcdFx0XHRjbGFzczogJ2NvbnRyb2wtbGFiZWwgaWQtbGFiZWwgZm9yY2UtdGV4dC1jb2xvci1ibGFjayBmb3JjZS10ZXh0LW5vcm1hbC13ZWlnaHQnLFxyXG5cdFx0XHRcdHRleHQ6IGRhdGEuaWRcclxuXHRcdFx0fSk7XHJcblxyXG5cdFx0XHRjb25zdCAkZW1haWxJbnB1dCA9ICQoJzxpbnB1dC8+Jywge1xyXG5cdFx0XHRcdGNsYXNzOiAnZm9ybS1jb250cm9sIGVtYWlsLWlucHV0JyxcclxuXHRcdFx0XHR0eXBlOiAndGV4dCcsXHJcblx0XHRcdFx0dmFsdWU6IGRhdGEuY3VzdG9tZXJFbWFpbFxyXG5cdFx0XHR9KTtcclxuXHJcblx0XHRcdCRpZExhYmVsLmFwcGVuZFRvKCRpZENvbHVtbik7XHJcblx0XHRcdCRlbWFpbElucHV0LmFwcGVuZFRvKCRlbWFpbENvbHVtbik7XHJcblxyXG5cdFx0XHQkcm93LmFwcGVuZChbJGlkQ29sdW1uLCAkZW1haWxDb2x1bW5dKTtcclxuXHRcdFx0JHJvdy5kYXRhKCdpbnZvaWNlJywgZGF0YSk7XHJcblxyXG5cdFx0XHRyZXR1cm4gJHJvdztcclxuXHRcdH07XHJcblxyXG5cdFx0Y29uc3Qgc2VsZWN0ZWRJbnZvaWNlID0gW107XHJcblxyXG5cdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuXHJcblx0XHQkdGhpcy5maW5kKCd0Ym9keSBpbnB1dDpjaGVja2JveDpjaGVja2VkJykuZWFjaChmdW5jdGlvbigpIHtcclxuXHRcdFx0Y29uc3Qgcm93RGF0YSA9ICQodGhpcykucGFyZW50cygndHInKS5kYXRhKCk7XHJcblx0XHRcdHNlbGVjdGVkSW52b2ljZS5wdXNoKHJvd0RhdGEpO1xyXG5cdFx0fSk7XHJcblxyXG5cdFx0aWYgKHNlbGVjdGVkSW52b2ljZS5sZW5ndGgpIHtcclxuXHRcdFx0JG1haWxMaXN0LmVtcHR5KCk7XHJcblx0XHRcdHNlbGVjdGVkSW52b2ljZS5mb3JFYWNoKG9yZGVyID0+ICRtYWlsTGlzdC5hcHBlbmQoZ2VuZXJhdGVNYWlsUm93TWFya3VwKG9yZGVyKSkpO1xyXG5cdFx0XHQkbW9kYWwubW9kYWwoJ3Nob3cnKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIENvbGxlY3RzIHRoZSBJRHMgb2YgdGhlIHNlbGVjdGVkIG9yZGVycyBhbmQgcmV0dXJucyB0aGVtIGFzIGFuIGFycmF5XHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBUaGUgdHJpZ2dlcmluZyB0YXJnZXRcclxuXHQgKlxyXG5cdCAqIEByZXR1cm4ge051bWJlcltdfSBhcnJheSBvZiBvcmRlciBJRHNcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfZ2V0U2VsZWN0ZWRPcmRlcnMoJHRhcmdldCkge1xyXG5cdFx0Y29uc3Qgc2VsZWN0ZWRPcmRlcnMgPSBbXTtcclxuXHJcblx0XHRpZiAoJHRhcmdldC5wYXJlbnRzKCcuYnVsay1hY3Rpb24nKS5sZW5ndGggPiAwKSB7XHJcblx0XHRcdC8vIEZldGNoIHRoZSBzZWxlY3RlZCBvcmRlciBJRHMuXHJcblx0XHRcdCR0aGlzLmZpbmQoJ3Rib2R5IGlucHV0OmNoZWNrYm94OmNoZWNrZWQnKS5lYWNoKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdHNlbGVjdGVkT3JkZXJzLnB1c2goJCh0aGlzKS5wYXJlbnRzKCd0cicpLmRhdGEoJ2lkJykpO1xyXG5cdFx0XHR9KTtcclxuXHRcdH0gZWxzZSB7XHJcblx0XHRcdGNvbnN0IHJvd0lkID0gJHRhcmdldC5wYXJlbnRzKCd0cicpLmRhdGEoJ2lkJyk7XHJcblxyXG5cdFx0XHRpZiAoIXJvd0lkKSB7XHJcblx0XHRcdFx0cmV0dXJuOyAvLyBObyBvcmRlciBJRCB3YXMgZm91bmQuXHJcblx0XHRcdH1cclxuXHJcblx0XHRcdHNlbGVjdGVkT3JkZXJzLnB1c2gocm93SWQpO1xyXG5cdFx0fVxyXG5cclxuXHRcdHJldHVybiBzZWxlY3RlZE9yZGVycztcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIE9uIEVtYWlsIEludm9pY2UgQ2xpY2tcclxuXHQgKlxyXG5cdCAqIERpc3BsYXkgdGhlIGVtYWlsLWludm9pY2UgbW9kYWwuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uRW1haWxJbnZvaWNlQ2xpY2soKSB7XHJcblx0XHRjb25zdCAkbW9kYWwgPSAkKCcuZW1haWwtaW52b2ljZS5tb2RhbCcpO1xyXG5cdFx0Y29uc3Qgcm93RGF0YSA9ICQodGhpcykucGFyZW50cygndHInKS5kYXRhKCk7XHJcblx0XHRjb25zdCB1cmwgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwJztcclxuXHRcdGNvbnN0IGRhdGEgPSB7XHJcblx0XHRcdGlkOiByb3dEYXRhLmlkLFxyXG5cdFx0XHRkYXRlOiByb3dEYXRhLnB1cmNoYXNlRGF0ZS5kYXRlLFxyXG5cdFx0XHRkbzogJ09yZGVyc01vZGFsc0FqYXgvR2V0RW1haWxJbnZvaWNlU3ViamVjdCcsXHJcblx0XHRcdHBhZ2VUb2tlbjoganNlLmNvcmUuY29uZmlnLmdldCgncGFnZVRva2VuJylcclxuXHRcdH07XHJcblxyXG5cdFx0JG1vZGFsLmZpbmQoJy5jdXN0b21lci1pbmZvJykudGV4dChgXCIke3Jvd0RhdGEuY3VzdG9tZXJOYW1lfVwiIFwiJHtyb3dEYXRhLmN1c3RvbWVyRW1haWx9XCJgKTtcclxuXHRcdCRtb2RhbC5maW5kKCcuZW1haWwtYWRkcmVzcycpLnZhbChyb3dEYXRhLmN1c3RvbWVyRW1haWwpO1xyXG5cclxuXHRcdCRtb2RhbFxyXG5cdFx0XHQuZGF0YSgnb3JkZXJJZCcsIHJvd0RhdGEuaWQpXHJcblx0XHRcdC5tb2RhbCgnc2hvdycpO1xyXG5cclxuXHRcdCQuYWpheCh7dXJsLCBkYXRhLCBkYXRhVHlwZTogJ2pzb24nfSkuZG9uZSgocmVzcG9uc2UpID0+IHtcclxuXHRcdFx0JG1vZGFsLmZpbmQoJy5zdWJqZWN0JykudmFsKHJlc3BvbnNlLnN1YmplY3QpO1xyXG5cdFx0XHRpZihyZXNwb25zZS5pbnZvaWNlSWRFeGlzdHMpIHtcclxuXHRcdFx0XHQkbW9kYWwuZmluZCgnLmludm9pY2UtbnVtLWluZm8nKS5hZGRDbGFzcygnaGlkZGVuJyk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0JG1vZGFsLmZpbmQoJy5pbnZvaWNlLW51bS1pbmZvJykucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIE9uIEVtYWlsIE9yZGVyIENsaWNrXHJcblx0ICpcclxuXHQgKiBEaXNwbGF5IHRoZSBlbWFpbC1vcmRlciBtb2RhbC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkVtYWlsT3JkZXJDbGljayhldmVudCkge1xyXG5cdFx0Y29uc3QgJG1vZGFsID0gJCgnLmVtYWlsLW9yZGVyLm1vZGFsJyk7XHJcblx0XHRjb25zdCByb3dEYXRhID0gJCh0aGlzKS5wYXJlbnRzKCd0cicpLmRhdGEoKTtcclxuXHRcdGNvbnN0IGRhdGVGb3JtYXQgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdsYW5ndWFnZUNvZGUnKSA9PT0gJ2RlJyA/ICdERC5NTS5ZWScgOiAnTU0uREQuWVknO1xyXG5cclxuXHRcdCRtb2RhbC5maW5kKCcuY3VzdG9tZXItaW5mbycpLnRleHQoYFwiJHtyb3dEYXRhLmN1c3RvbWVyTmFtZX1cIiBcIiR7cm93RGF0YS5jdXN0b21lckVtYWlsfVwiYCk7XHJcblx0XHQkbW9kYWwuZmluZCgnLnN1YmplY3QnKS52YWwoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ09SREVSX1NVQkpFQ1QnLCAnZ21fb3JkZXJfbWVudScpICsgcm93RGF0YS5pZFxyXG5cdFx0XHQrIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdPUkRFUl9TVUJKRUNUX0ZST00nLCAnZ21fb3JkZXJfbWVudScpXHJcblx0XHRcdCsgbW9tZW50KHJvd0RhdGEucHVyY2hhc2VEYXRlKS5mb3JtYXQoZGF0ZUZvcm1hdCkpO1xyXG5cdFx0JG1vZGFsLmZpbmQoJy5lbWFpbC1hZGRyZXNzJykudmFsKHJvd0RhdGEuY3VzdG9tZXJFbWFpbCk7XHJcblxyXG5cdFx0JG1vZGFsXHJcblx0XHRcdC5kYXRhKCdvcmRlcklkJywgcm93RGF0YS5pZClcclxuXHRcdFx0Lm1vZGFsKCdzaG93Jyk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBPbiBDaGFuZ2UgT3JkZXIgU3RhdHVzIENsaWNrXHJcblx0ICpcclxuXHQgKiBEaXNwbGF5IHRoZSBjaGFuZ2Ugb3JkZXIgc3RhdHVzIG1vZGFsLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uQ2hhbmdlT3JkZXJTdGF0dXNDbGljayhldmVudCkge1xyXG5cdFx0aWYgKCQoZXZlbnQudGFyZ2V0KS5oYXNDbGFzcygnb3JkZXItc3RhdHVzJykpIHtcclxuXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XHJcblx0XHR9XHJcblxyXG5cdFx0Y29uc3QgJG1vZGFsID0gJCgnLnN0YXR1cy5tb2RhbCcpO1xyXG5cdFx0Y29uc3Qgcm93RGF0YSA9ICQodGhpcykucGFyZW50cygndHInKS5kYXRhKCk7XHJcblx0XHRjb25zdCBzZWxlY3RlZE9yZGVycyA9IF9nZXRTZWxlY3RlZE9yZGVycygkKHRoaXMpKTtcclxuXHJcblx0XHQkbW9kYWwuZmluZCgnI3N0YXR1cy1kcm9wZG93bicpLnZhbCgocm93RGF0YSkgPyByb3dEYXRhLnN0YXR1c0lkIDogJycpO1xyXG5cclxuXHRcdCRtb2RhbC5maW5kKCcjY29tbWVudCcpLnZhbCgnJyk7XHJcblx0XHQkbW9kYWwuZmluZCgnI25vdGlmeS1jdXN0b21lciwgI3NlbmQtcGFyY2VsLXRyYWNraW5nLWNvZGUsICNzZW5kLWNvbW1lbnQnKVxyXG5cdFx0XHQuYXR0cignY2hlY2tlZCcsIGZhbHNlKVxyXG5cdFx0XHQucGFyZW50cygnLnNpbmdsZS1jaGVja2JveCcpXHJcblx0XHRcdC5yZW1vdmVDbGFzcygnY2hlY2tlZCcpO1xyXG5cclxuXHRcdC8vIFNob3cgdGhlIG9yZGVyIGRlbGV0ZSBtb2RhbC5cclxuXHRcdCRtb2RhbC5maW5kKCcuc2VsZWN0ZWQtb3JkZXJzJykudGV4dChzZWxlY3RlZE9yZGVycy5qb2luKCcsICcpKTtcclxuXHRcdCRtb2RhbC5tb2RhbCgnc2hvdycpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogT24gQWRkIFRyYWNraW5nIE51bWJlciBDbGlja1xyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uQWRkVHJhY2tpbmdOdW1iZXJDbGljayhldmVudCkge1xyXG5cdFx0Y29uc3QgJG1vZGFsID0gJCgnLmFkZC10cmFja2luZy1udW1iZXIubW9kYWwnKTtcclxuXHRcdGNvbnN0IHJvd0RhdGEgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCk7XHJcblxyXG5cdFx0JG1vZGFsLmRhdGEoJ29yZGVySWQnLCByb3dEYXRhLmlkKTtcclxuXHRcdCRtb2RhbC5tb2RhbCgnc2hvdycpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogT3BlbnMgdGhlIGdtX3BkZl9vcmRlci5waHAgaW4gYSBuZXcgdGFiIHdpdGggaW52b2ljZXMgYXMgdHlwZSAkX0dFVCBhcmd1bWVudC5cclxuXHQgKlxyXG5cdCAqIFRoZSBvcmRlciBpZHMgYXJlIHBhc3NlZCBhcyBhIHNlcmlhbGl6ZWQgYXJyYXkgdG8gdGhlIG9JRCAkX0dFVCBhcmd1bWVudC5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25CdWxrRG93bmxvYWRJbnZvaWNlQ2xpY2soKSB7XHJcblx0XHRjb25zdCBvcmRlcklkcyA9IFtdO1xyXG5cdFx0Y29uc3QgbWF4QW1vdW50SW52b2ljZXNCdWxrUGRmID0gJCgnI21heC1hbW91bnQtaW52b2ljZXMtYnVsay1wZGYnKS50ZXh0KCk7XHJcblx0XHRsZXQgJG1vZGFsO1xyXG5cdFx0bGV0ICRpbnZvaWNlTWVzc2FnZUNvbnRhaW5lcjtcclxuXHJcblx0XHQkdGhpcy5maW5kKCd0Ym9keSBpbnB1dDpjaGVja2JveDpjaGVja2VkJykuZWFjaChmdW5jdGlvbigpIHtcclxuXHRcdFx0b3JkZXJJZHMucHVzaCgkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKSk7XHJcblx0XHR9KTtcclxuXHJcblx0XHRpZiAob3JkZXJJZHMubGVuZ3RoID4gbWF4QW1vdW50SW52b2ljZXNCdWxrUGRmKSB7XHJcblx0XHRcdCRtb2RhbCA9ICQoJy5idWxrLWVycm9yLm1vZGFsJyk7XHJcblx0XHRcdCRtb2RhbC5tb2RhbCgnc2hvdycpO1xyXG5cdFx0XHQkaW52b2ljZU1lc3NhZ2VDb250YWluZXIgPSAkbW9kYWwuZmluZCgnLmludm9pY2VzLW1lc3NhZ2UnKTtcclxuXHJcblx0XHRcdCRpbnZvaWNlTWVzc2FnZUNvbnRhaW5lci5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XHJcblxyXG5cdFx0XHQkbW9kYWwub24oJ2hpZGUuYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHQkaW52b2ljZU1lc3NhZ2VDb250YWluZXIuYWRkQ2xhc3MoJ2hpZGRlbicpO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9jcmVhdGVCdWxrUGRmKG9yZGVySWRzLCAnaW52b2ljZScpO1xyXG5cdH1cclxuXHJcblxyXG5cdC8qKlxyXG5cdCAqIE9wZW5zIHRoZSBnbV9wZGZfb3JkZXIucGhwIGluIGEgbmV3IHRhYiB3aXRoIHBhY2tpbmcgc2xpcCBhcyB0eXBlICRfR0VUIGFyZ3VtZW50LlxyXG5cdCAqXHJcblx0ICogVGhlIG9yZGVyIGlkcyBhcmUgcGFzc2VkIGFzIGEgc2VyaWFsaXplZCBhcnJheSB0byB0aGUgb0lEICRfR0VUIGFyZ3VtZW50LlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkJ1bGtEb3dubG9hZFBhY2tpbmdTbGlwQ2xpY2soKSB7XHJcblx0XHRjb25zdCBvcmRlcklkcyA9IFtdO1xyXG5cdFx0Y29uc3QgbWF4QW1vdW50UGFja2luZ1NsaXBzQnVsa1BkZiA9ICQoJyNtYXgtYW1vdW50LXBhY2tpbmctc2xpcHMtYnVsay1wZGYnKS50ZXh0KCk7XHJcblx0XHRsZXQgJG1vZGFsO1xyXG5cdFx0bGV0ICRwYWNraW5nU2xpcHNNZXNzYWdlQ29udGFpbmVyO1xyXG5cclxuXHRcdCR0aGlzLmZpbmQoJ3Rib2R5IGlucHV0OmNoZWNrYm94OmNoZWNrZWQnKS5lYWNoKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRvcmRlcklkcy5wdXNoKCQodGhpcykucGFyZW50cygndHInKS5kYXRhKCdpZCcpKTtcclxuXHRcdH0pO1xyXG5cclxuXHRcdGlmIChvcmRlcklkcy5sZW5ndGggPiBtYXhBbW91bnRQYWNraW5nU2xpcHNCdWxrUGRmKSB7XHJcblx0XHRcdCRtb2RhbCA9ICQoJy5idWxrLWVycm9yLm1vZGFsJyk7XHJcblx0XHRcdCRtb2RhbC5tb2RhbCgnc2hvdycpO1xyXG5cdFx0XHQkcGFja2luZ1NsaXBzTWVzc2FnZUNvbnRhaW5lciA9ICRtb2RhbC5maW5kKCcucGFja2luZy1zbGlwcy1tZXNzYWdlJyk7XHJcblxyXG5cdFx0XHQkcGFja2luZ1NsaXBzTWVzc2FnZUNvbnRhaW5lci5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XHJcblxyXG5cdFx0XHQkbW9kYWwub24oJ2hpZGUuYnMubW9kYWwnLCBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHQkcGFja2luZ1NsaXBzTWVzc2FnZUNvbnRhaW5lci5hZGRDbGFzcygnaGlkZGVuJyk7XHJcblx0XHRcdH0pO1xyXG5cclxuXHRcdFx0cmV0dXJuO1xyXG5cdFx0fVxyXG5cclxuXHRcdF9jcmVhdGVCdWxrUGRmKG9yZGVySWRzLCAncGFja2luZ3NsaXAnKTtcclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIENyZWF0ZXMgYSBidWxrIHBkZiB3aXRoIGludm9pY2VzIG9yIHBhY2tpbmcgc2xpcHMgaW5mb3JtYXRpb24uXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge051bWJlcltdfSBvcmRlcklkc1xyXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSB0eXBlXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX2NyZWF0ZUJ1bGtQZGYob3JkZXJJZHMsIHR5cGUpIHtcclxuXHRcdGNvbnN0IGRlZmVycmVkcyA9IFtdO1xyXG5cdFx0Y29uc3QgekluZGV4ID0gJCgnLnRhYmxlLWZpeGVkLWhlYWRlciB0aGVhZC5maXhlZCcpLmNzcygnei1pbmRleCcpOyAvLyBDb3VsZCBiZSBcInVuZGVmaW5lZFwiIGFzIHdlbGwuXHJcblxyXG5cdFx0JHNwaW5uZXIgPSBqc2UubGlicy5sb2FkaW5nX3NwaW5uZXIuc2hvdygkdGhpcywgekluZGV4KTtcclxuXHJcblx0XHRvcmRlcklkcy5mb3JFYWNoKChpZCkgPT4ge1xyXG5cdFx0XHRjb25zdCBkYXRhID0ge1xyXG5cdFx0XHRcdHR5cGUsXHJcblx0XHRcdFx0YWpheDogJzEnLFxyXG5cdFx0XHRcdG9JRDogaWRcclxuXHRcdFx0fTtcclxuXHJcblx0XHRcdGRlZmVycmVkcy5wdXNoKCQuZ2V0SlNPTihqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vZ21fcGRmX29yZGVyLnBocCcsIGRhdGEpKTtcclxuXHRcdH0pO1xyXG5cclxuXHRcdCQud2hlbi5hcHBseShudWxsLCBkZWZlcnJlZHMpLmRvbmUoKCkgPT4ge1xyXG5cdFx0XHRfb3BlbkJ1bGtQZGZVcmwob3JkZXJJZHMsIHR5cGUpO1xyXG5cclxuXHRcdFx0Ly8gS2VlcCBjaGVja2JveGVzIGNoZWNrZWQgYWZ0ZXIgYSBkYXRhdGFibGUgcmVsb2FkLlxyXG5cdFx0XHQkdGhpcy5EYXRhVGFibGUoKS5hamF4LnJlbG9hZCgoKSA9PiB7XHJcblx0XHRcdFx0JHRoaXNcclxuXHRcdFx0XHRcdC5vZmYoJ3NpbmdsZV9jaGVja2JveDpyZWFkeScsIF9vblNpbmdsZUNoZWNrYm94UmVhZHkpXHJcblx0XHRcdFx0XHQub24oJ3NpbmdsZV9jaGVja2JveDpyZWFkeScsIHtvcmRlcklkc30sIF9vblNpbmdsZUNoZWNrYm94UmVhZHkpO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0JHRoaXMub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcigncmVsb2FkJyk7XHJcblx0XHR9KTtcclxuXHR9XHJcblxyXG5cdGZ1bmN0aW9uIF9vblNpbmdsZUNoZWNrYm94UmVhZHkoZXZlbnQpIHtcclxuXHRcdGV2ZW50LmRhdGEub3JkZXJJZHMuZm9yRWFjaCgoaWQpID0+IHtcclxuXHRcdFx0JHRoaXMuZmluZChgdHIjJHtpZH0gaW5wdXQ6Y2hlY2tib3hgKS5zaW5nbGVfY2hlY2tib3goJ2NoZWNrZWQnLCB0cnVlKS50cmlnZ2VyKCdjaGFuZ2UnKTtcclxuXHRcdH0pO1xyXG5cclxuXHRcdC8vIEJ1bGsgYWN0aW9uIGJ1dHRvbiBzaG91bGQndCBiZSBkaXNhYmxlZCBhZnRlciBhIGRhdGF0YWJsZSByZWxvYWQuXHJcblx0XHRpZiAoJCgndHIgaW5wdXQ6Y2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aCkge1xyXG5cdFx0XHQkKCcuYnVsay1hY3Rpb24nKS5maW5kKCdidXR0b24nKS5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKTtcclxuXHRcdH1cclxuXHR9XHJcblxyXG5cdC8qKlxyXG5cdCAqIE9wZW5zIHRoZSB1cmwgd2hpY2ggcHJvdmlkZSB0aGUgYnVsayBQREYncyBhcyBkb3dubG9hZC5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7TnVtYmVyW119IGNhbGxiYWNrc1xyXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSB0eXBlXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29wZW5CdWxrUGRmVXJsKG9yZGVySWRzLCB0eXBlKSB7XHJcblx0XHRjb25zdCBwYXJhbWV0ZXJzID0ge1xyXG5cdFx0XHRkbzogJ09yZGVyc01vZGFsc0FqYXgvQnVsa1BkZicgKyAodHlwZSA9PT0gJ2ludm9pY2UnID8gJ0ludm9pY2VzJyA6ICdQYWNraW5nU2xpcHMnKSxcclxuXHRcdFx0cGFnZVRva2VuOiBqc2UuY29yZS5jb25maWcuZ2V0KCdwYWdlVG9rZW4nKSxcclxuXHRcdFx0bzogb3JkZXJJZHNcclxuXHRcdH07XHJcblxyXG5cdFx0Y29uc3QgdXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2FkbWluLnBocD8nICsgJC5wYXJhbShwYXJhbWV0ZXJzKTtcclxuXHJcblx0XHRjb25zdCBkb3dubG9hZFBkZldpbmRvdyA9IHdpbmRvdy5vcGVuKHVybCwgJ19ibGFuaycpO1xyXG5cclxuXHRcdGlmICghZG93bmxvYWRQZGZXaW5kb3cpIHtcclxuXHRcdFx0Y29uc3QgJG1vZGFsID0gJCgnLmJ1bGstZG93bmxvYWQtZXJyb3IubW9kYWwnKTtcclxuXHRcdFx0Y29uc3QgJGRvd25sb2FkTGluayA9ICRtb2RhbC5maW5kKCcuZG93bmxvYWQtYnVsay1idG4nKTtcclxuXHRcdFx0JGRvd25sb2FkTGluay5hdHRyKCdocmVmJywgdXJsKTtcclxuXHRcdFx0JG1vZGFsLm1vZGFsKCdzaG93Jyk7XHJcblx0XHR9XHJcblxyXG5cdFx0anNlLmxpYnMubG9hZGluZ19zcGlubmVyLmhpZGUoJHNwaW5uZXIpO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogT24gSW52b2ljZSBMaW5rIENsaWNrXHJcblx0ICpcclxuXHQgKiBUaGUgc2NyaXB0IHRoYXQgZ2VuZXJhdGVzIHRoZSBQREZzIGlzIGNoYW5naW5nIHRoZSBzdGF0dXMgb2YgYW4gb3JkZXIgdG8gXCJpbnZvaWNlLWNyZWF0ZWRcIi4gVGh1cyB0aGVcclxuXHQgKiB0YWJsZSBkYXRhIG5lZWQgdG8gYmUgcmVkcmF3biBhbmQgdGhlIGZpbHRlciBvcHRpb25zIHRvIGJlIHVwZGF0ZWQuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uSW52b2ljZUNsaWNrKCkge1xyXG5cdFx0Y29uc3QgbGluayA9ICQodGhpcykuYXR0cignaHJlZicpO1xyXG5cclxuXHRcdHdpbmRvdy5vcGVuKGxpbmssICdfYmxhbmsnKTtcclxuXHRcdCR0aGlzLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XHJcblx0fVxyXG5cclxuXHQvKipcclxuXHQgKiBPbiBFZGl0IFJvdyBBY3Rpb24gQ2xpY2tcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25FZGl0T3JkZXJDbGljaygpIHtcclxuXHRcdGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKTtcclxuXHJcblx0XHRjb25zdCBwYXJhbWV0ZXJzID0ge1xyXG5cdFx0XHRvSUQ6IG9yZGVySWQsXHJcblx0XHRcdGFjdGlvbjogJ2VkaXQnLFxyXG5cdFx0XHRvdmVydmlldzogJC5kZXBhcmFtKHdpbmRvdy5sb2NhdGlvbi5zZWFyY2guc2xpY2UoMSkpXHJcblx0XHR9O1xyXG5cclxuXHRcdHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gJ29yZGVycy5waHA/JyArICQucGFyYW0ocGFyYW1ldGVycyk7XHJcblx0fVxyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdC8vIEJpbmQgdGFibGUgcm93IGFjdGlvbnMuXHJcblx0XHQkdGhpc1xyXG5cdFx0XHQub24oJ2NsaWNrJywgJ3Rib2R5IHRyJywgX29uVGFibGVSb3dDbGljaylcclxuXHRcdFx0Lm9uKCdjaGFuZ2UnLCAnLmJ1bGstc2VsZWN0aW9uJywgX29uQnVsa1NlbGVjdGlvbkNoYW5nZSlcclxuXHRcdFx0Lm9uKCdjaGFuZ2UnLCAnaW5wdXQ6Y2hlY2tib3gnLCBfb25UYWJsZVJvd0NoZWNrYm94Q2hhbmdlKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5pbnZvaWNlJywgX29uSW52b2ljZUNsaWNrKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5lbWFpbC1pbnZvaWNlJywgX29uRW1haWxJbnZvaWNlQ2xpY2spXHJcblx0XHRcdC5vbignY2xpY2snLCAnLmVtYWlsLW9yZGVyJywgX29uRW1haWxPcmRlckNsaWNrKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5vcmRlci1zdGF0dXMubGFiZWwnLCBfb25DaGFuZ2VPcmRlclN0YXR1c0NsaWNrKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5hZGQtdHJhY2tpbmctbnVtYmVyJywgX29uQWRkVHJhY2tpbmdOdW1iZXJDbGljaylcclxuXHRcdFx0Lm9uKCdjbGljaycsICcuYWN0aW9ucyAuZWRpdCcsIF9vbkVkaXRPcmRlckNsaWNrKTtcclxuXHJcblx0XHQvLyBCaW5kIHRhYmxlIHJvdyBhbmQgYnVsayBhY3Rpb25zLlxyXG5cdFx0JHRoaXMucGFyZW50cygnLm9yZGVycycpXHJcblx0XHRcdC5vbignY2xpY2snLCAnLmJ0bi1ncm91cCAuY2hhbmdlLXN0YXR1cycsIF9vbkNoYW5nZU9yZGVyU3RhdHVzQ2xpY2spXHJcblx0XHRcdC5vbignY2xpY2snLCAnLmJ0bi1ncm91cCAuY2FuY2VsJywgX29uQ2FuY2VsT3JkZXJDbGljaylcclxuXHRcdFx0Lm9uKCdjbGljaycsICcuYnRuLWdyb3VwIC5kZWxldGUsIC5hY3Rpb25zIC5kZWxldGUnLCBfb25EZWxldGVPcmRlckNsaWNrKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5idG4tZ3JvdXAgLmJ1bGstZW1haWwtb3JkZXInLCBfb25CdWxrRW1haWxPcmRlckNsaWNrKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5idG4tZ3JvdXAgLmJ1bGstZW1haWwtaW52b2ljZScsIF9vbkJ1bGtFbWFpbEludm9pY2VDbGljaylcclxuXHRcdFx0Lm9uKCdjbGljaycsICcuYnRuLWdyb3VwIC5idWxrLWRvd25sb2FkLWludm9pY2UnLCBfb25CdWxrRG93bmxvYWRJbnZvaWNlQ2xpY2spXHJcblx0XHRcdC5vbignY2xpY2snLCAnLmJ0bi1ncm91cCAuYnVsay1kb3dubG9hZC1wYWNraW5nLXNsaXAnLCBfb25CdWxrRG93bmxvYWRQYWNraW5nU2xpcENsaWNrKTtcclxuXHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHJcblx0cmV0dXJuIG1vZHVsZTtcclxufSk7XHJcbiJdfQ==
