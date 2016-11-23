'use strict';

/* --------------------------------------------------------------
 customers_table_controller.js.js 2016-03-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Customers Table Controller
 *
 * This controller contains the mapping logic of the customers table.
 *
 * @module Compatibility/customers_table_controller
 */
gx.compatibility.module('customers_table_controller', [gx.source + '/libs/button_dropdown'],

/**  @lends module:Compatibility/customers_table_controller */

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
	module = {},


	/**
  * Reference to the actual file
  *
  * @var {string}
  */
	srcPath = window.location.origin + window.location.pathname,


	/**
  * Query parameter string
  * 
  * @type {string}
  */
	queryString = '?' + window.location.search.replace(/\?/, '').replace(/cID=[\d]+/g, '').replace(/action=[\w]+/g, '').concat('&').replace(/&[&]+/g, '&').replace(/^&/g, '');

	// ------------------------------------------------------------------------
	// PRIVATE METHODS
	// ------------------------------------------------------------------------

	/**
  * Map actions for every row in the table.
  *
  * This method will map the actions for each
  * row of the table.
  *
  * @private
  */
	var _mapRowActions = function _mapRowActions() {
		// Iterate over table rows, except the header row
		$('.gx-customer-overview tr').not('.dataTableHeadingRow').each(function () {

			/**
    * Save that "this" scope here
    * @var {object | jQuery}
    */
			var $that = $(this);

			/**
    * Data attributes of current row
    * @var {object}
    */
			var data = $that.data();

			/**
    * Reference to the row action dropdown
    * @var {object | jQuery}
    */
			var $dropdown = $that.find('.js-button-dropdown');

			if ($dropdown.length) {

				// Add click event to the table row and open the
				// customer detail view
				$that.find('.btn-edit').closest('tr').css({
					cursor: 'pointer'
				}).on('click', function (event) {
					// Compose the URL and open it
					var url = [srcPath, '?cID=' + data.rowId, '&action=edit'].join('');
					if ($(event.target).prop('tagName') === 'TD') {
						window.open(url, '_self');
					}
				});

				// Icon behavior - Edit
				$that.find('.btn-edit').css({
					cursor: 'pointer'
				}).prop('title', jse.core.lang.translate('edit', 'buttons')).on('click', function () {
					// Compose the URL and open it
					var url = [srcPath, '?cID=' + data.rowId, '&action=edit'].join('');
					window.open(url, '_self');
				});

				// Icon behavior - Delete
				if (data.rowId !== 1) {
					$that.find('.btn-delete').css({
						cursor: 'pointer'
					}).prop('title', jse.core.lang.translate('delete', 'buttons')).on('click', function () {
						// Compose the URL and open it
						var url = [srcPath, queryString, 'cID=' + data.rowId, '&action=confirm'].join('');
						window.open(url, '_self');
					});
				}

				if (data.rowId === 1) {
					$that.find('.btn-delete').css({
						opacity: '0.2'
					});
				}

				// Icon behavior - Orders
				$that.find('.btn-order').css({
					cursor: 'pointer'
				}).prop('title', jse.core.lang.translate('BUTTON_ORDERS', 'admin_buttons')).on('click', function () {
					// Compose the URL and open it
					var url = [srcPath.replace('customers.php', 'admin.php'), '?' + $.param({
						do: 'OrdersOverview',
						filter: {
							customer: '#' + data.rowId
						}
					})].join('');
					window.open(url, '_self');
				});

				_mapButtonDropdown($that, $dropdown, data);
			}
		});
	};

	var _mapButtonDropdown = function _mapButtonDropdown($that, $dropdown, data) {
		var actions = ['edit'];

		// Bind dropdown option - Delete
		if (data.rowId !== 1) {
			actions.push('delete');
		}

		actions = actions.concat(['BUTTON_STATUS', 'BUTTON_ORDERS', 'BUTTON_EMAIL', 'BUTTON_IPLOG', 'BUTTON_NEW_ORDER']);

		// Admin rights button
		if ($that.find('[data-cust-group]').data('custGroup') === 0 && $that.find('[data-cust-id]').data('custId') !== 1) {
			actions.push('BUTTON_ACCOUNTING');
		}

		// Bind MailBeez dropdown options.
		var mailBeezConversationsSelector = '.contentTable .infoBoxContent a.context_view_button.btn_right';
		if ($(mailBeezConversationsSelector).length) {
			actions.push('MAILBEEZ_OVERVIEW');
			actions.push('MAILBEEZ_NOTIFICATIONS');
			actions.push('MAILBEEZ_CONVERSATIONS');
		}

		// Bind Mediafinanz dropdown options.
		var $mediafinanzAction = $('.mediafinanz-creditworthiness');
		if ($mediafinanzAction.length) {
			actions.push('BUTTON_MEDIAFINANZ_CREDITWORTHINESS');
		}

		for (var index in actions) {
			_mapCustomerAction($dropdown, actions[index], data);
		}
	};

	var _mapCustomerAction = function _mapCustomerAction($dropdown, action, data) {
		jse.libs.button_dropdown.mapAction($dropdown, action, _sectionMapping[action], function (event) {
			_executeActionCallback(action, data);
		});
	};

	var _sectionMapping = {
		edit: 'buttons',
		delete: 'buttons',
		BUTTON_STATUS: 'admin_buttons',
		BUTTON_ORDERS: 'admin_buttons',
		BUTTON_EMAIL: 'admin_buttons',
		BUTTON_IPLOG: 'admin_buttons',
		MAILBEEZ_OVERVIEW: 'admin_customers',
		MAILBEEZ_NOTIFICATIONS: 'admin_customers',
		MAILBEEZ_CONVERSATIONS: 'admin_customers',
		BUTTON_MEDIAFINANZ_CREDITWORTHINESS: 'admin_buttons',
		BUTTON_NEW_ORDER: 'admin_buttons',
		BUTTON_ACCOUNTING: 'admin_buttons'
	};

	/**
  * Get the corresponding callback
  *
  * @param action
  * @private
  */
	var _executeActionCallback = function _executeActionCallback(action, data) {
		switch (action) {
			case 'edit':
				_editCallback(data);
				break;
			case 'delete':
				_deleteCallback(data);
				break;
			case 'BUTTON_STATUS':
				_customerGroupCallBack(data);
				break;
			case 'BUTTON_ORDERS':
				_ordersCallback(data);
				break;
			case 'BUTTON_EMAIL':
				_emailCallback(data);
				break;
			case 'BUTTON_IPLOG':
				_ipLogCallback(data);
				break;
			case 'MAILBEEZ_OVERVIEW':
				_mailBeezOverviewCallback(data);
				break;
			case 'MAILBEEZ_NOTIFICATIONS':
				_mailBeezNotificationsCallback(data);
				break;
			case 'MAILBEEZ_CONVERSATIONS':
				_mailBeezConversationsCallback(data);
				break;
			case 'BUTTON_MEDIAFINANZ_CREDITWORTHINESS':
				_mediafinanzCreditworthinessCallback(data);
				break;
			case 'BUTTON_NEW_ORDER':
				_newOrderCallback(data);
				break;
			case 'BUTTON_ACCOUNTING':
				_adminRightsCallback(data);
				break;
			default:
				throw new Error('Callback not found.');
		}
	};

	var _editCallback = function _editCallback(data) {
		// Compose the URL and open it
		var url = [srcPath, '?cID=' + data.rowId, '&action=edit'].join('');
		window.open(url, '_self');
	};

	var _deleteCallback = function _deleteCallback(data) {
		// Compose the URL and open it
		var url = [srcPath, queryString, 'cID=' + data.rowId, '&action=confirm'].join('');
		window.open(url, '_self');
	};

	var _customerGroupCallBack = function _customerGroupCallBack(data) {
		// Compose the URL and open it
		var url = [srcPath, queryString, 'cID=' + data.rowId, '&action=editstatus'].join('');
		window.open(url, '_self');
	};

	var _ordersCallback = function _ordersCallback(data) {
		// Compose the URL and open it
		var url = [srcPath.replace('customers.php', 'admin.php'), '?' + $.param({
			do: 'OrdersOverview',
			filter: {
				customer: '#' + data.rowId
			}
		})].join('');
		window.open(url, '_self');
	};

	var _emailCallback = function _emailCallback(data) {
		// Compose the URL and open it
		var url = [srcPath.replace('customers.php', 'mail.php'), '?selected_box=tools', '&customer=' + data.custEmail].join('');
		window.open(url, '_self');
	};

	var _ipLogCallback = function _ipLogCallback(data) {
		// Compose the URL and open it
		var url = [srcPath, queryString, 'cID=' + data.rowId, '&action=iplog'].join('');
		window.open(url, '_self');
	};

	var _mailBeezOverviewCallback = function _mailBeezOverviewCallback(data) {
		var $target = $('.contentTable .infoBoxContent a.context_view_button.btn_left');
		var url = $('.contentTable .infoBoxContent a.context_view_button.btn_left').attr('onclick');
		url = url.replace(/cID=(.*)&/, 'cID=' + data.rowId + '&');
		$('.contentTable .infoBoxContent a.context_view_button.btn_left').attr('onclick', url);
		$target.get(0).click();
	};

	var _mailBeezNotificationsCallback = function _mailBeezNotificationsCallback(data) {
		var $target = $('.contentTable .infoBoxContent a.context_view_button.btn_middle');
		var url = $('.contentTable .infoBoxContent a.context_view_button.btn_middle').attr('onclick');
		url = url.replace(/cID=(.*)&/, 'cID=' + data.rowId + '&');
		$('.contentTable .infoBoxContent a.context_view_button.btn_middle').attr('onclick', url);
		$target.get(0).click();
	};

	var _mailBeezConversationsCallback = function _mailBeezConversationsCallback(data) {
		var $target = $('.contentTable .infoBoxContent a.context_view_button.btn_right');
		var url = $('.contentTable .infoBoxContent a.context_view_button.btn_right').attr('onclick');
		url = url.replace(/cID=(.*)&/, 'cID=' + data.rowId + '&');
		$('.contentTable .infoBoxContent a.context_view_button.btn_right').attr('onclick', url);
		$target.get(0).click();
	};

	var _mediafinanzCreditworthinessCallback = function _mediafinanzCreditworthinessCallback(data) {
		var $target = $('.mediafinanz-creditworthiness');
		var onclickAttribute = $target.attr('onclick');
		// Replace the customer number in the onclick attribute. 
		onclickAttribute = onclickAttribute.replace(/cID=(.*', 'popup')/, 'cID=' + data.rowId + '\', \'popup\'');
		$target.attr('onclick', onclickAttribute);
		$target.trigger('click'); // Trigger the click event in the <a> element. 
	};

	var _newOrderCallback = function _newOrderCallback(data) {
		// Compose the URL and open it
		var url = [srcPath, '?cID=' + data.rowId, '&action=new_order'].join('');
		window.open(url, '_self');
	};

	var _adminRightsCallback = function _adminRightsCallback(data) {
		// Compose the URL and open it
		var url = [srcPath.replace('customers.php', 'accounting.php'), '?cID=' + data.rowId].join('');
		window.open(url, '_self');
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Wait until the buttons are converted to dropdown for every row.
		var interval = setInterval(function () {
			if ($('.js-button-dropdown').length) {
				clearInterval(interval);
				_mapRowActions();
			}
		}, 500);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImN1c3RvbWVycy9jdXN0b21lcnNfdGFibGVfY29udHJvbGxlci5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwic3JjUGF0aCIsIndpbmRvdyIsImxvY2F0aW9uIiwib3JpZ2luIiwicGF0aG5hbWUiLCJxdWVyeVN0cmluZyIsInNlYXJjaCIsInJlcGxhY2UiLCJjb25jYXQiLCJfbWFwUm93QWN0aW9ucyIsIm5vdCIsImVhY2giLCIkdGhhdCIsIiRkcm9wZG93biIsImZpbmQiLCJsZW5ndGgiLCJjbG9zZXN0IiwiY3NzIiwiY3Vyc29yIiwib24iLCJldmVudCIsInVybCIsInJvd0lkIiwiam9pbiIsInRhcmdldCIsInByb3AiLCJvcGVuIiwianNlIiwiY29yZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJvcGFjaXR5IiwicGFyYW0iLCJkbyIsImZpbHRlciIsImN1c3RvbWVyIiwiX21hcEJ1dHRvbkRyb3Bkb3duIiwiYWN0aW9ucyIsInB1c2giLCJtYWlsQmVlekNvbnZlcnNhdGlvbnNTZWxlY3RvciIsIiRtZWRpYWZpbmFuekFjdGlvbiIsImluZGV4IiwiX21hcEN1c3RvbWVyQWN0aW9uIiwiYWN0aW9uIiwibGlicyIsImJ1dHRvbl9kcm9wZG93biIsIm1hcEFjdGlvbiIsIl9zZWN0aW9uTWFwcGluZyIsIl9leGVjdXRlQWN0aW9uQ2FsbGJhY2siLCJlZGl0IiwiZGVsZXRlIiwiQlVUVE9OX1NUQVRVUyIsIkJVVFRPTl9PUkRFUlMiLCJCVVRUT05fRU1BSUwiLCJCVVRUT05fSVBMT0ciLCJNQUlMQkVFWl9PVkVSVklFVyIsIk1BSUxCRUVaX05PVElGSUNBVElPTlMiLCJNQUlMQkVFWl9DT05WRVJTQVRJT05TIiwiQlVUVE9OX01FRElBRklOQU5aX0NSRURJVFdPUlRISU5FU1MiLCJCVVRUT05fTkVXX09SREVSIiwiQlVUVE9OX0FDQ09VTlRJTkciLCJfZWRpdENhbGxiYWNrIiwiX2RlbGV0ZUNhbGxiYWNrIiwiX2N1c3RvbWVyR3JvdXBDYWxsQmFjayIsIl9vcmRlcnNDYWxsYmFjayIsIl9lbWFpbENhbGxiYWNrIiwiX2lwTG9nQ2FsbGJhY2siLCJfbWFpbEJlZXpPdmVydmlld0NhbGxiYWNrIiwiX21haWxCZWV6Tm90aWZpY2F0aW9uc0NhbGxiYWNrIiwiX21haWxCZWV6Q29udmVyc2F0aW9uc0NhbGxiYWNrIiwiX21lZGlhZmluYW56Q3JlZGl0d29ydGhpbmVzc0NhbGxiYWNrIiwiX25ld09yZGVyQ2FsbGJhY2siLCJfYWRtaW5SaWdodHNDYWxsYmFjayIsIkVycm9yIiwiY3VzdEVtYWlsIiwiJHRhcmdldCIsImF0dHIiLCJnZXQiLCJjbGljayIsIm9uY2xpY2tBdHRyaWJ1dGUiLCJ0cmlnZ2VyIiwiaW5pdCIsImRvbmUiLCJpbnRlcnZhbCIsInNldEludGVydmFsIiwiY2xlYXJJbnRlcnZhbCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsNEJBREQsRUFHQyxDQUNDRixHQUFHRyxNQUFILEdBQVksdUJBRGIsQ0FIRDs7QUFPQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsV0FBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FwQlg7OztBQXNCQzs7Ozs7QUFLQUYsVUFBUyxFQTNCVjs7O0FBNkJDOzs7OztBQUtBUSxXQUFVQyxPQUFPQyxRQUFQLENBQWdCQyxNQUFoQixHQUF5QkYsT0FBT0MsUUFBUCxDQUFnQkUsUUFsQ3BEOzs7QUFvQ0M7Ozs7O0FBS0FDLGVBQWMsTUFBT0osT0FB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QSxNQUFJMkIsTUFBTSxDQUNUckIsUUFBUU8sT0FBUixDQUFnQixlQUFoQixFQUFpQyxnQkFBakMsQ0FEUyxFQUVULFVBQVViLEtBQUs0QixLQUZOLEVBR1JDLElBSFEsQ0FHSCxFQUhHLENBQVY7QUFJQXRCLFNBQU95QixJQUFQLENBQVlMLEdBQVosRUFBaUIsT0FBakI7QUFDQSxFQVBEOztBQVNBO0FBQ0E7QUFDQTs7QUFFQTdCLFFBQU95RixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCO0FBQ0EsTUFBSUMsV0FBV0MsWUFBWSxZQUFXO0FBQ3JDLE9BQUl4RixFQUFFLHFCQUFGLEVBQXlCbUIsTUFBN0IsRUFBcUM7QUFDcENzRSxrQkFBY0YsUUFBZDtBQUNBMUU7QUFDQTtBQUNELEdBTGMsRUFLWixHQUxZLENBQWY7O0FBT0F5RTtBQUNBLEVBVkQ7O0FBWUEsUUFBTzFGLE1BQVA7QUFDQSxDQTNiRiIsImZpbGUiOiJjdXN0b21lcnMvY3VzdG9tZXJzX3RhYmxlX2NvbnRyb2xsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGN1c3RvbWVyc190YWJsZV9jb250cm9sbGVyLmpzLmpzIDIwMTYtMDMtMTdcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIEN1c3RvbWVycyBUYWJsZSBDb250cm9sbGVyXG4gKlxuICogVGhpcyBjb250cm9sbGVyIGNvbnRhaW5zIHRoZSBtYXBwaW5nIGxvZ2ljIG9mIHRoZSBjdXN0b21lcnMgdGFibGUuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2N1c3RvbWVyc190YWJsZV9jb250cm9sbGVyXG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnY3VzdG9tZXJzX3RhYmxlX2NvbnRyb2xsZXInLFxuXHRcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9idXR0b25fZHJvcGRvd24nXG5cdF0sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9jdXN0b21lcnNfdGFibGVfY29udHJvbGxlciAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBSZWZlcmVuY2UgdG8gdGhlIGFjdHVhbCBmaWxlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7c3RyaW5nfVxuXHRcdFx0ICovXG5cdFx0XHRzcmNQYXRoID0gd2luZG93LmxvY2F0aW9uLm9yaWdpbiArIHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZSxcblx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogUXVlcnkgcGFyYW1ldGVyIHN0cmluZ1xuXHRcdFx0ICogXG5cdFx0XHQgKiBAdHlwZSB7c3RyaW5nfVxuXHRcdFx0ICovXG5cdFx0XHRxdWVyeVN0cmluZyA9ICc/JyArICh3aW5kb3cubG9jYXRpb24uc2VhcmNoXG5cdFx0XHRcdFx0XHRcdFx0LnJlcGxhY2UoL1xcPy8sICcnKVxuXHRcdFx0XHRcdFx0XHRcdC5yZXBsYWNlKC9jSUQ9W1xcZF0rL2csICcnKVxuXHRcdFx0XHRcdFx0XHRcdC5yZXBsYWNlKC9hY3Rpb249W1xcd10rL2csICcnKVxuXHRcdFx0XHRcdFx0XHRcdC5jb25jYXQoJyYnKVxuXHRcdFx0XHRcdFx0XHRcdC5yZXBsYWNlKC8mWyZdKy9nLCAnJicpXG5cdFx0XHRcdFx0XHRcdFx0LnJlcGxhY2UoL14mL2csICcnKSk7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBNRVRIT0RTXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogTWFwIGFjdGlvbnMgZm9yIGV2ZXJ5IHJvdyBpbiB0aGUgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBUaGlzIG1ldGhvZCB3aWxsIG1hcCB0aGUgYWN0aW9ucyBmb3IgZWFjaFxuXHRcdCAqIHJvdyBvZiB0aGUgdGFibGUuXG5cdFx0ICpcblx0XHQgKiBAcHJpdmF0ZVxuXHRcdCAqL1xuXHRcdHZhciBfbWFwUm93QWN0aW9ucyA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gSXRlcmF0ZSBvdmVyIHRhYmxlIHJvd3MsIGV4Y2VwdCB0aGUgaGVhZGVyIHJvd1xuXHRcdFx0JCgnLmd4LWN1c3RvbWVyLW92ZXJ2aWV3IHRyJykubm90KCcuZGF0YVRhYmxlSGVhZGluZ1JvdycpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFxuXHRcdFx0XHQvKipcblx0XHRcdFx0ICogU2F2ZSB0aGF0IFwidGhpc1wiIHNjb3BlIGhlcmVcblx0XHRcdFx0ICogQHZhciB7b2JqZWN0IHwgalF1ZXJ5fVxuXHRcdFx0XHQgKi9cblx0XHRcdFx0dmFyICR0aGF0ID0gJCh0aGlzKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8qKlxuXHRcdFx0XHQgKiBEYXRhIGF0dHJpYnV0ZXMgb2YgY3VycmVudCByb3dcblx0XHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0XHQgKi9cblx0XHRcdFx0dmFyIGRhdGEgPSAkdGhhdC5kYXRhKCk7XG5cdFx0XHRcdFxuXHRcdFx0XHQvKipcblx0XHRcdFx0ICogUmVmZXJlbmNlIHRvIHRoZSByb3cgYWN0aW9uIGRyb3Bkb3duXG5cdFx0XHRcdCAqIEB2YXIge29iamVjdCB8IGpRdWVyeX1cblx0XHRcdFx0ICovXG5cdFx0XHRcdHZhciAkZHJvcGRvd24gPSAkdGhhdC5maW5kKCcuanMtYnV0dG9uLWRyb3Bkb3duJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHRpZiAoJGRyb3Bkb3duLmxlbmd0aCkge1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEFkZCBjbGljayBldmVudCB0byB0aGUgdGFibGUgcm93IGFuZCBvcGVuIHRoZVxuXHRcdFx0XHRcdC8vIGN1c3RvbWVyIGRldGFpbCB2aWV3XG5cdFx0XHRcdFx0JHRoYXRcblx0XHRcdFx0XHRcdC5maW5kKCcuYnRuLWVkaXQnKS5jbG9zZXN0KCd0cicpXG5cdFx0XHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHRcdFx0Y3Vyc29yOiAncG9pbnRlcidcblx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHQub24oJ2NsaWNrJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0XHRcdFx0Ly8gQ29tcG9zZSB0aGUgVVJMIGFuZCBvcGVuIGl0XG5cdFx0XHRcdFx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdFx0XHRcdFx0c3JjUGF0aCxcblx0XHRcdFx0XHRcdFx0XHQnP2NJRD0nICsgZGF0YS5yb3dJZCxcblx0XHRcdFx0XHRcdFx0XHQnJmFjdGlvbj1lZGl0J1xuXHRcdFx0XHRcdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0XHRcdFx0XHRpZiAoJChldmVudC50YXJnZXQpLnByb3AoJ3RhZ05hbWUnKSA9PT0gJ1REJykge1xuXHRcdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0XHRcdFx0XHRcdH1cblx0XHRcdFx0XHRcdH0pO1xuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdC8vIEljb24gYmVoYXZpb3IgLSBFZGl0XG5cdFx0XHRcdFx0JHRoYXRcblx0XHRcdFx0XHRcdC5maW5kKCcuYnRuLWVkaXQnKVxuXHRcdFx0XHRcdFx0LmNzcyh7XG5cdFx0XHRcdFx0XHRcdGN1cnNvcjogJ3BvaW50ZXInXG5cdFx0XHRcdFx0XHR9KVxuXHRcdFx0XHRcdFx0LnByb3AoJ3RpdGxlJywganNlLmNvcmUubGFuZy50cmFuc2xhdGUoJ2VkaXQnLCAnYnV0dG9ucycpKVxuXHRcdFx0XHRcdFx0Lm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRcdFx0XHQvLyBDb21wb3NlIHRoZSBVUkwgYW5kIG9wZW4gaXRcblx0XHRcdFx0XHRcdFx0dmFyIHVybCA9IFtcblx0XHRcdFx0XHRcdFx0XHRzcmNQYXRoLFxuXHRcdFx0XHRcdFx0XHRcdCc/Y0lEPScgKyBkYXRhLnJvd0lkLFxuXHRcdFx0XHRcdFx0XHRcdCcmYWN0aW9uPWVkaXQnXG5cdFx0XHRcdFx0XHRcdF0uam9pbignJyk7XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHQvLyBJY29uIGJlaGF2aW9yIC0gRGVsZXRlXG5cdFx0XHRcdFx0aWYgKGRhdGEucm93SWQgIT09IDEpIHtcblx0XHRcdFx0XHRcdCR0aGF0XG5cdFx0XHRcdFx0XHRcdC5maW5kKCcuYnRuLWRlbGV0ZScpXG5cdFx0XHRcdFx0XHRcdC5jc3Moe1xuXHRcdFx0XHRcdFx0XHRcdGN1cnNvcjogJ3BvaW50ZXInXG5cdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdC5wcm9wKCd0aXRsZScsIGpzZS5jb3JlLmxhbmcudHJhbnNsYXRlKCdkZWxldGUnLCAnYnV0dG9ucycpKVxuXHRcdFx0XHRcdFx0XHQub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdFx0Ly8gQ29tcG9zZSB0aGUgVVJMIGFuZCBvcGVuIGl0XG5cdFx0XHRcdFx0XHRcdFx0dmFyIHVybCA9IFtcblx0XHRcdFx0XHRcdFx0XHRcdHNyY1BhdGgsXG5cdFx0XHRcdFx0XHRcdFx0XHRxdWVyeVN0cmluZyxcblx0XHRcdFx0XHRcdFx0XHRcdCdjSUQ9JyArIGRhdGEucm93SWQsXG5cdFx0XHRcdFx0XHRcdFx0XHQnJmFjdGlvbj1jb25maXJtJ1xuXHRcdFx0XHRcdFx0XHRcdF0uam9pbignJyk7XG5cdFx0XHRcdFx0XHRcdFx0d2luZG93Lm9wZW4odXJsLCAnX3NlbGYnKTtcblx0XHRcdFx0XHRcdFx0fSk7XG5cdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFxuXHRcdFx0XHRcdGlmIChkYXRhLnJvd0lkID09PSAxKSB7XG5cdFx0XHRcdFx0XHQkdGhhdC5maW5kKCcuYnRuLWRlbGV0ZScpLmNzcyh7XG5cdFx0XHRcdFx0XHRcdG9wYWNpdHk6ICcwLjInXG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHR9XG5cdFx0XHRcdFx0XG5cdFx0XHRcdFx0Ly8gSWNvbiBiZWhhdmlvciAtIE9yZGVyc1xuXHRcdFx0XHRcdCR0aGF0XG5cdFx0XHRcdFx0XHQuZmluZCgnLmJ0bi1vcmRlcicpXG5cdFx0XHRcdFx0XHQuY3NzKHtcblx0XHRcdFx0XHRcdFx0Y3Vyc29yOiAncG9pbnRlcidcblx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHQucHJvcCgndGl0bGUnLCBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnQlVUVE9OX09SREVSUycsICdhZG1pbl9idXR0b25zJykpXG5cdFx0XHRcdFx0XHQub24oJ2NsaWNrJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0XHRcdC8vIENvbXBvc2UgdGhlIFVSTCBhbmQgb3BlbiBpdFxuXHRcdFx0XHRcdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHRcdFx0XHRcdHNyY1BhdGgucmVwbGFjZSgnY3VzdG9tZXJzLnBocCcsICdhZG1pbi5waHAnKSxcblx0XHRcdFx0XHRcdFx0XHQnPycgKyAkLnBhcmFtKHtcblx0XHRcdFx0XHRcdFx0XHRcdGRvOiAnT3JkZXJzT3ZlcnZpZXcnLCBcblx0XHRcdFx0XHRcdFx0XHRcdGZpbHRlcjoge1xuXHRcdFx0XHRcdFx0XHRcdFx0XHRjdXN0b21lcjogJyMnICsgZGF0YS5yb3dJZFxuXHRcdFx0XHRcdFx0XHRcdFx0fVxuXHRcdFx0XHRcdFx0XHRcdH0pXG5cdFx0XHRcdFx0XHRcdF0uam9pbignJyk7XG5cdFx0XHRcdFx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0XHRcdFx0XHR9KTtcblx0XHRcdFx0XHRcblx0XHRcdFx0XHRfbWFwQnV0dG9uRHJvcGRvd24oJHRoYXQsICRkcm9wZG93biwgZGF0YSk7XG5cdFx0XHRcdH1cblx0XHRcdH0pO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tYXBCdXR0b25Ecm9wZG93biA9IGZ1bmN0aW9uKCR0aGF0LCAkZHJvcGRvd24sIGRhdGEpIHtcblx0XHRcdHZhciBhY3Rpb25zID0gWydlZGl0J107XG5cdFx0XHRcblx0XHRcdC8vIEJpbmQgZHJvcGRvd24gb3B0aW9uIC0gRGVsZXRlXG5cdFx0XHRpZiAoZGF0YS5yb3dJZCAhPT0gMSkge1xuXHRcdFx0XHRhY3Rpb25zLnB1c2goJ2RlbGV0ZScpO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRhY3Rpb25zID0gYWN0aW9ucy5jb25jYXQoW1xuXHRcdFx0XHQnQlVUVE9OX1NUQVRVUycsXG5cdFx0XHRcdCdCVVRUT05fT1JERVJTJyxcblx0XHRcdFx0J0JVVFRPTl9FTUFJTCcsXG5cdFx0XHRcdCdCVVRUT05fSVBMT0cnLFxuXHRcdFx0XHQnQlVUVE9OX05FV19PUkRFUidcblx0XHRcdF0pO1xuXHRcdFx0XG5cdFx0XHQvLyBBZG1pbiByaWdodHMgYnV0dG9uXG5cdFx0XHRpZiAoJHRoYXQuZmluZCgnW2RhdGEtY3VzdC1ncm91cF0nKS5kYXRhKCdjdXN0R3JvdXAnKSA9PT0gMCAmJlxuXHRcdFx0XHQkdGhhdC5maW5kKCdbZGF0YS1jdXN0LWlkXScpLmRhdGEoJ2N1c3RJZCcpICE9PSAxKSB7XG5cdFx0XHRcdGFjdGlvbnMucHVzaCgnQlVUVE9OX0FDQ09VTlRJTkcnKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gQmluZCBNYWlsQmVleiBkcm9wZG93biBvcHRpb25zLlxuXHRcdFx0dmFyIG1haWxCZWV6Q29udmVyc2F0aW9uc1NlbGVjdG9yID1cblx0XHRcdFx0Jy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fcmlnaHQnO1xuXHRcdFx0aWYgKCQobWFpbEJlZXpDb252ZXJzYXRpb25zU2VsZWN0b3IpLmxlbmd0aCkge1xuXHRcdFx0XHRhY3Rpb25zLnB1c2goJ01BSUxCRUVaX09WRVJWSUVXJyk7XG5cdFx0XHRcdGFjdGlvbnMucHVzaCgnTUFJTEJFRVpfTk9USUZJQ0FUSU9OUycpO1xuXHRcdFx0XHRhY3Rpb25zLnB1c2goJ01BSUxCRUVaX0NPTlZFUlNBVElPTlMnKTtcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Ly8gQmluZCBNZWRpYWZpbmFueiBkcm9wZG93biBvcHRpb25zLlxuXHRcdFx0dmFyICRtZWRpYWZpbmFuekFjdGlvbiA9ICQoJy5tZWRpYWZpbmFuei1jcmVkaXR3b3J0aGluZXNzJyk7IFxuXHRcdFx0aWYgKCRtZWRpYWZpbmFuekFjdGlvbi5sZW5ndGgpIHtcblx0XHRcdFx0YWN0aW9ucy5wdXNoKCdCVVRUT05fTUVESUFGSU5BTlpfQ1JFRElUV09SVEhJTkVTUycpOyBcblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0Zm9yICh2YXIgaW5kZXggaW4gYWN0aW9ucykge1xuXHRcdFx0XHRfbWFwQ3VzdG9tZXJBY3Rpb24oJGRyb3Bkb3duLCBhY3Rpb25zW2luZGV4XSwgZGF0YSk7XG5cdFx0XHR9XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX21hcEN1c3RvbWVyQWN0aW9uID0gZnVuY3Rpb24oJGRyb3Bkb3duLCBhY3Rpb24sIGRhdGEpIHtcblx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oJGRyb3Bkb3duLCBhY3Rpb24sIF9zZWN0aW9uTWFwcGluZ1thY3Rpb25dLCBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XHRfZXhlY3V0ZUFjdGlvbkNhbGxiYWNrKGFjdGlvbiwgZGF0YSk7XG5cdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfc2VjdGlvbk1hcHBpbmcgPSB7XG5cdFx0XHRlZGl0OiAnYnV0dG9ucycsXG5cdFx0XHRkZWxldGU6ICdidXR0b25zJyxcblx0XHRcdEJVVFRPTl9TVEFUVVM6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9PUkRFUlM6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9FTUFJTDogJ2FkbWluX2J1dHRvbnMnLFxuXHRcdFx0QlVUVE9OX0lQTE9HOiAnYWRtaW5fYnV0dG9ucycsXG5cdFx0XHRNQUlMQkVFWl9PVkVSVklFVzogJ2FkbWluX2N1c3RvbWVycycsXG5cdFx0XHRNQUlMQkVFWl9OT1RJRklDQVRJT05TOiAnYWRtaW5fY3VzdG9tZXJzJyxcblx0XHRcdE1BSUxCRUVaX0NPTlZFUlNBVElPTlM6ICdhZG1pbl9jdXN0b21lcnMnLFxuXHRcdFx0QlVUVE9OX01FRElBRklOQU5aX0NSRURJVFdPUlRISU5FU1M6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9ORVdfT1JERVI6ICdhZG1pbl9idXR0b25zJyxcblx0XHRcdEJVVFRPTl9BQ0NPVU5USU5HOiAnYWRtaW5fYnV0dG9ucydcblx0XHR9O1xuXHRcdFxuXHRcdC8qKlxuXHRcdCAqIEdldCB0aGUgY29ycmVzcG9uZGluZyBjYWxsYmFja1xuXHRcdCAqXG5cdFx0ICogQHBhcmFtIGFjdGlvblxuXHRcdCAqIEBwcml2YXRlXG5cdFx0ICovXG5cdFx0dmFyIF9leGVjdXRlQWN0aW9uQ2FsbGJhY2sgPSBmdW5jdGlvbihhY3Rpb24sIGRhdGEpIHtcblx0XHRcdHN3aXRjaCAoYWN0aW9uKSB7XG5cdFx0XHRcdGNhc2UgJ2VkaXQnOlxuXHRcdFx0XHRcdF9lZGl0Q2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ2RlbGV0ZSc6XG5cdFx0XHRcdFx0X2RlbGV0ZUNhbGxiYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fU1RBVFVTJzpcblx0XHRcdFx0XHRfY3VzdG9tZXJHcm91cENhbGxCYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdCVVRUT05fT1JERVJTJzpcblx0XHRcdFx0XHRfb3JkZXJzQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9FTUFJTCc6XG5cdFx0XHRcdFx0X2VtYWlsQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9JUExPRyc6XG5cdFx0XHRcdFx0X2lwTG9nQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ01BSUxCRUVaX09WRVJWSUVXJzpcblx0XHRcdFx0XHRfbWFpbEJlZXpPdmVydmlld0NhbGxiYWNrKGRhdGEpO1xuXHRcdFx0XHRcdGJyZWFrO1xuXHRcdFx0XHRjYXNlICdNQUlMQkVFWl9OT1RJRklDQVRJT05TJzpcblx0XHRcdFx0XHRfbWFpbEJlZXpOb3RpZmljYXRpb25zQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ01BSUxCRUVaX0NPTlZFUlNBVElPTlMnOlxuXHRcdFx0XHRcdF9tYWlsQmVlekNvbnZlcnNhdGlvbnNDYWxsYmFjayhkYXRhKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX01FRElBRklOQU5aX0NSRURJVFdPUlRISU5FU1MnOlxuXHRcdFx0XHRcdF9tZWRpYWZpbmFuekNyZWRpdHdvcnRoaW5lc3NDYWxsYmFjayhkYXRhKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0Y2FzZSAnQlVUVE9OX05FV19PUkRFUic6XG5cdFx0XHRcdFx0X25ld09yZGVyQ2FsbGJhY2soZGF0YSk7XG5cdFx0XHRcdFx0YnJlYWs7XG5cdFx0XHRcdGNhc2UgJ0JVVFRPTl9BQ0NPVU5USU5HJzpcblx0XHRcdFx0XHRfYWRtaW5SaWdodHNDYWxsYmFjayhkYXRhKTtcblx0XHRcdFx0XHRicmVhaztcblx0XHRcdFx0ZGVmYXVsdDpcblx0XHRcdFx0XHR0aHJvdyBuZXcgRXJyb3IoJ0NhbGxiYWNrIG5vdCBmb3VuZC4nKTtcblx0XHRcdH1cblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfZWRpdENhbGxiYWNrID0gZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0Ly8gQ29tcG9zZSB0aGUgVVJMIGFuZCBvcGVuIGl0XG5cdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHRzcmNQYXRoLFxuXHRcdFx0XHQnP2NJRD0nICsgZGF0YS5yb3dJZCxcblx0XHRcdFx0JyZhY3Rpb249ZWRpdCdcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9kZWxldGVDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdC8vIENvbXBvc2UgdGhlIFVSTCBhbmQgb3BlbiBpdFxuXHRcdFx0dmFyIHVybCA9IFtcblx0XHRcdFx0c3JjUGF0aCxcblx0XHRcdFx0cXVlcnlTdHJpbmcsXG5cdFx0XHRcdCdjSUQ9JyArIGRhdGEucm93SWQsXG5cdFx0XHRcdCcmYWN0aW9uPWNvbmZpcm0nXG5cdFx0XHRdLmpvaW4oJycpO1xuXHRcdFx0d2luZG93Lm9wZW4odXJsLCAnX3NlbGYnKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfY3VzdG9tZXJHcm91cENhbGxCYWNrID0gZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0Ly8gQ29tcG9zZSB0aGUgVVJMIGFuZCBvcGVuIGl0XG5cdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHRzcmNQYXRoLFxuXHRcdFx0XHRxdWVyeVN0cmluZyxcblx0XHRcdFx0J2NJRD0nICsgZGF0YS5yb3dJZCxcblx0XHRcdFx0JyZhY3Rpb249ZWRpdHN0YXR1cydcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9vcmRlcnNDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdC8vIENvbXBvc2UgdGhlIFVSTCBhbmQgb3BlbiBpdFxuXHRcdFx0dmFyIHVybCA9IFtcblx0XHRcdFx0c3JjUGF0aC5yZXBsYWNlKCdjdXN0b21lcnMucGhwJywgJ2FkbWluLnBocCcpLFxuXHRcdFx0XHQnPycgKyAkLnBhcmFtKHtcblx0XHRcdFx0XHRkbzogJ09yZGVyc092ZXJ2aWV3Jyxcblx0XHRcdFx0XHRmaWx0ZXI6IHtcblx0XHRcdFx0XHRcdGN1c3RvbWVyOiAnIycgKyBkYXRhLnJvd0lkXG5cdFx0XHRcdFx0fVxuXHRcdFx0XHR9KVxuXHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2VtYWlsQ2FsbGJhY2sgPSBmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHQvLyBDb21wb3NlIHRoZSBVUkwgYW5kIG9wZW4gaXRcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdHNyY1BhdGgucmVwbGFjZSgnY3VzdG9tZXJzLnBocCcsICdtYWlsLnBocCcpLFxuXHRcdFx0XHQnP3NlbGVjdGVkX2JveD10b29scycsXG5cdFx0XHRcdCcmY3VzdG9tZXI9JyArIGRhdGEuY3VzdEVtYWlsLFxuXHRcdFx0XS5qb2luKCcnKTtcblx0XHRcdHdpbmRvdy5vcGVuKHVybCwgJ19zZWxmJyk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX2lwTG9nQ2FsbGJhY2sgPSBmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHQvLyBDb21wb3NlIHRoZSBVUkwgYW5kIG9wZW4gaXRcblx0XHRcdHZhciB1cmwgPSBbXG5cdFx0XHRcdHNyY1BhdGgsXG5cdFx0XHRcdHF1ZXJ5U3RyaW5nLFxuXHRcdFx0XHQnY0lEPScgKyBkYXRhLnJvd0lkLFxuXHRcdFx0XHQnJmFjdGlvbj1pcGxvZydcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tYWlsQmVlek92ZXJ2aWV3Q2FsbGJhY2sgPSBmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHR2YXIgJHRhcmdldCA9ICQoJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fbGVmdCcpO1xuXHRcdFx0dmFyIHVybCA9ICQoJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fbGVmdCcpLmF0dHIoXG5cdFx0XHRcdCdvbmNsaWNrJyk7XG5cdFx0XHR1cmwgPSB1cmwucmVwbGFjZSgvY0lEPSguKikmLywgJ2NJRD0nICsgZGF0YS5yb3dJZCArICcmJyk7XG5cdFx0XHQkKCcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmNvbnRleHRfdmlld19idXR0b24uYnRuX2xlZnQnKS5hdHRyKCdvbmNsaWNrJywgdXJsKTtcblx0XHRcdCR0YXJnZXQuZ2V0KDApLmNsaWNrKCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX21haWxCZWV6Tm90aWZpY2F0aW9uc0NhbGxiYWNrID0gZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0dmFyICR0YXJnZXQgPSAkKCcuY29udGVudFRhYmxlIC5pbmZvQm94Q29udGVudCBhLmNvbnRleHRfdmlld19idXR0b24uYnRuX21pZGRsZScpO1xuXHRcdFx0dmFyIHVybCA9ICQoJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fbWlkZGxlJykuYXR0cihcblx0XHRcdFx0J29uY2xpY2snKTtcblx0XHRcdHVybCA9IHVybC5yZXBsYWNlKC9jSUQ9KC4qKSYvLCAnY0lEPScgKyBkYXRhLnJvd0lkICsgJyYnKTtcblx0XHRcdCQoJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fbWlkZGxlJykuYXR0cignb25jbGljaycsXG5cdFx0XHRcdHVybCk7XG5cdFx0XHQkdGFyZ2V0LmdldCgwKS5jbGljaygpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9tYWlsQmVlekNvbnZlcnNhdGlvbnNDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdHZhciAkdGFyZ2V0ID0gJCgnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5jb250ZXh0X3ZpZXdfYnV0dG9uLmJ0bl9yaWdodCcpO1xuXHRcdFx0dmFyIHVybCA9ICQoJy5jb250ZW50VGFibGUgLmluZm9Cb3hDb250ZW50IGEuY29udGV4dF92aWV3X2J1dHRvbi5idG5fcmlnaHQnKS5hdHRyKFxuXHRcdFx0XHQnb25jbGljaycpO1xuXHRcdFx0dXJsID0gdXJsLnJlcGxhY2UoL2NJRD0oLiopJi8sICdjSUQ9JyArIGRhdGEucm93SWQgKyAnJicpO1xuXHRcdFx0JCgnLmNvbnRlbnRUYWJsZSAuaW5mb0JveENvbnRlbnQgYS5jb250ZXh0X3ZpZXdfYnV0dG9uLmJ0bl9yaWdodCcpLmF0dHIoJ29uY2xpY2snLFxuXHRcdFx0XHR1cmwpO1xuXHRcdFx0JHRhcmdldC5nZXQoMCkuY2xpY2soKTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfbWVkaWFmaW5hbnpDcmVkaXR3b3J0aGluZXNzQ2FsbGJhY2sgPSBmdW5jdGlvbihkYXRhKSB7XG5cdFx0XHR2YXIgJHRhcmdldCA9ICQoJy5tZWRpYWZpbmFuei1jcmVkaXR3b3J0aGluZXNzJyk7XG5cdFx0XHR2YXIgb25jbGlja0F0dHJpYnV0ZSA9ICR0YXJnZXQuYXR0cignb25jbGljaycpO1xuXHRcdFx0Ly8gUmVwbGFjZSB0aGUgY3VzdG9tZXIgbnVtYmVyIGluIHRoZSBvbmNsaWNrIGF0dHJpYnV0ZS4gXG5cdFx0XHRvbmNsaWNrQXR0cmlidXRlID0gb25jbGlja0F0dHJpYnV0ZS5yZXBsYWNlKC9jSUQ9KC4qJywgJ3BvcHVwJykvLCAnY0lEPScgKyBkYXRhLnJvd0lkICsgJ1xcJywgXFwncG9wdXBcXCcnKTsgXG5cdFx0XHQkdGFyZ2V0LmF0dHIoJ29uY2xpY2snLCBvbmNsaWNrQXR0cmlidXRlKTtcblx0XHRcdCR0YXJnZXQudHJpZ2dlcignY2xpY2snKTsgLy8gVHJpZ2dlciB0aGUgY2xpY2sgZXZlbnQgaW4gdGhlIDxhPiBlbGVtZW50LiBcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfbmV3T3JkZXJDYWxsYmFjayA9IGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcdC8vIENvbXBvc2UgdGhlIFVSTCBhbmQgb3BlbiBpdFxuXHRcdFx0dmFyIHVybCA9IFtcblx0XHRcdFx0c3JjUGF0aCxcblx0XHRcdFx0Jz9jSUQ9JyArIGRhdGEucm93SWQsXG5cdFx0XHRcdCcmYWN0aW9uPW5ld19vcmRlcidcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0dmFyIF9hZG1pblJpZ2h0c0NhbGxiYWNrID0gZnVuY3Rpb24oZGF0YSkge1xuXHRcdFx0Ly8gQ29tcG9zZSB0aGUgVVJMIGFuZCBvcGVuIGl0XG5cdFx0XHR2YXIgdXJsID0gW1xuXHRcdFx0XHRzcmNQYXRoLnJlcGxhY2UoJ2N1c3RvbWVycy5waHAnLCAnYWNjb3VudGluZy5waHAnKSxcblx0XHRcdFx0Jz9jSUQ9JyArIGRhdGEucm93SWRcblx0XHRcdF0uam9pbignJyk7XG5cdFx0XHR3aW5kb3cub3Blbih1cmwsICdfc2VsZicpO1xuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdC8vIFdhaXQgdW50aWwgdGhlIGJ1dHRvbnMgYXJlIGNvbnZlcnRlZCB0byBkcm9wZG93biBmb3IgZXZlcnkgcm93LlxuXHRcdFx0dmFyIGludGVydmFsID0gc2V0SW50ZXJ2YWwoZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICgkKCcuanMtYnV0dG9uLWRyb3Bkb3duJykubGVuZ3RoKSB7XG5cdFx0XHRcdFx0Y2xlYXJJbnRlcnZhbChpbnRlcnZhbCk7XG5cdFx0XHRcdFx0X21hcFJvd0FjdGlvbnMoKTtcblx0XHRcdFx0fVxuXHRcdFx0fSwgNTAwKTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
