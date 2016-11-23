'use strict';

/* --------------------------------------------------------------
 categories_multi_action_controller.js 2016-02-15
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Orders Table Controller
 *
 * This controller contains the mapping logic of the categories/articles multi select actions for the button
 * dropdown (on the bottom).
 *
 * @module Compatibility/categories_multi_action_controller
 */
gx.compatibility.module('categories_multi_action_controller', [gx.source + '/libs/button_dropdown'],

/**  @lends module:Compatibility/categories_multi_action_controller */

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
  * Dropdown button selector
  * @var {object}
  */
	$dropdown = $this.find('.js-bottom-dropdown'),


	/**
  * Input fields
  * @type {*|jQuery|HTMLElement}
  */
	$inputs = $('tr[data-id] input[type="checkbox"]'),


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
  * Get Url Parameter
  *
  * Gets a specific URL get parameter from the address bar,
  * which name should be provided as an argument.
  * @param {string} parameterName
  * @returns {object}
  * @private
  */
	var _getUrlParameter = function _getUrlParameter(parameterName) {
		var results = new RegExp('[\?&]' + parameterName + '=([^&#]*)').exec(window.location.href);
		if (results == null) {
			return null;
		} else {
			return results[1] || 0;
		}
	};

	/**
  * Prepare Form
  *
  * @param {string} action
  *
  * @return {object | jQuery}
  */
	var _$prepareForm = function _$prepareForm(action) {
		var cPath;
		try {
			cPath = window.location.href.match(/cPath=(.*)/)[1];
		} catch (e) {
			cPath = $('[data-cpath]:first').data().cpath;
		}

		var page_token = $('input[name="page_token"]:first').attr('value');

		var formUrl = [_getSourcePath(), 'categories.php', '?action=multi_action', '&cPath=' + cPath].join('');

		var search = _getUrlParameter('search');
		if (search !== 0 && search !== null) {
			formUrl += '&search=' + search;
		}

		var page = _getUrlParameter('page');
		if (page !== 0 && page !== null && formUrl.indexOf('page=') === -1) {
			formUrl += '&page=' + page;
		}

		var $form = $('<form name="multi_action_form" method="post" action=' + formUrl + '></form>');
		$form.append('<input type="hidden" name="cpath" value=' + cPath + '>');
		$form.append('<input type="hidden" name="page_token" value=' + page_token + '>');
		$form.append('<input type="hidden" name=' + action + ' value="Action">');
		$form.appendTo('body');
		return $form;
	};

	var _sectionMapping = {
		delete: 'buttons',
		BUTTON_MOVE: 'admin_buttons',
		BUTTON_COPY: 'admin_buttons',
		BUTTON_STATUS_ON: 'admin_buttons',
		BUTTON_STATUS_OFF: 'admin_buttons'
	};

	/**
  * Map actions for the dropdown button
  *
  * This method will map the actions for multiple selects.
  */
	var _mapMultiActions = function _mapMultiActions() {
		var actions = ['delete', 'BUTTON_MOVE', 'BUTTON_COPY', 'BUTTON_STATUS_ON', 'BUTTON_STATUS_OFF'];

		for (var index in actions) {
			_mapAction(actions[index]);
		}
	};

	var _mapAction = function _mapAction(action) {
		var section = _sectionMapping[action],
		    callback = _getActionCallback(action);
		jse.libs.button_dropdown.mapAction($dropdown, action, section, callback);
	};

	var _callbackDelete = function _callbackDelete(event) {
		// Do not do anything when no product/category is checked
		if (!$inputs.filter(':checked').length) {
			return;
		}

		// Submit cached form
		var $form = _$prepareForm('multi_delete');
		$inputs.filter(':checked').appendTo($form);
		$form.submit();
	};

	var _callbackMove = function _callbackMove(event) {
		// Do not do anything when no product/category is checked
		if (!$inputs.filter(':checked').length) {
			return;
		}

		// Submit cached form
		var $form = _$prepareForm('multi_move');
		$inputs.filter(':checked').appendTo($form);
		$form.submit();
	};

	var _callbackCopy = function _callbackCopy(event) {
		// Do not do anything when no product/category is checked
		if (!$inputs.filter(':checked').length) {
			return;
		}

		// Submit cached form
		var $form = _$prepareForm('multi_copy');
		$inputs.filter(':checked').appendTo($form);
		$form.submit();
	};

	var _callbackStatusOn = function _callbackStatusOn(event) {
		// Do not do anything when no product/category is checked
		if (!$inputs.filter(':checked').length) {
			return;
		}

		// Submit cached form
		var $form = _$prepareForm('multi_status_on');
		$inputs.filter(':checked').appendTo($form);
		$form.submit();
	};

	var _callbackStatusOff = function _callbackStatusOff(event) {
		// Do not do anything when no product/category is checked
		if (!$inputs.filter(':checked').length) {
			return;
		}

		// Submit cached form
		var $form = _$prepareForm('multi_status_off');
		$inputs.filter(':checked').appendTo($form);
		$form.submit();
	};

	var _getActionCallback = function _getActionCallback(action) {
		switch (action) {
			case 'delete':
				return _callbackDelete;
			case 'BUTTON_MOVE':
				return _callbackMove;
			case 'BUTTON_COPY':
				return _callbackCopy;
			case 'BUTTON_STATUS_ON':
				return _callbackStatusOn;
			case 'BUTTON_STATUS_OFF':
				return _callbackStatusOff;
			default:
				console.alert('_getActionCallback: Action not found');
		}
		return null;
	};

	/**
  * Get path of the admin folder
  *
  * @returns {string}
  */
	var _getSourcePath = function _getSourcePath() {
		var url = window.location.origin,
		    path = window.location.pathname;

		var splittedPath = path.split('/');
		splittedPath.pop();

		var joinedPath = splittedPath.join('/');

		return url + joinedPath + '/';
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Wait until the dropdown is filled
		var interval = setInterval(function () {
			if ($('.js-button-dropdown').length > 0) {
				clearInterval(interval);
				_mapMultiActions();
			}
		}, 200);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhdGVnb3JpZXMvY2F0ZWdvcmllc19tdWx0aV9hY3Rpb25fY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCIkZHJvcGRvd24iLCJmaW5kIiwiJGlucHV0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfZ2V0VXJsUGFyYW1ldGVyIiwicGFyYW1ldGVyTmFtZSIsInJlc3VsdHMiLCJSZWdFeHAiLCJleGVjIiwid2luZG93IiwibG9jYXRpb24iLCJocmVmIiwiXyRwcmVwYXJlRm9ybSIsImFjdGlvbiIsImNQYXRoIiwibWF0Y2giLCJlIiwiY3BhdGgiLCJwYWdlX3Rva2VuIiwiYXR0ciIsImZvcm1VcmwiLCJfZ2V0U291cmNlUGF0aCIsImpvaW4iLCJzZWFyY2giLCJwYWdlIiwiaW5kZXhPZiIsIiRmb3JtIiwiYXBwZW5kIiwiYXBwZW5kVG8iLCJfc2VjdGlvbk1hcHBpbmciLCJkZWxldGUiLCJCVVRUT05fTU9WRSIsIkJVVFRPTl9DT1BZIiwiQlVUVE9OX1NUQVRVU19PTiIsIkJVVFRPTl9TVEFUVVNfT0ZGIiwiX21hcE11bHRpQWN0aW9ucyIsImFjdGlvbnMiLCJpbmRleCIsIl9tYXBBY3Rpb24iLCJzZWN0aW9uIiwiY2FsbGJhY2siLCJfZ2V0QWN0aW9uQ2FsbGJhY2siLCJqc2UiLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwibWFwQWN0aW9uIiwiX2NhbGxiYWNrRGVsZXRlIiwiZXZlbnQiLCJmaWx0ZXIiLCJsZW5ndGgiLCJzdWJtaXQiLCJfY2FsbGJhY2tNb3ZlIiwiX2NhbGxiYWNrQ29weSIsIl9jYWxsYmFja1N0YXR1c09uIiwiX2NhbGxiYWNrU3RhdHVzT2ZmIiwiY29uc29sZSIsImFsZXJ0IiwidXJsIiwib3JpZ2luIiwicGF0aCIsInBhdGhuYW1lIiwic3BsaXR0ZWRQYXRoIiwic3BsaXQiLCJwb3AiLCJqb2luZWRQYXRoIiwi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LGVBQVA7QUFDRCxRQUFLLGFBQUw7QUFDQyxXQUFPSyxhQUFQO0FBQ0QsUUFBSyxhQUFMO0FBQ0MsV0FBT0MsYUFBUDtBQUNELFFBQUssa0JBQUw7QUFDQyxXQUFPQyxpQkFBUDtBQUNELFFBQUssbUJBQUw7QUFDQyxXQUFPQyxrQkFBUDtBQUNEO0FBQ0NDLFlBQVFDLEtBQVIsQ0FBYyxzQ0FBZDtBQVpGO0FBY0EsU0FBTyxJQUFQO0FBQ0EsRUFoQkQ7O0FBa0JBOzs7OztBQUtBLEtBQUluQyxpQkFBaUIsU0FBakJBLGNBQWlCLEdBQVc7QUFDL0IsTUFBSW9DLE1BQU1oRCxPQUFPQyxRQUFQLENBQWdCZ0QsTUFBMUI7QUFBQSxNQUNDQyxPQUFPbEQsT0FBT0MsUUFBUCxDQUFnQmtELFFBRHhCOztBQUdBLE1BQUlDLGVBQWVGLEtBQUtHLEtBQUwsQ0FBVyxHQUFYLENBQW5CO0FBQ0FELGVBQWFFLEdBQWI7O0FBRUEsTUFBSUMsYUFBYUgsYUFBYXZDLElBQWIsQ0FBa0IsR0FBbEIsQ0FBakI7O0FBRUEsU0FBT21DLE1BQU1PLFVBQU4sR0FBbUIsR0FBMUI7QUFDQSxFQVZEOztBQVlBO0FBQ0E7QUFDQTs7QUFFQXZFLFFBQU93RSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCO0FBQ0EsTUFBSUMsV0FBV0MsWUFBWSxZQUFXO0FBQ3JDLE9BQUl2RSxFQUFFLHFCQUFGLEVBQXlCb0QsTUFBekIsR0FBa0MsQ0FBdEMsRUFBeUM7QUFDeENvQixrQkFBY0YsUUFBZDtBQUNBaEM7QUFDQTtBQUNELEdBTGMsRUFLWixHQUxZLENBQWY7O0FBT0ErQjtBQUNBLEVBVkQ7O0FBWUEsUUFBT3pFLE1BQVA7QUFDQSxDQTlRRiIsImZpbGUiOiJjYXRlZ29yaWVzL2NhdGVnb3JpZXNfbXVsdGlfYWN0aW9uX2NvbnRyb2xsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhdGVnb3JpZXNfbXVsdGlfYWN0aW9uX2NvbnRyb2xsZXIuanMgMjAxNi0wMi0xNVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgT3JkZXJzIFRhYmxlIENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgY29udGFpbnMgdGhlIG1hcHBpbmcgbG9naWMgb2YgdGhlIGNhdGVnb3JpZXMvYXJ0aWNsZXMgbXVsdGkgc2VsZWN0IGFjdGlvbnMgZm9yIHRoZSBidXR0b25cbiAqIGRyb3Bkb3duIChvbiB0aGUgYm90dG9tKS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvY2F0ZWdvcmllc19tdWx0aV9hY3Rpb25fY29udHJvbGxlclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2NhdGVnb3JpZXNfbXVsdGlfYWN0aW9uX2NvbnRyb2xsZXInLFxuXHRcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9idXR0b25fZHJvcGRvd24nXG5cdF0sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9jYXRlZ29yaWVzX211bHRpX2FjdGlvbl9jb250cm9sbGVyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRHJvcGRvd24gYnV0dG9uIHNlbGVjdG9yXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRkcm9wZG93biA9ICR0aGlzLmZpbmQoJy5qcy1ib3R0b20tZHJvcGRvd24nKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBJbnB1dCBmaWVsZHNcblx0XHRcdCAqIEB0eXBlIHsqfGpRdWVyeXxIVE1MRWxlbWVudH1cblx0XHRcdCAqL1xuXHRcdFx0JGlucHV0cyA9ICQoJ3RyW2RhdGEtaWRdIGlucHV0W3R5cGU9XCJjaGVja2JveFwiXScpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBQUklWQVRFIE1FVEhPRFNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgVXJsIFBhcmFtZXRlclxuXHRcdCAqXG5cdFx0ICogR2V0cyBhIHNwZWNpZmljIFVSTCBnZXQgcGFyYW1ldGVyIGZyb20gdGhlIGFkZHJlc3MgYmFyLFxuXHRcdCAqIHdoaWNoIG5hbWUgc2hvdWxkIGJlIHByb3ZpZGVkIGFzIGFuIGFyZ3VtZW50LlxuXHRcdCAqIEBwYXJhbSB7c3RyaW5nfSBwYXJhbWV0ZXJOYW1lXG5cdFx0ICogQHJldHVybnMge29iamVjdH1cblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0VXJsUGFyYW1ldGVyID0gZnVuY3Rpb24ocGFyYW1ldGVyTmFtZSkge1xuXHRcdFx0dmFyIHJlc3VsdHMgPSBuZXcgUmVnRXhwKCdbXFw/Jl0nICsgcGFyYW1ldGVyTmFtZSArICc9KFteJiNdKiknKS5leGVjKHdpbmRvdy5sb2NhdGlvbi5ocmVmKTtcblx0XHRcdGlmIChyZXN1bHRzID09IG51bGwpIHtcblx0XHRcdFx0cmV0dXJuIG51bGw7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRyZXR1cm4gcmVzdWx0c1sxXSB8fCAwO1xuXHRcdFx0fVxuXHRcdH07XG5cdFx0XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogUHJlcGFyZSBGb3JtXG5cdFx0ICpcblx0XHQgKiBAcGFyYW0ge3N0cmluZ30gYWN0aW9uXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJuIHtvYmplY3QgfCBqUXVlcnl9XG5cdFx0ICovXG5cdFx0dmFyIF8kcHJlcGFyZUZvcm0gPSBmdW5jdGlvbihhY3Rpb24pIHtcblx0XHRcdHZhciBjUGF0aDtcblx0XHRcdHRyeSB7XG5cdFx0XHRcdGNQYXRoID0gd2luZG93LmxvY2F0aW9uLmhyZWYubWF0Y2goL2NQYXRoPSguKikvKVsxXTtcblx0XHRcdH1cblx0XHRcdGNhdGNoIChlKSB7XG5cdFx0XHRcdGNQYXRoID0gJCgnW2RhdGEtY3BhdGhdOmZpcnN0JykuZGF0YSgpLmNwYXRoO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHR2YXIgcGFnZV90b2tlbiA9ICQoJ2lucHV0W25hbWU9XCJwYWdlX3Rva2VuXCJdOmZpcnN0JykuYXR0cigndmFsdWUnKTtcblx0XHRcdFxuXHRcdFx0dmFyIGZvcm1VcmwgPSBbXG5cdFx0XHRcdF9nZXRTb3VyY2VQYXRoKCksXG5cdFx0XHRcdCdjYXRlZ29yaWVzLnBocCcsXG5cdFx0XHRcdCc/YWN0aW9uPW11bHRpX2FjdGlvbicsXG5cdFx0XHRcdCcmY1BhdGg9JyArIGNQYXRoXG5cdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0XG5cdFx0XHR2YXIgc2VhcmNoID0gX2dldFVybFBhcmFtZXRlcignc2VhcmNoJyk7XG5cdFx0XHRpZiAoc2VhcmNoICE9PSAwICYmIHNlYXJjaCAhPT0gbnVsbCkge1xuXHRcdFx0XHRmb3JtVXJsICs9ICgnJnNlYXJjaD0nICsgc2VhcmNoKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0dmFyIHBhZ2UgPSBfZ2V0VXJsUGFyYW1ldGVyKCdwYWdlJyk7XG5cdFx0XHRpZiAocGFnZSAhPT0gMCAmJiBwYWdlICE9PSBudWxsICYmIGZvcm1VcmwuaW5kZXhPZigncGFnZT0nKSA9PT0gLTEpIHtcblx0XHRcdFx0Zm9ybVVybCArPSAnJnBhZ2U9JyArIHBhZ2U7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdHZhciAkZm9ybSA9ICQoJzxmb3JtIG5hbWU9XCJtdWx0aV9hY3Rpb25fZm9ybVwiIG1ldGhvZD1cInBvc3RcIiBhY3Rpb249JyArIGZvcm1VcmwgKyAnPjwvZm9ybT4nKTtcblx0XHRcdCRmb3JtLmFwcGVuZCgnPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiY3BhdGhcIiB2YWx1ZT0nICsgY1BhdGggKyAnPicpO1xuXHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9XCJwYWdlX3Rva2VuXCIgdmFsdWU9JyArIHBhZ2VfdG9rZW4gKyAnPicpO1xuXHRcdFx0JGZvcm0uYXBwZW5kKCc8aW5wdXQgdHlwZT1cImhpZGRlblwiIG5hbWU9JyArIGFjdGlvbiArICcgdmFsdWU9XCJBY3Rpb25cIj4nKTtcblx0XHRcdCRmb3JtLmFwcGVuZFRvKCdib2R5Jyk7XG5cdFx0XHRyZXR1cm4gJGZvcm07XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3NlY3Rpb25NYXBwaW5nID0ge1xuXHRcdFx0ZGVsZXRlOiAnYnV0dG9ucycsXG5cdFx0XHRCVVRUT05fTU9WRTogJ2FkbWluX2J1dHRvbnMnLFxuXHRcdFx0QlVUVE9OX0NPUFk6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9TVEFUVVNfT046ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9TVEFUVVNfT0ZGOiAnYWRtaW5fYnV0dG9ucydcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIE1hcCBhY3Rpb25zIGZvciB0aGUgZHJvcGRvd24gYnV0dG9uXG5cdFx0ICpcblx0XHQgKiBUaGlzIG1ldGhvZCB3aWxsIG1hcCB0aGUgYWN0aW9ucyBmb3IgbXVsdGlwbGUgc2VsZWN0cy5cblx0XHQgKi9cblx0XHR2YXIgX21hcE11bHRpQWN0aW9ucyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGFjdGlvbnMgPSBbXG5cdFx0XHRcdCdkZWxldGUnLFxuXHRcdFx0XHQnQlVUVE9OX01PVkUnLFxuXHRcdFx0XHQnQlVUVE9OX0NPUFknLFxuXHRcdFx0XHQnQlVUVE9OX1NUQVRVU19PTicsXG5cdFx0XHRcdCdCVVRUT05fU1RBVFVTX09GRidcblx0XHRcdF07XG5cdFx0XHRcblx0XHRcdGZvciAodmFyIGluZGV4IGluIGFjdGlvbnMpIHtcblx0XHRcdFx0X21hcEFjdGlvbihhY3Rpb25zW2luZGV4XSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX21hcEFjdGlvbiA9IGZ1bmN0aW9uKGFjdGlvbikge1xuXHRcdFx0dmFyIHNlY3Rpb24gPSBfc2VjdGlvbk1hcHBpbmdbYWN0aW9uXSxcblx0XHRcdFx0Y2FsbGJhY2sgPSBfZ2V0QWN0aW9uQ2FsbGJhY2soYWN0aW9uKTtcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oJGRyb3Bkb3duLCBhY3Rpb24sIHNlY3Rpb24sIGNhbGxiYWNrKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfY2FsbGJhY2tEZWxldGUgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gRG8gbm90IGRvIGFueXRoaW5nIHdoZW4gbm8gcHJvZHVjdC9jYXRlZ29yeSBpcyBjaGVja2VkXG5cdFx0XHRpZiAoISRpbnB1dHMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aCkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFN1Ym1pdCBjYWNoZWQgZm9ybVxuXHRcdFx0dmFyICRmb3JtID0gXyRwcmVwYXJlRm9ybSgnbXVsdGlfZGVsZXRlJyk7XG5cdFx0XHQkaW5wdXRzLmZpbHRlcignOmNoZWNrZWQnKS5hcHBlbmRUbygkZm9ybSk7XG5cdFx0XHQkZm9ybS5zdWJtaXQoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfY2FsbGJhY2tNb3ZlID0gZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdC8vIERvIG5vdCBkbyBhbnl0aGluZyB3aGVuIG5vIHByb2R1Y3QvY2F0ZWdvcnkgaXMgY2hlY2tlZFxuXHRcdFx0aWYgKCEkaW5wdXRzLmZpbHRlcignOmNoZWNrZWQnKS5sZW5ndGgpIHtcblx0XHRcdFx0cmV0dXJuO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHQvLyBTdWJtaXQgY2FjaGVkIGZvcm1cblx0XHRcdHZhciAkZm9ybSA9IF8kcHJlcGFyZUZvcm0oJ211bHRpX21vdmUnKTtcblx0XHRcdCRpbnB1dHMuZmlsdGVyKCc6Y2hlY2tlZCcpLmFwcGVuZFRvKCRmb3JtKTtcblx0XHRcdCRmb3JtLnN1Ym1pdCgpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9jYWxsYmFja0NvcHkgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gRG8gbm90IGRvIGFueXRoaW5nIHdoZW4gbm8gcHJvZHVjdC9jYXRlZ29yeSBpcyBjaGVja2VkXG5cdFx0XHRpZiAoISRpbnB1dHMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aCkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFN1Ym1pdCBjYWNoZWQgZm9ybVxuXHRcdFx0dmFyICRmb3JtID0gXyRwcmVwYXJlRm9ybSgnbXVsdGlfY29weScpO1xuXHRcdFx0JGlucHV0cy5maWx0ZXIoJzpjaGVja2VkJykuYXBwZW5kVG8oJGZvcm0pO1xuXHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2NhbGxiYWNrU3RhdHVzT24gPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gRG8gbm90IGRvIGFueXRoaW5nIHdoZW4gbm8gcHJvZHVjdC9jYXRlZ29yeSBpcyBjaGVja2VkXG5cdFx0XHRpZiAoISRpbnB1dHMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aCkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFN1Ym1pdCBjYWNoZWQgZm9ybVxuXHRcdFx0dmFyICRmb3JtID0gXyRwcmVwYXJlRm9ybSgnbXVsdGlfc3RhdHVzX29uJyk7XG5cdFx0XHQkaW5wdXRzLmZpbHRlcignOmNoZWNrZWQnKS5hcHBlbmRUbygkZm9ybSk7XG5cdFx0XHQkZm9ybS5zdWJtaXQoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfY2FsbGJhY2tTdGF0dXNPZmYgPSBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0Ly8gRG8gbm90IGRvIGFueXRoaW5nIHdoZW4gbm8gcHJvZHVjdC9jYXRlZ29yeSBpcyBjaGVja2VkXG5cdFx0XHRpZiAoISRpbnB1dHMuZmlsdGVyKCc6Y2hlY2tlZCcpLmxlbmd0aCkge1xuXHRcdFx0XHRyZXR1cm47XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdC8vIFN1Ym1pdCBjYWNoZWQgZm9ybVxuXHRcdFx0dmFyICRmb3JtID0gXyRwcmVwYXJlRm9ybSgnbXVsdGlfc3RhdHVzX29mZicpO1xuXHRcdFx0JGlucHV0cy5maWx0ZXIoJzpjaGVja2VkJykuYXBwZW5kVG8oJGZvcm0pO1xuXHRcdFx0JGZvcm0uc3VibWl0KCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2dldEFjdGlvbkNhbGxiYWNrID0gZnVuY3Rpb24oYWN0aW9uKSB7XG5cdFx0XHRzd2l0Y2ggKGFjdGlvbikge1xuXHRcdFx0XHRjYXNlICdkZWxldGUnOlxuXHRcdFx0XHRcdHJldHVybiBfY2FsbGJhY2tEZWxldGU7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9NT1ZFJzpcblx0XHRcdFx0XHRyZXR1cm4gX2NhbGxiYWNrTW92ZTtcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX0NPUFknOlxuXHRcdFx0XHRcdHJldHVybiBfY2FsbGJhY2tDb3B5O1xuXHRcdFx0XHRjYXNlICdCVVRUT05fU1RBVFVTX09OJzpcblx0XHRcdFx0XHRyZXR1cm4gX2NhbGxiYWNrU3RhdHVzT247XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9TVEFUVVNfT0ZGJzpcblx0XHRcdFx0XHRyZXR1cm4gX2NhbGxiYWNrU3RhdHVzT2ZmO1xuXHRcdFx0XHRkZWZhdWx0OlxuXHRcdFx0XHRcdGNvbnNvbGUuYWxlcnQoJ19nZXRBY3Rpb25DYWxsYmFjazogQWN0aW9uIG5vdCBmb3VuZCcpO1xuXHRcdFx0fVxuXHRcdFx0cmV0dXJuIG51bGw7XG5cdFx0fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBHZXQgcGF0aCBvZiB0aGUgYWRtaW4gZm9sZGVyXG5cdFx0ICpcblx0XHQgKiBAcmV0dXJucyB7c3RyaW5nfVxuXHRcdCAqL1xuXHRcdHZhciBfZ2V0U291cmNlUGF0aCA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIHVybCA9IHdpbmRvdy5sb2NhdGlvbi5vcmlnaW4sXG5cdFx0XHRcdHBhdGggPSB3aW5kb3cubG9jYXRpb24ucGF0aG5hbWU7XG5cdFx0XHRcblx0XHRcdHZhciBzcGxpdHRlZFBhdGggPSBwYXRoLnNwbGl0KCcvJyk7XG5cdFx0XHRzcGxpdHRlZFBhdGgucG9wKCk7XG5cdFx0XHRcblx0XHRcdHZhciBqb2luZWRQYXRoID0gc3BsaXR0ZWRQYXRoLmpvaW4oJy8nKTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIHVybCArIGpvaW5lZFBhdGggKyAnLyc7XG5cdFx0fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0Ly8gV2FpdCB1bnRpbCB0aGUgZHJvcGRvd24gaXMgZmlsbGVkXG5cdFx0XHR2YXIgaW50ZXJ2YWwgPSBzZXRJbnRlcnZhbChmdW5jdGlvbigpIHtcblx0XHRcdFx0aWYgKCQoJy5qcy1idXR0b24tZHJvcGRvd24nKS5sZW5ndGggPiAwKSB7XG5cdFx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHRcdFx0X21hcE11bHRpQWN0aW9ucygpO1xuXHRcdFx0XHR9XG5cdFx0XHR9LCAyMDApO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
