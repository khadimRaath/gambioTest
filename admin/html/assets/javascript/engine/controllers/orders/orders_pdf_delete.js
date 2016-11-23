'use strict';

/* --------------------------------------------------------------
 orders_pdf_delete.js 2016-08-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Order PDF Delete Controller
 *
 * @module Controllers/orders_pdf_delete
 */
gx.controllers.module('orders_pdf_delete', ['xhr', 'fallback'],

/** @lends module:Controllers/orders_pdf_delete */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var $this = $(this),
	    defaults = { type: 'invoice' },
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ------------------------------------------------------------------------
	// EVENT HANDLERS
	// ------------------------------------------------------------------------

	var _deleteHandler = function _deleteHandler(event) {
		event.preventDefault();
		event.stopPropagation();

		var $self = $(this),
		    dataset = $.extend({}, $this.data(), jse.libs.fallback._data($this, 'orders_pdf_delete'));

		var href = 'lightbox_confirm.html?section=admin_orders&amp;message=DELETE_PDF_CONFIRM_MESSAGE&amp;' + 'buttons=cancel-delete';

		var t_a_tag = $('<a href="' + href + '"></a>');
		var tmp_lightbox_identifier = $(t_a_tag).lightbox_plugin({
			'lightbox_width': '360px'
		});

		$('#lightbox_package_' + tmp_lightbox_identifier).on('click', '.delete', function () {
			$.lightbox_plugin('close', tmp_lightbox_identifier);
			if ($self.hasClass('active')) {
				return false;
			}
			$self.addClass('active');

			jse.libs.xhr.post({
				'url': 'request_port.php?module=OrderAdmin&action=deletePdf',
				'data': {
					'type': options.type,
					'file': $self.attr('rel')
				}
			}).done(function (response) {
				$self.closest('tr').remove();
				if ($('tr.' + options.type).length === 1) {
					$('tr.' + options.type).show();
				}
				$('.page_token').val(response.page_token);
			});
		});
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	/**
  * Init function of the widget
  */
	module.init = function (done) {
		$this.on('click', '.delete_pdf', _deleteHandler);
		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vcmRlcnNfcGRmX2RlbGV0ZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwidHlwZSIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZGVsZXRlSGFuZGxlciIsImV2ZW50IiwicHJldmVudERlZmF1bHQiLCJzdG9wUHJvcGFnYXRpb24iLCIkc2VsZiIsImRhdGFzZXQiLCJqc2UiLCJsaWJzIiwiZmFsbGJhY2siLCJfZGF0YSIsImhyZWYiLCJ0X2FfdGFnIiwidG1wX2xpZ2h0Ym94X2lkZW50aWZpZXIiLCJsaWdodGJveF9wbHVnaW4iLCJvbiIsImhhc0NsYXNzIiwiYWRkQ2xhc3MiLCJ4aHIiLCJwb3N0IiwiYXR0ciIsImRvbmUiLCJyZXNwb25zZSIsImNsb3Nlc3QiLCJyZW1vdmUiLCJsZW5ndGgiLCJzaG93IiwidmFsIiwicGFnZV90b2tlbiIsImluaXQiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQ0MsbUJBREQsRUFHQyxDQUFDLEtBQUQsRUFBUSxVQUFSLENBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLFdBQVcsRUFBQ0MsTUFBTSxTQUFQLEVBRFo7QUFBQSxLQUVDQyxVQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQUZYO0FBQUEsS0FHQ0QsU0FBUyxFQUhWOztBQUtBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJUSxpQkFBaUIsU0FBakJBLGNBQWlCLENBQVNDLEtBQVQsRUFBZ0I7QUFDcENBLFFBQU1DLGNBQU47QUFDQUQsUUFBTUUsZUFBTjs7QUFFQSxNQUFJQyxRQUFRVCxFQUFFLElBQUYsQ0FBWjtBQUFBLE1BQ0NVLFVBQVVWLEVBQUVJLE1BQUYsQ0FBUyxFQUFULEVBQWFMLE1BQU1ELElBQU4sRUFBYixFQUEyQmEsSUFBSUMsSUFBSixDQUFTQyxRQUFULENBQWtCQyxLQUFsQixDQUF3QmYsS0FBeEIsRUFBK0IsbUJBQS9CLENBQTNCLENBRFg7O0FBR0EsTUFBSWdCLE9BQ0gsMkZBQ0EsdUJBRkQ7O0FBSUEsTUFBSUMsVUFBVWhCLEVBQ2IsY0FBY2UsSUFBZCxHQUFxQixRQURSLENBQWQ7QUFHQSxNQUFJRSwwQkFBMEJqQixFQUFFZ0IsT0FBRixFQUFXRSxlQUFYLENBQzdCO0FBQ0MscUJBQWtCO0FBRG5CLEdBRDZCLENBQTlCOztBQUtBbEIsSUFBRSx1QkFBdUJpQix1QkFBekIsRUFBa0RFLEVBQWxELENBQXFELE9BQXJELEVBQThELFNBQTlELEVBQXlFLFlBQVc7QUFDbkZuQixLQUFFa0IsZUFBRixDQUFrQixPQUFsQixFQUEyQkQsdUJBQTNCO0FBQ0EsT0FBSVIsTUFBTVcsUUFBTixDQUFlLFFBQWYsQ0FBSixFQUE4QjtBQUM3QixXQUFPLEtBQVA7QUFDQTtBQUNEWCxTQUFNWSxRQUFOLENBQWUsUUFBZjs7QUFFQVYsT0FBSUMsSUFBSixDQUFTVSxHQUFULENBQWFDLElBQWIsQ0FBa0I7QUFDakIsV0FBTyxxREFEVTtBQUVqQixZQUFRO0FBQ1AsYUFBUXBCLFFBQVFELElBRFQ7QUFFUCxhQUFRTyxNQUFNZSxJQUFOLENBQVcsS0FBWDtBQUZEO0FBRlMsSUFBbEIsRUFNR0MsSUFOSCxDQU1RLFVBQVNDLFFBQVQsRUFBbUI7QUFDMUJqQixVQUFNa0IsT0FBTixDQUFjLElBQWQsRUFBb0JDLE1BQXBCO0FBQ0EsUUFBSTVCLEVBQUUsUUFBUUcsUUFBUUQsSUFBbEIsRUFBd0IyQixNQUF4QixLQUFtQyxDQUF2QyxFQUEwQztBQUN6QzdCLE9BQUUsUUFBUUcsUUFBUUQsSUFBbEIsRUFBd0I0QixJQUF4QjtBQUNBO0FBQ0Q5QixNQUFFLGFBQUYsRUFBaUIrQixHQUFqQixDQUFxQkwsU0FBU00sVUFBOUI7QUFDQSxJQVpEO0FBYUEsR0FwQkQ7QUFxQkEsRUF4Q0Q7O0FBMENBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0FuQyxRQUFPb0MsSUFBUCxHQUFjLFVBQVNSLElBQVQsRUFBZTtBQUM1QjFCLFFBQU1vQixFQUFOLENBQVMsT0FBVCxFQUFrQixhQUFsQixFQUFpQ2QsY0FBakM7QUFDQW9CO0FBQ0EsRUFIRDs7QUFLQTtBQUNBLFFBQU81QixNQUFQO0FBQ0EsQ0FoRkYiLCJmaWxlIjoib3JkZXJzL29yZGVyc19wZGZfZGVsZXRlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBvcmRlcnNfcGRmX2RlbGV0ZS5qcyAyMDE2LTA4LTE3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBPcmRlciBQREYgRGVsZXRlIENvbnRyb2xsZXJcbiAqXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL29yZGVyc19wZGZfZGVsZXRlXG4gKi9cbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcblx0J29yZGVyc19wZGZfZGVsZXRlJyxcblx0XG5cdFsneGhyJywgJ2ZhbGxiYWNrJ10sXG5cdFxuXHQvKiogQGxlbmRzIG1vZHVsZTpDb250cm9sbGVycy9vcmRlcnNfcGRmX2RlbGV0ZSAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRSBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGRlZmF1bHRzID0ge3R5cGU6ICdpbnZvaWNlJ30sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEVWRU5UIEhBTkRMRVJTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9kZWxldGVIYW5kbGVyID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcdFxuXHRcdFx0dmFyICRzZWxmID0gJCh0aGlzKSxcblx0XHRcdFx0ZGF0YXNldCA9ICQuZXh0ZW5kKHt9LCAkdGhpcy5kYXRhKCksIGpzZS5saWJzLmZhbGxiYWNrLl9kYXRhKCR0aGlzLCAnb3JkZXJzX3BkZl9kZWxldGUnKSk7XG5cdFx0XHRcblx0XHRcdHZhciBocmVmID1cblx0XHRcdFx0J2xpZ2h0Ym94X2NvbmZpcm0uaHRtbD9zZWN0aW9uPWFkbWluX29yZGVycyZhbXA7bWVzc2FnZT1ERUxFVEVfUERGX0NPTkZJUk1fTUVTU0FHRSZhbXA7JyArXG5cdFx0XHRcdCdidXR0b25zPWNhbmNlbC1kZWxldGUnO1xuXHRcdFx0XG5cdFx0XHR2YXIgdF9hX3RhZyA9ICQoXG5cdFx0XHRcdCc8YSBocmVmPVwiJyArIGhyZWYgKyAnXCI+PC9hPidcblx0XHRcdCk7XG5cdFx0XHR2YXIgdG1wX2xpZ2h0Ym94X2lkZW50aWZpZXIgPSAkKHRfYV90YWcpLmxpZ2h0Ym94X3BsdWdpbihcblx0XHRcdFx0e1xuXHRcdFx0XHRcdCdsaWdodGJveF93aWR0aCc6ICczNjBweCdcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoJyNsaWdodGJveF9wYWNrYWdlXycgKyB0bXBfbGlnaHRib3hfaWRlbnRpZmllcikub24oJ2NsaWNrJywgJy5kZWxldGUnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JC5saWdodGJveF9wbHVnaW4oJ2Nsb3NlJywgdG1wX2xpZ2h0Ym94X2lkZW50aWZpZXIpO1xuXHRcdFx0XHRpZiAoJHNlbGYuaGFzQ2xhc3MoJ2FjdGl2ZScpKSB7XG5cdFx0XHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdFx0XHR9XG5cdFx0XHRcdCRzZWxmLmFkZENsYXNzKCdhY3RpdmUnKTtcblx0XHRcdFx0XG5cdFx0XHRcdGpzZS5saWJzLnhoci5wb3N0KHtcblx0XHRcdFx0XHQndXJsJzogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPU9yZGVyQWRtaW4mYWN0aW9uPWRlbGV0ZVBkZicsXG5cdFx0XHRcdFx0J2RhdGEnOiB7XG5cdFx0XHRcdFx0XHQndHlwZSc6IG9wdGlvbnMudHlwZSxcblx0XHRcdFx0XHRcdCdmaWxlJzogJHNlbGYuYXR0cigncmVsJylcblx0XHRcdFx0XHR9XG5cdFx0XHRcdH0pLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHQkc2VsZi5jbG9zZXN0KCd0cicpLnJlbW92ZSgpO1xuXHRcdFx0XHRcdGlmICgkKCd0ci4nICsgb3B0aW9ucy50eXBlKS5sZW5ndGggPT09IDEpIHtcblx0XHRcdFx0XHRcdCQoJ3RyLicgKyBvcHRpb25zLnR5cGUpLnNob3coKTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0JCgnLnBhZ2VfdG9rZW4nKS52YWwocmVzcG9uc2UucGFnZV90b2tlbik7XG5cdFx0XHRcdH0pO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0JHRoaXMub24oJ2NsaWNrJywgJy5kZWxldGVfcGRmJywgX2RlbGV0ZUhhbmRsZXIpO1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
