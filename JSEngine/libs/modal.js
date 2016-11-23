/* --------------------------------------------------------------
 modal.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/* globals Mustache */

jse.libs.modal = jse.libs.modal || {};

/**
 * ## Modal Dialogs Library
 *
 * This library handles jQuery UI and Bootstrap modals and it is quite useful when it comes to display
 * plain messages. Make sure to use the "showMessage" function only in pages where Bootstrap is loaded.
 *
 * Notice: Some library methods are deprecated and will be removed with JSE v1.5. 
 * 
 * ### Examples 
 * 
 * **Display jQuery UI message.**
 * 
 *  ```javascript
 * jse.libs.modal.message({
 *      title: 'My Title',      // Required
 *      content: 'My Content'   // Required
 *      buttons: { ... }        // Optional
 *      // Other jQueryUI Dialog Widget Options
 * });
 * ```
 * 
 * **Display Bootstrap message.**
 * ```javascript
 * jse.libs.modal.showMessage('Title', 'Content'); 
 * ```
 * 
 * @module JSE/Libs/modal
 * @exports jse.libs.modal
 * 
 * @requires jQueryUI
 * @requires Bootstrap
 */
(function(exports) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	/**
	 * Contains Default Modal Buttons
	 *
	 * @type {Object}
	 */
	const buttons = {
		'yes': {
			'name': jse.core.lang.translate('yes', 'buttons'),
			'type': 'success'
		},
		'no': {
			'name': jse.core.lang.translate('no', 'buttons'),
			'type': 'fail'
		},
		'abort': {
			'name': jse.core.lang.translate('abort', 'buttons'),
			'type': 'fail'
		},
		'ok': {
			'name': jse.core.lang.translate('ok', 'buttons'),
			'type': 'success'
		},
		'close': {
			'name': jse.core.lang.translate('close', 'buttons'),
			'type': 'fail'
		}
	};
	
	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Get Form Data
	 *
	 * Returns all form data, which is stored inside the layer.
	 *
	 * @param {object} $self jQuery selector of the layer.
	 * @param {bool} validateForm Flag that determines whether the form must be validated
	 * before we get the data.
	 *
	 * @return {json} Returns a JSON with all form data.
	 *
	 * @private
	 */
	var _getFormData = function($self, validateForm) {
		var $forms = $self
				.filter('form')
				.add($self.find('form')),
			formData = {},
			promises = [];
		
		if ($forms.length) {
			$forms.each(function() {
				var $form = $(this);
				
				if (validateForm) {
					var localDeferred = $.Deferred();
					promises.push(localDeferred);
					$form.trigger('validator.validate', {
						'deferred': localDeferred
					});
				}
				
				var key = $form.attr('name') || $form.attr('id') || ('form_' + new Date().getTime() * Math.random());
				formData[key] = window.jse.lib.form.getData($form);
			});
		}
		
		return $.when
			.apply(undefined, promises)
			.then(function() {
					return formData;
				},
				function() {
					return formData;
				})
			.promise();
	};
	
	/**
	 * Reject Handler
	 *
	 * @param {object} $element Selector element.
	 * @param {object} deferred Deferred object.
	 *
	 * @private
	 */
	var _rejectHandler = function($element, deferred) {
		_getFormData($element).always(function(result) {
			deferred.reject(result);
			$element
				.dialog('close')
				.remove();
		});
	};
	
	/**
	 * Resolve Handler
	 *
	 * @param {object} $element Selector element.
	 * @param {object} deferred Deferred object.
	 *
	 * @private
	 */
	var _resolveHandler = function($element, deferred) {
		_getFormData($element, true).done(function(result) {
			deferred.resolve(result);
			$element
				.dialog('close')
				.remove();
		});
	};
	
	/**
	 * Generate Buttons
	 *
	 * Transforms the custom buttons object (which is incompatible with jQuery UI)
	 * to a jQuery UI compatible format and returns it.
	 *
	 * @param {object} dataset Custom buttons object for the dialog.
	 * @param {object} deferred Deferred-object to resolve/reject on close.
	 *
	 * @return {array} Returns a jQuery UI dialog compatible buttons array.
	 *
	 * @private
	 */
	var _generateButtons = function(dataset, deferred) {
		var newButtons = [],
			tmpButton = null;
		
		// Check if buttons are available.
		if (dataset) {
			$.each(dataset, function(k, v) {
				
				// Setup a new button.
				tmpButton = {};
				tmpButton.text = v.name || 'BUTTON';
				
				// Setup click handler.
				tmpButton.click = function() {
					var $self = $(this);
					
					// If a callback is given, execute it with the current scope.
					if (typeof v.callback === 'function') {
						v.callback.apply($self, []);
					}
					
					// Add the default behaviour for the close  functionality. On fail,
					// reject the deferred object, else resolve it.
					switch (v.type) {
						case 'fail':
							_rejectHandler($self, deferred);
							break;
						case 'success':
							_resolveHandler($self, deferred);
							break;
						default:
							break;
					}
				};
				
				// Add to the new buttons array.
				newButtons.push(tmpButton);
			});
			
		}
		
		return newButtons;
	};
	
	/**
	 * Get Template
	 *
	 * This method will return a promise object that can be used to execute code,
	 * once the template HTML of the modal is found.
	 *
	 * @param {object} options Options to be applied to the template.
	 *
	 * @return {object} Returns a deferred object.
	 *
	 * @private
	 */
	var _getTemplate = function(options) {
		var $selection = [],
			deferred = $.Deferred();
		
		try {
			$selection = $(options.template);
		} catch (exception) {
			jse.core.debug(jse.core.lang.templateNotFound(options.template));
		}
		
		if ($selection.length) {
			deferred.resolve($selection.html());
		} else {
			window.jse.lib.ajax({
				'url': options.template,
				'dataType': 'html'
			}).done(function(result) {
				if (options.storeTemplate) {
					var $append = $('<div />')
						.attr('id', options.template)
						.html(result);
					$('body').append($append);
				}
				deferred.resolve(result);
			}).fail(function() {
				deferred.reject();
			});
		}
		
		return deferred;
	};
	
	/**
	 * Create Modal Layer
	 *
	 * @param {object} options Extra modal options to be applied to the
	 * @param {string} title Modal title
	 * @param {string} className Class name to be added to the modal element.
	 * @param {object} defaultButtons Modal buttons for the layer.
	 * @param {string} template Template name to be used for the modal.
	 *
	 * @return {object} Returns a modal promise object.
	 *
	 * @private
	 */
	var _createLayer = function(options, title, className, defaultButtons, template) {
		// Setup defaults & deferred objects.
		var deferred = $.Deferred(),
			promise = deferred.promise(),
			$template = '',
			defaults = {
				'title': title || '',
				'dialogClass': className || '',
				'modal': true,
				'resizable': false,
				'buttons': defaultButtons || [buttons.close],
				'draggable': false,
				'closeOnEscape': false,
				'autoOpen': false,
				'template': template || '#modal_alert',
				'storeTemplate': false,
				'closeX': true,
				'modalClose': false
			},
			instance = null,
			$forms = null;
		
		// Merge custom settings with default settings
		options = options || {};
		options = $.extend({}, defaults, options);
		options.buttons = _generateButtons(options.buttons, deferred);
		
		_getTemplate(options).done(function(html) {
			// Generate template
			$template = $(Mustache.render(html, options));
			
			if (options.validator) {
				$template
					.find('form')
					.attr('data-gx-widget', 'validator')
					.find('input')
					.attr({
						'data-validator-validate': options.validator.validate,
						'data-validator-regex': options.validator.regex || ''
					})
					.addClass('validate');
			}
			
			// Setup dialog
			$template.dialog(options);
			try {
				instance = $template.dialog('instance');
			} catch (exception) {
				instance = $template.data('ui-dialog');
			}
			
			// Add bootstrap button classes to buttonSet.
			instance
				.uiButtonSet
				.children()
				.addClass('btn btn-default');
			
			// If the closeX-option is set to false, remove the button from the layout
			// else bind an event listener to reject the deferred object.
			if (options.closeX === false) {
				instance
					.uiDialogTitlebarClose
					.remove();
			} else {
				instance
					.uiDialogTitlebarClose
					.html('&times;')
					.one('click', function() {
						_rejectHandler(instance.element, deferred);
					});
			}
			
			// Add an event listener to the modal overlay if the option is set.
			if (options.modalClose) {
				$('body')
					.find('.ui-widget-overlay')
					.last()
					.one('click', function() {
						_rejectHandler(instance.element, deferred);
					});
			}
			
			// Prevent submit on enter in inner forms
			$forms = instance.element.find('form');
			if ($forms.length) {
				$forms.on('submit', function(event) {
					event.preventDefault();
				});
			}
			
			if (options.executeCode && typeof options.executeCode === 'function') {
				options.executeCode.call($(instance.element));
			}
			
			// Add a close layer method to the promise.
			promise.close = function(fail) {
				if (fail) {
					_rejectHandler(instance.element, deferred);
				} else {
					_resolveHandler(instance.element, deferred);
				}
			};
			
			$template.dialog('open');
			if (window.gx && window.jse.widgets && window.jse.widgets.init) {
				window.jse.widgets.init($template);
				window.jse.controllers.init($template);
				window.jse.extensions.init($template);
			}
		}).fail(function() {
			deferred.reject({
				'error': 'Template not found'
			});
		});
		
		return promise;
	};
	
	/**
	 * Create a warning log for the deprecated method. 
	 * 
	 * @param {String} method The method name to be included in the log. 
	 * 
	 * @private
	 */
	function _logDeprecatedMethod(method) {
		jse.core.debug.warn(`Used deprecated modal method ${method} which will be removed in JSE v1.5.`); 
	}
	
	// ------------------------------------------------------------------------
	// PUBLIC FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Generates the default alert layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options
	 * @param {string} title Default title for the type of alert layer
	 * @param {string} className Default class for the type of alert layer
	 * @param {array} defbuttons Array wih the default buttons for the array type
	 * @param {string} template Selector for the jQuery-object used as template
	 *
	 * @return {object} Returns a promise object.
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.alert = function(options) {
		_logDeprecatedMethod('jse.libs.modal.alert()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('hint', 'labels'), '', [buttons.ok]);
	};
	
	/**
	 * Returns a confirm layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {promise} Returns a promise
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.confirm = function(options) {
		_logDeprecatedMethod('jse.libs.modal.confirm()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog',
			[buttons.no, buttons.yes]);
	};
	
	/**
	 * Returns a prompt layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {promise} Returns a promise object.
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.prompt = function(options) {
		_logDeprecatedMethod('jse.libs.modal.prompt()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog',
			[buttons.abort, buttons.ok], '#modal_prompt');
	};
	
	/**
	 * Returns a success layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {object} Returns a promise object.
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.success = function(options) {
		_logDeprecatedMethod('jse.libs.modal.success()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('success', 'labels'), 'success_dialog');
	};
	
	/**
	 * Returns an error layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {object} Returns a promise object.
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.error = function(options) {
		_logDeprecatedMethod('jse.libs.modal.error()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('error', 'labels'), 'error_dialog');
	};
	
	/**
	 * Returns a warning layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {object} Returns a promise object.
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.warn = function(options) {
		_logDeprecatedMethod('jse.libs.modal.warn()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('warning', 'labels'), 'warn_dialog');
	};
	
	/**
	 * Returns an info layer.
	 *
	 * @param {object} options Mix of jQuery UI dialog options and custom options.
	 *
	 * @return {promise} Returns a promise object.
	 * 
	 * @deprecated This method will be removed with JSE v1.5.
	 */
	exports.info = function(options) {
		_logDeprecatedMethod('jse.libs.modal.info()');
		
		var data = $.extend({}, {
			'draggable': true
		}, options);
		
		return _createLayer(data, jse.core.lang.translate('info', 'labels'), 'info_dialog');
	};
	
	/**
	 * Display jQuery UI message.
	 *
	 * This method provides an easy way to display a message to the user by using jQuery UI dialog widget.
	 * 
	 * @param {Object} options Modal options are the same as the jQuery dialog widget.
	 */
	exports.message = function(options) {
		// Create div element for modal dialog.
		$('body').append('<div class="modal-layer">' + options.content + '</div>');
		
		// Append options object with extra dialog options.
		options.modal = true;
		options.dialogClass = 'gx-container';
		
		// Set default buttons, if option wasn't provided.
		if (options.buttons === undefined) {
			options.buttons = [
				{
					text: buttons.close.name,
					click: function() {
						$(this).dialog('close');
						$(this).remove();
					}
				}
			];
		}
		
		// Display message to the user.
		$('.modal-layer:last').dialog(options);
	};
	
	/**
	 * Display Bootstrap modal message. 
	 * 
	 * @param {String} title The message title.
	 * @param {String} content The message content. 
	 */
	exports.showMessage = function(title, content) {
		// Prepare the Bootstrap HTML markup. 
		const html = `<div class="modal fade" tabindex="-1" role="dialog">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
										<span aria-hidden="true">&times;</span>
									</button>
									<h4 class="modal-title">${title}</h4>
								</div>
								<div class="modal-body">
					                ${content}
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
							</div>
						</div>
					</div>`; 
		
		// Remove the modal element when its hidden. 
		const $modal = $(html).appendTo('body');
		$modal.on('hidden.bs.modal', () => $modal.remove()); 
		
		// Display the modal to the user.
		$modal.modal('show');
	}; 
	
}(jse.libs.modal));
