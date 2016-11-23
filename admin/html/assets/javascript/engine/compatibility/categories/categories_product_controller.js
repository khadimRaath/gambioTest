'use strict';

/* --------------------------------------------------------------
 categories_product_controller.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Product Controller
 *
 * This controller contains the mapping logic of the categories save/update buttons.
 *
 * @module Compatibility/categories_product_controller
 */
gx.compatibility.module('categories_product_controller', [gx.source + '/libs/button_dropdown'],

/**  @lends module:Compatibility/categories_product_controller */

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
	// Hide the original buttons
	$('[name="gm_update"]').hide();
	$('[name="save_original"]').hide();

	// Map the new save option to the old save button
	jse.libs.button_dropdown.mapAction($this, 'BUTTON_SAVE', 'admin_buttons', function (event) {
		$('[name="save_original"]').trigger('click');
	});

	// Map the new update option to the old update button
	jse.libs.button_dropdown.mapAction($this, 'BUTTON_UPDATE', 'admin_buttons', function (event) {
		$('[name="gm_update"]').trigger('click');
	});

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhdGVnb3JpZXMvY2F0ZWdvcmllc19wcm9kdWN0X2NvbnRyb2xsZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsImRlZmF1bHRzIiwib3B0aW9ucyIsImV4dGVuZCIsImhpZGUiLCJqc2UiLCJsaWJzIiwiYnV0dG9uX2Ryb3Bkb3duIiwibWFwQWN0aW9uIiwiZXZlbnQiLCJ0cmlnZ2VyIiwiaW5pdCIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLCtCQURELEVBR0MsQ0FDQ0YsR0FBR0csTUFBSCxHQUFZLHVCQURiLENBSEQ7O0FBT0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FGLFVBQVMsRUEzQlY7O0FBNkJBO0FBQ0E7QUFDQTtBQUNBO0FBQ0FJLEdBQUUsb0JBQUYsRUFBd0JJLElBQXhCO0FBQ0FKLEdBQUUsd0JBQUYsRUFBNEJJLElBQTVCOztBQUVBO0FBQ0FDLEtBQUlDLElBQUosQ0FBU0MsZUFBVCxDQUF5QkMsU0FBekIsQ0FBbUNULEtBQW5DLEVBQTBDLGFBQTFDLEVBQXlELGVBQXpELEVBQTBFLFVBQVNVLEtBQVQsRUFBZ0I7QUFDekZULElBQUUsd0JBQUYsRUFBNEJVLE9BQTVCLENBQW9DLE9BQXBDO0FBQ0EsRUFGRDs7QUFJQTtBQUNBTCxLQUFJQyxJQUFKLENBQVNDLGVBQVQsQ0FBeUJDLFNBQXpCLENBQW1DVCxLQUFuQyxFQUEwQyxlQUExQyxFQUEyRCxlQUEzRCxFQUE0RSxVQUFTVSxLQUFULEVBQWdCO0FBQzNGVCxJQUFFLG9CQUFGLEVBQXdCVSxPQUF4QixDQUFnQyxPQUFoQztBQUNBLEVBRkQ7O0FBSUE7QUFDQTtBQUNBOztBQUVBZCxRQUFPZSxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCQTtBQUNBLEVBRkQ7O0FBSUEsUUFBT2hCLE1BQVA7QUFDQSxDQXhFRiIsImZpbGUiOiJjYXRlZ29yaWVzL2NhdGVnb3JpZXNfcHJvZHVjdF9jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjYXRlZ29yaWVzX3Byb2R1Y3RfY29udHJvbGxlci5qcyAyMDE1LTEwLTE1IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBDYXRlZ29yaWVzIFByb2R1Y3QgQ29udHJvbGxlclxuICpcbiAqIFRoaXMgY29udHJvbGxlciBjb250YWlucyB0aGUgbWFwcGluZyBsb2dpYyBvZiB0aGUgY2F0ZWdvcmllcyBzYXZlL3VwZGF0ZSBidXR0b25zLlxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9jYXRlZ29yaWVzX3Byb2R1Y3RfY29udHJvbGxlclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2NhdGVnb3JpZXNfcHJvZHVjdF9jb250cm9sbGVyJyxcblx0XG5cdFtcblx0XHRneC5zb3VyY2UgKyAnL2xpYnMvYnV0dG9uX2Ryb3Bkb3duJ1xuXHRdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvY2F0ZWdvcmllc19wcm9kdWN0X2NvbnRyb2xsZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRGVmYXVsdCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0ZGVmYXVsdHMgPSB7fSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRvcHRpb25zID0gJC5leHRlbmQodHJ1ZSwge30sIGRlZmF1bHRzLCBkYXRhKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gT1BFUkFUSU9OU1xuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIEhpZGUgdGhlIG9yaWdpbmFsIGJ1dHRvbnNcblx0XHQkKCdbbmFtZT1cImdtX3VwZGF0ZVwiXScpLmhpZGUoKTtcblx0XHQkKCdbbmFtZT1cInNhdmVfb3JpZ2luYWxcIl0nKS5oaWRlKCk7XG5cdFx0XG5cdFx0Ly8gTWFwIHRoZSBuZXcgc2F2ZSBvcHRpb24gdG8gdGhlIG9sZCBzYXZlIGJ1dHRvblxuXHRcdGpzZS5saWJzLmJ1dHRvbl9kcm9wZG93bi5tYXBBY3Rpb24oJHRoaXMsICdCVVRUT05fU0FWRScsICdhZG1pbl9idXR0b25zJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdCQoJ1tuYW1lPVwic2F2ZV9vcmlnaW5hbFwiXScpLnRyaWdnZXIoJ2NsaWNrJyk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0Ly8gTWFwIHRoZSBuZXcgdXBkYXRlIG9wdGlvbiB0byB0aGUgb2xkIHVwZGF0ZSBidXR0b25cblx0XHRqc2UubGlicy5idXR0b25fZHJvcGRvd24ubWFwQWN0aW9uKCR0aGlzLCAnQlVUVE9OX1VQREFURScsICdhZG1pbl9idXR0b25zJywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdCQoJ1tuYW1lPVwiZ21fdXBkYXRlXCJdJykudHJpZ2dlcignY2xpY2snKTtcblx0XHR9KTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
