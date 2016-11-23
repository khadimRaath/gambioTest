/* --------------------------------------------------------------
 modal.js 2016-07-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.modal = jse.libs.template.modal || {};

/**
 * ## Honeygrid Modal Dialogs Library
 *
 * Library-function to open default modal layer.  This function depends on jQuery & jQuery UI.
 *
 * @module Honeygrid/Libs/modal
 * @exports jse.libs.template.modal
 * @ignore
 */
(function(exports) {
	'use strict';

	var $body = $('body'),
		tplStore = [],
		extension = null,
	// Object for default buttons
		buttons = {
			yes: {
				name: jse.core.lang.translate('yes', 'buttons'),
				type: 'success',
				class: 'btn-success'
			},
			no: {
				name: jse.core.lang.translate('no', 'buttons'),
				type: 'fail',
				class: 'btn-default'
			},
			abort: {
				name: jse.core.lang.translate('abort', 'buttons'),
				type: 'fail',
				class: 'btn-default'
			},
			ok: {
				name: jse.core.lang.translate('ok', 'buttons'),
				type: 'success',
				class: 'btn-success'
			},
			close: {
				name: jse.core.lang.translate('close', 'buttons'),
				type: 'fail',
				class: 'btn-default'
			}
		};

	/**
	 *    Function to get all form data stored inside
	 *    the layer
	 *
	 *    @param        {object}    $self        jQuery selection of the layer
	 *    @return    {json}                    Returns a JSON with all form data
	 */
	var _getFormData = function($self, checkform) {
		var $forms = $self
			.filter('form')
			.add($self.find('form')),
			formdata = {},
			valid = true,
			promises = [];

		if ($forms.length) {
			$forms.each(function() {
				var $form = $(this);

				if (checkform) {
					var localDeferred = $.Deferred();
					promises.push(localDeferred);
					$form.trigger('validator.validate', {deferred: localDeferred});
				}

				formdata[$form.attr('name') || $form.attr('id') || ('form_' + new Date().getTime() * Math.random())]
					= jse.libs.form.getData($form);
			});
		}

		return $.when
		        .apply(undefined, promises)
		        .then(function() {
			        return formdata;
		        }, function() {
			        return formdata;
		        })
		        .promise();
	};

	/**
	 *    Function to transform the custom buttons object (which is
	 *    incompatible with jQuery UI) to a jQuery UI compatible format
	 *
	 *    @param        {object}    dataset        Custom buttons object for the dialog
	 *    @param        {promise}    deferred    deferred-object to resolve / reject on close
	 *    @return    {array}                    Returns a jQuery UI dialog compatible buttons array
	 */
	var _genButtons = function(options, extensionDeferred) {

		// Check if buttons are available
		if (options.buttons) {

			var rejectHandler = extension.getRejectHandler,
				resolveHandler = extension.getResolveHandler;

			$.each(options.buttons, function(k, v) {

				// Setup click handler
				options.buttons[k].event = function() {
					var $self = $(this);

					// If a callback is given, execute it with
					// the current scope
					if (typeof v.callback === 'function') {
						if (!v.callback.apply($self, [])) {
							return false;
						}
					}

					// Add the default behaviour
					// for the close  functionality
					// On fail, reject the deferred
					// object, else resolve it
					switch (v.type) {
						case 'fail':
							rejectHandler($self, extensionDeferred, _getFormData);
							break;
						case 'success':
							resolveHandler($self, extensionDeferred, _getFormData);
							break;
						case 'link':
							location.href = v.value;
							break;
						default:
							break;
					}
				};

			});

		}

	};


	var _finalizeLayer = function($container, options) {
		// Prevent submit on enter in inner forms
		var $forms = $container.find('form');
		if ($forms.length) {
			$forms.on('submit', function(e) {
				e.preventDefault();
			});
		}

		if (window.gambio && window.gambio.widgets && window.gambio.widgets.init) {
			window.gambio.widgets.init($container);
		}
	};

	var _setLayer = function(name) {
		if (jse.libs.template.modal[name]) {
			extension = jse.libs.template.modal[name];
		} else {
			jse.core.debug.error('[MODAL] Can\'t set modal: "' + name + '". Extension doesn\'t exist');
		}
	};

	var _transferOptions = function(options) {
		var mapper = extension.getMapper(),
			result = {};

		$.each(options, function(k, v) {

			if (mapper[k] === false) {
				return true;
			} else if (mapper[k] === undefined) {
				result[k] = v;
			} else if (typeof mapper[k] === 'function') {
				var mapperResult = mapper[k](k, v);
				result[mapperResult[0]] = mapperResult[1];
			} else {
				result[mapper[k]] = v;
			}

		});

		return result;

	};

	var _getTemplate = function(options, iframe) {

		var $selection = [],
			deferred = $.Deferred();

		if (options.noTemplate) {
			deferred.resolve('');
		} else if (iframe) {
			deferred.resolve('<iframe width="100%" height="100%" frameborder="0" src="' + options.template + '" />');
		} else {
			if (options.storeTemplate && tplStore[options.template]) {
				deferred.resolve(tplStore[options.template]);
			} else {

				try {
					$selection = $(options.template);
				} catch (err) {
				}

				if ($selection.length) {
					deferred.resolve($selection.html());
				} else {
					jse.libs.xhr.ajax({url: options.template, dataType: 'html'}).done(function(result) {
						if (options.sectionSelector) {
							result = $(result).find(options.sectionSelector).html();
						}

						if (options.storeTemplate) {
							tplStore[options.template] = result;
						}
						deferred.resolve(result);
					}).fail(function() {
						deferred.reject();
					});
				}
			}
		}

		return deferred;
	};

	var _createLayer = function(options, title, className, defbuttons, template) {
		// Setup defaults & deferred objects
		var deferred = $.Deferred(),
			promise = deferred.promise(),
			iframe = (template === 'iframe'),
			defaults = {
				title: title,
				dialogClass: className,
				modal: true,
				buttons: defbuttons || [],
				closeOnEscape: true,
				template: template || null,
				storeTemplate: false,
				closeX: true,
				closeOnOuter: true
			},
			instance = null,
			$forms = null,
			extensionDeferred = $.Deferred();

		// Merge custom settings with default settings
		options = options || {};
		options = $.extend({}, defaults, options);

		var tplRequest = _getTemplate(options, iframe).done(function(result) {

			extensionDeferred.done(function(result) {
				deferred.resolve(result);
			}).fail(function(result) {
				deferred.reject(result);
			});

			// Generate template
			options.template = $(Mustache.render(result, options));
			jse.libs.template.helpers.setupWidgetAttr(options.template);
			options.template = $('<div>').append(options.template.clone()).html();

			// Generate default button object
			_genButtons(options, extensionDeferred);

			// Transfer options object to extension option object
			var originalOptions = $.extend({}, options);
			options = _transferOptions(options);

			// Call extension
			extension.openLayer(options, extensionDeferred, _getFormData, originalOptions);

			// Passthrough of the close method of the layer
			// to the layer caller
			promise.close = function(success) {
				extensionDeferred.close(success);
			};

		}).fail(function() {
			deferred.reject({error: 'Template not found'});
		});

		// Temporary close handler if the upper
		// deferred isn't finished now. It will be
		// overwritten after the layer opens
		if (!promise.close) {
			promise.close = function() {
				tplRequest.reject('Closed after opening');
			};
		}

		return promise;
	};


	/**
	 *    Shortcut function for an alert-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _alert = function(options) {
		return _createLayer(options, jse.core.lang.translate('hint', 'labels'), '', [buttons.close], '#modal_alert');
	};

	/**
	 *    Shortcut function for an confirm-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _confirm = function(options) {
		return _createLayer(options, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog', [
			buttons.yes,
			buttons.no
		], '#modal_alert');
	};

	/**
	 *    Shortcut function for a prompt-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _prompt = function(options) {
		return _createLayer(options, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog', [
			buttons.ok,
			buttons.abort
		], '#modal_prompt');
	};

	/**
	 *    Shortcut function for an success-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _success = function(options) {
		return _createLayer(options, jse.core.lang.translate('success', 'labels'), 'success_dialog', [], 
			'#modal_alert');
	};

	/**
	 *    Shortcut function for an error-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _error = function(options) {
		return _createLayer(options, jse.core.lang.translate('errors', 'labels'), 'error_dialog', [], '#modal_alert');
	};

	/**
	 *    Shortcut function for a warning-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _warn = function(options) {
		return _createLayer(options, jse.core.lang.translate('warning', 'labels'), 'warn_dialog', [], '#modal_alert');
	};

	/**
	 *    Shortcut function for an info-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _info = function(options) {
		return _createLayer(options, jse.core.lang.translate('info', 'labels'), 'info_dialog', [], '#modal_alert');
	};

	/**
	 *    Shortcut function for an iframe-layer
	 *    @param        {object}    options Options that are passed to the modal layer
	 *    @return    {promise}            Returns a promise
	 */
	var _iframe = function(options) {
		if (options.convertModal) {
			jse.libs.template.modal[options.convertModal](options, jse.core.lang.translate('info', 'labels'),
			                                              options.convertModal + '_dialog', [], '#modal_alert');
			return;
		}

		return _createLayer(options, jse.core.lang.translate('info', 'labels'), 'iframe_layer', [], 'iframe');
	};

// ########## VARIABLE EXPORT ##########

	exports.error = _error;
	exports.warn = _warn;
	exports.info = _info;
	exports.success = _success;
	exports.alert = _alert;
	exports.prompt = _prompt;
	exports.confirm = _confirm;
	exports.iframe = _iframe;
	exports.custom = _createLayer;
	exports.setLayer = _setLayer;
	exports.finalizeLayer = _finalizeLayer;

	// Set default layer.
	var currentTimestamp = Date.now,
		lifetime = 10000; // 10 sec

	extension = jse.core.registry.get('mainModalLayer');

	var intv = setInterval(function() {
		if (jse.libs.template.modal[extension] !== undefined) {
			_setLayer(extension);
			clearInterval(intv);
		}

		if (Date.now - currentTimestamp > lifetime) {
			throw new Error('Modal extension was not loaded: ' + extension);
		}
	}, 300);


}(jse.libs.template.modal));