/* --------------------------------------------------------------
 image_change.js 2016-01-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Products Image Change Module
 *
 * This module is responsible for effects on image changes.
 *
 * @module Compatibility/image_change
 */
gx.compatibility.module(
	// Module name
	'image_change',

	// Module dependencies
	['image_resizer', 'xhr'],

	/** @lends module:Compatibility/image_change */

	function (data) {

		'use strict';

		// ------------------------------------------------------------------------
		// VARIABLES DEFINITION
		// ------------------------------------------------------------------------

		// Shortcut to module element.
		var $this = $(this);

		// Shortcut to image resizer libary.
		var resize = jse.libs.image_resizer.resize;

		// AJAX request library
		var xhr =  jse.libs.xhr;

		// Elements selector object.
		var selectors = {
			input: 'input:file',
			form: 'form',
			previewImage: '[data-image]',
			filenameLabel: '[data-filename-label]',
			filenameInput: '[data-filename-input]',
			showImage: '[data-show-image]',
			fileInputName: '[data-file-input-name]',
			originalImageName: '[data-original-image]'
		};

		// Module object.
		var module = {};

		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------

		/**
		 * Handles file changes in input field.
		 * @param  {jQuery.Event} event Event fired
		 */
		var _onNewImage = function (event) {
			// Preview image.
			var $previewImage = $this.find(selectors.previewImage);

			// File put.
			var file = event.target.files[0];
			var fileName = file.name;

			// Make sure that the filename is unique.
			var length = $('input[name="image_file[' + fileName + ']"]').length,
				counter = 1;
			while(length !== 0) {
				var newFileName = fileName.replace(/(\.)/, String(counter) + '.');

				length = $('input[name="image_file[' + newFileName + ']"]').length;

				if (length === 0) {
					fileName = newFileName;
				}

				counter++;
			}

			xhr.get({url: 'admin.php?do=MaxFileSize'})
				.done(function (result) {
					var maxFileSizeAllowed = result.maxFileSize;
					var actualFileSize = file.size / Math.pow(1024, 2);

					if (actualFileSize > maxFileSizeAllowed) {
						var message = jse.core.lang.translate('TXT_FILE_TOO_LARGE', 'categories');
						alert(message + maxFileSizeAllowed + ' MB');
						return;
					}
					// Create a FileReader to read the input file.
					var Reader = new FileReader();

					// As soon as the image file has been loaded,
					// the loaded image file will be put into the
					// preview image tag and finally resized and displayed.
					Reader.onload = function (event) {
						// Put loaded image file into preview image tag and resize it.
						$previewImage.attr('src', event.target.result);
						resize($previewImage);
					};

					// Load image and trigger the FileReaders' `onload` event.
					Reader.readAsDataURL(file);

					// Change text in file name label and input field.
					$this.find(selectors.filenameLabel).text(fileName);
					$this.find(selectors.filenameInput).val(fileName);
					$this.find(selectors.showImage).val(fileName);
					if (!$this.find(selectors.originalImageName).val()) {
						$this.find(selectors.originalImageName).val(fileName);
						$this.find(selectors.showImage).val(fileName);
					}
					_updateFileInputName();
				});
			};

		var _updateFileInputName = function (event) {
			$this.find(selectors.fileInputName)
				.attr('name', 'image_file[' + $this.find(selectors.filenameInput).val() + ']');
		};

		module.init = function (done) {
			// Handle file change.
			$this
				.find(selectors.input)
				.on('change', _onNewImage);

			// Update name attribute of the file input
			$this
				.find(selectors.filenameInput)
				.on('change', _updateFileInputName);

			// Register as finished
			done();
		};

		return module;
	});
