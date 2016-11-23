'use strict';

/* --------------------------------------------------------------
 datatable.js 2016-07-11
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

jse.libs.datatable = jse.libs.datatable || {};

/**
 * ## DataTable Library
 *
 * This is a wrapper library for the manipulation of jQuery DataTables. Use the "create" method with DataTable 
 * configuration to initialize a table on your page. All you need when using this library is an empty `<table>` 
 * element. Visit the official website of DataTables to check examples and other information about the plugin.
 *
 * {@link http://www.datatables.net Official DataTables Website}
 *
 * ### Examples
 * 
 * **Example - Create A New Instance**
 * ```javascript
 * var tableApi = jse.libs.datatable.create($('#my-table'), {
 *      ajax: 'http://shop.de/table-data.php',
 *      columns: [
 *          { title: 'Name', data: 'name' defaultContent: '...' },
 *          { title: 'Email', data: 'email' },
 *          { title: 'Actions', data: null, orderable: false, defaultContent: 'Add | Edit | Delete' },
 *      ]
 * });
 * ```
 *
 * **Example - Add Error Handler**
 * ```javascript
 * jse.libs.datatable.error($('#my-table'), function(event, settings, techNote, message) {
 *      // Log error in the JavaScript console.
 *      console.log('DataTable Error:', message);
 * });
 * ```
 *
 * @module JSE/Libs/datatable
 * @exports jse.libs.datatable
 */
