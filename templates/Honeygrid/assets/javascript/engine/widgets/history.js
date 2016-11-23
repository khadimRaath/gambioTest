'use strict';

/* --------------------------------------------------------------
 history.js 2015-07-22 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Simple component that adds browser history-functionality
 * to elements (back, forward & refresh)
 */
gambio.widgets.module('history', [], function (data) {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    defaults = {},
	    options = $.extend(true, {}, defaults, data),
	    module = {};

	// ########## EVENT HANDLER ##########

	/**
  * Event handler that executes the browser
  * history functionality depending on the
  * given data
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _navigate = function _navigate(e) {
		e.preventDefault();

		history.go(e.data.step);
	};

	/**
  * Event handler that executes the browser
  * refresh functionality
  * @param       {object}    e       jQuery event object
  * @private
  */
	var _refresh = function _refresh(e) {
		e.preventDefault();

		location.reload();
	};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.on('click', '.history-back', { step: -1 }, _navigate).on('click', '.history-forward', { step: 1 }, _navigate).on('click', '.history-refresh', _refresh);

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaGlzdG9yeS5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsIl9uYXZpZ2F0ZSIsImUiLCJwcmV2ZW50RGVmYXVsdCIsImhpc3RvcnkiLCJnbyIsInN0ZXAiLCJfcmVmcmVzaCIsImxvY2F0aW9uIiwicmVsb2FkIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7O0FBSUFBLE9BQU9DLE9BQVAsQ0FBZUMsTUFBZixDQUFzQixTQUF0QixFQUFpQyxFQUFqQyxFQUFxQyxVQUFTQyxJQUFULEVBQWU7O0FBRW5EOztBQUVEOztBQUVDLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0MsV0FBVyxFQURaO0FBQUEsS0FFQ0MsVUFBVUYsRUFBRUcsTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CRixRQUFuQixFQUE2QkgsSUFBN0IsQ0FGWDtBQUFBLEtBR0NELFNBQVMsRUFIVjs7QUFLRDs7QUFFQzs7Ozs7OztBQU9BLEtBQUlPLFlBQVksU0FBWkEsU0FBWSxDQUFTQyxDQUFULEVBQVk7QUFDM0JBLElBQUVDLGNBQUY7O0FBRUFDLFVBQVFDLEVBQVIsQ0FBV0gsRUFBRVAsSUFBRixDQUFPVyxJQUFsQjtBQUNBLEVBSkQ7O0FBTUE7Ozs7OztBQU1BLEtBQUlDLFdBQVcsU0FBWEEsUUFBVyxDQUFTTCxDQUFULEVBQVk7QUFDMUJBLElBQUVDLGNBQUY7O0FBRUFLLFdBQVNDLE1BQVQ7QUFDQSxFQUpEOztBQU1EOztBQUVDOzs7O0FBSUFmLFFBQU9nQixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1QmYsUUFDRWdCLEVBREYsQ0FDSyxPQURMLEVBQ2MsZUFEZCxFQUMrQixFQUFDTixNQUFNLENBQUMsQ0FBUixFQUQvQixFQUMyQ0wsU0FEM0MsRUFFRVcsRUFGRixDQUVLLE9BRkwsRUFFYyxrQkFGZCxFQUVrQyxFQUFDTixNQUFNLENBQVAsRUFGbEMsRUFFNkNMLFNBRjdDLEVBR0VXLEVBSEYsQ0FHSyxPQUhMLEVBR2Msa0JBSGQsRUFHa0NMLFFBSGxDOztBQUtBSTtBQUNBLEVBUkQ7O0FBVUE7QUFDQSxRQUFPakIsTUFBUDtBQUNBLENBeEREIiwiZmlsZSI6IndpZGdldHMvaGlzdG9yeS5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gaGlzdG9yeS5qcyAyMDE1LTA3LTIyIGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiBTaW1wbGUgY29tcG9uZW50IHRoYXQgYWRkcyBicm93c2VyIGhpc3RvcnktZnVuY3Rpb25hbGl0eVxuICogdG8gZWxlbWVudHMgKGJhY2ssIGZvcndhcmQgJiByZWZyZXNoKVxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2hpc3RvcnknLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xuXG5cdCd1c2Ugc3RyaWN0JztcblxuLy8gIyMjIyMjIyMjIyBWQVJJQUJMRSBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0dmFyICR0aGlzID0gJCh0aGlzKSxcblx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdG1vZHVsZSA9IHt9O1xuXG4vLyAjIyMjIyMjIyMjIEVWRU5UIEhBTkRMRVIgIyMjIyMjIyMjI1xuXG5cdC8qKlxuXHQgKiBFdmVudCBoYW5kbGVyIHRoYXQgZXhlY3V0ZXMgdGhlIGJyb3dzZXJcblx0ICogaGlzdG9yeSBmdW5jdGlvbmFsaXR5IGRlcGVuZGluZyBvbiB0aGVcblx0ICogZ2l2ZW4gZGF0YVxuXHQgKiBAcGFyYW0gICAgICAge29iamVjdH0gICAgZSAgICAgICBqUXVlcnkgZXZlbnQgb2JqZWN0XG5cdCAqIEBwcml2YXRlXG5cdCAqL1xuXHR2YXIgX25hdmlnYXRlID0gZnVuY3Rpb24oZSkge1xuXHRcdGUucHJldmVudERlZmF1bHQoKTtcblxuXHRcdGhpc3RvcnkuZ28oZS5kYXRhLnN0ZXApO1xuXHR9O1xuXG5cdC8qKlxuXHQgKiBFdmVudCBoYW5kbGVyIHRoYXQgZXhlY3V0ZXMgdGhlIGJyb3dzZXJcblx0ICogcmVmcmVzaCBmdW5jdGlvbmFsaXR5XG5cdCAqIEBwYXJhbSAgICAgICB7b2JqZWN0fSAgICBlICAgICAgIGpRdWVyeSBldmVudCBvYmplY3Rcblx0ICogQHByaXZhdGVcblx0ICovXG5cdHZhciBfcmVmcmVzaCA9IGZ1bmN0aW9uKGUpIHtcblx0XHRlLnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRsb2NhdGlvbi5yZWxvYWQoKTtcblx0fTtcblxuLy8gIyMjIyMjIyMjIyBJTklUSUFMSVpBVElPTiAjIyMjIyMjIyMjXG5cblx0LyoqXG5cdCAqIEluaXQgZnVuY3Rpb24gb2YgdGhlIHdpZGdldFxuXHQgKiBAY29uc3RydWN0b3Jcblx0ICovXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXG5cdFx0JHRoaXNcblx0XHRcdC5vbignY2xpY2snLCAnLmhpc3RvcnktYmFjaycsIHtzdGVwOiAtMX0sIF9uYXZpZ2F0ZSlcblx0XHRcdC5vbignY2xpY2snLCAnLmhpc3RvcnktZm9yd2FyZCcsIHtzdGVwOiAxfSwgX25hdmlnYXRlKVxuXHRcdFx0Lm9uKCdjbGljaycsICcuaGlzdG9yeS1yZWZyZXNoJywgX3JlZnJlc2gpO1xuXG5cdFx0ZG9uZSgpO1xuXHR9O1xuXG5cdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
