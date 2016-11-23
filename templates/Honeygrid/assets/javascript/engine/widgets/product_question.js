'use strict';

/* --------------------------------------------------------------
 product_question.js 2016-08-26
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that updates that opens a lightbox for asking product questions. Sends an e-mail to the shop administrator
 * with the asked question
 */
gambio.widgets.module('product_question', ['xhr', gambio.source + '/libs/modal.ext-magnific', gambio.source + '/libs/modal'], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    $body = $('body'),
	    defaults = {
		btnOpen: '.btn-product-question',
		btnClose: '.btn-close-question-window',
		btnSend: '.btn-send-question',
		url: 'shop.php?do=ProductQuestion',
		sendUrl: 'shop.php?do=ProductQuestion/Send',
		productId: 0,
		formSelector: '#product-question-form'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	var _validateForm = function _validateForm() {
		try {
			var $privacyCheckbox = $('#privacy_accepted'),
			    error = false;

			$this.find('.form-group.mandatory, .checkbox-inline').removeClass('has-error');

			// Validate required fields. 
			$this.find('.form-group.mandatory').each(function () {
				var $formControl = $(this).find('.form-control');

				if ($formControl.val() === '') {
					$(this).addClass('has-error');
					error = true;
				}
			});

			if ($privacyCheckbox.length && !$privacyCheckbox.prop('checked')) {
				$privacyCheckbox.closest('.checkbox-inline').addClass('has-error');
				error = true;
			}

			if (error) {
				throw new Error();
			}

			return true;
		} catch (exception) {
			return false;
		}
	};

	var _openModal = function _openModal() {
		jse.libs.xhr.get({ url: options.url + '&productId=' + options.productId }, true).done(function (response) {
			_closeModal();
			$body.append(response.content);
			gambio.widgets.init($('.mfp-wrap'));
			_activateGoogleRecaptcha();
		});
	};

	var _closeModal = function _closeModal() {
		$('.mfp-bg, .mfp-wrap').remove();
		$(options.btnSend).off('click', _sendForm);
		$(options.btnClose).off('click', _closeModal);
	};

	var _sendForm = function _sendForm() {
		if (!_validateForm()) {
			return;
		}

		var url = options.sendUrl + '&productId=' + options.productId,
		    data = $(options.formSelector).serialize() + '&productLink=' + location.href;

		$.ajax({
			url: url,
			data: data,
			type: 'POST',
			dataType: 'json'
		}).done(function (response) {
			_closeModal();
			$body.append(response.content);
			gambio.widgets.init($('.mfp-wrap'));

			if (!response.success) {
				_activateGoogleRecaptcha();
			}
		});
	};

	var _activateGoogleRecaptcha = function _activateGoogleRecaptcha() {
		if (typeof window.showRecaptcha === 'function') {
			setTimeout(function () {
				window.showRecaptcha('captcha_wrapper');
			}, 500);
		}
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  */
	module.init = function (done) {
		if (options.modalMode === undefined) {
			$(options.btnOpen).on('click', _openModal);
		}
		$(options.btnSend).on('click', _sendForm);
		$(options.btnClose).on('click', _closeModal);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvcHJvZHVjdF9xdWVzdGlvbi5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRib2R5IiwiZGVmYXVsdHMiLCJidG5PcGVuIiwiYnRuQ2xvc2UiLCJidG5TZW5kIiwidXJsIiwic2VuZFVybCIsInByb2R1Y3RJZCIsImZvcm1TZWxlY3RvciIsIm9wdGlvbnMiLCJleHRlbmQiLCJfdmFsaWRhdGVGb3JtIiwiJHByaXZhY3lDaGVja2JveCIsImVycm9yIiwiZmluZCIsInJlbW92ZUNsYXNzIiwiZWFjaCIsIiRmb3JtQ29udHJvbCIsInZhbCIsImFkZENsYXNzIiwibGVuZ3RoIiwicHJvcCIsImNsb3Nlc3QiLCJFcnJvciIsImV4Y2VwdGlvbiIsIl9vcGVuTW9kYWwiLCJqc2UiLCJsaWJzIiwieGhyIiwiZ2V0IiwiZG9uZSIsInJlc3BvbnNlIiwiX2Nsb3NlTW9kYWwiLCJhcHBlbmQiLCJjb250ZW50IiwiaW5pdCIsIl9hY3RpdmF0ZUdvb2dsZVJlY2FwdGNoYSIsInJlbW92ZSIsIm9mZiIsIl9zZW5kRm9ybSIsInNlcmlhbGl6ZSIsImxvY2F0aW9uIiwiaHJlZiIsImFqYXgiLCJ0eXBlIiwiZGF0YVR5cGUiLCJzdWNjZXNzIiwid2luZG93Iiwic2hvd1JlY2FwdGNoYSIsInNldFRpbWVvdXQiLCJtb2RhbE1vZGUiLCJ1bmRlZmluZWQiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUNDLGtCQURELEVBR0MsQ0FBQyxLQUFELEVBQVFGLE9BQU9HLE1BQVAsR0FBZ0IsMEJBQXhCLEVBQW9ESCxPQUFPRyxNQUFQLEdBQWdCLGFBQXBFLENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7O0FBRUEsS0FBSUMsUUFBUUMsRUFBRSxJQUFGLENBQVo7QUFBQSxLQUNDQyxRQUFR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QUMxQkM7QUFDQWhDLFNBQU1pQyxNQUFOLENBQWFGLFNBQVNHLE9BQXRCO0FBQ0F6QyxVQUFPQyxPQUFQLENBQWV5QyxJQUFmLENBQW9CcEMsRUFBRSxXQUFGLENBQXBCOztBQUVBLE9BQUksQ0FBQ2dDLFNBQVNlLE9BQWQsRUFBdUI7QUFDdEJWO0FBQ0E7QUFDRCxHQWJEO0FBY0EsRUF0QkQ7O0FBd0JBLEtBQUlBLDJCQUEyQixTQUEzQkEsd0JBQTJCLEdBQVc7QUFDekMsTUFBSSxPQUFPVyxPQUFPQyxhQUFkLEtBQWlDLFVBQXJDLEVBQWlEO0FBQ2hEQyxjQUFXLFlBQVc7QUFDckJGLFdBQU9DLGFBQVAsQ0FBcUIsaUJBQXJCO0FBQ0EsSUFGRCxFQUVHLEdBRkg7QUFHQTtBQUNELEVBTkQ7O0FBUUE7O0FBRUE7OztBQUdBckQsUUFBT3dDLElBQVAsR0FBYyxVQUFTTCxJQUFULEVBQWU7QUFDNUIsTUFBSXJCLFFBQVF5QyxTQUFSLEtBQXNCQyxTQUExQixFQUFxQztBQUNwQ3BELEtBQUVVLFFBQVFQLE9BQVYsRUFBbUJrRCxFQUFuQixDQUFzQixPQUF0QixFQUErQjNCLFVBQS9CO0FBQ0E7QUFDRDFCLElBQUVVLFFBQVFMLE9BQVYsRUFBbUJnRCxFQUFuQixDQUFzQixPQUF0QixFQUErQmIsU0FBL0I7QUFDQXhDLElBQUVVLFFBQVFOLFFBQVYsRUFBb0JpRCxFQUFwQixDQUF1QixPQUF2QixFQUFnQ3BCLFdBQWhDOztBQUVBRjtBQUNBLEVBUkQ7O0FBVUE7QUFDQSxRQUFPbkMsTUFBUDtBQUNBLENBN0hGIiwiZmlsZSI6IndpZGdldHMvcHJvZHVjdF9xdWVzdGlvbi5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcHJvZHVjdF9xdWVzdGlvbi5qcyAyMDE2LTA4LTI2XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBXaWRnZXQgdGhhdCB1cGRhdGVzIHRoYXQgb3BlbnMgYSBsaWdodGJveCBmb3IgYXNraW5nIHByb2R1Y3QgcXVlc3Rpb25zLiBTZW5kcyBhbiBlLW1haWwgdG8gdGhlIHNob3AgYWRtaW5pc3RyYXRvclxuICogd2l0aCB0aGUgYXNrZWQgcXVlc3Rpb25cbiAqL1xuZ2FtYmlvLndpZGdldHMubW9kdWxlKFxuXHQncHJvZHVjdF9xdWVzdGlvbicsXG5cdFxuXHRbJ3hocicsIGdhbWJpby5zb3VyY2UgKyAnL2xpYnMvbW9kYWwuZXh0LW1hZ25pZmljJywgZ2FtYmlvLnNvdXJjZSArICcvbGlicy9tb2RhbCddLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblx0XHRcblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0JGJvZHkgPSAkKCdib2R5JyksXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0YnRuT3BlbjogJy5idG4tcHJvZHVjdC1xdWVzdGlvbicsXG5cdFx0XHRcdGJ0bkNsb3NlOiAnLmJ0bi1jbG9zZS1xdWVzdGlvbi13aW5kb3cnLFxuXHRcdFx0XHRidG5TZW5kOiAnLmJ0bi1zZW5kLXF1ZXN0aW9uJyxcblx0XHRcdFx0dXJsOiAnc2hvcC5waHA/ZG89UHJvZHVjdFF1ZXN0aW9uJyxcblx0XHRcdFx0c2VuZFVybDogJ3Nob3AucGhwP2RvPVByb2R1Y3RRdWVzdGlvbi9TZW5kJyxcblx0XHRcdFx0cHJvZHVjdElkOiAwLFxuXHRcdFx0XHRmb3JtU2VsZWN0b3I6ICcjcHJvZHVjdC1xdWVzdGlvbi1mb3JtJ1xuXHRcdFx0fSxcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBFVkVOVCBIQU5ETEVSICMjIyMjIyMjIyNcblx0XHRcblx0XHR2YXIgX3ZhbGlkYXRlRm9ybSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dHJ5IHtcblx0XHRcdFx0dmFyICRwcml2YWN5Q2hlY2tib3ggPSAkKCcjcHJpdmFjeV9hY2NlcHRlZCcpLCBcblx0XHRcdFx0XHRlcnJvciA9IGZhbHNlO1xuXHRcdFx0XHRcblx0XHRcdFx0JHRoaXMuZmluZCgnLmZvcm0tZ3JvdXAubWFuZGF0b3J5LCAuY2hlY2tib3gtaW5saW5lJykucmVtb3ZlQ2xhc3MoJ2hhcy1lcnJvcicpOyBcblx0XHRcdFx0XG5cdFx0XHRcdC8vIFZhbGlkYXRlIHJlcXVpcmVkIGZpZWxkcy4gXG5cdFx0XHRcdCR0aGlzLmZpbmQoJy5mb3JtLWdyb3VwLm1hbmRhdG9yeScpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0dmFyICRmb3JtQ29udHJvbCA9ICQodGhpcykuZmluZCgnLmZvcm0tY29udHJvbCcpOyBcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRpZiAoJGZvcm1Db250cm9sLnZhbCgpID09PSAnJykge1xuXHRcdFx0XHRcdFx0JCh0aGlzKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG5cdFx0XHRcdFx0XHRlcnJvciA9IHRydWU7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmICgkcHJpdmFjeUNoZWNrYm94Lmxlbmd0aCAmJiAhJHByaXZhY3lDaGVja2JveC5wcm9wKCdjaGVja2VkJykpIHtcblx0XHRcdFx0XHQkcHJpdmFjeUNoZWNrYm94LmNsb3Nlc3QoJy5jaGVja2JveC1pbmxpbmUnKS5hZGRDbGFzcygnaGFzLWVycm9yJyk7XG5cdFx0XHRcdFx0ZXJyb3IgPSB0cnVlO1xuXHRcdFx0XHR9XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoZXJyb3IpIHtcblx0XHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHR9IGNhdGNoKGV4Y2VwdGlvbikge1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX29wZW5Nb2RhbCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0anNlLmxpYnMueGhyLmdldCh7IHVybDogb3B0aW9ucy51cmwgKyAnJnByb2R1Y3RJZD0nICsgb3B0aW9ucy5wcm9kdWN0SWQgfSwgdHJ1ZSlcblx0XHRcdFx0LmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRfY2xvc2VNb2RhbCgpO1xuXHRcdFx0XHRcdCRib2R5LmFwcGVuZChyZXNwb25zZS5jb250ZW50KTtcblx0XHRcdFx0XHRnYW1iaW8ud2lkZ2V0cy5pbml0KCQoJy5tZnAtd3JhcCcpKTtcblx0XHRcdFx0XHRfYWN0aXZhdGVHb29nbGVSZWNhcHRjaGEoKTtcblx0XHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2Nsb3NlTW9kYWwgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoJy5tZnAtYmcsIC5tZnAtd3JhcCcpLnJlbW92ZSgpO1xuXHRcdFx0JChvcHRpb25zLmJ0blNlbmQpLm9mZignY2xpY2snLCBfc2VuZEZvcm0pO1xuXHRcdFx0JChvcHRpb25zLmJ0bkNsb3NlKS5vZmYoJ2NsaWNrJywgX2Nsb3NlTW9kYWwpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9zZW5kRm9ybSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0aWYgKCFfdmFsaWRhdGVGb3JtKCkpIHtcblx0XHRcdFx0cmV0dXJuOyBcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0dmFyIHVybCA9IG9wdGlvbnMuc2VuZFVybCsnJnByb2R1Y3RJZD0nK29wdGlvbnMucHJvZHVjdElkLFxuXHRcdFx0XHRkYXRhID0gJChvcHRpb25zLmZvcm1TZWxlY3Rvcikuc2VyaWFsaXplKCkgKyAnJnByb2R1Y3RMaW5rPScgKyBsb2NhdGlvbi5ocmVmO1xuXHRcdFx0XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHR1cmw6IHVybCwgIFxuXHRcdFx0XHRkYXRhOiBkYXRhLCAgXG5cdFx0XHRcdHR5cGU6ICdQT1NUJywgXG5cdFx0XHRcdGRhdGFUeXBlOiAnanNvbidcblx0XHRcdH0pLmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0X2Nsb3NlTW9kYWwoKTtcblx0XHRcdFx0JGJvZHkuYXBwZW5kKHJlc3BvbnNlLmNvbnRlbnQpO1xuXHRcdFx0XHRnYW1iaW8ud2lkZ2V0cy5pbml0KCQoJy5tZnAtd3JhcCcpKTtcblx0XHRcdFx0XG5cdFx0XHRcdGlmICghcmVzcG9uc2Uuc3VjY2Vzcykge1xuXHRcdFx0XHRcdF9hY3RpdmF0ZUdvb2dsZVJlY2FwdGNoYSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfYWN0aXZhdGVHb29nbGVSZWNhcHRjaGEgPSBmdW5jdGlvbigpIHtcblx0XHRcdGlmICh0eXBlb2Yod2luZG93LnNob3dSZWNhcHRjaGEpID09PSAnZnVuY3Rpb24nKSB7XG5cdFx0XHRcdHNldFRpbWVvdXQoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0d2luZG93LnNob3dSZWNhcHRjaGEoJ2NhcHRjaGFfd3JhcHBlcicpO1xuXHRcdFx0XHR9LCA1MDApO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5pdCBmdW5jdGlvbiBvZiB0aGUgd2lkZ2V0XG5cdFx0ICovXG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRpZiAob3B0aW9ucy5tb2RhbE1vZGUgPT09IHVuZGVmaW5lZCkge1xuXHRcdFx0XHQkKG9wdGlvbnMuYnRuT3Blbikub24oJ2NsaWNrJywgX29wZW5Nb2RhbCk7XG5cdFx0XHR9XG5cdFx0XHQkKG9wdGlvbnMuYnRuU2VuZCkub24oJ2NsaWNrJywgX3NlbmRGb3JtKTtcblx0XHRcdCQob3B0aW9ucy5idG5DbG9zZSkub24oJ2NsaWNrJywgX2Nsb3NlTW9kYWwpO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7Il19
