/* --------------------------------------------------------------
 image_processing.js 2015-09-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Image Processing
 *
 * This module will execute the image processing by sending POST-Requests to the
 * ImageProcessingController interface
 *
 * @module Controllers/image_processing
 */
gx.controllers.module(
	'image_processing',
	
	[
		gx.source + '/libs/info_messages'
	],
	
	/**  @lends module:Controllers/image_processing */
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Module Selector
			 *
			 * @var {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options
			 *
			 * @type {object}
			 */
			defaults = {},
			
			/**
			 * Flag if an error occurred during the image processing
			 *
			 * @type {boolean}
			 */
			error = false,
			
			/**
			 * Final Options
			 *
			 * @var {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Reference to the info messages library
			 * 
			 * @type {object}
			 */
			messages = jse.libs.info_messages,
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// EVENT HANDLERS
		// ------------------------------------------------------------------------
		
		var _onClick = function() {
			var title = jse.core.lang.translate('image_processing_title', 'image_processing');
			
			$('.process-modal').dialog({
				'title': title,
				'modal': true,
				'dialogClass': 'gx-container',
				'buttons': [
					{
						'text': jse.core.lang.translate('close', 'buttons'),
						'class': 'btn',
						'click': function() {
							$(this).dialog('close');
						}
					}
				],
				'width': 580
			});
			
			_processImage(1);
		};
		
		// ------------------------------------------------------------------------
		// AJAX
		// ------------------------------------------------------------------------
		
		var _processImage = function(imageNumber) {
			
			$.ajax({
				'type': 'POST',
				'url': 'admin.php?do=ImageProcessing/Process',
				'timeout': 30000,
				'dataType': 'json',
				'context': this,
				'data': {
					'image_number': imageNumber
				},
				success: function(response) {
					var progress = (100 / response.payload.imagesCount) * imageNumber;
					progress = Math.round(progress);
					
					$('.process-modal .progress-bar').attr('aria-valuenow', progress);
					$('.process-modal .progress-bar').css('min-width', '70px');
					$('.process-modal .progress-bar').css('width', progress + '%');
					$('.process-modal .progress-bar').html(imageNumber + ' / ' + response.payload.imagesCount);
					
					if (!response.success) {
						error = true;
					}
					
					if (!response.payload.finished) {
						imageNumber += 1;
						_processImage(imageNumber);
					} else {
						
						$('.process-modal').dialog('close');
						$('.process-modal .progress-bar').attr('aria-valuenow', 0);
						$('.process-modal .progress-bar').css('width', '0%');
						$('.process-modal .progress-bar').html('');
						
						if (error) {
							messages.addError(jse.core.lang.translate('image_processing_error',
								'image_processing'));
						} else {
							messages.addSuccess(jse.core.lang.translate('image_processing_success',
								'image_processing'));
						}
						
						error = false;
					}
				}
			});
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('click', '.js-process', _onClick);
			done();
		};
		
		return module;
	});
