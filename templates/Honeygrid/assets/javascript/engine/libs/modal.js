'use strict';

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
(function (exports) {
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
	var _getFormData = function _getFormData($self, checkform) {
		var $forms = $self.filter('form').add($self.find('form')),
		    formdata = {},
		    valid = true,
		    promises = [];

		if ($forms.length) {
			$forms.each(function () {
				var $form = $(this);

				if (checkform) {
					var localDeferred = $.Deferred();
					promises.push(localDeferred);
					$form.trigger('validator.validate', { deferred: localDeferred });
				}

				formdata[$form.attr('name') || $form.attr('id') || 'form_' + new Date().getTime() * Math.random()] = jse.libs.form.getData($form);
			});
		}

		return $.when.apply(undefined, promises).then(function () {
			return formdata;
		}, function () {
			return formdata;
		}).promise();
	};

	/**
  *    Function to transform the custom buttons object (which is
  *    incompatible with jQuery UI) to a jQuery UI compatible format
  *
  *    @param        {object}    dataset        Custom buttons object for the dialog
  *    @param        {promise}    deferred    deferred-object to resolve / reject on close
  *    @return    {array}                    Returns a jQuery UI dialog compatible buttons array
  */
	var _genButtons = function _genButtons(options, extensionDeferred) {

		// Check if buttons are available
		if (options.buttons) {

			var rejectHandler = extension.getRejectHandler,
			    resolveHandler = extension.getResolveHandler;

			$.each(options.buttons, function (k, v) {

				// Setup click handler
				options.buttons[k].event = function () {
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

	var _finalizeLayer = function _finalizeLayer($container, options) {
		// Prevent submit on enter in inner forms
		var $forms = $container.find('form');
		if ($forms.length) {
			$forms.on('submit', function (e) {
				e.preventDefault();
			});
		}

		if (window.gambio && window.gambio.widgets && window.gambio.widgets.init) {
			window.gambio.widgets.init($container);
		}
	};

	var _setLayer = function _setLayer(name) {
		if (jse.libs.template.modal[name]) {
			extension = jse.libs.template.modal[name];
		} else {
			jse.core.debug.error('[MODAL] Can\'t set modal: "' + name + '". Extension doesn\'t exist');
		}
	};

	var _transferOptions = function _transferOptions(options) {
		var mapper = extension.getMapper(),
		    result = {};

		$.each(options, function (k, v) {

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

	var _getTemplate = function _getTemplate(options, iframe) {

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
				} catch (err) {}

				if ($selection.length) {
					deferred.resolve($selection.html());
				} else {
					jse.libs.xhr.ajax({ url: options.template, dataType: 'html' }).done(function (result) {
						if (options.sectionSelector) {
							result = $(result).find(options.sectionSelector).html();
						}

						if (options.storeTemplate) {
							tplStore[options.template] = result;
						}
						deferred.resolve(result);
					}).fail(function () {
						deferred.reject();
					});
				}
			}
		}

		return deferred;
	};

	var _createLayer = function _createLayer(options, title, className, defbuttons, template) {
		// Setup defaults & deferred objects
		var deferred = $.Deferred(),
		    promise = deferred.promise(),
		    iframe = template === 'iframe',
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

		var tplRequest = _getTemplate(options, iframe).done(function (result) {

			extensionDeferred.done(function (result) {
				deferred.resolve(result);
			}).fail(function (result) {
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
			promise.close = function (success) {
				extensionDeferred.close(success);
			};
		}).fail(function () {
			deferred.reject({ error: 'Template not found' });
		});

		// Temporary close handler if the upper
		// deferred isn't finished now. It will be
		// overwritten after the layer opens
		if (!promise.close) {
			promise.close = function () {
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
	var _alert = function _alert(options) {
		return _createLayer(options, jse.core.lang.translate('hint', 'labels'), '', [buttons.close], '#modal_alert');
	};

	/**
  *    Shortcut function for an confirm-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _confirm = function _confirm(options) {
		return _createLayer(options, jse.core.lang.translate('confirm', 'labels'), 'confirm_dialog', [buttons.yes, buttons.no], '#modal_alert');
	};

	/**
  *    Shortcut function for a prompt-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _prompt = function _prompt(options) {
		return _createLayer(options, jse.core.lang.translate('prompt', 'labels'), 'prompt_dialog', [buttons.ok, buttons.abort], '#modal_prompt');
	};

	/**
  *    Shortcut function for an success-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _success = function _success(options) {
		return _createLayer(options, jse.core.lang.translate('success', 'labels'), 'success_dialog', [], '#modal_alert');
	};

	/**
  *    Shortcut function for an error-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _error = function _error(options) {
		return _createLayer(options, jse.core.lang.translate('errors', 'labels'), 'error_dialog', [], '#modal_alert');
	};

	/**
  *    Shortcut function for a warning-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _warn = function _warn(options) {
		return _createLayer(options, jse.core.lang.translate('warning', 'labels'), 'warn_dialog', [], '#modal_alert');
	};

	/**
  *    Shortcut function for an info-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _info = function _info(options) {
		return _createLayer(options, jse.core.lang.translate('info', 'labels'), 'info_dialog', [], '#modal_alert');
	};

	/**
  *    Shortcut function for an iframe-layer
  *    @param        {object}    options Options that are passed to the modal layer
  *    @return    {promise}            Returns a promise
  */
	var _iframe = function _iframe(options) {
		if (options.convertModal) {
			jse.libs.template.modal[options.convertModal](options, jse.core.lang.translate('info', 'labels'), options.convertModal + '_dialog', [], '#modal_alert');
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

	var intv = setInterval(function () {
		if (jse.libs.template.modal[extension] !== undefined) {
			_setLayer(extension);
			clearInterval(intv);
		}

		if (Date.now - currentTimestamp > lifetime) {
			throw new Error('Modal extension was not loaded: ' + extension);
		}
	}, 300);
})(jse.libs.template.modal);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxpYnMvbW9kYWwuanMiXSwibmFtZXMiOlsianNlIiwibGlicyIsInRlbXBsYXRlIiwibW9kYWwiLCJleHBvcnRzIiwiJGJvZHkiLCIkIiwidHBsU3RvcmUiLCJleHRlbnNpb24iLCJidXR0b25zIiwieWVzIiwibmFtZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwidHlwZSIsImNsYXNzIiwibm8iLCJhYm9ydCIsIm9rIiwiY2xvc2UiLCJfZ2V0Rm9ybURhdGEiLCIkc2VsZiIsImNoZWNrZm9ybSIsIiRmb3JtcyIsImZpbHRlciIsImFkZCIsImZpbmQiLCJmb3JtZGF0YSIsInZhbGlkIiwicHJvbWlzZXMiLCJsZW5ndGgiLCJlYWNoIiwiJGZvcm0iLCJsb2NhbERlZmVycmVkIiwiRGVmZXJyZWQiLCJwdXNoIiwidHJpZ2dlciIsImRlZmVycmVkIiwiYXR0ciIsIkRhdGUiLCJnZXRUaW1lIiwiTWF0aCIsInJhbmRvbSIsImZvcm0iLCJnZXREYXRhIiwid2hlbiIsImFwcGx5IiwidW5kZWZpbmVkIiwidGhlbiIsInByb21pc2UiLCJfZ2VuQnV0dG9ucyIsIm9wdGlvbnMiLCJleHRlbnNpb25EZWZlcnJlZCIsInJlamVjdEhhbmRsZXIiLCJnZXRSZWplY3RIYW5kbGVyIiwicmVzb2x2ZUhhbmRsZXIiLCJnZXRSZXNvbHZlSGFuZGxlciIsImsiLCJ2IiwiZXZlbnQiLCJjYWxsYmFjayIsImxvY2F0aW9uIiwiaHJlZiIsInZhbHVlIiwiX2ZpbmFsaXplTGF5ZXIiLCIkY29udGFpbmVyIiwib24iLCJlIiwicHJldmVudERlZmF1bHQiLCJ3aW5kb3ciLCJnYW1iaW8iLCJ3aWRnZXRzIiwiaW5pdCIsIl9zZXRMYXllciIsImRlYnVnIiwiZXJyb3IiLCJfdHJhbnNmZXJPcHRpb25zIiwibWFwcGVyIiwiZ2V0TWFwcGVyIiwicmVzdWx0IiwibWFwcGVyUmVzdWx0IiwiX2dldFRlbXBsYXRlIiwiaWZyYW1lIiwiJHNlbGVjdGlvbiIsIm5vVGVtcGxhdGUiLCJyZXNvbHZlIiwic3RvcmVUZW1wbGF0ZSIsImVyciIsImh0bWwiLCJ4aHIiLCJhamF4IiwidXJsIiwiZGF0YVR5cGUiLCJkb25lIiwic2VjdGlvblNlbGVjdG9yIiwiZmFpbCIsInJlamVjdCIsIl9jcmVhdGVMYXllciIsInRpdGxlIiwiY2xhc3NOYW1lIiwiZGVmYnV0dG9ucyIsImRlZmF1bHRzIiwiZGlhbG9nQ2xhc3MiLCJjbG9zZU9uRXNjYXBlIiwiY2xvc2VYIiwiY2xvc2VPbk91dGVyIiwiaW5zdGFuY2UiLCJleHRlbmQiLCJ0cGxSZXF1ZXN0IiwiTXVzdGFjaGUiLCJyZW5kZXIiLCJoZWxwZXJzIiwic2V0dXBXaWRnZXRBdHRyIiwiYXBwZW5kIiwiY2xvbmUiLCJvcmlnaW5hbE9wdGlvbnMiLCJvcGVuTGF5ZXIiLCJzdWNjZXNzIiwiX2FsZXJ0IiwiX2NvbmZpcm0iLCJfcHJvbXB0IiwiX3N1Y2Nlc3MiLCJfZXJyb3IiLCJfd2FybiIsIl9pbmZvIiwiX2lmcmFtZSIsImNvbnZlcnRNb2RhbCIsIndhcm4iLCJpbmZvIiwiYWxlcnQiLCJwcm9tcHQiLCJjb25maXJtIiwiY3VzdG9tIiwic2V0TGF5ZXIiLCJmaW5hbGl6ZUxheWVyIiwiY3VycmVudFRpbWVzdGFtcCIsIm5vdyIsImxpZmV0aW1lIiwicmVnaXN0cnkiLCJnZXQiLCJpbnR2Iiwic2V0SW50ZXJ2YWwiLCJjbGVhckludGVydmFsIiwiRXJyb3IiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxLQUFsQixHQUEwQkgsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxLQUFsQixJQUEyQixFQUFyRDs7QUFFQTs7Ozs7Ozs7O0FBU0MsV0FBU0MsT0FBVCxFQUFrQjtBQUNsQjs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLE1BQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVcsRUFEWjtBQUFBLEtBRUNDLFlBQVksSUFGYjs7QUFHQTtBQUNDQyxXQUFVO0FBQ1RDLE9BQUs7QUFDSkMsU0FB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QVRRLENBQVg7QUFZQSxDQXBaQSxFQW9aQ1IsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxLQXBabkIsQ0FBRCIsImZpbGUiOiJsaWJzL21vZGFsLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBtb2RhbC5qcyAyMDE2LTA3LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuanNlLmxpYnMudGVtcGxhdGUubW9kYWwgPSBqc2UubGlicy50ZW1wbGF0ZS5tb2RhbCB8fCB7fTtcblxuLyoqXG4gKiAjIyBIb25leWdyaWQgTW9kYWwgRGlhbG9ncyBMaWJyYXJ5XG4gKlxuICogTGlicmFyeS1mdW5jdGlvbiB0byBvcGVuIGRlZmF1bHQgbW9kYWwgbGF5ZXIuICBUaGlzIGZ1bmN0aW9uIGRlcGVuZHMgb24galF1ZXJ5ICYgalF1ZXJ5IFVJLlxuICpcbiAqIEBtb2R1bGUgSG9uZXlncmlkL0xpYnMvbW9kYWxcbiAqIEBleHBvcnRzIGpzZS5saWJzLnRlbXBsYXRlLm1vZGFsXG4gKiBAaWdub3JlXG4gKi9cbihmdW5jdGlvbihleHBvcnRzKSB7XG5cdCd1c2Ugc3RyaWN0JztcblxuXHR2YXIgJGJvZHkgPSAkKCdib2R5JyksXG5cdFx0dHBsU3RvcmUgPSBbXSxcblx0XHRleHRlbnNpb24gPSBudWxsLFxuXHQvLyBPYmplY3QgZm9yIGRlZmF1bHQgYnV0dG9uc1xuXHRcdGJ1dHRvbnMgPSB7XG5cdFx0XHR5ZXM6IHtcblx0XHRcdFx0bmFtZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ3llcycsICdidXR0b25zJyksXG5cdFx0XHRcdHR5cGU6ICdzdWNjZXNzJyxcblx0XHRcdFx0Y2xhc3M6ICdidG4tc3VjY2Vzcydcblx0XHRcdH0sXG5cdFx0XHRubzoge1xuXHRcdFx0XHRuYW1lOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnbm8nLCAnYnV0dG9ucycpLFxuXHRcdFx0XHR0eXBlOiAnZmFpbCcsXG5cdFx0XHRcdGNsYXNzOiAnYnRuLWRlZmF1bHQnXG5cdFx0XHR9LFxuXHRcdFx0YWJvcnQ6IHtcblx0XHRcdFx0bmFtZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Fib3J0JywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0dHlwZTogJ2ZhaWwnLFxuXHRcdFx0XHRjbGFzczogJ2J0bi1kZWZhdWx0J1xuXHRcdFx0fSxcblx0XHRcdG9rOiB7XG5cdFx0XHRcdG5hbWU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdvaycsICdidXR0b25zJyksXG5cdFx0XHRcdHR5cGU6ICdzdWNjZXNzJyxcblx0XHRcdFx0Y2xhc3M6ICdidG4tc3VjY2Vzcydcblx0XHRcdH0sXG5cdFx0XHRjbG9zZToge1xuXHRcdFx0XHRuYW1lOiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnY2xvc2UnLCAnYnV0dG9ucycpLFxuXHRcdFx0XHR0eXBlOiAnZmFpbCcsXG5cdFx0XHRcdGNsYXNzOiAnYnRuLWRlZmF1bHQnXG5cdFx0XHR9XG5cdFx0fTtcblxuXHQvKipcblx0ICogICAgRnVuY3Rpb24gdG8gZ2V0IGFsbCBmb3JtIGRhdGEgc3RvcmVkIGluc2lkZVxuXHQgKiAgICB0aGUgbGF5ZXJcblx0ICpcblx0ICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICAkc2VsZiAgICAgICAgalF1ZXJ5IHNlbGVjdGlvbiBvZiB0aGUgbGF5ZXJcblx0ICogICAgQHJldHVybiAgICB7anNvbn0gICAgICAgICAgICAgICAgICAgIFJldHVybnMgYSBKU09OIHdpdGggYWxsIGZvcm0gZGF0YVxuXHQgKi9cblx0dmFyIF9nZXRGb3JtRGF0YSA9IGZ1bmN0aW9uKCRzZWxmLCBjaGVja2Zvcm0pIHtcblx0XHR2YXIgJGZvcm1zID0gJHNlbGZcblx0XHRcdC5maWx0ZXIoJ2Zvcm0nKVxuXHRcdFx0LmFkZCgkc2VsZi5maW5kKCdmb3JtJykpLFxuXHRcdFx0Zm9ybWRhdGEgPSB7fSxcblx0XHRcdHZhbGlkID0gdHJ1ZSxcblx0XHRcdHByb21pc2VzID0gW107XG5cblx0XHRpZiAoJGZvcm1zLmxlbmd0aCkge1xuXHRcdFx0JGZvcm1zLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdHZhciAkZm9ybSA9ICQodGhpcyk7XG5cblx0XHRcdFx0aWYgKGNoZWNrZm9ybSkge1xuXHRcdFx0XHRcdHZhciBsb2NhbERlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXHRcdFx0XHRcdHByb21pc2VzLnB1c2gobG9jYWxEZWZlcnJlZCk7XG5cdFx0XHRcdFx0JGZvcm0udHJpZ2dlcigndmFsaWRhdG9yLnZhbGlkYXRlJywge2RlZmVycmVkOiBsb2NhbERlZmVycmVkfSk7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRmb3JtZGF0YVskZm9ybS5hdHRyKCduYW1lJykgfHwgJGZvcm0uYXR0cignaWQnKSB8fCAoJ2Zvcm1fJyArIG5ldyBEYXRlKCkuZ2V0VGltZSgpICogTWF0aC5yYW5kb20oKSldXG5cdFx0XHRcdFx0PSBqc2UubGlicy5mb3JtLmdldERhdGEoJGZvcm0pO1xuXHRcdFx0fSk7XG5cdFx0fVxuXG5cdFx0cmV0dXJuICQud2hlblxuXHRcdCAgICAgICAgLmFwcGx5KHVuZGVmaW5lZCwgcHJvbWlzZXMpXG5cdFx0ICAgICAgICAudGhlbihmdW5jdGlvbigpIHtcblx0XHRcdCAgICAgICAgcmV0dXJuIGZvcm1kYXRhO1xuXHRcdCAgICAgICAgfSwgZnVuY3Rpb24oKSB7XG5cdFx0XHQgICAgICAgIHJldHVybiBmb3JtZGF0YTtcblx0XHQgICAgICAgIH0pXG5cdFx0ICAgICAgICAucHJvbWlzZSgpO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiAgICBGdW5jdGlvbiB0byB0cmFuc2Zvcm0gdGhlIGN1c3RvbSBidXR0b25zIG9iamVjdCAod2hpY2ggaXNcblx0ICogICAgaW5jb21wYXRpYmxlIHdpdGggalF1ZXJ5IFVJKSB0byBhIGpRdWVyeSBVSSBjb21wYXRpYmxlIGZvcm1hdFxuXHQgKlxuXHQgKiAgICBAcGFyYW0gICAgICAgIHtvYmplY3R9ICAgIGRhdGFzZXQgICAgICAgIEN1c3RvbSBidXR0b25zIG9iamVjdCBmb3IgdGhlIGRpYWxvZ1xuXHQgKiAgICBAcGFyYW0gICAgICAgIHtwcm9taXNlfSAgICBkZWZlcnJlZCAgICBkZWZlcnJlZC1vYmplY3QgdG8gcmVzb2x2ZSAvIHJlamVjdCBvbiBjbG9zZVxuXHQgKiAgICBAcmV0dXJuICAgIHthcnJheX0gICAgICAgICAgICAgICAgICAgIFJldHVybnMgYSBqUXVlcnkgVUkgZGlhbG9nIGNvbXBhdGlibGUgYnV0dG9ucyBhcnJheVxuXHQgKi9cblx0dmFyIF9nZW5CdXR0b25zID0gZnVuY3Rpb24ob3B0aW9ucywgZXh0ZW5zaW9uRGVmZXJyZWQpIHtcblxuXHRcdC8vIENoZWNrIGlmIGJ1dHRvbnMgYXJlIGF2YWlsYWJsZVxuXHRcdGlmIChvcHRpb25zLmJ1dHRvbnMpIHtcblxuXHRcdFx0dmFyIHJlamVjdEhhbmRsZXIgPSBleHRlbnNpb24uZ2V0UmVqZWN0SGFuZGxlcixcblx0XHRcdFx0cmVzb2x2ZUhhbmRsZXIgPSBleHRlbnNpb24uZ2V0UmVzb2x2ZUhhbmRsZXI7XG5cblx0XHRcdCQuZWFjaChvcHRpb25zLmJ1dHRvbnMsIGZ1bmN0aW9uKGssIHYpIHtcblxuXHRcdFx0XHQvLyBTZXR1cCBjbGljayBoYW5kbGVyXG5cdFx0XHRcdG9wdGlvbnMuYnV0dG9uc1trXS5ldmVudCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdHZhciAkc2VsZiA9ICQodGhpcyk7XG5cblx0XHRcdFx0XHQvLyBJZiBhIGNhbGxiYWNrIGlzIGdpdmVuLCBleGVjdXRlIGl0IHdpdGhcblx0XHRcdFx0XHQvLyB0aGUgY3VycmVudCBzY29wZVxuXHRcdFx0XHRcdGlmICh0eXBlb2Ygdi5jYWxsYmFjayA9PT0gJ2Z1bmN0aW9uJykge1xuXHRcdFx0XHRcdFx0aWYgKCF2LmNhbGxiYWNrLmFwcGx5KCRzZWxmLCBbXSkpIHtcblx0XHRcdFx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdC8vIEFkZCB0aGUgZGVmYXVsdCBiZWhhdmlvdXJcblx0XHRcdFx0XHQvLyBmb3IgdGhlIGNsb3NlICBmdW5jdGlvbmFsaXR5XG5cdFx0XHRcdFx0Ly8gT24gZmFpbCwgcmVqZWN0IHRoZSBkZWZlcnJlZFxuXHRcdFx0XHRcdC8vIG9iamVjdCwgZWxzZSByZXNvbHZlIGl0XG5cdFx0XHRcdFx0c3dpdGNoICh2LnR5cGUpIHtcblx0XHRcdFx0XHRcdGNhc2UgJ2ZhaWwnOlxuXHRcdFx0XHRcdFx0XHRyZWplY3RIYW5kbGVyKCRzZWxmLCBleHRlbnNpb25EZWZlcnJlZCwgX2dldEZvcm1EYXRhKTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRjYXNlICdzdWNjZXNzJzpcblx0XHRcdFx0XHRcdFx0cmVzb2x2ZUhhbmRsZXIoJHNlbGYsIGV4dGVuc2lvbkRlZmVycmVkLCBfZ2V0Rm9ybURhdGEpO1xuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHRcdGNhc2UgJ2xpbmsnOlxuXHRcdFx0XHRcdFx0XHRsb2NhdGlvbi5ocmVmID0gdi52YWx1ZTtcblx0XHRcdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH07XG5cblx0XHRcdH0pO1xuXG5cdFx0fVxuXG5cdH07XG5cblxuXHR2YXIgX2ZpbmFsaXplTGF5ZXIgPSBmdW5jdGlvbigkY29udGFpbmVyLCBvcHRpb25zKSB7XG5cdFx0Ly8gUHJldmVudCBzdWJtaXQgb24gZW50ZXIgaW4gaW5uZXIgZm9ybXNcblx0XHR2YXIgJGZvcm1zID0gJGNvbnRhaW5lci5maW5kKCdmb3JtJyk7XG5cdFx0aWYgKCRmb3Jtcy5sZW5ndGgpIHtcblx0XHRcdCRmb3Jtcy5vbignc3VibWl0JywgZnVuY3Rpb24oZSkge1xuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHR9KTtcblx0XHR9XG5cblx0XHRpZiAod2luZG93LmdhbWJpbyAmJiB3aW5kb3cuZ2FtYmlvLndpZGdldHMgJiYgd2luZG93LmdhbWJpby53aWRnZXRzLmluaXQpIHtcblx0XHRcdHdpbmRvdy5nYW1iaW8ud2lkZ2V0cy5pbml0KCRjb250YWluZXIpO1xuXHRcdH1cblx0fTtcblxuXHR2YXIgX3NldExheWVyID0gZnVuY3Rpb24obmFtZSkge1xuXHRcdGlmIChqc2UubGlicy50ZW1wbGF0ZS5tb2RhbFtuYW1lXSkge1xuXHRcdFx0ZXh0ZW5zaW9uID0ganNlLmxpYnMudGVtcGxhdGUubW9kYWxbbmFtZV07XG5cdFx0fSBlbHNlIHtcblx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdbTU9EQUxdIENhblxcJ3Qgc2V0IG1vZGFsOiBcIicgKyBuYW1lICsgJ1wiLiBFeHRlbnNpb24gZG9lc25cXCd0IGV4aXN0Jyk7XG5cdFx0fVxuXHR9O1xuXG5cdHZhciBfdHJhbnNmZXJPcHRpb25zID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdHZhciBtYXBwZXIgPSBleHRlbnNpb24uZ2V0TWFwcGVyKCksXG5cdFx0XHRyZXN1bHQgPSB7fTtcblxuXHRcdCQuZWFjaChvcHRpb25zLCBmdW5jdGlvbihrLCB2KSB7XG5cblx0XHRcdGlmIChtYXBwZXJba10gPT09IGZhbHNlKSB7XG5cdFx0XHRcdHJldHVybiB0cnVlO1xuXHRcdFx0fSBlbHNlIGlmIChtYXBwZXJba10gPT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRyZXN1bHRba10gPSB2O1xuXHRcdFx0fSBlbHNlIGlmICh0eXBlb2YgbWFwcGVyW2tdID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRcdHZhciBtYXBwZXJSZXN1bHQgPSBtYXBwZXJba10oaywgdik7XG5cdFx0XHRcdHJlc3VsdFttYXBwZXJSZXN1bHRbMF1dID0gbWFwcGVyUmVzdWx0WzFdO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0cmVzdWx0W21hcHBlcltrXV0gPSB2O1xuXHRcdFx0fVxuXG5cdFx0fSk7XG5cblx0XHRyZXR1cm4gcmVzdWx0O1xuXG5cdH07XG5cblx0dmFyIF9nZXRUZW1wbGF0ZSA9IGZ1bmN0aW9uKG9wdGlvbnMsIGlmcmFtZSkge1xuXG5cdFx0dmFyICRzZWxlY3Rpb24gPSBbXSxcblx0XHRcdGRlZmVycmVkID0gJC5EZWZlcnJlZCgpO1xuXG5cdFx0aWYgKG9wdGlvbnMubm9UZW1wbGF0ZSkge1xuXHRcdFx0ZGVmZXJyZWQucmVzb2x2ZSgnJyk7XG5cdFx0fSBlbHNlIGlmIChpZnJhbWUpIHtcblx0XHRcdGRlZmVycmVkLnJlc29sdmUoJzxpZnJhbWUgd2lkdGg9XCIxMDAlXCIgaGVpZ2h0PVwiMTAwJVwiIGZyYW1lYm9yZGVyPVwiMFwiIHNyYz1cIicgKyBvcHRpb25zLnRlbXBsYXRlICsgJ1wiIC8+Jyk7XG5cdFx0fSBlbHNlIHtcblx0XHRcdGlmIChvcHRpb25zLnN0b3JlVGVtcGxhdGUgJiYgdHBsU3RvcmVbb3B0aW9ucy50ZW1wbGF0ZV0pIHtcblx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZSh0cGxTdG9yZVtvcHRpb25zLnRlbXBsYXRlXSk7XG5cdFx0XHR9IGVsc2Uge1xuXG5cdFx0XHRcdHRyeSB7XG5cdFx0XHRcdFx0JHNlbGVjdGlvbiA9ICQob3B0aW9ucy50ZW1wbGF0ZSk7XG5cdFx0XHRcdH0gY2F0Y2ggKGVycikge1xuXHRcdFx0XHR9XG5cblx0XHRcdFx0aWYgKCRzZWxlY3Rpb24ubGVuZ3RoKSB7XG5cdFx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZSgkc2VsZWN0aW9uLmh0bWwoKSk7XG5cdFx0XHRcdH0gZWxzZSB7XG5cdFx0XHRcdFx0anNlLmxpYnMueGhyLmFqYXgoe3VybDogb3B0aW9ucy50ZW1wbGF0ZSwgZGF0YVR5cGU6ICdodG1sJ30pLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdFx0XHRpZiAob3B0aW9ucy5zZWN0aW9uU2VsZWN0b3IpIHtcblx0XHRcdFx0XHRcdFx0cmVzdWx0ID0gJChyZXN1bHQpLmZpbmQob3B0aW9ucy5zZWN0aW9uU2VsZWN0b3IpLmh0bWwoKTtcblx0XHRcdFx0XHRcdH1cblxuXHRcdFx0XHRcdFx0aWYgKG9wdGlvbnMuc3RvcmVUZW1wbGF0ZSkge1xuXHRcdFx0XHRcdFx0XHR0cGxTdG9yZVtvcHRpb25zLnRlbXBsYXRlXSA9IHJlc3VsdDtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdGRlZmVycmVkLnJlc29sdmUocmVzdWx0KTtcblx0XHRcdFx0XHR9KS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0ZGVmZXJyZWQucmVqZWN0KCk7XG5cdFx0XHRcdFx0fSk7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHR9XG5cblx0XHRyZXR1cm4gZGVmZXJyZWQ7XG5cdH07XG5cblx0dmFyIF9jcmVhdGVMYXllciA9IGZ1bmN0aW9uKG9wdGlvbnMsIHRpdGxlLCBjbGFzc05hbWUsIGRlZmJ1dHRvbnMsIHRlbXBsYXRlKSB7XG5cdFx0Ly8gU2V0dXAgZGVmYXVsdHMgJiBkZWZlcnJlZCBvYmplY3RzXG5cdFx0dmFyIGRlZmVycmVkID0gJC5EZWZlcnJlZCgpLFxuXHRcdFx0cHJvbWlzZSA9IGRlZmVycmVkLnByb21pc2UoKSxcblx0XHRcdGlmcmFtZSA9ICh0ZW1wbGF0ZSA9PT0gJ2lmcmFtZScpLFxuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdHRpdGxlOiB0aXRsZSxcblx0XHRcdFx0ZGlhbG9nQ2xhc3M6IGNsYXNzTmFtZSxcblx0XHRcdFx0bW9kYWw6IHRydWUsXG5cdFx0XHRcdGJ1dHRvbnM6IGRlZmJ1dHRvbnMgfHwgW10sXG5cdFx0XHRcdGNsb3NlT25Fc2NhcGU6IHRydWUsXG5cdFx0XHRcdHRlbXBsYXRlOiB0ZW1wbGF0ZSB8fCBudWxsLFxuXHRcdFx0XHRzdG9yZVRlbXBsYXRlOiBmYWxzZSxcblx0XHRcdFx0Y2xvc2VYOiB0cnVlLFxuXHRcdFx0XHRjbG9zZU9uT3V0ZXI6IHRydWVcblx0XHRcdH0sXG5cdFx0XHRpbnN0YW5jZSA9IG51bGwsXG5cdFx0XHQkZm9ybXMgPSBudWxsLFxuXHRcdFx0ZXh0ZW5zaW9uRGVmZXJyZWQgPSAkLkRlZmVycmVkKCk7XG5cblx0XHQvLyBNZXJnZSBjdXN0b20gc2V0dGluZ3Mgd2l0aCBkZWZhdWx0IHNldHRpbmdzXG5cdFx0b3B0aW9ucyA9IG9wdGlvbnMgfHwge307XG5cdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHt9LCBkZWZhdWx0cywgb3B0aW9ucyk7XG5cblx0XHR2YXIgdHBsUmVxdWVzdCA9IF9nZXRUZW1wbGF0ZShvcHRpb25zLCBpZnJhbWUpLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cblx0XHRcdGV4dGVuc2lvbkRlZmVycmVkLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdGRlZmVycmVkLnJlc29sdmUocmVzdWx0KTtcblx0XHRcdH0pLmZhaWwoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdGRlZmVycmVkLnJlamVjdChyZXN1bHQpO1xuXHRcdFx0fSk7XG5cblx0XHRcdC8vIEdlbmVyYXRlIHRlbXBsYXRlXG5cdFx0XHRvcHRpb25zLnRlbXBsYXRlID0gJChNdXN0YWNoZS5yZW5kZXIocmVzdWx0LCBvcHRpb25zKSk7XG5cdFx0XHRqc2UubGlicy50ZW1wbGF0ZS5oZWxwZXJzLnNldHVwV2lkZ2V0QXR0cihvcHRpb25zLnRlbXBsYXRlKTtcblx0XHRcdG9wdGlvbnMudGVtcGxhdGUgPSAkKCc8ZGl2PicpLmFwcGVuZChvcHRpb25zLnRlbXBsYXRlLmNsb25lKCkpLmh0bWwoKTtcblxuXHRcdFx0Ly8gR2VuZXJhdGUgZGVmYXVsdCBidXR0b24gb2JqZWN0XG5cdFx0XHRfZ2VuQnV0dG9ucyhvcHRpb25zLCBleHRlbnNpb25EZWZlcnJlZCk7XG5cblx0XHRcdC8vIFRyYW5zZmVyIG9wdGlvbnMgb2JqZWN0IHRvIGV4dGVuc2lvbiBvcHRpb24gb2JqZWN0XG5cdFx0XHR2YXIgb3JpZ2luYWxPcHRpb25zID0gJC5leHRlbmQoe30sIG9wdGlvbnMpO1xuXHRcdFx0b3B0aW9ucyA9IF90cmFuc2Zlck9wdGlvbnMob3B0aW9ucyk7XG5cblx0XHRcdC8vIENhbGwgZXh0ZW5zaW9uXG5cdFx0XHRleHRlbnNpb24ub3BlbkxheWVyKG9wdGlvbnMsIGV4dGVuc2lvbkRlZmVycmVkLCBfZ2V0Rm9ybURhdGEsIG9yaWdpbmFsT3B0aW9ucyk7XG5cblx0XHRcdC8vIFBhc3N0aHJvdWdoIG9mIHRoZSBjbG9zZSBtZXRob2Qgb2YgdGhlIGxheWVyXG5cdFx0XHQvLyB0byB0aGUgbGF5ZXIgY2FsbGVyXG5cdFx0XHRwcm9taXNlLmNsb3NlID0gZnVuY3Rpb24oc3VjY2Vzcykge1xuXHRcdFx0XHRleHRlbnNpb25EZWZlcnJlZC5jbG9zZShzdWNjZXNzKTtcblx0XHRcdH07XG5cblx0XHR9KS5mYWlsKGZ1bmN0aW9uKCkge1xuXHRcdFx0ZGVmZXJyZWQucmVqZWN0KHtlcnJvcjogJ1RlbXBsYXRlIG5vdCBmb3VuZCd9KTtcblx0XHR9KTtcblxuXHRcdC8vIFRlbXBvcmFyeSBjbG9zZSBoYW5kbGVyIGlmIHRoZSB1cHBlclxuXHRcdC8vIGRlZmVycmVkIGlzbid0IGZpbmlzaGVkIG5vdy4gSXQgd2lsbCBiZVxuXHRcdC8vIG92ZXJ3cml0dGVuIGFmdGVyIHRoZSBsYXllciBvcGVuc1xuXHRcdGlmICghcHJvbWlzZS5jbG9zZSkge1xuXHRcdFx0cHJvbWlzZS5jbG9zZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHR0cGxSZXF1ZXN0LnJlamVjdCgnQ2xvc2VkIGFmdGVyIG9wZW5pbmcnKTtcblx0XHRcdH07XG5cdFx0fVxuXG5cdFx0cmV0dXJuIHByb21pc2U7XG5cdH07XG5cblxuXHQvKipcblx0ICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGFuIGFsZXJ0LWxheWVyXG5cdCAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgb3B0aW9ucyBPcHRpb25zIHRoYXQgYXJlIHBhc3NlZCB0byB0aGUgbW9kYWwgbGF5ZXJcblx0ICogICAgQHJldHVybiAgICB7cHJvbWlzZX0gICAgICAgICAgICBSZXR1cm5zIGEgcHJvbWlzZVxuXHQgKi9cblx0dmFyIF9hbGVydCA9IGZ1bmN0aW9uKG9wdGlvbnMpIHtcblx0XHRyZXR1cm4gX2NyZWF0ZUxheWVyKG9wdGlvbnMsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdoaW50JywgJ2xhYmVscycpLCAnJywgW2J1dHRvbnMuY2xvc2VdLCAnI21vZGFsX2FsZXJ0Jyk7XG5cdH07XG5cblx0LyoqXG5cdCAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhbiBjb25maXJtLWxheWVyXG5cdCAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgb3B0aW9ucyBPcHRpb25zIHRoYXQgYXJlIHBhc3NlZCB0byB0aGUgbW9kYWwgbGF5ZXJcblx0ICogICAgQHJldHVybiAgICB7cHJvbWlzZX0gICAgICAgICAgICBSZXR1cm5zIGEgcHJvbWlzZVxuXHQgKi9cblx0dmFyIF9jb25maXJtID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2NvbmZpcm0nLCAnbGFiZWxzJyksICdjb25maXJtX2RpYWxvZycsIFtcblx0XHRcdGJ1dHRvbnMueWVzLFxuXHRcdFx0YnV0dG9ucy5ub1xuXHRcdF0sICcjbW9kYWxfYWxlcnQnKTtcblx0fTtcblxuXHQvKipcblx0ICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGEgcHJvbXB0LWxheWVyXG5cdCAqICAgIEBwYXJhbSAgICAgICAge29iamVjdH0gICAgb3B0aW9ucyBPcHRpb25zIHRoYXQgYXJlIHBhc3NlZCB0byB0aGUgbW9kYWwgbGF5ZXJcblx0ICogICAgQHJldHVybiAgICB7cHJvbWlzZX0gICAgICAgICAgICBSZXR1cm5zIGEgcHJvbWlzZVxuXHQgKi9cblx0dmFyIF9wcm9tcHQgPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0cmV0dXJuIF9jcmVhdGVMYXllcihvcHRpb25zLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgncHJvbXB0JywgJ2xhYmVscycpLCAncHJvbXB0X2RpYWxvZycsIFtcblx0XHRcdGJ1dHRvbnMub2ssXG5cdFx0XHRidXR0b25zLmFib3J0XG5cdFx0XSwgJyNtb2RhbF9wcm9tcHQnKTtcblx0fTtcblxuXHQvKipcblx0ICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGFuIHN1Y2Nlc3MtbGF5ZXJcblx0ICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICBvcHRpb25zIE9wdGlvbnMgdGhhdCBhcmUgcGFzc2VkIHRvIHRoZSBtb2RhbCBsYXllclxuXHQgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG5cdCAqL1xuXHR2YXIgX3N1Y2Nlc3MgPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0cmV0dXJuIF9jcmVhdGVMYXllcihvcHRpb25zLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnc3VjY2VzcycsICdsYWJlbHMnKSwgJ3N1Y2Nlc3NfZGlhbG9nJywgW10sIFxuXHRcdFx0JyNtb2RhbF9hbGVydCcpO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiAgICBTaG9ydGN1dCBmdW5jdGlvbiBmb3IgYW4gZXJyb3ItbGF5ZXJcblx0ICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICBvcHRpb25zIE9wdGlvbnMgdGhhdCBhcmUgcGFzc2VkIHRvIHRoZSBtb2RhbCBsYXllclxuXHQgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG5cdCAqL1xuXHR2YXIgX2Vycm9yID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9ycycsICdsYWJlbHMnKSwgJ2Vycm9yX2RpYWxvZycsIFtdLCAnI21vZGFsX2FsZXJ0Jyk7XG5cdH07XG5cblx0LyoqXG5cdCAqICAgIFNob3J0Y3V0IGZ1bmN0aW9uIGZvciBhIHdhcm5pbmctbGF5ZXJcblx0ICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICBvcHRpb25zIE9wdGlvbnMgdGhhdCBhcmUgcGFzc2VkIHRvIHRoZSBtb2RhbCBsYXllclxuXHQgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG5cdCAqL1xuXHR2YXIgX3dhcm4gPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0cmV0dXJuIF9jcmVhdGVMYXllcihvcHRpb25zLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnd2FybmluZycsICdsYWJlbHMnKSwgJ3dhcm5fZGlhbG9nJywgW10sICcjbW9kYWxfYWxlcnQnKTtcblx0fTtcblxuXHQvKipcblx0ICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGFuIGluZm8tbGF5ZXJcblx0ICogICAgQHBhcmFtICAgICAgICB7b2JqZWN0fSAgICBvcHRpb25zIE9wdGlvbnMgdGhhdCBhcmUgcGFzc2VkIHRvIHRoZSBtb2RhbCBsYXllclxuXHQgKiAgICBAcmV0dXJuICAgIHtwcm9taXNlfSAgICAgICAgICAgIFJldHVybnMgYSBwcm9taXNlXG5cdCAqL1xuXHR2YXIgX2luZm8gPSBmdW5jdGlvbihvcHRpb25zKSB7XG5cdFx0cmV0dXJuIF9jcmVhdGVMYXllcihvcHRpb25zLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnaW5mbycsICdsYWJlbHMnKSwgJ2luZm9fZGlhbG9nJywgW10sICcjbW9kYWxfYWxlcnQnKTtcblx0fTtcblxuXHQvKipcblx0ICogICAgU2hvcnRjdXQgZnVuY3Rpb24gZm9yIGFuIGlmcmFtZS1sYXllclxuXHQgKiAgICBAcGFyYW0gICAgICAgIHtvYmplY3R9ICAgIG9wdGlvbnMgT3B0aW9ucyB0aGF0IGFyZSBwYXNzZWQgdG8gdGhlIG1vZGFsIGxheWVyXG5cdCAqICAgIEByZXR1cm4gICAge3Byb21pc2V9ICAgICAgICAgICAgUmV0dXJucyBhIHByb21pc2Vcblx0ICovXG5cdHZhciBfaWZyYW1lID0gZnVuY3Rpb24ob3B0aW9ucykge1xuXHRcdGlmIChvcHRpb25zLmNvbnZlcnRNb2RhbCkge1xuXHRcdFx0anNlLmxpYnMudGVtcGxhdGUubW9kYWxbb3B0aW9ucy5jb252ZXJ0TW9kYWxdKG9wdGlvbnMsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdpbmZvJywgJ2xhYmVscycpLFxuXHRcdFx0ICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIG9wdGlvbnMuY29udmVydE1vZGFsICsgJ19kaWFsb2cnLCBbXSwgJyNtb2RhbF9hbGVydCcpO1xuXHRcdFx0cmV0dXJuO1xuXHRcdH1cblxuXHRcdHJldHVybiBfY3JlYXRlTGF5ZXIob3B0aW9ucywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2luZm8nLCAnbGFiZWxzJyksICdpZnJhbWVfbGF5ZXInLCBbXSwgJ2lmcmFtZScpO1xuXHR9O1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIEVYUE9SVCAjIyMjIyMjIyMjXG5cblx0ZXhwb3J0cy5lcnJvciA9IF9lcnJvcjtcblx0ZXhwb3J0cy53YXJuID0gX3dhcm47XG5cdGV4cG9ydHMuaW5mbyA9IF9pbmZvO1xuXHRleHBvcnRzLnN1Y2Nlc3MgPSBfc3VjY2Vzcztcblx0ZXhwb3J0cy5hbGVydCA9IF9hbGVydDtcblx0ZXhwb3J0cy5wcm9tcHQgPSBfcHJvbXB0O1xuXHRleHBvcnRzLmNvbmZpcm0gPSBfY29uZmlybTtcblx0ZXhwb3J0cy5pZnJhbWUgPSBfaWZyYW1lO1xuXHRleHBvcnRzLmN1c3RvbSA9IF9jcmVhdGVMYXllcjtcblx0ZXhwb3J0cy5zZXRMYXllciA9IF9zZXRMYXllcjtcblx0ZXhwb3J0cy5maW5hbGl6ZUxheWVyID0gX2ZpbmFsaXplTGF5ZXI7XG5cblx0Ly8gU2V0IGRlZmF1bHQgbGF5ZXIuXG5cdHZhciBjdXJyZW50VGltZXN0YW1wID0gRGF0ZS5ub3csXG5cdFx0bGlmZXRpbWUgPSAxMDAwMDsgLy8gMTAgc2VjXG5cblx0ZXh0ZW5zaW9uID0ganNlLmNvcmUucmVnaXN0cnkuZ2V0KCdtYWluTW9kYWxMYXllcicpO1xuXG5cdHZhciBpbnR2ID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XG5cdFx0aWYgKGpzZS5saWJzLnRlbXBsYXRlLm1vZGFsW2V4dGVuc2lvbl0gIT09IHVuZGVmaW5lZCkge1xuXHRcdFx0X3NldExheWVyKGV4dGVuc2lvbik7XG5cdFx0XHRjbGVhckludGVydmFsKGludHYpO1xuXHRcdH1cblxuXHRcdGlmIChEYXRlLm5vdyAtIGN1cnJlbnRUaW1lc3RhbXAgPiBsaWZldGltZSkge1xuXHRcdFx0dGhyb3cgbmV3IEVycm9yKCdNb2RhbCBleHRlbnNpb24gd2FzIG5vdCBsb2FkZWQ6ICcgKyBleHRlbnNpb24pO1xuXHRcdH1cblx0fSwgMzAwKTtcblxuXG59KGpzZS5saWJzLnRlbXBsYXRlLm1vZGFsKSk7Il19
