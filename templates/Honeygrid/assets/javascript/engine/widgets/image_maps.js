'use strict';

/* --------------------------------------------------------------
 image_maps.js 2015-07-22 gm
 Gambio GmbH
 http://www.gambio.de
 Copyright (c) 2015 Gambio GmbH
 Released under the GNU General Public License (Version 2)
 [http://www.gnu.org/licenses/gpl-2.0.html]
 --------------------------------------------------------------
 */

/**
 * Widget that searches for images with an image map and calls
 * a plugin on them, so that the image maps getting responsive
 */
gambio.widgets.module('image_maps', [], function () {

	'use strict';

	// ########## VARIABLE INITIALIZATION ##########

	var $this = $(this),
	    module = {};

	// ########## INITIALIZATION ##########

	/**
  * Init function of the widget
  * @constructor
  */
	module.init = function (done) {

		$this.find('img[usemap]').rwdImageMaps();

		done();
	};

	// Return data to widget engine
	return module;
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndpZGdldHMvaW1hZ2VfbWFwcy5qcyJdLCJuYW1lcyI6WyJnYW1iaW8iLCJ3aWRnZXRzIiwibW9kdWxlIiwiJHRoaXMiLCIkIiwiaW5pdCIsImRvbmUiLCJmaW5kIiwicndkSW1hZ2VNYXBzIl0sIm1hcHBpbmdzIjoiOztBQUFBOzs7Ozs7Ozs7O0FBVUE7Ozs7QUFJQUEsT0FBT0MsT0FBUCxDQUFlQyxNQUFmLENBQXNCLFlBQXRCLEVBQW9DLEVBQXBDLEVBQXdDLFlBQVc7O0FBRWxEOztBQUVEOztBQUVDLEtBQUlDLFFBQVFDLEVBQUUsSUFBRixDQUFaO0FBQUEsS0FDQ0YsU0FBUyxFQURWOztBQUlEOztBQUVDOzs7O0FBSUFBLFFBQU9HLElBQVAsR0FBYyxVQUFTQyxJQUFULEVBQWU7O0FBRTVCSCxRQUNFSSxJQURGLENBQ08sYUFEUCxFQUVFQyxZQUZGOztBQUlBRjtBQUNBLEVBUEQ7O0FBU0E7QUFDQSxRQUFPSixNQUFQO0FBQ0EsQ0EzQkQiLCJmaWxlIjoid2lkZ2V0cy9pbWFnZV9tYXBzLmpzIiwic291cmNlc0NvbnRlbnQiOlsiLyogLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiBpbWFnZV9tYXBzLmpzIDIwMTUtMDctMjIgZ21cbiBHYW1iaW8gR21iSFxuIGh0dHA6Ly93d3cuZ2FtYmlvLmRlXG4gQ29weXJpZ2h0IChjKSAyMDE1IEdhbWJpbyBHbWJIXG4gUmVsZWFzZWQgdW5kZXIgdGhlIEdOVSBHZW5lcmFsIFB1YmxpYyBMaWNlbnNlIChWZXJzaW9uIDIpXG4gW2h0dHA6Ly93d3cuZ251Lm9yZy9saWNlbnNlcy9ncGwtMi4wLmh0bWxdXG4gLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqL1xuXG4vKipcbiAqIFdpZGdldCB0aGF0IHNlYXJjaGVzIGZvciBpbWFnZXMgd2l0aCBhbiBpbWFnZSBtYXAgYW5kIGNhbGxzXG4gKiBhIHBsdWdpbiBvbiB0aGVtLCBzbyB0aGF0IHRoZSBpbWFnZSBtYXBzIGdldHRpbmcgcmVzcG9uc2l2ZVxuICovXG5nYW1iaW8ud2lkZ2V0cy5tb2R1bGUoJ2ltYWdlX21hcHMnLCBbXSwgZnVuY3Rpb24oKSB7XG5cblx0J3VzZSBzdHJpY3QnO1xuXG4vLyAjIyMjIyMjIyMjIFZBUklBQkxFIElOSVRJQUxJWkFUSU9OICMjIyMjIyMjIyNcblxuXHR2YXIgJHRoaXMgPSAkKHRoaXMpLFxuXHRcdG1vZHVsZSA9IHt9O1xuXG5cbi8vICMjIyMjIyMjIyMgSU5JVElBTElaQVRJT04gIyMjIyMjIyMjI1xuXG5cdC8qKlxuXHQgKiBJbml0IGZ1bmN0aW9uIG9mIHRoZSB3aWRnZXRcblx0ICogQGNvbnN0cnVjdG9yXG5cdCAqL1xuXHRtb2R1bGUuaW5pdCA9IGZ1bmN0aW9uKGRvbmUpIHtcblxuXHRcdCR0aGlzXG5cdFx0XHQuZmluZCgnaW1nW3VzZW1hcF0nKVxuXHRcdFx0LnJ3ZEltYWdlTWFwcygpO1xuXG5cdFx0ZG9uZSgpO1xuXHR9O1xuXG5cdC8vIFJldHVybiBkYXRhIHRvIHdpZGdldCBlbmdpbmVcblx0cmV0dXJuIG1vZHVsZTtcbn0pOyJdfQ==
