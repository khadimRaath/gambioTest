'use strict';

/* --------------------------------------------------------------
 change_status.js 2016-05-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Change Order Status Modal Controller
 */
gx.controllers.module('change_status', ['modal'], function (data) {

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
			status: $this.find('#status-dropdown'),
			notifyCustomer: $this.find('#notify-customer'),
			sendParcelTrackingCode: $this.find('#send-parcel-tracking-code'),
			sendComment: $this.find('#send-comment'),
			comment: $this.find('#comment')
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
	function _changeStatus(event) {
		event.stopPropagation();

		if (module.bindings.status.get() === '') {
			return;
		}

		var url = jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersModalsAjax/ChangeOrderStatus';
		var data = {
			selectedOrders: module.bindings.selectedOrders.get().split(', '),
			statusId: module.bindings.status.get(),
			notifyCustomer: module.bindings.notifyCustomer.get(),
			sendParcelTrackingCode: module.bindings.sendParcelTrackingCode.get(),
			sendComment: module.bindings.sendComment.get(),
			comment: module.bindings.comment.get(),
			pageToken: jse.core.config.get('pageToken')
		};
		var $saveButton = $(event.target);

		$saveButton.addClass('disabled').attr('disabled', true);

		$.ajax({
			url: url,
			data: data,
			method: 'POST'
		}).done(function (response) {
			var content = data.notifyCustomer ? jse.core.lang.translate('MAIL_SUCCESS', 'gm_send_order') : jse.core.lang.translate('SUCCESS_ORDER_UPDATED', 'orders');

			$('.orders .table-main').DataTable().ajax.reload(null, false);
			$('.orders .table-main').orders_overview_filter('reload');

			// Show success message in the admin info box.
			jse.libs.info_box.service.addSuccessMessage(content);
		}).always(function () {
			$this.modal('hide');
			$saveButton.removeClass('disabled').attr('disabled', false);
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', '.btn.save', _changeStatus);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9tb2RhbHMvY2hhbmdlX3N0YXR1cy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImJpbmRpbmdzIiwic2VsZWN0ZWRPcmRlcnMiLCJmaW5kIiwic3RhdHVzIiwibm90aWZ5Q3VzdG9tZXIiLCJzZW5kUGFyY2VsVHJhY2tpbmdDb2RlIiwic2VuZENvbW1lbnQiLCJjb21tZW50IiwiX2NoYW5nZVN0YXR1cyIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwiZ2V0IiwidXJsIiwianNlIiwiY29yZSIsImNvbmZpZyIsInNwbGl0Iiwic3RhdHVzSWQiLCJwYWdlVG9rZW4iLCIkc2F2ZUJ1dHRvbiIsInRhcmdldCIsImFkZENsYXNzIiwiYXR0ciIsImFqYXgiLCJtZXRob2QiLCJkb25lIiwicmVzcG9uc2UiLCJjb250ZW50IiwibGFuZyIsInRyYW5zbGF0ZSIsIkRhdGFUYWJsZSIsInJlbG9hZCIsIm9yZGVyc19vdmVydmlld19maWx0ZXIiLCJsaWJzIiwiaW5mb19ib3giLCJzZXJ2aWNlIiwiYWRkU3VjY2Vzc01lc3NhZ2UiLCJhbHdheXMiLCJtb2RhbCIsInJlbW92ZUNsYXNzIiwiaW5pdCIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsZUFBdEIsRUFBdUMsQ0FBQyxPQUFELENBQXZDLEVBQWtELFVBQVNDLElBQVQsRUFBZTs7QUFFaEU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTO0FBQ2RJLFlBQVU7QUFDVEMsbUJBQWdCSCxNQUFNSSxJQUFOLENBQVcsa0JBQVgsQ0FEUDtBQUVUQyxXQUFRTCxNQUFNSSxJQUFOLENBQVcsa0JBQVgsQ0FGQztBQUdURSxtQkFBZ0JOLE1BQU1JLElBQU4sQ0FBVyxrQkFBWCxDQUhQO0FBSVRHLDJCQUF3QlAsTUFBTUksSUFBTixDQUFXLDRCQUFYLENBSmY7QUFLVEksZ0JBQWFSLE1BQU1JLElBQU4sQ0FBVyxlQUFYLENBTEo7QUFNVEssWUFBU1QsTUFBTUksSUFBTixDQUFXLFVBQVg7QUFOQTtBQURJLEVBQWY7O0FBV0E7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNNLGFBQVQsQ0FBdUJDLEtBQXZCLEVBQThCO0FBQzdCQSxRQUFNQyxlQUFOOztBQUVBLE1BQUlkLE9BQU9JLFFBQVAsQ0FBZ0JHLE1BQWhCLENBQXVCUSxHQUF2QixPQUFpQyxFQUFyQyxFQUF5QztBQUN4QztBQUNBOztBQUVELE1BQU1DLE1BQU1DLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkosR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0Msd0RBQTVDO0FBQ0EsTUFBTWQsT0FBTztBQUNYSSxtQkFBZ0JMLE9BQU9JLFFBQVAsQ0FBZ0JDLGNBQWhCLENBQStCVSxHQUEvQixHQUFxQ0ssS0FBckMsQ0FBMkMsSUFBM0MsQ0FETDtBQUVYQyxhQUFVckIsT0FBT0ksUUFBUCxDQUFnQkcsTUFBaEIsQ0FBdUJRLEdBQXZCLEVBRkM7QUFHWFAsbUJBQWdCUixPQUFPSSxRQUFQLENBQWdCSSxjQUFoQixDQUErQk8sR0FBL0IsRUFITDtBQUlYTiwyQkFBd0JULE9BQU9JLFFBQVAsQ0FBZ0JLLHNCQUFoQixDQUF1Q00sR0FBdkMsRUFKYjtBQUtYTCxnQkFBYVYsT0FBT0ksUUFBUCxDQUFnQk0sV0FBaEIsQ0FBNEJLLEdBQTVCLEVBTEY7QUFNWEosWUFBU1gsT0FBT0ksUUFBUCxDQUFnQk8sT0FBaEIsQ0FBd0JJLEdBQXhCLEVBTkU7QUFPWE8sY0FBV0wsSUFBSUMsSUFBSixDQUFTQyxNQUFULENBQWdCSixHQUFoQixDQUFvQixXQUFwQjtBQVBBLEdBQWI7QUFTQSxNQUFNUSxjQUFjcEIsRUFBRVUsTUFBTVcsTUFBUixDQUFwQjs7QUFFQUQsY0FBWUUsUUFBWixDQUFxQixVQUFyQixFQUFpQ0MsSUFBakMsQ0FBc0MsVUFBdEMsRUFBa0QsSUFBbEQ7O0FBRUF2QixJQUFFd0IsSUFBRixDQUFPO0FBQ05YLFdBRE07QUFFTmYsYUFGTTtBQUdOMkIsV0FBUTtBQUhGLEdBQVAsRUFLRUMsSUFMRixDQUtPLFVBQVNDLFFBQVQsRUFBbUI7QUFDeEIsT0FBTUMsVUFBVTlCLEtBQUtPLGNBQUwsR0FDQVMsSUFBSUMsSUFBSixDQUFTYyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsY0FBeEIsRUFBd0MsZUFBeEMsQ0FEQSxHQUVBaEIsSUFBSUMsSUFBSixDQUFTYyxJQUFULENBQWNDLFNBQWQsQ0FBd0IsdUJBQXhCLEVBQWlELFFBQWpELENBRmhCOztBQUlBOUIsS0FBRSxxQkFBRixFQUF5QitCLFNBQXpCLEdBQXFDUCxJQUFyQyxDQUEwQ1EsTUFBMUMsQ0FBaUQsSUFBakQsRUFBdUQsS0FBdkQ7QUFDQWhDLEtBQUUscUJBQUYsRUFBeUJpQyxzQkFBekIsQ0FBZ0QsUUFBaEQ7O0FBRUE7QUFDQW5CLE9BQUlvQixJQUFKLENBQVNDLFFBQVQsQ0FBa0JDLE9BQWxCLENBQTBCQyxpQkFBMUIsQ0FBNENULE9BQTVDO0FBQ0EsR0FmRixFQWdCRVUsTUFoQkYsQ0FnQlMsWUFBVztBQUNsQnZDLFNBQU13QyxLQUFOLENBQVksTUFBWjtBQUNBbkIsZUFBWW9CLFdBQVosQ0FBd0IsVUFBeEIsRUFBb0NqQixJQUFwQyxDQUF5QyxVQUF6QyxFQUFxRCxLQUFyRDtBQUNBLEdBbkJGO0FBb0JBOztBQUVEO0FBQ0E7QUFDQTs7QUFFQTFCLFFBQU80QyxJQUFQLEdBQWMsVUFBU2YsSUFBVCxFQUFlO0FBQzVCM0IsUUFBTTJDLEVBQU4sQ0FBUyxPQUFULEVBQWtCLFdBQWxCLEVBQStCakMsYUFBL0I7QUFDQWlCO0FBQ0EsRUFIRDs7QUFLQSxRQUFPN0IsTUFBUDtBQUNBLENBN0ZEIiwiZmlsZSI6Im9yZGVycy9tb2RhbHMvY2hhbmdlX3N0YXR1cy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gY2hhbmdlX3N0YXR1cy5qcyAyMDE2LTA1LTA5XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBDaGFuZ2UgT3JkZXIgU3RhdHVzIE1vZGFsIENvbnRyb2xsZXJcbiAqL1xuZ3guY29udHJvbGxlcnMubW9kdWxlKCdjaGFuZ2Vfc3RhdHVzJywgWydtb2RhbCddLCBmdW5jdGlvbihkYXRhKSB7XG5cdFxuXHQndXNlIHN0cmljdCc7XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gVkFSSUFCTEVTXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxuXHQgKlxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxuXHQgKi9cblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xuXHRcblx0LyoqXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxuXHQgKlxuXHQgKiBAdHlwZSB7T2JqZWN0fVxuXHQgKi9cblx0Y29uc3QgbW9kdWxlID0ge1xuXHRcdGJpbmRpbmdzOiB7XG5cdFx0XHRzZWxlY3RlZE9yZGVyczogJHRoaXMuZmluZCgnLnNlbGVjdGVkLW9yZGVycycpLFxuXHRcdFx0c3RhdHVzOiAkdGhpcy5maW5kKCcjc3RhdHVzLWRyb3Bkb3duJyksXG5cdFx0XHRub3RpZnlDdXN0b21lcjogJHRoaXMuZmluZCgnI25vdGlmeS1jdXN0b21lcicpLFxuXHRcdFx0c2VuZFBhcmNlbFRyYWNraW5nQ29kZTogJHRoaXMuZmluZCgnI3NlbmQtcGFyY2VsLXRyYWNraW5nLWNvZGUnKSxcblx0XHRcdHNlbmRDb21tZW50OiAkdGhpcy5maW5kKCcjc2VuZC1jb21tZW50JyksXG5cdFx0XHRjb21tZW50OiAkdGhpcy5maW5kKCcjY29tbWVudCcpXG5cdFx0fVxuXHR9O1xuXHRcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdC8vIEZVTkNUSU9OU1xuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XG5cdC8qKlxuXHQgKiBTZW5kIHRoZSBtb2RhbCBkYXRhIHRvIHRoZSBmb3JtIHRocm91Z2ggYW4gQUpBWCBjYWxsLlxuXHQgKlxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcblx0ICovXG5cdGZ1bmN0aW9uIF9jaGFuZ2VTdGF0dXMoZXZlbnQpIHtcblx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblx0XHRcblx0XHRpZiAobW9kdWxlLmJpbmRpbmdzLnN0YXR1cy5nZXQoKSA9PT0gJycpIHtcblx0XHRcdHJldHVybjtcblx0XHR9XG5cdFx0XG5cdFx0Y29uc3QgdXJsID0ganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2FkbWluLnBocD9kbz1PcmRlcnNNb2RhbHNBamF4L0NoYW5nZU9yZGVyU3RhdHVzJztcblx0XHRjb25zdCBkYXRhID0ge1xuXHRcdFx0XHRzZWxlY3RlZE9yZGVyczogbW9kdWxlLmJpbmRpbmdzLnNlbGVjdGVkT3JkZXJzLmdldCgpLnNwbGl0KCcsICcpLFxuXHRcdFx0XHRzdGF0dXNJZDogbW9kdWxlLmJpbmRpbmdzLnN0YXR1cy5nZXQoKSxcblx0XHRcdFx0bm90aWZ5Q3VzdG9tZXI6IG1vZHVsZS5iaW5kaW5ncy5ub3RpZnlDdXN0b21lci5nZXQoKSxcblx0XHRcdFx0c2VuZFBhcmNlbFRyYWNraW5nQ29kZTogbW9kdWxlLmJpbmRpbmdzLnNlbmRQYXJjZWxUcmFja2luZ0NvZGUuZ2V0KCksXG5cdFx0XHRcdHNlbmRDb21tZW50OiBtb2R1bGUuYmluZGluZ3Muc2VuZENvbW1lbnQuZ2V0KCksXG5cdFx0XHRcdGNvbW1lbnQ6IG1vZHVsZS5iaW5kaW5ncy5jb21tZW50LmdldCgpLFxuXHRcdFx0XHRwYWdlVG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpXG5cdFx0XHR9O1xuXHRcdGNvbnN0ICRzYXZlQnV0dG9uID0gJChldmVudC50YXJnZXQpO1xuXHRcdFxuXHRcdCRzYXZlQnV0dG9uLmFkZENsYXNzKCdkaXNhYmxlZCcpLmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XG5cdFx0XG5cdFx0JC5hamF4KHtcblx0XHRcdHVybCxcblx0XHRcdGRhdGEsXG5cdFx0XHRtZXRob2Q6ICdQT1NUJ1xuXHRcdH0pXG5cdFx0XHQuZG9uZShmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRjb25zdCBjb250ZW50ID0gZGF0YS5ub3RpZnlDdXN0b21lciA/XG5cdFx0XHRcdCAgICAgICAgICAgICAgICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnTUFJTF9TVUNDRVNTJywgJ2dtX3NlbmRfb3JkZXInKSA6XG5cdFx0XHRcdCAgICAgICAgICAgICAgICBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnU1VDQ0VTU19PUkRFUl9VUERBVEVEJywgJ29yZGVycycpO1xuXHRcdFx0XHRcblx0XHRcdFx0JCgnLm9yZGVycyAudGFibGUtbWFpbicpLkRhdGFUYWJsZSgpLmFqYXgucmVsb2FkKG51bGwsIGZhbHNlKTtcblx0XHRcdFx0JCgnLm9yZGVycyAudGFibGUtbWFpbicpLm9yZGVyc19vdmVydmlld19maWx0ZXIoJ3JlbG9hZCcpOyBcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFNob3cgc3VjY2VzcyBtZXNzYWdlIGluIHRoZSBhZG1pbiBpbmZvIGJveC5cblx0XHRcdFx0anNlLmxpYnMuaW5mb19ib3guc2VydmljZS5hZGRTdWNjZXNzTWVzc2FnZShjb250ZW50KTtcblx0XHRcdH0pXG5cdFx0XHQuYWx3YXlzKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkdGhpcy5tb2RhbCgnaGlkZScpO1xuXHRcdFx0XHQkc2F2ZUJ1dHRvbi5yZW1vdmVDbGFzcygnZGlzYWJsZWQnKS5hdHRyKCdkaXNhYmxlZCcsIGZhbHNlKTtcblx0XHRcdH0pO1xuXHR9XG5cdFxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0Ly8gSU5JVElBTElaQVRJT05cblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHQkdGhpcy5vbignY2xpY2snLCAnLmJ0bi5zYXZlJywgX2NoYW5nZVN0YXR1cyk7XG5cdFx0ZG9uZSgpO1xuXHR9O1xuXHRcblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
