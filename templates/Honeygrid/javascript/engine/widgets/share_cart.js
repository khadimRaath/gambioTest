/* --------------------------------------------------------------
 share_cart.js 2016-04-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module(
	'share_cart',

	[],

	function(data) {

		'use strict';

		// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			defaults = {},
			options = $.extend(true, {}, defaults, data),
			module = {
				model: {
					lang: jse.core.config.get('appUrl') + '/shop.php?do=JsTranslations&section=shared_shopping_cart'
				}
			};

		var _copyHandler = function() {
			var sharedCartUrl = document.querySelector('.shared_cart_url'),
				copySupported = document.queryCommandSupported('copy'),
				$cartResponseWrapper = $('.share-cart-response-wrapper'),
				error = false,
				commandSuccessful, txt;

			sharedCartUrl.select();
			try {
				commandSuccessful = document.execCommand('copy');
			} catch (err) {
				jse.core.debug.log('Error occurred when copying!');
				error = true;
			}

			txt = (!commandSuccessful || !copySupported
			|| error) ? module.model.lang.text_warning : module.model.lang.text_notice;

			$cartResponseWrapper.find('p').first().text(txt);
			$cartResponseWrapper.show();
		};


		// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			$this.on('click', _copyHandler);

			done();
		};

		// Return data to widget engine
		return module;
	});