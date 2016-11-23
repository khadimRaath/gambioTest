'use strict';

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
gx.extensions.module('ajax_file_upload', [], function (data) {

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
	var _checkElementType = function _checkElementType($element) {
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
	var _upload = function _upload(callback) {
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
		}).success(function (result, textStatus, jqXHR, file) {
			jse.core.debug.info('AJAX File Upload Success Response:', result, textStatus);
			if (typeof callback === 'function') {
				callback(result);
			}
		}).error(function (jqXHR, textStatus, errorThrown) {
			jse.core.debug.error('AJAX File Upload Failure Response:', jqXHR, textStatus, errorThrown);
		}).complete(function (result, textStatus, jqXHR) {
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
	var _validate = function _validate(file) {
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
	module.init = function (done) {
		// Check if upload script URL was provided (required value).
		if (options.url === undefined || options.url === '') {
			jse.core.debug.error('Upload URL was not provided for "ajax_file_upload" extension.');
			return;
		}

		if (options.auto === true) {
			$this.fileupload({
				'dataType': 'json',
				'url': options.url,
				done: function done(event, data) {
					$(this).trigger('upload', [data.result]);
				}
			});
		} else {
			// Extend jQuery object with upload method for element.
			$.fn.extend({
				upload: function upload(callback) {
					_checkElementType($(this));
					_upload(callback); // Trigger upload handler
				},
				validate: function validate() {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFqYXhfZmlsZV91cGxvYWQuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiYXV0byIsIm9wdGlvbnMiLCJleHRlbmQiLCJfY2hlY2tFbGVtZW50VHlwZSIsIiRlbGVtZW50IiwiaXMiLCJfdXBsb2FkIiwiY2FsbGJhY2siLCJmaWxlIiwiZ2V0IiwiZmlsZXMiLCJfdmFsaWRhdGUiLCJ0cmlnZ2VyIiwiZmlsZXVwbG9hZCIsInVybCIsImRhdGFUeXBlIiwic3VjY2VzcyIsInJlc3VsdCIsInRleHRTdGF0dXMiLCJqcVhIUiIsImpzZSIsImNvcmUiLCJkZWJ1ZyIsImluZm8iLCJlcnJvciIsImVycm9yVGhyb3duIiwiY29tcGxldGUiLCJ1bmRlZmluZWQiLCJleGNlcHRpb24iLCJpbml0IiwiZG9uZSIsImV2ZW50IiwiZm4iLCJ1cGxvYWQiLCJ2YWxpZGF0ZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUFnR0FBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUNDLGtCQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXO0FBQ1ZDLFFBQU07QUFESSxFQWJaOzs7QUFpQkM7Ozs7O0FBS0FDLFdBQVVILEVBQUVJLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkgsUUFBbkIsRUFBNkJILElBQTdCLENBdEJYOzs7QUF3QkM7Ozs7O0FBS0FELFVBQVMsRUE3QlY7O0FBK0JBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7Ozs7Ozs7O0FB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QUNYK0IsWUFBUSxnQkFBUzFCLFFBQVQsRUFBbUI7QUFDMUJKLHVCQUFrQkwsRUFBRSxJQUFGLENBQWxCO0FBQ0FRLGFBQVFDLFFBQVIsRUFGMEIsQ0FFUDtBQUNuQixLQUpVO0FBS1gyQixjQUFVLG9CQUFXO0FBQ3BCL0IsdUJBQWtCTCxFQUFFLElBQUYsQ0FBbEI7QUFDQSxZQUFPYSxVQUFVLEtBQUtELEtBQUwsQ0FBVyxDQUFYLENBQVYsQ0FBUDtBQUNBO0FBUlUsSUFBWjtBQVVBOztBQUVEO0FBQ0FvQjtBQUNBLEVBL0JEOztBQWlDQTtBQUNBLFFBQU9uQyxNQUFQO0FBQ0EsQ0F4S0YiLCJmaWxlIjoiYWpheF9maWxlX3VwbG9hZC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYWpheF9maWxlX3VwbG9hZC5qcyAyMDE2LTAyLTExXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBBSkFYIEZpbGUgVXBsb2FkIEV4dGVuc2lvblxuICpcbiAqIFRoaXMgZXh0ZW5zaW9uIHdpbGwgZW5hYmxlIGFuIGV4aXN0aW5nICoqaW5wdXRbdHlwZT1maWxlXSoqIGVsZW1lbnQgdG8gdXBsb2FkIGZpbGVzIHRocm91Z2ggQUpBWC5cbiAqIFRoZSB1cGxvYWQgbWV0aG9kIGNhbiBiZSBpbnZva2VkIGVpdGhlciBtYW51YWxseSBieSBjYWxsaW5nIHRoZSBcInVwbG9hZFwiIGZ1bmN0aW9uIG9yIGF1dG9tYXRpY2FsbHlcbiAqIG9uY2UgdGhlIGZpbGUgaXMgc2VsZWN0ZWQuIEEgXCJ2YWxpZGF0ZVwiIGV2ZW50IGlzIHRyaWdnZXJlZCBiZWZvcmUgdXBsb2FkIHN0YXJ0cyBzbyB0aGF0IHlvdSBjYW5cbiAqIHZhbGlkYXRlIHRoZSBzZWxlY3RlZCBmaWxlIGJlZm9yZSBpdCBpcyB1cGxvYWRlZCBhbmQgc3RvcCB0aGUgcHJvY2VkdXJlIGlmIG5lZWRlZC5cbiAqXG4gKiBDdXJyZW50bHkgdGhlIG1vZHVsZSBzdXBwb3J0cyB0aGUgYmFzaWMgdXBsb2FkIGZ1bmN0aW9uYWxpdHkgYnV0IHlvdSBjYW4gYWRkIGV4dHJhIGxvZ2ljIG9uIHlvdXIgb3duXG4gKiBieSBmb2xsb3dpbmcgY29kZSBleGFtcGxlcyBpbiB0aGUgb2ZmaWNpYWwgcGFnZSBvZiB0aGUgcGx1Z2luLlxuICogXG4gKiBUaGUgXCJhdXRvXCIgb3B0aW9uIGlzIGVuYWJsZWQgYnkgZGVmYXVsdCB3aGljaCBtZWFucyB0aGF0IHRoZSBleHRlbnNpb24gd2lsbCBhdXRvbWF0aWNhbGx5IHRyaWdnZXIgXG4gKiB0aGUgdXBsb2FkIG9wZXJhdGlvbiB3aGVuIHRoZSB1c2VyIHNlbGVjdHMgYSBmaWxlLlxuICpcbiAqIHtAbGluayBodHRwczovL2dpdGh1Yi5jb20vYmx1ZWltcC9qUXVlcnktRmlsZS1VcGxvYWQvd2lraS9CYXNpYy1wbHVnaW59XG4gKlxuICogKipJbXBvcnRhbnQqKjogSWYgeW91IG5lZWQgdG8gc3VwcG9ydCBvbGRlciB2ZXJzaW9ucyBvZiBJbnRlcm5ldCBFeHBsb3JlciB5b3Ugc2hvdWxkIHVzZSB0aGUgYXV0b21hdGljIHVwbG9hZCBcbiAqIG1vZGUgYmVjYXVzZSB0aGUgbWFudWFsIG1vZGUgdXNlcyB0aGUgSmF2YVNjcmlwdCBGaWxlIEFQSSBhbmQgdGhpcyBpcyBzdXBwb3J0ZWQgZnJvbSBJRSAxMCsuXG4gKlxuICogIyMjIE9wdGlvbnNcbiAqIFxuICogKipVUkwgfCBgZGF0YS1hamF4X3VwbG9hZF9maWxlLXVybGAgfCBTdHJpbmcgfCBSZXF1aXJlZCoqXG4gKiBcbiAqIERlZmluZSB0aGUgdXBsb2FkIFVSTCB0aGF0IHdpbGwgaGFuZGxlIHRoZSBmaWxlIHVwbG9hZC5cbiAqIFxuICogKipBdXRvIHwgYGRhdGEtYWpheF91cGxvYWRfZmlsZS1hdXRvYCB8IEJvb2xlYW4gfCBPcHRpb25hbCoqXG4gKiBcbiAqIERlZmluZSB3aGV0aGVyIHRoZSB1cGxvYWQgcHJvY2VzcyB3aWxsIGJlIHN0YXJ0ZWQgYXV0b21hdGljYWxseSBhZnRlciB0aGUgdXNlciBzZWxlY3RzIGEgZmlsZS5cbiAqXG4gKiAjIyMgRXZlbnRzXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiAvLyBBZGQgeW91ciB2YWxpZGF0aW9uIHJ1bGVzLCB0cmlnZ2VyZWQgYmVmb3JlIHVwbG9hZCAoTWFudWFsIE1vZGUgLSBSZXF1aXJlcyBKUyBmaWxlIEFQSSBzdXBwb3J0KS5cbiAqICQoJyN1cGxvYWQtZmlsZScpLm9uKCd2YWxpZGF0ZScsIGZ1bmN0aW9uKGV2ZW50LCBmaWxlKSB7fSk7XG4gKiBcbiAqIC8vIFRyaWdnZXJlZCB3aGVuIHNlcnZlciByZXNwb25kcyB0byB1cGxvYWQgcmVxdWVzdCAoTWFudWFsICsgQXV0byBNb2RlKS5cbiAqICQoJyN1cGxvYWQtZmlsZScpLm9uKCd1cGxvYWQnLCBmdW5jdGlvbihldmVudCwgcmVzcG9uc2UpIHt9KTtcbiAqIGBgYFxuICogXG4gKiAjIyMgTWV0aG9kc1xuICogYGBgamF2YXNjcmlwdFxuICogLy8gVHJpZ2dlciB0aGUgc2VsZWN0ZWQgZmlsZSB2YWxpZGF0aW9uLCByZXR1cm5zIGEgYm9vbCB2YWx1ZS5cbiAqICQoJyN1cGxvYWQtZmlsZScpLnZhbGlkYXRlKCk7IFxuICogXG4gKiAvLyBUcmlnZ2VyIHRoZSBmaWxlIHVwbG9hZCwgY2FsbGJhY2sgYXJndW1lbnQgaXMgb3B0aW9uYWwuXG4gKiAkKCcjdXBsb2FkLWZpbGUnKS51cGxvYWQoY2FsbGJhY2spOyBcbiAqIGBgYFxuICpcbiAqICMjIyBFeGFtcGxlXG4gKiBcbiAqICoqQXV0b21hdGljIFVwbG9hZCoqXG4gKiBcbiAqIFRoZSB1cGxvYWQgcHJvY2VzcyB3aWxsIGJlIHRyaWdnZXJlZCBhdXRvbWF0aWNhbGx5IGFmdGVyIHRoZSB1c2VyIHNlbGVjdHMgYSBmaWxlLlxuICogXG4gKiBgYGBodG1sXG4gKiA8IS0tIEhUTUwgLS0+XG4gKiA8aW5wdXQgaWQ9XCJ1cGxvYWQtZmlsZVwiIHR5cGU9XCJmaWxlXCIgZGF0YS1neC1leHRlbnNpb249XCJhamF4X2ZpbGVfdXBsb2FkXCJcbiAqICAgICAgICAgICAgIGRhdGEtYWpheF9maWxlX3VwbG9hZC11cmw9XCJodHRwOi8vdXJsL3RvL3VwbG9hZC1zY3JpcHQucGhwXCIgLz5cbiAqXG4gKiA8IS0tIEphdmFTY3JpcHQgLS0+XG4gKiA8c2NyaXB0PlxuICogICAgICQoJyN1cGxvYWQtZmlsZScpLm9uKCd2YWxpZGF0ZScsIGZ1bmN0aW9uKGV2ZW50LCBmaWxlKSB7XG4gKiAgICAgICAgICAvLyBWYWxpZGF0aW9uIENoZWNrcyAoT25seSBJRSAxMCspIC4uLlxuICogICAgICAgICAgcmV0dXJuIHRydWU7IC8vIFJldHVybiB0cnVlIGZvciBzdWNjZXNzIG9yIGZhbHNlIGZvciBmYWlsdXJlIC0gd2lsbCBzdG9wIHRoZSB1cGxvYWQuXG4gKiAgICAgfSk7XG4gKlxuICogICAgICQoJyN1cGxvYWQtZmlsZScpLm9uKCd1cGxvYWQnLCBmdW5jdGlvbihldmVudCwgcmVzcG9uc2UpIHtcbiAqICAgICAgICAgIC8vIFRoZSBcInJlc3BvbnNlXCIgcGFyYW1ldGVyIGNvbnRhaW5zIHRoZSBzZXJ2ZXIncyByZXNwb25zZSBpbmZvcm1hdGlvbi5cbiAqICAgICB9KTtcbiAqIDwvc2NyaXB0PlxuICogYGBgXG4gKiBcbiAqICoqTWFudWFsIFVwbG9hZCoqXG4gKiBcbiAqIFRoZSB1cGxvYWQgcHJvY2VzcyBuZWVkcyB0byBiZSB0cmlnZ2VyZWQgdGhyb3VnaCBKYXZhU2NyaXB0IGFzIHNob3duIGluIHRoZSBmb2xsb3dpbmcgZXhhbXBsZS5cbiAqIFxuICogXG4gKiBgYGBodG1sXG4gKiA8IS0tIEhUTUwgLS0+XG4gKiA8aW5wdXQgaWQ9XCJ1cGxvYWQtZmlsZVwiIHR5cGU9XCJmaWxlXCIgZGF0YS1neC1leHRlbnNpb249XCJhamF4X2ZpbGVfdXBsb2FkXCJcbiAqICAgICAgICAgZGF0YS1hamF4X2ZpbGVfdXBsb2FkLXVybD1cImh0dHA6Ly91cmwvdG8vdXBsb2FkLXNjcmlwdC5waHBcIiBcbiAqICAgICAgICAgZGF0YS1hamF4X2ZpbGVfdXBsb2FkLWF1dG89XCJmYWxzZVwiIC8+XG4gKiA8YnV0dG9uIGlkPVwidXBsb2FkLWZpbGUtYnV0dG9uXCI+VHJpZ2dlciBVcGxvYWQ8L2J1dHRvbj5cbiAqXG4gKiA8IS0tIEphdmFTY3JpcHQgLS0+XG4gKiA8c2NyaXB0PlxuICogICAgICQoJyN1cGxvYWQtZmlsZS1idXR0b24nKS5vbignY2xpY2snLCBmdW5jdGlvbigpIHtcbiAqICAgICAgICAgICQoJyN1cGxvYWQtZmlsZScpLnVwbG9hZChmdW5jdGlvbihyZXNwb25zZSkge1xuICogICAgICAgICAgICAgIC8vIENhbGxiYWNrIEZ1bmN0aW9uIChPcHRpb25hbClcbiAqICAgICAgICAgIH0pO1xuICogICAgIH0pO1xuICogPC9zY3JpcHQ+XG4gKiBgYGBcbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvYWpheF9maWxlX3VwbG9hZFxuICogQHJlcXVpcmVzIGpRdWVyeS1GaWxlLVVwbG9hZFxuICovXG5neC5leHRlbnNpb25zLm1vZHVsZShcblx0J2FqYXhfZmlsZV91cGxvYWQnLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogRXh0ZW5zaW9uIFJlZmVyZW5jZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnMgZm9yIEV4dGVuc2lvbi5cblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0YXV0bzogdHJ1ZVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTkFMSVRZXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogQ2hlY2sgbWV0aG9kIGVsZW1lbnQgdHlwZS5cblx0XHQgKlxuXHRcdCAqIFRoZSBlbGVtZW50IHRoYXQgdXNlcyB0aGUgZXh0ZW5kZWQganF1ZXJ5IG1ldGhvZHMgbXVzdCBiZSBhbiBpbnB1dFt0eXBlPWZpbGVdLlxuXHRcdCAqIE90aGVyd2lzZSBhbiBleGNlcHRpb24gaXMgdGhyb3duLlxuXHRcdCAqXG5cdFx0ICogQHBhcmFtIHtvYmplY3R9ICRlbGVtZW50IGpRdWVyeSBzZWxlY3RvciBmb3IgdGhlIGVsZW1lbnQgdG8gYmUgY2hlY2tlZC5cblx0XHQgKlxuXHRcdCAqIEB0aHJvd3MgRXhjZXB0aW9uIHdoZW4gdGhlIGVsZW1lbnQgY2FsbGVkIGlzIG5vdCBhIHZhbGlkIGlucHV0W3R5cGU9ZmlsZV0uXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfY2hlY2tFbGVtZW50VHlwZSA9IGZ1bmN0aW9uKCRlbGVtZW50KSB7XG5cdFx0XHRpZiAoISRlbGVtZW50LmlzKCdpbnB1dFt0eXBlPWZpbGVdJykpIHtcblx0XHRcdFx0dGhyb3cgJyQudXBsb2FkKCkgbWV0aG9kIGlzIHN1cHBvcnRlZCBvbmx5IGluIGlucHV0W3R5cGU9ZmlsZV0gZWxlbWVudHMuJztcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFVwbG9hZHMgc2VsZWN0ZWQgZmlsZSB0byBzZXJ2ZXIuXG5cdFx0ICpcblx0XHQgKiBUaGlzIG1ldGhvZCB1c2VzIHRoZSBKYXZhU2NyaXB0IEZpbGUgQVBJIHRoYXQgaXMgc3VwcG9ydGVkIGZyb20gSUUxMCsuIElmXG5cdFx0ICogeW91IG5lZWQgdG8gc3VwcG9ydCBvbGRlciBicm93c2VyIGp1c3QgZW5hYmxlIHRoZSBhdXRvLXVwbG9hZCBvcHRpb24gYW5kIGRvXG5cdFx0ICogbm90IHVzZSB0aGlzIG1ldGhvZC5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSBjYWxsYmFja1xuXHRcdCAqL1xuXHRcdHZhciBfdXBsb2FkID0gZnVuY3Rpb24oY2FsbGJhY2spIHtcblx0XHRcdC8vIFRyaWdnZXIgXCJ2YWxpZGF0ZVwiIGV2ZW50IGZvciBmaWxlIHVwbG9hZCBlbGVtZW50LlxuXHRcdFx0dmFyIGZpbGUgPSAkdGhpcy5nZXQoMCkuZmlsZXNbMF07XG5cdFx0XHRpZiAoIV92YWxpZGF0ZShmaWxlKSB8fCAhJHRoaXMudHJpZ2dlcigndmFsaWRhdGUnLCBbZmlsZV0pKSB7XG5cdFx0XHRcdHJldHVybjsgLy8gRG8gbm90IGNvbnRpbnVlIGFzIHZhbGlkYXRpb24gY2hlY2tzIGZhaWxlZC5cblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gQ3JlYXRlIGEgbmV3IGluc3RhbmNlIG9mIHRoZSBwbHVnaW4gYW5kIHVwbG9hZCB0aGUgc2VsZWN0ZWQgZmlsZS5cblx0XHRcdCR0aGlzLmZpbGV1cGxvYWQoe1xuXHRcdFx0XHR1cmw6IG9wdGlvbnMudXJsLFxuXHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0JHRoaXMuZmlsZXVwbG9hZCgnc2VuZCcsIHtcblx0XHRcdFx0XHRmaWxlczogW2ZpbGVdXG5cdFx0XHRcdH0pXG5cdFx0XHRcdC5zdWNjZXNzKGZ1bmN0aW9uKHJlc3VsdCwgdGV4dFN0YXR1cywganFYSFIsIGZpbGUpIHtcblx0XHRcdFx0XHRqc2UuY29yZS5kZWJ1Zy5pbmZvKCdBSkFYIEZpbGUgVXBsb2FkIFN1Y2Nlc3MgUmVzcG9uc2U6JywgcmVzdWx0LCB0ZXh0U3RhdHVzKTtcblx0XHRcdFx0XHRpZiAodHlwZW9mIGNhbGxiYWNrID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRcdFx0XHRjYWxsYmFjayhyZXN1bHQpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSlcblx0XHRcdFx0LmVycm9yKGZ1bmN0aW9uKGpxWEhSLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93bikge1xuXHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdBSkFYIEZpbGUgVXBsb2FkIEZhaWx1cmUgUmVzcG9uc2U6JywganFYSFIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKTtcblx0XHRcdFx0fSlcblx0XHRcdFx0LmNvbXBsZXRlKGZ1bmN0aW9uKHJlc3VsdCwgdGV4dFN0YXR1cywganFYSFIpIHtcblx0XHRcdFx0XHQkdGhpcy5maWxldXBsb2FkKCdkZXN0cm95Jyk7IC8vIE5vdCBuZWNlc3NhcnkgYW55bW9yZS5cblx0XHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBEZWZhdWx0IFZhbGlkYXRpb24gUnVsZXNcblx0XHQgKlxuXHRcdCAqIFRoaXMgbWV0aG9kIHdpbGwgY2hlY2sgZm9yIGludmFsaWQgZmlsZW5hbWVzIG9yIGV4Y2VlZGVkIGZpbGUgc2l6ZSAoaWYgbmVjZXNzYXJ5KS5cblx0XHQgKlxuXHRcdCAqIEBwYXJhbSB7b2JqZWN0fSBmaWxlIENvbnRhaW5zIHRoZSBpbmZvcm1hdGlvbiBvZiB0aGUgZmlsZSB0byBiZSB1cGxvYWRlZC5cblx0XHQgKi9cblx0XHR2YXIgX3ZhbGlkYXRlID0gZnVuY3Rpb24oZmlsZSkge1xuXHRcdFx0Ly8gQHRvZG8gSW1wbGVtZW50IGRlZmF1bHQgZmlsZSB2YWxpZGF0aW9uLlxuXHRcdFx0dHJ5IHtcblx0XHRcdFx0Ly8gQ2hlY2sgaWYgYSBmaWxlIHdhcyBzZWxlY3RlZC5cblx0XHRcdFx0aWYgKGZpbGUgPT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHRcdHRocm93ICdObyBmaWxlIHdhcyBzZWxlY3RlZCBmb3IgdXBsb2FkLic7XG5cdFx0XHRcdH1cblx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHR9IGNhdGNoIChleGNlcHRpb24pIHtcblx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoZXhjZXB0aW9uKTtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIGZ1bmN0aW9uIG9mIHRoZSBleHRlbnNpb24sIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gQ2hlY2sgaWYgdXBsb2FkIHNjcmlwdCBVUkwgd2FzIHByb3ZpZGVkIChyZXF1aXJlZCB2YWx1ZSkuXG5cdFx0XHRpZiAob3B0aW9ucy51cmwgPT09IHVuZGVmaW5lZCB8fCBvcHRpb25zLnVybCA9PT0gJycpIHtcblx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ1VwbG9hZCBVUkwgd2FzIG5vdCBwcm92aWRlZCBmb3IgXCJhamF4X2ZpbGVfdXBsb2FkXCIgZXh0ZW5zaW9uLicpO1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGlmIChvcHRpb25zLmF1dG8gPT09IHRydWUpIHtcblx0XHRcdFx0JHRoaXMuZmlsZXVwbG9hZCh7XG5cdFx0XHRcdFx0J2RhdGFUeXBlJzogJ2pzb24nLFxuXHRcdFx0XHRcdCd1cmwnOiBvcHRpb25zLnVybCxcblx0XHRcdFx0XHRkb25lOiBmdW5jdGlvbihldmVudCwgZGF0YSkge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS50cmlnZ2VyKCd1cGxvYWQnLCBbZGF0YS5yZXN1bHRdKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0Ly8gRXh0ZW5kIGpRdWVyeSBvYmplY3Qgd2l0aCB1cGxvYWQgbWV0aG9kIGZvciBlbGVtZW50LlxuXHRcdFx0XHQkLmZuLmV4dGVuZCh7XG5cdFx0XHRcdFx0dXBsb2FkOiBmdW5jdGlvbihjYWxsYmFjaykge1xuXHRcdFx0XHRcdFx0X2NoZWNrRWxlbWVudFR5cGUoJCh0aGlzKSk7XG5cdFx0XHRcdFx0XHRfdXBsb2FkKGNhbGxiYWNrKTsgLy8gVHJpZ2dlciB1cGxvYWQgaGFuZGxlclxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0dmFsaWRhdGU6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0X2NoZWNrRWxlbWVudFR5cGUoJCh0aGlzKSk7XG5cdFx0XHRcdFx0XHRyZXR1cm4gX3ZhbGlkYXRlKHRoaXMuZmlsZXNbMF0pO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIE5vdGlmeSBlbmdpbmUgdGhhdCB0aGUgZXh0ZW5zaW9uIGluaXRpYWxpemF0aW9uIGlzIGNvbXBsZXRlLlxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gbW9kdWxlIGVuZ2luZS5cblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
