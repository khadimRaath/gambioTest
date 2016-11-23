'use strict';

/* --------------------------------------------------------------
	shipcloud.js 2016-09-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function () {
	'use strict';

	var _openSingleFormModal = function _openSingleFormModal(event) {
		var orderId = $(event.target).parents('tr').attr('id') || $('body').find('#gm_order_id').val();
		$('#sc_modal_content').empty().addClass('sc_loading');
		var button_create_label = jse.core.lang.translate('create_label', 'shipcloud');
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
			width: 1200,
			position: { my: 'center top', at: 'center bottom', of: '#main-header' }
		});
		$('#shipcloud_modal').dialog('open');
		//$('#sc_modal_content').html('<p>Hallo!</p>');
		$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateLabelForm&template_version=2&orders_id=' + orderId, _singleFormInit);
	};

	var _showLabelsHandler = function _showLabelsHandler(e) {
		var orders_id = $('#sc_single_form input[name="orders_id"]').val();
		$('#sc_modal_content').empty().addClass('sc_loading');
		_loadLabelList(orders_id);
		$('#sc_show_labels').hide();
		$('#sc_get_quote').hide();
		return false;
	};

	var _loadLabelList = function _loadLabelList(orders_id) {
		$('#sc_modal_content').load('admin.php?do=Shipcloud/LoadLabelList&orders_id=' + orders_id + '&template_version=2', function () {
			gx.widgets.init($('#sc_modal_content'));
			$('#shipcloud_modal').dialog({
				'title': jse.core.lang.translate('labellist_for', 'shipcloud') + ' ' + orders_id
			});
			$('#sc_modal_content').removeClass('sc_loading');

			$('form#sc_pickup').on('submit', function (e) {
				e.preventDefault();
			});
			$('#download_labels').on('click', _packedDownloadHandler);
			$('#order_pickups').on('click', _pickupSubmitHandler);
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
				    $row = $(this).closest('tr');
				$.ajax({
					type: 'POST',
					url: jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/DeleteShipment',
					data: { shipment_id: shipment_id },
					dataType: 'json'
				}).done(function (data) {
					if (data.result === 'ERROR') {
						$('#status-output').html(data.error_message).show();
						$('#status-output').addClass('alert alert-danger');
					} else {
						$('#status-output').html(jse.core.lang.translate('shipment_deleted', 'shipcloud')).removeClass().addClass('alert alert-info').show();
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
			$('#pickup_result').addClass('alert alert-warning');
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

	var _labellistPickupCheckboxHandler = function _labellistPickupCheckboxHandler() {
		$('#sc-labellist-dropdown button, div.pickup_time input').prop('disabled', $('input.pickup_checkbox:checked').length === 0);
	};

	var _loadUnconfiguredNote = function _loadUnconfiguredNote() {
		$('#sc_modal_content').load('admin.php?do=Shipcloud/UnconfiguredNote');
	};

	var _singleFormGetQuoteHandler = function _singleFormGetQuoteHandler() {
		var $form = $('#sc_single_form');
		var quote = '';

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
		var $form = $(this).closest('form'),
		    $template = $('option:selected', $(this));
		if ($template.val() !== '-1') {
			$('input[name="package[weight]"]', $form).val($template.data('weight'));
			$('input[name="package[height]"]', $form).val($template.data('height'));
			$('input[name="package[width]"]', $form).val($template.data('width'));
			$('input[name="package[length]"]', $form).val($template.data('length'));
		}
	};

	var _singleFormSubmitHandler = function _singleFormSubmitHandler(e) {
		$('#sc_show_labels').hide();
		$('#sc_get_quote').hide();
		var carrier = $(this).attr('name');
		$('input[name="carrier"]').val(carrier);
		var formdata = $('#sc_single_form').serialize();
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

			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

	var _multiDropdownHandler = function _multiDropdownHandler(e) {
		var selected_orders = [],
		    orders_param = '';
		$('table.table tbody tr').each(function () {
			var order_id = $(this).attr('id'),
			    $checkbox = $('td:nth-child(1) span.single-checkbox', this);
			if ($checkbox.hasClass('checked')) {
				selected_orders.push(order_id);
			}
		});
		$('#sc_modal_content').empty().addClass('sc_loading');
		var shipcloud_modal_buttons = [];
		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('get_quotes', 'shipcloud'),
			'class': 'btn btn-primary',
			'click': _multiFormGetQuoteHandler,
			'id': 'sc_get_quote'
		});
		shipcloud_modal_buttons.push({
			'text': jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function click() {
				$(this).dialog('close');
				$('#sc_get_quote').show();
			}
		});

		$('#shipcloud_modal').dialog({
			autoOpen: false,
			modal: true,
			'title': jse.core.lang.translate('create_labels', 'shipcloud'),
			'dialogClass': 'gx-container',
			buttons: shipcloud_modal_buttons,
			width: 1200,
			position: { my: 'center top', at: 'center bottom', of: '#main-header' }
		});

		$('#shipcloud_modal').dialog('open');
		selected_orders.forEach(function (item) {
			orders_param += 'orders[]=' + item + '&';
		});
		$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateMultiLabelForm&template_version=2&' + orders_param, _multiFormInit);
	};

	var _multiFormInit = function _multiFormInit() {
		gx.widgets.init($('#shipcloud_modal'));
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
		var carrier = $(this).attr('name');
		$('#sc_multi_form input[name="carrier"]').val(carrier);
		var formdata = $('#sc_multi_form').serialize();
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

			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');
		}).fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	var _loadMultiLabelList = function _loadMultiLabelList(orders_ids, shipments) {
		var multiLabelListParams = { 'orders_ids': orders_ids, 'shipments': shipments };

		$('#sc_modal_content').load(jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/LoadMultiLabelList&template_version=2', { "json": JSON.stringify(multiLabelListParams) }, function () {
			gx.widgets.init($('#shipcloud_modal'));
			$('#shipcloud_modal').dialog({
				'title': jse.core.lang.translate('labellist', 'shipcloud')
			});
			$('#sc_modal_content').removeClass('sc_loading');
			$('#sc_get_quote').hide();

			$('form#sc_pickup').on('submit', function (e) {
				e.preventDefault();
			});
			$('#download_labels').on('click', _packedDownloadHandler);
			$('#order_pickups').on('click', _pickupSubmitHandler);
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
		var formdata = $('#sc_multi_form').serialize();
		$('div.sc_quote').html('');
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

	/* =========================================================================================================== */

	$('body').prepend($('<div id="shipcloud_modal" title="' + jse.core.lang.translate('create_label_window_title', 'shipcloud') + '" style="display: none;"><div id="sc_modal_content"></div></div>'));

	var $table = $('.orders .table-main');

	$table.on('init.dt', function () {
		var addRowAction = function addRowAction() {
			$table.find('.btn-group.dropdown').each(function () {
				var orderId = $(this).parents('tr').data('id'),
				    defaultRowAction = $table.data('init-default-row-action') || 'edit';

				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('admin_menu_entry', 'shipcloud'),
					href: 'orders.php?oID=' + orderId + '&action=edit',
					class: 'sc-single',
					data: { configurationValue: 'sc-single' },
					isDefault: defaultRowAction === 'sc-single',
					callback: function callback(e) {
						e.preventDefault();_openSingleFormModal(e);
					}
				});
			});
		};
		$table.on('draw.dt', addRowAction);
		addRowAction();

		var $bulkActions = $('.bulk-action'),
		    defaultBulkAction = $table.data('init-default-bulk-action') || 'edit';
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('admin_menu_entry', 'shipcloud'),
			class: 'sc-multi',
			data: { configurationValue: 'sc-multi' },
			isDefault: defaultBulkAction === 'sc-multi',
			callback: function callback(e) {
				e.preventDefault();_multiDropdownHandler(e);
			}
		});
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNoaXBjbG91ZC5qcyJdLCJuYW1lcyI6WyIkIiwiX29wZW5TaW5nbGVGb3JtTW9kYWwiLCJldmVudCIsIm9yZGVySWQiLCJ0YXJnZXQiLCJwYXJlbnRzIiwiYXR0ciIsImZpbmQiLCJ2YWwiLCJlbXB0eSIsImFkZENsYXNzIiwiYnV0dG9uX2NyZWF0ZV9sYWJlbCIsImpzZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwic2hpcGNsb3VkX21vZGFsX2J1dHRvbnMiLCJwdXNoIiwiZGlhbG9nIiwic2hvdyIsIl9zaG93TGFiZWxzSGFuZGxlciIsIl9zaW5nbGVGb3JtR2V0UXVvdGVIYW5kbGVyIiwiYXV0b09wZW4iLCJtb2RhbCIsImJ1dHRvbnMiLCJ3aWR0aCIsInBvc2l0aW9uIiwibXkiLCJhdCIsIm9mIiwibG9hZCIsIl9zaW5nbGVGb3JtSW5pdCIsImUiLCJvcmRlcnNfaWQiLCJfbG9hZExhYmVsTGlzdCIsImhpZGUiLCJneCIsIndpZGdldHMiLCJpbml0IiwicmVtb3ZlQ2xhc3MiLCJvbiIsInByZXZlbnREZWZhdWx0IiwiX3BhY2tlZERvd25sb2FkSGFuZGxlciIsIl9waWNrdXBTdWJtaXRIYW5kbGVyIiwiX2xhYmVsbGlzdFBpY2t1cENoZWNrYm94SGFuZGxlciIsInNldFRpbWVvdXQiLCJwcm9wIiwicGFyZW50Iiwic2hpcG1lbnRfaWQiLCJkYXRhIiwiJHJvdyIsImNsb3Nlc3QiLCJhamF4IiwidHlwZSIsInVybCIsImNvbmZpZyIsImdldCIsImRhdGFUeXBlIiwiZG9uZSIsInJlc3VsdCIsImh0bWwiLCJlcnJvcl9tZXNzYWdlIiwicmVtb3ZlIiwiZmFpbCIsIiRidXR0b25QbGFjZSIsInVybHMiLCJyZXF1ZXN0IiwiZWFjaCIsImhyZWYiLCJwYWdlX3Rva2VuIiwiSlNPTiIsInN0cmluZ2lmeSIsImRvd25sb2FkbGluayIsImRvd25sb2FkS2V5IiwibGVuZ3RoIiwiZm9ybWRhdGEiLCJzZXJpYWxpemUiLCJyZXN1bHRfbWVzc2FnZSIsInJlc3VsdF9tZXNzYWdlcyIsImZvckVhY2giLCJtZXNzYWdlIiwiYWxlcnQiLCJfbG9hZFVuY29uZmlndXJlZE5vdGUiLCIkZm9ybSIsInF1b3RlIiwiY2FycmllciIsIiRjcmVhdGVfbGFiZWwiLCJzaGlwbWVudF9xdW90ZSIsIl9zaW5nbGVGb3JtU3VibWl0SGFuZGxlciIsInRyaWdnZXIiLCJub3QiLCJfdGVtcGxhdGVTZWxlY3Rpb25IYW5kbGVyIiwiYnV0dG9uIiwiJHRlbXBsYXRlIiwiRGF0YVRhYmxlIiwicmVsb2FkIiwib3JkZXJzX292ZXJ2aWV3X2ZpbHRlciIsIl9tdWx0aURyb3Bkb3duSGFuZGxlciIsInNlbGVjdGVkX29yZGVycyIsIm9yZGVyc19wYXJhbSIsIm9yZGVyX2lkIiwiJGNoZWNrYm94IiwiaGFzQ2xhc3MiLCJfbXVsdGlGb3JtR2V0UXVvdGVIYW5kbGVyIiwiaXRlbSIsIl9tdWx0aUZvcm1Jbml0IiwiX211bHRpRm9ybVN1Ym1pdEhhbmRsZXIiLCJfbG9hZE11bHRpTGFiZWxMaXN0Iiwib3JkZXJzX2lkcyIsInNoaXBtZW50cyIsIm11bHRpTGFiZWxMaXN0UGFyYW1zIiwiX211bHRpUGlja3VwU3VibWl0SGFuZGxlciIsInNxdW90ZSIsInNoaXBtZW50X3F1b3RlcyIsImNhcnJpZXJzX3RvdGFsIiwicHJlcGVuZCIsIiR0YWJsZSIsImFkZFJvd0FjdGlvbiIsImRlZmF1bHRSb3dBY3Rpb24iLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwiYWRkQWN0aW9uIiwidGV4dCIsImNsYXNzIiwiY29uZmlndXJhdGlvblZhbHVlIiwiaXNEZWZhdWx0IiwiY2FsbGJhY2siLCIkYnVsa0FjdGlvbnMiLCJkZWZhdWx0QnVsa0FjdGlvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxFQUFFLFlBQVc7QUFDWjs7QUFFQSxLQUFNQyx1QkFBdUIsU0FBdkJBLG9CQUF1QixDQUFTQyxLQUFULEVBQzdCO0FBQ0MsTUFBTUMsVUFBVUgsRUFBRUUsTUFBTUUsTUFBUixFQUFnQkMs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LEVBQXFCNkUsWUFBckI7QUFDQUE7O0FBRUEsTUFBTVUsZUFBZS9ILEVBQUUsY0FBRixDQUFyQjtBQUFBLE1BQ0NnSSxvQkFBb0JaLE9BQU9uRSxJQUFQLENBQVksMEJBQVosS0FBMkMsTUFEaEU7QUFFQXJDLE1BQUkyRyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJDLFNBQXpCLENBQW1DTSxZQUFuQyxFQUFpRDtBQUNoREwsU0FBTTlHLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLGtCQUF4QixFQUE0QyxXQUE1QyxDQUQwQztBQUVoRDRHLFVBQU8sVUFGeUM7QUFHaEQxRSxTQUFNLEVBQUMyRSxvQkFBb0IsVUFBckIsRUFIMEM7QUFJaERDLGNBQVdHLHNCQUFzQixVQUplO0FBS2hERixhQUFVLGtCQUFTOUYsQ0FBVCxFQUFZO0FBQUVBLE1BQUVTLGNBQUYsR0FBb0J3RCxzQkFBc0JqRSxDQUF0QjtBQUEyQjtBQUx2QixHQUFqRDtBQU9BLEVBNUJEO0FBOEJBLENBM2hCRCIsImZpbGUiOiJzaGlwY2xvdWQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRzaGlwY2xvdWQuanMgMjAxNi0wOS0yMVxuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuJChmdW5jdGlvbigpIHtcblx0J3VzZSBzdHJpY3QnO1xuXG5cdGNvbnN0IF9vcGVuU2luZ2xlRm9ybU1vZGFsID0gZnVuY3Rpb24oZXZlbnQpXG5cdHtcblx0XHRjb25zdCBvcmRlcklkID0gJChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RyJykuYXR0cignaWQnKSB8fCAkKCdib2R5JykuZmluZCgnI2dtX29yZGVyX2lkJykudmFsKCk7XG5cdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5lbXB0eSgpLmFkZENsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0Y29uc3QgYnV0dG9uX2NyZWF0ZV9sYWJlbCA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfbGFiZWwnLCAnc2hpcGNsb3VkJyk7XG5cdFx0bGV0IHNoaXBjbG91ZF9tb2RhbF9idXR0b25zID0gW107XG5cblx0XHRzaGlwY2xvdWRfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdCd0ZXh0JzogIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5kaWFsb2coJ2Nsb3NlJyk7XG5cdFx0XHRcdFx0XHQkKCcjc2NfZ2V0X3F1b3RlJykuc2hvdygpO1xuXHRcdFx0XHRcdH1cblx0XHR9KTtcblx0XHRzaGlwY2xvdWRfbW9kYWxfYnV0dG9ucy5wdXNoKHtcblx0XHRcdCd0ZXh0JzogIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzaG93X2V4aXN0aW5nX2xhYmVscycsICdzaGlwY2xvdWQnKSxcblx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0J2NsaWNrJzogX3Nob3dMYWJlbHNIYW5kbGVyLFxuXHRcdFx0J2lkJzogICAgJ3NjX3Nob3dfbGFiZWxzJ1xuXHRcdH0pO1xuXHRcdHNoaXBjbG91ZF9tb2RhbF9idXR0b25zLnB1c2goe1xuXHRcdFx0J3RleHQnOiAganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2dldF9xdW90ZXMnLCAnc2hpcGNsb3VkJyksXG5cdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdCdjbGljayc6IF9zaW5nbGVGb3JtR2V0UXVvdGVIYW5kbGVyLFxuXHRcdFx0J2lkJzogICAgJ3NjX2dldF9xdW90ZSdcblx0XHR9KTtcblxuXHRcdCQoJyNzaGlwY2xvdWRfbW9kYWwnKS5kaWFsb2coe1xuXHRcdFx0YXV0b09wZW46ICAgICAgZmFsc2UsXG5cdFx0XHRtb2RhbDogICAgICAgICB0cnVlLFxuXHRcdFx0J3RpdGxlJzogICAgICAganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2NyZWF0ZV9sYWJlbCcsICdzaGlwY2xvdWQnKSxcblx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0YnV0dG9uczogICAgICAgc2hpcGNsb3VkX21vZGFsX2J1dHRvbnMsXG5cdFx0XHR3aWR0aDogICAgICAgICAxMjAwLFxuXHRcdFx0cG9zaXRpb246ICAgICAgeyBteTogJ2NlbnRlciB0b3AnLCBhdDogJ2NlbnRlciBib3R0b20nLCBvZjogJyNtYWluLWhlYWRlcicgfVxuXHRcdH0pO1xuXHRcdCQoJyNzaGlwY2xvdWRfbW9kYWwnKS5kaWFsb2coJ29wZW4nKTtcblx0XHQvLyQoJyNzY19tb2RhbF9jb250ZW50JykuaHRtbCgnPHA+SGFsbG8hPC9wPicpO1xuXHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9DcmVhdGVMYWJlbEZvcm0mdGVtcGxhdGVfdmVyc2lvbj0yJm9yZGVyc19pZD0nICsgb3JkZXJJZCwgX3NpbmdsZUZvcm1Jbml0KTtcblx0fTtcblxuXHRjb25zdCBfc2hvd0xhYmVsc0hhbmRsZXIgPSBmdW5jdGlvbiAoZSkge1xuXHRcdGNvbnN0IG9yZGVyc19pZCA9ICQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dFtuYW1lPVwib3JkZXJzX2lkXCJdJykudmFsKCk7XG5cdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5lbXB0eSgpLmFkZENsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0X2xvYWRMYWJlbExpc3Qob3JkZXJzX2lkKTtcblx0XHQkKCcjc2Nfc2hvd19sYWJlbHMnKS5oaWRlKCk7XG5cdFx0JCgnI3NjX2dldF9xdW90ZScpLmhpZGUoKTtcblx0XHRyZXR1cm4gZmFsc2U7XG5cdH07XG5cblx0Y29uc3QgX2xvYWRMYWJlbExpc3QgPSBmdW5jdGlvbiAob3JkZXJzX2lkKVxuXHR7XG5cdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5sb2FkKCdhZG1pbi5waHA/ZG89U2hpcGNsb3VkL0xvYWRMYWJlbExpc3Qmb3JkZXJzX2lkPScgKyBvcmRlcnNfaWQgKyAnJnRlbXBsYXRlX3ZlcnNpb249MicsXG5cdFx0XHRmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdGd4LndpZGdldHMuaW5pdCgkKCcjc2NfbW9kYWxfY29udGVudCcpKTtcblx0XHRcdFx0JCgnI3NoaXBjbG91ZF9tb2RhbCcpLmRpYWxvZyh7XG5cdFx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2xhYmVsbGlzdF9mb3InLCAnc2hpcGNsb3VkJykgKyAnICcgKyBvcmRlcnNfaWRcblx0XHRcdFx0fSk7XG5cdFx0XHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykucmVtb3ZlQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblxuXHRcdFx0XHQkKCdmb3JtI3NjX3BpY2t1cCcpLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihlKSB7IGUucHJldmVudERlZmF1bHQoKTsgfSk7XG5cdFx0XHRcdCQoJyNkb3dubG9hZF9sYWJlbHMnKS5vbignY2xpY2snLCBfcGFja2VkRG93bmxvYWRIYW5kbGVyKTtcblx0XHRcdFx0JCgnI29yZGVyX3BpY2t1cHMnKS5vbignY2xpY2snLCBfcGlja3VwU3VibWl0SGFuZGxlcik7XG5cdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLm9uKCdjbGljaycsIF9sYWJlbGxpc3RQaWNrdXBDaGVja2JveEhhbmRsZXIpO1xuXHRcdFx0XHRzZXRUaW1lb3V0KF9sYWJlbGxpc3RQaWNrdXBDaGVja2JveEhhbmRsZXIsIDIwMCk7XG5cdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveF9hbGwnKS5vbignY2xpY2snLCBmdW5jdGlvbigpXG5cdFx0XHRcdHtcblx0XHRcdFx0XHRpZiAoJCh0aGlzKS5wcm9wKCdjaGVja2VkJykgPT09IHRydWUpXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xuXHRcdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94JykucGFyZW50KCkuYWRkQ2xhc3MoJ2NoZWNrZWQnKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0ZWxzZVxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLnByb3AoJ2NoZWNrZWQnLCBmYWxzZSk7XG5cdFx0XHRcdFx0XHQkKCdpbnB1dC5waWNrdXBfY2hlY2tib3gnKS5wYXJlbnQoKS5yZW1vdmVDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyKCk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0XHQkKCdhLnNjLWRlbC1sYWJlbCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uKGUpIHtcblx0XHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRcdFx0Y29uc3Qgc2hpcG1lbnRfaWQgID0gJCh0aGlzKS5kYXRhKCdzaGlwbWVudC1pZCcpLFxuXHRcdFx0XHRcdCAgICAgICRyb3cgICAgICAgICA9ICQodGhpcykuY2xvc2VzdCgndHInKTtcblx0XHRcdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdFx0dHlwZTogICAgICdQT1NUJyxcblx0XHRcdFx0XHRcdHVybDogICAgICAganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2FkbWluLnBocD9kbz1TaGlwY2xvdWQvRGVsZXRlU2hpcG1lbnQnLFxuXHRcdFx0XHRcdFx0ZGF0YTogICAgIHsgc2hpcG1lbnRfaWQ6IHNoaXBtZW50X2lkIH0sXG5cdFx0XHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHRcdFx0fSlcblx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHRcdFx0XHRpZihkYXRhLnJlc3VsdCA9PT0gJ0VSUk9SJylcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0JCgnI3N0YXR1cy1vdXRwdXQnKS5odG1sKGRhdGEuZXJyb3JfbWVzc2FnZSkuc2hvdygpO1xuXHRcdFx0XHRcdFx0XHQkKCcjc3RhdHVzLW91dHB1dCcpLmFkZENsYXNzKCdhbGVydCBhbGVydC1kYW5nZXInKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdGVsc2Vcblx0XHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdFx0JCgnI3N0YXR1cy1vdXRwdXQnKVxuXHRcdFx0XHRcdFx0XHRcdC5odG1sKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzaGlwbWVudF9kZWxldGVkJywgJ3NoaXBjbG91ZCcpKVxuXHRcdFx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcygpXG5cdFx0XHRcdFx0XHRcdFx0LmFkZENsYXNzKCdhbGVydCBhbGVydC1pbmZvJylcblx0XHRcdFx0XHRcdFx0XHQuc2hvdygpO1xuXHRcdFx0XHRcdFx0XHQkKCdhLCBpbnB1dCwgdGQuY2hlY2tib3ggPiAqJywgJHJvdykucmVtb3ZlKCk7XG5cdFx0XHRcdFx0XHRcdCRyb3cuYWRkQ2xhc3MoJ2RlbGV0ZWQtc2hpcG1lbnQnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0XHRcdCRidXR0b25QbGFjZS5odG1sKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzdWJtaXRfZXJyb3InLCAnc2hpcGNsb3VkJykpO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHR9O1xuXG5cdGNvbnN0IF9wYWNrZWREb3dubG9hZEhhbmRsZXIgPSBmdW5jdGlvbihlKVxuXHR7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdGxldCB1cmxzID0gW10sIHJlcXVlc3QgPSB7fTtcblx0XHQkKCdpbnB1dC5waWNrdXBfY2hlY2tib3g6Y2hlY2tlZCcpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRjb25zdCBocmVmID0gJCgnYS5sYWJlbC1saW5rJywgJCh0aGlzKS5jbG9zZXN0KCd0cicpKS5hdHRyKCdocmVmJyk7XG5cdFx0XHR1cmxzLnB1c2goaHJlZik7XG5cdFx0fSk7XG5cdFx0aWYgKHVybHMpXG5cdFx0e1xuXHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLnNob3coKTtcblx0XHRcdCQoJyNkb3dubG9hZF9yZXN1bHQnKS5odG1sKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdsb2FkaW5nJywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdHJlcXVlc3QudXJscyAgICAgICA9IHVybHM7XG5cdFx0XHRyZXF1ZXN0LnBhZ2VfdG9rZW4gPSAkKCcjc2NfbW9kYWxfY29udGVudCBpbnB1dFtuYW1lPVwicGFnZV90b2tlblwiXScpLnZhbCgpO1xuXG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHR0eXBlOiAgICAgJ1BPU1QnLFxuXHRcdFx0XHR1cmw6ICAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89UGFja2VkRG93bmxvYWQvRG93bmxvYWRCeUpzb24nLFxuXHRcdFx0XHRkYXRhOiAgICAgSlNPTi5zdHJpbmdpZnkocmVxdWVzdCksXG5cdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHRcdGNvbnN0IGRvd25sb2FkbGluayA9XG5cdFx0XHRcdFx0ICBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArXG5cdFx0XHRcdFx0ICAnL2FkbWluL2FkbWluLnBocD9kbz1QYWNrZWREb3dubG9hZC9Eb3dubG9hZFBhY2thZ2Uma2V5PScgK1xuXHRcdFx0XHRcdCAgZGF0YS5kb3dubG9hZEtleTtcblx0XHRcdFx0aWYgKGRhdGEucmVzdWx0ID09PSAnT0snKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLmh0bWwoJzxpZnJhbWUgY2xhc3M9XCJkb3dubG9hZF9pZnJhbWVcIiBzcmM9XCInK2Rvd25sb2FkbGluaysnXCI+PC9pZnJhbWU+Jyk7XG5cdFx0XHRcdH1cblx0XHRcdFx0aWYgKGRhdGEucmVzdWx0ID09PSAnRVJST1InKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLmh0bWwoZGF0YS5lcnJvcl9tZXNzYWdlKTtcblx0XHRcdFx0fVxuXHRcdFx0fSlcblx0XHRcdC5mYWlsKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0JCgnI2Rvd25sb2FkX3Jlc3VsdCcpLmh0bWwoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0XHR9KTtcblx0XHR9XG5cdFx0cmV0dXJuIHRydWU7XG5cdH07XG5cblx0Y29uc3QgX3BpY2t1cFN1Ym1pdEhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xuXHRcdGlmICgkKCdpbnB1dC5waWNrdXBfY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aCA+IDApXG5cdFx0e1xuXHRcdFx0Y29uc3QgZm9ybWRhdGEgPSAkKCdmb3JtI3NjX3BpY2t1cCcpLnNlcmlhbGl6ZSgpO1xuXHRcdFx0JCgnI3BpY2t1cF9yZXN1bHQnKS5odG1sKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzZW5kaW5nX3BpY2t1cF9yZXF1ZXN0JywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdCQoJyNwaWNrdXBfcmVzdWx0Jykuc2hvdygpO1xuXHRcdFx0JCgnI3BpY2t1cF9yZXN1bHQnKS5hZGRDbGFzcygnYWxlcnQgYWxlcnQtd2FybmluZycpO1xuXHRcdFx0JC5hamF4KHtcblx0XHRcdFx0dHlwZTogICAgICdQT1NUJyxcblx0XHRcdFx0dXJsOiAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89U2hpcGNsb3VkL1BpY2t1cFNoaXBtZW50cycsXG5cdFx0XHRcdGRhdGE6ICAgICBmb3JtZGF0YSxcblx0XHRcdFx0ZGF0YVR5cGU6ICdqc29uJ1xuXHRcdFx0fSlcblx0XHRcdC5kb25lKGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdFx0bGV0IHJlc3VsdF9tZXNzYWdlID0gJyc7XG5cdFx0XHRcdGRhdGEucmVzdWx0X21lc3NhZ2VzLmZvckVhY2goZnVuY3Rpb24obWVzc2FnZSkgeyByZXN1bHRfbWVzc2FnZSA9IHJlc3VsdF9tZXNzYWdlICsgbWVzc2FnZSArICc8YnI+JzsgfSk7XG5cdFx0XHRcdCQoJyNwaWNrdXBfcmVzdWx0JykuaHRtbChyZXN1bHRfbWVzc2FnZSk7XG5cdFx0XHR9KVxuXHRcdFx0LmZhaWwoZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0XHRhbGVydChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc3VibWl0X2Vycm9yJywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdH0pO1xuXHRcdH1cblx0XHRyZXR1cm4gdHJ1ZTtcblx0fTtcblxuXHRjb25zdCBfbGFiZWxsaXN0UGlja3VwQ2hlY2tib3hIYW5kbGVyID0gZnVuY3Rpb24oKSB7XG5cdFx0JCgnI3NjLWxhYmVsbGlzdC1kcm9wZG93biBidXR0b24sIGRpdi5waWNrdXBfdGltZSBpbnB1dCcpXG5cdFx0XHQucHJvcCgnZGlzYWJsZWQnLCAkKCdpbnB1dC5waWNrdXBfY2hlY2tib3g6Y2hlY2tlZCcpLmxlbmd0aCA9PT0gMCk7XG5cdH07XG5cblx0Y29uc3QgX2xvYWRVbmNvbmZpZ3VyZWROb3RlID0gZnVuY3Rpb24gKClcblx0e1xuXHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykubG9hZCgnYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9VbmNvbmZpZ3VyZWROb3RlJyk7XG5cdH07XG5cblxuXHRjb25zdCBfc2luZ2xlRm9ybUdldFF1b3RlSGFuZGxlciA9IGZ1bmN0aW9uKClcblx0e1xuXHRcdGNvbnN0ICRmb3JtID0gJCgnI3NjX3NpbmdsZV9mb3JtJyk7XG5cdFx0bGV0ICAgcXVvdGUgPSAnJztcblxuXHRcdCQoJyNzY19zaW5nbGVfZm9ybSAuc2NfcXVvdGUnKS5odG1sKCcnKTtcblx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0gLnNjX3F1b3RlJykuYXR0cigndGl0bGUnLCAnJyk7XG5cblx0XHQkKCdpbnB1dFtuYW1lPVwicXVvdGVfY2FycmllcnNbXVwiXTpjaGVja2VkJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdGNvbnN0IGNhcnJpZXIgPSAkKHRoaXMpLnZhbCgpLFxuXHRcdFx0ICAgICAgJGNyZWF0ZV9sYWJlbCA9ICQoJ2lucHV0LmNyZWF0ZV9sYWJlbCcsICQodGhpcykuY2xvc2VzdCgndHInKSk7XG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwiY2FycmllclwiXScsICRmb3JtKS52YWwoY2Fycmllcik7XG5cdFx0XHQkKCcjc2NfcXVvdGVfJytjYXJyaWVyKS5odG1sKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdsb2FkaW5nJywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdHR5cGU6ICAgICAnUE9TVCcsXG5cdFx0XHRcdHVybDogICAgICBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9HZXRTaGlwbWVudFF1b3RlJyxcblx0XHRcdFx0ZGF0YTogICAgICRmb3JtLnNlcmlhbGl6ZSgpLFxuXHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHR9KVxuXHRcdFx0LmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdFx0aWYgKGRhdGEucmVzdWx0ID09PSAnT0snKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0cXVvdGUgPSBkYXRhLnNoaXBtZW50X3F1b3RlO1xuXHRcdFx0XHRcdCQoJyNzY19xdW90ZV8nK2NhcnJpZXIpLmh0bWwocXVvdGUpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGVsc2UgaWYgKGRhdGEucmVzdWx0ID09PSAnRVJST1InKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3NjX3F1b3RlXycrY2FycmllcikuaHRtbChqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbm90X3Bvc3NpYmxlJywgJ3NoaXBjbG91ZCcpKTtcblx0XHRcdFx0XHQkKCcjc2NfcXVvdGVfJytjYXJyaWVyKS5hdHRyKCd0aXRsZScsIGRhdGEuZXJyb3JfbWVzc2FnZSk7XG5cdFx0XHRcdH1cblx0XHRcdFx0ZWxzZSBpZiAoZGF0YS5yZXN1bHQgPT09ICdVTkNPTkZJR1VSRUQnKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0X2xvYWRVbmNvbmZpZ3VyZWROb3RlKCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pXG5cdFx0XHQuZmFpbChmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0XHRxdW90ZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdnZXRfcXVvdGVfZXJyb3InLCAnc2hpcGNsb3VkJyk7XG5cdFx0XHRcdCQoJyNzY19xdW90ZV8nK2NhcnJpZXIpLmh0bWwocXVvdGUpO1xuXHRcdFx0fSk7XG5cdFx0fSk7XG5cblx0XHQkKCdpbnB1dFtuYW1lPVwiY2FycmllclwiXScsICRmb3JtKS52YWwoJycpO1xuXHR9O1xuXG5cdGNvbnN0IF9zaW5nbGVGb3JtSW5pdCA9IGZ1bmN0aW9uKCkge1xuXHRcdGd4LndpZGdldHMuaW5pdCgkKCcjc2hpcGNsb3VkX21vZGFsJykpO1xuXHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykucmVtb3ZlQ2xhc3MoJ3NjX2xvYWRpbmcnKTtcblx0XHRpZiAoJCgnI3NjX3NpbmdsZV9jb250YWluZXInKS5kYXRhKCdpc19jb25maWd1cmVkJykgPT09IDEpXG5cdFx0e1xuXHRcdFx0JCgnI3NjX3Nob3dfbGFiZWxzJykuc2hvdygpO1xuXHRcdH1cblx0XHRlbHNlXG5cdFx0e1xuXHRcdFx0JCgnI3NjX3Nob3dfbGFiZWxzJykuaGlkZSgpO1xuXHRcdH1cblx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0nKS5vbignc3VibWl0JywgZnVuY3Rpb24oZSkgeyBlLnByZXZlbnREZWZhdWx0KCk7IH0pO1xuXHRcdCQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dC5jcmVhdGVfbGFiZWwnKS5vbignY2xpY2snLCBfc2luZ2xlRm9ybVN1Ym1pdEhhbmRsZXIpO1xuXHRcdCQoJyNzY19zaW5nbGVfZm9ybSBzZWxlY3RbbmFtZT1cImNhcnJpZXJcIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oZSkge1xuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIGlucHV0W3R5cGU9XCJ0ZXh0XCJdJykudHJpZ2dlcignY2hhbmdlJyk7XG5cdFx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0gLmNhcnJpZXItc3BlY2lmaWMnKS5ub3QoJy5jYXJyaWVyLScrJCh0aGlzKS52YWwoKSkuaGlkZSgnZmFzdCcpO1xuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIC5jYXJyaWVyLScrJCh0aGlzKS52YWwoKSkubm90KCc6dmlzaWJsZScpLnNob3coJ2Zhc3QnKTtcblx0XHR9KTtcblx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0gLnByaWNlX3ZhbHVlJykub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnI3NjX3NpbmdsZV9mb3JtIGRpdi5zY19xdW90ZScpLmh0bWwoJycpO1xuXHRcdH0pO1xuXHRcdCQoJyNzY19wYWNrYWdlX3RlbXBsYXRlJykub24oJ2NoYW5nZScsIF90ZW1wbGF0ZVNlbGVjdGlvbkhhbmRsZXIpO1xuXHRcdCQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dC50ZW1wbGF0ZV92YWx1ZScpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHsgJCgnI3NjX3BhY2thZ2VfdGVtcGxhdGUnKS52YWwoJy0xJyk7IH0pO1xuXHRcdCQoJyNzY19nZXRfcXVvdGUnKS5idXR0b24oJ2Rpc2FibGUnKTtcblx0XHQkKCcjc2Nfc2luZ2xlX2Zvcm0gaW5wdXRbbmFtZT1cInF1b3RlX2NhcnJpZXJzW11cIl0nKS5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRpZiAoJCgnI3NjX3NpbmdsZV9mb3JtIGlucHV0W25hbWU9XCJxdW90ZV9jYXJyaWVyc1tdXCJdOmNoZWNrZWQnKS5sZW5ndGggPiAwKVxuXHRcdFx0e1xuXHRcdFx0XHQkKCcjc2NfZ2V0X3F1b3RlJykuYnV0dG9uKCdlbmFibGUnKTtcblx0XHRcdH1cblx0XHRcdGVsc2Vcblx0XHRcdHtcblx0XHRcdFx0JCgnI3NjX2dldF9xdW90ZScpLmJ1dHRvbignZGlzYWJsZScpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXHRcdCQoJyNzY19zaW5nbGVfZm9ybSBpbnB1dFtuYW1lPVwicXVvdGVfY2FycmllcnNbXVwiXTpmaXJzdCcpLnRyaWdnZXIoJ2NoYW5nZScpO1xuXHR9O1xuXG5cdGNvbnN0IF90ZW1wbGF0ZVNlbGVjdGlvbkhhbmRsZXIgPSBmdW5jdGlvbihlKSB7XG5cdFx0Y29uc3QgJGZvcm0gICAgID0gJCh0aGlzKS5jbG9zZXN0KCdmb3JtJyksXG5cdFx0ICAgICAgJHRlbXBsYXRlID0gJCgnb3B0aW9uOnNlbGVjdGVkJywgJCh0aGlzKSk7XG5cdFx0aWYgKCR0ZW1wbGF0ZS52YWwoKSAhPT0gJy0xJylcblx0XHR7XG5cdFx0XHQkKCdpbnB1dFtuYW1lPVwicGFja2FnZVt3ZWlnaHRdXCJdJywgJGZvcm0pLnZhbCgkdGVtcGxhdGUuZGF0YSgnd2VpZ2h0JykpO1xuXHRcdFx0JCgnaW5wdXRbbmFtZT1cInBhY2thZ2VbaGVpZ2h0XVwiXScsICRmb3JtKS52YWwoJHRlbXBsYXRlLmRhdGEoJ2hlaWdodCcpKTtcblx0XHRcdCQoJ2lucHV0W25hbWU9XCJwYWNrYWdlW3dpZHRoXVwiXScsICAkZm9ybSkudmFsKCR0ZW1wbGF0ZS5kYXRhKCd3aWR0aCcpKTtcblx0XHRcdCQoJ2lucHV0W25hbWU9XCJwYWNrYWdlW2xlbmd0aF1cIl0nLCAkZm9ybSkudmFsKCR0ZW1wbGF0ZS5kYXRhKCdsZW5ndGgnKSk7XG5cdFx0fVxuXHR9O1xuXG5cdGNvbnN0IF9zaW5nbGVGb3JtU3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uIChlKSB7XG5cdFx0JCgnI3NjX3Nob3dfbGFiZWxzJykuaGlkZSgpO1xuXHRcdCQoJyNzY19nZXRfcXVvdGUnKS5oaWRlKCk7XG5cdFx0Y29uc3QgY2FycmllciA9ICQodGhpcykuYXR0cignbmFtZScpO1xuXHRcdCQoJ2lucHV0W25hbWU9XCJjYXJyaWVyXCJdJykudmFsKGNhcnJpZXIpO1xuXHRcdGNvbnN0IGZvcm1kYXRhID0gJCgnI3NjX3NpbmdsZV9mb3JtJykuc2VyaWFsaXplKCk7XG5cdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5lbXB0eSgpLmFkZENsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0Ly8gYWxlcnQoJ2RhdGE6ICcrZm9ybWRhdGEpO1xuXHRcdCQuYWpheCh7XG5cdFx0XHR0eXBlOiAgICAgJ1BPU1QnLFxuXHRcdFx0dXJsOiAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89U2hpcGNsb3VkL0NyZWF0ZUxhYmVsRm9ybVN1Ym1pdCcsXG5cdFx0XHRkYXRhOiAgICAgZm9ybWRhdGEsXG5cdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0fSlcblx0XHQuZG9uZShmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdFx0aWYgKGRhdGEucmVzdWx0ID09PSAnVU5DT05GSUdVUkVEJylcblx0XHRcdHtcblx0XHRcdFx0X2xvYWRVbmNvbmZpZ3VyZWROb3RlKCk7XG5cdFx0XHR9XG5cdFx0XHRlbHNlIGlmIChkYXRhLnJlc3VsdCA9PT0gJ09LJylcblx0XHRcdHtcblx0XHRcdFx0X2xvYWRMYWJlbExpc3QoZGF0YS5vcmRlcnNfaWQpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZVxuXHRcdFx0e1xuXHRcdFx0XHRpZiAoZGF0YS5lcnJvcl9tZXNzYWdlKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5odG1sKCc8ZGl2IGNsYXNzPVwic2NfZXJyb3JcIj4nK2RhdGEuZXJyb3JfbWVzc2FnZSsnPC9kaXY+Jyk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblxuXHRcdFx0JCgnLm9yZGVycyAudGFibGUtbWFpbicpLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XG5cdFx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcigncmVsb2FkJyk7XG5cdFx0fSlcblx0XHQuZmFpbChmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0fSk7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9O1xuXG5cdC8qIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtIC0gLSAtICovXG5cblx0Y29uc3QgX211bHRpRHJvcGRvd25IYW5kbGVyID0gZnVuY3Rpb24oZSlcblx0e1xuXHRcdGxldCBzZWxlY3RlZF9vcmRlcnMgPSBbXSwgb3JkZXJzX3BhcmFtID0gJyc7XG5cdFx0JCgndGFibGUudGFibGUgdGJvZHkgdHInKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0Y29uc3Qgb3JkZXJfaWQgPSAkKHRoaXMpLmF0dHIoJ2lkJyksXG5cdFx0XHQgICAgICAkY2hlY2tib3ggPSAkKCd0ZDpudGgtY2hpbGQoMSkgc3Bhbi5zaW5nbGUtY2hlY2tib3gnLCB0aGlzKTtcblx0XHRcdGlmKCRjaGVja2JveC5oYXNDbGFzcygnY2hlY2tlZCcpKVxuXHRcdFx0e1xuXHRcdFx0XHRzZWxlY3RlZF9vcmRlcnMucHVzaChvcmRlcl9pZCk7XG5cdFx0XHR9XG5cdFx0fSk7XG5cdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5lbXB0eSgpLmFkZENsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0bGV0IHNoaXBjbG91ZF9tb2RhbF9idXR0b25zID0gW107XG5cdFx0c2hpcGNsb3VkX21vZGFsX2J1dHRvbnMucHVzaCh7XG5cdFx0XHQndGV4dCc6ICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZ2V0X3F1b3RlcycsICdzaGlwY2xvdWQnKSxcblx0XHRcdCdjbGFzcyc6ICdidG4gYnRuLXByaW1hcnknLFxuXHRcdFx0J2NsaWNrJzogX211bHRpRm9ybUdldFF1b3RlSGFuZGxlcixcblx0XHRcdCdpZCc6ICAgICdzY19nZXRfcXVvdGUnXG5cdFx0fSk7XG5cdFx0c2hpcGNsb3VkX21vZGFsX2J1dHRvbnMucHVzaCh7XG5cdFx0XHQndGV4dCc6ICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0J2NsYXNzJzogJ2J0bicsXG5cdFx0XHQnY2xpY2snOiBmdW5jdGlvbiAoKSB7XG5cdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHQkKCcjc2NfZ2V0X3F1b3RlJykuc2hvdygpO1xuXHRcdFx0fVxuXHRcdH0pO1xuXG5cdFx0JCgnI3NoaXBjbG91ZF9tb2RhbCcpLmRpYWxvZyh7XG5cdFx0XHRhdXRvT3BlbjogICAgICBmYWxzZSxcblx0XHRcdG1vZGFsOiAgICAgICAgIHRydWUsXG5cdFx0XHQndGl0bGUnOiAgICAgICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY3JlYXRlX2xhYmVscycsICdzaGlwY2xvdWQnKSxcblx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0YnV0dG9uczogICAgICAgc2hpcGNsb3VkX21vZGFsX2J1dHRvbnMsXG5cdFx0XHR3aWR0aDogICAgICAgICAxMjAwLFxuXHRcdFx0cG9zaXRpb246ICAgICAgeyBteTogJ2NlbnRlciB0b3AnLCBhdDogJ2NlbnRlciBib3R0b20nLCBvZjogJyNtYWluLWhlYWRlcicgfVxuXHRcdH0pO1xuXG5cdFx0JCgnI3NoaXBjbG91ZF9tb2RhbCcpLmRpYWxvZygnb3BlbicpO1xuXHRcdHNlbGVjdGVkX29yZGVycy5mb3JFYWNoKGZ1bmN0aW9uKGl0ZW0pIHtcblx0XHRcdG9yZGVyc19wYXJhbSArPSAnb3JkZXJzW109JytpdGVtKycmJztcblx0XHR9KTtcblx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLmxvYWQoJ2FkbWluLnBocD9kbz1TaGlwY2xvdWQvQ3JlYXRlTXVsdGlMYWJlbEZvcm0mdGVtcGxhdGVfdmVyc2lvbj0yJicrb3JkZXJzX3BhcmFtLCBfbXVsdGlGb3JtSW5pdCk7XG5cdH07XG5cblx0Y29uc3QgX211bHRpRm9ybUluaXQgPSBmdW5jdGlvbigpIHtcblx0XHRneC53aWRnZXRzLmluaXQoJCgnI3NoaXBjbG91ZF9tb2RhbCcpKTtcblx0XHQkKCcjc2hpcGNsb3VkX21vZGFsJykuZGlhbG9nKHtcblx0XHRcdCd0aXRsZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjcmVhdGVfbGFiZWxzJywgJ3NoaXBjbG91ZCcpXG5cdFx0fSk7XG5cdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdCQoJyNzY19tdWx0aV9mb3JtJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGUpIHsgZS5wcmV2ZW50RGVmYXVsdCgpOyByZXR1cm4gZmFsc2U7IH0pO1xuXHRcdCQoJyNzY19jcmVhdGVfbGFiZWwnKS5oaWRlKCk7XG5cdFx0JCgnI3NjX3Nob3dfbGFiZWxzJykuaGlkZSgpO1xuXHRcdCQoJyNzY19tb2RhbF9jb250ZW50IGlucHV0LCAjc2NfbW9kYWxfY29udGVudCBzZWxlY3QnKS5vbignY2hhbmdlJywgZnVuY3Rpb24oKSB7XG5cdFx0XHQkKCcuc2NfbXVsdGlfcXVvdGUnKS5oaWRlKCk7XG5cdFx0fSk7XG5cdFx0JCgnI3NjX3BhY2thZ2VfdGVtcGxhdGUnKS5vbignY2hhbmdlJywgX3RlbXBsYXRlU2VsZWN0aW9uSGFuZGxlcik7XG5cdFx0JCgnaW5wdXQuY3JlYXRlX2xhYmVsJykub24oJ2NsaWNrJywgX211bHRpRm9ybVN1Ym1pdEhhbmRsZXIpO1xuXHR9O1xuXG5cdGNvbnN0IF9tdWx0aUZvcm1TdWJtaXRIYW5kbGVyID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRjb25zdCBjYXJyaWVyID0gJCh0aGlzKS5hdHRyKCduYW1lJyk7XG5cdFx0JCgnI3NjX211bHRpX2Zvcm0gaW5wdXRbbmFtZT1cImNhcnJpZXJcIl0nKS52YWwoY2Fycmllcik7XG5cdFx0Y29uc3QgZm9ybWRhdGEgPSAkKCcjc2NfbXVsdGlfZm9ybScpLnNlcmlhbGl6ZSgpO1xuXHRcdCQoJyNzY19tb2RhbF9jb250ZW50JykuZW1wdHkoKS5hZGRDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdCQuYWpheCh7XG5cdFx0XHR0eXBlOiAgICAgJ1BPU1QnLFxuXHRcdFx0dXJsOiAgICAgIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89U2hpcGNsb3VkL0NyZWF0ZU11bHRpTGFiZWxGb3JtU3VibWl0Jyxcblx0XHRcdGRhdGE6ICAgICBmb3JtZGF0YSxcblx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHR9KVxuXHRcdC5kb25lKGZ1bmN0aW9uIChkYXRhKSB7XG5cdFx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLnJlbW92ZUNsYXNzKCdzY19sb2FkaW5nJyk7XG5cdFx0XHRpZiAoZGF0YS5yZXN1bHQgPT09ICdVTkNPTkZJR1VSRUQnKVxuXHRcdFx0e1xuXHRcdFx0XHRfbG9hZFVuY29uZmlndXJlZE5vdGUoKTtcblx0XHRcdH1cblx0XHRcdGVsc2UgaWYgKGRhdGEucmVzdWx0ID09PSAnT0snKVxuXHRcdFx0e1xuXHRcdFx0XHRfbG9hZE11bHRpTGFiZWxMaXN0KGRhdGEub3JkZXJzX2lkcywgZGF0YS5zaGlwbWVudHMpO1xuXHRcdFx0fVxuXHRcdFx0ZWxzZVxuXHRcdFx0e1xuXHRcdFx0XHRpZiAoZGF0YS5lcnJvcl9tZXNzYWdlKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5odG1sKCc8ZGl2IGNsYXNzPVwic2NfZXJyb3JcIj4nK2RhdGEuZXJyb3JfbWVzc2FnZSsnPC9kaXY+Jyk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblxuXHRcdFx0JCgnLm9yZGVycyAudGFibGUtbWFpbicpLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKCk7XG5cdFx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcigncmVsb2FkJyk7XG5cdFx0fSlcblx0XHQuZmFpbChmdW5jdGlvbiAoZGF0YSkge1xuXHRcdFx0YWxlcnQoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3N1Ym1pdF9lcnJvcicsICdzaGlwY2xvdWQnKSk7XG5cdFx0fSk7XG5cdFx0cmV0dXJuIGZhbHNlO1xuXHR9O1xuXG5cdGNvbnN0IF9sb2FkTXVsdGlMYWJlbExpc3QgPSBmdW5jdGlvbihvcmRlcnNfaWRzLCBzaGlwbWVudHMpXG5cdHtcblx0XHRjb25zdCBtdWx0aUxhYmVsTGlzdFBhcmFtcyA9IHsgJ29yZGVyc19pZHMnOiBvcmRlcnNfaWRzLCAnc2hpcG1lbnRzJzogc2hpcG1lbnRzIH07XG5cblx0XHQkKCcjc2NfbW9kYWxfY29udGVudCcpLmxvYWQoXG5cdFx0XHRqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9Mb2FkTXVsdGlMYWJlbExpc3QmdGVtcGxhdGVfdmVyc2lvbj0yJyxcblx0XHRcdHsgXCJqc29uXCI6IEpTT04uc3RyaW5naWZ5KG11bHRpTGFiZWxMaXN0UGFyYW1zKSB9LFxuXHRcdFx0ZnVuY3Rpb24gKCkge1xuXHRcdFx0XHRneC53aWRnZXRzLmluaXQoJCgnI3NoaXBjbG91ZF9tb2RhbCcpKTtcblx0XHRcdFx0JCgnI3NoaXBjbG91ZF9tb2RhbCcpLmRpYWxvZyh7XG5cdFx0XHRcdFx0J3RpdGxlJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2xhYmVsbGlzdCcsICdzaGlwY2xvdWQnKVxuXHRcdFx0XHR9KTtcblx0XHRcdFx0JCgnI3NjX21vZGFsX2NvbnRlbnQnKS5yZW1vdmVDbGFzcygnc2NfbG9hZGluZycpO1xuXHRcdFx0XHQkKCcjc2NfZ2V0X3F1b3RlJykuaGlkZSgpO1xuXG5cdFx0XHRcdCQoJ2Zvcm0jc2NfcGlja3VwJykub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGUpIHsgZS5wcmV2ZW50RGVmYXVsdCgpOyB9KTtcblx0XHRcdFx0JCgnI2Rvd25sb2FkX2xhYmVscycpLm9uKCdjbGljaycsIF9wYWNrZWREb3dubG9hZEhhbmRsZXIpO1xuXHRcdFx0XHQkKCcjb3JkZXJfcGlja3VwcycpLm9uKCdjbGljaycsIF9waWNrdXBTdWJtaXRIYW5kbGVyKTtcblx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94Jykub24oJ2NsaWNrJywgX2xhYmVsbGlzdFBpY2t1cENoZWNrYm94SGFuZGxlcik7XG5cdFx0XHRcdHNldFRpbWVvdXQoX2xhYmVsbGlzdFBpY2t1cENoZWNrYm94SGFuZGxlciwgMjAwKTtcblx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94X2FsbCcpLm9uKCdjbGljaycsIGZ1bmN0aW9uKClcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGlmICgkKHRoaXMpLnByb3AoJ2NoZWNrZWQnKSA9PT0gdHJ1ZSlcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQkKCdpbnB1dC5waWNrdXBfY2hlY2tib3gnKS5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XG5cdFx0XHRcdFx0XHQkKCdpbnB1dC5waWNrdXBfY2hlY2tib3gnKS5wYXJlbnQoKS5hZGRDbGFzcygnY2hlY2tlZCcpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRlbHNlXG5cdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0JCgnaW5wdXQucGlja3VwX2NoZWNrYm94JykucHJvcCgnY2hlY2tlZCcsIGZhbHNlKTtcblx0XHRcdFx0XHRcdCQoJ2lucHV0LnBpY2t1cF9jaGVja2JveCcpLnBhcmVudCgpLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdF9sYWJlbGxpc3RQaWNrdXBDaGVja2JveEhhbmRsZXIoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0KTtcblx0fTtcblxuXHRjb25zdCBfbXVsdGlQaWNrdXBTdWJtaXRIYW5kbGVyID0gX3BpY2t1cFN1Ym1pdEhhbmRsZXI7XG5cblx0Y29uc3QgX211bHRpRm9ybUdldFF1b3RlSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdGNvbnN0IGZvcm1kYXRhID0gJCgnI3NjX211bHRpX2Zvcm0nKS5zZXJpYWxpemUoKTtcblx0XHQkKCdkaXYuc2NfcXVvdGUnKS5odG1sKCcnKTtcblx0XHQkLmFqYXgoe1xuXHRcdFx0dHlwZTogICAgICdQT1NUJyxcblx0XHRcdHVybDogICAgICBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vYWRtaW4ucGhwP2RvPVNoaXBjbG91ZC9HZXRNdWx0aVNoaXBtZW50UXVvdGUnLFxuXHRcdFx0ZGF0YTogICAgIGZvcm1kYXRhLFxuXHRcdFx0ZGF0YVR5cGU6ICdqc29uJ1xuXHRcdH0pXG5cdFx0LmRvbmUoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdGlmIChkYXRhLnJlc3VsdCA9PT0gJ09LJylcblx0XHRcdHtcblx0XHRcdFx0Zm9yKGxldCBzcXVvdGUgaW4gZGF0YS5zaGlwbWVudF9xdW90ZXMpIHtcblx0XHRcdFx0XHQkKCcjc2NfbXVsdGlfcXVvdGVfJyArIGRhdGEuc2hpcG1lbnRfcXVvdGVzW3NxdW90ZV0ub3JkZXJzX2lkKVxuXHRcdFx0XHRcdFx0Lmh0bWwoZGF0YS5zaGlwbWVudF9xdW90ZXNbc3F1b3RlXS5zaGlwbWVudF9xdW90ZSk7XG5cdFx0XHRcdH1cblx0XHRcdFx0JCgnZGl2LnNjX211bHRpX3F1b3RlJykuc2hvdygnZmFzdCcpO1xuXG5cdFx0XHRcdGZvcihsZXQgY2FycmllciBpbiBkYXRhLmNhcnJpZXJzX3RvdGFsKVxuXHRcdFx0XHR7XG5cdFx0XHRcdFx0JCgnI3NjX3F1b3RlXycrY2FycmllcikuaHRtbChkYXRhLmNhcnJpZXJzX3RvdGFsW2NhcnJpZXJdKTtcblx0XHRcdFx0fVxuXHRcdFx0fVxuXHRcdH0pXG5cdFx0LmZhaWwoZnVuY3Rpb24gKGRhdGEpIHtcblx0XHRcdGFsZXJ0KGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdzdWJtaXRfZXJyb3InLCAnc2hpcGNsb3VkJykpO1xuXHRcdH0pO1xuXHRcdHJldHVybiBmYWxzZTtcblx0fTtcblxuXHQvKiA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PSAqL1xuXG5cdCQoJ2JvZHknKS5wcmVwZW5kKCQoJzxkaXYgaWQ9XCJzaGlwY2xvdWRfbW9kYWxcIiB0aXRsZT1cIicgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZShcblx0XHRcdCdjcmVhdGVfbGFiZWxfd2luZG93X3RpdGxlJywgJ3NoaXBjbG91ZCcpICtcblx0XHQnXCIgc3R5bGU9XCJkaXNwbGF5OiBub25lO1wiPjxkaXYgaWQ9XCJzY19tb2RhbF9jb250ZW50XCI+PC9kaXY+PC9kaXY+JykpO1xuXG5cdGNvbnN0ICR0YWJsZSA9ICQoJy5vcmRlcnMgLnRhYmxlLW1haW4nKTtcblxuXHQkdGFibGUub24oJ2luaXQuZHQnLCBmdW5jdGlvbigpIHtcblx0XHR2YXIgYWRkUm93QWN0aW9uID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQkdGFibGUuZmluZCgnLmJ0bi1ncm91cC5kcm9wZG93bicpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKSxcblx0XHRcdFx0XHRkZWZhdWx0Um93QWN0aW9uID0gJHRhYmxlLmRhdGEoJ2luaXQtZGVmYXVsdC1yb3ctYWN0aW9uJykgfHwgJ2VkaXQnO1xuXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xuXHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdhZG1pbl9tZW51X2VudHJ5JywgJ3NoaXBjbG91ZCcpLFxuXHRcdFx0XHRcdGhyZWY6IGBvcmRlcnMucGhwP29JRD0ke29yZGVySWR9JmFjdGlvbj1lZGl0YCxcblx0XHRcdFx0XHRjbGFzczogJ3NjLXNpbmdsZScsXG5cdFx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ3NjLXNpbmdsZSd9LFxuXHRcdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdFJvd0FjdGlvbiA9PT0gJ3NjLXNpbmdsZScsXG5cdFx0XHRcdFx0Y2FsbGJhY2s6IGZ1bmN0aW9uKGUpIHsgZS5wcmV2ZW50RGVmYXVsdCgpOyBfb3BlblNpbmdsZUZvcm1Nb2RhbChlKTsgfVxuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0JHRhYmxlLm9uKCdkcmF3LmR0JywgYWRkUm93QWN0aW9uKTtcblx0XHRhZGRSb3dBY3Rpb24oKTtcblxuXHRcdGNvbnN0ICRidWxrQWN0aW9ucyA9ICQoJy5idWxrLWFjdGlvbicpLFxuXHRcdFx0ZGVmYXVsdEJ1bGtBY3Rpb24gPSAkdGFibGUuZGF0YSgnaW5pdC1kZWZhdWx0LWJ1bGstYWN0aW9uJykgfHwgJ2VkaXQnO1xuXHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJGJ1bGtBY3Rpb25zLCB7XG5cdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnYWRtaW5fbWVudV9lbnRyeScsICdzaGlwY2xvdWQnKSxcblx0XHRcdGNsYXNzOiAnc2MtbXVsdGknLFxuXHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ3NjLW11bHRpJ30sXG5cdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRCdWxrQWN0aW9uID09PSAnc2MtbXVsdGknLFxuXHRcdFx0Y2FsbGJhY2s6IGZ1bmN0aW9uKGUpIHsgZS5wcmV2ZW50RGVmYXVsdCgpOyBfbXVsdGlEcm9wZG93bkhhbmRsZXIoZSk7IH1cblx0XHR9KTtcblx0fSk7XG5cbn0pO1xuIl19
