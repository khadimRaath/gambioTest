'use strict';

/* --------------------------------------------------------------
 state.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Handles the table state for filtering, pagination and sorting.
 *
 * This controller will update the window history with the current state of the table. It reacts
 * to specific events such as filtering, pagination and sorting changes. After the window history
 * is updated the user will be able to navigate forth or backwards.
 *
 * Notice #1: This module must handle the window's pop-state events and not other modules because
 * this will lead to unnecessary code duplication and multiple AJAX requests.
 *
 * Notice #1: The window state must be always in sync with the URL for easier manipulation.
 */
gx.controllers.module('state', [], function (data) {

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

	/**
  * Window History Support
  *
  * @type {Boolean}
  */
	var historySupport = jse.core.config.get('history');

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Get parsed state from the URL GET parameters. 
  * 
  * @return {Object} Returns the table state.
  */
	function _getState() {
		return $.deparam(window.location.search.slice(1));
	}

	/**
  * Set the state to the browser's history.
  * 
  * The state is stored for enabling back and forth navigation from the browser. 
  * 
  * @param {Object} state Contains the new table state. 
  */
	function _setState(state) {
		var url = window.location.origin + window.location.pathname + '?' + $.param(state);
		window.history.pushState(state, '', url);
	}

	/**
  * Update page navigation state. 
  * 
  * @param {jQuery.Event} event jQuery event object.
  * @param {Object} pagination Contains the DataTable pagination info.
  */
	function _onPageChange(event, pagination) {
		var state = _getState();

		state.page = pagination.page + 1;

		_setState(state);
	}

	/**
  * Update page length state. 
  * 
  * @param {jQuery.Event} event jQuery event object.
  * @param {Number} length New page length.
  */
	function _onLengthChange(event, length) {
		var state = _getState();

		state.page = 1;
		state.length = length;

		_setState(state);
	}

	/**
  * Update filter state.
  * 
  * @param {jQuery.Event} event jQuery event object.
  * @param {Object} filter Contains the filtering values.
  */
	function _onFilterChange(event, filter) {
		var state = _getState();

		state.page = 1;
		state.filter = filter;

		_setState(state);
	}

	/**
  * Update sort state. 
  * 
  * @param {jQuery.Event} event jQuery event object.
  * @param {Object} sort Contains column sorting info {index, name, direction}. 
  */
	function _onSortChange(event, sort) {
		var state = _getState();

		state.sort = (sort.direction === 'desc' ? '-' : '+') + sort.name;

		_setState(state);
	}

	/**
  * Set the correct table state. 
  * 
  * This method will parse the new popped state and apply it on the table. It must be the only place where this 
  * happens in order to avoid multiple AJAX requests and data collisions. 
  * 
  * @param {jQuery.Event} event
  */
	function _onWindowPopState(event) {
		var state = event.originalEvent.state || {};

		if (state.page) {
			$this.find('.page-navigation select').val(state.page);
			$this.DataTable().page(parseInt(state.page) - 1);
		}

		if (state.length) {
			$this.find('.page-length select').val(state.length);
			$this.DataTable().page.len(parseInt(state.length));
		}

		if (state.sort) {
			var _$this$DataTable$init = $this.DataTable().init(),
			    columns = _$this$DataTable$init.columns;

			var direction = state.sort.charAt(0) === '-' ? 'desc' : 'asc';
			var name = state.sort.slice(1);
			var index = 1; // Default Value

			var _iteratorNormalCompletion = true;
			var _didIteratorError = false;
			var _iteratorError = undefined;

			try {
				for (var _iterator = columns[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
					var column = _step.value;

					if (column.name === name) {
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

			$this.DataTable().order([index, direction]);
		}

		if (state.filter) {
			// Update the filtering input elements. 
			for (var _column in state.filter) {
				var value = state.filter[_column];

				if (value.constructor === Array) {
					value = value.join('||'); // Join arrays into a single string.
				}

				$this.DataTable().column(_column + ':name').search(value);
			}
		}

		$this.DataTable().draw(false);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		if (historySupport) {
			$this.on('datatable_custom_pagination:page_change', _onPageChange).on('datatable_custom_pagination:length_change', _onLengthChange).on('datatable_custom_sorting:change', _onSortChange).on('orders_overview_filter:change', _onFilterChange);

			$(window).on('popstate', _onWindowPopState);
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm9yZGVycy9vdmVydmlldy9zdGF0ZS5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImhpc3RvcnlTdXBwb3J0IiwianNlIiwiY29yZSIsImNvbmZpZyIsImdldCIsIl9nZXRTdGF0ZSIsImRlcGFyYW0iLCJ3aW5kb3ciLCJsb2NhdGlvbiIsInNlYXJjaCIsInNsaWNlIiwiX3NldFN0YXRlIiwic3RhdGUiLCJ1cmwiLCJvcmlnaW4iLCJwYXRobmFtZSIsInBhcmFtIiwiaGlzdG9yeSIsInB1c2hTdGF0ZSIsIl9vblBhZ2VDaGFuZ2UiLCJldmVudCIsInBhZ2luYXRpb24iLCJwYWdlIiwiX29uTGVuZ3RoQ2hhbmdlIiwibGVuZ3RoIiwiX29uRmlsdGVyQ2hhbmdlIiwiZmlsdGVyIiwiX29uU29ydENoYW5nZSIsInNvcnQiLCJkaXJlY3Rpb24iLCJuYW1lIiwiX29uV2luZG93UG9wU3RhdGUiLCJvcmlnaW5hbEV2ZW50IiwiZmluZCIsInZhbCIsIkRhdGFUYWJsZSIsInBhcnNlSW50IiwibGVuIiwiaW5pdCIsImNvbHVtbnMiLCJjaGFyQXQiLCJpbmRleCIsImNvbHVtbiIsImluZGV4T2YiLCJvcmRlciIsInZhbHVlIiwiY29uc3RydWN0b3IiLCJBcnJheSIsImpvaW4iLCJkcmF3IiwiZG9uZSIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7Ozs7OztBQVlBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsT0FBdEIsRUFBK0IsRUFBL0IsRUFBbUMsVUFBU0MsSUFBVCxFQUFlOztBQUVqRDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFFQTs7Ozs7QUFLQSxLQUFNSSxpQkFBaUJDLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FB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RUFJc0MxQixlQUp0Qzs7QUFNQTFCLEtBQUVRLE1BQUYsRUFDRTRDLEVBREYsQ0FDSyxVQURMLEVBQ2lCcEIsaUJBRGpCO0FBRUE7O0FBRURtQjtBQUNBLEVBYkQ7O0FBZUEsUUFBT3RELE1BQVA7QUFFQSxDQTNMRCIsImZpbGUiOiJvcmRlcnMvb3ZlcnZpZXcvc3RhdGUuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gc3RhdGUuanMgMjAxNi0wNi0yMFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBIYW5kbGVzIHRoZSB0YWJsZSBzdGF0ZSBmb3IgZmlsdGVyaW5nLCBwYWdpbmF0aW9uIGFuZCBzb3J0aW5nLlxyXG4gKlxyXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCB1cGRhdGUgdGhlIHdpbmRvdyBoaXN0b3J5IHdpdGggdGhlIGN1cnJlbnQgc3RhdGUgb2YgdGhlIHRhYmxlLiBJdCByZWFjdHNcclxuICogdG8gc3BlY2lmaWMgZXZlbnRzIHN1Y2ggYXMgZmlsdGVyaW5nLCBwYWdpbmF0aW9uIGFuZCBzb3J0aW5nIGNoYW5nZXMuIEFmdGVyIHRoZSB3aW5kb3cgaGlzdG9yeVxyXG4gKiBpcyB1cGRhdGVkIHRoZSB1c2VyIHdpbGwgYmUgYWJsZSB0byBuYXZpZ2F0ZSBmb3J0aCBvciBiYWNrd2FyZHMuXHJcbiAqXHJcbiAqIE5vdGljZSAjMTogVGhpcyBtb2R1bGUgbXVzdCBoYW5kbGUgdGhlIHdpbmRvdydzIHBvcC1zdGF0ZSBldmVudHMgYW5kIG5vdCBvdGhlciBtb2R1bGVzIGJlY2F1c2VcclxuICogdGhpcyB3aWxsIGxlYWQgdG8gdW5uZWNlc3NhcnkgY29kZSBkdXBsaWNhdGlvbiBhbmQgbXVsdGlwbGUgQUpBWCByZXF1ZXN0cy5cclxuICpcclxuICogTm90aWNlICMxOiBUaGUgd2luZG93IHN0YXRlIG11c3QgYmUgYWx3YXlzIGluIHN5bmMgd2l0aCB0aGUgVVJMIGZvciBlYXNpZXIgbWFuaXB1bGF0aW9uLlxyXG4gKi9cclxuZ3guY29udHJvbGxlcnMubW9kdWxlKCdzdGF0ZScsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFdpbmRvdyBIaXN0b3J5IFN1cHBvcnRcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtCb29sZWFufVxyXG5cdCAqL1xyXG5cdGNvbnN0IGhpc3RvcnlTdXBwb3J0ID0ganNlLmNvcmUuY29uZmlnLmdldCgnaGlzdG9yeScpO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIEdldCBwYXJzZWQgc3RhdGUgZnJvbSB0aGUgVVJMIEdFVCBwYXJhbWV0ZXJzLiBcclxuXHQgKiBcclxuXHQgKiBAcmV0dXJuIHtPYmplY3R9IFJldHVybnMgdGhlIHRhYmxlIHN0YXRlLlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9nZXRTdGF0ZSgpIHtcclxuXHRcdHJldHVybiAkLmRlcGFyYW0od2luZG93LmxvY2F0aW9uLnNlYXJjaC5zbGljZSgxKSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFNldCB0aGUgc3RhdGUgdG8gdGhlIGJyb3dzZXIncyBoaXN0b3J5LlxyXG5cdCAqIFxyXG5cdCAqIFRoZSBzdGF0ZSBpcyBzdG9yZWQgZm9yIGVuYWJsaW5nIGJhY2sgYW5kIGZvcnRoIG5hdmlnYXRpb24gZnJvbSB0aGUgYnJvd3Nlci4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtPYmplY3R9IHN0YXRlIENvbnRhaW5zIHRoZSBuZXcgdGFibGUgc3RhdGUuIFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9zZXRTdGF0ZShzdGF0ZSkge1xyXG5cdFx0Y29uc3QgdXJsID0gd2luZG93LmxvY2F0aW9uLm9yaWdpbiArIHdpbmRvdy5sb2NhdGlvbi5wYXRobmFtZSArICc/JyArICQucGFyYW0oc3RhdGUpO1xyXG5cdFx0d2luZG93Lmhpc3RvcnkucHVzaFN0YXRlKHN0YXRlLCAnJywgdXJsKTtcclxuXHR9XHJcblx0XHJcblx0LyoqXHJcblx0ICogVXBkYXRlIHBhZ2UgbmF2aWdhdGlvbiBzdGF0ZS4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QuXHJcblx0ICogQHBhcmFtIHtPYmplY3R9IHBhZ2luYXRpb24gQ29udGFpbnMgdGhlIERhdGFUYWJsZSBwYWdpbmF0aW9uIGluZm8uXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uUGFnZUNoYW5nZShldmVudCwgcGFnaW5hdGlvbikge1xyXG5cdFx0Y29uc3Qgc3RhdGUgPSBfZ2V0U3RhdGUoKTtcclxuXHRcdFxyXG5cdFx0c3RhdGUucGFnZSA9IHBhZ2luYXRpb24ucGFnZSArIDE7XHJcblx0XHRcclxuXHRcdF9zZXRTdGF0ZShzdGF0ZSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFVwZGF0ZSBwYWdlIGxlbmd0aCBzdGF0ZS4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QuXHJcblx0ICogQHBhcmFtIHtOdW1iZXJ9IGxlbmd0aCBOZXcgcGFnZSBsZW5ndGguXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uTGVuZ3RoQ2hhbmdlKGV2ZW50LCBsZW5ndGgpIHtcclxuXHRcdGNvbnN0IHN0YXRlID0gX2dldFN0YXRlKCk7IFxyXG5cdFx0XHJcblx0XHRzdGF0ZS5wYWdlID0gMTsgXHJcblx0XHRzdGF0ZS5sZW5ndGggPSBsZW5ndGg7IFxyXG5cdFx0XHJcblx0XHRfc2V0U3RhdGUoc3RhdGUpO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBVcGRhdGUgZmlsdGVyIHN0YXRlLlxyXG5cdCAqIFxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudCBqUXVlcnkgZXZlbnQgb2JqZWN0LlxyXG5cdCAqIEBwYXJhbSB7T2JqZWN0fSBmaWx0ZXIgQ29udGFpbnMgdGhlIGZpbHRlcmluZyB2YWx1ZXMuXHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX29uRmlsdGVyQ2hhbmdlKGV2ZW50LCBmaWx0ZXIpIHtcclxuXHRcdGNvbnN0IHN0YXRlID0gX2dldFN0YXRlKCk7XHJcblx0XHRcclxuXHRcdHN0YXRlLnBhZ2UgPSAxO1xyXG5cdFx0c3RhdGUuZmlsdGVyID0gZmlsdGVyO1xyXG5cdFx0XHJcblx0XHRfc2V0U3RhdGUoc3RhdGUpO1xyXG5cdH1cclxuXHRcclxuXHQvKipcclxuXHQgKiBVcGRhdGUgc29ydCBzdGF0ZS4gXHJcblx0ICogXHJcblx0ICogQHBhcmFtIHtqUXVlcnkuRXZlbnR9IGV2ZW50IGpRdWVyeSBldmVudCBvYmplY3QuXHJcblx0ICogQHBhcmFtIHtPYmplY3R9IHNvcnQgQ29udGFpbnMgY29sdW1uIHNvcnRpbmcgaW5mbyB7aW5kZXgsIG5hbWUsIGRpcmVjdGlvbn0uIFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vblNvcnRDaGFuZ2UoZXZlbnQsIHNvcnQpIHtcclxuXHRcdGNvbnN0IHN0YXRlID0gX2dldFN0YXRlKCk7XHJcblx0XHRcclxuXHRcdHN0YXRlLnNvcnQgPSAoc29ydC5kaXJlY3Rpb24gPT09ICdkZXNjJyA/ICctJyA6ICcrJykgKyBzb3J0Lm5hbWU7XHJcblx0XHRcclxuXHRcdF9zZXRTdGF0ZShzdGF0ZSk7XHJcblx0fVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIFNldCB0aGUgY29ycmVjdCB0YWJsZSBzdGF0ZS4gXHJcblx0ICogXHJcblx0ICogVGhpcyBtZXRob2Qgd2lsbCBwYXJzZSB0aGUgbmV3IHBvcHBlZCBzdGF0ZSBhbmQgYXBwbHkgaXQgb24gdGhlIHRhYmxlLiBJdCBtdXN0IGJlIHRoZSBvbmx5IHBsYWNlIHdoZXJlIHRoaXMgXHJcblx0ICogaGFwcGVucyBpbiBvcmRlciB0byBhdm9pZCBtdWx0aXBsZSBBSkFYIHJlcXVlc3RzIGFuZCBkYXRhIGNvbGxpc2lvbnMuIFxyXG5cdCAqIFxyXG5cdCAqIEBwYXJhbSB7alF1ZXJ5LkV2ZW50fSBldmVudFxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9vbldpbmRvd1BvcFN0YXRlKGV2ZW50KSB7XHJcblx0XHRjb25zdCBzdGF0ZSA9IGV2ZW50Lm9yaWdpbmFsRXZlbnQuc3RhdGUgfHwge307XHJcblx0XHRcclxuXHRcdGlmIChzdGF0ZS5wYWdlKSB7XHJcblx0XHRcdCR0aGlzLmZpbmQoJy5wYWdlLW5hdmlnYXRpb24gc2VsZWN0JykudmFsKHN0YXRlLnBhZ2UpOyBcclxuXHRcdFx0JHRoaXMuRGF0YVRhYmxlKCkucGFnZShwYXJzZUludChzdGF0ZS5wYWdlKSAtIDEpO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRpZiAoc3RhdGUubGVuZ3RoKSB7XHJcblx0XHRcdCR0aGlzLmZpbmQoJy5wYWdlLWxlbmd0aCBzZWxlY3QnKS52YWwoc3RhdGUubGVuZ3RoKTtcclxuXHRcdFx0JHRoaXMuRGF0YVRhYmxlKCkucGFnZS5sZW4ocGFyc2VJbnQoc3RhdGUubGVuZ3RoKSk7IFxyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRpZiAoc3RhdGUuc29ydCkge1xyXG5cdFx0XHRjb25zdCB7Y29sdW1uc30gPSAkdGhpcy5EYXRhVGFibGUoKS5pbml0KCk7XHJcblx0XHRcdGNvbnN0IGRpcmVjdGlvbiA9IHN0YXRlLnNvcnQuY2hhckF0KDApID09PSAnLScgPyAnZGVzYycgOiAnYXNjJztcclxuXHRcdFx0Y29uc3QgbmFtZSA9IHN0YXRlLnNvcnQuc2xpY2UoMSk7XHJcblx0XHRcdGxldCBpbmRleCA9IDE7IC8vIERlZmF1bHQgVmFsdWVcclxuXHRcdFx0XHJcblx0XHRcdGZvciAobGV0IGNvbHVtbiBvZiBjb2x1bW5zKSB7XHJcblx0XHRcdFx0aWYgKGNvbHVtbi5uYW1lID09PSBuYW1lKSB7XHJcblx0XHRcdFx0XHRpbmRleCA9IGNvbHVtbnMuaW5kZXhPZihjb2x1bW4pO1xyXG5cdFx0XHRcdFx0YnJlYWs7XHJcblx0XHRcdFx0fVxyXG5cdFx0XHR9XHJcblx0XHRcdFxyXG5cdFx0XHQkdGhpcy5EYXRhVGFibGUoKS5vcmRlcihbaW5kZXgsIGRpcmVjdGlvbl0pO1xyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHRpZiAoc3RhdGUuZmlsdGVyKSB7XHJcblx0XHRcdC8vIFVwZGF0ZSB0aGUgZmlsdGVyaW5nIGlucHV0IGVsZW1lbnRzLiBcclxuXHRcdFx0Zm9yIChsZXQgY29sdW1uIGluIHN0YXRlLmZpbHRlcikge1xyXG5cdFx0XHRcdGxldCB2YWx1ZSA9IHN0YXRlLmZpbHRlcltjb2x1bW5dO1xyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdGlmICh2YWx1ZS5jb25zdHJ1Y3RvciA9PT0gQXJyYXkpIHtcclxuXHRcdFx0XHRcdHZhbHVlID0gdmFsdWUuam9pbignfHwnKTsgLy8gSm9pbiBhcnJheXMgaW50byBhIHNpbmdsZSBzdHJpbmcuXHJcblx0XHRcdFx0fVxyXG5cdFx0XHRcdFxyXG5cdFx0XHRcdCR0aGlzLkRhdGFUYWJsZSgpLmNvbHVtbihgJHtjb2x1bW59Om5hbWVgKS5zZWFyY2godmFsdWUpOyBcclxuXHRcdFx0fVxyXG5cdFx0fVxyXG5cdFx0XHJcblx0XHQkdGhpcy5EYXRhVGFibGUoKS5kcmF3KGZhbHNlKTtcclxuXHRcdFxyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0aWYgKGhpc3RvcnlTdXBwb3J0KSB7XHJcblx0XHRcdCR0aGlzXHJcblx0XHRcdFx0Lm9uKCdkYXRhdGFibGVfY3VzdG9tX3BhZ2luYXRpb246cGFnZV9jaGFuZ2UnLCBfb25QYWdlQ2hhbmdlKVxyXG5cdFx0XHRcdC5vbignZGF0YXRhYmxlX2N1c3RvbV9wYWdpbmF0aW9uOmxlbmd0aF9jaGFuZ2UnLCBfb25MZW5ndGhDaGFuZ2UpXHJcblx0XHRcdFx0Lm9uKCdkYXRhdGFibGVfY3VzdG9tX3NvcnRpbmc6Y2hhbmdlJywgX29uU29ydENoYW5nZSlcclxuXHRcdFx0XHQub24oJ29yZGVyc19vdmVydmlld19maWx0ZXI6Y2hhbmdlJywgX29uRmlsdGVyQ2hhbmdlKTtcclxuXHRcdFx0XHJcblx0XHRcdCQod2luZG93KVxyXG5cdFx0XHRcdC5vbigncG9wc3RhdGUnLCBfb25XaW5kb3dQb3BTdGF0ZSk7XHJcblx0XHR9XHJcblx0XHRcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcblx0XHJcbn0pOyJdfQ==
