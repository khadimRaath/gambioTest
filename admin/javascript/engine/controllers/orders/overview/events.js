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
gx.controllers.module('events', ['loading_spinner'], function(data) {

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

	/**
	 * Loading spinner instance.
	 *
	 * @type {jQuery|null}
	 */
	let $spinner = null;

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
	 * On Bulk Selection Change
	 *
	 * @param {jQuery.Event} event jQuery event object.
	 * @param {Boolean} propagate Whether to affect the body elements. We do not need this on "draw.dt" event.
	 */
	function _onBulkSelectionChange(event, propagate = true) {
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

		$(this).find('input:checkbox')
			.prop('checked', !$(this).find('input:checkbox').prop('checked'))
			.trigger('change');
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

		const selectedOrders = _getSelectedOrders($(this));

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

		const selectedOrders = _getSelectedOrders($(this));

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
		const $modal = $('.bulk-email-order.modal');
		const $mailList = $modal.find('.email-list');

		const generateMailRowMarkup = data => {
			const $row = $('<div/>', {class: 'form-group email-list-item'});
			const $idColumn = $('<div/>', {class: 'col-sm-2'});
			const $emailColumn = $('<div/>', {class: 'col-sm-10'});

			const $idLabel = $('<label/>', {
				class: 'control-label id-label force-text-color-black force-text-normal-weight',
				text: data.id
			});

			const $emailInput = $('<input/>', {
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

		const selectedOrders = [];

		event.preventDefault();

		$this.find('tbody input:checkbox:checked').each(function() {
			const rowData = $(this).parents('tr').data();
			selectedOrders.push(rowData);
		});

		if (selectedOrders.length) {
			$mailList.empty();
			selectedOrders.forEach(order => $mailList.append(generateMailRowMarkup(order)));
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
		const $modal = $('.bulk-email-invoice.modal');
		const $mailList = $modal.find('.email-list');

		const generateMailRowMarkup = data => {
			const $row = $('<div/>', {class: 'form-group email-list-item'});
			const $idColumn = $('<div/>', {class: 'col-sm-2'});
			const $emailColumn = $('<div/>', {class: 'col-sm-10'});

			const $idLabel = $('<label/>', {
				class: 'control-label id-label force-text-color-black force-text-normal-weight',
				text: data.id
			});

			const $emailInput = $('<input/>', {
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

		const selectedInvoice = [];

		event.preventDefault();

		$this.find('tbody input:checkbox:checked').each(function() {
			const rowData = $(this).parents('tr').data();
			selectedInvoice.push(rowData);
		});

		if (selectedInvoice.length) {
			$mailList.empty();
			selectedInvoice.forEach(order => $mailList.append(generateMailRowMarkup(order)));
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
		const selectedOrders = [];

		if ($target.parents('.bulk-action').length > 0) {
			// Fetch the selected order IDs.
			$this.find('tbody input:checkbox:checked').each(function() {
				selectedOrders.push($(this).parents('tr').data('id'));
			});
		} else {
			const rowId = $target.parents('tr').data('id');

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
		const $modal = $('.email-invoice.modal');
		const rowData = $(this).parents('tr').data();
		const url = jse.core.config.get('appUrl') + '/admin/admin.php';
		const data = {
			id: rowData.id,
			date: rowData.purchaseDate.date,
			do: 'OrdersModalsAjax/GetEmailInvoiceSubject',
			pageToken: jse.core.config.get('pageToken')
		};

		$modal.find('.customer-info').text(`"${rowData.customerName}" "${rowData.customerEmail}"`);
		$modal.find('.email-address').val(rowData.customerEmail);

		$modal
			.data('orderId', rowData.id)
			.modal('show');

		$.ajax({url, data, dataType: 'json'}).done((response) => {
			$modal.find('.subject').val(response.subject);
			if(response.invoiceIdExists) {
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
		const $modal = $('.email-order.modal');
		const rowData = $(this).parents('tr').data();
		const dateFormat = jse.core.config.get('languageCode') === 'de' ? 'DD.MM.YY' : 'MM.DD.YY';

		$modal.find('.customer-info').text(`"${rowData.customerName}" "${rowData.customerEmail}"`);
		$modal.find('.subject').val(jse.core.lang.translate('ORDER_SUBJECT', 'gm_order_menu') + rowData.id
			+ jse.core.lang.translate('ORDER_SUBJECT_FROM', 'gm_order_menu')
			+ moment(rowData.purchaseDate).format(dateFormat));
		$modal.find('.email-address').val(rowData.customerEmail);

		$modal
			.data('orderId', rowData.id)
			.modal('show');
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

		const $modal = $('.status.modal');
		const rowData = $(this).parents('tr').data();
		const selectedOrders = _getSelectedOrders($(this));

		$modal.find('#status-dropdown').val((rowData) ? rowData.statusId : '');

		$modal.find('#comment').val('');
		$modal.find('#notify-customer, #send-parcel-tracking-code, #send-comment')
			.attr('checked', false)
			.parents('.single-checkbox')
			.removeClass('checked');

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
		const $modal = $('.add-tracking-number.modal');
		const rowData = $(event.target).parents('tr').data();

		$modal.data('orderId', rowData.id);
		$modal.modal('show');
	}

	/**
	 * Opens the gm_pdf_order.php in a new tab with invoices as type $_GET argument.
	 *
	 * The order ids are passed as a serialized array to the oID $_GET argument.
	 */
	function _onBulkDownloadInvoiceClick() {
		const orderIds = [];
		const maxAmountInvoicesBulkPdf = $('#max-amount-invoices-bulk-pdf').text();
		let $modal;
		let $invoiceMessageContainer;

		$this.find('tbody input:checkbox:checked').each(function() {
			orderIds.push($(this).parents('tr').data('id'));
		});

		if (orderIds.length > maxAmountInvoicesBulkPdf) {
			$modal = $('.bulk-error.modal');
			$modal.modal('show');
			$invoiceMessageContainer = $modal.find('.invoices-message');

			$invoiceMessageContainer.removeClass('hidden');

			$modal.on('hide.bs.modal', function() {
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
		const orderIds = [];
		const maxAmountPackingSlipsBulkPdf = $('#max-amount-packing-slips-bulk-pdf').text();
		let $modal;
		let $packingSlipsMessageContainer;

		$this.find('tbody input:checkbox:checked').each(function() {
			orderIds.push($(this).parents('tr').data('id'));
		});

		if (orderIds.length > maxAmountPackingSlipsBulkPdf) {
			$modal = $('.bulk-error.modal');
			$modal.modal('show');
			$packingSlipsMessageContainer = $modal.find('.packing-slips-message');

			$packingSlipsMessageContainer.removeClass('hidden');

			$modal.on('hide.bs.modal', function() {
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
		const deferreds = [];
		const zIndex = $('.table-fixed-header thead.fixed').css('z-index'); // Could be "undefined" as well.

		$spinner = jse.libs.loading_spinner.show($this, zIndex);

		orderIds.forEach((id) => {
			const data = {
				type,
				ajax: '1',
				oID: id
			};

			deferreds.push($.getJSON(jse.core.config.get('appUrl') + '/admin/gm_pdf_order.php', data));
		});

		$.when.apply(null, deferreds).done(() => {
			_openBulkPdfUrl(orderIds, type);

			// Keep checkboxes checked after a datatable reload.
			$this.DataTable().ajax.reload(() => {
				$this
					.off('single_checkbox:ready', _onSingleCheckboxReady)
					.on('single_checkbox:ready', {orderIds}, _onSingleCheckboxReady);
			});
			$this.orders_overview_filter('reload');
		});
	}

	function _onSingleCheckboxReady(event) {
		event.data.orderIds.forEach((id) => {
			$this.find(`tr#${id} input:checkbox`).single_checkbox('checked', true).trigger('change');
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
		const parameters = {
			do: 'OrdersModalsAjax/BulkPdf' + (type === 'invoice' ? 'Invoices' : 'PackingSlips'),
			pageToken: jse.core.config.get('pageToken'),
			o: orderIds
		};

		const url = jse.core.config.get('appUrl') + '/admin/admin.php?' + $.param(parameters);

		const downloadPdfWindow = window.open(url, '_blank');

		if (!downloadPdfWindow) {
			const $modal = $('.bulk-download-error.modal');
			const $downloadLink = $modal.find('.download-bulk-btn');
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
		const link = $(this).attr('href');

		window.open(link, '_blank');
		$this.DataTable().ajax.reload();
	}

	/**
	 * On Edit Row Action Click
	 */
	function _onEditOrderClick() {
		const orderId = $(this).parents('tr').data('id');

		const parameters = {
			oID: orderId,
			action: 'edit',
			overview: $.deparam(window.location.search.slice(1))
		};

		window.location.href = 'orders.php?' + $.param(parameters);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function(done) {
		// Bind table row actions.
		$this
			.on('click', 'tbody tr', _onTableRowClick)
			.on('change', '.bulk-selection', _onBulkSelectionChange)
			.on('change', 'input:checkbox', _onTableRowCheckboxChange)
			.on('click', '.invoice', _onInvoiceClick)
			.on('click', '.email-invoice', _onEmailInvoiceClick)
			.on('click', '.email-order', _onEmailOrderClick)
			.on('click', '.order-status.label', _onChangeOrderStatusClick)
			.on('click', '.add-tracking-number', _onAddTrackingNumberClick)
			.on('click', '.actions .edit', _onEditOrderClick);

		// Bind table row and bulk actions.
		$this.parents('.orders')
			.on('click', '.btn-group .change-status', _onChangeOrderStatusClick)
			.on('click', '.btn-group .cancel', _onCancelOrderClick)
			.on('click', '.btn-group .delete, .actions .delete', _onDeleteOrderClick)
			.on('click', '.btn-group .bulk-email-order', _onBulkEmailOrderClick)
			.on('click', '.btn-group .bulk-email-invoice', _onBulkEmailInvoiceClick)
			.on('click', '.btn-group .bulk-download-invoice', _onBulkDownloadInvoiceClick)
			.on('click', '.btn-group .bulk-download-packing-slip', _onBulkDownloadPackingSlipClick);

		done();
	};

	return module;
});