(function (exports) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	var languages = {
		de: {
			'sEmptyTable': 'Keine Daten in der Tabelle vorhanden',
			'sInfo': '_START_ bis _END_ (von _TOTAL_)',
			'sInfoEmpty': '0 bis 0 von 0 Einträgen',
			'sInfoFiltered': '(gefiltert von _MAX_ Einträgen)',
			'sInfoPostFix': '',
			'sInfoThousands': '.',
			'sLengthMenu': '_MENU_ Einträge anzeigen',
			'sLoadingRecords': 'Wird geladen...',
			'sProcessing': 'Bitte warten...',
			'sSearch': 'Suchen',
			'sZeroRecords': 'Keine Einträge vorhanden.',
			'oPaginate': {
				'sFirst': 'Erste',
				'sPrevious': 'Zurück',
				'sNext': 'Nächste',
				'sLast': 'Letzte'
			},
			'oAria': {
				'sSortAscending': ': aktivieren, um Spalte aufsteigend zu sortieren',
				'sSortDescending': ': aktivieren, um Spalte absteigend zu sortieren'
			}
		},
		en: {
			'sEmptyTable': 'No data available in table',
			'sInfo': '_START_ to _END_ (of _TOTAL_)',
			'sInfoEmpty': 'Showing 0 to 0 of 0 entries',
			'sInfoFiltered': '(filtered from _MAX_ total entries)',
			'sInfoPostFix': '',
			'sInfoThousands': ',',
			'sLengthMenu': 'Show _MENU_ entries',
			'sLoadingRecords': 'Loading...',
			'sProcessing': 'Processing...',
			'sSearch': 'Search:',
			'sZeroRecords': 'No matching records found',
			'oPaginate': {
				'sFirst': 'First',
				'sLast': 'Last',
				'sNext': 'Next',
				'sPrevious': 'Previous'
			},
			'oAria': {
				'sSortAscending': ': activate to sort column ascending',
				'sSortDescending': ': activate to sort column descending'
			}
		}
	};

	// ------------------------------------------------------------------------
	// FUNCTIONALITY
	// ------------------------------------------------------------------------

	/**
  * Reorder the table columns as defined in the active columns array.
  *
  * @param {jQuery} $target Table jQuery selector object.
  * @param {Object} columnDefinitions Array containing the DataTable column definitions.
  * @param {Array} activeColumnNames Array containing the slug-names of the active columns.
  *
  * @return {Array} Returns array with the active column definitions ready to use in DataTable.columns option.
  *
  * @private
  */
	function _reorderColumns($target, columnDefinitions, activeColumnNames) {
		activeColumnNames.unshift('checkbox');
		activeColumnNames.push('actions');

		// Hide the table header cells that are not active.
		$.each(columnDefinitions, function (index, columnDefinition) {
			$target.find('thead tr').each(function () {
				var $headerCell = $(this).find('[data-column-name="' + columnDefinition.name + '"]');

				if (columnDefinition.data !== null && activeColumnNames.indexOf(columnDefinition.name) === -1) {
					$headerCell.hide();
				}
			});
		});

		// Prepare the active column definitions.
		var finalColumnDefinitions = [],
		    columnIndexes = [];

		$.each(activeColumnNames, function (index, name) {
			$.each(columnDefinitions, function (index, columnDefinition) {
				if (columnDefinition.name === name) {
					// Add the active column definition in the "finalColumnDefinitions" array.
					finalColumnDefinitions.push(columnDefinition);
					var headerCellIndex = $target.find('thead:first tr:first [data-column-name="' + columnDefinition.name + '"]').index();
					columnIndexes.push(headerCellIndex);
					return true; // continue
				}
			});
		});

		finalColumnDefinitions.sort(function (a, b) {
			var aIndex = activeColumnNames.indexOf(a.name);
			var bIndex = activeColumnNames.indexOf(b.name);

			if (aIndex < bIndex) {
				return -1;
			} else if (aIndex > bIndex) {
				return 1;
			} else {
				return 0;
			}
		});

		// Reorder the table header elements depending the activeColumnNames order.
		$target.find('thead tr').each(function () {
			var _this = this;

			var activeColumnSelections = [$(this).find('th:first')];

			// Sort the columns in the correct order.
			columnIndexes.forEach(function (index) {
				var $headerCell = $(_this).find('th').eq(index);
				activeColumnSelections.push($headerCell);
			});

			// Move the columns to their final position.
			activeColumnSelections.forEach(function ($headerCell, index) {
				if (index === 0) {
					return true;
				}

				$headerCell.insertAfter(activeColumnSelections[index - 1]);
			});
		});

		return finalColumnDefinitions;
	}

	/**
  * Creates a DataTable Instance
  *
  * This method will create a new instance of datatable into a `<table>` element. It enables
  * developers to easily pass the configuration needed for different and more special situations.
  *
  * @param {jQuery} $target jQuery object for the target table.
  * @param {Object} configuration DataTables configuration applied on the new instance.
  *
  * @return {DataTable} Returns the DataTable API instance (different from the jQuery object).
  */
	exports.create = function ($target, configuration) {
		return $target.DataTable(configuration);
	};

	/**
  * Sets the error handler for specific DataTable.
  *
  * DataTables provide a useful mechanism that enables developers to control errors during data parsing.
  * If there is an error in the AJAX response or some data are invalid in the JavaScript code you can use 
  * this method to control the behavior of the app and show or log the error messages.
  *
  * {@link http://datatables.net/reference/event/error}
  *
  * @param {jQuery} $target jQuery object for the target table.
  * @param {Object} callback Provide a callback method called with the "event", "settings", "techNote", 
  * "message" arguments (see provided link).
  */
	exports.error = function ($target, callback) {
		$.fn.dataTable.ext.errMode = 'none';
		$target.on('error.dt', callback).on('xhr.dt', function (event, settings, json, xhr) {
			if (json.exception === true) {
				callback(event, settings, null, json.message);
			}
		});
	};

	/**
  * Sets the callback method when ajax load of data is complete.
  *
  * This method is useful for checking PHP errors or modifying the data before
  * they are displayed to the server.
  *
  * {@link http://datatables.net/reference/event/xhr}
  *
  * @param {jQuery} $target jQuery object for the target table.
  * @param {Function} callback Provide a callback method called with the "event", "settings", "techNote", 
  * "message" arguments (see provided link).
  */
	exports.ajaxComplete = function ($target, callback) {
		$target.on('xhr.dt', callback);
	};

	/**
  * Sets the table column to be displayed as an index.
  *
  * This method will easily enable you to set a column as an index column, used
  * for numbering the table rows regardless of the search, sorting and row count.
  *
  * {@link http://www.datatables.net/examples/api/counter_columns.html}
  *
  * @param {jQuery} $target jQuery object for the target table.
  * @param {Number} columnIndex Zero based index of the column to be indexed.
  */
	exports.indexColumn = function ($target, columnIndex) {
		$target.on('order.dt search.dt', function () {
			$target.DataTable().column(columnIndex, {
				search: 'applied',
				order: 'applied'
			}).nodes().each(function (cell, index) {
				cell.innerHTML = index + 1;
			});
		});
	};

	/**
  * Returns the german translation of the DataTables
  *
  * This method provides a quick way to get the language JSON without having to perform
  * and AJAX request to the server. If you setup your DataTable manually you can set the
  * "language" attribute with this method.
  *
  * @deprecated Since v1.4, use the "getTranslations" method instead.
  *
  * @return {Object} Returns the german translation, must be the same as the "german.lang.json" file.
  */
	exports.getGermanTranslation = function () {
		jse.core.debug.warn('The getGermanTranslation method is deprecated and will be removed ' + 'in JSE v1.5, please use the "getTranslations" method instead.');
		return languages.de;
	};

	/**
  * Get the DataTables translation depending the language code parameter.
  *
  * @param {String} languageCode Provide 'de' or 'en' (you can also use the jse.core.config.get('languageCode') to
  * get the current language code).
  *
  * @return {Object} Returns the translation strings in an object literal as described by the official DataTables
  * documentation.
  *
  * {@link https://www.datatables.net/plug-ins/i18n}
  */
	exports.getTranslations = function (languageCode) {
		if (languages[languageCode] === undefined) {
			jse.core.debug.warn('The requested DataTables translation was not found:', languageCode);
			languageCode = 'en';
		}

		return languages[languageCode];
	};

	/**
  * Prepare table columns.
  *
  * This method will convert the column definitions to a DataTable compatible format and also reorder
  * the table header cells of the "thead" element.
  *
  * @param {jQuery} $target Table jQuery selector object.
  * @param {Object} columnDefinitions Array containing the DataTable column definitions.
  * @param {Array} activeColumnNames Array containing the slug-names of the active columns.
  *
  * @return {Array} Returns array with the active column definitions ready to use in DataTable.columns option.
  */
	exports.prepareColumns = function ($target, columnDefinitions, activeColumnNames) {
		var convertedColumnDefinitions = [];

		for (var columnName in columnDefinitions) {
			var columnDefinition = columnDefinitions[columnName];
			columnDefinition.name = columnName;
			convertedColumnDefinitions.push(columnDefinition);
		}

		return _reorderColumns($target, convertedColumnDefinitions, activeColumnNames);
	};
})(jse.libs.datatable);
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZS5qcyJdLCJuYW1lcyI6WyJqc2UiLCJsaWJzIiwiZGF0YXRhYmxlIiwiZXhwb3J0cyIsImxhbmd1YWdlcyIsImRlIiwiZW4iLCJfcmVvcmRlckNvbHVtbnMiLCIkdGFyZ2V0IiwiY29sdW1uRGVmaW5pdGlvbnMiLCJhY3RpdmVDb2x1bW5OYW1lcyIsInVuc2hpZnQiLCJwdXNoIiwiJCIsImVhY2giLCJpbmRleCIsImNvbHVtbkRlZmluaXRpb24iLCJmaW5kIiwiJGhlYWRlckNlbGwiLCJuYW1lIiwiZGF0YSIsImluZGV4T2YiLCJoaWRlIiwiZmluYWxDb2x1bW5EZWZpbml0aW9ucyIsImNvbHVtbkluZGV4ZXMiLCJoZWFkZXJDZWxsSW5kZXgiLCJzb3J0IiwiYSIsImIiLCJhSW5kZXgiLCJiSW5kZXgiLCJhY3RpdmVDb2x1bW5TZWxlY3Rpb25zIiwiZm9yRWFjaCIsImVxIiwiaW5zZXJ0QWZ0ZXIiLCJjcmVhdGUiLCJjb25maWd1cmF0aW9uIiwiRGF0YVRhYmxlIiwiZXJyb3IiLCJjYWxsYmFjayIsImZuIiwiZGF0YVRhYmxlIiwiZXh0IiwiZXJyTW9kZSIsIm9uIiwiZXZlbnQiLCJzZXR0aW5ncyIsImpzb24iLCJ4aHIiLCJleGNlcHRpb24iLCJtZXNzYWdlIiwiYWpheENvbXBsZXRlIiwiaW5kZXhDb2x1bW4iLCJjb2x1bW5JbmRleCIsImNvbHVtbiIsInNlYXJjaCIsIm9yZGVyIiwibm9kZXMiLCJjZWxsIiwiaW5uZXJIVE1MIiwiZ2V0R2VybWFuVHJhbnNsYXRpb24iLCJjb3JlIiwiZGVidWciLCJ3YXJuIiwiZ2V0VHJhbnNsYXRpb25zIiwibGFuZ3VhZ2VDb2RlIiwidW5kZWZpbmVkIiwicHJlcGFyZUNvbHVtbnMiLCJjb252ZXJ0ZWRDb2x1bW5EZWZpbml0aW9ucyIsImNvbHVtbk5hbWUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQUEsSUFBSUMsSUFBSixDQUFTQyxTQUFULEdBQXFCRixJQUFJQyxJQUFKLENBQVND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QyxJQUFmLENBQW9CLHVFQUNqQiwrREFESDtBQUVBLFNBQU8zRCxVQUFVQyxFQUFqQjtBQUNBLEVBSkQ7O0FBTUE7Ozs7Ozs7Ozs7O0FBV0FGLFNBQVE2RCxlQUFSLEdBQTBCLFVBQVNDLFlBQVQsRUFBdUI7QUFDaEQsTUFBSTdELFVBQVU2RCxZQUFWLE1BQTRCQyxTQUFoQyxFQUEyQztBQUMxQ2xFLE9BQUk2RCxJQUFKLENBQVNDLEtBQVQsQ0FBZUMsSUFBZixDQUFvQixxREFBcEIsRUFBMkVFLFlBQTNFO0FBQ0FBLGtCQUFlLElBQWY7QUFDQTs7QUFFRCxTQUFPN0QsVUFBVTZELFlBQVYsQ0FBUDtBQUNBLEVBUEQ7O0FBU0E7Ozs7Ozs7Ozs7OztBQVlBOUQsU0FBUWdFLGNBQVIsR0FBeUIsVUFBUzNELE9BQVQsRUFBa0JDLGlCQUFsQixFQUFxQ0MsaUJBQXJDLEVBQXdEO0FBQ2hGLE1BQUkwRCw2QkFBNkIsRUFBakM7O0FBRUEsT0FBSyxJQUFJQyxVQUFULElBQXVCNUQsaUJBQXZCLEVBQTBDO0FBQ3pDLE9BQUlPLG1CQUFtQlAsa0JBQWtCNEQsVUFBbEIsQ0FBdkI7QUFDQXJELG9CQUFpQkcsSUFBakIsR0FBd0JrRCxVQUF4QjtBQUNBRCw4QkFBMkJ4RCxJQUEzQixDQUFnQ0ksZ0JBQWhDO0FBQ0E7O0FBRUQsU0FBT1QsZ0JBQWdCQyxPQUFoQixFQUF5QjRELDBCQUF6QixFQUFxRDFELGlCQUFyRCxDQUFQO0FBQ0EsRUFWRDtBQVlBLENBdlJBLEVBdVJDVixJQUFJQyxJQUFKLENBQVNDLFNBdlJWLENBQUQiLCJmaWxlIjoiZGF0YXRhYmxlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBkYXRhdGFibGUuanMgMjAxNi0wNy0xMVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbmpzZS5saWJzLmRhdGF0YWJsZSA9IGpzZS5saWJzLmRhdGF0YWJsZSB8fCB7fTtcblxuLyoqXG4gKiAjIyBEYXRhVGFibGUgTGlicmFyeVxuICpcbiAqIFRoaXMgaXMgYSB3cmFwcGVyIGxpYnJhcnkgZm9yIHRoZSBtYW5pcHVsYXRpb24gb2YgalF1ZXJ5IERhdGFUYWJsZXMuIFVzZSB0aGUgXCJjcmVhdGVcIiBtZXRob2Qgd2l0aCBEYXRhVGFibGUgXG4gKiBjb25maWd1cmF0aW9uIHRvIGluaXRpYWxpemUgYSB0YWJsZSBvbiB5b3VyIHBhZ2UuIEFsbCB5b3UgbmVlZCB3aGVuIHVzaW5nIHRoaXMgbGlicmFyeSBpcyBhbiBlbXB0eSBgPHRhYmxlPmAgXG4gKiBlbGVtZW50LiBWaXNpdCB0aGUgb2ZmaWNpYWwgd2Vic2l0ZSBvZiBEYXRhVGFibGVzIHRvIGNoZWNrIGV4YW1wbGVzIGFuZCBvdGhlciBpbmZvcm1hdGlvbiBhYm91dCB0aGUgcGx1Z2luLlxuICpcbiAqIHtAbGluayBodHRwOi8vd3d3LmRhdGF0YWJsZXMubmV0IE9mZmljaWFsIERhdGFUYWJsZXMgV2Vic2l0ZX1cbiAqXG4gKiAjIyMgRXhhbXBsZXNcbiAqIFxuICogKipFeGFtcGxlIC0gQ3JlYXRlIEEgTmV3IEluc3RhbmNlKipcbiAqIGBgYGphdmFzY3JpcHRcbiAqIHZhciB0YWJsZUFwaSA9IGpzZS5saWJzLmRhdGF0YWJsZS5jcmVhdGUoJCgnI215LXRhYmxlJyksIHtcbiAqICAgICAgYWpheDogJ2h0dHA6Ly9zaG9wLmRlL3RhYmxlLWRhdGEucGhwJyxcbiAqICAgICAgY29sdW1uczogW1xuICogICAgICAgICAgeyB0aXRsZTogJ05hbWUnLCBkYXRhOiAnbmFtZScgZGVmYXVsdENvbnRlbnQ6ICcuLi4nIH0sXG4gKiAgICAgICAgICB7IHRpdGxlOiAnRW1haWwnLCBkYXRhOiAnZW1haWwnIH0sXG4gKiAgICAgICAgICB7IHRpdGxlOiAnQWN0aW9ucycsIGRhdGE6IG51bGwsIG9yZGVyYWJsZTogZmFsc2UsIGRlZmF1bHRDb250ZW50OiAnQWRkIHwgRWRpdCB8IERlbGV0ZScgfSxcbiAqICAgICAgXVxuICogfSk7XG4gKiBgYGBcbiAqXG4gKiAqKkV4YW1wbGUgLSBBZGQgRXJyb3IgSGFuZGxlcioqXG4gKiBgYGBqYXZhc2NyaXB0XG4gKiBqc2UubGlicy5kYXRhdGFibGUuZXJyb3IoJCgnI215LXRhYmxlJyksIGZ1bmN0aW9uKGV2ZW50LCBzZXR0aW5ncywgdGVjaE5vdGUsIG1lc3NhZ2UpIHtcbiAqICAgICAgLy8gTG9nIGVycm9yIGluIHRoZSBKYXZhU2NyaXB0IGNvbnNvbGUuXG4gKiAgICAgIGNvbnNvbGUubG9nKCdEYXRhVGFibGUgRXJyb3I6JywgbWVzc2FnZSk7XG4gKiB9KTtcbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgSlNFL0xpYnMvZGF0YXRhYmxlXG4gKiBAZXhwb3J0cyBqc2UubGlicy5kYXRhdGFibGVcbiAqL1xuKGZ1bmN0aW9uKGV4cG9ydHMpIHtcblx0XG5cdCd1c2Ugc3RyaWN0Jztcblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBWQVJJQUJMRVNcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFxuXHRsZXQgbGFuZ3VhZ2VzID0ge1xuXHRcdGRlOiB7XG5cdFx0XHQnc0VtcHR5VGFibGUnOiAnS2VpbmUgRGF0ZW4gaW4gZGVyIFRhYmVsbGUgdm9yaGFuZGVuJyxcblx0XHRcdCdzSW5mbyc6ICdfU1RBUlRfIGJpcyBfRU5EXyAodm9uIF9UT1RBTF8pJyxcblx0XHRcdCdzSW5mb0VtcHR5JzogJzAgYmlzIDAgdm9uIDAgRWludHLDpGdlbicsXG5cdFx0XHQnc0luZm9GaWx0ZXJlZCc6ICcoZ2VmaWx0ZXJ0IHZvbiBfTUFYXyBFaW50csOkZ2VuKScsXG5cdFx0XHQnc0luZm9Qb3N0Rml4JzogJycsXG5cdFx0XHQnc0luZm9UaG91c2FuZHMnOiAnLicsXG5cdFx0XHQnc0xlbmd0aE1lbnUnOiAnX01FTlVfIEVpbnRyw6RnZSBhbnplaWdlbicsXG5cdFx0XHQnc0xvYWRpbmdSZWNvcmRzJzogJ1dpcmQgZ2VsYWRlbi4uLicsXG5cdFx0XHQnc1Byb2Nlc3NpbmcnOiAnQml0dGUgd2FydGVuLi4uJyxcblx0XHRcdCdzU2VhcmNoJzogJ1N1Y2hlbicsXG5cdFx0XHQnc1plcm9SZWNvcmRzJzogJ0tlaW5lIEVpbnRyw6RnZSB2b3JoYW5kZW4uJyxcblx0XHRcdCdvUGFnaW5hdGUnOiB7XG5cdFx0XHRcdCdzRmlyc3QnOiAnRXJzdGUnLFxuXHRcdFx0XHQnc1ByZXZpb3VzJzogJ1p1csO8Y2snLFxuXHRcdFx0XHQnc05leHQnOiAnTsOkY2hzdGUnLFxuXHRcdFx0XHQnc0xhc3QnOiAnTGV0enRlJ1xuXHRcdFx0fSxcblx0XHRcdCdvQXJpYSc6IHtcblx0XHRcdFx0J3NTb3J0QXNjZW5kaW5nJzogJzogYWt0aXZpZXJlbiwgdW0gU3BhbHRlIGF1ZnN0ZWlnZW5kIHp1IHNvcnRpZXJlbicsXG5cdFx0XHRcdCdzU29ydERlc2NlbmRpbmcnOiAnOiBha3RpdmllcmVuLCB1bSBTcGFsdGUgYWJzdGVpZ2VuZCB6dSBzb3J0aWVyZW4nXG5cdFx0XHR9XG5cdFx0fSxcblx0XHRlbjoge1xuXHRcdFx0J3NFbXB0eVRhYmxlJzogJ05vIGRhdGEgYXZhaWxhYmxlIGluIHRhYmxlJyxcblx0XHRcdCdzSW5mbyc6ICdfU1RBUlRfIHRvIF9FTkRfIChvZiBfVE9UQUxfKScsXG5cdFx0XHQnc0luZm9FbXB0eSc6ICdTaG93aW5nIDAgdG8gMCBvZiAwIGVudHJpZXMnLFxuXHRcdFx0J3NJbmZvRmlsdGVyZWQnOiAnKGZpbHRlcmVkIGZyb20gX01BWF8gdG90YWwgZW50cmllcyknLFxuXHRcdFx0J3NJbmZvUG9zdEZpeCc6ICcnLFxuXHRcdFx0J3NJbmZvVGhvdXNhbmRzJzogJywnLFxuXHRcdFx0J3NMZW5ndGhNZW51JzogJ1Nob3cgX01FTlVfIGVudHJpZXMnLFxuXHRcdFx0J3NMb2FkaW5nUmVjb3Jkcyc6ICdMb2FkaW5nLi4uJyxcblx0XHRcdCdzUHJvY2Vzc2luZyc6ICdQcm9jZXNzaW5nLi4uJyxcblx0XHRcdCdzU2VhcmNoJzogJ1NlYXJjaDonLFxuXHRcdFx0J3NaZXJvUmVjb3Jkcyc6ICdObyBtYXRjaGluZyByZWNvcmRzIGZvdW5kJyxcblx0XHRcdCdvUGFnaW5hdGUnOiB7XG5cdFx0XHRcdCdzRmlyc3QnOiAnRmlyc3QnLFxuXHRcdFx0XHQnc0xhc3QnOiAnTGFzdCcsXG5cdFx0XHRcdCdzTmV4dCc6ICdOZXh0Jyxcblx0XHRcdFx0J3NQcmV2aW91cyc6ICdQcmV2aW91cydcblx0XHRcdH0sXG5cdFx0XHQnb0FyaWEnOiB7XG5cdFx0XHRcdCdzU29ydEFzY2VuZGluZyc6ICc6IGFjdGl2YXRlIHRvIHNvcnQgY29sdW1uIGFzY2VuZGluZycsXG5cdFx0XHRcdCdzU29ydERlc2NlbmRpbmcnOiAnOiBhY3RpdmF0ZSB0byBzb3J0IGNvbHVtbiBkZXNjZW5kaW5nJ1xuXHRcdFx0fVxuXHRcdH1cblx0fTtcblx0XG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHQvLyBGVU5DVElPTkFMSVRZXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcblx0LyoqXG5cdCAqIFJlb3JkZXIgdGhlIHRhYmxlIGNvbHVtbnMgYXMgZGVmaW5lZCBpbiB0aGUgYWN0aXZlIGNvbHVtbnMgYXJyYXkuXG5cdCAqXG5cdCAqIEBwYXJhbSB7alF1ZXJ5fSAkdGFyZ2V0IFRhYmxlIGpRdWVyeSBzZWxlY3RvciBvYmplY3QuXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBjb2x1bW5EZWZpbml0aW9ucyBBcnJheSBjb250YWluaW5nIHRoZSBEYXRhVGFibGUgY29sdW1uIGRlZmluaXRpb25zLlxuXHQgKiBAcGFyYW0ge0FycmF5fSBhY3RpdmVDb2x1bW5OYW1lcyBBcnJheSBjb250YWluaW5nIHRoZSBzbHVnLW5hbWVzIG9mIHRoZSBhY3RpdmUgY29sdW1ucy5cblx0ICpcblx0ICogQHJldHVybiB7QXJyYXl9IFJldHVybnMgYXJyYXkgd2l0aCB0aGUgYWN0aXZlIGNvbHVtbiBkZWZpbml0aW9ucyByZWFkeSB0byB1c2UgaW4gRGF0YVRhYmxlLmNvbHVtbnMgb3B0aW9uLlxuXHQgKlxuXHQgKiBAcHJpdmF0ZVxuXHQgKi9cblx0ZnVuY3Rpb24gX3Jlb3JkZXJDb2x1bW5zKCR0YXJnZXQsIGNvbHVtbkRlZmluaXRpb25zLCBhY3RpdmVDb2x1bW5OYW1lcykge1xuXHRcdGFjdGl2ZUNvbHVtbk5hbWVzLnVuc2hpZnQoJ2NoZWNrYm94Jyk7XG5cdFx0YWN0aXZlQ29sdW1uTmFtZXMucHVzaCgnYWN0aW9ucycpO1xuXHRcdFxuXHRcdC8vIEhpZGUgdGhlIHRhYmxlIGhlYWRlciBjZWxscyB0aGF0IGFyZSBub3QgYWN0aXZlLlxuXHRcdCQuZWFjaChjb2x1bW5EZWZpbml0aW9ucywgKGluZGV4LCBjb2x1bW5EZWZpbml0aW9uKSA9PiB7XG5cdFx0XHQkdGFyZ2V0LmZpbmQoJ3RoZWFkIHRyJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0bGV0ICRoZWFkZXJDZWxsID0gJCh0aGlzKS5maW5kKGBbZGF0YS1jb2x1bW4tbmFtZT1cIiR7Y29sdW1uRGVmaW5pdGlvbi5uYW1lfVwiXWApO1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKGNvbHVtbkRlZmluaXRpb24uZGF0YSAhPT0gbnVsbCAmJiBhY3RpdmVDb2x1bW5OYW1lcy5pbmRleE9mKGNvbHVtbkRlZmluaXRpb24ubmFtZSkgPT09IC0xKSB7XG5cdFx0XHRcdFx0JGhlYWRlckNlbGwuaGlkZSgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHQvLyBQcmVwYXJlIHRoZSBhY3RpdmUgY29sdW1uIGRlZmluaXRpb25zLlxuXHRcdGxldCBmaW5hbENvbHVtbkRlZmluaXRpb25zID0gW10sXG5cdFx0XHRjb2x1bW5JbmRleGVzID0gW107XG5cdFx0XG5cdFx0JC5lYWNoKGFjdGl2ZUNvbHVtbk5hbWVzLCAoaW5kZXgsIG5hbWUpID0+IHtcblx0XHRcdCQuZWFjaChjb2x1bW5EZWZpbml0aW9ucywgKGluZGV4LCBjb2x1bW5EZWZpbml0aW9uKSA9PiB7XG5cdFx0XHRcdGlmIChjb2x1bW5EZWZpbml0aW9uLm5hbWUgPT09IG5hbWUpIHtcblx0XHRcdFx0XHQvLyBBZGQgdGhlIGFjdGl2ZSBjb2x1bW4gZGVmaW5pdGlvbiBpbiB0aGUgXCJmaW5hbENvbHVtbkRlZmluaXRpb25zXCIgYXJyYXkuXG5cdFx0XHRcdFx0ZmluYWxDb2x1bW5EZWZpbml0aW9ucy5wdXNoKGNvbHVtbkRlZmluaXRpb24pO1xuXHRcdFx0XHRcdGNvbnN0IGhlYWRlckNlbGxJbmRleCA9ICR0YXJnZXRcblx0XHRcdFx0XHRcdC5maW5kKGB0aGVhZDpmaXJzdCB0cjpmaXJzdCBbZGF0YS1jb2x1bW4tbmFtZT1cIiR7Y29sdW1uRGVmaW5pdGlvbi5uYW1lfVwiXWApXG5cdFx0XHRcdFx0XHQuaW5kZXgoKTtcblx0XHRcdFx0XHRjb2x1bW5JbmRleGVzLnB1c2goaGVhZGVyQ2VsbEluZGV4KTtcblx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTsgLy8gY29udGludWVcblx0XHRcdFx0fVxuXHRcdFx0fSk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0ZmluYWxDb2x1bW5EZWZpbml0aW9ucy5zb3J0KChhLCBiKSA9PiB7XG5cdFx0XHRjb25zdCBhSW5kZXggPSBhY3RpdmVDb2x1bW5OYW1lcy5pbmRleE9mKGEubmFtZSk7XG5cdFx0XHRjb25zdCBiSW5kZXggPSBhY3RpdmVDb2x1bW5OYW1lcy5pbmRleE9mKGIubmFtZSk7XG5cdFx0XHRcblx0XHRcdGlmIChhSW5kZXggPCBiSW5kZXgpIHtcblx0XHRcdFx0cmV0dXJuIC0xO1xuXHRcdFx0fSBlbHNlIGlmIChhSW5kZXggPiBiSW5kZXgpIHtcblx0XHRcdFx0cmV0dXJuIDE7XG5cdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRyZXR1cm4gMDtcblx0XHRcdH1cblx0XHR9KTtcblx0XHRcblx0XHQvLyBSZW9yZGVyIHRoZSB0YWJsZSBoZWFkZXIgZWxlbWVudHMgZGVwZW5kaW5nIHRoZSBhY3RpdmVDb2x1bW5OYW1lcyBvcmRlci5cblx0XHQkdGFyZ2V0LmZpbmQoJ3RoZWFkIHRyJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdGxldCBhY3RpdmVDb2x1bW5TZWxlY3Rpb25zID0gWyQodGhpcykuZmluZCgndGg6Zmlyc3QnKV07XG5cdFx0XHRcblx0XHRcdC8vIFNvcnQgdGhlIGNvbHVtbnMgaW4gdGhlIGNvcnJlY3Qgb3JkZXIuXG5cdFx0XHRjb2x1bW5JbmRleGVzLmZvckVhY2goKGluZGV4KSA9PiB7XG5cdFx0XHRcdGxldCAkaGVhZGVyQ2VsbCA9ICQodGhpcykuZmluZCgndGgnKS5lcShpbmRleCk7XG5cdFx0XHRcdGFjdGl2ZUNvbHVtblNlbGVjdGlvbnMucHVzaCgkaGVhZGVyQ2VsbCk7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0Ly8gTW92ZSB0aGUgY29sdW1ucyB0byB0aGVpciBmaW5hbCBwb3NpdGlvbi5cblx0XHRcdGFjdGl2ZUNvbHVtblNlbGVjdGlvbnMuZm9yRWFjaChmdW5jdGlvbigkaGVhZGVyQ2VsbCwgaW5kZXgpIHtcblx0XHRcdFx0aWYgKGluZGV4ID09PSAwKSB7XG5cdFx0XHRcdFx0cmV0dXJuIHRydWU7XG5cdFx0XHRcdH1cblx0XHRcdFx0XG5cdFx0XHRcdCRoZWFkZXJDZWxsLmluc2VydEFmdGVyKGFjdGl2ZUNvbHVtblNlbGVjdGlvbnNbaW5kZXggLSAxXSk7XG5cdFx0XHR9KTtcblx0XHR9KTtcblx0XHRcblx0XHRyZXR1cm4gZmluYWxDb2x1bW5EZWZpbml0aW9ucztcblx0fVxuXHRcblx0LyoqXG5cdCAqIENyZWF0ZXMgYSBEYXRhVGFibGUgSW5zdGFuY2Vcblx0ICpcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBjcmVhdGUgYSBuZXcgaW5zdGFuY2Ugb2YgZGF0YXRhYmxlIGludG8gYSBgPHRhYmxlPmAgZWxlbWVudC4gSXQgZW5hYmxlc1xuXHQgKiBkZXZlbG9wZXJzIHRvIGVhc2lseSBwYXNzIHRoZSBjb25maWd1cmF0aW9uIG5lZWRlZCBmb3IgZGlmZmVyZW50IGFuZCBtb3JlIHNwZWNpYWwgc2l0dWF0aW9ucy5cblx0ICpcblx0ICogQHBhcmFtIHtqUXVlcnl9ICR0YXJnZXQgalF1ZXJ5IG9iamVjdCBmb3IgdGhlIHRhcmdldCB0YWJsZS5cblx0ICogQHBhcmFtIHtPYmplY3R9IGNvbmZpZ3VyYXRpb24gRGF0YVRhYmxlcyBjb25maWd1cmF0aW9uIGFwcGxpZWQgb24gdGhlIG5ldyBpbnN0YW5jZS5cblx0ICpcblx0ICogQHJldHVybiB7RGF0YVRhYmxlfSBSZXR1cm5zIHRoZSBEYXRhVGFibGUgQVBJIGluc3RhbmNlIChkaWZmZXJlbnQgZnJvbSB0aGUgalF1ZXJ5IG9iamVjdCkuXG5cdCAqL1xuXHRleHBvcnRzLmNyZWF0ZSA9IGZ1bmN0aW9uKCR0YXJnZXQsIGNvbmZpZ3VyYXRpb24pIHtcblx0XHRyZXR1cm4gJHRhcmdldC5EYXRhVGFibGUoY29uZmlndXJhdGlvbik7XG5cdH07XG5cdFxuXHQvKipcblx0ICogU2V0cyB0aGUgZXJyb3IgaGFuZGxlciBmb3Igc3BlY2lmaWMgRGF0YVRhYmxlLlxuXHQgKlxuXHQgKiBEYXRhVGFibGVzIHByb3ZpZGUgYSB1c2VmdWwgbWVjaGFuaXNtIHRoYXQgZW5hYmxlcyBkZXZlbG9wZXJzIHRvIGNvbnRyb2wgZXJyb3JzIGR1cmluZyBkYXRhIHBhcnNpbmcuXG5cdCAqIElmIHRoZXJlIGlzIGFuIGVycm9yIGluIHRoZSBBSkFYIHJlc3BvbnNlIG9yIHNvbWUgZGF0YSBhcmUgaW52YWxpZCBpbiB0aGUgSmF2YVNjcmlwdCBjb2RlIHlvdSBjYW4gdXNlIFxuXHQgKiB0aGlzIG1ldGhvZCB0byBjb250cm9sIHRoZSBiZWhhdmlvciBvZiB0aGUgYXBwIGFuZCBzaG93IG9yIGxvZyB0aGUgZXJyb3IgbWVzc2FnZXMuXG5cdCAqXG5cdCAqIHtAbGluayBodHRwOi8vZGF0YXRhYmxlcy5uZXQvcmVmZXJlbmNlL2V2ZW50L2Vycm9yfVxuXHQgKlxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBqUXVlcnkgb2JqZWN0IGZvciB0aGUgdGFyZ2V0IHRhYmxlLlxuXHQgKiBAcGFyYW0ge09iamVjdH0gY2FsbGJhY2sgUHJvdmlkZSBhIGNhbGxiYWNrIG1ldGhvZCBjYWxsZWQgd2l0aCB0aGUgXCJldmVudFwiLCBcInNldHRpbmdzXCIsIFwidGVjaE5vdGVcIiwgXG5cdCAqIFwibWVzc2FnZVwiIGFyZ3VtZW50cyAoc2VlIHByb3ZpZGVkIGxpbmspLlxuXHQgKi9cblx0ZXhwb3J0cy5lcnJvciA9IGZ1bmN0aW9uKCR0YXJnZXQsIGNhbGxiYWNrKSB7XG5cdFx0JC5mbi5kYXRhVGFibGUuZXh0LmVyck1vZGUgPSAnbm9uZSc7XG5cdFx0JHRhcmdldFxuXHRcdFx0Lm9uKCdlcnJvci5kdCcsIGNhbGxiYWNrKVxuXHRcdFx0Lm9uKCd4aHIuZHQnLCAoZXZlbnQsIHNldHRpbmdzLCBqc29uLCB4aHIpID0+IHtcblx0XHRcdFx0aWYgKGpzb24uZXhjZXB0aW9uID09PSB0cnVlKSB7XG5cdFx0XHRcdFx0Y2FsbGJhY2soZXZlbnQsIHNldHRpbmdzLCBudWxsLCBqc29uLm1lc3NhZ2UpOyBcblx0XHRcdFx0fVxuXHRcdFx0fSk7IFxuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFNldHMgdGhlIGNhbGxiYWNrIG1ldGhvZCB3aGVuIGFqYXggbG9hZCBvZiBkYXRhIGlzIGNvbXBsZXRlLlxuXHQgKlxuXHQgKiBUaGlzIG1ldGhvZCBpcyB1c2VmdWwgZm9yIGNoZWNraW5nIFBIUCBlcnJvcnMgb3IgbW9kaWZ5aW5nIHRoZSBkYXRhIGJlZm9yZVxuXHQgKiB0aGV5IGFyZSBkaXNwbGF5ZWQgdG8gdGhlIHNlcnZlci5cblx0ICpcblx0ICoge0BsaW5rIGh0dHA6Ly9kYXRhdGFibGVzLm5ldC9yZWZlcmVuY2UvZXZlbnQveGhyfVxuXHQgKlxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBqUXVlcnkgb2JqZWN0IGZvciB0aGUgdGFyZ2V0IHRhYmxlLlxuXHQgKiBAcGFyYW0ge0Z1bmN0aW9ufSBjYWxsYmFjayBQcm92aWRlIGEgY2FsbGJhY2sgbWV0aG9kIGNhbGxlZCB3aXRoIHRoZSBcImV2ZW50XCIsIFwic2V0dGluZ3NcIiwgXCJ0ZWNoTm90ZVwiLCBcblx0ICogXCJtZXNzYWdlXCIgYXJndW1lbnRzIChzZWUgcHJvdmlkZWQgbGluaykuXG5cdCAqL1xuXHRleHBvcnRzLmFqYXhDb21wbGV0ZSA9IGZ1bmN0aW9uKCR0YXJnZXQsIGNhbGxiYWNrKSB7XG5cdFx0JHRhcmdldC5vbigneGhyLmR0JywgY2FsbGJhY2spO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFNldHMgdGhlIHRhYmxlIGNvbHVtbiB0byBiZSBkaXNwbGF5ZWQgYXMgYW4gaW5kZXguXG5cdCAqXG5cdCAqIFRoaXMgbWV0aG9kIHdpbGwgZWFzaWx5IGVuYWJsZSB5b3UgdG8gc2V0IGEgY29sdW1uIGFzIGFuIGluZGV4IGNvbHVtbiwgdXNlZFxuXHQgKiBmb3IgbnVtYmVyaW5nIHRoZSB0YWJsZSByb3dzIHJlZ2FyZGxlc3Mgb2YgdGhlIHNlYXJjaCwgc29ydGluZyBhbmQgcm93IGNvdW50LlxuXHQgKlxuXHQgKiB7QGxpbmsgaHR0cDovL3d3dy5kYXRhdGFibGVzLm5ldC9leGFtcGxlcy9hcGkvY291bnRlcl9jb2x1bW5zLmh0bWx9XG5cdCAqXG5cdCAqIEBwYXJhbSB7alF1ZXJ5fSAkdGFyZ2V0IGpRdWVyeSBvYmplY3QgZm9yIHRoZSB0YXJnZXQgdGFibGUuXG5cdCAqIEBwYXJhbSB7TnVtYmVyfSBjb2x1bW5JbmRleCBaZXJvIGJhc2VkIGluZGV4IG9mIHRoZSBjb2x1bW4gdG8gYmUgaW5kZXhlZC5cblx0ICovXG5cdGV4cG9ydHMuaW5kZXhDb2x1bW4gPSBmdW5jdGlvbigkdGFyZ2V0LCBjb2x1bW5JbmRleCkge1xuXHRcdCR0YXJnZXQub24oJ29yZGVyLmR0IHNlYXJjaC5kdCcsIGZ1bmN0aW9uKCkge1xuXHRcdFx0JHRhcmdldC5EYXRhVGFibGUoKS5jb2x1bW4oY29sdW1uSW5kZXgsIHtcblx0XHRcdFx0c2VhcmNoOiAnYXBwbGllZCcsXG5cdFx0XHRcdG9yZGVyOiAnYXBwbGllZCdcblx0XHRcdH0pLm5vZGVzKCkuZWFjaChmdW5jdGlvbihjZWxsLCBpbmRleCkge1xuXHRcdFx0XHRjZWxsLmlubmVySFRNTCA9IGluZGV4ICsgMTtcblx0XHRcdH0pO1xuXHRcdH0pO1xuXHR9O1xuXHRcblx0LyoqXG5cdCAqIFJldHVybnMgdGhlIGdlcm1hbiB0cmFuc2xhdGlvbiBvZiB0aGUgRGF0YVRhYmxlc1xuXHQgKlxuXHQgKiBUaGlzIG1ldGhvZCBwcm92aWRlcyBhIHF1aWNrIHdheSB0byBnZXQgdGhlIGxhbmd1YWdlIEpTT04gd2l0aG91dCBoYXZpbmcgdG8gcGVyZm9ybVxuXHQgKiBhbmQgQUpBWCByZXF1ZXN0IHRvIHRoZSBzZXJ2ZXIuIElmIHlvdSBzZXR1cCB5b3VyIERhdGFUYWJsZSBtYW51YWxseSB5b3UgY2FuIHNldCB0aGVcblx0ICogXCJsYW5ndWFnZVwiIGF0dHJpYnV0ZSB3aXRoIHRoaXMgbWV0aG9kLlxuXHQgKlxuXHQgKiBAZGVwcmVjYXRlZCBTaW5jZSB2MS40LCB1c2UgdGhlIFwiZ2V0VHJhbnNsYXRpb25zXCIgbWV0aG9kIGluc3RlYWQuXG5cdCAqXG5cdCAqIEByZXR1cm4ge09iamVjdH0gUmV0dXJucyB0aGUgZ2VybWFuIHRyYW5zbGF0aW9uLCBtdXN0IGJlIHRoZSBzYW1lIGFzIHRoZSBcImdlcm1hbi5sYW5nLmpzb25cIiBmaWxlLlxuXHQgKi9cblx0ZXhwb3J0cy5nZXRHZXJtYW5UcmFuc2xhdGlvbiA9IGZ1bmN0aW9uKCkge1xuXHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSBnZXRHZXJtYW5UcmFuc2xhdGlvbiBtZXRob2QgaXMgZGVwcmVjYXRlZCBhbmQgd2lsbCBiZSByZW1vdmVkICdcblx0XHRcdCsgJ2luIEpTRSB2MS41LCBwbGVhc2UgdXNlIHRoZSBcImdldFRyYW5zbGF0aW9uc1wiIG1ldGhvZCBpbnN0ZWFkLicpO1xuXHRcdHJldHVybiBsYW5ndWFnZXMuZGU7XG5cdH07XG5cdFxuXHQvKipcblx0ICogR2V0IHRoZSBEYXRhVGFibGVzIHRyYW5zbGF0aW9uIGRlcGVuZGluZyB0aGUgbGFuZ3VhZ2UgY29kZSBwYXJhbWV0ZXIuXG5cdCAqXG5cdCAqIEBwYXJhbSB7U3RyaW5nfSBsYW5ndWFnZUNvZGUgUHJvdmlkZSAnZGUnIG9yICdlbicgKHlvdSBjYW4gYWxzbyB1c2UgdGhlIGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpIHRvXG5cdCAqIGdldCB0aGUgY3VycmVudCBsYW5ndWFnZSBjb2RlKS5cblx0ICpcblx0ICogQHJldHVybiB7T2JqZWN0fSBSZXR1cm5zIHRoZSB0cmFuc2xhdGlvbiBzdHJpbmdzIGluIGFuIG9iamVjdCBsaXRlcmFsIGFzIGRlc2NyaWJlZCBieSB0aGUgb2ZmaWNpYWwgRGF0YVRhYmxlc1xuXHQgKiBkb2N1bWVudGF0aW9uLlxuXHQgKlxuXHQgKiB7QGxpbmsgaHR0cHM6Ly93d3cuZGF0YXRhYmxlcy5uZXQvcGx1Zy1pbnMvaTE4bn1cblx0ICovXG5cdGV4cG9ydHMuZ2V0VHJhbnNsYXRpb25zID0gZnVuY3Rpb24obGFuZ3VhZ2VDb2RlKSB7XG5cdFx0aWYgKGxhbmd1YWdlc1tsYW5ndWFnZUNvZGVdID09PSB1bmRlZmluZWQpIHtcblx0XHRcdGpzZS5jb3JlLmRlYnVnLndhcm4oJ1RoZSByZXF1ZXN0ZWQgRGF0YVRhYmxlcyB0cmFuc2xhdGlvbiB3YXMgbm90IGZvdW5kOicsIGxhbmd1YWdlQ29kZSk7XG5cdFx0XHRsYW5ndWFnZUNvZGUgPSAnZW4nO1xuXHRcdH1cblx0XHRcblx0XHRyZXR1cm4gbGFuZ3VhZ2VzW2xhbmd1YWdlQ29kZV07XG5cdH07XG5cdFxuXHQvKipcblx0ICogUHJlcGFyZSB0YWJsZSBjb2x1bW5zLlxuXHQgKlxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIGNvbnZlcnQgdGhlIGNvbHVtbiBkZWZpbml0aW9ucyB0byBhIERhdGFUYWJsZSBjb21wYXRpYmxlIGZvcm1hdCBhbmQgYWxzbyByZW9yZGVyXG5cdCAqIHRoZSB0YWJsZSBoZWFkZXIgY2VsbHMgb2YgdGhlIFwidGhlYWRcIiBlbGVtZW50LlxuXHQgKlxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHRhcmdldCBUYWJsZSBqUXVlcnkgc2VsZWN0b3Igb2JqZWN0LlxuXHQgKiBAcGFyYW0ge09iamVjdH0gY29sdW1uRGVmaW5pdGlvbnMgQXJyYXkgY29udGFpbmluZyB0aGUgRGF0YVRhYmxlIGNvbHVtbiBkZWZpbml0aW9ucy5cblx0ICogQHBhcmFtIHtBcnJheX0gYWN0aXZlQ29sdW1uTmFtZXMgQXJyYXkgY29udGFpbmluZyB0aGUgc2x1Zy1uYW1lcyBvZiB0aGUgYWN0aXZlIGNvbHVtbnMuXG5cdCAqXG5cdCAqIEByZXR1cm4ge0FycmF5fSBSZXR1cm5zIGFycmF5IHdpdGggdGhlIGFjdGl2ZSBjb2x1bW4gZGVmaW5pdGlvbnMgcmVhZHkgdG8gdXNlIGluIERhdGFUYWJsZS5jb2x1bW5zIG9wdGlvbi5cblx0ICovXG5cdGV4cG9ydHMucHJlcGFyZUNvbHVtbnMgPSBmdW5jdGlvbigkdGFyZ2V0LCBjb2x1bW5EZWZpbml0aW9ucywgYWN0aXZlQ29sdW1uTmFtZXMpIHtcblx0XHRsZXQgY29udmVydGVkQ29sdW1uRGVmaW5pdGlvbnMgPSBbXTtcblx0XHRcblx0XHRmb3IgKGxldCBjb2x1bW5OYW1lIGluIGNvbHVtbkRlZmluaXRpb25zKSB7XG5cdFx0XHRsZXQgY29sdW1uRGVmaW5pdGlvbiA9IGNvbHVtbkRlZmluaXRpb25zW2NvbHVtbk5hbWVdO1xuXHRcdFx0Y29sdW1uRGVmaW5pdGlvbi5uYW1lID0gY29sdW1uTmFtZTtcblx0XHRcdGNvbnZlcnRlZENvbHVtbkRlZmluaXRpb25zLnB1c2goY29sdW1uRGVmaW5pdGlvbik7XG5cdFx0fVxuXHRcdFxuXHRcdHJldHVybiBfcmVvcmRlckNvbHVtbnMoJHRhcmdldCwgY29udmVydGVkQ29sdW1uRGVmaW5pdGlvbnMsIGFjdGl2ZUNvbHVtbk5hbWVzKTtcblx0fTtcblx0XG59KGpzZS5saWJzLmRhdGF0YWJsZSkpO1xuIl19
