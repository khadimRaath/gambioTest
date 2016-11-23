'use strict';

/* --------------------------------------------------------------
 callback_service.js 2016-02-01 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Checks the input values of the callback form and shows messages on error or success.
 */
gambio.widgets.module('callback_service', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {
		'successSelector': '#callback-service .alert-success',
		'errorSelector': '#callback-service .alert-danger',
		'vvCodeSelector': '#callback-service #vvcode',
		'vvCodeImageSelector': '#callback-service #vvcode_image'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Validates the form data. If an error occurs it will show the error message, otherwise the messages will 
  * be hidden.
  * 
  * @return {boolean}
  * @private
  */
	var _onSubmit = function _onSubmit() {

		var deferred = new $.Deferred();
		$(options.successSelector).addClass('hidden');
		$(options.errorSelector).addClass('hidden');

		$.ajax({
			data: $this.serialize(),
			url: 'request_port.php?module=CallbackService&action=check',
			type: 'GET',
			dataType: 'html',
			success: function success(error_message) {
				if (error_message.length > 0) {
					$(options.errorSelector).html(error_message).removeClass('hidden');

					try {
						Recaptcha.reload();
					} catch (e) {
						$(options.vvCodeSelector).val('');
						$(options.vvCodeImageSelector).attr('src', 'request_port.php?rand=' + Math.random() + '&module=CreateVVCode');
					}

					deferred.reject();
				} else {
					deferred.resolve();
				}
			}
		});
		deferred.done(_submitForm);
		return false;
	};

	/**
  * Submits the form data and shows a success message on success.
  * 
  * @private
  */
	var _submitForm = function _submitForm() {

		$.ajax({
			data: $this.serialize(),
			url: 'request_port.php?module=CallbackService&action=send',
			type: 'POST',
			dataType: 'html',
			success: function success(message) {
				if (message.length > 0) {
					$(options.successSelector).html(message).removeClass('hidden');

					try {
						Recaptcha.reload();
					} catch (e) {
						$(options.vvCodeSelector).val('');
						$(options.vvCodeImageSelector).attr('src', 'request_port.php?rand=' + Math.random() + '&module=CreateVVCode');
					}
				}
			}
		});
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.on('submit', _onSubmit);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvY2FsbGJhY2tfc2VydmljZS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9vblN1Ym1pdCIsImRlZmVycmVkIiwiRGVmZXJyZWQiLCJzdWNjZXNzU2VsZWN0b3IiLCJhZGRDbGFzcyIsImVycm9yU2VsZWN0b3IiLCJhamF4Iiwic2VyaWFsaXplIiwidXJsIiwidHlwZSIsImRhdGFUeXBlIiwic3VjY2VzcyIsImVycm9yX21lc3NhZ2UiLCJsZW5ndGgiLCJodG1sIiwicmVtb3ZlQ2xhc3MiLCJSZWNhcHRjaGEiLCJyZWxvYWQiLCJlIiwidnZDb2RlU2VsZWN0b3IiLCJ2YWwiLCJ2dkNvZGVJbWFnZVNlbGVjdG9yIiwiYXR0ciIsIk1hdGgiLCJyYW5kb20iLCJyZWplY3QiLCJyZXNvbHZlIiwiZG9uZSIsIl9zdWJtaXRGb3JtIiwibWVzc2FnZSIsImluaXQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVdBOzs7QUFHQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQ0Msa0JBREQsRUFHQyxFQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBOztBQUVBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsV0FBVztBQUNWLHFCQUFtQixrQ0FEVDtBQUVWLG1CQUFpQixpQ0FGUDtBQUdWLG9CQUFrQiwyQkFIUjtBQUlWLHlCQUF1QjtBQUpiLEVBRFo7QUFBQSxLQU9DQyxVQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQVBYO0FBQUEsS0FRQ0QsU0FBUyxFQVJWOztBQVdBOztBQUVBOzs7Ozs7O0FBT0EsS0FBSU8sWUFBWSxTQUFaQSxTQUFZLEdBQVk7O0FBRTNCLE1BQUlDLFdBQVcsSUFBSUwsRUFBRU0sUUFBTixFQUFmO0FBQ0FOLElBQUVFLFFBQVFLLGVBQVYsRUFBMkJDLFFBQTNCLENBQW9DLFFBQXBDO0FBQ0FSLElBQUVFLFFBQVFPLGFBQVYsRUFBeUJELFFBQXpCLENBQWtDLFFBQWxDOztBQUVBUixJQUFFVSxJQUFGLENBQU87QUFDTlosU0FBUUMsTUFBTVksU0FBTixFQURGO0FBRU5DLFFBQU8sc0RBRkQ7QUFHTkMsU0FBUSxLQUhGO0FBSU5DLGFBQVcsTUFKTDtBQUtOQyxZQUFVLGlCQUFTQyxhQUFULEVBQ1Y7QUFDQyxRQUFHQSxjQUFjQyxNQUFkLEdBQXVCLENBQTFCLEVBQTZCO0FBQzVCakIsT0FBRUUsUUFBUU8sYUFBVixFQUF5QlMsSUFBekIsQ0FBOEJGLGFBQTlCLEVBQTZDRyxXQUE3QyxDQUF5RCxRQUF6RDs7QUFFQSxTQUFJO0FBQ0hDLGdCQUFVQyxNQUFWO0FBQ0EsTUFGRCxDQUVFLE9BQU9DLENBQVAsRUFBVTtBQUNYdEIsUUFBRUUsUUFBUXFCLGNBQVYsRUFBMEJDLEdBQTFCLENBQThCLEVBQTlCO0FBQ0F4QixRQUFFRSxRQUFRdUIsbUJBQVYsRUFBK0JDLElBQS9CLENBQW9DLEtBQXBDLEVBQTJDLDJCQUEyQkMsS0FBS0MsTUFBTCxFQUEzQixHQUN4QyxzQkFESDtBQUVBOztBQUVEdkIsY0FBU3dCLE1BQVQ7QUFFQSxLQWJELE1BYU87QUFDTnhCLGNBQVN5QixPQUFUO0FBQ0E7QUFDRDtBQXZCSyxHQUFQO0FBeUJBekIsV0FBUzBCLElBQVQsQ0FBY0MsV0FBZDtBQUNBLFNBQU8sS0FBUDtBQUNBLEVBakNEOztBQW9DQTs7Ozs7QUFLQSxLQUFJQSxjQUFjLFNBQWRBLFdBQWMsR0FBWTs7QUFFN0JoQyxJQUFFVSxJQUFGLENBQU87QUFDTlosU0FBUUMsTUFBTVksU0FBTixFQURGO0FBRU5DLFFBQU8scURBRkQ7QUFHTkMsU0FBUSxNQUhGO0FBSU5DLGFBQVcsTUFKTDtBQUtOQyxZQUFVLGlCQUFTa0IsT0FBVCxFQUNWO0FBQ0MsUUFBR0EsUUFBUWhCLE1BQVIsR0FBaUIsQ0FBcEIsRUFBdUI7QUFDdEJqQixPQUFFRSxRQUFRSyxlQUFWLEVBQTJCVyxJQUEzQixDQUFnQ2UsT0FBaEMsRUFBeUNkLFdBQXpDLENBQXFELFFBQXJEOztBQUVBLFNBQUk7QUFDSEMsZ0JBQVVDLE1BQVY7QUFDQSxNQUZELENBRUUsT0FBT0MsQ0FBUCxFQUFVO0FBQ1h0QixRQUFFRSxRQUFRcUIsY0FBVixFQUEwQkMsR0FBMUIsQ0FBOEIsRUFBOUI7QUFDQXhCLFFBQUVFLFFBQVF1QixtQkFBVixFQUErQkMsSUFBL0IsQ0FBb0MsS0FBcEMsRUFBMkMsMkJBQTJCQyxLQUFLQyxNQUFMLEVBQTNCLEdBQ3hDLHNCQURIO0FBRUE7QUFDRDtBQUNEO0FBbEJLLEdBQVA7QUFvQkEsRUF0QkQ7O0FBd0JBOztBQUVBOzs7O0FBSUEvQixRQUFPcUMsSUFBUCxHQUFjLFVBQVNILElBQVQsRUFBZTs7QUFFNUJoQyxRQUFNb0MsRUFBTixDQUFTLFFBQVQsRUFBbUIvQixTQUFuQjs7QUFFQTJCO0FBQ0EsRUFMRDs7QUFPQTtBQUNBLFFBQU9sQyxNQUFQO0FBQ0EsQ0EvR0YiLCJmaWxlIjoid2lkZ2V0cy9jYWxsYmFja19zZXJ2aWNlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjYWxsYmFja19zZXJ2aWNlLmpzIDIwMTYtMDItMDEgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG5cbi8qKlxuICogQ2hlY2tzIHRoZSBpbnB1dCB2YWx1ZXMgb2YgdGhlIGNhbGxiYWNrIGZvcm0gYW5kIHNob3dzIG1lc3NhZ2VzIG9uIGVycm9yIG9yIHN1Y2Nlc3MuXG4gKi9cbmdhbWJpby53aWRnZXRzLm1vZHVsZShcblx0J2NhbGxiYWNrX3NlcnZpY2UnLFxuXHRcblx0W10sXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXHRcdFxuXHRcdHZhciAkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0J3N1Y2Nlc3NTZWxlY3Rvcic6ICcjY2FsbGJhY2stc2VydmljZSAuYWxlcnQtc3VjY2VzcycsXG5cdFx0XHRcdCdlcnJvclNlbGVjdG9yJzogJyNjYWxsYmFjay1zZXJ2aWNlIC5hbGVydC1kYW5nZXInLFxuXHRcdFx0XHQndnZDb2RlU2VsZWN0b3InOiAnI2NhbGxiYWNrLXNlcnZpY2UgI3Z2Y29kZScsXG5cdFx0XHRcdCd2dkNvZGVJbWFnZVNlbGVjdG9yJzogJyNjYWxsYmFjay1zZXJ2aWNlICN2dmNvZGVfaW1hZ2UnXG5cdFx0XHR9LFxuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIFZhbGlkYXRlcyB0aGUgZm9ybSBkYXRhLiBJZiBhbiBlcnJvciBvY2N1cnMgaXQgd2lsbCBzaG93IHRoZSBlcnJvciBtZXNzYWdlLCBvdGhlcndpc2UgdGhlIG1lc3NhZ2VzIHdpbGwgXG5cdFx0ICogYmUgaGlkZGVuLlxuXHRcdCAqIFxuXHRcdCAqIEByZXR1cm4ge2Jvb2xlYW59XG5cdFx0ICogQHByaXZhdGVcblx0XHQgKi9cblx0XHR2YXIgX29uU3VibWl0ID0gZnVuY3Rpb24gKCkge1xuXHRcdFx0XG5cdFx0XHR2YXIgZGVmZXJyZWQgPSBuZXcgJC5EZWZlcnJlZCgpO1xuXHRcdFx0JChvcHRpb25zLnN1Y2Nlc3NTZWxlY3RvcikuYWRkQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0JChvcHRpb25zLmVycm9yU2VsZWN0b3IpLmFkZENsYXNzKCdoaWRkZW4nKTtcblx0XHRcdFxuXHRcdFx0JC5hamF4KHtcblx0XHRcdFx0ZGF0YTogXHRcdCR0aGlzLnNlcmlhbGl6ZSgpLFxuXHRcdFx0XHR1cmw6IFx0XHQncmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9Q2FsbGJhY2tTZXJ2aWNlJmFjdGlvbj1jaGVjaycsXG5cdFx0XHRcdHR5cGU6IFx0XHQnR0VUJyxcblx0XHRcdFx0ZGF0YVR5cGU6IFx0J2h0bWwnLFxuXHRcdFx0XHRzdWNjZXNzOiBcdGZ1bmN0aW9uKGVycm9yX21lc3NhZ2UpXG5cdFx0XHRcdHtcdFx0XHRcdFx0XG5cdFx0XHRcdFx0aWYoZXJyb3JfbWVzc2FnZS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdFx0XHQkKG9wdGlvbnMuZXJyb3JTZWxlY3RvcikuaHRtbChlcnJvcl9tZXNzYWdlKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdHRyeSB7XG5cdFx0XHRcdFx0XHRcdFJlY2FwdGNoYS5yZWxvYWQoKTtcblx0XHRcdFx0XHRcdH0gY2F0Y2ggKGUpIHtcblx0XHRcdFx0XHRcdFx0JChvcHRpb25zLnZ2Q29kZVNlbGVjdG9yKS52YWwoJycpO1xuXHRcdFx0XHRcdFx0XHQkKG9wdGlvbnMudnZDb2RlSW1hZ2VTZWxlY3RvcikuYXR0cignc3JjJywgJ3JlcXVlc3RfcG9ydC5waHA/cmFuZD0nICsgTWF0aC5yYW5kb20oKSBcblx0XHRcdFx0XHRcdFx0XHQrICcmbW9kdWxlPUNyZWF0ZVZWQ29kZScpO1xuXHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRkZWZlcnJlZC5yZWplY3QoKTtcblx0XHRcdFx0XHRcdFxuXHRcdFx0XHRcdH0gZWxzZSB7XHRcdFx0XHRcdFx0XG5cdFx0XHRcdFx0XHRkZWZlcnJlZC5yZXNvbHZlKCk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdGRlZmVycmVkLmRvbmUoX3N1Ym1pdEZvcm0pO1xuXHRcdFx0cmV0dXJuIGZhbHNlO1xuXHRcdH07XG5cdFx0XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogU3VibWl0cyB0aGUgZm9ybSBkYXRhIGFuZCBzaG93cyBhIHN1Y2Nlc3MgbWVzc2FnZSBvbiBzdWNjZXNzLlxuXHRcdCAqIFxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9zdWJtaXRGb3JtID0gZnVuY3Rpb24gKCkge1xuXHRcdFx0XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRkYXRhOiBcdFx0JHRoaXMuc2VyaWFsaXplKCksXG5cdFx0XHRcdHVybDogXHRcdCdyZXF1ZXN0X3BvcnQucGhwP21vZHVsZT1DYWxsYmFja1NlcnZpY2UmYWN0aW9uPXNlbmQnLFxuXHRcdFx0XHR0eXBlOiBcdFx0J1BPU1QnLFxuXHRcdFx0XHRkYXRhVHlwZTogXHQnaHRtbCcsXG5cdFx0XHRcdHN1Y2Nlc3M6IFx0ZnVuY3Rpb24obWVzc2FnZSlcblx0XHRcdFx0e1xuXHRcdFx0XHRcdGlmKG1lc3NhZ2UubGVuZ3RoID4gMCkge1xuXHRcdFx0XHRcdFx0JChvcHRpb25zLnN1Y2Nlc3NTZWxlY3RvcikuaHRtbChtZXNzYWdlKS5yZW1vdmVDbGFzcygnaGlkZGVuJyk7XG5cdFx0XHRcdFx0XHRcblx0XHRcdFx0XHRcdHRyeVx0e1xuXHRcdFx0XHRcdFx0XHRSZWNhcHRjaGEucmVsb2FkKCk7XG5cdFx0XHRcdFx0XHR9IGNhdGNoIChlKVx0e1xuXHRcdFx0XHRcdFx0XHQkKG9wdGlvbnMudnZDb2RlU2VsZWN0b3IpLnZhbCgnJyk7XG5cdFx0XHRcdFx0XHRcdCQob3B0aW9ucy52dkNvZGVJbWFnZVNlbGVjdG9yKS5hdHRyKCdzcmMnLCAncmVxdWVzdF9wb3J0LnBocD9yYW5kPScgKyBNYXRoLnJhbmRvbSgpIFxuXHRcdFx0XHRcdFx0XHRcdCsgJyZtb2R1bGU9Q3JlYXRlVlZDb2RlJyk7XG5cdFx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5vbignc3VibWl0JywgX29uU3VibWl0KTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gUmV0dXJuIGRhdGEgdG8gd2lkZ2V0IGVuZ2luZVxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pOyJdfQ==
