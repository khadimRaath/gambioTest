'use strict';

/* --------------------------------------------------------------
 content_manager_details_controller.js 2016-08-24
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Content Manager Controller
 *
 * This controller contains the mapping logic of the content manager page.
 *
 * @module Compatibility/content_manager_details_controller
 */
gx.compatibility.module('content_manager_details_controller', [],

/**  @lends module:Compatibility/content_manager_details_controller */

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
	// MAIN FUNCTIONALITY
	// ------------------------------------------------------------------------

	var saveButton = $this.find('[data-value="BUTTON_SAVE"]');
	var updateButton = $this.find('[data-value="BUTTON_UPDATE"]');
	var originalSaveButton = $this.find('[name="save"]');
	var originalUpdateButton = $this.find('[name="reload"]');

	saveButton.on('click', function () {
		originalSaveButton.click();
	});

	updateButton.on('click', function () {
		originalUpdateButton.click();
	});

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------
	module.init = function (done) {
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbnRlbnRfbWFuYWdlci9jb250ZW50X21hbmFnZXJfZGV0YWlsc19jb250cm9sbGVyLmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJzYXZlQnV0dG9uIiwiZmluZCIsInVwZGF0ZUJ1dHRvbiIsIm9yaWdpbmFsU2F2ZUJ1dHRvbiIsIm9yaWdpbmFsVXBkYXRlQnV0dG9uIiwib24iLCJjbGljayIsImluaXQiLCJkb25lIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxvQ0FERCxFQUdDLEVBSEQ7O0FBS0M7O0FBRUEsVUFBU0MsSUFBVCxFQUFlOztBQUVkOztBQUVBO0FBQ0E7QUFDQTs7QUFFQTtBQUNDOzs7OztBQUtBQyxTQUFRQyxFQUFFLElBQUYsQ0FOVDs7O0FBUUM7Ozs7O0FBS0FDLFlBQVcsRUFiWjs7O0FBZUM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBcEJYOzs7QUFzQkM7Ozs7O0FBS0FELFVBQVMsRUEzQlY7O0FBNkJBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJTyxhQUFhTCxNQUFNTSxJQUFOLENBQVcsNEJBQVgsQ0FBakI7QUFDQSxLQUFJQyxlQUFlUCxNQUFNTSxJQUFOLENBQVcsOEJBQVgsQ0FBbkI7QUFDQSxLQUFJRSxxQkFBcUJSLE1BQU1NLElBQU4sQ0FBVyxlQUFYLENBQXpCO0FBQ0EsS0FBSUcsdUJBQXVCVCxNQUFNTSxJQUFOLENBQVcsaUJBQVgsQ0FBM0I7O0FBRUFELFlBQVdLLEVBQVgsQ0FBYyxPQUFkLEVBQXVCLFlBQVc7QUFDakNGLHFCQUFtQkcsS0FBbkI7QUFDQSxFQUZEOztBQUlBSixjQUFhRyxFQUFiLENBQWdCLE9BQWhCLEVBQXlCLFlBQVc7QUFDbkNELHVCQUFxQkUsS0FBckI7QUFDQSxFQUZEOztBQUtBO0FBQ0E7QUFDQTtBQUNBYixRQUFPYyxJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCQTtBQUNBLEVBRkQ7O0FBSUEsUUFBT2YsTUFBUDtBQUNBLENBdEVGIiwiZmlsZSI6ImNvbnRlbnRfbWFuYWdlci9jb250ZW50X21hbmFnZXJfZGV0YWlsc19jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjb250ZW50X21hbmFnZXJfZGV0YWlsc19jb250cm9sbGVyLmpzIDIwMTYtMDgtMjRcbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE2IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqICMjIENvbnRlbnQgTWFuYWdlciBDb250cm9sbGVyXG4gKlxuICogVGhpcyBjb250cm9sbGVyIGNvbnRhaW5zIHRoZSBtYXBwaW5nIGxvZ2ljIG9mIHRoZSBjb250ZW50IG1hbmFnZXIgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvY29udGVudF9tYW5hZ2VyX2RldGFpbHNfY29udHJvbGxlclxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2NvbnRlbnRfbWFuYWdlcl9kZXRhaWxzX2NvbnRyb2xsZXInLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9jb250ZW50X21hbmFnZXJfZGV0YWlsc19jb250cm9sbGVyICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIE9iamVjdFxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIE1BSU4gRlVOQ1RJT05BTElUWVxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhciBzYXZlQnV0dG9uID0gJHRoaXMuZmluZCgnW2RhdGEtdmFsdWU9XCJCVVRUT05fU0FWRVwiXScpO1xuXHRcdHZhciB1cGRhdGVCdXR0b24gPSAkdGhpcy5maW5kKCdbZGF0YS12YWx1ZT1cIkJVVFRPTl9VUERBVEVcIl0nKTtcblx0XHR2YXIgb3JpZ2luYWxTYXZlQnV0dG9uID0gJHRoaXMuZmluZCgnW25hbWU9XCJzYXZlXCJdJyk7XG5cdFx0dmFyIG9yaWdpbmFsVXBkYXRlQnV0dG9uID0gJHRoaXMuZmluZCgnW25hbWU9XCJyZWxvYWRcIl0nKTtcblx0XHRcblx0XHRzYXZlQnV0dG9uLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0b3JpZ2luYWxTYXZlQnV0dG9uLmNsaWNrKCk7XG5cdFx0fSk7XG5cdFx0XG5cdFx0dXBkYXRlQnV0dG9uLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0b3JpZ2luYWxVcGRhdGVCdXR0b24uY2xpY2soKTtcblx0XHR9KTtcblx0XHRcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
