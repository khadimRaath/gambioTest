'use strict';

/* --------------------------------------------------------------
 filter.js 2016-07-07
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Handles the orders table filtering.
 *
 * ### Methods
 *
 * **Reload Filtering Options**
 *
 * ```
 * // Reload the filter options with an AJAX request (optionally provide a second parameter for the AJAX URL).
 * $('.table-main').orders_overview_filter('reload');
 * ```
 */
gx.controllers.module('filter', [], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES
	// ------------------------------------------------------------------------

	/**
  * Enter Key Code
  *
  * @type {Number}
  */

	var ENTER_KEY_CODE = 13;

	/**
  * Module Selector
  *
  * @type {jQuery}
  */
	var $this = $(this);

	/**
  * Filter Row Selector
  *
  * @type {jQuery}
  */
	var $filter = $this.find('tr.filter');

	/**
  * Module Instance
  *
  * @type {Object}
  */
	var module = { bindings: {} };

	// Dynamically define the filter row data-bindings. 
	$filter.find('th').each(function () {
		var columnName = $(this).data('columnName');

		if (columnName === 'checkbox' || columnName === 'actions') {
			return true;
		}

		module.bindings[columnName] = $(this).find('input, select').first();
	});

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Reload filter options with an Ajax request.
  *
  * This function implements the $('.datatable').orders_overview_filter('reload') which will reload the filtering 
  * "multi_select" instances will new options. It must be used after some table data are changed and the filtering 
  * options need to be updated.
  * 
  * @param {String} url Optional, the URL to be used for fetching the options. Do not add the "pageToken" 
  * parameter to URL, it will be appended in this method.
  */
	function _reload(url) {
		url = url || jse.core.config.get('appUrl') + '/admin/admin.php?do=OrdersOverviewAjax/FilterOptions';
		var data = { pageToken: jse.core.config.get('pageToken') };

		$.getJSON(url, data).done(function (response) {
			for (var column in response) {
				var $select = $filter.find('.SumoSelect > select.' + column);
				var currentValueBackup = $select.val(); // Will try to set it back if it still exists. 

				if (!$select.length) {
					return; // The select element was not found.
				}

				$select.empty();

				var _iteratorNormalCompletion = true;
				var _didIteratorError = false;
				var _iteratorError = undefined;

				try {
					for (var _iterator = response[column][Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
						var option = _step.value;

						$select.append(new Option(option.text, option.value));
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

				if (currentValueBackup !== null) {
					$select.val(currentValueBackup);
				}

				$select.multi_select('refresh');
			}
		});
	}

	/**
  * Add public "orders_overview_filter" method to jQuery in order.
  */
	function _addPublicMethod() {
		if ($.fn.orders_overview_filter) {
			return;
		}

		$.fn.extend({
			orders_overview_filter: function orders_overview_filter(action) {
				for (var _len = arguments.length, args = Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
					args[_key - 1] = arguments[_key];
				}

				$.each(this, function () {
					switch (action) {
						case 'reload':
							_reload.apply(this, args);
							break;
					}
				});
			}
		});
	}

	/**
  * On Filter Button Click
  *
  * Apply the provided filters and update the table rows.
  */
	function _onApplyFiltersClick() {
		// Prepare the object with the final filtering data.
		var filter = {};

		$filter.find('th').each(function () {
			var columnName = $(this).data('columnName');

			if (columnName === 'checkbox' || columnName === 'actions') {
				return true;
			}

			var value = module.bindings[columnName].get();

			if (value) {
				filter[columnName] = value;
				$this.DataTable().column(columnName + ':name').search(value);
			} else {
				$this.DataTable().column(columnName + ':name').search('');
			}
		});

		$this.trigger('orders_overview_filter:change', [filter]);
		$this.DataTable().draw();
	}

	/**
  * On Reset Button Click
  *
  * Reset the filter form and reload the table data without filtering.
  */
	function _onResetFiltersClick() {
		// Remove values from the input boxes.
		$filter.find('input, select').not('.length').val('');
		$filter.find('select').not('.length').multi_select('refresh');

		// Reset the filtering values.
		$this.DataTable().columns().search('').draw();

		// Trigger Event
		$this.trigger('orders_overview_filter:change', [{}]);
	}

	/**
  * Apply the filters when the user presses the Enter key.
  *
  * @param {jQuery.Event} event
  */
	function _onInputTextKeyUp(event) {
		if (event.which === ENTER_KEY_CODE) {
			$filter.find('.apply-filters').trigger('click');
		}
	}

	/**
  * Parse the initial filtering parameters and apply them to the table.
  */
	function _parseFilteringParameters() {
		var _$$deparam = $.deparam(window.location.search.slice(1)),
		    filter = _$$deparam.filter;

		for (var name in filter) {
			var value = filter[name];

			if (module.bindings[name]) {
				module.bindings[name].set(value);
			}
		}
	}

	/**
  * Normalize array filtering values. 
  * 
  * By default datatables will concatenate array search values into a string separated with "," commas. This 
  * is not acceptable though because some filtering elements may contain values with comma and thus the array
  * cannot be parsed from backend. This method will reset those cases back to arrays for a clearer transaction
  * with the backend.
  * 
  * @param {jQuery.Event} event jQuery event object.
  * @param {DataTables.Settings} settings DataTables settings object.
  * @param {Object} data Data that will be sent to the server in an object form.
  */
	function _normalizeArrayValues(event, settings, data) {
		var filter = {};

		for (var name in module.bindings) {
			var value = module.bindings[name].get();

			if (value && value.constructor === Array) {
				filter[name] = value;
			}
		}

		for (var entry in filter) {
			var _iteratorNormalCompletion2 = true;
			var _didIteratorError2 = false;
			var _iteratorError2 = undefined;

			try {
				for (var _iterator2 = data.columns[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
					var column = _step2.value;

					if (entry === column.name && filter[entry].constructor === Array) {
						column.search.value = filter[entry];
						break;
					}
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
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		// Add public module method. 
		_addPublicMethod();

		// Parse filtering GET parameters. 
		_parseFilteringParameters();

		// Bind event handlers.
		$filter.on('keyup', 'input:text', _onInputTextKeyUp).on('click', '.apply-filters', _onApplyFiltersClick).on('click', '.reset-filters', _onResetFiltersClick);

		$this.on('preXhr.dt', _normalizeArrayValues);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vdmVydmlldy9maWx0ZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb250cm9sbGVycyIsIm1vZHVsZSIsImRhdGEiLCJFTlRFUl9LRVlfQ09ERSIsIiR0aGlzIiwiJCIsIiRmaWx0ZXIiLCJmaW5kIiwiYmluZGluZ3MiLCJlYWNoIiwiY29sdW1uTmFtZSIsImZpcnN0IiwiX3JlbG9hZCIsInVybCIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJwYWdlVG9rZW4iLCJnZXRKU09OIiwiZG9uZSIsInJlc3BvbnNlIiwiY29sdW1uIiwiJHNlbGVjdCIsImN1cnJlbnRWYWx1ZUJhY2t1cCIsInZhbCIsImxlbmd0aCIsImVtcHR5Iiwib3B0aW9uIiwiYXBwZW5kIiwiT3B0aW9uIiwidGV4dCIsInZhbHVlIiwibXVsdGlfc2VsZWN0IiwiX2FkZFB1YmxpY01ldGhvZCIsImZuIiwib3JkZXJzX292ZXJ2aWV3X2ZpbHRlciIsImV4dGVuZCIsImFjdGlvbiIsImFyZ3MiLCJhcHBseSIsIl9vbkFwcGx5RmlsdGVyc0NsaWNrIiwiZmlsdGVyIiwiRGF0YVRhYmxlIiwic2VhcmNoIiwidHJpZ2dlciIsImRyYXciLCJfb25SZXNldEZpbHRlcnNDbGljayIsIm5vdCIsImNvbHVtbnMiLCJfb25JbnB1dFRleHRLZXlVcCIsImV2ZW50Iiwid2hpY2giLCJfcGFyc2VGaWx0ZXJpbmdQYXJhbWV0ZXJzIiwiZGVwYXJhbSIsIndpbmRvdyIsImxvY2F0aW9uIiwic2xpY2UiLCJuYW1lIiwic2V0IiwiX25vcm1hbGl6ZUFycmF5VmFsdWVzIiwic2V0dGluZ3MiLCJjb25zdHJ1Y3RvciIsIkFycmF5IiwiZW50cnkiLCJpbml0Iiwib24iXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7O0FBWUFBLEdBQUdDLFdBQUgsQ0FBZUMsTUFBZixDQUFzQixRQUF0QixFQUFnQyxFQUFoQyxFQUFvQyxVQUFTQyxJQUFULEVBQWU7O0FBRWxEOztBQUVB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QyxJQUFqQixJQUF5QmhCLE9BQU9zQixLQUFQLEVBQWNGLFdBQWQsS0FBOEJDLEtBQTNELEVBQWtFO0FBQ2pFekMsYUFBT3NCLE1BQVAsQ0FBY1osS0FBZCxHQUFzQlUsT0FBT3NCLEtBQVAsQ0FBdEI7QUFDQTtBQUNBO0FBQ0Q7QUFOd0I7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQU96QjtBQUNEOztBQUVEO0FBQ0E7QUFDQTs7QUFFQS9ELFFBQU9nRSxJQUFQLEdBQWMsVUFBUzdDLElBQVQsRUFBZTtBQUM1QjtBQUNBYzs7QUFFQTtBQUNBbUI7O0FBRUE7QUFDQS9DLFVBQ0U0RCxFQURGLENBQ0ssT0FETCxFQUNjLFlBRGQsRUFDNEJoQixpQkFENUIsRUFFRWdCLEVBRkYsQ0FFSyxPQUZMLEVBRWMsZ0JBRmQsRUFFZ0N6QixvQkFGaEMsRUFHRXlCLEVBSEYsQ0FHSyxPQUhMLEVBR2MsZ0JBSGQsRUFHZ0NuQixvQkFIaEM7O0FBS0EzQyxRQUFNOEQsRUFBTixDQUFTLFdBQVQsRUFBc0JOLHFCQUF0Qjs7QUFFQXhDO0FBQ0EsRUFoQkQ7O0FBa0JBLFFBQU9uQixNQUFQO0FBQ0EsQ0EvT0QiLCJmaWxlIjoib3JkZXJzL292ZXJ2aWV3L2ZpbHRlci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBmaWx0ZXIuanMgMjAxNi0wNy0wN1xyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBIYW5kbGVzIHRoZSBvcmRlcnMgdGFibGUgZmlsdGVyaW5nLlxyXG4gKlxyXG4gKiAjIyMgTWV0aG9kc1xyXG4gKlxyXG4gKiAqKlJlbG9hZCBGaWx0ZXJpbmcgT3B0aW9ucyoqXHJcbiAqXHJcbiAqIGBgYFxyXG4gKiAvLyBSZWxvYWQgdGhlIGZpbHRlciBvcHRpb25zIHdpdGggYW4gQUpBWCByZXF1ZXN0IChvcHRpb25hbGx5IHByb3ZpZGUgYSBzZWNvbmQgcGFyYW1ldGVyIGZvciB0aGUgQUpBWCBVUkwpLlxyXG4gKiAkKCcudGFibGUtbWFpbicpLm9yZGVyc19vdmVydmlld19maWx0ZXIoJ3JlbG9hZCcpO1xyXG4gKiBgYGBcclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZSgnZmlsdGVyJywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHRcclxuXHQndXNlIHN0cmljdCc7XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gVkFSSUFCTEVTXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0LyoqXHJcblx0ICogRW50ZXIgS2V5IENvZGVcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtOdW1iZXJ9XHJcblx0ICovXHJcblx0Y29uc3QgRU5URVJfS0VZX0NPREUgPSAxMztcclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEZpbHRlciBSb3cgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJGZpbHRlciA9ICR0aGlzLmZpbmQoJ3RyLmZpbHRlcicpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7YmluZGluZ3M6IHt9fTtcclxuXHRcclxuXHQvLyBEeW5hbWljYWxseSBkZWZpbmUgdGhlIGZpbHRlciByb3cgZGF0YS1iaW5kaW5ncy4gXHJcblx0JGZpbHRlci5maW5kKCd0aCcpLmVhY2goZnVuY3Rpb24oKSB7XHJcblx0XHRjb25zdCBjb2x1bW5OYW1lID0gJCh0aGlzKS5kYXRhKCdjb2x1bW5OYW1lJyk7XHJcblx0XHRcclxuXHRcdGlmIChjb2x1bW5OYW1lID09PSAnY2hlY2tib3gnIHx8IGNvbHVtbk5hbWUgPT09ICdhY3Rpb25zJykge1xyXG5cdFx0XHRyZXR1cm4gdHJ1ZTtcclxuXHRcdH1cclxuXHRcdFxyXG5cdFx0bW9kdWxlLmJpbmRpbmdzW2NvbHVtbk5hbWVdID0gJCh0aGlzKS5maW5kKCdpbnB1dCwgc2VsZWN0JykuZmlyc3QoKTtcclxuXHR9KTtcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBGVU5DVElPTlNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBSZWxvYWQgZmlsdGVyIG9wdGlvbnMgd2l0aCBhbiBBamF4IHJlcXVlc3QuXHJcblx0ICpcclxuXHQgKiBUaGlzIGZ1bmN0aW9uIGltcGxlbWVudHMgdGhlICQoJy5kYXRhdGFibGUnKS5vcmRlcnNfb3ZlcnZpZXdfZmlsdGVyKCdyZWxvYWQnKSB3aGljaCB3aWxsIHJlbG9hZCB0aGUgZmlsdGVyaW5nIFxyXG5cdCAqIFwibXVsdGlfc2VsZWN0XCIgaW5zdGFuY2VzIHdpbGwgbmV3IG9wdGlvbnMuIEl0IG11c3QgYmUgdXNlZCBhZnRlciBzb21lIHRhYmxlIGRhdGEgYXJlIGNoYW5nZWQgYW5kIHRoZSBmaWx0ZXJpbmcgXHJcblx0ICogb3B0aW9ucyBuZWVkIHRvIGJlIHVwZGF0ZWQuXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtTdHJpbmd9IHVybCBPcHRpb25hbCwgdGhlIFVSTCB0byBiZSB1c2VkIGZvciBmZXRjaGluZyB0aGUgb3B0aW9ucy4gRG8gbm90IGFkZCB0aGUgXCJwYWdlVG9rZW5cIiBcclxuXHQgKiBwYXJhbWV0ZXIgdG8gVVJMLCBpdCB3aWxsIGJlIGFwcGVuZGVkIGluIHRoaXMgbWV0aG9kLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9yZWxvYWQodXJsKSB7XHJcblx0XHR1cmwgPSB1cmwgfHwganNlLmNvcmUuY29uZmlnLmdldCgnYXBwVXJsJykgKyAnL2FkbWluL2FkbWluLnBocD9kbz1PcmRlcnNPdmVydmlld0FqYXgvRmlsdGVyT3B0aW9ucyc7XHJcblx0XHRjb25zdCBkYXRhID0ge3BhZ2VUb2tlbjoganNlLmNvcmUuY29uZmlnLmdldCgncGFnZVRva2VuJyl9O1xyXG5cdFx0XHJcblx0XHQkLmdldEpTT04odXJsLCBkYXRhKS5kb25lKChyZXNwb25zZSkgPT4ge1xyXG5cdFx0XHRmb3IgKGxldCBjb2x1bW4gaW4gcmVzcG9uc2UpIHtcclxuXHRcdFx0XHRjb25zdCAkc2VsZWN0ID0gJGZpbHRlci5maW5kKCcuU3Vtb1NlbGVjdCA+IHNlbGVjdC4nICsgY29sdW1uKTtcclxuXHRcdFx0XHRjb25zdCBjdXJyZW50VmFsdWVCYWNrdXAgPSAkc2VsZWN0LnZhbCgpOyAvLyBXaWxsIHRyeSB0byBzZXQgaXQgYmFjayBpZiBpdCBzdGlsbCBleGlzdHMuIFxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmICghJHNlbGVjdC5sZW5ndGgpIHtcclxuXHRcdFx0XHRcdHJldHVybjsgLy8gVGhlIHNlbGVjdCBlbGVtZW50IHdhcyBub3QgZm91bmQuXHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdCRzZWxlY3QuZW1wdHkoKTtcclxuXHRcdFx0XHRcclxuXHRcdFx0XHRmb3IgKGxldCBvcHRpb24gb2YgcmVzcG9uc2VbY29sdW1uXSkge1xyXG5cdFx0XHRcdFx0JHNlbGVjdC5hcHBlbmQobmV3IE9wdGlvbihvcHRpb24udGV4dCwgb3B0aW9uLnZhbHVlKSk7XHJcblx0XHRcdFx0fVx0XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0aWYgKGN1cnJlbnRWYWx1ZUJhY2t1cCAhPT0gbnVsbCkge1xyXG5cdFx0XHRcdFx0JHNlbGVjdC52YWwoY3VycmVudFZhbHVlQmFja3VwKTtcclxuXHRcdFx0XHR9XHJcblx0XHRcdFx0XHJcblx0XHRcdFx0JHNlbGVjdC5tdWx0aV9zZWxlY3QoJ3JlZnJlc2gnKTtcclxuXHRcdFx0fVxyXG5cdFx0fSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEFkZCBwdWJsaWMgXCJvcmRlcnNfb3ZlcnZpZXdfZmlsdGVyXCIgbWV0aG9kIHRvIGpRdWVyeSBpbiBvcmRlci5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfYWRkUHVibGljTWV0aG9kKCkge1xyXG5cdFx0aWYgKCQuZm4ub3JkZXJzX292ZXJ2aWV3X2ZpbHRlcikge1xyXG5cdFx0XHRyZXR1cm47XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdCQuZm4uZXh0ZW5kKHtcclxuXHRcdFx0b3JkZXJzX292ZXJ2aWV3X2ZpbHRlcjogZnVuY3Rpb24oYWN0aW9uLCAuLi5hcmdzKSB7XHJcblx0XHRcdFx0JC5lYWNoKHRoaXMsIGZ1bmN0aW9uKCkge1xyXG5cdFx0XHRcdFx0c3dpdGNoIChhY3Rpb24pIHtcclxuXHRcdFx0XHRcdFx0Y2FzZSAncmVsb2FkJzpcclxuXHRcdFx0XHRcdFx0XHRfcmVsb2FkLmFwcGx5KHRoaXMsIGFyZ3MpO1xyXG5cdFx0XHRcdFx0XHRcdGJyZWFrO1xyXG5cdFx0XHRcdFx0fVxyXG5cdFx0XHRcdH0pO1xyXG5cdFx0XHR9XHJcblx0XHR9KTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogT24gRmlsdGVyIEJ1dHRvbiBDbGlja1xyXG5cdCAqXHJcblx0ICogQXBwbHkgdGhlIHByb3ZpZGVkIGZpbHRlcnMgYW5kIHVwZGF0ZSB0aGUgdGFibGUgcm93cy5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25BcHBseUZpbHRlcnNDbGljaygpIHtcclxuXHRcdC8vIFByZXBhcmUgdGhlIG9iamVjdCB3aXRoIHRoZSBmaW5hbCBmaWx0ZXJpbmcgZGF0YS5cclxuXHRcdGNvbnN0IGZpbHRlciA9IHt9O1xyXG5cdFx0XHJcblx0XHQkZmlsdGVyLmZpbmQoJ3RoJykuZWFjaChmdW5jdGlvbigpIHtcclxuXHRcdFx0Y29uc3QgY29sdW1uTmFtZSA9ICQodGhpcykuZGF0YSgnY29sdW1uTmFtZScpO1xyXG5cdFx0XHRcclxuXHRcdFx0aWYgKGNvbHVtbk5hbWUgPT09ICdjaGVja2JveCcgfHwgY29sdW1uTmFtZSA9PT0gJ2FjdGlvbnMnKSB7XHJcblx0XHRcdFx0cmV0dXJuIHRydWU7XHJcblx0XHRcdH1cclxuXHRcdFx0XHJcblx0XHRcdGxldCB2YWx1ZSA9IG1vZHVsZS5iaW5kaW5nc1tjb2x1bW5OYW1lXS5nZXQoKTtcclxuXHRcdFx0XHJcblx0XHRcdGlmICh2YWx1ZSkge1xyXG5cdFx0XHRcdGZpbHRlcltjb2x1bW5OYW1lXSA9IHZhbHVlO1xyXG5cdFx0XHRcdCR0aGlzLkRhdGFUYWJsZSgpLmNvbHVtbihgJHtjb2x1bW5OYW1lfTpuYW1lYCkuc2VhcmNoKHZhbHVlKTtcclxuXHRcdFx0fSBlbHNlIHtcclxuXHRcdFx0XHQkdGhpcy5EYXRhVGFibGUoKS5jb2x1bW4oYCR7Y29sdW1uTmFtZX06bmFtZWApLnNlYXJjaCgnJyk7XHJcblx0XHRcdH0gXHJcblx0XHR9KTtcclxuXHRcdFxyXG5cdFx0JHRoaXMudHJpZ2dlcignb3JkZXJzX292ZXJ2aWV3X2ZpbHRlcjpjaGFuZ2UnLCBbZmlsdGVyXSk7XHJcblx0XHQkdGhpcy5EYXRhVGFibGUoKS5kcmF3KCk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIFJlc2V0IEJ1dHRvbiBDbGlja1xyXG5cdCAqXHJcblx0ICogUmVzZXQgdGhlIGZpbHRlciBmb3JtIGFuZCByZWxvYWQgdGhlIHRhYmxlIGRhdGEgd2l0aG91dCBmaWx0ZXJpbmcuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uUmVzZXRGaWx0ZXJzQ2xpY2soKSB7XHJcblx0XHQvLyBSZW1vdmUgdmFsdWVzIGZyb20gdGhlIGlucHV0IGJveGVzLlxyXG5cdFx0JGZpbHRlci5maW5kKCdpbnB1dCwgc2VsZWN0Jykubm90KCcubGVuZ3RoJykudmFsKCcnKTtcclxuXHRcdCRmaWx0ZXIuZmluZCgnc2VsZWN0Jykubm90KCcubGVuZ3RoJykubXVsdGlfc2VsZWN0KCdyZWZyZXNoJyk7XHJcblx0XHRcclxuXHRcdC8vIFJlc2V0IHRoZSBmaWx0ZXJpbmcgdmFsdWVzLlxyXG5cdFx0JHRoaXMuRGF0YVRhYmxlKCkuY29sdW1ucygpLnNlYXJjaCgnJykuZHJhdygpO1xyXG5cdFx0XHJcblx0XHQvLyBUcmlnZ2VyIEV2ZW50XHJcblx0XHQkdGhpcy50cmlnZ2VyKCdvcmRlcnNfb3ZlcnZpZXdfZmlsdGVyOmNoYW5nZScsIFt7fV0pO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBBcHBseSB0aGUgZmlsdGVycyB3aGVuIHRoZSB1c2VyIHByZXNzZXMgdGhlIEVudGVyIGtleS5cclxuXHQgKlxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbklucHV0VGV4dEtleVVwKGV2ZW50KSB7XHJcblx0XHRpZiAoZXZlbnQud2hpY2ggPT09IEVOVEVSX0tFWV9DT0RFKSB7XHJcblx0XHRcdCRmaWx0ZXIuZmluZCgnLmFwcGx5LWZpbHRlcnMnKS50cmlnZ2VyKCdjbGljaycpO1xyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBQYXJzZSB0aGUgaW5pdGlhbCBmaWx0ZXJpbmcgcGFyYW1ldGVycyBhbmQgYXBwbHkgdGhlbSB0byB0aGUgdGFibGUuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX3BhcnNlRmlsdGVyaW5nUGFyYW1ldGVycygpIHtcclxuXHRcdGNvbnN0IHtmaWx0ZXJ9ID0gJC5kZXBhcmFtKHdpbmRvdy5sb2NhdGlvbi5zZWFyY2guc2xpY2UoMSkpO1xyXG5cdFx0XHJcblx0XHRmb3IgKGxldCBuYW1lIGluIGZpbHRlcikge1xyXG5cdFx0XHRjb25zdCB2YWx1ZSA9IGZpbHRlcltuYW1lXTtcclxuXHRcdFx0XHJcblx0XHRcdGlmIChtb2R1bGUuYmluZGluZ3NbbmFtZV0pIHtcclxuXHRcdFx0XHRtb2R1bGUuYmluZGluZ3NbbmFtZV0uc2V0KHZhbHVlKTtcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBOb3JtYWxpemUgYXJyYXkgZmlsdGVyaW5nIHZhbHVlcy4gXHJcblx0ICogXHJcblx0ICogQnkgZGVmYXVsdCBkYXRhdGFibGVzIHdpbGwgY29uY2F0ZW5hdGUgYXJyYXkgc2VhcmNoIHZhbHVlcyBpbnRvIGEgc3RyaW5nIHNlcGFyYXRlZCB3aXRoIFwiLFwiIGNvbW1hcy4gVGhpcyBcclxuXHQgKiBpcyBub3QgYWNjZXB0YWJsZSB0aG91Z2ggYmVjYXVzZSBzb21lIGZpbHRlcmluZyBlbGVtZW50cyBtYXkgY29udGFpbiB2YWx1ZXMgd2l0aCBjb21tYSBhbmQgdGh1cyB0aGUgYXJyYXlcclxuXHQgKiBjYW5ub3QgYmUgcGFyc2VkIGZyb20gYmFja2VuZC4gVGhpcyBtZXRob2Qgd2lsbCByZXNldCB0aG9zZSBjYXNlcyBiYWNrIHRvIGFycmF5cyBmb3IgYSBjbGVhcmVyIHRyYW5zYWN0aW9uXHJcblx0ICogd2l0aCB0aGUgYmFja2VuZC5cclxuXHQgKiBcclxuXHQgKiBAcGFyYW0ge2pRdWVyeS5FdmVudH0gZXZlbnQgalF1ZXJ5IGV2ZW50IG9iamVjdC5cclxuXHQgKiBAcGFyYW0ge0RhdGFUYWJsZXMuU2V0dGluZ3N9IHNldHRpbmdzIERhdGFUYWJsZXMgc2V0dGluZ3Mgb2JqZWN0LlxyXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBkYXRhIERhdGEgdGhhdCB3aWxsIGJlIHNlbnQgdG8gdGhlIHNlcnZlciBpbiBhbiBvYmplY3QgZm9ybS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfbm9ybWFsaXplQXJyYXlWYWx1ZXMoZXZlbnQsIHNldHRpbmdzLCBkYXRhKSB7XHJcblx0XHRjb25zdCBmaWx0ZXIgPSB7fTsgXHJcblx0XHRcclxuXHRcdGZvciAobGV0IG5hbWUgaW4gbW9kdWxlLmJpbmRpbmdzKSB7XHJcblx0XHRcdGNvbnN0IHZhbHVlID0gbW9kdWxlLmJpbmRpbmdzW25hbWVdLmdldCgpOyBcclxuXHRcdFx0XHJcblx0XHRcdGlmICh2YWx1ZSAmJiB2YWx1ZS5jb25zdHJ1Y3RvciA9PT0gQXJyYXkpIHtcclxuXHRcdFx0XHRmaWx0ZXJbbmFtZV0gPSB2YWx1ZTsgIFxyXG5cdFx0XHR9XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdGZvciAobGV0IGVudHJ5IGluIGZpbHRlcikge1xyXG5cdFx0XHRmb3IgKGxldCBjb2x1bW4gb2YgZGF0YS5jb2x1bW5zKSB7IFxyXG5cdFx0XHRcdGlmIChlbnRyeSA9PT0gY29sdW1uLm5hbWUgJiYgZmlsdGVyW2VudHJ5XS5jb25zdHJ1Y3RvciA9PT0gQXJyYXkpIHtcclxuXHRcdFx0XHRcdGNvbHVtbi5zZWFyY2gudmFsdWUgPSBmaWx0ZXJbZW50cnldOyBcclxuXHRcdFx0XHRcdGJyZWFrOyBcclxuXHRcdFx0XHR9XHJcblx0XHRcdH1cclxuXHRcdH1cclxuXHR9XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSU5JVElBTElaQVRJT05cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdC8vIEFkZCBwdWJsaWMgbW9kdWxlIG1ldGhvZC4gXHJcblx0XHRfYWRkUHVibGljTWV0aG9kKCk7XHJcblx0XHRcclxuXHRcdC8vIFBhcnNlIGZpbHRlcmluZyBHRVQgcGFyYW1ldGVycy4gXHJcblx0XHRfcGFyc2VGaWx0ZXJpbmdQYXJhbWV0ZXJzKCk7XHJcblx0XHRcclxuXHRcdC8vIEJpbmQgZXZlbnQgaGFuZGxlcnMuXHJcblx0XHQkZmlsdGVyXHJcblx0XHRcdC5vbigna2V5dXAnLCAnaW5wdXQ6dGV4dCcsIF9vbklucHV0VGV4dEtleVVwKVxyXG5cdFx0XHQub24oJ2NsaWNrJywgJy5hcHBseS1maWx0ZXJzJywgX29uQXBwbHlGaWx0ZXJzQ2xpY2spXHJcblx0XHRcdC5vbignY2xpY2snLCAnLnJlc2V0LWZpbHRlcnMnLCBfb25SZXNldEZpbHRlcnNDbGljayk7XHJcblx0XHRcclxuXHRcdCR0aGlzLm9uKCdwcmVYaHIuZHQnLCBfbm9ybWFsaXplQXJyYXlWYWx1ZXMpOyBcclxuXHRcdFxyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxufSk7Il19
