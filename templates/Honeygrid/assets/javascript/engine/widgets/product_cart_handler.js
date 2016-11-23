'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

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
gambio.widgets.module('product_cart_handler', ['form', 'xhr', gambio.source + '/libs/events', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

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
			shareContent: '.share-cart-content-wrapper',
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
	var _setAction = function _setAction(type) {
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
	var _updateDataValues = function _updateDataValues($target) {
		$target.find('input[type="text"]').each(function () {
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
	var _restoreDataValues = function _restoreDataValues(dataset) {
		// Reset each changed field given
		// by the dataset target
		$.each(dataset, function () {
			var value = this;

			value.target.find('.' + options.changeClass).each(function () {
				var $self = $(this),
				    name = $self.attr('name').replace('[]', ''),
				    val = $self.data().oldValue;

				value[name][0] = val;
				$self.val(val).removeClass(options.changeClass);
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
	var _generateFormdataObject = function _generateFormdataObject($row) {
		var $target = $row && $row.length ? $row : $form,
		    $rows = $row && $row.length ? $row : $form.find('.order-wishlist .item:gt(0)'),
		    $hiddens = $form.find('.hidden-options input[type="hidden"]'),
		    dataset = jse.libs.form.getData($target),
		    result = [],
		    tmpResult = null;

		$.each(dataset.products_id, function (i, v) {
			tmpResult = {};
			tmpResult.target = $rows.eq(i);

			// Store the data from the current row as a json
			$.each(dataset, function (key, value) {
				if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value[i] !== undefined) {
					// Store the value as an array to be compliant with the old API
					tmpResult[key] = [value[i]];
				}
			});

			// Get the hidden fields for the attributes
			// belonging to this row from the form head
			$hiddens.filter('[name^="id[' + v + '"], .force').each(function () {
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
	var _checkForm = function _checkForm(showChanges, revertChanges, formdata) {

		var promises = [],
		    hasChanged = false;

		// Get the complete form data if no row data is given
		formdata = formdata || _generateFormdataObject();

		// Check the formdata for changed values
		$.each(formdata, function () {
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
		var _revertChanges = function _revertChanges() {
			if (revertChanges) {
				_restoreDataValues(formdata);
			}

			// Check each dataset
			$.each(formdata, function () {
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
				$.each(v, function (key, value) {
					if ((typeof value === 'undefined' ? 'undefined' : _typeof(value)) === 'object' && value[0] !== undefined) {
						v[key] = value[0];
					}
				});

				// Check the row data
				jse.libs.xhr.ajax({ url: options.checkUrl, data: v }, true).done(function (result) {

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
				}).done(_revertChanges).fail(function () {
					// If the revert is canceled, reject the deferred
					modalDeferred.reject();
				}).always(function () {
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
	var _cleanupArray = function _cleanupArray(id, $row) {
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
	var _updateForm = function _updateForm($target, result, type) {
		// Update the rest of the page
		jse.libs.template.helpers.fill(result.content, $body, options.selectorMapping);

		// Toggle info-messages visibility.
		$('.info-message').toggleClass('hidden', $('.info-message').text() === '');

		// Inform other widgets about the update
		$updateTarget.trigger(jse.libs.template.events.CART_UPDATED(), []);
		$body.trigger(jse.libs.template.events.CART_UPDATE(), type === 'add');

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
	var _executeAction = function _executeAction($row, $target, dataset, article, type) {
		if (options.ajax) {
			// Delete the target element because ajax requests
			// will fail with a jQuery selection in the data json
			delete dataset.target;

			$row.trigger(jse.libs.template.events.TRANSITION(), transition);

			// Perform an ajax if the data is valid and the options for ajax is set
			jse.libs.xhr.ajax({ url: action, data: dataset }, true).done(function (result) {
				// Update the product row
				var $markup = $(result.products['product_' + article] || '');
				$markup.removeClass(options.classLoading);
				$target.replaceWith($markup);
				_updateForm($target, result, type);
			}).always(function () {
				_cleanupArray(article, $row);
			});
		} else {
			// Cleanup the active array on fail / success
			// of the following submit. This is a fallback
			// if an other component would prevent the submit
			// in some cases, so that this script can perform
			// actions again
			var deferred = $.Deferred();
			deferred.always(function () {
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
	var _changeHandler = function _changeHandler() {
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
	var _clickHandler = function _clickHandler(e) {
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
		if ($.isEmptyObject(active) || options.ajax && !active['product_' + article]) {
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
						}).done(function () {
							var deferred = $.Deferred();

							deferred.done(function () {
								_executeAction($row, $target, rowdata, article, type);
							});

							$body.trigger(jse.libs.template.events.WISHLIST_CART_DELETE(), [{
								'deferred': deferred,
								'dataset': rowdata
							}]);
						}).fail(function () {
							_cleanupArray(article, $row);
						});
					} else {
						var deferred = $.Deferred();

						deferred.done(function () {
							_executeAction($row, $target, rowdata, article, type);
						});

						$body.trigger(jse.libs.template.events.WISHLIST_CART_DELETE(), [{
							'deferred': deferred,
							'dataset': rowdata
						}]);
					}
					break;

				default:
					// In all other cases check if the form
					// has valid values and continue with the
					// done callback if valid
					_checkForm(false, false, [$.extend(true, {}, rowdata)]).done(function () {
						// Empty the delete hidden field in case it was set before
						$deleteField.val('');

						var event = null;

						if (type === 'add') {
							event = jse.libs.template.events.WISHLIST_TO_CART();
						}

						if (event) {
							var deferred = $.Deferred();

							deferred.done(function () {
								_executeAction($row, $target, rowdata, article, type);
							});

							$body.trigger(event, [{ 'deferred': deferred, 'dataset': rowdata }]);
						} else {
							_executeAction($row, $target, rowdata, article, type);
						}
					}).fail(function () {
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
	var _submitHandler = function _submitHandler(e, d) {

		// Prevent the default behaviour
		// in both cases
		e.preventDefault();
		e.stopPropagation();

		if (!d && e.originalEvent) {

			// Check if an input field has triggerd the submit event
			// and call the refresh handler
			var $source = $(e.originalEvent.explicitOriginalTarget);
			if ($source.length && $source.is('input[type="text"]')) {
				$source.closest('.item').find('.button-refresh').first().trigger('click');
			}
		} else if (d) {

			// Check the whole form and only submit
			// it if it's valid
			_checkForm().done(function () {
				// Remove the submit event handler
				// on a successful validation and
				// trigger a submit again, so that the
				// browser executes it's default behavior
				$form.off('submit').trigger('submit');

				// Resolve the deferred if given
				if ((typeof d === 'undefined' ? 'undefined' : _typeof(d)) === 'object') {
					d.resolve();
				}
			}).fail(function () {
				// Reject the deferred if given
				if ((typeof d === 'undefined' ? 'undefined' : _typeof(d)) === 'object') {
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
	var _submitButtonHandler = function _submitButtonHandler(e) {
		e.preventDefault();
		e.stopPropagation();

		var $self = $(this),
		    destination = $self.attr('href');

		// Check if there is any other process running
		if ($.isEmptyObject(active) && !busy && !updateList) {
			busy = true;

			_checkForm(true, true).done(function () {
				location.href = destination;
			}).always(function () {
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
	var _checkFormHandler = function _checkFormHandler(e, d) {
		e.stopPropagation();

		d = d || {};

		_checkForm(d.showChanges, d.revertChanges).done(function () {
			if (d.deferred) {
				d.deferred.resolve();
			}
		}).fail(function () {
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
	var _updateList = function _updateList() {
		updateList = true;
		jse.libs.xhr.ajax({ url: options.updateUrl }, true).done(function (result) {
			// Init with he first line since this ist the heading
			var $lastScanned = $form.find('.order-wishlist .item').first(),
			    $target = $();

			// Iterate through the products object and search for the
			// products inside the markup. If the product was found,
			// update the values, if not add the product row at the
			// correct position
			$.each(result.products, function (key, value) {
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
		}).always(function () {
			updateList = false;
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$updateTarget = $(options.updateTarget);
		$cartEmpty = $(options.cartEmpty);
		$cartNotEmpty = $(options.cartNotEmpty);
		$deleteField = $(options.deleteInput);
		$form = $this.find('form').first();
		deleteFieldName = $deleteField.attr('name');
		action = $form.attr('action');
		transition = { open: true, classOpen: options.classLoading };

		// Sets the current value of the input
		// to an hidden data attribute
		_updateDataValues($form);

		$form.on('change', 'input[type="text"]', _changeHandler).on('click.delete', '.button-delete', { 'type': 'delete' }, _clickHandler).on('click.refresh', '.button-refresh', { 'type': 'refresh' }, _clickHandler).on('click.addtocart', '.button-to-cart', { 'type': 'add' }, _clickHandler).on('click.submit', '.button-submit', { 'type': 'submit' }, _submitButtonHandler).on('submit', _submitHandler).on(jse.libs.template.events.CHECK_CART(), _checkFormHandler);

		if (options.updateUrl) {
			$window.on('focus', _updateList);
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9jYXJ0X2hhbmRsZXIuanMiXSwibmFtZXMiOlsiZ2FtYmlvIiwid2lkZ2V0cyIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCIkd2luZG93Iiwid2luZG93IiwiJGJvZHkiLCIkZm9ybSIsIiR1cGRhdGVUYXJnZXQiLCIkZGVsZXRlRmllbGQiLCIkY2FydEVtcHR5IiwiJGNhcnROb3RFbXB0eSIsImRlbGV0ZUZpZWxkTmFtZSIsImFjdGlvbiIsImJ1c3kiLCJ1cGRhdGVMaXN0IiwidHJhbnNpdGlvbiIsImFjdGl2ZSIsImRlZmF1bHRzIiwiYWpheCIsImNvbmZpcm1EZWxldGUiLCJkZWxldGVJbnB1dCIsInVwZGF0ZVRhcmdldCIsImNoZWNrVXJsIiwidXBkYXRlVXJsIiwiY2hhbmdlQ2xhc3MiLCJlcnJvckNsYXNzIiwiY2FydEVtcHR5IiwiY2FydE5vdEVtcHR5IiwiY2xhc3NMb2FkaW5nIiwiYWN0aW9ucyIsImFkZCIsImRlbGV0ZSIsInJlZnJlc2giLCJhamF4QWN0aW9ucyIsInNlbGVjdG9yTWFwcGluZyIsImJ1dHRvbnMiLCJnaWZ0Q29udGVudCIsImdpZnRMYXllciIsInNoYXJlQ29udGVudCIsInNoYXJlTGF5ZXIiLCJoaWRkZW5PcHRpb25zIiwibWVzc2FnZSIsImluZm9NZXNzYWdlIiwic2hpcHBpbmdJbmZvcm1hdGlvbiIsInRvdGFscyIsImVycm9yTXNnIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRBY3Rpb24iLCJ0eXBlIiwicmVwbGFjZSIsImF0dHIiLCJfdXBkYXRlRGF0YVZhbHVlcyIsIiR0YXJnZXQiLCJmaW5kIiwiZWFjaCIsIiRzZWxmIiwidmFsdWUiLCJ2YWwiLCJfcmVzdG9yZURhdGFWYWx1ZXMiLCJkYXRhc2V0IiwidGFyZ2V0IiwibmFtZSIsIm9sZFZhbHVlIiwicmVtb3ZlQ2xhc3MiLCJfZ2VuZXJhdGVGb3JtZGF0YU9iamVjdCIsIiRyb3ciLCJsZW5ndGgiLCIkcm93cyIsIiRoaWRkZW5zIiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwicmVzdWx0IiwidG1wUmVzdWx0IiwicHJvZHVjdHNfaWQiLCJpIiwidiIsImVxIiwia2V5IiwidW5kZWZpbmVkIiwiZmlsdGVyIiwicHVzaCIsIl9jaGVja0Zvcm0iLCJzaG93Q2hhbmdlcyIsInJldmVydENoYW5nZXMiLCJmb3JtZGF0YSIsInByb21pc2VzIiwiaGFzQ2hhbmdlZCIsIiRjaGFuZ2VkIiwiX3JldmVydENoYW5nZXMiLCJsb2NhbERlZmVycmVkIiwiRGVmZXJyZWQiLCJwcm9kdWN0Iiwic3BsaXQiLCJjb21iaW5hdGlvbiIsInhociIsInVybCIsImRvbmUiLCJzdGF0dXMiLCJzdWNjZXNzIiwic3RhdHVzX2NvZGUiLCJ0ZW1wbGF0ZSIsImhlbHBlcnMiLCJmaWxsIiwicXVhbnRpdHkiLCJyZXNvbHZlIiwiYWRkQ2xhc3MiLCJyZWplY3QiLCJtb2RhbERlZmVycmVkIiwibW9kYWwiLCJjb25maXJtIiwiY29udGVudCIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwidGl0bGUiLCJmYWlsIiwiYWx3YXlzIiwiaXNSZWplY3RlZCIsIndoZW4iLCJhcHBseSIsInByb21pc2UiLCJfY2xlYW51cEFycmF5IiwiaWQiLCJfdXBkYXRlRm9ybSIsInRvZ2dsZUNsYXNzIiwidGV4dCIsInRyaWdnZXIiLCJldmVudHMiLCJDQVJUX1VQREFURUQiLCJDQVJUX1VQREFURSIsImlzRW1wdHlPYmplY3QiLCJwcm9kdWN0cyIsImluaXQiLCJfZXhlY3V0ZUFjdGlvbiIsImFydGljbGUiLCJUUkFOU0lUSU9OIiwiJG1hcmt1cCIsInJlcGxhY2VXaXRoIiwiZGVmZXJyZWQiLCJfY2hhbmdlSGFuZGxlciIsIl9jbGlja0hhbmRsZXIiLCJlIiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCJjbG9zZXN0Iiwicm93ZGF0YSIsIm1vZGFsVGl0bGUiLCJtb2RhbE1lc3NhZ2UiLCJXSVNITElTVF9DQVJUX0RFTEVURSIsImV2ZW50IiwiV0lTSExJU1RfVE9fQ0FSVCIsIl9zdWJtaXRIYW5kbGVyIiwiZCIsIm9yaWdpbmFsRXZlbnQiLCIkc291cmNlIiwiZXhwbGljaXRPcmlnaW5hbFRhcmdldCIsImlzIiwiZmlyc3QiLCJvZmYiLCJfc3VibWl0QnV0dG9uSGFuZGxlciIsImRlc3RpbmF0aW9uIiwibG9jYXRpb24iLCJocmVmIiwiX2NoZWNrRm9ybUhhbmRsZXIiLCJfdXBkYXRlTGlzdCIsIiRsYXN0U2Nhbm5lZCIsImFydGljbGVJZCIsIiRhcnRpY2xlIiwiaW5zZXJ0QWZ0ZXIiLCIkcXR5Iiwib2xkUXR5IiwicGFyc2VGbG9hdCIsImN1cnJlbnRRdHkiLCJuZXdRdHkiLCJvcGVuIiwiY2xhc3NPcGVuIiwib24iLCJDSEVDS19DQVJUIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0Msc0JBREQsRUFHQyxDQUNDLE1BREQsRUFFQyxLQUZELEVBR0NGLE9BQU9HLE1BQVAsR0FBZ0IsY0FIakIsRUFJQ0gsT0FBT0csTUFBUCxHQUFnQiwwQkFKakIsRUFLQ0gsT0FBT0csTUFBUCxHQUFnQixhQUxqQixDQUhELEVBV0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVGOztBQUVFLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsVUFBVUQsRUFBRUUsTUFBRixDQURYO0FBQUEsS0FFQ0MsUUFBUUgsRUFBRSxNQUFGLENBRlQ7QUFBQSxLQUdDSSxRQUFRLElBSFQ7QUFBQSxLQUlDQyxnQkFBZ0IsSUFKakI7QUFBQSxLQUtDQyxlQUFlLElBTGhCO0FBQUEsS0FNQ0MsYUFBYSxJQU5kO0FBQUEsS0FPQ0MsZ0JBQWdCLElBUGpCO0FBQUEsS0FRQ0Msa0JBQWtCLElBUm5CO0FBQUEsS0FTQ0MsU0FBUyxJQVRWO0FBQUEsS0FVQ0MsT0FBTyxJQVZSO0FBQUEsS0FXQ0MsYUFBYSxLQVhkO0FBQUEsS0FZQ0MsYUFBYSxJQVpkO0FBQUEsS0Fh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REwsRUFDZSxvQkFEZixFQUNxQ3JDLGNBRHJDLEVBRUVxQyxFQUZGLENBRUssY0FGTCxFQUVxQixnQkFGckIsRUFFdUMsRUFBQyxRQUFRLFFBQVQsRUFGdkMsRUFFMkRwQyxhQUYzRCxFQUdFb0MsRUFIRixDQUdLLGVBSEwsRUFHc0IsaUJBSHRCLEVBR3lDLEVBQUMsUUFBUSxTQUFULEVBSHpDLEVBRzhEcEMsYUFIOUQsRUFJRW9DLEVBSkYsQ0FJSyxpQkFKTCxFQUl3QixpQkFKeEIsRUFJMkMsRUFBQyxRQUFRLEtBQVQsRUFKM0MsRUFJNERwQyxhQUo1RCxFQUtFb0MsRUFMRixDQUtLLGNBTEwsRUFLcUIsZ0JBTHJCLEVBS3VDLEVBQUMsUUFBUSxRQUFULEVBTHZDLEVBSzJEakIsb0JBTDNELEVBTUVpQixFQU5GLENBTUssUUFOTCxFQU1lekIsY0FOZixFQU9FeUIsRUFQRixDQU9LN0csSUFBSUMsSUFBSixDQUFTZ0MsUUFBVCxDQUFrQjJCLE1BQWxCLENBQXlCa0QsVUFBekIsRUFQTCxFQU80Q2QsaUJBUDVDOztBQVNBLE1BQUl4SCxRQUFRdkIsU0FBWixFQUF1QjtBQUN0QnBCLFdBQVFnTCxFQUFSLENBQVcsT0FBWCxFQUFvQlosV0FBcEI7QUFDQTs7QUFFRHBFO0FBQ0EsRUE3QkQ7O0FBK0JBO0FBQ0EsUUFBT3JHLE1BQVA7QUFDQSxDQWp2QkYiLCJmaWxlIjoid2lkZ2V0cy9wcm9kdWN0X2NhcnRfaGFuZGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9jYXJ0X2hhbmRsZXIuanMgMjAxNi0wOC0xOFxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogQ29tcG9uZW50IHRoYXQgaW5jbHVkZXMgdGhlIGZ1bmN0aW9uYWxpdHkgZm9yXG4gKiB0aGUgYWRkLXRvLWNhcnQsIHJlZnJlc2ggYW5kIGRlbGV0ZSBidXR0b25zXG4gKiBvbiB0aGUgd2lzaGxpc3QgYW5kIGNhcnRcbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQncHJvZHVjdF9jYXJ0X2hhbmRsZXInLFxuXG5cdFtcblx0XHQnZm9ybScsXG5cdFx0J3hocicsXG5cdFx0Z2FtYmlvLnNvdXJjZSArICcvbGlicy9ldmVudHMnLFxuXHRcdGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwuZXh0LW1hZ25pZmljJyxcblx0XHRnYW1iaW8uc291cmNlICsgJy9saWJzL21vZGFsJ1xuXHRdLFxuXG5cdGZ1bmN0aW9uKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JHdpbmRvdyA9ICQod2luZG93KSxcblx0XHRcdCRib2R5ID0gJCgnYm9keScpLFxuXHRcdFx0JGZvcm0gPSBudWxsLFxuXHRcdFx0JHVwZGF0ZVRhcmdldCA9IG51bGwsXG5cdFx0XHQkZGVsZXRlRmllbGQgPSBudWxsLFxuXHRcdFx0JGNhcnRFbXB0eSA9IG51bGwsXG5cdFx0XHQkY2FydE5vdEVtcHR5ID0gbnVsbCxcblx0XHRcdGRlbGV0ZUZpZWxkTmFtZSA9IG51bGwsXG5cdFx0XHRhY3Rpb24gPSBudWxsLFxuXHRcdFx0YnVzeSA9IG51bGwsXG5cdFx0XHR1cGRhdGVMaXN0ID0gZmFsc2UsXG5cdFx0XHR0cmFuc2l0aW9uID0gbnVsbCxcblx0XHRcdGFjdGl2ZSA9IHt9LFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdC8vIFVzZSBhbiBBSkFYIHRvIHVwZGF0ZSB0aGUgZm9ybVxuXHRcdFx0XHRhamF4OiB0cnVlLFxuXHRcdFx0XHQvLyBTaG93IGFuIGNvbmZpcm0tbGF5ZXIgb24gZGVsZXRpb24gb2YgYW4gaXRlbVxuXHRcdFx0XHRjb25maXJtRGVsZXRlOiBmYWxzZSxcblx0XHRcdFx0Ly8gU2VsZWN0b3Igb2YgdGhlIGhpZGRlbiBmaWVsZCBmb3IgdGhlIGRlbGV0aW9uIGVudHJpZXNcblx0XHRcdFx0ZGVsZXRlSW5wdXQ6ICcjZmllbGRfY2FydF9kZWxldGVfcHJvZHVjdHNfaWQnLFxuXHRcdFx0XHQvLyBUcmlnZ2VyIGFuIGV2ZW50IHRvIHRoYXQgaXRlbSBvbiBhbiBzdWNjZXNzZnVsbCBhamF4IChlLmcuIHRoZSBzaGlwcGluZyBjb3N0cyBlbGVtZW50KVxuXHRcdFx0XHR1cGRhdGVUYXJnZXQ6ICcuc2hpcHBpbmctY2FsY3VsYXRpb24nLFxuXHRcdFx0XHQvLyBUaGUgVVJMIGZvciB0aGUgcXVhbnRpdHkgY2hlY2sgb2YgdGhlIGl0ZW1cblx0XHRcdFx0Y2hlY2tVcmw6ICdzaG9wLnBocD9kbz1DaGVja1F1YW50aXR5Jyxcblx0XHRcdFx0Ly8gSWYgYW4gVVJMIGlzIHNldCwgdGhpcyBvbmUgd2lsbCBiZSByZXF1ZXN0cyBmb3Igc3RhdHVzIHVwZGF0ZXMgb24gdGFiIGZvY3VzXG5cdFx0XHRcdHVwZGF0ZVVybDogJ3Nob3AucGhwP2RvPUNhcnQnLFxuXG5cdFx0XHRcdGNoYW5nZUNsYXNzOiAnaGFzLWNoYW5nZWQnLCAvLyBDbGFzcyB0aGF0IGdldHMgYWRkZWQgaWYgYW4gaW5wdXQgaGFzIGNoYW5nZWRcblx0XHRcdFx0ZXJyb3JDbGFzczogJ2Vycm9yJywgLy8gQ2xhc3MgdGhhdCBnZXRzIGFkZGVkIHRvIHRoZSByb3cgaWYgYW4gZXJyb3IgaGFzIG9jY3VyZWRcblx0XHRcdFx0Y2FydEVtcHR5OiAnLmNhcnQtZW1wdHknLCAvLyBTaG93IHRoaXMgc2VsZWN0aW9uIGlmIHRoZSBjYXJ0IGlzIGVtcHR5IG9yIGhpZGUgaXQgZWxzZVxuXHRcdFx0XHRjYXJ0Tm90RW1wdHk6ICcuY2FydC1ub3QtZW1wdHknLCAvLyBTaG93IHRoaXMgc2VsZWN0aW9uIGlmIHRoZSBjYXJ0IGlzIG5vdCBlbXB0eSBvciBoaWRlIGl0IGVsc2Vcblx0XHRcdFx0Y2xhc3NMb2FkaW5nOiAnbG9hZGluZycsIC8vIFRoZSBjbGFzcyB0aGF0IGdldHMgYWRkZWQgdG8gYW4gY3VycmVudGx5IHVwZGF0aW5nIHJvd1xuXHRcdFx0XHRhY3Rpb25zOiB7IC8vIFRoZSBhY3Rpb25zIHRoYXQgZ2V0dGluZyBhcHBlbmRlZCB0byB0aGUgc3VibWl0IHVybCBvbiB0aGUgZGlmZmVyZW50IHR5cGUgb2YgdXBkYXRlc1xuXHRcdFx0XHRcdGFkZDogJ3dpc2hsaXN0X3RvX2NhcnQnLFxuXHRcdFx0XHRcdGRlbGV0ZTogJ3VwZGF0ZV9wcm9kdWN0Jyxcblx0XHRcdFx0XHRyZWZyZXNoOiAndXBkYXRlX3dpc2hsaXN0J1xuXHRcdFx0XHR9LFxuXHRcdFx0XHRhamF4QWN0aW9uczogeyAvLyBVUkxzIGZvciB0aGUgYWpheCB1cGRhdGVzIG9uIHRoZSBkaWZmZXJlbnQgYWN0aW9uc1xuXHRcdFx0XHRcdGFkZDogJ3Nob3AucGhwP2RvPVdpc2hMaXN0L0FkZFRvQ2FydCcsXG5cdFx0XHRcdFx0ZGVsZXRlOiAnc2hvcC5waHA/ZG89Q2FydC9EZWxldGUnLFxuXHRcdFx0XHRcdHJlZnJlc2g6ICdzaG9wLnBocD9kbz1DYXJ0L1VwZGF0ZSdcblx0XHRcdFx0fSxcblx0XHRcdFx0c2VsZWN0b3JNYXBwaW5nOiB7XG5cdFx0XHRcdFx0YnV0dG9uczogJy5zaG9wcGluZy1jYXJ0LWJ1dHRvbicsXG5cdFx0XHRcdFx0Z2lmdENvbnRlbnQ6ICcuZ2lmdC1jYXJ0LWNvbnRlbnQtd3JhcHBlcicsXG5cdFx0XHRcdFx0Z2lmdExheWVyOiAnLmdpZnQtY2FydC1sYXllcicsXG5cdFx0XHRcdFx0c2hhcmVDb250ZW50Oicuc2hhcmUtY2FydC1jb250ZW50LXdyYXBwZXInLFxuXHRcdFx0XHRcdHNoYXJlTGF5ZXI6ICcuc2hhcmUtY2FydC1sYXllcicsXG5cdFx0XHRcdFx0aGlkZGVuT3B0aW9uczogJyNjYXJ0X3F1YW50aXR5IC5oaWRkZW4tb3B0aW9ucycsXG5cdFx0XHRcdFx0bWVzc2FnZTogJy5nbG9iYWwtZXJyb3ItbWVzc2FnZXMnLFxuXHRcdFx0XHRcdGluZm9NZXNzYWdlOiAnLmluZm8tbWVzc2FnZScsXG5cdFx0XHRcdFx0c2hpcHBpbmdJbmZvcm1hdGlvbjogJyNzaGlwcGluZy1pbmZvcm1hdGlvbi1sYXllcicsXG5cdFx0XHRcdFx0dG90YWxzOiAnI2NhcnRfcXVhbnRpdHkgLnRvdGFsLWJveCcsXG5cdFx0XHRcdFx0ZXJyb3JNc2c6ICcuZXJyb3ItbXNnJ1xuXHRcdFx0XHR9XG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKGZhbHNlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cbi8vICMjIyMjIyMjIyMgSEVMUEVSIEZVTkNUSU9OUyAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBVcGRhdGVzIHRoZSBmb3JtIGFjdGlvbiB0byB0aGUgdHlwZSBnaXZlblxuXHRcdCAqIGluIHRoZSBvcHRpb25zLmFjdGlvbnMgb2JqZWN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtzdHJpbmd9ICAgICAgICB0eXBlICAgICAgICBUaGUgYWN0aW9uIG5hbWVcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc2V0QWN0aW9uID0gZnVuY3Rpb24odHlwZSkge1xuXHRcdFx0aWYgKG9wdGlvbnMuYWpheCkge1xuXHRcdFx0XHRhY3Rpb24gPSBvcHRpb25zLmFqYXhBY3Rpb25zW3R5cGVdO1xuXHRcdFx0fSBlbHNlIGlmIChvcHRpb25zLmFjdGlvbnMgJiYgb3B0aW9ucy5hY3Rpb25zW3R5cGVdKSB7XG5cdFx0XHRcdGFjdGlvbiA9IGFjdGlvbi5yZXBsYWNlKC8oYWN0aW9uPSlbXlxcJl0rLywgJyQxJyArIG9wdGlvbnMuYWN0aW9uc1t0eXBlXSk7XG5cdFx0XHRcdCRmb3JtLmF0dHIoJ2FjdGlvbicsIGFjdGlvbik7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEhlbHBlciBmdW5jdGlvbiB0aGF0IHVwZGF0ZXMgdGhlXG5cdFx0ICogaGlkZGVuIGRhdGEgYXR0cmlidXRlcyB3aXRoIHRoZSBjdXJyZW50XG5cdFx0ICogdmFsdWVzIG9mIHRoZSBpbnB1dCBmaWVsZHNcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgICR0YXJnZXQgICAgIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIHRvcG1vc3QgY29udGFpbmVyXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3VwZGF0ZURhdGFWYWx1ZXMgPSBmdW5jdGlvbigkdGFyZ2V0KSB7XG5cdFx0XHQkdGFyZ2V0XG5cdFx0XHRcdC5maW5kKCdpbnB1dFt0eXBlPVwidGV4dFwiXScpXG5cdFx0XHRcdC5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdFx0XHR2YWx1ZSA9ICRzZWxmLnZhbCgpO1xuXG5cdFx0XHRcdFx0JHNlbGYuZGF0YSgnb2xkVmFsdWUnLCB2YWx1ZSk7XG5cdFx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCByZXN0b3JlcyB0aGUgdmFsdWVzXG5cdFx0ICogc3RvcmVkIGJ5IHRoZSBfdXBkYXRlRGF0YVZhbHVlcyBmdW5jdGlvblxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZGF0YXNldCAgICAgVGhlIGRhdGEgb2JqZWN0IG9mIGFsbCB0YXJnZXRzIHRoYXQgbmVlZHMgdG8gYmUgcmVzZXRcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfcmVzdG9yZURhdGFWYWx1ZXMgPSBmdW5jdGlvbihkYXRhc2V0KSB7XG5cdFx0XHQvLyBSZXNldCBlYWNoIGNoYW5nZWQgZmllbGQgZ2l2ZW5cblx0XHRcdC8vIGJ5IHRoZSBkYXRhc2V0IHRhcmdldFxuXHRcdFx0JC5lYWNoKGRhdGFzZXQsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgdmFsdWUgPSB0aGlzO1xuXG5cdFx0XHRcdHZhbHVlXG5cdFx0XHRcdFx0LnRhcmdldFxuXHRcdFx0XHRcdC5maW5kKCcuJyArIG9wdGlvbnMuY2hhbmdlQ2xhc3MpXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHRuYW1lID0gJHNlbGYuYXR0cignbmFtZScpLnJlcGxhY2UoJ1tdJywgJycpLFxuXHRcdFx0XHRcdFx0XHR2YWwgPSAkc2VsZi5kYXRhKCkub2xkVmFsdWU7XG5cblx0XHRcdFx0XHRcdHZhbHVlW25hbWVdWzBdID0gdmFsO1xuXHRcdFx0XHRcdFx0JHNlbGZcblx0XHRcdFx0XHRcdFx0LnZhbCh2YWwpXG5cdFx0XHRcdFx0XHRcdC5yZW1vdmVDbGFzcyhvcHRpb25zLmNoYW5nZUNsYXNzKTtcblx0XHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBnZW5lcmF0ZXMgYW4gYXJyYXkgb2YgIGRhdGFzZXRzIGZyb20gdGhlIGZvcm0uIEVhY2ggYXJyYXkgaXRlbVxuXHRcdCAqIGNvbnRhaW5zIHRoZSBkYXRhIG9mIG9uZSByb3cgKGluY2x1c2l2ZSB0aGUgYXR0cmlidXRlcyBkYXRhIGZyb20gdGhlIGZvcm0gaGVhZCBiZWxvbmdpbmdcblx0XHQgKiB0byB0aGUgcm93KS4gQWRkaXRpb25hbGx5IGl0IGFkZHMgdGhlIHRhcmdldC1wYXJhbWV0ZXIgdG8gZWFjaCBkYXRhc2V0IHdoaWNoIGNvbnRhaW5zXG5cdFx0ICogdGhlIHNlbGVjdGlvbiBvZiB0aGUgcm93LHRoZSBjdXJyZW50IGRhdGFzZXQgYmVsb25ncyB0by5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSAkcm93IFRoZSBvcHRpb25hbCByb3cgc2VsZWN0aW9uIHRoZSBkYXRhIGdldHMgZnJvbS4gSWYgbm8gc2VsZWN0aW9uIGlzIGdpdmVuLCB0aGUgZm9ybVxuXHRcdCAqIGdldHMgc2VsZWN0ZWQuXG5cdFx0ICogQHJldHVybiB7QXJyYXl9IFRoZSBhcnJheSB3aXRoIHRoZSBkYXRhc2V0cyBvZiBlYWNoIHJvd1xuXHRcdCAqXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2dlbmVyYXRlRm9ybWRhdGFPYmplY3QgPSBmdW5jdGlvbigkcm93KSB7XG5cdFx0XHR2YXIgJHRhcmdldCA9ICgkcm93ICYmICRyb3cubGVuZ3RoKSA/ICRyb3cgOiAkZm9ybSxcblx0XHRcdFx0JHJvd3MgPSAoJHJvdyAmJiAkcm93Lmxlbmd0aCkgPyAkcm93IDogJGZvcm0uZmluZCgnLm9yZGVyLXdpc2hsaXN0IC5pdGVtOmd0KDApJyksXG5cdFx0XHRcdCRoaWRkZW5zID0gJGZvcm0uZmluZCgnLmhpZGRlbi1vcHRpb25zIGlucHV0W3R5cGU9XCJoaWRkZW5cIl0nKSxcblx0XHRcdFx0ZGF0YXNldCA9IGpzZS5saWJzLmZvcm0uZ2V0RGF0YSgkdGFyZ2V0KSxcblx0XHRcdFx0cmVzdWx0ID0gW10sXG5cdFx0XHRcdHRtcFJlc3VsdCA9IG51bGw7XG5cblx0XHRcdCQuZWFjaChkYXRhc2V0LnByb2R1Y3RzX2lkLCBmdW5jdGlvbihpLCB2KSB7XG5cdFx0XHRcdHRtcFJlc3VsdCA9IHt9O1xuXHRcdFx0XHR0bXBSZXN1bHQudGFyZ2V0ID0gJHJvd3MuZXEoaSk7XG5cblx0XHRcdFx0Ly8gU3RvcmUgdGhlIGRhdGEgZnJvbSB0aGUgY3VycmVudCByb3cgYXMgYSBqc29uXG5cdFx0XHRcdCQuZWFjaChkYXRhc2V0LCBmdW5jdGlvbihrZXksIHZhbHVlKSB7XG5cdFx0XHRcdFx0aWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWVbaV0gIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRcdFx0Ly8gU3RvcmUgdGhlIHZhbHVlIGFzIGFuIGFycmF5IHRvIGJlIGNvbXBsaWFudCB3aXRoIHRoZSBvbGQgQVBJXG5cdFx0XHRcdFx0XHR0bXBSZXN1bHRba2V5XSA9IFt2YWx1ZVtpXV07XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblxuXHRcdFx0XHQvLyBHZXQgdGhlIGhpZGRlbiBmaWVsZHMgZm9yIHRoZSBhdHRyaWJ1dGVzXG5cdFx0XHRcdC8vIGJlbG9uZ2luZyB0byB0aGlzIHJvdyBmcm9tIHRoZSBmb3JtIGhlYWRcblx0XHRcdFx0JGhpZGRlbnNcblx0XHRcdFx0XHQuZmlsdGVyKCdbbmFtZV49XCJpZFsnICsgdiArICdcIl0sIC5mb3JjZScpXG5cdFx0XHRcdFx0LmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHR2YXIgJHNlbGYgPSAkKHRoaXMpLFxuXHRcdFx0XHRcdFx0XHRuYW1lID0gJHNlbGYuYXR0cignbmFtZScpO1xuXG5cdFx0XHRcdFx0XHR0bXBSZXN1bHRbbmFtZV0gPSAkc2VsZi52YWwoKTtcblx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHQvLyBQdXNoIHRoZSBnZW5lcmF0ZWQganNvbiB0byB0aGUgZmluYWwgcmVzdWx0IGFycmF5XG5cdFx0XHRcdHJlc3VsdC5wdXNoKHRtcFJlc3VsdCk7XG5cdFx0XHR9KTtcblxuXHRcdFx0cmV0dXJuIHJlc3VsdDtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCBjaGVja3MgdGhlIGZvcm0gLyB0aGUgcm93IGlmIHRoZSBjb21iaW5hdGlvblxuXHRcdCAqIGFuZCBxdWFudGl0eSBpcyB2YWxpZC4gSXQgcmV0dXJucyBhbiBwcm9taXNlIHdoaWNoIGdldHMgcmVqZWN0ZWRcblx0XHQgKiBpZiBpbiB0aGUgc2NvcGUgd2FzIGFuIGludmFsaWQgdmFsdWUuIEluIG90aGVyIGNhc2VzIGl0IGdldHNcblx0XHQgKiByZXNvbHZlZC4gSWYgaXQgaXMgZGV0ZWN0aW5nIGNoYW5nZXMgaW5zaWRlIHRoZSBmb3JtIGl0IGNhblxuXHRcdCAqIHNob3cgYW4gaW5mbyBsYXllciB0byB0aGUgdXNlciBhbmQgLyBvciByZXZlcnQgdGhlIGNoYW5nZXNcblx0XHQgKiAoZGVwZW5kaW5nIG9uIHRoZSBjYWxsZXIgcGFyYW1ldGVycylcblx0XHQgKiBAcGFyYW0gICAgICAge2Jvb2xlYW59IHNob3dDaGFuZ2VzICAgU2hvdyBhbiBpbmZvLWxheWVyIGlmIGNoYW5nZXMgd291bGQgYmUgcmVmdXNlZFxuXHRcdCAqIEBwYXJhbSAgICAgICB7Ym9vbGVhbn0gcmV2ZXJ0Q2hhbmdlcyBSZXNldHMgdGhlIGZvcm0gdmFsdWVzIHdpdGggdGhlIG9uZSBmcm9tIHRoZSBkYXRhIGF0dHJpYnV0ZXMgaWYgdHJ1ZVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgZm9ybWRhdGEgICAgICBKc29uIHRoYXQgY29udGFpbnMgdGhlIGRhdGEgdG8gY2hlY2tcblx0XHQgKiBAcmV0dXJuICAgICAgeyp9ICAgICAgICAgICAgICAgICAgICAgUmV0dXJucyBhIHByb21pc2Vcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hlY2tGb3JtID0gZnVuY3Rpb24oc2hvd0NoYW5nZXMsIHJldmVydENoYW5nZXMsIGZvcm1kYXRhKSB7XG5cblx0XHRcdHZhciBwcm9taXNlcyA9IFtdLFxuXHRcdFx0XHRoYXNDaGFuZ2VkID0gZmFsc2U7XG5cblx0XHRcdC8vIEdldCB0aGUgY29tcGxldGUgZm9ybSBkYXRhIGlmIG5vIHJvdyBkYXRhIGlzIGdpdmVuXG5cdFx0XHRmb3JtZGF0YSA9IGZvcm1kYXRhIHx8IF9nZW5lcmF0ZUZvcm1kYXRhT2JqZWN0KCk7XG5cblx0XHRcdC8vIENoZWNrIHRoZSBmb3JtZGF0YSBmb3IgY2hhbmdlZCB2YWx1ZXNcblx0XHRcdCQuZWFjaChmb3JtZGF0YSwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkY2hhbmdlZCA9IHRoaXMudGFyZ2V0LmZpbmQoJy4nICsgb3B0aW9ucy5jaGFuZ2VDbGFzcyk7XG5cdFx0XHRcdGhhc0NoYW5nZWQgPSBoYXNDaGFuZ2VkIHx8ICEhJGNoYW5nZWQubGVuZ3RoO1xuXHRcdFx0XHRyZXR1cm4gIWhhc0NoYW5nZWQ7XG5cdFx0XHR9KTtcblxuXHRcdFx0LyoqXG5cdFx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCByZXNldHMgYWxsIGZvcm0gZmllbGRzXG5cdFx0XHQgKiBnaXZlbiBieSB0aGUgZm9ybWFkdGEgdGFyZ2V0IHRvIGl0J3MgcHJldmlvdXNcblx0XHRcdCAqIHN0YXRlIChpZiBzcGVjaWZpZWQpIGFuZCBhZnRlcndhcmRzIHZhbGlkYXRlc1xuXHRcdFx0ICogdGhlIHZhbHVlc1xuXHRcdFx0ICogQHByaXZhdGVcblx0XHRcdCAqL1xuXHRcdFx0dmFyIF9yZXZlcnRDaGFuZ2VzID0gZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmIChyZXZlcnRDaGFuZ2VzKSB7XG5cdFx0XHRcdFx0X3Jlc3RvcmVEYXRhVmFsdWVzKGZvcm1kYXRhKTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdC8vIENoZWNrIGVhY2ggZGF0YXNldFxuXHRcdFx0XHQkLmVhY2goZm9ybWRhdGEsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciB2ID0gdGhpcyxcblx0XHRcdFx0XHRcdCR0YXJnZXQgPSB2LnRhcmdldCxcblx0XHRcdFx0XHRcdGxvY2FsRGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRcdFx0XHRwcm9kdWN0ID0gdi5wcm9kdWN0c19pZFswXS5zcGxpdCgneCcpO1xuXG5cdFx0XHRcdFx0cHJvbWlzZXMucHVzaChsb2NhbERlZmVycmVkKTtcblxuXHRcdFx0XHRcdC8vIEdldCB0aGUgcHJvZHVjdCBpZCAmIGNvbWJpbmF0aW9uXG5cdFx0XHRcdFx0di5wcm9kdWN0c19pZFswXSA9IHByb2R1Y3RbMF07XG5cdFx0XHRcdFx0di5jb21iaW5hdGlvbiA9IHByb2R1Y3RbMV07XG5cblx0XHRcdFx0XHQvLyBEZWxldGUgdGhlIHRhcmdldCBlbGVtZW50IGJlY2F1c2UgYWpheCByZXF1ZXN0c1xuXHRcdFx0XHRcdC8vIHdpbGwgZmFpbCB3aXRoIGEgalF1ZXJ5IHNlbGVjdGlvbiBpbiB0aGUgZGF0YSBqc29uXG5cdFx0XHRcdFx0ZGVsZXRlIHYudGFyZ2V0O1xuXG5cdFx0XHRcdFx0Ly8gQWRkIHRoZSB2YWx1ZXMgZm9yIHRoZSBjaGVja2VyIHByb2Nlc3MgKHdpdGhvdXQgdGhlIGFycmF5cyBhcm91bmQgdGhlIHZhbHVlcylcblx0XHRcdFx0XHQkLmVhY2godiwgZnVuY3Rpb24oa2V5LCB2YWx1ZSkge1xuXHRcdFx0XHRcdFx0aWYgKHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWVbMF0gIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRcdFx0XHR2W2tleV0gPSB2YWx1ZVswXTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHRcdC8vIENoZWNrIHRoZSByb3cgZGF0YVxuXHRcdFx0XHRcdGpzZS5saWJzLnhoci5hamF4KHt1cmw6IG9wdGlvbnMuY2hlY2tVcmwsIGRhdGE6IHZ9LCB0cnVlKS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXG5cdFx0XHRcdFx0XHQvLyBSZW5kZXIgdGhlIHJlc3VsdCBhbmQgZ2V0IHRoZSBjb3JyZWN0IHN1Y2Nlc3Mgc3RhdGVcblx0XHRcdFx0XHRcdC8vIGluIGNhc2UgaXQgd2FzIGFuIGFydGljbGUgd2l0aCBhIGNvbWJpbmF0aW9uXG5cdFx0XHRcdFx0XHR2YXIgc3RhdHVzID0gcmVzdWx0LnN1Y2Nlc3M7XG5cdFx0XHRcdFx0XHRpZiAodi5jb21iaW5hdGlvbiAhPT0gdW5kZWZpbmVkICYmIHJlc3VsdC5zdGF0dXNfY29kZSA9PT0gMCkge1xuXHRcdFx0XHRcdFx0XHRqc2UubGlicy50ZW1wbGF0ZS5oZWxwZXJzLmZpbGwocmVzdWx0LmNvbWJpbmF0aW9uLCAkdGFyZ2V0LCBvcHRpb25zLnNlbGVjdG9yTWFwcGluZyk7XG5cdFx0XHRcdFx0XHRcdHN0YXR1cyA9IGZhbHNlO1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0anNlLmxpYnMudGVtcGxhdGUuaGVscGVycy5maWxsKHJlc3VsdC5xdWFudGl0eSwgJHRhcmdldCwgb3B0aW9ucy5zZWxlY3Rvck1hcHBpbmcpO1xuXHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHQvLyBSZXNvbHZlIG9yIHJlamVjdCB0aGUgZGVmZXJyZWQgYW5kIGFkZCAvIHJlbW92ZSBlcnJvciBjbGFzc2VzXG5cdFx0XHRcdFx0XHRpZiAoc3RhdHVzKSB7XG5cdFx0XHRcdFx0XHRcdCR0YXJnZXQucmVtb3ZlQ2xhc3Mob3B0aW9ucy5lcnJvckNsYXNzKTtcblx0XHRcdFx0XHRcdFx0bG9jYWxEZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHQkdGFyZ2V0LmFkZENsYXNzKG9wdGlvbnMuZXJyb3JDbGFzcyk7XG5cdFx0XHRcdFx0XHRcdGxvY2FsRGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fSk7XG5cblx0XHRcdFx0fSk7XG5cdFx0XHR9O1xuXG5cdFx0XHQvLyBJZiBzb21ldGhpbmcgaGFzIGNoYW5nZWQsIHNob3cgYSBsYXllciBpZlxuXHRcdFx0Ly8gc2hvd0NoYW5nZXMgaXMgc2V0IHRvIGFzayB0aGUgdXNlciB0byByZWZ1c2Vcblx0XHRcdC8vIHRoZSBjaGFuZ2VzLiBJZiBpdCBnZXRzIHJlZnVzZWQgdGhlIGRlZmVycmVkXG5cdFx0XHQvLyB3aWxsIGJlIHJlamVjdGVkLlxuXHRcdFx0aWYgKGhhc0NoYW5nZWQpIHtcblx0XHRcdFx0aWYgKCFzaG93Q2hhbmdlcykge1xuXHRcdFx0XHRcdF9yZXZlcnRDaGFuZ2VzKCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0Ly8gQ3JlYXRlIGFuIGRlZmVycmVkIHRoYXQgZ2V0cyByZXNvbHZlZFxuXHRcdFx0XHRcdC8vIGFmdGVyIHRoZSBtb2RhbCBoYXMgY2xvc2VkXG5cdFx0XHRcdFx0dmFyIG1vZGFsRGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcdFx0cHJvbWlzZXMucHVzaChtb2RhbERlZmVycmVkKTtcblxuXHRcdFx0XHRcdC8vIE9wZW4gYSBtb2RhbCBsYXllciB0byBjb25maXJtIHRoZSByZWZ1c2FsXG5cdFx0XHRcdFx0anNlLmxpYnMudGVtcGxhdGUubW9kYWwuY29uZmlybSh7XG5cdFx0XHRcdFx0XHRjb250ZW50OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQ0FSVF9XSVNITElTVF9SRUZVU0UnLCAnZ2VuZXJhbCcpLFxuXHRcdFx0XHRcdFx0dGl0bGU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdDQVJUX1dJU0hMSVNUX1JFRlVTRV9USVRMRScsICdnZW5lcmFsJylcblx0XHRcdFx0XHR9KS5kb25lKF9yZXZlcnRDaGFuZ2VzKS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0Ly8gSWYgdGhlIHJldmVydCBpcyBjYW5jZWxlZCwgcmVqZWN0IHRoZSBkZWZlcnJlZFxuXHRcdFx0XHRcdFx0bW9kYWxEZWZlcnJlZC5yZWplY3QoKTtcblx0XHRcdFx0XHR9KS5hbHdheXMoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRpZiAoIW1vZGFsRGVmZXJyZWQuaXNSZWplY3RlZCkge1xuXHRcdFx0XHRcdFx0XHRtb2RhbERlZmVycmVkLnJlc29sdmUoKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0X3JldmVydENoYW5nZXMoKTtcblx0XHRcdH1cblxuXHRcdFx0cmV0dXJuICQud2hlbi5hcHBseSh1bmRlZmluZWQsIHByb21pc2VzKS5wcm9taXNlKCk7XG5cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGVscGVyIGZ1bmN0aW9uIHRoYXQgY2xlYW5zIHVwIHRoZSBwcm9jZXNzIHN0YXRlXG5cdFx0ICogKE5lZWRlZCBlc3BlY2lhbGx5IGFmdGVyIGFqYXggcmVxdWVzdHMsIHRvIGJlIGFibGVcblx0XHQgKiB0byBtYWtlIGZ1cnRoZXIgcmVxdWVzdHMpXG5cdFx0ICogQHBhcmFtICAgICAgIHtzdHJpbmd9ICAgICAgICBpZCAgICAgICAgICAgICAgVGhlIHByb2R1Y3QgaWQgdGhhdCBuZWVkcyB0byBiZSByZXNldGVkXG5cdFx0ICogQHJldHVybiAgICAge0FycmF5LjxUPn0gICAgICAgICAgICAgICAgICAgICBSZXR1cm5zIGFuIGFycmF5IHdpdGhvdXQgZW1wdHkgZmllbGRzXG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NsZWFudXBBcnJheSA9IGZ1bmN0aW9uKGlkLCAkcm93KSB7XG5cdFx0XHRkZWxldGUgYWN0aXZlWydwcm9kdWN0XycgKyBpZF07XG5cdFx0XHQkcm93LnJlbW92ZUNsYXNzKCdsb2FkaW5nJyk7XG5cdFx0XHRyZXR1cm4gYWN0aXZlO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBkb2VzIHRoZSBnZW5lcmFsIGZvcm0gdXBkYXRlXG5cdFx0ICogYWZ0ZXIgYW4gYWpheCByZXF1ZXN0XG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICR0YXJnZXQgICAgICAgICBUaGUgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgdGFyZ2V0IGVsZW1lbnRzLlxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICByZXN1bHQgICAgICAgICAgVGhlIHJlc3VsdCBvZiB0aGUgYWpheCByZXF1ZXN0LlxuXHRcdCAqIEBwYXJhbSAgICAgICB7c3RyaW5nfSAgICB0eXBlICAgICAgICAgICAgVGhlIGV4ZWN1dGVkIGFjdGlvbiB0eXBlLlxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGVGb3JtID0gZnVuY3Rpb24oJHRhcmdldCwgcmVzdWx0LCB0eXBlKSB7XG5cdFx0XHQvLyBVcGRhdGUgdGhlIHJlc3Qgb2YgdGhlIHBhZ2Vcblx0XHRcdGpzZS5saWJzLnRlbXBsYXRlLmhlbHBlcnMuZmlsbChyZXN1bHQuY29udGVudCwgJGJvZHksIG9wdGlvbnMuc2VsZWN0b3JNYXBwaW5nKTtcblx0XHRcdFxuXHRcdFx0Ly8gVG9nZ2xlIGluZm8tbWVzc2FnZXMgdmlzaWJpbGl0eS5cblx0XHRcdCQoJy5pbmZvLW1lc3NhZ2UnKS50b2dnbGVDbGFzcygnaGlkZGVuJywgJCgnLmluZm8tbWVzc2FnZScpLnRleHQoKSA9PT0gJycpO1xuXG5cdFx0XHQvLyBJbmZvcm0gb3RoZXIgd2lkZ2V0cyBhYm91dCB0aGUgdXBkYXRlXG5cdFx0XHQkdXBkYXRlVGFyZ2V0LnRyaWdnZXIoanNlLmxpYnMudGVtcGxhdGUuZXZlbnRzLkNBUlRfVVBEQVRFRCgpLCBbXSk7XG5cdFx0XHQkYm9keS50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5DQVJUX1VQREFURSgpLCAodHlwZSA9PT0gJ2FkZCcpKTtcblxuXHRcdFx0Ly8gVXBkYXRlIHRoZSBoaWRkZW4gZGF0YSBhdHRyaWJ1dGVzIG9mIHRoYXQgcm93XG5cdFx0XHRfdXBkYXRlRGF0YVZhbHVlcygkdGFyZ2V0KTtcblxuXHRcdFx0aWYgKCQuaXNFbXB0eU9iamVjdChyZXN1bHQucHJvZHVjdHMpKSB7XG5cdFx0XHRcdC8vIEhpZGUgdGhlIHRhYmxlIGlmIG5vIHByb2R1Y3RzIGFyZSBhdCB0aGUgbGlzdFxuXHRcdFx0XHQkY2FydE5vdEVtcHR5LmFkZENsYXNzKCdoaWRkZW4nKTtcblx0XHRcdFx0JGNhcnRFbXB0eS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQvLyBTaG93IHRoZSB0YWJsZSBpZiB0aGVyZSBhcmUgcHJvZHVjdHMgYXQgaXRcblx0XHRcdFx0JGNhcnRFbXB0eS5hZGRDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHRcdCRjYXJ0Tm90RW1wdHkucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0fVxuXG5cdFx0XHQvLyByZWluaXRpYWxpemUgd2lkZ2V0cyBpbiB1cGRhdGVkIERPTVxuXHRcdFx0d2luZG93LmdhbWJpby53aWRnZXRzLmluaXQoJHRoaXMpO1xuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBIZWxwZXIgZnVuY3Rpb24gdGhhdCBwcm9jZXNzZXMgdGhlIGxpc3QgdXBkYXRlcy5cblx0XHQgKiBUaGVyZWZvciBpdCBjYWxscyBBSkFYLXJlcXVlc3RzIChpbiBjYXNlIGFqYXggaXNcblx0XHQgKiBlbmFibGVkKSB0byB0aGUgc2VydmVyIHRvIGdldCB0aGUgdXBkYXRlZCBpbmZvcm1hdGlvblxuXHRcdCAqIGFib3V0IHRoZSB0YWJsZSBzdGF0ZS4gSWYgYWpheCBpc24ndCBlbmFibGVkLCBpdCBzaW1wbHlcblx0XHQgKiBzdWJtaXRzIHRoZSBmb3JtLlxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgJHRhcmdldCAgICAgVGhlIGpRdWVyeSBzZWxlY3Rpb24gb2YgdGhlIHJvdyB0aGF0IGdldHMgdXBkYXRlZFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICAgICAgZGF0YXNldCAgICAgVGhlIGRhdGEgY29sbGVjdGVkIGZyb20gdGhlIHRhcmdldCByb3cgaW4gSlNPTiBmb3JtYXRcblx0XHQgKiBAcGFyYW0gICAgICAge2FydGljbGV9ICAgICAgIGFydGljbGUgICAgIFRoZSBwcm9kdWN0cyBpZCBvZiB0aGUgYXJ0aWNsZSBpbiB0aGF0IHJvd1xuXHRcdCAqIEBwYXJhbSAgICAgICB7YXJ0aWNsZX0gICAgICAgdHlwZSAgICAgICAgVGhlIG9wZXJhdGlvbiB0eXBlIGNhbiBlaXRoZXIgYmUgXCJhZGRcIiwgXCJkZWxldGVcIiBvciBcInJlZnJlc2hcIi5cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZXhlY3V0ZUFjdGlvbiA9IGZ1bmN0aW9uKCRyb3csICR0YXJnZXQsIGRhdGFzZXQsIGFydGljbGUsIHR5cGUpIHtcblx0XHRcdGlmIChvcHRpb25zLmFqYXgpIHtcblx0XHRcdFx0Ly8gRGVsZXRlIHRoZSB0YXJnZXQgZWxlbWVudCBiZWNhdXNlIGFqYXggcmVxdWVzdHNcblx0XHRcdFx0Ly8gd2lsbCBmYWlsIHdpdGggYSBqUXVlcnkgc2VsZWN0aW9uIGluIHRoZSBkYXRhIGpzb25cblx0XHRcdFx0ZGVsZXRlIGRhdGFzZXQudGFyZ2V0O1xuXG5cdFx0XHRcdCRyb3cudHJpZ2dlcihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuVFJBTlNJVElPTigpLCB0cmFuc2l0aW9uKTtcblxuXHRcdFx0XHQvLyBQZXJmb3JtIGFuIGFqYXggaWYgdGhlIGRhdGEgaXMgdmFsaWQgYW5kIHRoZSBvcHRpb25zIGZvciBhamF4IGlzIHNldFxuXHRcdFx0XHRqc2UubGlicy54aHIuYWpheCh7dXJsOiBhY3Rpb24sIGRhdGE6IGRhdGFzZXR9LCB0cnVlKS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0XHRcdC8vIFVwZGF0ZSB0aGUgcHJvZHVjdCByb3dcblx0XHRcdFx0XHR2YXIgJG1hcmt1cCA9ICQocmVzdWx0LnByb2R1Y3RzWydwcm9kdWN0XycgKyBhcnRpY2xlXSB8fCAnJyk7XG5cdFx0XHRcdFx0JG1hcmt1cC5yZW1vdmVDbGFzcyhvcHRpb25zLmNsYXNzTG9hZGluZyk7XG5cdFx0XHRcdFx0JHRhcmdldC5yZXBsYWNlV2l0aCgkbWFya3VwKTtcblx0XHRcdFx0XHRfdXBkYXRlRm9ybSgkdGFyZ2V0LCByZXN1bHQsIHR5cGUpO1xuXHRcdFx0XHR9KS5hbHdheXMoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0X2NsZWFudXBBcnJheShhcnRpY2xlLCAkcm93KTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQvLyBDbGVhbnVwIHRoZSBhY3RpdmUgYXJyYXkgb24gZmFpbCAvIHN1Y2Nlc3Ncblx0XHRcdFx0Ly8gb2YgdGhlIGZvbGxvd2luZyBzdWJtaXQuIFRoaXMgaXMgYSBmYWxsYmFja1xuXHRcdFx0XHQvLyBpZiBhbiBvdGhlciBjb21wb25lbnQgd291bGQgcHJldmVudCB0aGUgc3VibWl0XG5cdFx0XHRcdC8vIGluIHNvbWUgY2FzZXMsIHNvIHRoYXQgdGhpcyBzY3JpcHQgY2FuIHBlcmZvcm1cblx0XHRcdFx0Ly8gYWN0aW9ucyBhZ2FpblxuXHRcdFx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cdFx0XHRcdGRlZmVycmVkLmFsd2F5cyhmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRfY2xlYW51cEFycmF5KGFydGljbGUsICRyb3cpO1xuXHRcdFx0XHR9KTtcblxuXHRcdFx0XHQvLyBTdWJtaXQgdGhlIGZvcm1cblx0XHRcdFx0JGZvcm0udHJpZ2dlcignc3VibWl0JywgZGVmZXJyZWQpO1xuXHRcdFx0fVxuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEFkZHMgYW4gY2xhc3MgdG8gdGhlIGNoYW5nZWQgaW5wdXRcblx0XHQgKiBmaWVsZCwgc28gdGhhdCBpdCdzIHN0eWxpbmcgc2hvd3Ncblx0XHQgKiB0aGF0IGl0IHdhc24ndCByZWZyZXNoZWQgdGlsbCBub3dcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hhbmdlSGFuZGxlciA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0dmFsdWUgPSAkc2VsZi52YWwoKSxcblx0XHRcdFx0b2xkVmFsdWUgPSAkc2VsZi5kYXRhKCkub2xkVmFsdWU7XG5cblx0XHRcdGlmICh2YWx1ZSAhPT0gb2xkVmFsdWUpIHtcblx0XHRcdFx0JHNlbGYuYWRkQ2xhc3Mob3B0aW9ucy5jaGFuZ2VDbGFzcyk7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHQkc2VsZi5yZW1vdmVDbGFzcyhvcHRpb25zLmNoYW5nZUNsYXNzKTtcblx0XHRcdH1cblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogSGFuZGxlciB0aGF0IGxpc3RlbnMgb24gY2xpY2sgZXZlbnRzIG9uIHRoZVxuXHRcdCAqIGJ1dHRvbnMgXCJyZWZyZXNoXCIsIFwiZGVsZXRlXCIgJiBcImFkZCB0byBjYXJ0XCIuXG5cdFx0ICogSXQgdmFsaWRhdGVzIHRoZSBmb3JtIC8gcm93IGFuZCBwYXNzZXMgdGhlXG5cdFx0ICogdGhlIGRhdGEgdG8gYW4gc3VibWl0IGV4ZWN1dGUgZnVuY2l0b24gaWYgdmFsaWRcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX2NsaWNrSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdCRyb3cgPSAkc2VsZi5jbG9zZXN0KCcuaXRlbScpLFxuXHRcdFx0XHR0eXBlID0gZS5kYXRhLnR5cGUsXG5cdFx0XHRcdHJvd2RhdGEgPSBfZ2VuZXJhdGVGb3JtZGF0YU9iamVjdCgkcm93KVswXSxcblx0XHRcdFx0YXJ0aWNsZSA9IHJvd2RhdGEucHJvZHVjdHNfaWRbMF0sXG5cdFx0XHRcdCR0YXJnZXQgPSByb3dkYXRhLnRhcmdldCxcblx0XHRcdFx0dGl0bGUgPSAkdGFyZ2V0LmZpbmQoJy5wcm9kdWN0LXRpdGxlJykudGV4dCgpO1xuXG5cdFx0XHQvLyBBZGQgbG9hZGluZyBjbGFzc1xuXHRcdFx0JHJvdy5hZGRDbGFzcygnbG9hZGluZycpO1xuXG5cdFx0XHQvLyBDaGVjayBpZiB0aGVyZSBpcyBubyBjdXJyZW50IHByb2Nlc3MgZm9yIHRoaXMgYXJ0aWNsZVxuXHRcdFx0Ly8gb3IgaW4gY2FzZSBpdCdzIG5vIGFqYXggY2FsbCB0aGVyZSBpcyBOTyBvdGhlciBwcm9jZXNzXG5cdFx0XHRpZiAoJC5pc0VtcHR5T2JqZWN0KGFjdGl2ZSkgfHwgKG9wdGlvbnMuYWpheCAmJiAhYWN0aXZlWydwcm9kdWN0XycgKyBhcnRpY2xlXSkpIHtcblx0XHRcdFx0YWN0aXZlWydwcm9kdWN0XycgKyBhcnRpY2xlXSA9IHRydWU7XG5cdFx0XHRcdF9zZXRBY3Rpb24odHlwZSk7XG5cblx0XHRcdFx0c3dpdGNoICh0eXBlKSB7XG5cdFx0XHRcdFx0Y2FzZSAnZGVsZXRlJzpcblx0XHRcdFx0XHRcdC8vIFVwZGF0ZSB0aGUgZm9ybSBhbmQgdGhlIGRhdGFzZXQgd2l0aFxuXHRcdFx0XHRcdFx0Ly8gdGhlIGFydGljbGUgaWQgdG8gZGVsZXRlXG5cdFx0XHRcdFx0XHQkZGVsZXRlRmllbGQudmFsKGFydGljbGUpO1xuXHRcdFx0XHRcdFx0cm93ZGF0YVtkZWxldGVGaWVsZE5hbWVdID0gW2FydGljbGVdO1xuXG5cdFx0XHRcdFx0XHRpZiAob3B0aW9ucy5jb25maXJtRGVsZXRlKSB7XG5cdFx0XHRcdFx0XHRcdC8vIE9wZW4gYSBtb2RhbCBsYXllciB0byBjb25maXJtIHRoZSBkZWxldGlvblxuXHRcdFx0XHRcdFx0XHR2YXIgbW9kYWxUaXRsZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdDQVJUX1dJU0hMSVNUX0RFTEVURV9USVRMRScsICdnZW5lcmFsJyksXG5cdFx0XHRcdFx0XHRcdFx0bW9kYWxNZXNzYWdlID0ganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0NBUlRfV0lTSExJU1RfREVMRVRFJywgJ2dlbmVyYWwnKTtcblxuXHRcdFx0XHRcdFx0XHRqc2UubGlicy50ZW1wbGF0ZS5tb2RhbC5jb25maXJtKHtcblx0XHRcdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIGNvbnRlbnQ6IG1vZGFsTWVzc2FnZSxcblx0XHRcdFx0XHRcdFx0XHQgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRpdGxlOiBtb2RhbFRpdGxlXG5cdFx0XHRcdFx0XHRcdCAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSkuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cblx0XHRcdFx0XHRcdFx0XHRkZWZlcnJlZC5kb25lKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdFx0X2V4ZWN1dGVBY3Rpb24oJHJvdywgJHRhcmdldCwgcm93ZGF0YSwgYXJ0aWNsZSwgdHlwZSk7XG5cdFx0XHRcdFx0XHRcdFx0fSk7XG5cblx0XHRcdFx0XHRcdFx0XHQkYm9keS50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5XSVNITElTVF9DQVJUX0RFTEVURSgpLCBbXG5cdFx0XHRcdFx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdCdkZWZlcnJlZCc6IGRlZmVycmVkLFxuXHRcdFx0XHRcdFx0XHRcdFx0XHQnZGF0YXNldCc6IHJvd2RhdGFcblx0XHRcdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFx0XHRdKTtcblx0XHRcdFx0XHRcdFx0fSkuZmFpbChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHRfY2xlYW51cEFycmF5KGFydGljbGUsICRyb3cpO1xuXHRcdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0XHRcdHZhciBkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblxuXHRcdFx0XHRcdFx0XHRkZWZlcnJlZC5kb25lKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRcdF9leGVjdXRlQWN0aW9uKCRyb3csICR0YXJnZXQsIHJvd2RhdGEsIGFydGljbGUsIHR5cGUpO1xuXHRcdFx0XHRcdFx0XHR9KTtcblxuXHRcdFx0XHRcdFx0XHQkYm9keS50cmlnZ2VyKGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5XSVNITElTVF9DQVJUX0RFTEVURSgpLCBbXG5cdFx0XHRcdFx0XHRcdFx0e1xuXHRcdFx0XHRcdFx0XHRcdFx0J2RlZmVycmVkJzogZGVmZXJyZWQsXG5cdFx0XHRcdFx0XHRcdFx0XHQnZGF0YXNldCc6IHJvd2RhdGFcblx0XHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdF0pO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0YnJlYWs7XG5cblx0XHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdFx0Ly8gSW4gYWxsIG90aGVyIGNhc2VzIGNoZWNrIGlmIHRoZSBmb3JtXG5cdFx0XHRcdFx0XHQvLyBoYXMgdmFsaWQgdmFsdWVzIGFuZCBjb250aW51ZSB3aXRoIHRoZVxuXHRcdFx0XHRcdFx0Ly8gZG9uZSBjYWxsYmFjayBpZiB2YWxpZFxuXHRcdFx0XHRcdFx0X2NoZWNrRm9ybShmYWxzZSwgZmFsc2UsIFskLmV4dGVuZCh0cnVlLCB7fSwgcm93ZGF0YSldKVxuXHRcdFx0XHRcdFx0XHQuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdFx0XHQvLyBFbXB0eSB0aGUgZGVsZXRlIGhpZGRlbiBmaWVsZCBpbiBjYXNlIGl0IHdhcyBzZXQgYmVmb3JlXG5cdFx0XHRcdFx0XHRcdFx0JGRlbGV0ZUZpZWxkLnZhbCgnJyk7XG5cblx0XHRcdFx0XHRcdFx0XHR2YXIgZXZlbnQgPSBudWxsO1xuXG5cdFx0XHRcdFx0XHRcdFx0aWYgKHR5cGUgPT09ICdhZGQnKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRldmVudCA9IGpzZS5saWJzLnRlbXBsYXRlLmV2ZW50cy5XSVNITElTVF9UT19DQVJUKCk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHRcdFx0aWYgKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHRcdFx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cblx0XHRcdFx0XHRcdFx0XHRcdGRlZmVycmVkLmRvbmUoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0XHRcdF9leGVjdXRlQWN0aW9uKCRyb3csICR0YXJnZXQsIHJvd2RhdGEsIGFydGljbGUsIHR5cGUpO1xuXHRcdFx0XHRcdFx0XHRcdFx0fSk7XG5cblx0XHRcdFx0XHRcdFx0XHRcdCRib2R5LnRyaWdnZXIoZXZlbnQsIFt7J2RlZmVycmVkJzogZGVmZXJyZWQsICdkYXRhc2V0Jzogcm93ZGF0YX1dKTtcblx0XHRcdFx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XHRcdFx0X2V4ZWN1dGVBY3Rpb24oJHJvdywgJHRhcmdldCwgcm93ZGF0YSwgYXJ0aWNsZSwgdHlwZSk7XG5cdFx0XHRcdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdF9jbGVhbnVwQXJyYXkoYXJ0aWNsZSwgJHJvdyk7XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHR9XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIFByZXZlbnQgdGhlIHN1Ym1pdCBldmVudCB0aGF0IHdhcyB0cmlnZ2VyZFxuXHRcdCAqIGJ5IHVzZXIgb3IgYnkgc2NyaXB0LiBJZiBpdCB3YXMgdHJpZ2dlcmVkXG5cdFx0ICogYnkgdGhlIHVzZXIsIGNoZWNrIGlmIGl0IHdhcyBhbiBcIkVudGVyXCIta2V5XG5cdFx0ICogc3VibWl0IGZyb20gYW4gaW5wdXQgZmllbGQuIElmIHNvLCBleGVjdXRlXG5cdFx0ICogdGhlIHJlZnJlc2ggZnVuY3Rpb25hbGl0eSBmb3IgdGhhdCByb3cuXG5cdFx0ICogSWYgdGhlIGV2ZW50IHdhcyB0cmlnZ2VyZWQgYnkgdGhlIHNjcmlwdFxuXHRcdCAqIChpZGVudGlmaWVkIGJ5IHRoZSBkYXRhIGZsYWcgXCJkXCIpIGNoZWNrIHRoZVxuXHRcdCAqIHdob2xlIGZvcm0gZm9yIGVycm9ycy4gT25seSBpbiBjYXNlIG9mIHZhbGlkXG5cdFx0ICogZGF0YSBwcm9jZWVkIHRoZSBzdWJtaXRcblx0XHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgICAgIGUgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7Ym9vbGVhbn0gICAgICAgZCAgICAgICBBIGZsYWcgdGhhdCBpZGVudGlmaWVzIHRoYXQgdGhlIHN1Ym1pdCB3YXMgdHJpZ2dlcmVkIGJ5IHRoaXMgc2NyaXB0XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX3N1Ym1pdEhhbmRsZXIgPSBmdW5jdGlvbihlLCBkKSB7XG5cblx0XHRcdC8vIFByZXZlbnQgdGhlIGRlZmF1bHQgYmVoYXZpb3VyXG5cdFx0XHQvLyBpbiBib3RoIGNhc2VzXG5cdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRlLnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHRpZiAoIWQgJiYgZS5vcmlnaW5hbEV2ZW50KSB7XG5cblx0XHRcdFx0Ly8gQ2hlY2sgaWYgYW4gaW5wdXQgZmllbGQgaGFzIHRyaWdnZXJkIHRoZSBzdWJtaXQgZXZlbnRcblx0XHRcdFx0Ly8gYW5kIGNhbGwgdGhlIHJlZnJlc2ggaGFuZGxlclxuXHRcdFx0XHR2YXIgJHNvdXJjZSA9ICQoZS5vcmlnaW5hbEV2ZW50LmV4cGxpY2l0T3JpZ2luYWxUYXJnZXQpO1xuXHRcdFx0XHRpZiAoJHNvdXJjZS5sZW5ndGggJiYgJHNvdXJjZS5pcygnaW5wdXRbdHlwZT1cInRleHRcIl0nKSkge1xuXHRcdFx0XHRcdCRzb3VyY2Vcblx0XHRcdFx0XHRcdC5jbG9zZXN0KCcuaXRlbScpXG5cdFx0XHRcdFx0XHQuZmluZCgnLmJ1dHRvbi1yZWZyZXNoJylcblx0XHRcdFx0XHRcdC5maXJzdCgpXG5cdFx0XHRcdFx0XHQudHJpZ2dlcignY2xpY2snKTtcblx0XHRcdFx0fVxuXG5cdFx0XHR9IGVsc2UgaWYgKGQpIHtcblxuXHRcdFx0XHQvLyBDaGVjayB0aGUgd2hvbGUgZm9ybSBhbmQgb25seSBzdWJtaXRcblx0XHRcdFx0Ly8gaXQgaWYgaXQncyB2YWxpZFxuXHRcdFx0XHRfY2hlY2tGb3JtKCkuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0XHQvLyBSZW1vdmUgdGhlIHN1Ym1pdCBldmVudCBoYW5kbGVyXG5cdFx0XHRcdFx0Ly8gb24gYSBzdWNjZXNzZnVsIHZhbGlkYXRpb24gYW5kXG5cdFx0XHRcdFx0Ly8gdHJpZ2dlciBhIHN1Ym1pdCBhZ2Fpbiwgc28gdGhhdCB0aGVcblx0XHRcdFx0XHQvLyBicm93c2VyIGV4ZWN1dGVzIGl0J3MgZGVmYXVsdCBiZWhhdmlvclxuXHRcdFx0XHRcdCRmb3JtXG5cdFx0XHRcdFx0XHQub2ZmKCdzdWJtaXQnKVxuXHRcdFx0XHRcdFx0LnRyaWdnZXIoJ3N1Ym1pdCcpO1xuXG5cdFx0XHRcdFx0Ly8gUmVzb2x2ZSB0aGUgZGVmZXJyZWQgaWYgZ2l2ZW5cblx0XHRcdFx0XHRpZiAodHlwZW9mIGQgPT09ICdvYmplY3QnKSB7XG5cdFx0XHRcdFx0XHRkLnJlc29sdmUoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0Ly8gUmVqZWN0IHRoZSBkZWZlcnJlZCBpZiBnaXZlblxuXHRcdFx0XHRcdGlmICh0eXBlb2YgZCA9PT0gJ29iamVjdCcpIHtcblx0XHRcdFx0XHRcdGQucmVqZWN0KCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblxuXHRcdFx0fVxuXHRcdH07XG5cblx0XHQvKipcblx0XHQgKiBFdmVudCBoYW5kbGVyIGZvciBjbGlja2luZyBvbiB0aGUgcHJvY2VlZFxuXHRcdCAqIGJ1dHRvbiB0byBnZXQgdG8gdGhlIGNoZWNrb3V0IHByb2Nlc3MuIEl0XG5cdFx0ICogY2hlY2tzIGFsbCBpdGVtcyBhZ2FpbiBpZiB0aGV5IGNvbnRhaW4gdmFsaWRcblx0XHQgKiBkYXRhLiBPbmx5IGlmIHNvLCBwcm9jZWVkXG5cdFx0ICogQHBhcmFtICAgICAgIHtvYmplY3R9ICAgICAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfc3VibWl0QnV0dG9uSGFuZGxlciA9IGZ1bmN0aW9uKGUpIHtcblx0XHRcdGUucHJldmVudERlZmF1bHQoKTtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyksXG5cdFx0XHRcdGRlc3RpbmF0aW9uID0gJHNlbGYuYXR0cignaHJlZicpO1xuXG5cdFx0XHQvLyBDaGVjayBpZiB0aGVyZSBpcyBhbnkgb3RoZXIgcHJvY2VzcyBydW5uaW5nXG5cdFx0XHRpZiAoJC5pc0VtcHR5T2JqZWN0KGFjdGl2ZSkgJiYgIWJ1c3kgJiYgIXVwZGF0ZUxpc3QpIHtcblx0XHRcdFx0YnVzeSA9IHRydWU7XG5cblx0XHRcdFx0X2NoZWNrRm9ybSh0cnVlLCB0cnVlKS5kb25lKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGxvY2F0aW9uLmhyZWYgPSBkZXN0aW5hdGlvbjtcblx0XHRcdFx0fSkuYWx3YXlzKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdGJ1c3kgPSBmYWxzZTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0fTtcblxuXHRcdC8qKlxuXHRcdCAqIEV2ZW50IGhhbmRsZXIgdGhhdCBjaGVja3MgdGhlIGZvcm0gYW5kXG5cdFx0ICogcmVzb2x2ZXMgb3IgcmVqZWN0cyB0aGUgZGVsaXZlcmVkIGRlZmVycmVkXG5cdFx0ICogKFVzZWQgZm9yIGV4dGVybmFsIHBheW1lbnQgbW9kdWxlcyB0b1xuXHRcdCAqIGNoZWNrIGlmIHRoZSBmb3JtIGlzIHZhbGlkKVxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgICAgICAgICAgalF1ZXJ5IGV2ZW50IG9iamVjdFxuXHRcdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBkICAgICAgICAgICAgICAgSlNPTiBvYmplY3Qgd2l0aCB0aGUgZXZlbnQgc2V0dGluZ3Ncblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hlY2tGb3JtSGFuZGxlciA9IGZ1bmN0aW9uKGUsIGQpIHtcblx0XHRcdGUuc3RvcFByb3BhZ2F0aW9uKCk7XG5cblx0XHRcdGQgPSBkIHx8IHt9O1xuXG5cdFx0XHRfY2hlY2tGb3JtKGQuc2hvd0NoYW5nZXMsIGQucmV2ZXJ0Q2hhbmdlcykuZG9uZShmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKGQuZGVmZXJyZWQpIHtcblx0XHRcdFx0XHRkLmRlZmVycmVkLnJlc29sdmUoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSkuZmFpbChmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKGQuZGVmZXJyZWQpIHtcblx0XHRcdFx0XHRkLmRlZmVycmVkLnJlamVjdCgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXG5cdFx0LyoqXG5cdFx0ICogRnVuY3Rpb24gdGhhdCB1cGRhdGVzIHRoZSBsaXN0IG9uIGZvY3VzIG9mXG5cdFx0ICogdGhlIHdpbmRvd1xuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF91cGRhdGVMaXN0ID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR1cGRhdGVMaXN0ID0gdHJ1ZTtcblx0XHRcdGpzZS5saWJzLnhoci5hamF4KHt1cmw6IG9wdGlvbnMudXBkYXRlVXJsfSwgdHJ1ZSkuZG9uZShmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdFx0Ly8gSW5pdCB3aXRoIGhlIGZpcnN0IGxpbmUgc2luY2UgdGhpcyBpc3QgdGhlIGhlYWRpbmdcblx0XHRcdFx0dmFyICRsYXN0U2Nhbm5lZCA9ICRmb3JtLmZpbmQoJy5vcmRlci13aXNobGlzdCAuaXRlbScpLmZpcnN0KCksXG5cdFx0XHRcdFx0JHRhcmdldCA9ICQoKTtcblxuXHRcdFx0XHQvLyBJdGVyYXRlIHRocm91Z2ggdGhlIHByb2R1Y3RzIG9iamVjdCBhbmQgc2VhcmNoIGZvciB0aGVcblx0XHRcdFx0Ly8gcHJvZHVjdHMgaW5zaWRlIHRoZSBtYXJrdXAuIElmIHRoZSBwcm9kdWN0IHdhcyBmb3VuZCxcblx0XHRcdFx0Ly8gdXBkYXRlIHRoZSB2YWx1ZXMsIGlmIG5vdCBhZGQgdGhlIHByb2R1Y3Qgcm93IGF0IHRoZVxuXHRcdFx0XHQvLyBjb3JyZWN0IHBvc2l0aW9uXG5cdFx0XHRcdCQuZWFjaChyZXN1bHQucHJvZHVjdHMsIGZ1bmN0aW9uKGtleSwgdmFsdWUpIHtcblx0XHRcdFx0XHR2YXIgYXJ0aWNsZUlkID0ga2V5LnJlcGxhY2UoJ3Byb2R1Y3RfJywgJycpLFxuXHRcdFx0XHRcdFx0JGFydGljbGUgPSAkZm9ybS5maW5kKCdpbnB1dFtuYW1lPVwicHJvZHVjdHNfaWRbXVwiXVt2YWx1ZT1cIicgKyBhcnRpY2xlSWQgKyAnXCJdJyksXG5cdFx0XHRcdFx0XHQkcm93ID0gbnVsbDtcblxuXHRcdFx0XHRcdGlmICghJGFydGljbGUubGVuZ3RoKSB7XG5cdFx0XHRcdFx0XHQvLyBUaGUgYXJ0aWNsZSB3YXNuJ3QgZm91bmQgb24gcGFnZVxuXHRcdFx0XHRcdFx0Ly8gLT4gYWRkIGl0XG5cdFx0XHRcdFx0XHQkcm93ID0gJCh2YWx1ZSk7XG5cdFx0XHRcdFx0XHQkcm93Lmluc2VydEFmdGVyKCRsYXN0U2Nhbm5lZCk7XG5cdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdC8vIFRoZSBhcnRpY2xlIHdhcyBmb3VuZCBvbiBwYWdlXG5cdFx0XHRcdFx0XHQvLyAtPiB1cGRhdGUgaXRcblx0XHRcdFx0XHRcdCRyb3cgPSAkYXJ0aWNsZS5jbG9zZXN0KCcuaXRlbScpO1xuXG5cdFx0XHRcdFx0XHR2YXIgJHF0eSA9ICRyb3cuZmluZCgnaW5wdXRbbmFtZT1cImNhcnRfcXVhbnRpdHlbXVwiXScpLFxuXHRcdFx0XHRcdFx0XHRvbGRRdHkgPSBwYXJzZUZsb2F0KCRxdHkuZGF0YSgpLm9sZFZhbHVlKSxcblx0XHRcdFx0XHRcdFx0Y3VycmVudFF0eSA9IHBhcnNlRmxvYXQoJHF0eS52YWwoKSksXG5cdFx0XHRcdFx0XHRcdG5ld1F0eSA9IHBhcnNlRmxvYXQoJCh2YWx1ZSkuZmluZCgnaW5wdXRbbmFtZT1cImNhcnRfcXVhbnRpdHlbXVwiXScpLnZhbCgpKTtcblxuXHRcdFx0XHRcdFx0JHF0eS5kYXRhKCdvbGRWYWx1ZScsIG5ld1F0eSk7XG5cblx0XHRcdFx0XHRcdC8vIEFkZCBvciByZW1vdmUgdGhlIGNoYW5nZWQgY2xhc3NlcyBkZXBlbmRpbmcgb25cblx0XHRcdFx0XHRcdC8vIHRoZSBxdWFudGl0eSBjaGFuZ2VzIGFuZCB0aGUgb24gcGFnZSBzdG9yZWQgdmFsdWVzXG5cdFx0XHRcdFx0XHRpZiAob2xkUXR5ID09PSBjdXJyZW50UXR5ICYmIGN1cnJlbnRRdHkgIT09IG5ld1F0eSkge1xuXHRcdFx0XHRcdFx0XHQkcXR5LmFkZENsYXNzKG9wdGlvbnMuY2hhbmdlQ2xhc3MpO1xuXHRcdFx0XHRcdFx0fSBlbHNlIGlmIChvbGRRdHkgIT09IGN1cnJlbnRRdHkgJiYgY3VycmVudFF0eSA9PT0gbmV3UXR5KSB7XG5cdFx0XHRcdFx0XHRcdCRxdHkucmVtb3ZlQ2xhc3Mob3B0aW9ucy5jaGFuZ2VDbGFzcyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXG5cdFx0XHRcdFx0JHRhcmdldC5hZGQoJHJvdyk7XG5cdFx0XHRcdFx0JGxhc3RTY2FubmVkID0gJHJvdztcblx0XHRcdFx0fSk7XG5cblx0XHRcdFx0Ly8gVXBkYXRlIHRoZSByZXN0IG9mIHRoZSBmb3JtXG5cdFx0XHRcdF91cGRhdGVGb3JtKCR0YXJnZXQsIHJlc3VsdCk7XG5cdFx0XHR9KS5hbHdheXMoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHVwZGF0ZUxpc3QgPSBmYWxzZTtcblx0XHRcdH0pO1xuXHRcdH07XG5cblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0XHQvKipcblx0XHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0XHQgKiBAY29uc3RydWN0b3Jcblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblxuXHRcdFx0JHVwZGF0ZVRhcmdldCA9ICQob3B0aW9ucy51cGRhdGVUYXJnZXQpO1xuXHRcdFx0JGNhcnRFbXB0eSA9ICQob3B0aW9ucy5jYXJ0RW1wdHkpO1xuXHRcdFx0JGNhcnROb3RFbXB0eSA9ICQob3B0aW9ucy5jYXJ0Tm90RW1wdHkpO1xuXHRcdFx0JGRlbGV0ZUZpZWxkID0gJChvcHRpb25zLmRlbGV0ZUlucHV0KTtcblx0XHRcdCRmb3JtID0gJHRoaXMuZmluZCgnZm9ybScpLmZpcnN0KCk7XG5cdFx0XHRkZWxldGVGaWVsZE5hbWUgPSAkZGVsZXRlRmllbGQuYXR0cignbmFtZScpO1xuXHRcdFx0YWN0aW9uID0gJGZvcm0uYXR0cignYWN0aW9uJyk7XG5cdFx0XHR0cmFuc2l0aW9uID0ge29wZW46IHRydWUsIGNsYXNzT3Blbjogb3B0aW9ucy5jbGFzc0xvYWRpbmd9O1xuXG5cdFx0XHQvLyBTZXRzIHRoZSBjdXJyZW50IHZhbHVlIG9mIHRoZSBpbnB1dFxuXHRcdFx0Ly8gdG8gYW4gaGlkZGVuIGRhdGEgYXR0cmlidXRlXG5cdFx0XHRfdXBkYXRlRGF0YVZhbHVlcygkZm9ybSk7XG5cblx0XHRcdCRmb3JtXG5cdFx0XHRcdC5vbignY2hhbmdlJywgJ2lucHV0W3R5cGU9XCJ0ZXh0XCJdJywgX2NoYW5nZUhhbmRsZXIpXG5cdFx0XHRcdC5vbignY2xpY2suZGVsZXRlJywgJy5idXR0b24tZGVsZXRlJywgeyd0eXBlJzogJ2RlbGV0ZSd9LCBfY2xpY2tIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrLnJlZnJlc2gnLCAnLmJ1dHRvbi1yZWZyZXNoJywgeyd0eXBlJzogJ3JlZnJlc2gnfSwgX2NsaWNrSGFuZGxlcilcblx0XHRcdFx0Lm9uKCdjbGljay5hZGR0b2NhcnQnLCAnLmJ1dHRvbi10by1jYXJ0Jywgeyd0eXBlJzogJ2FkZCd9LCBfY2xpY2tIYW5kbGVyKVxuXHRcdFx0XHQub24oJ2NsaWNrLnN1Ym1pdCcsICcuYnV0dG9uLXN1Ym1pdCcsIHsndHlwZSc6ICdzdWJtaXQnfSwgX3N1Ym1pdEJ1dHRvbkhhbmRsZXIpXG5cdFx0XHRcdC5vbignc3VibWl0JywgX3N1Ym1pdEhhbmRsZXIpXG5cdFx0XHRcdC5vbihqc2UubGlicy50ZW1wbGF0ZS5ldmVudHMuQ0hFQ0tfQ0FSVCgpLCBfY2hlY2tGb3JtSGFuZGxlcik7XG5cblx0XHRcdGlmIChvcHRpb25zLnVwZGF0ZVVybCkge1xuXHRcdFx0XHQkd2luZG93Lm9uKCdmb2N1cycsIF91cGRhdGVMaXN0KTtcblx0XHRcdH1cblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
