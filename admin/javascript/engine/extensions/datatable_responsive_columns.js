/* --------------------------------------------------------------
 datatable_responsive_columns.js 2016-06-29
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable DataTable Responsive Columns
 *
 * This module will enable the responsive columns functionality which will resize the columns until a minimum
 * width is reach. Afterwards the columns will be hidden and the content will be displayed by through an icon
 * tooltip.
 *
 * ### Options
 *
 * **Initial Visibility Toggle Selector | `data-data_relative_columns-visibility-toggle-selector` | String | Optional**
 *
 * Provide a selector relative to each thead > tr element in order to hide the column on page load and then show it
 * again once the responsive widths have been calculated. The provided selector must point to the biggest column in
 * order to avoid broken displays till the table becomes responsive.
 *
 * @module Admin/Extensions/data_relative_columns
 */
gx.extensions.module('datatable_responsive_columns', [], function(data) {
	
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
		visibilityToggleSelector: '[data-column-name="actions"]'
	};
	
	/**
	 * Final Options
	 *
	 * @type {Object}
	 */
	const options = $.extend(true, {}, defaults, data);
	
	/**
	 * Module Instance
	 *
	 * @type {Object}
	 */
	const module = {};
	
	/**
	 * DataTable Initialization Columns
	 *
	 * @type {Array}
	 */
	let columnDefinitions;
	
	/**
	 * Width Factor Sum
	 *
	 * @type {Number}
	 */
	let widthFactorSum;
	
	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------
	
	/**
	 * Update empty table "colspan" attribute.
	 *
	 * This method will keep the empty table row width in sync with the table width.
	 */
	function _updateEmptyTableColSpan() {
		if ($this.find('.dataTables_empty').length > 0) {
			const colspan = ($this.find('thead:first tr:first .actions').index() + 1)
				- $this.find('thead:first tr:first th.hidden').length;
			$this.find('.dataTables_empty').attr('colspan', colspan);
		}
	}
	
	/**
	 * Add hidden columns content icon to actions cell of a single row. 
	 * 
	 * Call this method only if you are sure there is no icon previously set (runs faster). 
	 * 
	 * @param {jQuery} $tr
	 */
	function _addHiddenColumnsContentIcon($tr) {
		$tr.find('td.actions div:first')
			.prepend(`<i class="fa fa-ellipsis-h meta-icon hidden-columns-content"></i>`);
	}
	
	/**
	 * Generates and sets the tooltip content for the hidden columns content.
	 *
	 * @param {jQuery} $tr The current row selector.
	 */
	function _generateHiddenColumnsContent($tr) {
		let hiddenColumnContentHtml = '';
		
		$tr.find('td.hidden').each((index, td) => {
			hiddenColumnContentHtml += $this.find(`thead:first tr:first th:eq(${$(td).index()})`).text()
				+ ': ' + $(td).text() + '<br/>';
		});
		
		$tr.find('.hidden-columns-content').qtip({
			content: hiddenColumnContentHtml,
			style: {
				classes: 'gx-qtip info'
			},
			hide: {
				fixed: true,
				delay: 300
			}
		});
	}
	
	/**
	 * Hide DataTable Columns
	 *
	 * This method is part of the responsive tables solution.
	 *
	 * @param {jQuery} $targetWrapper Target datatable instance wrapper div.
	 * @param {jQuery} $firstHiddenColumn The first hidden column (first column with the .hidden class).
	 */
	function _hideColumns($targetWrapper, $firstHiddenColumn) {
		const $lastVisibleColumn = ($firstHiddenColumn.length !== 0)
			? $firstHiddenColumn.prev()
			: $this.find('thead:first th.actions').prev();
		
		if ($lastVisibleColumn.hasClass('hidden') || $lastVisibleColumn.index() === 0) {
			return; // First column or already hidden, do not continue.
		}
		
		// Show hidden column content icon.
		if ($this.find('.hidden-columns-content').length === 0) {
			$this.find('tbody tr').each((index, tr) => {
				_addHiddenColumnsContentIcon($(tr));
			});
		}
		
		// Hide the last visible column.
		$this.find('tr').each((index, tr) => {
			$(tr)
				.find(`th:eq(${$lastVisibleColumn.index()}), td:eq(${$lastVisibleColumn.index()})`)
				.addClass('hidden');
			
			// Generate the hidden columns content.
			_generateHiddenColumnsContent($(tr));
		});
		
		_updateEmptyTableColSpan();
		
		// If there are still columns which don't fit within the viewport, hide them.
		if ($targetWrapper.width() < $this.width() && $lastVisibleColumn.index() > 1) {
			_toggleColumnsVisibility();
		}
	}
	
	/**
	 * Show DataTable Columns
	 *
	 * This method is part of the responsive tables solution.
	 *
	 * @param {jQuery} $targetWrapper Target datatable instance wrapper div.
	 * @param {jQuery} $firstHiddenColumn The first hidden column (first column with the .hidden class).
	 */
	function _showColumns($targetWrapper, $firstHiddenColumn) {
		if ($firstHiddenColumn.length === 0) {
			return;
		}
		
		const firstHiddenColumnWidth = parseInt($firstHiddenColumn.css('min-width'));
		let tableMinWidth = 0;
		
		// Calculate the table min width by each column min width.
		$this.find('thead:first tr:first th').each((index, th) => {
			if (!$(th).hasClass('hidden')) {
				tableMinWidth += parseInt($(th).css('min-width'));
			}
		});
		
		// Show the first hidden column.
		if (tableMinWidth + firstHiddenColumnWidth <= $targetWrapper.outerWidth()) {
			$this.find('tr').each((index, tr) => {
				$(tr)
					.find(`th:eq(${$firstHiddenColumn.index()}), td:eq(${$firstHiddenColumn.index()})`)
					.removeClass('hidden');
				
				_generateHiddenColumnsContent($(tr));
			});
			
			_updateEmptyTableColSpan();
			
			// Hide hidden column content icon.
			if ($this.find('thead:first tr:first th.hidden').length === 0) {
				$this.find('.hidden-columns-content').remove();
			}
			
			// If there are still columns which would fit fit within the viewport, show them.
			const newTableMinWidth = tableMinWidth + firstHiddenColumnWidth
				+ parseInt($firstHiddenColumn.next('.hidden').css('min-width'));
			
			if (newTableMinWidth <= $targetWrapper.outerWidth() && $firstHiddenColumn.next('.hidden').length !== 0) {
				_toggleColumnsVisibility();
			}
		}
	}
	
	/**
	 * Toggle column visibility depending the window size.
	 */
	function _toggleColumnsVisibility() {
		const $targetWrapper = $this.parent();
		const $firstHiddenColumn = $this.find('thead:first th.hidden:first');
		
		if ($targetWrapper.width() < $this.width()) {
			_hideColumns($targetWrapper, $firstHiddenColumn);
		} else {
			_showColumns($targetWrapper, $firstHiddenColumn);
		}
	}
	
	/**
	 * Calculate and set the relative column widths.
	 *
	 * The relative width calculation works with a width-factor system where each column preserves a
	 * specific amount of the table width.
	 *
	 * This factor is not defining a percentage, rather only a width-volume. Percentage widths will not
	 * work correctly when the table has fewer columns than the original settings.
	 */
	function _applyRelativeColumnWidths() {
		$this.find('thead:first tr:first th').each(function() {
			if ($(this).css('display') === 'none') {
				return true;
			}
			
			let currentColumnDefinition;
			
			columnDefinitions.forEach((columnDefinition) => {
				if (columnDefinition.name === $(this).data('columnName')) {
					currentColumnDefinition = columnDefinition;
				}
			});
			
			if (currentColumnDefinition && currentColumnDefinition.widthFactor) {
				const index = $(this).index();
				const width = Math.round(currentColumnDefinition.widthFactor / widthFactorSum * 100 * 100) / 100;
				$this.find('thead').each((i, thead) => {
					$(thead).find('tr').each((i, tr) => {
						$(tr).find('th').eq(index).css('width', width + '%');
					});
				});
			}
		});
	}
	
	/**
	 * Applies the column width if the current column width is smaller.
	 */
	function _applyMinimumColumnWidths() {
		$this.find('thead:first tr:first th').each(function(index) {
			if ($(this).css('display') === 'none') {
				return true;
			}
			
			let currentColumnDefinition;
			
			columnDefinitions.forEach((columnDefinition) => {
				if (columnDefinition.name === $(this).data('columnName')) {
					currentColumnDefinition = columnDefinition;
				}
			});
			
			if (!currentColumnDefinition) {
				return true;
			}
			
			const currentWidth = $(this).outerWidth();
			const definitionMinWidth = parseInt(currentColumnDefinition.minWidth);
			
			if (currentWidth < definitionMinWidth) {
				// Force the correct column min-widths for all thead columns.
				$this.find('thead').each((i, thead) => {
					$(thead).find('tr').each((i, tr) => {
						$(tr).find('th').eq(index)
							.outerWidth(definitionMinWidth)
							.css('max-width', definitionMinWidth)
							.css('min-width', definitionMinWidth);
					});
				});
			}
		});
	}
	
	/**
	 * On DataTable Draw Event
	 */
	function _onDataTableDraw() {
		_applyRelativeColumnWidths();
		_applyMinimumColumnWidths();
		
		// Wait until the contents of the table are rendered. DataTables will sometimes fire the draw event
		// even before the td elements are rendered in the browser.
		const interval = setInterval(() => {
			if ($this.find('tbody tr:last td.actions').length === 1) {
				// Hide the tbody cells depending on whether the respective <th> element is hidden.
				$this.find('thead:first tr:first th').each((index, th) => {
					if ($(th).hasClass('hidden')) {
						$this.find('tbody tr').each((i, tr) => {
							$(tr).find(`td:eq(${index})`).addClass('hidden');
						});
					}
				});
				
				// Add the hidden columns icon if needed.
				if ($('thead th.hidden').length) {
					$('tbody tr').each((index, tr) => {
						_addHiddenColumnsContentIcon($(tr));
						_generateHiddenColumnsContent($(tr));
					});
				}
				
				clearInterval(interval);
			}
		}, 1);
	}
	
	/**
	 * On Window Resize Event
	 */
	function _onWindowResize() {
		$this.find('thead.fixed').outerWidth($this.outerWidth());
		_applyRelativeColumnWidths();
		_applyMinimumColumnWidths();
		_toggleColumnsVisibility();
	}
	
	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	
	module.init = function(done) {
		$this.on('init.dt', () => {
			$this.find(options.visibilityToggleSelector).show();
			_updateEmptyTableColSpan();
			
			columnDefinitions = $this.DataTable().init().columns;
			widthFactorSum = 0;
			
			columnDefinitions.forEach((columnDefinition) => {
				widthFactorSum += columnDefinition.widthFactor || 0;
			});
			
			$this.on('draw.dt', _onDataTableDraw);
			$(window).on('resize', _onWindowResize);
			
			_onWindowResize();
		});
		
		$this.find(options.visibilityToggleSelector).hide();
		
		done();
	};
	
	return module;
	
}); 
