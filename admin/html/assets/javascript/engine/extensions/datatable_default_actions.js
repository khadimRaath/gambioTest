'use strict';

/* --------------------------------------------------------------
 datatable_default_actions.js 2016-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Default Dropdown Actions
 *
 * This extension will handle the "defaultRowAction" and "defaultBulkAction" data attributes of the table upon
 * initialization or user click.
 *
 * ### Options
 *
 * **Default Row Action | `data-datatable_default_actions-row` | String | Required**
 *
 * Provide the default row action. This will automatically be mapped to the defaultRowAction data value of the table.
 *
 * **Default Bulk Action | `data-datatable_default_actions-bulk` | String | Required**
 *
 * Provide the default bulk action. This will automatically be mapped to the defaultBulkAction data value of the table.
 *
 * **Bulk Action Selector | `data-datatable_default_actions-bulk-action-selector` | String | Optional**
 *
 * Provide a selector for the bulk action dropdown widget. The default value is '.bulk-action'.
 *
 * ### Methods
 *
 * **Ensure Default Task**
 *
 * This method will make sure that there is a default task selected. Call it after you setup the row or bulk dropdown
 * actions. Sometimes the user_configuration db value might contain a default value that is not present in the dropdowns
 * anymore (e.g. removed module). In order to make sure that there will always be a default value use this method after
 * creating the dropdown actions and it will use the first dropdown action as default if needed.
 *
 * ```javascript
 * // Ensure default row actions.
 * $('.table-main').datatable_default_actions('ensure', 'row');
 *
 * // Ensure default bulk actions.
 * $('.table-main').datatable_default_actions('ensure', 'bulk');
 * ```
 *
 * @module Admin/extensions/datatable_default_actions
 */
