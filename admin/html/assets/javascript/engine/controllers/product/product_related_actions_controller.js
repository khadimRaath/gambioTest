'use strict';

/* --------------------------------------------------------------
 product_related_actions_controller.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Related Actions Controller
 *
 * This controller contains the mapping logic of the products properties/attributes/special buttons.
 *
 * @module Controllers/product_related_actions_controller
 */
gx.controllers.module('product_related_actions_controller', [gx.source + '/libs/button_dropdown'],

/** @lends module:Controllers/product_related_actions_controller */

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
	defaults = {
		'properties_url': '',
		'attributes_url': '',
		'specials_url': '',
		'product_id': '',
		'c_path': '',
		'recent_button': 'BUTTON_SPECIAL'
	},


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
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Map actions to buttons.
  *
  * @private
  */
	var _setActions = function _setActions() {
		var actions = [];

		actions.BUTTON_SPECIAL = _setSpecialPriceActionCallback;
		actions.BUTTON_PROPERTIES = _setPropertiesActionCallback;
		actions.BUTTON_ATTRIBUTES = _setAttributesActionCallback;

		if (options.attributes_url === '' && options.recent_button === 'BUTTON_ATTRIBUTES') {
			options.recent_button = defaults.recent_button;
		}

		jse.libs.button_dropdown.mapAction($this, 'BUTTON_SPECIAL', 'admin_buttons', _setSpecialPriceActionCallback);
		jse.libs.button_dropdown.mapAction($this, 'BUTTON_PROPERTIES', 'admin_buttons', _setPropertiesActionCallback);

		if (options.attributes_url !== '') {
			jse.libs.button_dropdown.mapAction($this, 'BUTTON_ATTRIBUTES', 'admin_buttons', _setAttributesActionCallback);
		}
	};

	/**
  * Redirect to special pricing page.
  *
  * @returns {boolean}
  *
  * @private
  */
	var _setSpecialPriceActionCallback = function _setSpecialPriceActionCallback() {

		if (options.specials_url !== '') {
			window.location.href = options.specials_url;

			return true;
		}

		return false;
	};

	/**
  * Redirect to properties page.
  *
  * @returns {boolean}
  * @private
  */
	var _setPropertiesActionCallback = function _setPropertiesActionCallback() {

		if (options.properties_url !== '') {
			window.location.href = options.properties_url;

			return true;
		}

		return false;
	};

	/**
  * Redirect to attributes page.
  *
  * @returns {boolean}
  *
  * @private
  */
	var _setAttributesActionCallback = function _setAttributesActionCallback() {

		if (options.attributes_url !== '' && options.product_id !== '') {
			var $form = $('<form action="' + options.attributes_url + '" method="post">' + '<input type="hidden" name="action" value="edit" />' + '<input type="hidden" name="current_product_id" value="' + options.product_id + '" />' + '<input type="hidden" name="cpath" value="' + options.c_path + '" />' + '</form>');

			$('body').prepend($form);

			$form.submit();

			return true;
		}

		return false;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		window.setTimeout(_setActions, 300);
		done(); // Finish it
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInByb2R1Y3QvcHJvZHVjdF9yZWxhdGVkX2FjdGlvbnNfY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9zZXRBY3Rpb25zIiwiYWN0aW9ucyIsIkJVVFRPTl9TUEVDSUFMIiwiX3NldFNwZWNpYWxQcmljZUFjdGlvbkNhbGxiYWNrIiwiQlVUVE9OX1BST1BFUlRJRVMiLCJfc2V0UHJvcGVydGllc0FjdGlvbkNhbGxiYWNrIiwiQlVUVE9OX0FUVFJJQlVURVMiLCJfc2V0QXR0cmlidXRlc0FjdGlvbkNhbGxiYWNrIiwiYXR0cmlidXRlc191cmwiLCJyZWNlbnRfYnV0dG9uIiwianNlIiwibGlicyIsImJ1dHRvbl9kcm9wZG93biIsIm1hcEFjdGlvbiIsInNwZWNpYWxzX3VybCIsIndpbmRvdyIsImxvY2F0aW9uIiwiaHJlZiIsInByb3BlcnRpZXNfdXJsIiwicHJvZHVjdF9pZCIsIiRmb3JtIiwiY19wYXRoIiwicHJlcGVuZCIsInN1Ym1pdCIsImluaXQiLCJkb25lIiwic2V0VGltZW91dCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUNDLG9DQURELEVBR0MsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHVCQURiLENBSEQ7O0FBT0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVc7QUFDVixvQkFBa0IsRUFEUjtBQUVWLG9CQUFrQixFQUZSO0FBR1Ysa0JBQWdCLEVBSE47QUFJVixnQkFBYyxFQUpKO0FBS1YsWUFBVSxFQUxBO0FBTVYsbUJBQWlCO0FBTlAsRUFiWjs7O0FBc0JDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQTNCWDs7O0FBNkJDOzs7OztBQUtBRixVQUFTLEVBbENWOztBQW9DQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7O0FBS0EsS0FBSVEsY0FBYyxTQUFkQSxXQUFjLEdBQVc7QUFDNUIsTUFBSUMsVUFBVSxFQUFkOztBQUVBQSxVQUFRQyxjQUFSLEdBQXlCQyw4QkFBekI7QUFDQUYsVUFBUUcsaUJBQVIsR0FBNEJDLDRCQUE1QjtBQUNBSixVQUFRSyxpQkFBUixHQUE0QkMsNEJBQTVCOztBQUVBLE1BQUlULFFBQVFVLGNBQVIsS0FBMkIsRUFBM0IsSUFBaUNWLFFBQVFXLGFBQVIsS0FBMEIsbUJBQS9ELEVBQW9GO0FBQ25GWCxXQUFRVyxhQUFSLEdBQXdCWixTQUFTWSxhQUFqQztBQUNBOztBQUVEQyxNQUFJQyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJDLFNBQXpCLENBQW1DbEIsS0FBbkMsRUFBMEMsZ0JBQTFDLEVBQTRELGVBQTVELEVBQ0NRLDhCQUREO0FBRUFPLE1BQUlDLElBQUosQ0FBU0MsZUFBVCxDQUF5QkMsU0FBekIsQ0FBbUNsQixLQUFuQyxFQUEwQyxtQkFBMUMsRUFBK0QsZUFBL0QsRUFDQ1UsNEJBREQ7O0FBR0EsTUFBSVAsUUFBUVUsY0FBUixLQUEyQixFQUEvQixFQUFtQztBQUNsQ0UsT0FBSUMsSUFBSixDQUFTQyxlQUFULENBQXlCQyxTQUF6QixDQUFtQ2xCLEtBQW5DLEVBQTBDLG1CQUExQyxFQUErRCxlQUEvRCxFQUNDWSw0QkFERDtBQUVBO0FBQ0QsRUFwQkQ7O0FBc0JBOzs7Ozs7O0FBT0EsS0FBSUosaUNBQWlDLFNBQWpDQSw4QkFBaUMsR0FBVzs7QUFFL0MsTUFBSUwsUUFBUWdCLFlBQVIsS0FBeUIsRUFBN0IsRUFBaUM7QUFDaENDLFVBQU9DLFFBQVAsQ0FBZ0JDLElBQWhCLEdBQXVCbkIsUUFBUWdCLFlBQS9COztBQUVBLFVBQU8sSUFBUDtBQUNBOztBQUVELFNBQU8sS0FBUDtBQUNBLEVBVEQ7O0FBV0E7Ozs7OztBQU1BLEtBQUlULCtCQUErQixTQUEvQkEsNEJBQStCLEdBQVc7O0FBRTdDLE1BQUlQLFFBQVFvQixjQUFSLEtBQTJCLEVBQS9CLEVBQW1DO0FBQ2xDSCxVQUFPQyxRQUFQLENBQWdCQyxJQUFoQixHQUF1Qm5CLFFBQVFvQixjQUEvQjs7QUFFQSxVQUFPLElBQVA7QUFDQTs7QUFFRCxTQUFPLEtBQVA7QUFDQSxFQVREOztBQVdBOzs7Ozs7O0FBT0EsS0FBSVgsK0JBQStCLFNBQS9CQSw0QkFBK0IsR0FBVzs7QUFFN0MsTUFBSVQsUUFBUVUsY0FBUixLQUEyQixFQUEzQixJQUFpQ1YsUUFBUXFCLFVBQVIsS0FBdUIsRUFBNUQsRUFBZ0U7QUFDL0QsT0FBSUMsUUFBUXhCLEVBQUUsbUJBQW1CRSxRQUFRVSxjQUEzQixHQUE0QyxrQkFBNUMsR0FDYixvREFEYSxHQUViLHdEQUZhLEdBRThDVixRQUFRcUIsVUFGdEQsR0FFbUUsTUFGbkUsR0FHYiwyQ0FIYSxHQUdpQ3JCLFFBQVF1QixNQUh6QyxHQUdrRCxNQUhsRCxHQUliLFNBSlcsQ0FBWjs7QUFNQXpCLEtBQUUsTUFBRixFQUFVMEIsT0FBVixDQUFrQkYsS0FBbEI7O0FBRUFBLFNBQU1HLE1BQU47O0FBRUEsVUFBTyxJQUFQO0FBQ0E7O0FBRUQsU0FBTyxLQUFQO0FBQ0EsRUFqQkQ7O0FBbUJBO0FBQ0E7QUFDQTs7QUFFQS9CLFFBQU9nQyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCVixTQUFPVyxVQUFQLENBQWtCMUIsV0FBbEIsRUFBK0IsR0FBL0I7QUFDQXlCLFNBRjRCLENBRXBCO0FBQ1IsRUFIRDs7QUFLQSxRQUFPakMsTUFBUDtBQUNBLENBM0pGIiwiZmlsZSI6InByb2R1Y3QvcHJvZHVjdF9yZWxhdGVkX2FjdGlvbnNfY29udHJvbGxlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBwcm9kdWN0X3JlbGF0ZWRfYWN0aW9uc19jb250cm9sbGVyLmpzIDIwMTUtMTAtMTUgZ21cclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgUmVsYXRlZCBBY3Rpb25zIENvbnRyb2xsZXJcclxuICpcclxuICogVGhpcyBjb250cm9sbGVyIGNvbnRhaW5zIHRoZSBtYXBwaW5nIGxvZ2ljIG9mIHRoZSBwcm9kdWN0cyBwcm9wZXJ0aWVzL2F0dHJpYnV0ZXMvc3BlY2lhbCBidXR0b25zLlxyXG4gKlxyXG4gKiBAbW9kdWxlIENvbnRyb2xsZXJzL3Byb2R1Y3RfcmVsYXRlZF9hY3Rpb25zX2NvbnRyb2xsZXJcclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcclxuXHQncHJvZHVjdF9yZWxhdGVkX2FjdGlvbnNfY29udHJvbGxlcicsXHJcblx0XHJcblx0W1xyXG5cdFx0Z3guc291cmNlICsgJy9saWJzL2J1dHRvbl9kcm9wZG93bidcclxuXHRdLFxyXG5cdFxyXG5cdC8qKiBAbGVuZHMgbW9kdWxlOkNvbnRyb2xsZXJzL3Byb2R1Y3RfcmVsYXRlZF9hY3Rpb25zX2NvbnRyb2xsZXIgKi9cclxuXHRcclxuXHRmdW5jdGlvbihkYXRhKSB7XHJcblx0XHRcclxuXHRcdCd1c2Ugc3RyaWN0JztcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdHZhclxyXG5cdFx0XHQvKipcclxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEB2YXIge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdGRlZmF1bHRzID0ge1xyXG5cdFx0XHRcdCdwcm9wZXJ0aWVzX3VybCc6ICcnLFxyXG5cdFx0XHRcdCdhdHRyaWJ1dGVzX3VybCc6ICcnLFxyXG5cdFx0XHRcdCdzcGVjaWFsc191cmwnOiAnJyxcclxuXHRcdFx0XHQncHJvZHVjdF9pZCc6ICcnLFxyXG5cdFx0XHRcdCdjX3BhdGgnOiAnJyxcclxuXHRcdFx0XHQncmVjZW50X2J1dHRvbic6ICdCVVRUT05fU1BFQ0lBTCdcclxuXHRcdFx0fSxcclxuXHRcdFx0XHJcblx0XHRcdC8qKlxyXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXHJcblx0XHRcdCAqXHJcblx0XHRcdCAqIEB2YXIge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxyXG5cdFx0XHRcclxuXHRcdFx0LyoqXHJcblx0XHRcdCAqIE1vZHVsZSBPYmplY3RcclxuXHRcdFx0ICpcclxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cclxuXHRcdFx0ICovXHJcblx0XHRcdG1vZHVsZSA9IHt9O1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIFBSSVZBVEUgTUVUSE9EU1xyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogTWFwIGFjdGlvbnMgdG8gYnV0dG9ucy5cclxuXHRcdCAqXHJcblx0XHQgKiBAcHJpdmF0ZVxyXG5cdFx0ICovXHJcblx0XHR2YXIgX3NldEFjdGlvbnMgPSBmdW5jdGlvbigpIHtcclxuXHRcdFx0dmFyIGFjdGlvbnMgPSBbXTtcclxuXHRcdFx0XHJcblx0XHRcdGFjdGlvbnMuQlVUVE9OX1NQRUNJQUwgPSBfc2V0U3BlY2lhbFByaWNlQWN0aW9uQ2FsbGJhY2s7XHJcblx0XHRcdGFjdGlvbnMuQlVUVE9OX1BST1BFUlRJRVMgPSBfc2V0UHJvcGVydGllc0FjdGlvbkNhbGxiYWNrO1xyXG5cdFx0XHRhY3Rpb25zLkJVVFRPTl9BVFRSSUJVVEVTID0gX3NldEF0dHJpYnV0ZXNBY3Rpb25DYWxsYmFjaztcclxuXHRcdFx0XHJcblx0XHRcdGlmIChvcHRpb25zLmF0dHJpYnV0ZXNfdXJsID09PSAnJyAmJiBvcHRpb25zLnJlY2VudF9idXR0b24gPT09ICdCVVRUT05fQVRUUklCVVRFUycpIHtcclxuXHRcdFx0XHRvcHRpb25zLnJlY2VudF9idXR0b24gPSBkZWZhdWx0cy5yZWNlbnRfYnV0dG9uO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCR0aGlzLCAnQlVUVE9OX1NQRUNJQUwnLCAnYWRtaW5fYnV0dG9ucycsXHJcblx0XHRcdFx0X3NldFNwZWNpYWxQcmljZUFjdGlvbkNhbGxiYWNrKTtcclxuXHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbigkdGhpcywgJ0JVVFRPTl9QUk9QRVJUSUVTJywgJ2FkbWluX2J1dHRvbnMnLFxyXG5cdFx0XHRcdF9zZXRQcm9wZXJ0aWVzQWN0aW9uQ2FsbGJhY2spO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKG9wdGlvbnMuYXR0cmlidXRlc191cmwgIT09ICcnKSB7XHJcblx0XHRcdFx0anNlLmxpYnMuYnV0dG9uX2Ryb3Bkb3duLm1hcEFjdGlvbigkdGhpcywgJ0JVVFRPTl9BVFRSSUJVVEVTJywgJ2FkbWluX2J1dHRvbnMnLFxyXG5cdFx0XHRcdFx0X3NldEF0dHJpYnV0ZXNBY3Rpb25DYWxsYmFjayk7XHJcblx0XHRcdH1cclxuXHRcdH07XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogUmVkaXJlY3QgdG8gc3BlY2lhbCBwcmljaW5nIHBhZ2UuXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0XHQgKlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqL1xyXG5cdFx0dmFyIF9zZXRTcGVjaWFsUHJpY2VBY3Rpb25DYWxsYmFjayA9IGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKG9wdGlvbnMuc3BlY2lhbHNfdXJsICE9PSAnJykge1xyXG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gb3B0aW9ucy5zcGVjaWFsc191cmw7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH07XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogUmVkaXJlY3QgdG8gcHJvcGVydGllcyBwYWdlLlxyXG5cdFx0ICpcclxuXHRcdCAqIEByZXR1cm5zIHtib29sZWFufVxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqL1xyXG5cdFx0dmFyIF9zZXRQcm9wZXJ0aWVzQWN0aW9uQ2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHJcblx0XHRcdGlmIChvcHRpb25zLnByb3BlcnRpZXNfdXJsICE9PSAnJykge1xyXG5cdFx0XHRcdHdpbmRvdy5sb2NhdGlvbi5ocmVmID0gb3B0aW9ucy5wcm9wZXJ0aWVzX3VybDtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0cmV0dXJuIGZhbHNlO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZWRpcmVjdCB0byBhdHRyaWJ1dGVzIHBhZ2UuXHJcblx0XHQgKlxyXG5cdFx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0XHQgKlxyXG5cdFx0ICogQHByaXZhdGVcclxuXHRcdCAqL1xyXG5cdFx0dmFyIF9zZXRBdHRyaWJ1dGVzQWN0aW9uQ2FsbGJhY2sgPSBmdW5jdGlvbigpIHtcclxuXHRcdFx0XHJcblx0XHRcdGlmIChvcHRpb25zLmF0dHJpYnV0ZXNfdXJsICE9PSAnJyAmJiBvcHRpb25zLnByb2R1Y3RfaWQgIT09ICcnKSB7XHJcblx0XHRcdFx0dmFyICRmb3JtID0gJCgnPGZvcm0gYWN0aW9uPVwiJyArIG9wdGlvbnMuYXR0cmlidXRlc191cmwgKyAnXCIgbWV0aG9kPVwicG9zdFwiPicgK1xyXG5cdFx0XHRcdFx0JzxpbnB1dCB0eXBlPVwiaGlkZGVuXCIgbmFtZT1cImFjdGlvblwiIHZhbHVlPVwiZWRpdFwiIC8+JyArXHJcblx0XHRcdFx0XHQnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiY3VycmVudF9wcm9kdWN0X2lkXCIgdmFsdWU9XCInICsgb3B0aW9ucy5wcm9kdWN0X2lkICsgJ1wiIC8+JyArXHJcblx0XHRcdFx0XHQnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiY3BhdGhcIiB2YWx1ZT1cIicgKyBvcHRpb25zLmNfcGF0aCArICdcIiAvPicgK1xyXG5cdFx0XHRcdFx0JzwvZm9ybT4nKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHQkKCdib2R5JykucHJlcGVuZCgkZm9ybSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0JGZvcm0uc3VibWl0KCk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdHJldHVybiBmYWxzZTtcclxuXHRcdH07XHJcblx0XHRcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdFx0d2luZG93LnNldFRpbWVvdXQoX3NldEFjdGlvbnMsIDMwMCk7XHJcblx0XHRcdGRvbmUoKTtcdC8vIEZpbmlzaCBpdFxyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0cmV0dXJuIG1vZHVsZTtcclxuXHR9KTtcclxuIl19
