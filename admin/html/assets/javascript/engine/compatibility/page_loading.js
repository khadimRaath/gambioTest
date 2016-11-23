'use strict';

/* --------------------------------------------------------------
 page_loading.js 2015-09-17 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Page Loading Module
 *
 * This module will display a loading page screen for approximately 1 second,
 * the time needed by the engine for the conversion. It will also fade the page
 * out when the user leaves a page, making the transition very smooth.
 *
 * @module Compatibility/page_loading
 */
gx.compatibility.module('page_loading', [],

/**  @lends module:Compatibility/page_loading */

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
  * Excluded pages to prevent white fade in.
  * @type {string[]}
  */
	excludedPages = ['backup.php', 'gm_backup_files_zip.php', 'orders_iloxx.php'],


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ------------------------------------------------------------------------

	var _pageLoad = function _pageLoad() {
		// show page content
		$this.delay(300).fadeIn(200, function () {
			$this.removeClass('hidden');
		});
	};

	var _pageUnload = function _pageUnload() {
		$('body').fadeOut(100); // Hide the entire body tag
	};

	/**
  * Indicates if the current page is contained in excluded pages array.
  * @return {boolean}
  */
	var _isExcludedPage = function _isExcludedPage() {
		var currentFile, found, result;

		currentFile = jse.libs.url_arguments.getCurrentFile();
		found = excludedPages.indexOf(currentFile);

		return found !== -1 ? true : false;
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {

		//_pageLoad();

		$(window).on('beforeunload', function () {
			if (!_isExcludedPage()) {
				_pageUnload();
			}
		});

		$('body').on('JSENGINE_INIT_FINISHED', function () {
			$this.fadeIn(200, function () {
				$this.removeClass('hidden');
			});
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInBhZ2VfbG9hZGluZy5qcyJdLCJuYW1lcyI6WyJneCIsImNvbXBhdGliaWxpdHkiLCJtb2R1bGUiLCJkYXRhIiwiJHRoaXMiLCIkIiwiZGVmYXVsdHMiLCJvcHRpb25zIiwiZXh0ZW5kIiwiZXhjbHVkZWRQYWdlcyIsIl9wYWdlTG9hZCIsImRlbGF5IiwiZmFkZUluIiwicmVtb3ZlQ2xhc3MiLCJfcGFnZVVubG9hZCIsImZhZGVPdXQiLCJfaXNFeGNsdWRlZFBhZ2UiLCJjdXJyZW50RmlsZSIsImZvdW5kIiwicmVzdWx0IiwianNlIiwibGlicyIsInVybF9hcmd1bWVudHMiLCJnZXRDdXJyZW50RmlsZSIsImluZGV4T2YiLCJpbml0IiwiZG9uZSIsIndpbmRvdyIsIm9uIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7OztBQVNBQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLGNBREQsRUFHQyxFQUhEOztBQUtDOztBQUVBLFVBQVNDLElBQVQsRUFBZTs7QUFFZDs7QUFFQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQzs7Ozs7QUFLQUMsU0FBUUMsRUFBRSxJQUFGLENBTlQ7OztBQVFDOzs7OztBQUtBQyxZQUFXLEVBYlo7OztBQWVDOzs7OztBQUtBQyxXQUFVRixFQUFFRyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJGLFFBQW5CLEVBQTZCSCxJQUE3QixDQXBCWDs7O0FBc0JDOzs7O0FBSUFNLGlCQUFnQixDQUNmLFlBRGUsRUFFZix5QkFGZSxFQUdmLGtCQUhlLENBMUJqQjs7O0FBZ0NDOzs7OztBQUtBUCxVQUFTLEVBckNWOztBQXVDQTtBQUNBO0FBQ0E7O0FBRUEsS0FBSVEsWUFBWSxTQUFaQSxTQUFZLEdBQVc7QUFDMUI7QUFDQU4sUUFBTU8sS0FBTixDQUFZLEdBQVosRUFBaUJDLE1BQWpCLENBQXdCLEdBQXhCLEVBQTZCLFlBQVc7QUFDdkNSLFNBQU1TLFdBQU4sQ0FBa0IsUUFBbEI7QUFDQSxHQUZEO0FBR0EsRUFMRDs7QUFPQSxLQUFJQyxjQUFjLFNBQWRBLFdBQWMsR0FBVztBQUM1QlQsSUFBRSxNQUFGLEVBQVVVLE9BQVYsQ0FBa0IsR0FBbEIsRUFENEIsQ0FDSjtBQUN4QixFQUZEOztBQUlBOzs7O0FBSUEsS0FBSUMsa0JBQWtCLFNBQWxCQSxlQUFrQixHQUFXO0FBQ2hDLE1BQUlDLFdBQUosRUFBaUJDLEtBQWpCLEVBQXdCQyxNQUF4Qjs7QUFFQUYsZ0JBQWNHLElBQUlDLElBQUosQ0FBU0MsYUFBVCxDQUF1QkMsY0FBdkIsRUFBZDtBQUNBTCxVQUFRVCxjQUFjZSxPQUFkLENBQXNCUCxXQUF0QixDQUFSOztBQUVBLFNBQU9DLFVBQVUsQ0FBQyxDQUFYLEdBQWUsSUFBZixHQUFzQixLQUE3QjtBQUNBLEVBUEQ7O0FBU0E7QUFDQTtBQUNBOztBQUVBaEIsUUFBT3VCLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCOztBQUVBckIsSUFBRXNCLE1BQUYsRUFBVUMsRUFBVixDQUFhLGNBQWIsRUFBNkIsWUFBVztBQUN2QyxPQUFJLENBQUNaLGlCQUFMLEVBQXdCO0FBQ3ZCRjtBQUNBO0FBQ0QsR0FKRDs7QUFPQVQsSUFBRSxNQUFGLEVBQVV1QixFQUFWLENBQWEsd0JBQWIsRUFBdUMsWUFBVztBQUNqRHhCLFNBQU1RLE1BQU4sQ0FBYSxHQUFiLEVBQWtCLFlBQVc7QUFDNUJSLFVBQU1TLFdBQU4sQ0FBa0IsUUFBbEI7QUFDQSxJQUZEO0FBR0EsR0FKRDs7QUFNQWE7QUFDQSxFQWxCRDs7QUFvQkEsUUFBT3hCLE1BQVA7QUFDQSxDQTNHRiIsImZpbGUiOiJwYWdlX2xvYWRpbmcuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuIHBhZ2VfbG9hZGluZy5qcyAyMDE1LTA5LTE3IGdtXG4gR2FtYmlvIEdtYkhcbiBodHRwOi8vd3d3LmdhbWJpby5kZVxuIENvcHlyaWdodCAoYykgMjAxNSBHYW1iaW8gR21iSFxuIFJlbGVhc2VkIHVuZGVyIHRoZSBHTlUgR2VuZXJhbCBQdWJsaWMgTGljZW5zZSAoVmVyc2lvbiAyKVxuIFtodHRwOi8vd3d3LmdudS5vcmcvbGljZW5zZXMvZ3BsLTIuMC5odG1sXVxuIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gKi9cblxuLyoqXG4gKiAjIyBQYWdlIExvYWRpbmcgTW9kdWxlXG4gKlxuICogVGhpcyBtb2R1bGUgd2lsbCBkaXNwbGF5IGEgbG9hZGluZyBwYWdlIHNjcmVlbiBmb3IgYXBwcm94aW1hdGVseSAxIHNlY29uZCxcbiAqIHRoZSB0aW1lIG5lZWRlZCBieSB0aGUgZW5naW5lIGZvciB0aGUgY29udmVyc2lvbi4gSXQgd2lsbCBhbHNvIGZhZGUgdGhlIHBhZ2VcbiAqIG91dCB3aGVuIHRoZSB1c2VyIGxlYXZlcyBhIHBhZ2UsIG1ha2luZyB0aGUgdHJhbnNpdGlvbiB2ZXJ5IHNtb290aC5cbiAqXG4gKiBAbW9kdWxlIENvbXBhdGliaWxpdHkvcGFnZV9sb2FkaW5nXG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQncGFnZV9sb2FkaW5nJyxcblx0XG5cdFtdLFxuXHRcblx0LyoqICBAbGVuZHMgbW9kdWxlOkNvbXBhdGliaWxpdHkvcGFnZV9sb2FkaW5nICovXG5cdFxuXHRmdW5jdGlvbihkYXRhKSB7XG5cdFx0XG5cdFx0J3VzZSBzdHJpY3QnO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIFZBUklBQkxFUyBERUZJTklUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRmluYWwgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB2YXIge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogRXhjbHVkZWQgcGFnZXMgdG8gcHJldmVudCB3aGl0ZSBmYWRlIGluLlxuXHRcdFx0ICogQHR5cGUge3N0cmluZ1tdfVxuXHRcdFx0ICovXG5cdFx0XHRleGNsdWRlZFBhZ2VzID0gW1xuXHRcdFx0XHQnYmFja3VwLnBocCcsXG5cdFx0XHRcdCdnbV9iYWNrdXBfZmlsZXNfemlwLnBocCcsXG5cdFx0XHRcdCdvcmRlcnNfaWxveHgucGhwJ1xuXHRcdFx0XSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2R1bGUgT2JqZWN0XG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bW9kdWxlID0ge307XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gUFJJVkFURSBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX3BhZ2VMb2FkID0gZnVuY3Rpb24oKSB7XG5cdFx0XHQvLyBzaG93IHBhZ2UgY29udGVudFxuXHRcdFx0JHRoaXMuZGVsYXkoMzAwKS5mYWRlSW4oMjAwLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JHRoaXMucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0fSk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3BhZ2VVbmxvYWQgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQoJ2JvZHknKS5mYWRlT3V0KDEwMCk7IC8vIEhpZGUgdGhlIGVudGlyZSBib2R5IHRhZ1xuXHRcdH07XG5cdFx0XG5cdFx0LyoqXG5cdFx0ICogSW5kaWNhdGVzIGlmIHRoZSBjdXJyZW50IHBhZ2UgaXMgY29udGFpbmVkIGluIGV4Y2x1ZGVkIHBhZ2VzIGFycmF5LlxuXHRcdCAqIEByZXR1cm4ge2Jvb2xlYW59XG5cdFx0ICovXG5cdFx0dmFyIF9pc0V4Y2x1ZGVkUGFnZSA9IGZ1bmN0aW9uKCkge1xuXHRcdFx0dmFyIGN1cnJlbnRGaWxlLCBmb3VuZCwgcmVzdWx0O1xuXHRcdFx0XG5cdFx0XHRjdXJyZW50RmlsZSA9IGpzZS5saWJzLnVybF9hcmd1bWVudHMuZ2V0Q3VycmVudEZpbGUoKTtcblx0XHRcdGZvdW5kID0gZXhjbHVkZWRQYWdlcy5pbmRleE9mKGN1cnJlbnRGaWxlKTtcblx0XHRcdFxuXHRcdFx0cmV0dXJuIGZvdW5kICE9PSAtMSA/IHRydWUgOiBmYWxzZTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRcblx0XHRcdC8vX3BhZ2VMb2FkKCk7XG5cdFx0XHRcblx0XHRcdCQod2luZG93KS5vbignYmVmb3JldW5sb2FkJywgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICghX2lzRXhjbHVkZWRQYWdlKCkpIHtcblx0XHRcdFx0XHRfcGFnZVVubG9hZCgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0XG5cdFx0XHQkKCdib2R5Jykub24oJ0pTRU5HSU5FX0lOSVRfRklOSVNIRUQnLCBmdW5jdGlvbigpIHtcblx0XHRcdFx0JHRoaXMuZmFkZUluKDIwMCwgZnVuY3Rpb24oKSB7XG5cdFx0XHRcdFx0JHRoaXMucmVtb3ZlQ2xhc3MoJ2hpZGRlbicpO1xuXHRcdFx0XHR9KTtcblx0XHRcdH0pO1xuXHRcdFx0XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
