'use strict';

/* --------------------------------------------------------------
 cancel_modal.js 2016-05-04
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Cancel Order Modal Controller
 */
gx.controllers.module('cancel', ['modal'], function (data) {

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
			reActivate: $this.find('.re-activate'),
			notifyCustomer: $this.find('.notify-customer'),
			sendComments: $this.find('.send-comments'),
			cancellationComments: $this.find('.cancellation-comments')
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
		var url = jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersModalsAjax/CancelOrder';
		var data = {
			selectedOrders: module.bindings.selectedOrders.get().split(', '),
			reStock: module.bindings.reStock.get(),
			reShip: module.bindings.reShip.get(),
			reActivate: module.bindings.reActivate.get(),
			notifyCustomer: module.bindings.notifyCustomer.get(),
			sendComments: module.bindings.sendComments.get(),
			cancellationComments: module.bindings.cancellationComments.get(),
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
			jse.libs.info_box.service.addSuccessMessage(jse.core.lang.translate('CANCEL_ORDERS_SUCCESS', 'admin_orders'));
			$('.orders .table-main').DataTable().ajax.reload();
			$('.orders .table-main').orders_overview_filter('reload');
		}).fail(function (jqxhr, textStatus, errorThrown) {
			jse.libs.modal.message({
				title: jse.core.lang.translate('error', 'messages'),
				content: jse.core.lang.translate('CANCEL_ORDERS_ERROR', 'admin_orders')
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvY2FuY2VsLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiYmluZGluZ3MiLCJzZWxlY3RlZE9yZGVycyIsImZpbmQiLCJyZVN0b2NrIiwicmVTaGlwIiwicmVBY3RpdmF0ZSIsIm5vdGlmeUN1c3RvbWVyIiwic2VuZENvbW1lbnRzIiwiY2FuY2VsbGF0aW9uQ29tbWVudHMiLCJfb25TZW5kQ2xpY2siLCJldmVudCIsInVybCIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJzcGxpdCIsInBhZ2VUb2tlbiIsIiRzZW5kQnV0dG9uIiwidGFyZ2V0IiwiYWRkQ2xhc3MiLCJwcm9wIiwiYWpheCIsIm1ldGhvZCIsImRhdGFUeXBlIiwiZG9uZSIsInJlc3BvbnNlIiwibGlicyIsImluZm9fYm94Iiwic2VydmljZSIsImFkZFN1Y2Nlc3NNZXNzYWdlIiwibGFuZyIsInRyYW5zbGF0ZSIsIkRhdGFUYWJsZSIsInJlbG9hZCIsIm9yZGVyc19vdmVydmlld19maWx0ZXIiLCJmYWlsIiwianF4aHIiLCJ0ZXh0U3RhdHVzIiwiZXJyb3JUaHJvd24iLCJtb2RhbCIsIm1lc3NhZ2UiLCJ0aXRsZSIsImNvbnRlbnQiLCJhbHdheXMiLCJyZW1vdmVDbGFzcyIsImluaXQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7QUFHQUEsR0FBR0MsV0FBSCxDQUFlQyxNQUFmLENBQXNCLFFBQXRCLEVBQWdDLENBQUMsT0FBRCxDQUFoQyxFQUEyQyxVQUFTQyxJQUFULEVBQWU7O0FBRXpEOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7O0FBS0EsS0FBTUMsUUFBUUMsRUFBRSxJQUFGLENBQWQ7O0FBRUE7Ozs7O0FBS0EsS0FBTUgsU0FBUztBQUNkSSxZQUFVO0FBQ1RDLG1CQUFnQkgsTUFBTUksSUFBTixDQUFXLGtCQUFYLENBRFA7QUFFVEMsWUFBU0wsTUFBTUksSUFBTixDQUFXLFdBQVgsQ0FGQTtBQUdURSxXQUFRTixNQUFNSSxJQUFOLENBQVcsVUFBWCxDQUhDO0FBSVRHLGVBQVlQLE1BQU1JLElBQU4sQ0FBVyxjQUFYLENBSkg7QUFLVEksbUJBQWdCUixNQUFNSSxJQUFOLENBQVcsa0JBQVgsQ0FMUDtBQU1USyxpQkFBY1QsTUFBTUksSUFBTixDQUFXLGdCQUFYLENBTkw7QUFPVE0seUJBQXNCVixNQUFNSSxJQUFOLENBQVcsd0JBQVg7QUFQYjtBQURJLEVBQWY7O0FBWUE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNPLFlBQVQsQ0FBc0JDLEtBQXRCLEVBQTZCO0FBQzVCLE1BQU1DLE1BQU1DLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0Msa0RBQTVDO0FBQ0EsTUFBTWxCLE9BQU87QUFDWkksbUJBQWdCTCxPQUFPSSxRQUFQLENBQWdCQyxjQUFoQixDQUErQmMsR0FBL0IsR0FBcUNDLEtBQXJDLENBQTJDLElBQTNDLENBREo7QUFFWmIsWUFBU1AsT0FBT0ksUUFBUCxDQUFnQkcsT0FBaEIsQ0FBd0JZLEdBQXhCLEVBRkc7QUFHWlgsV0FBUVIsT0FBT0ksUUFBUCxDQUFnQkksTUFBaEIsQ0FBdUJXLEdBQXZCLEVBSEk7QUFJWlYsZUFBWVQsT0FBT0ksUUFBUCxDQUFnQkssVUFBaEIsQ0FBMkJVLEdBQTNCLEVBSkE7QUFLWlQsbUJBQWdCVixPQUFPSSxRQUFQLENBQWdCTSxjQUFoQixDQUErQlMsR0FBL0IsRUFMSjtBQU1aUixpQkFBY1gsT0FBT0ksUUFBUCxDQUFnQk8sWUFBaEIsQ0FBNkJRLEdBQTdCLEVBTkY7QUFPWlAseUJBQXNCWixPQUFPSSxRQUFQLENBQWdCUSxvQkFBaEIsQ0FBcUNPLEdBQXJDLEVBUFY7QUFRWkUsY0FBV0wsSUFBSUMsSUFBSixDQUFTQyxNQUFULENBQWdCQyxHQUFoQixDQUFvQixXQUFwQjtBQVJDLEdBQWI7QUFVQSxNQUFNRyxjQUFjbkIsRUFBRVcsTUFBTVMsTUFBUixDQUFwQjs7QUFFQUQsY0FBWUUsUUFBWixDQUFxQixVQUFyQixFQUFpQ0MsSUFBakMsQ0FBc0MsVUFBdEMsRUFBa0QsSUFBbEQ7O0FBRUF0QixJQUFFdUIsSUFBRixDQUFPO0FBQ05YLFdBRE07QUFFTmQsYUFGTTtBQUdOMEIsV0FBUSxNQUhGO0FBSU5DLGFBQVU7QUFKSixHQUFQLEVBTUVDLElBTkYsQ0FNTyxVQUFTQyxRQUFULEVBQW1CO0FBQ3hCZCxPQUFJZSxJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLE9BQWxCLENBQTBCQyxpQkFBMUIsQ0FDQ2xCLElBQUlDLElBQUosQ0FBU2tCLElBQVQsQ0FBY0MsU0FBZCxDQUF3Qix1QkFBeEIsRUFBaUQsY0FBakQsQ0FERDtBQUVBakMsS0FBRSxxQkFBRixFQUF5QmtDLFNBQXpCLEdBQXFDWCxJQUFyQyxDQUEwQ1ksTUFBMUM7QUFDQW5DLEtBQUUscUJBQUYsRUFBeUJvQyxzQkFBekIsQ0FBZ0QsUUFBaEQ7QUFDQSxHQVhGLEVBWUVDLElBWkYsQ0FZTyxVQUFTQyxLQUFULEVBQWdCQyxVQUFoQixFQUE0QkMsV0FBNUIsRUFBeUM7QUFDOUMzQixPQUFJZSxJQUFKLENBQVNhLEtBQVQsQ0FBZUMsT0FBZixDQUF1QjtBQUN0QkMsV0FBTzlCLElBQUlDLElBQUosQ0FBU2tCLElBQVQsQ0FBY0MsU0FBZCxDQUF3QixPQUF4QixFQUFpQyxVQUFqQyxDQURlO0FBRXRCVyxhQUFTL0IsSUFBSUMsSUFBSixDQUFTa0IsSUFBVCxDQUFjQyxTQUFkLENBQXdCLHFCQUF4QixFQUErQyxjQUEvQztBQUZhLElBQXZCO0FBSUEsR0FqQkYsRUFrQkVZLE1BbEJGLENBa0JTLFlBQVc7QUFDbEI5QyxTQUFNMEMsS0FBTixDQUFZLE1BQVo7QUFDQXRCLGVBQVkyQixXQUFaLENBQXdCLFVBQXhCLEVBQW9DeEIsSUFBcEMsQ0FBeUMsVUFBekMsRUFBcUQsS0FBckQ7QUFDQSxHQXJCRjtBQXNCQTs7QUFFRDtBQUNBO0FBQ0E7O0FBRUF6QixRQUFPa0QsSUFBUCxHQUFjLFVBQVNyQixJQUFULEVBQWU7QUFDNUIzQixRQUFNaUQsRUFBTixDQUFTLE9BQVQsRUFBa0IsV0FBbEIsRUFBK0J0QyxZQUEvQjtBQUNBZ0I7QUFDQSxFQUhEOztBQUtBLFFBQU83QixNQUFQO0FBQ0EsQ0EzRkQiLCJmaWxlIjoib3JkZXJzL21vZGFscy9jYW5jZWwuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gY2FuY2VsX21vZGFsLmpzIDIwMTYtMDUtMDRcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogQ2FuY2VsIE9yZGVyIE1vZGFsIENvbnRyb2xsZXJcclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZSgnY2FuY2VsJywgWydtb2RhbCddLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHtcclxuXHRcdGJpbmRpbmdzOiB7XHJcblx0XHRcdHNlbGVjdGVkT3JkZXJzOiAkdGhpcy5maW5kKCcuc2VsZWN0ZWQtb3JkZXJzJyksXHJcblx0XHRcdHJlU3RvY2s6ICR0aGlzLmZpbmQoJy5yZS1zdG9jaycpLFxyXG5cdFx0XHRyZVNoaXA6ICR0aGlzLmZpbmQoJy5yZS1zaGlwJyksXHJcblx0XHRcdHJlQWN0aXZhdGU6ICR0aGlzLmZpbmQoJy5yZS1hY3RpdmF0ZScpLFxyXG5cdFx0XHRub3RpZnlDdXN0b21lcjogJHRoaXMuZmluZCgnLm5vdGlmeS1jdXN0b21lcicpLFxyXG5cdFx0XHRzZW5kQ29tbWVudHM6ICR0aGlzLmZpbmQoJy5zZW5kLWNvbW1lbnRzJyksXHJcblx0XHRcdGNhbmNlbGxhdGlvbkNvbW1lbnRzOiAkdGhpcy5maW5kKCcuY2FuY2VsbGF0aW9uLWNvbW1lbnRzJylcclxuXHRcdH1cclxuXHR9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFNlbmQgdGhlIG1vZGFsIGRhdGEgdG8gdGhlIGZvcm0gdGhyb3VnaCBhbiBBSkFYIGNhbGwuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25TZW5kQ2xpY2soZXZlbnQpIHtcclxuXHRcdGNvbnN0IHVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89T3JkZXJzTW9kYWxzQWpheC9DYW5jZWxPcmRlcic7XHJcblx0XHRjb25zdCBkYXRhID0ge1xyXG5cdFx0XHRzZWxlY3RlZE9yZGVyczogbW9kdWxlLmJpbmRpbmdzLnNlbGVjdGVkT3JkZXJzLmdldCgpLnNwbGl0KCcsICcpLFxyXG5cdFx0XHRyZVN0b2NrOiBtb2R1bGUuYmluZGluZ3MucmVTdG9jay5nZXQoKSxcclxuXHRcdFx0cmVTaGlwOiBtb2R1bGUuYmluZGluZ3MucmVTaGlwLmdldCgpLFxyXG5cdFx0XHRyZUFjdGl2YXRlOiBtb2R1bGUuYmluZGluZ3MucmVBY3RpdmF0ZS5nZXQoKSxcclxuXHRcdFx0bm90aWZ5Q3VzdG9tZXI6IG1vZHVsZS5iaW5kaW5ncy5ub3RpZnlDdXN0b21lci5nZXQoKSxcclxuXHRcdFx0c2VuZENvbW1lbnRzOiBtb2R1bGUuYmluZGluZ3Muc2VuZENvbW1lbnRzLmdldCgpLFxyXG5cdFx0XHRjYW5jZWxsYXRpb25Db21tZW50czogbW9kdWxlLmJpbmRpbmdzLmNhbmNlbGxhdGlvbkNvbW1lbnRzLmdldCgpLFxyXG5cdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpXHJcblx0XHR9O1xyXG5cdFx0Y29uc3QgJHNlbmRCdXR0b24gPSAkKGV2ZW50LnRhcmdldCk7XHJcblx0XHRcclxuXHRcdCRzZW5kQnV0dG9uLmFkZENsYXNzKCdkaXNhYmxlZCcpLnByb3AoJ2Rpc2FibGVkJywgdHJ1ZSk7XHJcblx0XHRcclxuXHRcdCQuYWpheCh7XHJcblx0XHRcdHVybCxcclxuXHRcdFx0ZGF0YSxcclxuXHRcdFx0bWV0aG9kOiAnUE9TVCcsXHJcblx0XHRcdGRhdGFUeXBlOiAnanNvbidcclxuXHRcdH0pXHJcblx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XHJcblx0XHRcdFx0anNlLmxpYnMuaW5mb19ib3guc2VydmljZS5hZGRTdWNjZXNzTWVzc2FnZShcclxuXHRcdFx0XHRcdGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdDQU5DRUxfT1JERVJTX1NVQ0NFU1MnLCAnYWRtaW5fb3JkZXJzJykpO1xyXG5cdFx0XHRcdCQoJy5vcmRlcnMgLnRhYmxlLW1haW4nKS5EYXRhVGFibGUoKS5hamF4LnJlbG9hZCgpO1xyXG5cdFx0XHRcdCQoJy5vcmRlcnMgLnRhYmxlLW1haW4nKS5vcmRlcnNfb3ZlcnZpZXdfZmlsdGVyKCdyZWxvYWQnKTtcclxuXHRcdFx0fSlcclxuXHRcdFx0LmZhaWwoZnVuY3Rpb24oanF4aHIsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKSB7XHJcblx0XHRcdFx0anNlLmxpYnMubW9kYWwubWVzc2FnZSh7XHJcblx0XHRcdFx0XHR0aXRsZToganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2Vycm9yJywgJ21lc3NhZ2VzJyksXHJcblx0XHRcdFx0XHRjb250ZW50OiBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQ0FOQ0VMX09SREVSU19FUlJPUicsICdhZG1pbl9vcmRlcnMnKVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9KVxyXG5cdFx0XHQuYWx3YXlzKGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdCR0aGlzLm1vZGFsKCdoaWRlJyk7XHJcblx0XHRcdFx0JHNlbmRCdXR0b24ucmVtb3ZlQ2xhc3MoJ2Rpc2FibGVkJykucHJvcCgnZGlzYWJsZWQnLCBmYWxzZSk7XHJcblx0XHRcdH0pO1xyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXMub24oJ2NsaWNrJywgJy5idG4uc2VuZCcsIF9vblNlbmRDbGljayk7XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG59KTsiXX0=
