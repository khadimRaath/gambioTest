'use strict';

/* --------------------------------------------------------------
 paypal_config.js 2015-09-20 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## PayPal Configuration
 *
 * Display info text in info message box.
 *
 * @module Compatibility/main_top_header
 */
gx.compatibility.module('paypal_config', [gx.source + '/libs/info_messages'],

/**  @lends module:Compatibility/paypal_config */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Final Options
  *
  * @var {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Reference to the info messages library
  * @var {object}
  */
	messages = jse.libs.info_messages,


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		if ($('.firstconfig_note').length > 0) {
			$('.firstconfig_note').hide();
			messages.addInfo($('.firstconfig_note').html());
		}

		$('p.message').each(function () {
			messages.addInfo($(this).html());
			$(this).hide();
		});

		$('p.message_info').each(function () {
			messages.addWarning($(this).html());
			$(this).hide();
		});

		$('p.message_success').each(function () {
			messages.addSuccess($(this).html());
			$(this).hide();
		});

		$('p.message_error').each(function () {
			messages.addError($(this).html());
			$(this).hide();
		});

		$('.message_stack_container').addClass('breakpoint-large');

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInBheXBhbC9wYXlwYWxfY29uZmlnLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsInNvdXJjZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJtZXNzYWdlcyIsImpzZSIsImxpYnMiLCJpbmZvX21lc3NhZ2VzIiwiaW5pdCIsImRvbmUiLCJsZW5ndGgiLCJoaWRlIiwiYWRkSW5mbyIsImh0bWwiLCJlYWNoIiwiYWRkV2FybmluZyIsImFkZFN1Y2Nlc3MiLCJhZGRFcnJvciIsImFkZENsYXNzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxlQURELEVBR0MsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHFCQURiLENBSEQ7O0FBT0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7QUFJQU0sWUFBV0MsSUFBSUMsSUFBSixDQUFTQyxhQTFCckI7OztBQTRCQzs7Ozs7QUFLQVgsVUFBUyxFQWpDVjs7QUFtQ0E7QUFDQTtBQUNBOztBQUVBQSxRQUFPWSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlOztBQUU1QixNQUFJVCxFQUFFLG1CQUFGLEVBQXVCVSxNQUF2QixHQUFnQyxDQUFwQyxFQUF1QztBQUN0Q1YsS0FBRSxtQkFBRixFQUF1QlcsSUFBdkI7QUFDQVAsWUFBU1EsT0FBVCxDQUFpQlosRUFBRSxtQkFBRixFQUF1QmEsSUFBdkIsRUFBakI7QUFDQTs7QUFFRGIsSUFBRSxXQUFGLEVBQWVjLElBQWYsQ0FBb0IsWUFBVztBQUM5QlYsWUFBU1EsT0FBVCxDQUFpQlosRUFBRSxJQUFGLEVBQVFhLElBQVIsRUFBakI7QUFDQWIsS0FBRSxJQUFGLEVBQVFXLElBQVI7QUFDQSxHQUhEOztBQUtBWCxJQUFFLGdCQUFGLEVBQW9CYyxJQUFwQixDQUF5QixZQUFXO0FBQ25DVixZQUFTVyxVQUFULENBQW9CZixFQUFFLElBQUYsRUFBUWEsSUFBUixFQUFwQjtBQUNBYixLQUFFLElBQUYsRUFBUVcsSUFBUjtBQUNBLEdBSEQ7O0FBS0FYLElBQUUsbUJBQUYsRUFBdUJjLElBQXZCLENBQTRCLFlBQVc7QUFDdENWLFlBQVNZLFVBQVQsQ0FBb0JoQixFQUFFLElBQUYsRUFBUWEsSUFBUixFQUFwQjtBQUNBYixLQUFFLElBQUYsRUFBUVcsSUFBUjtBQUNBLEdBSEQ7O0FBS0FYLElBQUUsaUJBQUYsRUFBcUJjLElBQXJCLENBQTBCLFlBQVc7QUFDcENWLFlBQVNhLFFBQVQsQ0FBa0JqQixFQUFFLElBQUYsRUFBUWEsSUFBUixFQUFsQjtBQUNBYixLQUFFLElBQUYsRUFBUVcsSUFBUjtBQUNBLEdBSEQ7O0FBS0FYLElBQUUsMEJBQUYsRUFBOEJrQixRQUE5QixDQUF1QyxrQkFBdkM7O0FBRUFUO0FBQ0EsRUE5QkQ7O0FBZ0NBLFFBQU9iLE1BQVA7QUFDQSxDQXpGRiIsImZpbGUiOiJwYXlwYWwvcGF5cGFsX2NvbmZpZy5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gcGF5cGFsX2NvbmZpZy5qcyAyMDE1LTA5LTIwIGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBQYXlQYWwgQ29uZmlndXJhdGlvblxuICpcbiAqIERpc3BsYXkgaW5mbyB0ZXh0IGluIGluZm8gbWVzc2FnZSBib3guXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L21haW5fdG9wX2hlYWRlclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J3BheXBhbF9jb25maWcnLFxuXHRcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9pbmZvX21lc3NhZ2VzJ1xuXHRdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvcGF5cGFsX2NvbmZpZyAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIFJlZmVyZW5jZSB0byB0aGUgaW5mbyBtZXNzYWdlcyBsaWJyYXJ5XG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1lc3NhZ2VzID0ganNlLmxpYnMuaW5mb19tZXNzYWdlcyxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdFxuXHRcdFx0aWYgKCQoJy5maXJzdGNvbmZpZ19ub3RlJykubGVuZ3RoID4gMCkge1xuXHRcdFx0XHQkKCcuZmlyc3Rjb25maWdfbm90ZScpLmhpZGUoKTtcblx0XHRcdFx0bWVzc2FnZXMuYWRkSW5mbygkKCcuZmlyc3Rjb25maWdfbm90ZScpLmh0bWwoKSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdCQoJ3AubWVzc2FnZScpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdG1lc3NhZ2VzLmFkZEluZm8oJCh0aGlzKS5odG1sKCkpO1xuXHRcdFx0XHQkKHRoaXMpLmhpZGUoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkKCdwLm1lc3NhZ2VfaW5mbycpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdG1lc3NhZ2VzLmFkZFdhcm5pbmcoJCh0aGlzKS5odG1sKCkpO1xuXHRcdFx0XHQkKHRoaXMpLmhpZGUoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkKCdwLm1lc3NhZ2Vfc3VjY2VzcycpLmVhY2goZnVuY3Rpb24oKSB7XG5cdFx0XHRcdG1lc3NhZ2VzLmFkZFN1Y2Nlc3MoJCh0aGlzKS5odG1sKCkpO1xuXHRcdFx0XHQkKHRoaXMpLmhpZGUoKTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHQkKCdwLm1lc3NhZ2VfZXJyb3InKS5lYWNoKGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRtZXNzYWdlcy5hZGRFcnJvcigkKHRoaXMpLmh0bWwoKSk7XG5cdFx0XHRcdCQodGhpcykuaGlkZSgpO1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdCQoJy5tZXNzYWdlX3N0YWNrX2NvbnRhaW5lcicpLmFkZENsYXNzKCdicmVha3BvaW50LWxhcmdlJyk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
