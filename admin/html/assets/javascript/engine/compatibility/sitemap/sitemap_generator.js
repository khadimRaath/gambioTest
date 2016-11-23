'use strict';

/* --------------------------------------------------------------
 sitemap_generator.js 2016-08-22
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2016 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * ## Sitemap Generator Controller
 *
 * This module will execute the sitemap generation
 *
 * @module Compatibility/sitemap_generator
 */
gx.compatibility.module('sitemap_generator', [gx.source + '/libs/info_messages', 'loading_spinner'], function (data) {

	'use strict';

	// ------------------------------------------------------------------------
	// VARIABLES 
	// ------------------------------------------------------------------------

	var
	/**
  * Module Selector
  *
  * @type {object}
  */
	$this = $(this),


	/**
  * Loading Spinner Selector 
  * 
  * @type {object}
  */
	$spinner,


	/**
  * Default Options
  *
  * @type {object}
  */
	defaults = { url: 'gm_sitemap_creator.php' },


	/**
  * Final Options
  *
  * @type {object}
  */
	options = $.extend(true, {}, defaults, data),


	/**
  * Reference to the info messages library
  * 
  * @type {object}
  */
	messages = jse.libs.info_messages,


	/**
  * Module Object
  *
  * @type {object}
  */
	module = {};

	// ------------------------------------------------------------------------
	// FUNCTIONS
	// ------------------------------------------------------------------------

	var _createSitemapXml = function _createSitemapXml() {
		$.ajax({
			url: options.url,
			data: options.params
		})
		// On success
		.done(function (response) {
			messages.addSuccess(response);
			jse.libs.loading_spinner.hide($spinner);
		})
		// On Failure
		.fail(function (response) {
			jse.core.debug.error('Prepare Content Error: ', response);
		});
	};

	var _prepareCategories = function _prepareCategories(deferred) {
		deferred = deferred || $.Deferred();

		$.ajax({
			url: options.url,
			data: {
				action: 'prepare_categories',
				page_token: jse.core.config.get('pageToken')
			},
			dataType: 'json'
		})
		// On success
		.done(function (response) {
			if (response.repeat === true) {
				_prepareCategories(deferred);
			} else {
				deferred.resolve();
			}
		})
		// On Failure
		.fail(function (response) {
			jse.core.debug.error('Prepare Categories Error: ', response);
			deferred.reject();
		});

		return deferred.promise();
	};

	var _prepareContent = function _prepareContent(deferred) {
		deferred = deferred || $.Deferred();

		$.ajax({
			url: options.url,
			data: {
				action: 'prepare_content',
				page_token: jse.core.config.get('pageToken')
			},
			dataType: 'json'
		})
		// On success
		.done(function (response) {
			if (response.repeat === true) {
				_prepareContent(deferred);
			} else {
				deferred.resolve();
			}
		})
		// On Failure
		.fail(function (response) {
			jse.core.debug.error('Prepare Content Error: ', response);
			deferred.reject();
		});

		return deferred.promise();
	};

	// ------------------------------------------------------------------------
	// INITIALIZATION
	// ------------------------------------------------------------------------

	module.init = function (done) {
		$this.on('click', function () {
			$spinner = jse.libs.loading_spinner.show($this.parents().eq(2));
			$.when(_prepareCategories(), _prepareContent()).done(_createSitemapXml);
			$this.blur();
			return false;
		});

		done();
	};

	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbInNpdGVtYXAvc2l0ZW1hcF9nZW5lcmF0b3IuanMiXSwibmFtZXMiOlsiZ3giLCJjb21wYXRpYmlsaXR5IiwibW9kdWxlIiwic291cmNlIiwiZGF0YSIsIiR0aGlzIiwiJCIsIiRzcGlubmVyIiwiZGVmYXVsdHMiLCJ1cmwiLCJvcHRpb25zIiwiZXh0ZW5kIiwibWVzc2FnZXMiLCJqc2UiLCJsaWJzIiwiaW5mb19tZXNzYWdlcyIsIl9jcmVhdGVTaXRlbWFwWG1sIiwiYWpheCIsInBhcmFtcyIsImRvbmUiLCJyZXNwb25zZSIsImFkZFN1Y2Nlc3MiLCJsb2FkaW5nX3NwaW5uZXIiLCJoaWRlIiwiZmFpbCIsImNvcmUiLCJkZWJ1ZyIsImVycm9yIiwiX3ByZXBhcmVDYXRlZ29yaWVzIiwiZGVmZXJyZWQiLCJEZWZlcnJlZCIsImFjdGlvbiIsInBhZ2VfdG9rZW4iLCJjb25maWciLCJnZXQiLCJkYXRhVHlwZSIsInJlcGVhdCIsInJlc29sdmUiLCJyZWplY3QiLCJwcm9taXNlIiwiX3ByZXBhcmVDb250ZW50IiwiaW5pdCIsIm9uIiwic2hvdyIsInBhcmVudHMiLCJlcSIsIndoZW4iLCJibHVyIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7Ozs7QUFPQUEsR0FBR0MsYUFBSCxDQUFpQkMsTUFBakIsQ0FDQyxtQkFERCxFQUdDLENBQ0NGLEdBQUdHLE1BQUgsR0FBWSxxQkFEYixFQUVDLGlCQUZELENBSEQsRUFRQyxVQUFTQyxJQUFULEVBQWU7O0FBRWQ7O0FBRUE7QUFDQTtBQUNBOztBQUVBO0FBQ0M7Ozs7O0FBS0FDLFNBQVFDLEVBQUUsSUFBRixDQU5UOzs7QUFRQzs7Ozs7QUFLQUMsU0FiRDs7O0FBZUM7Ozs7O0FBS0FDLFlBQVcsRUFBQ0MsS0FBSyx3QkFBTixFQXBCWjs7O0FBc0JDOzs7OztBQUtBQyxXQUFVSixFQUFFSyxNQUFGLENBQVMsSUFBVCxFQUFlLEVBQWYsRUFBbUJILFFBQW5CLEVBQTZCSixJQUE3QixDQTNCWDs7O0FB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QUF6QixDQUE4QnRDLE1BQU11QyxPQUFOLEdBQWdCQyxFQUFoQixDQUFtQixDQUFuQixDQUE5QixDQUFYO0FBQ0F2QyxLQUFFd0MsSUFBRixDQUFPbEIsb0JBQVAsRUFBNkJZLGlCQUE3QixFQUFnRHJCLElBQWhELENBQXFESCxpQkFBckQ7QUFDQVgsU0FBTTBDLElBQU47QUFDQSxVQUFPLEtBQVA7QUFDQSxHQUxEOztBQU9BNUI7QUFDQSxFQVREOztBQVdBLFFBQU9qQixNQUFQO0FBQ0EsQ0F2SkYiLCJmaWxlIjoic2l0ZW1hcC9zaXRlbWFwX2dlbmVyYXRvci5qcyIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG4gc2l0ZW1hcF9nZW5lcmF0b3IuanMgMjAxNi0wOC0yMlxuIEdhbWJpbyBHbWJIXG4gaHR0cDovL3d3dy5nYW1iaW8uZGVcbiBDb3B5cmlnaHQgKGMpIDIwMTYgR2FtYmlvIEdtYkhcbiBSZWxlYXNlZCB1bmRlciB0aGUgR05VIEdlbmVyYWwgUHVibGljIExpY2Vuc2UgKFZlcnNpb24gMilcbiBbaHR0cDovL3d3dy5nbnUub3JnL2xpY2Vuc2VzL2dwbC0yLjAuaHRtbF1cbiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLVxuICovXG5cbi8qKlxuICogIyMgU2l0ZW1hcCBHZW5lcmF0b3IgQ29udHJvbGxlclxuICpcbiAqIFRoaXMgbW9kdWxlIHdpbGwgZXhlY3V0ZSB0aGUgc2l0ZW1hcCBnZW5lcmF0aW9uXG4gKlxuICogQG1vZHVsZSBDb21wYXRpYmlsaXR5L3NpdGVtYXBfZ2VuZXJhdG9yXG4gKi9cbmd4LmNvbXBhdGliaWxpdHkubW9kdWxlKFxuXHQnc2l0ZW1hcF9nZW5lcmF0b3InLFxuXHRcblx0W1xuXHRcdGd4LnNvdXJjZSArICcvbGlicy9pbmZvX21lc3NhZ2VzJyxcblx0XHQnbG9hZGluZ19zcGlubmVyJ1xuXHRdLFxuXHRcblx0ZnVuY3Rpb24oZGF0YSkge1xuXHRcdFxuXHRcdCd1c2Ugc3RyaWN0Jztcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBWQVJJQUJMRVMgXG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0XG5cdFx0dmFyXG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBTZWxlY3RvclxuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCR0aGlzID0gJCh0aGlzKSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBMb2FkaW5nIFNwaW5uZXIgU2VsZWN0b3IgXG5cdFx0XHQgKiBcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdCRzcGlubmVyLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIERlZmF1bHQgT3B0aW9uc1xuXHRcdFx0ICpcblx0XHRcdCAqIEB0eXBlIHtvYmplY3R9XG5cdFx0XHQgKi9cblx0XHRcdGRlZmF1bHRzID0ge3VybDogJ2dtX3NpdGVtYXBfY3JlYXRvci5waHAnfSxcblx0XHRcdFxuXHRcdFx0LyoqXG5cdFx0XHQgKiBGaW5hbCBPcHRpb25zXG5cdFx0XHQgKlxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0b3B0aW9ucyA9ICQuZXh0ZW5kKHRydWUsIHt9LCBkZWZhdWx0cywgZGF0YSksXG5cdFx0XHRcblx0XHRcdC8qKlxuXHRcdFx0ICogUmVmZXJlbmNlIHRvIHRoZSBpbmZvIG1lc3NhZ2VzIGxpYnJhcnlcblx0XHRcdCAqIFxuXHRcdFx0ICogQHR5cGUge29iamVjdH1cblx0XHRcdCAqL1xuXHRcdFx0bWVzc2FnZXMgPSBqc2UubGlicy5pbmZvX21lc3NhZ2VzLFxuXHRcdFx0XG5cdFx0XHQvKipcblx0XHRcdCAqIE1vZHVsZSBPYmplY3Rcblx0XHRcdCAqXG5cdFx0XHQgKiBAdHlwZSB7b2JqZWN0fVxuXHRcdFx0ICovXG5cdFx0XHRtb2R1bGUgPSB7fTtcblx0XHRcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHQvLyBGVU5DVElPTlNcblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHR2YXIgX2NyZWF0ZVNpdGVtYXBYbWwgPSBmdW5jdGlvbigpIHtcblx0XHRcdCQuYWpheCh7XG5cdFx0XHRcdFx0dXJsOiBvcHRpb25zLnVybCxcblx0XHRcdFx0XHRkYXRhOiBvcHRpb25zLnBhcmFtc1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQvLyBPbiBzdWNjZXNzXG5cdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0bWVzc2FnZXMuYWRkU3VjY2VzcyhyZXNwb25zZSk7XG5cdFx0XHRcdFx0anNlLmxpYnMubG9hZGluZ19zcGlubmVyLmhpZGUoJHNwaW5uZXIpO1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQvLyBPbiBGYWlsdXJlXG5cdFx0XHRcdC5mYWlsKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0anNlLmNvcmUuZGVidWcuZXJyb3IoJ1ByZXBhcmUgQ29udGVudCBFcnJvcjogJywgcmVzcG9uc2UpO1xuXHRcdFx0XHR9KTtcblx0XHR9O1xuXHRcdFxuXHRcdHZhciBfcHJlcGFyZUNhdGVnb3JpZXMgPSBmdW5jdGlvbihkZWZlcnJlZCkge1xuXHRcdFx0ZGVmZXJyZWQgPSBkZWZlcnJlZCB8fCAkLkRlZmVycmVkKCk7IFxuXHRcdFx0XG5cdFx0XHQkLmFqYXgoe1xuXHRcdFx0XHRcdHVybDogb3B0aW9ucy51cmwsXG5cdFx0XHRcdFx0ZGF0YToge1xuXHRcdFx0XHRcdFx0YWN0aW9uOiAncHJlcGFyZV9jYXRlZ29yaWVzJyxcblx0XHRcdFx0XHRcdHBhZ2VfdG9rZW46IGpzZS5jb3JlLmNvbmZpZy5nZXQoJ3BhZ2VUb2tlbicpXG5cdFx0XHRcdFx0fSxcblx0XHRcdFx0XHRkYXRhVHlwZTogJ2pzb24nXG5cdFx0XHRcdH0pXG5cdFx0XHRcdC8vIE9uIHN1Y2Nlc3Ncblx0XHRcdFx0LmRvbmUoZnVuY3Rpb24ocmVzcG9uc2UpIHtcblx0XHRcdFx0XHRpZiAocmVzcG9uc2UucmVwZWF0ID09PSB0cnVlKSB7XG5cdFx0XHRcdFx0XHRfcHJlcGFyZUNhdGVnb3JpZXMoZGVmZXJyZWQpOyBcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZSgpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSlcblx0XHRcdFx0Ly8gT24gRmFpbHVyZVxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdQcmVwYXJlIENhdGVnb3JpZXMgRXJyb3I6ICcsIHJlc3BvbnNlKTtcblx0XHRcdFx0XHRkZWZlcnJlZC5yZWplY3QoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7XG5cdFx0fTtcblx0XHRcblx0XHR2YXIgX3ByZXBhcmVDb250ZW50ID0gZnVuY3Rpb24oZGVmZXJyZWQpIHtcblx0XHRcdGRlZmVycmVkID0gZGVmZXJyZWQgfHwgJC5EZWZlcnJlZCgpOyBcblx0XHRcdFxuXHRcdFx0JC5hamF4KHtcblx0XHRcdFx0XHR1cmw6IG9wdGlvbnMudXJsLFxuXHRcdFx0XHRcdGRhdGE6IHtcblx0XHRcdFx0XHRcdGFjdGlvbjogJ3ByZXBhcmVfY29udGVudCcsXG5cdFx0XHRcdFx0XHRwYWdlX3Rva2VuOiBqc2UuY29yZS5jb25maWcuZ2V0KCdwYWdlVG9rZW4nKVxuXHRcdFx0XHRcdH0sXG5cdFx0XHRcdFx0ZGF0YVR5cGU6ICdqc29uJ1xuXHRcdFx0XHR9KVxuXHRcdFx0XHQvLyBPbiBzdWNjZXNzXG5cdFx0XHRcdC5kb25lKGZ1bmN0aW9uKHJlc3BvbnNlKSB7XG5cdFx0XHRcdFx0aWYgKHJlc3BvbnNlLnJlcGVhdCA9PT0gdHJ1ZSkge1xuXHRcdFx0XHRcdFx0X3ByZXBhcmVDb250ZW50KGRlZmVycmVkKTtcblx0XHRcdFx0XHR9IGVsc2Uge1xuXHRcdFx0XHRcdFx0ZGVmZXJyZWQucmVzb2x2ZSgpO1xuXHRcdFx0XHRcdH1cblx0XHRcdFx0fSlcblx0XHRcdFx0Ly8gT24gRmFpbHVyZVxuXHRcdFx0XHQuZmFpbChmdW5jdGlvbihyZXNwb25zZSkge1xuXHRcdFx0XHRcdGpzZS5jb3JlLmRlYnVnLmVycm9yKCdQcmVwYXJlIENvbnRlbnQgRXJyb3I6ICcsIHJlc3BvbnNlKTtcblx0XHRcdFx0XHRkZWZlcnJlZC5yZWplY3QoKTtcblx0XHRcdFx0fSk7XG5cdFx0XHRcblx0XHRcdHJldHVybiBkZWZlcnJlZC5wcm9taXNlKCk7IFxuXHRcdH07XG5cdFx0XG5cdFx0Ly8gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tXG5cdFx0Ly8gSU5JVElBTElaQVRJT05cblx0XHQvLyAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cblx0XHRcblx0XHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblx0XHRcdCR0aGlzLm9uKCdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHQkc3Bpbm5lciA9IGpzZS5saWJzLmxvYWRpbmdfc3Bpbm5lci5zaG93KCR0aGlzLnBhcmVudHMoKS5lcSgyKSk7XG5cdFx0XHRcdCQud2hlbihfcHJlcGFyZUNhdGVnb3JpZXMoKSwgX3ByZXBhcmVDb250ZW50KCkpLmRvbmUoX2NyZWF0ZVNpdGVtYXBYbWwpOyBcblx0XHRcdFx0JHRoaXMuYmx1cigpO1xuXHRcdFx0XHRyZXR1cm4gZmFsc2U7XG5cdFx0XHR9KTtcblx0XHRcdFxuXHRcdFx0ZG9uZSgpO1xuXHRcdH07XG5cdFx0XG5cdFx0cmV0dXJuIG1vZHVsZTtcblx0fSk7XG4iXX0=
