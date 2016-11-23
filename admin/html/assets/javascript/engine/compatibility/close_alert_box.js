'use strict';

/* --------------------------------------------------------------
 close_alert_box.js 2016-08-25 
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Close Alert Box
 *
 * This module will hide an alert box by clicking a button with the class "close".
 *
 * @module Compatibility/close_alert_box
 */
gx.compatibility.module('close_alert_box', ['user_configuration_service'],

/**  @lends module:Compatibility/close_alert_box */

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
  * UserConfigurationService Alias
  *
  * @type {object}
  */
	userConfigurationService = jse.libs.user_configuration_service,


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
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		var $createNewWrapper = $('.create-new-wrapper');

		$this.find('button.close').on('click', function () {

			$(this).parent('.alert').hide();

			if (options.user_config_key !== undefined && options.user_config_value !== undefined) {
				userConfigurationService.set({
					data: {
						userId: options.user_id,
						configurationKey: options.user_config_key,
						configurationValue: options.user_config_value
					}
				});
			}

			if ($createNewWrapper.length > 0 && $('.message_stack_container .alert').length - 1 === 0) {
				$createNewWrapper.removeClass('message-stack-active');
			}
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNsb3NlX2FsZXJ0X2JveC5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJ1c2VyQ29uZmlndXJhdGlvblNlcnZpY2UiLCJqc2UiLCJsaWJzIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCIkY3JlYXRlTmV3V3JhcHBlciIsImZpbmQiLCJvbiIsInBhcmVudCIsImhpZGUiLCJ1c2VyX2NvbmZpZ19rZXkiLCJ1bmRlZmluZWQiLCJ1c2VyX2NvbmZpZ192YWx1ZSIsInNldCIsInVzZXJJZCIsInVzZXJfaWQiLCJjb25maWd1cmF0aW9uS2V5IiwiY29uZmlndXJhdGlvblZhbHVlIiwibGVuZ3RoIiwicmVtb3ZlQ2xhc3MiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGlCQURELEVBR0MsQ0FBQyw0QkFBRCxDQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyw0QkFBMkJDLElBQUlDLElBQUosQ0FBU0MsMEJBcEJyQzs7O0FBc0JDOzs7OztBQUtBQyxXQUFVTixFQUFFTyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJOLFFBQW5CLEVBQTZCSCxJQUE3QixDQTNCWDs7O0FBNkJDOzs7OztBQUtBRCxVQUFTLEVBbENWOztBQW9DQTtBQUNBO0FBQ0E7O0FBRUFBLFFBQU9XLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUIsTUFBSUMsb0JBQW9CVixFQUFFLHFCQUFGLENBQXhCOztBQUVBRCxRQUFNWSxJQUFOLENBQVcsY0FBWCxFQUEyQkMsRUFBM0IsQ0FBOEIsT0FBOUIsRUFBdUMsWUFBVzs7QUFFakRaLEtBQUUsSUFBRixFQUFRYSxNQUFSLENBQWUsUUFBZixFQUF5QkMsSUFBekI7O0FBRUEsT0FBSVIsUUFBUVMsZUFBUixLQUE0QkMsU0FBNUIsSUFBeUNWLFFBQVFXLGlCQUFSLEtBQThCRCxTQUEzRSxFQUFzRjtBQUNyRmQsNkJBQXlCZ0IsR0FBekIsQ0FBNkI7QUFDNUJwQixXQUFNO0FBQ0xxQixjQUFRYixRQUFRYyxPQURYO0FBRUxDLHdCQUFrQmYsUUFBUVMsZUFGckI7QUFHTE8sMEJBQW9CaEIsUUFBUVc7QUFIdkI7QUFEc0IsS0FBN0I7QUFPQTs7QUFFRCxPQUFJUCxrQkFBa0JhLE1BQWxCLEdBQTJCLENBQTNCLElBQWlDdkIsRUFBRSxpQ0FBRixFQUFxQ3VCLE1BQXJDLEdBQThDLENBQS9DLEtBQXNELENBQTFGLEVBQTZGO0FBQzVGYixzQkFBa0JjLFdBQWxCLENBQThCLHNCQUE5QjtBQUNBO0FBQ0QsR0FqQkQ7O0FBbUJBZjtBQUNBLEVBdkJEOztBQXlCQSxRQUFPWixNQUFQO0FBQ0EsQ0FqRkYiLCJmaWxlIjoiY2xvc2VfYWxlcnRfYm94LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjbG9zZV9hbGVydF9ib3guanMgMjAxNi0wOC0yNSBcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIENsb3NlIEFsZXJ0IEJveFxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgaGlkZSBhbiBhbGVydCBib3ggYnkgY2xpY2tpbmcgYSBidXR0b24gd2l0aCB0aGUgY2xhc3MgXCJjbG9zZVwiLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9jbG9zZV9hbGVydF9ib3hcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdjbG9zZV9hbGVydF9ib3gnLFxuXHRcblx0Wyd1c2VyX2NvbmZpZ3VyYXRpb25fc2VydmljZSddLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvY2xvc2VfYWxlcnRfYm94ICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogVXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlIEFsaWFzXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0dXNlckNvbmZpZ3VyYXRpb25TZXJ2aWNlID0ganNlLmxpYnMudXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UsXG5cblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHR2YXIgJGNyZWF0ZU5ld1dyYXBwZXIgPSAkKCcuY3JlYXRlLW5ldy13cmFwcGVyJyk7XG5cdFx0XHRcblx0XHRcdCR0aGlzLmZpbmQoJ2J1dHRvbi5jbG9zZScpLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcblx0XHRcdFx0JCh0aGlzKS5wYXJlbnQoJy5hbGVydCcpLmhpZGUoKTtcblxuXHRcdFx0XHRpZiAob3B0aW9ucy51c2VyX2NvbmZpZ19rZXkgIT09IHVuZGVmaW5lZCAmJiBvcHRpb25zLnVzZXJfY29uZmlnX3ZhbHVlICE9PSB1bmRlZmluZWQpIHtcblx0XHRcdFx0XHR1c2VyQ29uZmlndXJhdGlvblNlcnZpY2Uuc2V0KHtcblx0XHRcdFx0XHRcdGRhdGE6IHtcblx0XHRcdFx0XHRcdFx0dXNlcklkOiBvcHRpb25zLnVzZXJfaWQsXG5cdFx0XHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25LZXk6IG9wdGlvbnMudXNlcl9jb25maWdfa2V5LFxuXHRcdFx0XHRcdFx0XHRjb25maWd1cmF0aW9uVmFsdWU6IG9wdGlvbnMudXNlcl9jb25maWdfdmFsdWVcblx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHR9KTtcblx0XHRcdFx0fVxuXG5cdFx0XHRcdGlmICgkY3JlYXRlTmV3V3JhcHBlci5sZW5ndGggPiAwICYmICgkKCcubWVzc2FnZV9zdGFja19jb250YWluZXIgLmFsZXJ0JykubGVuZ3RoIC0gMSkgPT09IDApIHtcblx0XHRcdFx0XHQkY3JlYXRlTmV3V3JhcHBlci5yZW1vdmVDbGFzcygnbWVzc2FnZS1zdGFjay1hY3RpdmUnKTtcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
