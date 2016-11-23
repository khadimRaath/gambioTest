/* --------------------------------------------------------------
 product_question.js 2016-08-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that updates that opens a lightbox for asking product questions. Sends an e-mail to the shop administrator
 * with the asked question
 */
gambio.widgets.module(
	'product_question',
	
	['xhr', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'],
	
	function(data) {
		
		'use strict';
		
		// ########## VARIABLE INITIALIZATION ##########
		
		var $this = $(this),
			$body = $('body'),
			defaults = {
				btnOpen: '.btn-product-question',
				btnClose: '.btn-close-question-window',
				btnSend: '.btn-send-question',
				url: 'shop.php?do=ProductQuestion',
				sendUrl: 'shop.php?do=ProductQuestion/Send',
				productId: 0,
				formSelector: '#product-question-form'
			},
			options = $.extend(true, {}, defaults, data),
			module = {};
		
		
		// ########## EVENT HANDLER ##########
		
		var _validateForm = function() {
			try {
				var $privacyCheckbox = $('#privacy_accepted'), 
					error = false;
				
				$this.find('.form-group.mandatory, .checkbox-inline').removeClass('has-error'); 
				
				// Validate required fields. 
				$this.find('.form-group.mandatory').each(function() {
					var $formControl = $(this).find('.form-control'); 
					
					if ($formControl.val() === '') {
						$(this).addClass('has-error');
						error = true;
					}
				});
				
				if ($privacyCheckbox.length && !$privacyCheckbox.prop('checked')) {
					$privacyCheckbox.closest('.checkbox-inline').addClass('has-error');
					error = true;
				}
				
				if (error) {
					throw new Error();
				}
				
				return true;
			} catch(exception) {
				return false;
			}
		};
		
		var _openModal = function() {
			jse.libs.xhr.get({ url: options.url + '&productId=' + options.productId }, true)
				.done(function(response) {
					_closeModal();
					$body.append(response.content);
					gambio.widgets.init($('.mfp-wrap'));
					_activateGoogleRecaptcha();
				});
		};
		
		var _closeModal = function() {
			$('.mfp-bg, .mfp-wrap').remove();
			$(options.btnSend).off('click', _sendForm);
			$(options.btnClose).off('click', _closeModal);
		};
		
		var _sendForm = function() {
			if (!_validateForm()) {
				return; 
			}
			
			var url = options.sendUrl+'&productId='+options.productId,
				data = $(options.formSelector).serialize() + '&productLink=' + location.href;
			
			$.ajax({
				url: url,  
				data: data,  
				type: 'POST', 
				dataType: 'json'
			}).done(function(response) {
				_closeModal();
				$body.append(response.content);
				gambio.widgets.init($('.mfp-wrap'));
				
				if (!response.success) {
					_activateGoogleRecaptcha();
				}
			});
		};
		
		var _activateGoogleRecaptcha = function() {
			if (typeof(window.showRecaptcha) === 'function') {
				setTimeout(function() {
					window.showRecaptcha('captcha_wrapper');
				}, 500);
			}
		};
		
		// ########## INITIALIZATION ##########
		
		/**
		 * Init function of the widget
		 */
		module.init = function(done) {
			if (options.modalMode === undefined) {
				$(options.btnOpen).on('click', _openModal);
			}
			$(options.btnSend).on('click', _sendForm);
			$(options.btnClose).on('click', _closeModal);
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});