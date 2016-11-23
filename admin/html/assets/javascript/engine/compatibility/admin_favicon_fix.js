'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

/* --------------------------------------------------------------
 admin_favicon_fix.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Admin Section Favicon - JS Fix
 *
 * Many pages in the admin section are missing the favicon.ico file because they do not specify this
 * directive in the <head> tag. The following code (pure JavaScript) will fix the issue. This solution
 * will not work in IE9 see: http://stackoverflow.com/a/13388728.
 *
 * This module requires two attributes to be provided as in the following example:
 *
 * ```html
 * <div class="page-wrapper-element"
 *      data-gx-compatibility="admin_favicon_fix"
 *      data-admin_favicon_fix-status="enabled"
 *      data-admin_favicon_fix-filename="favicon.ico"> ... </div>
 * ```
 *
 * @module Compatibility/admin_favicon_fix
 */
gx.compatibility.module('admin_favicon_fix', [],

/** @lends module:Compatibility/admin_favicon_fix */

function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES DECLARATION
	// ------------------------------------------------------------------------

	var
	/**
  * Module Element Selector
  *
  * @var {object}
  */
	$this = $(this),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {
		'filename': '/admin/html/assets/images/gx-admin/favicon.ico'
	},


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
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		try {
			if (!document.querySelector('head link[rel="shortcut icon"]') && options.status === 'enabled') {
				var favicon = document.createElement('link');
				favicon.rel = 'shortcut icon';
				favicon.href = jse.core.config.get('appUrl') + options.filename;
				document.getElementsByTagName('head')[0].appendChild(favicon);
			}
		} catch (exception) {
			if ((typeof console === 'undefined' ? 'undefined' : _typeof(console)) === 'object') {
				console.log('Failed to create favicon tag in document <head> element. Exception: ' + exception);
			}
		}

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFkbWluX2Zhdmljb25fZml4LmpzIl0sIm5hbWVzIjpbImd4IiwiY29tcGF0aWJpbGl0eSIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsIm9wdGlvbnMiLCJleHRlbmQiLCJpbml0IiwiZG9uZSIsImRvY3VtZW50IiwicXVlcnlTZWxlY3RvciIsInN0YXR1cyIsImZhdmljb24iLCJjcmVhdGVFbGVtZW50IiwicmVsIiwiaHJlZiIsImpzZSIsImNvcmUiLCJjb25maWciLCJnZXQiLCJmaWxlbmFtZSIsImdldEVsZW1lbnRzQnlUYWdOYW1lIiwiYXBwZW5kQ2hpbGQiLCJleGNlcHRpb24iLCJjb25zb2xlIiwibG9nIl0sIm1hcHBpbmdzIjoiOzs7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7Ozs7Ozs7Ozs7Ozs7O0FBa0JBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLG1CQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVztBQUNWLGNBQVk7QUFERixFQWJaOzs7QUFpQkM7Ozs7O0FBS0FDLFdBQVVGLEVBQUVHLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkYsUUFBbkIsRUFBNkJILElBQTdCLENBdEJYOzs7QUF3QkM7Ozs7O0FBS0FELFVBQVMsRUE3QlY7O0FBK0JBO0FBQ0E7QUFDQTs7QUFFQUEsUUFBT08sSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTtBQUM1QixNQUFJO0FBQ0gsT0FBSSxDQUFDQyxTQUFTQyxhQUFULENBQXVCLGdDQUF2QixDQUFELElBQTZETCxRQUFRTSxNQUFSLEtBQW1CLFNBQXBGLEVBQStGO0FBQzlGLFFBQUlDLFVBQVVILFNBQVNJLGFBQVQsQ0FBdUIsTUFBdkIsQ0FBZDtBQUNBRCxZQUFRRSxHQUFSLEdBQWMsZUFBZDtBQUNBRixZQUFRRyxJQUFSLEdBQWVDLElBQUlDLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsUUFBcEIsSUFBZ0NkLFFBQVFlLFFBQXZEO0FBQ0FYLGFBQVNZLG9CQUFULENBQThCLE1BQTlCLEVBQXNDLENBQXRDLEVBQXlDQyxXQUF6QyxDQUFxRFYsT0FBckQ7QUFDQTtBQUNELEdBUEQsQ0FPRSxPQUFPVyxTQUFQLEVBQWtCO0FBQ25CLE9BQUksUUFBT0MsT0FBUCx5Q0FBT0EsT0FBUCxPQUFtQixRQUF2QixFQUFpQztBQUNoQ0EsWUFBUUMsR0FBUixDQUFZLHlFQUNYRixTQUREO0FBRUE7QUFDRDs7QUFFRGY7QUFDQSxFQWhCRDs7QUFrQkEsUUFBT1IsTUFBUDtBQUNBLENBckVGIiwiZmlsZSI6ImFkbWluX2Zhdmljb25fZml4LmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBhZG1pbl9mYXZpY29uX2ZpeC5qcyAyMDE1LTEwLTE1IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBBZG1pbiBTZWN0aW9uIEZhdmljb24gLSBKUyBGaXhcbiAqXG4gKiBNYW55IHBhZ2VzIGluIHRoZSBhZG1pbiBzZWN0aW9uIGFyZSBtaXNzaW5nIHRoZSBmYXZpY29uLmljbyBmaWxlIGJlY2F1c2UgdGhleSBkbyBub3Qgc3BlY2lmeSB0aGlzXG4gKiBkaXJlY3RpdmUgaW4gdGhlIDxoZWFkPiB0YWcuIFRoZSBmb2xsb3dpbmcgY29kZSAocHVyZSBKYXZhU2NyaXB0KSB3aWxsIGZpeCB0aGUgaXNzdWUuIFRoaXMgc29sdXRpb25cbiAqIHdpbGwgbm90IHdvcmsgaW4gSUU5IHNlZTogaHR0cDovL3N0YWNrb3ZlcmZsb3cuY29tL2EvMTMzODg3MjguXG4gKlxuICogVGhpcyBtb2R1bGUgcmVxdWlyZXMgdHdvIGF0dHJpYnV0ZXMgdG8gYmUgcHJvdmlkZWQgYXMgaW4gdGhlIGZvbGxvd2luZyBleGFtcGxlOlxuICpcbiAqIGBgYGh0bWxcbiAqIDxkaXYgY2xhc3M9XCJwYWdlLXdyYXBwZXItZWxlbWVudFwiXG4gKiAgICAgIGRhdGEtZ3gtY29tcGF0aWJpbGl0eT1cImFkbWluX2Zhdmljb25fZml4XCJcbiAqICAgICAgZGF0YS1hZG1pbl9mYXZpY29uX2ZpeC1zdGF0dXM9XCJlbmFibGVkXCJcbiAqICAgICAgZGF0YS1hZG1pbl9mYXZpY29uX2ZpeC1maWxlbmFtZT1cImZhdmljb24uaWNvXCI+IC4uLiA8L2Rpdj5cbiAqIGBgYFxuICpcbiAqIEBtb2R1bGUgQ29tcGF0aWJpbGl0eS9hZG1pbl9mYXZpY29uX2ZpeFxuICovXG5neC5jb21wYXRpYmlsaXR5Lm1vZHVsZShcblx0J2FkbWluX2Zhdmljb25fZml4Jyxcblx0XG5cdFtdLFxuXHRcblx0LyoqIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9hZG1pbl9mYXZpY29uX2ZpeCAqL1xuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgREVDTEFSQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIEVsZW1lbnQgU2VsZWN0b3Jcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHtcblx0XHRcdFx0J2ZpbGVuYW1lJzogJy9hZG1pbi9odG1sL2Fzc2V0cy9pbWFnZXMvZ3gtYWRtaW4vZmF2aWNvbi5pY28nXG5cdFx0XHR9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBJTklUSUFMSVpBVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0dHJ5IHtcblx0XHRcdFx0aWYgKCFkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCdoZWFkIGxpbmtbcmVsPVwic2hvcnRjdXQgaWNvblwiXScpICYmIG9wdGlvbnMuc3RhdHVzID09PSAnZW5hYmxlZCcpIHtcblx0XHRcdFx0XHR2YXIgZmF2aWNvbiA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2xpbmsnKTtcblx0XHRcdFx0XHRmYXZpY29uLnJlbCA9ICdzaG9ydGN1dCBpY29uJztcblx0XHRcdFx0XHRmYXZpY29uLmhyZWYgPSBqc2UuY29yZS5jb25maWcuZ2V0KCdhcHBVcmwnKSArIG9wdGlvbnMuZmlsZW5hbWU7XG5cdFx0XHRcdFx0ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ2hlYWQnKVswXS5hcHBlbmRDaGlsZChmYXZpY29uKTtcblx0XHRcdFx0fVxuXHRcdFx0fSBjYXRjaCAoZXhjZXB0aW9uKSB7XG5cdFx0XHRcdGlmICh0eXBlb2YgY29uc29sZSA9PT0gJ29iamVjdCcpIHtcblx0XHRcdFx0XHRjb25zb2xlLmxvZygnRmFpbGVkIHRvIGNyZWF0ZSBmYXZpY29uIHRhZyBpbiBkb2N1bWVudCA8aGVhZD4gZWxlbWVudC4gRXhjZXB0aW9uOiAnICtcblx0XHRcdFx0XHRcdGV4Y2VwdGlvbik7XG5cdFx0XHRcdH1cblx0XHRcdH1cblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
