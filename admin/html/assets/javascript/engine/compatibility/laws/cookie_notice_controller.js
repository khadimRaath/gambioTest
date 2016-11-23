'use strict';

/* --------------------------------------------------------------
 cookies_notice_controller.js 2016-04-01
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Cookie Notice Controller
 *
 * Compatibility module that handles the "Cookie Notice" page under the "Rights" menu of "Shop Settings" section. 
 * The data of the form are updated upon change and this module will just post them to LawsController. Check out 
 * the fields that are language dependent, they will be changed when the user selects a language from the language 
 * switcher component.
 *
 * @module Compatibility/cookie_notice_controller
 */
gx.compatibility.module('cookie_notice_controller', ['loading_spinner'], function (data) {

	'use strict';

	var
	/**
  * Module Selector
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Module Instance
  *
  * @type {object}
  */
	module = {
		model: {
			formData: jse.core.config.get('appUrl') + '/admin/admin.php?do=Laws/GetCookiePreferences&pageToken=' + jse.core.config.get('pageToken')
		}
	};

	/**
  * Show message in ".message_stack_container" object. 
  * 
  * The message will be hidden after 5 seconds.
  * 
  * @param {string} text The text to be displayed.
  * @param {string} type Provide "success" or "danger". 
  */
	var _showMessage = function _showMessage(text, type) {
		var $messageEntry = $('<div/>'),
		    $messageStack = $('.message_stack_container');
		$messageEntry.addClass('alert alert-' + type).text(text).appendTo('.message_stack_container');
		$messageStack.removeClass('hidden');
		setTimeout(function () {
			$messageEntry.remove();
			$messageStack.addClass('hidden');
		}, 5000);
	};

	/**
  * Initialize Module
  */
	module.init = function (done) {
		// Form submit event handler. 
		$this.on('submit', function (e) {
			e.preventDefault();

			// Prepare form data and send them to the LawsController class. 
			var postUrl = jse.core.config.get('appUrl') + '/admin/admin.php?do=Laws/SaveCookiePreferences',
			    postData = $.extend({ pageToken: jse.core.config.get('pageToken') }, module.model.formData),
			    $spinner;

			$.ajax({
				url: postUrl,
				type: 'POST',
				data: postData,
				dataType: 'json',
				beforeSend: function beforeSend() {
					$spinner = jse.libs.loading_spinner.show($this);
				}
			}).done(function () {
				// Display success message.
				_showMessage(jse.core.lang.translate('TXT_SAVE_SUCCESS', 'admin_general'), 'success');
			}).fail(function (jqxhr, textStatus, errorThrown) {
				// Display failure message.
				_showMessage(jse.core.lang.translate('TXT_SAVE_ERROR', 'admin_general'), 'danger');
				jse.core.debug.error('Could not save Cookie Notice preferences:', jqxhr, textStatus, errorThrown);
			}).always(function () {
				jse.libs.loading_spinner.hide($spinner);

				// Scroll to the top, so that the user sees the appropriate message.
				$('html, body').animate({ scrollTop: 0 });
			});
		});

		// Language change event handler. 
		$('.languages').on('click', 'a', function (e) {
			e.preventDefault();

			$(this).siblings().removeClass('active');
			$(this).addClass('active');

			// Load the language specific fields.
			$.each(module.model.formData, function (name, value) {
				var $element = $this.find('[name="' + name + '"]');

				if ($element.data('multilanguage') !== undefined) {
					var selectedLanguageCode = $('.languages a.active').data('code');
					$element.val(value[selectedLanguageCode]);
					if ($element.is('textarea')) {
						CKEDITOR.instances[name].setData(value[selectedLanguageCode]);
					}
				} else {
					$element.val(value);

					if ($element.is(':checkbox') && value === 'true') {
						$element.parent().addClass('checked');
						$element.prop('checked', true);
					}

					if (name === 'position' && !value) {
						$element.find('option[value="top"]').prop('selected', true).trigger('change');
					}
				}
			});
		});

		// Input change event handlers.
		$this.on('change', 'input:hidden, input:text, select, textarea', function () {
			if ($(this).data('multilanguage') !== undefined) {
				var selectedLanguageCode = $('.languages a.active').data('code');
				module.model.formData[$(this).attr('name')][selectedLanguageCode] = $(this).val();
			} else {
				module.model.formData[$(this).attr('name')] = $(this).val();
			}
		});

		$this.on('click', '.switcher', function () {
			module.model.formData[$(this).find('input:checkbox').attr('name')] = $(this).hasClass('checked');
		});

		// CKEditor change event handler. 
		for (var i in CKEDITOR.instances) {
			CKEDITOR.instances[i].on('change', function () {
				CKEDITOR.instances[i].updateElement();
				$('[name="' + i + '"]').trigger('change');
			});
		}

		// Select active language.
		$('.languages').find('.active').click();

		// Set the color-preview colors.
		$this.find('.color-preview').each(function () {
			$(this).css('background-color', $(this).siblings('input:hidden').val());
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxhd3MvY29va2llX25vdGljZV9jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJtb2RlbCIsImZvcm1EYXRhIiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsIl9zaG93TWVzc2FnZSIsInRleHQiLCJ0eXBlIiwiJG1lc3NhZ2VFbnRyeSIsIiRtZXNzYWdlU3RhY2siLCJhZGRDbGFzcyIsImFwcGVuZFRvIiwicmVtb3ZlQ2xhc3MiLCJzZXRUaW1lb3V0IiwicmVtb3ZlIiwiaW5pdCIsImRvbmUiLCJvbiIsImUiLCJwcmV2ZW50RGVmYXVsdCIsInBvc3RVcmwiLCJwb3N0RGF0YSIsImV4dGVuZCIsInBhZ2VUb2tlbiIsIiRzcGlubmVyIiwiYWpheCIsInVybCIsImRhdGFUeXBlIiwiYmVmb3JlU2VuZCIsImxpYnMiLCJsb2FkaW5nX3NwaW5uZXIiLCJzaG93IiwibGFuZyIsInRyYW5zbGF0ZSIsImZhaWwiLCJqcXhociIsInRleHRTdGF0dXMiLCJlcnJvclRocm93biIsImRlYnVnIiwiZXJyb3IiLCJhbHdheXMiLCJoaWRlIiwiYW5pbWF0ZSIsInNjcm9sbFRvcCIsInNpYmxpbmdzIiwiZWFjaCIsIm5hbWUiLCJ2YWx1ZSIsIiRlbGVtZW50IiwiZmluZCIsInVuZGVmaW5lZCIsInNlbGVjdGVkTGFuZ3VhZ2VDb2RlIiwidmFsIiwiaXMiLCJDS0VESVRPUiIsImluc3RhbmNlcyIsInNldERhdGEiLCJwYXJlbnQiLCJwcm9wIiwidHJpZ2dlciIsImF0dHIiLCJoYXNDbGFzcyIsImkiLCJ1cGRhdGVFbGVtZW50IiwiY2xpY2siLCJjc3MiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7OztBQVVBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLDBCQURELEVBR0MsQ0FBQyxpQkFBRCxDQUhELEVBS0MsVUFBU0MsSUFBVCxFQUFlOztBQUVk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QUFxQixTQUFyQixFQUFnQ2UsS0FBaEM7O0FBRUE7QUFDQW5FLFFBQU1vRCxJQUFOLENBQVcsZ0JBQVgsRUFBNkJKLElBQTdCLENBQWtDLFlBQVc7QUFDNUMvQyxLQUFFLElBQUYsRUFBUW1FLEdBQVIsQ0FBWSxrQkFBWixFQUFnQ25FLEVBQUUsSUFBRixFQUFROEMsUUFBUixDQUFpQixjQUFqQixFQUFpQ1EsR0FBakMsRUFBaEM7QUFDQSxHQUZEOztBQUlBcEM7QUFDQSxFQWxHRDs7QUFvR0EsUUFBT3JCLE1BQVA7QUFDQSxDQTNKRiIsImZpbGUiOiJsYXdzL2Nvb2tpZV9ub3RpY2VfY29udHJvbGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBjb29raWVzX25vdGljZV9jb250cm9sbGVyLmpzIDIwMTYtMDQtMDFcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgQ29va2llIE5vdGljZSBDb250cm9sbGVyXHJcbiAqXHJcbiAqIENvbXBhdGliaWxpdHkgbW9kdWxlIHRoYXQgaGFuZGxlcyB0aGUgXCJDb29raWUgTm90aWNlXCIgcGFnZSB1bmRlciB0aGUgXCJSaWdodHNcIiBtZW51IG9mIFwiU2hvcCBTZXR0aW5nc1wiIHNlY3Rpb24uIFxyXG4gKiBUaGUgZGF0YSBvZiB0aGUgZm9ybSBhcmUgdXBkYXRlZCB1cG9uIGNoYW5nZSBhbmQgdGhpcyBtb2R1bGUgd2lsbCBqdXN0IHBvc3QgdGhlbSB0byBMYXdzQ29udHJvbGxlci4gQ2hlY2sgb3V0IFxyXG4gKiB0aGUgZmllbGRzIHRoYXQgYXJlIGxhbmd1YWdlIGRlcGVuZGVudCwgdGhleSB3aWxsIGJlIGNoYW5nZWQgd2hlbiB0aGUgdXNlciBzZWxlY3RzIGEgbGFuZ3VhZ2UgZnJvbSB0aGUgbGFuZ3VhZ2UgXHJcbiAqIHN3aXRjaGVyIGNvbXBvbmVudC5cclxuICpcclxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2Nvb2tpZV9ub3RpY2VfY29udHJvbGxlclxyXG4gKi9cclxuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXHJcblx0J2Nvb2tpZV9ub3RpY2VfY29udHJvbGxlcicsXHJcblx0XHJcblx0Wydsb2FkaW5nX3NwaW5uZXInXSxcclxuXHRcclxuXHRmdW5jdGlvbihkYXRhKSB7XHJcblx0XHRcclxuXHRcdCd1c2Ugc3RyaWN0JztcclxuXHRcdFxyXG5cdFx0dmFyXHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdG1vZHVsZSA9IHtcclxuXHRcdFx0XHRtb2RlbDoge1xyXG5cdFx0XHRcdFx0Zm9ybURhdGE6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICtcclxuXHRcdFx0XHRcdCcvYWRtaW4vYWRtaW4ucGhwP2RvPUxhd3MvR2V0Q29va2llUHJlZmVyZW5jZXMmcGFnZVRva2VuPScgKyBqc2UuY29yZS5jb25maWcuZ2V0KCdwYWdlVG9rZW4nKVxyXG5cdFx0XHRcdH1cclxuXHRcdFx0fTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBTaG93IG1lc3NhZ2UgaW4gXCIubWVzc2FnZV9zdGFja19jb250YWluZXJcIiBvYmplY3QuIFxyXG5cdFx0ICogXHJcblx0XHQgKiBUaGUgbWVzc2FnZSB3aWxsIGJlIGhpZGRlbiBhZnRlciA1IHNlY29uZHMuXHJcblx0XHQgKiBcclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB0ZXh0IFRoZSB0ZXh0IHRvIGJlIGRpc3BsYXllZC5cclxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSB0eXBlIFByb3ZpZGUgXCJzdWNjZXNzXCIgb3IgXCJkYW5nZXJcIi4gXHJcblx0XHQgKi9cclxuXHRcdHZhciBfc2hvd01lc3NhZ2UgPSBmdW5jdGlvbih0ZXh0LCB0eXBlKSB7XHJcblx0XHRcdHZhciAkbWVzc2FnZUVudHJ5ID0gJCgnPGRpdi8+JyksXHJcblx0XHRcdFx0JG1lc3NhZ2VTdGFjayA9ICQoJy5tZXNzYWdlX3N0YWNrX2NvbnRhaW5lcicpO1xyXG5cdFx0XHQkbWVzc2FnZUVudHJ5XHJcblx0XHRcdFx0LmFkZENsYXNzKCdhbGVydCBhbGVydC0nICsgdHlwZSlcclxuXHRcdFx0XHQudGV4dCh0ZXh0KVxyXG5cdFx0XHRcdC5hcHBlbmRUbygnLm1lc3NhZ2Vfc3RhY2tfY29udGFpbmVyJyk7XHJcblx0XHRcdCRtZXNzYWdlU3RhY2sucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xyXG5cdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdCRtZXNzYWdlRW50cnkucmVtb3ZlKCk7XHJcblx0XHRcdFx0JG1lc3NhZ2VTdGFjay5hZGRDbGFzcygnaGlkZGVuJyk7XHJcblx0XHRcdH0sIDUwMDApO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBJbml0aWFsaXplIE1vZHVsZVxyXG5cdFx0ICovXHJcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdFx0Ly8gRm9ybSBzdWJtaXQgZXZlbnQgaGFuZGxlci4gXHJcblx0XHRcdCR0aGlzLm9uKCdzdWJtaXQnLCBmdW5jdGlvbihlKSB7XHJcblx0XHRcdFx0ZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdC8vIFByZXBhcmUgZm9ybSBkYXRhIGFuZCBzZW5kIHRoZW0gdG8gdGhlIExhd3NDb250cm9sbGVyIGNsYXNzLiBcclxuXHRcdFx0XHR2YXIgcG9zdFVybCA9IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89TGF3cy9TYXZlQ29va2llUHJlZmVyZW5jZXMnLFxyXG5cdFx0XHRcdFx0cG9zdERhdGEgPSAkLmV4dGVuZCh7cGFnZVRva2VuOiBqc2UuY29yZS5jb25maWcuZ2V0KCdwYWdlVG9rZW4nKX0sIG1vZHVsZS5tb2RlbC5mb3JtRGF0YSksXHJcblx0XHRcdFx0XHQkc3Bpbm5lcjtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQkLmFqYXgoe1xyXG5cdFx0XHRcdFx0XHR1cmw6IHBvc3RVcmwsXHJcblx0XHRcdFx0XHRcdHR5cGU6ICdQT1NUJyxcclxuXHRcdFx0XHRcdFx0ZGF0YTogcG9zdERhdGEsXHJcblx0XHRcdFx0XHRcdGRhdGFUeXBlOiAnanNvbicsXHJcblx0XHRcdFx0XHRcdGJlZm9yZVNlbmQ6IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0XHRcdCRzcGlubmVyID0ganNlLmxpYnMubG9hZGluZ19zcGlubmVyLnNob3coJHRoaXMpO1xyXG5cdFx0XHRcdFx0XHR9XHJcblx0XHRcdFx0XHR9KVxyXG5cdFx0XHRcdFx0LmRvbmUoZnVuY3Rpb24oKSB7IC8vIERpc3BsYXkgc3VjY2VzcyBtZXNzYWdlLlxyXG5cdFx0XHRcdFx0XHRfc2hvd01lc3NhZ2UoanNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ1RYVF9TQVZFX1NVQ0NFU1MnLCAnYWRtaW5fZ2VuZXJhbCcpLCAnc3VjY2VzcycpO1xyXG5cdFx0XHRcdFx0fSlcclxuXHRcdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKGpxeGhyLCB0ZXh0U3RhdHVzLCBlcnJvclRocm93bikgeyAvLyBEaXNwbGF5IGZhaWx1cmUgbWVzc2FnZS5cclxuXHRcdFx0XHRcdFx0X3Nob3dNZXNzYWdlKGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdUWFRfU0FWRV9FUlJPUicsICdhZG1pbl9nZW5lcmFsJyksICdkYW5nZXInKTtcclxuXHRcdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ0NvdWxkIG5vdCBzYXZlIENvb2tpZSBOb3RpY2UgcHJlZmVyZW5jZXM6JywganF4aHIsIHRleHRTdGF0dXMsIFxyXG5cdFx0XHRcdFx0XHRcdGVycm9yVGhyb3duKTsgXHJcblx0XHRcdFx0XHR9KVxyXG5cdFx0XHRcdFx0LmFsd2F5cyhmdW5jdGlvbigpIHtcclxuXHRcdFx0XHRcdFx0anNlLmxpYnMubG9hZGluZ19zcGlubmVyLmhpZGUoJHNwaW5uZXIpO1xyXG5cclxuXHRcdFx0XHRcdFx0Ly8gU2Nyb2xsIHRvIHRoZSB0b3AsIHNvIHRoYXQgdGhlIHVzZXIgc2VlcyB0aGUgYXBwcm9wcmlhdGUgbWVzc2FnZS5cclxuXHRcdFx0XHRcdFx0JCgnaHRtbCwgYm9keScpLmFuaW1hdGUoeyBzY3JvbGxUb3A6IDAgfSk7XHJcblx0XHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBMYW5ndWFnZSBjaGFuZ2UgZXZlbnQgaGFuZGxlci4gXHJcblx0XHRcdCQoJy5sYW5ndWFnZXMnKS5vbignY2xpY2snLCAnYScsIGZ1bmN0aW9uKGUpIHtcclxuXHRcdFx0XHRlLnByZXZlbnREZWZhdWx0KCk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0JCh0aGlzKS5zaWJsaW5ncygpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxuXHRcdFx0XHQkKHRoaXMpLmFkZENsYXNzKCdhY3RpdmUnKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQvLyBMb2FkIHRoZSBsYW5ndWFnZSBzcGVjaWZpYyBmaWVsZHMuXHJcblx0XHRcdFx0JC5lYWNoKG1vZHVsZS5tb2RlbC5mb3JtRGF0YSwgZnVuY3Rpb24obmFtZSwgdmFsdWUpIHtcclxuXHRcdFx0XHRcdHZhciAkZWxlbWVudCA9ICR0aGlzLmZpbmQoJ1tuYW1lPVwiJyArIG5hbWUgKyAnXCJdJyk7XHJcblx0XHRcdFx0XHRcclxuXHRcdFx0XHRcdGlmICgkZWxlbWVudC5kYXRhKCdtdWx0aWxhbmd1YWdlJykgIT09IHVuZGVmaW5lZCkge1xyXG5cdFx0XHRcdFx0XHR2YXIgc2VsZWN0ZWRMYW5ndWFnZUNvZGUgPSAkKCcubGFuZ3VhZ2VzIGEuYWN0aXZlJykuZGF0YSgnY29kZScpO1xyXG5cdFx0XHRcdFx0XHQkZWxlbWVudC52YWwodmFsdWVbc2VsZWN0ZWRMYW5ndWFnZUNvZGVdKTtcclxuXHRcdFx0XHRcdFx0aWYgKCRlbGVtZW50LmlzKCd0ZXh0YXJlYScpKSB7XHJcblx0XHRcdFx0XHRcdFx0Q0tFRElUT1IuaW5zdGFuY2VzW25hbWVdLnNldERhdGEodmFsdWVbc2VsZWN0ZWRMYW5ndWFnZUNvZGVdKTtcclxuXHRcdFx0XHRcdFx0fSBcclxuXHRcdFx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0XHRcdCRlbGVtZW50LnZhbCh2YWx1ZSk7XHJcblxyXG5cdFx0XHRcdFx0XHRpZiAoJGVsZW1lbnQuaXMoJzpjaGVja2JveCcpICYmIHZhbHVlID09PSAndHJ1ZScpIHtcclxuXHRcdFx0XHRcdFx0XHQkZWxlbWVudC5wYXJlbnQoKS5hZGRDbGFzcygnY2hlY2tlZCcpOyBcclxuXHRcdFx0XHRcdFx0XHQkZWxlbWVudC5wcm9wKCdjaGVja2VkJywgdHJ1ZSk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHJcblx0XHRcdFx0XHRcdGlmIChuYW1lID09PSAncG9zaXRpb24nICYmICF2YWx1ZSkge1xyXG5cdFx0XHRcdFx0XHRcdCRlbGVtZW50LmZpbmQoJ29wdGlvblt2YWx1ZT1cInRvcFwiXScpLnByb3AoJ3NlbGVjdGVkJywgdHJ1ZSkudHJpZ2dlcignY2hhbmdlJyk7XHJcblx0XHRcdFx0XHRcdH1cclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBJbnB1dCBjaGFuZ2UgZXZlbnQgaGFuZGxlcnMuXHJcblx0XHRcdCR0aGlzLm9uKCdjaGFuZ2UnLCAnaW5wdXQ6aGlkZGVuLCBpbnB1dDp0ZXh0LCBzZWxlY3QsIHRleHRhcmVhJywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0aWYgKCQodGhpcykuZGF0YSgnbXVsdGlsYW5ndWFnZScpICE9PSB1bmRlZmluZWQpIHtcclxuXHRcdFx0XHRcdHZhciBzZWxlY3RlZExhbmd1YWdlQ29kZSA9ICQoJy5sYW5ndWFnZXMgYS5hY3RpdmUnKS5kYXRhKCdjb2RlJyk7XHJcblx0XHRcdFx0XHRtb2R1bGUubW9kZWwuZm9ybURhdGFbJCh0aGlzKS5hdHRyKCduYW1lJyldW3NlbGVjdGVkTGFuZ3VhZ2VDb2RlXSA9ICQodGhpcykudmFsKCk7XHJcblx0XHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRcdG1vZHVsZS5tb2RlbC5mb3JtRGF0YVskKHRoaXMpLmF0dHIoJ25hbWUnKV0gPSAkKHRoaXMpLnZhbCgpO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCAnLnN3aXRjaGVyJywgZnVuY3Rpb24oKSB7XHJcblx0XHRcdFx0bW9kdWxlLm1vZGVsLmZvcm1EYXRhWyQodGhpcykuZmluZCgnaW5wdXQ6Y2hlY2tib3gnKS5hdHRyKCduYW1lJyldID0gJCh0aGlzKS5oYXNDbGFzcygnY2hlY2tlZCcpO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIENLRWRpdG9yIGNoYW5nZSBldmVudCBoYW5kbGVyLiBcclxuXHRcdFx0Zm9yICh2YXIgaSBpbiBDS0VESVRPUi5pbnN0YW5jZXMpIHtcclxuXHRcdFx0XHRDS0VESVRPUi5pbnN0YW5jZXNbaV0ub24oJ2NoYW5nZScsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0Q0tFRElUT1IuaW5zdGFuY2VzW2ldLnVwZGF0ZUVsZW1lbnQoKTtcclxuXHRcdFx0XHRcdCQoJ1tuYW1lPVwiJyArIGkgKyAnXCJdJykudHJpZ2dlcignY2hhbmdlJyk7XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdC8vIFNlbGVjdCBhY3RpdmUgbGFuZ3VhZ2UuXHJcblx0XHRcdCQoJy5sYW5ndWFnZXMnKS5maW5kKCcuYWN0aXZlJykuY2xpY2soKTtcclxuXHJcblx0XHRcdC8vIFNldCB0aGUgY29sb3ItcHJldmlldyBjb2xvcnMuXHJcblx0XHRcdCR0aGlzLmZpbmQoJy5jb2xvci1wcmV2aWV3JykuZWFjaChmdW5jdGlvbigpIHtcclxuXHRcdFx0XHQkKHRoaXMpLmNzcygnYmFja2dyb3VuZC1jb2xvcicsICQodGhpcykuc2libGluZ3MoJ2lucHV0OmhpZGRlbicpLnZhbCgpKTsgXHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0ZG9uZSgpO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0cmV0dXJuIG1vZHVsZTtcclxuXHR9KTsiXX0=
