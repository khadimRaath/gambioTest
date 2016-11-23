/* --------------------------------------------------------------
 modal.js 2016-02-23 
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.template.modal = jse.libs.template.modal || {};
jse.libs.template.modal.magnific = jse.libs.template.modal.magnific || {};

/**
 * ## Honeygrid Modal Magnific (Library Extension)
 * 
 * Library-function to open default modal layer. This function depends on jQuery & jQuery UI.
 *
 * @module Honeygrid/Libs/modal.ext-magnific
 * @exports jse.libs.modal.ext-magnific
 * @ignore
 */
(function(exports) {
	'use strict';

	var $document = $(document),
		$body = $('body');

	var _openLayer = function(dataset, deferred, getFormData, originalOptions) {

		var $wrap = null,
			$bg = null,
			$buttons = null,
			$closeX = null,
			$forms = null,
			promise = deferred.promise(),
			instance = null,
			defaults = {
				preloader: false
			},
			options = $.extend({}, defaults, dataset),
			uid = parseInt(Math.random() * 100000);

		// ADD BUTTON INFORMATION
		$.each(options.buttons, function(i, v) {
			options.showButtons = true;
			v.index = i;
			v.uid = uid;
		});

		// GENERATE LAYER
		options.items.src = Mustache.render($('#magnific_wrapper').html(), options);

		$.magnificPopup.open(options);
		instance = $.magnificPopup.instance;

		// GET SELECTIONS
		$wrap = $(instance.wrap);
		$bg = $(instance.bgOverlay);
		$buttons = $wrap.find('.modal-footer button');
		$closeX = $wrap.find('button.mfp-close');

// ########## EVENT HANDLER ##########

		// REMOVE MAGNIFIC EVENT HANDLER
		$wrap.off('click.mfp');
		$bg.off('click.mfp');
		$document.off('keyup.mfp');


		// BIND BUTTON HANDLER
		$buttons.each(function() {
			var $self = $(this),
				data = $self.data();

			if (typeof data.index === 'number') {
				$self.on('click', dataset.buttons[data.index].event);
			}
		});

		// BIND EVENT HANDLER FOR THE CLOSE BUTTON
		$closeX
			.off('click')
			.on('click', function(e) {
				e.stopPropagation();
				_rejectHandler($wrap, deferred, getFormData);
			});

		// BIND EVENT HANDLER FOR BACKGROUND LAYER
		if (dataset.closeOnBgClick) {

			$wrap
				.off('click')
				.on('click', function(e) {
					if (!$(e.target).closest('.modal-dialog').length) {
						_rejectHandler($wrap, deferred, getFormData);
					}
				});

		}

		// BIND CLOSE HANDLER FOR ESC-KEY
		if (dataset.enableEscapeKey) {

			$document
				.on('keyup.magnific', function(e) {
					if (e.keyCode === 27) {
						_rejectHandler($wrap, deferred, getFormData);
					}
				});

		}

		// ADD A CLOSE LAYER METHOD TO THE PROMISE
		// TODO: TESTING
		deferred.close = function(success) {
			if (success) {
				_resolveHandler($wrap, deferred, getFormData);
			} else {
				_rejectHandler($wrap, deferred, getFormData);
			}
		};

		// EXECUTE ADDITIONAL FUNCTION CODE ON LAYER OPEN
		if (options.executeCode && typeof options.executeCode === 'function') {
			options.executeCode.call($wrap);
		}


		if (originalOptions.bootstrapClass !== undefined) {
			$wrap.find('.modal-dialog').addClass(originalOptions.bootstrapClass);
		}

		if (originalOptions.zIndex !== undefined) {
			$wrap.css('z-index', originalOptions.zIndex);
		}

		jse.libs.template.modal.finalizeLayer($wrap, originalOptions);

		return promise;
	};

	var _convertTemplate = function(key, value) {
		var newValue = {
			src: value,
			type: 'inline'
		};

		return ['items', newValue];
	};

	var _getMapper = function() {
		return {
			dialogClass: 'mainClass',
			modal: false,
			closeOnEscape: 'enableEscapeKey',
			closeOnOuter: 'closeOnBgClick',
			closeX: 'showCloseBtn',
			storeTemplate: false,
			template: _convertTemplate
		};
	};

	var _rejectHandler = function($element, deferred, getFormData) {
		$element = $element.closest('.mfp-wrap');
		getFormData($element).always(function(result) {
			$document.off('keyup.magnific');
			deferred.reject(result);
			$.magnificPopup.close();
		});
	};

	var _resolveHandler = function($element, deferred, getFormData) {
		$element = $element.closest('.mfp-wrap');
		getFormData($element, true).done(function(result) {
			$document.off('keyup.magnific');
			deferred.resolve(result);
			$.magnificPopup.close();
		});
	};


// ########## VARIABLE EXPORT ##########

	exports.openLayer = _openLayer;
	exports.getMapper = _getMapper;
	exports.getResolveHandler = _resolveHandler;
	exports.getRejectHandler = _rejectHandler;

}(jse.libs.template.modal.magnific));