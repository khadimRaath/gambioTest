'use strict';

/* --------------------------------------------------------------
 menu_trigger.js 2016-04-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Menu Trigger Controller
 *
 * This controller will handle the main menu trigger. Provide the "data-menu_trigger-menu-selector" attribute
 * that must select the main menu. It also works with the "user_configuration_service" so the user ID is required.
 * Provide it with the "data-menu_trigger-customer-id" attribute.
 *
 * There are three states for the main menu: "collapse", "expand" and "expand-all".
 */
gx.controllers.module('menu_trigger', ['user_configuration_service'], function (data) {

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
  * Main Menu Selector
  *
  * @type {jQuery}
  */
	var $menu = $(data.menuSelector);

	/**
  * Menu Items List Selector
  *
  * @type {jQuery}
  */
	var $list = $menu.find('nav > ul');

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Set the menu state.
  *
  * This function will update the UI and save the state in the users_configuration db table.
  *
  * @param {String} state Accepts the "collapse", "expand" and "expandAll".
  * @param {Boolean} save Optional (false), whether to save the change with the user configuration service.
  */
	function _setMenuState(state) {
		var save = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;

		var stateClass = '';

		switch (state) {
			case 'collapse':
			case 'expand':
				stateClass = state;
				break;

			case 'expandAll':
				stateClass = 'expand-all';
				break;

			default:
				stateClass = 'expand';
		}

		var $radio = $this.find('input:radio#menu-' + stateClass);

		// Set the class to the <ul> element of the main menu.
		$list.attr('class', stateClass);

		// Make sure the correct radio is selected.
		$radio.prop('checked', true);

		// Display the next-state icons.
		$radio.prev('label').hide();
		if ($radio.next('label').length > 0) {
			$radio.next('label').show();
		} else {
			$this.find('label:first').show();
		}

		// Save the configuration setting.
		if (save) {
			jse.libs.user_configuration_service.set({
				data: {
					userId: data.customerId,
					configurationKey: 'menuVisibility',
					configurationValue: state
				},
				onSuccess: function onSuccess(response) {
					// Trigger a window resize in order to update the position of other UI elements.
					$(window).trigger('resize');
				}
			});
		}
	}

	/**
  * Handles the radio buttons change.
  *
  * This is triggered by the click on the menu trigger button.
  */
	function _onInputRadioChange() {
		_setMenuState($(this).val(), true);
	}

	// ------------------------------------------------------------------------
	// INITIALIZE
	// ------------------------------------------------------------------------

	module.init = function (done) {
		_setMenuState(data.menuVisibility);

		$this.on('change', 'input:radio', _onInputRadioChange);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9wYXJ0aWFsL21lbnVfdHJpZ2dlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRtZW51IiwibWVudVNlbGVjdG9yIiwiJGxpc3QiLCJmaW5kIiwiX3NldE1lbnVTdGF0ZSIsInN0YXRlIiwic2F2ZSIsInN0YXRlQ2xhc3MiLCIkcmFkaW8iLCJhdHRyIiwicHJvcCIsInByZXYiLCJoaWRlIiwibmV4dCIsImxlbmd0aCIsInNob3ciLCJqc2UiLCJsaWJzIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCJzZXQiLCJ1c2VySWQiLCJjdXN0b21lcklkIiwiY29uZmlndXJhdGlvbktleSIsImNvbmZpZ3VyYXRpb25WYWx1ZSIsIm9uU3VjY2VzcyIsInJlc3BvbnNlIiwid2luZG93IiwidHJpZ2dlciIsIl9vbklucHV0UmFkaW9DaGFuZ2UiLCJ2YWwiLCJpbml0IiwiZG9uZSIsIm1lbnVWaXNpYmlsaXR5Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7O0FBU0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixjQUF0QixFQUFzQyxDQUFDLDRCQUFELENBQXRDLEVBQXNFLFVBQVNDLElBQVQsRUFBZTs7QUFFcEY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNQyxRQUFRRCxFQUFFRixLQUFLSSxZQUFQLENBQWQ7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsUUFBUUYsTUFBTUcsSUFBTixDQUFXLFVBQVgsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNUCxTQUFTLEVBQWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7OztBQVFBLFVBQVNRLGFBQVQsQ0FBdUJDLEtBQXZCLEVBQTRDO0FBQUEsTUFBZEMsSUFBYyx1RUFBUCxLQUFPOztBQUMzQyxNQUFJQyxhQUFhLEVBQWpCOztBQUVBLFVBQVFGLEtBQVI7QUFDQyxRQUFLLFVBQUw7QUFDQSxRQUFLLFFBQUw7QUFDQ0UsaUJBQWFGLEtBQWI7QUFDQTs7QUFFRCxRQUFLLFdBQUw7QUFDQ0UsaUJBQWEsWUFBYjtBQUNBOztBQUVEO0FBQ0NBLGlCQUFhLFFBQWI7QUFYRjs7QUFjQSxNQUFNQyxTQUFTVixNQUFNSyxJQUFOLENBQVcsc0JBQXNCSSxVQUFqQyxDQUFmOztBQUVBO0FBQ0FMLFFBQU1PLElBQU4sQ0FBVyxPQUFYLEVBQW9CRixVQUFwQjs7QUFFQTtBQUNBQyxTQUFPRSxJQUFQLENBQVksU0FBWixFQUF1QixJQUF2Qjs7QUFFQTtBQUNBRixTQUFPRyxJQUFQLENBQVksT0FBWixFQUFxQkMsSUFBckI7QUFDQSxNQUFJSixPQUFPSyxJQUFQLENBQVksT0FBWixFQUFxQkMsTUFBckIsR0FBOEIsQ0FBbEMsRUFBcUM7QUFDcENOLFVBQU9LLElBQVAsQ0FBWSxPQUFaLEVBQXFCRSxJQUFyQjtBQUNBLEdBRkQsTUFFTztBQUNOakIsU0FBTUssSUFBTixDQUFXLGFBQVgsRUFBMEJZLElBQTFCO0FBQ0E7O0FBRUQ7QUFDQSxNQUFJVCxJQUFKLEVBQVU7QUFDVFUsT0FBSUMsSUFBSixDQUFTQywwQkFBVCxDQUFvQ0MsR0FBcEMsQ0FBd0M7QUFDdkN0QixVQUFNO0FBQ0x1QixhQUFRdkIsS0FBS3dCLFVBRFI7QUFFTEMsdUJBQWtCLGdCQUZiO0FBR0xDLHlCQUFvQmxCO0FBSGYsS0FEaUM7QUFNdkNtQixlQUFXLG1CQUFTQyxRQUFULEVBQW1CO0FBQzdCO0FBQ0ExQixPQUFFMkIsTUFBRixFQUFVQyxPQUFWLENBQWtCLFFBQWxCO0FBQ0E7QUFUc0MsSUFBeEM7QUFXQTtBQUNEOztBQUVEOzs7OztBQUtBLFVBQVNDLG1CQUFULEdBQStCO0FBQzlCeEIsZ0JBQWNMLEVBQUUsSUFBRixFQUFROEIsR0FBUixFQUFkLEVBQTZCLElBQTdCO0FBQ0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBakMsUUFBT2tDLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUIzQixnQkFBY1AsS0FBS21DLGNBQW5COztBQUVBbEMsUUFBTW1DLEVBQU4sQ0FBUyxRQUFULEVBQW1CLGFBQW5CLEVBQWtDTCxtQkFBbEM7O0FBRUFHO0FBQ0EsRUFORDs7QUFRQSxRQUFPbkMsTUFBUDtBQUVBLENBeEhEIiwiZmlsZSI6ImxheW91dHMvbWFpbi9wYXJ0aWFsL21lbnVfdHJpZ2dlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBtZW51X3RyaWdnZXIuanMgMjAxNi0wNC0yMlxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBNZW51IFRyaWdnZXIgQ29udHJvbGxlclxyXG4gKlxyXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCBoYW5kbGUgdGhlIG1haW4gbWVudSB0cmlnZ2VyLiBQcm92aWRlIHRoZSBcImRhdGEtbWVudV90cmlnZ2VyLW1lbnUtc2VsZWN0b3JcIiBhdHRyaWJ1dGVcclxuICogdGhhdCBtdXN0IHNlbGVjdCB0aGUgbWFpbiBtZW51LiBJdCBhbHNvIHdvcmtzIHdpdGggdGhlIFwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2VcIiBzbyB0aGUgdXNlciBJRCBpcyByZXF1aXJlZC5cclxuICogUHJvdmlkZSBpdCB3aXRoIHRoZSBcImRhdGEtbWVudV90cmlnZ2VyLWN1c3RvbWVyLWlkXCIgYXR0cmlidXRlLlxyXG4gKlxyXG4gKiBUaGVyZSBhcmUgdGhyZWUgc3RhdGVzIGZvciB0aGUgbWFpbiBtZW51OiBcImNvbGxhcHNlXCIsIFwiZXhwYW5kXCIgYW5kIFwiZXhwYW5kLWFsbFwiLlxyXG4gKi9cclxuZ3guY29udHJvbGxlcnMubW9kdWxlKCdtZW51X3RyaWdnZXInLCBbJ3VzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlJ10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNYWluIE1lbnUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJG1lbnUgPSAkKGRhdGEubWVudVNlbGVjdG9yKTtcclxuXHRcclxuXHQvKipcclxuXHQgKiBNZW51IEl0ZW1zIExpc3QgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJGxpc3QgPSAkbWVudS5maW5kKCduYXYgPiB1bCcpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBGVU5DVElPTlNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBTZXQgdGhlIG1lbnUgc3RhdGUuXHJcblx0ICpcclxuXHQgKiBUaGlzIGZ1bmN0aW9uIHdpbGwgdXBkYXRlIHRoZSBVSSBhbmQgc2F2ZSB0aGUgc3RhdGUgaW4gdGhlIHVzZXJzX2NvbmZpZ3VyYXRpb24gZGIgdGFibGUuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge1N0cmluZ30gc3RhdGUgQWNjZXB0cyB0aGUgXCJjb2xsYXBzZVwiLCBcImV4cGFuZFwiIGFuZCBcImV4cGFuZEFsbFwiLlxyXG5cdCAqIEBwYXJhbSB7Qm9vbGVhbn0gc2F2ZSBPcHRpb25hbCAoZmFsc2UpLCB3aGV0aGVyIHRvIHNhdmUgdGhlIGNoYW5nZSB3aXRoIHRoZSB1c2VyIGNvbmZpZ3VyYXRpb24gc2VydmljZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfc2V0TWVudVN0YXRlKHN0YXRlLCBzYXZlID0gZmFsc2UpIHtcclxuXHRcdGxldCBzdGF0ZUNsYXNzID0gJyc7XHJcblx0XHRcclxuXHRcdHN3aXRjaCAoc3RhdGUpIHtcclxuXHRcdFx0Y2FzZSAnY29sbGFwc2UnOlxyXG5cdFx0XHRjYXNlICdleHBhbmQnOlxyXG5cdFx0XHRcdHN0YXRlQ2xhc3MgPSBzdGF0ZTtcclxuXHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHJcblx0XHRcdGNhc2UgJ2V4cGFuZEFsbCc6XHJcblx0XHRcdFx0c3RhdGVDbGFzcyA9ICdleHBhbmQtYWxsJztcclxuXHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHJcblx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0c3RhdGVDbGFzcyA9ICdleHBhbmQnO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRjb25zdCAkcmFkaW8gPSAkdGhpcy5maW5kKCdpbnB1dDpyYWRpbyNtZW51LScgKyBzdGF0ZUNsYXNzKTtcclxuXHRcdFxyXG5cdFx0Ly8gU2V0IHRoZSBjbGFzcyB0byB0aGUgPHVsPiBlbGVtZW50IG9mIHRoZSBtYWluIG1lbnUuXHJcblx0XHQkbGlzdC5hdHRyKCdjbGFzcycsIHN0YXRlQ2xhc3MpO1xyXG5cdFx0XHJcblx0XHQvLyBNYWtlIHN1cmUgdGhlIGNvcnJlY3QgcmFkaW8gaXMgc2VsZWN0ZWQuXHJcblx0XHQkcmFkaW8ucHJvcCgnY2hlY2tlZCcsIHRydWUpO1xyXG5cdFx0XHJcblx0XHQvLyBEaXNwbGF5IHRoZSBuZXh0LXN0YXRlIGljb25zLlxyXG5cdFx0JHJhZGlvLnByZXYoJ2xhYmVsJykuaGlkZSgpO1xyXG5cdFx0aWYgKCRyYWRpby5uZXh0KCdsYWJlbCcpLmxlbmd0aCA+IDApIHtcclxuXHRcdFx0JHJhZGlvLm5leHQoJ2xhYmVsJykuc2hvdygpO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0JHRoaXMuZmluZCgnbGFiZWw6Zmlyc3QnKS5zaG93KCk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8vIFNhdmUgdGhlIGNvbmZpZ3VyYXRpb24gc2V0dGluZy5cclxuXHRcdGlmIChzYXZlKSB7XHJcblx0XHRcdGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLnNldCh7XHJcblx0XHRcdFx0ZGF0YToge1xyXG5cdFx0XHRcdFx0dXNlcklkOiBkYXRhLmN1c3RvbWVySWQsXHJcblx0XHRcdFx0XHRjb25maWd1cmF0aW9uS2V5OiAnbWVudVZpc2liaWxpdHknLFxyXG5cdFx0XHRcdFx0Y29uZmlndXJhdGlvblZhbHVlOiBzdGF0ZVxyXG5cdFx0XHRcdH0sXHJcblx0XHRcdFx0b25TdWNjZXNzOiBmdW5jdGlvbihyZXNwb25zZSkge1xyXG5cdFx0XHRcdFx0Ly8gVHJpZ2dlciBhIHdpbmRvdyByZXNpemUgaW4gb3JkZXIgdG8gdXBkYXRlIHRoZSBwb3NpdGlvbiBvZiBvdGhlciBVSSBlbGVtZW50cy5cclxuXHRcdFx0XHRcdCQod2luZG93KS50cmlnZ2VyKCdyZXNpemUnKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBIYW5kbGVzIHRoZSByYWRpbyBidXR0b25zIGNoYW5nZS5cclxuXHQgKlxyXG5cdCAqIFRoaXMgaXMgdHJpZ2dlcmVkIGJ5IHRoZSBjbGljayBvbiB0aGUgbWVudSB0cmlnZ2VyIGJ1dHRvbi5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25JbnB1dFJhZGlvQ2hhbmdlKCkge1xyXG5cdFx0X3NldE1lbnVTdGF0ZSgkKHRoaXMpLnZhbCgpLCB0cnVlKTtcclxuXHR9XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSU5JVElBTElaRVxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0X3NldE1lbnVTdGF0ZShkYXRhLm1lbnVWaXNpYmlsaXR5KTtcclxuXHRcdFxyXG5cdFx0JHRoaXMub24oJ2NoYW5nZScsICdpbnB1dDpyYWRpbycsIF9vbklucHV0UmFkaW9DaGFuZ2UpO1xyXG5cdFx0XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG5cdFxyXG59KTsiXX0=
