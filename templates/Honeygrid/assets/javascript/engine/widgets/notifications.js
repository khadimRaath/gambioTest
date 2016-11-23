'use strict';

/* --------------------------------------------------------------
 notifications.js 2016-06-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Used for hiding the Top-Bar- and the Pop-Up-Notification
 */
gambio.widgets.module('notifications', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    initialMarginTop = '0',
	    defaults = {
		outerWrapperSelector: '#outer-wrapper',
		headerSelector: '#header'
	},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	var _topBarPositioning = function _topBarPositioning() {
		var topBarHeight = $('.topbar-notification').outerHeight();

		topBarHeight += parseInt(initialMarginTop.replace('px', ''));

		$(options.outerWrapperSelector).css('margin-top', topBarHeight + 'px');
	};

	var _hideTopbarNotification = function _hideTopbarNotification(event) {
		event.stopPropagation();

		$.ajax({
			type: 'POST',
			url: 'request_port.php?module=Notification&action=hide_topbar',
			timeout: 5000,
			dataType: 'json',
			context: this,
			data: {},
			success: function success(p_response) {
				$('.topbar-notification').remove();
				$(options.outerWrapperSelector).removeClass('topbar-active');

				if ($(options.headerSelector).css('position') !== 'fixed') {
					$(options.outerWrapperSelector).css('margin-top', initialMarginTop);
				}
			}
		});

		return false;
	};

	var _hidePopUpNotification = function _hidePopUpNotification(event) {
		event.stopPropagation();

		$.ajax({
			type: 'POST',
			url: 'request_port.php?module=Notification&action=hide_popup_notification',
			timeout: 5000,
			dataType: 'json',
			context: this,
			data: {},
			success: function success(p_response) {
				$('.popup-notification').remove();
			}
		});

		return false;
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		initialMarginTop = $(options.outerWrapperSelector).css('margin-top');

		if ($(options.headerSelector).css('position') !== 'fixed') {
			_topBarPositioning();
		}

		$this.on('click', '.hide-topbar-notification', _hideTopbarNotification);
		$this.on('click', '.hide-popup-notification', _hidePopUpNotification);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvbm90aWZpY2F0aW9ucy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImluaXRpYWxNYXJnaW5Ub3AiLCJkZWZhdWx0cyIsIm91dGVyV3JhcHBlclNlbGVjdG9yIiwiaGVhZGVyU2VsZWN0b3IiLCJvcHRpb25zIiwiZXh0ZW5kIiwiX3RvcEJhclBvc2l0aW9uaW5nIiwidG9wQmFySGVpZ2h0Iiwib3V0ZXJIZWlnaHQiLCJwYXJzZUludCIsInJlcGxhY2UiLCJjc3MiLCJfaGlkZVRvcGJhck5vdGlmaWNhdGlvbiIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwiYWpheCIsInR5cGUiLCJ1cmwiLCJ0aW1lb3V0IiwiZGF0YVR5cGUiLCJjb250ZXh0Iiwic3VjY2VzcyIsInBfcmVzcG9uc2UiLCJyZW1vdmUiLCJyZW1vdmVDbGFzcyIsIl9oaWRlUG9wVXBOb3RpZmljYXRpb24iLCJpbml0IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7OztBQUdBQSxPQUFPQyxPQUFQLENBQWVDLE1BQWYsQ0FDQyxlQURELEVBR0MsRUFIRCxFQUtDLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFRjs7QUFFRSxLQUFJQyxRQUFRQyxFQUFFLElBQUYsQ0FBWjtBQUFBLEtBQ0NDLG1CQUFtQixHQURwQjtBQUFBLEtBRUNDLFdBQVc7QUFDVkMsd0JBQXNCLGdCQURaO0FBRVZDLGtCQUFnQjtBQUZOLEVBRlo7QUFBQSxLQU1DQyxVQUFVTCxFQUFFTSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJKLFFBQW5CLEVBQTZCSixJQUE3QixDQU5YO0FBQUEsS0FPQ0QsU0FBUyxFQVBWOztBQVVGOztBQUVFLEtBQUlVLHFCQUFxQixTQUFyQkEsa0JBQXFCLEdBQVc7QUFDbkMsTUFBSUMsZUFBZVIsRUFBRSxzQkFBRixFQUEwQlMsV0FBMUIsRUFBbkI7O0FBRUFELGtCQUFnQkUsU0FBU1QsaUJBQWlCVSxPQUFqQixDQUF5QixJQUF6QixFQUErQixFQUEvQixDQUFULENBQWhCOztBQUVBWCxJQUFFSyxRQUFRRixvQkFBVixFQUFnQ1MsR0FBaEMsQ0FBb0MsWUFBcEMsRUFBa0RKLGVBQWUsSUFBakU7QUFDQSxFQU5EOztBQVFBLEtBQUlLLDBCQUEwQixTQUExQkEsdUJBQTBCLENBQVNDLEtBQVQsRUFBZ0I7QUFDN0NBLFFBQU1DLGVBQU47O0FBRUFmLElBQUVnQixJQUFGLENBQU87QUFDQ0MsU0FBTSxNQURQO0FBRUNDLFFBQUsseURBRk47QUFHQ0MsWUFBUyxJQUhWO0FBSUNDLGFBQVUsTUFKWDtBQUtDQyxZQUFTLElBTFY7QUFNQ3ZCLFNBQU0sRUFOUDtBQU9Dd0IsWUFBUyxpQkFBU0MsVUFBVCxFQUFxQjtBQUM3QnZCLE1BQUUsc0JBQUYsRUFBMEJ3QixNQUExQjtBQUNBeEIsTUFBRUssUUFBUUYsb0JBQVYsRUFBZ0NzQixXQUFoQyxDQUE0QyxlQUE1Qzs7QUFFQSxRQUFJekIsRUFBRUssUUFBUUQsY0FBVixFQUEwQlEsR0FBMUIsQ0FBOEIsVUFBOUIsTUFBOEMsT0FBbEQsRUFBMkQ7QUFDMURaLE9BQUVLLFFBQVFGLG9CQUFWLEVBQWdDUyxHQUFoQyxDQUFvQyxZQUFwQyxFQUFrRFgsZ0JBQWxEO0FBQ0E7QUFDRDtBQWRGLEdBQVA7O0FBaUJBLFNBQU8sS0FBUDtBQUNBLEVBckJEOztBQXVCQSxLQUFJeUIseUJBQXlCLFNBQXpCQSxzQkFBeUIsQ0FBU1osS0FBVCxFQUFnQjtBQUM1Q0EsUUFBTUMsZUFBTjs7QUFFQWYsSUFBRWdCLElBQUYsQ0FBTztBQUNDQyxTQUFNLE1BRFA7QUFFQ0MsUUFBSyxxRUFGTjtBQUdDQyxZQUFTLElBSFY7QUFJQ0MsYUFBVSxNQUpYO0FBS0NDLFlBQVMsSUFMVjtBQU1DdkIsU0FBTSxFQU5QO0FBT0N3QixZQUFTLGlCQUFTQyxVQUFULEVBQXFCO0FBQzdCdkIsTUFBRSxxQkFBRixFQUF5QndCLE1BQXpCO0FBQ0E7QUFURixHQUFQOztBQVlBLFNBQU8sS0FBUDtBQUNBLEVBaEJEOztBQW1CRjs7QUFFRTs7OztBQUlBM0IsUUFBTzhCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCM0IscUJBQW1CRCxFQUFFSyxRQUFRRixvQkFBVixFQUFnQ1MsR0FBaEMsQ0FBb0MsWUFBcEMsQ0FBbkI7O0FBRUEsTUFBSVosRUFBRUssUUFBUUQsY0FBVixFQUEwQlEsR0FBMUIsQ0FBOEIsVUFBOUIsTUFBOEMsT0FBbEQsRUFBMkQ7QUFDMURMO0FBQ0E7O0FBRURSLFFBQU04QixFQUFOLENBQVMsT0FBVCxFQUFrQiwyQkFBbEIsRUFBK0NoQix1QkFBL0M7QUFDQWQsUUFBTThCLEVBQU4sQ0FBUyxPQUFULEVBQWtCLDBCQUFsQixFQUE4Q0gsc0JBQTlDOztBQUVBRTtBQUNBLEVBWkQ7O0FBY0E7QUFDQSxRQUFPL0IsTUFBUDtBQUNBLENBL0ZGIiwiZmlsZSI6IndpZGdldHMvbm90aWZpY2F0aW9ucy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gbm90aWZpY2F0aW9ucy5qcyAyMDE2LTA2LTA3XG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBVc2VkIGZvciBoaWRpbmcgdGhlIFRvcC1CYXItIGFuZCB0aGUgUG9wLVVwLU5vdGlmaWNhdGlvblxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoXG5cdCdub3RpZmljYXRpb25zJyxcblxuXHRbXSxcblxuXHRmdW5jdGlvbihkYXRhKSB7XG5cblx0XHQndXNlIHN0cmljdCc7XG5cbi8vICMjIyMjIyMjIyMgVkFSSUFCTEUgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdFx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRcdGluaXRpYWxNYXJnaW5Ub3AgPSAnMCcsXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0b3V0ZXJXcmFwcGVyU2VsZWN0b3I6ICcjb3V0ZXItd3JhcHBlcicsXG5cdFx0XHRcdGhlYWRlclNlbGVjdG9yOiAnI2hlYWRlcidcblx0XHRcdH0sXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgRVZFTlQgSEFORExFUiAjIyMjIyMjIyMjXG5cblx0XHR2YXIgX3RvcEJhclBvc2l0aW9uaW5nID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgdG9wQmFySGVpZ2h0ID0gJCgnLnRvcGJhci1ub3RpZmljYXRpb24nKS5vdXRlckhlaWdodCgpO1xuXHRcdFx0XG5cdFx0XHR0b3BCYXJIZWlnaHQgKz0gcGFyc2VJbnQoaW5pdGlhbE1hcmdpblRvcC5yZXBsYWNlKCdweCcsICcnKSk7XG5cdFx0XHRcblx0XHRcdCQob3B0aW9ucy5vdXRlcldyYXBwZXJTZWxlY3RvcikuY3NzKCdtYXJnaW4tdG9wJywgdG9wQmFySGVpZ2h0ICsgJ3B4Jyk7XG5cdFx0fTtcblxuXHRcdHZhciBfaGlkZVRvcGJhck5vdGlmaWNhdGlvbiA9IGZ1bmN0aW9uKGV2ZW50KSB7XG5cdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcblxuXHRcdFx0JC5hamF4KHtcblx0XHRcdFx0ICAgICAgIHR5cGU6ICdQT1NUJyxcblx0XHRcdFx0ICAgICAgIHVybDogJ3JlcXVlc3RfcG9ydC5waHA/bW9kdWxlPU5vdGlmaWNhdGlvbiZhY3Rpb249aGlkZV90b3BiYXInLFxuXHRcdFx0XHQgICAgICAgdGltZW91dDogNTAwMCxcblx0XHRcdFx0ICAgICAgIGRhdGFUeXBlOiAnanNvbicsXG5cdFx0XHRcdCAgICAgICBjb250ZXh0OiB0aGlzLFxuXHRcdFx0XHQgICAgICAgZGF0YToge30sXG5cdFx0XHRcdCAgICAgICBzdWNjZXNzOiBmdW5jdGlvbihwX3Jlc3BvbnNlKSB7XG5cdFx0XHRcdFx0ICAgICAgICQoJy50b3BiYXItbm90aWZpY2F0aW9uJykucmVtb3ZlKCk7XG5cdFx0XHRcdFx0ICAgICAgICQob3B0aW9ucy5vdXRlcldyYXBwZXJTZWxlY3RvcikucmVtb3ZlQ2xhc3MoJ3RvcGJhci1hY3RpdmUnKTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQgICAgICAgaWYgKCQob3B0aW9ucy5oZWFkZXJTZWxlY3RvcikuY3NzKCdwb3NpdGlvbicpICE9PSAnZml4ZWQnKSB7XG5cdFx0XHRcdFx0XHQgICAgICAgJChvcHRpb25zLm91dGVyV3JhcHBlclNlbGVjdG9yKS5jc3MoJ21hcmdpbi10b3AnLCBpbml0aWFsTWFyZ2luVG9wKTtcblx0XHRcdFx0XHQgICAgICAgfVxuXHRcdFx0XHQgICAgICAgfVxuXHRcdFx0ICAgICAgIH0pO1xuXG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblxuXHRcdHZhciBfaGlkZVBvcFVwTm90aWZpY2F0aW9uID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdGV2ZW50LnN0b3BQcm9wYWdhdGlvbigpO1xuXG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHQgICAgICAgdHlwZTogJ1BPU1QnLFxuXHRcdFx0XHQgICAgICAgdXJsOiAncmVxdWVzdF9wb3J0LnBocD9tb2R1bGU9Tm90aWZpY2F0aW9uJmFjdGlvbj1oaWRlX3BvcHVwX25vdGlmaWNhdGlvbicsXG5cdFx0XHRcdCAgICAgICB0aW1lb3V0OiA1MDAwLFxuXHRcdFx0XHQgICAgICAgZGF0YVR5cGU6ICdqc29uJyxcblx0XHRcdFx0ICAgICAgIGNvbnRleHQ6IHRoaXMsXG5cdFx0XHRcdCAgICAgICBkYXRhOiB7fSxcblx0XHRcdFx0ICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKHBfcmVzcG9uc2UpIHtcblx0XHRcdFx0XHQgICAgICAgJCgnLnBvcHVwLW5vdGlmaWNhdGlvbicpLnJlbW92ZSgpO1xuXHRcdFx0XHQgICAgICAgfVxuXHRcdFx0ICAgICAgIH0pO1xuXG5cdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0fTtcblxuXG4vLyAjIyMjIyMjIyMjIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHRcdC8qKlxuXHRcdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHRcdCAqIEBjb25zdHJ1Y3RvclxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHRpbml0aWFsTWFyZ2luVG9wID0gJChvcHRpb25zLm91dGVyV3JhcHBlclNlbGVjdG9yKS5jc3MoJ21hcmdpbi10b3AnKTtcblx0XHRcdFxuXHRcdFx0aWYgKCQob3B0aW9ucy5oZWFkZXJTZWxlY3RvcikuY3NzKCdwb3NpdGlvbicpICE9PSAnZml4ZWQnKSB7XG5cdFx0XHRcdF90b3BCYXJQb3NpdGlvbmluZygpO1xuXHRcdFx0fVxuXG5cdFx0XHQkdGhpcy5vbignY2xpY2snLCAnLmhpZGUtdG9wYmFyLW5vdGlmaWNhdGlvbicsIF9oaWRlVG9wYmFyTm90aWZpY2F0aW9uKTtcblx0XHRcdCR0aGlzLm9uKCdjbGljaycsICcuaGlkZS1wb3B1cC1ub3RpZmljYXRpb24nLCBfaGlkZVBvcFVwTm90aWZpY2F0aW9uKTtcblxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cblx0XHQvLyBSZXR1cm4gZGF0YSB0byB3aWRnZXQgZW5naW5lXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7Il19
