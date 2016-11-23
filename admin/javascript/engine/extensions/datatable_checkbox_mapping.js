/* --------------------------------------------------------------
 datatable_checkbox_mapping.js 2016-10-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## DataTable Checkbox Mapping Extension
 *
 * This extension maps the bulk actions from a datatable dropdown to the selected checkbox carets. Bind this
 * extension into a datatable element which has a first.
 *
 * ### Options
 *
 * **Bulk Action Selector | `data-datatable_checkbox_mapping-bulk-action` | String | Optional**
 *
 * Provide a selector for the bulk action dropdown widget. Default value is '.bulk-action'.
 *
 * **Bulk Selection Checkbox Selector | `data-datatable_checkbox_mapping-bulk-selection` | String | Optional**
 *
 * Provide a selector for the bulk selection checkbox in the table header. Default value is '.bulk-selection'.
 * 
 * **Row Selection Checkbox Selector | `data-datatable_checkbox_mapping-row-selection` | String | Optional**
 *
 * Provide a selector for the row selection checkbox in the table body. Default value is 'tbody tr input:checkbox'.
 *
 * **Caret Icon Class | `data-datatable_checkbox_mapping-caret-icon-class` | String | Optional**
 *
 * Provide a FontAwesome icon class for the checkbox caret. Default value is 'fa-caret-down'. Provide only the class
 * name without dots or the "fa" class.
 *
 * @module Admin/Extensions/datatable_checkbox_mapping
 */
