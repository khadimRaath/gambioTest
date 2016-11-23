'use strict';

/* --------------------------------------------------------------
	orders_shipcloud.js 2016-03-01
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

/**
 * ## Orders Shipcloud Module
 *
 * This module implements the user interface for creating shipping labels via Shipcloud.io
 *
 * @module Compatibility/orders_shipcloud
 */
gx.compatibility.module('orders_shipcloud', [gx.source + '/libs/action_mapper', gx.source + '/libs/button_dropdown', 'loading_spinner'],

/**  @lends module:Compatibility/orders_shipcloud */
function (data) {

	'use strict';

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * The mapper library
  *
  * @var {object}
  */
	mapper = jse.libs.action_mapper,


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	var _singleFormInit = function _singleFormInit() {
		gx.widgets.init($('#shipcloud_modal'));
		$('#sc_modal_content').removeClass('sc_loading');
		if ($('#sc_single_container').data('is_configured') === 1) {
			$('#sc_show_labels').show();
		} else {
			$('#sc_show_labels').hide();
		}
		$('#sc_single_form').on('submit', function (e) {
			e.preventDefault();
		});
		$('#sc_single_form input.create_label').on('click', _singleFormSubmitHandler);
		$('#sc_single_form select[name="carrier"]').on('change', function (e) {
			$('#sc_single_form input[type="text"]').trigger('change');
			$('#sc_single_form .carrier-specific').not('.carrier-' + $(this).val()).hide('fast');
			$('#sc_single_form .carrier-' + $(this).val()).not(':visible').show('fast');
		});
		$('#sc_single_form .price_value').on('change', function () {
			$('#sc_single_form div.sc_quote').html('');
		});
		$('#sc_package_template').on('change', _templateSelectionHandler);
		$('#sc_single_form input.template_value').on('change', function () {
			$('#sc_package_template').val('-1');
		});
		$('#sc_get_quote').button('disable');
		$('#sc_single_form input[name="quote_carriers[]"]').on('change', function () {
			if ($('#sc_single_form input[name="quote_carriers[]"]:checked').length > 0) {
				$('#sc_get_quote').button('enable');
			} else {
				$('#sc_get_quote').button('disable');
			}
		});
		$('#sc_single_form input[name="quote_carriers[]"]:first').trigger('change');
	};

	var _templateSelectionHandler = function _templateSelectionHandler(e) {
		var $form, $template;
		$form = $(this).closest('form');
		$template = $('option:selected', $(this));
		if ($template.val() !== '-1') {
			$('input[name="package[weight]"]', $form).val($template.data('weight'));
			$('input[name="package[height]"]', $form).val($template.data('height'));
			$('input[name="package[width]"]', $form).val($template.data('width'));
			$('input[name="package[length]"]', $form).val($template.data('length'));
		}
	};

	var _openSingleFormModal = function _openSingleFormModal(event) {
		var orderId = $(event.target).parents('tr').data('row-id') || $('body').find('#gm_order_id').val();
		$('#sc_modal_content').empty().addClass('sc_loading');
		var button_create_label = jse.core.lang.translate('create_label', 'shipcloud'),
		    shipcloud_modal_buttons = [];

		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function click() {
				$(this).dialog('close');
				$('#sc_get_quote').show();
			}
		});
		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('show_existing_labels', 'shipcloud'),
			'class': 'btn',
			'click': _showLabelsHandler,
			'id': 'sc_show_labels'
		});
		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('get_quotes', 'shipcloud'),
			'class': 'btn btn-primary',
			'click': _singleFormGetQuoteHandler,
			'id': 'sc_get_quote'
		});

		$('#shipcloud_modal').dialog({
			autoOpen: false,
			modal: true,
			'title': jse.core.lang.translate('create_label', 'shipcloud'),
			'dialogClass': 'gx-container',
			buttons: shipcloud_modal_buttons,
			width: 1000,
			position: { my: 'center top', at: 'center bottom', of: '.main-top-header' }
		});
		$('#shipcloud_modal').dialog('open');
		$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateLabelForm&orders_id=' + orderId, _singleFormInit);
	};

	var _addShipcloudDropdownEntry = function _addShipcloudDropdownEntry() {
		$('.gx-orders-table tr').not('.dataTableHeadingRow').each(function () {
			jse.libs.button_dropdown.mapAction($(this), 'admin_menu_entry', 'shipcloud', _openSingleFormModal);
		});
		jse.libs.button_dropdown.mapAction($('.order-footer'), 'admin_menu_entry', 'shipcloud', _openSingleFormModal);
	};

	var _labellistPickupCheckboxHandler = function _labellistPickupCheckboxHandler() {
		$('#sc-labellist-dropdown button, div.pickup_time input').prop('disabled', $('input.pickup_checkbox:checked').length === 0);
	};

	var _loadLabelList = function _loadLabelList(orders_id) {
		$('#sc_modal_content').load('admin.php?do=Shipcloud/LoadLabelList&orders_id=' + orders_id, function () {
			gx.widgets.init($('#sc_modal_content'));
			$('#shipcloud_modal').dialog({
				'title': jse.core.lang.translate('labellist_for', 'shipcloud') + ' ' + orders_id
			});
			$('#sc_modal_content').removeClass('sc_loading');

			$('form#sc_pickup').on('submit', function (e) {
				e.preventDefault();
			});
			jse.libs.button_dropdown.mapAction($('#sc-labellist-dropdown'), 'download_labels', 'shipcloud', _packedDownloadHandler);
			jse.libs.button_dropdown.mapAction($('#sc-labellist-dropdown'), 'order_pickups', 'shipcloud', _pickupSubmitHandler);
			$('input.pickup_checkbox').on('click', _labellistPickupCheckboxHandler);
			setTimeout(_labellistPickupCheckboxHandler, 200);
			$('input.pickup_checkbox_all').on('click', function () {
				if ($(this).prop('checked') === true) {
					$('input.pickup_checkbox').prop('checked', true);
					$('input.pickup_checkbox').parent().addClass('checked');
				} else {
					$('input.pickup_checkbox').prop('checked', false);
					$('input.pickup_checkbox').parent().removeClass('checked');
				}
				_labellistPickupCheckboxHandler();
			});
			$('a.sc-del-label').on('click', function (e) {
				e.preventDefault();
				var shipment_id = $(this).data('shipment-id'),
				    $buttonPlace = $(this).closest('span.sc-del-label'),
				    $row = $(this).closest('tr');
				$.ajax({
					type: 'POST',
					url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/DeleteShipment',
					data: { shipment_id: shipment_id },
					dataType: 'json'
				}).done(function (data) {
					if (data.result === 'ERROR') {
						$buttonPlace.html(data.error_message);
						$buttonPlace.addClass('badge').addClass('badge-danger');
					} else {
						$buttonPlace.html(jse.core.lang.translate('shipment_deleted', 'shipcloud'));
						$('a, input, td.checkbox > *', $row).remove();
						$row.addClass('deleted-shipment');
					}
				}).fail(function (data) {
					$buttonPlace.html(jse.core.lang.translate('submit_error', 'shipcloud'));
				});
			});
		});
	};

	var _packedDownloadHandler = function _packedDownloadHandler(e) {
		e.preventDefault();
		var urls = [],
		    request = {};
		$('input.pickup_checkbox:checked').each(function () {
			var href = $('a.label-link', $(this).closest('tr')).attr('href');
			urls.push(href);
		});
		if (urls) {
			$('#download_result').show();
			$('#download_result').html(jse.core.lang.translate('loading', 'shipcloud'));
			request.urls = urls;
			request.page_token = $('#sc_modal_content input[name="page_token"]').val();

			$.ajax({
				type: 'POST',
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=PackedDownload/DownloadByJson',
				data: JSON.stringify(request),
				dataType: 'json'
			}).done(function (data) {
				var downloadlink = jse.core.config.get('appUrl') + '/admin/admin.php?do=PackedDownload/DownloadPackage&key=' + data.downloadKey;
				if (data.result === 'OK') {
					$('#download_result').html('<iframe class="download_iframe" src="' + downloadlink + '"></iframe>');
				}
				if (data.result === 'ERROR') {
					$('#download_result').html(data.error_message);
				}
			}).fail(function (data) {
				$('#download_result').html(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
		}
		return true;
	};

	var _pickupSubmitHandler = function _pickupSubmitHandler(e) {
		e.preventDefault();
		if ($('input.pickup_checkbox:checked').length > 0) {
			var formdata = $('form#sc_pickup').serialize();
			$('#pickup_result').html(jse.core.lang.translate('sending_pickup_request', 'shipcloud'));
			$('#pickup_result').show();
			$.ajax({
				type: 'POST',
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/PickupShipments',
				data: formdata,
				dataType: 'json'
			}).done(function (data) {
				var result_message = '';
				data.result_messages.forEach(function (message) {
					result_message = result_message + message + '<br>';
				});
				$('#pickup_result').html(result_message);
			}).fail(function (data) {
				alert(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
		}
		return true;
	};

	var _loadUnconfiguredNote = function _loadUnconfiguredNote() {
		$('#sc_modal_content').load('admin.php?do=Shipcloud/UnconfiguredNote');
	};

	var _showLabelsHandler = function _showLabelsHandler(e) {
		var orders_id = $('#sc_single_form input[name="orders_id"]').val();
		$('#sc_modal_content').empty().addClass('sc_loading');
		_loadLabelList(orders_id);
		$('#sc_show_labels').hide();
		$('#sc_get_quote').hide();
		return false;
	};

	var _singleFormGetQuoteHandler = function _singleFormGetQuoteHandler() {
		var $form = $('#sc_single_form'),
		    quote = '';

		$('#sc_single_form .sc_quote').html('');
		$('#sc_single_form .sc_quote').attr('title', '');

		$('input[name="quote_carriers[]"]:checked').each(function () {
			var carrier = $(this).val(),
			    $create_label = $('input.create_label', $(this).closest('tr'));
			$('input[name="carrier"]', $form).val(carrier);
			$('#sc_quote_' + carrier).html(jse.core.lang.translate('loading', 'shipcloud'));
			$.ajax({
				type: 'POST',
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/GetShipmentQuote',
				data: $form.serialize(),
				dataType: 'json'
			}).done(function (data) {
				if (data.result === 'OK') {
					quote = data.shipment_quote;
					$('#sc_quote_' + carrier).html(quote);
				} else if (data.result === 'ERROR') {
					$('#sc_quote_' + carrier).html(jse.core.lang.translate('not_possible', 'shipcloud'));
					$('#sc_quote_' + carrier).attr('title', data.error_message);
				} else if (data.result === 'UNCONFIGURED') {
					_loadUnconfiguredNote();
				}
			}).fail(function (data) {
				quote = jse.core.lang.translate('get_quote_error', 'shipcloud');
				$('#sc_quote_' + carrier).html(quote);
			});
		});

		$('input[name="carrier"]', $form).val('');
	};

	var _singleFormSubmitHandler = function _singleFormSubmitHandler(e) {
		var carrier, formdata;
		$('#sc_show_labels').hide();
		$('#sc_get_quote').hide();
		carrier = $(this).attr('name');
		$('input[name="carrier"]').val(carrier);
		formdata = $('#sc_single_form').serialize();
		$('#sc_modal_content').empty().addClass('sc_loading');
		// alert('data: '+formdata);
		$.ajax({
			type: 'POST',
			url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/CreateLabelFormSubmit',
			data: formdata,
			dataType: 'json'
		}).done(function (data) {
			$('#sc_modal_content').removeClass('sc_loading');
			if (data.result === 'UNCONFIGURED') {
				_loadUnconfiguredNote();
			} else if (data.result === 'OK') {
				_loadLabelList(data.orders_id);
			} else {
				if (data.error_message) {
					$('#sc_modal_content').html('<div class="sc_error">' + data.error_message + '</div>');
				}
			}
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	var _multiDropdownHandler = function _multiDropdownHandler(e) {
		var selected_orders = [],
		    orders_param = '';
		$('input[name="gm_multi_status[]"]:checked').each(function () {
			selected_orders.push($(this).val());
		});
		$('#sc_modal_content').empty().addClass('sc_loading');
		var shipcloud_modal_buttons = [];
		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function click() {
				$(this).dialog('close');
				$('#sc_get_quote').show();
			}
		});
		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('get_quotes', 'shipcloud'),
			'class': 'btn btn-primary',
			'click': _multiFormGetQuoteHandler,
			'id': 'sc_get_quote'
		});

		$('#shipcloud_modal').dialog({
			autoOpen: false,
			modal: true,
			'title': jse.core.lang.translate('create_labels', 'shipcloud'),
			'dialogClass': 'gx-container',
			buttons: shipcloud_modal_buttons,
			width: 1000,
			position: { my: 'center top', at: 'center bottom', of: '.main-top-header' }
		});

		$('#shipcloud_modal').dialog('open');
		selected_orders.forEach(function (item) {
			orders_param += 'orders[]=' + item + '&';
		});
		$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateMultiLabelForm&' + orders_param, _multiFormInit);
	};

	var _multiFormInit = function _multiFormInit() {
		$('#shipcloud_modal').dialog({
			'title': jse.core.lang.translate('create_labels', 'shipcloud')
		});
		$('#sc_modal_content').removeClass('sc_loading');
		$('#sc_multi_form').on('submit', function (e) {
			e.preventDefault();return false;
		});
		$('#sc_create_label').hide();
		$('#sc_show_labels').hide();
		$('#sc_modal_content input, #sc_modal_content select').on('change', function () {
			$('.sc_multi_quote').hide();
		});
		$('#sc_package_template').on('change', _templateSelectionHandler);
		$('input.create_label').on('click', _multiFormSubmitHandler);
	};

	var _multiFormSubmitHandler = function _multiFormSubmitHandler(event) {
		var formdata, carrier;
		carrier = $(this).attr('name');
		$('#sc_multi_form input[name="carrier"]').val(carrier);
		formdata = $('#sc_multi_form').serialize();
		$('#sc_modal_content').empty().addClass('sc_loading');
		$.ajax({
			type: 'POST',
			url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/CreateMultiLabelFormSubmit',
			data: formdata,
			dataType: 'json'
		}).done(function (data) {
			$('#sc_modal_content').removeClass('sc_loading');
			if (data.result === 'UNCONFIGURED') {
				_loadUnconfiguredNote();
			} else if (data.result === 'OK') {
				_loadMultiLabelList(data.orders_ids, data.shipments);
			} else {
				if (data.error_message) {
					$('#sc_modal_content').html('<div class="sc_error">' + data.error_message + '</div>');
				}
			}
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	var _loadMultiLabelList = function _loadMultiLabelList(orders_ids, shipments) {
		var multiLabelListParams = { 'orders_ids': orders_ids, 'shipments': shipments };

		$('#sc_modal_content').load(jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/LoadMultiLabelList', { 'json': JSON.stringify(multiLabelListParams) }, function () {
			gx.widgets.init($('#shipcloud_modal'));
			$('#shipcloud_modal').dialog({
				'title': jse.core.lang.translate('labellist', 'shipcloud')
			});
			$('#sc_modal_content').removeClass('sc_loading');
			$('#sc_get_quote').hide();

			$('form#sc_pickup').on('submit', function (e) {
				e.preventDefault();
			});
			jse.libs.button_dropdown.mapAction($('#sc-labellist-dropdown'), 'download_labels', 'shipcloud', _packedDownloadHandler);
			jse.libs.button_dropdown.mapAction($('#sc-labellist-dropdown'), 'order_pickups', 'shipcloud', _pickupSubmitHandler);
			$('input.pickup_checkbox').on('click', _labellistPickupCheckboxHandler);
			setTimeout(_labellistPickupCheckboxHandler, 200);
			$('input.pickup_checkbox_all').on('click', function () {
				if ($(this).prop('checked') === true) {
					$('input.pickup_checkbox').prop('checked', true);
					$('input.pickup_checkbox').parent().addClass('checked');
				} else {
					$('input.pickup_checkbox').prop('checked', false);
					$('input.pickup_checkbox').parent().removeClass('checked');
				}
				_labellistPickupCheckboxHandler();
			});
		});
	};

	var _multiPickupSubmitHandler = _pickupSubmitHandler;

	var _multiFormGetQuoteHandler = function _multiFormGetQuoteHandler() {
		var formdata;
		$('div.sc_quote').html('');
		formdata = $('#sc_multi_form').serialize();
		$.ajax({
			type: 'POST',
			url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/GetMultiShipmentQuote',
			data: formdata,
			dataType: 'json'
		}).done(function (data) {
			if (data.result === 'OK') {
				for (var squote in data.shipment_quotes) {
					$('#sc_multi_quote_' + data.shipment_quotes[squote].orders_id).html(data.shipment_quotes[squote].shipment_quote);
				}
				$('div.sc_multi_quote').show('fast');

				for (var carrier in data.carriers_total) {
					$('#sc_quote_' + carrier).html(data.carriers_total[carrier]);
				}
			}
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	module.init = function (done) {
		$('body').prepend($('<div id="shipcloud_modal" title="' + jse.core.lang.translate('create_label_window_title', 'shipcloud') + '" style="display: none;"><div id="sc_modal_content"></div></div>'));

		var interval_counter = 10,
		    interval = setInterval(function () {
			if ($('.js-button-dropdown').length) {
				clearInterval(interval);
				_addShipcloudDropdownEntry();
			}
			if (interval_counter-- === 0) {
				clearInterval(interval);
			}
		}, 400);

		jse.libs.button_dropdown.mapAction($('#orders-table-dropdown'), 'create_labels', 'shipcloud', _multiDropdownHandler);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfc2hpcGNsb3VkLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJtYXBwZXIiLCJqc2UiLCJsaWJzIiwiYWN0aW9uX21hcHBlciIsIl9zaW5nbGVGb3JtSW5pdCIsIndpZGdldHMiLCJpbml0IiwicmVtb3ZlQ2xhc3MiLCJzaG93IiwiaGlkZSIsIm9uIiwiZSIsInByZXZlbnREZWZhdWx0IiwiX3NpbmdsZUZvcm1TdWJtaXRIYW5kbGVyIiwidHJpZ2dlciIsIm5vdCIsInZhbCIsImh0bWwiLCJfdGVtcGxhdGVTZWxlY3Rpb25IYW5kbGVyIiwiYnV0dG9uIiwibGVuZ3RoIiwiJGZvcm0iLCIkdGVtcGxhdGUiLCJjbG9zZXN0IiwiX29wZW5TaW5nbGVGb3JtTW9kYWwiLCJldmVudCIsIm9yZGVySWQiLCJ0YXJnZXQiLCJwYXJlbnRzIiwiZmluZCIsImVtcHR5IiwiYWRkQ2xhc3MiLCJidXR0b25fY3JlYXRlX2xhYmVsIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJzaGlwY2xvdWRfbW9kYWxfYnV0dG9ucyIsInB1c2giLCJkaWFsb2ciLCJfc2hvd0xhYmVsc0hhbmRsZXIiLCJfc2luZ2xlRm9ybUdldFF1b3RlSGFuZGxlciIsImF1dG9PcGVuIiwibW9kYWwiLCJidXR0b25zIiwid2lkdGgiLCJwb3NpdGlvbiIsIm15IiwiYXQiLCJvZiIsImxvYWQiLCJfYWRkU2hpcGNsb3VkRHJvcGRvd25FbnRyeSIsImVhY2giLCJidXR0b25fZHJvcGRvd24iLCJtYXBBY3Rpb24iLCJfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyIiwicHJvcCIsIl9sb2FkTGFiZWxMaXN0Iiwib3JkZXJzX2lkIiwiX3BhY2tlZERvd25sb2FkSGFuZGxlciIsIl9waWNrdXBTdWJtaXRIYW5kbGVyIiwic2V0VGltZW91dCIsInBhcmVudCIsInNoaXBtZW50X2lkIiwiJGJ1dHRvblBsYWNlIiwiJHJvdyIsImFqYXgiLCJ0eXBlIiwidXJsIiwiY29uZmlnIiwiZ2V0IiwiZGF0YVR5cGUiLCJkb25lIiwicmVzdWx0IiwiZXJyb3JfbWVzc2FnZSIsInJlbW92ZSIsImZhaWwiLCJ1cmxzIiwicmVxdWVzdCIsImhyZWYiLCJhdHRyIiwicGFnZV90b2tlbiIsIkpTT04iLCJzdHJpbmdpZnkiLCJkb3dubG9hZGxpbmsiLCJkb3dubG9hZEtleSIsImZvcm1kYXRhIiwic2VyaWFsaXplIiwicmVzdWx0X21lc3NhZ2UiLCJyZXN1bHRfbWVzc2FnZXMiLCJmb3JFYWNoIiwibWVzc2FnZSIsImFsZXJ0IiwiX2xvYWRVbmNvbmZpZ3VyZWROb3RlIiwicXVvdGUiLCJjYXJyaWVyIiwiJGNyZWF0ZV9sYWJlbCIsInNoaXBtZW50X3F1b3RlIiwiX211bHRpRHJvcGRvd25IYW5kbGVyIiwic2VsZWN0ZWRfb3JkZXJzIiwib3JkZXJzX3BhcmFtIiwiX211bHRpRm9ybUdldFF1b3RlSGFuZGxlciIsIml0ZW0iLCJfbXVsdGlGb3JtSW5pdCIsIl9tdWx0aUZvcm1TdWJtaXRIYW5kbGVyIiwiX2xvYWRNdWx0aUxhYmVsTGlzdCIsIm9yZGVyc19pZHMiLCJzaGlwbWVudHMiLCJtdWx0aUxhYmVsTGlzdFBhcmFtcyIsIl9tdWx0aVBpY2t1cFN1Ym1pdEhhbmRsZXIiLCJzcXVvdGUiLCJzaGlwbWVudF9xdW90ZXMiLCJjYXJyaWVyc190b3RhbCIsInByZXBlbmQiLCJpbnRlcnZhbF9jb3VudGVyIiwiaW50ZXJ2YWwiLCJzZXRJbnRlcnZhbCIsImNsZWFySW50ZXJ2YWwiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGtCQURELEVBR0MsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHFCQURiLEVBRUNILEdBQUdHLE1BQUgsR0FBWSx1QkFGYixFQUdDLGlCQUhELENBSEQ7O0FBU0M7QUFDQSxVQUFVQyxJQUFWLEVBQWdCOztB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QkQ7O0FBdUJBLFFBQU81RSxNQUFQO0FBQ0EsQ0E5akJGIiwiZmlsZSI6Im9yZGVycy9vcmRlcnNfc2hpcGNsb3VkLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0b3JkZXJzX3NoaXBjbG91ZC5qcyAyMDE2LTAzLTAxXG5cdEdhbWJpbyBHbWJIXG5cdGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG5cdENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuXHRSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcblx0W2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG5cdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4qL1xuXG4vKipcbiAqICMjIE9yZGVycyBTaGlwY2xvdWQgTW9kdWxlXG4gKlxuICogVGhpcyBtb2R1bGUgaW1wbGVtZW50cyB0aGUgdXNlciBpbnRlcmZhY2UgZm9yIGNyZWF0aW5nIHNoaXBwaW5nIGxhYmVscyB2aWEgU2hpcGNsb3VkLmlvXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L29yZGVyc19zaGlwY2xvdWRcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdvcmRlcnNfc2hpcGNsb3VkJyxcblxuXHRbXG5cdFx0Z3guc291cmNlICsgJy9saWJzL2FjdGlvbl9tYXBwZXInLFxuXHRcdGd4LnNvdXJjZSArICcvbGlicy9idXR0b25fZHJvcGRvd24nLFxuXHRcdCdsb2FkaW5nX3NwaW5uZXInXG5cdF0sXG5cblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvb3JkZXJzX3NoaXBjbG91ZCAqL1xuXHRmdW5jdGlvbiAoZGF0YSkge1xuXG5cdFx0J3VzZSBzdHJpY3QnO1xuXG5cdFx0dmFyXG5cdFx0LyoqXG5cdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0ICpcblx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogVGhlIG1hcHBlciBsaWJyYXJ5XG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtYXBwZXIgPSBqc2UubGlicy5hY3Rpb25fbWFwcGVyLFxuXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblxuXHRcdHZhciBfc2luZ2xlRm9ybUluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRcdGd4LndpZGdldHMuaW5pdCgkKCcjc2hpcGNsb3VkX21vZGFsJykpO1xuXHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdFx0aWYgKCQoJyNzY19zaW5nbGVfY29udGFpbmVyJykuZGF0YSgnaXNfY29uZmlndXJlZCcpID09PSAxKVxuXHRcdFx0e1xuXHRcdFx0XHQkKCcjc2Nfc2hvd19sYWJlbHMnKS5zaG93KCk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlXG5cdFx0XHR7XG5cdFx0XHRcdCQoJyNzY19zaG93X2xhYmVscycpLmhpZGUoKTtcblx0XHRcdH1cblx0XHRcdCQoJyNzY19zaW5nbGVfZm9ybScpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihlKSB7IGUucHJldmVudERlZmF1bHQoKTsgfSk7XG5cdFx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0gaW5wdXQuY3JlYXRlX2xhYmVsJykub24oJ2NsaWNrJywgX3NpbmdsZUZvcm1TdWJtaXRIYW5kbGVyKTtcblx0XHRcdCQoJyNzY19zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cImNhcnJpZXJcIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0gaW5wdXRbdHlwZT1cInRleHRcIl0nKS50cmlnZ2VyKCdjaGFuZ2UnKTtcblx0XHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIC5jYXJyaWVyLXNwZWNpZmljJykubm90KCcuY2Fycmllci0nKyQodGhpcykudmFsKCkpLmhpZGUoJ2Zhc3QnKTtcblx0XHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIC5jYXJyaWVyLScrJCh0aGlzKS52YWwoKSkubm90KCc6dmlzaWJsZScpLnNob3coJ2Zhc3QnKTtcblx0XHRcdH0pO1xuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIC5wcmljZV92YWx1ZScpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIGRpdi5zY19xdW90ZScpLmh0bWwoJycpO1xuXHRcdFx0fSk7XG5cdFx0XHQkKCcjc2NfcGFja2FnZV90ZW1wbGF0ZScpLm9uKCdjaGFuZ2UnLCBfdGVtcGxhdGVTZWxlY3Rpb25IYW5kbGVyKTtcblx0XHRcdCQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dC50ZW1wbGF0ZV92YWx1ZScpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHsgJCgnI3NjX3BhY2thZ2VfdGVtcGxhdGUnKS52YWwoJy0xJyk7IH0pO1xuXHRcdFx0JCgnI3NjX2dldF9xdW90ZScpLmJ1dHRvbignZGlzYWJsZScpO1xuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIGlucHV0W25hbWU9XCJxdW90ZV9jYXJyaWVyc1tdXCJdJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRpZiAoJCgnI3NjX3NpbmdsZV9mb3JtIGlucHV0W25hbWU9XCJxdW90ZV9jYXJyaWVyc1tdXCJdOmNoZWNrZWQnKS5sZW5ndGggPiAwKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3NjX2dldF9xdW90ZScpLmJ1dHRvbignZW5hYmxlJyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3NjX2dldF9xdW90ZScpLmJ1dHRvbignZGlzYWJsZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdCQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dFtuYW1lPVwicXVvdGVfY2FycmllcnNbXVwiXTpmaXJzdCcpLnRyaWdnZXIoJ2NoYW5nZScpO1xuXHRcdH07XG5cblx0XHR2YXIgX3RlbXBsYXRlU2VsZWN0aW9uSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdHZhciAkZm9ybSwgJHRlbXBsYXRlO1xuXHRcdFx0JGZvcm0gICAgID0gJCh0aGlzKS5jbG9zZXN0KCdmb3JtJyk7XG5cdFx0XHQkdGVtcGxhdGUgPSAkKCdvcHRpb246c2VsZWN0ZWQnLCAkKHRoaXMpKTtcblx0XHRcdGlmICgkdGVtcGxhdGUudmFsKCkgIT09ICctMScpXG5cdFx0XHR7XG5cdFx0XHRcdCQoJ2lucHV0W25hbWU9XCJwYWNrYWdlW3dlaWdodF1cIl0nLCAkZm9ybSkudmFsKCR0ZW1wbGF0ZS5kYXRhKCd3ZWlnaHQnKSk7XG5cdFx0XHRcdCQoJ2lucHV0W25hbWU9XCJwYWNrYWdlW2hlaWdodF1cIl0nLCAkZm9ybSkudmFsKCR0ZW1wbGF0ZS5kYXRhKCdoZWlnaHQnKSk7XG5cdFx0XHRcdCQoJ2lucHV0W25hbWU9XCJwYWNrYWdlW3dpZHRoXVwiXScsICAkZm9ybSkudmFsKCR0ZW1wbGF0ZS5kYXRhKCd3aWR0aCcpKTtcblx0XHRcdFx0JCgnaW5wdXRbbmFtZT1cInBhY2thZ2VbbGVuZ3RoXVwiXScsICRmb3JtKS52YWwoJHRlbXBsYXRlLmRhdGEoJ2xlbmd0aCcpKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0dmFyIF9vcGVuU2luZ2xlRm9ybU1vZGFsID0gZnVuY3Rpb24oZXZlbnQpXG5cdFx0e1xuXHRcdFx0dmFyIG9yZGVySWQgPSAkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5kYXRhKCdyb3ctaWQnKSB8fCAkKCdib2R5JykuZmluZCgnI2dtX29yZGVyX2lkJykudmFsKCk7XG5cdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblx0XHRcdHZhciBidXR0b25fY3JlYXRlX2xhYmVsID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2NyZWF0ZV9sYWJlbCcsICdzaGlwY2xvdWQnKSxcblx0XHRcdFx0c2hpcGNsb3VkX21vZGFsX2J1dHRvbnMgPSBbXTtcblxuXHRcdFx0c2hpcGNsb3VkX21vZGFsX2J1dHRvbnMucHVzaCh7XG5cdFx0XHRcdCd0ZXh0JzogIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHRcdCQoJyNzY19nZXRfcXVvdGUnKS5zaG93KCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdHNoaXBjbG91ZF9tb2RhbF9idXR0b25zLnB1c2goe1xuXHRcdFx0XHQndGV4dCc6ICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc2hvd19leGlzdGluZ19sYWJlbHMnLCAnc2hpcGNsb3VkJyksXG5cdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHQnY2xpY2snOiBfc2hvd0xhYmVsc0hhbmRsZXIsXG5cdFx0XHRcdCdpZCc6ICAgICdzY19zaG93X2xhYmVscydcblx0XHRcdH0pO1xuXHRcdFx0c2hpcGNsb3VkX21vZGFsX2J1dHRvbnMucHVzaCh7XG5cdFx0XHRcdCd0ZXh0JzogIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdnZXRfcXVvdGVzJywgJ3NoaXBjbG91ZCcpLFxuXHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0J2NsaWNrJzogX3NpbmdsZUZvcm1HZXRRdW90ZUhhbmRsZXIsXG5cdFx0XHRcdCdpZCc6ICAgICdzY19nZXRfcXVvdGUnXG5cdFx0XHR9KTtcblxuXHRcdFx0JCgnI3NoaXBjbG91ZF9tb2RhbCcpLmRpYWxvZyh7XG5cdFx0XHRcdGF1dG9PcGVuOiAgICAgIGZhbHNlLFxuXHRcdFx0XHRtb2RhbDogICAgICAgICB0cnVlLFxuXHRcdFx0XHQndGl0bGUnOiAgICAgICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY3JlYXRlX2xhYmVsJywgJ3NoaXBjbG91ZCcpLFxuXHRcdFx0XHQnZGlhbG9nQ2xhc3MnOiAnZ3gtY29udGFpbmVyJyxcblx0XHRcdFx0YnV0dG9uczogICAgICAgc2hpcGNsb3VkX21vZGFsX2J1dHRvbnMsXG5cdFx0XHRcdHdpZHRoOiAgICAgICAgIDEwMDAsXG5cdFx0XHRcdHBvc2l0aW9uOiAgICAgIHsgbXk6ICdjZW50ZXIgdG9wJywgYXQ6ICdjZW50ZXIgYm90dG9tJywgb2Y6ICcubWFpbi10b3AtaGVhZGVyJyB9XG5cdFx0XHR9KTtcblx0XHRcdCQoJyNzaGlwY2xvdWRfbW9kYWwnKS5kaWFsb2coJ29wZW4nKTtcblx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9DcmVhdGVMYWJlbEZvcm0mb3JkZXJzX2lkPScgKyBvcmRlcklkLFxuXHRcdFx0XHRfc2luZ2xlRm9ybUluaXQpO1xuXHRcdH07XG5cblx0XHR2YXIgX2FkZFNoaXBjbG91ZERyb3Bkb3duRW50cnkgPSBmdW5jdGlvbiAoKSB7XG5cdFx0XHQkKCcuZ3gtb3JkZXJzLXRhYmxlIHRyJykubm90KCcuZGF0YVRhYmxlSGVhZGluZ1JvdycpLmVhY2goZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCQodGhpcyksICdhZG1pbl9tZW51X2VudHJ5JywgJ3NoaXBjbG91ZCcsIF9vcGVuU2luZ2xlRm9ybU1vZGFsKTtcblx0XHRcdH0pO1xuXHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbigkKCcub3JkZXItZm9vdGVyJyksICdhZG1pbl9tZW51X2VudHJ5JywgJ3NoaXBjbG91ZCcsIFxuXHRcdFx0XHRfb3BlblNpbmdsZUZvcm1Nb2RhbCk7XG5cdFx0fTtcblxuXHRcdHZhciBfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcjc2MtbGFiZWxsaXN0LWRyb3Bkb3duIGJ1dHRvbiwgZGl2LnBpY2t1cF90aW1lIGlucHV0Jylcblx0XHRcdFx0LnByb3AoJ2Rpc2FibGVkJywgJCgnaW5wdXQucGlja3VwX2NoZWNrYm94OmNoZWNrZWQnKS5sZW5ndGggPT09IDApO1xuXHRcdH07XG5cblx0XHR2YXIgX2xvYWRMYWJlbExpc3QgPSBmdW5jdGlvbiAob3JkZXJzX2lkKVxuXHRcdHtcblx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9Mb2FkTGFiZWxMaXN0Jm9yZGVyc19pZD0nICsgb3JkZXJzX2lkLFxuXHRcdFx0XHRmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdFx0Z3gud2lkZ2V0cy5pbml0KCQoJyNzY19tb2RhbF9jb250ZW50JykpO1xuXHRcdFx0XHRcdCQoJyNzaGlwY2xvdWRfbW9kYWwnKS5kaWFsb2coe1xuXHRcdFx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2xhYmVsbGlzdF9mb3InLCAnc2hpcGNsb3VkJykgKyAnICcgKyBvcmRlcnNfaWRcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLnJlbW92ZUNsYXNzKCdzY19sb2FkaW5nJyk7XG5cblx0XHRcdFx0XHQkKCdmb3JtI3NjX3BpY2t1cCcpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihlKSB7IGUucHJldmVudERlZmF1bHQoKTsgfSk7XG5cdFx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbihcblx0XHRcdFx0XHRcdCQoJyNzYy1sYWJlbGxpc3QtZHJvcGRvd24nKSxcblx0XHRcdFx0XHRcdCdkb3dubG9hZF9sYWJlbHMnLFxuXHRcdFx0XHRcdFx0J3NoaXBjbG91ZCcsXG5cdFx0XHRcdFx0XHRfcGFja2VkRG93bmxvYWRIYW5kbGVyXG5cdFx0XHRcdFx0KTtcblx0XHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKFxuXHRcdFx0XHRcdFx0JCgnI3NjLWxhYmVsbGlzdC1kcm9wZG93bicpLFxuXHRcdFx0XHRcdFx0J29yZGVyX3BpY2t1cHMnLFxuXHRcdFx0XHRcdFx0J3NoaXBjbG91ZCcsXG5cdFx0XHRcdFx0XHRfcGlja3VwU3VibWl0SGFuZGxlclxuXHRcdFx0XHRcdCk7XG5cdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94Jykub24oJ2NsaWNrJywgX2xhYmVsbGlzdFBpY2t1cENoZWNrYm94SGFuZGxlcik7XG5cdFx0XHRcdFx0c2V0VGltZW91dChfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyLCAyMDApO1xuXHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveF9hbGwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0aWYgKCQodGhpcykucHJvcCgnY2hlY2tlZCcpID09PSB0cnVlKVxuXHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHQkKCdpbnB1dC5waWNrdXBfY2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLnBhcmVudCgpLmFkZENsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRlbHNlXG5cdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyKCk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0JCgnYS5zYy1kZWwtbGFiZWwnKS5vbignY2xpY2snLCBmdW5jdGlvbihlKSB7XG5cdFx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdFx0XHR2YXIgc2hpcG1lbnRfaWQgID0gJCh0aGlzKS5kYXRhKCdzaGlwbWVudC1pZCcpLFxuXHRcdFx0XHRcdFx0ICAgICRidXR0b25QbGFjZSA9ICQodGhpcykuY2xvc2VzdCgnc3Bhbi5zYy1kZWwtbGFiZWwnKSxcblx0XHRcdFx0XHRcdCAgICAkcm93ICAgICAgICAgPSAkKHRoaXMpLmNsb3Nlc3QoJ3RyJyk7XG5cdFx0XHRcdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdFx0XHR0eXBlOiAgICAgJ1BPU1QnLFxuXHRcdFx0XHRcdFx0XHR1cmw6ICAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89U2hpcGNsb3VkL0RlbGV0ZVNoaXBtZW50Jyxcblx0XHRcdFx0XHRcdFx0ZGF0YTogICAgIHsgc2hpcG1lbnRfaWQ6IHNoaXBtZW50X2lkIH0sXG5cdFx0XHRcdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHRcdFx0XHRcdGlmKGRhdGEucmVzdWx0ID09PSAnRVJST1InKVxuXHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0JGJ1dHRvblBsYWNlLmh0bWwoZGF0YS5lcnJvcl9tZXNzYWdlKTtcblx0XHRcdFx0XHRcdFx0XHQkYnV0dG9uUGxhY2UuYWRkQ2xhc3MoJ2JhZGdlJykuYWRkQ2xhc3MoJ2JhZGdlLWRhbmdlcicpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdGVsc2Vcblx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdCRidXR0b25QbGFjZS5odG1sKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzaGlwbWVudF9kZWxldGVkJywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdFx0XHRcdFx0XHQkKCdhLCBpbnB1dCwgdGQuY2hlY2tib3ggPiAqJywgJHJvdykucmVtb3ZlKCk7XG5cdFx0XHRcdFx0XHRcdFx0JHJvdy5hZGRDbGFzcygnZGVsZXRlZC1zaGlwbWVudCcpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0LmZhaWwoZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0XHRcdFx0XHQkYnV0dG9uUGxhY2UuaHRtbChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc3VibWl0X2Vycm9yJywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0dmFyIF9wYWNrZWREb3dubG9hZEhhbmRsZXIgPSBmdW5jdGlvbihlKVxuXHRcdHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdHZhciB1cmxzID0gW10sIHJlcXVlc3QgPSB7fTtcblx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveDpjaGVja2VkJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyIGhyZWYgPSAkKCdhLmxhYmVsLWxpbmsnLCAkKHRoaXMpLmNsb3Nlc3QoJ3RyJykpLmF0dHIoJ2hyZWYnKTtcblx0XHRcdFx0dXJscy5wdXNoKGhyZWYpO1xuXHRcdFx0fSk7XG5cdFx0XHRpZiAodXJscylcblx0XHRcdHtcblx0XHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLnNob3coKTtcblx0XHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLmh0bWwoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2xvYWRpbmcnLCAnc2hpcGNsb3VkJykpO1xuXHRcdFx0XHRyZXF1ZXN0LnVybHMgICAgICAgPSB1cmxzO1xuXHRcdFx0XHRyZXF1ZXN0LnBhZ2VfdG9rZW4gPSAkKCcjc2NfbW9kYWxfY29udGVudCBpbnB1dFtuYW1lPVwicGFnZV90b2tlblwiXScpLnZhbCgpO1xuXG5cdFx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdFx0dHlwZTogICAgICdQT1NUJyxcblx0XHRcdFx0XHR1cmw6ICAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89UGFja2VkRG93bmxvYWQvRG93bmxvYWRCeUpzb24nLFxuXHRcdFx0XHRcdGRhdGE6ICAgICBKU09OLnN0cmluZ2lmeShyZXF1ZXN0KSxcblx0XHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHRcdH0pXG5cdFx0XHRcdC5kb25lKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0XHR2YXIgZG93bmxvYWRsaW5rID1cblx0XHRcdFx0XHRcdCAganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgK1xuXHRcdFx0XHRcdFx0ICAnL2FkbWluL2FkbWluLnBocD9kbz1QYWNrZWREb3dubG9hZC9Eb3dubG9hZFBhY2thZ2Uma2V5PScgK1xuXHRcdFx0XHRcdFx0ICBkYXRhLmRvd25sb2FkS2V5O1xuXHRcdFx0XHRcdGlmIChkYXRhLnJlc3VsdCA9PT0gJ09LJylcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQkKCcjZG93bmxvYWRfcmVzdWx0JykuaHRtbCgnPGlmcmFtZSBjbGFzcz1cImRvd25sb2FkX2lmcmFtZVwiIHNyYz1cIicrZG93bmxvYWRsaW5rKydcIj48L2lmcmFtZT4nKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0aWYgKGRhdGEucmVzdWx0ID09PSAnRVJST1InKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJyNkb3dubG9hZF9yZXN1bHQnKS5odG1sKGRhdGEuZXJyb3JfbWVzc2FnZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KVxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLmh0bWwoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0fTtcblxuXHRcdHZhciBfcGlja3VwU3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGlmICgkKCdpbnB1dC5waWNrdXBfY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aCA+IDApXG5cdFx0XHR7XG5cdFx0XHRcdHZhciBmb3JtZGF0YSA9ICQoJ2Zvcm0jc2NfcGlja3VwJykuc2VyaWFsaXplKCk7XG5cdFx0XHRcdCQoJyNwaWNrdXBfcmVzdWx0JykuaHRtbChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc2VuZGluZ19waWNrdXBfcmVxdWVzdCcsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHRcdCQoJyNwaWNrdXBfcmVzdWx0Jykuc2hvdygpO1xuXHRcdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdHR5cGU6ICAgICAnUE9TVCcsXG5cdFx0XHRcdFx0dXJsOiAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89U2hpcGNsb3VkL1BpY2t1cFNoaXBtZW50cycsXG5cdFx0XHRcdFx0ZGF0YTogICAgIGZvcm1kYXRhLFxuXHRcdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdFx0fSlcblx0XHRcdFx0LmRvbmUoZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0XHRcdHZhciByZXN1bHRfbWVzc2FnZSA9ICcnO1xuXHRcdFx0XHRcdGRhdGEucmVzdWx0X21lc3NhZ2VzLmZvckVhY2goZnVuY3Rpb24obWVzc2FnZSkgeyBcblx0XHRcdFx0XHRcdHJlc3VsdF9tZXNzYWdlID0gcmVzdWx0X21lc3NhZ2UgKyBtZXNzYWdlICsgJzxicj4nOyBcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHQkKCcjcGlja3VwX3Jlc3VsdCcpLmh0bWwocmVzdWx0X21lc3NhZ2UpO1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0fTtcblxuXHRcdHZhciBfbG9hZFVuY29uZmlndXJlZE5vdGUgPSBmdW5jdGlvbiAoKVxuXHRcdHtcblx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9VbmNvbmZpZ3VyZWROb3RlJyk7XG5cdFx0fTtcblxuXHRcdHZhciBfc2hvd0xhYmVsc0hhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuXHRcdFx0dmFyIG9yZGVyc19pZCA9ICQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dFtuYW1lPVwib3JkZXJzX2lkXCJdJykudmFsKCk7XG5cdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblx0XHRcdF9sb2FkTGFiZWxMaXN0KG9yZGVyc19pZCk7XG5cdFx0XHQkKCcjc2Nfc2hvd19sYWJlbHMnKS5oaWRlKCk7XG5cdFx0XHQkKCcjc2NfZ2V0X3F1b3RlJykuaGlkZSgpO1xuXHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdH07XG5cblx0XHR2YXIgX3NpbmdsZUZvcm1HZXRRdW90ZUhhbmRsZXIgPSBmdW5jdGlvbigpXG5cdFx0e1xuXHRcdFx0dmFyICRmb3JtID0gJCgnI3NjX3NpbmdsZV9mb3JtJyksXG5cdFx0XHQgICAgcXVvdGUgPSAnJztcblxuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIC5zY19xdW90ZScpLmh0bWwoJycpO1xuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIC5zY19xdW90ZScpLmF0dHIoJ3RpdGxlJywgJycpO1xuXG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwicXVvdGVfY2FycmllcnNbXVwiXTpjaGVja2VkJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0dmFyIGNhcnJpZXIgPSAkKHRoaXMpLnZhbCgpLFxuXHRcdFx0XHQgICAgJGNyZWF0ZV9sYWJlbCA9ICQoJ2lucHV0LmNyZWF0ZV9sYWJlbCcsICQodGhpcykuY2xvc2VzdCgndHInKSk7XG5cdFx0XHRcdCQoJ2lucHV0W25hbWU9XCJjYXJyaWVyXCJdJywgJGZvcm0pLnZhbChjYXJyaWVyKTtcblx0XHRcdFx0JCgnI3NjX3F1b3RlXycrY2FycmllcikuaHRtbChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbG9hZGluZycsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdFx0dHlwZTogICAgICdQT1NUJyxcblx0XHRcdFx0XHR1cmw6ICAgICAganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2FkbWluLnBocD9kbz1TaGlwY2xvdWQvR2V0U2hpcG1lbnRRdW90ZScsXG5cdFx0XHRcdFx0ZGF0YTogICAgICRmb3JtLnNlcmlhbGl6ZSgpLFxuXHRcdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdFx0fSlcblx0XHRcdFx0LmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0XHRpZiAoZGF0YS5yZXN1bHQgPT09ICdPSycpXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0cXVvdGUgPSBkYXRhLnNoaXBtZW50X3F1b3RlO1xuXHRcdFx0XHRcdFx0JCgnI3NjX3F1b3RlXycrY2FycmllcikuaHRtbChxdW90ZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGVsc2UgaWYgKGRhdGEucmVzdWx0ID09PSAnRVJST1InKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJyNzY19xdW90ZV8nK2NhcnJpZXIpLmh0bWwoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ25vdF9wb3NzaWJsZScsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHRcdFx0XHQkKCcjc2NfcXVvdGVfJytjYXJyaWVyKS5hdHRyKCd0aXRsZScsIGRhdGEuZXJyb3JfbWVzc2FnZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdGVsc2UgaWYgKGRhdGEucmVzdWx0ID09PSAnVU5DT05GSUdVUkVEJylcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRfbG9hZFVuY29uZmlndXJlZE5vdGUoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pXG5cdFx0XHRcdC5mYWlsKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHRcdFx0cXVvdGUgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZ2V0X3F1b3RlX2Vycm9yJywgJ3NoaXBjbG91ZCcpO1xuXHRcdFx0XHRcdCQoJyNzY19xdW90ZV8nK2NhcnJpZXIpLmh0bWwocXVvdGUpO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwiY2FycmllclwiXScsICRmb3JtKS52YWwoJycpO1xuXHRcdH07XG5cblx0XHR2YXIgX3NpbmdsZUZvcm1TdWJtaXRIYW5kbGVyID0gZnVuY3Rpb24gKGUpIHtcblx0XHRcdHZhciBjYXJyaWVyLCBmb3JtZGF0YTtcblx0XHRcdCQoJyNzY19zaG93X2xhYmVscycpLmhpZGUoKTtcblx0XHRcdCQoJyNzY19nZXRfcXVvdGUnKS5oaWRlKCk7XG5cdFx0XHRjYXJyaWVyID0gJCh0aGlzKS5hdHRyKCduYW1lJyk7XG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwiY2FycmllclwiXScpLnZhbChjYXJyaWVyKTtcblx0XHRcdGZvcm1kYXRhID0gJCgnI3NjX3NpbmdsZV9mb3JtJykuc2VyaWFsaXplKCk7XG5cdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblx0XHRcdC8vIGFsZXJ0KCdkYXRhOiAnK2Zvcm1kYXRhKTtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdHR5cGU6ICAgICAnUE9TVCcsXG5cdFx0XHRcdHVybDogICAgICBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9DcmVhdGVMYWJlbEZvcm1TdWJtaXQnLFxuXHRcdFx0XHRkYXRhOiAgICAgZm9ybWRhdGEsXG5cdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLnJlbW92ZUNsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0XHRcdGlmIChkYXRhLnJlc3VsdCA9PT0gJ1VOQ09ORklHVVJFRCcpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRfbG9hZFVuY29uZmlndXJlZE5vdGUoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlIGlmIChkYXRhLnJlc3VsdCA9PT0gJ09LJylcblx0XHRcdFx0e1xuXHRcdFx0XHRcdF9sb2FkTGFiZWxMaXN0KGRhdGEub3JkZXJzX2lkKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRlbHNlXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRpZiAoZGF0YS5lcnJvcl9tZXNzYWdlKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykuaHRtbCgnPGRpdiBjbGFzcz1cInNjX2Vycm9yXCI+JytkYXRhLmVycm9yX21lc3NhZ2UrJzwvZGl2PicpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fVxuXHRcdFx0fSlcblx0XHRcdC5mYWlsKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHRcdGFsZXJ0KGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzdWJtaXRfZXJyb3InLCAnc2hpcGNsb3VkJykpO1xuXHRcdFx0fSk7XG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblxuXHRcdHZhciBfbXVsdGlEcm9wZG93bkhhbmRsZXIgPSBmdW5jdGlvbihlKVxuXHRcdHtcblx0XHRcdHZhciBzZWxlY3RlZF9vcmRlcnMgPSBbXSwgb3JkZXJzX3BhcmFtID0gJyc7XG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwiZ21fbXVsdGlfc3RhdHVzW11cIl06Y2hlY2tlZCcpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHNlbGVjdGVkX29yZGVycy5wdXNoKCQodGhpcykudmFsKCkpO1xuXHRcdFx0fSk7XG5cdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLmVtcHR5KCkuYWRkQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblx0XHRcdHZhciBzaGlwY2xvdWRfbW9kYWxfYnV0dG9ucyA9IFtdO1xuXHRcdFx0c2hpcGNsb3VkX21vZGFsX2J1dHRvbnMucHVzaCh7XG5cdFx0XHRcdCd0ZXh0JzogIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHQnY2xpY2snOiBmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0JCgnI3NjX2dldF9xdW90ZScpLnNob3coKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRzaGlwY2xvdWRfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdFx0J3RleHQnOiAganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2dldF9xdW90ZXMnLCAnc2hpcGNsb3VkJyksXG5cdFx0XHRcdCdjbGFzcyc6ICdidG4gYnRuLXByaW1hcnknLFxuXHRcdFx0XHQnY2xpY2snOiBfbXVsdGlGb3JtR2V0UXVvdGVIYW5kbGVyLFxuXHRcdFx0XHQnaWQnOiAgICAnc2NfZ2V0X3F1b3RlJ1xuXHRcdFx0fSk7XG5cblx0XHRcdCQoJyNzaGlwY2xvdWRfbW9kYWwnKS5kaWFsb2coe1xuXHRcdFx0XHRhdXRvT3BlbjogICAgICBmYWxzZSxcblx0XHRcdFx0bW9kYWw6ICAgICAgICAgdHJ1ZSxcblx0XHRcdFx0J3RpdGxlJzogICAgICAganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2NyZWF0ZV9sYWJlbHMnLCAnc2hpcGNsb3VkJyksXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHRidXR0b25zOiAgICAgICBzaGlwY2xvdWRfbW9kYWxfYnV0dG9ucyxcblx0XHRcdFx0d2lkdGg6ICAgICAgICAgMTAwMCxcblx0XHRcdFx0cG9zaXRpb246ICAgICAgeyBteTogJ2NlbnRlciB0b3AnLCBhdDogJ2NlbnRlciBib3R0b20nLCBvZjogJy5tYWluLXRvcC1oZWFkZXInIH1cblx0XHRcdH0pO1xuXG5cdFx0XHQkKCcjc2hpcGNsb3VkX21vZGFsJykuZGlhbG9nKCdvcGVuJyk7XG5cdFx0XHRzZWxlY3RlZF9vcmRlcnMuZm9yRWFjaChmdW5jdGlvbihpdGVtKSB7XG5cdFx0XHRcdG9yZGVyc19wYXJhbSArPSAnb3JkZXJzW109JytpdGVtKycmJztcblx0XHRcdH0pO1xuXHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5sb2FkKCdhZG1pbi5waHA/ZG89U2hpcGNsb3VkL0NyZWF0ZU11bHRpTGFiZWxGb3JtJicrb3JkZXJzX3BhcmFtLCBfbXVsdGlGb3JtSW5pdCk7XG5cdFx0fTtcblxuXHRcdHZhciBfbXVsdGlGb3JtSW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnI3NoaXBjbG91ZF9tb2RhbCcpLmRpYWxvZyh7XG5cdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfbGFiZWxzJywgJ3NoaXBjbG91ZCcpXG5cdFx0XHR9KTtcblx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykucmVtb3ZlQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblx0XHRcdCQoJyNzY19tdWx0aV9mb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGUpIHsgZS5wcmV2ZW50RGVmYXVsdCgpOyByZXR1cm4gZmFsc2U7IH0pO1xuXHRcdFx0JCgnI3NjX2NyZWF0ZV9sYWJlbCcpLmhpZGUoKTtcblx0XHRcdCQoJyNzY19zaG93X2xhYmVscycpLmhpZGUoKTtcblx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50IGlucHV0LCAjc2NfbW9kYWxfY29udGVudCBzZWxlY3QnKS5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQoJy5zY19tdWx0aV9xdW90ZScpLmhpZGUoKTtcblx0XHRcdH0pO1xuXHRcdFx0JCgnI3NjX3BhY2thZ2VfdGVtcGxhdGUnKS5vbignY2hhbmdlJywgX3RlbXBsYXRlU2VsZWN0aW9uSGFuZGxlcik7XG5cdFx0XHQkKCdpbnB1dC5jcmVhdGVfbGFiZWwnKS5vbignY2xpY2snLCBfbXVsdGlGb3JtU3VibWl0SGFuZGxlcik7XG5cdFx0fTtcblxuXHRcdHZhciBfbXVsdGlGb3JtU3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHR2YXIgZm9ybWRhdGEsIGNhcnJpZXI7XG5cdFx0XHRjYXJyaWVyID0gJCh0aGlzKS5hdHRyKCduYW1lJyk7XG5cdFx0XHQkKCcjc2NfbXVsdGlfZm9ybSBpbnB1dFtuYW1lPVwiY2FycmllclwiXScpLnZhbChjYXJyaWVyKTtcblx0XHRcdGZvcm1kYXRhID0gJCgnI3NjX211bHRpX2Zvcm0nKS5zZXJpYWxpemUoKTtcblx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykuZW1wdHkoKS5hZGRDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdFx0JC5hamF4KHtcblx0XHRcdFx0dHlwZTogICAgICdQT1NUJyxcblx0XHRcdFx0dXJsOiAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89U2hpcGNsb3VkL0NyZWF0ZU11bHRpTGFiZWxGb3JtU3VibWl0Jyxcblx0XHRcdFx0ZGF0YTogICAgIGZvcm1kYXRhLFxuXHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHR9KVxuXHRcdFx0LmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdFx0XHRpZiAoZGF0YS5yZXN1bHQgPT09ICdVTkNPTkZJR1VSRUQnKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0X2xvYWRVbmNvbmZpZ3VyZWROb3RlKCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZSBpZiAoZGF0YS5yZXN1bHQgPT09ICdPSycpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRfbG9hZE11bHRpTGFiZWxMaXN0KGRhdGEub3JkZXJzX2lkcywgZGF0YS5zaGlwbWVudHMpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2Vcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGlmIChkYXRhLmVycm9yX21lc3NhZ2UpXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5odG1sKCc8ZGl2IGNsYXNzPVwic2NfZXJyb3JcIj4nK2RhdGEuZXJyb3JfbWVzc2FnZSsnPC9kaXY+Jyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9KVxuXHRcdFx0LmZhaWwoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHR9KTtcblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9O1xuXG5cdFx0dmFyIF9sb2FkTXVsdGlMYWJlbExpc3QgPSBmdW5jdGlvbihvcmRlcnNfaWRzLCBzaGlwbWVudHMpXG5cdFx0e1xuXHRcdFx0dmFyIG11bHRpTGFiZWxMaXN0UGFyYW1zID0geyAnb3JkZXJzX2lkcyc6IG9yZGVyc19pZHMsICdzaGlwbWVudHMnOiBzaGlwbWVudHMgfTtcblxuXHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5sb2FkKFxuXHRcdFx0XHRqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9Mb2FkTXVsdGlMYWJlbExpc3QnLFxuXHRcdFx0XHR7ICdqc29uJzogSlNPTi5zdHJpbmdpZnkobXVsdGlMYWJlbExpc3RQYXJhbXMpIH0sXG5cdFx0XHRcdGZ1bmN0aW9uICgpIHtcblx0XHRcdFx0XHRneC53aWRnZXRzLmluaXQoJCgnI3NoaXBjbG91ZF9tb2RhbCcpKTtcblx0XHRcdFx0XHQkKCcjc2hpcGNsb3VkX21vZGFsJykuZGlhbG9nKHtcblx0XHRcdFx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdsYWJlbGxpc3QnLCAnc2hpcGNsb3VkJylcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLnJlbW92ZUNsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0XHRcdFx0JCgnI3NjX2dldF9xdW90ZScpLmhpZGUoKTtcblxuXHRcdFx0XHRcdCQoJ2Zvcm0jc2NfcGlja3VwJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGUpIHsgZS5wcmV2ZW50RGVmYXVsdCgpOyB9KTtcblx0XHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKFxuXHRcdFx0XHRcdFx0JCgnI3NjLWxhYmVsbGlzdC1kcm9wZG93bicpLFxuXHRcdFx0XHRcdFx0J2Rvd25sb2FkX2xhYmVscycsXG5cdFx0XHRcdFx0XHQnc2hpcGNsb3VkJyxcblx0XHRcdFx0XHRcdF9wYWNrZWREb3dubG9hZEhhbmRsZXJcblx0XHRcdFx0XHQpO1xuXHRcdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oXG5cdFx0XHRcdFx0XHQkKCcjc2MtbGFiZWxsaXN0LWRyb3Bkb3duJyksXG5cdFx0XHRcdFx0XHQnb3JkZXJfcGlja3VwcycsXG5cdFx0XHRcdFx0XHQnc2hpcGNsb3VkJyxcblx0XHRcdFx0XHRcdF9waWNrdXBTdWJtaXRIYW5kbGVyXG5cdFx0XHRcdFx0KTtcblx0XHRcdFx0XHQkKCdpbnB1dC5waWNrdXBfY2hlY2tib3gnKS5vbignY2xpY2snLCBfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyKTtcblx0XHRcdFx0XHRzZXRUaW1lb3V0KF9sYWJlbGxpc3RQaWNrdXBDaGVja2JveEhhbmRsZXIsIDIwMCk7XG5cdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94X2FsbCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uKClcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRpZiAoJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPT09IHRydWUpXG5cdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCB0cnVlKTtcblx0XHRcdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94JykucGFyZW50KCkuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdGVsc2Vcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94JykucGFyZW50KCkucmVtb3ZlQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdF9sYWJlbGxpc3RQaWNrdXBDaGVja2JveEhhbmRsZXIoKTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0KTtcblx0XHR9O1xuXG5cdFx0dmFyIF9tdWx0aVBpY2t1cFN1Ym1pdEhhbmRsZXIgPSBfcGlja3VwU3VibWl0SGFuZGxlcjtcblxuXHRcdHZhciBfbXVsdGlGb3JtR2V0UXVvdGVIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgZm9ybWRhdGE7XG5cdFx0XHQkKCdkaXYuc2NfcXVvdGUnKS5odG1sKCcnKTtcblx0XHRcdGZvcm1kYXRhID0gJCgnI3NjX211bHRpX2Zvcm0nKS5zZXJpYWxpemUoKTtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdHR5cGU6ICAgICAnUE9TVCcsXG5cdFx0XHRcdHVybDogICAgICBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9HZXRNdWx0aVNoaXBtZW50UXVvdGUnLFxuXHRcdFx0XHRkYXRhOiAgICAgZm9ybWRhdGEsXG5cdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRpZiAoZGF0YS5yZXN1bHQgPT09ICdPSycpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRmb3IodmFyIHNxdW90ZSBpbiBkYXRhLnNoaXBtZW50X3F1b3Rlcykge1xuXHRcdFx0XHRcdFx0JCgnI3NjX211bHRpX3F1b3RlXycgKyBkYXRhLnNoaXBtZW50X3F1b3Rlc1tzcXVvdGVdLm9yZGVyc19pZClcblx0XHRcdFx0XHRcdFx0Lmh0bWwoZGF0YS5zaGlwbWVudF9xdW90ZXNbc3F1b3RlXS5zaGlwbWVudF9xdW90ZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdCQoJ2Rpdi5zY19tdWx0aV9xdW90ZScpLnNob3coJ2Zhc3QnKTtcblxuXHRcdFx0XHRcdGZvcih2YXIgY2FycmllciBpbiBkYXRhLmNhcnJpZXJzX3RvdGFsKVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJyNzY19xdW90ZV8nK2NhcnJpZXIpLmh0bWwoZGF0YS5jYXJyaWVyc190b3RhbFtjYXJyaWVyXSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9KVxuXHRcdFx0LmZhaWwoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHR9KTtcblx0XHRcdHJldHVybiBmYWxzZTtcblx0XHR9O1xuXG5cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cdFx0XHQkKCdib2R5JykucHJlcGVuZCgkKCc8ZGl2IGlkPVwic2hpcGNsb3VkX21vZGFsXCIgdGl0bGU9XCInICsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoXG5cdFx0XHRcdFx0J2NyZWF0ZV9sYWJlbF93aW5kb3dfdGl0bGUnLCAnc2hpcGNsb3VkJykgK1xuXHRcdFx0XHQnXCIgc3R5bGU9XCJkaXNwbGF5OiBub25lO1wiPjxkaXYgaWQ9XCJzY19tb2RhbF9jb250ZW50XCI+PC9kaXY+PC9kaXY+JykpO1xuXG5cdFx0XHR2YXIgaW50ZXJ2YWxfY291bnRlciA9IDEwLFxuXHRcdFx0ICAgIGludGVydmFsID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRpZiAoJCgnLmpzLWJ1dHRvbi1kcm9wZG93bicpLmxlbmd0aCkge1xuXHRcdFx0XHRcdGNsZWFySW50ZXJ2YWwoaW50ZXJ2YWwpO1xuXHRcdFx0XHRcdF9hZGRTaGlwY2xvdWREcm9wZG93bkVudHJ5KCk7XG5cdFx0XHRcdH1cblx0XHRcdFx0aWYgKGludGVydmFsX2NvdW50ZXItLSA9PT0gMClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGNsZWFySW50ZXJ2YWwoaW50ZXJ2YWwpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LCA0MDApO1xuXG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCQoJyNvcmRlcnMtdGFibGUtZHJvcGRvd24nKSwgJ2NyZWF0ZV9sYWJlbHMnLCAnc2hpcGNsb3VkJywgXG5cdFx0XHRcdF9tdWx0aURyb3Bkb3duSGFuZGxlcik7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fVxuKTtcblxuIl19
