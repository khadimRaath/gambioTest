'use strict';

/* --------------------------------------------------------------
 initialize.js 2016-04-18
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Admin Layout Initialization Controller
 *
 * This controller will handle the initialization of the admin pages. Bind this controller
 * in the body element of the page.
 */
gx.controllers.module('initialize', [], function (data) {

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
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$('body').on('JSENGINE_INIT_FINISHED', function () {
			$this.fadeIn(200, function () {
				$this.removeClass('page-loading');
			});
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImxheW91dHMvbWFpbi9pbml0aWFsaXplLmpzIl0sIm5hbWVzIjpbImd4IiwiY29udHJvbGxlcnMiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiaW5pdCIsImRvbmUiLCJvbiIsImZhZGVJbiIsInJlbW92ZUNsYXNzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7OztBQU1BQSxHQUFHQyxXQUFILENBQWVDLE1BQWYsQ0FBc0IsWUFBdEIsRUFBb0MsRUFBcEMsRUFBd0MsVUFBU0MsSUFBVCxFQUFlOztBQUV0RDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7Ozs7OztBQUtBLEtBQU1DLFFBQVFDLEVBQUUsSUFBRixDQUFkOztBQUVBOzs7OztBQUtBLEtBQU1ILFNBQVMsRUFBZjs7QUFFQTtBQUNBO0FBQ0E7O0FBRUFBLFFBQU9JLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7QUFDNUJGLElBQUUsTUFBRixFQUFVRyxFQUFWLENBQWEsd0JBQWIsRUFBdUMsWUFBTTtBQUM1Q0osU0FBTUssTUFBTixDQUFhLEdBQWIsRUFBa0IsWUFBTTtBQUN2QkwsVUFBTU0sV0FBTixDQUFrQixjQUFsQjtBQUNBLElBRkQ7QUFHQSxHQUpEOztBQU1BSDtBQUNBLEVBUkQ7O0FBVUEsUUFBT0wsTUFBUDtBQUVBLENBdENEIiwiZmlsZSI6ImxheW91dHMvbWFpbi9pbml0aWFsaXplLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cclxuIGluaXRpYWxpemUuanMgMjAxNi0wNC0xOFxyXG4gR2FtYmlvIEdtYkhcclxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXHJcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcclxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxyXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXHJcbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG4gKi9cclxuXHJcbi8qKlxyXG4gKiBBZG1pbiBMYXlvdXQgSW5pdGlhbGl6YXRpb24gQ29udHJvbGxlclxyXG4gKlxyXG4gKiBUaGlzIGNvbnRyb2xsZXIgd2lsbCBoYW5kbGUgdGhlIGluaXRpYWxpemF0aW9uIG9mIHRoZSBhZG1pbiBwYWdlcy4gQmluZCB0aGlzIGNvbnRyb2xsZXJcclxuICogaW4gdGhlIGJvZHkgZWxlbWVudCBvZiB0aGUgcGFnZS5cclxuICovXHJcbmd4LmNvbnRyb2xsZXJzLm1vZHVsZSgnaW5pdGlhbGl6ZScsIFtdLCBmdW5jdGlvbihkYXRhKSB7XHJcblx0XHJcblx0J3VzZSBzdHJpY3QnO1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIFZBUklBQkxFU1xyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdFxyXG5cdC8qKlxyXG5cdCAqIE1vZHVsZSBTZWxlY3RvclxyXG5cdCAqXHJcblx0ICogQHR5cGUge2pRdWVyeX1cclxuXHQgKi9cclxuXHRjb25zdCAkdGhpcyA9ICQodGhpcyk7XHJcblx0XHJcblx0LyoqXHJcblx0ICogTW9kdWxlIEluc3RhbmNlXHJcblx0ICpcclxuXHQgKiBAdHlwZSB7T2JqZWN0fVxyXG5cdCAqL1xyXG5cdGNvbnN0IG1vZHVsZSA9IHt9O1xyXG5cdFxyXG5cdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxyXG5cdC8vIElOSVRJQUxJWkFUSU9OXHJcblx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXHJcblx0XHJcblx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XHJcblx0XHQkKCdib2R5Jykub24oJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCAoKSA9PiB7XHJcblx0XHRcdCR0aGlzLmZhZGVJbigyMDAsICgpID0+IHtcclxuXHRcdFx0XHQkdGhpcy5yZW1vdmVDbGFzcygncGFnZS1sb2FkaW5nJyk7XHJcblx0XHRcdH0pO1xyXG5cdFx0fSk7XHJcblx0XHRcclxuXHRcdGRvbmUoKTtcclxuXHR9O1xyXG5cdFxyXG5cdHJldHVybiBtb2R1bGU7XHJcblx0XHJcbn0pOyJdfQ==
