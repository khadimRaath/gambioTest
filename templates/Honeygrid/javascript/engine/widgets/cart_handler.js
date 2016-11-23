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
gambio.widgets.module(
	'cart_handler',

	[
		'form',
		'xhr',
		gambio.source + '/libs/events',
		gambio.source + '/libs/modal.ext-magnific',
		gambio.source + '/libs/modal'
	],

	function(data) {

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
				triggerAttrImagesTo: '#product_image_swiper, #product_thumbnail_swiper, '
				+ '#product_thumbnail_swiper_mobile',
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
					shareContent:'.share-cart-content-wrapper',
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
		var _addButtonState = function($target, state) {
			var timer = setTimeout(function() {
				$target.removeClass(options.processingClass + ' ' + options.processingClass + state);
			}, options.processingDuration);

			$target
				.data('timer', timer)
				.addClass(options.processingClass + state);
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
		var _stateManager = function(data, $form, disableButtons, showNoCombiSelectedMesssage) {

			// Remove the attribute images from the common content
			// so that it doesn't get rendered anymore. Then trigger
			// an event to the given selectors and deliver the
			// attrImages object
			if (options.attributImagesSwiper && data.attrImages && data.attrImages.length) {
				delete data.content.images;
				$(options.triggerAttrImagesTo)
					.trigger(jse.libs.template.events.SLIDES_UPDATE(), {attributes: data.attrImages});
			}

			// Set the messages given inside the data.content object
			$.each(data.content, function(i, v) {
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
							$element
								.addClass('hidden')
								.empty();
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
					$errorField
						.removeClass('hidden')
						.show();
				} else {
					$errorField
						.addClass('hidden')
						.hide();
					
					if (showNoCombiSelectedMesssage
						&& data.content.messageNoCombiSelected !== undefined
						&& data.content.messageNoCombiSelected) {
						if (data.content.messageNoCombiSelected.value) {
							$errorField
								.removeClass('hidden')
								.show();
						} else {
							$errorField
								.addClass('hidden')
								.hide();
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
		var _addToSomewhere = function(data, $form, url, $button) {

			if (!busy) {
				// only execute the ajax
				// if there is no pending ajax call
				busy = true;

				jse.libs.xhr.post({url: url, data: data}, true).done(function(result) {
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
									jse.libs.template.modal.info({title: result.title, content: result.msg});
									break;
								default:
									break;
							}
						}
					} catch (ignore) {
					}
					_addButtonState($button, '-success');
				}).fail(function() {
					_addButtonState($button, '-fail');
				}).always(function() {
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
		var _submitHandler = function(e) {
			if (e) {
				e.preventDefault();
			}

			var $self = $(this),
				$form = ($self.is('form')) ? $self : $self.closest('form'),
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
				formdata.target = (e && e.data && e.data.target) ? e.data.target : 'check';

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

					$self
						.removeClass(options.processingClass + '-success ' + options.processingClass + '-fail')
						.addClass(options.processingClass);
				}

				ajax = jse.libs.xhr.get({
					                        url: options.checkUrl + module,
					                        data: formdata
				                        }, true).done(function(result) {
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
								setTimeout(function() {
									$window.trigger(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE());
								}, 250);
								break;
						}

						if (event) {
							var deferred = $.Deferred();
							deferred.done(function(customizerRandom) {
								formdata[customizerRandom] = 0;
								_addToSomewhere(formdata, $form, url, $self);
							}).fail(function() {
								_addButtonState($self, '-fail');
							});
							$body.trigger(event, [{'deferred': deferred, 'dataset': formdata}]);
						} else if (url) {
							_addToSomewhere(formdata, $form, url, $self);
						}
					}

				}).fail(function() {
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
		var _keyupHandler = function(e) {
			clearTimeout(timeout);
			
			timeout = setTimeout(function() {
				_submitHandler.call(this, e);
			}.bind(this), 300);
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			var $forms = $this.find('form');

			$forms
				.on('submit', {'target': 'cart'}, _submitHandler)
				.on('click', options.cartButtons + ':not(.inactive)', {'target': 'cart'}, _submitHandler)
				.on('click', options.wishlistButtons, {'target': 'wishlist'}, _submitHandler)
				.on('click', options.priceOfferButtons, {'target': 'price_offer'}, _submitHandler)
				.on('change', options.attributes, {'target': 'check'}, _submitHandler)
				.on('blur', options.quantity, {'target': 'check'}, _submitHandler)
				.on('keyup', options.quantity, {'target': 'check'}, _keyupHandler);

			// Fallback if the backend renders incorrect data
			// on initial page call
			$forms.not('.no-status-check').each(function() {
				_submitHandler.call($(this));
			});

			done();
		};

		// Return data to widget engine
		return module;
	});
