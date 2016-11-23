/* --------------------------------------------------------------
 statistic_box.js 2016-02-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Table Sorting Widget
 *
 * Widget to sort the categories and customers table.
 *
 * ### Example
 * 
 * ```html
 * <table data-gx-widget="table_sorting">
 *   <td data-use-table_sorting="true"
 *      data-column="model"
 *      data-section="categories"
 *      data-direction="desc"
 *      data-active-caret="false">
 *    Artikel-Nr.
 *  </td>
 * </table>
 * ```
 *
 * Parameters:
 *   - column: The column which changes the sort order
 *   - section: Section of the table. Example: "categories"
 *   - direction: Ascending or descending. Example: "desc"
 *   - active-caret: Should the caret be added to this element? Example "true"
 *
 * Events:
 *   - Triggering click event on the target element on the mapping hash
 *
 * @module Admin/Widgets/table_sorting
 * @requires jQueryUI-Library
 * @ignore
 */
gx.widgets.module(
	'table_sorting',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// ELEMENT DEFINITION
		// ------------------------------------------------------------------------
		
		// Elements
		var $this = $(this),
		
		// The hidden table row which contains the links for the specific sortings
			hiddenSortbar = 'tr.dataTableHeadingRow_sortbar.hidden',
			caretUp = '<i class="fa fa-caret-up caret"></i>',
			caretDown = '<i class="fa fa-caret-down caret"></i>';
		
		// ------------------------------------------------------------------------
		// VARIABLE DEFINITION
		// ------------------------------------------------------------------------
		
		// Widget defaults
		var defaults = {
				elementChildren: '[data-use-table_sorting="true"]',
				caret: '.caret'
			},
			options = $.extend(true, {}, defaults, data),
			module = {};
		
		
		// ------------------------------------------------------------------------
		// Mapping hash
		// ------------------------------------------------------------------------
		
		/**
		 * Mappings to the correct links to trigger the table sorting.
		 */
		var mapping = {
			categories: {
				sort: {
					asc: 'a.sort',
					desc: 'a.sort-desc'
				},
				name: {
					asc: 'a.name',
					desc: 'a.name-desc'
				},
				model: {
					asc: 'a.model',
					desc: 'a.model-desc'
				},
				stock: {
					asc: 'a.stock',
					desc: 'a.stock-desc'
				},
				status: {
					asc: 'a.status',
					desc: 'a.status-desc'
				},
				startpage: {
					asc: 'a.startpage',
					desc: 'a.startpage-desc'
				},
				price: {
					asc: 'a.price',
					desc: 'a.price-desc'
				},
				discount: {
					asc: 'a.discount',
					desc: 'a.discount-desc'
				}
			},
			customers: {
				lastName: {
					asc: 'a.customers_lastname',
					desc: 'a.customers_lastname-desc'
				},
				firstName: {
					asc: 'a.customers_firstname',
					desc: 'a.customers_firstname-desc'
				},
				dateAccountCreated: {
					asc: 'a.date_account_created',
					desc: 'a.date_account_created-desc'
				},
				dateLastLogon: {
					asc: 'a.date_last_logon',
					desc: 'a.date_last_logon-desc'
				}
			}
		};
		
		// ------------------------------------------------------------------------
		// PRIVATE METHODS
		// ------------------------------------------------------------------------
		
		/**
		 * Find Target Selector
		 *
		 * Looks for the target element in the mapping hash and returns the found element.
		 *
		 * @param {string} section Current section (e.g. 'customers', 'categories', etc.)
		 * @param {string} column Column identifier (e.g. 'model', 'price', etc)
		 * @param {string} direction Sort direction (e.g. 'asc', 'desc')
		 *
		 * @throws Error if the element could not be found in the mapping hash
		 *
		 * @returns {*|jQuery|HTMLElement}
		 * @private
		 */
		var _findTargetSelector = function(section, column, direction) {
			
			// If the link is available in the mapping hash
			if (section in mapping &&
				column in mapping[section] &&
				direction in mapping[section][column]
			) {
				// Check the current sort order direction to get the opposite direction
				var targetDirection = (direction === 'asc') ? 'desc' : 'asc';
				
				// The found element from the hash
				var $element = $(hiddenSortbar).find(mapping[section][column][targetDirection]);
				return $element;
			} else {
				throw new Error('Could not find target element');
			}
		};
		
		/**
		 * Open Target Link
		 *
		 * Maps the column header click events to the correct links.
		 *
		 * @param event
		 * @private
		 */
		var _openTargetLink = function(event) {
			// Clicked element
			var $sourceElement = $(event.target);
			
			// Retrieve data attributes from element
			var section = $sourceElement.data('section'),
				column = $sourceElement.data('column'),
				direction = $sourceElement.data('direction');
			
			// Find the correct target selector
			var $targetElement = _findTargetSelector(section, column, direction);
			
			var targetLink = $targetElement.attr('href');
			
			// Open the target elements link
			window.open(targetLink, '_self');
		};
		
		/**
		 * Register Children
		 *
		 * @private
		 */
		var _registerChildren = function() {
			$(options.elementChildren).on('click', _openTargetLink);
			
			// Trigger parent click when caret is clicked
			$(options.caret).on('click', function() {
				$(options.caret).parent().click();
			});
		};
		
		var _addCaret = function() {
			var $activeCaret = $('[data-active-caret="true"]');
			
			if ($activeCaret.data('direction') === 'asc') {
				$activeCaret.append(caretUp);
			} else {
				$activeCaret.append(caretDown);
			}
		};
		
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		/**
		 * Initialize method of the widget, called by the engine.
		 */
		module.init = function(done) {
			_addCaret();
			_registerChildren();
			done();
		};
		
		// Return data to module engine.
		return module;
	});
