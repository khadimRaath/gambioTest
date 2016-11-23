/* --------------------------------------------------------------
 cookies_notice_controller.js 2016-04-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Cookie Notice Controller
 *
 * Compatibility module that handles the "Cookie Notice" page under the "Rights" menu of "Shop Settings" section. 
 * The data of the form are updated upon change and this module will just post them to LawsController. Check out 
 * the fields that are language dependent, they will be changed when the user selects a language from the language 
 * switcher component.
 *
 * @module Compatibility/cookie_notice_controller
 */
gx.compatibility.module(
	'cookie_notice_controller',
	
	['loading_spinner'],
	
	function(data) {
		
		'use strict';
		
		var
			/**
			 * Module Selector
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Module Instance
			 *
			 * @type {object}
			 */
			module = {
				model: {
					formData: jse.core.config.get('appUrl') +
					'/admin/admin.php?do=Laws/GetCookiePreferences&pageToken=' + jse.core.config.get('pageToken')
				}
			};
		
		/**
		 * Show message in ".message_stack_container" object. 
		 * 
		 * The message will be hidden after 5 seconds.
		 * 
		 * @param {string} text The text to be displayed.
		 * @param {string} type Provide "success" or "danger". 
		 */
		var _showMessage = function(text, type) {
			var $messageEntry = $('<div/>'),
				$messageStack = $('.message_stack_container');
			$messageEntry
				.addClass('alert alert-' + type)
				.text(text)
				.appendTo('.message_stack_container');
			$messageStack.removeClass('hidden');
			setTimeout(function() {
				$messageEntry.remove();
				$messageStack.addClass('hidden');
			}, 5000);
		};
		
		/**
		 * Initialize Module
		 */
		module.init = function(done) {
			// Form submit event handler. 
			$this.on('submit', function(e) {
				e.preventDefault();
				
				// Prepare form data and send them to the LawsController class. 
				var postUrl = jse.core.config.get('appUrl') + '/admin/admin.php?do=Laws/SaveCookiePreferences',
					postData = $.extend({pageToken: jse.core.config.get('pageToken')}, module.model.formData),
					$spinner;
				
				$.ajax({
						url: postUrl,
						type: 'POST',
						data: postData,
						dataType: 'json',
						beforeSend: function() {
							$spinner = jse.libs.loading_spinner.show($this);
						}
					})
					.done(function() { // Display success message.
						_showMessage(jse.core.lang.translate('TXT_SAVE_SUCCESS', 'admin_general'), 'success');
					})
					.fail(function(jqxhr, textStatus, errorThrown) { // Display failure message.
						_showMessage(jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general'), 'danger');
						jse.core.debug.error('Could not save Cookie Notice preferences:', jqxhr, textStatus, 
							errorThrown); 
					})
					.always(function() {
						jse.libs.loading_spinner.hide($spinner);

						// Scroll to the top, so that the user sees the appropriate message.
						$('html, body').animate({ scrollTop: 0 });
					});
			});
			
			// Language change event handler. 
			$('.languages').on('click', 'a', function(e) {
				e.preventDefault();
				
				$(this).siblings().removeClass('active');
				$(this).addClass('active');
				
				// Load the language specific fields.
				$.each(module.model.formData, function(name, value) {
					var $element = $this.find('[name="' + name + '"]');
					
					if ($element.data('multilanguage') !== undefined) {
						var selectedLanguageCode = $('.languages a.active').data('code');
						$element.val(value[selectedLanguageCode]);
						if ($element.is('textarea')) {
							CKEDITOR.instances[name].setData(value[selectedLanguageCode]);
						} 
					} else {
						$element.val(value);

						if ($element.is(':checkbox') && value === 'true') {
							$element.parent().addClass('checked'); 
							$element.prop('checked', true);
						}

						if (name === 'position' && !value) {
							$element.find('option[value="top"]').prop('selected', true).trigger('change');
						}
					}
				});
			});
			
			// Input change event handlers.
			$this.on('change', 'input:hidden, input:text, select, textarea', function() {
				if ($(this).data('multilanguage') !== undefined) {
					var selectedLanguageCode = $('.languages a.active').data('code');
					module.model.formData[$(this).attr('name')][selectedLanguageCode] = $(this).val();
				} else {
					module.model.formData[$(this).attr('name')] = $(this).val();
				}
			});
			
			$this.on('click', '.switcher', function() {
				module.model.formData[$(this).find('input:checkbox').attr('name')] = $(this).hasClass('checked');
			});
			
			// CKEditor change event handler. 
			for (var i in CKEDITOR.instances) {
				CKEDITOR.instances[i].on('change', function() {
					CKEDITOR.instances[i].updateElement();
					$('[name="' + i + '"]').trigger('change');
				});
			}
			
			// Select active language.
			$('.languages').find('.active').click();

			// Set the color-preview colors.
			$this.find('.color-preview').each(function() {
				$(this).css('background-color', $(this).siblings('input:hidden').val()); 
			});
			
			done();
		};
		
		return module;
	});