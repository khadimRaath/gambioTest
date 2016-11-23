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
(function(exports) {
	
	'use strict';
	
	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------
	
	let languages = {
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
		$.each(columnDefinitions, (index, columnDefinition) => {
			$target.find('thead tr').each(function() {
				let $headerCell = $(this).find(`[data-column-name="${columnDefinition.name}"]`);
				
				if (columnDefinition.data !== null && activeColumnNames.indexOf(columnDefinition.name) === -1) {
					$headerCell.hide();
				}
			});
		});
		
		// Prepare the active column definitions.
		let finalColumnDefinitions = [],
			columnIndexes = [];
		
		$.each(activeColumnNames, (index, name) => {
			$.each(columnDefinitions, (index, columnDefinition) => {
				if (columnDefinition.name === name) {
					// Add the active column definition in the "finalColumnDefinitions" array.
					finalColumnDefinitions.push(columnDefinition);
					const headerCellIndex = $target
						.find(`thead:first tr:first [data-column-name="${columnDefinition.name}"]`)
						.index();
					columnIndexes.push(headerCellIndex);
					return true; // continue
				}
			});
		});
		
		finalColumnDefinitions.sort((a, b) => {
			const aIndex = activeColumnNames.indexOf(a.name);
			const bIndex = activeColumnNames.indexOf(b.name);
			
			if (aIndex < bIndex) {
				return -1;
			} else if (aIndex > bIndex) {
				return 1;
			} else {
				return 0;
			}
		});
		
		// Reorder the table header elements depending the activeColumnNames order.
		$target.find('thead tr').each(function() {
			let activeColumnSelections = [$(this).find('th:first')];
			
			// Sort the columns in the correct order.
			columnIndexes.forEach((index) => {
				let $headerCell = $(this).find('th').eq(index);
				activeColumnSelections.push($headerCell);
			});
			
			// Move the columns to their final position.
			activeColumnSelections.forEach(function($headerCell, index) {
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
	exports.create = function($target, configuration) {
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
	exports.error = function($target, callback) {
		$.fn.dataTable.ext.errMode = 'none';
		$target
			.on('error.dt', callback)
			.on('xhr.dt', (event, settings, json, xhr) => {
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
	exports.ajaxComplete = function($target, callback) {
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
	exports.indexColumn = function($target, columnIndex) {
		$target.on('order.dt search.dt', function() {
			$target.DataTable().column(columnIndex, {
				search: 'applied',
				order: 'applied'
			}).nodes().each(function(cell, index) {
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
	exports.getGermanTranslation = function() {
		jse.core.debug.warn('The getGermanTranslation method is deprecated and will be removed '
			+ 'in JSE v1.5, please use the "getTranslations" method instead.');
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
	exports.getTranslations = function(languageCode) {
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
	exports.prepareColumns = function($target, columnDefinitions, activeColumnNames) {
		let convertedColumnDefinitions = [];
		
		for (let columnName in columnDefinitions) {
			let columnDefinition = columnDefinitions[columnName];
			columnDefinition.name = columnName;
			convertedColumnDefinitions.push(columnDefinition);
		}
		
		return _reorderColumns($target, convertedColumnDefinitions, activeColumnNames);
	};
	
}(jse.libs.datatable));
