'use strict';

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
gx.extensions.module('datatable_checkbox_mapping', [], function (data) {

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
	var options = $.extend(true, {}, defaults, data);

	/**
  * Bulk Action Selector
  *
  * @type {jQuery}
  */
	var $bulkAction = $(options.bulkAction);

	/**
  * Bulk Selection Selector
  *
  * @type {jQuery}
  */
	var $bulkSelection = $this.find(options.bulkSelection).last();

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

		var caretPosition = $(event.target).offset();
		var $dropdownMenu = $bulkAction.find('.dropdown-menu');

		// Open the dropdown menu.
		$bulkAction.addClass('open');

		// Reposition the dropdown menu near the clicked caret.
		$dropdownMenu.offset({
			top: caretPosition.top + 16,
			left: caretPosition.left
		});

		// Don't show the long empty dropdown menu box when it is repositioned.
		$dropdownMenu.css({ bottom: 'initial' });

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
		var $th = $bulkSelection.parents('th');

		if ($th.find('.' + options.caretIconClass).length === 0) {
			$th.append('<i class="fa ' + options.caretIconClass + '"></i>');
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
		$(event.target).parents('td').append('<i class="fa ' + options.caretIconClass + '"></i>');
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
		var dropDownMenuOffset = $dropdownMenu.offset().top + $dropdownMenu.outerHeight() + 50;
		var windowHeight = window.innerHeight + $(window).scrollTop();
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
		var $tableBodyCheckboxes = $this.find(options.rowSelection);

		// Table data checkbox event handling.
		$tableBodyCheckboxes.on('change', function (event) {
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

	module.init = function (done) {
		$this.on('single_checkbox:ready', _onSingleCheckboxReady);
		$bulkSelection.on('change', _onBulkSelectionChange);
		$bulkAction.find('.dropdown-toggle').on('click', _onBulkActionDropdownToggleClick);
		done();
	};
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9jaGVja2JveF9tYXBwaW5nLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsImJ1bGtBY3Rpb24iLCJidWxrU2VsZWN0aW9uIiwiY2FyZXRJY29uQ2xhc3MiLCJyb3dTZWxlY3Rpb24iLCJvcHRpb25zIiwiZXh0ZW5kIiwiJGJ1bGtBY3Rpb24iLCIkYnVsa1NlbGVjdGlvbiIsImZpbmQiLCJsYXN0IiwiX3RvZ2dsZURyb3Bkb3duTWVudSIsImV2ZW50Iiwic3RvcFByb3BhZ2F0aW9uIiwicHJldmVudERlZmF1bHQiLCJoYXNDbGFzcyIsInJlbW92ZUNsYXNzIiwiY2FyZXRQb3NpdGlvbiIsInRhcmdldCIsIm9mZnNldCIsIiRkcm9wZG93bk1lbnUiLCJhZGRDbGFzcyIsInRvcCIsImxlZnQiLCJjc3MiLCJib3R0b20iLCJfZHJvcGRvd25Jc091dE9mVmlldyIsIm91dGVySGVpZ2h0IiwiX3Jlc2V0RHJvcGRvd25Qb3NpdGlvbiIsIl9hZGRDYXJldFRvQnVsa1NlbGVjdGlvbiIsIiR0aCIsInBhcmVudHMiLCJsZW5ndGgiLCJhcHBlbmQiLCJfcmVtb3ZlQ2FyZXRGcm9tQnVsa1NlbGVjdGlvbiIsInJlbW92ZSIsIl9hZGRDYXJldFRvQWN0aXZhdGVkQ2hlY2tib3giLCJfcmVtb3ZlQ2FyZXRGcm9tQ2hlY2tib3giLCJfbGlzdGVuRm9yQ2FyZXRDbGlja0V2ZW50cyIsIm9mZiIsIm9uIiwiX3NldEJ1bGtTZWxlY3Rpb25TdGF0ZSIsImlzQ2hlY2tlZCIsInByb3AiLCJkcm9wRG93bk1lbnVPZmZzZXQiLCJ3aW5kb3dIZWlnaHQiLCJ3aW5kb3ciLCJpbm5lckhlaWdodCIsInNjcm9sbFRvcCIsIl9vblNpbmdsZUNoZWNrYm94UmVhZHkiLCIkdGFibGVCb2R5Q2hlY2tib3hlcyIsIm5vdCIsIl9vbkJ1bGtTZWxlY3Rpb25DaGFuZ2UiLCJfb25CdWxrQWN0aW9uRHJv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aGVja2JveF9tYXBwaW5nLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGRhdGF0YWJsZV9jaGVja2JveF9tYXBwaW5nLmpzIDIwMTYtMTAtMThcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgRGF0YVRhYmxlIENoZWNrYm94IE1hcHBpbmcgRXh0ZW5zaW9uXHJcbiAqXHJcbiAqIFRoaXMgZXh0ZW5zaW9uIG1hcHMgdGhlIGJ1bGsgYWN0aW9ucyBmcm9tIGEgZGF0YXRhYmxlIGRyb3Bkb3duIHRvIHRoZSBzZWxlY3RlZCBjaGVja2JveCBjYXJldHMuIEJpbmQgdGhpc1xyXG4gKiBleHRlbnNpb24gaW50byBhIGRhdGF0YWJsZSBlbGVtZW50IHdoaWNoIGhhcyBhIGZpcnN0LlxyXG4gKlxyXG4gKiAjIyMgT3B0aW9uc1xyXG4gKlxyXG4gKiAqKkJ1bGsgQWN0aW9uIFNlbGVjdG9yIHwgYGRhdGEtZGF0YXRhYmxlX2NoZWNrYm94X21hcHBpbmctYnVsay1hY3Rpb25gIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxyXG4gKlxyXG4gKiBQcm92aWRlIGEgc2VsZWN0b3IgZm9yIHRoZSBidWxrIGFjdGlvbiBkcm9wZG93biB3aWRnZXQuIERlZmF1bHQgdmFsdWUgaXMgJy5idWxrLWFjdGlvbicuXHJcbiAqXHJcbiAqICoqQnVsayBTZWxlY3Rpb24gQ2hlY2tib3ggU2VsZWN0b3IgfCBgZGF0YS1kYXRhdGFibGVfY2hlY2tib3hfbWFwcGluZy1idWxrLXNlbGVjdGlvbmAgfCBTdHJpbmcgfCBPcHRpb25hbCoqXHJcbiAqXHJcbiAqIFByb3ZpZGUgYSBzZWxlY3RvciBmb3IgdGhlIGJ1bGsgc2VsZWN0aW9uIGNoZWNrYm94IGluIHRoZSB0YWJsZSBoZWFkZXIuIERlZmF1bHQgdmFsdWUgaXMgJy5idWxrLXNlbGVjdGlvbicuXHJcbiAqIFxyXG4gKiAqKlJvdyBTZWxlY3Rpb24gQ2hlY2tib3ggU2VsZWN0b3IgfCBgZGF0YS1kYXRhdGFibGVfY2hlY2tib3hfbWFwcGluZy1yb3ctc2VsZWN0aW9uYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcclxuICpcclxuICogUHJvdmlkZSBhIHNlbGVjdG9yIGZvciB0aGUgcm93IHNlbGVjdGlvbiBjaGVja2JveCBpbiB0aGUgdGFibGUgYm9keS4gRGVmYXVsdCB2YWx1ZSBpcyAndGJvZHkgdHIgaW5wdXQ6Y2hlY2tib3gnLlxyXG4gKlxyXG4gKiAqKkNhcmV0IEljb24gQ2xhc3MgfCBgZGF0YS1kYXRhdGFibGVfY2hlY2tib3hfbWFwcGluZy1jYXJldC1pY29uLWNsYXNzYCB8IFN0cmluZyB8IE9wdGlvbmFsKipcclxuICpcclxuICogUHJvdmlkZSBhIEZvbnRBd2Vzb21lIGljb24gY2xhc3MgZm9yIHRoZSBjaGVja2JveCBjYXJldC4gRGVmYXVsdCB2YWx1ZSBpcyAnZmEtY2FyZXQtZG93bicuIFByb3ZpZGUgb25seSB0aGUgY2xhc3NcclxuICogbmFtZSB3aXRob3V0IGRvdHMgb3IgdGhlIFwiZmFcIiBjbGFzcy5cclxuICpcclxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2RhdGF0YWJsZV9jaGVja2JveF9tYXBwaW5nXHJcbiAqL1xyXG5neC5leHRlbnNpb25zLm1vZHVsZShcclxuXHQnZGF0YXRhYmxlX2NoZWNrYm94X21hcHBpbmcnLFxyXG5cdFxyXG5cdFtdLFxyXG5cdFxyXG5cdGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcdFxyXG5cdFx0J3VzZSBzdHJpY3QnO1xyXG5cdFx0XHJcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcdC8vIFZBUklBQkxFU1xyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge2pRdWVyeX1cclxuXHRcdCAqL1xyXG5cdFx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIERlZmF1bHQgT3B0aW9uc1xyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0XHQgKi9cclxuXHRcdGNvbnN0IGRlZmF1bHRzID0ge1xyXG5cdFx0XHRidWxrQWN0aW9uOiAnLmJ1bGstYWN0aW9uJyxcclxuXHRcdFx0YnVsa1NlbGVjdGlvbjogJy5idWxrLXNlbGVjdGlvbicsXHJcblx0XHRcdGNhcmV0SWNvbkNsYXNzOiAnZmEtY2FyZXQtZG93bicsXHJcblx0XHRcdHJvd1NlbGVjdGlvbjogJ3Rib2R5IHRyIGlucHV0OmNoZWNrYm94J1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBGaW5hbCBPcHRpb25zXHJcblx0XHQgKlxyXG5cdFx0ICogQHR5cGUge09iamVjdH1cclxuXHRcdCAqL1xyXG5cdFx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogQnVsayBBY3Rpb24gU2VsZWN0b3JcclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdFx0ICovXHJcblx0XHRjb25zdCAkYnVsa0FjdGlvbiA9ICQob3B0aW9ucy5idWxrQWN0aW9uKTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBCdWxrIFNlbGVjdGlvbiBTZWxlY3RvclxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0XHQgKi9cclxuXHRcdGNvbnN0ICRidWxrU2VsZWN0aW9uID0gJHRoaXMuZmluZChvcHRpb25zLmJ1bGtTZWxlY3Rpb24pLmxhc3QoKTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdFx0ICovXHJcblx0XHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBGVU5DVElPTlNcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIFRvZ2dsZSB0aGUgZHJvcGRvd24gbWVudSB1bmRlciB0aGUgY2FyZXQuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IFRyaWdnZXJlZCBldmVudC5cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gX3RvZ2dsZURyb3Bkb3duTWVudShldmVudCkge1xyXG5cdFx0XHRldmVudC5zdG9wUHJvcGFnYXRpb24oKTtcclxuXHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcclxuXHRcdFx0XHJcblx0XHRcdGlmICgkYnVsa0FjdGlvbi5oYXNDbGFzcygnb3BlbicpKSB7XHJcblx0XHRcdFx0JGJ1bGtBY3Rpb24ucmVtb3ZlQ2xhc3MoJ29wZW4nKTtcclxuXHRcdFx0XHRyZXR1cm47XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdGNvbnN0IGNhcmV0UG9zaXRpb24gPSAkKGV2ZW50LnRhcmdldCkub2Zmc2V0KCk7XHJcblx0XHRcdGNvbnN0ICRkcm9wZG93bk1lbnUgPSAkYnVsa0FjdGlvbi5maW5kKCcuZHJvcGRvd24tbWVudScpO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gT3BlbiB0aGUgZHJvcGRvd24gbWVudS5cclxuXHRcdFx0JGJ1bGtBY3Rpb24uYWRkQ2xhc3MoJ29wZW4nKTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIFJlcG9zaXRpb24gdGhlIGRyb3Bkb3duIG1lbnUgbmVhciB0aGUgY2xpY2tlZCBjYXJldC5cclxuXHRcdFx0JGRyb3Bkb3duTWVudS5vZmZzZXQoe1xyXG5cdFx0XHRcdHRvcDogY2FyZXRQb3NpdGlvbi50b3AgKyAxNixcclxuXHRcdFx0XHRsZWZ0OiBjYXJldFBvc2l0aW9uLmxlZnRcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBEb24ndCBzaG93IHRoZSBsb25nIGVtcHR5IGRyb3Bkb3duIG1lbnUgYm94IHdoZW4gaXQgaXMgcmVwb3NpdGlvbmVkLlxyXG5cdFx0XHQkZHJvcGRvd25NZW51LmNzcyh7Ym90dG9tOiAnaW5pdGlhbCd9KTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIFNob3cgdGhlIGRyb3Bkb3duIG1lbnUgdW5kZXIgb3IgYWJvdmUgdGhlIGNhcmV0LCBkZXBlbmRpbmcgb24gdGhlIHZpZXdwb3J0LlxyXG5cdFx0XHRpZiAoX2Ryb3Bkb3duSXNPdXRPZlZpZXcoJGRyb3Bkb3duTWVudSkpIHtcclxuXHRcdFx0XHQkZHJvcGRvd25NZW51Lm9mZnNldCh7XHJcblx0XHRcdFx0XHR0b3A6IGNhcmV0UG9zaXRpb24udG9wIC0gJGRyb3Bkb3duTWVudS5vdXRlckhlaWdodCgpLFxyXG5cdFx0XHRcdFx0bGVmdDogY2FyZXRQb3NpdGlvbi5sZWZ0XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZXNldCB0aGUgZHJvcGRvd24gcG9zaXRpb24gdG8gaXRzIG9yaWdpbmFsIHN0YXRlLlxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBfcmVzZXREcm9wZG93blBvc2l0aW9uKCkge1xyXG5cdFx0XHQkYnVsa0FjdGlvbi5maW5kKCcuZHJvcGRvd24tbWVudScpLmNzcyh7XHJcblx0XHRcdFx0dG9wOiAnJyxcclxuXHRcdFx0XHRsZWZ0OiAnJyxcclxuXHRcdFx0XHRib3R0b206ICcnXHJcblx0XHRcdH0pO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEFkZCBhIGNhcmV0IHRvIHRoZSB0YWJsZSBoZWFkIGNoZWNrZWQgY2hlY2tib3guXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9hZGRDYXJldFRvQnVsa1NlbGVjdGlvbigpIHtcclxuXHRcdFx0Y29uc3QgJHRoID0gJGJ1bGtTZWxlY3Rpb24ucGFyZW50cygndGgnKTtcclxuXHRcdFx0XHJcblx0XHRcdGlmICgkdGguZmluZCgnLicgKyBvcHRpb25zLmNhcmV0SWNvbkNsYXNzKS5sZW5ndGggPT09IDApIHtcclxuXHRcdFx0XHQkdGguYXBwZW5kKGA8aSBjbGFzcz1cImZhICR7b3B0aW9ucy5jYXJldEljb25DbGFzc31cIj48L2k+YCk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBSZW1vdmUgdGhlIGNhcmV0IGZyb20gdGhlIGJ1bGsgc2VsZWN0aW9uIGNoZWNrYm94LlxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBfcmVtb3ZlQ2FyZXRGcm9tQnVsa1NlbGVjdGlvbigpIHtcclxuXHRcdFx0JGJ1bGtTZWxlY3Rpb24ucGFyZW50cygndGgnKS5maW5kKCcuJyArIG9wdGlvbnMuY2FyZXRJY29uQ2xhc3MpLnJlbW92ZSgpO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEFkZCBhIGNhcmV0IHRvIHRoZSBjaGVja2VkIGNoZWNrYm94LlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBUcmlnZ2VyZWQgZXZlbnQuXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9hZGRDYXJldFRvQWN0aXZhdGVkQ2hlY2tib3goZXZlbnQpIHtcclxuXHRcdFx0JChldmVudC50YXJnZXQpLnBhcmVudHMoJ3RkJykuYXBwZW5kKGA8aSBjbGFzcz1cImZhICR7b3B0aW9ucy5jYXJldEljb25DbGFzc31cIj48L2k+YCk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogUmVtb3ZlIHRoZSBjYXJldCBmcm9tIHRoZSBjaGVja2JveCBpZiB0aGUgY2hlY2tib3ggaXMgdW5jaGVja2VkLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBUcmlnZ2VyZWQgZXZlbnQuXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9yZW1vdmVDYXJldEZyb21DaGVja2JveChldmVudCkge1xyXG5cdFx0XHQkKGV2ZW50LnRhcmdldCkucGFyZW50cygndHInKS5maW5kKCcuJyArIG9wdGlvbnMuY2FyZXRJY29uQ2xhc3MpLnJlbW92ZSgpO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIFN0YXJ0IGxpc3RlbmluZyBmb3IgY2xpY2sgZXZlbnRzIGZvciB0aGUgY2FyZXQgc3ltYm9sLlxyXG5cdFx0ICpcclxuXHRcdCAqIFdoZW4gdGhlIGNhcmV0IHN5bWJvbCBnZXRzIGNsaWNrZWQsIHNob3cgdGhlIGRyb3Bkb3duIG1lbnUuXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9saXN0ZW5Gb3JDYXJldENsaWNrRXZlbnRzKCkge1xyXG5cdFx0XHQkdGhpcy5maW5kKCd0ciAuJyArIG9wdGlvbnMuY2FyZXRJY29uQ2xhc3MpLm9mZignY2xpY2snKS5vbignY2xpY2snLCBfdG9nZ2xlRHJvcGRvd25NZW51KTtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBTZXQgdGhlIGJ1bGsgc2VsZWN0aW9uIHN0YXRlLlxyXG5cdFx0ICpcclxuXHRcdCAqIEBwYXJhbSB7Qm9vbGVhbn0gaXNDaGVja2VkIFdoZXRoZXIgdGhlIGNoZWNrYm94IHdpbGwgYmUgY2hlY2tlZCBvciBub3QuXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9zZXRCdWxrU2VsZWN0aW9uU3RhdGUoaXNDaGVja2VkKSB7XHJcblx0XHRcdCRidWxrU2VsZWN0aW9uLnByb3AoJ2NoZWNrZWQnLCBpc0NoZWNrZWQpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKGlzQ2hlY2tlZCkge1xyXG5cdFx0XHRcdCRidWxrU2VsZWN0aW9uLnBhcmVudHMoJy5zaW5nbGUtY2hlY2tib3gnKS5hZGRDbGFzcygnY2hlY2tlZCcpO1xyXG5cdFx0XHRcdF9hZGRDYXJldFRvQnVsa1NlbGVjdGlvbigpO1xyXG5cdFx0XHRcdF9saXN0ZW5Gb3JDYXJldENsaWNrRXZlbnRzKCk7XHJcblx0XHRcdH0gZWxzZSB7XHJcblx0XHRcdFx0JGJ1bGtTZWxlY3Rpb24ucGFyZW50cygnLnNpbmdsZS1jaGVja2JveCcpLnJlbW92ZUNsYXNzKCdjaGVja2VkJyk7XHJcblx0XHRcdFx0X3JlbW92ZUNhcmV0RnJvbUJ1bGtTZWxlY3Rpb24oKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIENoZWNrcyBpZiB0aGUgcHJvdmlkZWQgZHJvcGRvd24gaXMgb3V0c2lkZSBvZiB0aGUgdmlld3BvcnQgKGluIGhlaWdodCkuXHJcblx0XHQgKlxyXG5cdFx0ICogQHBhcmFtIHtqUXVlcnl9ICRkcm9wZG93bk1lbnUgRHJvcGRvd24gbWVudSBzZWxlY3Rvci5cclxuXHRcdCAqXHJcblx0XHQgKiBAcmV0dXJuIHtCb29sZWFufVxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBfZHJvcGRvd25Jc091dE9mVmlldygkZHJvcGRvd25NZW51KSB7XHJcblx0XHRcdGNvbnN0IGRyb3BEb3duTWVudU9mZnNldCA9ICRkcm9wZG93bk1lbnUub2Zmc2V0KCkudG9wICsgJGRyb3Bkb3duTWVudS5vdXRlckhlaWdodCgpICsgNTA7XHJcblx0XHRcdGNvbnN0IHdpbmRvd0hlaWdodCA9IHdpbmRvdy5pbm5lckhlaWdodCArICQod2luZG93KS5zY3JvbGxUb3AoKTtcclxuXHRcdFx0cmV0dXJuIGRyb3BEb3duTWVudU9mZnNldCA+IHdpbmRvd0hlaWdodDtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBPbiBTaW5nbGUgQ2hlY2tib3ggUmVhZHkgRXZlbnRcclxuXHRcdCAqXHJcblx0XHQgKiBCaW5kIHRoZSBjaGVja2JveCBtYXBwaW5nIGZ1bmN0aW9uYWxpdHkgb24gdGhlIHRhYmxlLiBXZSBuZWVkIHRvIHdhaXQgZm9yIHRoZSBcInNpbmdsZV9jaGVja2JveDpyZWFkeVwiLFxyXG5cdFx0ICogdGhhdCB3aWxsIGJlIHRyaWdnZXJlZCB3aXRoIGV2ZXJ5IHRhYmxlIHJlLWRyYXcuIFdoZW5ldmVyIGEgcm93IGNoZWNrYm94IGlzIGNsaWNrZWQgdGhlIGJ1bGstYWN0aW9uXHJcblx0XHQgKiBjYXJldCBpY29uIHdpbGwgYmUgYWRkZWQgbmV4dCB0byBpdC5cclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gX29uU2luZ2xlQ2hlY2tib3hSZWFkeSgpIHtcclxuXHRcdFx0Ly8gRmluZCBhbGwgY2hlY2tib3hlcyB0YWJsZSBib2R5IGNoZWNrYm94ZXMuXHJcblx0XHRcdGNvbnN0ICR0YWJsZUJvZHlDaGVja2JveGVzID0gJHRoaXMuZmluZChvcHRpb25zLnJvd1NlbGVjdGlvbik7XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBUYWJsZSBkYXRhIGNoZWNrYm94IGV2ZW50IGhhbmRsaW5nLlxyXG5cdFx0XHQkdGFibGVCb2R5Q2hlY2tib3hlcy5vbignY2hhbmdlJywgZXZlbnQgPT4ge1xyXG5cdFx0XHRcdC8vIENsb3NlIGFueSBvcGVuIGRyb3Bkb3duIG1lbnVzLlxyXG5cdFx0XHRcdCRidWxrQWN0aW9uLnJlbW92ZUNsYXNzKCdvcGVuJyk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0aWYgKCQoZXZlbnQudGFyZ2V0KS5wcm9wKCdjaGVja2VkJykpIHtcclxuXHRcdFx0XHRcdF9hZGRDYXJldFRvQWN0aXZhdGVkQ2hlY2tib3goZXZlbnQpO1xyXG5cdFx0XHRcdFx0X2xpc3RlbkZvckNhcmV0Q2xpY2tFdmVudHMoKTtcclxuXHRcdFx0XHR9IGVsc2Uge1xyXG5cdFx0XHRcdFx0X3JlbW92ZUNhcmV0RnJvbUNoZWNrYm94KGV2ZW50KTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gQWN0aXZhdGUgdGhlIHRhYmxlIGhlYWQgY2hlY2tib3ggaWYgYWxsIGNoZWNrYm94ZXMgYXJlIGFjdGl2YXRlZC4gT3RoZXJ3aXNlIGRlYWN0aXZhdGUgaXQuXHJcblx0XHRcdFx0X3NldEJ1bGtTZWxlY3Rpb25TdGF0ZSgkdGFibGVCb2R5Q2hlY2tib3hlcy5ub3QoJzpjaGVja2VkJykubGVuZ3RoID09PSAwKTtcclxuXHRcdFx0fSk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8qKlxyXG5cdFx0ICogQWRkIG9yIHJlbW92ZSB0aGUgY2FyZXQgZnJvbSB0aGUgdGFibGUgaGVhZCBjaGVja2JveC5cclxuXHRcdCAqXHJcblx0XHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnRcclxuXHRcdCAqL1xyXG5cdFx0ZnVuY3Rpb24gX29uQnVsa1NlbGVjdGlvbkNoYW5nZShldmVudCkge1xyXG5cdFx0XHRpZiAoJGJ1bGtTZWxlY3Rpb24ucGFyZW50cygnLnNpbmdsZS1jaGVja2JveCcpLmxlbmd0aCA9PT0gMCkge1xyXG5cdFx0XHRcdHJldHVybjsgLy8gRG8gbm90IHByb2NlZWQgd2l0aCB0aGUgZnVuY3Rpb24gaWYgdGhlIHRoZWFkIHNpbmdsZS1jaGVja2JveCBpcyBub3QgcmVhZHkgeWV0LlxyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHRpZiAoJGJ1bGtTZWxlY3Rpb24ucHJvcCgnY2hlY2tlZCcpKSB7XHJcblx0XHRcdFx0X2FkZENhcmV0VG9CdWxrU2VsZWN0aW9uKCk7XHJcblx0XHRcdFx0X2xpc3RlbkZvckNhcmV0Q2xpY2tFdmVudHMoKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHRfcmVtb3ZlQ2FyZXRGcm9tQnVsa1NlbGVjdGlvbihldmVudCk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiAgRXZlbnQgaGFuZGxpbmcgZm9yIHRoZSBvcmlnaW5hbCBkcm9wZG93biBidXR0b24gY2xpY2suXHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9vbkJ1bGtBY3Rpb25Ecm9wZG93blRvZ2dsZUNsaWNrKCkge1xyXG5cdFx0XHRfcmVzZXREcm9wZG93blBvc2l0aW9uKCk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdFx0JHRoaXMub24oJ3NpbmdsZV9jaGVja2JveDpyZWFkeScsIF9vblNpbmdsZUNoZWNrYm94UmVhZHkpO1xyXG5cdFx0XHQkYnVsa1NlbGVjdGlvbi5vbignY2hhbmdlJywgX29uQnVsa1NlbGVjdGlvbkNoYW5nZSk7XHJcblx0XHRcdCRidWxrQWN0aW9uLmZpbmQoJy5kcm9wZG93bi10b2dnbGUnKS5vbignY2xpY2snLCBfb25CdWxrQWN0aW9uRHJvcGRvd25Ub2dnbGVDbGljayk7XHJcblx0XHRcdGRvbmUoKTtcclxuXHRcdH07XHJcblx0XHRyZXR1cm4gbW9kdWxlO1xyXG5cdH0pO1xyXG4iXX0=
