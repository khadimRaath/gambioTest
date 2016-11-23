'use strict';

/* --------------------------------------------------------------
 init.js 2016-08-17
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Orders Table Controller
 *
 * This controller initializes the main orders table with a new jQuery DataTables instance.
 */
gx.controllers.module('init', ['datatable', 'modal', 'loading_spinner', 'user_configuration_service', gx.source + '/libs/button_dropdown', gx.source + '/libs/orders_overview_columns'], function (data) {

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
  * Module Instance
  *
  * @type {Object}
  */
	var module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Get Initial Table Order 
  * 
  * @param {Object} parameters Contains the URL parameters. 
  * @param {Object} columns Contains the column definitions. 
  * 
  * @return {Array[]}
  */
	function _getOrder(parameters, columns) {
		var index = 1; // Order by first column by default.
		var direction = 'desc'; // Order DESC by default. 

		// Apply initial table sort. 
		if (parameters.sort) {
			direction = parameters.sort.charAt(0) === '-' ? 'desc' : 'asc';
			var columnName = parameters.sort.slice(1);

			var _iteratorNormalCompletion = true;
			var _didIteratorError = false;
			var _iteratorError = undefined;

			try {
				for (var _iterator = columns[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
					var column = _step.value;

					if (column.name === columnName) {
						index = columns.indexOf(column);
						break;
					}
				}
			} catch (err) {
				_didIteratorError = true;
				_iteratorError = err;
			} finally {
				try {
					if (!_iteratorNormalCompletion && _iterator.return) {
						_iterator.return();
					}
				} finally {
					if (_didIteratorError) {
						throw _iteratorError;
					}
				}
			}
		} else if (data.activeColumns.indexOf('number') > -1) {
			// Order by number if possible.
			index = data.activeColumns.indexOf('number');
		}

		return [[index, direction]];
	}

	/**
  * Get Initial Search Cols
  * 
  * @param {Object} parameters Contains the URL parameters.
  * 
  * @returns {Object[]} Returns the initial filtering values.
  */
	function _getSearchCols(parameters, columns) {
		if (!parameters.filter) {
			return [];
		}

		var searchCols = [];

		var _iteratorNormalCompletion2 = true;
		var _didIteratorError2 = false;
		var _iteratorError2 = undefined;

		try {
			for (var _iterator2 = columns[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
				var column = _step2.value;

				var entry = null;
				var value = parameters.filter[column.name];

				if (value) {
					entry = { search: value };
				}

				searchCols.push(entry);
			}
		} catch (err) {
			_didIteratorError2 = true;
			_iteratorError2 = err;
		} finally {
			try {
				if (!_iteratorNormalCompletion2 && _iterator2.return) {
					_iterator2.return();
				}
			} finally {
				if (_didIteratorError2) {
					throw _iteratorError2;
				}
			}
		}

		return searchCols;
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		var columns = jse.libs.datatable.prepareColumns($this, jse.libs.orders_overview_columns, data.activeColumns);
		var parameters = $.deparam(window.location.search.slice(1));
		var pageLength = parseInt(parameters.length || data.pageLength);

		jse.libs.datatable.create($this, {
			autoWidth: false,
			dom: 't',
			pageLength: pageLength,
			displayStart: parseInt(parameters.page) ? (parseInt(parameters.page) - 1) * pageLength : 0,
			serverSide: true,
			language: jse.libs.datatable.getTranslations(jse.core.config.get('languageCode')),
			ajax: {
				url: jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersOverviewAjax/DataTable',
				type: 'POST',
				data: {
					pageToken: jse.core.config.get('pageToken')
				}
			},
			orderCellsTop: true,
			order: _getOrder(parameters, columns),
			searchCols: _getSearchCols(parameters, columns),
			columns: columns
		});

		// Add table error handler.
		jse.libs.datatable.error($this, function (event, settings, techNote, message) {
			jse.libs.modal.message({
				title: 'DataTables ' + jse.core.lang.translate('error', 'messages'),
				content: message
			});
		});

		$this.on('datatable_custom_pagination:length_change', function (event, newPageLength) {
			jse.libs.user_configuration_service.set({
				data: {
					userId: jse.core.registry.get('userId'),
					configurationKey: 'ordersOverviewPageLength',
					configurationValue: newPageLength
				}
			});
		});

		$this.on('draw.dt', function () {
			$this.find('thead input:checkbox').prop('checked', false).trigger('change', [false]); // No need to update the tbody checkboxes (event.js).
			$this.find('tbody').attr('data-gx-widget', 'single_checkbox');
			gx.widgets.init($this); // Initialize the checkbox widget.
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vdmVydmlldy9pbml0LmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJzb3VyY2UiLCJkYXRhIiwiJHRoaXMiLCIkIiwiX2dldE9yZGVyIiwicGFyYW1ldGVycyIsImNvbHVtbnMiLCJpbmRleCIsImRpcmVjdGlvbiIsInNvcnQiLCJjaGFyQXQiLCJjb2x1bW5OYW1lIiwic2xpY2UiLCJjb2x1bW4iLCJuYW1lIiwiaW5kZXhPZiIsImFjdGl2ZUNvbHVtbnMiLCJfZ2V0U2VhcmNoQ29scyIsImZpbHRlciIsInNlYXJjaENvbHMiLCJlbnRyeSIsInZhbHVlIiwic2VhcmNoIiwicHVzaCIsImluaXQiLCJkb25lIiwianNlIiwibGlicyIsImRhdGF0YWJsZSIsInByZXBhcmVDb2x1bW5zIiwib3JkZXJzX292ZXJ2aWV3X2NvbHVtbnMiLCJkZXBhcmFtIiwid2luZG93IiwibG9jYXRpb24iLCJwYWdlTGVuZ3RoIiwicGFyc2VJbnQiLCJsZW5ndGgiLCJjcmVhdGUiLCJhdXRvV2lkdGgiLCJkb20iLCJkaXNwbGF5U3RhcnQiLCJwYWdlIiwic2VydmVyU2lkZSIsImxhbmd1YWdlIiwiZ2V0VHJhbnNsYXRpb25zIiwiY29yZSIsImNvbmZpZyIsImdldCIsImFqYXgiLCJ1cmwiLCJ0eXBlIiwicGFnZVRva2VuIiwib3JkZXJDZWxsc1RvcCIsIm9yZGVyIiwiZXJyb3IiLCJldmVudCIsInNldHRpbmdzIiwidGVjaE5vdGUiLCJtZXNzYWdlIiwibW9kYWwiLCJ0aXRsZSIsImxhbmciLCJ0cmFuc2xhdGUiLCJjb250ZW50Iiwib24iLCJuZXdQYWdlTGVuZ3RoIiwidXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UiLCJzZXQiLCJ1c2VySWQiLCJyZWdpc3RyeSIsImNvbmZpZ3VyYXRpb25LZXkiLCJjb25maWd1cmF0aW9uVmFsdWUiLCJmaW5kIiwicHJvcCIsInRyaWdnZXIiLCJhdHRyIiwid2lkZ2V0cyJdLCJtYXBwaW5ncyI6Ijs7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QjtBQUN0QkUsV0FBTyxnQkFBZ0JsQyxJQUFJbUIsSUFBSixDQUFTZ0IsSUFBVCxDQUFjQyxTQUFkLENBQXdCLE9BQXhCLEVBQWlDLFVBQWpDLENBREQ7QUFFdEJDLGFBQVNMO0FBRmEsSUFBdkI7QUFJQSxHQUxEOztBQU9BeEQsUUFBTThELEVBQU4sQ0FBUywyQ0FBVCxFQUFzRCxVQUFTVCxLQUFULEVBQWdCVSxhQUFoQixFQUErQjtBQUNwRnZDLE9BQUlDLElBQUosQ0FBU3VDLDBCQUFULENBQW9DQyxHQUFwQyxDQUF3QztBQUN2Q2xFLFVBQU07QUFDTG1FLGFBQVExQyxJQUFJbUIsSUFBSixDQUFTd0IsUUFBVCxDQUFrQnRCLEdBQWxCLENBQXNCLFFBQXRCLENBREg7QUFFTHVCLHVCQUFrQiwwQkFGYjtBQUdMQyx5QkFBb0JOO0FBSGY7QUFEaUMsSUFBeEM7QUFPQSxHQVJEOztBQVVBL0QsUUFBTThELEVBQU4sQ0FBUyxTQUFULEVBQW9CLFlBQU07QUFDekI5RCxTQUFNc0UsSUFBTixDQUFXLHNCQUFYLEVBQ0VDLElBREYsQ0FDTyxTQURQLEVBQ2tCLEtBRGxCLEVBRUVDLE9BRkYsQ0FFVSxRQUZWLEVBRW9CLENBQUMsS0FBRCxDQUZwQixFQUR5QixDQUdLO0FBQzlCeEUsU0FBTXNFLElBQU4sQ0FBVyxPQUFYLEVBQW9CRyxJQUFwQixDQUF5QixnQkFBekIsRUFBMkMsaUJBQTNDO0FBQ0E5RSxNQUFHK0UsT0FBSCxDQUFXcEQsSUFBWCxDQUFnQnRCLEtBQWhCLEVBTHlCLENBS0Q7QUFDeEIsR0FORDs7QUFRQXVCO0FBQ0EsRUFyREQ7O0FBdURBLFFBQU8xQixNQUFQO0FBQ0EsQ0E1SkYiLCJmaWxlIjoib3JkZXJzL292ZXJ2aWV3L2luaXQuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gaW5pdC5qcyAyMDE2LTA4LTE3XHJcbiBHYW1iaW8gR21iSFxyXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcclxuIENvcHlyaWdodCAoYykgMjAxNiBHYW1iaW8gR21iSFxyXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXHJcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cclxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiAqL1xyXG5cclxuLyoqXHJcbiAqIE9yZGVycyBUYWJsZSBDb250cm9sbGVyXHJcbiAqXHJcbiAqIFRoaXMgY29udHJvbGxlciBpbml0aWFsaXplcyB0aGUgbWFpbiBvcmRlcnMgdGFibGUgd2l0aCBhIG5ldyBqUXVlcnkgRGF0YVRhYmxlcyBpbnN0YW5jZS5cclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZShcclxuXHQnaW5pdCcsXHJcblx0XHJcblx0W1xyXG5cdFx0J2RhdGF0YWJsZScsXHJcblx0XHQnbW9kYWwnLFxyXG5cdFx0J2xvYWRpbmdfc3Bpbm5lcicsXHJcblx0XHQndXNlcl9jb25maWd1cmF0aW9uX3NlcnZpY2UnLFxyXG5cdFx0YCR7Z3guc291cmNlfS9saWJzL2J1dHRvbl9kcm9wZG93bmAsXHJcblx0XHRgJHtneC5zb3VyY2V9L2xpYnMvb3JkZXJzX292ZXJ2aWV3X2NvbHVtbnNgXHJcblx0XSxcclxuXHRcclxuXHRmdW5jdGlvbihkYXRhKSB7XHJcblx0XHRcclxuXHRcdCd1c2Ugc3RyaWN0JztcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBWQVJJQUJMRVNcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdFx0ICpcclxuXHRcdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0XHQgKi9cclxuXHRcdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBNb2R1bGUgSW5zdGFuY2VcclxuXHRcdCAqXHJcblx0XHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdFx0ICovXHJcblx0XHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBGVU5DVElPTlNcclxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFx0XHJcblx0XHQvKipcclxuXHRcdCAqIEdldCBJbml0aWFsIFRhYmxlIE9yZGVyIFxyXG5cdFx0ICogXHJcblx0XHQgKiBAcGFyYW0ge09iamVjdH0gcGFyYW1ldGVycyBDb250YWlucyB0aGUgVVJMIHBhcmFtZXRlcnMuIFxyXG5cdFx0ICogQHBhcmFtIHtPYmplY3R9IGNvbHVtbnMgQ29udGFpbnMgdGhlIGNvbHVtbiBkZWZpbml0aW9ucy4gXHJcblx0XHQgKiBcclxuXHRcdCAqIEByZXR1cm4ge0FycmF5W119XHJcblx0XHQgKi9cclxuXHRcdGZ1bmN0aW9uIF9nZXRPcmRlcihwYXJhbWV0ZXJzLCBjb2x1bW5zKSB7XHJcblx0XHRcdGxldCBpbmRleCA9IDE7IC8vIE9yZGVyIGJ5IGZpcnN0IGNvbHVtbiBieSBkZWZhdWx0LlxyXG5cdFx0XHRsZXQgZGlyZWN0aW9uID0gJ2Rlc2MnOyAvLyBPcmRlciBERVNDIGJ5IGRlZmF1bHQuIFxyXG5cdFx0XHRcclxuXHRcdFx0Ly8gQXBwbHkgaW5pdGlhbCB0YWJsZSBzb3J0LiBcclxuXHRcdFx0aWYgKHBhcmFtZXRlcnMuc29ydCkge1xyXG5cdFx0XHRcdGRpcmVjdGlvbiA9IHBhcmFtZXRlcnMuc29ydC5jaGFyQXQoMCkgPT09ICctJyA/ICdkZXNjJyA6ICdhc2MnO1xyXG5cdFx0XHRcdGNvbnN0IGNvbHVtbk5hbWUgID0gcGFyYW1ldGVycy5zb3J0LnNsaWNlKDEpO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGZvciAobGV0IGNvbHVtbiBvZiBjb2x1bW5zKSB7XHJcblx0XHRcdFx0XHRpZiAoY29sdW1uLm5hbWUgPT09IGNvbHVtbk5hbWUpIHtcclxuXHRcdFx0XHRcdFx0aW5kZXggPSBjb2x1bW5zLmluZGV4T2YoY29sdW1uKTtcclxuXHRcdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0XHR9XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9IGVsc2UgaWYgKGRhdGEuYWN0aXZlQ29sdW1ucy5pbmRleE9mKCdudW1iZXInKSA+IC0xKSB7IC8vIE9yZGVyIGJ5IG51bWJlciBpZiBwb3NzaWJsZS5cclxuXHRcdFx0XHRpbmRleCA9IGRhdGEuYWN0aXZlQ29sdW1ucy5pbmRleE9mKCdudW1iZXInKTtcclxuXHRcdFx0fVxyXG5cdFx0XHRcclxuXHRcdFx0cmV0dXJuIFtbaW5kZXgsIGRpcmVjdGlvbl1dOyBcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0LyoqXHJcblx0XHQgKiBHZXQgSW5pdGlhbCBTZWFyY2ggQ29sc1xyXG5cdFx0ICogXHJcblx0XHQgKiBAcGFyYW0ge09iamVjdH0gcGFyYW1ldGVycyBDb250YWlucyB0aGUgVVJMIHBhcmFtZXRlcnMuXHJcblx0XHQgKiBcclxuXHRcdCAqIEByZXR1cm5zIHtPYmplY3RbXX0gUmV0dXJucyB0aGUgaW5pdGlhbCBmaWx0ZXJpbmcgdmFsdWVzLlxyXG5cdFx0ICovXHJcblx0XHRmdW5jdGlvbiBfZ2V0U2VhcmNoQ29scyhwYXJhbWV0ZXJzLCBjb2x1bW5zKSB7XHJcblx0XHRcdGlmICghcGFyYW1ldGVycy5maWx0ZXIpIHtcclxuXHRcdFx0XHRyZXR1cm4gW107XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdGNvbnN0IHNlYXJjaENvbHMgPSBbXTtcclxuXHRcdFx0XHJcblx0XHRcdGZvciAobGV0IGNvbHVtbiBvZiBjb2x1bW5zKSB7XHRcdFx0XHRcclxuXHRcdFx0XHRsZXQgZW50cnkgPSBudWxsO1xyXG5cdFx0XHRcdGxldCB2YWx1ZSA9IHBhcmFtZXRlcnMuZmlsdGVyW2NvbHVtbi5uYW1lXTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRpZiAodmFsdWUpIHtcclxuXHRcdFx0XHRcdGVudHJ5ID0geyBzZWFyY2g6IHZhbHVlIH07XHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdHNlYXJjaENvbHMucHVzaChlbnRyeSk7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdHJldHVybiBzZWFyY2hDb2xzOyBcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHRcclxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0XHRjb25zdCBjb2x1bW5zID0ganNlLmxpYnMuZGF0YXRhYmxlLnByZXBhcmVDb2x1bW5zKCR0aGlzLCBqc2UubGlicy5vcmRlcnNfb3ZlcnZpZXdfY29sdW1ucyxcclxuXHRcdFx0XHRkYXRhLmFjdGl2ZUNvbHVtbnMpO1xyXG5cdFx0XHRjb25zdCBwYXJhbWV0ZXJzID0gJC5kZXBhcmFtKHdpbmRvdy5sb2NhdGlvbi5zZWFyY2guc2xpY2UoMSkpO1xyXG5cdFx0XHRjb25zdCBwYWdlTGVuZ3RoID0gcGFyc2VJbnQocGFyYW1ldGVycy5sZW5ndGggfHwgZGF0YS5wYWdlTGVuZ3RoKTsgXHJcblx0XHRcdFxyXG5cdFx0XHRqc2UubGlicy5kYXRhdGFibGUuY3JlYXRlKCR0aGlzLCB7XHJcblx0XHRcdFx0YXV0b1dpZHRoOiBmYWxzZSxcclxuXHRcdFx0XHRkb206ICd0JyxcclxuXHRcdFx0XHRwYWdlTGVuZ3RoOiBwYWdlTGVuZ3RoLFxyXG5cdFx0XHRcdGRpc3BsYXlTdGFydDogcGFyc2VJbnQocGFyYW1ldGVycy5wYWdlKSA/IChwYXJzZUludChwYXJhbWV0ZXJzLnBhZ2UpIC0gMSkgKiBwYWdlTGVuZ3RoIDogMCxcclxuXHRcdFx0XHRzZXJ2ZXJTaWRlOiB0cnVlLFxyXG5cdFx0XHRcdGxhbmd1YWdlOiBqc2UubGlicy5kYXRhdGFibGUuZ2V0VHJhbnNsYXRpb25zKGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2xhbmd1YWdlQ29kZScpKSxcclxuXHRcdFx0XHRhamF4OiB7XHJcblx0XHRcdFx0XHR1cmw6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ2FwcFVybCcpICsgJy9hZG1pbi9hZG1pbi5waHA/ZG89T3JkZXJzT3ZlcnZpZXdBamF4L0RhdGFUYWJsZScsXHJcblx0XHRcdFx0XHR0eXBlOiAnUE9TVCcsXHJcblx0XHRcdFx0XHRkYXRhOiB7XHJcblx0XHRcdFx0XHRcdHBhZ2VUb2tlbjoganNlLmNvcmUuY29uZmlnLmdldCgncGFnZVRva2VuJylcclxuXHRcdFx0XHRcdH1cclxuXHRcdFx0XHR9LFxyXG5cdFx0XHRcdG9yZGVyQ2VsbHNUb3A6IHRydWUsXHJcblx0XHRcdFx0b3JkZXI6IF9nZXRPcmRlcihwYXJhbWV0ZXJzLCBjb2x1bW5zKSxcclxuXHRcdFx0XHRzZWFyY2hDb2xzOiBfZ2V0U2VhcmNoQ29scyhwYXJhbWV0ZXJzLCBjb2x1bW5zKSxcclxuXHRcdFx0XHRjb2x1bW5zOiBjb2x1bW5zXHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0Ly8gQWRkIHRhYmxlIGVycm9yIGhhbmRsZXIuXHJcblx0XHRcdGpzZS5saWJzLmRhdGF0YWJsZS5lcnJvcigkdGhpcywgZnVuY3Rpb24oZXZlbnQsIHNldHRpbmdzLCB0ZWNoTm90ZSwgbWVzc2FnZSkge1xyXG5cdFx0XHRcdGpzZS5saWJzLm1vZGFsLm1lc3NhZ2Uoe1xyXG5cdFx0XHRcdFx0dGl0bGU6ICdEYXRhVGFibGVzICcgKyBqc2UuY29yZS5sYW5nLnRyYW5zbGF0ZSgnZXJyb3InLCAnbWVzc2FnZXMnKSxcclxuXHRcdFx0XHRcdGNvbnRlbnQ6IG1lc3NhZ2VcclxuXHRcdFx0XHR9KTtcclxuXHRcdFx0fSk7XHJcblx0XHRcdFxyXG5cdFx0XHQkdGhpcy5vbignZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uOmxlbmd0aF9jaGFuZ2UnLCBmdW5jdGlvbihldmVudCwgbmV3UGFnZUxlbmd0aCkge1xyXG5cdFx0XHRcdGpzZS5saWJzLnVzZXJfY29uZmlndXJhdGlvbl9zZXJ2aWNlLnNldCh7XHJcblx0XHRcdFx0XHRkYXRhOiB7XHJcblx0XHRcdFx0XHRcdHVzZXJJZDoganNlLmNvcmUucmVnaXN0cnkuZ2V0KCd1c2VySWQnKSxcclxuXHRcdFx0XHRcdFx0Y29uZmlndXJhdGlvbktleTogJ29yZGVyc092ZXJ2aWV3UGFnZUxlbmd0aCcsXHJcblx0XHRcdFx0XHRcdGNvbmZpZ3VyYXRpb25WYWx1ZTogbmV3UGFnZUxlbmd0aFxyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9KTtcclxuXHRcdFx0XHJcblx0XHRcdCR0aGlzLm9uKCdkcmF3LmR0JywgKCkgPT4ge1xyXG5cdFx0XHRcdCR0aGlzLmZpbmQoJ3RoZWFkIGlucHV0OmNoZWNrYm94JylcclxuXHRcdFx0XHRcdC5wcm9wKCdjaGVja2VkJywgZmFsc2UpXHJcblx0XHRcdFx0XHQudHJpZ2dlcignY2hhbmdlJywgW2ZhbHNlXSk7IC8vIE5vIG5lZWQgdG8gdXBkYXRlIHRoZSB0Ym9keSBjaGVja2JveGVzIChldmVudC5qcykuXHJcblx0XHRcdFx0JHRoaXMuZmluZCgndGJvZHknKS5hdHRyKCdkYXRhLWd4LXdpZGdldCcsICdzaW5nbGVfY2hlY2tib3gnKTtcclxuXHRcdFx0XHRneC53aWRnZXRzLmluaXQoJHRoaXMpOyAvLyBJbml0aWFsaXplIHRoZSBjaGVja2JveCB3aWRnZXQuXHJcblx0XHRcdH0pO1xyXG5cdFx0XHRcclxuXHRcdFx0ZG9uZSgpO1xyXG5cdFx0fTtcclxuXHRcdFxyXG5cdFx0cmV0dXJuIG1vZHVsZTtcclxuXHR9KTtcclxuIl19
