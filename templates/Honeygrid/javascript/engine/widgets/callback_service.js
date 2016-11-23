/* --------------------------------------------------------------
 callback_service.js 2016-02-01 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */


/**
 * Checks the input values of the callback form and shows messages on error or success.
 */
gambio.widgets.module(
	'callback_service',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ########## VARIABLE INITIALIZATION ##########
		
		var $this = $(this),
			defaults = {
				'successSelector': '#callback-service .alert-success',
				'errorSelector': '#callback-service .alert-danger',
				'vvCodeSelector': '#callback-service #vvcode',
				'vvCodeImageSelector': '#callback-service #vvcode_image'
			},
			options = $.extend(true, {}, defaults, data),
			module = {};
		
		
		// ########## EVENT HANDLER ##########
		
		/**
		 * Validates the form data. If an error occurs it will show the error message, otherwise the messages will 
		 * be hidden.
		 * 
		 * @return {boolean}
		 * @private
		 */
		var _onSubmit = function () {
			
			var deferred = new $.Deferred();
			$(options.successSelector).addClass('hidden');
			$(options.errorSelector).addClass('hidden');
			
			$.ajax({
				data: 		$this.serialize(),
				url: 		'request_port.php?module=CallbackService&action=check',
				type: 		'GET',
				dataType: 	'html',
				success: 	function(error_message)
				{					
					if(error_message.length > 0) {
						$(options.errorSelector).html(error_message).removeClass('hidden');
						
						try {
							Recaptcha.reload();
						} catch (e) {
							$(options.vvCodeSelector).val('');
							$(options.vvCodeImageSelector).attr('src', 'request_port.php?rand=' + Math.random() 
								+ '&module=CreateVVCode');
						}
						
						deferred.reject();
						
					} else {						
						deferred.resolve();
					}
				}
			});
			deferred.done(_submitForm);
			return false;
		};
		
		
		/**
		 * Submits the form data and shows a success message on success.
		 * 
		 * @private
		 */
		var _submitForm = function () {
			
			$.ajax({
				data: 		$this.serialize(),
				url: 		'request_port.php?module=CallbackService&action=send',
				type: 		'POST',
				dataType: 	'html',
				success: 	function(message)
				{
					if(message.length > 0) {
						$(options.successSelector).html(message).removeClass('hidden');
						
						try	{
							Recaptcha.reload();
						} catch (e)	{
							$(options.vvCodeSelector).val('');
							$(options.vvCodeImageSelector).attr('src', 'request_port.php?rand=' + Math.random() 
								+ '&module=CreateVVCode');
						}
					}
				}
			});
		};
		
		// ########## INITIALIZATION ##########
		
		/**
		 * Init function of the widget
		 * @constructor
		 */
		module.init = function(done) {
			
			$this.on('submit', _onSubmit);
			
			done();
		};
		
		// Return data to widget engine
		return module;
	});