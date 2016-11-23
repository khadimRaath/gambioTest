'use strict';

/* --------------------------------------------------------------
 additional_fields.js 2015-09-30 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Additional Fields
 *
 * This module will handle the additional fields actions on the product page.
 *
 * @module Compatibility/additional_fields
 */
gx.compatibility.module('additional_fields', [],

/**  @lends module:Compatibility/additional_fields */

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
  * Count var for adding new fields
  *
  * @type {int}
  */
	newFieldFormCount = 1,


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _delete = function _delete() {
		var id = $(this).data('additional_field_id'),
		    $message = $('<div class="add-padding-10"><p>' + jse.core.lang.translate('additional_fields_delete_confirmation', 'new_product') + '</p></div>'),
		    $addtionalField = $(this).parents('tbody:first');

		$message.dialog({
			'title': '',
			'modal': true,
			'dialogClass': 'gx-container',
			'buttons': [{
				'text': jse.core.lang.translate('close', 'buttons'),
				'class': 'btn',
				'click': function click() {
					$(this).dialog('close');
				}
			}, {
				'text': jse.core.lang.translate('delete', 'buttons'),
				'class': 'btn btn-primary',
				'click': function click() {
					if (id) {
						$this.append('<input type="hidden" ' + 'name="additional_field_delete_array[]" value="' + id + '" />');
					}

					$addtionalField.remove();
					$(this).dialog('close');
				}
			}],
			'width': 420
		});
	};

	var _add = function _add(event) {

		event.preventDefault();

		$this.find('.additional_fields').append($this.find('.new_additional_fields').html().replace(/%/g, newFieldFormCount));

		$this.find('.additional_fields input').prop('disabled', false);
		$this.find('.additional_fields textarea').prop('disabled', false);

		$this.find('.additional_fields .delete_additional_field:last').on('click', _delete);

		newFieldFormCount++;
		$(this).blur();

		return false;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		$this.find('.add_additional_field').on('click', _add);

		$this.find('.delete_additional_field').each(function () {
			$(this).on('click', _delete);
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkZGl0aW9uYWxfZmllbGRzLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJuZXdGaWVsZEZvcm1Db3VudCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9kZWxldGUiLCJpZCIsIiRtZXNzYWdlIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCIkYWRkdGlvbmFsRmllbGQiLCJwYXJlbnRzIiwiZGlhbG9nIiwiYXBwZW5kIiwicmVtb3ZlIiwiX2FkZCIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJmaW5kIiwiaHRtbCIsInJlcGxhY2UiLCJwcm9wIiwib24iLCJibHVyIiwiaW5pdCIsImRvbmUiLCJlYWNoIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxtQkFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLHFCQUFvQixDQWJyQjs7O0FBZUM7Ozs7O0FBS0FDLFlBQVcsRUFwQlo7OztBQXNCQzs7Ozs7QUFLQUMsV0FBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkosSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQUQsVUFBUyxFQWxDVjs7QUFvQ0E7QUFDQTtBQUNBOztBQUVBLEtBQUlRLFVBQVUsU0FBVkEsT0FBVSxHQUFXO0FBQ3hCLE1BQUlDLEtBQUtOLEVBQUUsSUFBRixFQUFRRixJQUFSLENBQWEscUJBQWIsQ0FBVDtBQUFBLE1BQ0NTLFdBQVdQLEVBQUUsb0NBQW9DUSxJQUFJQyxJQUFKLENBQVNDLElBQVQsQ0FBY0MsU0FBZCxDQUMvQyx1Q0FEK0MsRUFFL0MsYUFGK0MsQ0FBcEMsR0FFTSxZQUZSLENBRFo7QUFBQSxNQUlDQyxrQkFBa0JaLEVBQUUsSUFBRixFQUFRYSxPQUFSLENBQWdCLGFBQWhCLENBSm5COztBQU1BTixXQUFTTyxNQUFULENBQWdCO0FBQ2YsWUFBUyxFQURNO0FBRWYsWUFBUyxJQUZNO0FBR2Ysa0JBQWUsY0FIQTtBQUlmLGNBQVcsQ0FDVjtBQUNDLFlBQVFOLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLE9BQXhCLEVBQWlDLFNBQWpDLENBRFQ7QUFFQyxhQUFTLEtBRlY7QUFHQyxhQUFTLGlCQUFXO0FBQ25CWCxPQUFFLElBQUYsRUFBUWMsTUFBUixDQUFlLE9BQWY7QUFDQTtBQUxGLElBRFUsRUFRVjtBQUNDLFlBQVFOLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxTQUFkLENBQXdCLFFBQXhCLEVBQWtDLFNBQWxDLENBRFQ7QUFFQyxhQUFTLGlCQUZWO0FBR0MsYUFBUyxpQkFBVztBQUNuQixTQUFJTCxFQUFKLEVBQVE7QUFDUFAsWUFBTWdCLE1BQU4sQ0FBYSwwQkFDVixnREFEVSxHQUN5Q1QsRUFEekMsR0FDOEMsTUFEM0Q7QUFFQTs7QUFFRE0scUJBQWdCSSxNQUFoQjtBQUNBaEIsT0FBRSxJQUFGLEVBQVFjLE1BQVIsQ0FBZSxPQUFmO0FBQ0E7QUFYRixJQVJVLENBSkk7QUEwQmYsWUFBUztBQTFCTSxHQUFoQjtBQTRCQSxFQW5DRDs7QUFxQ0EsS0FBSUcsT0FBTyxTQUFQQSxJQUFPLENBQVNDLEtBQVQsRUFBZ0I7O0FBRTFCQSxRQUFNQyxjQUFOOztBQUVBcEIsUUFBTXFCLElBQU4sQ0FBVyxvQkFBWCxFQUFpQ0wsTUFBakMsQ0FBd0NoQixNQUFNcUIsSUFBTixDQUFXLHdCQUFYLEVBQXFDQyxJQUFyQyxHQUN0Q0MsT0FEc0MsQ0FDOUIsSUFEOEIsRUFDeEJyQixpQkFEd0IsQ0FBeEM7O0FBR0FGLFFBQU1xQixJQUFOLENBQVcsMEJBQVgsRUFBdUNHLElBQXZDLENBQTRDLFVBQTVDLEVBQXdELEtBQXhEO0FBQ0F4QixRQUFNcUIsSUFBTixDQUFXLDZCQUFYLEVBQTBDRyxJQUExQyxDQUErQyxVQUEvQyxFQUEyRCxLQUEzRDs7QUFFQXhCLFFBQU1xQixJQUFOLENBQVcsa0RBQVgsRUFBK0RJLEVBQS9ELENBQWtFLE9BQWxFLEVBQTJFbkIsT0FBM0U7O0FBRUFKO0FBQ0FELElBQUUsSUFBRixFQUFReUIsSUFBUjs7QUFFQSxTQUFPLEtBQVA7QUFDQSxFQWhCRDs7QUFrQkE7QUFDQTtBQUNBOztBQUVBNUIsUUFBTzZCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCNUIsUUFBTXFCLElBQU4sQ0FBVyx1QkFBWCxFQUFvQ0ksRUFBcEMsQ0FBdUMsT0FBdkMsRUFBZ0RQLElBQWhEOztBQUVBbEIsUUFBTXFCLElBQU4sQ0FBVywwQkFBWCxFQUF1Q1EsSUFBdkMsQ0FBNEMsWUFBVztBQUN0RDVCLEtBQUUsSUFBRixFQUFRd0IsRUFBUixDQUFXLE9BQVgsRUFBb0JuQixPQUFwQjtBQUNBLEdBRkQ7O0FBSUFzQjtBQUNBLEVBVEQ7O0FBV0EsUUFBTzlCLE1BQVA7QUFDQSxDQTlIRiIsImZpbGUiOiJhZGRpdGlvbmFsX2ZpZWxkcy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYWRkaXRpb25hbF9maWVsZHMuanMgMjAxNS0wOS0zMCBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQWRkaXRpb25hbCBGaWVsZHNcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIGhhbmRsZSB0aGUgYWRkaXRpb25hbCBmaWVsZHMgYWN0aW9ucyBvbiB0aGUgcHJvZHVjdCBwYWdlLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9hZGRpdGlvbmFsX2ZpZWxkc1xuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2FkZGl0aW9uYWxfZmllbGRzJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvYWRkaXRpb25hbF9maWVsZHMgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQ291bnQgdmFyIGZvciBhZGRpbmcgbmV3IGZpZWxkc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtpbnR9XG5cdFx0XHQgKi9cblx0XHRcdG5ld0ZpZWxkRm9ybUNvdW50ID0gMSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfZGVsZXRlID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgaWQgPSAkKHRoaXMpLmRhdGEoJ2FkZGl0aW9uYWxfZmllbGRfaWQnKSxcblx0XHRcdFx0JG1lc3NhZ2UgPSAkKCc8ZGl2IGNsYXNzPVwiYWRkLXBhZGRpbmctMTBcIj48cD4nICsganNlLmNvcmUubGFuZy50cmFuc2xhdGUoXG5cdFx0XHRcdFx0XHQnYWRkaXRpb25hbF9maWVsZHNfZGVsZXRlX2NvbmZpcm1hdGlvbicsXG5cdFx0XHRcdFx0XHQnbmV3X3Byb2R1Y3QnKSArICc8L3A+PC9kaXY+JyksXG5cdFx0XHRcdCRhZGR0aW9uYWxGaWVsZCA9ICQodGhpcykucGFyZW50cygndGJvZHk6Zmlyc3QnKTtcblx0XHRcdFxuXHRcdFx0JG1lc3NhZ2UuZGlhbG9nKHtcblx0XHRcdFx0J3RpdGxlJzogJycsXG5cdFx0XHRcdCdtb2RhbCc6IHRydWUsXG5cdFx0XHRcdCdkaWFsb2dDbGFzcyc6ICdneC1jb250YWluZXInLFxuXHRcdFx0XHQnYnV0dG9ucyc6IFtcblx0XHRcdFx0XHR7XG5cdFx0XHRcdFx0XHQndGV4dCc6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdjbG9zZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuJyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQkKHRoaXMpLmRpYWxvZygnY2xvc2UnKTtcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9LFxuXHRcdFx0XHRcdHtcblx0XHRcdFx0XHRcdCd0ZXh0JzoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2RlbGV0ZScsICdidXR0b25zJyksXG5cdFx0XHRcdFx0XHQnY2xhc3MnOiAnYnRuIGJ0bi1wcmltYXJ5Jyxcblx0XHRcdFx0XHRcdCdjbGljayc6IGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHRpZiAoaWQpIHtcblx0XHRcdFx0XHRcdFx0XHQkdGhpcy5hcHBlbmQoJzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgJ1xuXHRcdFx0XHRcdFx0XHRcdFx0KyAnbmFtZT1cImFkZGl0aW9uYWxfZmllbGRfZGVsZXRlX2FycmF5W11cIiB2YWx1ZT1cIicgKyBpZCArICdcIiAvPicpO1xuXHRcdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdFx0XHQkYWRkdGlvbmFsRmllbGQucmVtb3ZlKCk7XG5cdFx0XHRcdFx0XHRcdCQodGhpcykuZGlhbG9nKCdjbG9zZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdH1cblx0XHRcdFx0XSxcblx0XHRcdFx0J3dpZHRoJzogNDIwXG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfYWRkID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFxuXHRcdFx0JHRoaXMuZmluZCgnLmFkZGl0aW9uYWxfZmllbGRzJykuYXBwZW5kKCR0aGlzLmZpbmQoJy5uZXdfYWRkaXRpb25hbF9maWVsZHMnKS5odG1sKClcblx0XHRcdFx0LnJlcGxhY2UoLyUvZywgbmV3RmllbGRGb3JtQ291bnQpKTtcblx0XHRcdFxuXHRcdFx0JHRoaXMuZmluZCgnLmFkZGl0aW9uYWxfZmllbGRzIGlucHV0JykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XG5cdFx0XHQkdGhpcy5maW5kKCcuYWRkaXRpb25hbF9maWVsZHMgdGV4dGFyZWEnKS5wcm9wKCdkaXNhYmxlZCcsIGZhbHNlKTtcblx0XHRcdFxuXHRcdFx0JHRoaXMuZmluZCgnLmFkZGl0aW9uYWxfZmllbGRzIC5kZWxldGVfYWRkaXRpb25hbF9maWVsZDpsYXN0Jykub24oJ2NsaWNrJywgX2RlbGV0ZSk7XG5cdFx0XHRcblx0XHRcdG5ld0ZpZWxkRm9ybUNvdW50Kys7XG5cdFx0XHQkKHRoaXMpLmJsdXIoKTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdFxuXHRcdFx0JHRoaXMuZmluZCgnLmFkZF9hZGRpdGlvbmFsX2ZpZWxkJykub24oJ2NsaWNrJywgX2FkZCk7XG5cdFx0XHRcblx0XHRcdCR0aGlzLmZpbmQoJy5kZWxldGVfYWRkaXRpb25hbF9maWVsZCcpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdCQodGhpcykub24oJ2NsaWNrJywgX2RlbGV0ZSk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
