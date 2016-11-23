'use strict';

/* --------------------------------------------------------------
 resize_page.js 2015-10-03
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Resize Page
 *
 * Resizes the page to a calculated height including the (absolutely positioned) configuration box on the right side.
 *
 * @module Compatibility/resize_page
 */
gx.compatibility.module('resize_page', [],

/**  @lends module:Compatibility/resize_page */

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
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	/**
  * Resizes the page to the maximum height of boxCenterWrapper and gx-configuration-box
  */
	var _resizePage = function _resizePage() {
		$('.boxCenterWrapper').height(Math.max($('.boxCenterWrapper').height(), $('.configuration-box-content').height()));
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		if ($('div.gx-configuration-box').length) {
			$('div.gx-configuration-box').on('resize', _resizePage);
			$('.boxCenterWrapper').on('resize', _resizePage);
			window.setTimeout(function () {
				_resizePage();
			}, 500);
		}

		if ($('#toolbar').length) {
			$('#toolbar').on('click', _resizePage);
			$('#gm_gprint_content').on('dblclick', _resizePage);
			$('#element_type').on('change', _resizePage);
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInJlc2l6ZV9wYWdlLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJfcmVzaXplUGFnZSIsImhlaWdodCIsIk1hdGgiLCJtYXgiLCJpbml0IiwiZG9uZSIsImxlbmd0aCIsIm9uIiwid2luZG93Iiwic2V0VGltZW91dCJdLCJtYXBwaW5ncyI6Ijs7QUFBQTs7Ozs7Ozs7OztBQVVBOzs7Ozs7O0FBT0FBLEdBQUdDLGFBQUgsQ0FBaUJDLE1BQWpCLENBQ0MsYUFERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FELFVBQVMsRUEzQlY7O0FBNkJBO0FBQ0E7QUFDQTs7QUFFQTs7O0FBR0EsS0FBSU8sY0FBYyxTQUFkQSxXQUFjLEdBQVc7QUFDNUJKLElBQUUsbUJBQUYsRUFBdUJLLE1BQXZCLENBQThCQyxLQUFLQyxHQUFMLENBQVNQLEVBQUUsbUJBQUYsRUFBdUJLLE1BQXZCLEVBQVQsRUFBMENMLEVBQ3ZFLDRCQUR1RSxFQUN6Q0ssTUFEeUMsRUFBMUMsQ0FBOUI7QUFHQSxFQUpEOztBQU1BO0FBQ0E7QUFDQTs7QUFFQVIsUUFBT1csSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QixNQUFJVCxFQUFFLDBCQUFGLEVBQThCVSxNQUFsQyxFQUEwQztBQUN6Q1YsS0FBRSwwQkFBRixFQUE4QlcsRUFBOUIsQ0FBaUMsUUFBakMsRUFBMkNQLFdBQTNDO0FBQ0FKLEtBQUUsbUJBQUYsRUFBdUJXLEVBQXZCLENBQTBCLFFBQTFCLEVBQW9DUCxXQUFwQztBQUNBUSxVQUFPQyxVQUFQLENBQWtCLFlBQVc7QUFDNUJUO0FBQ0EsSUFGRCxFQUVHLEdBRkg7QUFHQTs7QUFFRCxNQUFJSixFQUFFLFVBQUYsRUFBY1UsTUFBbEIsRUFBMEI7QUFDekJWLEtBQUUsVUFBRixFQUFjVyxFQUFkLENBQWlCLE9BQWpCLEVBQTBCUCxXQUExQjtBQUNBSixLQUFFLG9CQUFGLEVBQXdCVyxFQUF4QixDQUEyQixVQUEzQixFQUF1Q1AsV0FBdkM7QUFDQUosS0FBRSxlQUFGLEVBQW1CVyxFQUFuQixDQUFzQixRQUF0QixFQUFnQ1AsV0FBaEM7QUFDQTs7QUFFREs7QUFDQSxFQWhCRDs7QUFrQkEsUUFBT1osTUFBUDtBQUNBLENBaEZGIiwiZmlsZSI6InJlc2l6ZV9wYWdlLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiByZXNpemVfcGFnZS5qcyAyMDE1LTEwLTAzXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBSZXNpemUgUGFnZVxuICpcbiAqIFJlc2l6ZXMgdGhlIHBhZ2UgdG8gYSBjYWxjdWxhdGVkIGhlaWdodCBpbmNsdWRpbmcgdGhlIChhYnNvbHV0ZWx5IHBvc2l0aW9uZWQpIGNvbmZpZ3VyYXRpb24gYm94IG9uIHRoZSByaWdodCBzaWRlLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9yZXNpemVfcGFnZVxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J3Jlc2l6ZV9wYWdlJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvcmVzaXplX3BhZ2UgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBSZXNpemVzIHRoZSBwYWdlIHRvIHRoZSBtYXhpbXVtIGhlaWdodCBvZiBib3hDZW50ZXJXcmFwcGVyIGFuZCBneC1jb25maWd1cmF0aW9uLWJveFxuXHRcdCAqL1xuXHRcdHZhciBfcmVzaXplUGFnZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnLmJveENlbnRlcldyYXBwZXInKS5oZWlnaHQoTWF0aC5tYXgoJCgnLmJveENlbnRlcldyYXBwZXInKS5oZWlnaHQoKSwgJChcblx0XHRcdFx0Jy5jb25maWd1cmF0aW9uLWJveC1jb250ZW50JykuaGVpZ2h0KCkpKTtcblx0XHRcdFxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdGlmICgkKCdkaXYuZ3gtY29uZmlndXJhdGlvbi1ib3gnKS5sZW5ndGgpIHtcblx0XHRcdFx0JCgnZGl2Lmd4LWNvbmZpZ3VyYXRpb24tYm94Jykub24oJ3Jlc2l6ZScsIF9yZXNpemVQYWdlKTtcblx0XHRcdFx0JCgnLmJveENlbnRlcldyYXBwZXInKS5vbigncmVzaXplJywgX3Jlc2l6ZVBhZ2UpO1xuXHRcdFx0XHR3aW5kb3cuc2V0VGltZW91dChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRfcmVzaXplUGFnZSgpO1xuXHRcdFx0XHR9LCA1MDApO1xuXHRcdFx0fVxuXHRcdFx0XG5cdFx0XHRpZiAoJCgnI3Rvb2xiYXInKS5sZW5ndGgpIHtcblx0XHRcdFx0JCgnI3Rvb2xiYXInKS5vbignY2xpY2snLCBfcmVzaXplUGFnZSk7XG5cdFx0XHRcdCQoJyNnbV9ncHJpbnRfY29udGVudCcpLm9uKCdkYmxjbGljaycsIF9yZXNpemVQYWdlKTtcblx0XHRcdFx0JCgnI2VsZW1lbnRfdHlwZScpLm9uKCdjaGFuZ2UnLCBfcmVzaXplUGFnZSk7XG5cdFx0XHR9XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdHJldHVybiBtb2R1bGU7XG5cdH0pO1xuIl19
