/* --------------------------------------------------------------
	intraship.js 2016-09-21
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

	$table.on('init.dt', function() {

		const _initSingleAction = function($theTable) {
			$theTable.find('.btn-group.dropdown').each(function() {
				const orderId = $(this).parents('tr').data('id'),
					defaultRowAction = $theTable.data('init-default-row-action') || 'edit';

				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('dhl_label_get', 'intraship'),
					href: jse.core.config.get('appUrl') + '/admin/print_intraship_label.php?oID=' + orderId,
					class: 'intraship-single',
					data: {configurationValue: 'intraship-single'},
					isDefault: defaultRowAction === 'intraship-single',
				});
			});
		}
		$table.on('draw.dt', function() { _initSingleAction($table);} );
		_initSingleAction($table);
	});
});
