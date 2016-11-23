/* --------------------------------------------------------------
 afterbuy.js 2016-07-07
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
                    text: jse.core.lang.translate('BUTTON_AFTERBUY_SEND', 'admin_buttons'),
                    href: '',
                    class: 'afterbuy-send',
                    data: {configurationValue: 'afterbuy-send'},
                    isDefault: defaultRowAction === 'afterbuy-send',
                    callback: (event) => {
                        event.preventDefault();

                        $.ajax({
                            url: jse.core.config.get('appUrl') +
                                '/admin/admin.php?do=AfterbuyAjax/AfterbuySend&orderId=' + orderId,
                            error: () => {
                                console.log('Afterbuy send error');
                            }
                        });
                    }
                });
            });
        };

        $table.on('draw.dt', () => _initSingleAction($table));
        _initSingleAction($table);
    }) ;

});
