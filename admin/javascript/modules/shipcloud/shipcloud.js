/* --------------------------------------------------------------
	shipcloud.js 2016-09-21
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function() {
	'use strict';

	const _openSingleFormModal = function(event)
	{
		const orderId = $(event.target).parents('tr').attr('id') || $('body').find('#gm_order_id').val();
		$('#sc_modal_content').empty().addClass('sc_loading');
		const button_create_label = jse.core.lang.translate('create_label', 'shipcloud');
		let shipcloud_modal_buttons = [];

		shipcloud_modal_buttons.push({
			'text':  jse.core.lang.translate('close', 'buttons'),
					'class': 'btn',
					'click': function () {
						$(this).dialog('close');
						$('#sc_get_quote').show();
					}
		});
		shipcloud_modal_buttons.push({
			'text':  jse.core.lang.translate('show_existing_labels', 'shipcloud'),
			'class': 'btn',
			'click': _showLabelsHandler,
			'id':    'sc_show_labels'
		});
		shipcloud_modal_buttons.push({
			'text':  jse.core.lang.translate('get_quotes', 'shipcloud'),
			'class': 'btn btn-primary',
			'click': _singleFormGetQuoteHandler,
			'id':    'sc_get_quote'
		});

		$('#shipcloud_modal').dialog({
			autoOpen:      false,
			modal:         true,
			'title':       jse.core.lang.translate('create_label', 'shipcloud'),
			'dialogClass': 'gx-container',
			buttons:       shipcloud_modal_buttons,
			width:         1200,
			position:      { my: 'center top', at: 'center bottom', of: '#main-header' }
		});
		$('#shipcloud_modal').dialog('open');
		//$('#sc_modal_content').html('<p>Hallo!</p>');
		$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateLabelForm&template_version=2&orders_id=' + orderId, _singleFormInit);
	};

	const _showLabelsHandler = function (e) {
		const orders_id = $('#sc_single_form input[name="orders_id"]').val();
		$('#sc_modal_content').empty().addClass('sc_loading');
		_loadLabelList(orders_id);
		$('#sc_show_labels').hide();
		$('#sc_get_quote').hide();
		return false;
	};

	const _loadLabelList = function (orders_id)
	{
		$('#sc_modal_content').load('admin.php?do=Shipcloud/LoadLabelList&orders_id=' + orders_id + '&template_version=2',
			function () {
				gx.widgets.init($('#sc_modal_content'));
				$('#shipcloud_modal').dialog({
					'title': jse.core.lang.translate('labellist_for', 'shipcloud') + ' ' + orders_id
				});
				$('#sc_modal_content').removeClass('sc_loading');

				$('form#sc_pickup').on('submit', function(e) { e.preventDefault(); });
				$('#download_labels').on('click', _packedDownloadHandler);
				$('#order_pickups').on('click', _pickupSubmitHandler);
				$('input.pickup_checkbox').on('click', _labellistPickupCheckboxHandler);
				setTimeout(_labellistPickupCheckboxHandler, 200);
				$('input.pickup_checkbox_all').on('click', function()
				{
					if ($(this).prop('checked') === true)
					{
						$('input.pickup_checkbox').prop('checked', true);
						$('input.pickup_checkbox').parent().addClass('checked');
					}
					else
					{
						$('input.pickup_checkbox').prop('checked', false);
						$('input.pickup_checkbox').parent().removeClass('checked');
					}
					_labellistPickupCheckboxHandler();
				});
				$('a.sc-del-label').on('click', function(e) {
					e.preventDefault();
					const shipment_id  = $(this).data('shipment-id'),
					      $row         = $(this).closest('tr');
					$.ajax({
						type:     'POST',
						url:       jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/DeleteShipment',
						data:     { shipment_id: shipment_id },
						dataType: 'json'
					})
					.done(function(data) {
						if(data.result === 'ERROR')
						{
							$('#status-output').html(data.error_message).show();
							$('#status-output').addClass('alert alert-danger');
						}
						else
						{
							$('#status-output')
								.html(jse.core.lang.translate('shipment_deleted', 'shipcloud'))
								.removeClass()
								.addClass('alert alert-info')
								.show();
							$('a, input, td.checkbox > *', $row).remove();
							$row.addClass('deleted-shipment');
						}
					})
					.fail(function(data) {
						$buttonPlace.html(jse.core.lang.translate('submit_error', 'shipcloud'));
					});
				});
			});
	};

	const _packedDownloadHandler = function(e)
	{
		e.preventDefault();
		let urls = [], request = {};
		$('input.pickup_checkbox:checked').each(function() {
			const href = $('a.label-link', $(this).closest('tr')).attr('href');
			urls.push(href);
		});
		if (urls)
		{
			$('#download_result').show();
			$('#download_result').html(jse.core.lang.translate('loading', 'shipcloud'));
			request.urls       = urls;
			request.page_token = $('#sc_modal_content input[name="page_token"]').val();

			$.ajax({
				type:     'POST',
				url:       jse.core.config.get('appUrl') + '/admin/admin.php?do=PackedDownload/DownloadByJson',
				data:     JSON.stringify(request),
				dataType: 'json'
			})
			.done(function(data) {
				const downloadlink =
					  jse.core.config.get('appUrl') +
					  '/admin/admin.php?do=PackedDownload/DownloadPackage&key=' +
					  data.downloadKey;
				if (data.result === 'OK')
				{
					$('#download_result').html('<iframe class="download_iframe" src="'+downloadlink+'"></iframe>');
				}
				if (data.result === 'ERROR')
				{
					$('#download_result').html(data.error_message);
				}
			})
			.fail(function(data) {
				$('#download_result').html(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
		}
		return true;
	};

	const _pickupSubmitHandler = function(e) {
		e.preventDefault();
		if ($('input.pickup_checkbox:checked').length > 0)
		{
			const formdata = $('form#sc_pickup').serialize();
			$('#pickup_result').html(jse.core.lang.translate('sending_pickup_request', 'shipcloud'));
			$('#pickup_result').show();
			$('#pickup_result').addClass('alert alert-warning');
			$.ajax({
				type:     'POST',
				url:      jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/PickupShipments',
				data:     formdata,
				dataType: 'json'
			})
			.done(function(data) {
				let result_message = '';
				data.result_messages.forEach(function(message) { result_message = result_message + message + '<br>'; });
				$('#pickup_result').html(result_message);
			})
			.fail(function(data) {
				alert(jse.core.lang.translate('submit_error', 'shipcloud'));
			});
		}
		return true;
	};

	const _labellistPickupCheckboxHandler = function() {
		$('#sc-labellist-dropdown button, div.pickup_time input')
			.prop('disabled', $('input.pickup_checkbox:checked').length === 0);
	};

	const _loadUnconfiguredNote = function ()
	{
		$('#sc_modal_content').load('admin.php?do=Shipcloud/UnconfiguredNote');
	};


	const _singleFormGetQuoteHandler = function()
	{
		const $form = $('#sc_single_form');
		let   quote = '';

		$('#sc_single_form .sc_quote').html('');
		$('#sc_single_form .sc_quote').attr('title', '');

		$('input[name="quote_carriers[]"]:checked').each(function() {
			const carrier = $(this).val(),
			      $create_label = $('input.create_label', $(this).closest('tr'));
			$('input[name="carrier"]', $form).val(carrier);
			$('#sc_quote_'+carrier).html(jse.core.lang.translate('loading', 'shipcloud'));
			$.ajax({
				type:     'POST',
				url:      jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/GetShipmentQuote',
				data:     $form.serialize(),
				dataType: 'json'
			})
			.done(function (data) {
				if (data.result === 'OK')
				{
					quote = data.shipment_quote;
					$('#sc_quote_'+carrier).html(quote);
				}
				else if (data.result === 'ERROR')
				{
					$('#sc_quote_'+carrier).html(jse.core.lang.translate('not_possible', 'shipcloud'));
					$('#sc_quote_'+carrier).attr('title', data.error_message);
				}
				else if (data.result === 'UNCONFIGURED')
				{
					_loadUnconfiguredNote();
				}
			})
			.fail(function (data) {
				quote = jse.core.lang.translate('get_quote_error', 'shipcloud');
				$('#sc_quote_'+carrier).html(quote);
			});
		});

		$('input[name="carrier"]', $form).val('');
	};

	const _singleFormInit = function() {
		gx.widgets.init($('#shipcloud_modal'));
		$('#sc_modal_content').removeClass('sc_loading');
		if ($('#sc_single_container').data('is_configured') === 1)
		{
			$('#sc_show_labels').show();
		}
		else
		{
			$('#sc_show_labels').hide();
		}
		$('#sc_single_form').on('submit', function(e) { e.preventDefault(); });
		$('#sc_single_form input.create_label').on('click', _singleFormSubmitHandler);
		$('#sc_single_form select[name="carrier"]').on('change', function(e) {
			$('#sc_single_form input[type="text"]').trigger('change');
			$('#sc_single_form .carrier-specific').not('.carrier-'+$(this).val()).hide('fast');
			$('#sc_single_form .carrier-'+$(this).val()).not(':visible').show('fast');
		});
		$('#sc_single_form .price_value').on('change', function() {
			$('#sc_single_form div.sc_quote').html('');
		});
		$('#sc_package_template').on('change', _templateSelectionHandler);
		$('#sc_single_form input.template_value').on('change', function() { $('#sc_package_template').val('-1'); });
		$('#sc_get_quote').button('disable');
		$('#sc_single_form input[name="quote_carriers[]"]').on('change', function() {
			if ($('#sc_single_form input[name="quote_carriers[]"]:checked').length > 0)
			{
				$('#sc_get_quote').button('enable');
			}
			else
			{
				$('#sc_get_quote').button('disable');
			}
		});
		$('#sc_single_form input[name="quote_carriers[]"]:first').trigger('change');
	};

	const _templateSelectionHandler = function(e) {
		const $form     = $(this).closest('form'),
		      $template = $('option:selected', $(this));
		if ($template.val() !== '-1')
		{
			$('input[name="package[weight]"]', $form).val($template.data('weight'));
			$('input[name="package[height]"]', $form).val($template.data('height'));
			$('input[name="package[width]"]',  $form).val($template.data('width'));
			$('input[name="package[length]"]', $form).val($template.data('length'));
		}
	};

	const _singleFormSubmitHandler = function (e) {
		$('#sc_show_labels').hide();
		$('#sc_get_quote').hide();
		const carrier = $(this).attr('name');
		$('input[name="carrier"]').val(carrier);
		const formdata = $('#sc_single_form').serialize();
		$('#sc_modal_content').empty().addClass('sc_loading');
		// alert('data: '+formdata);
		$.ajax({
			type:     'POST',
			url:      jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/CreateLabelFormSubmit',
			data:     formdata,
			dataType: 'json'
		})
		.done(function (data) {
			$('#sc_modal_content').removeClass('sc_loading');
			if (data.result === 'UNCONFIGURED')
			{
				_loadUnconfiguredNote();
			}
			else if (data.result === 'OK')
			{
				_loadLabelList(data.orders_id);
			}
			else
			{
				if (data.error_message)
				{
					$('#sc_modal_content').html('<div class="sc_error">'+data.error_message+'</div>');
				}
			}

			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');
		})
		.fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	/* - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - */

	const _multiDropdownHandler = function(e)
	{
		let selected_orders = [], orders_param = '';
		$('table.table tbody tr').each(function() {
			const order_id = $(this).attr('id'),
			      $checkbox = $('td:nth-child(1) span.single-checkbox', this);
			if($checkbox.hasClass('checked'))
			{
				selected_orders.push(order_id);
			}
		});
		$('#sc_modal_content').empty().addClass('sc_loading');
		let shipcloud_modal_buttons = [];
		shipcloud_modal_buttons.push({
			'text':  jse.core.lang.translate('get_quotes', 'shipcloud'),
			'class': 'btn btn-primary',
			'click': _multiFormGetQuoteHandler,
			'id':    'sc_get_quote'
		});
		shipcloud_modal_buttons.push({
			'text':  jse.core.lang.translate('close', 'buttons'),
			'class': 'btn',
			'click': function () {
				$(this).dialog('close');
				$('#sc_get_quote').show();
			}
		});

		$('#shipcloud_modal').dialog({
			autoOpen:      false,
			modal:         true,
			'title':       jse.core.lang.translate('create_labels', 'shipcloud'),
			'dialogClass': 'gx-container',
			buttons:       shipcloud_modal_buttons,
			width:         1200,
			position:      { my: 'center top', at: 'center bottom', of: '#main-header' }
		});

		$('#shipcloud_modal').dialog('open');
		selected_orders.forEach(function(item) {
			orders_param += 'orders[]='+item+'&';
		});
		$('#sc_modal_content').load('admin.php?do=Shipcloud/CreateMultiLabelForm&template_version=2&'+orders_param, _multiFormInit);
	};

	const _multiFormInit = function() {
		gx.widgets.init($('#shipcloud_modal'));
		$('#shipcloud_modal').dialog({
			'title': jse.core.lang.translate('create_labels', 'shipcloud')
		});
		$('#sc_modal_content').removeClass('sc_loading');
		$('#sc_multi_form').on('submit', function(e) { e.preventDefault(); return false; });
		$('#sc_create_label').hide();
		$('#sc_show_labels').hide();
		$('#sc_modal_content input, #sc_modal_content select').on('change', function() {
			$('.sc_multi_quote').hide();
		});
		$('#sc_package_template').on('change', _templateSelectionHandler);
		$('input.create_label').on('click', _multiFormSubmitHandler);
	};

	const _multiFormSubmitHandler = function(event) {
		const carrier = $(this).attr('name');
		$('#sc_multi_form input[name="carrier"]').val(carrier);
		const formdata = $('#sc_multi_form').serialize();
		$('#sc_modal_content').empty().addClass('sc_loading');
		$.ajax({
			type:     'POST',
			url:      jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/CreateMultiLabelFormSubmit',
			data:     formdata,
			dataType: 'json'
		})
		.done(function (data) {
			$('#sc_modal_content').removeClass('sc_loading');
			if (data.result === 'UNCONFIGURED')
			{
				_loadUnconfiguredNote();
			}
			else if (data.result === 'OK')
			{
				_loadMultiLabelList(data.orders_ids, data.shipments);
			}
			else
			{
				if (data.error_message)
				{
					$('#sc_modal_content').html('<div class="sc_error">'+data.error_message+'</div>');
				}
			}

			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');
		})
		.fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	const _loadMultiLabelList = function(orders_ids, shipments)
	{
		const multiLabelListParams = { 'orders_ids': orders_ids, 'shipments': shipments };

		$('#sc_modal_content').load(
			jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/LoadMultiLabelList&template_version=2',
			{ "json": JSON.stringify(multiLabelListParams) },
			function () {
				gx.widgets.init($('#shipcloud_modal'));
				$('#shipcloud_modal').dialog({
					'title': jse.core.lang.translate('labellist', 'shipcloud')
				});
				$('#sc_modal_content').removeClass('sc_loading');
				$('#sc_get_quote').hide();

				$('form#sc_pickup').on('submit', function(e) { e.preventDefault(); });
				$('#download_labels').on('click', _packedDownloadHandler);
				$('#order_pickups').on('click', _pickupSubmitHandler);
				$('input.pickup_checkbox').on('click', _labellistPickupCheckboxHandler);
				setTimeout(_labellistPickupCheckboxHandler, 200);
				$('input.pickup_checkbox_all').on('click', function()
				{
					if ($(this).prop('checked') === true)
					{
						$('input.pickup_checkbox').prop('checked', true);
						$('input.pickup_checkbox').parent().addClass('checked');
					}
					else
					{
						$('input.pickup_checkbox').prop('checked', false);
						$('input.pickup_checkbox').parent().removeClass('checked');
					}
					_labellistPickupCheckboxHandler();
				});
			}
		);
	};

	const _multiPickupSubmitHandler = _pickupSubmitHandler;

	const _multiFormGetQuoteHandler = function() {
		const formdata = $('#sc_multi_form').serialize();
		$('div.sc_quote').html('');
		$.ajax({
			type:     'POST',
			url:      jse.core.config.get('appUrl') + '/admin/admin.php?do=Shipcloud/GetMultiShipmentQuote',
			data:     formdata,
			dataType: 'json'
		})
		.done(function (data) {
			if (data.result === 'OK')
			{
				for(let squote in data.shipment_quotes) {
					$('#sc_multi_quote_' + data.shipment_quotes[squote].orders_id)
						.html(data.shipment_quotes[squote].shipment_quote);
				}
				$('div.sc_multi_quote').show('fast');

				for(let carrier in data.carriers_total)
				{
					$('#sc_quote_'+carrier).html(data.carriers_total[carrier]);
				}
			}
		})
		.fail(function (data) {
			alert(jse.core.lang.translate('submit_error', 'shipcloud'));
		});
		return false;
	};

	/* =========================================================================================================== */

	$('body').prepend($('<div id="shipcloud_modal" title="' + jse.core.lang.translate(
			'create_label_window_title', 'shipcloud') +
		'" style="display: none;"><div id="sc_modal_content"></div></div>'));

	const $table = $('.orders .table-main');

	$table.on('init.dt', function() {
		var addRowAction = function() {
			$table.find('.btn-group.dropdown').each(function() {
				const orderId = $(this).parents('tr').data('id'),
					defaultRowAction = $table.data('init-default-row-action') || 'edit';

				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('admin_menu_entry', 'shipcloud'),
					href: `orders.php?oID=${orderId}&action=edit`,
					class: 'sc-single',
					data: {configurationValue: 'sc-single'},
					isDefault: defaultRowAction === 'sc-single',
					callback: function(e) { e.preventDefault(); _openSingleFormModal(e); }
				});
			});
		};
		$table.on('draw.dt', addRowAction);
		addRowAction();

		const $bulkActions = $('.bulk-action'),
			defaultBulkAction = $table.data('init-default-bulk-action') || 'edit';
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('admin_menu_entry', 'shipcloud'),
			class: 'sc-multi',
			data: {configurationValue: 'sc-multi'},
			isDefault: defaultBulkAction === 'sc-multi',
			callback: function(e) { e.preventDefault(); _multiDropdownHandler(e); }
		});
	});

});
