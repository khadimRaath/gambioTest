'use strict';

/* --------------------------------------------------------------
 datatable.js 2016-02-23
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## DataTable Widget
 *
 * Wrapper widget for the jquery datatables plugin. You can create a whole
 * data table with sort, search, pagination and other useful utilities.
 *
 * Official DataTables Website: {@link http://www.datatables.net}
 * 
 * ### Options
 *
 * **Language | `data-datatable-language` | Object | Optional**
 *
 * Provide the default language for the data table. If no language is provided, the language
 * defaults to german. [Click here](https://datatables.net/reference/option/language) to see
 * how the language object should look like.
 *
 * ### Example
 *
 * ```html
 * <table data-gx-widget="datatable">
 *   <thead>
 *     <tr>
 *       <th>Column 1</th>
 *       <th>Column 2</th>
 *     </tr>
 *   </thead>
 *   <tbody>
 *     <tr>
 *       <td>Cell 1</td>
 *       <td>Cell 2</td>
 *     </tr>
 *   </tbody>
 * </table>
 * ```
 *
 * *Place the ".disable-sort" class to <th> elements that shouldn't be sorted.*
 *
 * @module Admin/Widgets/datatable
 * @requires jQuery-DataTables-Plugin
 */
gx.widgets.module('datatable', ['datatable'],

/** @lends module:Widgets/datatable */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	var
	/**
  * Widget Reference Selector
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * DataTable plugin handler used for triggering API operations.
  *
  * @type {object}
  */
	$table = {},


	/**
  * Default options of Widget
  *
  * @type {object}
  */
	defaults = {
		language: jse.libs.datatable.getGermanTranslation()
	},


	/**
  * Final Widget Options
  *
  * @type {object}
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

	/**  Define Views Data */
	module.view = {};

	/** Define Models Data */
	module.model = {};

	/** Define Dependencies */
	module.dependencies = {};

	/**
  * Initialize method of the widget, called by the engine.
  */
	module.init = function (done) {
		$table = $this.DataTable(options);
		done();
	};

	// Return data to module engine.
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZS5qcyJdLCJuYW1lcyI6WyJneCIsIndpZGdldHMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiJHRhYmxlIiwiZGVmYXVsdHMiLCJsYW5ndWFnZSIsImpzZSIsImxpYnMiLCJkYXRhdGFibGUiLCJnZXRHZXJtYW5UcmFuc2xhdGlvbiIsIm9wdGlvbnMiLCJleHRlbmQiLCJ2aWV3IiwibW9kZWwiLCJkZXBlbmRlbmNpZXMiLCJpbml0IiwiZG9uZSIsIkRhdGFUYWJsZSJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBd0NBQSxHQUFHQyxPQUFILENBQVdDLE1BQVgsQ0FDQyxXQURELEVBR0MsQ0FBQyxXQUFELENBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFVBQVMsRUFiVjs7O0FBZUM7Ozs7O0FBS0FDLFlBQVc7QUFDVkMsWUFBVUMsSUFBSUMsSUFBSixDQUFTQyxTQUFULENBQW1CQyxvQkFBbkI7QUFEQSxFQXBCWjs7O0FBd0JDOzs7OztBQUtBQyxXQUFVUixFQUFFUyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJQLFFBQW5CLEVBQTZCSixJQUE3QixDQTdCWDs7O0FBK0JDOzs7OztBQUtBRCxVQUFTLEVBcENWOztBQXNDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQUEsUUFBT2EsSUFBUCxHQUFjLEVBQWQ7O0FBRUE7QUFDQWIsUUFBT2MsS0FBUCxHQUFlLEVBQWY7O0FBRUE7QUFDQWQsUUFBT2UsWUFBUCxHQUFzQixFQUF0Qjs7QUFFQTs7O0FBR0FmLFFBQU9nQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCYixXQUFTRixNQUFNZ0IsU0FBTixDQUFnQlAsT0FBaEIsQ0FBVDtBQUNBTTtBQUNBLEVBSEQ7O0FBS0E7QUFDQSxRQUFPakIsTUFBUDtBQUNBLENBNUVGIiwiZmlsZSI6ImRhdGF0YWJsZS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gZGF0YXRhYmxlLmpzIDIwMTYtMDItMjNcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIERhdGFUYWJsZSBXaWRnZXRcbiAqXG4gKiBXcmFwcGVyIHdpZGdldCBmb3IgdGhlIGpxdWVyeSBkYXRhdGFibGVzIHBsdWdpbi4gWW91IGNhbiBjcmVhdGUgYSB3aG9sZVxuICogZGF0YSB0YWJsZSB3aXRoIHNvcnQsIHNlYXJjaCwgcGFnaW5hdGlvbiBhbmQgb3RoZXIgdXNlZnVsIHV0aWxpdGllcy5cbiAqXG4gKiBPZmZpY2lhbCBEYXRhVGFibGVzIFdlYnNpdGU6IHtAbGluayBodHRwOi8vd3d3LmRhdGF0YWJsZXMubmV0fVxuICogXG4gKiAjIyMgT3B0aW9uc1xuICpcbiAqICoqTGFuZ3VhZ2UgfCBgZGF0YS1kYXRhdGFibGUtbGFuZ3VhZ2VgIHwgT2JqZWN0IHwgT3B0aW9uYWwqKlxuICpcbiAqIFByb3ZpZGUgdGhlIGRlZmF1bHQgbGFuZ3VhZ2UgZm9yIHRoZSBkYXRhIHRhYmxlLiBJZiBubyBsYW5ndWFnZSBpcyBwcm92aWRlZCwgdGhlIGxhbmd1YWdlXG4gKiBkZWZhdWx0cyB0byBnZXJtYW4uIFtDbGljayBoZXJlXShodHRwczovL2RhdGF0YWJsZXMubmV0L3JlZmVyZW5jZS9vcHRpb24vbGFuZ3VhZ2UpIHRvIHNlZVxuICogaG93IHRoZSBsYW5ndWFnZSBvYmplY3Qgc2hvdWxkIGxvb2sgbGlrZS5cbiAqXG4gKiAjIyMgRXhhbXBsZVxuICpcbiAqIGBgYGh0bWxcbiAqIDx0YWJsZSBkYXRhLWd4LXdpZGdldD1cImRhdGF0YWJsZVwiPlxuICogICA8dGhlYWQ+XG4gKiAgICAgPHRyPlxuICogICAgICAgPHRoPkNvbHVtbiAxPC90aD5cbiAqICAgICAgIDx0aD5Db2x1bW4gMjwvdGg+XG4gKiAgICAgPC90cj5cbiAqICAgPC90aGVhZD5cbiAqICAgPHRib2R5PlxuICogICAgIDx0cj5cbiAqICAgICAgIDx0ZD5DZWxsIDE8L3RkPlxuICogICAgICAgPHRkPkNlbGwgMjwvdGQ+XG4gKiAgICAgPC90cj5cbiAqICAgPC90Ym9keT5cbiAqIDwvdGFibGU+XG4gKiBgYGBcbiAqXG4gKiAqUGxhY2UgdGhlIFwiLmRpc2FibGUtc29ydFwiIGNsYXNzIHRvIDx0aD4gZWxlbWVudHMgdGhhdCBzaG91bGRuJ3QgYmUgc29ydGVkLipcbiAqXG4gKiBAbW9kdWxlIEFkbWluL1dpZGdldHMvZGF0YXRhYmxlXG4gKiBAcmVxdWlyZXMgalF1ZXJ5LURhdGFUYWJsZXMtUGx1Z2luXG4gKi9cbmd4LndpZGdldHMubW9kdWxlKFxuXHQnZGF0YXRhYmxlJyxcblx0XG5cdFsnZGF0YXRhYmxlJ10sXG5cdFxuXHQvKiogQGxlbmRzIG1vZHVsZTpXaWRnZXRzL2RhdGF0YWJsZSAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogV2lkZ2V0IFJlZmVyZW5jZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEYXRhVGFibGUgcGx1Z2luIGhhbmRsZXIgdXNlZCBmb3IgdHJpZ2dlcmluZyBBUEkgb3BlcmF0aW9ucy5cblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGFibGUgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IG9wdGlvbnMgb2YgV2lkZ2V0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7XG5cdFx0XHRcdGxhbmd1YWdlOiBqc2UubGlicy5kYXRhdGFibGUuZ2V0R2VybWFuVHJhbnNsYXRpb24oKVxuXHRcdFx0fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBXaWRnZXQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8qKiAgRGVmaW5lIFZpZXdzIERhdGEgKi9cblx0XHRtb2R1bGUudmlldyA9IHt9O1xuXHRcdFxuXHRcdC8qKiBEZWZpbmUgTW9kZWxzIERhdGEgKi9cblx0XHRtb2R1bGUubW9kZWwgPSB7fTtcblx0XHRcblx0XHQvKiogRGVmaW5lIERlcGVuZGVuY2llcyAqL1xuXHRcdG1vZHVsZS5kZXBlbmRlbmNpZXMgPSB7fTtcblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIG1ldGhvZCBvZiB0aGUgd2lkZ2V0LCBjYWxsZWQgYnkgdGhlIGVuZ2luZS5cblx0XHQgKi9cblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0YWJsZSA9ICR0aGlzLkRhdGFUYWJsZShvcHRpb25zKTtcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmUuXG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
