'use strict';

/* --------------------------------------------------------------
 datatable_custom_sorting.js 2016-06-20
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Enable Custom DataTable Sorting
 *
 * DataTables will reset the table to the first page after sorting by default. As there is no way to override
 * this behavior, this module will remove the DataTable sorting event handlers and set its own, which will keep
 * the table to the current page. This module will also set a sort parameter to the URL on sorting change but will
 * not parse it during initialization. This must happen from the module that initializes the table.
 *
 * Important: This method will remove the click event from the "th.sorting" elements, so bind extra "click" events
 * after enabling the custom-sorting extension (on init.dt event).
 * 
 * ### Events
 * 
 * ```javascript
 * // Add custom callback once the column sorting was changed (the "info" object contains the column index,  
 * // column name and sort direction: {index, name, direction}).
 * $('#datatable-instance').on('datatable_custom_sorting:change', function(event, info) {...}); 
 * ```
 *
 * @module Admin/Extensions/datatable_custom_sorting
 */
gx.extensions.module('datatable_custom_sorting', [], function (data) {

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
  * On Table Header Cell Click
  *
  * Perform the table sorting without changing the current page.
  */
	function _onTableHeaderCellClick() {
		// Change Table Order
		var index = $(this).index();
		var destination = $(this).hasClass('sorting_asc') ? 'desc' : 'asc';

		$this.DataTable().order([index, destination]).draw(false);

		// Trigger Event 
		var order = $this.DataTable().order()[0];

		var _$this$DataTable$init = $this.DataTable().init(),
		    columns = _$this$DataTable$init.columns;

		var info = {
			index: order[0],
			name: columns[order[0]].name,
			direction: order[1]
		};

		$this.trigger('datatable_custom_sorting:change', [info]);
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('preInit.dt', function () {
			$this.find('thead tr:first th.sorting').off('click').on('click', _onTableHeaderCellClick);
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9jdXN0b21fc29ydGluZy5qcyJdLCJuYW1lcyI6WyJneCIsImV4dGVuc2lvbnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiX29uVGFibGVIZWFkZXJDZWxsQ2xpY2siLCJpbmRleCIsImRlc3RpbmF0aW9uIiwiaGFzQ2xhc3MiLCJEYXRhVGFibGUiLCJvcmRlciIsImRyYXciLCJpbml0IiwiY29sdW1ucyIsImluZm8iLCJuYW1lIiwiZGlyZWN0aW9uIiwidHJpZ2dlciIsImRvbmUiLCJvbiIsImZpbmQiLCJvZmYiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBcUJBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FBcUIsMEJBQXJCLEVBQWlELEVBQWpELEVBQXFELFVBQVNDLElBQVQsRUFBZTs7QUFFbkU7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7Ozs7QUFLQSxLQUFNQyxRQUFRQyxFQUFFLElBQUYsQ0FBZDs7QUFFQTs7Ozs7QUFLQSxLQUFNSCxTQUFTLEVBQWY7O0FBRUE7QUFDQTtBQUNBOztBQUVBOzs7OztBQUtBLFVBQVNJLHVCQUFULEdBQW1DO0FBQ2xDO0FBQ0EsTUFBTUMsUUFBUUYsRUFBRSxJQUFGLEVBQVFFLEtBQVIsRUFBZDtBQUNBLE1BQU1DLGNBQWNILEVBQUUsSUFBRixFQUFRSSxRQUFSLENBQWlCLGFBQWpCLElBQWtDLE1BQWxDLEdBQTJDLEtBQS9EOztBQUVBTCxRQUFNTSxTQUFOLEdBQWtCQyxLQUFsQixDQUF3QixDQUFDSixLQUFELEVBQVFDLFdBQVIsQ0FBeEIsRUFBOENJLElBQTlDLENBQW1ELEtBQW5EOztBQUVBO0FBQ0EsTUFBTUQsUUFBUVAsTUFBTU0sU0FBTixHQUFrQkMsS0FBbEIsR0FBMEIsQ0FBMUIsQ0FBZDs7QUFSa0MsOEJBU2hCUCxNQUFNTSxTQUFOLEdBQWtCRyxJQUFsQixFQVRnQjtBQUFBLE1BUzNCQyxPQVQyQix5QkFTM0JBLE9BVDJCOztBQVVsQyxNQUFNQyxPQUFPO0FBQ1pSLFVBQU9JLE1BQU0sQ0FBTixDQURLO0FBRVpLLFNBQU1GLFFBQVFILE1BQU0sQ0FBTixDQUFSLEVBQWtCSyxJQUZaO0FBR1pDLGNBQVdOLE1BQU0sQ0FBTjtBQUhDLEdBQWI7O0FBTUFQLFFBQU1jLE9BQU4sQ0FBYyxpQ0FBZCxFQUFpRCxDQUFDSCxJQUFELENBQWpEO0FBQ0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBYixRQUFPVyxJQUFQLEdBQWMsVUFBU00sSUFBVCxFQUFlO0FBQzVCZixRQUFNZ0IsRUFBTixDQUFTLFlBQVQsRUFBdUIsWUFBTTtBQUM1QmhCLFNBQU1pQixJQUFOLENBQVcsMkJBQVgsRUFDRUMsR0FERixDQUNNLE9BRE4sRUFFRUYsRUFGRixDQUVLLE9BRkwsRUFFY2QsdUJBRmQ7QUFHQSxHQUpEOztBQU1BYTtBQUNBLEVBUkQ7O0FBVUEsUUFBT2pCLE1BQVA7QUFFQSxDQWxFRCIsImZpbGUiOiJkYXRhdGFibGVfY3VzdG9tX3NvcnRpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gZGF0YXRhYmxlX2N1c3RvbV9zb3J0aW5nLmpzIDIwMTYtMDYtMjBcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgRW5hYmxlIEN1c3RvbSBEYXRhVGFibGUgU29ydGluZ1xyXG4gKlxyXG4gKiBEYXRhVGFibGVzIHdpbGwgcmVzZXQgdGhlIHRhYmxlIHRvIHRoZSBmaXJzdCBwYWdlIGFmdGVyIHNvcnRpbmcgYnkgZGVmYXVsdC4gQXMgdGhlcmUgaXMgbm8gd2F5IHRvIG92ZXJyaWRlXHJcbiAqIHRoaXMgYmVoYXZpb3IsIHRoaXMgbW9kdWxlIHdpbGwgcmVtb3ZlIHRoZSBEYXRhVGFibGUgc29ydGluZyBldmVudCBoYW5kbGVycyBhbmQgc2V0IGl0cyBvd24sIHdoaWNoIHdpbGwga2VlcFxyXG4gKiB0aGUgdGFibGUgdG8gdGhlIGN1cnJlbnQgcGFnZS4gVGhpcyBtb2R1bGUgd2lsbCBhbHNvIHNldCBhIHNvcnQgcGFyYW1ldGVyIHRvIHRoZSBVUkwgb24gc29ydGluZyBjaGFuZ2UgYnV0IHdpbGxcclxuICogbm90IHBhcnNlIGl0IGR1cmluZyBpbml0aWFsaXphdGlvbi4gVGhpcyBtdXN0IGhhcHBlbiBmcm9tIHRoZSBtb2R1bGUgdGhhdCBpbml0aWFsaXplcyB0aGUgdGFibGUuXHJcbiAqXHJcbiAqIEltcG9ydGFudDogVGhpcyBtZXRob2Qgd2lsbCByZW1vdmUgdGhlIGNsaWNrIGV2ZW50IGZyb20gdGhlIFwidGguc29ydGluZ1wiIGVsZW1lbnRzLCBzbyBiaW5kIGV4dHJhIFwiY2xpY2tcIiBldmVudHNcclxuICogYWZ0ZXIgZW5hYmxpbmcgdGhlIGN1c3RvbS1zb3J0aW5nIGV4dGVuc2lvbiAob24gaW5pdC5kdCBldmVudCkuXHJcbiAqIFxyXG4gKiAjIyMgRXZlbnRzXHJcbiAqIFxyXG4gKiBgYGBqYXZhc2NyaXB0XHJcbiAqIC8vIEFkZCBjdXN0b20gY2FsbGJhY2sgb25jZSB0aGUgY29sdW1uIHNvcnRpbmcgd2FzIGNoYW5nZWQgKHRoZSBcImluZm9cIiBvYmplY3QgY29udGFpbnMgdGhlIGNvbHVtbiBpbmRleCwgIFxyXG4gKiAvLyBjb2x1bW4gbmFtZSBhbmQgc29ydCBkaXJlY3Rpb246IHtpbmRleCwgbmFtZSwgZGlyZWN0aW9ufSkuXHJcbiAqICQoJyNkYXRhdGFibGUtaW5zdGFuY2UnKS5vbignZGF0YXRhYmxlX2N1c3RvbV9zb3J0aW5nOmNoYW5nZScsIGZ1bmN0aW9uKGV2ZW50LCBpbmZvKSB7Li4ufSk7IFxyXG4gKiBgYGBcclxuICpcclxuICogQG1vZHVsZSBBZG1pbi9FeHRlbnNpb25zL2RhdGF0YWJsZV9jdXN0b21fc29ydGluZ1xyXG4gKi9cclxuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoJ2RhdGF0YWJsZV9jdXN0b21fc29ydGluZycsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE9uIFRhYmxlIEhlYWRlciBDZWxsIENsaWNrXHJcblx0ICpcclxuXHQgKiBQZXJmb3JtIHRoZSB0YWJsZSBzb3J0aW5nIHdpdGhvdXQgY2hhbmdpbmcgdGhlIGN1cnJlbnQgcGFnZS5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfb25UYWJsZUhlYWRlckNlbGxDbGljaygpIHtcclxuXHRcdC8vIENoYW5nZSBUYWJsZSBPcmRlclxyXG5cdFx0Y29uc3QgaW5kZXggPSAkKHRoaXMpLmluZGV4KCk7XHJcblx0XHRjb25zdCBkZXN0aW5hdGlvbiA9ICQodGhpcykuaGFzQ2xhc3MoJ3NvcnRpbmdfYXNjJykgPyAnZGVzYycgOiAnYXNjJztcclxuXHRcdFxyXG5cdFx0JHRoaXMuRGF0YVRhYmxlKCkub3JkZXIoW2luZGV4LCBkZXN0aW5hdGlvbl0pLmRyYXcoZmFsc2UpO1xyXG5cdFx0XHJcblx0XHQvLyBUcmlnZ2VyIEV2ZW50IFxyXG5cdFx0Y29uc3Qgb3JkZXIgPSAkdGhpcy5EYXRhVGFibGUoKS5vcmRlcigpWzBdO1xyXG5cdFx0Y29uc3Qge2NvbHVtbnN9ID0gJHRoaXMuRGF0YVRhYmxlKCkuaW5pdCgpO1xyXG5cdFx0Y29uc3QgaW5mbyA9IHtcclxuXHRcdFx0aW5kZXg6IG9yZGVyWzBdLFxyXG5cdFx0XHRuYW1lOiBjb2x1bW5zW29yZGVyWzBdXS5uYW1lLFxyXG5cdFx0XHRkaXJlY3Rpb246IG9yZGVyWzFdXHJcblx0XHR9O1xyXG5cdFx0XHJcblx0XHQkdGhpcy50cmlnZ2VyKCdkYXRhdGFibGVfY3VzdG9tX3NvcnRpbmc6Y2hhbmdlJywgW2luZm9dKTtcclxuXHR9XHJcblx0XHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0Ly8gSU5JVElBTElaQVRJT05cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdCR0aGlzLm9uKCdwcmVJbml0LmR0JywgKCkgPT4ge1xyXG5cdFx0XHQkdGhpcy5maW5kKCd0aGVhZCB0cjpmaXJzdCB0aC5zb3J0aW5nJylcclxuXHRcdFx0XHQub2ZmKCdjbGljaycpXHJcblx0XHRcdFx0Lm9uKCdjbGljaycsIF9vblRhYmxlSGVhZGVyQ2VsbENsaWNrKTtcclxuXHRcdH0pO1xyXG5cdFx0XHJcblx0XHRkb25lKCk7XHJcblx0fTtcclxuXHRcclxuXHRyZXR1cm4gbW9kdWxlO1xyXG5cdFxyXG59KTsgXHJcbiJdfQ==
