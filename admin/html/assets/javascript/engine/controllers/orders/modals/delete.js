'use strict';

/* --------------------------------------------------------------
 delete.js 2016-05-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Delete Order Modal Controller
 */
gx.controllers.module('delete', ['modal'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Module Selector
  *
  * @type {jQuery}
  */

	var $this = $(this);

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {
		bindings: {
			selectedOrders: $this.find('.selected-orders'),
			reStock: $this.find('.re-stock'),
			reShip: $this.find('.re-ship'),
			reActivate: $this.find('.re-activate')
		}
	};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Send the modal data to the form through an AJAX call.
  *
  * @param {jQuery.Event} event
  */
	function _onSendClick(event) {
		var url = jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersModalsAjax/DeleteOrder';
		var data = {
			selectedOrders: module.bindings.selectedOrders.get().split(', '),
			reStock: module.bindings.reStock.get(),
			reShip: module.bindings.reShip.get(),
			reActivate: module.bindings.reActivate.get(),
			pageToken: jse.core.config.get('pageToken')
		};
		var $sendButton = $(event.target);

		$sendButton.addClass('disabled').prop('disabled', true);

		$.ajax({
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json'
		}).done(function (response) {
			jse.libs.info_box.service.addSuccessMessage(jse.core.lang.translate('DELETE_ORDERS_SUCCESS', 'admin_orders'));
			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');
		}).fail(function (jqxhr, textStatus, errorThrown) {
			jse.libs.modal.message({
				title: jse.core.lang.translate('error', 'messages'),
				content: jse.core.lang.translate('DELETE_ORDERS_ERROR', 'admin_orders')
			});
		}).always(function () {
			$this.modal('hide');
			$sendButton.removeClass('disabled').prop('disabled', false);
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', '.btn.send', _onSendClick);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvZGVsZXRlLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiYmluZGluZ3MiLCJzZWxlY3RlZE9yZGVycyIsImZpbmQiLCJyZVN0b2NrIiwicmVTaGlwIiwicmVBY3RpdmF0ZSIsIl9vblNlbmRDbGljayIsImV2ZW50IiwidXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsInNwbGl0IiwicGFnZVRva2VuIiwiJHNlbmRCdXR0b24iLCJ0YXJnZXQiLCJhZGRDbGFzcyIsInByb3AiLCJhamF4IiwibWV0aG9kIiwiZGF0YVR5cGUiLCJkb25lIiwicmVzcG9uc2UiLCJsaWJzIiwiaW5mb19ib3giLCJzZXJ2aWNlIiwiYWRkU3VjY2Vzc01lc3NhZ2UiLCJsYW5nIiwidHJhbnNsYXRlIiwiRGF0YVRhYmxlIiwicmVsb2FkIiwib3JkZXJzX292ZXJ2aWV3X2ZpbHRlciIsImZhaWwiLCJqcXhociIsInRleHRTdGF0dXMiLCJlcnJvclRocm93biIsIm1vZGFsIiwibWVzc2FnZSIsInRpdGxlIiwiY29udGVudCIsImFsd2F5cyIsInJlbW92ZUNsYXNzIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsUUFBdEIsRUFBZ0MsQ0FBQyxPQUFELENBQWhDLEVBQTJDLFVBQVNDLElBQVQsRUFBZTs7QUFFekQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTO0FBQ2RJLFlBQVU7QUFDVEMsbUJBQWdCSCxNQUFNSSxJQUFOLENBQVcsa0JBQVgsQ0FEUDtBQUVUQyxZQUFTTCxNQUFNSSxJQUFOLENBQVcsV0FBWCxDQUZBO0FBR1RFLFdBQVFOLE1BQU1JLElBQU4sQ0FBVyxVQUFYLENBSEM7QUFJVEcsZUFBWVAsTUFBTUksSUFBTixDQUFXLGNBQVg7QUFKSDtBQURJLEVBQWY7O0FBU0E7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNJLFlBQVQsQ0FBc0JDLEtBQXRCLEVBQTZCO0FBQzVCLE1BQU1DLE1BQU1DLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0Msa0RBQTVDO0FBQ0EsTUFBTWYsT0FBTztBQUNYSSxtQkFBZ0JMLE9BQU9JLFFBQVAsQ0FBZ0JDLGNBQWhCLENBQStCVyxHQUEvQixHQUFxQ0MsS0FBckMsQ0FBMkMsSUFBM0MsQ0FETDtBQUVYVixZQUFTUCxPQUFPSSxRQUFQLENBQWdCRyxPQUFoQixDQUF3QlMsR0FBeEIsRUFGRTtBQUdYUixXQUFRUixPQUFPSSxRQUFQLENBQWdCSSxNQUFoQixDQUF1QlEsR0FBdkIsRUFIRztBQUlYUCxlQUFZVCxPQUFPSSxRQUFQLENBQWdCSyxVQUFoQixDQUEyQk8sR0FBM0IsRUFKRDtBQUtYRSxjQUFXTCxJQUFJQyxJQUFKLENBQVNDLE1BQVQsQ0FBZ0JDLEdBQWhCLENBQW9CLFdBQXBCO0FBTEEsR0FBYjtBQU9BLE1BQU1HLGNBQWNoQixFQUFFUSxNQUFNUyxNQUFSLENBQXBCOztBQUVBRCxjQUFZRSxRQUFaLENBQXFCLFVBQXJCLEVBQWlDQyxJQUFqQyxDQUFzQyxVQUF0QyxFQUFrRCxJQUFsRDs7QUFFQW5CLElBQUVvQixJQUFGLENBQU87QUFDTlgsV0FETTtBQUVOWCxhQUZNO0FBR051QixXQUFRLE1BSEY7QUFJTkMsYUFBVTtBQUpKLEdBQVAsRUFNRUMsSUFORixDQU1PLFVBQVNDLFFBQVQsRUFBbUI7QUFDeEJkLE9BQUllLElBQUosQ0FBU0MsUUFBVCxDQUFrQkMsT0FBbEIsQ0FBMEJDLGlCQUExQixDQUNDbEIsSUFBSUMsSUFBSixDQUFTa0IsSUFBVCxDQUFjQyxTQUFkLENBQXdCLHVCQUF4QixFQUFpRCxjQUFqRCxDQUREO0FBRUE5QixLQUFFLHFCQUFGLEVBQXlCK0IsU0FBekIsR0FBcUNYLElBQXJDLENBQTBDWSxNQUExQztBQUNBaEMsS0FBRSxxQkFBRixFQUF5QmlDLHNCQUF6QixDQUFnRCxRQUFoRDtBQUNBLEdBWEYsRUFZRUMsSUFaRixDQVlPLFVBQVNDLEtBQVQsRUFBZ0JDLFVBQWhCLEVBQTRCQyxXQUE1QixFQUF5QztBQUM5QzNCLE9BQUllLElBQUosQ0FBU2EsS0FBVCxDQUFlQyxPQUFmLENBQXVCO0FBQ3RCQyxXQUFPOUIsSUFBSUMsSUFBSixDQUFTa0IsSUFBVCxDQUFjQyxTQUFkLENBQXdCLE9BQXhCLEVBQWlDLFVBQWpDLENBRGU7QUFFdEJXLGFBQVMvQixJQUFJQyxJQUFKLENBQVNrQixJQUFULENBQWNDLFNBQWQsQ0FBd0IscUJBQXhCLEVBQStDLGNBQS9DO0FBRmEsSUFBdkI7QUFJQSxHQWpCRixFQWtCRVksTUFsQkYsQ0FrQlMsWUFBVztBQUNsQjNDLFNBQU11QyxLQUFOLENBQVksTUFBWjtBQUNBdEIsZUFBWTJCLFdBQVosQ0FBd0IsVUFBeEIsRUFBb0N4QixJQUFwQyxDQUF5QyxVQUF6QyxFQUFxRCxLQUFyRDtBQUNBLEdBckJGO0FBc0JBOztBQUVEO0FBQ0E7QUFDQTs7QUFFQXRCLFFBQU8rQyxJQUFQLEdBQWMsVUFBU3JCLElBQVQsRUFBZTtBQUM1QnhCLFFBQU04QyxFQUFOLENBQVMsT0FBVCxFQUFrQixXQUFsQixFQUErQnRDLFlBQS9CO0FBQ0FnQjtBQUNBLEVBSEQ7O0FBS0EsUUFBTzFCLE1BQVA7QUFDQSxDQXJGRCIsImZpbGUiOiJvcmRlcnMvbW9kYWxzL2RlbGV0ZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBkZWxldGUuanMgMjAxNi0wNS0wNFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBEZWxldGUgT3JkZXIgTW9kYWwgQ29udHJvbGxlclxyXG4gKi9cclxuZ3guY29udHJvbGxlcnMubW9kdWxlKCdkZWxldGUnLCBbJ21vZGFsJ10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3QgbW9kdWxlID0ge1xyXG5cdFx0YmluZGluZ3M6IHtcclxuXHRcdFx0c2VsZWN0ZWRPcmRlcnM6ICR0aGlzLmZpbmQoJy5zZWxlY3RlZC1vcmRlcnMnKSxcclxuXHRcdFx0cmVTdG9jazogJHRoaXMuZmluZCgnLnJlLXN0b2NrJyksXHJcblx0XHRcdHJlU2hpcDogJHRoaXMuZmluZCgnLnJlLXNoaXAnKSxcclxuXHRcdFx0cmVBY3RpdmF0ZTogJHRoaXMuZmluZCgnLnJlLWFjdGl2YXRlJylcclxuXHRcdH1cclxuXHR9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFNlbmQgdGhlIG1vZGFsIGRhdGEgdG8gdGhlIGZvcm0gdGhyb3VnaCBhbiBBSkFYIGNhbGwuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25TZW5kQ2xpY2soZXZlbnQpIHtcclxuXHRcdGNvbnN0IHVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89T3JkZXJzTW9kYWxzQWpheC9EZWxldGVPcmRlcic7XHJcblx0XHRjb25zdCBkYXRhID0ge1xyXG5cdFx0XHRcdHNlbGVjdGVkT3JkZXJzOiBtb2R1bGUuYmluZGluZ3Muc2VsZWN0ZWRPcmRlcnMuZ2V0KCkuc3BsaXQoJywgJyksXHJcblx0XHRcdFx0cmVTdG9jazogbW9kdWxlLmJpbmRpbmdzLnJlU3RvY2suZ2V0KCksXHJcblx0XHRcdFx0cmVTaGlwOiBtb2R1bGUuYmluZGluZ3MucmVTaGlwLmdldCgpLFxyXG5cdFx0XHRcdHJlQWN0aXZhdGU6IG1vZHVsZS5iaW5kaW5ncy5yZUFjdGl2YXRlLmdldCgpLFxyXG5cdFx0XHRcdHBhZ2VUb2tlbjoganNlLmNvcmUuY29uZmlnLmdldCgncGFnZVRva2VuJylcclxuXHRcdFx0fTtcclxuXHRcdGNvbnN0ICRzZW5kQnV0dG9uID0gJChldmVudC50YXJnZXQpO1xyXG5cdFx0XHJcblx0XHQkc2VuZEJ1dHRvbi5hZGRDbGFzcygnZGlzYWJsZWQnKS5wcm9wKCdkaXNhYmxlZCcsIHRydWUpO1xyXG5cdFx0XHJcblx0XHQkLmFqYXgoe1xyXG5cdFx0XHR1cmwsXHJcblx0XHRcdGRhdGEsXHJcblx0XHRcdG1ldGhvZDogJ1BPU1QnLFxyXG5cdFx0XHRkYXRhVHlwZTogJ2pzb24nXHJcblx0XHR9KVxyXG5cdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xyXG5cdFx0XHRcdGpzZS5saWJzLmluZm9fYm94LnNlcnZpY2UuYWRkU3VjY2Vzc01lc3NhZ2UoXHJcblx0XHRcdFx0XHRqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnREVMRVRFX09SREVSU19TVUNDRVNTJywgJ2FkbWluX29yZGVycycpKTtcclxuXHRcdFx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykuRGF0YVRhYmxlKCkuYWpheC5yZWxvYWQoKTtcclxuXHRcdFx0XHQkKCcub3JkZXJzIC50YWJsZS1tYWluJykub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcigncmVsb2FkJyk7XHJcblx0XHRcdH0pXHJcblx0XHRcdC5mYWlsKGZ1bmN0aW9uKGpxeGhyLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93bikge1xyXG5cdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xyXG5cdFx0XHRcdFx0dGl0bGU6IGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdlcnJvcicsICdtZXNzYWdlcycpLFxyXG5cdFx0XHRcdFx0Y29udGVudDoganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ0RFTEVURV9PUkRFUlNfRVJST1InLCAnYWRtaW5fb3JkZXJzJylcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSlcclxuXHRcdFx0LmFsd2F5cyhmdW5jdGlvbigpIHtcclxuXHRcdFx0XHQkdGhpcy5tb2RhbCgnaGlkZScpO1xyXG5cdFx0XHRcdCRzZW5kQnV0dG9uLnJlbW92ZUNsYXNzKCdkaXNhYmxlZCcpLnByb3AoJ2Rpc2FibGVkJywgZmFsc2UpO1xyXG5cdFx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSU5JVElBTElaQVRJT05cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdCR0aGlzLm9uKCdjbGljaycsICcuYnRuLnNlbmQnLCBfb25TZW5kQ2xpY2spO1xyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxufSk7Il19
