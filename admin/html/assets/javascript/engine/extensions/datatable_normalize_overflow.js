'use strict';

/* --------------------------------------------------------------
 datatable_normalize_overflow.js 2016-06-09
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Normalize DataTable Text Overflow
 *
 * This extension works in cooperation with the _tables.scss file which will set the default styling of `tbody`
 * `td` elements to overflow: hidden and text-overflow: ellipsis. This can produce problems with `td` elements
 * that contain an `i` tag, by cutting the icon image in the middle. This module will reset the default styling of
 * _tables.scss for those columns.
 *
 * @module Admin/Extensions/datatable_normalize_overflow
 */
gx.extensions.module('datatable_normalize_overflow', [], function (data) {

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
  * Normalize the overflow of the table cells that contain an icon.
  */
	function _normalizeOverflow() {
		$this.find('tbody i').each(function (index, icon) {
			$(icon).parents('td').css({
				overflow: 'initial'
			});
		});
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('draw.dt', _normalizeOverflow);
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImRhdGF0YWJsZV9ub3JtYWxpemVfb3ZlcmZsb3cuanMiXSwibmFtZXMiOlsiZ3giLCJleHRlbnNpb25zIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIl9ub3JtYWxpemVPdmVyZmxvdyIsImZpbmQiLCJlYWNoIiwiaW5kZXgiLCJpY29uIiwicGFyZW50cyIsImNzcyIsIm92ZXJmbG93IiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7Ozs7O0FBVUFBLEdBQUdDLFVBQUgsQ0FBY0MsTUFBZCxDQUFxQiw4QkFBckIsRUFBcUQsRUFBckQsRUFBeUQsVUFBU0MsSUFBVCxFQUFlOztBQUV2RTs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7OztBQUdBLFVBQVNJLGtCQUFULEdBQThCO0FBQzdCRixRQUFNRyxJQUFOLENBQVcsU0FBWCxFQUFzQkMsSUFBdEIsQ0FBMkIsVUFBQ0MsS0FBRCxFQUFRQyxJQUFSLEVBQWlCO0FBQzNDTCxLQUFFSyxJQUFGLEVBQVFDLE9BQVIsQ0FBZ0IsSUFBaEIsRUFBc0JDLEdBQXRCLENBQTBCO0FBQ3pCQyxjQUFVO0FBRGUsSUFBMUI7QUFHQSxHQUpEO0FBS0E7O0FBRUQ7QUFDQTtBQUNBOztBQUVBWCxRQUFPWSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCWCxRQUFNWSxFQUFOLENBQVMsU0FBVCxFQUFvQlYsa0JBQXBCO0FBQ0FTO0FBQ0EsRUFIRDs7QUFLQSxRQUFPYixNQUFQO0FBRUEsQ0FoREQiLCJmaWxlIjoiZGF0YXRhYmxlX25vcm1hbGl6ZV9vdmVyZmxvdy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcbiBkYXRhdGFibGVfbm9ybWFsaXplX292ZXJmbG93LmpzIDIwMTYtMDYtMDlcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG4vKipcclxuICogIyMgTm9ybWFsaXplIERhdGFUYWJsZSBUZXh0IE92ZXJmbG93XHJcbiAqXHJcbiAqIFRoaXMgZXh0ZW5zaW9uIHdvcmtzIGluIGNvb3BlcmF0aW9uIHdpdGggdGhlIF90YWJsZXMuc2NzcyBmaWxlIHdoaWNoIHdpbGwgc2V0IHRoZSBkZWZhdWx0IHN0eWxpbmcgb2YgYHRib2R5YFxyXG4gKiBgdGRgIGVsZW1lbnRzIHRvIG92ZXJmbG93OiBoaWRkZW4gYW5kIHRleHQtb3ZlcmZsb3c6IGVsbGlwc2lzLiBUaGlzIGNhbiBwcm9kdWNlIHByb2JsZW1zIHdpdGggYHRkYCBlbGVtZW50c1xyXG4gKiB0aGF0IGNvbnRhaW4gYW4gYGlgIHRhZywgYnkgY3V0dGluZyB0aGUgaWNvbiBpbWFnZSBpbiB0aGUgbWlkZGxlLiBUaGlzIG1vZHVsZSB3aWxsIHJlc2V0IHRoZSBkZWZhdWx0IHN0eWxpbmcgb2ZcclxuICogX3RhYmxlcy5zY3NzIGZvciB0aG9zZSBjb2x1bW5zLlxyXG4gKlxyXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvZGF0YXRhYmxlX25vcm1hbGl6ZV9vdmVyZmxvd1xyXG4gKi9cclxuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoJ2RhdGF0YWJsZV9ub3JtYWxpemVfb3ZlcmZsb3cnLCBbXSwgZnVuY3Rpb24oZGF0YSkge1xyXG5cdFxyXG5cdCd1c2Ugc3RyaWN0JztcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBNb2R1bGUgU2VsZWN0b3JcclxuXHQgKlxyXG5cdCAqIEB0eXBlIHtqUXVlcnl9XHJcblx0ICovXHJcblx0Y29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBJbnN0YW5jZVxyXG5cdCAqXHJcblx0ICogQHR5cGUge09iamVjdH1cclxuXHQgKi9cclxuXHRjb25zdCBtb2R1bGUgPSB7fTtcclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBGVU5DVElPTlNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHRcclxuXHQvKipcclxuXHQgKiBOb3JtYWxpemUgdGhlIG92ZXJmbG93IG9mIHRoZSB0YWJsZSBjZWxscyB0aGF0IGNvbnRhaW4gYW4gaWNvbi5cclxuXHQgKi9cclxuXHRmdW5jdGlvbiBfbm9ybWFsaXplT3ZlcmZsb3coKSB7XHJcblx0XHQkdGhpcy5maW5kKCd0Ym9keSBpJykuZWFjaCgoaW5kZXgsIGljb24pID0+IHtcclxuXHRcdFx0JChpY29uKS5wYXJlbnRzKCd0ZCcpLmNzcyh7XHJcblx0XHRcdFx0b3ZlcmZsb3c6ICdpbml0aWFsJ1xyXG5cdFx0XHR9KTtcclxuXHRcdH0pO1xyXG5cdH1cclxuXHRcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xyXG5cdFx0JHRoaXMub24oJ2RyYXcuZHQnLCBfbm9ybWFsaXplT3ZlcmZsb3cpO1xyXG5cdFx0ZG9uZSgpO1xyXG5cdH07XHJcblx0XHJcblx0cmV0dXJuIG1vZHVsZTtcclxuXHRcclxufSk7Il19
