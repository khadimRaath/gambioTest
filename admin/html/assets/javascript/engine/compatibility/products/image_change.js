'use strict';

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
	var xhr = jse.libs.xhr;

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
	var _onNewImage = function _onNewImage(event) {
		// Preview image.
		var $previewImage = $this.find(selectors.previewImage);

		// File put.
		var file = event.target.files[0];
		var fileName = file.name;

		// Make sure that the filename is unique.
		var length = $('input[name="image_file[' + fileName + ']"]').length,
		    counter = 1;
		while (length !== 0) {
			var newFileName = fileName.replace(/(\.)/, String(counter) + '.');

			length = $('input[name="image_file[' + newFileName + ']"]').length;

			if (length === 0) {
				fileName = newFileName;
			}

			counter++;
		}

		xhr.get({ url: 'admin.php?do=MaxFileSize' }).done(function (result) {
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

	var _updateFileInputName = function _updateFileInputName(event) {
		$this.find(selectors.fileInputName).attr('name', 'image_file[' + $this.find(selectors.filenameInput).val() + ']');
	};

	module.init = function (done) {
		// Handle file change.
		$this.find(selectors.input).on('change', _onNewImage);

		// Update name attribute of the file input
		$this.find(selectors.filenameInput).on('change', _updateFileInputName);

		// Register as finished
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3RzL2ltYWdlX2NoYW5nZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwicmVzaXplIiwianNlIiwibGlicyIsImltYWdlX3Jlc2l6ZXIiLCJ4aHIiLCJzZWxlY3RvcnMiLCJpbnB1dCIsImZvcm0iLCJwcmV2aWV3SW1hZ2UiLCJmaWxlbmFtZUxhYmVsIiwiZmlsZW5hbWVJbnB1dCIsInNob3dJbWFnZSIsImZpbGVJbnB1dE5hbWUiLCJvcmlnaW5hbEltYWdlTmFtZSIsIl9vbk5ld0ltYWdlIiwiZXZlbnQiLCIkcHJldmlld0ltYWdlIiwiZmluZCIsImZpbGUiLCJ0YXJnZXQiLCJmaWxlcyIsImZpbGVOYW1lIiwibmFtZSIsImxlbmd0aCIsImNvdW50ZXIiLCJuZXdGaWxlTmFtZSIsInJlcGxhY2UiLCJTdHJpbmciLCJnZXQiLCJ1cmwiLCJkb25lIiwicmVzdWx0IiwibWF4RmlsZVNpemVBbGxvd2VkIiwibWF4RmlsZVNpemUiLCJhY3R1YWxGaWxlU2l6ZSIsInNpemUiLCJNYXRoIiwicG93IiwibWVzc2FnZSIsImNvcmUiLCJsYW5nIiwidHJhbnNsYXRlIiwiYWxlcnQiLCJSZWFkZXIiLCJGaWxlUmVhZGVyIiwib25sb2FkIiwiYXR0ciIsInJlYWRBc0RhdGFVUkwiLCJ0ZXh0IiwidmFsIiwiX3VwZGF0ZUZpbGVJbnB1dE5hbWUiLCJpbml0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQjtBQUNDO0FBQ0EsY0FGRDs7QUFJQztBQUNBLENBQUMsZUFBRCxFQUFrQixLQUFsQixDQUxEOztBQU9DOztBQUVBLFVBQVVDLElBQVYsRUFBZ0I7O0FBRWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOztBQUNBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaOztBQUVBO0FBQ0EsS0FBSUMsU0FBU0MsSUFB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O0FBQ0Q2QjtBQUNBLEdBbENGO0FBbUNDLEVBMURGOztBQTREQSxLQUFJQSx1QkFBdUIsU0FBdkJBLG9CQUF1QixDQUFVbkMsS0FBVixFQUFpQjtBQUMzQ2pCLFFBQU1tQixJQUFOLENBQVdaLFVBQVVPLGFBQXJCLEVBQ0VrQyxJQURGLENBQ08sTUFEUCxFQUNlLGdCQUFnQmhELE1BQU1tQixJQUFOLENBQVdaLFVBQVVLLGFBQXJCLEVBQW9DdUMsR0FBcEMsRUFBaEIsR0FBNEQsR0FEM0U7QUFFQSxFQUhEOztBQUtBckQsUUFBT3VELElBQVAsR0FBYyxVQUFVckIsSUFBVixFQUFnQjtBQUM3QjtBQUNBaEMsUUFDRW1CLElBREYsQ0FDT1osVUFBVUMsS0FEakIsRUFFRThDLEVBRkYsQ0FFSyxRQUZMLEVBRWV0QyxXQUZmOztBQUlBO0FBQ0FoQixRQUNFbUIsSUFERixDQUNPWixVQUFVSyxhQURqQixFQUVFMEMsRUFGRixDQUVLLFFBRkwsRUFFZUYsb0JBRmY7O0FBSUE7QUFDQXBCO0FBQ0EsRUFiRDs7QUFlQSxRQUFPbEMsTUFBUDtBQUNBLENBbElGIiwiZmlsZSI6InByb2R1Y3RzL2ltYWdlX2NoYW5nZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaW1hZ2VfY2hhbmdlLmpzIDIwMTYtMDEtMjlcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIFByb2R1Y3RzIEltYWdlIENoYW5nZSBNb2R1bGVcbiAqXG4gKiBUaGlzIG1vZHVsZSBpcyByZXNwb25zaWJsZSBmb3IgZWZmZWN0cyBvbiBpbWFnZSBjaGFuZ2VzLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9pbWFnZV9jaGFuZ2VcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdC8vIE1vZHVsZSBuYW1lXG5cdCdpbWFnZV9jaGFuZ2UnLFxuXG5cdC8vIE1vZHVsZSBkZXBlbmRlbmNpZXNcblx0WydpbWFnZV9yZXNpemVyJywgJ3hociddLFxuXG5cdC8qKiBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvaW1hZ2VfY2hhbmdlICovXG5cblx0ZnVuY3Rpb24gKGRhdGEpIHtcblxuXHRcdCd1c2Ugc3RyaWN0JztcblxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvLyBTaG9ydGN1dCB0byBtb2R1bGUgZWxlbWVudC5cblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpO1xuXG5cdFx0Ly8gU2hvcnRjdXQgdG8gaW1hZ2UgcmVzaXplciBsaWJhcnkuXG5cdFx0dmFyIHJlc2l6ZSA9IGpzZS5saWJzLmltYWdlX3Jlc2l6ZXIucmVzaXplO1xuXG5cdFx0Ly8gQUpBWCByZXF1ZXN0IGxpYnJhcnlcblx0XHR2YXIgeGhyID0gIGpzZS5saWJzLnhocjtcblxuXHRcdC8vIEVsZW1lbnRzIHNlbGVjdG9yIG9iamVjdC5cblx0XHR2YXIgc2VsZWN0b3JzID0ge1xuXHRcdFx0aW5wdXQ6ICdpbnB1dDpmaWxlJyxcblx0XHRcdGZvcm06ICdmb3JtJyxcblx0XHRcdHByZXZpZXdJbWFnZTogJ1tkYXRhLWltYWdlXScsXG5cdFx0XHRmaWxlbmFtZUxhYmVsOiAnW2RhdGEtZmlsZW5hbWUtbGFiZWxdJyxcblx0XHRcdGZpbGVuYW1lSW5wdXQ6ICdbZGF0YS1maWxlbmFtZS1pbnB1dF0nLFxuXHRcdFx0c2hvd0ltYWdlOiAnW2RhdGEtc2hvdy1pbWFnZV0nLFxuXHRcdFx0ZmlsZUlucHV0TmFtZTogJ1tkYXRhLWZpbGUtaW5wdXQtbmFtZV0nLFxuXHRcdFx0b3JpZ2luYWxJbWFnZU5hbWU6ICdbZGF0YS1vcmlnaW5hbC1pbWFnZV0nXG5cdFx0fTtcblxuXHRcdC8vIE1vZHVsZSBvYmplY3QuXG5cdFx0dmFyIG1vZHVsZSA9IHt9O1xuXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cblx0XHQvKipcblx0XHQgKiBIYW5kbGVzIGZpbGUgY2hhbmdlcyBpbiBpbnB1dCBmaWVsZC5cblx0XHQgKiBAcGFyYW0gIHtqUXVlcnkuRXZlbnR9IGV2ZW50IEV2ZW50IGZpcmVkXG5cdFx0ICovXG5cdFx0dmFyIF9vbk5ld0ltYWdlID0gZnVuY3Rpb24gKGV2ZW50KSB7XG5cdFx0XHQvLyBQcmV2aWV3IGltYWdlLlxuXHRcdFx0dmFyICRwcmV2aWV3SW1hZ2UgPSAkdGhpcy5maW5kKHNlbGVjdG9ycy5wcmV2aWV3SW1hZ2UpO1xuXG5cdFx0XHQvLyBGaWxlIHB1dC5cblx0XHRcdHZhciBmaWxlID0gZXZlbnQudGFyZ2V0LmZpbGVzWzBdO1xuXHRcdFx0dmFyIGZpbGVOYW1lID0gZmlsZS5uYW1lO1xuXG5cdFx0XHQvLyBNYWtlIHN1cmUgdGhhdCB0aGUgZmlsZW5hbWUgaXMgdW5pcXVlLlxuXHRcdFx0dmFyIGxlbmd0aCA9ICQoJ2lucHV0W25hbWU9XCJpbWFnZV9maWxlWycgKyBmaWxlTmFtZSArICddXCJdJykubGVuZ3RoLFxuXHRcdFx0XHRjb3VudGVyID0gMTtcblx0XHRcdHdoaWxlKGxlbmd0aCAhPT0gMCkge1xuXHRcdFx0XHR2YXIgbmV3RmlsZU5hbWUgPSBmaWxlTmFtZS5yZXBsYWNlKC8oXFwuKS8sIFN0cmluZyhjb3VudGVyKSArICcuJyk7XG5cblx0XHRcdFx0bGVuZ3RoID0gJCgnaW5wdXRbbmFtZT1cImltYWdlX2ZpbGVbJyArIG5ld0ZpbGVOYW1lICsgJ11cIl0nKS5sZW5ndGg7XG5cblx0XHRcdFx0aWYgKGxlbmd0aCA9PT0gMCkge1xuXHRcdFx0XHRcdGZpbGVOYW1lID0gbmV3RmlsZU5hbWU7XG5cdFx0XHRcdH1cblxuXHRcdFx0XHRjb3VudGVyKys7XG5cdFx0XHR9XG5cblx0XHRcdHhoci5nZXQoe3VybDogJ2FkbWluLnBocD9kbz1NYXhGaWxlU2l6ZSd9KVxuXHRcdFx0XHQuZG9uZShmdW5jdGlvbiAocmVzdWx0KSB7XG5cdFx0XHRcdFx0dmFyIG1heEZpbGVTaXplQWxsb3dlZCA9IHJlc3VsdC5tYXhGaWxlU2l6ZTtcblx0XHRcdFx0XHR2YXIgYWN0dWFsRmlsZVNpemUgPSBmaWxlLnNpemUgLyBNYXRoLnBvdygxMDI0LCAyKTtcblxuXHRcdFx0XHRcdGlmIChhY3R1YWxGaWxlU2l6ZSA+IG1heEZpbGVTaXplQWxsb3dlZCkge1xuXHRcdFx0XHRcdFx0dmFyIG1lc3NhZ2UgPSBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnVFhUX0ZJTEVfVE9PX0xBUkdFJywgJ2NhdGVnb3JpZXMnKTtcblx0XHRcdFx0XHRcdGFsZXJ0KG1lc3NhZ2UgKyBtYXhGaWxlU2l6ZUFsbG93ZWQgKyAnIE1CJyk7XG5cdFx0XHRcdFx0XHRyZXR1cm47XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdC8vIENyZWF0ZSBhIEZpbGVSZWFkZXIgdG8gcmVhZCB0aGUgaW5wdXQgZmlsZS5cblx0XHRcdFx0XHR2YXIgUmVhZGVyID0gbmV3IEZpbGVSZWFkZXIoKTtcblxuXHRcdFx0XHRcdC8vIEFzIHNvb24gYXMgdGhlIGltYWdlIGZpbGUgaGFzIGJlZW4gbG9hZGVkLFxuXHRcdFx0XHRcdC8vIHRoZSBsb2FkZWQgaW1hZ2UgZmlsZSB3aWxsIGJlIHB1dCBpbnRvIHRoZVxuXHRcdFx0XHRcdC8vIHByZXZpZXcgaW1hZ2UgdGFnIGFuZCBmaW5hbGx5IHJlc2l6ZWQgYW5kIGRpc3BsYXllZC5cblx0XHRcdFx0XHRSZWFkZXIub25sb2FkID0gZnVuY3Rpb24gKGV2ZW50KSB7XG5cdFx0XHRcdFx0XHQvLyBQdXQgbG9hZGVkIGltYWdlIGZpbGUgaW50byBwcmV2aWV3IGltYWdlIHRhZyBhbmQgcmVzaXplIGl0LlxuXHRcdFx0XHRcdFx0JHByZXZpZXdJbWFnZS5hdHRyKCdzcmMnLCBldmVudC50YXJnZXQucmVzdWx0KTtcblx0XHRcdFx0XHRcdHJlc2l6ZSgkcHJldmlld0ltYWdlKTtcblx0XHRcdFx0XHR9O1xuXG5cdFx0XHRcdFx0Ly8gTG9hZCBpbWFnZSBhbmQgdHJpZ2dlciB0aGUgRmlsZVJlYWRlcnMnIGBvbmxvYWRgIGV2ZW50LlxuXHRcdFx0XHRcdFJlYWRlci5yZWFkQXNEYXRhVVJMKGZpbGUpO1xuXG5cdFx0XHRcdFx0Ly8gQ2hhbmdlIHRleHQgaW4gZmlsZSBuYW1lIGxhYmVsIGFuZCBpbnB1dCBmaWVsZC5cblx0XHRcdFx0XHQkdGhpcy5maW5kKHNlbGVjdG9ycy5maWxlbmFtZUxhYmVsKS50ZXh0KGZpbGVOYW1lKTtcblx0XHRcdFx0XHQkdGhpcy5maW5kKHNlbGVjdG9ycy5maWxlbmFtZUlucHV0KS52YWwoZmlsZU5hbWUpO1xuXHRcdFx0XHRcdCR0aGlzLmZpbmQoc2VsZWN0b3JzLnNob3dJbWFnZSkudmFsKGZpbGVOYW1lKTtcblx0XHRcdFx0XHRpZiAoISR0aGlzLmZpbmQoc2VsZWN0b3JzLm9yaWdpbmFsSW1hZ2VOYW1lKS52YWwoKSkge1xuXHRcdFx0XHRcdFx0JHRoaXMuZmluZChzZWxlY3RvcnMub3JpZ2luYWxJbWFnZU5hbWUpLnZhbChmaWxlTmFtZSk7XG5cdFx0XHRcdFx0XHQkdGhpcy5maW5kKHNlbGVjdG9ycy5zaG93SW1hZ2UpLnZhbChmaWxlTmFtZSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdF91cGRhdGVGaWxlSW5wdXROYW1lKCk7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fTtcblxuXHRcdHZhciBfdXBkYXRlRmlsZUlucHV0TmFtZSA9IGZ1bmN0aW9uIChldmVudCkge1xuXHRcdFx0JHRoaXMuZmluZChzZWxlY3RvcnMuZmlsZUlucHV0TmFtZSlcblx0XHRcdFx0LmF0dHIoJ25hbWUnLCAnaW1hZ2VfZmlsZVsnICsgJHRoaXMuZmluZChzZWxlY3RvcnMuZmlsZW5hbWVJbnB1dCkudmFsKCkgKyAnXScpO1xuXHRcdH07XG5cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uIChkb25lKSB7XG5cdFx0XHQvLyBIYW5kbGUgZmlsZSBjaGFuZ2UuXG5cdFx0XHQkdGhpc1xuXHRcdFx0XHQuZmluZChzZWxlY3RvcnMuaW5wdXQpXG5cdFx0XHRcdC5vbignY2hhbmdlJywgX29uTmV3SW1hZ2UpO1xuXG5cdFx0XHQvLyBVcGRhdGUgbmFtZSBhdHRyaWJ1dGUgb2YgdGhlIGZpbGUgaW5wdXRcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5maW5kKHNlbGVjdG9ycy5maWxlbmFtZUlucHV0KVxuXHRcdFx0XHQub24oJ2NoYW5nZScsIF91cGRhdGVGaWxlSW5wdXROYW1lKTtcblxuXHRcdFx0Ly8gUmVnaXN0ZXIgYXMgZmluaXNoZWRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
