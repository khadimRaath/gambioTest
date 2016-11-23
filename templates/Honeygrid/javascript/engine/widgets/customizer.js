/* --------------------------------------------------------------
 customizer.js 2016-07-13
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gambio.widgets.module(
	'customizer',

	[gambio.source + '/libs/events'],

	function(data) {

		'use strict';

// ########## VARIABLE INITIALIZATION ##########

		var $this = $(this),
			$body = $('body'),
			$window = $(window),
			ajax = null,
			defaults = {
				requestUrl: 'request_port.php?module=GPrint',
				uidSelector: '#gm_gprint_random',
				page: 'product'
			},
			options = $.extend(true, {}, defaults, data),
			module = {};

		/**
		 * Add customizer data to cart or wish list.
		 *
		 * @private
		 */
		var _addCustomizerData = function(e, d) {

			var formdata = jse.libs.form.getData($this, null, true),
				dataset = $.extend({'mode': 'frontend', 'action': e.data.action}, d.dataset, {}, formdata),
				promises = [],
				attributeIdsString = '';

			$('.customizer select[name^="id["], .customizer input[name^="id["]:checked').each(function() {
				var optionId = $(this).attr('name').replace(/id\[(\d+)\]/, '$1');
				attributeIdsString += '{' + optionId + '}' + $(this).val();
			});

			dataset.products_id = dataset.products_id + attributeIdsString + '{' + e.data.random.match(/\d+/) + '}0';

			$this.find('input[type="file"]').each(function() {
				if ($(this).get(0).files.length > 0) {
					var deferred = $.Deferred();
					promises.push(deferred);

					$(this).hide();
					$(this)
						.parent()
						.append('<img src="gm/images/gprint/upload.gif" width="16" height="11" '
						        + 'class="gm_gprint_loading" id="loading_' + $(this).attr('id') + '" />');

					_upload($(this), dataset, deferred);
				}
			});

			if (promises.length) {
				$.when.apply(undefined, promises).done(function() {
					_send_customizer_data(e, d, dataset);
				}).always(function() {
					var test = 1;
				});
			} else {
				_send_customizer_data(e, d, dataset);
			}
		};


		/**
		 * Upload files from customizer form.
		 *
		 * @private
		 */
		var _upload = function(uploadField, dataset, deferred) {
			var filesList = uploadField.get(0).files,
				url = options.requestUrl
					+ '&action=upload&target=' + dataset.target + '&mode=frontend&upload_field_id='
					+ uploadField.attr('id')
					+ '&products_id=' + dataset.products_id;

			$('.customizer select[name^="properties_values_ids["]').each(function() {
				url += '&properties_values_ids[]=' + $(this).val();
			});

			uploadField.fileupload({
				                       url: url,
				                       autoUpload: false,
				                       dataType: 'json'
			                       });

			uploadField.fileupload('send', {files: filesList})
			           .done(function(result) {
				           var uploadFieldName = uploadField.attr('id'),
					           filename = uploadField.val().replace(/C:\\fakepath\\/i, '');

				           dataset[uploadFieldName] = filename;
				           uploadField.parent().find('img').remove();
				           uploadField.show();

				           if (result.ERROR) {
					           alert(result.ERROR_MESSAGE);
					           deferred.reject();
				           } else {
					           deferred.resolve(result);
				           }
			           })
			           .fail(function(jqxhr, testStatus, errorThrown) {
				           uploadField.parent().find('img').remove();
				           uploadField.show();
				           deferred.reject();
			           });
		};


		/**
		 * Send customizer data beloning to a product which is going to be added to the cart.
		 *
		 * @private
		 */
		var _send_customizer_data = function(e, d, dataset) {
			ajax = (ajax) ? ajax.abort() : null;
			ajax = jse.libs.xhr.post({url: options.requestUrl, data: dataset}, true);

			ajax.done(function() {
				if (d.deferred) {
					d.deferred.resolve(e.data.random);
				}
			}).fail(function() {
				if (d.deferred) {
					d.deferred.reject();
				}
			});
		};

		/**
		 * Send customizer data beloning to a wish list product which is going to be added to the cart.
		 *
		 * @private
		 */
		var _wishlist_to_cart = function(e, d) {
			if (d.dataset.products_id[0].indexOf('}0') === -1) {
				if (d.deferred) {
					d.deferred.resolve();
				}

				return;
			}

			ajax = (ajax) ? ajax.abort() : null;

			ajax = jse.libs.xhr.post({
				                         url: options.requestUrl,
				                         data: {
					                         action: 'wishlist_to_cart',
					                         products_id: d.dataset.products_id[0],
					                         mode: 'frontend'
				                         }
			                         }, true);

			ajax
				.done(function() {
					if (d.deferred) {
						d.deferred.resolve();
					}
				})
				.fail(function() {
					if (d.deferred) {
						d.deferred.reject();
					}
				});
		};

		/**
		 * Delete customizer data belonging to a product which is going to be deleted in cart or wish list.
		 *
		 * @private
		 */
		var _delete = function(e, d) {
			if (d.dataset.products_id[0].indexOf('}0') === -1) {
				if (d.deferred) {
					d.deferred.resolve();
				}

				return;
			}

			var action = 'update_wishlist';
			if (options.page === 'cart') {
				action = 'update_cart';
			}

			ajax = (ajax) ? ajax.abort() : null;

			ajax = jse.libs.xhr.post({
				                         url: options.requestUrl,
				                         data: {
					                         action: action,
					                         products_id: d.dataset.products_id[0],
					                         mode: 'frontend'
				                         }
			                         }, true);

			ajax
				.done(function() {
					if (d.deferred) {
						d.deferred.resolve();
					}
				})
				.fail(function() {
					if (d.deferred) {
						d.deferred.reject();
					}
				});
		};


// ########## INITIALIZATION ##########

		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			if (options.page === 'product') {
				var random = $(options.uidSelector).attr('name');
				$body.on(jse.libs.template.events.ADD_CUSTOMIZER_CART(),
				         {action: 'add_cart', target: 'cart', random: random},
				         _addCustomizerData);
				$body.on(jse.libs.template.events.ADD_CUSTOMIZER_WISHLIST(),
				         {action: 'add_wishlist', target: 'wishlist', random: random},
				         _addCustomizerData);
			}

			$body.on(jse.libs.template.events.WISHLIST_TO_CART(), _wishlist_to_cart);
			$body.on(jse.libs.template.events.WISHLIST_CART_DELETE(), _delete);
			
			$('#gm_gprint_tabs li').on('click', function() {
				$window.trigger(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE());
			});
			
			$window.trigger(jse.libs.template.events.STICKYBOX_CONTENT_CHANGE());
			
			done();
		};

		// Return data to widget engine
		return module;
	});
