'use strict';

/* --------------------------------------------------------------
	intraship.js 2016-09-21
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

	$table.on('init.dt', function () {

		var _initSingleAction = function _initSingleAction($theTable) {
			$theTable.find('.btn-group.dropdown').each(function () {
				var orderId = $(this).parents('tr').data('id'),
				    defaultRowAction = $theTable.data('init-default-row-action') || 'edit';

				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('dhl_label_get', 'intraship'),
					href: jse.core.config.get('appUrl') + '/admin/print_intraship_label.php?oID=' + orderId,
					class: 'intraship-single',
					data: { configurationValue: 'intraship-single' },
					isDefault: defaultRowAction === 'intraship-single'
				});
			});
		};
		$table.on('draw.dt', function () {
			_initSingleAction($table);
		});
		_initSingleAction($table);
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImludHJhc2hpcC5qcyJdLCJuYW1lcyI6WyIkIiwiJHRhYmxlIiwib24iLCJfaW5pdFNpbmdsZUFjdGlvbiIsIiR0aGVUYWJsZSIsImZpbmQiLCJlYWNoIiwib3JkZXJJZCIsInBhcmVudHMiLCJkYXRhIiwiZGVmYXVsdFJvd0FjdGlvbiIsImpzZSIsImxpYnMiLCJidXR0b25fZHJvcGRvd24iLCJhZGRBY3Rpb24iLCJ0ZXh0IiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJocmVmIiwiY29uZmlnIiwiZ2V0IiwiY2xhc3MiLCJjb25maWd1cmF0aW9uVmFsdWUiLCJpc0RlZmF1bHQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsRUFBRSxZQUFXO0FBQ1o7O0FBRUEsS0FBTUMsU0FBU0QsRUFBRSxxQkFBRixDQUFmOztBQUVBQyxRQUFPQyxFQUFQLENBQVUsU0FBVixFQUFxQixZQUFXOztBQUUvQixNQUFNQyxvQkFBb0IsU0FBcEJBLGlCQUFvQixDQUFTQyxTQUFULEVBQW9CO0FBQzdDQSxhQUFVQyxJQUFWLENBQWUscUJBQWYsRUFBc0NDLElBQXRDLENBQTJDLFlBQVc7QUFDckQsUUFBTUMsVUFBVVAsRUFBRSxJQUFGLEVBQVFRLE9BQVIsQ0FBZ0IsSUFBaEIsRUFBc0JDLElBQXRCLENBQTJCLElBQTNCLENBQWhCO0FBQUEsUUFDQ0MsbUJBQW1CTixVQUFVSyxJQUFWLENBQWUseUJBQWYsS0FBNkMsTUFEakU7O0FBR0FFLFFBQUlDLElBQUosQ0FBU0MsZUFBVCxDQUF5QkMsU0FBekIsQ0FBbUNkLEVBQUUsSUFBRixDQUFuQyxFQUE0QztBQUMzQ2UsV0FBTUosSUFBSUssSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsZUFBeEIsRUFBeUMsV0FBekMsQ0FEcUM7QUFFM0NDLFdBQU1SLElBQUlLLElBQUosQ0FBU0ksTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0MsdUNBQWhDLEdBQTBFZCxPQUZyQztBQUczQ2UsWUFBTyxrQkFIb0M7QUFJM0NiLFdBQU0sRUFBQ2Msb0JBQW9CLGtCQUFyQixFQUpxQztBQUszQ0MsZ0JBQVdkLHFCQUFxQjtBQUxXLEtBQTVDO0FBT0EsSUFYRDtBQVlBLEdBYkQ7QUFjQVQsU0FBT0MsRUFBUCxDQUFVLFNBQVYsRUFBcUIsWUFBVztBQUFFQyxxQkFBa0JGLE1BQWxCO0FBQTJCLEdBQTdEO0FBQ0FFLG9CQUFrQkYsTUFBbEI7QUFDQSxFQWxCRDtBQW1CQSxDQXhCRCIsImZpbGUiOiJpbnRyYXNoaXAuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRpbnRyYXNoaXAuanMgMjAxNi0wOS0yMVxuXHRHYW1iaW8gR21iSFxuXHRodHRwOi8vd3d3LmdhbWJpby5kZVxuXHRDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcblx0UmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG5cdFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuXHQtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuKi9cblxuJChmdW5jdGlvbigpIHtcblx0J3VzZSBzdHJpY3QnO1xuXG5cdGNvbnN0ICR0YWJsZSA9ICQoJy5vcmRlcnMgLnRhYmxlLW1haW4nKTtcblxuXHQkdGFibGUub24oJ2luaXQuZHQnLCBmdW5jdGlvbigpIHtcblxuXHRcdGNvbnN0IF9pbml0U2luZ2xlQWN0aW9uID0gZnVuY3Rpb24oJHRoZVRhYmxlKSB7XG5cdFx0XHQkdGhlVGFibGUuZmluZCgnLmJ0bi1ncm91cC5kcm9wZG93bicpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKSxcblx0XHRcdFx0XHRkZWZhdWx0Um93QWN0aW9uID0gJHRoZVRhYmxlLmRhdGEoJ2luaXQtZGVmYXVsdC1yb3ctYWN0aW9uJykgfHwgJ2VkaXQnO1xuXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xuXHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdkaGxfbGFiZWxfZ2V0JywgJ2ludHJhc2hpcCcpLFxuXHRcdFx0XHRcdGhyZWY6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9wcmludF9pbnRyYXNoaXBfbGFiZWwucGhwP29JRD0nICsgb3JkZXJJZCxcblx0XHRcdFx0XHRjbGFzczogJ2ludHJhc2hpcC1zaW5nbGUnLFxuXHRcdFx0XHRcdGRhdGE6IHtjb25maWd1cmF0aW9uVmFsdWU6ICdpbnRyYXNoaXAtc2luZ2xlJ30sXG5cdFx0XHRcdFx0aXNEZWZhdWx0OiBkZWZhdWx0Um93QWN0aW9uID09PSAnaW50cmFzaGlwLXNpbmdsZScsXG5cdFx0XHRcdH0pO1xuXHRcdFx0fSk7XG5cdFx0fVxuXHRcdCR0YWJsZS5vbignZHJhdy5kdCcsIGZ1bmN0aW9uKCkgeyBfaW5pdFNpbmdsZUFjdGlvbigkdGFibGUpO30gKTtcblx0XHRfaW5pdFNpbmdsZUFjdGlvbigkdGFibGUpO1xuXHR9KTtcbn0pO1xuIl19
