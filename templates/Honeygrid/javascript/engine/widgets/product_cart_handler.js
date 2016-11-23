/* --------------------------------------------------------------
 product_cart_handler.js 2016-08-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Component that includes the functionality for
 * the add-to-cart, refresh and delete buttons
 * on the wishlist and cart
 */
gambio.widgets.module(
	'product_cart_handler',

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
			$window = $(window),
			$body = $('body'),
			$form = null,
			$updateTarget = null,
			$deleteField = null,
			$cartEmpty = null,
			$cartNotEmpty = null,
			deleteFieldName = null,
			action = null,
			busy = null,
			updateList = false,
			transition = null,
			active = {},
			defaults = {
				// Use an AJAX to update the form
				ajax: true,
				// Show an confirm-layer on deletion of an item
				confirmDelete: false,
				// Selector of the hidden field for the deletion entries
				deleteInput: '#field_cart_delete_products_id',
				// Trigger an event to that item on an successfull ajax (e.g. the shipping costs element)
				updateTarget: '.shipping-calculation',
				// The URL for the quantity check of the item
				checkUrl: 'shop.php?do=CheckQuantity',
				// If an URL is set, this one will be requests for status updates on tab focus
				updateUrl: 'shop.php?do=Cart',

				changeClass: 'has-changed', // Class that gets added if an input has changed
				errorClass: 'error', // Class that gets added to the row if an error has occured
				cartEmpty: '.cart-empty', // Show this selection if the cart is empty or hide it else
				cartNotEmpty: '.cart-not-empty', // Show this selection if the cart is not empty or hide it else
				classLoading: 'loading', // The class that gets added to an currently updating row
				actions: { // The actions that getting appended to the submit url on the different type of updates
					add: 'wishlist_to_cart',
					delete: 'update_product',
					refresh: 'update_wishlist'
				},
				ajaxActions: { // URLs for the ajax updates on the different actions
					add: 'shop.php?do=WishList/AddToCart',
					delete: 'shop.php?do=Cart/Delete',
					refresh: 'shop.php?do=Cart/Update'
				},
				selectorMapping: {
					buttons: '.shopping-cart-button',
					giftContent: '.gift-cart-content-wrapper',
					giftLayer: '.gift-cart-layer',
					shareContent:'.share-cart-content-wrapper',
					shareLayer: '.share-cart-layer',
					hiddenOptions: '#cart_quantity .hidden-options',
					message: '.global-error-messages',
					infoMessage: '.info-message',
					shippingInformation: '#shipping-information-layer',
					totals: '#cart_quantity .total-box',
					errorMsg: '.error-msg'
				}
			},
			options = $.extend(false, {}, defaults, data),
			module = {};

// ########## HELPER FUNCTIONS ##########

		/**
		 * Updates the form action to the type given
		 * in the options.actions object
		 * @param       {string}        type        The action name
		 * @private
		 */
		var _setAction = function(type) {
			if (options.ajax) {
				action = options.ajaxActions[type];
			} else if (options.actions && options.actions[type]) {
				action = action.replace(/(action=)[^\&]+/, '$1' + options.actions[type]);
				$form.attr('action', action);
			}
		};

		/**
		 * Helper function that updates the
		 * hidden data attributes with the current
		 * values of the input fields
		 * @param       {object}        $target     jQuery selection of the topmost container
		 * @private
		 */
		var _updateDataValues = function($target) {
			$target
				.find('input[type="text"]')
				.each(function() {
					var $self = $(this),
						value = $self.val();

					$self.data('oldValue', value);
				});
		};

		/**
		 * Helper function that restores the values
		 * stored by the _updateDataValues function
		 * @param       {object}        dataset     The data object of all targets that needs to be reset
		 * @private
		 */
		var _restoreDataValues = function(dataset) {
			// Reset each changed field given
			// by the dataset target
			$.each(dataset, function() {
				var value = this;

				value
					.target
					.find('.' + options.changeClass)
					.each(function() {
						var $self = $(this),
							name = $self.attr('name').replace('[]', ''),
							val = $self.data().oldValue;

						value[name][0] = val;
						$self
							.val(val)
							.removeClass(options.changeClass);
					});
			});
		};

		/**
		 * Helper function that generates an array of  datasets from the form. Each array item
		 * contains the data of one row (inclusive the attributes data from the form head belonging
		 * to the row). Additionally it adds the target-parameter to each dataset which contains
		 * the selection of the row,the current dataset belongs to.
		 *
		 * @param {object} $row The optional row selection the data gets from. If no selection is given, the form
		 * gets selected.
		 * @return {Array} The array with the datasets of each row
		 *
		 * @private
		 */
		var _generateFormdataObject = function($row) {
			var $target = ($row && $row.length) ? $row : $form,
				$rows = ($row && $row.length) ? $row : $form.find('.order-wishlist .item:gt(0)'),
				$hiddens = $form.find('.hidden-options input[type="hidden"]'),
				dataset = jse.libs.form.getData($target),
				result = [],
				tmpResult = null;

			$.each(dataset.products_id, function(i, v) {
				tmpResult = {};
				tmpResult.target = $rows.eq(i);

				// Store the data from the current row as a json
				$.each(dataset, function(key, value) {
					if (typeof value === 'object' && value[i] !== undefined) {
						// Store the value as an array to be compliant with the old API
						tmpResult[key] = [value[i]];
					}
				});

				// Get the hidden fields for the attributes
				// belonging to this row from the form head
				$hiddens
					.filter('[name^="id[' + v + '"], .force')
					.each(function() {
						var $self = $(this),
							name = $self.attr('name');

						tmpResult[name] = $self.val();
					});

				// Push the generated json to the final result array
				result.push(tmpResult);
			});

			return result;
		};

		/**
		 * Function that checks the form / the row if the combination
		 * and quantity is valid. It returns an promise which gets rejected
		 * if in the scope was an invalid value. In other cases it gets
		 * resolved. If it is detecting changes inside the form it can
		 * show an info layer to the user and / or revert the changes
		 * (depending on the caller parameters)
		 * @param       {boolean} showChanges   Show an info-layer if changes would be refused
		 * @param       {boolean} revertChanges Resets the form values with the one from the data attributes if true
		 * @param       {object}  formdata      Json that contains the data to check
		 * @return      {*}                     Returns a promise
		 * @private
		 */
		var _checkForm = function(showChanges, revertChanges, formdata) {

			var promises = [],
				hasChanged = false;

			// Get the complete form data if no row data is given
			formdata = formdata || _generateFormdataObject();

			// Check the formdata for changed values
			$.each(formdata, function() {
				var $changed = this.target.find('.' + options.changeClass);
				hasChanged = hasChanged || !!$changed.length;
				return !hasChanged;
			});

			/**
			 * Helper function that resets all form fields
			 * given by the formadta target to it's previous
			 * state (if specified) and afterwards validates
			 * the values
			 * @private
			 */
			var _revertChanges = function() {
				if (revertChanges) {
					_restoreDataValues(formdata);
				}

				// Check each dataset
				$.each(formdata, function() {
					var v = this,
						$target = v.target,
						localDeferred = $.Deferred(),
						product = v.products_id[0].split('x');

					promises.push(localDeferred);

					// Get the product id & combination
					v.products_id[0] = product[0];
					v.combination = product[1];

					// Delete the target element because ajax requests
					// will fail with a jQuery selection in the data json
					delete v.target;

					// Add the values for the checker process (without the arrays around the values)
					$.each(v, function(key, value) {
						if (typeof value === 'object' && value[0] !== undefined) {
							v[key] = value[0];
						}
					});

					// Check the row data
					jse.libs.xhr.ajax({url: options.checkUrl, data: v}, true).done(function(result) {

						// Render the result and get the correct success state
						// in case it was an article with a combination
						var status = result.success;
						if (v.combination !== undefined && result.status_code === 0) {
							jse.libs.template.helpers.fill(result.combination, $target, options.selectorMapping);
							status = false;
						} else {
							jse.libs.template.helpers.fill(result.quantity, $target, options.selectorMapping);
						}

						// Resolve or reject the deferred and add / remove error classes
						if (status) {
							$target.removeClass(options.errorClass);
							localDeferred.resolve();
						} else {
							$target.addClass(options.errorClass);
							localDeferred.reject();
						}
					});

				});
			};

			// If something has changed, show a layer if
			// showChanges is set to ask the user to refuse
			// the changes. If it gets refused the deferred
			// will be rejected.
			if (hasChanged) {
				if (!showChanges) {
					_revertChanges();
				} else {
					// Create an deferred that gets resolved
					// after the modal has closed
					var modalDeferred = $.Deferred();
					promises.push(modalDeferred);

					// Open a modal layer to confirm the refusal
					jse.libs.template.modal.confirm({
						content: jse.core.lang.translate('CART_WISHLIST_REFUSE', 'general'),
						title: jse.core.lang.translate('CART_WISHLIST_REFUSE_TITLE', 'general')
					}).done(_revertChanges).fail(function() {
						// If the revert is canceled, reject the deferred
						modalDeferred.reject();
					}).always(function() {
						if (!modalDeferred.isRejected) {
							modalDeferred.resolve();
						}
					});
				}
			} else {
				_revertChanges();
			}

			return $.when.apply(undefined, promises).promise();

		};

		/**
		 * Helper function that cleans up the process state
		 * (Needed especially after ajax requests, to be able
		 * to make further requests)
		 * @param       {string}        id              The product id that needs to be reseted
		 * @return     {Array.<T>}                     Returns an array without empty fields
		 * @private
		 */
		var _cleanupArray = function(id, $row) {
			delete active['product_' + id];
			$row.removeClass('loading');
			return active;
		};

		/**
		 * Helper function that does the general form update
		 * after an ajax request
		 * @param       {object}    $target         The jQuery selection of the target elements.
		 * @param       {object}    result          The result of the ajax request.
		 * @param       {string}    type            The executed action type.
		 * @private
		 */
		var _updateForm = function($target, result, type) {
			// Update the rest of the page
			jse.libs.template.helpers.fill(result.content, $body, options.selectorMapping);
			
			// Toggle info-messages visibility.
			$('.info-message').toggleClass('hidden', $('.info-message').text() === '');

			// Inform other widgets about the update
			$updateTarget.trigger(jse.libs.template.events.CART_UPDATED(), []);
			$body.trigger(jse.libs.template.events.CART_UPDATE(), (type === 'add'));

			// Update the hidden data attributes of that row
			_updateDataValues($target);

			if ($.isEmptyObject(result.products)) {
				// Hide the table if no products are at the list
				$cartNotEmpty.addClass('hidden');
				$cartEmpty.removeClass('hidden');
			} else {
				// Show the table if there are products at it
				$cartEmpty.addClass('hidden');
				$cartNotEmpty.removeClass('hidden');
			}

			// reinitialize widgets in updated DOM
			window.gambio.widgets.init($this);
		};

		/**
		 * Helper function that processes the list updates.
		 * Therefor it calls AJAX-requests (in case ajax is
		 * enabled) to the server to get the updated information
		 * about the table state. If ajax isn't enabled, it simply
		 * submits the form.
		 * @param       {object}        $target     The jQuery selection of the row that gets updated
		 * @param       {object}        dataset     The data collected from the target row in JSON format
		 * @param       {article}       article     The products id of the article in that row
		 * @param       {article}       type        The operation type can either be "add", "delete" or "refresh".
		 * @private
		 */
		var _executeAction = function($row, $target, dataset, article, type) {
			if (options.ajax) {
				// Delete the target element because ajax requests
				// will fail with a jQuery selection in the data json
				delete dataset.target;

				$row.trigger(jse.libs.template.events.TRANSITION(), transition);

				// Perform an ajax if the data is valid and the options for ajax is set
				jse.libs.xhr.ajax({url: action, data: dataset}, true).done(function(result) {
					// Update the product row
					var $markup = $(result.products['product_' + article] || '');
					$markup.removeClass(options.classLoading);
					$target.replaceWith($markup);
					_updateForm($target, result, type);
				}).always(function() {
					_cleanupArray(article, $row);
				});
			} else {
				// Cleanup the active array on fail / success
				// of the following submit. This is a fallback
				// if an other component would prevent the submit
				// in some cases, so that this script can perform
				// actions again
				var deferred = $.Deferred();
				deferred.always(function() {
					_cleanupArray(article, $row);
				});

				// Submit the form
				$form.trigger('submit', deferred);
			}
		};


// ########## EVENT HANDLER ##########

		/**
		 * Adds an class to the changed input
		 * field, so that it's styling shows
		 * that it wasn't refreshed till now
		 * @private
		 */
		var _changeHandler = function() {
			var $self = $(this),
				value = $self.val(),
				oldValue = $self.data().oldValue;

			if (value !== oldValue) {
				$self.addClass(options.changeClass);
			} else {
				$self.removeClass(options.changeClass);
			}
		};

		/**
		 * Handler that listens on click events on the
		 * buttons "refresh", "delete" & "add to cart".
		 * It validates the form / row and passes the
		 * the data to an submit execute funciton if valid
		 * @param       {object}    e       jQuery event object
		 * @private
		 */
		var _clickHandler = function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $self = $(this),
				$row = $self.closest('.item'),
				type = e.data.type,
				rowdata = _generateFormdataObject($row)[0],
				article = rowdata.products_id[0],
				$target = rowdata.target,
				title = $target.find('.product-title').text();

			// Add loading class
			$row.addClass('loading');

			// Check if there is no current process for this article
			// or in case it's no ajax call there is NO other process
			if ($.isEmptyObject(active) || (options.ajax && !active['product_' + article])) {
				active['product_' + article] = true;
				_setAction(type);

				switch (type) {
					case 'delete':
						// Update the form and the dataset with
						// the article id to delete
						$deleteField.val(article);
						rowdata[deleteFieldName] = [article];

						if (options.confirmDelete) {
							// Open a modal layer to confirm the deletion
							var modalTitle = jse.core.lang.translate('CART_WISHLIST_DELETE_TITLE', 'general'),
								modalMessage = jse.core.lang.translate('CART_WISHLIST_DELETE', 'general');

							jse.libs.template.modal.confirm({
								                                content: modalMessage,
								                                title: modalTitle
							                                }).done(function() {
								var deferred = $.Deferred();

								deferred.done(function() {
									_executeAction($row, $target, rowdata, article, type);
								});

								$body.trigger(jse.libs.template.events.WISHLIST_CART_DELETE(), [
									{
										'deferred': deferred,
										'dataset': rowdata
									}
								]);
							}).fail(function() {
								_cleanupArray(article, $row);
							});
						} else {
							var deferred = $.Deferred();

							deferred.done(function() {
								_executeAction($row, $target, rowdata, article, type);
							});

							$body.trigger(jse.libs.template.events.WISHLIST_CART_DELETE(), [
								{
									'deferred': deferred,
									'dataset': rowdata
								}
							]);
						}
						break;

					default:
						// In all other cases check if the form
						// has valid values and continue with the
						// done callback if valid
						_checkForm(false, false, [$.extend(true, {}, rowdata)])
							.done(function() {
								// Empty the delete hidden field in case it was set before
								$deleteField.val('');

								var event = null;

								if (type === 'add') {
									event = jse.libs.template.events.WISHLIST_TO_CART();
								}

								if (event) {
									var deferred = $.Deferred();

									deferred.done(function() {
										_executeAction($row, $target, rowdata, article, type);
									});

									$body.trigger(event, [{'deferred': deferred, 'dataset': rowdata}]);
								} else {
									_executeAction($row, $target, rowdata, article, type);
								}

							}).fail(function() {
							_cleanupArray(article, $row);
						});
						break;
				}
			}
		};

		/**
		 * Prevent the submit event that was triggerd
		 * by user or by script. If it was triggered
		 * by the user, check if it was an "Enter"-key
		 * submit from an input field. If so, execute
		 * the refresh functionality for that row.
		 * If the event was triggered by the script
		 * (identified by the data flag "d") check the
		 * whole form for errors. Only in case of valid
		 * data proceed the submit
		 * @param       {object}        e       jQuery event object
		 * @param       {boolean}       d       A flag that identifies that the submit was triggered by this script
		 * @private
		 */
		var _submitHandler = function(e, d) {

			// Prevent the default behaviour
			// in both cases
			e.preventDefault();
			e.stopPropagation();

			if (!d && e.originalEvent) {

				// Check if an input field has triggerd the submit event
				// and call the refresh handler
				var $source = $(e.originalEvent.explicitOriginalTarget);
				if ($source.length && $source.is('input[type="text"]')) {
					$source
						.closest('.item')
						.find('.button-refresh')
						.first()
						.trigger('click');
				}

			} else if (d) {

				// Check the whole form and only submit
				// it if it's valid
				_checkForm().done(function() {
					// Remove the submit event handler
					// on a successful validation and
					// trigger a submit again, so that the
					// browser executes it's default behavior
					$form
						.off('submit')
						.trigger('submit');

					// Resolve the deferred if given
					if (typeof d === 'object') {
						d.resolve();
					}
				}).fail(function() {
					// Reject the deferred if given
					if (typeof d === 'object') {
						d.reject();
					}
				});

			}
		};

		/**
		 * Event handler for clicking on the proceed
		 * button to get to the checkout process. It
		 * checks all items again if they contain valid
		 * data. Only if so, proceed
		 * @param       {object}        e       jQuery event object
		 * @private
		 */
		var _submitButtonHandler = function(e) {
			e.preventDefault();
			e.stopPropagation();

			var $self = $(this),
				destination = $self.attr('href');

			// Check if there is any other process running
			if ($.isEmptyObject(active) && !busy && !updateList) {
				busy = true;

				_checkForm(true, true).done(function() {
					location.href = destination;
				}).always(function() {
					busy = false;
				});
			}
		};

		/**
		 * Event handler that checks the form and
		 * resolves or rejects the delivered deferred
		 * (Used for external payment modules to
		 * check if the form is valid)
		 * @param       {object}    e               jQuery event object
		 * @param       {object}    d               JSON object with the event settings
		 * @private
		 */
		var _checkFormHandler = function(e, d) {
			e.stopPropagation();

			d = d || {};

			_checkForm(d.showChanges, d.revertChanges).done(function() {
				if (d.deferred) {
					d.deferred.resolve();
				}
			}).fail(function() {
				if (d.deferred) {
					d.deferred.reject();
				}
			});
		};

		/**
		 * Function that updates the list on focus of
		 * the window
		 * @private
		 */
		var _updateList = function() {
			updateList = true;
			jse.libs.xhr.ajax({url: options.updateUrl}, true).done(function(result) {
				// Init with he first line since this ist the heading
				var $lastScanned = $form.find('.order-wishlist .item').first(),
					$target = $();

				// Iterate through the products object and search for the
				// products inside the markup. If the product was found,
				// update the values, if not add the product row at the
				// correct position
				$.each(result.products, function(key, value) {
					var articleId = key.replace('product_', ''),
						$article = $form.find('input[name="products_id[]"][value="' + articleId + '"]'),
						$row = null;

					if (!$article.length) {
						// The article wasn't found on page
						// -> add it
						$row = $(value);
						$row.insertAfter($lastScanned);
					} else {
						// The article was found on page
						// -> update it
						$row = $article.closest('.item');

						var $qty = $row.find('input[name="cart_quantity[]"]'),
							oldQty = parseFloat($qty.data().oldValue),
							currentQty = parseFloat($qty.val()),
							newQty = parseFloat($(value).find('input[name="cart_quantity[]"]').val());

						$qty.data('oldValue', newQty);

						// Add or remove the changed classes depending on
						// the quantity changes and the on page stored values
						if (oldQty === currentQty && currentQty !== newQty) {
							$qty.addClass(options.changeClass);
						} else if (oldQty !== currentQty && currentQty === newQty) {
							$qty.removeClass(options.changeClass);
						}
					}

					$target.add($row);
					$lastScanned = $row;
				});

				// Update the rest of the form
				_updateForm($target, result);
			}).always(function() {
				updateList = false;
			});
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {

			$updateTarget = $(options.updateTarget);
			$cartEmpty = $(options.cartEmpty);
			$cartNotEmpty = $(options.cartNotEmpty);
			$deleteField = $(options.deleteInput);
			$form = $this.find('form').first();
			deleteFieldName = $deleteField.attr('name');
			action = $form.attr('action');
			transition = {open: true, classOpen: options.classLoading};

			// Sets the current value of the input
			// to an hidden data attribute
			_updateDataValues($form);

			$form
				.on('change', 'input[type="text"]', _changeHandler)
				.on('click.delete', '.button-delete', {'type': 'delete'}, _clickHandler)
				.on('click.refresh', '.button-refresh', {'type': 'refresh'}, _clickHandler)
				.on('click.addtocart', '.button-to-cart', {'type': 'add'}, _clickHandler)
				.on('click.submit', '.button-submit', {'type': 'submit'}, _submitButtonHandler)
				.on('submit', _submitHandler)
				.on(jse.libs.template.events.CHECK_CART(), _checkFormHandler);

			if (options.updateUrl) {
				$window.on('focus', _updateList);
			}

			done();
		};

		// Return data to widget engine
		return module;
	});
