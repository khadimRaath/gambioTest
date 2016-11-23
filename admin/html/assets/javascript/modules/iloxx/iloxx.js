'use strict';

/* --------------------------------------------------------------
	iloxx.js 2016-06-20
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2015 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function () {
	'use strict';

	var $table = $('.orders .table-main');

	var _iloxxBulkActionDropdownHandler = function _iloxxBulkActionDropdownHandler(e) {
		var selected_orders = [],
		    orders_param = '',
		    redirect_url = '';
		$('table.table tbody tr').each(function () {
			var order_id = $(this).attr('id'),
			    $checkbox = $('td:nth-child(1) span.single-checkbox', this);
			if ($checkbox.hasClass('checked')) {
				selected_orders.push(order_id);
				orders_param += '&orders_id[]=' + order_id;
			}
		});

		redirect_url = jse.core.config.get('appUrl') + '/admin/orders_iloxx.php?' + orders_param;
		document.location = redirect_url;
	};

	var _initBulkAction = function _initBulkAction() {
		var $bulkActions = $('.bulk-action'),
		    defaultBulkAction = $table.data('init-default-bulk-action') || 'edit';
		jse.libs.button_dropdown.addAction($bulkActions, {
			text: jse.core.lang.translate('get_labels', 'iloxx'),
			class: 'iloxx-multi',
			data: { configurationValue: 'iloxx-multi' },
			isDefault: defaultBulkAction === 'iloxx-multi',
			callback: function callback(e) {
				e.preventDefault();_iloxxBulkActionDropdownHandler(e);
			}
		});
	};

	var _initSingleAction = function _initSingleAction() {
		$table.find('.btn-group.dropdown').each(function () {
			var orderId = $(this).parents('tr').data('id'),
			    defaultRowAction = $table.data('init-default-row-action') || 'edit';

			jse.libs.button_dropdown.addAction($(this), {
				text: jse.core.lang.translate('get_labels', 'iloxx'),
				href: jse.core.config.get('appUrl') + '/admin/orders_iloxx.php?oID=' + orderId,
				class: 'iloxx-single',
				data: { configurationValue: 'iloxx-single' },
				isDefault: defaultRowAction === 'iloxx-single'
			});
		});
	};

	$table.on('init.dt', function () {
		_initSingleAction();
		_initBulkAction();
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImlsb3h4LmpzIl0sIm5hbWVzIjpbIiQiLCIkdGFibGUiLCJfaWxveHhCdWxrQWN0aW9uRHJvcGRvd25IYW5kbGVyIiwiZSIsInNlbGVjdGVkX29yZGVycyIsIm9yZGVyc19wYXJhbSIsInJlZGlyZWN0X3VybCIsImVhY2giLCJvcmRlcl9pZCIsImF0dHIiLCIkY2hlY2tib3giLCJoYXNDbGFzcyIsInB1c2giLCJqc2UiLCJjb3JlIiwiY29uZmlnIiwiZ2V0IiwiZG9jdW1lbnQiLCJsb2NhdGlvbiIsIl9pbml0QnVsa0FjdGlvbiIsIiRidWxrQWN0aW9ucyIsImRlZmF1bHRCdWxrQWN0aW9uIiwiZGF0YSIsImxpYnMiLCJidXR0b25fZHJvcGRvd24iLCJhZGRBY3Rpb24iLCJ0ZXh0IiwibGFuZyIsInRyYW5zbGF0ZSIsImNsYXNzIiwiY29uZmlndXJhdGlvblZhbHVlIiwiaXNEZWZhdWx0IiwiY2FsbGJhY2siLCJwcmV2ZW50RGVmYXVsdCIsIl9pbml0U2luZ2xlQWN0aW9uIiwiZmluZCIsIm9yZGVySWQiLCJwYXJlbnRzIiwiZGVmYXVsdFJvd0FjdGlvbiIsImhyZWYiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxFQUFFLFlBQVc7QUFDWjs7QUFFQSxLQUFNQyxTQUFTRCxFQUFFLHFCQUFGLENBQWY7O0FBRUEsS0FBTUUsa0NBQWtDLFNBQWxDQSwrQkFBa0MsQ0FBU0MsQ0FBVCxFQUFZO0FBQ25ELE1BQUlDLGtCQUFrQixFQUF0QjtBQUFBLE1BQTBCQyxlQUFlLEVBQXpDO0FBQUEsTUFBNkNDLGVBQWUsRUFBNUQ7QUFDQU4sSUFBRSxzQkFBRixFQUEwQk8sSUFBMUIsQ0FBK0IsWUFBVztBQUN6QyxPQUFJQyxXQUFXUixFQUFFLElBQUYsRUFBUVMsSUFBUixDQUFhLElBQWIsQ0FBZjtBQUFBLE9BQ0lDLFlBQVlWLEVBQUUsc0NBQUYsRUFBMEMsSUFBMUMsQ0FEaEI7QUFFQSxPQUFHVSxVQUFVQyxRQUFWLENBQW1CLFNBQW5CLENBQUgsRUFDQTtBQUNDUCxvQkFBZ0JRLElBQWhCLENBQXFCSixRQUFyQjtBQUNBSCxvQkFBZ0Isa0JBQWtCRyxRQUFsQztBQUNBO0FBQ0QsR0FSRDs7QUFVQUYsaUJBQWVPLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsMEJBQWhDLEdBQTZEWCxZQUE1RTtBQUNBWSxXQUFTQyxRQUFULEdBQW9CWixZQUFwQjtBQUNBLEVBZEQ7O0FBZ0JBLEtBQU1hLGtCQUFrQixTQUFsQkEsZUFBa0IsR0FBVztBQUNsQyxNQUFNQyxlQUFlcEIsRUFBRSxjQUFGLENBQXJCO0FBQUEsTUFDQ3FCLG9CQUFvQnBCLE9BQU9xQixJQUFQLENBQVksMEJBQVosS0FBMkMsTUFEaEU7QUFFQVQsTUFBSVUsSUFBSixDQUFTQyxlQUFULENBQXlCQyxTQUF6QixDQUFtQ0wsWUFBbkMsRUFBaUQ7QUFDaERNLFNBQU1iLElBQUlDLElBQUosQ0FBU2EsSUFBVCxDQUFjQyxTQUFkLENBQXdCLFlBQXhCLEVBQXNDLE9BQXRDLENBRDBDO0FBRWhEQyxVQUFPLGFBRnlDO0FBR2hEUCxTQUFNLEVBQUNRLG9CQUFvQixhQUFyQixFQUgwQztBQUloREMsY0FBV1Ysc0JBQXNCLGFBSmU7QUFLaERXLGFBQVUsa0JBQVM3QixDQUFULEVBQVk7QUFBRUEsTUFBRThCLGNBQUYsR0FBb0IvQixnQ0FBZ0NDLENBQWhDO0FBQXFDO0FBTGpDLEdBQWpEO0FBT0EsRUFWRDs7QUFZQSxLQUFNK0Isb0JBQW9CLFNBQXBCQSxpQkFBb0IsR0FBVztBQUNwQ2pDLFNBQU9rQyxJQUFQLENBQVkscUJBQVosRUFBbUM1QixJQUFuQyxDQUF3QyxZQUFXO0FBQ2xELE9BQU02QixVQUFVcEMsRUFBRSxJQUFGLEVBQVFxQyxPQUFSLENBQWdCLElBQWhCLEVBQXNCZixJQUF0QixDQUEyQixJQUEzQixDQUFoQjtBQUFBLE9BQ0NnQixtQkFBbUJyQyxPQUFPcUIsSUFBUCxDQUFZLHlCQUFaLEtBQTBDLE1BRDlEOztBQUdBVCxPQUFJVSxJQUFKLENBQVNDLGVBQVQsQ0FBeUJDLFNBQXpCLENBQW1DekIsRUFBRSxJQUFGLENBQW5DLEVBQTRDO0FBQzNDMEIsVUFBTWIsSUFBSUMsSUFBSixDQUFTYSxJQUFULENBQWNDLFNBQWQsQ0FBd0IsWUFBeEIsRUFBc0MsT0FBdEMsQ0FEcUM7QUFFM0NXLFVBQU0xQixJQUFJQyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFFBQXBCLElBQWdDLDhCQUFoQyxHQUFpRW9CLE9BRjVCO0FBRzNDUCxXQUFPLGNBSG9DO0FBSTNDUCxVQUFNLEVBQUNRLG9CQUFvQixjQUFyQixFQUpxQztBQUszQ0MsZUFBV08scUJBQXFCO0FBTFcsSUFBNUM7QUFPQSxHQVhEO0FBWUEsRUFiRDs7QUFlQXJDLFFBQU91QyxFQUFQLENBQVUsU0FBVixFQUFxQixZQUFXO0FBQy9CTjtBQUNBZjtBQUNBLEVBSEQ7QUFJQSxDQXBERCIsImZpbGUiOiJpbG94eC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdGlsb3h4LmpzIDIwMTYtMDYtMjBcblx0R2FtYmlvIEdtYkhcblx0aHR0cDovL3d3dy5nYW1iaW8uZGVcblx0Q29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG5cdFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuXHRbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cblx0LS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiovXG5cbiQoZnVuY3Rpb24oKSB7XG5cdCd1c2Ugc3RyaWN0JztcblxuXHRjb25zdCAkdGFibGUgPSAkKCcub3JkZXJzIC50YWJsZS1tYWluJyk7XG5cblx0Y29uc3QgX2lsb3h4QnVsa0FjdGlvbkRyb3Bkb3duSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRsZXQgc2VsZWN0ZWRfb3JkZXJzID0gW10sIG9yZGVyc19wYXJhbSA9ICcnLCByZWRpcmVjdF91cmwgPSAnJztcblx0XHQkKCd0YWJsZS50YWJsZSB0Ym9keSB0cicpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRsZXQgb3JkZXJfaWQgPSAkKHRoaXMpLmF0dHIoJ2lkJyksXG5cdFx0XHQgICAgJGNoZWNrYm94ID0gJCgndGQ6bnRoLWNoaWxkKDEpIHNwYW4uc2luZ2xlLWNoZWNrYm94JywgdGhpcyk7XG5cdFx0XHRpZigkY2hlY2tib3guaGFzQ2xhc3MoJ2NoZWNrZWQnKSlcblx0XHRcdHtcblx0XHRcdFx0c2VsZWN0ZWRfb3JkZXJzLnB1c2gob3JkZXJfaWQpO1xuXHRcdFx0XHRvcmRlcnNfcGFyYW0gKz0gJyZvcmRlcnNfaWRbXT0nICsgb3JkZXJfaWQ7XG5cdFx0XHR9XG5cdFx0fSk7XG5cblx0XHRyZWRpcmVjdF91cmwgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArICcvYWRtaW4vb3JkZXJzX2lsb3h4LnBocD8nICsgb3JkZXJzX3BhcmFtO1xuXHRcdGRvY3VtZW50LmxvY2F0aW9uID0gcmVkaXJlY3RfdXJsO1xuXHR9O1xuXG5cdGNvbnN0IF9pbml0QnVsa0FjdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdGNvbnN0ICRidWxrQWN0aW9ucyA9ICQoJy5idWxrLWFjdGlvbicpLFxuXHRcdFx0ZGVmYXVsdEJ1bGtBY3Rpb24gPSAkdGFibGUuZGF0YSgnaW5pdC1kZWZhdWx0LWJ1bGstYWN0aW9uJykgfHwgJ2VkaXQnO1xuXHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJGJ1bGtBY3Rpb25zLCB7XG5cdFx0XHR0ZXh0OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZ2V0X2xhYmVscycsICdpbG94eCcpLFxuXHRcdFx0Y2xhc3M6ICdpbG94eC1tdWx0aScsXG5cdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnaWxveHgtbXVsdGknfSxcblx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdEJ1bGtBY3Rpb24gPT09ICdpbG94eC1tdWx0aScsXG5cdFx0XHRjYWxsYmFjazogZnVuY3Rpb24oZSkgeyBlLnByZXZlbnREZWZhdWx0KCk7IF9pbG94eEJ1bGtBY3Rpb25Ecm9wZG93bkhhbmRsZXIoZSk7IH1cblx0XHR9KTtcblx0fTtcblxuXHRjb25zdCBfaW5pdFNpbmdsZUFjdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdCR0YWJsZS5maW5kKCcuYnRuLWdyb3VwLmRyb3Bkb3duJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKSxcblx0XHRcdFx0ZGVmYXVsdFJvd0FjdGlvbiA9ICR0YWJsZS5kYXRhKCdpbml0LWRlZmF1bHQtcm93LWFjdGlvbicpIHx8ICdlZGl0JztcblxuXHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XG5cdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdnZXRfbGFiZWxzJywgJ2lsb3h4JyksXG5cdFx0XHRcdGhyZWY6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9vcmRlcnNfaWxveHgucGhwP29JRD0nICsgb3JkZXJJZCxcblx0XHRcdFx0Y2xhc3M6ICdpbG94eC1zaW5nbGUnLFxuXHRcdFx0XHRkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnaWxveHgtc2luZ2xlJ30sXG5cdFx0XHRcdGlzRGVmYXVsdDogZGVmYXVsdFJvd0FjdGlvbiA9PT0gJ2lsb3h4LXNpbmdsZScsXG5cdFx0XHR9KTtcblx0XHR9KTtcblx0fVxuXG5cdCR0YWJsZS5vbignaW5pdC5kdCcsIGZ1bmN0aW9uKCkge1xuXHRcdF9pbml0U2luZ2xlQWN0aW9uKCk7XG5cdFx0X2luaXRCdWxrQWN0aW9uKCk7XG5cdH0pO1xufSk7XG4iXX0=
