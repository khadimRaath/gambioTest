/* --------------------------------------------------------------
 share_cart_button_handler.js 2016-04-08
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module(
    'share_cart_button_handler',

    [
        'xhr',
        gambio.source + '/libs/events'
    ],

    function(data) {

        'use strict';

// ########## VARIABLE INITIALIZATION ##########

        var $this = $(this),
            defaults = {
                url: 'shop.php?do=SharedShoppingCart/StoreShoppingCart'
            },
            options = $.extend(true, {}, defaults, data),
            module = {};

        var _shareCartHandler = function() {
            jse.libs.xhr.ajax({url: options.url}, true).done(function(result) {
                $('.shared_cart_url').val($('<div/>').html(result.link).text());
            });
            $('.share-cart-response-wrapper').find('p').first().empty();
        };


// ########## INITIALIZATION ##########

        /**
         * Init function of the widget
         * @constructor
         */
        module.init = function(done) {
            $('body').on(jse.libs.template.events.SHARE_CART_MODAL_READY(), _shareCartHandler);

            done();
        };

        // Return data to widget engine
        return module;
    });