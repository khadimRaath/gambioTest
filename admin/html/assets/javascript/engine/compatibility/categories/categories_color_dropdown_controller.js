'use strict';

/* --------------------------------------------------------------
 categories_color_dropdown_controller.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Color Dropdown Controller
 *
 * This controller changes the color of the dropdown button to gambio blue
 *
 * @module Compatibility/categories_color_dropdown_controller
 */
gx.compatibility.module('categories_color_dropdown_controller', [],

/**  @lends module:Compatibility/categories_color_dropdown_controller */

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
	// OPERATIONS
	// ------------------------------------------------------------------------

	// Add the btn-primary css class to the dropdown
	$('.js-button-dropdown button').addClass('btn-primary');

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		done(); // Finish it
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhdGVnb3JpZXMvY2F0ZWdvcmllc19jb2xvcl9kcm9wZG93bl9jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJhZGRDbGFzcyIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxzQ0FERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FELFVBQVMsRUEzQlY7O0FBNkJBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBRyxHQUFFLDRCQUFGLEVBQWdDSSxRQUFoQyxDQUF5QyxhQUF6Qzs7QUFFQTtBQUNBO0FBQ0E7O0FBRUFQLFFBQU9RLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJBLFNBRDRCLENBQ3BCO0FBQ1IsRUFGRDs7QUFJQSxRQUFPVCxNQUFQO0FBQ0EsQ0E1REYiLCJmaWxlIjoiY2F0ZWdvcmllcy9jYXRlZ29yaWVzX2NvbG9yX2Ryb3Bkb3duX2NvbnRyb2xsZXIuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIGNhdGVnb3JpZXNfY29sb3JfZHJvcGRvd25fY29udHJvbGxlci5qcyAyMDE1LTA5LTI5IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBDYXRlZ29yaWVzIENvbG9yIERyb3Bkb3duIENvbnRyb2xsZXJcbiAqXG4gKiBUaGlzIGNvbnRyb2xsZXIgY2hhbmdlcyB0aGUgY29sb3Igb2YgdGhlIGRyb3Bkb3duIGJ1dHRvbiB0byBnYW1iaW8gYmx1ZVxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9jYXRlZ29yaWVzX2NvbG9yX2Ryb3Bkb3duX2NvbnRyb2xsZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdjYXRlZ29yaWVzX2NvbG9yX2Ryb3Bkb3duX2NvbnRyb2xsZXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9jYXRlZ29yaWVzX2NvbG9yX2Ryb3Bkb3duX2NvbnRyb2xsZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gT1BFUkFUSU9OU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdC8vIEFkZCB0aGUgYnRuLXByaW1hcnkgY3NzIGNsYXNzIHRvIHRoZSBkcm9wZG93blxuXHRcdCQoJy5qcy1idXR0b24tZHJvcGRvd24gYnV0dG9uJykuYWRkQ2xhc3MoJ2J0bi1wcmltYXJ5Jyk7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdGRvbmUoKTsgLy8gRmluaXNoIGl0XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
