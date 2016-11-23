'use strict';

/* --------------------------------------------------------------
	hermes.js 2016-06-16
	Gambio GmbH
	http://www.gambio.de
	Copyright (c) 2016 Gambio GmbH
	Released under the GNU General Public License (Version 2)
	[http://www.gnu.org/licenses/gpl-2.0.html]
	--------------------------------------------------------------
*/

$(function () {
	'use strict';

	var $table = $('.orders .table-main');

	$table.on('init.dt', function () {
		var _initSingleAction = function _initSingleAction($table) {
			$table.find('.btn-group.dropdown').each(function () {
				var orderId = $(this).parents('tr').data('id');
				var defaultRowAction = $table.data('defaultRowAction') || 'edit';

				jse.libs.button_dropdown.addAction($(this), {
					text: jse.core.lang.translate('hermes_shipping', 'hermes'),
					href: jse.core.config.get('appUrl') + '/admin/hermes_order.php?orders_id=' + orderId,
					class: 'hermes-single',
					data: { configurationValue: 'hermes-single' },
					isDefault: defaultRowAction === 'hermes-single'
				});
			});
		};

		$table.on('draw.dt', function () {
			_initSingleAction($table);
		});

		_initSingleAction($table);
	});
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImhlcm1lcy5qcyJdLCJuYW1lcyI6WyIkIiwiJHRhYmxlIiwib24iLCJfaW5pdFNpbmdsZUFjdGlvbiIsImZpbmQiLCJlYWNoIiwib3JkZXJJZCIsInBhcmVudHMiLCJkYXRhIiwiZGVmYXVsdFJvd0FjdGlvbiIsImpzZSIsImxpYnMiLCJidXR0b25fZHJvcGRvd24iLCJhZGRBY3Rpb24iLCJ0ZXh0IiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJocmVmIiwiY29uZmlnIiwiZ2V0IiwiY2xhc3MiLCJjb25maWd1cmF0aW9uVmFsdWUiLCJpc0RlZmF1bHQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsRUFBRSxZQUFXO0FBQ1o7O0FBRUEsS0FBTUMsU0FBU0QsRUFBRSxxQkFBRixDQUFmOztBQUVBQyxRQUFPQyxFQUFQLENBQVUsU0FBVixFQUFxQixZQUFXO0FBQy9CLE1BQU1DLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQVNGLE1BQVQsRUFBaUI7QUFDMUNBLFVBQU9HLElBQVAsQ0FBWSxxQkFBWixFQUFtQ0MsSUFBbkMsQ0FBd0MsWUFBVztBQUNsRCxRQUFNQyxVQUFVTixFQUFFLElBQUYsRUFBUU8sT0FBUixDQUFnQixJQUFoQixFQUFzQkMsSUFBdEIsQ0FBMkIsSUFBM0IsQ0FBaEI7QUFDQSxRQUFNQyxtQkFBbUJSLE9BQU9PLElBQVAsQ0FBWSxrQkFBWixLQUFtQyxNQUE1RDs7QUFFQUUsUUFBSUMsSUFBSixDQUFTQyxlQUFULENBQXlCQyxTQUF6QixDQUFtQ2IsRUFBRSxJQUFGLENBQW5DLEVBQTRDO0FBQzNDYyxXQUFNSixJQUFJSyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixpQkFBeEIsRUFBMkMsUUFBM0MsQ0FEcUM7QUFFM0NDLFdBQU1SLElBQUlLLElBQUosQ0FBU0ksTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0Msb0NBQWhDLEdBQXVFZCxPQUZsQztBQUczQ2UsWUFBTyxlQUhvQztBQUkzQ2IsV0FBTSxFQUFDYyxvQkFBb0IsZUFBckIsRUFKcUM7QUFLM0NDLGdCQUFXZCxxQkFBcUI7QUFMVyxLQUE1QztBQU9BLElBWEQ7QUFZQSxHQWJEOztBQWVBUixTQUFPQyxFQUFQLENBQVUsU0FBVixFQUFxQixZQUFXO0FBQy9CQyxxQkFBa0JGLE1BQWxCO0FBQ0EsR0FGRDs7QUFJQUUsb0JBQWtCRixNQUFsQjtBQUNBLEVBckJEO0FBdUJBLENBNUJEIiwiZmlsZSI6Imhlcm1lcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdGhlcm1lcy5qcyAyMDE2LTA2LTE2XG5cdEdhbWJpbyBHbWJIXG5cdGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG5cdENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuXHRSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcblx0W2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG5cdC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4qL1xuXG4kKGZ1bmN0aW9uKCkge1xuXHQndXNlIHN0cmljdCc7XG5cblx0Y29uc3QgJHRhYmxlID0gJCgnLm9yZGVycyAudGFibGUtbWFpbicpO1xuXG5cdCR0YWJsZS5vbignaW5pdC5kdCcsIGZ1bmN0aW9uKCkge1xuXHRcdGNvbnN0IF9pbml0U2luZ2xlQWN0aW9uID0gZnVuY3Rpb24oJHRhYmxlKSB7XG5cdFx0XHQkdGFibGUuZmluZCgnLmJ0bi1ncm91cC5kcm9wZG93bicpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGNvbnN0IG9yZGVySWQgPSAkKHRoaXMpLnBhcmVudHMoJ3RyJykuZGF0YSgnaWQnKTtcblx0XHRcdFx0Y29uc3QgZGVmYXVsdFJvd0FjdGlvbiA9ICR0YWJsZS5kYXRhKCdkZWZhdWx0Um93QWN0aW9uJykgfHwgJ2VkaXQnO1xuXG5cdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5hZGRBY3Rpb24oJCh0aGlzKSwge1xuXHRcdFx0XHRcdHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdoZXJtZXNfc2hpcHBpbmcnLCAnaGVybWVzJyksXG5cdFx0XHRcdFx0aHJlZjoganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2hlcm1lc19vcmRlci5waHA/b3JkZXJzX2lkPScgKyBvcmRlcklkLFxuXHRcdFx0XHRcdGNsYXNzOiAnaGVybWVzLXNpbmdsZScsXG5cdFx0XHRcdFx0ZGF0YToge2NvbmZpZ3VyYXRpb25WYWx1ZTogJ2hlcm1lcy1zaW5nbGUnfSxcblx0XHRcdFx0XHRpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdoZXJtZXMtc2luZ2xlJ1xuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQkdGFibGUub24oJ2RyYXcuZHQnLCBmdW5jdGlvbigpIHtcblx0XHRcdF9pbml0U2luZ2xlQWN0aW9uKCR0YWJsZSk7XG5cdFx0fSk7XG5cblx0XHRfaW5pdFNpbmdsZUFjdGlvbigkdGFibGUpO1xuXHR9KSA7XG5cbn0pO1xuIl19
