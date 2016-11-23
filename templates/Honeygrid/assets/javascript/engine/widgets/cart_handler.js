'use strict';

/* --------------------------------------------------------------
 cart_handler.js 2016-06-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component for handling the add to cart and wishlist features
 * at the product details and the category listing pages. It cares
 * for attributes, properties, quantity and all other
 * relevant data for adding an item to the basket or wishlist
 */
gambio.widgets.module('cart_handler', ['form', 'xhr', gambio.source + '/libs/events', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    $window = $(window),
	    busy = false,
	    ajax = null,
	    timeout = 0,
	    defaults = {
		// AJAX "add to cart" URL
		addCartUrl: 'shop.php?do=Cart/BuyProduct',
		// AJAX "add to cart" URL for customizer products
		addCartCustomizerUrl: 'shop.php?do=Cart/Add',
		// AJAX URL to perform a value check
		checkUrl: 'shop.php?do=CheckStatus',
		// AJAX URL to perform the add to wishlist
		wishlistUrl: 'shop.php?do=WishList/Add',
		// Submit URL for price offer button
		priceOfferUrl: 'gm_price_offer.php',
		// Submit method for price offer
		priceOfferMethod: 'get',
		// Selector for the cart dropdown
		dropdown: '#head_shopping_cart',
		// "Add to cart" buttons selectors
		cartButtons: '.js-btn-add-to-cart',
		// "Wishlist" buttons selectors
		wishlistButtons: '.btn-wishlist',
		// "Price offer" buttons selectors
		priceOfferButtons: '.btn-price-offer',
		// Selector for the attribute fields
		attributes: '.js-calculate',
		// Selector for the quantity
		quantity: '.js-calculate-qty',
		// URL where to get the template for the dropdown
		tpl: null,
		// Show attribute images in product images swiper (if possible)
		// -- this feature is not supported yet --
		attributImagesSwiper: false,
		// Trigger the attribute images to this selectors
		triggerAttrImagesTo: '#product_image_swiper, #product_thumbnail_swiper, ' + '#product_thumbnail_swiper_mobile',
		// Class that gets added to the button on processing
		processingClass: 'loading',
		// Duration for that the success or fail class gets added to the button
		processingDuration: 2000,
		// AJAX response content selectors
		selectorMapping: {
			attributeImages: '.attribute-images',
			buttons: '.shopping-cart-button',
			giftContent: '.gift-cart-content-wrapper',
			giftLayer: '.gift-cart-layer',
			shareContent: '.share-cart-content-wrapper',
			shareLayer: '.share-cart-layer',
			hiddenOptions: '#cart_quantity .hidden-options',
			message: '.global-error-messages',
			messageCart: '.cart-error-msg',
			messageHelp: '.help-block',
			modelNumber: '.model-number',
			price: '.current-price-container',
			propertiesForm: '.properties-selection-form',
			quantity: '.products-quantity-value',
			ribbonSpecial: '.ribbon-special',
			shippingInformation: '#shipping-information-layer',
			shippingTime: '.products-shipping-time-value',
			shippingTimeImage: '.img-shipping-time img',
			totals: '#cart_quantity .total-box',
			weight: '.products-details-weight-container span'
		}
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## HELPER FUNCTIONS ##########

	/**
  * Helper function that updates the button
  * state with an error or success class for
  * a specified duration
  * @param   {object}        $target         jQuery selection of the target button
  * @param   {string}        state           The state string that gets added to the loading class
  * @private
  */
	var _addButtonState = function _addButtonState($target, state) {
		var timer = setTimeout(function () {
			$target.removeClass(options.processingClass + ' ' + options.processingClass + state);
		}, options.processingDuration);

		$target.data('timer', timer).addClass(options.processingClass + state);
	};

	/**
  * Helper function to set the messages and the
  * button state.
  * @param {object}    data                        Result form the ajax request
  * @param {object}    $form                       jQuery selection of the form
  * @param {boolean}   disableButtons              If true, the button state gets set to (in)active
  * @param {boolean}   showNoCombiSelectedMesssage If true, the error message for missing property combination 
  *                                                selection will be displayed
  * @private
  */
	var _stateManager = function _stateManager(data, $form, disableButtons, showNoCombiSelectedMesssage) {

		// Remove the attribute images from the common content
		// so that it doesn't get rendered anymore. Then trigger
		// an event to the given selectors and deliver the
		// attrImages object
		if (options.attributImagesSwiper && data.attrImages && data.attrImages.length) {
			delete data.content.images;
			$(options.triggerAttrImagesTo).trigger(jse.libs.template.events.SLIDES_UPDATE(), { attributes: data.attrImages });
		}

		// Set the messages given inside the data.content object
		$.each(data.content, function (i, v) {
			var $element = $form.parent().find(options.selectorMapping[v.selector]);

			if ((!showNoCombiSelectedMesssage || v.value === '') && i === 'messageNoCombiSelected') {
				return true;
			}

			switch (v.type) {
				case 'html':
					$element.html(v.value);
					break;
				case 'attribute':
					$element.attr(v.key, v.value);
					break;
				case 'replace':
					if (v.value) {
						$element.replaceWith(v.value);
					} else {
						$element.addClass('hidden').empty();
					}
					break;
				default:
					$element.text(v.value);
					break;
			}
		});

		// Dis- / Enable the buttons
		if (disableButtons) {
			var $buttons = $form.find(options.cartButtons);
			if (data.success) {
				$buttons.removeClass('inactive');
			} else {
				$buttons.addClass('inactive');
			}
		}

		if (data.content.message) {
			var $errorField = $form.find(options.selectorMapping[data.content.message.selector]);
			if (data.content.message.value) {
				$errorField.removeClass('hidden').show();
			} else {
				$errorField.addClass('hidden').hide();

				if (showNoCombiSelectedMesssage && data.content.messageNoCombiSelected !== undefined && data.content.messageNoCombiSelected) {
					if (data.content.messageNoCombiSelected.value) {
						$errorField.removeClass('hidden').show();
					} else {
						$errorField.addClass('hidden').hide();
					}
				}
			}
		}

		$window.trigger(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE());
	};

	/**
  * Helper function to send the ajax
  * On success redirect to a given url, open a layer with
  * a message or add the item to the cart-dropdown directly
  * (by triggering an event to the body)
  * @param       {object}      data      Form data
  * @param       {object}      $form     The form to fill
  * @param       {string}      url       The URL for the AJAX request
  * @private
  */
	var _addToSomewhere = function _addToSomewhere(data, $form, url, $button) {

		if (!busy) {
			// only execute the ajax
			// if there is no pending ajax call
			busy = true;

			jse.libs.xhr.post({ url: url, data: data }, true).done(function (result) {
				try {
					// Fill the page with the result from the ajax
					_stateManager(result, $form, false);

					// If the AJAX was successful execute
					// a custom functionality
					if (result.success) {
						switch (result.type) {
							case 'url':
								if (result.url.substr(0, 4) !== 'http') {
									location.href = jse.core.config.get('appUrl') + '/' + result.url;
								} else {
									location.href = result.url;
								}

								break;
							case 'dropdown':
								$body.trigger(jse.libs.template.events.CART_UPDATE(), [true]);
								break;
							case 'layer':
								jse.libs.template.modal.info({ title: result.title, content: result.msg });
								break;
							default:
								break;
						}
					}
				} catch (ignore) {}
				_addButtonState($button, '-success');
			}).fail(function () {
				_addButtonState($button, '-fail');
			}).always(function () {
				// Reset the busy flag to be able to perform
				// further AJAX requests
				busy = false;
			});
		}
	};

	// ########## EVENT HANDLER ##########

	/**
  * Handler for the submit form / click
  * on "add to cart" & "wishlist" button.
  * It performs a check on the availability
  * of the combination and quantity. If
  * successful it performs the add to cart
  * or wishlist action, if it's not a
  * "check" call
  * @param       {object}    e      jQuery event object
  * @private
  */
	var _submitHandler = function _submitHandler(e) {
		if (e) {
			e.preventDefault();
		}

		var $self = $(this),
		    $form = $self.is('form') ? $self : $self.closest('form'),
		    customizer = $form.hasClass('customizer'),
		    properties = !!$form.find('.properties-selection-form').length,
		    module = properties ? '' : '/Attributes',
		    showNoCombiSelectedMesssage = e && e.data && e.data.target && e.data.target !== 'check';

		if ($form.length) {

			// Show properties overlay
			// to disable user interaction
			// before markup replace
			if (properties) {
				$this.addClass('loading');
			}

			var formdata = jse.libs.form.getData($form, null, true);
			formdata.target = e && e.data && e.data.target ? e.data.target : 'check';

			// Abort previous check ajax if
			// there is one in progress
			if (ajax && e) {
				ajax.abort();
			}

			// Add processing-class to the button
			// and remove old timed events
			if (formdata.target !== 'check') {
				var timer = $self.data('timer');
				if (timer) {
					clearTimeout(timer);
				}

				$self.removeClass(options.processingClass + '-success ' + options.processingClass + '-fail').addClass(options.processingClass);
			}

			ajax = jse.libs.xhr.get({
				url: options.checkUrl + module,
				data: formdata
			}, true).done(function (result) {
				_stateManager(result, $form, true, showNoCombiSelectedMesssage);
				$this.removeClass('loading');

				if (result.success) {
					var event = null,
					    url = null;

					switch (formdata.target) {
						case 'wishlist':
							if (customizer) {
								event = jse.libs.template.events.ADD_CUSTOMIZER_WISHLIST();
							}
							url = options.wishlistUrl;
							break;
						case 'cart':
							if (customizer) {
								event = jse.libs.template.events.ADD_CUSTOMIZER_CART();
								url = options.addCartCustomizerUrl;
							} else {
								url = options.addCartUrl;
							}
							break;
						case 'price_offer':
							$form.attr('action', options.priceOfferUrl).attr('method', options.priceOfferMethod);
							$form.off('submit');
							$form.submit();

							return;
						default:
							setTimeout(function () {
								$window.trigger(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE());
							}, 250);
							break;
					}

					if (event) {
						var deferred = $.Deferred();
						deferred.done(function (customizerRandom) {
							formdata[customizerRandom] = 0;
							_addToSomewhere(formdata, $form, url, $self);
						}).fail(function () {
							_addButtonState($self, '-fail');
						});
						$body.trigger(event, [{ 'deferred': deferred, 'dataset': formdata }]);
					} else if (url) {
						_addToSomewhere(formdata, $form, url, $self);
					}
				}
			}).fail(function () {
				_addButtonState($self, '-fail');
			});
		}
	};

	/**
  * Keyup handler for quantity input field
  * 
  * @param e
  * @private
  */
	var _keyupHandler = function _keyupHandler(e) {
		clearTimeout(timeout);

		timeout = setTimeout(function () {
			_submitHandler.call(this, e);
		}.bind(this), 300);
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		var $forms = $this.find('form');

		$forms.on('submit', { 'target': 'cart' }, _submitHandler).on('click', options.cartButtons + ':not(.inactive)', { 'target': 'cart' }, _submitHandler).on('click', options.wishlistButtons, { 'target': 'wishlist' }, _submitHandler).on('click', options.priceOfferButtons, { 'target': 'price_offer' }, _submitHandler).on('change', options.attributes, { 'target': 'check' }, _submitHandler).on('blur', options.quantity, { 'target': 'check' }, _submitHandler).on('keyup', options.quantity, { 'target': 'check' }, _keyupHandler);

		// Fallback if the backend renders incorrect data
		// on initial page call
		$forms.not('.no-status-check').each(function () {
			_submitHandler.call($(this));
		});

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2FydF9oYW5kbGVyLmpzIl0sIm5hbWVzIjpbImdhbWJpbyIsIndpZGdldHMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJGJvZHkiLCIkd2luZG93Iiwid2luZG93IiwiYnVzeSIsImFqYXgiLCJ0aW1lb3V0IiwiZGVmYXVsdHMiLCJhZGRDYXJ0VXJsIiwiYWRkQ2FydEN1c3RvbWl6ZXJVcmwiLCJjaGVja1VybCIsIndpc2hsaXN0VXJsIiwicHJpY2VPZmZlclVybCIsInByaWNlT2ZmZXJNZXRob2QiLCJkcm9wZG93biIsImNhcnRCdXR0b25zIiwid2lzaGxpc3RCdXR0b25zIiwicHJpY2VPZmZlckJ1dHRvbnMiLCJhdHRyaWJ1dGVzIiwicXVhbnRpdHkiLCJ0cGwiLCJhdHRyaWJ1dEltYWdlc1N3aXBlciIsInRyaWdnZXJBdHRySW1hZ2VzVG8iLCJwcm9jZXNzaW5nQ2xhc3MiLCJwcm9jZXNzaW5nRHVyYXRpb24iLCJzZWxlY3Rvck1hcHBpbmciLCJhdHRyaWJ1dGVJbWFnZXMiLCJidXR0b25zIiwiZ2lmdENvbnRlbnQiLCJnaWZ0TGF5ZXIiLCJzaGFyZUNvbnRlbnQiLCJzaGFyZUxheWVyIiwiaGlkZGVuT3B0aW9ucyIsIm1lc3NhZ2UiLCJtZXNzYWdlQ2FydCIsIm1lc3NhZ2VIZWxwIiwibW9kZWxOdW1iZXIiLCJwcmljZSIsInByb3BlcnRpZXNGb3JtIiwicmliYm9uU3BlY2lhbCIsInNoaXBwaW5nSW5mb3JtYXRpb24iLCJzaGlwcGluZ1RpbWUiLCJzaGlwcGluZ1RpbWVJbWFnZSIsInRvdGFscyIsIndlaWdodCIsIm9wdGlvbnMiLCJleHRlbmQiLCJfYWRkQnV0dG9uU3RhdGUiLCIkdGFyZ2V0Iiwic3RhdGUiLCJ0aW1lciIsInNldFRpbWVvdXQiLCJyZW1vdmVDbGFzcyIsImFkZENsYXNzIiwiX3N0YXRlTWFuYWdlciIsIiRmb3JtIiwiZGlzYWJsZUJ1dHRvbnMiLCJzaG93Tm9Db21iaVNlbGVjdGVkTWVzc3NhZ2UiLCJhdHRySW1hZ2VzIiwibGVuZ3RoIiwiY29udGVudCIsImltYWdlcyIsInRyaWdnZXIiLCJqc2UiLCJsaWJzIiwidGVtcGxhdGUiLCJldmVudHMiLCJTTElERVNfVVBEQVRFIiwiZWFjaCIsImkiLCJ2IiwiJGVsZW1lbnQiLCJwYXJlbnQiLCJmaW5kIiwic2VsZWN0b3IiLCJ2YWx1ZSIsInR5cGUiLCJodG1sIiwiYXR0ciIsImtleSIsInJlcGxhY2VXaXRoIiwiZW1wdHkiLCJ0ZXh0IiwiJGJ1dHRvbnMiLCJzdWNjZXNzIiwiJGVycm9yRmllbGQiLCJzaG93IiwiaGlkZSIsIm1lc3NhZ2VOb0NvbWJpU2VsZWN0ZWQiLCJ1bmRlZmluZWQiLCJTVElDS1lCT1hfQ09OVEVOVF9DSEFOR0UiLCJfYWRkVG9Tb21ld2hlcmUiLCJ1cmwiLCIkYnV0dG9uIiwieGhyIiwicG9zdCIsImRvbmUiLCJyZXN1bHQiLCJzdWJzdHIiLCJsb2NhdGlvbiIsImhyZWYiLCJjb3JlIiwiY29uZmlnIiwiZ2V0IiwiQ0FSVF9VUERBVEUiLCJtb2RhbCIsImluZm8iLCJ0aXRsZSIsIm1zZyIsImlnbm9yZSIsImZhaWwiLCJhbHdheXMiLCJfc3VibWl0SGFuZGxlciIsImUiLCJwcmV2ZW50RGVmYXVsdCIsIiRzZWxmIiwiaXMiLCJjbG9zZXN0IiwiY3VzdG9taXplciIsImhhc0NsYXNzIiwicHJvcGVydGllcyIsInRhcmdldCIsImZvcm1kYXRhIiwiZm9ybSIsImdldERhdGEiLCJhYm9ydCIsImNsZWFyVGltZW91dCIsImV2ZW50IiwiQUREX0NVU1RPTUlaRVJfV0lTSExJU1QiLCJBRERfQ1VTVE9NSVpFUl9DQVJUIiwib2ZmIiwic3VibWl0IiwiZGVmZXJyZWQiLCJEZWZlcnJlZCIsImN1c3RvbWl6ZXJSYW5kb20iLCJfa2V5dXBIYW5kbGVyIiwiY2FsbCIsImJpbmQiLCJpbml0IiwiJGZvcm1zIiwib24iLCJub3QiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7O0FBTUFBLE9BQU9DLE9BQVAs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QUFRM0IsVUFMdkIsRUFLbUMsRUFBQyxVQUFVLE9BQVgsRUFMbkMsRUFLd0Q4RixjQUx4RCxFQU1FNEIsRUFORixDQU1LLE1BTkwsRUFNYS9GLFFBQVExQixRQU5yQixFQU0rQixFQUFDLFVBQVUsT0FBWCxFQU4vQixFQU1vRDZGLGNBTnBELEVBT0U0QixFQVBGLENBT0ssT0FQTCxFQU9jL0YsUUFBUTFCLFFBUHRCLEVBT2dDLEVBQUMsVUFBVSxPQUFYLEVBUGhDLEVBT3FEb0gsYUFQckQ7O0FBU0E7QUFDQTtBQUNBSSxTQUFPRSxHQUFQLENBQVcsa0JBQVgsRUFBK0J6RSxJQUEvQixDQUFvQyxZQUFXO0FBQzlDNEMsa0JBQWV3QixJQUFmLENBQW9CeEksRUFBRSxJQUFGLENBQXBCO0FBQ0EsR0FGRDs7QUFJQWdHO0FBQ0EsRUFwQkQ7O0FBc0JBO0FBQ0EsUUFBT3BHLE1BQVA7QUFDQSxDQW5hRiIsImZpbGUiOiJ3aWRnZXRzL2NhcnRfaGFuZGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY2FydF9oYW5kbGVyLmpzIDIwMTYtMDYtMjJcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIENvbXBvbmVudCBmb3IgaGFuZGxpbmcgdGhlIGFkZCB0byBjYXJ0IGFuZCB3aXNobGlzdCBmZWF0dXJlc1xuICogYXQgdGhlIHByb2R1Y3QgZGV0YWlscyBhbmQgdGhlIGNhdGVnb3J5IGxpc3RpbmcgcGFnZXMuIEl0IGNhcmVzXG4gKiBmb3IgYXR0cmlidXRlcywgcHJvcGVydGllcywgcXVhbnRpdHkgYW5kIGFsbCBvdGhlclxuICogcmVsZXZhbnQgZGF0YSBmb3IgYWRkaW5nIGFuIGl0ZW0gdG8gdGhlIGJhc2tldCBvciB3aXNobGlzdFxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdjYXJ0X2hhbmRsZXInLFxuXG5cdFtcblx0XHQnZm9ybScsXG5cdFx0J3hocicsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwuZXh0LW1hZ25pZmljJyxcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL21vZGFsJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHQkd2luZG93ID0gJCh3aW5kb3cpLFxuXHRcdFx0YnVzeSA9IGZhbHNlLFxuXHRcdFx0YWpheCA9IG51bGwsXG5cdFx0XHR0aW1lb3V0ID0gMCxcblx0XHRcdGRlZmF1bHRzID0ge1xuXHRcdFx0XHQvLyBBSkFYIFwiYWRkIHRvIGNhcnRcIiBVUkxcblx0XHRcdFx0YWRkQ2FydFVybDogJ3Nob3AucGhwP2RvPUNhcnQvQnV5UHJvZHVjdCcsXG5cdFx0XHRcdC8vIEFKQVggXCJhZGQgdG8gY2FydFwiIFVSTCBmb3IgY3VzdG9taXplciBwcm9kdWN0c1xuXHRcdFx0XHRhZGRDYXJ0Q3VzdG9taXplclVybDogJ3Nob3AucGhwP2RvPUNhcnQvQWRkJyxcblx0XHRcdFx0Ly8gQUpBWCBVUkwgdG8gcGVyZm9ybSBhIHZhbHVlIGNoZWNrXG5cdFx0XHRcdGNoZWNrVXJsOiAnc2hvcC5waHA/ZG89Q2hlY2tTdGF0dXMnLFxuXHRcdFx0XHQvLyBBSkFYIFVSTCB0byBwZXJmb3JtIHRoZSBhZGQgdG8gd2lzaGxpc3Rcblx0XHRcdFx0d2lzaGxpc3RVcmw6ICdzaG9wLnBocD9kbz1XaXNoTGlzdC9BZGQnLFxuXHRcdFx0XHQvLyBTdWJtaXQgVVJMIGZvciBwcmljZSBvZmZlciBidXR0b25cblx0XHRcdFx0cHJpY2VPZmZlclVybDogJ2dtX3ByaWNlX29mZmVyLnBocCcsXG5cdFx0XHRcdC8vIFN1Ym1pdCBtZXRob2QgZm9yIHByaWNlIG9mZmVyXG5cdFx0XHRcdHByaWNlT2ZmZXJNZXRob2Q6ICdnZXQnLFxuXHRcdFx0XHQvLyBTZWxlY3RvciBmb3IgdGhlIGNhcnQgZHJvcGRvd25cblx0XHRcdFx0ZHJvcGRvd246ICcjaGVhZF9zaG9wcGluZ19jYXJ0Jyxcblx0XHRcdFx0Ly8gXCJBZGQgdG8gY2FydFwiIGJ1dHRvbnMgc2VsZWN0b3JzXG5cdFx0XHRcdGNhcnRCdXR0b25zOiAnLmpzLWJ0bi1hZGQtdG8tY2FydCcsXG5cdFx0XHRcdC8vIFwiV2lzaGxpc3RcIiBidXR0b25zIHNlbGVjdG9yc1xuXHRcdFx0XHR3aXNobGlzdEJ1dHRvbnM6ICcuYnRuLXdpc2hsaXN0Jyxcblx0XHRcdFx0Ly8gXCJQcmljZSBvZmZlclwiIGJ1dHRvbnMgc2VsZWN0b3JzXG5cdFx0XHRcdHByaWNlT2ZmZXJCdXR0b25zOiAnLmJ0bi1wcmljZS1vZmZlcicsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIGZvciB0aGUgYXR0cmlidXRlIGZpZWxkc1xuXHRcdFx0XHRhdHRyaWJ1dGVzOiAnLmpzLWNhbGN1bGF0ZScsXG5cdFx0XHRcdC8vIFNlbGVjdG9yIGZvciB0aGUgcXVhbnRpdHlcblx0XHRcdFx0cXVhbnRpdHk6ICcuanMtY2FsY3VsYXRlLXF0eScsXG5cdFx0XHRcdC8vIFVSTCB3aGVyZSB0byBnZXQgdGhlIHRlbXBsYXRlIGZvciB0aGUgZHJvcGRvd25cblx0XHRcdFx0dHBsOiBudWxsLFxuXHRcdFx0XHQvLyBTaG93IGF0dHJpYnV0ZSBpbWFnZXMgaW4gcHJvZHVjdCBpbWFnZXMgc3dpcGVyIChpZiBwb3NzaWJsZSlcblx0XHRcdFx0Ly8gLS0gdGhpcyBmZWF0dXJlIGlzIG5vdCBzdXBwb3J0ZWQgeWV0IC0tXG5cdFx0XHRcdGF0dHJpYnV0SW1hZ2VzU3dpcGVyOiBmYWxzZSxcblx0XHRcdFx0Ly8gVHJpZ2dlciB0aGUgYXR0cmlidXRlIGltYWdlcyB0byB0aGlzIHNlbGVjdG9yc1xuXHRcdFx0XHR0cmlnZ2VyQXR0ckltYWdlc1RvOiAnI3Byb2R1Y3RfaW1hZ2Vfc3dpcGVyLCAjcHJvZHVjdF90aHVtYm5haWxfc3dpcGVyLCAnXG5cdFx0XHRcdCsgJyNwcm9kdWN0X3RodW1ibmFpbF9zd2lwZXJfbW9iaWxlJyxcblx0XHRcdFx0Ly8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIHRoZSBidXR0b24gb24gcHJvY2Vzc2luZ1xuXHRcdFx0XHRwcm9jZXNzaW5nQ2xhc3M6ICdsb2FkaW5nJyxcblx0XHRcdFx0Ly8gRHVyYXRpb24gZm9yIHRoYXQgdGhlIHN1Y2Nlc3Mgb3IgZmFpbCBjbGFzcyBnZXRzIGFkZGVkIHRvIHRoZSBidXR0b25cblx0XHRcdFx0cHJvY2Vzc2luZ0R1cmF0aW9uOiAyMDAwLFxuXHRcdFx0XHQvLyBBSkFYIHJlc3BvbnNlIGNvbnRlbnQgc2VsZWN0b3JzXG5cdFx0XHRcdHNlbGVjdG9yTWFwcGluZzoge1xuXHRcdFx0XHRcdGF0dHJpYnV0ZUltYWdlczogJy5hdHRyaWJ1dGUtaW1hZ2VzJyxcblx0XHRcdFx0XHRidXR0b25zOiAnLnNob3BwaW5nLWNhcnQtYnV0dG9uJyxcblx0XHRcdFx0XHRnaWZ0Q29udGVudDogJy5naWZ0LWNhcnQtY29udGVudC13cmFwcGVyJyxcblx0XHRcdFx0XHRnaWZ0TGF5ZXI6ICcuZ2lmdC1jYXJ0LWxheWVyJyxcblx0XHRcdFx0XHRzaGFyZUNvbnRlbnQ6Jy5zaGFyZS1jYXJ0LWNvbnRlbnQtd3JhcHBlcicsXG5cdFx0XHRcdFx0c2hhcmVMYXllcjogJy5zaGFyZS1jYXJ0LWxheWVyJyxcblx0XHRcdFx0XHRoaWRkZW5PcHRpb25zOiAnI2NhcnRfcXVhbnRpdHkgLmhpZGRlbi1vcHRpb25zJyxcblx0XHRcdFx0XHRtZXNzYWdlOiAnLmdsb2JhbC1lcnJvci1tZXNzYWdlcycsXG5cdFx0XHRcdFx0bWVzc2FnZUNhcnQ6ICcuY2FydC1lcnJvci1tc2cnLFxuXHRcdFx0XHRcdG1lc3NhZ2VIZWxwOiAnLmhlbHAtYmxvY2snLFxuXHRcdFx0XHRcdG1vZGVsTnVtYmVyOiAnLm1vZGVsLW51bWJlcicsXG5cdFx0XHRcdFx0cHJpY2U6ICcuY3VycmVudC1wcmljZS1jb250YWluZXInLFxuXHRcdFx0XHRcdHByb3BlcnRpZXNGb3JtOiAnLnByb3BlcnRpZXMtc2VsZWN0aW9uLWZvcm0nLFxuXHRcdFx0XHRcdHF1YW50aXR5OiAnLnByb2R1Y3RzLXF1YW50aXR5LXZhbHVlJyxcblx0XHRcdFx0XHRyaWJib25TcGVjaWFsOiAnLnJpYmJvbi1zcGVjaWFsJyxcblx0XHRcdFx0XHRzaGlwcGluZ0luZm9ybWF0aW9uOiAnI3NoaXBwaW5nLWluZm9ybWF0aW9uLWxheWVyJyxcblx0XHRcdFx0XHRzaGlwcGluZ1RpbWU6ICcucHJvZHVjdHMtc2hpcHBpbmctdGltZS12YWx1ZScsXG5cdFx0XHRcdFx0c2hpcHBpbmdUaW1lSW1hZ2U6ICcuaW1nLXNoaXBwaW5nLXRpbWUgaW1nJyxcblx0XHRcdFx0XHR0b3RhbHM6ICcjY2FydF9xdWFudGl0eSAudG90YWwtYm94Jyxcblx0XHRcdFx0XHR3ZWlnaHQ6ICcucHJvZHVjdHMtZGV0YWlscy13ZWlnaHQtY29udGFpbmVyIHNwYW4nXG5cdFx0XHRcdH1cblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCB1cGRhdGVzIHRoZSBidXR0b25cblx0XHQgKiBzdGF0ZSB3aXRoIGFuIGVycm9yIG9yIHN1Y2Nlc3MgY2xhc3MgZm9yXG5cdFx0ICogYSBzcGVjaWZpZWQgZHVyYXRpb25cblx0XHQgKiBAcGFyYW0gICB7b2JqZWN0fSAgICAgICAgJHRhcmdldCAgICAgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIHRhcmdldCBidXR0b25cblx0XHQgKiBAcGFyYW0gICB7c3RyaW5nfSAgICAgICAgc3RhdGUgICAgICAgICAgIFRoZSBzdGF0ZSBzdHJpbmcgdGhhdCBnZXRzIGFkZGVkIHRvIHRoZSBsb2FkaW5nIGNsYXNzXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2FkZEJ1dHRvblN0YXRlID0gZnVuY3Rpb24oJHRhcmdldCwgc3RhdGUpIHtcblx0XHRcdHZhciB0aW1lciA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCR0YXJnZXQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5wcm9jZXNzaW5nQ2xhc3MgKyAnICcgKyBvcHRpb25zLnByb2Nlc3NpbmdDbGFzcyArIHN0YXRlKTtcblx0XHRcdH0sIG9wdGlvbnMucHJvY2Vzc2luZ0R1cmF0aW9uKTtcblxuXHRcdFx0JHRhcmdldFxuXHRcdFx0XHQuZGF0YSgndGltZXInLCB0aW1lcilcblx0XHRcdFx0LmFkZENsYXNzKG9wdGlvbnMucHJvY2Vzc2luZ0NsYXNzICsgc3RhdGUpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gc2V0IHRoZSBtZXNzYWdlcyBhbmQgdGhlXG5cdFx0ICogYnV0dG9uIHN0YXRlLlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAgICBkYXRhICAgICAgICAgICAgICAgICAgICAgICAgUmVzdWx0IGZvcm0gdGhlIGFqYXggcmVxdWVzdFxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAgICAkZm9ybSAgICAgICAgICAgICAgICAgICAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgZm9ybVxuXHRcdCAqIEBwYXJhbSB7Ym9vbGVhbn0gICBkaXNhYmxlQnV0dG9ucyAgICAgICAgICAgICAgSWYgdHJ1ZSwgdGhlIGJ1dHRvbiBzdGF0ZSBnZXRzIHNldCB0byAoaW4pYWN0aXZlXG5cdFx0ICogQHBhcmFtIHtib29sZWFufSAgIHNob3dOb0NvbWJpU2VsZWN0ZWRNZXNzc2FnZSBJZiB0cnVlLCB0aGUgZXJyb3IgbWVzc2FnZSBmb3IgbWlzc2luZyBwcm9wZXJ0eSBjb21iaW5hdGlvbiBcblx0XHQgKiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHNlbGVjdGlvbiB3aWxsIGJlIGRpc3BsYXllZFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zdGF0ZU1hbmFnZXIgPSBmdW5jdGlvbihkYXRhLCAkZm9ybSwgZGlzYWJsZUJ1dHRvbnMsIHNob3dOb0NvbWJpU2VsZWN0ZWRNZXNzc2FnZSkge1xuXG5cdFx0XHQvLyBSZW1vdmUgdGhlIGF0dHJpYnV0ZSBpbWFnZXMgZnJvbSB0aGUgY29tbW9uIGNvbnRlbnRcblx0XHRcdC8vIHNvIHRoYXQgaXQgZG9lc24ndCBnZXQgcmVuZGVyZWQgYW55bW9yZS4gVGhlbiB0cmlnZ2VyXG5cdFx0XHQvLyBhbiBldmVudCB0byB0aGUgZ2l2ZW4gc2VsZWN0b3JzIGFuZCBkZWxpdmVyIHRoZVxuXHRcdFx0Ly8gYXR0ckltYWdlcyBvYmplY3Rcblx0XHRcdGlmIChvcHRpb25zLmF0dHJpYnV0SW1hZ2VzU3dpcGVyICYmIGRhdGEuYXR0ckltYWdlcyAmJiBkYXRhLmF0dHJJbWFnZXMubGVuZ3RoKSB7XG5cdFx0XHRcdGRlbGV0ZSBkYXRhLmNvbnRlbnQuaW1hZ2VzO1xuXHRcdFx0XHQkKG9wdGlvbnMudHJpZ2dlckF0dHJJbWFnZXNUbylcblx0XHRcdFx0XHQudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuU0xJREVTX1VQREFURSgpLCB7YXR0cmlidXRlczogZGF0YS5hdHRySW1hZ2VzfSk7XG5cdFx0XHR9XG5cblx0XHRcdC8vIFNldCB0aGUgbWVzc2FnZXMgZ2l2ZW4gaW5zaWRlIHRoZSBkYXRhLmNvbnRlbnQgb2JqZWN0XG5cdFx0XHQkLmVhY2goZGF0YS5jb250ZW50LCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRcdHZhciAkZWxlbWVudCA9ICRmb3JtLnBhcmVudCgpLmZpbmQob3B0aW9ucy5zZWxlY3Rvck1hcHBpbmdbdi5zZWxlY3Rvcl0pO1xuXG5cdFx0XHRcdGlmICgoIXNob3dOb0NvbWJpU2VsZWN0ZWRNZXNzc2FnZSB8fCB2LnZhbHVlID09PSAnJykgJiYgaSA9PT0gJ21lc3NhZ2VOb0NvbWJpU2VsZWN0ZWQnKSB7XG5cdFx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRzd2l0Y2ggKHYudHlwZSkge1xuXHRcdFx0XHRcdGNhc2UgJ2h0bWwnOlxuXHRcdFx0XHRcdFx0JGVsZW1lbnQuaHRtbCh2LnZhbHVlKTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGNhc2UgJ2F0dHJpYnV0ZSc6XG5cdFx0XHRcdFx0XHQkZWxlbWVudC5hdHRyKHYua2V5LCB2LnZhbHVlKTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGNhc2UgJ3JlcGxhY2UnOlxuXHRcdFx0XHRcdFx0aWYgKHYudmFsdWUpIHtcblx0XHRcdFx0XHRcdFx0JGVsZW1lbnQucmVwbGFjZVdpdGgodi52YWx1ZSk7XG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHQkZWxlbWVudFxuXHRcdFx0XHRcdFx0XHRcdC5hZGRDbGFzcygnaGlkZGVuJylcblx0XHRcdFx0XHRcdFx0XHQuZW1wdHkoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0XHQkZWxlbWVudC50ZXh0KHYudmFsdWUpO1xuXHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXG5cdFx0XHQvLyBEaXMtIC8gRW5hYmxlIHRoZSBidXR0b25zXG5cdFx0XHRpZiAoZGlzYWJsZUJ1dHRvbnMpIHtcblx0XHRcdFx0dmFyICRidXR0b25zID0gJGZvcm0uZmluZChvcHRpb25zLmNhcnRCdXR0b25zKTtcblx0XHRcdFx0aWYgKGRhdGEuc3VjY2Vzcykge1xuXHRcdFx0XHRcdCRidXR0b25zLnJlbW92ZUNsYXNzKCdpbmFjdGl2ZScpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRidXR0b25zLmFkZENsYXNzKCdpbmFjdGl2ZScpO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cblx0XHRcdGlmIChkYXRhLmNvbnRlbnQubWVzc2FnZSkge1xuXHRcdFx0XHR2YXIgJGVycm9yRmllbGQgPSAkZm9ybS5maW5kKG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nW2RhdGEuY29udGVudC5tZXNzYWdlLnNlbGVjdG9yXSk7XG5cdFx0XHRcdGlmIChkYXRhLmNvbnRlbnQubWVzc2FnZS52YWx1ZSkge1xuXHRcdFx0XHRcdCRlcnJvckZpZWxkXG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpXG5cdFx0XHRcdFx0XHQuc2hvdygpO1xuXHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdCRlcnJvckZpZWxkXG5cdFx0XHRcdFx0XHQuYWRkQ2xhc3MoJ2hpZGRlbicpXG5cdFx0XHRcdFx0XHQuaGlkZSgpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmIChzaG93Tm9Db21iaVNlbGVjdGVkTWVzc3NhZ2Vcblx0XHRcdFx0XHRcdCYmIGRhdGEuY29udGVudC5tZXNzYWdlTm9Db21iaVNlbGVjdGVkICE9PSB1bmRlZmluZWRcblx0XHRcdFx0XHRcdCYmIGRhdGEuY29udGVudC5tZXNzYWdlTm9Db21iaVNlbGVjdGVkKSB7XG5cdFx0XHRcdFx0XHRpZiAoZGF0YS5jb250ZW50Lm1lc3NhZ2VOb0NvbWJpU2VsZWN0ZWQudmFsdWUpIHtcblx0XHRcdFx0XHRcdFx0JGVycm9yRmllbGRcblx0XHRcdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpXG5cdFx0XHRcdFx0XHRcdFx0LnNob3coKTtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdCRlcnJvckZpZWxkXG5cdFx0XHRcdFx0XHRcdFx0LmFkZENsYXNzKCdoaWRkZW4nKVxuXHRcdFx0XHRcdFx0XHRcdC5oaWRlKCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCR3aW5kb3cudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuU1RJQ0tZQk9YX0NPTlRFTlRfQ0hBTkdFKCkpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdG8gc2VuZCB0aGUgYWpheFxuXHRcdCAqIE9uIHN1Y2Nlc3MgcmVkaXJlY3QgdG8gYSBnaXZlbiB1cmwsIG9wZW4gYSBsYXllciB3aXRoXG5cdFx0ICogYSBtZXNzYWdlIG9yIGFkZCB0aGUgaXRlbSB0byB0aGUgY2FydC1kcm9wZG93biBkaXJlY3RseVxuXHRcdCAqIChieSB0cmlnZ2VyaW5nIGFuIGV2ZW50IHRvIHRoZSBib2R5KVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgIGRhdGEgICAgICBGb3JtIGRhdGFcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAkZm9ybSAgICAgVGhlIGZvcm0gdG8gZmlsbFxuXHRcdCAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICAgIHVybCAgICAgICBUaGUgVVJMIGZvciB0aGUgQUpBWCByZXF1ZXN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2FkZFRvU29tZXdoZXJlID0gZnVuY3Rpb24oZGF0YSwgJGZvcm0sIHVybCwgJGJ1dHRvbikge1xuXG5cdFx0XHRpZiAoIWJ1c3kpIHtcblx0XHRcdFx0Ly8gb25seSBleGVjdXRlIHRoZSBhamF4XG5cdFx0XHRcdC8vIGlmIHRoZXJlIGlzIG5vIHBlbmRpbmcgYWpheCBjYWxsXG5cdFx0XHRcdGJ1c3kgPSB0cnVlO1xuXG5cdFx0XHRcdGpzZS5saWJzLnhoci5wb3N0KHt1cmw6IHVybCwgZGF0YTogZGF0YX0sIHRydWUpLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdFx0dHJ5IHtcblx0XHRcdFx0XHRcdC8vIEZpbGwgdGhlIHBhZ2Ugd2l0aCB0aGUgcmVzdWx0IGZyb20gdGhlIGFqYXhcblx0XHRcdFx0XHRcdF9zdGF0ZU1hbmFnZXIocmVzdWx0LCAkZm9ybSwgZmFsc2UpO1xuXG5cdFx0XHRcdFx0XHQvLyBJZiB0aGUgQUpBWCB3YXMgc3VjY2Vzc2Z1bCBleGVjdXRlXG5cdFx0XHRcdFx0XHQvLyBhIGN1c3RvbSBmdW5jdGlvbmFsaXR5XG5cdFx0XHRcdFx0XHRpZiAocmVzdWx0LnN1Y2Nlc3MpIHtcblx0XHRcdFx0XHRcdFx0c3dpdGNoIChyZXN1bHQudHlwZSkge1xuXHRcdFx0XHRcdFx0XHRcdGNhc2UgJ3VybCc6XG5cdFx0XHRcdFx0XHRcdFx0XHRpZiAocmVzdWx0LnVybC5zdWJzdHIoMCwgNCkgIT09ICdodHRwJykge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRsb2NhdGlvbi5ocmVmID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnLycgKyByZXN1bHQudXJsO1xuXHRcdFx0XHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0XHRcdFx0bG9jYXRpb24uaHJlZiA9IHJlc3VsdC51cmw7XG5cdFx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XHRcdGNhc2UgJ2Ryb3Bkb3duJzpcblx0XHRcdFx0XHRcdFx0XHRcdCRib2R5LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLkNBUlRfVVBEQVRFKCksIFt0cnVlXSk7XG5cdFx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFx0XHRjYXNlICdsYXllcic6XG5cdFx0XHRcdFx0XHRcdFx0XHRqc2UubGlicy50ZW1wbGF0ZS5tb2RhbC5pbmZvKHt0aXRsZTogcmVzdWx0LnRpdGxlLCBjb250ZW50OiByZXN1bHQubXNnfSk7XG5cdFx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9IGNhdGNoIChpZ25vcmUpIHtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0X2FkZEJ1dHRvblN0YXRlKCRidXR0b24sICctc3VjY2VzcycpO1xuXHRcdFx0XHR9KS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdF9hZGRCdXR0b25TdGF0ZSgkYnV0dG9uLCAnLWZhaWwnKTtcblx0XHRcdFx0fSkuYWx3YXlzKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdC8vIFJlc2V0IHRoZSBidXN5IGZsYWcgdG8gYmUgYWJsZSB0byBwZXJmb3JtXG5cdFx0XHRcdFx0Ly8gZnVydGhlciBBSkFYIHJlcXVlc3RzXG5cdFx0XHRcdFx0YnVzeSA9IGZhbHNlO1xuXHRcdFx0XHR9KTtcblx0XHRcdH1cblxuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEhhbmRsZXIgZm9yIHRoZSBzdWJtaXQgZm9ybSAvIGNsaWNrXG5cdFx0ICogb24gXCJhZGQgdG8gY2FydFwiICYgXCJ3aXNobGlzdFwiIGJ1dHRvbi5cblx0XHQgKiBJdCBwZXJmb3JtcyBhIGNoZWNrIG9uIHRoZSBhdmFpbGFiaWxpdHlcblx0XHQgKiBvZiB0aGUgY29tYmluYXRpb24gYW5kIHF1YW50aXR5LiBJZlxuXHRcdCAqIHN1Y2Nlc3NmdWwgaXQgcGVyZm9ybXMgdGhlIGFkZCB0byBjYXJ0XG5cdFx0ICogb3Igd2lzaGxpc3QgYWN0aW9uLCBpZiBpdCdzIG5vdCBhXG5cdFx0ICogXCJjaGVja1wiIGNhbGxcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc3VibWl0SGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGlmIChlKSB7XG5cdFx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdH1cblxuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0JGZvcm0gPSAoJHNlbGYuaXMoJ2Zvcm0nKSkgPyAkc2VsZiA6ICRzZWxmLmNsb3Nlc3QoJ2Zvcm0nKSxcblx0XHRcdFx0Y3VzdG9taXplciA9ICRmb3JtLmhhc0NsYXNzKCdjdXN0b21pemVyJyksXG5cdFx0XHRcdHByb3BlcnRpZXMgPSAhISRmb3JtLmZpbmQoJy5wcm9wZXJ0aWVzLXNlbGVjdGlvbi1mb3JtJykubGVuZ3RoLFxuXHRcdFx0XHRtb2R1bGUgPSBwcm9wZXJ0aWVzID8gJycgOiAnL0F0dHJpYnV0ZXMnLFxuXHRcdFx0XHRzaG93Tm9Db21iaVNlbGVjdGVkTWVzc3NhZ2UgPSBlICYmIGUuZGF0YSAmJiBlLmRhdGEudGFyZ2V0ICYmIGUuZGF0YS50YXJnZXQgIT09ICdjaGVjayc7XG5cblx0XHRcdGlmICgkZm9ybS5sZW5ndGgpIHtcblxuXHRcdFx0XHQvLyBTaG93IHByb3BlcnRpZXMgb3ZlcmxheVxuXHRcdFx0XHQvLyB0byBkaXNhYmxlIHVzZXIgaW50ZXJhY3Rpb25cblx0XHRcdFx0Ly8gYmVmb3JlIG1hcmt1cCByZXBsYWNlXG5cdFx0XHRcdGlmIChwcm9wZXJ0aWVzKSB7XG5cdFx0XHRcdFx0JHRoaXMuYWRkQ2xhc3MoJ2xvYWRpbmcnKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdHZhciBmb3JtZGF0YSA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkZm9ybSwgbnVsbCwgdHJ1ZSk7XG5cdFx0XHRcdGZvcm1kYXRhLnRhcmdldCA9IChlICYmIGUuZGF0YSAmJiBlLmRhdGEudGFyZ2V0KSA/IGUuZGF0YS50YXJnZXQgOiAnY2hlY2snO1xuXG5cdFx0XHRcdC8vIEFib3J0IHByZXZpb3VzIGNoZWNrIGFqYXggaWZcblx0XHRcdFx0Ly8gdGhlcmUgaXMgb25lIGluIHByb2dyZXNzXG5cdFx0XHRcdGlmIChhamF4ICYmIGUpIHtcblx0XHRcdFx0XHRhamF4LmFib3J0KCk7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHQvLyBBZGQgcHJvY2Vzc2luZy1jbGFzcyB0byB0aGUgYnV0dG9uXG5cdFx0XHRcdC8vIGFuZCByZW1vdmUgb2xkIHRpbWVkIGV2ZW50c1xuXHRcdFx0XHRpZiAoZm9ybWRhdGEudGFyZ2V0ICE9PSAnY2hlY2snKSB7XG5cdFx0XHRcdFx0dmFyIHRpbWVyID0gJHNlbGYuZGF0YSgndGltZXInKTtcblx0XHRcdFx0XHRpZiAodGltZXIpIHtcblx0XHRcdFx0XHRcdGNsZWFyVGltZW91dCh0aW1lcik7XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcyhvcHRpb25zLnByb2Nlc3NpbmdDbGFzcyArICctc3VjY2VzcyAnICsgb3B0aW9ucy5wcm9jZXNzaW5nQ2xhc3MgKyAnLWZhaWwnKVxuXHRcdFx0XHRcdFx0LmFkZENsYXNzKG9wdGlvbnMucHJvY2Vzc2luZ0NsYXNzKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGFqYXggPSBqc2UubGlicy54aHIuZ2V0KHtcblx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICB1cmw6IG9wdGlvbnMuY2hlY2tVcmwgKyBtb2R1bGUsXG5cdFx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgZGF0YTogZm9ybWRhdGFcblx0XHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgfSwgdHJ1ZSkuZG9uZShmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdFx0XHRfc3RhdGVNYW5hZ2VyKHJlc3VsdCwgJGZvcm0sIHRydWUsIHNob3dOb0NvbWJpU2VsZWN0ZWRNZXNzc2FnZSk7XG5cdFx0XHRcdFx0JHRoaXMucmVtb3ZlQ2xhc3MoJ2xvYWRpbmcnKTtcblxuXHRcdFx0XHRcdGlmIChyZXN1bHQuc3VjY2Vzcykge1xuXHRcdFx0XHRcdFx0dmFyIGV2ZW50ID0gbnVsbCxcblx0XHRcdFx0XHRcdFx0dXJsID0gbnVsbDtcblxuXHRcdFx0XHRcdFx0c3dpdGNoIChmb3JtZGF0YS50YXJnZXQpIHtcblx0XHRcdFx0XHRcdFx0Y2FzZSAnd2lzaGxpc3QnOlxuXHRcdFx0XHRcdFx0XHRcdGlmIChjdXN0b21pemVyKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRldmVudCA9IGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5BRERfQ1VTVE9NSVpFUl9XSVNITElTVCgpO1xuXHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHR1cmwgPSBvcHRpb25zLndpc2hsaXN0VXJsO1xuXHRcdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0XHRjYXNlICdjYXJ0Jzpcblx0XHRcdFx0XHRcdFx0XHRpZiAoY3VzdG9taXplcikge1xuXHRcdFx0XHRcdFx0XHRcdFx0ZXZlbnQgPSBqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQUREX0NVU1RPTUlaRVJfQ0FSVCgpO1xuXHRcdFx0XHRcdFx0XHRcdFx0dXJsID0gb3B0aW9ucy5hZGRDYXJ0Q3VzdG9taXplclVybDtcblx0XHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdFx0dXJsID0gb3B0aW9ucy5hZGRDYXJ0VXJsO1xuXHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdFx0Y2FzZSAncHJpY2Vfb2ZmZXInOlxuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLmF0dHIoJ2FjdGlvbicsIG9wdGlvbnMucHJpY2VPZmZlclVybCkuYXR0cignbWV0aG9kJywgb3B0aW9ucy5wcmljZU9mZmVyTWV0aG9kKTtcblx0XHRcdFx0XHRcdFx0XHQkZm9ybS5vZmYoJ3N1Ym1pdCcpO1xuXHRcdFx0XHRcdFx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHRcdHJldHVybjtcblx0XHRcdFx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHRcdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0JHdpbmRvdy50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5TVElDS1lCT1hfQ09OVEVOVF9DSEFOR0UoKSk7XG5cdFx0XHRcdFx0XHRcdFx0fSwgMjUwKTtcblx0XHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0aWYgKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcdFx0XHRcdFx0ZGVmZXJyZWQuZG9uZShmdW5jdGlvbihjdXN0b21pemVyUmFuZG9tKSB7XG5cdFx0XHRcdFx0XHRcdFx0Zm9ybWRhdGFbY3VzdG9taXplclJhbmRvbV0gPSAwO1xuXHRcdFx0XHRcdFx0XHRcdF9hZGRUb1NvbWV3aGVyZShmb3JtZGF0YSwgJGZvcm0sIHVybCwgJHNlbGYpO1xuXHRcdFx0XHRcdFx0XHR9KS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdF9hZGRCdXR0b25TdGF0ZSgkc2VsZiwgJy1mYWlsJyk7XG5cdFx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFx0XHQkYm9keS50cmlnZ2VyKGV2ZW50LCBbeydkZWZlcnJlZCc6IGRlZmVycmVkLCAnZGF0YXNldCc6IGZvcm1kYXRhfV0pO1xuXHRcdFx0XHRcdFx0fSBlbHNlIGlmICh1cmwpIHtcblx0XHRcdFx0XHRcdFx0X2FkZFRvU29tZXdoZXJlKGZvcm1kYXRhLCAkZm9ybSwgdXJsLCAkc2VsZik7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0X2FkZEJ1dHRvblN0YXRlKCRzZWxmLCAnLWZhaWwnKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBLZXl1cCBoYW5kbGVyIGZvciBxdWFudGl0eSBpbnB1dCBmaWVsZFxuXHRcdCAqIFxuXHRcdCAqIEBwYXJhbSBlXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2tleXVwSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGNsZWFyVGltZW91dCh0aW1lb3V0KTtcblx0XHRcdFxuXHRcdFx0dGltZW91dCA9IHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdF9zdWJtaXRIYW5kbGVyLmNhbGwodGhpcywgZSk7XG5cdFx0XHR9LmJpbmQodGhpcyksIDMwMCk7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0XHR2YXIgJGZvcm1zID0gJHRoaXMuZmluZCgnZm9ybScpO1xuXG5cdFx0XHQkZm9ybXNcblx0XHRcdFx0Lm9uKCdzdWJtaXQnLCB7J3RhcmdldCc6ICdjYXJ0J30sIF9zdWJtaXRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgb3B0aW9ucy5jYXJ0QnV0dG9ucyArICc6bm90KC5pbmFjdGl2ZSknLCB7J3RhcmdldCc6ICdjYXJ0J30sIF9zdWJtaXRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgb3B0aW9ucy53aXNobGlzdEJ1dHRvbnMsIHsndGFyZ2V0JzogJ3dpc2hsaXN0J30sIF9zdWJtaXRIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrJywgb3B0aW9ucy5wcmljZU9mZmVyQnV0dG9ucywgeyd0YXJnZXQnOiAncHJpY2Vfb2ZmZXInfSwgX3N1Ym1pdEhhbmRsZXIpXG5cdFx0XHRcdC5vbignY2hhbmdlJywgb3B0aW9ucy5hdHRyaWJ1dGVzLCB7J3RhcmdldCc6ICdjaGVjayd9LCBfc3VibWl0SGFuZGxlcilcblx0XHRcdFx0Lm9uKCdibHVyJywgb3B0aW9ucy5xdWFudGl0eSwgeyd0YXJnZXQnOiAnY2hlY2snfSwgX3N1Ym1pdEhhbmRsZXIpXG5cdFx0XHRcdC5vbigna2V5dXAnLCBvcHRpb25zLnF1YW50aXR5LCB7J3RhcmdldCc6ICdjaGVjayd9LCBfa2V5dXBIYW5kbGVyKTtcblxuXHRcdFx0Ly8gRmFsbGJhY2sgaWYgdGhlIGJhY2tlbmQgcmVuZGVycyBpbmNvcnJlY3QgZGF0YVxuXHRcdFx0Ly8gb24gaW5pdGlhbCBwYWdlIGNhbGxcblx0XHRcdCRmb3Jtcy5ub3QoJy5uby1zdGF0dXMtY2hlY2snKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRfc3VibWl0SGFuZGxlci5jYWxsKCQodGhpcykpO1xuXHRcdFx0fSk7XG5cblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
