'use strict';

/* --------------------------------------------------------------
 bookmarks_nav_tabs.js 2015-09-29 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Categories Modal Layer Module
 *
 * This module will open a modal layer for categories/articles actions like deleting the article.
 *
 * @module Compatibility/categories_modal_layer
 */
gx.compatibility.module('bookmarks_nav_tabs', [],

/**  @lends module:Compatibility/bookmarks_nav_tabs */

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
  * Parent container table, which contains this part and the buttons
  * @type {object}
  */
	$container = $(this).parents('table:first'),


	/**
  * Modal Selector
  *
  * @type {object}
  */
	$modal = $('#modal_layer_container'),


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = {},


	/**
  * Link of the activated tab
  *
  * @type {object}
  */
	link = '',


	/**
  * Link of the activated tab
  *
  * @type {object}
  */
	onClickValue = '',


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

	// Timeout is needed, else, some elements won't be found
	setTimeout(function () {
		$('.nav-tab').on('click', function (event) {

			if (link && onClickValue) {
				$('.no-link').wrapInner('<a></a>').removeClass('no-link').children().attr('href', link).attr('onclick', onClickValue);
			}

			link = $(this).children().attr('href');
			onClickValue = $(this).children().attr('onclick');

			$(this).addClass('no-link');
			$(this).css('text-align', 'center');
			$(this).children().contents().unwrap();
		});
	}, 1000);

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImJvb2ttYXJrcy9ib29rbWFya3NfbmF2X3RhYnMuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRjb250YWluZXIiLCJwYXJlbnRzIiwiJG1vZGFsIiwiZGVmYXVsdHMiLCJsaW5rIiwib25DbGlja1ZhbHVlIiwib3B0aW9ucyIsImV4dGVuZCIsInNldFRpbWVvdXQiLCJvbiIsImV2ZW50Iiwid3JhcElubmVyIiwicmVtb3ZlQ2xhc3MiLCJjaGlsZHJlbiIsImF0dHIiLCJhZGRDbGFzcyIsImNzcyIsImNvbnRlbnRzIiwidW53cmFwIiwiaW5pdCIsImRvbmUiXSwibWFwcGluZ3MiOiI7O0FBQUE7Ozs7Ozs7Ozs7QUFVQTs7Ozs7OztBQU9BQSxHQUFHQyxhQUFILENBQWlCQyxNQUFqQixDQUNDLG9CQURELEVBR0MsRUFIRDs7QUFLQzs7QUFFQSxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7OztBQUlBQyxjQUFhRCxFQUFFLElBQUYsRUFBUUUsT0FBUixDQUFnQixhQUFoQixDQVpkOzs7QUFjQzs7Ozs7QUFLQUMsVUFBU0gsRUFBRSx3QkFBRixDQW5CVjs7O0FBcUJDOzs7OztBQUtBSSxZQUFXLEVBMUJaOzs7QUE0QkM7Ozs7O0FBS0FDLFFBQU8sRUFqQ1I7OztBQW1DQzs7Ozs7QUFLQUMsZ0JBQWUsRUF4Q2hCOzs7QUEwQ0M7Ozs7O0FBS0FDLFdBQVVQLEVBQUVRLE1BQUYsQ0FBUyxJQUFULEVBQWUsRUFBZixFQUFtQkosUUFBbkIsRUFBNkJOLElBQTdCLENBL0NYOzs7QUFpREM7Ozs7O0FBS0FELFVBQVMsRUF0RFY7O0FBd0RBO0FBQ0E7QUFDQTs7QUFFQTtBQUNBWSxZQUFXLFlBQVc7QUFDckJULElBQUUsVUFBRixFQUFjVSxFQUFkLENBQWlCLE9BQWpCLEVBQTBCLFVBQVNDLEtBQVQsRUFBZ0I7O0FBRXpDLE9BQUlOLFFBQVFDLFlBQVosRUFBMEI7QUFDekJOLE1BQUUsVUFBRixFQUNFWSxTQURGLENBQ1ksU0FEWixFQUVFQyxXQUZGLENBRWMsU0FGZCxFQUdFQyxRQUhGLEdBSUVDLElBSkYsQ0FJTyxNQUpQLEVBSWVWLElBSmYsRUFLRVUsSUFMRixDQUtPLFNBTFAsRUFLa0JULFlBTGxCO0FBTUE7O0FBRURELFVBQU9MLEVBQUUsSUFBRixFQUFRYyxRQUFSLEdBQW1CQyxJQUFuQixDQUF3QixNQUF4QixDQUFQO0FBQ0FULGtCQUFlTixFQUFFLElBQUYsRUFBUWMsUUFBUixHQUFtQkMsSUFBbkIsQ0FBd0IsU0FBeEIsQ0FBZjs7QUFFQWYsS0FBRSxJQUFGLEVBQVFnQixRQUFSLENBQWlCLFNBQWpCO0FBQ0FoQixLQUFFLElBQUYsRUFBUWlCLEdBQVIsQ0FBWSxZQUFaLEVBQTBCLFFBQTFCO0FBQ0FqQixLQUFFLElBQUYsRUFBUWMsUUFBUixHQUFtQkksUUFBbkIsR0FBOEJDLE1BQTlCO0FBQ0EsR0FqQkQ7QUFrQkEsRUFuQkQsRUFtQkcsSUFuQkg7O0FBcUJBO0FBQ0E7QUFDQTs7QUFFQXRCLFFBQU91QixJQUFQLEdBQWMsVUFBU0MsSUFBVCxFQUFlO0FBQzVCQTtBQUNBLEVBRkQ7O0FBSUEsUUFBT3hCLE1BQVA7QUFDQSxDQTFHRiIsImZpbGUiOiJib29rbWFya3MvYm9va21hcmtzX25hdl90YWJzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBib29rbWFya3NfbmF2X3RhYnMuanMgMjAxNS0wOS0yOSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQ2F0ZWdvcmllcyBNb2RhbCBMYXllciBNb2R1bGVcbiAqXG4gKiBUaGlzIG1vZHVsZSB3aWxsIG9wZW4gYSBtb2RhbCBsYXllciBmb3IgY2F0ZWdvcmllcy9hcnRpY2xlcyBhY3Rpb25zIGxpa2UgZGVsZXRpbmcgdGhlIGFydGljbGUuXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L2NhdGVnb3JpZXNfbW9kYWxfbGF5ZXJcbiAqL1xuZ3guY29tcGF0aWJpbGl0eS5tb2R1bGUoXG5cdCdib29rbWFya3NfbmF2X3RhYnMnLFxuXHRcblx0W10sXG5cdFxuXHQvKiogIEBsZW5kcyBtb2R1bGU6Q29tcGF0aWJpbGl0eS9ib29rbWFya3NfbmF2X3RhYnMgKi9cblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEVTIERFRklOSVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXJcblx0XHRcdC8qKlxuXHRcdFx0ICogTW9kdWxlIFNlbGVjdG9yXG5cdFx0XHQgKlxuXHRcdFx0ICogQHZhciB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHQkdGhpcyA9ICQodGhpcyksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogUGFyZW50IGNvbnRhaW5lciB0YWJsZSwgd2hpY2ggY29udGFpbnMgdGhpcyBwYXJ0IGFuZCB0aGUgYnV0dG9uc1xuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JGNvbnRhaW5lciA9ICQodGhpcykucGFyZW50cygndGFibGU6Zmlyc3QnKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBNb2RhbCBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRtb2RhbCA9ICQoJyNtb2RhbF9sYXllcl9jb250YWluZXInKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBEZWZhdWx0IE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRkZWZhdWx0cyA9IHt9LFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIExpbmsgb2YgdGhlIGFjdGl2YXRlZCB0YWJcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRsaW5rID0gJycsXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogTGluayBvZiB0aGUgYWN0aXZhdGVkIHRhYlxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9uQ2xpY2tWYWx1ZSA9ICcnLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIEZpbmFsIE9wdGlvbnNcblx0XHRcdCAqXG5cdFx0XHQgKiBAdmFyIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBPUEVSQVRJT05TXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0Ly8gVGltZW91dCBpcyBuZWVkZWQsIGVsc2UsIHNvbWUgZWxlbWVudHMgd29uJ3QgYmUgZm91bmRcblx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xuXHRcdFx0JCgnLm5hdi10YWInKS5vbignY2xpY2snLCBmdW5jdGlvbihldmVudCkge1xuXHRcdFx0XHRcblx0XHRcdFx0aWYgKGxpbmsgJiYgb25DbGlja1ZhbHVlKSB7XG5cdFx0XHRcdFx0JCgnLm5vLWxpbmsnKVxuXHRcdFx0XHRcdFx0LndyYXBJbm5lcignPGE+PC9hPicpXG5cdFx0XHRcdFx0XHQucmVtb3ZlQ2xhc3MoJ25vLWxpbmsnKVxuXHRcdFx0XHRcdFx0LmNoaWxkcmVuKClcblx0XHRcdFx0XHRcdC5hdHRyKCdocmVmJywgbGluaylcblx0XHRcdFx0XHRcdC5hdHRyKCdvbmNsaWNrJywgb25DbGlja1ZhbHVlKTtcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0bGluayA9ICQodGhpcykuY2hpbGRyZW4oKS5hdHRyKCdocmVmJyk7XG5cdFx0XHRcdG9uQ2xpY2tWYWx1ZSA9ICQodGhpcykuY2hpbGRyZW4oKS5hdHRyKCdvbmNsaWNrJyk7XG5cdFx0XHRcdFxuXHRcdFx0XHQkKHRoaXMpLmFkZENsYXNzKCduby1saW5rJyk7XG5cdFx0XHRcdCQodGhpcykuY3NzKCd0ZXh0LWFsaWduJywgJ2NlbnRlcicpO1xuXHRcdFx0XHQkKHRoaXMpLmNoaWxkcmVuKCkuY29udGVudHMoKS51bndyYXAoKTtcblx0XHRcdH0pO1xuXHRcdH0sIDEwMDApO1xuXHRcdFxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdC8vIElOSVRJQUxJWkFUSU9OXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0bW9kdWxlLmluaXQgPSBmdW5jdGlvbihkb25lKSB7XG5cdFx0XHRkb25lKCk7XG5cdFx0fTtcblx0XHRcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
