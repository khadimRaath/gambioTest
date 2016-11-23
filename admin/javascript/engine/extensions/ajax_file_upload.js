/* --------------------------------------------------------------
 ajax_file_upload.js 2016-02-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## AJAX File Upload Extension
 *
 * This extension will enable an existing **input[type=file]** element to upload files through AJAX.
 * The upload method can be invoked either manually by calling the "upload" function or automatically
 * once the file is selected. A "validate" event is triggered before upload starts so that you can
 * validate the selected file before it is uploaded and stop the procedure if needed.
 *
 * Currently the module supports the basic upload functionality but you can add extra logic on your own
 * by following code examples in the official page of the plugin.
 * 
 * The "auto" option is enabled by default which means that the extension will automatically trigger 
 * the upload operation when the user selects a file.
 *
 * {@link https://github.com/blueimp/jQuery-File-Upload/wiki/Basic-plugin}
 *
 * **Important**: If you need to support older versions of Internet Explorer you should use the automatic upload 
 * mode because the manual mode uses the JavaScript File API and this is supported from IE 10+.
 *
 * ### Options
 * 
 * **URL | `data-ajax_upload_file-url` | String | Required**
 * 
 * Define the upload URL that will handle the file upload.
 * 
 * **Auto | `data-ajax_upload_file-auto` | Boolean | Optional**
 * 
 * Define whether the upload process will be started automatically after the user selects a file.
 *
 * ### Events
 * ```javascript
 * // Add your validation rules, triggered before upload (Manual Mode - Requires JS file API support).
 * $('#upload-file').on('validate', function(event, file) {});
 * 
 * // Triggered when server responds to upload request (Manual + Auto Mode).
 * $('#upload-file').on('upload', function(event, response) {});
 * ```
 * 
 * ### Methods
 * ```javascript
 * // Trigger the selected file validation, returns a bool value.
 * $('#upload-file').validate(); 
 * 
 * // Trigger the file upload, callback argument is optional.
 * $('#upload-file').upload(callback); 
 * ```
 *
 * ### Example
 * 
 * **Automatic Upload**
 * 
 * The upload process will be triggered automatically after the user selects a file.
 * 
 * ```html
 * <!-- HTML -->
 * <input id="upload-file" type="file" data-gx-extension="ajax_file_upload"
 *             data-ajax_file_upload-url="http://url/to/upload-script.php" />
 *
 * <!-- JavaScript -->
 * <script>
 *     $('#upload-file').on('validate', function(event, file) {
 *          // Validation Checks (Only IE 10+) ...
 *          return true; // Return true for success or false for failure - will stop the upload.
 *     });
 *
 *     $('#upload-file').on('upload', function(event, response) {
 *          // The "response" parameter contains the server's response information.
 *     });
 * </script>
 * ```
 * 
 * **Manual Upload**
 * 
 * The upload process needs to be triggered through JavaScript as shown in the following example.
 * 
 * 
 * ```html
 * <!-- HTML -->
 * <input id="upload-file" type="file" data-gx-extension="ajax_file_upload"
 *         data-ajax_file_upload-url="http://url/to/upload-script.php" 
 *         data-ajax_file_upload-auto="false" />
 * <button id="upload-file-button">Trigger Upload</button>
 *
 * <!-- JavaScript -->
 * <script>
 *     $('#upload-file-button').on('click', function() {
 *          $('#upload-file').upload(function(response) {
 *              // Callback Function (Optional)
 *          });
 *     });
 * </script>
 * ```
 *
 * @module Admin/Extensions/ajax_file_upload
 * @requires jQuery-File-Upload
 */
gx.extensions.module(
	'ajax_file_upload',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		var
			/**
			 * Extension Reference Selector
			 *
			 * @type {object}
			 */
			$this = $(this),
			
			/**
			 * Default Options for Extension.
			 *
			 * @type {object}
			 */
			defaults = {
				auto: true
			},
			
			/**
			 * Final Extension Options
			 *
			 * @type {object}
			 */
			options = $.extend(true, {}, defaults, data),
			
			/**
			 * Module Object
			 *
			 * @type {object}
			 */
			module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONALITY
		// ------------------------------------------------------------------------
		
		/**
		 * Check method element type.
		 *
		 * The element that uses the extended jquery methods must be an input[type=file].
		 * Otherwise an exception is thrown.
		 *
		 * @param {object} $element jQuery selector for the element to be checked.
		 *
		 * @throws Exception when the element called is not a valid input[type=file].
		 *
		 * @private
		 */
		var _checkElementType = function($element) {
			if (!$element.is('input[type=file]')) {
				throw '$.upload() method is supported only in input[type=file] elements.';
			}
		};
		
		/**
		 * Uploads selected file to server.
		 *
		 * This method uses the JavaScript File API that is supported from IE10+. If
		 * you need to support older browser just enable the auto-upload option and do
		 * not use this method.
		 *
		 * @param callback
		 */
		var _upload = function(callback) {
			// Trigger "validate" event for file upload element.
			var file = $this.get(0).files[0];
			if (!_validate(file) || !$this.trigger('validate', [file])) {
				return; // Do not continue as validation checks failed.
			}
			
			// Create a new instance of the plugin and upload the selected file.
			$this.fileupload({
				url: options.url,
				dataType: 'json'
			});
			
			$this.fileupload('send', {
					files: [file]
				})
				.success(function(result, textStatus, jqXHR, file) {
					jse.core.debug.info('AJAX File Upload Success Response:', result, textStatus);
					if (typeof callback === 'function') {
						callback(result);
					}
				})
				.error(function(jqXHR, textStatus, errorThrown) {
					jse.core.debug.error('AJAX File Upload Failure Response:', jqXHR, textStatus, errorThrown);
				})
				.complete(function(result, textStatus, jqXHR) {
					$this.fileupload('destroy'); // Not necessary anymore.
				});
		};
		
		/**
		 * Default Validation Rules
		 *
		 * This method will check for invalid filenames or exceeded file size (if necessary).
		 *
		 * @param {object} file Contains the information of the file to be uploaded.
		 */
		var _validate = function(file) {
			// @todo Implement default file validation.
			try {
				// Check if a file was selected.
				if (file === undefined) {
					throw 'No file was selected for upload.';
				}
				return true;
			} catch (exception) {
				jse.core.debug.error(exception);
				return false;
			}
		};
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize function of the extension, called by the engine.
		 */
		module.init = function(done) {
			// Check if upload script URL was provided (required value).
			if (options.url === undefined || options.url === '') {
				jse.core.debug.error('Upload URL was not provided for "ajax_file_upload" extension.');
				return;
			}
			
			if (options.auto === true) {
				$this.fileupload({
					'dataType': 'json',
					'url': options.url,
					done: function(event, data) {
						$(this).trigger('upload', [data.result]);
					}
				});
			} else {
				// Extend jQuery object with upload method for element.
				$.fn.extend({
					upload: function(callback) {
						_checkElementType($(this));
						_upload(callback); // Trigger upload handler
					},
					validate: function() {
						_checkElementType($(this));
						return _validate(this.files[0]);
					}
				});
			}
			
			// Notify engine that the extension initialization is complete.
			done();
		};
		
		// Return data to module engine.
		return module;
	});
