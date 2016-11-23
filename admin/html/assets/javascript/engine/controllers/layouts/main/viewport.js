'use strict';

/* --------------------------------------------------------------
 viewport.js 2016-06-14
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

gx.controllers.module('viewport', [], function (data) {

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
  * Info Row
  *
  * @type {jQuery}
  */
	var $infoRow = $('#main-footer .info.row');

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Checks if the provided dropdown is out of the viewport.
  *
  * @param {jQuery} $dropDownMenu
  *
  * @returns {boolean}
  */
	function _isDropDownOutOfView($dropDownMenu) {
		var infoRowTopPosition = $infoRow.offset().top;

		return $dropDownMenu.height() + $dropDownMenu.siblings('.dropdown-toggle').offset().top > infoRowTopPosition;
	}

	/**
  * Adjust the dropdown position, depending on the current viewport.
  */
	function _adjustDropDownPosition() {

		var $target = $(this);

		var $dropDownMenu = $target.find('.dropdown-menu');

		// Put the dropdown menu above the clicked target,
		// if the menu would touch or even be larger than the info row in the main footer.
		if (_isDropDownOutOfView($dropDownMenu)) {
			$target.addClass('dropup');
			$target.find('.caret').addClass('caret-reversed');
		} else if ($target.hasClass('dropup')) {
			$target.removeClass('dropup');
			$target.find('.caret').removeClass('caret-reversed');
		}
	}

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$('body').on('show.bs.dropdown', '.btn-group.dropdown', _adjustDropDownPosition);

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi92aWV3cG9ydC5qcyJdLCJuYW1lcyI6WyJneCIsImNvbnRyb2xsZXJzIiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRpbmZvUm93IiwiX2lzRHJvcERvd25PdXRPZlZpZXciLCIkZHJvcERvd25NZW51IiwiaW5mb1Jvd1RvcFBvc2l0aW9uIiwib2Zmc2V0IiwidG9wIiwiaGVpZ2h0Iiwic2libGluZ3MiLCJfYWRqdXN0RHJvcERvd25Qb3NpdGlvbiIsIiR0YXJnZXQiLCJmaW5kIiwiYWRkQ2xhc3MiLCJoYXNDbGFzcyIsInJlbW92ZUNsYXNzIiwiaW5pdCIsImRvbmUiLCJvbiJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsVUFBdEIsRUFBa0MsRUFBbEMsRUFBc0MsVUFBU0MsSUFBVCxFQUFlOztBQUVwRDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFFQTs7Ozs7QUFLQSxLQUFNSSxXQUFXRCxFQUFFLHdCQUFGLENBQWpCOztBQUdBO0FBQ0E7QUFDQTs7QUFFQTs7Ozs7OztBQU9BLFVBQVNFLG9CQUFULENBQThCQyxhQUE5QixFQUE2QztBQUM1QyxNQUFNQyxxQkFBcUJILFNBQVNJLE1BQVQsR0FBa0JDLEdBQTdDOztBQUVBLFNBQVFILGNBQWNJLE1BQWQsS0FBeUJKLGNBQWNLLFFBQWQsQ0FBdUIsa0JBQXZCLEVBQTJDSCxNQUEzQyxHQUFvREMsR0FBOUUsR0FBcUZGLGtCQUE1RjtBQUNBOztBQUVEOzs7QUFHQSxVQUFTSyx1QkFBVCxHQUFtQzs7QUFFbEMsTUFBTUMsVUFBVVYsRUFBRSxJQUFGLENBQWhCOztBQUVBLE1BQUlHLGdCQUFnQk8sUUFBUUMsSUFBUixDQUFhLGdCQUFiLENBQXBCOztBQUVBO0FBQ0E7QUFDQSxNQUFHVCxxQkFBcUJDLGFBQXJCLENBQUgsRUFBd0M7QUFDdkNPLFdBQVFFLFFBQVIsQ0FBaUIsUUFBakI7QUFDQUYsV0FBUUMsSUFBUixDQUFhLFFBQWIsRUFBdUJDLFFBQXZCLENBQWdDLGdCQUFoQztBQUNBLEdBSEQsTUFHTyxJQUFJRixRQUFRRyxRQUFSLENBQWlCLFFBQWpCLENBQUosRUFBZ0M7QUFDdENILFdBQVFJLFdBQVIsQ0FBb0IsUUFBcEI7QUFDQUosV0FBUUMsSUFBUixDQUFhLFFBQWIsRUFBdUJHLFdBQXZCLENBQW1DLGdCQUFuQztBQUNBO0FBQ0Q7O0FBRUQ7QUFDQTtBQUNBOztBQUVBakIsUUFBT2tCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJoQixJQUFFLE1BQUYsRUFBVWlCLEVBQVYsQ0FBYSxrQkFBYixFQUFpQyxxQkFBakMsRUFBd0RSLHVCQUF4RDs7QUFFQU87QUFDQSxFQUpEOztBQU1BLFFBQU9uQixNQUFQO0FBQ0EsQ0E5RUQiLCJmaWxlIjoibGF5b3V0cy9tYWluL3ZpZXdwb3J0LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIHZpZXdwb3J0LmpzIDIwMTYtMDYtMTRcclxuIEdhbWJpbyBHbWJIXHJcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxyXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXHJcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcclxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxyXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuICovXHJcblxyXG5neC5jb250cm9sbGVycy5tb2R1bGUoJ3ZpZXdwb3J0JywgW10sIGZ1bmN0aW9uKGRhdGEpIHtcclxuXHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBWQVJJQUJMRVNcclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHJcblx0LyoqXHJcblx0ICogTW9kdWxlIFNlbGVjdG9yXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7alF1ZXJ5fVxyXG5cdCAqL1xyXG5cdGNvbnN0ICR0aGlzID0gJCh0aGlzKTtcclxuXHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cclxuXHQvKipcclxuXHQgKiBJbmZvIFJvd1xyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkaW5mb1JvdyA9ICQoJyNtYWluLWZvb3RlciAuaW5mby5yb3cnKTtcclxuXHJcblxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIEZVTkNUSU9OU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHQvKipcclxuXHQgKiBDaGVja3MgaWYgdGhlIHByb3ZpZGVkIGRyb3Bkb3duIGlzIG91dCBvZiB0aGUgdmlld3BvcnQuXHJcblx0ICpcclxuXHQgKiBAcGFyYW0ge2pRdWVyeX0gJGRyb3BEb3duTWVudVxyXG5cdCAqXHJcblx0ICogQHJldHVybnMge2Jvb2xlYW59XHJcblx0ICovXHJcblx0ZnVuY3Rpb24gX2lzRHJvcERvd25PdXRPZlZpZXcoJGRyb3BEb3duTWVudSkge1xyXG5cdFx0Y29uc3QgaW5mb1Jvd1RvcFBvc2l0aW9uID0gJGluZm9Sb3cub2Zmc2V0KCkudG9wO1xyXG5cclxuXHRcdHJldHVybiAoJGRyb3BEb3duTWVudS5oZWlnaHQoKSArICRkcm9wRG93bk1lbnUuc2libGluZ3MoJy5kcm9wZG93bi10b2dnbGUnKS5vZmZzZXQoKS50b3ApID4gaW5mb1Jvd1RvcFBvc2l0aW9uO1xyXG5cdH1cclxuXHJcblx0LyoqXHJcblx0ICogQWRqdXN0IHRoZSBkcm9wZG93biBwb3NpdGlvbiwgZGVwZW5kaW5nIG9uIHRoZSBjdXJyZW50IHZpZXdwb3J0LlxyXG5cdCAqL1xyXG5cdGZ1bmN0aW9uIF9hZGp1c3REcm9wRG93blBvc2l0aW9uKCkge1xyXG5cclxuXHRcdGNvbnN0ICR0YXJnZXQgPSAkKHRoaXMpO1xyXG5cclxuXHRcdGxldCAkZHJvcERvd25NZW51ID0gJHRhcmdldC5maW5kKCcuZHJvcGRvd24tbWVudScpO1xyXG5cclxuXHRcdC8vIFB1dCB0aGUgZHJvcGRvd24gbWVudSBhYm92ZSB0aGUgY2xpY2tlZCB0YXJnZXQsXHJcblx0XHQvLyBpZiB0aGUgbWVudSB3b3VsZCB0b3VjaCBvciBldmVuIGJlIGxhcmdlciB0aGFuIHRoZSBpbmZvIHJvdyBpbiB0aGUgbWFpbiBmb290ZXIuXHJcblx0XHRpZihfaXNEcm9wRG93bk91dE9mVmlldygkZHJvcERvd25NZW51KSkge1xyXG5cdFx0XHQkdGFyZ2V0LmFkZENsYXNzKCdkcm9wdXAnKTtcclxuXHRcdFx0JHRhcmdldC5maW5kKCcuY2FyZXQnKS5hZGRDbGFzcygnY2FyZXQtcmV2ZXJzZWQnKTtcclxuXHRcdH0gZWxzZSBpZiAoJHRhcmdldC5oYXNDbGFzcygnZHJvcHVwJykpIHtcclxuXHRcdFx0JHRhcmdldC5yZW1vdmVDbGFzcygnZHJvcHVwJyk7XHJcblx0XHRcdCR0YXJnZXQuZmluZCgnLmNhcmV0JykucmVtb3ZlQ2xhc3MoJ2NhcmV0LXJldmVyc2VkJyk7XHJcblx0XHR9XHJcblx0fVxyXG5cclxuXHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuXHQvLyBJTklUSUFMSVpBVElPTlxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cclxuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcclxuXHRcdCQoJ2JvZHknKS5vbignc2hvdy5icy5kcm9wZG93bicsICcuYnRuLWdyb3VwLmRyb3Bkb3duJywgX2FkanVzdERyb3BEb3duUG9zaXRpb24pO1xyXG5cclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cclxuXHRyZXR1cm4gbW9kdWxlO1xyXG59KTtcclxuIl19