gx.extensions.module(
	'datatable_checkbox_mapping',
	
	[],
	
	function(data) {
		
		'use strict';
		
		// ------------------------------------------------------------------------
		// VARIABLES
		// ------------------------------------------------------------------------
		
		/**
		 * Module Selector
		 *
		 * @type {jQuery}
		 */
		const $this = $(this);
		
		/**
		 * Default Options
		 *
		 * @type {Object}
		 */
		const defaults = {
			bulkAction: '.bulk-action',
			bulkSelection: '.bulk-selection',
			caretIconClass: 'fa-caret-down',
			rowSelection: 'tbody tr input:checkbox'
		};
		
		/**
		 * Final Options
		 *
		 * @type {Object}
		 */
		const options = $.extend(true, {}, defaults, data);
		
		/**
		 * Bulk Action Selector
		 *
		 * @type {jQuery}
		 */
		const $bulkAction = $(options.bulkAction);
		
		/**
		 * Bulk Selection Selector
		 *
		 * @type {jQuery}
		 */
		const $bulkSelection = $this.find(options.bulkSelection).last();
		
		/**
		 * Module Instance
		 *
		 * @type {Object}
		 */
		const module = {};
		
		// ------------------------------------------------------------------------
		// FUNCTIONS
		// ------------------------------------------------------------------------
		
		/**
		 * Toggle the dropdown menu under the caret.
		 *
		 * @param {jQuery.Event} event Triggered event.
		 */
		function _toggleDropdownMenu(event) {
			event.stopPropagation();
			event.preventDefault();
			
			if ($bulkAction.hasClass('open')) {
				$bulkAction.removeClass('open');
				return;
			}
			
			const caretPosition = $(event.target).offset();
			const $dropdownMenu = $bulkAction.find('.dropdown-menu');
			
			// Open the dropdown menu.
			$bulkAction.addClass('open');
			
			// Reposition the dropdown menu near the clicked caret.
			$dropdownMenu.offset({
				top: caretPosition.top + 16,
				left: caretPosition.left
			});
			
			// Don't show the long empty dropdown menu box when it is repositioned.
			$dropdownMenu.css({bottom: 'initial'});
			
			// Show the dropdown menu under or above the caret, depending on the viewport.
			if (_dropdownIsOutOfView($dropdownMenu)) {
				$dropdownMenu.offset({
					top: caretPosition.top - $dropdownMenu.outerHeight(),
					left: caretPosition.left
				});
			}
		}
		
		/**
		 * Reset the dropdown position to its original state.
		 */
		function _resetDropdownPosition() {
			$bulkAction.find('.dropdown-menu').css({
				top: '',
				left: '',
				bottom: ''
			});
		}
		
		/**
		 * Add a caret to the table head checked checkbox.
		 */
		function _addCaretToBulkSelection() {
			const $th = $bulkSelection.parents('th');
			
			if ($th.find('.' + options.caretIconClass).length === 0) {
				$th.append(`<i class="fa ${options.caretIconClass}"></i>`);
			}
		}
		
		/**
		 * Remove the caret from the bulk selection checkbox.
		 */
		function _removeCaretFromBulkSelection() {
			$bulkSelection.parents('th').find('.' + options.caretIconClass).remove();
		}
		
		/**
		 * Add a caret to the checked checkbox.
		 *
		 * @param {jQuery.Event} event Triggered event.
		 */
		function _addCaretToActivatedCheckbox(event) {
			$(event.target).parents('td').append(`<i class="fa ${options.caretIconClass}"></i>`);
		}
		
		/**
		 * Remove the caret from the checkbox if the checkbox is unchecked.
		 *
		 * @param {jQuery.Event} event Triggered event.
		 */
		function _removeCaretFromCheckbox(event) {
			$(event.target).parents('tr').find('.' + options.caretIconClass).remove();
		}
		
		/**
		 * Start listening for click events for the caret symbol.
		 *
		 * When the caret symbol gets clicked, show the dropdown menu.
		 */
		function _listenForCaretClickEvents() {
			$this.find('tr .' + options.caretIconClass).off('click').on('click', _toggleDropdownMenu);
		}
		
		/**
		 * Set the bulk selection state.
		 *
		 * @param {Boolean} isChecked Whether the checkbox will be checked or not.
		 */
		function _setBulkSelectionState(isChecked) {
			$bulkSelection.prop('checked', isChecked);
			
			if (isChecked) {
				$bulkSelection.parents('.single-checkbox').addClass('checked');
				_addCaretToBulkSelection();
				_listenForCaretClickEvents();
			} else {
				$bulkSelection.parents('.single-checkbox').removeClass('checked');
				_removeCaretFromBulkSelection();
			}
		}
		
		/**
		 * Checks if the provided dropdown is outside of the viewport (in height).
		 *
		 * @param {jQuery} $dropdownMenu Dropdown menu selector.
		 *
		 * @return {Boolean}
		 */
		function _dropdownIsOutOfView($dropdownMenu) {
			const dropDownMenuOffset = $dropdownMenu.offset().top + $dropdownMenu.outerHeight() + 50;
			const windowHeight = window.innerHeight + $(window).scrollTop();
			return dropDownMenuOffset > windowHeight;
		}
		
		/**
		 * On Single Checkbox Ready Event
		 *
		 * Bind the checkbox mapping functionality on the table. We need to wait for the "single_checkbox:ready",
		 * that will be triggered with every table re-draw. Whenever a row checkbox is clicked the bulk-action
		 * caret icon will be added next to it.
		 */
		function _onSingleCheckboxReady() {
			// Find all checkboxes table body checkboxes.
			const $tableBodyCheckboxes = $this.find(options.rowSelection);
			
			// Table data checkbox event handling.
			$tableBodyCheckboxes.on('change', event => {
				// Close any open dropdown menus.
				$bulkAction.removeClass('open');
				
				if ($(event.target).prop('checked')) {
					_addCaretToActivatedCheckbox(event);
					_listenForCaretClickEvents();
				} else {
					_removeCaretFromCheckbox(event);
				}
				
				// Activate the table head checkbox if all checkboxes are activated. Otherwise deactivate it.
				_setBulkSelectionState($tableBodyCheckboxes.not(':checked').length === 0);
			});
		}
		
		/**
		 * Add or remove the caret from the table head checkbox.
		 *
		 * @param {jQuery.Event} event
		 */
		function _onBulkSelectionChange(event) {
			if ($bulkSelection.parents('.single-checkbox').length === 0) {
				return; // Do not proceed with the function if the thead single-checkbox is not ready yet.
			}
			
			if ($bulkSelection.prop('checked')) {
				_addCaretToBulkSelection();
				_listenForCaretClickEvents();
			} else {
				_removeCaretFromBulkSelection(event);
			}
		}
		
		/**
		 *  Event handling for the original dropdown button click.
		 */
		function _onBulkActionDropdownToggleClick() {
			_resetDropdownPosition();
		}
		
		// ------------------------------------------------------------------------
		// INITIALIZATION
		// ------------------------------------------------------------------------
		
		module.init = function(done) {
			$this.on('single_checkbox:ready', _onSingleCheckboxReady);
			$bulkSelection.on('change', _onBulkSelectionChange);
			$bulkAction.find('.dropdown-toggle').on('click', _onBulkActionDropdownToggleClick);
			done();
		};
		return module;
	});
