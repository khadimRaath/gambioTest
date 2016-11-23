'use strict';

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
gx.extensions.module('datatable_responsive_columns', [], function (data) {

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
		visibilityToggleSelector: '[data-column-name="actions"]'
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

	/**
  * DataTable Initialization Columns
  *
  * @type {Array}
  */
	var columnDefinitions = void 0;

	/**
  * Width Factor Sum
  *
  * @type {Number}
  */
	var widthFactorSum = void 0;

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
			var colspan = $this.find('thead:first tr:first .actions').index() + 1 - $this.find('thead:first tr:first th.hidden').length;
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
		$tr.find('td.actions div:first').prepend('<i class="fa fa-ellipsis-h meta-icon hidden-columns-content"></i>');
	}

	/**
  * Generates and sets the tooltip content for the hidden columns content.
  *
  * @param {jQuery} $tr The current row selector.
  */
	function _generateHiddenColumnsContent($tr) {
		var hiddenColumnContentHtml = '';

		$tr.find('td.hidden').each(function (index, td) {
			hiddenColumnContentHtml += $this.find('thead:first tr:first th:eq(' + $(td).index() + ')').text() + ': ' + $(td).text() + '<br/>';
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
		var $lastVisibleColumn = $firstHiddenColumn.length !== 0 ? $firstHiddenColumn.prev() : $this.find('thead:first th.actions').prev();

		if ($lastVisibleColumn.hasClass('hidden') || $lastVisibleColumn.index() === 0) {
			return; // First column or already hidden, do not continue.
		}

		// Show hidden column content icon.
		if ($this.find('.hidden-columns-content').length === 0) {
			$this.find('tbody tr').each(function (index, tr) {
				_addHiddenColumnsContentIcon($(tr));
			});
		}

		// Hide the last visible column.
		$this.find('tr').each(function (index, tr) {
			$(tr).find('th:eq(' + $lastVisibleColumn.index() + '), td:eq(' + $lastVisibleColumn.index() + ')').addClass('hidden');

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

		var firstHiddenColumnWidth = parseInt($firstHiddenColumn.css('min-width'));
		var tableMinWidth = 0;

		// Calculate the table min width by each column min width.
		$this.find('thead:first tr:first th').each(function (index, th) {
			if (!$(th).hasClass('hidden')) {
				tableMinWidth += parseInt($(th).css('min-width'));
			}
		});

		// Show the first hidden column.
		if (tableMinWidth + firstHiddenColumnWidth <= $targetWrapper.outerWidth()) {
			$this.find('tr').each(function (index, tr) {
				$(tr).find('th:eq(' + $firstHiddenColumn.index() + '), td:eq(' + $firstHiddenColumn.index() + ')').removeClass('hidden');

				_generateHiddenColumnsContent($(tr));
			});

			_updateEmptyTableColSpan();

			// Hide hidden column content icon.
			if ($this.find('thead:first tr:first th.hidden').length === 0) {
				$this.find('.hidden-columns-content').remove();
			}

			// If there are still columns which would fit fit within the viewport, show them.
			var newTableMinWidth = tableMinWidth + firstHiddenColumnWidth + parseInt($firstHiddenColumn.next('.hidden').css('min-width'));

			if (newTableMinWidth <= $targetWrapper.outerWidth() && $firstHiddenColumn.next('.hidden').length !== 0) {
				_toggleColumnsVisibility();
			}
		}
	}

	/**
  * Toggle column visibility depending the window size.
  */
	function _toggleColumnsVisibility() {
		var $targetWrapper = $this.parent();
		var $firstHiddenColumn = $this.find('thead:first th.hidden:first');

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
		$this.find('thead:first tr:first th').each(function () {
			var _this = this;

			if ($(this).css('display') === 'none') {
				return true;
			}

			var currentColumnDefinition = void 0;

			columnDefinitions.forEach(function (columnDefinition) {
				if (columnDefinition.name === $(_this).data('columnName')) {
					currentColumnDefinition = columnDefinition;
				}
			});

			if (currentColumnDefinition && currentColumnDefinition.widthFactor) {
				(function () {
					var index = $(_this).index();
					var width = Math.round(currentColumnDefinition.widthFactor / widthFactorSum * 100 * 100) / 100;
					$this.find('thead').each(function (i, thead) {
						$(thead).find('tr').each(function (i, tr) {
							$(tr).find('th').eq(index).css('width', width + '%');
						});
					});
				})();
			}
		});
	}

	/**
  * Applies the column width if the current column width is smaller.
  */
	function _applyMinimumColumnWidths() {
		$this.find('thead:first tr:first th').each(function (index) {
			var _this2 = this;

			if ($(this).css('display') === 'none') {
				return true;
			}

			var currentColumnDefinition = void 0;

			columnDefinitions.forEach(function (columnDefinition) {
				if (columnDefinition.name === $(_this2).data('columnName')) {
					currentColumnDefinition = columnDefinition;
				}
			});

			if (!currentColumnDefinition) {
				return true;
			}

			var currentWidth = $(this).outerWidth();
			var definitionMinWidth = parseInt(currentColumnDefinition.minWidth);

			if (currentWidth < definitionMinWidth) {
				// Force the correct column min-widths for all thead columns.
				$this.find('thead').each(function (i, thead) {
					$(thead).find('tr').each(function (i, tr) {
						$(tr).find('th').eq(index).outerWidth(definitionMinWidth).css('max-width', definitionMinWidth).css('min-width', definitionMinWidth);
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
		var interval = setInterval(function () {
			if ($this.find('tbody tr:last td.actions').length === 1) {
				// Hide the tbody cells depending on whether the respective <th> element is hidden.
				$this.find('thead:first tr:first th').each(function (index, th) {
					if ($(th).hasClass('hidden')) {
						$this.find('tbody tr').each(function (i, tr) {
							$(tr).find('td:eq(' + index + ')').addClass('hidden');
						});
					}
				});

				// Add the hidden columns icon if needed.
				if ($('thead th.hidden').length) {
					$('tbody tr').each(function (index, tr) {
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

	module.init = function (done) {
		$this.on('init.dt', function () {
			$this.find(options.visibilityToggleSelector).show();
			_updateEmptyTableColSpan();

			columnDefinitions = $this.DataTable().init().columns;
			widthFactorSum = 0;

			columnDefinitions.forEach(function (columnDefinition) {
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
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9yZXNwb25zaXZlX2NvbHVtbnMuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwidmlzaWJpbGl0eVRvZ2dsZVNlbGVjdG9yIiwib3B0aW9ucyIsImV4dGVuZCIsImNvbHVtbkRlZmluaXRpb25zIiwid2lkdGhGYWN0b3JTdW0iLCJfdXBkYXRlRW1wdHlUYWJsZUNvbFNwYW4iLCJmaW5kIiwibGVuZ3RoIiwiY29sc3BhbiIsImluZGV4IiwiYXR0ciIsIl9hZGRIaWRkZW5Db2x1bW5zQ29udGVudEljb24iLCIkdHIiLCJwcmVwZW5kIiwiX2dlbmVyYXRlSGlkZGVuQ29sdW1uc0NvbnRlbnQiLCJoaWRkZW5Db2x1bW5Db250ZW50SHRtbCIsImVhY2giLCJ0ZCIsInRleHQiLCJxdGlwIiwiY29udGVudCIsInN0eWxlIiwiY2xhc3NlcyIsImhpZGUiLCJmaXhlZCIsImRlbGF5IiwiX2hpZGVDb2x1bW5zIiwiJHRhcmdldFdyYXBwZXIiLCIkZmlyc3RIaWRkZW5Db2x1bW4iLCIkbGFzdFZpc2libGVDb2x1bW4iLCJwcmV2IiwiaGFzQ2xhc3MiLCJ0ciIsImFkZENsYXNzIiwid2lkdGgiLCJfdG9nZ2xlQ29sdW1uc1Zpc2liaWxpdHkiLCJfc2hvd0NvbHVtbnMiLCJmaXJzdEhpZGRlbkNvbHVtbldpZHRoIiwicGFyc2VJbnQiLCJjc3MiLCJ0YWJsZU1pbldpZHRoIiwidGgiLCJvdXRlcldpZHRoIiwicmVtb3ZlQ2xhc3MiLCJyZW1vdmUiLCJuZXdUYWJsZU1pbldpZHRoIiwibmV4dCIsInBhcmVudCIsIl9hcHBseVJlbGF0aXZlQ29sdW1uV2lkdGhzIiwiY3VycmVudENvbHVtbkRlZmluaXRpb24iLCJmb3JFYWNoIiwiY29sdW1uRGVmaW5pdGlvbiIsIm5hbWUiLCJ3aWR0aEZhY3RvciIsIk1hdGgiLCJyb3VuZCIsImkiLCJ0aGVhZCIsImVxIiwiX2FwcGx5TWluaW11bUNvbHVtbldpZHRocyIsImN1cnJlbnRXaWR0aCIsImRlZmluaXRpb25NaW5XaWR0aCIsIm1pbldpZHRoIiwiX29uRGF0YVRhYmxlRHJhdyIsImludGVydmFsIiwic2V0SW50ZXJ2YWwiLCJjbGVhckludGVydmFsIiwiX29uV2luZG93UmVzaXplIiwiaW5pdCIsImRvbmUiLCJvbiIsInNob3ciLCJEYXRhVGFibGUiLCJjb2x1bW5zIiwid2luZG93Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBaUJBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FBcUIsOEJBQXJCLEVBQXFELEVBQXJELEVBQXlELFVBQVNDLElBQVQsRUFBZTs7QUFFdkU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNQyxXQUFXO0FBQ2hCQyw0QkFBMEI7QUFEVixFQUFqQjs7QUFJQTs7Ozs7QUFLQSxLQUFNQyxVQUFVSCxFQUFFSSxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSCxJQUE3QixDQUFoQjs7QUFFQTs7Ozs7QUFLQSxLQUFNRCxTQUFTLEVBQWY7O0FBRUE7Ozs7O0FBS0EsS0FBSVEsMEJBQUo7O0FBRUE7Ozs7O0FBS0EsS0FBSUMsdUJBQUo7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNDLHdCQUFULEdBQW9DO0FBQ25DLE1BQUlSLE1BQU1TLElBQU4sQ0FBVyxtQkFBWCxFQUFnQ0MsTUFBaEMsR0FBeUMsQ0FBN0MsRUFBZ0Q7QUFDL0MsT0FBTUMsVUFBV1gsTUFBTVMsSUFBTixDQUFXLCtCQUFYLEVBQTRDRyxLQUE1QyxLQUFzRCxDQUF2RCxHQUNiWixNQUFNUyxJQUFOLENBQVcsZ0NBQVgsRUFBNkNDLE1BRGhEO0FBRUFWLFNBQU1TLElBQU4sQ0FBVyxtQkFBWCxFQUFnQ0ksSUFBaEMsQ0FBcUMsU0FBckMsRUFBZ0RGLE9BQWhE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QUFPeUUsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QnhFLFFBQU15RSxFQUFOLENBQVMsU0FBVCxFQUFvQixZQUFNO0FBQ3pCekUsU0FBTVMsSUFBTixDQUFXTCxRQUFRRCx3QkFBbkIsRUFBNkN1RSxJQUE3QztBQUNBbEU7O0FBRUFGLHVCQUFvQk4sTUFBTTJFLFNBQU4sR0FBa0JKLElBQWxCLEdBQXlCSyxPQUE3QztBQUNBckUsb0JBQWlCLENBQWpCOztBQUVBRCxxQkFBa0IrQyxPQUFsQixDQUEwQixVQUFDQyxnQkFBRCxFQUFzQjtBQUMvQy9DLHNCQUFrQitDLGlCQUFpQkUsV0FBakIsSUFBZ0MsQ0FBbEQ7QUFDQSxJQUZEOztBQUlBeEQsU0FBTXlFLEVBQU4sQ0FBUyxTQUFULEVBQW9CUCxnQkFBcEI7QUFDQWpFLEtBQUU0RSxNQUFGLEVBQVVKLEVBQVYsQ0FBYSxRQUFiLEVBQXVCSCxlQUF2Qjs7QUFFQUE7QUFDQSxHQWZEOztBQWlCQXRFLFFBQU1TLElBQU4sQ0FBV0wsUUFBUUQsd0JBQW5CLEVBQTZDdUIsSUFBN0M7O0FBRUE4QztBQUNBLEVBckJEOztBQXVCQSxRQUFPMUUsTUFBUDtBQUVBLENBcldEIiwiZmlsZSI6ImRhdGF0YWJsZV9yZXNwb25zaXZlX2NvbHVtbnMuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gZGF0YXRhYmxlX3Jlc3BvbnNpdmVfY29sdW1ucy5qcyAyMDE2LTA2LTI5XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqICMjIEVuYWJsZSBEYXRhVGFibGUgUmVzcG9uc2l2ZSBDb2x1bW5zXHJcbiAqXHJcbiAqIFRoaXMgbW9kdWxlIHdpbGwgZW5hYmxlIHRoZSByZXNwb25zaXZlIGNvbHVtbnMgZnVuY3Rpb25hbGl0eSB3aGljaCB3aWxsIHJlc2l6ZSB0aGUgY29sdW1ucyB1bnRpbCBhIG1pbmltdW1cclxuICogd2lkdGggaXMgcmVhY2guIEFmdGVyd2FyZHMgdGhlIGNvbHVtbnMgd2lsbCBiZSBoaWRkZW4gYW5kIHRoZSBjb250ZW50IHdpbGwgYmUgZGlzcGxheWVkIGJ5IHRocm91Z2ggYW4gaWNvblxyXG4gKiB0b29sdGlwLlxyXG4gKlxyXG4gKiAjIyMgT3B0aW9uc1xyXG4gKlxyXG4gKiAqKkluaXRpYWwgVmlzaWJpbGl0eSBUb2dnbGUgU2VsZWN0b3IgfCBgZGF0YS1kYXRhX3JlbGF0aXZlX2NvbHVtbnMtdmlzaWJpbGl0eS10b2dnbGUtc2VsZWN0b3JgIHwgU3RyaW5nIHwgT3B0aW9uYWwqKlxyXG4gKlxyXG4gKiBQcm92aWRlIGEgc2VsZWN0b3IgcmVsYXRpdmUgdG8gZWFjaCB0aGVhZCA+IHRyIGVsZW1lbnQgaW4gb3JkZXIgdG8gaGlkZSB0aGUgY29sdW1uIG9uIHBhZ2UgbG9hZCBhbmQgdGhlbiBzaG93IGl0XHJcbiAqIGFnYWluIG9uY2UgdGhlIHJlc3BvbnNpdmUgd2lkdGhzIGhhdmUgYmVlbiBjYWxjdWxhdGVkLiBUaGUgcHJvdmlkZWQgc2VsZWN0b3IgbXVzdCBwb2ludCB0byB0aGUgYmlnZ2VzdCBjb2x1bW4gaW5cclxuICogb3JkZXIgdG8gYXZvaWQgYnJva2VuIGRpc3BsYXlzIHRpbGwgdGhlIHRhYmxlIGJlY29tZXMgcmVzcG9uc2l2ZS5cclxuICpcclxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2RhdGFfcmVsYXRpdmVfY29sdW1uc1xyXG4gKi9cclxuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoJ2RhdGF0YWJsZV9yZXNwb25zaXZlX2NvbHVtbnMnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIERlZmF1bHQgT3B0aW9uc1xyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBkZWZhdWx0cyA9IHtcclxuXHRcdHZpc2liaWxpdHlUb2dnbGVTZWxlY3RvcjogJ1tkYXRhLWNvbHVtbi1uYW1lPVwiYWN0aW9uc1wiXSdcclxuXHR9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEZpbmFsIE9wdGlvbnNcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtPYmplY3R9XHJcblx0ICovXHJcblx0Y29uc3Qgb3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIERhdGFUYWJsZSBJbml0aWFsaXphdGlvbiBDb2x1bW5zXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7QXJyYXl9XHJcblx0ICovXHJcblx0bGV0IGNvbHVtbkRlZmluaXRpb25zO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFdpZHRoIEZhY3RvciBTdW1cclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtOdW1iZXJ9XHJcblx0ICovXHJcblx0bGV0IHdpZHRoRmFjdG9yU3VtO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFVwZGF0ZSBlbXB0eSB0YWJsZSBcImNvbHNwYW5cIiBhdHRyaWJ1dGUuXHJcblx0ICpcclxuXHQgKiBUaGlzIG1ldGhvZCB3aWxsIGtlZXAgdGhlIGVtcHR5IHRhYmxlIHJvdyB3aWR0aCBpbiBzeW5jIHdpdGggdGhlIHRhYmxlIHdpZHRoLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF91cGRhdGVFbXB0eVRhYmxlQ29sU3BhbigpIHtcclxuXHRcdGlmICgkdGhpcy5maW5kKCcuZGF0YVRhYmxlc19lbXB0eScpLmxlbmd0aCA+IDApIHtcclxuXHRcdFx0Y29uc3QgY29sc3BhbiA9ICgkdGhpcy5maW5kKCd0aGVhZDpmaXJzdCB0cjpmaXJzdCAuYWN0aW9ucycpLmluZGV4KCkgKyAxKVxyXG5cdFx0XHRcdC0gJHRoaXMuZmluZCgndGhlYWQ6Zmlyc3QgdHI6Zmlyc3QgdGguaGlkZGVuJykubGVuZ3RoO1xyXG5cdFx0XHQkdGhpcy5maW5kKCcuZGF0YVRhYmxlc19lbXB0eScpLmF0dHIoJ2NvbHNwYW4nLCBjb2xzcGFuKTtcclxuXHRcdH1cclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogQWRkIGhpZGRlbiBjb2x1bW5zIGNvbnRlbnQgaWNvbiB0byBhY3Rpb25zIGNlbGwgb2YgYSBzaW5nbGUgcm93LiBcclxuXHQgKiBcclxuXHQgKiBDYWxsIHRoaXMgbWV0aG9kIG9ubHkgaWYgeW91IGFyZSBzdXJlIHRoZXJlIGlzIG5vIGljb24gcHJldmlvdXNseSBzZXQgKHJ1bnMgZmFzdGVyKS4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICR0clxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9hZGRIaWRkZW5Db2x1bW5zQ29udGVudEljb24oJHRyKSB7XHJcblx0XHQkdHIuZmluZCgndGQuYWN0aW9ucyBkaXY6Zmlyc3QnKVxyXG5cdFx0XHQucHJlcGVuZChgPGkgY2xhc3M9XCJmYSBmYS1lbGxpcHNpcy1oIG1ldGEtaWNvbiBoaWRkZW4tY29sdW1ucy1jb250ZW50XCI+PC9pPmApO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBHZW5lcmF0ZXMgYW5kIHNldHMgdGhlIHRvb2x0aXAgY29udGVudCBmb3IgdGhlIGhpZGRlbiBjb2x1bW5zIGNvbnRlbnQuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJHRyIFRoZSBjdXJyZW50IHJvdyBzZWxlY3Rvci5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfZ2VuZXJhdGVIaWRkZW5Db2x1bW5zQ29udGVudCgkdHIpIHtcclxuXHRcdGxldCBoaWRkZW5Db2x1bW5Db250ZW50SHRtbCA9ICcnO1xyXG5cdFx0XHJcblx0XHQkdHIuZmluZCgndGQuaGlkZGVuJykuZWFjaCgoaW5kZXgsIHRkKSA9PiB7XHJcblx0XHRcdGhpZGRlbkNvbHVtbkNvbnRlbnRIdG1sICs9ICR0aGlzLmZpbmQoYHRoZWFkOmZpcnN0IHRyOmZpcnN0IHRoOmVxKCR7JCh0ZCkuaW5kZXgoKX0pYCkudGV4dCgpXHJcblx0XHRcdFx0KyAnOiAnICsgJCh0ZCkudGV4dCgpICsgJzxici8+JztcclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHQkdHIuZmluZCgnLmhpZGRlbi1jb2x1bW5zLWNvbnRlbnQnKS5xdGlwKHtcclxuXHRcdFx0Y29udGVudDogaGlkZGVuQ29sdW1uQ29udGVudEh0bWwsXHJcblx0XHRcdHN0eWxlOiB7XHJcblx0XHRcdFx0Y2xhc3NlczogJ2d4LXF0aXAgaW5mbydcclxuXHRcdFx0fSxcclxuXHRcdFx0aGlkZToge1xyXG5cdFx0XHRcdGZpeGVkOiB0cnVlLFxyXG5cdFx0XHRcdGRlbGF5OiAzMDBcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEhpZGUgRGF0YVRhYmxlIENvbHVtbnNcclxuXHQgKlxyXG5cdCAqIFRoaXMgbWV0aG9kIGlzIHBhcnQgb2YgdGhlIHJlc3BvbnNpdmUgdGFibGVzIHNvbHV0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICR0YXJnZXRXcmFwcGVyIFRhcmdldCBkYXRhdGFibGUgaW5zdGFuY2Ugd3JhcHBlciBkaXYuXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICRmaXJzdEhpZGRlbkNvbHVtbiBUaGUgZmlyc3QgaGlkZGVuIGNvbHVtbiAoZmlyc3QgY29sdW1uIHdpdGggdGhlIC5oaWRkZW4gY2xhc3MpLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9oaWRlQ29sdW1ucygkdGFyZ2V0V3JhcHBlciwgJGZpcnN0SGlkZGVuQ29sdW1uKSB7XHJcblx0XHRjb25zdCAkbGFzdFZpc2libGVDb2x1bW4gPSAoJGZpcnN0SGlkZGVuQ29sdW1uLmxlbmd0aCAhPT0gMClcclxuXHRcdFx0PyAkZmlyc3RIaWRkZW5Db2x1bW4ucHJldigpXHJcblx0XHRcdDogJHRoaXMuZmluZCgndGhlYWQ6Zmlyc3QgdGguYWN0aW9ucycpLnByZXYoKTtcclxuXHRcdFxyXG5cdFx0aWYgKCRsYXN0VmlzaWJsZUNvbHVtbi5oYXNDbGFzcygnaGlkZGVuJykgfHwgJGxhc3RWaXNpYmxlQ29sdW1uLmluZGV4KCkgPT09IDApIHtcclxuXHRcdFx0cmV0dXJuOyAvLyBGaXJzdCBjb2x1bW4gb3IgYWxyZWFkeSBoaWRkZW4sIGRvIG5vdCBjb250aW51ZS5cclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0Ly8gU2hvdyBoaWRkZW4gY29sdW1uIGNvbnRlbnQgaWNvbi5cclxuXHRcdGlmICgkdGhpcy5maW5kKCcuaGlkZGVuLWNvbHVtbnMtY29udGVudCcpLmxlbmd0aCA9PT0gMCkge1xyXG5cdFx0XHQkdGhpcy5maW5kKCd0Ym9keSB0cicpLmVhY2goKGluZGV4LCB0cikgPT4ge1xyXG5cdFx0XHRcdF9hZGRIaWRkZW5Db2x1bW5zQ29udGVudEljb24oJCh0cikpO1xyXG5cdFx0XHR9KTtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0Ly8gSGlkZSB0aGUgbGFzdCB2aXNpYmxlIGNvbHVtbi5cclxuXHRcdCR0aGlzLmZpbmQoJ3RyJykuZWFjaCgoaW5kZXgsIHRyKSA9PiB7XHJcblx0XHRcdCQodHIpXHJcblx0XHRcdFx0LmZpbmQoYHRoOmVxKCR7JGxhc3RWaXNpYmxlQ29sdW1uLmluZGV4KCl9KSwgdGQ6ZXEoJHskbGFzdFZpc2libGVDb2x1bW4uaW5kZXgoKX0pYClcclxuXHRcdFx0XHQuYWRkQ2xhc3MoJ2hpZGRlbicpO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gR2VuZXJhdGUgdGhlIGhpZGRlbiBjb2x1bW5zIGNvbnRlbnQuXHJcblx0XHRcdF9nZW5lcmF0ZUhpZGRlbkNvbHVtbnNDb250ZW50KCQodHIpKTtcclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHRfdXBkYXRlRW1wdHlUYWJsZUNvbFNwYW4oKTtcclxuXHRcdFxyXG5cdFx0Ly8gSWYgdGhlcmUgYXJlIHN0aWxsIGNvbHVtbnMgd2hpY2ggZG9uJ3QgZml0IHdpdGhpbiB0aGUgdmlld3BvcnQsIGhpZGUgdGhlbS5cclxuXHRcdGlmICgkdGFyZ2V0V3JhcHBlci53aWR0aCgpIDwgJHRoaXMud2lkdGgoKSAmJiAkbGFzdFZpc2libGVDb2x1bW4uaW5kZXgoKSA+IDEpIHtcclxuXHRcdFx0X3RvZ2dsZUNvbHVtbnNWaXNpYmlsaXR5KCk7XHJcblx0XHR9XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFNob3cgRGF0YVRhYmxlIENvbHVtbnNcclxuXHQgKlxyXG5cdCAqIFRoaXMgbWV0aG9kIGlzIHBhcnQgb2YgdGhlIHJlc3BvbnNpdmUgdGFibGVzIHNvbHV0aW9uLlxyXG5cdCAqXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICR0YXJnZXRXcmFwcGVyIFRhcmdldCBkYXRhdGFibGUgaW5zdGFuY2Ugd3JhcHBlciBkaXYuXHJcblx0ICogQHBhcmFtIHtqUXVlcnl9ICRmaXJzdEhpZGRlbkNvbHVtbiBUaGUgZmlyc3QgaGlkZGVuIGNvbHVtbiAoZmlyc3QgY29sdW1uIHdpdGggdGhlIC5oaWRkZW4gY2xhc3MpLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9zaG93Q29sdW1ucygkdGFyZ2V0V3JhcHBlciwgJGZpcnN0SGlkZGVuQ29sdW1uKSB7XHJcblx0XHRpZiAoJGZpcnN0SGlkZGVuQ29sdW1uLmxlbmd0aCA9PT0gMCkge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdGNvbnN0IGZpcnN0SGlkZGVuQ29sdW1uV2lkdGggPSBwYXJzZUludCgkZmlyc3RIaWRkZW5Db2x1bW4uY3NzKCdtaW4td2lkdGgnKSk7XHJcblx0XHRsZXQgdGFibGVNaW5XaWR0aCA9IDA7XHJcblx0XHRcclxuXHRcdC8vIENhbGN1bGF0ZSB0aGUgdGFibGUgbWluIHdpZHRoIGJ5IGVhY2ggY29sdW1uIG1pbiB3aWR0aC5cclxuXHRcdCR0aGlzLmZpbmQoJ3RoZWFkOmZpcnN0IHRyOmZpcnN0IHRoJykuZWFjaCgoaW5kZXgsIHRoKSA9PiB7XHJcblx0XHRcdGlmICghJCh0aCkuaGFzQ2xhc3MoJ2hpZGRlbicpKSB7XHJcblx0XHRcdFx0dGFibGVNaW5XaWR0aCArPSBwYXJzZUludCgkKHRoKS5jc3MoJ21pbi13aWR0aCcpKTtcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblx0XHRcclxuXHRcdC8vIFNob3cgdGhlIGZpcnN0IGhpZGRlbiBjb2x1bW4uXHJcblx0XHRpZiAodGFibGVNaW5XaWR0aCArIGZpcnN0SGlkZGVuQ29sdW1uV2lkdGggPD0gJHRhcmdldFdyYXBwZXIub3V0ZXJXaWR0aCgpKSB7XHJcblx0XHRcdCR0aGlzLmZpbmQoJ3RyJykuZWFjaCgoaW5kZXgsIHRyKSA9PiB7XHJcblx0XHRcdFx0JCh0cilcclxuXHRcdFx0XHRcdC5maW5kKGB0aDplcSgkeyRmaXJzdEhpZGRlbkNvbHVtbi5pbmRleCgpfSksIHRkOmVxKCR7JGZpcnN0SGlkZGVuQ29sdW1uLmluZGV4KCl9KWApXHJcblx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdF9nZW5lcmF0ZUhpZGRlbkNvbHVtbnNDb250ZW50KCQodHIpKTtcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHRfdXBkYXRlRW1wdHlUYWJsZUNvbFNwYW4oKTtcclxuXHRcdFx0XHJcblx0XHRcdC8vIEhpZGUgaGlkZGVuIGNvbHVtbiBjb250ZW50IGljb24uXHJcblx0XHRcdGlmICgkdGhpcy5maW5kKCd0aGVhZDpmaXJzdCB0cjpmaXJzdCB0aC5oaWRkZW4nKS5sZW5ndGggPT09IDApIHtcclxuXHRcdFx0XHQkdGhpcy5maW5kKCcuaGlkZGVuLWNvbHVtbnMtY29udGVudCcpLnJlbW92ZSgpO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQvLyBJZiB0aGVyZSBhcmUgc3RpbGwgY29sdW1ucyB3aGljaCB3b3VsZCBmaXQgZml0IHdpdGhpbiB0aGUgdmlld3BvcnQsIHNob3cgdGhlbS5cclxuXHRcdFx0Y29uc3QgbmV3VGFibGVNaW5XaWR0aCA9IHRhYmxlTWluV2lkdGggKyBmaXJzdEhpZGRlbkNvbHVtbldpZHRoXHJcblx0XHRcdFx0KyBwYXJzZUludCgkZmlyc3RIaWRkZW5Db2x1bW4ubmV4dCgnLmhpZGRlbicpLmNzcygnbWluLXdpZHRoJykpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKG5ld1RhYmxlTWluV2lkdGggPD0gJHRhcmdldFdyYXBwZXIub3V0ZXJXaWR0aCgpICYmICRmaXJzdEhpZGRlbkNvbHVtbi5uZXh0KCcuaGlkZGVuJykubGVuZ3RoICE9PSAwKSB7XHJcblx0XHRcdFx0X3RvZ2dsZUNvbHVtbnNWaXNpYmlsaXR5KCk7XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogVG9nZ2xlIGNvbHVtbiB2aXNpYmlsaXR5IGRlcGVuZGluZyB0aGUgd2luZG93IHNpemUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX3RvZ2dsZUNvbHVtbnNWaXNpYmlsaXR5KCkge1xyXG5cdFx0Y29uc3QgJHRhcmdldFdyYXBwZXIgPSAkdGhpcy5wYXJlbnQoKTtcclxuXHRcdGNvbnN0ICRmaXJzdEhpZGRlbkNvbHVtbiA9ICR0aGlzLmZpbmQoJ3RoZWFkOmZpcnN0IHRoLmhpZGRlbjpmaXJzdCcpO1xyXG5cdFx0XHJcblx0XHRpZiAoJHRhcmdldFdyYXBwZXIud2lkdGgoKSA8ICR0aGlzLndpZHRoKCkpIHtcclxuXHRcdFx0X2hpZGVDb2x1bW5zKCR0YXJnZXRXcmFwcGVyLCAkZmlyc3RIaWRkZW5Db2x1bW4pO1xyXG5cdFx0fSBlbHNlIHtcclxuXHRcdFx0X3Nob3dDb2x1bW5zKCR0YXJnZXRXcmFwcGVyLCAkZmlyc3RIaWRkZW5Db2x1bW4pO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBDYWxjdWxhdGUgYW5kIHNldCB0aGUgcmVsYXRpdmUgY29sdW1uIHdpZHRocy5cclxuXHQgKlxyXG5cdCAqIFRoZSByZWxhdGl2ZSB3aWR0aCBjYWxjdWxhdGlvbiB3b3JrcyB3aXRoIGEgd2lkdGgtZmFjdG9yIHN5c3RlbSB3aGVyZSBlYWNoIGNvbHVtbiBwcmVzZXJ2ZXMgYVxyXG5cdCAqIHNwZWNpZmljIGFtb3VudCBvZiB0aGUgdGFibGUgd2lkdGguXHJcblx0ICpcclxuXHQgKiBUaGlzIGZhY3RvciBpcyBub3QgZGVmaW5pbmcgYSBwZXJjZW50YWdlLCByYXRoZXIgb25seSBhIHdpZHRoLXZvbHVtZS4gUGVyY2VudGFnZSB3aWR0aHMgd2lsbCBub3RcclxuXHQgKiB3b3JrIGNvcnJlY3RseSB3aGVuIHRoZSB0YWJsZSBoYXMgZmV3ZXIgY29sdW1ucyB0aGFuIHRoZSBvcmlnaW5hbCBzZXR0aW5ncy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfYXBwbHlSZWxhdGl2ZUNvbHVtbldpZHRocygpIHtcclxuXHRcdCR0aGlzLmZpbmQoJ3RoZWFkOmZpcnN0IHRyOmZpcnN0IHRoJykuZWFjaChmdW5jdGlvbigpIHtcclxuXHRcdFx0aWYgKCQodGhpcykuY3NzKCdkaXNwbGF5JykgPT09ICdub25lJykge1xyXG5cdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHRsZXQgY3VycmVudENvbHVtbkRlZmluaXRpb247XHJcblx0XHRcdFxyXG5cdFx0XHRjb2x1bW5EZWZpbml0aW9ucy5mb3JFYWNoKChjb2x1bW5EZWZpbml0aW9uKSA9PiB7XHJcblx0XHRcdFx0aWYgKGNvbHVtbkRlZmluaXRpb24ubmFtZSA9PT0gJCh0aGlzKS5kYXRhKCdjb2x1bW5OYW1lJykpIHtcclxuXHRcdFx0XHRcdGN1cnJlbnRDb2x1bW5EZWZpbml0aW9uID0gY29sdW1uRGVmaW5pdGlvbjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKGN1cnJlbnRDb2x1bW5EZWZpbml0aW9uICYmIGN1cnJlbnRDb2x1bW5EZWZpbml0aW9uLndpZHRoRmFjdG9yKSB7XHJcblx0XHRcdFx0Y29uc3QgaW5kZXggPSAkKHRoaXMpLmluZGV4KCk7XHJcblx0XHRcdFx0Y29uc3Qgd2lkdGggPSBNYXRoLnJvdW5kKGN1cnJlbnRDb2x1bW5EZWZpbml0aW9uLndpZHRoRmFjdG9yIC8gd2lkdGhGYWN0b3JTdW0gKiAxMDAgKiAxMDApIC8gMTAwO1xyXG5cdFx0XHRcdCR0aGlzLmZpbmQoJ3RoZWFkJykuZWFjaCgoaSwgdGhlYWQpID0+IHtcclxuXHRcdFx0XHRcdCQodGhlYWQpLmZpbmQoJ3RyJykuZWFjaCgoaSwgdHIpID0+IHtcclxuXHRcdFx0XHRcdFx0JCh0cikuZmluZCgndGgnKS5lcShpbmRleCkuY3NzKCd3aWR0aCcsIHdpZHRoICsgJyUnKTtcclxuXHRcdFx0XHRcdH0pO1xyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogQXBwbGllcyB0aGUgY29sdW1uIHdpZHRoIGlmIHRoZSBjdXJyZW50IGNvbHVtbiB3aWR0aCBpcyBzbWFsbGVyLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9hcHBseU1pbmltdW1Db2x1bW5XaWR0aHMoKSB7XHJcblx0XHQkdGhpcy5maW5kKCd0aGVhZDpmaXJzdCB0cjpmaXJzdCB0aCcpLmVhY2goZnVuY3Rpb24oaW5kZXgpIHtcclxuXHRcdFx0aWYgKCQodGhpcykuY3NzKCdkaXNwbGF5JykgPT09ICdub25lJykge1xyXG5cdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHRsZXQgY3VycmVudENvbHVtbkRlZmluaXRpb247XHJcblx0XHRcdFxyXG5cdFx0XHRjb2x1bW5EZWZpbml0aW9ucy5mb3JFYWNoKChjb2x1bW5EZWZpbml0aW9uKSA9PiB7XHJcblx0XHRcdFx0aWYgKGNvbHVtbkRlZmluaXRpb24ubmFtZSA9PT0gJCh0aGlzKS5kYXRhKCdjb2x1bW5OYW1lJykpIHtcclxuXHRcdFx0XHRcdGN1cnJlbnRDb2x1bW5EZWZpbml0aW9uID0gY29sdW1uRGVmaW5pdGlvbjtcclxuXHRcdFx0XHR9XHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKCFjdXJyZW50Q29sdW1uRGVmaW5pdGlvbikge1xyXG5cdFx0XHRcdHJldHVybiB0cnVlO1xyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHRjb25zdCBjdXJyZW50V2lkdGggPSAkKHRoaXMpLm91dGVyV2lkdGgoKTtcclxuXHRcdFx0Y29uc3QgZGVmaW5pdGlvbk1pbldpZHRoID0gcGFyc2VJbnQoY3VycmVudENvbHVtbkRlZmluaXRpb24ubWluV2lkdGgpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKGN1cnJlbnRXaWR0aCA8IGRlZmluaXRpb25NaW5XaWR0aCkge1xyXG5cdFx0XHRcdC8vIEZvcmNlIHRoZSBjb3JyZWN0IGNvbHVtbiBtaW4td2lkdGhzIGZvciBhbGwgdGhlYWQgY29sdW1ucy5cclxuXHRcdFx0XHQkdGhpcy5maW5kKCd0aGVhZCcpLmVhY2goKGksIHRoZWFkKSA9PiB7XHJcblx0XHRcdFx0XHQkKHRoZWFkKS5maW5kKCd0cicpLmVhY2goKGksIHRyKSA9PiB7XHJcblx0XHRcdFx0XHRcdCQodHIpLmZpbmQoJ3RoJykuZXEoaW5kZXgpXHJcblx0XHRcdFx0XHRcdFx0Lm91dGVyV2lkdGgoZGVmaW5pdGlvbk1pbldpZHRoKVxyXG5cdFx0XHRcdFx0XHRcdC5jc3MoJ21heC13aWR0aCcsIGRlZmluaXRpb25NaW5XaWR0aClcclxuXHRcdFx0XHRcdFx0XHQuY3NzKCdtaW4td2lkdGgnLCBkZWZpbml0aW9uTWluV2lkdGgpO1xyXG5cdFx0XHRcdFx0fSk7XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdH1cclxuXHRcdH0pO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBPbiBEYXRhVGFibGUgRHJhdyBFdmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbkRhdGFUYWJsZURyYXcoKSB7XHJcblx0XHRfYXBwbHlSZWxhdGl2ZUNvbHVtbldpZHRocygpO1xyXG5cdFx0X2FwcGx5TWluaW11bUNvbHVtbldpZHRocygpO1xyXG5cdFx0XHJcblx0XHQvLyBXYWl0IHVudGlsIHRoZSBjb250ZW50cyBvZiB0aGUgdGFibGUgYXJlIHJlbmRlcmVkLiBEYXRhVGFibGVzIHdpbGwgc29tZXRpbWVzIGZpcmUgdGhlIGRyYXcgZXZlbnRcclxuXHRcdC8vIGV2ZW4gYmVmb3JlIHRoZSB0ZCBlbGVtZW50cyBhcmUgcmVuZGVyZWQgaW4gdGhlIGJyb3dzZXIuXHJcblx0XHRjb25zdCBpbnRlcnZhbCA9IHNldEludGVydmFsKCgpID0+IHtcclxuXHRcdFx0aWYgKCR0aGlzLmZpbmQoJ3Rib2R5IHRyOmxhc3QgdGQuYWN0aW9ucycpLmxlbmd0aCA9PT0gMSkge1xyXG5cdFx0XHRcdC8vIEhpZGUgdGhlIHRib2R5IGNlbGxzIGRlcGVuZGluZyBvbiB3aGV0aGVyIHRoZSByZXNwZWN0aXZlIDx0aD4gZWxlbWVudCBpcyBoaWRkZW4uXHJcblx0XHRcdFx0JHRoaXMuZmluZCgndGhlYWQ6Zmlyc3QgdHI6Zmlyc3QgdGgnKS5lYWNoKChpbmRleCwgdGgpID0+IHtcclxuXHRcdFx0XHRcdGlmICgkKHRoKS5oYXNDbGFzcygnaGlkZGVuJykpIHtcclxuXHRcdFx0XHRcdFx0JHRoaXMuZmluZCgndGJvZHkgdHInKS5lYWNoKChpLCB0cikgPT4ge1xyXG5cdFx0XHRcdFx0XHRcdCQodHIpLmZpbmQoYHRkOmVxKCR7aW5kZXh9KWApLmFkZENsYXNzKCdoaWRkZW4nKTtcclxuXHRcdFx0XHRcdFx0fSk7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fSk7XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0Ly8gQWRkIHRoZSBoaWRkZW4gY29sdW1ucyBpY29uIGlmIG5lZWRlZC5cclxuXHRcdFx0XHRpZiAoJCgndGhlYWQgdGguaGlkZGVuJykubGVuZ3RoKSB7XHJcblx0XHRcdFx0XHQkKCd0Ym9keSB0cicpLmVhY2goKGluZGV4LCB0cikgPT4ge1xyXG5cdFx0XHRcdFx0XHRfYWRkSGlkZGVuQ29sdW1uc0NvbnRlbnRJY29uKCQodHIpKTtcclxuXHRcdFx0XHRcdFx0X2dlbmVyYXRlSGlkZGVuQ29sdW1uc0NvbnRlbnQoJCh0cikpO1xyXG5cdFx0XHRcdFx0fSk7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGNsZWFySW50ZXJ2YWwoaW50ZXJ2YWwpO1xyXG5cdFx0XHR9XHJcblx0XHR9LCAxKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gV2luZG93IFJlc2l6ZSBFdmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbldpbmRvd1Jlc2l6ZSgpIHtcclxuXHRcdCR0aGlzLmZpbmQoJ3RoZWFkLmZpeGVkJykub3V0ZXJXaWR0aCgkdGhpcy5vdXRlcldpZHRoKCkpO1xyXG5cdFx0X2FwcGx5UmVsYXRpdmVDb2x1bW5XaWR0aHMoKTtcclxuXHRcdF9hcHBseU1pbmltdW1Db2x1bW5XaWR0aHMoKTtcclxuXHRcdF90b2dnbGVDb2x1bW5zVmlzaWJpbGl0eSgpO1xyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXMub24oJ2luaXQuZHQnLCAoKSA9PiB7XHJcblx0XHRcdCR0aGlzLmZpbmQob3B0aW9ucy52aXNpYmlsaXR5VG9nZ2xlU2VsZWN0b3IpLnNob3coKTtcclxuXHRcdFx0X3VwZGF0ZUVtcHR5VGFibGVDb2xTcGFuKCk7XHJcblx0XHRcdFxyXG5cdFx0XHRjb2x1bW5EZWZpbml0aW9ucyA9ICR0aGlzLkRhdGFUYWJsZSgpLmluaXQoKS5jb2x1bW5zO1xyXG5cdFx0XHR3aWR0aEZhY3RvclN1bSA9IDA7XHJcblx0XHRcdFxyXG5cdFx0XHRjb2x1bW5EZWZpbml0aW9ucy5mb3JFYWNoKChjb2x1bW5EZWZpbml0aW9uKSA9PiB7XHJcblx0XHRcdFx0d2lkdGhGYWN0b3JTdW0gKz0gY29sdW1uRGVmaW5pdGlvbi53aWR0aEZhY3RvciB8fCAwO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdCR0aGlzLm9uKCdkcmF3LmR0JywgX29uRGF0YVRhYmxlRHJhdyk7XHJcblx0XHRcdCQod2luZG93KS5vbigncmVzaXplJywgX29uV2luZG93UmVzaXplKTtcclxuXHRcdFx0XHJcblx0XHRcdF9vbldpbmRvd1Jlc2l6ZSgpO1xyXG5cdFx0fSk7XHJcblx0XHRcclxuXHRcdCR0aGlzLmZpbmQob3B0aW9ucy52aXNpYmlsaXR5VG9nZ2xlU2VsZWN0b3IpLmhpZGUoKTtcclxuXHRcdFxyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxuXHRcclxufSk7IFxyXG4iXX0=
