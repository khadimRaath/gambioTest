'use strict';

/* --------------------------------------------------------------
 ajax_search.js 2015-10-15 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## AJAX Search Extension
 *
 * Enables the AJAX search and display for an element. This extension is used along with text_edit.js and 
 * ajax_search.js in the Gambio Admin "Text Edit | Texte Anpassen" page.
 *
 * @module Admin/Extensions/ajax_search
 * @ignore
 */
gx.extensions.module('ajax_search', ['form'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLE DEFINITION
	// ------------------------------------------------------------------------

	var
	/**
  * Extension Reference
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Default Options for Extension.
  *
  * @type {object}
  */
	defaults = {},


	/**
  * AJAX URL
  *
  * @type {string}
  */
	url = $this.attr('action'),


	/**
  * Final Extension Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// META INITIALIZE
	// ------------------------------------------------------------------------

	/**
  * Initialize function of the extension, called by the engine.
  */
	module.init = function (done) {

		var template = $(options.template).html(),
		    $target = $(options.target);

		$this.on('submit', function (event) {
			event.preventDefault();

			var data = jse.libs.form.getData($this);

			// Check for required fields.
			var abort = false;
			$this.find('[required]').each(function () {
				if ($(this).val() === '') {
					abort = true;
					return false; // exit $.each loop
				}
			});
			if (abort) {
				return; // abort because there is a missing field
			}

			$.ajax({
				'url': url,
				'method': 'post',
				'dataType': 'json',
				'data': data,
				'page_token': jse.core.config.get('pageToken')
			}).done(function (result) {
				var markup = Mustache.render(template, result.payload);
				$target.empty().append(markup).parent().show();
			});
		});

		done();
	};

	// Return data to module engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImFqYXhfc2VhcmNoLmpzIl0sIm5hbWVzIjpbImd4IiwiZXh0ZW5zaW9ucyIsIm1vZHVsZSIsImRhdGEiLCIkdGhpcyIsIiQiLCJkZWZhdWx0cyIsInVybCIsImF0dHIiLCJvcHRpb25zIiwiZXh0ZW5kIiwiaW5pdCIsImRvbmUiLCJ0ZW1wbGF0ZSIsImh0bWwiLCIkdGFyZ2V0IiwidGFyZ2V0Iiwib24iLCJldmVudCIsInByZXZlbnREZWZhdWx0IiwianNlIiwibGlicyIsImZvcm0iLCJnZXREYXRhIiwiYWJvcnQiLCJmaW5kIiwiZWFjaCIsInZhbCIsImFqYXgiLCJjb3JlIiwiY29uZmlnIiwiZ2V0IiwicmVzdWx0IiwibWFya3VwIiwiTXVzdGFjaGUiLCJyZW5kZXIiLCJwYXlsb2FkIiwiZW1wdHkiLCJhcHBlbmQiLCJwYXJlbnQiLCJzaG93Il0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7OztBQVNBQSxHQUFHQyxVQUFILENBQWNDLE1BQWQsQ0FDQyxhQURELEVBR0MsQ0FBQyxNQUFELENBSEQsRUFLQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsWUFBVyxFQWJaOzs7QUFlQzs7Ozs7QUFLQUMsT0FBTUgsTUFBTUksSUFBTixDQUFXLFFBQVgsQ0FwQlA7OztBQXNCQzs7Ozs7QUFLQUMsV0FBVUosRUFBRUssTUFBRixDQUFTLElBQVQsRUFBZSxFQUFmLEVBQW1CSixRQUFuQixFQUE2QkgsSUFBN0IsQ0EzQlg7OztBQTZCQzs7Ozs7QUFLQUQsVUFBUyxFQWxDVjs7QUFvQ0E7QUFDQTtBQUNBOztBQUVBOzs7QUFHQUEsUUFBT1MsSUFBUCxHQUFjLFVBQVNDLElBQVQsRUFBZTs7QUFFNUIsTUFBSUMsV0FBV1IsRUFBRUksUUFBUUksUUFBVixFQUFvQkMsSUFBcEIsRUFBZjtBQUFBLE1BQ0NDLFVBQVVWLEVBQUVJLFFBQVFPLE1BQVYsQ0FEWDs7QUFHQVosUUFBTWEsRUFBTixDQUFTLFFBQVQsRUFBbUIsVUFBU0MsS0FBVCxFQUFnQjtBQUNsQ0EsU0FBTUMsY0FBTjs7QUFFQSxPQUFJaEIsT0FBT2lCLElBQUlDLElBQUosQ0FBU0MsSUFBVCxDQUFjQyxPQUFkLENBQXNCbkIsS0FBdEIsQ0FBWDs7QUFFQTtBQUNBLE9BQUlvQixRQUFRLEtBQVo7QUFDQXBCLFNBQU1xQixJQUFOLENBQVcsWUFBWCxFQUF5QkMsSUFBekIsQ0FBOEIsWUFBVztBQUN4QyxRQUFJckIsRUFBRSxJQUFGLEVBQVFzQixHQUFSLE9BQWtCLEVBQXRCLEVBQTBCO0FBQ3pCSCxhQUFRLElBQVI7QUFDQSxZQUFPLEtBQVAsQ0FGeUIsQ0FFWDtBQUNkO0FBQ0QsSUFMRDtBQU1BLE9BQUlBLEtBQUosRUFBVztBQUNWLFdBRFUsQ0FDRjtBQUNSOztBQUVEbkIsS0FBRXVCLElBQUYsQ0FBTztBQUNOLFdBQU9yQixHQUREO0FBRU4sY0FBVSxNQUZKO0FBR04sZ0JBQVksTUFITjtBQUlOLFlBQVFKLElBSkY7QUFLTixrQkFBY2lCLElBQUlTLElBQUosQ0FBU0MsTUFBVCxDQUFnQkMsR0FBaEIsQ0FBb0IsV0FBcEI7QUFMUixJQUFQLEVBTUduQixJQU5ILENBTVEsVUFBU29CLE1BQVQsRUFBaUI7QUFDeEIsUUFBSUMsU0FBU0MsU0FBU0MsTUFBVCxDQUFnQnRCLFFBQWhCLEVBQTBCbUIsT0FBT0ksT0FBakMsQ0FBYjtBQUNBckIsWUFDRXNCLEtBREYsR0FFRUMsTUFGRixDQUVTTCxNQUZULEVBR0VNLE1BSEYsR0FJRUMsSUFKRjtBQUtBLElBYkQ7QUFlQSxHQWhDRDs7QUFrQ0E1QjtBQUNBLEVBeENEOztBQTBDQTtBQUNBLFFBQU9WLE1BQVA7QUFDQSxDQXBHRiIsImZpbGUiOiJhamF4X3NlYXJjaC5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gYWpheF9zZWFyY2guanMgMjAxNS0xMC0xNSBnbVxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTUgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgQUpBWCBTZWFyY2ggRXh0ZW5zaW9uXG4gKlxuICogRW5hYmxlcyB0aGUgQUpBWCBzZWFyY2ggYW5kIGRpc3BsYXkgZm9yIGFuIGVsZW1lbnQuIFRoaXMgZXh0ZW5zaW9uIGlzIHVzZWQgYWxvbmcgd2l0aCB0ZXh0X2VkaXQuanMgYW5kIFxuICogYWpheF9zZWFyY2guanMgaW4gdGhlIEdhbWJpbyBBZG1pbiBcIlRleHQgRWRpdCB8IFRleHRlIEFucGFzc2VuXCIgcGFnZS5cbiAqXG4gKiBAbW9kdWxlIEFkbWluL0V4dGVuc2lvbnMvYWpheF9zZWFyY2hcbiAqIEBpZ25vcmVcbiAqL1xuZ3guZXh0ZW5zaW9ucy5tb2R1bGUoXG5cdCdhamF4X3NlYXJjaCcsXG5cdFxuXHRbJ2Zvcm0nXSxcblx0XG5cdGZ1bmN0aW9uKGRhdGEpIHtcblx0XHRcblx0XHQndXNlIHN0cmljdCc7XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gVkFSSUFCTEUgREVGSU5JVElPTlxuXHRcdC8vIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuXHRcdFxuXHRcdHZhclxuXHRcdFx0LyoqXG5cdFx0XHQgKiBFeHRlbnNpb24gUmVmZXJlbmNlXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0JHRoaXMgPSAkKHRoaXMpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9ucyBmb3IgRXh0ZW5zaW9uLlxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge30sXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogQUpBWCBVUkxcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7c3RyaW5nfVxuXHRcdFx0ICovXG5cdFx0XHR1cmwgPSAkdGhpcy5hdHRyKCdhY3Rpb24nKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBFeHRlbnNpb24gT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdG9wdGlvbnMgPSAkLmV4dGVuZCh0cnVlLCB7fSwgZGVmYXVsdHMsIGRhdGEpLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBNRVRBIElOSVRJQUxJWkVcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHQvKipcblx0XHQgKiBJbml0aWFsaXplIGZ1bmN0aW9uIG9mIHRoZSBleHRlbnNpb24sIGNhbGxlZCBieSB0aGUgZW5naW5lLlxuXHRcdCAqL1xuXHRcdG1vZHVsZS5pbml0ID0gZnVuY3Rpb24oZG9uZSkge1xuXHRcdFx0XG5cdFx0XHR2YXIgdGVtcGxhdGUgPSAkKG9wdGlvbnMudGVtcGxhdGUpLmh0bWwoKSxcblx0XHRcdFx0JHRhcmdldCA9ICQob3B0aW9ucy50YXJnZXQpO1xuXHRcdFx0XG5cdFx0XHQkdGhpcy5vbignc3VibWl0JywgZnVuY3Rpb24oZXZlbnQpIHtcblx0XHRcdFx0ZXZlbnQucHJldmVudERlZmF1bHQoKTtcblx0XHRcdFx0XG5cdFx0XHRcdHZhciBkYXRhID0ganNlLmxpYnMuZm9ybS5nZXREYXRhKCR0aGlzKTtcblx0XHRcdFx0XG5cdFx0XHRcdC8vIENoZWNrIGZvciByZXF1aXJlZCBmaWVsZHMuXG5cdFx0XHRcdHZhciBhYm9ydCA9IGZhbHNlO1xuXHRcdFx0XHQkdGhpcy5maW5kKCdbcmVxdWlyZWRdJykuZWFjaChmdW5jdGlvbigpIHtcblx0XHRcdFx0XHRpZiAoJCh0aGlzKS52YWwoKSA9PT0gJycpIHtcblx0XHRcdFx0XHRcdGFib3J0ID0gdHJ1ZTtcblx0XHRcdFx0XHRcdHJldHVybiBmYWxzZTsgLy8gZXhpdCAkLmVhY2ggbG9vcFxuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSk7XG5cdFx0XHRcdGlmIChhYm9ydCkge1xuXHRcdFx0XHRcdHJldHVybjsgLy8gYWJvcnQgYmVjYXVzZSB0aGVyZSBpcyBhIG1pc3NpbmcgZmllbGRcblx0XHRcdFx0fVxuXHRcdFx0XHRcblx0XHRcdFx0JC5hamF4KHtcblx0XHRcdFx0XHQndXJsJzogdXJsLFxuXHRcdFx0XHRcdCdtZXRob2QnOiAncG9zdCcsXG5cdFx0XHRcdFx0J2RhdGFUeXBlJzogJ2pzb24nLFxuXHRcdFx0XHRcdCdkYXRhJzogZGF0YSxcblx0XHRcdFx0XHQncGFnZV90b2tlbic6IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpXG5cdFx0XHRcdH0pLmRvbmUoZnVuY3Rpb24ocmVzdWx0KSB7XG5cdFx0XHRcdFx0dmFyIG1hcmt1cCA9IE11c3RhY2hlLnJlbmRlcih0ZW1wbGF0ZSwgcmVzdWx0LnBheWxvYWQpO1xuXHRcdFx0XHRcdCR0YXJnZXRcblx0XHRcdFx0XHRcdC5lbXB0eSgpXG5cdFx0XHRcdFx0XHQuYXBwZW5kKG1hcmt1cClcblx0XHRcdFx0XHRcdC5wYXJlbnQoKVxuXHRcdFx0XHRcdFx0LnNob3coKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcdFxuXHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdGRvbmUoKTtcblx0XHR9O1xuXHRcdFxuXHRcdC8vIFJldHVybiBkYXRhIHRvIG1vZHVsZSBlbmdpbmVcblx0XHRyZXR1cm4gbW9kdWxlO1xuXHR9KTtcbiJdfQ==
