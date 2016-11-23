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
gx.widgets.module(
	'datatable',
	
	['datatable'],
	
	/** @lends module:Widgets/datatable */
	
	function(data) {
		
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
		module.init = function(done) {
			$table = $this.DataTable(options);
			done();
		};
		
		// Return data to module engine.
		return module;
	});