gx.extensions.module('datatable_default_actions', [gx.source + '/libs/button_dropdown'], function (data) {

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
  * Default Options
  *
  * @type {Object}
  */
	var defaults = {
		bulkActionSelector: '.bulk-action'
	};

	/**
  * Final Options
  *
  * @type {Object}
  */
	var options = $.extend(true, {}, defaults, data);

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
  * Ensure that there will be a default action in the row or bulk dropdowns.
  *
  * @param {String} type Can be whether 'row' or 'bulk'.
  */
	function _ensure(type) {
		var $table = $(this);

		switch (type) {
			case 'row':
				var $rowActions = $table.find('tbody .btn-group.dropdown');

				if ($rowActions.eq(0).find('button:first').text() === '') {
					var $actionLink = $rowActions.eq(0).find('ul li:first a');
					jse.libs.button_dropdown.setDefaultAction($rowActions, $actionLink);
				}

				break;

			case 'bulk':
				var $bulkAction = $(options.bulkActionSelector);

				if ($bulkAction.find('button:first').text() === '') {
					var _$actionLink = $bulkAction.find('ul li:first a');
					jse.libs.button_dropdown.setDefaultAction($bulkAction, _$actionLink);
				}

				break;

			default:
				throw new Error('Invalid "ensure" type given (expected "row" or "bulk" got : "' + type + '").');
		}
	}

	/**
  * On Button Drodpown Action Click
  *
  * Update the defaultBulkAction and defaultRowAction data attributes.
  */
	function _onButtonDropdownActionClick() {
		var property = $(this).parents('.btn-group')[0] === $(options.bulkActionSelector)[0] ? 'defaultBulkAction' : 'defaultRowAction';

		$this.data(property, $(this).data('configurationValue'));
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.data({
			defaultRowAction: options.row,
			defaultBulkAction: options.bulk
		});

		$this.on('click', '.btn-group.dropdown a', _onButtonDropdownActionClick);
		$('body').on('click', options.bulkActionSelector, _onButtonDropdownActionClick);

		// Bind module api to jQuery object. 
		$.fn.extend({
			datatable_default_actions: function datatable_default_actions(action) {
				switch (action) {
					case 'ensure':
						for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
							args[_key - 1] = arguments[_key];
						}

						return _ensure.apply(this, args);
				}
			}
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwiYnVsa0FjdGlvblNlbGVjdG9yIiwib3B0aW9ucyIsImV4dGVuZCIsIl9lbnN1cmUiLCJ0eXBlIiwiJHRhYmxlIiwiJHJvd0FjdGlvbnMiLCJmaW5kIiwiZXEiLCJ0ZXh0IiwiJGFjdGlvbkxpbmsiLCJqc2UiLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwic2V0RGVmYXVsdEFjdGlvbiIsIiRidWxrQWN0aW9uIiwiRXJyb3IiLCJfb25CdXR0b25Ecm9wZG93bkFjdGlvbkNsaWNrIiwicHJvcGVydHkiLCJwYXJlbnRzIiwiaW5pdCIsImRvbmUiLCJkZWZhdWx0Um93QWN0aW9uIiwicm93IiwiZGVmYXVsdEJ1bGtBY3Rpb24iLCJidWxrIiwib24iLCJmbiIsImRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMiLCJhY3Rpb24iLCJhcmdzIiwiYXBwbHkiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBdUNBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FBcUIsMkJBQXJCLEVBQWtELENBQUlGLEdBQUdHLE1BQVAsMkJBQWxELEVBQXlGLFVBQVNDLElBQVQsRUFBZTs7QUFFdkc7O0FBRUE7QUFDQTtBQUNBOzs7QUFHQTs7Ozs7O0FBS0EsS0FBTUMsUUFBUUMsRUFBRSxJQUFGLENBQWQ7O0FBRUE7Ozs7O0FBS0EsS0FBTUMsV0FBVztBQUNoQkMsc0JBQW9CO0FBREosRUFBakI7O0FBSUE7Ozs7O0FBS0EsS0FBTUMsVUFBVUgsRUFBRUksTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSCxRQUFuQixFQUE2QkgsSUFBN0IsQ0FBaEI7O0FBRUE7Ozs7O0FBS0EsS0FBTUYsU0FBUyxFQUFmOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7QUFLQSxVQUFTUyxPQUFULENBQWlCQyxJQUFqQixFQUF1QjtBQUN0QixNQUFNQyxTQUFTUCxFQUFFLElBQUYsQ0FBZjs7QUFFQSxVQUFRTSxJQUFSO0FBQ0MsUUFBSyxLQUFMO0FBQ0MsUUFBTUUsY0FBY0QsT0FBT0UsSUFBUCxDQUFZLDJCQUFaLENBQXBCOztBQUVBLFFBQUlELFlBQVlFLEVBQVosQ0FBZSxDQUFmLEVBQWtCRCxJQUFsQixDQUF1QixjQUF2QixFQUF1Q0UsSUFBdkMsT0FBa0QsRUFBdEQsRUFBMEQ7QUFDekQsU0FBTUMsY0FBY0osWUFBWUUsRUFBWixDQUFlLENBQWYsRUFBa0JELElBQWxCLENBQXVCLGVBQXZCLENBQXBCO0FBQ0FJLFNBQUlDLElBQUosQ0FBU0MsZUFBVCxDQUF5QkMsZ0JBQXpCLENBQTBDUixXQUExQyxFQUF1REksV0FBdkQ7QUFDQTs7QUFFRDs7QUFFRCxRQUFLLE1BQUw7QUFDQyxRQUFNSyxjQUFjakIsRUFBRUcsUUFBUUQsa0JBQVYsQ0FBcEI7O0FBRUEsUUFBSWUsWUFBWVIsSUFBWixDQUFpQixjQUFqQixFQUFpQ0UsSUFBakMsT0FBNEMsRUFBaEQsRUFBb0Q7QUFDbkQsU0FBTUMsZUFBY0ssWUFBWVIsSUFBWixDQUFpQixlQUFqQixDQUFwQjtBQUNBSSxTQUFJQyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJDLGdCQUF6QixDQUEwQ0MsV0FBMUMsRUFBdURMLFlBQXZEO0FBQ0E7O0FBRUQ7O0FBRUQ7QUFDQyxVQUFNLElBQUlNLEtBQUosbUVBQTBFWixJQUExRSxTQUFOO0FBdEJGO0FBd0JBOztBQUVEOzs7OztBQUtBLFVBQVNhLDRCQUFULEdBQXdDO0FBQ3ZDLE1BQU1DLFdBQVdwQixFQUFFLElBQUYsRUFBUXFCLE9BQVIsQ0FBZ0IsWUFBaEIsRUFBOEIsQ0FBOUIsTUFBcUNyQixFQUFFRyxRQUFRRCxrQkFBVixFQUE4QixDQUE5QixDQUFyQyxHQUNkLG1CQURjLEdBQ1Esa0JBRHpCOztBQUdBSCxRQUFNRCxJQUFOLENBQVdzQixRQUFYLEVBQXFCcEIsRUFBRSxJQUFGLEVBQVFGLElBQVIsQ0FBYSxvQkFBYixDQUFyQjtBQUNBOztBQUVEO0FBQ0E7QUFDQTs7QUFFQUYsUUFBTzBCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJ4QixRQUFNRCxJQUFOLENBQVc7QUFDVjBCLHFCQUFrQnJCLFFBQVFzQixHQURoQjtBQUVWQyxzQkFBbUJ2QixRQUFRd0I7QUFGakIsR0FBWDs7QUFLQTVCLFFBQU02QixFQUFOLENBQVMsT0FBVCxFQUFrQix1QkFBbEIsRUFBMkNULDRCQUEzQztBQUNBbkIsSUFBRSxNQUFGLEVBQVU0QixFQUFWLENBQWEsT0FBYixFQUFzQnpCLFFBQVFELGtCQUE5QixFQUFrRGlCLDRCQUFsRDs7QUFFQTtBQUNBbkIsSUFBRTZCLEVBQUYsQ0FBS3pCLE1BQUwsQ0FBWTtBQUNYMEIsOEJBQTJCLG1DQUFTQyxNQUFULEVBQTBCO0FBQ3BELFlBQVFBLE1BQVI7QUFDQyxVQUFLLFFBQUw7QUFBQSx3Q0FGNkNDLElBRTdDO0FBRjZDQSxXQUU3QztBQUFBOztBQUNDLGFBQU8zQixRQUFRNEIsS0FBUixDQUFjLElBQWQsRUFBb0JELElBQXBCLENBQVA7QUFGRjtBQUlBO0FBTlUsR0FBWjs7QUFTQVQ7QUFDQSxFQXBCRDs7QUFzQkEsUUFBTzNCLE1BQVA7QUFFQSxDQXJIRCIsImZpbGUiOiJkYXRhdGFibGVfZGVmYXVsdF9hY3Rpb25zLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMuanMgMjAxNi0wNi0wOVxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiAjIyBFbmFibGUgRGVmYXVsdCBEcm9wZG93biBBY3Rpb25zXHJcbiAqXHJcbiAqIFRoaXMgZXh0ZW5zaW9uIHdpbGwgaGFuZGxlIHRoZSBcImRlZmF1bHRSb3dBY3Rpb25cIiBhbmQgXCJkZWZhdWx0QnVsa0FjdGlvblwiIGRhdGEgYXR0cmlidXRlcyBvZiB0aGUgdGFibGUgdXBvblxyXG4gKiBpbml0aWFsaXphdGlvbiBvciB1c2VyIGNsaWNrLlxyXG4gKlxyXG4gKiAjIyMgT3B0aW9uc1xyXG4gKlxyXG4gKiAqKkRlZmF1bHQgUm93IEFjdGlvbiB8IGBkYXRhLWRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMtcm93YCB8IFN0cmluZyB8IFJlcXVpcmVkKipcclxuICpcclxuICogUHJvdmlkZSB0aGUgZGVmYXVsdCByb3cgYWN0aW9uLiBUaGlzIHdpbGwgYXV0b21hdGljYWxseSBiZSBtYXBwZWQgdG8gdGhlIGRlZmF1bHRSb3dBY3Rpb24gZGF0YSB2YWx1ZSBvZiB0aGUgdGFibGUuXHJcbiAqXHJcbiAqICoqRGVmYXVsdCBCdWxrIEFjdGlvbiB8IGBkYXRhLWRhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnMtYnVsa2AgfCBTdHJpbmcgfCBSZXF1aXJlZCoqXHJcbiAqXHJcbiAqIFByb3ZpZGUgdGhlIGRlZmF1bHQgYnVsayBhY3Rpb24uIFRoaXMgd2lsbCBhdXRvbWF0aWNhbGx5IGJlIG1hcHBlZCB0byB0aGUgZGVmYXVsdEJ1bGtBY3Rpb24gZGF0YSB2YWx1ZSBvZiB0aGUgdGFibGUuXHJcbiAqXHJcbiAqICoqQnVsayBBY3Rpb24gU2VsZWN0b3IgfCBgZGF0YS1kYXRhdGFibGVfZGVmYXVsdF9hY3Rpb25zLWJ1bGstYWN0aW9uLXNlbGVjdG9yYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcclxuICpcclxuICogUHJvdmlkZSBhIHNlbGVjdG9yIGZvciB0aGUgYnVsayBhY3Rpb24gZHJvcGRvd24gd2lkZ2V0LiBUaGUgZGVmYXVsdCB2YWx1ZSBpcyAnLmJ1bGstYWN0aW9uJy5cclxuICpcclxuICogIyMjIE1ldGhvZHNcclxuICpcclxuICogKipFbnN1cmUgRGVmYXVsdCBUYXNrKipcclxuICpcclxuICogVGhpcyBtZXRob2Qgd2lsbCBtYWtlIHN1cmUgdGhhdCB0aGVyZSBpcyBhIGRlZmF1bHQgdGFzayBzZWxlY3RlZC4gQ2FsbCBpdCBhZnRlciB5b3Ugc2V0dXAgdGhlIHJvdyBvciBidWxrIGRyb3Bkb3duXHJcbiAqIGFjdGlvbnMuIFNvbWV0aW1lcyB0aGUgdXNlcl9jb25maWd1cmF0aW9uIGRiIHZhbHVlIG1pZ2h0IGNvbnRhaW4gYSBkZWZhdWx0IHZhbHVlIHRoYXQgaXMgbm90IHByZXNlbnQgaW4gdGhlIGRyb3Bkb3duc1xyXG4gKiBhbnltb3JlIChlLmcuIHJlbW92ZWQgbW9kdWxlKS4gSW4gb3JkZXIgdG8gbWFrZSBzdXJlIHRoYXQgdGhlcmUgd2lsbCBhbHdheXMgYmUgYSBkZWZhdWx0IHZhbHVlIHVzZSB0aGlzIG1ldGhvZCBhZnRlclxyXG4gKiBjcmVhdGluZyB0aGUgZHJvcGRvd24gYWN0aW9ucyBhbmQgaXQgd2lsbCB1c2UgdGhlIGZpcnN0IGRyb3Bkb3duIGFjdGlvbiBhcyBkZWZhdWx0IGlmIG5lZWRlZC5cclxuICpcclxuICogYGBgamF2YXNjcmlwdFxyXG4gKiAvLyBFbnN1cmUgZGVmYXVsdCByb3cgYWN0aW9ucy5cclxuICogJCgnLnRhYmxlLW1haW4nKS5kYXRhdGFibGVfZGVmYXVsdF9hY3Rpb25zKCdlbnN1cmUnLCAncm93Jyk7XHJcbiAqXHJcbiAqIC8vIEVuc3VyZSBkZWZhdWx0IGJ1bGsgYWN0aW9ucy5cclxuICogJCgnLnRhYmxlLW1haW4nKS5kYXRhdGFibGVfZGVmYXVsdF9hY3Rpb25zKCdlbnN1cmUnLCAnYnVsaycpO1xyXG4gKiBgYGBcclxuICpcclxuICogQG1vZHVsZSBBZG1pbi9leHRlbnNpb25zL2RhdGF0YWJsZV9kZWZhdWx0X2FjdGlvbnNcclxuICovXHJcbmd4LmV4dGVuc2lvbnMubW9kdWxlKCdkYXRhdGFibGVfZGVmYXVsdF9hY3Rpb25zJywgW2Ake2d4LnNvdXJjZX0vbGlicy9idXR0b25fZHJvcGRvd25gXSwgZnVuY3Rpb24oZGF0YSkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIERlZmF1bHQgT3B0aW9uc1xyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBkZWZhdWx0cyA9IHtcclxuXHRcdGJ1bGtBY3Rpb25TZWxlY3RvcjogJy5idWxrLWFjdGlvbidcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEZpbmFsIE9wdGlvbnNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEVuc3VyZSB0aGF0IHRoZXJlIHdpbGwgYmUgYSBkZWZhdWx0IGFjdGlvbiBpbiB0aGUgcm93IG9yIGJ1bGsgZHJvcGRvd25zLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtTdHJpbmd9IHR5cGUgQ2FuIGJlIHdoZXRoZXIgJ3Jvdycgb3IgJ2J1bGsnLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9lbnN1cmUodHlwZSkge1xyXG5cdFx0Y29uc3QgJHRhYmxlID0gJCh0aGlzKTtcclxuXHRcdFxyXG5cdFx0c3dpdGNoICh0eXBlKSB7XHJcblx0XHRcdGNhc2UgJ3Jvdyc6XHJcblx0XHRcdFx0Y29uc3QgJHJvd0FjdGlvbnMgPSAkdGFibGUuZmluZCgndGJvZHkgLmJ0bi1ncm91cC5kcm9wZG93bicpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmICgkcm93QWN0aW9ucy5lcSgwKS5maW5kKCdidXR0b246Zmlyc3QnKS50ZXh0KCkgPT09ICcnKSB7XHJcblx0XHRcdFx0XHRjb25zdCAkYWN0aW9uTGluayA9ICRyb3dBY3Rpb25zLmVxKDApLmZpbmQoJ3VsIGxpOmZpcnN0IGEnKTtcclxuXHRcdFx0XHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5zZXREZWZhdWx0QWN0aW9uKCRyb3dBY3Rpb25zLCAkYWN0aW9uTGluayk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcclxuXHRcdFx0Y2FzZSAnYnVsayc6XHJcblx0XHRcdFx0Y29uc3QgJGJ1bGtBY3Rpb24gPSAkKG9wdGlvbnMuYnVsa0FjdGlvblNlbGVjdG9yKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRpZiAoJGJ1bGtBY3Rpb24uZmluZCgnYnV0dG9uOmZpcnN0JykudGV4dCgpID09PSAnJykge1xyXG5cdFx0XHRcdFx0Y29uc3QgJGFjdGlvbkxpbmsgPSAkYnVsa0FjdGlvbi5maW5kKCd1bCBsaTpmaXJzdCBhJyk7XHJcblx0XHRcdFx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24uc2V0RGVmYXVsdEFjdGlvbigkYnVsa0FjdGlvbiwgJGFjdGlvbkxpbmspO1xyXG5cdFx0XHRcdH1cclxuXHRcdFx0XHRcclxuXHRcdFx0XHRicmVhaztcclxuXHRcdFx0XHJcblx0XHRcdGRlZmF1bHQ6XHJcblx0XHRcdFx0dGhyb3cgbmV3IEVycm9yKGBJbnZhbGlkIFwiZW5zdXJlXCIgdHlwZSBnaXZlbiAoZXhwZWN0ZWQgXCJyb3dcIiBvciBcImJ1bGtcIiBnb3QgOiBcIiR7dHlwZX1cIikuYCk7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIEJ1dHRvbiBEcm9kcG93biBBY3Rpb24gQ2xpY2tcclxuXHQgKlxyXG5cdCAqIFVwZGF0ZSB0aGUgZGVmYXVsdEJ1bGtBY3Rpb24gYW5kIGRlZmF1bHRSb3dBY3Rpb24gZGF0YSBhdHRyaWJ1dGVzLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkJ1dHRvbkRyb3Bkb3duQWN0aW9uQ2xpY2soKSB7XHJcblx0XHRjb25zdCBwcm9wZXJ0eSA9ICQodGhpcykucGFyZW50cygnLmJ0bi1ncm91cCcpWzBdID09PSAkKG9wdGlvbnMuYnVsa0FjdGlvblNlbGVjdG9yKVswXVxyXG5cdFx0XHQ/ICdkZWZhdWx0QnVsa0FjdGlvbicgOiAnZGVmYXVsdFJvd0FjdGlvbic7XHJcblx0XHRcclxuXHRcdCR0aGlzLmRhdGEocHJvcGVydHksICQodGhpcykuZGF0YSgnY29uZmlndXJhdGlvblZhbHVlJykpO1xyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXMuZGF0YSh7XHJcblx0XHRcdGRlZmF1bHRSb3dBY3Rpb246IG9wdGlvbnMucm93LFxyXG5cdFx0XHRkZWZhdWx0QnVsa0FjdGlvbjogb3B0aW9ucy5idWxrXHJcblx0XHR9KTtcclxuXHRcdFxyXG5cdFx0JHRoaXMub24oJ2NsaWNrJywgJy5idG4tZ3JvdXAuZHJvcGRvd24gYScsIF9vbkJ1dHRvbkRyb3Bkb3duQWN0aW9uQ2xpY2spO1xyXG5cdFx0JCgnYm9keScpLm9uKCdjbGljaycsIG9wdGlvbnMuYnVsa0FjdGlvblNlbGVjdG9yLCBfb25CdXR0b25Ecm9wZG93bkFjdGlvbkNsaWNrKTtcclxuXHRcdFxyXG5cdFx0Ly8gQmluZCBtb2R1bGUgYXBpIHRvIGpRdWVyeSBvYmplY3QuIFxyXG5cdFx0JC5mbi5leHRlbmQoe1xyXG5cdFx0XHRkYXRhdGFibGVfZGVmYXVsdF9hY3Rpb25zOiBmdW5jdGlvbihhY3Rpb24sIC4uLmFyZ3MpIHtcclxuXHRcdFx0XHRzd2l0Y2ggKGFjdGlvbikge1xyXG5cdFx0XHRcdFx0Y2FzZSAnZW5zdXJlJzpcclxuXHRcdFx0XHRcdFx0cmV0dXJuIF9lbnN1cmUuYXBwbHkodGhpcywgYXJncyk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHRcdFxyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxuXHRcclxufSk7Il19
