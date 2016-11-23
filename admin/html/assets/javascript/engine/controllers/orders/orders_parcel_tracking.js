'use strict';

/* --------------------------------------------------------------
 orders_parcel_tracking.js 2015-08-27
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order Tracking Codes Controller
 *
 * @module Controllers/orders_parcel_tracking
 */
gx.controllers.module('orders_parcel_tracking', ['fallback'],

/** @lends module:Controllers/orders_parcel_tracking */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
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

	var _addTrackingCode = function _addTrackingCode(event) {

		event.stopPropagation();

		var data_set = jse.libs.fallback._data($(this), 'orders_parcel_tracking');
		var tracking_code = $('#parcel_service_tracking_code').val();
		if (tracking_code === '') {
			return false;
		}

		$.ajax({
			'type': 'POST',
			'url': 'request_port.php?module=ParcelServices&action=add_tracking_code',
			'timeout': 30000,
			'dataType': 'json',
			'context': this,
			'data': {

				'tracking_code': tracking_code,
				'service_id': $('#parcel_services_dropdown option:selected').val(),
				'order_id': data_set.order_id,
				'page_token': data_set.page_token
			},
			success: function success(response) {
				$('#tracking_code_wrapper > .frame-content > table').html(response.html);
			}
		});

		return false;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the widget
  */
	module.init = function (done) {

		if (options.container === 'tracking_code_wrapper') {
			$this.on('click', '.add_tracking_code', _addTrackingCode);
		}

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfcGFyY2VsX3RyYWNraW5nLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX2FkZFRyYWNraW5nQ29kZSIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwiZGF0YV9zZXQiLCJqc2UiLCJsaWJzIiwiZmFsbGJhY2siLCJfZGF0YSIsInRyYWNraW5nX2NvZGUiLCJ2YWwiLCJhamF4Iiwib3JkZXJfaWQiLCJwYWdlX3Rva2VuIiwic3VjY2VzcyIsInJlc3BvbnNlIiwiaHRtbCIsImluaXQiLCJkb25lIiwiY29udGFpbmVyIiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0Msd0JBREQsRUFHQyxDQUFDLFVBQUQsQ0FIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUQsVUFBUyxFQTNCVjs7QUE2QkE7QUFDQTtBQUNBOztBQUVBLEtBQUlPLG1CQUFtQixTQUFuQkEsZ0JBQW1CLENBQVNDLEtBQVQsRUFBZ0I7O0FBRXRDQSxRQUFNQyxlQUFOOztBQUVBLE1BQUlDLFdBQVdDLElBQUlDLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsS0FBbEIsQ0FBd0JYLEVBQUUsSUFBRixDQUF4QixFQUFpQyx3QkFBakMsQ0FBZjtBQUNBLE1BQUlZLGdCQUFnQlosRUFBRSwrQkFBRixFQUFtQ2EsR0FBbkMsRUFBcEI7QUFDQSxNQUFJRCxrQkFBa0IsRUFBdEIsRUFBMEI7QUFDekIsVUFBTyxLQUFQO0FBQ0E7O0FBRURaLElBQUVjLElBQUYsQ0FBTztBQUNOLFdBQVEsTUFERjtBQUVOLFVBQU8saUVBRkQ7QUFHTixjQUFXLEtBSEw7QUFJTixlQUFZLE1BSk47QUFLTixjQUFXLElBTEw7QUFNTixXQUFROztBQUVQLHFCQUFpQkYsYUFGVjtBQUdQLGtCQUFjWixFQUFFLDJDQUFGLEVBQStDYSxHQUEvQyxFQUhQO0FBSVAsZ0JBQVlOLFNBQVNRLFFBSmQ7QUFLUCxrQkFBY1IsU0FBU1M7QUFMaEIsSUFORjtBQWFOQyxZQUFTLGlCQUFTQyxRQUFULEVBQW1CO0FBQzNCbEIsTUFBRSxpREFBRixFQUFxRG1CLElBQXJELENBQTBERCxTQUFTQyxJQUFuRTtBQUNBO0FBZkssR0FBUDs7QUFrQkEsU0FBTyxLQUFQO0FBQ0EsRUE3QkQ7O0FBK0JBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0F0QixRQUFPdUIsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUIsTUFBSW5CLFFBQVFvQixTQUFSLEtBQXNCLHVCQUExQixFQUFtRDtBQUNsRHZCLFNBQU13QixFQUFOLENBQVMsT0FBVCxFQUFrQixvQkFBbEIsRUFBd0NuQixnQkFBeEM7QUFDQTs7QUFFRGlCO0FBQ0EsRUFQRDs7QUFTQTtBQUNBLFFBQU94QixNQUFQO0FBQ0EsQ0FqR0YiLCJmaWxlIjoib3JkZXJzL29yZGVyc19wYXJjZWxfdHJhY2tpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIG9yZGVyc19wYXJjZWxfdHJhY2tpbmcuanMgMjAxNS0wOC0yN1xuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgT3JkZXIgVHJhY2tpbmcgQ29kZXMgQ29udHJvbGxlclxuICpcbiAqIEBtb2R1bGUgQ29udHJvbGxlcnMvb3JkZXJzX3BhcmNlbF90cmFja2luZ1xuICovXG5neC5jb250cm9sbGVycy5tb2R1bGUoXG5cdCdvcmRlcnNfcGFyY2VsX3RyYWNraW5nJyxcblx0XG5cdFsnZmFsbGJhY2snXSxcblx0XG5cdC8qKiBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL29yZGVyc19wYXJjZWxfdHJhY2tpbmcgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBFVkVOVCBIQU5ETEVSU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBfYWRkVHJhY2tpbmdDb2RlID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFxuXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XG5cdFx0XHRcblx0XHRcdHZhciBkYXRhX3NldCA9IGpzZS5saWJzLmZhbGxiYWNrLl9kYXRhKCQodGhpcyksICdvcmRlcnNfcGFyY2VsX3RyYWNraW5nJyk7XG5cdFx0XHR2YXIgdHJhY2tpbmdfY29kZSA9ICQoJyNwYXJjZWxfc2VydmljZV90cmFja2luZ19jb2RlJykudmFsKCk7XG5cdFx0XHRpZiAodHJhY2tpbmdfY29kZSA9PT0gJycpIHtcblx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHQndHlwZSc6ICdQT1NUJyxcblx0XHRcdFx0J3VybCc6ICdyZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1QYXJjZWxTZXJ2aWNlcyZhY3Rpb249YWRkX3RyYWNraW5nX2NvZGUnLFxuXHRcdFx0XHQndGltZW91dCc6IDMwMDAwLFxuXHRcdFx0XHQnZGF0YVR5cGUnOiAnanNvbicsXG5cdFx0XHRcdCdjb250ZXh0JzogdGhpcyxcblx0XHRcdFx0J2RhdGEnOiB7XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0J3RyYWNraW5nX2NvZGUnOiB0cmFja2luZ19jb2RlLFxuXHRcdFx0XHRcdCdzZXJ2aWNlX2lkJzogJCgnI3BhcmNlbF9zZXJ2aWNlc19kcm9wZG93biBvcHRpb246c2VsZWN0ZWQnKS52YWwoKSxcblx0XHRcdFx0XHQnb3JkZXJfaWQnOiBkYXRhX3NldC5vcmRlcl9pZCxcblx0XHRcdFx0XHQncGFnZV90b2tlbic6IGRhdGFfc2V0LnBhZ2VfdG9rZW5cblx0XHRcdFx0fSxcblx0XHRcdFx0c3VjY2VzczogZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHQkKCcjdHJhY2tpbmdfY29kZV93cmFwcGVyID4gLmZyYW1lLWNvbnRlbnQgPiB0YWJsZScpLmh0bWwocmVzcG9uc2UuaHRtbCk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHRpZiAob3B0aW9ucy5jb250YWluZXIgPT09ICd0cmFja2luZ19jb2RlX3dyYXBwZXInKSB7XG5cdFx0XHRcdCR0aGlzLm9uKCdjbGljaycsICcuYWRkX3RyYWNraW5nX2NvZGUnLCBfYWRkVHJhY2tpbmdDb2RlKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
