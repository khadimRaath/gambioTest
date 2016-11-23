'use strict';

/* --------------------------------------------------------------
 afterbuy.js 2016-07-07
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
                    text: jse.core.lang.translate('BUTTON_AFTERBUY_SEND', 'admin_buttons'),
                    href: '',
                    class: 'afterbuy-send',
                    data: { configurationValue: 'afterbuy-send' },
                    isDefault: defaultRowAction === 'afterbuy-send',
                    callback: function callback(event) {
                        event.preventDefault();

                        $.ajax({
                            url: jse.core.config.get('appUrl') + '/admin/admin.php?do=AfterbuyAjax/AfterbuySend&orderId=' + orderId,
                            error: function error() {
                                console.log('Afterbuy send error');
                            }
                        });
                    }
                });
            });
        };

        $table.on('draw.dt', function () {
            return _initSingleAction($table);
        });
        _initSingleAction($table);
    });
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFmdGVyYnV5LmpzIl0sIm5hbWVzIjpbIiQiLCIkdGFibGUiLCJvbiIsIl9pbml0U2luZ2xlQWN0aW9uIiwiZmluZCIsImVhY2giLCJvcmRlcklkIiwicGFyZW50cyIsImRhdGEiLCJkZWZhdWx0Um93QWN0aW9uIiwianNlIiwibGlicyIsImJ1dHRvbl9kcm9wZG93biIsImFkZEFjdGlvbiIsInRleHQiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImhyZWYiLCJjbGFzcyIsImNvbmZpZ3VyYXRpb25WYWx1ZSIsImlzRGVmYXVsdCIsImNhbGxiYWNrIiwiZXZlbnQiLCJwcmV2ZW50RGVmYXVsdCIsImFqYXgiLCJ1cmwiLCJjb25maWciLCJnZXQiLCJlcnJvciIsImNvbnNvbGUiLCJsb2ciXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsRUFBRSxZQUFXO0FBQ1Q7O0FBRUEsUUFBTUMsU0FBU0QsRUFBRSxxQkFBRixDQUFmOztBQUVBQyxXQUFPQyxFQUFQLENBQVUsU0FBVixFQUFxQixZQUFXO0FBQzVCLFlBQU1DLG9CQUFvQixTQUFwQkEsaUJBQW9CLENBQVNGLE1BQVQsRUFBaUI7QUFDdkNBLG1CQUFPRyxJQUFQLENBQVkscUJBQVosRUFBbUNDLElBQW5DLENBQXdDLFlBQVc7QUFDL0Msb0JBQU1DLFVBQVVOLEVBQUUsSUFBRixFQUFRTyxPQUFSLENBQWdCLElBQWhCLEVBQXNCQyxJQUF0QixDQUEyQixJQUEzQixDQUFoQjtBQUNBLG9CQUFNQyxtQkFBbUJSLE9BQU9PLElBQVAsQ0FBWSxrQkFBWixLQUFtQyxNQUE1RDs7QUFFQUUsb0JBQUlDLElBQUosQ0FBU0MsZUFBVCxDQUF5QkMsU0FBekIsQ0FBbUNiLEVBQUUsSUFBRixDQUFuQyxFQUE0QztBQUN4Q2MsMEJBQU1KLElBQUlLLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLHNCQUF4QixFQUFnRCxlQUFoRCxDQURrQztBQUV4Q0MsMEJBQU0sRUFGa0M7QUFHeENDLDJCQUFPLGVBSGlDO0FBSXhDWCwwQkFBTSxFQUFDWSxvQkFBb0IsZUFBckIsRUFKa0M7QUFLeENDLCtCQUFXWixxQkFBcUIsZUFMUTtBQU14Q2EsOEJBQVUsa0JBQUNDLEtBQUQsRUFBVztBQUNqQkEsOEJBQU1DLGNBQU47O0FBRUF4QiwwQkFBRXlCLElBQUYsQ0FBTztBQUNIQyxpQ0FBS2hCLElBQUlLLElBQUosQ0FBU1ksTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFDRCx3REFEQyxHQUMwRHRCLE9BRjVEO0FBR0h1QixtQ0FBTyxpQkFBTTtBQUNUQyx3Q0FBUUMsR0FBUixDQUFZLHFCQUFaO0FBQ0g7QUFMRSx5QkFBUDtBQU9IO0FBaEJ1QyxpQkFBNUM7QUFrQkgsYUF0QkQ7QUF1QkgsU0F4QkQ7O0FBMEJBOUIsZUFBT0MsRUFBUCxDQUFVLFNBQVYsRUFBcUI7QUFBQSxtQkFBTUMsa0JBQWtCRixNQUFsQixDQUFOO0FBQUEsU0FBckI7QUFDQUUsMEJBQWtCRixNQUFsQjtBQUNILEtBN0JEO0FBK0JILENBcENEIiwiZmlsZSI6ImFmdGVyYnV5LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBhZnRlcmJ1eS5qcyAyMDE2LTA3LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuJChmdW5jdGlvbigpIHtcbiAgICAndXNlIHN0cmljdCc7XG5cbiAgICBjb25zdCAkdGFibGUgPSAkKCcub3JkZXJzIC50YWJsZS1tYWluJyk7XG5cbiAgICAkdGFibGUub24oJ2luaXQuZHQnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgY29uc3QgX2luaXRTaW5nbGVBY3Rpb24gPSBmdW5jdGlvbigkdGFibGUpIHtcbiAgICAgICAgICAgICR0YWJsZS5maW5kKCcuYnRuLWdyb3VwLmRyb3Bkb3duJykuZWFjaChmdW5jdGlvbigpIHtcbiAgICAgICAgICAgICAgICBjb25zdCBvcmRlcklkID0gJCh0aGlzKS5wYXJlbnRzKCd0cicpLmRhdGEoJ2lkJyk7XG4gICAgICAgICAgICAgICAgY29uc3QgZGVmYXVsdFJvd0FjdGlvbiA9ICR0YWJsZS5kYXRhKCdkZWZhdWx0Um93QWN0aW9uJykgfHwgJ2VkaXQnO1xuXG4gICAgICAgICAgICAgICAganNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLmFkZEFjdGlvbigkKHRoaXMpLCB7XG4gICAgICAgICAgICAgICAgICAgIHRleHQ6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdCVVRUT05fQUZURVJCVVlfU0VORCcsICdhZG1pbl9idXR0b25zJyksXG4gICAgICAgICAgICAgICAgICAgIGhyZWY6ICcnLFxuICAgICAgICAgICAgICAgICAgICBjbGFzczogJ2FmdGVyYnV5LXNlbmQnLFxuICAgICAgICAgICAgICAgICAgICBkYXRhOiB7Y29uZmlndXJhdGlvblZhbHVlOiAnYWZ0ZXJidXktc2VuZCd9LFxuICAgICAgICAgICAgICAgICAgICBpc0RlZmF1bHQ6IGRlZmF1bHRSb3dBY3Rpb24gPT09ICdhZnRlcmJ1eS1zZW5kJyxcbiAgICAgICAgICAgICAgICAgICAgY2FsbGJhY2s6IChldmVudCkgPT4ge1xuICAgICAgICAgICAgICAgICAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICAgICAgICAgICAgICAgICAgJC5hamF4KHtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICtcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgJy9hZG1pbi9hZG1pbi5waHA/ZG89QWZ0ZXJidXlBamF4L0FmdGVyYnV5U2VuZCZvcmRlcklkPScgKyBvcmRlcklkLFxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yOiAoKSA9PiB7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnNvbGUubG9nKCdBZnRlcmJ1eSBzZW5kIGVycm9yJyk7XG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgICAgICAgICAgICAgfSk7XG4gICAgICAgICAgICAgICAgICAgIH1cbiAgICAgICAgICAgICAgICB9KTtcbiAgICAgICAgICAgIH0pO1xuICAgICAgICB9O1xuXG4gICAgICAgICR0YWJsZS5vbignZHJhdy5kdCcsICgpID0+IF9pbml0U2luZ2xlQWN0aW9uKCR0YWJsZSkpO1xuICAgICAgICBfaW5pdFNpbmdsZUFjdGlvbigkdGFibGUpO1xuICAgIH0pIDtcblxufSk7XG4iXX0=
