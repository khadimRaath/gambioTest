'use strict';

/* --------------------------------------------------------------
 categories_goto_controller.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Overview Goto
 *
 * @module Compatibility/categories_goto_controller
 */
gx.compatibility.module(
// Module name
'categories_goto_controller',

// Module dependencies
[],

/**  @lends module:Compatibility/categories_goto_controller */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DEFINITION
	// ------------------------------------------------------------------------

	// Element: Module selector

	var $this = $(this);

	// Meta object
	var module = {};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	var _createForm = function _createForm() {
		var $form = $('<form>');

		$form.attr({
			name: data.name,
			action: data.action,
			method: 'get'
		});

		return $form;
	};

	var _initialize = function _initialize() {
		// Create new form
		var $form = _createForm();

		// Save HTML content
		var html = $this.html();

		// Insert HTML into form and put form into this element
		$form.html(html);

		$this.empty().append($form);
	};

	module.init = function (done) {
		// Initialize
		_initialize();

		// Register as finished
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNhdGVnb3JpZXMvY2F0ZWdvcmllc19nb3RvX2NvbnRyb2xsZXIuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIl9jcmVhdGVGb3JtIiwiJGZvcm0iLCJhdHRyIiwibmFtZSIsImFjdGlvbiIsIm1ldGhvZCIsIl9pbml0aWFsaXplIiwiaHRtbCIsImVtcHR5IiwiYXBwZW5kIiwiaW5pdCIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7QUFLQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakI7QUFDQztBQUNBLDRCQUZEOztBQUlDO0FBQ0EsRUFMRDs7QUFPQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBOztBQUNBLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaOztBQUVBO0FBQ0EsS0FBSUgsU0FBUyxFQUFiOztBQUVBO0FBQ0E7QUFDQTs7QUFFQSxLQUFJSSxjQUFjLFNBQWRBLFdBQWMsR0FBVztBQUM1QixNQUFJQyxRQUFRRixFQUFFLFFBQUYsQ0FBWjs7QUFFQUUsUUFBTUMsSUFBTixDQUFXO0FBQ1ZDLFNBQU1OLEtBQUtNLElBREQ7QUFFVkMsV0FBUVAsS0FBS08sTUFGSDtBQUdWQyxXQUFRO0FBSEUsR0FBWDs7QUFNQSxTQUFPSixLQUFQO0FBQ0EsRUFWRDs7QUFZQSxLQUFJSyxjQUFjLFNBQWRBLFdBQWMsR0FBVztBQUM1QjtBQUNBLE1BQUlMLFFBQVFELGFBQVo7O0FBRUE7QUFDQSxNQUFJTyxPQUFPVCxNQUFNUyxJQUFOLEVBQVg7O0FBRUE7QUFDQU4sUUFBTU0sSUFBTixDQUFXQSxJQUFYOztBQUVBVCxRQUNFVSxLQURGLEdBRUVDLE1BRkYsQ0FFU1IsS0FGVDtBQUdBLEVBYkQ7O0FBZUFMLFFBQU9jLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUI7QUFDQUw7O0FBRUE7QUFDQUs7QUFDQSxFQU5EOztBQVFBLFFBQU9mLE1BQVA7QUFDQSxDQS9ERiIsImZpbGUiOiJjYXRlZ29yaWVzL2NhdGVnb3JpZXNfZ290b19jb250cm9sbGVyLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBjYXRlZ29yaWVzX2dvdG9fY29udHJvbGxlci5qcyAyMDE1LTA5LTI5IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBDYXRlZ29yaWVzIE92ZXJ2aWV3IEdvdG9cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvY2F0ZWdvcmllc19nb3RvX2NvbnRyb2xsZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdC8vIE1vZHVsZSBuYW1lXG5cdCdjYXRlZ29yaWVzX2dvdG9fY29udHJvbGxlcicsXG5cdFxuXHQvLyBNb2R1bGUgZGVwZW5kZW5jaWVzXG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvY2F0ZWdvcmllc19nb3RvX2NvbnRyb2xsZXIgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvLyBFbGVtZW50OiBNb2R1bGUgc2VsZWN0b3Jcblx0XHR2YXIgJHRoaXMgPSAkKHRoaXMpO1xuXHRcdFxuXHRcdC8vIE1ldGEgb2JqZWN0XG5cdFx0dmFyIG1vZHVsZSA9IHt9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyIF9jcmVhdGVGb3JtID0gZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgJGZvcm0gPSAkKCc8Zm9ybT4nKTtcblx0XHRcdFxuXHRcdFx0JGZvcm0uYXR0cih7XG5cdFx0XHRcdG5hbWU6IGRhdGEubmFtZSxcblx0XHRcdFx0YWN0aW9uOiBkYXRhLmFjdGlvbixcblx0XHRcdFx0bWV0aG9kOiAnZ2V0J1xuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdHJldHVybiAkZm9ybTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfaW5pdGlhbGl6ZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0Ly8gQ3JlYXRlIG5ldyBmb3JtXG5cdFx0XHR2YXIgJGZvcm0gPSBfY3JlYXRlRm9ybSgpO1xuXHRcdFx0XG5cdFx0XHQvLyBTYXZlIEhUTUwgY29udGVudFxuXHRcdFx0dmFyIGh0bWwgPSAkdGhpcy5odG1sKCk7XG5cdFx0XHRcblx0XHRcdC8vIEluc2VydCBIVE1MIGludG8gZm9ybSBhbmQgcHV0IGZvcm0gaW50byB0aGlzIGVsZW1lbnRcblx0XHRcdCRmb3JtLmh0bWwoaHRtbCk7XG5cdFx0XHRcblx0XHRcdCR0aGlzXG5cdFx0XHRcdC5lbXB0eSgpXG5cdFx0XHRcdC5hcHBlbmQoJGZvcm0pO1xuXHRcdH07XG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHQvLyBJbml0aWFsaXplXG5cdFx0XHRfaW5pdGlhbGl6ZSgpO1xuXHRcdFx0XG5cdFx0XHQvLyBSZWdpc3RlciBhcyBmaW5pc2hlZFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
