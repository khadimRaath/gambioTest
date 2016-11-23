/* --------------------------------------------------------------
	iloxx.js 2016-06-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function() {
	'use strict';

	const $table = $('.orders .table-main');

	const _iloxxBulkActionDropdownHandler = function(e) {
		let selected_orders = [], orders_param = '', redirect_url = '';
		$('table.table tbody tr').each(function() {
			let order_id = $(this).attr('id'),
			    $checkbox = $('td:nth-child(1) span.single-checkbox', this);
			if($checkbox.hasClass('checked'))
			{
				selected_orders.push(order_id);
				orders_param += '&orders_id[]=' + order_id;
			}
		});

		redirect_url = jse.core.config.get('appUrl') + '/admin/orders_iloxx.php?' + orders_param;
		document.location = redirect_url;
	};

	const _initBulkAction = function() {
		const $bulkActions = $('.bulk-action'),
			defaultBulkAction = $table.data('init-default-bulk-action') || 'edit';
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('get_labels', 'iloxx'),
			class: 'iloxx-multi',
			data: {configurationValue: 'iloxx-multi'},
			isDefault: defaultBulkAction === 'iloxx-multi',
			callback: function(e) { e.preventDefault(); _iloxxBulkActionDropdownHandler(e); }
		});
	};

	const _initSingleAction = function() {
		$table.find('.btn-group.dropdown').each(function() {
			const orderId = $(this).parents('tr').data('id'),
				defaultRowAction = $table.data('init-default-row-action') || 'edit';

			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('get_labels', 'iloxx'),
				href: jse.core.config.get('appUrl') + '/admin/orders_iloxx.php?oID=' + orderId,
				class: 'iloxx-single',
				data: {configurationValue: 'iloxx-single'},
				isDefault: defaultRowAction === 'iloxx-single',
			});
		});
	}

	$table.on('init.dt', function() {
		_initSingleAction();
		_initBulkAction();
	});
});
