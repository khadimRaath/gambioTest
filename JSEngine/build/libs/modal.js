'use strict';

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
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Contains Default Modal Buttons
  *
  * @type {Object}
  */

	var buttons = {
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
	var _getFormData = function _getFormData($self, validateForm) {
		var $forms = $self.filter('form').add($self.find('form')),
		    formData = {},
		    promises = [];

		if ($forms.length) {
			$forms.each(function () {
				var $form = $(this);

				if (validateForm) {
					var localDeferred = $.Deferred();
					promises.push(localDeferred);
					$form.trigger('validator.validate', {
						'deferred': localDeferred
					});
				}

				var key = $form.attr('name') || $form.attr('id') || 'form_' + new Date().getTime() * Math.random();
				formData[key] = window.jse.lib.form.getData($form);
			});
		}

		return $.when.apply(undefined, promises).then(function () {
			return formData;
		}, function () {
			return formData;
		}).promise();
	};

	/**
  * Reject Handler
  *
  * @param {object} $element Selector element.
  * @param {object} deferred Deferred object.
  *
  * @private
  */
	var _rejectHandler = function _rejectHandler($element, deferred) {
		_getFormData($element).always(function (result) {
			deferred.reject(result);
			$element.dialog('close').remove();
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
	var _resolveHandler = function _resolveHandler($element, deferred) {
		_getFormData($element, true).done(function (result) {
			deferred.resolve(result);
			$element.dialog('close').remove();
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
	var _generateButtons = function _generateButtons(dataset, deferred) {
		var newButtons = [],
		    tmpButton = null;

		// Check if buttons are available.
		if (dataset) {
			$.each(dataset, function (k, v) {

				// Setup a new button.
				tmpButton = {};
				tmpButton.text = v.name || 'BUTTON';

				// Setup click handler.
				tmpButton.click = function () {
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
	var _getTemplate = function _getTemplate(options) {
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
			}).done(function (result) {
				if (options.storeTemplate) {
					var $append = $('<div />').attr('id', options.template).html(result);
					$('body').append($append);
				}
				deferred.resolve(result);
			}).fail(function () {
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
	var _createLayer = function _createLayer(options, title, className, defaultButtons, template) {
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

		_getTemplate(options).done(function (html) {
			// Generate template
			$template = $(Mustache.render(html, options));

			if (options.validator) {
				$template.find('form').attr('data-gx-widget', 'validator').find('input').attr({
					'data-validator-validate': options.validator.validate,
					'data-validator-regex': options.validator.regex || ''
				}).addClass('validate');
			}

			// Setup dialog
			$template.dialog(options);
			try {
				instance = $template.dialog('instance');
			} catch (exception) {
				instance = $template.data('ui-dialog');
			}

			// Add bootstrap button classes to buttonSet.
			instance.uiButtonSet.children().addClass('btn btn-default');

			// If the closeX-option is set to false, remove the button from the layout
			// else bind an event listener to reject the deferred object.
			if (options.closeX === false) {
				instance.uiDialogTitlebarClose.remove();
			} else {
				instance.uiDialogTitlebarClose.html('&times;').one('click', function () {
					_rejectHandler(instance.element, deferred);
				});
			}

			// Add an event listener to the modal overlay if the option is set.
			if (options.modalClose) {
				$('body').find('.ui-widget-overlay').last().one('click', function () {
					_rejectHandler(instance.element, deferred);
				});
			}

			// Prevent submit on enter in inner forms
			$forms = instance.element.find('form');
			if ($forms.length) {
				$forms.on('submit', function (event) {
					event.preventDefault();
				});
			}

			if (options.executeCode && typeof options.executeCode === 'function') {
				options.executeCode.call($(instance.element));
			}

			// Add a close layer method to the promise.
			promise.close = function (fail) {
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
		}).fail(function () {
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
		jse.core.debug.warn('Used deprecated modal method ' + method + ' which will be removed in JSE v1.5.');
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
	exports.alert = function (options) {
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
	exports.confirm = function (options) {
		_logDeprecatedMethod('jse.libs.modal.confirm()');

		var data = $.extend({}, {
			'draggable': true
		}, options);

		return _createLayer(data, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog', [buttons.no, buttons.yes]);
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
	exports.prompt = function (options) {
		_logDeprecatedMethod('jse.libs.modal.prompt()');

		var data = $.extend({}, {
			'draggable': true
		}, options);

		return _createLayer(data, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog', [buttons.abort, buttons.ok], '#modal_prompt');
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
	exports.success = function (options) {
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
	exports.error = function (options) {
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
	exports.warn = function (options) {
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
	exports.info = function (options) {
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
	exports.message = function (options) {
		// Create div element for modal dialog.
		$('body').append('<div class="modal-layer">' + options.content + '</div>');

		// Append options object with extra dialog options.
		options.modal = true;
		options.dialogClass = 'gx-container';

		// Set default buttons, if option wasn't provided.
		if (options.buttons === undefined) {
			options.buttons = [{
				text: buttons.close.name,
				click: function click() {
					$(this).dialog('close');
					$(this).remove();
				}
			}];
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
	exports.showMessage = function (title, content) {
		// Prepare the Bootstrap HTML markup. 
		var html = '<div class="modal fade" tabindex="-1" role="dialog">\n\t\t\t\t\t\t<div class="modal-dialog">\n\t\t\t\t\t\t\t<div class="modal-content">\n\t\t\t\t\t\t\t\t<div class="modal-header">\n\t\t\t\t\t\t\t\t\t<button type="button" class="close" data-dismiss="modal" aria-label="Close">\n\t\t\t\t\t\t\t\t\t\t<span aria-hidden="true">&times;</span>\n\t\t\t\t\t\t\t\t\t</button>\n\t\t\t\t\t\t\t\t\t<h4 class="modal-title">' + title + '</h4>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class="modal-body">\n\t\t\t\t\t                ' + content + '\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t\t<div class="modal-footer">\n\t\t\t\t\t\t\t\t\t<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>\n\t\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t\t</div>\n\t\t\t\t\t\t</div>\n\t\t\t\t\t</div>';

		// Remove the modal element when its hidden. 
		var $modal = $(html).appendTo('body');
		$modal.on('hidden.bs.modal', function () {
			return $modal.remove();
		});

		// Display the modal to the user.
		$modal.modal('show');
	};
})(jse.libs.modal);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm1vZGFsLmpzIl0sIm5hbWVzIjpbImpzZSIsImxpYnMiLCJtb2RhbCIsImV4cG9ydHMiLCJidXR0b25zIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJfZ2V0Rm9ybURhdGEiLCIkc2VsZiIsInZhbGlkYXRlRm9ybSIsIiRmb3JtcyIsImZpbHRlciIsImFkZCIsImZpbmQiLCJmb3JtRGF0YSIsInByb21pc2VzIiwibGVuZ3RoIiwiZWFjaCIsIiRmb3JtIiwiJCIsImxvY2FsRGVmZXJyZWQiLCJEZWZlcnJlZCIsInB1c2giLCJ0cmlnZ2VyIiwia2V5IiwiYXR0ciIsIkRhdGUiLCJnZXRUaW1lIiwiTWF0aCIsInJhbmRvbSIsIndpbmRvdyIsImxpYiIsImZvcm0iLCJnZXREYXRhIiwid2hlbiIsImFwcGx5IiwidW5kZWZpbmVkIiwidGhlbiIsInByb21pc2UiLCJfcmVqZWN0SGFuZGxlciIsIiRlbGVtZW50IiwiZGVmZXJyZWQiLCJhbHdheXMiLCJyZXN1bHQiLCJyZWplY3QiLCJkaWFsb2ciLCJyZW1vdmUiLCJfcmVzb2x2ZUhhbmRsZXIiLCJkb25lIiwicmVzb2x2ZSIsIl9nZW5lcmF0ZUJ1dHRvbnMiLCJkYXRhc2V0IiwibmV3QnV0dG9ucyIsInRtcEJ1dHRvbiIsImsiLCJ2IiwidGV4dCIsIm5hbWUiLCJjbGljayIsImNhbGxiYWNrIiwidHlwZSIsIl9nZXRUZW1wbGF0ZSIsIm9wdGlvbnMiLCIkc2VsZWN0aW9uIiwidGVtcGxhdGUiLCJleGNlcHRpb24iLCJkZWJ1ZyIsInRlbXBsYXRlTm90Rm91bmQiLCJodG1sIiwiYWpheCIsInN0b3JlVGVtcGxhdGUiLCIkYXBwZW5kIiwiYXBwZW5kIiwiZmFpbCIsIl9jcmVhdGVMYXllciIsInRpdGxlIiwiY2xhc3NOYW1lIiwiZGVmYXVsdEJ1dHRvbnMiLCIkdGVtcGxhdGUiLCJkZWZhdWx0cyIsImNsb3NlIiwiaW5zdGFuY2UiLCJleHRlbmQiLCJNdXN0YWNoZSIsInJlbmRlciIsInZhbGlkYXRvciIsInZhbGlkYXRlIiwicmVnZXgiLCJhZGRDbGFzcyIsImRhdGEiLCJ1aUJ1dHRvblNldCIsImNoaWxkcmVuIiwiY2xvc2VYIiwidWlEaWFsb2dUaXRsZWJhckNsb3NlIiwib25lIiwiZWxlbWVudCIsIm1vZGFsQ2xvc2UiLCJsYXN0Iiwib24iLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwiZXhlY3V0ZUNvZGUiLCJjYWxsIiwiZ3giLCJ3aWRnZXRzIiwiaW5pdCIsImNvbnRyb2xsZXJzIiwiZXh0ZW5zaW9ucyIsIl9sb2dEZXByZWNhdGVkTWV0aG9kIiwibWV0aG9kIiwid2FybiIsImFsZXJ0Iiwib2siLCJjb25maXJtIiwibm8iLCJ5ZXMiLCJwcm9tcHQiLCJhYm9ydCIsInN1Y2Nlc3MiLCJlcnJvciIsImluZm8iLCJtZXNzYWdlIiwiY29udGVudCIsImRpYWxvZ0NsYXNzIiwic2hvd01lc3NhZ2UiLCIkbW9kYWwiLCJhcHBlbmRUbyJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOztBQUVBQSxJQUFJQyxJQUFKLENBQVNDLEtBQVQsR0FBaUJGLElBQUlDLElBQUosQ0FBU0MsS0FBVCxJQUFrQixFQUFuQzs7QUFFQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFnQ0MsV0FBU0MsT0FBVCxFQUFrQjs7QUFFbEI7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxVQUFVO0FBQ2YsU0FBTztBQUNOLFdBQVFKLElBQUlLLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLEtBQXhCLEVBQStCLFNBQS9CLENBREY7QUFFTixXQUFRO0FBRkYsR0FEUTtBQUtmLFFBQU07QUFDTCxXQUFRUCxJQUFJSyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixJQUF4QixFQUE4QixTQUE5QixDQURIO0FBRUwsV0FBUTtBQUZILEdBTFM7QUFTZixXQUFTO0FBQ1IsV0FBUVAsSUFBSUssSUFBSixDQUFTQyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsT0FBeEIsRUFB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UUFBUTRELE9BQXRDLEdBQWdELFFBQWpFOztBQUVBO0FBQ0E1RCxVQUFRN0QsS0FBUixHQUFnQixJQUFoQjtBQUNBNkQsVUFBUTZELFdBQVIsR0FBc0IsY0FBdEI7O0FBRUE7QUFDQSxNQUFJN0QsUUFBUTNELE9BQVIsS0FBb0JpQyxTQUF4QixFQUFtQztBQUNsQzBCLFdBQVEzRCxPQUFSLEdBQWtCLENBQ2pCO0FBQ0NxRCxVQUFNckQsUUFBUTZFLEtBQVIsQ0FBY3ZCLElBRHJCO0FBRUNDLFdBQU8saUJBQVc7QUFDakJ2QyxPQUFFLElBQUYsRUFBUTBCLE1BQVIsQ0FBZSxPQUFmO0FBQ0ExQixPQUFFLElBQUYsRUFBUTJCLE1BQVI7QUFDQTtBQUxGLElBRGlCLENBQWxCO0FBU0E7O0FBRUQ7QUFDQTNCLElBQUUsbUJBQUYsRUFBdUIwQixNQUF2QixDQUE4QmlCLE9BQTlCO0FBQ0EsRUF2QkQ7O0FBeUJBOzs7Ozs7QUFNQTVELFNBQVEwSCxXQUFSLEdBQXNCLFVBQVNqRCxLQUFULEVBQWdCK0MsT0FBaEIsRUFBeUI7QUFDOUM7QUFDQSxNQUFNdEQscWFBTzJCTyxLQVAzQiwyR0FVZStDLE9BVmYsa1FBQU47O0FBbUJBO0FBQ0EsTUFBTUcsU0FBUzFHLEVBQUVpRCxJQUFGLEVBQVEwRCxRQUFSLENBQWlCLE1BQWpCLENBQWY7QUFDQUQsU0FBTzNCLEVBQVAsQ0FBVSxpQkFBVixFQUE2QjtBQUFBLFVBQU0yQixPQUFPL0UsTUFBUCxFQUFOO0FBQUEsR0FBN0I7O0FBRUE7QUFDQStFLFNBQU81SCxLQUFQLENBQWEsTUFBYjtBQUNBLEVBM0JEO0FBNkJBLENBL2pCQSxFQStqQkNGLElBQUlDLElBQUosQ0FBU0MsS0EvakJWLENBQUQiLCJmaWxlIjoibW9kYWwuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIG1vZGFsLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKiBnbG9iYWxzIE11c3RhY2hlICovXG5cbmpzZS5saWJzLm1vZGFsID0ganNlLmxpYnMubW9kYWwgfHwge307XG5cbi8qKlxuICogIyMgTW9kYWwgRGlhbG9ncyBMaWJyYXJ5XG4gKlxuICogVGhpcyBsaWJyYXJ5IGhhbmRsZXMgalF1ZXJ5IFVJIGFuZCBCb290c3RyYXAgbW9kYWxzIGFuZCBpdCBpcyBxdWl0ZSB1c2VmdWwgd2hlbiBpdCBjb21lcyB0byBkaXNwbGF5XG4gKiBwbGFpbiBtZXNzYWdlcy4gTWFrZSBzdXJlIHRvIHVzZSB0aGUgXCJzaG93TWVzc2FnZVwiIGZ1bmN0aW9uIG9ubHkgaW4gcGFnZXMgd2hlcmUgQm9vdHN0cmFwIGlzIGxvYWRlZC5cbiAqXG4gKiBOb3RpY2U6IFNvbWUgbGlicmFyeSBtZXRob2RzIGFyZSBkZXByZWNhdGVkIGFuZCB3aWxsIGJlIHJlbW92ZWQgd2l0aCBKU0UgdjEuNS4gXG4gKiBcbiAqICMjIyBFeGFtcGxlcyBcbiAqIFxuICogKipEaXNwbGF5IGpRdWVyeSBVSSBtZXNzYWdlLioqXG4gKiBcbiAqICBgYGBqYXZhc2NyaXB0XG4gKiBqc2UubGlicy5tb2RhbC5tZXNzYWdlKHtcbiAqICAgICAgdGl0bGU6ICdNeSBUaXRsZScsICAgICAgLy8gUmVxdWlyZWRcbiAqICAgICAgY29udGVudDogJ015IENvbnRlbnQnICAgLy8gUmVxdWlyZWRcbiAqICAgICAgYnV0dG9uczogeyAuLi4gfSAgICAgICAgLy8gT3B0aW9uYWxcbiAqICAgICAgLy8gT3RoZXIgalF1ZXJ5VUkgRGlhbG9nIFdpZGdldCBPcHRpb25zXG4gKiB9KTtcbiAqIGBgYFxuICogXG4gKiAqKkRpc3BsYXkgQm9vdHN0cmFwIG1lc3NhZ2UuKipcbiAqIGBgYGphdmFzY3JpcHRcbiAqIGpzZS5saWJzLm1vZGFsLnNob3dNZXNzYWdlKCdUaXRsZScsICdDb250ZW50Jyk7IFxuICogYGBgXG4gKiBcbiAqIEBtb2R1bGUgSlNFL0xpYnMvbW9kYWxcbiAqIEBleHBvcnRzIGpzZS5saWJzLm1vZGFsXG4gKiBcbiAqIEByZXF1aXJlcyBqUXVlcnlVSVxuICogQHJlcXVpcmVzIEJvb3RzdHJhcFxuICovXG4oZnVuY3Rpb24oZXhwb3J0cykge1xuXHRcblx0J3VzZSBzdHJpY3QnO1xuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIFZBUklBQkxFU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdC8qKlxuXHQgKiBDb250YWlucyBEZWZhdWx0IE1vZGFsIEJ1dHRvbnNcblx0ICpcblx0ICogQHR5cGUge09iamVjdH1cblx0ICovXG5cdGNvbnN0IGJ1dHRvbnMgPSB7XG5cdFx0J3llcyc6IHtcblx0XHRcdCduYW1lJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdidXR0b25zJyksXG5cdFx0XHQndHlwZSc6ICdzdWNjZXNzJ1xuXHRcdH0sXG5cdFx0J25vJzoge1xuXHRcdFx0J25hbWUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbm8nLCAnYnV0dG9ucycpLFxuXHRcdFx0J3R5cGUnOiAnZmFpbCdcblx0XHR9LFxuXHRcdCdhYm9ydCc6IHtcblx0XHRcdCduYW1lJzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Fib3J0JywgJ2J1dHRvbnMnKSxcblx0XHRcdCd0eXBlJzogJ2ZhaWwnXG5cdFx0fSxcblx0XHQnb2snOiB7XG5cdFx0XHQnbmFtZSc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdvaycsICdidXR0b25zJyksXG5cdFx0XHQndHlwZSc6ICdzdWNjZXNzJ1xuXHRcdH0sXG5cdFx0J2Nsb3NlJzoge1xuXHRcdFx0J25hbWUnOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0J3R5cGUnOiAnZmFpbCdcblx0XHR9XG5cdH07XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHQvKipcblx0ICogR2V0IEZvcm0gRGF0YVxuXHQgKlxuXHQgKiBSZXR1cm5zIGFsbCBmb3JtIGRhdGEsIHdoaWNoIGlzIHN0b3JlZCBpbnNpZGUgdGhlIGxheWVyLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJHNlbGYgalF1ZXJ5IHNlbGVjdG9yIG9mIHRoZSBsYXllci5cblx0ICogQHBhcmFtIHtib29sfSB2YWxpZGF0ZUZvcm0gRmxhZyB0aGF0IGRldGVybWluZXMgd2hldGhlciB0aGUgZm9ybSBtdXN0IGJlIHZhbGlkYXRlZFxuXHQgKiBiZWZvcmUgd2UgZ2V0IHRoZSBkYXRhLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtqc29ufSBSZXR1cm5zIGEgSlNPTiB3aXRoIGFsbCBmb3JtIGRhdGEuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX2dldEZvcm1EYXRhID0gZnVuY3Rpb24oJHNlbGYsIHZhbGlkYXRlRm9ybSkge1xuXHRcdHZhciAkZm9ybXMgPSAkc2VsZlxuXHRcdFx0XHQuZmlsdGVyKCdmb3JtJylcblx0XHRcdFx0LmFkZCgkc2VsZi5maW5kKCdmb3JtJykpLFxuXHRcdFx0Zm9ybURhdGEgPSB7fSxcblx0XHRcdHByb21pc2VzID0gW107XG5cdFx0XG5cdFx0aWYgKCRmb3Jtcy5sZW5ndGgpIHtcblx0XHRcdCRmb3Jtcy5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR2YXIgJGZvcm0gPSAkKHRoaXMpO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKHZhbGlkYXRlRm9ybSkge1xuXHRcdFx0XHRcdHZhciBsb2NhbERlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXHRcdFx0XHRcdHByb21pc2VzLnB1c2gobG9jYWxEZWZlcnJlZCk7XG5cdFx0XHRcdFx0JGZvcm0udHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJywge1xuXHRcdFx0XHRcdFx0J2RlZmVycmVkJzogbG9jYWxEZWZlcnJlZFxuXHRcdFx0XHRcdH0pO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHR2YXIga2V5ID0gJGZvcm0uYXR0cignbmFtZScpIHx8ICRmb3JtLmF0dHIoJ2lkJykgfHwgKCdmb3JtXycgKyBuZXcgRGF0ZSgpLmdldFRpbWUoKSAqIE1hdGgucmFuZG9tKCkpO1xuXHRcdFx0XHRmb3JtRGF0YVtrZXldID0gd2luZG93LmpzZS5saWIuZm9ybS5nZXREYXRhKCRmb3JtKTtcblx0XHRcdH0pO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gJC53aGVuXG5cdFx0XHQuYXBwbHkodW5kZWZpbmVkLCBwcm9taXNlcylcblx0XHRcdC50aGVuKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHJldHVybiBmb3JtRGF0YTtcblx0XHRcdFx0fSxcblx0XHRcdFx0ZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0cmV0dXJuIGZvcm1EYXRhO1xuXHRcdFx0XHR9KVxuXHRcdFx0LnByb21pc2UoKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZWplY3QgSGFuZGxlclxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gJGVsZW1lbnQgU2VsZWN0b3IgZWxlbWVudC5cblx0ICogQHBhcmFtIHtvYmplY3R9IGRlZmVycmVkIERlZmVycmVkIG9iamVjdC5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfcmVqZWN0SGFuZGxlciA9IGZ1bmN0aW9uKCRlbGVtZW50LCBkZWZlcnJlZCkge1xuXHRcdF9nZXRGb3JtRGF0YSgkZWxlbWVudCkuYWx3YXlzKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0ZGVmZXJyZWQucmVqZWN0KHJlc3VsdCk7XG5cdFx0XHQkZWxlbWVudFxuXHRcdFx0XHQuZGlhbG9nKCdjbG9zZScpXG5cdFx0XHRcdC5yZW1vdmUoKTtcblx0XHR9KTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXNvbHZlIEhhbmRsZXJcblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IFNlbGVjdG9yIGVsZW1lbnQuXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkZWZlcnJlZCBEZWZlcnJlZCBvYmplY3QuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX3Jlc29sdmVIYW5kbGVyID0gZnVuY3Rpb24oJGVsZW1lbnQsIGRlZmVycmVkKSB7XG5cdFx0X2dldEZvcm1EYXRhKCRlbGVtZW50LCB0cnVlKS5kb25lKGZ1bmN0aW9uKHJlc3VsdCkge1xuXHRcdFx0ZGVmZXJyZWQucmVzb2x2ZShyZXN1bHQpO1xuXHRcdFx0JGVsZW1lbnRcblx0XHRcdFx0LmRpYWxvZygnY2xvc2UnKVxuXHRcdFx0XHQucmVtb3ZlKCk7XG5cdFx0fSk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogR2VuZXJhdGUgQnV0dG9uc1xuXHQgKlxuXHQgKiBUcmFuc2Zvcm1zIHRoZSBjdXN0b20gYnV0dG9ucyBvYmplY3QgKHdoaWNoIGlzIGluY29tcGF0aWJsZSB3aXRoIGpRdWVyeSBVSSlcblx0ICogdG8gYSBqUXVlcnkgVUkgY29tcGF0aWJsZSBmb3JtYXQgYW5kIHJldHVybnMgaXQuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBkYXRhc2V0IEN1c3RvbSBidXR0b25zIG9iamVjdCBmb3IgdGhlIGRpYWxvZy5cblx0ICogQHBhcmFtIHtvYmplY3R9IGRlZmVycmVkIERlZmVycmVkLW9iamVjdCB0byByZXNvbHZlL3JlamVjdCBvbiBjbG9zZS5cblx0ICpcblx0ICogQHJldHVybiB7YXJyYXl9IFJldHVybnMgYSBqUXVlcnkgVUkgZGlhbG9nIGNvbXBhdGlibGUgYnV0dG9ucyBhcnJheS5cblx0ICpcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfZ2VuZXJhdGVCdXR0b25zID0gZnVuY3Rpb24oZGF0YXNldCwgZGVmZXJyZWQpIHtcblx0XHR2YXIgbmV3QnV0dG9ucyA9IFtdLFxuXHRcdFx0dG1wQnV0dG9uID0gbnVsbDtcblx0XHRcblx0XHQvLyBDaGVjayBpZiBidXR0b25zIGFyZSBhdmFpbGFibGUuXG5cdFx0aWYgKGRhdGFzZXQpIHtcblx0XHRcdCQuZWFjaChkYXRhc2V0LCBmdW5jdGlvbihrLCB2KSB7XG5cdFx0XHRcdFxuXHRcdFx0XHQvLyBTZXR1cCBhIG5ldyBidXR0b24uXG5cdFx0XHRcdHRtcEJ1dHRvbiA9IHt9O1xuXHRcdFx0XHR0bXBCdXR0b24udGV4dCA9IHYubmFtZSB8fCAnQlVUVE9OJztcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNldHVwIGNsaWNrIGhhbmRsZXIuXG5cdFx0XHRcdHRtcEJ1dHRvbi5jbGljayA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyk7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gSWYgYSBjYWxsYmFjayBpcyBnaXZlbiwgZXhlY3V0ZSBpdCB3aXRoIHRoZSBjdXJyZW50IHNjb3BlLlxuXHRcdFx0XHRcdGlmICh0eXBlb2Ygdi5jYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykge1xuXHRcdFx0XHRcdFx0di5jYWxsYmFjay5hcHBseSgkc2VsZiwgW10pO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBBZGQgdGhlIGRlZmF1bHQgYmVoYXZpb3VyIGZvciB0aGUgY2xvc2UgIGZ1bmN0aW9uYWxpdHkuIE9uIGZhaWwsXG5cdFx0XHRcdFx0Ly8gcmVqZWN0IHRoZSBkZWZlcnJlZCBvYmplY3QsIGVsc2UgcmVzb2x2ZSBpdC5cblx0XHRcdFx0XHRzd2l0Y2ggKHYudHlwZSkge1xuXHRcdFx0XHRcdFx0Y2FzZSAnZmFpbCc6XG5cdFx0XHRcdFx0XHRcdF9yZWplY3RIYW5kbGVyKCRzZWxmLCBkZWZlcnJlZCk7XG5cdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdFx0Y2FzZSAnc3VjY2Vzcyc6XG5cdFx0XHRcdFx0XHRcdF9yZXNvbHZlSGFuZGxlcigkc2VsZiwgZGVmZXJyZWQpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdGRlZmF1bHQ6XG5cdFx0XHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIEFkZCB0byB0aGUgbmV3IGJ1dHRvbnMgYXJyYXkuXG5cdFx0XHRcdG5ld0J1dHRvbnMucHVzaCh0bXBCdXR0b24pO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHR9XG5cdFx0XG5cdFx0cmV0dXJuIG5ld0J1dHRvbnM7XG5cdH07XG5cdFxuXHQvKipcblx0ICogR2V0IFRlbXBsYXRlXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgcmV0dXJuIGEgcHJvbWlzZSBvYmplY3QgdGhhdCBjYW4gYmUgdXNlZCB0byBleGVjdXRlIGNvZGUsXG5cdCAqIG9uY2UgdGhlIHRlbXBsYXRlIEhUTUwgb2YgdGhlIG1vZGFsIGlzIGZvdW5kLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyBPcHRpb25zIHRvIGJlIGFwcGxpZWQgdG8gdGhlIHRlbXBsYXRlLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtvYmplY3R9IFJldHVybnMgYSBkZWZlcnJlZCBvYmplY3QuXG5cdCAqXG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX2dldFRlbXBsYXRlID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdHZhciAkc2VsZWN0aW9uID0gW10sXG5cdFx0XHRkZWZlcnJlZCA9ICQuRGVmZXJyZWQoKTtcblx0XHRcblx0XHR0cnkge1xuXHRcdFx0JHNlbGVjdGlvbiA9ICQob3B0aW9ucy50ZW1wbGF0ZSk7XG5cdFx0fSBjYXRjaCAoZXhjZXB0aW9uKSB7XG5cdFx0XHRqc2UuY29yZS5kZWJ1Zyhqc2UuY29yZS5sYW5nLnRlbXBsYXRlTm90Rm91bmQob3B0aW9ucy50ZW1wbGF0ZSkpO1xuXHRcdH1cblx0XHRcblx0XHRpZiAoJHNlbGVjdGlvbi5sZW5ndGgpIHtcblx0XHRcdGRlZmVycmVkLnJlc29sdmUoJHNlbGVjdGlvbi5odG1sKCkpO1xuXHRcdH0gZWxzZSB7XG5cdFx0XHR3aW5kb3cuanNlLmxpYi5hamF4KHtcblx0XHRcdFx0J3VybCc6IG9wdGlvbnMudGVtcGxhdGUsXG5cdFx0XHRcdCdkYXRhVHlwZSc6ICdodG1sJ1xuXHRcdFx0fSkuZG9uZShmdW5jdGlvbihyZXN1bHQpIHtcblx0XHRcdFx0aWYgKG9wdGlvbnMuc3RvcmVUZW1wbGF0ZSkge1xuXHRcdFx0XHRcdHZhciAkYXBwZW5kID0gJCgnPGRpdiAvPicpXG5cdFx0XHRcdFx0XHQuYXR0cignaWQnLCBvcHRpb25zLnRlbXBsYXRlKVxuXHRcdFx0XHRcdFx0Lmh0bWwocmVzdWx0KTtcblx0XHRcdFx0XHQkKCdib2R5JykuYXBwZW5kKCRhcHBlbmQpO1xuXHRcdFx0XHR9XG5cdFx0XHRcdGRlZmVycmVkLnJlc29sdmUocmVzdWx0KTtcblx0XHRcdH0pLmZhaWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGRlZmVycmVkLnJlamVjdCgpO1xuXHRcdFx0fSk7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBkZWZlcnJlZDtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBDcmVhdGUgTW9kYWwgTGF5ZXJcblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgRXh0cmEgbW9kYWwgb3B0aW9ucyB0byBiZSBhcHBsaWVkIHRvIHRoZVxuXHQgKiBAcGFyYW0ge3N0cmluZ30gdGl0bGUgTW9kYWwgdGl0bGVcblx0ICogQHBhcmFtIHtzdHJpbmd9IGNsYXNzTmFtZSBDbGFzcyBuYW1lIHRvIGJlIGFkZGVkIHRvIHRoZSBtb2RhbCBlbGVtZW50LlxuXHQgKiBAcGFyYW0ge29iamVjdH0gZGVmYXVsdEJ1dHRvbnMgTW9kYWwgYnV0dG9ucyBmb3IgdGhlIGxheWVyLlxuXHQgKiBAcGFyYW0ge3N0cmluZ30gdGVtcGxhdGUgVGVtcGxhdGUgbmFtZSB0byBiZSB1c2VkIGZvciB0aGUgbW9kYWwuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIG1vZGFsIHByb21pc2Ugb2JqZWN0LlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0dmFyIF9jcmVhdGVMYXllciA9IGZ1bmN0aW9uKG9wdGlvbnMsIHRpdGxlLCBjbGFzc05hbWUsIGRlZmF1bHRCdXR0b25zLCB0ZW1wbGF0ZSkge1xuXHRcdC8vIFNldHVwIGRlZmF1bHRzICYgZGVmZXJyZWQgb2JqZWN0cy5cblx0XHR2YXIgZGVmZXJyZWQgPSAkLkRlZmVycmVkKCksXG5cdFx0XHRwcm9taXNlID0gZGVmZXJyZWQucHJvbWlzZSgpLFxuXHRcdFx0JHRlbXBsYXRlID0gJycsXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0J3RpdGxlJzogdGl0bGUgfHwgJycsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6IGNsYXNzTmFtZSB8fCAnJyxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J3Jlc2l6YWJsZSc6IGZhbHNlLFxuXHRcdFx0XHQnYnV0dG9ucyc6IGRlZmF1bHRCdXR0b25zIHx8IFtidXR0b25zLmNsb3NlXSxcblx0XHRcdFx0J2RyYWdnYWJsZSc6IGZhbHNlLFxuXHRcdFx0XHQnY2xvc2VPbkVzY2FwZSc6IGZhbHNlLFxuXHRcdFx0XHQnYXV0b09wZW4nOiBmYWxzZSxcblx0XHRcdFx0J3RlbXBsYXRlJzogdGVtcGxhdGUgfHwgJyNtb2RhbF9hbGVydCcsXG5cdFx0XHRcdCdzdG9yZVRlbXBsYXRlJzogZmFsc2UsXG5cdFx0XHRcdCdjbG9zZVgnOiB0cnVlLFxuXHRcdFx0XHQnbW9kYWxDbG9zZSc6IGZhbHNlXG5cdFx0XHR9LFxuXHRcdFx0aW5zdGFuY2UgPSBudWxsLFxuXHRcdFx0JGZvcm1zID0gbnVsbDtcblx0XHRcblx0XHQvLyBNZXJnZSBjdXN0b20gc2V0dGluZ3Mgd2l0aCBkZWZhdWx0IHNldHRpbmdzXG5cdFx0b3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0cywgb3B0aW9ucyk7XG5cdFx0b3B0aW9ucy5idXR0b25zID0gX2dlbmVyYXRlQnV0dG9ucyhvcHRpb25zLmJ1dHRvbnMsIGRlZmVycmVkKTtcblx0XHRcblx0XHRfZ2V0VGVtcGxhdGUob3B0aW9ucykuZG9uZShmdW5jdGlvbihodG1sKSB7XG5cdFx0XHQvLyBHZW5lcmF0ZSB0ZW1wbGF0ZVxuXHRcdFx0JHRlbXBsYXRlID0gJChNdXN0YWNoZS5yZW5kZXIoaHRtbCwgb3B0aW9ucykpO1xuXHRcdFx0XG5cdFx0XHRpZiAob3B0aW9ucy52YWxpZGF0b3IpIHtcblx0XHRcdFx0JHRlbXBsYXRlXG5cdFx0XHRcdFx0LmZpbmQoJ2Zvcm0nKVxuXHRcdFx0XHRcdC5hdHRyKCdkYXRhLWd4LXdpZGdldCcsICd2YWxpZGF0b3InKVxuXHRcdFx0XHRcdC5maW5kKCdpbnB1dCcpXG5cdFx0XHRcdFx0LmF0dHIoe1xuXHRcdFx0XHRcdFx0J2RhdGEtdmFsaWRhdG9yLXZhbGlkYXRlJzogb3B0aW9ucy52YWxpZGF0b3IudmFsaWRhdGUsXG5cdFx0XHRcdFx0XHQnZGF0YS12YWxpZGF0b3ItcmVnZXgnOiBvcHRpb25zLnZhbGlkYXRvci5yZWdleCB8fCAnJ1xuXHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0LmFkZENsYXNzKCd2YWxpZGF0ZScpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBTZXR1cCBkaWFsb2dcblx0XHRcdCR0ZW1wbGF0ZS5kaWFsb2cob3B0aW9ucyk7XG5cdFx0XHR0cnkge1xuXHRcdFx0XHRpbnN0YW5jZSA9ICR0ZW1wbGF0ZS5kaWFsb2coJ2luc3RhbmNlJyk7XG5cdFx0XHR9IGNhdGNoIChleGNlcHRpb24pIHtcblx0XHRcdFx0aW5zdGFuY2UgPSAkdGVtcGxhdGUuZGF0YSgndWktZGlhbG9nJyk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIEFkZCBib290c3RyYXAgYnV0dG9uIGNsYXNzZXMgdG8gYnV0dG9uU2V0LlxuXHRcdFx0aW5zdGFuY2Vcblx0XHRcdFx0LnVpQnV0dG9uU2V0XG5cdFx0XHRcdC5jaGlsZHJlbigpXG5cdFx0XHRcdC5hZGRDbGFzcygnYnRuIGJ0bi1kZWZhdWx0Jyk7XG5cdFx0XHRcblx0XHRcdC8vIElmIHRoZSBjbG9zZVgtb3B0aW9uIGlzIHNldCB0byBmYWxzZSwgcmVtb3ZlIHRoZSBidXR0b24gZnJvbSB0aGUgbGF5b3V0XG5cdFx0XHQvLyBlbHNlIGJpbmQgYW4gZXZlbnQgbGlzdGVuZXIgdG8gcmVqZWN0IHRoZSBkZWZlcnJlZCBvYmplY3QuXG5cdFx0XHRpZiAob3B0aW9ucy5jbG9zZVggPT09IGZhbHNlKSB7XG5cdFx0XHRcdGluc3RhbmNlXG5cdFx0XHRcdFx0LnVpRGlhbG9nVGl0bGViYXJDbG9zZVxuXHRcdFx0XHRcdC5yZW1vdmUoKTtcblx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdGluc3RhbmNlXG5cdFx0XHRcdFx0LnVpRGlhbG9nVGl0bGViYXJDbG9zZVxuXHRcdFx0XHRcdC5odG1sKCcmdGltZXM7Jylcblx0XHRcdFx0XHQub25lKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0X3JlamVjdEhhbmRsZXIoaW5zdGFuY2UuZWxlbWVudCwgZGVmZXJyZWQpO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBBZGQgYW4gZXZlbnQgbGlzdGVuZXIgdG8gdGhlIG1vZGFsIG92ZXJsYXkgaWYgdGhlIG9wdGlvbiBpcyBzZXQuXG5cdFx0XHRpZiAob3B0aW9ucy5tb2RhbENsb3NlKSB7XG5cdFx0XHRcdCQoJ2JvZHknKVxuXHRcdFx0XHRcdC5maW5kKCcudWktd2lkZ2V0LW92ZXJsYXknKVxuXHRcdFx0XHRcdC5sYXN0KClcblx0XHRcdFx0XHQub25lKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0X3JlamVjdEhhbmRsZXIoaW5zdGFuY2UuZWxlbWVudCwgZGVmZXJyZWQpO1xuXHRcdFx0XHRcdH0pO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBQcmV2ZW50IHN1Ym1pdCBvbiBlbnRlciBpbiBpbm5lciBmb3Jtc1xuXHRcdFx0JGZvcm1zID0gaW5zdGFuY2UuZWxlbWVudC5maW5kKCdmb3JtJyk7XG5cdFx0XHRpZiAoJGZvcm1zLmxlbmd0aCkge1xuXHRcdFx0XHQkZm9ybXMub24oJ3N1Ym1pdCcsIGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLmV4ZWN1dGVDb2RlICYmIHR5cGVvZiBvcHRpb25zLmV4ZWN1dGVDb2RlID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRcdG9wdGlvbnMuZXhlY3V0ZUNvZGUuY2FsbCgkKGluc3RhbmNlLmVsZW1lbnQpKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gQWRkIGEgY2xvc2UgbGF5ZXIgbWV0aG9kIHRvIHRoZSBwcm9taXNlLlxuXHRcdFx0cHJvbWlzZS5jbG9zZSA9IGZ1bmN0aW9uKGZhaWwpIHtcblx0XHRcdFx0aWYgKGZhaWwpIHtcblx0XHRcdFx0XHRfcmVqZWN0SGFuZGxlcihpbnN0YW5jZS5lbGVtZW50LCBkZWZlcnJlZCk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0X3Jlc29sdmVIYW5kbGVyKGluc3RhbmNlLmVsZW1lbnQsIGRlZmVycmVkKTtcblx0XHRcdFx0fVxuXHRcdFx0fTtcblx0XHRcdFxuXHRcdFx0JHRlbXBsYXRlLmRpYWxvZygnb3BlbicpO1xuXHRcdFx0aWYgKHdpbmRvdy5neCAmJiB3aW5kb3cuanNlLndpZGdldHMgJiYgd2luZG93LmpzZS53aWRnZXRzLmluaXQpIHtcblx0XHRcdFx0d2luZG93LmpzZS53aWRnZXRzLmluaXQoJHRlbXBsYXRlKTtcblx0XHRcdFx0d2luZG93LmpzZS5jb250cm9sbGVycy5pbml0KCR0ZW1wbGF0ZSk7XG5cdFx0XHRcdHdpbmRvdy5qc2UuZXh0ZW5zaW9ucy5pbml0KCR0ZW1wbGF0ZSk7XG5cdFx0XHR9XG5cdFx0fSkuZmFpbChmdW5jdGlvbigpIHtcblx0XHRcdGRlZmVycmVkLnJlamVjdCh7XG5cdFx0XHRcdCdlcnJvcic6ICdUZW1wbGF0ZSBub3QgZm91bmQnXG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHRyZXR1cm4gcHJvbWlzZTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBDcmVhdGUgYSB3YXJuaW5nIGxvZyBmb3IgdGhlIGRlcHJlY2F0ZWQgbWV0aG9kLiBcblx0ICogXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBtZXRob2QgVGhlIG1ldGhvZCBuYW1lIHRvIGJlIGluY2x1ZGVkIGluIHRoZSBsb2cuIFxuXHQgKiBcblx0ICogQHByaXZhdGVcblx0ICovXG5cdGZ1bmN0aW9uIF9sb2dEZXByZWNhdGVkTWV0aG9kKG1ldGhvZCkge1xuXHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oYFVzZWQgZGVwcmVjYXRlZCBtb2RhbCBtZXRob2QgJHttZXRob2R9IHdoaWNoIHdpbGwgYmUgcmVtb3ZlZCBpbiBKU0UgdjEuNS5gKTsgXG5cdH1cblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBQVUJMSUMgRlVOQ1RJT05TXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIEdlbmVyYXRlcyB0aGUgZGVmYXVsdCBhbGVydCBsYXllci5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgTWl4IG9mIGpRdWVyeSBVSSBkaWFsb2cgb3B0aW9ucyBhbmQgY3VzdG9tIG9wdGlvbnNcblx0ICogQHBhcmFtIHtzdHJpbmd9IHRpdGxlIERlZmF1bHQgdGl0bGUgZm9yIHRoZSB0eXBlIG9mIGFsZXJ0IGxheWVyXG5cdCAqIEBwYXJhbSB7c3RyaW5nfSBjbGFzc05hbWUgRGVmYXVsdCBjbGFzcyBmb3IgdGhlIHR5cGUgb2YgYWxlcnQgbGF5ZXJcblx0ICogQHBhcmFtIHthcnJheX0gZGVmYnV0dG9ucyBBcnJheSB3aWggdGhlIGRlZmF1bHQgYnV0dG9ucyBmb3IgdGhlIGFycmF5IHR5cGVcblx0ICogQHBhcmFtIHtzdHJpbmd9IHRlbXBsYXRlIFNlbGVjdG9yIGZvciB0aGUgalF1ZXJ5LW9iamVjdCB1c2VkIGFzIHRlbXBsYXRlXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0LlxuXHQgKiBcblx0ICogQGRlcHJlY2F0ZWQgVGhpcyBtZXRob2Qgd2lsbCBiZSByZW1vdmVkIHdpdGggSlNFIHYxLjUuXG5cdCAqL1xuXHRleHBvcnRzLmFsZXJ0ID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdF9sb2dEZXByZWNhdGVkTWV0aG9kKCdqc2UubGlicy5tb2RhbC5hbGVydCgpJyk7XG5cdFx0XG5cdFx0dmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuXHRcdFx0J2RyYWdnYWJsZSc6IHRydWVcblx0XHR9LCBvcHRpb25zKTtcblx0XHRcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdoaW50JywgJ2xhYmVscycpLCAnJywgW2J1dHRvbnMub2tdKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXR1cm5zIGEgY29uZmlybSBsYXllci5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgTWl4IG9mIGpRdWVyeSBVSSBkaWFsb2cgb3B0aW9ucyBhbmQgY3VzdG9tIG9wdGlvbnMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge3Byb21pc2V9IFJldHVybnMgYSBwcm9taXNlXG5cdCAqIFxuXHQgKiBAZGVwcmVjYXRlZCBUaGlzIG1ldGhvZCB3aWxsIGJlIHJlbW92ZWQgd2l0aCBKU0UgdjEuNS5cblx0ICovXG5cdGV4cG9ydHMuY29uZmlybSA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHRfbG9nRGVwcmVjYXRlZE1ldGhvZCgnanNlLmxpYnMubW9kYWwuY29uZmlybSgpJyk7XG5cdFx0XG5cdFx0dmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuXHRcdFx0J2RyYWdnYWJsZSc6IHRydWVcblx0XHR9LCBvcHRpb25zKTtcblx0XHRcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjb25maXJtJywgJ2xhYmVscycpLCAnY29uZmlybV9kaWFsb2cnLFxuXHRcdFx0W2J1dHRvbnMubm8sIGJ1dHRvbnMueWVzXSk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyBhIHByb21wdCBsYXllci5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgTWl4IG9mIGpRdWVyeSBVSSBkaWFsb2cgb3B0aW9ucyBhbmQgY3VzdG9tIG9wdGlvbnMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge3Byb21pc2V9IFJldHVybnMgYSBwcm9taXNlIG9iamVjdC5cblx0ICogXG5cdCAqIEBkZXByZWNhdGVkIFRoaXMgbWV0aG9kIHdpbGwgYmUgcmVtb3ZlZCB3aXRoIEpTRSB2MS41LlxuXHQgKi9cblx0ZXhwb3J0cy5wcm9tcHQgPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0X2xvZ0RlcHJlY2F0ZWRNZXRob2QoJ2pzZS5saWJzLm1vZGFsLnByb21wdCgpJyk7XG5cdFx0XG5cdFx0dmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuXHRcdFx0J2RyYWdnYWJsZSc6IHRydWVcblx0XHR9LCBvcHRpb25zKTtcblx0XHRcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdwcm9tcHQnLCAnbGFiZWxzJyksICdwcm9tcHRfZGlhbG9nJyxcblx0XHRcdFtidXR0b25zLmFib3J0LCBidXR0b25zLm9rXSwgJyNtb2RhbF9wcm9tcHQnKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXR1cm5zIGEgc3VjY2VzcyBsYXllci5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgTWl4IG9mIGpRdWVyeSBVSSBkaWFsb2cgb3B0aW9ucyBhbmQgY3VzdG9tIG9wdGlvbnMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0LlxuXHQgKiBcblx0ICogQGRlcHJlY2F0ZWQgVGhpcyBtZXRob2Qgd2lsbCBiZSByZW1vdmVkIHdpdGggSlNFIHYxLjUuXG5cdCAqL1xuXHRleHBvcnRzLnN1Y2Nlc3MgPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0X2xvZ0RlcHJlY2F0ZWRNZXRob2QoJ2pzZS5saWJzLm1vZGFsLnN1Y2Nlc3MoKScpO1xuXHRcdFxuXHRcdHZhciBkYXRhID0gJC5leHRlbmQoe30sIHtcblx0XHRcdCdkcmFnZ2FibGUnOiB0cnVlXG5cdFx0fSwgb3B0aW9ucyk7XG5cdFx0XG5cdFx0cmV0dXJuIF9jcmVhdGVMYXllcihkYXRhLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc3VjY2VzcycsICdsYWJlbHMnKSwgJ3N1Y2Nlc3NfZGlhbG9nJyk7XG5cdH07XG5cdFxuXHQvKipcblx0ICogUmV0dXJucyBhbiBlcnJvciBsYXllci5cblx0ICpcblx0ICogQHBhcmFtIHtvYmplY3R9IG9wdGlvbnMgTWl4IG9mIGpRdWVyeSBVSSBkaWFsb2cgb3B0aW9ucyBhbmQgY3VzdG9tIG9wdGlvbnMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge29iamVjdH0gUmV0dXJucyBhIHByb21pc2Ugb2JqZWN0LlxuXHQgKiBcblx0ICogQGRlcHJlY2F0ZWQgVGhpcyBtZXRob2Qgd2lsbCBiZSByZW1vdmVkIHdpdGggSlNFIHYxLjUuXG5cdCAqL1xuXHRleHBvcnRzLmVycm9yID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdF9sb2dEZXByZWNhdGVkTWV0aG9kKCdqc2UubGlicy5tb2RhbC5lcnJvcigpJyk7XG5cdFx0XG5cdFx0dmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuXHRcdFx0J2RyYWdnYWJsZSc6IHRydWVcblx0XHR9LCBvcHRpb25zKTtcblx0XHRcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdlcnJvcicsICdsYWJlbHMnKSwgJ2Vycm9yX2RpYWxvZycpO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJldHVybnMgYSB3YXJuaW5nIGxheWVyLlxuXHQgKlxuXHQgKiBAcGFyYW0ge29iamVjdH0gb3B0aW9ucyBNaXggb2YgalF1ZXJ5IFVJIGRpYWxvZyBvcHRpb25zIGFuZCBjdXN0b20gb3B0aW9ucy5cblx0ICpcblx0ICogQHJldHVybiB7b2JqZWN0fSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QuXG5cdCAqIFxuXHQgKiBAZGVwcmVjYXRlZCBUaGlzIG1ldGhvZCB3aWxsIGJlIHJlbW92ZWQgd2l0aCBKU0UgdjEuNS5cblx0ICovXG5cdGV4cG9ydHMud2FybiA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHRfbG9nRGVwcmVjYXRlZE1ldGhvZCgnanNlLmxpYnMubW9kYWwud2FybigpJyk7XG5cdFx0XG5cdFx0dmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuXHRcdFx0J2RyYWdnYWJsZSc6IHRydWVcblx0XHR9LCBvcHRpb25zKTtcblx0XHRcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCd3YXJuaW5nJywgJ2xhYmVscycpLCAnd2Fybl9kaWFsb2cnKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBSZXR1cm5zIGFuIGluZm8gbGF5ZXIuXG5cdCAqXG5cdCAqIEBwYXJhbSB7b2JqZWN0fSBvcHRpb25zIE1peCBvZiBqUXVlcnkgVUkgZGlhbG9nIG9wdGlvbnMgYW5kIGN1c3RvbSBvcHRpb25zLlxuXHQgKlxuXHQgKiBAcmV0dXJuIHtwcm9taXNlfSBSZXR1cm5zIGEgcHJvbWlzZSBvYmplY3QuXG5cdCAqIFxuXHQgKiBAZGVwcmVjYXRlZCBUaGlzIG1ldGhvZCB3aWxsIGJlIHJlbW92ZWQgd2l0aCBKU0UgdjEuNS5cblx0ICovXG5cdGV4cG9ydHMuaW5mbyA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHRfbG9nRGVwcmVjYXRlZE1ldGhvZCgnanNlLmxpYnMubW9kYWwuaW5mbygpJyk7XG5cdFx0XG5cdFx0dmFyIGRhdGEgPSAkLmV4dGVuZCh7fSwge1xuXHRcdFx0J2RyYWdnYWJsZSc6IHRydWVcblx0XHR9LCBvcHRpb25zKTtcblx0XHRcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKGRhdGEsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdpbmZvJywgJ2xhYmVscycpLCAnaW5mb19kaWFsb2cnKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBEaXNwbGF5IGpRdWVyeSBVSSBtZXNzYWdlLlxuXHQgKlxuXHQgKiBUaGlzIG1ldGhvZCBwcm92aWRlcyBhbiBlYXN5IHdheSB0byBkaXNwbGF5IGEgbWVzc2FnZSB0byB0aGUgdXNlciBieSB1c2luZyBqUXVlcnkgVUkgZGlhbG9nIHdpZGdldC5cblx0ICogXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBvcHRpb25zIE1vZGFsIG9wdGlvbnMgYXJlIHRoZSBzYW1lIGFzIHRoZSBqUXVlcnkgZGlhbG9nIHdpZGdldC5cblx0ICovXG5cdGV4cG9ydHMubWVzc2FnZSA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHQvLyBDcmVhdGUgZGl2IGVsZW1lbnQgZm9yIG1vZGFsIGRpYWxvZy5cblx0XHQkKCdib2R5JykuYXBwZW5kKCc8ZGl2IGNsYXNzPVwibW9kYWwtbGF5ZXJcIj4nICsgb3B0aW9ucy5jb250ZW50ICsgJzwvZGl2PicpO1xuXHRcdFxuXHRcdC8vIEFwcGVuZCBvcHRpb25zIG9iamVjdCB3aXRoIGV4dHJhIGRpYWxvZyBvcHRpb25zLlxuXHRcdG9wdGlvbnMubW9kYWwgPSB0cnVlO1xuXHRcdG9wdGlvbnMuZGlhbG9nQ2xhc3MgPSAnZ3gtY29udGFpbmVyJztcblx0XHRcblx0XHQvLyBTZXQgZGVmYXVsdCBidXR0b25zLCBpZiBvcHRpb24gd2Fzbid0IHByb3ZpZGVkLlxuXHRcdGlmIChvcHRpb25zLmJ1dHRvbnMgPT09IHVuZGVmaW5lZCkge1xuXHRcdFx0b3B0aW9ucy5idXR0b25zID0gW1xuXHRcdFx0XHR7XG5cdFx0XHRcdFx0dGV4dDogYnV0dG9ucy5jbG9zZS5uYW1lLFxuXHRcdFx0XHRcdGNsaWNrOiBmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5yZW1vdmUoKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdF07XG5cdFx0fVxuXHRcdFxuXHRcdC8vIERpc3BsYXkgbWVzc2FnZSB0byB0aGUgdXNlci5cblx0XHQkKCcubW9kYWwtbGF5ZXI6bGFzdCcpLmRpYWxvZyhvcHRpb25zKTtcblx0fTtcblx0XG5cdC8qKlxuXHQgKiBEaXNwbGF5IEJvb3RzdHJhcCBtb2RhbCBtZXNzYWdlLiBcblx0ICogXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSB0aXRsZSBUaGUgbWVzc2FnZSB0aXRsZS5cblx0ICogQHBhcmFtIHtTdHJpbmd9IGNvbnRlbnQgVGhlIG1lc3NhZ2UgY29udGVudC4gXG5cdCAqL1xuXHRleHBvcnRzLnNob3dNZXNzYWdlID0gZnVuY3Rpb24odGl0bGUsIGNvbnRlbnQpIHtcblx0XHQvLyBQcmVwYXJlIHRoZSBCb290c3RyYXAgSFRNTCBtYXJrdXAuIFxuXHRcdGNvbnN0IGh0bWwgPSBgPGRpdiBjbGFzcz1cIm1vZGFsIGZhZGVcIiB0YWJpbmRleD1cIi0xXCIgcm9sZT1cImRpYWxvZ1wiPlxuXHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cIm1vZGFsLWRpYWxvZ1wiPlxuXHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwibW9kYWwtY29udGVudFwiPlxuXHRcdFx0XHRcdFx0XHRcdDxkaXYgY2xhc3M9XCJtb2RhbC1oZWFkZXJcIj5cblx0XHRcdFx0XHRcdFx0XHRcdDxidXR0b24gdHlwZT1cImJ1dHRvblwiIGNsYXNzPVwiY2xvc2VcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiIGFyaWEtbGFiZWw9XCJDbG9zZVwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0XHQ8c3BhbiBhcmlhLWhpZGRlbj1cInRydWVcIj4mdGltZXM7PC9zcGFuPlxuXHRcdFx0XHRcdFx0XHRcdFx0PC9idXR0b24+XG5cdFx0XHRcdFx0XHRcdFx0XHQ8aDQgY2xhc3M9XCJtb2RhbC10aXRsZVwiPiR7dGl0bGV9PC9oND5cblx0XHRcdFx0XHRcdFx0XHQ8L2Rpdj5cblx0XHRcdFx0XHRcdFx0XHQ8ZGl2IGNsYXNzPVwibW9kYWwtYm9keVwiPlxuXHRcdFx0XHRcdCAgICAgICAgICAgICAgICAke2NvbnRlbnR9XG5cdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdFx0PGRpdiBjbGFzcz1cIm1vZGFsLWZvb3RlclwiPlxuXHRcdFx0XHRcdFx0XHRcdFx0PGJ1dHRvbiB0eXBlPVwiYnV0dG9uXCIgY2xhc3M9XCJidG4gYnRuLWRlZmF1bHRcIiBkYXRhLWRpc21pc3M9XCJtb2RhbFwiPkNsb3NlPC9idXR0b24+XG5cdFx0XHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0XHRcdDwvZGl2PlxuXHRcdFx0XHRcdFx0PC9kaXY+XG5cdFx0XHRcdFx0PC9kaXY+YDsgXG5cdFx0XG5cdFx0Ly8gUmVtb3ZlIHRoZSBtb2RhbCBlbGVtZW50IHdoZW4gaXRzIGhpZGRlbi4gXG5cdFx0Y29uc3QgJG1vZGFsID0gJChodG1sKS5hcHBlbmRUbygnYm9keScpO1xuXHRcdCRtb2RhbC5vbignaGlkZGVuLmJzLm1vZGFsJywgKCkgPT4gJG1vZGFsLnJlbW92ZSgpKTsgXG5cdFx0XG5cdFx0Ly8gRGlzcGxheSB0aGUgbW9kYWwgdG8gdGhlIHVzZXIuXG5cdFx0JG1vZGFsLm1vZGFsKCdzaG93Jyk7XG5cdH07IFxuXHRcbn0oanNlLmxpYnMubW9kYWwpKTtcbiJdfQ==
