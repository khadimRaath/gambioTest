/* --------------------------------------------------------------
	hermes.js 2016-06-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function() {
	'use strict';

	const $table = $('.orders .table-main');

	$table.on('init.dt', function() {
		const _initSingleAction = function($table) {
			$table.find('.btn-group.dropdown').each(function() {
				const orderId = $(this).parents('tr').data('id');
				const defaultRowAction = $table.data('defaultRowAction') || 'edit';

				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('hermes_shipping', 'hermes'),
					href: jse.core.config.get('appUrl') + '/admin/hermes_order.php?orders_id=' + orderId,
					class: 'hermes-single',
					data: {configurationValue: 'hermes-single'},
					isDefault: defaultRowAction === 'hermes-single'
				});
			});
		};

		$table.on('draw.dt', function() {
			_initSingleAction($table);
		});

		_initSingleAction($table);
	}) ;

});
