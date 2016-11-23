'use strict';

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
gx.controllers.module('image_processing', [gx.source + '/libs/info_messages'],

/**  @lends module:Controllers/image_processing */

function (data) {

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

	var _onClick = function _onClick() {
		var title = jse.core.lang.translate('image_processing_title', 'image_processing');

		$('.process-modal').dialog({
			'title': title,
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': [{
				'text': jse.core.lang.translate('close', 'buttons'),
				'class': 'btn',
				'click': function click() {
					$(this).dialog('close');
				}
			}],
			'width': 580
		});

		_processImage(1);
	};

	// ------------------------------------------------------------------------
	// AJAX
	// ------------------------------------------------------------------------

	var _processImage = function _processImage(imageNumber) {

		$.ajax({
			'type': 'POST',
			'url': 'admin.php?do=ImageProcessing/Process',
			'timeout': 30000,
			'dataType': 'json',
			'context': this,
			'data': {
				'image_number': imageNumber
			},
			success: function success(response) {
				var progress = 100 / response.payload.imagesCount * imageNumber;
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
						messages.addError(jse.core.lang.translate('image_processing_error', 'image_processing'));
					} else {
						messages.addSuccess(jse.core.lang.translate('image_processing_success', 'image_processing'));
					}

					error = false;
				}
			}
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', '.js-process', _onClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImltYWdlX3Byb2Nlc3NpbmcvaW1hZ2VfcHJvY2Vzc2luZy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiZXJyb3IiLCJvcHRpb25zIiwiZXh0ZW5kIiwibWVzc2FnZXMiLCJqc2UiLCJsaWJzIiwiaW5mb19tZXNzYWdlcyIsIl9vbkNsaWNrIiwidGl0bGUiLCJjb3JlIiwibGFuZyIsInRyYW5zbGF0ZSIsImRpYWxvZyIsIl9wcm9jZXNzSW1hZ2UiLCJpbWFnZU51bWJlciIsImFqYXgiLCJzdWNjZXNzIiwicmVzcG9uc2UiLCJwcm9ncmVzcyIsInBheWxvYWQiLCJpbWFnZXNDb3VudCIsIk1hdGgiLCJyb3VuZCIsImF0dHIiLCJjc3MiLCJodG1sIiwiZmluaXNoZWQiLCJhZGRFcnJvciIsImFkZFN1Y2Nlc3MiLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7O0FBUUFBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLGtCQURELEVBR0MsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHFCQURiLENBSEQ7O0FBT0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFNBQVEsS0FwQlQ7OztBQXNCQzs7Ozs7QUFLQUMsV0FBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkgsSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQU8sWUFBV0MsSUFBSUMsSUFBSixDQUFTQyxhQWxDckI7OztBQW9DQzs7Ozs7QUFLQVosVUFBUyxFQXpDVjs7QUEyQ0E7QUFDQTtBQUNBOztBQUVBLEtBQUlh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ZmlsZSI6ImltYWdlX3Byb2Nlc3NpbmcvaW1hZ2VfcHJvY2Vzc2luZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaW1hZ2VfcHJvY2Vzc2luZy5qcyAyMDE1LTA5LTIzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBJbWFnZSBQcm9jZXNzaW5nXG4gKlxuICogVGhpcyBtb2R1bGUgd2lsbCBleGVjdXRlIHRoZSBpbWFnZSBwcm9jZXNzaW5nIGJ5IHNlbmRpbmcgUE9TVC1SZXF1ZXN0cyB0byB0aGVcbiAqIEltYWdlUHJvY2Vzc2luZ0NvbnRyb2xsZXIgaW50ZXJmYWNlXG4gKlxuICogQG1vZHVsZSBDb250cm9sbGVycy9pbWFnZV9wcm9jZXNzaW5nXG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0J2ltYWdlX3Byb2Nlc3NpbmcnLFxuXHRcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9pbmZvX21lc3NhZ2VzJ1xuXHRdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL2ltYWdlX3Byb2Nlc3NpbmcgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGbGFnIGlmIGFuIGVycm9yIG9jY3VycmVkIGR1cmluZyB0aGUgaW1hZ2UgcHJvY2Vzc2luZ1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtib29sZWFufVxuXHRcdFx0ICovXG5cdFx0XHRlcnJvciA9IGZhbHNlLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFJlZmVyZW5jZSB0byB0aGUgaW5mbyBtZXNzYWdlcyBsaWJyYXJ5XG5cdFx0XHQgKiBcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1lc3NhZ2VzID0ganNlLmxpYnMuaW5mb19tZXNzYWdlcyxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gRVZFTlQgSEFORExFUlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX29uQ2xpY2sgPSBmdW5jdGlvbigpIHtcblx0XHRcdHZhciB0aXRsZSA9IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdpbWFnZV9wcm9jZXNzaW5nX3RpdGxlJywgJ2ltYWdlX3Byb2Nlc3NpbmcnKTtcblx0XHRcdFxuXHRcdFx0JCgnLnByb2Nlc3MtbW9kYWwnKS5kaWFsb2coe1xuXHRcdFx0XHQndGl0bGUnOiB0aXRsZSxcblx0XHRcdFx0J21vZGFsJzogdHJ1ZSxcblx0XHRcdFx0J2RpYWxvZ0NsYXNzJzogJ2d4LWNvbnRhaW5lcicsXG5cdFx0XHRcdCdidXR0b25zJzogW1xuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Nsb3NlJywgJ2J1dHRvbnMnKSxcblx0XHRcdFx0XHRcdCdjbGFzcyc6ICdidG4nLFxuXHRcdFx0XHRcdFx0J2NsaWNrJzogZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblx0XHRcdFx0XSxcblx0XHRcdFx0J3dpZHRoJzogNTgwXG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0X3Byb2Nlc3NJbWFnZSgxKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEFKQVhcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX3Byb2Nlc3NJbWFnZSA9IGZ1bmN0aW9uKGltYWdlTnVtYmVyKSB7XG5cdFx0XHRcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdCd0eXBlJzogJ1BPU1QnLFxuXHRcdFx0XHQndXJsJzogJ2FkbWluLnBocD9kbz1JbWFnZVByb2Nlc3NpbmcvUHJvY2VzcycsXG5cdFx0XHRcdCd0aW1lb3V0JzogMzAwMDAsXG5cdFx0XHRcdCdkYXRhVHlwZSc6ICdqc29uJyxcblx0XHRcdFx0J2NvbnRleHQnOiB0aGlzLFxuXHRcdFx0XHQnZGF0YSc6IHtcblx0XHRcdFx0XHQnaW1hZ2VfbnVtYmVyJzogaW1hZ2VOdW1iZXJcblx0XHRcdFx0fSxcblx0XHRcdFx0c3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHR2YXIgcHJvZ3Jlc3MgPSAoMTAwIC8gcmVzcG9uc2UucGF5bG9hZC5pbWFnZXNDb3VudCkgKiBpbWFnZU51bWJlcjtcblx0XHRcdFx0XHRwcm9ncmVzcyA9IE1hdGgucm91bmQocHJvZ3Jlc3MpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdCQoJy5wcm9jZXNzLW1vZGFsIC5wcm9ncmVzcy1iYXInKS5hdHRyKCdhcmlhLXZhbHVlbm93JywgcHJvZ3Jlc3MpO1xuXHRcdFx0XHRcdCQoJy5wcm9jZXNzLW1vZGFsIC5wcm9ncmVzcy1iYXInKS5jc3MoJ21pbi13aWR0aCcsICc3MHB4Jyk7XG5cdFx0XHRcdFx0JCgnLnByb2Nlc3MtbW9kYWwgLnByb2dyZXNzLWJhcicpLmNzcygnd2lkdGgnLCBwcm9ncmVzcyArICclJyk7XG5cdFx0XHRcdFx0JCgnLnByb2Nlc3MtbW9kYWwgLnByb2dyZXNzLWJhcicpLmh0bWwoaW1hZ2VOdW1iZXIgKyAnIC8gJyArIHJlc3BvbnNlLnBheWxvYWQuaW1hZ2VzQ291bnQpO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmICghcmVzcG9uc2Uuc3VjY2Vzcykge1xuXHRcdFx0XHRcdFx0ZXJyb3IgPSB0cnVlO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAoIXJlc3BvbnNlLnBheWxvYWQuZmluaXNoZWQpIHtcblx0XHRcdFx0XHRcdGltYWdlTnVtYmVyICs9IDE7XG5cdFx0XHRcdFx0XHRfcHJvY2Vzc0ltYWdlKGltYWdlTnVtYmVyKTtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHQkKCcucHJvY2Vzcy1tb2RhbCcpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdCQoJy5wcm9jZXNzLW1vZGFsIC5wcm9ncmVzcy1iYXInKS5hdHRyKCdhcmlhLXZhbHVlbm93JywgMCk7XG5cdFx0XHRcdFx0XHQkKCcucHJvY2Vzcy1tb2RhbCAucHJvZ3Jlc3MtYmFyJykuY3NzKCd3aWR0aCcsICcwJScpO1xuXHRcdFx0XHRcdFx0JCgnLnByb2Nlc3MtbW9kYWwgLnByb2dyZXNzLWJhcicpLmh0bWwoJycpO1xuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRpZiAoZXJyb3IpIHtcblx0XHRcdFx0XHRcdFx0bWVzc2FnZXMuYWRkRXJyb3IoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2ltYWdlX3Byb2Nlc3NpbmdfZXJyb3InLFxuXHRcdFx0XHRcdFx0XHRcdCdpbWFnZV9wcm9jZXNzaW5nJykpO1xuXHRcdFx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRcdFx0bWVzc2FnZXMuYWRkU3VjY2Vzcyhqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnaW1hZ2VfcHJvY2Vzc2luZ19zdWNjZXNzJyxcblx0XHRcdFx0XHRcdFx0XHQnaW1hZ2VfcHJvY2Vzc2luZycpKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0ZXJyb3IgPSBmYWxzZTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzLm9uKCdjbGljaycsICcuanMtcHJvY2VzcycsIF9vbkNsaWNrKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
